<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');
class Chit_transaction_model extends CI_Model
{
    const PAY_MODEL = 'payment_model';
    const WALL_MODEL = "Wallet_model";
    const CUS_MODEL = 'customer_model';
    const API_MODEL = 'syncapi_model';

    function __construct()
    {
        parent::__construct();


        $this->chit = $this->chit_settings();

        $this->load->model(self::WALL_MODEL);
        $this->load->model(self::PAY_MODEL);
        $this->load->model(self::CUS_MODEL);
        $this->load->model(self::API_MODEL);

        $this->log_dir = 'log/' . date("Y-m-d");
        if (!is_dir($this->log_dir)) {
            mkdir($this->log_dir, 0777, TRUE);
        }
    }

    function insertData($data, $table)
    {
        $status = $this->db->insert($table, $data);
        return    array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
    }

    function updData($data, $id_field, $id_value, $table)
    {
        $edit_flag = 0;
        $this->db->where($id_field, $id_value);
        $edit_flag = $this->db->update($table, $data);
        return ($edit_flag == 1 ? TRUE : FALSE);
    }

    function oldupdateGatewayResponse($data, $txnid)
    {
        $status = $this->updData($data, 'ref_trans_id', $txnid, 'payment');

        $payids = $this->db->query("Select id_payment,payment_status from payment where ref_trans_id = '" . $txnid . "'")->result_array();

        $result = array(
            'status' => $status,
            'payids' => $payids
        );

        return $result;
    }

    function chit_settings()
    {
        $sql = $this->db->query("SELECT * From chit_settings");
        return $sql->row_array();
    }

    function get_payment_details($id_payment)
    {
        $sql = $this->db->query("SELECT p.id_payment,p.id_scheme_account,p.id_branch as pay_branch,p.payment_mode,p.payment_ref_number,
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
	                             WHERE p.id_payment = '" . $id_payment . "'");

        return $sql->row_array();
    }

    function updPayModeBRefTranID($data, $ref_tran_id)
	{
		$this->db->where('ref_trans_id', $ref_tran_id);
		$status = $this->db->update('payment', $data);
		return $status;
	}

    function cancel_existing_mode_details($id_payment)
    {

        $update_pmd = array(
            'payment_status' => 9, // Cancelled
            "updated_time"  => date('Y-m-d H:i:s'),
            "remark"        =>  "Removed while webhook update " . date('Y-m-d H:i:s'),
        );

        return $this->updData($update_pmd, 'id_payment', $id_payment, 'payment_mode_details');
    }

    function insert_pay_mode_details($payment)
    {
        // print_r($payment);exit;
        if ($payment['added_by'] == 2) {
            $actual_trans_amt = $payment['actual_trans_amt'];
            $act_wal_redeemed = $payment['redeemed_amount'];
            $pay_amount = 0;
            if ($payment['gst_type'] == 1) { // Exclusive
                $tax = $payment['gst_amount'];
            }
            $pay_amount = ($payment['payment_amount'] - $payment['discountAmt'] + $tax);
            $mode_amount = $pay_amount;
            if ($act_wal_redeemed > 0) {
				// Update payment table - payment mode as multi
				$this->updPayModeBRefTranID(["payment_mode" => 'MULTI'], $payment['ref_trans_id']);
			}
            // print_r($pay_amount);exit;
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

                $arrayPayMode = array(
                    'payment_amount'     => $mode_amount,
                    'payment_date'         => $payment['date_payment'],
                    'created_time'         => date("Y-m-d H:i:s"),
                    "payment_mode"       => $payment['payment_mode'],
                    "remark"             => "WebHook update on " . date('Y-m-d H:i:s'),
                    "payment_ref_number" => $payment['payment_ref_number'],
                    "payment_status"     => $payment['payment_status'],
                    "id_payment"        => $payment['id_payment']
                );

                $payModeInsert = $this->insertData($arrayPayMode, 'payment_mode_details');
            }
            if (($remaining_amt > 0 || $actual_trans_amt == 0) && $act_wal_redeemed > 0) { // Having wallet redemption: add wallet mode & add debit transaction 
                if ($act_wal_redeemed > $pay_amount) {
                    $act_wal_redeemed -= $pay_amount;
                } else if ($act_wal_redeemed == $pay_amount) {
                    $act_wal_redeemed -= $pay_amount;
                }
                $walletPayMode = array(
                    'payment_amount'    => $pay_amount,
                    'payment_date'      => date("Y-m-d H:i:s"),
                    'created_time'      => date("Y-m-d H:i:s"),
                    "payment_mode"      => "REF_WALLET",
                    "remark"            => "Wallet Utilized",
                    "payment_ref_number" => NULL,
                    "payment_status"    => 1,
                    "id_payment"        => $payment['id_payment']
                );
                $payModeInsert = $this->insertData($walletPayMode, 'payment_mode_details');

                $WalletinsData = array(
                    'id_wallet_account' => $payment['id_wallet_account'],
                    'transaction_type' => 1, //0-Credit,1-Debit
                    'type' => 0, //CRM
                    'id_sch_ac' => $payment['id_scheme_account'],
                    'value' => $pay_amount,
                    'description' => 'Chit Redeem',
                    'date_transaction' => date("Y-m-d H:i:s"),
                    'id_employee' => NULL,
                    'date_add' => date("Y-m-d H:i:s"),
                    'credit_for' => 'Redeem',
                    'id_payment' => $payment['id_payment']
                );
                $this->insertData($WalletinsData, 'wallet_transaction');
            }
        } else {
            $arrayPayMode = array(
                'payment_amount'     => $payment['payment_amount'],
                'payment_date'         => $payment['date_payment'],
                'created_time'         => date("Y-m-d H:i:s"),
                "payment_mode"       => $payment['payment_mode'],
                "remark"             => "WebHook update on " . date('Y-m-d H:i:s'),
                "payment_ref_number" => $payment['payment_ref_number'],
                "payment_status"     => $payment['payment_status'],
                "id_payment"        => $payment['id_payment']
            );

            $this->insertData($arrayPayMode, 'payment_mode_details');
        }

        return true;
    }

    function sanitize_payment_data($payment)
    {
        $cleaned = [];
        foreach ($payment as $key => $value) {
            $cleaned[$key] = $this->db->escape_str($value);
        }
        return $cleaned;
    }

    function generate_receipt_no($payment)
    {
        $payment = $this->sanitize_payment_data($payment); // Sanitize once

        $sql = "SELECT IFNULL(MAX(p.receipt_no), 0) + 1 as next_receipt 
                FROM payment p 
                LEFT JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
                LEFT JOIN customer c ON c.id_customer = sa.id_customer";

        $where = [];

        if ($this->chit['company_settings'] == 1 && !empty($payment['id_company'])) {
            $where[] = "c.id_company = '{$payment['id_company']}'";
        }

        if ($this->chit['group_wise_receipt'] == 1 && !empty($payment['group_code'])) {
            $where[] = "sa.group_code = '{$payment['group_code']}'";
        }

        if (in_array($this->chit['scheme_wise_receipt'], [2, 4, 6, 7]) && !empty($payment['pay_branch'])) {
            $where[] = "p.id_branch = '{$payment['pay_branch']}'";
        }

        if (in_array($this->chit['scheme_wise_receipt'], [3, 4, 6]) && !empty($payment['id_scheme'])) {
            $where[] = "p.id_scheme = '{$payment['id_scheme']}'";
        }

        if (in_array($this->chit['scheme_wise_receipt'], [5, 6, 7]) && !empty($payment['receipt_year'])) {
            $where[] = "p.receipt_year = '{$payment['receipt_year']}'";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $next_receipt = array('receipt_no' => $this->db->query($sql)->row_array()['next_receipt'] ?? null);

        return $this->updData($next_receipt, 'id_payment', $payment['id_payment'], 'payment');
    }

    function generate_account_no($payment)
    {
        $payment = $this->sanitize_payment_data($payment); // Sanitize once

        $sql = "SELECT IFNULL(MAX(sa.scheme_acc_number), 0) + 1 as next_account 
                FROM scheme_account sa
                LEFT JOIN customer c ON c.id_customer = sa.id_customer";

        $where = [];

        if ($this->chit['company_settings'] == 1 && !empty($payment['id_company'])) {
            $where[] = "c.id_company = '{$payment['id_company']}'";
        }

        if (in_array($this->chit['scheme_wise_acc_no'], [1, 3, 6]) && !empty($payment['join_branch'])) {
            $where[] = "sa.id_branch = '{$payment['join_branch']}'";
        }

        if (in_array($this->chit['scheme_wise_acc_no'], [2, 3, 5, 6]) && !empty($payment['id_scheme'])) {
            $where[] = "sa.id_scheme = '{$payment['id_scheme']}'";
        }

        if ($this->chit['is_lucky_draw'] == 1 && !empty($payment['group_code'])) {
            $where[] = "sa.group_code = '{$payment['group_code']}'";
        }

        if (in_array($this->chit['scheme_wise_acc_no'], [4, 5, 6]) && !empty($payment['start_year'])) {
            $where[] = "sa.start_year = '{$payment['start_year']}'";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $next_account = array('scheme_acc_number' => $this->db->query($sql)->row_array()['next_account'] ?? null);

        return $this->updData($next_account, 'id_scheme_account', $payment['id_scheme_account'], 'scheme_account');
    }

    function generate_client_id($payment)
    {

        $client_id = array('ref_no' => $this->config->item('cliIDcode') . "/" . $payment['id_scheme_account']);
        return $this->updData($client_id, 'id_scheme_account', $payment['id_scheme_account'], 'scheme_account');
    }

    function fix_payable($payment)
    {
        $result = TRUE;
        if (($payment['flexible_sch_type'] == 4 || $payment['flexible_sch_type'] == 8) && ($payment['firstPayment_wgt'] == null || $payment['firstPayment_wgt'] == "")) {
            $fixPayable = array('firstPayment_wgt'  =>  $payment['metal_weight']);
        } else if (($payment['firstPayamt_maxpayable'] == 1 || $payment['firstPayamt_as_payamt'] == 1) && ($payment['firstPayment_amt'] == '' ||  $payment['firstPayment_amt'] == null || $payment['firstPayment_amt'] == 0)) {
            $fixPayable = array('firstPayment_amt'  =>  $payment['payment_amount']);
        }

        if (!empty($fixPayable)) {
            $result =  $this->updData($fixPayable, 'id_scheme_account', $payment['id_scheme_account'], 'scheme_account');
        }

        return $result;
    }

    function get_Incentivedata($id_scheme, $id_scheme_account, $type, $id_payment, $credit_for)
    {
        $sql = $this->db->query("SELECT * FROM scheme_incentive_settings where credit_to = " . $type . " and id_scheme=" . $id_scheme . " AND credit_for = '" . $credit_for . "'");

        $data = array();
        if ($sql->num_rows() > 0) {
            $sql1 = $this->db->query("SELECT sa.agent_code,sa.id_agent,sa.id_customer as cus_loyal_cus_id,is_refferal_by,sa.id_scheme,p.payment_amount,p.due_type,
					(select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), 
					if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 AND s.firstPayamt_as_payamt = 0), COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) 
					from payment pay where pay.payment_status=1 and pay.id_scheme_account=sa.id_scheme_account group by pay.id_scheme_account) as paid_installments,date(p.date_payment) as payment_date
					     FROM scheme_account sa
					    left join scheme s on (s.id_scheme=sa.id_scheme)
					    left join payment p on (sa.id_scheme_account=p.id_scheme_account) 
						where sa.id_scheme_account=" . $id_scheme_account . "  and  p.payment_status=1 and p.id_payment=" . $id_payment . " group by sa.id_scheme_account");
            $acc_data = $sql1->row_array();
            //echo $this->db->last_query();exit;
            $ref_data = $sql->result_array();
            $credit_remark = '';
            if ($acc_data['payment_amount'] != '' && $acc_data['payment_amount'] > 0) {
                foreach ($ref_data as $ref) {

                    //benefit in sch join - type1 (applicable for Agent and Employee)
                    if (($ref['credit_for'] == 0 || $ref['credit_for'] == 1) && ($acc_data['due_type'] == 'ND') && ($ref['from_range'] <= $acc_data['paid_installments'] && $ref['to_range'] >= $acc_data['paid_installments'])) {
                        //calc % and amt based on settings
                        $cash_point = $this->calcIncentiveAmt($ref['credit_type'], $acc_data['payment_amount'], $ref['credit_value']);
                        $credit_remark = 'New Scheme Join';
                        $data[] = array('referal_amount' => $cash_point, 'credit_for' => $ref['credit_for'], 'id_customer' => $acc_data['cus_loyal_cus_id'], 'credit_remark' => $credit_remark, 'id_payment' => $id_payment, 'is_refferal_by' => $acc_data['is_refferal_by']);
                    } else if ($ref['credit_for'] == 3) { //credit benefit based on no of days - only for Agent credit in Collection App
                        $month_first_day = date('Y-m-01');
                        $no_of_days = $this->dateDiff($month_first_day, $acc_data['payment_date']);
                        if ($no_of_days == 0) {
                            $no_of_days = 1;
                        } else {
                            $no_of_days = $no_of_days;
                        }
                        if ($ref['from_range'] <= $no_of_days && $ref['to_range'] >= $no_of_days) {
                            //calc % and amt based on settings
                            $cash_point = $this->calcIncentiveAmt($ref['credit_type'], $acc_data['payment_amount'], $ref['credit_value']);
                            $credit_remark = 'Credits between date ' . $ref['from_range'] . ' to ' . $ref['to_range'];
                            $data[] = array('referal_amount' => $cash_point, 'credit_for' => $ref['credit_for'], 'id_customer' => $acc_data['cus_loyal_cus_id'], 'credit_remark' => $credit_remark, 'id_payment' => $id_payment, 'is_refferal_by' => $acc_data['is_refferal_by']);
                        }
                    } else if ($ref['credit_for'] == 2) // date wise credits ( Sunday Collection benefits only for Collection App)
                    {

                        $nameOfDay = date('l', strtotime($acc_data['payment_date']));
                        if ($nameOfDay == $ref['from_range'] || $nameOfDay == $ref['to_range']) {
                            $cash_point = $this->calcIncentiveAmt($ref['credit_type'], $acc_data['payment_amount'], $ref['credit_value']);
                            $credit_remark = $nameOfDay . ' Collection';
                            $data[] = array('referal_amount' => $cash_point, 'credit_for' => $ref['credit_for'], 'id_customer' => $acc_data['cus_loyal_cus_id'], 'credit_remark' => $credit_remark, 'id_payment' => $id_payment, 'is_refferal_by' => $acc_data['is_refferal_by']);
                        }
                    }
                }
                return $data;
            } else {
                return $data;
            }
        } else {
            return $data;
        }
    }

    function calcIncentiveAmt($type, $pay_amt, $ref_val)
    {
        $cash_point = 0;
        if ($type == 1) {
            $cash_point = ($pay_amt * $ref_val) / 100;
        } else {
            $cash_point = $ref_val;
        }
        return $cash_point;
    }

    function dateDiff($date1, $date2)
    {
        $date1_ts = strtotime($date1);
        $date2_ts = strtotime($date2);
        $diff = $date2_ts - $date1_ts;
        return round($diff / 86400);
    }

    function insertEmployeeIncentive($refdata, $id_scheme_account, $id_payment)

    {

        $model = self::PAY_MODEL;

        $model_name = self::WALL_MODEL;

        $status = FALSE;

        $chkreferral = $this->$model->get_referral_code($id_scheme_account);

        $data = array();

        $checkCreditExist = $this->$model->checkCreditTransExist($id_scheme_account, $id_payment);

        if ($checkCreditExist == 0) {

            if ($chkreferral['referal_code'] != '' && $chkreferral['is_refferal_by'] == 1) {

                $data = $this->$model->get_empreferrals_datas($id_scheme_account);
               
            }
        }

        if (!empty($data) && $chkreferral['is_refferal_by'] == 1 && $checkCreditExist == 0) {
            // echo"<pre>"; print_r($data);exit;
            if ($data['referal_code'] != '' && $refdata['referal_amount'] != '' && $data['id_wallet_account'] != '' && $data['id_wallet_account'] > 0) {

                // insert wallet transaction data //

                $wallet_data = array(

                    'id_wallet_account' => $data['id_wallet_account'],

                    'id_sch_ac' => $id_scheme_account,

                    'date_transaction' => date("Y-m-d H:i:s"),

                    'id_employee' => $data['idemployee'],

                    'transaction_type' => 0,

                    'value' => $refdata['referal_amount'],

                    'id_payment' => $id_payment,

                    'credit_for' => $refdata['credit_remark'],

                    'description' => 'Referral Benefits - ' . $data['cusname'] . ''

                );

                	

                $status = $this->$model_name->wallet_transactionDB('insert', '', $wallet_data);
            }
        }

        return true;
    }

    function customerIncentive($refdata, $id_scheme_account, $id_payment)

    {

        $cusmodel = self::CUS_MODEL;

        $model_name = self::WALL_MODEL;

        $model = self::PAY_MODEL;

        $chkreferral = $this->$model->get_referral_code($id_scheme_account);

        //credit customer introduce staff incentive

        if ($chkreferral['is_refferal_by'] == 0) {

            // customer referal - multiple  

            $this->$cusmodel->update_customer_only(array('is_refbenefit_crt_cus' => 1), $chkreferral['id_customer']);

            //check customer 

            $isEmpRef = $this->$model->get_empRefExist_datas($id_scheme_account);

            //echo '<pre>';print_r($isEmpRef);exit;

            if (sizeof($isEmpRef) > 0) {

                //print_r($refdata);exit;

                if ($refdata['referal_amount'] != '' && $isEmpRef['id_wallet_account'] != null) {

                    // insert wallet transaction data //

                    $wallet_data = array(

                        'id_wallet_account' => $isEmpRef['id_wallet_account'],

                        'id_sch_ac' => $id_scheme_account,

                        'date_transaction' => date("Y-m-d H:i:s"),

                        'id_employee' => $this->session->userdata('uid'),

                        'transaction_type' => 0,

                        'value' => $refdata['referal_amount'],

                        'id_payment' => $id_payment,

                        'credit_for' => 'Customer Intro Scheme Incentive',

                        'description' => 'Customer Intro Referral Benefits - ' . $isEmpRef['cusname'] . ''

                    );

                    // echo"<pre>"; print_r($model_name);exit;

                    $status = $this->$model_name->wallet_transactionDB('insert', '', $wallet_data);

                    // echo $this->db->last_query();exit;

                }
            }
        }
    }

    function upd_firstPayDateAsStartDate($payment)
    {

        $join_date = array('start_date' => $payment['date_payment']);
        if (!empty($payment['start_year'])) {
            $join_date['start_year'] = $payment['start_year'];
        }
        return $this->updData($join_date, 'id_scheme_account', $payment['id_scheme_account'], 'scheme_account');
    }

    function upd_maturityDate($payment)
    {
        $result = TRUE;
        if ($payment['maturity_type'] > 0 && $payment['maturity_days'] != null && $payment['maturity_days'] > 0) {
            $maturity_data = array('maturity_date' => date('Y-m-d', strtotime("+" . $payment['maturity_days'] . " days", strtotime($payment['start_date']))));
            $result = $this->updData($maturity_data, 'id_scheme_account', $payment['id_scheme_account'], 'scheme_account');
        }

        return $result;
    }


    /**
     * ✅Payment mode details table 
     * ✅Receipt no
     * ✅Scheme Account no
     * Internal Client id
     * First payment date as start date
     * Maturity date
     * ✅Installment No, Due date from, Due date to, Grace date, Due Type
     * 
     * Employee referral
     * Customer referral
     * Agent referral
     * 
     * Group code for lucky draw
     * First payment amount/ weight
     * One time premium rate fix
     * 
     * Inter-tool integration insert
     * 
     * Chit deposit estimation [only admin]
     * Gift
     * 
     * Notification - SMS, Email, Whatsapp
     */

    function onPayTranStream($id_payment)
    {
        //get payment details

        $payment = $this->get_payment_details($id_payment);

        //Payment mode details table [Except admin payment]
        if (!empty($payment['id_payGateway'])) {
            $cancel_emd = $this->cancel_existing_mode_details($id_payment);

            $insert_pmd = $this->insert_pay_mode_details($payment);
        }

        //Receipt no generate

        if ($this->chit['receipt_no_set'] == 1 && empty($payment['receipt_no'])) {
            $gen_receipt = $this->generate_receipt_no($payment);
        }

        if (empty($payment['scheme_acc_number'])) {

            $joinDateUpd = $this->upd_firstPayDateAsStartDate($payment);

            $payment = $this->get_payment_details($payment['id_payment']);
            $matDateUpd = $this->upd_maturityDate($payment);

            //Account no generate
            if ($this->chit['schemeacc_no_set'] == 0) {
                $gen_account = $this->generate_account_no($payment);
            }

            //Internal client id
            if ($this->chit['gent_clientid'] == 1) {
                $gen_client = $this->generate_client_id($payment);
            }

            //Lucky draw scheme group code update
            if ($payment['is_lucky_draw'] == 1 && $payment['max_members'] > 0) {
            }

            //First payment amount/ weight
            $fixPayable = $this->fix_payable($payment);
        }

        // update due details like (ins no, due date, due date to, total paid ins)
        $installments = $this->updatedue_details($payment);

        //Inter-tool integration insert
        if( $this->config->item('auto_pay_approval') == 2 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3))
		{ 	 
            if($this->config->item('integrationType') == 1){
                $this->insert_common_data_jil($id_payment);
            }else if($this->config->item('integrationType') == 2){
                $this->insert_common_data($id_payment);
            }  		            
		}
        // print_r($payment);exit;
        if ($payment['referal_code'] != '' && $payment['is_refferal_by'] == 1) { // Employee Referal
            $type = 1; //1- employee 2- agent
            $credit_for = 0;
            $emp_refral = $this->get_Incentivedata($payment['id_scheme'], $payment['id_scheme_account'], $type, $payment['id_payment'], $credit_for);
            if ($emp_refral > 0) {
                // $res = $this->insertEmployeeIncentive($emp_refral, $pay['id_scheme_account'], $pay['id_payment']);
                // if ($emp_refral > 0) {
                foreach ($emp_refral as $emp) {
                    if ($emp['is_refferal_by'] == 1) {
                        if ($emp['referal_amount'] > 0) {
                            $res = $this->insertEmployeeIncentive($emp, $payment['id_scheme_account'], $payment['id_payment']);
                            if ($emp['credit_for'] == 1) {
                                $this->customerIncentive($emp, $payment['id_scheme_account'], $payment['id_payment']);
                            }
                        }
                    }
                }
                // }
            }
        }

        if ($payment['referal_code'] != '' && $payment['is_refferal_by'] == 0) { // Customer Referal
            $type = 1; //1- employee 2- agent
            $credit_for = 1;
            $emp_refral = $this->get_Incentivedata($payment['id_scheme'], $payment['id_scheme_account'], $type, $payment['id_payment'], $credit_for);

            if ($emp_refral > 0) {
                // $res = $this->insertEmployeeIncentive($emp_refral, $pay['id_scheme_account'], $pay['id_payment']);
                // if ($emp_refral > 0) {

                foreach ($emp_refral as $emp) {
                    if ($emp['is_refferal_by'] == 0) {
                        if ($emp['referal_amount'] > 0) {
                            // $res = $this->insertEmployeeIncentive($emp, $payment['id_scheme_account'], $payment['id_payment']);
                            if ($emp['credit_for'] == 1) {
                                $this->customerIncentive($emp, $payment['id_scheme_account'], $payment['id_payment']);
                            }
                        }
                    }
                }
                // }
            }
        }
    }

    function updatedue_details($pay)
    {
        $dt_pay = date('Y-m-d', strtotime(str_replace("/", "-", $pay['date_payment'])));

        $ins_cycle = $this->payment_model->get_due_date($pay['due_type'], $dt_pay, $pay['id_scheme_account']);

        //	print_r($this->db->last_query());exit;

        if (sizeof($ins_cycle[0]) > 0) {

            $cycle_data = array(

                'due_date' => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),

                'due_date_to' => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),

                'grace_date' => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),

                'installment' => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),

                'is_limit_exceed' => (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),

            );

            $this->updData($cycle_data, 'id_payment', $pay['id_payment'], 'payment');

        } else {
            // [FIX] DigiGold same-day multiple payments:
            // get_due_date() returns empty when the due_date slot for today is already
            // claimed by an earlier payment (NOT IN clause blocks the 2nd+ payment).
            // Look up the earlier same-day payment's installment/due dates and reuse them.
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
                $cycle_data = array(
                    'due_date'    => $sameday_pay['due_date'],
                    'due_date_to' => $sameday_pay['due_date_to'],
                    'grace_date'  => $sameday_pay['grace_date'],
                    'installment' => $sameday_pay['installment'],
                );
                $this->updData($cycle_data, 'id_payment', $pay['id_payment'], 'payment');
            }
        }

        //RHR scheme ends....

        //update paid installments against account...

        $paid = $this->getPaidInsData($pay['id_scheme_account']);

        if (sizeof($paid) > 0) {

            $paid_ins = array('total_paid_ins' => $paid['paid_installments']);

            $this->updData($paid_ins, 'id_scheme_account', $pay['id_scheme_account'], 'scheme_account');
        }
    }

    function get_due_date($due_type, $date_payment, $id_scheme_account)

    {

        $result = [];

        $where = '';

        $sch = $this->get_scheme_details($id_scheme_account);

        $now = date('Y-m-d');

        $date_payment = date('Y-m-d', strtotime($date_payment));

        $first_payment_date = (!empty($sch['first_payment_date']) ? $sch['first_payment_date'] : $date_payment);

        $c_wh = "and  dt.due_date_from NOT IN (SELECT p.due_date from payment p where p.payment_status = 1 and p.due_date is not null and p.id_scheme_account = sa.id_scheme_account) limit 1";

        if ($due_type == 'ND' || $due_type == '') {

            $where = "and  date('" . $date_payment . "') BETWEEN dt.due_date_from and dt.due_date_to " . $c_wh . " ";
        } else if ($due_type == 'AD') {

            $where = "and dt.due_date_from >= date('" . $date_payment . "')  " . $c_wh . " ";
        } else if ($due_type == 'PD') {

            $where = "and dt.due_date_from <= date('" . $date_payment . "') " . $c_wh . " ";
        } else if ($due_type == 'allow_pay') {

            $where = "and  dt.due_date_from NOT IN (SELECT p.due_date from payment p where p.payment_status = 1 and p.due_date is not null and p.id_scheme_account = sa.id_scheme_account)";
        } else if ($due_type == 'MND') {

            $where = "and '" . $date_payment . "' BETWEEN dt.due_date_from AND dt.due_date_to ";
        } else if ($due_type == 'current_range') {

            $where = "and  date('" . $date_payment . "') BETWEEN dt.due_date_from and dt.due_date_to";
        } else {

            $where = $c_wh;
        }

        if ($sch['installment_cycle'] == 2) {  //by days duration cycle

            $days_duration = $sch['ins_days_duration'] - 1;

            $grace_days = $sch['grace_days'] - 1;

            $sql = "SELECT 		if(

    			dt.due_date_to < '" . $date_payment . "' ,

    			'PD',

    			if( '" . $date_payment . "' BETWEEN dt.due_date_from AND dt.due_date_to  , 

    				'ND',

    				if(

                       dt.due_date_to >= '" . $date_payment . "'

                        ,'AD','-'

                    )

    			) 

    		) as due_type,

    		dt.installment,dt.due_date_from,dt.due_date_to,dt.grace_date,

    		if((('" . $due_type . "' = 'ND' OR '" . $due_type . "' = '') AND date('" . $date_payment . "') BETWEEN dt.due_date_from and dt.grace_date) OR ('" . $due_type . "' = 'AD')  , '0','1') as is_limit_exceed	

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

    		WHERE  sa.id_scheme_account = " . $id_scheme_account . "  " . $where . " ";

            $pay = $this->db->query($sql)->result_array();
        }

        //daily payment cycle

        else if ($sch['installment_cycle'] == 1) {

            $sql = "SELECT 		if(

        		dt.due_date_to < '" . $date_payment . "' ,

        		'PD',

        		if( '" . $date_payment . "' = dt.due_date_from  , 

        			'ND',

        			if(

                       dt.due_date_to > '" . $date_payment . "'

                        ,'AD','-'

                    )

        		) 

        	) as due_type,

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

        	WHERE  sa.id_scheme_account = " . $id_scheme_account . "  " . $where . " ";

            $pay = $this->db->query($sql)->result_array();
        }

        //monthly cycle    

        else if ($sch['installment_cycle'] == 0) {

            $sql = "SELECT 		if(

            			dt.due_date_to < '" . $date_payment . "' ,

            			'PD',

            			if( '" . $date_payment . "' BETWEEN dt.due_date_from AND dt.due_date_to  , 

            				'ND',

            				if(

                               dt.due_date_to >= '" . $date_payment . "'

                                ,'AD','-'

                            )

            			) 

            		) as due_type,

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

                    WHERE sa.id_scheme_account = " . $id_scheme_account . "  " . $where . " ";

            $pay = $this->db->query($sql)->result_array();
        }

        if ($due_type == 'allow_pay') {

            foreach ($pay as $p) {

                $grouped_dues[$p['due_type']][] = $p;
            }

            foreach ($grouped_dues as $key => $gd) {

                $result[] = array('due_name' => $key, 'dues_count' => sizeof($gd));
            }
        } else {

            $result = $pay;
        }

        return $result;
    }

    function get_scheme_details($id_scheme_account)

    {

        $sql = $this->db->query("SELECT s.total_installments,s.grace_days,s.installment_cycle,s.ins_days_duration,

		(SELECT MIN(date(p.date_payment)) FROM payment p WHERE p.payment_status = 1 and p.id_scheme_account = sa.id_scheme_account) as first_payment_date,	

	    s.scheme_name,c.firstname as cus_name,IFNULL(c.lastname,'') as lastname,c.mobile,sa.account_name,concat(s.code,'-',sa.scheme_acc_number) as scheme_acc_number,sa.firstPayment_amt,sa.received_wgt,sa.fixed_metal_rate,sa.fixed_wgt,sa.fixed_rate_on,sa.maturity_date,s.otp_price_fix_type,s.one_time_premium,

	    date_format(sa.start_date,'%d-%m-%Y') as start_date,date_format(sa.fixed_rate_on,'%d-%m-%Y') as fixed_rate_on,IFNULL(s.description,'') as description,

	    s.emp_refferal,s.emp_incentive_closing,s.ref_benifitadd_ins_type,s.ref_benifitadd_ins,sa.id_employee,s.firstPayamt_as_payamt,

	    s.emp_refferal_value

        FROM scheme_account sa 

        LEFT JOIN scheme s ON s.id_scheme=sa.id_scheme

        LEFT JOIN customer c ON c.id_customer=sa.id_customer

        WHERE sa.id_scheme_account=" . $id_scheme_account . "");

        //print_r($this->db->last_query());exit;

        return $sql->row_array();
    }

    function getPaidInsData($id_scheme_account)

    {

        $date = date('Y-m-d');

        $acc = $this->db->query("select sa.id_scheme_account,sa.start_date,s.total_installments,s.installment_cycle, s.ins_days_duration,sa.maturity_date,sa.lapse_date,

	                        IF(sa.is_opening=1,

                              IFNULL((sa.paid_installments + COUNT(Distinct Date_Format(pay.date_payment,'%Y%m'))),0),

                               if(s.installment_cycle = 0,

                                  IFNULL(IF(s.min_chance=s.max_chance,COUNT(pay.id_payment),COUNT(Distinct Date_Format(pay.date_payment,'%Y%m'))),0),

                                  if(s.installment_cycle = 1,

                                	  IFNULL(IF(s.min_chance=s.max_chance,COUNT(pay.id_payment),COUNT(Distinct Date_Format(pay.date_payment,'%Y%m%d'))),0),

                                	  if(s.installment_cycle = 2,

                                		IFNULL(COUNT( Date_Format(pay.due_date,'%Y%m')),0),

                                		IFNULL(COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')),0)

                                	  )

                                  )

                               )

                            ) as old_paid_installments,

                            IFNULL(COUNT(Distinct pay.installment),0) as paid_installments,

                            if(s.installment_cycle = 0,

                               PERIOD_DIFF(date_format('" . $date . "','%Y%m'),date_format(sa.start_date,'%Y%m'))+1,

                               if(s.installment_cycle = 1,

                                  DATEDIFF(date_format('" . $date . "','%Y%m%d'),date_format(sa.start_date,'%Y%m%d'))+1,

                                  if(s.installment_cycle = 2,

                                  CEIL((DATEDIFF(date_format('" . $date . "','%Y%m%d'),date_format(sa.start_date,'%Y%m%d'))+1) /s.ins_days_duration),'-'

                                    )

                                 )

                              ) 

                              as ins_till_date

                    from payment pay 

					left join scheme_account  sa on sa.id_scheme_account = pay.id_scheme_account

					left join scheme s on s.id_scheme = sa.id_scheme

                    where pay.payment_status=1 and sa.id_scheme_account = " . $id_scheme_account . "

                    group by sa.id_scheme_account")->row_array();

        return $acc;
    }


    function getPendCancelPayments($previousDay, $currentDay, $id_branch, $id_pg)
    {
        $this->db->select('
			p.ref_trans_id as txn_id,
			p.payu_id,
			p.payment_status,
			p.pay_email,
			p.actual_trans_amt,
			c.mobile,
			p.payment_ref_number,
			SUM(p.payment_amount) as payment_amount,
			p.id_payGateway,
			p.date_payment,
			c.last_payment_on
		');
        $this->db->from('payment p');
        $this->db->join('scheme_account sa', 'sa.id_scheme_account = p.id_scheme_account', 'left');
        $this->db->join('customer c', 'c.id_customer = sa.id_customer', 'left');

        // Optional filters
        if ($id_pg > 0) {
            $this->db->where('p.id_payGateway', $id_pg);
        }
        if ($id_branch > 0) {
            $this->db->where('p.id_branch', $id_branch);
        }
        // $this->db->where('p.id_payment', 562);
        // Date range condition
        $this->db->where("DATE(p.date_payment) >=", date('Y-m-d', strtotime($previousDay)));
		$this->db->where("DATE(p.date_payment) <=", date('Y-m-d', strtotime($currentDay)));
		// Subtract 30 minutes from current timestamp
        $this->db->where("p.date_payment <", date("Y-m-d H:i:s", strtotime("-30 minutes")));
        // Payment status condition
        $this->db->where_in('p.payment_status', [7]);
        // Group and limit
        $this->db->group_by('p.ref_trans_id');
        $this->db->limit(50);
        $sql = $this->db->get();
        // echo $this->db->last_query();exit;
        return $sql->result_array();
    }
    function updateGatewayResponse($gatewayResponse, $transactionDetail)
    {
       // Extract all numeric keys (actual data rows)
        $rows = $transactionDetail['rows'] ?? [];

        // Collect all id_payment values
        $includeIds = array_column($rows, 'id_payment');

        // Get ref_trans_id (assuming all rows share the same one)
        $refTransId = $rows[0]['ref_trans_id'] ?? null;

        if (!$refTransId) {
            return ['status' => false, 'message' => 'Missing ref_trans_id'];
        }
        // Debugging check
        // print_r([$includeIds, $excludePayuIds, $refTransId]); exit;

        // Apply conditions for update
        $this->db->where('ref_trans_id', $refTransId);

        if (!empty($includeIds)) {
            $this->db->where_in('id_payment', $includeIds);
        }

        // Perform the update
        $status = $this->db->update('payment', $gatewayResponse);

        // Fetch updated records (same conditions)
        $this->db->select('id_payment, payu_id, payment_status');
        $this->db->from('payment');
        $this->db->where('ref_trans_id', $refTransId);

        if (!empty($includeIds)) {
            $this->db->where_in('id_payment', $includeIds);
        }
        /* if (!empty($excludePayuIds)) {
            $this->db->where_not_in('payu_id', $excludePayuIds);
        } */

        $payids = $this->db->get()->result_array();

        return [
            'status' => $status,
            'payids' => $payids
        ];
    }
    function getBranchGatewayData($branch_id, $pg_code)
    {
        $sql = "SELECT param_1,param_2,param_3,param_4,pg_code,api_url,id_pg from gateway where is_default=1 and active=1 and  pg_code=" . $pg_code . " " . ($branch_id != '' ? "and id_branch=" . $branch_id . "" : '') . "";
        $result = $this->db->query($sql)->row_array();
        //	print_r($sql);exit;
        return $result;
    }

    function getPendpayment_Data($previousDay, $currentDay, $id_branch, $id_pg)
    {
        /*$previousDay = "2025-01-11";
        $currentDay = "2025-01-11";*/
        $sql = $this->db->query(
            "SELECT 
                                    p.ref_trans_id as txn_ids,payment_status,p.payment_ref_number as order_id,p.payment_amount,p.pay_email as email,c.mobile  
                                FROM payment p
                                left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
                                left join customer c on c.id_customer=sa.id_customer 
                                WHERE " . ($id_pg > 0 ? ' p.id_payGateway =' . $id_pg . ' and ' : '') . " 
                                " . ($id_branch > 0 ? ' p.id_branch =' . $id_branch . ' and ' : '') . "
                                p.date_payment >= '" . $previousDay . " 00:00:00' AND p.date_payment <= '" . $currentDay . " 23:59:59'
                                AND p.date_payment <= DATE_SUB('" . date('Y-m-d H:i:s') . "', INTERVAL 2 MINUTE) 
                                And (p.payment_status=3 OR p.payment_status=7 OR p.payment_status=4) order by p.id_payment desc LIMIT 50"
        );
        // and p.ref_trans_id is not null group by p.ref_trans_id 
        // echo $this->db->last_query();exit;
        return $sql->result_array();
    }
    function getPayData($txnid)
	        {
    	$sql = "Select sa.ref_no,flexible_sch_type,sa.id_customer,firstPayment_amt,s.firstPayamt_as_payamt,s.firstPayamt_maxpayable,p.id_payment,sa.id_scheme_account,sa.scheme_acc_number,sa.id_scheme,cs.schemeacc_no_set,cs.receipt_no_set,cs.scheme_wise_receipt,p.ref_trans_id,cs.edit_custom_entry_date,
    	cs.custom_entry_date,p.payment_amount,s.one_time_premium,sa.id_branch as branch,cs.allow_referral,cs.gent_clientid,s.firstPayamt_maxpayable,is_lucky_draw,c.mobile,p.pay_email as email,sum(p.payment_amount) as payment_amount,p.ref_trans_id as txn_ids,p.id_payGateway,g.pg_code
    			 From payment p
    			 left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
    			 left join customer c on c.id_customer= sa.id_customer
    			 left join scheme s on s.id_scheme=sa.id_scheme
    			 left join gateway g on g.id_pg=p.id_payGateway
    			 join chit_settings cs
    			 Where p.ref_trans_id='".$txnid."' group by p.ref_trans_id" ;
    	return $this->db->query($sql)->result_array();	
    }
    function update_paymentMode($mode){

		$mode = strtolower(trim($mode));

		// Check if mode_name or short_code already matches (case-insensitive)
		$this->db->from('payment_mode');
		 $this->db->where('LOWER(short_code)', $mode);
		$query = $this->db->get();

		$short_code = strtoupper($mode); // e.g., upi_payment -> UPI_PAYMENT

		if ($query->num_rows() == 0 && !empty($mode)) {
			// Convert mode to proper format
			$formatted_mode_name = ucwords(str_replace('_', ' ', $mode)); // e.g., upi_payment -> Upi Payment
			

			// Insert new payment mode
			$data = array(
				'mode_name'   => $formatted_mode_name,
				'short_code'  => $short_code,
				'status'      => 1,
				'sort_order'  => 0,
				'show_in_pay' => 0
			);
			$this->db->insert('payment_mode', $data);
		}
		return $short_code;
	}

    //To insert payment and registration details in intermediate table

	function insert_common_data_old($id_payment)

	{

	    

		$model = 'syncapi_model';

		$this->load->model($model);

		

		//getting payment detail

		$pay_data = $this->$model->getPaymentByID($id_payment);	

	

		//storing temp values

		$ref_no = $pay_data[0]['ref_no'];

		$id_scheme_account = $pay_data[0]['id_scheme_account']; 

		// Validate gent_clientid setting — skip client_id if disabled
		$chit_settings = $this->db->query("SELECT gent_clientid FROM chit_settings LIMIT 1")->row_array();
		if(empty($chit_settings['gent_clientid']) || $chit_settings['gent_clientid'] == 0){
			$pay_data[0]['client_id'] = '';
		}

		$isCusRegExists = $this->$model->checkCusRegExists($id_scheme_account,$ref_no);

		if(!$isCusRegExists['status']){

		     $reg = $this->$model->getCustomerByID($id_scheme_account);	

             //insert customer registration detail

             if($reg)

             {

            	$reg[0]['record_to']= 1 ;

            	$reg[0]['is_registered_online']= 2 ;  // 2 - online record

            	$reg[0]['ref_no']		= $ref_no;

            	// Skip clientid if gent_clientid is disabled
            	if(empty($chit_settings['gent_clientid']) || $chit_settings['gent_clientid'] == 0){
            		$reg[0]['clientid'] = NULL;
            	}

            	$status = $this->$model->insert_CustomerReg($reg[0]);

             }	

		}

		$isTranExists = $this->$model->checkTransExists($ref_no);

		if(!$isTranExists['status'])

		{

            //insert payment detail

            $pay_data[0]['record_to'] = 1;	

            $pay_data[0]['payment_type'] = 1;	// 1 - online 

            $status =	$this->$model->insert_transaction($pay_data[0]); 

		}

		//echo $this->db->last_query();exit;

		return true;

	}
    
	function insert_common_data($id_payment)
	{
		$model = self::API_MODEL;

		$this->load->model($model);

		//getting payment detail

		$pay_data = $this->$model->getPaymentByID($id_payment);

		//storing temp values
		$ref_no = $pay_data[0]['ref_no'];
		$id_scheme_account = $pay_data[0]['id_scheme_account']; 

		$isCusRegExists = $this->$model->checkCusRegExists($id_scheme_account,$ref_no);

		$reg = $this->$model->getCustomerByID($id_scheme_account);

		$reg_1 = $this->$model->getCustomerDet($id_scheme_account);

		$reg[0]['record_to']= 1 ;

		$reg[0]['is_registered_online']= 2 ;  // 2 - online record

		$reg[0]['ref_no']		= $ref_no;
		$grp_name = '';
		
		if(!$isCusRegExists['status']) {

            // Skip clientid if gent_clientid is disabled
            $chit_settings = $this->db->query("SELECT gent_clientid FROM chit_settings LIMIT 1")->row_array();
            if(empty($chit_settings['gent_clientid']) || $chit_settings['gent_clientid'] == 0){
                $reg[0]['clientid'] = '';
                $pay_data[0]['client_id'] = '';
            }
			//insert customer registration detail
			$status = $this->$model->insert_CustomerReg($reg[0]);

			$runDirectAPI = true;

		} 
       /*  elseif($isCusRegExists['status'] && $isCusRegExists['clientid'] == null && $isCusRegExists['is_transferred'] == 'N' && $chit_settings['gent_clientid'] == 1) {

			$reg_data = array(
				'clientid' => "ON-".$id_scheme_account
			);
			$this->$model->update_CustomerReg($reg_data, $isCusRegExists['id_customer_reg']);

			$runDirectAPI = true;

		}  */
        elseif ($isCusRegExists['status'] && $isCusRegExists['is_transferred'] == 'N') {

			$runDirectAPI = true;
		} else {

			$runDirectAPI = false; // for all other cases
		}

		if($runDirectAPI && $this->config->item('directAPI') == '1') {

            if (!empty($reg[0]['maturity_date'])) {

                $maturitydate = $reg[0]['maturity_date'];
            } else {

                $total_installment = $reg_1[0]['total_installment'];
                    $maturitydate = date('Y-m-d', strtotime("+" . $reg_1[0]['maturity_days'] . " days" , strtotime($reg[0]['reg_date'])));

                $maturitydate = date('Y-m-d', strtotime("+" . $total_installment . " months", strtotime($reg[0]['reg_date']))); 
                if($reg_1[0]['maturity_type'] == 2){
                    $maturitydate = date('Y-m-d', strtotime("+" . ($reg_1[0]['maturity_days'] + $reg_1[0]['closing_maturity_days']) . " days", strtotime($reg[0]['reg_date'])));

                }
                else{
                    $maturitydate = date('Y-m-d', strtotime("+" .$reg_1[0]['closing_maturity_days'] . " days", strtotime($reg[0]['reg_date'])));
                } 
            }
		
			$account = array(
				'customer' => array(
					'customerid'    => $reg_1[0]['id_customer'],
					'customerName'  => $reg[0]['firstname'],
					'mobileNo'      => $reg[0]['mobile'],
					'branch'        => (int) $reg[0]['id_branch'],
					'insert_update' => 1,
					'cardnumber'    => $reg[0]['mobile'],
					'nomineeMobile' => $reg_1[0]['nomineeMobile'],
					'doorNo'        => 'NA',
					'street'        => 'NA',
					'area'          => $reg_1[0]['area'],
					'taluk'         => 'NA',
					'city'          => $reg_1[0]['city'],
					'pinCode'       => $reg_1[0]['pincode'],
					'state'         => $reg_1[0]['state']
				),
				'joining' => array(
					'clientid'     => $reg[0]['clientid'],
					'schemeid'     => (int) $reg_1[0]['id_scheme'],
					'schemerefid'  => (int) $id_scheme_account,
					'schemeamount' => (int) $pay_data[0]['amount'],
					'cardnumber'   => $reg[0]['mobile'],
					'groupname'    => $reg[0]['sync_scheme_code'],
					'startdate'    => $reg[0]['reg_date'],
					'enddate'      => $maturitydate,
					'branch'       => (int) $reg[0]['id_branch'],
					'digi'         => (int) $reg[0]['is_digi']
				)
			);
            
			$response = $this->sendtoDirectApi('/scheme-joining-insertion/insert',$account);

			if($response->success == true) {
				$cus_reg_data = $this->$model->getCustomerRegbyID($id_scheme_account);
				
				$acc_data = array(
					'scheme_acc_number' => $response->data->softwareJoinNo,
					'ref_no'            => $response->data->clientid,
					'date_upd'          => date("Y-m-d H:i:s")
				);

				if (!empty($response->data->softwareJoinNo)) {
					$acc_status = $this->$model->update_account(
						$acc_data, 
						$cus_reg_data[0]['id_scheme_account'], 
						$cus_reg_data[0]['id_customer_reg']
					);
				}
				
			}

			// Log response
			if (!is_dir($this->log_dir.'/directAPI')) {
				mkdir($this->log_dir.'/directAPI', 0777, true);
			}

			$log_path = $this->log_dir.'/directAPI/response'.date("Y-m-d").'.txt';
			$ldata = "\n".date('d-m-Y H:i:s')
				." \n Acc Postdata : ".json_encode($account,true)
				." \n Acc Response :".json_encode($response,true);

			file_put_contents($log_path, $ldata, FILE_APPEND | LOCK_EX);
		}

		$isTranExists = $this->$model->checkTransExists($ref_no);
	
		$payID_data = $this->$model->getPayIDdet($id_payment);

        $pay_data[0]['client_id'] = $payID_data[0]['clientid'] ;
        

		if(!$isTranExists['status'])
		{

			//insert payment detail

			$pay_data[0]['record_to'] = 1;	

			$pay_data[0]['payment_type'] = 1;	// 1 - online 

			$status =	$this->$model->insert_transaction($pay_data[0]); 

			$runPayDirect = true;

		} 
        /* elseif ($isTranExists['status'] && ($isTranExists['clientid'] == null || $isTranExists['clientid'] == '') && $chit_settings['gent_clientid'] == 1) {
			$trans_data = array(
				'client_id' => $pay_data[0]['client_id']
			);
			$this->$model->update_transaction($trans_data,$isTranExists['id_transaction']);

			$runPayDirect = true;
		}  */
        else if ($isTranExists['status'] && $isTranExists['is_transferred'] == 'N' && ($payID_data[0]['scheme_acc_number'] != null || $payID_data[0]['scheme_acc_number'] != '')) {

			$runPayDirect = true;

		}else {
			
			$runPayDirect = false;
		}

			//For online payments Send in direct API and update receipt no , ref no 

		if ($runPayDirect && $this->config->item('directAPI') == '1') {
				$payment = array(
                    "payment" => array(
                        "paymentbranch" => (int)$pay_data[0]['id_branch'],
                        "schemeid" => (int)$payID_data[0]['id_scheme'],
                        "schemename" => $payID_data[0]['scheme_name'],
                        "schemeamount" => (float)$pay_data[0]['amount'],
                        "groupno" => ($payID_data[0]['scheme_acc_number'] ? $payID_data[0]['scheme_acc_number'] : ''),
                        "groupname" => ($payID_data[0]['group_code'] != "" ? $payID_data[0]['group_code'] : ''),
                        "customermobile" => (int) $pay_data[0]['mobile'],
                        "cardnumber" => $pay_data[0]['mobile'],
                        "customerid" => (int)$payID_data[0]['id_customer'],
                        "monthyear" => $pay_data[0]['payment_date'],
                        "saved_weight" => (float)$pay_data[0]['weight'],
                        "saved_benefitswt" => (float)$pay_data[0]['saved_benefits_wgt'],
                        "saved_benefit_amt" => (float)$pay_data[0]['saved_benefit_amt'],
                        "installment" => (int) $payID_data[0]['installment'],
                        "benefit_value" => $pay_data[0]['benefit_value'],
                        "benefit_type" => (int) $pay_data[0]['benefit_type'],
                        "is_digi" => (int) $pay_data[0]['is_digi'],
                        "goldrate" => (int)$pay_data[0]['rate'],
                        "customername" => $payID_data[0]['customername'],
                        "onlinepaymentrefid" => ($pay_data[0]['pay_trans_id'] ? $pay_data[0]['pay_trans_id'] : ''),
                        "onlinepayment" => ($payID_data[0]['added_by'] == 2 || $payID_data[0]['added_by'] == 4) ? 1 : 0,
                        "onlineamount" => (float)$pay_data[0]['amount'],
                        "clientid" => ($payID_data[0]['clientid'] ? $payID_data[0]['clientid'] : ''),
                        "schemerefid" => (string) $id_payment
                    )
			);
				
				$response = $this->sendtoDirectApi('/scheme-payment-entry-insertion/insert',$payment);
						
				if ($response->success == true) {
					$isClientID =  $this->$model->checkClientID($pay_data[0]['id_scheme_account'],$response->data->clientid);

					if (!empty($response->data->softwarePaymentId)) {
						if ($isClientID['status']) {
							$pay_array = array(
								'receipt_no' => $response->data->softwarePaymentId,
								'date_upd'	 => date("Y-m-d H:i:s")
							);

							$pay_status = $this->$model->updatedirPayment($pay_array,$pay_data[0]['ref_no']);

						}
					}
					
				}

				if (!is_dir($this->log_dir.'/directAPI')) 
				{
					mkdir($this->log_dir.'/directAPI', 0777, true);
				}
				$log_path = $this->log_dir.'/directAPI/response'.date("Y-m-d").'.txt';
				$ldata = "\n".date('d-m-Y H:i:s')." \n Postdata : ".json_encode($payment,true)." \n Response :".json_encode($response,true);
				file_put_contents($log_path,$ldata,FILE_APPEND | LOCK_EX);
		}
		
		//echo $this->db->last_query();exit;

		return true;

	}

	function sendtoDirectApi($api,$postData)
	{
		$url = $this->config->item('directAPIurl').$api;

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($postData),
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: application/json"
			),
		));

		$response = curl_exec($curl);
		//print_r($response);exit;

		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			return false;
		} else {
			return json_decode($response);
		}
	}


}
