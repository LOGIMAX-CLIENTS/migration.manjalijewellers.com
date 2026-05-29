<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ret_wishlist_model extends CI_Model

{

	const ENQ_IMG_PATH  = 'assets/img/enquiry/';

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
	
	public function get_wishlist_data($type = "", $from_date = "", $to_date = "", $wishlist_id = "", $status = 0, $id_branch = 0, $id_employee = 0) {

		$return_data = array();

		$date_from 	= date('Y-m-d', strtotime('-29 days'));

		$date_to 	= date("Y-m-d");

		$where = "";

		if($type == 1) {

			$where = $where." AND type IN ('tag_Wishlist', 'Wishlist')";

		} else if($type == 2) {

			$where = $where." AND type = 'supp_Wishlist'";

		} else if($type == 3) {

			$where = $where." AND type = 'FactSheet'";

		} else if($type == 4) {

			$where = $where." AND type = 'Enquiry'";

		} else if($type == 5) {

			$where = $where." AND type IN ('tag_Wishlist', 'Wishlist', 'supp_Wishlist')";

		}

		if($from_date != "" && $to_date != "") {

			$date_from 	= $from_date;

			$date_to 	= $to_date;

		}

		if($wishlist_id > 0) {

			$where = $where." AND we.id_wishlist = ".$wishlist_id;

		}

		if($status > 0) {

			$where = $where." AND we.status = ".$status;

		}

		if($id_branch > 0) {

			$where = $where." AND we.emp_login_branch = ".$id_branch;

		}

		if($id_employee > 0) {

			$where = $where." AND we.created_by = ".$id_employee;

		}

		$where = $where." AND DATE(we.created_on) BETWEEN '".$date_from."' AND '".$date_to."'";

		$sql = "SELECT 
					
					we.id_wishlist,

					we.customer_id,

					we.tag_id,
					
					we.tag_code,

					we.id_supp_catalogue,

					we.esti_id,

					we.product_id,

					we.design_id,

					we.sub_design_id,

					we.price_range,

					we.weight_range,

					we.weight,

					we.description,

					we.images,

					we.reasons_for_leaving,

					we.enq_product,

					we.due_days,

					we.type,

					we.emp_login_branch,

					we.status,

					IF(we.status = 1, 'Created', (IF(we.status = 2, 'Converted', (IF(we.status = 3, 'Closed', ''))))) AS status_desc,

					we.remarks,

					DATE_FORMAT(we.close_date, '%d-%m-%Y') AS close_date,

					we.close_employee,

					DATE_FORMAT(we.created_on, '%d-%m-%Y') AS created_date,

					we.created_by,

					CONCAT(IFNULL(cus.firstname,''),' ',IFNULL(cus.lastname,'')) AS cus_name,

					IFNULL(cus.firstname,'') AS firstname,

					IFNULL(cus.lastname,'') AS lastname,

					cus.mobile,

					emp.firstname AS emp_name,

					empCl.firstname AS emp_close_name,

					IFNULL(pro.product_name, '-') as product_name, 

					IFNULL(des.design_name, '-') as design_name,

					IFNULL(sub_des.sub_design_name, '-') as sub_design_name,

					vill.village_name AS area,

					IFNULL(br.name,'') AS branch_name

				FROM  ret_wishlist_enquiry AS we

				LEFT JOIN customer AS cus ON cus.id_customer = we.customer_id

				LEFT JOIN village AS vill ON vill.id_village = we.area

				LEFT JOIN employee AS emp ON  emp.id_employee = we.created_by

				LEFT JOIN employee AS empCl ON empCl.id_employee = we.close_employee

				LEFT JOIN ret_product_master AS pro ON pro.pro_id = we.product_id

				LEFT JOIN ret_design_master AS des ON des.design_no = we.design_id

				LEFT JOIN ret_sub_design_master AS sub_des ON sub_des.id_sub_design = we.sub_design_id

				LEFT JOIN branch AS br ON br.id_branch = we.emp_login_branch

				WHERE 1 ".$where." ORDER BY we.created_on DESC";

				//echo $sql;exit;

		$result_query = $this->db->query($sql);

		$resultArr = $result_query->result_array();

		foreach($resultArr as $arr) {

			$arr['default_image'] = '';

			$arr['img_arr'] = array();

			if($arr['images'] != "") {

				$imgArr = explode(",", $arr['images']); 

				$imgInc = 1;

				foreach($imgArr as $imgName) {

					if(trim($imgName) != "") {

						$imgPath = base_url().self::ENQ_IMG_PATH.$imgName; 

						$arr['img_arr'][] = $imgPath;

						if($imgInc  == 1) {

							$arr['default_image'] = $imgPath;

						}

						$imgInc++;

					}

				}

			}

			$arr['followup_data'] = $this->getFollowupData($arr['id_wishlist']);

			if($arr['status'] == 2 || $arr['status'] == 3) {

				$close_array = array(

					"id_enq_followup" 	=> "",

					"id_wishlist_enq" 	=> $arr['id_wishlist'],

					"followup_date"		=> $arr['close_date'],

					"followup_remarks" 	=> $arr['remarks'],

					"followup_employee" => $arr['close_employee'],

					"emp_name" 			=> $arr['emp_close_name']

				);

				array_push($arr['followup_data'], $close_array);

			}

			$return_data[] = $arr;

		}

		return $return_data;

	}

	function getFollowupData($wishlist_id) {

		$sql = "SELECT

					fu.id_enq_followup,

					fu.id_wishlist_enq,

					DATE_FORMAT(fu.followup_date, '%d-%m-%Y') AS followup_date,

					fu.followup_remarks,

					fu.followup_employee,

					emp.firstname AS emp_name

				FROM  ret_wishlist_enquiry_followup AS fu

				LEFT JOIN employee AS emp ON fu.followup_employee = emp.id_employee

				WHERE id_wishlist_enq = ".$wishlist_id;

		$result_query = $this->db->query($sql);

		$resultArr = $result_query->result_array();

		return $resultArr;

	}

	function get_factsheet_for_sms($product = "", $design = "", $sub_design = "", $weight = "") {

		$sql = "SELECT 

					we.customer_id,

					we.product_id,

					we.design_id,

					we.sub_design_id,

					we.weight_range,

					cus.mobile,

					IFNULL(pro.product_name, '-') as product_name, 

					IFNULL(des.design_name, '-') as design_name,

					IFNULL(sub_des.sub_design_name, '-') as sub_design_name

			FROM ret_wishlist_enquiry as we

			LEFT JOIN customer AS cus ON cus.id_customer = we.customer_id

			LEFT JOIN ret_product_master as pro ON pro.pro_id = we.product_id

			LEFT JOIN ret_design_master as des ON des.design_no = we.design_id

			LEFT JOIN ret_sub_design_master as sub_des ON sub_des.id_sub_design = we.sub_design_id

			WHERE we.product_id =".$product." AND  we.design_id =".$design." AND we.sub_design_id =".$sub_design." AND we.weight =".$weight." AND we.type = 'FactSheet'";

		$result_query = $this->db->query($sql);

		$resultArr = $result_query->result_array();

		return $resultArr;

	}

}

?>