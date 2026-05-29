<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_catalog extends CI_Controller {
	
	const WALL_MODEL = "Catalog_model";
	const CUS_MODEL = "Customer_model";
	const SET_MODEL = "Admin_settings_model";
	const SMS_MODEL = "admin_usersms_model";
	const ADM_MODEL = "chitadmin_model";
	const LOG_MODEL = "log_model";
	const MAIL_MODEL = "email_model";
		
	public function __construct()
    {
        parent::__construct();
        if(!$this->session->userdata('is_logged'))
        {
			redirect('admin/login');
		}
		$this->load->model(self::WALL_MODEL);
		$this->load->model(self::ADM_MODEL);
		$this->load->model(self::SET_MODEL);
		$this->load->model(self::SMS_MODEL);
		$this->load->model(self::LOG_MODEL);
		$this->load->model(self::MAIL_MODEL);
		$this->id_log =  $this->session->userdata('id_log');
	}
	
	
    // Catalog catogery Detalis //


	function catagory_detalis($type="",$id="")
	{
		
		$model = self::WALL_MODEL;
		$set_model = self::SET_MODEL;
		$log_model = self::LOG_MODEL;
		switch($type){
			case "List":
					
			    $data['catagory']=$this->$model->category_settingDB('get',($id!=NULL?$id:''));    
				$data['main_content'] = "catalog/category/list" ;
	 			$this->load->view('layout/template', $data);   
				break;
			case "View":
			      if($id!=NULL)
			      {
					$data['category'] = $this->$model->category_settingDB('get',$id);
				  }
				  else
				  {
				  	$data['category'] = $this->$model->category_settingDB();					
				  }
				  $data['main_content'] = "catalog/category/form" ;
	 			  $this->load->view('layout/template', $data);  
				break;
			case "Save":
			       
				   $category = $this->input->post('category');
				   
				     //formatting form values
			        $insertData=array( 
			                           'id_parent'      => (isset($category['id_parent']) && $category['id_parent']!=''? $category['id_parent'] == 0 ? "1" : $category['id_parent']:NULL),
			                            'categoryname' 	 => (isset($category['category_name']) && $category['category_name']!=''? $category['category_name']:NULL),
			                            'description'       => (isset($category['description']) && $category['description']!=''? $category['description']:NULL),
										'catimage'       => (isset($category['catimage']) && $category['catimage']!=''? $category['catimage']:NULL),
			                            'active'		 => (isset($category['active']) && $category['active']!=''? $category['active']:0)
									); 
			         
			       //inserting data  
                              
			        $this->db->trans_begin();

	 				$status = $this->$model->category_settingDB("insert","",$insertData);
					if(isset($_FILES['category']['name']))	
			            {
						   if($status>0)
						   {
							    $this->set_catimage($status);
						   }
						}
		 			if($this->db->trans_status()===TRUE)
		             {
                       $this->db->trans_commit();

					 	$this->session->set_flashdata('chit_alert', array('message' => 'Catalog Category successfully','class' => 'success','title'=>'Category List'));
					 }
					 else
					 {
                        $this->db->trans_rollback();						 	
					 	$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'Category List'));
					 }
					redirect('catalog/category/list');               
			      break;
			case "Update":
			        //get form values
			       $category=$this->input->post('category');
			       
			       //formatting form values
			      $updateData=array(   
			                            'id_parent'      => (isset($category['id_parent']) && $category['id_parent']!=''? $category['id_parent']:NULL),
			                            'categoryname' 	 => (isset($category['category_name']) && $category['category_name']!=''? $category['category_name']:NULL),
			                            'description'       => (isset($category['description']) && $category['description']!=''? $category['description']:NULL),
			                            'active'		 => (isset($category['active']) && $category['active']!=''? $category['active']:0)          
			                         );    
                   					
			       //update data                  
			        $this->db->trans_begin();                
			       $status = $this->$model->category_settingDB("update",$id,$updateData);
			       
			        if(isset($_FILES['category']['name']['catimage']) && $_FILES['category']['name']['catimage'] !='')	
			            {
							
						   if($status>0)
						   {
							    $this->set_catimage($status);
						   }
						}
						
		 			if($this->db->trans_status()===TRUE)
		             {
                       $this->db->trans_commit();

					 	$this->session->set_flashdata('chit_alert',array('message'=>'Category Detalis Updated successfully','class'=>'success','title'=>'Category List'));
					 }
					 else
					 {
                        $this->db->trans_rollback();						 	
					 	$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Category List'));
					 }
					redirect('catalog/category/list'); 
			      break;
              case 'Delete':
				 	      $status = $this->$model->category_settingDB("delete",$id);
					         if($status)
							{
								 $log_data = array(
													'id_log'     => $this->id_log,
													'event_date' => date("Y-m-d H:i:s"),
													'module'     => 'Catalog',
													'operation'  => 'Delete',
													'record'     => $status['insertID'],  
													'remark'     => 'Catalog Deleted successfully'
												 );
						         $this->$log_model->log_detail('insert','',$log_data);
								 $this->session->set_flashdata('chit_alert', array('message' => 'Category Records deleted successfully','class' => 'success','title'=>'Category Records'));
							}else{
									  $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'Category Records'));
							}
							redirect('catalog/category/list');  
			 	break;				  
			default:
			
			     $data['category'] = $this->$model->category_settingDB('get',($id!=NULL ? $id:''));
			       $data['access'] = $this->$set_model->get_access('catalog/category/list');
			       echo json_encode($data);
				break;
		}
		
	}
	
	 // Catalog Product Detalis //


	function product_detalis($type="",$id="")
	{
		
		$model = self::WALL_MODEL;
		$set_model = self::SET_MODEL;
		$log_model = self::LOG_MODEL;
		switch($type){
			case "List":
					
			    $data['product']=$this->$model->product_settingDB('get',($id!=NULL?$id:''));    
				$data['main_content'] = "catalog/product/list" ;
	 			$this->load->view('layout/template', $data);   
				break;
			case "View":
			      if($id!=NULL)
			      {
					$data['product'] = $this->$model->product_settingDB('get',$id);
				  }
				  else
				  {
				  	$data['product'] = $this->$model->product_settingDB();					
				  }

				//   print_r($data); exit;
				  $data['main_content'] = "catalog/product/form" ;
	 			  $this->load->view('layout/template', $data);  
				break;
			case "Save":
			       
				   $product   = $this->input->post('product');
				   
				  
				   
				     //formatting form values
			        $insertData=array( 
			                           'id_category'      => (isset($product['id_category']) && $product['id_category']!=''? $product['id_category']:NULL),
			                            'productname' 	 => (isset($product['product_name']) && $product['product_name']!=''? $product['product_name']:NULL),
			                            'description'       => (isset($product['description']) && $product['description']!=''? $product['description']:NULL),'code'       => (isset($product['code']) && $product['code']!=''? $product['code']:NULL),
										'type'       => (isset($product['type']) && $product['type']!=''? $product['type']:NULL),
										
									    'weight'       => (isset($product['weight']) && $product['weight']!=''? $product['weight']:NULL),
										
										'size'       => (isset($product['size']) && $product['size']!=''? $product['size']:NULL),
										
										'purity'       => (isset($product['purity']) && $product['purity']!=''? $product['purity']:NULL),
										
										'price'       => (isset($product['price']) && $product['price']!=''? $product['price']:NULL),
										
										'proimage'       => (isset($product['proimage']) && $product['proimage']!=''? $product['proimage']:NULL),
			                            'active'		 => (isset($product['active']) && $product['active']!=''? $product['active']:0)
									); 
			       
			       //inserting data                  
			        $this->db->trans_begin();

	 				$status = $this->$model->product_settingDB("insert","",$insertData);
						
	 				if(isset($_FILES['product']['name']))	
			            {
						   if($status>0)
						   {
							    $this->set_image($status);
						   }
						}

		 			if($this->db->trans_status()===TRUE)
		             {
                       $this->db->trans_commit();

					 	$this->session->set_flashdata('chit_alert',array('message'=>'New Product added successfully','class'=>'success','title'=>'Product List'));
					 }
					 else
					 {
					   
                        $this->db->trans_rollback();						 	
					 	$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Product List'));
					 }

					redirect('catalog/product/list');	             
			      break;
			case "Update":
			        //get form values
			       $product   = $this->input->post('product');
				   
				   
				   
				     //formatting form values
			        $updateData=array( 
			                           'id_category'      => (isset($product['id_category']) && $product['id_category']!=''? $product['id_category']:NULL),
			                            'productname' 	 => (isset($product['product_name']) && $product['product_name']!=''? $product['product_name']:NULL),
			                            'description'       => (isset($product['description']) && $product['description']!=''? $product['description']:NULL),'code'       => (isset($product['code']) && $product['code']!=''? $product['code']:NULL),
										'type'       => (isset($product['type']) && $product['type']!=''? $product['type']:NULL),
										
									    'weight'       => (isset($product['weight']) && $product['weight']!=''? $product['weight']:NULL),
										
										'size'       => (isset($product['size']) && $product['size']!=''? $product['size']:NULL),
										
										'purity'       => (isset($product['purity']) && $product['purity']!=''? $product['purity']:NULL),
										
										'price'       => (isset($product['price']) && $product['price']!=''? $product['price']:NULL),
									
			                            'active'		 => (isset($product['active']) && $product['active']!=''? $product['active']:0)
									); 
			        
			       //update data                  
			        $this->db->trans_begin();  
                   					              
			       $status = $this->$model->product_settingDB("update",$id,$updateData);
			       
			        if(isset($_FILES['product']['name']['product_img']) && $_FILES['product']['name']['product_img'] !='')	
			            {
							
						   if($status>0)
						   {
							    $this->set_image($status);
						   }
						}
						
		 			if($this->db->trans_status()===TRUE)
		             {
                       $this->db->trans_commit();

					 	$this->session->set_flashdata('chit_alert',array('message'=>'Product Detalis Updated successfully','class'=>'success','title'=>'Product List'));
					 }
					 else
					 {
                        $this->db->trans_rollback();						 	
					 	$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Product List'));
					 }                
			          redirect('catalog/product/list');   
			      break;
              case 'Delete':
				 	      $status = $this->$model->product_settingDB("delete",$id);
					         if($status)
							{
								 
						         $this->$log_model->log_detail('insert','',$log_data);
								 $this->session->set_flashdata('chit_alert', array('message' => 'Category Records deleted successfully','class' => 'success','title'=>'Product Records'));
							}else{
									  $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'Prodcut Records'));
							}
							redirect('catalog/product/list');  
			 	break;				  
			default:
			
			     $data['product'] = $this->$model->product_settingDB('get',($id!=NULL ? $id:''));
			       $data['access'] = $this->$set_model->get_access('catalog/product/list');
			       echo json_encode($data);
				break;
		}
		
	}
	
	function set_catimage($id)
	{
	$data=array();
     $model = self::WALL_MODEL;
   	 if($_FILES['category']['name']['catimage'])
     { 
   	 	$path='assets/img/category/';
	
         if (!is_dir($path)) {

            mkdir($path, 0777, TRUE);
		}
		else{
			$file = $path.$id['ID'].".jpg" ;
            chmod($path,0777);
            unlink($file);
		}

   	 	$img=$_FILES['category']['tmp_name']['catimage'];

		$filename = $_FILES['category']['name']['catimage'];	



   	 	$imgpath='assets/img/category/'.$filename;



	 	$upload=$this->upload_img('catimage',$imgpath,$img);	



	 	$data['catimage']= base_url().$imgpath;
		
	 	$this->$model->category_settingDB("update",$id['ID'],$data);


	   } 
	}
	
	
	function set_image($id)
   {
     $data=array();
     $model = self::WALL_MODEL;
   	 if($_FILES['product']['name']['product_img'])
     { 
   	 	$path='assets/img/products/';
	
         if (!is_dir($path)) {

            mkdir($path, 0777, TRUE);
		}
		else{
			$file = $path.$id['ID'].".jpg" ;
            chmod($path,0777);
            unlink($file);
		}

   	 	$img= str_replace(' ', '_', $_FILES['product']['tmp_name']['product_img']);

		$filename =str_replace(' ','_', $_FILES['product']['name']['product_img']);	



   	 	$imgpath='assets/img/products/'.$filename;



	 	$upload=$this->upload_img('proimage',$imgpath,$img);	



	 	$data['proimage']= base_url().$imgpath;

	 	$this->$model->product_settingDB("update",$id['ID'],$data);



	 } 
 }
 
 
  function upload_img( $outputImage,$dst, $img)



	{	



	if (($img_info = getimagesize($img)) === FALSE)



	{



		// die("Image not found or not an image");



		return false;



	}



	$width = $img_info[0];



	$height = $img_info[1];



	



	switch ($img_info[2]) {



	  case IMAGETYPE_GIF  : $src = imagecreatefromgif($img);



	  						$tmp = imagecreatetruecolor($width, $height);



	  						$kek=imagecolorallocate($tmp, 255, 255, 255);



				      		imagefill($tmp,0,0,$kek);



	  						break;



	  case IMAGETYPE_JPEG : $src = imagecreatefromjpeg($img); 



	  						$tmp = imagecreatetruecolor($width, $height);



	 						break;



	  case IMAGETYPE_PNG  : $src = imagecreatefrompng($img);



						    $tmp = imagecreatetruecolor($width, $height);



	  						$kek=imagecolorallocate($tmp, 255, 255, 255);



				     		imagefill($tmp,0,0,$kek);



				     		break;



	  default : //die("Unknown filetype");	



	  return false;



	  }		



	 



	  imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);



	  imagejpeg($tmp, $dst, 60);



	}
	
}

?>