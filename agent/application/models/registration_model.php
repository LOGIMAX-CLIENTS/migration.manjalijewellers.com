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
	/*function insert_data($code="")
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
	} */
	
// agent registration data insert function 

		function insert_data($code="")
	{

		$branch=$this->input->post('id_branch');
		if($branch!=null||$branch!=''){
			$id_branch=$branch;	
		}else{	
		   $id_branch=null;	
		}
		
		$address1=$this->input->post('address1');
		$address2=$this->input->post('address2');
		$id_state=$this->input->post('id_state');
		$id_city=$this->input->post('id_city');
		$id_country=$this->input->post('id_country');
		
		$agentInsert  = array(
					"title" => $this->input->post('title'),
					"firstname" => ucfirst($this->input->post('firstname')),
					"mobile" => trim($this->input->post('mobile')),
					"email" => $this->input->post('email'),
					"passwd" => trim($this->__encrypt($this->input->post('passwd'))),
					"active" => 1,
					"date_add" => date('Y-m-d H:i:s'),
					"gender"=> -1,
					'agent_code'=>trim($this->input->post('mobile')),
					"id_branch"=>$id_branch
		);
		
		
		if($this->db->insert('agent', $agentInsert))
		{
		     $insertID = $this->db->insert_id();
                if($insertID)
                {
					$agentAddress = array(
								'id_agent '   => $insertID,
								'address1'			=>	(isset($address1)?$address1:NULL),
								'address2'			=>	(isset($address2)?$address2:NULL),
								'id_country'        =>  101,
								//'id_country'		=>	(isset($id_country)?$id_country:NULL),
								'id_state'          =>  (isset($id_state)?$id_state:NULL),
								'id_city'			=>	(isset($id_city)?$id_city:NULL),
								
								);
						$result=$this->db->insert('address',$agentAddress);
						//print_r($this->db->last_query());exit;
					
						if($result){						
							$id_address=$this->db->insert_id();
							$address = array('id_address' => $id_address);
							$this->db->where('id_agent',$insertID); 
							$this->db->update('agent',$address);
							//print_r($this->db->last_query());exit;
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
		//$query = $this->db->query("SELECT * FROM customer WHERE mobile=".$mobile);
		$query = $this->db->query("SELECT * FROM agent WHERE mobile=".$mobile);
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
		//$resultset = $this->db->query("select email from customer where email='".$id."'");
		$resultset = $this->db->query("select email from agent where email='".$id."'");
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
	
	function insExisAcByMobile($data) 
	{
	   $result = array();
	   if($data['branch_code'] > 0 && $data['branch_code'] != NULL && $data['branch_code'] != ""){ // Only for SCM and TKTM
	       $resultset = $this->db->query("select * from customer_reg where record_to=2 and is_registered_online=0 and is_closed=0 and branch_code='".$data['branch_code']."' and mobile=".$data['mobile']);
	   }
	   else if($data['id_branch'] > 0 && ($data['id_branch'] != '' || $data['id_branch'] != NULL)){
	       $resultset = $this->db->query("select * from customer_reg where record_to=2 and is_registered_online=0 and is_closed=0 and id_branch='".$data['id_branch']."' and mobile=".$data['mobile']);
	   }
	   else{
	         $resultset = $this->db->query("select * from customer_reg where record_to=2 and is_registered_online=0 and is_closed=0 and mobile=".$data['mobile']);
	   } 
		if($resultset->num_rows() > 0 ){
			$records = array();
			foreach($resultset->result() as $row)
			{
			    $data['sync_scheme_code'] = $row->sync_scheme_code;
			    $data['client_id'] = $row->clientid;
			    $data['firstname']  =$row->firstname;
			    //print_r($data['firstname']);exit;
			    $id_scheme = $this->getschId($data);
			    if($id_scheme > 0){
					$records = array( 	'id_customer' 		=> $data['id_customer'],
									'id_scheme'			=> $id_scheme,
									'scheme_acc_number' => $row->scheme_ac_no,
									'ref_no'            => $row->clientid,
									'account_name' 		=> $row->account_name,
									'group_code' 		=> $row->group_code,
									'start_date' 		=> $row->reg_date,
									'maturity_date' 	=> $row->maturity_date,
									'is_new' 			=> $row->new_customer,
									'firstPayment_amt'	=> $row->firstPayment_amt,
									'firstpayment_wgt'  => $row->firstpayment_wgt,
									'fixed_rate_on' 	=> $row->fixed_rate_on,
									'fixed_metal_rate' 	=> $row->fixed_metal_rate,
									'fixed_wgt' 		=> $row->fixed_wgt,
									'id_branch' 		=> ($row->id_branch > 0 ? $row->id_branch : $data['id_branch'] ),
									'date_add' 			=> date("Y-m-d H:i:s"),
									'is_registered' 	=> 1,
									'active' 			=> 1, 
									'added_by' 			=> isset($data['added_by']) ? $data['added_by'] : 0
								); 
					$status = $this->db->insert('scheme_account',$records);					
					//echo '$updateCus-true';exit; 
					if($status){
					    
						$data['id_sch_ac'] = $this->db->insert_id();
						$result[] =  $data;
					}
				}
				 
			} 
		}
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
				    if($row->is_modified == 0){
    					$records = array( 'id_scheme_account'   =>$data['id_sch_ac'],
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
    	                                    'is_offline'	    => 1,
    	                                    'due_type'          =>$row->due_type,
    	                                    'due_month'         =>$row->due_month,
    	                                    'due_year'          =>$row->due_year,
    	                                    'added_by' 			=> isset($data['added_by']) ? $data['added_by'] : 0,
    	                                    'installment'       => $row->installment_no,
    	                                    'discountAmt'       =>(!empty($row->discountAmt) ? $row->discountAmt :	0.00)
    	                                );
    	               	$status = $this->db->insert('payment',$records);
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
					
				} 
			}else{
				$no_records += 1; 
				// There wont be any reords for plan 2 & 3 schemes so by default set valuse as 1.
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
			    foreach($payData as $data){
    		        $upddata = array("is_modified"=>0,"transfer_date"=>date('Y-m-d H:i:s'),'is_transferred'=>'Y');
    				$this->db->where('id_transaction',$data); 
    				$transtatus= $this->db->update('transaction',$upddata);
    				/*echo $this->db->last_query();
    				echo $this->db->_error_message();
    				var_dump($transtatus);*/
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
    
}
?>