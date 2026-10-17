<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Cashfree auto debit — authorization installment and monthly debits through the manual payment pipeline.
 *
 * Flow:  app -> mobile_api/mobile_payment (cf_subscription_id) / daily job -> pending payment rows (ref_trans_id = txnid)
 *        Cashfree SUCCESS -> complete_auth_txn() / FAILED -> fail_installment_txn()
 *        Both run the same steps as a manual app payment completed by the Cashfree webhook
 *        (services::update_cf_order_status): status, payment mode details, employee incentive, receipt no,
 *        account no, first payment amount, intermediate table, SMS — in one DB transaction, on this request
 *        (no HTTP call), so a payment is never left pending. Installments are then updated by the offline sync,
 *        exactly as for a manual payment.
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
	 * Same steps as services::update_cf_order_status() (manual app payment, Cashfree webhook).
	 * Differences, all because that code reads variables it never sets:
	 *  - payment_mode_details gets this payment's status / reference (manual writes PENDING with an empty reference);
	 *  - receipt number uses payment_modal (manual calls a non-loaded payment_model);
	 *  - client ID is not generated here, same as manual in practice (it reads $scheme_acc_number before it is set).
	 */
	private function _update_order_status($trans_id, $payStatus, $cf_payment_id, $mode, $remark, $extra)
	{
		$approval_type = $this->config->item('auto_pay_approval');
		$paymentMode   = (!empty($mode) ? $mode : 'UPI');
		$updateData = array(
			"payment_mode"       => $paymentMode,
			"payu_id"            => $cf_payment_id,
			"remark"             => (!empty($remark) ? $remark : 'Auto debit - cashfree subscription') . " :: CF Subscription",
			"payment_ref_number" => $cf_payment_id,
			"payment_status"     => ($payStatus === "SUCCESS" ? ($approval_type == 1 || $approval_type == 2 ? self::PAY_SUCCESS : self::PAY_AWAITING) : self::PAY_FAILURE)
		);
		if ($payStatus !== "SUCCESS") {
			unset($updateData['payment_mode'], $updateData['payu_id'], $updateData['payment_ref_number']);
		}
		// Monthly debit: the row is dated on the debit itself
		foreach ($extra as $k => $v) {
			$updateData[$k] = $v;
		}

		$this->db->trans_begin();
		$payment = $this->payment_modal->updateGatewayResponse($updateData, $trans_id);
		$error = '';
		if ($payment['status'] == true) {
			if ($payStatus == "SUCCESS") {
				$payIds = $this->payment_modal->getPayIds($trans_id);
				if (sizeof($payIds) > 0) {
					$this->payment_modal->updData(array("last_payment_on" => NULL), 'id_customer', $payIds[0]['id_customer'], 'customer');
					foreach ($payIds as $pay) {
						if (empty($pay['id_payment'])) {
							continue; // general advance rows are not part of an auto debit
						}
						// Multi mode payment
						$this->payment_modal->insertData(array(
							'id_payment'         => $pay['id_payment'],
							'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
							'payment_date'       => date("Y-m-d H:i:s"),
							'created_time'       => date("Y-m-d H:i:s"),
							'payment_mode'       => $paymentMode,
							'remark'             => $updateData['remark'],
							'payment_ref_number' => $cf_payment_id,
							'payment_status'     => $updateData['payment_status']
						), 'payment_mode_details');

						$schData = array();
						$cusRegData = array();
						// Employee incentive credits based on installment settings
						if ($pay['referal_code'] != '' && $pay['emp_refferal'] == 1) {
							$type = 1; // 1- employee 2- agent
							$emp_refral = $this->payment_modal->get_Incentivedata($pay['id_scheme'], $pay['id_scheme_account'], $type, $pay['id_payment']);
							if (!empty($emp_refral)) {
								$this->insertEmployeeIncentive($emp_refral, $pay['id_scheme_account'], $pay['id_payment']);
								foreach ($emp_refral as $emp) {
									if ($emp['referal_amount'] > 0 && $emp['credit_for'] == 1) {
										$this->customerIncentive($emp, $pay['id_scheme_account'], $pay['id_payment']);
									}
								}
							}
						}
						// Generate receipt number
						if ($pay['receipt_no_set'] == 1 && ($approval_type == 1 || $approval_type == 2)) {
							$receipt = array('receipt_no' => $this->payment_modal->generate_receipt_no($pay['id_scheme'], $pay['branch']));
							$this->payment_modal->update_receipt($pay['id_payment'], $receipt);
						}
						// Generate account number
						if ($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set'] == 3) {
							if ($pay['scheme_acc_number'] == '' || $pay['scheme_acc_number'] == null || $pay['scheme_acc_number'] == 0) {
								$ac_group_code = NULL;
								if ($pay['is_lucky_draw'] == 1) { // Lucky draw: group code in scheme_account
									$updCode = $this->payment_modal->updateGroupCode($pay);
									$ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
								}
								$scheme_acc_number = $this->payment_modal->account_number_generator($pay['id_scheme'], $pay['branch'], $ac_group_code);
								if ($scheme_acc_number != NULL && $pay['id_scheme_account'] > 0) {
									$schData['scheme_acc_number'] = $scheme_acc_number;
									$cusRegData['scheme_acc_number'] = $scheme_acc_number;
								}
							}
						}
						if ($pay['id_scheme_account'] > 0) {
							if (sizeof($schData) > 0) {
								$this->payment_modal->update_account($schData, $pay['id_scheme_account']);
							}
							if (sizeof($cusRegData) > 0 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
								$this->payment_modal->update_cusreg($cusRegData, $pay['id_scheme_account']); // Customer reg - Ac No
							}
						}
						// Update First Payment Amount In Scheme Account
						if (($approval_type == 1 || $approval_type == 2 || $approval_type == 3) && ($pay['firstPayamt_maxpayable'] == 1 || $pay['firstPayamt_as_payamt'] == 1) && ($pay['firstPayment_amt'] == '' || $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0)) {
							if ($pay['flexible_sch_type'] == 4 && empty($pay['firstpayment_wgt'])) { // Fix First payable as weight
								$fixPayable = array('firstPayment_wgt' => $pay['metal_weight']);
							} else {
								$fixPayable = array('firstPayment_amt' => $pay['payment_amount']);
							}
							$this->payment_modal->update_account($fixPayable, $pay['id_scheme_account']);
						}
						if ($approval_type == 2 && $this->config->item('integrationType') == 2) {
							$this->mobileapi_model->insert_common_data($pay['id_payment']);
						}
					}
				}
			}
		} else {
			$error = $this->db->_error_message();
		}

		if ($payment['status'] == true && $this->db->trans_status() === TRUE) {
			$this->db->trans_commit();
			$serviceID = ($payStatus == "SUCCESS" ? 3 : 7); // 3 payment success (as manual), 7 auto debit failed
			$service = $this->services_modal->checkService($serviceID);
			if (!empty($service) && $service['sms'] == 1 && !empty($payment['id_payment'])) {
				$data = $this->services_modal->get_SMS_data($serviceID, $payment['id_payment']);
				if (!empty($data['mobile'])) {
					$this->mobileapi_model->send_sms($data['mobile'], $data['message'], '', (isset($service['dlt_te_id']) ? $service['dlt_te_id'] : ''));
				}
			}
			return array('status' => TRUE, 'message' => ($payStatus == "SUCCESS" ? 'Payment completed' : 'Failed payment updated') . ' (transaction ' . $trans_id . ')');
		}
		$this->db->trans_rollback();
		return array('status' => FALSE, 'message' => 'Error updating ref_trans_id : ' . $trans_id . ' ' . $error . ' ' . $this->db->_error_message() . ' - transaction rolled back');
	}

	/**
	 * Same as mobile_api::insertEmployeeIncentive() (manual flow calls it from services).
	 */
	private function insertEmployeeIncentive($refdata, $id_scheme_account, $id_payment)
	{
		$chkreferral = $this->payment_modal->get_referral_code($id_scheme_account);
		$data = array();
		foreach ($refdata as $ref) {
			$checkCreditExist = $this->payment_modal->checkCreditTransExist($id_scheme_account, $id_payment);
			if ($checkCreditExist == 0) {
				if ($chkreferral['referal_code'] != '' && $chkreferral['is_refferal_by'] == 1) {
					$data = $this->payment_modal->get_empreferrals_datas($id_scheme_account);
				}
			}
			if (!empty($data) && $chkreferral['is_refferal_by'] == 1 && $checkCreditExist == 0) {
				if ($data['referal_code'] != '' && $ref['referal_amount'] != '' && $data['id_wallet_account'] != '' && $data['id_wallet_account'] > 0) {
					$this->payment_modal->wallet_transactionDB(array(
						'id_wallet_account' => $data['id_wallet_account'],
						'id_sch_ac'         => $id_scheme_account,
						'date_transaction'  => date("Y-m-d H:i:s"),
						'id_employee'       => $data['idemployee'],
						'transaction_type'  => 0,
						'value'             => $ref['referal_amount'],
						'id_payment'        => $id_payment,
						'credit_for'        => $ref['credit_remark'],
						'description'       => 'Referral Benefits - ' . $data['cusname'] . ''
					));
				}
			}
		}
		return true;
	}

	/**
	 * Same as mobile_api::customerIncentive().
	 */
	private function customerIncentive($refdata, $id_scheme_account, $id_payment)
	{
		$chkreferral = $this->payment_modal->get_referral_code($id_scheme_account);
		if ($chkreferral['is_refferal_by'] == 0) { // credit customer introduce staff incentive
			$this->payment_modal->update_customer_only(array('is_refbenefit_crt_cus' => 1), $chkreferral['id_customer']);
			$isEmpRef = $this->payment_modal->get_empRefExist_datas($id_scheme_account);
			if (sizeof($isEmpRef) > 0) {
				if ($refdata['referal_amount'] != '' && $isEmpRef['id_wallet_account'] != null) {
					$this->payment_modal->wallet_transactionDB(array(
						'id_wallet_account' => $isEmpRef['id_wallet_account'],
						'id_sch_ac'         => NULL,
						'date_transaction'  => date("Y-m-d H:i:s"),
						'id_employee'       => null,
						'transaction_type'  => 0,
						'value'             => $refdata['referal_amount'],
						'id_payment'        => $id_payment,
						'credit_for'        => 'Customer Intro Scheme Incentive',
						'description'       => 'Customer Intro Referral Benefits - ' . $isEmpRef['cusname'] . ''
					));
				}
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
