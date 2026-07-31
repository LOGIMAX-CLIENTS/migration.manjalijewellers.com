<?php
class Registration_model extends CI_Model {
	var $table_name = 'customer';						//Initialize table Name
	const CUS_IMG_PATH ='admin/assets/img/customer/';
	const DEF_CUS_IMG_PATH = 'admin/assets/img/default.png/';
	const DEF_IMG_PATH = 'admin/assets/img/no_image.png/';
	const CUS_IMG= 'customer.jpg';
	const PAN_IMG = 'pan.jpg';
	const RATION_IMG = 'rationcard.jpg';
	const VOTERID_IMG = 'voterid.jpg';
	function empty_record()
	{	
		$records[] = array(
		            'title'         =>NULL,
					'firstname' 	=> NULL, 
					'lastname' 		=> NULL,
					'email' 		=> NULL, 
					'passwd' 		=> NULL,
					'mobile' 		=> NULL
					);	
		return $records;
	}
	
		//encrypt
	public function __encrypt($str)
	{
		return base64_encode($str);		
	}	
	
	//decrypt
	public function __decrypt($str)
	{
		return base64_decode($str);		
	}
	
	
	function get_entryRecord()
	{
		$records = array();
		//$query_profile = $this->db->query('SELECT cus.id_customer, cus.lastname as lastname, cus.firstname as firstname, DATE_FORMAT(date_of_birth, "%d-%m-%Y") as date_of_birth, gender,  cus.email as email, cus.mobile as mobile, cus.phone as phone, cus_img, cus.pan as pan, cus.pan_proof as pan_proof, cus.voterid as voterid, cus.voterid_proof as voterid_proof, cus.rationcard as rationcard, cus.rationcard_proof as rationcard_proof, comments, address1,pincode,id_state,id_country,id_city,nominee.firstname as nomineename,nominee.relationship as relationship  FROM customer as cus LEFT JOIN address as addr ON addr.id_customer = cus.id_customer LEFT JOIN nominee ON nominee.id_customer = cus.id_customer WHERE cus.id_customer='.$this->session->userdata('cus_id'));
		$query_profile = $this->db->query('SELECT cus.id_customer, cus.lastname as lastname, cus.firstname as firstname, DATE_FORMAT(date_of_birth, "%d-%m-%Y") as date_of_birth, DATE_FORMAT(date_of_wed, "%d-%m-%Y") as date_of_wed, gender,  cus.email as email, cus.mobile as mobile, cus.phone as phone, cus_img, cus.pan as pan, cus.pan_proof as pan_proof, cus.voterid as voterid, cus.voterid_proof as voterid_proof, cus.rationcard as rationcard, cus.rationcard_proof as rationcard_proof, comments,cus.title, addr.address1,addr.address2,addr.address3,id_state,id_country,id_city,addr.pincode,cus.nominee_name as nomineename,cus.nominee_mobile as nomineemobile,cus.nominee_relationship as relationship,cus.ispan_req,cus.is_cus_synced
		 FROM customer as cus 
		 LEFT JOIN address as addr ON addr.id_customer = cus.id_customer
		 WHERE cus.id_customer='.$this->session->userdata('cus_id'));
			if($query_profile->num_rows() > 0)
			{
				foreach($query_profile->result() as $row)
				{
					$records[] = array('id_customer' => $row->id_customer, 'lastname' =>ucfirst( $row->lastname),'firstname' => ucfirst($row->firstname),'date_of_birth' => $row->date_of_birth,'ispan_req' => $row->ispan_req,'is_cus_synced' => $row->is_cus_synced,'date_of_wed' => $row->date_of_wed, 'gender' => $row->gender,'email' => $row->email,'mobile' => $row->mobile,'phone' => $row->phone, 'cus_img' => $row->cus_img,'pan' => $row->pan,'pan_proof' => $row->pan_proof,'voterid' => $row->voterid,'voterid_proof' => $row->voterid_proof,'rationcard' => $row->rationcard,'rationcard_proof' => $row->rationcard_proof,'address1' =>ucfirst( $row->address1),'address2' => ucfirst($row->address2),'address3' => ucfirst($row->address3),'pincode' => $row->pincode,'id_state' => ($row->id_state==null?'0':$row->id_state) ,'id_country' => ($row->id_country==null?'0':$row->id_country),'id_city' => ($row->id_city==null?'0':$row->id_city),'nominee_name' => ucfirst($row->nomineename),'nominee_mobile' => ucfirst($row->nomineemobile),'nominee_relationship' => ucfirst($row->relationship),'title'=>$row->title);
				}
			}
	
		$query_profile->free_result();
		//print_r($this->db->last_query());exit;
		return array('profile' => $records);
	}
	function get_scheme()
	{
		$records = array();
		$query_scheme = $this->db->query('SELECT id_scheme,scheme_name, scheme_type, description, amount,total_installments, min_weight, max_weight, min_chance, max_chance, interest_by, interest_value   FROM scheme');
			if($query_scheme->num_rows() > 0)
			{
				foreach($query_scheme->result() as $row)
				{
					$records[] = array('id' => $row->id_scheme, 'name' => $row->scheme_name,'description' => $row->description,'scheme_type' => $row->scheme_type, 'amount' => $row->amount,'total_installments' => $row->total_installments,'min_weight' => $row->min_weight,'max_weight' => $row->max_weight, 'min_chance' => $row->min_chance,'max_chance' => $row->max_chance, 'interest_by' => $row->interest_by,'interest_value' => $row->interest_value);
				}
			}
		$query_scheme->free_result();
		echo json_encode(array('scheme' => $records));
	}
	
	function get_country()
	{
		$records = array();
		$query_country = $this->db->query('SELECT id_country,name FROM country');
		$records[] = array('id' => '0', 'name' => '--Choose Country--');
			{
				foreach($query_country->result() as $row)
				{
					$records[] = array('id' => $row->id_country, 'name' => $row->name);
				}
			}
		 return json_encode($records);
	}
	
	function get_state($id)
	{
		$records = array();
		$query_state = $this->db->query('SELECT id_state,name FROM state WHERE id_country ='.$id);
		//	print_r($this->db->last_query());exit;
			$records[] = array('id' => '0', 'name' => '--Choose State--');
			if($query_state->num_rows() > 0)
			{
				foreach($query_state->result() as $row)
				{
					$records[] = array('id' => $row->id_state, 'name' => $row->name);
				}
			}
		
		 return json_encode($records);
	}
	
	function get_city($id)
	{
		$records = array();
		$query_city = $this->db->query('SELECT id_city,name FROM city WHERE id_state ='.$id);
				//print_r($this->db->last_query());exit;
			$records[] = array('id' => '0', 'name' => '--Choose City--');
			if($query_city->num_rows() > 0)
			{
				foreach($query_city->result() as $row)
				{
					$records[] = array('id' => $row->id_city, 'name' => $row->name);
				}
			}
		 return json_encode($records);
	}
	function insert_data($code="")
	{
	   
		if($code!=null){
			$referal_code=$code;		
		}else{			
		    $referal_code=null;		
		}
		
		$branch=$this->input->post('id_branch');
		if($branch!=null||$branch!='')
		{
			$id_branch=$branch;	
		}
		else
		{	
		   $id_branch=null;	
		}
		
		$address1=$this->input->post('address1');
		$address2=$this->input->post('address2');
		
		$id_state=$this->input->post('id_state');
		$id_city=$this->input->post('id_city');
		$id_country=$this->input->post('id_country');

		$cusInsert  = array(
		'info'=>array(	"title" => $this->input->post('title'),
		 "firstname" => ucfirst($this->input->post('firstname')),
		"mobile" => trim($this->input->post('mobile')),
		"email" => $this->input->post('email'),
		"passwd" => trim($this->__encrypt($this->input->post('passwd'))),
		"active" => 1,
		"date_add" => date('Y-m-d H:i:s'),
		"gender"=> -1,
		'cus_ref_code'=>$referal_code,
		"id_branch"=>$id_branch
		),
		'address'=>array(
			
								'address1'			=>	(isset($address1)?$address1:NULL),
								'address2'			=>	(isset($address2)?$address2:NULL),
							//	'id_country'        =>  101,
								'id_country'		=>	(isset($id_country)?$id_country:NULL),
								'id_state'          =>  (isset($id_state)?$id_state:NULL),
								'id_city'			=>	(isset($id_city)?$id_city:NULL),
								
								)
		);
		//print_r($cusInsert);exit;
		if($this->db->insert('customer', $cusInsert['info']))
		{
		     $insertID = $this->db->insert_id();
                if($insertID)
                {
						$cusInsert['address']['id_customer']=$insertID;
						$res=$this->db->insert('address',$cusInsert['address']);
						//print_r($this->db->last_query());exit;
					
						if($res){						
							$id_address=$this->db->insert_id();
							$address = array('id_address' => $id_address);
							$this->db->where('id_customer',$insertID); 
							$this->db->update('customer',$address);
							$status = array("status" => true, "insertID" => $insertID);
						}
						else{
						$status = array("status" => false, "insertID" => '');
					}				
				}
				else{
					$status = array("status" => false, "insertID" => '');
				}
		}
		else
		{
			$status = array("status" => false, "insertID" => '');
		}
		return $status;
	}
	function exitingCusRegister()
	{
		$cusInsert  = array("firstname" => $this->input->post('firstname'),"mobile" => trim($this->input->post('mobile')),"email" => $this->input->post('email'),"passwd" => trim($this->input->post('passwd')),"active" => 1,"date_add" => date('Y-m-d H:i:s'));
		
		if($this->db->insert('customer', $cusInsert))
		{
			$insertID = $this->db->insert_id();
			$scheme_acc_number = $this->input->post('acc1').$this->input->post('acc2').$this->input->post('acc3').$this->input->post('acc4').$this->input->post('acc5').$this->input->post('acc6');
			$scheme_acc  = array("id_scheme" => $this->input->post('scheme_code'),"id_customer" => $insertID,"scheme_acc_number" => $scheme_acc_number,"ref_no" => '',"start_date" => date('Y-m-d H:i:s'),"date_add" => date('Y-m-d H:i:s'),"is_new" => 'N', "active" => 1);
			
			if($this->db->insert('scheme_account', $scheme_acc))
			{
				$acc_id = $this->db->insert_id();
				$status = array("status" => true, "insertID" => $insertID, "acc_id" => $acc_id);
			}
			else
			{
				$status = array("status" => false, "insertID" => '',"acc_id" => '');
			}
		}
		else
		{
			$status = array("status" => false, "insertID" => '');
		}
		return $status;
	}
	
	function customer_detail()
	{
		$sql="Select
				   c.id_customer,c.firstname,c.lastname,c.date_of_birth,c.date_of_wed,
				   a.address1,a.address2,a.address3,ct.name as city,a.pincode,s.name as state,cy.name as country,
				   c.phone,c.mobile,c.email,c.nominee_name,c.nominee_relationship,c.nominee_mobile,
				   c.cus_img,c.pan,c.pan_proof,c.voterid,c.voterid_proof,c.rationcard,c.rationcard_proof,a.id_country,a.id_city,a.id_state,c.id_employee,
				   count(sa.id_scheme_account) as accounts,
				   c.comments,c.username,c.passwd,c.is_new,c.active,c.profile_complete, DATE_FORMAT(c.`date_add`,'%d-%m-%Y') as date_add,c.`date_upd`
			From customer c
				left join address a on(c.id_customer=a.id_customer)
				left join country cy on (a.id_country=cy.id_country)
				left join state s on (a.id_state=s.id_state)
				left join city ct on (a.id_city=ct.id_city)
				left join scheme_account sa on (c.id_customer=sa.id_customer and sa.active=1 and sa.is_closed=0)
			Where c.active=1 and c.id_customer='".$this->session->userdata('cus_id')."'";
		$data['profile'] =	$this->db->query($sql)->row_array();
		
		
		  if($data['profile']!='')
		  {  
		     $sum = 0;
		     $customer =$data['profile'];
		     
		      if($customer['firstname']!=''||$customer['firstname']!=NULL)
		      {
			  	 $sum=$sum + 5;
			  }
			 
			  if($customer['lastname']!=''||$customer['lastname']!=NULL)
		      {
			  	 $sum=$sum + 5;
			  }  
			  
			  if($customer['date_of_birth']!=''||$customer['date_of_birth']!=NULL)
		      {
			  	 $sum=$sum + 5;
			  }
		  	   
		  	   if($customer['date_of_wed']!=''||$customer['date_of_wed']!=NULL)
		      {
			  	 $sum=$sum + 5;
			  }  
			  
			   if($customer['cus_img']!=''||$customer['cus_img']!=NULL)
		      {
			  	 $sum=$sum + 20;
			  }   
			  
			  if(($customer['address1']!=''||$customer['address1']!=NULL) || ($customer['address2']!=''||$customer['address2']!=NULL))
		      {
			  	 $sum=$sum + 10;
			  }
			  
			  if($customer['pincode']!=''||$customer['pincode']!=NULL)
		      {
			  	 $sum=$sum + 10;
			  }
			  
			  if(($customer['city']!=''||$customer['city']!=NULL) && ($customer['state']!=''||$customer['state']!=NULL))
		      {
			  	 $sum=$sum + 10;
			  }
			  
			  if(($customer['rationcard_proof']!=''||$customer['rationcard_proof']!=NULL) && ($customer['voterid_proof']!=''||$customer['voterid_proof']!=NULL))
		      {
			  	 $sum=$sum + 15;
			  }
			  
			  if($customer['pan_proof']!=''||$customer['pan_proof']!=NULL)
		      {
			  	 $sum=$sum + 15;
			  }
			  
			  $data['profile_stat'] = $sum;
		  	  
		  }
      return $data;	
	}
	function check_profile()
	{
		$record = array();
		$is_complete = 0;
		$query_profile = $this->db->query("SELECT ifnull(profile_complete,0) as profile_complete, DATE_FORMAT(date_add,'%d-%m-%Y') AS member_since FROM customer WHERE mobile='".$this->session->userdata('username')."'");
		if($query_profile->num_rows() > 0)
		{
			foreach($query_profile->result() as $row)
			{
				$record['is_complete'] = $row->profile_complete;
				$record['member_since'] = $row->member_since;
				
			}
		}
		return $record;			
	}
	function check_mobileno($mobile)
	{
		$query = $this->db->query("SELECT * FROM customer WHERE mobile=".$mobile);
		if($query->num_rows() > 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	function update_mobile($mobile)
	{
		$query = $this->db->query("update customer set mobile = ".$mobile." WHERE mobile='".$this->session->userdata('username')."'");
		if($query)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function clientEmail($id) 
	{
		$resultset = $this->db->query("select email from customer where email='".$id."'");
		if ($resultset->num_rows() > 0)	
		{
			return 1;
		}
		else	
		{
			return 0;
		}
	}
	function reset_passwd()
	{
		$resultset = $this->db->query("UPDATE customer SET passwd ='".$this->__encrypt($this->input->post('passwd'))."' WHERE  mobile='".$this->session->userdata('username')."'");
		if ($this->db->affected_rows() > 0)	
		{
			return 1;
		}
		else	
		{
			return 0;
		}
	}
	function update_data($cus_id)
	{
		
		$cusInsert  = array("firstname" => $this->input->post('firstname'),
							"lastname" => $this->input->post('lastname'),
							"gender" => $this->input->post('gender'),
							"date_of_birth" => strlen($this->input->post('date_of_birth')) ? date("Y-m-d", strtotime($this->input->post('date_of_birth'))) : NULL,"date_of_wed" => strlen($this->input->post('date_of_wed')) ? date("Y-m-d", strtotime($this->input->post('date_of_wed'))) : NULL,
							"email" => $this->input->post('email'),
							"nominee_name" => $this->input->post('nominee_name'),
							"nominee_relationship" => $this->input->post('nominee_relationship'),
							"pan" => $this->input->post('pan')!=null?$this->input->post('pan'):null,
							"title" => $this->input->post('title')!=null?$this->input->post('title'):null,
							"date_upd" => date('Y-m-d H:i:s'));

		$this->db->where('id_customer',$cus_id);
		if($this->db->update('customer', $cusInsert))
		{
				
			//Nominee table insert
		/*	$deleteNominee = $this->db->delete('nominee',array('id_customer' => $cus_id));
			if($deleteNominee)
			{
				$nominee  = array("id_customer" => $cus_id,"firstname" => $this->input->post('nominee_name'),"relationship" => $this->input->post('nominee_relationship'),"date_add" => date("Y-m-d h:i:sa"));
				
				$this->db->insert('nominee',$nominee);
			}
		*/	   
			   //address table insert
			$addr  = array("id_country" => $this->input->post('id_country'),"id_state" => $this->input->post('id_state'),"id_city" => $this->input->post('id_city'),"id_customer" => $cus_id,"address1" => $this->input->post('address1'),"address2" => $this->input->post('address2'),"address3" => $this->input->post('address3'),"pincode" => $this->input->post('pincode'),"date_add" => date('Y-m-d',strtotime(str_replace("/","-",$this->input->post('date_add')))));
			
				$this->db->where('id_customer',$cus_id);
				$q = $this->db->get('address');
			
			   if ( $q->num_rows() > 0 ) 
			   {
				  $this->db->where('id_customer',$cus_id);
				  $addrval = $this->db->update('address',$addr);
			   } else {
			   	
				  $addrval = $this->db->insert('address',$addr);
			   }
			  
			if($addrval)
			{
					$folderName = $cus_id;
					$pathToUpload =self::CUS_IMG_PATH.$folderName."/";
					if ( ! file_exists($pathToUpload) )
						$create = mkdir($pathToUpload, 0777, true); 
					if(isset($_FILES['cus_img']['name']) || isset($_FILES['pan_proof']['name']) || isset($_FILES['voterid_proof']['name']) || isset($_FILES['rationcard_proof']['name'] ))	
			            {
						$img=$this->set_image($cus_id);
						return $img;
						}
						
			}
		}
	}
function upload_img( $outputImage,$dst, $img)
	{
	
	if (($img_info = getimagesize($img)) === FALSE)
	  die("Image not found or not an image");

	$width = $img_info[0];
	$height = $img_info[1];

	switch ($img_info[2]) {
	  case IMAGETYPE_GIF  : $src = imagecreatefromgif($img);  break;
	  case IMAGETYPE_JPEG : $src = imagecreatefromjpeg($img); break;
	  case IMAGETYPE_PNG  : $src = imagecreatefrompng($img);  break;
	  default : die("Unknown filetype");
	  }
	  $tmp = imagecreatetruecolor($width, $height);
	  
	imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);
	imagejpeg($tmp, $dst);

	}


function upload_img__($field,$img_path,$filename)
	{
		
	
		if (!is_dir($img_path)) {
		    mkdir($img_path, 0777, TRUE);
		}
	
		if ($_FILES && $_FILES[$field]["tmp_name"] !="") {
   	   	list($w, $h) = getimagesize($_FILES[$field]["tmp_name"]);
		     	/* calculate new image size with ratio */
		     $width = 900;
			 $height = 900;
			 $ratio = max($width/$w, $height/$h);
			 $h = ceil($height / $ratio);
			 $x = ($w - $width / $ratio) / 2;
			 $w = ceil($width / $ratio);
			 /* new file name */
			 $path = trim($img_path).$filename;
	
			 /* read binary data from image file */
			 $imgString = file_get_contents($_FILES[$field]['tmp_name']);
		
			 /* create image from string */
			 $image = imagecreatefromstring($imgString);
			 $tmp = imagecreatetruecolor($width, $height);
			 imagecopyresampled($tmp, $image,
			0, 0,
			$x, 0,
			$width, $height,
			$w, $h);
			 /* Save image */
			 switch ($_FILES[$field]['type']) {
			case 'image/jpeg':
			 imagejpeg($tmp, $path, 60);
			 break;
			case 'image/png':
			 imagepng($tmp, $path, 0);
			 break;
			case 'image/gif':
			 imagegif($tmp, $path);
			 break;
			default:
			 exit;
			 break;
			 }
			 $file_name = $path;
			     imagedestroy($image);
			     imagedestroy($tmp);
			     
			 }   
	}
	
	function set_image($cus_id)
 {
 	
 	$data=array();
    
   	 if($_FILES['cus_img']['name'])
   	 {   
      //  $ext = pathinfo($_FILES['cus_img']['name'], PATHINFO_EXTENSION);
        $img=$_FILES['cus_img']['tmp_name'];
        $path=self::CUS_IMG_PATH.$cus_id."/".self::CUS_IMG;
		$filename = self::CUS_IMG.".jpg";	 	
	 	$this->upload_img('cus_img',$path,$img);	
	 	$data['cus_img']= $filename;	
		
	 } 
	
	 if($_FILES['pan_proof']['name']!="")
   	 {
		//$ext = pathinfo($_FILES['pan_proof']['name'], PATHINFO_EXTENSION); 
		$img=$_FILES['pan_proof']['tmp_name'];
		$path=self::CUS_IMG_PATH.$cus_id."/".self::PAN_IMG;
		$filename = self::PAN_IMG.".jpg";
   	 	$this->upload_img('pan_proof',$path,$img);
   	 	$data['pan_proof']=$filename;
	 	
	 } 
	 
	 if($_FILES['voterid_proof']['name']!="")
   	 {
		// $ext = pathinfo($_FILES['voterid_proof']['name'], PATHINFO_EXTENSION); 
		 $img=$_FILES['voterid_proof']['tmp_name'];
		 $path=self::CUS_IMG_PATH.$cus_id."/".self::VOTERID_IMG;
		 $filename =self::VOTERID_IMG.".jpg";
   	 	 $this->upload_img('voterid_proof',$path,$img);
   	 	 $data['voterid_proof']=  $filename;
	 	
	 } 
	 
	 if($_FILES['rationcard_proof']['name']!="")
   	 {
		//$ext = pathinfo($_FILES['rationcard_proof']['name'], PATHINFO_EXTENSION);  
		$img=$_FILES['rationcard_proof']['tmp_name'];
		$path=self::CUS_IMG_PATH.$cus_id."/".self::RATION_IMG;
		$filename = self::RATION_IMG.".jpg";
   	 	$this->upload_img('rationcard_proof',$path,$img );
   	 	$data['rationcard_proof']= $filename ;
	 }
	

	 
 }
 
 
	function upload_image($field_name, $filename, $pathToUpload)
	{
		
		@unlink ($pathToUpload.'/'.$filename.'.png');
		@unlink ($pathToUpload.'/'.$filename.'.jpg');
		$config['upload_path'] = $pathToUpload ;
		$config['allowed_types'] = 'jpg|png';
		$config['max_size'] = '1024';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';
		$config['file_name'] = $filename;
		$config['overwrite'] = TRUE;
		$this->upload->initialize($config);
		if (!$this->upload->do_upload($field_name))
		{
			return false;
		}
		else
		{
			return true;	
		}
	}
	
	function enquirySubmit($enqInsert)
	{
			if($this->db->insert('cust_enquiry', $enqInsert))
			{
				return true;
			}
			else
			{
				return false;
			}
	}
	
	function get_cusData($mobile)
	{
		$records = array();
		$query_invoice = $this->db->query("SELECT firstname,lastname,email FROM  customer WHERE  mobile='".$mobile."'");
		if($query_invoice->num_rows() == 1)
			{
				foreach($query_invoice->result() as $row)
				{
					$records[] = array('firstname' => $row->firstname,'lastname' => $row->lastname,'email' => $row->email);
				}
				
			}
			return $records;
	}
	
	function get_cusData_by_ID($id_customer)
	{
		$records = array();
		$query_invoice = $this->db->query("SELECT firstname,lastname,email,mobile FROM  customer WHERE  id_customer='".$id_customer."'");
		if($query_invoice->num_rows() == 1)
			{
				foreach($query_invoice->result() as $row)
				{
					$records[] = array('name' => $row->firstname,'lastname' => $row->lastname,'mobile'=>$row->mobile,'email' => $row->email);
				}
				
			}
			return $records;
	}
	
	function updateCustomer($data)
	{
    	$this->db->where('mobile',$this->session->userdata('username')); 
		$cus_info=$this->db->update('customer',$data);	
		return $cus_info;
	}
	
	
	function wallet_accno_generator() 
	{
		$resultset = $this->db->query("SELECT c.wallet_account_type FROM chit_settings c");	
	     if($resultset->num_rows() == 1){
		  return array('wallet_account_type'=>$resultset->row()->wallet_account_type);
	    }	
	}
	
    function insChitwallet($id_wal_ac,$mobile,$id_customer)
	{
		$redeem_updated=[];
		$sql = $this->db->query("select date_format(iwt.entry_date,'%d-%m-%Y') as bill_date,iwd.trans_points,iwt.actual_redeemed,iwt.bill_no,category_code,trans_type from inter_wallet_trans	 iwt
		LEFT JOIN  inter_wallet_trans_detail iwd on iwd.id_inter_wallet_trans = iwt.id_inter_wallet_trans
		where mobile=".$mobile);
    	if($sql->num_rows() > 0){
		    foreach($sql->result_array() as $record){ 
		    	$b_date = date_create($record['bill_date']);
                $bill_date = date_format($b_date,"Y-m-d H:i:s");
    		        if($record['actual_redeemed'] > 0 ){
    		        	$debitdata = array('id_wallet_account'  => $id_wal_ac,
                						  'date_add' 	=> date('Y-m-d H:i:s'),
                						  'date_transaction' 	=> $bill_date,
                						  'transaction_type'	=> 1, // debit
                						  'value'				=> $record['actual_redeemed'],
                						  'ref_no'              => $record['bill_no'].'-'.$record['category_code'],
                						  'description'			=> 'Debited for bill no '.$record['bill_no'].' on '.$record['bill_date'],
                						  );
    		        	if(sizeof($redeem_updated) > 0){
    		        		$alreadyUpdated = 0;
    		        		foreach($redeem_updated as $k=>$v){
								if($k == $record['bill_no']){
									$alreadyUpdated = 1;
								}
							}	
							if($alreadyUpdated == 0){
								$this->db->insert('wallet_transaction',$debitdata);
    				    		$redeem_updated[$record['bill_no']]=1;
							}
						}else{
    				    	$this->db->insert('wallet_transaction',$debitdata);
    				    	$redeem_updated[$record['bill_no']]=1;
						}
    		              
    		        } 
    		        if($record['trans_type'] == 1 && $record['trans_points'] >0){
    		        	$data = array('id_wallet_account'   => $id_wal_ac,
            						  'date_add' 	=> date('Y-m-d H:i:s'),
                					  'date_transaction' 	=> $bill_date,
            						  'transaction_type'	=> ($record['trans_type'] == 1 ? 0 :1),
            						  'value'				=> $record['trans_points'],
            						  'ref_no'              => $record['bill_no'].'-'.$record['category_code'],
            						  'description'			=> 'Credited for bill no. '.$record['bill_no'].' on '.$record['bill_date'],
            						  );
            						  
        			    $status = $this->db->insert('wallet_transaction',$data);
    		        }
        			
        			// Update Customer ID in inter_wallet_account
        			$this->db->where('mobile',$mobile);
        			$this->db->update('inter_wallet_account',array('id_customer' => $id_customer));
		    }
		
		}
		$sql->free_result();
		
		return TRUE;
		
	}
	
 
   function insertdata($insData)
    {
		$status = $this->db->insert('cust_enquiry',$insData); 
		return array('status' => $status, 'insertID' => $this->db->insert_id());
	}
	
	function get_dthEmpryRecord()
	{
		$records = array();
		$sql = $this->db->query("SELECT concat(firstname,' ',lastname) as name,email,mobile FROM  customer WHERE  id_customer=".$this->session->userdata('cus_id'));
		return $sql->row_array();
	} 
	
	/**	
	* Sync Customer accounts On Registration by Mobile Number
	* Scheme Accounts and payments
	*/
	function getBranchCode($id_branch)
    {
		$sql="Select short_name from branch where id_branch=".$id_branch;
		$data = $this->db->query($sql);
		return $data->row()->short_name;			
	}
	
	function updateExisAcByMobile($data)
	{
		if (isset($data['branch_code']) && $data['branch_code'] > 0 && $data['branch_code'] != NULL && $data['branch_code'] != "") { // Only for SCM and TKTM
			$resultset = $this->db->query("SELECT * FROM customer_reg WHERE record_to=2 AND is_modified=1 AND branch_code='" . $data['branch_code'] . "' AND mobile=" . $data['mobile']);
		} else if (isset($data['id_branch']) && $data['id_branch'] > 0 && ($data['id_branch'] != '' || $data['id_branch'] != NULL)) {
			$resultset = $this->db->query("SELECT * FROM customer_reg WHERE record_to=2 AND is_modified=1 AND id_branch='" . $data['id_branch'] . "' AND mobile=" . $data['mobile']);
		} else {
			$resultset = $this->db->query("SELECT * FROM customer_reg WHERE record_to=2 AND is_modified=1 AND mobile=" . $data['mobile']);
		}
		if ($resultset->num_rows() > 0) {
			foreach ($resultset->result() as $row) {
				if (!empty($row->clientid)) {
					$sql = $this->db->query("SELECT id_scheme_account FROM scheme_account WHERE ref_no='" . $row->clientid . "'");
					if ($sql->num_rows() > 0) {
						$id_sch_ac = $sql->row()->id_scheme_account;
						if ($row->is_closed == 1) {
							$acc_data = array(
								'closed_by'           => $row->closed_by,
								'closing_date'        => $row->closing_date,
								'closing_amount'      => $row->closing_amount,
								'closing_weight'      => $row->closing_weight,
								'closing_add_chgs'    => $row->closing_add_chgs,
								'additional_benefits' => $row->additional_benefits,
								'remark_close'        => $row->remark_close,
								'is_closed'           => $row->is_closed,
								'active'              => ($row->is_closed == 1 ? 0 : 1),
								'date_upd'            => date("Y-m-d H:i:s")
							);
							$this->db->where('ref_no', $row->clientid);
							$acc_status = $this->db->update('scheme_account', $acc_data);
						} else {
							$acc_data = array(
								'group_code'        => $row->group_code,
								'scheme_acc_number' => $row->scheme_ac_no,
								'ref_no'            => $row->clientid,
								'date_upd'          => date("Y-m-d H:i:s")
							);
							$this->db->where('id_scheme_account', $id_sch_ac);
							$acc_status = $this->db->update('scheme_account', $acc_data);
						}
						if ($acc_status) {
							$inter_data = array('is_transferred' => 'Y', 'is_modified' => 'N', 'transfer_date' => date('Y-m-d H:i:s'));
							$this->db->where('id_customer_reg', $row->id_customer_reg);
							$this->db->update('customer_reg', $inter_data);
						}
					}
				}
			}
		}
	}

	function insExisAcByMobile($data) 
	{
	   // Update existing modified/closed scheme accounts first
	   $this->updateExisAcByMobile($data);

	   $result = array();
	   if($data['branch_code'] > 0 && $data['branch_code'] != NULL && $data['branch_code'] != ""){ // Only for SCM and TKTM
	       $resultset = $this->db->query("select * from customer_reg where record_to=2 and is_closed=0 and branch_code='".$data['branch_code']."' and mobile=".$data['mobile']);
	   }
	   else if($data['id_branch'] > 0 && ($data['id_branch'] != '' || $data['id_branch'] != NULL)){
	       $resultset = $this->db->query("select * from customer_reg where record_to=2 and is_closed=0 and id_branch='".$data['id_branch']."' and mobile=".$data['mobile']);
	   }
	   else{
	       $resultset = $this->db->query("select * from customer_reg where record_to=2 and is_closed=0 and mobile=".$data['mobile']);
	   } 
	   $processed_client_ids = array();

		if($resultset->num_rows() > 0 ){
			foreach($resultset->result() as $row)
			{
			    $curr_data = $data;
			    $curr_data['sync_scheme_code'] = $row->sync_scheme_code;
			    $curr_data['client_id'] = $row->clientid;
			    $curr_data['firstname']  =$row->firstname;
			    
			    $id_scheme = $this->getschId($curr_data);
			    $sql =$this->db->query("SELECT id_scheme_account FROM scheme_account WHERE ref_no='".$row->clientid."'");
			    $existing_sch = $sql->row_array();

			    if($sql->num_rows() > 0 ){
				    $id_sch_ac = $existing_sch['id_scheme_account'];
				    
				    // Check if there are unsynced transactions OR if customer_reg is not yet registered online
				    $has_pending_trans = $this->db->query("SELECT 1 FROM transaction WHERE is_transferred='N' AND record_to=2 AND client_id='".$row->clientid."' LIMIT 1")->num_rows();
				    
				    if($row->is_registered_online == 0 || $has_pending_trans > 0){
				        if($id_scheme > 0){
				           $curr_data['id_scheme'] = $id_scheme; 
				        }
					    $curr_data['id_sch_ac'] = $id_sch_ac;
	    				$result[] =  $curr_data;
				    }
				    $processed_client_ids[] = $row->clientid;
			    }else{
    			    if($id_scheme > 0){
					    // Digi Gold duplicate prevention - skip if customer already has an active digi scheme
					    $sch_digi_check = $this->db->query("SELECT is_digi FROM scheme WHERE id_scheme = " . $id_scheme);
					    if ($sch_digi_check->num_rows() > 0 && $sch_digi_check->row()->is_digi == 1) {
					        $digi_exists = $this->db->query(
					            "SELECT sa.id_scheme_account FROM scheme_account sa
					             LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
					             WHERE s.is_digi = 1 AND sa.active = 1 AND sa.is_closed = 0
					             AND sa.id_customer = " . $data['id_customer']
					        );
					        if ($digi_exists->num_rows() > 0) {
					            continue; // Customer already has an active digi gold account, skip this entry
					        }
					    }
    					$records = array( 	'id_customer' 		=> $data['id_customer'],
    									'id_scheme'			=> $id_scheme,
    									'scheme_acc_number' => $row->scheme_ac_no,
    									'ref_no'            => $row->clientid,
    									'account_name' 		=> ($row->account_name != '' || $row->account_name != NULL ? $row->account_name : $row->ac_name),
    									'group_code' 		=> $row->group_code,
    									'start_date' 		=> $row->reg_date,
    									'maturity_date' 	=> $row->maturity_date,
    									'is_new' 			=> $row->new_customer,
    									'firstPayment_amt'	=> $row->firstPayment_amt,
    									'firstpayment_wgt'  => $row->firstpayment_wgt,
    									'fixed_rate_on' 	=> $row->fixed_rate_on,
    									'fixed_metal_rate' 	=> $row->fixed_metal_rate,
    									'fixed_wgt' 		=> ($row->fixed_wgt !== null ? $row->fixed_wgt : 0.000),
										'closing_amount' 	=> ($row->closing_amount !== null ? $row->closing_amount : 0.00),
										'closing_weight' 	=> ($row->closing_weight !== null ? $row->closing_weight : 0.000),
    									'id_branch' 		=> ($row->id_branch > 0 ? $row->id_branch : $data['id_branch'] ),
    									'date_add' 			=> date("Y-m-d H:i:s"),
    									'is_registered' 	=> 1,
    									'active' 			=> 1, 
    									'added_by' 			=> isset($data['added_by']) ? $data['added_by'] : 0
    								); 
    					$status = $this->db->insert('scheme_account',$records);	
    					if($status){
    						$_sch_id = $this->db->insert_id();
    						if(empty($_sch_id) || $_sch_id == 0){
    							$_sch_id = $this->db->query('SELECT LAST_INSERT_ID() as last_id')->row()->last_id;
    						}
    						$curr_data['id_sch_ac'] = $_sch_id;
							$curr_data['id_scheme'] = $id_scheme;
    						$result[] =  $curr_data;
    						$processed_client_ids[] = $row->clientid;
    					}
    				}
    			} 
			} 
		}

		// Also check existing active scheme_accounts belonging to this customer that have pending transactions
		// if(!empty($data['id_customer']) && $data['id_customer'] > 0) {
		// 	$existing_accs = $this->db->query("SELECT sa.id_scheme_account, sa.ref_no, sa.id_scheme FROM scheme_account sa WHERE sa.id_customer = ". (int)$data['id_customer'] ." AND sa.ref_no IS NOT NULL AND sa.ref_no != '' AND sa.active = 1 AND sa.is_closed = 0")->result_array();
		// 	foreach($existing_accs as $acc) {
		// 		if(!in_array($acc['ref_no'], $processed_client_ids)) {
		// 			$has_pending_trans = $this->db->query("SELECT 1 FROM transaction WHERE is_transferred='N' AND record_to=2 AND client_id='".$acc['ref_no']."' LIMIT 1")->num_rows();
		// 			if($has_pending_trans > 0) {
		// 				$curr_data = $data;
		// 				$curr_data['client_id'] = $acc['ref_no'];
		// 				$curr_data['id_sch_ac'] = $acc['id_scheme_account'];
		// 				$curr_data['id_scheme'] = $acc['id_scheme'];
		// 				$result[] = $curr_data;
		// 				$processed_client_ids[] = $acc['ref_no'];
		// 			}
		// 		}
		// 	}
		// }

		return $result; 
	} 
	
	function getschId($data) 
	{
		$branchwise_scheme = 0;
    	$settings = $this->db->query("select branchwise_scheme from chit_settings"); 
    	if($settings->num_rows() > 0 ){
    		$branchwise_scheme =  $settings->row()->branchwise_scheme;
    	}  
    	if($branchwise_scheme == 1 && ($data['id_branch'] != '' || $data['id_branch'] != NULL)){
    	   $result = $this->db->query("SELECT s.id_scheme
                                       FROM `scheme` s
                                        LEFT JOIN scheme_branch sb ON sb.id_scheme = s.id_scheme
                                       WHERE sync_scheme_code='".$data['sync_scheme_code']."' AND sb.id_branch='".$data['id_branch']."'" 
                                    );  
    	}else{
    		$result = $this->db->query("select id_scheme from scheme where sync_scheme_code='".$data['sync_scheme_code']."'");  
    	}
    	if($result->num_rows() > 0 ){
    		return $result->row()->id_scheme;
    	}
    	else{
    		return null;
    	}
	}
	
	/*function getschId($data) 
	{		
		$result = $this->db->query("select id_scheme from scheme where sync_scheme_code='".$data['sync_scheme_code']."'");
		//echo $this->db->last_query();exit;
		if($result->num_rows() > 0 ){
			return $result->row()->id_scheme;
		}
		else{
			return null;
		}
	}*/
		
	function old_syncPayData($ac_data) 
	{
		//echo "<pre>";print_r($ac_data);
		$succeedIds = array();
		$no_records = 0;
		foreach($ac_data as $data){	 
			$resultset = $this->db->query("select * from transaction where is_transferred='N' and record_to=2 and client_id='".$data['client_id']."'"); 
			$i = 1;
			if($resultset->num_rows() > 0 ){
				$records = array();
				foreach($resultset->result() as $row)
				{
				    $status = false; // Reset status for this iteration
				    $checkRef_no = $this->db->query("SELECT id_payment, is_offline FROM payment WHERE payment_ref_number ='".$row->ref_no."' and receipt_no ='".$row->receipt_no."'");
				   // echo $this->db->last_query();exit;
				    if($checkRef_no->num_rows() == 0)
				    {
    				    if($row->is_modified == 0){
        					$records = array( 'id_scheme_account'   =>$data['id_sch_ac'],
												'id_scheme'      => $data['id_scheme'],
        	                                    'metal_rate'	    =>$row->rate,
        	                                    'receipt_no'        =>$row->receipt_no,
        	                                    'metal_weight'      =>$row->weight,
        	                                    'payment_amount'	=>$row->amount,
        	                                     'payment_ref_number' =>$row->ref_no,
        	                                    'actual_trans_amt'	=>$row->amount,
        	                                    'date_payment'	    =>$row->payment_date,
        	                                    'date_add'	        =>$row->payment_date,
        	                                    'id_branch' 		=> ($row->id_branch > 0 ? $row->id_branch : $data['id_branch'] ),
        	                                    'custom_entry_date'	=>$row->custom_entry_date,
        	                                    'payment_status'	=>1, 
        	                                    'payment_mode'      =>$row->payment_mode,
        	                                    'payment_status'    =>$row->payment_status,
        	                                    //	'dues' =>$row->NO_OF_INSTAL,
        	                                    'payment_type'      =>'Offline',
												'saved_benefits'    =>$row->saved_benefits_wgt,
												'saved_benefit_amt' =>$row->saved_benefit_amt,
												'benefit_value'     =>$row->benefit_value,
												'benefit_type'     =>$row->benefit_type,
        	                                    'is_offline'	    => 1,
        	                                    'due_type'          =>$row->due_type,
        	                                    'due_month'         =>$row->due_month,
        	                                    'due_year'          =>$row->due_year,
        	                                    'added_by' 			=> isset($data['added_by']) ? $data['added_by'] : 0,
        	                                    'installment'       => $row->installment_no,
        	                                    'gst'               => 0.00,
        	                                    'gst_type'          => 0,
        	                                    'add_charges'       => 0.00,
        	                                    'discountAmt'       =>(!empty($row->discountAmt) ? $row->discountAmt :	0.00)
        	                                );
        	               	$status = $this->db->insert('payment',$records);
							 	$id_payment = $this->db->insert_id();
                               if(empty($id_payment) || $id_payment == 0){
                                   $id_payment = $this->db->query('SELECT LAST_INSERT_ID() as last_id')->row()->last_id;
                               }
                               // NOTE: Do NOT commit/begin here - the controller owns the transaction boundary.
                               //update mode detail table
                               $arrayPayMode = array('payment_amount'     => $row->amount,
                                                    'payment_date'         => $row->payment_date,
                                                    'created_time'         => date("Y-m-d H:i:s"),
                                                    "payment_mode"       => $row->payment_mode,
                                                    "remark"             => 'Offline',
                                                    "payment_ref_number" => $row->ref_no,
                                                    "payment_status"     => 1);

                               
                            $update_pmd = array('payment_status'=> 9, // Cancelled
                                                "updated_time"  => date('Y-m-d H:i:s'),
                                                "remark"        =>  "Removed while direct success on cashfree ".date('Y-m-d H:i:s'),
                                            );
                            $update_existing_pmd = $this->payment_modal->updData($update_pmd,'id_payment',$id_payment,'payment_mode_details');
                            $arrayPayMode['id_payment'] = $id_payment;
                            $payModeInsert = $this->payment_modal->insertData($arrayPayMode,'payment_mode_details');
                            
                            $log_path = 'log/'.date("Y-m-d").'/existing/'.date("Y-m-d").'.txt';
                            $TESTRes = array("status" => "Payment Mode Detail Log", "e" => $this->db->_error_message() ,"q" => $this->db->last_query(), "res" => $res, "data" => $data);
                            $logData = "\n".date('d-m-Y H:i:s')."\n API : mobile_api \n Response : ".json_encode($TESTRes,true);
                            file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
                               
                               //update due_date,installment
                               $dt_pay = date('Y-m-d H:i:s',strtotime(str_replace("/","-",$row->payment_date)));
                            $actual_due_type = $this->get_sync_due_type($data['id_sch_ac'], $dt_pay);
                            $ins_cycle = $this->payment_modal->get_due_date($actual_due_type, $dt_pay,$data['id_sch_ac']);  
                                                

                            if(!empty($ins_cycle) && isset($ins_cycle[0]) && sizeof($ins_cycle[0]) > 0){
                                $cycle_data = array('due_date'           =>  (isset($ins_cycle[0]['due_date_from'])?$ins_cycle[0]['due_date_from']: NULL),
                                                    'due_date_to'           =>  (isset($ins_cycle[0]['due_date_to'])?$ins_cycle[0]['due_date_to']: NULL),
                                                    'grace_date'           =>  (isset($ins_cycle[0]['grace_date'])?$ins_cycle[0]['grace_date']: NULL),
                                                    'installment'           =>  (isset($ins_cycle[0]['installment'])?$ins_cycle[0]['installment']: NULL),
                                                    'is_limit_exceed'           =>  (isset($ins_cycle[0]['is_limit_exceed'])?$ins_cycle[0]['is_limit_exceed']: 0), );
                                                
                                $this->payment_modal->updData($cycle_data, 'id_payment', $id_payment, 'payment'); // FIX: use $id_payment (captured via LAST_INSERT_ID fallback), not insert_id() which now points to payment_mode_details insert
                                                                
                            } else {
                                // Log empty get_due_date result for debugging
                                $due_log_path = 'log/'.date("Y-m-d").'/existing/due_date_fail_'.date("Y-m-d").'.txt';
                                $due_log_data = "\n".date('d-m-Y H:i:s')." EMPTY get_due_date for id_payment=".$id_payment
                                    ." id_sch_ac=".$data['id_sch_ac']." due_type=".$actual_due_type
                                    ." dt_pay=".$dt_pay." client_id=".$data['client_id'];
                                file_put_contents($due_log_path, $due_log_data, FILE_APPEND | LOCK_EX);
                            }

                            // ── POST-PAYMENT PROCESSING (ported from admin_payment saveall) ──────────
                            // getSyncSchemeData fetches scheme flags + paid_installments inline
                            // (mirrors admin payment_model->getSchemeData + getPaidInsData exact queries)
                            $sch_data       = $this->payment_modal->getSyncSchemeData($data['id_sch_ac']);
                            $paid_count     = (isset($sch_data['paid_installments']) ? (int)$sch_data['paid_installments'] : 0);
                            $payment_amount = $row->amount;
                            $metal_wgt      = (!empty($row->weight) ? $row->weight : 0);
                            $gold_rate      = (!empty($row->rate)   ? $row->rate   : 0);
                            $date_payment   = date('Y-m-d', strtotime(str_replace('/', '-', $row->payment_date)));


                            // 1. Rate fix for one-time premium schemes (first payment only)
                            if (!empty($sch_data['one_time_premium']) && $sch_data['one_time_premium'] == 1
                                && !empty($sch_data['rate_fix_by'])   && $sch_data['rate_fix_by']   == 0
                                && !empty($sch_data['rate_select'])   && $sch_data['rate_select']   == 1
                                && $gold_rate != 0
                            ) {
                                $isRateFixed = $this->payment_modal->isSyncRateFixed($data['id_sch_ac']);
                                if ($isRateFixed['status'] == 0) {
                                    $rateUpdData = array(
                                        'fixed_wgt'        => ($metal_wgt > 0 ? $metal_wgt : $payment_amount / $gold_rate),
                                        'firstPayment_amt' => $payment_amount,
                                        'fixed_metal_rate' => $gold_rate,
                                        'rate_fixed_in'    => 0,
                                        'fixed_rate_on'    => date('Y-m-d H:i:s'),
                                    );
                                    $this->payment_modal->updData($rateUpdData, 'id_scheme_account', $data['id_sch_ac'], 'scheme_account');
                                }
                            }

                            // 2. First-payment: set start_date and calculate maturity date
                            if ($paid_count <= 1 && !empty($sch_data['maturity_type']) && $sch_data['maturity_type'] != 4) {
                                $this->payment_modal->updData(
                                    array('start_date' => $date_payment),
                                    'id_scheme_account', $data['id_sch_ac'], 'scheme_account'
                                );
                                if (!empty($sch_data['maturity_days']) && $sch_data['maturity_days'] > 0) {
                                    $maturity_date = date('Y-m-d', strtotime($date_payment . ' + ' . $sch_data['maturity_days'] . ' days'));
                                    $this->payment_modal->updData(
                                        array('maturity_date' => (!empty($sch_data['calc_maturity_date']) ? $sch_data['calc_maturity_date'] : $maturity_date)),
                                        'id_scheme_account', $data['id_sch_ac'], 'scheme_account'
                                    );
                                }
                            } else if (!empty($sch_data['maturity_type']) && $sch_data['maturity_type'] == 4) {
                                $this->payment_modal->calculate_maturityLapse_Date($data['id_sch_ac']);
                            }

                            // 3. Capture firstPayment_amt (flexible schemes)
                            if (
                                !empty($sch_data['firstPayamt_as_payamt']) && ($sch_data['firstPayamt_as_payamt'] == 1 ||
                                !empty($sch_data['firstPayamt_maxpayable']) && $sch_data['firstPayamt_maxpayable'] == 1)
                                && $paid_count == 0
                            ) {
                                $this->payment_modal->updData(
                                    array('firstPayment_amt' => $payment_amount),
                                    'id_scheme_account', $data['id_sch_ac'], 'scheme_account'
                                );
                            }

                            // 4. Capture firstpayment_wgt (weight-based schemes)
                            if (!empty($sch_data['firstPayment_as_wgt']) && $sch_data['firstPayment_as_wgt'] == 1 && $paid_count == 0) {
                                $this->payment_modal->updData(
                                    array('firstpayment_wgt' => (!empty($metal_wgt) ? $metal_wgt : 0.000)),
                                    'id_scheme_account', $data['id_sch_ac'], 'scheme_account'
                                );
                            }
                            // ── END POST-PAYMENT PROCESSING ───────────────────────────────────────────
                            // Update total_paid_ins using old_paid_installments from getPaidInsData:
                            //  - NOT paid_installments (COUNT DISTINCT installment) → broken for offline
                            //    because offline installment_no resets to 1 causing collision
                            //  - NOT COUNT(id_payment) → overcounts multi-chance schemes (2 pays/month = 1 install)
                            //  - old_paid_installments = date-based, scheme-aware:
                            //    monthly single-chance  → COUNT(payment rows)
                            //    monthly multi-chance   → COUNT(DISTINCT month)
                            //    daily single-chance    → COUNT(payment rows)
                            //    daily multi-chance     → COUNT(DISTINCT day)
                            //    days-duration cycle    → COUNT(due_date month)
                            $paid = $this->payment_modal->getPaidInsData($data['id_sch_ac']);
                            if (!empty($paid)) {
                                $this->payment_modal->updData(
                                    array('total_paid_ins' => (int)$paid['old_paid_installments']),
                                    'id_scheme_account', $data['id_sch_ac'], 'scheme_account'
                                );
                            }
    				    }elseif($row->is_modified == 1){
    				        //update if offline record is with cancelled status
                				$upd_array = array ( "payment_status" 	=> 4,
                			   					    'receipt_no'        =>$row->receipt_no,
                			   						"remark" 			=> $row->remarks,
                			   						"date_upd" 			=> date('Y-m-d H:i:s'),
                			   						"payment_ref_number"=> $row->ref_no
                			    					);
                			    $status = $this->db->insert('payment',$upd_array);
    				    }
    										
    				
    				 	 /*echo $this->db->_error_message();
    				 	echo $this->db->last_query();exit; */
    					if($status){
    					    $succeedIds[] = $row->id_transaction;
    					}
    					$i++;
					} else {
					    $existing_payment = $checkRef_no->row();
					    if ($existing_payment->is_offline == 0) {
					        $this->db->where('id_payment', $existing_payment->id_payment);
					        $this->db->update('payment', array('is_offline' => 1));
					    }
					    // Already synced — add it to succeedIds to ensure transaction table is marked transferred
					    $succeedIds[] = $row->id_transaction;
					}
				} 
			}else{
				if($this->config->item("integrationType") == 3 || $this->config->item("integrationType") == 2){ // Jewelone ERP or Sync Tool
			        $no_records += 1; 
			        // No pending transactions - scheme_account still needs to be registered
			    }
			} 
		}
		return array('succeedIds' => $succeedIds,'no_records' => $no_records);
	}
	function syncPayData($ac_data) 
	{
		//echo "<pre>";print_r($ac_data);
		$succeedIds = array();
		$no_records = 0;
		foreach($ac_data as $data){	 
			$resultset = $this->db->query("select * from transaction where is_transferred='N' and record_to=2 and client_id='".$data['client_id']."'"); 
			$i = 1;
			if($resultset->num_rows() > 0 ){
				$records = array();
				foreach($resultset->result() as $row)
				{
				    $status = false; // Reset status for this iteration
				    $checkRef_no = $this->db->query("SELECT id_payment, is_offline FROM payment WHERE payment_ref_number ='".$row->ref_no."' and receipt_no ='".$row->receipt_no."'");
				   // echo $this->db->last_query();exit;
				    if($checkRef_no->num_rows() == 0)
				    {
    				    if($row->is_modified == 0){
        					$records = array( 'id_scheme_account'   =>$data['id_sch_ac'],
												'id_scheme'      => $data['id_scheme'],
        	                                    'metal_rate'	    =>$row->rate,
        	                                    'receipt_no'        =>$row->receipt_no,
        	                                    'metal_weight'      =>$row->weight,
        	                                    'payment_amount'	=>$row->amount,
        	                                     'payment_ref_number' =>$row->ref_no,
        	                                    'actual_trans_amt'	=>$row->amount,
        	                                    'date_payment'	    =>$row->payment_date,
        	                                    'date_add'	        =>$row->payment_date,
        	                                    'id_branch' 		=> ($row->id_branch > 0 ? $row->id_branch : $data['id_branch'] ),
        	                                    'custom_entry_date'	=>$row->custom_entry_date,
        	                                    'payment_status'	=>1, 
        	                                    'payment_mode'      =>$row->payment_mode,
        	                                    'payment_status'    =>$row->payment_status,
        	                                    //	'dues' =>$row->NO_OF_INSTAL,
        	                                    'payment_type'      =>'Offline',
												'saved_benefits'    =>$row->saved_benefits_wgt,
												'saved_benefit_amt' =>$row->saved_benefit_amt,
												'benefit_value'     =>$row->benefit_value,
												'benefit_type'     =>$row->benefit_type,
        	                                    'is_offline'	    => 1,
        	                                    'due_type'          =>$row->due_type,
        	                                    'due_month'         =>$row->due_month,
        	                                    'due_year'          =>$row->due_year,
        	                                    'added_by' 			=> isset($data['added_by']) ? $data['added_by'] : 0,
        	                                    'installment'       => NULL,
        	                                    'gst'               => 0.00,
        	                                    'gst_type'          => 0,
        	                                    'add_charges'       => 0.00,
        	                                    'discountAmt'       =>(!empty($row->discountAmt) ? $row->discountAmt :	0.00)
        	                                );
        	               	$status = $this->db->insert('payment',$records);
							 	$id_payment = $this->db->insert_id();
                               if(empty($id_payment) || $id_payment == 0){
                                   $id_payment = $this->db->query('SELECT LAST_INSERT_ID() as last_id')->row()->last_id;
                               }
                               // NOTE: Do NOT commit/begin here - the controller owns the transaction boundary.
                               //update mode detail table
                               $arrayPayMode = array('payment_amount'     => $row->amount,
                                                    'payment_date'         => $row->payment_date,
                                                    'created_time'         => date("Y-m-d H:i:s"),
                                                    "payment_mode"       => $row->payment_mode,
                                                    "remark"             => 'Offline',
                                                    "payment_ref_number" => $row->ref_no,
                                                    "payment_status"     => 1);

                               
                            $update_pmd = array('payment_status'=> 9, // Cancelled
                                                "updated_time"  => date('Y-m-d H:i:s'),
                                                "remark"        =>  "Removed while direct success on cashfree ".date('Y-m-d H:i:s'),
                                            );
                            $update_existing_pmd = $this->payment_modal->updData($update_pmd,'id_payment',$id_payment,'payment_mode_details');
                            $arrayPayMode['id_payment'] = $id_payment;
                            $payModeInsert = $this->payment_modal->insertData($arrayPayMode,'payment_mode_details');
                            
                            $log_path = 'log/'.date("Y-m-d").'/existing/'.date("Y-m-d").'.txt';
                            $TESTRes = array("status" => "Payment Mode Detail Log", "e" => $this->db->_error_message() ,"q" => $this->db->last_query(), "res" => $res, "data" => $data);
                            $logData = "\n".date('d-m-Y H:i:s')."\n API : mobile_api \n Response : ".json_encode($TESTRes,true);
                            file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
                               
                               // Calculate Dynamic Due Date and Installment Number based on Scheme Cycle
                               $dt_pay = date('Y-m-d H:i:s',strtotime(str_replace("/","-",$row->payment_date)));
                               $pay_date_only = date('Y-m-d', strtotime($dt_pay));
                               $pay_month_only = date('Y-m', strtotime($dt_pay));

                               // Fetch scheme settings & cycle rules
                               $sch_info = $this->get_scheme_sync_info($data['id_sch_ac']);
                               $is_multi_chance = false;
                               if (!empty($sch_info)) {
                                   if ($sch_info['is_digi'] == 1 || $sch_info['max_chance'] > 1 || $sch_info['payment_chances'] == 1 || $sch_info['scheme_type'] == 3 || $sch_info['scheme_type'] == 2 || $sch_info['flexible_sch_type'] > 0) {
                                       $is_multi_chance = true;
                                   }
                               }

                               $samecycle_pay = null;

                               if (!empty($sch_info) && $sch_info['installment_cycle'] == 1) {
                                   // 1. Daily Cycle: Check for earlier payment on the SAME DAY
                                   $samecycle_pay = $this->db->query(
                                       "SELECT p.installment, p.due_date, p.due_date_to, p.grace_date, p.due_type, p.is_limit_exceed
                                        FROM payment p
                                        WHERE p.id_scheme_account = " . (int)$data['id_sch_ac'] . "
                                          AND p.payment_status = 1
                                          AND p.installment IS NOT NULL
                                          AND p.installment > 0
                                          AND (DATE(p.date_payment) = '" . $this->db->escape_str($pay_date_only) . "' OR DATE(p.due_date) = '" . $this->db->escape_str($pay_date_only) . "')
                                          AND p.id_payment != " . (int)$id_payment . "
                                        ORDER BY p.id_payment ASC
                                        LIMIT 1"
                                   )->row_array();
                               } else if ($is_multi_chance && (!empty($sch_info) && $sch_info['installment_cycle'] == 0)) {
                                   // 2. Monthly Cycle with Multiple Payment Chance (Digi Gold / flexible): Check for earlier payment in the SAME MONTH
                                   $samecycle_pay = $this->db->query(
                                       "SELECT p.installment, p.due_date, p.due_date_to, p.grace_date, p.due_type, p.is_limit_exceed
                                        FROM payment p
                                        WHERE p.id_scheme_account = " . (int)$data['id_sch_ac'] . "
                                          AND p.payment_status = 1
                                          AND p.installment IS NOT NULL
                                          AND p.installment > 0
                                          AND (DATE_FORMAT(p.date_payment, '%Y-%m') = '" . $this->db->escape_str($pay_month_only) . "' OR DATE_FORMAT(p.due_date, '%Y-%m') = '" . $this->db->escape_str($pay_month_only) . "')
                                          AND p.id_payment != " . (int)$id_payment . "
                                        ORDER BY p.id_payment ASC
                                        LIMIT 1"
                                   )->row_array();
                               } else {
                                   // 3. Fallback: Check for same exact payment date
                                   $samecycle_pay = $this->db->query(
                                       "SELECT p.installment, p.due_date, p.due_date_to, p.grace_date, p.due_type, p.is_limit_exceed
                                        FROM payment p
                                        WHERE p.id_scheme_account = " . (int)$data['id_sch_ac'] . "
                                          AND p.payment_status = 1
                                          AND p.installment IS NOT NULL
                                          AND p.installment > 0
                                          AND DATE(p.date_payment) = '" . $this->db->escape_str($pay_date_only) . "'
                                          AND p.id_payment != " . (int)$id_payment . "
                                        ORDER BY p.id_payment ASC
                                        LIMIT 1"
                                   )->row_array();
                               }

                               if (!empty($samecycle_pay) && !empty($samecycle_pay['installment'])) {
                                   // Reuse existing cycle installment and due dates
                                   $cycle_data = array(
                                       'due_date'        => $samecycle_pay['due_date'],
                                       'due_date_to'      => $samecycle_pay['due_date_to'],
                                       'grace_date'       => $samecycle_pay['grace_date'],
                                       'installment'      => $samecycle_pay['installment'],
                                       'due_type'         => $samecycle_pay['due_type'],
                                       'is_limit_exceed'  => (isset($samecycle_pay['is_limit_exceed']) ? $samecycle_pay['is_limit_exceed'] : 0),
                                   );
                                   $this->payment_modal->updData($cycle_data, 'id_payment', $id_payment, 'payment');
                               } else {
                                   // First payment for this cycle: calculate due type and due date range
                                   $actual_due_type = (!empty($row->due_type) && $row->due_type != '') ? $row->due_type : $this->get_sync_due_type($data['id_sch_ac'], $dt_pay);
                                   $ins_cycle = $this->payment_modal->get_due_date($actual_due_type, $dt_pay, $data['id_sch_ac']);

                                   if (!empty($ins_cycle) && isset($ins_cycle[0]) && sizeof($ins_cycle[0]) > 0) {
                                       $cycle_data = array(
                                           'due_date'        => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),
                                           'due_date_to'      => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),
                                           'grace_date'       => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),
                                           'installment'      => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),
                                           'is_limit_exceed'  => (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),
                                           'due_type'         => (isset($actual_due_type) ? $actual_due_type : NULL),
                                       );

                                       $this->payment_modal->updData($cycle_data, 'id_payment', $id_payment, 'payment');
                                   } else {
                                       // Log empty get_due_date result for debugging
                                       $due_log_path = 'log/' . date("Y-m-d") . '/existing/due_date_fail_' . date("Y-m-d") . '.txt';
                                       $due_log_data = "\n" . date('d-m-Y H:i:s') . " EMPTY get_due_date for id_payment=" . $id_payment
                                           . " id_sch_ac=" . $data['id_sch_ac'] . " due_type=" . $actual_due_type
                                           . " dt_pay=" . $dt_pay . " client_id=" . $data['client_id'];
                                       file_put_contents($due_log_path, $due_log_data, FILE_APPEND | LOCK_EX);
                                   }
                               }

                            // ── POST-PAYMENT PROCESSING (ported from admin_payment saveall) ──────────
                            // getSyncSchemeData fetches scheme flags + paid_installments inline
                            // (mirrors admin payment_model->getSchemeData + getPaidInsData exact queries)
                            $sch_data       = $this->payment_modal->getSyncSchemeData($data['id_sch_ac']);
                            $paid_count     = (isset($sch_data['paid_installments']) ? (int)$sch_data['paid_installments'] : 0);
                            $payment_amount = $row->amount;
                            $metal_wgt      = (!empty($row->weight) ? $row->weight : 0);
                            $gold_rate      = (!empty($row->rate)   ? $row->rate   : 0);
                            $date_payment   = date('Y-m-d', strtotime(str_replace('/', '-', $row->payment_date)));

                            // 1. Rate fix for one-time premium schemes (first payment only)
                            if (!empty($sch_data['one_time_premium']) && $sch_data['one_time_premium'] == 1
                                && !empty($sch_data['rate_fix_by'])   && $sch_data['rate_fix_by']   == 0
                                && !empty($sch_data['rate_select'])   && $sch_data['rate_select']   == 1
                                && $gold_rate != 0
                            ) {
                                $isRateFixed = $this->payment_modal->isSyncRateFixed($data['id_sch_ac']);
                                if ($isRateFixed['status'] == 0) {
                                    $rateUpdData = array(
                                        'fixed_wgt'        => ($metal_wgt > 0 ? $metal_wgt : $payment_amount / $gold_rate),
                                        'firstPayment_amt' => $payment_amount,
                                        'fixed_metal_rate' => $gold_rate,
                                        'rate_fixed_in'    => 0,
                                        'fixed_rate_on'    => date('Y-m-d H:i:s'),
                                    );
                                    $this->payment_modal->updData($rateUpdData, 'id_scheme_account', $data['id_sch_ac'], 'scheme_account');
                                }
                            }
                            
                            // 2. First-payment: set start_date and calculate maturity date
                            if ($paid_count <= 1 && !empty($sch_data['maturity_type']) && $sch_data['maturity_type'] != 4) {
                                $this->payment_modal->updData(
                                    array('start_date' => $date_payment),
                                    'id_scheme_account', $data['id_sch_ac'], 'scheme_account'
                                );
                                if (!empty($sch_data['maturity_days']) && $sch_data['maturity_days'] > 0) {
                                    $maturity_date = date('Y-m-d', strtotime($date_payment . ' + ' . $sch_data['maturity_days'] . ' days'));
                                    $this->payment_modal->updData(
                                        array('maturity_date' => (!empty($sch_data['calc_maturity_date']) ? $sch_data['calc_maturity_date'] : $maturity_date)),
                                        'id_scheme_account', $data['id_sch_ac'], 'scheme_account'
                                    );
                                }
                            } else if (!empty($sch_data['maturity_type']) && $sch_data['maturity_type'] == 4) {
                                $this->payment_modal->calculate_maturityLapse_Date($data['id_sch_ac']);
                            }

                            // 3. Capture firstPayment_amt (flexible schemes)
                            if (
                                !empty($sch_data['firstPayamt_as_payamt']) && ($sch_data['firstPayamt_as_payamt'] == 1 ||
                                !empty($sch_data['firstPayamt_maxpayable']) && $sch_data['firstPayamt_maxpayable'] == 1)
                                && $paid_count == 0
                            ) {
                                $this->payment_modal->updData(
                                    array('firstPayment_amt' => $payment_amount),
                                    'id_scheme_account', $data['id_sch_ac'], 'scheme_account'
                                );
                            }

                            // 4. Capture firstpayment_wgt (weight-based schemes)
                            if (!empty($sch_data['firstPayment_as_wgt']) && $sch_data['firstPayment_as_wgt'] == 1 && $paid_count == 0) {
                                $this->payment_modal->updData(
                                    array('firstpayment_wgt' => (!empty($metal_wgt) ? $metal_wgt : 0.000)),
                                    'id_scheme_account', $data['id_sch_ac'], 'scheme_account'
                                );
                            }
                            // ── END POST-PAYMENT PROCESSING ───────────────────────────────────────────
                            // Update total_paid_ins using old_paid_installments from getPaidInsData:
                            //  - NOT paid_installments (COUNT DISTINCT installment) → broken for offline
                            //    because offline installment_no resets to 1 causing collision
                            //  - NOT COUNT(id_payment) → overcounts multi-chance schemes (2 pays/month = 1 install)
                            //  - old_paid_installments = date-based, scheme-aware:
                            //    monthly single-chance  → COUNT(payment rows)
                            //    monthly multi-chance   → COUNT(DISTINCT month)
                            //    daily single-chance    → COUNT(payment rows)
                            //    daily multi-chance     → COUNT(DISTINCT day)
                            //    days-duration cycle    → COUNT(due_date month)
                            $paid = $this->payment_modal->getPaidInsData($data['id_sch_ac']);
                            if (!empty($paid)) {
                                $this->payment_modal->updData(
                                    array('total_paid_ins' => (int)$paid['old_paid_installments']),
                                    'id_scheme_account', $data['id_sch_ac'], 'scheme_account'
                                );
                            }
    				    }elseif($row->is_modified == 1){
    				        //update if offline record is with cancelled status
                				$upd_array = array ( "payment_status" 	=> 4,
                			   					    'receipt_no'        =>$row->receipt_no,
                			   						"remark" 			=> $row->remarks,
                			   						"date_upd" 			=> date('Y-m-d H:i:s'),
                			   						"payment_ref_number"=> $row->ref_no
                			    					);
                			    $status = $this->db->insert('payment',$upd_array);
    				    }
    										
    				
    				 	 /*echo $this->db->_error_message();
    				 	echo $this->db->last_query();exit; */
    					if($status){
    					    $succeedIds[] = $row->id_transaction;
    					}
    					$i++;
					} else {
					    $existing_payment = $checkRef_no->row();
					    if ($existing_payment->is_offline == 0) {
					        $this->db->where('id_payment', $existing_payment->id_payment);
					        $this->db->update('payment', array('is_offline' => 1));
					    }
					    // Already synced — add it to succeedIds to ensure transaction table is marked transferred
					    $succeedIds[] = $row->id_transaction;
					}
				} 
			}else{
				if($this->config->item("integrationType") == 3 || $this->config->item("integrationType") == 2){ // Jewelone ERP or Sync Tool
			        $no_records += 1; 
			        // No pending transactions - scheme_account still needs to be registered
			    }
			} 
		}
		return array('succeedIds' => $succeedIds,'no_records' => $no_records);
	}
		
		
	function updateInterTableStatus($data,$payData)
	{	
	    $transtatus = false;
	    
	    foreach($data as $accdata){
	       
			$arrdata = array("id_scheme_account"=>$accdata['id_sch_ac'],"is_registered_online"=>1,"is_modified"=>0,"transfer_date"=>date('Y-m-d H:i:s'),'is_transferred'=>'Y');
			$this->db->where('clientid',$accdata['client_id']); 
			//$this->db->where('id_branch',$accdata['id_branch']); 
			$accstatus= $this->db->update('customer_reg',$arrdata);
			
			//during sync - Offline cus name should be updated to Online customer//HH
			$arrcusdata = array("firstname"=>$accdata['firstname']);
			$this->db->where('id_customer',$accdata['id_customer']); 
		
			$accstatus= $this->db->update('customer',$arrcusdata);
				//print_r($this->db->last_query());exit;
			
			if($accstatus){
			    if(sizeof($payData) == 0){
			        // No payment transactions to update — scheme_account only sync
			        $transtatus = TRUE;
			    } else {
			        foreach($payData as $data){
    		            $upddata = array("is_modified"=>0,"transfer_date"=>date('Y-m-d H:i:s'),'is_transferred'=>'Y');
    				    $this->db->where('id_transaction',$data); 
    				    $transtatus= $this->db->update('transaction',$upddata);
			        }
			    }
			}
		} 
		return $transtatus;
	} 
	
	// GiftedCards list //hh 
	function insertdatas($insData)
    {
		$status = $this->db->insert('gift_card_trans',$insData); 
		//print_r($this->db->last_query());exit;
		return array('status' => $status, 'insertID' => $this->db->insert_id());
	}
	
	function get_giftEmpryRecords()
	{
		$records = array();
		$sql = $this->db->query("SELECT concat(firstname,' ',lastname) as name FROM  customer WHERE  id_customer=".$this->session->userdata('cus_id'));
		return $sql->row_array();
	} 
	// GiftedCards list // 
	
	//Coin enquiry Form//HH
	function insCusFeedback($data)  
    {
		$status = $this->db->insert('cust_enquiry',$data);
	//	print_r($this->db->last_query());exit;
		return array('status' => $status, 'insertID' => $this->db->insert_id());
	}
	
    
    function insert_coin_data($data)
    {
		$status = $this->db->insert('cust_enquiry_product',$data); 
		//print_r($this->db->last_query());exit;
		return array('status' => $status, 'insertID' => $this->db->insert_id());
	}
	
	function get_coinenq_EmpryRecord()
	{
		$records = array();
		$sql = $this->db->query("SELECT concat(firstname,' ',lastname) as name,email,mobile FROM  customer WHERE  id_customer=".$this->session->userdata('cus_id'));
		return $sql->row_array();
	} 
	
	function getLastSyncTime($mbl){
	    $sql = $this->db->query("select last_sync_time from customer where mobile=".$mbl);
	    if($sql->num_rows() > 0){
	        return $sql->row()->last_sync_time;
	    }else{
	        return NULL;
	    }
	}
	
	
	/**
	 * Find already-registered accounts (is_registered_online=1) that still have
	 * pending offline transactions (is_transferred='N'). These are skipped by
	 * insExisAcByMobile which only looks for is_registered_online=0.
	 */
	function getAlreadyRegisteredPendingAc($mobile, $id_branch)
	{
		$result = array();
		// Find customer_reg rows that are already registered online but have pending transactions
		$sql = "SELECT cr.clientid, cr.id_scheme_account, cr.sync_scheme_code,
		               cr.firstname, cr.id_branch,
		               sa.id_scheme
		        FROM customer_reg cr
		        JOIN scheme_account sa ON sa.id_scheme_account = cr.id_scheme_account
		        WHERE cr.mobile = '" . $this->db->escape_str($mobile) . "'
		          AND cr.record_to = 2
		          AND cr.is_registered_online != 0
		          AND EXISTS (
		              SELECT 1 FROM transaction t
		              WHERE t.client_id = cr.clientid
		                AND t.is_transferred = 'N'
		                AND t.record_to = 2
		          )";
		$resultset = $this->db->query($sql);
		if ($resultset->num_rows() > 0) {
			foreach ($resultset->result() as $row) {
				$result[] = array(
					
					'id_branch'      => ($row->id_branch > 0 ? $row->id_branch : $id_branch),
					'client_id'      => $row->clientid,
					'id_sch_ac'      => $row->id_scheme_account,
					'id_scheme'      => $row->id_scheme,
					'firstname'      => $row->firstname,
					'mobile'         => $mobile,
					'added_by'       => 0,
				);
			}
		}
		return $result;
	}

	public function syncClosedAccounts($mobile)
	{
		$mobile_esc = $this->db->escape_str($mobile);
		// Find any closed accounts that are modified but not transferred
		$sql = "SELECT * FROM customer_reg 
		        WHERE mobile = '$mobile_esc' 
		          AND record_to = 2 
		          AND is_closed = 1 
		          AND is_modified = 1 
		          AND is_transferred = 'N'";
		$res = $this->db->query($sql);
		if ($res->num_rows() > 0) {
			foreach ($res->result_array() as $row) {
				// Only update if clientid is not empty
				if (!empty($row['clientid'])) {
					// Check if scheme_account exists with this ref_no
					$chk = $this->db->query("SELECT id_scheme_account FROM scheme_account WHERE ref_no = '" . $this->db->escape_str($row['clientid']) . "'");
					if ($chk->num_rows() > 0) {
						$acc_data = array(
							'closed_by'         => $row['closed_by'],
							'closing_date'      => $row['closing_date'],
							'closing_amount'    => ($row['closing_amount'] !== null ? $row['closing_amount'] : 0.00),
							'closing_weight'    => ($row['closing_weight'] !== null ? $row['closing_weight'] : 0.000),
							'closing_add_chgs'  => ($row['closing_add_chgs'] !== null ? $row['closing_add_chgs'] : 0),
							'additional_benefits' => ($row['additional_benefits'] !== null ? $row['additional_benefits'] : 0),
							'remark_close'      => $row['remark_close'],
							'is_closed'         => 1,
							'active'            => 0,
							'date_upd'	        => date("Y-m-d H:i:s")
						);
						$this->db->where('ref_no', $row['clientid']);
						$this->db->update('scheme_account', $acc_data);
						
						// Now update customer_reg to is_transferred = 'Y'
						$inter_data = array(
						    'is_transferred' => 'Y', 
						    'is_modified' => 'N', 
						    'transfer_date' => date('Y-m-d')
						);
						$this->db->where('clientid', $row['clientid']);
						if (!empty($row['id_branch'])) {
						    $this->db->where('id_branch', $row['id_branch']);
						} else {
						    $this->db->where('id_branch', null);
						}
						$this->db->update('customer_reg', $inter_data);
					}
				}
			}
		}
	}

	function updateLastSyncTime($id_customer)
	{
    	$this->db->where('id_customer',$id_customer); 
		$cus_info=$this->db->update('customer',array("last_sync_time" => date("Y-m-d H:i:s")));	
		return $cus_info;
	}

	public function get_scheme_sync_info($id_scheme_account)
	{
		$sql = "SELECT 
				sa.id_scheme_account,
				s.installment_cycle,
				IFNULL(s.max_chance, 0) as max_chance,
				IFNULL(s.scheme_type, 0) as scheme_type,
				IFNULL(s.is_digi, 0) as is_digi,
				IFNULL(s.payment_chances, 0) as payment_chances,
				IFNULL(s.flexible_sch_type, 0) as flexible_sch_type,
				s.allow_advance_in,
				s.allow_unpaid_in,
				s.allow_advance,
				IF(s.allow_advance = 1, s.advance_months, 0) as advance_months,
				s.allow_unpaid,
				IF(s.allow_unpaid = 1, s.unpaid_months, 0) as allow_unpaid_months,
				IFNULL(sa.total_paid_ins, 0) as paid_installments,
				s.total_installments
			FROM scheme_account sa
			LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
			WHERE sa.id_scheme_account = ?";
		
		$query = $this->db->query($sql, array($id_scheme_account));
		return $query->row_array();
	}

	public function get_sync_due_type($id_scheme_account, $date = '')
	{
		if (empty($date)) {
			$date = date('Y-m-d');
		} else {
			$date = date('Y-m-d', strtotime($date));
		}

		$record = $this->get_scheme_sync_info($id_scheme_account);
		if (empty($record)) {
			return 'ND';
		}
		
		$record = (object)$record;
		$due_type = 'ND';
		
		if ($record->installment_cycle == 3 || $record->installment_cycle == 2 || $record->installment_cycle == 1 || $record->installment_cycle == 0) {
			$allowed_due = 0;
			$paid_normal_due = 0;
			$paid_advance_due = 0;
			$paid_pending_due = 0;
			$paid_due = 0;
			$remaining_normal_due = 0;
			$remaining_advance_due = 0;
			$remaining_pending_due = 0;
			$remaining_due = 0;
			$paid_multiple_chance = 0;
			$chances_allowed_due = 0;
			$max_chance = $record->max_chance;
			
			//get range for date....
			$range = $this->mobileapi_modal->get_due_date('current_range', $date, $record->id_scheme_account);
			
			if (!empty($range)) {
				//take the no of paid dues with due_type customer paid already...
				$paid_dueData = $this->db->query("SELECT due_type as due_name, COUNT(due_type) as dues_count 
											FROM payment where payment_status = 1 and id_scheme_account = " . $record->id_scheme_account . " 
											and date(date_payment) BETWEEN '" . $range[0]['due_date_from'] . "' AND '" . $range[0]['due_date_to'] . "'
											group by due_type")->result_array();
				foreach ($paid_dueData as $due) {
					if ($due['due_name'] == 'ND') {
						$paid_normal_due = $due['dues_count'];
					} else if ($due['due_name'] == 'AD') {
						$paid_advance_due = $due['dues_count'];
					} else if ($due['due_name'] == 'PD') {
						$paid_pending_due = $due['dues_count'];
					} else {
						$paid_multiple_chance = $due['dues_count'];
					}
				}
			}
			
			//take the no of remaining dues with due_type customer want to pay..
			$remaining_dueData = $this->payment_modal->get_due_date('allow_pay', $date, $record->id_scheme_account);
			foreach ($remaining_dueData as $due) {
				if ($due['due_name'] == 'ND') {
					$remaining_normal_due = $due['dues_count'];
				} else if ($due['due_name'] == 'AD') {
					$remaining_advance_due = $due['dues_count'];
				} else if ($due['due_name'] == 'PD') {
					$remaining_pending_due = $due['dues_count'];
				} else {
					$remaining_due = $due['dues_count'];
				}
			}
			
			//calculate can pay advance due and pending dues...
			$chances_allowed_due = $max_chance - ($paid_multiple_chance + $paid_normal_due);
			$allow_advance_in = explode(',', $record->allow_advance_in);
			$allow_unpaid_in = explode(',', $record->allow_unpaid_in);
			
			if ($chances_allowed_due <= 1 && (($record->allow_advance == 1 && $record->advance_months > 0 && (in_array('1', $allow_advance_in) || in_array('4', $allow_advance_in)) && $remaining_advance_due > 0) || ($record->allow_unpaid == 1 && $record->allow_unpaid_months > 0 && (in_array('1', $allow_unpaid_in) || in_array('4', $allow_unpaid_in)) && $remaining_pending_due > 0))) {
				//advance..
				$sch_advance = $record->advance_months;
				$cur_advance = ($remaining_advance_due > 0 ? ($sch_advance < $remaining_advance_due && $remaining_advance_due > 0 && $paid_advance_due < $sch_advance ? $sch_advance : abs($sch_advance - $paid_advance_due)) : 0);
				$canPay_advance = ($remaining_advance_due < $cur_advance ? $remaining_advance_due : $cur_advance);
				//pending
				$sch_unpaid = $record->allow_unpaid_months;
				$cur_unpaid = ($remaining_pending_due > 0 ? ($sch_unpaid < $remaining_pending_due && $remaining_pending_due > 0 && $paid_pending_due < $sch_unpaid ? $sch_unpaid : abs($sch_unpaid - $paid_pending_due)) : 0);
				$canPay_pending = ($remaining_pending_due > $cur_unpaid ? $cur_unpaid : $remaining_pending_due);
				
				if ($remaining_normal_due == 0 && $canPay_pending > 0) {            //only pending
					$due_type = 'PD';
				} else if ($remaining_normal_due == 0 && $canPay_advance > 0) {        //only advance
					$due_type = 'AD';
				} else if ($remaining_normal_due > 0 && $canPay_pending > 0) {        //normal + pending
					$due_type = 'PN';
				} else if ($remaining_normal_due > 0 && $canPay_advance > 0) {        //normal + advance
					$due_type = 'AN';
				}
			} else {
				if ($chances_allowed_due != 0 && $paid_normal_due != 0 && $max_chance > 1 && sizeof($range) > 0 && $max_chance > $chances_allowed_due) {
					$due_type = 'MND';
				} else {
					$due_type = 'ND';
				}
			}
		}
		
		return $due_type;
	}
    function get_due_date($due_type, $date_payment, $id_scheme_account)
    {
        $result = [];
        // print_r($due_type);exit;
        $where = '';
        $sch = $this->get_scheme_details($id_scheme_account);
        $now = date('Y-m-d');
        $first_payment_date = (!empty($sch['first_payment_date']) ? $sch['first_payment_date'] : $date_payment);
        $c_wh = "and  dt.due_date_from NOT IN (SELECT p.due_date from payment p where p.payment_status = 1 and p.due_date is not null and p.id_scheme_account = sa.id_scheme_account) limit 1";
        if ($due_type == 'ND' || $due_type == '') {
            $where = "and  date('" . $date_payment . "') BETWEEN dt.due_date_from and dt.due_date_to " . $c_wh . " ";
        } else if ($due_type == 'AD') {
            $where = "and dt.due_date_from >= date('" . $date_payment . "')  " . $c_wh . " ";
        } else if ($due_type == 'PD') {
            $where = "and dt.due_date_from <= date('" . $date_payment . "') " . $c_wh . " ";
        } else if ($due_type == 'allow_pay') {
            $where = "and  dt.due_date_from NOT IN (SELECT p.due_date from payment p where p.payment_status = 1 and p.due_date is not null and p.id_scheme_account = sa.id_scheme_account)";
        } else if ($due_type == 'MND') {
            $where = "and '" . $date_payment . "' BETWEEN dt.due_date_from AND dt.due_date_to ";
        } else if ($due_type == 'current_range') {
            $where = "and  date('" . $date_payment . "') BETWEEN dt.due_date_from and dt.due_date_to";
        } else {
            $where = $c_wh;
        }
        if ($sch['installment_cycle'] == 2) {  //by days duration cycle
            $days_duration = $sch['ins_days_duration'] - 1;
            $grace_days = $sch['grace_days'] - 1;
            $sql = "SELECT 		if(
    			dt.due_date_to < '" . $date_payment . "' ,
    			'PD',
    			if( '" . $date_payment . "' BETWEEN dt.due_date_from AND dt.due_date_to  , 
    				'ND',
    				if(
                       dt.due_date_to >= '" . $date_payment . "'
                        ,'AD','-'
                    )
    			) 
    		) as due_type,
    		dt.installment,dt.due_date_from,dt.due_date_to,dt.grace_date,
    		if((('" . $due_type . "' = 'ND' OR '" . $due_type . "' = '') AND date('" . $date_payment . "') BETWEEN dt.due_date_from and dt.grace_date) OR ('" . $due_type . "' = 'AD')  , '0','1') as is_limit_exceed	
    		FROM scheme_account sa
    		JOIN (SELECT @sno := @sno + 1 as installment,
    			@due_Date_from := if(@sno = 1, '" . $first_payment_date . "',date_add(@pay_date ,INTERVAL " . $sch['ins_days_duration'] . " day )) as due_date_from,
    			@due_Date_to := if(@sno = 1, date_add('" . $first_payment_date . "',INTERVAL " . $days_duration . " day ),date_add(@due_Date_from,INTERVAL " . $days_duration . " day )) as due_date_to,  
    			@grace_date := if(@sno = 1, date_add('" . $first_payment_date . "',INTERVAL " . $grace_days . " day ),date_add(@due_Date_from,INTERVAL " . $grace_days . " day )) as grace_date,
    			@pay_date := if(@sno = 1,'" . $first_payment_date . "',@due_Date_from) as due_pay_date
    			FROM access
    			join (SELECT @pay_date := if(@sno = 1,'" . $first_payment_date . "',@due_Date_from), @sno := 0 ) as t
    			limit " . $sch['total_installments'] . "
    		) as dt
    		WHERE  sa.id_scheme_account = " . $id_scheme_account . "  " . $where . " ";
            $pay = $this->db->query($sql)->result_array();
        }
        //daily payment cycle
        // else if ($sch['installment_cycle'] == 1) {
        else if ($sch['installment_cycle'] == 1 || $sch['installment_cycle'] == 3) {
            $sql = "SELECT 		if(
        		dt.due_date_to < '" . $date_payment . "' ,
        		'PD',
        		if( '" . $date_payment . "' = dt.due_date_from  , 
        			'ND',
        			if(
                       dt.due_date_to > '" . $date_payment . "'
                        ,'AD','-'
                    )
        		) 
        	) as due_type,
        	dt.installment,dt.due_date_from,dt.due_date_to
        	FROM scheme_account sa
        	JOIN (SELECT @sno := @sno + 1 as installment,
        		@due_Date_from := if(@sno = 1, '" . $first_payment_date . "',date_add(@pay_date ,INTERVAL 1 day )) as due_date_from,
        		@due_Date_to := @due_Date_from  as due_date_to,  
        		@pay_date := if(@sno = 1,'" . $first_payment_date . "',@due_Date_from) as due_pay_date
        		FROM access
        		join (SELECT @pay_date := if(@sno = 1,'" . $first_payment_date . "',@due_Date_from), @sno := 0 ) as t
        		limit " . $sch['total_installments'] . "
        	) as dt
        	WHERE  sa.id_scheme_account = " . $id_scheme_account . "  " . $where . " ";
            $pay = $this->db->query($sql)->result_array();
        }
        //monthly cycle    
        else if ($sch['installment_cycle'] == 0) {
            $sql = "SELECT 		if(
            			dt.due_date_to < '" . $date_payment . "' ,
            			'PD',
            			if( '" . $date_payment . "' BETWEEN dt.due_date_from AND dt.due_date_to  , 
            				'ND',
            				if(
                               dt.due_date_to >= '" . $date_payment . "'
                                ,'AD','-'
                            )
            			) 
            		) as due_type,
                    dt.installment,
                    dt.due_date_from,
                    dt.due_date_to 
                    FROM scheme_account sa 
                    JOIN (SELECT @sno := @sno + 1 as installment, 
                          @due_Date_from := if(@sno = 1, date_format('" . $first_payment_date . "','%Y-%m-01'),date_add(@pay_date ,INTERVAL 1 month )) as due_date_from, 
                          @due_Date_to := LAST_DAY(@due_Date_from) as due_date_to, 
                          @pay_date := if(@sno = 1,date_format('" . $first_payment_date . "','%Y-%m-01'),@due_Date_from) as due_pay_date 
                          FROM access 
                          join (SELECT @pay_date := if(@sno = 1,date_format('" . $first_payment_date . "','%Y-%m-01'),@due_Date_from), @sno := 0 ) as t limit " . $sch['total_installments'] . " 
                         ) as dt 
                    WHERE sa.id_scheme_account = " . $id_scheme_account . "  " . $where . " ";
            $pay = $this->db->query($sql)->result_array();
        }
        // print_r($this->db->last_query());exit;
        if ($due_type == 'allow_pay') {
            foreach ($pay as $p) {
                $grouped_dues[$p['due_type']][] = $p;
            }
            foreach ($grouped_dues as $key => $gd) {
                $result[] = array('due_name' => $key, 'dues_count' => sizeof($gd));
            }
        } else {
            $result = $pay;
        }
        return $result;
    }
	function get_scheme_details($id_scheme_account)
    {
        $sql = $this->db->query("SELECT s.total_installments,s.grace_days,s.installment_cycle,s.ins_days_duration,
		(SELECT MIN(date(p.date_payment)) FROM payment p WHERE p.payment_status = 1 and p.id_scheme_account = sa.id_scheme_account) as first_payment_date,	
	    s.scheme_name,c.firstname as cus_name,IFNULL(c.lastname,'') as lastname,c.mobile,sa.account_name,concat(s.code,'-',sa.scheme_acc_number) as scheme_acc_number,sa.firstPayment_amt,sa.received_wgt,sa.fixed_metal_rate,sa.fixed_wgt,sa.fixed_rate_on,sa.maturity_date,s.otp_price_fix_type,s.one_time_premium,
	    date_format(sa.start_date,'%d-%m-%Y') as start_date,date_format(sa.fixed_rate_on,'%d-%m-%Y') as fixed_rate_on,IFNULL(s.description,'') as description,
	    s.emp_refferal,s.emp_incentive_closing,s.ref_benifitadd_ins_type,s.ref_benifitadd_ins,sa.id_employee,s.firstPayamt_as_payamt,
	    s.emp_refferal_value
        FROM scheme_account sa 
        LEFT JOIN scheme s ON s.id_scheme=sa.id_scheme
        LEFT JOIN customer c ON c.id_customer=sa.id_customer
        WHERE sa.id_scheme_account=" . $id_scheme_account . "");
        //print_r($this->db->last_query());exit;
        return $sql->row_array();
    }
	function sync_existing_data($mobile, $id_customer, $id_branch = '')
	{
		if (empty($mobile) || empty($id_customer)) {
			return array("status" => FALSE, "msg" => "Invalid parameters");
		}
		$this->load->model('payment_modal');
		$log_dir = 'log/' . date("Y-m-d");
		if (!is_dir($log_dir . '/existing')) {
			mkdir($log_dir . '/existing', 0777, true);
		}
		$log_path = $log_dir . '/existing/' . date("Y-m-d") . '.txt';
		$allow_sync = false;
		$last_sync_time = $this->getLastSyncTime($mobile);
		if (!empty($last_sync_time)) {
			$fifteen_min_earlier = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) - (15 * 60));
			if (strtotime($last_sync_time) <= strtotime($fifteen_min_earlier)) {
				$allow_sync = true;
			} else {
				$allow_sync = false;
			}
		} else {
			$allow_sync = true;
		}
		if ($allow_sync) {
			$data['id_customer'] = $id_customer;
			$data['id_branch'] = $id_branch;
			$data['branch_code'] = ($id_branch > 0 ? $this->getBranchCode($id_branch) : NULL);
			$data['branchWise'] = 0;
			$data['mobile'] = $mobile;
			$this->db->trans_begin();
			$this->updateLastSyncTime($id_customer);
			$res = $this->insExisAcByMobile($data);
			if (sizeof($res) > 0) {
				$payData = $this->syncPayData($res);
				if (sizeof($payData['succeedIds']) > 0 || $payData['no_records'] > 0) {
					$status = $this->updateInterTableStatus($res, $payData['succeedIds']);
					if ($status === TRUE && $this->db->trans_status() === TRUE) {
						$this->db->trans_commit();
						$TESTRes = array("status" => "On ENTER", "e" => $this->db->_error_message(), "q" => $this->db->last_query(), "res" => $res, "data" => $data);
						$logData = "\n" . date('d-m-Y H:i:s') . "\n Model : registration_model \n Response : " . json_encode($TESTRes, true);
						file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
						return array("status" => TRUE, "msg" => "Purchase Plan registered successfully");
					} else {
						$this->db->trans_rollback();
						$response = array("status" => FALSE, "e" => $this->db->_error_message(), "q" => $this->db->last_query(), "msg" => "Error in updating intermediate tables");
						$logData = "\n" . date('d-m-Y H:i:s') . "\n Model : registration_model \n Response : " . json_encode($response, true);
						file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
						return $response;
					}
				} else {
					$response = array("status" => FALSE, "e" => $this->db->_error_message(), "q" => $this->db->last_query(), "msg" => "Error in updating payment tables, kindly check payment data.");
					$logData = "\n" . date('d-m-Y H:i:s') . "\n Model : registration_model \n Response : " . json_encode($response, true);
					file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
					$this->db->trans_rollback();
					return $response;
				}
			} else {
				$response = array("status" => FALSE, "e" => $this->db->_error_message(), "q" => $this->db->last_query(), "msg" => "No records to update in scheme account tables");
				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();
				} else {
					$this->db->trans_rollback();
				}
				$logData = "\n" . date('d-m-Y H:i:s') . "\n Model : registration_model \n Response : " . json_encode($response, true);
				file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
				return $response;
			}
		} else {
			$logData = "\n" . date('d-m-Y H:i:s') . "\n Model : registration_model \n sync called less than 15 min";
			file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
			return array("status" => FALSE, "msg" => "sync called less than 15 min");
		}
	}
}
?>