<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Cashfree auto debit — authorization installment through the manual payment pipeline.
 *
 * Flow:  app -> mobile_api/mobile_payment (cf_subscription_id) -> pending payment rows (ref_trans_id = txnid)
 *        Cashfree authorization SUCCESS -> complete_auth_txn() -> same completion as a manual payment
 *        (services::payment_success): status, receipt no, account no, first payment amount, referral,
 *        intermediate table sync, SMS, email.
 *
 * Used by mobile_api (status check right after authorization), cf_autodebit (webhook + daily job),
 * so the payment is completed by whichever sees the authorization result first — exactly once.
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
	 * Complete the pending mobile_payment rows of an authorization installment.
	 * $cf_payment_id: Cashfree payment reference; $mode: payment method (UPI / CC / DC / NB).
	 * Returns array('status' => bool, 'message' => string). Safe to call any number of times.
	 */
	function complete_auth_txn($txnid, $cf_payment_id = NULL, $mode = NULL, $remark = NULL, $extra = array())
	{
		if (empty($txnid)) {
			return array('status' => FALSE, 'message' => 'No authorization payment');
		}
		$this->load->model('payment_modal');
		$this->load->model('services_modal');
		$this->load->model('sms_model');
		$this->load->model('mobileapi_model');

		// Webhook, app status check and daily job can arrive together: one at a time per transaction
		$lock = $this->db->escape('cf_auth_txn_' . md5($txnid));
		$got  = $this->db->query("SELECT GET_LOCK(" . $lock . ", 10) as got")->row();
		if (empty($got) || $got->got != 1) {
			return array('status' => FALSE, 'message' => 'Authorization payment is being completed by another request');
		}
		try {
			// Same completion as a manual app payment: the manual Cashfree webhook handler
			// (services/cf_webhook -> update_cf_order_status: status, payment mode details, incentive, receipt,
			// client id, account no, first payment, offline sync, SMS — all in one transaction)
			$res = $this->_complete_via_manual($txnid, $cf_payment_id, $mode, $remark, $extra, 'SUCCESS');
			if ($res !== NULL) {
				return $res;
			}
			// Manual handler not reachable from this server: local equivalent (logged)
			return $this->_complete_auth_txn($txnid, $cf_payment_id, $mode, $remark, $extra);
		} finally {
			$this->db->query("SELECT RELEASE_LOCK(" . $lock . ")");
		}
	}

	/**
	 * Complete (SUCCESS) or fail (FAILED) the pending rows of $txnid with the manual payment code itself:
	 * the same signed request Cashfree sends to services/cf_webhook/{id_pg} for a manual app payment
	 * (PAYMENT_SUCCESS_WEBHOOK / PAYMENT_FAILED_WEBHOOK, order_id = ref_trans_id). That handler checks the
	 * status first, so a payment is never completed twice and a SUCCESS is never turned into FAILED.
	 * Returns the result, or NULL when the handler could not be reached (caller uses the local fallback).
	 */
	private function _complete_via_manual($txnid, $cf_payment_id, $mode, $remark, $extra, $target)
	{
		$this->load->model('payment_modal');
		$st = $this->payment_modal->getStatusByTxnId($txnid);
		if (empty($st)) {
			return array('status' => FALSE, 'message' => 'No payment found for transaction ' . $txnid);
		}
		if ($st['payment_status'] == self::PAY_SUCCESS || $st['payment_status'] == self::PAY_AWAITING) {
			return array('status' => FALSE, 'message' => 'Already completed. Status : ' . $st['payment_status']);
		}
		if ($target == 'FAILED' && $st['payment_status'] != self::PAY_PENDING) {
			return array('status' => FALSE, 'message' => 'Already updated. Status : ' . $st['payment_status']);
		}
		$row = $this->db->query("SELECT id_payGateway FROM payment WHERE ref_trans_id = " . $this->db->escape($txnid) . " LIMIT 1")->row_array();
		$id_pg = !empty($row['id_payGateway']) ? (int)$row['id_payGateway'] : 0;
		$gw = $id_pg > 0 ? $this->db->query("SELECT param_1 FROM gateway WHERE id_pg = " . $id_pg)->row_array() : array();
		if (empty($gw['param_1'])) {
			log_message('error', 'cf_autodebit: no gateway secret for id_pg ' . $id_pg . ' (txn ' . $txnid . '), local completion used');
			return NULL;
		}
		// Monthly debit: the row is dated on the debit itself before it is completed
		if (!empty($extra)) {
			$this->db->where('ref_trans_id', $txnid);
			$this->db->where_in('payment_status', array(self::PAY_PENDING, self::PAY_FAILURE));
			$this->db->update('payment', $extra);
		}
		$groups = array('UPI' => 'upi', 'CC' => 'credit_card', 'DC' => 'debit_card', 'NB' => 'net_banking');
		$body = json_encode(array(
			'type' => ($target == 'SUCCESS' ? 'PAYMENT_SUCCESS_WEBHOOK' : 'PAYMENT_FAILED_WEBHOOK'),
			'data' => array(
				'order'   => array('order_id' => $txnid),
				'payment' => array(
					'cf_payment_id'   => $cf_payment_id,
					'payment_status'  => $target,
					'payment_group'   => (isset($groups[$mode]) ? $groups[$mode] : 'upi'),
					'payment_message' => (!empty($remark) ? $remark : 'Auto debit - cashfree subscription'),
					'bank_reference'  => $cf_payment_id
				)
			)
		));
		$ts  = (string)time();
		$sig = base64_encode(hash_hmac('sha256', $ts . $body, $gw['param_1'], true)); // = services::computeSignature()
		$url = rtrim($this->config->item('base_url'), '/') . '/index.php/services/cf_webhook/' . $id_pg;
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_CUSTOMREQUEST  => 'POST',
			CURLOPT_POSTFIELDS     => $body,
			CURLOPT_HTTPHEADER     => array('Content-Type: application/json', 'X-Webhook-Signature: ' . $sig, 'X-Webhook-Timestamp: ' . $ts)
		));
		curl_exec($curl);
		$http = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$err  = curl_error($curl);
		curl_close($curl);

		$after = $this->payment_modal->getStatusByTxnId($txnid);
		$done = ($target == 'SUCCESS')
			? (isset($after['payment_status']) && in_array($after['payment_status'], array(self::PAY_SUCCESS, self::PAY_AWAITING)))
			: (isset($after['payment_status']) && $after['payment_status'] == self::PAY_FAILURE);
		if ($done) {
			return array('status' => TRUE, 'message' => ($target == 'SUCCESS' ? 'Completed' : 'Marked failed') . ' by the manual payment flow (transaction ' . $txnid . ')');
		}
		if ($err != '' || $http < 200 || $http >= 300) {
			log_message('error', 'cf_autodebit: manual webhook not reachable (' . $url . ' HTTP ' . $http . ' ' . $err . '), local completion used for ' . $txnid);
			return NULL;
		}
		return array('status' => FALSE, 'message' => 'Manual payment flow did not update the payment (status ' . (isset($after['payment_status']) ? $after['payment_status'] : '-') . '), see log/<date>/cashfree/webhook.txt');
	}

	private function _complete_auth_txn($id_transaction, $cf_payment_id, $mode, $remark = NULL, $extra = array())
	{
		$pay_status = $this->payment_modal->getStatusByTxnId($id_transaction);
		if (empty($pay_status)) {
			return array('status' => FALSE, 'message' => 'No payment found for transaction ' . $id_transaction);
		}
		if ($pay_status['payment_status'] == self::PAY_SUCCESS || $pay_status['payment_status'] == self::PAY_AWAITING) {
			return array('status' => FALSE, 'message' => 'Already completed. Status : ' . $pay_status['payment_status']);
		}

		// ── same as services::payment_success() from here ──
		$serv_model    = 'services_modal';
		$pay           = $this->payment_modal->getWalletPaymentContent($id_transaction);
		$approval_type = $this->config->item('auto_pay_approval');
		$updateData = array(
			"bank_name"          => NULL,
			"payment_mode"       => (!empty($mode) ? $mode : 'UPI'),
			"card_no"            => NULL,
			"payu_id"            => $cf_payment_id,
			"payment_ref_number" => $cf_payment_id,
			"remark"             => (!empty($remark) ? $remark : "Auto debit authorization - cashfree subscription"),
			"payment_status"     => ($approval_type == 1 || $approval_type == 2 ? self::PAY_SUCCESS : self::PAY_AWAITING)
		);
		// Monthly debit: the row was created when the debit was requested — date it on the debit itself
		foreach ($extra as $k => $v) {
			$updateData[$k] = $v;
		}
		$payment = $this->payment_modal->updateGatewayResponse($updateData, $id_transaction);
		$serviceID = 7;
		$service = $this->services_modal->checkService($serviceID);
		if ($payment['status'] != true) {
			return array('status' => FALSE, 'message' => 'Error in updating the database');
		}
		$payDatas = $this->payment_modal->get_paymenMailData(isset($payment['id_payment']) ? $payment['id_payment'] : '');
		if ($approval_type == 1 || $approval_type == 2 || $approval_type == 3) {
			if ($pay['allow_referral'] == 1) {
				$ref_data = $this->payment_modal->get_refdata($pay['id_scheme_account']);
				$ischkref = $this->payment_modal->get_ischkrefamtadd($pay['id_scheme_account']);
				if ($ref_data['ref_benifitadd_ins_type'] == 1 && $ref_data['referal_code'] != '' && ($ref_data['ref_benifitadd_ins'] == $ref_data['paid_installments']) && $ischkref == TRUE) {
					$this->insert_referral_data($ref_data['id_scheme_account'], $ref_data);
				} else if ($ref_data['ref_benifitadd_ins_type'] == 0 && $ref_data['referal_code'] != '' && $ischkref == TRUE) {
					$this->insert_referral_data($ref_data['id_scheme_account'], $ref_data);
				}
			}
		}
		$payIds = $this->payment_modal->getPayIds($id_transaction);
		if (sizeof($payIds) > 0) {
			foreach ($payIds as $pay) {
				if (empty($pay['id_payment'])) {
					continue; // general advance rows are not part of an authorization installment
				}
				$updData = array("last_payment_on" => NULL);
				$this->payment_modal->updData($updData, 'id_customer', $payIds[0]['id_customer'], 'customer');
				if ($this->config->item("integrationType") == 5) { // Generate A/C No and Receipt No
					$this->generateAcNoOrReceiptNo($pay);
				}
				// Generate receipt number
				if ($pay['receipt_no_set'] == 1 && ($approval_type == 1 || $approval_type == 2 || $approval_type == 3)) {
					$data = array();
					// payment_modal::get_receipt_no() applies scheme_wise_receipt (common / branch / scheme / scheme+branch) itself
					$data['receipt_no'] = $this->payment_modal->generate_receipt_no($pay['id_scheme'], $pay['branch']);
					$this->payment_modal->update_receipt($pay['id_payment'], $data);
				}
				// Generate account number
				if ($pay['schemeacc_no_set'] == 0) {
					if ($pay['scheme_acc_number'] == '' || $pay['scheme_acc_number'] == null) {
						$schData = array();
						$scheme_acc_number = $this->payment_modal->account_number_generator($pay['id_scheme'], $pay['id_branch'], '');
						if ($scheme_acc_number != NULL && $scheme_acc_number != "Not Allocated") {
							$schData['scheme_acc_number'] = $scheme_acc_number;
						}
						if (!empty($schData)) {
							$this->payment_modal->update_account($schData, $pay['id_scheme_account']);
						}
					}
				}
				// Update First Payment Amount In Scheme Account
				if (($approval_type == 1 || $approval_type == 2 || $approval_type == 3) && ($pay['firstPayamt_maxpayable'] == 1 || $pay['firstPayamt_as_payamt'] == 1) && ($pay['firstPayment_amt'] == '' || $pay['firstPayment_amt'] == null)) {
					$this->payment_modal->update_account(array('firstPayment_amt' => $pay['payment_amount']), $pay['id_scheme_account']);
				}
				// Insert Data in Intermediate table (same condition as services::payment_success)
				if ($approval_type == 2 || $approval_type == 3 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
					$this->mobileapi_model->insert_common_data($pay['id_payment']);
				}
				if ($service['sms'] == 1) {
					$sms = $this->$serv_model->get_SMS_data($serviceID, $pay['id_payment']);
					$this->send_sms($sms['mobile'], $sms['message'], isset($sms['dlt_te_id']) ? $sms['dlt_te_id'] : '');
				}
				if ($service['email'] == 1 && isset($payDatas[0]['email']) && $payDatas[0]['email'] != '') {
					$this->load->model('email_model');
					$to = $payDatas[0]['email'];
					$subject = "Reg - " . $this->comp['company_name'] . " payment for the purchase plan";
					$mail = array('payData' => $payDatas[0], 'type' => 3, 'company_details' => $this->comp);
					$message = $this->load->view('include/emailPayment', $mail, true);
					$this->email_model->send_email($to, $subject, $message, "", "");
				}
			}
		}
		return array('status' => TRUE, 'message' => 'Authorization installment completed (transaction ' . $id_transaction . ')');
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
	 * Monthly debit failed: mark its pending payment failed with the bank's reason (same SMS as before).
	 * A row already completed (e.g. a SUCCESS that arrived first) is never changed.
	 */
	function fail_installment_txn($txnid, $reason)
	{
		// Same as a failed manual payment (manual webhook handler, PAYMENT_FAILED_WEBHOOK)
		$res = $this->_complete_via_manual($txnid, NULL, NULL, $reason, array(), 'FAILED');
		if ($res !== NULL) {
			return $res;
		}
		$this->load->model('payment_modal');
		$this->load->model('services_modal');
		$this->load->model('sms_model');
		$this->db->where('ref_trans_id', $txnid);
		$this->db->where('payment_status', self::PAY_PENDING);
		$this->db->update('payment', array('payment_status' => self::PAY_FAILURE, 'remark' => (!empty($reason) ? $reason : 'Auto debit failed')));
		if ($this->db->affected_rows() < 1) {
			return array('status' => FALSE, 'message' => 'Already updated');
		}
		$serviceID = 7;
		$service = $this->services_modal->checkService($serviceID);
		if ($service['sms'] == 1) {
			foreach ($this->payment_modal->getPayIds($txnid) as $pay) {
				if (!empty($pay['id_payment'])) {
					$sms = $this->services_modal->get_SMS_data($serviceID, $pay['id_payment']);
					$this->send_sms($sms['mobile'], $sms['message'], isset($sms['dlt_te_id']) ? $sms['dlt_te_id'] : '');
				}
			}
		}
		return array('status' => TRUE, 'message' => 'Failed payment updated');
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

	private function send_sms($mobile, $message, $dlt_te_id)
	{
		if ($this->config->item('sms_gateway') == '1') {
			$this->sms_model->sendSMS_MSG91($mobile, $message, '', $dlt_te_id);
		} elseif ($this->config->item('sms_gateway') == '2') {
			$this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
		} elseif ($this->config->item('sms_gateway') == '3') {
			$this->sms_model->sendSMS_SpearUC($mobile, $message, '', $dlt_te_id);
		} elseif ($this->config->item('sms_gateway') == '4') {
			$this->sms_model->sendSMS_Asterixt($mobile, $message, '', $dlt_te_id);
		} elseif ($this->config->item('sms_gateway') == '5') {
			$this->sms_model->sendSMS_Qikberry($mobile, $message, '', $dlt_te_id);
		}
	}

	/**
	 * Same as services::insert_referral_data(). The services version also updates the referrer's
	 * "benefit credited" flag using an undefined $chkreferral, which never matches a row — left out here.
	 */
	private function insert_referral_data($id_scheme_account, $referral_data)
	{
		$serviceID = 16;
		$data = array();
		if ($referral_data['referal_code'] != '' && $referral_data['is_refferal_by'] == 1) {
			$data = $this->payment_modal->get_empreferrals_datas($id_scheme_account);
		} else if ($referral_data['referal_code'] != '' && $referral_data['is_refferal_by'] == 0) {
			$data = $this->payment_modal->get_cusreferrals_datas($id_scheme_account);
		}
		if (!empty($data) && $data['referal_code'] != '' && $data['referal_value'] != '' && $data['id_wallet_account'] != '') {
			$wallet_data = array(
				'id_wallet_account' => $data['id_wallet_account'],
				'date_transaction'  => date("Y-m-d H:i:s"),
				'transaction_type'  => 0,
				'value'             => $data['referal_value'],
				'description'       => 'Referral Benefits - ' . $data['cusname'] . ' ref no. ' . $data['id_scheme_account']
			);
			$this->payment_modal->wallet_transactionDB($wallet_data);
			$service = $this->services_modal->checkService($serviceID);
			if ($service['sms'] == 1 && $data['mobile']) {
				$sms_data = $this->services_modal->get_SMS_data($serviceID, $data['id_scheme_account']);
				$this->send_sms($sms_data['mobile'], $sms_data['message'], $service['dlt_te_id']);
			}
		}
	}

	/**
	 * Same as services::generateAcNoOrReceiptNo() — Khimji integration (integrationType 5) only.
	 */
	private function generateAcNoOrReceiptNo($pay)
	{
		$this->load->model('integration_model');
		$postData = array(
			"transactionType" => 2,
			"tranUniqueId"    => $pay['offline_tran_uniqueid'],
			"branchCode"      => $pay['warehouse'],
			"paymentDetail"   => array(
				"paymentType"           => 9,
				"paymentTypeName"       => "Online",
				"amount"                => $pay['payment_amount'],
				"authorizationNo"       => $pay['payment_amount'],
				"narration"             => $pay['payment_type'] . "-" . $pay['payment_mode'],
				"originalAmt"           => $pay['payment_amount'],
				"transationAmt"         => $pay['payment_amount'],
				"marchantCharges"       => 0.00,
				"cardChargesAmount"     => 0.00,
				"cardChargesPercentage" => 0
			)
		);
		if ($pay['scheme_acc_number'] != "" && $pay['scheme_acc_number'] != NULL) {
			$postData["orderNo"] = $pay['scheme_acc_number'];
		}
		$response = $this->integration_model->khimji_curl('app/v1/saveSchemeOrInstallmentDetails', $postData);
		if ($response['status'] == TRUE) {
			$resData = $response['data']->data;
			if ($resData->errorCode == 0) {
				if (isset($resData->result->orderNo)) {
					$this->payment_modal->updData(array('scheme_acc_number' => $resData->result->orderNo, 'date_upd' => date("Y-m-d H:i:s")), 'id_scheme_account', $pay['id_scheme_account'], 'scheme_account');
					$this->payment_modal->updData(array('receipt_no' => $resData->result->orderNo, 'date_upd' => date("Y-m-d H:i:s")), 'id_payment', $pay['id_payment'], 'payment');
					return true;
				}
				if (isset($resData->result->installmentNo)) {
					$this->payment_modal->updData(array('receipt_no' => $resData->result->installmentNo, 'date_upd' => date("Y-m-d H:i:s")), 'id_payment', $pay['id_payment'], 'payment');
					return true;
				}
			}
		}
		return true;
	}
}
