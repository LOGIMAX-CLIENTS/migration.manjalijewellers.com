<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ret_order_model extends CI_Model
{
	function __construct()
    {
        parent::__construct();
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
            // If the value is empty, set it to the default value
            
            if ((empty($value) || $value == 'null')  ) {
                // $data[$field] = $default_values[$field];

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
	
    // public function insertData($data,$table)
    // {
    // 	$insert_flag = 0;
	// 	$insert_flag = $this->db->insert($table,$data);
	// 	return ($insert_flag == 1 ? $this->db->insert_id(): 0);
	// }
	// public function updateData($data,$id_field,$id_value,$table)
    // {
	//     $edit_flag = 0;
	//     $this->db->where($id_field,$id_value);
	// 	$edit_flag = $this->db->update($table,$data);
	// 	return ($edit_flag==1?$id_value:0);
	// }
	public function deleteData($id_field,$id_value,$table)
    {
        $this->db->where($id_field, $id_value);
        $status= $this->db->delete($table);
		return $status;
	}
	function get_FinancialYear()
	{
		$sql=$this->db->query("SELECT fin_year_code From ret_financial_year where fin_status=1");
		return $sql->row_array();
	}
	function get_profile_settings($id_profile)
    {
       $sql=$this->db->query("SELECT * FROM `profile` WHERE id_profile=".$id_profile."");
       return $sql->row_array();
    }
	function generatePurNo()
	{
	    $lastno = NULL;
	    $sql = "SELECT MAX(pur_no) as lastorder_no
					FROM customerorder o
					ORDER BY id_customerorder DESC
					LIMIT 1";
			$result = $this->db->query($sql);
			if( $result->num_rows() > 0){
				$lastno = $result->row()->lastorder_no;
			}
	    if($lastno != NULL)
		{
		    //$max_num = explode("-",$lastno);
            $number = (int) $lastno;
            $number++;
            $order_number = str_pad($number, 5, '0', STR_PAD_LEFT);
		}
		else
		{
           $order_number = str_pad('1', 5, '0', STR_PAD_LEFT);
		}
		return $order_number;
	}
	function generateOrderNo($id_branch,$order_type)
	{
		$branch_code = "";
		$lastno = NULL;
		$fin_year_code = "";
		$fin_year = $this->get_FinancialYear();
		$isBranch = (($id_branch > 0 && $id_branch != NULL && $id_branch != "") ? 1 : 0);
		$fin_sql = $this->db->query("SELECT fin_year_code FROM ret_financial_year WHERE fin_status = 1");
		if( $fin_sql->num_rows() > 0){
			$fin_year_code = $fin_sql->row()->fin_year_code;
		}
		$fin_sql->free_result();
		if($isBranch){
			$branch_sql = $this->db->query("SELECT short_name FROM branch WHERE id_branch=".$id_branch);
			$branch_code = $branch_sql->row()->short_name;
			$branch_sql->free_result();
				$sql = "SELECT  RIGHT(order_no, 5) as lastorder_no
					FROM customerorder o
					WHERE fin_year_code='".$fin_year['fin_year_code']."' ".($id_branch!='' ? " and order_from=".$id_branch." " :'')."
					AND o.order_type = ".$order_type."
					ORDER BY id_customerorder DESC
					LIMIT 1";
			$result = $this->db->query($sql);
			if( $result->num_rows() > 0){
				$lastno = $result->row()->lastorder_no;
			}
		}else{
			$sql = "SELECT  RIGHT(order_no, 5) as lastorder_no FROM customerorder o WHERE fin_year_code=".$fin_year['fin_year_code']." ".($isBranch ? " and id_branch=".$id_branch : '')." AND o.order_type = ".$order_type." ORDER BY id_customerorder DESC limit 1";
			$result = $this->db->query($sql);
			if( $result->num_rows() > 0){
				$lastno = $result->row()->lastorder_no;
			}
		}
	 	if($lastno != NULL)
		{
		    //$max_num = explode("-",$lastno);
            $number = (int) $lastno;
            $number++;
            $order_number = str_pad($number, 5, '0', STR_PAD_LEFT);
		}
		else
		{
           $order_number = str_pad('1', 5, '0', STR_PAD_LEFT);
		}
		/*
		*	2 -> Customized order, 5 -> Reserve Order or Tagged Order, 6 -> Home Bill Order
		*/
		$order_code = $order_type == 2 ? 'OR' : ($order_type == 5 ? 'RO' : ($order_type == 6 ? 'HO' : ($order_type == 3 ? 'RE' : ($order_type == 4 ? 'SRE' : ''))));
		$br_code = "";
		$ot_shortcode = "";
		if($branch_code != "") {
			$br_code = $branch_code;
		}
		if($br_code != "") {
			$br_code = $br_code;
		}
		if($order_code != "") {
			$ot_shortcode = $order_code;
		}
		if($ot_shortcode != "") {
			$ot_shortcode = $ot_shortcode;
		}
		$order_number = $br_code.$fin_year['fin_year_code'].'-'.$ot_shortcode.'-'.$order_number;
		return $order_number;
	}
	// Order Functions
	function getOrderNos($SearchTxt){
		$data = $this->db->query("SELECT orderno as label,id_orderdetails as value FROM customerorderdetails WHERE orderno like '%".$SearchTxt."%' ");
		return $data->result_array();
	}
	function getOrderByCus($id_cus){
		$data = $this->db->query("SELECT orderno,id_orderdetails FROM customerorderdetails od LEFT JOIN customerorder o on o.id_customerorder=od.id_customerorder WHERE o.order_for = 2 and order_to=".$id_cus);
		return $data->result_array();
	}
	function get_ret_settings($settings)
	{
		$data=$this->db->query("SELECT value FROM ret_settings where name='".$settings."'");
		return $data->row()->value;
	}
	function empty_rec_order()
    {
        $customer_due_date = $this->get_ret_settings('customer_due_date');
        $karigar_due_date = $this->get_ret_settings('karigar_orderalert_remain_days');
		$profile_settings = $this->get_profile_settings($this->session->userdata('profile'));
        $branch             = $this->get_headoffice_branch();
		$data['order'] = array(
			'id_customerorder'		=> NULL,
			'id_orderdetails'		=> NULL,
			'order_for'				=> 2,
			'order_to'				=> NULL,
			'ortertype'				=> NULL,
			'order_from'			=> NULL,
			'rate_calc_from'        => 1,
			'is_ho_branch'          =>$branch['id_branch'],
			'order_date'			=> date('d-m-Y'),
			'smith_due_date'		=> date('d-m-Y',(strtotime('+'.($customer_due_date-1).' day'))),
			'smith_remainder_date'	=> date('d-m-Y',(strtotime('+'.($customer_due_date-3-$karigar_due_date).' day'))),
		    'cus_due_date'			=> date('d-m-Y',(strtotime('+'.($customer_due_date).' day'))),
			'allow_bill_type'       => $profile_settings['allow_bill_type'],
		);
		$data['adv'] = array(
			'ortertype'			=> NULL,
			'cheque_date'		=> NULL,
			'advance_date'		=> date('d-m-Y'),
		);
		return $data;
	}
	function get_headoffice_branch()
	{
	    $sql=$this->db->query("SELECT * FROM branch WHERE is_ho=1");
	    return $sql->row_array();
	}
	function fetchEstiBySearch($SearchTxt){
		$result = $this->db->query("SELECT estimation_id as value,estimation_id as label from ret_estimation where estimation_id like '%".$SearchTxt."%'");
		return $result->result_array();
	}
	function getEstiDetailsById($SearchTxt){
		$sql = $this->db->query("SELECT  DATE_FORMAT(estimation_datetime,'%d-%m-%Y %H:%i:%s') as esti_date,discount,gift_voucher_amt,total_cost,concat(firstname,' ',if(lastname!=NULL,lastname,'')) as customer,c.title,c.mobile,id_customer
		 from ret_estimation e
		LEFT JOIN customer c on id_customer=e.cus_id
		where estimation_id=".$SearchTxt);
		$r = $sql->row_array();
		$sql_1 = $this->db->query("SELECT  if(product_short_code = '' or product_short_code is null ,product_name ,CONCAT(product_short_code,' - ',product_name) ) as product_name,`esti_id`,`item_type`,ei.`product_id`,`tag_id`,pur.`purity`,`size`,`uom`,`piece`,ei.`less_wt`,ei.`net_wt`,ei.`gross_wt`,`item_cost`,pur.id_purity,IFNULL(design_id,'' ) as design_no,IFNULL(design_name,'' ) as itemname
		 from ret_estimation_items ei
		LEFT JOIN ret_product_master p on pro_id=ei.product_id
		LEFT JOIN ret_design_master d on d.design_no=ei.design_id
		LEFT JOIN ret_purity pur on pur.id_purity=ei.purity
		where purchase_status=0 AND (item_type =2 OR (item_type = 1 AND is_non_tag=0)) AND esti_id=".$SearchTxt);
		$r1 = $sql_1->result_array();
		$old_matel_query = $this->db->query("SELECT old_metal_sale_id, est_id,
						   if(id_category=1,'Gold','Silver') as category, type, item_type, gross_wt,
						   ifnull(dust_wt,0.000) as dust_wt,ifnull(stone_wt,0.000) as stone_wt,
						   ifnull(net_wt,0.000) as net_wt,
						   round((ifnull(dust_wt,0.000) - ifnull(stone_wt,0.000)),3) as less_wt,purpose,
                           if(type = 1, 'Melting', 'Retag') as reusetype,
                           if(item_type = 1, 'Ornament', if(item_type = 2, 'Coin', if(item_type = 3, 'Bar',''))) as receiveditem, est_old.purity as purid, wastage_percent ,
						   wastage_wt, rate_per_gram, amount,
						   pur.purity, if(purpose=1,'Sale','Purchase' ) as purpose
						   FROM ret_estimation as est
							   LEFT JOIN ret_estimation_old_metal_sale_details as est_old ON est_old.est_id = est.estimation_id
							   LEFT JOIN ret_purity as pur ON pur.id_purity = est_old.purity
						   WHERE est_id=".$SearchTxt);
		$r2 = $old_matel_query->result_array();
		return array('esti' => $r , 'esti_items' => $r1 , 'esti_old_gold' => $r2  );
	}
	/*function ajax_getOrders(){
	    $id_branch =$this->session->userdata('id_branch');
		//0-> Pending 1->Process 2-> Confirm 3->Work in progress 4->delivery ready 5-> delivered 6->Canceled 7->Closed
		$result = $this->db->query("SELECT o.order_no,est_id as est_no,DATE_FORMAT(o.order_date,'%d-%m-%Y') as order_date,id_customerorder,if(order_for = 1,'Branch','Customer') as order_for,
		(select count(od.id_customerorder) from customerorderdetails od where id_customerorder=o.id_customerorder) as order_items,
		if(order_for = 2,concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')),b.name )as order_to,c.title,c.mobile,b.name
		from  customerorder o
		LEFT JOIN customer c on id_customer=o.order_to and order_for = 2
		LEFT JOIN branch b on b.id_branch=o.order_to and order_for = 1
		where o.order_type=2 ".($id_branch!='' && $id_branch!=0 ? " and o.order_from=".$id_branch."" :'')."
		order by o.order_no DESC");
		return $result->result_array();
	} */
	function ajax_getOrders($data){
	    $return_data=array();
	    $id_branch =$this->session->userdata('id_branch');
		//0-> Pending 1->Process 2-> Confirm 3->Work in progress 4->delivery ready 5-> delivered 6->Canceled 7->Closed
		$sql = $this->db->query("SELECT o.order_no,est_id as est_no,DATE_FORMAT(o.order_date,'%d-%m-%Y') as order_date,o.id_customerorder,if(order_for = 1,'Branch','Customer') as order_for,
		IF(order_type = 1,'Stock Order',IF(order_type = 2,'Customer Order',IF(order_type = 3,'Customer Repair',IF(order_type = 4,'Stock Repair',IF(order_type = 5,'Tagged Order',IF(order_type = 6,'Home Bill Order','-')))))) as ordertype_name, order_type,
		if(order_for = 2,concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')),b.name )as order_to,c.title,c.mobile,b.name,o.order_status,
		co.order_items,if(o.order_status=6,m.order_status,'') as status,co.tag_code,
		(SELECT MAX(orderstatus) FROM customerorderdetails WHERE id_customerorder = o.id_customerorder) AS max_order_status
		from  customerorder o
		LEFT JOIN (
		SELECT GROUP_CONCAT(tag_name) as tag_code,id_customerorder, count(id_customerorder) AS order_items
		FROM customerorderdetails
		WHERE orderstatus<=5
		GROUP BY id_customerorder) AS co ON co.id_customerorder = o.id_customerorder
		LEFT JOIN customer c on id_customer=o.order_to and order_for = 2
		LEFT JOIN branch b on b.id_branch=o.order_to and order_for = 1
		LEFT join order_status_message m on m.id_order_msg=o.order_status
		where (o.order_type=2 OR o.order_type=5 OR o.order_type=6)
		".($data['id_branch']!='' && $data['id_branch']!=0 ? " and o.order_from=".$data['id_branch']."" :'')."
		".($data['from_date']!='' && $data['to_date']!='' ? ' and date(o.order_date) BETWEEN "'.date('Y-m-d',strtotime($data['from_date'])).'" AND "'.date('Y-m-d',strtotime($data['to_date'])).'" ' :'')."
		order by o.order_no DESC");

		// echo $this->db->last_query();exit;


		$result =  $sql->result_array();
		foreach($result as $items)
		{
		    $orderDetails = $this->getCustomerOrderDetails($items['id_customerorder']);
			$profile  = $this->get_profile_settings($this->session->userdata('profile'));
		    $items['profile'] = $profile;
		    $items['order_details'] = $orderDetails;
		    $return_data[]=$items;
		}
		return $return_data;
	}
	function getOrder($id){
		$sql = $this->db->query("SELECT order_from,order_for,est_id as est_no,DATE_FORMAT(o.order_date,'%d-%m-%Y') as order_date,id_customerorder,DATE_FORMAT(o.order_date,'%d-%m-%Y') as order_date,
		order_to,concat(firstname,' ',if(lastname!=NULL,lastname,''),'-',c.mobile) as customer,c.title,c.mobile,b.name,o.order_no,o.work_at,o.order_type
		from  customerorder o
		LEFT JOIN customer c on id_customer=o.order_to and order_for = 2
		LEFT JOIN branch b on b.id_branch=o.order_to and order_for = 1
		where o.id_customerorder=".$id);
		$r =  $sql->row_array();
		return $r;
	}
	function getOrderDetails($id){

	     $returnData = array();
	     $sql_1 = $this->db->query("SELECT est_id as est_no,id_orderdetails,orderno,ortertype,od.id_product,od.design_no,od.id_sub_design,(p.cat_id) as id_ret_category,p.product_name,de.design_name,sub.sub_design_name,od.size,od.totalitems,od.mc,od.id_purity,od.wast_percent,if(od.id_mc_type=1,'Per Gram','Piece') as mc_type,weight,totalitems,concat(ci.value,' ',ci.name) as size_name,cat.name as category,
         DATE_FORMAT(od.cus_due_date,'%d-%m-%Y') as cus_due_date,DATE_FORMAT(od.smith_remainder_date,'%d-%m-%Y') as smith_remainder_date,DATE_FORMAT(od.smith_due_date,'%d-%m-%Y') as smith_due_date,od.id_purity,pur.purity,itemname,rate,m.order_status as status,od.image,od.orderstatus,
         DATEDIFF(od.cus_due_date, o.order_date) AS cus_due_date,IFNULL(od.reject_reason,'') as reject_reason,od.id_mc_type as mc_type, od.stn_amt,od.charge_value
         from customerorderdetails od
         LEFT JOIN customerorder o on o.id_customerorder=od.id_customerorder
         LEFT JOIN ret_product_master p on pro_id=od.id_product
         LEFT JOIN ret_design_master de on de.design_no=od.design_no
         LEFT JOIN ret_sub_design_master  sub on sub.id_sub_design=od.id_sub_design
         LEFT JOIN ret_size  ci on ci.id_size=od.size
         LEFT JOIN ret_purity pur on pur.id_purity=od.id_purity
         LEFT JOIN ret_category cat on cat.id_ret_category=p.cat_id
         LEFT join order_status_message m on m.id_order_msg=od.orderstatus
        where od.orderstatus<=4 AND o.id_customerorder=".$id);
		$result =  $sql_1->result_array();
		foreach($result as $items)
		{
		    $items['image_details']=$this->get_order_images($items['id_orderdetails']);
		    $returnData[]=$items;
		}
		return $returnData;
	}
	function get_order_charges($id_orderdetails)
    {
    	$sql=$this->db->query("SELECT oc.id_charge as charge_id,oc.amount as charge_value,c.name_charge
    	                       from ret_order_other_charges oc
    			       Left join ret_charges c on c.id_charge=oc.id_charge
    			       where oc.order_item_id=".$id_orderdetails."");
    	return $sql->result_array();
    }
	function getCustomerOrderDetails($id){
		$sql = $this->db->query("SELECT order_from,order_for,est_id as est_no,DATE_FORMAT(o.order_date,'%d-%m-%Y') as order_date,id_customerorder,
		order_to,concat(firstname,' ',if(lastname!=NULL,lastname,''),'-',c.mobile) as customer,c.title,c.mobile,b.name
		from  customerorder o
		LEFT JOIN customer c on id_customer=o.order_to and order_for = 2
		LEFT JOIN branch b on b.id_branch=o.order_to and order_for = 1
		where o.id_customerorder=".$id);
		$r =  $sql->row_array();
	     $returnData = array();
	     $sql_1 = $this->db->query("SELECT DATE_FORMAT(od.cus_due_date,'%d-%m-%Y') as cus_duedate,est_id as est_no,id_orderdetails,orderno,ortertype,od.id_product,od.design_no,od.id_sub_design,(p.cat_id) as id_ret_category,p.product_name,de.design_name,sub.sub_design_name,IFNULL(od.size,'') as size,od.totalitems,od.mc,od.id_purity,od.wast_percent,if(od.id_mc_type=1,'Per Gram','Piece') as mc_type,weight,totalitems,concat(ifnull(ci.value,''),' ',ifnull(ci.name,'')) as size_name,cat.name as category,
         DATE_FORMAT(od.cus_due_date,'%d-%m-%Y') as cus_due_date,DATE_FORMAT(od.smith_remainder_date,'%d-%m-%Y') as smith_remainder_date,DATE_FORMAT(od.smith_due_date,'%d-%m-%Y') as smith_due_date,od.id_purity,pur.purity,itemname,rate,m.order_status as status,od.image,od.orderstatus,
         DATEDIFF(od.cus_due_date, o.order_date) AS cus_due_date,IFNULL(od.reject_reason,'') as reject_reason,od.id_mc_type as mc_type, IFNULL(od.stn_amt,'') as stn_amt,IFNULL(od.less_wt,'') as less_wt,IFNULL(od.net_wt,'') as net_wt,IFNULL(od.description,'') as description
         from customerorderdetails od
         LEFT JOIN customerorder o on o.id_customerorder=od.id_customerorder
         LEFT JOIN ret_product_master p on pro_id=od.id_product
         LEFT JOIN ret_design_master de on de.design_no=od.design_no
         LEFT JOIN ret_sub_design_master  sub on sub.id_sub_design=od.id_sub_design
         LEFT JOIN ret_size  ci on ci.id_size=od.size
         LEFT JOIN ret_purity pur on pur.id_purity=od.id_purity
         LEFT JOIN ret_category cat on cat.id_ret_category=p.cat_id
         LEFT join order_status_message m on m.id_order_msg=od.orderstatus
        where o.id_customerorder=".$id);
		$result =  $sql_1->result_array();
		foreach($result as $items)
		{
		    $items['image_details']=$this->get_order_images($items['id_orderdetails']);
		    $returnData[]=$items;
		}
		return $returnData;
	}
    function get_order_stones($id_orderdetails)
    {
    		$sql=$this->db->query("SELECT os.is_apply_in_lwt,os.stone_type,os.stone_id,
    		os.pieces,os.wt,os.uom_id,
    		os.price as amount,os.stone_cal_type,os.rate_per_gram
    		FROM ret_order_item_stones os
    		where os.order_item_id=".$id_orderdetails."");
    		//print_r($this->db->last_query());exit;
    		return $sql->result_array();
    }
    function get_order_images($id_orderdetails)
    {
        $sql = $this->db->query("SELECT * FROM `customer_order_image` where id_orderdetails=".$id_orderdetails."");
        return $sql->result_array();
    }
	/*
	//New order
	function get_new_orderlist($from_date,$to_date,$branch,$id_karigar,$order_type,$repair_type,$work_type,$id_employee,$id_counter)
	{
		$branchWiseLogin=$this->session->userdata('branchWiseLogin');

		// echo $branchWiseLogin;exit;
		$id_branch=$this->session->userdata('id_branch');
		 $sql = $this->db->query("SELECT o.id_customerorder,od.id_orderdetails,od.orderno,
		 	od.ortertype,od.id_product,od.weight,od.size,IFNULL(od.totalitems,'') as totalitems,od.image,
		 	od.deliverydate,DATE_FORMAT(od.order_date,'%d-%m-%Y') as order_date,od.description,od.id_employee,
		 	od.reject_reason,od.customer_ref_no,o.order_taken_by,o.order_from as id_branch,
		 	od.id_karigar,p.product_short_code,p.product_name,p.hsn_code,p.cat_id,c.mobile,
		 	c.firstname as cus_name,k.firstname as karigar_name,
		 	DATE_FORMAT(od.cus_due_date,'%d-%m-%Y') as cus_due_date,e.firstname as emp_name,
		 	b.name as branch_name,od.image,m.order_status,m.color,od.orderstatus as cus_ord_status,IFNULL(DATE_FORMAT(od.smith_due_date,'%d-%m-%Y'),'') as smith_due_date,
		 	des.design_name,s.sub_design_name,t.tag_code,if(o.order_for=1,'Company','Customer') repair_type,if(o.work_at=1,'In House','Out Source') work_at,IFNULL(od.kar_remark,'') as kar_remark,IFNULL(cus.pur_no,'-') as pur_no
		 	from customerorderdetails od
		 	LEFT join customerorder o on o.id_customerorder=od.id_customerorder
			LEFT join customerorderdetails ok on ok.cus_orderdet_ref=od.id_orderdetails and ok.orderstatus !=6
			LEFT JOIN customerorder cus ON cus.id_customerorder=ok.id_customerorder and cus.order_status !=6
		 	LEFT join ret_product_master p on p.pro_id=od.id_product
		 	LEFT JOIN ret_design_master des on des.design_no=od.design_no
		 	left join ret_sub_design_master s on s.id_sub_design=od.id_sub_design
		 	LEFT join customer c on c.id_customer=o.order_to
		 	LEFT join ret_karigar k on k.id_karigar=od.id_karigar
		 	LEFT join employee e on e.id_employee=o.order_taken_by
		 	LEFT join branch b on b.id_branch=o.order_from
		 	LEFT join order_status_message m on m.id_order_msg=od.orderstatus
			left join ret_taging t on t.tag_id=od.tag_id
		 	where  o.order_type in (".$order_type.") and od.orderstatus<=3
			".($from_date!='' && $to_date!='' ? "and (date(o.order_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ": '')."
			".($branch!='' && $branch!=0?  " and o.order_from=".$branch."" :'')."
			".($id_employee!='' && $id_employee!=0 ?  " and o.order_taken_by=".$id_employee."" :'')."
			".($repair_type!='' && $repair_type!=0 ?  " and o.order_for=".$repair_type."" :'')."
			".($work_type!='' && $work_type!=0 ?  " and o.work_at=".$work_type."" :'')."
		    ".($id_karigar!='' ? " and od.id_karigar=".$id_karigar."" :'')."
		    ".($id_counter!='' ? " and o.counter_id=".$id_counter."" :'')."
		    order by od.id_orderdetails DESC");
		    // print_r($this->db->last_query());exit;
		 return $sql->result_array();
	}*/

		//New order
		function get_new_orderlist($from_date,$to_date,$branch,$id_karigar,$order_type,$repair_type,$work_type,$id_employee,$id_counter)
		{
			 $branchWiseLogin=$this->session->userdata('branchWiseLogin');
	
			// echo $branchWiseLogin;exit;
			 $id_branch=$this->session->userdata('id_branch');
			 $sql = $this->db->query("SELECT o.id_customerorder,od.id_orderdetails,od.orderno,
				 od.ortertype,od.id_product,od.weight,od.size,IFNULL(od.totalitems,'') as totalitems,od.image,
				 od.deliverydate,DATE_FORMAT(od.order_date,'%d-%m-%Y') as order_date,od.description,od.id_employee,
				 od.reject_reason,od.customer_ref_no,o.order_taken_by,o.order_from as id_branch,
				 od.id_karigar,p.product_short_code,p.product_name,p.hsn_code,p.cat_id,c.mobile,
				 c.firstname as cus_name,k.firstname as karigar_name,
				 DATE_FORMAT(od.cus_due_date,'%d-%m-%Y') as cus_due_date,e.firstname as emp_name,IFNULL(emp.firstname,'-') as employee_name,
				 b.name as branch_name,od.image,m.order_status,m.color,od.orderstatus as cus_ord_status,IFNULL(DATE_FORMAT(od.smith_due_date,'%d-%m-%Y'),'') as smith_due_date,
				 des.design_name,s.sub_design_name,t.tag_code,if(o.order_for=1,'Company','Customer') repair_type,if(o.work_at=1,'In House','Out Source') work_at,IFNULL(od.kar_remark,'') as kar_remark,IFNULL(cus.pur_no,'-') as pur_no
				 from customerorderdetails od
				 LEFT join customerorder o on o.id_customerorder=od.id_customerorder
				LEFT join customerorderdetails ok on ok.cus_orderdet_ref=od.id_orderdetails and ok.orderstatus !=6
				LEFT JOIN customerorder cus ON cus.id_customerorder=ok.id_customerorder and cus.order_status !=6
				 LEFT join ret_product_master p on p.pro_id=od.id_product
				 LEFT JOIN ret_design_master des on des.design_no=od.design_no
				 left join ret_sub_design_master s on s.id_sub_design=od.id_sub_design
				 LEFT join customer c on c.id_customer=o.order_to
				 LEFT join ret_karigar k on k.id_karigar=od.id_karigar
				LEFT join employee emp on emp.id_employee=od.emp_id
				 LEFT join employee e on e.id_employee=o.order_taken_by
				 LEFT join branch b on b.id_branch=o.order_from
				 LEFT join order_status_message m on m.id_order_msg=od.orderstatus
				left join ret_taging t on t.tag_id=od.tag_id
				 where  o.order_type in (".$order_type.") and od.orderstatus<=3
				".($from_date!='' && $to_date!='' ? "and (date(o.order_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ": '')."
				".($branch!='' && $branch!=0?  " and o.order_from=".$branch."" :'')."
				".($id_employee!='' && $id_employee!=0 ?  " and o.order_taken_by=".$id_employee."" :'')."
				".($repair_type!='' && $repair_type!=0 ?  " and o.order_for=".$repair_type."" :'')."
				".($work_type!='' && $work_type!=0 ?  " and o.work_at=".$work_type."" :'')."
				".($id_karigar!='' ? " and od.id_karigar=".$id_karigar."" :'')."
				".($id_counter!='' ? " and o.counter_id=".$id_counter."" :'')."
				order by od.id_orderdetails DESC");
				// print_r($this->db->last_query());exit;
			 return $sql->result_array();
		}
	function get_customerorder_details($id_orderdetails)
	{
			$sql=$this->db->query("SELECT * from customerorderdetails where id_orderdetails=".$id_orderdetails."");
			return $sql->row_array();
	}
	function get_order_item_totals($id_customerorder)
	{
		$sql = $this->db->query(
			"SELECT COALESCE(SUM(totalitems), 0) as total_pcs,
			        COALESCE(SUM(weight), 0) as total_wt
			 FROM customerorderdetails
			 WHERE id_customerorder = " . (int)$id_customerorder . "
			 AND orderstatus != 6"
		);
		return $sql->row_array();
	}
	function get_joborder_details($id_orderdetails)
	{
			$sql=$this->db->query("SELECT * from joborder where id_order=".$id_orderdetails."");
			return $sql->result_array();
	}
	function get_all_branch()
	{
		$sql=$this->db->query("select b.id_branch,b.name as branch_name from branch b where b.active=1");
		return $sql->result_array();
	}
	function get_orderdetails_by_id($id)
	{
		 $sql=$this->db->query("SELECT o.id_customerorder,od.id_orderdetails,od.orderno,
		 	od.ortertype,od.id_product,od.weight,od.size,od.totalitems,od.image,
		 	od.deliverydate,DATE_FORMAT(od.order_date,'%d-%m-%Y') as order_date,od.description,od.id_employee,
		 	od.reject_reason,od.customer_ref_no,o.order_taken_by,o.order_from as id_branch,
		 	j.id_vendor,pm.product_short_code,pm.product_name,pm.hsn_code,pm.cat_id,c.mobile,
		 	c.firstname as cus_name,k.firstname as karigar_name,
		 	DATE_FORMAT(od.cus_due_date,'%d-%m-%Y') as cus_due_date,e.firstname as emp_name,
		 	b.name as branch_name,od.image,m.order_status,m.color,p.purity as purity
		 	from customerorderdetails od
		 	LEFT join customerorder o on o.id_customerorder=od.id_customerorder
		 	LEFT join  joborder j on j.id_order=od.id_orderdetails
		 	LEFT join ret_product_master pm on pm.pro_id=od.id_product
		 	LEFT join ret_purity p on p.id_purity=od.id_purity
		 	LEFT join customer c on c.id_customer=o.order_to
			 LEFT join ret_karigar k on k.id_karigar=od.id_karigar
		 	LEFT join employee e on e.id_employee=o.order_taken_by
		 	LEFT join branch b on b.id_branch=od.branch_id
		 	LEFT join order_status_message m on m.id_order_msg=od.orderstatus
		 	where od.id_orderdetails=".$id." ");
		// print_r($this->db->last_query());exit;
		 return $sql->row_array();
	}
	//.New order
	function getActiveCategories()
    {
		$data = $this->db->query("
			SELECT
				c.tgrp_id,id_ret_category,name,description,c.id_metal,image
			FROM `ret_category` c
				LEFT JOIN metal m on m.id_metal=c.id_metal
			WHERE status = 1
		");
		return $data->result_array();
	}
	function getActiveproducts()
    {
        $data = $this->db->query("SELECT p.pro_id,p.product_name,p.cat_id
        FROM ret_product_master  p
        LEFT JOIN ret_category c on  c.id_ret_category=p.cat_id");
        return $data->result_array();
    }
    function getActivedesigns()
    {
        $data = $this->db->query("SELECT des.design_no,des.design_name,p.pro_id
        FROM ret_product_mapping p
        LEFT JOIN ret_product_master pro ON pro.pro_id=p.pro_id
        LEFT JOIN ret_design_master des ON des.design_no=p.id_design");
        //print_r($this->db->last_query());exit;
        return $data->result_array();
    }
    function getActivesubdesigns()
    {
        $data = $this->db->query("SELECT  sub.id_sub_design_mapping,sub.id_product,sub.id_design,sub.id_sub_design,sub_name.sub_design_name,pro.pro_id
        FROM ret_sub_design_mapping sub
        LEFT JOIN ret_product_master pro ON pro.pro_id=sub.id_product
        LEFT JOIN ret_design_master des ON des.design_no=sub.id_design
        LEFT JOIN ret_sub_design_master sub_name ON sub_name.id_sub_design=sub.id_sub_design");
        //print_r($this->db->last_query());exit;
        return $data->result_array();
    }
	function getAvailableTaxGroupItems($taxgroupid){
		$taxGroupData = $this->db->query("SELECT tgi_tgrpcode,
						tax_percentage,tgi_calculation ,tgi_type
						FROM ret_taxgroupitems as grpitems
						LEFT JOIN ret_taxmaster as tax ON tax.tax_id = grpitems.tgi_taxcode
						WHERE tgi_tgrpcode = ".$taxgroupid);
		return $taxGroupData->result_array();
	}
	function get_karigar_orders($id_customerorder)
	{
	    $sql=$this->db->query("SELECT SUM(d.totalitems) as tot_items,SUM(d.weight) as value,k.firstname as karigar_name,IFNULL(k.email,'') as email,c.company_name,
	    p.product_name,des.design_name,concat(s.value,' ',s.name) as size,IFNULL(k.contactno1,'') as mobile,cus.order_no,date_format(cus.order_date,'%d-%m-%Y') as order_date,
	    emp.firstname as emp_name
        FROM customerorder cus
        JOIN company c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=cus.id_customerorder
        LEFT JOIN joborder j ON j.id_order=d.id_orderdetails
        LEFT JOIN ret_karigar k ON k.id_karigar=j.id_vendor
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_size s ON s.id_size=d.size
        LEFT JOIN employee emp on emp.id_employee=cus.order_taken_by
        WHERE cus.order_for=2  AND cus.id_customerorder=".$id_customerorder."
        group by d.id_product,d.design_no");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
    function getSms_data($id_customerorder)
    {
        $sql=$this->db->query("SELECT SUM(d.totalitems) as tot_pcs,SUM(d.weight*d.totalitems) as approx_wt
        FROM customerorder c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder
        WHERE c.id_customerorder=".$id_customerorder."");
        //print_r($this->db->last_query());exit;
        return  $sql->row_array();
    }
    function get_product_size($id_product)
    {
        $sql=$this->db->query("SELECT * FROM `ret_size` WHERE active=1 ".($id_product!='' ? " and id_product=".$id_product."" :'')."");
        return $sql->result_array();
    }
    //Order Image and Desctiption
		function get_img_by_id($id_orderdetails)
		{
			// print_r($id_orderdetails);exit;
			$sql=$this->db->query("SELECT image from customer_order_image where id_orderdetails = $id_orderdetails");
			return $sql->row_array();
		}
		public function delete_order_img($id_orderdetails)
		{
			$edit_flag = 0;
			$data = [
				'image' => null,
			];
			$this->db->where('id_orderdetails', $id_orderdetails);
			$this->db->update('customerorderdetails', $data);
			return ($edit_flag==1?$id_value:0);
		}
		public function update_order_des($id_orderdetails, $description)
		{
			$edit_flag = 0;
			$data = [
				'description' => $description,
			];
			$this->db->where('id_orderdetails', $id_orderdetails);
			$this->db->update('customerorderdetails', $data);
			return ($edit_flag==1?$id_value:0);
		}
	function get_dec_by_id($id_orderdetails)
    {
		$sql=$this->db->query("SELECT description from customerorderdetails where id_orderdetails = $id_orderdetails");
    	return $sql->result_array();
    }
		//Order Image and Desctiption
	 //GET Sub Designs
    function getSearchSubDesign($data)
    {
		$result = $this->db->query("SELECT d.id_sub_design as value,d.sub_design_name as label
        FROM ret_sub_design_mapping s
        LEFT JOIN ret_sub_design_master d ON d.id_sub_design=s.id_sub_design
        WHERE s.id_sub_design is NOT NULL
        AND sub_design_name like '%".$data['searchTxt']."%'
         ".($data['design_no']!='' ? " and s.id_design=".$data['design_no']."" :'')."
         ".($data['id_product']!='' ? " and s.id_product=".$data['id_product']."" :'')."
        GROUP by d.id_sub_design");
		return $result->result_array();
	}
	function getSearchDesign($data)
    {
		$result = $this->db->query("
		SELECT des.design_no as value,des.design_name as label
        FROM ret_product_mapping p
        LEFT JOIN ret_product_master pro ON pro.pro_id=p.pro_id
        LEFT JOIN ret_design_master des ON des.design_no=p.id_design
        where des.design_no is NOT NULL AND design_name like '%".$data['searchTxt']."%' ".($data['product_id']!='' ? " and p.pro_id=".$data['product_id']."" :'')."
        GROUP by p.id_design");
        //print_r($this->db->last_query());exit;
		return $result->result_array();
	}
    //GET Sub Designs
	function get_active_cat_product_list()
	{
	    $data = $this->db->query("
			SELECT
				c.tgrp_id,id_ret_category,name,description,c.id_metal
			FROM `ret_category` c
				LEFT JOIN metal m on m.id_metal = c.id_metal
			WHERE status = 1
		");
		$reponse_data = $data->result_array();
		foreach($reponse_data as $catId => $catVal){
		    $reponse_data[$catId]['products'] = $this->get_active_product_byCatId($catVal['id_ret_category']);
		}
		return $reponse_data;
	}
	function get_active_product_byCatId($catId)
	{
	    $data = $this->db->query("
			SELECT
				pro_id, product_name
			FROM `ret_product_master` c
			WHERE product_status = 1 AND cat_id = '".$catId."'");
		return $data->result_array();
	}
	function getIssueTaggingBySearch($SearchTxt, $searchField, $id_branch)
	{
		$return_data=array();
        $data = $this->db->query("SELECT tag.tag_id as value, tag_code as label, tag.tag_type, tag_lot_id, design_id, cost_center,
                            tag.purity, tag.size, uom, piece, tag.less_wt, tag.net_wt, tag.gross_wt, tag.calculation_based_on,
                            retail_max_wastage_percent,tag_mc_value,tag_mc_type, halmarking, sales_value, tag.tag_status,
                             product_name, product_short_code, c.id_ret_category as catid, c.name as catname,
                            tag.product_id as lot_product, pur.purity as purname,lot_inw.lot_received_at,
                            tag.tag_id,pro.sales_mode,tag.item_rate,tag.current_branch,
                            des.design_name,tag.tag_mark, tag.id_sub_design as subdesignid, sdes.sub_design_name as sub_design_name
                            FROM ret_taging as tag Left join ret_lot_inwards_detail lot_det ON tag.id_lot_inward_detail = lot_det.id_lot_inward_detail
                            LEFT JOIN ret_lot_inwards as lot_inw ON lot_inw.lot_no = lot_det.lot_no
                            LEFT JOIN ret_product_master as pro ON pro.pro_id = tag.product_id
                            LEFT JOIN ret_design_master des on des.design_no=tag.design_id
                            left join ret_sub_design_master sdes on sdes.id_sub_design=tag.id_sub_design
                            LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity
                            left join ret_category c on c.id_ret_category=pro.cat_id
                            left join metal mt on mt.id_metal=c.id_metal
                            LEFT JOIN ret_stock_issue_detail as si ON si.tag_id = tag.tag_id
                            WHERE tag.tag_status = 0 and tag.id_orderdetails is NULL AND (si.status = 2 OR si.status = 3 OR si.tag_id IS NULL)
                            and tag.".$searchField." LIKE '%".$SearchTxt."%'  ".($id_branch !='' ? " and tag.current_branch = ".$id_branch."" :'')."");
       //print_r($this->db->last_query());exit;
        $tagging= $data->result_array();
        foreach($tagging as $tag)
        {
            $return_data[]=array(
                                 'current_branch'               =>$tag['current_branch'],
                                 'catid'                        => $tag['catid'],
                                 'catname'                      => $tag['catname'],
                                 'design_id'                    =>$tag['design_id'],
                                 'design_name'                  =>$tag['design_name'],
                                 'sub_design_name'              =>$tag['sub_design_name'],
                                 'subdesignid'                  => $tag['subdesignid'],
                                 'gross_wt'                     =>$tag['gross_wt'],
                                 'item_rate'                    =>$tag['item_rate'],
                                 'label'                        =>$tag['label'],
                                 'less_wt'                      =>$tag['less_wt'],
                                 'lot_product'                  =>$tag['lot_product'],
                                 'lot_received_at'              =>$tag['lot_received_at'],
                                 'net_wt'                       =>$tag['net_wt'],
                                 'piece'                        =>$tag['piece'],
                                 'product_name'                 => $tag['product_name'],
                                 'product_short_code'           =>$tag['product_short_code'],
                                 'purity'                       =>$tag['purity'],
                                 'purname'                      =>$tag['purname'],
                                 'sales_value'                  =>$tag['sales_value'],
                                 'size'                         =>$tag['size'],
                                 'tag_id'                       =>$tag['tag_id'],
                                 'tag_lot_id'                   =>$tag['tag_lot_id'],
                                 'tag_mc_type'                  =>$tag['tag_mc_type'],
                                 'tag_mc_value'                 =>$tag['tag_mc_value'],
                                 'tag_status'                   =>$tag['tag_status'],
                                 'value'                        =>$tag['value']
                                );
        }
        return $return_data;
	}
	function ajax_getRepairOrders($data)
	{
		// print_r($data);exit;
	    $returnData = array();
	    $sql=$this->db->query("SELECT c.id_customerorder,c.order_no,if(c.order_type=3,'Customer Order','Stock Order') as ordertype,c.order_pcs,date_format(c.order_date,'%d-%m-%Y') as order_date,br.name as branch_name,concat(IFNULL(cus.firstname,''),' - ',cus.mobile) as cus_name,
	    IFNULL(c.order_pcs,0) as order_pcs,IFNULL(c.order_approx_wt,0) as order_approx_wt,concat(e.firstname,' ',IFNULL(e.lastname,'')) as firstname
        FROM customerorder c
        LEFT JOIN branch br ON br.id_branch=c.order_from
        LEFT JOIN customer cus ON cus.id_customer=c.order_to
		LEFT JOIN employee e ON e.id_employee=c.order_taken_by
        WHERE (c.order_type=3 or c.order_type=4)
		".($data['id_branch']!='' && $data['id_branch']!=0 ? " and c.order_from=".$data['id_branch']."" :'')."
		".($data['id_employee']!='' ? " and c.order_taken_by=".$data['id_employee']."" :'')."
		".($data['id_counter']!='' ? " and c.counter_id=".$data['id_counter']."" :'')."
		".($data['from_date'] != '' && $data['to_date']!='' ? ' and date(c.order_date) BETWEEN "'.date('Y-m-d',strtotime($data['from_date'])).'" AND "'.date('Y-m-d',strtotime($data['to_date'])).'"' : '')."
		");

        // print_r($this->db->last_query());exit;


        $result = $sql->result_array();
        foreach($result as $items)
        {
            $orderDetails = $this->getRepairOrderDet($items['id_customerorder']);
		    $items['order_details'] = $orderDetails;
			$items['allow_cancel']=$this->getRepairDetailsStatus($items['id_customerorder']);
            $returnData[]=$items;
        }
        return $returnData;
	}
	function getRepairOrderDet($id_customerorder)
	{
	    $returnData = array();
	     $sql_1 = $this->db->query("SELECT est_id as est_no,od.id_orderdetails,orderno,ortertype,od.id_product,od.design_no,od.id_sub_design,(p.cat_id) as id_ret_category,IFNULL(p.product_name,'') as product_name ,IFNULL(de.design_name,'') as design_name,IFNULL(sub.sub_design_name,'') as sub_design_name ,od.size,od.totalitems,od.mc,od.id_purity,od.wast_percent,if(od.id_mc_type=1,'Per Gram','Piece') as mc_type,weight,totalitems,concat(ci.value,' ',ci.name) as size_name,cat.name as category,
         DATE_FORMAT(od.cus_due_date,'%d-%m-%Y') as cus_due_date_date,DATE_FORMAT(od.smith_remainder_date,'%d-%m-%Y') as smith_remainder_date,DATE_FORMAT(od.smith_due_date,'%d-%m-%Y') as smith_due_date,od.id_purity,pur.purity,itemname,rate,m.order_status as status,od.image,od.orderstatus,
         DATEDIFF(od.cus_due_date, o.order_date) AS cus_due_date,IFNULL(r.name,'') as repair_type,IFNULL(od.description,'') as description,od.less_wt,od.net_wt,od.reject_reason,IFNULL(t.tag_code,'') tag_code
         from customerorderdetails od
         LEFT JOIN customerorder o on o.id_customerorder=od.id_customerorder
         LEFT JOIN ret_product_master p on pro_id=od.id_product
         LEFT JOIN ret_design_master de on de.design_no=od.design_no
         LEFT JOIN ret_sub_design_master  sub on sub.id_sub_design=od.id_sub_design
         LEFT JOIN ret_size  ci on ci.id_size=od.size
		 LEFT JOIN ret_taging t on t.tag_id=od.tag_id
         LEFT JOIN ret_purity pur on pur.id_purity=od.id_purity
         LEFT JOIN ret_category cat on cat.id_ret_category=p.cat_id
         LEFT join order_status_message m on m.id_order_msg=od.orderstatus
         LEFT JOIN ret_repair_master r ON r.id_repair_master = od.id_repair_master
        where o.id_customerorder=".$id_customerorder);
        // print_r($this->db->last_query());exit;
		$result =  $sql_1->result_array();
		foreach($result as $items)
		{
		    $items['image_details']=$this->get_order_images($items['id_orderdetails']);
		    $returnData[]=$items;
		}
		return $returnData;
	}

	function getRepairDetailsStatus($id_customerorder)
	{
	         $sql=$this->db->query("SELECT id_customerorder FROM customerorderdetails od
		 	where od.orderstatus>=3
		 	".($id_customerorder!='' ? " and od.id_customerorder=".$id_customerorder."" :'')." ");
		    // print_r($this->db->last_query());exit;

			$resultCount = $sql->num_rows();

			if($resultCount>0) {
				return 0;
			}else{
				return 1;
			}
	}

	function get_repair_orders_list($data)
	{
	         $sql=$this->db->query("SELECT o.id_customerorder,od.id_orderdetails,od.orderno,o.order_type,
		 	od.ortertype,od.id_product,od.weight,od.size,od.totalitems,od.image,
		 	DATE_FORMAT(od.order_date,'%d-%m-%Y') as order_date,od.description,od.id_employee,
		 	od.reject_reason,od.customer_ref_no,o.order_taken_by,o.order_from as id_branch,
		 	k.id_karigar as id_vendor,p.product_short_code,p.product_name,p.hsn_code,p.cat_id,c.mobile,
		 	concat(c.firstname,' - ',c.mobile) as cus_name,
		 	DATE_FORMAT(od.cus_due_date,'%d-%m-%Y') as cus_due_date,e.firstname as emp_name,od.orderstatus,
		 	b.name as branch_name,m.order_status,m.color,ifnull(des.design_name, '') as design_name,od.orderstatus,IFNULL(od.completed_weight,0) as completed_weight,IFNULL(od.rate,0) as amount,
		 	fb.name as from_branch,IFNULL(bill.bill_no,'-') as bill_no,bill.bill_id,if(o.order_for=1,'Company','Customer') repair_type,if(o.work_at=1,'In House','Out Source') work_type,o.work_at,k.firstname as karigar_name,img.image as order_img,sub.sub_design_name,

			IFNULL(tag.tag_code,'-') as tag_code,IFNULL(od.less_wt,'-') as less_wt,IFNULL(od.net_wt,'-') as net_wt,IFNULL(o.order_pcs,'-') as order_pcs,IFNULL(od.totalitems,'-') as totalitems,IFNULL(cus.pur_no,'-') as po_no,IFNULL(od.description,'-') as description,

			IF(o.work_at=2 , IF(kar.contactno1!='',kar.contactno1,(IF(kar.contactno2!='',kar.contactno2,'-'))), IF(o.work_at=1,IF(k.contactno1!='',k.contactno1,(IF(k.contactno2!='',k.contactno2,'-'))),'-')) as kar_mobile,

			IF(o.work_at=1 ,if(od.assign_to=1,k.firstname,emp.firstname), IF(o.work_at=2 , if(od.assign_to=1,kar.firstname,emp.firstname),'-' )) as kar_name,

			IF(o.work_at=1 , IFNULL(DATE_FORMAT(od.smith_due_date,'%d-%m-%Y'),''), IF(o.work_at=2 ,IFNULL(DATE_FORMAT(ok.smith_due_date,'%d-%m-%Y'),''),'- ' ))  as smith_due_date,

			if(o.work_at=2 , DATE_FORMAT(od.deliverydate,'%d-%m-%Y'), IF(o.work_at=1 , DATE_FORMAT(od.delivered_date,'%d-%m-%Y'), '-') ) as deliverydate

		 	FROM customerorderdetails od
		 	LEFT join customerorder o on o.id_customerorder=od.id_customerorder
		 	LEFT join customerorderdetails ok on ok.cus_orderdet_ref=od.id_orderdetails and ok.orderstatus !=6
			 LEFT JOIN customerorder cus ON cus.id_customerorder=ok.id_customerorder and cus.order_status !=6
		 	LEFT join ret_karigar kar on kar.id_karigar=ok.id_karigar
			LEFT JOIN (SELECT image,i.id_orderdetails FROM customer_order_image i
						group by i.id_orderdetails ) as  img on img.id_orderdetails = od.id_orderdetails
		 	LEFT join ret_product_master p on p.pro_id=od.id_product
		 	LEFT JOIN ret_design_master as des ON des.design_no = od.design_no
			LEFT JOIN ret_sub_design_master  sub on sub.id_sub_design=od.id_sub_design
			LEFT JOIN ret_taging  tag on if(od.tag_id is null,tag.id_orderdetails =od.id_orderdetails,tag.tag_id=od.tag_id)

		 	LEFT join customer c on c.id_customer=o.order_to
		 	LEFT join ret_karigar k on k.id_karigar=od.id_karigar
		 	LEFT JOIN employee emp ON emp.id_employee = od.emp_id
		 	LEFT join employee e on e.id_employee=o.order_taken_by
		 	LEFT join branch b on b.id_branch=od.current_branch
		 	LEFT join branch fb on fb.id_branch=o.order_from
		 	LEFT join order_status_message m on m.id_order_msg=od.orderstatus
		 	LEFT JOIN ret_billing bill on bill.bill_id = od.bill_id and bill.bill_status=1
		 	where (o.order_type=3 OR o.order_type=4)
		 	".($data['id_branch']!='' && $data['id_branch']!=0 ? " and od.current_branch=".$data['id_branch']."" :'')."
		 	".($data['order_status']!='' ? " and od.orderstatus=".$data['order_status']."" :'')."
		 	".($data['id_employee']!='' ? " and o.order_taken_by=".$data['id_employee']."" :'')."
		 	".($data['id_counter']!='' ? " and o.counter_id=".$data['id_counter']."" :'')."
		 	".($data['repair_type']!='' && $data['repair_type']>0 ? " and o.order_for=".$data['repair_type']."" :'')."
		 	".($data['work_type']!='' && $data['work_type']>0 ? " and o.work_at=".$data['work_type']."" :'')."
		 	".($data['from_date'] != '' && $data['to_date']!='' ? ' and date(od.order_date) BETWEEN "'.date('Y-m-d',strtotime($data['from_date'])).'" AND "'.date('Y-m-d',strtotime($data['to_date'])).'"' : '')."
		 	order by o.id_customerorder DESC");
		    // print_r($this->db->last_query());exit;
		 return $sql->result_array();
	}

	function get_repair_item_details($order_id)
    {
        $sql=$this->db->query("Select co.id_ret_category,co.id_purity,co.id_product,co.id_design,
		co.id_sub_design,IFNULL(co.gross_wt,'') as gross_wt,IFNULL(co.less_wt,'') as less_wt,IFNULL(co.net_wt,'') as net_wt,co.wast_percent,
		IFNULL(co.mc_value,'') as mc_value,co.mc_type,IFNULL(cat.name,'') as category_name,
		IFNULL(p.purity,'') as purity,IFNULL(pm.product_name,'')as product_name,
		IFNULL(dm.design_name,'')as design_name,IFNULL(sdm.sub_design_name,'')as sub_design_name,
		IF(co.item_type=1,'Non Tag','Repair') as item_type_name,co.item_type,IFNULL(sec.section_name,'') as section,co.pcs
        from customer_order_other_details co
        Left join ret_category cat on cat.id_ret_category=co.id_ret_category
		LEFT join ret_purity p on p.id_purity=co.id_purity
		LEFT join ret_product_master pm on pm.pro_id=co.id_product
		LEFT join ret_design_master dm on dm.design_no=co.id_design
		Left join ret_sub_design_master sdm on sdm.id_sub_design=co.id_sub_design
		LEFT JOIN ret_section sec ON sec.id_section = co.id_section
        where co.id_orderdetails=" .$order_id . "");
        // print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
	 function repair_detail($order_id)
    {
        $sql=$this->db->query("select co.order_type,co.order_from as id_branch,  co.id_customerorder,co.order_no,DATE_FORMAT(co.order_date,'%d-%m-%Y') order_date,DATE_FORMAT(cod.deliverydate,'%d-%m-%Y') deliverydate,b.name,c.firstname,c.mobile,c.id_village,v.village_name,co.order_taken_by,e.firstname as emp_name,cusState.name as cus_state,cusCity.name as cus_ciy,cusAdd.address1 as cus_address1,cusAdd.address2 as cus_address2,cusAdd.address3 as cus_address3,IF(co.order_for=1,'Branch','Customer') as order_for,DATE_FORMAT(cod.cus_due_date,'%d-%m-%Y') as cus_due_date
        from customerorder co
        left join customerorderdetails cod on (cod.id_customerorder=co.id_customerorder)
        left join branch b on (b.id_branch=co.order_from)
        left join customer c on (c.id_customer=co.order_to)
        left join village v on (v.id_village=c.id_village)
        LEFT JOIN address cusAdd on cusAdd.id_customer=c.id_customer
        LEFT JOIN state cusState on cusState.id_state=cusAdd.id_state
                LEFT JOIN city cusCity on cusCity.id_city=cusAdd.id_city
                LEFT JOIN country cusCty ON cusCty.id_country=cusAdd.id_country
				LEFT join employee e on e.id_employee=co.order_taken_by
        where co.id_customerorder=" .$order_id . "");
        // print_r($sql->row_array());exit;
        return $sql->row_array();
    }
	function get_repair_details($order_id)
    {
        $data=$this->db->query("select itemname,totalitems,id_product,id_customerorder,p.product_name,description,
        IFNULL(m.name,'') as repair_type,de.design_name,sub.sub_design_name,cod.less_wt,cod.net_wt,cod.weight as gross_wt,
		cod.id_orderdetails,IFNULL(t.tag_code,'') tag_code,DATE_FORMAT(cod.cus_due_date,'%d-%m-%Y') as  cus_due_date
        from customerorderdetails cod
        left join ret_product_master p ON p.pro_id=cod.id_product
		left join ret_taging t on t.tag_id=cod.tag_id
        LEFT JOIN ret_repair_master m on m.id_repair_master = cod.id_repair_master
		LEFT JOIN ret_design_master de on de.design_no=cod.design_no
		LEFT JOIN ret_sub_design_master  sub on sub.id_sub_design=cod.id_sub_design
        where cod.id_customerorder=" . $order_id . " ");
		$tagging= $data->result_array();
        foreach($tagging as $tag)
        {
            $return_data[]=array(
                                 'itemname'                     =>$tag['itemname'],
                                 'totalitems'                   =>$tag['totalitems'],
                                 'id_product'                   => $tag['id_product'],
                                 'id_customerorder'             =>$tag['id_customerorder'],
                                 'product_name'                 =>$tag['product_name'],
                                 'description'                  =>$tag['description'],
                                 'repair_type'                  =>$tag['repair_type'],
                                 'design_name'                  =>$tag['design_name'],
                                 'sub_design_name'              =>$tag['sub_design_name'],
                                 'less_wt'                      =>$tag['less_wt'],
                                 'net_wt'                       =>$tag['net_wt'],
                                 'gross_wt'                     =>$tag['gross_wt'],
								 'id_orderdetails'				=>$tag['id_orderdetails'],
								 'tag_code'						=>$tag['tag_code'],
								 'cus_due_date'					=>$tag['cus_due_date'],
								 'stone_details'                => $this->getorderstones($tag['id_orderdetails']),
                                );
        }
        return $return_data;
	}
    function get_repair_damage_master()
    {
        $sql=$this->db->query("SELECT * FROM ret_repair_master");
        return $sql->result_array();
    }
    	//cart
	function getActiveKarigar()
    {
		$data = $this->db->query("SELECT id_karigar,CONCAT(firstname,' ',lastname) as karigar,IFNULL(code_karigar,'') as code FROM `ret_karigar` WHERE status_karigar = 1");
		return $data->result_array();
	}
	function karigar_search($SearchTxt)
    {
		$data = $this->db->query("SELECT id_karigar as value,CONCAT(firstname,' ',lastname) as label,IFNULL(code_karigar,'') as code FROM `ret_karigar`
			WHERE firstname like '%".$SearchTxt."%' and status_karigar = 1");
		return $data->result_array();
	}
	function ajax_getCartOrders($data)
	{
		$return_data=array();
	    $sql=$this->db->query("SELECT o.id_cart_order,o.id_product,o.design_no,ifnull(o.size,'') as id_size, o.totalitems as totalitems ,
	    w.weight_description as weight_range,o.id_branch,IFNULL(b.name,'') as branch_name,
	    DATE_FORMAT(o.created_on,'%d-%m-%Y') as order_date,p.product_name,d.design_name,o.id_wt_range,
	    IFNULL(concat(s.value,' ',s.name),'') as size_name, w.value as weight_range_value,e.firstname as emp_name,IFNULL(v.id_karigar,'') as id_karigar,IFNULL(v.karigar_name,'') as karigar_name,
	    subDes.sub_design_name,o.id_sub_design, w.id_weight
        FROM order_cart o
        LEFT JOIN ret_product_master p ON p.pro_id=o.id_product
        LEFT JOIN ret_design_master d ON d.design_no=o.design_no
        LEFT JOIN ret_sub_design_master subDes on subDes.id_sub_design=o.id_sub_design
        LEFT JOIN ret_weight w ON w.id_weight=o.id_wt_range
        LEFT JOIN ret_uom m on m.uom_id=w.id_uom
        LEFT JOIN ret_size s ON s.id_size=o.size
        LEFT JOIN branch b on b.id_branch=o.id_branch
        LEFT JOIN employee e on e.id_employee=o.created_by
        LEFT JOIN (SELECT k.id_product,k.id_design,k.id_karigar,kr.firstname as karigar_name
                  FROM ret_karigar_products k
                  LEFT JOIN ret_karigar kr ON kr.id_karigar=k.id_karigar
                  WHERE k.status=1) as v ON (v.id_product=p.pro_id AND v.id_design=d.design_no)
        WHERE o.orderstatus=0
        ".($data['id_product']!='' ? " and o.id_product=".$data['id_product']."" :'')."
        ".($data['id_design']!='' ? " and o.design_no=".$data['id_design']."" :'')."
        ".($data['id_wt_range']!='' ? " and o.id_wt_range=".$data['id_wt_range']."" :'')."
        ");
       //print_r($this->db->last_query());exit;
       $order=$sql->result_array();
       foreach($order as $items)
       {
                $id_branch = $items['id_branch'];
                $id_product = $items['id_product'];
                $id_design = $items['design_no'];
                $id_sub_design = $items['id_sub_design'];
                $id_wt_range = $items['id_wt_range'];
                $id_size = $items['id_size'];

                // Handle nulls for SQL
                $where_size = ($id_size != '' && $id_size != 0) ? " = $id_size" : " IS NULL";
                $where_sub = ($id_sub_design != '' && $id_sub_design != 0) ? " = $id_sub_design" : " IS NULL";
                $where_design = ($id_design != '' && $id_design != 0) ? " = $id_design" : " IS NULL";
                $where_wt = ($id_wt_range != '' && $id_wt_range != 0) ? " = $id_wt_range" : " IS NULL";

                // Get Weight Range values
                $weight = $this->db->query("SELECT from_weight, to_weight FROM ret_weight WHERE id_weight $where_wt")->row_array();
                $from_weight = isset($weight['from_weight']) ? $weight['from_weight'] : 0;
                $to_weight = isset($weight['to_weight']) ? $weight['to_weight'] : 0;

                // 1. Get Reorder Settings
                $reorder = $this->db->query("SELECT IFNULL(min_pcs,0) as min_pcs, IFNULL(max_pcs,0) as max_pcs FROM ret_reorder_settings 
                        WHERE id_branch=$id_branch AND id_product=$id_product AND id_design $where_design AND id_sub_design $where_sub AND id_wt_range $where_wt AND size $where_size")->row_array();
                
                $min_pcs = isset($reorder['min_pcs']) ? $reorder['min_pcs'] : 0;
                $max_pcs = isset($reorder['max_pcs']) ? $reorder['max_pcs'] : 0;

                // Re-calculate with safe NULL handling
                $ordered = $this->db->query("SELECT IFNULL(SUM(det.totalitems),0) as ordered_pcs FROM customerorderdetails det 
                        LEFT JOIN customerorder c ON c.id_customerorder = det.id_customerorder
                        WHERE c.order_type=1 AND c.order_status=3 
                        AND det.id_product=$id_product AND det.design_no $where_design AND det.id_sub_design $where_sub AND det.size $where_size")->row()->ordered_pcs;

                $tagged = $this->db->query("SELECT IFNULL(SUM(piece),0) as tagged_pcs FROM ret_taging 
                        WHERE tag_status=0 AND current_branch=$id_branch 
                        AND product_id=$id_product AND design_id $where_design AND id_sub_design $where_sub AND size $where_size
                        AND gross_wt BETWEEN $from_weight AND $to_weight")->row()->tagged_pcs;

                $stockpcs = $this->db->query("SELECT IFNULL(SUM(pi.no_of_pcs),0) as stockpcs FROM ret_purchase_order_items pi 
                        LEFT JOIN ret_purchase_order po ON po.po_id = pi.po_item_po_id
                        WHERE po.is_approved = 1 AND pi.is_lot_created=0 
                        AND pi.po_item_pro_id=$id_product AND pi.po_item_des_id $where_design AND pi.po_item_sub_des_id $where_sub")->row()->stockpcs;

                $total = $ordered + $tagged + $stockpcs;
                $shortage = ($total < $min_pcs) ? ($min_pcs - $total) : 0;
                $excess = ($total > $max_pcs) ? ($total - $max_pcs) : 0;

        		$return_data[]=array(
						'id_cart_order'		=>$items['id_cart_order'],
						'branch_name'		=>$items['branch_name'],
						'design_name'		=>$items['design_name'],
						'sub_design_name'	=>$items['sub_design_name'],
						'id_sub_design'	    =>$items['id_sub_design'],
						'design_no'			=>$items['design_no'],
						'emp_name'			=>$items['emp_name'],
						'id_branch'			=>$items['id_branch'],
						'id_product'		=>$items['id_product'],
						'id_wt_range'		=>$items['id_wt_range'],
						'order_date'		=>$items['order_date'],
						'product_name'		=>$items['product_name'],
						'id_size'			=>$items['id_size'],
						'size_name'			=>$items['size_name'],
						'totalitems'		=>$items['totalitems'],
						'weight_range'		=>$items['weight_range'],
						'weight_range_value' =>$items['weight_range_value'],
						'id_karigar'        =>$items['id_karigar'],
						'karigar_name'      =>$items['karigar_name'],
						'min_pcs'           =>$min_pcs,
						'max_pcs'			=>$max_pcs,
						'ordered_pcs'       =>$ordered,
						'tagged_pcs'        =>$tagged,
						'stockpcs'          =>$stockpcs,
						'total_pcs'         =>$total,
						'shortage'          =>$shortage,
						'excess'            =>$excess
        						 );
       }
       return $return_data;
	}
	function getCartDetails()
	{
	    $sql=$this->db->query("SELECT c.id_cart_order,b.name as branch_name,p.product_name,des.design_name,subDes.sub_design_name,
        date_format(c.created_on,'%d-%m-%Y') as date_add,if(c.orderstatus=0,'In Cart',if(c.orderstatus=1,'Order Placed','Order Rejected')) as cart_status,cus.pur_no
        FROM order_cart c
        LEFT JOIN branch b on b.id_branch=c.id_branch
        LEFT JOIN ret_product_master p ON p.pro_id=c.id_product
        LEFT JOIN ret_design_master des ON des.design_no=c.design_no
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design=c.id_sub_design
        LEFT JOIN customerorderdetails d ON d.id_orderdetails=c.id_orderdetails
        LEFT JOIN customerorder cus ON cus.id_customerorder=d.id_customerorder
        LEFT JOIN employee e ON e.id_employee=c.created_by");
        return $sql->result_array();
	}
	function getReorderitems($id_branch,$id_product,$id_design,$id_weight)
	{
		$data=$this->db->query("SELECT IFNULL(SUM(s.min_pcs),0) as min_pcs,IFNULL(SUM(s.max_pcs),0) as max_pcs
        FROM ret_reorder_settings s
        LEFT JOIN ret_weight wt on wt.id_weight=s.id_wt_range
        where s.id_product is not null
        ".($id_branch!='' && $id_branch>0 ? " and s.id_branch=".$id_branch."":'')."
        ".($id_product!='' ? " and s.id_product=".$id_product."" :'')."
        ".($id_design!='' ? " and s.id_design=".$id_design."" :'')."
        ".($id_weight!='' ? " and wt.id_weight=".$id_weight."" :'')."");
        //print_r($this->db->last_query());exit;
        return $data->row()->max_pcs;
	}
	function get_purchase_order_Details()
	{
        $sql=$this->db->query("SELECT c.id_customerorder,IFNULL(c.order_pcs,0) as order_pcs,IFNULL(c.order_approx_wt,0) as order_approx_wt,IFNULL(c.delivered_wt,0) as delivered_wt,IFNULL(c.delivered_qty,0) as delivered_qty,k.firstname as karigar_name,
        date_format(c.order_date,'%d-%m-%Y') as order_date,c.pur_no,IFNULL(k.contactno1,'') as mobile,
        if(c.order_for=1,'Stock Order','Customer Order') as order_for,cus.order_no,br.name as cus_order_branch,m.order_status as order_status_msg,m.color
        FROM customerorder c
        LEFT JOIN ret_karigar k ON k.id_karigar=c.id_karigar
        LEFT JOIN customerorder cus ON cus.id_customerorder=c.cus_ord_ref
        LEFT JOIN branch br ON br.id_branch=cus.order_from
        LEFT JOIN order_status_message m ON m.id_order_msg=c.order_status
        WHERE c.pur_no IS NOT NULL and c.order_type=1
        Order by c.id_customerorder DESC");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	function get_karigar_order_details($id_customerorder)
	{
	    $sql=$this->db->query("SELECT (d.totalitems) as tot_items,concat(w.value,'',u.uom_name) as weight_range,k.firstname as karigar_name,IFNULL(k.email,'') as email,c.company_name, p.product_name,des.design_name,concat(s.value,' ',s.name) as size,
	    IFNULL(k.contactno1,'') as mobile,cus.order_no,date_format(cus.order_date,'%d-%m-%Y') as order_date, emp.firstname as emp_name,cus.pur_no, date_format(d.smith_due_date, '%d-%m-%Y') as smith_due_date,IFNULL(d.description,'') as description,IFNULL(w.value,0) as approx_wt,k.id_karigar as id_vendor
        FROM customerorder cus
        JOIN company c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=cus.id_customerorder
        LEFT JOIN joborder j ON j.id_order=d.id_orderdetails
        LEFT JOIN ret_karigar k ON k.id_karigar=cus.id_karigar
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_size s ON s.id_size=d.size
        LEFT JOIN ret_weight w ON w.id_weight=d.weight
        LEFT JOIN ret_uom u ON u.uom_id=w.id_uom
        LEFT JOIN employee emp on emp.id_employee=cus.order_taken_by
        WHERE cus.pur_no IS NOT NULL  AND cus.id_customerorder=".$id_customerorder."
        Order by d.id_product,d.design_no,d.id_sub_design,w.value");
        //print_r($this->db->last_query());exit;
        $result = $sql->result_array();
        return $result;
	}
	function get_karigar_order_products($id_customerorder)
	{
	    $responseData=array();
	    $sql=$this->db->query("SELECT p.product_name,des.design_name,d.id_product,d.design_no,d.id_sub_design,
	    IFNULL(d.description,'') as description
        FROM customerorder c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        WHERE c.pur_no IS NOT NULL AND c.id_customerorder=".$id_customerorder."
        GROUP by d.id_product,d.design_no,d.id_sub_design");
        //print_r($this->db->last_query());exit;
        $result = $sql->result_array();
        foreach($result as $items)
        {
            $weight_details=[];
            $size_details=[];
            $tot_items=[];
            $item_details = $this->get_karigar_order_product_details($id_customerorder,$items['id_product'],$items['design_no']);
            foreach($item_details as $itemDet)
            {
                $weight_details[]=array('weight_range'=>$itemDet['weight_range'],'approx_wt'=>$itemDet['approx_wt'],'tot_items'=>$itemDet['tot_items']);
                $size_details[]=array('size'=>$itemDet['size']);
                $tot_items[]=array('tot_items'=>$itemDet['tot_items']);
            }
            $responseData[]=array(
                                 'product_name'     =>$items['product_name'],
                                 'design_name'      =>$items['design_name'],
                                 'description'      =>$items['description'],
                                 'weight_details'   =>$weight_details,
                                 'size_details'     =>$size_details,
                                 'pcs_details'      =>$tot_items,
                                 );
        }
        return $responseData;
	}
	function get_karigar_order_product_details($id_customerorder,$id_product,$id_design)
	{
	    $sql=$this->db->query("SELECT p.product_name,des.design_name,(d.totalitems) as tot_items,concat(w.value,'',u.uom_name) as weight_range,
	    concat(s.value,' ',s.name) as size,IFNULL(w.value,0) as approx_wt,IFNULL(d.description,'') as description
        FROM customerorder c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_size s ON s.id_size=d.size
        LEFT JOIN ret_weight w ON w.id_weight=d.id_weight_range
        LEFT JOIN ret_uom u ON u.uom_id=w.id_uom
        WHERE c.pur_no IS NOT NULL AND c.id_customerorder=".$id_customerorder." AND d.id_product=".$id_product." AND d.design_no=".$id_design."
        Order by w.id_weight");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	function get_karigar_details($id_karigar)
	{
	    $sql=$this->db->query("SELECT k.id_karigar,concat(k.firstname,' ',IFNULL(k.lastname,'')) as karigar_name,k.contactno1 as mobile,k.code_karigar,IFNULL(k.address1,'') as address1,IFNULL(k.address2,'') as address2,IFNULL(k.address3,'') as address3,IFNULL(cy.name,'') as country_name,IFNULL(st.name,'') as state_name,IFNULL(ct.name,'') as city_name,
	    IFNULL(k.company,'') as company_name,IFNULL(k.address1,'') as address1,IFNULL(k.address2,'') as address2,IFNULL(k.address3,'') as address3,
	    cy.name as country_name,st.name as state_name,ct.name as city_name,IFNULL(k.gst_number,'') as gst_number,IFNULL(k.email,'') as email
        FROM ret_karigar k
        LEFT JOIN country cy ON cy.id_country=k.id_country
        LEFT JOIN state st ON st.id_state=k.id_state
        LEFT JOIN city ct ON ct.id_city=k.id_city
        where k.id_karigar=".$id_karigar."");
        //print_r($this->db->last_query());exit;
        return $sql->row_array();
	}
	function get_pur_order_details($data)
	{
	    $sql=$this->db->query("SELECT d.id_orderdetails,(d.totalitems) as tot_items,concat(w.value,'',u.uom_name) as weight_range,k.firstname as karigar_name,IFNULL(k.email,'') as email,
	    p.product_name,des.design_name,concat(s.value,' ',s.name) as size,IFNULL(k.contactno1,'') as mobile,cus.pur_no,date_format(cus.order_date,'%d-%m-%Y') as order_date,
	    emp.firstname as emp_name,m.order_status as order_status_msg,d.orderstatus,m.color,IFNULL(d.delivered_qty,0) as delivered_qty,date_format(d.delivered_date,'%d-%m-%Y') as delivered_date,
	    IFNULL(d.delivered_wt,0) as delivered_wt
        FROM customerorderdetails d
        LEFT JOIN customerorder cus ON cus.id_customerorder=d.id_customerorder
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_size s ON s.id_size=d.size
        LEFT JOIN ret_weight w ON w.id_weight=d.id_weight_range
        LEFT JOIN joborder j ON j.id_order=d.id_orderdetails
        LEFT JOIN ret_karigar k ON k.id_karigar=cus.id_karigar
        LEFT JOIN employee emp ON emp.id_employee=cus.order_taken_by
        LEFT JOIN order_status_message m ON m.id_order_msg=d.orderstatus
        LEFT JOIN ret_uom u ON u.uom_id=w.id_uom
        WHERE cus.order_for=1 and cus.pur_no is NOT NULL
        ".($data['from_date']!='' && $data['to_date']!='' && $data['date_group_by']==1 ? " and (date(cus.order_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')" :'')."
        ".($data['from_date']!='' && $data['to_date']!='' && $data['date_group_by']==2 ? " and (date(d.delivered_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')" :'')."
        ".($data['id_karigar']!='' ? " and cus.id_karigar=".$data['id_karigar']."" :'')."	");
        return $sql->result_array();
	}
	function get_karigar_pending_orders($data)
	{
	    $sql=$this->db->query("SELECT c.id_customerorder,c.pur_no,k.firstname as karigar_name
        FROM customerorder c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder
        LEFT JOIN joborder j ON j.id_order=d.id_orderdetails
        LEFT JOIN ret_karigar k ON k.id_karigar=c.id_karigar
        WHERE c.order_for=1 AND d.orderstatus=3 AND c.pur_no IS NOT NULL
        ".($data['id_karigar']!='' && $data['id_karigar']!=null ? " and c.id_karigar=".$data['id_karigar']."" :'')."
        GROUP by c.id_customerorder");
        return $sql->result_array();
	}
	function get_karigar_pending_order_details($data)
	{
	    $sql=$this->db->query("SELECT (d.totalitems) as tot_items,concat(w.value,'',u.uom_name) as weight_range,k.firstname as karigar_name,IFNULL(k.email,'') as email,c.company_name,
	    p.product_name,des.design_name,IFNULL(concat(s.value,' ',s.name),'') as size,IFNULL(k.contactno1,'') as mobile,cus.order_no,date_format(cus.order_date,'%d-%m-%Y') as order_date,
	    emp.firstname as emp_name,cus.pur_no, date_format(d.smith_due_date, '%d-%m-%Y') as smith_due_date,
	    IFNULL(d.description,'') as description,d.id_orderdetails,j.id as id_joborder,IFNULL(d.delivered_qty,0) as delivered_pcs
        FROM customerorder cus
        JOIN company c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=cus.id_customerorder
        LEFT JOIN joborder j ON j.id_order=d.id_orderdetails
        LEFT JOIN ret_karigar k ON k.id_karigar=j.id_vendor
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_size s ON s.id_size=d.size
        LEFT JOIN ret_weight w ON w.id_weight=d.id_weight_range
        LEFT JOIN ret_uom u ON u.uom_id=w.id_uom
        LEFT JOIN employee emp on emp.id_employee=cus.order_taken_by
        WHERE cus.order_for=1  AND cus.id_customerorder=".$data['id_customerorder']." and d.orderstatus=3
        ");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	function update_partial_order_delivery($data,$arith){
		$sql = "UPDATE customerorderdetails SET delivered_qty=(delivered_qty".$arith." ".$data['delivered_qty']."),delivered_wt=(delivered_wt".$arith." ".$data['delivered_wt']."),is_partial_delivery=".$data['is_partial_delivery']."
		WHERE id_orderdetails=".$data['id_orderdetails'];
		//print_r($sql);exit;
		$status = $this->db->query($sql);
		return $status;
	}
	function update_order_delivery($data,$arith){
		$sql = "UPDATE customerorderdetails SET delivered_qty=(delivered_qty".$arith." ".$data['delivered_qty']."),delivered_wt=(delivered_wt".$arith." ".$data['delivered_wt'].")
		WHERE id_orderdetails=".$data['id_orderdetails'];
		//print_r($sql);exit;
		$status = $this->db->query($sql);
		return $status;
	}
	function get_customer_order_pending_details($data)
	{
	    $returnData=array();
	    $sql=$this->db->query("SELECT c.id_customerorder,c.order_no
        FROM customerorder c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder
        LEFT JOIN joborder j ON j.id=d.id_orderdetails
        WHERE d.orderstatus<3
        GROUP by d.id_customerorder");
        $result= $sql->result_array();
        foreach($result as $items)
        {
            $returnData[]=array(
                              'id_customerorder'=>$items['id_customerorder'],
                              'order_no'        =>$items['order_no'],
                              'item_details'    =>$this->get_customer_order_item_details($items['id_customerorder']),
                             );
        }
        return $returnData;
	}
	function get_customer_order_item_details($id_customerorder)
	{
	    $sql=$this->db->query("SELECT (d.totalitems) as tot_items,concat(w.value,'',u.uom_name) as weight_range,k.firstname as karigar_name,IFNULL(k.email,'') as email,c.company_name,
	    p.product_name,des.design_name,concat(s.value,' ',s.name) as size,IFNULL(k.contactno1,'') as mobile,cus.order_no,date_format(cus.order_date,'%d-%m-%Y') as order_date,
	    emp.firstname as emp_name,cus.pur_no, date_format(d.smith_due_date, '%d-%m-%Y') as smith_due_date,
	    (SELECT image_name from ret_sub_design_mapping_images as img where is_default=1 and img.id_sub_design_mapping=m.id_sub_design_mapping) as default_image,
	    IFNULL(d.description,'') as description,m.id_sub_design_mapping,IFNULL(w.value,0) as approx_wt,j.id_vendor,d.id_product,d.id_customerorder
        FROM customerorder cus
        JOIN company c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=cus.id_customerorder
        LEFT JOIN joborder j ON j.id_order=d.id_orderdetails
        LEFT JOIN ret_karigar k ON k.id_karigar=j.id_vendor
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_size s ON s.id_size=d.size
        LEFT JOIN ret_weight w ON w.id_weight=d.weight
        LEFT JOIN ret_uom u ON u.uom_id=w.id_uom
        LEFT JOIN employee emp on emp.id_employee=cus.order_taken_by
        LEFT JOIN ret_sub_design_mapping m ON m.id_product=(d.id_product AND d.design_no AND d.id_sub_design)
        WHERE cus.order_no IS NOT NULL  AND cus.id_customerorder=".$id_customerorder."
        group by d.id_product");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	//cart
	function repair_order_acknowladgement($insOrder)
	{
	    $sql=$this->db->query("SELECT  o.id_customerorder,o.order_from,date_format(o.order_date,'%d-%m-%Y') as create_order_date,c.orderno,c.id_product, c.weight,c.cus_due_date,c.totalitems,p.product_name,
	    IF(o.order_type=1,'Stock Order ',if(o.order_type=2,'Customer Order',IF(o.order_type=3,'Customer Repair','Stock Repair'))) as order_type,(b.name)as branch,
		o.order_to,concat(cus.firstname,' ',ifnull(cus.lastname,'')) as cus_name,cus.mobile,a.id_state,a.id_city,a.address1,a.address2,a.address3,a.pincode,s.name as state_name, ci.name as city_name,
		date_format(c.cus_due_date,'%d-%m-%y') as cus_due_date,IFNULL(s.state_code,'') as state_code,IFNULL(c.description,'') as description,IFNULL(v.village_name,'') as village_name
		from customerorder o
		left join branch b  on  b.id_branch = o.order_from
		left join customer cus  on  cus.id_customer = o.order_to
		left join address a  on  a.id_customer = cus.id_customer
		left join  state s  on  s.id_state = a.id_state
		left join city ci  on  ci.id_city = a.id_city
		LEFT JOIN village v on v.id_village = cus.id_village
	    left join customerorderdetails c  on  c.id_customerorder = o.id_customerorder
		left join ret_product_master p  on  p.pro_id =c.id_product
		where c.id_customerorder=".$insOrder."");
		//print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	function get_active_metal()
	{
	    $sql=$this->db->query("SELECT * from metal");
        return $sql->result_array();
	}
	function get_cus_product($id_metal)
	{
	    $sql=$this->db->query("SELECT pro_id,metal_type,product_name
		from ret_product_master
		where product_status=1 and metal_type=".$id_metal."");
        return $sql->result_array();
	}
	function get_ActiveProducts($data)
    {
        $sql=$this->db->query("SELECT p.pro_id,p.product_name
        FROM ret_product_master p
        WHERE p.pro_id IS NOT NULL AND p.product_status=1
        ".($data['id_category']!='' ? " and p.cat_id=".$data['id_category']."" :'')."");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    function get_active_design_products($data)
    {
        $sql=$this->db->query("
        SELECT des.design_no,des.design_name
        FROM ret_product_mapping p
        LEFT JOIN ret_product_master pro ON pro.pro_id=p.pro_id
        LEFT JOIN ret_design_master des ON des.design_no=p.id_design
        where des.design_status = 1 and des.design_no is not null and des. ".($data['id_product']!='' ? " and p.pro_id=".$data['id_product']."" :'')."
        group by des.design_no");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    function get_order_total_advance($orderid)
    {
        $order_advance = 0;
        $sql = $this->db->query("SELECT sum(adv.advance_amount) as advance_amount, b.is_eda as is_eda
        FROM ret_billing_advance adv
        LEFT JOIN ret_billing b on b.bill_id = adv.bill_id
        WHERE id_customerorder = '".$orderid."'
        and b.bill_status=1");
        if($sql->num_rows() > 0){
            $order_advance = $sql->row_array();
        }
        return $order_advance;
    }
	function get_order_customer_id($orderid)
	{
	    $cusId = "";
	    $sql = $this->db->query("SELECT order_to FROM customerorder WHERE id_customerorder = '".$orderid."'");
	    if($sql->num_rows() > 0){
	        $cusId = $sql->row()->order_to;
	    }
	    return $cusId;
	}
	function get_order_details($orderid)
	{
	    $sql = $this->db->query("SELECT det.id_orderdetails, order_from
	                            FROM customerorderdetails det
	                            LEFT JOIN customerorder as cor ON cor.id_customerorder = det.id_customerorder
	                            WHERE det.id_customerorder = '".$orderid."'");
	    return $sql->result_array();
	}
	function bill_no_generate($id_branch, $is_eda)
	{
		$lastno = $this->get_max_bill_no($id_branch, $is_eda);
		if($lastno!=NULL)
		{
			$number = (int) $lastno;
			$number++;
			$code_number = str_pad($number, 5, '0', STR_PAD_LEFT);
			return $code_number;
		}
		else
		{
			$code_number = str_pad('1', 5, '0', STR_PAD_LEFT);
			return $code_number;
		}
	}
	function get_max_bill_no($id_branch, $is_eda)
    {
        $fin_year = $this->get_FinancialYear();
		$sql = "SELECT max(bill_no) as lastBill_no FROM ret_issue_receipt where fin_year_code=".$fin_year['fin_year_code']." ".($id_branch!='' && $id_branch>0 ? " and id_branch=".$id_branch."" :'')." 
		".($is_eda!='' && $is_eda >0 ? "and is_eda = " . $is_eda . "" : "")."
		ORDER BY id_issue_receipt DESC";
		return $this->db->query($sql)->row()->lastBill_no;
	}
	function getCompanyDetails($id_branch)
	{
		if($id_branch=='')
		{
			$sql = $this->db->query("Select  c.id_company,c.company_name,c.gst_number,c.short_code,c.pincode,c.cin_number,c.mobile,c.whatsapp_no,c.phone,c.email,c.website,c.address1,c.address2,c.id_country,c.id_state,c.id_city,ct.name as city,s.name as state,cy.name as country,cs.currency_symbol,cs.currency_name,cs.mob_code,cs.mob_no_len,c.mail_server,c.mail_password,c.send_through,c.mobile1,c.phone1,c.smtp_user,c.smtp_pass,c.smtp_host,c.server_type,cs.login_branch,
			s.state_code
			from company c
			join chit_settings cs
			left join country cy on (c.id_country=cy.id_country)
			left join state s on (c.id_state=s.id_state)
			left join city ct on (c.id_city=ct.id_city)");
		}
		else
		{
			$sql=$this->db->query("select b.name,b.address1,b.address2,c.company_name,
				cy.name as country,ct.name as city,s.name as state,b.pincode,s.id_state,s.state_code,cy.id_country,IFNULL(b.cin_number,'') as cin_number,
				IFNULL(b.gst_number,'') as gst_number
				from branch b
				join company c
				left join country cy on (b.id_country=cy.id_country)
				left join state s on (b.id_state=s.id_state)
				left join city ct on (b.id_city=ct.id_city)
				where b.id_branch=".$id_branch."");
		}
		$result = $sql->row_array();
		return $result;
	}
	function get_order_cus_details($id_cus_order)
	{
        $sql= $this->db->query("SELECT o.order_to,o.order_no as orderno,DATE_FORMAT(o.order_date,'%d-%m-%Y') as order_date,cus.firstname as cus_name,cus.mobile,
        IFNULL(a.address1,'') as address1,IFNULL(a.address2,'') as address2,IFNULL(a.address3,'') as address3,IFNULL(a.pincode,'') as pincode,
		ct.name as country_name,s.name as state_name,ct.name as city_name,s.name as cus_state,br.name as branch_name,br.id_branch,
		IFNULL(v.village_name,'') as village_name,IFNULL(s.state_code,'') as state_code
        FROM customerorder o
        LEFT JOIN customerorderdetails as od on od.id_customerorder=o.id_customerorder
        LEFT JOIN customer as cus on cus.id_customer=o.order_to
        LEFT JOIN address a on a.id_customer=cus.id_customer
        LEFT JOIN city ct on a.id_city=ct.id_city
        LEFT JOIN state s on s.id_state=a.id_state
        left join country cy on (a.id_country=cy.id_country)
        LEFT JOIN village v on v.id_village = cus.id_village
        LEFT JOIN branch br on br.id_branch = o.order_from
        WHERE o.id_customerorder=".$id_cus_order);
        return $sql->row_array();
	}
	function get_customer_orders($id_customerorder)
	{
	   $returnData=[];
       $sql= $this->db->query("SELECT od.id_orderdetails,od.orderno,od.id_product,od.design_no,od.id_sub_design,od.size,od.weight,od.totalitems,o.order_from,o.order_to,p.product_name,d.design_name,sub.sub_design_name,b.name,pur.purity,pur.description as pur_description,
	   concat(ifnull(s.value,''),'-',ifnull(s.name,'')) as size_name,IFNULL(od.description,'') as description,IFNULL(date_format(od.smith_due_date,'%d-%m-%Y'),'') as cus_due_date,m.order_status as status,ifnull(od.less_wt,'') as less_wt,
	   ifnull(od.net_wt,'') as net_wt,  ifnull(od.mc,'') as mc_value,ifnull(od.stn_amt,'') as stn_amt,ifnull(od.wast_percent,'') as wast_percent,
	   IFNULL(date_format(od.cus_due_date,'%d-%m-%Y'),'') as cus_duedate,od.rate,IFNULL(od.total_cgst,0) as total_cgst,IFNULL(od.total_sgst,0) as total_sgst,IFNULL(od.total_igst,0) as total_igst,tax.tax_percentage,
	   IFNULL(od.rate_per_gram,'') as rate_per_gram,IF(o.rate_type=1,'Order Rate(Fixed)','Delivery Rate(UnFixed)') as rate_type
	   FROM customerorderdetails od
	   LEFT JOIN customerorder as o on o.id_customerorder=od.id_customerorder
	   LEFT JOIN branch as b on b.id_branch=o.order_from
	   LEFT JOIN ret_product_master as p on p.pro_id=od.id_product
	   LEFT JOIN ret_design_master as d on d.design_no=od.design_no
	   LEFT JOIN ret_sub_design_master as sub on sub.id_sub_design=od.id_sub_design
	   LEFT JOIN ret_size as s on s.id_size=od.size
	   LEFT join order_status_message m on m.id_order_msg=od.orderstatus
	   LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,
                                    m.tax_percentage as tax_percentage,
                                    i.tgi_calculation as tgi_calculation
                                    FROM ret_taxgroupitems i
                                    LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=p.tgrp_id
	   LEFT JOIN ret_purity as pur on pur.id_purity = od.id_purity
	   where od.id_customerorder=".$id_customerorder);
       // echo "<pre>";print_r($this->db->last_query());exit;
       $result = $sql->result_array();
       foreach($result as $items)
		{
			$items['stone_details']=$this->getorderstones($items['id_orderdetails']);
		    $items['image_details']=$this->get_order_images($items['id_orderdetails']);
		    $returnData[]=$items;
		}
		return $returnData;
    }
	function get_metal_for_category($id_category)
	{
		$data=array();
		$sql = "SELECT
						id_metal
				FROM ret_category c
				WHERE id_ret_category = ".$id_category;
		$category = $this->db->query($sql);
		$data =	$category->row_array();
        return $data;
	}
	function get_cmp_state($id_branch)
	{
		$sql=$this->db->query("SELECT id_state from branch b
		where ".($id_branch!='' ? " b.id_branch=".$id_branch."" :'')."");
		return $sql->result_array();
	}
	function get_orderdetails($id)
	{
		 $sql=$this->db->query("SELECT o.id_customerorder,od.id_orderdetails,od.orderno,od.tag_id,ifnull(od.tag_name,'') as tag_name,
		 	od.ortertype,od.id_product,od.design_no,od.id_sub_design,od.weight as gross_wt,od.less_wt,od.net_wt,
			od.size,od.totalitems as piece,od.image,od.rate,od.rate_per_gram,
		 	od.deliverydate,DATE_FORMAT(od.order_date,'%d-%m-%Y') as order_date,ifnull(od.description,'') as description,od.id_employee,
		 	od.reject_reason,od.customer_ref_no,o.order_taken_by,o.order_from as id_branch,
		 	j.id_vendor,pm.product_short_code,pm.product_name,pm.hsn_code,pm.cat_id,c.mobile,
		 	concat(c.firstname,'-',c.mobile) as cus_name,c.id_customer,k.firstname as karigar_name,
		 	DATE_FORMAT(od.cus_due_date,'%d-%m-%Y') as cus_due_date,e.firstname as emp_name,
		 	b.name as branch_name,od.image,m.order_status,m.color,od.id_purity,p.purity,dm.design_name,sdm.sub_design_name,
			od.mc,od.wast_percent,od.id_mc_type as mc_type,DATE_FORMAT(od.smith_due_date,'%d-%m-%Y') as s_due_date,
            DATE_FORMAT(od.smith_remainder_date,'%d-%m-%Y') as s_remainder_date,od.charge_value,od.stn_amt,pm.cat_id,cat.id_metal,
			pm.sales_mode,pm.calculation_based_on,ad.id_state,o.order_no,o.balance_type,o.rate_type,DATEDIFF(od.cus_due_date, CURDATE()) AS days_difference,
			od.orderstatus,od.assign_to
		 	from customerorderdetails od
		 	LEFT join customerorder o on o.id_customerorder=od.id_customerorder
		 	LEFT join  joborder j on j.id_order=od.id_orderdetails
		 	LEFT join ret_product_master pm on pm.pro_id=od.id_product
			LEFT join ret_design_master dm on dm.design_no=od.design_no
			Left join ret_sub_design_master sdm on sdm.id_sub_design=od.id_sub_design
			Left join ret_category cat on cat.id_ret_category=pm.cat_id
		 	LEFT join ret_purity p on p.id_purity=od.id_purity
		 	LEFT join customer c on c.id_customer=o.order_to
			LEFT join address ad on ad.id_customer=c.id_customer
		 	LEFT join ret_karigar k on k.id_karigar=j.id_vendor
		 	LEFT join employee e on e.id_employee=o.order_taken_by
		 	LEFT join branch b on b.id_branch=o.order_from
		 	LEFT join order_status_message m on m.id_order_msg=od.orderstatus
		 	where od.id_customerorder=".$id." and od.orderstatus!=6 ");
		// print_r($this->db->last_query());exit;
		 $data= $sql->result_array();
		 foreach($data as $val)
		 {
			$returndata[]=array(
				'cus_name'  	    => $val['cus_name'],
				'id_ret_category'   => $val['cat_id'],
				'label'             => $val['tag_name'],
				'tag_id'            => $val['tag_id'],
				'id_customer'       => $val['id_customer'],
				'id_state'          => $val['id_state'],
				'id_product'  		=> $val['id_product'],
				'design_id'  		=> $val['design_no'],
				'id_sub_design'  	=> $val['id_sub_design'],
				'product_name'  	=> $val['product_name'],
				'design_name'  		=> $val['design_name'],
				'sub_design_name'  	=> $val['sub_design_name'],
				'id_purity'         => $val['id_purity'],
				'id_size'           => $val['size'],
				'gross_wt'  	    => $val['gross_wt'],
				'less_wt'  	        => $val['less_wt'],
				'net_wt'  	        => $val['net_wt'],
				'piece'             => $val['piece'],
				'id_customerorder'  => $val['id_customerorder'],
				'id_orderdetails'   => $val['id_orderdetails'],
				'id_branch'         => $val['id_branch'],
				'order_no'          => $val['order_no'],
				'orderno'           => $val['orderno'],
				'ortertype'         => $val['ortertype'],
				'amount'            => $val['rate'],
				'rate_per_gram'     => $val['rate_per_gram'],
				'mc'                => $val['mc'],
				'mc_type'           => $val['mc_type'],
				'wast_percent'      => $val['wast_percent'],
				'order_date'        => $val['order_date'],
				'description'       => $val['description'],
				'cus_due_date'      => $val['cus_due_date'],
				's_due_date'        => $val['s_due_date'],
				's_remainder_date'  => $val['s_remainder_date'],
				'charge_value'      => $val['charge_value'],
				'stn_amt'           => $val['stn_amt'],
				'metal_type'        => $val['id_metal'],
				'sales_mode'        => $val['sales_mode'],
				'balance_type'      => $val['balance_type'],
				'rate_type'         => $val['rate_type'],
				'days_difference'   => $val['days_difference'],
				'calculation_based_on'=> $val['calculation_based_on'],
				'orderstatus'       => $val['orderstatus'],
				'assign_to'         => $val['assign_to'],
				'charges_details'   => $this->getordercharges($val['id_orderdetails']),
				'stone_details'     => $this->getorderstones($val['id_orderdetails']),
				'purity'            => $this->getorderpurity($val['id_purity']),
				'products'          => $this->getorderProducts(),
				'image_details'   => $this->getordersImages($val['id_orderdetails']),
				'size'              => ($val['size']!=null ? $this->getordersize($val['size']):[]),
			);
		 }
		 return $returndata;
	}
function getordercharges($id_orderdetails)
{
	$sql=$this->db->query("SELECT oc.id_charge as charge_id,oc.amount as charge_value,c.name_charge
	                       from ret_order_other_charges oc
						   Left join ret_charges c on c.id_charge=oc.id_charge
						   where oc.order_item_id=".$id_orderdetails."");
		//print_r($this->db->last_query());exit;
		return $sql->result_array();
}
function getorderstones($id_orderdetails)
{
	$sql=$this->db->query("SELECT os.is_apply_in_lwt,os.stone_id,os.pieces,os.wt,
	os.price as amount,os.stone_cal_type,os.rate_per_gram,st.stone_type as stones_type,
	st.stone_name,uom.uom_short_code,os.uom_id
	FROM ret_order_item_stones os
	LEFT JOIN ret_stone st ON st.stone_id = os.stone_id
    LEFT JOIN ret_uom uom on uom.uom_id=os.uom_id
	where os.order_item_id=".$id_orderdetails."");
	// print_r($this->db->last_query());exit;
	return $sql->result_array();
}
function getorderProducts()
{
	$sql=$this->db->query("SELECT pro_id,product_name
	from ret_product_master");
	return $sql->result_array();
}
function getorderpurity($id_purity)
{
	$sql=$this->db->query("SELECT id_purity,purity
	from ret_purity where id_purity=".$id_purity."");
	//print_r($this->db->last_query());exit;
	return $sql->result_array();
}
function getordersize($id_size)
{
	$sql=$this->db->query("SELECT id_size,concat(value,'-',name) as size
	from ret_size where ".($id_size!='' ? " id_size=".$id_size."" :'')."");
	//print_r($this->db->last_query());exit;
	return $sql->result_array();
}
function getordersImages($id)
{
	$sql = "SELECT * FROM customer_order_image where id_orderdetails=$id";
	$order_images=array();
	$status = $this->db->query($sql);
	$order_images_array = $status->result_array();
		foreach($order_images_array as $img){
			$path = 'assets/img/orders/'.$img['image'];
			$type = pathinfo($path, PATHINFO_EXTENSION);
			$data = file_get_contents($path);
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
			$img['img_src']=$base64;
			$order_images[]=$img;
		}
  return $order_images;
}
function get_tagorder_details($id_orderdetails)
{
	$sql = $this->db->query("SELECT tag_id,id_orderdetails
	FROM ret_taging
	WHERE id_orderdetails=$id_orderdetails");
	//print_r($this->db->last_query());exit;
	return $sql->result_array();
}
function get_repair_orderdetails($id)
{
	 $sql=$this->db->query("SELECT o.id_customerorder,od.id_orderdetails,od.orderno,od.tag_id,ifnull(od.tag_name,'') as tag_name,
		 od.ortertype,od.id_product,od.design_no,od.id_sub_design,od.weight as gross_wt,od.less_wt,od.net_wt,
		od.size,od.totalitems as piece,od.image,od.rate,od.rate_per_gram,
		 od.deliverydate,DATE_FORMAT(od.order_date,'%d-%m-%Y') as order_date,ifnull(od.description,'') as description,od.id_employee,
		 od.reject_reason,od.customer_ref_no,o.order_taken_by,o.order_from as id_branch,
		 j.id_vendor,pm.product_short_code,pm.product_name,pm.hsn_code,pm.cat_id,c.mobile,
		 concat(c.firstname,'-',c.mobile) as cus_name,c.id_customer,k.firstname as karigar_name,
		 DATE_FORMAT(od.cus_due_date,'%d-%m-%Y') as cus_due_date,e.firstname as emp_name,
		 b.name as branch_name,od.image,m.order_status,m.color,od.id_purity,p.purity,dm.design_name,sdm.sub_design_name,
		od.mc,od.wast_percent,od.id_mc_type as mc_type,DATE_FORMAT(od.smith_due_date,'%d-%m-%Y') as s_due_date,
		DATE_FORMAT(od.smith_remainder_date,'%d-%m-%Y') as s_remainder_date,od.charge_value,od.stn_amt,pm.cat_id,cat.id_metal,
		metal as metal_name,od.id_repair_master, DATE_FORMAT(od.cus_due_date,'%d-%m-%Y') AS days_difference,
		pm.sales_mode,pm.calculation_based_on,ad.id_state,o.order_no,o.balance_type,o.rate_type,o.order_type,
		tag.tag_code,tag.old_tag_id,b.name as branch_name
		 from customerorderdetails od
		LEFT join ret_taging tag on tag.tag_id = od.tag_id
		 LEFT join customerorder o on o.id_customerorder=od.id_customerorder
		 LEFT join  joborder j on j.id_order=od.id_orderdetails
		 LEFT join ret_product_master pm on pm.pro_id=od.id_product
		LEFT join ret_design_master dm on dm.design_no=od.design_no
		Left join ret_sub_design_master sdm on sdm.id_sub_design=od.id_sub_design
		Left join ret_category cat on cat.id_ret_category=pm.cat_id
		Left join metal mt on mt.id_metal=cat.id_metal
		 LEFT join ret_purity p on p.id_purity=od.id_purity
		 LEFT join customer c on c.id_customer=o.order_to
		LEFT join address ad on ad.id_customer=c.id_customer
		 LEFT join ret_karigar k on k.id_karigar=j.id_vendor
		 LEFT join employee e on e.id_employee=o.order_taken_by
		 LEFT join branch b on b.id_branch=o.order_from
		 LEFT JOIN ret_repair_master r ON r.id_repair_master = od.id_repair_master
		 LEFT join order_status_message m on m.id_order_msg=od.orderstatus
		 where od.id_customerorder=".$id." and od.orderstatus!=6");
	// print_r($this->db->last_query());exit;
	 $data= $sql->result_array();
	 foreach($data as $val)
	 {
		$returndata[]=array(
			'cus_name'  	    => $val['cus_name'],
			'id_ret_category'   => $val['cat_id'],
			'order_type'        => $val['order_type'],
			'label'             => $val['tag_name'],
			'tag_id'            => $val['tag_id'],
			'tag_code'          => $val['tag_code'],
			'old_tag_id'        => $val['old_tag_id'],
			'id_customer'       => $val['id_customer'],
			'id_state'          => $val['id_state'],
			'id_product'  		=> $val['id_product'],
			'design_id'  		=> $val['design_no'],
			'id_sub_design'  	=> $val['id_sub_design'],
			'product_name'  	=> $val['product_name'],
			'design_name'  		=> $val['design_name'],
			'sub_design_name'  	=> $val['sub_design_name'],
			'days_difference'   => $val['days_difference'],
			'metal_name'     	=> $val['metal_name'],
			'id_purity'         => $val['id_purity'],
			'id_size'           => $val['size'],
			'gross_wt'  	    => $val['gross_wt'],
			'less_wt'  	        => $val['less_wt'],
			'net_wt'  	        => $val['net_wt'],
			'piece'             => $val['piece'],
			'id_repair_master'  => $val['id_repair_master'],
			'id_customerorder'  => $val['id_customerorder'],
			'id_orderdetails'   => $val['id_orderdetails'],
			'id_branch'         => $val['id_branch'],
			'order_no'          => $val['order_no'],
			'orderno'           => $val['orderno'],
			'ortertype'         => $val['ortertype'],
			'amount'            => $val['rate'],
			'rate_per_gram'     => $val['rate_per_gram'],
			'mc'                => $val['mc'],
			'mc_type'           => $val['mc_type'],
			'wast_percent'      => $val['wast_percent'],
			'order_date'        => $val['order_date'],
			'description'       => $val['description'],
			'cus_due_date'      => $val['cus_due_date'],
			's_due_date'        => $val['s_due_date'],
			's_remainder_date'  => $val['s_remainder_date'],
			'charge_value'      => $val['charge_value'],
			'stn_amt'           => $val['stn_amt'],
			'metal_type'        => $val['id_metal'],
			'sales_mode'        => $val['sales_mode'],
			'balance_type'      => $val['balance_type'],
			'rate_type'         => $val['rate_type'],
			'calculation_based_on'=> $val['calculation_based_on'],
			'order_taken_by'=> $val['order_taken_by'],
			'stone_details'     => $this->getorderstones($val['id_orderdetails']),
			'damage_repair'        => $this->getorderdamage(),
			'products'             => $this->getorderProducts(),
			'design'              => $this->getorderdesigns(),
			'sub_design'          => $this->getordersubdesigns(),
			'metal'              => $this->getordermetal(),
			'image_details'      => $this->getordersImages($val['id_orderdetails']),
		);
		if ($val['tag_code'] != "" || $val['old_tag_id'] != "") {
			$tagDetails = $this->get_tag_scan_details($val['tag_code'], $val['old_tag_id']);
		} else {
			$tagDetails = [];
		}
		$returndata[count($returndata) - 1]['tag_details'] = $tagDetails;
	 }
	 return $returndata;
}
function get_tag_scan_details($tag_code,$old_tag_code)

{

	$return_data=array();



	$data = $this->db->query("SELECT tag.tag_id as value, tag_code as label, tag.tag_type, tag_lot_id, design_id, cost_center,

						tag.purity, tag.size, uom, tag.piece, tag.less_wt,IFNULL(tag.net_wt,0) as net_wt,IFNULL(tag.gross_wt,0) as gross_wt, tag.calculation_based_on,

						retail_max_wastage_percent,tag_mc_value,tag_mc_type, halmarking, sales_value, tag.tag_status,

						 product_name, product_short_code, c.id_ret_category as catid, c.name as catname,

						tag.product_id as lot_product, pur.purity as purname,lot_inw.lot_received_at,

						tag.tag_id,pro.sales_mode,tag.item_rate,tag.current_branch,

						des.design_name,tag.tag_mark, tag.id_sub_design as subdesignid, sdes.sub_design_name as sub_design_name,

						tag.old_tag_id,IFNULL(tag.id_section,'') as id_section,

						si.rate_per_gram,r.rate_field as rate_field,c.id_metal,IFNULL(stn.stone_price,0) as stone_price,IFNULL(other_met.tag_other_itm_amount,0) as othermetal_amount,

						tag_img.image as tag_img,tag.remarks,IFNULL(sec.section_name,'') as section_name

						FROM ret_taging as tag

						Left join ret_lot_inwards_detail lot_det ON tag.id_lot_inward_detail = lot_det.id_lot_inward_detail

						LEFT JOIN ret_lot_inwards as lot_inw ON lot_inw.lot_no = lot_det.lot_no

						LEFT JOIN ret_product_master as pro ON pro.pro_id = tag.product_id

						LEFT JOIN ret_design_master des on des.design_no=tag.design_id

						left join ret_sub_design_master sdes on sdes.id_sub_design=tag.id_sub_design

						LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity

						left join ret_category c on c.id_ret_category=pro.cat_id

						left join metal mt on mt.id_metal=c.id_metal

						LEFT JOIN ret_section sec ON sec.id_section = tag.id_section


						LEFT JOIN ret_taging_images tag_img on tag_img.tag_id=tag.tag_id and tag_img.is_default = 1


						LEFT JOIN (SELECT IFNULL(SUM(st.amount),0) as stone_price,st.tag_id

							FROM ret_taging_stone st

							GROUP BY st.tag_id) as stn ON stn.tag_id = tag.tag_id

						LEFT JOIN (SELECT IFNULL(SUM(m.tag_other_itm_amount),0) as tag_other_itm_amount,m.tag_other_itm_tag_id

							From ret_tag_other_metals m

							Group by m.tag_other_itm_tag_id) as other_met ON other_met.tag_other_itm_tag_id = tag.tag_id

						LEFT JOIN ret_metal_purity_rate r on r.id_metal= mt.id_metal and r.id_purity= tag.purity

						LEFT JOIN ret_stock_issue_detail as si ON si.tag_id = tag.tag_id

						WHERE  tag.id_orderdetails is NOT NULL

						and ".($old_tag_code!='' ? " tag.old_tag_id='".$old_tag_code."'" : ($tag_code!='' ? "tag_code='".$tag_code."'" :'') )."

					
						");

	// print_r($this->db->last_query());exit;

	$tagging= $data->result_array();

	foreach($tagging as $tag)

	{

		$return_data[]=array(

							 'current_branch'               =>$tag['current_branch'],

							 'catid'                        => $tag['catid'],

							 'catname'                      => $tag['catname'],

							 'id_metal'                      => $tag['id_metal'],

							 'design_id'                    =>$tag['design_id'],

							 'design_name'                  =>$tag['design_name'],

							 'section_name'                  =>$tag['section_name'],

							 'sub_design_name'              =>$tag['sub_design_name'],

							 'subdesignid'                  => $tag['subdesignid'],

							 'gross_wt'                     =>$tag['gross_wt'],

							 'item_rate'                    =>$tag['item_rate'],

							 'label'                        =>$tag['label'],

							 'old_tag_id'                   =>$tag['old_tag_id'],

							 'less_wt'                      =>$tag['less_wt'],

							 'lot_product'                  =>$tag['lot_product'],

							 'lot_received_at'              =>$tag['lot_received_at'],

							 'net_wt'                       =>$tag['net_wt'],

							 'piece'                        =>$tag['piece'],

							 'product_name'                 =>$tag['product_name'],

							 'product_short_code'           =>$tag['product_short_code'],

							 'purity'                       =>$tag['purity'],

							 'purname'                      =>$tag['purname'],

							 'sales_value'                  =>$tag['sales_value'],

							 'size'                         =>$tag['size'],

							 'tag_id'                       =>$tag['tag_id'],

							 'tag_lot_id'                   =>$tag['tag_lot_id'],

							 'tag_mc_type'                  =>$tag['tag_mc_type'],

							 'tag_mc_value'                 =>$tag['tag_mc_value'],

							 'tag_status'                   =>$tag['tag_status'],

							 'value'                        =>$tag['value'],

							 'id_section'                   =>$tag['id_section'],

							 'wastage_percent'              =>$tag['retail_max_wastage_percent'],

							 'rate_per_gram'                =>$tag['rate_per_gram'],

							 'rate_field'                   =>$tag['rate_field'],

							 'stone_price'                  =>$tag['stone_price'],

							 'othermetal_amount'            =>$tag['othermetal_amount'],

							 'remarks'                      =>$tag['remarks'],

							 'tag_img'                      =>$tag['tag_img'],

							 'stone_details'                =>$this->get_stock_issue_StoneDetails($tag['tag_id']),

							);

	}

	return $return_data;

}





function get_stock_issue_StoneDetails($tag_id)

{

	$sql = $this->db->query("SELECT tag_stone_id,tag_id, st.stone_name, uom.uom_name, pieces, wt, rate_per_gram,
	amount,s.stone_id, amount as price, IFNULL(certification_cost,0) as certification_cost, is_apply_in_lwt,
	s.stone_cal_type
	FROM ret_taging_stone as s
	LEFT JOIN ret_stone as st ON st.stone_id = s.stone_id
	LEFT JOIN ret_uom as uom ON uom.uom_id = s.uom_id
	WHERE s.tag_id = '".$tag_id."'");

	return $sql->result_array();

}
function get_tag_order_details($tag_code,$old_tag_code)
{
	$return_data=array();
	$data = $this->db->query("SELECT tag.tag_id as value, tag_code as label, tag.tag_type, tag_lot_id, design_id, cost_center,
						tag.purity, tag.size, uom, tag.piece, tag.less_wt,IFNULL(tag.net_wt,0) as net_wt,IFNULL(tag.gross_wt,0) as gross_wt,tag.calculation_based_on,
						retail_max_wastage_percent,tag_mc_value,tag_mc_type, halmarking, sales_value, tag.tag_status,
						product_name, product_short_code, c.id_ret_category as catid, c.name as catname,
						tag.product_id as lot_product, pur.purity as purname,lot_inw.lot_received_at,
						tag.tag_id,pro.sales_mode,tag.item_rate,tag.current_branch,
						des.design_name,tag.tag_mark, tag.id_sub_design as subdesignid, sdes.sub_design_name as sub_design_name,
						IFNULL(old_tag_id,'-')as old_label
						FROM ret_taging as tag
						Left join ret_lot_inwards_detail lot_det ON tag.id_lot_inward_detail = lot_det.id_lot_inward_detail
						LEFT JOIN ret_lot_inwards as lot_inw ON lot_inw.lot_no = lot_det.lot_no
						LEFT JOIN ret_product_master as pro ON pro.pro_id = tag.product_id
						LEFT JOIN ret_design_master des on des.design_no=tag.design_id
						left join ret_sub_design_master sdes on sdes.id_sub_design=tag.id_sub_design
						LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity
						left join ret_category c on c.id_ret_category=pro.cat_id
						left join metal mt on mt.id_metal=c.id_metal
						LEFT JOIN ret_stock_issue_detail as si ON si.tag_id = tag.tag_id
						WHERE 1
						and ".($old_tag_code!='' ? " tag.old_tag_id='".$old_tag_code."'" : ($tag_code!='' ? "tag.tag_code='".$tag_code."'" :'') )."
						");
	// print_r($this->db->last_query());exit;
	$tagging= $data->result_array();
	foreach($tagging as $tag)
	{
		$return_data[]=array(
							 'current_branch'               =>$tag['current_branch'],
							 'catid'                        =>$tag['catid'],
							 'catname'                      => $tag['catname'],
							 'design_id'                    =>$tag['design_id'],
							 'design_name'                  =>$tag['design_name'],
							 'sub_design_name'              =>$tag['sub_design_name'],
							 'subdesignid'                  => $tag['subdesignid'],
							 'gross_wt'                     =>$tag['gross_wt'],
							 'item_rate'                    =>$tag['item_rate'],
							 'label'                        =>$tag['label'],
							 'old_label'                    =>$tag['old_label'],
							 'less_wt'                      =>$tag['less_wt'],
							 'lot_product'                  =>$tag['lot_product'],
							 'lot_received_at'              =>$tag['lot_received_at'],
							 'net_wt'                       =>$tag['net_wt'],
							 'piece'                        =>$tag['piece'],
							 'product_name'                 => $tag['product_name'],
							 'product_short_code'           =>$tag['product_short_code'],
							 'purity'                       =>$tag['purity'],
							 'purname'                      =>$tag['purname'],
							 'sales_value'                  =>$tag['sales_value'],
							 'size'                         =>$tag['size'],
							 'tag_id'                       =>$tag['tag_id'],
							 'tag_lot_id'                   =>$tag['tag_lot_id'],
							 'tag_mc_type'                  =>$tag['tag_mc_type'],
							 'tag_mc_value'                 =>$tag['tag_mc_value'],
							 'tag_status'                   =>$tag['tag_status'],
							 'value'                        =>$tag['value'],
							 'wastage_percent'              =>$tag['retail_max_wastage_percent']
							);
	}
	return $return_data;
}
function getorderdamage()
{
	$sql=$this->db->query("SELECT id_repair_master,name
	from ret_repair_master");
	return $sql->result_array();
}
function getordermetal()
{
	$sql=$this->db->query("SELECT id_metal,metal
	from metal");
	return $sql->result_array();
}
function getorderdesigns()
{
	$sql=$this->db->query("SELECT design_no,design_name
	from ret_design_master");
	return $sql->result_array();
}
function getordersubdesigns()
{
	$sql=$this->db->query("SELECT id_sub_design,sub_design_name
	from ret_sub_design_master");
	return $sql->result_array();
}
function get_order($order_id)
{
	$sql = $this->db->query("Select * from customerorder where id_customerorder=" . $order_id . "");
	return $sql->row()->order_from;
}
function getBrnachOtpRegMobile($id_branch)
{
	$sql = $this->db->query("Select otp_verif_mobileno from branch where id_branch=" . $id_branch . "");
	return $sql->row()->otp_verif_mobileno;
}


function get_estimation_tags($id_branch, $estId)
{

	$dCData = $this->getBranchDayClosingData($id_branch);

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

	if(est_itms.tag_id != null,stn_wgt,stn_wt) as stn_wgt,

	if(est_itms.tag_id != null,othermat_amount,other_mat_price) as othermat_amount,

	if(est_itms.tag_id != null,othermat_wt,other_mat_wgt) as othermat_wt,est_itms.is_non_tag,
	
	concat(cus.firstname,' ',cus.mobile) as cus_name,cus.id_customer,

	v.village_name,if(cus.is_vip=1,'Yes','No') as vip,

	(select count(sa.id_scheme_account) from scheme_account sa left join customer cust on cust.id_customer=sa.id_customer) as accounts,

	pro.min_wastage,pro.max_wastage,pro.stock_type,nt.no_of_piece as available_pieces,nt.gross_wt as available_gross_wt,
	
	IFNULL(est_itms.orderno,'') as order_no,


	ifnull(est_itms.id_orderdetails,'') as id_orderdetails,IFNULL(ord.id_customerorder,'') as id_customerorder,pro.gift_applicable,
	
	r.rate_field, 

	ifnull(rec.charge_value, 0) as charge_value,IFNULL(cus.pan,'') as pan_no,IFNULL(est_itms.id_collecion_maping_det,'') as id_collecion_maping_det,est.esti_for, est_itms.id_sub_design,sub.sub_design_name,est_itms.item_total_tax,


	ifnull(tag.tag_code,'') as tag_code,est_itms.est_rate_per_grm,IFNULL(est.discount,0) as est_discount,c.scheme_closure_benefit,
	
	IFNULL(tag.net_wt,0) as tag_net_wt,IFNULL(est_itms.esti_purchase_cost,0) AS esti_purchase_cost, IFNULL(tag.tag_lot_id, 0) AS tag_lot_id,IFNULL(cus.aadharid,'') as aadharid,
	
	IFNULL(est_itms.id_section,'') as id_section,est_itms.item_emp_id,tag.sell_rate as sales_value,pro.cat_id,a.id_state

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

	left join ret_sub_design_master sub  on sub.id_sub_design=est_itms.id_sub_design

	LEFT JOIN ret_purity as pur ON pur.id_purity = est_itms.purity

	LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id

	LEFT JOIN customer cus on cus.id_customer=est.cus_id

	LEFT JOIN address a on a.id_customer=cus.id_customer

	LEFT JOIN village v on v.id_village=cus.id_village

	


	LEFT JOIN ret_metal_purity_rate r on r.id_metal=c.id_metal and r.id_purity=est_itms.purity

	LEFT JOIN (SELECT est_item_id, SUM(IFNULL(amount,0)) AS charge_value FROM ret_estimation_other_charges GROUP BY est_item_id) AS rec ON rec.est_item_id = est_itms.est_item_id

	LEFT JOIN ret_nontag_item nt on nt.product=est_itms.product_id " . ($id_branch != '' ? " and nt.branch=" . $id_branch . "" : '') . " 

	WHERE " . (!empty($id_branch) ? "est.id_branch=" . $id_branch . "" : '') . " 

	" . ($estId != '' ? " and est.esti_no ='" . $estId . "' " : '') . " 

	AND est_itms.est_item_id IS NOT NULL and tag.id_orderdetails IS NULL and est_itms.purchase_status=0 AND (tag.tag_status =0 OR tag.tag_status IS NULL OR tag.is_partial=1)

	AND date(est.estimation_datetime)='" . $dCData['entry_date'] . "' 

	AND IF(ifnull(tag.tag_type,0)=0,IF(IFNULL(est_itms.tag_id,'')!='' ,tag.tag_type=0,est_itms.est_item_id IS NOT null),(tag.tag_type=1 AND tag.is_approval_stock_converted = 1))

	GROUP by  est_itms.est_item_id

	order by est_itms.esti_id DESC");

	//echo $this->db->_error_message(); exit;

	// print_r($this->db->last_query());exit;

	$returndata = $items_query->result_array();

	foreach ($returndata as $rkey => $rval) {

		$returndata[$rkey]['stone_details'] = $this->getTagStoneDetails($rval['tag_id']);

		$returndata[$rkey]['charges_details'] = $this->get_charges($rval['tag_id']);
	}

	return $returndata;
}

function getTagStoneDetails($tagid)

{

	$data = $this->db->query("SELECT ts.tag_id, ts.stone_id, ts.pieces, ts.wt, ts.uom_id,

	 if(ts.rate_per_gram=0,round((ts.amount/ts.wt),2),rate_per_gram) as rate_per_gram, amount, is_apply_in_lwt , st.stone_type , stone_cal_type  

	FROM ret_taging_stone ts 

	LEFT JOIN ret_stone as st ON st.stone_id = ts.stone_id 

	where tag_id = $tagid");

	return $data->result_array();
}


function get_charges($tag_id)

{

	$sql = $this->db->query("SELECT rtc.tag_charge_id, rtc.tag_id, rtc.charge_id, rtc.charge_value, c.code_charge, c.tag_display FROM ret_taging_charges AS rtc LEFT JOIN ret_charges AS c ON rtc.charge_id = c.id_charge  WHERE tag_id=" . $tag_id);

	return $sql->result_array();
}

function getBranchDayClosingData($id_branch)

{

	$sql = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing where id_branch=".$id_branch);  

	return $sql->row_array();

}
    function save_email_log($data)
    {
        return $this->insertData($data, 'ret_order_email_logs');
    }

    function get_email_log_by_token($token)
    {
        $token = $this->db->escape_str($token);
        $sql = $this->db->query("SELECT * FROM ret_order_email_logs WHERE token = '$token'");
        return $sql->row_array();
    }

    function update_email_log($id, $data)
    {
        return $this->updateData($data, 'id', $id, 'ret_order_email_logs');
    }

    function update_order_acceptance($id_customerorder, $due_date, $token_id)
    {
        $this->db->trans_begin();
        
        // Update customerorder status to 3 (Process)
        $this->updateData(array('order_status' => 3), 'id_customerorder', $id_customerorder, 'customerorder');
        
        // Update customerorderdetails status and smith_due_date
        $this->db->where('id_customerorder', $id_customerorder);
        $this->db->update('customerorderdetails', array('orderstatus' => 3, 'smith_due_date' => $due_date));
        
        // Update email log
        $this->updateData(array('status' => 3, 'due_date' => $due_date, 'accepted_at' => date("Y-m-d H:i:s")), 'id', $token_id, 'ret_order_email_logs');
        
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return TRUE;
        } else {
            $this->db->trans_rollback();
            return FALSE;
        }
    }

    function update_order_rejection($id_customerorder, $token_id, $reason = '')
    {
        $this->db->trans_begin();
        
        // Update customerorder status to 6 (Cancelled/Rejected)
        $this->updateData(array('order_status' => 6), 'id_customerorder', $id_customerorder, 'customerorder');
        
        // Update customerorderdetails status to 6
        $this->db->where('id_customerorder', $id_customerorder);
        $this->db->update('customerorderdetails', array('orderstatus' => 6));
        
		// Reset order_cart items linked to this PO back to pending (orderstatus=0) for reassignment
        $po_details = $this->db->query("SELECT id_orderdetails FROM customerorderdetails WHERE id_customerorder = ".$id_customerorder)->result_array();
        foreach($po_details as $det) {
            if(!empty($det['id_orderdetails'])) {
                $this->db->where('id_orderdetails', $det['id_orderdetails']);
                $this->db->update('order_cart', array('orderstatus' => 0, 'id_orderdetails' => NULL));
            }
        }
        
        // Update email log with rejection status (4 = rejected)
        $update_data = array(
            'status' => 4,
            'rejected_at' => date("Y-m-d H:i:s"),
            'rejection_reason' => $reason
        );
        $this->updateData($update_data, 'id', $token_id, 'ret_order_email_logs');
        
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return TRUE;
        } else {
            $this->db->trans_rollback();
            return FALSE;
        }
    }
}
?>