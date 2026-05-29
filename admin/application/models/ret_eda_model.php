<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ret_eda_model extends CI_Model

{

	function __construct()

    {

        parent::__construct();

    }

    // General Functions

    public function insertData($data,$table)

    {

    	$insert_flag = 0;

		$insert_flag = $this->db->insert($table, $data);

		return ($insert_flag == 1 ? $this->db->insert_id(): 0);

	}

	public function updateData($data, $id_field, $id_value, $table)

    {    

	    $edit_flag = 0;

	    $this->db->where($id_field, $id_value);

		$edit_flag = $this->db->update($table,$data);

		return ($edit_flag==1?$id_value:0);

	}	 

	public function deleteData($id_field,$id_value,$table)

    {

        $this->db->where($id_field, $id_value);

        $status= $this->db->delete($table); 

		return $status;

	}

	

	function checkNonTagItemExist($data){

		$r = array("status" => FALSE);

        $sql = "SELECT id_nontag_item FROM ret_nontag_item WHERE product=".$data['id_product']." ".($data['id_design']!='' ? " and design=".$data['id_design']."" :'')." AND branch=".$data['id_branch']; 		

        

        $res = $this->db->query($sql);

		if($res->num_rows() > 0){

			$r = array("status" => TRUE, "id_nontag_item" => $res->row()->id_nontag_item); 

		}else{

			$r = array("status" => FALSE, "id_nontag_item" => ""); 

		} 

		return $r;

	}

	function updateNTData($data,$arith){ 

		$sql = "UPDATE ret_nontag_item SET no_of_piece=(no_of_piece".$arith." ".$data['no_of_piece']."),gross_wt=(gross_wt".$arith." ".$data['gross_wt']."),net_wt=(net_wt".$arith." ".$data['net_wt']."),updated_by=".$data['updated_by'].",updated_on='".$data['updated_on']."' WHERE id_nontag_item=".$data['id_nontag_item'];  

		$status = $this->db->query($sql);

		return $status;

	}

	

	public function get_eda_list()

	{

	}

	function ajax_getEdaList($id_branch)

    {

		$from_date 	= date("Y-m-d");

		$to_date 	= date("Y-m-d");

        $return_data=array();

		if($id_branch!='' && $id_branch>0)

        {

            $data=$this->getBranchDayClosingData($id_branch);

        }else{

            $id_branch = 1;

            $data=$this->getBranchDayClosingData($id_branch);

        }

		$uid=$this->session->userdata('uid');

		$sql = $this->db->query("SELECT estimation_id, esti_no,cus.mobile,

					date_format(estimation_datetime, '%d-%m-%Y %H:%i') as estimation_datetime, 

					firstname, pro.edasalesamt as total_cost, if(esti_for = 1,'Customer','Branch Transfer') as esti_for,IFNULL(bill.bill_id,old_bill.bill_id) as bill_id,

					IFNULL(bill.bill_no,old_bill.bill_no) as bill_no,

					IFNULL(pro.product_name,'') as product_name, est.estimate_final_amt, is_eda_approved

					

					FROM ret_estimation as est 

					LEFT JOIN customer as cus ON cus.id_customer = est.cus_id 

					

					LEFT JOIN (SELECT e.esti_id,b.bill_id,b.bill_no

					FROM ret_estimation_items e 

					LEFT JOIN ret_bill_details d ON d.esti_item_id=e.est_item_id

					LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

					WHERE b.bill_status=1

					GROUP by b.bill_id) as bill ON bill.esti_id=est.estimation_id

					

					LEFT JOIN (SELECT sum(item_cost - item_total_tax) as edasalesamt, GROUP_CONCAT(concat(m.metal),'-',p.product_name)  as product_name,e.esti_id

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

					GROUP by b.bill_id) as old_bill ON old_bill.est_id=est.estimation_id

					WHERE ".(($id_branch==0 || $id_branch=='') ? " date(estimation_datetime) = '".date("Y-m-d")."' " : " date(estimation_datetime) = '".$data['entry_date']."' ")." 

				

					".($id_branch!=0 && $id_branch!='' ? " and est.id_branch=".$id_branch."" :'')."

					AND is_eda = 1 AND (is_eda_approved = 0 || is_eda_approved = 1)

					ORDER BY est.estimation_id desc ");

           // echo $this->db->last_query();exit;

			$result= $sql->result_array();

			foreach($result as $item)

			{

				$return_data[]=$item;

			}

			return $return_data;

	}

	function getBranchDayClosingData($id_branch)

    {

	    $sql = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing where id_branch=".$id_branch);  

	    return $sql->row_array();

	}

	

	function getEstDetails($estId)

	{

	    $sql = $this->db->query("SELECT est_item_id, itm.esti_id, itm.tag_id, est.id_branch, is_non_tag, product_id, design_id, id_sub_design, 

	                            ifnull(piece,0) as piece, ifnull(less_wt,0) as less_wt, ifnull(net_wt, 0) as net_wt, gross_wt, est.est_date  

	                            FROM ret_estimation_items itm 

	                            LEFT JOIN ret_estimation as est ON est.estimation_id = itm.esti_id  

	                            WHERE itm. esti_id = '".$estId."' AND est.is_eda_approved = 0");

	    $result = $sql->result_array();

	    

	   foreach($result as $rkey => $rval){

	        if(!empty($rval['tag_id'])){

	            $tag_query = $this->db->query("SELECT * FROM `ret_estimation_items` as estitm 

	                            LEFT JOIN ret_estimation as est ON est.estimation_id = estitm.esti_id

	                            LEFT JOIN ret_taging tag ON tag.tag_id = estitm.tag_id

	                            WHERE tag.tag_id = '".$rval['tag_id']."' and tag.tag_status = 10");

	           //echo $this->db->last_query();exit;

	           if($tag_query->num_rows() > 0){

	               //echo $this->db->last_query();exit;

	               unset($result[$rkey]);

	           }

	        }

	    }

	    

	    return $result; 

	}
	//reports
	function ajax_getSalesList($data)
	{
	    $return_data = [];
	    
	   /* $multiple_id_branch = implode(' , ', $data['id_branch']);
		if($multiple_id_branch != '')
		{
			$branch = $multiple_id_branch;
		}else{
			$branch = $data['id_branch'];
		}
		*/
		$branch = $data['id_branch'];
	    
	    $est_details = $this->db->query("SELECT e.estimation_id,e.esti_no,date_format(e.estimation_datetime,'%d-%m-%Y') as est_date,br.name as branch_name,cus.firstname as cus_name,cus.mobile,
	    e.total_cost
        FROM ret_estimation e 
        LEFT JOIN branch br ON br.id_branch = e.id_branch
        LEFT JOIN ret_day_closing d ON d.id_branch = e.id_branch
        LEFT JOIN customer cus ON cus.id_customer = e.cus_id
        WHERE e.is_eda = 1 and e.is_eda_approved = 1 AND date(d.entry_date) = date(e.estimation_datetime)
        ".($branch!='' && $branch !='0' ? " and e.id_branch in (".$branch.") " :'' )."
        ");
        
        $est_result = $est_details->result_array();
        
        foreach($est_result as $items)
        {
            $sales_details = $this->get_sales_details($items['estimation_id'],$data['id_metal'],$data['id_branch']);
            $pur_details   = $this->get_purchase_details($items['estimation_id'],$data['id_metal'],$data['id_branch']);
            if($data['id_metal']=='' || $data['id_metal']==0)
            {
                $return_data[]=array(
                                'estimation_id' =>$items['estimation_id'],
                                'esti_no'       =>$items['esti_no'],
                                'est_date'      =>$items['est_date'],
                                'branch_name'   =>$items['branch_name'],
                                'cus_name'      =>$items['cus_name'],
                                'mobile'        =>$items['mobile'],
                                'total_cost'    =>$items['total_cost'],
                                'sales_details' =>$sales_details,
                                'purchase_details'=>$pur_details,
                                );
            }
            else if($data['id_metal']!='')
            {
                if(sizeof($sales_details) >0 || sizeof($pur_details) > 0)
                {
                    $return_data[]=array(
                                'estimation_id' =>$items['estimation_id'],
                                'esti_no'       =>$items['esti_no'],
                                'est_date'      =>$items['est_date'],
                                'branch_name'   =>$items['branch_name'],
                                'cus_name'      =>$items['cus_name'],
                                'mobile'        =>$items['mobile'],
                                'sales_details' =>$sales_details,
                                'purchase_details'=>$pur_details,
                                );
                }
            }
            
        }
        return $return_data;
	}
	
	function get_sales_details($estimation_id,$id_metal,$id_branch)
	{
	    /*$multiple_id_branch = implode(' , ', $id_branch);
		if($multiple_id_branch != '')
		{
			$branch = $multiple_id_branch;
		}else{
			$branch = $id_branch;
		}*/

		$branch = $id_branch;

	    $sales_query = $this->db->query("SELECT est.esti_no,br.name,IFNULL(t.tag_code,'-') as tag_code,p.product_name,IFNULL(des.design_name,'-') as design_name,
	        IFNULL(subDes.sub_design_name,'-') as sub_design_name,e.piece,IFNULL(e.gross_wt,0) as gross_wt,IFNULL(e.net_wt,0) as net_wt,IFNULL(e.less_wt,0) as less_wt,e.item_total_tax,e.item_cost,
            date_format(est.estimation_datetime,'%d-%m-%Y') as est_date,c.name as category_name,mt.metal as metal_name,IFNULL(e.discount,0) as discount,est.estimation_id,
            cus.firstname as cus_name,cus.mobile,d.entry_date,e.is_non_tag,IFNULL(t.hu_id,'')as hu_id,IFNULL(t.hu_id2,'') as hu_id2,e.est_rate_per_grm,c.id_metal, ((e.est_rate_per_grm + e.mc_value) * IFNULL(e.net_wt,0)) as sale_value
            FROM ret_estimation_items e
            LEFT JOIN ret_estimation est ON est.estimation_id = e.esti_id
            LEFT JOIN branch br ON br.id_branch = est.id_branch
            LEFT JOIN ret_day_closing d ON d.id_branch = est.id_branch
            LEFT JOIN ret_taging t ON t.tag_id = e.tag_id
            LEFT JOIN ret_product_master p ON p.pro_id = e.product_id
            LEFT JOIN ret_design_master des ON des.design_no = e.design_id
            LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design = e.id_sub_design
            LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
            LEFT JOIN metal mt ON mt.id_metal = c.id_metal
            LEFT JOIN customer cus ON cus.id_customer = est.cus_id
            WHERE est.estimation_id=".$estimation_id."
            ".($id_metal!='' && $id_metal!=0 ? " and mt.id_metal=".$id_metal."" :'')."
            ".($branch!='' && $branch !='0' ? " and est.id_branch in (".$branch.") " :'' )."
            ");
            //echo $this->db->last_query();exit;
        return $sales_query->result_array();
	}

	function get_purchase_details($estimation_id,$id_metal,$id_branch)
	{
	    /*$multiple_id_branch = implode(' , ', $id_branch);
		if($multiple_id_branch != '')
		{
			$branch = $multiple_id_branch;
		}else{
			$branch = $id_branch;
		}*/

		$branch = $id_branch;

	    $pur_query = $this->db->query("SELECT s.piece,s.gross_wt,(IFNULL(s.dust_wt,0)+IFNULL(s.stone_wt,0)) as less_wt,s.net_wt,s.amount,t.metal_type,c.old_metal_cat,
	    mt.metal as metal_name,((s.gross_wt - IFNULL(s.dust_wt,0) )* s.rate_per_gram) as purchase_amt
        FROM ret_estimation_old_metal_sale_details s
        LEFT JOIN ret_estimation est ON est.estimation_id = s.est_id
        LEFT JOIN ret_old_metal_type t ON t.id_metal_type = s.id_old_metal_type
        LEFT JOIN ret_old_metal_category c ON c.id_old_metal_cat = s.id_old_metal_category
        LEFT JOIN metal mt ON mt.id_metal = s.id_category
        WHERE est.estimation_id=".$estimation_id."
        ".($id_metal!='' && $id_metal!=0 ? " and mt.id_metal=".$id_metal."" :'')."
        ".($branch!='' && $branch !='0' ? " and est.id_branch in (".$branch.") " :'' )."
        ");
        return $pur_query->result_array();
	}
	//reports
		
   // EDA PURCHASE REPORT

	
   function get_eda_purchase($data)
   {

	   //print_r($data['id_metal']);exit;

	   $id_metal=$data['id_metal'];

	   $id_branch=$data['id_branch'];
	   
	   
	   if($id_branch!='' && $id_branch>0)
	   {
		   $data=$this->getBranchDayClosingData($id_branch);
	   }else{
		   $id_branch = 1;
		   $data=$this->getBranchDayClosingData($id_branch);
	   }


	   
	   $pur=array();
   
	   $sql=$this->db->query("SELECT br.name as branch,
	   
	   est.estimation_id,est.esti_no,date_format(est.est_date,'%d-%m-%Y') as est_date,
	   
	   mt.metal as metal_name,t.metal_type,c.old_metal_cat,
	   
	   sum(s.piece) as piece,sum(s.gross_wt) as gross_wt,
	   
	   (IFNULL(sum(s.dust_wt),0)+IFNULL(sum(s.stone_wt),0)) as less_wt,
	   
	   sum(s.net_wt) as net_wt,IFNULL(sum(s.wastage_wt),0) as wastage_wt ,
	   
	   avg(s.rate_per_gram) as rate_per_gram,
	   
	   sum(s.amount) as pur_amt,mt.id_metal
	   
	   
	   FROM ret_estimation_old_metal_sale_details s 
	   
	   LEFT JOIN ret_estimation est ON est.estimation_id = s.est_id
	   
	   LEFT JOIN branch br on br.id_branch=est.id_branch
	   
	   LEFT JOIN ret_old_metal_type t ON t.id_metal_type = s.id_old_metal_type
	   
	   LEFT JOIN ret_old_metal_category c ON c.id_old_metal_cat = s.id_old_metal_category
	   
	   LEFT JOIN metal mt ON mt.id_metal = s.id_category
	   
	   WHERE est.is_eda=1 and est.is_eda_approved=1 
	   
	   AND est_date = '".$data['entry_date']."'

	   ".($id_metal!='' && $id_metal!=0 ? " and mt.id_metal=".$id_metal."" :'')."
	   
	   
	   ".($id_branch!='' && $id_branch!=0 ? " and est.id_branch=".$id_branch."" :'')."
	   
			   
	   GROUP BY s.old_metal_sale_id");

	   $eda_pur=$sql->result_array();
	   

	   foreach($eda_pur as $val)
	   {
		   $pur[$val['metal_name']][]=$val;
	   }

	   return $pur;


   }









	 // EDA PARTLY SOLD

	 function get_eda_partlysold($data)
	{

		$id_metal=$data['id_metal'];

		$id_branch=$data['id_branch'];
		
		
		if($id_branch!='' && $id_branch>0)
        {
            $data=$this->getBranchDayClosingData($id_branch);
        }else{
            $id_branch = 1;
            $data=$this->getBranchDayClosingData($id_branch);
        }

		$partly=array();

		$sql=$this->db->query("SELECT br.name as branch,
			
			est.estimation_id,est.esti_no,
			
			date_format(est.est_date,'%d-%m-%Y') as est_date,
			
			tag.tag_code,pro.product_name,des.design_name,
			
			tag.piece as act_piece,tag.gross_wt as act_grs_wt,
			
			tag.net_wt as act_net_wt,
			
			est_itms.piece as sold_piece,
			
			est_itms.gross_wt as sold_gwt,
			
			est_itms.net_wt as sold_nwt,
			
			est_itms.item_cost,mt.metal as metal_name
			
			FROM ret_estimation_items  est_itms
			
			LEFT JOIN ret_estimation est on est.estimation_id=est_itms.esti_id
			
			LEFT JOIN ret_taging tag on tag.tag_id=est_itms.tag_id
			
			LEFT JOIN branch br on br.id_branch=est.id_branch
			
			LEFT JOIN ret_product_master pro on pro.pro_id=est_itms.product_id

			LEFT JOIN ret_category c on c.id_ret_category = pro.cat_id
			
			LEFT JOIN metal mt on mt.id_metal = c.id_metal

			LEFT JOIN ret_design_master des on des.design_no = est_itms.design_id
			
			WHERE est_itms.is_partial=1 and est.is_eda_approved=1
			
			AND est_date = '".$data['entry_date']."'
			
			".($id_metal!='' && $id_metal!=0 ? " and mt.id_metal=".$id_metal."" :'')."
		
		
			".($id_branch!='' && $id_branch!=0 ? " and est.id_branch=".$id_branch."" :'')."");

			//print_r($this->db->last_query());exit;

			$data=$sql->result_array();

			foreach($data as $r)
			{
				$partly[$r['metal_name']][]=$r;
			}
			
			return $partly; 
	}

}

?>