<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ret_billing_model extends CI_Model

{
	function __construct()

	{

		parent::__construct();
		$this->load->model('ret_reports_model');
	}
	// General Functions

	public function insertData($data, $table)
    {
        $query = $this->db->query("SHOW COLUMNS FROM `$table`");
        $columns = $query->result_array();

        
        $default_values = [];
        foreach ($columns as $column) {
            if (!is_null($column['Default'])) {
                $default_values[$column['Field']] = $column['Default'];
            } else {
                // If no default value, use an empty string or null based on column nullability
                $default_values[$column['Field']] = $column['Null'] === 'YES' ? null : '';
            }
        }
        
        foreach ($data as $field => $value) {
            // Remove fields that do not exist in the table columns
            if (!array_key_exists($field, $default_values)) {
                unset($data[$field]);
                continue;
            }
            
            // If the value is empty, set it to the default value
            if ((empty($value) || $value == 'null')  ) {
                if($value === 0 || $value === '0'){
                    $data[$field] = 0;
                }else{
                    $data[$field] = $default_values[$field];
                }
            }
        }
    
        $insert_flag = $this->db->insert($table, $data);

        return ($insert_flag == 1 ? $this->db->insert_id() : 0);
    }

    public function updateData($data, $id_field, $id_value, $table)
    {    
        $query = $this->db->query("SHOW COLUMNS FROM `$table`");
        $columns = $query->result_array();

        
        $default_values = [];
        foreach ($columns as $column) {
            if (!is_null($column['Default'])) {
                $default_values[$column['Field']] = $column['Default'];
            } else {
                // If no default value, use an empty string or null based on column nullability
                $default_values[$column['Field']] = $column['Null'] === 'YES' ? null : '';
            }
        }
        foreach ($data as $field => $value) {
            // Remove fields that do not exist in the table columns
            if (!array_key_exists($field, $default_values)) {
                unset($data[$field]);
                continue;
            }

            // If the value is empty, set it to the default value
            
            if ((empty($value) || $value == 'null')) {
                if($value === 0 || $value === '0'){
                    $data[$field] = 0;
                }else{
                    $data[$field] = $default_values[$field];
                }
            
            }
        }

        $edit_flag = 0;

        $this->db->where($id_field, $id_value);

        $edit_flag = $this->db->update($table,$data);

        return ($edit_flag==1?$id_value:0);

    }

	// public function insertData($data, $table)

	// {

	// 	$insert_flag = 0;

	// 	$insert_flag = $this->db->insert($table, $data);

	// 	return ($insert_flag == 1 ? $this->db->insert_id() : 0);
	// }

	public function insertBatchData($data, $table)

	{

		$insert_flag = 0;

		$insert_flag = $this->db->insert_batch($table, $data);

		if ($this->db->affected_rows() > 0) {

			return TRUE;
		} else {

			return FALSE;
		}
	}

	// public function updateData($data, $id_field, $id_value, $table)

	// {

	// 	$edit_flag = 0;

	// 	$this->db->where($id_field, $id_value);

	// 	$edit_flag = $this->db->update($table, $data);

	// 	return ($edit_flag == 1 ? $id_value : 0);
	// }

	public function deleteData($id_field, $id_value, $table)

	{

		$this->db->where($id_field, $id_value);

		$status = $this->db->delete($table);

		return $status;
	}



	function get_tag_status($tag_id)

	{

		$sql = $this->db->query("SELECT * from ret_taging where tag_id=" . $tag_id . "");

		return $sql->row_array();
	}



	function get_esti_status($est_item_id)

	{

		$sql = $this->db->query("SELECT * from ret_estimation_items where est_item_id=" . $est_item_id . "");

		return $sql->row_array();
	}



	function get_old_esti_status($est_old_itm_id)

	{

		$sql = $this->db->query("SELECT * from ret_estimation_old_metal_sale_details where old_metal_sale_id=" . $est_old_itm_id . "");

		return $sql->row_array();
	}

	function viewdb($post_data)
	{

		$data = $this->db->query("SELECT *
		FROM ret_section_nontag_item_log  m1
order by id_nontag_log desc limit 10");

		return $data->result_array();
	}



	function get_branch($id_branch)

	{

		$sql = $this->db->query("SELECT * from branch where id_branch=" . $id_branch . "");

		return $sql->row_array();
	}

	/*function generateRefNo($id_branch,$field,$metal_type)

	{

		$branch_code 	='';

		$ref_no 		='';

		$ref_code 		='';

		$fin_year 		= $this->get_FinancialYear();



		if($id_branch!='')

		{

			$branch=$this->get_branch($id_branch);

			$branch_code=$branch['short_name'];

		}



		$metal_details=$this->get_metal_details($metal_type);



		$last_no=$this->get_bill_no($id_branch,$field,$metal_type);

		if($last_no!=null && $last_no!='')

		{

			$code=explode("/",$last_no);

			$bill_fin_year=$code[1];

			$last_no =($fin_year['fin_year_code']>$bill_fin_year ? 0 :$code[4]);

			$bill_number = (int) $last_no;

		  	$bill_number++;

		}

		else{

			$bill_number=1;

		}

		if($field=='sales_ref_no')

		{

			$ref_code='SA';

		}

		else if($field=='pur_ref_no'){

			$ref_code='PU';

		}

		else if($field=='order_adv_ref_no')

		{

			$ref_code='OR';

		}else if($field=='s_ret_refno')

		{

			$ref_code='SR';

		}



		$ref_no=($branch_code!='' ? $branch_code.'/' :'').$fin_year['fin_year_code'].'/'.($ref_code!='' ? $ref_code.'/':'').$metal_details['metal_code'].'/'.$bill_number;

		return $ref_no;

	}*/

	/*function get_bill_no($id_branch,$field,$metal_type)

	{

		$ref_no='';

        $sql=$this->db->query("SELECT ".$field." From ret_billing where ".$field." is not null

        ".($id_branch!='' ? " and id_branch=".$id_branch."" :'')."

        ".($metal_type!='' ? " and metal_type=".$metal_type."" :'')."

        order by bill_id DESC LIMIT 1");

		//print_r($this->db->last_query());exit;

		$max_no=$sql->row_array();

		if(sizeof($max_no)>0)

		{

			if($max_no[$field]!='' && $max_no[$field]!=null)

			{

				$ref_no=$max_no[$field];

			}

		}



		return $ref_no;

	}*/





	function generateRefNo($id_branch, $field, $metal_type, $is_eda)

	{

		$ref_no 		= '';

		$fin_year 		= $this->get_FinancialYear();

		//$metal_details=$this->get_metal_details($metal_type);

		$last_no = $this->get_bill_no($id_branch, $field, $metal_type, $is_eda);

		if ($last_no != NULL) {
			$code_det = explode('-', $last_no);
			$LastBillNo = (int) end($code_det);
			$LastBillNo++;
			$code_number = str_pad($LastBillNo, 5, '0', STR_PAD_LEFT);
			$ref_no = $code_number;
		} else {
			$code_number = str_pad('1', 5, '0', STR_PAD_LEFT);
			$ref_no = $code_number;
		}

		return $ref_no;
	}

	function get_bill_no($id_branch, $field, $metal_type, $is_eda)

	{

		$fin_year = $this->get_FinancialYear();

		$sql = "SELECT " . $field . " as lastBill_no FROM ret_billing

		where fin_year_code='" . $fin_year['fin_year_code'] . "' and  " . $field . " is not null

		" . ($is_eda != '' ? " and is_eda=" . $is_eda . "" : '') . "
		" . ($id_branch != '' && $id_branch > 0 ? " and id_branch=" . $id_branch . "" : '') . "
		ORDER BY bill_id DESC LIMIT 1 FOR UPDATE";
		//print_r($sql);exit;
		return $this->db->query($sql)->row()->lastBill_no;
	}





	function get_branch_details($id_branch)

	{

		$data = array();

		$sql = $this->db->query("SELECT * From ret_bill_gift_voucher_settings  where status=1 and is_default=1" . ($id_branch != '' && $id_branch > 0 ? " and id_branch=" . $id_branch . "" : '') . "");

		$data = $sql->row_array();

		if ($data['validity_days'] != '') {

			$data['validate_date'] = date("d-m-Y", strtotime($data['validity_days'] . 'days'));
		}



		return $data;
	}



	function CheckProductAvailability($id)

	{

		$sql = $this->db->query("select s.id_gift_voucher,s.id_product,s.issue,s.utilize from ret_gift_issue_redeem_prod s where id_gift_voucher=" . $id);

		return $sql->result_array();
	}

	function get_FinancialYear()

	{

		$sql = $this->db->query("SELECT fin_year_code From ret_financial_year where fin_status=1");

		return $sql->row_array();
	}



	function get_ret_settings($settings)

	{

		$data = $this->db->query("SELECT value FROM ret_settings where name='" . $settings . "'");

		return $data->row()->value;
	}



	function get_gift_voucher_settings()

	{

		$data = $this->db->query("SELECT metal,gift_type,sale_value,credit_value,validity_days,utilize_for FROM ret_bill_gift_voucher_settings where status=1 and is_default=1");

		return $data->row_array();
	}



	function get_branchwise_rate($id_branch)

	{

		$is_branchwise_rate = $this->session->userdata('is_branchwise_rate');

		if ($id_branch != '' && $id_branch != 0 && $is_branchwise_rate == 1) {

			$sql = "SELECT  b.name as name,m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,

		    Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime,m.goldrate_18ct,m.goldrate_14ct,m.goldrate_9ct

		    FROM metal_rates m

		    LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates

		    left join branch b on b.id_branch=br.id_branch

		    " . ($id_branch != '' ? " WHERE br.id_branch=" . $id_branch . "" : '') . " ORDER by br.id_metalrate desc LIMIT 1";
		} else {

			$sql = "SELECT  m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,

		    Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime,m.goldrate_18ct,m.goldrate_14ct,m.goldrate_9ct

		    FROM metal_rates m

		    ORDER by m.id_metalrates desc LIMIT 1";
		}

		return $this->db->query($sql)->row_array();
	}



	function get_customer($id)

	{

		$customers = $this->db->query("Select

		   c.id_customer,c.firstname,c.lastname,c.date_of_birth,c.date_of_wed,

		   a.address1,a.address2,a.address3,ct.name as city,a.pincode,s.name as state,cy.name as country,

		   c.phone,c.mobile,c.email,c.nominee_name,c.nominee_relationship,c.nominee_mobile,

		   c.cus_img,c.pan,c.pan_proof,c.voterid,c.voterid_proof,c.rationcard,c.rationcard_proof,a.id_country,a.id_city,a.id_state,c.id_employee,

		   (Select count(id_scheme_account) as accounts from scheme_account where id_customer=1 and active=1 and is_closed=0) as accounts,

   	c.comments,c.username,c.passwd,c.is_new,c.active,c.`date_add`,c.`date_upd`, IFNULL(c.gender,'') AS gender, c.cus_type,c.vip_up_time,c.vip_up_by

			From

			  customer c

			left join address a on(c.id_customer=a.id_customer)

			left join country cy on (a.id_country=cy.id_country)

			left join state s on (a.id_state=s.id_state)

			left join city ct on (a.id_city=ct.id_city)

			where c.active=1 and c.id_customer=" . $id);

		//print_r($this->db->last_query());exit;

		return $customers->row_array();
	}

	function ajax_getBillingList($data)

	{

		$profile_settings = $this->get_profile_settings($this->session->userdata('profile'));

		$type = '';
		$FromDt     =$this->input->post('FromDt');

			$ToDt     =$this->input->post('ToDt');
		
			$d1 = date_create($FromDt);
        
			$d2 = date_create($ToDt);
        
			$FromDt = date_format($d1,"Y-m-d");
        
			$ToDt = date_format($d2,"Y-m-d");

		// if ($_POST['dt_range'] != '') {

		// 	$dateRange = explode('-', $_POST['dt_range']);

		// 	$from = str_replace('/', '-', $dateRange[0]);

		// 	$to = str_replace('/', '-', $dateRange[1]);

		// 	$d1 = date_create($from);

		// 	$d2 = date_create($to);

		// 	$FromDt = date_format($d1, "Y-m-d");

		// 	$ToDt = date_format($d2, "Y-m-d");
		// }





		$sql = $this->db->query("SELECT bill.bill_id,concat(b.short_name,'-',if(IFNULL(bill.sales_ref_no,'')!='',concat('SA-',bill.sales_ref_no),if(IFNULL(bill.pur_ref_no,'')!='',concat('PU-',bill.pur_ref_no),if(IFNULL(bill.s_ret_refno,'')!='',concat('SR-',bill.s_ret_refno),if(bill.bill_type=5,concat('OD-',bill.order_adv_ref_no),if(bill.bill_type=8,concat('CC-',bill.credit_coll_refno),bill.bill_no)))))) as bill_no,

        if(bill_type = 1,'Sales',if(bill_type = 2,'Sales&Purchase',if(bill_type = 3,'Sales&Return',if(bill_type = 4,'Purchase',if(bill_type = 5,'Order Advance',if(bill_type = 6,'Advance',if(bill_type = 7,'Sales Return',if(bill_type=8,'Credit Collection',if(bill_type=9,'Order Delivery',if(bill.bill_type=10,'Chit Pre Close',if(bill.bill_type=11,'Repair Order Delivery',if(bill.bill_type=12,'Supplier Sales Bill',if(bill.bill_type=13,'Sales Transfer','Sales Return Transfer'))))))))))))) as bill_type,

        if(bill_status = 1,'Success',if(bill_status = 2,'Cancelled','')) as bill_status,

        date_format(bill_date, '%d-%m-%Y %H:%i') as bill_date,bill.bill_status as status,

        CASE WHEN bill.customer_name is not null then bill.customer_name
            WHEN (bill.billing_for=1 || bill.billing_for=2) then concat(cus.firstname,' ',if(cus.lastname!=NULL,cus.lastname,''))
            ELSE k.firstname END as customer,

        if(bill.billing_for=1 || bill.billing_for=2 , cus.mobile,k.contactno1 ) as mobile,

        IFNULL(if(bill.bill_type=13 or bill.bill_type=14,tot_bill_amount,tot_amt_received),0) as tot_bill_amt,print_taken,if(date(d.entry_date)=date(bill.bill_date),'1','0') as allow_cancel,b.name as branch_name,

        k.firstname as karigar_name,bill.bill_type as billing_type,k.contactno1 as karigar_mobile,

		if(emp_bill.emp_name!='',emp_bill.emp_name,if(emp_bill_old.emp_name!='',emp_bill_old.emp_name,if(e.emp_code!='',CONCAT(e.emp_code,'-',e.firstname),e.firstname))) as emp_name,
		bill.billing_for,bill.id_branch,ifnull(emp_bill.bill_id,'') as billdet_bill_id,ifnull(ret_amt.payment_amount,0) as return_amt, ins.bill_id as ins_bill_id
        FROM ret_billing as bill

		LEFT JOIN ret_insurance as ins ON ins.bill_id = bill.bill_id

        LEFT JOIN customer as cus ON cus.id_customer = bill.bill_cus_id


        LEFT JOIN ret_karigar k on k.id_karigar=bill.bill_cus_id

        LEFT JOIN ret_day_closing d on d.id_branch=bill.id_branch

        LEFT JOIN branch b on b.id_branch=bill.id_branch

        LEFT JOIN employee e on e.id_employee=bill.created_by

        LEFT JOIN (SELECT r.bill_id,e.est_item_id,est.estimation_id, GROUP_CONCAT(DISTINCT emp.firstname,'.',emp.lastname) as emp_name

		FROM ret_bill_details r

		LEFT JOIN ret_estimation_items as e ON e.est_item_id = r.esti_item_id

		LEFT JOIN ret_estimation as est ON est.estimation_id = e.esti_id

		LEFT JOIN employee as emp ON emp.id_employee = est.created_by  group by r.bill_id) as emp_bill on emp_bill.bill_id=bill.bill_id

		LEFT JOIN(SELECT est.created_by,bill_old.bill_id,GROUP_CONCAT(DISTINCT emp.firstname,'.',emp.lastname) as emp_name

		FROM ret_bill_old_metal_sale_details  bill_old

        LEFT JOIN ret_estimation_old_metal_sale_details as est_old on est_old.old_metal_sale_id=bill_old.esti_old_metal_sale_id

		LEFT JOIN ret_estimation as est on est.estimation_id=est_old.est_id

		LEFT JOIN employee as emp ON emp.id_employee = est.created_by  group by bill_old.bill_id) as emp_bill_old on emp_bill_old.bill_id=bill.bill_id

		LEFT JOIN (
			SELECT bill_id,sum(payment_amount) as payment_amount FROM `ret_billing_payment` WHERE type = 2 group by bill_id
		) as ret_amt  ON ret_amt.bill_id= bill.bill_id



         where  ".($FromDt!= '' && $ToDt!='' ? '(date(bill.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')."



        " . ($data['bill_no'] != '' ? " and bill.bill_no=" . $data['bill_no'] . "" : '') . "



        " . ($data['id_branch'] != 0 && $data['id_branch'] != '' ? " and bill.id_branch=" . $data['id_branch'] . "" : '') . "



       " . ($profile_settings['allow_bill_type'] == 3 ? " and (bill.is_eda=1 OR bill.is_eda=2)" : ($profile_settings['allow_bill_type'] == 1 ? " and bill.is_eda=1" : " and bill.is_eda=2")) . "



         ORDER BY bill.bill_id desc");

		// echo $this->db->last_query();exit;

		$result =  $sql->result_array();



		foreach ($result as $val) {

			$val['bill_no'] = $this->get_bill_no_format_detail($val['bill_id'], $type);

			$return_data[] = $val;
		}



		if ($data['id_branch'] != '' && $data['id_branch'] != 0) {

			$dayClose = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing where id_branch=" . $data['id_branch']);

			$cur_entry_date = $dayClose->row()->entry_date;

			if ($profile_settings['allow_bill_type'] == 2) {

				if ($FromDt != $cur_entry_date) {

					$return_data = [];
				}
			}
		}

		return $return_data;
	}

	function ajax_getApprovalBillingList($data)
	{

		if ($_POST['dt_range'] != '') {

			$dateRange = explode('-', $_POST['dt_range']);

			$from = str_replace('/', '-', $dateRange[0]);

			$to = str_replace('/', '-', $dateRange[1]);

			$d1 = date_create($from);

			$d2 = date_create($to);

			$FromDt = date_format($d1, "Y-m-d");

			$ToDt = date_format($d2, "Y-m-d");
		}

		$sql = $this->db->query("SELECT r.id_issue_receipt, r.bill_no, date_format(r.bill_date, '%d-%m-%Y') as billdate,

                                concat(ifnull(cus.firstname, ''), '-', ifnull(cus.mobile,'')) as customer,

                                tag.piece ,tag.tag_code, tag.gross_wt, tag.less_wt, tag.net_wt,

                                concat(ifnull(sup.firstname,''), '-', ifnull(sup.contactno1, '')) as supplier,

                                date_format(po.po_date, '%d-%m-%Y') as purchaseon, po.po_ref_no, br.name as branchname, emp.lastname as emp_name,

                                ifnull(cusor.pur_no, '') as order_no, ifnull(date_format(cusor.order_date, '%d-%m-%Y'), '') as orderedon,

                                ifnull(m.order_status, '') as order_status_msg, ifnull(cusor.order_status,'') as orderstatus, tag.tag_id ,lot.gold_smith as id_karigar,

                                date_format(b.bill_date,'%d-%m-%Y') as sale_billdate,IFNULL(b.bill_no,'') as sale_billno,IFNULL(b.bill_id,'') as sale_billid,

                                tag.tag_status

                                FROM ret_adv_receipt_tags t

                                LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = t.adv_rcpt_issue_receipt_id

                                LEFT JOIN ret_taging as tag ON tag.tag_id = t.adv_rcpt_tagid

                                LEFT JOIN ret_lot_inwards as lot ON lot.lot_no = tag.tag_lot_id

                                LEFT JOIN ret_purchase_order as po ON po.po_id = lot.po_id

                                LEFT JOIN customerorderdetails as ordet ON ordet.approval_tagid = tag.tag_id

                                LEFT JOIN customerorder as cusor ON cusor.id_customerorder = ordet.id_customerorder

                                LEFT JOIN order_status_message m ON m.id_order_msg=cusor.order_status

                                LEFT JOIN customer as cus ON cus.id_customer = r.id_customer

                                LEFT JOIN ret_karigar as sup ON sup.id_karigar = lot.gold_smith

                                LEFT JOIN branch as br ON br.id_branch = r.id_branch

                                LEFT JOIN employee as emp ON emp.id_employee =  r.created_by

                                LEFT JOIN ret_bill_details dt ON dt.tag_id = tag.tag_id

                                LEFT JOIN ret_billing b ON b.bill_id = dt.bill_id AND b.bill_status = 1

                                WHERE tag.tag_type = 1 and r.bill_status = 1

                                " . ($data['order_status'] == 0 ? " AND (cusor.order_status = " . $data['order_status'] . " OR cusor.order_status IS NULL)" : " AND cusor.order_status = " . $data['order_status'] . "") . "

                                " . ($data['order_status'] == 3 ? " AND tag.tag_status = 11" : " AND (tag.tag_status = 11 OR tag.tag_status = 0 OR tag.tag_status = 1)") . "

                                " . ($data['dt_range'] != '' ? 'AND date(r.bill_date) BETWEEN "' . $FromDt . '" AND "' . $ToDt . '"' : '') . "

                                " . ($data['bill_no'] != '' ? " and r.bill_no=" . $data['bill_no'] . "" : '') . "

                                " . ($data['id_branch'] != 0 && $data['id_branch'] != '' ? " and r.id_branch=" . $data['id_branch'] . "" : '') . "

                                ORDER BY r.id_issue_receipt desc");

		//echo $this->db->last_query();exit;

		return $sql->result_array();
	}



	function get_entry_records($est_id)

	{

		$sql = $this->db->query("SELECT estimation_id,

				concat(firstname, '-', mobile) as cus_name,mobile,

				date_format(estimation_datetime, '%d-%m-%Y %H:%i:%s') as estimation_datetime,

				cus_id, created_by, date_format(created_time, '%d-%m-%Y %H:%i:%s') as created_time,

				has_converted_order, discount, gift_voucher_amt, total_cost, est.id_branch

				FROM ret_estimation as est

				LEFT JOIN customer as cus ON cus.id_customer = est.cus_id

				WHERE est.estimation_id ='" . $est_id . "'");

		return $sql->result_array()[0];
	}



	function getBillingMetalrate($id_branch, $date)

	{

		$date_add = date('Y-m-d', strtotime($date));

		$is_branchwise_rate = $this->session->userdata('is_branchwise_rate');

		if ($id_branch != '' && $id_branch != 0 && $is_branchwise_rate == 1) {

			$sql = "SELECT  b.name as name,m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,

		    Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime,m.goldrate_18ct,m.goldrate_14ct,m.goldrate_9ct

		    FROM metal_rates m

		    LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates

		    left join branch b on b.id_branch=br.id_branch

		    where date(m.updatetime)='" . $date_add . "' " . ($id_branch != '' ? " and br.id_branch=" . $id_branch . "" : '') . " ORDER by br.id_metalrate desc LIMIT 1";
		} else {

			$sql = "SELECT  m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,

		    Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime,m.goldrate_18ct,m.goldrate_14ct,m.goldrate_9ct

		    FROM metal_rates m

		    where date(m.updatetime)='" . $date_add . "'

		    ORDER by m.id_metalrates desc LIMIT 1";
		}

		return $this->db->query($sql)->row_array();
	}

	function getBillingDetails($bill_id, $type = "")

	{

		$items_query = $this->db->query("SELECT b.bill_type,b.bill_cus_id,IFNULL(b.pan_no,'') as pan_no,b.bill_no,date_format(b.bill_date,'%d-%m-%Y') as bill_date, date_format(b.created_time,'%h:%i %p') as bill_time,DATE_FORMAT(b.created_time, '%Y-%m-%d %H:%i:%s') AS bill_date_time, DATE_FORMAT(b.created_time, '%Y-%m-%d %H:%i:%s') AS created_on,

		b.bill_id,
		CASE WHEN b.customer_name is not null then b.customer_name
        	Else concat(c.mobile,'-',c.firstname) END cus_name,
		b.id_branch,b.tot_bill_amount,date_format(b.credit_due_date,'%d-%m-%Y') as credit_due_date,b.is_credit,b.ref_bill_id as ref_bill_id,b.print_taken,IFNULL(b.tot_discount,0) as tot_discount,a.id_state as cus_state,

		 CASE WHEN b.customer_name is not null then b.customer_name
            ELSE c.firstname END as customer_name,

		c.mobile,br.name as branch_name,b.tot_amt_received,round_off_amt, b.return_round_off_amt ,b.pur_ref_no,e.id_employee,

		br.short_name as branch_code,d.name as delivery_location,IFNULL(v.village_name,'') as village_name,IFNULL(c.gst_number,'') as gst_number,a.address1,a.address2,a.pincode,a.address3,c.cus_type,b.pan_no as pan_number,ct.name as city,

		IFNULL(g.amount,0) as gift_issue_amount,IFNULL(g.weight,0) as gift_issue_weight,date_format(g.valid_to,'%d-%m-%Y') as valid_to,g.code,IFNULL(b.handling_charges,0) as handling_charges,gift.utilize_for,IFNULL(gift.note,'') as note,b.is_trail,

		IFNULL(b.goldrate_22ct,0) as goldrate_22ct,IFNULL(b.silverrate_1gm,0) as silverrate_1gm,IFNULL(b.goldrate_14ct,0) as goldrate_14ct,IFNULL(b.goldrate_9ct,0) as goldrate_9ct,b.billing_for,IFNULL(b.id_cmp_emp,'') as id_cmp_emp,concat(IFNULL(cmp.firstname,''),'-',cmp.mobile) as cmp_user_name,IFNULL(b.tcs_tax_amt,0) as tcs_tax_amt,IFNULL(b.tcs_tax_per,0) as tcs_tax_per,b.fin_year_code,

		b.bill_status,IFNULL(b.credit_disc_amt,0) as credit_disc_amt,e.firstname as emp_name, b.metal_type,s.name as cus_state,cy.name as cus_country,IFNULL(b.advance_deposit,0) as advance_deposit,b.make_as_advance,rct.bill_no as adv_recpt_no,

		concat(k.firstname,'',IFNULL(concat('-','',k.contactno1),'')) as karigar_name,IFNULL(k.address1,'') as karigar_address1,IFNULL(k.address2,'') as karigar_address2,

		b.delivered_at,IFNULL(del_add.address1,'') as del_add_address1,IFNULL(del_add.address2,'') as del_add_address2,

		IFNULL(del_add.address3,'') as del_add_address3,IFNULL(cyt.name,'') as del_country_name,

		IFNULL(st.name,'') as del_state_name,IFNULL(cty.name,'') as del_city_name,IFNULL(del_add.pincode,'') as del_pincode,IFNULL(k.gst_number,'') as karigar_gst_number,

		IFNULL(b.sales_ref_no,'') as sales_ref_no, IFNULL(b.approval_ref_no,'') as approval_ref_no, IFNULL(b.pur_ref_no,'') as pur_ref_no,IFNULL(b.s_ret_refno,'') as s_ret_refno,mt.metal_code,IFNULL(b.credit_coll_refno,'') as credit_coll_refno,IFNULL(b.order_adv_ref_no,'') as order_adv_ref_no,CONCAT(br.short_name,b.fin_year_code,'-',b.s_ret_refno) as sales_refno,CONCAT(br.short_name,b.fin_year_code,'-',b.pur_ref_no) as purchase_ref_no,

		IFNULL(b.chit_preclose_refno,'') as chit_preclose_refno,s.state_code,st.state_code,b.goldrate_18ct,

		IFNULL(b.credit_due_amt,0) as credit_due_amt,IFNULL(b.credit_ret_amt,0) as credit_ret_amt,b.to_branch,b.from_branch, IFNULL(c.gender,'') AS gender,

		date_format(b.created_time,'%Y-%m-%d') as bill_created_time,fc.counter_short_code,b.is_credit,b.is_to_be,

		ifnull(c.driving_license_no,'') as driving_license_no,ifnull(c.passport_no,'') as passport_no,ifnull(b.remark, '') as remark,e.emp_code as bill_emp_code,e.firstname as bill_emp_name, emp.firstname as attender_name, emp.emp_code as attender_code, b.is_to_be, fc.counter_name as counter_name,b.cusdel_irn as irnno,

		ifnull(b.return_charges,0) as return_charges,b.is_eda,rct.bill_no as adv_bill_no,date_format(rct.bill_date,'%d-%m-%Y') as adv_bill_date,ifnull(b.aadhar_no,'') as adhar_no

		FROM ret_billing b

		LEFT JOIN customer c ON c.id_customer=b.bill_cus_id

		LEFT JOIN ret_karigar k on k.id_karigar=b.bill_cus_id

		LEFT JOIN address a on a.id_customer=c.id_customer

		LEFT JOIN branch br on br.id_branch=b.id_branch

		LEFT join employee e on e.id_employee=b.created_by

		LEFT join employee emp on emp.id_employee=b.id_employee

		LEFT JOIN ret_financial_year f on f.fin_status = 1

		LEFT JOIN ret_sale_delivery d on d.id_sale_delivery=b.id_delivery

		LEFT JOIN village v on v.id_village=c.id_village

		LEFT JOIN gift_card g on g.bill_id=b.bill_id

		LEFT JOIN ret_bill_gift_voucher_settings gift on gift.id_set_gift_voucher=g.id_set_gift_voucher

		LEFT JOIN ret_customer_company_users cmp on cmp.id_cmp_emp=b.id_cmp_emp

		LEFT JOIN metal mt on mt.id_metal=b.metal_type

		LEFT JOIN city ct on a.id_city=ct.id_city

		LEFT JOIN state s on s.id_state=a.id_state

		left join country cy on (a.id_country=cy.id_country)



		LEFT JOIN ret_bill_delivery del_add on del_add.bill_id=b.bill_id

		LEFT JOIN country cyt on cyt.id_country=del_add.id_country

		LEFT JOIN state st on st.id_state=del_add.id_state

		LEFT JOIN city cty on cty.id_city=del_add.id_city

		LEFT JOIN ret_branch_floor_counter fc ON fc.counter_id = b.counter_id

		LEFT JOIN ret_issue_receipt rct  ON rct.deposit_bill_id = b.bill_id



		where b.bill_id=" . $bill_id . "");

		//echo "<pre>"; echo $this->db->last_query();exit;

		$data = $items_query->row_array();

		$data['pan_verification'] = $this->get_ret_settings('pan_verification');

		$data['invoice_no'] = $this->get_bill_no_format_detail($bill_id, $type);



		if ($data['ref_bill_id'] != '') {



			//$data['ref_bill_no']=$this->getBill_details($data['ref_bill_id']);

			$data['ref_bill_no'] = $this->get_bill_no_format_detail($data['ref_bill_id'], $type);
			$data['ref_numbers']=$this->getBill_refnumbers($data['ref_bill_id']);

			if ($data['bill_type'] == 8) {

				$data['due_amount'] = $this->get_due_bill($data['ref_bill_id']);

				$data['tot_adv_received'] = $this->get_paid_bill($data['ref_bill_id']);

				$data['tot_paid_amt'] = $this->get_credit_pay_amount($data['ref_bill_id']);

				$data['previous_credit_collections'] = $this->get_previous_credit_collections($data['ref_bill_id'], $data['bill_date_time'],$data['bill_type']);
			}
		}



		$data['transfer_details'] = $this->getCompanyDetails($data['to_branch']);

		$advance_adj = $this->get_advance_adjusted($bill_id);
		$advance_refund = $this->get_advance_refund($bill_id);
		// $data['adv_adj_amt'] = $advance_adj + $advance_refund;
		$data['adv_adj_amt'] = $advance_adj;
		// $data['advance_refunded'] = $this->get_advance_refund($bill_id);



		$data['adv_rcpt_no'] = $data['bill_type'] == 4 || $data['bill_type'] == 7 ? $this->get_receipt_no_against_purchase($bill_id) : '';

		// $data['adjusted_in_sales']= $data['bill_type']==1 ? $this->get_adjust_in_sales($bill_id) : '';



		// $data['sales_rtn_bill_no']=$data['bill_type']==7  || $data['bill_type']==4 ? $this->get_sales_return_bill_no($bill_id) : '';



		// $data['ord_adv_adj']=$data['bill_type']==5 ? $this->get_ord_adv_adj($bill_id) :'';

		//print_r($data);exit;

		return $data;
	}



	function get_ord_adv_adj($bill_id)

	{

		$ord_adj = $this->db->query("SELECT u.utilized_amt,r.deposit_type,m.metal,

		concat(br.short_name,'',b.fin_year_code,'-',b.pur_ref_no) as adjusted_bill_no,u.bill_adv_id

		FROM ret_advance_utilized u

		LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = u.id_issue_receipt

		LEFT JOIN ret_billing b ON b.bill_id = r.deposit_bill_id

		LEFT JOIN ret_bill_old_metal_sale_details s On s.bill_id = r.deposit_bill_id

		LEFT JOIN metal m ON m.id_metal = s.metal_type

		LEFT JOIN branch br ON br.id_branch = b.id_branch

		where u.bill_id = " . $bill_id . " ");

		return $ord_adj->result_array();
	}

	function get_adjust_in_sales($bill_id)

	{

		$adjusted = $this->db->query("SELECT b.bill_no,r.id_issue_receipt,au.utilized_amt,m.metal,r.deposit_type,r.receipt_type,r.deposit_bill_id,

		concat(br.short_name,'',bill.fin_year_code,'-',bill.pur_ref_no) as adjusted_bill_no

		FROM ret_billing b

		LEFT JOIN ret_advance_utilized au ON au.bill_id = b.bill_id

		LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt  = au.id_issue_receipt

		LEFT JOIN ret_bill_old_metal_sale_details s On s.bill_id = r.deposit_bill_id

		left join ret_billing bill on bill.bill_id = r.deposit_bill_id

		LEFT JOIN branch br ON br.id_branch = bill.id_branch

		LEFT JOIN metal m ON m.id_metal = s.metal_type

		where r.id_issue_receipt is not null and b.bill_id=" . $bill_id . " and b.bill_type!=5 ");

		return $adjusted->result_array();
	}



	function get_sales_return_bill_no($bill_id)

	{

		$sales_return_adjusted = $this->db->query("SELECT concat(br.short_name,'',b.fin_year_code,'-',b.s_ret_refno) as bill_no,

		concat(br.short_name,'',b.fin_year_code,'-',b.sales_ref_no) as sales_ref_no,

		concat(br.short_name,'',b.fin_year_code,'-',b.order_adv_ref_no) as order_adv_ref_no

		FROM ret_issue_receipt r

		LEFT JOIN ret_advance_utilized au ON au.id_issue_receipt = r.id_issue_receipt

		LEFT JOIN ret_billing b ON b.bill_id = au.bill_id

		LEFT JOIN branch br ON br.id_branch = b.id_branch

		WHERE b.bill_id is not null and r.deposit_bill_id=" . $bill_id . "");

		return $sales_return_adjusted->result_array();
	}





	function get_order_adj_details($bill_id)

	{

		$sql = $this->db->query("SELECT id_customerorder FROM ret_billing_advance WHERE adjusted_bill_id = " . $bill_id . " GROUP by id_customerorder");

		return $sql->result_array();
	}



	function get_due_bill($bill_id)

	{

		$items_query = $this->db->query("SELECT (b.tot_bill_amount-b.tot_amt_received) as due_amount

		FROM ret_billing b

		where b.bill_id=" . $bill_id . "");

		return $items_query->row('due_amount');
	}



	function get_paid_bill($bill_id)

	{

		$items_query = $this->db->query("SELECT b.tot_amt_received

		FROM ret_billing b

		where b.bill_id=" . $bill_id . "");

		return $items_query->row('tot_amt_received');
	}

	function getBill_details($bill_id)

	{

		$items_query = $this->db->query("SELECT b.bill_no

		FROM ret_billing b

		where b.bill_id=" . $bill_id . "");

		return $items_query->row('bill_no');
	}


	// function getPaymentDetails($bill_id)

	// {

	// 	$pay_details = array("pay_details" => array());

	// 	$items_query = $this->db->query("SELECT bk.bank_name,p.cheque_no,p.id_billing_payment,p.type,p.bill_id,p.payment_for,p.payment_amount,p.card_no,p.cvv,p.payment_mode,IFNULL(p.payment_ref_number,'') as payment_ref_number,date_format(p.payment_date,'%d-%m-%Y') as payment_date,

	// 	if(p.NB_type=1,'RTGS',if(p.NB_type=2,'IMPS',if(p.NB_type=3,'UPI',if(p.NB_type=4,'NEFT','')))) as transfer_type,date_format(p.cheque_date,'%d-%m-%Y') as cheque_date,date_format(p.net_banking_date,'%d-%m-%Y') as net_banking_date,ifnull(d.device_name,'') as device_name

	// 	FROM ret_billing_payment p

	// 	LEFT JOIN bank bk ON bk.id_bank = p.id_bank

	// 	left join ret_bill_pay_device d on d.id_device = p.id_pay_device

	// 	where p.bill_id=" . $bill_id . "");

	// 	$data = $items_query->result_array();

	// 	foreach ($data as $items) {

	// 		$pay_details['pay_details'][] = array(

	// 			'id_billing_payment'    => $items['id_billing_payment'],

	// 			'type'                  => $items['type'],

	// 			'bill_id'               => $items['bill_id'],

	// 			'payment_for'           => $items['payment_for'],

	// 			'payment_amount'        => $items['payment_amount'],

	// 			'cvv'                   => $items['cvv'],

	// 			'card_no'               => $items['card_no'],

	// 			'payment_mode'          => $items['payment_mode'],

	// 			'payment_ref_number'    => $items['payment_ref_number'],

	// 			'transfer_type'         => $items['transfer_type'],

	// 			'cheque_no'         	=> $items['cheque_no'],

	// 			'bank_name'         	=> $items['bank_name'],

	// 			'net_banking_date'      => $items['net_banking_date'],

	// 			'cheque_date'         	=> $items['cheque_date'],

	// 			'payment_date'         	=> $items['payment_date'],

	// 			'device_name'         	=> $items['device_name'],

	// 		);
	// 	}

	// 	return $pay_details;
	// }

	function getPaymentDetails($bill_id)
{
    $pay_details = array("pay_details" => array());

    $query = $this->db->query("
        SELECT 
            p.cheque_no, 
            p.id_billing_payment, 
            p.type, 
            p.bill_id, 
            p.payment_for, 
            p.payment_amount, 
            p.card_no, 
            p.cvv, 
            p.payment_mode, 
            IFNULL(p.payment_ref_number, '') as payment_ref_number, 
            DATE_FORMAT(p.card_date, '%d-%m-%Y') as card_date,
            DATE_FORMAT(p.payment_date, '%d-%m-%Y') as payment_date, 
            IF(p.NB_type=1, 'RTGS', IF(p.NB_type=2, 'IMPS', IF(p.NB_type=3 OR p.NB_type=5, 'UPI', IF(p.NB_type=4, 'NEFT', '')))) as transfer_type, 
            DATE_FORMAT(p.cheque_date, '%d-%m-%Y') as cheque_date, 
            DATE_FORMAT(p.net_banking_date, '%d-%m-%Y') as net_banking_date, 
            IFNULL(d.device_name, '') as device_name, 
            IFNULL(bk.bank_name, '') as bank_name 
        FROM ret_billing_payment p
        LEFT JOIN bank bk ON bk.id_bank = p.id_bank
        LEFT JOIN ret_bill_pay_device d ON d.id_device = p.id_pay_device
        WHERE p.bill_id = " . intval($bill_id) . "
        
        UNION ALL
        
        SELECT 
            p.cheque_no, 
            NULL AS id_billing_payment, 
            p.type AS type, 
            r.ref_bill_id AS bill_id, 
            NULL AS payment_for, 
            (CASE WHEN p.type = 1 THEN p.payment_amount ELSE -p.payment_amount END) AS payment_amount, 
            p.card_no, 
            p.cvv, 
            p.payment_mode, 
            p.payment_ref_number, 
            DATE_FORMAT(p.card_date, '%d-%m-%Y') as card_date,
            DATE_FORMAT(p.payment_date, '%d-%m-%Y') as payment_date, 
            IF(p.NB_type=1, 'RTGS', IF(p.NB_type=2, 'IMPS', IF(p.NB_type=3, 'UPI', IF(p.NB_type=4, 'NEFT', '')))) as transfer_type, 
            DATE_FORMAT(p.cheque_date, '%d-%m-%Y') as cheque_date, 
            DATE_FORMAT(p.net_banking_date, '%d-%m-%Y') as net_banking_date, 
            '' AS device_name, 
            '' AS bank_name
        FROM ret_issue_rcpt_payment p
        INNER JOIN ret_issue_receipt r ON p.id_issue_rcpt = r.id_issue_receipt
        WHERE r.ref_bill_id = " . intval($bill_id) . "
    ");

    $data = $query->result_array();

    foreach ($data as $items) {
        $pay_details['pay_details'][] = array(
            'id_billing_payment'    => $items['id_billing_payment'],
            'type'                  => $items['type'],
            'bill_id'               => $items['bill_id'],
            'payment_for'           => $items['payment_for'],
            'payment_amount'        => $items['payment_amount'],
            'cvv'                   => $items['cvv'],
            'card_no'               => $items['card_no'],
            'payment_mode'          => $items['payment_mode'],
            'payment_ref_number'    => $items['payment_ref_number'],
            'transfer_type'         => $items['transfer_type'],
            'cheque_no'             => $items['cheque_no'],
            'bank_name'             => $items['bank_name'],
            'net_banking_date'      => $items['net_banking_date'],
            'cheque_date'           => $items['cheque_date'],
            'payment_date'          => $items['payment_date'],
            'card_date'             => $items['card_date'],
            'device_name'           => $items['device_name'],
			);
		}

		return $pay_details;
	}

	function getOtherEstimateItemsDetails($bill_id, $bill_type = '', $type = "")

	{

		$item_details = array();



		$bill_det = $this->getBillingDetails($bill_id, $type);

		$return_data = array("item_details" => array(), "old_matel_details" => array(), "stone_details" => array(), "other_material_details" => array(), "voucher_details" => array(), "chit_details" => array(), "chit_gift_details" => array(), "return_details" => array(), 'advance_details' => array(), "order_adj" => array(), "order_details" => array());

		if ($bill_type != 5 && $bill_type != 13 && $bill_type != 14) {

			$items_query = $this->db->query("SELECT d.bill_det_id,IFNULL(d.esti_item_id,'') as  esti_item_id,est_itms.esti_id,est_itms.item_type,est_itms.purchase_status,

			ifnull(d.product_id, '') as product_id, ifnull(est_itms.tag_id, '') as tag_id,

			ifnull(d.design_id, '') as design_id, ifnull(pro.hsn_code,'') as hsn_code,

			d.purity as purid,d.size, CONCAT(s.value,' ',s.name) as size_name, ifnull(d.uom,'') as uom,d.piece,

			ifnull(d.less_wt,'') as less_wt, IF((istag_merged=0  OR d.esti_item_id is null),d.net_wt,(mrg_tag.mrg_nwt + d.net_wt)) as net_wt,IF((est_itms.istag_merged=0 OR d.esti_item_id is null),d.gross_wt,(d.gross_wt + mrg_tag.mrg_gwt)) as gross_wt,

			d.calculation_based_on, d.wastage_percent, d.mc_value, d.mc_type,

			IF((est_itms.istag_merged=0 OR d.esti_item_id is null),d.item_cost,(mrg_tag.mrg_item_cost + d.item_cost)) as item_cost, ifnull(pro.product_short_code, '-') as product_short_code,

			ifnull(pro.product_name, '-') as product_name, est_itms.is_partial,est_itms.discount,

			ifnull(des.design_code, '-') as design_code,

			ifnull(des.design_name, '') as design_name, pur.purity as purname,

			pro.tgrp_id as tax_group_id , tgrp_name, ifnull(c.id_metal,'') as metal_type,

			ifnull(des.fixed_rate,0) as fixed_rate,d.is_non_tag,d.id_lot_inward_detail

			,IF((est_itms.istag_merged=0 OR d.esti_item_id is null),d.bill_discount,(d.bill_discount + mrg_tag.bill_discount) ) as bill_discount,IF((est_itms.istag_merged=0 OR d.esti_item_id is null),d.item_total_tax,(mrg_tag.mrg_tag_tax +  d.item_total_tax)) as item_total_tax,tax.tax_percentage,tax.tgi_calculation,

			IF((est_itms.istag_merged=0 OR d.esti_item_id is null) ,d.total_cgst,(ifnull(mrg_tag.mrg_tag_cgst,0) + d.total_cgst)) as total_cgst,

			IF((est_itms.istag_merged=0 OR d.esti_item_id is null),d.total_igst,(ifnull(mrg_tag.mrg_tag_igst,0) + d.total_igst)) as total_igst,

			IF((est_itms.istag_merged=0 ),d.total_sgst,(ifnull(mrg_tag.mrg_tag_sgst,0) + d.total_sgst)) as total_sgst,

			ifnull(est_itms.id_orderdetails,'') as id_orderdetails,ifnull(c.name, '-') as cat_name,

			cus.order_no, '' as code_charge, e.firstname as esti_emp_name, e.id_employee AS esti_emp_id,IFNULL(d.mc_discount,0) as mc_discount,

			IFNULL(d.wastage_discount,0) as wastage_discount,IFNULL(d.rate_per_grm,0) as rate_per_grm,IFNULL(d.item_blc_discount,0) as item_blc_discount,c.cat_code as cat_code,b.fin_year_code,tag.hu_id,tag.hu_id2,pu.uom_short_code as pro_uom,

			d.show_huid,IFNULL(d.huid,IFNULL(concat(tag.hu_id,IFNULL(concat(',',tag.hu_id2),'')),IFNULL(tag.hu_id,IFNULL(tag.hu_id2,'')))) as huid, d.is_delivered AS is_delivered, est_itms.isTagsplitted, IFNULL(d.return_item_cost,0) as return_item_cost, d.pure_wt
			From ret_billing b

			Left JOIN ret_bill_details d on d.bill_id=b.bill_id

			LEFT JOIN ret_taging as tag ON tag.tag_id = d.tag_id

			LEFT JOIN customerorderdetails ord on ord.id_orderdetails=d.id_orderdetails

			LEFT JOIN customerorder cus on cus.id_customerorder=ord.id_customerorder

			LEFT JOIN ret_product_master as pro ON pro.pro_id = d.product_id

			left join ret_uom pu on pu.uom_id  = pro.uom_id

			LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id

			LEFT JOIN metal mt on mt.id_metal=c.id_metal

			LEFT JOIN ret_design_master as des ON des.design_no = d.design_id

			LEFT JOIN ret_estimation_items est_itms on est_itms.est_item_id=d.esti_item_id

			LEFT JOIN ret_estimation esti ON esti.estimation_id = est_itms.esti_id

            LEFT JOIN employee e on e.id_employee = esti.created_by

			LEFT JOIN ret_purity as pur ON pur.id_purity = est_itms.purity

			LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id

			LEFT JOIN ret_size s   ON s.id_size = d.size

			LEFT JOIN (SELECT m.est_item_id as ests_item_id,SUM(e.gross_wt) as mrg_gwt,SUM(d.item_cost) as mrg_item_cost,SUM(e.net_wt) as mrg_nwt,d.bill_discount,d.mc_discount, SUM(d.total_cgst) as mrg_tag_cgst,SUM(d.total_sgst) as mrg_tag_sgst,SUM(d.item_total_tax) as mrg_tag_tax, SUM(d.total_igst) as mrg_tag_igst
						FROM ret_est_tag_merge m
						LEFT JOIN ret_estimation_items e ON e.est_item_id = m.ref_est_item_id
						LEFT JOIN ret_bill_details d on d.esti_item_id = m.ref_est_item_id
						GROUP BY m.est_item_id) as mrg_tag ON mrg_tag.ests_item_id = est_itms.est_item_id

			LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,

						m.tax_percentage as tax_percentage,

						i.tgi_calculation as tgi_calculation

						FROM ret_taxgroupitems i

						LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=pro.tgrp_id

			WHERE d.bill_id=" . $bill_id . "  AND (est_itms.istag_merged in (0,1) OR d.esti_item_id is null) GROUP by d.bill_det_id HAVING bill_det_id!=''");

			// echo "<pre>";print_r($this->db->last_query());exit;

			$item_details = $items_query->result_array();
		} else {

			// ST/SRT (bill_type 13/14): group by salesTransType + metal for multi-type summary
			// Sales Return (bill_type 5): group by bill_det_id for per-item detail
			$is_sales_transfer = in_array($bill_type, array(13, 14));

			if ($is_sales_transfer) {
				$select_columns = "IFNULL(SUM(d.piece),0) as piece,
				IFNULL(SUM(d.net_wt),0) as net_wt,IFNULL(SUM(d.gross_wt),0) as gross_wt,
				IFNULL(SUM(d.item_cost),0) as item_cost,IFNULL(SUM(d.item_total_tax),0) as item_total_tax,IFNULL(SUM(d.total_cgst),0) as total_cgst,
				IFNULL(SUM(d.total_igst),0) as total_igst,IFNULL(SUM(d.total_sgst),0) as total_sgst,
				CASE IFNULL(d.salesTransType, 1)
					WHEN 1 THEN IFNULL(c.name, 'TAGGED ITEMS')
					WHEN 2 THEN CONCAT('NON-TAGGED-', IF(d.product_id > 0, IFNULL(mt.metal, 'ITEMS'), IF(d.rate_per_grm > b.silverrate_1gm * 3, 'GOLD', 'SILVER')))
					WHEN 3 THEN CONCAT('OLD METAL ', IF(d.product_id > 0, IFNULL(mt.metal, ''), IF(d.rate_per_grm > b.silverrate_1gm * 3, 'GOLD', 'SILVER')))
					WHEN 4 THEN CONCAT('SALES RETURN-', IF(d.product_id > 0, IFNULL(mt.metal, 'ITEMS'), IF(d.rate_per_grm > b.silverrate_1gm * 3, 'GOLD', 'SILVER')))
					WHEN 5 THEN CONCAT('PARTLY SALE-', IF(d.product_id > 0, IFNULL(mt.metal, 'ITEMS'), IF(d.rate_per_grm > b.silverrate_1gm * 3, 'GOLD', 'SILVER')))
					ELSE IFNULL(c.name, 'ITEMS')
				END as category_name,
				ifnull(pro.hsn_code,'') as hsn_code,
				tax.tax_percentage,d.rate_per_grm,IFNULL(c.cat_code,'') as cat_code,b.fin_year_code,d.bill_det_id,tag.hu_id,tag.hu_id2,
				d.show_huid,IFNULL(d.huid,IFNULL(concat(tag.hu_id,IFNULL(concat(',',tag.hu_id2),'')),IFNULL(tag.hu_id,IFNULL(tag.hu_id2,'')))) as huid,
				IFNULL(tag.tag_code,'') as tag_code,
				IFNULL(tag.lot_purchase_touch,0) as purchase_touch,
				ifnull(pro.product_name,'-') as product_name, ifnull(des.design_name,'') as design_name,
				IFNULL(d.salesTransType, 1) as salesTransType,
				IF(d.product_id > 0, IFNULL(c.id_metal, 0), IF(d.rate_per_grm > b.silverrate_1gm * 3, 1, 2)) as metal_class";
				$group_by = "GROUP By d.salesTransType, metal_class";
			} else {
				$select_columns = "IFNULL(d.piece,0) as piece,
				IFNULL(d.net_wt,0) as net_wt,IFNULL(d.gross_wt,0) as gross_wt,
				IFNULL(d.item_cost,0) as item_cost,IFNULL(d.item_total_tax,0) as item_total_tax,IFNULL(d.total_cgst,0) as total_cgst,
				IFNULL(d.total_igst,0) as total_igst,IFNULL(d.total_sgst,0) as total_sgst,c.name as category_name,ifnull(pro.hsn_code,'') as hsn_code,
				tax.tax_percentage,d.rate_per_grm,c.cat_code as cat_code,b.fin_year_code,d.bill_det_id,tag.hu_id,tag.hu_id2,
				d.show_huid,IFNULL(d.huid,IFNULL(concat(tag.hu_id,IFNULL(concat(',',tag.hu_id2),'')),IFNULL(tag.hu_id,IFNULL(tag.hu_id2,'')))) as huid,
				IFNULL(tag.tag_code,'') as tag_code,
				IFNULL(tag.lot_purchase_touch,0) as purchase_touch,
				ifnull(pro.product_name,'-') as product_name, ifnull(des.design_name,'') as design_name";
				$group_by = "GROUP By d.bill_det_id";
			}

			$items_query = $this->db->query("SELECT " . $select_columns . "

			From ret_billing b

			Left JOIN ret_bill_details d on d.bill_id=b.bill_id

			LEFT JOIN ret_taging as tag ON tag.tag_id = d.tag_id

			LEFT JOIN customerorderdetails ord on ord.id_orderdetails=d.id_orderdetails

			LEFT JOIN customerorder cus on cus.id_customerorder=ord.id_customerorder

			LEFT JOIN ret_product_master as pro ON pro.pro_id = d.product_id

			LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id

			LEFT JOIN metal mt on mt.id_metal=c.id_metal

			LEFT JOIN ret_design_master as des ON des.design_no = d.design_id

			LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity

			LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id

			LEFT JOIN ret_size s   ON s.id_size = d.size

			LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,

						m.tax_percentage as tax_percentage,

						i.tgi_calculation as tgi_calculation

						FROM ret_taxgroupitems i

						LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=pro.tgrp_id

			WHERE d.bill_id=" . $bill_id . "  " . $group_by);

			$return_data['sales_trasnfer_details'] = $items_query->result_array();
			if (!$is_sales_transfer) {
				$return_data['vetri_transfer_details'] = $return_data['sales_trasnfer_details'];
			}
		}

		if (sizeof($item_details) > 0) {

			foreach ($item_details as $item) {

				$return_data['item_details'][] = array(

					'calculation_based_on' => $item['calculation_based_on'],

					'design_code'		  	=> $item['design_code'],

					'pro_uom'		  	    => $item['pro_uom'],

					'cat_name'				=> $item['cat_name'],

					'design_id'				=> $item['design_id'],

					'design_name'			=> $item['design_name'],

					'discount'				=> $item['discount'],

					'est_item_id'			=> $item['esti_item_id'],

					'esti_id'				=> $item['esti_id'],

					'fixed_rate'			=> $item['fixed_rate'],

					'gross_wt'				=> $item['gross_wt'],

					'hsn_code'				=> $item['hsn_code'],

					'is_partial'			=> $item['is_partial'],

					'is_non_tag'			=> $item['is_non_tag'],

					'item_cost'				=> $item['item_cost'],

					'item_type'				=> $item['item_type'],

					'less_wt'				=> $item['less_wt'],

					'mc_type'				=> $item['mc_type'],

					'mc_value'				=> $item['mc_value'],

					'metal_type'			=> $item['metal_type'],

					'net_wt'				=> $item['net_wt'],

					'product_id'			=> $item['product_id'],

					'product_name'			=> $item['product_name'],

					'product_short_code'	=> $item['product_short_code'],

					'purchase_status'		=> $item['purchase_status'],

					'purid'					=> $item['purid'],

					'purname'				=> $item['purname'],

					'piece'					=> $item['piece'],

					'size'					=> $item['size'],

					'size_name'				=> $item['size_name'],

					'tag_id'				=> $item['tag_id'],

					'tax_group_id'			=> $item['tax_group_id'],

					'tgrp_name'				=> $item['tgrp_name'],

					'tax_percentage'		=> $item['tax_percentage'],

					'tgi_calculation'		=> $item['tgi_calculation'],

					'uom'					=> $item['uom'],

					'wastage_percent'		=> $item['wastage_percent'],

					'item_total_tax'		=> $item['item_total_tax'],

					'bill_discount'			=> $item['bill_discount'],

					'total_igst'			=> $item['total_igst'],

					'total_cgst'			=> $item['total_cgst'],

					'total_sgst'			=> $item['total_sgst'],

					'code_charge'           => $item['code_charge'],

					'wastage_discount'		=> $item['wastage_discount'],

					'mc_discount'			=> $item['mc_discount'],

					'rate_per_grm'			=> $item['rate_per_grm'],

					'item_blc_discount'		=> $item['item_blc_discount'],

					'esti_emp_name'			=> $item['esti_emp_name'],

					'esti_emp_id'			=> $item['esti_emp_id'],

					'cat_code'			    => $item['cat_code'],

					'fin_year_code'			=> $item['fin_year_code'],

					'bill_det_id'			=> $item['bill_det_id'],

					'stone_details'         => ($item['bill_det_id'] != '' ? $this->stone_details_by_bill_det_id($item['bill_det_id']) : $this->get_tag_stone_details($item['tag_id'])),

					'order_no'				=> isset($item['order_no']) ? $item['order_no'] : NULL,

					'charges'				=> ($item['esti_item_id'] != '' ? $this->get_other_estcharges($item['esti_item_id']) : ''),

					'hu_id'					=> isset($item['hu_id']) ? $item['hu_id'] : NULL,

					'hu_id2'				=> isset($item['hu_id2']) ? $item['hu_id2'] : NULL,

					'huid'					=> $item['huid'],

					'is_delivered'			=> $item['is_delivered'],

					'show_huid'				=> $item['show_huid'],

					'other_metal_details'	=> ($item['bill_det_id'] != '' ? $this->get_est_other_metal_details($item['bill_det_id']) : []),
					
					'isTagsplitted'			=> $item['isTagsplitted'],

					'return_item_cost'		=> $item['return_item_cost'],
					
					'pure_wt'			=> $item['pure_wt'],

				);
			}
		}


		$tax_details = $this->db->query("SELECT tax.tax_percentage,IFNULL(SUM(d.item_cost),0) as item_cost,IFNULL(SUM(d.item_total_tax),0) as item_total_tax,IFNULL(SUM(d.total_sgst),0) as total_sgst,

		IFNULL(SUM(d.total_cgst),0) as total_cgst,IFNULL(SUM(d.total_igst),0) as total_igst,p.hsn_code

		FROM ret_bill_details d

		LEFT JOIN ret_product_master p ON p.pro_id = d.product_id

		LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id

		LEFT JOIN ret_billing b ON b.bill_id = d.bill_id

		LEFT JOIN (SELECT i.tgi_taxcode,i.tgi_tgrpcode,

								m.tax_percentage as tax_percentage,

								i.tgi_calculation as tgi_calculation

				  FROM ret_taxgroupitems i

				  LEFT JOIN ret_taxmaster m ON m.tax_id = i.tgi_taxcode

				  GROUP BY i.tgi_tgrpcode) as tax on tax.tgi_tgrpcode = d.tax_group_id

		WHERE b.bill_id = " . $bill_id . "

		GROUP BY d.tax_group_id");

		$return_data['tax_details'] = $tax_details->result_array();



		$old_metal_query = $this->db->query("SELECT s.old_metal_sale_id,s.bill_id,s.purpose,s.metal_type,s.item_type,s.gross_wt,s.stone_wt,s.dust_wt,s.stone_wt,s.wastage_percent,

		s.wast_wt,s.rate_per_grm as rate_per_gram,s.rate as amount,s.bill_id,s.bill_discount,s.est_id,s.net_wt, e.firstname as esti_emp_name, e.id_employee AS esti_emp_id,s.id_employee,

		t.metal_type as old_metal_type,s.purity,s.piece,m.metal as metal_name,IFNULL(s.touch,0) as touch

		FROM ret_bill_old_metal_sale_details s

		LEFT JOIN ret_estimation_old_metal_sale_details  AS est_old ON est_old.old_metal_sale_id = s.esti_old_metal_sale_id

		LEFT JOIN ret_estimation esti ON esti.estimation_id = est_old.est_id

		LEFT JOIN employee e on e.id_employee = esti.created_by

		LEFT JOIN metal m ON m.id_metal = s.metal_type

		LEFT JOIN ret_old_metal_type t ON t.id_metal_type=s.id_old_metal_type

		where s.bill_id=" . $bill_id . "");

		//print_r($this->db->last_query());exit;

		$old_matel_details = $old_metal_query->result_array();

		$old_metal_total_qty = 0;
		$old_metal_total_gross_wt = 0;
		$old_metal_total_net_wt = 0;
		$old_metal_sub_total = 0;

		foreach ($old_matel_details as $metal) {

			$return_data['old_matel_details'][] = array(

				'amount'			=> $metal['amount'],

				'bill_id'			=> $metal['bill_id'],

				'est_id'			=> $metal['est_id'],

				'dust_wt'			=> $metal['dust_wt'],

				'stone_wt'			=> $metal['stone_wt'],

				'gross_wt'			=> $metal['gross_wt'],

				'item_type'			=> $metal['item_type'],

				'metal_type'		=> $metal['metal_type'],

				'old_metal_sale_id'	=> $metal['old_metal_sale_id'],

				'purpose'			=> $metal['purpose'],

				'rate_per_gram'		=> $metal['rate_per_gram'],

				'stone_wt'			=> $metal['stone_wt'],

				'wastage_percent'	=> $metal['wastage_percent'],

				'wast_wt'	        => $metal['wast_wt'],

				'bill_discount'		=> $metal['bill_discount'],

				'old_metal_type'	=> $metal['old_metal_type'],

				'net_wt'		    => $metal['net_wt'],

				'esti_emp_name'		=> $metal['esti_emp_name'],

				'esti_emp_id'		=> $metal['esti_emp_id'],

				'purity'		    => $metal['purity'],
				'piece'		        => $metal['piece'],
				'id_employee'		=> $metal['id_employee'],

				'metal_name'		=> $metal['metal_name'],

				'touch'		        => $metal['touch'],

				'stone_details'		=> $this->stone_details_by_bill_id($metal['old_metal_sale_id'])

			);

			// Accumulate totals
			$old_metal_total_qty      += floatval($metal['piece']);
			$old_metal_total_gross_wt += floatval($metal['gross_wt']);
			$old_metal_total_net_wt   += floatval($metal['net_wt']);
			$old_metal_sub_total      += floatval($metal['amount']);
			$old_metal_stone_wt       += floatval($metal['stone_wt']);
		}

		// Store old metal totals
		$return_data['total_old_qty']      = $old_metal_total_qty;
		$return_data['total_old_gross_wt']  = number_format($old_metal_total_gross_wt, 3, '.', '');
		$return_data['total_old_net_wt']    = number_format($old_metal_total_net_wt, 3, '.', '');
		$return_data['total_old_amount']       = number_format($old_metal_sub_total, 2, '.', '');
		$return_data['total_old_stone_wt']       = number_format($old_metal_stone_wt, 3, '.', '');

		$return_details = $this->db->query("SELECT bill_items.bill_det_id, bill.bill_id, bill_items.item_type,bill_items.status,bill_items.return_item_cost,

		ifnull(bill_items.product_id, '') as product_id, ifnull(bill_items.tag_id, '') as tag_id, bill_items.esti_item_id,esti_id,

		ifnull(bill_items.design_id, '') as design_id, ifnull(pro.hsn_code,'') as hsn_code,

		bill_items.size, bill_items.uom, bill_items.piece,

		ifnull(bill_items.less_wt,'') as less_wt, bill_items.net_wt, bill_items.gross_wt,

		bill_items.calculation_based_on, bill_items.wastage_percent, IFNULL(bill_items.mc_value,'') as mc_value, bill_items.mc_type,

		bill_items.item_cost, ifnull(product_short_code, '-') as product_short_code,

		ifnull(product_name, '-') as product_name, bill_items.is_partial_sale,bill_items.bill_discount as discount,bill_items.item_total_tax,

		ifnull(design_code, '-') as design_code,

		ifnull(design_name, '') as design_name, pur.purity as purname,

		pro.tgrp_id as tax_group_id , tgrp_name, ifnull(c.id_metal,'') as metal_type,

		ifnull(des.fixed_rate,0) as fixed_rate,

		if(bill_items.tag_id != null,stn_price,stn_amount) as stone_price,

		if(bill_items.tag_id != null,stn_wgt,stn_wt) as stn_wgt,

		if(bill_items.tag_id != null,othermat_amount,other_mat_price) as othermat_amount,

		if(bill_items.tag_id != null,othermat_wt,other_mat_wgt) as othermat_wt,ref_bill.sales_ref_no as ref_bill_no,ref_bill.bill_date as ref_bill_date,bill_items.total_sgst,bill_items.total_igst,bill_items.total_cgst,tax.tax_percentage, e.firstname as esti_emp_name, e.id_employee AS esti_emp_id,ref_bill.sales_ref_no,bill_items.rate_per_grm,bill_items.bill_id as ret_bill_id, bill_items.wastage_discount,bill_items.mc_discount

		FROM ret_billing as bill

		LEFT JOIN ret_bill_return_details d on d.bill_id=bill.bill_id

		LEFT JOIN ret_bill_details as bill_items ON bill_items.bill_det_id = d.ret_bill_det_id

		LEFT JOIN(SELECT b.sales_ref_no,b.bill_no,b.bill_id,date_format(b.bill_date,'%d-%m-%Y') as bill_date From ret_billing b) as ref_bill on ref_bill.bill_id=d.ret_bill_id

		LEFT JOIN (SELECT esti_id,est_item_id from ret_estimation_items where bil_detail_id is not null and purchase_status=1) as est_itms ON est_itms.est_item_id = bill_items.esti_item_id

		LEFT JOIN ret_estimation esti ON esti.estimation_id = est_itms.esti_id

		LEFT JOIN employee e on e.id_employee = esti.created_by



		LEFT JOIN ret_purity as pur ON pur.id_purity = bill_items.purity

		LEFT JOIN (SELECT bill_det_id,sum(price) as stn_price,sum(wt) as stn_wgt FROM `ret_billing_item_stones` GROUP by bill_det_id) as stn_detail ON stn_detail.bill_det_id = bill_items.bill_det_id

		LEFT JOIN (SELECT bill_det_id,sum(price) as other_mat_price,sum(price) as other_mat_wgt FROM `ret_billing_item_other_materials` GROUP by bill_det_id) as est_oth_mat ON est_oth_mat.bill_det_id = bill_items.bill_det_id

		LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(wt) as stn_wt FROM `ret_taging_stone` GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = bill_items.tag_id

		LEFT JOIN (SELECT tag_id,sum(price) as othermat_amount,sum(wt) as othermat_wt FROM `ret_taging_other_materials` GROUP by tag_id) as tag_other_mat ON tag_other_mat.tag_id = bill_items.tag_id

		LEFT JOIN ret_product_master as pro ON pro.pro_id = bill_items.product_id

		LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id

		LEFT JOIN metal mt on mt.id_metal=c.id_metal

		LEFT JOIN ret_design_master as des ON des.design_no = bill_items.design_id

		LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id

		LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,

		GROUP_CONCAT(m.tax_percentage) as tax_percentage,

		GROUP_CONCAT(i.tgi_calculation) as tgi_calculation

		FROM ret_taxgroupitems i

		LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=pro.tgrp_id

		WHERE  bill.bill_id ='" . $bill_id . "' and (bill.bill_type=7 OR bill.bill_type=3)");

		//print_r($this->db->last_query());exit;

		$result = $return_details->result_array();



		foreach ($result as $ret) {

			$ret['rtn_stone_details'] = $this->stone_details_by_bill_det_id($ret['bill_det_id']);

			$return_data['return_details'][] = $ret;
		}

		// Always filter return items (return_item_cost > 0) out of sales item_details
		// If return_details is empty, move them there as fallback; otherwise just remove from item_details
		if (!empty($return_data['item_details'])) {
			$sales_only = [];
			foreach ($return_data['item_details'] as $itm) {
				if (isset($itm['return_item_cost']) && floatval($itm['return_item_cost']) > 0) {
					// Only add to return_details if not already populated by the ret_bill_return_details query
					if (empty($return_data['return_details'])) {
						$itm['rtn_stone_details'] = isset($itm['stone_details']) ? $itm['stone_details'] : [];
						$return_data['return_details'][] = $itm;
					}
				} else {
					$sales_only[] = $itm;
				}
			}
			$return_data['item_details'] = $sales_only;
		}



		$sales_ret_trans_details = $this->db->query("SELECT d.gross_wt, d.piece, d.item_cost, d.item_total_tax, IFNULL(d.total_cgst,0) as total_cgst, IFNULL(d.total_igst,0) as total_igst, IFNULL(d.total_sgst,0) as total_sgst,
        d.net_wt, cat.name as category_name, IFNULL(pro.hsn_code,'') as hsn_code, d.rate_per_grm, tax.tax_percentage

        FROM ret_billing b

        LEFT JOIN ret_bill_return_details r ON r.bill_id=b.bill_id

        LEFT JOIN ret_bill_details d ON d.bill_det_id=r.ret_bill_det_id

        LEFT JOIN ret_taging t ON t.tag_id=d.tag_id

        LEFT JOIN ret_product_master pro ON pro.pro_id=COALESCE(t.product_id, d.product_id)

        LEFT JOIN ret_category cat ON cat.id_ret_category=pro.cat_id

        LEFT JOIN metal mt on mt.id_metal=cat.id_metal

        LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,

		(m.tax_percentage) as tax_percentage,

		(i.tgi_calculation) as tgi_calculation

		FROM ret_taxgroupitems i

		LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=mt.tgrp_id

        WHERE b.bill_id=" . $bill_id . "
        AND d.piece IS NOT NULL AND d.piece > 0

        ORDER BY r.return_id");

		//print_r($this->db->last_query());exit;

		$return_data["sales_ret_trans_details"] = $sales_ret_trans_details->result_array();





		$order_adv_details = $this->db->query("SELECT a.bill_adv_id,a.bill_id,a.advance_type,

		a.order_no,a.advance_amount,a.advance_type,a.advance_weight,a.rate_per_gram,a.store_as,a.id_customerorder,

		date_format(cad.cus_due_date,'%d-%m-%Y') as cus_due_date, ca.rate_type as rate_type, cad.rate_per_gram as rate_per_gram

		from ret_billing_advance a

		LEFT JOIN customerorderdetails cad ON cad.id_customerorder = a.id_customerorder AND cad.orderstatus != 6

		LEFT JOIN customerorder ca ON ca.id_customerorder = cad.id_customerorder

		where a.bill_id=" . $bill_id . "");

		$return_data["advance_details"] = $order_adv_details->result_array();



		$chit_details = $this->db->query("SELECT pay.total_installments,pay.scheme_type,pay.paid_installments,pay.firstPayDisc_value,pay.id_scheme,s.closing_amount,c.utilized_amt,c.scheme_account_id,

        concat(sch.code,'-',s.scheme_acc_number) as scheme_acc_number,c.closing_weight,c.savings_in_wastage,c.savings_in_making_charge,c.rate_per_gram,IFNULL(s.additional_benefits,0) as additional_benefits,c.utilized_amt,
		sch.scheme_type as new_scheme_type, ifnull(sch.flexible_sch_type, 0) as flexible_sch_type, sch.wgt_convert, IFNULL(s.closing_benefits,0) as closing_benefits

		from ret_billing_chit_utilization c

        left join scheme_account s on s.id_scheme_account=c.scheme_account_id

        LEFT JOIN scheme sch on sch.id_scheme=s.id_scheme

        LEFT JOIN (select s.total_installments,s.scheme_type,s.id_scheme,s.firstPayDisc_value,sa.id_scheme_account,IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)as paid_installments

				FROM payment p

				left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account

				left join scheme s on s.id_scheme=sa.id_scheme

				where p.payment_status=1 group by sa.id_scheme_account) as pay on pay.id_scheme_account=s.id_scheme_account

		where c.bill_id=" . $bill_id . " and s.id_scheme_account IS NOT NULL ");

		$return_data["chit_details"] = $chit_details->result_array();

		// Fetch gift issued details for chit accounts utilized in this bill
		$show_gift_in_bill = $this->get_ret_settings('show_chit_gift_in_bill');
		if ($show_gift_in_bill == 1 && sizeof($return_data['chit_details']) > 0) {
			$chit_gift_details = $this->db->query("SELECT gi.id_gift_issued, gi.gift_desc, gi.gift_amount, IFNULL(gi.quantity, 1) as quantity,
			DATE_FORMAT(gi.date_issued, '%d-%m-%Y') as date_issued, gi.id_scheme_account,
			concat(sch.code, '-', sa.scheme_acc_number) as scheme_acc_number
			FROM ret_billing_chit_utilization c
			INNER JOIN gift_issued gi ON gi.id_scheme_account = c.scheme_account_id AND gi.status = 1 AND gi.type = 1
			LEFT JOIN scheme_account sa ON sa.id_scheme_account = c.scheme_account_id
			LEFT JOIN scheme sch ON sch.id_scheme = sa.id_scheme
			WHERE c.bill_id = " . $bill_id . "");
			$return_data["chit_gift_details"] = $chit_gift_details->result_array();
		}



		$repair_order_details = $this->db->query("SELECT c.order_no,od.orderno,p.product_name,des.design_name,od.weight,od.totalitems,od.wast_percent,od.mc,od.completed_weight,od.rate,IFNULL(od.total_sgst,0) sgst,IFNULL(od.total_cgst,0) cgst,IFNULL(od.total_igst,0) igst,od.repair_tot_tax,od.repair_percent

        FROM customerorderdetails od

        LEFT JOIN customerorder c ON c.id_customerorder=od.id_customerorder

        LEFT JOIN ret_product_master p ON p.pro_id=od.id_product

        LEFT JOIN ret_design_master des ON des.design_no=od.design_no

        LEFT JOIN ret_billing b ON b.bill_id=od.bill_id

        WHERE od.bill_id=" . $bill_id . " AND od.orderstatus != 6 ");

		// print_r($this->db->last_query());exit;

		$return_data['repair_order_details'] = $repair_order_details->result_array();

/*

		$order_adj = $this->db->query("SELECT a.received_amount,a.received_weight,a.rate_per_gram,b.bill_id,a.store_as,a.advance_amount,date_format(b.bill_date,'%d-%m-%Y') as bill_date,a.advance_type,b.order_adv_ref_no



        FROM ret_billing b



        LEFT JOIN ret_billing_advance a ON a.bill_id=b.bill_id



        WHERE a.is_adavnce_adjusted=1 and b.bill_status=1 and a.adjusted_bill_id=" . $bill_id . " ");

		$return_data['order_adj'] = $order_adj->result_array(); */

		$order_adj=$this->db->query("SELECT a.received_amount,a.received_weight,a.rate_per_gram,b.bill_id,a.store_as,u.utilized_amt as advance_amount,date_format(a.advance_date,'%d-%m-%Y') as bill_date,a.advance_type,b.order_adv_ref_no

        FROM ret_billing b

		LEFT JOIN ret_advance_utilized u ON u.bill_id =  b.bill_id

        RIGHT JOIN ret_billing_advance a ON a.bill_adv_id =  u.bill_adv_id

        WHERE  b.bill_status=1 and b.bill_id=".$bill_id." ");

        $return_data['order_adj']=$order_adj->result_array();




		$voucher_details = $this->db->query("SELECT a.gift_voucher_amt,b.bill_id



        FROM ret_billing b



        LEFT JOIN ret_billing_gift_voucher_details a ON a.bill_id=b.bill_id



        WHERE b.bill_status=1 and b.bill_id=" . $bill_id . " ");



		$return_data['voucher_details'] = $voucher_details->result_array();



		if ($bill_type == 5) {

			$order_details = $this->db->query("SELECT  od.id_orderdetails,od.orderno,p.product_name,des.design_name,

        	od.weight,od.less_wt,od.net_wt,od.totalitems,od.wast_percent,od.mc,

        	subDes.sub_design_name,ifnull(concat(s.value,' ',s.name),'') as size_name,c.balance_type,

        	od.total_sgst,od.total_cgst,od.total_igst,od.rate_per_gram,od.rate,ifnull(p.hsn_code,'-') as hsn_code,tax.tax_percentage,uom.uom_short_code as pro_uom

        	FROM customerorderdetails od

        	LEFT JOIN customerorder c ON c.id_customerorder=od.id_customerorder

        	LEFT JOIN ret_product_master p ON p.pro_id=od.id_product

        	LEFT JOIN ret_category cat on cat.id_ret_category=p.cat_id

        	LEFT JOIN ret_design_master des ON des.design_no=od.design_no

        	LEFT JOIN ret_sub_design_master subDes on subDes.id_sub_design = od.id_sub_design

        	LEFT JOIN ret_billing_advance a ON a.id_customerorder=c.id_customerorder

        	LEFT JOIN ret_billing b ON b.bill_id=a.bill_id

        	LEFT JOIN ret_size s ON s.id_size = od.size

			LEFT JOIN ret_uom uom ON uom.uom_id = p.uom_id




			LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,

					m.tax_percentage as tax_percentage,

					i.tgi_calculation as tgi_calculation

					FROM ret_taxgroupitems i

					LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=p.tgrp_id

        	WHERE c.order_to=" . $bill_det['bill_cus_id'] . " AND b.bill_id=" . $bill_id . " AND b.bill_type=5 AND b.bill_status=1 AND od.orderstatus != 6 GROUP by od.id_orderdetails");



			$order_details = $order_details->result_array();



			foreach ($order_details as $ordetails) {



				$ord = $ordetails;



				$ord['stones'] = $this->get_order_stones($ordetails['id_orderdetails']);



				$return_data['order_details'][] = $ord;
			}
		}







		return $return_data;
	}



	function get_order_stones($order_no)
	{

		$order_stones = $this->db->query("SELECT os.pieces, IFNULL(os.price,0) as st_price, os.stone_id, os.stone_id, os.wt, st.stone_name, st.stone_type, st.stone_code,um.uom_short_code,os.rate_per_gram,stone_cal_type,price as amount

		FROM ret_order_item_stones os

        LEFT JOIN ret_stone st ON st.stone_id=os.stone_id

		LEFT JOIN ret_uom as um ON um.uom_id = st.uom_id

        WHERE os.order_item_id=" . $order_no);

		//print_r($this->db->last_query());exit;

		$order_stones = $order_stones->result_array();

		return $order_stones;
	}



	function get_billing_advance_details($bill_id)

	{

		// $bill_time = $bill_time != "" ? $bill_time : '';

		$adv_details = array("adv_details" => array());

		// $adv_details_query = $this->db->query("SELECT r.bill_no,date_format(r.bill_date,'%d-%m-%Y') as bill_date,adj.bill_id,adj.utilized_amt as adjuseted_amt,adj.id_issue_receipt,

		// r.amount as tot_receipt_amount,IFNULL(a.tot_utilized_amt,0) as tot_utilized_amt,(r.amount-adj.utilized_amt-IFNULL(a.tot_utilized_amt,0)) as bal_amt, a.refund_amt

		// FROM ret_advance_utilized adj

		// LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = adj.id_issue_receipt

		// LEFT JOIN (SELECT IFNULL(SUM(adj.utilized_amt),0) as tot_utilized_amt,adj.id_issue_receipt,adj.bill_id,r1.amount as refund_amt

		// FROM ret_advance_utilized adj

		// LEFT JOIN ret_billing b ON b.bill_id = adj.bill_id

		// LEFT JOIN ret_issue_receipt r1 ON r1.ref_bill_id = b.bill_id

		// WHERE b.bill_status = 1 AND adj.bill_id < " . $bill_id . " GROUP by adj.id_issue_receipt) as a ON a.id_issue_receipt = r.id_issue_receipt

		// WHERE adj.adv_utilized_type=1 and adj.bill_id = " . $bill_id . " ");




		$adv_details_query = $this->db->query("SELECT r.bill_no,date_format(r.bill_date,'%d-%m-%Y') as bill_date,adj.bill_id,adj.utilized_amt as adjuseted_amt,adj.id_issue_receipt,

        r.amount as tot_receipt_amount,IFNULL(a.tot_utilized_amt,0) as tot_utilized_amt,(r.amount-adj.utilized_amt-IFNULL(a.tot_utilized_amt,0)-IFNULL(refund.refund_amount,0)) as bal_amt, refund.refund_amount as refund_amt, adv_trns_iss.transfer_amount

        FROM ret_advance_utilized adj

        LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = adj.id_issue_receipt

        LEFT JOIN (SELECT IFNULL(SUM(adj.utilized_amt),0) as tot_utilized_amt,adj.id_issue_receipt,adj.bill_id

        FROM ret_advance_utilized adj

        LEFT JOIN ret_billing b ON b.bill_id = adj.bill_id

        WHERE b.bill_status = 1 AND adj.bill_id < '" . $bill_id . "' GROUP by adj.id_issue_receipt) as a ON a.id_issue_receipt = r.id_issue_receipt



		LEFT JOIN (select a.refund_receipt,IFNULL(SUM(a.refund_amount),0) as refund_amount

                   From ret_advance_refund a

                   LEFT JOIN ret_issue_receipt r on r.id_issue_receipt=a.id_issue_receipt

				   Where r.bill_status=1 " . ($bill_time != '' ? "and r.created_on <= '" . $bill_time . "'" : "") . "

                   group by a.refund_receipt) as refund on refund.refund_receipt=r.id_issue_receipt

		LEFT JOIN(
			
			SELECT trn.id_issue_receipt,IFNULL(SUM(trn.transfer_amount),0) as transfer_amount, IFNULL(SUM(trn.transfer_cash_amt),0) as transfer_cash_amt

			FROM ret_advance_transfer trn

			LEFT JOIN ret_issue_receipt ir ON ir.id_issue_receipt = trn.transfer_receipt_id

			WHERE ir.bill_status=1 " . ($bill_time != '' ? "and ir.created_on <= '" . $bill_time . "'" : "") . "

			GROUP BY trn.id_issue_receipt
		
		) as adv_trns_iss ON adv_trns_iss.id_issue_receipt = r.id_issue_receipt


        WHERE adj.adv_utilized_type=1 and adj.bill_id = " . $bill_id . " ");

		//print_r($this->db->last_query());exit;

		// return $adv_details_query->result_array();

		$data = $adv_details_query->result_array();

		// foreach ($data as $items) {
		// 	$pay_details['pay_details'][] = array(
		// 		'bill_no'    			=> $items['bill_no'],
		// 		'bill_date'             => $items['bill_date'],
		// 		'bill_id'               => $items['bill_id'],
		// 		'adjuseted_amt'         => $items['payment_for'],
		// 		'id_issue_receipt'      => $items['id_issue_receipt'],
		// 		'tot_receipt_amount'    => $items['tot_receipt_amount'],
		// 		'transfer_amt'      	=> $items['transfer_amount'],
		// 		'bal_amt'        		=> $items['bal_amt'],
		// 		'refund_amt'        	=> $items['refund_amt'],
				
		// 	);
		// }
	
		return $data;
	}



	function get_bill_stone_details($bill_id)

	{

		$sql = $this->db->query("SELECT s.pieces as st_pcs,IFNULL(s.price,0) as st_price,IFNULL(s.certification_price,0) as certification_price

        FROM ret_billing_item_stones s

        LEFT JOIN ret_stone st ON st.stone_id=s.stone_id

        WHERE s.bill_id=" . $bill_id . "");

		return $sql->result_array();
	}



	function get_advance_adjusted($bill_id)

	{

		$advance_adjusted = $this->db->query("SELECT IFNULL(sum(rau.utilized_amt),0) as adj_amt

			FROM ret_billing bill

			LEFT JOIN ret_advance_utilized rau on rau.bill_id=bill.bill_id

			LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = rau.id_issue_receipt

			where rau.bill_id is not null and rau.adv_utilized_type=1

			and bill.bill_status=1 and bill.bill_id=" . $bill_id . "");

		// print_r($this->db->last_query());exit;

		return $advance_adjusted->row()->adj_amt;
	}

	function get_advance_refund($bill_id)

	{

		$advance_refund_bill= $this->db->query("SELECT SUM(rar.refund_amount) as refund_amount FROM ret_billing b
INNER JOIN ret_issue_receipt r ON r.ref_bill_id = b.bill_id
INNER JOIN ret_advance_refund as rar ON rar.id_issue_receipt = r.id_issue_receipt
WHERE b.bill_id = " . $bill_id . "");

		//print_r($this->db->last_query());exit;

		return $advance_refund_bill->row()->refund_amount;
	}

	function get_order_advance($order_no)

	{

		$sql = "select IFNULL(sum(a.advance_amount),0) as advance_amount  from ret_billing_advance a where a.order_no='" . $order_no . "'";

		return $this->db->query($sql)->row('advance_amount');
	}

	function get_empty_record()

	{

		$emptyquery = $this->db->field_data('ret_billing');



		$min_pan_amt = $this->get_ret_settings('min_pan_amt');



		$is_pan_required = $this->get_ret_settings('is_pan_required');



		$spc_gift_voucher = $this->get_ret_settings('spc_gift_voucher');



		$min_wt_gram = $this->get_ret_settings('min_wt_gram');



		$free_gift_validate_days = $this->get_ret_settings('free_gift_validate_days');



		$per_gram_amt = $this->get_ret_settings('per_gram_amt');



		$is_counter_req = $this->get_ret_settings('is_counter_req');



		$is_tcs_required = $this->get_ret_settings('is_tcs_required');



		$tcs_tax_per = $this->get_ret_settings('tcs_tax_per');



		$tcs_min_bill_amt = $this->get_ret_settings('tcs_min_bill_amt');



		$repair_percentage = $this->get_ret_settings('repair_order_per');



		$is_credit_enable = $this->get_ret_settings('is_credit_enable');



		$bill_discount_type = $this->get_ret_settings('bill_discount_type');



		$weightschemecaltype = $this->get_ret_settings('weightschemecaltype');

		$max_return_amt = $this->get_ret_settings('max_return_amt');



		$weight_scheme_closure_type = $this->get_ret_settings('weight_scheme_closure_type');

		$IsMetalForBilling = $this->get_ret_settings('is_metal_for_billing');



		$gift = $this->get_gift_voucher_settings();



		$employee_settings = $this->get_employee_settings($this->session->userdata('uid'));

		// print_r($employee_settings);exit;



		$profile_settings = $this->get_profile_settings($this->session->userdata('profile'));

		$maxcash_setting = $this->get_maxcash_settings();

		$id_branch  = $this->session->userdata('id_branch');



		$company_details    = $this->getCompanyDetails($id_branch);



		$billing_employee = $this->get_ret_settings('billing_emp_select_req');



		$bill_split_min_amount = $this->get_ret_settings('bill_split_min_amount');



		$bill_split_max_amount = $this->get_ret_settings('bill_split_max_amount');

		$bill_discount_apply_on = $this->get_ret_settings('bill_discount_apply_on');

		// BIL-CLT02: Granular discount limit toggles (1=enforce, 0=bypass)
		$enable_emp_disc_limit      = $this->get_ret_settings('enable_emp_disc_limit');
		$enable_mc_va_disc_limit    = $this->get_ret_settings('enable_mc_va_disc_limit');
		$enable_disc_blw_metal_rate = $this->get_ret_settings('enable_disc_blw_metal_rate');

		$pan_verification = $this->get_ret_settings('pan_verification');

		$emptydata = array();

		foreach ($emptyquery as $field) {

			$emptydata[$field->name] = $field->default;
		}

		$emptydata['bill_date'] = date('d-m-Y H:i:s');

		$emptydata['min_pan_amt'] = $min_pan_amt;

		$emptydata['is_pan_required'] = $is_pan_required;

		$emptydata['pan_verification'] = $pan_verification;

		$emptydata['spc_gift_voucher'] = $spc_gift_voucher;



		$emptydata['cus_name'] 			  = '';



		$emptydata['is_counter_req'] = $is_counter_req;



		$emptydata['is_tcs_required'] = $is_tcs_required;



		$emptydata['tcs_tax_per'] = $tcs_tax_per;



		$emptydata['tcs_min_bill_amt'] = $tcs_min_bill_amt;



		$emptydata['financial_year'] = $this->GetFinancialYear();



		$emptydata['disc_limit_type'] = $employee_settings['disc_limit_type'];



		$emptydata['disc_limit'] = $employee_settings['disc_limit'];



		$emptydata['otp_dis_approval'] = $employee_settings['otp_dis_approval'];

		$emptydata['otp_mcva_dis_approval'] = $employee_settings['otp_mcva_dis_approval'];

		$emptydata['otp_credit_approval'] = $employee_settings['otp_credit_approval'];

		$emptydata['max_return_amt'] = $max_return_amt;



		$emptydata['repair_percentage'] = $repair_percentage;



		$emptydata['cmp_state'] = $company_details['id_state'];



		$emptydata['cmp_country'] = $company_details['id_country'];



		$emptydata['is_credit_enable'] = $is_credit_enable;



		$emptydata['bill_discount_type'] = $bill_discount_type;



		$emptydata['weight_scheme_closure_type'] = $weight_scheme_closure_type;



		$emptydata['weightschemecaltype'] = $weightschemecaltype;



		$emptydata['allow_bill_type'] = $profile_settings['allow_bill_type'];

		$emptydata['IsMetalForBilling'] = $IsMetalForBilling;



		$emptydata['billing_emp_select_req'] = $billing_employee;



		$emptydata['bill_split_min_amount'] = $bill_split_min_amount;



		$emptydata['bill_split_max_amount'] = $bill_split_max_amount;

		$emptydata['bill_discount_apply_on'] = $bill_discount_apply_on;

		// BIL-CLT02: Granular discount limit toggles — default 1 (enabled) if setting not yet in DB
		$emptydata['enable_emp_disc_limit']      = ($enable_emp_disc_limit      !== '' ? $enable_emp_disc_limit      : 1);
		$emptydata['enable_mc_va_disc_limit']    = ($enable_mc_va_disc_limit    !== '' ? $enable_mc_va_disc_limit    : 1);
		$emptydata['enable_disc_blw_metal_rate'] = ($enable_disc_blw_metal_rate !== '' ? $enable_disc_blw_metal_rate : 1);
		$emptydata['validate_cash_amt']          = $maxcash_setting['validate_cash_amt'];
		$emptydata['max_cash_amt']               = $maxcash_setting['max_cash_amt'];

		return $emptydata;
	}



	function GetFinancialYear()

	{

		$sql = $this->db->query("SELECT fin_year_code,fin_status,fin_year_name From ret_financial_year");

		return $sql->result_array();
	}

	function get_retSettings()

	{

		$min_pan_amt                = $this->get_ret_settings('min_pan_amt');

		$is_pan_required            = $this->get_ret_settings('is_pan_required');

		$on_exchange_in_billing     = $this->get_ret_settings('on_exchange_in_billing');

		$profile_settings           = $this->get_profile_settings($this->session->userdata('profile'));

		$maxcash_setting            = $this->get_maxcash_settings();

		$max_return_amt     		= $this->get_ret_settings('max_return_amt');

		$advance_transfer_otp    	 =	 $this->get_ret_settings('advance_transfer_otp');

		$bill_discount_type 		= $this->get_ret_settings('bill_discount_type');

		$id_branch  = $this->session->userdata('id_branch');

		$pan_verification = $this->get_ret_settings('pan_verification');

		$company_details    = $this->getCompanyDetails($id_branch);

		$emptydata = array();

		$emptydata['min_pan_amt']               = $min_pan_amt;

		$emptydata['is_pan_required']           = $is_pan_required;

		$emptydata['pan_verification']           = $pan_verification;

		$emptydata['on_exchange_in_billing']    = $on_exchange_in_billing;

		$emptydata['validate_cash_amt']         = $maxcash_setting['validate_cash_amt'];

		$emptydata['max_cash_amt']              = $maxcash_setting['max_cash_amt'];

		$emptydata['allow_bill_type']           = $profile_settings['allow_bill_type'];


		$emptydata['cmp_country']             = $company_details['id_country'];

		$emptydata['max_return_amt']             = $max_return_amt;

		$emptydata['advance_transfer_otp']    = $advance_transfer_otp;

		$emptydata['bill_discount_type']      = $bill_discount_type;

		$emptydata['allow_previous_rcpt_date'] = $profile_settings['allow_previous_rcpt_date'];

		return $emptydata;
	}



	public function encrypt($str)

	{

		return base64_encode($str);
	}



	function createNewCustomer($cusname, $cusmobile, $branch, $id_village, $country, $state, $city, $address1, $address2, $address3, $pincode, $mail, $cus_type, $pan_no, $aadharid, $gst_no, $title, $id_profession, $gender, $date_of_birth, $date_of_wed, $dl_no, $pp_no,$is_vip)

	{

		$customer_check_query = $this->db->query("SELECT * FROM customer WHERE mobile='" . $cusmobile . "'");

		if ($customer_check_query->num_rows() == 0) {

			if ($date_of_birth != '') {

				$d1 = date_create($date_of_birth);

				$dateofbirth = date_format($d1, "Y-m-d");
			}



			if ($date_of_wed != '') {

				$d1 = date_create($date_of_wed);

				$dateofwed = date_format($d1, "Y-m-d");
			}

			$insert_data = array(

				"firstname" => strtoupper($cusname),

				"id_branch" => $branch,

				"mobile"    => $cusmobile,

				"username"  => $cusmobile,

				"passwd"    => $this->encrypt($cusmobile),

				"id_village" => $this->isEmptySetDefault($id_village, NULL),

				"email"       => $this->isEmptySetDefault($mail, NULL),

				"cus_type"  => $this->isEmptySetDefault($cus_type, 1),

				'pan'       => $this->isEmptySetDefault($pan_no, NULL),

				'gst_number' => $this->isEmptySetDefault($gst_no, NULL),

				'aadharid'  => $this->isEmptySetDefault($aadharid, NULL),

				'date_add'  => date("Y-m-d H:i:s"),

				"title"     => $title,

				"is_vip"     => $this->isEmptySetDefault($is_vip,NULL),

				'id_profession' => $this->isEmptySetDefault($id_profession, NULL),

				'gender'        => $this->isEmptySetDefault($gender, NULL),

				'date_of_birth' => $this->isEmptySetDefault($dateofbirth, NULL),

				'date_of_wed'   => $this->isEmptySetDefault($dateofwed, NULL),

				'driving_license_no' =>$this->isEmptySetDefault($dl_no, NULL),

				'passport_no' => $this->isEmptySetDefault($pp_no, NULL),

			);

			$cus_insert_id = $this->insertData($insert_data, "customer");

			if (!empty($cus_insert_id)) {

				$insert_addressdata  = array(

					"id_country"    => !empty($country) ? $country : null,

					"id_state"      => !empty($state) ? $state : null,

					"id_city"       => !empty($city) ? $city : null,

					"company_name"  => ($cus_type == 2 ? strtoupper($cusname) : NULL),

					"id_customer"   => $cus_insert_id,

					"address1"      => $address1,

					'address2'      => $address2,

					'address3'      => $address3,

					'pincode'       => $pincode,

					'date_add'      => date("Y-m-d H:i:s")

				);
				$cus_addressinsert_id = $this->insertData($insert_addressdata, "address");
				$update_data = array(

				"id_address"   => $cus_addressinsert_id,
				);
				$cus_update_id = $this->updateData($update_data, "id_customer", $cus_insert_id, "customer");
				$insert_data["id_customer"] = $cus_insert_id;


				$insert_data["address1"] = $address1;
				$insert_data["pincode"] = $pincode;
				$insert_data["id_state"] = $state;
				$insert_data["id_country"] = $country;

				return array("success" => TRUE, "message" => "Customer details added successfully", "response" => $insert_data, $insert_addressdata);
			} else {

				return array("success" => FALSE, "message" => "Could not add customer, please try again", "response" => array());
			}
		} else {

			return array("success" => FALSE, "message" => "Given mobile number already exist", "response" => $customer_check_query->row());
		}
	}



	function updateNewCustomer($id_customer, $cusname, $cusmobile, $branch, $id_village, $country, $state, $city, $address1, $address2, $address3, $pincode, $mail, $cus_type, $pan_no, $aadharid, $gst_no, $title, $id_profession, $gender, $date_of_birth, $date_of_wed, $dl_no, $pp_no,$is_vip)

	{

		if (empty($id_customer)) {
			return array("success" => FALSE, "message" => "Invalid Customer ID provided.");
		}

		$customer_check_query = $this->db->query("SELECT * FROM customer WHERE id_customer !='" . $id_customer . "' and mobile='" . $cusmobile . "'");

		if ($customer_check_query->num_rows() == 0) {


			$data = $this->get_customer($id_customer);
			if (empty($data)) {
				return array("success" => FALSE, "message" => "Customer record not found.");
			}

            $uid=$this->session->userdata('uid');
			$dateofbirth = NULL;
			if ($date_of_birth != '' && $date_of_birth != '0000-00-00') {

				$d1 = date_create($date_of_birth);

				$dateofbirth = date_format($d1, "Y-m-d");
			}


			$dateofwed = NULL;
			if ($date_of_wed != '' && $date_of_wed != '0000-00-00') {

				$d1 = date_create($date_of_wed);

				$dateofwed = date_format($d1, "Y-m-d");
			}



			$update_data = array(

				"firstname"   => strtoupper($cusname),

				"id_branch"   => $branch,

				"id_village"  => $this->isEmptySetDefault($id_village, NULL),

				"email"       => $this->isEmptySetDefault($mail, NULL),

				"cus_type"    => $cus_type,

				'pan'         => $this->isEmptySetDefault($pan_no, NULL),

				'gst_number'  => $this->isEmptySetDefault($gst_no, NULL),

				'aadharid'    => $this->isEmptySetDefault($aadharid, NULL),

				"title"       => $title,

				'id_profession' => $this->isEmptySetDefault($id_profession, NULL),

				'gender'        => $gender,

				'is_vip'        => $is_vip,

				'date_of_birth' => ($dateofbirth != '' ? $dateofbirth : NULL),

				'date_of_wed'   => ($dateofwed != '' ? $dateofwed : NULL),

				'driving_license_no' => ($dl_no != '' ? $dl_no : NULL),

				'passport_no' => ($pp_no != '' ? $pp_no : NULL),

				'vip_up_by'         => $data['is_vip']!=$is_vip ? $uid :$data['vip_up_by'],

                'vip_up_time'       => $data['is_vip']!=$is_vip ? date("Y-m-d H:i:s") :$data['vip_up_time'],

			);



			$cus_update_id = $this->updateData($update_data, "id_customer", $id_customer, "customer");

			//print_r($this->db->last_query());exit;



			$cus_addr_delete_id = $this->deleteData("id_customer", $id_customer, "address");



			$insert_addressdata  = array(

				"id_country" => !empty($country) ? $country : NULL,

				"id_state" => !empty($state) ? $state : NULL,

				"id_city" => !empty($city) ? $city : NULL,

				"id_customer" => $id_customer,

				"address1" => $address1,

				'address2' => $address2,

				'address3' => $address3,

				'pincode' => $pincode,

				"company_name" => ($cus_type == 2 ? strtoupper($cusname."1") : NULL),

				'date_add' => date("Y-m-d H:i:s")
			);



			$cus_addressinsert_id = $this->insertData($insert_addressdata, "address");
			$update_data_add = array(

				"id_address"   => $cus_addressinsert_id,
				);
				$cus_update_id = $this->updateData($update_data_add, "id_customer", $id_customer, "customer");
			if ($cus_addressinsert_id) {

				$update_data["id_customer"] = $id_customer;

				$update_data["username"] = $cusmobile;



				$update_data["mobile"] = $cusmobile;

				$update_data["address1"] = $address1;
				$update_data["pincode"] = $pincode;
				$update_data["id_state"] = $state;
				$update_data["id_country"] = $country;



				return array("success" => TRUE, "message" => "Customer details Updated successfully", "response" => $update_data, "address_data" => $insert_addressdata);
			} else {
				return array("success" => FALSE, "message" => "Failed to update customer address.");
			}
		} else {

			return array("success" => FALSE, "message" => "Given mobile number already exist", "response" => $customer_check_query->row());
		}
		
		return array("success" => FALSE, "message" => "An unexpected error occurred during update.");
	}



	function getBranchDayClosingData($id_branch)

	{

		$sql = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing where id_branch=" . $id_branch);

		return $sql->row_array();
	}

	/**
	 * BIL-CLT05 FIX: Reset estimation items orphaned by cancelled bills.
	 * When a bill is cancelled (bill_status=2), the estimation items' purchase_status
	 * and the tag_status may remain in a "billed" state, preventing re-billing.
	 * This method resets them so all estimation items are available again.
	 */
	function resetOrphanedEstimationItems($estId, $id_branch = '')
	{
		if (empty($estId)) return;

		// Find estimation items that have purchase_status=1 but are linked to
		// cancelled bills (bill_status=2) via ret_bill_details
		$orphaned = $this->db->query("
			SELECT DISTINCT ei.est_item_id, ei.tag_id
			FROM ret_estimation_items ei
			INNER JOIN ret_estimation est ON est.estimation_id = ei.esti_id
			INNER JOIN ret_bill_details bd ON bd.esti_item_id = ei.est_item_id
			INNER JOIN ret_billing b ON b.bill_id = bd.bill_id AND b.bill_status = 2
			WHERE est.esti_no = ?
			AND ei.purchase_status = 1
			" . (!empty($id_branch) ? " AND est.id_branch = " . intval($id_branch) : "") . "
		", array($estId));

		if ($orphaned->num_rows() > 0) {
			foreach ($orphaned->result_array() as $item) {
				// Reset estimation item purchase_status back to 0 (available)
				$this->db->where('est_item_id', $item['est_item_id']);
				$this->db->update('ret_estimation_items', array('purchase_status' => 0));

				// Reset tag_status back to 0 (available) if tag exists
				if (!empty($item['tag_id'])) {
					$this->db->where('tag_id', $item['tag_id']);
					$this->db->where('tag_status !=', 0);
					$this->db->update('ret_taging', array('tag_status' => 0));
				}
			}
			log_message('info', 'BIL-CLT05: Reset ' . $orphaned->num_rows() . ' orphaned estimation items for esti_no=' . $estId);
		}
	}

	function getEstimationDetails($estId, $billType, $id_branch, $order_no, $fin_year, $metal_type, $tag_code , $old_tag_id = "")

	{

		// 1-Sales, 2-Sales&Purchase, 3-Sales,purchase&Return, 4-Purchase, 5-Order Advance, 6-Advance,7-Sales Return, 15-> Approval sales



		$dCData = $this->getBranchDayClosingData($id_branch);



		$item_details = array();

		$order_details = array();

		$return_data = array("item_details" => array(), "old_matel_details" => array(), "stone_details" => array(), "other_material_details" => array(), "voucher_details" => array(), "chit_details" => array(), 'advance_details' => array(), "order_details" => array(), "order_sale_det" => array(), "packing_details" => array());

		if ($billType == 5 && $order_no != '') { // Order Advance

			$order_query = $this->db->query("SELECT d.id_customerorder,c.order_no,d.id_product,d.design_no,d.wast_percent,d.mc,d.stn_amt,d.weight as net_wt,d.weight as gross_wt,d.totalitems,d.rate,d.id_purity,d.less_wt,

        IFNULL(prod.hsn_code,'-') as hsn_code,prod.product_name,des.design_name,p.purity as purname,

        m.tgrp_id as tax_group_id , tgrp_name, ifnull(cat.id_metal,'') as metal_type,prod.calculation_based_on,d.size,des.design_code,prod.gift_applicable,



        concat(cus.firstname,' ',cus.mobile) as cus_name,c.order_to,IFNULL(cus.pan,'') as pan_no,IFNULL(cus.gst_number,'') as gst_number,0 as est_rate_per_grm,IFNULL(cus.aadharid,'') as aadharid,

		IFNULL(cus.driving_license_no,'') as dl_no,IFNULL(cus.passport_no,'') as pp_no,cus.mobile, b.is_eda AS is_eda 



        FROM customerorder c

        LEFT JOIN customerorderdetails d on d.id_customerorder=c.id_customerorder

        LEFT JOIN ret_product_master prod on prod.pro_id=d.id_product

        LEFT JOIN ret_design_master des on des.design_no=d.design_no

        LEFT JOIN ret_category cat on cat.id_ret_category=prod.cat_id

        LEFT JOIN metal m ON m.id_metal=cat.id_metal

        LEFT JOIN ret_purity p on p.id_purity=d.id_purity

        LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = m.tgrp_id



        LEFT JOIN customer cus on cus.id_customer=c.order_to
		
		LEFT JOIN ret_billing_advance adv ON adv.id_customerorder = c.id_customerorder

		LEFT JOIN ret_billing b ON b.bill_id = adv.bill_id




        where c.id_customerorder is not null  and d.orderstatus<=4 and c.order_for=2

        " . ($order_no != '' ? " and c.order_no='" . $order_no . "'" : '') . "

        " . ($metal_type != '' ? " and m.id_metal='" . $metal_type . "'" : '') . "

        " . ($fin_year != '' ? " and c.fin_year_code='" . $fin_year . "'" : '') . "

        " . ($id_branch != '' ? " and c.order_from=" . $id_branch . "" : '') . "");

			$return_data['order_details'] = $order_query->result_array();

			//print_r($this->db->last_query());exit;

		} 
		
		/*else if (($billType == 11) && $order_no != '') {

			$order_query = $this->db->query("SELECT c.order_type,c.work_at,d.id_customerorder,c.order_no,d.id_product,d.design_no,d.wast_percent,d.mc,d.stn_amt,d.weight as net_wt,d.weight as gross_wt,d.totalitems,d.rate,d.id_purity,d.less_wt,

            IFNULL(prod.hsn_code,'-') as hsn_code,prod.product_name,IFNULL(des.design_name,'') as design_name,p.purity as purname,

            prod.tgrp_id as tax_group_id , tgrp_name, ifnull(cat.id_metal,'') as metal_type,prod.calculation_based_on,d.size,des.design_code,prod.gift_applicable,cat.id_ret_category,IFNULL(d.completed_weight,0) as completed_weight,IFNULL(d.rate,0) as amount,d.id_orderdetails,concat(cus.firstname,' ',cus.mobile) as cus_name,c.order_to,

            a.id_state as cus_state,a.id_state as cmp_state,a.id_country as cmp_country,a.id_country as cus_country,IFNULL(cus.pan,'') as pan_no,0 as est_rate_per_grm,cat.scheme_closure_benefit,IFNULL(cus.aadharid,'') as aadharid,IFNULL(cus.driving_license_no,'') as dl_no,IFNULL(cus.passport_no,'') as pp_no,cus.mobile

            FROM customerorder c

            LEFT JOIN customerorderdetails d on d.id_customerorder=c.id_customerorder

            LEFT JOIN ret_product_master prod on prod.pro_id=d.id_product

            LEFT JOIN ret_design_master des on des.design_no=d.design_no

            LEFT JOIN ret_category cat on cat.id_ret_category=prod.cat_id

            LEFT JOIN metal m ON m.id_metal=cat.id_metal

            LEFT JOIN ret_purity p on p.id_purity=d.id_purity

            LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = prod.tgrp_id

            LEFT JOIN customer cus on cus.id_customer=c.order_to

            LEFT JOIN address a on a.id_customer=cus.id_customer join company cmp

            where c.id_customerorder is not null  and d.orderstatus=4 and c.order_for=2

            " . ($order_no != '' ? " and c.order_no='" . $order_no . "'" : '') . "

            " . ($id_branch != '' ? ($billType == 11 ? " and d.current_branch=" . $id_branch . "" : " and c.order_from=" . $id_branch . "") : '') . "

            GROUP BY d.id_orderdetails");

			//echo $this->db->last_query();exit;

			$return_data['order_details'] = $order_query->result_array();


			$items_query = $this->db->query("SELECT tag.tag_code,ifnull(tag.tag_id,'') as tag_id,ifnull(tag.product_id, '') as product_id,ifnull(tag.design_id, '') as design_id,

            ifnull(pro.hsn_code,'') as hsn_code,tag.purity as purid,IFNULL(tag.size,'') as size,tag.piece as piece,IFNULL(tag.less_wt,0) as less_wt,

            IFNULL(tag.gross_wt,0) as gross_wt,IFNULL(tag.net_wt,0) as net_wt,tag.sales_value as item_cost,ifnull(product_short_code, '-') as product_short_code,

            ifnull(product_name, '-') as product_name,ifnull(design_code, '-') as design_code,

            ifnull(design_name, '') as design_name, pur.purity as purname,

            mt.tgrp_id as tax_group_id , tgrp_name, ifnull(c.id_metal,'') as metal_type,

            IFNULL(tag_stn_detail.stn_amount,0) as stone_price,IFNULL(tag_stn_detail.certification_cost,0) as certification_cost,r.rate_field,

            ifnull(rec.charge_value, 0) as charge_value, c.id_ret_category as catid ,tag_other_metal.tag_other_itm_amount,

            IFNULL(tag.retail_max_wastage_percent,0) as wastage_percent , IFNULL(tag.tag_mc_value,'') as mc_value, tag.tag_mc_type as mc_type,tag.purity as purid,tag.calculation_based_on,IFNULL(tag.net_wt,0) as tag_net_wt,IFNULL(sub_des.sub_design_name,0) as sub_design_name,0 as item_type,IFNULL(tag.id_section,'') as id_section,IFNULL(tag.id_orderdetails,'') as id_orderdetails,tag.id_sub_design


			FROM customerorder as cus_order

			LEFT JOIN customerorderdetails as ord ON ord.id_customerorder = cus_order.id_customerorder

            LEFT JOIN ret_taging tag ON tag.id_orderdetails = ord.id_orderdetails

            LEFT JOIN ret_product_master as pro ON pro.pro_id = tag.product_id

            LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id

            LEFT JOIN metal mt on mt.id_metal=c.id_metal

            LEFT JOIN ret_design_master as des ON des.design_no = tag.design_id

			LEFT JOIN ret_sub_design_master as sub_des ON sub_des.id_sub_design = tag.id_sub_design

            LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity

            LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = mt.tgrp_id

            LEFT JOIN ret_metal_purity_rate r on r.id_metal=c.id_metal and r.id_purity=tag.purity

            LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(certification_cost) as certification_cost,sum(wt) as stn_wt FROM `ret_taging_stone` GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = tag.tag_id

            LEFT JOIN (SELECT tag_id,sum(price) as othermat_amount,sum(wt) as othermat_wt FROM `ret_taging_other_materials` GROUP by tag_id) as tag_other_mat ON tag_other_mat.tag_id = tag.tag_id

            LEFT JOIN (SELECT tag_id, SUM(IFNULL(t.charge_value,0)) AS charge_value FROM ret_taging_charges t GROUP BY t.tag_id) AS rec ON rec.tag_id = tag.tag_id

			LEFT JOIN (SELECT tag_other_itm_tag_id,sum(tag_other_itm_amount) as tag_other_itm_amount FROM `ret_tag_other_metals` GROUP by tag_other_itm_tag_id) as tag_other_metal ON tag_other_metal.tag_other_itm_tag_id =tag.tag_id


			WHERE " . (!empty($id_branch) ? "tag.current_branch=" . $id_branch . "" : '') . "

					" . ($order_no != '' ? " and cus_order.order_no='" . $order_no . "'" : '') . "

    		AND (tag.tag_status =0)

    		 ");

			//print_r($this->db->last_query());exit; AND ifnull(tag.tag_type, 0) != 1

			$item_details = $items_query->result_array();





			$repair_other_metal_details = $this->db->query("SELECT d.detail_id,r.rate_field,d.id_orderdetails,p.pro_id as product_id,p.product_name,d.id_purity as purid,

					IFNULL(des.design_name,'') as design_name,IFNULL(sub_des.sub_design_name,0) as sub_design_name,d.id_sub_design,d.id_design as design_id,

            	    d.gross_wt,IFNULL(d.less_wt,0) as less_wt,IFNULL(d.net_wt,0) as net_wt,d.wast_percent as wastage_percent,d.mc_value,d.mc_type,

                    pur.purity as purname,mt.tgrp_id as tax_group_id,'' as esti_item_id,'2' as calculation_based_on,'1' as piece,tgrp_name,d.item_type as item_type

                    FROM customer_order_other_details d

                    LEFT JOIN customerorderdetails ord ON ord.id_orderdetails = d.id_orderdetails

                    LEFT JOIN customerorder c ON c.id_customerorder = ord.id_customerorder

                    LEFT JOIN ret_product_master p ON p.pro_id = d.id_product

					LEFT JOIN ret_design_master des on des.design_no=d.id_design

					LEFT JOIN ret_sub_design_master as sub_des ON sub_des.id_sub_design = d.id_sub_design

                    LEFT JOIN ret_category cat ON cat.id_ret_category = p.cat_id

                    LEFT JOIN ret_purity pur ON pur.id_purity = d.id_purity

                    LEFT JOIN metal mt ON mt.id_metal = cat.id_metal

		            LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = p.tgrp_id



                    LEFT JOIN ret_metal_purity_rate r on r.id_metal=cat.id_metal and r.id_purity=d.id_purity

                    WHERE ord.orderstatus=4

                    " . ($order_no != '' ? " and c.order_no='" . $order_no . "'" : '') . "

                    " . ($id_branch != '' ? ($billType == 11 ? " and ord.current_branch=" . $id_branch . "" : " and c.order_from=" . $id_branch . "") : '') . "

                    ");

			//echo $this->db->last_query();exit;

			$item_details = $return_data['order_details'][0]['work_at'] == 2 ? $item_details : $repair_other_metal_details->result_array();
		}*/ 

				
		else if (($billType == 11) && $order_no != '') {



			$order_query = $this->db->query("SELECT c.order_type,c.work_at,d.id_customerorder,c.order_no,d.id_product,d.design_no,d.wast_percent,d.mc,d.stn_amt,d.weight as net_wt,d.weight as gross_wt,d.totalitems,d.rate,d.id_purity,d.less_wt,



            IFNULL(prod.hsn_code,'-') as hsn_code,prod.product_name,IFNULL(des.design_name,'') as design_name,p.purity as purname,



            prod.tgrp_id as tax_group_id , tgrp_name, ifnull(cat.id_metal,'') as metal_type,prod.calculation_based_on,d.size,des.design_code,prod.gift_applicable,cat.id_ret_category,IFNULL(d.completed_weight,0) as completed_weight,IFNULL(d.rate,0) as amount,d.id_orderdetails,concat(cus.firstname,' ',cus.mobile) as cus_name,c.order_to,



            a.id_state as cus_state,a.id_state as cmp_state,a.id_country as cmp_country,a.id_country as cus_country,IFNULL(cus.pan,'') as pan_no,0 as est_rate_per_grm,cat.scheme_closure_benefit,IFNULL(cus.aadharid,'') as aadharid,IFNULL(cus.driving_license_no,'') as dl_no,IFNULL(cus.passport_no,'') as pp_no,cus.mobile



            FROM customerorder c



            LEFT JOIN customerorderdetails d on d.id_customerorder=c.id_customerorder



            LEFT JOIN ret_product_master prod on prod.pro_id=d.id_product



            LEFT JOIN ret_design_master des on des.design_no=d.design_no



            LEFT JOIN ret_category cat on cat.id_ret_category=prod.cat_id



            LEFT JOIN metal m ON m.id_metal=cat.id_metal



            LEFT JOIN ret_purity p on p.id_purity=d.id_purity



            LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = prod.tgrp_id



            LEFT JOIN customer cus on cus.id_customer=c.order_to



            LEFT JOIN address a on a.id_customer=cus.id_customer join company cmp



            where c.id_customerorder is not null  and d.orderstatus=4 and c.order_for=2



            " . ($order_no != '' ? " and c.order_no='" . $order_no . "'" : '') . "



            " . ($id_branch != '' ? ($billType == 11 ? " and d.current_branch=" . $id_branch . "" : " and c.order_from=" . $id_branch . "") : '') . "



            GROUP BY d.id_orderdetails");



			//echo $this->db->last_query();exit;



			$return_data['order_details'] = $order_query->result_array();





			$items_query = $this->db->query("SELECT tag.tag_code,ifnull(tag.tag_id,'') as tag_id,ifnull(tag.product_id, '') as product_id,ifnull(tag.design_id, '') as design_id,



            ifnull(pro.hsn_code,'') as hsn_code,tag.purity as purid,IFNULL(tag.size,'') as size,tag.piece as piece,IFNULL(tag.less_wt,0) as less_wt,



            IFNULL(tag.gross_wt,0) as gross_wt,IFNULL(tag.net_wt,0) as net_wt,tag.sales_value as item_cost,ifnull(product_short_code, '-') as product_short_code,



            ifnull(product_name, '-') as product_name,ifnull(design_code, '-') as design_code,



            ifnull(design_name, '') as design_name, pur.purity as purname,



            mt.tgrp_id as tax_group_id , tgrp_name, ifnull(c.id_metal,'') as metal_type,



            IFNULL(tag_stn_detail.stn_amount,0) as stone_price,IFNULL(tag_stn_detail.certification_cost,0) as certification_cost,r.rate_field,



            ifnull(rec.charge_value, 0) as charge_value, c.id_ret_category as catid ,tag_other_metal.tag_other_itm_amount,



            IFNULL(tag.retail_max_wastage_percent,0) as wastage_percent , IFNULL(tag.tag_mc_value,'') as mc_value, tag.tag_mc_type as mc_type,tag.purity as purid,tag.calculation_based_on,IFNULL(tag.net_wt,0) as tag_net_wt,IFNULL(sub_des.sub_design_name,0) as sub_design_name,0 as item_type,IFNULL(tag.id_section,'') as id_section,IFNULL(tag.id_orderdetails,'') as id_orderdetails,tag.id_sub_design





			FROM customerorder as cus_order



			LEFT JOIN customerorderdetails as ord ON ord.id_customerorder = cus_order.id_customerorder



            LEFT JOIN ret_taging tag ON tag.id_orderdetails = ord.id_orderdetails



            LEFT JOIN ret_product_master as pro ON pro.pro_id = tag.product_id



            LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id



            LEFT JOIN metal mt on mt.id_metal=c.id_metal



            LEFT JOIN ret_design_master as des ON des.design_no = tag.design_id



			LEFT JOIN ret_sub_design_master as sub_des ON sub_des.id_sub_design = tag.id_sub_design



            LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity



            LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = mt.tgrp_id



            LEFT JOIN ret_metal_purity_rate r on r.id_metal=c.id_metal and r.id_purity=tag.purity



            LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(certification_cost) as certification_cost,sum(wt) as stn_wt FROM `ret_taging_stone` GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = tag.tag_id



            LEFT JOIN (SELECT tag_id,sum(price) as othermat_amount,sum(wt) as othermat_wt FROM `ret_taging_other_materials` GROUP by tag_id) as tag_other_mat ON tag_other_mat.tag_id = tag.tag_id



            LEFT JOIN (SELECT tag_id, SUM(IFNULL(t.charge_value,0)) AS charge_value FROM ret_taging_charges t GROUP BY t.tag_id) AS rec ON rec.tag_id = tag.tag_id



			LEFT JOIN (SELECT tag_other_itm_tag_id,sum(tag_other_itm_amount) as tag_other_itm_amount FROM `ret_tag_other_metals` GROUP by tag_other_itm_tag_id) as tag_other_metal ON tag_other_metal.tag_other_itm_tag_id =tag.tag_id





			WHERE " . (!empty($id_branch) ? "tag.current_branch=" . $id_branch . "" : '') . "



					" . ($order_no != '' ? " and cus_order.order_no='" . $order_no . "'" : '') . "



    		AND (tag.tag_status =0)



    		 ");



			//print_r($this->db->last_query());exit; AND ifnull(tag.tag_type, 0) != 1



			$item_details = $items_query->result_array();











			$repair_other_metal_details = $this->db->query("SELECT d.detail_id,r.rate_field,d.id_orderdetails,p.pro_id as product_id,p.product_name,d.id_purity as purid,



					IFNULL(des.design_name,'') as design_name,IFNULL(sub_des.sub_design_name,0) as sub_design_name,d.id_sub_design,d.id_design as design_id,



            	    d.gross_wt,IFNULL(d.less_wt,0) as less_wt,IFNULL(d.net_wt,0) as net_wt,d.wast_percent as wastage_percent,d.mc_value,d.mc_type,d.id_section,



                    pur.purity as purname,mt.tgrp_id as tax_group_id,'' as esti_item_id,'2' as calculation_based_on,'1' as piece,tgrp_name,d.item_type as item_type,IF(d.item_type=1,1,0) as is_non_tag,d.detail_id as id_orderdetails



                    FROM customer_order_other_details d



                    LEFT JOIN customerorderdetails ord ON ord.id_orderdetails = d.id_orderdetails



                    LEFT JOIN customerorder c ON c.id_customerorder = ord.id_customerorder



                    LEFT JOIN ret_product_master p ON p.pro_id = d.id_product



					LEFT JOIN ret_design_master des on des.design_no=d.id_design



					LEFT JOIN ret_sub_design_master as sub_des ON sub_des.id_sub_design = d.id_sub_design



                    LEFT JOIN ret_category cat ON cat.id_ret_category = p.cat_id



                    LEFT JOIN ret_purity pur ON pur.id_purity = d.id_purity



                    LEFT JOIN metal mt ON mt.id_metal = cat.id_metal



		            LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = p.tgrp_id







                    LEFT JOIN ret_metal_purity_rate r on r.id_metal=cat.id_metal and r.id_purity=d.id_purity



                    WHERE ord.orderstatus=4



                    " . ($order_no != '' ? " and c.order_no='" . $order_no . "'" : '') . "



                    " . ($id_branch != '' ? ($billType == 11 ? " and ord.current_branch=" . $id_branch . "" : " and c.order_from=" . $id_branch . "") : '') . "



                    ");



			//echo $this->db->last_query();exit;



			$item_details = $return_data['order_details'][0]['work_at'] == 2 ? $item_details : $repair_other_metal_details->result_array();

		}
			
		else if ($billType == 9 && $estId != '') { //Order Delivery



		$items_query = $this->db->query("SELECT est_itms.est_item_id, esti_id, item_type, est_itms.purchase_status,est.esti_no,

		ifnull(est_itms.product_id, '') as product_id, ifnull(est_itms.tag_id, '') as tag_id,

		ifnull(est_itms.design_id, '') as design_id, ifnull(pro.hsn_code,'') as hsn_code,

		est_itms.purity as purid, IFNULL(est_itms.size,'') as size, ifnull(est_itms.uom,'') as uom,IFNULL(est_itms.piece,'') as piece,

		ifnull(est_itms.less_wt,'') as less_wt,IFNULL(est_itms.net_wt,0) as net_wt,IFNULL(est_itms.gross_wt,0) as gross_wt,

		est_itms.calculation_based_on, IFNULL(est_itms.wastage_percent,0) as wastage_percent , IFNULL(est_itms.mc_value,'') as mc_value, est_itms.mc_type,

		tag.sell_rate as item_cost, ifnull(product_short_code, '-') as product_short_code,

		ifnull(product_name, '-') as product_name, est_itms.is_partial,est_itms.discount,

		ifnull(design_code, '-') as design_code,

		ifnull(design_name, '') as design_name, pur.purity as purname,

		pro.tgrp_id as tax_group_id , tgrp_name, ifnull(c.id_metal,'') as metal_type,

		ifnull(des.fixed_rate,0) as fixed_rate,

		if(est_itms.id_orderdetails!='',ord.stn_amt,if(est_itms.tag_id != '',tag_stn_detail.stn_amount,stn_detail.stn_price)) as stone_price,

		IFNULL(tag_stn_detail.certification_cost,0) as certification_cost,

		if(est_itms.tag_id != null,stn_wgt,stn_wt) as stn_wgt,

		if(est_itms.tag_id != null,othermat_amount,other_mat_price) as othermat_amount,

		if(est_itms.tag_id != null,othermat_wt,other_mat_wgt) as othermat_wt,est_itms.is_non_tag,concat(cus.firstname,' ',cus.mobile) as cus_name,cus.id_customer,

		v.village_name,if(cus.is_vip=1,'Yes','No') as vip,cus.mobile,

		(select count(sa.id_scheme_account) from scheme_account sa left join customer cust on cust.id_customer=sa.id_customer) as accounts,

		pro.min_wastage,pro.max_wastage,pro.stock_type,nt.no_of_piece as available_pieces,nt.gross_wt as available_gross_wt,IFNULL(est_itms.orderno,'') as order_no,



		ifnull(est_itms.id_orderdetails,'') as id_orderdetails,IFNULL(ord.id_customerorder,'') as id_customerorder,pro.gift_applicable,r.rate_field, rec.charge_value,IFNULL(cus.pan,'') as pan_no,

		IFNULL(est_itms.id_collecion_maping_det,'') as id_collecion_maping_det,est.esti_for, est_itms.id_sub_design,ifnull(tag.tag_code,'') as tag_code,est_itms.est_rate_per_grm,IFNULL(est.discount,0) as est_discount,

		c.scheme_closure_benefit,IFNULL(tag.net_wt,0) as tag_net_wt,IFNULL(cus.aadharid,'') as aadharid,IFNULL(est_itms.id_section,'') as id_section,IFNULL(cus.driving_license_no,'') as dl_no,IFNULL(cus.passport_no,'') as pp_no,
		ifnull(tag.quality_id,'') as quality_id,pro.stone_type,ifnull(tag.uom_gross_wt,'') as uom_id,ifnull(tag.stone_calculation_based_on,'') as stone_calculation_based_on,est_other_metal.tag_other_itm_amount, addr.id_state, addr.id_country,est.is_eda, est_itms.pure_rate



		FROM ret_estimation as est

		LEFT JOIN ret_estimation_items as est_itms ON est_itms.esti_id = est.estimation_id



		left join ret_taging tag on tag.tag_id=est_itms.tag_id



		LEFT JOIN customerorderdetails as ord ON ord.id_orderdetails=est_itms.id_orderdetails

		LEFT JOIN (SELECT est_item_id,sum(price) as stn_price,sum(wt) as stn_wgt FROM `ret_estimation_item_stones` GROUP by est_item_id) as stn_detail ON stn_detail.est_item_id = est_itms.est_item_id

		LEFT JOIN (SELECT est_item_id,sum(price) as other_mat_price,sum(price) as other_mat_wgt FROM `ret_estimation_item_other_materials` GROUP by est_item_id) as est_oth_mat ON est_oth_mat.est_item_id = est_itms.est_item_id

		LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(certification_cost) as certification_cost,sum(wt) as stn_wt FROM `ret_taging_stone` GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = est_itms.tag_id

		LEFT JOIN (SELECT tag_id,sum(price) as othermat_amount,sum(wt) as othermat_wt FROM `ret_taging_other_materials` GROUP by tag_id) as tag_other_mat ON tag_other_mat.tag_id = est_itms.tag_id

		LEFT JOIN ret_product_master as pro ON pro.pro_id = est_itms.product_id

		LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id

		LEFT JOIN metal mt on mt.id_metal=c.id_metal

		LEFT JOIN ret_design_master as des ON des.design_no = est_itms.design_id

		LEFT JOIN ret_purity as pur ON pur.id_purity = est_itms.purity

		LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id

		LEFT JOIN customer cus on cus.id_customer=est.cus_id

		LEFT JOIN village v on v.id_village=cus.id_village

		LEFT JOIN address AS addr ON addr.id_customer = cus.id_customer

		LEFT JOIN ret_metal_purity_rate r on r.id_metal=c.id_metal and r.id_purity=est_itms.purity

		LEFT JOIN (SELECT est_item_id, SUM(IFNULL(amount,0)) AS charge_value FROM ret_estimation_other_charges GROUP BY est_item_id) AS rec ON rec.est_item_id = est_itms.est_item_id

		LEFT JOIN (SELECT est_item_id,sum(tag_other_itm_amount) as tag_other_itm_amount FROM `ret_est_other_metals` GROUP by est_item_id) as est_other_metal ON est_other_metal.est_item_id

		LEFT JOIN ret_nontag_item nt on nt.product=est_itms.product_id " . ($id_branch != '' ? " and nt.branch=" . $id_branch . "" : '') . "

		WHERE " . (!empty($id_branch) ? "est.id_branch=" . $id_branch . "" : '') . "

		" . ($estId != '' ? " and est.esti_no ='" . $estId . "' " : '') . "

		" . ($metal_type != '' ? " and mt.id_metal='" . $metal_type . "'" : '') . "

		AND est_itms.est_item_id IS NOT NULL and est_itms.purchase_status=0 AND (tag.tag_status =0 OR tag.tag_status IS NULL OR tag.is_partial=1)

		AND date(est.estimation_datetime)='" . $dCData['entry_date'] . "'

		GROUP by  est_itms.est_item_id

		order by est_itms.esti_id DESC");

			//print_r($this->db->last_query());exit;

			$item_details = $items_query->result_array();
		} else if (($billType == 1 || $billType == 2 || $billType == 3) && $estId != '') {

			$items_query = $this->db->query("SELECT est_itms.est_item_id, esti_id, item_type, est_itms.purchase_status,est.esti_no,

		ifnull(est_itms.product_id, '') as product_id, ifnull(est_itms.tag_id, '') as tag_id,

		ifnull(est_itms.design_id, '') as design_id, ifnull(pro.hsn_code,'') as hsn_code,

		est_itms.purity as purid, IFNULL(est_itms.size,'') as size, ifnull(est_itms.uom,'') as uom,IFNULL(est_itms.piece,'') as piece,

		ifnull(est_itms.less_wt,'') as less_wt,IFNULL(est_itms.net_wt,0) as net_wt,IFNULL(est_itms.gross_wt,0) as gross_wt,

		est_itms.calculation_based_on, IFNULL(est_itms.wastage_percent,0) as wastage_percent , IFNULL(est_itms.mc_value,'') as mc_value, est_itms.mc_type,

		ifnull(product_short_code, '-') as product_short_code,



		if(est_itms.item_type = 0, tag.sell_rate, if(est_itms.calculation_based_on=4,est_itms.est_rate_per_grm,(est_itms.item_cost - est_itms.item_total_tax))) as item_cost,

		ifnull(product_name, '-') as product_name, est_itms.is_partial,est_itms.discount,

		ifnull(design_code, '-') as design_code,

		ifnull(design_name, '') as design_name, pur.purity as purname,

		pro.tgrp_id as tax_group_id , tgrp_name, ifnull(c.id_metal,'') as metal_type,

		ifnull(des.fixed_rate,0) as fixed_rate,

		if(est_itms.id_orderdetails!='',ord.stn_amt,if(est_itms.tag_id != '',tag_stn_detail.stn_amount,stn_detail.stn_price)) as stone_price,

		IFNULL(tag_stn_detail.certification_cost,0) as certification_cost,

		if(est_itms.tag_id != null,stn_wgt,stn_wt) as stn_wgt,cus.mobile,

		if(est_itms.tag_id != null,othermat_amount,other_mat_price) as othermat_amount,

		if(est_itms.tag_id != null,othermat_wt,other_mat_wgt) as othermat_wt,est_itms.is_non_tag,concat(cus.firstname,' ',cus.mobile) as cus_name,cus.id_customer,

		v.village_name,if(cus.is_vip=1,'Yes','No') as vip,cus.mobile,

		(select count(sa.id_scheme_account) from scheme_account sa left join customer cust on cust.id_customer=sa.id_customer) as accounts,

		pro.min_wastage,pro.max_wastage,pro.stock_type,nt.no_of_piece as available_pieces,nt.gross_wt as available_gross_wt,IFNULL(est_itms.orderno,'') as order_no,



		ifnull(est_itms.id_orderdetails,'') as id_orderdetails,IFNULL(ord.id_customerorder,'') as id_customerorder,pro.gift_applicable,r.rate_field,

		ifnull(rec.charge_value, 0) as charge_value,IFNULL(cus.pan,'') as pan_no,IFNULL(cus.gst_number,'') as gst_number,IFNULL(est_itms.id_collecion_maping_det,'') as id_collecion_maping_det,est.esti_for, est_itms.id_sub_design,



		ifnull(tag.tag_code,'') as tag_code,est_itms.est_rate_per_grm,IFNULL(est.discount,0) as est_discount,c.scheme_closure_benefit,IFNULL(tag.net_wt,0) as tag_net_wt,IFNULL(est_itms.esti_purchase_cost,0) AS esti_purchase_cost, IFNULL(tag.tag_lot_id, 0) AS tag_lot_id,IFNULL(cus.aadharid,'') as aadharid,IFNULL(est_itms.id_section,'') as id_section,IFNULL(cus.driving_license_no,'') as dl_no,IFNULL(cus.passport_no,'') as pp_no,ifnull(tag.quality_id,'') as quality_id,pro.stone_type,ifnull(tag.uom_gross_wt,'') as uom_id,ifnull(tag.stone_calculation_based_on,'') as stone_calculation_based_on,IFNULL(sub_des.sub_design_name,0) as sub_design_name,

		IFNULL(tag.hu_id,IFNULL(tag.hu_id2,'')) as huid,tag.hu_id,tag.hu_id2,DATEDIFF(date(now()),date(tag_datetime)) AS age,date_format(tag.tag_datetime,'%d-%m-%Y') as tag_date,est_other_metal.tag_other_itm_amount,

        IFNULL(puritm.item_wastage,IFNULL(tag.lot_wastage_percentage,0)) as purchase_va,IF(puritm.mc_type is null,if(tag.lot_mc_type=1,'Per Gram','Per Pcs'),if(puritm.mc_type=1,'Per Gram','Per Pcs')) as pur_mc_typ,IFNULL(puritm.mc_type,IFNULL(tag.lot_mc_type,0)) as purchase_mc_type,IFNULL(puritm.mc_value,IFNULL(tag.lot_making_charge,0)) as purchase_mc,IFNULL(puritm.purchase_touch,IFNULL(tag.lot_purchase_touch,0)) as purchase_touch,IFNULL(puritm.fix_rate_per_grm,IFNULL(tag.lot_rate,0)) as purchase_rate,IFNULL(puritm.rate_calc_type,IFNULL(tag.lot_rate_calc_type,0)) as purchase_rate_calc_type,IFNULL(puritm.pure_wt_calc_type,IFNULL(tag.lot_calc_type,0)) as purchase_calc_type,IFNULL((CONCAT(emp.firstname,' ',IFNULL(emp.lastname,''))),'') as item_emp_name,IFNULL(est_itms.item_emp_id,'') as item_emp_id, sizes.name as size_name, sizes.value as size_value, tag.tag_purchase_cost as pur_cost,est.is_eda, est_itms.pure_rate,

		IFNULL(ad.id_country,'') as id_country,IFNULL(ad.id_state,'') as id_state

		FROM ret_estimation as est

		LEFT JOIN ret_estimation_items as est_itms ON est_itms.esti_id = est.estimation_id

		left join ret_taging tag on tag.tag_id=est_itms.tag_id

		LEFT JOIN ret_purchase_order_items as puritm ON  puritm.po_item_pro_id = tag.product_id AND puritm.po_item_des_id = tag.design_id AND puritm.po_item_sub_des_id = tag.id_sub_design and puritm.lot_no = tag.tag_lot_id

		LEFT JOIN customerorderdetails as ord ON ord.id_orderdetails=est_itms.id_orderdetails

		LEFT JOIN (SELECT est_item_id,sum(price) as stn_price,sum(wt) as stn_wgt FROM `ret_estimation_item_stones` GROUP by est_item_id) as stn_detail ON stn_detail.est_item_id = est_itms.est_item_id

		LEFT JOIN (SELECT est_item_id,sum(price) as other_mat_price,sum(price) as other_mat_wgt FROM `ret_estimation_item_other_materials` GROUP by est_item_id) as est_oth_mat ON est_oth_mat.est_item_id = est_itms.est_item_id

		LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(certification_cost) as certification_cost,sum(wt) as stn_wt FROM `ret_taging_stone` GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = est_itms.tag_id

		LEFT JOIN (SELECT tag_id,sum(price) as othermat_amount,sum(wt) as othermat_wt FROM `ret_taging_other_materials` GROUP by tag_id) as tag_other_mat ON tag_other_mat.tag_id = est_itms.tag_id

		LEFT JOIN ret_product_master as pro ON pro.pro_id = est_itms.product_id

		LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id

		LEFT JOIN metal mt on mt.id_metal=c.id_metal

		LEFT JOIN ret_design_master as des ON des.design_no = est_itms.design_id

		LEFT JOIN ret_sub_design_master as sub_des ON sub_des.id_sub_design = est_itms.id_sub_design

		LEFT JOIN ret_purity as pur ON pur.id_purity = est_itms.purity

		LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id

		LEFT JOIN customer cus on cus.id_customer=est.cus_id

		LEFT JOIN address ad on ad.id_customer=cus.id_customer

		LEFT JOIN village v on v.id_village=cus.id_village

		LEFT JOIN employee emp on emp.id_employee=est_itms.item_emp_id

		LEFT JOIN ret_size as sizes on sizes.id_size = est_itms.size




		LEFT JOIN ret_metal_purity_rate r on r.id_metal=c.id_metal and r.id_purity=est_itms.purity

		LEFT JOIN (SELECT est_item_id, SUM(IFNULL(amount,0)) AS charge_value FROM ret_estimation_other_charges GROUP BY est_item_id) AS rec ON rec.est_item_id = est_itms.est_item_id

		LEFT JOIN (SELECT est_item_id,sum(tag_other_itm_amount) as tag_other_itm_amount FROM `ret_est_other_metals` GROUP by est_item_id) as est_other_metal ON est_other_metal.est_item_id

		LEFT JOIN ret_nontag_item nt on nt.product=est_itms.product_id " . ($id_branch != '' ? " and nt.branch=" . $id_branch . "" : '') . "

		WHERE " . (!empty($id_branch) ? "est.id_branch=" . $id_branch . "" : '') . "

		" . ($estId != '' ? " and est.esti_no ='" . $estId . "' " : '') . "

		" . ($metal_type != '' ? " and mt.id_metal=" . $metal_type . "" : '') . "

		AND est_itms.est_item_id IS NOT NULL and tag.id_orderdetails IS NULL and est_itms.purchase_status=0 AND (tag.tag_status =0 OR tag.tag_status IS NULL OR tag.is_partial=1)

		AND date(est.estimation_datetime)='" . $dCData['entry_date'] . "'

		GROUP by  est_itms.est_item_id

		order by est_itms.esti_id DESC");

			//echo $this->db->_error_message(); exit;

			//print_r($this->db->last_query());exit;

			$item_details = $items_query->result_array();
		} else if ($billType == 15 && $estId != '') {

			$items_query = $this->db->query("SELECT est_itms.est_item_id, esti_id, item_type, est_itms.purchase_status,est.esti_no,

    		ifnull(est_itms.product_id, '') as product_id, ifnull(est_itms.tag_id, '') as tag_id,

    		ifnull(est_itms.design_id, '') as design_id, ifnull(pro.hsn_code,'') as hsn_code,

    		est_itms.purity as purid, IFNULL(est_itms.size,'') as size, ifnull(est_itms.uom,'') as uom,IFNULL(est_itms.piece,'') as piece,

    		ifnull(est_itms.less_wt,'') as less_wt,IFNULL(est_itms.net_wt,0) as net_wt,IFNULL(est_itms.gross_wt,0) as gross_wt,

    		est_itms.calculation_based_on, IFNULL(est_itms.wastage_percent,0) as wastage_percent , IFNULL(est_itms.mc_value,'') as mc_value, est_itms.mc_type,

    		ifnull(product_short_code, '-') as product_short_code,



    		if(est_itms.item_type = 0, tag.sell_rate, (est_itms.item_cost - est_itms.item_total_tax)) as item_cost,

    		ifnull(product_name, '-') as product_name, est_itms.is_partial,est_itms.discount,

    		ifnull(design_code, '-') as design_code,

    		ifnull(design_name, '') as design_name, pur.purity as purname,

    		pro.tgrp_id as tax_group_id , tgrp_name, ifnull(c.id_metal,'') as metal_type,

    		ifnull(des.fixed_rate,0) as fixed_rate,

    		if(est_itms.id_orderdetails!='',ord.stn_amt,if(est_itms.tag_id != '',tag_stn_detail.stn_amount,stn_detail.stn_price)) as stone_price,

    		IFNULL(tag_stn_detail.certification_cost,0) as certification_cost,

    		if(est_itms.tag_id != null,stn_wgt,stn_wt) as stn_wgt,

    		if(est_itms.tag_id != null,othermat_amount,other_mat_price) as othermat_amount,

    		if(est_itms.tag_id != null,othermat_wt,other_mat_wgt) as othermat_wt,est_itms.is_non_tag,concat(cus.firstname,' ',cus.mobile) as cus_name,cus.id_customer,

    		v.village_name,if(cus.is_vip=1,'Yes','No') as vip,cus.mobile,

    		(select count(sa.id_scheme_account) from scheme_account sa left join customer cust on cust.id_customer=sa.id_customer) as accounts,

    		pro.min_wastage,pro.max_wastage,pro.stock_type,nt.no_of_piece as available_pieces,nt.gross_wt as available_gross_wt,IFNULL(est_itms.orderno,'') as order_no,



    		ifnull(est_itms.id_orderdetails,'') as id_orderdetails,IFNULL(ord.id_customerorder,'') as id_customerorder,pro.gift_applicable,r.rate_field,est_other_metal.tag_other_itm_amount,

    		ifnull(rec.charge_value, 0) as charge_value,IFNULL(cus.pan,'') as pan_no,IFNULL(cus.gst_number,'') as gst_number,IFNULL(est_itms.id_collecion_maping_det,'') as id_collecion_maping_det,est.esti_for, est_itms.id_sub_design,



    		ifnull(tag.tag_code,'') as tag_code,est_itms.est_rate_per_grm,IFNULL(est.discount,0) as est_discount,c.scheme_closure_benefit,IFNULL(tag.net_wt,0) as tag_net_wt,tag.sales_value as item_cost,IFNULL(cus.aadharid,'') as aadharid,IFNULL(cus.driving_license_no,'') as dl_no,IFNULL(cus.passport_no,'') as pp_no,ifnull(tag.quality_id,'') as quality_id,pro.stone_type,ifnull(tag.uom_gross_wt,'') as uom_id,ifnull(tag.stone_calculation_based_on,'') as stone_calculation_based_on, est.is_eda, est_itms.pure_rate



    		FROM ret_estimation as est

    		LEFT JOIN ret_estimation_items as est_itms ON est_itms.esti_id = est.estimation_id



    		left join ret_taging tag on tag.tag_id=est_itms.tag_id



    		LEFT JOIN customerorderdetails as ord ON ord.id_orderdetails=est_itms.id_orderdetails

    		LEFT JOIN (SELECT est_item_id,sum(price) as stn_price,sum(wt) as stn_wgt FROM `ret_estimation_item_stones` GROUP by est_item_id) as stn_detail ON stn_detail.est_item_id = est_itms.est_item_id

    		LEFT JOIN (SELECT est_item_id,sum(price) as other_mat_price,sum(price) as other_mat_wgt FROM `ret_estimation_item_other_materials` GROUP by est_item_id) as est_oth_mat ON est_oth_mat.est_item_id = est_itms.est_item_id

    		LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(certification_cost) as certification_cost,sum(wt) as stn_wt FROM `ret_taging_stone` GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = est_itms.tag_id

    		LEFT JOIN (SELECT tag_id,sum(price) as othermat_amount,sum(wt) as othermat_wt FROM `ret_taging_other_materials` GROUP by tag_id) as tag_other_mat ON tag_other_mat.tag_id = est_itms.tag_id

    		LEFT JOIN ret_product_master as pro ON pro.pro_id = est_itms.product_id

    		LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id

    		LEFT JOIN metal mt on mt.id_metal=c.id_metal

    		LEFT JOIN ret_design_master as des ON des.design_no = est_itms.design_id

    		LEFT JOIN ret_purity as pur ON pur.id_purity = est_itms.purity

    		LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id

    		LEFT JOIN customer cus on cus.id_customer=est.cus_id

    		LEFT JOIN village v on v.id_village=cus.id_village



    		LEFT JOIN ret_metal_purity_rate r on r.id_metal=c.id_metal and r.id_purity=est_itms.purity

    		LEFT JOIN (SELECT est_item_id, SUM(IFNULL(amount,0)) AS charge_value FROM ret_estimation_other_charges GROUP BY est_item_id) AS rec ON rec.est_item_id = est_itms.est_item_id

			LEFT JOIN (SELECT est_item_id,sum(tag_other_itm_amount) as tag_other_itm_amount FROM `ret_est_other_metals` GROUP by est_item_id) as est_other_metal ON est_other_metal.est_item_id

    		LEFT JOIN ret_nontag_item nt on nt.product=est_itms.product_id " . ($id_branch != '' ? " and nt.branch=" . $id_branch . "" : '') . "

    		WHERE " . (!empty($id_branch) ? "est.id_branch=" . $id_branch . "" : '') . "

    		" . ($estId != '' ? " and est.esti_no ='" . $estId . "' " : '') . "

    		" . ($metal_type != '' ? " and mt.id_metal=" . $metal_type . "" : '') . "

    		AND est_itms.est_item_id IS NOT NULL and tag.id_orderdetails IS NULL and est_itms.purchase_status=0 AND (tag.tag_status =0 OR tag.tag_status IS NULL OR tag.is_partial=1)

    		AND date(est.estimation_datetime)='" . $dCData['entry_date'] . "'

    		AND ifnull(tag.tag_type, 0) = 1

    		GROUP by  est_itms.est_item_id

    		order by est_itms.esti_id DESC");

			// echo $this->db->_error_message(); exit;

			//print_r($this->db->last_query());exit;

			$item_details = $items_query->result_array();
		} else if (($billType == 1 || $billType == 2 || $billType == 3 || $billType == 9 ) && $tag_code != '' || $old_tag_id != '' || $order_no!='') {

			$items_query = $this->db->query("SELECT tag.tag_code,ifnull(tag.tag_id,'') as tag_id,ifnull(tag.product_id, '') as product_id,ifnull(tag.design_id, '') as design_id,

            ifnull(pro.hsn_code,'') as hsn_code,tag.purity as purid,IFNULL(tag.size,'') as size,tag.piece as piece,IFNULL(tag.less_wt,0) as less_wt,

            IFNULL(tag.gross_wt,0) as gross_wt,IFNULL(tag.net_wt,0) as net_wt,tag.sales_value as item_cost,ifnull(product_short_code, '-') as product_short_code,

            ifnull(product_name, '-') as product_name,ifnull(design_code, '-') as design_code,

            ifnull(design_name, '') as design_name, pur.purity as purname,pro.stone_type,

            mt.tgrp_id as tax_group_id , tgrp_name, ifnull(c.id_metal,'') as metal_type,

            IFNULL(tag_stn_detail.stn_amount,0) as stone_price,IFNULL(tag_stn_detail.certification_cost,0) as certification_cost,r.rate_field,

            ifnull(rec.charge_value, 0) as charge_value, c.id_ret_category as catid ,

            IFNULL(tag.retail_max_wastage_percent,0) as wastage_percent , IFNULL(tag.tag_mc_value,'') as mc_value, tag.tag_mc_type as mc_type,tag.purity as purid,tag.calculation_based_on,IFNULL(tag.net_wt,0) as tag_net_wt,IFNULL(sub_des.sub_design_name,0) as sub_design_name,0 as item_type,IFNULL(tag.id_section,'') as id_section,IFNULL(tag.id_orderdetails,'') as id_orderdetails,tag.id_sub_design,

			IFNULL(tag.hu_id,IFNULL(tag.hu_id2,'')) as huid,tag.hu_id,tag.hu_id2,tag_other_metal.tag_other_itm_amount,

            DATEDIFF(date(now()),date(tag_datetime)) AS age,date_format(tag.tag_datetime,'%d-%m-%Y') as tag_date,

       		IFNULL(puritm.item_wastage,IFNULL(tag.lot_wastage_percentage,0)) as purchase_va,IF(puritm.mc_type is null,if(tag.lot_mc_type=1,'Per Gram','Per Pcs'),if(puritm.mc_type=1,'Per Gram','Per Pcs')) as pur_mc_typ,IFNULL(puritm.mc_type,IFNULL(tag.lot_mc_type,0)) as purchase_mc_type,IFNULL(puritm.mc_value,IFNULL(tag.lot_making_charge,0)) as purchase_mc,IFNULL(puritm.purchase_touch,IFNULL(tag.lot_purchase_touch,0)) as purchase_touch,IFNULL(puritm.fix_rate_per_grm,IFNULL(tag.lot_rate,0)) as purchase_rate,IFNULL(puritm.rate_calc_type,IFNULL(tag.lot_rate_calc_type,0)) as purchase_rate_calc_type,IFNULL(puritm.pure_wt_calc_type,IFNULL(tag.lot_calc_type,0)) as purchase_calc_type,concat(cus.firstname,' ',cus.mobile) as cus_name,cus.id_customer, tag.tag_purchase_cost as pur_cost,

			IFNULL(co.order_no,'') AS order_no,IFNULL(ad.id_country,'') as id_country,IFNULL(ad.id_state,'') as id_state


            FROM ret_taging tag

            LEFT JOIN ret_product_master as pro ON pro.pro_id = tag.product_id

			LEFT JOIN ret_purchase_order_items as puritm ON  puritm.po_item_pro_id = tag.product_id AND puritm.po_item_des_id = tag.design_id AND puritm.po_item_sub_des_id = tag.id_sub_design and puritm.lot_no = tag.tag_lot_id

            LEFT JOIN customerorderdetails as cod ON cod.id_orderdetails = tag.id_orderdetails

            LEFT JOIN customerorder as co ON co.id_customerorder = cod.id_customerorder

			LEFT JOIN customer cus on cus.id_customer=co.order_to

			LEFT JOIN address ad on ad.id_customer=cus.id_customer

            LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id

            LEFT JOIN metal mt on mt.id_metal=c.id_metal

            LEFT JOIN ret_design_master as des ON des.design_no = tag.design_id

			LEFT JOIN ret_sub_design_master as sub_des ON sub_des.id_sub_design = tag.id_sub_design

            LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity

            LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = mt.tgrp_id

            LEFT JOIN ret_metal_purity_rate r on r.id_metal=c.id_metal and r.id_purity=tag.purity

            LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(certification_cost) as certification_cost,sum(wt) as stn_wt FROM `ret_taging_stone` GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = tag.tag_id

            LEFT JOIN (SELECT tag_id,sum(price) as othermat_amount,sum(wt) as othermat_wt FROM `ret_taging_other_materials` GROUP by tag_id) as tag_other_mat ON tag_other_mat.tag_id = tag.tag_id

            LEFT JOIN (SELECT tag_id, SUM(IFNULL(t.charge_value,0)) AS charge_value FROM ret_taging_charges t GROUP BY t.tag_id) AS rec ON rec.tag_id = tag.tag_id

			LEFT JOIN (SELECT tag_other_itm_tag_id,sum(tag_other_itm_amount) as tag_other_itm_amount FROM `ret_tag_other_metals` GROUP by tag_other_itm_tag_id) as tag_other_metal ON tag_other_metal.tag_other_itm_tag_id =tag.tag_id

    		WHERE " . (!empty($id_branch) ? "tag.current_branch=" . $id_branch . "" : '') . "

    		" . ($tag_code != '' ? " and tag.tag_code ='" . $tag_code . "' " : '') . "

			" . ($old_tag_id != '' ? " and tag.old_tag_id ='" . $old_tag_id . "' " : '') . "

			" . ($order_no != '' ? " and co.order_no ='" . $order_no . "' " : '') . "



    		AND (tag.tag_status =0)

    		AND ifnull(tag.tag_type, 0) != 1 ");

			//print_r($this->db->last_query());exit;

			$item_details = $items_query->result_array();
		}

		if (($billType == 2 || $billType == 3 || $billType == 4 || $billType == 5 || $billType == 8 || $billType == 9) && $estId != '') {

			$old_matel_query = $this->db->query("SELECT old_metal_sale_id,est_old.est_id, purchase_status,bill_id,

		id_category, type, item_type,IFNULL(gross_wt,0) as gross_wt,IFNULL(net_wt,0) as net_wt,met.id_metal,esti_no,

		ifnull(dust_wt,0.000) as dust_wt,ifnull(stone_wt,0.000) as stone_wt,

		round((ifnull(dust_wt,0.000) - ifnull(stone_wt,0.000)),3) as less_wt,purpose,

		if(type = 1, 'Melting', 'Retag') as reusetype,

		if(item_type = 1, 'Ornament', if(item_type = 2, 'Coin', if(item_type = 3, 'Bar',''))) as receiveditem, est_old.purity as purid, IFNULL(wastage_percent,0) as wastage_percent,

		IFNULL(wastage_wt,0) as wastage_wt, rate_per_gram, amount,

		pur.purity as purname, met.metal,ifnull(stn_detail.stn_price,0) as stone_price,concat(cus.firstname,' ',cus.mobile) as cus_name,cus.id_customer,

		v.village_name,if(cus.is_vip=1,'Yes','No') as vip,cus.mobile,

		(select count(sa.id_scheme_account) from scheme_account sa left join customer cust on cust.id_customer=sa.id_customer) as accounts,

		IFNULL(est_old.purity,'') as purity,IFNULL(cus.pan,'') as pan_no,IFNULL(cus.gst_number,'') as gst_number,IFNULL(est_old.piece,0) as piece,est.esti_for, 0 as est_rate_per_grm,IFNULL(est.discount,0) as est_discount,

		IFNULL(est_old.touch,0) as touch,IFNULL(cus.aadharid,'') as aadharid,IFNULL(cus.driving_license_no,'') as dl_no,IFNULL(cus.passport_no,'') as pp_no,

        est_old.id_old_metal_type,est_old.id_old_metal_category,old_mtype.metal_type,old_mcat.old_metal_cat,est_old.remark,IFNULL(ad.id_country,'') as id_country,IFNULL(ad.id_state,'') as id_state

		FROM ret_estimation as est

		LEFT JOIN ret_estimation_old_metal_sale_details as est_old ON est_old.est_id = est.estimation_id

		LEFT JOIN ret_purity as pur ON pur.id_purity = est_old.purity

		LEFT JOIN metal as met ON met.id_metal = est_old.id_category

		LEFT JOIN ret_old_metal_type old_mtype ON old_mtype.id_metal_type=est_old.id_old_metal_type

        LEFT JOIN ret_old_metal_category old_mcat ON old_mcat.id_old_metal_cat=est_old.id_old_metal_category

		LEFT JOIN customer cus on cus.id_customer=est.cus_id

		LEFT JOIN address ad on ad.id_customer=cus.id_customer

		LEFT JOIN village v on v.id_village=cus.id_village

		LEFT JOIN (SELECT est_id,sum(price) as stn_price,sum(wt) as stn_wgt FROM `ret_esti_old_metal_stone_details` GROUP by est_id) as stn_detail ON stn_detail.est_id = est_old.est_id

		WHERE " . (!empty($id_branch) ? "est.id_branch=" . $id_branch . ' AND' : '') . "

		" . ($metal_type != '' ? " met.id_metal=" . $metal_type . " AND " : '') . "

		est.esti_no ='" . $estId . "' AND old_metal_sale_id IS NOT NULL  and est_old.purchase_status=0

		AND date(est.estimation_datetime)='" . $dCData['entry_date'] . "'

		and est_old.purchase_status=0

		order by old_metal_sale_id DESC");

			// 		echo $this->db->last_query(); exit;

			$old_matel_details = $old_matel_query->result_array();

			foreach ($old_matel_details as $metal) {

				$return_data['old_matel_details'][] = array(

					'amount'			=> $metal['amount'],

					'bill_id'			=> $metal['bill_id'],

					'dust_wt'			=> $metal['dust_wt'],

					'est_id'			=> $metal['est_id'],

					'piece'			    => $metal['piece'],

					'esti_no'			=> $metal['esti_no'],

					'gross_wt'			=> $metal['gross_wt'],

					'net_wt'            => $metal['net_wt'],

					'id_metal'          => $metal['id_metal'],

					'id_category'		=> $metal['id_category'],

					'item_type'			=> $metal['item_type'],

					'less_wt'			=> $metal['less_wt'],

					'metal'				=> $metal['metal'],

					'old_metal_sale_id'	=> $metal['old_metal_sale_id'],

					'purchase_status'	=> $metal['purchase_status'],

					'purid'				=> $metal['purid'],

					'purname'			=> $metal['purname'],

					'purpose'			=> $metal['purpose'],

					'rate_per_gram'		=> $metal['rate_per_gram'],

					'receiveditem'		=> $metal['receiveditem'],

					'reusetype'			=> $metal['reusetype'],

					'stone_wt'			=> $metal['stone_wt'],

					'type'				=> $metal['type'],

					'wastage_percent'	=> $metal['wastage_percent'],

					'wastage_wt'		=> $metal['wastage_wt'],

					'stone_price'		=> $metal['stone_price'],

					'cus_name'		    => $metal['cus_name'],

					'mobile'		    => $metal['mobile'],

					'id_customer'		=> $metal['id_customer'],

					'chit_cus'		    => ($metal['accounts'] == 0 ? 'No' : 'Yes'),

					'vip_cus'		    => $metal['vip'],

					'mobile'		    => $metal['mobile'],

					'village_name'		=> $metal['village_name'],

					'purity'		    => $metal['purity'],

					'pan_no'		    => $metal['pan_no'],

					'gst_number'	    => $metal['gst_number'],

					'aadharid'		    => $metal['aadharid'],

					'dl_no'		        => $metal['dl_no'],

					'pp_no'		        => $metal['pp_no'],

					'touch'		        => $metal['touch'],

					'esti_for'          => $metal['esti_for'],

					'id_old_metal_type'	=> $metal['id_old_metal_type'],

					'id_old_metal_category'	=> $metal['id_old_metal_category'],

					'metal_type'	=> $metal['metal_type'],

					'old_metal_cat'	=> $metal['old_metal_cat'],

					'remark'	=> $metal['remark'],

					'id_sub_design'	=> $metal['id_sub_design'],

					'stone_details'		=> $this->get_old_metal_stone_details($metal['old_metal_sale_id']),

					'id_country'					=> $metal['id_country'],

					'id_state'						=> $metal['id_state'],

				);
			}
		}

		if (($billType == 5 || $billType == 9) && ($order_no != '')) {
		// if (($billType == 9) && ($order_no != '')) {

			$advance = $this->db->query("SELECT b.bill_id,b.bill_type,a.order_no,a.advance_amount as paid_advance,(a.advance_amount-a.adjusted_amount) as paid_advance,

			a.advance_weight as paid_weight,s.metal_type,a.store_as,a.advance_type,a.rate_calc,a.rate_per_gram,a.bill_id,a.bill_adv_id,(a.advance_amount-a.adjusted_amount) as adj_advance,0 as balance_amount,0 as is_checked, b.is_eda AS is_eda

			from ret_billing b

			LEFT JOIN ret_billing_advance a on a.bill_id=b.bill_id

			LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id=a.old_metal_sale_id

			where a.is_adavnce_adjusted=0  and b.bill_status=1

			" . ($order_no != '' ? " and a.order_no='" . $order_no . "'" : '') . "



			" . ($id_branch != '' ? " and b.id_branch='" . $id_branch . "'" : '') . "

			");

			//print_r($this->db->last_query());exit;

			$adv = $advance->result_array();

			foreach ($adv as $key => &$value) {

				$value['bill_no'] = $this->get_bill_no_format_detail($value['bill_id'], $value['bill_type']);

			}

			$return_data["advance_details"] = $adv;

		} else if ($billType == 9 && $estId != '') {

			$advance_details = $this->db->query("SELECT b.bill_id,b.bill_type,a.order_no,(a.advance_amount-a.adjusted_amount) as paid_advance,a.advance_weight as paid_weight,s.metal_type,a.store_as,a.advance_type,a.rate_calc,a.rate_per_gram,a.bill_id,a.bill_adv_id,(a.advance_amount-a.adjusted_amount) as adj_advance,0 as balance_amount,0 as is_checked, b.is_eda AS is_eda

            FROM ret_billing_advance a

            LEFT JOIN ret_billing b ON b.bill_id=a.bill_id

            LEFT JOIN customerorder c ON c.id_customerorder=a.id_customerorder

            LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder

            LEFT JOIN ret_estimation_items e ON e.id_orderdetails=d.id_orderdetails

            LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id=a.old_metal_sale_id

            LEFT JOIN ret_estimation est ON est.estimation_id=e.esti_id

            where a.is_adavnce_adjusted=0 and b.bill_status=1

            and est.esti_no ='" . $estId . "' AND date(est.estimation_datetime)='" . $dCData['entry_date'] . "'

            " . ($id_branch != '' ? " and b.id_branch='" . $id_branch . "'" : '') . "

            GROUP by a.bill_adv_id");

			// print_r($this->db->last_query());exit;

			$adv= $advance_details->result_array();

			foreach ($adv as $key => &$value) {

				$value['bill_no'] = $this->get_bill_no_format_detail($value['bill_id'], $value['bill_type']);

			}
			


			$return_data["advance_details"] = $adv;
		}

		if (sizeof($item_details) > 0) {

			foreach ($item_details as $item) {

				$grossWt = ($item['gross_wt'] != null ? $item['gross_wt'] : '');

				$lot_no = ($item['tag_lot_id'] != null ? $item['tag_lot_id'] : '');

				$purchase_stone =($item['est_item_id'] != null ? $this->get_purchase_stone($item['est_item_id']) : ($item['tag_id']!=''?$this->get_purchase_stone_tag($item['tag_id']):[]) ) ;

				$return_data['item_details'][] = array(

					'calculation_based_on' => $item['calculation_based_on'],

					'design_code'		  	=> $item['design_code'],

					'tag_other_itm_amount'  => $item['tag_other_itm_amount'],

					'design_id'				=> ($item['design_id'] != null ? $item['design_id'] : ''),

					'design_name'			=> ($item['design_name'] != null ? $item['design_name'] : ''),

					'id_sub_design'			=> ($item['id_sub_design'] != null ? $item['id_sub_design'] : ''),

					'discount'				=> $item['discount'],

					'est_item_id'			=> ($item['est_item_id'] != null ? $item['est_item_id'] : ''),

					'esti_id'				=> ($item['esti_id'] != null ? $item['esti_id'] : ''),

					'esti_no'				=> ($item['esti_no'] != null ? $item['esti_no'] : ''),

					'fixed_rate'			=> ($item['fixed_rate'] != null ? $item['fixed_rate'] : ''),

					'gross_wt'				=> $grossWt,

					'hsn_code'				=> ($item['hsn_code'] != null ? $item['hsn_code'] : ''),

					'is_partial'			=> ($item['is_partial'] != null ? $item['is_partial'] : ''),

					'is_non_tag'			=> ($item['is_non_tag'] != null ? $item['is_non_tag'] : ''),

					'item_cost'				=> ($item['item_cost'] != null ? $item['item_cost'] : ''),

					'item_type'				=> ($item['item_type'] != null ? $item['item_type'] : ''),

					'less_wt'				=> $item['less_wt'],

					'mc_type'				=> ($item['mc_type'] != null ? $item['mc_type'] : ''),

					'mc_value'				=> ($item['mc_value'] != '' ? $item['mc_value'] : ''),

					'metal_type'			=> $item['metal_type'],

					'scheme_closure_benefit' => $item['scheme_closure_benefit'],

					'net_wt'				=> $item['net_wt'],

					'tag_net_wt'			=> $item['tag_net_wt'],

					'othermat_amount'		=> ($item['othermat_amount'] != null ? $item['othermat_amount'] : 0),

					'othermat_wt'			=> ($item['othermat_wt'] != null ? $item['othermat_wt'] : 0),

					'stock_type'			=> ($item['stock_type'] != null ? $item['stock_type'] : ''),

					'piece'					=> $item['piece'],

					'product_id'			=> $item['product_id'],

					'product_name'			=> $item['product_name'],

					'product_short_code'	=> $item['product_short_code'],

					'purchase_status'		=> $item['purchase_status'],

					'purid'					=> ($item['purid'] != null ? $item['purid'] : ''),

					'purname'				=> $item['purname'],

					'pure_rate'				=> (isset($item['pure_rate']) ? $item['pure_rate'] : 0),

					'size'					=> ($item['size'] != null ? $item['size'] : ''),
					
					'size_name'				=> ($item['size_name'] != null ? $item['size_name'] : ''),
					
					'size_value'			=> ($item['size_value'] != null ? $item['size_value'] : 0),

					'stn_wgt'				=> ($item['stn_wgt'] != null ? $item['stn_wgt'] : ''),

					'stone_price'			=> ($item['stone_price'] != null ? $item['stone_price'] : 0),

					'certification_cost'	=> $item['certification_cost'],

					'tag_id'				=> ($item['tag_id'] != null ? $item['tag_id'] : ''),

					'tag_code'              => ($item['tag_code'] != null ? $item['tag_code'] : ''),

					'tax_group_id'			=> $item['tax_group_id'],

					'tgrp_name'				=> $item['tgrp_name'],

					'huid'		    		=>$item['huid'],
					'hu_id'		    		=>$item['hu_id'],
					'hu_id2'		    	=>$item['hu_id2'],

					'uom'					=> ($item['uom'] != null ? $item['uom'] : ''),

					'wastage_percent'		=> ($item['wastage_percent'] != null ? $item['wastage_percent'] : ''),

					'max_wastage'		    => ($item['max_wastage'] != null ? $item['max_wastage'] : 0),

					'min_wastage'		    => $item['min_wastage'],

					'cus_name'		        => $item['cus_name'],

					'mobile'		        => $item['mobile'],

					'id_customer'		    => $item['id_customer'],

					'pan_no'		        => $item['pan_no'],

					'gst_number'		    => $item['gst_number'],

					'sub_design_name'		=> $item['sub_design_name'],

					'aadharid'		        => $item['aadharid'],

					'dl_no'		            => $item['dl_no'],

					'pp_no'		            => $item['pp_no'],

					'chit_cus'		        => ($item['accounts'] == 0 ? 'No' : 'Yes'),

					'vip_cus'		        => $item['vip'],

					'mobile'		        => $item['mobile'],

					'village_name'		    => ($item['village_name'] != null ? $item['village_name'] : ''),

					'available_pieces'		=> ($item['available_pieces'] != null ? $item['available_pieces'] : ''),

					'available_gross_wt'	=> ($item['available_gross_wt'] != null ? $item['available_gross_wt'] : ''),

					'id_orderdetails'	    => $item['id_orderdetails'],

					'id_customerorder'	    => $item['id_customerorder'],

					'gift_applicable'		=> $item['gift_applicable'],

					'rate_calc_from'		=> $item['rate_calc_from'],

					'rate_field'            => $item['rate_field'],

					'order_no'				=> ($item['order_no'] != null ? $item['order_no'] : ''),

					'stone_details'			=> ($estId != '' && $item['est_item_id'] != '' ? $this->get_stone_details($item['est_item_id']) : ($item['tag_id']!=''?$this->get_tag_stone_details($item['tag_id']):$this->get_rep_ord_stone_details($item['id_orderdetails']))),

                       //other metal details

					'other_metal_details'	=> ($estId != '' ? ($item['est_item_id'] != '' ? $this->get_estimation_other_metal_details($item['est_item_id']) : []) :  ($item['tag_id'] != '' ? $this->get_other_metal_details($item['tag_id']) : [])),

					'charge_value'			=> $item['charge_value'],



					'est_rate_per_grm'		=> $item['est_rate_per_grm'],

					'est_discount'		    => $item['est_discount'],
					
					'is_eda'		   		=> (isset($item['is_eda']) ? $item['is_eda'] : 0),



					'id_section'		    => $item['id_section'],

					'quality_id'            => $item['quality_id'],

					'stone_type'            => $item['stone_type'],

					'uom_id'                => $item['uom_id'],

					'purchase_mc_type'                => $item['purchase_mc_type'],

					'purchase_va'                => $item['purchase_va'],

					'purchase_mc'                => $item['purchase_mc'],

					'purchase_cal_type'          => $item['purchase_calc_type'],

					'purchase_rate_calc_type'          => $item['purchase_rate_calc_type'],

					'purchase_rate'          => $item['purchase_rate'],

					'purchase_touch'          => $item['purchase_touch'],

					'tag_purchase_cost'          => $item['pur_cost'],

					'tag_age'               		 => $item['age'],

					'pur_diamond'               		 => ($purchase_stone['dia_amount'] != null ? $purchase_stone['dia_amount'] : 0),

					'pur_stone'               		 => ($purchase_stone['stn_amount'] != null ? $purchase_stone['stn_amount'] : 0),

					'sale_diamond'               		 => 0,

					'sale_stone'               		 => 0,

					'tag_date'               		 => $item['tag_date'],

					'item_emp_name'         		=> $item['item_emp_name'],

					'item_emp_id'					=> $item['item_emp_id'],

					'id_state'						=> $item['id_state'],

					'id_country'					=> $item['id_country'],


					'stone_calculation_based_on'  => $item['stone_calculation_based_on'],

					'esti_for'              => ($item['esti_for'] != null ? $item['esti_for'] : ''),

					'id_collecion_maping_det' => '',

					'charges'				=> ($estId != '' && $item['est_item_id'] != '' ? $this->get_other_estcharges($item['est_item_id']) : ($item['tag_id'] != '' ? $this->get_charges($item['tag_id']) : '')),

					'esti_purchase_cost'		=> isset($item['esti_purchase_cost']) ? $item['esti_purchase_cost'] : 0,

					'mc_va_limit'			=> ($item['product_id'] != ''  && $item['design_id'] != '' && $item['id_sub_design'] != '' ?  $this->get_mc_va_limit($item['product_id'], $item['design_id'], $item['id_sub_design'], $grossWt, $lot_no, $id_branch, $item['net_wt']) : ''),

					'tag_images'	=> ($item['tag_id'] != '' ? $this->getTagImageDetails($item['tag_id']) : []),

				);
			}
		}



		if ($item_details[0]['esti_id'] != '') {

			$return_data["packing_details"] = $this->get_EstimationPackingItems($item_details[0]['esti_id']);
		}



		$est_chit_query = $this->db->query("SELECT chit_ut_id, est_id, est_chit.scheme_account_id, utl_amount,concat(s.code,'',sa.scheme_acc_number) as scheme_acc_number,s.scheme_type,s.scheme_type as new_scheme_type,sa.closing_balance,

		IFNULL(est_chit.closing_weight,0) as closing_weight,IFNULL(est_chit.wastage_per,0) as wastage_per,IFNULL(est_chit.savings_in_wastage,0) as savings_in_wastage,s.rate_type,

		IFNULL(est_chit.mc_value,0) as mc_value,IFNULL(est_chit.savings_in_making_charge,0) as savings_in_making_charge,s.total_installments,pay.paid_installments,sa.closing_amount as closing_amount,

		s.is_wast_and_mc_benefit_apply,sa.additional_benefits,sa.closing_add_chgs,est_chit.rate_per_gram,
		ifnull(s.flexible_sch_type, 0) as flexible_sch_type, s.wgt_convert, IFNULL(sa.closing_benefits,0) as closing_benefits

        FROM ret_est_chit_utilization as est_chit

        LEFT JOIN scheme_account sa ON sa.id_scheme_account=est_chit.scheme_account_id

        LEFT JOIN scheme s ON s.id_scheme=sa.id_scheme

        LEFT JOIN ret_estimation e ON e.estimation_id=est_chit.est_id

        LEFT JOIN (select SUM(p.payment_amount) as paid_amount, IFNULL(cp.cash_pay,0) as cash_pay,sa.id_scheme_account,

        IFNULL(IF(sa.is_opening = 1,

        IFNULL(sa.paid_installments,0) + IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0),

        if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0) as paid_installments

        FROM payment p

        LEFT JOIN (SELECT SUM(IFNULL(pmd.payment_amount,0)) AS cash_pay, id_payment FROM `payment_mode_details` AS pmd WHERE pmd.payment_mode = 'CSH' AND pmd.payment_status = 1 GROUP BY id_payment) AS cp ON cp.id_payment = p.id_payment

        left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account

        left join scheme s on s.id_scheme=sa.id_scheme

        where p.payment_status=1 GROUP BY p.id_scheme_account) as pay on pay.id_scheme_account = sa.id_scheme_account

        WHERE e.esti_no = '" . $estId . "' AND date(e.estimation_datetime)='" . $dCData['entry_date'] . "' and sa.is_utilized = 0

        " . ($id_branch != '' ? " and e.id_branch='" . $id_branch . "'" : '') . "");

		//print_r($this->db->last_query());exit;

		$return_data["chit_details"] = $est_chit_query->result_array();



		/*$est_voucher_query = $this->db->query("SELECT gift_voucher_id, est_id,

		voucher_no, gift_voucher_details, est_vouch.gift_voucher_amt

		FROM ret_estimation as est

		LEFT JOIN ret_est_gift_voucher_details as est_vouch ON est_vouch.est_id = est.estimation_id

		WHERE est.estimation_id ='".$estId."' AND cus_id ='".$cusId."' AND voucher_no IS NOT NULL");

		$return_data["voucher_details"] = $est_voucher_query->result_array();

		$est_chit_query = $this->db->query("SELECT chit_ut_id, est_id,

		scheme_account_id, utl_amount

		FROM ret_estimation as est

		LEFT JOIN ret_est_chit_utilization as est_chit ON est_chit.est_id = est.estimation_id

		WHERE est.estimation_id ='".$estId."' AND cus_id ='".$cusId."' AND scheme_account_id IS NOT NULL");

		$return_data["chit_details"] = $est_chit_query->result_array();*/

		$max_cash = $this->get_maxcash_settings();

		if($max_cash['validate_cash_amt'] == 1) {

			if(($billType == 9 && $estId !='') || ($billType == 5 && $order_no!='')) {

				$bill_ids = array();

				if($billType == 9 && $estId != '') {

					$sql = "SELECT

								DISTINCT bill.bill_id

							FROM ret_estimation AS est

							LEFT JOIN ret_estimation_items AS est_itms ON est_itms.esti_id = est.estimation_id

							LEFT JOIN customerorderdetails AS cod ON cod.id_orderdetails = est_itms.id_orderdetails

							LEFT JOIN ret_billing_advance AS ba ON ba.id_customerorder = cod.id_customerorder

							LEFT JOIN ret_billing AS bill ON bill.bill_id = ba.bill_id

							WHERE esti_no = ".$estId." AND `est_date` = '".$dCData['entry_date']."' AND est_itms.id_orderdetails iS NOT NULL AND ba.advance_type = 1 AND bill.bill_status = 1 AND est.id_branch = '".$id_branch."'";

					$query = $this->db->query($sql);

					$bill_ids = $query->result_array();

				} else if($billType == 5 && $order_no != '') {

					$sql = "SELECT

								DISTINCT ba.bill_id

							FROM ret_billing_advance AS ba

							LEFT JOIN ret_billing AS rb ON rb.bill_id = ba.bill_id

							WHERE rb.bill_status = 1 AND ba.advance_type = 1 AND ba.order_no = '".$order_no."'

							".($id_branch!='' ? " AND rb.id_branch = '".$id_branch."'" :'')."";

					$query = $this->db->query($sql);

					$bill_ids = $query->result_array();

				}

				$where = "";

				$where_adv_adj = "";

				$i = 1;

				foreach($bill_ids as $bill_id) {

					if($i == 1) {

						$where = $where." bp.bill_id = ".$bill_id['bill_id'];

						$where_adv_adj = $where_adv_adj." au.bill_id = ".$bill_id['bill_id'];

					} else {

						$where = $where." OR bp.bill_id = ".$bill_id['bill_id'];

						$where_adv_adj = $where_adv_adj." OR au.bill_id = ".$bill_id['bill_id'];

					}

					$i++;

				}

				if($where != "") {

					$where = " AND (".$where.")";

					$sql = "SELECT

								IFNULL(SUM(bp.payment_amount),0) AS paid_cash

							FROM ret_billing_payment bp

							LEFT JOIN ret_billing bill ON bill.bill_id = bp.bill_id

							WHERE bp.payment_mode = 'Cash' AND DATE(bill.bill_date) != '".$dCData['entry_date']."' AND bill.bill_status = 1 ".$where;

					$query = $this->db->query($sql);

					$q_res = $query->row_array();


					$where_adv_adj = " AND (".$where_adv_adj.")";

					$sql = "SELECT 
								
								IFNULL(SUM(cash_utilized_amt), 0) AS recp_cash_adv_adj 
								
							FROM `ret_advance_utilized` au 
							
							LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = au.id_issue_receipt
							
							LEFT JOIN ret_billing b ON b.bill_id = au.bill_id 
							
							WHERE au.adjusted_for = 1 AND DATE(b.bill_date) != DATE('".$dCData['entry_date']."') AND au.bill_id IS NOT NULL ".$where_adv_adj;

					$query_au = $this->db->query($sql);

					$au_res = $query_au->row_array();


					$cash_amt = round(($max_cash['max_cash_amt'] - $q_res['paid_cash'] - $au_res['recp_cash_adv_adj']),2);

					$max_cash['max_cash_amt'] = $cash_amt;

				}

			}

		}

		$return_data['other_details'] = array(

			"max_cash" => $max_cash,

			"wastage_edit_in_bill" => $this->get_ret_settings("wastage_edit_in_bill"),



			"mc_edit_in_bill" => $this->get_ret_settings("mc_edit_in_bill")

		);

		return $return_data;
	}



	function get_maxcash_settings()
	{

		$maxcash['max_cash_amt'] = $this->get_ret_settings('max_cash_amt');

		$maxcash['validate_cash_amt'] = $this->get_ret_settings('validate_cash_amt');

		return $maxcash;
	}



	function get_EstimationPackingItems($estId)

	{

		$result = [];

		if ($estId != '') {

			$sql = $this->db->query("SELECT i.esti_id,i.id_other_item,i.no_of_piece,inv.name as item_name,IFNULL(inv.item_image,'') as item_image,inv.sku_id

            FROM ret_estimation_other_inventory_issue i

            LEFT JOIN ret_other_inventory_item inv ON inv.id_other_item=i.id_other_item

            WHERE i.esti_id=" . $estId . "");

			return $sql->result_array();
		} else {

			return $result;
		}
	}



	function getEstimationDetailsTags($estId, $billType, $id_branch, $order_no, $fin_year, $metal_type)

	{



		$dCData = $this->getBranchDayClosingData($id_branch);



		$item_details = array();

		$order_details = array();

		$return_data = array("item_details" => array(),  'advance_details' => array(), "order_details" => array(), "order_sale_det" => array());





		$items_query = $this->db->query("SELECT est_itms.est_item_id, esti_id, item_type, est_itms.purchase_status,est.esti_no,

		ifnull(est_itms.product_id, '') as product_id, ifnull(est_itms.tag_id, '') as tag_id, tag.tag_code,

		ifnull(est_itms.design_id, '') as design_id, ifnull(pro.hsn_code,'') as hsn_code,

		est_itms.purity as purid, IFNULL(est_itms.size,'') as size, ifnull(est_itms.uom,'') as uom,IFNULL(est_itms.piece,'') as piece,

		ifnull(est_itms.less_wt,'') as less_wt,IFNULL(est_itms.net_wt,0) as net_wt,IFNULL(est_itms.gross_wt,0) as gross_wt,

		est_itms.calculation_based_on, IFNULL(est_itms.wastage_percent,0) as wastage_percent , IFNULL(est_itms.mc_value,'') as mc_value, est_itms.mc_type,

		tag.sales_value as item_cost, ifnull(product_short_code, '-') as product_short_code,

		ifnull(product_name, '-') as product_name, est_itms.is_partial,est_itms.discount,

		ifnull(design_code, '-') as design_code,

		ifnull(design_name, '') as design_name, pur.purity as purname,

		pro.tgrp_id as tax_group_id , tgrp_name, ifnull(c.id_metal,'') as metal_type,

		ifnull(des.fixed_rate,0) as fixed_rate,

		if(est_itms.id_orderdetails!='',ord.stn_amt,if(est_itms.tag_id != '',tag_stn_detail.stn_amount,stn_detail.stn_price)) as stone_price,

		IFNULL(tag_stn_detail.certification_cost,0) as certification_cost,

		if(est_itms.tag_id != null,stn_wgt,stn_wt) as stn_wgt,

		if(est_itms.tag_id != null,othermat_amount,other_mat_price) as othermat_amount,

		if(est_itms.tag_id != null,othermat_wt,other_mat_wgt) as othermat_wt,est_itms.is_non_tag,concat(cus.firstname,' ',cus.mobile) as cus_name,cus.id_customer,

		v.village_name,if(cus.is_vip=1,'Yes','No') as vip,cus.mobile,

		(select count(sa.id_scheme_account) from scheme_account sa left join customer cust on cust.id_customer=sa.id_customer) as accounts,

		pro.min_wastage,pro.max_wastage,pro.stock_type,nt.no_of_piece as available_pieces,nt.gross_wt as available_gross_wt,IFNULL(est_itms.orderno,'') as order_no,



		ifnull(est_itms.id_orderdetails,'') as id_orderdetails,IFNULL(ord.id_customerorder,'') as id_customerorder,pro.gift_applicable,r.rate_field,

		ifnull(rec.charge_value, 0) as charge_value,IFNULL(est_itms.item_cost,0) as est_cost

		FROM ret_estimation as est

		LEFT JOIN ret_estimation_items as est_itms ON est_itms.esti_id = est.estimation_id



		left join ret_taging tag on tag.tag_id=est_itms.tag_id



		LEFT JOIN customerorderdetails as ord ON ord.id_orderdetails=est_itms.id_orderdetails

		LEFT JOIN (SELECT est_item_id,sum(price) as stn_price,sum(wt) as stn_wgt FROM `ret_estimation_item_stones` GROUP by est_item_id) as stn_detail ON stn_detail.est_item_id = est_itms.est_item_id

		LEFT JOIN (SELECT est_item_id,sum(price) as other_mat_price,sum(price) as other_mat_wgt FROM `ret_estimation_item_other_materials` GROUP by est_item_id) as est_oth_mat ON est_oth_mat.est_item_id = est_itms.est_item_id

		LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(certification_cost) as certification_cost,sum(wt) as stn_wt FROM `ret_taging_stone` GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = est_itms.tag_id

		LEFT JOIN (SELECT tag_id,sum(price) as othermat_amount,sum(wt) as othermat_wt FROM `ret_taging_other_materials` GROUP by tag_id) as tag_other_mat ON tag_other_mat.tag_id = est_itms.tag_id

		LEFT JOIN ret_product_master as pro ON pro.pro_id = est_itms.product_id

		LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id

		LEFT JOIN metal mt on mt.id_metal=c.id_metal

		LEFT JOIN ret_design_master as des ON des.design_no = est_itms.design_id

		LEFT JOIN ret_purity as pur ON pur.id_purity = est_itms.purity

		LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id

		LEFT JOIN customer cus on cus.id_customer=est.cus_id

		LEFT JOIN village v on v.id_village=cus.id_village



		LEFT JOIN ret_metal_purity_rate r on r.id_metal=c.id_metal and r.id_purity=est_itms.purity

		LEFT JOIN (SELECT est_item_id, SUM(IFNULL(amount,0)) AS charge_value FROM ret_estimation_other_charges GROUP BY est_item_id) AS rec ON rec.est_item_id = est_itms.est_item_id

		LEFT JOIN ret_nontag_item nt on nt.design=est_itms.design_id " . ($id_branch != '' ? " and nt.branch=" . $id_branch . "" : '') . "

		WHERE " . (!empty($id_branch) ? "est.id_branch=" . $id_branch . "" : '') . "

		" . ($estId != '' ? " and est.esti_no ='" . $estId . "' " : '') . "

		" . ($metal_type != '' ? " and mt.id_metal=" . $metal_type . "" : '') . "

		AND est_itms.est_item_id IS NOT NULL and est_itms.purchase_status=0

		AND date(est.estimation_datetime)='" . $dCData['entry_date'] . "'

		order by est_itms.esti_id DESC");

		// echo $this->db->_error_message(); exit;

		//print_r($this->db->last_query());exit;

		$item_details = $items_query->result_array();



		if (sizeof($item_details) > 0) {

			foreach ($item_details as $item) {

				$return_data['item_details'][] = array(

					'calculation_based_on' => $item['calculation_based_on'],

					'design_code'		  	=> $item['design_code'],

					'design_id'				=> $item['design_id'],

					'design_name'			=> $item['design_name'],

					'discount'				=> $item['discount'],

					'est_item_id'			=> ($item['est_item_id'] != null ? $item['est_item_id'] : ''),

					'esti_id'				=> ($item['esti_id'] != null ? $item['esti_id'] : ''),

					'tag_code'              => $item['tag_code'],

					'esti_no'				=> ($item['esti_no'] != null ? $item['esti_no'] : ''),

					'fixed_rate'			=> ($item['fixed_rate'] != null ? $item['fixed_rate'] : ''),

					'gross_wt'				=> ($item['gross_wt'] != null ? $item['gross_wt'] : ''),

					'hsn_code'				=> ($item['hsn_code'] != null ? $item['hsn_code'] : ''),

					'is_partial'			=> ($item['is_partial'] != null ? $item['is_partial'] : ''),

					'is_non_tag'			=> ($item['is_non_tag'] != null ? $item['is_non_tag'] : ''),

					'item_cost'				=> ($item['item_cost'] != null ? $item['item_cost'] : ''),

					'item_type'				=> ($item['item_type'] != null ? $item['item_type'] : ''),

					'less_wt'				=> $item['less_wt'],

					'mc_type'				=> ($item['mc_type'] != null ? $item['mc_type'] : ''),

					'mc_value'				=> ($item['mc_value'] != '' ? $item['mc_value'] : ''),

					'metal_type'			=> $item['metal_type'],

					'net_wt'				=> $item['net_wt'],

					'othermat_amount'		=> ($item['othermat_amount'] != null ? $item['othermat_amount'] : 0),

					'othermat_wt'			=> ($item['othermat_wt'] != null ? $item['othermat_wt'] : 0),

					'stock_type'			=> ($item['stock_type'] != null ? $item['stock_type'] : ''),

					'piece'					=> $item['piece'],

					'product_id'			=> $item['product_id'],

					'product_name'			=> $item['product_name'],

					'product_short_code'	=> $item['product_short_code'],

					'purchase_status'		=> $item['purchase_status'],

					'purid'					=> ($item['purid'] != null ? $item['purid'] : ''),

					'purname'				=> $item['purname'],

					'size'					=> ($item['size'] != null ? $item['size'] : ''),

					'stn_wgt'				=> ($item['stn_wgt'] != null ? $item['stn_wgt'] : ''),

					'stone_price'			=> ($item['stone_price'] != null ? $item['stone_price'] : 0),

					'certification_cost'	=> $item['certification_cost'],

					'tag_id'				=> $item['tag_id'],

					'tax_group_id'			=> $item['tax_group_id'],

					'tgrp_name'				=> $item['tgrp_name'],

					'uom'					=> ($item['uom'] != null ? $item['uom'] : ''),

					'wastage_percent'		=> ($item['wastage_percent'] != null ? $item['wastage_percent'] : ''),

					'max_wastage'		    => ($item['max_wastage'] != null ? $item['max_wastage'] : 0),

					'min_wastage'		    => $item['min_wastage'],

					'cus_name'		        => $item['cus_name'],

					'mobile'		        => $item['mobile'],

					'id_customer'		    => $item['id_customer'],

					'chit_cus'		        => ($item['accounts'] == 0 ? 'No' : 'Yes'),

					'vip_cus'		        => $item['vip'],

					'village_name'		    => ($item['village_name'] != null ? $item['village_name'] : ''),

					'available_pieces'		=> ($item['available_pieces'] != null ? $item['available_pieces'] : ''),

					'available_gross_wt'	=> ($item['available_gross_wt'] != null ? $item['available_gross_wt'] : ''),



					'id_orderdetails'	    => $item['id_orderdetails'],



					'id_customerorder'	    => $item['id_customerorder'],



					'gift_applicable'		=> $item['gift_applicable'],



					'rate_calc_from'		=> $item['rate_calc_from'],



					'rate_field'            => $item['rate_field'],

					'est_cost'              => $item['est_cost'],

					'order_no'				=> isset($item['order_no']) ? $item['order_no'] : NULL,

					'stone_details'			=> ($item['tag_id'] == '' ? $this->get_stone_details($item['est_item_id']) : $this->get_tag_stone_details($item['tag_id'])),

					'charge_value'			=> $item['charge_value'],

					'charges'				=> $this->get_other_estcharges($item['est_item_id']),



					'other_metal_details'	=> ($item['tag_id'] != '' ? $this->get_other_metal_details($item['tag_id']) : []),

				);
			}
		}

		/*$est_voucher_query = $this->db->query("SELECT gift_voucher_id, est_id,

		voucher_no, gift_voucher_details, est_vouch.gift_voucher_amt

		FROM ret_estimation as est

		LEFT JOIN ret_est_gift_voucher_details as est_vouch ON est_vouch.est_id = est.estimation_id

		WHERE est.estimation_id ='".$estId."' AND cus_id ='".$cusId."' AND voucher_no IS NOT NULL");

		$return_data["voucher_details"] = $est_voucher_query->result_array();

		$est_chit_query = $this->db->query("SELECT chit_ut_id, est_id,

		scheme_account_id, utl_amount

		FROM ret_estimation as est

		LEFT JOIN ret_est_chit_utilization as est_chit ON est_chit.est_id = est.estimation_id

		WHERE est.estimation_id ='".$estId."' AND cus_id ='".$cusId."' AND scheme_account_id IS NOT NULL");

		$return_data["chit_details"] = $est_chit_query->result_array();*/

		return $return_data;
	}

	function get_stone_details($est_item_id)

	{

		$est_stone_query = $this->db->query("SELECT est_item_stone_id,est_item_id,

						   est_st.stone_id, pieces, wt, price as amount,

                           stone_name, stone_code, uom_name, uom_short_code,is_apply_in_lwt , st.stone_type , stone_cal_type ,rate_per_gram,

                           um.uom_short_code,est_st.uom_id,ifnull(est_st.quality_id,'') as quality_id

						   FROM ret_estimation_item_stones as est_st

                           LEFT JOIN ret_stone as st ON st.stone_id = est_st.stone_id

                           LEFT JOIN ret_uom as um ON um.uom_id = est_st.uom_id

						   WHERE est_st.est_item_id = '" . $est_item_id . "'");

		return $est_stone_query->result_array();
	}


	function get_other_metal_details($tagid)

    {

         $data = $this->db->query("Select rm.tag_other_itm_id,rm.tag_other_itm_tag_id,rm.id_metal,

		 rm.tag_other_itm_metal_id,rm.tag_other_itm_pur_id,rm.tag_other_itm_grs_weight,rm.tag_other_itm_wastage,

		 rm.tag_other_itm_uom,rm.tag_other_itm_cal_type,rm.tag_other_itm_mc,rm.tag_other_itm_rate,rm.tag_other_itm_pcs,

		 rm.tag_other_itm_amount,IFNULL(mt.metal,'-') as metal_name,IFNULL(pur.purity ,'-')as purname

		 from ret_tag_other_metals rm

		 left join ret_category c on c.id_ret_category=rm.tag_other_itm_metal_id

         left join metal mt on mt.id_metal=c.id_metal

         LEFT JOIN ret_purity as pur ON pur.id_purity = rm.tag_other_itm_pur_id

		 where tag_other_itm_tag_id = $tagid");

        return $data->result_array();

    }

	function get_estimation_other_metal_details($est_item_id)

	{

		$data = $this->db->query("Select rm.est_other_itm_id,rm.est_item_id,rm.tag_other_itm_metal_id,

		rm.tag_other_itm_pur_id,rm.tag_other_itm_grs_weight,rm.tag_other_itm_wastage,rm.tag_other_itm_uom,

		rm.tag_other_itm_cal_type,rm.tag_other_itm_mc,rm.tag_other_itm_rate,rm.tag_other_itm_pcs,

		rm.tag_other_itm_amount,IFNULL(mt.metal,'-') as metal_name,IFNULL(pur.purity ,'-')as purname

		from ret_est_other_metals rm

		left join ret_category c on c.id_ret_category=rm.tag_other_itm_metal_id

		left join metal mt on mt.id_metal=c.id_metal

		LEFT JOIN ret_purity as pur ON pur.id_purity = rm.tag_other_itm_pur_id

		where est_item_id = $est_item_id");

		return $data->result_array();
	}

	function get_tag_stone_details($tag_id)

	{

		$tag_stone_query = $this->db->query("SELECT s.tag_stone_id,s.tag_id,s.pieces,s.wt,s.amount,s.stone_id,s.certification_cost,s.rate_per_gram,st.stone_name,st.stone_type,

        um.uom_short_code,s.uom_id,stone_cal_type,s.is_apply_in_lwt,IFNULL(s.stone_quality_id,'') as quality_id

        FROM ret_taging_stone as s

        LEFT JOIN ret_stone st ON st.stone_id=s.stone_id

        LEFT JOIN ret_uom as um ON um.uom_id = st.uom_id

        WHERE s.tag_id = '" . $tag_id . "'");

		return $tag_stone_query->result_array();
	}

	function get_old_metal_stone_details($old_metal_sale_id)

	{

		$est_stone_query = $this->db->query("SELECT est_old_metal_stone_id,est_id,

						   est_st.stone_id, pieces, wt, price,est_st.rate_per_gram,

                           stone_name, stone_code, uom_name, uom_short_code,est_st.uom_id

						   FROM ret_esti_old_metal_stone_details as est_st

                           LEFT JOIN ret_stone as st ON st.stone_id = est_st.stone_id

                           LEFT JOIN ret_uom as um ON um.uom_id = st.uom_id

						   WHERE est_st.est_old_metal_sale_id = '" . $old_metal_sale_id . "'");

		return $est_stone_query->result_array();
	}

	function stone_details_by_bill_id($old_metal_sale_id)

	{

		$est_stone_query = $this->db->query("SELECT bill_item_stone_id,bill_id,bill_det_id,

						   bill_st.stone_id, bill_st.pieces, bill_st.wt, bill_st.price,bill_st.rate_per_gram,

                           stone_name, stone_code, uom_name, uom_short_code,stone_cal_type

						   FROM ret_billing_item_stones as bill_st

                           LEFT JOIN ret_stone as st ON st.stone_id = bill_st.stone_id

                           LEFT JOIN ret_uom as um ON um.uom_id = st.uom_id

						   WHERE bill_st.old_metal_sale_id = '" . $old_metal_sale_id . "'");

		//print_r($this->db->last_query());exit;

		return $est_stone_query->result_array();
	}



	function stone_details_by_bill_det_id($bill_det_id)

	{

		$est_stone_query = $this->db->query("SELECT bill_item_stone_id,bill_id,bill_det_id,

						   bill_st.stone_id, bill_st.pieces, bill_st.wt, bill_st.price,

                           stone_name, stone_code, uom_name, uom_short_code,rate_per_gram,bill_st.price as amount,stone_cal_type,stone_type

						   FROM ret_billing_item_stones as bill_st

                           LEFT JOIN ret_stone as st ON st.stone_id = bill_st.stone_id

                           LEFT JOIN ret_uom as um ON um.uom_id = st.uom_id

						   WHERE bill_st.bill_det_id = '" . $bill_det_id . "'");

		return $est_stone_query->result_array();
	}



	function getAllTaxgroupItems()
	{

		$return_data = array();

		$taxitems = $this->db->query("SELECT tgi_tgrpcode, tgrp_name, tgi_calculation, tgi_type, tax_percentage

									FROM ret_taxgroupitems as tx_grp_itm

									LEFT JOIN ret_taxgroupmaster as grp ON grp.tgrp_id = tx_grp_itm.tgi_tgrpcode

									LEFT JOIN ret_taxmaster as tx ON tx.tax_id = tx_grp_itm.tgi_taxcode");

		if ($taxitems->num_rows() > 0) {

			$return_data = $taxitems->result_array();
		}

		return $return_data;
	}

	function getAvailableCustomers($SearchTxt)
	{

		$data = $this->db->query("SELECT id_customer as value, concat(firstname,'-',username) as label, reference_no, id_branch, id_village, title, initials, lastname, firstname, date_of_birth, date_of_wed, gender, id_address, id_employee, email, mobile, phone, nominee_name, nominee_relationship, nominee_mobile, cus_img, pan, pan_proof, ispan_req, voterid, voterid_proof, rationcard, rationcard_proof, comments, username, passwd, profile_complete, active, is_new, date_add, custom_entry_date, date_upd, added_by, notification, gst_number, cus_ref_code, is_refbenefit_crt_cus, emp_ref_code, is_refbenefit_crt_emp, religion, kyc_status FROM customer

			WHERE username like '%" . $SearchTxt . "%' OR mobile like '%" . $SearchTxt . "%' OR firstname like '%" . $SearchTxt . "%'");

		return $data->result_array();
	}

	function getTaggingBySearch($SearchTxt)
	{

		$bill_type = (isset($_POST['bill_type']) ? $_POST['bill_type'] : '');



		$return_data = array();

		$tag = $this->db->query("SELECT tag.tag_id,

				tag_code, tag_datetime, tag.tag_type, tag_lot_id, ifnull(pro.hsn_code,'') as hsn_code,

				ifnull(tag.design_id,'') as design_id, cost_center, tag.purity, ifnull(tag.size,'') as size, ifnull(uom,'')uom, piece, tag.less_wt, IFNULL(tag.net_wt,0) as net_wt ,IFNULL(tag.gross_wt,0) as gross_wt, ifnull(tag.less_wt,0) as less_wt,

				tag.calculation_based_on, IFNULL(retail_max_wastage_percent,0) as retail_max_wastage_percent, tag_mc_type,retail_max_mc,IFNULL(tag_mc_value,0) as tag_mc_value,

				halmarking, sales_value, pro.tgrp_id, tag.tag_status, product_name, product_short_code, lot_product, pur.purity as purname, ifnull(c.id_metal,'') as metal_type,

				tgrp_name, ifnull(design_code, '-') as design_code,

				ifnull(design_name, '') as design_name,

				stn_amount,stn_wt,othermat_amount,othermat_wt,pro.disc_type,

				IFNULL(l.order_no,'') as order_no

				FROM ret_taging as tag

				Left join ret_lot_inwards_detail ld on tag.id_lot_inward_detail = ld.id_lot_inward_detail

				LEFT JOIN ret_lot_inwards l on l.lot_no=ld.lot_no

				LEFT JOIN ret_product_master as pro ON pro.pro_id = ld.lot_product

                LEFT JOIN ret_category c on c.id_ret_category=pro.cat_id

				LEFT JOIN metal mt on mt.id_metal=c.id_metal

				LEFT JOIN ret_design_master as des ON des.design_no = tag.design_id

				LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id

				LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity

				LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(wt) as stn_wt FROM `ret_taging_stone` GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = tag.tag_id

				LEFT JOIN (SELECT tag_id,sum(price) as othermat_amount,sum(wt) as othermat_wt FROM `ret_taging_other_materials` GROUP by tag_id) as tag_other_mat ON tag_other_mat.tag_id = tag.tag_id

				WHERE tag.tag_status=0  and tag.id_orderdetails is NULL and tag.tag_code = '" . $SearchTxt . "' " . ($_POST['id_branch'] != '' ? "and tag.current_branch=" . $_POST['id_branch'] . "" : '') . " ");

		//echo $this->db->last_query();exit;

		$tag_items = $tag->result_array();

		foreach ($tag_items as $item) {

			$return_data[] = array(

				'calculation_based_on' => $item['calculation_based_on'],

				'cost_center'			=> $item['cost_center'],

				'design_code'		  	=> $item['design_code'],

				'design_id'				=> $item['design_id'],

				'design_name'			=> $item['design_name'],

				'gross_wt'				=> $item['gross_wt'],

				'halmarking'			=> $item['halmarking'],

				'hsn_code'				=> $item['hsn_code'],

				'less_wt'				=> $item['less_wt'],

				'lot_product'			=> $item['lot_product'],

				'net_wt'				=> $item['net_wt'],

				'othermat_amount'		=> $item['othermat_amount'],

				'metal_type'			=> $item['metal_type'],

				'othermat_wt'			=> $item['othermat_wt'],

				'piece'					=> $item['piece'],

				'product_name'			=> $item['product_name'],

				'product_short_code'	=> $item['product_short_code'],

				'purid'					=> $item['purity'],

				'purname'				=> $item['purname'],

				'retail_max_mc'			=> $item['retail_max_mc'],

				'retail_max_wastage_percent'			=> $item['retail_max_wastage_percent'],

				'sales_value'			=> $item['sales_value'],

				'size'					=> $item['size'],

				'stn_amount'			=> $item['stn_amount'],

				'stn_wt'				=> $item['stn_wt'],

				'tag_code'				=> $item['tag_code'],

				'tag_datetime'			=> $item['tag_datetime'],

				'tag_id'				=> $item['tag_id'],

				'tag_lot_id'			=> $item['tag_lot_id'],

				'tag_mc_type'			=> $item['tag_mc_type'],

				'tag_mc_value'			=> $item['tag_mc_value'],

				'tag_status'			=> $item['tag_status'],

				'tag_type'				=> $item['tag_type'],

				'tgrp_id'				=> $item['tgrp_id'],

				'tgrp_name'				=> $item['tgrp_name'],

				'uom'					=> $item['uom'],

				'disc_type'		        => $item['disc_type'],

				'order_no'		        => $item['order_no'],

				'stone_details'			=> $this->get_tag_stone_details($item['tag_id'])

			);
		}

		return $return_data;
	}

	function getProductBySearch($SearchTxt)
	{

		$data = $this->db->query("SELECT pro_id as value,

				product_short_code as label, product_name,

				wastage_type, other_materials, has_stone,

				has_hook, has_screw, has_fixed_price,

				has_size, less_stone_wt, no_of_pieces, calculation_based_on

				FROM ret_product_master as pro

				WHERE product_short_code LIKE '%" . $SearchTxt . "%' OR product_name LIKE '%" . $SearchTxt . "%'");

		return $data->result_array();
	}

	function getProductDesignBySearch($SearchTxt, $procode)
	{

		$where = empty($procode) ? "WHERE " : "WHERE product_id =$procode AND ";

		$data = $this->db->query("SELECT design_no as value,

				design_code as label, design_name,

				min_length, max_length, min_width, max_width,

				min_dia, max_dia,

				min_weight, max_weight, fixed_rate

				FROM ret_design_master as des

				" . $where . " design_code LIKE '%" . $SearchTxt . "%'");

		return $data->result_array();
	}

	function getMetalTypes()
	{

		$query = $this->db->query("SELECT id_metal, metal FROM metal");

		return $query->result_array();
	}

	function getUOMDetails()

	{

		$sql = $this->db->query("SELECT * FROM ret_uom where uom_status = 1");

		return $sql->result_array();
	}

	function get_currentBranchName($branch_id)
	{

		$branch_name = "";

		$branch_query = $this->db->query("SELECT id_branch, name FROM branch WHERE id_branch = $branch_id");

		if ($branch_query->num_rows() > 0) {

			$branch_name = $branch_query->row()->name;
		}

		return $branch_name;
	}

	function get_currentBranches($record_id)
	{

		$record_id = ($record_id == NULL) ? -1 : $record_id;

		$strData = "<option value='' ";

		$strData .= $record_id == -1 ? "selected='selected'" : "";

		$strData .= ">- SELECT -</option>";

		$resultset = $this->db->query("SELECT id_branch, name FROM branch WHERE active = 1 ORDER BY name");

		foreach ($resultset->result() as $row) {

			$strData .= "<option value='" . $row->id_branch . "' ";

			$strData .= ($record_id == $row->id_branch) ? "selected='selected'" : "";

			$strData .= ">" . $row->name . "</option>";
		}

		$resultset->free_result();

		return $strData;
	}



	function code_number_generator($id_branch, $metal_type, $is_eda)

	{

		$IsMetalForBilling = $this->get_ret_settings('is_metal_for_billing');

		$lastno = $this->get_last_code_no($id_branch, $metal_type, $is_eda);



		if ($lastno != NULL) {

			if ($IsMetalForBilling == 1) {
				$code_det       = explode('-', $lastno);
				$LastBillNo      = end($code_det);
			} else {
				$LastBillNo      = $lastno;
			}

			$number = (int) $LastBillNo;
			$number++;
			$code_number = str_pad($number, 5, '0', STR_PAD_LEFT);
			return $code_number;
		} else {

			$code_number = str_pad('1', 5, '0', STR_PAD_LEFT);

			return $code_number;
		}
	}

	function get_last_code_no($id_branch, $metal_type, $is_eda)

	{

		$fin_year = $this->get_FinancialYear();

		$sql = "SELECT bill_no as lastBill_no
		FROM ret_billing

		where fin_year_code='" . $fin_year['fin_year_code'] . "'

		" . ($id_branch != '' && $id_branch > 0 ? " and id_branch=" . $id_branch . "" : '') . "
		" . ($is_eda != '' ? " and is_eda=" . $is_eda . "" : '') . "
		ORDER BY bill_id DESC LIMIT 1 FOR UPDATE";
		//print_r($sql);exit;
		return $this->db->query($sql)->row()->lastBill_no;
	}



	function get_payModes()

	{

		$sql = "SELECT * FROM payment_mode where show_in_pay = 1 ORDER BY sort_order";

		return $this->db->query($sql)->result_array();
	}

	//chit account

	function get_closed_accounts($SearchTxt, $id_customer, $id_scheme_acc,$id_branch)
	{

		$dCData=$this->getBranchDayClosingData($id_branch);

		$data = $this->db->query("SELECT sa.bonus_percent,c.mobile, sa.id_scheme_account as value, sa.id_scheme_account as label,



		sa.closing_balance, sa.is_closed, s.scheme_type, s.scheme_name, ifnull(s.flexible_sch_type, 0) as flexible_sch_type,

		sa.closing_amount as closing_amount, sa.closing_add_chgs, pay.paid_installments, s.total_installments, s.firstPayDisc_value, s.scheme_type,



		IFNULL(pay.paid_amount,0) as paid_amount,s.rate_type,

		IFNULL(sa.additional_benefits,0) as additional_benefits, IFNULL(sa.benefit,0) as sch_benefit, IFNULL(pay.cash_pay, 0) as cash_pay,s.is_wast_and_mc_benefit_apply,IFNULL(sa.closing_weight,0) as closing_weight,s.code as scheme_short_code, s.wgt_convert as wgt_convert, scheme_type as new_scheme_type

				from scheme_account sa

				left join customer c on c.id_customer=sa.id_customer

				left join scheme s on s.id_scheme = sa.id_scheme

				LEFT JOIN (select SUM(p.payment_amount) as paid_amount, IFNULL(SUM(cp.cash_pay),0) + IFNULL(SUM(chit_adv_adj_cash.chit_cash_adv_adj),0) as cash_pay,sa.id_scheme_account,



				IFNULL(IF(sa.is_opening = 1,

				            IFNULL(sa.paid_installments,0) + IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0),

				            if((s.scheme_type = 1 OR s.scheme_type = 3) and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0) as paid_installments

				FROM payment p

				LEFT JOIN (SELECT SUM(IFNULL(pmd.payment_amount,0)) AS cash_pay, pmd.id_payment FROM `payment_mode_details` AS pmd LEFT JOIN payment p ON p.id_payment = pmd.id_payment WHERE pmd.payment_mode = 'CSH' AND pmd.payment_status = 1 AND DATE(p.custom_entry_date) != DATE('".$dCData['entry_date']."') GROUP BY pmd.id_payment) AS cp ON cp.id_payment = p.id_payment

				LEFT JOIN (
					SELECT IFNULL(SUM(cash_utilized_amt), 0) AS chit_cash_adv_adj, p.id_payment 
					FROM `ret_advance_utilized` au 
					LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = au.id_issue_receipt
					LEFT JOIN payment p ON p.id_payment = au.id_payment 
					WHERE au.adjusted_for = 2 AND DATE(p.custom_entry_date) != DATE('".$dCData['entry_date']."') AND au.id_payment IS NOT NULL
					GROUP BY p.id_payment
				) AS chit_adv_adj_cash ON chit_adv_adj_cash.id_payment = p.id_payment

				left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account

				left join scheme s on s.id_scheme=sa.id_scheme

				where p.payment_status=1 GROUP BY p.id_scheme_account) as pay on pay.id_scheme_account = sa.id_scheme_account

				WHERE sa.id_scheme_account = " . $id_scheme_acc . " and sa.is_closed = 1 and sa.is_utilized = 0



				");

		// echo $this->db->last_query();exit;

		// " . ($id_customer != '' ? " and sa.id_customer=" . $id_customer . "" : '') . "

		return $data->result_array();
	}

	//Adv Adj



	function get_advance_details($bill_cus_id, $is_eda, $id_branch = 0)

	{

		$dayclose_date = "";

		if($id_branch > 0) {

			$dCData=$this->getBranchDayClosingData($id_branch);

			$dayclose_date = $dCData['entry_date'];
		}

		$data = $this->db->query("SELECT IFNULL(ir.amount,0) as total_amount, IFNULL(irp.cash_pay,0) AS total_cash_pay,

		IFNULL(ir.amount-IFNULL(advance.amount,0)-IFNULL(chit_adj.chit_utilized_amount,0)-IFNULL(advance_adjusted.amount,0)-IFNULL(refund.refund_amount,0)-IFNULL(adv_trns.transfer_amount,0),0)as amount,

		ir.id_issue_receipt,ir.bill_no, 

        IF((IFNULL(irp.cash_pay,0) + IFNULL(adv_trns_iss.transfer_cash_amt,0) -IFNULL(advance.amount,0)-IFNULL(chit_adj.chit_utilized_amount,0)-IFNULL(advance_adjusted.amount,0)-IFNULL(refund.refund_amount,0)-IFNULL(adv_trns.transfer_amount,0)) > 0, IFNULL(irp.cash_pay,0) + IFNULL(adv_trns_iss.transfer_cash_amt,0) -IFNULL(advance.amount,0)-IFNULL(chit_adj.chit_utilized_amount,0)-IFNULL(advance_adjusted.amount,0)-IFNULL(refund.refund_amount,0)-IFNULL(adv_trns.transfer_amount,0), 0) as cash_pay,

        IFNULL(adv_trns.transfer_amount,0) transfer_amount, (IFNULL(ir.weight,0) - IFNULL(weight.bal_wt,0)) as weight, CASE when ir.weight>0 THEN ir.rate_calc ELSE 0 END as rate_calc, ir.bill_date, '".$dayclose_date."' AS dayclose_date, 
		-- IF('".$dayclose_date."' != '', IF(DATE(ir.bill_date) = '".$dayclose_date."', 1, 0), 0) AS is_currentday_adv
		" . (!empty($dayclose_date) ? "IF(DATE(ir.bill_date) = '".$dayclose_date."', 1, 0)" : "0") . " AS is_currentday_adv

        from ret_issue_receipt ir

        left join (select sum(u.utilized_amt) as amount,ir.id_issue_receipt

                    from ret_issue_receipt as ir

                    left JOIN ret_advance_utilized as u on u.id_issue_receipt=ir.id_issue_receipt

                    LEFT JOIN ret_billing bill on bill.bill_id=u.bill_id

                    where bill.bill_status=1

                    GROUP by ir.id_issue_receipt) as advance on advance.id_issue_receipt=ir.id_issue_receipt

		LEFT JOIN (SELECT SUM(IFNULL(payment_amount,0)) AS cash_pay, id_issue_rcpt FROM ret_issue_rcpt_payment WHERE payment_mode = 'Cash' AND payment_status = 1 GROUP BY id_issue_rcpt) AS irp ON irp.id_issue_rcpt = ir.id_issue_receipt

        left join (select sum(u.utilized_amt) as chit_utilized_amount,ir.id_issue_receipt

                    from ret_issue_receipt as ir

                    left JOIN ret_advance_utilized as u on u.id_issue_receipt=ir.id_issue_receipt

                    LEFT JOIN payment p on p.id_payment=u.id_payment

                    where p.payment_status=1

                    GROUP by ir.id_issue_receipt) as chit_adj on chit_adj.id_issue_receipt=ir.id_issue_receipt



         left join (select sum(adj.adjusted_amt) as amount,adj.receipt_for

                    FROM ret_issue_receipt_advance_adj adj

                    LEFT JOIN ret_issue_receipt ir ON ir.id_issue_receipt=adj.id_issue_receipt

                    where ir.bill_status=1

                    GROUP by adj.receipt_for) as advance_adjusted on advance_adjusted.receipt_for=ir.id_issue_receipt



        LEFT JOIN (select a.refund_receipt,IFNULL(SUM(a.refund_amount),0) as refund_amount

                   From ret_advance_refund a

                   LEFT JOIN ret_issue_receipt r on r.id_issue_receipt=a.id_issue_receipt

                   Where r.bill_status=1

                   group by a.refund_receipt) as refund on refund.refund_receipt=ir.id_issue_receipt

		LEFT JOIN (SELECT IFNULL(sum(u.adj_weight),0) as bal_wt ,ir.id_issue_receipt FROM ret_issue_receipt ir
					LEFT JOIN ret_advance_utilized u ON ir.id_issue_receipt=u.id_issue_receipt
					LEFT JOIN ret_billing b on b.bill_id=u.bill_id
                   WHERE b.bill_status=1
					GROUP BY ir.id_issue_receipt) weight ON weight.id_issue_receipt=ir.id_issue_receipt



        LEFT JOIN(SELECT trn.transfer_receipt_id,IFNULL(SUM(trn.transfer_amount),0) as transfer_amount



		FROM ret_advance_transfer trn



		LEFT JOIN ret_issue_receipt ir ON ir.id_issue_receipt = trn.transfer_receipt_id



		LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = trn.transfer_receipt_id



		Where r.bill_status=1



		GROUP BY trn.transfer_receipt_id) as adv_trns ON adv_trns.transfer_receipt_id = ir.id_issue_receipt

		LEFT JOIN(
			
			SELECT trn.id_issue_receipt,IFNULL(SUM(trn.transfer_amount),0) as transfer_amount, IFNULL(SUM(trn.transfer_cash_amt),0) as transfer_cash_amt

			FROM ret_advance_transfer trn

			LEFT JOIN ret_issue_receipt ir ON ir.id_issue_receipt = trn.transfer_receipt_id

			WHERE ir.bill_status=1

			GROUP BY trn.id_issue_receipt
		
		) as adv_trns_iss ON adv_trns_iss.id_issue_receipt = ir.id_issue_receipt

        where ir.id_customer=" . $bill_cus_id . " and ir.type=2 and ir.bill_status=1 AND (ir.receipt_type=2 or ir.receipt_type=3 or ir.receipt_type=4 or ir.receipt_type=5 or ir.receipt_type=7)



        " . (isset($is_eda) && $is_eda != '' ? 'and ir.is_eda=' . $is_eda : '') . "

        group by ir.id_issue_receipt HAVING amount>0 or weight>0");

		//print_r($this->db->last_query());exit;

		return $data->result_array();
	}



	//Adv Adj

	function getBillData($bill_no, $billType, $id_branch, $fin_year, $metal_type, $is_eda, $id_customer = '')

	{

		// 1-Sales, 2-Sales&Purchase, 3-Sales,purchase&Return, 4-Purchase, 5-Order Advance, 6-Advance,7-Sales Return

		$dCData=$this->getBranchDayClosingData($id_branch);

		$return_data = array("item_details" => array(), "old_matel_details" => array(), "bill_details" => array(), "due_amount" => 0, "paid_amount" => 0);

		$items_query = $this->db->query("SELECT bill_items.bill_det_id, bill.bill_id, bill_items.item_type,bill_items.status,

		ifnull(bill_items.product_id, '') as product_id, ifnull(bill_items.tag_id, '') as tag_id, bill_items.esti_item_id,esti_id ,

		ifnull(bill_items.design_id, '') as design_id, ifnull(pro.hsn_code,'') as hsn_code,

		bill_items.uom, bill_items.piece,IFNULL(concat(s.value ,s.name),'-')as size,

		ifnull(bill_items.less_wt,'') as less_wt, bill_items.net_wt, bill_items.gross_wt,

		bill_items.calculation_based_on, bill_items.wastage_percent, bill_items.mc_value, bill_items.mc_type,

		bill_items.item_cost, ifnull(product_short_code, '-') as product_short_code,

		ifnull(product_name, '-') as product_name, bill_items.is_partial_sale,bill_items.bill_discount as discount,bill_items.item_total_tax,

		ifnull(design_code, '-') as design_code,

		ifnull(design_name, '') as design_name, pur.purity as purname,

		pro.tgrp_id as tax_group_id , tgrp_name, ifnull(c.id_metal,'') as metal_type,

		ifnull(des.fixed_rate,0) as fixed_rate,

		if(bill_items.tag_id != null,stn_price,stn_amount) as stone_price,

		if(bill_items.tag_id != null,stn_wgt,stn_wt) as stn_wgt,

		if(bill_items.tag_id != null,othermat_amount,other_mat_price) as othermat_amount,

		if(bill_items.tag_id != null,othermat_wt,other_mat_wgt) as othermat_wt,concat(cus.firstname,' ',cus.mobile) as cus_name,v.village_name,

		if(cus.is_vip=1,'Yes','No') as vip,cus.mobile,

		(select count(sa.id_scheme_account) from scheme_account sa left join customer cust on cust.id_customer=sa.id_customer) as accounts,cus.id_customer

		FROM ret_billing as bill

		LEFT JOIN ret_bill_details as bill_items ON bill_items.bill_id = bill.bill_id

		LEFT JOIN (SELECT esti_id,est_item_id from ret_estimation_items where bil_detail_id is not null and purchase_status=1) as est_itms ON est_itms.est_item_id = bill_items.esti_item_id

		LEFT JOIN (SELECT bill_det_id,sum(price) as stn_price,sum(wt) as stn_wgt FROM `ret_billing_item_stones` GROUP by bill_det_id) as stn_detail ON stn_detail.bill_det_id = bill_items.bill_det_id

		LEFT JOIN (SELECT bill_det_id,sum(price) as other_mat_price,sum(price) as other_mat_wgt FROM `ret_billing_item_other_materials` GROUP by bill_det_id) as est_oth_mat ON est_oth_mat.bill_det_id = bill_items.bill_det_id

		LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(wt) as stn_wt FROM `ret_taging_stone` GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = bill_items.tag_id

		LEFT JOIN (SELECT tag_id,sum(price) as othermat_amount,sum(wt) as othermat_wt FROM `ret_taging_other_materials` GROUP by tag_id) as tag_other_mat ON tag_other_mat.tag_id = bill_items.tag_id

		LEFT JOIN ret_product_master as pro ON pro.pro_id = bill_items.product_id

		LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id

		LEFT JOIN metal mt on mt.id_metal=c.id_metal

		LEFT JOIN ret_design_master as des ON des.design_no = bill_items.design_id

		LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id

		LEFT JOIN customer cus on cus.id_customer=bill.bill_cus_id

		LEFT JOIN village v on v.id_village=cus.id_village

		LEFT JOIN ret_purity pur   ON pur.id_purity = bill_items.purity

		LEFT JOIN ret_size s   ON s.id_size = bill_items.size

		WHERE bill.is_eda=" . $is_eda . " " . (!empty($id_branch) ? " and bill.id_branch=" . $id_branch . '' : '') . " and bill.sales_ref_no ='" . $bill_no . "'  AND bill_items.bill_det_id IS NOT NULL and bill.bill_status=1

		" . ($metal_type != '' ? " and mt.id_metal=" . $metal_type . "" : '') . " 
		
		" . ($id_customer != '' ? " and bill.bill_cus_id=" . $id_customer . "" : '') . "

		and bill.fin_year_code=" . $fin_year . " ");

		//print_r($this->db->last_query());exit;

		$return_data["item_details"] = $items_query->result_array();



		$billing_details = $this->db->query("SELECT (b.tot_bill_amount-b.tot_amt_received) as due_amount,b.tot_amt_received,b.bill_id

        FROM ret_billing b

        WHERE b.is_credit = 1 and b.bill_status = 1 and b.credit_status = 2

        " . (!empty($id_branch) ? " and b.id_branch=" . $id_branch . ' AND' : '') . " b.sales_ref_no ='" . $bill_no . "'

        and b.fin_year_code=" . $fin_year . " and b.is_eda=".$is_eda."");



		$result = $billing_details->row_array();

		$bill_id = $this->db->query("SELECT bill_id FROM ret_billing where sales_ref_no =".$bill_no."");
		$credit_collection = $this->get_credit_collection_details($bill_id->row()->bill_id);




		if ($result) {
			$credit_paid_amt = $this->get_credit_pay_amount($result['bill_id']);

		
			$returned_sql = $this->db->query("SELECT IFNULL(SUM(d.item_cost),0) as total_returned
				FROM ret_bill_return_details r
				LEFT JOIN ret_bill_details d ON d.bill_det_id = r.ret_bill_det_id
				LEFT JOIN ret_billing rb ON rb.bill_id = r.bill_id
				WHERE d.bill_det_id IS NOT NULL
				AND rb.bill_status = 1
				AND rb.make_as_advance = 1
				AND r.ret_bill_id = " . $result['bill_id']);
			$total_returned = $returned_sql->row()->total_returned;

			// $return_data["due_amount"] = $result['due_amount'] - $credit_paid_amt;
			
			$return_data["due_amount"] = $result['due_amount'] - $credit_collection - $total_returned;
		}



		$pay_sql = $this->db->query("SELECT IFNULL(SUM(pay.payment_amount),0) as paid_amount

        FROM ret_billing_payment pay

        LEFT JOIN ret_billing b ON b.bill_id = pay.bill_id

        WHERE b.bill_status = 1

        " . (!empty($id_branch) ? " and b.id_branch=" . $id_branch . ' AND' : '') . " b.sales_ref_no ='" . $bill_no . "'

        and b.fin_year_code=" . $fin_year . " ");

		$return_data['paid_amount'] = $pay_sql->row()->paid_amount;

   //
		$purchase_amt = $this->db->query("SELECT SUM(p.rate) as purchase_amount FROM ret_bill_old_metal_sale_details p

		LEFT JOIN ret_billing b on b.bill_id = p.bill_id

		where b.bill_status=1 and b.is_eda=".($is_eda)." and b.sales_ref_no=".($bill_no)." ");

		$return_data['purchase_amt']=$purchase_amt->row()->purchase_amount;

		$chit_utilized = $this->db->query("SELECT SUM(u.utilized_amt) as utilzed_amt FROM ret_billing_chit_utilization u

		LEFT JOIN ret_billing b on b.bill_id = u.bill_id

		where b.bill_status=1 and b.sales_ref_no=".($bill_no)." ");

		$return_data['chit_utilized']=$chit_utilized->row()->utilzed_amt;






		$return_data['paid_amount'] = $pay_sql->row()->paid_amount + $purchase_amt->row()->purchase_amount + $chit_utilized->row()->utilzed_amt +$credit_collection;

		$tot_bill_amt = $this->db->query("SELECT
		SUM(d.item_cost) as tot_cost FROM ret_bill_details d
		left join ret_billing b on b.bill_id = d.bill_id
		where b.bill_status = 1 and b.is_eda=".($is_eda)."
        " . (!empty($id_branch) ? " and b.id_branch=" . $id_branch . ' AND' : '') . " b.sales_ref_no ='" . $bill_no . "'

		");


		$return_data['tot_bill_amt'] = $tot_bill_amt->row()->tot_cost;


		$cash_pay_sql = $this->db->query("SELECT IFNULL(pay.cash_payment,0) + IFNULL(bill_adv_adj_cash.bill_cash_adv_adj,0) + IFNULL(chit_cash_utilized,0) as cash_paid_amount

		FROM ret_billing b 

        LEFT JOIN (
			SELECT IFNULL(payment_amount, 0) AS cash_payment, bill_id
			FROM ret_billing_payment WHERE payment_mode = 'Cash' 
		) pay ON pay.bill_id = b.bill_id
		
		LEFT JOIN (
			SELECT IFNULL(SUM(cash_utilized_amt), 0) AS bill_cash_adv_adj, au.bill_id
			FROM `ret_advance_utilized` au 
			LEFT JOIN ret_billing b ON b.bill_id = au.bill_id 
			WHERE b.bill_status =1 AND au.adjusted_for = 1 AND DATE(b.bill_date) != DATE('".$dCData['entry_date']."') AND au.bill_id IS NOT NULL
			GROUP BY au.bill_id
		) AS bill_adv_adj_cash ON bill_adv_adj_cash.bill_id = b.bill_id

		LEFT JOIN (
			SELECT IFNULL(pay.cash_pay, 0) AS chit_cash_utilized, cu.bill_id 
			FROM `ret_billing_chit_utilization` cu
			LEFT JOIN ret_billing b ON b.bill_id = cu.bill_id 
			LEFT JOIN (
				SELECT 
					IFNULL(SUM(cp.cash_pay),0) + IFNULL(SUM(chit_adv_adj_cash.chit_cash_adv_adj),0) as cash_pay, p.id_scheme_account
				FROM payment p
				LEFT JOIN (SELECT SUM(IFNULL(pmd.payment_amount,0)) AS cash_pay, pmd.id_payment FROM `payment_mode_details` AS pmd LEFT JOIN payment p ON p.id_payment = pmd.id_payment WHERE pmd.payment_mode = 'CSH' AND pmd.payment_status = 1 AND DATE(p.custom_entry_date) != DATE('".$dCData['entry_date']."') GROUP BY pmd.id_payment) AS cp ON cp.id_payment = p.id_payment
				LEFT JOIN (
						SELECT IFNULL(SUM(cash_utilized_amt), 0) AS chit_cash_adv_adj, p.id_payment 
						FROM `ret_advance_utilized` au 
						LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = au.id_issue_receipt
						LEFT JOIN payment p ON p.id_payment = au.id_payment 
						WHERE au.adjusted_for = 2 AND DATE(p.custom_entry_date) != DATE('".$dCData['entry_date']."') AND au.id_payment IS NOT NULL
						GROUP BY p.id_payment
				) AS chit_adv_adj_cash ON chit_adv_adj_cash.id_payment = p.id_payment
				where p.payment_status=1 GROUP BY p.id_scheme_account
			) as pay on pay.id_scheme_account = cu.scheme_account_id
			WHERE b.bill_status =1 AND DATE(b.bill_date) != DATE('".$dCData['entry_date']."')
		) AS bill_chit_utilized ON bill_chit_utilized.bill_id = b.bill_id

        WHERE b.bill_status = 1 AND DATE(b.bill_date) != DATE('".$dCData['entry_date']."')

        ".(!empty($id_branch)? " AND b.id_branch=".$id_branch :'')." AND b.sales_ref_no ='".$bill_no."'

        and b.fin_year_code=".$fin_year." ");

        $cash_paid_amount = $cash_pay_sql->row()->cash_paid_amount;


		$ret_cash_sql = $this->db->query("SELECT 
											
											IFNULL(SUM(brd.ret_cash_paid), 0) AS ret_cash_paid
											
										FROM `ret_bill_return_details` brd
										
										LEFT JOIN ret_billing b ON b.bill_id = brd.bill_id

										LEFT JOIN ret_billing br ON br.bill_id = brd.ret_bill_id 

										WHERE b.bill_status = 1 ".(!empty($id_branch)? " AND br.id_branch=".$id_branch :'')." AND br.sales_ref_no ='".$bill_no."'");

		$return_data['ret_cash_paid'] = $ret_cash_sql->row()->ret_cash_paid;

		$return_data['cash_paid_amount'] = round($cash_paid_amount - $return_data['ret_cash_paid'],2);



		return $return_data;
	}



	function getreturnBillData($bill_no, $billType, $id_branch)

	{

		$bill = str_replace(',', ' OR bill.bill_no=', 'bill.bill_no=' . $bill_no);

		//print_r($bill);exit;

		// 1-Sales, 2-Sales&Purchase, 3-Sales,purchase&Return, 4-Purchase, 5-Order Advance, 6-Advance,7-Sales Return

		$return_data = array("item_details" => array(), "old_matel_details" => array(), "bill_details" => array());

		$items_query = $this->db->query("SELECT bill_items.bill_det_id, bill.bill_id, bill_items.item_type,bill_items.status,

		ifnull(bill_items.product_id, '') as product_id, ifnull(bill_items.tag_id, '') as tag_id,IFNULL(bill_items.esti_item_id,'') as esti_item_id,esti_id ,

		ifnull(bill_items.design_id, '') as design_id, ifnull(pro.hsn_code,'') as hsn_code,

		bill_items.size, bill_items.uom, bill_items.piece,

		ifnull(bill_items.less_wt,'') as less_wt, bill_items.net_wt, bill_items.gross_wt,

		bill_items.calculation_based_on, bill_items.wastage_percent, bill_items.mc_value, bill_items.mc_type,

		bill_items.item_cost, ifnull(product_short_code, '-') as product_short_code,

		ifnull(product_name, '-') as product_name, bill_items.is_partial_sale,bill_items.bill_discount as discount,bill_items.item_total_tax,

		ifnull(design_code, '-') as design_code,

		ifnull(design_name, '') as design_name, purity as purname,

		pro.tgrp_id as tax_group_id , tgrp_name, ifnull(c.id_metal,'') as metal_type,

		ifnull(des.fixed_rate,0) as fixed_rate,

		if(bill_items.tag_id != null,stn_price,stn_amount) as stone_price,

		if(bill_items.tag_id != null,stn_wgt,stn_wt) as stn_wgt,

		if(bill_items.tag_id != null,othermat_amount,other_mat_price) as othermat_amount,

		if(bill_items.tag_id != null,othermat_wt,other_mat_wgt) as othermat_wt,concat(cus.firstname,' ',cus.mobile) as cus_name,v.village_name,

		if(cus.is_vip=1,'Yes','No') as vip,cus.mobile,

		(select count(sa.id_scheme_account) from scheme_account sa left join customer cust on cust.id_customer=sa.id_customer) as accounts,

		bill_items.total_sgst,total_igst,total_cgst

		FROM ret_billing as bill

		LEFT JOIN ret_bill_details as bill_items ON bill_items.bill_id = bill.bill_id

		LEFT JOIN (SELECT esti_id,est_item_id from ret_estimation_items where bil_detail_id is not null and purchase_status=1) as est_itms ON est_itms.est_item_id = bill_items.esti_item_id

		LEFT JOIN (SELECT bill_det_id,sum(price) as stn_price,sum(wt) as stn_wgt FROM `ret_billing_item_stones` GROUP by bill_det_id) as stn_detail ON stn_detail.bill_det_id = bill_items.bill_det_id

		LEFT JOIN (SELECT bill_det_id,sum(price) as other_mat_price,sum(price) as other_mat_wgt FROM `ret_billing_item_other_materials` GROUP by bill_det_id) as est_oth_mat ON est_oth_mat.bill_det_id = bill_items.bill_det_id

		LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(wt) as stn_wt FROM `ret_taging_stone` GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = bill_items.tag_id

		LEFT JOIN (SELECT tag_id,sum(price) as othermat_amount,sum(wt) as othermat_wt FROM `ret_taging_other_materials` GROUP by tag_id) as tag_other_mat ON tag_other_mat.tag_id = bill_items.tag_id

		LEFT JOIN ret_product_master as pro ON pro.pro_id = bill_items.product_id

		LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id

		LEFT JOIN metal mt on mt.id_metal=c.id_metal

		LEFT JOIN ret_design_master as des ON des.design_no = bill_items.design_id

		LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id

		LEFT JOIN customer cus on cus.id_customer=bill.bill_cus_id

		LEFT JOIN village v on v.id_village=cus.id_village

		WHERE " . (!empty($id_branch) ? "bill.id_branch=" . $id_branch . ' AND' : '') . " (" . $bill . ")  AND bill_items.bill_det_id IS NOT NULL");

		// print_r($this->db->last_query());
		// exit;

		$return_data["item_details"] = $items_query->result_array();

		return $return_data;
	}







	function getCreditBillDetails($bill_no, $bill_type, $id_branch, $fin_year_code, $is_eda, $metal_type = '')
	{

		$return_data = array("bill_details" => array());
		$IsMetalForBilling = $this->get_ret_settings('is_metal_for_billing');
		$extra_join = "";
		$extra_select = "";
		if ($IsMetalForBilling == 1) {
			$extra_select = " ,c.id_metal ";
			$extra_join = " LEFT JOIN ret_bill_details bd ON bd.bill_id = b.bill_id
							LEFT JOIN ret_product_master p ON p.pro_id = bd.product_id
							LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id ";
		}

		$items_query = $this->db->query("Select b.bill_id,b.tot_bill_amount,b.tot_amt_received,b.bill_cus_id as id_customer,

            concat(cus.firstname,' ',cus.mobile) as cus_name,v.village_name,cus.mobile,

            if(cus.is_vip=1,'Yes','No') as vip,

            (select count(sa.id_scheme_account) from scheme_account sa left join customer cust on cust.id_customer=sa.id_customer) as accounts,
            IFNULL(cus.pan,'') as pan_no,IFNULL(ret.total_returned,0) as total_returned " . $extra_select . "
            from ret_billing b

            LEFT JOIN customer cus on cus.id_customer=b.bill_cus_id

		    LEFT JOIN village v on v.id_village=cus.id_village
			   
		    LEFT JOIN(SELECT IFNULL(SUM(d.item_cost),0) as total_returned, r.ret_bill_id
                     FROM ret_bill_return_details r
                     LEFT JOIN ret_bill_details d ON d.bill_det_id = r.ret_bill_det_id
                     LEFT JOIN ret_billing rb ON rb.bill_id = r.bill_id
                     WHERE d.bill_det_id IS NOT NULL
                     AND rb.bill_status = 1
                     AND rb.make_as_advance = 1
                     GROUP BY r.ret_bill_id) as ret ON ret.ret_bill_id = b.bill_id
			" . $extra_join . "
            where b.is_credit=1 AND b.bill_status = 1 and b.bill_type!=12 and b.credit_status=2 and b.sales_ref_no ='" . $bill_no . "' and b.bill_type!=8 and b.fin_year_code=" . $fin_year_code . "
            and b.is_eda=" . $is_eda . "
            " . ($id_branch != '' ? " and b.id_branch=" . $id_branch . "" : '') . " GROUP BY b.bill_id");







		// print_r($this->db->last_query());exit;

		$return_data['bill_details'] = $items_query->row_array();

		if (!empty($return_data['bill_details'])) {



			$paid_amount        =  $this->get_credit_pay_amount($return_data["bill_details"]['bill_id']);

			$old_metal_amount   =  $this->get_credit_old_metal_amount($return_data["bill_details"]['bill_id'], 8); //8-Credit collection

			$return_data['bill_details']['credit_pay_amount'] = $paid_amount + $old_metal_amount;
		}

		return $return_data;
	}



	function get_credit_old_metal_amount($bill_id)

	{

		$old_metal_query = $this->db->query("SELECT IFNULL(sum(s.rate),0) as amount

            FROM ret_billing b

            LEFT join  ret_bill_old_metal_sale_details s on s.bill_id=b.bill_id

            where b.bill_status=1 and b.ref_bill_id=" . $bill_id . "");

		return $old_metal_query->row()->amount;
	}



	function get_credit_pay_amount($bill_id)

	{

		$sql = "SELECT IFNULL(sum(tot_amt_received+credit_disc_amt),0) as paid_amount from ret_billing b where b.ref_bill_id=" . $bill_id . " and b.bill_status=1";

		$paid_amount = $this->db->query($sql)->row()->paid_amount;



		$chit_adj = $this->db->query("SELECT IFNULL(SUM(c.utilized_amt),0) as utilized_amt

            FROM ret_billing_chit_utilization c

            LEFT JOIN ret_billing b ON b.bill_id = c.bill_id

            WHERE b.bill_status = 1 AND b.ref_bill_id = " . $bill_id . "");

		$chit_adj_amt = $chit_adj->row_array();



		return $paid_amount + $chit_adj_amt['utilized_amt'];
	}



	function get_BillAmount($bill_id)

	{

		$sql = "SELECT b.tot_bill_amount,b.tot_amt_received,b.is_credit,b.round_off_amt from ret_billing b where b.bill_id=" . $bill_id . "";

		return $this->db->query($sql)->row_array();
	}



	function max_metalrate()

	{

		$is_branchwise_rate = $this->session->userdata('is_branchwise_rate');

		$id_branch = $this->session->userdata('id_branch');

		$sql = "select m.goldrate_22ct,m.goldrate_14ct,m.goldrate_18ct,m.goldrate_9ct,m.silverrate_1gm from  metal_rates m" . ($is_branchwise_rate == 1 && $id_branch != '' ? " left join branch_rate br on br.id_metalrate=m.id_metalrates where br.id_branch=" . $id_branch . "" : '') . "";

		// print_r($sql);exit;

		return $this->db->query($sql)->row_array();
	}



	function getBilling_details($from_date, $to_date, $id_branch, $bill_cus_id, $bill_type)

	{

		$items_query = $this->db->query("SELECT b.bill_no,c.mobile,c.firstname,b.tot_bill_amount

    		FROM ret_billing b

    		LEFT JOIN customer c on c.id_customer=b.bill_cus_id

    		where  b.bill_cus_id=" . $bill_cus_id . " and b.id_branch=" . $id_branch . " and date(b.bill_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "' and b.tot_bill_amount>0" . ($bill_type == 8 ? " and b.is_credit=1 and b.credit_status=2" : " and b.bill_type=1") . "");

		//print_r($this->db->last_query());exit;

		return $items_query->result_array();
	}

	function getCompanyDetails($id_branch="")

	{

		if ($id_branch == '') {

			$sql = $this->db->query("Select  c.id_company,c.company_name,IF(c.gst_number IS NOT NULL AND c.gst_number != '', c.gst_number, b.gst_number) AS gst_number,c.short_code,c.pincode,c.mobile,c.whatsapp_no,c.phone,c.email,c.website,c.address1,c.address2,c.id_country,c.id_state,c.id_city,ct.name as city,s.name as state,cy.name as country,cs.currency_symbol,cs.currency_name,cs.mob_code,cs.mob_no_len,c.mail_server,c.mail_password,c.send_through,c.mobile1,c.phone1,c.smtp_user,c.smtp_pass,c.smtp_host,c.server_type,cs.login_branch,
			s.state_code

			from company c

			join branch b

			join chit_settings cs

			left join country cy on (c.id_country=cy.id_country)

			left join state s on (c.id_state=s.id_state)

			left join city ct on (c.id_city=ct.id_city)");
		} else {

			$sql = $this->db->query("select b.name, b.name as company_name, b.address1, b.address2, b.address1 as company_address,

cy.name as country, ct.name as city, ct.name as company_city, s.name as state, b.pincode, b.pincode as company_pincode, s.id_state, s.state_code, cy.id_country, IF(b.gst_number IS NOT NULL AND b.gst_number != '', b.gst_number, c.gst_number) AS gst_number, b.mobile, IFNULL(b.email, c.email) as email, '' as pan_no, IFNULL(b.phone, c.phone) as phone
				from branch b

				join company c

				left join country cy on (b.id_country=cy.id_country)

				left join state s on (b.id_state=s.id_state)

				left join city ct on (b.id_city=ct.id_city)

				where b.id_branch=" . $id_branch . "");
		}

		$result = $sql->row_array();

		return $result;
	}





	// issue and receipt

	function get_account_head()

	{

		$sql = $this->db->query("SELECT a.name,a.id_acc_head

		 FROM ret_account_head a where a.status=1");

		return  $sql->result_array();
	}

	function get_borrower_details($SearchTxt, $id_branch, $issue_to, $receipt_to, $receipt_type, $is_eda)

	{

		$return_data = array();

		if ($issue_to == 1 || ($receipt_type == 8 && $receipt_to == 1)) {

			$data = $this->db->query("select e.id_employee as value,concat(e.firstname,'-',e.mobile) as label,e.mobile,e.firstname as barrower_name, e.mobile as mobile ,e.id_branch

			from employee e

			where (e.mobile like '%" . $SearchTxt . "%' OR e.firstname like '%" . $SearchTxt . "%' )"  . "");



			$return_data = $data->result_array();
		} else if ($issue_to == 4 || ($receipt_type == 8 && $receipt_to == 2)) {

			$data = $this->db->query(" SELECT k.firstname,k.id_karigar as value, concat(k.firstname,'-',IFNULL(k.contactno1,'')) as label,k.contactno1 as mobile,k.firstname as barrower_name FROM ret_karigar k

			where ( k.contactno1 like '%" . $SearchTxt . "%' OR k.firstname like '%" . $SearchTxt . "%' )

			");



			$return_data = $data->result_array();
		} else {

			$data = $this->db->query("select c.id_customer as value,concat(c.firstname,'-',c.mobile) as label,c.mobile,c.firstname as barrower_name,w.id_ret_wallet,

			c.mobile as mobile,IFNULL(c.pan,'') as pan,IFNULL(a.id_country,'') as id_country,IFNULL(c.aadharid,'') as aadhar_no,IFNULL(c.passport_no,'') as passport_no,

			IFNULL(c.driving_license_no,'') as driving_license_no

			From customer c

			LEFT JOIN  ret_wallet w on w.id_customer=c.id_customer

			LEFT JOIN address a on a.id_customer=c.id_customer

			where c.mobile like '%" . $SearchTxt . "%' OR c.firstname like '%" . $SearchTxt . "%'

			group by c.id_customer");



			$cus_details = $data->result_array();



			foreach ($cus_details as $cus) {

				$return_data[] = array(

					'value'           => $cus['value'],

					'label'           => $cus['label'],

					'barrower_name'   => $cus['barrower_name'],

					'id_ret_wallet'   => $cus['id_ret_wallet'],

					'mobile'          => $cus['mobile'],

					'pan'             => $cus['pan'],

					'country'		  => $cus['id_country'],

					'aadhar_no'		  => $cus['aadhar_no'],

					'passport_no'		  => $cus['passport_no'],

					'driving_license_no'  => $cus['driving_license_no'],

					'wallet_det'      => $this->get_cus_advance_details($cus['value']),

					'advance_details' => $this->get_receipt_refund($cus['value'], $is_eda),

				);
			}
		}

		return $return_data;
	}



	function get_cus_advance_details($bill_cus_id)

	{

		$data = $this->db->query("SELECT w.amount as amount,w.id_ret_wallet

        from ret_wallet w

        where w.id_customer=" . $bill_cus_id . "

        Having amount>0");

		//print_r($this->db->last_query());exit;

		return $data->row_array();
	}



	//advance refund

	function get_receipt_refund($bill_cus_id, $is_eda)

	{

		$sql = $this->db->query("SELECT (ir.amount-IFNULL(advance.amount,0)-IFNULL(refund.refund_amount,0)-IFNULL(chit_adj.chit_utilized_amount,0)-IFNULL(advance_adjusted.amount,0)-IFNULL(adv_trns.transfer_amount,0))as amount,IFNULL(adv_trns.transfer_amount,0) as transfer_amount,

		ir.id_issue_receipt,ir.bill_no

        from ret_issue_receipt ir



        left join (select sum(u.utilized_amt) as amount,ir.id_issue_receipt

                    from ret_issue_receipt as ir

                    left JOIN ret_advance_utilized as u on u.id_issue_receipt=ir.id_issue_receipt

                    LEFT JOIN ret_billing bill on bill.bill_id=u.bill_id

                    where bill.bill_status=1

                    GROUP by ir.id_issue_receipt) as advance on advance.id_issue_receipt=ir.id_issue_receipt



        left join (select sum(u.utilized_amt) as chit_utilized_amount,ir.id_issue_receipt

                    from ret_issue_receipt as ir

                    left JOIN ret_advance_utilized as u on u.id_issue_receipt=ir.id_issue_receipt

                    LEFT JOIN payment p on p.id_payment=u.id_payment

                    where p.payment_status=1 and u.id_payment is not null

                    GROUP by ir.id_issue_receipt) as chit_adj on chit_adj.id_issue_receipt=ir.id_issue_receipt



         left join (select sum(adj.adjusted_amt) as amount,adj.receipt_for

                    FROM ret_issue_receipt_advance_adj adj

                    LEFT JOIN ret_issue_receipt ir ON ir.id_issue_receipt=adj.id_issue_receipt

                    where ir.bill_status=1

                    GROUP by adj.receipt_for) as advance_adjusted on advance_adjusted.receipt_for=ir.id_issue_receipt



        LEFT JOIN (select a.refund_receipt,IFNULL(SUM(a.refund_amount),0) as refund_amount

                   From ret_advance_refund a

                   LEFT JOIN ret_issue_receipt r on r.id_issue_receipt=a.id_issue_receipt

                   Where r.bill_status=1

                   group by a.refund_receipt) as refund on refund.refund_receipt=ir.id_issue_receipt


			LEFT JOIN(SELECT trn.transfer_receipt_id,IFNULL(SUM(trn.transfer_amount),0) as transfer_amount

					FROM ret_advance_transfer trn

					LEFT JOIN ret_issue_receipt ir ON ir.id_issue_receipt = trn.transfer_receipt_id

					LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = trn.transfer_receipt_id

					Where r.bill_status=1

					GROUP BY trn.transfer_receipt_id) as adv_trns ON adv_trns.transfer_receipt_id = ir.id_issue_receipt



        where ir.id_customer=" . $bill_cus_id . " and ir.bill_status=1 AND (ir.receipt_type=2 or ir.receipt_type=3 or ir.receipt_type=4 or ir.receipt_type=5 or ir.receipt_type=7)

        and ir.is_eda=" . $is_eda . "

        Having amount>0");

		//print_r($this->db->last_query());exit;

		return $sql->result_array();
	}

	//advance refund



	function getCreditBill($searchTxt, $id_branch)

	{

		$data = $this->db->query("SELECT r.bill_no as label,r.id_issue_receipt as value,r.issue_to,r.id_customer,r.id_employee,r.amount,

			(Select ifnull(sum(rct.amount),0)  FROM ret_issue_receipt rct where rct.receipt_for=" . $searchTxt . ") as paid_amount

			FROM ret_issue_receipt r

			where r.is_collect=0 and (r.bill_no LIKE '%" . $searchTxt . "%' OR r.id_issue_receipt LIKE '%" . $searchTxt . "%') " . ($id_branch != '' ? "and r.id_branch=" . $id_branch . " " : '') . "");

		//print_r($this->db->last_query());exit;

		return  $data->result_array();
	}

	function get_retWallet_details($id_customer)

	{

		$data = $this->db->query("SELECT id_ret_wallet,id_customer FROM ret_wallet w where w.id_customer=" . $id_customer . "");

		if ($data->num_rows() > 0) {

			return array('status' => TRUE, 'id_ret_wallet' => $data->row('id_ret_wallet'));
		} else {

			return array('status' => FALSE, 'id_ret_wallet' => '');
		}
	}


/*
	function ajax_getReceiptlist($data)

	{

		$profile_settings = $this->get_profile_settings($this->session->userdata('profile'));

		if ($_POST['dt_range'] != '') {

			$dateRange = explode('-', $_POST['dt_range']);

			$from = str_replace('/', '-', $dateRange[0]);

			$to = str_replace('/', '-', $dateRange[1]);

			$d1 = date_create($from);

			$d2 = date_create($to);

			$FromDt = date_format($d1, "Y-m-d");

			$ToDt = date_format($d2, "Y-m-d");
		}



		$sql = $this->db->query("SELECT r.bill_no,b.name,if(r.type=1,'Issue','Receipt') as type,IFNULL(e.firstname,'-') as emp_name,IFNULL(c.firstname,'-') as cus_name,IFNULL(r.amount,0) as amount,IFNULL(adv.utilized_amt,0) as utilized_amt,r.bill_status as status,

			date_format(r.bill_date,'%d-%m-%Y') as date_add,r.issue_to,r.id_issue_receipt,IFNULL(r.weight,0) as weight,IF(r.bill_status=1,'Success','Cancelled') as bill_status,if(date(d.entry_date)=date(r.bill_date),'1','0') as allow_cancel,IF(r.amount = adv.utilized_amt,'1','0') as current_status,

			r.receipt_type,IFNULL(refund.refund_amount,0) as refund_amount,IFNULL(c.mobile,'') as mobile,

			IF(r.receipt_type = 1, 'Credit Collection',IF(r.receipt_type = 2, 'Advance',IF(r.receipt_type = 3, 'Advance Deposit',IF(r.receipt_type = 4, 'Opening Blc',IF(r.receipt_type = 5, 'Order Advance to General Advance',

            IF(r.receipt_type = 6, 'Chit Close',IF(r.receipt_type = 7, 'Advance Transfer', ''))))))) AS receipt_type,

			IFNULL(e.firstname,'-') as emp_name

			FROM ret_issue_receipt r

			left join ret_account_head a on r.issue_to=a.id_acc_head

			LEFT join customer c on c.id_customer=r.id_customer

			LEFT JOIN employee e on e.id_employee=r.emp_id

			LEFT JOIN ret_day_closing d on d.id_branch=r.id_branch

			LEFT JOIN branch b on b.id_branch=r.id_branch

            LEFT JOIN (SELECT b.bill_id,sum(IFNULL(adv.utilized_amt,0)) utilized_amt,adv.id_issue_receipt FROM ret_billing b

                LEFT JOIN ret_advance_utilized adv ON adv.bill_id=b.bill_id

                WHERE b.bill_status=1 AND adv.bill_id is NOT null

                GROUP BY adv.id_issue_receipt) adv ON adv.id_issue_receipt=r.id_issue_receipt



            LEFT JOIN (select a.refund_receipt,IFNULL(SUM(a.refund_amount),0) as refund_amount

                   From ret_advance_refund a

                   LEFT JOIN ret_issue_receipt r on r.id_issue_receipt=a.id_issue_receipt

                   Where r.bill_status=1

                   group by a.refund_receipt) as refund on refund.refund_receipt=r.id_issue_receipt

			where r.type=2

			" . ($FromDt != '' ? 'and date(r.bill_date) BETWEEN "' . $FromDt . '" AND "' . $ToDt . '"' : '') . "

            " . ($data['id_branch'] != '' && $data['id_branch'] != 0 ? " and r.id_branch=" . $data['id_branch'] . "" : '') . "

            " . ($profile_settings['allow_bill_type'] == 3 ? " and (r.is_eda=1 OR r.is_eda=2)" : ($profile_settings['allow_bill_type'] == 1 ? " and r.is_eda=1" : " and r.is_eda=2")) . "

            ");

		//print_r($this->db->last_query());exit;

		$return_data =  $sql->result_array();

		if ($data['id_branch'] != '' && $data['id_branch'] != 0) {

			$dayClose = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing where id_branch=" . $data['id_branch']);

			$cur_entry_date = $dayClose->row()->entry_date;

			if ($profile_settings['allow_bill_type'] == 2) {

				if ($FromDt != $cur_entry_date) {

					$return_data = [];
				}
			}
		}

		return $return_data;
	}*/


/*
	function ajax_getIssuetist($data)

	{

		$profile_settings = $this->get_profile_settings($this->session->userdata('profile'));

		if ($_POST['dt_range'] != '') {

			$dateRange = explode('-', $_POST['dt_range']);

			$from = str_replace('/', '-', $dateRange[0]);

			$to = str_replace('/', '-', $dateRange[1]);

			$d1 = date_create($from);

			$d2 = date_create($to);

			$FromDt = date_format($d1, "Y-m-d");

			$ToDt = date_format($d2, "Y-m-d");
		}



		$sql = $this->db->query("SELECT if(r.type=1,'Issue','Receipt') as type,

			IFNULL(r.amount,0) as amount,if(r.issue_to=1,r.name,if(r.issue_to=2,c.firstname,a.name)) as barrower_name,

			date_format(r.created_on,'%d-%m-%Y') as date_add,r.issue_to,r.id_issue_receipt,

			b.name,r.bill_no,IFNULL(r.narration,'') as narration,IFNULL(c.mobile,'') as mobile,

			if(r.issue_type=1,'Petty Cash',if(r.issue_type=2,'Credit Issue',if(r.issue_type=3,'Advance Refund',if(r.issue_type=4,'Existing Out Standing','')))) as issue_type,

			if(date(d.entry_date)=date(r.bill_date),'1','0') as allow_cancel,r.bill_status,IFNULL(e.firstname,'-') as emp_name

			FROM ret_issue_receipt r

			left join ret_account_head a on r.id_acc_head=a.id_acc_head

			LEFT join customer c on c.id_customer=r.id_customer

			LEFT JOIN employee e on e.id_employee=r.emp_id

			LEFT JOIN branch b on b.id_branch=r.id_branch



			LEFT JOIN ret_day_closing d on d.id_branch=r.id_branch

			where r.type=1

			" . ($FromDt != '' ? 'and date(r.bill_date) BETWEEN "' . $FromDt . '" AND "' . $ToDt . '"' : '') . "

			" . ($profile_settings['allow_bill_type'] == 3 ? " and (r.is_eda=1 OR r.is_eda=2)" : ($profile_settings['allow_bill_type'] == 1 ? " and r.is_eda=1" : " and r.is_eda=2")) . "

            " . ($data['id_branch'] != '' && $data['id_branch'] != 0 ? " and r.id_branch=" . $data['id_branch'] . "" : '') . " ;");

		$return_data =   $sql->result_array();



		if ($data['id_branch'] != '' && $data['id_branch'] != 0) {

			$dayClose = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing where id_branch=" . $data['id_branch']);

			$cur_entry_date = $dayClose->row()->entry_date;

			if ($profile_settings['allow_bill_type'] == 2) {

				if ($FromDt != $cur_entry_date) {

					$return_data = [];
				}
			}
		}

		return $return_data;
	}*/


	function ajax_getReceiptlist($data)

	{

	    $profile_settings=$this->get_profile_settings($this->session->userdata('profile'));

		if($_POST['dt_range'] != '')

        {

            $dateRange = explode('-',$_POST['dt_range']);

            $from = str_replace('/','-',$dateRange[0]);

            $to = str_replace('/','-',$dateRange[1]);

            $d1 = date_create($from);

            $d2 = date_create($to);

            $FromDt = date_format($d1,"Y-m-d");

            $ToDt = date_format($d2,"Y-m-d");

        }



		$sql=$this->db->query("SELECT r.bill_no,b.name,if(r.type=1,'Issue','Receipt') as type,IFNULL(e.firstname,'-') as emp_name,IFNULL(c.firstname,'-') as cus_name,IFNULL(r.amount,0) as amount,IFNULL(adv.utilized_amt,0) as utilized_amt,r.bill_status as status,

			date_format(r.bill_date,'%d-%m-%Y') as date_add,r.issue_to,r.id_issue_receipt,IFNULL(r.weight,0) as weight,IF(r.bill_status=1,'Success','Cancelled') as bill_status,if(date(d.entry_date)=date(r.bill_date),'1','0') as allow_cancel,IF(r.amount = adv.utilized_amt,'1','0') as current_status,

			r.receipt_type,IFNULL(refund.refund_amount,0) as refund_amount,IFNULL(kar.firstname,'-') as karigar_name,IF(r.receipt_to =1,IFNULL(e.firstname,'-'),'-') as employee_name,IF(r.receipt_to=1,'EMPLOYEE',IF(r.receipt_to=2,'KARIGAR','CUSTOMER')) as receipt_to,

			IF(r.receipt_type = 1 ,'Credit Collection',IF(r.receipt_type = 2 ,'Advance Receipt',IF(r.receipt_type = 3 ,'Advance Deposit',IF(r.receipt_type = 4 ,'Opening Blc',IF(r.receipt_type = 5 ,'Order Advance to General Advance',IF(r.receipt_type = 6 ,'Advance Deposit',IF(r.receipt_type = 7 ,'Advance Transfer', CONCAT('Petty Cash Receipt ',IF(r.receipt_to = 1,'To Employee','To Karigar')))))))) ) as r_type

			,IFNULL(r.narration,'') as narration,IFNULL(IF(r.receipt_to=1,e.mobile,IF(r.receipt_to=2,kar.contactno1,c.mobile)),'-') as mobile

			FROM ret_issue_receipt r

			left join ret_account_head a on r.issue_to=a.id_acc_head

			LEFT join customer c on c.id_customer=r.id_customer

			LEFT JOIN employee e on e.id_employee=IFNULL(r.id_customer,r.id_employee)

			LEFT JOIN ret_karigar kar ON  kar.id_karigar = r.id_karigar

			LEFT JOIN ret_day_closing d on d.id_branch=r.id_branch

			LEFT JOIN branch b on b.id_branch=r.id_branch

            LEFT JOIN (SELECT b.bill_id,sum(IFNULL(adv.utilized_amt,0)) utilized_amt,adv.id_issue_receipt FROM ret_billing b

                LEFT JOIN ret_advance_utilized adv ON adv.bill_id=b.bill_id

                WHERE b.bill_status=1 AND adv.bill_id is NOT null

                GROUP BY adv.id_issue_receipt) adv ON adv.id_issue_receipt=r.id_issue_receipt



            LEFT JOIN (select a.refund_receipt,IFNULL(SUM(a.refund_amount),0) as refund_amount

                   From ret_advance_refund a

                   LEFT JOIN ret_issue_receipt r on r.id_issue_receipt=a.id_issue_receipt

                   Where r.bill_status=1

                   group by a.refund_receipt) as refund on refund.refund_receipt=r.id_issue_receipt

			where r.type=2

			".($FromDt != '' ? 'and date(r.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'"' : '')."

			".($data['id_karigar']!='' && $data['id_karigar']!= 0? " and r.id_karigar=".$data['id_karigar']."" :'')."

			".($data['id_employee']!='' && $data['id_employee']!= 0? "and r.receipt_to = 1 and r.id_employee=".$data['id_employee']."" :'')."

            ".($data['id_branch']!='' && $data['id_branch']!= 0 ? " and r.id_branch=".$data['id_branch']."" :'')."

            ".($profile_settings['allow_bill_type']==3 ? " and (r.is_eda=1 OR r.is_eda=2)" : ($profile_settings['allow_bill_type']==1 ? " and r.is_eda=1" :" and r.is_eda=2"))."

            ");

		// print_r($this->db->last_query());exit;

		$return_data =  $sql->result_array();

		if($data['id_branch']!='' && $data['id_branch']!=0){

            $dayClose = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing where id_branch=".$data['id_branch']);

    	    $cur_entry_date = $dayClose->row()->entry_date;

    	    if($profile_settings['allow_bill_type']==2)

    	    {

    	         if($FromDt != $cur_entry_date)

    	         {

    	             $return_data =[];

    	         }

    	    }

        }

		// echo "<pre>"; print_r($return_data); exit;

		// Override allow_cancel based on IR cancel profile settings
		$allow_ir = isset($profile_settings['allow_ir_cancel']) ? $profile_settings['allow_ir_cancel'] : 0;
		$prev_ir  = isset($profile_settings['previous_ir_cancel']) ? $profile_settings['previous_ir_cancel'] : 0;
		foreach ($return_data as &$row) {
			if ($allow_ir == 0) {
				$row['allow_cancel'] = '0'; // No IR cancel permission at all
			} elseif ($prev_ir == 1) {
				// Has both current + previous cancel permission
				if ($row['bill_status'] == 1 || $row['bill_status'] == 'Success') {
					$row['allow_cancel'] = '1';
				}
			}
			// else: allow_ir=1, prev_ir=0 → keep existing SQL logic (same-day only)
		}
		unset($row);

        return $return_data;

	}

	function ajax_getIssuetist($data)

	{

	    $profile_settings=$this->get_profile_settings($this->session->userdata('profile'));

		if($_POST['dt_range'] != '')

        {

            $dateRange = explode('-',$_POST['dt_range']);

            $from = str_replace('/','-',$dateRange[0]);

            $to = str_replace('/','-',$dateRange[1]);

            $d1 = date_create($from);

            $d2 = date_create($to);

            $FromDt = date_format($d1,"Y-m-d");

            $ToDt = date_format($d2,"Y-m-d");

        }



		$sql=$this->db->query("SELECT
					IF(r.type=1, 'Issue', 'Receipt') as type,
					IFNULL(kar.firstname, '-') as karigar_name,
					IF(r.issue_to=1, IFNULL(emp.firstname, e.firstname), '-') as employee_name,
					IF(r.issue_to=2, 'CUSTOMER', IF(r.issue_to=1, 'EMPLOYEE', IF(r.issue_to=3, 'OTHERS', 'KARIGAR'))) as issue_to,
					IFNULL(
					(SELECT GROUP_CONCAT(CONCAT(ah.name, ':', CAST(ed.amount AS UNSIGNED)) SEPARATOR '||')
					 FROM ret_issue_expense_details ed
					 LEFT JOIN ret_account_head ah ON ah.id_acc_head = ed.id_account_head
					 WHERE ed.id_issue_receipt = r.id_issue_receipt),
					IFNULL(a.name, '-')
				) as account_head,
					IFNULL(
						IF(r.issue_to=2, c.mobile,
							IF(r.issue_to=1, e.mobile,
								IF(r.issue_to=3, r.mobile, kar.contactno1)
							)
						), '-') as mobile,
					IFNULL(r.amount, 0) as amount,
					IF(r.issue_to=2, c.firstname, IF(r.issue_to=3, r.name, '-')) as barrower_name,
					r.refno,
					DATE_FORMAT(r.created_on, '%d-%m-%Y') as date_add,
					r.id_issue_receipt,
					b.name,
					r.bill_no,
					IFNULL(r.narration, '') as narration,
					IF(r.issue_type=1, 'Petty Cash',
						IF(r.issue_type=2, 'Credit Issue',
							IF(r.issue_type=3, 'Advance Refund',
								IF(r.issue_type=4, 'Existing Out Standing', '')
							)
						)
					) as issue_type,
					IF(DATE(d.entry_date)=DATE(r.bill_date), '1', '0') as allow_cancel,
					r.bill_status,
					r.is_closed,
					(SELECT COUNT(*) FROM ret_issue_rcpt_payment p WHERE p.id_issue_rcpt = r.id_issue_receipt AND p.payment_mode = 'CHQ') as has_cheque


			FROM ret_issue_receipt r

			left join ret_account_head a on r.id_acc_head=a.id_acc_head

			LEFT join customer c on c.id_customer=r.id_customer

			LEFT JOIN employee e on e.id_employee=r.id_employee

			LEFT JOIN employee emp on emp.id_employee=r.id_customer

			LEFT JOIN ret_karigar kar ON  kar.id_karigar = r.id_karigar

			LEFT JOIN branch b on b.id_branch=r.id_branch



			LEFT JOIN ret_day_closing d on d.id_branch=r.id_branch

			where r.type=1

			".($FromDt != '' ? 'and date(r.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'"' : '')."

			".($profile_settings['allow_bill_type']==3 ? " and (r.is_eda=1 OR r.is_eda=2)" : ($profile_settings['allow_bill_type']==1 ? " and r.is_eda=1" :" and r.is_eda=2"))."

            ".($data['id_branch']!='' && $data['id_branch']!= 0? " and r.id_branch=".$data['id_branch']."" :'')."

			".($data['id_karigar']!='' && $data['id_karigar']!= 0? " and r.id_karigar=".$data['id_karigar']."" :'')."

			".($data['id_employee']!='' && $data['id_employee']!= 0? "and r.issue_to = 1 and IFNULL(c.id_customer,r.id_employee)=".$data['id_employee']."" :'')."

			".($data['id_acc_head']!='' && $data['id_acc_head']!= 0? " and r.id_acc_head=".$data['id_acc_head']."" :'')."

			;");

		$return_data =   $sql->result_array();



		if($data['id_branch']!='' && $data['id_branch']!=0){

            $dayClose = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing where id_branch=".$data['id_branch']);

    	    $cur_entry_date = $dayClose->row()->entry_date;

    	    if($profile_settings['allow_bill_type']==2)

    	    {

    	         if($FromDt != $cur_entry_date)

    	         {

    	             $return_data =[];

    	         }

    	    }

        }

		// Override allow_cancel based on IR cancel profile settings
		$allow_ir = isset($profile_settings['allow_ir_cancel']) ? $profile_settings['allow_ir_cancel'] : 0;
		$prev_ir  = isset($profile_settings['previous_ir_cancel']) ? $profile_settings['previous_ir_cancel'] : 0;
		foreach ($return_data as &$row) {
			if ($allow_ir == 0) {
				$row['allow_cancel'] = '0'; // No IR cancel permission at all
			} elseif ($prev_ir == 1) {
				// Has both current + previous cancel permission
				if ($row['bill_status'] == 1 || $row['bill_status'] == 'Success') {
					$row['allow_cancel'] = '1';
				}
			}
			// else: allow_ir=1, prev_ir=0 → keep existing SQL logic (same-day only)
		}
		unset($row);

        return $return_data;

	}


/*
	function get_issue_details($id)

	{

		$data = $this->db->query("SELECT r.id_issue_receipt,if(r.type=1,'Issue','Receipt') as issue_type,c.mobile,r.issue_to,r.id_customer,r.id_employee,r.issue_type,r.amount,r.weight,r.narration,

		if(r.issue_to=1,r.name,if(r.issue_to=2,c.firstname,a.name)) as name,if(r.issue_type=1,'Petty Cash Issue',if(r.issue_type=3,'Advance Refund','Issue')) as receipt_type,

		IFNULL(r.receipt_as,1) as receipt_as,date_format(r.bill_date,'%d-%m-%Y') as date_add,r.id_branch,e.emp_code,r.id_branch,

		IFNULL(addr.address1,'') as address1,IFNULL(addr.address2,'') as address2,IFNULL(addr.address3,'') as address3,IFNULL(addr.pincode,'') as pincode,

		ct.name as country_name,s.name as state_name,ct.name as city_name,s.name as cus_state,e.firstname as emp_name,IFNULL(r.narration,'') as narration,r.type,concat(br.short_name,r.fin_year_code,'-',r.bill_no) as bill_no

			FROM ret_issue_receipt r

			LEFT JOIN customer c on c.id_customer=r.id_customer

			LEFT JOIN branch br on br.id_branch=r.id_branch


			LEFT JOIN address addr on addr.id_customer=c.id_customer

		    LEFT JOIN city ct on addr.id_city=ct.id_city

		    LEFT JOIN state s on s.id_state=addr.id_state

		    left join country cy on (addr.id_country=cy.id_country)

			LEFT JOIN employee e on e.id_employee=r.emp_id

			left join ret_account_head a on r.id_acc_head=a.id_acc_head

			WHERE r.type=1 and r.id_issue_receipt=" . $id . "");

		//print_r($this->db->last_query());exit;

		return $data->row_array();
	}
*/


function get_issue_details($id)

{

	$data = $this->db->query("SELECT
				r.id_issue_receipt,
				IF(r.type=1, 'Issue', 'Receipt') as issue_type,
				r.issue_to,
				r.id_customer,
				r.id_employee,
				r.issue_type,
				r.amount,
				r.weight,
				r.narration,
				IF(r.issue_to=1, r.name, IF(r.issue_to=2, c.firstname, r.name)) as name,
				IF(r.issue_to=1, e.mobile, IF(r.issue_to=2, c.mobile, IF(r.issue_to=4 ,kar.contactno1, r.mobile))) as mobile,

				IF(r.issue_type=1, 'PAYMENT ISSUE VOUCHER', IF(r.issue_type=3, 'Advance Refund', 'Issue')) as receipt_type,
				IFNULL(r.receipt_as, 1) as receipt_as,
				DATE_FORMAT(r.bill_date, '%d-%m-%Y') as date_add,
				r.id_branch,
				e.emp_code,
				r.id_branch,
				IFNULL(addr.address1, '') as address1,
				IFNULL(addr.address2, '') as address2,
				IFNULL(addr.address3, '') as address3,
				IFNULL(addr.pincode, '') as pincode,
				ct.name as country_name,
				s.name as state_name,
				ct.name as city_name,
				s.name as cus_state,
				e.firstname as emp_name,
				IFNULL(r.narration, '') as narration,
				r.type,
				CONCAT(br.short_name, r.fin_year_code, '-', r.bill_no) as bill_no,
				IFNULL(kar.firstname, '-') as karigar_name,
				IFNULL(c.pan,'') as pan_no,IFNULL(c.aadharid,'') as adhar_no,IFNULL(c.passport_no,'') as pp_no,IFNULL(c.driving_license_no,'') as dl_no 


		FROM ret_issue_receipt r

		LEFT JOIN customer c on c.id_customer=r.id_customer

		LEFT JOIN branch br on br.id_branch=r.id_branch



		LEFT JOIN address addr on addr.id_customer=c.id_customer



		LEFT JOIN city ct on addr.id_city=ct.id_city

		LEFT JOIN state s on s.id_state=addr.id_state

		left join country cy on (addr.id_country=cy.id_country)



		LEFT JOIN employee e on e.id_employee=r.emp_id

		LEFT JOIN ret_karigar kar ON  kar.id_karigar = r.id_karigar

		left join ret_account_head a on r.id_acc_head=a.id_acc_head

		WHERE r.type=1 and r.id_issue_receipt=" . $id . "");

	//print_r($this->db->last_query());exit;

	// print_r($data->row_array());exit;

	return $data->row_array();
}


function get_receipt_advance_details($id)

{

	$sql  = $this->db->query("SELECT date_format(r.bill_date,'%d-%m-%Y') as bill_date,r.bill_no,r.amount as receipt_amt,(IFNULL(advance.utilized_amt,0)+IFNULL(chit_adj.chit_utilized_amount,0)) as utilized_amt,IFNULL(refund.refund_amount,0) as refund_amount,

	(r.amount-IFNULL(advance.utilized_amt,0)-IFNULL(chit_adj.chit_utilized_amount,0)-IFNULL(refund.refund_amount,0)-IFNULL(advance_adjusted.amount,0)) as balance_amount

	FROM ret_advance_refund adv

	LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = adv.refund_receipt

	LEFT JOIN (select sum(u.utilized_amt) as utilized_amt,ir.id_issue_receipt

		from ret_issue_receipt as ir

		left JOIN ret_advance_utilized as u on u.id_issue_receipt=ir.id_issue_receipt

		LEFT JOIN ret_billing bill on bill.bill_id=u.bill_id

		where bill.bill_status=1

		GROUP by ir.id_issue_receipt) as advance on advance.id_issue_receipt=adv.refund_receipt
	LEFT JOIN (select sum(u.utilized_amt) as chit_utilized_amount,ir.id_issue_receipt 
		from ret_issue_receipt as ir 
		left JOIN ret_advance_utilized as u on u.id_issue_receipt=ir.id_issue_receipt 
		LEFT JOIN payment p on p.id_payment=u.id_payment
		where p.payment_status=1
		GROUP by ir.id_issue_receipt) as chit_adj on chit_adj.id_issue_receipt=adv.refund_receipt
	LEFT JOIN (select sum(adj.adjusted_amt) as amount,adj.receipt_for 
		FROM ret_issue_receipt_advance_adj adj
		LEFT JOIN ret_issue_receipt ir ON ir.id_issue_receipt=adj.id_issue_receipt
		where ir.bill_status=1
		GROUP by adj.receipt_for) as advance_adjusted on advance_adjusted.receipt_for=adv.refund_receipt

	LEFT JOIN (select a.refund_receipt,IFNULL(SUM(a.refund_amount),0) as refund_amount

		From ret_advance_refund a

		LEFT JOIN ret_issue_receipt r on r.id_issue_receipt=a.id_issue_receipt

		Where r.bill_status=1 and r.issue_type=3 and a.id_issue_receipt = ".$id."

		group by a.refund_receipt) as refund on refund.refund_receipt=adv.refund_receipt

	 WHERE adv.id_issue_receipt = ".$id."");

	 return $sql->result_array();

}



/*	function get_receipt_details($id)

	{

		$sql = $this->db->query("SELECT r.id_customer,r.id_employee,r.issue_type,r.amount,

			r.weight,c.firstname as name,c.mobile,r.narration,r.id_issue_receipt,if(r.receipt_type=1,'Credit Collection Receipt',if(r.receipt_type=2 or r.receipt_type=3 or r.receipt_type=5,'Advance Receipt','Issue Receipt')) as receipt_type,

			r.receipt_type as rct_type,r.receipt_as as rct_as,r.receipt_for,IFNULL(r.receipt_as,1) as receipt_as,r.weight,r.id_branch,r.bill_no,b.name as branch_name,b.short_name,f.fin_year_code,date_format(r.bill_date,'%d-%m-%Y') as date_add,

			IFNULL(a.address1,'') as address1,IFNULL(a.address2,'') as address2,IFNULL(a.address3,'') as address3,IFNULL(a.pincode,'') as pincode,

			ct.name as country_name,s.name as state_name,ct.name as city_name,s.name as cus_state,IFNULL(bill.bill_no,'') as ref_no,e.emp_code,e.firstname as emp_name,r.type

			FROM ret_issue_receipt r

			LEFT JOIN ret_billing bill on bill.bill_id = r.deposit_bill_id

			LEFT JOIN customer c on c.id_customer=r.id_customer

		    LEFT JOIN address a on a.id_customer=c.id_customer



		    LEFT JOIN city ct on a.id_city=ct.id_city

		    LEFT JOIN state s on s.id_state=a.id_state

		    left join country cy on (a.id_country=cy.id_country)



			LEFT JOIN employee e on e.id_employee=r.emp_id

			LEFT JOIN branch b on b.id_branch=r.id_branch

			LEFT JOIN ret_financial_year f on f.fin_status = 1



			WHERE r.type=2 and r.id_issue_receipt=" . $id . "");



		$data = $sql->row_array();



		return $data;
	}*/

	/**
	 * Common validation for IR (Issue/Receipt) cancellation.
	 * Type-aware checks based on issue_type / receipt_type:
	 *
	 * ISSUE (type=1):
	 *   issue_type=1 (Petty Cash)       → Permission check only
	 *   issue_type=2 (Credit)           → Block if credit collections exist (receipt_for)
	 *   issue_type=3 (Advance Refund)   → Permission check only
	 *   issue_type=4 (Existing Outstanding) → Block if credit collections exist
	 *   issue_type=6 (Non-Jewellery)    → Permission check only
	 *
	 * RECEIPT (type=2):
	 *   receipt_type=1 (Credit Collection) → Permission check only
	 *   receipt_type=2 (Advance)           → Utilized + Refunded + Transferred checks
	 *   receipt_type=3 (Advance Deposit)   → Utilized + Refunded + Transferred checks
	 *   receipt_type=4 (Opening Blc)       → Utilized + Refunded + Transferred checks
	 *   receipt_type=5 (Order→General)     → Utilized + Refunded + Transferred checks
	 *   receipt_type=6 (Chit Close)        → Permission check only
	 *   receipt_type=7 (Advance Transfer)  → Utilized + Refunded + Transferred checks
	 *
	 * @param  int    $ir_id  The id_issue_receipt to validate
	 * @param  string $type   'Issue' or 'Receipt' — used in error messages
	 * @return array  ['status' => TRUE] if cancel is allowed,
	 *                ['status' => FALSE, 'message' => '...'] if blocked
	 */
	function validate_ir_cancel($ir_id, $type = 'Entry')
	{
		// 1. Fetch record
		$ir_detail = $this->db->select('type, issue_type, receipt_type, bill_date, is_closed, is_refunded, istransfered')
			->where('id_issue_receipt', $ir_id)
			->get('ret_issue_receipt')->row_array();

		if (!$ir_detail) {
			return array('status' => FALSE, 'message' => $type . ' record not found');
		}

		// 2. Profile permission check (common for all types)
		$profile = $this->get_profile_settings($this->session->userdata('profile'));
		$bill_date_only = date('Y-m-d', strtotime($ir_detail['bill_date']));
		$is_backdated = ($bill_date_only < date('Y-m-d'));

		$allow_ir = isset($profile['allow_ir_cancel']) ? $profile['allow_ir_cancel'] : 0;
		$prev_ir  = isset($profile['previous_ir_cancel']) ? $profile['previous_ir_cancel'] : 0;

		if (!$is_backdated && empty($allow_ir)) {
			return array('status' => FALSE, 'message' => 'You do not have permission to cancel ' . $type . ' entries');
		}
		if ($is_backdated && empty($prev_ir)) {
			return array('status' => FALSE, 'message' => 'You do not have permission to cancel previous date ' . $type . ' entries');
		}

		// 3. Tally/Accounts transfer check (common for all types)
		if ($ir_detail['istransfered'] == 1) {
			return array('status' => FALSE, 'message' => 'Cannot cancel: This entry has been pushed to Tally/Accounts');
		}

		// 4. Type-specific validation
		if ($ir_detail['type'] == 1) {
			// ── ISSUE entry ──
			$issue_type = $ir_detail['issue_type'];

			if ($issue_type == 2 || $issue_type == 4) {
				// Credit Issue / Existing Outstanding → check if any credit collection receipt exists
				$collection_count = $this->db
					->where('receipt_for', $ir_id)
					->where('bill_status', 1)  // only active collections
					->where('type', 2)         // receipt type entry
					->where('receipt_type', 1)  // credit collection
					->count_all_results('ret_issue_receipt');
				if ($collection_count > 0) {
					return array('status' => FALSE, 'message' => 'Cannot cancel: Credit collections have been made against this entry. Cancel the collection(s) first.');
				}
			}
			// issue_type=1 (Petty Cash), 3 (Advance Refund), 6 (Non-Jewellery) → permission check only, no dependencies

		} else {
			// ── RECEIPT entry ──
			$receipt_type = $ir_detail['receipt_type'];

			// receipt_type=3 (Advance Deposit) and receipt_type=5 (Order→General) are auto-created from billing.
			// They get auto-cancelled when the source bill is cancelled (deposit_bill_id link).
			if ($receipt_type == 3 || $receipt_type == 5) {
				return array('status' => FALSE, 'message' => 'Cannot cancel: This entry was created from a bill. Cancel the source bill to cancel this entry automatically.');
			}

			// Advance-related receipt types: 2=Advance, 4=Opening Blc, 7=Advance Transfer
			if (in_array($receipt_type, array(2, 4, 7))) {

				// Utilized check (is_closed flag + ret_advance_utilized table)
				if ($ir_detail['is_closed'] == 1) {
					return array('status' => FALSE, 'message' => 'Cannot cancel: This entry has been utilized against a bill');
				}
				$utilized_count = $this->db->where('id_issue_receipt', $ir_id)->count_all_results('ret_advance_utilized');
				if ($utilized_count > 0) {
					return array('status' => FALSE, 'message' => 'Cannot cancel: This advance has been utilized');
				}

				// Refunded check
				if ($ir_detail['is_refunded'] == 1) {
					return array('status' => FALSE, 'message' => 'Cannot cancel: This entry has been refunded');
				}
				$refund_count = $this->db->where('refund_receipt', $ir_id)->count_all_results('ret_advance_refund');
				if ($refund_count > 0) {
					return array('status' => FALSE, 'message' => 'Cannot cancel: This entry has a linked refund');
				}

				// Advance Transfer to another customer
				$transfer_count = $this->db->where('id_issue_receipt', $ir_id)->count_all_results('ret_advance_transfer');
				if ($transfer_count > 0) {
					return array('status' => FALSE, 'message' => 'Cannot cancel: This advance has been transferred to another customer');
				}
			}
			// receipt_type=1 (Credit Collection), 6 (Chit Close) → permission check only
		}

		return array('status' => TRUE);
	}

	function get_receipt_details($id)

	{

		$sql = $this->db->query("SELECT r.id_customer,r.id_employee,r.issue_type,r.amount,  ti.from_customer,

			r.weight,IF(r.receipt_type = 8,IF(r.receipt_to = 1,emp.firstname,kar.firstname),c.firstname) as name,IF(r.receipt_type = 8,IF(r.receipt_to = 1,emp.mobile,kar.contactno1),c.mobile) as mobile,r.narration,r.id_issue_receipt,if(r.receipt_type=1,'Credit Collection Receipt',if(r.receipt_type=2 or r.receipt_type=3 or r.receipt_type=5 or r.receipt_type=7,'Advance Receipt','PAYMENT RECEIPT VOUCHER')) as receipt_type,

			r.receipt_type as rct_type,r.receipt_as as rct_as,r.receipt_for,IFNULL(r.receipt_as,1) as receipt_as,r.weight,r.id_branch,CONCAT(b.short_name, f.fin_year_code, '-', r.bill_no) as bill_no,b.name as branch_name,b.short_name,f.fin_year_code,date_format(r.bill_date,'%d-%m-%Y') as date_add,

			IFNULL(a.address1,'') as address1,IFNULL(a.address2,'') as address2,IFNULL(a.address3,'') as address3,IFNULL(a.pincode,'') as pincode,

			ct.name as country_name,s.name as state_name,ct.name as city_name,s.name as cus_state,IFNULL(bill.bill_no,'') as ref_no,e.emp_code,e.firstname as emp_name,r.type,emp_issue.bills,c.title,date_format(r.bill_date,'%h:%i %p') as time_add,state_code,ifnull(ord.order_no,'') as ord_no, ifnull(r.goldrate_18ct,0) as goldrate_18ct, ifnull(r.goldrate_22ct,0) as goldrate_22ct,ifnull(r.silverrate_1gm,0) as silverrate_1gm,

			IFNULL(c.pan,'') as pan_no,IFNULL(c.aadharid,'') as adhar_no,IFNULL(c.passport_no,'') as pp_no,IFNULL(c.driving_license_no,'') as dl_no 

			FROM ret_issue_receipt r

			LEFT JOIN ret_billing bill on bill.bill_id = r.deposit_bill_id

			left join customerorder ord on ord.id_customerorder = r.id_customerorder

			LEFT JOIN customer c on c.id_customer=r.id_customer

		    LEFT JOIN address a on a.id_customer=c.id_customer

			left join (select GROUP_CONCAT(ir.bill_no) as bills,crd.id_issue_receipt

					FROM ret_issue_credit_collection_details crd

					LEFT JOIN ret_issue_receipt ir ON ir.id_issue_receipt=crd.receipt_for

					LEFT JOIN ret_issue_receipt ire ON ire.id_issue_receipt=crd.id_issue_receipt

					where ir.bill_status=1

					GROUP by crd.id_issue_receipt) as emp_issue on emp_issue.id_issue_receipt=r.id_issue_receipt



		    LEFT JOIN city ct on a.id_city=ct.id_city

		    LEFT JOIN state s on s.id_state=a.id_state

		    left join country cy on (a.id_country=cy.id_country)



			LEFT JOIN employee e on e.id_employee=r.emp_id


			LEFT JOIN employee emp on emp.id_employee=r.id_employee

			LEFT JOIN ret_karigar kar on kar.id_karigar=r.id_karigar

			LEFT JOIN branch b on b.id_branch=r.id_branch

			LEFT JOIN ret_financial_year f on f.fin_status = 1

			LEFT JOIN (
				SELECT tc.firstname as to_customer, fc.firstname as from_customer, adt.id_issue_receipt
				FROM ret_advance_transfer adt 
				LEFT JOIN ret_issue_receipt f on f.id_issue_receipt = adt.transfer_receipt_id
				LEFT JOIN customer fc on fc.id_customer = f.id_customer
				LEFT JOIN ret_issue_receipt t on t.id_issue_receipt = adt.id_issue_receipt
				LEFT JOIN customer tc on tc.id_customer = t.id_customer
			) AS ti ON ti.id_issue_receipt = r.id_issue_receipt


			WHERE r.type=2 and r.id_issue_receipt=" . $id . "");

			// echo "<pre>"; print_r($this->db->last_query());exit;



		$data = $sql->row_array();


		return $data;
	}


	function get_receipt_advance_adj_details($id)

	{

		$sql = $this->db->query("SELECT * FROM `ret_issue_receipt_advance_adj` WHERE id_issue_receipt=" . $id . "");

		return $sql->result_array();
	}



	function get_est_adv_details($id)

	{

		$sql = $this->db->query("SELECT * FROM `ret_adv_receipt_weight` WHERE id_issue_receipt=" . $id . "");

		return $sql->result_array();
	}



	function get_est_adv_tag_details($id)

	{

		$sql = $this->db->query("SELECT * FROM `ret_adv_receipt_tags` WHERE adv_rcpt_issue_receipt_id=" . $id . "");

		return $sql->result_array();
	}



	function get_receipt_payment($id)

	{

		$pay_details = array("pay_details" => array());

		$items_query = $this->db->query("SELECT p.cheque_no,p.id_issue_rcpt_pay,p.id_issue_rcpt,p.payment_amount,p.card_no,p.cvv,p.payment_mode,

		if(p.NB_type=1,'RTGS',if(p.NB_type=2,'IMPS',if(p.NB_type=3,'UPI',if(p.NB_type=4,'NEFT','')))) as nb_type, DATE_FORMAT(p.cheque_date,'%Y-%m-%d') as cheque_date,DATE_FORMAT(p.payment_date,'%Y-%m-%d') as payment_date, p.payment_ref_number as payment_ref_number,DATE_FORMAT(p.net_banking_date,'%Y-%m-%d') as net_banking_date,

		IFNULL(p.id_bank,'') as id_bank,IFNULL(p.bank_branch,'') as bank_branch,IFNULL(b.acc_number,'') as acc_number,IFNULL(c.firstname,'') as payee_name	

		FROM ret_issue_rcpt_payment p

		LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = p.id_issue_rcpt

		LEFT JOIN customer c ON c.id_customer = r.id_customer

		LEFT JOIN bank b ON b.id_bank = p.id_bank

		where p.id_issue_rcpt=" . $id . "");

		$data = $items_query->result_array();


		return $data;
	}



	// ---------------------------------------------------------------
	// Issue & Receipt Payment Mode Edit — Methods (TODO-04 & TODO-05)
	// ---------------------------------------------------------------

	/**
	 * Fetch voucher header + all ACTIVE payment rows for an issue/receipt.
	 *
	 * @param  int $id_issue_rcpt  Primary key of ret_issue_receipt
	 * @return array  ['header' => [...], 'payments' => [...]]
	 */
	// -------------------------------------------------------
	// Lookup id_issue_receipt by bill_no + financial year code
	// -------------------------------------------------------
	function get_id_by_issue_billno($bill_no, $fin_year = NULL)
	{
		$this->db->select('id_issue_receipt');
		$this->db->from('ret_issue_receipt');
		$this->db->where('bill_no', $bill_no);
		if (!empty($fin_year)) {
			$this->db->where('fin_year_code', $fin_year);
		}
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return $q->row()->id_issue_receipt;
		}
		return 0;
	}

	function get_issue_rcpt_payment_details($id_issue_rcpt)
	{
		// Header — works for both Issue (type=1) and Receipt (type=2)
		$header_q = $this->db->query("
			SELECT
				r.id_issue_receipt,
				r.bill_no,
				r.fin_year_code,
				r.type,
				r.amount,
				r.bill_date,
				r.narration,
				r.id_branch,
				r.bill_status,
				IF(r.type=1, 'Issue', 'Receipt') AS voucher_type,
				IF(r.issue_to=1, r.name,
					IF(r.issue_to=2, c.firstname,
						IF(r.issue_to=3, acc.name,
							IF(r.issue_to=4, kar.firstname, r.name)
						)
					)
				) AS party_name,
				IFNULL(c.id_customer, '') AS id_customer,
				CONCAT(br.short_name, r.fin_year_code, '-', r.bill_no) AS full_bill_no
			FROM ret_issue_receipt r
			LEFT JOIN customer c       ON c.id_customer  = r.id_customer
			LEFT JOIN branch   br      ON br.id_branch   = r.id_branch
			LEFT JOIN ret_karigar kar  ON kar.id_karigar  = r.id_karigar
			LEFT JOIN ret_account_head acc ON acc.id_acc_head = r.id_acc_head
			WHERE r.id_issue_receipt = " . (int)$id_issue_rcpt . "
		");

		// Active payment rows only
		$pay_q = $this->db->query("
			SELECT
				p.id_issue_rcpt_pay,
				p.id_issue_rcpt,
				p.payment_mode,
				p.payment_amount,
				p.card_type,
				p.card_no,
				p.payment_ref_number,
				p.NB_type,
				p.id_bank,
				p.id_pay_device,
				IFNULL(p.bank_name,'')   AS bank_name,
				IFNULL(p.bank_branch,'') AS bank_branch,
				IFNULL(p.bank_IFSC,'')   AS bank_IFSC,
				DATE_FORMAT(p.cheque_date,'%Y-%m-%d')      AS cheque_date,
				DATE_FORMAT(p.net_banking_date,'%Y-%m-%d') AS net_banking_date,
				DATE_FORMAT(p.card_date,'%Y-%m-%d')        AS card_date,
				p.cheque_no,
				p.remark
			FROM ret_issue_rcpt_payment p
			WHERE p.id_issue_rcpt = " . (int)$id_issue_rcpt . "
			  AND p.payment_status = 1
		");

		return array(
			'header'   => $header_q->row_array(),
			'payments' => $pay_q->result_array(),
		);
	}


	/**
	 * Hard-delete existing payment rows then insert new ones — same pattern as billing paymentmode_edit.
	 * Caller must wrap in trans_begin / trans_commit / trans_rollback.
	 *
	 * @param  int   $id_issue_rcpt   FK to ret_issue_receipt
	 * @param  array $payment_data    Keys: cash, cards[], cheques[], net_bankings[]
	 * @param  int   $updated_by      Employee ID performing the update
	 * @return bool  TRUE on success
	 */
	function update_issue_rcpt_payment($id_issue_rcpt, $payment_data, $updated_by)
	{
		$now = date('Y-m-d H:i:s');

		// Pre-build new rows FIRST — abort if nothing valid to insert
		$rows_to_insert = array();

		// Cash
		if (!empty($payment_data['cash']) && (float)$payment_data['cash'] > 0) {
			$rows_to_insert[] = array(
				'id_issue_rcpt'  => $id_issue_rcpt,
				'payment_mode'   => 'Cash',
				'payment_amount' => $payment_data['cash'],
				'payment_status' => 1,
				'type'           => 2,
				'payment_type'   => 'Manual',
				'payment_date'   => $now,
				'created_time'   => $now,
				'created_by'     => $updated_by,
			);
		}

		// Cards (CC / DC)
		if (!empty($payment_data['cards']) && is_array($payment_data['cards'])) {
			foreach ($payment_data['cards'] as $card) {
				if (empty($card['card_amt']) || (float)$card['card_amt'] <= 0) continue;
				$rows_to_insert[] = array(
					'id_issue_rcpt'      => $id_issue_rcpt,
					'payment_mode'       => (isset($card['card_type']) && $card['card_type'] == 1 ? 'CC' : 'DC'),
					'card_type'          => isset($card['card_type']) ? $card['card_type'] : NULL,
					'payment_amount'     => $card['card_amt'],
					'card_no'            => (!empty($card['card_no'])      ? $card['card_no']      : NULL),
					'payment_ref_number' => (!empty($card['approval_no'])  ? $card['approval_no']  : NULL),
					'id_pay_device'      => (!empty($card['id_pay_device'])? $card['id_pay_device']: NULL),
					'card_date'          => (!empty($card['card_date'])    ? $card['card_date']    : NULL),
					'payment_status'     => 1,
					'type'               => 2,
					'payment_date'       => $now,
					'created_time'       => $now,
					'created_by'         => $updated_by,
				);
			}
		}

		// Cheques
		if (!empty($payment_data['cheques']) && is_array($payment_data['cheques'])) {
			foreach ($payment_data['cheques'] as $chq) {
				if (empty($chq['payment_amount']) || (float)$chq['payment_amount'] <= 0) continue;
				$rows_to_insert[] = array(
					'id_issue_rcpt'  => $id_issue_rcpt,
					'payment_mode'   => 'CHQ',
					'payment_amount' => $chq['payment_amount'],
					'cheque_no'      => (!empty($chq['cheque_no'])   ? $chq['cheque_no']   : NULL),
					'cheque_date'    => (!empty($chq['cheque_date']) ? $chq['cheque_date'] : NULL),
					'bank_name'      => (!empty($chq['bank_name'])   ? $chq['bank_name']   : NULL),
					'bank_branch'    => (!empty($chq['bank_branch']) ? $chq['bank_branch'] : NULL),
					'payment_status' => 1,
					'type'           => 2,
					'payment_date'   => $now,
					'created_time'   => $now,
					'created_by'     => $updated_by,
				);
			}
		}

		// Net Banking (RTGS / IMPS / UPI / NEFT)
		if (!empty($payment_data['net_bankings']) && is_array($payment_data['net_bankings'])) {
			foreach ($payment_data['net_bankings'] as $nb) {
				if (empty($nb['amount']) || (float)$nb['amount'] <= 0) continue;
				$rows_to_insert[] = array(
					'id_issue_rcpt'      => $id_issue_rcpt,
					'payment_mode'       => 'NB',
					'NB_type'            => (!empty($nb['nb_type']) ? $nb['nb_type'] : NULL),
					'payment_amount'     => $nb['amount'],
					'payment_ref_number' => (!empty($nb['ref_no'])  ? $nb['ref_no']  : NULL),
					'id_bank'            => (!empty($nb['id_bank']) ? $nb['id_bank'] : NULL),
					'net_banking_date'   => (!empty($nb['nb_date']) ? $nb['nb_date'] : NULL),
					'payment_status'     => 1,
					'type'               => 2,
					'payment_date'       => $now,
					'created_time'       => $now,
					'created_by'         => $updated_by,
				);
			}
		}

		// Safety gate: never delete if we have nothing valid to insert
		if (empty($rows_to_insert)) {
			return FALSE;
		}

		// Step 1: Hard DELETE existing payment rows (same as billing paymentmode_edit)
		$this->db->where('id_issue_rcpt', (int)$id_issue_rcpt);
		$this->db->delete('ret_issue_rcpt_payment');

		// Step 2: Insert new rows (individual inserts — rows have different column sets)
		foreach ($rows_to_insert as $row) {
			$this->db->insert('ret_issue_rcpt_payment', $row);
		}

		return $this->db->trans_status();
	}





	// issue and receipt



	function get_bill_detail($bill_id)

	{

		$items_query = $this->db->query("SELECT bill.bill_id,b.bill_det_id,IFNULL(b.tag_id,'') as tag_id,bill.bill_type,

			b.piece as no_of_piece,b.gross_wt as gross_wt,b.net_wt as net_wt,bill.id_branch,b.product_id,b.design_id,tag.current_branch,b.item_type,

			IFNULL(b.id_sub_design,'') as id_sub_design,IFNULL(b.id_section,'') as id_section,bill.cancelled_date

			FROM ret_billing bill

			LEFT JOIN ret_bill_details b on b.bill_id=bill.bill_id

			LEFT JOIN ret_taging tag on tag.tag_id = b.tag_id

			where b.bill_id=" . $bill_id . "");

		return $items_query->result_array();
	}

	function get_bill_detail_other_inv($bill_id)

	{

		$items_query = $this->db->query("SELECT * FROM ret_other_invnetory_issue otrinviss WHERE otrinviss.bill_id =" . $bill_id . "");

		// print_r($this->db->last_query());exit;

		return $items_query->result_array();
	}

	function checkNonTagItemExist($data)
	{

		$r = array("status" => FALSE);

		$id_sub_design = (isset($data['id_sub_design']) ? ($data['id_sub_design'] != '' ? $data['id_sub_design'] : '') : '');

		$sql = "SELECT id_nontag_item FROM ret_nontag_item WHERE product=" . $data['id_product'] . "

        " . ($data['id_design'] != '' ? " and design=" . $data['id_design'] . "" : '') . "



        " . ($data['id_section'] != '' ? " and id_section=" . $data['id_section'] . "" : '') . "

        " . ($id_sub_design != '' ? " and id_sub_design=" . $id_sub_design . "" : '') . "

        AND branch=" . $data['id_branch'];
		$res = $this->db->query($sql);

		if ($res->num_rows() > 0) {

			$r = array("status" => TRUE, "id_nontag_item" => $res->row()->id_nontag_item);
		} else {

			$r = array("status" => FALSE, "id_nontag_item" => "");
		}

		return $r;
	}

	function updateNTData($data, $arith)
	{

		$sql = "UPDATE ret_nontag_item SET no_of_piece=(no_of_piece" . $arith . " " . $data['no_of_piece'] . "),gross_wt=(gross_wt" . $arith . " " . $data['gross_wt'] . "),net_wt=(net_wt" . $arith . " " . $data['net_wt'] . "),updated_by=" . $data['updated_by'] . ",updated_on='" . $data['updated_on'] . "' WHERE id_nontag_item=" . $data['id_nontag_item'];

		$status = $this->db->query($sql);

		return $status;
	}



	function no_to_words($no = "")

	{

		$nos = explode('.', $no);

		$val1 = "";

		$val2 = "";

		$val = "";

		if (isset($nos[0])) {

			$val1 = $this->no_to_words1($nos[0]);

			$val = $val1 . " Rupees";
		}

		if (isset($nos[1]) && $nos[1] != 0) {

			$val2 = $this->no_to_words1($nos[1]);

			if (isset($val2))

				$val = $val1 . " Rupees and" . " " . $val2 . " Paisa";
		}

		return $val;
	}



	function no_to_words1($nos1 = "")

	{

		$words = array('0' => '', '1' => 'One', '2' => 'Two', '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six', '7' => 'Seven', '8' => 'Eight', '9' => 'Nine', '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve', '13' => 'Thirteen', '14' => 'Fourteen', '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen', '18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty', '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty', '60' => 'Sixty', '70' => 'Seventy', '80' => 'Eighty', '90' => 'Ninety', '100' => 'Hundred &', '1000' => 'Thousand', '100000' => 'Lakh', '10000000' => 'Crore');

		$nos[0] = $nos1;

		if ($nos[0] == 0)

			return '';

		else {

			$novalue = '';

			$highno = $nos[0];

			$remainno = 0;

			$value = 100;

			$value1 = 1000;

			$temp = '';



			while ($nos[0] >= 100) {

				if (($value <= $nos[0]) && ($nos[0]  < $value1)) {

					$novalue = $words["$value"];

					$highno = (int)($nos[0] / $value);

					$remainno = $nos[0] % $value;

					break;
				}

				$value = $value1;

				$value1 = $value * 100;
			}

			if (array_key_exists("$highno", $words)) {

				return $words["$highno"] . " " . $novalue . " " . $this->no_to_words1($remainno);
			} else {

				$unit = $highno % 10;

				$ten = (int)($highno / 10) * 10;

				return $words["$ten"] . " " . $words["$unit"] . " " . $novalue . " " . $this->no_to_words1($remainno);
			}
		}
	}

	function advance_details_order_no($orderno)

	{

		$return_data = array();

		if ($orderno != null && $orderno != '') {

			$advance = $this->db->query("SELECT a.order_no,a.advance_amount as paid_advance,

			a.advance_weight as paid_weight,s.metal_type,a.store_as,a.advance_type,a.rate_calc,a.rate_per_gram

			from ret_billing b

			LEFT JOIN ret_billing_advance a on a.bill_id=b.bill_id

			LEFT JOIN ret_estimation_items est on est.orderno=a.order_no

			LEFT JOIN ret_estimation e on e.estimation_id=est.esti_id

			LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id=a.old_metal_sale_id

			where a.is_adavnce_adjusted=0 and a.order_no='" . $orderno . "'");

			//print_r($this->db->last_query());exit;

			$return_data = $advance->result_array();
		}

		return $return_data;
	}



	function get_partial_sale_det($tag_id)

	{

		$status = true;

		$partial = $this->db->query("SELECT * FROM ret_partlysold WHERE tag_id='" . $tag_id . "'");

		if ($partial->num_rows() > 0) {

			$status = $this->updateData(array('status' => 0), 'tag_id', $tag_id, 'ret_partlysold');
		}

		return $status;
	}

	function get_tag_details($tag_id)

	{

		$sql = $this->db->query("SELECT t.tag_id,t.gross_wt,t.net_wt,IFNULL(p.sold_nwt,0) as sold_nwt

        FROM ret_taging t

        LEFT JOIN(SELECT SUM(s.sold_gross_wt) as sold_gwt,SUM(s.sold_net_wt) as sold_nwt,s.tag_id

        FROM ret_partlysold s

        LEFT JOIN ret_bill_details d ON d.bill_det_id=s.sold_bill_det_id

        LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

        WHERE b.bill_status=1 and s.tag_id=" . $tag_id . "

        GROUP by d.tag_id) as p ON p.tag_id=t.tag_id

        WHERE t.tag_id=" . $tag_id . "

        GROUP by t.tag_id");

		//print_r($this->db->last_query());exit;

		return $sql->row_array();
	}



	function bill_no_generate($id_branch, $is_eda)

	{

		$lastno = $this->get_max_bill_no($id_branch, $is_eda);

		if ($lastno != NULL) {

			$number = (int) $lastno;

			$number++;

			$code_number = str_pad($number, 5, '0', STR_PAD_LEFT);

			return $code_number;
		} else {

			$code_number = str_pad('1', 5, '0', STR_PAD_LEFT);

			return $code_number;
		}
	}



	function get_max_bill_no($id_branch, $is_eda)



	{



		$fin_year = $this->get_FinancialYear();



		$sql = "SELECT MAX(CAST(bill_no AS UNSIGNED)) AS lastBillNo
        FROM ret_issue_receipt
        WHERE fin_year_code = '" . $fin_year['fin_year_code'] . "' " .
        ($id_branch != '' && $id_branch > 0 ? " AND id_branch = " . $id_branch : '') . "
        AND is_eda = " . $is_eda;

		// echo "<pre>";print_r($sql);exit;



		return $this->db->query($sql)->row()->lastBillNo;

	}



	function getVoucherDetails($id_branch, $id_cus, $code)

	{

		$data = array();

		$responseData = array();

		$sql = $this->db->query("SELECT g.id_gift_card,IFNULL(g.bill_id,'') as bill_id,g.code as label,g.id_gift_card as value,g.free_card,

        g.amount,g.percentage,g.weight,g.credit_type,DATE_FORMAT(g.valid_to,'%Y-%m-%d') as valid_to,IFNULL(g.id_gift_voucher,'') as id_gift_voucher,IFNULL(g.id_set_gift_voucher,'') as id_set_gift_voucher,g.status,IFNULL(g.id_branch,'') as id_branch

        FROM gift_card g

        WHERE g.code like '%" . $code . "%' ");



		$gift = $sql->result_array();



		if (sizeof($gift) > 0) {

			foreach ($gift as $items) {

				$from_date = strtotime(date("Y-m-d"));

				$to_date = strtotime($items['valid_to']);

				if ($from_date > $to_date && $to_date != null) {

					$gift_status['status'] = 3;

					$this->updateData($gift_status, 'id_gift_card', $items['id_gift_card'], 'gift_card');

					$status = 3;
				} else {

					$status = $items['status'];
				}



				$responseData[] = array(

					'id_gift_card'  => $items['id_gift_card'],

					'bill_id'       => $items['bill_id'],

					'label'         => $items['label'],

					'value'         => $items['value'],

					'weight'        => $items['weight'],

					'amount'         => $items['amount'],

					'percentage'    => $items['percentage'],

					'credit_type'    => $items['credit_type'],

					'valid_to'      => $items['valid_to'],

					'free_card'     => $items['free_card'],

					'id_branch'     => $items['id_branch'],

					'status'        => $status,

					'id_gift_voucher' => $items['id_gift_voucher'],

					'id_set_gift_voucher' => $items['id_set_gift_voucher'],

					'gift_redeem_det' => ($items['id_set_gift_voucher'] != '' ? $this->get_purchase_voucher($items['id_set_gift_voucher']) : $this->gift_voucher_master($items['id_gift_voucher'])),

				);



				$data = array('status' => TRUE, 'responseData' => $responseData);
			}
		} else {

			$data = array('status' => false, 'message' => 'Invalid Voucher Code..');
		}



		return $data;
	}



	function gift_voucher_master($id_gift_voucher)

	{



		$data = array();

		if ($id_gift_voucher != '') {

			$sql = $this->db->query("select * FROM ret_gift_voucher_master where id_gift_voucher=" . $id_gift_voucher);



			$data = $sql->row_array();
		}

		return $data;
	}



	function get_purchase_voucher($id_set_gift_voucher)

	{



		$data = array();

		if ($id_set_gift_voucher != '') {

			$sql = $this->db->query("select * FROM ret_bill_gift_voucher_settings where id_set_gift_voucher=" . $id_set_gift_voucher);



			$data = $sql->row_array();
		}

		return $data;
	}



	function CheckRedeemProduct($id)

	{

		$sql = $this->db->query("select s.id_gift_voucher,s.id_product,s.issue,s.utilize from ret_gift_issue_redeem_prod s where utilize=1 and id_gift_voucher=" . $id);

		return $sql->result_array();
	}



	function GeneralGiftRedeemProduct($id)

	{

		$sql = $this->db->query("select s.id_gift_voucher,s.id_product,s.issue,s.utilize from ret_gift_master_redeem_prod s where utilize=1 and id_gift_voucher=" . $id);

		return $sql->result_array();
	}





	function ret_bill_return_details($bill_id)

	{

		$sql = $this->db->query("select * from ret_bill_return_details where bill_id=" . $bill_id . "");

		return $sql->result_array();
	}

	function get_total_returned_amount_by_bill($bill_id)
	{
		$sql = $this->db->query("SELECT IFNULL(SUM(d.item_cost),0) as total_returned
			FROM ret_bill_return_details r
			LEFT JOIN ret_bill_details d ON d.bill_det_id = r.ret_bill_det_id
			LEFT JOIN ret_billing rb ON rb.bill_id = r.bill_id
			WHERE rb.bill_status = 1 AND d.bill_det_id IS NOT NULL AND r.ret_bill_id = " . $bill_id);
		$result = $sql->row_array();
		return isset($result['total_returned']) ? $result['total_returned'] : 0;
	}



	function get_redeem_details($id)

	{

		$sql = $this->db->query("SELECT g.bill_id,d.bill_gift_voucher_id

        FROM ret_billing_gift_voucher_details d

        LEFT JOIN gift_card g on g.adjusted_bill_id=d.bill_gift_voucher_id

        where d.bill_id=" . $id . "");



		if ($sql->num_rows() > 0) {

			$data = $sql->row_array();

			$this->updateData(array('status' => 0, 'adjusted_bill_id' => NULL), 'adjusted_bill_id', $data['bill_gift_voucher_id'], 'gift_card');
		}

		return true;
	}



	function get_gift_issue_details($bill_id)

	{

		$sql = $this->db->query("SELECT g.bill_id FROM gift_card g WHERE g.bill_id=" . $bill_id . "");



		if ($sql->num_rows() > 0) {

			$data = $sql->row_array();

			$this->updateData(array('status' => 5, 'bill_id' => NULL), 'bill_id', $data['bill_id'], 'gift_card');
		}

		return true;
	}



	function getOldMetalRate($id_metal)

	{

		$sql = $this->db->query("SELECT rate from ret_old_metal_rate where id_metal=" . $id_metal . " and status=1");

		return $sql->row()->rate;
	}





	function getChitUtilized($bill_id)

	{

		$sql = $this->db->query("Select * from ret_billing_chit_utilization where bill_id=" . $bill_id . "");

		return $sql->result_array();
	}





	//Business Customers



	function getSearchCompanyUsers($SearchTxt, $id_customer)

	{

		$data = $this->db->query("SELECT c.id_cmp_emp as value,concat(c.firstname,'-',c.mobile) as label

        FROM

        ret_customer_company_users c

        WHERE (firstname like '%" . $SearchTxt . "%' OR mobile like '%" . $SearchTxt . "%')

        and c.id_customer=" . $id_customer . "");

		return $data->result_array();
	}



	function addNewCompanyUsers($data)

	{

		$customer_check_query = $this->db->query("SELECT * FROM ret_customer_company_users WHERE mobile='" . $data['mobile'] . "'");

		if ($customer_check_query->num_rows() == 0) {

			$insert_data = array("id_customer" => $data['id_customer'], "firstname" => strtoupper($data['emp_name']), "mobile" => $data['mobile'], 'date_add' => date("Y-m-d H:i:s"), "created_by" => $this->session->userdata('uid'));

			$cus_insert_id = $this->insertData($insert_data, "ret_customer_company_users");

			if (!empty($cus_insert_id)) {

				$insert_data["id_cmp_emp"] = $cus_insert_id;

				return array("success" => TRUE, "message" => "Customer details added successfully", "response" => $insert_data);
			} else {

				return array("success" => FALSE, "message" => "Could not add customer, please try again", "response" => array());
			}
		} else {

			return array("success" => FALSE, "message" => "Given mobile number already exist", "response" => $customer_check_query->row());
		}
	}

	//Business Customers





	function get_credit_collection_details($bill_id)

	{

		$return_data = array();

		$total_bill_amount = 0;

		$credit_disc_amt = 0;

		$data = $this->db->query("SELECT b.bill_id,b.bill_no,b.bill_type,b.ref_bill_id,b.tot_amt_received,b.credit_disc_amt,

    	b.tot_bill_amount,DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date

    	 From ret_billing b

    	 where b.bill_status=1 and b.ref_bill_id=" . $bill_id . "");

		$items = $data->result_array();

		$old__metal_amount  = 0;

		foreach ($items as $item) {

			$total_bill_amount  += $item['tot_amt_received'];

			$credit_disc_amt    += $item['credit_disc_amt'];

			$old_metal_details  =  $this->getOld_sales_details($item['bill_id'], 8);

			foreach ($old_metal_details as $old_items) {

				$old__metal_amount += $old_items['amount'];
			}
		}

		return $total_bill_amount + $old__metal_amount + $credit_disc_amt;
	}



	function getOld_sales_details($bill_id, $bill_type)

	{

		$old_metal_query = $this->db->query("SELECT s.old_metal_sale_id,s.bill_id,s.purpose,s.metal_type,s.item_type,s.gross_wt,s.stone_wt,s.dust_wt,s.stone_wt,s.wastage_percent,s.wast_wt,

		s.net_wt,s.rate_per_grm as rate_per_gram,s.rate as amount,s.bill_id,s.bill_discount,est_id,b.bill_no,b.pur_ref_no

		FROM ret_billing b

		LEFT join  ret_bill_old_metal_sale_details s on s.bill_id=b.bill_id

		where b.bill_status=1 and s.bill_id=" . $bill_id . "");

		$old_matel_details = $old_metal_query->result_array();

		return $old_matel_details;
	}







	//Update Wallet Account

	function updateWalletData($data, $arith)

	{

		$sql = "UPDATE ret_wallet SET amount=(amount" . $arith . " " . $data['amount'] . "),weight=(weight" . $arith . " " . $data['weight'] . "),updated_by=" . $this->session->userdata('uid') . ",updated_time='" . date("Y-m-d H:i:s") . "' WHERE id_customer=" . $data['id_customer'];

		$status = $this->db->query($sql);

		return $status;
	}

	//Update Wallet Account





	function getAdvanceAdjusted_Details($bill_id)

	{

		$advance_adjusted = $this->db->query("SELECT utilized_amt as adj_amt FROM ret_advance_utilized WHERE bill_id=" . $bill_id . "");

		return $advance_adjusted->row()->adj_amt;
	}



	function getCompanyPurchaseAmount($id_customer)

	{

		$sql = $this->db->query("SELECT IFNULL(SUM(d.item_cost),0) as tot_purchase_amt

        FROM ret_billing b

        LEFT JOIN ret_bill_details d ON d.bill_id=b.bill_id

        WHERE b.bill_status=1

        " . ($id_customer != '' ? " and b.bill_cus_id=" . $id_customer . "" : '') . "");

		return $sql->row_array();
	}





	//Incentive Report

	function getTagDetails($tag_id, $estimation_id)

	{

		$sql = $this->db->query("SELECT tag.tag_id,c.id_metal,e.created_by as id_employee

        FROM ret_taging tag

        LEFT JOIN ret_product_master p on p.pro_id=tag.product_id

        LEFT JOIN ret_category c ON c.id_ret_category=p.cat_id

        LEFT JOIN metal m ON m.id_metal=c.id_metal

        LEFT JOIN ret_estimation_items est on est.tag_id=tag.tag_id

        LEFT JOIN ret_estimation e on e.estimation_id=est.esti_id

        WHERE tag.tag_mark=1 and tag.tag_id=" . $tag_id . " and est.est_item_id=" . $estimation_id . "");

		//print_r($this->db->last_query());exit;

		return $sql->row_array();
	}



	function get_wallet_account($id_employee)

	{

		$return_data = array();

		$sql = $this->db->query("SELECT * FROM wallet_account WHERE idemployee=" . $id_employee);

		if ($sql->num_rows() > 0) {

			$return_data = array('status' => true, 'message' => 'Account Already Exist', 'id_wallet_account' => $sql->row()->id_wallet_account);
		} else {

			$return_data = array('status' => false);
		}

		return $return_data;
	}



	function get_wallet_acc_number()

	{

		$query = $this->db->query("SELECT LPAD(round(rand() * 10000000),8,0) as myCode

								FROM wallet_account

								HAVING myCode NOT IN (SELECT wallet_acc_number FROM wallet_account) limit 0,1");

		if ($query->num_rows() == 0) {

			$query = $this->db->query("SELECT LPAD(round(rand() * 10000000),8,0) as myCode");
		}

		return $query->row()->myCode;
	}



	function getWalletTransDetails($bill_id)

	{

		$sql = $this->db->query("SELECT * FROM `wallet_transaction` WHERE bill_id=" . $bill_id . "");

		return $sql->result_array();
	}



	function getWalletTransTagDetails($tag_id)

	{

		$sql = $this->db->query("SELECT * FROM `wallet_transaction` WHERE ref_no=" . $tag_id . "");

		return $sql->row_array();
	}

	function get_charges($tag_id)

	{

		$sql = $this->db->query("SELECT rtc.tag_charge_id, rtc.tag_id, rtc.charge_id, rtc.charge_value, c.code_charge, c.tag_display FROM ret_taging_charges AS rtc LEFT JOIN ret_charges AS c ON rtc.charge_id = c.id_charge  WHERE tag_id=" . $tag_id);

		return $sql->result_array();
	}

	//Incentive Report

	function get_other_estcharges($est_item_id)

	{

		$sql = $this->db->query("

		SELECT e.id_charge,IFNULL(e.amount,0) AS charge_value,s.code_charge, e.est_item_id

        FROM ret_estimation_other_charges e

        LEFT JOIN ret_charges s on s.id_charge=e.id_charge

	    WHERE e.est_item_id=" . $est_item_id . "");

		return $sql->result_array();
	}





	//metal details

	function get_metal_details($id_metal)

	{

		$sql = $this->db->query("SELECT * FROM `metal` WHERE id_metal=" . $id_metal . "");

		return $sql->row_array();
	}

	//metal details



	function get_employee_settings($id_employee)

	{

		$sql = $this->db->query("SELECT *,CONCAT(e.firstname,' ',e.lastname) as emp_name
		FROM `employee_settings` s
		LEFT JOIN employee e on e.id_employee = s.id_employee
		WHERE s.id_employee=" . $id_employee . "");



		return $sql->row_array();
	}



	function get_profile_settings($id_profile)

	{

		$sql = $this->db->query("SELECT * FROM `profile` WHERE id_profile='" . $id_profile . "'");

		return $sql->row_array();
	}







	function get_InventoryCategory($id_other_item_type)

	{

		$id_other_item_type = $this->db->query("SELECT t.id_other_item_type,t.qrcode,i.issue_preference

        FROM ret_other_inventory_item i

        LEFT JOIN ret_other_inventory_item_type t ON t.id_other_item_type=i.item_for

        WHERE i.id_other_item=" . $id_other_item_type . "");

		return $id_other_item_type->row_array();
	}



	function get_other_inventory_purchase_items_details($id_other_item, $id_branch, $issue_preference, $total_pcs)

	{

		$sql = $this->db->query("SELECT * FROM `ret_other_inventory_purchase_items_details`

        WHERE other_invnetory_item_id=" . $id_other_item . " AND current_branch=" . $id_branch . " AND status=0

        " . ($issue_preference == 1 ? 'order by pur_item_detail_id ASC' : 'order by pur_item_detail_id DESC') . "

        LIMIT " . $total_pcs . "");

		//print_r($this->db->last_query());exit;

		return $sql->result_array();
	}



	function get_old_estimation_details($est_old_itm_id)

	{

		$sql = $this->db->query("SELECT * FROM ret_estimation_old_metal_sale_details s WHERE s.old_metal_sale_id=" . $est_old_itm_id . "");

		return $sql->row_array();
	}





	//Purchase Item Stock Summary

	/* function checkPurchaseItemStockExist($data)

    {

		$r = array("status" => FALSE);

        $sql = "SELECT * FROM `ret_purchase_item_stock_summary`  WHERE type=".$data['type']."  ".(isset($data['id_old_metal_type']) && $data['id_old_metal_type']!='' ? " and id_old_metal_type=".$data['id_old_metal_type']."" :'')." ".(isset($data['id_product']) && $data['id_product']!='' ? " and id_product=".$data['id_product']."" :'')." ".($data['id_branch']!='' ?  " and id_branch=".$data['id_branch']."" :'')."  ";

        $res = $this->db->query($sql);



		if($res->num_rows() > 0){

			$r = array("status" => TRUE,);

		}else{

			$r = array("status" => FALSE);

		}

		return $r;

	}



	function get_return_bill_details($bill_detail_id)

	{

	    $sql=$this->db->query("SELECT * FROM ret_bill_details d WHERE d.bill_det_id=".$bill_detail_id."");

	    return $sql->row_array();

	}





	function updatePurItemData($data,$arith){

		$sql = "UPDATE ret_purchase_item_stock_summary SET pieces=(pieces".$arith." ".$data['pieces']."),gross_wt=(gross_wt".$arith." ".$data['gross_wt']."),less_wt=(less_wt".$arith." ".$data['less_wt']."),net_wt=(net_wt".$arith." ".$data['net_wt']."),updated_by=".$data['updated_by'].",updated_on='".$data['updated_on']."' WHERE id_product=".$data['id_product']." and id_branch=".$data['id_branch']."";

		$status = $this->db->query($sql);

		return $status;

	}

	*/



	function updatePurItemData($id_stock_summary, $data, $arith)
	{

		$sql = "UPDATE ret_purchase_item_stock_summary SET pieces=(pieces" . $arith . " " . $data['pieces'] . "),gross_wt=(gross_wt" . $arith . " " . $data['gross_wt'] . "),less_wt=(less_wt" . $arith . " " . $data['less_wt'] . "),net_wt=(net_wt" . $arith . " " . $data['net_wt'] . "),updated_by=" . $data['updated_by'] . ",updated_on='" . $data['updated_on'] . "' WHERE id_stock_summary=" . $data['id_stock_summary'] . " ";

		$status = $this->db->query($sql);

		return $status;
	}



	//Purchase Item Stock Summary



	function get_one_time_pre_weight_scheme()

	{

		$sql = $this->db->query("SELECT * FROM `scheme` WHERE one_time_premium=1 AND flexible_sch_type=4 or flexible_sch_type=5");

		return $sql->result_array();
	}



	function get_customer_weight_scheme_details($data)

	{

		$sql = $this->db->query("SELECT s.id_scheme_account,IFNULL(pay.paid_installments,0) as paid_installments,s.id_scheme,sch.total_installments,s.account_name

        FROM scheme_account s

        left join scheme sch on sch.id_scheme=s.id_scheme

        LEFT JOIN (

                select s.total_installments,s.scheme_type,s.id_scheme,s.firstPayDisc_value,sa.id_scheme_account,IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)as paid_installments

                FROM payment p

                left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account

                left join scheme s on s.id_scheme=sa.id_scheme

                where p.payment_status=1 AND s.id_scheme=" . $data['id_scheme'] . "

                group by sa.id_scheme_account

        ) as pay on pay.id_scheme_account=s.id_scheme_account



        WHERE s.id_scheme=" . $data['id_scheme'] . " AND s.id_customer=" . $data['id_customer'] . "

        HAVING  total_installments>paid_installments");

		//print_r($this->db->last_query());exit;

		return $sql->result_array();
	}



	function getChitPayDetails($bill_id)

	{

		$sql = $this->db->query("SELECT * FROM `payment_old_metal` WHERE bill_id=" . $bill_id . "");

		return $sql->row_array();
	}





	function get_purity_details($purity)

	{

		$sql = $this->db->query("SELECT * FROM ret_purity WHERE purity='" . $purity . "'");

		return $sql->row_array();
	}





	//bank account details

	function get_bank_acc_details()

	{

		$sql = $this->db->query("SELECT concat(short_code,' ',acc_number) as acc_number,address,id_bank

            FROM bank

            WHERE acc_number is NOT null");

		return $sql->result_array();
	}



	function get_payment_device_details()

	{

		$sql = $this->db->query("SELECT * FROM `ret_bill_pay_device` WHERE status=1");

		return $sql->result_array();
	}



	//bank account details



	function get_customer_reg_add($id_customer)

	{

		$sql = $this->db->query("SELECT a.id_address,IFNULL(a.id_country,'') as id_country,a.id_city,a.address1,a.address2,a.address3,a.pincode,c.name as country_name,s.name as state_name,

		cy.name as city_name,IFNULL(a.id_state,'') as id_state

		FROM address a

		LEFT JOIN country c ON c.id_country=a.id_country

		LEFT JOIN state s ON s.id_state=a.id_state

		LEFT JOIN city cy ON cy.id_city=a.id_city

		WHERE a.id_customer=" . $id_customer . "");

		return $sql->row_array();
	}



	function getCusDelivery_address($id_delivery_addr)

	{

		$sql = $this->db->query("SELECT d.id_delivery_addr as id_delivery,d.id_customer,d.id_country,d.id_state,IFNULL(d.address1,'') as address1,IFNULL(d.address2,'') as address2,IFNULL(d.address3,'') as address3,d.pincode,d.address_name,c.name as country_name,s.name as state_name,cy.name as city_name,

		IFNULL(d.id_city,'') as id_city

		FROM customer_delivery_address d

		LEFT JOIN country c ON c.id_country=d.id_country

		LEFT JOIN state s ON s.id_state=d.id_state

		LEFT JOIN city cy on cy.id_city=d.id_city

		WHERE d.id_delivery_addr=" . $id_delivery_addr . " ");

		return $sql->row_array();
	}



	function get_mydelivery_address($id_customer)

	{

		$sql = $this->db->query("SELECT d.id_delivery_addr as id_delivery,d.id_customer,d.id_country,d.id_state,IFNULL(d.address1,'') as address1,IFNULL(d.address2,'') as address2,IFNULL(d.address3,'') as address3,d.pincode,d.address_name,c.name as country_name,s.name as state_name,cy.name as city_name

		FROM customer_delivery_address d

		LEFT JOIN country c ON c.id_country=d.id_country

		LEFT JOIN state s ON s.id_state=d.id_state

		LEFT JOIN city cy on cy.id_city=d.id_city

		WHERE d.id_customer=" . $id_customer . " and d.address_name is not null");

		return $sql->result_array();
	}





	function getAvailableIndCustomers($SearchTxt)
	{

		$billing_for = $_POST['billing_for'];



		$data = $this->db->query("SELECT c.id_customer as value, concat(firstname,'-',mobile) as label, mobile,firstname, id_branch,v.village_name,if(c.is_vip=1,'Yes','No') as vip,c.id_village,c.is_vip,

			(select count(sa.id_scheme_account) from scheme_account sa where sa.id_customer=c.id_customer) as accounts,

			addr.id_country,addr.id_state,addr.id_city,addr.address1,addr.address2,addr.address3,addr.pincode,ct.name as country_name,s.name as state_name,cy.name as city_name,

			c.email,IFNULL(c.pan,'') as pan_no,IFNULL(c.aadharid,'') as aadharid,IFNULL(c.gst_number,'') as gst_number,IFNULL(c.id_village,'') as id_village,c.title,

			IFNULL(c.id_profession,'') as id_profession,c.gender,IFNULL(date_format(c.date_of_birth,'%d-%m-%Y'),'') as date_of_birth,IFNULL(date_format(c.date_of_wed,'%d-%m-%Y'),'') as date_of_wed,IFNULL(c.driving_license_no,'') as dl_no,IFNULL(c.passport_no,'') as pp_no

			FROM customer c

			LEFT JOIN village v on v.id_village=c.id_village

			LEFT JOIN address addr on addr.id_customer=c.id_customer

            LEFT JOIN country ct ON ct.id_country=addr.id_country

            LEFT JOIN state s ON s.id_state=addr.id_state

            LEFT JOIN city cy ON cy.id_city=addr.id_city

			WHERE c.id_customer='" . $SearchTxt . "'

			" . ($billing_for == 2 ? " and c.cus_type=2" : '') . "");

		//print_r($this->db->last_query());exit;

		return $data->result_array();
	}


	/*
	function get_active_bill_list($bill_no, $branch,$fin_year)

	{

	    	$sql=$this->db->query("SELECT p.id_pay_device,p.payment_mode,p.payment_amount,p.bill_id,b.bill_no,b.id_branch,p.card_no,c.firstname,c.mobile,b.tot_amt_received,p.NB_type,

	    	p.cheque_date,p.cheque_no,p.card_no,p.payment_ref_number,Date_format(p.payment_date,'%Y-%m%-%d ') as payment_date,p.bill_id,p.created_by,

			b.tot_bill_amount

			from ret_billing b

			left join ret_billing_payment p on p.bill_id=b.bill_id

			left join customer c on c.id_customer=b.bill_cus_id

			WHERE b.bill_no='".$bill_no."' and b.id_branch='".$branch."'

			and b.bill_status=1 and b.fin_year_code='".$fin_year."'");

		    return $sql->result_array();

	}*/

	function get_active_bill_list($bill_id, $branch, $fin_year)
	{
		$type = '';
		$sql = $this->db->query("SELECT p.id_pay_device,p.payment_mode,IFNULL(p.payment_amount,0) as payment_amount,p.bill_id,
			b.id_branch,p.card_no,b.customer_name as firstname,c.mobile,concat(c.firstname,' ',c.mobile) as cus_name,
			p.NB_type,
	    	Date_format(p.cheque_date, '%d-%m-%Y') as cheque_date,p.cheque_no,p.card_no,p.payment_ref_number,Date_format(p.card_date,'%d-%m-%Y') as card_date,Date_format(p.payment_date,'%d-%m-%Y') as payment_date,p.bill_id,p.created_by,p.id_bank,
			b.tot_bill_amount,concat(br.short_name,'-',if(IFNULL(b.sales_ref_no,'')!='',concat('SA-',b.sales_ref_no),if(IFNULL(b.pur_ref_no,'')!='',concat('PU-',b.pur_ref_no),if(IFNULL(b.s_ret_refno,'')!='',concat('SR-',b.s_ret_refno),if(b.bill_type=5,concat('OD-',b.order_adv_ref_no),if(b.bill_type=8,concat('CC-',b.credit_coll_refno),b.bill_no)))))) as bill_no,
			b.bill_id as bill, b.id_employee as id_employee, b.bill_cus_id as bill_cus_id, b.pan_no AS pan_no, b.gst_number AS gst_no, b.aadhar_no AS aadhaar_no, b.billing_for AS billing_for, IFNULL(b.cusdel_irn,'') AS cusdel_irn, Date_format(p.net_banking_date,'%d-%m-%Y') as nb_date,
			(
				SELECT SUM(IFNULL(p2.payment_amount, 0))
				FROM ret_billing_payment p2
				WHERE p2.bill_id = b.bill_id
			) AS tot_amt_received
			from ret_billing b
			left join ret_billing_payment p on p.bill_id=b.bill_id
			left join customer c on c.id_customer=b.bill_cus_id
			LEFT JOIN branch br on br.id_branch=b.id_branch
			LEFT JOIN employee as emp ON emp.id_employee = b.id_employee
			WHERE b.bill_id='" . $bill_id . "'
			and b.bill_status=1 ");
		$result =  $sql->result_array();
		foreach ($result as $val) {

			$val['bill_no'] = $this->get_bill_no_format_detail($val['bill'], $type);

			$return_data[] = $val;
		}

		return $return_data;
	}



	function get_active_bill($bill_id)
	{
		$sql = $this->db->query("SELECT bill_no from ret_billing WHERE bill_id='" . $bill_id . "' and bill_status=1");
		return $sql->num_rows();
	}



	function get_customer_credit_details($data)

	{

		$profile_settings=$this->get_profile_settings($this->session->userdata('profile'));

		$sql = $this->db->query("SELECT r.bill_no,r.id_issue_receipt,r.amount as issue_amt,IFNULL(coll.paid_amt,0) as paid_amt,

        IFNULL(r.amount-IFNULL(coll.paid_amt,0)-IFNULL(coll.discount_amt,0),0) as balance_amount,IFNULL(coll.discount_amt,0) as discount_amt

        FROM ret_issue_receipt r

        LEFT JOIN (SELECT IFNULL(SUM(c.received_amount),0) as paid_amt,c.receipt_for,IFNULL(SUM(c.discount_amt),0) as discount_amt

                  FROM ret_issue_credit_collection_details c

                  LEFT JOIN ret_issue_receipt rct ON rct.id_issue_receipt=c.receipt_for

                  LEFT JOIN ret_issue_receipt rc ON rc.id_issue_receipt=c.id_issue_receipt

                  where rc.bill_status=1

                  GROUP by c.receipt_for) as coll ON coll.receipt_for=r.id_issue_receipt

        WHERE r.type=1 AND r.is_collect=0

		" . ($data['receipt_type'] == 8 ? 'and r.issue_type=1 and r.is_closed = 0' : 'and (r.issue_type=2 or r.issue_type=4)') . "

		" . ($data['id_customer'] != '' &&  $data['receipt_type'] != 8 ? " and r.id_customer=" . $data['id_customer'] . "" : '') . "

		" . ($data['id_employee'] != '' &&  $data['receipt_type'] == 8   &&  $data['receipt_to'] == 1 ? " and r.id_employee=" . $data['id_employee'] . "" : '') . "

		" . ($data['id_karigar'] != '' &&  $data['receipt_type'] == 8  &&  $data['receipt_to'] == 2 ? " and r.id_karigar=" . $data['id_karigar'] . "" : '') . "

		".($profile_settings['allow_bill_type']==3 ? " and (r.is_eda=1 OR r.is_eda=2)" : ($profile_settings['allow_bill_type']==1 ? " and r.is_eda=1" :" and r.is_eda=2") )."

        HAVING balance_amount>0 ");

		//print_r($this->db->last_query());exit;

		return $sql->result_array();
	}

	function get_mc_va_limit($id_product, $id_design, $id_sub_design, $grossWt = 0, $lot_no = "", $id_branch = "", $netWt = 0)
	{

		$is_va_mc_based_on_branch = $this->get_ret_settings('is_va_mc_based_on_branch');

		$mc_min = 0;

		$wastag_min = 0;

		$wastag_method = 0;

		$wastag_min_wt = 0;

		$margin_mrp = 0;

		$po_details = array();

		$po_mc = 0;

		$po_va = 0;

		$mc_cal_type = 0;

		$where = "";

		if ($id_sub_design > 0) {

			$where  =  " AND id_sub_design = " . $id_sub_design;

			if ($lot_no > 0 && $lot_no != "") {

				$po_details = $this->get_purchase_details($id_product, $id_design, $id_sub_design, $lot_no);

				$po_mc = is_numeric($po_details[0]['mc_value']) && $po_details[0]['mc_value'] > 0 ? $po_details[0]['mc_value'] : 0;

				$po_va = is_numeric($po_details[0]['item_wastage']) && $po_details[0]['item_wastage'] > 0 ? $po_details[0]['item_wastage'] : 0;
			}
		}



		$sql = "SELECT

					id_selling_settings,

					type,

					wastag_method,

					mc_type as mc_cal_type,

                    IFNULL(mc_min,0) AS mc_min,

                    IFNULL(wastag_min ,0) AS wastag_min,

					IFNULL(wastag_min_wt ,0) AS wastag_min_wt,

					IFNULL(margin_mrp ,0) AS margin_mrp

                FROM ret_selling_settings

                WHERE 1

				".($is_va_mc_based_on_branch == 1 ? " AND id_branch = '" . $id_branch . "'" : '')."

				 AND id_product = " . $id_product . "

				 AND id_design=" . $id_design . $where;

		//echo $sql;exit;


		$query_details = $this->db->query($sql);

		if ($query_details->num_rows() > 0) {

			$row = $query_details->row_array();;

			$id_selling_settings = $row['id_selling_settings'];

			$mc_cal_type = $row['mc_cal_type'];

			$type = $row['type'];

			$margin_mrp = $row['margin_mrp'];

			if ($type == 1) {

				$mc_min = $row['mc_min'];

				$wastag_method = $row['wastag_method'];

				$wastag_min = $row['wastag_min'];

				$wastag_min_wt = $row['wastag_min_wt'];

			} else if ($type == 2) {

				if ($grossWt != "") {

					$sql = "SELECT

								wc_method AS wastag_method,

								IFNULL(mcrg_min,0) AS mc_min,

								IFNULL(wc_min,0) AS wastag_min,

								IFNULL(wc_min_wt,0) AS wastag_min_wt

							FROM ret_design_weight_range_wc

							WHERE id_selling_settings = " . $id_selling_settings . " AND (" . $grossWt . " >= wc_from_weight AND " . $grossWt . " <= wc_to_weight)";

					$query_details = $this->db->query($sql);

					if ($query_details->num_rows() > 0) {

						$row = $query_details->row_array();

						$wastag_method = $row['wastag_method'];

						$mc_min = is_numeric($row['mc_min']) && $row['mc_min'] > 0 ? $row['mc_min'] : 0;

						$wastag_min = is_numeric($row['wastag_min']) && $row['wastag_min'] > 0 ? $row['wastag_min'] : 0;

						$wastag_min_wt = is_numeric($row['wastag_min_wt']) && $row['wastag_min_wt'] > 0 ? $row['wastag_min_wt'] : 0;
					}
				}
			}
		}

		if ($wastag_method == 2) {

			$wast_perc  = $netWt > 0 ? round((($wastag_min_wt * 100) / $netWt ), 2) : 0;

			$wastag_min = $wast_perc;
		}

		$mc_min = $mc_min > $po_mc ? $mc_min : $po_mc;

		$wastag_min = $wastag_min > $po_va ? $wastag_min : $po_va;

		$result_arr = array("mc_min" => $mc_min, "mc_cal_type" => $mc_cal_type, "wastag_min" => $wastag_min, "margin_mrp" => $margin_mrp);

		return $result_arr;
	}



	function getBillDetailsData($billId)
	{



		$det_query = $this->db->query("SELECT sum(total_sgst), sum(total_cgst), sum(total_igst), sum(item_total_tax), sum(item_cost), sum(CAST(((item_cost - item_total_tax) + total_sgst + total_cgst + total_igst) AS DECIMAL(10,2))) as itemwithtax FROM ret_bill_details WHERE bill_id = '" . $billId . "' GROUP BY bill_id");

		return $det_query->row_array();
	}

	/**

	 * Getting purchase details from tagging model.

	 *

	 * Created By : Vivek. Created On : 29-08-2022

	 *

	 */

	function get_purchase_details($product_id, $design_id, $subdesign_id, $lot_no)
	{

		$pur_details = array();

		if ($product_id > 0 && $design_id > 0 && $subdesign_id > 0 && $lot_no > 0) {

			$data['product_id'] 	= $product_id;

			$data['design_id'] 		= $design_id;

			$data['subdesign_id'] 	= $subdesign_id;

			$data['lot_no'] 		= $lot_no;

			$CI = &get_instance();

			$CI->load->model('ret_tag_model');

			$pur_details = $CI->ret_tag_model->getPoDetailsforPC($data);
		}

		return $pur_details;
	}





	//service bill type

	function service_bill_number_generator($id_branch)

	{

		$lastno = $this->get_last_service_bill_no($id_branch);

		if ($lastno != NULL && $lastno != '') {

			$number = (int) $lastno;

			$number++;

			$code_number = str_pad($number, 5, '0', STR_PAD_LEFT);

			return $code_number;
		} else {

			$code_number = str_pad('1', 5, '0', STR_PAD_LEFT);

			return $code_number;
		}
	}

	function get_last_service_bill_no($id_branch)

	{

		$fin_year = $this->get_FinancialYear();

		$sql = "SELECT (bill_no) as lastBill_no

		FROM ret_service_bill

		where fin_year_code=" . $fin_year['fin_year_code'] . "

		" . ($id_branch != '' && $id_branch > 0 ? " and id_branch=" . $id_branch . "" : '') . "

				ORDER BY id_service_bill DESC LIMIT 1";



		//print_r($sql);exit;

		return $this->db->query($sql)->row()->lastBill_no;
	}



	function ajax_getServiceBillList($data)

	{



		if ($_POST['dt_range'] != '') {

			$dateRange = explode('-', $_POST['dt_range']);

			$from = str_replace('/', '-', $dateRange[0]);

			$to = str_replace('/', '-', $dateRange[1]);

			$d1 = date_create($from);

			$d2 = date_create($to);

			$FromDt = date_format($d1, "Y-m-d");

			$ToDt = date_format($d2, "Y-m-d");
		}

		$sql = $this->db->query("SELECT b.id_service_bill,b.bill_no,date_format(b.bill_date,'%d-%m-%Y') as bill_date,b.total_bill_amount,if(b.bill_status=1,'Success','Cancelled') as billStatus,

        b.bill_status,c.firstname as cus_name,c.mobile,br.name as branch_name,if(date(d.entry_date)=date(b.bill_date),'1','0') as allow_cancel

        FROM ret_service_bill b

        LEFT JOIN customer c ON c.id_customer = b.id_customer

        LEFT JOIN branch br ON br.id_branch = b.id_branch

        LEFT JOIN ret_day_closing d on d.id_branch=b.id_branch

        where  " . ($data['dt_range'] != '' ? 'date(b.bill_date) BETWEEN "' . $FromDt . '" AND "' . $ToDt . '"' : '') . "

        " . ($data['bill_no'] != '' ? " and b.bill_no=" . $data['bill_no'] . "" : '') . "

        " . ($data['id_branch'] != 0 && $data['id_branch'] != '' ? " and b.id_branch=" . $data['id_branch'] . "" : '') . "

         ORDER BY b.id_service_bill desc");

		//echo $this->db->last_query();exit;

		return $sql->result_array();
	}


	function get_otp_profile_settings($id_profile)

	{

		$data=$this->db->query("SELECT pr.order_delivery_otp  FROM  profile pr  where pr.id_profile ='".$id_profile."'");

		return $data->row()->order_delivery_otp;

	}

	function getServiceBillingDetails($id)

	{

		$sql = $this->db->query("SELECT b.id_service_bill,b.bill_no,date_format(b.bill_date,'%d-%m-%Y') as bill_date,b.total_bill_amount,if(b.bill_status=1,'Success','Cancelled') as billStatus,

        b.bill_status,c.firstname as customer_name,c.mobile,br.name as branch_name,if(date(d.entry_date)=date(b.bill_date),'1','0') as allow_cancel,

        a.address1,a.address2,a.address3,ct.name as city,a.pincode,ct.name as city,s.name as state,cy.name as country,b.id_branch,b.total_bill_amount as tot_bill_amount

        FROM ret_service_bill b

        LEFT JOIN customer c ON c.id_customer = b.id_customer

        LEFT JOIN branch br ON br.id_branch = b.id_branch

        LEFT JOIN ret_day_closing d on d.id_branch=b.id_branch

        LEFT JOIN address a on a.id_customer=c.id_customer

        LEFT JOIN city ct on a.id_city=ct.id_city

		LEFT JOIN state s on s.id_state=a.id_state

		LEFT JOIN country cy on (a.id_country=cy.id_country)

        where  b.id_service_bill=" . $id . "

        ORDER BY b.id_service_bill desc");

		return $sql->row_array();
	}



	function getServiceBillPaymentDetails($bill_id)

	{

		$pay_details = array("pay_details" => array());

		$items_query = $this->db->query("SELECT p.id_billing_payment,p.type,p.payment_for,p.payment_amount,p.card_no,p.cvv,p.payment_mode,IFNULL(p.payment_ref_number,'') as payment_ref_number,

		if(p.NB_type=1,'RTGS',if(p.NB_type=2,'IMPS',if(p.NB_type=3,'UPI',if(p.NB_type=4,'NEFT','')))) as transfer_type

		FROM ret_service_bill_payment p

		where p.id_service_bill=" . $bill_id . "");

		$data = $items_query->result_array();

		foreach ($data as $items) {

			$pay_details['pay_details'][] = array(

				'id_billing_payment'    => $items['id_billing_payment'],

				'type'                  => $items['type'],

				'bill_id'               => $items['bill_id'],

				'payment_for'           => $items['payment_for'],

				'payment_amount'        => $items['payment_amount'],

				'cvv'                   => $items['cvv'],

				'card_no'               => $items['card_no'],

				'payment_mode'          => $items['payment_mode'],

				'payment_ref_number'    => $items['payment_ref_number'],

				'transfer_type'         => $items['transfer_type'],

			);
		}

		return $pay_details;
	}



	function getServiceBillItemDetails($id)

	{

		$sql = $this->db->query("SELECT p.product_name,m.name,d.piece,d.weight,d.item_total_tax,d.item_total_cost,d.total_cgst,d.total_sgst,d.tax_percentage

        FROM ret_service_bill_details d

        LEFT JOIN ret_service_bill b ON b.id_service_bill = d.bill_detail_id

        LEFT JOIN ret_product_master p ON p.pro_id = d.id_product

        LEFT JOIN ret_repair_master m ON m.id_repair_master = d.id_service

        Where d.id_service_bill=" . $id . "");

		return $sql->result_array();
	}





	function get_repair_item_details($bill_id)

	{

		$repair_order_details = $this->db->query("SELECT IFNULL(SUM(od.rate),0) as amount,IFNULL(SUM(od.total_sgst),0) sgst,IFNULL(SUM(od.total_cgst),0) cgst,IFNULL(SUM(od.total_igst),0) igst,IFNULL(SUM(od.repair_tot_tax),0) as repair_tot_tax,(IFNULL(SUM(od.rate),0)-IFNULL(SUM(od.repair_tot_tax),0)) as taxable_amount,od.repair_percent

        FROM customerorderdetails od

        LEFT JOIN customerorder c ON c.id_customerorder=od.id_customerorder

        LEFT JOIN ret_product_master p ON p.pro_id=od.id_product

        LEFT JOIN ret_design_master des ON des.design_no=od.design_no

        LEFT JOIN ret_billing b ON b.bill_id=od.bill_id

        WHERE od.bill_id=" . $bill_id . "");

		return $repair_order_details->row_array();
	}

	//service bill type



	function get_headOffice()

	{

		$data = $this->db->query("SELECT b.is_ho,b.id_branch,name FROM branch b where b.is_ho=1");

		return $data->row_array();
	}



	function get_approval_tag_details($tag_id)

	{

		$sql = $this->db->query("SELECT * FROM ret_taging WHERE tag_id = '" . $tag_id . "' ");

		return $sql->row_array();
	}



	function get_order_id_details($bill_id)
	{

		$order_id = $this->db->query("SELECT id_orderdetails FROM ret_bill_details WHERE bill_id=" . $bill_id . "");

		return  $order_id->result_array();
	}





	function getCustomerDet($id_branch, $id_customer)

	{

		$return_data = array();

		$data = $this->db->query("SELECT c.firstname,c.id_customer,c.is_vip,c.mobile,v.village_name,b.name as branch_name,IFNULL(esti.tot_est,0) as estimation_no,

        IFNULL(bill_tot.bill_count,0) as bill_count,IFNULL(tot_acc.tot_acc,0) as tot_account,IFNULL(iactive.tot_acc,0)as inactive_acount,

        IFNULL(tot_gold.gold_wt,0) as gold_wt,IFNULL(tot_silver.silver_wt,0) as silver_wt,IFNULL(closed_chit.closed_count,0) as closed_count,IFNULL(tot_payment.pay_amount,0) as tot_amount,

        IFNULL(fixed_rate.item_cost,0) as tot_fixed_rate,IFNULL(active_acc.tot_acc,0) as active_acc,IFNULL(DATE_FORMAT(max(bill.bill_date),'%d-%m-%Y'),'-') as last_billdate,z.name as zone_name, c.pan AS pan_no, c.gst_number as gst_number, c.aadharid as aadhar_no,

		IFNULL(c.passport_no,'') as pp_no, IFNULL(c.driving_license_no,'') as dl_no

        from customer c
        
        left join ret_billing bill on (c.id_customer=bill.bill_cus_id)

        left join branch b on (b.id_branch=c.id_branch)

        LEFT JOIN village v on (v.id_village=c.id_village)

        LEFT JOIN village_zone z on (z.id_zone=v.id_zone)
        
        left join (select count(est.estimation_id) as tot_est,est.cus_id from ret_estimation est

                   left join customer as c on c.id_customer=est.cus_id where cus_id=" . $id_customer . ")as esti on esti.cus_id=c.id_customer


        left join (select COUNT(bill.bill_id) as bill_count,bill.bill_cus_id from ret_billing as bill

            left join customer as c on c.id_customer=bill.bill_cus_id

            where bill.bill_status=1 and c.id_customer=" . $id_customer . "

            ) as bill_tot on bill_tot.bill_cus_id=c.id_customer


        left join(select count(sa.id_scheme_account) as tot_acc,sa.id_customer,c.mobile from scheme_account sa

            left join customer c on (c.id_customer=sa.id_customer)

            where sa.scheme_acc_number is not null and c.id_customer=" . $id_customer . ") as tot_acc on tot_acc.id_customer=c.id_customer


        left join(select count(sa.id_scheme_account) as tot_acc,sa.id_customer,c.mobile from scheme_account sa

            left join customer c on (c.id_customer=sa.id_customer)

            where sa.scheme_acc_number is not null and sa.is_closed=0 and c.id_customer=" . $id_customer . ") as active_acc on active_acc.id_customer=c.id_customer


        left join(select count(sa.scheme_acc_number) as closed_count,sa.id_customer,c.mobile from scheme_account sa

            left join customer c on (c.id_customer=sa.id_customer)

            where sa.is_closed=1 and c.id_customer=" . $id_customer . ") as closed_chit on closed_chit.id_customer=c.id_customer


        left join (SELECT COUNT(sa.id_scheme_account) as tot_acc,

            TIMESTAMPDIFF(month, max(p.date_add), current_date()) as month_ago,sa.id_customer

            FROM scheme_account sa

            LEFT JOIN payment p ON p.id_scheme_account=sa.id_scheme_account

            LEFT JOIN scheme s ON s.id_scheme=sa.id_scheme

            LEFT JOIN customer cus ON cus.id_customer=sa.id_customer

            WHERE sa.is_closed=0

            and cus.id_customer=" . $id_customer . " HAVING month_ago>3) as iactive on iactive.id_customer=c.id_customer

        left join(select sum(bill_det.net_wt) as gold_wt,c.id_customer from ret_billing as bill

            left JOIN ret_bill_details  as bill_det on(bill_det.bill_id=bill.bill_id)

            left join ret_product_master as pro on(pro.pro_id=bill_det.product_id)

            left join ret_category as cat on(cat.id_ret_category=pro.cat_id)

            left join metal as m on(m.id_metal=cat.id_metal)

            left join customer as c on(c.id_customer=bill.bill_cus_id)

            left join branch b on (b.id_branch=c.id_branch)

            LEFT join village v on (v.id_village=c.id_village)

            LEFT JOIN village_zone z on (z.id_zone=v.id_zone)

            where bill.bill_status=1 and m.id_metal=1

            and c.id_customer=" . $id_customer . "

             ) as tot_gold on tot_gold.id_customer=c.id_customer


        left join(SELECT sum(bill_det.net_wt) as silver_wt,c.id_customer from ret_billing as bill

            left JOIN ret_bill_details  as bill_det on(bill_det.bill_id=bill.bill_id)

            left join ret_product_master as pro on(pro.pro_id=bill_det.product_id)

            left join ret_category as cat on(cat.id_ret_category=pro.cat_id)

            left join metal as m on(m.id_metal=cat.id_metal)

            left join customer as c on(c.id_customer=bill.bill_cus_id)

            left join branch b on (b.id_branch=c.id_branch)

            LEFT join village v on (v.id_village=c.id_village)

            LEFT JOIN village_zone z on (z.id_zone=v.id_zone)

            where bill.bill_status=1 and m.id_metal=2 and c.id_customer=" . $id_customer . "

            ) as tot_silver on tot_silver.id_customer=c.id_customer


         left join(SELECT sum(bill_det.item_cost) as item_cost,c.id_customer from ret_billing as bill

            left JOIN ret_bill_details  as bill_det on(bill_det.bill_id=bill.bill_id)

            left join ret_product_master as pro on(pro.pro_id=bill_det.product_id)

            left join ret_category as cat on(cat.id_ret_category=pro.cat_id)

            left join metal as m on(m.id_metal=cat.id_metal)

            left join customer as c on(c.id_customer=bill.bill_cus_id)

            left join branch b on (b.id_branch=c.id_branch)

            LEFT join village v on (v.id_village=c.id_village)

            LEFT JOIN village_zone z on (z.id_zone=v.id_zone)

            where bill.bill_status=1 and pro.sales_mode=1 and c.id_customer=" . $id_customer . "

        	) as fixed_rate on fixed_rate.id_customer=c.id_customer


        left join(select sum(p.payment_amount) as pay_amount,c.id_customer,c.mobile from customer as c

            left join scheme_account as sa on(sa.id_customer=c.id_customer)

            left join payment as p on (p.id_scheme_account=sa.id_scheme_account)

            where c.id_customer=" . $id_customer . ") as tot_payment on tot_payment.id_customer=c.id_customer

            where c.id_customer=" . $id_customer . "");




			// print_r($this->db->last_query());exit;

		$return_data['cus_details'] = $data->result_array();

		$return_data['cus_details'][0]['firstname'] =$this->db->query("SELECT firstname FROM customer where id_customer =" . $id_customer . "")->row()->firstname;

		$sql = $this->db->query("SELECT b.bill_id,br.name as branch_name,IFNULL(g_wt.net_wt,0) as gold_wt,IFNULL(s_wt.net_wt,0) as silver_wt,IFNULL(fixed_rate.item_cost,0) as mrp_amount,DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date,b.tot_bill_amount,if(b.bill_status=1,'Success','Cancelled') as bill_status

        FROM ret_billing b

        LEFT JOIN customer c ON c.id_customer=b.bill_cus_id

        LEFT JOIN ret_bill_details d ON d.bill_id=b.bill_id

		LEFT JOIN branch br on br.id_branch=b.id_branch



        left JOIN (select d.net_wt,b.bill_id from ret_billing b

                  left join customer c ON c.id_customer=b.bill_cus_id

                  left JOIN ret_bill_details d ON d.bill_id=b.bill_id

                  left join ret_product_master as pro on pro.pro_id=d.product_id

            	  left join ret_category as cat on cat.id_ret_category=pro.cat_id

            	  left join metal as m on m.id_metal=cat.id_metal WHERE b.bill_status=1

                  and m.id_metal=1 and d.bill_det_id is NOT NULL and c.id_customer=" . $id_customer . ") as g_wt

                  ON g_wt.bill_id=b.bill_id



          left JOIN (select d.net_wt,b.bill_id from ret_billing b

                  left join customer c ON c.id_customer=b.bill_cus_id

                  left JOIN ret_bill_details d ON d.bill_id=b.bill_id

                  left join ret_product_master as pro on pro.pro_id=d.product_id

            	  left join ret_category as cat on cat.id_ret_category=pro.cat_id

            	  left join metal as m on m.id_metal=cat.id_metal WHERE b.bill_status=1

                  and m.id_metal=2 and d.bill_det_id is NOT NULL and c.id_customer=" . $id_customer . ") as s_wt

                  ON s_wt.bill_id=b.bill_id



          left JOIN (select d.item_cost,b.bill_id from ret_billing b

                  left join customer c ON c.id_customer=b.bill_cus_id

                  left JOIN ret_bill_details d ON d.bill_id=b.bill_id

                  left join ret_product_master as pro on pro.pro_id=d.product_id

            	  left join ret_category as cat on cat.id_ret_category=pro.cat_id

            	  left join metal as m on m.id_metal=cat.id_metal WHERE b.bill_status=1

                  and pro.sales_mode=1 and d.bill_det_id is NOT NULL and c.id_customer=" . $id_customer . ") as fixed_rate

                  ON fixed_rate.bill_id=b.bill_id



        WHERE b.bill_status=1 and d.bill_det_id is NOT null and c.id_customer=" . $id_customer . "

        GROUP by b.bill_id ORDER BY b.bill_date DESC LIMIT 5");



		$res = $sql->result_array();
		$return_data['bill_details'] = array();
		foreach($res as $r){
			$r['bill_no'] = $this->get_bill_no_format_detail($r['bill_id']);
			$return_data['bill_details'][] = $r;
		}

		$return_data['outstanding'] = $this->get_credit_pending_details($id_branch, $id_customer);



		return $return_data;
	}





	function get_credit_pending_details($id_branch, $id_customer)

	{

		$return_data = array();

		$credit_detail = array();

		$credit_detai2 = array();

		$sql = $this->db->query("SELECT b.tot_amt_received,b.bill_id,b.bill_no,b.bill_type,DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date,DATE_FORMAT(b.credit_due_date,'%d-%m-%Y') as credit_due_date,b.tot_bill_amount,b.tot_amt_received,b.bill_cus_id,c.mobile,c.firstname as cus_name,

		if(b.credit_status=1,'Paid','Pending') as credit_status,br.name as branch_name,b.tot_bill_amount,(b.tot_bill_amount-b.tot_amt_received-IFNULL(ret.credit_due_amt,0)) as bal_amt,b.credit_disc_amt,IFNULL(ret.credit_due_amt,0) as credit_due_amt,IFNULL(ret.total_returned,0) as total_returned

			from ret_billing b

			LEFT JOIN customer c on c.id_customer=b.bill_cus_id

			LEFT JOIN branch br on br.id_branch=b.id_branch

            LEFT JOIN(SELECT IFNULL((b.credit_due_amt),0) as credit_due_amt,

            r.ret_bill_id,IFNULL(b.credit_ret_amt,0) as credit_ret_amt,IFNULL(SUM(d.item_cost),0) as total_returned

            FROM ret_bill_return_details r

			LEFT JOIN ret_bill_details d ON d.bill_det_id = r.ret_bill_det_id

            LEFT JOIN ret_billing b ON b.bill_id = r.bill_id

            WHERE b.bill_status = 1

            GROUP BY r.ret_bill_id) as ret ON ret.ret_bill_id = b.bill_id

			where  b.bill_id is not null and b.is_credit=1 and b.is_to_be=0 and b.bill_status=1  and b.bill_type!=8 and b.credit_status=2 and b.bill_type!=12

			" . ($id_branch != '' && $id_branch > 0 ? ' and b.id_branch=' . $id_branch : '') . "

			" . ($id_customer != '' && $id_customer > 0 ? ' and b.bill_cus_id=' . $id_customer : '') . "



			ORDER BY b.bill_cus_id");

		// print_r($this->db->last_query());exit;

		$result = $sql->result_array();

		foreach ($result as $r) {

			$current_datetime = date('Y-m-d H:i:s');
			// print_r($r['bill_id']);print_r($r['bill_date_time']);exit;
			$credit_collection_details = $this->get_previous_credit_collections($r['bill_id'], $current_datetime,$r['bill_type']);

				$paid_amount = 0;
				if (!empty($credit_collection_details)) {
				    foreach ($credit_collection_details as $credit) {
				        $collection_amount = $credit['credit_disc_amt']
				            - $credit['advance_deposit']
				            + $credit['old_metal_amount']
				            + $credit['chit_amount']
				            + $credit['payment_amount']
				            + $credit['advance_adjusted'];
					
				        $paid_amount += $collection_amount;
				    }
				}

			//$paid_amount = $this->get_credit_collection_details($r['bill_id']);

			// print_r($paid_amount);exit;

			$credit_detail[] = array(

				'type'              => 0,

				'bill_no'           => $r['bill_no'],

				'bill_date'         => $r['bill_date'],

				'cus_name'          => $r['cus_name'],

				'mobile'            => $r['mobile'],

				'branch_name'       => $r['branch_name'],

				'tot_bill_amount'   => $r['tot_bill_amount'],

				'credit_due_amt'    => $r['credit_due_amt'],

				'credit_ret_amt'    => $r['credit_ret_amt'],

				'tot_amt_received'    => $r['tot_amt_received'],

				'bal_amt'           => $r['bal_amt'] - $paid_amount,

				'due_amount'        => $r['tot_bill_amount'] - $r['tot_amt_received'],

				'paid_amount'       => $paid_amount,

				'bill_id'           => $r['bill_id'],

				'total_returned'    => $r['total_returned'],

				'credit_collection' => $this->getCreditCollection($r['bill_id'])

			);
		}







		$issue_sql = $this->db->query("SELECT r.id_issue_receipt as bill_id,r.bill_no as bill_no,cus.mobile,r.amount as due_amount,

        DATE_FORMAT(r.bill_date,'%d-%m-%Y') as bill_date,'' as credit_due_date,IFNULL(r.amount-IFNULL(coll.paid_amt,0),0) as bal_amt,r.amount as tot_amt_received,

        IFNULL(coll.paid_amt,0) as paid_amount,cus.firstname as cus_name,br.name as branch_name,'1' as type,'0' as credit_ret_amt,r.amount as tot_bill_amount

        FROM ret_issue_receipt r

        LEFT JOIN branch br on br.id_branch=r.id_branch

        LEFT JOIN (SELECT IFNULL(SUM(c.received_amount+c.discount_amt),0) as paid_amt,c.receipt_for

        FROM ret_issue_receipt r

        LEFT JOIN ret_issue_credit_collection_details c ON c.id_issue_receipt=r.id_issue_receipt

        where r.bill_status=1

        GROUP by c.receipt_for) as coll ON coll.receipt_for=r.id_issue_receipt

        LEFT JOIN customer cus ON cus.id_customer=r.id_customer

        WHERE r.type=1  and r.bill_status=1 and (r.issue_type=2 or r.issue_type=4)

        " . ($id_customer != '' && $id_customer > 0 ? ' and r.id_customer=' . $id_customer : '') . "

        " . ($id_branch != '' && $id_branch > 0 ? ' and r.id_branch=' . $id_branch : '') . "

          ");

		//print_r($this->db->last_query());exit;

		$result1 = $issue_sql->result_array();

		foreach ($result1 as $r) {

			$issueCreditDetails = $this->get_IssueCreditCollectionDetails($r['bill_id']);

			$r['credit_collection'] = $issueCreditDetails;

			$credit_detai2[] = $r;
		}

		//echo "<pre>"; print_r($credit_detai2);exit;

		$return_data = array_merge($credit_detail, $credit_detai2);

		return $return_data;
	}



	function getCreditCollection($bill_id)

	{

		$return_data = array();

		$data = $this->db->query("SELECT b.bill_id,b.bill_no,b.bill_type,b.ref_bill_id,b.tot_amt_received,b.credit_disc_amt,

    	b.tot_bill_amount,DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date

    	 From ret_billing b

    	 where b.bill_status=1 and b.ref_bill_id=" . $bill_id . "");

		$items = $data->result_array();

		foreach ($items as $item) {

			$old_metal_details = $this->getOld_sales_detail($item['bill_id'], 8);

			$old_metal_amount = 0;

			foreach ($old_metal_details as $old_items) {

				$old_metal_amount += $old_items['amount'];
			}

			$return_data[] = array(

				'0'                => 'type',

				'bill_no'          => $item['bill_no'],

				'bill_id'          => $item['bill_id'],

				'bill_type'        => $item['bill_type'],

				'ref_bill_id'      => $item['ref_bill_id'],

				'tot_amt_received' => $item['tot_amt_received'],

				'credit_disc_amt'  => $item['credit_disc_amt'],

				'tot_bill_amount'  => ($item['tot_bill_amount'] + $old_metal_amount),

				'bill_date'        => $item['bill_date'],

				'old_metal_amount' => $old_metal_amount,

			);
		}

		return $return_data;
	}

	function get_IssueCreditCollectionDetails($bill_id)

	{

		$sql = $this->db->query("SELECT r.id_issue_receipt as bill_id,r.bill_no,date_format(r.bill_date,'%d-%m-%Y') as bill_date,r.amount as tot_amt_received,

        coll.discount_amt as credit_disc_amt,'0' as old_metal_amount,'1' as type

        FROM ret_issue_receipt r

        LEFT JOIN ret_issue_credit_collection_details coll ON coll.id_issue_receipt=r.id_issue_receipt

        WHERE r.receipt_type=1 and r.bill_status=1 AND coll.receipt_for=" . $bill_id . "");

		//print_r($this->db->last_query());exit;

		return $sql->result_array();
	}













	function getOld_sales_detail($bill_id, $bill_type)

	{

		$old_metal_query = $this->db->query("SELECT s.old_metal_sale_id,s.bill_id,s.purpose,s.metal_type,s.item_type,s.gross_wt,s.stone_wt,s.dust_wt,s.stone_wt,s.wastage_percent,s.wast_wt,

		s.net_wt,s.rate_per_grm as rate_per_gram,s.rate as amount,s.bill_id,s.bill_discount,est_id,b.bill_no,b.pur_ref_no

		FROM ret_billing b

		LEFT join  ret_bill_old_metal_sale_details s on s.bill_id=b.bill_id

		where b.bill_status=1 and s.bill_id=" . $bill_id . "");

		//print_r($this->db->last_query());exit;

		$old_matel_details = $old_metal_query->result_array();

		return $old_matel_details;
	}



	function getBrnachOtpRegMobile($id_branch)

	{

		$sql = $this->db->query("Select otp_verif_mobileno from branch where id_branch=" . $id_branch . "");

		return $sql->row()->otp_verif_mobileno;
	}

	// function get_previous_order_details($order_no, $bill_id, $branch_id, $bill_date)

	// {

	// 	$result_array = array();

	// 	$sql = $this->db->query("SELECT

	// 							rba.advance_type,

	// 							rba.received_amount,

	// 							rba.advance_amount,

	// 							bill.bill_date,rba.advance_date,

	// 							bill.goldrate_22ct,

	// 							bill.goldrate_18ct,

	// 							bill.silverrate_1gm,

	// 							c.id_metal,

	// 							ord.id_purity,

	// 							r.rate_field,

	// 							co.balance_type,

	// 							co.rate_type

	// 						FROM `ret_billing_advance` rba

	// 						LEFT JOIN ret_billing bill ON bill.bill_id = rba.bill_id

	// 						LEFT JOIN customerorder co ON co.id_customerorder = rba.id_customerorder

	// 						LEFT JOIN customerorderdetails ord ON ord.id_orderdetails = (SELECT MIN(one_ord.id_orderdetails) FROM customerorderdetails one_ord WHERE one_ord.id_customerorder = co.id_customerorder LIMIT 1)

	// 						LEFT JOIN ret_product_master as pro ON pro.pro_id = ord.id_product

	// 						LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id

	// 						LEFT JOIN ret_metal_purity_rate r on r.id_metal = c.id_metal and r.id_purity=ord.id_purity

	// 						WHERE rba.order_no = '" . $order_no . "' AND

	// 							  bill.id_branch = " . $branch_id . " AND

	// 							  date(bill.bill_date) <= '" . $bill_date . "' AND

	// 							  (bill.bill_status=1 OR bill.bill_id = " . $bill_id . ") ORDER BY rba.advance_date");

	// 	$sql_result = $sql->result_array();

	// 	//print_r($this->db->last_query());exit;

	// 	return $sql_result;
	// }


	function get_previous_order_details($id_customerorder, $bill_id, $branch_id, $bill_date)
	{
		$result_array = array();
		$result_array1 = array();

		$sql=$this->db->query("SELECT
								rba.advance_type,
								rba.received_amount,
								sum(rba.advance_amount) as advance_amount,
								rba.advance_date,
								bill.bill_date,
								bill.goldrate_22ct,
								bill.goldrate_18ct,
								bill.goldrate_14ct,
								bill.goldrate_9ct,
								bill.silverrate_1gm,
								c.id_metal,
								ord.id_purity,
								r.rate_field,
								co.balance_type,
								bill.order_adv_ref_no,
								bill.bill_id
							FROM `ret_billing_advance` rba
							LEFT JOIN ret_billing bill ON bill.bill_id = rba.bill_id
							LEFT JOIN customerorder co ON co.id_customerorder = rba.id_customerorder
							LEFT JOIN customerorderdetails ord ON ord.id_orderdetails = (SELECT MIN(one_ord.id_orderdetails) FROM customerorderdetails one_ord WHERE one_ord.id_customerorder = co.id_customerorder LIMIT 1)
							LEFT JOIN ret_product_master as pro ON pro.pro_id = ord.id_product
							LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id
							LEFT JOIN ret_metal_purity_rate r on r.id_metal = c.id_metal and r.id_purity=ord.id_purity
							WHERE rba.id_customerorder = '".$id_customerorder."' AND rba.advance_type=1 and
								  bill.id_branch = ".$branch_id." AND rba.advance_amount>0 And
								  (bill.bill_status=1 and bill.bill_id != ".$bill_id.") group by bill.bill_id,rba.advance_type, bill.pur_ref_no ORDER BY rba.advance_date");
			$sql_result = $sql->result_array();
			$sql1=$this->db->query("SELECT
								  rba.advance_type,
								  rba.received_amount,
								  sum(rba.advance_amount) as advance_amount,
								  rba.advance_date,
								  bill.bill_date,
								  bill.goldrate_22ct,
								  bill.goldrate_18ct,
								  bill.goldrate_14ct,
								  bill.silverrate_1gm,
								  c.id_metal,
								  ord.id_purity,
								  r.rate_field,
								  co.balance_type,
								  bill.pur_ref_no,
								  bill.bill_id
							  FROM `ret_billing_advance` rba
							  LEFT JOIN ret_billing bill ON bill.bill_id = rba.bill_id
							  LEFT JOIN customerorder co ON co.id_customerorder = rba.id_customerorder
							  LEFT JOIN customerorderdetails ord ON ord.id_orderdetails = (SELECT MIN(one_ord.id_orderdetails) FROM customerorderdetails one_ord WHERE one_ord.id_customerorder = co.id_customerorder LIMIT 1)
							  LEFT JOIN ret_product_master as pro ON pro.pro_id = ord.id_product
							  LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id
							  LEFT JOIN ret_metal_purity_rate r on r.id_metal = c.id_metal and r.id_purity=ord.id_purity
							  WHERE rba.id_customerorder = '".$id_customerorder."' AND
									bill.id_branch = ".$branch_id." AND rba.advance_amount>0 And
									rba.advance_type=2 and
									(bill.bill_status=1 OR bill.bill_id = ".$bill_id.") group by bill.bill_id,rba.advance_type, bill.pur_ref_no ORDER BY rba.advance_date");

        $sql_result1 = $sql1->result_array();
		$return_data =array_merge($sql_result,$sql_result1);
		// print_r($return_data);exit;

		return $return_data;

	}

	/* Credit History Details*/



	function getCreditPending($data)

	{

		$return_data = array();

		$profile_settings = $this->get_profile_settings($this->session->userdata('profile'));



		if ($data['col_type'] == 1) {

			$sql = $this->db->query("SELECT b.bill_id,concat(mt.metal_code,'-',b.sales_ref_no) as bill_no,DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date,DATE_FORMAT(b.credit_due_date,'%d-%m-%Y') as credit_due_date,b.tot_bill_amount,b.tot_amt_received,b.bill_cus_id,c.mobile,c.firstname as cus_name,

			if(b.credit_status=1,'Paid','Pending') as credit_status,br.name as branch_name,b.tot_bill_amount,(b.tot_bill_amount-b.tot_amt_received-IFNULL(ret.credit_due_amt,0)) as bal_amt,b.credit_disc_amt,IFNULL(ret.credit_due_amt,0) as credit_due_amt,IFNULL(ret.total_returned,0) as total_returned,b.sales_ref_no as s_ref_no,bill_det.pro_name

				from ret_billing b

				LEFT JOIN (SELECT bd.bill_id,Group_concat(bd.product_id) as pro_id,

				Group_concat(pm.product_name) as pro_name

				FROM ret_bill_details bd

				LEFT JOIN ret_product_master pm on pm.pro_id = bd.product_id

				Group by bd.bill_id) as bill_det on bill_det.bill_id = b.bill_id

				LEFT JOIN customer c on c.id_customer=b.bill_cus_id

				LEFT JOIN branch br on br.id_branch=b.id_branch

				LEFT JOIN metal mt on mt.id_metal=b.metal_type

				LEFT JOIN(SELECT IFNULL((b.credit_due_amt),0) as credit_due_amt,

				r.ret_bill_id,IFNULL(b.credit_ret_amt,0) as credit_ret_amt,IFNULL(SUM(d.item_cost),0) as total_returned

				FROM ret_bill_return_details r

				LEFT JOIN ret_bill_details d ON d.bill_det_id = r.ret_bill_det_id

				LEFT JOIN ret_billing b ON b.bill_id = r.bill_id

				WHERE b.bill_status = 1

				GROUP BY r.ret_bill_id) as ret ON ret.ret_bill_id = b.bill_id

				where  b.bill_id is not null and b.is_credit=1 and b.is_to_be = 0 and  b.bill_status=1  and b.bill_type!=8 and b.credit_status=2 and b.bill_type !=12

				AND b.fin_year_code != 2021

				" . ($data['id_branch'] != '' && $data['id_branch'] > 0 ? ' and b.id_branch=' . $data['id_branch'] : '') . "

				" . ($data['id_customer'] != '' && $data['id_customer'] > 0 ? ' and b.bill_cus_id=' . $data['id_customer'] : '') . "



				" . ($profile_settings['allow_bill_type'] == 3 ? " and (b.is_eda=1 OR b.is_eda=2)" : ($profile_settings['allow_bill_type'] == 1 ? " and b.is_eda=1" : " and b.is_eda=2")) . "



				ORDER BY b.bill_cus_id");

			//print_r($this->db->last_query());exit;

			$result = $sql->result_array();
		} else {

			$sql = $this->db->query("SELECT b.bill_id,concat(mt.metal_code,'-',b.sales_ref_no) as bill_no,DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date,DATE_FORMAT(b.credit_due_date,'%d-%m-%Y') as credit_due_date,b.tot_bill_amount,b.tot_amt_received,b.bill_cus_id,c.mobile,c.firstname as cus_name,

			if(b.credit_status=1,'Paid','Pending') as credit_status,br.name as branch_name,b.tot_bill_amount,(b.tot_bill_amount-b.tot_amt_received-IFNULL(ret.credit_due_amt,0)) as bal_amt,b.credit_disc_amt,IFNULL(ret.credit_due_amt,0) as credit_due_amt,IFNULL(ret.total_returned,0) as total_returned,b.sales_ref_no as s_ref_no

				from ret_billing b

				LEFT JOIN (SELECT bd.bill_id,Group_concat(bd.product_id) as pro_id,

				Group_concat(pm.product_name) as pro_name

				FROM ret_bill_details bd

				LEFT JOIN ret_product_master pm on pm.pro_id = bd.product_id

				Group by bd.bill_id) as bill_det on bill_det.bill_id = b.bill_id

				LEFT JOIN customer c on c.id_customer=b.bill_cus_id

				LEFT JOIN branch br on br.id_branch=b.id_branch

				LEFT JOIN metal mt on mt.id_metal=b.metal_type

				LEFT JOIN(SELECT IFNULL((b.credit_due_amt),0) as credit_due_amt,

				r.ret_bill_id,IFNULL(b.credit_ret_amt,0) as credit_ret_amt,IFNULL(SUM(d.item_cost),0) as total_returned

				FROM ret_bill_return_details r

				LEFT JOIN ret_bill_details d ON d.bill_det_id = r.ret_bill_det_id

				LEFT JOIN ret_billing b ON b.bill_id = r.bill_id

				WHERE b.bill_status = 1

				GROUP BY r.ret_bill_id) as ret ON ret.ret_bill_id = b.bill_id

				where  b.bill_id is not null and b.is_credit=1 and b.is_to_be=1 and  b.bill_status=1  and b.bill_type!=8 and b.credit_status=2 and b.bill_type !=12

				AND b.fin_year_code != 2021

				" . ($data['id_branch'] != '' && $data['id_branch'] > 0 ? ' and b.id_branch=' . $data['id_branch'] : '') . "

				" . ($data['id_customer'] != '' && $data['id_customer'] > 0 ? ' and b.bill_cus_id=' . $data['id_customer'] : '') . "



				" . ($profile_settings['allow_bill_type'] == 3 ? " and (b.is_eda=1 OR b.is_eda=2)" : ($profile_settings['allow_bill_type'] == 1 ? " and b.is_eda=1" : " and b.is_eda=2")) . "



				ORDER BY b.bill_cus_id");

			$result = $sql->result_array();
		}

		//print_r($this->db->last_query());exit;

		foreach ($result as $r) {

			$paid_amount = $this->get_credit_collection_details($r['bill_id']);



			$return_data[] = array(



				'type'              => 0,



				'bill_no'           => $this->get_bill_no_format_detail($r['bill_id']),



				'bill_date'         => $r['bill_date'],



				'cus_name'          => $r['cus_name'],

				'pro_name'          => $r['pro_name'],



				'mobile'            => $r['mobile'],



				'branch_name'       => $r['branch_name'],



				'tot_bill_amount'   => $r['tot_bill_amount'],



				'credit_due_amt'    => $r['credit_due_amt'],



				'credit_ret_amt'    => $r['credit_ret_amt'],



				'bal_amt'           => $r['bal_amt'] - $paid_amount,



				'due_amount'        => $r['tot_bill_amount'] - $r['tot_amt_received'],



				'paid_amount'       => $paid_amount,



				'bill_id'           => $r['bill_id'],

				's_ref_no'         => $r['s_ref_no']



			);
		}

		return $return_data;
	}

	/* Credit History Details*/

	/*Sales Bill no For Sales Return*/



	function getCustomerSalesDetails($data)

	{

		$profile_settings = $this->get_profile_settings($this->session->userdata('profile'));

		$sql = $this->db->query("SELECT b.bill_id,b.sales_ref_no as bill_no,

		DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date,ifnull(bd.item_cost,0) as invoice_value



		FROM ret_billing b

		LEFT JOIN ret_bill_details as bd on bd.bill_id=b.bill_id



		WHERE

		b.bill_id is not null and b.bill_status=1 and (b.bill_type <=3 or b.bill_type=9) 

		" . ($data['id_branch'] != '' && $data['id_branch'] > 0 ? ' and b.id_branch=' . $data['id_branch'] : '') . "

		" . ($data['id_customer'] != '' && $data['id_customer'] > 0 ? ' and b.bill_cus_id=' . $data['id_customer'] : '') . " 
		
		" . ($data['fin_year_code'] != '' && $data['fin_year_code'] > 0 ? ' and b.fin_year_code=' . $data['fin_year_code'] : '') . " 

		" . ($profile_settings['allow_bill_type'] == 3 ? " and (b.is_eda=1 OR b.is_eda=2)" : ($profile_settings['allow_bill_type'] == 1 ? " and b.is_eda=1" : " and b.is_eda=2")) . "



		GROUP BY b.bill_id");

		return $sql->result_array();
	}



	/*Sales Bill no For Sales Return*/

	function get_old_metal_est_details($old_metal_sale_id)

	{

		$sql = $this->db->query("SELECT s.est_id

        FROM ret_estimation_old_metal_sale_details s

        WHERE s.old_metal_sale_id =" . $old_metal_sale_id . "");

		return $sql->row_array();
	}



	function get_sale_est_details($est_itm_id)

	{

		$sql = $this->db->query("SELECT s.esti_id

        FROM ret_estimation_items s

        WHERE s.est_item_id =" . $est_itm_id . "");

		return $sql->row_array();
	}





	function get_est_split_details($data)

	{

		$dCData = $this->getBranchDayClosingData($data['id_branch']);

		$items_query = $this->db->query("SELECT est_itms.est_item_id, esti_id, item_type, est_itms.purchase_status,est.esti_no,

		ifnull(est_itms.product_id, '') as product_id, ifnull(est_itms.tag_id, '') as tag_id,

		ifnull(est_itms.design_id, '') as design_id, ifnull(pro.hsn_code,'') as hsn_code,

		est_itms.purity as purid, IFNULL(est_itms.size,'') as size, ifnull(est_itms.uom,'') as uom,IFNULL(est_itms.piece,'') as piece,

		ifnull(est_itms.less_wt,'') as less_wt,IFNULL(est_itms.net_wt,0) as net_wt,IFNULL(est_itms.gross_wt,0) as gross_wt,

		est_itms.calculation_based_on, IFNULL(est_itms.wastage_percent,0) as wastage_percent , IFNULL(est_itms.mc_value,'') as mc_value, est_itms.mc_type,

		ifnull(product_short_code, '-') as product_short_code,



		if(est_itms.item_type = 0, tag.sell_rate, (est_itms.item_cost - est_itms.item_total_tax)) as item_cost,

		ifnull(product_name, '-') as product_name, est_itms.is_partial,est_itms.discount,

		ifnull(design_code, '-') as design_code,

		ifnull(design_name, '') as design_name, pur.purity as purname,

		pro.tgrp_id as tax_group_id , tgrp_name, ifnull(c.id_metal,'') as metal_type,

		ifnull(des.fixed_rate,0) as fixed_rate,

		if(est_itms.id_orderdetails!='',ord.stn_amt,if(est_itms.tag_id != '',tag_stn_detail.stn_amount,stn_detail.stn_price)) as stone_price,

		IFNULL(tag_stn_detail.certification_cost,0) as certification_cost,

		if(est_itms.tag_id != null,stn_wgt,stn_wt) as stn_wgt,

		if(est_itms.tag_id != null,othermat_amount,other_mat_price) as othermat_amount,

		if(est_itms.tag_id != null,othermat_wt,other_mat_wgt) as othermat_wt,est_itms.is_non_tag,concat(cus.firstname,' ',cus.mobile) as cus_name,cus.id_customer,

		v.village_name,if(cus.is_vip=1,'Yes','No') as vip,tag_other_metal.tag_other_itm_amount,

		(select count(sa.id_scheme_account) from scheme_account sa left join customer cust on cust.id_customer=sa.id_customer) as accounts,

		pro.min_wastage,pro.max_wastage,pro.stock_type,nt.no_of_piece as available_pieces,nt.gross_wt as available_gross_wt,IFNULL(est_itms.orderno,'') as order_no,



		ifnull(est_itms.id_orderdetails,'') as id_orderdetails,IFNULL(ord.id_customerorder,'') as id_customerorder,pro.gift_applicable,r.rate_field,

		ifnull(rec.charge_value, 0) as charge_value,IFNULL(cus.pan,'') as pan_no,IFNULL(est_itms.id_collecion_maping_det,'') as id_collecion_maping_det,est.esti_for, est_itms.id_sub_design,



		ifnull(tag.tag_code,'') as tag_code,est_itms.est_rate_per_grm,IFNULL(est.discount,0) as est_discount,c.scheme_closure_benefit,IFNULL(tag.net_wt,0) as tag_net_wt,IFNULL(est_itms.esti_purchase_cost,0) AS esti_purchase_cost, IFNULL(tag.tag_lot_id, 0) AS tag_lot_id



		FROM ret_estimation as est

		LEFT JOIN ret_estimation_items as est_itms ON est_itms.esti_id = est.estimation_id



		left join ret_taging tag on tag.tag_id=est_itms.tag_id



		LEFT JOIN customerorderdetails as ord ON ord.id_orderdetails=est_itms.id_orderdetails

		LEFT JOIN (SELECT est_item_id,sum(price) as stn_price,sum(wt) as stn_wgt FROM `ret_estimation_item_stones` GROUP by est_item_id) as stn_detail ON stn_detail.est_item_id = est_itms.est_item_id

		LEFT JOIN (SELECT est_item_id,sum(price) as other_mat_price,sum(price) as other_mat_wgt FROM `ret_estimation_item_other_materials` GROUP by est_item_id) as est_oth_mat ON est_oth_mat.est_item_id = est_itms.est_item_id

		LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(certification_cost) as certification_cost,sum(wt) as stn_wt FROM `ret_taging_stone` GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = est_itms.tag_id

		LEFT JOIN (SELECT tag_id,sum(price) as othermat_amount,sum(wt) as othermat_wt FROM `ret_taging_other_materials` GROUP by tag_id) as tag_other_mat ON tag_other_mat.tag_id = est_itms.tag_id

		LEFT JOIN (SELECT tag_other_itm_tag_id,sum(tag_other_itm_amount) as tag_other_itm_amount FROM `ret_tag_other_metals` GROUP by tag_other_itm_tag_id) as tag_other_metal ON tag_other_metal.tag_other_itm_tag_id =est_itms.tag_id

		LEFT JOIN ret_product_master as pro ON pro.pro_id = est_itms.product_id

		LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id

		LEFT JOIN metal mt on mt.id_metal=c.id_metal

		LEFT JOIN ret_design_master as des ON des.design_no = est_itms.design_id

		LEFT JOIN ret_purity as pur ON pur.id_purity = est_itms.purity

		LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id

		LEFT JOIN customer cus on cus.id_customer=est.cus_id

		LEFT JOIN village v on v.id_village=cus.id_village



		LEFT JOIN ret_metal_purity_rate r on r.id_metal=c.id_metal and r.id_purity=est_itms.purity

		LEFT JOIN (SELECT est_item_id, SUM(IFNULL(amount,0)) AS charge_value FROM ret_estimation_other_charges GROUP BY est_item_id) AS rec ON rec.est_item_id = est_itms.est_item_id

		LEFT JOIN ret_nontag_item nt on nt.product=est_itms.product_id

		WHERE " . (!empty($data['id_branch']) ? "est.id_branch=" . $data['id_branch'] . "" : '') . "

		" . ($data['est_no'] != '' ? " and est.esti_no ='" . $data['est_no'] . "' " : '') . "

		AND est_itms.est_item_id IS NOT NULL and tag.id_orderdetails IS NULL and est_itms.purchase_status=0 AND (tag.tag_status =0 OR tag.tag_status IS NULL OR tag.is_partial=1)

		AND date(est.estimation_datetime)='" . $dCData['entry_date'] . "'

		AND IF(ifnull(tag.tag_type,0)=0,IF(IFNULL(est_itms.tag_id,'')!='' ,tag.tag_type=0,est_itms.est_item_id IS NOT null),(tag.tag_type=1 AND tag.is_approval_stock_converted = 1))

		GROUP by  est_itms.est_item_id

		order by est_itms.esti_id DESC");

		$item_details = $items_query->result_array();

		//print_r($this->db->last_query());exit;

		$return_data = [];

		foreach ($item_details as $item) {

			$grossWt = ($item['gross_wt']!=null ? $item['gross_wt']:'');

			$lot_no = ($item['tag_lot_id']!=null ? $item['tag_lot_id']:'');

			$return_data['item_details'][] = array(

				'calculation_based_on'  => $item['calculation_based_on'],

				'design_code'		  	=> $item['design_code'],

				'tag_other_itm_amount'  => $item['tag_other_itm_amount'],


				'design_id'				=> ($item['design_id'] != null ? $item['design_id'] : ''),

				'design_name'			=> ($item['design_name'] != null ? $item['design_name'] : ''),

				'id_sub_design'			=> ($item['id_sub_design'] != null ? $item['id_sub_design'] : ''),

				'discount'				=> $item['discount'],

				'est_item_id'			=> ($item['est_item_id'] != null ? $item['est_item_id'] : ''),

				'esti_id'				=> ($item['esti_id'] != null ? $item['esti_id'] : ''),

				'esti_no'				=> ($item['esti_no'] != null ? $item['esti_no'] : ''),

				'fixed_rate'			=> ($item['fixed_rate'] != null ? $item['fixed_rate'] : ''),

				'gross_wt'				=> $grossWt,

				'hsn_code'				=> ($item['hsn_code'] != null ? $item['hsn_code'] : ''),

				'is_partial'			=> ($item['is_partial'] != null ? $item['is_partial'] : ''),

				'is_non_tag'			=> ($item['is_non_tag'] != null ? $item['is_non_tag'] : ''),

				'item_cost'				=> ($item['item_cost'] != null ? $item['item_cost'] : ''),

				'item_type'				=> ($item['item_type'] != null ? $item['item_type'] : ''),

				'less_wt'				=> $item['less_wt'],

				'mc_type'				=> ($item['mc_type'] != null ? $item['mc_type'] : ''),

				'mc_value'				=> ($item['mc_value'] != '' ? $item['mc_value'] : ''),

				'metal_type'			=> $item['metal_type'],

				'scheme_closure_benefit' => $item['scheme_closure_benefit'],

				'net_wt'				=> $item['net_wt'],

				'tag_net_wt'			=> $item['tag_net_wt'],

				'othermat_amount'		=> ($item['othermat_amount'] != null ? $item['othermat_amount'] : 0),

				'othermat_wt'			=> ($item['othermat_wt'] != null ? $item['othermat_wt'] : 0),

				'stock_type'			=> ($item['stock_type'] != null ? $item['stock_type'] : ''),

				'piece'					=> $item['piece'],

				'product_id'			=> $item['product_id'],

				'product_name'			=> $item['product_name'],

				'product_short_code'	=> $item['product_short_code'],

				'purchase_status'		=> $item['purchase_status'],

				'purid'					=> ($item['purid'] != null ? $item['purid'] : ''),

				'purname'				=> $item['purname'],

				'size'					=> ($item['size'] != null ? $item['size'] : ''),

				'stn_wgt'				=> ($item['stn_wgt'] != null ? $item['stn_wgt'] : ''),

				'stone_price'			=> ($item['stone_price'] != null ? $item['stone_price'] : 0),

				'certification_cost'	=> $item['certification_cost'],

				'tag_id'				=> ($item['tag_id'] != null ? $item['tag_id'] : ''),

				'tag_code'              => ($item['tag_code'] != null ? $item['tag_code'] : ''),

				'tax_group_id'			=> $item['tax_group_id'],

				'tgrp_name'				=> $item['tgrp_name'],

				'uom'					=> ($item['uom'] != null ? $item['uom'] : ''),

				'wastage_percent'		=> ($item['wastage_percent'] != null ? $item['wastage_percent'] : ''),

				'max_wastage'		    => ($item['max_wastage'] != null ? $item['max_wastage'] : 0),

				'min_wastage'		    => $item['min_wastage'],

				'cus_name'		        => $item['cus_name'],

				'id_customer'		    => $item['id_customer'],

				'pan_no'		        => $item['pan_no'],

				'chit_cus'		        => ($item['accounts'] == 0 ? 'No' : 'Yes'),

				'vip_cus'		        => $item['vip'],

				'village_name'		    => ($item['village_name'] != null ? $item['village_name'] : ''),

				'available_pieces'		=> ($item['available_pieces'] != null ? $item['available_pieces'] : ''),

				'available_gross_wt'	=> ($item['available_gross_wt'] != null ? $item['available_gross_wt'] : ''),

				'id_orderdetails'	    => $item['id_orderdetails'],

				'id_customerorder'	    => $item['id_customerorder'],

				'gift_applicable'		=> $item['gift_applicable'],

				'rate_calc_from'		=> $item['rate_calc_from'],

				'rate_field'            => $item['rate_field'],

				'order_no'				=> ($item['order_no'] != null ? $item['order_no'] : ''),

				'stone_details'			=> ($estId != '' && $item['est_item_id'] != '' ? $this->get_stone_details($item['est_item_id']) : $this->get_tag_stone_details($item['tag_id'])),

				'other_metal_details'	=> ($item['tag_id'] != '' ? $this->get_other_metal_details($item['tag_id']) : []),

				'charge_value'			=> $item['charge_value'],

				'est_rate_per_grm'		=> $item['est_rate_per_grm'],

				'est_discount'		    => $item['est_discount'],

				'esti_for'              => ($item['esti_for'] != null ? $item['esti_for'] : ''),

				'id_collecion_maping_det' => '',

				'charges'				=> ($estId != '' && $item['est_item_id'] != '' ? $this->get_other_estcharges($item['est_item_id']) : ($item['tag_id'] != '' ? $this->get_charges($item['tag_id']) : '')),

				'esti_purchase_cost'		=> isset($item['esti_purchase_cost']) ? $item['esti_purchase_cost'] : 0,

				'mc_va_limit'			=> ($item['product_id'] != ''  && $item['design_id'] != '' && $item['id_sub_design'] != '' ?  $this->get_mc_va_limit($item['product_id'], $item['design_id'], $item['id_sub_design'], $grossWt, $lot_no, $data['id_branch'], $item['net_wt']) : '')

			);
		}

		return $return_data;
	}

	function get_DeliveryList($data)

	{

		if ($_POST['dt_range'] != '') {

			$dateRange = explode('-', $_POST['dt_range']);

			$from = str_replace('/', '-', $dateRange[0]);

			$to = str_replace('/', '-', $dateRange[1]);

			$d1 = date_create($from);

			$d2 = date_create($to);

			$FromDt = date_format($d1, "Y-m-d");

			$ToDt = date_format($d2, "Y-m-d");
		}

		$sql = $this->db->query("SELECT b.bill_id,b.sales_ref_no as bill_no,cus.firstname as cus_name,cus.mobile,t.tag_code,d.item_cost,

        d.is_delivered,p.product_name,des.design_name,br.name as branch_name,

        date_format(b.bill_date,'%d-%m-%Y') as bill_date,d.bill_det_id,date_format(d.delivered_date,'%d-%m-%Y') as delivered_date,emp.firstname as delivered_by,b.id_branch

        FROM ret_bill_details d

        LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

        LEFT JOIN ret_taging t ON t.tag_id=d.tag_id

        LEFT JOIN ret_product_master p ON p.pro_id=d.product_id

        LEFT JOIN customer cus ON cus.id_customer=b.bill_cus_id

        LEFT JOIN ret_design_master des ON des.design_no=d.design_id

        LEFT JOIN branch br ON br.id_branch=b.id_branch

        LEFT JOIN employee emp on emp.id_employee=d.delivered_by

        WHERE b.bill_status=1 AND (d.is_delivered=0 or d.is_delivered=2)

        " . ($data['dt_range'] != '' ? 'and date(b.bill_date) BETWEEN "' . $FromDt . '" AND "' . $ToDt . '"' : '') . "

        " . ($data['bill_no'] != '' ? " and b.sales_ref_no=" . $data['bill_no'] . "" : '') . "

        " . ($data['id_branch'] != 0 && $data['id_branch'] != '' ? " and b.id_branch=" . $data['id_branch'] . "" : '') . "

        ORDER BY b.bill_id desc");

		//echo $this->db->last_query();exit;

		return $sql->result_array();
	}



	function is_estno_already_billed($est_id, $id_branch)

	{

		$dCData = $this->getBranchDayClosingData($id_branch);

		$sql = $this->db->query("SELECT *

        FROM ret_estimation e

        LEFT JOIN ret_billing b ON b.bill_id = e.estbillid

        where  e.estbillid is not null AND b.bill_status = 1

        " . ($est_id != '' && $est_id > 0 ? ' and e.esti_no=' . $est_id : '') . "

        " . ($id_branch != '' && $id_branch > 0 ? ' and e.id_branch=' . $id_branch : '') . "

        AND date(e.estimation_datetime)='" . $dCData['entry_date'] . "' ");

		if ($sql->num_rows() > 0) {

			$status = 1;
		} else if ($sql->num_rows() == 0) {

			$status = 0;
		}

		return $status;
	}



	function checkSectionItemExist($data)

	{

		$r = array("status" => FALSE);

		$sql = "SELECT id_hometag_item FROM ret_home_section_item WHERE id_branch=" . $data['id_branch'] . " AND id_section=" . $data['id_section'] . " AND id_product=" . $data['id_product'];

		$res = $this->db->query($sql);

		if ($res->num_rows() > 0) {

			$r = array("status" => true, "id_hometag_item" => $res->row()->id_hometag_item);
		} else {

			$r = array("status" => false, "id_hometag_item" => "");
		}

		return $r;
	}

	function updatesecNTData($data, $arith)

	{

		$sql = "UPDATE ret_home_section_item SET no_of_piece=(no_of_piece" . $arith . " " . $data['no_of_piece'] . "),gross_wt=(gross_wt" . $arith . " " . $data['gross_wt'] . "),net_wt=(net_wt" . $arith . " " . $data['net_wt'] . "),updated_by=" . $data['updated_by'] . ",updated_on='" . $data['updated_on'] . "' WHERE id_hometag_item=" . $data['id_hometag_item'];

		$status = $this->db->query($sql);

		return $status;
	}



	function get_billing_adj_details($id)

	{

		$sql = $this->db->query("SELECT r.id_issue_receipt,au.utilized_amt,

		concat(br.short_name,'',b.fin_year_code,'-',b.sales_ref_no) as adjusted_bill_no

		FROM ret_issue_receipt r

		LEFT JOIN ret_advance_utilized au ON au.id_issue_receipt = r.id_issue_receipt

		LEFT JOIN ret_billing b ON b.bill_id = au.bill_id

		LEFT JOIN branch br ON br.id_branch = b.id_branch

		WHERE r.id_issue_receipt=" . $id . "");

		return $sql->result_array();
	}





	function get_customer_details($id_customer)

	{

		/*$sql=("SELECT * From customer where id_customer=".$id_customer);

        //print_r($this->db->last_query()); exit;

        return $data = $this->db->query($sql)->row_array();		*/



		$sql = "SELECT cus.*, st.name as statename, st.state_code, ad.address1, ad.address2, ad.pincode

                    From customer as cus

                    LEFT JOIN address as ad ON ad.id_customer = cus.id_customer

                    LEFT JOIN state as st ON st.id_state = ad.id_state

                    where cus.id_customer ='" . $id_customer . "' GROUP BY cus.id_customer";

		//print_r($this->db->last_query()); exit;

		return $data = $this->db->query($sql)->row_array();
	}

	function getBillingDetTaxPer($tgrp_id)

	{
		// Guard: OG/NT items may have no product → no tax group
		if (empty($tgrp_id)) {
			return array();
		}

		$sql = $this->db->query("SELECT tgi_tgrpcode, tgrp_name, tgi_calculation, tgi_type, tax_percentage

        FROM ret_taxgroupitems as tx_grp_itm

        LEFT JOIN ret_taxgroupmaster as grp ON grp.tgrp_id = tx_grp_itm.tgi_tgrpcode

        LEFT JOIN ret_taxmaster as tx ON tx.tax_id = tx_grp_itm.tgi_taxcode

        WHERE grp.tgrp_id = " . $this->db->escape($tgrp_id) . "");

		return $sql->row_array();
	}



	function getbillingdetailsitems($billId)
	{

		$returnData = array();

		$sql = $this->db->query("SELECT bd.bill_det_id, cat.name as catname, pro.product_name,IFNULL(pro.hsn_code,'-') as hsn_code,

	                              bd.piece, bd.gross_wt, bd.item_cost,ifnull(bd.bill_discount, 0) as bill_discount, bd.item_total_tax,

	                              ifnull(bd.total_sgst, 0) as total_sgst, ifnull(bd.total_cgst, 0) as total_cgst,

	                              ifnull(bd.total_igst, 0) as total_igst,

	                              ((item_cost - ifnull(item_total_tax,0)) + ifnull(total_sgst,0) + ifnull(total_cgst, 0) + ifnull(total_igst,0)) as valcost,

	                              (round(CAST(((item_cost - item_total_tax) + total_sgst + total_cgst + total_igst) AS DECIMAL(10,2)))) as roundamt,

	                              (item_cost - ifnull(item_total_tax,0)) costwotax,

	                              CAST(((item_cost - item_total_tax) / if(ifnull(bd.gross_wt,0) = 0, bd.piece, bd.gross_wt)) AS DECIMAL(10,2)) as ratepergram,



	                              bd.tax_group_id

	                              FROM `ret_bill_details` as bd

	                              LEFT JOIN ret_product_master as pro ON pro.pro_id = bd.product_id

	                              LEFT JOIN ret_category as cat ON cat.id_ret_category = pro.cat_id

	                              WHERE bd.bill_id = '" . $billId . "' GROUP BY bd.bill_det_id");



		$result =  $sql->result_array();

		foreach ($result as $items) {

			$tax_details = $this->getBillingDetTaxPer($items['tax_group_id']);

			$items['GstRt'] = !empty($tax_details['tax_percentage']) ? $tax_details['tax_percentage'] : 0;

			$returnData[] = $items;
		}



		return $returnData;
	}





	function getbillingInfobybillId($billId)
	{

		$sql = $this->db->query("SELECT b.bill_id, b.bill_no, b.bill_cus_id, b.sales_ref_no,
								date_format(b.bill_date,'%d/%m/%Y') as billdate,
								b.bill_type, b.id_branch,
								ifnull(b.to_branch, b.id_branch) as to_branch,
								CONCAT(br.short_name, '-SA-', b.sales_ref_no) as transrefno,
								IF(EXISTS(
									SELECT 1 FROM ret_bill_old_metal_sale_details oms
									WHERE oms.bill_id = b.bill_id
								) AND b.bill_status = 1, '1', '0') as transfer_item_type,
								br.grnrequest, br.authtoken

								FROM ret_billing as b

								LEFT JOIN branch br ON br.id_branch = b.id_branch

								LEFT JOIN metal as met ON met.id_metal = b.metal_type

								where b.bill_id = '" . $billId . "'");

		return $sql->row_array();
	}



	function getbilltotalvaluesdetails($billId)
	{

		$sql = $this->db->query("SELECT sum(bd.item_total_tax) as item_total_tax,

                        	  sum(ifnull(bd.total_sgst, 0)) as total_sgst, sum(ifnull(bd.total_cgst, 0)) as total_cgst,

                        	  sum(ifnull(bd.total_igst, 0)) as total_igst,

                        	  sum(((item_cost - ifnull(item_total_tax,0)) + ifnull(total_sgst,0) + ifnull(total_cgst, 0) + ifnull(total_igst,0))) as valcost,

                              sum((item_cost - ifnull(item_total_tax,0))) costwotax,

                              (round(sum(CAST(((item_cost - item_total_tax) + total_sgst + total_cgst + total_igst) AS DECIMAL(10,2)))) - sum(CAST(((item_cost - item_total_tax) + total_sgst + total_cgst + total_igst) AS DECIMAL(10,2)))) as roundval,

                              (round(sum(CAST(((item_cost - item_total_tax) + total_sgst + total_cgst + total_igst) AS DECIMAL(10,2))))) as totalroundamt

                        	  FROM `ret_bill_details` as bd

                              WHERE bd.bill_id = '" . $billId . "' GROUP BY bd.bill_id");

		return $sql->row_array();
	}



	function getexistingAuthToken()
	{

		$sql = $this->db->query("SELECT ifnull(authtoken, '') as authtoken FROM company");

		return $sql->row()->authtoken;
	}



	function updatecompanyauthtoken($updateData)
	{

		$edit_flag = $this->db->update('company', $updateData);

		return $edit_flag;
	}



	function updatebilleinvoicedetails($updatedata, $billId)
	{



		$edit_flag = 0;

		$this->db->where("bill_id", $billId);

		$edit_flag = $this->db->update("ret_billing", $updatedata);
	}



	function geteinvoiceirndetails()
	{

		$return_data = array();

		$sql = $this->db->query("SELECT bill_id, cusdel_irn, qrcodeimage, cusdel_signature FROM ret_billing WHERE cusdel_irn IS NOT NULL");

		return $sql->result_array();
	}



	//Bill number format settings

	function get_bill_no_format_detail($bill_id, $type="")

	{

		// print_r($type.'lmx'.$bill_id);exit;

		$format1 = $this->db->query("SELECT b.bill_type,bf.bill_no_format,b.bill_id,b.pur_ref_no,b.order_adv_ref_no,b.s_ret_refno,b.credit_coll_refno,b.approval_ref_no,b.chit_preclose_refno,br.short_name as '@@branch_code@@',b.fin_year_code as '@@fin_year@@',

			IFNULL(m.metal_code,'') as '@@metal_code@@',

				CASE


					WHEN b.bill_type = 4 then ifNULL(b.pur_ref_no, b.bill_no)

					" . ($type == 'p' ? 'WHEN b.bill_type = 5 or b.bill_type = 9 then ifNULL(b.pur_ref_no,b.bill_no)' : '') . "


					WHEN b.bill_type = 5 then ifNULL(b.order_adv_ref_no, b.bill_no)

					WHEN b.bill_type = 7 then ifNULL(b.s_ret_refno, b.bill_no)

					WHEN b.bill_type = 8 then ifNULL(b.credit_coll_refno, b.bill_no)

					WHEN b.bill_type = 9 then ifNULL(b.sales_ref_no, b.bill_no)

					WHEN b.bill_type = 15 then ifNULL(b.approval_ref_no, b.bill_no)

					WHEN b.bill_type = 10 then ifNULL(b.chit_preclose_refno, b.bill_no)
					
					WHEN b.bill_type = 14 then b.bill_no

					" . ($type == 'sr' ? 'WHEN b.bill_type = 1 or b.bill_type = 2 or b.bill_type = 3  then ifNULL(b.s_ret_refno,b.bill_no)' : '') . "

					" . ($type == 'p' ? 'WHEN b.bill_type = 1 or b.bill_type = 2 or b.bill_type = 3  then ifNULL(b.pur_ref_no,b.bill_no)' : '') . "

					" . ($type == '' ? 'WHEN b.bill_type = 1 or  b.bill_type = 13 or b.bill_type = 2 or b.bill_type = 3 then ifNULL(b.sales_ref_no,b.bill_no)' : '') . "


				ELSE b.bill_no

				END as '@@bill_no@@',

				CASE

				    	" . ($type == 'p' ? 'WHEN b.bill_type = 1 or b.bill_type = 2 or b.bill_type = 3 or b.bill_type = 5 or b.bill_type = 9 then "PU"' : '') . "
				    	" . ($type == 'sr' ? 'WHEN b.bill_type = 1 or b.bill_type = 2 or b.bill_type = 3 then "SR"' : '') . "

					WHEN b.bill_type = 1 or b.bill_type = 13  then ifNULL('SA','')

					WHEN b.bill_type = 2 then ifNULL('SA','')

					WHEN b.bill_type = 9 then ifNULL('SA','')

					WHEN b.bill_type = 3 then ifNULL('SA','')

					WHEN b.bill_type = 4 then ifNULL('PU','')

					WHEN b.bill_type = 5 then ifNULL('OD','')

					WHEN b.bill_type = 7 then ifNULL('SR','')

					WHEN b.bill_type = 14 then ifNULL('SR','')

					WHEN b.bill_type = 8 then ifNULL('CC','')

					WHEN b.bill_type = 11 then ifNULL('RE','')

					WHEN b.bill_type = 15 then NULL

					WHEN b.bill_type = 10 then NULL








				ELSE NULL

				END as '@@short_code@@'

						FROM bill_no_format bf

						LEFT JOIN ret_billing b ON b.bill_type = b.bill_type

						LEFT JOIN metal m ON m.id_metal = b.metal_type

						LEFT JOIN branch br on br.id_branch=b.id_branch

						WHERE b.bill_id is not null

						" . ($bill_id != '' && $bill_id > 0 ? 'and b.bill_id=' . $bill_id . '' : '') . " ");



		$query1 =  $format1->row_array();

		$format2 = $this->db->query("SELECT bill_no_format,

				CASE

					WHEN bill_type = 1 or bill_type = 13  then ifNULL('SA','')

					WHEN bill_type = 2 then ifNULL('SA','')

					WHEN bill_type = 3 then ifNULL('SA','')

					WHEN bill_type = 4 then ifNULL('PU','')

					WHEN bill_type = 5 then ifNULL('OD','')

					WHEN bill_type = 7 then ifNULL('SR','')

					WHEN bill_type = 14 then ifNULL('SR','')

					WHEN bill_type = 8 then ifNULL('CC','')

					WHEN bill_type = 15 then NULL

					WHEN bill_type = 10 then NULL

				ELSE NULL

				END as '@@short_code@@'

					from bill_no_format

					" . ($query1['bill_type'] != ''  ? 'where bill_type=' . $query1['bill_type'] : '') . "");

        $row2 = $format2->row();
        if ($row2 && !empty($row2->bill_no_format)) {
            $query2 = $row2->bill_no_format;
        } else {
            $query2 = "-@@branch_code@@-@@fin_year@@-@@short_code@@-@@bill_no@@";
        }

		$query2 = substr($query2, 1, strlen($query2) - 1);



		$billno = strtr($query2, $query1);

		if (substr($billno, 0, 1) === '-') {

			$billno = ltrim($billno, '-');

			return $billno;
		} else {

			return $billno;
		}
	}



	function get_data()

	{

		$sql = $this->db->query("SELECT bill_type,id_bill_no_format,bill_no_format FROM bill_no_format");

		return $sql->result_array();
	}



	//Bill number format settings





	function get_deposit_type_bill_no($id, $type = "")
	{

		$sql = $this->db->query("SELECT r.deposit_type,b.bill_id,m.metal,b.make_as_advance,

        concat(br.short_name,'',b.fin_year_code,'-',b.s_ret_refno) as s_ret_refno,

        concat(br.short_name,'',b.fin_year_code,'-',b.pur_ref_no) as pur_ref_no

        FROM ret_issue_receipt r

        LEFT JOIN ret_billing b ON b.bill_id = r.deposit_bill_id

		LEFT JOIN ret_bill_old_metal_sale_details s On s.bill_id = r.deposit_bill_id



		LEFT JOIN metal m ON m.id_metal = s.metal_type

        LEFT JOIN branch br ON br.id_branch = b.id_branch

        WHERE r.id_issue_receipt=" . $id . "");

		$result = $sql->row_array();

		$result['bill_no'] = $this->get_bill_no_format_detail($result['bill_id'], $type);

		return $result;
	}
	function get_customer_wise_tcs_percent($id_customer, $fin_year="")
	{
		$sql = $this->db->query("SELECT cus.id_customer,
        cus.fin_year_code,
        (IFNULL(rp.item_cost,0)+IFNULL(ct.opening_balance_amount,0)) as balance
        FROM customer cus
        
		LEFT JOIN(SELECT IFNULL(SUM(d.item_cost),0) as item_cost,b.bill_cus_id
        FROM ret_billing b
        Left JOIN ret_bill_details d on d.bill_id=b.bill_id
		WHERE 
		b.is_eda = 1 
		" . ($fin_year !=  '' ? " and b.fin_year_code=" . $fin_year . "" : '') . "
		and b.bill_status = 1
        Group by b.bill_cus_id ) as rp on rp.bill_cus_id = cus.id_customer

        LEFT JOIN(SELECT IFNULL(cr.opening_balance_amount,0) as opening_balance_amount,
        cr.id_customer
        FROM customer cr
         " . ($fin_year !=  '' ? " WHERE cr.fin_year_code=" . $fin_year . "" : '') . "
        Group by cr.id_customer) as ct on ct.id_customer =cus.id_customer
        WHERE cus.id_customer =" . $id_customer . "
        Group by cus.id_customer");

		return $sql->row_array();
	}

	function get_receipt_no_against_purchase($bill_id)
	{

		$sql = $this->db->query("SELECT r.bill_no as adv_rcpt_no

			FROM ret_billing b

			LEFT JOIN ret_issue_receipt r ON r.deposit_bill_id = b.bill_id

			where b.bill_id=" . $bill_id . "");

		return $sql->row()->adv_rcpt_no;
	}

	function getTagImageDetails($tag_id)
	{
		$sql = $this->db->query("SELECT ti.id_tag_img,ti.tag_id,ti.image,ti.is_default
        FROM ret_taging_images ti
        WHERE ti.tag_id=" . $tag_id . "");
		//print_r($this->db->last_query());exit;
		return $sql->result_array();
	}

	function isEmptySetDefault($value, $default)
	{

		if ($value != '' &&  $value != NULL &&  $value != 'null') {
			return $value;
		} else {
			return $default;
		}
	}

	function getPreviousDateStatuslog($bill_id)
	{
		$sql = $this->db->query("SELECT rb.bill_id, rb.bill_date from ret_billing rb
        	" . ($bill_id != '' ?  " where rb.bill_id=" . $bill_id . "" : '') . "");
		return $sql->row_array();
	}

	// customer payment Details

	function getCustomerpaymentDetails($cus_id, $id_branch)
    {
		$dCData=$this->getBranchDayClosingData($id_branch);

        $sql = $this->db->query("SELECT cus.id_customer,(IFNULL(bp.cash_bill_pay,0)+IFNULL(pr.cash_bill_pay,0)+IFNULL(pd.cash_bill_pay,0)+IFNULL(bill_adv_adj_cash.bill_cash_adv_adj,0)+IFNULL(chit_adv_adj_cash.chit_cash_adv_adj,0)+IFNULL(bill_chit_utilized.chit_cash_utilized,0)+IFNULL(trans_to.transfer_amount,0)+IFNULL(bill_return.ret_cash_paid,0)-IFNULL(trans_from.transfer_amount,0)) as bill_amount,
		IFNULL(bp.cash_bill_pay,0) AS billing_payment,
		IFNULL(pr.cash_bill_pay,0) AS advance_payment,
		IFNULL(pd.cash_bill_pay,0) AS chit_payment,
		IFNULL(bill_adv_adj_cash.bill_cash_adv_adj,0) AS bill_adv_adjustment,
		IFNULL(chit_adv_adj_cash.chit_cash_adv_adj,0) AS chit_adv_adjustment,
		IFNULL(bill_chit_utilized.chit_cash_utilized,0) AS bill_chit_utilized,
		IFNULL(trans_to.transfer_amount,0) AS advance_transfered_to,
		IFNULL(bill_return.ret_cash_paid,0) AS bill_returns,
		IFNULL(trans_from.transfer_amount,0) AS advance_transfered_from
        FROM customer cus

		LEFT JOIN (SELECT cus.id_customer,ifnull(concat(cus.firstname,'-',cus.mobile),'') as cus_name,
		ifnull(sum(bp.payment_amount),0) as cash_bill_pay
		from ret_billing_payment bp
		left join ret_billing b on b.bill_id = bp.bill_id
		left join customer cus on cus.id_customer = b.bill_cus_id
		where b.bill_status =1 and b.is_eda=1
		and bp.payment_mode = 'Cash' and bp.type=1 and DATE(b.bill_date) = DATE('".$dCData['entry_date']."')
		group by b.bill_cus_id) as bp on bp.id_customer = cus.id_customer

		LEFT JOIN (SELECT cus.id_customer,ifnull(concat(cus.firstname,'-',cus.mobile),'') as cus_name,
		ifnull(sum(rp.payment_amount),0) as cash_bill_pay
		from ret_issue_rcpt_payment rp
		left join ret_issue_receipt rt on rt.id_issue_receipt = rp.id_issue_rcpt
		left join customer cus on cus.id_customer = rt.id_customer
		where rt.bill_status =1
		and rp.payment_mode = 'Cash' and rp.type=1 and DATE(rt.bill_date) = DATE('".$dCData['entry_date']."')
		group by rt.id_customer) as pr on pr.id_customer = cus.id_customer

		LEFT JOIN (SELECT cus.id_customer,ifnull(concat(cus.firstname,'-',cus.mobile),'') as cus_name,
		ifnull(sum(pm.payment_amount),0) as cash_bill_pay
		from payment_mode_details pm
		left join payment pt on pt.id_payment = pm.id_payment
		left join scheme_account st on st.id_scheme_account = pt.id_scheme_account
		left join customer cus on cus.id_customer = st.id_customer
		where pt.payment_status =1 and pm.payment_status=1
		and pm.payment_mode = 'CSH' and pt.type=1 and DATE(pt.custom_entry_date) = DATE('".$dCData['entry_date']."')
		group by st.id_customer) as pd on pd.id_customer = cus.id_customer

		LEFT JOIN (SELECT cus.id_customer,ifnull(concat(cus.firstname,'-',cus.mobile),'') as cus_name,
		ifnull(sum(at.transfer_cash_amt),0) as transfer_amount
		from ret_advance_transfer at
		left join ret_issue_receipt rt on rt.id_issue_receipt = at.id_issue_receipt
		left join customer cus on cus.id_customer = rt.id_customer
		where rt.bill_status =1
		and DATE(rt.bill_date) = DATE('".$dCData['entry_date']."')
		group by rt.id_customer) as trans_to on trans_to.id_customer = cus.id_customer


		LEFT JOIN (SELECT cus.id_customer,ifnull(concat(cus.firstname,'-',cus.mobile),'') as cus_name,
		ifnull(sum(at.transfer_cash_amt),0) as transfer_amount
		from ret_advance_transfer at
		left join ret_issue_receipt rt on rt.id_issue_receipt = at.transfer_receipt_id
		left join customer cus on cus.id_customer = rt.id_customer
		where rt.bill_status =1
		and DATE(rt.bill_date) = DATE('".$dCData['entry_date']."')
		group by rt.id_customer) as trans_from on trans_from.id_customer = cus.id_customer

		LEFT JOIN (
			SELECT IFNULL(SUM(cash_utilized_amt), 0) AS bill_cash_adv_adj, b.bill_cus_id 
			FROM `ret_advance_utilized` au 
			LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = au.id_issue_receipt
			LEFT JOIN ret_billing b ON b.bill_id = au.bill_id 
			WHERE b.bill_status =1 AND au.adjusted_for = 1 AND DATE(b.bill_date) = DATE('".$dCData['entry_date']."') AND DATE(r.bill_date) != DATE('".$dCData['entry_date']."') AND au.bill_id IS NOT NULL
			GROUP BY b.bill_cus_id
		) AS bill_adv_adj_cash ON bill_adv_adj_cash.bill_cus_id = cus.id_customer

		LEFT JOIN (
			SELECT IFNULL(SUM(cash_utilized_amt), 0) AS chit_cash_adv_adj, sa.id_customer 
			FROM `ret_advance_utilized` au 
			LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = au.id_issue_receipt
			LEFT JOIN payment p ON p.id_payment = au.id_payment 
			LEFT JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
			WHERE p.payment_status = 1 AND au.adjusted_for = 2 AND DATE(p.custom_entry_date) = DATE('".$dCData['entry_date']."') AND DATE(r.bill_date) != DATE('".$dCData['entry_date']."') AND au.id_payment IS NOT NULL
			GROUP BY sa.id_customer
		) AS chit_adv_adj_cash ON chit_adv_adj_cash.id_customer = cus.id_customer

		LEFT JOIN (
			SELECT IFNULL(SUM(pay.cash_pay), 0) AS chit_cash_utilized, b.bill_cus_id 
			FROM `ret_billing_chit_utilization` cu
			LEFT JOIN ret_billing b ON b.bill_id = cu.bill_id 
			LEFT JOIN (
				SELECT 
					IFNULL(SUM(cp.cash_pay),0) + IFNULL(SUM(chit_adv_adj_cash.chit_cash_adv_adj),0) as cash_pay, p.id_scheme_account
				FROM payment p
				LEFT JOIN (SELECT SUM(IFNULL(pmd.payment_amount,0)) AS cash_pay, pmd.id_payment FROM `payment_mode_details` AS pmd LEFT JOIN payment p ON p.id_payment = pmd.id_payment WHERE pmd.payment_mode = 'CSH' AND pmd.payment_status = 1 AND DATE(p.custom_entry_date) != DATE('".$dCData['entry_date']."') GROUP BY pmd.id_payment) AS cp ON cp.id_payment = p.id_payment
				LEFT JOIN (
						SELECT IFNULL(SUM(cash_utilized_amt), 0) AS chit_cash_adv_adj, p.id_payment 
						FROM `ret_advance_utilized` au 
						LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = au.id_issue_receipt
						LEFT JOIN payment p ON p.id_payment = au.id_payment 
						WHERE au.adjusted_for = 2 AND DATE(p.custom_entry_date) != DATE('".$dCData['entry_date']."') AND au.id_payment IS NOT NULL
						GROUP BY p.id_payment
				) AS chit_adv_adj_cash ON chit_adv_adj_cash.id_payment = p.id_payment
				where p.payment_status=1 GROUP BY p.id_scheme_account
			) as pay on pay.id_scheme_account = cu.scheme_account_id
			WHERE b.bill_status =1 AND DATE(b.bill_date) = DATE('".$dCData['entry_date']."')
			GROUP BY b.bill_cus_id
		) AS bill_chit_utilized ON bill_chit_utilized.bill_cus_id = cus.id_customer

		LEFT JOIN (
			SELECT IFNULL(SUM(ret.ret_cash_paid), 0) AS ret_cash_paid, b.bill_cus_id
			FROM `ret_bill_return_details` ret
			LEFT JOIN ret_billing b ON b.bill_id = ret.bill_id
			LEFT JOIN ret_billing br ON br.bill_id = ret.ret_bill_id
			WHERE b.bill_status =1 AND DATE(b.bill_date) = DATE('".$dCData['entry_date']."') AND DATE(br.bill_date) != DATE('".$dCData['entry_date']."')
			GROUP BY b.bill_cus_id
		) AS bill_return ON bill_return.bill_cus_id = cus.id_customer

		WHERE cus.id_customer =".$cus_id."

		GROUP BY cus.id_customer");

		//echo $this->db->last_query();exit;

        return $sql->result_array();

    }

	function get_tax_group_from_billing()
	{

		$data = $this->db->query("SELECT tgrp_id, tgrp_name FROM `ret_bill_details` d LEFT JOIN ret_billing bill ON bill.bill_id = d.bill_id LEFT JOIN ret_taxgroupmaster tgp ON tgp.tgrp_id = d.tax_group_id WHERE bill.bill_status = 1 AND tgrp_status = 1 GROUP BY d.tax_group_id");

		return $data->result_array();
	}



	function get_order_rate($order, $branch_id)
	{

		$sql = $this->db->query("SELECT

							bill.bill_date,
							bill.goldrate_22ct,
							r.rate_field,
							bill.goldrate_18ct,
							bill.goldrate_14ct,
							bill.goldrate_9ct,
							bill.silverrate_1gm

						FROM `ret_billing_advance` rba
						LEFT JOIN ret_billing bill ON bill.bill_id = rba.bill_id
						LEFT JOIN customerorder co ON co.id_customerorder = rba.id_customerorder
						LEFT JOIN customerorderdetails ord ON ord.id_orderdetails = (SELECT MIN(one_ord.id_orderdetails) FROM customerorderdetails one_ord WHERE one_ord.id_customerorder = co.id_customerorder LIMIT 1)
						LEFT JOIN ret_product_master as pro ON pro.pro_id = ord.id_product
						LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id
						LEFT JOIN ret_metal_purity_rate r on r.id_metal = c.id_metal and r.id_purity=ord.id_purity
						WHERE rba.order_no = '" . $order . "' and
							  bill.id_branch = " . $branch_id . " AND rba.advance_amount>0");

		// print_r($this->db->last_query());exit;

		$return_data = $sql->result_array();

		return $return_data;



	}

	function getactivesize($id_product)
		{
			$sql = $this->db->query("SELECT id_size,CONCAT(id_size,'-',name) as size_name FROM ret_size where id_product=" . ($id_product) . " and active='1'");
			return $sql->result_array();
		}

	function get_test_datas()
	{
		$sql = $this->db->query("SELECT items FROM ret_admin_approval_status ");
		return $sql->result_array();
	}

	function otp_app_approval()
	{
		$sql = $this->db->query("SELECT apprl_id,apprl_bt_id,apprl_bill_discount,apprl_type,apprl_requested_by,apprl_disp_message,apprl_approved_by,apprl_approved_on,apprl_tot_bill_amount,apprl_cr_paid_amt,apprl_status,'3' as id_customer,'1' as emp_id,'Logimax Developer' as emp_name FROM ret_admin_approval_status WHERE apprl_status=0");
		return $sql->result_array();
	}


	function getApprovallists($approval_type){
		$return_data = array();

		$sql =  $this->db->query("SELECT IFNULL(apprl_bill_discount,'')  AS bill_discount,IFNULL(apprl_bt_id,'') as apprl_bt_id,IFNULL(apprl_disp_message,'') as apprl_disp_message,IFNULL(apprl_tot_bill_amount,'') as apprl_tot_bill_amount,IFNULL(apprl_cr_paid_amt,'') as apprl_cr_paid_amt,apprl_status,CONCAT(e.firstname,' ',e.lastname) as apprl_requested_by,a.apprl_id as approval_id,IFNULL(a.apprl_cus_id,'') as id_customer,IFNULL(CONCAT(c.firstname,' ',IFNULL(c.lastname,'')),'') as cus_name,IFNULL(c.mobile,'') as cus_mobile,IFNULL(a.apprl_esti_id,'') as apprl_esti_id,IFNULL(a.apprl_bt_code,'') as apprl_bt_code
		 FROM ret_admin_approval_status a
		 LEFT JOIN employee e on e.id_employee = a.apprl_requested_by
		 LEFT JOIN customer c on c.id_customer = a.apprl_cus_id
		 WHERE apprl_type=".($approval_type)." AND apprl_status=0
		 ORDER BY a.apprl_id DESC");
		//  print_r($this->db->last_query());exit;
		 $data = $sql->result_array();
		 foreach ($data as $key => $items) {
			$return_ids = [];
			$bt_codes = [];


			if($items['apprl_esti_id']!=''){
				$exploded = explode(',',$items['apprl_esti_id']);
				foreach($exploded as $est_id){
					$return_ids[] = array('esti_id' => $est_id);
				}
			}

			if($items['apprl_bt_code']!=''){
				$apprl_bt_code = explode(',',$items['apprl_bt_code']);

				foreach($apprl_bt_code as $code){
					// print_r($apprl_bt_id);exit;
					$bt_codes[] = array('trans_code' => $code);


				}


			}




				$return_data[] = array(
					'bill_discount' =>$items['bill_discount'],
					'apprl_bt_id' =>$items['apprl_bt_id'],
					'apprl_cr_paid_amt' =>$items['apprl_cr_paid_amt'],
					'apprl_disp_message' =>$items['apprl_disp_message'],
					'apprl_tot_bill_amount' =>$items['apprl_tot_bill_amount'],
					'apprl_status' =>$items['apprl_status'],
					'apprl_requested_by' =>$items['apprl_requested_by'],
					'approval_id' =>$items['approval_id'],
					'id_customer' =>$items['id_customer'],
					'cus_name' =>$items['cus_name'],
					'cus_mobile' =>$items['cus_mobile'],
					'bt_details' =>(($items['apprl_bt_id']!='') ? $this->get_bt_details($items['apprl_bt_id']): ''),
					'trans_code' =>($items['apprl_bt_id']!='' ? $this->get_trans_code($items['apprl_bt_id']): ''),
					'estids' =>($return_ids),
				);

				// echo "<pre>";print_r($return_data);exit;

		 }
		return $return_data;

	}


	function getApprovaldetails($apprl_id){

		$sql = $this->db->query("SELECT apprl_bill_discount AS bill_discount,apprl_bt_id,CONCAT(e.firstname,' ',e.lastname) as apprl_requested_by,apprl_disp_message,apprl_tot_bill_amount,apprl_cr_paid_amt,apprl_status,items as item_details
		FROM ret_admin_approval_status a
		LEFT JOIN employee e on e.id_employee = a.apprl_requested_by
		WHERE apprl_id=".($apprl_id)."
		");
		return $sql->result_array();

	}

	// function get_registered_devices_details(){

	// }

	function get_trans_code($trans_id)
	{
		$apprl_bt_code = explode(',',$trans_id);
		$code = array();
		foreach ($apprl_bt_code as $key => $id) {

			$sql = $this->db->query("SELECT transfer_item_type as s_type,branch_trans_code as trans_code,date_format(created_time,'%d/%m/%Y') as bt_date
			FROM ret_branch_transfer where branch_transfer_id=".($id)."");
			$code[]= $sql->row_array();
		}


		return $code;

	}

	function get_bt_details($bt_id)
	{
		$trans_id = explode(',',$bt_id);
		$data = array();
		$trans_codes = '';
		$net_wt = 0;

		foreach ($trans_id as $key => $id) {

			$sql = $this->db->query("SELECT b.transfer_item_type as s_type,b.branch_trans_code as trans_code,date_format(created_time,'%d/%m/%Y') as bt_date ,fb.name as from_branch, tb.name as to_branch,b.net_wt
			FROM ret_branch_transfer b
			LEFT JOIN branch  fb on fb.id_branch = b.transfer_from_branch
			LEFT JOIN branch  tb on tb.id_branch = b.transfer_to_branch
			 where branch_transfer_id=".($id)."");
			$data[]= $sql->row_array();
		}

		// print_r(($data));exit;

		foreach ($data as $key => $code) {
			$trans_codes =$trans_codes.$code['trans_code'].',';
			$net_wt += $code['net_wt'];
			$data = $code;

		}
		// print_r($net_wt);exit;


		if (substr($trans_codes, -1) === ',') {
			$trans_codes = substr($trans_codes, 0, -1);
		}
		$return_data = array(
			'to_branch' => $data['to_branch'],
			'from_branch' => $data['from_branch'],
			's_type' => $data['s_type'],
			'trans_code' => $trans_codes,
			'net_wt' => $net_wt,
		);




		return $return_data;


	}

	function get_prev_ref_no($ref_no) {







        $sql=$this->db->query("SELECT payment_ref_number FROM ret_billing_payment WHERE payment_ref_number='$ref_no'");



        return $sql->result_array();



    }


	function validate_huid($tag_id,$huid)
    {

        $bill = $this->db->query(" Select * from ret_bill_details d
		LEFT JOIN ret_billing bill on bill.bill_id = d.bill_id
		where bill.bill_status = 1 and  d.huid = '".$huid."'");

        if ( $bill->num_rows > 0){

			return 0;

		}else{

			$tag = $this->db->query(" Select t.hu_id from ret_taging t

			where (t.hu_id ='".$huid."' ||  t.hu_id2 ='".$huid."')  ".( $tag_id != '' ? " and t.tag_id != ".$tag_id : '' ));


			if ( $tag->num_rows > 0){

				return 0;

			}
		}

		return 1;
    }


	function get_denomination()

	{

		$sql = $this->db->query("SELECT * FROM denomination  ORDER by sort_order DESC");

		return $sql->result_array();

	}

	function ajax_getCashCollection()
	{

		$profile_settings=$this->get_profile_settings($this->session->userdata('profile'));

			$id_branch      =$this->input->post('id_branch');
			$counter_id     =$this->input->post('counter_id');
			$floor_id       =$this->input->post('floor_id');
			$FromDt     =$this->input->post('from_date');
			$ToDt     =$this->input->post('from_date');
			$cash_type     =$this->input->post('cash_type');

			$d1 = date_create($FromDt);
            $d2 = date_create($ToDt);

            $FromDt = date_format($d1,"Y-m-d");
            $ToDt = date_format($d2,"Y-m-d");

			if(is_array($this->input->post('employee_ids')))
            {
                $emp_ids = implode(', ',$this->input->post('employee_ids'));
            }
            else
            {
                $emp_ids = $this->input->post('employee_ids');
            }

			$return_data=array('general_pay'=>array(),'payment_details'=>array(),'chit_payment'=>array());

			if($cash_type==2){
				//GENERAL PAY
				$general_pay=$this->db->query("SELECT p.payment_amount,p.payment_mode,r.type,p.type as transcation_type,r.id_issue_receipt,p.nb_type
				FROM ret_issue_receipt r
				LEFT JOIN ret_issue_rcpt_payment p ON p.id_issue_rcpt=r.id_issue_receipt
				LEFT JOIN ret_branch_floor_counter f on f.counter_id=r.counter_id
				WHERE p.payment_status=1 and r.bill_status=1 and (r.receipt_type!=4 and r.receipt_type!=5 OR r.type = 1)
				".($id_branch!='' && $id_branch!=0 ? " and r.id_branch=".$id_branch."" :'')."
				and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($FromDt))."' AND '".date('Y-m-d',strtotime($ToDt))."')
				".($counter_id!='' && $counter_id!=0 ? " and r.counter_id=".$counter_id."" :'')."
				".($floor_id!='' && $floor_id!=0 ? " and f.floor_id=".$floor_id."" :'')."
				".($profile_settings['allow_bill_type']==3 ? " and (r.is_eda=1 OR r.is_eda=2)" : ($profile_settings['allow_bill_type']==1 ? " and r.is_eda=1" :" and r.is_eda=2") )."
				".($emp_ids!="" ? " and r.created_by in (".$emp_ids.")" :'')."
				");
				$return_data['general_pay']=$general_pay->result_array();
				//GENERAL PAY

				//PAYMENT DETAILS
				$payment_details = $this->db->query("SELECT p.id_billing_payment,p.type,p.bill_id,p.payment_for,p.payment_amount,p.card_no,p.cvv,p.payment_mode,
				p.nb_type
				FROM ret_billing_payment p
				LEFT JOIN ret_billing b on b.bill_id=p.bill_id
				LEFT JOIN ret_branch_floor_counter f on f.counter_id=b.counter_id
				where b.bill_id is not null and b.bill_status=1
				and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($FromDt))."' AND '".date('Y-m-d',strtotime($ToDt))."')
				".($counter_id!='' && $counter_id!=0 ? " and b.counter_id=".$counter_id."" :'')."
				".($floor_id!='' && $floor_id!=0 ? " and f.floor_id=".$floor_id."" :'')."
				".($profile_settings['allow_bill_type']==3 ? " and (b.is_eda=1 OR b.is_eda=2)" : ($profile_settings['allow_bill_type']==1 ? " and b.is_eda=1" :" and b.is_eda=2") )."
				and b.bill_type!=6  ".($id_branch!='' && $id_branch!=0 ? " and b.id_branch=".$id_branch."" :'')."
				".($emp_ids!="" ? " and b.id_employee in (".$emp_ids.")" :'')."");
				$return_data['payment_details']=$payment_details->result_array();
				//PAYMENT DETAILS
			}else if($cash_type==1){
				$payment_details = $this->db->query("SELECT IFNULL(sum(pm.payment_amount),0) as cash_bill_pay,pt.id_branch
				from payment_mode_details pm
				left join payment pt on pt.id_payment = pm.id_payment
				where pm.payment_status =1
				and pm.payment_mode = 'CSH' and pt.type=1 AND pt.payment_status = 1 and pm.is_active = 1
				and (DATE(
						CASE 
							WHEN pt.custom_entry_date IS NOT NULL 
							THEN pt.custom_entry_date 
							ELSE pt.date_payment 
						END
				) BETWEEN '".date('Y-m-d',strtotime($FromDt))."' AND '".date('Y-m-d',strtotime($ToDt))."')
				".($id_branch!='' && $id_branch!=0 ? " and pt.id_branch=".$id_branch."" :'')."
				".($emp_ids!="" ? " and pt.id_employee in (".$emp_ids.")" :'')."
				");
				// print_r($this->db->last_query());exit;
				$return_data['chit_payment']=$payment_details->row_array();
				//pm.payment_date
			}else if($cash_type==3){

				//GENERAL PAY
				$general_pay=$this->db->query("SELECT p.payment_amount,p.payment_mode,r.type,p.type as transcation_type,r.id_issue_receipt,p.nb_type
				FROM ret_issue_receipt r
				LEFT JOIN ret_issue_rcpt_payment p ON p.id_issue_rcpt=r.id_issue_receipt
				LEFT JOIN ret_branch_floor_counter f on f.counter_id=r.counter_id
				WHERE p.payment_status=1 and r.bill_status=1 and (r.receipt_type!=4 and r.receipt_type!=5 OR r.type = 1)
				".($id_branch!='' && $id_branch!=0 ? " and r.id_branch=".$id_branch."" :'')."
				and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($FromDt))."' AND '".date('Y-m-d',strtotime($ToDt))."')
				".($counter_id!='' && $counter_id!=0 ? " and r.counter_id=".$counter_id."" :'')."
				".($floor_id!='' && $floor_id!=0 ? " and f.floor_id=".$floor_id."" :'')."
				".($profile_settings['allow_bill_type']==3 ? " and (r.is_eda=1 OR r.is_eda=2)" : ($profile_settings['allow_bill_type']==1 ? " and r.is_eda=1" :" and r.is_eda=2") )."
				".($emp_ids!="" ? " and r.created_by in (".$emp_ids.")" :'')."
				");
				$return_data['general_pay']=$general_pay->result_array();
				//GENERAL PAY

				//PAYMENT DETAILS
				$payment_details = $this->db->query("SELECT p.id_billing_payment,p.type,p.bill_id,p.payment_for,p.payment_amount,p.card_no,p.cvv,p.payment_mode,
				p.nb_type
				FROM ret_billing_payment p
				LEFT JOIN ret_billing b on b.bill_id=p.bill_id
				LEFT JOIN ret_branch_floor_counter f on f.counter_id=b.counter_id
				where b.bill_id is not null and b.bill_status=1
				and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($FromDt))."' AND '".date('Y-m-d',strtotime($ToDt))."')
				".($counter_id!='' && $counter_id!=0 ? " and b.counter_id=".$counter_id."" :'')."
				".($floor_id!='' && $floor_id!=0 ? " and f.floor_id=".$floor_id."" :'')."
				".($profile_settings['allow_bill_type']==3 ? " and (b.is_eda=1 OR b.is_eda=2)" : ($profile_settings['allow_bill_type']==1 ? " and b.is_eda=1" :" and b.is_eda=2") )."
				and b.bill_type!=6  ".($id_branch!='' && $id_branch!=0 ? " and b.id_branch=".$id_branch."" :'')."
				".($emp_ids!="" ? " and b.id_employee in (".$emp_ids.")" :'')."");
				$return_data['payment_details']=$payment_details->result_array();
				//PAYMENT DETAILS

				$payment_details = $this->db->query("SELECT IFNULL(sum(pm.payment_amount),0) as cash_bill_pay,pt.id_branch
				from payment_mode_details pm
				left join payment pt on pt.id_payment = pm.id_payment
				where pm.payment_status =1
				and pm.payment_mode = 'CSH' and pt.type=1
				and (DATE(
						CASE 
							WHEN pt.custom_entry_date IS NOT NULL 
							THEN pt.custom_entry_date 
							ELSE pt.date_payment 
						END
				) BETWEEN '".date('Y-m-d',strtotime($FromDt))."' AND '".date('Y-m-d',strtotime($ToDt))."')
				".($id_branch!='' && $id_branch!=0 ? " and pt.id_branch=".$id_branch."" :'')."
				".($emp_ids!="" ? " and pt.id_employee in (".$emp_ids.")" :'')."
				");
				// print_r($this->db->last_query());exit;
				//pm.payment_date
				$return_data['chit_payment']=$payment_details->row_array();

			}



		 return $return_data;

	}


	function ajax_getCashCollectionList()
	{
		$FromDt     =$this->input->post('from_date');
		$ToDt     =$this->input->post('to_date');
		$qry = $this->db->query("SELECT csh.*,b.name as branch_name,IFNULL(c.counter_name,'ALL') as counter_name,

		IF(csh.cash_type=1,'CRM',IF(csh.cash_type=2,'Retail','ALL')) as cash_type,

		IFNULL(csh.total_amount,0) - IFNULL(csh.cash_on_hand,0) as diff from ret_cash_collection csh
		left join branch b on b.id_branch=csh.branch_id
		LEFT JOIN ret_branch_floor_counter c ON c.counter_id=csh.counter_id
		where (date(csh.date) BETWEEN '".date('Y-m-d',strtotime($FromDt))."' AND '".date('Y-m-d',strtotime($ToDt))."')");

		// print_r($this->db->last_query());exit;


		foreach($qry->result_array() as $key => $res){
			$return_data[] = $res;
			$return_data[$key]['cash_details'] = $this->get_cashCollectionDetails($res['cash_collection_id']);
		}


		return $return_data;
	}


	function get_cashCollectionDetails($id)
	{
		$qry = $this->db->query("SELECT d.value note,cd.value,cd.amount FROM `ret_cash_collection_details` cd
		LEFT JOIN denomination d ON d.id_denomination=cd.denomination_id
		WHERE cd.cash_collection_id='" . $id . "' order by d.sort_order DESC ");

		// print_r($this->db->last_query());exit;

		return $qry->result_array();
	}

	function getDenomination($id)
	{
		$qry = $this->db->query("SELECT c.*,br.name branch_name FROM `ret_cash_collection` c
		LEFT JOIN branch br ON br.id_branch=c.branch_id
		WHERE c.cash_collection_id='" . $id . "' ");

		// print_r($this->db->last_query());exit;

		return $qry->row_array();
	}

	function get_est_other_metal_details($est_item_id)



	{



            $sql =$this->db->query("SELECT m.tag_other_itm_grs_weight,m.tag_other_itm_wastage,m.tag_other_itm_mc,m.tag_other_itm_rate,m.tag_other_itm_pcs,m.tag_other_itm_amount,mt.metal



            FROM ret_bill_other_metals m



            LEFT JOIN metal mt ON mt.id_metal = m.tag_other_itm_metal_id WHERE m.bill_det_id = '".$est_item_id."'");



			return $sql->result_array();



	}

	function get_homebill_counters($data){

		$where = "";
		if(isset($data['status'])) {
			$where = $where." AND sect.status = ".$data['status'];
		}
		$sql=$this->db->query("SELECT sect.id_section,sect.section_name,sect.section_short_code as short_code,b.name as branch,sect_br.id_branch,sect.is_home_bill_counter
		FROM ret_section sect
		LEFT JOIN ret_section_branch sect_br on sect_br.id_section = sect.id_section
		LEFT join branch b on b.id_branch = sect_br.id_branch
		where sect.id_section IS NOT NULL and sect.is_home_bill_counter=1
		".($data['id_branch']!='' ? " and sect_br.id_branch=".$data['id_branch']."" :'')." ".$where." GROUP BY sect.id_section");
		return $sql->result_array();

	}


	function get_purchase_stone($est_item_id)
    {
        $sql = $this->db->query("SELECT tag.tag_id,SUM(IF(m.uom_id=6 , round(IFNULL((if(billstn.stone_cal_type=1,(billstn.wt),(billstn.wt))*IFNULL(purstn.po_stone_rate,IFNULL(tagstn.pur_rate,0))),0),2),0)) as dia_amount,
		SUM(IF(m.uom_id!=6 , round(IFNULL((if(billstn.stone_cal_type=1,(billstn.wt),(billstn.wt))*IFNULL(purstn.po_stone_rate,IFNULL(tagstn.pur_rate,0))),0),2),0)) as stn_amount,
		IFNULL(purstn.po_stone_rate,IFNULL(tagstn.pur_rate,0)) as po_stone_rate,IFNULL(SUM(tagstn.wt),0) as tagstn_wt,IFNULL(SUM(billstn.pieces),0) as stn_pcs,
		IFNULL(SUM(billstn.wt),0) as stn_wt
						FROM ret_estimation_item_stones  billstn
						LEFT JOIN ret_estimation_items   billdet ON billstn.est_item_id = billdet.est_item_id
						LEFT JOIN ret_taging tag ON tag.tag_id = billdet.tag_id
						LEFT JOIN ret_taging as ref_tag on ref_tag.tag_id = tag.ref_tag_id
						LEFT JOIN (SELECT tagstn.tag_id,tagstn.stone_id ,pur_rate,wt FROM ret_taging_stone as tagstn GROUP BY tagstn.tag_id,tagstn.stone_id  ) tagstn ON tagstn.tag_id = billdet.tag_id and tagstn.stone_id = billstn.stone_id
						LEFT JOIN ret_stone st ON st.stone_id = billstn.stone_id
						LEFT JOIN ret_uom m ON m.uom_id = st.uom_id
						LEFT JOIN ret_lot_inwards l ON l.lot_no = IF(ref_tag.tag_id IS NULL,tag.tag_lot_id,ref_tag.tag_lot_id)
									LEFT JOIN (SELECT s.po_stone_rate,s.po_stone_id,r.po_item_po_id,r.po_item_pro_id,r.po_item_des_id,r.po_item_sub_des_id
											FROM ret_po_stone_items s
											LEFT JOIN ret_purchase_order_items r ON r.po_item_id = s.po_item_id
											GROUP BY s.po_stone_id,r.po_item_po_id,r.po_item_pro_id,r.po_item_des_id,r.po_item_sub_des_id) as purstn ON purstn.po_stone_id = tagstn.stone_id AND purstn.po_item_po_id = l.po_id AND purstn.po_item_pro_id = tag.product_id AND purstn.po_item_des_id = tag.design_id AND purstn.po_item_sub_des_id = tag.id_sub_design
						WHERE  billstn.est_item_id  = ".$est_item_id."

				  GROUP BY billstn.est_item_id ");
        return $sql->row_array();
    }

	function get_purchase_stone_tag($tag_id)
    {
        $sql = $this->db->query("SELECT tag.tag_id,SUM(IF(m.uom_id=6 , round(IFNULL((if(tagstn.stone_cal_type=1,tagstn.wt,tagstn.wt)*IFNULL(purstn.po_stone_rate,IFNULL(tagstn.pur_rate,0))),0),2),0)) as dia_amount,
		SUM(IF(m.uom_id!=6 ,round(IFNULL((if(tagstn.stone_cal_type=1,(tagstn.wt),(tagstn.wt))*IFNULL(purstn.po_stone_rate,IFNULL(tagstn.pur_rate,0))),0),2),0)) as stn_amount,
		IFNULL(purstn.po_stone_rate,IFNULL(tagstn.pur_rate,0)) as po_stone_rate,IFNULL(SUM(tagstn.wt),0) as tagstn_wt,IFNULL(SUM(tagstn.pieces),0) as stn_pcs,
		IFNULL(SUM(tagstn.wt),0) as stn_wt
						FROM ret_taging_stone tagstn
						LEFT JOIN ret_taging tag ON tag.tag_id = tagstn.tag_id
						LEFT JOIN ret_taging as ref_tag on ref_tag.tag_id = tag.ref_tag_id
						LEFT JOIN ret_stone st ON st.stone_id = tagstn.stone_id
						LEFT JOIN ret_uom m ON m.uom_id = st.uom_id
						LEFT JOIN ret_lot_inwards l ON l.lot_no = IF(ref_tag.tag_id IS NULL,tag.tag_lot_id,ref_tag.tag_lot_id)
									LEFT JOIN (SELECT s.po_stone_rate,s.po_stone_id,r.po_item_po_id,r.po_item_pro_id,r.po_item_des_id,r.po_item_sub_des_id
											FROM ret_po_stone_items s
											LEFT JOIN ret_purchase_order_items r ON r.po_item_id = s.po_item_id
											GROUP BY s.po_stone_id,r.po_item_po_id,r.po_item_pro_id,r.po_item_des_id,r.po_item_sub_des_id) as purstn ON purstn.po_stone_id = tagstn.stone_id AND purstn.po_item_po_id = l.po_id AND purstn.po_item_pro_id = tag.product_id AND purstn.po_item_des_id = tag.design_id AND purstn.po_item_sub_des_id = tag.id_sub_design
						WHERE  tagstn.tag_id  = ".$tag_id."

				  GROUP BY tag.tag_id ");
        return $sql->row_array();
    }

    	function get_rep_ord_stone_details($id_ord)

	{

		$tag_stone_query = $this->db->query("SELECT d.stone_id,pieces, wt, amount,stone_name, stone_code, uom_name,uom_short_code,is_apply_in_lwt , st.stone_type , stone_cal_type ,rate_per_gram,um.uom_short_code,
		 d.uom_id

            FROM customer_order_stone_details d
            left JOIN customer_order_other_details od on od.detail_id= d.id_other_details
            LEFT JOIN ret_stone as st ON st.stone_id = d.stone_id
            LEFT JOIN ret_uom as um ON um.uom_id = d.uom_id
            WHERE od.id_orderdetails  = '" . $id_ord . "'");

		return $tag_stone_query->result_array();
	}

	function get_order_advance_details($bill_adv_id){

		$advance_details=$this->db->query("SELECT a.order_no as bill_no,a.order_no,(a.advance_amount-a.adjusted_amount) as paid_advance,a.advance_weight as paid_weight,a.store_as,a.advance_type,a.rate_calc,a.rate_per_gram,a.bill_id,a.bill_adv_id,a.adjusted_amount as adjusted_advance,0 as balance_amount,0 as is_checked

		FROM ret_billing_advance a

		where  a.bill_adv_id  = $bill_adv_id 

		GROUP by a.bill_adv_id");

	   // print_r($this->db->last_query());exit;

		return $advance_details->row_array();

	}

	function get_petty_cash_issue_amt($data){

		$sql = $this->db->query("SELECT
				id_issue_receipt,
				
				-- Total petty cash (issue_type = 1)
				SUM(CASE WHEN issue_type = 1 " . ($data['id_branch'] != 0 && $data['id_branch'] != '' ? " AND id_branch = " . $data['id_branch'] : '') . " THEN amount ELSE 0 END) AS total_petty_cash,

				-- Total account head (issue_type = 5)
				SUM(CASE WHEN issue_type = 5 " . ($data['id_branch'] != 0 && $data['id_branch'] != '' ? " AND id_branch = " . $data['id_branch'] : '') . " THEN amount ELSE 0 END) AS total_acc_head_amount,

				-- Total refunded (receipt_type = 8)
				SUM(CASE WHEN receipt_type = 8 " . ($data['id_branch'] != 0 && $data['id_branch'] != '' ? " AND id_branch = " . $data['id_branch'] : '') . " THEN amount ELSE 0 END) AS total_refunded_amount,

				-- Total receipt amount (receipt_type = 9)
				SUM(CASE WHEN receipt_type = 9 " . ($data['id_branch'] != 0 && $data['id_branch'] != '' ? " AND id_branch = " . $data['id_branch'] : '') . " THEN amount ELSE 0 END) AS total_receipt_amount,

				-- Final in-hand amount
				SUM(
					(CASE WHEN issue_type = 1 " . ($data['id_branch'] != 0 && $data['id_branch'] != '' ? " AND id_branch = " . $data['id_branch'] : '') . " THEN amount ELSE 0 END)
					-
					(CASE WHEN issue_type = 5 " . ($data['id_branch'] != 0 && $data['id_branch'] != '' ? " AND id_branch = " . $data['id_branch'] : '') . " THEN amount ELSE 0 END)
					+
					(CASE WHEN receipt_type = 8 " . ($data['id_branch'] != 0 && $data['id_branch'] != '' ? " AND id_branch = " . $data['id_branch'] : '') . " THEN amount ELSE 0 END)
					+
					(CASE WHEN receipt_type = 9 " . ($data['id_branch'] != 0 && $data['id_branch'] != '' ? " AND id_branch = " . $data['id_branch'] : '') . " THEN amount ELSE 0 END)
				) AS tot_amt_inhand
					
			FROM ret_issue_receipt

			WHERE id_issue_receipt IS NOT NULL;
			");

		return $sql->row_array();
	
	}

	function get_petty_cash_emp($data){

		$sql = $this->db->query("SELECT IFNULL(firstname,'') as emp_name, IFNULL(mobile,'') as phn, is_store_manager, concat(ifnull(firstname, ''), '-', ifnull(mobile,'')) as customer,
				id_employee  as emp_id 
				FROM employee
				WHERE is_store_manager = 1 
				" . ($data['id_branch'] != 0 && $data['id_branch'] != '' ? " AND id_branch = " . $data['id_branch'] : '') . " ;
			");

		// print_r($this->db->last_query());exit;
		return $sql->row_array();

	}
		

	function getInsuranceDetails($id)
	{
		$sql = $this->db->query("SELECT insurance_amount,policy_from,policy_to,insurance_no FROM ret_insurance WHERE bill_id = ".$id);
        return $sql->result_array()[0];
	}

	function code_insurance_number_generator($id_branch, $metal_type, $is_eda)

	{

		$bill_no_padding = $this->get_ret_settings('bill_no_padding');

		$lastno = $this->get_insurance_last_code_no($id_branch, $metal_type, $is_eda);


				$code_det       = explode('INS', $lastno);

				$LastBillNo      = $code_det[1];
			

			$number = (int) $LastBillNo;

			$number++;

			$code_number = str_pad($number, $bill_no_padding, '0', STR_PAD_LEFT);

			return $code_number;
		
	}

	function get_insurance_last_code_no($id_branch, $metal_type, $is_eda)

	{

		$fin_year = $this->get_FinancialYear();

		$sql = "SELECT (insurance_no) as lastBill_no

		FROM ret_insurance

		ORDER BY insurance_id DESC LIMIT 1";

		//print_r($sql);exit;


		return $this->db->query($sql)->row()->lastBill_no;
	}

	public function get_customer_order_details($order_id, $branch_id)
	{
		$sql = $this->db->query("SELECT SUM(IFNULL(od.rate,0.00)) as order_amount,ord.id_customerorder,ord.order_from
		FROM customerorder ord
		LEFT JOIN customerorderdetails od ON od.id_customerorder = ord.id_customerorder
		WHERE ord.id_customerorder = ".$order_id." AND ord.order_from = ".$branch_id." and od.orderstatus != 5 and od.orderstatus != 6 and od.orderstatus != 7
		GROUP BY ord.id_customerorder");
		// print_r($this->db->last_query());exit;
		return $sql->row_array();
	}

	public function update_customer_order_rate($order_id, $rate_type)
	{
		$this->db->where('id_customerorder', $order_id)
				->update('customerorder', ['rate_type' => $rate_type]);
	}

	public function get_total_advance_for_order($order_id, $exclude_bill_id = '')
	{
		$this->db->select_sum('advance_amount')
				->where('id_customerorder', $order_id);

		// Exclude this advance bill entry
		if (!empty($exclude_bill_id)) {
			$this->db->where('bill_id !=', $exclude_bill_id);
		}

		$result = $this->db->get('ret_billing_advance')->row_array();

		// print_r($result);exit;	

		return $result['advance_amount'] ?? 0;
	}

	function get_previous_credit_collections($bill_id, $bill_date, $bill_type)
	{
		$credit_collection_bills = $this->get_bill_ids($bill_id, $bill_date);
		$data = array();
		foreach ($credit_collection_bills as $bill_id) {
			$sql = $this->db->query("SELECT b.bill_id, b.credit_coll_refno, b.pur_ref_no, b.s_ret_refno, DATE_FORMAT(b.bill_date, '%Y-%m-%d %H:%i:%s') AS bill_date, IFNULL(b.credit_disc_amt, 0) AS credit_disc_amt, IFNULL(b.advance_deposit, 0) AS advance_deposit, COALESCE(oms.old_metal_amount, 0) AS old_metal_amount, COALESCE(bcu.chit_amount, 0) AS chit_amount, COALESCE(pay.payment_amount, 0) AS payment_amount, COALESCE(adut.advance_adjusted, 0) AS advance_adjusted FROM ret_billing b LEFT JOIN ( SELECT bill_id, SUM(rate) AS old_metal_amount FROM ret_bill_old_metal_sale_details GROUP BY bill_id ) oms ON oms.bill_id = b.bill_id LEFT JOIN ( SELECT bill_id, SUM(utilized_amt) AS chit_amount FROM ret_billing_chit_utilization GROUP BY bill_id ) bcu ON bcu.bill_id = b.bill_id LEFT JOIN ( SELECT bill_id, SUM(payment_amount) AS payment_amount FROM ret_billing_payment GROUP BY bill_id ) pay ON pay.bill_id = b.bill_id LEFT JOIN ( SELECT bill_id, SUM(utilized_amt) AS advance_adjusted FROM ret_advance_utilized GROUP BY bill_id ) adut ON adut.bill_id = b.bill_id WHERE b.bill_status = 1 AND b.bill_type = 8 AND b.bill_id = " . $bill_id . " GROUP BY b.bill_id, b.credit_coll_refno, b.pur_ref_no, b.s_ret_refno, b.bill_date, b.credit_disc_amt, b.advance_deposit");
			// Fetch the result as an associative array
			$result = $sql->result_array();
			if (!empty($result)) {
				$result[0]['invoice_no'] = $this->get_bill_no_format_detail($bill_id, $bill_type);
				$data[$bill_id] = $result[0]; // Use bill_id as the index
			}
		}
		// Print the data with bill_id as the index
		return $data;
	}
	function get_bill_ids($bill_id, $bill_date)
	{
		// Escape the variables to prevent SQL injection
		$escaped_bill_id = $this->db->escape($bill_id);
		$escaped_bill_date = $this->db->escape($bill_date);
		// Construct the query with proper escaping and quoting
		$sql = $this->db->query("
			SELECT bill_id 
			FROM ret_billing 
			WHERE ref_bill_id = $escaped_bill_id 
			AND DATE_FORMAT(created_time, '%Y-%m-%d %H:%i:%s') <= $escaped_bill_date
		");

		// Fetch the result as an associative array
		$bill_ids_result = $sql->result_array();
		// Extract the bill IDs into a simple array
		$bill_ids = array_column($bill_ids_result, 'bill_id');

		// Return the array of bill IDs
		return $bill_ids;
	}

	function getBill_refnumbers($bill_id)
	{
		$items_query = $this->db->query("SELECT date_format(bill.bill_date,'%d-%m-%Y') as ref_date,br.short_name as branch_code, IFNULL(bill.sales_ref_no,'') as sales_ref_no,IFNULL(bill.pur_ref_no,'') as pur_ref_no, IFNULL(bill.s_ret_refno,'') as s_ret_refno, mt.metal_code, IFNULL(bill.credit_coll_refno,'') as credit_coll_refno, IFNULL(bill.order_adv_ref_no,'') as order_adv_ref_no, IFNULL(bill.chit_preclose_refno,'') as chit_preclose_refno, bill.bill_type, is_to_be,is_credit
		FROM ret_billing bill
		LEFT JOIN metal mt on mt.id_metal=bill.metal_type
		LEFT JOIN branch as br ON br.id_branch = bill.id_branch
		where bill.bill_id=".$bill_id."");
		return $items_query->row_array();

	}
	public function get_adv_order_details($bill_id, $branch_id){

		$advance_details=$this->db->query("SELECT * FROM ret_billing_advance a

		LEFT JOIN customerorder co ON co.id_customerorder = a.id_customerorder

		where  a.bill_id  = $bill_id  and  co.order_from = $branch_id

		GROUP by a.bill_adv_id");

	//    print_r($this->db->last_query());exit;

		return $advance_details->result_array();
	}

	function get_all_ledgers() {
        $this->db->select('id_ledger, ledger_name, opening_balance, opening_date');
        $this->db->from('ledger_master');
        $this->db->order_by('ledger_name', 'ASC');
        return $this->db->get()->result_array();
    }

    function get_ledger_current_balance($id_ledger) {
             
        $ledger = $this->db->get_where('ledger_master', ['id_ledger' => $id_ledger])->row_array();
        $opening_date = !empty($ledger['opening_date']) ? date('d-m-Y', strtotime($ledger['opening_date'])) : '';

        $inputs = [
            'id_ledger' => $id_ledger,
            'from_date' => $opening_date,
            'to_date'   => date('d-m-Y'),
            'id_branch' => '' 
        ];

        $report_data = $this->ret_reports_model->getLedgerReportData($inputs);
		// echo"<pre>";print_r(end($report_data));exit;

        if (empty($report_data)) {
            $ledger = $this->db->get_where('ledger_master', ['id_ledger' => $id_ledger])->row_array();
            return $ledger ? floatval($ledger['opening_balance']) : 0;
        }
        $last_day_data = end($report_data);
        $current_balance = isset($last_day_data['opening']) && $last_day_data['opening'] >= 0 ? floatval($last_day_data['opening']) : 0;
		if($current_balance > 0){
        foreach ($last_day_data as $key => $row) {
            if (is_array($row)) { 
                $credit = isset($row['Credit']) && is_numeric($row['Credit']) ? floatval($row['Credit']) : 0;
                $debit  = isset($row['Debit'])  && is_numeric($row['Debit'])  ? floatval($row['Debit'])  : 0;
                $current_balance += $credit - $debit;
            }
        }
	}

        return $current_balance;
    }

    function process_ledger_transfer($data) {
        $this->db->trans_start();
        $this->db->insert('ret_ledger_transfer', $data);
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    function get_ledger_transfer_list() {
        $this->db->select('t.*, DATE(t.transfer_date) as transfer_date, l1.ledger_name as from_ledger_name, l2.ledger_name as to_ledger_name');
        $this->db->from('ret_ledger_transfer t');
        $this->db->join('ledger_master l1', 'l1.id_ledger = t.from_ledger_id', 'left');
        $this->db->join('ledger_master l2', 'l2.id_ledger = t.to_ledger_id', 'left');
        $this->db->order_by('t.transfer_date', 'ASC');
        return $this->db->get()->result_array();
    }

    function get_credit_history_for_print($current_bill_id) {
        // 1. Get Ref Bill ID
        $this->db->select('ref_bill_id');
        $this->db->where('bill_id', $current_bill_id);
        $query = $this->db->get('ret_billing');
        $current = $query->row_array();
        
        if (!$current || empty($current['ref_bill_id'])) {
            return [];
        }
        $ref_bill_id = $current['ref_bill_id'];

        // 2. Original Bill (Opening)
        $this->db->select('bill_id, bill_no, sales_ref_no, bill_date, tot_bill_amount, id_branch, fin_year_code');
        $this->db->where('bill_id', $ref_bill_id);
        $orig = $this->db->get('ret_billing')->row_array();

        // 3. Collections
        $this->db->select('bill_id, bill_no, credit_coll_refno, bill_date, tot_amt_received, id_branch, fin_year_code');
        $this->db->where('ref_bill_id', $ref_bill_id);
        $this->db->where('bill_type', 8); // Credit Collection
        $this->db->where('bill_status', 1);
        $this->db->order_by('bill_date', 'ASC');
        $this->db->order_by('bill_id', 'ASC');
        $colls = $this->db->get('ret_billing')->result_array();

        return [
            'original' => $orig,
            'collections' => $colls
        ];
    }


	// ============================================
	// POS INTEGRATION — Multi-Provider [BIL-NR01]
	// ============================================

	/**
	 * Check if a POS table exists — cached per request
	 */
	private $_posTablesChecked = array();
	private function _posTableSafe($table)
	{
	    if(!isset($this->_posTablesChecked[$table])){
	        $this->_posTablesChecked[$table] = $this->db->table_exists($table);
	    }
	    return $this->_posTablesChecked[$table];
	}

	/**
	 * Get list of POS devices filtered by device type setting.
	 * Joins with ret_pos_providers to include provider info.
	 */
	function getPOSDeviceList()
	{
	    if(!$this->_posTableSafe('ret_pos_device_list')) return array();
	    $device_query = $this->db->query("
	        SELECT d.id_device, d.dispname, d.poscode, d.merchantid, d.securitytoken, 
	               d.imei, d.is_default, d.id_provider, d.devicetype,
	               p.provider_code, p.provider_name
	        FROM ret_pos_device_list d
	        LEFT JOIN ret_pos_providers p ON d.id_provider = p.id_provider
	        WHERE (d.is_active = 1 OR d.is_active IS NULL)
	        ORDER BY d.is_default DESC, d.dispname ASC
	    ");
	    return $device_query->result_array();
	}

	/**
	 * Get full device details + provider config for a specific device.
	 * Returns device credentials AND provider API URLs/settings in one query.
	 */
	function getPOSDeviceDetails($deviceId)
	{
	    if(!$this->_posTableSafe('ret_pos_device_list')) return null;
	    $deviceId = intval($deviceId);
	    $device_query = $this->db->query("
	        SELECT d.*, 
	               p.provider_code, p.provider_name, p.auth_type,
	               p.api_url_uat_init, p.api_url_uat_status, p.api_url_uat_cancel,
	               p.api_url_live_init, p.api_url_live_status, p.api_url_live_cancel,
	               p.is_env_live, p.has_qr_display, p.has_callback, p.status_method
	        FROM ret_pos_device_list d
	        LEFT JOIN ret_pos_providers p ON d.id_provider = p.id_provider
	        WHERE d.id_device = $deviceId
	    ");
	    return $device_query->row_array();
	}

	/**
	 * Get provider details by provider ID
	 */
	function getPOSProvider($providerId)
	{
	    if(!$this->_posTableSafe('ret_pos_providers')) return null;
	    $providerId = intval($providerId);
	    $query = $this->db->query("SELECT * FROM ret_pos_providers WHERE id_provider = $providerId");
	    return $query->row_array();
	}

	/**
	 * Get the correct API URL (UAT or Live) for a given action.
	 * @param array $deviceDetails - result from getPOSDeviceDetails (includes provider info)
	 * @param string $action - 'init', 'status', or 'cancel'
	 * @return string - the resolved URL
	 */
	function getPOSApiUrl($deviceDetails, $action)
	{
	    $env = $deviceDetails['is_env_live'] ? 'live' : 'uat';
	    $key = "api_url_{$env}_{$action}";
	    return isset($deviceDetails[$key]) ? $deviceDetails[$key] : '';
	}

	function getcusLastMobile($cusId)
	{
	    $cusId = intval($cusId);
	    $mob_query = $this->db->query("SELECT RIGHT(mobile,5) as mobile FROM customer where id_customer = $cusId");
	    return $mob_query->row()->mobile;
	}
	
	function getPOSMachineRequired()
	{
	    $val = $this->get_ret_settings('pay_by_pos');
	    return ($val !== null && $val !== false) ? $val : 0;
	}

	// --- POS Settings CRUD [BIL-NR01] ---

	function getAllPOSProviders()
	{
	    if(!$this->_posTableSafe('ret_pos_providers')) return array();
	    return $this->db->query("SELECT * FROM ret_pos_providers ORDER BY id_provider")->result_array();
	}

	function getPOSProviderById($providerId)
	{
	    if(!$this->_posTableSafe('ret_pos_providers')) return null;
	    return $this->db->query("SELECT * FROM ret_pos_providers WHERE id_provider = ".intval($providerId))->row_array();
	}

	function getAllPOSDevicesWithProvider()
	{
	    if(!$this->_posTableSafe('ret_pos_device_list')) return array();
	    return $this->db->query("
	        SELECT d.*, p.provider_name, p.provider_code
	        FROM ret_pos_device_list d
	        LEFT JOIN ret_pos_providers p ON d.id_provider = p.id_provider
	        ORDER BY d.id_device
	    ")->result_array();
	}

	function getPOSDeviceById($deviceId)
	{
	    if(!$this->_posTableSafe('ret_pos_device_list')) return null;
	    return $this->db->query("SELECT * FROM ret_pos_device_list WHERE id_device = ".intval($deviceId))->row_array();
	}

	function savePOSDevice($data)
	{
	    if(!$this->_posTableSafe('ret_pos_device_list')) return 0;
	    $this->db->insert('ret_pos_device_list', $data);
	    return $this->db->insert_id();
	}

	function updatePOSDevice($deviceId, $data)
	{
	    if(!$this->_posTableSafe('ret_pos_device_list')) return false;
	    $this->db->where('id_device', intval($deviceId));
	    return $this->db->update('ret_pos_device_list', $data);
	}

	function deletePOSDevice($deviceId)
	{
	    if(!$this->_posTableSafe('ret_pos_device_list')) return false;
	    $this->db->where('id_device', intval($deviceId));
	    return $this->db->update('ret_pos_device_list', array('is_active' => 0));
	}

	function setDefaultPOSDevice($deviceId)
	{
	    if(!$this->_posTableSafe('ret_pos_device_list')) return false;
	    // Unset all defaults first
	    $this->db->update('ret_pos_device_list', array('is_default' => 0));
	    // Set the selected device as default
	    $this->db->where('id_device', intval($deviceId));
	    return $this->db->update('ret_pos_device_list', array('is_default' => 1));
	}

	function toggleProviderEnv($providerId, $isLive)
	{
	    if(!$this->_posTableSafe('ret_pos_providers')) return false;
	    $this->db->where('id_provider', intval($providerId));
	    return $this->db->update('ret_pos_providers', array('is_env_live' => intval($isLive)));
	}

	function savePOSProvider($data)
	{
	    if(!$this->_posTableSafe('ret_pos_providers')) return 0;
	    $this->db->insert('ret_pos_providers', $data);
	    return $this->db->insert_id();
	}

	function updatePOSProvider($providerId, $data)
	{
	    if(!$this->_posTableSafe('ret_pos_providers')) return false;
	    $this->db->where('id_provider', intval($providerId));
	    return $this->db->update('ret_pos_providers', $data);
	}


	// ------------------------------------------
	// POS TRANSACTION LOG — Phase 5
	// ------------------------------------------

	/**
	 * Get all POS transactions with provider + device names
	 * Joins ret_pos_requests → ret_pos_providers + ret_pos_device_list
	 */
	function getAllPOSTransactions()
	{
	    if(!$this->_posTableSafe('ret_pos_requests')) return array();
	    $sql = "SELECT r.*, 
	                   p.provider_name, p.provider_code,
	                   d.dispname as device_name
	            FROM ret_pos_requests r
	            LEFT JOIN ret_pos_providers p ON p.id_provider = r.id_provider
	            LEFT JOIN ret_pos_device_list d ON d.merchantid = r.pos_mer_id
	            ORDER BY r.pos_req_id DESC
	            LIMIT 500";
	    $query = $this->db->query($sql);
	    return $query->result_array();
	}

	/**
	 * Get single POS transaction by ID (for detail modal)
	 */
	function getPOSTransactionById($id)
	{
	    if(!$this->_posTableSafe('ret_pos_requests')) return null;
	    $this->db->select('r.*, p.provider_name, p.provider_code');
	    $this->db->from('ret_pos_requests r');
	    $this->db->join('ret_pos_providers p', 'p.id_provider = r.id_provider', 'left');
	    $this->db->where('r.pos_req_id', intval($id));
	    $query = $this->db->get();
	    return $query->row_array();
	}

	// --- POS SECURITY HELPERS [Priority Fixes] ---

	/**
	 * Fix 2: Check for active (INIT/PENDING) POS transaction for a customer.
	 * Prevents duplicate payment submissions.
	 * Gap #6 Fix: Auto-expire INIT transactions older than 30 minutes —
	 * they are set to status 3 (FAILED) so they don't block future payments.
	 * Status: 0=INIT/PENDING, 1=SUCCESS, 2=CANCELLED, 3=FAILED
	 */
	function getActivePOSTransaction($cusid)
	{
	    if(!$this->_posTableSafe('ret_pos_requests')) return null;
	    $cusid = intval($cusid);
	    
	    // Gap #6: Auto-expire stuck INIT transactions older than 30 minutes
	    // Note: temporarily suppress DB errors in case pos_req_created_at column doesn't exist
	    $oldDebug = $this->db->db_debug;
	    $this->db->db_debug = FALSE;
	    $this->db->query("
	        UPDATE ret_pos_requests 
	        SET pos_req_status = 3 
	        WHERE pos_req_bill_cusid = $cusid
	        AND pos_req_status = 0
	        AND pos_req_created_at < DATE_SUB(NOW(), INTERVAL 30 MINUTE)
	    ");
	    $this->db->db_debug = $oldDebug;
	    
	    // Now check for any remaining active (non-expired) ones
	    $query = $this->db->query("
	        SELECT pos_req_id, pos_res_ref_id, pos_req_amount, pos_req_status
	        FROM ret_pos_requests
	        WHERE pos_req_bill_cusid = $cusid
	        AND pos_req_status = 0
	        ORDER BY pos_req_id DESC
	        LIMIT 1
	    ");
	    return $query->row_array();
	}

	/**
	 * Fix 5: Get a POS transaction by its reference ID.
	 * Used to check status before allowing cancellation.
	 */
	function getPOSTransactionByRef($refId)
	{
	    if(!$this->_posTableSafe('ret_pos_requests')) return null;
	    $refId = $this->db->escape($refId);
	    $query = $this->db->query("
	        SELECT pos_req_id, pos_res_ref_id, pos_req_status
	        FROM ret_pos_requests
	        WHERE pos_res_ref_id = $refId
	        LIMIT 1
	    ");
	    return $query->row_array();
	}

	/**
	 * Fix 3: Get the PhonePe salt key for callback signature verification.
	 * Looks up any PhonePe device to get the salt_key from its config.
	 */
	function getPhonePeSaltKey()
	{
	    if(!$this->_posTableSafe('ret_pos_device_list')) return '';
	    $query = $this->db->query("
	        SELECT d.salt_key
	        FROM ret_pos_device_list d
	        LEFT JOIN ret_pos_providers p ON d.id_provider = p.id_provider
	        WHERE p.provider_code LIKE 'phonepe%'
	        AND d.salt_key IS NOT NULL AND d.salt_key != ''
	        LIMIT 1
	    ");
	    $row = $query->row_array();
	    return $row ? $row['salt_key'] : '';
	}

	/**
	 * Helper: detect POS date column name
	 */
	private function _getPOSDateColumn()
	{
	    if(!$this->_posTableSafe('ret_pos_requests')) return '';
	    $candidates = array('pos_req_createdon', 'created_at', 'pos_created_on', 'pos_req_created_at');
	    $cols = $this->db->list_fields('ret_pos_requests');
	    foreach($candidates as $c){
	        if(in_array($c, $cols)) return $c;
	    }
	    return ''; // no date column found
	}

	/**
	 * Fix #31: Settlement reconciliation — daily totals by provider and status
	 */
	function getPOSSettlementSummary($fromDate, $toDate)
	{
	    $dateCol = $this->_getPOSDateColumn();
	    
	    if($dateCol){
	        $sql = "SELECT DATE(r.{$dateCol}) as txn_date,
	                       IFNULL(p.provider_name, 'Unknown') as provider_name,
	                       r.pos_req_status,
	                       COUNT(*) as txn_count,
	                       SUM(r.pos_req_amount) as total_paise
	                FROM ret_pos_requests r
	                LEFT JOIN ret_pos_providers p ON p.id_provider = r.id_provider
	                WHERE DATE(r.{$dateCol}) BETWEEN ? AND ?
	                GROUP BY DATE(r.{$dateCol}), IFNULL(p.provider_name, 'Unknown'), r.pos_req_status
	                ORDER BY txn_date DESC, provider_name";
	        return $this->db->query($sql, array($fromDate, $toDate))->result_array();
	    } else {
	        // No date column — return all grouped by provider/status
	        $sql = "SELECT NULL as txn_date,
	                       IFNULL(p.provider_name, 'Unknown') as provider_name,
	                       r.pos_req_status,
	                       COUNT(*) as txn_count,
	                       SUM(r.pos_req_amount) as total_paise
	                FROM ret_pos_requests r
	                LEFT JOIN ret_pos_providers p ON p.id_provider = r.id_provider
	                GROUP BY IFNULL(p.provider_name, 'Unknown'), r.pos_req_status
	                ORDER BY provider_name";
	        return $this->db->query($sql)->result_array();
	    }
	}

	/**
	 * Fix #35: Audit trail — all POS actions with user names
	 */
	function getPOSAuditTrail($fromDate, $toDate)
	{
	    $dateCol = $this->_getPOSDateColumn();
	    $dateSelect = $dateCol ? "r.{$dateCol} as pos_req_createdon" : "NULL as pos_req_createdon";
	    $dateWhere  = $dateCol ? "WHERE DATE(r.{$dateCol}) BETWEEN ? AND ?" : "WHERE 1=1";
	    $dateOrder  = $dateCol ? "r.{$dateCol} DESC" : "r.pos_req_id DESC";
	    
	    $sql = "SELECT r.pos_req_id, r.pos_trans_no, r.pos_req_amount,
	                   r.pos_req_status, r.pos_res_ref_id,
	                   r.pos_req_createdby, {$dateSelect},
	                   r.pos_req_bill_cusid, r.pos_req_bill_id,
	                   IFNULL(e.firstname, CONCAT('UID:', r.pos_req_createdby)) as created_by_name,
	                   IFNULL(p.provider_name, 'Unknown') as provider_name,
	                   IFNULL(d.dispname, '-') as device_name
	            FROM ret_pos_requests r
	            LEFT JOIN employee e ON e.id_employee = r.pos_req_createdby
	            LEFT JOIN ret_pos_providers p ON p.id_provider = r.id_provider
	            LEFT JOIN ret_pos_device_list d ON d.merchantid = r.pos_mer_id
	            {$dateWhere}
	            ORDER BY {$dateOrder}
	            LIMIT 500";
	    
	    $params = $dateCol ? array($fromDate, $toDate) : array();
	    return $this->db->query($sql, $params)->result_array();
	}

	// END POS INTEGRATION

	// ========================================================================
	// IRN E-Invoice Integration Model Functions
	// ========================================================================

	/**
	 * Get full branch details including GSP credentials by bill ID
	 * Used by IRN generation to get per-branch GSP config
	 */
	function getBranchDetailsForIRN($billId)
	{
		$sql = $this->db->query("SELECT br.*, 
		br.aspid AS gsp_aspid, 
		br.gsp_password AS gsp_ci_password, 
		br.gsp_user_name AS gsp_api_username, 
		br.eInvPwd AS gsp_einv_password
		FROM ret_billing b 
		LEFT JOIN branch br ON br.id_branch=b.id_branch 
		where b.bill_id = '" . $billId . "'");
		return $sql->row_array();
	}

	/**
	 * Get head office branch details for sales return IRN
	 */
	function getHOBranchDetails()
	{
		$sql = $this->db->query("SELECT *, 
		aspid AS gsp_aspid, 
		gsp_password AS gsp_ci_password, 
		gsp_user_name AS gsp_api_username, 
		eInvPwd AS gsp_einv_password
		FROM branch WHERE is_ho='1' AND active='1'");
		return $sql->row_array();
	}

	/**
	 * Get billing info with IRN-required fields (bill_type, id_branch)
	 * transfer_item_type derived from ret_bill_old_metal_sale_details (1=purchase/old metal, 0=regular)
	 */
	function getbillingInfoForIRN($billId)
	{
		$sql = $this->db->query("SELECT bill_id, bill_no, bill_cus_id, sales_ref_no, 
			date_format(bill_date,'%d/%m/%Y') as billdate, bill_type, id_branch, is_eda,
			ifnull(to_branch, id_branch) as to_branch, 
			IF(EXISTS(SELECT 1 FROM ret_bill_old_metal_sale_details WHERE bill_id = rb.bill_id AND bill_status = 1), '1', '0') as transfer_item_type
			FROM ret_billing rb
			where rb.bill_id = '" . $billId . "'");
		return $sql->row_array();
	}

	/**
	 * Update auth token on branch table (per-branch GSP auth)
	 */
	function updateBranchAuthToken($updateData, $id_branch)
	{
		$this->db->where('id_branch', $id_branch);
		$edit_flag = $this->db->update('branch', $updateData);
		return $edit_flag;
	}

	/**
	 * Clear auth token from branch (force re-authentication with GSP)
	 */
	function removeCompanyAuthToken($id_branch)
	{
		$this->db->where('id_branch', $id_branch);
		$edit_flag = $this->db->update('branch', array('authtoken' => NULL));
		return $edit_flag;
	}

	/**
	 * Get total values for sales return (bill_type=14) IRN
	 */
	function getbilltotalvaluesdetails_str($billId)
	{
		$sql = $this->db->query("SELECT sum(bd.item_total_tax) as item_total_tax,
                          sum(ifnull(bd.total_sgst, 0)) as total_sgst, sum(ifnull(bd.total_cgst, 0)) as total_cgst,
                          sum(ifnull(bd.total_igst, 0)) as total_igst,
                          sum(((item_cost - ifnull(item_total_tax,0)) + ifnull(total_sgst,0) + ifnull(total_cgst, 0) + ifnull(total_igst,0))) as valcost,
                          sum((item_cost - ifnull(item_total_tax,0))) costwotax,
                          (round(sum(CAST(((item_cost - item_total_tax) + total_sgst + total_cgst + total_igst) AS DECIMAL(10,2)))) - sum(CAST(((item_cost - item_total_tax) + total_sgst + total_cgst + total_igst) AS DECIMAL(10,2)))) as roundval,
                          (round(sum(CAST(((item_cost - item_total_tax) + total_sgst + total_cgst + total_igst) AS DECIMAL(10,2))))) as totalroundamt
                          FROM `ret_bill_return_details` as bd
                          LEFT JOIN ret_billing b ON b.bill_id = bd.ret_bill_id
                          WHERE bd.ret_bill_id = '" . $billId . "' GROUP BY bd.ret_bill_id");
		return $sql->row_array();
	}

	/**
	 * Get line item details for sales return IRN
	 */
	function getbillingdetailsitems_str($billId)
	{
		$returnData = array();
		$sql = $this->db->query("SELECT bd.id_bill_return_details as bill_det_id, cat.name as catname, pro.product_name,
			IFNULL(pro.hsn_code,'-') as hsn_code,
			bd.piece, bd.gross_wt, bd.item_cost, ifnull(bd.bill_discount, 0) as bill_discount, bd.item_total_tax,
			ifnull(bd.total_sgst, 0) as total_sgst, ifnull(bd.total_cgst, 0) as total_cgst,
			ifnull(bd.total_igst, 0) as total_igst,
			((item_cost - ifnull(item_total_tax,0)) + ifnull(total_sgst,0) + ifnull(total_cgst, 0) + ifnull(total_igst,0)) as valcost,
			(item_cost - ifnull(item_total_tax,0)) costwotax,
			CAST(((item_cost - item_total_tax) / if(ifnull(bd.gross_wt,0) = 0, bd.piece, bd.gross_wt)) AS DECIMAL(10,2)) as ratepergram,
			bd.tax_group_id
			FROM `ret_bill_return_details` as bd
			LEFT JOIN ret_product_master as pro ON pro.pro_id = bd.product_id
			LEFT JOIN ret_category as cat ON cat.id_ret_category = pro.cat_id
			WHERE bd.ret_bill_id = '" . $billId . "' GROUP BY bd.id_bill_return_details");

		$result = $sql->result_array();
		foreach ($result as $items) {
			$tax_details = $this->getBillingDetTaxPer($items['tax_group_id']);
			$items['GstRt'] = !empty($tax_details['tax_percentage']) ? $tax_details['tax_percentage'] : 0;
			$returnData[] = $items;
		}
		return $returnData;
	}

	/**
	 * Get line items for old metal transfer IRN
	 */
	function getbillingdetailsitems_old_metal($billId)
	{
		$returnData = array();
		$sql = $this->db->query("SELECT bd.id_old_metal_sale as bill_det_id, 
			IFNULL(m.metal, cat.name) as catname, 
			IFNULL(pro.product_name,m.metal) as product_name,
			IFNULL(pro.hsn_code,'7108') as hsn_code,
			1 as piece, bd.weight as gross_wt, bd.item_cost, 0 as bill_discount, bd.item_total_tax,
			ifnull(bd.total_sgst, 0) as total_sgst, ifnull(bd.total_cgst, 0) as total_cgst,
			ifnull(bd.total_igst, 0) as total_igst,
			((bd.item_cost - ifnull(bd.item_total_tax,0)) + ifnull(bd.total_sgst,0) + ifnull(bd.total_cgst, 0) + ifnull(bd.total_igst,0)) as valcost,
			(bd.item_cost - ifnull(bd.item_total_tax,0)) costwotax,
			CAST(((bd.item_cost - bd.item_total_tax) / if(ifnull(bd.weight,0) = 0, 1, bd.weight)) AS DECIMAL(10,2)) as ratepergram,
			bd.tax_group_id
			FROM `ret_bill_old_metal_sale_details` as bd
			LEFT JOIN metal m ON m.id_metal = bd.metal_type
			LEFT JOIN ret_product_master as pro ON pro.pro_id = bd.product_id
			LEFT JOIN ret_category as cat ON cat.id_ret_category = pro.cat_id
			WHERE bd.bill_id = '" . $billId . "'");

		$result = $sql->result_array();
		foreach ($result as $items) {
			$tax_details = $this->getBillingDetTaxPer($items['tax_group_id']);
			$items['GstRt'] = isset($tax_details['tax_percentage']) ? $tax_details['tax_percentage'] : 3;
			$returnData[] = $items;
		}
		return $returnData;
	}

	/**
	 * Get original invoice details for sales return IRN (RefDtls/PrecDocDtls)
	 */
	function getinvoicedetails_str($billId)
	{
		$sql = $this->db->query("SELECT 
			CONCAT(br.short_name, '-', b.fin_year_code, '-', b.sales_ref_no) as bill_no,
			date_format(b.bill_date,'%d/%m/%Y') as bill_date
			FROM ret_bill_return_details brd
			LEFT JOIN ret_billing b ON b.bill_id = brd.bill_id
			LEFT JOIN branch br ON br.id_branch = b.id_branch
			WHERE brd.ret_bill_id = '" . $billId . "' 
			GROUP BY brd.bill_id");
		return $sql->result_array();
	}

	// END IRN E-Invoice Integration

}
