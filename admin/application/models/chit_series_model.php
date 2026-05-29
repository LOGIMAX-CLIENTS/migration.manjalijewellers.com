<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Chit_series_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();

        $this->load->model('payment_model');

        $this->chit = $this->chit_settings();

        $this->log_dir = 'log/' . date("Y-m-d");
        if (!is_dir($this->log_dir)) {
            mkdir($this->log_dir, 0777, TRUE);
        }
    }

    function insertData($data, $table)
    {
        $status = $this->db->insert($table, $data);
        return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
    }

    function updData($data, $id_field, $id_value, $table)
    {
        $edit_flag = 0;
        $this->db->where($id_field, $id_value);
        $edit_flag = $this->db->update($table, $data);
        return ($edit_flag == 1 ? TRUE : FALSE);
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

            if ($payment['is_lucky_draw'] == 1 && !empty($payment['group_code'])) {
                $where[] = "sa.group_code = '{$payment['group_code']}'";
            }
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

    function upd_firstPayDateAsStartDate($id_payment)
    {
        //get payment details

        $payment = $this->get_payment_details($id_payment);

        $join_date = array('start_date' => $payment['date_payment'], 'start_year' => $payment['receipt_year']);
        return $this->updData($join_date, 'id_scheme_account', $payment['id_scheme_account'], 'scheme_account');
    }


    /**
     * ✅Receipt no
     * ✅Scheme Account no
     */

    function onPayTranStream($id_payment)
    {
        //get payment details

        $payment = $this->get_payment_details($id_payment);


        //Receipt no generate

        if ($this->chit['receipt_no_set'] == 1 && empty($payment['receipt_no'])) {
            $gen_receipt = $this->generate_receipt_no($payment);
        }

        if (empty($payment['scheme_acc_number'])) {
            $joinDateUpd = $this->upd_firstPayDateAsStartDate($id_payment);
            $payment = $this->get_payment_details($id_payment);
            //Account no generate
            if ($this->chit['schemeacc_no_set'] == 0) {
                $gen_account = $this->generate_account_no($payment);
            }
        }

        // Update due details: installment no, due_date, due_date_to, grace_date
        $this->updatedue_details($payment);
    }

    /**
     * Update installment number, due dates, and paid installments count
     * for the given payment record.
     */
    function updatedue_details($pay)
    {
        $dt_pay = date('Y-m-d', strtotime(str_replace("/", "-", $pay['date_payment'])));

        $ins_cycle = $this->payment_model->get_due_date($pay['due_type'], $dt_pay, $pay['id_scheme_account']);

        if (!empty($ins_cycle) && isset($ins_cycle[0]) && sizeof($ins_cycle[0]) > 0) {

            $cycle_data = array(
                'due_date'        => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),
                'due_date_to'     => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),
                'grace_date'      => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),
                'installment'     => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),
                'is_limit_exceed' => (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),
            );

            $this->updData($cycle_data, 'id_payment', $pay['id_payment'], 'payment');
        }
    }

}
