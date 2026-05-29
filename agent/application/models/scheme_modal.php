<?php
class Scheme_modal extends CI_Model {
	var $table_name = 'scheme';						
	//Initialize table Name
	function empty_record()
	{
		$cusActive = 0;
		$query = $this->db->query('SELECT active FROM customer WHERE mobile="'.$this->session->userdata('username').'"');
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$cusActive = $row->active;
			}
		}
		$query->free_result();
		return array('cusActive' => $cusActive);
	}
	function get_schemes()
	{
		$this->db->select("id_scheme,code");
		$this->db->where("active",1);
		$schemes = $this->db->get("scheme");
		return $schemes->result_array();
	}
	function get_scheme($schemeType)
	{
		$records = array();
		$isJoined = 0;
			$query_scheme = $this->db->query('SELECT maturity_installment,sch.id_scheme AS id_scheme,scheme_name, scheme_type, description, amount,total_installments, interest, interest_by, interest_value,description, min_weight, max_weight, min_chance, max_chance,schAcc.id_scheme_account AS id_scheme_account,IF((IFNULL(totalpay.paidIns,0) + IFNULL(schAcc.paid_installments,0)) > 0, 1, 0) AS isPaid 
												FROM scheme AS sch
												LEFT JOIN (SELECT id_scheme, id_scheme_account, if(IFNULL(is_opening,0) = 0,0, IFNULL(paid_installments,0)) AS paid_installments FROM scheme_account WHERE id_customer = "'.$this->session->userdata('cus_id').'" AND is_closed != 1 AND active = 1) AS schAcc
												ON schAcc.id_scheme = sch.id_scheme
												LEFT JOIN (SELECT id_scheme_account,IFNULL(COUNT(DISTINCT(DATE_FORMAT(date_payment,"%Y%m"))),0) AS paidIns from payment WHERE payment_status = 1 GROUP BY id_scheme_account) AS totalpay ON totalpay.id_scheme_account = schAcc.id_scheme_account
												WHERE active = 1 ORDER BY id_scheme');
				if($query_scheme->num_rows() > 0)
				{
					foreach($query_scheme->result() as $row)
					{
						if($row->id_scheme_account != '')
						{
							$isJoined = 1; //not joined in any scheme
							$schemeJoined = 1; //for join button
						}
						else
						{
							$schemeJoined = 0;
						}
						$records[] = array('id' => $row->id_scheme, 'name' => $row->scheme_name, 'maturity_installment' => $row->maturity_installment, 'description' => $row->description,'scheme_type' => $row->scheme_type, 'amount' => $row->amount,'total_installments' => $row->total_installments,  'interest' => $row->interest, 'interest_by' => $row->interest_by,'interest_value' => $row->interest_value,'min_weight' => $row->min_weight,'max_weight' => $row->max_weight, 'min_chance' => $row->min_chance,'max_chance' => $row->max_chance,'schemeJoined' => $schemeJoined,'isPaid' => $row->isPaid);
					}
				}
			$query_scheme->free_result();
			$query_weight = $this->db->query('SELECT weight FROM weight WHERE active = 1');
				if($query_weight->num_rows() > 0)
				{
					foreach($query_weight->result() as $row)
					{
						$weight[] = array('weight' => $row->weight);
					}
				}
			$query_weight->free_result();
			$allow_unpaid =$this->allowUnpaid();	//allow customer not paid single installment		
			$allow_multiple = $this->allowMultipleChits(); //allow multiple chits for customer
			$unpaid = $this->check_unpaid_schemes($this->session->userdata('cus_id'));
			echo json_encode(array('scheme' => $records, 'weight' => $weight,'isJoined' => $isJoined,'unpaid' => $unpaid ,'allow_multiple' =>$allow_multiple,'allow_unpaid' =>$allow_unpaid ));
	}
	function sch_acc_count()
	{
		$sql = "SELECT id_scheme_account FROM scheme_account";
		return $this->db->query($sql)->num_rows();
	}	
	function limit_sch_acc()
	{
						$limit= $this->services_modal->limitDB('get','1');
						$sch_acc_count = $this->sch_acc_count(); //count of total scheme accounts 
						//print_r($sch_acc_count);exit;
						if($limit['limit_sch_acc']==1)
						{
							if($sch_acc_count < $limit['sch_acc_max_count'])
							{
								return FALSE;
							}else
							{
								return TRUE;
						 	}
						}else
						{
							return FALSE;	
						}
	}	
	/*function get_active_schemes()
	{
		$sql = "Select
					  s.id_scheme,s.scheme_name,s.scheme_type,s.code,s.id_classification,
					   s.description,cs.currency_name,cs.currency_symbol,cs.regExistingReqOtp,cs.newSchjoinonline,
					   IFNULL(s.min_chance,0) as min_chance,
					   IF(s.scheme_type=1,IFNULL(s.max_chance,0),1) as max_chance,
					   Format(IFNULL(s.max_weight,0),3) as max_weight,
					   Format(IFNULL(s.min_weight,0),3) as min_weight,
						Format(IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,
						s.max_weight,if(s.scheme_type=3,s.min_amount,0))),2) as payable,
					   Format(IF(s.scheme_type=0 || s.scheme_type=2 ,s.amount * s.total_installments,if(s.scheme_type=3 && s.max_amount!=0,s.max_amount* s.total_installments,(s.max_weight *(SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1)) * s.total_installments)),2) as total_payable,
					   Format(if(s.interest_by=0,s.interest_value,round(s.amount * (s.interest_value/100))),2) as interest,
					   s.total_installments,cmp.company_name,if(cus.referal_code!='',cus.referal_code,'null')as referal_code,cs.allow_referral as isReferal
				     From
					  scheme s
					  join chit_settings cs
					  join company cmp
					  join customer cus 
					  left join scheme_account  sa on(sa.id_customer=cus.id_customer)
				Where s.active=1 and s.visible=1 and cus.active=1 and cus.mobile=".$this->session->userdata('username')." group by s.id_scheme";
		$schemes = $this->db->query($sql)->result_array();
		$query_weight = $this->db->query('SELECT weight,cs.currency_name,cs.currency_symbol FROM weight join chit_settings cs WHERE active = 1 ');
		$weights =$query_weight->result_array();	
		$filename = base_url().'api/rate.txt'; 	
	    $data = file_get_contents($filename);
	    $metalrates = (array) json_decode($data);
		$allow_unpaid =$this->allowUnpaid();	//allow customer not paid single installment		
		$allow_multiple = $this->allowMultipleChits(); //allow multiple chits for customer
		$limit_exceeded = $this->limit_sch_acc(); //Scheme acc limit check
		$unpaid = $this->check_unpaid_schemes($this->session->userdata('cus_id')); 
		$unClosedAcc = $this->hasUnclosedAccounts($this->session->userdata('cus_id')); 
		$allow_join = '';
		if($limit_exceeded == FALSE)
		{			
			if($allow_multiple == TRUE )
			{
				if($allow_unpaid == TRUE)
				{
					$allow_join = array('status'=> TRUE);
				}
				else
				{
					if($unpaid == TRUE)
					{
						$allow_join = array('status'=> FALSE, 'msg' => 'You can\'t join now, as you have scheme accounts without single payment, make payments for unpaid before joining new scheme' );
					}
					else
					{
						$allow_join = array('status'=> TRUE);
					}
				}
			}
			else
			{
				if($unClosedAcc == TRUE)
				{
					$allow_join = array('status'=> FALSE, 'msg' => 'You have unclosed chits, kindly contact customer care and close to join new scheme.' );
				}
				else
				{
					$allow_join = array('status'=> TRUE);
				}
			}
			$sch = "Select id_scheme,scheme_name,scheme_type,code From scheme Where active=1";
			$schs = $this->db->query($sch)->result();
			$sch_list[]=array('id_scheme'=>" ",'code'=>" ");	
			foreach($schs as $scheme)
			{
				$sch_list[]=array(
					'id_scheme'=> $scheme->id_scheme,
					'code' => $scheme->code
				);
			} 
			return array('schemes' => $schemes,'sch_list' => $sch_list,'weights' =>$weights,'goldrate_22ct' => $metalrates['goldrate_22ct'],'allow_join' => $allow_join ); 
		}
	}*/
	function getSchemes()
	{
	    $data=$this->get_chitsettings();
		$is_branchwise_cus_reg=$this->session->userdata('is_branchwise_cus_reg');
		//print_r($this->session->all_userdata());
		$branchwise_scheme=$this->session->userdata('branchwise_scheme');
		$id_branch=$this->session->userdata('id_branch');
		//echo "<pre>";print_r($this->session->all_userdata()); 
		$sql = "Select
					  s.id_scheme,s.scheme_name,s.scheme_type,s.code,s.id_classification,s.id_metal,cs.is_multi_commodity,
					   s.description,cs.currency_name,cs.currency_symbol,cs.regExistingReqOtp,cs.newSchjoinonline,
					   IFNULL(s.min_chance,0) as min_chance,sb.id_branch,
					   IF(s.scheme_type=1,IFNULL(s.max_chance,0),1) as max_chance,
					   Format(IFNULL(s.max_weight,0),3) as max_weight,
					   Format(IFNULL(s.min_weight,0),3) as min_weight,
						Format(IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,
						s.max_weight,if(s.scheme_type=3,s.min_amount,0))),2) as payable,s.min_amount,s.max_amount,s.flx_denomintion,
					   Format(IF(s.scheme_type=0 || s.scheme_type=2 ,s.amount * s.total_installments,if(s.scheme_type=3 && s.max_amount!=0,s.max_amount* s.total_installments,(s.max_weight *(SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1)) * s.total_installments)),2) as total_payable,
					   Format(if(s.interest_by=0,s.interest_value,round(s.amount * (s.interest_value/100))),2) as interest,
					   s.total_installments,cmp.company_name,if(cus.cus_ref_code!='',cus.cus_ref_code,'')as cus_ref_code,if(cus.emp_ref_code!='',cus.emp_ref_code,'')as emp_ref_code,cs.allow_referral as isReferal,is_pan_required,cs.cusbenefitscrt_type,cs.empbenefitscrt_type,cs.is_branchwise_cus_reg,cs.cusName_edit,
					   s.sch_limit_value,cs.sch_limit,IFNULL(c.accounts,0)as accounts,
					   if(cs.is_kyc_required = 1,if(ifnull(cus.kyc_status,0) = 0,0,1 ),1) as cus_kyc_status,s.get_amt_in_schjoin,s.one_time_premium,s.is_enquiry,s.flexible_sch_type,
					   cs.is_branchwise_cus_reg,cs.branchwise_scheme,0 as askBranch,cs.branch_settings,s.rate_fix_by,s.rate_select,s.otp_price_fixing,s.otp_price_fix_type,s.agent_credit_type,s.agent_refferal
				     From
					  scheme s
					  join chit_settings cs
					  join company cmp
					  join customer cus 
					  left join scheme_account  sa on(sa.id_customer=cus.id_customer)
					   left join scheme_branch sb on sb.id_scheme=s.id_scheme
					  left join (SELECT sa.id_scheme,COUNT(sa.id_scheme_account)as accounts from scheme_account sa GROUP by sa.id_scheme) c on c.id_scheme=s.id_scheme
				Where s.active=1 and s.visible=1 and cus.active=1 and cus.mobile=".$this->session->userdata('username')." ".($id_branch!='' && $is_branchwise_cus_reg==1 &&$branchwise_scheme==1 ? 'and sb.id_branch='.$id_branch.'' :'')." group by s.id_scheme";
	//print_r($sql);exit;
		$schemes = $this->db->query($sql)->result_array();
			//print_r($this->db->last_query());exit;
		foreach($schemes as $key=>$sch){
		   $schemes[$key]['id_branch'] = ($id_branch == '' ? NULL : $id_branch); 
		   if($sch['branch_settings'] == 1){
        		if($sch['is_branchwise_cus_reg'] == 0){ 
    	    		if($sch['branchwise_scheme'] == 1){ 
    	    			$sch_branch = $this->db->query("SELECT id_branch from scheme_branch where id_scheme = ".$sch['id_scheme']);
    	    			$schBrData = $sch_branch->result_array();
    	    			if($sch_branch->num_rows() == 1){ // Donot ask the branch set the available branch
    						$schemes[$key]['id_branch'] = $schBrData[0]['id_branch'];
    					}
    					else if($sch_branch->num_rows() > 0){ // Ask the branch
    						$schemes[$key]['askBranch'] = 1;
    					}
    	    		}else{
    					$schemes[$key]['askBranch'] = 1;
    				}
    			}
    		} 
		}
		$query_weight = $this->db->query('SELECT weight,cs.currency_name,cs.currency_symbol FROM weight join chit_settings cs WHERE active = 1 ');
		$weights =$query_weight->result_array();	
		$filename = base_url().'api/rate.txt'; 	
	    $data = file_get_contents($filename);
	    $metalrates = (array) json_decode($data);
		$allow_unpaid =$this->allowUnpaid();	//allow customer not paid single installment		
		$allow_multiple = $this->allowMultipleChits(); //allow multiple chits for customer
		$limit_exceeded = $this->limit_sch_acc(); //Scheme acc limit check
		$unpaid = $this->check_unpaid_schemes($this->session->userdata('cus_id')); 
		$unClosedAcc = $this->hasUnclosedAccounts($this->session->userdata('cus_id')); 
		$allow_join = '';
		if($limit_exceeded == FALSE)
		{			
			if($allow_multiple == TRUE )
			{
				if($allow_unpaid == TRUE)
				{
					$allow_join = array('status'=> TRUE);
				}
				else
				{
					if($unpaid == TRUE)
					{
						$allow_join = array('status'=> FALSE, 'msg' => 'You can\'t join now, as you have scheme accounts without single payment, make payments for unpaid before joining new scheme' );
					}
					else
					{
						$allow_join = array('status'=> TRUE);
					}
				}
			}
			else
			{
				if($unClosedAcc == TRUE)
				{
					$allow_join = array('status'=> FALSE, 'msg' => 'You have unclosed chits, kindly contact customer care and close to join new scheme.' );
				}
				else
				{
					$allow_join = array('status'=> TRUE);
				}
			}
			$sch = "Select id_scheme,scheme_name,scheme_type,code,is_pan_required From scheme  where visible=1";
			$schs = $this->db->query($sch)->result();
			$sch_list[]=array('id_scheme'=>" ",'code'=>" ",'is_pan_required'=>" ",'scheme_name'=>"--Choose--");	
			foreach($schs as $scheme)
			{
				$sch_list[]=array(
					'id_scheme'=> $scheme->id_scheme,
					'code' => $scheme->code,
					'is_pan_required' => $scheme->is_pan_required,
					'scheme_name' => $scheme->scheme_name
				);
			} 
			return array('schemes' => $schemes,'sch_list' => $sch_list,'weights' =>$weights,'goldrate_22ct' => $metalrates['goldrate_22ct'],'allow_join' => $allow_join ); 
		}
	}
	function get_chitsettings()
  	{
   		$sql="SELECT * from  chit_settings";
		$result=  $this->db->query($sql)->row_array();
		return $result;   	
   }
/*Coded by ARVK*/
	function get_classifications()
    {
    	$id_branch = $this->session->userdata('id_branch');
    	$is_branchwise_cus_reg = $this->session->userdata('is_branchwise_cus_reg');
		$branchwise_scheme = $this->session->userdata('branchwise_scheme');
	    $result = ["classification" => array(), "commodities" => array(), "branches" => array()];
	    $CLSFY_IMG_PATH = base_url().'admin/assets/img/sch_classify/';
	    $sql = "SELECT  cs.branch_settings,cs.is_multi_commodity,id_classification, classification_name, description,concat('".$CLSFY_IMG_PATH."','',sc.logo) as logo
	    FROM sch_classify sc
	    join chit_settings cs
	    WHERE active =1";
	    $classifications = $this->db->query($sql)->result_array();
	    foreach($classifications as $clsfy){
	    	if($branchwise_scheme==1){
				$sch_sql = $this->db->query("SELECT id_classification,sb.id_branch,b.name
										    FROM scheme s 
											    left join scheme_branch sb on sb.id_scheme=s.id_scheme and scheme_active=1
											    LEFT JOIN branch b on b.id_branch = sb.id_branch
		    								WHERE ".($id_branch!='' && $is_branchwise_cus_reg ==1 ?' sb.id_branch='.$id_branch.' and'  :'')."  s.active=1 and s.visible=1 and id_classification=".$clsfy['id_classification']. " GROUP BY id_classification,id_branch");
			}else{
				$sch_sql = $this->db->query("SELECT id_classification,null as id_branch
											    FROM scheme s 
											    WHERE s.active=1 and s.visible=1 and id_classification=".$clsfy['id_classification']. " GROUP BY id_classification");
			}
	         $schemes = $sch_sql->result_array();
	         foreach($schemes as $sch){
	         	/*if($clsfy['is_multi_commodity'] == 1){
					$result["commodities"][$sch['id_metal']] = array(
													         "metal" => $sch['metal'],
													         "id_metal" => $sch['id_metal'],
					         								);
				}*/
		        if($clsfy['branch_settings'] == 1 && $is_branchwise_cus_reg == 0 && $branchwise_scheme == 1){
					$result["branches"][$sch['id_branch']] = array(
													         "name" => $sch['name'],
													         "id_branch" => $sch['id_branch'],
					         								);
				}
		        $result["classification"][] = array(
										         "id_classification" => $clsfy['id_classification'],
										         "id_branch" => $sch['id_branch'],
										         "classification_name"=> $clsfy['classification_name'],
										         "description"=> $clsfy['description'],
										         "logo"=> $clsfy['logo']
							        		 );
	        }
    	}
    	/*if($clsfy['branch_settings'] == 1 && $is_branchwise_cus_reg == 0 && $branchwise_scheme == 0){
    		$branches = $this->get_branch();
    		foreach($branches as $branch){
				$result["branches"][$branch['id_branch']] = array(
												         "name" => $branch['name'],
												         "id_branch" => $branch['id_branch'],
				         								);
			}
		}*/
    	return $result;
    }
	/*function get_classifications()
	{
	    $CLSFY_IMG_PATH = base_url().'admin/assets/img/sch_classify/';
		$sql = "SELECT  id_classification, classification_name, description,concat('".$CLSFY_IMG_PATH."','',sc.logo) as logo
				FROM sch_classify sc
				WHERE EXISTS (SELECT id_classification
					FROM scheme s
                  	WHERE sc.id_classification = s.id_classification and s.active=1 and s.visible=1)";
		$classifications = $this->db->query($sql)->result_array();
		return $classifications;
	}*/
	function get_classification($id)
   {
   	 $this->db->select('id_classification,classification_name,description');
   	 $this->db->where('id_classification',$id);
   	 $data=$this->db->get('sch_classify');
   	 return $data->row_array();
   }
   	function get_acc($sch_acc_id)  
    {	
		$sql = $this->db->query("SELECT sa.id_branch,s.id_metal  FROM  scheme_account sa 
		Left join scheme s On (s.id_scheme = sa.id_scheme)
		where sa.id_scheme_account=".$sch_acc_id.""); 
		//print_r($this->db->last_query());exit;
	    return $sql->row_array();
     } 	
	function join_scheme()
	{
//		$account_number = $this->account_number_generator($this->input->post('schemeID'));
		$sql = $this->db->query("select  cusbenefitscrt_type,empbenefitscrt_type,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as cus_name,cs.branchWiseLogin,cs.is_branchwise_cus_reg,cs.cusName_edit,c.id_branch from customer c
			join chit_settings cs 
		 where c.id_customer=".$this->session->userdata('cus_id').""); 
			$is_refferal_by = NULL;
			$data = $sql->row_array();
			$customer = $data['cus_name'];
			//print_r($customer);exit;
			$cusbenefitscrt_type = $data['cusbenefitscrt_type'];
			$empbenefitscrt_type = $data['empbenefitscrt_type'];
			// Referral code
			/* NOTE :-
				cusbenefitscrt_type -> 0 - Credit once , 1 - Credit on successive joining
				empbenefitscrt_type -> 0 - Credit once , 1 - Credit on successive joining
			*/
			if($this->input->post('referal_code')!=''){
				$ref_code = $this->veriflyreferral_code($this->input->post('referal_code'));
				if($ref_code['status']==1){
					$is_refferal_by = (strtoupper($ref_code['user']) == 'CUS' ? 0 :(strtoupper($ref_code['user']) == 'EMP'?1:NULL));
				//$chit_setting=$this->chitsettings_acc();
					  if( ($is_refferal_by==0 && $cusbenefitscrt_type==0) || ($is_refferal_by==1 && $empbenefitscrt_type==0) )
					  {
						  if($is_refferal_by==0)
						  {
							$cus_data  = array(
								    'referal_code'=>$this->input->post('referal_code'),
								    'id_customer'=>$this->session->userdata('cus_id'),
									'is_refferal_by'=>$is_refferal_by,
									'cus_single'=>$cusbenefitscrt_type
								     );	  
						  }
						  else
						  {
							  $cus_data  = array(
							    'referal_code'=>$this->input->post('referal_code'),
							    'id_customer'=>$this->session->userdata('cus_id'),
							    'is_refferal_by'=>$is_refferal_by,
								'emp_single'=>$empbenefitscrt_type
							     );	
						  }
							$rs = $this->available_refcode($cus_data);
					 }else   if( ($is_refferal_by==0 && $cusbenefitscrt_type==1) || ($is_refferal_by==1 && $empbenefitscrt_type==1) )
					 {
						 $cus_data  = array(
							    'id_customer'=>$this->session->userdata('cus_id'),
							    'is_refferal_by'=>$is_refferal_by,
								'cus_single'=>$cusbenefitscrt_type,
								'emp_single'=>$empbenefitscrt_type,
								'emp_ref_code' => NULL,
								'cus_ref_code' => NULL
							     );	
						$rs = $this->available_refcode($cus_data);
					 }
				}	
			}
			// referal_code check //
		if($this->session->userdata('branch_settings')==1)
		{	
			if($data['is_branchwise_cus_reg']==1)
			{
				$id_branch  = $data['id_branch'];
			}
			else if($data['branchWiseLogin']==1)
			{
			$id_branch  = $this->input->post('id_branch');
			}
			else
			{
			$id_branch  = $this->input->post('id_branch');
			}
		}
		else{
			$id_branch =NULL;
		}
        $schemeID= $this->input->post('schemeID');
        $schData=$this->get_schemeby_Id($schemeID);
        $entry_date = $this->get_entrydate($id_branch); // Taken from ret_day_closing  table branch wise //HH
					$custom_entry_date = $entry_date['custom_entry_date'];
        
        if($schData['is_enquiry']==0)
        {
            $sql_scheme = $this->db->query("select s.approvalReqForFP,cs.receipt_no_set,s.free_payment, s.amount, s.scheme_type, s.min_weight, s.max_weight, c.company_name, c.short_code ,s.gst,s.gst_type,s.maturity_days,maturity_type,total_installments,s.one_time_premium,s.rate_fix_by
		  										from scheme s join company c
		  										join chit_settings cs	
		  										where s.id_scheme=".$this->input->post('schemeID'));
		  	$sch_data = $sql_scheme->row_array();
		  	// 	1 - Flexible[Can pay installments and close], 2 - Fixed Maturity, 3 - Fixed Flexible[Increase maturity if has Default]
            $maturity_date = ( $sch_data['maturity_type'] == 2 ? ( $sch_data['maturity_days'] > 0 ? date('Y-m-d', strtotime(date('Y-m-d'). '+'.$sch_data['maturity_days'].' days')) : NULL ) : ( $sch_data['maturity_type'] == 3 ? date('Y-m-d', strtotime(date('Y-m-d'). '+'.$sch_data['total_installments'].' months')) : NULL) );
		    $scheme_acc  = array(
			                "id_scheme"         => $this->input->post('schemeID'),
		                    "id_customer"       => $this->session->userdata('cus_id'),
                            "account_name"      => ($this->input->post('account_name')!= '' ? ucfirst($this->input->post('account_name')):ucfirst($customer )), 
		                    "pan_no"            => ($this->input->post('pan_no') != '' ? strtoupper($this->input->post('pan_no')) : NULL), 
		                    "scheme_acc_number" => NULL,
		                    "referal_code"      => ($this->input->post('referal_code')!=''? $this->input->post('referal_code'):NULL ),
		                    "is_refferal_by"    => $is_refferal_by,
							"ref_no"            => '',
							"id_branch"         => $id_branch,
							"firstPayment_amt"  => ($this->input->post('payment_amount')!='' ? $this->input->post('payment_amount'):NULL),
		                    "start_date"        => date('Y-m-d H:i:s'),
		               //   "custom_entry_date" => (isset($data['custom_entry_date']) ? $data['custom_entry_date']:NULL),
			                "custom_entry_date" => ($custom_entry_date ? $custom_entry_date:NULL),
		                    'maturity_date'     => $maturity_date,
		                    "date_add"          => date('Y-m-d H:i:s'),
		                    "is_new"            => 'Y', 
		                    "active"            => 1
		                );
			  	$sch_data['id_scheme'] = $this->input->post('schemeID');
			if($this->db->insert('scheme_account', $scheme_acc))
			{
			    	//print_r($this->db->last_query());exit;
				$insertID = $this->db->insert_id();
				$data = array('is_scheme_selected' => 1);
				$this->session->set_userdata($data);
				$status = array("status" => true, "insertID" => $insertID, "sch_data"=>$sch_data,"is_enquiry"=>$schData['is_enquiry']);
			}
			else
			{
				$status = array("status" => false, "insertID" => '');
			}
	    }else
	    {
	            $interested_amt=$this->input->post('interseted_amount');
	            $interseted_weight=$this->input->post('interseted_weight');
	            $message=$this->input->post('message');
	        	$scheme_acc=array(
	        	                    'id_scheme'         => $this->input->post('schemeID'),
	        	                    'id_customer'       =>$this->session->userdata('cus_id'),
	        	                    'intresred_amt'     => (isset($interested_amt) ? $interested_amt:NULL),
	        	                    'intrested_wgt'     => (isset($interseted_weight) ? $interseted_weight:NULL),
	        	                    'message'           => (isset($message) ? $message:NULL),
	        	                    'enquiry_date'          => date('Y-m-d H:i:s'),
	        	                 );
	           $this->db->insert('sch_enquiry', $scheme_acc);
	           //print_r($this->db->last_query());exit;
	        	if($this->db->trans_status()===TRUE)
	        	{
	        	        $this->db->trans_commit();
			            $status = array("status" => true,'name'=>$customer,'intresred_amt'=>$interested_amt,'interseted_weight'=>$interseted_weight,"one_time_premium"=>$schData['one_time_premium']);
	        	}
	        	else
	        	{   
	        	        $this->db->trans_rollback();
	        	    	$status = array("status" => false);
	        	}
	    }
			return $status;
	} 
	
	
		function get_entrydate($id_branch)

	{
        $sql = "SELECT entry_date as custom_entry_date,cs.edit_custom_entry_date FROM ret_day_closing 
		join chit_settings cs 
	".($id_branch!='' ?' where id_branch='.$id_branch.''  :'')."";
        // print_r($sql);exit;
	     $result=$this->db->query($sql);
	     return $result->row_array();
  	
	}
	
	function get_schemeby_Id($id_scheme)
	{
	    $sql="select * from scheme where id_scheme=".$id_scheme."";
	    $result=$this->db->query($sql)->row_array();
	    return $result;
	}
	function update_account($data,$id)
	{
		$this->db->where('id_scheme_account',$id);
		$status=$this->db->update('scheme_account',$data);
		return $status;
	}
	function get_receipt_no()
    {
		$sql = "Select max(receipt_no) as receipt_no
				From payment
				Where payment_status=1";
		return $this->db->query($sql)->row()->receipt_no;		
	}
	
	function join_existing_createac($scheme_acc) // directly create a/c
	{
			if($this->db->insert('scheme_account', $scheme_acc))
			{
				$insertID = $this->db->insert_id();
				$data = array('is_scheme_selected' => 1);
				$this->session->set_userdata($data);
				$status = array("status" => true, "insertID" => $insertID);
			}
			else
			{
				$status = array("status" => false, "insertID" => '');
			}
			return $status;
	}
	function join_existing($scheme_acc)
	{
			if($this->db->insert('scheme_reg_request', $scheme_acc))
			{
				$insertID = $this->db->insert_id();
				$status = array("status" => true, "insertID" => $insertID);
			}
			else
			{
				$status = array("status" => false, "insertID" => '');
			}
			//echo $this->db->last_query();exit;
			return $status;
	}
/*  //Generate 10 digit Account number random
	function account_number_generator()
	{
	  $query = $this->db->query("SELECT LPAD(round(rand() * 1000000000),10,0) as myCode
								FROM scheme_account
								HAVING myCode NOT IN (SELECT scheme_acc_number FROM scheme_account) limit 0,1");
		if($query->num_rows()==0){
			$query = $this->db->query("SELECT LPAD(round(rand() * 1000000000),10,0) as myCode");
		}
		return $query->row()->myCode;
	}*/
	//Generate account number 
	function account_number_generator($id_scheme)
	{
	  $lastno=$this->get_schAccount_no($id_scheme);
	  if($lastno!=NULL)
		{
		  	$number = (int) $lastno;
			$number++;
    		$schAc_number=str_pad($number, 5, '0', STR_PAD_LEFT);;
    		return $schAc_number;
		}
		else
		{
			$schAc_number=str_pad('1', 5, '0', STR_PAD_LEFT);;
    		return $schAc_number;
		}
	}
	function get_schAccount_no($id_scheme)
    {
		$sql = "SELECT max(scheme_acc_number) as lastSchAcc_no FROM scheme_account where id_scheme=".$id_scheme." ORDER BY id_scheme_account DESC";
		//$sql = "SELECT max(TRIM(LEADING '0' FROM scheme_acc_number))  as lastSchAcc_no FROM scheme_account where id_scheme=".$id_scheme." ORDER BY id_scheme_account DESC ";
		return $this->db->query($sql)->row()->lastSchAcc_no;		
	}
	//check reference exists
	function is_refno_exists($ref_no)
	{
		$this->db->select('ref_no');
		$this->db->where('ref_no', $ref_no); 
		$status=$this->db->get(self::ACC_TABLE);
		if($status->num_rows()>0)
		{
			 return TRUE;
		}
	}
	function get_currentScheme()
	{
		$records = array();
		$query_scheme = $this->db->query('SELECT sa.id_scheme_account as scheme_account ,s.id_scheme as id_scheme,scheme_name, scheme_type, description,amount,total_installments, min_weight, max_weight, min_chance,max_chance, interest_by, interest_value, payment_amount, date_payment, id_payment,metal_rate,metal_weight FROM scheme_account as sa LEFT JOIN scheme as s ON s.id_scheme = sa.id_scheme LEFT JOIN customer as cus ON cus.id_customer = sa.id_customer LEFT JOIN payment as p ON p.id_scheme_account = sa.id_scheme_account AND MONTH(date_payment) = MONTH(CURDATE()) WHERE mobile="'.$this->session->userdata('username').'"');
			if($query_scheme->num_rows() > 0)
			{
				foreach($query_scheme->result() as $row)
				{
					$records[] = array('scheme_account' => $row->scheme_account,'id_scheme' => $row->id_scheme, 'scheme_name' => $row->scheme_name,'description' => $row->description,'scheme_type' => $row->scheme_type, 'amount' => $row->amount,'total_installments' => $row->total_installments,'min_weight' => $row->min_weight,'max_weight' => $row->max_weight, 'min_chance' => $row->min_chance,'max_chance' => $row->max_chance, 'interest_by' => $row->interest_by,'interest_value' => $row->interest_value,'payment_amount' => $row->payment_amount,'date_payment' => $row->date_payment,'id_payment' => $row->id_payment,'metal_rate' => $row->metal_rate,'metal_weight' => $row->metal_weight);
				}
			}
		$records = array();
		$query_weight = $this->db->query('SELECT * FROM  weight');
			if($query_weight->num_rows() > 0)
			{
				foreach($query_weight->result() as $row)
				{
					$weight[] = array('id_weight' => $row->id_weight,'weight' => $row->weight);
				}
			}
		$query_scheme->free_result();
		return array('scheme' => $records,'weight' => $weight);
	}
	function getJoinedScheme($schemeID)
	{
		$records = array();
		$query_scheme = $this->db->query('SELECT sa.id_scheme_account as scheme_account ,s.id_scheme as id_scheme,scheme_name,  if(cs.has_lucky_draw=1,concat(IFNULL(sa.group_code,"")," ",IFNULL(sa.scheme_acc_number,"Not Allocated")),concat(s.code," ",IFNULL(sa.scheme_acc_number,"Transcation Pending")))as scheme_acc_number,s.max_amount, s.min_amount, s.scheme_type,email,firstname,lastname,s.amount,s.max_weight,s.min_weight,s.payment_chances,s.total_installments,sa.account_name,cs.currency_symbol,cs.currency_name  
	FROM scheme_account as sa 
	join chit_settings cs
	LEFT JOIN scheme as s ON s.id_scheme = sa.id_scheme LEFT JOIN customer as cus ON cus.id_customer = sa.id_customer WHERE sa.id_scheme_account='.$schemeID);
			if($query_scheme->num_rows() > 0)
			{
				foreach($query_scheme->result() as $row)
				{
					$records[] = array('scheme_account' => $row->scheme_account,'id_scheme' => $row->id_scheme, 'scheme_name' => $row->scheme_name,'scheme_type' => $row->scheme_type,'scheme_acc_number' => $row->scheme_acc_number,'email' => $row->email,'firstname' => $row->firstname,'lastname' => $row->lastname,'amount' => $row->amount,'min_amount' => $row->min_amount,'max_amount' => $row->max_amount,'max_weight' => $row->max_weight,'min_weight' => $row->min_weight,'payment_chances' => $row->payment_chances,'total_installments' => $row->total_installments,'account_name' => $row->account_name,'currency_symbol' => $row->currency_symbol,'currency_name' => $row->currency_name);
				}
			}
		$query_scheme->free_result();
		return $records;
	}
	public function get_closeScheme()
	{
		$records = array();
		$query = $this->db->query("SELECT `ref_no` AS `scheme_acc_number`, `scheme_name`, `scheme_account`.`id_scheme_account` AS `id_scheme_account`, `req_close`, `total_installments`, IFNULL(`paid_installments`,0) + IFNULL(`totalpay`.`paidIns`,0) AS `paid_installments` FROM (`scheme_account`) LEFT JOIN `scheme` ON `scheme`.`id_scheme` = `scheme_account`.`id_scheme` LEFT JOIN (SELECT id_scheme_account,COUNT(DISTINCT(DATE_FORMAT(date_payment,'%Y%m'))) AS paidIns from payment WHERE (payment_status = 0 OR payment_status = 1) GROUP BY id_scheme_account) AS totalpay ON `totalpay`.`id_scheme_account` = `scheme_account`.`id_scheme_account` WHERE `is_closed` != 1 AND `scheme_account`.`id_customer` = ".$this->session->userdata('cus_id'));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$records[] =  array('id_scheme_account' => $row->id_scheme_account,'scheme_acc_number' => $row->scheme_acc_number, 'scheme_name' => $row->scheme_name, 'paid_installments' => $row->paid_installments,'total_installments' => $row->total_installments, 'req_close' => $row->req_close);
			}
		}
		return array("schemeClose" =>  $records);
	}
	function close_scheme()
	{
		if($this->input->post('remark_close'))
			$updateData = array("remark_close" => $this->input->post('remark_close'),"req_close"=>1);
		else
			$updateData = array("remark_close" => '',"req_close"=>0);
		$this->db->where('id_scheme_account', $this->input->post('id_scheme_account'));
		if($this->db->update("scheme_account",$updateData))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function verify_existing($data)
		{
			if($this->session->userdata('branch_settings')==1)
			{
			$this->db->select('id_scheme_account,id_customer');
			$this->db->where('scheme_acc_number',$data['scheme_acc_number']);
			$this->db->where('id_branch',$data['id_branch']);
			$this->db->where('id_scheme',$data['id_scheme']);
			if($data['group_code'] != NULL){
				   $this->db->where('group_code',$data['group_code']);
				}
			$schAcc = $this->db->get('scheme_account');
			}
			else{
			$this->db->select('id_scheme_account,id_customer');
			$this->db->where('scheme_acc_number',$data['scheme_acc_number']);	
			$this->db->where('id_scheme',$data['id_scheme']);
			if($data['group_code'] != NULL){
				   $this->db->where('group_code',$data['group_code']);
				}
			$schAcc = $this->db->get('scheme_account');
			}
			if($schAcc->num_rows() > 0)
			{
			return array('status' => TRUE , 'table'=>'scheme_account');
			}else{
			$this->db->select('id_reg_request,id_customer');
			$this->db->where('id_branch',$data['id_branch']);
			if($data['id_scheme_group'] != ''){
			   $this->db->where('id_scheme_group',$data['id_scheme_group']);
			}
			$this->db->where('id_scheme',$data['id_scheme']);
			$this->db->where('scheme_acc_number',$data['scheme_acc_number']);
			$schAcc = $this->db->get('scheme_reg_request');
			//print_r($schAcc);exit;
			if($schAcc->num_rows() > 0)
			{
			return array('status' => TRUE , 'table'=>'scheme_reg_request');
			}else{
			return FALSE;
			}
			}	
		}
	function scheme_status()
	{
		if($this->session->userdata('cus_id'))
			$cus_id = $this->session->userdata('cus_id');
		else
			$cus_id = 0;
		$scheme_status = 0;
		$query_scheme = $this->db->query('SELECT * FROM scheme_account AS schAcc LEFT JOIN payment AS pay ON pay.id_scheme_account = schAcc.id_scheme_account WHERE is_closed = 0 AND id_customer='.$cus_id);
				if($query_scheme->num_rows() > 0)
				{
					$scheme_status = 1;
					foreach($query_scheme->result() as $row)
					{
						if($row->id_payment !== NULL)
						{
							$scheme_status = 2;
						}
					}
				}
			$query_scheme->free_result();
		return $scheme_status;
	}
	function no_avail_schemes()
	{
		$records = array();
		$query_scheme = $this->db->query('SELECT id_scheme,code,scheme_name FROM scheme WHERE active  = 1');
				if($query_scheme->num_rows() > 0)
				{
					foreach($query_scheme->result() as $row)
					{
						$records[] = array("id_scheme" => $row->id_scheme,"code" => $row->code,"scheme_name" => $row->scheme_name);
					}
				}
			$query_scheme->free_result();
		return array('scheme' => $records);
	}
	function check_unpaid_schemes($id_customer)
	{
		$sql="SELECT * FROM scheme_account sa
			  Left Join payment p ON (sa.id_scheme_account=p.id_scheme_account And p.payment_status=1)
			Where sa.id_customer ='$id_customer' and is_closed=0 and p.id_scheme_account IS NULL and sa.paid_installments=0
			GROUP BY id_scheme";
		$unpaid = $this->db->query($sql);	
		if($unpaid->num_rows()>0)
		{
			return TRUE;
		}	
	}
	/*
	function check_unpaid_schemes($id_customer)
	{
		$sql="SELECT sa.id_scheme_account,IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),1,0) as  previous_paid FROM scheme_account sa
			  Left Join payment p ON (sa.id_scheme_account=p.id_scheme_account And p.payment_status=1)
			Where sa.id_customer ='$id_customer' and is_closed=0 and p.id_scheme_account IS NULL 
			GROUP BY id_scheme";
		$unpaid = $this->db->query($sql);	
		$result = $unpaid->result_array();	
		if($unpaid->num_rows()>0)
		{
			if($result['last_paid_date'] != NULL){
				return ($result['previous_paid'] == 1 ? FALSE : TRUE);
			}
			else{
				return TRUE;	
			}
		}	
	}*/
	function hasUnclosedAccounts($id_customer)
	{
		$sql="SELECT COUNT(id_scheme_account) as accounts
				FROM scheme_account
				WHERE is_closed=0 and id_customer ='$id_customer'
				GROUP BY id_customer";
		$unpaid = $this->db->query($sql);	
		if($unpaid->num_rows()>0)
		{
			return TRUE;
		}	
	}
	//to check whether to allow customers to join multiple scheme
	function allowMultipleChits()
	{
		$sql ="Select allow_join_multiple From chit_settings";
		$allow_multiple = $this->db->query($sql);	
		if($allow_multiple->row('allow_join_multiple')==1)
		{
			return TRUE;			
		}		
	}	
	//to check whether to not allow new scheme join 
	function allowNewscheme_join()
	{
		$sql ="Select newSchjoinonline From chit_settings";
		$allowNewscheme = $this->db->query($sql);	
		if($allowNewscheme->row('newSchjoinonline') == 1)
		{
			return TRUE;			
		}		
	}	
	//to check whether to allow customers to join even if any unpaid chit exists
	function allowUnpaid()
	{
		$sql ="Select allow_join_unpaid From chit_settings";
		$allow_multiple = $this->db->query($sql);	
	   if($allow_multiple->row('allow_join_unpaid')==1)
		{
			return TRUE;			
		}
	}	
	//to check whether to allow customers to delete unpaid chit exists
	function deleteUnpaid()
	{
		$sql ="Select delete_unpaid From chit_settings";
		$allow_multiple = $this->db->query($sql);	
	   if($allow_multiple->row('delete_unpaid')==1)
		{
			return TRUE;			
		}
		else
		{
		   	return FALSE;
		}
	}
	//to check whether to allow customers to register existing scheme
	function getChitSettings()
	{
		$sql="Select * FROM chit_settings c where c.id_chit_settings = 1";
		return $this->db->query($sql)->row_array();
	}
	function regExistingScheme()
	{
		$sql ="Select reg_existing From chit_settings";
		$allow_multiple = $this->db->query($sql);	
		$result=$this->db->query($sql);	   
		return $result->row('reg_existing');	
	}
	function regExistingReqOtpSch()
	{
		$sql ="Select regExistingReqOtp From chit_settings";
		$allow_multiple = $this->db->query($sql);	
		$result=$this->db->query($sql);	   
		return $result->row('regExistingReqOtp');	
	}
	function joinExistingSchjoinadmin()
	{
		$sql ="Select ExistingSchjoinoffline From chit_settings";
		$allow_multiple = $this->db->query($sql);	
		$result=$this->db->query($sql);	   
		return $result->row('ExistingSchjoinoffline');	
	}
	//to show closed accounts list to cutomers
	function showClosedAcc()
	{
		$sql ="Select show_closed_list From chit_settings";
		$allow_multiple = $this->db->query($sql);	
		$result=$this->db->query($sql);	   
		return $result->row('show_closed_list');	
	}
	//to show Coin Enq list to cutomers//HH
	function showcoinenq()
	{
		$sql ="Select enable_coin_enq From chit_settings";
		$allow_multiple = $this->db->query($sql);	
		$result=$this->db->query($sql);	   
		return $result->row('enable_coin_enq');	
	}
	function delete_scheme_account($id)
	{
        $pay = $this->db->query("SELECT p.id_payment FROM payment p where p.id_scheme_account=".$id."");
        if($pay->num_rows() > 0){
            return	array('status' => false, 'DeleteID' => $id);
        }
        else{
            $this->db->where("id_scheme_account",$id);
    		$status = $this->db->delete("scheme_account");
    		return	array('status' => $status, 'DeleteID' => $id);
        }
	}	
	function delete_payment($id)
	{
		$this->db->where("id_payment",$id);
		$status = $this->db->delete("payment");
		return	array('status' => $status, 'DeleteID' => $id);
	}	
	function get_payment($id)
	{
		$payid=$this->db->query("SELECT p.id_payment FROM payment p where p.id_scheme_account=".$id."");
		return $payid->row_array();
	}	 
	function get_scheme_detail($id_metal)
	{
	    $showGCodeInAcNo = $this->config->item('showGCodeInAcNo');  	    
	    $accounts = $this->db->query("Select
	    			sa.auto_debit_status,s.auto_debit_plan_type,ad.auth_link,
					maturity_installment,sa.id_scheme_account,sg.group_code as scheme_group_code, UNIX_TIMESTAMP(Date_Format(sg.start_date,'%Y-%m-%d')) as group_start_date,  UNIX_TIMESTAMP(Date_Format(sg.end_date,'%Y-%m-%d')) as  group_end_date,  cs.has_lucky_draw,Date_Format(sa.fixed_rate_on,'%d-%m-%Y') as fixed_rate_on,IF(sa.fixed_rate_on is NULL,'NO','YES') as is_rate_fixed, 
                    s.id_scheme, IF(rate_fixed_in = 1, 'Web App', IF(rate_fixed_in = 2, 'Mobile App', IF(rate_fixed_in = 3, 'Offline', '-'))) as rate_fixed_in,s.is_lucky_draw as is_lucky_draw,
					c.id_customer,s.flexible_sch_type,s.one_time_premium,s.is_enquiry,IFNULL(sa.fixed_wgt,'-')fixed_wgt,s.id_metal,m.metal,cs.is_multi_commodity,
					IF(c.lastname IS NULL,c.firstname,CONCAT(c.firstname,' ',c.lastname)) customer_name,
					CONCAT(if(".$showGCodeInAcNo."=1,if(has_lucky_draw = 1 && is_lucky_draw = 1,sg.group_code,s.code),'') ,' ',ifnull(sa.scheme_acc_number,'Not Allocated')) as scheme_acc_number,
					IFNULL(sa.account_name,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname))) as account_name,
					c.mobile,cs.currency_name,cs.currency_symbol,
					s.scheme_name,
					IF(s.scheme_type=0,'Amount',IF(s.scheme_type=1,'Weight',IF(s.scheme_type=3,'Flexible','Amount to Weight'))) AS scheme_type,ifnull(p.add_charges,0.00) as add_charges,
					s.code,s.min_amount,s.max_amount,
					IFNULL(s.min_chance,0) as min_chance,
					IFNULL(s.max_chance,0) as max_chance,
					Format(IFNULL(s.max_weight,0),3) as max_weight,
					Format(IFNULL(s.min_weight,0),3) as min_weight,Date_Format(sa.start_date,'%d-%m-%Y')start_date,
					IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,s.max_weight,if(s.scheme_type=3,if(s.firstPayamt_as_payamt=1,sa.firstPayment_amt ,s.min_amount),0))) as payable,
					s.total_installments,s.amount,
					cp.paid_installment as paid_ins,
					IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)
  as paid_installments,										  
IFNULL(IF(sa.is_opening=1,IFNULL(balance_amount,0)+IFNULL(SUM(p.payment_amount * p.no_of_dues),0),IFNULL(SUM(p.payment_amount * p.no_of_dues),0)) ,0)
  as total_paid_amount,
  FORMAT(sum(if(p.gst > 0,if((p.gst_type = 1),0,p.payment_amount-(p.payment_amount*(100/(100+p.gst)))),0)),0) as paid_gst,
IFNULL(IF(sa.is_opening=1,IFNULL(balance_weight,0)+IFNULL(SUM(p.metal_weight),0),IFNULL(SUM(p.metal_weight),0)),0.000)
 as total_paid_weight,
										ROUND(IFNULL(cp.total_amount,0),2) as  current_paid_amount,
				    					ROUND(IFNULL(cp.total_weight,0),3) as  current_paid_weight,
				    					IFNULL(cp.paid_installment,0)       as  current_paid_installments,
				    					IFNULL(cp.chances,0)                as  current_chances_used,
										s.is_pan_required,
										 if(s.allow_unpaid=1,s.unpaid_months,0) as allow_unpaid_month,
										Date_Format(max(p.date_payment),'%d-%m-%Y') as last_paid_date,
										TIMESTAMPDIFF(MONTH, max(Date(p.date_payment)), Date(Current_Date())) as duration,
										sa.active as chit_active,
										sa.is_closed as is_closed,
										pp.total_pdc as pdc,
										pp.pdc_status as pdc_status,
										(select SUM(CASE
					                                WHEN p.payment_status=1 OR p.payment_status=2  THEN 1
					                                ELSE 0
					                                END) as has_payment
					                      from payment p where p.id_scheme_account=sa.id_scheme_account)as has_payment,
										Date_Format(pp.last_paid_date,'%d-%m-%Y') as last_pdc_date,br.name as branch_name,br.id_branch,s.one_time_premium
									From scheme_account sa
									Left Join scheme_group sg On (sa.group_code = sg.group_code )
									Left Join scheme s On (sa.id_scheme=s.id_scheme)
									left join metal m on(m.id_metal=s.id_metal)
									Left Join branch br On (sa.id_branch=br.id_branch)
									Left Join payment p On (sa.id_scheme_account=p.id_scheme_account and (p.payment_status=1 or p.payment_status = 2 or p.payment_status = 8))
									LEFT JOIN auto_debit_subscription ad on ad.id_scheme_account = sa.id_scheme_account and ad.status=1
	    							Left Join customer c On (sa.id_customer=c.id_customer and c.active=1)
									join chit_settings cs
										Left Join
													(	Select
														  sa.id_scheme_account,
														  COUNT(Distinct Date_Format(p.date_payment,'%Y%m')) as paid_installment,
														  COUNT(Date_Format(p.date_payment,'%Y%m')) as chances,
														  SUM(p.payment_amount) as total_amount,
														  SUM(p.metal_weight) as total_weight
														From payment p
														Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0)
														Where p.payment_status=1 and Date_Format(Current_Date(),'%Y%m')=Date_Format(p.date_payment,'%Y%m')
														Group By sa.id_scheme_account
													) cp On (sa.id_scheme_account=cp.id_scheme_account)
										Left Join(Select
													    id_scheme_account,payment_status as pdc_status,
													    Count(id_post_payment) as total_pdc,
													    Max(date_payment) as last_paid_date
													From postdate_payment
													Where payment_status!=1
													Group By id_scheme_account
											      ) pp on (sa.id_scheme_account=pp.id_scheme_account)				
									Where sa.active=1 and sa.is_closed = 0 and sa.id_customer=".$this->session->userdata('cus_id')."  ".($id_metal!=''?  "and s.id_metal=".$id_metal." " :'')."
									Group By sa.id_scheme_account");
		return $accounts->result_array();
	}
		//report  <!--Rate Fixed Flag show in user side HH -->
	  function get_account_details($id_scheme_account)
	{
	    $showGCodeInAcNo = $this->config->item('showGCodeInAcNo'); 
		$sql="SELECT c.id_customer, sa.auto_debit_status,ad.auth_link,s.auto_debit_plan_type,s.total_installments,DATE_FORMAT(ad.expires_on,'%d-%m-%Y') as sub_expires_on,
		IF(rate_fixed_in = 1, 'Web App', IF(rate_fixed_in = 2, 'Mobile App', IF(rate_fixed_in = 3, 'Offline', '-'))) as rate_fixed_in,s.has_gift,s.has_prize,s.id_metal,m.metal,cs.is_multi_commodity,
		c.cus_img, sa.account_name,IFNULL(sa.scheme_acc_number,'') as scheme_acc_number,sa.id_branch,sa.id_scheme_account,s.is_lucky_draw as is_lucky_draw,
		CONCAT(if(".$showGCodeInAcNo."=1,if(has_lucky_draw = 1 && is_lucky_draw = 1,sg.group_code,s.code),'') ,' ',ifnull(sa.scheme_acc_number,'Not Allocated')) as scheme_ac,sa.firstPayment_amt,sa.fixed_wgt,sa.fixed_metal_rate,sa.fixed_rate_on,sa.id_branch,
			sg.group_code as scheme_group_code, UNIX_TIMESTAMP(Date_Format(sg.start_date,'%Y-%m-%d')) as group_start_date,  UNIX_TIMESTAMP(Date_Format(sg.end_date,'%Y-%m-%d')) as  group_end_date,  cs.has_lucky_draw as has_lucky_draw ,
		if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,b.name as branch_name,s.otp_price_fix_type,sch.id_customer as sch_enq_customer,
			c.mobile, DATE_FORMAT(sa.`start_date`,'%d-%m-%Y') as start_date, DATE_FORMAT(sa.`fixed_rate_on`,'%d-%m-%Y') as fixed_rate_on,s.min_amount,s.max_amount,
			IF(s.scheme_type=0,'Amount',IF(s.scheme_type=1,'Weight',IF(s.scheme_type=3,'Flexible','Amount to Weight'))) AS scheme_type,s.flexible_sch_type,sa.fixed_wgt,
			s.code as scheme_code, s.total_installments,s.min_weight,
			s.max_weight, IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,s.max_weight,if(s.scheme_type=3,s.min_amount,0))) as payable, if(sa.balance_amount is null,0,sa.balance_amount) as balance_amount,
			 if(sa.balance_weight is null,0,sa.balance_weight) as balance_weight, lg.paid_ins as paid_installments, s.scheme_type as type, cs.currency_name,
			 cs.currency_symbol, sa.paid_installments as ins,a.address1,a.address2,a.address3,st.name as state,ct.name as city,cy.name as country,a.pincode,s.one_time_premium,DATE_FORMAT(sa.`maturity_date`,'%d-%m-%Y') as maturity_date
			 from customer c
				 left join scheme_account sa on(sa.id_customer=c.id_customer)
				 Left Join scheme_group sg On (sa.group_code = sg.group_code )
				 left join scheme s on(s.id_scheme=sa.id_scheme)
				 left join metal m on(m.id_metal=s.id_metal)
				 left join sch_enquiry sch on(sch.id_scheme=sa.id_scheme)
				 left join address a on c.id_customer=a.id_customer
				 left join country cy on (a.id_country=cy.id_country)
				 left join state st on (a.id_state=st.id_state)
				 left join city ct on (a.id_city=ct.id_city)
				  left join branch b on (b.id_branch=sa.id_branch)
				  LEFT JOIN auto_debit_subscription ad on ad.id_scheme_account = sa.id_scheme_account and ad.status=1
				 left join ( select 
				 		   sch.id_scheme_account ,
						   IFNULL(IF(sch.is_opening=1,IFNULL(sch.paid_installments,0)+ IFNULL(if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0)
  as paid_ins
						 From payment pay
						   Left Join scheme_account sch on(pay.id_scheme_account=sch.id_scheme_account and sch.active=1 and sch.is_closed=0)
						   Left Join scheme sc on(sc.id_scheme=sch.id_scheme) 
						   Where (pay.payment_status=2 or pay.payment_status=1 or pay.payment_status=8 ) 
						   Group By sch.id_scheme_account) lg on (sa.id_scheme_account=lg.id_scheme_account )
					join chit_settings cs
				WHERE sa.id_scheme_account='$id_scheme_account' AND c.id_customer=".$this->session->userdata('cus_id')."";
//print_r($sql);exit;
		$account=$this->db->query($sql);	   
		return $account->row_array();	
	}
	// showed gift/price details to the users based on the type //HH
	function get_gift($id_scheme_account)
	{
		$sql="SELECT c.id_customer,sa.id_scheme_account,gi.gift_desc, Date_Format(gi.date_issued,'%d-%m-%Y') as date_issued,s.has_gift,gi.type FROM  customer c
		left join scheme_account sa on(sa.id_customer=c.id_customer)
		left join gift_issued gi on (gi.id_scheme_account=sa.id_scheme_account)
		left join scheme s on(s.id_scheme=sa.id_scheme)
		WHERE sa.id_scheme_account='$id_scheme_account' AND gi.type=1 AND c.id_customer=".$this->session->userdata('cus_id')."";
	//print_r($this->db->last_query());exit;
	$gift=$this->db->query($sql);
				return $gift->result_array();
	}
	function get_prize($id_scheme_account)
	{
		$sql="SELECT c.id_customer,sa.id_scheme_account,gi.gift_desc, Date_Format(gi.date_issued,'%d-%m-%Y') as date_issued,s.has_prize,gi.type FROM  customer c
		left join scheme_account sa on(sa.id_customer=c.id_customer)
		left join gift_issued gi on (gi.id_scheme_account=sa.id_scheme_account)
		left join scheme s on(s.id_scheme=sa.id_scheme)
		WHERE sa.id_scheme_account='$id_scheme_account' AND gi.type=2 AND c.id_customer=".$this->session->userdata('cus_id')."";
	//print_r($this->db->last_query());exit;
		$prize=$this->db->query($sql);
				return $prize->result_array();
	}
	//Amount
	function get_payment_detail($id_scheme_account)
	{
		$sql="Select 
				p.id_payment  as ins_no,b.name,
				DATE_FORMAT(p.date_payment,'%d-%m-%Y') as date_payment,
				p.payment_mode as mode,
				s.scheme_type as type,
				if(sa.balance_amount is null,0,sa.balance_amount) as balance_amount,
				p.payment_amount,ifnull(p.add_charges,0.00) as add_charges,
				cs.currency_name,
                cs.currency_symbol,p.gst,p.gst_type ,psm.payment_status as payment_status
			FROM payment p
				left join scheme_account sa on(sa.id_scheme_account=p.id_scheme_account)
				left join scheme s on(s.id_scheme=sa.id_scheme)
				left join customer c on(c.id_customer=sa.id_customer)
				left join branch b on (p.id_branch=b.id_branch)
				join chit_settings cs
				LEFT Join payment_status_message psm ON (p.payment_status=psm.id_status_msg)
			WHERE sa.id_scheme_account = '$id_scheme_account' and (p.payment_status=1 or p.payment_status=2) and s.scheme_type=0";
				$payments=$this->db->query($sql);
				return $payments->result_array();
	}
	//report weight
	function get_details($id_scheme_account)
	{
		$sql="Select 
				p.id_payment as ins_no,
				DATE_FORMAT(p.date_payment,'%d-%m-%Y') as date_payment,s.one_time_premium,s.rate_fix_by,s.rate_select,Date(sa.start_date) as start_date,
				p.payment_mode as mode,
				s.scheme_type as type,IFNULL(sa.scheme_acc_number,'Not Allocated')as scheme_acc_number,sa.id_branch,
				if(sa.balance_amount is null,0,sa.balance_amount) as balance_amount,
				if(sa.balance_weight is null,0,sa.balance_weight) as balance_weight,
				p.payment_amount,
				p.metal_rate,
ifnull(p.add_charges,0.00) as add_charges,
				p.metal_weight,
				cs.currency_name,
                cs.currency_symbol ,p.gst,p.gst_type,psm.payment_status as payment_status,
                concat(if(p.due_month=1,'JAN',if(p.due_month=2,'FEB',if(p.due_month=3,'MAR',if(p.due_month=4,'APR',if(p.due_month=5,'MAY',if(p.due_month=6,'JUN',if(p.due_month=7,'JULY',if(p.due_month=8,'AUG',if(p.due_month=9,'SEP',if(p.due_month=10,'OCT',if(p.due_month=11,'NOV',if(p.due_month=12,'DEC','')))))))))))),'-',p.due_year) as due_month
			FROM payment p
				left join scheme_account sa on(sa.id_scheme_account=p.id_scheme_account)
				left join scheme s on(s.id_scheme=sa.id_scheme)
				left join customer c on(c.id_customer=sa.id_customer)
				LEFT Join payment_status_message psm ON (p.payment_status=psm.id_status_msg)
					join chit_settings cs
			WHERE sa.id_scheme_account = '$id_scheme_account' and (p.payment_status=1 or p.payment_status=2 or p.payment_status=8) and (s.scheme_type=1 or s.scheme_type=2 or s.scheme_type=3) order by p.due_year,p.due_month";
				$payments=$this->db->query($sql);
				//print_r($this->db->last_query());exit;
				return $payments->result_array();
	}
	//to get closed account by customer
	 function get_closed_account()
	{
        $showGCodeInAcNo = $this->config->item('showGCodeInAcNo'); 
		$accounts=$this->db->query("select
		 s.id_scheme_account,CONCAT(if(".$showGCodeInAcNo."=1,if(has_lucky_draw = 1,s.group_code,sc.code),'') ,' ',ifnull(s.scheme_acc_number,'Not Allocated')) as scheme_acc_number,concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')) as name,s.ref_no,s.account_name,Date_Format(s.start_date,'%d-%m-%Y') as start_date,Date_Format(s.closing_date,'%d-%m-%Y') as closing_date,
         IF(sc.scheme_type=0 OR sc.scheme_type=2,sc.amount,IF(sc.scheme_type=1 ,sc.max_weight,if(sc.scheme_type=3,if(sc.firstPayamt_as_payamt=1,s.firstPayment_amt ,sc.min_amount),0))) as amount,s.id_branch,
         if(sc.scheme_type=0 || (sc.scheme_type=3 && (sc.flexible_sch_type=1 || sc.flexible_sch_type=2 || sc.flexible_sch_type=0)),CONCAT(cs.currency_symbol,' ',s.closing_balance),CONCAT(s.closing_balance,' ',' Gm')) as closing_balance,
		 sc.scheme_name,sc.code,IF(sc.scheme_type=0,'Amount',IF(sc.scheme_type=1,'Weight',if(sc.scheme_type=2,'Amount to Weight','Flexible amount'))) AS scheme_type,sc.total_installments,sc.max_chance,c.mobile,sc.id_metal,m.metal,cs.is_multi_commodity
		from
		 scheme_account s
		left join customer c on (s.id_customer=c.id_customer)
		left join scheme sc on (sc.id_scheme=s.id_scheme)
		Left Join branch b On (s.id_branch=b.id_branch)
		left join metal m on(m.id_metal=sc.id_metal)
        JOIN chit_settings cs
		where s.active=0 and s.is_closed=1 and s.id_customer=".$this->session->userdata('cus_id'));
		//print_r($this->db->last_query());exit;
		if($accounts->num_rows > 0){
		return $accounts->result_array();
		}
		else{
		return false;
		}
	}
	function isAccExist_bymobile($data)
	{
		if($this->session->userdata('branch_settings')==1)
			{
				$id_branch  = $this->input->post('id_branch');
			}
			else
			{
				$id_branch =NULL;
			}
		$resultset = $this->db->query("SELECT is_acc_registered, REPLACE(mobile_no,'-','') as mobile_no from chit_customer where is_acc_registered=0 and REPLACE(mobile_no,'-','') ='".$data['scheme_mob_number']."' ".($id_branch!=NULL?' and BRANCH ='.$id_branch:'')." ");
		if($resultset->num_rows() > 0 ){
			 if($resultset->row()->mobile_no != NULL && $resultset->row()->is_acc_registered==0){
				 if(strlen($resultset->row()->mobile_no) == 10){
					return array('status'=>TRUE , 'mobile'=>$resultset->row()->mobile_no,'msg'=>'We will send OTP to mobile number associated with this account');
				 }else{
					 return array('status'=> FALSE,'msg'=>'Please visit our branch to update valid mobile number');
				 }
			 }
			 else if($resultset->row()->is_acc_registered == 1){
			 	return array('status'=> FALSE,'msg'=>'Account already registered');	
			 }
			 else{
			 	return array('status'=> FALSE,'msg'=>'Update mobile number in our branch');	
			 }
		}else{
			 	return array('status'=> FALSE,'msg'=>'No schemes are available for this mobile number');	
		}
	}
	function isAccExist($data) 
	{
	   if($data['id_branch'] != '' || $data['id_branch'] != NULL){
	        $resultset = $this->db->query("select * from customer_reg where id_branch=".$data['id_branch']." and group_code='".$data['group_code']."' and scheme_ac_no='".$data['scheme_acc_number']."'");
	   }else{
	         $resultset = $this->db->query("select * from customer_reg where id_branch is null and group_code='".$data['group_code']."' and scheme_ac_no='".$data['scheme_acc_number']."'");      
	   }
	   //print_r($this->db->last_query());exit;
		if($resultset->num_rows() > 0 ){
			 if($resultset->row()->mobile != NULL && $resultset->row()->is_registered_online ==0){
				 if(strlen($resultset->row()->mobile) == 10){
				 return array('status'=>TRUE , 'mobile'=>$resultset->row()->mobile,'email'=>$resultset->row()->email,'name'=>$resultset->row()->ac_name,'msg'=>'We will send OTP to mobile number associated with this account');				 
				 }else{
					 return array('status'=> FALSE,'msg'=>'Please visit our branch to update valid mobile number');
				 }
			 }
			 else if($resultset->row()->is_registered_online == 1){
			 	return array('status'=> FALSE,'msg'=>'Account already registered');	
			 }
			 else{
			 	return array('status'=> FALSE,'msg'=>'Update mobile number in our branch');	
			 }
		}else{
		 	return array('status'=> FALSE,'msg'=>'Enter valid details');	
		}
	}
	function insertExisAccData($data) 
	{
    	if($data['id_branch'] != '' || $data['id_branch'] != NULL){
	       $resultset = $this->db->query("select * from customer_reg where record_to=2 and is_registered_online=0 and is_closed=0 and id_branch='".$data['id_branch']."' and group_code='".$data['group_code']."' and scheme_ac_no='".$data['scheme_acc_number']."'");
	   }else{
	         $resultset = $this->db->query("select * from customer_reg where record_to=2 and is_registered_online=0 and is_closed=0 and id_branch is null and group_code='".$data['group_code']."' and scheme_ac_no='".$data['scheme_acc_number']."'");
	   }
		if($resultset->num_rows() == 1 ){
			$records = array();
		foreach($resultset->result() as $row)
			{
			    $data['sync_scheme_code'] = $row->sync_scheme_code;
			    $data['client_id'] = $row->clientid;
				$records = array( 	'id_customer' 		=> $data['id_customer'],
									'id_scheme'			=> $this->getschId($data),
									'scheme_acc_number' => $row->scheme_ac_no,
									'ref_no'            => $row->clientid,
									'account_name' 		=> $row->ac_name,
									'group_code' 		=> $row->group_code,
									'start_date' 		=> $row->reg_date,
									'is_new' 			=> $row->new_customer,
									'date_add' 			=> date("Y-m-d H:i:s"),
									'is_registered' 	=> 1,
									'active' 			=> 1,
								//	'is_opening' 		=> $row->is_opening,
								//	'balance_amount' 	=> $row->balance_amount,
								//	'balance_weight'	=> $row->balance_weight,
								//	'last_paid_weight' 	=> $row->last_paid_weight,
								//	'last_paid_chances'	=> $row->last_paid_chances,
								//	'last_paid_date' 	=> $row->last_paid_date,
								//	'paid_installments' => $row->paid_installments,
									'id_branch '        => $data['id_branch'],
									'added_by' 			=> 1
								);
				$addData = $this->get_cityData($row->city);
				$address = array( 'id_customer' =>$data['id_customer'],
    							  'address1' =>$row->address1,
    							  'address2' =>$row->address2,
    							  'address3' =>$row->address3,
    							  'id_city'	 =>$addData['id_city'],
    							  'id_state' =>$addData['id_state']
    							);
				$cusName = array('firstname' =>$row->firstname,  
            					  'lastname' =>$row->lastname,
            					  'is_cus_synced' => 1,
            					  'date_upd' =>date("Y-m-d H:i:s")
            					);
			}
			$insAccount     = TRUE;
			$cus_sql = $this->db->query("select is_cus_synced from customer where id_customer=".$data['id_customer']);
			$isCusSynced   = ($cus_sql->num_rows() > 0  ? $cus_sql->row('is_cus_synced') : FALSE);
			if(!$isCusSynced){
			    // Online customer names should be updated with Offline data, if customer data already not synced.
    			$sql = $this->db->query("select * from address where id_customer=".$data['id_customer']);
    			if($sql->num_rows() > 0){
    			    // Update Address
    				$this->db->where('id_customer',$data['id_customer']);
    				$address['date_upd'] = date("Y-m-d H:i:s");
    				$updateCus = $this->db->update('address',$address);
    			} 
    			else{				
    			    $address['date_add'] = date("Y-m-d H:i:s");
    				$updateCus = $this->db->insert('address',$address);		
    			}
    			// Update Customer
    	        $this->db->where('id_customer',$data['id_customer']); 
    		  	$this->db->update('customer',$cusName);
			} 
			if($insAccount){
				$status = $this->db->insert('scheme_account',$records); 
				return array('status' =>  $status, 'data' => $data,'insertID' => $this->db->insert_id());
			}			
			else{	
				return array('status' =>  $updateCus);
			}
		}else{
			 	return array('status'=> FALSE,'msg'=>'Unable to proceed your request,try again later or contact customer care');	
			 }
		}
		function getschId_old($data) 
		{
			$result = $this->db->query("select id_scheme from scheme where sync_scheme_code='".$data['sync_scheme_code']."'");
			//echo $this->db->last_query();exit;
			if($result->num_rows() > 0 ){
					return $result->row()->id_scheme;
				}
				else{
						return null;
				}
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
		function get_cityData($city) 
		{
			$result = $this->db->query("select id_city,id_state from city where name='".$city."'");	
			return $result->row_array();
		}
		function insert_paymentData($data,$id) 
		{
			$resultset = $this->db->query("select * from transaction where is_transferred='N' and client_id='".$data['client_id']."'");
			$i = 1;
			if($resultset->num_rows() > 0 ){
				$records = array();
				$suceedIds = array();
			foreach($resultset->result() as $row)
				{				
					$records = array( 'id_scheme_account'   =>$id,
                                        'metal_rate'	    =>$row->rate,
                                        'receipt_no'        =>$row->receipt_no,
                                        'metal_weight'      =>$row->weight,
                                        'payment_amount'	=>$row->amount,
                                         'payment_ref_number' =>$row->ref_no,
                                        'actual_trans_amt'	=>$row->amount,
                                        'date_payment'	    =>$row->payment_date,
                                        'date_add'	        =>$row->payment_date,
                                        'id_branch'	        =>$row->id_branch,
                                        'custom_entry_date'	=>$row->custom_entry_date,
                                        'payment_status'	=>1, 
                                        'payment_mode'      =>$row->payment_mode,
                                        'payment_status'    =>$row->payment_status,
                                        //	'dues' =>$row->NO_OF_INSTAL,
                                        'payment_type'      =>'Offline',
                                        'is_offline'	    => 1,
                                        'due_type'          =>$row->due_type,
                                        'added_by'          =>0, //admin
                                        'is_offline'        =>1,
                                        'installment'       => $row->installment_no,
                                        'discountAmt'       =>$row->discountAmt	
                                    );
					$status = $this->db->insert('payment',$records);
				//	echo $this->db->last_query();exit;
					if($status){
					    $suceedIds[] = $row->id_transaction;
					}
					$i++;
				}
				return array('status' =>  $status,'suceedIds'=>$suceedIds);
			}
			else{
				 	return array('status'=> True,'msg'=>'No payment records found');	
				 }
		}
		function updateInterTableStatus($accdata,$id,$payData)
		{	
		    $transtatus = false;
			$arrdata = array("id_scheme_account"=>$id,"is_registered_online"=>1,"is_modified"=>0,"transfer_date"=>date('Y-m-d H:i:s'),'is_transferred'=>'Y');
			$this->db->where('clientid',$accdata['client_id']); 
			$this->db->where('id_branch',$accdata['id_branch']); 
			$accstatus= $this->db->update('customer_reg',$arrdata);
			if($accstatus){
			    foreach($payData as $data){
    		        $upddata = array("is_modified"=>0,"transfer_date"=>date('Y-m-d H:i:s'),'is_transferred'=>'Y');
        			$this->db->where('id_transaction',$data); 
        			$transtatus= $this->db->update('transaction',$upddata);
			    }
			}
				//echo $this->db->last_query();//exit;
			return $transtatus;
		} 
		function insertExisAccData_bymobileno($data)
		{
			$resultset = $this->db->query("SELECT * FROM chit_customer WHERE mobile_no='".$data['scheme_mob_number']."' and is_acc_registered=0");
			if($resultset->num_rows() > 0 ){
				$response_data = array();
				foreach($resultset->result() as $key =>$row)
				{
					if($this->session->userdata('branch_settings')==1)
					{
						$id_branch  = $row->BRANCH;
					}
					else{
						$id_branch =NULL;
					}
					$records = array();
					$data['scheme_type']  = $row->scheme_type; 
					$data['amount']  = $row->AMOUNT; 
					$data['scheme_acc_number'] = $row->GROUP_CUS_NO;
					$data['group_name'] = $row->GROUP_NAME;
					$records = array(   'id_customer' 		=>$data['id_customer'],
										'id_scheme' 		=>$this->getschId($data),
										'scheme_acc_number' =>$row->GROUP_CUS_NO,
										'account_name' 		=>$row->NAME,
										'start_date' 		=>$row->ENTRYDATE,
										'is_new' 			=>'N',
										'date_add' 			=>date("Y-m-d H:i:s"),
										'is_registered'		=>1,
										'active'			=>1,
										'is_opening' 		=> $row->is_opening,
										'balance_amount' 	=> $row->balance_amount,
										'balance_weight'	=> $row->balance_weight,
										'last_paid_weight' 	=> $row->last_paid_weight,
										'last_paid_chances'	=> $row->last_paid_chances,
										'last_paid_date' 	=> $row->last_paid_date,
										'id_branch' 	    => $id_branch,
										'paid_installments' => $row->paid_installments,
										'added_by' 			=> 1
									);
					$addData=$this->get_cityData($row->CITY_NAME);
					$address = array( 'id_customer' =>$data['id_customer'],
									  'address1' 	=>$row->ADDRESS1,
									  'address2' 	=>$row->ADDRESS2,
									  'address3' 	=>$row->ADDRESS3,
									  'id_city'	 	=>$addData['id_city'],
									  'id_state'	=>$addData['id_state']
									);
					$sql =$this->db->query("SELECT * FROM address WHERE id_customer=".$data['id_customer']);
					if($sql->num_rows() > 0){
						$this->db->where('id_customer',$data['id_customer']);
						$updateCus = $this->db->update('address',$address);
					} 
					else{
						$updateCus = $this->db->insert('address',$address);
					}
					if($updateCus){
						$status = $this->db->insert('scheme_account',$records);
						$data['insertID'] = $this->db->insert_id();
					}
					$response_data[$key] = $data;
				}
				if($updateCus){
					return array('status' =>  $status, 'data' => $response_data);
				}else{
					return array('status' =>  $updateCus);
				}
			}else{
				 return array('status'=> FALSE, 'msg' => 'Unable to proceed your request, try again later or contact customer care');	
			}
		}
		function insert_paymentData_bymobile($data)
		{
			foreach($data as $key => $val){
				$resultset = $this->db->query("SELECT * FROM chit_transaction WHERE group_cus_no='".$val['scheme_acc_number']."' and group_name='".$val['group_name']."'");
				$i = 1;
				if($resultset->num_rows() > 0 ){
					$records = array();
					foreach($resultset->result() as $row)
					{				
						$records = array( 	'id_scheme_account' =>$val['insertID'],
											'metal_rate'		=>$row->GOLD_RATE,
											'receipt_no' 		=>$row->RECEIPT_NO,
											'metal_weight' 		=>$row->WEIGHT,
											'payment_amount'	=>$row->AMOUNT,
											 'payment_ref_number' =>$row->ref_no,
											'date_payment' 		=>$row->RECEIPT_DATE,
											'payment_status' 	=>1,
											'date_add' 			=>$row->RECEIPT_DATE,
											'payment_mode' 		=>($row->is_free_payment == 1?'FP':$row->CASH_TYPE) ,	
											'dues' 				=>$row->NO_OF_INSTAL,
											'payment_type' 		=>'Manual',
											'is_offline'		=> 1,
											'due_type' 			=>$row->due_type,
											'added_by' 			=>0, //admin
											'is_offline' 		=>1,
											'discountAmt' 		=>$row->discountAmt	
											);
						$status = $this->db->insert('payment',$records);
						$i++;
					}
				}
			}
			if($status){
				return array('status' =>  $status);
			}else{
				return array('status'=> TRUE, 'msg'=>'No payment records found');	
			}
		}
		function updateOfflineData_bymobile($data)
		{
			foreach($data as $key => $val){
				$arrdata=array("is_acc_registered"=>1);
				//$this->db->where('branch',$branch_code); 
				$this->db->where('group_name',$val['group_name']); 
				$this->db->where('group_cus_no',$val['scheme_acc_number']); 
				$status= $this->db->update('chit_customer',$arrdata);
			}
			return $status;
		}
		public function check_cusrefcode($cusref_code)
		{
			$this->db->select('id_customer');
			$this->db->where('id_customer',$cusref_code); 
			$status=$this->db->get('customer');
			if($status->num_rows()>0)
			{
				return TRUE;
			}
		}
		public function check_emprefcode($emp_code)
		{
			$this->db->select('id_employee');
			$this->db->where('id_employee',$emp_code); 
			$status=$this->db->get('employee');
			if($status->num_rows()>0)
			{
				return TRUE;
			}
		}
// branch name list 
    function getSchJoinBranches($id_scheme){
    	$sql = $this->db->query("SELECT b.name as name,sb.id_branch as id_branch 
                        			FROM scheme_branch sb        
                        		LEFT JOIN scheme s ON sb.id_scheme = s.id_scheme
                        		LEFT JOIN branch b ON b.id_branch = sb.id_branch
                        		WHERE sb.id_scheme=".$id_scheme);
                        	//	print_r($this->db->last_query());exit;
        $branch = $sql->result_array();
        return $branch;
    }
	function get_branch()
	{
	    $data=$this->get_settings();
	    $sql = "SELECT * FROM branch b where b.show_to_all = 1";
	    $branch = $this->db->query($sql)->result_array();		
		return $branch;
	} 
	function get_metal()
	{
	    $data=$this->get_settings();;
	    $sql = "SELECT m.id_metal,m.metal FROM metal m ";
	    //print_r($sql);exit;
	    $metal = $this->db->query($sql)->result_array();		
		return $metal;
	} 
// branch name list 		
// scheme account number generate	
	function accno_generatorset() 
	{
		$resultset = $this->db->query("SELECT c.schemeacc_no_set FROM chit_settings c");	
	     if($resultset->row()->schemeacc_no_set == 0){
		return array('status'=>TRUE , 'schemeacc_no_set'=>$resultset->row()->schemeacc_no_set);
	    }else{
		 return array('status'=> FALSE, 'schemeacc_no_set'=>$resultset->row()->schemeacc_no_set);
	   }
	}
	// scheme account number generate
       /*function checkreferral_code($mbi){
		    $this->db->select('mobile');
			$this->db->where('mobile',$mbi); 
			$status=$this->db->get('customer');
			if($status->num_rows()>0)
			{
				return array("status" => TRUE,'user'=>'CUS');
			}else{
				$this->db->select('mobile');
				$this->db->where('mobile',$mbi); 
				$status=$this->db->get('employee');
				if($status->num_rows()>0)
				{
					return array("status" => TRUE,'user'=>'EMP');
				}
			}
			return FALSE;
	   }*/
	function checkreferral_code($mbi,$cus_id)
	{
		  $isEnteredCodevalid =$this->veriflyreferral_code($mbi);
			if($isEnteredCodevalid['user'] == 'CUS')
			{
	   		$referal_cod = $this->db->query("select c.id_customer,c.cus_ref_code as referal_code,cs.cusbenefitscrt_type,cs.empbenefitscrt_type  from customer c join chit_settings cs where c.id_customer='".$cus_id."'");
			}
			else
			{
			$referal_cod = $this->db->query("select c.id_customer,c.emp_ref_code as referal_code,cs.cusbenefitscrt_type,cs.empbenefitscrt_type  from customer c join chit_settings cs where c.id_customer='".$cus_id."'");	
			}
	   		$referal_code=$referal_cod->row()->referal_code;
	   		$empSingle=$referal_cod->row()->empbenefitscrt_type;
	   		$cusSingle=$referal_cod->row()->cusbenefitscrt_type;
		if($referal_code == null || $referal_code == "")
		{	
			// $isEnteredCodevalid = check whether entered referral code is valid using verify function
		$isEnteredCodevalid = $this->veriflyreferral_code($mbi);
			if($isEnteredCodevalid['status'] == true)
			{
			$result = array('status' => true, 'msg' => 'Valid referal Code','is_referral_by' => $isEnteredCodevalid['user'] );
			}
			else
			{
			$result = array('status' => false, 'msg' => 'Invalid referal Code' );
			}
		}
		else
		{
			$checkCusRefCodeType = $this->veriflyreferral_code($referal_code);
		$isEnteredCodevalid = $this->veriflyreferral_code($mbi);
		if($isEnteredCodevalid['status']==0)
		{
		$result = array('status' => false, 'msg' => 'Invalid referal Code' );
		}else
		{
			if($checkCusRefCodeType['user'] == 'CUS')
		{
			$isEnteredCodevalid =$this->veriflyreferral_code($mbi);
			if($isEnteredCodevalid['user']=='CUS')
			{
			if($cusSingle == 0){
			$result = array('status' => false, 'msg' => 'Referral code already used ' );
			}else{
			$isEnteredCodevalid =$this->veriflyreferral_code($mbi);
			if($isEnteredCodevalid['status'] == true){
				$result = array('status' => true, 'msg' => 'Valid referal Code' ,'is_referral_by' => $isEnteredCodevalid['user']);
			}else{
				$result = array('status' => false, 'msg' => 'Invalid referal Code' );
			}
		}
			}
			else if($isEnteredCodevalid['user']=='EMP')
			{
			if($empSingle == 0){
			$result = array('status' => false, 'msg' => 'Referral code already used ' );
		}else{
			$isEnteredCodevalid =$this->veriflyreferral_code($mbi);
			if($isEnteredCodevalid['status'] == true){
				$result = array('status' => true, 'msg' => 'Valid referal Code' , 'is_referral_by' => $isEnteredCodevalid['user']);
			}else{
				$result = array('status' => false, 'msg' => 'Invalid referal Code' );
			}
		}
			}
	}else if($checkCusRefCodeType['user'] == 'EMP')
	{
		$isEnteredCodevalid =$this->veriflyreferral_code($mbi);
			if($isEnteredCodevalid['user']=='CUS')
			{
				if($cusSingle == 0){
			$result = array('status' => false, 'msg' => 'Referral code already used ' );
		}else{
			$isEnteredCodevalid =$this->veriflyreferral_code($mbi);
			if($isEnteredCodevalid['status'] == true){
				$result = array('status' => true, 'msg' => 'Valid referal Code','is_referral_by' => $isEnteredCodevalid['user'] );
			}else{
				$result = array('status' => false, 'msg' => 'Invalid referal Code' );
			}
		}
			}
			else
			{
				if($empSingle == 0){
			$result = array('status' => false, 'msg' => 'Referral code already used ' );
		}else{
			if($isEnteredCodevalid['status'] == true){
				$result = array('status' => true, 'msg' => 'Valid referal Code','is_referral_by' => $isEnteredCodevalid['user'] );
			}else{
				$result = array('status' => false, 'msg' => 'Invalid referal Code' );
			}
		}
			}
	}
		}
		}
		return $result;    
	}
	 public function get_settings()
	 {
	     $sql="select * from chit_settings";
	     $result=$this->db->query($sql);
	     return $result->row_array();
	 }
	public function veriflyreferral_code($mbi) 
			{
				$referral_code= strlen((string)$mbi);
					$data=$this->get_settings();
				if($referral_code>6)
				{
					$this->db->select('mobile');
						$this->db->where('mobile',$mbi); 
						$status=$this->db->get('customer');
						if($status->num_rows()>0)
                			{
                				$this->db->select('mobile');
                			    $this->db->where('mobile',$mbi); 
                			    $status=$this->db->get('employee');
                				if($status->num_rows()>0 && $data['emp_ref_by']==1)
                				{				
                				  return array("status" => TRUE,'user'=>'EMP');
                				}
                				else
                				{
                				    return array("status" => TRUE,'user'=>'CUS');
                				}
                			}
                			else
                			{
                                $this->db->select('mobile');
                                $this->db->where('mobile',$mbi); 
                                $status=$this->db->get('employee');
                                if($status->num_rows()>0 && $data['emp_ref_by']==1){
                                return array("status" => TRUE,'user'=>'EMP');
                                }
                                return array("status" => FALSE);
                            }
				}
				else
				{
						$this->db->select('emp_code');
						$this->db->where('emp_code',$mbi); 
						$status=$this->db->get('employee');
						if($status->num_rows()>0)
						{		
						return array("status" => TRUE,'user'=>'EMP');
						}
					   return array("status" => FALSE);
				}
				 return array("status" => FALSE);
			}
	/*function available_refcode($data){  
	 $query=$this->db->query("SELECT c.referal_code 
				FROM customer c 
				where c.id_customer=".$data['id_customer']." and c.referal_code='".$data['referal_code']."'");
	   if($query->num_rows()>0){
		   return TRUE;		   
	   }else{
		   $this->db->where('id_customer',$data['id_customer']); 		  
		   $updaterefcode =  $this->db->update('customer',array('referal_code'=>$data['referal_code']));
		  return TRUE;	
	   }
	} */
	function available_refcode($data)
	{
	if($data['is_refferal_by']==0 && $data['cus_single']==0)
	{
		$query=$this->db->query("SELECT c.cus_ref_code 
				FROM customer c 
				where c.id_customer=".$data['id_customer']." and c.cus_ref_code='".$data['referal_code']."'");
			if($query->num_rows()>0)
	 	 	 {
		  	 return TRUE;		   
	  		 }
	  		 else
	  		 {
	  		 	 $this->db->where('id_customer',$data['id_customer']); 		  
				 $updaterefcode =  $this->db->update('customer',array('cus_ref_code'=>$data['referal_code']));
		 			 return TRUE;	
	  		 }
	}
	else if($data['is_refferal_by']==1 && $data['emp_single']==0)
	{
		$query=$this->db->query("SELECT c.emp_ref_code 
				FROM customer c 
				where c.id_customer=".$data['id_customer']." and c.emp_ref_code='".$data['referal_code']."'");
			if($query->num_rows()>0)
	 	 	 {
		  	 return TRUE;		   
	  		 }
	  		 else
	  		 {
	  		 	 $this->db->where('id_customer',$data['id_customer']); 		  
				 $updaterefcode =  $this->db->update('customer',array('emp_ref_code'=>$data['referal_code']));
		 			 return TRUE;	
	  		 }
	}
	else if( $data['is_refferal_by']==0||$data['is_refferal_by']==1 && ($data['cus_single']==1&&$data['emp_single']==1))
	{
		 $this->db->where('id_customer',$data['id_customer']); 		  
				 $updaterefcode =  $this->db->update('customer',array('cus_ref_code'=>$data['cus_ref_code'],'emp_ref_code'=>$data['cus_ref_code']));
		 			 return TRUE;	
	}
	}
	function chitsettings_acc(){		
		$sql="Select empbenefitscrt_type,cusbenefitscrt_type
						  From chit_settings
						  Where id_chit_settings";
	   	 return $this->db->query($sql)->row_array();
	}
	function get_groups($id_scheme,$id_branch)
    {	
        $sql="SELECT s.id_scheme_group, s.id_scheme, s.group_code,sch.code as scheme_code, DATE_FORMAT(s.start_date,'%d-%m-%Y') as start_date, DATE_FORMAT(s.end_date,'%d-%m-%Y') as end_date FROM scheme_group s left join scheme sch on (sch.id_scheme=s.id_scheme) where s.id_scheme=".$id_scheme;	
        if($id_branch > 0){
            $sql = $sql.' and id_branch='.$id_branch;
        } 
        return $this->db->query($sql)->result_array();	
    } 	
	 // complaints-ticket count in dashboard//HH
	function get_complints()  
    {	
		$sql="SELECT count(id_enquiry) as cust_Complaints FROM cust_enquiry WHERE id_customer='".$this->session->userdata('cus_id')."'";	
	    return $this->db->query($sql)->row()->cust_Complaints;	
     } 			
	 // DTH & Coin Enq count in dashboard//HH
	function get_dth()  
    {	
	    $sql="SELECT count(id_enquiry) as cus_dth FROM cust_enquiry WHERE  (type=5 or type=6) and id_customer=".$this->session->userdata('cus_id')."";	
		//print_r($this->db->query($sql)->row()->cus_dth);exit;
	    return $this->db->query($sql)->row()->cus_dth;	
     } 	
//GG kyc
function kyc_insert($kyc_detail)
    {
		$status = $this->db->insert("kyc",$kyc_detail);
		return	array('status'=>$status,'insertID'=>($status == TRUE ? $this->db->insert_id():''));
	}
	function kyc_update($kyc_detail,$id_cus,$type)
    {
		$this->db->where('id_customer',$id_cus ); 	
		$this->db->where('kyc_type',$type); 	
		$status = $this->db->update("kyc",$kyc_detail);
	 	return	array('status'=>$status);
	}
	function get_kyc_details(){
		$data=$this->get_settings();
		$cus_id = $this->session->userdata('cus_id');
		$data['bank'] = [];
		$data['pan'] = [];
		$data['aadhar'] = [];
		$data['dl'] = [];
		$b_stat="";
		$p_stat="";
		$a_stat=""; 
		$d_stat="";
		$kyc_bank ="select * from kyc where id_agent=".$cus_id;
		$result = $this->db->query($kyc_bank);
		$kyc = $result->result_array();
		if($result->num_rows() > 0){
			foreach($kyc as $r){
				if($r['kyc_type'] == 1){
					$data['bank'] = array(
										"name" 			=> $r['name'],
										"number" 		=> $r['number'],
										"bank_branch"	=> $r['bank_branch'],
										"bank_ifsc"		=> $r['bank_ifsc'],
										"status"		=> $r['status'],
									);
					$b_stat = $r['status']; 
				}
				elseif($r['kyc_type'] == 2){
					$data['pan'] = array(
										"number" 		=> $r['number'],
										"name" 			=> $r['name'],
										"status" 		=> $r['status'],
									);
					$p_stat = $r['status'];
				}
				elseif($r['kyc_type'] == 3){
					$data['aadhar'] = array(
										"name" 			=> $r['name'],
										"number"    	=> $r['number'],
										"dob" 	    	=> $r['dob'],
										"status" 	    => $r['status'],
											);
					$a_stat = $r['status'];
				}
				elseif($r['kyc_type'] == 4){
					$data['dl'] = array(
										"number"    	=> $r['number'],
										"dob" 	    	=> $r['dob'],
										"status" 	    => $r['status'],
											);
					$d_stat = $r['status'];
				}
			} 
			$data['bank']['type'] = ( $b_stat=='' ?'add' : 'edit' );
			$data['pan']['type'] = ( $p_stat=='' ?'add' : 'edit' );
			$data['aadhar']['type']= ( $a_stat=='' ?'add' : 'edit' );
			$data['dl']['type']= ( $d_stat=='' ?'add' : 'edit' );
			$data['bank']['status']=(($b_stat==0 )? 'Pending' : 
									(($b_stat==1 )?'In Progress':
									(($b_stat==2 )?'Verified': 'Rejected')));
			$data['pan']['status']=(( $p_stat==0 )? 'Pending' : 
									(( $p_stat==1 )?'In Progress':
									(( $p_stat==2  )?'Verified': 'Rejected')));
			$data['aadhar']['status']=(( $a_stat==0)? 'Pending' : 
									(( $a_stat==1 )?'In Progress':
									(( $a_stat==2 )?'Verified': 'Rejected')));	
            $data['dl']['status']=(( $d_stat==0)? 'Pending' : 
									(( $d_stat==1 )?'In Progress':
									(( $d_stat==2 )?'Verified': 'Rejected')));										
		}	
		else{
			$data['bank'] = array(
								"bank_name" 	=> NULL,
								"number" 		=> NULL,
								"bank_branch"	=> NULL,
								"bank_ifsc"		=> NULL,
								"status"		=> 'Pending',
								"type"			=> "add"
								);
			$data['pan'] = array(
								"number" 		=> NULL,
								"name" 			=> NULL,
								"status"		=> 'Pending',
								"type"			=> "add"
							);
			$data['aadhar'] = array(
								"number" 		=> NULL,
								"name" 			=> NULL,
								"dob" 	    	=> NULL,
								"status"		=> 'Pending',
								"type"			=> "add"
							);
			$data['dl'] = array(
								"number" 		=> NULL,
								"dob" 			=> NULL,
								"status"		=> 'Pending',
								"type"			=> "add"
							);
		}		
		//print_r($data);exit;
		return $data;
	}	 
//GG kyc	  
 // Store Locatore based branch master in admin//HH
	function storeLocatorBranches()
	{
	   $data=$this->get_settings();
	   $sql = "SELECT branch.logo,state.name as state,city.name as city,branch.short_name,branch.pincode,branch.map_url,branch.name,branch.address1,branch.address2,branch.phone,branch.mobile FROM `branch` 
		LEFT JOIN state state ON state.id_state=branch.id_state 
		LEFT JOIN city city ON city.id_city=branch.id_city where active=1 order by sort asc";
	    $branch = $this->db->query($sql)->result_array();		
		return $branch;
	}  
		function get_storeLocatorBranches($id_branch)
	{
	   $data=$this->get_settings();
	   $sql = "SELECT branch.id_branch,branch.logo,state.name as state,city.name as city,branch.short_name,branch.map_url,branch.pincode,branch.name,branch.address1,branch.address2,branch.phone,branch.mobile FROM `branch` 
		LEFT JOIN state state ON state.id_state=branch.id_state 
		LEFT JOIN city city ON city.id_city=branch.id_city where active=1 order by sort asc";
		//print_r($sql);exit;
	    $branch = $this->db->query($sql)->result_array();		
		return $branch;
	}  
   // Store Locatore based branch master in admin//  
   //customer name by default in account name while joining scheme//HH
    	/*function get_cusname($id)
	{
		$records = array();
		$sql = $this->db->query("SELECT if(lastname is null,firstname,concat(firstname,' ',lastname)) as name FROM  customer WHERE  id_customer=".$id);
		return $sql->row_array();
	}*/
	function get_cusname()  
    {	
		$sql = $this->db->query("SELECT if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as cus_name FROM  customer c
		where c.id_customer=".$this->session->userdata('cus_id').""); 	
	    return $sql->row_array();
     } 	
	function get_plan_detail($id_sch_ac)  
	{	
		$sql = $this->db->query("SELECT 
					if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as cus_name,
					c.email, c.mobile,
					s.sync_scheme_code,s.total_installments,
					IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 and s.payment_chances=1) , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)as paid_installments,
					g.param_1,g.param_3,g.api_url,g.id_pg,
					IFNULL(ad.id_auto_debit_subscription,0) as id_auto_debit_subscription
					FROM  customer c
						LEFT JOIN scheme_account sa on sa.id_customer = c.id_customer
						LEFT JOIN auto_debit_subscription ad on ad.id_scheme_account = sa.id_scheme_account and ad.status=1
						LEFT JOIN scheme s on s.id_scheme = sa.id_scheme
						LEFT JOIN payment p on p.id_scheme_account = sa.id_scheme_account and payment_status <=2
						LEFT JOIN gateway g on g.id_branch = sa.id_branch and g.is_default=1 and g.pg_code=4
					where sa.id_scheme_account=".$id_sch_ac); 	
		return $sql->row_array();
	} 
	function get_subscriptionData($id_sch_ac)  
	{	
		$sql = $this->db->query("SELECT g.param_1,g.param_3,g.api_url,g.id_pg,sub_reference_id,id_auto_debit_subscription
								FROM auto_debit_subscription ad
									LEFT JOIN scheme_account sa on sa.id_scheme_account = ad.id_scheme_account
									LEFT JOIN gateway g on g.id_branch = sa.id_branch and g.is_default=1 and g.pg_code=4
								WHERE ad.status=1 and ad.id_scheme_account=".$id_sch_ac); 	
		return $sql->row_array();
	} 	
	function get_subsDetail($field,$id)  
	{	
		$sql = $this->db->query("SELECT 
									sub_reference_id, id_auto_debit_subscription, sa.id_scheme_account, auth_status,
									sa.id_branch, sa.scheme_acc_number,sa.firstPayment_amt,
									s.id_metal, s.scheme_type, s.flexible_sch_type, s.id_scheme, s.max_members, s.sync_scheme_code, s.code, s.firstPayamt_maxpayable, s.firstPayamt_as_payamt,
									cs.metal_wgt_decimal, cs.metal_wgt_roundoff, cs.allow_referral, cs.receipt_no_set, cs.scheme_wise_receipt, cs.schemeacc_no_set, s.is_lucky_draw, cs.gent_clientid,
									g.param_1,g.param_3,g.api_url,g.id_pg, 
									c.email, c.firstname, c.lastname
								FROM auto_debit_subscription ad
									LEFT JOIN scheme_account sa on sa.id_scheme_account = ad.id_scheme_account
									LEFT JOIN customer c on c.id_customer = sa.id_customer
									LEFT JOIN scheme s on s.id_scheme = sa.id_scheme
									LEFT JOIN gateway g on g.id_branch = sa.id_branch and g.is_default=1 and g.pg_code=4
								JOIN chit_settings cs 
								WHERE ad.status=1 and  ".$field."=".$id); 	
							//	echo $this->db->last_query();exit;
		return $sql->row_array();
	}  	
	function isPaymentAlreadyExist($payment_ref_number)  
	{	
		$sql = $this->db->query("SELECT payment_ref_number FROM payment WHERE payment_ref_number='".$payment_ref_number."'"); 	
		if($sql->num_rows() > 0){
		    return true;
		}else{
		    return false;
		}
	}  	
	function insertData($data,$table){
	    $status = $this->db->insert($table,$data);
	    return	array('status'=>$status,'insertID'=>($status == TRUE ? $this->db->insert_id():''));
	}
	public function updateData($data, $id_field, $id_value, $table)
    {    
	    $edit_flag = 0;
	    $this->db->where($id_field, $id_value);
		$edit_flag = $this->db->update($table,$data);
		return ($edit_flag==1?$id_value:0);
	}
	function getSchCommodity()
	{
		$result = array();
		$sql = "SELECT m.id_metal,metal,0 as cus_sch_ac FROM scheme s
				LEFT JOIN metal m on m.id_metal=s.id_metal
			WHERE s.active=1  
			GROUP BY id_metal";
		return $this->db->query($sql)->result_array();
	}
	function getSchBranches()
	{ 
		$result = array();
		$sql = "SELECT sb.id_branch,b.name,0 as cus_sch_ac 
			    FROM scheme_branch sb 
				    left join branch b on sb.id_branch=b.id_branch
				WHERE scheme_active=1  
				GROUP BY id_branch";
		return $this->db->query($sql)->result_array();
	}
	function getSubscriptionStatus($subscription_id)
	{
		$sql = $this->db->query("SELECT auth_status,subscription_id
				FROM auto_debit_subscription
				WHERE subscription_id = '".$subscription_id."'"); 
		return $sql->row()->auth_status;
	}
	function get_ratesByJoin($start_date)
	{
	    $today = date('Y-m-d H:i:s');
	    $sql = $this->db->query("SELECT id_metalrates,goldrate_22ct,DATE(add_date) as add_date FROM `metal_rates` WHERE date(add_date) BETWEEN '".$start_date."' AND '".$today."' ORDER BY goldrate_22ct ASC LIMIT 1");
	    	if($sql->num_rows() > 0)
			{
				foreach($sql->result() as $row)
				{
					$rate[] = array('id_metalrates' => $row->id_metalrates,'goldrate_22ct' => $row->goldrate_22ct,'add_date' => $row->add_date);
				}
			}
		return $rate;
	}
	function checkCusKYC($id_cus){
		$sql = $this->db->query("SELECT kyc_type from kyc where status=2 and id_agent = ".$id_cus);
		if($sql->num_rows()> 0){
		    $res = $sql->result_array();
		    $bank = FALSE;
		    $anyoneKYC = FALSE;
		    foreach($res as $r){
		        if($r['kyc_type'] == 1){
		            $bank = TRUE;
		        }else if($r['kyc_type'] == 2 || $r['kyc_type'] == 3 || $r['kyc_type'] == 4){
		            $anyoneKYC = TRUE;   
		        }
		       // echo $bank;
		    }
	        if($bank == TRUE && $anyoneKYC == TRUE){
	            $this->db->where('id_agent',$id_cus); 
    			$data = array("kyc_status" => 1);
    			$status = $this->db->update("customer",$data);	
	        }else{
	            $status = FALSE;
	        }
			if($status){
				return 1; 
			}else{
				return 0; 				
			}	
		}else{
			return 0;
		}
	}
	
}
?>