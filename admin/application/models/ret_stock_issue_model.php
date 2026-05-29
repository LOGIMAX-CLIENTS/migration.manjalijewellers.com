<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ret_stock_issue_model extends CI_Model

{



	function __construct()

    {

        parent::__construct();

    }



    // General Functions

    public function insertData($data,$table)

    {

    	$insert_flag = 0;

		$insert_flag = $this->db->insert($table,$data);

		return ($insert_flag == 1 ? $this->db->insert_id(): 0);

	}



	public function updateData($data,$id_field,$id_value,$table)

    {

	    $edit_flag = 0;

	    $this->db->where($id_field,$id_value);

		$edit_flag = $this->db->update($table,$data);

		return ($edit_flag==1?$id_value:0);

	}

	public function deleteData($id_field,$id_value,$table)

    {

        $this->db->where($id_field, $id_value);

        $status= $this->db->delete($table);

		return $status;

	}

	// function get_profile_settings($id_profile)
    // {
    //    $sql=$this->db->query("SELECT * FROM `profile` WHERE id_profile=".$id_profile."");
    //    return $sql->row_array();
    // }


   function get_profile_settings($id_profile)

   {

       $data=$this->db->query("SELECT pr.stock_issue_otp_req  FROM  profile pr  where pr.id_profile ='".$id_profile."'");

       return $data->row()->stock_issue_otp_req;

   }
    
	function get_FinancialYear()

	{

		$sql=$this->db->query("SELECT fin_year_code From ret_financial_year where fin_status=1");

		return $sql->row_array();

	}


    function get_ret_settings($settings)

	{

		$data=$this->db->query("SELECT value FROM ret_settings where name='".$settings."'");

		return $data->row()->value;

	}


	function generateIssueNo()

	{

        $fin_year = $this->get_FinancialYear();

        // print_r($fin_year);exit;

	    $lastno = NULL;

	    $sql = "SELECT MAX(SUBSTRING_INDEX(issue_no, '-', -1)) AS last_issue_no FROM ret_stock_issue WHERE fin_year = '" . $fin_year['fin_year_code'] . "' ORDER BY last_issue_no DESC LIMIT 1";

			$result = $this->db->query($sql);

			if( $result->num_rows() > 0){

				$lastno = $result->row()->last_issue_no;

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

        $issue_number = $fin_year['fin_year_code'] . '-' . $order_number;



		return $issue_number;

	}





    function ajax_getStockIssueList($data)

	{



        $stock_data=array();



	   $sql = $this->db->query("SELECT i.id_stock_issue,
                                i.issue_no,
                                iss_type.name as issue_type,
                                emp.firstname as emp_name,
                                DATE_FORMAT(i.issue_date,'%d-%m-%Y') as issue_date,
                                br.name as branch_name,
                                IFNULL(c.order_no,'') as order_no,
                                IF(c.order_type=3,'Customer Repair',IF(c.order_type=4,'Stock Repair','')) as repair_type,
                                CASE
                                    WHEN i.status = 0 THEN 'Approval Pending'
                                    WHEN i.status = 2 THEN 'Rejected'
                                    WHEN i.status = 3 THEN 'Received'
                                    WHEN (SELECT COUNT(*) FROM ret_stock_issue_detail d2 WHERE d2.id_stock_issue = i.id_stock_issue AND d2.status = 3) > 0
                                         AND (SELECT COUNT(*) FROM ret_stock_issue_detail d3 WHERE d3.id_stock_issue = i.id_stock_issue AND d3.status != 3) > 0
                                    THEN 'Partially Received'
                                    WHEN (SELECT COUNT(*) FROM ret_stock_issue_detail d4 WHERE d4.id_stock_issue = i.id_stock_issue) > 0
                                         AND (SELECT COUNT(*) FROM ret_stock_issue_detail d5 WHERE d5.id_stock_issue = i.id_stock_issue AND d5.status != 3) = 0
                                    THEN 'Received'
                                    ELSE 'Issued'
                                END as issue_status,
                                issue_det.tag_code,
                                issue_det.cat_name,
                                issue_det.status
                            FROM ret_stock_issue i
                            LEFT JOIN (SELECT iss_dt.id_stock_issue,
                                              GROUP_CONCAT(iss_dt.tag_id) AS tag_id,
                                              GROUP_CONCAT(tag.tag_code) AS tag_code,
                                              GROUP_CONCAT(cm.name) AS cat_name,
                                              iss_dt.status
                                        FROM ret_stock_issue_detail AS iss_dt
                                        LEFT JOIN ret_taging AS tag ON tag.tag_id = iss_dt.tag_id
                                        LEFT JOIN ret_product_master AS pm ON pm.pro_id = tag.product_id
                                        LEFT JOIN ret_category AS cm ON cm.id_ret_category = pm.cat_id
                                        GROUP BY iss_dt.id_stock_issue) AS issue_det ON issue_det.id_stock_issue = i.id_stock_issue
                            LEFT JOIN ret_stock_issue_types AS iss_type ON iss_type.id_stock_issue_type = i.issue_type
                            LEFT JOIN branch br ON br.id_branch = i.id_branch
                            LEFT JOIN employee emp ON emp.id_employee = i.created_by
                            LEFT JOIN customerorder c ON c.id_stock_issue = i.id_stock_issue
                            WHERE i.id_stock_issue IS NOT NULL " . ($data['status'] != '' && $data['status'] > 0 ? 'AND issue_det.status=' . $data['status'] : ''));


         $data = $sql->result_array();

         foreach($data as $items)

         {

            $stock_data[]=array(

                                 'id_stock_issue'  =>$items['id_stock_issue'],

                                 'issue_no'        =>$items['issue_no'],

                                 'tag_code'        =>$items['tag_code'],

                                 'issue_no'        =>$items['issue_no'],

                                 'issue_type'      =>$items['issue_type'],

                                 'emp_name'        =>$items['emp_name'],

                                 'issue_date'      =>$items['issue_date'],

                                 'branch_name'     =>$items['branch_name'],

                                 'order_no'        =>$items['order_no'],

                                 'repair_type'     =>$items['repair_type'],

                                 'issue_status'    =>$items['issue_status'],

                                 'status'          =>$items['status'],

                                 'cat_name'        =>$items['cat_name'],

                                 'summary'         =>$this->get_stock_issue_det($items['id_stock_issue']),

                             );

        }

        //print_r($this->db->last_query());exit;

        return $stock_data;

	}



    function get_stock_issue_det($id_stock_issue)

	{

                $sql=$this->db->query("SELECT sd.id_stock_issue,ifnull(emp.firstname,'-')as emp_name, ifnull(sd.received_date,'-') as received_date,ifnull(sum(t.less_wt),0) as less_wt,

                ifnull(sum(t.net_wt),0) as net_wt,ifnull(sum(t.gross_wt),0) as gross_wt,IFNULL(sd.received_by,'') as received_by,IFNULL(sd.received_time,'') as received_time

                FROM ret_stock_issue_detail sd

                LEFT JOIN employee emp ON emp.id_employee= sd.received_by

                LEFT JOIN ret_taging t ON t.tag_id = sd.tag_id

                where sd.id_stock_issue=".$id_stock_issue." and sd.received_date is not null

                Group by sd.received_date,sd.received_by,sd.received_time");

        // print_r($this->db->last_query());exit;

        return $sql->result_array();

	}





    function get_IssueItems($id)

	{

	    $sql=$this->db->query("SELECT i.id_branch,i.id_stock_issue,i.issue_no,i.stock_type,i.issued_to,i.issue_type as issue_type,date_format(i.issue_date,'%d-%m-%Y') as issue_date,br.name as branch_name,i.repair_type,

	    IFNULL(i.remarks,'') as remarks,emp.firstname as emp_name,emp.mobile as emp_mobile,i.id_employee as employee,

        cus.firstname as customer_name,cus.mobile as cus_mobile,ad.address1 as cus_address1,ad.address2 as cus_address2,ad.address3,ct.name as  cus_city_name,st.name as  cus_state_name,co.name as  cus_country_name,

        i.id_customer as customer,ad.pincode as cus_pincode,ad.id_state as cus_id_state,st.state_code as cus_state_code,cus.gst_number as cus_gst_number,

        i.id_karigar as karigar,k.firstname as karigar_name, IFNULL(k.address1,'') as supplier_address1,IFNULL(k.address2,'') as supplier_address2,k.contactno1 as kar_mobile,

        IFNULL(k.pincode,'') as supplier_pincode,k.gst_number as kar_gst_number,sk.name as supplier_state_name,ck.name as supplier_city_name,date_format(sd.received_date,'%d-%m-%Y')as received_date

        FROM ret_stock_issue i

        LEFT JOIN ret_stock_issue_detail sd ON sd.id_stock_issue=i.id_stock_issue

        LEFT JOIN branch br ON br.id_branch=i.id_branch

        LEFT JOIN employee emp ON emp.id_employee=i.id_employee

        LEFT JOIN customer cus ON cus.id_customer=i.id_customer

        LEFT JOIN ret_karigar k ON k.id_karigar = i.id_karigar

        Left join address ad on  ad.id_address = cus.id_address

        Left join city ct on ct.id_city = ad.id_city

        Left join state st on st.id_state = ad.id_state

        LEFT JOIN state sk ON sk.id_state = k.id_state

        LEFT JOIN city ck ON ck.id_city = k.id_city

        Left join country co on co.id_country = ad.id_country

        where i.id_stock_issue=".$id."");

        //  print_r($this->db->last_query());exit;

        return $sql->row_array();

	}



	function get_issue_item_details($id,$issue_type,$repair_type,$received_time,$stock_type)

	{

	    //$issue_type =>1-Repair 2 - Marketing 3 - Photoshooting

	    //$repair_type =>1-Stock Repair 2 - Customer Repair

	        $returnData= array();

	       if($stock_type==1){ //TAGED

            $sql=$this->db->query("SELECT IFNULL(SUM(tag.piece),0) as total_items,IFNULL(SUM(tag.gross_wt),0) as gross_wt,IFNULL(SUM(tag.net_wt),0) as issue_weight,i.id_stock_issue,p.product_name,des.design_name,

            s.sub_design_name,c.name as category_name,m.tax_percentage as tax_percentage,t.tgi_calculation as tgi_calculation,c.id_metal,d.rate_per_gram,date_format(d.received_date,'%d-%m-%Y') as received_date,p.cat_id,

            c.hsn_code

            FROM ret_stock_issue i

            LEFT JOIN ret_stock_issue_detail d ON d.id_stock_issue=i.id_stock_issue

            LEFT JOIN ret_taging tag ON tag.tag_id=d.tag_id

            LEFT JOIN ret_product_master p ON p.pro_id=tag.product_id

            left join ret_category c on c.id_ret_category=p.cat_id

            left join ret_taxgroupitems t on t.tgi_tgrpcode = c.tgrp_id

            left join ret_taxmaster m on m.tax_id = t.tgi_taxcode

            LEFT JOIN ret_design_master des ON des.design_no=tag.design_id

            LEFT JOIN ret_sub_design_master s ON s.id_sub_design=tag.id_sub_design

            WHERE i.id_stock_issue=".$id."

            ".($received_time!='' ? " and d.received_time=".$received_time."" :'')."

            GROUP BY p.cat_id");

            $result = $sql->result_array();



            foreach($result as $items)

            {

                $returnData[]=array(

                    'total_items'            =>$items['total_items'],

                    'gross_wt'               =>$items['gross_wt'],

                    'issue_weight'           =>$items['issue_weight'],

                    'id_stock_issue'         =>$items['id_stock_issue'],

                    'product_name'           =>$items['product_name'],

                    'design_name'            =>$items['design_name'],

                    'sub_design_name'        =>$items['sub_design_name'],

                    'category_name'          =>$items['category_name'],

                    'tax_percentage'         =>$items['tax_percentage'],

                    'tgi_calculation'        =>$items['tgi_calculation'],

                    'id_metal'               =>$items['id_metal'],

                    'rate_per_gram'          =>$items['rate_per_gram'],

                    'received_date'          =>$items['received_date'],

                    'hsn_code'               =>$items['hsn_code'],

                    'tag_id'                 =>$items['tag_id'],

                    'stn_details'            =>$this->get_StoneDetails($items['cat_id'],$items['id_stock_issue'],$received_time),

                    'other_metal_details'    =>$this->get_other_metal_details($items['cat_id'],$items['id_stock_issue'],$received_time),

                );



            }
        }else if($stock_type==2){ //NON TAGED

            $sql=$this->db->query("SELECT IFNULL(SUM(d.piece),0) as total_items,IFNULL(SUM(d.gross_wt),0) as gross_wt,IFNULL(SUM(d.net_wt),0) as issue_weight,i.id_stock_issue,p.product_name,des.design_name,

            s.sub_design_name,c.name as category_name,m.tax_percentage as tax_percentage,t.tgi_calculation as tgi_calculation,c.id_metal,d.rate_per_gram,date_format(d.received_date,'%d-%m-%Y') as received_date,p.cat_id,

            c.hsn_code
            FROM ret_stock_issue i
            LEFT JOIN ret_stock_issue_detail d ON d.id_stock_issue=i.id_stock_issue
            LEFT JOIN ret_nontag_item nt ON nt.id_nontag_item=d.id_non_tag_item
            LEFT JOIN ret_product_master p ON p.pro_id=nt.product
            left join ret_category c on c.id_ret_category=p.cat_id
            left join ret_taxgroupitems t on t.tgi_tgrpcode = c.tgrp_id
            left join ret_taxmaster m on m.tax_id = t.tgi_taxcode
            LEFT JOIN ret_design_master des ON des.design_no=nt.design
            LEFT JOIN ret_sub_design_master s ON s.id_sub_design=nt.id_sub_design
            WHERE i.id_stock_issue=".$id."

            ".($received_time!='' ? " and d.received_time=".$received_time."" :'')."

            GROUP BY p.cat_id");

            $result = $sql->result_array();


            // echo "<pre>";print_r($this->db->last_query());exit;



            foreach($result as $items)

            {

                $returnData[]=array(

                    'total_items'            =>$items['total_items'],

                    'gross_wt'               =>$items['gross_wt'],

                    'issue_weight'           =>$items['issue_weight'],

                    'id_stock_issue'         =>$items['id_stock_issue'],

                    'product_name'           =>$items['product_name'],

                    'design_name'            =>$items['design_name'],

                    'sub_design_name'        =>$items['sub_design_name'],

                    'category_name'          =>$items['category_name'],

                    'tax_percentage'         =>$items['tax_percentage'],

                    'tgi_calculation'        =>$items['tgi_calculation'],

                    'id_metal'               =>$items['id_metal'],

                    'rate_per_gram'          =>$items['rate_per_gram'],

                    'received_date'          =>$items['received_date'],

                    'hsn_code'               =>$items['hsn_code'],

                    'tag_id'                 =>$items['tag_id'],

                    'stn_details'            =>$this->get_StoneDetails($items['cat_id'],$items['id_stock_issue'],$received_time),

                    'other_metal_details'    =>$this->get_other_metal_details($items['cat_id'],$items['id_stock_issue'],$received_time),

                );
        }

	}
	    return $returnData;
	}
	function get_issue_item_tag($id,$issue_type,$repair_type,$received_time,$stock_type)

	{

	    //$issue_type =>1-Repair 2 - Marketing 3 - Photoshooting

	    //$repair_type =>1-Stock Repair 2 - Customer Repair

	        $returnData= array();

	       if($stock_type==1){ //TAGED

            $sql=$this->db->query("SELECT IFNULL((tag.piece),0) as total_items,IFNULL((tag.gross_wt),0) as gross_wt,IFNULL((tag.net_wt),0) as issue_weight,i.id_stock_issue,p.product_name,des.design_name,

            s.sub_design_name,c.name as category_name,m.tax_percentage as tax_percentage,t.tgi_calculation as tgi_calculation,c.id_metal,d.rate_per_gram,date_format(d.received_date,'%d-%m-%Y') as received_date,p.cat_id,ifnull(tag.tag_code,'') as tag_code,

            c.hsn_code

            FROM ret_stock_issue i

            LEFT JOIN ret_stock_issue_detail d ON d.id_stock_issue=i.id_stock_issue

            LEFT JOIN ret_taging tag ON tag.tag_id=d.tag_id

            LEFT JOIN ret_product_master p ON p.pro_id=tag.product_id

            left join ret_category c on c.id_ret_category=p.cat_id

            left join ret_taxgroupitems t on t.tgi_tgrpcode = c.tgrp_id

            left join ret_taxmaster m on m.tax_id = t.tgi_taxcode

            LEFT JOIN ret_design_master des ON des.design_no=tag.design_id

            LEFT JOIN ret_sub_design_master s ON s.id_sub_design=tag.id_sub_design

            WHERE i.id_stock_issue=".$id."

            ".($received_time!='' ? " and d.received_time=".$received_time."" :'')." ");

            $result = $sql->result_array();



            foreach($result as $items)

            {

                $returnData[]=array(

                    'total_items'            =>$items['total_items'],

                    'gross_wt'               =>$items['gross_wt'],

                    'issue_weight'           =>$items['issue_weight'],

                    'id_stock_issue'         =>$items['id_stock_issue'],

                    'product_name'           =>$items['product_name'],

                    'design_name'            =>$items['design_name'],

                    'sub_design_name'        =>$items['sub_design_name'],

                    'category_name'          =>$items['category_name'],

                    'tax_percentage'         =>$items['tax_percentage'],

                    'tgi_calculation'        =>$items['tgi_calculation'],

                    'tag_code'               =>$items['tag_code'],

                    'id_metal'               =>$items['id_metal'],

                    'rate_per_gram'          =>$items['rate_per_gram'],

                    'received_date'          =>$items['received_date'],

                    'hsn_code'               =>$items['hsn_code'],

                    'tag_id'                 =>$items['tag_id'],

                    'stn_details'            =>$this->get_StoneDetails($items['cat_id'],$items['id_stock_issue'],$received_time),

                    'other_metal_details'    =>$this->get_other_metal_details($items['cat_id'],$items['id_stock_issue'],$received_time),

                );



            }
        }else if($stock_type==2){ //NON TAGED

            $sql=$this->db->query("SELECT IFNULL(SUM(d.piece),0) as total_items,IFNULL(SUM(d.gross_wt),0) as gross_wt,IFNULL(SUM(d.net_wt),0) as issue_weight,i.id_stock_issue,p.product_name,des.design_name,

            s.sub_design_name,c.name as category_name,m.tax_percentage as tax_percentage,t.tgi_calculation as tgi_calculation,c.id_metal,d.rate_per_gram,date_format(d.received_date,'%d-%m-%Y') as received_date,p.cat_id,

            c.hsn_code
            FROM ret_stock_issue i
            LEFT JOIN ret_stock_issue_detail d ON d.id_stock_issue=i.id_stock_issue
            LEFT JOIN ret_nontag_item nt ON nt.id_nontag_item=d.id_non_tag_item
            LEFT JOIN ret_product_master p ON p.pro_id=nt.product
            left join ret_category c on c.id_ret_category=p.cat_id
            left join ret_taxgroupitems t on t.tgi_tgrpcode = c.tgrp_id
            left join ret_taxmaster m on m.tax_id = t.tgi_taxcode
            LEFT JOIN ret_design_master des ON des.design_no=nt.design
            LEFT JOIN ret_sub_design_master s ON s.id_sub_design=nt.id_sub_design
            WHERE i.id_stock_issue=".$id."

            ".($received_time!='' ? " and d.received_time=".$received_time."" :'')."

            GROUP BY p.cat_id");

            $result = $sql->result_array();


            // echo "<pre>";print_r($this->db->last_query());exit;



            foreach($result as $items)

            {

                $returnData[]=array(

                    'total_items'            =>$items['total_items'],

                    'gross_wt'               =>$items['gross_wt'],

                    'issue_weight'           =>$items['issue_weight'],

                    'id_stock_issue'         =>$items['id_stock_issue'],

                    'product_name'           =>$items['product_name'],

                    'design_name'            =>$items['design_name'],

                    'sub_design_name'        =>$items['sub_design_name'],

                    'category_name'          =>$items['category_name'],

                    'tax_percentage'         =>$items['tax_percentage'],

                    'tgi_calculation'        =>$items['tgi_calculation'],

                    'id_metal'               =>$items['id_metal'],

                    'rate_per_gram'          =>$items['rate_per_gram'],

                    'received_date'          =>$items['received_date'],

                    'hsn_code'               =>$items['hsn_code'],

                    'tag_id'                 =>$items['tag_id'],

                    'stn_details'            =>$this->get_StoneDetails($items['cat_id'],$items['id_stock_issue'],$received_time),

                    'other_metal_details'    =>$this->get_other_metal_details($items['cat_id'],$items['id_stock_issue'],$received_time),

                );
        }

	}
	    return $returnData;
	}



	function get_tag_scan_details($data)

	{

	    $return_data=array();

	    $tag_code=$this->input->post('tag_code');

        $old_tag_code=$this->input->post('old_tag_code');



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

                            WHERE tag.tag_status = 0 and tag.id_orderdetails is NULL AND (si.status = 2 OR si.status = 3 OR si.tag_id IS NULL)

                            and ".($old_tag_code!='' ? " tag.old_tag_id='".$old_tag_code."'" : ($tag_code!='' ? "tag_code='".$tag_code."'" :'') )."

                            ".($data['id_branch'] !='' ? " and tag.current_branch = ".$data['id_branch']."" :'')."

                            ".($data['id_metal'] !='' ? " and c.id_metal = ".$data['id_metal']."" :'')."

                            ".($data['id_section'] !='' ? " and tag.id_section = ".$data['id_section']."" :'')."


                           GROUP BY tag.tag_id");

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
        amount,s.stone_id, amount as price, IFNULL(certification_cost,0) as certification_cost, is_apply_in_lwt,s.uom_id,
        s.stone_cal_type
        FROM ret_taging_stone as s
        LEFT JOIN ret_stone as st ON st.stone_id = s.stone_id
        LEFT JOIN ret_uom as uom ON uom.uom_id = s.uom_id
        WHERE s.tag_id = '".$tag_id."'");

        return $sql->result_array();

	}

	function get_receipt_tag_scan_details($data)

	{

	    $return_data=array();

        $tag_code=$this->input->post('tag_code');

        $old_tag_code=$this->input->post('old_tag_code');

        $data = $this->db->query("SELECT tag.tag_id as value, tag_code as label, tag.tag_type, tag_lot_id, design_id, cost_center,

                            tag.purity, tag.size, uom, piece, tag.less_wt, tag.net_wt, tag.gross_wt, tag.calculation_based_on,

                            retail_max_wastage_percent,tag_mc_value,tag_mc_type, halmarking, sales_value, tag.tag_status,

                             product_name, product_short_code, c.id_ret_category as catid, c.name as catname,

                            tag.product_id as lot_product, pur.purity as purname,lot_inw.lot_received_at,

                            tag.tag_id,pro.sales_mode,tag.item_rate,tag.current_branch,

                            des.design_name,tag.tag_mark, tag.id_sub_design as subdesignid, sdes.sub_design_name as sub_design_name,

                            IFNULL(tag.id_section,'') as id_section, tag.old_tag_id,IFNULL(stn.stone_price,0) as stone_price,IFNULL(other_met.tag_other_itm_amount,0) as othermetal_amount,

                            d.rate_per_gram


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

                            LEFT JOIN (SELECT IFNULL(SUM(st.amount),0) as stone_price,st.tag_id

                                FROM ret_taging_stone st

                                GROUP BY st.tag_id) as stn ON stn.tag_id = tag.tag_id



                             LEFT JOIN (SELECT IFNULL(SUM(m.tag_other_itm_amount),0) as tag_other_itm_amount,m.tag_other_itm_tag_id

                                From ret_tag_other_metals m

                                Group by m.tag_other_itm_tag_id) as other_met ON other_met.tag_other_itm_tag_id = tag.tag_id

                            WHERE tag.tag_status = 7 and tag.id_orderdetails is NULL

                            and ".($old_tag_code!='' ? " tag.old_tag_id='".$old_tag_code."'" : ($tag_code!='' ? "tag.tag_code='".$tag_code."'" :'') )."

                            ");

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

                                 'value'                        =>$tag['value'],

                                 'id_section'                   =>$tag['id_section'],

                                 'stone_price'                  =>$tag['stone_price'],

                                 'othermetal_amount'            =>$tag['othermetal_amount'],

                                 'rate_per_gram'                =>$tag['rate_per_gram'],

                                 'old_tag_id'                   =>$tag['old_tag_id'],

                                );

        }

        return $return_data;

	}





	function get_stock_issue_type()

	{

	    $sql=$this->db->query("SELECT * FROM `ret_stock_issue_types` WHERE status=1");

	    return $sql->result_array();

	}



	function stock_issue_type_detail($id)

	{

	    $sql=$this->db->query("SELECT * FROM `ret_stock_issue_types` WHERE id_stock_issue_type=".$id."");

	    return $sql->row_array();

	}





	function get_StockIssuedItems($data)

	{

	    $returnData=array();

        if($data['stock_type']==1){

            $sql=$this->db->query("SELECT id_stock_issue , issue_no FROM `ret_stock_issue` WHERE status=1 and stock_type=".$data['stock_type']."");

	        $result= $sql->result_array();

            foreach($result as $items)
            {
                $tag_details=$this->stock_issue_tags($items['id_stock_issue']);
                if(sizeof($tag_details))
                {

                    $returnData[]=array(

                        'id_stock_issue'=>$items['id_stock_issue'],

                        'issue_no'      =>$items['issue_no'],

                        'tag_details'   =>$tag_details,

                    );
                }
            }
        }else if($data['stock_type']==2){

            $sql=$this->db->query("SELECT id_stock_issue , issue_no FROM `ret_stock_issue` WHERE status=1 and stock_type=".$data['stock_type']."");

	        $result= $sql->result_array();

            foreach($result as $items){
                $ntag_details=$this->stock_issue_nontags($items['id_stock_issue']);
                if(sizeof($ntag_details)){
                    $returnData[]=array(

                        'id_stock_issue'=>$items['id_stock_issue'],

                        'issue_no'      =>$items['issue_no'],

                        'nontag_details'   =>$ntag_details,

                    );
                }
            }

        }

	    return $returnData;

	}





	// function stock_issue_tags($id_stock_issue)

	// {

	//     $sql=$this->db->query("SELECT t.tag_id,t.tag_code,t.old_tag_id

    //     FROM ret_stock_issue_detail d

    //     LEFT JOIN ret_taging t ON t.tag_id=d.tag_id

    //     WHERE d.status=1 and id_stock_issue=".$id_stock_issue."");

	//     return $sql->result_array();

	// }

    function stock_issue_tags($id_stock_issue)

	{

	    $sql=$this->db->query("SELECT tag.tag_id,tag.tag_code,tag.old_tag_id,tag.tag_type,tag_lot_id, design_id,cost_center,



        tag.purity, tag.size, uom, tag.piece, tag.less_wt, tag.net_wt, tag.gross_wt, tag.calculation_based_on,



        retail_max_wastage_percent,tag_mc_value,tag_mc_type, halmarking, sales_value, tag.tag_status,



        product_name, product_short_code, c.id_ret_category as catid, c.name as catname,



        tag.product_id as lot_product, pur.purity as purname,lot_inw.lot_received_at,



        tag.tag_id,pro.sales_mode,tag.item_rate,tag.current_branch,



        des.design_name,tag.tag_mark, tag.id_sub_design as subdesignid, sdes.sub_design_name as sub_design_name,



        IFNULL(stn.stone_price,0) as stone_price,IFNULL(other_met.tag_other_itm_amount,0) as othermetal_amount,



        d.rate_per_gram



        FROM ret_stock_issue_detail d



        LEFT JOIN ret_taging tag ON tag.tag_id=d.tag_id



        Left join ret_lot_inwards_detail lot_det ON tag.id_lot_inward_detail = lot_det.id_lot_inward_detail



        LEFT JOIN ret_lot_inwards as lot_inw ON lot_inw.lot_no = lot_det.lot_no



        LEFT JOIN ret_product_master as pro ON pro.pro_id = tag.product_id



        LEFT JOIN ret_design_master des on des.design_no=tag.design_id



        left join ret_sub_design_master sdes on sdes.id_sub_design=tag.id_sub_design



        LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity



        left join ret_category c on c.id_ret_category=pro.cat_id



        left join metal mt on mt.id_metal=c.id_metal



        LEFT JOIN (SELECT IFNULL(SUM(st.amount),0) as stone_price,st.tag_id

                                FROM ret_taging_stone st

                                GROUP BY st.tag_id) as stn ON stn.tag_id = tag.tag_id



        LEFT JOIN (SELECT IFNULL(SUM(m.tag_other_itm_amount),0) as tag_other_itm_amount,m.tag_other_itm_tag_id

                                From ret_tag_other_metals m

                                Group by m.tag_other_itm_tag_id) as other_met ON other_met.tag_other_itm_tag_id = tag.tag_id



        WHERE d.status=1 and id_stock_issue=".$id_stock_issue."");



        return $sql->result_array();



	}



	function getTagDetails($tag_id)

	{

	    $sql = $this->db->query("select IFNULL(t.id_section,'') as id_section, t.gross_wt, t.net_wt, t.piece FROM ret_taging t where t.tag_id=".$tag_id."");

	    return $sql->row_array();

	}



    function get_other_metal_details($cat_id,$id_stock_issue,$received_time)

    {

        $sql = $this->db->query("SELECT IFNULL(SUM(m.tag_other_itm_amount),0) as tag_other_itm_amount,IFNULL(SUM(m.tag_other_itm_grs_weight),0) as othermetal_weight,c.name as catname

        FROM ret_stock_issue_detail d

        LEFT JOIN ret_stock_issue i ON i.id_stock_issue = d.id_stock_issue

        LEFT JOIN ret_taging t ON t.tag_id = d.tag_id

        LEFT JOIN ret_tag_other_metals m ON m.tag_other_itm_tag_id = t.tag_id

        LEFT JOIN ret_product_master p ON p.pro_id = t.product_id

        LEFT JOIN ret_category c ON c.id_ret_category = m.tag_other_itm_metal_id

        WHERE i.id_stock_issue = ".$id_stock_issue." AND p.cat_id = ".$cat_id." and m.tag_other_itm_metal_id IS NOT NULL

        ".($received_time!='' ? " and d.received_time=".$received_time."" :'')."

        GROUP BY m.tag_other_itm_metal_id");

        return $sql->result_array();

    }



    function get_StoneDetails($cat_id,$id_stock_issue,$received_time)

	{

	    $sql=$this->db->query("SELECT po_stn.tag_id,IFNULL(SUM(po_stn.pieces),0) as stone_pcs,IFNULL(SUM(po_stn.wt),0) as stone_wt,

        IFNULL((po_stn.rate_per_gram),0) as stone_rate,IFNULL(SUM(po_stn.amount),0) as stone_amount,s.stone_name,

        uom.uom_name

        FROM ret_taging_stone po_stn

        LEFT JOIN ret_stone s ON s.stone_id = po_stn.stone_id

        LEFT JOIN ret_uom uom on uom.uom_id=po_stn.uom_id

        LEFT JOIN ret_taging t ON t.tag_id = po_stn.tag_id

        LEFT JOIN ret_product_master p ON p.pro_id = t.product_id

        LEFT JOIN ret_stock_issue_detail d ON d.tag_id = t.tag_id

        WHERE p.cat_id =".$cat_id." and d.id_stock_issue=".$id_stock_issue."

        ".($received_time!='' ? " and d.received_time=".$received_time."" :'')."

        group by p.cat_id");

        //print_r($this->db->last_query());exit;

        return $sql->result_array();

	}

    function get_nontag_scan_details($data)
    {
        $sql = ("SELECT CONCAT(design_code,' - ',design_name) as design_name,CONCAT(product_short_code ,' - ',product_name) as product_name,
	    (nt.gross_wt - ifnull(bt.grs_wt,0) ) as gross_wt,(nt.net_wt - ifnull(bt.net_wt,0) ) as net_wt,(nt.no_of_piece - ifnull(bt.pieces,0) ) as no_of_piece,nt.id_nontag_item,'' as id_lot_inward_detail,IFNULL(nt.id_section,'') as id_section,IFNULL(rs.section_name,'') as section_name,
	    nt.id_sub_design,subDes.sub_design_name,nt.product,nt.design,nt.id_sub_design,nt.id_section
        FROM  ret_nontag_item nt
        Left join ret_product_master p on p.pro_id = nt.product
        Left join ret_design_master d on d.design_no = nt.design
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design = nt.id_sub_design
        left join ret_section rs on rs.id_section = nt.id_section
        Left join (SELECT id_nontag_item,sum(grs_wt) as grs_wt,sum(net_wt) as net_wt,sum(pieces) as pieces,status FROM `ret_branch_transfer` WHERE status != 3 and status != 1 AND status != 4 GROUP BY id_nontag_item) bt on bt.id_nontag_item=nt.id_nontag_item and bt.status != 3
        WHERE branch=".$data['id_branch']." 
        ".($data['prodId'] != '' ? ' and nt.product='.$data['prodId']: '')."
        ".($data['id_section'] != '' ? ' and nt.id_section='.$data['id_section']: '')."
        group by id_nontag_item");
        // print_r($sql);exit;
        $res =  $this->db->query($sql)->result_array();
        foreach($res as $r){
            if($r['gross_wt'] > 0){
                $result[] = array(
                "design_name"           => ($r['design_name'] == NULL ? '-' : $r['design_name']),
                "section_name"          => $r['section_name'],
                "product_name"          => $r['product_name'],
                "sub_design_name"       => $r['sub_design_name'],
                "gross_wt"              => $r['gross_wt'],
                "net_wt"                => $r['net_wt'],
                "no_of_piece"           => $r['no_of_piece'],
                "id_nontag_item"        => $r['id_nontag_item'],
                "id_lot_inward_detail"  => $r['id_lot_inward_detail'],
                "id_sub_design"         => $r['id_sub_design'],
                "product"               => $r['product'],
                "design"                => $r['design'],
                "id_section"            => $r['id_section'],
                );
            }
        }

        // echo "<pre>";print_r($result);exit;

		return $result;
    }


    function updateNTData($data,$arith)
    {
		$sql = "UPDATE ret_nontag_item SET no_of_piece=(no_of_piece".$arith." ".$data['no_of_piece']."),gross_wt=(gross_wt".$arith." ".$data['gross_wt']."),net_wt=(net_wt".$arith." ".$data['net_wt']."),updated_by=".$data['updated_by'].",updated_on='".$data['updated_on']."' WHERE id_nontag_item=".$data['id_nontag_item'];
		$status = $this->db->query($sql);
		return $status;
	}


	function stock_issue_nontags($id_stock_issue)
	{

        $sql = $this->db->query("SELECT nt.id_nontag_item,nt.branch issued_branch,nt.id_section,nt.product,nt.design,nt.id_sub_design,sd.id_stock_issue_detail,sd.id_stock_issue,sd.piece no_of_piece,sd.gross_wt,sd.net_wt,sd.rate_per_gram,concat(p.product_short_code,'-',p.product_name) product_name,
        sec.id_section,sec.section_name,des.design_no as design_id,concat(des.design_code,'-',des.design_name) design_name,s.issue_type as issued_type FROM ret_stock_issue_detail sd
        LEFT JOIN ret_stock_issue s ON s.id_stock_issue=sd.id_stock_issue
        LEFT JOIN ret_nontag_item nt on nt.id_nontag_item=sd.id_non_tag_item
        LEFT JOIN ret_product_master p ON p.pro_id=nt.product
        LEFT JOIN ret_design_master des ON des.design_no=nt.design
        LEFT JOIN ret_sub_design_master subdes ON subdes.id_sub_design=nt.id_sub_design
        LEFT JOIN ret_section sec ON sec.id_section=nt.id_section
        WHERE s.stock_type=2 and s.status=1  and sd.status=1 and s.id_stock_issue=".$id_stock_issue."");
        return $sql->result_array();
    }

    



}

?>