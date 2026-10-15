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

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Complete the pending mobile_payment rows of an authorization installment.
	 * $cf_payment_id: Cashfree payment reference; $mode: payment method (UPI / CC / DC / NB).
	 * Returns array('status' => bool, 'message' => string). Safe to call any number of times.
	 */
	function complete_auth_txn($txnid, $cf_payment_id = NULL, $mode = NULL)
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
			return $this->_complete_auth_txn($txnid, $cf_payment_id, $mode);
		} finally {
			$this->db->query("SELECT RELEASE_LOCK(" . $lock . ")");
		}
	}

	private function _complete_auth_txn($id_transaction, $cf_payment_id, $mode)
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
			"remark"             => "Auto debit authorization - cashfree subscription",
			"payment_status"     => ($approval_type == 1 || $approval_type == 2 ? self::PAY_SUCCESS : self::PAY_AWAITING)
		);
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
