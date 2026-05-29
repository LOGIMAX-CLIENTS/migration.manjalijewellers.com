<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Agent_model extends CI_Model
{
    const TABLE_NAME 	= "agent";
	const ADDRESS_TABLE = "address";
	const CUS_IMG_PATH 	  			= 'agent/assets/img/agent';
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
    function approval_range($from_date,$to_date,$limit="",$date_type,$settle="")
	{
		   
		$sql="SELECT
					  if(ag.lastname is null,ag.firstname,concat(ag.firstname,' ',ag.lastname)) as name,
					  concat(concat(ifnull(sa.group_code,''),' ',ifnull(sa.scheme_acc_number,'Not Allocated')),' - ',s.code ) as scheme_acc_number,
					  ag.mobile,ag.agent_code,lys.id_cus_loyal_tran as id_transaction,
					  lys.cash_point as cash_point,lys.status as status,if(lys.ly_trans_type=1,'Billing',if(lys.ly_trans_type=2,'Welcome Bonus',if(lys.ly_trans_type=3,'Purchase Plan Refral','Purchase Plan Collection'))) as trans_type,
					  IFNULL(Date_format(lys.date_add,'%d-%m%-%Y'),'-') as request_date
				FROM ly_customer_loyalty_transaction lys
			    Left join ly_cus_loyalty_trans_details slg on (slg.id_cus_loyalty_trans=lys.id_cus_loyal_tran)
			    left join scheme_account sa on (sa.id_scheme_account=lys.id_scheme_account)
			    left join scheme s on (s.id_scheme=sa.id_scheme)
				Left Join agent ag on (ag.id_agent=lys.id_agent)
       Where   (date(lys.date_add) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') 
        ".($settle==3?" AND lys.status=3  ":($settle==4?" AND lys.status=4  ":''))."
			   ORDER BY lys.id_cus_loyal_tran DESC ".($limit!=NULL? " LIMIT ".$limit." OFFSET ".$limit : " ");
			  //print_r($sql);exit;
		return $this->db->query($sql)->result_array();
	}
	
	function update_settlement_status($id,$data)
	{
		$this->db->where('id_cus_loyal_tran',$id);
		$status=$this->db->update('ly_settlement_request',$data);
	//	print_r($this->db->last_query());exit;
		return $status;
	}
	public function update_agent_only($data,$id)
    {    	
    	$edit_flag=0;
    	$this->db->where('id_customer',$id); 
		$cus_info=$this->db->update(self::TABLE_NAME,$data);		
		return $cus_info;
	}
	
	 function get_agents_by_date($from_date,$to_date,$id_branch)
    {
        	$date_type = $this->input->post('date_type');
		 $sql = "Select ag.*,
		   ag.id_agent,concat(firstname,' ',if(lastname!=NULL,lastname,'')) as name,
		   a.address1,a.address2,a.address3,ct.name as city,a.pincode,s.name as state,cy.name as country,
		   a.id_country,a.id_city,a.id_state
       
			From
			  agent ag
			left join address a on(ag.id_agent=a.id_agent)
			left join country cy on (a.id_country=cy.id_country)
			left join state s on (a.id_state=s.id_state)
			left join city ct on (a.id_city=ct.id_city)
     
        Where (date(".($date_type!='' ? ("ag.date_add") : "ag.date_add").") BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($id_branch!='' ? " and ag.id_branch=".$id_branch."" :'')."
	  Group by ag.id_agent";
       // print_r($sql);exit;
		return $customers=$this->db->query($sql)->result_array();
	}
	
	function get_all_agents($limit="",$id_branch='',$id_village='')
    {
		$branch_settings=$this->session->userdata('branch_settings');
		$branch=$this->session->userdata('id_branch');
		$uid=$this->session->userdata('uid');
		
		$limit = "";
		$sql = "Select ag.*,concat(ag.firstname,' ',if(ag.lastname!=NULL,ag.lastname,'')) as name,cs.edit_custom_entry_date
		From agent ag 
		 left join branch b on b.id_branch=ag.id_branch
		 join chit_settings cs 
	   where ag.active=1   ".($uid!=1 && $uid!=2 ? ($branch_settings==1 ? ($id_branch!='' && $id_branch!=0 ? "and ag.id_branch=".$id_branch."" : ($branch!='' ? "and(ag.id_branch=".$branch." or b.show_to_all=2)":'')):'') : ($id_branch!='' && $id_branch!=0 ? "and ag.id_branch=".$id_branch."" :''))." ".($id_village!='' ? " and ag.id_village='".$id_village."'":'')." 
	  Group by ag.id_agent
	  Order By ag.id_agent Desc ".($limit!=NULL? " LIMIT ".$limit : " ");
	//  print_r($sql);exit;
		return $customers=$this->db->query($sql)->result_array();
	} 
	
	function agent_count()
	{
		$sql = "SELECT id_agent FROM agent";
		return $this->db->query($sql)->num_rows();
	}
	
	function empty_record()
    {
		$data=array(
			'id_agent'			=> NULL,
			'id_village'			=> NULL,
			'id_branch'			=> NULL,
			'lastname'				=> NULL,
			'firstname'				=> NULL,
			'date_of_birth'			=> NULL,
			'date_of_wed'			=> NULL,
		    'id_country' 			=> 0,
			'id_state' 				=> 0,
			'id_city' 				=> 0,
			'address1' 				=> NULL,
			'address2' 				=> NULL,
			'address3' 				=> NULL,
			'pincode' 				=> NULL,
			'post_office' 				=> NULL,
		    'taluk' 				=> NULL,
			'email'					=> NULL,	
			'gender'				=> -1,
			'age'					=> NULL,
			'mobile'				=> NULL,
			'phone'					=> NULL,
			'nominee_name'			=> NULL,
			'nominee_relationship'	=> NULL,
			'nominee_mobile'		=> NULL,	
			'pan'					=> NULL,
			'pan_proof'				=> NULL,
			'voterid'				=> NULL,
			'voterid_proof'			=> NULL,
			'rationcard'			=> NULL,
			'rationcard_proof'		=> NULL,
			'comments'				=> NULL,
			'username'				=> NULL,
			'passwd'				=> NULL,
			'gst_number'			=>null,
			'company_name'			=>null,
			'religion'	    		=>null,
			'active'				 =>1,
			'is_cus_synced'         =>0
		);
		return $data;
	}
function set_image($id)
 {
 	$data=array();
    //$model=self::SET_MODEL;
  
   	 if($_FILES['cus_img']['name'])
   	 { 
   	 	$path='assets/img/agent/';
	    if (!is_dir($path)) {
		  mkdir($path, 0777, TRUE);
		}
		else{
			$file = $path.$id.".jpg" ;
			chmod($path,0777);
	        unlink($file);
		}
   	 	$img=$_FILES['cus_img']['tmp_name'];
		$filename = $_FILES['cus_img']['name'];	
   	 	$imgpath='assets/img/agent/'.$filename;
	 	$upload=$this->upload_img('cus_img',$imgpath,$img);		
	 } 
 }
 function upload_img( $outputImage,$dst, $img)
	{	
		//print_r(getimagesize($img));exit;    //Array ( [0] => 512 [1] => 288 [2] => 3 [3] => width="512" height="288" [bits] => 8 [mime] => image/png )
		if (($img_info = getimagesize($img)) === FALSE)
		{
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
	
	
	public function insert_agent($data)
    {
    	$agent_id=0;
    	$insert_flag=0;
		$agent_info=$this->db->insert(self::TABLE_NAME,$data['info']);
		//echo $this->db->last_query();exit;
		if($agent_info)
		{	
			$agent_id=$this->db->insert_id();
			$data['address']['id_agent']=$agent_id;
			$insert_flag=$this->db->insert(self::ADDRESS_TABLE,$data['address']);
			if($insert_flag){
				$id_address=$this->db->insert_id();
				$address = array('id_address' => $id_address);
				$this->db->where('id_agent',$agent_id); 
		        $this->db->update(self::TABLE_NAME,$address);				
			}
		}
		
		if($insert_flag > 0){
			
			$this->set_image($agent_id);
		}
		
		return ($insert_flag==1?$agent_id:0);
	}
	
	public function check_password($id_agent,$pswd)
	{
		$this->db->select('passwd');
		$this->db->where('id_agent', $id_agent); 
		$this->db->where('passwd', $pswd); 
		$status=$this->db->get(self::TABLE_NAME);
		if($status->num_rows()>0)
		{
			return TRUE;
		}	
	}
	
	public function update_agent($data,$id)
    {   
		//print_r($data);exit;
    	$edit_flag=0;
    	$this->db->where('id_agent',$id); 
		$agent_info=$this->db->update('agent',$data['info']);
		
		if($agent_info)
		{			
		    $qry = $this->db->query('select id_agent from address where id_agent='.$id);
			
		    if($qry->num_rows() > 0){
				$this->db->where('id_agent',$id); 
			    $edit_flag = $this->db->update(self::ADDRESS_TABLE,$data['address']);
			}
			else{
				$edit_flag = $this->db->insert(self::ADDRESS_TABLE,$data['address']);
			}
			
		}
		$filename = $_FILES['cus_img']['name'];
		$imgpath = base_url().'assets/img/agent/'.$filename;
		//print_r($imgpath);exit;
		if($edit_flag > 0){
			
			$this->set_image($id);
		}
		//echo $this->db->last_query();exit;
		return ($edit_flag==1?$id:0);
	}
	
	function get_agent($id)
    {
		$customers=$this->db->query("Select
		   ag.*,
		   a.address1,a.address2,a.address3,ct.name as city,a.pincode,s.name as state,cy.name as country,a.id_country,a.id_city,a.id_state
			From
			  agent ag
			left join address a on(ag.id_agent=a.id_agent)
			left join country cy on (a.id_country=cy.id_country)
			left join state s on (a.id_state=s.id_state)
			left join city ct on (a.id_city=ct.id_city)

			where ag.id_agent=".$id);
         //print_r($this->db->last_query());exit;
		return $customers->row_array();
	}
	
		public function delete_agent($id)
    {
        $this->db->where('id_agent', $id);
        $child= $this->db->delete(self::ADDRESS_TABLE);
        if($child)
        {
		  $this->db->where('id_agent', $id);
           $status= $this->db->delete(self::TABLE_NAME); 
		}
		return $status;
	} 
	
	function getAgent_records($data)
	{
		    $Influencer_records = array();
			
		  
			$sql = $this->db->query("SELECT IFNULL(count(id_cus_loyal_tran),0) as no_of_referrals, IFNULL(sum(unsettled_cash_pts),0) as cash_points, IFNULL(ag.cash_reward,0) as cus_cash_pts,
			ag.mobile, concat(firstname,' ',if(lastname!=NULL,lastname,'')) as agent_name, if(b.name!='',b.name,'-') as branch_name,ag.id_branch as id_branch,
			ly_set.influencer_settlement_date_type, ly_set.influencer_settlement_date,tr.id_agent as cus_id,ly_set.influ_minimum_amt_required_to_settle,tr.status
			FROM `ly_customer_loyalty_transaction` as tr
			LEFT JOIN agent ag on ag.id_agent = tr.id_agent
			LEFT JOIN branch  b on b.id_branch = ag.id_branch
			JOIN ly_loyalty_settings ly_set
			WHERE  tr.tr_cus_type = 4 and tr.unsettled_cash_pts > 0 and (tr.status = 1 or tr.status = 3)
			".($_POST['from_date'] != '' ? ' AND date(tr.date_add) BETWEEN "'.$_POST['from_date'].'" AND "'.$_POST['to_date'].'"' : '')."
            ".($_POST['id_branch']!='' && $_POST['id_branch']>0 ? " and ag.id_branch=".$_POST['id_branch']."" :'')."
            ".($_POST['id_agent']!='' && $_POST['id_agent']>0 ? " and tr.id_agent=".$_POST['id_agent']."" :'')." GROUP BY tr.id_agent");
           // echo $this->db->last_query();exit;
		   $records = $sql->result_array();
		   foreach($records as $row)
		   {
			   $row['bill_transcation'] =  $this->InfReferalPerson_PaymentDetails($row['cus_id']); 
			   $Influencer_records[]    = $row;
		   }
		 return $Influencer_records;;
	}
	
	function InfReferalPerson_PaymentDetails($inf_cusid)
	{
		  $sql = $this->db->query("SELECT id_cus_loyal_tran,t.id_agent,cash_point,tr_cus_type,cr_based_on,status,t.id_payment,expiry_on,unsettled_cash_pts,
		    Date_format(t.date_add,'%d %b %Y') as cr_date,IFNULL(p.payment_amount,0) as purchase,
		    concat(concat(ifnull(sa.group_code,''),' ',ifnull(sa.scheme_acc_number,'Not Allocated')),' - ',s.code ) as sch_acc_no,t.credit_for,if(t.ly_issue_type=1,'CREDIT','DEBIT') as issue_type
		  FROM `ly_customer_loyalty_transaction` t
		    LEFT JOIN payment p on p.id_payment = t.id_payment
		    LEFT JOIN scheme_account sa on sa.id_scheme_account=t.id_scheme_account
		    LEFT JOIN scheme s on s.id_scheme=sa.id_scheme
		  where t.id_agent='".$inf_cusid."' and  tr_cus_type = 4  and (status = 1 or status = 3) ORDER BY id_cus_loyal_tran ASC");
		  $trnscation_records  = ($sql->num_rows() >0 ? $sql->result_array() : []);
		  return $trnscation_records;
	}
    
	
	function get_loyalty_settings()
	{
		$data=$this->db->query("SELECT influ_minimum_amt_required_to_settle,influ_settle_amt_max_percent FROM ly_loyalty_settings"); 
		return $data->row_array();
	}
	
	public function insertData($data,$table)
    {
    	$insert_flag = 0;
		$insert_flag = $this->db->insert($table, $data);
		//print_r($this->db->last_query());die;
		return ($insert_flag == 1 ? $this->db->insert_id(): 0);
	}
	public function updateData($data,$id_field,$id_value,$table)
    {    
	    $edit_flag = 0;
	    $this->db->where($id_field,$id_value);
		$edit_flag = $this->db->update($table,$data);
		return ($edit_flag==1?$id_value:0);
	}
	
	function get_influencer_list($id = 0)
    {
        $resultArr = array();
        $where = "";
        if($id > 0)
        {
            $this->db->where("id_influencer_settings", $id);
        }
        $queryRes = $this->db->get('ly_influencer_settings');
        if($queryRes) {
            $result = $queryRes->result_array();
            foreach($result as $row)
            {
                if($id > 0)
                {
                    $type                   = $row['type'];
                    $point_based_category   = $row['point_based_category'];
                    $point_type             = $row['point_type'];
                    $accumulate_type        = $row['accumulate_type'];
                }
                else
                {
                    $type                   = $row['type'] == 1 ? "Influencer" : "Referal";
                    $point_based_category   = $row['point_based_category'] == 1 ? "Yes" : "No";
                    $point_type             = $row['point_type'] == 1 ? "Point" : "Cash";
                    $accumulate_type        = $row['accumulate_type'] == 1 ? "%" : "Value";
                }
                $record = array(
                    "id_influencer_settings"    => $row['id_influencer_settings'],
                    "name"    					=> $row['name'],
                    "type"                      => $type,
                    "type_value"                => $row['type'],
                    "point_based_category"      => $point_based_category,
                    "point_type"                => $point_type,
                    "accumulate_type"           => $accumulate_type,
                    "earning_rule_value"        => $row['earning_rule_value'],
                    "earning_rule_point"        => $row['earning_rule_point'],
                    "expiration"                => $row['expiration'],
                    "expire_after"              => $row['expire_after'],
                    "expire_type"               => $row['expire_type']
                );
                $resultArr[] = $record;
            }
            $result = $resultArr;
        } else {
            $err_message = $this->db->_error_message();
            throw new Exception("Database Error occured.".$err_message); 
        }
        return $result;
    }
    
    function getCashReward($id_agent)
    {
         $sql = $this->db->query("SELECT cash_reward from agent where id_agent=".$id_agent);
		if($sql->num_rows()>0)
		{
			return $sql->row_array();
		}	
    }
    
    function get_active_agents($id_branch)
	{
		$sql="select  concat(ag.firstname,' ',ag.lastname)as  agent_name,ag.id_agent from agent ag".($id_branch!=0 ?" where ag.id_branch=".$id_branch."" :'')."";
		return $this->db->query($sql)->result_array();
	}
	
	function get_agentBankData($id_agent)
	{
	    $sql = "SELECT ifnull(bank_account_number,'-') as bank_account_number,ifnull(ifsc_code,'-') as ifsc_code,ifnull(bank_name,'-') as bank_name,ifnull(bank_acc_holder_name,'-') as bank_acc_holder_name,bank_image,if(preferred_mode=1,'CASH','ONLINE') as preferred_mode from agent where id_agent =".$id_agent;
	    return $this->db->query($sql)->row_array();
	}
	
	
	function get_agents(){
	    $data = array();
	    
    	$sql = "SELECT ag.agent_code,ag.id_agent,ag.firstname as agent_name,ag.mobile as agent_mobile,sa.id_scheme_account,count(sa.id_scheme_account) as referrals, SUM(IFNULL(tr.conversions,0)) as conversions, SUM(IFNULL(tr.revenue,0)) as revenue, SUM(IFNULL(tr.earnings,0)) as earnings
                FROM scheme_account sa 
                left JOIN agent ag on ag.id_agent = sa.id_agent
                LEFT JOIN (
                    SELECT 
                        IFNULL(COUNT(distinct(t.id_scheme_account)),0) as conversions,t.date_add,t.id_agent,
                        SUM(IFNULL(pay.payment_amount,0)) as revenue,
                        SUM(IFNULL(t.cash_point,0)) as earnings,
                        t.id_scheme_account 
                    FROM ly_customer_loyalty_transaction t 
                        LEFT JOIN payment pay on pay.id_payment = t.id_payment
                    WHERE  tr_cus_type = 4 
                    GROUP BY t.id_scheme_account
                )tr on tr.id_scheme_account = sa.id_scheme_account WHERE sa.id_agent is not null
                ".($_POST['id_agent']!='' && $_POST['id_agent']>0 ? " and tr.id_agent=".$_POST['id_agent']."" :'')."
                ".($_POST['from_date'] != '' ? ' AND date(sa.date_add) BETWEEN "'.$_POST['from_date'].'" AND "'.$_POST['to_date'].'"' : '')."
                GROUP BY sa.id_agent";
                //echo $sql;exit;
        $res = $this->db->query($sql)->result_array();
			foreach($res as $row)
			{
			    $row['unpaid'] = $row['referrals']-$row['conversions'];
			    $data['records'][] = $row;
			}
		//echo $this->db->last_query();exit;
			
        return $data;
	}
	
	
	function get_agent_details($postData)
    {      
    	
    	$data = array('agent' => [], 'transactions' => [], 'unpaid' => [], 'conversions' => 0, 'referrals' => [], 'settlement' => [] );
        $cus = $this->db->query("SELECT ag.firstname as cus_name,ag.mobile,IFNULL(ag.email,'') as email,IFNULL(a.address1,'') as address1,Date_format(date_of_birth,'%d %b %Y') as date_of_birth,Date_format(date_of_wed,'%d %b %Y') as date_of_wed,
        IFNULL(a.address2,'') as address2,IFNULL(a.address3,'') as address3,IFNULL(c.name,'') as county_name,IFNULL(s.name,'') as state_name,IFNULL(ct.name,'') as city_name,IFNULL(ag.image,'') as cus_img,
        ag.active,cash_reward as cus_cash_pts,ag.id_agent,
        IFNULL(instagram_url,'') as instagram,IFNULL(website_url,'') as website,IFNULL(twitter_url,'') as twitter,IFNULL(youtube_url,'') as youtube,IFNULL(facebook_url,'') as facebook,IF(preferred_mode=1,'CASH','ONLINE') as pref_pay_mode,preferred_mode,
        ly_set.influencer_settlement_date_type, ly_set.influencer_settlement_date,ly_set.influ_minimum_amt_required_to_settle,
        IFNULL(bank_account_number,'') as bank_acc_number,IFNULL(ifsc_code,'') as bank_ifsc,IFNULL(bank_name,'') as bank_name
        FROM agent ag
        LEFT JOIN address a ON a.id_agent=ag.id_agent
        LEFT JOIN country c ON c.id_country=a.id_country
        LEFT JOIN state s ON s.id_state=a.id_state
        LEFT JOIN city ct ON ct.id_city=a.id_city
		JOIN ly_loyalty_settings ly_set
        WHERE ag.mobile=".$postData['mobile']."");
        if($cus->num_rows == 0)
        {
            $data['count'] = 0;
            $data['msg'] = "No Mobile Number found";
            return $data;
        }else
        {
            $data['count'] = 1;
            $data['agent'] = $cus->row_array();
            
        	// REFERRALS
	        $sql  = $this->db->query( "SELECT count(id_scheme_account) as referrals from scheme_account 
	                WHERE id_agent = ".$data['agent']['id_agent']."
				    ".($postData['from_date'] != '' ? ' and (date(date_add) BETWEEN "'.$postData['from_date'].'" AND "'.$postData['to_date'].'")' :'')
				                );
			if($sql->num_rows() == 0) {
				$data['total_referrals'] = 0;
			}else{
				$data['total_referrals'] = $sql->row()->referrals;  
			}
			
			//EARNINGS
			$result  = $this->db->query("SELECT  
				 	IFNULL(SUM(cash_point),0) as cash_point			 	
				FROM ly_customer_loyalty_transaction 
				WHERE id_agent = ".$data['agent']['id_agent']."
				".($postData['from_date'] != '' ? ' and (date(date_add) BETWEEN "'.$postData['from_date'].'" AND "'.$postData['to_date'].'")' :''));  
				
				
		  // print_r($this->db->last_query());exit;
			if($result->num_rows() == 0) {
				$data['earnings'] = 0;
			}else{
				$data['earnings'] = $result->row()->cash_point;  
			}
			
			 // CONVERSIONS
	        $sql  = $this->db->query("SELECT  
				 	COUNT(IFNULL(id_scheme_account,0)) as conversions
				FROM `ly_customer_loyalty_transaction`
				WHERE id_agent = ".$data['agent']['id_agent']."".($postData['from_date'] != '' ? ' and (date(date_add) BETWEEN "'.$postData['from_date'].'" AND "'.$postData['to_date'].'")' :''));
			if($sql->num_rows() == 0) {
				$data['conversions'] = 0;
			}else{
				$data['conversions'] = $sql->row()->conversions;  
			}
			
			//UNPAID
			
			//$data['unpaid'] = $data['total_referrals'] - $data['conversions'];
			$unpaid_sql = $this->db->query( "SELECT count(id_scheme_account) as unpaid from scheme_account sa
	                            WHERE sa.id_agent = ".$data['agent']['id_agent']." AND id_scheme_account NOT IN (SELECT p.id_scheme_account FROM payment p WHERE sa.id_agent = ".$data['agent']['id_agent'].")
				                ".($postData['from_date'] != '' ? ' and (date(date_add) BETWEEN "'.$postData['from_date'].'" AND "'.$postData['to_date'].'")' :'')."
				                "
				                );
			$data['unpaid'] = $unpaid_sql->row()->unpaid;
			
			
			//AGENT REFERRALS LIST
			$refsql  = "SELECT  
		            sa.account_name,if(cs.has_lucky_draw=1 && s.is_lucky_draw = 1,concat(concat(ifnull(sa.group_code,''),' ',ifnull(sa.scheme_acc_number,'Not Allocated')),' - ',s.code ),concat(s.code,' ',ifnull(sa.scheme_acc_number,'Not Allcoated')))as scheme_acc_number,
				 	concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')) as customer_name,c.mobile,
				 	IFNULL(Date_format(sa.start_date,'%d %b %Y'),'') as start_date,p.id_payment,
				 	IFNULL(p.payment_amount,'-') as payment_amount,
				 	IFNULL(p.metal_weight,'-') as metal_weight,
				 	IFNULL(Date_format(p.date_payment,'%d %b %Y'),'') as date_payment,
				 	id_cus_loyal_tran,t.cr_based_on,
				 	IF(ly_trans_type =2 , 'Welcome Bonus' , IF(ly_trans_type =3 , 'Purchase Plan Referral' , IF(ly_trans_type =4 , 'Purchase Plan Collection' , 'Order' )  ) ) as ly_trans_type_name,
				 	IFNULL(t.redeem_point,0) as redeem_point,SUM(IFNULL(t.cash_point,0)) as cash_point,
				 	if(tr_cus_type = 1,'Purchase','Referral Commission') as trans_type,
				 	'+' as trans_type_sign,
    				'green' as trans_type_color,
				 	Date_format(sa.start_date,'%d %b %Y') as cr_date,t.date_add as date,
				 	Date_format(t.expiry_on,'%d %b %Y') as expiry_on,
				 	if(t.status = 1, 'Active',if(t.status = 2, 'Settled','Partially Settled')) as status
				FROM `scheme_account` sa
					LEFT JOIN ly_customer_loyalty_transaction t on t.id_scheme_account = sa.id_scheme_account
					LEFT JOIN scheme s On (s.id_scheme=sa.id_scheme)
					LEFT JOIN customer c on c.id_customer = sa.id_customer
					LEFT JOIN payment p on p.id_payment = t.id_payment
					JOIN chit_settings cs 
				WHERE sa.id_agent = ".$data['agent']['id_agent'].""; 
		if($postData['from_date'] != ''){
			$refsql = $refsql." and (date(sa.date_add) BETWEEN '".$postData['from_date']."' AND '".$postData['to_date']."')";
		}
		$refsql = $refsql." GROUP BY sa.id_scheme_account ORDER BY sa.id_scheme_account DESC"; 
		$data['referral_list'] =  $this->db->query($refsql)->result_array();  
			
	        
	       
			
			// TRANSACTIONS
        	$transSQL  = "SELECT  
		            sa.account_name,if(cs.has_lucky_draw=1 && s.is_lucky_draw = 1,concat(concat(ifnull(sa.group_code,''),' ',ifnull(sa.scheme_acc_number,'Not Allocated')),' - ',s.code ),concat(s.code,' ',ifnull(sa.scheme_acc_number,'Not Allcoated')))as scheme_acc_number,
				 	c.firstname as customer_name,c.mobile,ifnull(p.receipt_no,'-') as receipt_no,
				 	IFNULL(Date_format(sa.start_date,'%d %b %Y'),'') as start_date,p.id_payment,
				 	IFNULL(p.payment_amount,'-') as payment_amount,
				 	IFNULL(p.metal_weight,'-') as metal_weight,
				 	IFNULL(Date_format(p.date_payment,'%d %b %Y'),'') as date_payment,
				 	id_cus_loyal_tran,t.cr_based_on,
				 	IF(ly_trans_type =2 , 'Welcome Bonus' , IF(ly_trans_type =3 , 'Purchase Plan Referral' , IF(ly_trans_type =4 , 'Purchase Plan Collection' , 'Order' )  ) ) as ly_trans_type_name,
				 	IFNULL(t.redeem_point,0) as redeem_point,IFNULL(t.cash_point,0) as cash_point,
				 	if(tr_cus_type = 1,'Purchase','Referral Commission') as trans_type,
				 	'+' as trans_type_sign,
    				'green' as trans_type_color,
				 	Date_format(t.date_add,'%d %b %Y') as cr_date,t.date_add as date,
				 	Date_format(t.expiry_on,'%d %b %Y') as expiry_on,
				 	if(t.status = 1, 'Active',if(t.status = 2, 'Settled','Partially Settled')) as status
				FROM `ly_customer_loyalty_transaction` t
					LEFT JOIN scheme_account sa on sa.id_scheme_account = t.id_scheme_account
					LEFT JOIN scheme s On (s.id_scheme=sa.id_scheme)
					LEFT JOIN customer c on c.id_customer = sa.id_customer
					LEFT JOIN payment p on p.id_payment = t.id_payment
					JOIN chit_settings cs 
				WHERE t.id_agent = ".$data['agent']['id_agent'].""; 
		if($postData['from_date'] != ''){
			$transSQL = $transSQL." and (date(t.date_add) BETWEEN '".$postData['from_date']."' AND '".$postData['to_date']."')";
		}
		$transSQL = $transSQL." ORDER BY id_cus_loyal_tran DESC LIMIT 25"; 
		$rows =  $this->db->query($transSQL)->result_array();  
			
			$i = 0;
			foreach( $rows as $row ){
				$data['transactions'][$i] = $row; 
				$file = $data['agent']['cus_img']; 
				$data['transactions'][$i]['referred_cus_img'] = (file_exists($file)? base_url().''.$file : null );
				$data['transactions'][$i]['date'] = strtotime($row['date']);
				$i++;
			} 
			
			$settlement_sql  = "SELECT
				 	id_influencer_settlement, settlement_pts, settlement_branch, if(b.name!='',b.name,'-') as branch_name,
				 	Date_format(s.settlement_date,'%d %b %Y') as settlement_date, 'Settled' as status
				FROM `ly_influencer_settlement` s
					LEFT JOIN branch  b on b.id_branch = settlement_branch
				WHERE s.id_agent = ".$data['agent']['id_agent']."
				
				".($postData['from_date'] != '' ? ' and (date(settlement_date) BETWEEN "'.$postData['from_date'].'" AND "'.$postData['to_date'].'")' :'');
    		$settlement_sql = $settlement_sql." order by id_influencer_settlement DESC";
    		//echo $settlement_sql;exit;
    		$settlmtData =  $this->db->query($settlement_sql)->result_array(); 
    		foreach( $settlmtData as $s ){
    			$data['settle_data'][$i] = $s;
    			$data['settle_data'][$i]['date'] = strtotime($s['date']);
    			$i++;
    		}
    		if(sizeof($data['settle_data']) > 0){ 
    		  
    		    $data['settle_data'] = $this->array_sort($data['settle_data'],'date',SORT_DESC);
         
    		}else{
    		    $data['settle_data'] = [];
    		}
    		
    		// PENDING SETTLEMENT
			$data['pend_settlmnt'] =  $this->InfReferalPerson_PaymentDetails($data['agent']['id_agent']); 
		
        return $data;
       }
    }
    
    function array_sort($array, $on, $order=SORT_ASC){
		$new_array = array();
		$sortable_array = array();
		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}
			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}
			foreach ($sortable_array as $k => $v) {
				//$new_array[$k] = $array[$k];
				$new_array[] = $array[$k];
			}
		}
		return $new_array;
	}
	
	function agent_referral_account($id)	
	{
		
	  $sql="select c.mobile,cp.receipt_no,wt.credit_for,if(wt.ly_issue_type=1,'Credit','Debit') as issue_type,if(wt.ly_issue_type=1,wt.cash_point,'') as benefit,if(wt.ly_issue_type=0,wt.cash_point,'') as debit,s.code,p.payment_amount,ag.agent_code, IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,if(sa.scheme_acc_number is null ,s.scheme_name,concat(s.scheme_name,'-',sa.scheme_acc_number))as scheme_acc_number,
	  c.id_customer,if(c.lastname is null ,c.firstname,concat(c.firstname,'',c.lastname))as cus_name from customer c
		left join scheme_account sa on(sa.id_customer=c.id_customer)
		left join ly_customer_loyalty_transaction wt on wt.id_scheme_account = sa.id_scheme_account
		left join payment p on (p.id_scheme_account=sa.id_scheme_account)
		left join scheme s on (s.id_scheme=sa.id_scheme)
		left join (
					        SELECT pay.id_payment,if(pay.receipt_no is null,'',concat(IFNULL(concat(pay.receipt_year,'-'),''),pay.receipt_no)) as receipt_no from wallet_transaction wt  
					            LEFT JOIN payment pay on pay.id_payment = wt.id_payment
					) cp on cp.id_payment = wt.id_payment
		
		left join agent ag on(ag.agent_code=sa.agent_code) where p.due_type='ND' and ag.agent_code='$id' and payment_status=1 and sa.is_refferal_by=1 GROUP BY wt.id_payment";
	//echo $sql;exit;
	  $payments=$this->db->query($sql);
	
	  return $payments->result_array();
	
	}
	
	
}
?>