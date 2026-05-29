<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ret_supp_catalog_model extends CI_Model
{
	const SUPPIMG_PATH = 'assets/img/supplier/';
	
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

	function get_empty_record() {

		$record['id_supp_catalogue'] = "";
		
		$record['ctl_datetime'] = "";
		
		$record['product_id'] = "";

		$record['cat_id'] = "";
		
		$record['design_id'] = "";
		
		$record['id_sub_design'] = "";
		
		$record['design_code'] = "";

		$record['status'] = "1";
		
		/*$record['images'][0]['id_supp_cat_img'] = "";

		$record['images'][0]['id_supp_catalogue'] = "";

		$record['images'][0]['image'] = "";
		
		$record['images'][0]['is_default'] = "";*/
		
		$record['weightRange'] = array();

		return $record;
	}

	function ajax_getSupplierCatList($id= "", $type = "") {

        $return_data=array();

		$where = "";

		if($id > 0) {

			$where = $where." AND sc.id_supp_catalogue =".$id;

		}

		$sql = "SELECT

					sc.id_supp_catalogue,

					sc.ctl_datetime,

					sc.product_id,

					sc.design_id,

					sc.id_sub_design,

					sc.design_code,

					sc.status,

					sc.image,

					pm.product_name,

					pm.cat_id,

					dm.design_name,

					sdm.sub_design_name,

					sc.status,

					IF(sc.status = 1, 'Active', 'InActive') AS supp_cat_status

				FROM ret_supp_catalogue sc

				LEFT JOIN ret_product_master pm ON pm.pro_id = sc.product_id

				LEFT JOIN ret_design_master dm ON dm.design_no = sc.design_id

				LEFT JOIN ret_sub_design_master sdm ON sdm.id_sub_design = sc.id_sub_design
				
				WHERE 1 ".$where;

				//echo $sql;exit;

		$sql = $this->db->query($sql);

		// echo $this->db->last_query();exit;
		
		$result = $sql->result_array();

		if($type != "list") {

			foreach($result as $data) {

				$data['weightRange'] = $this->get_suppWeightRangeData($data['id_supp_catalogue'])[0];

				$images_details	= $this->get_supp_cat_images($data['id_supp_catalogue']);

				$data['images_details'] = array();

				foreach($images_details as $imgs) {

					$img_path = self::SUPPIMG_PATH.$imgs['id_supp_cat_img']."-".$imgs['image'];

					$type = pathinfo($img_path, PATHINFO_EXTENSION);

					$fileContents = file_get_contents($img_path);

					$imgs['base64'] = 'data:image/' . $type . ';base64,' . base64_encode($fileContents);

					$data['images_details'][] = $imgs;
					
				}

				$return_data[] = $data;

			}

		} else {

			$return_data = $result;

		}
		
		return $return_data;
	}

	function get_suppWeightRangeData($id_supp_catalogue = "") {

		$where = "";

		if($id_supp_catalogue > 0) {

			$where = $where." AND cw.id_supp_catalogue = ".$id_supp_catalogue;

		}

		$weight_sql = $this->db->query("SELECT
											
											cw.id_catalogue_weight,

											cw.id_supp_catalogue,
											
											cw.weight, 

											IFNULL(cw.from_weight,'') AS from_weight, 

											IFNULL(cw.to_weight,'') AS to_weight, 

											cw.purity,

											cw.size,

											cw.wastage,

											cw.display_va,

											cw.mc_value,

											cw.mc_type,

											cw.display_mc,

											cw.delivery_duration,

											cw.display_duration,

											cw.calculation_based_on,

											cw.karigar,
											
											CONCAT(TRIM(cw.weight),'GM') AS weight_description

										FROM ret_supp_catalogue_weight AS cw
										
										WHERE 1  ".$where);
// echo $this->db->last_query();exit;
		$weights = $weight_sql->result_array();

		foreach($weights as $data) {

			$return_data[] = $data;

		}
		return $return_data;
	}

	public function get_designcode() {

		$design_code = 1;

		$sql = $this->db->query("SELECT MAX(CAST(design_code AS UNSIGNED)) AS design_code FROM ret_supp_catalogue");

		$result = $sql->row_array();

		if($result) {

			$design_code = $result['design_code'];

			$design_code = $design_code + 1;

		}

		return $design_code;

	}

	public function get_supp_cat_images($supp_cat_id) {

		$this->db->select('id_supp_cat_img, id_supp_catalogue, image, date_add, is_default');
		
		$this->db->from('supp_catalogue_images');
		
		$this->db->where('id_supp_catalogue', $supp_cat_id);

		$query = $this->db->get();
		
		$result = $query->result_array();

		return $result;

	}
	
	public function get_catgory_id($pro_id){

		$sql = $this->db->query('SELECT cat_id FROM `ret_product_master` WHERE pro_id = '.$pro_id.' ;');		
			$result = $sql->row_array();
		
		

		return $result;
	}
	// public function update_status($data,$id)

    // {    	

    // 	$edit_flag=0;

    // 	$this->db->where('id_customer',$id); 

	// 	$cus_info=$this->db->update(self::TABLE_NAME,$data);		

	// 	return $cus_info;

	// }

	function get_ActiveProducts($data)
	{
	    $sql=$this->db->query("SELECT p.pro_id,p.product_name, p.sales_mode
        FROM ret_product_master p 
        WHERE p.pro_id IS NOT NULL AND p.product_status=1 
        ".($data['id_ret_category']!='' ? " and p.cat_id=".$data['id_ret_category']."" :'')."");

		//echo $this->db->last_query();exit;
        return $sql->result_array();
	}
	function get_Activedesign($id_product)
    {
        if($id_product != ""){
    	    $data=$this->db->query("SELECT d.design_no, d.design_name
    	                            FROM ret_design_master d 
    	                            LEFT JOIN ret_product_mapping dm ON dm.id_design = d.design_no 
    	                            LEFT JOIN ret_product_master as pr ON pr.pro_id = dm.pro_id 
    	                            where d.design_status=1 ".($id_product!='' ? " and dm.pro_id=".$id_product."" :'')." ");
    	   //echo $this->db->last_query();exit;
        }else{
            $data=$this->db->query("SELECT d.design_no,d.design_name
    		FROM ret_design_master d 
    		LEFT JOIN ret_product_mapping dm ON dm.id_design = d.design_no 
    		where d.design_status=1 ".($id_product!='' ? " and dm.pro_id = ".$id_product."" :'')." ");
        }
    	return $data->result_array();
    }
    function get_ActiveSubDesign($data)
    {
        $sql=$this->db->query("SELECT subDes.id_sub_design,subDes.sub_design_name
        FROM ret_sub_design_mapping s 
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design=s.id_sub_design
        WHERE s.id_product=".$data['id_product']." AND s.id_design=".$data['id_design']."");
        return $sql->result_array();
    }


	function ajax_getSupplierCal_images($id_supp_catalogue,$id_catalogue_weight)
    {
		$sql = $this->db->query("SELECT id_supp_catalogue,id_catalogue_weight,id_supp_cat_img,image,is_default,image as name FROM `supp_catalogue_images` WHERE id_supp_catalogue = ".$id_supp_catalogue." and id_catalogue_weight =".$id_catalogue_weight.";");
		return $sql->result_array();
	}
}
?>