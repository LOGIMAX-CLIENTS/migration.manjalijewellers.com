# Correct Installment & Due Date Update Workflow

This document provides a comprehensive technical guide and copy-paste code blocks to implement the **Correct Installment Calculation & Due Date Update Workflow** across client applications.

---

## 1. Overview & Problem Statement

### 1.1 Legacy Implementation Issue (`admin_services.php`)
In older / un-updated client services (`admin_services.php`):
1. **Hardcoded Due Type**: `update_client()` passed `'service'` as a hardcoded due type string to `$this->payment_model->get_due_date('service', $dt_pay, $isClientID['id_scheme_account'])`. This ignored scheme configurations (`allow_advance`, `allow_unpaid`, `max_chance`, `installment_cycle`) and caused incorrect `due_type` (`ND`, `AD`, `PD`, `PN`, `AN`, `MND`), wrong due date boundaries, and invalid installment numbers during offline sync.
2. **Naive Paid Count**: Updated `scheme_account.total_paid_ins` using `$paid['paid_installments']` (a raw count) instead of `$paid['old_paid_installments']` (scheme-aware date/due range count).
3. **Scalar `insertPayment` Return Expectation**: Handled `$insPayment` as a scalar ID/boolean (`$insPayment > 0`) instead of an array response (`['status' => TRUE/FALSE, 'id' => ..., 'remark' => ...]`).
4. **Lack of Reset Utilities**: No mechanisms to fix accounts with duplicate installment numbers or broken due dates caused by past sync errors.

### 1.2 Fixed Implementation (`admin/application/controllers/admin_services.php`)
1. **Dynamic Due Type Resolution**: Uses `$this->get_sync_due_type($id_scheme_account, $payment_date)` to dynamically evaluate scheme rules and payment history.
2. **Scheme-Aware Installment Count**: Uses `$paid['old_paid_installments']` when updating `scheme_account.total_paid_ins`.
3. **Array Structure for Payment Insert**: Extracts `$insPayment['id']` and checks `$insPayment['status'] === TRUE` or `$insPayment['remark'] === 'ALREADY_SYNCED'`.
4. **Recovery & Reset Tools**: Includes `reset_duplicate_installments()`, `reset_offline_payments()`, and `reset_due_dates()` to clean up historic database anomalies.

---

## 2. Component File Matrix

| Component Layer | Target File Path | Description of Required Change |
| :--- | :--- | :--- |
| **Controller** | `admin/application/controllers/admin_services.php` | Replace `update_client()`, add `get_sync_due_type()`, `get_scheme_sync_info()`, and reset utilities. |
| **Sync API Model** | `admin/application/models/syncapi_model.php` | Update `insertPayment()`, `getRegisteredAccTransactions()`, and `updatePayment()`. |
| **Payment Model** | `application/models/payment_modal.php` / `admin/application/models/payment_model.php` | Ensure `get_due_date()` and `getPaidInsData()` handle dynamic due types & return `old_paid_installments`. |

---

## 3. Step-by-Step Implementation Guide

### Step 1: Database Pre-requisites Verification

Ensure the following columns exist in your target database tables:

- **`payment` Table**: `due_type`, `due_date`, `due_date_to`, `grace_date`, `installment`, `is_limit_exceed`, `dev_remark`, `payment_ref_number`, `is_offline`, `payment_status`, `saved_benefits`, `saved_benefit_amt`, `benefit_value`, `benefit_type`, `gst`, `gst_type`.
- **`scheme_account` Table**: `total_paid_ins`, `form_secret`.
- **`scheme` Table**: `installment_cycle`, `max_chance`, `allow_advance`, `advance_months`, `allow_advance_in`, `allow_unpaid`, `unpaid_months`, `allow_unpaid_in`, `total_installments`.

---

### Step 2: Controller Implementation (`admin/application/controllers/admin_services.php`)

Copy and paste the following PHP methods into `admin/application/controllers/admin_services.php`:

```php
	/**
	 * Dynamic Due Type Generator for Synchronization
	 * Evaluates scheme rules (installment cycle, max chance, advance/unpaid allowances)
	 * and determines exact due type ('ND', 'AD', 'PD', 'PN', 'AN', 'MND').
	 */
	public function get_scheme_sync_info($id_scheme_account)
	{
		$sql = "SELECT 
				sa.id_scheme_account,
				s.installment_cycle,
				IFNULL(s.max_chance, 0) as max_chance,
				s.allow_advance_in,
				s.allow_unpaid_in,
				s.allow_advance,
				IF(s.allow_advance = 1, s.advance_months, 0) as advance_months,
				s.allow_unpaid,
				IF(s.allow_unpaid = 1, s.unpaid_months, 0) as allow_unpaid_months,
				IFNULL(sa.total_paid_ins, 0) as paid_installments,
				s.total_installments
			FROM scheme_account sa
			LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
			WHERE sa.id_scheme_account = ?";
		
		$query = $this->db->query($sql, array($id_scheme_account));
		return $query->row_array();
	}

	public function get_sync_due_type($id_scheme_account, $date = '')
	{
		if (empty($date)) {
			$date = date('Y-m-d');
		} else {
			$date = date('Y-m-d', strtotime($date));
		}

		$record = $this->get_scheme_sync_info($id_scheme_account);
		if (empty($record)) {
			return 'ND';
		}
		
		$record = (object)$record;
		$due_type = 'ND';
		
		if ($record->installment_cycle == 3 || $record->installment_cycle == 2 || $record->installment_cycle == 1 || $record->installment_cycle == 0) {
			$allowed_due = 0;
			$paid_normal_due = 0;
			$paid_advance_due = 0;
			$paid_pending_due = 0;
			$paid_due = 0;
			$remaining_normal_due = 0;
			$remaining_advance_due = 0;
			$remaining_pending_due = 0;
			$remaining_due = 0;
			$paid_multiple_chance = 0;
			$chances_allowed_due = 0;
			$max_chance = $record->max_chance;
			
			// Get date range for target payment date
			$range = $this->payment_model->get_due_date('current_range', $date, $record->id_scheme_account);
			
			if (!empty($range)) {
				// Count already paid dues in range grouped by due_type
				$paid_dueData = $this->db->query("SELECT due_type as due_name, COUNT(due_type) as dues_count 
											FROM payment where payment_status = 1 and id_scheme_account = " . $record->id_scheme_account . " 
											and date(date_payment) BETWEEN '" . $range[0]['due_date_from'] . "' AND '" . $range[0]['due_date_to'] . "'
											group by due_type")->result_array();
				foreach ($paid_dueData as $due) {
					if ($due['due_name'] == 'ND') {
						$paid_normal_due = $due['dues_count'];
					} else if ($due['due_name'] == 'AD') {
						$paid_advance_due = $due['dues_count'];
					} else if ($due['due_name'] == 'PD') {
						$paid_pending_due = $due['dues_count'];
					} else {
						$paid_multiple_chance = $due['dues_count'];
					}
				}
			}
			
			// Get remaining due counts allowed to pay
			$remaining_dueData = $this->payment_model->get_due_date('allow_pay', $date, $record->id_scheme_account);
			foreach ($remaining_dueData as $due) {
				if ($due['due_name'] == 'ND') {
					$remaining_normal_due = $due['dues_count'];
				} else if ($due['due_name'] == 'AD') {
					$remaining_advance_due = $due['dues_count'];
				} else if ($due['due_name'] == 'PD') {
					$remaining_pending_due = $due['dues_count'];
				} else {
					$remaining_due = $due['dues_count'];
				}
			}
			
			// Evaluate allowed chances and advance/pending conditions
			$chances_allowed_due = $max_chance - ($paid_multiple_chance + $paid_normal_due);
			$allow_advance_in = explode(',', $record->allow_advance_in);
			$allow_unpaid_in = explode(',', $record->allow_unpaid_in);
			
			if ($chances_allowed_due <= 1 && (($record->allow_advance == 1 && $record->advance_months > 0 && (in_array('1', $allow_advance_in) || in_array('4', $allow_advance_in)) && $remaining_advance_due > 0) || ($record->allow_unpaid == 1 && $record->allow_unpaid_months > 0 && (in_array('1', $allow_unpaid_in) || in_array('4', $allow_unpaid_in)) && $remaining_pending_due > 0))) {
				// Advance calculation
				$sch_advance = $record->advance_months;
				$cur_advance = ($remaining_advance_due > 0 ? ($sch_advance < $remaining_advance_due && $remaining_advance_due > 0 && $paid_advance_due < $sch_advance ? $sch_advance : abs($sch_advance - $paid_advance_due)) : 0);
				$canPay_advance = ($remaining_advance_due < $cur_advance ? $remaining_advance_due : $cur_advance);
				
				// Pending calculation
				$sch_unpaid = $record->allow_unpaid_months;
				$cur_unpaid = ($remaining_pending_due > 0 ? ($sch_unpaid < $remaining_pending_due && $remaining_pending_due > 0 && $paid_pending_due < $sch_unpaid ? $sch_unpaid : abs($sch_unpaid - $paid_pending_due)) : 0);
				$canPay_pending = ($remaining_pending_due > $cur_unpaid ? $cur_unpaid : $remaining_pending_due);
				
				if ($remaining_normal_due == 0 && $canPay_pending > 0) {            // Only pending
					$due_type = 'PD';
				} else if ($remaining_normal_due == 0 && $canPay_advance > 0) {        // Only advance
					$due_type = 'AD';
				} else if ($remaining_normal_due > 0 && $canPay_pending > 0) {        // Normal + pending
					$due_type = 'PN';
				} else if ($remaining_normal_due > 0 && $canPay_advance > 0) {        // Normal + advance
					$due_type = 'AN';
				}
			} else {
				if ($chances_allowed_due != 0 && $paid_normal_due != 0 && $max_chance > 1 && sizeof($range) > 0 && $max_chance > $chances_allowed_due) {
					$due_type = 'MND';
				} else {
					$due_type = 'ND';
				}
			}
		}
		
		return $due_type;
	}
```

Next, update `update_client()` in `admin/application/controllers/admin_services.php`:

```php
	/**
	 * Main Synchronization Function
	 * Synchronizes staged customer registrations & payments from inter-tables
	 * into scheme_account and payment with accurate dynamic installment due calculations.
	 */
	function update_client()
	{
		$api_model = self::SYN_MODEL;
		$acc_model = self::ACC_MODEL;
		$record_to = 2; // 2 - Online 

		$trans_date = (isset($_GET['sync_trans_date']) ? $_GET['sync_trans_date'] : date('Y-m-d'));

		$acc_id = "";
		$acc_rec = 0;
		$trans_rec = 0;
		$records = 0;
		$pay_id = "";

		// Phase 1: Sync Customer Registrations to Scheme Accounts
		$cus_reg_data = $this->$api_model->getcustomerByStatus('N', '-1', $record_to, $trans_date);

		if ($cus_reg_data) {
			$records += count($cus_reg_data);
			foreach ($cus_reg_data as $client) {
				if ($client['is_modified'] == 1 && $client['is_registered_online'] >= 1) {
					if ($client['clientid'] != null) {
						$isClientID =  $this->$api_model->checkClientID("", $client['clientid']);
						if ($isClientID['status']) {
							$acc_data = array(
								'closed_by'           => $client['closed_by'],
								'closing_date'        => $client['closing_date'],
								'closing_amount'      => $client['closing_amount'],
								'closing_weight'      => $client['closing_weight'],
								'closing_add_chgs'    => $client['closing_add_chgs'],
								'additional_benefits' => $client['additional_benefits'],
								'remark_close'        => $client['remark_close'],
								'is_closed'           => $client['is_closed'],
								'active'              => ($client['is_closed'] == 1 ? 0 : 1),
								'date_upd'	          => date("Y-m-d H:i:s")
							);
							$acc_status = $this->$api_model->update_closed_ac($acc_data, $client['clientid'], $client['id_customer_reg']);
						} else {
							$acc_data = array(
								'group_code'          => $client['group_code'],
								'scheme_acc_number'   => $client['scheme_ac_no'],
								'ref_no'              => $client['clientid'],
								'date_upd'	          => date("Y-m-d H:i:s")
							);
							$acc_status = $this->$api_model->update_account($acc_data, $client['id_scheme_account'], $client['id_customer_reg']);
						}
					}
					if ($acc_status) {
						$acc_rec += 1;
						$acc_id .= $client['id_scheme_account'] . '|';
						$inter_data = array('is_transferred' => 'Y', 'is_modified' => 'N', 'transfer_date' => date('Y-m-d'), 'ref_no' => $client['ref_no']);
						$this->$api_model->updateData($inter_data, $client['id_branch'], 'customer_reg');
					}
				}
			}
		}

		// Phase 2: Sync Transactions to Payment Table
		$trans_data = $this->$api_model->getRegisteredAccTransactions('N', '-1', $record_to, $trans_date);

		if ($trans_data) {
			$records += count($trans_data);
			foreach ($trans_data as $trans) {
				if ($trans['payment_type'] == 1) { // Online payment
					$isClientID =  $this->$acc_model->checkClientID($trans['id_scheme_account'], "");
					if ($isClientID['status'] &&  $trans['is_modified'] == 1 && $trans['payment_status'] == 1) {
						$trans_data = array(
							'receipt_no'         => $trans['receipt_no'],
							'payment_ref_number' => $trans['ref_no'],
							"payment_status" 	 => 1,
							'date_upd'	         => date("Y-m-d H:i:s")
						);
						$updPayment  = $this->$api_model->updatePayment($trans_data, $trans['payment_type'], $trans['id_scheme_account'], $trans['payment_date']);
						$trans_rec += 1;
						$pay_id .= $trans['ref_no'] . '|';
					}
				} else if ($trans['payment_type'] == 2 && ($trans['client_id'] != null || $trans['client_id'] != '')) { // Offline payment
					$isClientID =  $this->$api_model->checkClientID($trans['id_scheme_account'], "");
					if ($isClientID['status']) {
						if ($trans['payment_status'] == 1) {
							$pay_array = array(
								"id_scheme_account"  => $isClientID['id_scheme_account'],
								"id_scheme"          => $isClientID['id_scheme'],
								"id_branch" 		 => $trans['id_branch'],
								"date_payment" 		 => $trans['payment_date'],
								"date_add" 			 => $trans['payment_date'],
								"metal_rate" 		 => $trans['rate'],
								"payment_amount"	 => $trans['amount'],
								"actual_trans_amt"	 => $trans['amount'],
								"metal_weight" 		 => $trans['weight'],
								"payment_mode" 		 => $trans['payment_mode'],
								"payment_status" 	 => 1,
								"payment_type" 		 => "Offline",
								"due_type"           => $trans['due_type'],
								"installment" 		 => $trans['installment_no'],
								"receipt_no" 		 => $trans['receipt_no'],
								"remark" 			 => $trans['remarks'] . " :: service",
								"discountAmt"		 => $trans['discountAmt'],
								"saved_benefits"    => $trans['saved_benefits_wgt'],
								"saved_benefit_amt"  => $trans['saved_benefit_amt'],
								"benefit_value" 	 => $trans['benefit_value'],
								"benefit_type" 		 => $trans['benefit_type'],
								"payment_ref_number" => $trans['ref_no'],
								"gst"				 => (!empty($trans['gst']) ? $trans['gst'] : 0),
								"gst_type"			 => (!empty($trans['gst_type']) ? $trans['gst_type'] : 0),
								"date_upd" 			 => date('Y-m-d H:i:s')
							);

							$insPayment  = $this->$api_model->insertPayment($pay_array);

                            if ($insPayment['status'] === TRUE) {
                                // Insert payment_mode_details
                                $arrayPayMode = array(
                                	'payment_amount'     => $trans['amount'],
                                    'payment_date'       => $trans['payment_date'],
                                    'created_time'       => date("Y-m-d H:i:s"),
                                    "payment_mode"       => $trans['payment_mode'],
                                    "remark"             => 'Offline CRON service',
                                    "payment_ref_number" => $trans['ref_no'],
                                    "id_payment"         => $insPayment['id'],
                                    "payment_status"     => 1
                                );
    
                                $update_pmd = array(
                                	'payment_status' => 9, // Cancelled
                                    "updated_time"   => date('Y-m-d H:i:s'),
                                    "remark"         => "Removed while direct success on cashfree ".date('Y-m-d H:i:s'),
                                );
                                $update_existing_pmd = $this->payment_model->updData($update_pmd, 'id_payment', $insPayment['id'], 'payment_mode_details');
                                $arrayPayMode['id_payment'] = $insPayment['id'];
                                $payModeInsert = $this->payment_model->insertData($arrayPayMode, 'payment_mode_details');

                                // Calculate Dynamic Due Date and Installment Number based on Scheme Cycle
                                $dt_pay = date('Y-m-d H:i:s', strtotime(str_replace("/", "-", $trans['payment_date'])));
                                $pay_date_only = date('Y-m-d', strtotime($dt_pay));
                                $pay_month_only = date('Y-m', strtotime($dt_pay));

                                // Fetch scheme settings & cycle rules
                                $sch_info = $this->get_scheme_sync_info($isClientID['id_scheme_account']);
                                $is_multi_chance = false;
                                if (!empty($sch_info)) {
                                    if ($sch_info['is_digi'] == 1 || $sch_info['max_chance'] > 1 || $sch_info['payment_chances'] == 1 || $sch_info['scheme_type'] == 3 || $sch_info['scheme_type'] == 2 || $sch_info['flexible_sch_type'] > 0) {
                                        $is_multi_chance = true;
                                    }
                                }

                                $samecycle_pay = null;

                                if (!empty($sch_info) && $sch_info['installment_cycle'] == 1) {
                                    // 1. Daily Cycle: Check for earlier payment on the SAME DAY
                                    $samecycle_pay = $this->db->query(
                                        "SELECT p.installment, p.due_date, p.due_date_to, p.grace_date, p.due_type, p.is_limit_exceed
                                         FROM payment p
                                         WHERE p.id_scheme_account = " . (int)$isClientID['id_scheme_account'] . "
                                           AND p.payment_status = 1
                                           AND p.installment IS NOT NULL
                                           AND p.installment > 0
                                           AND (DATE(p.date_payment) = '" . $this->db->escape_str($pay_date_only) . "' OR DATE(p.due_date) = '" . $this->db->escape_str($pay_date_only) . "')
                                           AND p.id_payment != " . (int)$insPayment['id'] . "
                                         ORDER BY p.id_payment ASC
                                         LIMIT 1"
                                    )->row_array();
                                } else if ($is_multi_chance && (!empty($sch_info) && $sch_info['installment_cycle'] == 0)) {
                                    // 2. Monthly Cycle with Multiple Payment Chance (Digi Gold / flexible): Check for earlier payment in the SAME MONTH
                                    $samecycle_pay = $this->db->query(
                                        "SELECT p.installment, p.due_date, p.due_date_to, p.grace_date, p.due_type, p.is_limit_exceed
                                         FROM payment p
                                         WHERE p.id_scheme_account = " . (int)$isClientID['id_scheme_account'] . "
                                           AND p.payment_status = 1
                                           AND p.installment IS NOT NULL
                                           AND p.installment > 0
                                           AND (DATE_FORMAT(p.date_payment, '%Y-%m') = '" . $this->db->escape_str($pay_month_only) . "' OR DATE_FORMAT(p.due_date, '%Y-%m') = '" . $this->db->escape_str($pay_month_only) . "')
                                           AND p.id_payment != " . (int)$insPayment['id'] . "
                                         ORDER BY p.id_payment ASC
                                         LIMIT 1"
                                    )->row_array();
                                } else {
                                    // 3. Fallback: Check for same exact payment date
                                    $samecycle_pay = $this->db->query(
                                        "SELECT p.installment, p.due_date, p.due_date_to, p.grace_date, p.due_type, p.is_limit_exceed
                                         FROM payment p
                                         WHERE p.id_scheme_account = " . (int)$isClientID['id_scheme_account'] . "
                                           AND p.payment_status = 1
                                           AND p.installment IS NOT NULL
                                           AND p.installment > 0
                                           AND DATE(p.date_payment) = '" . $this->db->escape_str($pay_date_only) . "'
                                           AND p.id_payment != " . (int)$insPayment['id'] . "
                                         ORDER BY p.id_payment ASC
                                         LIMIT 1"
                                    )->row_array();
                                }

                                if (!empty($samecycle_pay) && !empty($samecycle_pay['installment'])) {
                                    // Reuse existing cycle installment and due dates
                                    $cycle_data = array(
                                        'due_date'        => $samecycle_pay['due_date'],
                                        'due_date_to'      => $samecycle_pay['due_date_to'],
                                        'grace_date'       => $samecycle_pay['grace_date'],
                                        'installment'      => $samecycle_pay['installment'],
                                        'due_type'         => $samecycle_pay['due_type'],
                                        'is_limit_exceed'  => (isset($samecycle_pay['is_limit_exceed']) ? $samecycle_pay['is_limit_exceed'] : 0),
                                    );
                                    $this->payment_model->updData($cycle_data, 'id_payment', $insPayment['id'], 'payment');
                                } else {
                                    // First payment for this cycle: calculate due type and due date range
                                    $sync_due_type = $this->get_sync_due_type($isClientID['id_scheme_account'], $dt_pay);
                                    $ins_cycle = $this->payment_model->get_due_date($sync_due_type, $dt_pay, $isClientID['id_scheme_account']);

                                    if (!empty($ins_cycle[0]) && sizeof($ins_cycle[0]) > 0) {
                                        $cycle_data = array(
                                            'due_date'        => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),
                                            'due_date_to'      => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),
                                            'grace_date'       => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),
                                            'installment'      => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),
                                            'is_limit_exceed'  => (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),
                                            'due_type'         => (isset($sync_due_type) ? $sync_due_type : NULL),
                                        );

                                        $this->payment_model->updData($cycle_data, 'id_payment', $insPayment['id'], 'payment');
                                    }
                                }

                                // Update Total Paid Installment Count in Scheme Account
                                $paid = $this->payment_model->getPaidInsData($isClientID['id_scheme_account']);
                                if (!empty($paid)) {
                                    $paid_ins = array('total_paid_ins' => $paid['old_paid_installments']);
                                    $this->payment_model->updData($paid_ins, 'id_scheme_account', $isClientID['id_scheme_account'], 'scheme_account');
                                }
                            }

							if ($insPayment['status'] === TRUE || $insPayment['remark'] === 'ALREADY_SYNCED') {
								$trans_rec += 1;
								$pay_id .= $trans['ref_no'] . '|' . $insPayment['remark'] . '|';
							}
						} else {
							// Update cancelled offline record
							$upd_array = array(
								"payment_status" 	 => 2,
								"receipt_no" 		 => $trans['receipt_no'],
								"remark" 			 => $trans['remarks'],
								"date_upd" 			 => date('Y-m-d H:i:s'),
								"payment_ref_number" => $trans['ref_no']
							);
							$updPayment  = $this->$api_model->updatePayment($upd_array, $trans['payment_type'], $isClientID['id_scheme_account'], $trans['payment_date']);
							if ($updPayment) {
								$trans_rec += 1;
								$pay_id .= $trans['ref_no'] . '|';
							}
						}
					}
				}
			}
		}

		if ($acc_id != '' || $pay_id != '') {
			$remark = array("acc" => $acc_id, "pay" => $pay_id);
			$sync_data = array(
				"total_records"   => $records,
				"scheme_accounts" => $acc_rec,
				"payments"		  => $trans_rec,
				"sync_date"		  => date('Y-m-d H:i:s'),
				"remark"          => json_encode($remark)
			);
			$this->$acc_model->insert_sync($sync_data);
			$result = array('message' => 'Total ' . $records . ' records. Updated ' . $acc_rec . ' scheme accounts and ' . $trans_rec . ' payments records.', 'class' => 'success', 'title' => 'Update Client Details');
		} else {
			$result = array('message' => 'No updates to proceed', 'class' => 'danger', 'title' => 'Update Client Details');
		}

		$this->load->database('default', true);
		echo json_encode($result);
	}
```

---

### Step 3: Installment Repair & Reset Utility Methods

Copy and paste these utility controller functions to fix existing database anomalies:

```php
	/**
	 * Resets duplicate installment numbers for active scheme accounts
	 * Recalculates installments sequentially using dayDurationSchemeService.
	 */
	public function reset_duplicate_installments()
	{
		$sql_select = "SELECT DISTINCT(t.id_scheme_account) FROM (
			SELECT p.id_scheme_account
			FROM payment p 
			LEFT JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account 
			WHERE p.payment_status = 1 
			  AND sa.is_closed = 0 
			  AND p.installment IS NOT NULL 
			  AND p.id_scheme != 72 
			GROUP BY p.id_scheme_account, p.installment 
			HAVING COUNT(*) > 1
		) as t
		ORDER BY t.id_scheme_account ASC";
		
		$accounts = $this->db->query($sql_select)->result_array();
		
		$log_dir = $this->log_dir . 'reset_due_dates_duplicate/';
		if (!is_dir($log_dir)) {
			mkdir($log_dir, 0777, true);
		}
		$log_path = $log_dir . date("Y-m-d") . '.txt';
		
		$msg = "\n" . date("Y-m-d H:i:s") . " - Starting reset_duplicate_installments. Found " . count($accounts) . " accounts to update.\n";
		file_put_contents($log_path, $msg, FILE_APPEND | LOCK_EX);
		echo "Found " . count($accounts) . " accounts to update.<br/>";
		
		foreach ($accounts as $acc) {
			$id_scheme_account = $acc['id_scheme_account'];
			$msg = date("Y-m-d H:i:s") . " - Updating account ID: " . $id_scheme_account . "\n";
			file_put_contents($log_path, $msg, FILE_APPEND | LOCK_EX);
			echo "Updating account ID: " . $id_scheme_account . "<br/>";
			
			// Reset payment table due attributes
			$this->db->query("UPDATE payment SET due_type = NULL, due_date = NULL, due_date_to = NULL, grace_date = NULL, is_limit_exceed = 0, installment = NULL, dev_remark = ? WHERE id_scheme_account = ?", array('due date reset - ' . date('Y-m-d H:i:s'), $id_scheme_account));
			
			// Reset total_paid_ins
			$this->db->query("UPDATE scheme_account SET total_paid_ins = 0, form_secret = ? WHERE id_scheme_account = ?", array('due date reset - ' . $id_scheme_account, $id_scheme_account));
			
			// Sequential Recalculation
			$this->dayDurationSchemeService("", $id_scheme_account);
			$this->upd_total_paid_ins("", $id_scheme_account);
		}
		
		$msg = date("Y-m-d H:i:s") . " - Reset process completed.\n";
		file_put_contents($log_path, $msg, FILE_APPEND | LOCK_EX);
		echo "Reset process completed.<br/>";
	}

	/**
	 * Resets offline payments that have null form_secret or invalid due dates
	 */
	public function reset_offline_payments()
	{
		$sql_select = "SELECT sa.id_scheme_account
			FROM `scheme_account` sa 
			INNER JOIN payment p ON p.id_scheme_account = sa.id_scheme_account 
			WHERE sa.id_scheme_account IN (
				SELECT DISTINCT id_scheme_account 
				FROM payment 
				WHERE payment_type = 'offline' 
				  AND payment_status = 1
			) 
			AND sa.active = 1 and sa.form_secret is null and sa.id_scheme != 72
			GROUP BY sa.id_scheme_account
			ORDER BY sa.id_scheme_account";
		
		$accounts = $this->db->query($sql_select)->result_array();
		
		$log_dir = $this->log_dir . 'reset_due_dates_offline/';
		if (!is_dir($log_dir)) {
			mkdir($log_dir, 0777, true);
		}
		$log_path = $log_dir . date("Y-m-d") . '.txt';
		
		$msg = "\n" . date("Y-m-d H:i:s") . " - Starting reset_offline_payments. Found " . count($accounts) . " accounts to update.\n";
		file_put_contents($log_path, $msg, FILE_APPEND | LOCK_EX);
		echo "Found " . count($accounts) . " accounts to update.<br/>";
		
		foreach ($accounts as $acc) {
			$id_scheme_account = $acc['id_scheme_account'];
			$msg = date("Y-m-d H:i:s") . " - Updating account ID: " . $id_scheme_account . "\n";
			file_put_contents($log_path, $msg, FILE_APPEND | LOCK_EX);
			echo "Updating account ID: " . $id_scheme_account . "<br/>";
			
			$this->db->query("UPDATE payment SET due_type = NULL, due_date = NULL, due_date_to = NULL, grace_date = NULL, is_limit_exceed = 0, installment = NULL, dev_remark = ? WHERE id_scheme_account = ?", array('due date reset - ' . date('Y-m-d H:i:s'), $id_scheme_account));
			$this->db->query("UPDATE scheme_account SET total_paid_ins = 0, form_secret = ? WHERE id_scheme_account = ?", array('due date reset - ' . $id_scheme_account, $id_scheme_account));
			
			$this->dayDurationSchemeService("", $id_scheme_account);
			$this->upd_total_paid_ins("", $id_scheme_account);
		}
		
		$msg = date("Y-m-d H:i:s") . " - Reset process completed.\n";
		file_put_contents($log_path, $msg, FILE_APPEND | LOCK_EX);
		echo "Reset process completed.<br/>";
	}

	/**
	 * Resets due dates for payments where the due date day != 1
	 */
	public function reset_due_dates()
	{
		$sql_select = "SELECT p.id_scheme_account
			FROM payment p
			LEFT JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
			WHERE p.payment_status = 1 
			  AND EXTRACT(DAY FROM p.due_date) != 1 
			  AND p.id_scheme != 72 
			  AND sa.is_closed = 0 
			GROUP BY p.id_scheme_account 
			ORDER BY p.id_payment DESC";
		
		$accounts = $this->db->query($sql_select)->result_array();
		
		$log_dir = $this->log_dir . 'reset_due_dates/';
		if (!is_dir($log_dir)) {
			mkdir($log_dir, 0777, true);
		}
		$log_path = $log_dir . date("Y-m-d") . '.txt';
		
		$msg = "\n" . date("Y-m-d H:i:s") . " - Starting reset_due_dates. Found " . count($accounts) . " accounts to update.\n";
		file_put_contents($log_path, $msg, FILE_APPEND | LOCK_EX);
		echo "Found " . count($accounts) . " accounts to update.<br/>";
		
		foreach ($accounts as $acc) {
			$id_scheme_account = $acc['id_scheme_account'];
			$msg = date("Y-m-d H:i:s") . " - Updating account ID: " . $id_scheme_account . "\n";
			file_put_contents($log_path, $msg, FILE_APPEND | LOCK_EX);
			echo "Updating account ID: " . $id_scheme_account . "<br/>";
			
			$this->db->query("UPDATE payment SET due_type = NULL, due_date = NULL, due_date_to = NULL, grace_date = NULL, is_limit_exceed = 0, installment = NULL, dev_remark = ? WHERE id_scheme_account = ?", array('due date reset - ' . date('Y-m-d H:i:s'), $id_scheme_account));
			$this->db->query("UPDATE scheme_account SET total_paid_ins = 0, form_secret = ? WHERE id_scheme_account = ?", array('due date reset - ' . $id_scheme_account, $id_scheme_account));
			
			$this->dayDurationSchemeService("", $id_scheme_account);
			$this->upd_total_paid_ins("", $id_scheme_account);
		}
		
		$msg = date("Y-m-d H:i:s") . " - Reset process completed.\n";
		file_put_contents($log_path, $msg, FILE_APPEND | LOCK_EX);
		echo "Reset process completed.<br/>";
	}
```

---

### Step 4: Model Implementation (`admin/application/models/syncapi_model.php`)

Ensure `insertPayment()` in `syncapi_model.php` sets `is_offline = 1` and returns an array structure:

```php
	function insertPayment($data)
	{
		$data['is_offline'] = 1;
		$pay_ref_no = $data['payment_ref_number'];
		
		// Scoped check for offline duplicate prevention
		$sql = $this->db->query("SELECT id_payment FROM payment WHERE is_offline=1 AND payment_ref_number='$pay_ref_no'");
			
		if ($sql->num_rows() > 0) {
			$trans = array(
				'is_transferred' => 'Y',
				"date_upd"		 => date('Y-m-d'),
				'transfer_date'	 => date('Y-m-d')
			);
    		$this->db->where('ref_no', $data['payment_ref_number']);
    		$this->db->where('record_to', 2);
        	$this->db->update('transaction', $trans);
			return array(
				'status' => FALSE,
				'remark' => 'ALREADY_SYNCED',
				'msg'    => 'Payment already exists in payment table for ref: ' . $pay_ref_no,
				'id'     => $sql->row()->id_payment,
			);
		} else {
			$status = $this->db->insert('payment', $data);
			$insert_id = $this->db->insert_id();
			if ($status) {	
			    $trans = array(
			    	'is_transferred' => 'Y',
					"date_upd"		 => date('Y-m-d'),
					'transfer_date'	 => date('Y-m-d')
				);
    		    $this->db->where('ref_no', $data['payment_ref_number']);
    		    $this->db->where('record_to', 2);
        		$status = $this->db->update('transaction', $trans);
				return array(
					'status' => TRUE,
					'remark' => 'INSERTED',
					'msg'    => 'New offline payment inserted',
					'id'     => $insert_id,
				);
			}
			return array(
				'status' => FALSE,
				'remark' => 'INSERT_FAILED',
				'msg'    => 'DB insert failed: ' . $this->db->_error_message(),
				'id'     => 0,
			);
		}		
	}
```

---

## 4. Verification & Testing Checklist

After copying the updated code to the target client:

1. **Verify Offline Sync via Admin Interface / URL**:
   - Access URL: `http://<client-domain>/admin/index.php/admin_services/update_client?sync_trans_date=YYYY-MM-DD`
   - Confirm that JSON response returns `"class": "success"` and payments/accounts are updated.
2. **Verify Installment Numbers & Due Types in DB**:
   ```sql
   SELECT id_payment, id_scheme_account, installment, due_type, due_date, due_date_to, is_offline 
   FROM payment 
   WHERE payment_type = 'Offline' AND payment_status = 1 
   ORDER BY id_payment DESC LIMIT 20;
   ```
   Check that `installment` numbers are incremental (1, 2, 3...) and `due_type` is dynamically populated (`ND`, `AD`, `PD`, `PN`, etc.) rather than NULL or hardcoded.
3. **Execute Duplicate Installment Reset Routine**:
   - Access URL: `http://<client-domain>/admin/index.php/admin_services/reset_duplicate_installments`
   - Confirm output shows zero duplicate accounts remaining or logs fixed accounts.
4. **Execute Offline Payment Reset Routine**:
   - Access URL: `http://<client-domain>/admin/index.php/admin_services/reset_offline_payments`
