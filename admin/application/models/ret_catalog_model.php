<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ret_catalog_model extends CI_Model

{

	const DEVICE_TABLE 	    = "web_registered_devices";

	function __construct()

    {
        parent::__construct();

    }



  /*Ret category functions here */

    function empty_record_category()

    {

		$data=array(

			'id_ret_category'	=> NULL,

			'name'				=> NULL,

			'description'		=> NULL,

			'image'				=> NULL,

			'status'			=> 1

		);

		return $data;

	}



	// Default functions

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

	// //	print_r($this->db->last_query());exit;

	// 	return ($insert_flag == 1 ? $this->db->insert_id(): 0);

	// }



	// public function updateData($data,$id_field,$id_value,$table)

    // {

	//     $edit_flag = 0;

	//     $this->db->where($id_field,$id_value);

	// 	$edit_flag = $this->db->update($table,$data);

	// 	// print_r($this->db->last_query());exit;

	// 	return ($edit_flag==1?$id_value:0);

	// }

	public function deleteData($id_field,$id_value,$table)

    {

        $this->db->where($id_field, $id_value);

        $status= $this->db->delete($table);

		//print_r($this->db->last_query());exit;

		return $status;

	}





	/*Purity functions here */



   	 function ajax_getPurity()

    {

		$id_purity = $this->db->query("SELECT * FROM ret_purity ORDER BY id_purity desc");

		return $id_purity->result_array();

	}



	 function get_purity($id_purity)

    {

		$id_purity = $this->db->query("select * from ret_purity where id_purity=".$id_purity);

		return $id_purity->row_array();

	}


	function get_profile_settings($id_profile)

	{

		$data=$this->db->query("SELECT pr.vendor_approval_otp_req  FROM  profile pr  where pr.id_profile ='".$id_profile."'");

		return $data->row()->vendor_approval_otp_req;

	}

    public function insert_purity($data)

    {

    	$insert_flag = 0;

		$insert_flag = $this->db->insert('ret_purity',$data);

		return ($insert_flag == 1 ? $this->db->insert_id(): 0);

	}



	public function update_purity($data,$id)

    {

    	$edit_flag = 0;

    	$this->db->where('id_purity',$id);

		$edit_flag = $this->db->update('ret_purity',$data);

		return ($edit_flag==1?$id:0);

	}



	public function delete_purity($id)

    {

        $this->db->where('id_purity', $id);

        $status= $this->db->delete('ret_purity');

		return $status;

	}

	/*End of purity functions*/



	/*color functions here */



   	 function ajax_getcolor()

    {

		$id_color = $this->db->query("SELECT * FROM ret_color ORDER BY id_color desc");

		return $id_color->result_array();

	}



	 function get_color($id_color)

    {

		$id_color = $this->db->query("select * FROM ret_color where id_color=".$id_color);

		return $id_color->row_array();

	}



    public function insert_color($data)

    {

    	$insert_flag = 0;

		$insert_flag = $this->db->insert('ret_color',$data);

		return ($insert_flag == 1 ? $this->db->insert_id(): 0);

	}



	public function update_color($data,$id)

    {

    	$edit_flag = 0;

    	$this->db->where('id_color',$id);

		$edit_flag = $this->db->update('ret_color',$data);

		return ($edit_flag==1?$id:0);

	}



	public function delete_color($id)

    {

        $this->db->where('id_color', $id);

        $status= $this->db->delete('ret_color');

		return $status;

	}

	/*End of color functions*/

	/*cut functions here */



   	 function ajax_getcut()

    {

		$id_cut = $this->db->query("SELECT * FROM ret_cut ORDER BY id_cut desc");

		return $id_cut->result_array();

	}



	 function get_cut($id_cut)

    {

		$id_cut = $this->db->query("select * FROM ret_cut where id_cut=".$id_cut);

		return $id_cut->row_array();

	}



    public function insert_cut($data)

    {

    	$insert_flag = 0;

		$insert_flag = $this->db->insert('ret_cut',$data);

		return ($insert_flag == 1 ? $this->db->insert_id(): 0);

	}



	public function update_cut($data,$id)

    {

    	$edit_flag = 0;

    	$this->db->where('id_cut',$id);

		$edit_flag = $this->db->update('ret_cut',$data);

		return ($edit_flag==1?$id:0);

	}



	public function delete_cut($id)

    {

        $this->db->where('id_cut', $id);

        $status= $this->db->delete('ret_cut');

		return $status;

	}

	/*End of cut functions*/

	/*clarity functions here */



   	 function ajax_getclarity()

    {

		$id_clarity = $this->db->query("SELECT * FROM ret_clarity ORDER BY id_clarity desc");

		return $id_clarity->result_array();

	}



	 function get_clarity($id_clarity)

    {

		$id_clarity = $this->db->query("select * FROM ret_clarity where id_clarity=".$id_clarity);

		return $id_clarity->row_array();

	}



    public function insert_clarity($data)

    {

    	$insert_flag = 0;

		$insert_flag = $this->db->insert('ret_clarity',$data);

		return ($insert_flag == 1 ? $this->db->insert_id(): 0);

	}



	public function update_clarity($data,$id)

    {

    	$edit_flag = 0;

    	$this->db->where('id_clarity',$id);

		$edit_flag = $this->db->update('ret_clarity',$data);

		return ($edit_flag==1?$id:0);

	}



	public function delete_clarity($id)

    {

        $this->db->where('id_clarity', $id);

        $status= $this->db->delete('ret_clarity');

		return $status;

	}

	/*End of clarity functions*/



 /*product functions here */

    function empty_record_product()

    {

		$data=array(

			'id_product'			=> NULL,

			'id_subcategory'		=> NULL,

			'id_metal'				=> NULL,

			'name'					=> NULL,

			'description'			=> NULL,

			'code'					=> NULL,

			'qty'					=> 1,

			'qty_type'				=> 1,

			'allow_customization'	=> 0,

			'certification'			=> NULL,

			'min_size'				=> NULL,

			'max_size'				=> NULL,

			'gold_value'			=> NULL,

			'wastage'				=> 0.00,

			'min_wastage'			=> NULL,

			'max_wastage'			=> NULL,

			'tax'					=> 0.00,

			'stone_charges'			=> 0.00,

			'other_charges'			=> 0.00,

			'making_charges'		=> 0.00,

			'allowed_order_qty'		=> 1,

			'certif_charges'		=> 0.00,

			'product_stone'			=> 2,

			'length'				=> NULL,

			'width'					=> NULL,

			'height'				=> NULL,

			'total_stones'			=> NULL,

			'stone_name'			=> NULL,

			'default_img'			=> NULL,

			'certif_img'			=> NULL,

			'can_view_by'			=> 1,

			'show_rate'				=> 1,

			'status'				=> 1,

			'product_for'			=> 0

		);

		return $data;

	}



   	 function ajax_getProduct()

    {

		$product = $this->db->query("SELECT p.`id_product`, p.`id_subcategory`, p.`name`, p.`status`, p.`code`, p.`allow_customization`, p.`product_stone`,c.name as category_name,sc.name as subcategory_name

		 FROM product p

		left join sub_category sc on (p.id_subcategory=sc.id_subcategory)

		left join category c on (c.id_ret_category=sc.id_ret_category)");

		return $product->result_array();

	}



	 function get_product($id_product)

    {

		$product = $this->db->query("SELECT p.allowed_order_qty,p.qty_type,p.can_view_by,p.length,p.width,p.height,p.stone_name,p.`id_product`, p.`id_subcategory`, p.`name`, p.`description`, p.`status`, p.`code`, p.`qty`, p.`date_add`, p.`date_update`, p.`allow_customization`, p.`certification`, p.`min_size`, p.`max_size`, p.`gold_value`, p.`wastage`, p.`tax`, p.`stone_charges`, p.`making_charges`, p.`other_charges`, p.`product_stone`, p.`certif_charges`,p.`total_stones`, p.`id_employee`, p.`id_metal`,c.`id_ret_category`, c.`name` as category_name,s.`id_subcategory`, s.`name` as subcategory,p.show_rate,p.certif_img,(SELECT image from product_images as img where is_default=1 and img.id_product=p.id_product) as default_img,p.product_for,metal_type

 		FROM product p

    	 left join sub_category s on (p.id_subcategory=s.id_subcategory)

		 left join category c on (c.id_ret_category=s.id_ret_category)

		 where p.id_product=".$id_product);

		return $product->row_array();

	}



	// get product images except defalt image

	function get_prodimage($id)

    {

		$product = $this->db->query("select image,is_default from product_images where is_default=0 and id_product=".$id);

		return $product->result_array('image');

	}



	// get product images except defalt image

	function get_defaultProdimage($id)

    {

		$product = $this->db->query("select image,is_default from product_images where is_default=1 and id_product=".$id);

		return $product->result_array('image');

	}



	function delete_prodimage($file)

    {

		 $this->db->where('image', $file);

        $status= $this->db->delete('product_images');

		return $status;

	}



	function insertProdImage($data){

		$insert_flag = $this->db->insert('product_images',$data);

		return ($insert_flag == 1 ? $this->db->insert_id(): 0);

	}



	public function deleteProdImage($id)

    {

    	 $this->db->where('id_image', $id);

        $status= $this->db->delete('product_images');

		return $status;

	}



    public function insert_product($data)

    {

    	$insert_flag = 0;



		$insert_flag = $this->db->insert('product',$data);

		return ($insert_flag == 1 ? $this->db->insert_id(): 0);

	}



	 public function insert_product_detail($data)

    {

    	$insert_flag = 0;



		$insert_flag = $this->db->insert('product_details',$data);

		return ($insert_flag == 1 ? $this->db->insert_id(): 0);

	}



	public function update_product($data,$id)

    {

    	$edit_flag = 0;

    	$this->db->where('id_product',$id);

		$edit_flag = $this->db->update('product',$data);

		return ($edit_flag==1?$id:0);

	}



	public function delete_product($id)

    {

    	$this->db->where('id_product', $id);

        $img= $this->db->delete('product_images');

        if($img)

        {

		  $this->db->where('id_product', $id);

         $child= $this->db->delete('product_details');

		}

        if($child)

        {

		  $this->db->where('id_product', $id);

           $status= $this->db->delete('product');

		}



		return $status;

	}

	public function delete_prodDetail($id)

    {

        $this->db->where('id_product', $id);

        $status= $this->db->delete('product_details');

		return $status;

	}

	/*End of product functions*/



	function getActiveMetals()

    {

		$data = $this->db->query("SELECT * FROM metal");

		return $data->result_array();

	}

	function getActivePurity()

    {

		$data = $this->db->query("SELECT * FROM ret_purity WHERE status = 1");

		return $data->result_array();

	}

	function getActiveCut()

    {

		$data = $this->db->query("SELECT * FROM ret_cut WHERE status = 1");

		return $data->result_array();

	}

	function getActiveClarity()

    {

		$data = $this->db->query("SELECT * FROM ret_clarity WHERE status = 1");

		return $data->result_array();

	}

	function getmetalInfo($id)

    {

		$data = $this->db->query("SELECT p.`metal_color`, p.`purity`, p.`id_metal_details`, p.`max_metal_weight`, p.`min_metal_weight`, p.`type` FROM product_details p WHERE type=0 and id_product =".$id);

		return $data->result_array();

	}

	function getdiamondInfo($id)

    {

		$data = $this->db->query("SELECT p.`type`, p.`diamond_color`, p.`carat`, p.`clarity`, p.`cut`, p.`id_metal_details` FROM product_details p WHERE type=1 and id_product =".$id);

		return $data->result_array();

	}



	function getActivecolor()

    {

		$id_color = $this->db->query("SELECT * FROM ret_color WHERE status = 1");

		return $id_color->result_array();

	}

	function getActiveSubctg()

    {

		$data = $this->db->query("SELECT s.`id_subcategory`, s.`id_ret_category`, s.`name`, s.`status`,

      (select name from category where id_ret_category = s.id_ret_category) as category

     FROM sub_category s WHERE status = 1");

		return $data->result_array();

	}







	// Floor Master

	function ajax_getfloor($from_date,$to_date,$id_branch)

    {

		$data =array();

		$sql = "SELECT f.floor_id,b.id_branch,b.name as branch_name,f.floor_name,f.floor_short_code,

		f.created_on,f.floor_status FROM ret_branch_floor as f

		left join branch b on b.id_branch = f.branch_id";



		if(($id_branch!='' && $id_branch > 0) && $from_date!='')

		{

			$sql = $sql." where b.id_branch =" .$id_branch. ' and (date(f.created_on)

		BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")';

		}

		elseif(($id_branch!='' && $id_branch > 0 ) || $from_date!='')

		{

			$sql = $sql." where ".($id_branch != '' && $id_branch > 0  ? ('b.id_branch ='.$id_branch) : ($from_date != '' ? ' date(f.created_on) BETWEEN '.date('Y-m-d',strtotime($from_date)).' AND '.date('Y-m-d',strtotime($to_date)) : (''))) ;

		}

		$result=$this->db->query($sql);

		$data = $result->result_array();

		return $data;

	}



	function get_floor($floor_id)

    {

		$floor_id = $this->db->query("select * from ret_branch_floor where floor_id=".$floor_id);

		return $floor_id->row_array();

	}



	function getActiveFloors()

    {

		$data = $this->db->query("SELECT * FROM ret_branch_floor WHERE floor_status = 1");

		return $data->result_array();

	}



	// Counter Master

	function ajax_getcounter($branch="",$floor="",$from_date,$to_date)

    {

		$data = array();

		$sql = "SELECT * FROM ret_branch_floor_counter as c

		left join ret_branch_floor f on f.floor_id=c.floor_id

		left join branch r on r.id_branch=f.branch_id";



		if($floor!='' && $branch!='' && $from_date!='')

		{

			$sql = $sql." where r.id_branch =" .$branch. ' and c.floor_id = '.$floor. ' and (date(c.created_on) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")';

		}

		elseif($floor!='' && ($branch!='' && $branch > 0))

		{

			$sql = $sql." where r.id_branch =" .$branch. " and c.floor_id = ".$floor;

		}

		elseif($floor!='' && $from_date!='')

		{

			$sql = $sql." where c.floor_id =" .$floor. ' and (date(c.created_on) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")';

		}

		elseif(($branch!='' && $branch > 0) && $from_date!='')

		{

			$sql = $sql." where r.id_branch =" .$branch. ' and (date(c.created_on) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")';

		}

		elseif($floor!='' || ($branch!='' && $branch > 0) || $from_date!= ''){

			$sql = $sql." where ".($floor != '' ? ('c.floor_id ='.$floor) : ($branch!='' && $branch > 0 ? 'branch_id ='.$branch: ($from_date != '' ? ' date(c.created_on) BETWEEN '.date('Y-m-d',strtotime($from_date)).' AND '.date('Y-m-d',strtotime($to_date)) : ('')))) ;

		}

		$result = $this->db->query($sql);

		$data = $result->result_array();

		return $data;

	}



	function get_counter($counter_id)

    {

		$counter_id = $this->db->query("select * from ret_branch_floor_counter where counter_id=".$counter_id);

		return $counter_id->row_array();

	}



	function getActiveCounters()

    {

        $id_branch=(isset($_POST['id_branch']) ? ($_POST['id_branch']!='' ?$_POST['id_branch'] :''):'');

		$data = $this->db->query("SELECT c.counter_id,c.floor_id,c.counter_name,c.counter_short_code,c.system_fp_id,c.counter_status,c.sort

        FROM ret_branch_floor_counter c

        LEFT JOIN ret_branch_floor f ON f.floor_id=c.floor_id

        WHERE c.counter_status=1

        ".($id_branch!='' ? " and f.branch_id=".$id_branch."" :'')."");

		return $data->result_array();

	}



	/**

	*	Master :: Counter -- Starts

	*/

	function ajax_get_makingtype($from_date,$to_date)

	{

		$data = array();

		$sql = ("SELECT mak_id,mak_name,mak_short_code,mak_status,

		created_on FROM ret_making_type");

		if($from_date!='')

		{

		$sql = $sql.( ' where (date(created_on) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

		}

		$result = $this->db->query($sql);

		$data = $result->result_array();

		return $data;

	}



	function get_make_type($mak_id)

	{

		$counter_id = $this->db->query("select * from ret_making_type where mak_id=".$mak_id);

		return $counter_id->row_array();

	}

	/**

	*	Master :: Counter -- Ends

	*/



	/**

	*	Master :: Theme -- Ends

	*/

	function ajax_gettheme($from_date,$to_date)

    {

		$data = array();

		$sql  = ("SELECT id_theme,theme_name,theme_code,

		theme_desc,theme_status,created_on FROM ret_theme");

		if($from_date!='')

		{

			$sql = $sql." where".('(date(created_on)

			BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

		}

		$result=$this->db->query($sql);

		$data = $result->result_array();

		return $data;



	}

	function get_theme($id_theme)

    {

		$id_theme = $this->db->query("select * from ret_theme where id_theme=".$id_theme);

		return $id_theme->row_array();

	}

	function getActiveTheme()

   {

       $data = $this->db->query("SELECT id_theme,theme_name,theme_desc,IFNULL(theme_code,'') as code FROM `ret_theme` WHERE theme_status = 1");

       return $data->result_array();

   }

	/**

	*	Master :: Theme -- Ends

	*/



	/**

	*	Master :: Material -- Ends

	*/

	function ajax_getmaterial($from_date,$to_date)

    {

		$data = array();

		$sql  = ("SELECT material_id,material_name,material_code,material_status,

		created_on FROM ret_material");

		if($from_date!='')

		{

			$sql = $sql." where".('(date(created_on)

			BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

		}

		$result=$this->db->query($sql);

		$data = $result->result_array();

		return $data;



	}

	function get_material($material_id)

    {

		$result = $this->db->query("select * from ret_material where material_id=".$material_id);

		return $result->row_array();

	}

	function get_material_lst()

    {

		$data = $this->db->query("SELECT Distinct m.material_id,m.material_name FROM ret_material m left join ret_material_rate mtr on m.material_id = mtr.material_id WHERE mtr.material_id is NULL and m.material_status=1");

		return $data->result_array();

	}



 	function ajax_getmtrrate($from_date,$to_date,$material_id)

    {

		$data = array();

		$sql  ="SELECT m.material_id,m.material_name,mtr.mat_rate_id,mtr.mat_rate,Date_Format(mtr.effective_date,'%d-%m-%Y') as effective_date,mtr.created_on FROM ret_material_rate mtr

		left join ret_material m on m.material_id = mtr.material_id";

		if(($material_id!='' && $material_id > 0) && $from_date!='')

		{

			$sql = $sql." where status=1 and mtr.material_id =" .$material_id. ' and (date(mtr.created_on)

			BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")';

		}

		elseif(($material_id!='' && $material_id > 0 ) || $from_date!='')

		{

			$sql = $sql." where status=1".($material_id != '' && $material_id > 0  ? ('and mtr.material_id ='.$material_id) : ($from_date != '' ? ' date(mtr.created_on) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : (''))) ;

		}

		else

		{

			$sql=$sql." where status=1";

		}

		$result=$this->db->query($sql);

		$data = $result->result_array();

		return $data;



	}



	function get_materialrate($mat_rate_id)

    {

		$mat_rate_id = $this->db->query("select material_id,mat_rate_id,mat_rate,Date_Format(effective_date,'%d-%m-%Y') as effective_date from ret_material_rate where mat_rate_id=".$mat_rate_id);

		return $mat_rate_id->row_array();

	}

	function getActiveMaterial()

   {

		$data = $this->db->query("SELECT material_id,material_name,IFNULL(material_code,'') as code FROM `ret_material` WHERE material_status = 1");

		return $data->result_array();

   }

	/**

	*	Master :: Material -- Ends

	*/



	/**

	*	Master :: Product -- Starts

	*/

	function getActiveProducts(){

		$result = $this->db->query("select pro_id,product_name as name,IFNULL(product_short_code,'') as code,

		weight_range_based,reorder_based_on

		from ret_product_master where product_status =1");

		return $result->result_array();

	}



	function getActiveSearchProd($SearchTxt){ // For autocomplete

		$result = $this->db->query("select pro_id as value,if(product_short_code = '' or product_short_code is null ,product_name ,CONCAT(product_short_code,' - ',product_name) ) as label from ret_product_master where product_status=1 and (product_name like '%".$SearchTxt."%' or product_short_code like '%".$SearchTxt."%')");

		return $result->result_array();

	}

	function ajax_get_TaxProd($pro_id,$tax_group_id,$product_status,$from_date,$to_date)

    {

		$data = array();

		$sql  = ("SELECT pr.pro_id,tx.tgrp_id,tx.tgrp_name,pr.product_name,pr.product_status,pr.created_time,pr.tax_group_id,pr.product_short_code

		FROM ret_product_master as pr

		left join ret_taxgroupmaster tx on tx.tgrp_id = pr.tax_group_id");

		if($pro_id!='' && $tax_group_id!='' && $from_date!='' && $product_status!='')

		{

			$sql = $sql." where tx.tgrp_id =" .$tax_group_id. ' and pr.pro_id = '.$pro_id. ' and pr.product_status='.$product_status.'(date(pr.created_time) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")';

		}

		elseif($pro_id!='' && $tax_group_id!='' && $from_date!='')

		{

			$sql = $sql." where tx.tgrp_id =" .$tax_group_id. ' and pr.pro_id = '.$pro_id. ' and (date(pr.created_time) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")';

		}

		elseif($pro_id!='' && $tax_group_id!='' && $product_status!='')

		{

			$sql = $sql." where tx.tgrp_id =" .$tax_group_id. ' and pr.pro_id = '.$pro_id. ' and pr.product_status='.$product_status;

		}

		elseif($pro_id!='' || ($tax_group_id!='' && $tax_group_id > 0) || $from_date!= ''){

			$sql = $sql." where ".($pro_id != '' ? ('pr.pro_id ='.$pro_id) : ($tax_group_id!='' && $tax_group_id > 0 ? 'tgrp_id ='.$tax_group_id: ($from_date != '' ? ' date(pr.created_time) BETWEEN '.date('Y-m-d',strtotime($from_date)).' AND '.date('Y-m-d',strtotime($to_date)) : ('')))) ;

		}

		elseif($pro_id!='' && ($tax_group_id!='' && $tax_group_id > 0))

		{

			$sql = $sql." where tx.tgrp_id =" .$tax_group_id. " and pr.pro_id = ".$pro_id;

		}

		elseif($product_status!='' && ($tax_group_id!='' && $tax_group_id > 0))

		{

			$sql = $sql." where tx.tgrp_id =" .$tax_group_id. " and pr.product_status = ".$product_status;

		}

		elseif($product_status!='' && ($pro_id!='' && $pro_id > 0))

		{

			$sql = $sql." where pr.pro_id =" .$pro_id. " and pr.product_status = ".$product_status;

		}

		elseif($pro_id!='' && $from_date!='')

		{

			$sql = $sql." where pr.pro_id =" .$pro_id. ' and (date(pr.created_time) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")';

		}

		elseif($product_status!='' && $from_date!='')

		{

			$sql = $sql." where pr.product_status =" .$product_status. ' and (date(pr.created_time) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")';

		}

		elseif(($tax_group_id!='' && $tax_group_id > 0) && $from_date!='')

		{

			$sql = $sql." where tx.tgrp_id=" .$tax_group_id. ' and (date(pr.created_time) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")';

		}



		elseif($product_status!='' || ($tax_group_id!='' && $tax_group_id > 0) || $from_date!= ''){

			$sql = $sql." where ".($product_status != '' ? ('pr.product_status ='.$product_status) : ($tax_group_id!='' && $tax_group_id > 0 ? 'tgrp_id ='.$tax_group_id: ($from_date != '' ? ' date(pr.created_time) BETWEEN '.date('Y-m-d',strtotime($from_date)).' AND '.date('Y-m-d',strtotime($to_date)) : ('')))) ;

		}

		elseif($pro_id!='' || $product_status!='' ||($tax_group_id!='' && $tax_group_id > 0)|| $from_date!= ''){

			$sql = $sql." where ".($pro_id != '' ? ('pr.pro_id ='.$pro_id) : ($product_status != '' ?('pr.product_status='.$product_status): ($tax_group_id!='' && $tax_group_id > 0 ? 'tgrp_id ='.$tax_group_id: ($from_date != '' ? ' date(pr.created_time) BETWEEN '.date('Y-m-d',strtotime($from_date)).' AND '.date('Y-m-d',strtotime($to_date)) : (''))))) ;

		}



		//echo $sql;exit;

		$result=$this->db->query($sql);

		$data=$result->result_array();

		return $data;

	}

	/**

	*	Master :: Product -- Ends

	*/



	/**

	*	Master :: Product -- Starts

	*/

	function getActiveSubProducts(){

		$result = $this->db->query("select sub_pro_id as value,sub_pro_name as name,IFNULL(sub_pro_code,'') as code from ret_sub_product_master where sub_pro_status=1");

		return $result->result_array();

	}



	function getActiveSearchSubProd($SearchTxt,$id_prod){

		if(!empty($id_prod)){

			$result = $this->db->query("select sp.sub_pro_id as value,if(sub_pro_code = '' or sub_pro_code is null ,sub_pro_name ,CONCAT(sub_pro_code,' - ',sub_pro_name) ) as label

		from ret_sub_product_master sp

		LEFT JOIN ret_product_sub_product psp on sp.sub_pro_id = psp.sub_pro_id

		where psp.pro_id=".$id_prod." and sub_pro_status=1 and (sub_pro_name like '%".$SearchTxt."%' or sub_pro_code like '%".$SearchTxt."%')");

		}else{

			$result = $this->db->query("select sp.sub_pro_id as value,if(sub_pro_code = '' or sub_pro_code is null ,sub_pro_name ,CONCAT(sub_pro_code,' - ',sub_pro_name) ) as label

		from ret_sub_product_master sp

		where sub_pro_status=1 and (sub_pro_name like '%".$SearchTxt."%' or sub_pro_code like '%".$SearchTxt."%')");

		}







		return $result->result_array();

	}

	/**

	*	Master :: Product -- Ends

	*/



	/**

	*	Master :: Stone -- Starts

	*/

	function getActiveStone()

    {

		$data = $this->db->query("SELECT stone_id,stone_name,IFNULL(stone_code,'') as code FROM `ret_stone` WHERE stone_status = 1");

		return $data->result_array();

	}



	function ajax_get_stone($from_date,$to_date)

	{

		$data = array();

		$sql = ("SELECT s.stone_id,s.uom_id,s.stone_name,s.stone_code,s.stone_status,s.created_on,u.uom_name FROM ret_stone s

				left join ret_uom u on u.uom_id=s.uom_id");



		if($from_date!='')

		{

			$sql = $sql.( ' where (date(s.created_on) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

		}

		$result = $this->db->query($sql);

		$data = $result->result_array();

		return $data;

	}



	function get_stone($stone_id)

	{

		$counter_id = $this->db->query("select * from ret_stone where stone_id=".$stone_id);

		return $counter_id->row_array();

	}

	/**

	*	Master :: Stone -- Ends

	*/



	/**

	*	Master :: UOM -- Starts

	*/

	function ajax_getUOM($from_date,$to_date)

    {

		$data = array();

		$sql  = ("SELECT uom_id,uom_name,uom_short_code,uom_status,created_on FROM ret_uom");

		if($from_date!='')

		{

			$sql = $sql." where".('(date(created_on)

			BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

		}

		$result=$this->db->query($sql);

		$data = $result->result_array();

		return $data;



	}

	function get_uom($uom_id)

    {

		$uom_id = $this->db->query("select uom_id,uom_name,uom_short_code,uom_status from ret_uom where uom_id=".$uom_id);

		return $uom_id->row_array();

	}

	function getActiveUOM()

    {

		$data = $this->db->query("SELECT uom_id,uom_name,IFNULL(uom_short_code,'') as code,divided_by_value,is_default FROM `ret_uom` WHERE uom_status = 1");

		return $data->result_array();

	}

	/**

	*	Master :: UOM -- Ends

	*/



	/**

	*	Master :: Category -- Starts

	*/

	function get_catByMetal($id)

	{

		$sql = $this->db->query("SELECT c.id_metal,id_ret_category,c.name as name,c.cat_type

		from ret_category c

		left join metal m on m.id_metal=c.id_metal

		where c.id_metal=".$id);

		return $sql->result_array();

	}

	function ajax_getcategory($from_date,$to_date)

    {

		$data = array();

		$sql  = ("SELECT c.id_ret_category ,c.name,m.id_metal,c.cat_code,c.is_multimetal, if(c.is_multimetal = 1, 'Yes', 'No') AS multimetal, m.metal,c.status,c.image,c.created_on, c.hsn_code FROM ret_category as c left join metal m on m.id_metal=c.id_metal");

		if($from_date!='')

		{

			$sql = $sql." where".('(date(c.created_on)

			BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

		}

		$result=$this->db->query($sql);

		$data = $result->result_array();

		return $data;



	}

	function get_ret_category($id_category)

    {

		$id_category  = $this->db->query("select * from ret_category where id_ret_category =".$id_category);

		return $id_category->row_array();

	}

	function get_category_purity($id)

	{

	   $list_purity=[];

	   $id1=$this->db->query("select id_category,id_purity from ret_metal_cat_purity where id_category=".$id);

      for($i=0;$i<count($id1->result_array());$i++)

      {

        array_push($list_purity,

        $id1->result_array()[$i]['id_purity']);

      }

       return $list_purity;

	}



	function getActiveCategorymtr($data)

    {

        $id_metal=(isset($data['id_metal']) ? ($data['id_metal']!='' ? $data['id_metal'] :'') :'');

		$data = $this->db->query("SELECT id_ret_category,name,description,id_metal,image,tgrp_id FROM `ret_category` WHERE status = 1

		".($id_metal!='' ? " and id_metal=".$id_metal."" :'')."");

		return $data->result_array();

	}

	function getCatPurity($id_cat)

    {

        $data = $this->db->query("SELECT cp.id_purity, p.purity, cp.id_category FROM `ret_metal_cat_purity` cp

        LEFT JOIN ret_purity p on p.id_purity = cp.id_purity

        WHERE id_category IS NOT NULL

        ".($id_cat!='' && $id_cat>0 ? " and id_category = ".$id_cat."":"")."

		group by cp.id_category, p.id_purity");

        return $data->result_array();

	}

	/**

	*	Master :: Category -- Ends

	*/



	/**

	*	Master :: Karigar -- Starts

	*/



	function getActiveKarigar($karigar_for = "")

	{

		$data = $this->db->query("SELECT id_karigar,

		if(karigar_type=1,ifnull(company,CONCAT(firstname,' ',IFNULL(lastname,''))),CONCAT(firstname,' ',IFNULL(lastname,''))) as karigar, IFNULL(code_karigar,'') as code,

		karigar_type as karigartpe, id_state, id_country, if(is_tcs = 1, tcs_tax, 0) as tcs_tax,

		if(is_tds = 1, tds_tax, 0) as tds_tax,karigar_for

		FROM `ret_karigar` WHERE status_karigar = 1

		".($karigar_for !='' ? " and karigar_for=".$karigar_for."" :'')."

		");



		return $data->result_array();

	}

	function ajax_getkarigar($from_date,$to_date,$karigar_for,$karigar_type)

	{



		$data = array();

			$sql = ("SELECT id_karigar,firstname,urname,contactno1,status_karigar,createdon,IF(karigar_for=1,'Jewellery Manufacturer',if(karigar_for=2,'Jewellery Supplier',if(karigar_for=3,'Testing / HM Centers',if(karigar_for=4,'Other Inventory Supplier',if(karigar_for=5,'Supplier & Manufacturer',''))))) as karigartype,

			if(karigar_type=0,'Individual',if(karigar_type=1,'Company','Firm'))as usertype

			FROM ret_karigar where id_karigar IS NOT NULL

			".($karigar_type!='' ? " and karigar_type=".$karigar_type."" :'')."

			".($karigar_for!='' ? " and karigar_for=".$karigar_for."" :'')."

			");

			if($from_date!='')

			{

			$sql = $sql.( ' and (date(createdon) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

			}

		$result = $this->db->query($sql);

		$data = $result->result_array();

		return $data;

	}

	function get_karigar($id_karigar)

	{

		$id = $this->db->query("select * from ret_karigar where id_karigar=".$id_karigar);

		return $id->row_array();

	}



	function get_karigar_wastages($id_karigar)

	{

		$id = $this->db->query("select * from ret_karikar_items_wastage where id_karikar=".$id_karigar."  and active = 1 ORDER BY id_karikar_wast ASC");

		return $id->result_array();

	}





	function get_karigar_stones($id_karigar)

	{

		$id = $this->db->query("SELECT * FROM ret_karigar_stones where id_karigar=".$id_karigar." and active = 1 ORDER BY id_karigar_stone ASC");

		//print_r($this->db->last_query());exit;

		return $id->result_array();

	}



	function get_karigar_charges($id_karikar_wast)

	{

		$id = $this->db->query("SELECT * FROM ret_karigar_charges where id_karikar_wast=".$id_karikar_wast." ORDER BY id_karigar_charges  ASC");

		//print_r($this->db->last_query());exit;

		return $id->result_array();

	}









	public function mobile_available($mobile)

	{

		$this->db->select('contactno1');

		$this->db->where('contactno1', $mobile);

		$status=$this->db->get('ret_karigar');

		if($status->num_rows()>0)

		{

			$return_data=array('status'=>false,'message'=>'Mobile Number Already Exists');

		}else{

			$return_data=array('status'=>true,'message'=>'');

		}

		return $return_data;

	}





	public function email_available($email)

	{

		$this->db->select('email');

		$this->db->where('email', $email);

		$status=$this->db->get('ret_karigar');

		if($status->num_rows()>0)

		{

			$return_data=array('status'=>false,'message'=>'Email Already Exists');

		}else{

			$return_data=array('status'=>true,'message'=>'');

		}

		return $return_data;

	}

	/**

	*	Master :: Karigar -- Ends

	*/



	/**

	*	Master :: Tag Type -- Starts

	*/

	function ajax_gettag($from_date,$to_date)

    {

		$data = array();

		$sql  = ("SELECT tag_id,tag_name,tag_status,created_time FROM ret_tag_type_master");

		if($from_date!='')

		{

			$sql = $sql." where".('(date(created_time)

			BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

		}

		$result=$this->db->query($sql);

		$data = $result->result_array();

		return $data;



	}

	function get_tag($tag_id)

    {

		$tag_id = $this->db->query("select tag_id,tag_name,tag_status from ret_tag_type_master where tag_id=".$tag_id);

		return $tag_id->row_array();

	}

	function getActivetag()

    {

		$data = $this->db->query("SELECT tag_id,tag_name FROM `ret_tag_type_master` WHERE tag_status = 1");

		return $data->result_array();

	}

	/**

	*	Master :: Tag Type -- Ends

	*/



	/**

	*	Master :: Design -- Starts

	*/

	function getSearchDesign($SearchTxt,$id_prod)

    {

		$result = $this->db->query("SELECT design_no as value,if(design_code = '' or design_code is null ,design_name ,CONCAT(design_code,' - ',design_name) ) as label from ret_design_master where design_status=1 and product_id=".$id_prod." and (design_name like '%".$SearchTxt."%' or design_code like '%".$SearchTxt."%')");

		return $result->result_array();

	}

	function genDesignShortCode()

	{

		$sql = $this->db->query("SELECT MAX(CAST(design_code AS UNSIGNED)) AS design_code FROM ret_design_master;");

		$lastCode = $sql->row()->design_code;

		if($sql->num_rows() == 0){

			return 1;

		}else{

			return $lastCode+1;

		}

	}

	function get_empty_design()

	{

		$data=array(

						'product_id'           => NULL,

						'design_id'            => NULL,

						'design_no'            => NULL,

		                'default_img'          => NULL,

						'design_status'        => 1,

						'image'                => NULL,

						'id_image'             => NULL,

						'design_code'		   => NULL,

						'design_name'		   => NULL,

						'theme'		           => NULL,

						'hook_type'		       => NULL,

						'mc_cal_type'          => 1,

						'mc_cal_value'         => NULL,

						'wastage_type'         => 1,

						'wastag_value'         => NULL,

						'screw_type'		   => NULL,

						'design_for'		   => 2,

						'min_length'		   => NULL,

						'max_length'		   => NULL,

						'min_width'            => NULL,

						'max_width'            => NULL,

						'min_dia'		       => NULL,

						'max_dia'		       => NULL,

						'min_weight'		   => NULL,

						'max_weight'		   => NULL,

						'fixed_rate'		   => NULL,

						'usage_type'           => 1,

						'created_time'         => NULL,

						'id_size'               => NULL,

						'create_by'            => $this->session->userdata('uid'));

		return $data;

	}

	function getkarigar()

	{

		$data=array(

		'karigar_id'           => NULL );

		return $data;

	}

		function getpurity()

	{

		$data=array(

		 'pur_id'               => NULL );

		return $data;

	}

		function getmaterial()

	{

		$data=array(

		'material_id'          => NULL );

		return $data;

	}

		function getstone()

	{

		$data=array(

		'stone_id'           => NULL );

		return $data;

	}

		function getsize()

	{

		$data=array(

		'uom_id'                 => NULL );

		return $data;

	}

	
	function ajax_get_design($from_date,$to_date,$id_product)

	{
		$data = array();

			// NOTE: Removed LEFT JOIN on ret_product_mapping — that join caused
			// duplicate rows when a design has multiple mapping entries.
			// ret_design_master already carries product_id, so we filter directly.
			$sql = "SELECT design_no,design_code,design_name,d.created_time,design_status

			        from ret_design_master d

			        where d.design_no IS NOT NULL

					".($id_product!='' ? " and d.product_id=".$id_product."" :'')."

					";

		//print_r($sql);exit;

		$result = $this->db->query($sql);

		$data = $result->result_array();

		return $data;

	}



	function ajax_get_retmaster(){

	    $returndata = array();

	    $catsql         = $this->db->query("SELECT id_ret_category, id_metal, name, hsn_code, cat_code from ret_category");

	    $prosql         = $this->db->query("SELECT pro_id, cat_id, stock_type, sales_mode, purchase_mode,  product_short_code, product_name  FROM `ret_product_master`");

	    $designsql      = $this->db->query("SELECT design_no, design_code, design_name, product_id, mc_cal_type, ifnull(mc_cal_value,0) as mc_cal_value, ifnull(wastag_value,0) as wastag_value from ret_design_master");

	    $catresult      = $catsql->result_array();

	    $proresult      = $prosql->result_array();

	    $designresult   = $designsql->result_array();



	    foreach($catresult as $ckey=>$catrow){

	        $cat_pro_design = $catrow;

	        $product_row = array();

	        foreach($proresult as $prorow){

	            $pro_des_row = array();

	            if($prorow['cat_id'] == $catrow['id_ret_category']){

	                $pro_des_row = $prorow;

	                foreach($designresult as $desrow){

	                    if($desrow['product_id'] == $prorow['pro_id']){

	                        $pro_des_row['design'][] = $desrow;

	                    }

	                }

	            }



	            if(!empty($pro_des_row)){

	                $product_row[] = $pro_des_row;

	            }

	        }

	        $catresult[$ckey]['prodata'] = $product_row;

	    }

	    return $catresult;

	}

	function get_designimage($id)

    {

		$product = $this->db->query("select id_design, image,is_default,id_image from ret_design_images where id_design=".$id);

		return $product->result_array('image');

	}

	function get_ret_design($id)

	{

			$id = $this->db->query("select * from ret_design_master where design_no=".$id);

		    return $id->row_array();

	}

	function get_design_karigar($id)

	{

		$list_karigar=[];

        $this->db->select('karigar_id');

        $this->db->where('design_id',$id);

        $result=$this->db->get('ret_design_karigars');

      for($i=0;$i<count($result->result_array());$i++)

      {

        array_push($list_karigar,

        $result->result_array()[$i]['karigar_id']);

      }

       return $list_karigar;

	}

	function get_design_material($id)

	{

		$list_material=[];

        $this->db->select('material_id');

        $this->db->where('design_id',$id);

        $result=$this->db->get('ret_design_other_materials');

      for($i=0;$i<count($result->result_array());$i++)

      {

        array_push($list_material,

        $result->result_array()[$i]['material_id']);

      }

       return $list_material;

	}

	function get_design_purity($id)

	{

	   $list_purity=[];

        $this->db->select('pur_id');

        $this->db->where('design_id',$id);

        $result=$this->db->get('ret_design_purity');

      for($i=0;$i<count($result->result_array());$i++)

      {

        array_push($list_purity,

        $result->result_array()[$i]['pur_id']);

      }

       return $list_purity;

	}

	function get_design_size($id)

	{

		$id = $this->db->query("select * from ret_design_sizes where design_id=".$id);

		return $id->row_array();

	}

	function get_productData($id)

	{

		$sql="SELECT pro_id,has_screw,has_hook,has_stone,has_size from ret_product_master where pro_id=".$id;

   	    $result = $this->db->query($sql);

		return $result->row_array();

	}

	function delete_designimage($file)

    {

		$this->db->where('image',$file);

        $status= $this->db->delete('ret_design_images');

		return $status;

	}



	/**

	*	Master :: Design -- Ends

	*/



	/**

	*	Master :: Tax -- Starts

	*/

	function ajax_gettax($from_date,$to_date)

    {

		$data = array();

		$sql  = ("SELECT tax_id,tax_name,tax_code,tax_percentage,branch_code,tax_status,created_on FROM ret_taxmaster");

		if($from_date!='')

		{

			$sql = $sql." where".('(date(created_on)

			BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

		}

		$result=$this->db->query($sql);

		$data = $result->result_array();

		return $data;



	}

	function get_tax($tax_id)

    {

		$tax_id = $this->db->query("select tax_name,tax_code,tax_percentage,tax_status from ret_taxmaster where tax_id=".$tax_id);

		return $tax_id->row_array();

	}

	function get_tgrp_items($tgrp_id)
	{

		$sql = "
        SELECT 
            tgi.*, t.tgrp_name
        FROM ret_taxgroupitems tgi
        LEFT JOIN ret_taxgroupmaster t 
            ON t.tgrp_id = tgi.tgi_taxcode
        WHERE tgi.tgi_tgrpcode = ?
    ";

		$query = $this->db->query($sql, [$tgrp_id]);

		// Debug if needed
		// echo $this->db->last_query();
		// exit;

		return $query->result_array();
	}

	function getActivetax()

    {

		$data = $this->db->query("SELECT tax_id,tax_name,tax_code,tax_percentage FROM `ret_taxmaster` WHERE tax_status = 1");

		return $data->result_array();

	}

	function get_empty_tgrp()

	{

		$data=array(

			'tgrp_id'			    => NULL,

			'tgrp_name'			    => NULL,

			'tgrp_status'			=> 1,



		);

		return $data;

	}



 	function ajax_gettgrp($from_date,$to_date)

    {

		$data = array();

		$sql  = ("SELECT tgrp_id,tgrp_name,tgrp_status,created_time FROM ret_taxgroupmaster");

		if($from_date!='')

		{

			$sql = $sql." where".('(date(created_time)

			BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

		}

		$result=$this->db->query($sql);

		$data=$result->result_array();

		return $data;



	}

	function get_tgrp($tgrp_id)

    {

		$tgrp_id = $this->db->query("select * from ret_taxgroupmaster where tgrp_id=".$tgrp_id);

		return $tgrp_id->row_array();

	}

	function getActivetgrp()

    {

		$data = $this->db->query("SELECT * FROM `ret_taxgroupmaster`");

		return $data->result_array();

	}

	function get_empty_tgi()

	{

	 $data=array(

			'tgi_sno'			=> NULL,

			'tgi_tgrpcode'	    => NULL,

			'tgi_taxcode'	    => NULL,

			'tgi_calculation'   => NULL,

			'tgi_type'          => 1,

		);

		return $data;

	}

	function get_tgi($tgi_sno)

    {

		$tgi_sno = $this->db->query("SELECT * FROM `ret_taxgroupmaster` WHERE tgi_sno =".$tgi_sno);

		return $tgi_sno->result_array();

	}



	/**

	*	Master :: Tax -- Ends

	*/



	/**

	*	Master :: Product -- Starts

	*/

	function ajax_get_retProduct($from_date,$to_date)

	{

		$data = array();

			$sql = ("SELECT p.pro_id,p.cat_id,p.product_name,p.product_short_code,c.name,p.created_time,p.product_status,p.image as image,r.section_name,
			tm.tgrp_name,p.hsn_code,if(p.stock_type=1,'Tagged','Non tagged') as stock_type,
			IF(p.calculation_based_on = 0, ' Mc & Wast On Gross',
			IF(p.calculation_based_on = 1, ' Mc & Wast On Net',
			IF(p.calculation_based_on = 2, 'Mc on Gross,Wast On Net',
			IF(p.calculation_based_on = 3, 'Fixed Rate','Fixed Rate based on Weight')))) AS calculation_based_on,
			IF(p.sales_mode =1,'Fixed Rate','Flexible') as sales_based_on

			FROM ret_product_master p

			left join ret_category c on c.id_ret_category=p.cat_id

			left join ret_taxgroupmaster tm on tm.tgrp_id=p.tax_group_id

			left join ret_taxgroupmaster tr on tr.tgrp_id=p.tgrp_id

			left join ret_section r on p.id_section=r.id_section");

             //print_r($sql);exit;

			if($from_date!='')

			{

			$sql = $sql.( ' where (date(p.created_time) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

			}

		$result = $this->db->query($sql);

		$data = $result->result_array();

		return $data;

	}

	function genProdShortCode()

	{

		$sql = $this->db->query("SELECT max(product_short_code) as product_short_code  from ret_product_master");

		$lastCode = $sql->row()->product_short_code;

		if($sql->num_rows() == 0){

			return 1;

		}else{

			return $lastCode+1;

		}

	}



	function get_ret_product($id_product)

	{

		$id = $this->db->query("select * from ret_product_master where pro_id=".$id_product);

		return $id->row_array();

	}

	function get_product_section($id_product)

	{

		$id = $this->db->query("select * from ret_product_section where pro_id=".$id_product);

		return $id->result_array();

	}

	function getActiveProduct(){



		$result = $this->db->query("select * from ret_product_master where product_status=1 ".($_POST['id_category']!='' ? " and cat_id=".$_POST['id_category']."" :'')."");

		return $result->result_array();

	}

	public function getProd_empty_record()

    {

		$data=array(

		'pro_id'           		=> NULL,

		'cat_id'           		=> NULL,

		'id_section'           	=> NULL,

		'hsn_code'             	=> NULL,

		'stock_type'           => 1,

		'stone_type'           => 1,

		'sales_mode'   		   => 2,

		'purchase_mode'   	  => 2,

		'reorder_based_on'    => 1,

		'wastage_type'   	   => 2,

		'min_wastage'			=> NULL,

		'max_wastage'			=> NULL,

		'other_materials'      => 0,

		'has_stone'		       => 0,

		'has_hook'		       => 0,

		'has_screw'		       => 0,

		'has_fixed_price'	   => 0,

		'metal_type'		   => NULL,

		'product_short_code'   => NULL,

		'product_name'		   => NULL,

		'has_size'		       => 0,

		'less_stone_wt'		   => 0,

		'tag_split'		       => 0,

		'tag_merge'		       => 0,

		'tag_type'		       => 0,

		'other_charges'		   => 0,

		'net_wt'		       => 0,

		'stock_report'		   => 0,

		'central_exces_duty'   => 0,

		'no_of_pieces'		   => NULL,

		'rfid_required'		   => 0,

		'rfid_in_stock'		   => 0,

		'hallmark'		       => 0,

		'counter'		       => 1,

		'stone_board_rate_cal' => 0,

		'calculation_based_on' => 2,

		'sales_markup'		   => 0,

		'max_markup_per_for_rateitems'  => NULL,

		'no_of_tags_to_print'   => NULL,

		'tax_group_id'       	=>NULL,

		'product_status'       	=> 1,

		'tax_type'              => 2,

		'display_purity'        => 1,

		'created_time'          => NULL,

		'create_by'            	=> $this->session->userdata('uid'),

		'image'                 =>NULL);



		return $data;

	}



	/**

	*	Master :: Product -- Ends

	*/



	/**

	*	Master :: Sub Product -- Starts

	*/



	function ajax_get_retSubProduct($from_date,$to_date)

	{

		$data = array();

			$sql = ("SELECT s.sub_pro_id,s.sub_pro_name,s.sub_pro_code,s.sub_pro_status,s.created_time

			from ret_sub_product_master s");



			if($from_date!='')

			{

			$sql = $sql.( ' where (date(s.created_time) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

			}

		$result = $this->db->query($sql);

		$data = $result->result_array();

		return $data;

	}

	function get_ret_subproduct($id)

	{

		$id = $this->db->query("select * from ret_sub_product_master where sub_pro_id=".$id);

		return $id->row_array();

	}

	/**

	*	Master :: Sub Product -- Ends

	*/



	/**

	*	Master :: Metal -- Starts

	*/

	function ajax_getmetal($from_date,$to_date)

    {

		$data = array();

		$sql  = ("SELECT m.id_metal,m.metal,m.tgrp_id,tgrp_name,m.metal_code,m.metal_status,m.created_on FROM metal m

					left join ret_taxgroupmaster tg on tg.tgrp_id = m.tgrp_id");

		if($from_date!='')

		{

			$sql = $sql." where".('(date(m.created_on)

			BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

		}

		$result=$this->db->query($sql);

		$data = $result->result_array();

		return $data;



	}

	function get_metals($id_metal)

    {

		$id_metal = $this->db->query("select id_metal,tgrp_id,metal,metal_code,metal_status from metal where id_metal=".$id_metal);

		return $id_metal->row_array();

	}



	function getActivemetal()

    {

		$data = $this->db->query("SELECT id_metal,metal,IFNULL(metal_code,'') as code FROM `metal` WHERE metal_status = 1");

		return $data->result_array();

	}

	/**

	*	Master :: Metal -- Ends

	*/







	/**

	*	Master :: Screw -- Starts

	*/

	function ajax_getscrew($from_date,$to_date)

    {

		$data = array();

		$sql  = ("SELECT screw_id,screw_name,screw_short_code,screw_status,created_on FROM ret_screw_type");

		if($from_date!='')

		{

			$sql = $sql." where".('(date(created_on)

			BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

		}

		$result=$this->db->query($sql);

		$data = $result->result_array();

		return $data;



	}

	function get_screw($screw_id)

    {

		$screw_id = $this->db->query("select screw_id,screw_name,screw_short_code,screw_status from ret_screw_type where screw_id=".$screw_id);

		return $screw_id->row_array();

	}

	function getActivescrew()

    {

		$data = $this->db->query("SELECT screw_id,screw_name,IFNULL(screw_short_code,'') as code FROM `ret_screw_type` WHERE screw_status = 1");

		return $data->result_array();

	}

	/**

	*	Master :: Screw -- Ends

	*/



	/**

	*	Master :: Hook -- Starts

	*/

	function ajax_gethook($from_date,$to_date)

    {

		$data = array();

		$sql  = ("SELECT hook_id,hook_name,hook_short_code,hook_status,created_on FROM ret_hook_type");

		if($from_date!='')

		{

			$sql = $sql." where".('(date(created_on)

			BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

		}

		$result=$this->db->query($sql);

		$data = $result->result_array();

		return $data;



	}

	function get_hook($hook_id)

    {

		$hook_id = $this->db->query("select hook_id,hook_name,hook_short_code,hook_status from ret_hook_type where hook_id=".$hook_id);

		return $hook_id->row_array();

	}

	function getActivehook()

    {

		$data = $this->db->query("SELECT hook_id,hook_name,IFNULL(hook_short_code,'') as code FROM `ret_hook_type` WHERE hook_status = 1");

		return $data->result_array();

	}

	/**

	*	Master :: Hook -- Ends

	*/



	/**

	*	Master :: Financial Year -- Starts

	*/

	function fincnce_empty_record()



    {







		$emptyquery = $this->db->field_data('ret_financial_year');



		$emptydata = array();



		foreach ($emptyquery as $field)



		{



			$emptydata[$field->name] = $field->default;



		}



		$emptydata['tag_datetime'] = date('d-m-Y');



		$emptydata['design_code']  = '';



		return $emptydata;



	}







	function ajax_get_financial_year_List()

    {

        $sql = $this->db->query(" SELECT f.fin_id,f.fin_year_name as fin_code,f.fin_year_code,CONCAT(date_format(f.fin_year_from,'%Y-%m-%d'),'  / ',date_format(f.fin_year_to,'%Y-%m-%d')) as fin_year,f.fin_status,f.fin_year_code,date_format(f.created_on,'%Y-%m-%d')as created_on

        FROM ret_financial_year f

        ORDER BY f.fin_id desc");

        return $sql->result_array();

	}



    function get_financialyear_by_status()

    {

        $sql = $this->db->query(" SELECT f.fin_id,f.fin_year_name as fin_code,f.fin_year_code,date_format(f.fin_year_from,'%Y-%m-%d')as fin_year_from,date_format(f.fin_year_to,'%Y-%m-%d')as fin_year_to,f.fin_status,f.fin_year_code,date_format(f.created_on,'%Y-%m-%d')as created_on

        FROM ret_financial_year f

        where f.fin_status=1");

        return $sql->row_array();

    }



    function get_finance_entry_records($id)

    {

        $sql = $this->db->query("SELECT f.fin_id,f.fin_year_name as fin_code,date_format(f.fin_year_from,'%Y-%m-%d')as fin_year_from,date_format(f.fin_year_to,'%Y-%m-%d')as fin_year_to,f.fin_status,f.fin_year_code

        FROM ret_financial_year as f

        WHERE fin_id='".$id."'");

        return $sql->result_array()[0];

    }



	public function update_financialData($data,$id,$id_field,$table)

    {

	    $edit_flag = 0;

	    $this->db->where($id_field,$id);

		$edit_flag = $this->db->update($table,$data);

		return ($edit_flag==1?$id:0);

	}



	public function update_financialstatus($insId)

    {

	   $status = $this->db->query("UPDATE ret_financial_year SET fin_status='0' WHERE fin_id<".$insId);

	   return $status;

	}



    public function setFinancialYearStatus()

    {

	   $status = $this->db->query("UPDATE ret_financial_year SET fin_status='0' ");

	   return $status;

	}







	/**

	*	Master :: Financial Year -- Ends

	*/



	//Old Metal Rate

	public function get_old_metal_rate($id_metal,$id_branch)

	{

			$data=$this->db->query("select m.metal,o.id_old_metal_rate,m.metal,m.metal_code,o.id_metal,o.rate,date_format(o.created_on,'%d-%m-%Y')as created_on,p.purity,o.id_purity

								   from ret_old_metal_rate o

								   left join metal m on m.id_metal=o.id_metal

								   left join ret_purity p on p.id_purity=o.id_purity

								  where o.status=1 ".($id_metal!='' ? " and o.id_metal=".$id_metal."" :'')."

								  ".($id_branch!='' ? " and o.id_branch=".$id_branch."":'')." order by o.id_old_metal_rate desc");

			// /print_r($this->db->last_query());exit;

			return $data->result_array();

	}

	//Old Metal Rate



	function weight_details($id_product,$id_design,$id_sub_design)

	{

	    $weights=$this->db->query("SELECT w.value,w.id_weight,w.from_weight,w.to_weight,w.id_product,p.product_name,

	                w.id_design,concat(w.value,u.uom_name) as weight_range,w.id_weight

                	 from ret_weight w

                	 LEFT join ret_product_master p on p.pro_id=w.id_product

                	 LEFT JOIN ret_uom u on u.uom_id=w.id_uom

                	 where w.id_product=".$id_product." ".($id_design!='' ? " and w.id_design=".$id_design."" :'')."

                	 ".($id_sub_design!='' ? " and w.id_sub_design=".$id_sub_design."" :'')."

                	 ");

                	 //print_r($this->db->last_query());exit;

   	    return $weights->result_array();

	}



  function get_weight($id)

   {

        $weights=$this->db->query("SELECT w.id_uom,w.id_weight,w.value as value,

		w.from_weight,w.to_weight,w.id_product,IFNULL(w.id_design,'') as id_design,

        p.product_name,IFNULL(d.design_name,'-') as design_name,w.value as weight_value, weight_description,

        IFNULL(w.id_sub_design,'') as id_sub_design,IFNULL(sd.sub_design_name,'-') as sub_design_name

        from ret_weight w

        LEFT join ret_product_master p on p.pro_id=w.id_product

        LEFT join ret_design_master d on d.design_no=w.id_design

        LEFT join ret_sub_design_master sd on sd.id_sub_design=w.id_sub_design

        LEFT JOIN ret_uom u on u.uom_id=w.id_uom

        where w.id_weight=".$id."");

   	    return $weights->result_array();

   }

   function ajax_get_weights($id_product,$id_design,$id_sub_design)

   {

     $wt_based_on=$this->get_product_details($id_product);

     if($wt_based_on==1)

     {

          $weights=$this->db->query("SELECT concat(w.value,u.uom_name) as value,w.id_weight,w.from_weight,w.to_weight,w.id_product,p.product_name,

                    IFNULL(d.design_name,'-') as design_name, ifnull(weight_description, '-') as weight_description,IFNULL(sd.sub_design_name,'-') as sub_design_name,

						IFNULL(ep.firstname,'')as emp_created,IFNULL(emp.firstname,'')as emp_updated,IFNULL(Date_Format(w.created_on,'%d-%m-%Y'),'')as created_date,

						IFNULL(Date_Format(w.updated_on,'%d-%m-%Y'),'') as updated_date

                	 from ret_weight w

					 left join employee ep on ep.id_employee = w.created_by

					 left join employee emp on emp.id_employee = w.updated_by

                	 LEFT join ret_product_master p on p.pro_id=w.id_product

                	 LEFT join ret_design_master d on d.design_no=w.id_design

                	 LEFT join ret_sub_design_master sd on sd.id_sub_design=w.id_sub_design

                	 LEFT JOIN ret_uom u on u.uom_id=w.id_uom

                	 where w.id_weight is not null ".($id_product!='' ? " and w.id_product=".$id_product."" :'')."

                	 ".($id_design!='' ? " and w.id_design=".$id_design."" :'')."

                	 ");

     }else{

          $weights=$this->db->query("SELECT w.value as value,w.id_weight,w.from_weight,w.to_weight,w.id_product,p.product_name,

                     IFNULL(d.design_name,'-') as design_name, ifnull(weight_description, '-') as weight_description,

                     IFNULL(sd.sub_design_name,'-') as sub_design_name,	IFNULL(ep.firstname,'')as emp_created,IFNULL(emp.firstname,'')as emp_updated,IFNULL(Date_Format(w.created_on,'%d-%m-%Y'),'')as created_date,

                     IFNULL(Date_Format(w.updated_on,'%d-%m-%Y'),'') as updated_date


                	 from ret_weight w

					 left join employee ep on ep.id_employee = w.created_by

                      left join employee emp on emp.id_employee = w.updated_by

                	 LEFT join ret_product_master p on p.pro_id=w.id_product

                	 LEFT join ret_design_master d on d.design_no=w.id_design

                	 LEFT join ret_sub_design_master sd on sd.id_sub_design=w.id_sub_design

                	 LEFT JOIN ret_uom u on u.uom_id=w.id_uom

                	 where w.id_weight is not null

						".($id_product!='' ? " and w.id_product=".$id_product."" :'')."

						".($id_design!='' ? " and w.id_design=".$id_design."" :'')."

						".($id_sub_design!='' ? " and w.id_sub_design=".$id_sub_design."" :'')."

                	 ");

     }



    //print_r($this->db->last_query());exit;

   	 return $weights->result_array();

   }



   function get_product_details($id_product)

   {

       $sql = "SELECT p.weight_range_based from ret_product_master p where p.product_status=1 ".($id_product!='' ? " and p.pro_id=".$id_product."" :'')." ";

		return $this->db->query($sql)->row()->weight_range_based;

   }




   function checkReorderItemExist($id_branch,$addData,$id_sub_design,$set)

   {

       $sql=$this->db->query("SELECT * FROM `ret_reorder_settings`

	   WHERE id_branch=".$id_branch."

       AND id_section=".$set['id_section']."

	   ".($set['weight_range']!='' ? " and id_wt_range=".$set['weight_range']."" :'')."

	   AND max_pcs=".$set['max_pcs']."

	   AND min_pcs=".$set['min_pcs']."

	   ".($set['id_size']!='' ? " and size=".$set['id_size']."" :'')."

	   AND id_product=".$addData['id_product']."

	   AND id_design=".$addData['id_design']."

       AND id_sub_design=".$id_sub_design." ");

    //    print_r($this->db->last_query());exit;

       if($sql->num_rows()>0)

       {

           	return true;

       }else

       {

           	return false;

       }

   }


//    function checkReorderItemExist($id_branch,$addData,$id_sub_design,$weight_range,$size)

//    {

//        $sql=$this->db->query("SELECT * FROM `ret_reorder_settings` WHERE id_branch=".$id_branch." AND id_product=".$addData['id_product']." AND id_design=".$addData['id_design']."

//        AND id_sub_design=".$id_sub_design." AND id_wt_range=".$weight_range." ".($size!='' ? " and size=".$size."" :'')."");

//        //print_r($this->db->last_query());exit;

//        if($sql->num_rows()>0)

//        {

//            	return 0;

//        }else

//        {

//            	return 1;

//        }

//    }



   function checkReorderSettingsUpdateItemExist($id_reorder_settings,$addData)

   {

       $sql=$this->db->query("SELECT * FROM `ret_reorder_settings` WHERE id_reorder_settings!=".$id_reorder_settings."

       and id_branch=".$addData['id_branch']." AND id_product=".$addData['id_product']." AND id_design=".$addData['id_design']."

       AND id_sub_design=".$addData['id_sub_design']." AND id_wt_range=".$addData['id_wt_range']."

       ".($addData['size']!='' ? " and size=".$addData['size']."" :'')."");

       //print_r($this->db->last_query());exit;

       if($sql->num_rows()>0)

       {

           	return 0;

       }else

       {

           	return 1;

       }

   }



   function ajax_getreorder_settings($id,$id_product,$id_design,$id_wt_range,$ref_no)

   {

   		$data=$this->db->query("SELECT s.id_reorder_settings,s.id_branch,s.id_product,s.id_design,concat(ret_s.value,' ',ret_s.name) as size,

		s.id_wt_range as wt_range,s.min_pcs,s.max_pcs,b.name as branch_name,s.ref_no,

   		d.design_name,p.product_name,concat(w.value,m.uom_name) as wt_name,ret_s.id_size,w.id_weight,

   		s.id_sub_design,sub.sub_design_name,s.id_section

   			from ret_reorder_settings s

   			LEFT join branch b on b.id_branch=s.id_branch

   			LEFT join ret_product_master p on p.pro_id=s.id_product

   			LEFT join ret_design_master d on d.design_no=s.id_design

   			LEFT join ret_sub_design_master sub on sub.id_sub_design=s.id_sub_design

   			LEFT join ret_weight w on w.id_weight=s.id_wt_range

   			LEFT JOIN ret_size ret_s on ret_s.id_size = s.size

   			LEFT JOIN ret_uom m on m.uom_id=w.id_uom

   			where s.id_reorder_settings is not null

			".($ref_no=='' && $id!='' ? " and s.id_reorder_settings=".$id."" :'')."

			".($ref_no!='' ? " and s.ref_no = ".$ref_no."" :'')."

   			".($id_product!='' ? " and s.id_product=".$id_product."" :'')."

   			".($id_design!='' ? " and s.id_design=".$id_design."" :'')."

   			".($id_wt_range!='' ? " and s.id_wt_range=".$id_wt_range."" :'')."

   			ORDER by s.id_branch");

   		//print_r($this->db->last_query());exit;

   		if($id!='')

   		{

   			return $data->result_array();

   		}else

   		{

   			return $data->row_array();

   		}

   }


 function get_ref_no($id)
 {

	$data = $this->db->query("select  s.ref_no from ret_reorder_settings s where s.id_reorder_settings=".$id);

	return $data->row()->ref_no;

 }





   function ajax_getreorderSett($data)

   {

   		 $data=$this->db->query("SELECT s.id_reorder_settings,s.id_branch,s.id_product,s.id_design,concat(ret_s.value,' ',ret_s.name) as size,s.id_wt_range as wt_range,s.min_pcs,s.max_pcs,b.name as branch_name,

   		   d.design_name,p.product_name,concat(w.value,IFNULL(m.uom_name,'')) as wt_name,ret_s.id_size,

   		   s.id_sub_design,sub.sub_design_name,r.section_name,s.ref_no,w.from_weight,w.to_weight

   			from ret_reorder_settings s

   			LEFT join branch b on b.id_branch=s.id_branch

   			LEFT join ret_product_master p on p.pro_id=s.id_product

   			LEFT join ret_design_master d on d.design_no=s.id_design

   			LEFT join ret_sub_design_master sub on sub.id_sub_design=s.id_sub_design

   			LEFT join ret_weight w on w.id_weight=s.id_wt_range

   			LEFT JOIN ret_size ret_s on ret_s.id_size=s.size

			left join ret_section r on r.id_section=s.id_section


   			LEFT JOIN ret_uom m on m.uom_id=w.id_uom

   			where s.id_reorder_settings is not null

   			".($data['id_branch']!='' ? " and s.id_branch=".$data['id_branch']."" :'')."

   			".($data['id_product']!='' ? " and s.id_product=".$data['id_product']."" :'')."

   			".($data['id_design']!='' ? " and s.id_design=".$data['id_design']."" :'')."

   			".($data['id_sub_design']!='' ? " and s.id_sub_design=".$data['id_sub_design']."" :'')."

   			".($data['id_wt_range']!='' ? " and s.id_wt_range=".$data['id_wt_range']."" :'')."

   			".($data['id_size']!='' ? " and s.size=".$data['id_size']."" :'')."

			".($data['id_section']!='' ? " and s.id_section=".$data['id_section']."" :'')."


   			ORDER by s.id_branch");

   		//print_r($this->db->last_query());exit;

   		if($id!='')

   		{

   			return $data->row_array();

   		}else

   		{

   			return $data->result_array();

   		}

   }





   /*Delivery functions here */



   	 function ajax_getDelivery()

    {

		$id_sale_delivery = $this->db->query("SELECT * FROM ret_sale_delivery ORDER BY id_sale_delivery desc");

		return $id_sale_delivery->result_array();

	}



	 function get_delivery($id_sale_delivery)

    {

		$id_sale_delivery = $this->db->query("select * from ret_sale_delivery where id_sale_delivery=".$id_sale_delivery);

		return $id_sale_delivery->row_array();

	}



    public function insert_delivery($data)

    {

    	$insert_flag = 0;

		$insert_flag = $this->db->insert('ret_sale_delivery',$data);

		return ($insert_flag == 1 ? $this->db->insert_id(): 0);

	}



	public function update_delivery($data,$id)

    {

    	$edit_flag = 0;

    	$this->db->where('id_sale_delivery',$id);

		$edit_flag = $this->db->update('ret_sale_delivery',$data);

		return ($edit_flag==1?$id:0);

	}



	public function update_location()

	{

	    $sql="update ret_sale_delivery set is_default=0";

	    $data=$this->db->query($sql);

	    return true;

	}



	public function delete_delivery($id)

    {

        $this->db->where('id_sale_delivery', $id);

        $status= $this->db->delete('ret_sale_delivery');

		return $status;

	}

	/*End of delivery functions*/



	/*Delivery functions here */



   	 function get_Activesize($id_product)

   	 {

   	  	$weights=$this->db->query("SELECT concat(s.value,' ',s.name) as name,id_size from ret_size s where active=1".($id_product!='' ? " and id_product=".$id_product."" :'')." ");

       	 return $weights->result_array();


   	 }



   	 /*Size functions here */



   	 function ajax_getsize()

    {

		$size = $this->db->query("SELECT s.id_size,s.name,s.value,s.active,s.id_product,p.product_name

		FROM ret_size s

		LEFT JOIN ret_product_master p on p.pro_id=s.id_product

		ORDER BY id_size desc");

		//print_r($this->db->last_query());exit;

		return $size->result_array();

	}



	 function get_size($id_size)

    {

		$id_size = $this->db->query("select * from ret_size where id_size=".$id_size);

		return $id_size->row_array();

	}
	function check_size($value,$name,$id_product)

    {

		$id_size = $this->db->query("select * from ret_size s

		where s.value is not null

		".($value!='' ? " and s.value =".$value."" :'')."

		".($name!='' ? " and s.name ='".$name."'" :'')."

		".($id_product!='' ? " and s.id_product =".$id_product."" :'')."

		");

		// print_r($this->db->last_query());exit;

		return $id_size->num_rows();

	}


    public function insert_size($data)

    {

    	$insert_flag = 0;

		$insert_flag = $this->db->insert('ret_size',$data);

		return ($insert_flag == 1 ? $this->db->insert_id(): 0);

	}



	public function update_size($data,$id)

    {

    	$edit_flag = 0;

    	$this->db->where('id_size',$id);

		$edit_flag = $this->db->update('ret_size',$data);

		return ($edit_flag==1?$id:0);

	}





	public function delete_size($id)

    {

        $this->db->where('id_size', $id);

        $status= $this->db->delete('ret_size');

		return $status;

	}





	/*End of size functions*/



        //Old Metal Type



	function ajax_getOldMetalType()

	{

		$sql=$this->db->query("SELECT m.metal,t.id_metal_type,t.metal_type

		from ret_old_metal_type t

		LEFT JOIN metal m on m.id_metal=t.id_metal");

		return $sql->result_array();



	}



	function get_old_metal_type($id_metal_type)

	{

	    $sql=$this->db->query("SELECT * from ret_old_metal_type where id_metal_type=".$id_metal_type);

	    return $sql->row_array();

	}



    //Old Metal Type





	//Old Metal Category



	function ajax_getOldMetalCategory()

	{

		$sql=$this->db->query("SELECT mc.id_old_metal_cat, mc.id_metal, mc.id_old_metal_type, mc.old_metal_cat, mc.old_metal_perc, t.id_metal_type,t.metal_type,mc.old_metal_discount, mc.touch_from, mc.touch_to, m.metal,mc.is_active

		FROM ret_old_metal_category as mc

		LEFT JOIN ret_old_metal_type t on t.id_metal_type = mc.id_old_metal_type

		LEFT JOIN metal m on m.id_metal=t.id_metal");

		return $sql->result_array();

	}



	function get_old_metal_category($id_old_metal_cat)

	{

		$sql=$this->db->query("SELECT * from ret_old_metal_category where id_old_metal_cat =".$id_old_metal_cat);

		return $sql->row_array();

	}





	//Old Metal Category



    //OLd Metal Rate

    public function ajax_GetOldMetalRate()

	{

        $data=$this->db->query("select m.metal,o.id_old_metal_rate,m.metal,m.metal_code,o.id_metal,o.rate,date_format(o.created_on,'%d-%m-%Y')as date_add,

        e.firstname as emp_created,if(o.status=1,'Active','Inactive') as status

        from ret_old_metal_rate o

        left join metal m on m.id_metal=o.id_metal

        left join employee e on e.id_employee=o.created_by

        order by o.id_old_metal_rate desc");

			return $data->result_array();

	}



	function getOldMetalRate($id_old_metal_rate)

	{

	    $sql=$this->db->query("SELECT * from ret_old_metal_rate where id_old_metal_rate=".$id_old_metal_rate."");

	    return $sql->row_array();

	}

    //OLd Metal Rate







    //Section Master



       	 /*Size functions here */



   	function ajax_getSection()

    {

		$size = $this->db->query("SELECT * FROM `ret_section` ORDER by id_section DESC");

		return $size->result_array();

	}



	 function get_sections($id_section)

    {

		$id_size = $this->db->query("SELECT * FROM `ret_section` where id_section=".$id_section);

		return $id_size->row_array();

	}







	/*End of size functions*/



    //Section Master





    //Section Master

    function get_section()

    {

        $sql=$this->db->query("select id_section,section_name FROM ret_section where STATUS=1");

        return $sql->result_array();

    }



    //Section Master

	//Pay Device Master


   	function ajax_getdev()

    {

		$size = $this->db->query("SELECT * FROM `ret_bill_pay_device` ORDER by id_device DESC");

		return $size->result_array();

	}



	 function get_dev($id_device)

    {

		$id_size = $this->db->query("SELECT * FROM `ret_bill_pay_device` where id_device=".$id_device);

		return $id_size->row_array();

	}

    function get_paydev()

    {

        $sql=$this->db->query("select id_section,section_name FROM ret_bill_pay_device where STATUS=1");

        return $sql->result_array();

    }

	function checkDeviceName($device_name)

	{
		$sql=$this->db->query("SELECT * from  ret_bill_pay_device where device_name	 = '".$device_name."'");

		return $sql->num_rows();
	}

    //Pay Device Master

	function check_weight_range($from_weight,$to_weight,$value,$id_product,$desc)
	{

		$sql=$this->db->query("SELECT  w.id_weight,w.from_weight,  w.to_weight ,w.value,w.id_product, w.weight_description from  ret_weight w

		 where w.from_weight and  w.from_weight is not null

		 ".($id_product!='' ? " and w.id_product =".$id_product."" :'')."

		 AND ((w.from_weight <=".$from_weight." and w.to_weight >= ".$from_weight.") OR (w.from_weight <= ".$to_weight." and w.to_weight >= ".$to_weight."))");

		//print_r($this->db->last_query());exit;
		//  ".($value!='' ? " and w.value =".$value."" :'')."


		return $sql->num_rows();

		//  ".($desc!='' ? " and w.weight_description = '".$desc."' " :'')."

	}
	//Account Head Master End
   	function ajax_getacc()

    {

		$size = $this->db->query("SELECT * FROM `ret_account_head` ORDER by id_acc_head DESC");

		return $size->result_array();

	}



	 function get_acc($id_acc_head)

    {

		$id_size = $this->db->query("SELECT * FROM `ret_account_head` where id_acc_head=".$id_acc_head);

		return $id_size->row_array();

	}

	function ajax_get_crdr_ledger()
	{
		$query = $this->db->query("SELECT * FROM `ret_crdr_ledger` ORDER by id_crdr_ledger DESC");
		return $query->result_array();
	}

	function get_crdr_ledger($id_crdr_ledger)
	{
		$query = $this->db->query("SELECT * FROM `ret_crdr_ledger` where id_crdr_ledger=".$id_crdr_ledger);
		return $query->row_array();
	}

    function get_acched()

    {

        $sql=$this->db->query("select id_section,section_name FROM ret_account_head where STATUS=1");

        return $sql->result_array();

    }

	/* function checkDeviceName($device_name)

	{
		$sql=$this->db->query("SELECT * from  ret_account_head where device_name	 = '".$device_name."'");

		return $sql->num_rows();
	} */

    //Account Head Master End

	//Breakeven Logs Master Start
   		function ajax_getrbl($data)

    {

		$size = $this->db->query("SELECT b.*,br.name branch_name FROM `ret_breakeven_logs` b

		left join branch br on br.id_branch=b.brevn_log_branchid

		where

		".($data['from_date']!='' && $data['to_date']!='' ? ' date(b.brevn_log_date) BETWEEN "'.date('Y-m-d',strtotime($data['from_date'])).'" AND "'.date('Y-m-d',strtotime($data['to_date'])).'" ' :'')."

		".($data['id_branch']!='' && $data['id_branch']!=0 ? " and b.brevn_log_branchid=".$data['id_branch']."" :'')."



		ORDER by brevn_log_id ");

		//print_r($this->db->last_query());exit;

		return $size->result_array();

	}



	 function get_rbl($brevn_log_id)

    {

		$id_size = $this->db->query("SELECT * FROM `ret_breakeven_logs` where brevn_log_id=".$brevn_log_id);

		return $id_size->row_array();

	}

    function get_accrbl()

    {

        $sql=$this->db->query("select id_section,section_name FROM ret_breakeven_logs where STATUS=1");

        return $sql->result_array();

    }

    //Breakeven Logs Master End

	// Stack Type Starts

		function ajax_getsta()

		{

			$size = $this->db->query("SELECT * FROM `ret_stock_issue_types` ORDER by id_stock_issue_type DESC");

			return $size->result_array();

		}

		 function get_stock($id_stock_issue_type)

		{

			$id_size = $this->db->query("SELECT * FROM `ret_stock_issue_types` where id_stock_issue_type=".$id_stock_issue_type);

			return $id_size->row_array();

		}

		 function get_stak($id_stock_issue_type)

		{

			$id_size = $this->db->query("SELECT * FROM `ret_stock_issue_types` where id_stock_issue_type=".$id_stock_issue_type);

			return $id_size->row_array();

		}

	// Stack Type End

    //feedback master

	function ajax_getFeedback()

	{

		$feedback=$this->db->query("SELECT * FROM `customer_feedback_master` ORDER by id_feedback DESC");

		return $feedback->result_array();

	}



	function get_feedback($id_feedback)

	{

		$id=$this->db->query("SELECT * FROM `customer_feedback_master` where id_feedback=".$id_feedback);



		return $id->row_array();

	}

	 //feedback master



	function check_transaction($tableName, $fieldName, $fieldValue)

	{

		$tableName = trim($tableName);

		$fieldName = trim($fieldName);

		$fieldValue = trim($fieldValue);

		if($tableName != "" && $fieldName != "" && $fieldValue != "")

		{

			$sql = $this->db->query("SELECT * FROM ".$tableName." WHERE ".$fieldName." = '".$fieldValue."'");

			if($sql->num_rows() > 0){

				return 0;

			}else{

				return 1;

			}

		}

		else

		{

			return 0;

		}

	}



	// Charges Functions :: STARTS

	public function get_charges_list()

    {

    	$sql="SELECT id_charge, code_charge, name_charge, description_charge, charge_tax, value_charge, created_on, created_by from ret_charges";

		$users=$this->db->query($sql);

		return $users->result_array();

	}

	public function get_charges_list_date($from_date, $to_date)

    {

		      date_default_timezone_set('Asia/Kolkata');

			  $first='00:00:00';

			  $last='23:59:59';

    	      $sql="SELECT id_charge, code_charge, name_charge, description_charge, value_charge, charge_tax,  created_on, created_by from ret_charges

		      where

		      created_on BETWEEN '".date('Y-m-d H:i:s',strtotime($to_date.$first))."' AND '".date('Y-m-d H:i:s',strtotime($from_date.$last))."'";

			  $users=$this->db->query($sql);

			  return $users->result_array();

	}

	public function get_charges_list_edit($id_charge)

    {

    	$sql="SELECT id_charge, code_charge, name_charge, description_charge, value_charge, tag_display, charge_tax,  created_on, created_by from ret_charges where id_charge = $id_charge";

			   	 $users=$this->db->query($sql);

				 return $users->result_array();

	}

	// Charges Functions :: ENDS





	 //ret sub design master

	public function get_empty_subproduct()

    {

		$data=array(

		'id_sub_design'=> NULL,

		'sub_design_name'=> NULL,

		'status'=> 1,

	    );



		return $data;

	}



	  function get_sub_design($id_sub_design)

    {

		$id_sub_design = $this->db->query("SELECT * FROM `ret_sub_design_master` where id_sub_design=".$id_sub_design);

		return $id_sub_design->row_array();

	}



	function ajax_getSubDesign($data)

    {

		$sql = $this->db->query("SELECT d.sub_design_name,d.sub_design_code,d.status,d.id_sub_design

        FROM ret_sub_design_master d

        WHERE d.id_sub_design is NOT NULL

        ");

       // print_r($this->db->last_query());exit;

		return $sql->result_array();

	}



	function ret_sub_design_mapping_images($id_sub_design_mapping)

	{

	    $sql=$this->db->query("SELECT * FROM `ret_sub_design_mapping_images` WHERE id_sub_design_mapping=".$id_sub_design_mapping."");

	    return $sql->result_array();

	}



	function get_karigar_mapping_products($id_sub_design_mapping)

	{

	    $sql=$this->db->query("SELECT k.firstname as karigar_name,IFNULL(k.contactno1,'') as mobile,IFNULL(k.code_karigar,'') as code

        FROM ret_karigar_products m

        LEFT JOIN ret_karigar k ON k.id_karigar=m.id_karigar

        WHERE m.id_sub_design_mapping=".$id_sub_design_mapping."");

        //print_r($this->db->last_query());exit;

        return $sql->result_array();

	}



    function get_sub_design_mapping($id_sub_design_mapping)

	{

	    $sql=$this->db->query("SELECT * FROM `ret_sub_design_mapping` WHERE id_sub_design_mapping=".$id_sub_design_mapping."");

	    return $sql->row_array();

	}













	function genSubDesignShortCode()

	{

		$sql = $this->db->query("SELECT max(CAST(sub_design_code AS UNSIGNED)) as sub_design_code  from ret_sub_design_master");

		$lastCode = $sql->row()->sub_design_code;

		if($sql->num_rows() == 0){

			return 1;

		}else{

			return $lastCode+1;

		}

	}



	 function getActiveSubDesigns()

	 {

	       $data = $this->db->query("SELECT id_sub_design,sub_design_name FROM `ret_sub_design_master` WHERE status = 1");

		   return $data->result_array();

	 }



	 function get_ActiveDesign()

	 {

	     $sql=$this->db->query("SELECT * FROM ret_design_master WHERE design_status=1");

	     return $sql->result_array();

	 }





	function check_subdesign_maping($id_sub_design,$design_no)

	{

		$sql=$this->db->query("SELECT * FROM `ret_sub_design_mapping` WHERE design_no=".$design_no." AND id_sub_design=".$id_sub_design."");

		if($sql->num_rows() == 0){

			return TRUE;

		}else{

			return FALSE;

		}

	}





    function check_products_design_maping($id_product,$design_no)

	{

		$sql=$this->db->query("SELECT * FROM `ret_product_mapping` WHERE pro_id=".$id_product." AND id_design=".$design_no." ");

		if($sql->num_rows() == 0){

			return TRUE;

		}else{

			return FALSE;

		}

	}



	function check_sub_design_mapping($id_product,$design_no,$id_sub_design)

	{

		$sql=$this->db->query("SELECT * FROM `ret_sub_design_mapping` WHERE id_product=".$id_product." AND id_design=".$design_no." and id_sub_design=".$id_sub_design."");

		if($sql->num_rows() == 0){

			return TRUE;

		}else{

			return FALSE;

		}

	}



	function get_ProductDesign($data)

	{

	    $sql=$this->db->query("SELECT des.design_no,des.design_name

        FROM ret_product_mapping p

        LEFT JOIN ret_design_master des ON des.design_no=p.id_design

        where p.id_design IS NOT NULL

        ".($data['id_product']!='' ? " and p.pro_id=".$data['id_product']."" :'')."

        group by des.design_no");

        return $sql->result_array();

	}



	function get_active_design_products($data)

	{

	    $sql=$this->db->query("

	    SELECT des.design_no,des.design_name,pro.pro_id

        FROM ret_product_mapping p

        LEFT JOIN ret_product_master pro ON pro.pro_id=p.pro_id

        LEFT JOIN ret_design_master des ON des.design_no=p.id_design

        where des.design_no is not null ".($data['id_product']!='' ? " and p.pro_id=".$data['id_product']."" :'')."

        group by p.pro_id, des.design_no");

        //print_r($this->db->last_query());exit;

        return $sql->result_array();

	}





	function get_ActiveSubDesigns($data)

	{

	    $sql=$this->db->query("SELECT s.id_product, s.id_design, d.id_sub_design,d.sub_design_name

        FROM ret_sub_design_mapping s

        LEFT JOIN ret_sub_design_master d ON d.id_sub_design=s.id_sub_design

        WHERE s.id_sub_design is NOT NULL

         ".($data['design_no']!='' ? " and s.id_design=".$data['design_no']."" :'')."

         ".($data['id_product']!='' ? " and s.id_product=".$data['id_product']."" :'')."

         group by  d.id_sub_design");

        return $sql->result_array();

	}



	function ajax_subDesignMapingDetails($data)

	{

	    $returnData=array();

	    $sql=$this->db->query("SELECT m.id_sub_design_mapping,d.sub_design_name,p.product_name,des.design_name,img.image_name AS default_image 
		FROM ret_sub_design_mapping m
		LEFT JOIN ret_product_master p 
    	ON p.pro_id = m.id_product
		LEFT JOIN ret_design_master des 
    	ON des.design_no = m.id_design
		LEFT JOIN ret_sub_design_master d 
    	ON d.id_sub_design = m.id_sub_design
		LEFT JOIN ret_sub_design_mapping_images img 
    	ON img.id_sub_design_mapping = m.id_sub_design_mapping
   		AND img.is_default = 1
		WHERE m.id_sub_design_mapping IS NOT NULL;


        ".($data['id_product']!='' ? " and m.id_product=".$data['id_product']."" :'')."

        ".($data['id_design']!='' ? " and m.id_design=".$data['id_design']."" :'')."

        ".($data['id_sub_design']!='' ? " and m.id_sub_design=".$data['id_sub_design']."" :'')."

        ");


        $result= $sql->result_array();

        foreach($result as $items)

        {

            $returnData[]=array(

                              'id_sub_design_mapping'=>$items['id_sub_design_mapping'],

                              'default_image'        =>$items['default_image'],

                              'sub_design_name'      =>$items['sub_design_name'],

                              'product_name'         =>$items['product_name'],

                              'design_name'          =>$items['design_name'],

                              'img_details'          =>$this->ret_sub_design_mapping_images($items['id_sub_design_mapping']),

                             // 'karigar_details'      =>$this->get_karigar_mapping_products($items['id_sub_design_mapping']),

                              'karigar_details'      =>[],

                             );

        }

        return $returnData;

	}



	function ajax_ProductMapingDetails($data)

    {

        $sql=$this->db->query("SELECT m.mapping_id,p.product_name,des.design_name

        FROM ret_product_mapping m

        LEFT JOIN ret_product_master p ON p.pro_id=m.pro_id

        LEFT JOIN ret_design_master des ON des.design_no=m.id_design

        where m.mapping_id is NOT NULL

        ".($data['id_product']!='' ? " and m.pro_id=".$data['id_product']."" :'')."

        ".($data['id_design']!='' ? " and m.id_design=".$data['id_design']."" :'')."

        ");

        //print_r($this->db->last_query());exit;

        return $sql->result_array();

    }





    function getSubDesignName($SearchTxt){



		$data = $this->db->query("SELECT s.id_sub_design as value,s.sub_design_name as label

                                  FROM ret_sub_design_master s

			                      WHERE s.sub_design_name like '%".$SearchTxt."%'");

		return $data->result_array();

	}



	function getDesignName($SearchTxt){



		$data = $this->db->query("SELECT des.design_name as label,des.design_no as value

                                  FROM ret_design_master des

			                      WHERE des.design_name like '%".$SearchTxt."%'");

		return $data->result_array();

	}



	function get_DesignSettingsDetails($data)

	{

	    $sql=$this->db->query("SELECT m.id_sub_design_mapping,cat.name as category_name,p.product_name,des.design_name,s.sub_design_name,IFNULL(m.mc_cal_type,'-') as mc_cal_type,IFNULL(m.mc_cal_value,'-') as mc_cal_value,IFNULL(m.wastage_type,'') as wastage_type,IFNULL(m.wastag_value,'-') as wastag_value, IFNULL(ret_attr.total_attr, 0) AS total_attr, IFNULL(ret_wc.total_wc, 0) AS total_wc, IFNULL(mc_min,'-') AS mc_min, IFNULL(mc_max, '-') AS mc_max, IFNULL(wastag_min, '-') AS wastag_min, IFNULL(wastag_max, '-') AS wastag_max, margin_mrp

        FROM ret_sub_design_mapping m

        LEFT JOIN ret_product_master p ON p.pro_id=m.id_product

        LEFT JOIN ret_design_master des ON des.design_no=m.id_design

        LEFT JOIN ret_sub_design_master s ON s.id_sub_design=m.id_sub_design

        LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id

		LEFT JOIN (SELECT COUNT(*) as total_attr, id_sub_design_mapping FROM ret_design_attributes GROUP BY id_sub_design_mapping) AS ret_attr ON ret_attr.id_sub_design_mapping = m.id_sub_design_mapping

		LEFT JOIN (SELECT COUNT(*) as total_wc, id_sub_design_mapping FROM ret_design_weight_range_wc GROUP BY id_sub_design_mapping) AS ret_wc ON ret_wc.id_sub_design_mapping = m.id_sub_design_mapping

        WHERE m.id_sub_design_mapping IS NOT NULL

        ".($data['id_design']!='' ? " and m.id_design=".$data['id_design']."" :'')."

         ".($data['id_product']!='' ? " and m.id_product=".$data['id_product']."" :'')."

         ".($data['id_sub_design']!='' ? " and m.id_sub_design=".$data['id_sub_design']."" :'')."

         ".($data['id_mc_type']!='' && $data['id_mc_type']>0 ? " and m.mc_cal_type=".$data['id_mc_type']."" :'')."

         ".($data['mc_value']!='' ? " and m.mc_cal_value=".$data['mc_value']."" :'')."

         ".($data['wast_per']!='' ? " and m.wastag_value=".$data['wast_per']."" :'')."

        ");

        return $sql->result_array();

	}





	function get_wastage_details($id)

	{

	    $sql=$this->db->query("SELECT * FROM `ret_design_weight_range_wc` WHERE id_sub_design_mapping=".$id."");

	    return $sql->result_array();

	}







	//Sub Design and Design Mapping



	//Attribute



	function ajax_getattribute($from_date,$to_date)

	{

		$data = array();



		$sql = "SELECT attr_id, attr_name, attr_status, created_on, created_by, updated_on, updated_by FROM ret_attribute";



		if($from_date != '') {



			$sql = $sql.( ' where (date(created_on) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');



		}



		$result = $this->db->query($sql);



		$data = $result->result_array();



		return $data;

	}



	function get_empty_attribute()

	{

		$data['attr_name'] = "";

		$data['attr_status'] = 1;



		return $data;

	}



	function getActiveAttribute()

	{

		$data = array();



		$sql = "SELECT attr_id, attr_name, attr_status, created_on, created_by, updated_on, updated_by FROM ret_attribute WHERE attr_status = 1";



		$result = $this->db->query($sql);



		$data = $result->result_array();



		return $data;

	}



	function get_attribute($attr_id)

	{

		$sql = "SELECT  attr_id, attr_name, attr_status, created_on, created_by, updated_on, updated_by FROM ret_attribute WHERE attr_id =".$attr_id;

		$res = $this->db->query($sql);

		return $res->row_array();

	}



	function get_attribute_values($attr_id)

	{

		$sql = "SELECT attr_val_id, attr_id, attr_val FROM ret_attribute_values WHERE attr_id=".$attr_id." ORDER BY attr_val_id ASC";

		$res = $this->db->query($sql);

		return $res->result_array();

	}



	function get_attribute_with_values($attr_id = 0, $status = '')

	{

		$where = "";



		if($status == 0 || $status == 1)

		{

			$where = $where." AND attr_status = ".$status;

		}



		if($attr_id > 0)

		{

			$where = $where." AND attr_id = ".$attr_id;

		}



		$returnData = array();



		$sql = "SELECT attr_id, attr_name, attr_status, created_on, created_by, updated_on, updated_by FROM ret_attribute WHERE 1 ".$where;



		$result = $this->db->query($sql);



		$data = $result->result_array();



		foreach($data as $items)

        {

            $returnData[]=array(

                              'attr_id'		 =>	$items['attr_id'],

                              'attr_name'    =>	$items['attr_name'],

                              'attr_status'  =>	$items['attr_status'],

                              'created_on'   =>	$items['created_on'],

                              'created_by'   =>	$items['created_by'],

							  'updated_on'   =>	$items['updated_on'],

                              'updated_by'   =>	$items['updated_by'],

                              'attr_values'  =>	$this->get_attribute_values($items['attr_id'])

                             );

        }



		return $returnData;

	}



	//Attributes



	//Design Attributes

	function get_design_attr_values($design_id, $attr_id = 0)

	{

		$where = "";

		if($attr_id > 0)

		{

			$where = " AND rda.attr_id = ".$attr_id;

		}

		$sql = "SELECT

					rda.attr_des_id,

					rda.id_sub_design_mapping,

					rda.attr_id,

					rda.attr_val_id,

					ra.attr_name,

					rav.attr_val

				FROM ret_design_attributes AS rda

				LEFT JOIN ret_attribute AS ra ON ra.attr_id = rda.attr_id

				LEFT JOIN ret_attribute_values AS rav ON rav.attr_val_id = rda.attr_val_id

				WHERE id_sub_design_mapping=".$design_id.$where;



		$res = $this->db->query($sql);



		return $res->result_array();

	}



	function get_attributes_from_subdesign($product_id, $design_id, $subdesign_id)

	{

		$sql = "SELECT

					rsdm.id_sub_design_mapping,

					IFNULL(rda.attr_des_id,0) AS attr_des_id,

					IFNULL(rda.attr_id,0) AS attr_id,

					IFNULL(rda.attr_val_id,0) AS attr_val_id,

					ra.attr_name,

					rav.attr_val

				FROM ret_sub_design_mapping AS rsdm

				LEFT JOIN ret_design_attributes AS rda ON rda.id_sub_design_mapping = rsdm.id_sub_design_mapping

				LEFT JOIN ret_attribute AS ra ON ra.attr_id = rda.attr_id

				LEFT JOIN ret_attribute_values AS rav ON rav.attr_val_id = rda.attr_val_id

				WHERE rsdm.id_product = ".$product_id." AND rsdm.id_design = ".$design_id." AND rsdm.id_sub_design = ".$subdesign_id;



		$res = $this->db->query($sql);



		return $res->result_array();

	}



	function get_ActiveProducts($data)

	{

	    $sql=$this->db->query("SELECT p.pro_id,p.product_name, p.sales_mode, p.purchase_mode,p.stock_type,p.tax_type,p.tax_group_id,p.job_work_receipt_tgrp_id,p.purchase_tax_type,p.stone_type,p.cat_id, p.calculation_based_on

        FROM ret_product_master p

        WHERE p.pro_id IS NOT NULL AND p.product_status=1

        ".($data['id_ret_category']!='' ? " and p.cat_id=".$data['id_ret_category']."" :'')."");

        return $sql->result_array();

	}



	function get_MetalCategory($data)
    {
        $multiple_id_metal = implode(' , ', $data['id_metal']);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $data['id_metal'];
		}
		$data = $this->db->query("SELECT id_ret_category, name, description, id_metal, image, tgrp_id, cat_type FROM `ret_category`
		WHERE status = 1
		".($id_metal!='' && $id_metal !='0' ? " and id_metal in (".$id_metal.") " :'' )."
		".($data['id_cat_type']!='' ? " and cat_type =".$data['id_cat_type']."" :'')."
		");
		return $data->result_array();
	}



	function get_NonTagProducts($data)

    {

		$data = $this->db->query("SELECT pro_id, product_short_code, product_name, cat.name as categoryname,pro.cat_id,cat.tgrp_id

		                        FROM `ret_product_master` as pro

		                        LEFT JOIN ret_category as cat ON cat.id_ret_category = pro.cat_id

		                        WHERE stock_type = 2");

		return $data->result_array();

	}



	/** Product Charges */



	function get_product_charges($prod_id)

	{

		$sql = "SELECT



					rpc.prod_charge_id,



					rpc.prod_id,



					rpc.charge_id,



					rpc.charge_value,



					rc.name_charge



				FROM ret_product_charges AS rpc



				LEFT JOIN ret_charges AS rc ON rc.id_charge = rpc.charge_id



				WHERE rpc.prod_id = ".$prod_id;



		$sql = $this->db->query($sql);



		return $sql->result_array();



	}



	/** Product Charges */







	//Karigar wise wastage settings

	function get_karigar_wise_wastage($id_kaigar)

	{

	    $reutnrData = [];

	    $sql=$this->db->query("SELECT * FROM `ret_karikar_items_wastage` where active=1 and status=1 ".($id_kaigar!='' ? " and id_karikar=".$id_kaigar."" :'')."");

	    $result = $sql->result_array();

	    foreach($result as $val)

	    {

	        $val['charges_details'] = $this->get_karigar_charges($val['id_karikar_wast']);

	        $reutnrData[] = $val;

	    }

	    return $reutnrData;

	}

	function karigar_waiting_for_app_products($id_kaigar)

	{

	    $sql=$this->db->query("SELECT * FROM `ret_karikar_items_wastage` where status=0 ".($id_kaigar!='' ? " and id_karikar=".$id_kaigar."" :'')." ");

	    return $sql->result_array();

	}

	function get_karigar_hold_wastages($id_kaigar)

	{

		$sql=$this->db->query("SELECT * FROM `ret_karikar_items_wastage` where status=3 ".($id_kaigar!='' ? " and id_karikar=".$id_kaigar."" :'')." ");

	    return $sql->result_array();

	}

	//Karigar wise wastage settings





	//Karigar wise stones

	function get_karigar_wise_stones()

	{

	    $sql=$this->db->query("SELECT * FROM `ret_karigar_stones` where active=1 and status=1");

	    return $sql->result_array();

	}

	//Karigar wise stones



	//Karigar wise charges

	function get_karigar_wise_charges()

	{

	    $sql=$this->db->query("SELECT * FROM `ret_karigar_charges` where active=1 and status=1");

	    return $sql->result_array();

	}

	//Karigar wise charges





	function get_karigar_product_mapping($data)

	{

	    $sql=$this->db->query("SELECT p.id_karigar_product,k.firstname as karigar_name,pro.product_name,des.design_name,p.status

        FROM ret_karigar_products p

        LEFT JOIN ret_karigar k ON k.id_karigar=p.id_karigar

        LEFT JOIN ret_product_master pro ON pro.pro_id=p.id_product

        LEFT JOIN ret_design_master des ON des.design_no=p.id_design

        where p.id_karigar_product IS NOT NULL

        ".($data['id_product']!='' ? " and p.id_product=".$data['id_product']."" :'')."

        ".($data['id_design']!='' ? " and p.id_design=".$data['id_design']."" :'')."

        ".($data['id_karigar']!='' ? " and p.id_karigar=".$data['id_karigar']."" :'')."

        ");

        return $sql->result_array();

	}



	function check_design_products_maping($id_product,$design_no,$id_karigar)

	{

		$sql=$this->db->query("SELECT * FROM `ret_karigar_products` WHERE id_product=".$id_product." AND id_design=".$design_no." and id_karigar=".$id_karigar."");

		if($sql->num_rows() == 0){

			return TRUE;

		}else{

			return FALSE;

		}

	}





	function get_karigar_products($id)

	{

	    $sql1=$this->db->query("SELECT * FROM `ret_karigar_products` WHERE id_karigar_product=".$id."");

	    if($sql1->num_rows() > 0)

	    {

	        $result=$sql1->row_array();

	        $status = $this->db->query("UPDATE `ret_karigar_products` SET status=0 WHERE id_product=".$result['id_product']." AND id_design=".$result['id_design']."");

	        return $status;

	    }else

	    {

	        return TRUE;

	    }

	}





	//Web Devices Functions



	function get_DeviceList()

	{

	    $sql=$this->db->query("SELECT w.id_device,w.device_name,w.token_id,w.status,w.browser,br.name as branch_name

        FROM web_registered_devices w

        LEFT JOIN branch br ON br.id_branch=w.id_branch");

	    return $sql->result_array();

	}



	function get_ActiveCounter($data)

	{
		$id_floor_cond = "";
		if(isset($data['id_floor']) && $data['id_floor'] != '') {
			if(is_array($data['id_floor'])) {
			    $floors = array_filter($data['id_floor'], function($v) { return $v != 0 && $v != ''; });
			    if(!empty($floors)) {
				    $id_floor_cond = " and c.floor_id IN (".implode(',', $floors).")";
			    }
			} else {
			    if($data['id_floor'] != 0) {
				    $id_floor_cond = " and c.floor_id=".$data['id_floor'];
			    }
			}
		}

	    $sql=$this->db->query("SELECT c.counter_id,c.counter_name

        FROM ret_branch_floor_counter c

        LEFT JOIN ret_branch_floor f ON f.floor_id=c.floor_id

        WHERE c.counter_status=1 ".($data['id_branch']!='' ? " and f.branch_id=".$data['id_branch']."" :'')."

 		".$id_floor_cond."
        ");

        return $sql->result_array();

	}



	function get_ActiveBranchFloor($data)

	{

	    $sql=$this->db->query("SELECT * FROM ret_branch_floor WHERE floor_status=1 ".($data['id_branch']!='' ? " and branch_id=".$data['id_branch']."" :'')."");

	    return $sql->result_array();

	}



	function web_device_settingDB($type="",$id="",$data="")

	{

		switch($type)

		{

			case 'get':

				  if($id!=NULL)

				  {

				    $sql = "SELECT *  FROM web_registered_devices d ".($id!=null? 'Where d.id_device='.$id:'');

				    $r = $this->db->query($sql);

				  	return $r->row_array(); //for single row

				  }



			 break;



			case 'insert': //insert operation

		                $status = $this->db->insert(self::DEVICE_TABLE,$data);

 						return	array('status'=>$status,'ID'=>($status == TRUE ? $this->db->insert_id():''));

			      break;

			case 'update': //update operation

			             $this->db->where("id_device",$id);

			             $status = $this->db->update(self::DEVICE_TABLE,$data);

					     return	array('status' => $status, 'ID' => $id);

			      break;

			case 'delete':

				   $this->db->where("id_device",$id);

		           $status = $this->db->delete(self::DEVICE_TABLE);

				   return	array('status' => $status, 'deleteID' => $id);

			      break;



			default: //empty record

				  $vilage =array(

		  					     	'device_name' => NULL,

		  					     	'id_device'   => NULL,

		  							'id_branch'   => NULL,

		  							'token_id'    => NULL,

		  					   );

			      return $vilage;

		}

	}

	//Web Devices Functions





	//ret collection master



	function ajax_collection_master($data)

    {

		$sql = $this->db->query("SELECT * FROM `ret_collection_master`");

		return $sql->result_array();

	}



	function get_colection($id)

	{

	    $sql = $this->db->query("SELECT * FROM `ret_collection_master` WHERE id_collection=".$id."");

		return $sql->row_array();

	}



	//ret collection master





		//ret repair master



	function ajax_repair_master($data)

    {

		$sql = $this->db->query("SELECT * FROM `ret_repair_master`");

		return $sql->result_array();

	}



	function get_repair_items($id)

	{

	    $sql = $this->db->query("SELECT * FROM `ret_repair_master` WHERE id_repair_master=".$id."");

		return $sql->row_array();

	}



	//ret repair master





		/*Product Division functions here */



	function ajax_getprod_division()

    {

		$prod_division = $this->db->query("SELECT * FROM ret_product_division ORDER BY id_pro_division desc");

		return $prod_division->result_array();

	}



	function get_prod_division($id_pro_division)

    {

		$prod_division = $this->db->query("select * FROM ret_product_division where id_pro_division=".$id_pro_division);

		return $prod_division->row_array();

	}



    public function insert_prod_division($data)

    {

    	$insert_flag = 0;

		$insert_flag = $this->db->insert('ret_product_division',$data);

		return ($insert_flag == 1 ? $this->db->insert_id(): 0);

	}



	public function update_prod_division($data,$id)

    {

    	$edit_flag = 0;

    	$this->db->where('id_pro_division',$id);

		$edit_flag = $this->db->update('ret_product_division',$data);

		return ($edit_flag==1?$id:0);

	}



	public function delete_prod_division($id)

    {

        $this->db->where('id_pro_division', $id);

        $status= $this->db->delete('ret_product_division');

		return $status;

	}

	/*End of product division functions*/





	//Rate Purity Master



    // To select Metal Name from db to Metal rate Purity>catalog master>add

	function MetalSelectModel()

	{

		$data = $this->db->query("SELECT id_metal,metal FROM `metal`");

		return $data->result_array();

	}

	// To select Purity Name from db to Metal rate Purity>catalog master>add

	function PuritySelectModel($id_metal)

	{

		// $data = $this->db->query('SELECT id_purity from ret_purity');

		$data = $this->db->query("SELECT rc.id_metal,r.id_purity,purity,m.metal

		FROM ret_metal_cat_purity r

		LEFT JOIN ret_purity rp on rp.id_purity = r.id_purity

		LEFT JOIN ret_category rc on rc.id_ret_category = r.id_category

		LEFT JOIN metal m on m.id_metal = rc.id_metal

		where rc.id_metal=" . $id_metal . " group by purity;");



		return $data->result_array();

	}

	//To select COLUMN NAME to Metal Rate Purity>catalog master>

	function MetalRatesModel()

	{

		$data = $this->db->query("SELECT DISTINCT COLUMN_NAME

		FROM INFORMATION_SCHEMA.COLUMNS

		WHERE  TABLE_NAME = 'metal_rates'  AND COLUMN_NAME in ('goldrate_22ct','goldrate_24ct','coin_gold_22ct','silverrate_1gm','silverrate_1kg','platinum_1g','goldrate_14ct','goldrate_18ct','mjdmasilverrate_1gm','market_gold_18ct','market_gold_20ct','market_gold_995','mjdmasilverrate_999','goldrate_9ct');");

		return $data->result_array();

	}

	function get_saved_purity()

	{

		$data = $this->db->query("SELECT pr.id_metal_purity_rate,pr.id_metal,pr.id_purity,pr.rate_field,pr.market_rate_field,m.metal as metalname,r.purity as PurityName

		from ret_metal_purity_rate pr

		LEFT JOIN metal m on m.id_metal = pr.id_metal

		LEFT JOIN ret_purity r on r.id_purity = pr.id_purity");



		return $data->result_array();

	}



	//To get datas from db to Edit Modal

	function get_Purityratemetals($id)

	{

		 $data = $this->db->query("SELECT r.rate_field,r.id_purity,m.metal as metal_name,rp.purity as purity_name,r.id_metal

    	 FROM ret_metal_purity_rate r

    	 LEFT JOIN metal m ON m.id_metal = r.id_metal

    	 LEFT JOIN ret_purity rp ON rp.id_purity = r.id_purity

    	 WHERE id_metal_purity_rate= $id");

		return $data->row_array();

	}

	function addmodel($metal, $purity, $rate)

	{

		$data = $this->db->query("SELECT * FROM ret_metal_purity_rate WHERE id_metal='" . $metal . "'

	    AND id_purity='" . $purity . "'");

		if ($data->num_rows() == 0) {

			return array('status' => TRUE);

		} else {

			return array('status' => FALSE);

		}

	}

	public function editmodel($edmetal, $edpurity, $id)
{
    $data = $this->db->query("SELECT * FROM ret_metal_purity_rate WHERE id_metal = '$edmetal'AND id_purity = '$edpurity'
	AND id_metal_purity_rate != '$id'");

    if ($data->num_rows() < 1) {
        return array('status' => TRUE);
    } else {
        return array('status' => FALSE);
    }
}


	//Rate Purity Master



	function get_deposit($id="", $max = "", $limit = "", $from_date = "", $to_date = "", $type = 0, $dep_branch = 0) {



		$where = "";



		if($id > 0) {



			$where = $where." AND dep_id = ".$id;



		}



		if($type > 0) {



			$where = $where." AND type = ".$type;



		}



		if($dep_branch > 0) {



			$where = $where." AND dep_branch = ".$dep_branch;



		}



		if($from_date != "" && $to_date != "") {



			$where = $where." AND (date(dep.created_on) BETWEEN '".date("Y-m-d",strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ORDER BY created_on ASC";



		}

		else if ($max == 'max') {



			$where = $where." ORDER BY dep_id DESC LIMIT ".$limit;



		} else {



			$where = $where." ORDER BY dep_id ASC";



		}



		$sql = "SELECT

					dep_id,



					dep_branch,



					br.name AS branch_name,



					FORMAT(dep_amount, 2, 'en_IN') AS dep_amount_formatted,



					dep_amount,



					type,



					IF(IFNULL(type,0) = 1, 'Retail', IF(IFNULL(type,0) = 2, 'CRM', '')) AS cash_type,



					dep_type,



					IF(dep_type = 1, 'Credit', 'Debit') AS deposit_type,



					dep_bank,



					dep_ref_id,



					dep_cur_balance,



					dep_narration,



					dep_by,



					dep_date,



					Date_Format(dep_date,'%d-%m-%Y %H:%i:%S') AS deposited_date,



					cash_date,



					Date_Format(cash_date,'%d-%m-%Y') AS cash_in_hand_date,



					dep_mode,



					Date_Format(created_on,'%d-%m-%Y %H:%i:%S') AS entry_date,



					created_on,



					created_by,



					bnk.bank_name,



					pm.mode_name,



					CONCAT(firstname,' ',IFNULL(lastname,'')) AS deposited_by



				FROM ret_bank_deposit AS dep



				LEFT JOIN branch br on br.id_branch = dep.dep_branch



				LEFT JOIN bank AS bnk ON bnk.id_bank = dep.dep_bank



				LEFT JOIN payment_mode AS pm ON pm.id_mode = dep.dep_mode



				LEFT JOIN employee AS emp ON emp.id_employee = dep.dep_by



				WHERE 1".$where;



		$sql = $this->db->query($sql);



		return $sql->result_array();

   }



   function getDeposit_empty_record() {



		$deposit = array(



			'dep_branch'	=>	1,



			'dep_amount'	=>	"",



			'dep_type'		=>	1,



			'dep_bank'		=>	1,



			'dep_ref_id'	=>	"",



			'dep_narration'	=> "",



			'dep_by'		=> 0,



			'deposited_date' => date("d-m-Y H:i:s"),



			'cash_in_hand_date' => date("d-m-Y"),



			'dep_mode'		=> 1,



		);



		return $deposit;

   }



	function get_banks()

	{

		 $sql = "SELECT

					 id_bank,



					 bank_name,



					 short_code



					 FROM bank";



		 $sql = $this->db->query($sql);



		 return $sql->result_array();

	}



	function get_payment_mode()

	{

		 $sql = "SELECT

					 id_mode,



					 mode_name,



					 short_code



					 FROM payment_mode



					 WHERE status = 1



					 ORDER BY sort_order";



		 $sql = $this->db->query($sql);



		 return $sql->result_array();

	}



	function get_employee()

	{

		 $sql = "SELECT



					 id_employee,



					 CONCAT(firstname,' ',IFNULL(lastname,'')) AS emp_name



				 FROM  employee



				 WHERE active = 1";



			 $sql = $this->db->query($sql);



			 return $sql->result_array();

	}



	// =========================================================================
	// NEW: getall_cashamt() — uses cash_flow_model (2026-05-23)
	// Old monolithic version commented out below for reference.
	// =========================================================================
	function getall_cashamt($branch = 0) {
		$CI =& get_instance();
		$CI->load->model('cash_flow_model');

		return $CI->cash_flow_model->get_cash_split(intval($branch));
	}

	// =========================================================================
	// OLD: getall_cashamt() — COMMENTED OUT (2026-05-23)
	// Replaced by cash_flow_model->get_cash_split()
	// Kept for comparison testing and rollback reference.
	// =========================================================================
	/*
	function getall_cashamt_OLD($branch = 0) {

		$date_restriction = "2022-10-10";

		$where_billing_payment = "";

		if($branch > 0) {

			$where_billing_payment = " AND bill.id_branch = ".$branch;

		}

		$sql = "SELECT

					IFNULL(SUM(payment_amount),0) AS cash_amt

			FROM  ret_billing_payment AS bill_pay

			LEFT JOIN ret_billing AS bill ON bill.bill_id = bill_pay.bill_id

			WHERE payment_mode = 'Cash' AND bill_status = 1 AND DATE(bill.bill_date) >= '".$date_restriction."' ".$where_billing_payment;

		$sql = $this->db->query($sql);

		$result = $sql->row_array();

		$billing_payment = $result['cash_amt'];



		$where_iss_rcp_payment = "";

		if($branch > 0) {

			$where_iss_rcp_payment = " AND ir.id_branch = ".$branch;

		}

		$sql = "SELECT

					IFNULL(SUM(IF(ir_pay.type = 1, payment_amount, (payment_amount * -1))),0) AS cash_amt

			FROM  ret_issue_rcpt_payment AS ir_pay

			LEFT JOIN ret_issue_receipt AS ir ON ir.id_issue_receipt = ir_pay.id_issue_rcpt

			WHERE payment_mode = 'Cash' AND bill_status = 1 AND DATE(ir.bill_date) >= '".$date_restriction."' ".$where_iss_rcp_payment;

		$sql = $this->db->query($sql);

		$result = $sql->row_array();

		$iss_rcp_payment = $result['cash_amt'];



		$where_chit_pay = "";

		if($branch > 0) {

			$where_chit_pay = " AND pay.id_branch = ".$branch;

		}

		$sql = "SELECT SUM(dt.payment_amount) as cash_amt
					FROM payment_mode_details dt
			LEFT JOIN payment as pay ON pay.id_payment = dt.id_payment
			WHERE approval_date is not null AND dt.payment_mode = 'CSH' AND dt.payment_status = 1 AND pay.payment_status = 1 AND DATE(pay.approval_date) >= '".$date_restriction."' ".$where_chit_pay;

		$sql = $this->db->query($sql);

		$result = $sql->row_array();

		$chit_pay = $result['cash_amt'];

		$return['retail_cash']	=	$billing_payment + $iss_rcp_payment;

		$return['chit_cash']	=	$chit_pay;

		$return['total_pay'] 	= 	$return['retail_cash'] + $return['chit_cash'];

		return $return;

	}
	*/



	function get_sum_of_deposits($branch = 0, $type = 0) {



		$where = "";



		if($branch > 0) {



			$where = $where." AND dep_branch = ".$branch;



		}



		if($type > 0) {



			$where = $where." AND type = ".$type;



		}



		$sql = "SELECT



					IFNULL(SUM(IF(dep_type = 1, dep_amount, (dep_amount * -1))),0) AS dep_amount



				FROM ret_bank_deposit WHERE 1 ".$where;



		$sql = $this->db->query($sql);



		$result = $sql->row_array();



		$dep_amount = $result['dep_amount'];



		return $dep_amount;



	}


	function check_deposit_ref_id_exists($ref_id, $exclude_id = 0) {

		$this->db->where('dep_ref_id', $ref_id);

		if ($exclude_id > 0) {
			$this->db->where('dep_id !=', $exclude_id);
		}

		$count = $this->db->count_all_results('ret_bank_deposit');

		return ($count > 0);

	}









	function get_karigar_approvalWastage_list($data)

	{



		$sql = $this->db->query("SELECT kw.id_karikar_wast,kw.id_karikar,



		kw.id_category,kw.id_product,kw.id_design,kw.id_sub_design,



	    kw.wastage_type,kw.calc_type,kw.karigar_calc_type,IFNULL(kar.contactno1,'') as mobile,



		ifnull(kw.wastage_per,'0') as wastage_per,ifnull(kw.wastage_wt,'0') as wastage_wt,



		if(kw.wastage_type=1,concat(kw.wastage_per,' ','%'),concat(kw.wastage_wt,' ','Gm')) as wastage_percentage,



		kw.mc_type,if(kw.mc_type=1,'Per Grm','Per Pcs') as type, kw.mc_value,kw.pur_touch,



		kar.firstname as karigar,kw.id_purity,ifnull(pur.purity,'') as purity,



		ifnull(cat.name,'All Categories') as cat_name,ifnull(pro.product_name,'All Produts') as product_name,ifnull(des.design_name,'All Designs') as design_name,ifnull(subdes.sub_design_name,'All Sub Designs') as sub_design_name,wast_img.image_name,

		kw.status,if(kw.status =0,'Yet to Approve','Hold') as wastage_status,ifnull(kar.company,'') as karigar_company,ifnull(kw.uom_id,'') as uom_id,ifnull(kw.quality_id,'') as quality_id,ifnull(kw.from_wt,0) as from_wt,ifnull(kw.to_wt,0) as to_wt,ifnull(kw.rate_per_gram,0) as rate_per_gram,kw.stone_cal_type,
		ifnull(pro.stone_type,0) as stone_type





		FROM ret_karikar_items_wastage kw



		LEFT JOIN ret_karigar kar on kar.id_karigar=kw.id_karikar



		LEFT JOIN ret_category cat on cat.id_ret_category=kw.id_category



		LEFT JOIN ret_product_master pro on pro.pro_id=kw.id_product



		LEFT JOIN ret_design_master des on des.design_no=kw.id_design



		LEFT JOIN ret_purity pur on pur.id_purity=kw.id_purity



		LEFT JOIN ret_sub_design_master subdes on subdes.id_sub_design=kw.id_sub_design



		LEFT JOIN (SELECT ki.id_karikar_wast,ki.image_name

		FROM ret_karigar_item_wast_pro_images ki

		where ki.is_default =1 ) as wast_img on wast_img.id_karikar_wast = kw.id_karikar_wast



		WHERE



		".($data['status']!="" && $data['status']==0 ? "kw.status=0" : ($data['status']!="" &&  $data['status']==3 ? "kw.status=3" : "(kw.status=0 or kw.status=3)"))."



		".($data['id_karigar']!='' && $data['id_karigar'] > 0 ? "and kw.id_karikar=".$data['id_karigar'].



		"":'')." ORDER BY kw.id_karikar_wast DESC");



		//print_r($this->db->last_query());exit;



	    $result =  $sql->result_array();

	    $returnData = [];

	    foreach($result as $val)

	    {

	        $val['charges_details'] = $this->get_karigar_charges($val['id_karikar_wast']);

	        $returnData[]=$val;

	    }

        return $returnData;

	}





	function get_karigar_approvalStones_list($data)

	{

		$sql = $this->db->query("SELECT ks.id_karigar_stone,ks.id_karigar,ks.stone_type as stone_type_id,ks.stone_id,



		ks.uom_id,if(ks.stone_cal_type=1,'Weight','Per Pcs') as stn_cal_type,ks.rate_per_gram as stn_rate,



		IFNULL(kar.firstname ,'-')as karigar,st.stone_type,stn.stone_name,uom.uom_name,ifnull(qc.code,'')as quality_code,ifnull(rc.clarity,'')as clarity,



		ifnull(ro.color,'')as color,ifnull(rt.cut,'')as cut,ifnull(rs.name,'') as shape,ifnull(ks.from_wt,'0') as from_wt ,ifnull(ks.to_wt,'0') as to_wt,



		IFNULL(ks.rejected_on,'-') as rejected_on,IFNULL(concat(ed.firstname,' ',IF(ed.lastname is not null, ed.lastname,'-')),'') as rejected_by,



		IFNULL(ks.approved_on,'-') as approved_on,IFNULL(ks.created_on,'-') as created_on,ifnull(concat(ec.firstname,' ',IF(ec.lastname is not null, ec.lastname,'-')),'')as created_by,



		ifnull(concat(ea.firstname,' ',IF(ea.lastname is not null, ea.lastname,'-')),'')as approved_by,



		ks.status,if(ks.status =0,'Yet to Approve','Hold') as stone_status,ifnull(kar.company,'') as company,iFNULL(ks.quality_id,'') as quality_id




		FROM ret_karigar_stones ks



		LEFT JOIN ret_karigar kar on kar.id_karigar=ks.id_karigar



		LEFT JOIN employee ed   ON ed.id_employee = ks.rejected_by



		LEFT JOIN employee ea   ON ea.id_employee = ks.approved_by



        LEFT JOIN employee ec   ON ec.id_employee = ks.created_by



		LEFT JOIN ret_quality_code qc on qc.quality_id = ks.quality_id



        LEFT JOIN ret_clarity rc  on rc.id_clarity = qc.clarity_id



		LEFT JOIN ret_color ro  on ro.id_color = qc.color_id



		LEFT JOIN ret_cut rt  on rt.id_cut = qc.cut_id



		LEFT JOIN ret_shape rs on rs.shape_id = qc.shape_id



		LEFT JOIN ret_stone_type st on st.id_stone_type=ks.stone_type



		LEFT JOIN ret_stone stn on stn.stone_id=ks.stone_id



		LEFT JOIN ret_uom uom on uom.uom_id=ks.uom_id



		WHERE ks.status=0 or ks.status=3".($data['id_karigar']!='' && $data['id_karigar'] > 0 ? "and ks.id_karigar=".$data['id_karigar']."":"")."



		ORDER BY ks.id_karigar_stone DESC");



		//print_r($this->db->last_query());exit;



		return $sql->result_array();



	}





	function get_karigar_approvalCharges_list($data)

	{



		$sql=$this->db->query("SELECT kc.id_karigar_charges,kc.id_karigar,kc.charge_id,kc.charge_value,



		kar.firstname as karigar,ch.name_charge as charge_name



		FROM ret_karigar_charges kc



		LEFT JOIN ret_karigar kar on kar.id_karigar=kc.id_karigar



		LEFT JOIN ret_charges ch on ch.id_charge=kc.charge_id



		WHERE kc.status=0 ".($data['id_karigar']!='' && $data['id_karigar']>0 ? "and kc.id_karigar=".$data['id_karigar']."":"")."



		ORDER BY kc.id_karigar_charges DESC");



		//print_r($this->db->last_query());exit;





		return $sql->result_array();

	}







	public function update_karigar_wastages($id_karikar,$cat_id="",$id_purity="",$pro_id="",$des_id="",$sub_des_id="",$quality_id="",$from_wt="",$to_wt="")
	{
		$status = $this->db->query("UPDATE ret_karikar_items_wastage SET active='0'
		WHERE id_karikar=".$id_karikar."
		".($cat_id!='' ? "and id_category=".$cat_id."" : "")."
		".($id_purity!='' ? "and id_purity=".$id_purity."" : "")."
		".($pro_id!="" ? "and id_product=".$pro_id."":"")."
		".($des_id!=''? "and id_design=".$des_id."":"")."
		".($sub_des_id!='' ? "and id_sub_design=".$sub_des_id."":"")."
		".($quality_id!='' ? "and quality_id=".$quality_id."":"")."
		".($from_wt!='' && $from_wt>0 ? "and from_wt <= ".$from_wt." and to_wt >= ".$from_wt."":"")."
		".($to_wt!='' && $to_wt>0 ? "and from_wt <= ".$to_wt." and to_wt >= ".$to_wt."":"")."");
       // print_r($this->db->last_query());exit;
		return $status;
	}



	function getWastageDetails($id_karikar)

	{

		$sql = $this->db->query("SELECT * FROM ret_karikar_items_wastage where id_karikar=".$id_karikar."");



		return $sql->result_array();

	}



	function get_WastApprvKarigars($approval_for)

	{

		$sql = $this->db->query("SELECT k.id_karigar, if(karigar_type=1,ifnull(company,CONCAT(firstname,' ',IFNULL(lastname,''))),CONCAT(firstname,' ',IFNULL(lastname,''))) as karigar

		FROM ret_karigar k

		LEFT JOIN ret_karikar_items_wastage kw on kw.id_karikar = k.id_karigar

		LEFT JOIN ret_karigar_stones ks on ks.id_karigar = k.id_karigar

		WHERE ".($approval_for ==0 ? "k.status_karigar = 1 and (kw.status=0 or kw.status=3)" : "k.status_karigar = 1 and (ks.status=0 or  ks.status=3 )")."

		Group by k.id_karigar");

		return $sql->result_array();

	}





	public function get_active_karigar_wastages($id_karikar,$pro_id,$des_id,$sub_des_id)

	{

		$sql = $this->db->query("SELECT * from ret_karikar_items_wastage



		WHERE id_karikar=".$id_karikar." and id_product=".$pro_id." and id_design=".$des_id." and id_sub_design=".$sub_des_id."");



       // print_r($this->db->last_query());exit;



		return $sql->result_array();

	}



	public function update_karigar_stones($id_karigar,$stone_type,$stone_id,$quality_id='',$from_wt='',$to_wt='')
	{

		$status = $this->db->query("UPDATE ret_karigar_stones SET active='0'
		WHERE id_karigar=".$id_karigar." and stone_type=".$stone_type." and stone_id=".$stone_id."
		".($quality_id!='' ? "and quality_id=".$quality_id."":"")."
		".($from_wt!='' && $from_wt>0 ? "and from_wt=".$from_wt."":"")."
		".($to_wt!='' && $to_wt>0 ? "and to_wt=".$to_wt."":"")."
		");
		// print_r($this->db->last_query());exit;
		return $status;

	}



	public function update_karigar_charges($id_karigar,$charge_id)

	{

		$status = $this->db->query("UPDATE ret_karigar_charges SET active='0'



		WHERE id_karigar=".$id_karigar." and charge_id=".$charge_id."");



		// print_r($this->db->last_query());exit;



		return $status;

	}



	function getActiveStoneTypes()

	{

		$sql = $this->db->query("SELECT * FROM ret_stone_type WHERE status=1");



		//print_r($this->db->last_query());exit;



		return $sql->result_array();

	}



	function getActiveCharges()

	{

		$sql = $this->db->query("SELECT * FROM ret_charges WHERE id_charge is not null");



		return $sql->result_array();

	}



	function get_ActiveStones($data)

	{

		$sql=$this->db->query("SELECT stone_id,stone_name,uom_id from ret_stone where stone_type=".$data['stone_type']."");



		//print_r($this->db->last_query());exit;



		return $sql->result_array();

	}



	function get_karigar_images($id_karikar_wast)

	{

		$returnData = array();



		$sql=$this->db->query("SELECT * FROM ret_karigar_item_wast_pro_images where id_karikar_wast=".$id_karikar_wast."");



		$result = $sql->result_array();



		foreach($result as $val)

		{

			$path = 'assets/img/karigar/product_wastage/'.$val['id_karikar_wast'].'/'.$val['image_name'];

			$type = pathinfo($path, PATHINFO_EXTENSION);

			$data = file_get_contents($path);

			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);



			$returnData[]=array(



				'id_pro_image'  => $val['id_pro_image'],



				'id_karikar_wast' => $val['id_karikar_wast'],



				'image_name'   => $val['image_name'],



				'is_default'  => $val['is_default'],



				'img_src'    => $base64,

			);



		}



		return $returnData;

	}







	function get_sectionBranchwise($data)

	{

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



		".($id_branch !='' && $id_branch != 0 ? " and sect_br.id_branch IN (".$id_branch.")" :'')." ".$where."


		GROUP BY sect.id_section");





		return $sql->result_array();



	}





	function get_sectionsbranch($id_section)

	{

		$list_branch=[];



		$sql=$this->db->query("SELECT br.id_branch,br.name as branch_name



		FROM ret_section_branch sect_br



		LEFT join branch br on br.id_branch=sect_br.id_branch



		WHERE sect_br.id_section=".$id_section."");





		for($i=0;$i<count($sql->result_array());$i++)

		{

			array_push($list_branch,

			$sql->result_array()[$i]['id_branch']);





		}



		return $list_branch;

	}





	function get_ActiveKYC()

	{



		$sql=$this->db->query("SELECT id_kyc_master,name as kyc_name FROM ret_kyc_master where status=1");



		//print_r($this->db->last_query());exit;



		return $sql->result_array();

	}







	function get_ActiveKYCDetails($data)

	{

		$sql=$this->db->query("SELECT * FROM ret_kyc_master WHERE id_kyc_master=".$data['id_kyc']."");



		return $sql->result_array();

	}





	function get_karigar_kyc_det($id_karigar)

	{

		$return_data = array();



		$sql = $this->db->query("SELECT k.id_karigar_kyc,k.id_karigar,k.id_kyc,ifnull(k.kyc_number,'') as kyc_number,



		ifnull(k.front_images,'')  as front_images,ifnull(k.back_images,'') as back_images,ifnull(k.document,'') as document,km.image,



		km.is_id_req,km.regular_expression,km.image_type,ifnull(km.doc_type,1) as doc_type



		FROM ret_karigar_kyc k



		LEFT JOIN ret_kyc_master km on km.id_kyc_master = k.id_kyc



		where id_karigar=".$id_karigar."");



		//print_r($this->db->last_query());exit;



		$result = $sql->result_array();



		foreach($result as $r)

		{



			if($r['front_images']!='')

			{

				$path = 'assets/img/karigar/kyc/'.$r['front_images'];

				$type = pathinfo($path, PATHINFO_EXTENSION);

				$data = file_get_contents($path);

				$fr_base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

			}

			else

			{

				$fr_base64 ='';

			}





			if($r['back_images']!='')

			{



				$Bkpath = 'assets/img/karigar/kyc/'.$r['back_images'];

				$Bktype = pathinfo($Bkpath, PATHINFO_EXTENSION);

				$Bkdata = file_get_contents($Bkpath);

				$Bk_base64 = 'data:image/' . $Bktype . ';base64,' . base64_encode($Bkdata);

			}

			else

			{

				$Bk_base64 ='';

			}



			if($r['document']!='')

			{

				$Docpath = 'assets/img/karigar/kyc/documents/'.$r['document'];



				$DocType = ($r['doc_type'] ==1 ? 'application/pdf':'application/vnd.openxmlformats-officedocument.wordprocessingml.document');



				$b64Doc = chunk_split(base64_encode(file_get_contents($Docpath)));



				$Doc_base64 = 'data:image/' . $DocType . ';base64,' . ($b64Doc);



				//print_r($Docdata);exit;

			}

			else

			{

				$Doc_base64='';

			}







			$return_data[]=array(



				'id_karigar'        =>  $r['id_karigar'],



				'id_kyc'          => $r['id_kyc'],



				'id_karigar_kyc'   =>  $r['id_karigar_kyc'],



				'kyc_number'       =>  $r['kyc_number'],



				'front_images'   =>  $r['front_images'],



				'back_images'   =>  $r['back_images'],



				'is_id_req'   =>  $r['is_id_req'],



				'image'      =>  $r['image'],



				'regular_expression'  =>  $r['regular_expression'],



				'image_type'   =>  $r['image_type'],



				'doc_type'    => $r['doc_type'],



				'fr_img_src'  =>  $fr_base64,



				'bk_img_src'  =>  $Bk_base64,



				'document'  => $r['document'],



				'doc_src'  => $Doc_base64,

			);



		}



		return $return_data;

	}





	//Master:Diamond Quality Starts

    function get_empty_quality()

	{

	$data=array(

			'code'			=> NULL,

			'clarity_id'    => NULL,

			'color_id'      => NULL,

			'cut_id'        => NULL,

			'shape_id'      => NULL,

			'status'        => 1,

		);

		return $data;

	}

    function ajax_getquality()

	{

		$sql=$this->db->query("SELECT q.quality_id,q.code,c.id_clarity,c.clarity,cr.id_color,cr.color,

		ct.id_cut,ct.cut,s.shape_id,s.name,q.status

		FROM ret_quality_code q

		left join ret_clarity c on (q.clarity_id=c.id_clarity)

		left join ret_color cr on (q.color_id=cr.id_color)

		left join ret_cut ct on (q.cut_id=ct.id_cut)

		left join ret_shape s on (q.shape_id=s.shape_id)");



		return $sql->result_array();

		//print_r($data);

	}

    function get_quality($quality_id)

    {

		$quality_id = $this->db->query("SELECT * FROM ret_quality_code WHERE  quality_id=".$quality_id);

		return $quality_id->row_array();

		//print_r($quality_id);exit;



	}

	//quality check

	public function check_code($code)

	{



		$this->db->select('code');



		$this->db->where('code', $code);



		$user=$this->db->get(self::TABLE_NAME);



		return ($user->num_rows() > 0? TRUE : FALSE);



	}

	function getclarity()

    {

		$data = $this->db->query("SELECT * FROM ret_clarity WHERE status = 1");

		return $data->result_array();

	}

	function getcolor()

    {

		$data = $this->db->query("SELECT * FROM ret_color WHERE status = 1");

		return $data->result_array();

	}

	function getcut()

    {

		$data = $this->db->query("SELECT * FROM ret_cut WHERE status = 1");

		return $data->result_array();

	}

	function getshape()

    {

		$data = $this->db->query("SELECT * FROM ret_shape WHERE status = 1");

		return $data->result_array();

	}

//Master:Diamond Quality Ends

//Master:Diamond Quality rate Starts

    function get_empty_rate()

	{

		$data=array(

			'rate_id'			    => NULL,

			'quality_code_id'	    => NULL,

			'rate_status'           => 1,

		);

		return $data;

	}

	function get_empty_rate_list()

	{

	$data=array(

			'from_cent'			=> NULL,

			'to_cent'	        => NULL,

			'rate'	            => NULL,

		);

		return $data;

	}

    function getquality()

    {

		$data = $this->db->query("SELECT q.quality_id,q.code FROM ret_quality_code q WHERE status = 1");

		return $data->result_array();

	}

	function ajax_getrate($data)

    {

		$data = $this->db->query("SELECT *FROM ret_diamond_rate r

		left join ret_quality_code q on (r.quality_code_id=q.quality_id)

		WHERE r.rate_id IS NOT NULL

		".($data['quality_code_id']!='' ? " and r.quality_code_id=".$$data['quality_code_id']."" :'')."");

		//print_r($this->db->last_query());exit;

		return $data->result_array();

	}

	function get_rate($id_cents_rate)

    {

		$id_cents_rate = $this->db->query("select * from ret_diamond_cent_rates where id_rate_id=".$id_cents_rate);

		//print_r($this->db->last_query());exit;

		return $id_cents_rate->result_array();

	}

	function get_quality_rate($rate_id)

    {

		$rate_id = $this->db->query("select * from ret_diamond_rate where rate_id=".$rate_id);

		return $rate_id->row_array();

	}

	function get_status($quality_code_id)

	{

		$data = $this->db->query("SELECT * FROM ret_diamond_rate  WHERE quality_code_id=".$quality_code_id);

		$status = $data->num_rows();

		if($status)

		{

			$return_data=array('status'=>true,'message'=>'status updated successfully');

		}else{

			$return_data=array('status'=>false,'message'=>'');

		}

		return $return_data;

	}

//Master:Diamond Quality rate Ends



    function get_bank_details($id_karigar)

    {

        $id = $this->db->query("select rb.id_acc_details,rb.bank_name,rb.account_name,rb.account_number,rb.ifsc_code
        from ret_karigar_bank_acc_details rb
		where rb.id_karigar=".$id_karigar);
		//print_r($this->db->last_query());exit;
        return $id->result_array();

    }



    function getCompanyDetails($id_branch="")

    {

        if($id_branch=='')

        {

            $sql = $this->db->query("Select  c.id_company,c.company_name,c.gst_number,c.short_code,c.pincode,c.mobile,c.whatsapp_no,c.phone,c.email,c.website,c.address1,c.address2,c.id_country,c.id_state,c.id_city,ct.name as city,s.name as state,cy.name as country,cs.currency_symbol,cs.currency_name,cs.mob_code,cs.mob_no_len,c.mail_server,c.mail_password,c.send_through,c.mobile1,c.phone1,c.smtp_user,c.smtp_pass,c.smtp_host,c.server_type,cs.login_branch,

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

            cy.name as country,ct.name as city,s.name as state,b.pincode,s.id_state,s.state_code,cy.id_country, c.gst_number

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



    function get_print_karigar($id_karigar)

    {

        $id = $this->db->query("select d.id_dealer,rk.id_karigar, CONCAT(rk.firstname,' ',IFNULL(rk.lastname,'')) as karigar,rk.contactno1,

        rk.address1,rk.address2,rk.address3,ct.name as city,s.name as state,cy.name as country,rk.pincode,rk.gst_number,s.state_code,

        rk.code_karigar

        from ret_karigar rk

        join company c

        left join dealer d on (rk.id_karigar=d.id_dealer)

        left join country cy on (rk.id_country=cy.id_country)

        left join state s on (rk.id_state=s.id_state)

        left join city ct on (rk.id_city=ct.id_city)

        where id_karigar=".$id_karigar);

        //print_r($this->db->last_query());exit;

        return $id->row_array();

    }



    function get_karigar_wastage_details($id_karigar)

    {

        $id = $this->db->query("select rk.id_karikar_wast,rm.product_name,rc.name,rd.design_name,rk.pur_touch,

        rs.sub_design_name,rk.wastage_per,rp.purity,rk.mc_value,Date_Format(rk.created_on,'%d-%m-%Y') as requested_date,

		Date_Format(rk.approved_on,'%d-%m-%Y') as approved_date,ifnull(cr.firstname,'') as created_by,

		ifnull(apr.firstname,'') as approved_by

        from ret_karikar_items_wastage rk

        left join ret_product_master rm on (rk.id_product=rm.pro_id)

        left join ret_category rc on (rk.id_category=rc.id_ret_category)

        left join ret_design_master rd on (rk.id_design=rd.design_no)

        left join ret_sub_design_master rs on (rk.id_sub_design=rs.id_sub_design)

        left join ret_purity rp on (rk.id_purity=rp.id_purity)

			left join employee cr on cr.id_employee = rk.created_by

			left join employee apr on apr.id_employee = rk.approved_by

        where id_karikar=".$id_karigar);

        //print_r($this->db->last_query());exit;

        return $id->result_array();

    }



    function get_karigar_gem_stones($id_karigar)

    {

        $id = $this->db->query("select rs.id_karigar_stone,re.stone_name,rt.stone_type,ru.uom_name,

        rs.rate_per_gram,rs.stone_cal_type,IFNULL(qc.code,'') as code,IFNULL(rs.from_wt,'') as from_wt,IFNULL(rs.to_wt,'') as to_wt,

		IFNULL(cl.clarity,'') as clarity,IFNULL(color.color,'') as color,IFNULL(cut.cut,'') as cut,IFNULL(shape.name,'') as shape,

		ru.uom_short_code as uom_code

        from ret_karigar_stones rs

        left join ret_stone re on (rs.stone_id = re.stone_id)

        left join ret_stone_type rt on (rs.stone_type = rt.id_stone_type)

        left join ret_uom ru on (rs.uom_id = ru.uom_id)

        left join ret_quality_code qc on (qc.quality_id = rs.quality_id)

		left join ret_clarity cl on (cl.id_clarity = qc.clarity_id)

        left join ret_color color on (color.id_color = qc.color_id)

        left join ret_cut cut on (cut.id_cut = qc.cut_id)

        left join ret_shape shape on (shape.shape_id = qc.shape_id)

        where rs.active =1 and rs.id_karigar =".$id_karigar);

        return $id->result_array();

    }





    //Selling V.A and M.C Settings

    function get_ret_settings($settings)

	{

		$data=$this->db->query("SELECT value FROM ret_settings where name='".$settings."'");

		return $data->row()->value;

	}

	function check_branch_wastage_settings($id_branch,$id_sub_design_mapping)

	{

		$data=$this->db->query("Select count(*) as cnt from ret_design_weight_range_wc where id_sub_design_mapping = '".$id_sub_design_mapping."' and id_branch = '".$id_branch."' ;");

		return $data->row()->cnt;

	}

	function delete_branch_wastage_settings($id_branch,$id_sub_design_mapping)

	{



		$data=$this->db->query("DELETE FROM `ret_design_weight_range_wc` WHERE id_sub_design_mapping = '".$id_sub_design_mapping."' and id_branch = '".$id_branch."' ;");



	}



	function get_product_weight($data)

	{

		$data=$this->db->query("select w.from_weight,w.to_weight,w.id_weight from ret_weight w WHERE w.id_product = '".$data['id_product']."' and w.id_design = '".$data['id_design']."' and w.id_sub_design ='".$data['id_sub_design']."';");

		return $data->result_array();

	}



	//selling rate settings

	function check_selling_rate_settings($id_product,$id_deign,$id_sub_design,$id_branch = "",$id_selling_settings = "")

	{



		// echo "SELECT * FROM `ret_selling_settings` WHERE id_product=".$id_product." AND id_design=".$id_deign." ".($id_sub_design!='' && $id_sub_design>0 ? " and id_sub_design=".$id_sub_design."" :'')." ".($is_va_mc_based_on_branch == 1 && $id_branch != '' ? " and id_branch=".$id_branch."" :'')."";exit;

		$sql=$this->db->query("SELECT * FROM `ret_selling_settings` WHERE id_product=".$id_product." AND id_design=".$id_deign." ".($id_sub_design!='' && $id_sub_design>0 ? " and id_sub_design=".$id_sub_design."" :'')."
		".( $id_branch != '' ? " and id_branch=".$id_branch."" :'')."
		".( $id_selling_settings != '' ? " and id_selling_settings !=".$id_selling_settings."" :'')."");

		// print_r($this->db->last_query());exit;

	    if($sql->num_rows()>0)

	    {

	        return array('status'=>false,'message'=>'Already Added For This Product','id_selling_settings'=>$sql->row()->id_selling_settings,'type'=>$sql->row()->type);

	    }

	    else

	    {

	        return array('status'=>true,'message'=>'');

	    }

	}



	function check_selling_rate_weight_settings($from_weight,$to_wight,$id_product,$id_deign,$id_sub_design,$id_branch = "")

	{

	    $sql=$this->db->query("SELECT wc.wc_from_weight,wc.wc_to_weight,wc.wc_percent,wc.mc

        FROM ret_design_weight_range_wc wc

        LEFT JOIN ret_selling_settings s ON s.id_selling_settings=wc.id_selling_settings

        WHERE s.id_product=".$id_product." AND s.id_design=".$id_deign." ".($id_sub_design!='' ? " and s.id_sub_design=".$id_sub_design."" :'')."

        AND ((wc.wc_from_weight <= $from_weight AND wc_to_weight >= $from_weight)  OR (wc_from_weight <= $to_wight AND wc_to_weight >= $to_wight))

		".(   $id_branch != '' ? " and s.id_branch=".$id_branch."" :'')."

        ");

        //print_r($this->db->last_query());exit;

	    if($sql->num_rows()>0)

	    {

	        return array('status'=>false,'message'=>'Already Added For This Product');

	    }

	    else

	    {

	        return array('status'=>true,'message'=>'');

	    }

	}



	function ajax_get_wastage_settings_details($data)

	{

	    $responseData=array();

	    $sql=$this->db->query("SELECT s.id_selling_settings,p.product_name,des.design_name,IFNULL(subDes.sub_design_name,'') as sub_design_name,if(s.mc_type=1,'  / Piece',if(s.mc_type=2,'/ Gram',' % Price')) as mc_type,if(s.type=1,'Fixed','Weight Range') as type,IFNULL(s.mc_value,'-') as mc,IFNULL(s.mc_min,'-') as min_mc,IFNULL(s.wastage_perc ,'-') as va,IFNULL(s.wastag_min,'-') as min_va,b.name,s.type as wastage_type,s.margin_mrp, s.wastage_wt, s.wastag_min_wt, s.wastag_method

        FROM ret_selling_settings s

        LEFT JOIN ret_product_master p ON p.pro_id=s.id_product

        LEFT JOIN ret_design_master des ON des.design_no=s.id_design

        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design=s.id_sub_design

        LEFT JOIN branch b on b.id_branch=s.id_branch

        where s.id_selling_settings IS NOT NULL

        ".($data['id_product']!='' && $data['id_product']!=null ? " and s.id_product=".$data['id_product']."" :'')."

        ".($data['id_design']!='' && $data['id_design']!=null  ? " and s.id_design=".$data['id_design']."" :'')."

        ".($data['id_sub_design']!='' && $data['id_sub_design']!=null  ? " and s.id_sub_design=".$data['id_sub_design']."" :'')."

		".($data['id_branch']!='' && $data['id_branch']!=null  ? " and s.id_branch=".$data['id_branch']."" :'')."

        ");

        $result=$sql->result_array();

        foreach($result as $items)

        {

            $responseData[]=array(

                               'id_selling_settings'    =>$items['id_selling_settings'],

                               'product_name'           =>$items['product_name'],

                               'design_name'            =>$items['design_name'],

                               'sub_design_name'        =>$items['sub_design_name'],

							   'type'                   =>$items['type'],

							   'mc_type'                =>$items['mc_type'],

                               'wastag_method'          =>$items['wastag_method'],

                               'va'                     =>$items['va'],

							   'wastage_wt'             =>$items['wastage_wt'],

                               'mc'                     =>$items['mc'],

                               'min_mc'                 =>$items['min_mc'],

                               'min_va'                 =>$items['min_va'],

							   'wastag_min_wt'          =>$items['wastag_min_wt'],

							   'id_branch'              =>$items['name'],

							   'wastage_type'              =>$items['wastage_type'],

							   'margin_mrp'              =>$items['margin_mrp'],

                               );

        }

        return $responseData;

	}

	function get_wastage_details_settings($id)

	{

	    $sql=$this->db->query("SELECT * FROM `ret_design_weight_range_wc` WHERE id_selling_settings=".$id." ORDER BY id_wc ASC");

	    return $sql->result_array();

	}

	function get_wastage_mc_settings($id)

	{

	    $sql=$this->db->query("SELECT pro.cat_id,c.id_ret_category,rss.id_product,rss.id_sub_design,rss.id_design,rss.type,rss.wastag_method,rss.wastage_perc,rss.wastage_wt,

		rss.mc_value,rss.mc_type,rss.wastag_min,rss.wastag_min_wt,rss.id_branch,rss.mc_min,rss.margin_mrp

		FROM `ret_selling_settings` rss

		LEFT JOIN ret_product_master pro ON pro.pro_id = rss.id_product

		LEFT JOIN ret_category c on c.id_ret_category =pro.cat_id

		 WHERE id_selling_settings=".$id."");

	    return $sql->row_array();

	}

	function check_wastage_details_settings($id)

	{

	    $sql=$this->db->query("SELECT * FROM `ret_design_weight_range_wc` WHERE id_selling_settings=".$id."");



		if($sql->num_rows()>0)

	    {

	        return true;

	    }

	    else

	    {

	        return false;

	    }

	}

	function delete_wastage_details_settings($id)

	{

	    $this->deleteData('id_selling_settings',$id,'ret_design_weight_range_wc');

		if($this->deleteData('id_selling_settings',$id,'ret_selling_settings')){

			return true;

		}else{

			return false;

		}

	}



	public function insertBatchData($data,$table)

    {

    	$insert_flag = 0;

		$insert_flag = $this->db->insert_batch($table,$data);



		return ($insert_flag == 1 ? $this->db->insert_id(): 0);

	}



    //Selling V.A and M.C Settings





    function get_quality_code($id ="")

    {

        $sql=$this->db->query("select qc.quality_id,qc.code,rc.clarity,ro.color,rt.cut,rs.name as shape



		FROM ret_quality_code qc



		LEFT JOIN ret_clarity rc  on rc.id_clarity = qc.clarity_id



		LEFT JOIN ret_color ro  on ro.id_color = qc.color_id



		LEFT JOIN ret_cut rt  on rt.id_cut = qc.cut_id



		LEFT JOIN ret_shape rs on rs.shape_id = qc.shape_id



		where qc.status = 1");



        return $sql->result();

    }





    /*shape functions here */

	function ajax_getshape()

    {

		$shape_id = $this->db->query("SELECT * FROM ret_shape ORDER BY shape_id desc");

		return $shape_id->result_array();

	}

	public function insert_shape($data)

    {

    	$insert_flag = 0;

		$insert_flag = $this->db->insert('ret_shape',$data);

		// print_r($this->db->last_query());exit;

		return ($insert_flag == 1 ? $this->db->insert_id():0);

	}

	function get_shape($shape_id)

    {

		$shape_id = $this->db->query("select * FROM ret_shape where shape_id=".$shape_id);

		return $shape_id->row_array();

	}

	public function update_shape($data,$id)

    {

    	$edit_flag = 0;

    	$this->db->where('shape_id',$id);

		$edit_flag = $this->db->update('ret_shape',$data);

		return ($edit_flag==1?$id:0);

	}



	public function delete_shape($id)

    {

        $this->db->where('shape_id', $id);

        $status= $this->db->delete('ret_shape');

		return $status;

	}



  /*End of shape functions*/

  function getExistingKarigars($searchtxt)
  {
	$data = $this->db->query("SELECT kar.id_karigar,concat(kar.firstname,'',ifnull(kar.lastname,'')) as label
	FROM ret_karigar kar
	WHERE (kar.firstname like '%".$searchtxt."%')");
	//print_r($this->db->last_query());exit;
	return $data->result_array();
  }

  function get_kyc_details($id_karigar)
  {
	  $sql=$this->db->query("SELECT ky.id_kyc,ifnull(km.name,'') as kyc_name,
	  ifnull(ky.kyc_number,'') as kyc_number
	  from ret_karigar_kyc ky
	  LEFT JOIN ret_kyc_master km on km.id_kyc_master = id_kyc
	  where ky.id_karigar=".$id_karigar."");
	  return $sql->result_array();
  }

  	function get_seldiamond_status($quality_code_id)
	{
		$data = $this->db->query("SELECT * FROM ret_selling_diamond_rate  WHERE quality_code_id=".$quality_code_id);
		$status = $data->num_rows();
		if($status)
		{
			$return_data=array('status'=>true,'message'=>'status updated successfully');
		}else{
			$return_data=array('status'=>false,'message'=>'');
		}
		return $return_data;
	}

	function ajax_getsellrate($data)
    {
		$data = $this->db->query("SELECT *FROM ret_selling_diamond_rate r
		left join ret_quality_code q on (r.quality_code_id=q.quality_id)
		WHERE r.selling_rate_id  IS NOT NULL
		".($data['quality_code_id']!='' ? " and r.quality_code_id=".$$data['quality_code_id']."" :'')."");
		//print_r($this->db->last_query());exit;
		return $data->result_array();
	}

	function get_selling_rate($selling_rate_id)
    {
		$id_sel_cents_rate = $this->db->query("select * from ret_selling_diamond_cent_rates where id_selling_rate_id=".$selling_rate_id);
		//print_r($this->db->last_query());exit;
		return $id_sel_cents_rate->result_array();
	}

	function get_selling_quality_rate($selling_rate_id)
    {
		$rate_id = $this->db->query("select * from ret_selling_diamond_rate where selling_rate_id=".$selling_rate_id);
		return $rate_id->row_array();
	}

	function getQualityDiamondRates()
	{
		$sql = $this->db->query("SELECT s.selling_rate_id,s.quality_code_id,q.code,sd.from_cent,sd.to_cent,sd.rate
		FROM ret_selling_diamond_rate s
		LEFT JOIN ret_selling_diamond_cent_rates sd on sd.id_selling_rate_id = s.selling_rate_id
		LEFT JOIN ret_quality_code q on q.quality_id = s.quality_code_id
		WHERE s.rate_status=1");

		return $sql->result_array();
	}

	function check_quality_code($code)
	{
	    $sql = $this->db->query("SELECT * FROM `ret_quality_code` where code = '".$code."' ");
	    if($sql->num_rows() == 1){
			return 1;
		}else{
			return 0;
		}
	}

	function getHsnCode() {

		$data = $this->db->query("SELECT hsn_code FROM `ret_product_master` WHERE product_status = 1 GROUP BY hsn_code");

		return $data->result_array();

	}


	function ajax_get_stone_rates_settings($data)
	{
		if($data['type']==1)
		{
			$sql = $this->db->query("SELECT sr.id_stone_rate,sr.id_branch,sr.stone_type,br.name as branch,
			sr.stone_id,sr.uom_id,st.stone_type,s.stone_name,
			u.uom_name,ifnull(qc.code,'-') as quality_code,sr.stone_calc_type,
			if(sr.stone_calc_type=1,'Wt','PCS') as stone_calc,
			ifnull(sr.quality_id,'') as quality_id,sr.uom_id,ifnull(sr.from_wt,0) as from_cent,ifnull(sr.to_wt,0) as to_cent,
			ifnull(sr.min_rate,0) as min_rate,ifnull(sr.max_rate,0) as max_rate,sr.type
			FROM ret_stone_rate_settings sr
			LEFT join ret_stone_type st on  st.id_stone_type = sr.stone_type
			LEFT JOIN branch br on br.id_branch = sr.id_branch
			LEFT JOIN ret_stone s on s.stone_id = sr.stone_id
			LEFT JOIN ret_uom u on u.uom_id = sr.uom_id
			LEFT JOIN ret_quality_code qc on qc.quality_id = sr.quality_id
			WHERE sr.id_stone_rate is not null and sr.active=1 and sr.type=".$data['type']."
			".($data['stone_type']!='' && $data['stone_type']>0 ? "and sr.stone_type=".$data['stone_type']."":"")."
			".($data['stone_id']!='' && $data['stone_id']>0 ? "and sr.stone_id=".$data['stone_id']."":"")."
			".($data['quality_code']!='' && $data['quality_code']>0 ? "and sr.quality_id=".$data['quality_code']."":"")."
			".($data['id_branch']!='' && $data['id_branch']>0 ? "and sr.id_branch=".$data['id_branch']."":"")."");
		}

		else
		{
			$sql = $this->db->query("SELECT sr.id_stone_rate,sr.id_branch,sr.id_product,sr.id_design,sr.id_sub_design,
			br.name as branch,pro.product_name,des.design_name,subdes.sub_design_name,
			ifnull(sr.uom_id,'') as uom_id,sr.stone_calc_type,ifnull(sr.quality_id,'') as quality_id,
			ifnull(sr.from_wt,0) as from_cent,ifnull(sr.to_wt,0) as to_cent,ifnull(sr.min_rate,0) as min_rate,
			ifnull(sr.max_rate,0) as max_rate,u.uom_name,ifnull(qc.code,'-') as quality_code,if(sr.stone_calc_type=1,'Wt','PCS') as stone_calc,sr.type
			from ret_stone_rate_settings sr
			LEFT JOIN branch br on br.id_branch = sr.id_branch
			LEFT JOIN ret_product_master pro on pro.pro_id = sr.id_product
			LEFT JOIN ret_design_master des on des.design_no = sr.id_design
			LEFT JOIN ret_sub_design_master subdes on subdes.id_sub_design = sr.id_sub_design
			LEFT JOIN ret_uom u on u.uom_id = sr.uom_id
			LEFT JOIN ret_quality_code qc on qc.quality_id = sr.quality_id
			where sr.id_stone_rate is not null and sr.active=1 and sr.type = ".$data['type']."
			".($data['id_product']!='' && $data['id_product']>0 ? "and sr.id_product=".$data['id_product']."":"")."
			".($data['id_design']!='' && $data['id_design']>0 ? "and sr.id_design=".$data['id_design']."":"")."
			".($data['id_sub_design']!='' && $data['id_sub_design']>0 ? "and sr.id_sub_design=".$data['id_sub_design']."":"")."
			".($data['quality_code']!='' && $data['quality_code']>0 ? "and sr.quality_id=".$data['quality_code']."":"")."
			".($data['id_branch']!='' && $data['id_branch']>0 ? "and sr.id_branch=".$data['id_branch']."":"")."");
		}

		//print_R($this->db->last_query());exit;
		return $sql->result_array();
	}

	function get_Stone_min_max_rate($id_stone_rate)
	{
		$sql = $this->db->query("SELECT sr.id_stone_rate,sr.id_branch,sr.stone_type,br.name as branch,
		sr.stone_id,sr.uom_id,sr.stone_type,s.stone_name,
		u.uom_name,ifnull(qc.code,'-') as quality_code,sr.stone_calc_type,
		ifnull(sr.quality_id,'') as quality_id,ifnull(sr.from_wt,0) as from_cent,ifnull(sr.to_wt,0) as to_cent,ifnull(sr.min_rate,0) as min_rate,ifnull(sr.max_rate,0) as max_rate
		FROM ret_stone_rate_settings sr
		LEFT join ret_stone_type st on  st.id_stone_type = sr.stone_type
		LEFT JOIN branch br on br.id_branch = sr.id_branch
		LEFT JOIN ret_stone s on s.stone_id = sr.stone_id
		LEFT JOIN ret_uom u on u.uom_id = sr.uom_id
		LEFT JOIN ret_quality_code qc on qc.quality_id = sr.quality_id
		WHERE sr.id_stone_rate = ".$id_stone_rate."");
		// print_r($this->db->last_query());exit;
		return $sql->result_array();
	}

	function get_product_min_max_rate($id_stone_rate)
	{
		$sql = $this->db->query("SELECT sr.id_stone_rate,sr.id_branch,sr.id_product,sr.id_design,sr.id_sub_design,
		br.name as branch,pro.product_name,des.design_name,subdes.sub_design_name,
		ifnull(sr.uom_id,'') as uom_id,sr.stone_calc_type,ifnull(sr.quality_id,'') as quality_id,
		ifnull(sr.from_wt,0) as from_cent,ifnull(sr.to_wt,0) as to_cent,ifnull(sr.min_rate,0) as min_rate,ifnull(sr.max_rate,0) as max_rate,
		pro.stone_type
		from ret_stone_rate_settings sr
		LEFT JOIN branch br on br.id_branch = sr.id_branch
		LEFT JOIN ret_product_master pro on pro.pro_id = sr.id_product
		LEFT JOIN ret_design_master des on des.design_no = sr.id_design
		LEFT JOIN ret_sub_design_master subdes on subdes.id_sub_design = sr.id_sub_design
		where sr.id_stone_rate =".$id_stone_rate."");
		//print_r($this->db->last_query());exit;
		return $sql->result_array();
	}


	function getStoneRateSettings()
	{
		$sql = $this->db->query("SELECT sr.id_stone_rate,sr.id_branch,sr.stone_type,sr.stone_id,
		ifnull(sr.uom_id,'') as uom_id,sr.stone_calc_type,ifnull(sr.quality_id,'') as quality_id,
		ifnull(sr.from_wt,0) as from_cent,ifnull(sr.to_wt,0) as to_cent,ifnull(sr.min_rate,0) as min_rate,ifnull(sr.max_rate,0) as max_rate
		from ret_stone_rate_settings sr
		where sr.id_stone_rate is not null and sr.active=1 and sr.type=1");


		return $sql->result_array();
	}

	function checkStoneRateExist($id_branch,$stone_type,$stone_id,$quality_id="",$stone_calc_type,$uom_id,$from_wt="",$to_wt="")
	{
		$sql = $this->db->query("SELECT id_stone_rate from ret_stone_rate_settings
		where type=1 and id_branch=".$id_branch."
		and stone_type=".$stone_type."
		and stone_id=".$stone_id."
		and uom_id=".$uom_id."
		and stone_calc_type=".$stone_calc_type."
		".($quality_id!="" ? "and quality_id=".$quality_id."":"")."
		".($from_wt!='' && $from_wt>0 ? "and from_wt <= ".$from_wt." and to_wt >= ".$from_wt."":"")."
		".($to_wt!='' && $to_wt>0 ? "and from_wt <= ".$to_wt." and to_wt >= ".$to_wt."":"")."");
		//print_r($this->db->last_query());exit;
		return $sql->result_array();
	}

	function getLooseStonesProduct()
	{
		$sql = $this->db->query("SELECT pro.pro_id,pro.product_name,pro.stone_type
		FROM ret_product_master pro
		WHERE pro.stone_type!=0");

		return $sql->result_array();
	}


	function getLooseStoneProductRateSettings()
	{
		$sql = $this->db->query("SELECT sr.id_stone_rate,sr.id_branch,sr.id_product,sr.id_design,sr.id_sub_design,
		br.name as branch,pro.product_name,des.design_name,subdes.sub_design_name,
		ifnull(sr.uom_id,'') as uom_id,sr.stone_calc_type,ifnull(sr.quality_id,'') as quality_id,
		ifnull(sr.from_wt,0) as from_cent,ifnull(sr.to_wt,0) as to_cent,ifnull(sr.min_rate,0) as min_rate,ifnull(sr.max_rate,0) as max_rate
		from ret_stone_rate_settings sr
		LEFT JOIN branch br on br.id_branch = sr.id_branch
		LEFT JOIN ret_product_master pro on pro.pro_id = sr.id_product
		LEFT JOIN ret_design_master des on des.design_no = sr.id_design
		LEFT JOIN ret_sub_design_master subdes on subdes.id_sub_design = sr.id_sub_design
		where sr.id_stone_rate is not null and sr.active=1 and sr.type = 2");

		return $sql->result_array();
	}


	function checkProductStoneRateExist($id_branch,$id_product,$id_design,$id_sub_design,$quality_id="",$stone_calc_type,$uom_id,$from_wt="",$to_wt="")
	{
		$sql = $this->db->query("SELECT id_stone_rate from ret_stone_rate_settings
		where type=2 and id_branch=".$id_branch."
		and id_product=".$id_product."
		and id_design=".$id_design."
		and id_sub_design=".$id_sub_design."
		and uom_id=".$uom_id."
		and stone_calc_type=".$stone_calc_type."
		".($quality_id!="" ? "and quality_id=".$quality_id."":"")."
		".($from_wt!='' && $from_wt>0 ? "and from_wt <= ".$from_wt." and to_wt >= ".$from_wt."":"")."
		".($to_wt!='' && $to_wt>0 ? "and from_wt <= ".$to_wt." and to_wt >= ".$to_wt."":"")."");
		//print_r($this->db->last_query());exit;
		return $sql->result_array();
	}

	function GetFinancialYear()

	{

		$sql=$this->db->query("SELECT fin_year_code,fin_status,fin_year_name From ret_financial_year");

		return $sql->result_array();

	}

	function get_deviceNames()
	{
	    $sql = $this->db->query("SELECT id_device, device_name, device_type FROM `ret_bill_pay_device` WHERE status = 1");
	    return $sql->result_array();
	}



	function get_reorder_entry_records($branch,$sub_design,$product,$design,$section,$weight_range,$max_pcs,$min_pcs,$size)

	{
				$multiple_branch = implode(' , ', $branch);

				if($multiple_branch != '')
				{
					$id_branch = $multiple_branch;
				}else{
					$id_branch = $branch;
				}

				$multiple_subdesign = implode(' , ', $sub_design);

				if($multiple_subdesign != '')
				{
					$id_sub_design = $multiple_subdesign;
				}else{
					$id_sub_design = $sub_design;
				}

			$sql=$this->db->query("SELECT s.id_reorder_settings,s.id_branch,s.id_product,s.id_design,concat(ret_s.value,' ',ret_s.name) as size,s.id_wt_range as wt_range,s.min_pcs,s.max_pcs,b.name as branch_name,

			d.design_name,p.product_name,concat(w.value,m.uom_name) as wt_name,ret_s.id_size,s.id_section,

			s.id_sub_design,sub.sub_design_name,r.section_name

				from ret_reorder_settings s

				LEFT join branch b on b.id_branch=s.id_branch

				LEFT join ret_product_master p on p.pro_id=s.id_product

				LEFT join ret_design_master d on d.design_no=s.id_design

				LEFT join ret_sub_design_master sub on sub.id_sub_design=s.id_sub_design

				LEFT join ret_weight w on w.id_weight=s.id_wt_range

				LEFT JOIN ret_size ret_s on ret_s.id_size=s.size

			   left join ret_section r on r.id_section=s.id_section


				LEFT JOIN ret_uom m on m.uom_id=w.id_uom

				where s.id_reorder_settings is not null

			   ".($id_branch!='' && $id_branch !='0' ? " and s.id_branch in (".$id_branch.") " :'' )."

               ".($id_sub_design!='' && $id_sub_design !='0' ? " and s.id_sub_design in (".$id_sub_design.") " :'' )."

				".($product!='' ? " and s.id_product=".$product."" :'')."

				".($design!='' ? " and s.id_design=".$design."" :'')."

				".($section!='' ? " and s.id_section=".$section."" :'')."

				".($weight_range!='' ? " and s.id_wt_range=".$weight_range."" :'')."

				".($size!='' ? " and s.size=".$size."" :'')."

				ORDER by s.id_branch");

			//print_r($this->db->last_query());exit;

			return $sql->row_array();

	}


	function ajax_dayclose()

	{

		$sql=$this->db->query("SELECT id_day_closing, b.id_branch,b.name branch_name, IF(type=1,'Auto','Manual') dayclose_type, day_close_time, IF(is_day_closed=0,'No','Yes') is_day_closed_status,is_day_closed, entry_date FROM ret_day_closing d
		LEFT JOIN branch b ON b.id_branch=d.id_branch order by id_day_closing ASC");

		return $sql->result_array();

	}

	function get_cover_up_list($from_date,$to_date){

		$sql=$this->db->query('SELECT c.id_coverup,c.weight,date_format(c.created_on,"%d-%m-%Y") as created_on,m.metal FROM ret_cover_up c
		LEFT JOIN metal m ON m.id_metal = c.id_metal
		where (date(c.created_on) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

		return $sql->result_array();

	}

	function get_coverup_list_edit($id_coverup){

		$sql=$this->db->query('SELECT c.id_coverup,c.weight,date_format(c.created_on,"%d-%m-%Y") as created_on,m.metal FROM ret_cover_up c
		LEFT JOIN metal m ON m.id_metal = c.id_metal
		where id_coverup = '.$id_coverup);

		return $sql->row_array();

	}

	function GetBranches()
	{
		$sql = $this->db->query("SELECT br.id_branch,br.name
		FROM branch br
		WHERE br.active = 1");
		
		return $sql->result_array();
	}

	function ajax_get_cancel_reason()

    {

        $sql=$this->db->query("select * FROM ret_qc_cancel_reason ");

        return $sql->result_array();

    }
	function get_cancel_reason($id)

    {

        $sql=$this->db->query("select * FROM ret_qc_cancel_reason where id_cancel ='".$id."'");

        return $sql->row_array();

    }


		//Product grouping
		function ajax_get_productgrp_name()
		{
			$pro_grp = $this->db->query("SELECT id_prod_group ,pg.group_metal,m.metal,group_name,status
			from ret_product_grouping pg 
			left join metal m on m.id_metal=pg.group_metal");
			//print_r($this->db->last_query());
			return $pro_grp->result_array();
		}
	
	
	
		function get_prodgrp($id_prod_group)
		{
			$id_prod_group  = $this->db->query("select * from ret_product_grouping where id_prod_group =".$id_prod_group);
			//print_r($this->db->last_query());
			return $id_prod_group->row_array();
		}
	
	
	
		function get_product_group($id)
		{
			$sql=$this->db->query("select id_prod_group,group_name FROM ret_product_grouping where STATUS=1 and group_metal=".$id);
			//print_r($this->db->last_query());
			return $sql->result_array();
		}
		//End of product grouping

		function getItemsinTagDetails($id_metal="",$id_cat="",$id_prod="",$id_des="",$id_sub_des="",$id_purity="")
		{
			$sql = $this->db->query("Select t.tag_id,t.tag_code 
			from ret_taging t
			left join ret_product_master p on p.pro_id = t.product_id
			left join ret_category cat on cat.id_ret_category = p.cat_id
			where t.tag_id is not null
			".($id_metal!='' ? "and cat.id_metal=".$id_metal."" : "" )."
			".($id_cat!='' ? "and cat.id_ret_category=".$id_cat."" : "")."
			".($id_prod!='' ? "and t.product_id = ".$id_prod."" : "")."
			".($id_des!='' ? "and t.design_id = ".$id_des."" : "")."
			".($id_sub_des!='' ? "and t.id_sub_design = ".$id_sub_des."" : "")."
			".($id_purity!='' ? "and t.purity = ".$id_purity."" : "")."");
			//print_r($this->db->last_query());exit;
			if($sql->num_rows()>0)
			{
				return 1;
			}else
			{
				return 0;
			}
		}

		function check_token_duplicates($token_id){

			$sql = $this->db->query("Select * from web_registered_devices where token_id = '".($token_id)."'");
			if($sql->num_rows()>0)
			{
				$return_data=array('status'=>1);
			}else{
				$return_data=array('status'=>0);
			}
			return $return_data;

		}

		function ajax_get_stn_disc(){

			$sql=$this->db->query("SELECT d.id,IFNULL(st.stone_type,'') AS stone_type,IFNULL(d.max_disc_per,0) AS max_disc_per,
			IFNULL(d.min_disc_per,0) AS min_disc_per,d.id_stone_type,d.status
			FROM ret_stone_discount_master d
			LEFT JOIN ret_stone_type st ON st.id_stone_type = d.id_stone_type");

			$result = $sql->result_array();

			return $result;
		}

		function get_stn_disc($id)

    {

		$id = $this->db->query("select id_stone_type,min_disc_per,max_disc_per,status from ret_stone_discount_master
		 where id=".$id);

		return $id->row_array();

	}

	function check_wastage_range($from_wastage,$to_wastage,$value,$id_metal)
	{

		$sql=$this->db->query("SELECT  va.id_wastage,va.from_range,va.to_range ,va.value,va.id_metal from  ret_wastage_discount_master va

		 where va.from_range and  va.to_range is not null

		 ".($id_metal!='' ? " and va.id_metal =".$id_metal."" :'')."

		 AND ((va.from_range <=".$from_wastage." and va.to_range >= ".$from_wastage.") OR (va.from_range <= ".$to_wastage." and va.to_range >= ".$to_wastage."))");

		return $sql->num_rows();

	}
	function ajax_get_wastage($id_metal)
	{

		$sql=$this->db->query("SELECT  va.id_wastage,va.from_range,va.to_range ,va.value,va.id_metal,m.metal AS metal_name 
		
		FROM  ret_wastage_discount_master va

		LEFT JOIN metal m ON m.id_metal = va.id_metal

		 WHERE va.from_range IS NOT NULL AND va.to_range IS NOT NULL 		 
		 
		 ".($id_metal!='' ? " and va.id_metal =".$id_metal."" :'')."

		 ");

		return $sql->result_array();

	}
	function get_wastage($id)
	{

		$sql=$this->db->query("SELECT  va.id_wastage,va.from_range,va.to_range ,va.value,va.id_metal,m.metal AS metal_name 
		
		FROM  ret_wastage_discount_master va

		LEFT JOIN metal m ON m.id_metal = va.id_metal

		 WHERE va.id_wastage =".$id."

		 ");

		return $sql->row_array();

		}


	/**
	 * Get employees who have stock entries for a given branch and date
	 */
	function get_employees_by_date($id_branch, $date){
		$fromDt = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
		$sql = $this->db->query("
			SELECT DISTINCT
				e.id_employee,
				CONCAT(e.emp_code, '-', e.firstname, ' ', IFNULL(e.lastname,'')) as emp_name
			FROM ret_employee_stock res
			INNER JOIN employee e ON e.id_employee = res.id_emp
			WHERE res.date = '" . $this->db->escape_str($fromDt) . "'
			" . ($id_branch != '' ? " AND res.id_branch = " . $this->db->escape_str($id_branch) : "") . "
			AND e.active = 1
			ORDER BY e.firstname
		");
		return $sql->result_array();
	}

	function get_employee_stock_product(){

		$sql=$this->db->query('select emp.firstname,rp.pcs,br.name,re.date,rp.id_emp_stock from ret_employee_stock re
		left join ret_employee_stock_product rp on rp.id_emp_stock=re.id_emp_stock
		left join branch br on br.id_branch =re.id_branch
		left join employee emp on emp.id_employee=re.id_emp');

		return $sql->result_array();
	}


	function getProductBySection($id_branch = '', $id_emp = '', $date = '', $product_type = '1'){

		$branch_filter = '';
		if($id_branch != '') {
			$branch_filter = " AND tag.current_branch = ".$id_branch;
		}

		// Build LEFT JOIN to get this employee's previously entered pcs for the given date
		$emp_join = '';
		$emp_pcs_field = 'IFNULL(rep.pcs, 0) as pcs';
		$emp_updated_fields = 'rep.is_updated, rep.updated_on';

		if ($id_emp != '' && $date != '') {
			$fromDt = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
			$emp_join = "
				LEFT JOIN ret_employee_stock res_emp
					ON res_emp.id_emp = " . $this->db->escape_str($id_emp) . "
					AND res_emp.id_branch = " . ($id_branch != '' ? $this->db->escape_str($id_branch) : 'res_emp.id_branch') . "
					AND res_emp.date = '" . $fromDt . "'
				LEFT JOIN ret_employee_stock_product rep
					ON rep.id_emp_stock = res_emp.id_emp_stock
					AND rep.id_product = rp.pro_id
					AND rep.id_section = rc.id_section
			";
		} else {
			$emp_join = "LEFT JOIN ret_employee_stock_product rep ON rep.id_product = rp.pro_id AND rep.id_section = rc.id_section";
		}

		$result = $this->db->query("
			SELECT
				rp.pro_id,
				rp.product_name,
				IFNULL(rep.pcs, 0) as pcs,
				IFNULL(rep.is_updated, 0) as is_updated,
				rc.section_name,
				rep.updated_on,
				rc.id_section,
				COUNT(CASE WHEN tag.tag_status = 0 THEN 1 END) as stock_count
			FROM ret_product_master rp
			INNER JOIN ret_category cat ON cat.id_ret_category = rp.cat_id
			LEFT JOIN ret_taging tag ON tag.product_id = rp.pro_id
			LEFT JOIN ret_section rc ON rc.id_section = tag.id_section
			" . $emp_join . "
			WHERE rc.id_section IS NOT NULL 
			  AND rp.stock_type = " . $this->db->escape_str($product_type) . "
			" . $branch_filter . "
			" . ($this->input->post('id_metal') != '' ? " AND cat.id_metal = " . $this->db->escape_str($this->input->post('id_metal')) : "") . "
			GROUP BY rp.pro_id, rc.id_section
			HAVING stock_count > 0
		");

		return $result->result_array();
	}

	/**
	 * Get unique products (flat list, no section grouping) for product-wise stock entry (stock_audit_by = 1)
	 * Groups by pro_id only — each product appears once regardless of how many sections it exists in
	 */
	function getProductFlat($id_branch = '', $id_emp = '', $date = '', $product_type = '1'){

		$branch_filter = '';
		if($id_branch != '') {
			$branch_filter = " AND tag.current_branch = " . $this->db->escape_str($id_branch);
		}

		// Subqueries for previously entered pieces (sum across all sections for this product)
		$prev_pcs = '0';
		$prev_is_updated = '0';
		$prev_updated_on = 'NULL';

		if ($id_emp != '' && $date != '') {
			$fromDt = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
			$prev_pcs = "(
				SELECT IFNULL(SUM(esp.pcs), 0)
				FROM ret_employee_stock_product esp
				INNER JOIN ret_employee_stock es ON es.id_emp_stock = esp.id_emp_stock
				WHERE es.id_emp    = " . $this->db->escape_str($id_emp) . "
				  AND es.id_branch = " . $this->db->escape_str($id_branch) . "
				  AND es.date      = '" . $fromDt . "'
				  AND esp.id_product = rp.pro_id
			)";
			$prev_is_updated = "(
				SELECT IFNULL(MAX(esp.is_updated), 0)
				FROM ret_employee_stock_product esp
				INNER JOIN ret_employee_stock es ON es.id_emp_stock = esp.id_emp_stock
				WHERE es.id_emp    = " . $this->db->escape_str($id_emp) . "
				  AND es.id_branch = " . $this->db->escape_str($id_branch) . "
				  AND es.date      = '" . $fromDt . "'
				  AND esp.id_product = rp.pro_id
			)";
			$prev_updated_on = "(
				SELECT MAX(esp.updated_on)
				FROM ret_employee_stock_product esp
				INNER JOIN ret_employee_stock es ON es.id_emp_stock = esp.id_emp_stock
				WHERE es.id_emp    = " . $this->db->escape_str($id_emp) . "
				  AND es.id_branch = " . $this->db->escape_str($id_branch) . "
				  AND es.date      = '" . $fromDt . "'
				  AND esp.id_product = rp.pro_id
			)";
		}

		$result = $this->db->query("
			SELECT
				rp.pro_id,
				rp.product_name,
				IFNULL(" . $prev_pcs . ", 0) as pcs,
				IFNULL(" . $prev_is_updated . ", 0) as is_updated,
				" . $prev_updated_on . " as updated_on,
				COUNT(CASE WHEN tag.tag_status = 0 THEN 1 END) as stock_count
			FROM ret_product_master rp
			INNER JOIN ret_category cat ON cat.id_ret_category = rp.cat_id
			LEFT JOIN ret_taging tag ON tag.product_id = rp.pro_id
			WHERE tag.id_section IS NOT NULL 
			  AND rp.stock_type = " . $this->db->escape_str($product_type) . "
			" . $branch_filter . "
			" . ($this->input->post('id_metal') != '' ? " AND cat.id_metal = " . $this->db->escape_str($this->input->post('id_metal')) : "") . "
			GROUP BY rp.pro_id
			HAVING stock_count > 0
			ORDER BY rp.product_name
		");
		return $result->result_array();
	}

	function get_employee_stock_products($emp,$branch,$from_date){

		$sql=$this->db->query("select emp.firstname,rp.pcs,br.name,re.date,rp.id_emp_stock,rp.id_product from ret_employee_stock re
		left join ret_employee_stock_product rp on rp.id_emp_stock=re.id_emp_stock
		left join branch br on br.id_branch =re.id_branch
		left join employee emp on emp.id_employee=re.id_emp
		where 
		re.id_branch=".$branch." and 
		re.id_emp=".$emp." and
		re.date='".$from_date."'
		
		");
	
		return $sql->result_array();
	}

	function get_employee_stock_product_details($emp,$product,$id_section){
		$sql=$this->db->query("select emp.firstname,rp.pcs,br.name,re.date,rp.id_emp_stock,rp.id_product from ret_employee_stock re
		left join ret_employee_stock_product rp on rp.id_emp_stock=re.id_emp_stock
		left join branch br on br.id_branch =re.id_branch
		left join employee emp on emp.id_employee=re.id_emp
		where 
		re.id_emp_stock=".$emp." and
		rp.id_product='".$product."' and
		rp.id_section='".$id_section."'
		
		");

		return $sql->result_array();
	}

	public function updateWhereData($data, $where_conditions, $table)
	{
		foreach ($where_conditions as $key => $value) {
			$this->db->where($key, $value);
		}
		$this->db->update($table, $data);
	}

	/**
	 * Get all employee entries for a specific product/section/date — for the entry log modal
	 */
	public function get_employee_entry_log($product_id, $id_section, $id_branch, $date)
	{
		$FromDt = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
		$sql = $this->db->query("
			SELECT
				emp.firstname AS employee_name,
				IFNULL(esp.pcs, 0) AS pcs,
				IFNULL(esp.weight, 0) AS weight,
				esp.product_type,
				esp.created_on,
				br.name AS branch_name
			FROM ret_employee_stock_product esp
			LEFT JOIN ret_employee_stock es ON es.id_emp_stock = esp.id_emp_stock
			LEFT JOIN employee emp ON emp.id_employee = es.id_emp
			LEFT JOIN branch br ON br.id_branch = es.id_branch
			WHERE esp.id_product = '" . $this->db->escape_str($product_id) . "'
			  AND esp.id_section = '" . $this->db->escape_str($id_section) . "'
			  AND es.date = '" . $FromDt . "'
			  " . ($id_branch != '' ? " AND es.id_branch = '" . $this->db->escape_str($id_branch) . "'" : '') . "
			ORDER BY esp.created_on ASC
		");
		return $sql->result_array();
	}

	/**
	 * Get non-tagged products grouped by section for employee stock entry
	 * Uses ret_product_master where stock_type = 2 (non-tagged) with positive stock
	 */
	public function getProductBySection_NonTag($id_branch, $id_emp = '', $date = '', $product_type = '2')
	{
		// Build subquery to get previously entered weight for this employee on this date
		$entered_wt_sub = 'NULL';
		$entered_is_updated = '0';
		$entered_updated_on = 'NULL';
		if ($id_emp != '' && $date != '') {
			$fromDt = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
			$entered_wt_sub = "(
				SELECT esp.weight
				FROM ret_employee_stock_product esp
				INNER JOIN ret_employee_stock es ON es.id_emp_stock = esp.id_emp_stock
				WHERE es.id_emp     = " . $this->db->escape_str($id_emp) . "
				  AND es.id_branch  = " . ($id_branch != '' ? $this->db->escape_str($id_branch) : 'es.id_branch') . "
				  AND es.date       = '" . $fromDt . "'
				  AND esp.id_product = p.pro_id
				LIMIT 1
			)";
			$entered_is_updated = "(
				SELECT IFNULL(esp.is_updated,0)
				FROM ret_employee_stock_product esp
				INNER JOIN ret_employee_stock es ON es.id_emp_stock = esp.id_emp_stock
				WHERE es.id_emp    = " . $this->db->escape_str($id_emp) . "
				  AND es.id_branch = " . ($id_branch != '' ? $this->db->escape_str($id_branch) : 'es.id_branch') . "
				  AND es.date      = '" . $fromDt . "'
				  AND esp.id_product = p.pro_id
				LIMIT 1
			)";
			$entered_updated_on = "(
				SELECT esp.updated_on
				FROM ret_employee_stock_product esp
				INNER JOIN ret_employee_stock es ON es.id_emp_stock = esp.id_emp_stock
				WHERE es.id_emp    = " . $this->db->escape_str($id_emp) . "
				  AND es.id_branch = " . ($id_branch != '' ? $this->db->escape_str($id_branch) : 'es.id_branch') . "
				  AND es.date      = '" . $fromDt . "'
				  AND esp.id_product = p.pro_id
				LIMIT 1
			)";
		}

		$branch_filter = ($id_branch != '') ? " AND m1.to_branch = '" . $this->db->escape_str($id_branch) . "'" : '';

		$sql = $this->db->query("
			SELECT
				p.pro_id,
				p.product_name,
				IFNULL(sec.section_name, 'No Section') AS section_name,
				p.id_section,
				IFNULL(
					(SELECT SUM(IFNULL(ni.no_of_piece, 0))
					 FROM ret_nontag_item ni
					 WHERE ni.product = p.pro_id
					   AND ni.branch = '" . $this->db->escape_str($id_branch) . "'
					), 0
				) AS stock_pcs,
				IFNULL(
					(SELECT SUM(IFNULL(ni.gross_wt, 0))
					 FROM ret_nontag_item ni
					 WHERE ni.product = p.pro_id
					   AND ni.branch = '" . $this->db->escape_str($id_branch) . "'
					), 0
				) AS stock_gwt,
				IFNULL(" . $entered_wt_sub . ", 0)       AS entered_wt,
				IFNULL(" . $entered_is_updated . ", 0)   AS is_updated,
				" . $entered_updated_on . "               AS updated_on
			FROM ret_product_master p
			INNER JOIN ret_category cat ON cat.id_ret_category = p.cat_id
			INNER JOIN ret_section sec ON sec.id_section = p.id_section AND sec.status = 1
			WHERE p.stock_type = " . $this->db->escape_str($product_type) . "
			  AND p.id_section IS NOT NULL AND p.id_section > 0
			  " . ($this->input->post('id_metal') != '' ? " AND cat.id_metal = " . $this->db->escape_str($this->input->post('id_metal')) : "") . "
			GROUP BY p.pro_id
			HAVING stock_gwt > 0
			ORDER BY sec.section_name, p.product_name
		");
		return $sql->result_array();
	}

	/**
	 * Section-based stock entry: return sections with stock counts and previously entered data
	 */
	public function getSectionListForStock($id_branch, $id_emp = '', $date = '')
	{
		$branch_filter = '';
		if ($id_branch != '') {
			$branch_filter = " AND tag.current_branch = " . $this->db->escape_str($id_branch);
		}

		// Previous entry subqueries
		$prev_pcs = '0';
		$prev_wt  = '0';
		$prev_is_updated = '0';
		$prev_updated_on = 'NULL';

		if ($id_emp != '' && $date != '') {
			$fromDt = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
			$prev_pcs = "(
				SELECT IFNULL(SUM(esp.pcs), 0)
				FROM ret_employee_stock_product esp
				INNER JOIN ret_employee_stock es ON es.id_emp_stock = esp.id_emp_stock
				WHERE es.id_emp    = " . $this->db->escape_str($id_emp) . "
				  AND es.id_branch = " . $this->db->escape_str($id_branch) . "
				  AND es.date      = '" . $fromDt . "'
				  AND esp.id_section = sec.id_section
				  AND esp.id_product = 0
			)";
			$prev_wt = "(
				SELECT IFNULL(SUM(esp.weight), 0)
				FROM ret_employee_stock_product esp
				INNER JOIN ret_employee_stock es ON es.id_emp_stock = esp.id_emp_stock
				WHERE es.id_emp    = " . $this->db->escape_str($id_emp) . "
				  AND es.id_branch = " . $this->db->escape_str($id_branch) . "
				  AND es.date      = '" . $fromDt . "'
				  AND esp.id_section = sec.id_section
				  AND esp.id_product = 0
			)";
			$prev_is_updated = "(
				SELECT IFNULL(MAX(esp.is_updated), 0)
				FROM ret_employee_stock_product esp
				INNER JOIN ret_employee_stock es ON es.id_emp_stock = esp.id_emp_stock
				WHERE es.id_emp    = " . $this->db->escape_str($id_emp) . "
				  AND es.id_branch = " . $this->db->escape_str($id_branch) . "
				  AND es.date      = '" . $fromDt . "'
				  AND esp.id_section = sec.id_section
				  AND esp.id_product = 0
			)";
			$prev_updated_on = "(
				SELECT MAX(esp.updated_on)
				FROM ret_employee_stock_product esp
				INNER JOIN ret_employee_stock es ON es.id_emp_stock = esp.id_emp_stock
				WHERE es.id_emp    = " . $this->db->escape_str($id_emp) . "
				  AND es.id_branch = " . $this->db->escape_str($id_branch) . "
				  AND es.date      = '" . $fromDt . "'
				  AND esp.id_section = sec.id_section
				  AND esp.id_product = 0
			)";
		}

		$sql = $this->db->query("
			SELECT
				sec.id_section,
				sec.section_name,
				COUNT(CASE WHEN tag.tag_status = 0 THEN 1 END) AS stock_count,
				IFNULL(" . $prev_pcs . ", 0) AS prev_pcs,
				IFNULL(" . $prev_wt  . ", 0) AS prev_wt,
				IFNULL(" . $prev_is_updated . ", 0) AS is_updated,
				" . $prev_updated_on . " AS updated_on
			FROM ret_section sec
			LEFT JOIN ret_taging tag ON tag.id_section = sec.id_section
				AND tag.tag_status = 0
				" . $branch_filter . "
			LEFT JOIN ret_product_master rp ON rp.pro_id = tag.product_id
			LEFT JOIN ret_category cat ON cat.id_ret_category = rp.cat_id
			WHERE sec.status = 1
			  " . ($this->input->post('id_metal') != '' ? " AND cat.id_metal = " . $this->db->escape_str($this->input->post('id_metal')) : "") . "
			GROUP BY sec.id_section
			HAVING stock_count > 0
			ORDER BY sec.section_name
		");
		return $sql->result_array();
	}

	/**
	 * Get previously entered section-level stock product detail
	 */
	public function get_employee_stock_section_detail($id_emp_stock, $id_section)
	{
		$sql = $this->db->query("
			SELECT * FROM ret_employee_stock_product
			WHERE id_emp_stock = " . $this->db->escape_str($id_emp_stock) . "
			  AND id_section   = " . $this->db->escape_str($id_section) . "
			  AND id_product   = 0
		");
		return $sql->result_array();
	}

}


?>