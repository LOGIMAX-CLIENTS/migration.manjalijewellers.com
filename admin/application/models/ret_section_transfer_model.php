<?php

    if( ! defined('BASEPATH')) exit('No direct script access allowed');



    class Ret_section_transfer_model extends CI_Model

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





    

    function getBranchDayClosingData($id_branch)

    {

        $sql = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing where id_branch=".$id_branch); 

        return $sql->row_array();

    }



    function getSectionTags($data)

    {

        
        // print_r($data);exit;


        $dCData=$this->getBranchDayClosingData($data['id_branch']);

        $onlyBranchSelected =  (!empty($data['id_branch']) || !empty($data['id_section'])) && (empty($data['old_tag_id']) && empty($data['tag_code']) && empty($data['est_no']));
        

        if(empty($data['est_no'])){



        

        $sql = "SELECT t.tag_id,t.tag_code,IFNULL(t.id_section,'') as id_section,t.product_id,t.current_branch as id_branch,

        

        IFNULL(sec.section_name,'') as section_name,p.product_name,br.name as branch_name,



        t.piece,t.gross_wt,t.net_wt,IFNULL(t.old_tag_id,'') as old_tag_id,IFNULL(co.orderno,'') AS orderno,
        
        IFNULL(t.id_orderdetails,'') AS orderid



        FROM ret_taging t 



        LEFT join ret_section sec on sec.id_section = t.id_section



        LEFT JOIN ret_product_master p on p.pro_id = t.product_id



        LEFT JOIN branch br on br.id_branch = t.current_branch

        LEFT JOIN customerorderdetails co ON co.id_orderdetails = t.id_orderdetails

        where t.tag_status=0 

        

        ".($data['id_section']!='' && $data['id_section'] > 0 ? "and t.id_section=".$data['id_section']."" : "")."

        

        ".($data['id_branch']!='' && $data['id_branch'] > 0 ? "and t.current_branch=".($data['id_branch'])."":'')."

        

        ".($data['id_product']!='' && $data['id_product'] > 0 ? "and t.product_id=".($data['id_product'])."":'')."

        

        ".($data['old_tag_id']!='' ? "and t.old_tag_id = '".($data['old_tag_id'])."'":'')."



        ".($data['tag_code']!=''  ? "and t.tag_code = '".($data['tag_code'])."'":'')."

        

        ".($data['est_no'] !='' && $data['est_no'] > 0 ? " AND est.esti_for = 2 AND date(est.estimation_datetime)='".$dCData['entry_date']."' AND est.esti_no=".$data['est_no']."":"")." ";

        

        }
        else{

            $sql ="SELECT t.tag_id,t.tag_code,IFNULL(t.id_section,'') as id_section,t.product_id,t.current_branch as id_branch,

        

        IFNULL(sec.section_name,'') as section_name,p.product_name,br.name as branch_name,



        t.piece,t.gross_wt,t.net_wt,IFNULL(t.old_tag_id,'') as old_tag_id,IFNULL(co.orderno,'') AS orderno,
        
        IFNULL(t.id_orderdetails,'') AS orderid



        FROM ret_taging t 



        LEFT join ret_section sec on sec.id_section = t.id_section



        LEFT JOIN ret_product_master p on p.pro_id = t.product_id



        LEFT JOIN branch br on br.id_branch = t.current_branch



        LEFT JOIN ret_estimation_items estitms on estitms.tag_id = t.tag_id



        LEFT JOIN ret_estimation est on est.estimation_id = estitms.esti_id

        LEFT JOIN customerorderdetails co ON co.id_orderdetails = t.id_orderdetails

        where t.tag_status=0 and t.id_section is not null

        

        ".($data['id_section']!='' && $data['id_section'] > 0 ? "and t.id_section=".$data['id_section']."" : "")."

        

        ".($data['id_branch']!='' && $data['id_branch'] > 0 ? "and t.current_branch=".($data['id_branch'])."":'')."

        

        ".($data['old_tag_id']!='' ? "and t.old_tag_id = '".($data['old_tag_id'])."'":'')."



        ".($data['tag_code']!=''  ? "and t.tag_code = '".($data['tag_code'])."'":'')."

        

        ".($data['id_product']!='' && $data['id_product'] > 0 ? "and t.product_id=".($data['id_product'])."":'')."

        

        ".($data['est_no'] !='' && $data['est_no'] > 0 ? " AND est.esti_for = 2 AND date(est.estimation_datetime)='".$dCData['entry_date']."' AND est.esti_no=".$data['est_no']."":"")." ";



        }

        //print_r($this->db->last_query());exit;

        if ($onlyBranchSelected) {
            $sql .= " AND (t.id_orderdetails IS NULL OR t.id_orderdetails = '')";
        }

        $query = $this->db->query($sql);
        return $query->result_array();
        // return $sql->result_array();

    

    }

    

    //Check Non_tag Item exist Starts



    function checkNonTagItemExist($data){

		$r = array("status" => FALSE);

		$sql = "SELECT id_nontag_item FROM ret_nontag_item WHERE  branch=".$data['branch']." AND product=".$data['product']." AND design=".$data['design']." AND id_section=".$data['id_section'] ." AND id_sub_design=".$data['id_sub_design'] ;  

       

		$res = $this->db->query($sql);

		if($res->num_rows() > 0){

			$r = array("status" => true, "id_nontag_item" => $res->row()->id_nontag_item); 

            

		}else{

			$r = array("status" => false , "id_nontag_item" => ""); 

		} 

        // print_r($this->db->last_query());exit;

		return $r;

	}

	

	function updateNTData($data,$arith){ 



		$sql = "UPDATE ret_nontag_item rt SET no_of_piece=(no_of_piece".$arith." ".$data['no_of_piece']."),gross_wt=(gross_wt".$arith." ".$data['gross_wt']."),net_wt=(net_wt".$arith." ".$data['net_wt']."),updated_by=".$data['updated_by'].",updated_on='".$data['updated_on']."' WHERE id_nontag_item=".$data['id_nontag_item'];  

		$status = $this->db->query($sql);

		return $status;

	}

    

 //Check Non_tag Item exist Ends

    

    function get_tag_details($tag_id)

    {

        $sql = $this->db->query("SELECT * FROM ret_taging WHERE tag_id = ".$tag_id."");

        return $sql->row_array();

    }

    

    //Home Section

    function sectionData($transfer_to_section)

	{

	    $sql=$this->db->query("SELECT is_home_bill_counter FROM ret_section WHERE id_section=".$transfer_to_section."");

	    return $sql->row_array();

	}



    function checkSectionItemExist($data){



		$r = array("status" => FALSE);



	    $sql = "SELECT id_hometag_item FROM ret_home_section_item WHERE id_branch=".$data['id_branch'] ." AND id_section=".$data['id_section'] ." AND id_product=".$data['id_product'] ;  

	    $res = $this->db->query($sql);



		if($res->num_rows() > 0){



			$r = array("status" => true, "id_hometag_item" => $res->row()->id_hometag_item); 

            

		}else{

			$r = array("status" => false , "id_hometag_item" => ""); 

		} 

        //  print_r($this->db->last_query());exit;

		return $r;

	}







    function updatesecNTData($data,$arith){ 



		$sql = "UPDATE ret_home_section_item SET no_of_piece=(no_of_piece".$arith." ".$data['no_of_piece']."),gross_wt=(gross_wt".$arith." ".$data['gross_wt']."),net_wt=(net_wt".$arith." ".$data['net_wt']."),updated_by=".$data['updated_by'].",updated_on='".$data['updated_on']."' WHERE id_hometag_item=".$data['id_hometag_item'];  



		$status = $this->db->query($sql);



		return $status;



	}	



    function updatestatus($tag_id){ 



		$sql = "UPDATE ret_taging SET tag_status= 14 WHERE tag_id=".$tag_id;  



		$status = $this->db->query($sql);

		return $status;

	}	

	

    //Home Section

    function getBrnachOtpRegMobile($id_branch)
    {
    
        $sql = $this->db->query("Select otp_verif_mobileno from branch where id_branch=" . $id_branch . "");
    
        return $sql->row()->otp_verif_mobileno;
    }

    

}



?>