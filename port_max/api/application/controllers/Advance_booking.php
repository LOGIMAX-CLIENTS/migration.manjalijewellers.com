<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');

require(APPPATH.'libraries/REST_Controller.php');


class advance_booking extends REST_Controller
{
	const ADV_MOD = "advance_booking_model";

	function __construct()
	{
		parent::__construct();
		$this->response->format = 'json';
		$this->load->model(self::ADV_MOD);
		ini_set('date.timezone', 'Asia/Calcutta');
		
		$this->comp = $this->advance_booking_model->company_details();
	}

	function get_values()
    {
		return (array)json_decode(file_get_contents('php://input'));
	}

	public function payment_gateway($id_branch,$id_pg)
	{	
		$model = self::ADV_MOD;
		$data = $this->$model->getBranchGatewayData($id_branch,$id_pg);  
		return $data;
	}
	
	public function booking_close_post(){
	    $model = self::ADV_MOD;
	    $data = $_POST;
        $booking_id = $data['booking_id'];
        
        $upd_closing = array('status' => '4',
                            'closing_date' => date('Y-m-d H:i:s'),
                            'employee_closed' => $data['employee_closed'],
                            'Closing_id_branch' => $data['closing_id_branch'],
                            );
        //print_r($upd_closing);exit;
        $status = $this->$model->updData($upd_closing, 'booking_id', $booking_id, 'ct_advance_account');
        
        if($status){
            $result = array('status'=>true,'msg'=> "Booking Account Closed successfully...");
        }else{
            $result = array('status'=>false,'msg'=> "Unable to proceed...");
        }
        
        $this->response($result,200); 
        
	}

	function plan_submit_post(){

		$model = self::ADV_MOD;
		$data = $_POST;
        //echo "<pre>";print_r($data);exit;
		$plan_array = array('plan_name' => (isset($data['plan_name']) && $data['plan_name'] != '' ? $data['plan_name'] : NULL),
							'plan_code' => (isset($data['plan_code']) && $data['plan_code'] != '' ? $data['plan_code'] : NULL),
							'sync_plan_code' => (isset($data['sync_plan_code']) && $data['sync_plan_code'] != '' ? $data['sync_plan_code'] : NULL),
							'maturity_type' => (isset($data['maturity_type']) && $data['maturity_type'] != '' ? $data['maturity_type'] : 1),
							'maturity_value' => (isset($data['maturity_value']) && $data['maturity_value'] != '' ? $data['maturity_value'] : 0),
							'accessible_branches' => (isset($data['accessible_branches']) && $data['accessible_branches'] != '' ? $data['accessible_branches'] : NULL),
							'id_metal' => (isset($data['id_metal']) && $data['id_metal'] != '' ? $data['id_metal'] : NULL),
							'purity' => (isset($data['purity']) && $data['purity'] != '' ? $data['purity'] : NULL),
							'payable_by' => (isset($data['payable_by']) && $data['payable_by'] != '' ? $data['payable_by'] : 1),
							'minimum_val' => (isset($data['minimum_val']) && $data['minimum_val'] != '' ? $data['minimum_val'] : NULL),
							'maximum_val' => (isset($data['maximum_val']) && $data['maximum_val'] != '' ? $data['maximum_val'] : NULL),
							'denomination' => (isset($data['flx_denomintion']) && $data['flx_denomintion'] != '' ? $data['flx_denomintion'] : NULL),
							'adv_limit_type' => (isset($data['adv_limit_type']) && $data['adv_limit_type'] != '' ? $data['adv_limit_type'] : 0),
							'adv_limit_value_online' => (isset($data['adv_limit_value_online']) && $data['adv_limit_value_online'] != '' ? $data['adv_limit_value_online'] : NULL),
							'plan_description' => (isset($data['plan_description']) && $data['plan_description'] != '' ? $data['plan_description'] : NULL),
							'is_active' => (isset($data['is_active']) && $data['is_active'] != '' ? $data['is_active'] : 1),
							'is_visible' => (isset($data['is_visible']) && $data['is_visible'] != '' ? $data['is_visible'] : 1),
							'total_adv_limit_value' => (isset($data['total_adv_limit_value']) && $data['total_adv_limit_value'] != '' ? $data['total_adv_limit_value'] : NULL),
							'is_adv_limit_available' => (isset($data['is_adv_limit_available']) && $data['is_adv_limit_available'] != '' ? $data['is_adv_limit_available'] : 1),

		);

		if($data['form_type'] == 'update' && $data['id_plan'] > 0){
			$status = $this->$model->updData($plan_array, 'id_plan', $data['id_plan'], 'ct_advance_booking_settings');
		}else{
			$status = $this->$model->insertData($plan_array,'ct_advance_booking_settings');
		}

		$this->response($status,200); 
	}

	function get_records($id_plan = ''){
		
	}

	function allActivePlans_get(){
		$model = self::ADV_MOD;
		$plans = $this->$model->activePlans();
	//	echo '<pre>';print_r($this->db->last_query());exit;
	//	$this->response($plans,200);
	  echo json_encode($plans);
	}

	function planFormData_get(){
		$id_plan = $this->get('id_plan');
		$model = self::ADV_MOD;
		$formData = $this->$model->fetch_planFormData($id_plan);
		//$this->response($formData,200);
		echo json_encode($formData);

	}

	function create_booking_post(){
		$model = self::ADV_MOD;
		$data = $this->get_values();
		$pay_data = [];
		
		
		if(empty($data)){
		    $data = $_POST;
		}
		
		$booking_amount = $data['booking_amount'];
		$booking_weight = $data['booking_weight'];
		$gst_amt = '0.00';
		$gst_wgt = '0.000';
		$advance_amt = '0.00';
		
		/*if($data['source_type'] == 'ADMIN'){
		    echo '<pre>';print_r($data);exit;
		}*/
		
		
		
		if($data['gst_setting'] == 1){
		    $gst_type = $data['gst_type'];
		    $gst = $data['gst'];
		  
            
            if($gst_type == 1){
                //FOR EXCLUSIVE GST TYPE...
                $gst_amt = $booking_amount * ($gst / 100); // 14750 * (3/100)  = 442.5
            }else{
                //FOR INCLUSIVE GST TYPE...
                $gst_amt = $booking_amount - ($booking_amount * (100 / (100 + $gst))); // 14750 - (14750 * (100 / (100 + 3)))  = 429.61
            }
            $gst_wgt = $gst_amt / $data['booking_rate'];
            
            /*$booking_amount = ($gst_type == 0 ? ($booking_amount - $gst_amt) : $booking_amount );
            $booking_weight = ($gst_type == 0 ? ($booking_weight - $gst_wgt) : $booking_weight );*/
            
		}
		
		if($data['is_adv_limit_available'] == 1){
		    if($data['adv_limit_type'] == 1){   // 1 - percentage , 0 -amount
		        $advance_amt = round(($booking_amount + $gst_amt) / $data['total_adv_limit_value']);
		    }else{
		        $advance_amt = $data['total_adv_limit_value'];
		    }
		}

    
		$book_array = array(
		                    'gst_type'      => $data['gst_type'],
                            'gst_amount'    => $gst_amt,
                            'gst_weight'    => $gst_wgt,
                            'gst'           => $gst,
                            'advance_amt'   => $advance_amt, 
                            
		                    'id_plan' => $data['id_plan'],
							'id_customer' => $data['id_customer'],
							'id_branch' => $data['id_branch'],
							'booking_name' => $data['booking_name'],
							'booking_date' => date('Y-m-d H:i:s'),   //Current date_time
							'booking_amount' => $booking_amount,
							'booking_weight' => $booking_weight,
							'booking_rate' => $data['booking_rate'],
							'status' => '1',    //1-open,2-advance done,3-paid,4-closed,5-cancelled
							'added_by' => ($data['source_type'] == 'MOB' ? 2 : ($data['source_type'] == 'WEB' ? 0 : 1 )), //	0 - WebApp , 1 - admin, 2 - MobileApp, 3 - Collection app, 4 - Retail App, 5 - Sync, 6 - import
		);
			//print_r($book_array);exit;

		$status = $this->$model->insertData($book_array,'ct_advance_account');
		
		if($status > 0){
			$pay_data = $this->$model->get_paymentData($data['id_customer'],$data['source_type'],$status,'','','');


			$result = array(	'status'=>TRUE,
								'msg' => 'Metal Booked successfully...',
								'pay_content' => $pay_data,
								);
		}else{
			$result = array(	'status'=>FALSE,
								'msg' => 'Unable to proceed further...',
								'pay_content' => $pay_data	);
		}

		$this->response($result,200);

	}
	
	function paymentData_get(){
	    $model = self::ADV_MOD;
	    $data = $_GET;
	    $result['chits'] = [];
	   
	    $chits = $this->$model->get_paymentData($data['id_customer'],$data['source_type'],$data['booking_id'],$data['id_plan'],$data['type'],$data['fil_status']);
	    
	    if(sizeof($chits) > 0 ){
	        $result['chits'] = $chits;
	    }
	    
	    $cusLastPayData = $this->$model->getCusData($this->get('id_customer'));
	    if(!empty($cusLastPayData['last_bookingPay_on'])){
	        $block_pay_mins = 2;
	        $last_pay_sett = strtotime($cusLastPayData['last_bookingPay_on'])+(60*$block_pay_mins);
	        //$tm = time() - (60*$result['currency']['currency']['block_pay_mins']);
	        if($last_pay_sett < time()) {
                $result['show_timer'] = false;
                $result['block_pay_mins'] = $block_pay_mins;
                $result['last_pay_sett'] = $last_pay_sett;
            }else{
                $result['show_timer'] = true;
                $sec = $last_pay_sett - time();
                $result['remaining_sec'] = $sec;
                $result['remaining_min'] = gmdate("i:s", $sec);
                $result['timer_desc'] = "You Can Retry The Payment after ".$block_pay_mins." mins";
            }
        }else{
          $result['show_timer'] = false; 
        }
    
    
	    echo json_encode($result);
	    //$this->response($pay_data,200);
	   
	}
	//Booking List Gopal code starts
	function old_pre_booking_payment_get(){
	     $data = $_GET;
	    $from_date=$data['from_date'];
        $to_date=$data['to_date'];
        $id_branch=$data['id_branch'];
        $id_customer=$data['id_customer'];
		$model = self::ADV_MOD;
		//$prebookpayment=$this->$model->get_prebookpayment();
		$prebookpayment=$this->$model->get_prebookpayment($from_date,$to_date,$id_branch,$id_customer);
		echo json_encode($prebookpayment);
	}
	 //Booking List Gopal code ends
    	//Booking List Gopal code starts
	function pre_booking_payment_get(){
	     $data = $_GET;
	    $from_date=$data['from_date'];
        $to_date=$data['to_date'];
        $id_branch=$data['id_branch'];
        $id_customer=$data['id_customer'];
        $id_booking=$data['id_booking'];
		$model = self::ADV_MOD;
		//$prebookpayment=$this->$model->get_prebookpayment();
		$prebookpayment=$this->$model->get_prebookpayment($from_date,$to_date,$id_branch,$id_customer,$id_booking);
		echo json_encode($prebookpayment);
	}
	 //Booking List Gopal code ends
    //pre booking acc listing starts
    function pre_booking_accounts_get()
    {
         $data = $_GET;
	    $from_date=$data['from_date'];
        $to_date=$data['to_date'];
        $id_branch=$data['id_branch'];
        $id_customer=$data['id_customer'];
		$model = self::ADV_MOD;
		
		$prebookacc=$this->$model->get_prebookacc($from_date,$to_date,$id_branch,$id_customer);
		echo json_encode($prebookacc);
    }
    //pre booking acc listing ends
	
	function selectMetalPlans_get(){
        $model = self::ADV_MOD;

	    $data = $this->$model->activePlans();
	    
	    foreach($data as $k=> $v){
	        $metals[] = array('id_metal'=>$v['id_metal'],'metal_name'=>$v['metal'],'id_plan'=>$v['id_plan'],'plan_name'=>$v['plan_name']);
	    }
	    
	    echo json_encode($metals);
    }


	function book_byPayment_post(){
		$model = self::ADV_MOD;
		$postData = $this->get_values();
		

		if(empty($postData)){
		    $postData = $_POST;
		}
		
		
		
		
		$source = ($postData['source_type'] == 'WEB' || $postData['source_type'] == 'MOB' ? 'ONLINE' : $postData['source_type']);
		$added_by = ($postData['source_type'] == 'ADMIN' ? '0' : ($postData['source_type'] == 'WEB' ? '1' : ($postData['source_type'] == 'MOB' ? '2' : '')));

        $phone = (!empty($postData['phone']) ? $postData['mobile_number'] :  $postData['phone']);
		
		if($postData['amount'] > 0)
		{
			//get the values posted from mobile in array 
			
			$allow_flag = FALSE;
			$submit_pay = FALSE;
			$cusData = $this->$model->get_customer($postData['phone']);
			$pay_email = $postData['email'];
			$gateway = (isset($postData['gateway']) && $postData['gateway'] > 0 ? $postData['gateway'] : 0);
			$paycred = $this->$model->payment_gateway($gateway);
			
			$i=1;
			$txnid = uniqid(time());     //generate txnid
			$pay_arr =  json_decode(urldecode($postData['pay_arr'])) ;          // decode format for post method....  
						
			$actAmount = (float) $postData['amount'];  // sum of all chit amount
			$udf1= "";
			$udf2= "";
			$udf3= "";
			$productinfo= "";
			$this->db->trans_begin();

			    $pay = $pay_arr;
			    
				
				
				if($source == 'ONLINE'){
				    $metal_rate = (isset($pay->udf3) && $pay->udf3 !='' ? $pay->udf3 : 0.00);
    				$amount = (isset($pay->amount)? $pay->amount : NULL );
    				$metal_wgt = $amount/$metal_rate;
    				$insertData = array(
    								"id_adv_booking"	 => (isset($pay->udf1)? $pay->udf1 : NULL ),
    								"payment_amount" 	 => (isset($pay->amount)? $pay->amount : NULL ), 
    								"payment_type" 	     => (isset($paycred) ? ($paycred['pg_name']) : $source),
    								"date_payment" 		 =>  date('Y-m-d H:i:s'),
    								"metal_rate"         => (isset($pay->udf3) && $pay->udf3 !='' ? $pay->udf3 : 0.00),
    								"metal_weight"       =>  $metal_wgt,
    								"id_transaction"     => (isset($txnid) ? $txnid.'-'.$i : NULL),
    								"ref_trans_id"       => (isset($txnid) ? $txnid : NULL),// to update pay status after trans complete.
    								"remark"             =>  'Paid for '.$pay->udf5.($pay->udf5 >1?'months':'month'),
    								"added_by"			 =>  $added_by,
    								"payment_status"     => ($source == 'ONLINE' ? '7' : '1'),
    								"id_payGateway"      => (isset($gateway) ? $gateway: NULL),
    								'id_branch'          => $postData['id_branch'],
    								"pay_email"          => $pay_email,
    								'transaction_type'   => $pay->udf2,
    				);
    				$udf1 = $udf1." ".$pay->udf1;
    				$udf2 = $udf2." ".$pay->udf2;
    				$udf3 = $udf3." ".$pay->udf3;
    				$productinfo = $productinfo." ".$pay->adv_bookNum;
				}else{
				    $metal_rate = $postData['booking_rate'];
    				$amount = $postData['amount'];
    				$metal_wgt = $amount/$metal_rate;
				   $insertData = array(
    								"id_adv_booking"	 => $postData['id_adv_booking'],
    								"payment_amount" 	 => $postData['amount'], 
    								"payment_type" 	     => 'Manual',
    								"date_payment" 		 =>  date('Y-m-d H:i:s'),
    								"metal_rate"         =>  $postData['booking_rate'] ,
    								"metal_weight"       => $metal_wgt,
    								"remark"             =>  'Manual Payment',
    								"added_by"			 =>  $added_by,
    								"payment_status"     => 1,
    								'id_branch'          => $postData['id_branch'],
    								"pay_email"          => $pay_email,
    								'transaction_type'   => 'AP',
    								"id_employee"       =>$this->session->userdata('uid')    
    				); 
				}
			

//			echo '<pre>';print_r($insertData);exit;
		

				$status = $this->$model->insertData($insertData,'ct_advance_payment');  	
				
		
		//	print_r($this->db->last_query());exit;
			
			    

				if($this->db->trans_status()=== TRUE)
				{
					$this->db->trans_commit();
					$submit_pay = TRUE;
				}else{
					$this->db->trans_rollback();
					$submit_pay = FALSE;
				}
				
				if($submit_pay){ 
				
					
					if($source == 'ONLINE'){
					    
				    	$updData = array("last_bookingPay_on" => date("Y-m-d H:i:s"));
				        $this->$model->updData($updData,'id_customer',$postData['id_customer'],'customer');
				        
    					$secretKey = $paycred['param_1']; //Shared by Cashfree  
    					$appId     = $paycred['param_3'];
    					//set data for hash generation
    					$data['pay'] =	array (
    						'key' 			=> $secretKey, 
    						'txnid' 		=> $txnid, 
    						'amount' 		=> (isset($postData['amount'])   ? $postData['amount']:''),
    						'productinfo'	=> (isset($productinfo)    ? $productinfo :''),
    						'firstname' 	=> (isset($cusData['firstname'])? $cusData['firstname'] :''),
    						'lastname' 		=> (isset($cusData['lastname']) ? $cusData['lastname']:''),
    						'email' 		=> (isset($cusData['email'])    ? $cusData['email']:''), 
    						'phone' 		=> (isset($cusData['mobile'])    ? $cusData['mobile'] :''),
    						'address1'		=> (isset($cusData['address1']) ? $cusData['address1']:''), 
    						'address2'		=> (isset($cusData['address2']) ? $cusData['address2'] :''), 
    						'city'			=> (isset($cusData['city']) ? $cusData['city'] :''), 
    						'state'			=> (isset($cusData['state']) ? $cusData['state'] : ''), 
    						'country'		=> (isset($cusData['country']) ? $cusData['country'] : ''), 
    						'zipcode'		=> (isset($cusData['pincode']) ? $cusData['pincode'] :''), 
    						'udf1' 			=> (isset($udf1)    ? $udf1 :''),
    						'udf2' 			=> (isset($udf2)    ? $udf2 :''),
    						'udf3'			=> (isset($udf3)    ? $udf3 :''),
    						'udf4' 			=> (isset($data['udf4'])    ? $data['udf4'] : ''),
    						'udf5' 			=> (isset($data['udf5'])? $data['udf5']:'') 
    					);
    					$gen_email =  $this->random_strings(8).'@gmail.com'; 
    
    					if($paycred['pg_code'] == 8) { // Easebuzz gateway
    						$amount = $actAmount.".00";
    						$env = $paycred['type'] == 0 ? "test" : 'prod';
    						$key = $paycred['param_1'];
    						$salt = $paycred['param_3'];
    						$productinfo = 'Online Money Transaction';
    						$email = $pay_email;
    						
    						$hash_sequence = trim($key).'|'.trim($txnid).'|'.trim($amount).'|'.trim($productinfo).'|'.trim($cusData['firstname']).'|'.trim($email).'|||||||||||'.trim($salt);
    						$hash_value =  strtolower(hash('sha512',$hash_sequence));
    						$easebuzz = array (
    							'salt'         =>$salt ,  //Shared by Easebuzz gateway
    							'Key'           => $key, //Shared by Easebuzz gateway
    							'txnid'         =>  $txnid,
    							'amount' 	    =>  $amount, 
    							'productinfo'	=> $productinfo,
    							'firstname' 	=> (isset($cusData['firstname'])? (isset($cusData['lastname']) ? $cusData['firstname'].' '.$cusData['lastname']:$cusData['firstname']) :''),
    							'email'         => $email,
    							'phone'         => (isset($cusData['mobile']) ? $cusData['mobile'] :''),
    							'surl'          => $this->config->item('usr_url')."index.php/mobile_api/easebuzzSuccessResponse/",
    							'furl'          => $this->config->item('usr_url')."index.php/mobile_api/easebuzzFailedResponse/",
    							'hash'          => $hash_value
    						); 
    						
    				//	echo '<pre>';	print_r($easebuzz);exit;
    						$response['easebuzzData'] = $this->_payment($easebuzz,'',$key,$salt,$env);
    					//	$response['easebuzzData'] = $this->config->item('usr_url')."index.php/mobile_api/_payment/".$easebuzz."//".&key."/".$salt."/".$env,
    						$response['pay_mode'] = $env=='prod'?'production':'test';
    						$this->response($response,200);
    					}
					}else{
					    $cus_pay_mode = $postData['cus_pay_mode'];
    					$card_pay_details	= json_decode($cus_pay_mode['card_pay'],true);
    					$cheque_details	    = json_decode($cus_pay_mode['chq_pay'],true); 
    					$net_banking_details = json_decode($cus_pay_mode['net_bank_pay'],true); 
    					$adv_adj             = json_decode($cus_pay_mode['adv_adj'],true); 
    					$adv_adj_details     =  $adv_adj[0];
    					$vch_details            = json_decode($cus_pay_mode['vch_pay'],true); 
    					//$advan_amout = json_decode($pay['adv']['advance_muliple_receipt'][0],true);//Commented and replaced by Durga 19.05.2023
    					$advan_amout = json_decode($postData['adv']['advance_muliple_receipt'][0],true);
        				//echo '<pre>'; print_r(sizeof($advan_amout));exit;
        				if( $cus_pay_mode['cash_payment'] > 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) == 0 && sizeof($vch_details) == 0) {
        					$payment_mode = 'CSH';
        				}
        				else if( $cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) > 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) == 0 && sizeof($vch_details) == 0) {
        					foreach($card_pay_details as $card_pay)
        					{
        						$mode = ($card_pay['card_type']==1 ?'CC':'DC');
        						if($payment_mode == ''){
        							$payment_mode = $mode;
        						} else if($payment_mode != $mode){
        							$payment_mode = 'MULTI';
        						}
        					}
        				}
        				else if( $cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) > 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) == 0 && sizeof($vch_details) == 0) {
        					$payment_mode = 'CHQ';
        				}
        				else if( $cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) > 0 && sizeof($advan_amout) == 0 && sizeof($vch_details) == 0) {
        					$payment_mode = 'NB';
        				}
        				else if( $cus_pay_mode['cash_payment'] == 0 && sizeof($vch_details) > 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) == 0) {
        					$payment_mode = 'VCH';
        					
        				}
        		
        				else if(
    					($cus_pay_mode['cash_payment'] > 0 && sizeof($card_pay_details) > 0) || 
    					($cus_pay_mode['cash_payment'] > 0 && sizeof($cheque_details) > 0) || 
        				($cus_pay_mode['cash_payment'] > 0 && sizeof($net_banking_details) > 0) || 
        				($cus_pay_mode['cash_payment'] > 0 && sizeof($vch_details) > 0) || 
    					($cus_pay_mode['cash_payment'] > 0 && sizeof($advan_amout) > 0) || 
    					(sizeof($net_banking_details) > 0 && sizeof($cheque_details) > 0) || 
        				(sizeof($net_banking_details) > 0 && sizeof($card_pay_details) > 0) || 
        				(sizeof($net_banking_details) > 0 && sizeof($vch_details) > 0) ||
    					(sizeof($net_banking_details) > 0 && sizeof($advan_amout) > 0) ||
    					(sizeof($cheque_details) > 0 && sizeof($card_pay_details) > 0) || 
    					(sizeof($cheque_details) > 0 && sizeof($vch_details) > 0) || 
        				(sizeof($cheque_details) > 0 && sizeof($advan_amout) > 0) || 
    					(sizeof($card_pay_details) > 0 && sizeof($advan_amout) > 0) || 
    					(sizeof($card_pay_details) > 0 && sizeof($vch_details) > 0) || 
        				(sizeof($vch_details) > 0 && sizeof($advan_amout) > 0) ||
    					(sizeof($vch_details) > 0 && sizeof($card_pay_details) > 0 )
    					) 
    					
    					{
        					$payment_mode = 'MULTI';
        				}
        				else if($cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) > 0 && sizeof($vch_details) == 0) {
        					$payment_mode = 'ADV_ADJ';
        				} 
    				
    				
    				
    				/*	// CREATE LOG
    					if (!is_dir($this->log_dir.'/manual')) {
    			            mkdir($this->log_dir.'/manual', 0777, true);
    			        }
    			        $log_path = $this->log_dir.'/manual/create_payment_'.date("Y-m-d").'.txt';
    					$lg_data = "\n CP --".date('Y-m-d H:i:s')." -- : ".json_encode($_POST);
    					file_put_contents($log_path,$lg_data,FILE_APPEND | LOCK_EX); */
    					
    					
    					if($cus_pay_mode['cash_payment']>0)
						{
                            
							$arrayCashPay=array(
											'id_adv_payment'        => $status,
                                            'payment_amount'    => $cus_pay_mode['cash_payment'],
											'payment_mode'      => 'CSH',
											'payment_status'    => 1,
											'payment_date'		=> date("Y-m-d H:i:s"),
											'created_time'	    => date("Y-m-d H:i:s"),
											'created_by'	    => $this->session->userdata('uid')
											);
							if(!empty($arrayCashPay)){
								$cashPayInsert = $this->$model->insertData($arrayCashPay,'ct_advbook_mode_detail');
							}
						}
						
					
				
                        if(sizeof($card_pay_details)>0)
						{
							
									
								foreach($card_pay_details as $card_pay)
								{
									$arrayCardPay=array(
														'id_adv_payment'        => $status,
														'card_type'         =>$card_pay['card_name'],
														'payment_amount'    => $card_pay['card_amt'],
														'payment_mode'      => ($card_pay['card_type']==1 ?'CC':'DC'),
														'card_no'		    =>($card_pay['card_no']!='' ? $card_pay['card_no']:NULL),
														'payment_ref_number'=>($card_pay['ref_no']!='' ? $card_pay['ref_no']:NULL),	
														'id_pay_device'     =>($card_pay['id_device']!='' ? $card_pay['id_device']:NULL),
														'payment_status'    => 1,
														'payment_date'		=> date("Y-m-d H:i:s"),
														'created_time'	    => date("Y-m-d H:i:s"),
														'created_by'	    => $this->session->userdata('uid')
													);
														
									if(!empty($arrayCardPay)){
									    $cardPayInsert = $this->$model->insertData($arrayCardPay,'ct_advbook_mode_detail'); 
								    }
								   // print_r($this->db->last_query());exit;
								}
								
								
						}
						
                     
						if(sizeof($cheque_details)>0)
						{
							foreach($cheque_details as $chq_pay)
							{
								$arraychqPay=array(
									'id_adv_payment'    => $status,
									'payment_amount'=>$chq_pay['payment_amount'],
									'payment_status'    => 1,
									'payment_date'	=>date("Y-m-d H:i:s"),
									'cheque_date'	=>date("Y-m-d H:i:s"),
									'payment_mode'	=>'CHQ',
									'bank_IFSC'		=>($chq_pay['bank_IFSC']!='' ? $chq_pay['bank_IFSC']:NULL),
									'cheque_no'		=>($chq_pay['cheque_no']!='' ? $chq_pay['cheque_no']:NULL),
									'bank_name'		=>($chq_pay['bank_name']!='' ? $chq_pay['bank_name']:NULL),
									'bank_branch'	=>($chq_pay['bank_branch']!='' ? $chq_pay['bank_branch']:NULL),
									'bank_IFSC'	=>($chq_pay['bank_IFSC']!='' ? $chq_pay['bank_IFSC']:NULL),
									'created_time'	=> date("Y-m-d H:i:s"),
									'created_by'	=> $this->session->userdata('uid')
								);
								
								if(!empty($arraychqPay))
								{
									$chqPayInsert = $this->$model->insertData($arraychqPay,'ct_advbook_mode_detail'); 
								}
								
							}
								
								
						}
                      
                        if(sizeof($net_banking_details)>0)
						{
						    foreach($net_banking_details as $nb_pay)
							{
								$arrayNBPay=array(
									'id_adv_payment'        => $status,
									'payment_amount'    =>$nb_pay['amount'],
									'payment_status'    => 1,
									'payment_date'		=> date("Y-m-d H:i:s"),
									'payment_mode'	    =>'NB',
									'payment_ref_number'=>($nb_pay['ref_no']!='' ? $nb_pay['ref_no']:NULL),
									'net_banking_date'=>($nb_pay['nb_date']!='' ? $nb_pay['nb_date']:NULL),
									'NB_type'           =>($nb_pay['nb_type']!='' ? $nb_pay['nb_type']:NULL),
									'id_pay_device'     =>($nb_pay['nb_type']==3 ? $nb_pay['id_device']:NULL),
									'id_bank'           =>($nb_pay['nb_type']==1 || $nb_pay['nb_type']==2? $nb_pay['id_bank']:NULL),
									'created_time'	    => date("Y-m-d H:i:s"),
									'created_by'	    => $this->session->userdata('uid')
								);
								if(!empty($arrayNBPay)){
									$NbPayInsert = $this->$model->insertData($arrayNBPay,'ct_advbook_mode_detail'); 
								}
							}
									
								
						}
                        
                       $advan_amout = $postData['adv']['advance_muliple_receipt'][0];
						if(count($advan_amout)>0 and $advan_amout!='')
						{
							$advance_amount_adj = json_decode($advan_amout);
							$advance_amt=0;
							$adv_adj_array=[];
							foreach($advance_amount_adj as $obj)
							{
								$advance_amt+=$obj->adj_amount;
								$data_adv_amount    = array(
															'id_issue_receipt'  => $obj->id_issue_receipt,
															'id_adv_payment'        => $status['insertID'],
															'adjusted_for'      => 2, // Adjusted in CRM
															'utilized_amt'      => $obj->adj_amount
														);
								$insId_adv_amount = $this->$model->insertData($data_adv_amount,'ret_advance_utilized');
								$array_adj_pay=array(
									'id_adv_payment'        => $status,
									'payment_amount'    =>$obj->adj_amount,
									'payment_status'    => 1,
									'payment_date'		=>date("Y-m-d H:i:s"),
									'payment_mode'	    =>'ADV_ADJ',
									'created_time'	    => date("Y-m-d H:i:s"),
									'created_by'	    => $this->session->userdata('uid')
								);
								$this->$model->insertData($array_adj_pay,'ct_advbook_mode_detail'); 
							}
						}
						
    					//update account status
    					
    					$total = $postData['total_paid_amount'] + $postData['amount'];
    					
    					if($total >= $postData['booking_amount']){
                    		$updAcc = array('status' => 3);   //paid
                    	}elseif($total >= $postData['advance_amt'] && $total < $postData['booking_amount']){
                    		$updAcc = array('status' => 2);   //advance paid
                    	}
    					
    					$this->$model->updData($updAcc, 'booking_id', $postData['id_adv_booking'], 'ct_advance_account');
    					
    					
					    //admin success goes here....
					    $result['status'] = true;
    					$result['message'] = 'Paid successfully in admin...';
    					$this->response($result,200);
					}
				}else{
					$result['status'] = false;
					$result['message'] = 'Unable to proceed your request...';
					$this->response($result,200); 
				}
			
		}else{
			$result['status'] = false;
			$result['message'] = 'Invalid payment request...';
			$this->response($result,200); 
		}
	}

	/*easbuzz functions - by haritha */
	function _payment($params, $redirect, $merchant_key, $salt, $env){
		$postedArray = '';
		$URL = '';
		// argument validation
		$argument_validation = $this->_checkArgumentValidation($params, $merchant_key, $salt, $env);
		if (is_array($argument_validation) && $argument_validation['status'] === 0) {
			return $argument_validation;
		}
		// push merchant key into $params array.
		$params['key'] =  $merchant_key;
		// remove white space, htmlentities(converts characters to HTML entities), prepared $postedArray.
		$postedArray = $this->_removeSpaceAndPreparePostArray($params);
		// empty validation
		$empty_validation = $this->_emptyValidation($postedArray, $salt);
		if (is_array($empty_validation) && $empty_validation['status'] === 0) {
			return $empty_validation;
		}
		// check amount should be float or not 
		if (preg_match("/^([\d]+)\.([\d]?[\d])$/", $postedArray['amount'])) {
			$postedArray['amount'] = (float) $postedArray['amount'];
		}
		// type validation
		$type_validation = $this->_typeValidation($postedArray, $salt, $env);
		if ($type_validation !== true) {
			return $type_validation;
		}
		// again amount convert into string
		$diff_amount_string = abs(strlen($params['amount']) - strlen("" . $postedArray['amount'] . ""));
		$diff_amount_string = ($diff_amount_string === 2) ? 1 : 2;
		$postedArray['amount'] = sprintf("%." . $diff_amount_string . "f", $postedArray['amount']);
		// email validation
		$email_validation = $this->_email_validation($postedArray['email']);
		if ($email_validation !== true)
			return $email_validation;
		// get URL based on enviroment like ($env = 'test' or $env = 'prod')
		$URL = $this->_getURL($env);
		// process to start pay
		$pay_result = $this->_pay($postedArray, $redirect, $salt, $URL);
		return $pay_result;
	}
	function _pay($params_array, $redirect, $salt_key, $url){
		$hash_key = '';
		// generate hash key and push into params array.
		$hash_key = $this->_getHashKey($params_array, $salt_key);
		$params_array['hash'] = $hash_key;
		// call curl_call() for initiate pay link
		$curl_result = $this->_curlCall($url . 'payment/initiateLink', http_build_query($params_array));
		//  print_r($curl_result);
		//  die;
		$accesskey = ($curl_result->status === 1) ? $curl_result->data : null;
		if (empty($accesskey)) {
			return $curl_result;
		} else {
			if ($redirect == true) {
				$curl_result->data = $url . 'pay/' . $accesskey;
			} else {
				$curl_result->data = $accesskey;
				// return $accesskey;
			}
			return $curl_result;
		}
	}
	function _curlCall($url, $params_array){
		// Initializes a new session and return a cURL.
		$cURL = curl_init();
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		// Set multiple options for a cURL transfer.
		curl_setopt_array(
			$cURL,
			array(
				CURLOPT_URL => $url,
				CURLOPT_POSTFIELDS => $params_array,
				CURLOPT_POST => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36',
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_SSL_VERIFYPEER => 0
			)
		);
		// Perform a cURL session
		$result = curl_exec($cURL);
		// check there is any error or not in curl execution.
		if (curl_errno($cURL)) {
			$cURL_error = curl_error($cURL);
			if (empty($cURL_error))
				$cURL_error = 'Server Error';
			return array(
				'curl_status' => 0,
				'error' => $cURL_error
			);
		}
		$result = trim($result);
		$result_response = json_decode($result);
		return $result_response;
	}
	function _getHashKey($posted, $salt_key){
		$hash_sequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
		// make an array or split into array base on pipe sign.
		$hash_sequence_array = explode('|', $hash_sequence);
		$hash = null;
		// prepare a string based on hash sequence from the $params array.
		foreach ($hash_sequence_array as $value) {
			$hash .= isset($posted[$value]) ? $posted[$value] : '';
			$hash .= '|';
		}
		$hash .= $salt_key;
		#echo $hash;
		#echo " ";
		#echo strtolower(hash('sha512', $hash));
		// generate hash key using hash function(predefine) and return
		return strtolower(hash('sha512', $hash));
	}
	function _getURL($env){
		$url_link = '';
		switch ($env) {
			case 'test':
				$url_link = "https://testpay.easebuzz.in/";
				break;
			case 'prod':
				$url_link = 'https://pay.easebuzz.in/';
				break;
			case 'local':
				$url_link = 'http://localhost:8005/';
				break;
			case 'dev':
				$url_link = 'https://devpay.easebuzz.in/';
				break;
			default:
				$url_link = "https://testpay.easebuzz.in/";
		}
		return $url_link;
	}
	function _checkArgumentValidation($params, $merchant_key, $salt, $env){
		$args = func_get_args();
		$argsc = count($args);
		if ($argsc !== 4) {
			return array(
				'status' => 0,
				'data' => 'Invalid number of arguments.'
			);
		}
		return 1;
	}
	function _email_validation($email){
		$email_regx = "/^([\w\.-]+)@([\w-]+)\.([\w]{2,8})(\.[\w]{2,8})?$/";
		if (!preg_match($email_regx, $email)) {
			return array(
				'status' => 0,
				'data' => 'Email invalid, Please enter valid email.'
			);
		}
		return true;
	}
	function _removeSpaceAndPreparePostArray($params){
		$temp_array = array();
		foreach ($params as $key => $value) {            
				if (array_key_exists($key, $params)  and  !empty($key) ){
					if($key != "split_payments"){
						$temp_array[$key] = trim(htmlentities($value, ENT_QUOTES));
						}else{
						$temp_array[$key] = trim($value);
						}
				}            
		}        
		return $temp_array;
	}
	function _emptyValidation($params, $salt){
		$empty_value = false;
		if (empty($params['key']))
			$empty_value = 'Merchant Key';
		if (empty($params['txnid']))
			$empty_value = 'Transaction ID';
		if (empty($params['amount']))
			$empty_value = 'Amount';
		if (empty($params['firstname']))
			$empty_value = 'First Name';
		if (empty($params['email']))
			$empty_value = 'Email';
		if (empty($params['phone']))
			$empty_value = 'Phone';
		if (!empty($params['phone'])){
			if (strlen((string)$params['phone'])!=10){
				$empty_value = 'Phone number must be 10 digit and ';
			}
		}
		if (empty($params['productinfo']))
			$empty_value = 'Product Infomation';
		if (empty($params['surl']))
			$empty_value = 'Success URL';
		if (empty($params['furl']))
			$empty_value = 'Failure URL';
		if (empty($salt))
			$empty_value = 'Merchant Salt Key';
		if ($empty_value !== false) {
			return array(
				'status' => 0,
				'data' => 'Mandatory Parameter ' . $empty_value . ' can not empty'
			);
		}
		return true;
	}
	function _typeValidation($params, $salt, $env){
		$type_value = false;
		if (!is_string($params['key']))
			$type_value = "Merchant Key should be string";
		if (!is_float($params['amount']))
			$type_value = "The amount should float up to two or one decimal.";
		if (!is_string($params['productinfo']))
			$type_value =  "Product Information should be string";
		if (!is_string($params['firstname']))
			$type_value =  "First Name should be string";
		if (!is_string($params['phone']))
			$type_value = "Phone Number should be number";
		if (!is_string($params['email']))
			$type_value = "Email should be string";
		if (!is_string($params['surl']))
			$type_value = "Success URL should be string";
		if (!is_string($params['furl']))
			$type_value = "Failure URL should be string";
		if ($type_value !== false) {
			return array(
				'status' => 0,
				'data' => $type_value
			);
		}
		return true;
	}
	function easebuzzResponse_post(){
	    
		$model = self::ADV_MOD;
		
			/* {"status":1,"url":"http:\/\/localhost\/paywitheasebuzz-php-lib-master\/response.php","data":{"name_on_card":"Test","bank_ref_num":"NA","udf3":"",
			"hash":"18597796bb6944d8a43de378b23a443b1fe38b376f006693e3b0b95d692fb05c5ced14e24d6eb8f00a73605f54520ca1c23ac6be03d860c3ca3b56c3aae70bf3","firstname":"Haritha",
			"net_amount_debit":"43.0","payment_source":"Easebuzz","surl":"http:\/\/localhost\/paywitheasebuzz-php-lib-master\/response.php",
			"error_Message":"ResponseCode : BNF (Bin not found)","issuing_bank":"NA","cardCategory":"NA","phone":"9025384947","easepayid":"E230512TQYBDMR",
			"cardnum":"XXXXXXXXXXXX1111","key":"ZJFVJEL714","udf8":"","unmappedstatus":"NA","PG_TYPE":"NA","addedon":"2023-05-12 07:19:19","cash_back_percentage":"50.0",
			"status":"failure","card_type":"Credit Card","merchant_logo":"NA","udf6":"","udf10":"","upi_va":"NA","txnid":"333","productinfo":"Laptop","bank_name":"NA",
			"furl":"http:\/\/localhost\/paywitheasebuzz-php-lib-master\/response.php","udf1":"","amount":"43.0","udf2":"","udf5":"","mode":"CC","udf7":"","error":"ResponseCode : BNF (Bin not found)","udf9":"","bankcode":"NA","deduction_percentage":"1.95","email":"haritha@vikashinfosolutions.com","udf4":""}}*/
			$postData = $this->get_values();     //print_r($postData);exit;
			$trans_id      = $postData['response']->txnid;
			$txStatus      = $postData['response']->status; 
			$orderAmount   = isset($postData['response']->amount) ? $postData['response']->amount : NULL;
			$referenceId   = isset($postData['response']->easepayid) ? $postData['response']->easepayid : NULL;
			$paymentMode   = $postData['response']->mode;
			$txMsg         = "Online Money Transaction";
			$txTime        =  NULL;
			// $computedSignature = base64_encode($hash_hmac); 
			// Write log 
			if (!is_dir($this->log_dir.'/easebuzz')) {
				mkdir($this->log_dir.'/easebuzz', 0777, true);
			}
			$log_path = $this->log_dir.'/easebuzz/mob_response_'.date("Y-m-d").'.txt';
			$ldata = "\n".date('d-m-Y H:i:s')." \n Mobile : ".json_encode($postData,true);
			file_put_contents($log_path,$ldata,FILE_APPEND | LOCK_EX);
			
			
			
			if(!empty($trans_id) && $trans_id != NULL)
			{
				$updateData = array( 
								"payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":($paymentMode == "NA" ? "-":$paymentMode)))),
								"payu_id"            => $referenceId,
								"remark"             => $txMsg."[".$txTime."] mbl-status",
								"payment_ref_number" => $referenceId,
								"payment_status"     => 1,
								); 
				$payment = $this->$model->updateGatewayResponse($updateData,$trans_id);    
				if($payment['status'] == true)
				{
					$serviceID = 0;
					$pay_msg = "Your payment has been ".$txStatus;
					if($txStatus==="success")
					{
						$serviceID = 3; 
						$type = 2;
						$pay_msg = 'Transaction ID: '.$trans_id.'. Payment amount INR. '.$orderAmount.' is paid successfully. Thanks for your payment with '.$this->comp['company_name'];
					}
					else if($txStatus === "failure")
					{ 
						$serviceID = 7; 
						$type = -1;
						$pay_msg = 'Your payment has been failed. Please try again. Transaction ID: '.$trans_id;
					}
					else if($txStatus === "userCancelled")
					{ 
						$serviceID = 7; 
						$type = 3;
						$pay_msg = 'Your payment has been cancelled.';
					}
				
					if($serviceID > 0){
						if($txStatus==="success")
						{   
							$payIds = $this->$model->getPayIds($trans_id);	
							if(sizeof($payIds) > 0)
							{
								foreach ($payIds as $pay)
								{
								    
									// Multi mode payment
									if($updateData['payment_mode']!= NULL)
									 {
										 $arrayPayMode=array(
														'id_adv_payment '         => $pay['id_payment'],
														'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
														'payment_date'		 => date("Y-m-d H:i:s"),
														'created_time'	     => date("Y-m-d H:i:s"),
														"payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":$paymentMode))),
														"remark"             => $txMsg."[".$txTime."] mbl-status",
														"payment_ref_number" => $referenceId,
														"payment_status"     => 1
														);
										if(!empty($arrayPayMode)){
											$cashPayInsert = $this->$model->insertData($arrayPayMode,'ct_advbook_mode_detail'); 
										}
									 }
								$amount += $pay['payment_amount'];
								$booking_id = $pay['booking_id'];
								$id_cus = $pay['id_customer'];
								
								}
							}
							
							//update account status
						
						$adv = $this->$model->get_paymentData($id_cus,'ADMIN',$booking_id,'','','');
    					
    					$total = $adv['total_paid_amount'] + $amount;
    					
    					if($total >= $adv['booking_amount']){
                    		$updAcc = array('status' => 3);   //paid
                    	}elseif($total >= $adv['advance_amt'] && $total < $adv['booking_amount']){
                    		$updAcc = array('status' => 2);   //advance paid
                    	}
    					
    					$this->$model->updData($updAcc, 'booking_id', $booking_id, 'ct_advance_account');
    					
    					
    				
						}
/*						$service = $this->services_modal->checkService($serviceID); 
						if($service['sms'] == 1)
						{
							$id=$payment['id_payment'];
							$data =$this->services_modal->get_SMS_data($serviceID,$id);
							$mobile =$data['mobile'];
							$message = $data['message'];
							$this->mobileapi_model->send_sms($mobile,$message,'',$service['dlt_te_id']);
						}*/
					}  
					$response = array("status" => TRUE, "title" => "Transaction ".$txStatus, "msg" => $pay_msg);
				}else{ 
					$response = array("status" => TRUE, "title" => "Transaction ".$txStatus, "msg" => "Unable to proceed your payment.."); 
				} 
			}else{ 
				$response = array("status" => TRUE, "title" => "Transaction ".$txStatus, "msg" => "Your payment has been Cancelled");
			}
			$this->response($response,200); 
		}
	//ends
    
    function random_strings($length)
    {
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($str_result), 0, $length);
    }
    
    	 
    









}
?> 


