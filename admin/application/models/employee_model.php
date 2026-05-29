<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employee_model extends CI_Model

{

	const TABLE_NAME ="employee";

	const ADDRESS_TABLE ="address";
	


	

	function __construct()

    {

        parent::__construct();

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

    

    public function get_empty_record()

    {

		$data=array( 'id_employee' 		=> NULL,

					  'firstname' 		=> NULL,

					  'lastname' 		=> NULL,

					  'date_of_birth' 	=> NULL,

					  'emp_code' 		=> NULL,

					  'dept' 			=> 0, 

					  'designation' 	=> 0,
					  
					  'active'			=> 1,	

					  'date_of_join' 	=> NULL,

					  'email'			=> NULL,

					  'mobile' 			=> NULL,

					  'phone' 			=> NULL,

					  'username' 		=> NULL,

					  'passwd' 			=> NULL,

					  'id_profile' 		=> 0,

					  'comments' 		=> NULL,

					  'id_country' 		=> 0,

					  'id_state' 		=> 0,

					  'id_city' 		=> 0,

					  'address1' 		=> NULL,

					  'address2' 		=> NULL,

					  'address3' 		=> NULL,

					  'pincode' 		=> NULL );

					   

		return $data;

	}

    

    public function get_designation()

    {

      $this->db->select('id_design, name');

	  $designations = $this->db->get('designation');    	

   	  foreach($designations->result() as $designation)

   	  {

	  	$design_data[] = array('id'=> $designation->id_design,

	  							'name'=>$designation->name);

	  }

   	  return json_encode($design_data);

	} 

	

	public function get_dept()
    {

		

		$this->db->select('id_dept, name');

		$depts = $this->db->get('department');     	

   	  foreach($depts->result() as $dept)

   	  {

	  	$dept_data[] = array('id'=> $dept->id_dept,

	  							'name'=>$dept->name);

	  }

   	  return json_encode($dept_data);

	}

	

	public function get_design()

    {

		$this->db->select('id_design,name');

		$design_data=$this->db->get('designation');

		return $design_data->result_array();

	}

	

	public function check_username($username,$emp_id="")

	{
	    
	    $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');

		$this->db->select('username');

		$this->db->where('username',$username); 
		
		if($company_settings == 1 && $id_company != '')
        {
			$this->db->where('id_company', $id_company); 
		}
		
		if($emp_id)

        {

			$this->db->where('id_employee <>', $emp_id); 

		}			

		

		$status=$this->db->get(self::TABLE_NAME);

		

		if($status->num_rows()>0)

		{

			return TRUE;

		}	

	   

	}

/* -- coded by ARVK -- */

    public function mobile_available($mobile,$id_employee="")
	{
	    $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        
		$this->db->select('mobile');
		$this->db->where('mobile', $mobile);
		
		if($company_settings == 1 && $id_company != '')
        {
			$this->db->where('id_company', $id_company); 
		}
		
        if($id_employee)
        {
			$this->db->where('id_employee <>', $id_employee); 
		}	
		
		$status=$this->db->get(self::TABLE_NAME);
	  
		if($status->num_rows()>0)
		{
			return TRUE;
		}
	}

/* -- / coded by ARVK -- */       

    /** Get all employee records  **/

    public function get_list_data()

    {

    	$userType = $this->session->userdata('profile');

		$this->db->_protect_identifiers = false;

		$this->db->select('employee.mobile, employee.emp_code, employee.id_employee, employee.firstname, employee.lastname, department.name as dept, employee.username, profile.profile_name as usertype, employee.active, IFNULL(branch.name,"ALL") as branch_name,
		COALESCE(GROUP_CONCAT(CONCAT(ed.id_collection_device, "~~", ed.device_uuid, "~~", COALESCE(ed.device_type," "), "~~", COALESCE(DATE_FORMAT(ed.date_add, "%d-%m-%Y %H:%i:%s")," "), "~~", COALESCE(ed.app_type," "), "~~", COALESCE(ed.device_info," "), "~~", ed.device_status) SEPARATOR "|"),"") AS emp_devices');
         
         $this->db->from('employee');

		 $this->db->join('employee_devices ed','ed.emp_id=employee.id_employee','left');

          $is_multi_company=$this->admin_settings_model->get_company_settings();
   	    if($is_multi_company == 1){
         $this->db->where('employee.id_company', $id_company); 
        }

         
         if($userType!=1)

         {

		 	$this->db->where('employee.id_profile != ','1');

		 }

		 

	     $this->db->join('department','department.id_dept=employee.dept','left');

	     $this->db->join('profile','profile.id_profile=employee.id_profile','left');
	     
	     $this->db->join('branch','branch.id_branch=employee.login_branches','left');

		 $this->db->group_by('employee.id_employee');

       $list_data=$this->db->get();
	   //echo $this->db->last_query();exit;
	   

		//$this->db->select('id_employee,firstname,lastname,dept,username,active');

		//$list_data=$this->db->get(self::TABLE_NAME);

		

		return $list_data->result_array();

	}
	
	/*function authenticate_user_id($username,$passwd)
	{
		$this->db->where('username',$username);

		$this->db->where('passwd',$this->__encrypt($passwd));

		$this->db->where('active',1);

		$r=$this->db->get(self::TABLE_NAME);

		

		if($r->num_rows==1)
		{
			$result=$r->row_array();
			return $result;
		}
		else
		{
			return array('status'=>2,'msg'=>'Invalid');
		}
		    
		
		}
*/

	function authenticate_user_id($username,$passwd)
	{
	    $username = $this->db->escape($username);
		$sql = "SELECT  
                e.username,login_branches,b.name as branch_name,p.req_otplogin,e.id_profile,
                e.id_employee,e.firstname,e.lastname,e.mobile,e.email,e.id_branch,e.image,
                e.pwd_hash 
                From employee e 
                Left join branch b on b.id_branch = e.id_branch 
                Left join profile p on p.id_profile = e.id_profile 
                where e.active = 1 and e.username = $username LIMIT 1"; // and e.passwd='".$this->__encrypt($passwd)."'
		$r = $this->db->query($sql);
		if($r->num_rows==1)
		{
		    if (password_verify($passwd, $r->row()->pwd_hash)) {
                $result = $r->row_array();
			    return $result;
            } else {
                return array('status'=>2,'msg'=>'Invalid password');
            }
			
		}
		else
		{
			return array('status'=>2,'msg'=>'Invalid');
		}
	}
	

	/** Get selected employee record **/	


	 public function get_emp_record($id)

    {

		$this->db->select('id_employee,firstname,lastname,date_of_birth,emp_code,active,dept,designation,date_of_join,email,mobile,phone,username,passwd,comments,id_profile,id_branch,
		login_branches');

		$this->db->where('id_employee', $id); 

		$list_data=$this->db->get(self::TABLE_NAME);
		

		return $list_data->row_array();

	} 	

	

	/** Get employee record  by username**/	

	 public function get_emp_by_username($name)

    {

		$this->db->select('id_employee,date_of_birth,emp_code,id_profile,username,id_branch,login_branches');

		$this->db->where('username', $name); 

		$list_data=$this->db->get(self::TABLE_NAME);
		
				//print_r($this->db->last_query());exit;


		return $list_data->row_array();

	} 

	

	public function get_emp_address($id)

    {

		$this->db->select('id_country,id_state,id_city,address1,address2,address3,pincode');

		$this->db->where('id_employee', $id); 

		$list_data=$this->db->get(self::ADDRESS_TABLE);

		return $list_data->row_array();

	}

	

	public function get_emp_id($emp_name)

	{

		$this->db->select('id_employee');

		$this->db->where('username', $emp_name); 

		$list_data=$this->db->get(self::TABLE_NAME);

		return $list_data->row_array();

	}

	

	/** Insert Employee record	**/

	public function insert_employee($data)

	{

		$add_flag=0;		

	 

		$emp_info= $this->db->insert(self::TABLE_NAME,$data[0]);
        // print_r($this->db->last_query());exit;
		if($emp_info)

		{

			$emp_id=$this->db->insert_id();

			$data[1]['id_employee']=$emp_id;

			$emp_address=$this->db->insert(self::ADDRESS_TABLE,$data[1]);

			

			if($emp_address)

			{

				$add_flag=1;

			}

			else

			{

				$add_flag=0;	

			}

		}

		 

		 return ($add_flag==1?$emp_id:0);

	}

	

	/** Update Employee record	**/

	public function update_employee($data)

	{

		$emp_id= $data[0] ['id_employee'];		

		$add_flag=0;		

	    $this->db->where('id_employee',$emp_id);    

		$emp_info= $this->db->update(self::TABLE_NAME,$data[0]);

		if($emp_info)

		{

			

		 $chk_emp_add_exists = $this->db->query("SELECT * from address where id_employee='.$emp_id.'");
           
           
           if($chk_emp_add_exists -> num_rows() > 0){
           $this->db->where('id_employee',$emp_id);    

			$emp_address=$this->db->update(self::ADDRESS_TABLE,$data[1]);
               
           }else{
	            
    		    $data[1]['id_employee']=$emp_id;
    
    			$emp_address=$this->db->insert(self::ADDRESS_TABLE,$data[1]);
    		    
		    }

			

			if($emp_address)

			{

				$add_flag=1;

			}

			else

			{

				$add_flag=0;	

			}

		}

		 

		 return ($add_flag==1?TRUE:FALSE);

	}

	

	/** Delete employee record **/

	 public function delete_emp_record($id)

    {

    	

        $this->db->where('id_employee', $id);

        $child= $this->db->delete(self::ADDRESS_TABLE);

        if($child)

        {

		  $this->db->where('id_employee', $id);

           $status= $this->db->delete(self::TABLE_NAME); 

		}

  

		

		return $status;

	} 

	

	  /**** Login ****/	

   	 function authenticate_user($username,$passwd)

	{

		$this->db->where('username',$username);

		$this->db->where('passwd',$this->__encrypt($passwd));

		$this->db->where('active',1);

		$r=$this->db->get(self::TABLE_NAME);

		    

		if($r->num_rows==1)

		{

			return true;

		}

	}

	

	public function check_password($id_emp,$pswd)

	{

		$this->db->select('passwd');

		$this->db->where('id_employee', $id_emp); 

		$this->db->where('passwd', $pswd); 

		

		$status=$this->db->get(self::TABLE_NAME);

		

		if($status->num_rows()>0)

		{

			return TRUE;

		}	

	}	
	
	 function isOTPReqToLogin()
	{
		 $sql="Select isOTPReqToLogin from chit_settings where id_chit_settings = 1";
		 return $this->db->query($sql)->row()->isOTPReqToLogin;
	}
	function loginOTP_exp()
	{
		 $sql="Select loginOTP_exp from chit_settings where id_chit_settings = 1";
		 return $this->db->query($sql)->row()->loginOTP_exp;
	}

	public function check_empcode($emp_code,$emp_id="")

	{
	    
	    $id_company = $this->session->userdata('id_company');
	    $company_settings = $this->session->userdata('company_settings');

		$this->db->select('emp_code');

		$this->db->where('emp_code',$emp_code); 
		
		if($id_company != 0 && $company_settings == 1)
		{
		    $this->db->where('id_company =', $id_company); 
		}

		if($emp_id)

        {

			$this->db->where('id_employee <>', $emp_id); 

		}			

		

		$status=$this->db->get(self::TABLE_NAME);

		

		if($status->num_rows()>0)

		{

			return TRUE;

		}	

	   

	}
	
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
	// 	return ($insert_flag == 1 ? $this->db->insert_id(): 0);
	// }
	
	// public function updateData($data,$id_field,$id_value,$table)
    // {    
	//     $edit_flag = 0;
	//     $this->db->where($id_field,$id_value);
	// 	$edit_flag = $this->db->update($table,$data);
	// 	return ($edit_flag==1?$id_value:0);
	// }
	public function updEmpAccessTime($data)
    {    
	    $sql = $this->db->query("update employee_settings set access_time_from='".$data['access_time_from']."' , access_time_to='".$data['access_time_to']."' where access_time_from is not null and access_time_to is not null");
	    return $sql;
	}	 
	
	public function deleteData($id_field,$id_value,$table)
    {
        $this->db->where($id_field, $id_value);
        $status= $this->db->delete($table); 
		return $status;
	} 
	
     function get_employee_records(){		 		
         $sql="SELECT * FROM employee e";		 		
         return $this->db->query($sql)->result_array();						 	 
         
     } 	 	
     
     //employee Active/Inactive Options hh//
     
     public function update_employee_only($data,$id)

    {    	

    	$edit_flag=0;

    	$this->db->where('id_employee',$id); 

		$emp_info=$this->db->update(self::TABLE_NAME,$data);		

		//print_r($this->db->_error_message());
		//print_r($this->db->last_query());exit;
		return $emp_info;

	}
	
	
	function checkAccessTime($id_emp)
	{
		$now = time();
		$result = array('status' => FALSE, 'msg' => 'Access Permissions not updated for your account, kindly contact admin..');
		$sql = $this->db->query("Select access_time_from,access_time_to from employee_settings where id_employee = ".$id_emp); 
		if($sql->num_rows == 1){
			$empSett = $sql->row_array(); 
			if($empSett['access_time_from'] == NULL){
				$result['status'] = TRUE;
				$result['msg'] = "Valid Request";
				$result['access_time_from'] = $empSett['access_time_from'] ;
				$result['access_time_to'] = $empSett['access_time_to'] ;
			}else{ 
				$now = strtotime(date("Y-m-d H:i:s")); 
				$from = strtotime(date("Y-m-d")." ".$empSett['access_time_from']); 
				$to = strtotime(date("Y-m-d")." ".$empSett['access_time_to']);  
				$allowedAccess = ($now > $from && $now < $to) ? TRUE : FALSE ;
				if( $allowedAccess ){
					$result['status'] = TRUE;
					$result['msg'] = "Valid Request"; 
					$result['access_time_from'] = $from ;
					$result['access_time_to'] = $to ;
				}else{  
					$result['status'] = FALSE;
					$result['msg'] = "Allowed access time ".$empSett['access_time_from'].' to '.$empSett['access_time_to'];
				}
			} 
		}  
		return  $result;
	}
	
	
	function branchname_list()
    {		
		$id_company=$this->session->userdata('id_company');
        $company_settings=$this->session->userdata('company_settings');
		$branch=$this->db->query("SELECT b.name,b.id_branch FROM branch b WHERE b.active = 1 ".($id_company!=0 && $company_settings == 1? "AND b.id_company=".$id_company."" :'')."");	
		return $branch->result_array();	
	}
	
   function getActiveemployee()
   {
		$data = $this->db->query("SELECT id_employee,username FROM `employee` WHERE active = 1");
		return $data->result_array();
   }
   
	function get_empset_lst()
    {
		$data = $this->db->query("SELECT Distinct e.id_employee,e.username FROM employee e left join employee_settings emp on e.id_employee = emp.id_employee WHERE emp.id_employee is NULL and e.active=1");
		return $data->result_array();
	}
	
	
	function ajax_emp_setting($from_date,$to_date,$id_employee)
    {
		$sql  ="SELECT access_time_from,access_time_to,e.id_employee,e.username,emp.id_emp_sett,
		emp.disc_limit_type,emp.allowed_old_met_pur,emp.disc_limit,emp.created_on,emp.allow_day_close,
		emp.otp_dis_approval,emp.allow_manual_rate,emp.max_silver_tol,emp.min_silver_tol,
		emp.max_gold_tol,emp.min_gold_tol,emp.bulk_wast_disc_limit as blk_wast_disc_lmt,emp.dia_disc_limit as dia_disc_lmt
		FROM employee_settings emp
		left join employee e on emp.id_employee = e.id_employee where  e.active=1 ".($id_employee!='' ? " and e.id_employee=".$id_employee."" :'')." ";
		//print_r($sql);exit;
		if($from_date!='')
		{
			$sql = $sql." and".($from_date != '' ? ' date(emp.created_on) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : (''));
		}
		$result=$this->db->query($sql);
		$data = $result->result_array();
		return $data;
	}
	
	public function get_emp_by_company($id_company,$username)

    {
       if(!empty($id_company)){
        $compy_login_query = $this->db->query('SELECT e.id_company from employee e WHERE e.username = "'.$username.'"');
        if($compy_login_query->row()->id_company == 0){
		    $list_data=$this->db->query('SELECT e.id_employee,e.id_company,e.username,e.login_branches,c.company_name 
		                                    from employee e 
		                                    left join company c on c.id_company = '.$id_company.' 
		                                    where e.username = "'.$username.'"'); //e.id_company = '.$id_company.' and
        }else if($compy_login_query->row()->id_company == ''){
             $list_data=$this->db->query('SELECT e.id_employee,e.id_company,e.username,e.login_branches,c.company_name 
		                                    from employee e 
		                                    left join company c on c.id_company = 1 
		                                    where e.username = "'.$username.'"'); //e.id_company = '.$id_company.' and
        }else{
            	$list_data=$this->db->query('SELECT e.id_employee,e.id_company,e.username,e.login_branches,c.company_name 
            	                            from employee e 
            	                            left join company c on c.id_company = e.id_company 
            	                            where e.id_company = '.$id_company.' and e.username = "'.$username.'"');
        }
       }else{
           $list_data=$this->db->query('SELECT e.id_employee,e.id_company,e.username,e.login_branches,c.company_name 
		                                    from employee e 
		                                    left join company c on c.id_company = 1 
		                                    where e.username = "'.$username.'"'); //e.id_company = '.$id_company.' and
       }
		//echo $this->db->last_query();exit;
		return $list_data->row_array();

	} 
	
	public function get_empBranch_company($id_company,$id_branch,$username)

    {
        
        $compy_login_query = $this->db->query('SELECT e.id_company from employee e WHERE e.username = "'.$username.'"');
        if($compy_login_query->row()->id_company == 0){
            $list_data=$this->db->query('SELECT e.id_employee,e.id_company,e.username,e.login_branches,c.company_name from employee e
    		left join company c on c.id_company = '.$id_company.'
    		where e.id_company = '.$id_company.' and FIND_IN_SET('.$id_branch.',e.login_branches)  and e.username = "'.$username.'"');
        }else{
    		$list_data=$this->db->query('SELECT e.id_employee,e.id_company,e.username,e.login_branches,c.company_name from employee e
    		left join company c on c.id_company = e.id_company
    		where e.id_company = '.$id_company.' and FIND_IN_SET('.$id_branch.',e.login_branches)  and e.username = "'.$username.'"');
        }
		//echo $this->db->last_query();exit;   
		// return $list_data->row_array();
		if($list_data->num_rows()>0 || $compy_login_query->row()->id_company == 0){
            return TRUE;
    	}else{
    	    return FALSE;
    	}
		

	} 
	//Lines Added by DUrga Starts here 18.07.2023
	public function get_emp_name_list()
	{
		$sql="SELECT e.id_employee,e.firstname,IFNULL(e.lastname,'') as lastname,IFNULL(e.emp_code,'-') as emp_code from employee e where e.active=1";
		
		return $this->db->query($sql)->result_array();
	}
	public function get_device_list()
	{
		$sql="SELECT ed.id_collection_device,ed.device_uuid from employee_devices ed";
		
		return $this->db->query($sql)->result_array();
	}
	public function get_employee_name_byid($emp_id="", $emp_dev_id="")
	{
		if($emp_id!='' && $emp_id!=null)
		{
			$id=$emp_id;
		}
		else
		{
			$id=$this->input->post('id_employee');
		}
		
		$sql="SELECT e.id_employee,e.firstname,IFNULL(e.lastname,'') as lastname,IFNULL(e.emp_code,'-') as emp_code,IFNULL(ed.device_uuid,'') as device_uuid from employee e left join employee_devices ed on e.id_employee = ed.emp_id where active=1 and e.id_employee = ".$id." and ed.id_collection_device= '".$emp_dev_id."'";
		
		return $this->db->query($sql)->row_array();
	}
	function get_chit_settings_data()
	{
		 $sql="Select * from chit_settings where id_chit_settings = 1";
		 return $this->db->query($sql)->row_array();
	}
	function get_enabled_device_count($for_estimation_app_count = 0)
	{
		$field_name = "";

		if($for_estimation_app_count == 1) {

			$field_name = "app_type = 2";

		} else {

			$field_name = "app_type = 1";

		}

		$sql = "SELECT count(emp_id) as count FROM `employee_devices` WHERE ".$field_name." and device_status = 1";
		
		return $this->db->query($sql)->row()->count;
	}
    //Lines Added by DUrga ends here 18.07.2023

	public function get_employee_devices($emp_id, $app_type) {

		$result_array = array();
		
		$sql = "Select id_collection_device, device_uuid, app_type, device_status, device_type, device_info from employee_devices where emp_id = ".$emp_id." AND app_type = ".$app_type;

		$q_sql = $this->db->query($sql);

		if($q_sql->num_rows() > 0) {

			$q_result = $q_sql->result_array();

			foreach($q_result as $res) {
			
				$device_name = "";

				if($res['device_type'] == 1) {
			
					$device_name = $this->get_device_from_useragent($res['device_info']);
			
				} else {
			
					$device_name = "IOS";
			
				}

				$res['device_name'] = $device_name;

				unset($res['device_info']);

				$result_array[] = $res;

			}

		}

		return $result_array;
		
	}

	function update_emp_device_status($emp_dev_id, $status) {

		$status = array(

			"device_status" => $status

		);

		$this->db->where('id_collection_device',$emp_dev_id);    

		if($this->db->update("employee_devices",$status)) {
		
			return true;

		} else {

			return false;

		}

	}

	function get_device_from_useragent($user_agent_string) {

		$device = "";
	
		$pattern = '/\bAndroid \d+; ([^;]+) Build/';

		if (preg_match($pattern, $user_agent_string, $matches)) {
	
			$device = $matches[1];
	
		}

		return $device;

	}

	// function to get employee by branch
	function getEmployeeByBranch($idBranch = ''){

	if($idBranch != '') {
		$query = $this->db->query("SELECT id_employee, CONCAT(firstname, ' - ', emp_code) as emp_data FROM employee WHERE (login_branches = '0' OR FIND_IN_SET('".$idBranch."', login_branches)) AND active = 1");
		// print_r($this->db->last_query());exit;
		$result = $query->result_array();
	}else{

		$query = $this->db->query("SELECT id_employee, CONCAT(firstname, ' - ', emp_code) as emp_data FROM employee WHERE active = 1");
		$result = $query->result_array();
	}		
	
		if (empty($result)) {

			$result = "No data found";

		} 
		return $result;	

	}

	function get_ret_settings($name){

		$sql = "SELECT
				`name`,
				`value`
				FROM ret_settings WHERE name = '".$name."'";
		$sql = $this->db->query($sql);
	    return $sql->row_array();
	}
	function get_dia_disc(){

		$sql = "SELECT min_disc_per,max_disc_per
				FROM ret_stone_discount_master WHERE id_stone_type = 1 AND status = 1";
		$sql = $this->db->query($sql);
	    return $sql->row_array();
	}

}   

?>