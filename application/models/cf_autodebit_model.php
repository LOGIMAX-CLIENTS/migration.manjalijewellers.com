<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Cashfree auto debit — authorization installment and monthly debits through the manual payment pipeline.
 *
 * Flow:  app -> mobile_api/mobile_payment (cf_subscription_id) / daily job -> pending payment rows (ref_trans_id = txnid)
 *        Cashfree SUCCESS -> complete_auth_txn() / FAILED -> fail_installment_txn()
 *        Both run the same steps as a manual app payment completed by the Cashfree webhook
 *        (admin chit_transaction::webhook -> chit_transaction_model::onPayTranStream): status, payment mode details,
 *        receipt no, start / maturity date, account no, client id, first payable, installment no + due dates +
 *        total paid installments, intermediate table, referral incentive, SMS — on this request (no HTTP call),
 *        so a payment is never left pending.
 *
 * Used by mobile_api (status check right after authorization), cf_autodebit (webhook + daily job),
 * so the payment is completed by whichever sees the result first — exactly once.
 */
class Cf_autodebit_model extends CI_Model
{
	const PAY_PENDING  = 7;
	const PAY_AWAITING = 2;
	const PAY_SUCCESS  = 1;
	const PAY_CANCEL   = 4;
	const PAY_FAILURE  = 3;

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Complete the pending payment rows of $txnid (authorization installment or monthly debit).
	 * $cf_payment_id: Cashfree payment reference; $mode: payment method (UPI / CC / DC / NB);
	 * $extra: extra payment columns (monthly debit: date_payment = actual debit time).
	 * Returns array('status' => bool, 'message' => string). Safe to call any number of times.
	 */
	function complete_auth_txn($txnid, $cf_payment_id = NULL, $mode = NULL, $remark = NULL, $extra = array())
	{
		return $this->_locked_update($txnid, 'SUCCESS', $cf_payment_id, $mode, $remark, $extra);
	}

	/**
	 * Monthly debit failed: mark its pending payment failed with the bank's reason.
	 * A row already completed (e.g. a SUCCESS that arrived first) is never changed.
	 */
	function fail_installment_txn($txnid, $reason)
	{
		return $this->_locked_update($txnid, 'FAILED', NULL, NULL, $reason, array());
	}

	/**
	 * Webhook, app status check and daily job can arrive together: one at a time per transaction.
	 */
	private function _locked_update($txnid, $target, $cf_payment_id, $mode, $remark, $extra)
	{
		if (empty($txnid)) {
			return array('status' => FALSE, 'message' => 'No payment transaction');
		}
		$this->load->model('payment_modal');
		$this->load->model('services_modal');
		$this->load->model('sms_model');
		$this->load->model('mobileapi_model');

		$lock = $this->db->escape('cf_auth_txn_' . md5($txnid));
		$got  = $this->db->query("SELECT GET_LOCK(" . $lock . ", 10) as got")->row();
		if (empty($got) || $got->got != 1) {
			return array('status' => FALSE, 'message' => 'Payment is being updated by another request');
		}
		try {
			$st = $this->payment_modal->getStatusByTxnId($txnid);
			if (empty($st)) {
				return array('status' => FALSE, 'message' => 'No payment found for transaction ' . $txnid);
			}
			// Same check as services::cf_webhook before update_cf_order_status
			if ($st['payment_status'] == self::PAY_SUCCESS || $st['payment_status'] == self::PAY_AWAITING) {
				return array('status' => FALSE, 'message' => 'Already completed. Status : ' . $st['payment_status']);
			}
			if ($target == 'FAILED' && $st['payment_status'] != self::PAY_PENDING) {
				return array('status' => FALSE, 'message' => 'Already updated. Status : ' . $st['payment_status']);
			}
			$res = $this->_update_order_status($txnid, $target, $cf_payment_id, $mode, $remark, $extra);
			$this->_log(array('txnid' => $txnid, 'target' => $target, 'cf_payment_id' => $cf_payment_id, 'result' => $res));
			return $res;
		} finally {
			$this->db->query("SELECT RELEASE_LOCK(" . $lock . ")");
		}
	}

	/**
	 * Same as a manual app payment completed by the Cashfree webhook:
	 * admin chit_transaction::webhook() -> chit_transaction_model::updateGatewayResponse() -> payment_update()
	 * (onPayTranStream() for every successful row, then SMS 3 on success / 7 on failure).
	 */
	private function _update_order_status($trans_id, $payStatus, $cf_payment_id, $mode, $remark, $extra)
	{
		$this->chit = $this->db->query("SELECT * From chit_settings")->row_array();

		// chit_transaction::checkTransExist(): rows of this transaction not yet successful
		$rows = $this->db->query("SELECT id_payment, payu_id, ref_trans_id FROM payment WHERE ref_trans_id = ? AND payment_status != 1", array($trans_id))->result_array();
		if (empty($rows)) {
			return array('status' => FALSE, 'message' => 'No payment to update for transaction ' . $trans_id);
		}
		$includeIds = array_column($rows, 'id_payment');

		// chit_transaction::prepareGatewayResponse()
		$gatewayResponse = array(
			"remark"         => (!empty($remark) ? $remark : 'Auto debit - cashfree subscription') . " on " . date('Y-m-d H:i:s'),
			"payment_status" => ($payStatus === "SUCCESS" ? self::PAY_SUCCESS : self::PAY_FAILURE)
		);
		if ($payStatus === "SUCCESS") {
			$gatewayResponse["payment_mode"]       = (!empty($mode) ? $mode : 'UPI');
			$gatewayResponse["payu_id"]            = $cf_payment_id;
			$gatewayResponse["payment_ref_number"] = $cf_payment_id;
		}
		// Monthly debit: the row is dated on the debit itself
		foreach ($extra as $k => $v) {
			$gatewayResponse[$k] = $v;
		}

		// chit_transaction_model::updateGatewayResponse()
		$this->db->where('ref_trans_id', $trans_id);
		$this->db->where_in('id_payment', $includeIds);
		$status = $this->db->update('payment', $gatewayResponse);
		if (!$status) {
			return array('status' => FALSE, 'message' => 'Error updating ref_trans_id : ' . $trans_id . ' ' . $this->db->_error_message());
		}
		$this->db->select('id_payment, payu_id, payment_status');
		$this->db->from('payment');
		$this->db->where('ref_trans_id', $trans_id);
		$this->db->where_in('id_payment', $includeIds);
		$payids = $this->db->get()->result_array();

		// chit_transaction::payment_update()
		foreach ($payids as $payid) {
			if ($payid['payment_status'] == 1) {
				$this->onPayTranStream($payid['id_payment']);
				$serviceID = 3;
			} else {
				$serviceID = 7;
			}
			$service = $this->services_modal->checkService($serviceID);
			if (!empty($service) && $service['sms'] == 1) {
				$data = $this->services_modal->get_SMS_data($serviceID, $payid['id_payment']);
				if (!empty($data['mobile'])) {
					$this->mobileapi_model->send_sms($data['mobile'], $data['message'], '', (isset($service['dlt_te_id']) ? $service['dlt_te_id'] : ''));
				}
			}
		}
		return array('status' => TRUE, 'message' => ($payStatus == "SUCCESS" ? 'Payment completed' : 'Failed payment updated') . ' (transaction ' . $trans_id . ')');
	}

	/**
	 * Successful auto debit payments completed before installments were set here (installment still empty):
	 * set installment no / due dates / total paid installments only — receipt, incentive, SMS were already done.
	 * Returns the number of payments repaired.
	 */
	function repair_missing_installments($limit = 100)
	{
		$rows = $this->db->query("SELECT p.id_payment FROM payment p
								WHERE p.payment_status = 1 AND p.installment IS NULL AND p.ref_trans_id IS NOT NULL
								  AND (p.ref_trans_id IN (SELECT auth_txnid FROM auto_debit_subscription WHERE auth_txnid IS NOT NULL)
								    OR p.ref_trans_id IN (SELECT txnid FROM auto_debit_charge WHERE txnid IS NOT NULL))
								ORDER BY p.id_scheme_account, p.date_payment
								LIMIT " . (int)$limit);
		if ($rows === FALSE) {
			return 0;
		}
		$fixed = 0;
		foreach ($rows->result_array() as $row) {
			$payment = $this->_get_payment_details($row['id_payment']);
			if (!empty($payment)) {
				$this->_updatedue_details($payment);
				$fixed++;
			}
		}
		return $fixed;
	}

	/* ── Port of admin chit_transaction_model (manual webhook completion) ── */

	/**
	 * chit_transaction_model::onPayTranStream(): payment mode details, receipt no, start / maturity date,
	 * account no, client id, first payable, installment no + due dates + total paid installments,
	 * intermediate table, employee / customer referral.
	 */
	private function onPayTranStream($id_payment)
	{
		$payment = $this->_get_payment_details($id_payment);

		// Payment mode details table [Except admin payment]
		if (!empty($payment['id_payGateway'])) {
			$this->_updData(array('payment_status' => 9, "updated_time" => date('Y-m-d H:i:s'), "remark" => "Removed while webhook update " . date('Y-m-d H:i:s')), 'id_payment', $id_payment, 'payment_mode_details');
			$this->_insert_pay_mode_details($payment);
		}

		// Receipt no generate
		if ($this->chit['receipt_no_set'] == 1 && empty($payment['receipt_no'])) {
			$this->_generate_receipt_no($payment);
		}

		if (empty($payment['scheme_acc_number'])) {
			// Only update start_date and maturity on first successful payment
			$existing_success = $this->db->query(
				"SELECT COUNT(*) as cnt FROM payment WHERE payment_status = 1 AND id_scheme_account = " . (int)$payment['id_scheme_account'] . " AND id_payment != " . (int)$payment['id_payment']
			)->row_array();
			if ($existing_success['cnt'] == 0) {
				$join_date = array('start_date' => $payment['date_payment']);
				if (!empty($payment['start_year'])) {
					$join_date['start_year'] = $payment['start_year'];
				}
				$this->_updData($join_date, 'id_scheme_account', $payment['id_scheme_account'], 'scheme_account');

				$payment = $this->_get_payment_details($payment['id_payment']);
				if ($payment['maturity_type'] > 0 && $payment['maturity_days'] != null && $payment['maturity_days'] > 0) {
					$this->_updData(array('maturity_date' => date('Y-m-d', strtotime("+" . $payment['maturity_days'] . " days", strtotime($payment['start_date'])))), 'id_scheme_account', $payment['id_scheme_account'], 'scheme_account');
				}
			}
			// Account no generate
			if ($this->chit['schemeacc_no_set'] == 0) {
				$this->_generate_account_no($payment);
			}
			// Internal client id
			if ($this->chit['gent_clientid'] == 1) {
				$this->_updData(array('ref_no' => $this->config->item('cliIDcode') . "/" . $payment['id_scheme_account']), 'id_scheme_account', $payment['id_scheme_account'], 'scheme_account');
			}
			// First payment amount / weight
			$this->_fix_payable($payment);
		}

		// Installment no, due date, due date to, total paid installments
		$this->_updatedue_details($payment);

		// Inter-tool integration insert
		if ($this->config->item('auto_pay_approval') == 2 && $this->config->item('integrationType') == 2) {
			$this->mobileapi_model->insert_common_data($id_payment);
		}

		if ($payment['referal_code'] != '' && $payment['is_refferal_by'] == 1) { // Employee Referal
			$emp_refral = $this->_get_Incentivedata($payment['id_scheme'], $payment['id_scheme_account'], 1, $payment['id_payment'], 0);
			foreach ($emp_refral as $emp) {
				if ($emp['is_refferal_by'] == 1 && $emp['referal_amount'] > 0) {
					$this->_insertEmployeeIncentive($emp, $payment['id_scheme_account'], $payment['id_payment']);
					if ($emp['credit_for'] == 1) {
						$this->_customerIncentive($emp, $payment['id_scheme_account'], $payment['id_payment']);
					}
				}
			}
		}
		if ($payment['referal_code'] != '' && $payment['is_refferal_by'] == 0) { // Customer Referal
			$emp_refral = $this->_get_Incentivedata($payment['id_scheme'], $payment['id_scheme_account'], 1, $payment['id_payment'], 1);
			foreach ($emp_refral as $emp) {
				if ($emp['is_refferal_by'] == 0 && $emp['referal_amount'] > 0 && $emp['credit_for'] == 1) {
					$this->_customerIncentive($emp, $payment['id_scheme_account'], $payment['id_payment']);
				}
			}
		}
	}

	private function _updData($data, $id_field, $id_value, $table)
	{
		$this->db->where($id_field, $id_value);
		return ($this->db->update($table, $data) == 1 ? TRUE : FALSE);
	}

	private function _get_payment_details($id_payment)
	{
		return $this->db->query("SELECT p.id_payment,p.id_scheme_account,p.id_branch as pay_branch,p.payment_mode,p.payment_ref_number,
	                                sa.id_branch as join_branch,sa.id_scheme,sa.start_date,sa.maturity_date, p.metal_weight,
	                                s.maturity_type,s.maturity_days, c.id_company, sa.group_code, p.receipt_year,s.is_lucky_draw,s.max_members,
	                                s.flexible_sch_type,s.firstPayamt_maxpayable,s.firstPayamt_as_payamt,
                                    sa.firstPayment_wgt,sa.firstPayment_amt,sa.scheme_acc_number,p.date_payment,p.payment_amount,p.payment_status,p.added_by,
                                    s.maturity_type,s.maturity_days,p.id_payGateway,p.due_type,sa.referal_code,sa.is_refferal_by,
                                    IFNULL(p.actual_trans_amt,0) as actual_trans_amt,p.redeemed_amount,
                                    IFNULL(p.gst_amount,0) as gst_amount,IFNULL(p.discountAmt,0) as discountAmt,
                                    p.gst_type,p.ref_trans_id, wa.id_wallet_account,p.receipt_no,sa.start_year
	                             FROM payment p
	                             LEFT JOIN scheme_account sa on sa.id_scheme_account = p.id_scheme_account
	                             LEFT JOIN scheme s on s.id_scheme = sa.id_scheme
	                             LEFT JOIN customer c on c.id_customer = sa.id_customer
                                 LEFT JOIN wallet_account wa ON wa.id_customer=sa.id_customer
	                             WHERE p.id_payment = " . (int)$id_payment)->row_array();
	}

	private function _insert_pay_mode_details($payment)
	{
		$tax = 0;
		$remaining_amt = 0;
		if ($payment['added_by'] == 2) {
			$actual_trans_amt = $payment['actual_trans_amt'];
			$act_wal_redeemed = $payment['redeemed_amount'];
			if ($payment['gst_type'] == 1) { // Exclusive
				$tax = $payment['gst_amount'];
			}
			$pay_amount = ($payment['payment_amount'] - $payment['discountAmt'] + $tax);
			$mode_amount = $pay_amount;
			if ($act_wal_redeemed > 0) {
				$this->db->where('ref_trans_id', $payment['ref_trans_id']);
				$this->db->update('payment', array("payment_mode" => 'MULTI'));
			}
			if ($actual_trans_amt > 0) { // Having Gateway balance, add gateway mode
				if ($actual_trans_amt > $pay_amount) {
					$actual_trans_amt -= $pay_amount;
				} else if ($actual_trans_amt == $pay_amount) {
					$actual_trans_amt -= $pay_amount;
				} else if ($actual_trans_amt < $pay_amount) {
					$mode_amount = $actual_trans_amt;
					$remaining_amt = abs($actual_trans_amt - $pay_amount);
					$actual_trans_amt = 0;
					$pay_amount = $remaining_amt;
				}
				$this->db->insert('payment_mode_details', array(
					'payment_amount'     => $mode_amount,
					'payment_date'       => $payment['date_payment'],
					'created_time'       => date("Y-m-d H:i:s"),
					"payment_mode"       => $payment['payment_mode'],
					"remark"             => "WebHook update on " . date('Y-m-d H:i:s'),
					"payment_ref_number" => $payment['payment_ref_number'],
					"payment_status"     => $payment['payment_status'],
					"id_payment"         => $payment['id_payment']
				));
			}
			if (($remaining_amt > 0 || $actual_trans_amt == 0) && $act_wal_redeemed > 0) { // Wallet redemption: wallet mode + debit transaction
				$this->db->insert('payment_mode_details', array(
					'payment_amount'     => $pay_amount,
					'payment_date'       => date("Y-m-d H:i:s"),
					'created_time'       => date("Y-m-d H:i:s"),
					"payment_mode"       => "REF_WALLET",
					"remark"             => "Wallet Utilized",
					"payment_ref_number" => NULL,
					"payment_status"     => 1,
					"id_payment"         => $payment['id_payment']
				));
				$this->db->insert('wallet_transaction', array(
					'id_wallet_account' => $payment['id_wallet_account'],
					'transaction_type'  => 1, //0-Credit,1-Debit
					'type'              => 0, //CRM
					'id_sch_ac'         => $payment['id_scheme_account'],
					'value'             => $pay_amount,
					'description'       => 'Chit Redeem',
					'date_transaction'  => date("Y-m-d H:i:s"),
					'id_employee'       => NULL,
					'date_add'          => date("Y-m-d H:i:s"),
					'credit_for'        => 'Redeem',
					'id_payment'        => $payment['id_payment']
				));
			}
		} else {
			$this->db->insert('payment_mode_details', array(
				'payment_amount'     => $payment['payment_amount'],
				'payment_date'       => $payment['date_payment'],
				'created_time'       => date("Y-m-d H:i:s"),
				"payment_mode"       => $payment['payment_mode'],
				"remark"             => "WebHook update on " . date('Y-m-d H:i:s'),
				"payment_ref_number" => $payment['payment_ref_number'],
				"payment_status"     => $payment['payment_status'],
				"id_payment"         => $payment['id_payment']
			));
		}
		return true;
	}

	private function _sanitize($payment)
	{
		$cleaned = array();
		foreach ($payment as $key => $value) {
			$cleaned[$key] = $this->db->escape_str($value);
		}
		return $cleaned;
	}

	private function _generate_receipt_no($payment)
	{
		$payment = $this->_sanitize($payment);
		$sql = "SELECT IFNULL(MAX(p.receipt_no), 0) + 1 as next_receipt
                FROM payment p
                LEFT JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
                LEFT JOIN customer c ON c.id_customer = sa.id_customer";
		$where = array();
		if ($this->chit['company_settings'] == 1 && !empty($payment['id_company'])) {
			$where[] = "c.id_company = '{$payment['id_company']}'";
		}
		if ($this->chit['group_wise_receipt'] == 1 && !empty($payment['group_code'])) {
			$where[] = "sa.group_code = '{$payment['group_code']}'";
		}
		if (in_array($this->chit['scheme_wise_receipt'], array(2, 4, 6, 7)) && !empty($payment['pay_branch'])) {
			$where[] = "p.id_branch = '{$payment['pay_branch']}'";
		}
		if (in_array($this->chit['scheme_wise_receipt'], array(3, 4, 6)) && !empty($payment['id_scheme'])) {
			$where[] = "p.id_scheme = '{$payment['id_scheme']}'";
		}
		if (in_array($this->chit['scheme_wise_receipt'], array(5, 6, 7)) && !empty($payment['receipt_year'])) {
			$where[] = "p.receipt_year = '{$payment['receipt_year']}'";
		}
		if (!empty($where)) {
			$sql .= " WHERE " . implode(" AND ", $where);
		}
		$row = $this->db->query($sql)->row_array();
		return $this->_updData(array('receipt_no' => (isset($row['next_receipt']) ? $row['next_receipt'] : null)), 'id_payment', $payment['id_payment'], 'payment');
	}

	private function _generate_account_no($payment)
	{
		$payment = $this->_sanitize($payment);
		$sql = "SELECT IFNULL(MAX(sa.scheme_acc_number), 0) + 1 as next_account
                FROM scheme_account sa
                LEFT JOIN customer c ON c.id_customer = sa.id_customer";
		$where = array();
		if ($this->chit['company_settings'] == 1 && !empty($payment['id_company'])) {
			$where[] = "c.id_company = '{$payment['id_company']}'";
		}
		if (in_array($this->chit['scheme_wise_acc_no'], array(1, 3, 6)) && !empty($payment['join_branch'])) {
			$where[] = "sa.id_branch = '{$payment['join_branch']}'";
		}
		if (in_array($this->chit['scheme_wise_acc_no'], array(2, 3, 5, 6)) && !empty($payment['id_scheme'])) {
			$where[] = "sa.id_scheme = '{$payment['id_scheme']}'";
		}
		if ($this->chit['is_lucky_draw'] == 1 && !empty($payment['group_code'])) {
			$where[] = "sa.group_code = '{$payment['group_code']}'";
		}
		if (in_array($this->chit['scheme_wise_acc_no'], array(4, 5, 6)) && !empty($payment['start_year'])) {
			$where[] = "sa.start_year = '{$payment['start_year']}'";
		}
		if (!empty($where)) {
			$sql .= " WHERE " . implode(" AND ", $where);
		}
		$row = $this->db->query($sql)->row_array();
		return $this->_updData(array('scheme_acc_number' => (isset($row['next_account']) ? $row['next_account'] : null)), 'id_scheme_account', $payment['id_scheme_account'], 'scheme_account');
	}

	private function _fix_payable($payment)
	{
		$fixPayable = array();
		if (($payment['flexible_sch_type'] == 4 || $payment['flexible_sch_type'] == 8) && ($payment['firstPayment_wgt'] == null || $payment['firstPayment_wgt'] == "")) {
			$fixPayable = array('firstPayment_wgt' => $payment['metal_weight']);
		} else if (($payment['firstPayamt_maxpayable'] == 1 || $payment['firstPayamt_as_payamt'] == 1) && ($payment['firstPayment_amt'] == '' || $payment['firstPayment_amt'] == null || $payment['firstPayment_amt'] == 0)) {
			$fixPayable = array('firstPayment_amt' => $payment['payment_amount']);
		}
		if (!empty($fixPayable)) {
			return $this->_updData($fixPayable, 'id_scheme_account', $payment['id_scheme_account'], 'scheme_account');
		}
		return TRUE;
	}

	/**
	 * chit_transaction_model::updatedue_details() — installment no + due dates of the payment, total_paid_ins of the plan
	 */
	private function _updatedue_details($pay)
	{
		$dt_pay = date('Y-m-d', strtotime(str_replace("/", "-", $pay['date_payment'])));
		$ins_cycle = $this->_get_due_date($pay['due_type'], $dt_pay, $pay['id_scheme_account']);
		if (!empty($ins_cycle[0]) && sizeof($ins_cycle[0]) > 0) {
			$this->_updData(array(
				'due_date'        => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),
				'due_date_to'     => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),
				'grace_date'      => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),
				'installment'     => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),
				'is_limit_exceed' => (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),
			), 'id_payment', $pay['id_payment'], 'payment');
		} else {
			// Same-day multiple payments: reuse the earlier same-day payment's installment / due dates
			$sameday_pay = $this->db->query(
				"SELECT p.installment, p.due_date, p.due_date_to, p.grace_date
                 FROM payment p
                 WHERE p.id_scheme_account = " . (int)$pay['id_scheme_account'] . "
                   AND p.payment_status = 1
                   AND p.installment IS NOT NULL
                   AND p.installment > 0
                   AND DATE(p.due_date) = '" . $this->db->escape_str($dt_pay) . "'
                   AND p.id_payment != " . (int)$pay['id_payment'] . "
                 ORDER BY p.id_payment ASC
                 LIMIT 1"
			)->row_array();
			if (!empty($sameday_pay) && !empty($sameday_pay['installment'])) {
				$this->_updData(array(
					'due_date'    => $sameday_pay['due_date'],
					'due_date_to' => $sameday_pay['due_date_to'],
					'grace_date'  => $sameday_pay['grace_date'],
					'installment' => $sameday_pay['installment'],
				), 'id_payment', $pay['id_payment'], 'payment');
			}
		}
		// update paid installments against account
		$paid = $this->_getPaidInsData($pay['id_scheme_account']);
		if (!empty($paid)) {
			$this->_updData(array('total_paid_ins' => $paid['paid_installments']), 'id_scheme_account', $pay['id_scheme_account'], 'scheme_account');
		}
	}

	/**
	 * admin payment_model::get_due_date() (used by the manual webhook completion)
	 */
	private function _get_due_date($due_type, $date_payment, $id_scheme_account)
	{
		$result = array();
		$pay = array();
		$sch = $this->_get_scheme_details($id_scheme_account);
		if (empty($sch)) {
			return array();
		}
		$now = date('Y-m-d');
		$date_payment = date('Y-m-d', strtotime($date_payment));
		$first_payment_date = (!empty($sch['first_payment_date']) ? $sch['first_payment_date'] : $date_payment);
		if ($sch['maturity_type'] != 2 && ($now > $sch['maturity_date'] || $now > $sch['due_cycle_end']) && $due_type == 'current_range' && $sch['total_paid_ins'] > 0) {
			$sch['total_installments'] = max(1, intval($sch['till_days_ins']) - 1);
		}
		// Ensure total_installments is always positive to avoid invalid LIMIT clause
		if (empty($sch['total_installments']) || $sch['total_installments'] < 1) {
			$sch['total_installments'] = 1;
		}
		$c_wh = "and  dt.due_date_from NOT IN (SELECT p.due_date from payment p where p.payment_status = 1 and p.due_date is not null and p.id_scheme_account = sa.id_scheme_account) limit 1";
		if ($due_type == 'ND' || $due_type == '') {
			$where = "and  date('" . $date_payment . "') BETWEEN dt.due_date_from and dt.due_date_to " . $c_wh . " ";
		} else if ($due_type == 'AD') {
			$where = "and dt.due_date_from >= date('" . $date_payment . "')  " . $c_wh . " ";
		} else if ($due_type == 'PD') {
			$where = "and dt.due_date_from <= date('" . $date_payment . "') " . $c_wh . " ";
		} else if ($due_type == 'MND') {
			$where = "and '" . $date_payment . "' BETWEEN dt.due_date_from AND dt.due_date_to ";
		} else {
			$where = $c_wh;
		}
		if ($sch['installment_cycle'] == 2) {  //by days duration cycle
			$days_duration = $sch['ins_days_duration'] - 1;
			$grace_days = $sch['grace_days'] - 1;
			$sql = "SELECT if(dt.due_date_to < '" . $date_payment . "', 'PD',
    				if('" . $date_payment . "' BETWEEN dt.due_date_from AND dt.due_date_to, 'ND',
    				if(dt.due_date_to >= '" . $date_payment . "', 'AD', '-'))) as due_type,
    		dt.installment,dt.due_date_from,dt.due_date_to,dt.grace_date,
    		if((('" . $due_type . "' = 'ND' OR '" . $due_type . "' = '') AND date('" . $date_payment . "') BETWEEN dt.due_date_from and dt.grace_date) OR ('" . $due_type . "' = 'AD'), '0','1') as is_limit_exceed
    		FROM scheme_account sa
    		JOIN (SELECT @sno := @sno + 1 as installment,
    			@due_Date_from := if(@sno = 1, '" . $first_payment_date . "',date_add(@pay_date ,INTERVAL " . $sch['ins_days_duration'] . " day )) as due_date_from,
    			@due_Date_to := if(@sno = 1, date_add('" . $first_payment_date . "',INTERVAL " . $days_duration . " day ),date_add(@due_Date_from,INTERVAL " . $days_duration . " day )) as due_date_to,
    			@grace_date := if(@sno = 1, date_add('" . $first_payment_date . "',INTERVAL " . $grace_days . " day ),date_add(@due_Date_from,INTERVAL " . $grace_days . " day )) as grace_date,
    			@pay_date := if(@sno = 1,'" . $first_payment_date . "',@due_Date_from) as due_pay_date
    			FROM access
    			join (SELECT @pay_date := if(@sno = 1,'" . $first_payment_date . "',@due_Date_from), @sno := 0 ) as t
    			limit " . $sch['total_installments'] . "
    		) as dt
    		WHERE  sa.id_scheme_account = " . (int)$id_scheme_account . "  " . $where . " ";
			$pay = $this->db->query($sql)->result_array();
		} else if ($sch['installment_cycle'] == 1 || $sch['installment_cycle'] == 3) { //daily payment cycle
			$sql = "SELECT if(dt.due_date_to < '" . $date_payment . "', 'PD',
        			if('" . $date_payment . "' = dt.due_date_from, 'ND',
        			if(dt.due_date_to > '" . $date_payment . "', 'AD', '-'))) as due_type,
        	dt.installment,dt.due_date_from,dt.due_date_to
        	FROM scheme_account sa
        	JOIN (SELECT @sno := @sno + 1 as installment,
        		@due_Date_from := if(@sno = 1, '" . $first_payment_date . "',date_add(@pay_date ,INTERVAL 1 day )) as due_date_from,
        		@due_Date_to := @due_Date_from  as due_date_to,
        		@pay_date := if(@sno = 1,'" . $first_payment_date . "',@due_Date_from) as due_pay_date
        		FROM access
        		join (SELECT @pay_date := if(@sno = 1,'" . $first_payment_date . "',@due_Date_from), @sno := 0 ) as t
        		limit " . $sch['total_installments'] . "
        	) as dt
        	WHERE  sa.id_scheme_account = " . (int)$id_scheme_account . "  " . $where . " ";
			$pay = $this->db->query($sql)->result_array();
		} else if ($sch['installment_cycle'] == 0) { //monthly cycle
			$sql = "SELECT if(dt.due_date_to < '" . $date_payment . "', 'PD',
            				if('" . $date_payment . "' BETWEEN dt.due_date_from AND dt.due_date_to, 'ND',
            				if(dt.due_date_to >= '" . $date_payment . "', 'AD', '-'))) as due_type,
                    dt.installment,
                    dt.due_date_from,
                    dt.due_date_to
                    FROM scheme_account sa
                    JOIN (SELECT @sno := @sno + 1 as installment,
                          @due_Date_from := if(@sno = 1, date_format('" . $first_payment_date . "','%Y-%m-01'),date_add(@pay_date ,INTERVAL 1 month )) as due_date_from,
                          @due_Date_to := LAST_DAY(@due_Date_from) as due_date_to,
                          @pay_date := if(@sno = 1,date_format('" . $first_payment_date . "','%Y-%m-01'),@due_Date_from) as due_pay_date
                          FROM access
                          join (SELECT @pay_date := if(@sno = 1,date_format('" . $first_payment_date . "','%Y-%m-01'),@due_Date_from), @sno := 0 ) as t limit " . $sch['total_installments'] . "
                         ) as dt
                    WHERE sa.id_scheme_account = " . (int)$id_scheme_account . "  " . $where . " ";
			$pay = $this->db->query($sql)->result_array();
		}
		// (custom payable cycle 4 is not used by auto debit — Amount schemes only)
		return $pay;
	}

	/**
	 * admin payment_model::get_scheme_details()
	 */
	private function _get_scheme_details($id_scheme_account)
	{
		return $this->db->query("SELECT s.topup_store_slab,s.total_installments,s.grace_days,s.installment_cycle,s.ins_days_duration,
		(SELECT MIN(date(p.date_payment)) FROM payment p WHERE p.payment_status = 1 and p.id_scheme_account = sa.id_scheme_account) as first_payment_date,p.payment_mode,sa.maturity_date,s.maturity_installment,s.maturity_days,s.closing_maturity_days,s.maturity_type,
	    sa.firstPayment_amt,s.firstPayamt_as_payamt,
        IFNULL(IF(sa.is_opening=1,IFNULL(sa.balance_amount,0)+IFNULL(SUM(p.payment_amount * p.no_of_dues),0),IFNULL(SUM(p.payment_amount * p.no_of_dues),0)) ,0) as total_paid_amount,
        if(s.installment_cycle = 0 || s.installment_cycle = 3,
                               PERIOD_DIFF(date_format(CURDATE(),'%Y%m'),date_format(sa.start_date,'%Y%m'))+1,
                               if(s.installment_cycle = 1,
                                  DATEDIFF(date_format(CURDATE(),'%Y%m%d'),date_format(sa.start_date,'%Y%m%d'))+1,
                                  if(s.installment_cycle = 2,
                                  CEIL((DATEDIFF(date_format(CURDATE(),'%Y%m%d'),date_format(sa.start_date,'%Y%m%d'))+1) /s.ins_days_duration),'-'
                                    )
                                 )
                              ) as till_days_ins,
        DATE_FORMAT(
            IF(s.installment_cycle = 0, LAST_DAY(DATE_ADD(sa.start_date, INTERVAL (s.total_installments - 1) MONTH)),
            IF(s.installment_cycle = 1, DATE_ADD(sa.start_date, INTERVAL (s.total_installments - 1) DAY),
            IF(s.installment_cycle = 2, DATE_ADD(sa.start_date, INTERVAL ((s.ins_days_duration * s.total_installments) - 1) DAY), NULL))),
            '%Y-%m-%d') AS due_cycle_end, sa.total_paid_ins
        FROM scheme_account sa
        LEFT JOIN scheme s ON s.id_scheme=sa.id_scheme
        LEFT JOIN customer c ON c.id_customer=sa.id_customer
        LEFT JOIN payment p on p.id_scheme_account = sa.id_scheme_account AND p.payment_status IN (1,2)
        WHERE sa.id_scheme_account=" . (int)$id_scheme_account)->row_array();
	}

	/**
	 * chit_transaction_model::getPaidInsData() — paid_installments = distinct installment numbers paid
	 */
	private function _getPaidInsData($id_scheme_account)
	{
		return $this->db->query("select sa.id_scheme_account, IFNULL(COUNT(Distinct pay.installment),0) as paid_installments
                    from payment pay
					left join scheme_account sa on sa.id_scheme_account = pay.id_scheme_account
                    where pay.payment_status=1 and sa.id_scheme_account = " . (int)$id_scheme_account . "
                    group by sa.id_scheme_account")->row_array();
	}

	/**
	 * chit_transaction_model::get_Incentivedata()
	 */
	private function _get_Incentivedata($id_scheme, $id_scheme_account, $type, $id_payment, $credit_for)
	{
		$data = array();
		$sql = $this->db->query("SELECT * FROM scheme_incentive_settings where credit_to = " . (int)$type . " and id_scheme=" . (int)$id_scheme . " AND credit_for = '" . $this->db->escape_str($credit_for) . "'");
		if ($sql->num_rows() == 0) {
			return $data;
		}
		$acc_data = $this->db->query("SELECT sa.agent_code,sa.id_agent,sa.id_customer as cus_loyal_cus_id,is_refferal_by,sa.id_scheme,p.payment_amount,p.due_type,
					(select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0),
					if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 AND s.firstPayamt_as_payamt = 0), COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0)
					from payment pay where pay.payment_status=1 and pay.id_scheme_account=sa.id_scheme_account group by pay.id_scheme_account) as paid_installments,date(p.date_payment) as payment_date
				     FROM scheme_account sa
				    left join scheme s on (s.id_scheme=sa.id_scheme)
				    left join payment p on (sa.id_scheme_account=p.id_scheme_account)
					where sa.id_scheme_account=" . (int)$id_scheme_account . "  and  p.payment_status=1 and p.id_payment=" . (int)$id_payment . " group by sa.id_scheme_account")->row_array();
		if (empty($acc_data) || $acc_data['payment_amount'] == '' || $acc_data['payment_amount'] <= 0) {
			return $data;
		}
		foreach ($sql->result_array() as $ref) {
			if (($ref['credit_for'] == 0 || $ref['credit_for'] == 1) && ($acc_data['due_type'] == 'ND') && ($ref['from_range'] <= $acc_data['paid_installments'] && $ref['to_range'] >= $acc_data['paid_installments'])) {
				$cash_point = $this->_calcIncentiveAmt($ref['credit_type'], $acc_data['payment_amount'], $ref['credit_value']);
				$data[] = array('referal_amount' => $cash_point, 'credit_for' => $ref['credit_for'], 'id_customer' => $acc_data['cus_loyal_cus_id'], 'credit_remark' => 'New Scheme Join', 'id_payment' => $id_payment, 'is_refferal_by' => $acc_data['is_refferal_by']);
			} else if ($ref['credit_for'] == 3) { // credit based on no of days (Collection App)
				$no_of_days = round((strtotime($acc_data['payment_date']) - strtotime(date('Y-m-01'))) / 86400);
				if ($no_of_days == 0) {
					$no_of_days = 1;
				}
				if ($ref['from_range'] <= $no_of_days && $ref['to_range'] >= $no_of_days) {
					$cash_point = $this->_calcIncentiveAmt($ref['credit_type'], $acc_data['payment_amount'], $ref['credit_value']);
					$data[] = array('referal_amount' => $cash_point, 'credit_for' => $ref['credit_for'], 'id_customer' => $acc_data['cus_loyal_cus_id'], 'credit_remark' => 'Credits between date ' . $ref['from_range'] . ' to ' . $ref['to_range'], 'id_payment' => $id_payment, 'is_refferal_by' => $acc_data['is_refferal_by']);
				}
			} else if ($ref['credit_for'] == 2) { // day wise credits (Collection App)
				$nameOfDay = date('l', strtotime($acc_data['payment_date']));
				if ($nameOfDay == $ref['from_range'] || $nameOfDay == $ref['to_range']) {
					$cash_point = $this->_calcIncentiveAmt($ref['credit_type'], $acc_data['payment_amount'], $ref['credit_value']);
					$data[] = array('referal_amount' => $cash_point, 'credit_for' => $ref['credit_for'], 'id_customer' => $acc_data['cus_loyal_cus_id'], 'credit_remark' => $nameOfDay . ' Collection', 'id_payment' => $id_payment, 'is_refferal_by' => $acc_data['is_refferal_by']);
				}
			}
		}
		return $data;
	}

	private function _calcIncentiveAmt($type, $pay_amt, $ref_val)
	{
		return ($type == 1 ? ($pay_amt * $ref_val) / 100 : $ref_val);
	}

	/**
	 * chit_transaction_model::insertEmployeeIncentive()
	 */
	private function _insertEmployeeIncentive($refdata, $id_scheme_account, $id_payment)
	{
		$chkreferral = $this->payment_modal->get_referral_code($id_scheme_account);
		$data = array();
		$checkCreditExist = $this->payment_modal->checkCreditTransExist($id_scheme_account, $id_payment);
		if ($checkCreditExist == 0 && $chkreferral['referal_code'] != '' && $chkreferral['is_refferal_by'] == 1) {
			$data = $this->payment_modal->get_empreferrals_datas($id_scheme_account);
		}
		if (!empty($data) && $chkreferral['is_refferal_by'] == 1 && $checkCreditExist == 0) {
			if ($data['referal_code'] != '' && $refdata['referal_amount'] != '' && $data['id_wallet_account'] != '' && $data['id_wallet_account'] > 0) {
				$this->payment_modal->wallet_transactionDB(array(
					'id_wallet_account' => $data['id_wallet_account'],
					'id_sch_ac'         => $id_scheme_account,
					'date_transaction'  => date("Y-m-d H:i:s"),
					'id_employee'       => $data['idemployee'],
					'transaction_type'  => 0,
					'value'             => $refdata['referal_amount'],
					'id_payment'        => $id_payment,
					'credit_for'        => $refdata['credit_remark'],
					'description'       => 'Referral Benefits - ' . $data['cusname'] . ''
				));
			}
		}
		return true;
	}

	/**
	 * chit_transaction_model::customerIncentive() (no admin session here: id_employee NULL)
	 */
	private function _customerIncentive($refdata, $id_scheme_account, $id_payment)
	{
		$chkreferral = $this->payment_modal->get_referral_code($id_scheme_account);
		if ($chkreferral['is_refferal_by'] == 0) {
			$this->payment_modal->update_customer_only(array('is_refbenefit_crt_cus' => 1), $chkreferral['id_customer']);
			$isEmpRef = $this->payment_modal->get_empRefExist_datas($id_scheme_account);
			if (!empty($isEmpRef) && $refdata['referal_amount'] != '' && $isEmpRef['id_wallet_account'] != null) {
				$this->payment_modal->wallet_transactionDB(array(
					'id_wallet_account' => $isEmpRef['id_wallet_account'],
					'id_sch_ac'         => $id_scheme_account,
					'date_transaction'  => date("Y-m-d H:i:s"),
					'id_employee'       => NULL,
					'transaction_type'  => 0,
					'value'             => $refdata['referal_amount'],
					'id_payment'        => $id_payment,
					'credit_for'        => 'Customer Intro Scheme Incentive',
					'description'       => 'Customer Intro Referral Benefits - ' . $isEmpRef['cusname'] . ''
				));
			}
		}
	}

	/**
	 * log/cf_subscription/<date>_payment_update.txt — every completion / failure, for tracing
	 */
	private function _log($data)
	{
		$dir = 'log/cf_subscription';
		if (!is_dir($dir)) {
			@mkdir($dir, 0777, true);
		}
		@file_put_contents($dir . '/' . date('Y-m-d') . '_payment_update.txt', date('Y-m-d H:i:s') . ' | ' . json_encode($data) . "\n", FILE_APPEND | LOCK_EX);
	}

	/**
	 * Monthly auto debit: create the month's PENDING payment before the debit is requested from Cashfree,
	 * with the same fields mobile_api::mobile_payment_post() writes for one installment of an Amount scheme
	 * (auto debit is allowed only for Amount schemes below 15,000). Completed by complete_auth_txn() when
	 * Cashfree reports the debit, or marked failed by fail_installment_txn().
	 * $schedule: debit date (Y-m-d) — used as date_payment so the month is right even when requested the
	 * month before (completion re-dates it to the actual debit).
	 * Returns array('status' => bool, 'txnid' => ..., 'message' => ...).
	 */
	function create_installment_txn($id_scheme_account, $amount, $month, $year, $schedule, $id_pg)
	{
		$this->load->model('payment_modal');
		$this->load->model('mobileapi_model');

		$chit = $this->payment_modal->get_schemeByChit($id_scheme_account);
		if (empty($chit)) {
			return array('status' => FALSE, 'message' => 'Scheme account not found');
		}
		if ($chit['scheme_type'] != 0) {
			return array('status' => FALSE, 'message' => 'Auto debit is allowed only for Amount schemes');
		}
		$sa = $this->db->query("SELECT c.mobile, sa.id_branch FROM scheme_account sa JOIN customer c ON c.id_customer = sa.id_customer
								WHERE sa.id_scheme_account = " . (int)$id_scheme_account)->row_array();
		$cusData = !empty($sa) ? $this->payment_modal->get_customer($sa['mobile']) : array();
		if (empty($cusData)) {
			return array('status' => FALSE, 'message' => 'Customer not found');
		}

		// Branch: same rule as mobile_payment (branch-wise registration -> customer branch, else plan branch)
		if ($cusData['branch_settings'] == 1) {
			$id_branch = ($cusData['is_branchwise_cus_reg'] == 1 ? $cusData['id_branch'] : $sa['id_branch']);
		} else {
			$id_branch = NULL;
		}
		$paycred = $this->mobileapi_model->getBranchGatewayData($id_branch, $id_pg);
		$custom_entry_date = $this->mobileapi_model->get_entrydate($id_branch);
		$metal_rate = $this->payment_modal->getMetalRate('');
		$email_regx = "/^([\w\.-]+)@([\w-]+)\.([\w]{2,8})(\.[\w]{2,8})?$/";
		$pay_email = (!empty($cusData['email']) && preg_match($email_regx, $cusData['email'])) ? str_replace(' ', '', $cusData['email']) : str_replace(' ', '', $cusData['pg_email']);

		$txnid = uniqid(time());
		$insertData = array(
			"id_scheme_account"    => $id_scheme_account,
			"id_scheme"            => (isset($chit['id_scheme']) ? $chit['id_scheme'] : 0),
			"payment_amount"       => $amount,
			"gst"                  => (isset($chit['gst']) ? $chit['gst'] : 0),
			"gst_type"             => (isset($chit['gst_type']) ? $chit['gst_type'] : 0),
			"payment_type"         => (!empty($paycred['pg_name']) ? $paycred['pg_name'] : ''),
			"due_type"             => 'ND',
			"due_month"            => (int)$month,
			"due_year"             => (int)$year,
			"no_of_dues"           => 1,
			"actual_trans_amt"     => $amount,
			"date_payment"         => $schedule . ' 10:00:00',
			"custom_entry_date"    => (!empty($custom_entry_date) && $custom_entry_date['edit_custom_entry_date'] == 1 ? $custom_entry_date['custom_entry_date'] : NULL),
			"metal_rate"           => (isset($metal_rate['goldrate_22ct']) ? $metal_rate['goldrate_22ct'] : 0.00),
			"metal_weight"         => 0.000,
			"id_transaction"       => $txnid . '-1',
			"ref_trans_id"         => $txnid,
			"remark"               => 'Auto debit for ' . date('M-Y', mktime(0, 0, 0, $month, 1, $year)),
			"added_by"             => 4, // Cashfree Subscription (auto debit)
			"add_charges"          => NULL,
			"discountAmt"          => 0.00,
			"payment_status"       => self::PAY_PENDING,
			"id_payGateway"        => $id_pg,
			"id_branch"            => $id_branch,
			"receipt_year"         => $this->payment_modal->get_financialYear(),
			"pay_email"            => $pay_email,
			"redeemed_amount"      => 0.00,
			"id_employee"          => NULL,
			"paidBy_id_customer"   => NULL,
			"paidBy_mobile"        => NULL,
			"saved_benefits"       => 0,
			"benefit_value"        => 0,
			"benefit_type"         => 0,
			"saved_benefit_amt"    => 0,
			"dg_other_benefit_wgt" => NULL,
			"dg_other_benefit_amt" => NULL,
		);
		$payment = $this->payment_modal->addPayment($insertData);
		if (empty($payment['insertID'])) {
			return array('status' => FALSE, 'message' => 'Unable to create the payment');
		}
		return array('status' => TRUE, 'txnid' => $txnid, 'id_payment' => $payment['insertID'], 'message' => 'Pending payment created');
	}

	/**
	 * Debit went through after the plan was closed: keep it visible for refund — no receipt, no SMS.
	 */
	function refund_installment_txn($txnid, $cf_payment_id)
	{
		$this->db->where('ref_trans_id', $txnid);
		$this->db->where_in('payment_status', array(self::PAY_PENDING, self::PAY_FAILURE));
		$this->db->update('payment', array(
			'payment_status'     => self::PAY_AWAITING,
			'payment_ref_number' => $cf_payment_id,
			'payu_id'            => $cf_payment_id,
			'date_payment'       => date('Y-m-d H:i:s'),
			'remark'             => 'Auto debit received after the plan was closed - verify and refund'
		));
		return array('status' => $this->db->affected_rows() > 0, 'message' => 'Saved for refund (plan closed)');
	}

	/**
	 * payment.payment_mode short code from a Cashfree AUTH payment (payment_group / payment_method).
	 */
	function cf_payment_mode($auth)
	{
		$group = '';
		if (is_array($auth)) {
			if (!empty($auth['payment_group'])) {
				$group = strtolower($auth['payment_group']);
			} else if (!empty($auth['authorization_details']['payment_group'])) {
				$group = strtolower($auth['authorization_details']['payment_group']);
			}
		}
		if (strpos($group, 'credit') !== false) {
			return 'CC';
		}
		if (strpos($group, 'debit') !== false || $group == 'card') {
			return 'DC';
		}
		if (strpos($group, 'nach') !== false || strpos($group, 'net') !== false) {
			return 'NB';
		}
		return 'UPI';
	}

	/**
	 * Authorization not completed and abandoned (subscription cancelled / recreated): close its pending rows,
	 * the same way an abandoned manual payment is closed. Completed rows are never touched.
	 */
	function cancel_auth_txn($txnid, $reason)
	{
		if (empty($txnid)) {
			return 0;
		}
		$this->db->where('ref_trans_id', $txnid);
		$this->db->where('payment_status', self::PAY_PENDING);
		$this->db->update('payment', array('payment_status' => self::PAY_CANCEL, 'remark' => 'Auto debit authorization not completed - ' . $reason));
		return $this->db->affected_rows();
	}
}
