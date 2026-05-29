<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ret_estimation_model extends CI_Model
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
	// 	$insert_flag = $this->db->insert($table, $data);
	// 	return ($insert_flag == 1 ? $this->db->insert_id(): 0);
	// }
	public function insertBatchData($data,$table)
    {
    	$insert_flag = 0;
		$insert_flag = $this->db->insert_batch($table, $data);
		if ($this->db->affected_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	// public function updateData($data, $id_field, $id_value, $table)
    // {
	//     $edit_flag = 0;
	//     $this->db->where($id_field, $id_value);
	// 	$edit_flag = $this->db->update($table,$data);
	// 	return ($edit_flag==1?$id_value:0);
	// }
	public function deleteData($id_field,$id_value,$table)
    {
        $this->db->where($id_field, $id_value);
        $status= $this->db->delete($table);
		return $status;
	}
	function get_ret_settings($name)
	{
		$sql = "SELECT
				`name`,
				`value`
				FROM ret_settings WHERE name = '".$name."'";
		$sql = $this->db->query($sql);
	    return $sql->row_array();
	}
	function get_employee_settings($emp_id)
	{
		$query = "SELECT es.allow_manual_rate, es.min_gold_tol, es.max_gold_tol, es.min_silver_tol, es.max_silver_tol, e.id_profile,
                 es.bulk_wast_disc_limit, es.dia_disc_limit
				 from employee_settings es
                 LEFT JOIN employee e ON e.id_employee = es.id_employee
				 where es.id_employee =".$emp_id;
		$sql = $this->db->query($query);
		return $sql->row_array();
	}
	function get_village()
    {
		$sql="Select *From village where status=1";
		return $data = $this->db->query($sql)->result_array();
	}
	function get_FinancialYear()
	{
		$sql=$this->db->query("SELECT fin_year_code From ret_financial_year where fin_status=1");
		return $sql->row_array();
	}
	function getEstTags($tag_id)
	{
	    $sql=$this->db->query("SELECT * FROM ret_taging WHERE tag_id=".$tag_id."");
	    return $sql->row_array();
	}
	function get_customer($id_customer)
    {
		$sql=$this->db->query("SELECT c.id_customer,c.cus_type,c.gst_number,c.firstname,c.mobile,c.email,a.id_country,a.id_state,a.id_city, a.address1,a.address2,a.address3,a.pincode,c.is_vip,
		IFNULL(c.pan,'') as pan_no,IFNULL(c.aadharid,'') as aadharid,IFNULL(c.gst_number,'') as gst_number,IFNULL(c.id_village,'') as id_village,c.title,c.id_profession,c.gender,IFNULL(date_format(c.date_of_birth,'%d-%m-%Y'),'') as date_of_birth,IFNULL(date_format(c.date_of_wed,'%d-%m-%Y'),'') as date_of_wed,IFNULL(c.driving_license_no,'') as dl_no,IFNULL(c.passport_no,'') as pp_no
		FROM customer c
		LEFT JOIN  address a on a.id_customer =c.id_customer
		WHERE c.id_customer=".$id_customer);
		return $sql ->row_array();
	}
	function getNonTagLots($SearchTxt,$id_branch)
	{
	    $data = $this->db->query("SELECT
                            	    l.lot_no as value,l.lot_no as label
                            	  FROM ret_lot_inwards l
                            	    LEFT JOIN ret_lot_inwards_detail lt_det on lt_det.lot_no = l.lot_no
                            	    LEFT JOIN ret_branch_transfer bt on bt.id_lot_inward_detail = lt_det.id_lot_inward_detail
                            	  WHERE transfer_item_type = 2 AND transfer_to_branch=".$id_branch." AND stock_type=2 AND l.lot_no LIKE ?",
	                            array('%'.$SearchTxt.'%'));
	                           // echo $this->db->last_query();
	     return $data->result_array();
	}
	function ajax_getEstimationList($id_branch,$from_date,$to_date)
    {
        $return_data=array();
        $uid=$this->session->userdata('uid');
        if($id_branch!='' && $id_branch>0)
        {
            $data=$this->getBranchDayClosingData($id_branch);
        }
		$sql = $this->db->query("SELECT est.estimation_id, esti_no,cus.mobile,
				date_format(estimation_datetime, '%d-%m-%Y %H:%i') as estimation_datetime,
				total_cost, if(esti_for = 1,'Customer','Branch Transfer') as esti_for,
                IFNULL(bill.bill_id,old_bill.bill_id) as bill_id,
                IFNULL(bill.bill_no,old_bill.bill_no) as bill_no,IFNULL(chit_util.chit_ut_id,'') as chit_util_id,IFNULL(sr_util.sr_ut_id,'') as sr_util_id,
				IFNULL(pro.product_name,'') as product_name,cus.firstname,e.firstname as emp_name,e.emp_code, IFNULL(rev.rating, '') AS rating, IFNULL(rev.review, '') AS review, IFNULL(rev.suggestion, '') AS suggestion, IF(est.added_through = 1, 'Admin', IF(est.added_through = 2, 'App', '')) AS added_through,
                IFNULL(s.is_topup_scheme,'') as is_topup_scheme
				FROM ret_estimation as est
				LEFT JOIN customer as cus ON cus.id_customer = est.cus_id
				LEFT JOIN employee e on e.id_employee=est.created_by

                LEFT JOIN  ret_customer_review as rev ON rev.esti_id = est.estimation_id
                LEFT JOIN ret_est_chit_utilization as chit_util ON chit_util.est_id = est.estimation_id
                LEFT JOIN scheme_account as sa ON sa.id_scheme_account = chit_util.scheme_account_id
                LEFT JOIN scheme as s ON s.id_scheme = sa.id_scheme
                LEFT JOIN ret_est_sales_return_utilization as sr_util ON sr_util.est_id = est.estimation_id 
				LEFT JOIN (SELECT e.estimation_id,b.bill_id,b.bill_no
                FROM ret_estimation e
                LEFT JOIN ret_billing b ON b.bill_id=e.estbillid
                WHERE b.bill_status=1
                GROUP by e.estimation_id ) as bill ON bill.estimation_id=est.estimation_id
                LEFT JOIN (SELECT GROUP_CONCAT(concat(m.metal),'-',p.product_name)  as product_name,e.esti_id
                FROM ret_estimation_items e
                LEFT JOIN ret_product_master p ON p.pro_id=e.product_id
                LEFT JOIN ret_category c ON c.id_ret_category=p.cat_id
                LEFT JOIN metal m ON m.id_metal=c.id_metal
                GROUP by e.esti_id) as pro ON pro.esti_id=est.estimation_id
                LEFT JOIN (SELECT s.est_id,b.bill_id,b.bill_no
                FROM ret_estimation_old_metal_sale_details s
                LEFT JOIN ret_bill_old_metal_sale_details old ON old.esti_old_metal_sale_id=s.old_metal_sale_id
                LEFT JOIN ret_billing b ON b.bill_id=old.bill_id
                WHERE b.bill_status=1
                GROUP by b.bill_id LIMIT 1) as old_bill ON old_bill.est_id=est.estimation_id
				where ".($from_date=='' || $to_date=='' ? (($id_branch==0 || $id_branch=='') ? " date(estimation_datetime) = '".date("Y-m-d")."' " : " date(estimation_datetime) = '".$data['entry_date']."' "):'')."
				 ".($from_date != '' && $to_date != '' ? 'date(estimation_datetime) BETWEEN "'.$from_date.'" AND "'.$to_date.'"' : '')."
				".($id_branch!=0 && $id_branch!='' ? " and est.id_branch=".$id_branch."" :'')."
                 GROUP BY est.estimation_id
				ORDER BY est.estimation_id desc ");
			//print_r($this->db->last_query());exit;
		$result= $sql->result_array();
		foreach($result as $item)
		{
            $old_metal=$this->get_oldmetalDetails($item['estimation_id']);
            if($old_metal>0)
            {
                $item['item_type']='Sales and Purchase';
            }
            else
            {
                $item['item_type']='Sales';
            }
            if($item['bill_id'] != ''){

            $item['bill_no'] = $this->get_bill_no_format_detail($item['bill_id']);
            }
		     $return_data[]=$item;
		}
		return $return_data;
	}
        //Bill number format settings
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

					WHEN b.bill_type = 9 then ifNULL('OD','')

					WHEN b.bill_type = 3 then ifNULL('SA','')

					WHEN b.bill_type = 4 then ifNULL('PU','')

					WHEN b.bill_type = 5 then ifNULL('OD','')

					WHEN b.bill_type = 7 then ifNULL('SR','')

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

					WHEN bill_type = 8 then ifNULL('CC','')

					WHEN bill_type = 15 then NULL

					WHEN bill_type = 10 then NULL

				ELSE NULL

				END as '@@short_code@@'

					from bill_no_format

					" . ($query1['bill_type'] != ''  ? 'where bill_type=' . $query1['bill_type'] : '') . "");

		$query2 = $format2->row()->bill_no_format;

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
	function get_oldmetalDetails($estim_id)
    {
        $sql=$this->db->query("SELECT est_id from ret_estimation_old_metal_sale_details where est_id=".$estim_id);
        return $sql->num_rows();
    }
	function getBranchDayClosingData($id_branch)
    {
	    $sql = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing where id_branch=".$id_branch);
	    return $sql->row_array();
	}
	function get_charges($tag_id)
	{
		$sql = $this->db->query("SELECT rtc.tag_charge_id, rtc.tag_id, rtc.charge_id, rtc.charge_value, c.code_charge, c.tag_display FROM ret_taging_charges AS rtc LEFT JOIN ret_charges AS c ON rtc.charge_id = c.id_charge  WHERE tag_id=".$tag_id);
	    return $sql->result_array();
	}
	function get_entry_records($est_id)
	{
		$sql = $this->db->query("SELECT estimation_id,esti_no,
				concat(cus.firstname, '-',cus.mobile) as cus_name,  esti_for,
				date_format(estimation_datetime, '%d-%m-%Y %H:%i:%s') as estimation_datetime,
				date_format(estimation_datetime, '%d-%m-%Y') as est_date,est.goldrate_18ct as goldrate_18ct,
				cus_id, created_by, date_format(created_time, '%d-%m-%Y %H:%i:%s') as created_time,
				has_converted_order, discount, gift_voucher_amt, total_cost, est.id_branch,cus.mobile,IFNULL(v.village_name,'') as village_name,
				cus.firstname as customer_name,e.emp_code,b.short_name ,e.id_employee,e.firstname as emp_name,IFNULL(est.goldrate_22ct,0) as goldrate_22ct,IFNULL(est.goldrate_14ct,0) as goldrate_14ct,IFNULL(est.goldrate_9ct,0) as goldrate_9ct,
				IFNULL(est.silverrate_1gm,0) as silverrate_1gm,a.address1,a.address2,a.address3,c.name as city_name,est.is_eda,IFNULL(est.estbillid,'') as estbillid,ifnull(a.pincode,'') as pincode,a.id_country,a.id_state,a.id_city,IFNULL(est_item.isTagsplitted,0) as isTagsplitted,
                est.disc_per AS disc_per,est.bulk_was_disc_per AS bulk_was_disc_per,est.manual_rate
				FROM ret_estimation as est
                LEFT JOIN ( select esti_id, count(isTagsplitted) as isTagsplitted from ret_estimation_items  GROUP by esti_id  ) as est_item ON est_item.esti_id = est.estimation_id
				LEFT JOIN customer as cus ON cus.id_customer = est.cus_id
				LEFT JOIN village v on v.id_village=cus.id_village
				Left join address a on a.id_customer=cus.id_customer
				LEFT JOIN employee e on e.id_employee=est.created_by
				LEFT JOIN branch b on b.id_branch=est.id_branch
				LEFT JOIN city c on c.id_city=a.id_city
				WHERE est.estimation_id ='".$est_id."'");
				// print_r($this->db->last_query());exit;

            $result =$sql->result_array()[0];
            $result['financial_year'] = $this->GetFinancialYear();
            // echo"<pre>";print_r($result);exit;
            return $result;
	}
	function getOtherEstimateItemsDetails($est_id)
	{
		$return_data = array("item_details" => array(), "old_matel_details" => array(), "stone_details" => array(), "other_material_details" => array(), "voucher_details" => array(), "chit_details" => array(),"sales_return_details"=>array(),"advance_details"=>array());
		$items_query = $this->db->query("SELECT est_item_id, esti_id, item_type,
					   est.product_id, est.tag_id, est.design_id, est.purity as purid, pur.purity as purity, est.size, est.uom, est.piece,est.pure_wt,
                       IF(est.istag_merged = 0, IFNULL(est.less_wt,0), (IFNULL(mrg_tag.mrg_less_wt,0) + IFNULL(est.less_wt,0))) as less_wt,
                       IF(est.istag_merged=0 , est.net_wt , (mrg_tag.mrg_nwt+ est.net_wt)) as net_wt,IF(est.istag_merged=0,est.gross_wt,(mrg_tag.mrg_gwt + est.gross_wt)) as gross_wt, est.pure_rate,
					   est.calculation_based_on, est.wastage_percent, est.mc_value,
					   IF(istag_merged=0,item_cost, (mrg_tag.mrg_item_cost + item_cost)) as item_cost,
                       IF(est.istag_merged = 0, IFNULL(est.item_total_tax,0), (IFNULL(mrg_tag.mrg_total_tax,0) + IFNULL(est.item_total_tax,0))) as item_total_tax, IF(est.istag_merged = 0, IFNULL(est.market_rate_cost,0), (IFNULL(mrg_tag.mrg_market_rate_cost,0) + IFNULL(est.market_rate_cost,0))) as market_rate_cost,
                       product_short_code,product_name as product_name,
                       concat(design_name,'-',design_code) as design_name, design_name as design, design_code, pur.purity as purname, pur.description as pur_description,
                       tax.tax_percentage,tax.tgi_calculation,est.discount,e.created_by,s.disc_limit,s.disc_limit_type,e.id_branch,est.mc_type,est.is_non_tag,est.is_partial,est.lot_no,pro.sales_mode,tag.item_rate,
                       tag.net_wt as tag_net_wt,txgrp.tgrp_name,tag.tag_code,IFNULL(est.id_orderdetails,'') as id_orderdetails, rtc.charge_value,
                       sub.sub_design_name,mt.metal as metal_name,IFNULL(est.est_rate_per_grm,0) as est_rate_per_grm,c.id_metal as metal_type,c.is_916,c.name as cat_name,IFNULL(est.id_section,'') as id_section,sec.section_short_code,sect.section_short_code as est_sec_code,ifnull(u.uom_short_code,'') as pro_uom,
                       est.isTagsplitted, IFNULL(est.max_VA_per,0) AS max_VA_per, IFNULL(est.max_mc,0) AS max_mc,est.is_split_row, pro.cat_id,rss.wastag_method,rss.type,wc.wc_method
					   FROM ret_estimation_items as est
					   LEFT join ret_estimation e on e.estimation_id=est.esti_id
                       LEFT JOIN ret_product_master as pro ON pro.pro_id = est.product_id
                        left join ret_uom u on u.uom_id = pro.uom_id
                       LEFT JOIN (SELECT m.est_item_id as ests_item_id,SUM(e.gross_wt) as mrg_gwt,SUM(e.item_cost) as mrg_item_cost,SUM(e.net_wt) as mrg_nwt,SUM(e.mc_value) as mrg_mc_value,tag.tag_code as mrg_tag_code, SUM(IFNULL(e.less_wt,0)) as mrg_less_wt, SUM(IFNULL(e.market_rate_cost,0)) as mrg_market_rate_cost, SUM(IFNULL(e.item_total_tax,0)) as mrg_total_tax
                                FROM ret_est_tag_merge m
                                LEFT JOIN ret_estimation_items e ON e.est_item_id = m.ref_est_item_id
                                LEFT JOIN ret_taging tag ON tag.tag_id = e.tag_id
                                GROUP BY m.est_item_id) as mrg_tag ON mrg_tag.ests_item_id = est.est_item_id
                       LEFT JOIN ret_design_master as des ON des.design_no = est.design_id
                       LEFT JOIN ret_sub_design_master as sub ON sub.id_sub_design = est.id_sub_design
                       LEFT JOIN ret_purity as pur ON pur.id_purity = est.purity
                       LEFT JOIN ret_category c on c.id_ret_category=pro.cat_id
					   LEFT JOIN metal mt on mt.id_metal=c.id_metal
					   LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id
					   left JOIN employee_settings s on s.id_employee=e.created_by
					   LEFT JOIN ret_taging as tag ON tag.tag_id = est.tag_id
					   LEFT JOIN (SELECT tag_id, SUM(IFNULL(charge_value,0)) AS charge_value FROM ret_taging_charges GROUP BY tag_id) AS rtc ON rtc.tag_id = tag.tag_id
                       LEFT JOIN ret_section sec ON sec.id_section = tag.id_section
                       LEFT JOIN ret_section sect ON sect.id_section = est.id_section
                       LEFT JOIN ret_selling_settings rss ON rss.id_product = est.product_id AND rss.id_design = est.design_id
                       LEFT JOIN ret_design_weight_range_wc wc ON wc.id_selling_settings = rss.id_selling_settings 
                        AND est.gross_wt >= wc.wc_from_weight 
                        AND est.gross_wt <= wc.wc_to_weight
						LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,
						m.tax_percentage as tax_percentage,
						i.tgi_calculation as tgi_calculation
						FROM ret_taxgroupitems i
						LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=pro.tgrp_id
					    WHERE est.esti_id ='".$est_id."' and istag_merged in (0,1)
					    GROUP by est.est_item_id");
		//print_r($this->db->last_query());exit;
		$item_details = $items_query->result_array();
		foreach ($item_details as $items) {
			$return_data["item_details"][]=array(
							'item_rate'			=>$items['item_rate'],
							'sales_mode'		=>$items['sales_mode'],
							'est_item_id'		=>$items['est_item_id'],
							'item_type'			=>$items['item_type'],
							'esti_id'			=>$items['esti_id'],
							'product_id'		=>$items['product_id'],
							'pro_uom'		    =>$items['pro_uom'],
							'tag_id'			=>$items['tag_id'],
							'tag_code'			=>$items['tag_code'],
							'design_id'			=>$items['design_id'],
							'purid'				=>$items['purid'],
							'purity'			=>$items['purity'],
							'size'				=>$items['size'],
							'uom'				=>$items['uom'],
							'piece'				=>$items['piece'],
							'less_wt'			=>$items['less_wt'],
							'net_wt'			=>$items['net_wt'],
							'gross_wt'			=>$items['gross_wt'],
                            'pure_wt'           =>$items['pure_wt'],
                            'pure_rate'           =>$items['pure_rate'],
							'calculation_based_on'=>$items['calculation_based_on'],
							'wastage_percent'	=>$items['wastage_percent'],
							'mc_value'		=>$items['mc_value'],
							'item_cost'			=>$items['item_cost'],
							'product_short_code'=>$items['product_short_code'],
							'product_name'		=>$items['product_name'],
							'design_code'		=>$items['design_code'],
							'design_name'		=>$items['design_name'],
							'metal_type'        =>$items['metal_type'],
							'sub_design_name'   =>$items['sub_design_name'],
							'design'			=>$items['design'],
							'purname'			=>$items['purname'],
							'tax_percentage'	=>$items['tax_percentage'],
							'tgi_calculation'	=>$items['tgi_calculation'],
							'discount'			=>$items['discount'],
							'disc_limit'		=>$items['disc_limit'],
							'disc_limit_type'	=>$items['disc_limit_type'],
							'id_branch'			=>$items['id_branch'],
							'mc_type'			=>$items['mc_type'],
							'is_non_tag'		=>$items['is_non_tag'],
							'is_partial'		=>$items['is_partial'],
							'lot_no'		    =>$items['lot_no'],
							'item_total_tax'    =>$items['item_total_tax'],
							'tag_net_wt'    	=>$items['tag_net_wt'],
							'market_rate_cost'  =>$items['market_rate_cost'],
							'tgrp_name'  		=>$items['tgrp_name'],
							'metal_name'  		=>$items['metal_name'],
							'id_orderdetails'  	=>$items['id_orderdetails'],
							'est_rate_per_grm'  =>$items['est_rate_per_grm'],
						     'stone_details'		=>  $this->get_stone_details($items['est_item_id']),
						     'est_stone_wt'		=>  $this->get_est_stone_wt($items['est_item_id']),
						     'tag_stone_wt'		=> $items['tag_id']!='' ? $this->get_tag_stone_wt($items['tag_id']):0,
						    'other_metal_details'=>$this->get_est_other_metal_details($items['est_item_id']),
						    'material_details'	=>$this->get_other_material_details($items['est_item_id']),
							'charge_value'		=> $items['charge_value'],
							'charges'			=> $this->get_other_estcharges($items['est_item_id']),
							'is_916'			=>$items['is_916'],
							'cat_name'		    =>$items['cat_name'],
                            'cat_id'		    =>$items['cat_id'],
                            'section_short_code'=>$items['section_short_code'],
                            'est_sec_code'		=>$items['est_sec_code'],
                            'isTagsplitted'		=>$items['isTagsplitted'],
                            'max_VA_per'		=>$items['max_VA_per'],
                            'max_mc'		    =>$items['max_mc'],
                            'is_split_row'		=>$items['is_split_row'],
                            'pur_description'	=>$items['pur_description'],
                            'wastag_method'		=>$items['wastag_method'],
                            'type'				=>$items['type'],
                            'wc_method'			=>$items['wc_method'],
					   );
		}
		$old_matel_query = $this->db->query("SELECT IFNULL(pro.product_name,'-') as old_metal_prod_name,est_old.old_metal_prod_id,
                            est_old.remark,est_old.old_metal_sale_id,est_id,
						   id_category, type, item_type, gross_wt,est_old.net_wt,
						   round((gross_wt - stone_wt),3) as ls_wt,
                           if(type = 1, 'Melting', 'Retag') as reusetype,
                           if(item_type = 1, 'Ornament', if(item_type = 2, 'Coin', if(item_type = 3, 'Bar',''))) as receiveditem,
						   IFNULL(stone_wt,0) as stone_wt, IFNULL(dust_wt,0) as dust_wt , est_old.purity as purid, wastage_percent,
						   wastage_wt, rate_per_gram, amount, ifnull(est_old.piece,0) as piece,
						   pur.purity as purname, met.metal ,est_old.purpose,est_old.id_old_metal_type, est_old.id_old_metal_category, oldM.metal_type AS old_metal_type
						   FROM ret_estimation_old_metal_sale_details as est_old
						   LEFT JOIN ret_purity as pur ON pur.id_purity = est_old.purity
                           LEFT JOIN ret_product_master as pro ON pro.pro_id = est_old.old_metal_prod_id
						   LEFT JOIN  ret_old_metal_type AS oldM ON oldM.id_metal_type = est_old.id_old_metal_type
						   LEFT JOIN metal as met ON met.id_metal = est_old.id_category
						   WHERE est_old.est_id = '".$est_id."'");
		$old_matel_detail= $old_matel_query->result_array();
		foreach ($old_matel_detail as $data) {
			$return_data["old_matel_details"][]=array(
								'old_metal_sale_id'=>$data['old_metal_sale_id'],
								'id_old_metal_type'=>$data['id_old_metal_type'],
								'id_old_metal_category'=>$data['id_old_metal_category'],
								'old_metal_type'=>$data['old_metal_type'],
								'est_id'=>$data['est_id'],
								'id_category'=>$data['id_category'],
								'type'=>$data['type'],
								'item_type'=>$data['item_type'],
								'gross_wt'=>$data['gross_wt'],
								'piece'=>$data['piece'],
								'ls_wt'=>$data['ls_wt'],
								'net_wt'=>$data['net_wt'],
								'stone_wt'=>$data['stone_wt'],
								'reusetype'=>$data['reusetype'],
								'receiveditem'=>$data['receiveditem'],
								'dust_wt'=>$data['dust_wt'],
								'purid'=>$data['purid'],
								'wastage_percent'=>$data['wastage_percent'],
								'wastage_wt'=>$data['wastage_wt'],
								'rate_per_gram'=>$data['rate_per_gram'],
								'amount'=>$data['amount'],
								'purname'=>$data['purname'],
								'metal'=>$data['metal'],
								'purpose'=>$data['purpose'],
                                'remark'=>$data['remark'],
								'old_metal_prod_id'=>$data['old_metal_prod_id'],
								'old_metal_prod_name'=>$data['old_metal_prod_name'],
								'goldrate_24ct'=>($this->getOldMetalRate($data['id_old_metal_type'])),
								'stone_details'		 =>$this->get_old_metal_stone_details($data['old_metal_sale_id']),
								'old_metal_types'	=>	$this->get_old_metal_type($data['id_category']),
								'old_metal_category'	=>	$this->get_old_metal_category($data['id_old_metal_type'])
						);
		}
		$est_stone_query = $this->db->query("SELECT est_item_stone_id,
						   est_id, est_st.stone_id, pieces, wt, price,
                           stone_name, stone_code, uom_name, uom_short_code, est_st.is_apply_in_lwt,est_st.stone_cal_type
						   FROM ret_estimation_item_stones as est_st
                           LEFT JOIN ret_stone as st ON st.stone_id = est_st.stone_id
                           LEFT JOIN ret_uom as um ON um.uom_id = st.uom_id
						   WHERE est_st.est_id = '".$est_id."'");
		$return_data["stone_details"] = $est_stone_query->result_array();
		$est_material_query = $this->db->query("SELECT est_other_material_id,est_item_id,
							  est_id, est_mt.material_id, wt, price ,
							  material_name, material_code,
							  uom_name, uom_short_code
							  FROM ret_estimation_item_other_materials as est_mt
							  LEFT JOIN ret_material as mat ON mat.material_id = est_mt.material_id
							  LEFT JOIN ret_uom as um ON um.uom_id = mat.uom_id
							  WHERE est_mt.est_id = '".$est_id."'");
		$other_material = $est_material_query->result_array();
		foreach($other_material as $materials)
		{
			//echo "<pre>"; print_r($materials);exit;
 			$return_data['other_material_details'][]=array(
								'est_other_material_id'=>$materials['est_other_material_id'],
								'est_id'			   =>$materials['est_id'],
								'material_id'		   =>$materials['material_id'],
								'wt'				   =>$materials['wt'],
								'price'				   =>$materials['price'],
								'material_name'	       =>$materials['material_name'],
								'material_code'		   =>$materials['material_code'],
								'uom_name'		       =>$materials['uom_name'],
								'uom_short_code'	   =>$materials['uom_short_code'],
								'est_item_id'	       =>$materials['est_item_id'],
								'stone_details'		 =>$this->get_stone_details($materials['est_item_id']),
								'material_details'		 =>$this->get_other_material_details($materials['est_item_id'])
								);
		}
		$est_voucher_query = $this->db->query("SELECT gift_voucher_id, est_id,
							voucher_no, gift_voucher_details, gift_voucher_amt
						   FROM ret_est_gift_voucher_details as est_vouch
						   WHERE est_vouch.est_id  = '".$est_id."'");
		$return_data["voucher_details"] = $est_voucher_query->result_array();
        $est_chit_query = $this->db->query("SELECT chit_ut_id, est_id, est_chit.scheme_account_id, utl_amount,concat(s.code,'',sa.scheme_acc_number) as scheme_acc_number,s.scheme_type,sa.closing_balance,
        IFNULL(est_chit.closing_weight,0) as closing_weight,IFNULL(est_chit.wastage_per,0) as wastage_per,IFNULL(est_chit.savings_in_wastage,0) as savings_in_wastage,
        IFNULL(est_chit.mc_value,0) as mc_value,IFNULL(est_chit.savings_in_making_charge,0) as savings_in_making_charge,sa.additional_benefits,sa.closing_add_chgs,est_chit.rate_per_gram,
        s.scheme_type as new_scheme_type, IFNULL(s.flexible_sch_type, 0) as flexible_sch_type, s.wgt_convert as wgt_convert, IFNULL(sa.closing_benefits,0) as closing_benefits
        FROM ret_est_chit_utilization as est_chit
        LEFT JOIN scheme_account sa ON sa.id_scheme_account=est_chit.scheme_account_id
        LEFT JOIN scheme s ON s.id_scheme=sa.id_scheme
        WHERE est_chit.est_id = '".$est_id."'");
		$return_data["chit_details"] = $est_chit_query->result_array();

        $est_sales_return = $this->db->query("SELECT sr.sr_ut_id, SUM(bd.item_cost) as sr_utilized_amt
        FROM ret_est_sales_return_utilization sr
        LEFT JOIN ret_bill_details bd ON bd.bill_det_id=sr.bill_det_id
        LEFT JOIN ret_billing bill ON bill.bill_id=bd.bill_id
        WHERE sr.est_id = '".$est_id."'");
        // print_r($this->db->last_query());exit;
        $return_data["sales_return_details"] = $est_sales_return->result_array();

	/*	$advance_details=$this->db->query("SELECT a.order_no,a.advance_amount as paid_advance,(a.advance_weight*a.rate_per_gram) as paid_weight,s.metal_type,a.store_as,a.advance_type,a.rate_calc,a.rate_per_gram,a.bill_id,a.bill_adv_id
            FROM ret_billing_advance a
            LEFT JOIN ret_billing b ON b.bill_id=a.bill_id
            LEFT JOIN ret_estimation_items e ON e.order_no=a.order_no
            LEFT JOIN customerorderdetails d on d.id_orderdetails=e.id_orderdetails
            LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id=a.old_metal_sale_id
            LEFT JOIN ret_estimation est ON est.estimation_id=e.esti_id
            where est.estimation_id ='".$est_id."' and b.bill_status=1
            AND b.id_branch=est.id_branch
            GROUP BY a.bill_adv_id");*/
        $advance_details=$this->db->query("SELECT a.order_no,a.advance_amount as paid_advance,(a.advance_weight*a.rate_per_gram) as paid_weight,s.metal_type,a.store_as,a.advance_type,a.rate_calc,a.rate_per_gram,a.bill_id,a.bill_adv_id,
        date_format(b.bill_date,'%d-%m-%Y') as bill_date
            FROM ret_estimation_items e
            LEFT JOIN customerorderdetails d ON d.id_orderdetails=e.id_orderdetails
            LEFT JOIN customerorder c ON c.id_customerorder=d.id_customerorder
            LEFT JOIN ret_billing_advance a ON a.id_customerorder=c.id_customerorder
            LEFT JOIN ret_billing b on b.bill_id=a.bill_id
            LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id=a.old_metal_sale_id
            LEFT JOIN ret_estimation est ON est.estimation_id=e.esti_id
            where est.estimation_id ='".$est_id."' and b.bill_status=1
            AND b.id_branch=est.id_branch
            GROUP BY a.bill_adv_id");
           // print_r($this->db->last_query());exit;
        $return_data["advance_details"] = $advance_details->result_array();
		return $return_data;
	}
	function getOldMetalRate($id_old_metal_type)
    {
        $sql=$this->db->query("SELECT r.rate as goldrate_24ct
        FROM ret_old_metal_type t
        LEFT JOIN ret_old_metal_rate r ON r.id_metal=t.id_metal
        WHERE t.id_metal_type=".$id_old_metal_type."
        ORDER by id_old_metal_rate DESC LIMIT 1");
        return $sql->row()->goldrate_24ct;
    }
	function get_old_metal_stone_details($est_old_metal_sale_id)
	{
			$est_stone_query=$this->db->query("SELECT est_old_metal_stone_id,est_old_metal_sale_id,
						   est_st.stone_id, pieces, wt, price,rate_per_gram,is_apply_in_lwt,stone_cal_type,
                           stone_name, stone_code, uom_name, uom_short_code, est_st.uom_id,stone_cal_type
						   FROM ret_esti_old_metal_stone_details as est_st
                           LEFT JOIN ret_stone as st ON st.stone_id = est_st.stone_id
                           LEFT JOIN ret_uom as um ON um.uom_id = est_st.uom_id
						   WHERE est_st.est_old_metal_sale_id = '".$est_old_metal_sale_id."'");
		//	print_r($this->db->last_query());exit;
			return $est_stone_query->result_array();
	}
	function get_stone_details($est_item_id)
	{
			$est_stone_query=$this->db->query("SELECT est_item_stone_id,est_st.est_item_id,st.stone_type, est_st.stone_id, est_st.pieces, est_st.wt, price, est_st.rate_per_gram,est_st.is_apply_in_lwt,est_st.stone_cal_type,'' as tag_wt, stone_name, stone_code, uom_name, uom_short_code,est_st.uom_id,
            est_st.is_apply_in_lwt as show_in_lwt,est_st.pieces as stone_pcs,est_st.rate_per_gram as stone_rate,est_st.price as amount,est_st.stone_cal_type as stone_cal_type,est_st.wt as stone_wt,est.tag_id,est_st.uom_id as stone_uom_id,
            est_st.max_stn_amt as max_stn_amt,IFNULL(est_st.quality_id,'') AS quality_id
            FROM ret_estimation_item_stones as est_st

            LEFT join ret_estimation_items est on est.est_item_id = est_st.est_item_id
            LEFT JOIN ret_stone as st ON st.stone_id = est_st.stone_id
            LEFT JOIN ret_uom as um ON um.uom_id = est_st.uom_id
            WHERE est_st.est_item_id= '".$est_item_id."'");
			return $est_stone_query->result_array();
	}
	function get_est_other_metal_details($est_item_id)
	{
            $sql =$this->db->query("SELECT m.tag_other_itm_grs_weight,m.tag_other_itm_wastage,m.tag_other_itm_mc,m.tag_other_itm_rate,m.tag_other_itm_pcs,m.tag_other_itm_amount,IFNULL(mt.metal,'-') as metal_name, m.tag_other_itm_metal_id, m.tag_other_itm_pur_id, IFNULL(pur.purity ,'-')as purname, m.tag_other_itm_cal_type,m.tag_other_itm_uom
            FROM ret_est_other_metals m
            LEFT JOIN ret_category c on c.id_ret_category=m.tag_other_itm_metal_id
            LEFT JOIN metal mt ON mt.id_metal = c.id_metal
            LEFT JOIN ret_purity AS pur ON pur.id_purity = m.tag_other_itm_pur_id
            WHERE m.est_item_id = '".$est_item_id."'");
			return $sql->result_array();
	}
	function get_tag_stone_details($tag_id)
	{
		$tag_stone_query=$this->db->query("SELECT tag_stone_id,tag_id, pieces, wt,amount,stone_id,amount as price,IFNULL(certification_cost,0) as certification_cost,
		s.is_apply_in_lwt,s.stone_cal_type,s.rate_per_gram
		FROM ret_taging_stone as s
		WHERE s.tag_id = '".$tag_id."'");
		return $tag_stone_query->result_array();
	}
	function get_other_material_details($est_item_id)
	{
			$est_stone_query=$this->db->query("SELECT est_other_material_id,
							  est_id, est_mt.material_id, wt, price ,
							  material_name, material_code,
							  uom_name, uom_short_code
							  FROM ret_estimation_item_other_materials as est_mt
							  LEFT JOIN ret_material as mat ON mat.material_id = est_mt.material_id
							  LEFT JOIN ret_uom as um ON um.uom_id = mat.uom_id
							  WHERE est_mt.est_item_id = '".$est_item_id."'");
			return $est_stone_query->result_array();
	}
	function get_empty_record()
    {
		$emptyquery = $this->db->field_data('ret_estimation');
		$emptydata = array();
		foreach ($emptyquery as $field)
		{
			$emptydata[$field->name] = $field->default;
		}
		$emptydata['estimation_datetime'] = date('d-m-Y H:i:s');
		$emptydata['cus_name'] 			  = '';
        $emptydata['financial_year'] = $this->GetFinancialYear();
		//$emptydata['design_code']  = '';
		return $emptydata;
	}
	function GetFinancialYear()
	{
		$sql=$this->db->query("SELECT fin_year_code,fin_status,fin_year_name From ret_financial_year");
		return $sql->result_array();
	}
    function GetActiveFinancialYear()
	{
		$sql=$this->db->query("SELECT fin_year_code,fin_status,fin_year_name From ret_financial_year where fin_status=1");
		return $sql->row_array();
	}
	public function encrypt($str)
	{
		return base64_encode($str);
	}
	function createNewCustomer($cusname,$cusmobile,$mail,$branch,$id_village,$cus_type,$id_country,$id_state,$id_city,$address1,$address2,$address3,$pincode,$gst_number,$title,$id_profession,$gender,$date_of_birth,$date_of_wed,$pan_no,$aadharid,$dl_no,$pp_no)
	{
		$customer_check_query = $this->db->query("SELECT * FROM customer WHERE mobile='".$cusmobile."'");
		if($customer_check_query->num_rows() == 0){
            if($date_of_birth!=''){
                $d1 = date_create($date_of_birth);
                $dateofbirth = date_format($d1,"Y-m-d");
            }
            if($date_of_wed!=''){
                $d1 = date_create($date_of_wed);
                $dateofwed = date_format($d1,"Y-m-d");
            }
            $insert_data= array(
            "firstname"         => strtoupper($cusname),
            "id_branch"         => $branch,
            "mobile"            => $cusmobile,
            "username"          => $cusmobile,
            "email"             => $mail,
            "passwd"            => $this->encrypt($cusmobile),
            "id_village"        => ($id_village!='' ? $id_village:NULL),
            'cus_type'          => $cus_type,
            'gst_number'        => ($gst_number!='' ? $gst_number:NULL),
            'date_add'          => date("Y-m-d H:i:s"),
            'title'             => $title,
            'id_profession'     => ($id_profession!='' ? $id_profession : NULL),
            'gender'            => $gender,
            'date_of_birth'     => ($dateofbirth!='' ? $dateofbirth :NULL),
            'date_of_wed'       => ($dateofwed!='' ?$dateofwed : NULL),
            'pan'       =>($pan_no!='' ? $pan_no :NULL),
            'aadharid'  =>($aadharid!='' ? $aadharid :NULL),
            'driving_license_no' =>($dl_no!='' ? $dl_no : NULL),
			'passport_no' =>($pp_no!='' ? $pp_no : NULL),
            );
			$cus_insert_id = $this->insertData($insert_data, "customer");
			$insert_address_data  = array("id_customer" => $cus_insert_id, "address1"=>$address1,"address2"=>$address2,"address3"=>$address3,"pincode"=>$pincode,"id_country"=>$id_country,"id_state"=>$id_state,"id_city"=>$id_city);
			$cus_insert_address=$this->insertData($insert_address_data, "address");
            $update_data_add = array(

				"id_address"   => $cus_insert_address,
				);
				$cus_update_id = $this->updateData($update_data_add, "id_customer", $cus_insert_id, "customer");
			if(!empty($cus_insert_id)){
				$insert_data["id_customer"] = $cus_insert_id;
                $insert_data["address1"] = $address1;
				$insert_data["pincode"] = $pincode;
				$insert_data["id_country"] = $id_country;
				$insert_data["id_state"] = $id_state;
				return array("success" => TRUE, "message" => "Customer details added successfully", "response" => $insert_data);
			}else{
				return array("success" => FALSE, "message" => "Could not add customer, please try again", "response" => array());
			}
		}else{
			return array("success" => FALSE, "message" => "Given mobile number already exist", "response" => $customer_check_query->row());
		}
	}
	function updateCustomer($id_customer,$cusname,$cusmobile,$mail,$branch,$cus_type,$id_country,$id_state,$id_city,$address1,$address2,$address3,$pincode,$gst_number,$title,$id_profession,$gender,$date_of_birth,$date_of_wed,$id_village,$pan_no,$aadharid,$dl_no,$pp_no)
	{
            if($date_of_birth!=''){
                $d1 = date_create($date_of_birth);
                $dateofbirth = date_format($d1,"Y-m-d");
            }
            if($date_of_wed!=''){
                $d1 = date_create($date_of_wed);
                $dateofwed = date_format($d1,"Y-m-d");
            }
            $update_Data= array(
                "firstname"         => strtoupper($cusname),
                "id_branch"         => $branch,
                "mobile"            => $cusmobile,
                "username"          => $cusmobile,
                "passwd"            => $this->encrypt($cusmobile),
                'cus_type'          => $cus_type,
                "id_village"=>$this->isEmptySetDefault($id_village,NULL),
                "email"       =>$this->isEmptySetDefault($mail,NULL),
                'gst_number'=>$this->isEmptySetDefault($gst_number,NULL),
                'date_add'          => date("Y-m-d H:i:s"),
                'title'             => $title,
                'id_profession' =>$this->isEmptySetDefault($id_profession,NULL),
                'gender'        =>$this->isEmptySetDefault($gender,NULL),
                'date_of_birth' =>$this->isEmptySetDefault($dateofbirth,NULL),
                'date_of_wed'   =>$this->isEmptySetDefault($dateofwed,NULL),
                'pan'       =>($pan_no!='' ? $pan_no :NULL),
                'aadharid'  =>($aadharid!='' ? $aadharid :NULL),
                'driving_license_no' =>($dl_no!='' ? $dl_no : NULL),
                'passport_no' =>($pp_no!='' ? $pp_no : NULL),
                );
		   $cus_insert_id=$this->updateData($update_Data,'id_customer',$id_customer, 'customer');
		   //print_r($this->db->last_query());exit;
           $update_address  = array("address1"=>$address1,"address2"=>$address2,"address3"=>$address3,"pincode"=>$pincode,"id_country"=>$id_country,"id_state"=>$id_state,"id_city"=>$id_city);
		   $cus_insert_address=$this->updateData($update_address , 'id_customer',$id_customer,"address");
		 // print_r($this->db->last_query());exit;
			if(!empty($cus_insert_id)){
				$update_Data["id_customer"] = $id_customer;
				$update_Data["mobile"] = $cusmobile;
				return array("success" => TRUE, "message" => "Customer details updated successfully", "response" => $update_Data);
			}
	}
	/*function getAvailableCustomers($SearchTxt,$esti_for,$billing_for){
        if($billing_for=='' || $billing_for!=3)
        {
            $data = $this->db->query("SELECT c.id_customer as value, concat(c.firstname,'-',c.mobile) as label,c.id_village,v.village_name,if(c.is_vip=1,'Yes','No') as vip,
            (select count(sa.id_scheme_account) from scheme_account sa where sa.id_customer=c.id_customer) as accounts,
            IFNULL(a.id_country,'') as id_country,IFNULL(a.id_state,'') as id_state,IFNULL(a.id_city,'') as id_city
            FROM customer c
            LEFT JOIN address a on a.id_customer=c.id_customer
            left join village v on v.id_village=c.id_village
            WHERE (username like '%".$SearchTxt."%' OR mobile like '%".$SearchTxt."%' OR firstname like '%".$SearchTxt."%')
            ".($esti_for==3 ? " and c.cus_type=2" :'')."");
        }
        else if($billing_for==3)
        {
            $data=$this->db->query("SELECT concat(k.firstname,'-',k.contactno1)  as label,k.id_karigar as value,k.address1,k.id_country,k.id_state,k.id_city
            FROM ret_karigar k
            WHERE k.firstname like '%".$SearchTxt."%' OR k.contactno1 like '%".$SearchTxt."%' ");
            //print_r($this->db->last_query());exit;
        }
		return $data->result_array();
	}*/
	function getAvailableCustomers($SearchTxt,$esti_for,$billing_for)
	{
        if($billing_for=='' || $billing_for!=3)
        {
            $data = $this->db->query("SELECT c.cus_type,c.id_customer as value, concat(c.firstname,'-',c.mobile) as label,c.id_village,v.village_name,if(c.is_vip=1,'Yes','No') as vip,
            (select count(sa.id_scheme_account) from scheme_account sa where sa.id_customer=c.id_customer) as accounts,
            IFNULL(a.id_country,'') as id_country,IFNULL(a.id_state,'') as id_state,IFNULL(a.id_city,'') as id_city,c.mobile, c.firstname as firstname,
            c.pan as pan_no, c.aadharid, c.driving_license_no as dl_no, c.passport_no as pp_no
            FROM customer c
            LEFT JOIN address a on a.id_customer=c.id_customer
            left join village v on v.id_village=c.id_village
            WHERE (mobile like ? OR firstname like ?)
             GROUP by c.id_customer",
            array('%'.$SearchTxt.'%', '%'.$SearchTxt.'%'));
            //print_r($this->db->last_query());exit;
        }
        else if($billing_for==3)
        {
            $data=$this->db->query("SELECT concat(k.firstname,'-',k.contactno1)  as label,k.id_karigar as value,k.address1,k.id_country,k.id_state,k.id_city
            FROM ret_karigar k
            WHERE k.firstname like ? OR k.contactno1 like ? GROUP by k.id_karigar",
            array('%'.$SearchTxt.'%', '%'.$SearchTxt.'%'));
            //print_r($this->db->last_query());exit;
        }
		return $data->result_array();
	}
     /*function getTaggingBySearch($SearchTxt,$branch)
    {
        $data = $this->db->query("SELECT tag.tag_id as value,
        tag.tag_id as label, tag_code, tag_datetime, tag.tag_type, tag_lot_id,
        design_id, cost_center, tag.purity, tag.size, uom, piece, tag.less_wt, tag.net_wt, tag.gross_wt,
        tag.calculation_based_on, retail_max_wastage_percent,tag_mc_value,tag_mc_type,
        halmarking, sales_value, tag.tag_status, product_name, product_short_code, lot_product, pur.purity as purname,lot_inw.lot_received_at
        ,group_concat(m.tax_percentage) as tax_percentage,GROUP_CONCAT(i.tgi_calculation) as tgi_calculation,
        ifnull(tag_stn_detail.stn_amount,0) as stone_price,tag.tag_id,pro.sales_mode,tag.item_rate
        FROM ret_taging as tag
        Left join ret_lot_inwards_detail lot_det ON tag.id_lot_inward_detail = lot_det.id_lot_inward_detail
        LEFT JOIN ret_lot_inwards as lot_inw ON lot_inw.lot_no = lot_det.lot_no
        LEFT JOIN ret_product_master as pro ON pro.pro_id = lot_det.lot_product
        LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity
        left join ret_category c on c.id_ret_category=pro.cat_id
        left join metal mt on mt.id_metal=c.id_metal
        left join ret_taxgroupitems i on i.tgi_tgrpcode=mt.tgrp_id
        left join ret_taxmaster m on m.tax_id=i.tgi_taxcode
        LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(wt) as stn_wt FROM `ret_taging_stone` GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = tag.tag_id
        WHERE tag.current_branch=".$branch." AND tag.tag_id LIKE '%".$SearchTxt."%'");
       // print_r($this->db->last_query());exit;
        return $data->result_array();
    }*/
     function getTaggingBySearch($SearchTxt,$searchField,$branch)
    {
        // Whitelist allowed search columns to prevent SQL injection (EST-R301)
        if ($SearchTxt != '') {
            $allowed_fields = array('tag_id', 'tag_code', 'barcode', 'old_tag_id');
            if (!in_array($searchField, $allowed_fields)) {
                return array();
            }
        }

        $data = $this->db->query("SELECT tag.tag_id as value,
        tag_code as label, tag_datetime, tag.tag_type, tag_lot_id,
        design_id, cost_center, tag.purity, IFNULL(tag.size,'') as size, uom, piece, IFNULL(tag.less_wt,0) as less_wt,IFNULL(tag.net_wt,0) as net_wt,IFNULL(tag.gross_wt,0) as gross_wt,
        tag.calculation_based_on, retail_max_wastage_percent,tag_mc_value,tag_mc_type,
        halmarking,tag.sell_rate as sales_value, tag.tag_status, product_name, product_short_code, tag.product_id as lot_product,
        pur.purity as purname,lot_inw.lot_received_at,
        ifnull(tag_stn_detail.stn_amount,if(tag.id_orderdetails!='',ord.stn_amt,0)) as stone_price,IFNULL(tag_stn_detail.certification_cost,0) as certification_cost,
        tag.tag_id,pro.sales_mode,tag.item_rate,tax.tax_percentage,tax.tgi_calculation,pro.metal_type,tag.current_branch,
        pro.tgrp_id as tax_group_id,IFNULL(tag.id_orderdetails,'') as id_orderdetails,
        IFNULL(lot_inw.order_no,'') as order_no,des.design_name,ifnull(tag.tag_mark,0) as Is_green_tag,r.rate_field,r.market_rate_field, rtc.charge_value,sub.sub_design_name,tag.id_sub_design,
        IFNULL(coll.id_tag_mapping,'') as id_tag_mapping,IFNULL(coll.id_mapping_details,'') as id_mapping_details,
        IFNULL(tagOtherMetal.tag_other_itm_amount,0) as tag_other_itm_amount,c.scheme_closure_benefit,c.id_ret_category, CONCAT(s.value, ' ', s.name) as size_name,IFNULL(tag.tag_purchase_cost,0) as tag_purchase_cost,
        IFNULL(sec.section_name,'') as section_name,IFNULL(tag.hu_id,'') as huid,tag.id_section
        FROM ret_taging as tag
        Left join ret_lot_inwards_detail lot_det ON tag.id_lot_inward_detail = lot_det.id_lot_inward_detail
        LEFT JOIN ret_lot_inwards as lot_inw ON lot_inw.lot_no = lot_det.lot_no
        LEFT JOIN ret_product_master as pro ON pro.pro_id = tag.product_id
        LEFT JOIN ret_design_master des on des.design_no=tag.design_id
        left join ret_sub_design_master sub  on sub.id_sub_design=tag.id_sub_design
        LEFT JOIN ret_section sec ON sec.id_section = tag.id_section
        LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity
        LEFT JOIN ret_size s on s.id_size=tag.size
        left join ret_category c on c.id_ret_category=pro.cat_id
        left join metal mt on mt.id_metal=c.id_metal
        left join customerorderdetails ord on ord.id_orderdetails=tag.id_orderdetails
        LEFT JOIN ret_metal_purity_rate r on r.id_metal=c.id_metal and r.id_purity=tag.purity
		LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,
		GROUP_CONCAT(m.tax_percentage) as tax_percentage,
		GROUP_CONCAT(i.tgi_calculation) as tgi_calculation
		FROM ret_taxgroupitems i
		LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=pro.tgrp_id
        LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(wt) as stn_wt,sum(certification_cost) as certification_cost
        FROM `ret_taging_stone` WHERE is_apply_in_lwt = 1
        GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = tag.tag_id
		LEFT JOIN (SELECT tag_id, SUM(IFNULL(charge_value,0)) AS charge_value FROM ret_taging_charges GROUP BY tag_id) AS rtc ON rtc.tag_id = tag.tag_id
		LEFT JOIN (SELECT tag_other_itm_tag_id, SUM(IFNULL(tag_other_itm_amount,0)) AS tag_other_itm_amount FROM ret_tag_other_metals GROUP BY tag_other_itm_tag_id) AS tagOtherMetal ON tagOtherMetal.tag_other_itm_tag_id = tag.tag_id
		LEFT JOIN(SELECT d.tag_id,d.id_tag_mapping,(d.tag_id) as total_set_collection,d.id_mapping_details
                 FROM ret_tag_collection_mapping_details d
                 LEFT JOIN ret_tag_collection_mapping tagmap ON tagmap.id_tag_mapping=d.id_tag_mapping
                 WHERE tagmap.status=0
                 ) as coll ON coll.tag_id=tag.tag_id
        WHERE tag.tag_status=0 and tag.id_orderdetails is NULL AND tag.current_branch=? AND tag.".$searchField." = ?",
        array($branch, $SearchTxt));
        //print_r($this->db->last_query());exit;
        $returndata = $data->result_array();
        foreach($returndata as $rkey => $rval){
            if($rval['id_tag_mapping']!='')
            {
                $collectionDetails   = $this->getCollectionDetails($rval['id_tag_mapping']);
                $returndata[$rkey]['collection_details'] =$collectionDetails;
                $returndata[$rkey]['total_set_collection'] =sizeof($collectionDetails);
            }
            $returndata[$rkey]['stone_details'] = $this->getTagStoneDetails($rval['value']);
            $returndata[$rkey]['other_metal_details'] = $this->get_other_metal_details($rval['value']);
            $returndata[$rkey]['mc_va_limit'] = $this->get_mc_va_limit($rval['current_branch'],$rval['lot_product'], $rval['design_id'], $rval['id_sub_design']);
            $returndata[$rkey]['po_details'] = $this->get_purchase_details($rval['lot_product'], $rval['design_id'], $rval['id_sub_design'], $rval['tag_lot_id']);
            $returndata[$rkey]['charges_details'] = $this->get_charges($rval['value']);
            $returndata[$rkey]['tag_images'] = $this->getTagImageDetails($rval['value']);
        }
        //return $data->result_array();
        return $returndata;
    }
    function getTaggingSearchByCollection($data)
    {
        $data = $this->db->query("SELECT tag.tag_id as value,
        tag_code as label, tag_datetime, tag.tag_type, tag_lot_id,
        design_id, cost_center, tag.purity, IFNULL(tag.size,'') as size, uom, piece, IFNULL(tag.less_wt,0) as less_wt,IFNULL(tag.net_wt,0) as net_wt,IFNULL(tag.gross_wt,0) as gross_wt,
        tag.calculation_based_on, retail_max_wastage_percent,tag_mc_value,tag_mc_type,
        halmarking,tag.sell_rate as sales_value, tag.tag_status, product_name, product_short_code, tag.product_id as lot_product,
        pur.purity as purname,lot_inw.lot_received_at,
        ifnull(tag_stn_detail.stn_amount,if(tag.id_orderdetails!='',ord.stn_amt,0)) as stone_price,IFNULL(tag_stn_detail.certification_cost,0) as certification_cost,
        tag.tag_id,pro.sales_mode,tag.item_rate,tax.tax_percentage,tax.tgi_calculation,pro.metal_type,tag.current_branch,
        pro.tgrp_id as tax_group_id,IFNULL(tag.id_orderdetails,'') as id_orderdetails,
        IFNULL(lot_inw.order_no,'') as order_no,des.design_name,tag.tag_mark,r.rate_field,r.market_rate_field, rtc.charge_value,sub.sub_design_name,tag.id_sub_design,
        IFNULL(coll.id_tag_mapping,'') as id_tag_mapping,IFNULL(d.id_mapping_details,'') as id_mapping_details,
        IFNULL(tagOtherMetal.tag_other_itm_amount,0) as tag_other_itm_amount,c.scheme_closure_benefit, CONCAT(s.value, ' ', s.name) as size_name,IFNULL(tag.tag_purchase_cost,0) as tag_purchase_cost,
        IFNULL(sec.section_name,'') as section_name,IFNULL(tag.hu_id,'') as huid,tag.id_section
        FROM ret_tag_collection_mapping_details d
        LEFT JOIN ret_taging as tag on tag.tag_id=d.tag_id
        Left join ret_lot_inwards_detail lot_det ON tag.id_lot_inward_detail = lot_det.id_lot_inward_detail
        LEFT JOIN ret_lot_inwards as lot_inw ON lot_inw.lot_no = lot_det.lot_no
        LEFT JOIN ret_product_master as pro ON pro.pro_id = tag.product_id
        LEFT JOIN ret_design_master des on des.design_no=tag.design_id
        left join ret_sub_design_master sub  on sub.id_sub_design=tag.id_sub_design
        LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity
        LEFT JOIN ret_size s on s.id_size=tag.size
        left join ret_category c on c.id_ret_category=pro.cat_id
        left join metal mt on mt.id_metal=c.id_metal
        left join customerorderdetails ord on ord.id_orderdetails=tag.id_orderdetails
        LEFT JOIN ret_metal_purity_rate r on r.id_metal=c.id_metal and r.id_purity=tag.purity
        LEFT JOIN ret_section sec ON sec.id_section = tag.id_section
		LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,
		GROUP_CONCAT(m.tax_percentage) as tax_percentage,
		GROUP_CONCAT(i.tgi_calculation) as tgi_calculation
		FROM ret_taxgroupitems i
		LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=pro.tgrp_id
        LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(wt) as stn_wt,sum(certification_cost) as certification_cost
        FROM `ret_taging_stone` WHERE is_apply_in_lwt = 1
        GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = tag.tag_id
		LEFT JOIN (SELECT tag_id, SUM(IFNULL(charge_value,0)) AS charge_value FROM ret_taging_charges GROUP BY tag_id) AS rtc ON rtc.tag_id = tag.tag_id
		LEFT JOIN (SELECT tag_other_itm_tag_id, SUM(IFNULL(tag_other_itm_amount,0)) AS tag_other_itm_amount FROM ret_tag_other_metals GROUP BY tag_other_itm_tag_id) AS tagOtherMetal ON tagOtherMetal.tag_other_itm_tag_id = tag.tag_id
		LEFT JOIN(SELECT d.tag_id,d.id_tag_mapping,(d.tag_id) as total_set_collection
                 FROM ret_tag_collection_mapping_details d
                 LEFT JOIN ret_tag_collection_mapping tagmap ON tagmap.id_tag_mapping=d.id_tag_mapping
                 WHERE tagmap.status=0
                 ) as coll ON coll.tag_id=tag.tag_id
        WHERE tag.tag_status=0 and tag.id_orderdetails is NULL AND tag.current_branch=".$data['id_branch']." and d.id_tag_mapping=".$data['id_tag_mapping']."");
        //print_r($this->db->last_query());exit;
        $returndata = $data->result_array();
        foreach($returndata as $rkey => $rval){
            $returndata[$rkey]['stone_details'] = $this->getTagStoneDetails($rval['value']);
            $returndata[$rkey]['other_metal_details'] = $this->get_other_metal_details($rval['value']);
            $returndata[$rkey]['mc_va_limit'] = $this->get_mc_va_limit($rval['current_branch'],$rval['lot_product'], $rval['design_id'], $rval['id_sub_design']);

            $returndata[$rkey]['tag_images'] = $this->getTagImageDetails($rval['value']);
        }
        return $returndata;
    }
    function getCollectionDetails($id_tag_mapping)
    {
        $sql=$this->db->query("SELECT t.tag_id,t.tag_code
        FROM ret_tag_collection_mapping_details d
        LEFT JOIN ret_taging t ON t.tag_id=d.tag_id
        WHERE d.id_tag_mapping=".$id_tag_mapping."");
        return $sql->result_array();
    }
    function tag_reserve_check($tag_code){
        $sql=$this->db->query("SELECT t.id_orderdetails as reserved,t.tag_status as status
        FROM ret_taging t
        WHERE t.tag_code='".$tag_code."' and id_orderdetails is not null");
        // if($sql->num_rows()>0){
        //     return 1;
        // }else{
        //     return 0;
        // }
        return $sql->result_array();
    }
    function getTaggingScanBySearch($SearchTxt,$searchField,$branch,$order_no)
    {
        // Whitelist allowed search columns to prevent SQL injection (EST-R301)
        if ($SearchTxt != '') {
            $allowed_fields = array('tag_id', 'tag_code', 'barcode', 'old_tag_id');
            if (!in_array($searchField, $allowed_fields)) {
                return array();
            }
        }

        $data = $this->db->query("SELECT tag.tag_id as value,
        tag_code as label, tag_datetime, tag.tag_type, tag_lot_id,
        design_id, cost_center, tag.purity, IFNULL(tag.size,'') as size, uom, piece, IFNULL(tag.less_wt,0) as less_wt,IFNULL(tag.net_wt,0) as net_wt,IFNULL(tag.gross_wt,0) as gross_wt,
        tag.calculation_based_on, retail_max_wastage_percent,tag_mc_value,tag_mc_type,
        halmarking,tag.sell_rate as sales_value, tag.tag_status, product_name, product_short_code, tag.product_id as lot_product,
        ifnull(pur.purity,'-') as purname,lot_inw.lot_received_at,IFNULL(tag.tag_mark,0) as Is_green_tag,
        ifnull(tag_stn_detail.stn_amount,if(tag.id_orderdetails!='',ord.stn_amt,0)) as stone_price,IFNULL(tag_stn_detail.certification_cost,0) as certification_cost,
        tag.tag_id,pro.sales_mode,tag.item_rate,tax.tax_percentage,tax.tgi_calculation,pro.metal_type,tag.current_branch,
        pro.tgrp_id as tax_group_id,IFNULL(tag.id_orderdetails,'') as id_orderdetails,
        IFNULL(lot_inw.order_no,'') as order_no,des.design_name,tag.tag_mark,r.rate_field,r.market_rate_field, IFNULL(rtc.charge_value,0) as charge_value,sub.sub_design_name,tag.id_sub_design,ord.rate_per_gram as order_rate_per_grm,cusord.rate_type as ord_rate_type,
        IFNULL(coll.id_tag_mapping,'') as id_tag_mapping,IFNULL(coll.id_mapping_details,'') as id_mapping_details,IFNULL(tagOtherMetal.tag_other_itm_amount,0) as tag_other_itm_amount,c.scheme_closure_benefit, ifnull(CONCAT(s.value, ' ', s.name),'-') as size_name,
        IFNULL(tag.tag_purchase_cost,0) as tag_purchase_cost,IFNULL(sec.section_name,'') as section_name,IFNULL(tag.hu_id,'') as huid,tag.id_section,ifnull(tag.quality_id,'') as quality_id,pro.stone_type,ifnull(tag.uom_gross_wt,'') as uom_id,ifnull(tag.stone_calculation_based_on,'') as stone_calculation_based_on,
        pro.cat_id,c.id_metal,
        cus.id_customer , concat(cus.firstname,'-',cus.mobile) as name,cus.mobile,retail_max_wastage_percent as max_va_per
        FROM ret_taging as tag
        Left join ret_lot_inwards_detail lot_det ON tag.id_lot_inward_detail = lot_det.id_lot_inward_detail
        LEFT JOIN ret_lot_inwards as lot_inw ON lot_inw.lot_no = lot_det.lot_no
        LEFT JOIN ret_product_master as pro ON pro.pro_id = tag.product_id
        LEFT JOIN ret_design_master des on des.design_no=tag.design_id
        left join ret_sub_design_master sub  on sub.id_sub_design=tag.id_sub_design
        LEFT JOIN ret_section sec ON sec.id_section = tag.id_section
        LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity
        LEFT JOIN ret_size s on s.id_size=tag.size
        left join ret_category c on c.id_ret_category=pro.cat_id
        left join metal mt on mt.id_metal=c.id_metal
        left join customerorderdetails ord on ord.id_orderdetails=tag.id_orderdetails
        LEFT JOIN customerorder cusord ON cusord.id_customerorder = ord.id_customerorder
        LEFT JOIN customer cus ON cus.id_customer = cusord.order_to
        LEFT JOIN ret_metal_purity_rate r on r.id_metal=c.id_metal and r.id_purity=tag.purity
		LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,
		GROUP_CONCAT(m.tax_percentage) as tax_percentage,
		GROUP_CONCAT(i.tgi_calculation) as tgi_calculation
		FROM ret_taxgroupitems i
		LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=pro.tgrp_id
        LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(wt) as stn_wt,sum(certification_cost) as certification_cost
        FROM `ret_taging_stone` WHERE is_apply_in_lwt = 1
        GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = tag.tag_id
		LEFT JOIN (SELECT tag_id, SUM(IFNULL(charge_value,0)) AS charge_value FROM ret_taging_charges GROUP BY tag_id) AS rtc ON rtc.tag_id = tag.tag_id
		LEFT JOIN (SELECT tag_other_itm_tag_id, SUM(IFNULL(tag_other_itm_amount,0)) AS tag_other_itm_amount FROM ret_tag_other_metals GROUP BY tag_other_itm_tag_id) AS tagOtherMetal ON tagOtherMetal.tag_other_itm_tag_id = tag.tag_id
		LEFT JOIN(SELECT d.tag_id,d.id_tag_mapping,(d.tag_id) as total_set_collection,d.id_mapping_details
                 FROM ret_tag_collection_mapping_details d
                 LEFT JOIN ret_tag_collection_mapping tagmap ON tagmap.id_tag_mapping=d.id_tag_mapping
                 WHERE tagmap.status=0
                 ) as coll ON coll.tag_id=tag.tag_id
        WHERE tag.tag_status=0  AND tag.current_branch=?
        ".($SearchTxt!='' && $searchField!='' ? " AND tag.".$searchField." = ? and tag.id_orderdetails is NULL " :'')."
        ".($order_no!='' ? " and cusord.order_no= ? and tag.id_orderdetails is NOT NULL and ord.orderstatus = 4 " :'')."
         ",
        array_merge(
            array($branch),
            ($SearchTxt!='' && $searchField!='' ? array($SearchTxt) : array()),
            ($order_no!='' ? array($order_no) : array())
        ));
        //print_r($this->db->last_query());exit;
        $returndata = $data->result_array();
        foreach($returndata as $rkey => $rval){
            if($rval['id_tag_mapping']!='')
            {
                $collectionDetails   = $this->getCollectionDetails($rval['id_tag_mapping']);
                $returndata[$rkey]['collection_details'] =$collectionDetails;
                $returndata[$rkey]['total_set_collection'] =sizeof($collectionDetails);
            }
            $returndata[$rkey]['stone_details'] = $this->getTagStoneDetails($rval['value']);
            $returndata[$rkey]['mc_va_limit'] = $this->get_mc_va_limit($rval['current_branch'],$rval['lot_product'], $rval['design_id'], $rval['id_sub_design']);
            $returndata[$rkey]['other_metal_details'] = $this->get_other_metal_details($rval['value']);
            $returndata[$rkey]['tag_images'] = $this->getTagImageDetails($rval['value']);
            $returndata[$rkey]['charges_details'] = $this->get_charges($rval['value']);
        }
        return $returndata;
    }
    function getTagStoneDetails($tagid)
    {
         $data = $this->db->query("SELECT ts.tag_id, ts.stone_id, ts.pieces, ts.wt, ts.uom_id,ts.wt as act_stone_wt,ts.pieces as act_stn_pcs,ts.pieces as stone_pcs,
         if(ts.rate_per_gram=0,round((ts.amount/ts.wt),2),rate_per_gram) as rate_per_gram, amount, ts.is_apply_in_lwt , st.stone_type , stone_cal_type,

         ts.is_apply_in_lwt as show_in_lwt,ts.wt as stone_wt,amount as stone_price,if(ts.rate_per_gram=0,round((ts.amount/ts.wt),2),rate_per_gram) as stone_rate,IFNULL(ts.stone_quality_id,'') as quality_id,

         ts.uom_id as stone_uom_id
        FROM ret_taging_stone ts
        LEFT JOIN ret_stone as st ON st.stone_id = ts.stone_id
        where tag_id = $tagid");
        return $data->result_array();
    }
    function get_other_metal_details($tagid)
    {
         $data = $this->db->query("Select rm.tag_other_itm_id,rm.tag_other_itm_tag_id,rm.id_metal,
         rm.tag_other_itm_metal_id,rm.tag_other_itm_pur_id,tag_other_itm_grs_weight,rm.tag_other_itm_wastage,
         rm.tag_other_itm_uom,rm.tag_other_itm_cal_type,rm.tag_other_itm_mc,rm.tag_other_itm_rate,
         rm.tag_other_itm_pcs,rm.tag_other_itm_amount,IFNULL(mt.metal,'-') as metal_name,IFNULL(pur.purity ,'-')as purname
         from ret_tag_other_metals rm
         left join ret_category c on c.id_ret_category=rm.tag_other_itm_metal_id
         left join metal mt on mt.id_metal=c.id_metal
         LEFT JOIN ret_purity as pur ON pur.id_purity = rm.tag_other_itm_pur_id
         where tag_other_itm_tag_id = $tagid");
        return $data->result_array();
    }
    function getProductBySearch($SearchTxt,$category,$is_non_tag,$id_branch,$pro_id)
    {
        $data=array();
        if($SearchTxt!='')
        {
            $products = $this->db->query("SELECT pro_id as value,
            concat(product_name,'-',product_short_code) as label,
            wastage_type, other_materials, has_stone,sales_mode,
            has_hook, has_screw, has_fixed_price,
            has_size, less_stone_wt, no_of_pieces,metal_type, calculation_based_on,group_concat(m.tax_percentage) as tax_percentage,GROUP_CONCAT(i.tgi_calculation) as tgi_calculation,pro.cat_id,
            mt.id_metal,pro.tgrp_id as tax_group_id,
            ifnull(nt.gross_wt,0) as gross_wt,IFNULL(nt.no_of_piece,0) as no_of_piece,c.scheme_closure_benefit
            FROM ret_product_master as pro
	            left join ret_category c on c.id_ret_category=pro.cat_id
	            left join metal mt on mt.id_metal=c.id_metal
	            left join ret_taxgroupitems i on i.tgi_tgrpcode=pro.tgrp_id
	            left join ret_taxmaster m on m.tax_id=i.tgi_taxcode
	            left join ret_nontag_item nt on nt.product=pro.pro_id
	            left join ret_design_masterdes on des.design_no=nt.design_id
			WHERE (product_short_code LIKE '%".$SearchTxt."%' OR product_name LIKE '%".$SearchTxt."%') ".(empty($category) ? ' ' : 'AND pro.cat_id ='.$category)."
			".($id_branch!='' && $is_non_tag==1 ? " and nt.branch=".$id_branch."" :'')."
			GROUP BY pro.pro_id");
			//print_r($this->db->last_query());exit;
            $data=$products->result_array();
        }else{
            $products = $this->db->query("SELECT pro_id as value,
            concat(product_name,'-',product_short_code) as label,
            wastage_type, other_materials, has_stone,sales_mode,
            has_hook, has_screw, has_fixed_price,
            has_size, less_stone_wt, no_of_pieces,metal_type, calculation_based_on,group_concat(m.tax_percentage) as tax_percentage,GROUP_CONCAT(i.tgi_calculation) as tgi_calculation,pro.cat_id,
            mt.id_metal,pro.tgrp_id as tax_group_id,
            ifnull(nt.gross_wt,0) as gross_wt,IFNULL(nt.no_of_piece,0) as no_of_piece,c.scheme_closure_benefit
            FROM ret_product_master as pro
            left join ret_category c on c.id_ret_category=pro.cat_id
            left join metal mt on mt.id_metal=c.id_metal
            left join ret_taxgroupitems i on i.tgi_tgrpcode=pro.tgrp_id
            left join ret_taxmaster m on m.tax_id=i.tgi_taxcode
            left join ret_nontag_item nt on nt.product=pro.pro_id
            WHERE  pro.pro_id is not null
            ".($pro_id!='' && $pro_id>0 ? "AND pro.pro_id=".$pro_id."":"")."
            ".(empty($category) ? ' ' : 'AND pro.cat_id ='.$category)."
            ".($id_branch!='' && $is_non_tag==1 ? " and nt.branch=".$id_branch."" :'')."
            GROUP BY pro.pro_id");
            //print_r($this->db->last_query());exit;
            $data=$products->result_array();
        }
        return $data;
    }
   function getdesigndetails($cat_product,$cat_design,$cat_sub_design)
   {
        $sql=$this->db->query("SELECT rn.no_of_piece,rn.gross_wt
        FROM  ret_nontag_item rn
        where rn.product is not null
        ".($cat_product!='' && $cat_product > 0 ? "AND rn.product=".$cat_product."":"")."
        ".($id_branch!='' && $id_branch > 0 ? "AND rn.branch=".$id_branch."":"")."
        ".($cat_design!='' && $cat_design > 0 ? "AND rn.design=".$cat_design."":"")."
        ".($cat_sub_design!='' && $cat_sub_design> 0 ? "AND rn.id_sub_design=".$cat_sub_design."":"")."
        " );
        return $sql->row_array();
   }
   function getNonTagproducts()
    {
        $sql = $this->db->query("SELECT *,m.id_metal,tgrp_name

         FROM ret_product_master pro
         left join ret_category as cat on cat.id_ret_category=pro.cat_id
            left join metal as m on m.id_metal=cat.id_metal
            LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,
                                    m.tax_percentage as tax_percentage,
                                    i.tgi_calculation as tgi_calculation
                                    FROM ret_taxgroupitems i
                                    LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=pro.tgrp_id
            LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id
        where stock_type=2");
        return $sql->result_array();
    }
      function getCustomProductBySearch($SearchTxt,$category,$pro_id)
    {
        $data=array();
        if($SearchTxt!='')
        {
            $products = $this->db->query("SELECT pro_id as value,
            concat(product_name,'-',product_short_code) as label,
            wastage_type, other_materials, has_stone,sales_mode,
            has_hook, has_screw, has_fixed_price,
            has_size, less_stone_wt, no_of_pieces,metal_type, calculation_based_on,group_concat(m.tax_percentage) as tax_percentage,GROUP_CONCAT(i.tgi_calculation) as tgi_calculation,
            mt.id_metal,pro.tgrp_id as tax_group_id,c.scheme_closure_benefit
            FROM ret_product_master as pro
	            left join ret_category c on c.id_ret_category=pro.cat_id
	            left join metal mt on mt.id_metal=c.id_metal
	            left join ret_taxgroupitems i on i.tgi_tgrpcode=pro.tgrp_id
	            left join ret_taxmaster m on m.tax_id=i.tgi_taxcode
			WHERE (product_short_code LIKE '%".$SearchTxt."%' OR product_name LIKE '%".$SearchTxt."%') ".(empty($category) ? ' ' : 'AND pro.cat_id ='.$category)." GROUP BY pro.pro_id");
			// print_r($this->db->last_query());exit;
            $data=$products->result_array();
        }else{
            $products = $this->db->query("SELECT pro_id as value,
                concat(product_name,'-',product_short_code) as label,
                wastage_type, other_materials, has_stone,sales_mode,
                has_hook, has_screw, has_fixed_price,
                has_size, less_stone_wt, no_of_pieces,metal_type, calculation_based_on,group_concat(m.tax_percentage) as tax_percentage,GROUP_CONCAT(i.tgi_calculation) as tgi_calculation,
                mt.id_metal,pro.tgrp_id as tax_group_id,c.scheme_closure_benefit
                FROM ret_product_master as pro
                left join ret_category c on c.id_ret_category=pro.cat_id
                left join metal mt on mt.id_metal=c.id_metal
                left join ret_taxgroupitems i on i.tgi_tgrpcode=pro.tgrp_id
                left join ret_taxmaster m on m.tax_id=i.tgi_taxcode
                WHERE  pro.pro_id is not null
                ".($pro_id!='' && $pro_id>0 ? "and pro.pro_id=".$pro_id."":"")."
                ".(empty($category) ? ' ' : 'AND pro.cat_id ='.$category)." GROUP BY pro.pro_id");
            $data=$products->result_array();
        }
        return $data;
    }
    /*function getProductDesignBySearch($SearchTxt, $procode,$id_branch)
    {
        $data = $this->db->query("SELECT design_no as value,
        concat(design_name,'-',design_code) as label,
        min_length, max_length, min_width, max_width,
        min_dia, max_dia,
        min_weight, max_weight, fixed_rate,ifnull(nt.gross_wt,0) as gross_wt,IFNULL(nt.no_of_piece,0) as no_of_piece
        FROM ret_design_master as des
        left join ret_nontag_item nt on nt.design=des.design_no
        WHERE ( design_code LIKE '%".$SearchTxt."%' OR design_name LIKE '%".$SearchTxt."%' )
        ".($id_branch!='' ? " and nt.branch=".$id_branch."" :'')."
        ".(empty($procode) ? ' ' : 'AND product_id ='.$procode) );
        return $data->result_array();
    }*/
    /*function getProductDesignBySearch($SearchTxt, $procode,$id_branch)
    {
        $data = $this->db->query("SELECT design_no as value,
        concat(design_name,'-',design_code) as label,
        min_length, max_length, min_width, max_width,
        min_dia, max_dia,
        min_weight, max_weight, fixed_rate
        FROM ret_design_master as des
        WHERE ( design_code LIKE '%".$SearchTxt."%' OR design_name LIKE '%".$SearchTxt."%' )
        ".(empty($procode) ? ' ' : 'AND product_id ='.$procode) );
    print_r($this->db->last_query());exit;
        return $data->result_array();
    }
    */
     function getProductDesignBySearch($SearchTxt, $procode,$id_branch,$is_non_tag)
    {
        $data = $this->db->query("SELECT design_no as value,
        concat(design_name,'-',design_code) as label,
        min_length, max_length, min_width, max_width,
        min_dia, max_dia,
        min_weight, max_weight, fixed_rate,ifnull(nt.gross_wt,0) as gross_wt,IFNULL(nt.no_of_piece,0) as no_of_piece
        FROM ret_product_mapping p
        LEFT JOIN ret_product_master pro ON pro.pro_id=p.pro_id
        LEFT JOIN ret_design_master des ON des.design_no=p.id_design
        left join ret_nontag_item nt on nt.design=des.design_no
        WHERE ( design_code LIKE '%".$SearchTxt."%' OR design_name LIKE '%".$SearchTxt."%' )
         ".($id_branch!='' && $is_non_tag==1 ? " and nt.branch=".$id_branch."" :'')."
        ".(empty($procode) ? ' ' : 'AND pro.pro_id ='.$procode)."
        GROUP BY design_no");
       // print_r($this->db->last_query());exit;
        return $data->result_array();
    }
	function getMetalTypes(){
		$query = $this->db->query("SELECT id_metal, metal FROM metal");
		return $query->result_array();
	}
	function getUOMDetails()
	{
		$sql = $this->db->query("SELECT * FROM ret_uom where uom_status = 1");
		return $sql->result_array();
	}
	function get_old_metal_rate($id_metal)
	{
		$sql=$this->db->query("SELECT * from ret_old_metal_rate where id_metal=".$id_metal." and status=1");
		return $sql->row_array();
	}
	function get_all_old_metal_rates($id_metal = 0)
	{
		$where = "";
		if($id_metal > 0) {
			$where = " AND id_metal=".$id_metal;
		}
		$sql=$this->db->query("SELECT id_old_metal_rate, id_metal, IF(id_metal = 1 , 'Old Gold', 'Old Silver') AS metal, id_purity, id_branch, rate  from ret_old_metal_rate where 1 ".$where." and status=1");
		return $sql->result_array();
	}
	//chit account
	function get_closed_accounts($SearchTxt){
		$data = $this->db->query("SELECT  sa.id_scheme_account as value,sa.scheme_acc_number as label,sa.closing_balance,sa.is_closed,s.scheme_type
				from scheme_account sa
				left join scheme s on s.id_scheme=sa.id_scheme
				WHERE sa.id_scheme_account LIKE '%".$SearchTxt."%' and sa.is_closed=1");
		//echo $this->db->last_query();exit;
		return $data->result_array();
	}
	function get_employee($id_branch)
	{
		$result = [];
        $data = $this->db->query("SELECT
        e.id_employee,
        CONCAT(CONCAT(e.emp_code,'-',e.firstname),' ',e.lastname)as emp_name,
        s.disc_limit_type,s.disc_limit,s.allowed_old_met_pur,e.login_branches,
        s.allow_branch_transfer,cs.login_branch
        FROM employee e
        LEFT JOIN employee_settings s on s.id_employee = e.id_employee
        JOIN chit_settings cs
        WHERE e.active=1");
		$employees = $data->result_array();
		if(sizeof($employees) > 0){
			if($id_branch == 0 || $id_branch == '' || $employees[0]['login_branch'] == 0){
				return $employees;
			}else{
				foreach($employees as $emp){
					if($emp['login_branches'] == 0 || $emp['login_branches'] == NULL){
						$result[] = array(
									"id_employee" 			=> $emp['id_employee'],
									"emp_name" 				=> $emp['emp_name'],
									"disc_limit_type"		=> $emp['disc_limit_type'],
									"disc_limit" 			=> $emp['disc_limit'],
									"allowed_old_met_pur"	=> $emp['allowed_old_met_pur'],
									"allow_branch_transfer" => $emp['allow_branch_transfer']
								);
					}else{
						$login_branches = explode(',',$emp['login_branches']);
						foreach($login_branches as $b){
							if($b == $id_branch){
								$result[] = array(
										"id_employee" 			=> $emp['id_employee'],
										"emp_name" 				=> $emp['emp_name'],
										"disc_limit_type"		=> $emp['disc_limit_type'],
										"disc_limit" 			=> $emp['disc_limit'],
										"allowed_old_met_pur"	=> $emp['allowed_old_met_pur'],
										"allow_branch_transfer" => $emp['allow_branch_transfer']
									);
							}
						}
					}
				}
				return $result;
			}
		}else{
			return $result;
		}
	}
	function get_branchwise_rate($id_branch)
	{
		$is_branchwise_rate=$this->session->userdata('is_branchwise_rate');
		if($id_branch!='' && $id_branch!=0 && $is_branchwise_rate==1)
		{
		    $sql="SELECT  b.name as name,m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,
		    Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime,m.goldrate_18ct,m.goldrate_14ct,m.goldrate_9ct
		    FROM metal_rates m
		    LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates
		    left join branch b on b.id_branch=br.id_branch
		    ".($id_branch!='' ?" WHERE br.id_branch=".$id_branch."" :'')." ORDER by br.id_metalrate desc LIMIT 1";
		}
		else
		{
		    $sql="SELECT  m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,
		    Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime,m.goldrate_18ct,m.goldrate_14ct,m.goldrate_9ct
		    FROM metal_rates m
		    ORDER by m.id_metalrates desc LIMIT 1";
		}
		return $this->db->query($sql)->row_array();
	}
	function generateEstiNo($date,$id_branch)
	{
	  $sql = "SELECT max(esti_no) as lasEstiNo FROM ret_estimation WHERE date(estimation_datetime)='".$date."' ".($id_branch!='' ? " and id_branch=".$id_branch."" :'')." ORDER BY estimation_id DESC";
	  $lastno =  $this->db->query($sql)->row()->lasEstiNo;
	  if($lastno != NULL && $lastno != '')
		{
		  	$number = (int) $lastno;
		  	$number++;
			$code_number=str_pad($number, 4, '0', STR_PAD_LEFT);
    		return $code_number;
		}
		else
		{
			$code_number=str_pad('1', 4, '0', STR_PAD_LEFT);;
			return $code_number;
		}
	}
	/*function getOrderBySearch($SearchTxt,$branch)
    {
        $data = $this->db->query("SELECT cus.order_no as label,cus.order_no as value,e.id_product,e.design_no,e.wast_percent,product_name,
        e.mc,e.stn_amt,e.weight,e.size,e.size,e.totalitems,e.rate,des.design_name,
        pur.purity as purname,tax.tax_percentage,tax.tgi_calculation,
        pro.metal_type,mt.tgrp_id as tax_group_id,e.id_purity,e.stn_amt
        FROM customerorder cus
        left join customerorderdetails as e On e.id_customerorder=cus.id_customerorder
        LEFT JOIN ret_product_master as pro ON pro.pro_id = e.id_product
         LEFT JOIN ret_design_master as des ON des.design_no = e.design_no
        LEFT JOIN ret_purity as pur ON pur.id_purity = e.id_purity
        left join ret_category c on c.id_ret_category=pro.cat_id
        left join metal mt on mt.id_metal=c.id_metal
		LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,
		GROUP_CONCAT(m.tax_percentage) as tax_percentage,
		GROUP_CONCAT(i.tgi_calculation) as tgi_calculation
		FROM ret_taxgroupitems i
		LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=mt.tgrp_id
        WHERE cus.order_no LIKE '%".$SearchTxt."%' ".($branch!='' ? " and cus.order_from=".$branch."" :'')."
        and cus.est_id is null");
       //print_r($this->db->last_query());exit;
        return $data->result_array();
    }
	*/
	function getOrderBySearch($SearchTxt,$branch,$fin_year_code)
    {
    	$return_data=array();
        $data = $this->db->query("SELECT tag.tag_id as value,tag.tag_id, tag_code as label, tag_datetime, tag.tag_type, tag_lot_id,tag.tag_code as label, design_id, cost_center, tag.purity,IFNULL(tag.size,'') as size, uom, piece,
        IFNULL(tag.less_wt,'') as less_wt, tag.net_wt, tag.gross_wt, tag.calculation_based_on, retail_max_wastage_percent,tag_mc_value,tag_mc_type, halmarking, sales_value, tag.tag_status,
        product_name, product_short_code,tag.product_id, pur.purity as purname,tag.id_orderdetails,mt.id_metal,p.tgrp_id as tax_group_id,tag.tag_mc_type,c.order_no,r.rate_field,r.market_rate_field,tag_stn_detail.stn_amount as stn_amt,
        d.design_name, rtc.charge_value,sub.sub_design_name,tag.id_sub_design,concat(cust.firstname,'-',cust.mobile) as customer,cust.id_customer,
        IFNULL(tagOtherMetal.tag_other_itm_amount,0) as tag_other_itm_amount,cus.rate_per_gram as order_rate, CONCAT(s.value, ' ', s.name) as size_name,c.rate_type,IFNULL(sec.section_name,'') as section_name,IFNULL(tag.hu_id,'') as huid,tag.id_section,tag.current_branch,
        ifnull(tag.quality_id,'') as quality_id,p.stone_type,ifnull(tag.uom_gross_wt,'') as uom_id,ifnull(tag.stone_calculation_based_on,'') as stone_calculation_based_on
        FROM ret_taging tag
        LEFT JOIN customerorderdetails cus ON cus.id_orderdetails=tag.id_orderdetails
        LEFT JOIN customerorder c ON c.id_customerorder=cus.id_customerorder
        LEFT JOIN customer cust on cust.id_customer=c.order_to
        LEFT JOIN ret_product_master p on p.pro_id=tag.product_id
        LEFT JOIN ret_design_master d on d.design_no=tag.design_id
        left join ret_sub_design_master sub  on sub.id_sub_design=tag.id_sub_design
        LEFT JOIN ret_section sec ON sec.id_section = tag.id_section
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
        LEFT JOIN metal mt on mt.id_metal=cat.id_metal
        LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity
        LEFT JOIN ret_size s on s.id_size=tag.size
        LEFT JOIN ret_metal_purity_rate r on r.id_metal=cat.id_metal and r.id_purity=tag.purity
        LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(wt) as stn_wt,sum(certification_cost) as certification_cost
        FROM `ret_taging_stone`
        GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = tag.tag_id
		LEFT JOIN (SELECT tag_id, SUM(IFNULL(charge_value,0)) AS charge_value FROM ret_taging_charges GROUP BY tag_id) AS rtc ON rtc.tag_id = tag.tag_id
		LEFT JOIN (SELECT tag_other_itm_tag_id, SUM(IFNULL(tag_other_itm_amount,0)) AS tag_other_itm_amount FROM ret_tag_other_metals GROUP BY tag_other_itm_tag_id) AS tagOtherMetal ON tagOtherMetal.tag_other_itm_tag_id = tag.tag_id
		WHERE c.order_no='".$SearchTxt."' and cus.orderstatus=4 and tag.tag_status=0
		and c.fin_year_code=".$fin_year_code."
		".($branch!='' ? " and c.order_from=".$branch."" :'')."
		".($branch!='' ? " and tag.current_branch=".$branch."" :'')."
		AND cus.id_orderdetails is NOT null");
        //print_r($this->db->last_query());exit;
        $return_data=$data->result_array();
        foreach($return_data as $rkey => $rval){
            $return_data[$rkey]['stone_details'] = $this->getTagStoneDetails($rval['value']);
            $return_data[$rkey]['other_metal_details'] = $this->get_other_metal_details($rval['value']);
            $return_data[$rkey]['mc_va_limit']   = $this->get_mc_va_limit($rval['current_branch'],$rval['product_id'], $rval['design_id'], $rval['id_sub_design']);
			$returndata[$rkey]['po_details'] = $this->get_purchase_details($rval['product_id'], $rval['design_id'], $rval['id_sub_design'], $rval['tag_lot_id']);
            $return_data[$rkey]['charges_details'] = $this->get_charges($rval['value']);
            $return_data[$rkey]['tag_images'] = $this->getTagImageDetails($rval['value']);
        }
        return $return_data;
    }
     function advance_details_order_no($orderno,$id_branch,$fin_year_code)
	{
	    $return_data=array();
	    if($orderno!=null && $orderno!='')
	    {
	       $advance=$this->db->query("SELECT a.order_no,a.advance_amount as paid_advance, a.advance_weight as paid_weight,s.metal_type,a.store_as,a.advance_type,a.rate_calc,a.rate_per_gram
            FROM customerorder c
            LEFT JOIN ret_billing_advance a ON a.id_customerorder=c.id_customerorder
            LEFT JOIN ret_billing b ON b.bill_id=a.bill_id
            LEFT JOIN ret_bill_old_metal_sale_details s ON s.old_metal_sale_id=a.old_metal_sale_id
			where c.fin_year_code=".$fin_year_code." and a.is_adavnce_adjusted=0 and b.bill_status=1 and a.order_no='".$orderno."'
			".($id_branch!='' ? " and b.id_branch=".$id_branch."" :'')."");
	        //print_r($this->db->last_query());exit;
			$return_data=$advance->result_array();
	    }
	    return $return_data;
	}
    function get_order_details($order_no,$id_branch,$fin_year)
    {
    	$sql=$this->db->query("SELECT sum(od.totalitems) as tot_pcs,c.order_no
		FROM customerorder c
		LEFT JOIN customerorderdetails od ON od.id_customerorder=c.id_customerorder
		WHERE c.fin_year_code=".$fin_year." and od.orderstatus=4 and c.order_no='".$order_no."' ".($id_branch!='' ? "and c.order_from=".$id_branch."" :'')." ");
		//print_r($this->db->last_query());exit;
		return $sql->row()->tot_pcs;
    }
    function order_details($order_no)
    {
    	$sql=$this->db->query("SELECT sum(od.totalitems) as tot_pcs,c.order_no
		FROM customerorder c
		LEFT JOIN customerorderdetails od ON od.id_customerorder=c.id_customerorder
		WHERE c.order_no='".$order_no."'");
		//print_r($this->db->last_query());exit;
		return $sql->row_array();
    }
    function get_tag_details($tag_id)
    {
    	$sql=$this->db->query("SELECT
    		IFNULL(SUM(tag.gross_wt),0) as tot_gwt,
    		IFNULL(SUM(tag.net_wt),0) as tot_nwt,
    		IFNULL(SUM(tag.less_wt),0) as tot_less_wt,
    		IFNULL(SUM(s.sold_gross_wt),0) as tot_sold_gwt,
    		IFNULL(SUM(s.sold_less_wt),0) as tot_sold_lwt,
    		IFNULL(SUM(s.sold_net_wt),0) as tot_sold_nwt
		FROM ret_taging tag
		LEFT JOIN ret_partlysold s on s.tag_id=tag.tag_id
		where tag.tag_id=".$tag_id."");
		//print_r($this->db->last_query());exit;
		return $sql->row_array();
    }
    function get_partial_details()
    {
    	$data = $this->db->query("SELECT tag.tag_id as value,
        tag_code as label, tag_datetime, tag.tag_type, tag_lot_id,
        design_id, cost_center, tag.purity, tag.size, uom, piece, tag.less_wt, tag.net_wt, tag.gross_wt,
        tag.calculation_based_on, retail_max_wastage_percent,tag_mc_value,tag_mc_type,
        halmarking, sales_value, tag.tag_status, product_name, product_short_code, lot_product,
        pur.purity as purname,lot_inw.lot_received_at,
        ifnull(tag_stn_detail.stn_amount,if(tag.id_orderdetails!='',ord.stn_amt,0)) as stone_price,IFNULL(tag_stn_detail.certification_cost,0) as certification_cost,
        tag.tag_id,pro.sales_mode,tag.item_rate,tax.tax_percentage,tax.tgi_calculation,pro.metal_type,tag.current_branch,
        pro.tgrp_id as tax_group_id,IFNULL(tag.id_orderdetails,'') as id_orderdetails,
        IFNULL(lot_inw.order_no,'') as order_no,IFNULL(tag.old_tag_id,'') as old_tag_id
        FROM ret_taging as tag
        Left join ret_lot_inwards_detail lot_det ON tag.id_lot_inward_detail = lot_det.id_lot_inward_detail
        LEFT JOIN ret_lot_inwards as lot_inw ON lot_inw.lot_no = lot_det.lot_no
        LEFT JOIN ret_product_master as pro ON pro.pro_id = lot_det.lot_product
        LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity
        left join ret_category c on c.id_ret_category=pro.cat_id
        left join metal mt on mt.id_metal=c.id_metal
        left join customerorderdetails ord on ord.id_orderdetails=tag.id_orderdetails
        LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(wt) as stn_wt,sum(certification_cost) as certification_cost
        FROM `ret_taging_stone`
        GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = tag.tag_id
        WHERE tag.current_branch=".$branch." AND tag.".$searchField." LIKE '%".$SearchTxt."%'");
    }
     function getPartialTagSearch_1($SearchTxt,$searchField,$branch)
    {
		$data=$this->db->query("SELECT tag.tag_id as value,
         tag_datetime,prod.tgrp_id as tax_group_id,
        (tag.gross_wt-IFNULL(py.sold_gross_wt,0)) as gross_wt,(tag.net_wt-IFNULL(py.sold_net_wt,0)) as net_wt,
        prod.product_name,prod.product_short_code,pur.purity as purname,prod.metal_type,
        prod.calculation_based_on,tag.product_id,tag.design_id,tag.purity,
        tag.size,tag.piece,tag.tag_mc_type,tag.tag_mc_value,tag.retail_max_wastage_percent,
        tag.item_rate,tag.current_branch,des.design_name,tag.design_id,
        r.rate_field,r.market_rate_field,sub.sub_design_name, tag.id_sub_design,IFNULL(tag.old_tag_id,'') as old_tag_id,tag.tag_code
        ,tag.tag_code as label
		from ret_taging tag
        LEFT JOIN (SELECT IFNULL(SUM(p.sold_gross_wt),0) as sold_gross_wt,IFNULL(SUM(p.sold_net_wt),0) as sold_net_wt,d.tag_id
        FROM ret_partlysold p
        LEFT JOIN ret_bill_details d ON d.bill_det_id = p.sold_bill_det_id
        LEFT JOIN ret_billing b ON b.bill_id = d.bill_id
        WHERE b.bill_status = 1
        GROUP BY p.tag_id) as py ON py.tag_id = tag.tag_id
		LEFT JOIN ret_product_master prod on prod.pro_id = tag.product_id
		LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity
	    left join ret_category c on c.id_ret_category=prod.cat_id
	    LEFT JOIN ret_design_master as des ON des.design_no = tag.design_id
	    left join ret_sub_design_master sub  on sub.id_sub_design=tag.id_sub_design
	    left join metal mt on mt.id_metal=c.id_metal
	    LEFT JOIN ret_metal_purity_rate r on r.id_metal=c.id_metal and r.id_purity=tag.purity
		WHERE tag.current_branch=".$branch." AND tag.".$searchField." LIKE '%".$SearchTxt."%'
		and tag.is_partial = 1 and (tag.tag_status = 6 OR tag.tag_status = 1) Having gross_wt > 0");
        /*$data = $this->db->query("SELECT tag.tag_id as value,
        tag_code as label, tag_datetime, tag.tag_type, tag_lot_id,
        design_id, cost_center, tag.purity, tag.size, uom, piece, tag.less_wt, tag.net_wt, tag.gross_wt,
        tag.calculation_based_on, retail_max_wastage_percent,tag_mc_value,tag_mc_type,
        halmarking, sales_value, tag.tag_status, product_name, product_short_code, lot_product,
        pur.purity as purname,lot_inw.lot_received_at,
        ifnull(tag_stn_detail.stn_amount,if(tag.id_orderdetails!='',ord.stn_amt,0)) as stone_price,IFNULL(tag_stn_detail.certification_cost,0) as certification_cost,
        tag.tag_id,pro.sales_mode,tag.item_rate,tax.tax_percentage,tax.tgi_calculation,pro.metal_type,tag.current_branch,
        mt.tgrp_id as tax_group_id,IFNULL(tag.id_orderdetails,'') as id_orderdetails,
        IFNULL(lot_inw.order_no,'') as order_no
        FROM ret_taging as tag
        LEFT JOIN ret_partlysold t on t.tag_id=tag.tag_id
        Left join ret_lot_inwards_detail lot_det ON tag.id_lot_inward_detail = lot_det.id_lot_inward_detail
        LEFT JOIN ret_lot_inwards as lot_inw ON lot_inw.lot_no = lot_det.lot_no
        LEFT JOIN ret_product_master as pro ON pro.pro_id = lot_det.lot_product
        LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity
        left join ret_category c on c.id_ret_category=pro.cat_id
        left join metal mt on mt.id_metal=c.id_metal
        left join customerorderdetails ord on ord.id_orderdetails=tag.id_orderdetails
		LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,
		GROUP_CONCAT(m.tax_percentage) as tax_percentage,
		GROUP_CONCAT(i.tgi_calculation) as tgi_calculation
		FROM ret_taxgroupitems i
		LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=mt.tgrp_id
        LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(wt) as stn_wt,sum(certification_cost) as certification_cost
        FROM `ret_taging_stone`
        GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = tag.tag_id
        WHERE tag.current_branch=".$branch." AND t.".$searchField." LIKE '%".$SearchTxt."%'");*/
     // print_r($this->db->last_query());exit;
        $returnarray =  $data->result_array();
        foreach($returnarray as $rkey => $rval){
            $returnarray[$rkey]['stone_details'] = $this->get_partly_home_bill_stones($rval['value']);
			$return_data[$rkey]['mc_va_limit']      = $this->get_mc_va_limit($rval['current_branch'],$rval['product_id'], $rval['design_id'], $rval['id_sub_design']);
            $returndata[$rkey]['po_details']        = $this->get_purchase_details($rval['product_id'], $rval['design_id'], $rval['id_sub_design'], $rval['tag_lot_id']);
        }
        return $returnarray;
    }
     function getPartialTagSearch($SearchTxt,$searchField,$branch)
    {
		$data=$this->db->query("SELECT tag.tag_id as value,
         tag_datetime,prod.tgrp_id as tax_group_id,
        (tag.gross_wt-IFNULL(py.sold_gross_wt,0)) as gross_wt,(tag.net_wt-IFNULL(py.sold_net_wt,0)) as net_wt,
        prod.product_name,prod.product_short_code,pur.purity as purname,prod.metal_type,
        prod.calculation_based_on,tag.product_id,tag.design_id,tag.purity,
        tag.size,tag.piece,tag.tag_mc_type,tag.tag_mc_value,tag.retail_max_wastage_percent,
        tag.item_rate,tag.current_branch,des.design_name,tag.design_id,
        r.rate_field,r.market_rate_field,sub.sub_design_name, tag.id_sub_design,IFNULL(tag.old_tag_id,'') as old_tag_id,tag.tag_code
        ,".($searchField == 'old_tag_id'  ? 'tag.old_tag_id as label':'tag.tag_code as label')."
		from ret_taging tag
        LEFT JOIN (SELECT IFNULL(SUM(p.sold_gross_wt),0) as sold_gross_wt,IFNULL(SUM(p.sold_net_wt),0) as sold_net_wt,d.tag_id
        FROM ret_partlysold p
        LEFT JOIN ret_bill_details d ON d.bill_det_id = p.sold_bill_det_id
        LEFT JOIN ret_billing b ON b.bill_id = d.bill_id
        WHERE b.bill_status = 1
        GROUP BY p.tag_id) as py ON py.tag_id = tag.tag_id
		LEFT JOIN ret_product_master prod on prod.pro_id = tag.product_id
		LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity
	    left join ret_category c on c.id_ret_category=prod.cat_id
	    LEFT JOIN ret_design_master as des ON des.design_no = tag.design_id
	    left join ret_sub_design_master sub  on sub.id_sub_design=tag.id_sub_design
	    left join metal mt on mt.id_metal=c.id_metal
	    LEFT JOIN ret_metal_purity_rate r on r.id_metal=c.id_metal and r.id_purity=tag.purity
		WHERE tag.current_branch=".$branch." AND tag.".$searchField." LIKE '%".$SearchTxt."%'
		and tag.is_partial = 1 and (tag.tag_status = 6 OR tag.tag_status = 1) Having gross_wt > 0");
        $returnarray =  $data->result_array();
        foreach($returnarray as $rkey => $rval){
            $returnarray[$rkey]['stone_details'] = $this->get_partly_home_bill_stones($rval['value']);
			$return_data[$rkey]['mc_va_limit']      = $this->get_mc_va_limit($rval['current_branch'],$rval['product_id'], $rval['design_id'], $rval['id_sub_design']);
            $returndata[$rkey]['po_details']        = $this->get_purchase_details($rval['product_id'], $rval['design_id'], $rval['id_sub_design'], $rval['tag_lot_id']);
        }
        return $returnarray;
    }
    function get_partly_home_bill_stones($tag_id)
    {
        $sql = $this->db->query("SELECT ts.is_apply_in_lwt, ts.stone_id, ts.uom_id, ts.rate_per_gram, st.stone_type, ts.stone_cal_type,
                                (ts.pieces - ((SELECT ifnull(sum(eis.pieces),0)
                                FROM ret_estimation_item_stones as eis
                                LEFT JOIN ret_estimation_items as esi ON esi.est_item_id = eis.est_item_id
                                WHERE purchase_status = 1 AND esi.tag_id = ts.tag_id and eis.stone_id = ts.stone_id))) as pieces ,
                                (ts.wt - ((SELECT ifnull(sum(eis.wt),0)
                                FROM ret_estimation_item_stones as eis
                                LEFT JOIN ret_estimation_items as esi ON esi.est_item_id = eis.est_item_id
                                WHERE purchase_status = 1 AND esi.tag_id = ts.tag_id and eis.stone_id = ts.stone_id))) as wt,
                                (ts.amount - ((SELECT ifnull(sum(eis.price),0)
                                FROM ret_estimation_item_stones as eis
                                LEFT JOIN ret_estimation_items as esi ON esi.est_item_id = eis.est_item_id
                                WHERE purchase_status = 1 AND esi.tag_id = ts.tag_id and eis.stone_id = ts.stone_id))) as amount
                                FROM `ret_taging_stone` as ts
                                LEFT JOIN ret_stone as st ON st.stone_id =  ts.stone_id
                                WHERE ts.tag_id = '".$tag_id."' GROUP BY stone_id");
        return $sql->result_array();
    }
    function get_old_metal_type($id_metal)
    {
        $sql=$this->db->query("SELECT metal_type,id_metal_type FROM ret_old_metal_type where id_metal=".$id_metal);
        return $sql->result_array();
    }
	function get_old_metal_category()
    {
        $sql=$this->db->query("SELECT id_old_metal_cat,id_metal,id_old_metal_type,old_metal_cat,old_metal_perc,old_metal_discount,touch_from,touch_to  FROM ret_old_metal_category WHERE is_active = 1");
        return $sql->result_array();
    }
	function get_metal_purity_rate($data)
    {
        $sql=$this->db->query("SELECT * FROM `ret_metal_purity_rate` where id_metal=".$data['id_metal']." and id_purity=".$data['id_purity']."");
        return $sql->row_array();
    }
    //CUstomer Purchase Details
    function getCustomerDet($id_branch='',$id_customer='')
	{
	    $return_data=array();
        $financial_year =$this->GetActiveFinancialYear();
		$data=$this->db->query("SELECT c.firstname,c.id_customer,c.is_vip,c.mobile,v.village_name,b.name as branch_name,IFNULL(esti.tot_est,0) as estimation_no,
        IFNULL(bill_tot.bill_count,0) as bill_count,IFNULL(tot_acc.tot_acc,0) as tot_account,IFNULL(iactive.tot_acc,0)as inactive_acount,
        IFNULL(tot_gold.gold_wt,0) as gold_wt,IFNULL(tot_silver.silver_wt,0) as silver_wt,IFNULL(closed_chit.closed_count,0) as closed_count,IFNULL(tot_payment.pay_amount,0) as tot_amount,
        IFNULL(fixed_rate.item_cost,0) as tot_fixed_rate,IFNULL(active_acc.tot_acc,0) as active_acc,IFNULL(DATE_FORMAT(max(bill.bill_date),'%d-%m-%Y'),'-') as last_billdate,z.name as zone_name,addr.id_country,addr.id_state,addr.address1,addr.pincode,b.short_name as brn_short_name
        FROM customer c
        left join address addr on (c.id_customer=addr.id_customer)
        LEFT JOIN ret_billing bill ON bill.bill_cus_id = c.id_customer and bill.fin_year_code=".($financial_year['fin_year_code'])."
        left join branch b on (b.id_branch=c.id_branch)
        LEFT JOIN village v on (v.id_village=c.id_village)
        LEFT JOIN village_zone z on (z.id_zone=v.id_zone)
        left join (select count(est.estimation_id) as tot_est,est.cus_id from ret_estimation est
                   left join customer as c on c.id_customer=est.cus_id where cus_id=".$id_customer.")as esti on esti.cus_id=c.id_customer
        left join (select COUNT(bill.bill_id) as bill_count,bill.bill_cus_id from ret_billing as bill
            left join customer as c on c.id_customer=bill.bill_cus_id
            where bill.bill_status=1 and c.id_customer=".$id_customer." and bill.fin_year_code=".($financial_year['fin_year_code'])."
            ) as bill_tot on bill_tot.bill_cus_id=c.id_customer
        left join(select count(sa.id_scheme_account) as tot_acc,sa.id_customer,c.mobile from scheme_account sa
            left join customer c on (c.id_customer=sa.id_customer)
            where sa.scheme_acc_number is not null and c.id_customer=".$id_customer.") as tot_acc on tot_acc.id_customer=c.id_customer
        left join(select count(sa.id_scheme_account) as tot_acc,sa.id_customer,c.mobile from scheme_account sa
            left join customer c on (c.id_customer=sa.id_customer)
            where sa.scheme_acc_number is not null and sa.is_closed=0 and c.id_customer=".$id_customer.") as active_acc on active_acc.id_customer=c.id_customer
        left join(select count(sa.scheme_acc_number) as closed_count,sa.id_customer,c.mobile from scheme_account sa
            left join customer c on (c.id_customer=sa.id_customer)
            where sa.is_closed=1 and c.id_customer=".$id_customer.") as closed_chit on closed_chit.id_customer=c.id_customer
        left join (SELECT COUNT(sa.id_scheme_account) as tot_acc,
            TIMESTAMPDIFF(month, max(p.date_add), current_date()) as month_ago,sa.id_customer
            FROM scheme_account sa
            LEFT JOIN payment p ON p.id_scheme_account=sa.id_scheme_account
            LEFT JOIN scheme s ON s.id_scheme=sa.id_scheme
            LEFT JOIN customer cus ON cus.id_customer=sa.id_customer
            WHERE sa.is_closed=0
            and cus.id_customer=".$id_customer." HAVING month_ago>3) as iactive on iactive.id_customer=c.id_customer
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
            and c.id_customer=".$id_customer." and bill.fin_year_code=".($financial_year['fin_year_code'])."
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
            where bill.bill_status=1 and m.id_metal=2 and c.id_customer=".$id_customer." and bill.fin_year_code=".($financial_year['fin_year_code'])."
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
            where bill.bill_status=1 and pro.sales_mode=1 and c.id_customer=".$id_customer." and bill.fin_year_code=".($financial_year['fin_year_code'])."
        	) as fixed_rate on fixed_rate.id_customer=c.id_customer
        left join(select sum(p.payment_amount) as pay_amount,c.id_customer,c.mobile from customer as c
            left join scheme_account as sa on(sa.id_customer=c.id_customer)
            left join payment as p on (p.id_scheme_account=sa.id_scheme_account)
            where c.id_customer=".$id_customer.") as tot_payment on tot_payment.id_customer=c.id_customer
            where c.id_customer IS NOT NULL
            and c.id_customer=".$id_customer."");
			// print_r($this->db->last_query());exit;
		$return_data['cus_details']=$data->result_array();
		$sql=$this->db->query("SELECT b.bill_id,br.name as branch_name,IFNULL(g_wt.net_wt,0) as gold_wt,IFNULL(s_wt.net_wt,0) as silver_wt,IFNULL(fixed_rate.item_cost,0) as mrp_amount,DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date,b.tot_bill_amount,if(b.bill_status=1,'Success','Cancelled') as bill_status,br.short_name as brn_short_name
        FROM ret_billing b
        LEFT JOIN customer c ON c.id_customer=b.bill_cus_id
        LEFT JOIN ret_bill_details d ON d.bill_id=b.bill_id
		LEFT JOIN branch br on br.id_branch=b.id_branch
        left JOIN (select sum(d.net_wt) as net_wt,b.bill_id from ret_billing b
                  left join customer c ON c.id_customer=b.bill_cus_id
                  left JOIN ret_bill_details d ON d.bill_id=b.bill_id
                  left join ret_product_master as pro on pro.pro_id=d.product_id
            	  left join ret_category as cat on cat.id_ret_category=pro.cat_id
            	  left join metal as m on m.id_metal=cat.id_metal WHERE b.bill_status=1
                  and m.id_metal=1 and d.bill_det_id is NOT NULL and c.id_customer=".$id_customer."
                  GROUP by b.bill_id ) as g_wt ON g_wt.bill_id=b.bill_id
          left JOIN (select sum(d.net_wt) as net_wt,b.bill_id from ret_billing b
                  left join customer c ON c.id_customer=b.bill_cus_id
                  left JOIN ret_bill_details d ON d.bill_id=b.bill_id
                  left join ret_product_master as pro on pro.pro_id=d.product_id
            	  left join ret_category as cat on cat.id_ret_category=pro.cat_id
            	  left join metal as m on m.id_metal=cat.id_metal WHERE b.bill_status=1
                  and m.id_metal=2 and d.bill_det_id is NOT NULL and c.id_customer=".$id_customer."
                  GROUP by b.bill_id ) as s_wt ON s_wt.bill_id=b.bill_id
          left JOIN (select sum(d.item_cost) as item_cost,b.bill_id from ret_billing b
                  left join customer c ON c.id_customer=b.bill_cus_id
                  left JOIN ret_bill_details d ON d.bill_id=b.bill_id
                  left join ret_product_master as pro on pro.pro_id=d.product_id
            	  left join ret_category as cat on cat.id_ret_category=pro.cat_id
            	  left join metal as m on m.id_metal=cat.id_metal WHERE b.bill_status=1
                  and pro.sales_mode=1 and d.bill_det_id is NOT NULL and c.id_customer=".$id_customer."
                  GROUP by b.bill_id ) as fixed_rate ON fixed_rate.bill_id=b.bill_id
        WHERE c.id_customer=".$id_customer."  and b.fin_year_code=".($financial_year['fin_year_code'])." and b.bill_status = 1
        GROUP by b.bill_id ORDER BY b.bill_date DESC LIMIT 5");
        $res=$sql->result_array();
        $return_data['bill_details']=array();
        foreach($res as $r){
            $r['bill_no'] = $this->get_bill_no_format_detail($r['bill_id']);
            $return_data['bill_details'][]=$r;
        }

		$outstanding = $this->get_credit_pending_details($id_branch, $id_customer);
		$return_data['outstanding'] = $this->get_credit_pending_details($id_branch, $id_customer);
        $return_data['receipt_details'] = $this->get_billing_advance_details($id_branch, $id_customer);
        $return_data['order_advance'] = $this->get_order_advance_details($id_branch, $id_customer);


        foreach ($outstanding as $key => $value) {
            $credit_col = 0;
            foreach ($value['credit_collection'] as $key => $cc_amt) {
                $credit_col += $cc_amt['tot_amt_received'];
            }
           $return_data['bal_amt'] += $value['bal_amt'];
           $return_data['tot_bill_amount'] += $value['tot_bill_amount'];
           $return_data['credit_collection'] += $credit_col;
        }
        return $return_data;
	}
    //CUstomer Purchase Details
    function get_other_estcharges($est_item_id)
	{
		$sql = $this->db->query("
		SELECT e.id_charge,e.amount,s.code_charge,e.amount as charge_value
        FROM ret_estimation_other_charges e
        LEFT JOIN ret_charges s on s.id_charge=e.id_charge
	    WHERE e.est_item_id=".$est_item_id."");
	    return $sql->result_array();
	}
	function getProductSubDesignBySearch($SearchTxt, $procode,$id_branch)
    {
        $sql=$this->db->query("SELECT p.id_sub_design as value,p.sub_design_name as label
        FROM ret_sub_design_master p
        where p.sub_design_name LIKE ?",
        array('%'.$SearchTxt.'%'));
        return $sql->result_array();
    }
	function get_mc_va_limit($id_branch,$id_product, $id_design, $id_sub_design) {
        $mc_min = 0;
        $wastag_min = 0;
		$margin_mrp = 0;
        $sql = "SELECT
                    IFNULL(mc_min,0) AS mc_min,
                    IFNULL(wastag_min ,0) AS wastag_min,
					IFNULL(margin_mrp ,0) AS margin_mrp
                FROM ret_selling_settings
                WHERE id_branch = ? AND id_product = ? AND id_design = ? AND id_sub_design = ?";
        //echo $sql;exit;
        $query_details = $this->db->query($sql, array($id_branch, $id_product, $id_design, $id_sub_design));
        if ($query_details->num_rows() > 0)
        {
            $row = $query_details->row_array();
            $mc_min = $row['mc_min'];
            $wastag_min = $row['wastag_min'];
			$margin_mrp = $row['margin_mrp'];
        }
        $result_arr = array("mc_min" => $mc_min, "wastag_min" => $wastag_min, "margin_mrp" => $margin_mrp);
		return $result_arr;
	}
    /**
	 * Getting purchase details from tagging model.
	 *
	 * Created By : Vivek. Created On : 29-08-2022
	 *
	 */
	function get_purchase_details($product_id, $design_id, $subdesign_id, $lot_no) {
		$pur_details = array();
		if($product_id > 0 && $design_id > 0 && $subdesign_id > 0 && $lot_no > 0) {
			$data['product_id'] 	= $product_id;
			$data['design_id'] 		= $design_id;
			$data['subdesign_id'] 	= $subdesign_id;
			$data['lot_no'] 		= $lot_no;
			$CI = & get_instance();
         	$CI->load->model('ret_tag_model');
			$pur_details = $CI->ret_tag_model->getPoDetailsforPC($data);
		}
		return $pur_details;
	}
    function get_purity_rate()
    {
        $sql=$this->db->query("SELECT * FROM `ret_metal_purity_rate`");
        return $sql->result_array();
    }
    function getCompanyDetails($id_branch)
	{
		if($id_branch=='')
		{
			$sql = $this->db->query("Select  c.id_company,c.company_name,c.gst_number,c.short_code,c.pincode,c.mobile,c.whatsapp_no,c.phone,c.email,c.website,
			c.address1,c.address2,c.id_country,c.id_state,c.id_city,ct.name as city,s.name as state,cy.name as country,cs.currency_symbol,
			cs.currency_name,cs.mob_code,cs.mob_no_len,c.mail_server,c.mail_password,c.send_through,c.mobile1,
			c.phone1,c.smtp_user,c.smtp_pass,c.smtp_host,c.server_type,cs.login_branch,
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
				cy.name as country,ct.name as city,s.name as state,b.pincode,s.id_state,s.state_code, c.gst_number
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
	function get_ActiveProduct()
	{
	    $sql = $this->db->query("SELECT * FROM `ret_product_master` WHERE product_status = 1");
	    return $sql->result_array();
	}
	function get_taggedorder_details($id_orderdetails)
	{
		$sql=$this->db->query("SELECT tag_id,tag_code,id_orderdetails from ret_taging
		where id_orderdetails=".$id_orderdetails."");
		return $sql->result_array();
	}
	function get_non_tag_stock_details($data)
    {
        $sql = $this->db->query("SELECT rn.no_of_piece,rn.gross_wt
        FROM  ret_nontag_item rn
        where rn.product is not null
        AND rn.id_section = ?
        AND rn.product = ?
        AND rn.branch = ?
        AND rn.design = ?
        AND rn.id_sub_design = ?",
        array($data['id_section'], $data['id_product'], $data['id_branch'], $data['id_design'], $data['id_sub_design']));
        return $sql->row_array();
    }
   function advance_details_order_details($orderno,$id_branch)
	{
	    $return_data=array();
	    if($orderno!=null && $orderno!='')
	    {
	        $advance=$this->db->query("SELECT a.order_no,sum(a.advance_amount) as paid_advance, a.advance_weight as paid_weight,s.metal_type,a.store_as,a.advance_type,a.rate_calc,a.rate_per_gram
            FROM customerorder c
            LEFT JOIN ret_billing_advance a ON a.id_customerorder=c.id_customerorder
			LEFT JOIN ret_estimation_items r ON r.orderno=c.order_no
            LEFT JOIN ret_billing b ON b.bill_id=a.bill_id
            LEFT JOIN ret_bill_old_metal_sale_details s ON s.old_metal_sale_id=a.old_metal_sale_id
			where a.is_adavnce_adjusted=0 and b.bill_status=1 and a.order_no='".$orderno."'
			".($id_branch!='' ? " and b.id_branch=".$id_branch."" :'')."
            GROUP by c.id_customerorder");
	        //print_r($this->db->last_query());exit;
			$return_data=$advance->result_array();
	    }
	    return $return_data;
	}
    function get_chit_details($est_id){
		$data = $this->db->query("SELECT sa.bonus_percent,rn.scheme_account_id,c.mobile, sa.id_scheme_account as value, sa.id_scheme_account as label,
		sa.closing_balance, sa.is_closed, s.scheme_type, s.scheme_name, ifnull(s.flexible_sch_type, 0) as flexible_sch_type,
		sa.closing_amount as closing_amount, sa.closing_add_chgs, pay.paid_installments, s.total_installments, s.firstPayDisc_value, s.scheme_type,
		IFNULL(pay.paid_amount,0) as paid_amount,rn.closing_weight,rn.utl_amount,rn.wastage_per,rn.savings_in_wastage,rn.mc_value,rn.savings_in_making_charge,
		IFNULL(sa.additional_benefits,0) as additional_benefits, IFNULL(sa.benefit,0) as sch_benefit, IFNULL(pay.cash_pay, 0) as cash_pay,s.is_wast_and_mc_benefit_apply,s.is_topup_scheme
                FROM ret_est_chit_utilization rn 
                left join scheme_account sa on sa.id_scheme_account =rn.scheme_account_id
				left join customer c on c.id_customer=sa.id_customer
				left join scheme s on s.id_scheme = sa.id_scheme
				LEFT JOIN (select SUM(p.payment_amount) as paid_amount, IFNULL(cp.cash_pay,0) as cash_pay,sa.id_scheme_account,
				IFNULL(IF(sa.is_opening = 1,
				            IFNULL(sa.paid_installments,0) + IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0),
				            if((s.scheme_type = 1 OR s.scheme_type = 3) and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0) as paid_installments
				FROM payment p
				LEFT JOIN (SELECT SUM(IFNULL(pmd.payment_amount,0)) AS cash_pay, id_payment FROM `payment_mode_details` AS pmd WHERE pmd.payment_mode = 'CSH' AND pmd.payment_status = 1 GROUP BY id_payment) AS cp ON cp.id_payment = p.id_payment
				left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
				left join scheme s on s.id_scheme=sa.id_scheme
				where p.payment_status=1 GROUP BY p.id_scheme_account) as pay on pay.id_scheme_account = sa.id_scheme_account
				WHERE sa.id_scheme_account and sa.is_closed = 1 and sa.is_utilized = 0 and rn.est_id ='".$est_id."'
                GROUP by rn.chit_ut_id");
		//echo $this->db->last_query();exit;
		return $data->result_array();
	}
    function get_est_tag_details($est_id,$isTagsplitted)
    {
        if($isTagsplitted > 0){

            $items_query = $this->db->query("SELECT est.isTagsplitted,est.est_item_id, esti_id, item_type, item_emp_id,c.scheme_closure_benefit,
            est.product_id, est.tag_id, est.design_id, est.purity as purity, est.size, est.uom, tag.piece,est.id_sub_design,
            IFNULL(est.less_wt,0) as less_wt, est.net_wt, est.gross_wt,IFNULL(co.order_no,'') as order_no,
            tag.calculation_based_on, tag.retail_max_wastage_percent as tagmaxva,
            est.wastage_percent, tag.tag_mc_value, est.mc_value as mc_value, est.mc_type as mc_type,
            item_cost, product_short_code,product_name as product_name,tag.sell_rate as sale_value,
            concat(design_name,IFNULL(concat('-',design_code),'')) as design_name, design_name as design, design_code, pur.purity as purname,
            tax.tax_percentage,tax.tgi_calculation,est.discount,e.created_by,s.disc_limit,s.disc_limit_type,e.id_branch,est.mc_type as mc_type,est.is_non_tag,est.is_partial,est.lot_no,pro.sales_mode,tag.item_rate,
            est.item_total_tax,est.market_rate_cost, tag.gross_wt as tag_gross_wt, tag.net_wt as tag_net_wt,txgrp.tgrp_name,tag.tag_code,IFNULL(est.id_orderdetails,'') as id_orderdetails, rtc.charge_value,
            sub.sub_design_name,mt.metal as metal_name,IFNULL(est.est_rate_per_grm,0) as est_rate_per_grm,c.id_metal as metal_type,c.is_916,c.name as cat_name,
            IFNULL(concat(sz.value,'',sz.name),'') as size,sz.id_size,pro.tgrp_id as tax_group_id,IFNULL(tag.tag_purchase_cost,0) as tag_purchase_cost,r.rate_field,r.market_rate_field, pro.tax_type, IFNULL(est_stn_detail.stone_price,0) AS stone_price, tag.sell_rate as sales_value,IFNULL(sec.section_name,'') as section_name,IFNULL(tag.hu_id,'') as huid,tag.id_section,ifnull(tag.quality_id,'') as quality_id,pro.stone_type,ifnull(tag.uom_gross_wt,'') as uom_id,
            d.rate_per_gram as order_rate_per_grm,co.rate_type as ord_rate_type,
            ifnull(tag.stone_calculation_based_on,'') as stone_calculation_based_on,pro.cat_id,c.id_metal,IFNULL(other_metal.metal_value,0) AS tag_other_itm_amount,

            IFNULL(est.max_va_per,0.00) AS max_va_per,IFNULL(est.wastage_slab_id,'') AS wastage_slab_id,CONCAT(wtg.from_range, ' TO ',wtg.to_range) AS text_range,
            IFNULL(wtg.id_metal,'') AS id_metal,IFNULL(wtg.value,'') AS value,est.tag_blk_disc AS tag_blk_disc,IFNULL(est.act_wast_per,0) as act_wast_per,
            est.stone_amount AS stn_amt,est.diamond_amount AS dia_amt,IFNULL(est.max_mc,0) AS max_mc,est.wast_slab_value as slb_val,IFNULL(est.is_split_row,0) as is_split_row

            FROM ret_estimation_items as est
            LEFT JOIN customerorderdetails d ON d.id_orderdetails=est.id_orderdetails
            LEFT JOIN customerorder co ON co.id_customerorder=d.id_customerorder
            LEFT join ret_estimation e on e.estimation_id=est.esti_id
            LEFT JOIN ret_product_master as pro ON pro.pro_id = est.product_id
            LEFT JOIN ret_design_master as des ON des.design_no = est.design_id
            LEFT JOIN ret_taging as tag ON tag.tag_id = est.tag_id
            LEFT JOIN ret_sub_design_master as sub ON sub.id_sub_design = est.id_sub_design
            LEFT JOIN ret_section sec ON sec.id_section = tag.id_section
            LEFT JOIN ret_purity as pur ON pur.id_purity = est.purity
            LEFT JOIN ret_category c on c.id_ret_category=pro.cat_id
            LEFT JOIN metal mt on mt.id_metal=c.id_metal
            LEFT JOIN ret_metal_purity_rate r on r.id_metal= mt.id_metal and r.id_purity=est.purity
            LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id
            left JOIN employee_settings s on s.id_employee=e.created_by
            LEFT JOIN ret_size sz on sz.id_size = tag.size
            LEFT JOIN ret_wastage_discount_master wtg ON wtg.id_wastage = est.wastage_slab_id
            LEFT JOIN (SELECT est_item_id,SUM(tag_other_itm_amount) as metal_value FROM  ret_est_other_metals  GROUP BY est_item_id) as other_metal ON  other_metal.est_item_id =est.est_item_id
            LEFT JOIN (SELECT est_item_id, SUM(IFNULL(amount,0)) AS charge_value FROM ret_estimation_other_charges GROUP BY est_item_id) AS rtc ON rtc.est_item_id =est.est_item_id
            LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,
                m.tax_percentage as tax_percentage,
                i.tgi_calculation as tgi_calculation
                FROM ret_taxgroupitems i
                LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=pro.tgrp_id
                LEFT JOIN (SELECT est_item_id, IFNULL(SUM(price),0) as stone_price
                FROM `ret_estimation_item_stones`
                GROUP by est_item_id) as est_stn_detail ON est_stn_detail.est_item_id = est.est_item_id
                WHERE est.esti_id ='".$est_id."' and est.item_type in (0) and est.purchase_status = 0 and est.istag_merged in (0,1)
                GROUP by est.est_item_id");

        }else {
                $items_query = $this->db->query("SELECT est.isTagsplitted,est.est_item_id, esti_id, item_type, item_emp_id,c.scheme_closure_benefit,
                            est.product_id, est.tag_id, est.design_id, est.purity as purity, est.size, est.uom, est.piece,est.id_sub_design,
                            IFNULL(est.less_wt,0) as less_wt, est.net_wt, est.gross_wt,IFNULL(co.order_no,'') as order_no,
                            est.calculation_based_on, est.wastage_percent, est.mc_value, est.mc_type as mc_type,
                            item_cost, product_short_code,product_name as product_name,tag.sell_rate as sale_value,
                            concat(design_name,IFNULL(concat('-',design_code),'')) as design_name, design_name as design, design_code, pur.purity as purname,
                            tax.tax_percentage,tax.tgi_calculation,est.discount,e.created_by,s.disc_limit,s.disc_limit_type,e.id_branch,est.mc_type,est.is_non_tag,est.is_partial,est.lot_no,pro.sales_mode,tag.item_rate,
                            est.item_total_tax,est.market_rate_cost, tag.gross_wt as tag_gross_wt, tag.net_wt as tag_net_wt,txgrp.tgrp_name,tag.tag_code,IFNULL(est.id_orderdetails,'') as id_orderdetails, rtc.charge_value,
                            sub.sub_design_name,mt.metal as metal_name,IFNULL(est.est_rate_per_grm,0) as est_rate_per_grm,c.id_metal as metal_type,c.is_916,c.name as cat_name,
                            IFNULL(concat(sz.value,'',sz.name),'') as size,sz.id_size,pro.tgrp_id as tax_group_id,IFNULL(tag.tag_purchase_cost,0) as tag_purchase_cost,r.rate_field,r.market_rate_field, pro.tax_type, IFNULL(est_stn_detail.stone_price,0) AS stone_price, tag.sell_rate as sales_value,IFNULL(sec.section_name,'') as section_name,IFNULL(tag.hu_id,'') as huid,tag.id_section,ifnull(tag.quality_id,'') as quality_id,pro.stone_type,ifnull(tag.uom_gross_wt,'') as uom_id,
                            ifnull(tag.stone_calculation_based_on,'') as stone_calculation_based_on,pro.cat_id,c.id_metal,IFNULL(other_metal.metal_value,0) AS tag_other_itm_amount,
                            d.rate_per_gram as order_rate_per_grm,co.rate_type as ord_rate_type, tag.retail_max_wastage_percent as tagmaxva, tag.tag_mc_value, IFNULL(est.max_va_per,0.00) AS max_va_per ,
                            IFNULL(est.wastage_slab_id,'') AS wastage_slab_id,CONCAT(wtg.from_range, ' TO ',wtg.to_range) AS text_range,

                            IFNULL(wtg.id_metal,'') AS id_metal,IFNULL(wtg.value,'') AS value,est.tag_blk_disc AS tag_blk_disc,IFNULL(est.act_wast_per,0) as act_wast_per,
                            est.stone_amount AS stn_amt,est.diamond_amount AS dia_amt,IFNULL(est.max_mc,0) AS max_mc,est.wast_slab_value as slb_val,IFNULL(est.is_split_row,0) as is_split_row,est.pure_rate

                            FROM ret_estimation_items as est
                            LEFT JOIN customerorderdetails d ON d.id_orderdetails=est.id_orderdetails
                            LEFT JOIN customerorder co ON co.id_customerorder=d.id_customerorder
                            LEFT join ret_estimation e on e.estimation_id=est.esti_id
                            LEFT JOIN ret_product_master as pro ON pro.pro_id = est.product_id
                            LEFT JOIN ret_design_master as des ON des.design_no = est.design_id
                            LEFT JOIN ret_taging as tag ON tag.tag_id = est.tag_id
                            LEFT JOIN ret_sub_design_master as sub ON sub.id_sub_design = est.id_sub_design
                            LEFT JOIN ret_section sec ON sec.id_section = tag.id_section
                            LEFT JOIN ret_purity as pur ON pur.id_purity = est.purity
                            LEFT JOIN ret_category c on c.id_ret_category=pro.cat_id
                            LEFT JOIN metal mt on mt.id_metal=c.id_metal
                            LEFT JOIN ret_metal_purity_rate r on r.id_metal= mt.id_metal and r.id_purity=est.purity
                            LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id
                            left JOIN employee_settings s on s.id_employee=e.created_by
                            LEFT JOIN ret_size sz on sz.id_size = tag.size
                            LEFT JOIN ret_wastage_discount_master wtg ON wtg.id_wastage = est.wastage_slab_id
                            LEFT JOIN (SELECT est_item_id,SUM(tag_other_itm_amount) as metal_value FROM  ret_est_other_metals  GROUP BY est_item_id) as other_metal ON  other_metal.est_item_id =est.est_item_id
                            LEFT JOIN (SELECT est_item_id, SUM(IFNULL(amount,0)) AS charge_value FROM ret_estimation_other_charges GROUP BY est_item_id) AS rtc ON rtc.est_item_id =est.est_item_id
                            LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,
                                m.tax_percentage as tax_percentage,
                                i.tgi_calculation as tgi_calculation
                                FROM ret_taxgroupitems i
                                LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=pro.tgrp_id
                                LEFT JOIN (SELECT est_item_id, IFNULL(SUM(price),0) as stone_price
                                FROM `ret_estimation_item_stones`
                                GROUP by est_item_id) as est_stn_detail ON est_stn_detail.est_item_id = est.est_item_id
                                WHERE est.esti_id ='".$est_id."' and if(est.isTagsplitted = 1,est.item_type in (0,2), est.item_type in (0)) and est.purchase_status = 0 and est.istag_merged in (0,1)
                                GROUP by est.est_item_id");
        }
        //print_r($this->db->last_query());exit;
        $item_details = $items_query->result_array();
        $return_data=[];
        foreach ($item_details as $items) {
            $return_data["item_details"][]=array(
                            'isTagsplitted'     =>$items['isTagsplitted'],
                            'item_emp_id'       =>$items['item_emp_id'],
                            'item_rate'			=>$items['item_rate'],
                            'id_sub_design'		=>$items['id_sub_design'],
                            'sales_mode'		=>$items['sales_mode'],
                            'est_item_id'		=>$items['est_item_id'],
                            'item_type'			=>$items['item_type'],
                            'is_partial'        =>$items['is_partial'],
                            'tag_other_itm_amount'=>$items['tag_other_itm_amount'],
                            'scheme_closure_benefit'=>$items['scheme_closure_benefit'],
                            'order_no'       	=>$items['order_no'],
                            'esti_id'			=>$items['esti_id'],
                            'product_id'		=>$items['product_id'],
                            'lot_product'		=>$items['product_id'],
                            'tag_id'			=>$items['tag_id'],
                            'value'			    =>$items['tag_id'],
                            'cat_id'			=>$items['cat_id'],
                            'id_metal'			=>$items['id_metal'],
                            'tag_code'			=>$items['tag_code'],
                            'label'			    =>$items['tag_code'],
                            'design_id'			=>$items['design_id'],
                            'purity'			=>$items['purity'],
                            'size'			    =>$items['size'],
                            'size_name'			    =>$items['size'],
                            'uom'				=>$items['uom'],
                            'piece'				=>$items['piece'],
                            'less_wt'			=>$items['less_wt'],
                            'net_wt'			=>$items['net_wt'],
                            'gross_wt'			=>$items['gross_wt'],
                            'sale_value'			=>$items['sale_value'],
                            'calculation_based_on'=>$items['calculation_based_on'],
                            'wastage_percent'	  =>$items['wastage_percent'],
                            'tagmaxva'           => $items['tagmaxva'],
                            'retail_max_wastage_percent'=>$items['wastage_percent'],
                            'mc_value'		      =>$items['mc_value'],
                            'tag_mc_value'		=>$items['tag_mc_value'],
                            'item_cost'			=>$items['item_cost'],
                            'product_short_code'=>$items['product_short_code'],
                            'product_name'		=>$items['product_name'],
                            'design_code'		=>$items['design_code'],
                            'design_name'		=>$items['design_name'],
                            'metal_type'        =>$items['metal_type'],
                            'sub_design_name'   =>$items['sub_design_name'],
                            'design'			=>$items['design'],
                            'purname'			=>$items['purname'],
                            'tax_percentage'	=>$items['tax_percentage'],
                            'tgi_calculation'	=>$items['tgi_calculation'],
                            'discount'			=>$items['discount'],
                            'disc_limit'		=>$items['disc_limit'],
                            'disc_limit_type'	=>$items['disc_limit_type'],
                            'id_branch'			=>$items['id_branch'],
                            'mc_type'			=>$items['mc_type'],
                            'tag_mc_type'			=>$items['mc_type'],
                            'is_non_tag'		=>$items['is_non_tag'],
                            'is_partial'		=>$items['is_partial'],
                            'lot_no'		    =>$items['lot_no'],
                            'item_total_tax'    =>$items['item_total_tax'],
                            'tag_gross_wt'    	=>$items['tag_gross_wt'],
                            'tag_net_wt'    	=>$items['tag_net_wt'],
                            'market_rate_cost'  =>$items['market_rate_cost'],
                            'tgrp_name'  		=>$items['tgrp_name'],
                            'metal_name'  		=>$items['metal_name'],
                            'id_orderdetails'  	=>$items['id_orderdetails'],
                            'est_rate_per_grm'  =>$items['est_rate_per_grm'],
                            'item_rate'  =>$items['est_rate_per_grm'],
                            'order_rate_per_grm'  =>$items['order_rate_per_grm'],
                            'rate_per_grm'  =>$items['est_rate_per_grm'],
                            'ord_rate_type'  =>$items['ord_rate_type'],
                            //'stone_details'		=>($items['tag_id']=='' ? $this->get_stone_details($items['est_item_id']):$this->get_tag_stone_details($items['tag_id'])),
                            'stone_details'		=> ($items['isTagsplitted'] != 1 ? $this->get_stone_details($items['est_item_id']) : $this->getTagStoneDetails($items['tag_id']) ) ,
                            'other_metal_details'=>$this->get_est_other_metal_details($items['est_item_id']),
                            'material_details'	=>$this->get_other_material_details($items['est_item_id']),
                            'child_tag_details'	=>$this->child_tag_details($items['est_item_id']),
                            'charge_value'		=> $items['charge_value'],
                            'charges'			=> ( $items['isTagsplitted'] != 1 ?  $this->get_other_estcharges($items['est_item_id']) : $this->get_charges($items['tag_id']) ),
                            'charges_details'			=> ( $items['isTagsplitted'] != 1 ?  $this->get_other_estcharges($items['est_item_id']) : $this->get_charges($items['tag_id']) ),
                            'advance_details'   => $this->advance_details_order_details($items['order_no'],$items['id_branch']),
                            'mc_va_limit'       => $this->get_mc_va_limit($items['id_branch'],$items['product_id'], $items['design_id'], $items['id_sub_design']),
                            'is_916'			=>$items['is_916'],
                            'cat_name'		    =>$items['cat_name'],
                            'cat_id'		    =>$items['cat_id'],
                            'id_size'		    =>$items['id_size'],
                            'tax_group_id'		=>$items['tax_group_id'],
                            'tag_purchase_cost'	=>$items['tag_purchase_cost'],
                            'rate_field'	    =>$items['rate_field'],
                            'id_section'		=>$items['id_section'],
                            'section_name'		=>$items['section_name'],
                            'huid'			    =>$items['huid'],
                            'market_rate_field' =>$items['market_rate_field'],
                            'tax_type'          =>$items['tax_type'],
                            'sales_value'       =>$items['sales_value'],
                            'stone_price'       =>$items['stone_price'],
                            'quality_id'	    =>$items['quality_id'],
                            'stone_type'	    =>$items['stone_type'],
                            'stone_calculation_based_on' => $items['stone_calculation_based_on'],
                            'uom_id'			=>$items['uom_id'],
                            'tag_images'		=>$this->getTagImageDetails($items['tag_id']),
                            'pure_wt'			=>$items['pure_wt'],
                            'pure_rate'			=>$items['pure_rate'],
                            'max_va_per'        =>$items['max_va_per'],
                            'id_wastage'        =>$items['wastage_slab_id'],
                            'value'             =>$items['value'],
                            'id_metal'          =>$items['id_metal'],
                            'slab_range'        =>$items['text_range'],
                            'tag_blk_disc'      =>$items['tag_blk_disc'],
                            'act_wast_per'      =>$items['act_wast_per'],
                            'stn_amt'           =>$items['stn_amt'],
                            'dia_amt'           =>$items['dia_amt'],
                            'max_mc'            =>$items['max_mc'],
                            'slb_val'           =>$items['slb_val'],
                            'is_split_row'      =>$items['is_split_row']

                            
                    );
        }
        // print_r($this->db->last_query());exit;
        return $return_data;
    }
    function est_non_tag_items($est_id)
    {
		$items_query = $this->db->query("SELECT est.est_item_id, esti_id, item_type, est.id_sub_design,c.scheme_closure_benefit,item_emp_id,
					   est.product_id, est.tag_id, est.design_id, est.purity as purid, est.size, est.uom, est.piece,
                       IFNULL(est.less_wt,0) as less_wt, est.net_wt, est.gross_wt,txgrp.tgrp_id,
					   est.calculation_based_on, est.wastage_percent, est.mc_value,
					   item_cost, product_short_code,product_name as product_name,
                       concat(design_name,'-',design_code) as design_name, design_name as design, design_code, pur.purity as purname,
                       tax.tax_percentage,tax.tgi_calculation,est.discount,e.created_by,s.disc_limit,s.disc_limit_type,e.id_branch,est.mc_type,est.is_non_tag,est.is_partial,est.lot_no,pro.sales_mode,tag.item_rate,
                       est.item_total_tax,est.market_rate_cost,tag.net_wt as tag_net_wt,txgrp.tgrp_name,tag.tag_code,IFNULL(est.id_orderdetails,'') as id_orderdetails, rtc.charge_value,
                       sub.sub_design_name,mt.metal as metal_name,IFNULL(est.est_rate_per_grm,0) as est_rate_per_grm,c.id_metal as metal_type,c.is_916,c.name as cat_name, est.size, pro.tgrp_id as tax_group_id, pro.tax_type, r.rate_field,r.market_rate_field, IFNULL(est_stn_detail.stone_price,0) AS stone_price,ifnull(est.id_section,'') as id_section,ifnull(sec.section_name,'') as section_name,

                        IFNULL(est.max_va_per,0.00) AS max_va_per,IFNULL(est.wastage_slab_id,'') AS wastage_slab_id,CONCAT(wtg.from_range, ' TO ',wtg.to_range) AS text_range,
                        IFNULL(wtg.id_metal,'') AS id_metal,IFNULL(wtg.value,'') AS value,IFNULL(est.act_wast_per,0) as act_wast_per,est.stone_amount AS stn_amt,est.diamond_amount AS dia_amt,IFNULL(est.max_mc,0) AS max_mc,
                        est.wast_slab_value as slb_val

					   FROM ret_estimation_items as est
					   LEFT join ret_estimation e on e.estimation_id=est.esti_id
                       LEFT JOIN ret_product_master as pro ON pro.pro_id = est.product_id
                       LEFT JOIN ret_design_master as des ON des.design_no = est.design_id
                       LEFT JOIN ret_sub_design_master as sub ON sub.id_sub_design = est.id_sub_design
                       LEFT JOIN ret_purity as pur ON pur.id_purity = est.purity
                       LEFT JOIN ret_category c on c.id_ret_category=pro.cat_id
					   LEFT JOIN metal mt on mt.id_metal=c.id_metal
                       LEFT JOIN ret_metal_purity_rate r on r.id_metal= mt.id_metal and r.id_purity=est.purity
					   LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id
					   LEFT JOIN employee_settings s on s.id_employee=e.created_by
					   LEFT JOIN ret_taging as tag ON tag.tag_id = est.tag_id
                       LEFT JOIN ret_wastage_discount_master wtg ON wtg.id_wastage = est.wastage_slab_id
                       LEFT JOIN ret_section sec ON sec.id_section = est.id_section
					   LEFT JOIN (SELECT tag_id, SUM(IFNULL(charge_value,0)) AS charge_value FROM ret_taging_charges GROUP BY tag_id) AS rtc ON rtc.tag_id = tag.tag_id
					   LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,
						m.tax_percentage as tax_percentage,
						i.tgi_calculation as tgi_calculation
						FROM ret_taxgroupitems i
						LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=pro.tgrp_id
                        LEFT JOIN (SELECT est_item_id, IFNULL(SUM(price),0) as stone_price
                        FROM `ret_estimation_item_stones`
                        GROUP by est_item_id) as est_stn_detail ON est_stn_detail.est_item_id = est.est_item_id
					    WHERE est.esti_id ='".$est_id."' and est.item_type = 1 and est.purchase_status = 0
					    GROUP by est.est_item_id");
		//print_r($this->db->last_query());exit;
		$item_details = $items_query->result_array();
        $return_data=[];
		foreach ($item_details as $items) {
			$return_data["item_details"][]=array(
							'item_rate'			=>$items['item_rate'],
							'sales_mode'		=>$items['sales_mode'],
							'est_item_id'		=>$items['est_item_id'],
							'item_type'			=>$items['item_type'],
                            'tgrp_id'			=>$items['tgrp_id'],
							'esti_id'			=>$items['esti_id'],
                            'id_section'        =>$items['id_section'],
                            'section_name'        =>$items['section_name'],
							'product_id'		=>$items['product_id'],
							'tag_id'			=>$items['tag_id'],
							'tag_code'			=>$items['tag_code'],
							'design_id'			=>$items['design_id'],
                            'id_sub_design'		=>$items['id_sub_design'],
							'purid'				=>$items['purid'],
							'size'				=>$items['size'],
							'uom'				=>$items['uom'],
							'piece'				=>$items['piece'],
							'less_wt'			=>$items['less_wt'],
							'net_wt'			=>$items['net_wt'],
							'gross_wt'			=>$items['gross_wt'],
                            'scheme_closure_benefit'=>$items['scheme_closure_benefit'],
							'calculation_based_on' =>$items['calculation_based_on'],
							'wastage_percent'	   =>$items['wastage_percent'],
                            'charges'			=> $this->get_other_estcharges($items['est_item_id']),
							'mc_value'		       =>$items['mc_value'],
							'item_cost'			=>$items['item_cost'],
							'product_short_code'=>$items['product_short_code'],
							'product_name'		=>$items['product_name'],
							'design_code'		=>$items['design_code'],
							'design_name'		=>$items['design_name'],
							'metal_type'        =>$items['metal_type'],
							'sub_design_name'   =>$items['sub_design_name'],
							'design'			=>$items['design'],
							'purname'			=>$items['purname'],
							'tax_percentage'	=>$items['tax_percentage'],
							'tgi_calculation'	=>$items['tgi_calculation'],
							'discount'			=>$items['discount'],
							'disc_limit'		=>$items['disc_limit'],
							'disc_limit_type'	=>$items['disc_limit_type'],
							'id_branch'			=>$items['id_branch'],
							'mc_type'			=>$items['mc_type'],
							'is_non_tag'		=>$items['is_non_tag'],
							'is_partial'		=>$items['is_partial'],
							'lot_no'		    =>$items['lot_no'],
							'item_total_tax'    =>$items['item_total_tax'],
							'tag_net_wt'    	=>$items['tag_net_wt'],
							'market_rate_cost'  =>$items['market_rate_cost'],
							'tgrp_name'  		=>$items['tgrp_name'],
							'metal_name'  		=>$items['metal_name'],
							'id_orderdetails'  	=>$items['id_orderdetails'],
							'est_rate_per_grm'  =>$items['est_rate_per_grm'],
						    //'stone_details'		=>($items['tag_id']=='' ? $this->get_stone_details($items['est_item_id']):$this->get_tag_stone_details($items['tag_id'])),
						     'stone_details'		=>  $this->get_stone_details($items['est_item_id']),
						    'other_metal_details'=>$this->get_est_other_metal_details($items['est_item_id']),
						    'material_details'	=>$this->get_other_material_details($items['est_item_id']),
							'charge_value'		=> $items['charge_value'],
							'charges'			=> $this->get_other_estcharges($items['est_item_id']),
							'is_916'			=>$items['is_916'],
							'cat_name'		    =>$items['cat_name'],
                            'cat_id'		    =>$items['cat_id'],
                            'tax_group_id'		=>$items['tax_group_id'],
                            'rate_field'	    =>$items['rate_field'],
                            'market_rate_field' =>$items['market_rate_field'],
                            'tax_type'          =>$items['tax_type'],
                            'stone_price'       =>$items['stone_price'],
                            'item_emp_id'       =>$items['item_emp_id'],
                            'max_va_per'        =>$items['max_va_per'],
                            'id_wastage'        =>$items['wastage_slab_id'],
                            'value'             =>$items['value'],
                            'id_metal'          =>$items['id_metal'],
                            'slab_range'        =>$items['text_range'],

                            'act_wast_per'      =>$items['act_wast_per'],
                            'stn_amt'           =>$items['stn_amt'],
                            'dia_amt'           =>$items['dia_amt'],
                            'max_mc'            =>$items['max_mc'],
                            'slb_val'           =>$items['slb_val'],

					   );
		}
        //print_r($this->db->last_query());exit;
		return $return_data;
    }
    function est_home_bill($est_id)
    {
        $items_query = $this->db->query("SELECT est.est_item_id, esti_id, item_type,est.id_sub_design,c.scheme_closure_benefit,item_emp_id,
                       est.product_id, IFNULL(est.tag_id,'') as tag_id, est.design_id, est.purity as purid, est.size, est.uom, est.piece,
                       IFNULL(est.less_wt,0) as less_wt, est.net_wt, est.gross_wt, txgrp.tgrp_id,
                       est.calculation_based_on, est.wastage_percent, est.mc_value,r.rate_field,r.market_rate_field,
                       item_cost, product_short_code,product_name as product_name,
                       concat(design_name,'-',design_code) as design_name, design_name as design, design_code, pur.purity as purname,
                       tax.tax_percentage,tax.tgi_calculation,est.discount,e.created_by,s.disc_limit,s.disc_limit_type,e.id_branch,est.mc_type,est.is_non_tag,est.is_partial,est.lot_no,pro.sales_mode,tag.item_rate,
                       est.item_total_tax,est.market_rate_cost,tag.net_wt as tag_net_wt,txgrp.tgrp_name,IFNULL(tag.tag_code,'') as tag_code,
                       IFNULL(est.id_orderdetails,'') as id_orderdetails, rtc.charge_value,
                       sub.sub_design_name,mt.metal as metal_name,IFNULL(est.est_rate_per_grm,0) as est_rate_per_grm,c.id_metal as metal_type,c.is_916,c.name as cat_name,est.id_section as id_section,est.size,pro.tgrp_id as tax_group_id, pro.tax_type, IFNULL(est_stn_detail.stone_price,0) AS stone_price,ifnull(sec.section_name,'') as section_name,

                        IFNULL(est.max_va_per,0.00) AS max_va_per,IFNULL(est.wastage_slab_id,'') AS wastage_slab_id,CONCAT(wtg.from_range, ' TO ',wtg.to_range) AS text_range,
                        IFNULL(wtg.id_metal,'') AS id_metal,IFNULL(wtg.value,'') AS value,IFNULL(est.act_wast_per,0) as act_wast_per,est.stone_amount AS stn_amt,est.diamond_amount AS dia_amt,IFNULL(est.max_mc,0) AS max_mc,
                        est.wast_slab_value as slb_val, est.pure_rate

                       FROM ret_estimation_items as est
                       LEFT join ret_estimation e on e.estimation_id=est.esti_id
                       LEFT JOIN ret_product_master as pro ON pro.pro_id = est.product_id
                       LEFT JOIN ret_design_master as des ON des.design_no = est.design_id
                       LEFT JOIN ret_sub_design_master as sub ON sub.id_sub_design = est.id_sub_design
                       LEFT JOIN ret_purity as pur ON pur.id_purity = est.purity
                       LEFT JOIN ret_category c on c.id_ret_category=pro.cat_id
                       LEFT JOIN metal mt on mt.id_metal=c.id_metal
                       LEFT JOIN ret_metal_purity_rate r on r.id_metal= mt.id_metal and r.id_purity=est.purity
                       LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = pro.tgrp_id
                       left JOIN employee_settings s on s.id_employee=e.created_by
                       LEFT JOIN ret_taging as tag ON tag.tag_id = est.tag_id
                       LEFT JOIN ret_section sec ON sec.id_section = est.id_section
                       LEFT JOIN ret_wastage_discount_master wtg ON wtg.id_wastage = est.wastage_slab_id
                       LEFT JOIN (SELECT tag_id, SUM(IFNULL(charge_value,0)) AS charge_value FROM ret_taging_charges GROUP BY tag_id) AS rtc ON rtc.tag_id = tag.tag_id
                       LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,
                        m.tax_percentage as tax_percentage,
                        i.tgi_calculation as tgi_calculation
                        FROM ret_taxgroupitems i
                        LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=pro.tgrp_id
                        LEFT JOIN (SELECT est_item_id, IFNULL(SUM(price),0) as stone_price
                        FROM `ret_estimation_item_stones`
                        GROUP by est_item_id) as est_stn_detail ON est_stn_detail.est_item_id = est.est_item_id
                        WHERE est.esti_id ='".$est_id."' and est.item_type = 2 and est.isTagsplitted = 0  and est.purchase_status = 0
                        GROUP by est.est_item_id");
        //print_r($this->db->last_query());exit;
        $item_details = $items_query->result_array();
        $return_data=[];
        foreach ($item_details as $items) {
            $return_data["item_details"][]=array(
                            'item_rate'			=>$items['item_rate'],
                            'tgrp_id'			=>$items['tgrp_id'],
                            'sales_mode'		=>$items['sales_mode'],
                            'est_item_id'		=>$items['est_item_id'],
                            'rate_field'        =>$items['rate_field'],
                            'market_rate_field' =>$items['market_rate_field'],
                            'item_type'			=>$items['item_type'],
                            'esti_id'			=>$items['esti_id'],
                            'product_id'		=>$items['product_id'],
                            'tag_id'			=>$items['tag_id'],
                            'tag_code'			=>$items['tag_code'],
                            'design_id'			=>$items['design_id'],
                            'id_sub_design'		=>$items['id_sub_design'],
                            'purid'				=>$items['purid'],
                            'size'				=>$items['size'],
                            'uom'				=>$items['uom'],
                            'piece'				=>$items['piece'],
                            'less_wt'			=>$items['less_wt'],
                            'net_wt'			=>$items['net_wt'],
                            'gross_wt'			=>$items['gross_wt'],
                            'calculation_based_on'=>$items['calculation_based_on'],
                            'wastage_percent'	=>$items['wastage_percent'],
                            'mc_value'		=>$items['mc_value'],
                            'item_cost'			=>$items['item_cost'],
                            'product_short_code'=>$items['product_short_code'],
                            'product_name'		=>$items['product_name'],
                            'design_code'		=>$items['design_code'],
                            'design_name'		=>$items['design_name'],
                            'metal_type'        =>$items['metal_type'],
                            'sub_design_name'   =>$items['sub_design_name'],
                            'design'			=>$items['design'],
                            'purname'			=>$items['purname'],
                            'tax_percentage'	=>$items['tax_percentage'],
                            'tgi_calculation'	=>$items['tgi_calculation'],
                            'discount'			=>$items['discount'],
                            'disc_limit'		=>$items['disc_limit'],
                            'disc_limit_type'	=>$items['disc_limit_type'],
                            'id_branch'			=>$items['id_branch'],
                            'mc_type'			=>$items['mc_type'],
                            'is_non_tag'		=>$items['is_non_tag'],
                            'is_partial'		=>$items['is_partial'],
                            'lot_no'		    =>$items['lot_no'],
                            'item_total_tax'    =>$items['item_total_tax'],
                            'tag_net_wt'    	=>$items['tag_net_wt'],
                            'market_rate_cost'  =>$items['market_rate_cost'],
                            'tgrp_name'  		=>$items['tgrp_name'],
                            'metal_name'  		=>$items['metal_name'],
                            '`id_orderdetails`'  	=>$items['id_orderdetails'],
                            'est_rate_per_grm'  =>$items['est_rate_per_grm'],
                            //'stone_details'		=>($items['tag_id']=='' ? $this->get_stone_details($items['est_item_id']):$this->get_tag_stone_details($items['tag_id'])),
                            'stone_details'	=>  $this->get_stone_details($items['est_item_id']),
                            'other_metal_details'=>$this->get_est_other_metal_details($items['est_item_id']),
                            'material_details'	=>$this->get_other_material_details($items['est_item_id']),
                            'charge_value'		=> $items['charge_value'],
                            'charges'			=> $this->get_other_estcharges($items['est_item_id']),
                            //'charges'			=> [],
                            'is_916'			=>$items['is_916'],
                            'cat_name'		    =>$items['cat_name'],
                            'cat_id'		    =>$items['cat_id'],
                            'id_section'		=>$items['id_section'],
                            'section_name'      =>$items['section_name'],
                            'tax_group_id'		=>$items['tax_group_id'],
                            'tax_type'          =>$items['tax_type'],
                            'stone_price'       =>$items['stone_price'],
                            'pure_wt'			=>$items['pure_wt'],
                            'pure_rate'			=>$items['pure_rate'],
                            'item_emp_id'       =>$items['item_emp_id'],
                            'max_va_per'        =>$items['max_va_per'],
                            'id_wastage'        =>$items['wastage_slab_id'],
                            'value'             =>$items['value'],
                            'id_metal'          =>$items['id_metal'],
                            'slab_range'        =>$items['text_range'],
                            'act_wast_per'      =>$items['act_wast_per'],
                            'stn_amt'           =>$items['stn_amt'],
                            'dia_amt'           =>$items['dia_amt'],
                            'max_mc'            =>$items['max_mc'],
                            'slb_val'           =>$items['slb_val']

                       );
        }
          return $return_data;
    }
    function old_metal($est_id)
    {
        $old_matel_query = $this->db->query("SELECT IFNULL(est_old.old_metal_prod_id,'') as old_metal_prod_id,
                                IFNULL(p.product_name,'') as old_metal_prod_name,est_old.old_metal_sale_id, est_id,piece,
    						   id_category, type, item_type,est_old.gross_wt,est_old.net_wt,est_old.remark as remark,
    						   round((gross_wt - stone_wt),3) as ls_wt,
                               if(type = 1, 'Melting', 'Retag') as reusetype,
                               if(item_type = 1, 'Ornament', if(item_type = 2, 'Coin', if(item_type = 3, 'Bar',''))) as receiveditem,
    						   IFNULL(stone_wt,0) as stone_wt, IFNULL(dust_wt,0) as dust_wt , est_old.purity as purid, wastage_percent,
    						   wastage_wt, rate_per_gram, amount,
    						   pur.purity as purname, met.metal ,est_old.purpose,est_old.id_old_metal_type, est_old.id_old_metal_category, oldM.metal_type AS old_metal_type,
    						   IFNULL(est_old.touch,'') as touch
    						   FROM ret_estimation_old_metal_sale_details as est_old
    						   LEFT JOIN ret_purity as pur ON pur.id_purity = est_old.purity
                               LEFT JOIN  ret_product_master as p  ON p.pro_id = est_old.old_metal_prod_id
    						   LEFT JOIN  ret_old_metal_type AS oldM ON oldM.id_metal_type = est_old.id_old_metal_type
    						   LEFT JOIN metal as met ON met.id_metal = est_old.id_category
    						   WHERE est_old.est_id = '".$est_id."' and est_old.purchase_status = 0 ");
    		$old_matel_detail= $old_matel_query->result_array();
            $return_data=[];
    		foreach ($old_matel_detail as $data) {
    			$return_data["old_matel_details"][]=array(
    								'old_metal_sale_id'=>$data['old_metal_sale_id'],
    								'id_old_metal_type'=>$data['id_old_metal_type'],
    								'id_old_metal_category'=>$data['id_old_metal_category'],
                                    'remark' =>$data['remark'],
                                    'piece'=>$data['piece'],
    								'old_metal_type'=>$data['old_metal_type'],
    								'est_id'=>$data['est_id'],
    								'id_category'=>$data['id_category'],
    								'type'=>$data['type'],
    								'item_type'=>$data['item_type'],
    								'gross_wt'=>$data['gross_wt'],
    								'ls_wt'=>$data['ls_wt'],
    								'net_wt'=>$data['net_wt'],
    								'stone_wt'=>$data['stone_wt'],
    								'reusetype'=>$data['reusetype'],
    								'receiveditem'=>$data['receiveditem'],
    								'dust_wt'=>$data['dust_wt'],
    								'purid'=>$data['purid'],
    								'wastage_percent'=>$data['wastage_percent'],
    								'wastage_wt'=>$data['wastage_wt'],
    								'rate_per_gram'=>$data['rate_per_gram'],
    								'amount'=>$data['amount'],
    								'purname'=>$data['purname'],
    								'metal'=>$data['metal'],
    								'purpose'=>$data['purpose'],
    								'touch'=>$data['touch'],
																'old_metal_prod_name'=>$data['old_metal_prod_name'],
							    								'old_metal_prod_id'=>$data['old_metal_prod_id'],
    								'goldrate_24ct'=>($this->getOldMetalRate($data['id_old_metal_type'])),
    								'stone_details' =>$this->get_old_metal_stone_details($data['old_metal_sale_id']),
    								'old_metal_types'=>	$this->get_old_metal_type($data['id_category']),
    								'old_metal_category'=>	$this->get_old_metal_category($data['id_old_metal_type'])
    						);
    		}
            return $return_data;
    }
    function get_old_metal_Product($data)
	{
	    $sql = $this->db->query("SELECT p.pro_id,p.product_name,c.id_metal
         FROM ret_product_master p
         LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
         WHERE product_status = 1
         ".($data['id_metal']!='' ? " and c.id_metal=".$data['id_metal']."" :'')." ");
         //print_r($this->db->last_query());exit;
	    return $sql->result_array();
	}
    function get_tag_status_details($data)
    {
        $sql=$this->db->query("SELECT tag_id,tag_code,tag_status,(if(tag_status =0 ,if( id_orderdetails IS  NULL,'Not yet sale','Reserved'),if(tag_status =1 ,'Sold out',if(tag_status =2 ,'Deleted',if(tag_status =3 ,'Other Issue,',if(tag_status =4 ,' In Transit',if(tag_status =5 ,'Deleted for Stock',if(tag_status =6 ,'Sales Return',if(tag_status =7 ,'Tag Issued',if(tag_status =10 ,'Delivery Ready',if(tag_status =15 ,'Tag Unavailable',''))))))))))) as status from ret_taging WHERE current_branch = '".$data['id_branch']."' and ".$data['searchField']." =  '".$data['searchTxt']."' ");
        return $sql->row_array();
    }
    function getTagImageDetails($tag_id)
    {
        $sql = $this->db->query("SELECT ti.id_tag_img,ti.tag_id,ti.image,ti.is_default
        FROM ret_taging_images ti
        WHERE ti.tag_id=".$tag_id."");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    function isEmptySetDefault($value,$default){
	    if($value != '' &&  $value != NULL &&  $value != 'null' ){
	        return $value;
	    }else{
	        return $default;
	    }
	}
	function get_profile_settings($id_profile)
    {
       $sql=$this->db->query("SELECT * FROM `profile` WHERE id_profile=".$id_profile."");
       return $sql->row_array();
    }
    function child_tag_details($est_item_id){
        $return_data=array();
        $data = $this->db->query("SELECT e.tag_id,e.esti_id,e.product_id,e.item_emp_id,e.design_id,e.id_sub_design,e.purity,IFNULL(e.size,'') as size ,e.piece,e.less_wt,e.net_wt,e.gross_wt,e.calculation_based_on,e.wastage_percent,e.mc_value,e.item_cost,e.est_rate_per_grm,e.is_partial,e.mc_type,e.tax_group_id,e.item_total_tax,e.market_rate_cost,e.market_rate_tax,e.purchase_status,e.bil_detail_id,e.istag_merged,e.discount,e.id_orderdetails,des.design_name,sub_des.sub_design_name,pro.product_name,tag.tag_code as label,e.est_item_id,pur.purity as pur_name,est.tag_id as parent_tag_id,pro.sales_mode,r.rate_field,IFNULL(sec.id_section,'') as id_section,IFNULL(sec.section_name,'') as section_name,IFNULL(tag.hu_id,'') as huid
                                FROM ret_est_tag_merge  mrg
                                LEFT JOIN ret_estimation_items e ON e.est_item_id = mrg.ref_est_item_id
                                LEFT JOIN ret_estimation_items est ON est.est_item_id = mrg.est_item_id
                                LEFT JOIN ret_taging tag ON tag.tag_id = e.tag_id
                                LEFT JOIN ret_section sec ON sec.id_section = tag.id_section
                                LEFT JOIN ret_product_master pro ON pro.pro_id = e.product_id
                                left join ret_category c on c.id_ret_category=pro.cat_id
                                LEFT JOIN ret_design_master des ON des.design_no = e.design_id
                                LEFT JOIN ret_sub_design_master sub_des ON sub_des.id_sub_design = e.id_sub_design
                                LEFT JOIN ret_metal_purity_rate r on r.id_metal=c.id_metal and r.id_purity=tag.purity
                                LEFT JOIN ret_purity pur ON pur.id_purity = e.purity
                                WHERE mrg.est_item_id = '".$est_item_id."' and e.istag_merged in (2)");
        $result =   $data->result_array();
        foreach($result as $items){
            $items['stone_details'] = $this->get_child_tag_stone_details($items['est_item_id']);
            $return_data[] = $items;
        }
        return $return_data;
    }
    function get_child_tag_stone_details($est_item_id){
        $sql = $this->db->query("SELECT  est_item_stone_id,est_item_id,st.stone_type,
                       est_st.stone_id, pieces, wt, price, rate_per_gram,is_apply_in_lwt,stone_cal_type,
                       stone_name, stone_code, uom_name, uom_short_code,est_st.uom_id
                       FROM ret_estimation_item_stones as est_st
                       LEFT JOIN ret_stone as st ON st.stone_id = est_st.stone_id
                       LEFT JOIN ret_uom as um ON um.uom_id = est_st.uom_id
        WHERE est_st.est_item_id='".$est_item_id."' ");
    }
    function get_Active_Purity($data)
    {
        $sql = $this->db->query("SELECT p.id_purity,p.purity,p.description
         FROM ret_purity p
            LEFT JOIN ret_metal_cat_purity mp on mp.id_purity = p.id_purity
            LEFT JOIN ret_category c on c.id_ret_category=mp.id_category
            LEFT JOIN metal mt on mt.id_metal=c.id_metal
          WHERE  p.id_purity is not null and p.status = 1
        ".($data['id_metal']!='' ? " and c.id_metal = ".$data['id_metal']."" :'')."
        ".($data['cat_id'] !='' ? " and mp.id_category = ".$data['cat_id'] ."" :'')."
        GROUP BY p.id_purity
        ");
        // print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    function get_village_by_pincode($pincode){
        $data = $this->db->query("SELECT *
        FROM village v
        where v.id_village is not null and v.status=1 ".($pincode!=0 && $pincode!='' ? " and v.pincode=".$pincode."" :'')."
        ");
        return $data->result_array();
    }
 function get_old_metal_types()
    {
        $sql=$this->db->query("SELECT metal_type,id_metal_type,id_metal FROM ret_old_metal_type");
        return $sql->result_array();
    }
    function get_sectionBranchwise($data) {
		$id_branch = (isset($data['id_branch']) ? ($data['id_branch'] !='' ? $data['id_branch'] :'') :'');

		$multiple_id_branch = implode(' , ', $id_branch);

        if($multiple_id_branch != '') {

			$id_branch = $multiple_id_branch;

		} else {

			$id_branch = $id_branch;

		}
		$where = "";
		if(isset($data['status'])) {
			$where = $where." AND sect.status = ".$data['status'];
		}
		$sql=$this->db->query("SELECT sect.id_section,sect.section_name,sect.section_short_code as short_code,b.name as branch,sect_br.id_branch,sect.is_home_bill_counter
		FROM ret_section sect
		LEFT JOIN ret_section_branch sect_br on sect_br.id_section = sect.id_section
		LEFT join branch b on b.id_branch = sect_br.id_branch
		where sect.id_section IS NOT NULL
		".($id_branch !='' && $id_branch != 0 ? " and sect_br.id_branch IN (".$id_branch.")" :'')." ".$where);
		return $sql->result_array();
	}
    function get_est_stone_wt($id){
        $sql = $this->db->query("select sum(wt) as wt from ret_estimation_item_stones where est_item_id=".$id."");
        return $sql->row()->wt;

    }
    function get_tag_stone_wt($id){

        $sql = $this->db->query("select sum(wt) as wt from ret_taging_stone where tag_id=".$id."");
        return $sql->row()->wt;

    }
    function get_credit_pending_details($id_branch, $id_customer)
	{
		$return_data = array();
		$credit_detail = array();
		$credit_detai2 = array();
		$sql = $this->db->query("SELECT b.tot_amt_received,b.bill_id,b.bill_no,DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date,DATE_FORMAT(b.credit_due_date,'%d-%m-%Y') as credit_due_date,b.tot_bill_amount,b.tot_amt_received,b.bill_cus_id,c.mobile,c.firstname as cus_name,
		if(b.credit_status=1,'Paid','Pending') as credit_status,br.name as branch_name,b.tot_bill_amount,(b.tot_bill_amount-b.tot_amt_received-IFNULL(ret.credit_due_amt,0)) as bal_amt,b.credit_disc_amt,IFNULL(ret.credit_due_amt,0) as credit_due_amt,IFNULL(ret.credit_ret_amt,0) as credit_ret_amt
			from ret_billing b
			LEFT JOIN customer c on c.id_customer=b.bill_cus_id
			LEFT JOIN branch br on br.id_branch=b.id_branch
            LEFT JOIN(SELECT IFNULL((b.credit_due_amt),0) as credit_due_amt,
            r.ret_bill_id,IFNULL(b.credit_ret_amt,0) as credit_ret_amt
            FROM ret_bill_return_details r
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
			$paid_amount = $this->get_credit_collection_details($r['bill_id']);
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
		foreach ($items as $item) {
			$total_bill_amount  += $item['tot_amt_received'];
			$credit_disc_amt    += $item['credit_disc_amt'];
			$old_metal_details  =  $this->getOld_sales_details($item['bill_id'], 8);
			$old__metal_amount  = 0;
			foreach ($old_metal_details as $old_items) {
				$old__metal_amount += $old_items['amount'];
			}
		}
		return $total_bill_amount + $old__metal_amount + $credit_disc_amt;
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

    public function pan_available($pan_number, $id_customer = null)
	{
		$this->db->select('pan');
		$this->db->where('pan',$pan_number);

        if ($id_customer) {
            $this->db->where('id_customer !=', $id_customer);
        }

		$status=$this->db->get('customer');
        // print_r($status->num_rows());exit;
		if($status->num_rows()>0)
		{
			$return_data=array('status'=>false,'message'=>'PAN Number Already Exists');
		}else{
			$return_data=array('status'=>true,'message'=>'');
		}
		return $return_data;
	}

    public function gst_available($gst_number, $id_customer = null)
	{
		$this->db->select('gst_number');
		$this->db->where('gst_number',$gst_number);
        
        if ($id_customer) {
            $this->db->where('id_customer !=', $id_customer);
        }

		$status=$this->db->get('customer');
		//print_r($status->num_rows());exit;
		if($status->num_rows()>0)
		{
			$return_data=array('status'=>false,'message'=>'GST Number Already Exists');
		}else{
			$return_data=array('status'=>true,'message'=>'');
		}
		return $return_data;
	}

    public function aadhar_available($aadhar_number, $id_customer = null)
	{
		$this->db->select('aadharid');
		$this->db->where('aadharid',$aadhar_number);
        
        if ($id_customer) {
            $this->db->where('id_customer !=', $id_customer);
        }
		$status=$this->db->get('customer');
		//print_r($status->num_rows());exit;
		if($status->num_rows()>0)
		{
			$return_data=array('status'=>false,'message'=>'AADHAR Number Already Exists');
		}else{
			$return_data=array('status'=>true,'message'=>'');
		}
		return $return_data;
	}


    function get_billing_advance_details($id_branch, $id_customer)
	{
		$adv_details_query = $this->db->query("SELECT IFNULL((SUM(r.amount)-(IFNULL(adv_trns.transfer_amount,0) + IFNULL(advance.amount,0)+ IFNULL(refund.refund_amount,0))),0) as amount
						FROM ret_issue_receipt  r

						LEFT JOIN(SELECT trn.transfer_receipt_id,IFNULL(SUM(trn.transfer_amount),0) as transfer_amount
							FROM ret_advance_transfer trn
							LEFT JOIN ret_issue_receipt ir ON ir.id_issue_receipt = trn.transfer_receipt_id
							LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = trn.transfer_receipt_id
							Where r.bill_status=1 and r.id_customer=".($id_customer)."
							GROUP BY trn.transfer_receipt_id) as adv_trns ON adv_trns.transfer_receipt_id = r.id_issue_receipt



							left join (select sum(u.utilized_amt) as amount,ir.id_issue_receipt, ir.id_customer
							from ret_issue_receipt as ir
							left JOIN ret_advance_utilized as u on u.id_issue_receipt=ir.id_issue_receipt
							LEFT JOIN ret_billing bill on bill.bill_id=u.bill_id
							where bill.bill_status=1  and  ir.id_customer=".($id_customer)."
							GROUP by ir.id_customer) as advance on advance.id_customer=r.id_customer

							LEFT JOIN (select a.refund_receipt,IFNULL(SUM(a.refund_amount),0) as refund_amount
							From ret_advance_refund a
							LEFT JOIN ret_issue_receipt r on r.id_issue_receipt=a.id_issue_receipt
							Where r.bill_status=1 and r.id_customer=".($id_customer)."
							group by a.refund_receipt) as refund on refund.refund_receipt=r.id_issue_receipt


							Where bill_status=1 and r.id_customer=".($id_customer)."");

							// print_r($this->db->last_query());exit;

	   	return $adv_details_query->row()->amount;

	}

    function get_order_advance_details($id_branch, $id_customer){

        $sql = $this->db->query("SELECT SUM(IFNULL(a.advance_amount,0)-IFNULL(a.adjusted_amount,0)) as amount 
        FROM ret_billing_advance a 
        LEFT JOIN ret_billing b on b.bill_id = a.bill_id
        where a.is_adavnce_adjusted = 0 and b.bill_status=1 and a.adjusted_bill_id is null and b.bill_type=5  and b.bill_cus_id=".($id_customer)."
        " . ($id_branch != '' && $id_branch > 0 ? ' and b.id_branch=' . $id_branch : '') . "
        
        ");

        return $sql->row()->amount;

    }
    public function passport_available($passport_no, $id_customer = null)
	{
        // print_r($id_customer);exit;
		$this->db->select('passport_no');
		$this->db->where('passport_no',$passport_no);
        
        if ($id_customer) {
            $this->db->where('id_customer !=', $id_customer);
        }

		$status=$this->db->get('customer');
		// print_r($status->num_rows());exit;
		if($status->num_rows()>0)
		{
			$return_data=array('status'=>false,'message'=>'Passport Number Already Exists');
		}else{
			$return_data=array('status'=>true,'message'=>'');
		}
		return $return_data;
	}

    public function dl_available($dl_no, $id_customer = null)
	{
        // print_r($id_customer);exit;
		$this->db->select('driving_license_no');
		$this->db->where('driving_license_no',$dl_no);
        
        if ($id_customer) {
            $this->db->where('id_customer !=', $id_customer);
        }

		$status=$this->db->get('customer');
		// print_r($status->num_rows());exit;
		if($status->num_rows()>0)
		{
			$return_data=array('status'=>false,'message'=>'Driving License Number Already Exists');
		}else{
			$return_data=array('status'=>true,'message'=>'');
		}
		return $return_data;
	}
    function get_stone_disc(){
        
        $sql = $this->db->query("SELECT IFNULL(max_disc_per,0) AS max_disc_per, IFNULL(min_disc_per,0) AS min_disc_per,
        id_stone_type,status FROM ret_stone_discount_master
        WHERE id_stone_type = 1 AND status = 1 ORDER BY id DESC");

        return $sql->row_array();
    }

    function get_va_range($id_metal){

        $sql = $this->db->query("SELECT va.from_range,va.to_range,va.id_metal,va.value,
        CONCAT(va.from_range, ' TO ', va.to_range) AS text_range,va.id_wastage
        FROM ret_wastage_discount_master va
        WHERE va.from_range IS NOT NULL AND va.to_range IS NOT NULL AND va.id_metal = ".$id_metal."");

        return $sql->result_array();
    }

}
?>