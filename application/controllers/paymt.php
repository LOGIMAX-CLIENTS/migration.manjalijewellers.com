<?php
ob_start();
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH.'libraries/payu.php');
require_once (APPPATH.'libraries/techprocess/TransactionRequestBean.php');
require_once (APPPATH.'libraries/techprocess/TransactionResponseBean.php');
//require_once(APPPATH.'libraries/hdfc.php');
//require_once (APPPATH.'libraries/ccavenue/Crypto.php');
require_once (APPPATH.'libraries/atompay/TransactionRequest.php');
require_once (APPPATH.'libraries/atompay/TransactionResponse.php');
function payment_success()
{
	$userObj = new Paymt();
	$result = $userObj->payment_success();
}
function payment_failure()
{
	$userObj = new Paymt();
	$result = $userObj->payment_failure();
}
function payment_cancel()
{
	$userObj = new Paymt();
	$result = $userObj->payment_cancel();
}
function mobile_failure()
{
	$userObj = new Paymt();
	$result = $userObj->failureMURL();
}
function mobile_cancel()
{
	$userObj = new Paymt();
	$result = $userObj->cancelMURL();
}
function mobile_success()
{
	$userObj = new Paymt();
	$result = $userObj->successMURL();
}
class Paymt extends CI_Controller {
	const VIEW_FOLDER = 'chitscheme/';
	const SERV_MODEL = 'services_modal';
	const API_MODEL = 'syncapi_model';
	const CHITAPI_MODEL = 'chitapi_model';
	public function __construct()
    {
        parent::__construct();
		ini_set('date.timezone', 'Asia/Kolkata');
		$this->branch = array(1,2,3,4,5,6,7,8); // for SSS
	    $this->load->model('login_model');
        $this->m_mode=$this->login_model->site_mode();
        if( $this->m_mode['maintenance_mode'] == 1) {
        	redirect("user/maintenance");
	    }
		$this->load->model('registration_model');
		$this->load->helper('metal_wgt_digit');
		$this->load->model('payment_modal');
		$this->load->model('digigold_modal');
		$this->load->model('scheme_modal');		
		$this->load->model('email_model');
		$this->load->model('services_modal');
		$this->load->model('syncapi_model');
		$this->load->model('chitapi_model');
		$this->load->model('sms_model');
		$this->load->model("mobileapi_model");
		if($this->config->item("integrationType") == 5 ){
            $this->load->model("integration_model");
		}
		$this->comp = $this->login_model->company_details();
		//$this->branch_settings = $this->login_model->branch_settings();
		$this->scheme_status = $this->scheme_modal->scheme_status();
		//$this->payment_gateway = $this->payment_modal->get_payment_gateway();
		$this->sms_data = $this->services_modal->sms_info();
		$this->sms_chk = $this->services_modal->otp_smsavilable();
		$this->branch_settings =  $this->session->userdata('branch_settings');
		//default payment status 
		$this->payment_status = array(
										'pending'   => 7,
										'awaiting'  => 2,
										'success'   => 1,
										'failure'   => 3,
										'cancel'    => 4
									  );
		$this->log_dir = 'log/'.date("Y-m-d");
    	if (!is_dir($this->log_dir)) {
            mkdir($this->log_dir, 0777, TRUE); 
        }
    }
    public function payment_gateway($id_branch,$id_pg)
	{		
		/*param_1 => key
		param_2 => salt
		param_3 => Access code for ccavenue/ Merchant code for Techprâ€¦
		param_4 => merchant_id for ccavenue / iv for techprocess*/
		//$data = $this->payment_modal->getBranchGateways($id_branch);
		$data = $this->payment_modal->getBranchGatewayData($id_branch,$id_pg);  
		return $data;
	}
    public function index()
	{
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{
            $schemes = $this->payment_modal->get_payment_details($this->session->userdata('username'));
            $scheme['chits'] = $this->array_sort($schemes['chits'],'allow_pay',SORT_DESC);
            $paymentgateway = $this->payment_modal->getBranchGateways($this->session->userdata('id_branch'));
            $scheme['chits'] = $this->array_sort($schemes['chits'],'id_scheme_account',SORT_DESC);
            $data['settings'] = $this->payment_modal->get_settings();
            if($data['settings']['cost_center'] == 3){				
            	$data['branches'] = $this->payment_modal->branchesData();
			}
            $data['content'] = $scheme;
            $data['gateway']=$paymentgateway;
            $pageType = array('page' => 'payment','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
            $data['header_data'] = $pageType;
            $data['footer_data'] = $pageType;
            $data['fileName'] = self::VIEW_FOLDER.'payment';
            $this->load->view('layout/template', $data);
		}
	}	
	//sort array
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
	public function getPaymentContent($id_scheme_account)
	{
		$scheme = $this->payment_modal->get_paymentContent($id_scheme_account);
		echo json_encode($scheme);
	}
    public function wallet_transaction()
	{
	    $wallets =	$this->payment_modal->get_wallet_accounts();	
	    $transactions =	$this->payment_modal->get_wallet_transactions();	
		$wallet_data = array('wallets' => $wallets,'transactions' => $transactions);
		$data['content'] = $wallet_data;
	  	$pageType = array('page' => 'payment','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER.'wallet';
		$this->load->view('layout/template', $data);
	}
	/* public function paySubmit()
	{
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{ 
		 $payments = $this->input->post('pay');
		 $cusData = $this->input->post('cus');
		   $pay_flag = FALSE;	
		   $submit_pay = FALSE;   
		   $payment_amount = 0.00;
		   $productinfo = "";
		   $udf1= "";
		   $udf2= "";
		   $udf3= "";
		   $udf4= "";
		   $udf5= 0;
	       $txnid = uniqid('DRHN'.time());
		   $i=1;
		  $recdTransAmount = $_POST['total_amount'];
		   $calc_amt =0.00;
		   foreach($payments as $pay){
		   	if($pay['ischecked'] == 1){
		   		$calc_amt = $calc_amt + $pay['amount'] + $pay['udf5'];
		   		}
		   	}
		 // echo ceil($calc_amt);
		  if($recdTransAmount == ceil($calc_amt)){
	    	foreach($payments as $payData){
		   	if($payData['ischecked'] == 1){ // insert data if chit is selected
		   	$rcvdAmount = (float) $payData['amount'];
		   //validate amount
		      if(isset($payData)&& $payData['scheme_type']==1)
			   {
			   	  $metal_rate = $this->payment_modal->getMetalRate();	
			      $gold_rate = (float) $metal_rate['goldrate_22ct'];
			      $actAmount = $gold_rate * $payData['udf2'];
			     $pay_flag =  ($rcvdAmount >= $actAmount? TRUE :($payData['discountedAmt']==""?FALSE:TRUE));
			   }	
			   else
			   {
			   	 $chit = $this->payment_modal->get_schemeByChit($payData['udf1']);
				// echo "<pre>";print_r($chit);echo "</pre>";
			   	 $pay_flag =  ($rcvdAmount >= $chit['amount'] ? TRUE :($payData['discountedAmt']==""?FALSE:TRUE));
				 // echo "<pre>";print_r($pay_flag);echo "</pre>";exit;
			   }
			   //check pay_flag
			   if($pay_flag )
			   {					
					//set insert data
							$insertData = array(
								"id_scheme_account"	 => (isset($payData['udf1'])? $payData['udf1'] : NULL ),
								"payment_amount" 	 => (isset($payData['amount'])? ($payData['amount']+$payData['discountedAmt']) : NULL ), 
								"bank_charges" 	     => (isset($payData['udf5']) ?$payData['udf5'] : NULL), 
								"date_payment" 		 => date('Y-m-d H:i:s'),
								"metal_rate"         => (isset($payData['udf3']) && $payData['udf3'] !='' ? $payData['udf3'] : 0.00),
								"metal_weight"       => (isset($payData['udf2']) && $payData['udf2'] !='' ? $payData['udf2'] : 0.000),
								"actual_trans_amt"   => (isset($recdTransAmount) ? $recdTransAmount : 0.00),
								"id_transaction"           => (isset($txnid) ? $txnid.'-'.$i : NULL),
								"ref_trans_id"       => (isset($txnid) ? $txnid : NULL),// to update pay status after trans complete.
								"discountAmt"        => ($payData['discountedAmt']!="" ? $payData['discountedAmt'] : 0.00),
								"payment_status"     => -1 //status - 0 (pending), will change to 1 after approved at backend
							);
					$this->db->trans_begin();		
					//inserting pay_data before gateway process
					$payment = $this->payment_modal->addPayment($insertData);
					$payment_amount = $payment_amount+$payData['payment_amt'];
					$productinfo = $productinfo." ".$payData['productinfo'];
					$udf1 = $udf1." ".$payData['udf1'];
					$udf2 = $udf2." ".$payData['udf2'];
					$udf3 = $udf3." ".$payData['udf3'];
					$udf4 = $udf4." ".$payData['udf4'];
					$udf5 = $udf5+$payData['udf5'];
				  $i++;
		        }
		       }
			 if($this->db->trans_status()===TRUE)
             {	
			 	$this->db->trans_commit();
			 	$submit_pay = TRUE;
			 }
			 else{
			 	$this->db->trans_rollback();
				$this->session->set_flashdata('errMsg','Unable to proceed your request,please try after sometime..');
				$submit_pay = FALSE;
				redirect("/paymt");
			}
		  }
		  //exit;
		 }
			else{
				$this->session->set_flashdata('errMsg','Unable to proceed your request,please try after sometime..');
				$submit_pay = FALSE;
				redirect("/paymt");
			}		
					if($submit_pay == TRUE)
					{
						//set data for hash generation
						$data['pay'] =	array (
						'key' 			=> $this->payment_gateway[0]['key'], 
						'txnid' 		=> $txnid, 
						'amount' 		=> (isset($payment_amount)   ? $payment_amount:''),
						'productinfo'	=> (isset($productinfo)? $productinfo: ''),
						'firstname' 	=> (isset($cusData['firstname'])? $cusData['firstname'] :''),
						'lastname' 		=> (isset($cusData['lastname']) ? $cusData['lastname']:''),
						'email' 		=> (isset($cusData['email'])    ? $cusData['email']:''), 
						'phone' 		=> (isset($cusData['phone'])    ? $cusData['phone'] :''),
						'address1'		=> (isset($cusData['address1']) ? $cusData['address1']:''), 
						'address2'		=> (isset($cusData['address2']) ? $cusData['address2'] :''), 
						'city'			=> (isset($cusData['city']) ? $cusData['city'] :''), 
						'state'			=> (isset($cusData['state']) ? $cusData['state'] : ''), 
						'country'		=> (isset($cusData['country']) ? $cusData['country'] : ''), 
						'zipcode'		=> (isset($cusData['zipcode']) ? $cusData['zipcode'] :''), 
						'udf1' 			=> (isset($udf1)    ? $udf1 :''),
						'udf2' 			=> (isset($udf2)    ? $udf2 :''),
						'udf3'			=> (isset($udf3)    ? $udf3 :''),
						'udf4' 			=> (isset($udf4)    ? $udf4 : ''),
						'udf5' 			=> (isset($udf5)	? $udf5:'') 
					  );
					  //generate hash
					  $hash_sequence =   Misc::get_hash( $data['pay'],$this->config->item('salt'));		
						if($hash_sequence!='' && $txnid !='')
						{
							$data['pay']['hash'] 		 = $hash_sequence; 
							$data['pay']['curl']         =  $this->config->item('base_url')."index.php/paymt/cancelURL"; 
							$data['pay']['furl']         =  $this->config->item('base_url')."index.php/paymt/payment_failure"; 
							$data['pay']['surl']         =  $this->config->item('base_url')."index.php/paymt/payment_success";
							$data['pay']['user_credentials']     =  $this->payment_gateway[0]['key'].':'.$cusData['phone'];
							$this->load->view('web/payment',$data);
						}
					}
			   }
	} */
	public function paySubmit()
	{
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{ 
		   $payments = $this->input->post('pay');
		   $cusData = $this->input->post('cus');			
		   $wallet = $this->input->post('wallet');
		   $gateway= $this->input->post('payment'); //id_gateway	
		   $is_use_wallet = $wallet['use_wallet'];		
		   $recdTransAmount = $this->input->post('total_amount');
		   $redeem_request = (isset($wallet['redeem_request']) ?floor($wallet['redeem_request']):0);
           $submitpay_flag = TRUE;
           $pay_flag = FALSE;
		   $payment_amount = 0.00;
		   $payment_amt = 0.00;
		   $productinfo = "";
		   $udf1= "";
		   $udf2= "";
		   $udf3= "";
		   $udf4= "";
		   $udf5= "";
		   $udf6=0;
		   $i=1;
		   $txnid =uniqid(time());
   		   $calc_amt =0.00;
   		   $calc_mt =0.00;
   		   $sel_dues_amt = 0;
		   $redeemed_amount =0.00;
		   $used_wallet =FALSE; 
   		   //NOTE : $pay['amount'] -> (payment amount per instalment - discount) , $payData['actamt'] -> actual payable per installment
   		   // calculate amount to cross check	  
		   $walletData = $this->payment_modal->wallet_balance();
	   	   // echo "<pre>";print_r($payments);exit;
		   foreach($payments as $pay){
		   	//echo "<pre>";print_r($pay);exit;
		   	$discount = ($pay['discount'] >0 ? $pay['discount'] : 0.00);
		   	$pay['discount'] = $discount;
		   	$metal_rate = $this->payment_modal->getMetalRate($pay['id_branch']);
			if($pay['ischecked'] == 1){
				$sch_data = $this->payment_modal->get_schemeByChit($pay['udf1']);
				$gold_rate = (float) $metal_rate[$pay['rate_field']]; 
			//	print_r($gold_rate);exit;
				if($pay['sel_dues']>1)
                {
			  	    $amount = ($pay['actamt']+$pay['discount'])/$pay['sel_dues'];
			    }
			    else
			    {
			        $amount = $pay['actamt']+$pay['discount'];
			    } 
    			$gst_amt = 0;
    			if($sch_data['gst'] > 0 ){
			        $insAmt_withoutDisc = $amount-$pay['discount'];
			        if($sch_data['gst_type'] == 0){  // Inclusive  
                        $gst_removed_amt = $insAmt_withoutDisc*100/(100+$sch_data['gst']);
                    	$gst_amt = $insAmt_withoutDisc - $gst_removed_amt;
                    	$metal_wgt = ($gst_removed_amt+$pay['discount'])/$pay['udf3'];  
                    }
                    else if($sch_data['gst_type'] == 1){ // Exclusive
                        $amt_with_gst = $insAmt_withoutDisc*((100+$sch_data['gst'])/100);
                    	$gst_amt = $amt_with_gst - $insAmt_withoutDisc ;
                    	$metal_wgt = $amount/$pay['udf3'] ; 
                    }
   	            }
				if($pay['sel_dues']>1 && $pay['allowed_dues']>1){
					for($a=1;$a<=$pay['sel_dues'];$a++){
						 $disc = ($a==1 ? $pay['discount']: 0.00);
						 $calc_amt = $calc_amt + ($pay['amount']-$disc)+ $pay['udf6'];
						}
				}
				else{//  udf6 -> charge
					 $calc_amt = $calc_amt + $pay['amount']+ $pay['udf6'];
				//print_r($calc_amt);exit;
				}	
				$calc_amt = ($sch_data['gst_type']==1 ? ($calc_amt+($gst_amt*$pay['sel_dues'])):$calc_amt);
			  }
			}
			if($is_use_wallet == 1 ){	
				 $allowed_redeem = ($recdTransAmount*($walletData['redeem_percent']/100)); 
			  	 if( $allowed_redeem > $walletData['wal_balance'] ){
				 	$can_redeem = $walletData['wal_balance'];
				 }else{
				 	$can_redeem = $allowed_redeem;
				 }
				 $redeemed_amount = floor($redeem_request <= $can_redeem ? $redeem_request : $can_redeem);
				 $used_wallet = TRUE;
		    }
		    //echo ceil($recdTransAmount) == ceil($calc_amt);
			//	echo $recdTransAmount; echo 'calc - '.$calc_amt;exit;
			// Proceed if recdTransAmount equals calculated amount
		   if($recdTransAmount == round($calc_amt)){			
			 $this->db->trans_begin();		
			 foreach($payments as $payData){ 
			    //echo "<pre>"; print_r($payData);exit;
			 	if($payData['ischecked'] == 1){ 
                    $chit = $this->payment_modal->get_paymentContent($payData['udf1']);
                    if($payData['sel_dues']>1)
	                {
				  	    $amount = ($payData['actamt']+$payData['discount'])/$payData['sel_dues'];
				    }
				    else
				    {
				        $amount = $payData['actamt']+$payData['discount'];
				    }
                    $gst_amt = 0;
	    			if($chit['chit']['gst'] > 0 ){
				        $insAmt_withoutDisc = $amount-$payData['discount'];
				        if($sch_data['gst_type'] == 0){  // Inclusive  
	                        $gst_removed_amt = $insAmt_withoutDisc*100/(100+$chit['chit']['gst']);
	                    	$gst_amt = $insAmt_withoutDisc - $gst_removed_amt;
	                    	$metal_wgt = ($gst_removed_amt+$payData['discount'])/$payData['udf3'];  
	                    }
	                    else if($sch_data['gst_type'] == 1){ // Exclusive
	                        $amt_with_gst = $insAmt_withoutDisc*((100+$chit['chit']['gst'])/100);
	                    	$gst_amt = $amt_with_gst - $insAmt_withoutDisc ;
	                    	$metal_wgt = $amount/$payData['udf3']; 
	                    }
	   	            } 
                    $rcvdAmount = (float) $payData['amount'];
                    $this->session->set_userdata('id_pg',$gateway['id_pg']);
                    $gateway_pg_code= $this->payment_gateway($this->session->userdata('id_branch'),$gateway['id_pg']); 
                    $pg_code = $gateway_pg_code['pg_code'];
                    $this->session->set_userdata('pg_code',$pg_code);
                    $this->session->set_userdata('txn_id',$txnid);
                    $this->session->set_userdata('amount',($recdTransAmount-$redeemed_amount));
                    // insert data if chit is selected
                    if($chit['chit']['scheme_type']==1)
                    {
                      $rate_fields = $this->payment_modal->getRateFields($chit['chit']['id_metal']); 
            	      $rate_field = sizeof($rate_fields) == 1 ? $rate_fields['rate_field'] : NULL;
			          $gold_rate   = (float) ( $rate_field == null ? null : $chit['metal_rates'][$rate_field] );
                      //$gold_rate = (float) $chit['metal_rates']['goldrate_22ct'];
                      $actAmount = $gold_rate * $payData['udf2'];
                      $pay_flag =  ($rcvdAmount >= $actAmount? TRUE :($payData['discount']==""?FALSE:TRUE));
                    }
                    else
                    {
                      $pay_flag =  ($rcvdAmount >= $chit['chit']['payable'] ? TRUE :($payData['discount']==""?FALSE:TRUE));
                    }
                    if($this->config->item('pay_branchId') == NULL)
                    {	
                    	$id_branch  = $chit['chit']['ac_branch'];
                    }
    				else{
    					$id_branch = $this->config->item('pay_branchId');
    				}
			        if((isset($payData) && $payData['udf1']!='' && $payData['amount'] && $pay_flag == TRUE) ||(isset($_POST)&& $_POST['txnid'] && $_POST['udf1']))//cross check received and actual amount
    				{
    					if(isset($payData) && $payData['udf1']!='')
    					{
    						if($gst_amt == 0){
								if($chit['chit']['scheme_type']==2)
	    						{
	    							$amount = ($chit['chit']['gst_type'] == 0 ? ($payData['actamt']-$gst_amt):$payData['actamt']);
	    							$data = array('metal_rate'=>$payData['udf3'],'amount'=>$amount);
	    							$metal_wgt = $this->amount_to_weight($data);
	    						}
	    						else if($chit['chit']['one_time_premium']==1 && $chit['chit']['wgt_convert']==0 ){
    								$metal_wgt = 0;
    							}
	    						else
	    						{
	    							$metal_wgt = (isset($payData['udf2']) && $payData['udf2'] !='' ? $payData['udf2'] : 0.000);
	    						}
							}  
							// metal_wgt_decimal = 2 means only 2 decimals are allowed for metal wgt, hence bcdiv() is used to make the weight to 2 decimals and 0 is appended as last digit.
							$decimal = $chit['chit']['metal_wgt_decimal'];   
                            $round_off = $chit['chit']['metal_wgt_roundoff'] ; 
                            $metal_wgt =  ($round_off == 0 ? bcdiv($metal_wgt,1,$decimal) : $metal_wgt );
							$metal_weight = ( $chit['chit']['scheme_type'] == 1 || $chit['chit']['scheme_type'] == 2 || ($chit['chit']['scheme_type'] == 3 && $chit['chit']['flexible_sch_type'] >= 2) ) ? ( $metal_wgt !='' ? $metal_wgt : 0.000) : 0 ;
    						$entry_date = $this->get_entrydate($id_branch); // Taken from ret_day_closing  table branch wise //HH
						$custom_entry_date = $entry_date['custom_entry_date'];
    						$insertData = array(
    									"id_scheme_account"	 => (isset($payData['udf1'])? $payData['udf1'] : NULL ),
    									//"payment_amount" 	 => $amount, 
                                         "payment_type" 	 => ($redeemed_amount == $calc_amt?"Wallet Redeem":(isset($pg_code) ? ($pg_code == 1 ? "Payu Checkout":($pg_code == 2 ? "HDFC":($pg_code == 4 ? "Cash Free":($pg_code == 5 ? "Atom":"Tech Process")))): NULL)),
    								 // "custom_entry_date"  => (isset($chit['chit']['custom_entry_date']) ? $chit['chit']['custom_entry_date'] : NULL),
    									"custom_entry_date"  => ($custom_entry_date ? $custom_entry_date :NULL),
    									"gst" 	             => (isset($chit['chit']['gst']) ? $chit['chit']['gst'] : 0),
    									"gst_type" 	   		 => (isset($chit['chit']['gst_type'])  ? $chit['chit']['gst_type'] : 0),
    									"gst_amount" 	   	 => $gst_amt,
    									"no_of_dues" 	     => 1,
    									"act_amount" 	     => $payData['actamt'],
    									"actual_trans_amt"   => ($redeemed_amount == $calc_amt ? 0:($recdTransAmount-$redeemed_amount)),
    									"date_payment"   	 =>   date('Y-m-d H:i:s'),
    									"metal_rate"         => (isset($payData['udf3']) && $payData['udf3'] !='' ? $payData['udf3'] : NULL),
    								//	"metal_rate"         => ($gold_rate !='' ? $gold_rate : 0.00),
    									"metal_weight"       => $metal_weight,	
    									"ref_trans_id"       => (isset($txnid) ? $txnid: NULL),
    									"remark"             => ($redeemed_amount == $calc_amt?'Wallet Redeem':''),
    									"added_by"			 =>  1,
    									"add_charges"		 => (isset($payData['udf6']) ?$payData['udf6'] :0.00),
    									"payment_status"     =>  ($is_use_wallet == 1 && ($redeemed_amount==$calc_amt)?$this->payment_status['success']:$this->payment_status['pending']),
    									"redeemed_amount"    => (isset($redeemed_amount) ?$redeemed_amount :0.00),
    									"id_payGateway"      => (isset($gateway['id_pg']) ? $gateway['id_pg']: NULL),
    									"id_branch"          => ($id_branch)
    								 );	 
    							//	 print_r($insertData);exit;
    						if($this->config->item("integrationType") == 5){ // Generate tranUniqueId
                               $tranUniqueId = $this->generateTranUniqueId($chit['chit'],$payData['actamt']);
                               $insertData['offline_tran_uniqueid'] = $tranUniqueId;
                            }			
    						//inserting pay_data before gateway
    					    $due_month  =   date("m");
							$month      =   date("m");
		    				$due_year   =   date("Y");
							$dueType    =   '';
    						 if($payData['sel_dues']>1 && $chit['chit']['allowed_dues']>1){	
    		  					$pay_amt = 0.00;
    							$due_count= (int)$payData['sel_dues'];
    								for($b=1;$b<=$due_count;$b++){
    									// ND - normal, PN - pending & normal, AN - adv & normal, PD pending due ,AD-adv due
    									$dueType = ''; 
    									if($payData['due_type'] == 'PD'){
										    $month  	=  NULL;
					        				$due_year   =  NULL;
					        				$dueType    = 'PD';
										}
    									else if($payData['due_type'] == 'PN'){
    										$dueType = ($b == 1 ? 'ND' : 'PD'); 
        				    				if($dueType == 'PD'){  
					    						$month  =   NULL;
					        				    $due_year   =   NULL;
											}
											else if($dueType=='ND')
											{ 
											   $dueData = $this->payment_modal->generateDueDate($payData['udf1'],$dueType);
												// set due data
												$month      = $dueData['due_month'];
												$due_year   = $dueData['due_year'];
											}
    									}
    									else if($payData['due_type'] == 'AN'){
    										$dueType = ($b == 1 ? 'ND' : 'AD');
    										if($dueType == 'ND'){
												$dueData = $this->payment_modal->generateDueDate($payData['udf1'],$dueType);
												// set due data
												$month      = $dueData['due_month'];
												$due_year   = $dueData['due_year'];
												$last_due_month	= $month;
												$last_due_year	= $due_year;
											}else{
												$d = $last_due_year."-".$last_due_month."-01";
												$month      = date('m', strtotime("+1 months", strtotime($d)));
								           		$due_year   = date('Y', strtotime("+1 months", strtotime($d)));
											}
    									}
    									else if($payData['due_type']=='ND')
    									{
										    $dueType = $payData['due_type'];
										    $dueData = $this->payment_modal->generateDueDate($payData['udf1'],$dueType);
											// set due data
											$month      = $dueData['due_month'];
											$due_year   = $dueData['due_year'];    									
    									} 
    									else if($payData['due_type']=='AD')
    									{
										    $dueType = $payData['due_type'];
										    $dueData = $this->payment_modal->generateDueDate($payData['udf1'],$dueType);
											// set due data
											$month      = $dueData['due_month'];
											$due_year   = $dueData['due_year'];    									
    									}
    									else
    									{
											$dueType = $payData['due_type'];
										}    								
    									$new = array(
    										'id_transaction' => (isset($txnid) ? $txnid.'-'.$i.''.$dueType.''.$b : NULL),
    										'due_type'		 => $dueType,
    										"payment_amount" => $payData['actamt'],
    										"due_month"     =>$month,
    										"due_year"     =>$due_year,
    										"discountAmt" => ($chit['chit']['discount_installment']==$b ? $payData['discount']:0.00)
    										);
    									$new_insertData=$new+$insertData;
    									/*echo "<pre>"; print_r($payData);
    									echo "<pre>"; print_r($new_insertData);*/
    									$payment = $this->payment_modal->addPayment($new_insertData);
    								}
    								$pay_amt = $payData['payment_amt'];
    						 }
    						 else{
    						 	$pay_amt = 0.00;
                                $dueType = $payData['due_type'];
							    $dueData = $this->payment_modal->generateDueDate($payData['udf1'],$dueType);
								// set due data
								$month      = $dueData['due_month'];
								$due_year   = $dueData['due_year']; 
    							$new = array(
    								'id_transaction' => (isset($txnid) ? $txnid.'-'.$i : NULL),
    								'due_type'		 => ($payData['due_type'] == 'PN' || $payData['due_type'] == 'AN'?'ND':$payData['due_type']) ,
    								"payment_amount" => $payData['actamt'],
    								"discountAmt" 	 => $payData['discount'],
    								 "due_month"      =>$month,
    								 "due_year"       =>$due_year,
    							); 
    							$new_insertData = $new+$insertData;
    							$payment = $this->payment_modal->addPayment($new_insertData); 
    							$pay_amt = $pay_amt+$payData['payment_amt'];
    						 }
    						 $payment_amt = $calc_amt;
    						 $productinfo = $productinfo." ".$payData['productinfo'];
    						 $udf1 = $udf1." ".$payData['udf1'];
    						 $udf2 = $udf2." ".$payData['udf2'];
    						 $udf3 = $udf3." ".$payData['udf3'];
    						 $udf4 = $udf4." ".$payment_amt;
    						 $udf5 = $udf5+$payData['udf5'];
    						 $i++;
    					}				    	
    				  }
			    } 									
				//rate fixed at the time of scheme join
				if($chit['chit']['one_time_premium']==1 && $chit['chit']['rate_fix_by'] == 0 && $chit['chit']['rate_select'] == 1)
				{
				    if($chit['metal_rates']['goldrate_22ct'] != 0)
				    {
				        $isRateFixed = $this->mobileapi_model->isRateFixed($payData['udf1']);
                			    if($isRateFixed['status'] == 0){
                    		        $updData = array(
                	   							"fixed_wgt" => $amount/$chit['metal_rates']['goldrate_22ct'],
                	   							"firstPayment_amt" =>$amount,
                	   							"fixed_metal_rate" => $chit['metal_rates']['goldrate_22ct'],
                	   							"rate_fixed_in" => 1,
                	   							"fixed_rate_on" => date("Y-m-d H:i:s")
                	   						); 
                	   					//print_r($updData);exit; 
                	   				$ratestatus = $this->mobileapi_model->updFixedRate($updData,$payData['udf1']);
                			    }else{
                			        $data = array('is_valid' => TRUE,'success' => TRUE, 'msg' => "Rate already fixed !!");
                			    }
				    }
				}
			 } 
			 if($this->db->trans_status()===TRUE){					
				 $this->db->trans_commit();
				 $submitpay_flag = TRUE;  
				//print_r($submitpay_flag);exit;
				 // Check wallet usage and proceed payment
				 if($used_wallet){	
					if($payment_amt == $redeemed_amount){
						$transData = array();
				        $pay = $this->payment_modal->getWalletPaymentContent($txnid); 
				        if($pay['redeemed_amount'] > 0){ 
		    				$transData = array(	 'mobile' 			=> $pay['mobile'],
		    									 'actual_trans_amt' => $pay['actual_trans_amt'],
		    									 'available_points'	=> ($pay['isAvail'] == 0 ?0:$pay['available_points']),
		    									 'isAvail'			=> ($pay['isAvail'] == 0 ?0:1),
		    									 'redeemed_amount'	=> $pay['redeemed_amount'], 
		    									 'id_payment'       => $pay['id_payment'],
		    									 'txnid'            => $txnid.'- D',
		    									 'branch'           => $pay['branch'],
		    									 'walletIntegration'=> $pay['walletIntegration'],
		    									 'wallet_points'=> $pay['wallet_points'],
		    									 'wallet_amt_per_points'=> $pay['wallet_amt_per_points'],
		    									 'wallet_balance_type'=> $pay['wallet_balance_type']
		    									); 
				    		if(!empty($transData))
				    		{
				    		    $result=$this->insertWalletTrans($transData);
				    		    //$pays = $this->payment_modal->getPayIds($txnid);
				    		    if($result['status']== TRUE)
				    		    {
				    		    	if($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set']==3 )
									{   
										// Generate a/c no
										if($pay['acc_no'] == '' ||  $pay['acc_no'] == null)
										{
											// $this->db->query('LOCK TABLES scheme_account WRITE');
											$scheme_acc_number = $this->payment_modal->account_number_generator($pay['id_scheme'],$pay['branch'],'');
											if($scheme_acc_number != NULL && $scheme_acc_number != "Not Allocated"){
												$schData['scheme_acc_number'] = $scheme_acc_number;
											}else{
												// $this->db->query('UNLOCK TABLES');
											}
										if($pay['gent_clientid'] ==1 && $scheme_acc_number != NULL){  
											$cliData = array(
															 "cliID_short_code"	=> $this->config->item('cliIDcode'),
															 "sync_scheme_code"	=> $pay['sync_scheme_code'],
															 "ac_no"			=> $scheme_acc_number
															);											
											$schData['ref_no'] = $this->payment_modal->generateClientID($cliData);
										}
											if($pay['receipt_no_set'] == 1 && $this->config->item('integrationType') == 2){ 	//upd client id & acc no cus reg table//
											$cliData = array(
															 "cliID_short_code"	=> $this->config->item('cliIDcode'),
															 "sync_scheme_code"	=> $pay['sync_scheme_code'],
															 "ac_no"			=> $scheme_acc_number
															);											
											$schDatacus['ref_no'] = $this->payment_modal->generateClientID($cliData);
											$schDatacus['scheme_acc_number'] = $scheme_acc_number;
										//	$schDatacus['group_code'] =$pay['group_code'];
										}
									$updSchAc = $this->payment_modal->update_account($schData,$pay['id_scheme_account']);
									// $this->db->query('UNLOCK TABLES');
									 $updcusreg = $this->payment_modal->update_cusreg($schDatacus,$pay['id_scheme_account']); //acc no upd to cus reg tab//hh
									 $updtrans = $this->payment_modal->update_trans($schDatacus,$pay['id_scheme_account']); //Client Id upd to trans tab//
										}
									}
									$submitpay_flag = FALSE;
									$this->session->set_flashdata('successMsg','Payment successful');
									redirect("/paymt");
				    		    } 
				    		}				    		 
				         }
					 }						 
				 }
			 }
			 else{
				$submitpay_flag = FALSE;
				// Check wallet usage and proceed payme
				$this->db->trans_rollback();
				$this->session->set_flashdata('errMsg','Unable to proceed your request,please try after sometime..');
				redirect("/paymt");
			 }
			 $paycred = $this->payment_gateway($this->session->userdata('id_branch'),$this->session->userdata('id_pg')); 
			 if($submitpay_flag)
			 {
			 	$amount = ($payment_amt-$redeemed_amount);
				 $data=array (
							'key' 			=> $paycred['param_1'],
							'txnid' 		=> $txnid,
							'amount' 		=> $amount,
							'firstname' 	=> $cusData['firstname'],
							'lastname' 		=> $cusData['lastname'],
						    'email' 		=> !empty($cusData['email']) ? $cusData['email'] : $this->comp['email'],
							'phone' 		=> $cusData['phone'],
							'productinfo'	=> (isset($productinfo)? $productinfo: ''),
							'address1'		=> $cusData['address1'],
							'address2'		=> $cusData['address2'],
							'city'			=> $cusData['city'],
							'state'			=> $cusData['state'],
							'country'		=> $cusData['country'],
							'zipcode'		=> $cusData['zipcode'],
							'm_code'		=> $paycred['param_3'],
							'iv'		    => $paycred['param_4'],
							'udf1' 			=> (isset($udf1)    ? $udf1 :''),
							'udf2' 			=> (isset($udf2)    ? $udf2 :''),
							'udf3'			=> (isset($udf3)    ? $udf3 :''),
							'udf4'			=> (isset($udf4)    ? $udf4 :''),
							'udf5' 			=> (isset($udf5)    ? $udf5 :'')
							); 
				}
			}
			else
			{
				$this->session->set_flashdata('errMsg','Unable to proceed your request,please try after sometime.');
				$submit_pay = FALSE;
				redirect("/paymt");
			}
			//Proceed to pay_page	  
			if($submitpay_flag == TRUE)
			{
				//echo $pg_code;exit;
			    if($pg_code == 3)
			    { // Techprocess
			        $this->submitToTechProcess($data,'web','SS');
			    }else if($pg_code == 2){ // HDFC
                    $merchant_data ="";
                    $merchant_id   = $paycred['param_4'];
                    $working_key   = $paycred['param_1'];//Shared by CCAVENUES
                    $access_code   = $paycred['param_3'] ;//Shared by CCAVENUES 
                    $data['hdfcpay'] =	array (
                    'tid' 		    =>  (rand(10,100).''.time()), // Should contain numbers only
                    'merchant_id'   =>  $merchant_id,
                    'order_id'      =>  $txnid,
                    'amount' 		=>  $amount,
                    'productinfo'	=> (isset($productinfo)? $productinfo: ''),
                    'currency'	    => 'INR',
                    'redirect_url'  =>  $this->config->item('base_url')."index.php/paymt/responseURL",
                    'language'      => 'EN',
                    'id_payment'    => $payment['insertID'],
                    'firstname' 	=> (isset($cusData['firstname'])? $cusData['firstname'] :''),
                    'lastname' 		=> (isset($cusData['lastname']) ? $cusData['lastname']:''),
                    'email' 		=> !empty($cusData['email']) ? $cusData['email'] : $this->comp['email'],
                    'phone' 		=> (isset($cusData['phone'])    ? $cusData['phone'] :''),
                    'address1'		=> (isset($cusData['address1']) ? $cusData['address1']:''), 
                    'address2'		=> (isset($cusData['address2']) ? $cusData['address2'] :''), 
                    'city'			=> (isset($cusData['city']) ? $cusData['city'] :''), 
                    'state'			=> (isset($cusData['state']) ? $cusData['state'] : ''), 
                    'country'		=> (isset($cusData['country']) ? $cusData['country'] : ''), 
                    'zipcode'		=> (isset($cusData['zipcode']) ? $cusData['zipcode'] :''), 
                    'merchant_param1' => (isset($udf1) ? $udf1 :''),
                    'merchant_param2' => (isset($udf2) ? $udf2 :''),
                    'merchant_param3' => (isset($udf3) ? $udf3 :''),
                    'merchant_param4' => (isset($payment['insertID']) ? $payment['insertID'] : ''),
                    'merchant_param5' => (isset($udf5) ? $udf5:'') 
                    );
                    foreach ($data['hdfcpay'] as $key => $value){ 
                        $merchant_data.=$key.'='.urlencode($value).'&';
                    }   
                    $encrypted_data=encrypt($merchant_data,$working_key);
                    //Generate Encrypted Datas
                    if($encrypted_data!='' && $txnid !='')
                    {
                        $data['hdfcpay']['encRequest']   = $encrypted_data;
                        $data['hdfcpay']['access_code']  = $access_code; 
                       // echo "<pre>";print_r($data);exit;
                        $this->load->view('hdfc/payment',$data);
                    }
			    }
			     else if($pg_code == 4){   // cash free//hh
					$secretKey       = $paycred['param_1']; //Shared by Cashfree
					$gen_email =  $this->random_strings(8).'@gmail.com'; 
                    $data['cashfreepay'] =	array (
                    'appId'         => $paycred['param_3'],  //Shared by Cashfree
                    'orderId'       =>  $txnid,
                    'orderAmount' 	=>  $amount, 
                    'orderCurrency'	=> 'INR',
                    'orderNote'	    => 'Online Money Transaction',
                    'customerName' 	=> (isset($cusData['firstname'])? (isset($cusData['lastname']) ? $cusData['firstname'].' '.$cusData['lastname']:$cusData['firstname']) :''),
                  //  'customerEmail' => !empty($cusData['email']) ? $cusData['email'] : $this->comp['email'],
                    'customerEmail' => !empty($cusData['email']) ? $cusData['email'] : $gen_email,
                    'customerPhone' => (isset($cusData['phone'])    ? $cusData['phone'] :''),
					"returnUrl"     => $this->config->item('base_url')."index.php/paymt/cashfreeresponseURL",	
                     "notifyUrl"     => $this->config->item('base_url')."index.php/services/cashfreeStatusNotify/".$this->session->userdata('id_pg')	
                     ); 
                    // print_r($data['cashfreepay']);exit;
                     // get secret key from your config
                    ksort($data['cashfreepay']); 
                    $signatureData = "";
                    foreach ($data['cashfreepay'] as $key => $value){ 
                           $signatureData .= $key.$value;
                      }
					$signature       = hash_hmac('sha256', $signatureData,$secretKey,true);
                    $signature       = base64_encode($signature);
				     //print_r($signature);exit;
					//Generate Encrypted Datas
                    if($signature!='' && $secretKey !='' && $txnid !='')
                    {
                        $data['cashfreepay']['signature']   = $signature; 
                       //print_r($signature);exit;
                        $this->load->view('cashsfree/payment',$data);
                    }
				}  // cash free//hh
				else if($pg_code == 6){   // Ippo Gateway
					$gen_email =  $this->random_strings(8).'@gmail.com'; 
                    $data['ippo'] =	array (
                    'publicKey'         => $paycred['param_3'],  //Shared by Ippo gateway
                    'secretKey'       => $paycred['param_1'], //Shared by Ippo gateway
                    'orderId'       =>  $txnid,
                    'orderAmount' 	=>  $amount, 
                    'orderCurrency'	=> 'INR',
                    'orderNote'	    => 'Online Money Transaction',
                    'customerName' 	=> (isset($cusData['firstname'])? (isset($cusData['lastname']) ? $cusData['firstname'].' '.$cusData['lastname']:$cusData['firstname']) :''),
                    'customerEmail' => !empty($cusData['email']) ? $cusData['email'] : $gen_email,
                    'customerPhone' => (isset($cusData['phone'])    ? $cusData['phone'] :''),
					"returnUrl"     =>   $this->config->item('base_url')."index.php/paymt/ipporesponseURL"                      //$this->config->item('base_url')."index.php/paymt/cashfreeresponseURL"
                     ); 
                   //print_r($data['ippo']);exit;
                    $this->load->view('ippo/ippo_payment',$data);
				}  
				else if($pg_code == 5) // ATOM
			    {  
			        $data['gateway'] = $paycred; 
			        $this->submitToAtom($data,'Web');
			    }
			    else{ // Payu
				    $paycred = $this->payment_gateway($this->session->userdata('id_branch'),$this->session->userdata('id_pg')); 
		            $hash_sequence = Misc::get_hash($data,$paycred['param_2']);	
		            $datas['pay']=	array (
						'key' 			=> $paycred['param_1'],
						'txnid' 		=> $txnid,
						'amount' 		=> $amount,
						'firstname' 	=> $cusData['firstname'],
						'lastname' 		=> $cusData['lastname'],
					    'email' 		=> !empty($cusData['email']) ? $cusData['email'] : $this->comp['email'],
						'phone' 		=> $cusData['phone'],
						'productinfo'	=> (isset($productinfo)? $productinfo: ''),
						'address1'		=> $cusData['address1'],
						'address2'		=> $cusData['address2'],
						'city'			=> $cusData['city'],
						'state'			=> $cusData['state'],
						'country'		=> $cusData['country'],
						'zipcode'		=> $cusData['zipcode'],
						'udf1' 			=> (isset($udf1)    ? $udf1 :''),
						'udf2' 			=> (isset($udf2)    ? $udf2 :''),
						'udf3'			=> (isset($udf3)    ? $udf3 :''),
						'udf4' 			=>(isset($udf4)    ? $udf4 :''),
						'udf5' 			=> (isset($udf5)    ? $udf5 :''),
						'surl'			=> 'payment_success',
						'furl' 			=> 'payment_failure',
						'curl' 			=> site_url('paymt/payment_cancel'),
						'user_credentials' 	=> $paycred['param_1'].':'.$cusData['phone'],
						'hash' 	=> $hash_sequence
					);
				   if($hash_sequence!='' && $txnid !='')
					{ 
						$datas['pay']['hash'] 	=  $hash_sequence;  
						$datas['pay']['curl']    =  $this->config->item('base_url')."index.php/paymt/payment_cancel"; 
						$datas['pay']['furl']    =  $this->config->item('base_url')."index.php/paymt/payment_failure"; 
						$datas['pay']['surl']    =  $this->config->item('base_url')."index.php/paymt/payment_success";
						$datas['pay']['user_credentials'] =  $paycred['param_1'].':'.$cusData['phone'];
						//echo"<pre>"; print_r($datas);exit;
						$this->load->view('web/payment',$datas);
					}
			    } 
			}
			else{
				$scheme_success = array("errMsg" => 'Sorry!! Unable to proceed your request. Please try after some time');
				$this->session->set_flashdata($scheme_success);
				redirect("/paymt");
			}
		}
	} 
	function submitToAtom($payData,$paidThrough)
	{     
	    $transactionRequest = new TransactionRequest();
	    $datenow = date("d/m/Y h:m:s");
        $transactionDate = str_replace(" ", "%20", $datenow);
        //Setting all values here
        //echo "<pre>";print_r($payData['gateway']);
        $mode = ($payData['gateway']['type'] == 1 ? 'live' : 'test' );//echo $mode;exit;
        $clientCode = $this->session->userdata('cus_id');
        $transactionRequest->setMode($mode);
        $transactionRequest->setLogin($payData['gateway']['param_1']);
        $transactionRequest->setPassword($payData['gateway']['param_2']);
        $transactionRequest->setProductId("RAGAVENDRA");
        $transactionRequest->setAmount($payData['amount']);
        $transactionRequest->setTransactionCurrency("INR");
        $transactionRequest->setTransactionAmount($payData['amount']);
        $transactionRequest->setReturnUrl(site_url('paymt/atomReturnURL/'.$paidThrough.'/'.$payData['gateway']['id_branch'].'/'.$payData['gateway']['id_pg']));
        $transactionRequest->setClientCode($clientCode);
        $transactionRequest->setTransactionId($payData['txnid']);
        $transactionRequest->setTransactionDate($transactionDate);
        $transactionRequest->setCustomerName($payData['firstname']);
        $transactionRequest->setCustomerEmailId($payData['email']);
        $transactionRequest->setCustomerMobile($payData['phone']);
        $transactionRequest->setCustomerBillingAddress($payData['state']);
        $transactionRequest->setCustomerAccount("1234567891234567");
        $transactionRequest->setReqHashKey($payData['gateway']['param_3']); 
        /*echo "<pre>";print_r($payData);
        echo "<pre>";print_r($transactionRequest);*/
        $url = $transactionRequest->getPGUrl();
        // Write log 
        if (!is_dir($this->log_dir.'/atom')) {
            mkdir($this->log_dir.'/atom', 0777, true);
        }
        $log_path = $this->log_dir.'/atom/'.date("Y-m-d").'.txt';
        $ldata = "\n ".$paidThrough." - ".date('d-m-Y H:i:s')." \n URL : ".$url."<br/>";
        file_put_contents($log_path,$ldata,FILE_APPEND | LOCK_EX);
        header("Location: $url");
    }
    function atomReturnURL($paidThrough,$id_branch,$id_pg)
    {
        $paycred = $this->payment_gateway($id_branch,$id_pg); 
        $transactionResponse = new TransactionResponse();
        $transactionResponse->setRespHashKey($paycred['param_4']);
        /*echo "Status : ".$_POST['desc']."<br/>";
        echo "<pre>";print_r($_POST);*/
		// Write log 
        if (!is_dir($this->log_dir.'/atom')) {
            mkdir($this->log_dir.'/atom', 0777, true);
        }
        $log_path = $this->log_dir.'/atom/'.date("Y-m-d").'.txt';
        $ldata = "\n ".date('d-m-Y H:i:s')." \n Post : ".json_encode($_POST,true);
        file_put_contents($log_path,$ldata,FILE_APPEND | LOCK_EX);
        if($transactionResponse->validateResponse($_POST)){
            echo "atomReturnURL - Transaction Processed <br/>";
            /*Array
            (
                [date] => Mon Sep 27 12:57:18 IST 2021
                [CardNumber] => 401288XXXXXX1881
                [surcharge] => 15.00
                [clientcode] => 15
                [udf15] => null
                [udf14] => null
                [signature] => 149ecd1f4f9aa4a2bcd27987a548496ed74b822a5d1fd327639595ebbe7fc6d49d2cde124cbef082ee1de88e10b1f6cf97831985e0124fa69d646babe4b87756
                [udf13] => null
                [udf12] => null
                [udf11] => null
                [amt] => 1000.00
                [udf10] => null
                [merchant_id] => 197
                [mer_txn] => 16327275996151722f0d444
                [f_code] => F
                [bank_txn] => 11000000153497827
                [udf9] => null
                [ipg_txn_id] => 11000000153497
                [bank_name] => Hdfc Bank
                [prod] => NSE
                [mmp_txn] => 11000000153497
                [udf5] => null
                [udf6] => null
                [udf3] => 8526737799
                [udf4] => null
                [udf1] => Pavithra
                [udf2] => pavithra@vikashinfosolutions.com
                [discriminator] => CC
                [auth_code] => null
                [desc] => FAILED
            )*/
            $updateData = array("issuing_bank"=> $_POST['bank_name'],
            					"mode"        => $_POST['discriminator'],
            					"cardnum"	  => $_POST['CardNumber'] ,
            					"mihpayid"    => $_POST['mmp_txn'],
            					"remark"      => $_POST['desc'],
            					"field9"      => $_POST['desc'], // remark
            					"bank_ref_num"=> $_POST['bank_txn'] ,
            					"txnid"       => $_POST['mer_txn'],
            					"amount"      => $_POST['amt']
            				  ); 
        	if($paidThrough == 'Web'){
        	    if($_POST['f_code'] == 'Ok')
    			{ 
    				$this->payment_success($updateData,2);
    			}
    			else if($_POST['f_code'] == 'C')
    			{
    			    $this->payment_cancel($updateData,2);
    			}
    			else if($_POST['f_code'] == 'F')
    			{ 
    			    $this->payment_failure($updateData,2);			
    			}
    			else
    			{ 
    				$scheme_failure = array("errMsg" => 'Security Error. Illegal access detected');
    				$this->session->set_flashdata($scheme_failure);
    				redirect("/paymt");
    			}
        	}
        	else if($paidThrough == 'Mob'){
        	    if($_POST['f_code'] == 'Ok')
    			{  
    			    $this->successMURL($updateData,5); // postData and gateway
    			}
    			else if($_POST['f_code'] == 'C')
    			{ 
    		        $this->cancelMURL($updateData,5); // postData and gateway
    			}
    			else if($_POST['f_code'] == 'F')
    			{ 
    				$this->failureMURL($updateData,5);  // postData and gateway
    			}
        	}
        } else {
            echo "Invalid Signature";
        }
    }
	function submitToTechProcess($payData,$type,$payFor)
	{    
		$mrctCode =$payData['m_code'];  
        $key = $payData['key'];   
	    $iv = $payData['iv'];  
	   /* $mrctCode = $this->payment_gateway[2]['m_code'];  
        $key = $this->payment_gateway[2]['key'];   
	    $iv = $this->payment_gateway[2]['param_1'];  */
	    //$ClientMetaData = $payData['productinfo'].' - param1'.$payData['udf1'].' - param2'.$payData['udf2'].' - param3'.$payData['udf3'].' - param4'.$payData['udf4'];
	    $ClientMetaData=$payData['phone'];
	    //$reqType = ($type == 'web' ? 'T': $payData['pg'] == 'NB' ? 'T' : 'TRC' );
	    $reqType = 'T';
	    $currency = 'INR';
		$rMblURL = ($payFor == "G" ? "gPaytechProMblResponseURL" : "techProcessMobileResponseURL");
		$rwebURL = ($payFor == "G" ? "gPaytechProWebResponseURL" : "techProcessResponseURL");
		$returnURL = ($type == 'web' ? site_url('paymt/'.$rwebURL) : site_url('paymt/'.$rMblURL.'?'.$payData['id_branch'].'&'.$payData['id_pg'])); 		
	    $ShoppingCartDetails = 'FIRST_'.$payData['amount'].'_0.0';
	    $txnDate  = date('d-m-Y'); 
	    $locatorURL = "https://www.tpsl-india.in/PaymentGateway/TransactionDetailsNew.wsdl";  // LIVE 
	    $timeOut = '60000'; 
	    $bankCode = '1180'; // for TRC only
        $transactionRequestBean = new TransactionRequestBean();
        //Set all values here
        $transactionRequestBean->setMerchantCode($mrctCode);
        $transactionRequestBean->setITC($ClientMetaData);
        $transactionRequestBean->setRequestType($reqType);
        $transactionRequestBean->setCurrencyCode($currency);
        $transactionRequestBean->setReturnURL($returnURL);
        $transactionRequestBean->setShoppingCartDetails($ShoppingCartDetails); // conditional
        $transactionRequestBean->setTxnDate($txnDate); 
        $transactionRequestBean->setKey($key);
        $transactionRequestBean->setIv($iv);
        $transactionRequestBean->setWebServiceLocator($locatorURL);
        $transactionRequestBean->setTimeOut($timeOut);
        //$transactionRequestBean->setMobileNumber($payData['phone']); //mobile registerd with bank
        $transactionRequestBean->setCustomerName($payData['firstname']);
        $transactionRequestBean->setMerchantTxnRefNumber($payData['txnid']);
        $transactionRequestBean->setAmount($payData['amount']);
        $transactionRequestBean->setCustId($payData['phone']); // unique number
        if($reqType == 'TRC'){
    	    $transactionRequestBean->setCardName($payData['ccname']);
            $transactionRequestBean->setCardNo($payData['ccnum']);
            $transactionRequestBean->setCardCVV($payData['ccvv']);
            $transactionRequestBean->setCardExpMM($payData['ccexpmon']);
            $transactionRequestBean->setCardExpYY($payData['ccexpyr']);
            $transactionRequestBean->setBankCode($bankCode);
    	}
        //$transactionRequestBean->setBankCode($payData['bankcode']);
        //$transactionRequestBean->setCardId($val['cardID']); 
        //$transactionRequestBean->setMMID($val['mmid']);
        //$transactionRequestBean->setOTP($val['otp']);
        //$transactionRequestBean->setAccountNo($val['tpvAccntNo']); 
       // $url = $transactionRequestBean->getTransactionToken(); 
        $responseDetails = $transactionRequestBean->getTransactionToken();
        $responseDetails = (array)$responseDetails;
        $response = $responseDetails[0]; 
      /*  echo "<pre>";
        print_r($transactionRequestBean);
        print_r($response);exit; */
        if(is_string($response) && preg_match('/^msg=/',$response)){
            $outputStr = str_replace('msg=', '', $response);
            $outputArr = explode('&', $outputStr);
            $str = $outputArr[0];
            $transactionResponseBean = new TransactionResponseBean();
            $transactionResponseBean->setResponsePayload($str);
            $transactionResponseBean->setKey($key);
            $transactionResponseBean->setIv($iv);
            $response = $transactionResponseBean->getResponsePayload();
            if($type == 'web'){
                $msg = array("errMsg" => 'Unable to proceed your request please try again later or contact admin...');
			    $this->session->set_flashdata($msg);
			    redirect("/paymt");
            }else{
                $updateData = array(   
                		"remark"            => $response,
                	    "payment_status"    => $this->payment_status['failure']
                	 //status - 0 (pending), will change to 1 after approved at backend
                	);
        		if($payFor == "SS"){
            		$payment = $this->payment_modal->updateGatewayResponse($updateData,$payData['txnid']);
            		redirect("/paymt/failureMURL");
        		}
        		elseif($payFor == "G"){
        		    $payment = $this->payment_modal->updateGcardPay($updateData,$payData['txnid']);
        		    redirect("/paymt/gPayResponseMURL/f");
        		}
            }
        }elseif(is_string($response) && preg_match('/^txn_status=/',$response)){
            if($type == 'web'){
                $msg = array("errMsg" => 'Unable to proceed your request please try again later or contact admin...');
			    $this->session->set_flashdata($msg);
			    redirect("/paymt");
            }else{ 
                $updateData = array(   
                		"remark"            => $response,
                	    "payment_status"    => $this->payment_status['failure']
                	 //status - 0 (pending), will change to 1 after approved at backend
                	);
        		if($payFor == "SS"){
            		$payment = $this->payment_modal->updateGatewayResponse($updateData,$payData['txnid']);
            		redirect("/paymt/failureMURL");
        		}
        		elseif($payFor == "G"){
        		    $payment = $this->payment_modal->updateGcardPay($updateData,$payData['txnid']);
        		    redirect("/paymt/gPayResponseMURL/f");
        		}
            }
    	}
    	$parseURL = parse_url($response);
        if($parseURL['path'] == "/PaymentGateway/txnreq.pg" || $parseURL['path'] == "/PaymentGateway/txnreqcardver2.pg"){
            echo "<script>window.location = '".$response."'</script>";   
        }else{
            if($type == 'web'){
                $msg = array("errMsg" => 'Unable to proceed your request please try again later or contact admin...');
			    $this->session->set_flashdata($msg);
			    redirect("/paymt");
            }else{
                $updateData = array(   
                		"remark"            => $response,
                	    "payment_status"    => $this->payment_status['failure']
                	 //status - 0 (pending), will change to 1 after approved at backend
                	);
        		if($payFor == "SS"){
            		$payment = $this->payment_modal->updateGatewayResponse($updateData,$payData['txnid']);
            		redirect("/paymt/failureMURL");
        		}
        		elseif($payFor == "G"){
        		    $payment = $this->payment_modal->updateGcardPay($updateData,$payData['txnid']);
        		    redirect("/paymt/gPayResponseMURL/f");
        		}
            } 
        }
    }
    function techProcessResponseURL($payData = "")
    {
	   /* $mrctCode = $this->payment_gateway[2]['m_code'];  
        $key = $this->payment_gateway[2]['key'];   
	    $iv = $this->payment_gateway[2]['param_1']; */
 $paymentgateway = $this->payment_modal->getBranchGatewayData($this->session->userdata('id_branch'),$this->session->userdata('id_pg'));
	  $iv=$paymentgateway['param_4'];
	  $key=$paymentgateway['param_1'];
	  $mrctCode=$paymentgateway['param_3'];
        $response = $_POST;
        if(is_array($response)){
            $str = $response['msg'];
        }else if(is_string($response) && strstr($response, 'msg=')){
            $outputStr = str_replace('msg=', '', $response);
            $outputArr = explode('&', $outputStr);
            $str = $outputArr[0];
        }else {
            $str = $response;
        }
        $transactionResponseBean = new TransactionResponseBean();
        $transactionResponseBean->setResponsePayload($str);
        $transactionResponseBean->setKey($key);
        $transactionResponseBean->setIv($iv);
        $response = $transactionResponseBean->getResponsePayload();
        /*echo "<pre>";
        print_r($response);
        echo "<br><br><br><br>";  exit;*/
        /*txn_status=0300|txn_msg=success|txn_err_msg=NA|clnt_txn_ref=15420175985be9523e2b822|tpsl_bank_cd=470|tpsl_txn_id=643573574|txn_amt=10.00|
        clnt_rqst_meta={custname:Pavithra}|tpsl_txn_time=12-11-2018 15:43:36|tpsl_rfnd_id=NA|bal_amt=NA|rqst_token=f5b01fa2-59b9-4fd6-9fa4-590341178f14|
        hash=3538400ed021fc95755b03f3454ea9385b5f03e2*/
        $transData = array(); 
		$payData = explode("|", $response); 
        $status_code = "";
        $status_msg = "";
        $err_msg = "";
        $txn_id = "";
        $payu_id = "";
        $mode = "";
        foreach($payData as $pay){ 
        	$r = explode("=", $pay); 
        	if($r[0] === "txn_status") $status_code = $r[1];
        	if($r[0] === "txn_msg") $status_msg = $r[1];
        	if($r[0] === "txn_err_msg") $err_msg = $r[1];
        	if($r[0] === "clnt_txn_ref") $txn_id = $r[1]; 
        	if($r[0] === "tpsl_txn_id") $payu_id = $r[1];
        	if($r[0] === "tpsl_bank_cd") $mode = $r[1];
        }
        $mode = ($mode == '137'? 'RuPay':($mode == '127' || $mode == '82'? 'CC':($mode == '128' || $mode == '118'? 'DC': ($mode == 'NA' ? 'NA':'NB') ) ) );
        $updateData = array(
        	//"bank_name"			=> (isset($payData['issuing_bank']) ? $payData['issuing_bank'] : NULL),
        		"payment_mode"        => (isset($mode) ?$mode : NULL),
        	//	"card_no"			=> (isset($payData['mode']) && ($payData['mode'] == 'CC' || $payData['mode'] == 'DC') ? $payData['cardnum'] :NULL ),
        		"payu_id"           => (isset($payu_id) ? $payu_id : NULL),
        	//	"payment_ref_number"=> (isset($payData['bank_ref_num']) ? $payData['bank_ref_num'] : NULL ),
        		"remark"            => 'Bank Code :'.$mode.'.'.$status_msg.' - '.$status_code.' - '.($err_msg != 'NA' ? $err_msg : ''),
        	    "payment_status"    => ($status_code == '0300' ? ( $this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success']:$this->payment_status['awaiting']):($status_code == '0392'?$this->payment_status['cancel']:$this->payment_status['failure']))
        	 //status - 0 (pending), will change to 1 after approved at backend
        	); 
		$payment = $this->payment_modal->updateGatewayResponse($updateData,$txn_id); 
	    $serviceID = 7;
		$service = $this->services_modal->checkService($serviceID);
		$payData = $this->payment_modal->get_paymenMailData(isset($payment['id_payment'])?$payment['id_payment']:'');
		if($payment['status'] == true)
		{
			if($service['sms'] == 1)
			{
				$id=$payment['id_payment'];
				$data =$this->services_modal->get_SMS_data($serviceID,$id);
				
				$mobile =$data['mobile'];
				$message = $data['message']; 
				if($this->config->item('sms_gateway') == '1'){
	    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
	    		}
	    		elseif($this->config->item('sms_gateway') == '2'){
	    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
	    		}
	    		elseif($this->config->item('sms_gateway') == '3'){
					 $this->sms_model->sendSMS_SpearUC($mobile,$message,'',$service['dlt_te_id']);
				}
				elseif($this->config->item('sms_gateway') == '4'){
					$this->sms_model->sendSMS_Asterixt($mobile,$message,'',$service['dlt_te_id']);
				}
				elseif($this->config->item('sms_gateway') == '5'){
					$this->sms_model->sendSMS_Qikberry($mobile,$message,'',$service['dlt_te_id']);
				}
			}
			if($service['serv_whatsapp'] == 1)
			{
            	$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
                $this->sms_model->send_whatsApp_message($mobile,$smsData);
            }
			if($service['email'] == 1 && isset($payData[0]['email']) && $payData[0]['email'] != '')
			{
				$to = $payData[0]['email'];
				$subject = "Reg - ".$this->comp['company_name']." payment for the purchase plan";
				$data['payData'] = $payData[0];
				$data['type'] = 3;
				$data['company_details'] = $this->comp;
				$message = $this->load->view('include/emailPayment',$data,true);
				$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
			}
			if($status_code == '0300'){
				$pay = $this->payment_modal->getWalletPaymentContent($txn_id);
		          if($pay['redeemed_amount'] > 0){ 
					$transData = array('mobile' 			=> $pay['mobile'],
										 'actual_trans_amt' => $pay['actual_trans_amt'],
										 'available_points'	=> ($pay['isAvail'] == 0 ?0:$pay['available_points']),
										 'isAvail'			=> ($pay['isAvail'] == 0 ?0:1),
										 'redeemed_amount'	=> $pay['redeemed_amount'],
										 //'is_point_credited'=> $pay['is_point_credited'],
										 'id_payment'       => $pay['id_payment'],
										 'txnid'            => $txn_id.'- D',
										 'branch'           => $pay['branch'],
				    					 'walletIntegration'=> $pay['walletIntegration'],
				    					 'wallet_balance_type' => $pay['wallet_balance_type'],
				    					 'wallet_points' => $pay['wallet_points'],
				    					 'wallet_amt_per_points' => $pay['wallet_amt_per_points'],
										); 
		    		if(!empty($transData)){
		    		    $this->insertWalletTrans($transData); 
		    		}
		          }
			    if( $this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2){ 
    		        if($payData[0]['allow_referral'] == 1){
    				    $ref_data	=	$this->payment_modal->get_refdata($payData[0]['id_scheme_account']);
    					$ischkref	=	$this->payment_modal->get_ischkrefamtadd($payData[0]['id_scheme_account']);	
    			 		if($ref_data['ref_benifitadd_ins_type'] == 1 && $ref_data['referal_code'] != '' && ($ref_data['ref_benifitadd_ins'] == $ref_data['paid_installments']) && $ischkref == TRUE){	
    						$this->insert_referral_data($ref_data['id_scheme_account'],$ref_data);
    					}else if($ref_data['ref_benifitadd_ins_type'] == 0 && $ref_data['referal_code'] != '' && $ischkref == TRUE){
    						$this->insert_referral_data($ref_data['id_scheme_account'],$ref_data);
    					}
    			 	}
						$payIds = $this->payment_modal->getPayIds($txn_id);
					 //print_r($payIds);exit;
					if(sizeof($payIds) > 0)
					{
						foreach ($payIds as $pay)
						{
							if($pay['schemeacc_no_set'] == 0  || $pay['schemeacc_no_set']==3)
							{
								if($pay['scheme_acc_number'] == '' ||  $pay['scheme_acc_number'] == null)
								{
								    $ac_group_code = NULL;
									// Lucky draw
									if($pay['is_lucky_draw'] == 1 ){ // Based on scheme settings 
										// Update Group code in scheme_account table 
										$updCode = $this->payment_modal->updateGroupCode($pay); 
										$ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
									}
									// $this->db->query('LOCK TABLES scheme_account WRITE');
									$scheme_acc_number = $this->payment_modal->account_number_generator($pay['id_scheme'],$pay['branch'],$ac_group_code); 
									if($scheme_acc_number != NULL && $scheme_acc_number !="Not Allocated"){
										$schData['scheme_acc_number'] = $scheme_acc_number;
									}else{
										// $this->db->query('UNLOCK TABLES');
									}
									if($pay['gent_clientid'] ==1 && $scheme_acc_number != NULL){  
											$cliData = array(
															 "cliID_short_code"	=> $this->config->item('cliIDcode'),
															 "sync_scheme_code"	=> $pay['sync_scheme_code'],
															 "ac_no"			=> $scheme_acc_number
															);											
											$schData['ref_no'] = $this->payment_modal->generateClientID($cliData);
										}
											if($pay['receipt_no_set'] == 1 && $this->config->item('integrationType') == 2){ 	//upd client id & acc no cus reg table//
											$cliData = array(
															 "cliID_short_code"	=> $this->config->item('cliIDcode'),
															 "sync_scheme_code"	=> $pay['sync_scheme_code'],
															 "ac_no"			=> $scheme_acc_number
															);											
											$schDatacus['ref_no'] = $this->payment_modal->generateClientID($cliData);
											$schDatacus['scheme_acc_number'] = $scheme_acc_number;
										//	$schDatacus['group_code'] =$pay['group_code'];
										}
									$updSchAc = $this->payment_modal->update_account($schData,$pay['id_scheme_account']);
									// $this->db->query('UNLOCK TABLES');
									 $updcusreg = $this->payment_modal->update_cusreg($schDatacus,$pay['id_scheme_account']); //acc no upd to cus reg tab//
									 $updtrans = $this->payment_modal->update_trans($schDatacus,$pay['id_scheme_account']); //Client Id upd to trans tab//
							//	print_r($this->db->last_query());exit;	
								}
							}
    						// Generate receipt number
							if($pay['receipt_no_set'] == 1 && ($this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2) )
							{ 
								// $this->db->query('LOCK TABLES payment WRITE');
								$receipt['receipt_no'] = $this->payment_modal->generate_receipt_no($pay['id_scheme'],$pay['branch']);
								$payment['status'] = $this->payment_modal->update_receipt($pay['id_payment'],$receipt);
								// $this->db->query('UNLOCK TABLES');
							}
							 //Update First Payment Amount In Scheme Account
							 $approval_type = $this->config->item('auto_pay_approval');
    						 if(($approval_type == 1 || $approval_type == 2 || $approval_type == 3) && ($pay['firstPayamt_maxpayable']==1 || $pay['firstPayamt_as_payamt']==1) && ($pay['firstPayment_amt'] == '' ||  $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0))
    						 {
    							if($pay['flexible_sch_type'] == 4 && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "") ){ // Fix First payable as weight
									$fixPayable = array('firstPayment_wgt'  =>  $pay['metal_weight'] );
								}else{
									$fixPayable = array('firstPayment_amt'  =>  $pay['payment_amount'] );
								}
								$status = $this->payment_modal->update_account($fixPayable,$pay['id_scheme_account']);	 
    						 }
							if( $this->config->item('auto_pay_approval') == 2 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3))
							{ 	 
								/*if($pay['edit_custom_entry_date']==1)
								{
										$receipt['custom_entry_date']=$pay['custom_entry_date'];
										$payment['status'] = $this->payment_modal->update_receipt($pay['id_payment'],$receipt);
								}*/
								//$this->mobileapi_model->insert_common_data($pay['id_payment']);  
								  if($this->config->item('integrationType') == 1){
								        $this->insert_common_data_jil($pay['id_payment']);
								    }else if($this->config->item('integrationType') == 2){
								        $this->mobileapi_model->insert_common_data($pay['id_payment']);
								    }  		            
    		        		}
						}
					}
    		    } 
			    $scheme_success = array("successMsg" => 'Payment '.$status_msg.'. Thanks for your payment with '.$this->comp['company_name'].'');
			}else{
			    $scheme_success = array("errMsg" => 'Payment '.$status_msg);
			}
			$this->session->set_flashdata($scheme_success);
			redirect("/paymt/payment_history");
		}
		else
		{
			$msg = array("errMsg" => 'Error in updating the database.Please contact administrator at your earliest convenience.');
			$this->session->set_flashdata($msg);
			redirect("/paymt");
		}
    }
	function techProcessMobileResponseURL($payData = ""){
	    echo "<h1 style='text-align:center;font-size:75px'>Please wait</h1>";
		$serv_model= self::SERV_MODEL; 
		$data=explode('&',$_SERVER['QUERY_STRING']);
		$id_branch=$data[0];
		$id_pg=$data[1];
		$paymentgateway = $this->payment_modal->getBranchGatewayData($id_branch,$id_pg);
		$iv=$paymentgateway['param_4'];	
		$key=$paymentgateway['param_1'];
		$mrctCode=$paymentgateway['param_3'];
	   /* $mrctCode = $this->payment_gateway[2]['m_code'];  
        $key = $this->payment_gateway[2]['key'];   
	    $iv = $this->payment_gateway[2]['param_1']; */  
        $response = $_POST;
        if(is_array($response)){
            $str = $response['msg'];
        }else if(is_string($response) && strstr($response, 'msg=')){
            $outputStr = str_replace('msg=', '', $response);
            $outputArr = explode('&', $outputStr);
            $str = $outputArr[0];
        }else {
            $str = $response;
        }
        $transactionResponseBean = new TransactionResponseBean();
        $transactionResponseBean->setResponsePayload($str);
        $transactionResponseBean->setKey($key);
        $transactionResponseBean->setIv($iv);
        $response = $transactionResponseBean->getResponsePayload(); 
        /* SAMPLE :- 
            TRC :-  txn_status=0300|txn_msg=success|txn_err_msg=NA|clnt_txn_ref=TPSL399698281|tpsl_bank_cd=10140|tpsl_txn_id=117203564|txn_amt=2.00
    |clnt_rqst_meta={itc:NIC~TXN0001~122333~rt14154~8 mar 2014~Payment~forpayment}{email:sunil.sahu@techproces.co.in}{mob:9856987456}|
    tpsl_txn_time=12-11-2014 16:40:54|tpsl_rfnd_id=NA|bal_amt=NA|rqst_token=6308eff9-87db-40f1-8a17-0097c4264818|card_id=4077|
    BankTransactionID=114111201389206|alias_name=VISA_DBT_****9847|hash=d9cf87c453fbc49f32536db6097d9e80cc7d3397 
            T :-    txn_status=0399|txn_msg=failure|txn_err_msg=Transaction Cancelled : ERROR CODE TPPGE152|clnt_txn_ref=15423712435beeb7abaa834|tpsl_bank_cd=NA|tpsl_txn_id=E9472341|txn_amt=1.00|clnt_rqst_meta={custname:Pavithra}|tpsl_txn_time=16-11-2018 17:57:37|tpsl_rfnd_id=NA|bal_amt=NA|rqst_token=1817cb57-7dee-45da-b71d-477ca2ca7f87|hash=20574cff7f4ad0b1597d4088c95e04c6527b33ad
        */
        /*echo "<pre>";
        print_r($response);exit;*/
        $transData = array(); 
		$payData = explode("|", $response); 
        $status_code = "";
        $status_msg = "";
        $err_msg = "";
        $txn_id = "";
        $payu_id = "";
        $card_id = NULL;
        $mode = "";
        foreach($payData as $pay){ 
        	$r = explode("=", $pay); 
        	if($r[0] === "txn_status") $status_code = $r[1];
        	if($r[0] === "txn_msg") $status_msg = $r[1];
        	if($r[0] === "txn_err_msg") $err_msg = $r[1];
        	if($r[0] === "clnt_txn_ref") $txn_id = $r[1]; 
        	if($r[0] === "tpsl_txn_id") $payu_id = $r[1];
        	if($r[0] === "card_id") $card_id = $r[1];
        	if($r[0] === "tpsl_bank_cd") $mode = $r[1];
        }
        $mode = ($mode == '137'? 'RuPay':($mode == '127' || $mode == '82'? 'CC':($mode == '128' || $mode == '118'? 'DC': ($mode == 'NA' ? 'NA':'NB') ) ) );
        $updateData = array(
                //"tp_card_id"        => $card_id,
        	//"bank_name"			=> (isset($payData['issuing_bank']) ? $payData['issuing_bank'] : NULL),
        		"payment_mode"        => (isset($mode) ?$mode : NULL),
        	//	"card_no"			=> (isset($payData['mode']) && ($payData['mode'] == 'CC' || $payData['mode'] == 'DC') ? $payData['cardnum'] :NULL ),
        		"payu_id"           => (isset($payu_id) ? $payu_id : NULL),
        	//	"payment_ref_number"=> (isset($payData['bank_ref_num']) ? $payData['bank_ref_num'] : NULL ),
        		"remark"            => 'Bank Code :'.$mode.'.'.$status_msg.' - '.$status_code.' - '.($err_msg != 'NA' ? $err_msg : ''),
        	    "payment_status"    => ($status_code == '0300' ? ( $this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success']:$this->payment_status['awaiting']):($status_code == '0392'?$this->payment_status['cancel']:$this->payment_status['failure']))
        	 //status - 0 (pending), will change to 1 after approved at backend
        	);
		$payment = $this->payment_modal->updateGatewayResponse($updateData,$txn_id);
	    $serviceID = 7;
		$service = $this->services_modal->checkService($serviceID);
		$payData = $this->payment_modal->get_paymenMailData(isset($payment['id_payment'])?$payment['id_payment']:'');
		$redirectURL = ($status_code == '0300' ? "successMURL":($status_code == '0392'?"cancelMURL":'failureMURL'));
		if($payment['status'] == true)
		{
		    if($status_code == '0300'){
		    	$pay = $this->payment_modal->getWalletPaymentContent($txn_id);
		          if($pay['redeemed_amount'] > 0){ 
					$transData = array('mobile' 			=> $pay['mobile'],
										 'actual_trans_amt' => $pay['actual_trans_amt'],
										 'available_points'	=> ($pay['isAvail'] == 0 ?0:$pay['available_points']),
										 'isAvail'			=> ($pay['isAvail'] == 0 ?0:1),
										 'redeemed_amount'	=> $pay['redeemed_amount'],
										 //'is_point_credited'=> $pay['is_point_credited'],
										 'id_payment'       => $pay['id_payment'],
										 'txnid'            => $txn_id.'- D',
										 'branch'           => $pay['branch'],
				    					 'walletIntegration'=> $pay['walletIntegration'],
				    					 'wallet_balance_type' => $pay['wallet_balance_type'],
				    					 'wallet_points' => $pay['wallet_points'],
				    					 'wallet_amt_per_points' => $pay['wallet_amt_per_points'],
										); 
		    		if(!empty($transData)){
		    		    $this->insertWalletTrans($transData); 
		    		}
		          }
		        if( $this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2){
    		        if($payData[0]['allow_referral'] == 1){
    				    $ref_data	=	$this->payment_modal->get_refdata($payData[0]['id_scheme_account']);
    					$ischkref	=	$this->payment_modal->get_ischkrefamtadd($payData[0]['id_scheme_account']);	
    			 		if($ref_data['ref_benifitadd_ins_type'] == 1 && $ref_data['referal_code'] != '' && ($ref_data['ref_benifitadd_ins'] == $ref_data['paid_installments']) && $ischkref == TRUE){	
    						$this->insert_referral_data($ref_data['id_scheme_account'],$ref_data);
    					}else if($ref_data['ref_benifitadd_ins_type'] == 0 && $ref_data['referal_code'] != '' && $ischkref == TRUE){
    						$this->insert_referral_data($ref_data['id_scheme_account'],$ref_data);
    					}
    			 	}
    			 	$payIds = $this->payment_modal->getPayIds($txn_id);
					if(sizeof($payIds) > 0)
					{
						foreach ($payIds as $pay)
						{
							// Generate account  number  based on one more settings Integ Auto//hh
							if($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set']==3)
							{
								if($pay['scheme_acc_number'] == '' ||  $pay['scheme_acc_number'] == null)
								{
								    $ac_group_code = NULL;
									// Lucky draw
									if($pay['is_lucky_draw'] == 1 ){ // Based on scheme settings 
										// Update Group code in scheme_account table 
										$updCode = $this->payment_modal->updateGroupCode($pay);
										$ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
									}
									// $this->db->query('LOCK TABLES scheme_account WRITE');
									$scheme_acc_number = $this->payment_modal->account_number_generator($pay['id_scheme'],$pay['branch'],$ac_group_code);
									if($scheme_acc_number != NULL && $scheme_acc_number !="Not Allocated"){
										$schData['scheme_acc_number'] = $scheme_acc_number;
									}else{
										// $this->db->query('UNLOCK TABLES');
									}
									if($pay['gent_clientid'] ==1 && $scheme_acc_number != NULL){   
											$cliData = array(
															 "cliID_short_code"	=> $this->config->item('cliIDcode'),
															 "sync_scheme_code"	=> $pay['sync_scheme_code'],
															 "ac_no"			=> $scheme_acc_number
															);											
											$schData['ref_no'] = $this->payment_modal->generateClientID($cliData);
										}
											if($pay['receipt_no_set'] == 1 && $this->config->item('integrationType') == 2){ 	//upd client id & acc no cus reg table//
											$cliData = array(
															 "cliID_short_code"	=> $this->config->item('cliIDcode'),
															 "sync_scheme_code"	=> $pay['sync_scheme_code'],
															 "ac_no"			=> $scheme_acc_number
															);											
											$schDatacus['ref_no'] = $this->payment_modal->generateClientID($cliData);
											$schDatacus['scheme_acc_number'] = $scheme_acc_number;
										//	$schDatacus['group_code'] =$pay['group_code'];
										}
									$updSchAc = $this->payment_modal->update_account($schData,$pay['id_scheme_account']);
									// $this->db->query('UNLOCK TABLES');
									 $updcusreg = $this->payment_modal->update_cusreg($schDatacus,$pay['id_scheme_account']); //acc no upd to cus reg tab//hh
									 $updtrans = $this->payment_modal->update_trans($schDatacus,$pay['id_scheme_account']); //Client Id upd to trans tab//
								}
							}
							// Generate receipt number  Based on the new settings Integ Auto //hh
							if($pay['receipt_no_set'] == 1 || $pay['receipt_no_set'] == 3 && ($this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2) )
								{  
									// $this->db->query('LOCK TABLES payment WRITE');
									$data['receipt_no'] = $this->payment_modal->generate_receipt_no($pay['id_scheme'],$pay['branch']);
									$payment['status'] = $this->payment_modal->update_receipt($pay['id_payment'],$data);
									// $this->db->query('UNLOCK TABLES');
								}
								//Update First Payment Amount In Scheme Account
								$approval_type = $this->config->item('auto_pay_approval');
								if(($approval_type == 1 || $approval_type == 2 || $approval_type == 3) && ($pay['firstPayamt_maxpayable']==1 || $pay['firstPayamt_as_payamt']==1) && ($pay['firstPayment_amt'] == '' ||  $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0))
        						 {
        							if($pay['flexible_sch_type'] == 4 && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "") ){ // Fix First payable as weight
    									$fixPayable = array('firstPayment_wgt'  =>  $pay['metal_weight'] );
    								}else{
    									$fixPayable = array('firstPayment_amt'  =>  $pay['payment_amount'] );
    								}
    								$status = $this->payment_modal->update_account($fixPayable,$pay['id_scheme_account']);	 
        						 }
								if( $this->config->item('auto_pay_approval') == 2 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3))
								{	
									/*if($pay['edit_custom_entry_date']==1)
									{
											$receipt['custom_entry_date']=$pay['custom_entry_date'];
											$payment['status'] = $this->payment_modal->update_receipt($pay['id_payment'],$receipt);
									}*/
									//$this->mobileapi_model->insert_common_data($pay['id_payment']);  
								  if($this->config->item('integrationType') == 1){
								        $this->insert_common_data_jil($pay['id_payment']);
								    }else if($this->config->item('integrationType') == 2){
								        $this->mobileapi_model->insert_common_data($pay['id_payment']);
								    }
								}						
						}
					}
    		    }    
		    }
			if($service['sms'] == 1)
			{
				$id=$payment['id_payment'];
				$data =$this->$serv_model->get_SMS_data($serviceID,$id);
				$mobile =$data['mobile'];
				$message = $data['message'];
				if($this->config->item('sms_gateway') == '1'){
	    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
	    		}
	    		elseif($this->config->item('sms_gateway') == '2'){
	    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
	    		}
	    		elseif($this->config->item('sms_gateway') == '3'){
					 $this->sms_model->sendSMS_SpearUC($mobile,$message,'',$service['dlt_te_id']);
				}
				elseif($this->config->item('sms_gateway') == '4'){
					$this->sms_model->sendSMS_Asterixt($mobile,$message,'',$service['dlt_te_id']);
				}
				elseif($this->config->item('sms_gateway') == '5'){
					$this->sms_model->sendSMS_Qikberry($mobile,$message,'',$service['dlt_te_id']);
				}
			}
			if($service['serv_whatsapp'] == 1)
			{
            	$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
                $this->sms_model->send_whatsApp_message($mobile,$smsData);
            }
			if($service['email'] == 1 && isset($payData[0]['email']) && $payData[0]['email'] != '')
			{
				$to = $payData[0]['email'];
				$subject = "Reg - ".$this->comp['company_name']." payment for the purchase plan";
				$data['payData'] = $payData[0];
				$data['type'] = 3;
				$data['company_details'] = $this->comp;
				$message = $this->load->view('include/emailPayment',$data,true);
				$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
			} 
		}  
    	redirect("/paymt/".$redirectURL);
    } 
	function amount_to_weight($to_pay) 
	{
		$converted_metal_wgt = $to_pay['amount']/$to_pay['metal_rate'];
		return $converted_metal_wgt;
	}
	function get_entrydate($id_branch)
	{
	    if($id_branch > 0){
            $sql = "SELECT entry_date as custom_entry_date,cs.edit_custom_entry_date FROM ret_day_closing 
    		join chit_settings cs 
    		".($id_branch!='' ?" WHERE id_branch=".$id_branch."" :'')."";
    	     $result=$this->db->query($sql);
    	     return $result->row_array();
	    }else{
	        return date("Y-m-d H:i:s");
	    }
	}	
	public function payment_failure($payData = "", $gateway = "")
    {
        if($gateway == ""){
            $payData = $_POST;
        } 
		//echo"<pre>"; print_r($payData);exit
        if(!empty($payData['txnid']) && $payData['txnid'] != NULL){
    	     $serv_model= self::SERV_MODEL;
    		 $updateData = array(
    						"bank_name"			 =>	(isset($payData['issuing_bank']) ? $payData['issuing_bank'] : NULL),
    						"payment_mode"       => (isset($payData['mode']) ? $payData['mode'] : NULL),
    						"card_no"			 => (isset($payData['mode']) && ($payData['mode'] == 'CC' || $payData['mode'] == 'DC') ? $payData['cardnum'] :NULL ),
    						"payu_id"             => (isset($payData['mihpayid']) ? $payData['mihpayid'] : NULL),
    						"remark"             => (isset($payData['field9']) ? $payData['field9'] : NULL),
    						"payment_ref_number" => (isset($payData['bank_ref_num']) ? $payData['bank_ref_num'] : NULL ),
    						"payment_status"     => $this->payment_status['failure']
    					);
    		$payment = $this->payment_modal->updateGatewayResponse($updateData,$payData['txnid']);
    		$payData = $this->payment_modal->get_paymenMailData(isset($payment['id_payment'])?$payment['id_payment']:'');
    		//$payData = $this->registration_model->get_cusData($this->session->userdata('username'));
    		$serviceID = 7;
    		$service = $this->services_modal->checkService($serviceID);
    		if($service['sms'] == 1)
    		{
    				$id=$payment['id_payment'];
    				$data =$this->$serv_model->get_SMS_data($serviceID,$id);
    				$mobile =$data['mobile'];
    				$message = $data['message'];
    				if($this->config->item('sms_gateway') == '1'){
		    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
		    		}
		    		elseif($this->config->item('sms_gateway') == '2'){
		    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
		    		}
		    		elseif($this->config->item('sms_gateway') == '3'){
    					 $this->sms_model->sendSMS_SpearUC($mobile,$message,'',$service['dlt_te_id']);
    				}
    				elseif($this->config->item('sms_gateway') == '4'){
    					$this->sms_model->sendSMS_Asterixt($mobile,$message,'',$service['dlt_te_id']);
    				}
    				elseif($this->config->item('sms_gateway') == '5'){
    					$this->sms_model->sendSMS_Qikberry($mobile,$message,'',$service['dlt_te_id']);
    				}
    		}
    		if($service['serv_whatsapp'] == 1)
			{
            	$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
                $this->sms_model->send_whatsApp_message($mobile,$smsData);
            }
    		if($service['email'] == 1 && isset($payData[0]['email']) && $payData[0]['email'] != '')
    		{
    			$to = $payData[0]['email'];
    			$subject = "Reg - ".$this->comp['company_name']." payment for the purchase plan";
    			$data['payData'] = $payData[0];
    			$data['type'] = 3;
    			$data['company_details'] = $this->comp;
    			$message = $this->load->view('include/emailPayment',$data,true);
    			$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");	
    		}
    		$scheme_failure = array("errMsg" => 'Payment failure.Please try again later');
    		$this->session->set_flashdata($scheme_failure);
    		redirect("/paymt");
        }else{
            $scheme_success = array("errMsg" => 'Payment failure.Error in updating the database.Please contact administrator for status.');
			$this->session->set_flashdata($scheme_success);
			redirect("/paymt");
        }
	}	
	public function payment_success($payData = "", $gateway = "")
    { 
        if($gateway == ""){
            $payData = $_POST;
        } 
	    $serv_model= self::SERV_MODEL; 
	    if(!empty($payData['txnid']) && $payData['txnid'] != NULL){
    	    $amount =0;
       	    $remark =''; 
            $id_transaction = $payData['txnid'];
            $transData = array();
            $pay = $this->payment_modal->getWalletPaymentContent($id_transaction);
            if($pay['redeemed_amount'] > 0){ 
    		    $transData = array('mobile' 			=> $pay['mobile'],
    								 'actual_trans_amt' => $pay['actual_trans_amt'],
    								 'available_points'	=> ($pay['isAvail'] == 0 ?0:$pay['available_points']),
    								 'isAvail'			=> ($pay['isAvail'] == 0 ?0:1),
    								 'redeemed_amount'	=> $pay['redeemed_amount'],
    								 'id_payment'       => $pay['id_payment'],
    								 'txnid'            => $id_transaction.'- D',
    								 'branch'           => $pay['branch'],
    		    					 'walletIntegration'=> $pay['walletIntegration'],
    		    					 'wallet_balance_type' => $pay['wallet_balance_type'],
    		    					 'wallet_points' => $pay['wallet_points'],
    		    					 'wallet_amt_per_points' => $pay['wallet_amt_per_points'],
    								); 
        		if(!empty($transData)){
        		    $this->insertWalletTrans($transData); 
        	    }
            }
			$approval_type = $this->config->item('auto_pay_approval');
    		$updateData = array(
    					//	"bank_name"			 =>	(isset($payData['issuing_bank']) ? $payData['issuing_bank'] : NULL),
    						"payment_mode"       => (isset($payData['mode']) ? $payData['mode'] : NULL),
    						"card_no"			 => (isset($payData['mode']) && ($payData['mode'] == 'CC' || $payData['mode'] == 'DC') ? $payData['cardnum'] :NULL ),
    						"payu_id"             => (isset($payData['mihpayid']) ? $payData['mihpayid'] : NULL),
    						"payment_ref_number" => (isset($payData['bank_ref_num']) ? $payData['bank_ref_num'] : NULL ),
    					//	"remark"             => $payData['field9']." - pay_success",
    						"payment_status"     => ( $approval_type == 1 ||  $approval_type == 2 ? $this->payment_status['success']:$this->payment_status['awaiting']) //status - 0 (pending), will change to 1 after approved at backend
    					); 
    		$payment = $this->payment_modal->updateGatewayResponse($updateData,$id_transaction);
    	    $serviceID = 7;
    		$service = $this->services_modal->checkService($serviceID);
    		if($payment['status'] == true)
    		{
    			$payDatas = $this->payment_modal->get_paymenMailData(isset($payment['id_payment'])?$payment['id_payment']:'');
    		    if( $approval_type == 1 ||  $approval_type == 2 || $approval_type == 3){
    			 	$payIds = $this->payment_modal->getPayIds($id_transaction);
    				if(sizeof($payIds) > 0)
    				{
    					foreach ($payIds as $pay)
    					{	
    						$updData = array("last_payment_on" => NULL);
							$this->payment_modal->updData($updData,'id_customer',$payIds[0]['id_customer'],'customer'); 
            				if($this->config->item("integrationType") == 5){ // Generate A/C No and Receipt No
            					$this->generateAcNoOrReceiptNo($pay);
            				}
            		        if($pay['allow_referral'] == 1){
            				    $ref_data	=	$this->payment_modal->get_refdata($pay['id_scheme_account']);
            					$ischkref	=	$this->payment_modal->get_ischkrefamtadd($pay['id_scheme_account']);	
            			 		if($ref_data['ref_benifitadd_ins_type'] == 1 && $ref_data['referal_code'] != '' && ($ref_data['ref_benifitadd_ins'] == $ref_data['paid_installments']) && $ischkref == TRUE){	
            						$this->insert_referral_data($ref_data['id_scheme_account'],$ref_data);
            					}else if($ref_data['ref_benifitadd_ins_type'] == 0 && $ref_data['referal_code'] != '' && $ischkref == TRUE){
            						$this->insert_referral_data($ref_data['id_scheme_account'],$ref_data);
            					}
            			 	}
            			 	if($pay['agent_refferal'] == 1 && $pay['agent_credit_type'] == 1)
            			 	{
            			 	    $agent_refral = $this->payment_modal->get_agent_refdata($pay['id_scheme_account'],$pay['id_payment']);
            			 	    $agent_benefits = $this->payment_modal->get_agentBenefits($agent_refral['id_scheme'],$agent_refral['payment_amount'],$agent_refral['paid_installments']);
            			 	    if($agent_benefits['cash_point'] > 0)
            			 	    {
                			 	    $insert_array = array("ly_trans_type" => 3,
                			 	                    "cus_loyal_cus_id" => $agent_refral['cus_loyal_cus_id'],
                			 	                    "id_agent"   => $agent_refral['id_agent'],
                			 	                    "id_scheme_account" => $pay['id_scheme_account'],
                			 	                    "id_payment"  => $pay['id_payment'],
                			 	                    "cash_point" => $agent_benefits['cash_point'],
                			 	                    "status"    => 1,
                			 	                    "tr_cus_type" => 4,
                			 	                    "cr_based_on" => 3,
                			 	                    "unsettled_cash_pts" => $agent_benefits['cash_point'],
                			 	                    "date_add"       => date('Y-m-d H:i:s')
                			 	                            );
                			 	     $status = $this->payment_modal->insert_agent_transaction($insert_array);
                			 	     $this->payment_modal->updateAgentCash($agent_refral['id_agent'],$agent_benefits['cash_point']);
                			 	     $ag_data = array("id_agent"   => $agent_refral['id_agent']);
                			 	     $this->payment_modal->updData($ag_data,'id_payment',$pay['id_payment'],'payment');
            			 	    }
            			 	}
                			if($pay['one_time_premium'] == 1){
            					$acData = array('firstPayment_amt' => $pay['payment_amount'], 'date_upd' => date("Y-m-d H:i:s"));
            					$this->payment_modal->updData($acData,'id_scheme_account',$pay['id_scheme_account'],'scheme_account');
            				}
    					    // Generate receipt number
    						if($pay['receipt_no_set'] == 1 && ($approval_type == 1 ||  $approval_type == 2 || $approval_type == 3) )
    						{ 
    							$data['receipt_no'] = $this->payment_modal->generate_receipt_no($pay['id_scheme'],$pay['branch']);
    							$payment['status'] = $this->payment_modal->update_receipt($pay['id_payment'],$data);
    						}
							// Generate account  number  based on one more settings Integ Auto//hh
							if($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set']==3)
							{  
								if($pay['scheme_acc_number'] == '' ||  $pay['scheme_acc_number'] == null)
								{
								    $ac_group_code = NULL;
									// Lucky draw
									if($pay['is_lucky_draw'] == 1 ){ // Based on scheme settings 
										// Update Group code in scheme_account table 
										$updCode = $this->payment_modal->updateGroupCode($pay); 
										$ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
									}
									// $this->db->query('LOCK TABLES scheme_account WRITE');
									$scheme_acc_number = $this->payment_modal->account_number_generator($pay['id_scheme'],$pay['branch'],$ac_group_code);  //Branch wise account number generation based on settings //HH
									if($scheme_acc_number != NULL && $scheme_acc_number !="Not Allocated"){
										$schData['scheme_acc_number'] = $scheme_acc_number; 
									}else{
										// $this->db->query('UNLOCK TABLES');
									}
									if($pay['gent_clientid'] ==1 && $scheme_acc_number != NULL){  
										$cliData = array(
														 "cliID_short_code"	=> $this->config->item('cliIDcode'),
														 "sync_scheme_code"	=> $pay['sync_scheme_code'],
														 "ac_no"			=> $scheme_acc_number
														);											
										$schData['ref_no'] = $this->payment_modal->generateClientID($cliData);
									}
									if($pay['receipt_no_set'] == 1 && $this->config->item('integrationType') == 2){ 	//upd client id & acc no cus reg table//
										$cliData = array(
														 "cliID_short_code"	=> $this->config->item('cliIDcode'),
														 "sync_scheme_code"	=> $pay['sync_scheme_code'],
														 "ac_no"			=> $scheme_acc_number
														);											
										$schDatacus['ref_no'] = $this->payment_modal->generateClientID($cliData);
										$schDatacus['scheme_acc_number'] = $scheme_acc_number;
										$schDatacus['group_code'] =$pay['group_code'];
									}
									$updSchAc = $this->payment_modal->update_account($schData,$pay['id_scheme_account']);
									// $this->db->query('UNLOCK TABLES');
									 $updcusreg = $this->payment_modal->update_cusreg($schDatacus,$pay['id_scheme_account']); //acc no upd to cus reg tab//hh
									 $updtrans = $this->payment_modal->update_trans($schDatacus,$pay['id_scheme_account']); //Client Id upd to trans tab//
								}
							}
    						//Update First Payment Amount In Scheme Account
    						 if(($approval_type == 1 || $approval_type == 2 || $approval_type == 3) && ($pay['firstPayamt_maxpayable']==1 || $pay['firstPayamt_as_payamt']==1) && ($pay['firstPayment_amt'] == '' ||  $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0))
    						 {
    							if($pay['flexible_sch_type'] == 4 && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "") ){ // Fix First payable as weight
									$fixPayable = array('firstPayment_wgt'  =>  $pay['metal_weight'] );
								}else{
									$fixPayable = array('firstPayment_amt'  =>  $pay['payment_amount'] );
								}
								$status = $this->payment_modal->update_account($fixPayable,$pay['id_scheme_account']);	 
    						 }
    						// Insert Data in Intermediate table
    						 if($approval_type == 2 || $approval_type == 3 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3))
    						 {
								if($this->config->item('integrationType') == 1){
									$this->insert_common_data_jil($pay['id_payment']);
								}else if($this->config->item('integrationType') == 2){
									$this->mobileapi_model->insert_common_data($pay['id_payment']);
								}
    						 }
    					}
    				}
    		    }
    			 if($service['sms'] == 1)
    			{
    				$id=$payment['id_payment'];
    				$data =$this->$serv_model->get_SMS_data($serviceID,$id);
    				$mobile =$data['mobile'];
    				$message = $data['message'];
    				if($this->config->item('sms_gateway') == '1'){
		    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
		    		}
		    		elseif($this->config->item('sms_gateway') == '2'){
		    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
		    		}
		    		elseif($this->config->item('sms_gateway') == '3'){
    					 $this->sms_model->sendSMS_SpearUC($mobile,$message,'',$service['dlt_te_id']);
    				}
    				elseif($this->config->item('sms_gateway') == '4'){
    					$this->sms_model->sendSMS_Asterixt($mobile,$message,'',$service['dlt_te_id']);
    				}
    				elseif($this->config->item('sms_gateway') == '5'){
    					$this->sms_model->sendSMS_Qikberry($mobile,$message,'',$service['dlt_te_id']);
    				}
    			}
    			if($service['serv_whatsapp'] == 1)
    			{
                	$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
                    $this->sms_model->send_whatsApp_message($mobile,$smsData);
                }
    			if($service['email'] == 1 && isset($payDatas[0]['email']) && $payDatas[0]['email'] != '')
    			{
    				$to = $payDatas[0]['email'];
    				$subject = "Reg - ".$this->comp['company_name']." payment for the purchase plan";
    				$data['payData'] = $payDatas[0];
    				$data['type'] = 3;
    				$data['company_details'] = $this->comp;
    				$message = $this->load->view('include/emailPayment',$data,true);
    				$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
    			}
				 if($gateway == 2  || $gateway == 4){
									$scheme_success = array("successMsg" => 'Transaction ID: '.$payData['txnid'].' for the amount INR. '.$payData['amount'].' is paid successfully. Thanks for your payment with '.$this->comp['company_name'].'');
								}else{
									$scheme_success = array("successMsg" => 'Payment successful. Thanks for your payment with '.$this->comp['company_name'].'');
								}
    			$this->session->set_flashdata($scheme_success);
    			redirect("/paymt/payment_history");
    		}
    		else
    		{
    			$scheme_success = array("errMsg" => 'Error in updating the database.Please contact administrator at your earliest convenience.');
    			$this->session->set_flashdata($scheme_success);
    			redirect("/paymt");
    		} 
	    }
    }
	public function payment_cancel($payData = "", $gateway = "")
    {
        if($gateway == ""){
            $payData = $_POST;
        } 
        $serv_model= self::SERV_MODEL;
        $updateData = array(
        			"bank_name"			 =>	(isset($payData['issuing_bank']) ? $payData['issuing_bank'] : NULL),
        			"payment_mode"       => (isset($payData['mode']) ? $payData['mode'] : NULL),
        			"card_no"			 => (isset($payData['mode']) && ($payData['mode'] == 'CC' || $payData['mode'] == 'DC') ? $payData['cardnum'] :NULL ),
        			"payu_id"            => (isset($payData['mihpayid']) ? $payData['mihpayid'] : NULL),
        			"remark"             => (isset($payData['field9']) ? $payData['field9'] : NULL),
        			"payment_ref_number" => (isset($payData['bank_ref_num']) ? $payData['bank_ref_num'] : NULL ),
        			"payment_status"     => $this->payment_status['cancel'] 
        		);
        if(!empty($payData['txnid']) && $payData['txnid'] != NULL){
            $payment = $this->payment_modal->updateGatewayResponse($updateData,$payData['txnid']);
            $payData = $this->payment_modal->get_paymenMailData(isset($payment['id_payment'])?$payment['id_payment']:'');
            //$payData = $this->registration_model->get_cusData($this->session->userdata('username'));
            $serviceID = 7;
            $service = $this->services_modal->checkService($serviceID);
            if($service['sms'] == 1)
            {
            		$id=$payment['id_payment'];
            		$data =$this->$serv_model->get_SMS_data($serviceID,$id);
            		$mobile =$data['mobile'];
            		$message = $data['message'];
	            	if($this->config->item('sms_gateway') == '1'){
		    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
		    		}
		    		elseif($this->config->item('sms_gateway') == '2'){
		    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
		    		}
		    		elseif($this->config->item('sms_gateway') == '3'){
    					 $this->sms_model->sendSMS_SpearUC($mobile,$message,'',$service['dlt_te_id']);
    				}
    				elseif($this->config->item('sms_gateway') == '4'){
    					$this->sms_model->sendSMS_Asterixt($mobile,$message,'',$service['dlt_te_id']);
    				}
    				elseif($this->config->item('sms_gateway') == '5'){
    					$this->sms_model->sendSMS_Qikberry($mobile,$message,'',$service['dlt_te_id']);
    				}
            }
            if($service['serv_whatsapp'] == 1)
			{
            	$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
                $this->sms_model->send_whatsApp_message($mobile,$smsData);
            }
            if($service['email'] == 1 && isset($payData[0]['email']) && $payData[0]['email'] != '')
            {
            	$to = $payData[0]['email'];
            	$subject = "Reg - ".$this->comp['company_name']." payment for the purchase plan";
            	$data['payData'] = $payData[0];
            	$data['type'] = -1;
            	$data['company_details'] = $this->comp;
            	$message = $this->load->view('include/emailPayment',$data,true);
            	$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");	
            }
            $scheme_failure = array("errMsg" => 'Payment failure.Please try again later');
            $this->session->set_flashdata($scheme_failure);
        }
        redirect("/paymt");
	}
     public function generateInvoice($payment_no)
	{
		//create PDF invoice	
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{	 
			$this->comp = $this->login_model->company_details();
			$this->load->helper(array('dompdf', 'file'));
			//$records = $this->payment_modal->get_invoiceData($payment_no);
			$data['records'] = $this->payment_modal->get_invoiceData($payment_no);
			//echo $this->db->last_query();exit;
			$data['gstSplitup'] = $this->payment_modal->get_gstSplitupData($data['records'][0]['id_scheme'],$data['records'][0]['date_add']);
			//echo "<pre>";print_r($data['records']);exit;
			//$data['comp_details'] =$this->comp;
			if($this->session->userdata('branch_settings')==1){
			$data['comp_details']=$this->login_model->get_branchcompany($data['records'][0]['id_branch']);			
		   }else{
			 $data['comp_details'] = $this->comp;
		    }
   	   		$data['records'][0]['amount_in_words'] = $this->no_to_words($data['records'][0]['payment_amount']);
			$dompdf = new DOMPDF();
			//$html = $this->load->view('include/receipt1', $data,true);
			$html = $this->load->view('include/receipt_temp', $data,true);
			//echo $html;exit;
            $dompdf->load_html($html); 
			$dompdf->set_paper("a4", "portriat" );
			$dompdf->render();
			$dompdf->stream("receipt1.pdf",array('Attachment'=>0));
		}
	}
	function no_to_words($no="")
 	{
		$nos =explode('.', $no);
		$val1="";
		$val2="";
		$val="";
		if(isset($nos[0]))
		{
			$val1=$this->no_to_words1($nos[0]);
			$val=$val1." RUPEES";
		}
		if(isset($nos[1]) && $nos[1] != 0)
		{
			$val2=$this->no_to_words1($nos[1]);
			if(isset($val2))
			$val=$val1." RUPEES AND"." ".$val2." PAISA";
		}
		return $val;
	}
	function no_to_words1($nos1="")
 	{
	$words = array('0'=> '' ,'1'=> 'One' ,'2'=> 'Two' ,'3' => 'Three','4' => 'Four','5' => 'Five','6' => 'Six','7' => 'Seven','8' => 'Eight','9' => 'Nine','10' => 'Ten','11' => 'Eleven','12' => 'Twelve','13' => 'Thirteen','14' => 'Fouteen','15' => 'Fifteen','16' => 'Sixteen','17' => 'Seventeen','18' => 'Eighteen','19' => 'Nineteen','20' => 'Twenty','30' => 'Thirty','40' => 'Fourty','50' => 'Fifty','60' => 'Sixty','70' => 'Seventy','80' => 'Eighty','90' => 'Ninty','100' => 'Hundred &','1000' => 'Thousand','100000' => 'Lakh','10000000' => 'Crore');
	$nos[0] = $nos1;
	if($nos[0] == 0 )
        return '';
    else {           
			$novalue='';
			$highno=$nos[0];
			$remainno=0;
			$value=100;
			$value1=1000;
			$temp='';   
            while($nos[0]>=100)   
			 { 
                if(($value <= $nos[0]) &&($nos[0]  < $value1))   
				{
                	$novalue=$words["$value"];
                	$highno = (int)($nos[0]/$value);
                	$remainno = $nos[0] % $value;
                	break;
                }
                $value= $value1;
                $value1 = $value * 100;
            }       
          if(array_key_exists("$highno",$words))
		  {
			  return $words["$highno"]." ".$novalue." ". $this-> no_to_words1($remainno);
		  }
          else 
		  {
             $unit=$highno%10;
             $ten =(int)($highno/10)*10;       
             return $words["$ten"]." ".$words["$unit"]." ".$novalue." ". $this->no_to_words1($remainno);
          }
		}
	}
	public function get_metal()
    {
    $data=$this->scheme_modal->get_metal();
	echo json_encode($data);
    } 
    //metal filter IN Pay history page//HH
     public function metal_report()
	{
		$id_metal  = $this->input->post('id_metal');
		$metal= $this->payment_modal->get_payment_history($id_metal);
		echo json_encode($metal);
	}
	// payment history listing based on tab[branches] selection  //HH
	function payment_history()
	{
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{
			$branchWiseLogin = $this->payment_modal->get_branchWiseLogin(); 
			$payHistory = $this->payment_modal->get_paymenthistory($branchWiseLogin);
			$chitSettings   = $this->scheme_modal->getChitSettings();
		     $branches 	= array();
		    $active_tab 	= NULL;
		    if($chitSettings['branch_settings'] == 1){				
	        	$branches = $this->payment_modal->branchesData();
			}
			$data = array('payHistory' => $payHistory);
			$pageType = array('page' => 'payHistory','currency_symbol'=>$this->comp['currency_symbol'],'scheme_status' =>  $this->scheme_status,'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
		//	$data['content'] = $data;
		    $data['content'] = array(
								'payHistory'			=> $payHistory,
								'branches' 		=> $branches,
								'is_multi_commodity'=> $chitSettings['is_multi_commodity'],
								'is_branchwise_cus_reg'=> $chitSettings['is_branchwise_cus_reg'],
								'branchwise_scheme'=> $chitSettings['branchwise_scheme'],
								'branch_settings'=> $chitSettings['branch_settings']
							);
			$data['fileName'] = self::VIEW_FOLDER.'payment_history';
			$this->load->view('layout/template', $data);
		}
	}
	function mobile_payment()
	{
	   if(isset($_GET))
	   {  
		   //get the values posted from mobile in array 
		   $payData = $_GET;
		   $pay_flag = TRUE;
		   $allow_flag = FALSE;
		   $submit_pay = FALSE;
		   $actAmount = (float) $payData['amount'];  // sum of all chit amount
		   $cusData = $this->payment_modal->get_customer($payData['phone']);
		   $gateway = (isset($payData['gateway']) ? $payData['gateway'] :1 );
		   $redeemed_amount = $payData['redeemed_amount'] ;
		   $id_branch = (isset($payData['id_branch']) ? $payData['id_branch']:NULL);
		   // For admin app
		   $id_employee = (isset($payData['id_employee']) && $payData['login_type'] == 'EMP' ? $payData['id_employee']:NULL);
		   $added_through = (!empty($payData['id_employee']) ? 3:2); // 2- custmer mobile app, 3 - Admin mobile app
		   if($pay_flag)
		   {
				//generate txnid
				$txnid = uniqid(time());
				$i=1;
				$sch_payment = json_decode($payData['pay_arr']);
				$udf1= "";
				$udf2= "";
				$udf3= "";
				$productinfo= "";
				$payIds= "";
				$this->db->trans_begin();	
				foreach ($sch_payment as $pay){	
					$metal_wgt	= NULL;			
					$discount 	= ($pay->discount >0 ? $pay->discount : 0.00);
					$chit 		= $this->payment_modal->get_schemeByChit($pay->udf1);
	   				$pay->discount = $discount;
					//validate amount
					$metal_rate = $this->payment_modal->getMetalRate($pay->id_branch);
					//$branch = $this->payment_modal->get_schjoinbranch($pay->udf1);
                    $rate_fields = $this->payment_modal->getRateFields($chit['id_metal']); 
                    $rate_field = sizeof($rate_fields) == 1 ? $rate_fields['rate_field'] : NULL;
                    $rate   = (float) ( $rate_field == null ? null : $metal_rate[$rate_field] );
					//$rate = (float) ($chit['id_metal'] == 1 ? $metal_rate['goldrate_22ct'] : $metal_rate['silverrate_1gm']);               
					// GST Calculation
					$gst_amt = 0;
					$amount = $pay->amount + $pay->discount;
	    			if($chit['gst'] > 0 ){
				        $insAmt_withoutDisc = $amount-$pay->discount;
				        if($sch_data['gst_type'] == 0){  // Inclusive  
	                        $gst_removed_amt = $insAmt_withoutDisc*100/(100+$chit['gst']);
	                    	$gst_amt = $insAmt_withoutDisc - $gst_removed_amt;
	                    	$metal_wgt = ($gst_removed_amt+$pay->discount)/$pay->udf3;  
	                    }
	                    else if($sch_data['gst_type'] == 1){ // Exclusive
	                        $amt_with_gst = $insAmt_withoutDisc*((100+$chit['gst'])/100);
	                    	$gst_amt = $amt_with_gst - $insAmt_withoutDisc ;
	                    	$metal_wgt = $amount/$pay->udf3; 
	                    }
	   	            }
				   if($pay->scheme_type == 0 || ($pay->scheme_type == 3 && $chit['flexible_sch_type'] == 1)){    
                      $allow_flag = (($pay->discount==""?0.00:$pay->discount)+$pay->amount >= $chit['amount']? TRUE :FALSE);
                   }
                   else if( $pay->scheme_type == 1){  
                      $amt = $rate * $pay->udf2;
                      $allow_flag = ((($pay->amount >= $amt)&&($rate == (float) $pay->udf3))? TRUE : FALSE);$metal_wgt = (isset($pay->udf2) && $pay->udf2 !='' ? $pay->udf2 : 0.000);              
                   }
				   else if($pay->scheme_type == 2 || ($pay->scheme_type == 3 && ($chit['flexible_sch_type'] == 2 ||$chit['flexible_sch_type'] == 3 ) )){                              	 
                      $data = array('metal_rate'=>$pay->udf3,'amount'=>$pay->udf4);
                        // print_r($data['metal_rate']);exit; 
                      if($gst_amt == 0){
					  	$metal_wgt = $this->amount_to_weight($data); 
					  }	
                    //  $allow_flag = (($rate == (float) $pay->udf3) ? TRUE :FALSE);
                    $allow_flag = (($data['metal_rate'] == (float) $pay->udf3) ? TRUE :FALSE);
                   //	print_r($allow_flag);exit;
                   }
                   else{    
                      $metal_wgt = (isset($pay->udf2) && $pay->udf2 !='' ? $pay->udf2 : 0.000);                 
                      $allow_flag= (($rate == (float) $pay->udf3) ? TRUE :FALSE);
                   }
                   if(!$allow_flag){
                      $this->db->trans_rollback();
                      $submit_pay = FALSE;
                      redirect('paymt/payment_rejected');
                   }
				   if($cusData['branch_settings']==1)
				   {
					    if($cusData['is_branchwise_cus_reg']==1)
						{
							$id_branch  = $cusData['id_branch'];
						}
						else
						{
							$id_branch  = $pay->id_branch;
							//$id_branch  = $branch['sch_join_branch'];
						}								
					}
					else{
						$id_branch = NULL;
					}
					$due_month	= date("m");
					$month		= date("m");
					$due_year	= date("Y");
					// ND - normal, PN - pending & normal, AN - adv & normal, PD pending due ,AD-adv due
					$dueType = ''; 
						if($pay->due_type == 'PD'){
						    $month  	=  NULL;
	        				$due_year   =  NULL;
	        				$dueType    = 'PD';
						}
						else if($pay->due_type == 'ND')
						{ 
							$dueData = $this->payment_modal->generateDueDate($pay->udf1,$dueType);
							// set due data
							$month      = $dueData['due_month'];
							$due_year   = $dueData['due_year'];
							$dueType = $pay->due_type;
						}
						else if($pay->due_type == 'AD')
						{
						    $dueType = $pay->due_type;
						    $dueData = $this->payment_modal->generateDueDate($pay->udf1,$dueType);
							// set due data
							$month      = $dueData['due_month'];
							$due_year   = $dueData['due_year'];    									
						}
						else
						{
							$dueType = $pay->due_type;
						} 
					    // metal_wgt_decimal = 2 means only 2 decimals are allowed for metal wgt, hence bcdiv() is used to make the weight to 2 decimals and 0 is appended as last digit.
						$decimal = $chit['metal_wgt_decimal'];   
                        $round_off = $chit['metal_wgt_roundoff'] ; 
                        $metal_wgt =  ($round_off == 0 ? bcdiv($metal_wgt,1,$decimal) : $metal_wgt );
					 // print_r($allow_flag);exit;


					 // AB - digi_benefits & weight...  RAHUL DIGI UPDATES

					$dg_saved_benefit = 0;
					$dg_benefit_value = 0;
					$dg_benefit_type = 0;
					$dg_saved_benefit_amt = 0;
					$dg_other_benefit_wgt = 0;
					$dg_other_benefit_amt = 0;

					if ($chit['is_digi'] == 1) {
						$data = array('amount' => $pay->amount, 'metal_rate' => $pay->udf3);
						$metal_wgt = formatMetalWeight($this->amount_to_weight($data));

						if ($chit['interest'] == 1) {
							$digi_account = $this->digigold_modal->digiGold_account($cusData['id_customer'], $chit['id_scheme']);
							$digi_benefitData = $this->digigold_modal->get_digi_benefit($digi_account);

							if (sizeof($digi_benefitData) > 0) {
								foreach ($digi_benefitData as $digi_benefit) {
									if ($digi_benefit['commodity'] == $chit['id_metal']) {
										if ($digi_benefit['interest_type'] == 0) {
											$ben = $pay->amount * ($digi_benefit['interest_value'] / 100);
										} else {
											$ben = $digi_benefit['interest_value'];
										}
										$dg_saved_benefit_amt = $ben;
										$data = array('amount' => $dg_saved_benefit_amt, 'metal_rate' => $pay->udf3);
										$dg_saved_benefit = formatMetalWeight($this->amount_to_weight($data));
										$dg_benefit_value = $digi_benefit['interest_value'];
										$dg_benefit_type = $digi_benefit['interest_type']; // 0-percent, 1- amount
									} else {
										// Cross-commodity benefit (e.g. silver benefit on gold scheme)
										$other_metal_rate = $this->payment_modal->get_metalrate_by_branch($id_branch, $digi_benefit['commodity'], '');
										if ($digi_benefit['interest_type'] == 0) {
											$dg_other_benefit_wgt = $metal_wgt * ($digi_benefit['interest_value'] / 100);
											$dg_other_benefit_amt = $dg_other_benefit_wgt * $other_metal_rate;
										} else {
											$dg_other_benefit_amt = $digi_benefit['interest_value'];
											$dg_other_benefit_wgt = $dg_other_benefit_amt * $other_metal_rate;
										}
										$dg_other_benefit_wgt = formatMetalWeight(($dg_other_benefit_wgt * 1000) / 1000);
									}
								}
							}
						}
                    }


					   if($allow_flag){
					       	$entry_date = $this->get_entrydate($id_branch); // Taken from ret_day_closing  table branch wise //HH
							$custom_entry_date = (sizeof($entry_date) > 0 ? $entry_date['custom_entry_date'] : NULL);
					        $start_year = $this->payment_modal->get_financialYear();
							$insertData = array(
						     //     "custom_entry_date"	 => (isset($chit['custom_entry_date'])? $chit['custom_entry_date'] : NULL ),
							        "custom_entry_date"	 => ($custom_entry_date? $custom_entry_date : NULL ),
									"id_scheme_account"	 => (isset($pay->udf1)? $pay->udf1 : NULL ),
									"payment_amount" 	 => (isset($pay->amount)? $pay->amount+($pay->discount==""?0.00:$pay->discount) : NULL ), 
									"gst" 	    		 => (isset( $chit['gst'])? $chit['gst'] : 0 ),
									"gst_type" 	    	 => (isset($chit['gst_type'])? $chit['gst_type'] : 0 ),
									"gst_amount"    	 => $gst_amt,
									"payment_type" 	     => ($gateway == 0 ?'Manual':(isset($payData['pg_code']) ? ($payData['pg_code'] == 1 ? ($redeemed_amount > 0 ?"Wallet + Payu Checkout":"Payu Checkout"):($payData['pg_code'] == 2 ? "CC Avenue":($payData['pg_code'] == 4 ? "Cash Free":($payData['pg_code'] == 5 ? "Atom":($payData['pg_code'] == 6 ? "Ippo Pay":"Tech Process"))))): 1)),
									"payment_mode"       => ($gateway == 0 ?'CSH':NULL ),
									"no_of_dues" 	     => 1,
									"actual_trans_amt"   => (isset($actAmount) ? $actAmount : 0.00),
									"act_amount"         => (isset($pay->amount)? $pay->amount : NULL ),
									"date_payment" 		 =>  date('Y-m-d H:i:s'),
									"metal_rate"         => (isset($pay->udf3) && $pay->udf3 !='' && $pay->udf3 > 0 ? $pay->udf3 : $this->payment_modal->getMetalRate('')['goldrate_22ct']),
									"metal_weight"       =>  $metal_wgt,
									"id_transaction"     => ($gateway == 0 ?NULL:(isset($txnid) ? $txnid.'-'.$i : NULL)),
									"ref_trans_id"       => ($gateway == 0 ?NULL:(isset($txnid) ? $txnid : NULL)),// to update pay status after trans complete.
									"remark"             =>  'Payment for '.$pay->udf5." Initiated.",
									"added_by"			 =>  $added_through,
									"id_employee"		 =>  $id_employee,
									"add_charges" 	     => (isset($pay->charge) ?$pay->charge : NULL), 
									"discountAmt"        => ($pay->discount!="" ? $pay->discount : 0.00),
									"payment_status"     => ($gateway == 0 ? ($added_through== 3 ?$this->payment_status['success']:$this->payment_status['awaiting']):$this->payment_status['pending']),
									"id_payGateway"      => (isset($payData['gateway']) ? $payData['gateway']: 1),
							 		"redeemed_amount"    => (isset($redeemed_amount) ?$redeemed_amount :0.00),
							 		"id_branch"    		 => $id_branch,
							 		"due_type"           => $dueType,
							 		"due_month"          => $month,
							 		"due_year"           => $due_year,
							 		"receipt_year"            => $start_year,
							 		"id_employee"        => (isset($payData['id_employee']) && $payData['login_type'] == 'EMP' ?$payData['id_employee'] :NULL),
							 		"id_agent"        => (isset($payData['id_employee']) && $payData['login_type'] == 'AGENT' ?$payData['id_employee'] :NULL),
							 		"payment_mode" =>  (isset($payData['pay_mode']) ?$payData['pay_mode'] : NULL),
							 		"card_no"        => (isset($payData['refernceNo']) ?$payData['refernceNo'] : NULL),
							 		"saved_benefits"      => $dg_saved_benefit,   //digi scheme benefit weight
							 		"benefit_value"       => $dg_benefit_value,
							 		"benefit_type"        => $dg_benefit_type,
							 		"saved_benefit_amt"   => $dg_saved_benefit_amt,  //digi scheme benefit amount
							 		'dg_other_benefit_wgt' => (!empty($dg_other_benefit_wgt) ? $dg_other_benefit_wgt : NULL),
							 		'dg_other_benefit_amt' => (!empty($dg_other_benefit_amt) ? $dg_other_benefit_amt : NULL),
							// 	    "is_point_credited"   => 1
									//status - 0 (pending), will change to 1 after approved at backend
								);  
							//	print_r($insertData);exit;
						$udf1 = $udf1." ".$pay->udf1;
						$udf2 = $udf2." ".$pay->udf2;
						$udf3 = $udf3." ".$pay->udf3;
						$productinfo = $productinfo." ".$pay->udf1;
						if($this->config->item("integrationType") == 5){ // Generate tranUniqueId
						   $chit['reference_no'] = $cusData['reference_no'];
						   $chit['nominee_address1'] = $cusData['nominee_address1'];
						   $chit['nominee_address2'] = $cusData['nominee_address2'];
						   $chit['nominee_name'] = $cusData['nominee_name'];
						   $chit['nominee_mobile'] = $cusData['nominee_mobile'];
						   $chit['nominee_relationship'] = $cusData['nominee_relationship'];
						   //$chit['referal_code'] = $cusData['id_employee'];
                           $tranUniqueId = $this->generateTranUniqueId($chit,$insertData['payment_amount']);
                           if($tranUniqueId['status'] == false){
                                //$result['resData'] = $tranUniqueId['resData'];
                                $result['status'] = false;
                                $result['message'] = isset($tranUniqueId['resData']->errorMsg) ? $tranUniqueId['resData']->errorMsg :'Unable to proceed payment. Kindly contact customer support...';
                                //$this->response($result,200); 
                                return $result;
                           }
                           $insertData['offline_tran_uniqueid'] = $tranUniqueId['tranUniqueId'];
                        }
						//inserting pay_data before gateway process
						//echo "<pre>"; print_r($insertData);exit; 
                         $payment = $this->payment_modal->addPayment($insertData);	
						 $payIds = $payIds.",".$payment['insertID'];
						$i++;
					}
				 }
			//	 echo $this->db->last_query();exit;
				 // [DEBUG] First trans_status check
				 $_ts1 = $this->db->trans_status();
				 $_dbg_err1 = $this->db->error();
				 $_dbg_last1 = $this->db->last_query();
				 $_dbg_log1 = "\n[DEBUG-TRANS1] " . date('Y-m-d H:i:s') . 
				 	"\ntrans_status: " . ($_ts1 ? 'TRUE' : 'FALSE') .
				 	"\ngateway: " . $gateway .
				 	"\nDB Error: " . json_encode($_dbg_err1) .
				 	"\nLast Query: " . substr($_dbg_last1, 0, 500) .
				 	"\npayIds: " . $payIds;
				 @file_put_contents('d:/xampp7/htdocs/manjalijewellers/log/debug_trans_' . date('Y-m-d') . '.txt', $_dbg_log1, FILE_APPEND | LOCK_EX);
				 
				 if($_ts1 === TRUE)
	             { 
				 	$this->db->trans_commit();
					 if($gateway == 0){ // For admin app
					 	$paymtData['schAc_ids'] = $productinfo;
					 	$paymtData['pay_ids'] = $payIds;
					 	$paymtData['login_type'] = $payData['login_type'];
					 	$paymtData['id_employee'] = $payData['id_employee'];
					 	//print_r($paymtData);exit;
					 	$this->adminAppSuccess($paymtData);
					 }else{
					 	$submit_pay = TRUE;
					 }
				 }
				 else{
				 	$this->db->trans_rollback();
				 	if($gateway == 0){ // For admin app
					 	redirect('paymt/adminapp/failed');
					 }else{
					 	$submit_pay = FALSE;
					 }
				 }
				if($submit_pay)
				{
		 			$paycred = $this->payment_gateway($cusData['id_branch'],$payData['gateway']); 
					//set data for hash generation
					  $data['pay'] =	array (
						'key' 			=> $paycred['param_1'], 
						'txnid' 		=> $txnid, 
						'amount' 		=> (isset($payData['amount'])   ? $payData['amount']:''),
						'productinfo'	=> (isset($productinfo)    ? $productinfo :''),
						'firstname' 	=> (isset($cusData['firstname'])? $cusData['firstname'] :''),
						'lastname' 		=> (isset($cusData['lastname']) ? $cusData['lastname']:''),
						'email' 		=> !empty($cusData['email']) ? $cusData['email'] : $this->comp['email'],
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
						'udf4' 			=> (isset($payData['udf4'])    ? $payData['udf4'] : ''),
						'udf5' 			=> (isset($payData['udf5'])? $payData['udf5']:'') 
					  );
				    /* echo "<pre>"; print_r( $data['pay']); echo "<pre>";exit; */
				    //generate hash
				    $hash_sequence =   Misc::get_hash( $data['pay'],$paycred['param_2']);		
					if($payData['pg_code'] == 3) // Techprocess
                    { 
                        $data['pay']['bankcode'] 	 = $payData['bankcode']; 
						$data['pay']['ccnum'] 		 = $payData['ccnum']; 
						$data['pay']['ccname'] 		 = $payData['ccname']; 
						$data['pay']['ccvv'] 		 = $payData['ccvv']; 
						$data['pay']['ccexpmon'] 	 = $payData['ccexpmon']; 
						$data['pay']['ccexpyr']      = $payData['ccexpyr']; 
						$data['pay']['pg']    		 = $payData['pg'];
						$data['pay']['m_code']    		 =$paycred['param_3'];
						$data['pay']['iv']    		 = $paycred['param_4'];
						$data['pay']['store_card']   = (isset($payData['store_card'])? $payData['store_card'] : 0 );
                        $data['pay']['id_pg'] = $payData['gateway'];
                        $data['pay']['id_branch'] = $cusData['id_branch'];
                        $this->submitToTechProcess($data['pay'],'mobile','SS');
                    }
                    else if($payData['pg_code'] == 2){ // CCAVENUES
                    	$merchant_data = "";
	                    $merchant_id   = $paycred['param_4'];
	                    $working_key   = $paycred['param_1'];//Shared by CCAVENUES
	                    $access_code   = $paycred['param_3'] ;//Shared by CCAVENUES 
                    	$data['hdfcpay'] =	array (
							'tid' 		    =>  (rand(10,100).''.time()), // should contain numbers only
					        'merchant_id'   =>  $merchant_id,
					        'order_id'      =>  $txnid,
							'amount' 		=> (isset($payData['amount'])   ? $payData['amount']:''),
							'productinfo'	=> (isset($productinfo)? $productinfo: ''),
							'currency'	    => 'INR',
						    'redirect_url'  =>  $this->config->item('base_url')."index.php/paymt/mobileResponseURL",
						    'language'      => 'EN',
							'id_payment'    => $payment['insertID'],
							'billing_name' 	=> (isset($cusData['firstname'])? (isset($cusData['lastname']) ? $cusData['firstname'].' '.$cusData['lastname']:$payData['firstname']) :''),
							'billing_email' => (isset($cusData['email'])    ? $cusData['email']:''), 
							'billing_tel' 	=> (isset($cusData['phone'])    ? $cusData['phone'] :''),
							'billing_address' 	=> (isset($cusData['address1'])? (isset($cusData['address2']) ? $cusData['address1'].' '.$cusData['address2']:$payData['address1']) :''),
							'billing_city'	=> (isset($cusData['city']) ? $cusData['city'] :''), 
							'billing_state'	=> (isset($cusData['state']) ? $cusData['state'] : ''), 
							'billing_country'=> (isset($cusData['country']) ? $cusData['country'] : ''), 
							'billing_zip'	=> (isset($cusData['zipcode']) ? $cusData['zipcode'] :''),
							'merchant_param1' => (isset($udf1) ? $udf1 :''),
							'merchant_param2' => (isset($udf2) ? $udf2 :''),
							'merchant_param3' => (isset($udf3) ? $udf3 :''),
							'merchant_param4' => (isset($payment['insertID']) ? $payment['insertID'] : ''),
							'merchant_param5' => (isset($udf5) ? $udf5:'') 
						  );
	                    foreach ($data['hdfcpay'] as $key => $value){ 
	                        $merchant_data.=$key.'='.urlencode($value).'&';
	                    }   
	                    $encrypted_data=encrypt($merchant_data,$working_key);
	                    //Generate Encrypted Datas
	                    if($encrypted_data!='' && $txnid !='')
	                    {
	                        $data['hdfcpay']['encRequest']   = $encrypted_data;
	                        $data['hdfcpay']['access_code']  = $access_code; 
	                        $this->load->view('hdfc/payment',$data);
	                    }
					}
    				else if($payData['pg_code'] == 4){   // Cash Free
        			    $paycred = $this->payment_gateway($cusData['id_branch'],$payData['gateway']); 
        				$secretKey       = $paycred['param_1']; //Shared by Cashfree
        				$gen_email =  $this->random_strings(8).'@gmail.com'; 
                        $data['cashfreepay'] =	array (
                            'appId'         => $paycred['param_3'],  //Shared by Cashfree
                            'orderId'       =>  $txnid,
                            'orderAmount' 	=>  $actAmount, 
                            'orderCurrency'	=> 'INR',
                            'orderNote'	    => 'Online Money Transaction',
                            'customerName' 	=> (isset($cusData['firstname'])? (isset($cusData['lastname']) ? $cusData['firstname'].' '.$cusData['lastname']:$cusData['firstname']) :''),
                           // 'customerEmail' => !empty($cusData['email']) ? $cusData['email'] : $this->comp['email'],
                            'customerEmail' => !empty($cusData['email']) ? $cusData['email'] : $gen_email,
                            'customerPhone' => (isset($cusData['mobile'])    ? $cusData['mobile'] :''),
            				"returnUrl"     => $this->config->item('base_url')."index.php/paymt/cashfreemobile",
            				"notifyUrl"     => $this->config->item('base_url')."index.php/services/cashfreeStatusNotify/".$payData['gateway']
                        ); 
                         // get secret key from your config
                        ksort($data['cashfreepay']); 
                        $signatureData = "";
                        foreach ($data['cashfreepay'] as $key => $value){ 
                            $signatureData .= $key.$value;
                        }
        				$signature       = hash_hmac('sha256', $signatureData,$secretKey,true);
                        $signature       = base64_encode($signature);
        				//Generate Encrypted Datas
                        if($signature!='' && $secretKey !='' && $txnid !='')
                        {
                            $data['cashfreepay']['signature']   = $signature; 
                            $this->load->view('mobile/cashfreepayment',$data);
                        }
    			    }   
    			    else if($payData['pg_code'] == 5) // ATOM
    			    {  
    			        $data['pay']['gateway'] = $paycred; 
    			        $this->submitToAtom($data['pay'],'Mob');
    			    }else if($payData['pg_code'] == 6){   // Ippo Gateway
					$gen_email =  $this->random_strings(8).'@gmail.com'; 
                    $data['ippo'] =	array (
                    'publicKey'         => $paycred['param_3'],  //Shared by Ippo gateway
                    'secretKey'       => $paycred['param_1'], //Shared by Ippo gateway
                    'orderId'       =>  $txnid,
                    'orderAmount' 	=>  $amount, 
                    'orderCurrency'	=> 'INR',
                    'orderNote'	    => 'Online Money Transaction',
                    'customerName' 	=> (isset($cusData['firstname'])? (isset($cusData['lastname']) ? $cusData['firstname'].' '.$cusData['lastname']:$cusData['firstname']) :''),
                    'customerEmail' => !empty($cusData['email']) ? $cusData['email'] : $gen_email,
                    'customerPhone' => (isset($cusData['phone'])    ? $cusData['phone'] :''),
					"returnUrl"     =>   $this->config->item('base_url')."index.php/paymt/ipporesponseURL"                      //$this->config->item('base_url')."index.php/paymt/cashfreeresponseURL"
                     ); 
                   //print_r($data['ippo']);exit;
                    $this->load->view('ippo/ippo_payment',$data);
				}
                    else{ // Payu 
                    	$hash_sequence =   Misc::get_hash( $data['pay'],$paycred['param_2']);	
    					if($hash_sequence!='' && $txnid !='')
    					{
    						$data['pay']['hash'] 		 = $hash_sequence; 
    						$data['pay']['pg']    		 = $payData['pg']; 
    						$data['pay']['bankcode'] 	 = $payData['bankcode']; 
    						$data['pay']['ccnum'] 		 = $payData['ccnum']; 
    						$data['pay']['ccname'] 		 = $payData['ccname']; 
    						$data['pay']['ccvv'] 		 = $payData['ccvv']; 
    						$data['pay']['ccexpmon'] 	 = $payData['ccexpmon']; 
    						$data['pay']['ccexpyr']      = $payData['ccexpyr']; 
    						$data['pay']['curl']         =  $this->config->item('base_url')."index.php/paymt/cancelMURL"; 
    						$data['pay']['furl']         =  $this->config->item('base_url')."index.php/paymt/failureMURL"; 
    						$data['pay']['surl']         =  $this->config->item('base_url')."index.php/paymt/successMURL";		
    						$data['pay']['user_credentials']   =  $paycred['param_1'].':'.$payData['phone'];		
    						$data['pay']['store_card']   =  (isset($payData['store_card'])? $payData['store_card'] : NULL );		
    						$data['pay']['store_card_token']   =  (isset($payData['card_token'])? $payData['card_token'] : NULL );	
    						$this->load->view('mobile/payment',$data);
    					}	
				    }
			}
		   }
	   }
	}
	function adminAppSuccess($paymtData){ 
		$schAc_ids = explode(' ',$paymtData['schAc_ids']);
		unset($schAc_ids[0]); // 0th key will be always empty so delete
		$pay_ids = explode(',',$paymtData['pay_ids']);
		unset($pay_ids[0]); // 0th key will be always empty so delete 
		$transData = array();
		$this->load->model('adminappapi_model');
        $pay = $this->adminappapi_model->getWalletPaymentContent($pay_ids[1]); 
        $this->db->trans_begin();
        if($pay['redeemed_amount'] > 0){ 
        	  $transData = array('mobile' 			=> $pay['mobile'],
        						 'actual_trans_amt' => $pay['actual_trans_amt'],
        						 'available_points'	=> ($pay['isAvail'] == 0 ?0:$pay['available_points']),
        						 'isAvail'			=> ($pay['isAvail'] == 0 ?0:1),
        						 'redeemed_amount'	=> $pay['redeemed_amount'],
        						 'id_payment'       => $pay['id_payment'],
        						 'txnid'            => $trans_id.'- D',
        						 'branch'           => $pay['branch'],
            					 'walletIntegration'=> $pay['walletIntegration'],
		    					 'wallet_balance_type' => $pay['wallet_balance_type'],
		    					 'wallet_points' => $pay['wallet_points'],
		    					 'wallet_amt_per_points' => $pay['wallet_amt_per_points']
        						);  
        	if(!empty($transData)){
        	    $this->insertWalletTrans($transData);  
        	}
        }
        $serviceID = 7;
        $service = $this->services_modal->checkService($serviceID); 
			$payIds = $this->payment_modal->getPayIds($trans_id);  
			if(sizeof($pay_ids) > 0)
			{
				foreach ($pay_ids as $pay_id)
				{	 
				    $pay = $this->adminappapi_model->getPayGenData($pay_id);
				    
			/* Insert into multi mode table code upd on : 19-01-2023 by AB*/	    
				    $paymentMode = $pay['payment_mode']; 
				    
				    // Multi mode payment
				    if($paymentMode != NULL)
     				{
     				    
     					$arrayPayMode=array(
        								'id_payment'         => $pay['id_payment'],
    							        'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
        								'payment_date'		 => $pay['date_payment'],
        								'created_time'	     => date("Y-m-d H:i:s"),
        								"payment_mode"       => $pay['payment_mode'],
                						"remark"             => "collection app",
                						"payment_status"     => $pay['payment_status']
                						
                					    );

						                if ($pay['due_type'] == 'GA') {
                                            $arrayPayMode['id_adv_payment'] = $pay['id_payment'];
                                            $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'general_advance_mode_detail');
                                        } else {
                                            $update_pmd = array(
                                                'payment_status' => 9, // Cancelled
                                                "updated_time" => date('Y-m-d H:i:s'),
                                                "remark" => "Removed while direct success on cashfree " . date('Y-m-d H:i:s'),
                                            );
                                            $update_existing_pmd = $this->payment_modal->updData($update_pmd, 'id_payment', $pay['id_payment'], 'payment_mode_details');
                                            $arrayPayMode['id_payment'] = $pay['id_payment'];
                                            $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'payment_mode_details');
                                        }
    					/* if(!empty($arrayPayMode)){
    						$cashPayInsert = $this->payment_modal->insertData($arrayPayMode,'payment_mode_details'); 
    					} */
    					
    				//	print_r($this->db->last_query());exit;
     				}
     				
				    if($this->config->item("integrationType") == 5){ // Generate A/C No and Receipt No
                					$this->generateAcNoOrReceiptNo($pay);
                				}
					else if($pay['receipt_no_set'] == 1 && empty($pay['receipt_no']))
					{  
						// $this->db->query('LOCK TABLES payment WRITE');
						$data['receipt_no'] = $this->payment_modal->generate_receipt_no($pay['id_scheme'],$pay['branch']);
						$data['payment_status'] = $this->payment_status['success'];
						$payment['status'] = $this->payment_modal->update_receipt($pay['id_payment'],$data);
						// $this->db->query('UNLOCK TABLES');
					// Generate account  number  based on one more settings Integ Auto//hh
						if($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set']==3)
						{
							if(empty($pay['scheme_acc_number']))
							{
								// $this->db->query('LOCK TABLES scheme_account WRITE');
								$scheme_acc_number = $this->payment_modal->account_number_generator($pay['id_scheme'],$pay['branch'],'');
								if($scheme_acc_number != NULL && $scheme_acc_number !="Not Allocated"){
									$schData['scheme_acc_number'] = $scheme_acc_number;
								}else{
									// $this->db->query('UNLOCK TABLES');
								}
							if($pay['gent_clientid'] ==1 && $scheme_acc_number != NULL){  
									$cliData = array(
													 "cliID_short_code"	=> $this->config->item('cliIDcode'),
													 "sync_scheme_code"	=> $pay['sync_scheme_code'],
													 "ac_no"			=> $scheme_acc_number
													);											
									$updateData['ref_no'] = $this->payment_modal->generateClientID($cliData);
							}
							if($pay['receipt_no_set'] == 1 && $this->config->item('integrationType') == 2){ 		//upd client id & acc no cus reg table//
									$cliData = array(
													 "cliID_short_code"	=> $this->config->item('cliIDcode'),
													 "sync_scheme_code"	=> $pay['sync_scheme_code'],
													 "ac_no"			=> $scheme_acc_number
													);											
									$schDatacus['ref_no'] = $this->payment_modal->generateClientID($cliData);
									$schDatacus['scheme_acc_number'] = $scheme_acc_number;
								//	$schDatacus['group_code'] =$pay['group_code'];
							}
							$updSchAc = $this->payment_modal->update_account($schData,$pay['id_scheme_account']);
							// $this->db->query('UNLOCK TABLES');
							 $updcusreg = $this->payment_modal->update_cusreg($schDatacus,$pay['id_scheme_account']); //acc no upd to cus reg tab//HH
							 $updtrans = $this->payment_modal->update_trans($schDatacus,$pay['id_scheme_account']); //Client Id upd to trans tab//
							}
						}
				}

						// [FIX] Update start_date on first payment (matching onPayTranStream -> upd_firstPayDateAsStartDate)
						if (empty($pay['scheme_acc_number'])) {
							$start_date_data = array('start_date' => $pay['date_payment']);
							if (!empty($pay['start_year'])) {
								$start_date_data['start_year'] = $pay['start_year'];
							}
							$this->payment_modal->update_account($start_date_data, $pay['id_scheme_account']);

							// Calculate maturity date based on start_date + maturity_days (matching onPayTranStream -> upd_maturityDate)
							$settings = $this->payment_modal->getSchemeData($pay['id_scheme_account']);
							if ($settings['maturity_type'] > 0 && $settings['maturity_days'] != null && $settings['maturity_days'] > 0) {
								$maturity_date = date('Y-m-d', strtotime("+" . $settings['maturity_days'] . " days", strtotime($pay['date_payment'])));
								$maturity_data = array('maturity_date' => $maturity_date);
								$this->payment_modal->update_account($maturity_data, $pay['id_scheme_account']);
							}
						}

						/*Agent and employee incentive starts
						Coded By Haritha 15-9-22
						*/
						//agent benefits credit
                                if($pay['agent_refferal'] == 1 && $paymtData['login_type'] == 'AGENT')
                                {
                                    $type=2; //1- employee 2- agent
                                    $agent_refral = $this->payment_modal->get_Incentivedata($pay['id_scheme'],$pay['id_scheme_account'],$type,$pay['id_payment']);
                		            if($agent_refral > 0)
                		            {
                		                foreach($agent_refral as $ag)
                		                {
                        			 	    if($ag['referal_amount'] > 0)
                        			 	    {
                        			 	        $res = $this->insertAgentIncentive($ag,$pay['id_scheme_account'],$pay['id_payment'],$paymtData['id_employee']);
                    			 	         }
                		                }
                		            }
                                }
                            //employee benefits credit
                                if($pay['emp_refferal'] == 1 && $paymtData['login_type'] == 'EMP')
                                {
                                    $type=1; //1- employee 2- agent
                                    $emp_refral = $this->payment_modal->get_Incentivedata($pay['id_scheme'],$pay['id_scheme_account'],$type,$pay['id_payment']);
                                    //print_r($emp_refral);exit;
                		            if($emp_refral > 0)
                		            {
                		                //$res = $this->insertEmployeeIncentive($emp_refral,$generic['id_scheme_account'],$status['insertID']);
                		                foreach($emp_refral as $emp)
                		                {
                        			 	    if($emp['referal_amount'] > 0)
                        			 	    {
                        			 	        $res = $this->insertEmployeeIncentive($emp,$pay['id_scheme_account'],$pay['id_payment']);
                        			 	        if($emp['credit_for'] == 1)
                        			 	        {
                        			 	            $this->customerIncentive($emp,$pay['id_scheme_account'],$pay['id_payment']);
                        			 	        }
                    			 	         }
                		                }
                		            }
                                }
                            // [FIX] Customer referral incentive (matching onPayTranStream L644-665)
                                if(isset($pay['allow_referral']) && $pay['allow_referral'] == 1) {
                                	$ref_data = $this->payment_modal->get_refdata($pay['id_scheme_account']);
                                	if(!empty($ref_data)) {
	                                	$ischkref = $this->payment_modal->get_ischkrefamtadd($pay['id_scheme_account']);
	                                	if($ref_data['ref_benifitadd_ins_type'] == 1 && $ref_data['referal_code'] != '' && ($ref_data['ref_benifitadd_ins'] == $ref_data['paid_installments']) && $ischkref == TRUE) {
	                                		$this->insert_referral_data($ref_data['id_scheme_account'], $ref_data);
	                                	} else if($ref_data['ref_benifitadd_ins_type'] == 0 && $ref_data['referal_code'] != '' && $ischkref == TRUE) {
	                                		$this->insert_referral_data($ref_data['id_scheme_account'], $ref_data);
	                                	}
	                                }
                                }
						//ends


						//Update First Payment Amount In Scheme Account
                                        $approval_type = $this->config->item('auto_pay_approval');
                                        //	print_r($approval_type);exit;
                                        if (($approval_type == 1 || $approval_type == 2 || $approval_type == 3)) {
                                            if (($pay['flexible_sch_type'] == 4 || $pay['flexible_sch_type'] == 8) && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "") && $pay['firstPayment_as_wgt'] == 1) { // Fix First payable as weight
                                                $fixPayable = array('firstPayment_wgt' => $pay['metal_weight']);
                                            } else if (($pay['firstPayamt_maxpayable'] == 1 || $pay['firstPayamt_as_payamt'] == 1) && ($pay['firstPayment_amt'] == '' || $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0)) {
                                                $fixPayable = array('firstPayment_amt' => $pay['payment_amount']);
                                            }
                                            if (sizeof($fixPayable) > 0) {    //AB
                                                $status = $this->payment_modal->update_account($fixPayable, $pay['id_scheme_account']);
                                            }
                                        }
                                        //RHR scheme starts...		
                                        $dt_pay = date('Y-m-d', strtotime(str_replace("/", "-", $pay['date_payment'])));
                                        $ins_cycle = $this->mobileapi_model->get_due_date($pay['due_type'], $dt_pay, $pay['id_scheme_account']);
                                        //	print_r($this->db->last_query());exit;
                                        if (sizeof($ins_cycle[0]) > 0) {
                                            $cycle_data = array(
                                                'due_date' => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),
                                                'due_date_to' => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),
                                                'grace_date' => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),
                                                'installment' => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),
                                                'is_limit_exceed' => (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),
                                            );
                                            $this->mobileapi_model->updData($cycle_data, 'id_payment', $pay['id_payment'], 'payment');
                                        }
                                        //RHR scheme ends....
                                        //update paid installments against account...
                                        $paid = $this->payment_modal->getPaidInsData($pay['id_scheme_account']);
                                        if (sizeof($paid) > 0) {
                                            $paid_ins = array('total_paid_ins' => $paid['paid_installments']);
                                            $this->payment_modal->updData($paid_ins, 'id_scheme_account', $pay['id_scheme_account'], 'scheme_account');
                                        }

					// Insert Data in Intermediate table
					 if($approval_type == 2 || $approval_type == 3 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3))
					 {
						if($this->config->item('integrationType') == 1){
							$this->insert_common_data_jil($pay['id_payment']);
						}else if($this->config->item('integrationType') == 2){
							$this->mobileapi_model->insert_common_data($pay['id_payment']);
						}
					 }
				}
			} 
       // }              
		// [DEBUG] Log trans_status result before check
		$_ts = $this->db->trans_status();
		$_dbg_err = $this->db->error();
		$_dbg_last = $this->db->last_query();
		$_dbg_log = "\n[DEBUG-adminAppSuccess] " . date('Y-m-d H:i:s') . 
			"\ntrans_status: " . ($_ts ? 'TRUE' : 'FALSE') .
			"\nDB Error: " . json_encode($_dbg_err) .
			"\nLast Query: " . substr($_dbg_last, 0, 500) .
			"\npay_ids: " . json_encode($pay_ids);
		file_put_contents('log/debug_adminapp_' . date('Y-m-d') . '.txt', $_dbg_log, FILE_APPEND | LOCK_EX);
		
		if($_ts === TRUE)
	    {
	    	$serv_model= self::SERV_MODEL;
	    	foreach ($pay_ids as $pay_id)
			{
    	    	if($service['sms'] == 1)
    	    	{
    	    		//$id=$payment['id_payment'];
    	    		$data =$this->$serv_model->get_SMS_data($serviceID,$pay_id);
    	    		$mobile =$data['mobile'];
    	    		$message = $data['message'];
    	    		if($this->config->item('sms_gateway') == '1'){
		    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
		    		}
		    		elseif($this->config->item('sms_gateway') == '2'){
		    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
		    		}
		    		elseif($this->config->item('sms_gateway') == '3'){
    					 $this->sms_model->sendSMS_SpearUC($mobile,$message,'',$service['dlt_te_id']);
    				}
    				elseif($this->config->item('sms_gateway') == '4'){
    					$this->sms_model->sendSMS_Asterixt($mobile,$message,'',$service['dlt_te_id']);
    				}
    				elseif($this->config->item('sms_gateway') == '5'){
    					$this->sms_model->sendSMS_Qikberry($mobile,$message,'',$service['dlt_te_id']);
    				}
    	    	}
    	    	if($service['serv_whatsapp'] == 1)
    			{
                	$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
                    $this->sms_model->send_whatsApp_message($mobile,$smsData);
                }
    	    	if($service['email'] == 1 && isset($pay['email']) && $pay['email'] != '')
    	    	{ 
    	    		//$invoiceData = $this->payment_modal->get_invoiceDataM(isset($payment['id_payment'])?$payment['id_payment']:'');
    	    		$invoiceData = $this->payment_modal->get_invoiceDataM($pay_id);
    	    		$to = $pay['email'];
    	    		$subject = "Reg - ".$this->comp['company_name']." payment for the purchase plan";
    	    		$data['payData'] = $invoiceData[0];
    	    		$data['type'] = 2;
    	    		$data['company_details'] = $this->comp;
    	    		$message = $this->load->view('include/emailPayment',$data,true);
    	    		$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");	
    	    	}	
			}	
		 	$this->db->trans_commit();
    		redirect('paymt/adminapp/success');
    	}else{
		 	$this->db->trans_rollback();
		 	redirect('paymt/adminapp/failed');
		}
	}
    function adminapp($type){
     	echo "Payment ".$type.".Please wait we are redirection you to the app";
    }
	//for mobile success
	function successMURL($payData = "", $gateway = "")
    {
        if($gateway == ""){
            $payData = $_POST;
        } 
        $resData['txnid'] = $payData['txnid'];
        $resData['amount'] = isset($payData['amount'])?$payData['amount']:"";
         if(sizeof($payData) > 0 && (!empty($payData['txnid']) && $payData['txnid'] != NULL))
         {	 
            $trans_id = $payData['txnid']; 
            $serv_model= self::SERV_MODEL;
            //user detail
            /*$user = array(
                         'firstname' => isset($payData['firstname'])?$payData['firstname']:NULL, 
                         'lastname'  => $payData['lastname'], 
                         'mobile'    => $payData['phone'], 
                         'email'    => $payData['email']
             		 );*/
            $transData = array();
            $pay = $this->payment_modal->getWalletPaymentContent($trans_id); 
            if($pay['redeemed_amount'] > 0){ 
            	  $transData = array('mobile' 			=> $pay['mobile'],
            						 'actual_trans_amt' => $pay['actual_trans_amt'],
            						 'available_points'	=> ($pay['isAvail'] == 0 ?0:$pay['available_points']),
            						 'isAvail'			=> ($pay['isAvail'] == 0 ?0:1),
            						 'redeemed_amount'	=> $pay['redeemed_amount'],
            						 'id_payment'       => $pay['id_payment'],
            						 'txnid'            => $trans_id.'- D',
            						 'branch'           => $pay['branch'],
                					 'walletIntegration'=> $pay['walletIntegration'],
    		    					 'wallet_balance_type' => $pay['wallet_balance_type'],
    		    					 'wallet_points' => $pay['wallet_points'],
    		    					 'wallet_amt_per_points' => $pay['wallet_amt_per_points']
            						);  
            	if(!empty($transData)){
            	    $this->insertWalletTrans($transData);  
            	}
            }
            $updateData = array(
            				//"bank_name"			 =>	(isset($payData['issuing_bank']) ? $payData['issuing_bank'] : NULL),
            				"payment_mode"       => (isset($payData['mode']) ? $payData['mode'] : NULL),
            				"card_no"			 => (isset($payData['mode']) && ($payData['mode'] == 'CC' || $payData['mode'] == 'DC') ? $payData['cardnum'] :NULL ),
            				"card_holder"		 => (isset($payData['mode']) && ($payData['mode'] == 'CC' || $payData['mode'] == 'DC') ? (isset($payData['name_on_card'])?$payData['name_on_card']:NULL) :NULL ),
            				"payu_id"            => (isset($payData['mihpayid']) ? $payData['mihpayid'] : NULL),
            			//	"remark"             => (isset($payData['field9']) ? $payData['field9']."-successmurl" :NULL),
            				"payment_ref_number" => (isset($payData['bank_ref_num']) ? $payData['bank_ref_num'] : NULL ),
            				"payment_status"     => ( $this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2? $this->payment_status['success']:$this->payment_status['awaiting'])
            			);
            	$payment = $this->payment_modal->updateGatewayResponse($updateData,$trans_id);
                 //echo $this->db->last_query();exit;
                $serviceID = 7;
            	$service = $this->services_modal->checkService($serviceID); 
             if($payment['status'] == true)
            { 
                if( $this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2){
					$payIds = $this->payment_modal->getPayIds($trans_id);
				    if(sizeof($payIds) > 0)
    				{
							$updData = array("last_payment_on" => NULL);
							$this->payment_modal->updData($updData,'id_customer',$payIds[0]['id_customer'],'customer'); 
    						foreach ($payIds as $pay)
    						{
    						    
    						     //insert into payment mode details table....
    						    if($payData['mode'] != NULL)
                 				{
                 					$arrayPayMode=array(
                    								'id_payment'         => $pay['id_payment'],
                							        'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
                    								'payment_date'		 => date("Y-m-d H:i:s"),
                    								'created_time'	     => date("Y-m-d H:i:s"),
                    								"payment_mode"       => ($payData['mode']== "CREDIT_CARD" ? "CC":($payData['mode'] == "DEBIT_CARD" ? "DC":($payData['mode'] == "NET_BANKING" ? "NB":$payData['mode']))),
                            						//"payu_id"            => $trans_id,
                            						"remark"             => "mbl-status",
                            						"payment_ref_number" => $trans_id,
            				                        "payment_status"     => ( $this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2? $this->payment_status['success']:$this->payment_status['awaiting'])
                            					    );
                            					    
                            					   
                					if(!empty($arrayPayMode)){
                						$cashPayInsert = $this->payment_modal->insertData($arrayPayMode,'payment_mode_details'); 
                					}
                					
                 				}
    						    
    						    
    							if($this->config->item("integrationType") == 5){ // Generate A/C No and Receipt No
	            					$this->generateAcNoOrReceiptNo($pay);
	            				}
                                if($pay['allow_referral'] == 1){
                        		    $ref_data	=	$this->payment_modal->get_refdata($pay['id_scheme_account']);
                        			$ischkref	=	$this->payment_modal->get_ischkrefamtadd($pay['id_scheme_account']); 
                        	 		if($ref_data['ref_benifitadd_ins_type'] == 1 && $ref_data['referal_code'] != '' && ($ref_data['ref_benifitadd_ins'] == $ref_data['paid_installments']) && $ischkref == TRUE){	
                        				$this->insert_referral_data($ref_data['id_scheme_account'],$ref_data);
                        			}else if($ref_data['ref_benifitadd_ins_type'] == 0 && $ref_data['referal_code'] != '' && $ischkref == TRUE){
                        				$this->insert_referral_data($ref_data['id_scheme_account'],$ref_data);
                        			} 
                        	 	}
                        	 	if($pay['one_time_premium'] == 1){
                					$acData = array('firstPayment_amt' => $pay['payment_amount'], 'date_upd' => date("Y-m-d H:i:s"));
                					$this->payment_modal->updData($acData,'id_scheme_account',$pay['id_scheme_account'],'scheme_account');
                				}
    						    // Generate receipt number
    								if($pay['receipt_no_set'] == 1 && ($this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2) )
    								{  
    									$data['receipt_no'] = $this->payment_modal->generate_receipt_no($pay['id_scheme'],$pay['branch']);
    									$payment['status'] = $this->payment_modal->update_receipt($pay['id_payment'],$data);
    								}// Generate account  number  based on one more settings Integ Auto//hh
    								if($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set']==3)
    								{
    									if($pay['scheme_acc_number'] == '' ||  $pay['scheme_acc_number'] == null)
    									{
    									    $ac_group_code = NULL;
    										// Lucky draw
											if($pay['is_lucky_draw'] == 1 ){ // Based on scheme settings 
												// Update Group code in scheme_account table 
												$updCode = $this->payment_modal->updateGroupCode($pay); 
												$ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
											}
											// $this->db->query('LOCK TABLES scheme_account WRITE');
    										$scheme_acc_number = $this->payment_modal->account_number_generator($pay['id_scheme'],$pay['branch'],$ac_group_code);
    										if($scheme_acc_number != NULL && $scheme_acc_number !="Not Allocated"){
    											$schData['scheme_acc_number'] = $scheme_acc_number;
    										}else{
												// $this->db->query('UNLOCK TABLES');
											}
    										if($pay['gent_clientid'] ==1 && $scheme_acc_number != NULL){  
    											$cliData = array(
    															 "cliID_short_code"	=> $this->config->item('cliIDcode'),
    															 "sync_scheme_code"	=> $pay['sync_scheme_code'],
    															 "ac_no"			=> $scheme_acc_number
    															);											
    											$schData['ref_no'] = $this->payment_modal->generateClientID($cliData);	 
    										}
    											if($pay['receipt_no_set'] == 1 && $this->config->item('integrationType') == 2){ 	//upd client id & acc no cus reg table//
    											$cliData = array(
    															 "cliID_short_code"	=> $this->config->item('cliIDcode'),
    															 "sync_scheme_code"	=> $pay['sync_scheme_code'],
    															 "ac_no"			=> $scheme_acc_number
    															);											
    											$schDatacus['ref_no'] = $this->payment_modal->generateClientID($cliData);
    											$schDatacus['scheme_acc_number'] = $scheme_acc_number;
    										//	$schDatacus['group_code'] =$pay['group_code'];
    										}
    										$updSchAc = $this->payment_modal->update_account($schData,$pay['id_scheme_account']);
    										// $this->db->query('UNLOCK TABLES');
    										$updcusreg = $this->payment_modal->update_cusreg($schDatacus,$pay['id_scheme_account']); //acc no upd to cus reg tab//hh
    									}
    								}
    								 //Update First Payment Amount In Scheme Account
    								 if(($this->config->item('auto_pay_approval') == 1 || $this->config->item('auto_pay_approval') == 2 || $this->config->item('auto_pay_approval') == 3) && ($pay['firstPayamt_maxpayable']==1 || $pay['firstPayamt_as_payamt']==1) && ($pay['firstPayment_amt'] == '' ||  $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0))
    	    						 {
    	    							if($pay['flexible_sch_type'] == 4 && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "") ){ // Fix First payable as weight
    										$fixPayable = array('firstPayment_wgt'  =>  $pay['metal_weight'] );
    									}else{
    										$fixPayable = array('firstPayment_amt'  =>  $pay['payment_amount'] );
    									}
    									$status = $this->payment_modal->update_account($fixPayable,$pay['id_scheme_account']);
    	    						 }
    								 if($this->config->item('auto_pay_approval') == 2 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3))
    								 {
    									 /*if($pay['edit_custom_entry_date']==1)
    										{
    												$receipt['custom_entry_date']=$pay['custom_entry_date'];
    												$payment['status'] = $this->payment_modal->update_receipt($pay['id_payment'],$receipt);
    										}*/
    									  //$this->mobileapi_model->insert_common_data($pay['id_payment']);  
    								    if($this->config->item('integrationType') == 1){
    								        $this->insert_common_data_jil($pay['id_payment']);
    								    }else if($this->config->item('integrationType') == 2){
    								        $this->mobileapi_model->insert_common_data($pay['id_payment']);
    								    }
    								 }

    								 // [FIX] Update start_date on first payment (matching onPayTranStream -> upd_firstPayDateAsStartDate)
    								 if (empty($pay['scheme_acc_number'])) {
    								 	$start_date_data = array('start_date' => $pay['date_payment']);
    								 	if (!empty($pay['start_year'])) {
    								 		$start_date_data['start_year'] = $pay['start_year'];
    								 	}
    								 	$this->payment_modal->update_account($start_date_data, $pay['id_scheme_account']);

    								 	// Calculate maturity date (matching onPayTranStream -> upd_maturityDate)
    								 	$settings = $this->payment_modal->getSchemeData($pay['id_scheme_account']);
    								 	if ($settings['maturity_type'] > 0 && $settings['maturity_days'] != null && $settings['maturity_days'] > 0) {
    								 		$maturity_date = date('Y-m-d', strtotime("+" . $settings['maturity_days'] . " days", strtotime($pay['date_payment'])));
    								 		$maturity_data = array('maturity_date' => $maturity_date);
    								 		$this->payment_modal->update_account($maturity_data, $pay['id_scheme_account']);
    								 	}
    								 }

    								 // [FIX] Due date/installment update (matching onPayTranStream -> updatedue_details)
    								 $dt_pay = date('Y-m-d', strtotime(str_replace("/", "-", $pay['date_payment'])));
    								 $ins_cycle = $this->payment_modal->get_due_date($pay['due_type'], $dt_pay, $pay['id_scheme_account']);
    								 if (sizeof($ins_cycle[0]) > 0) {
    								 	$cycle_data = array(
    								 		'due_date' => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),
    								 		'due_date_to' => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),
    								 		'grace_date' => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),
    								 		'installment' => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),
    								 		'is_limit_exceed' => (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),
    								 	);
    								 	$this->payment_modal->updData($cycle_data, 'id_payment', $pay['id_payment'], 'payment');
    								 }

    								 // [FIX] Update paid installments count (matching onPayTranStream -> getPaidInsData)
    								 $paid = $this->payment_modal->getPaidInsData($pay['id_scheme_account']);
    								 if (sizeof($paid) > 0) {
    								 	$paid_ins = array('total_paid_ins' => $paid['paid_installments']);
    								 	$this->payment_modal->updData($paid_ins, 'id_scheme_account', $pay['id_scheme_account'], 'scheme_account');
    								 }
    								 
    								if($service['sms'] == 1)
                                	{
                                		$id=$pay['id_payment'];
                                		$data =$this->$serv_model->get_SMS_data($serviceID,$id);
                                		$mobile =$data['mobile'];
                                		$message = $data['message'];
                                		if($this->config->item('sms_gateway') == '1'){
                    		    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
                    		    		}
                    		    		elseif($this->config->item('sms_gateway') == '2'){
                    		    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
                    		    		}
                    		    		elseif($this->config->item('sms_gateway') == '3'){
                        					 $this->sms_model->sendSMS_SpearUC($mobile,$message,'',$service['dlt_te_id']);
                        				}
                        				elseif($this->config->item('sms_gateway') == '4'){
                        					$this->sms_model->sendSMS_Asterixt($mobile,$message,'',$service['dlt_te_id']);
                        				}
                        				elseif($this->config->item('sms_gateway') == '5'){
                        					$this->sms_model->sendSMS_Qikberry($mobile,$message,'',$service['dlt_te_id']);
                        				}
                                	}
                                	if($service['serv_whatsapp'] == 1)
                        			{
                                    	$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
                                        $this->sms_model->send_whatsApp_message($mobile,$smsData);
                                    }
                                	if($service['email'] == 1 && isset($payData['email']) && $payData['email'] != '')
                                	{ 
                                		$invoiceData = $this->payment_modal->get_invoiceDataM(isset($pay['id_payment'])?$pay['id_payment']:'');
                                		$to = $payData['email'];
                                		$subject = "Reg - ".$this->comp['company_name']." payment for the purchase plan";
                                		$data['payData'] = $invoiceData[0];
                                		$data['type'] = 2;
                                		$data['company_details'] = $this->comp;
                                		$message = $this->load->view('include/emailPayment',$data,true);
                                		$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");	
                                	}	
                                	
            	
    						}	
    				}
                }

            	if($gateway != ""){
            	    if($gateway == 6)
            	    {
            	       $url = base_url().'index.php/paymt/hdfcTransStatus/success/'.$resData['txnid'].'/'.$resData['amount'];
            	       echo json_encode($url);
            	    }else{
            	         redirect('paymt/hdfcTransStatus/success/'.$resData['txnid'].'/'.$resData['amount']);
            	    }
            	}	
            }else{
                if($gateway != ""){
                    if($gateway == 6)
            	    {
            	       $url = base_url().'index.php/paymt/hdfcTransStatus/success/'.$resData['txnid'].'/'.$resData['amount'];
            	       echo json_encode($url);
            	    }else{
                    redirect('paymt/hdfcTransStatus/completed/'.$resData['txnid'].'/'.$resData['amount']);
            	    }
                }
            }	
        }else{
            if($gateway != ""){
                if($gateway == 6)
            	    {
            	       $url = base_url().'index.php/paymt/hdfcTransStatus/success/'.$resData['txnid'].'/'.$resData['amount'];
            	       echo json_encode($url);
            	    }else{
                redirect('paymt/hdfcTransStatus/completed/'.$resData['txnid'].'/'.$resData['amount']);
            	    }
        	}
        }
    }
	//for mobile payment failure
	function failureMURL($payData = "", $gateway = "")
	{
		if($gateway == ""){
            $payData = $_POST;
        }  
	    $user = array(
		                'firstname' => $payData['firstname'], 
		                'lastname'  => $payData['lastname'], 
		                'mobile'    => $payData['phone'],
	                    'email'    => $payData['email']					
					 );
		$updateData = array(
								"bank_name"			 =>	(isset($payData['issuing_bank']) ? $payData['issuing_bank'] : NULL),
								"payment_mode"       => (isset($payData['mode']) ? $payData['mode'] : NULL),
								"card_no"			 => (isset($payData['mode']) && ($payData['mode'] == 'CC' || $payData['mode'] == 'DC') ? $payData['cardnum'] :NULL ),
								"payu_id"             => (isset($payData['mihpayid']) ? $payData['mihpayid'] : NULL),
								"remark"             => (isset($payData['field9']) ? $payData['field9'] : NULL),
								"payment_ref_number" => (isset($payData['bank_ref_num']) ? $payData['bank_ref_num'] : NULL ),
								"payment_status"     => $this->payment_status['failure']
							);
		if(!empty($payData['txnid']) && $payData['txnid'] != NULL){
	    	$payment = $this->payment_modal->updateGatewayResponse($updateData,$payData['txnid']);
	    	if($payment['status'] == true)
    		{
    			$payData = $this->payment_modal->get_invoiceDataM($payment['id_payment'],$user['mobile']);
    			//print_r($payData);exit;
    			$serviceID = 7;
    			$service = $this->services_modal->checkService($serviceID);
    		//	print_r($service);exit;
    			if($service['sms'] == 1)
    			{
    					$id=$payment['id_payment'];
    					$data =$this->services_modal->get_SMS_data($serviceID,$id);
    					$mobile =$data['mobile'];
    					$message = $data['message'];
    					if($this->config->item('sms_gateway') == '1'){
			    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
			    		}
			    		elseif($this->config->item('sms_gateway') == '2'){
			    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
			    		}
			    		elseif($this->config->item('sms_gateway') == '3'){
        					 $this->sms_model->sendSMS_SpearUC($mobile,$message,'',$service['dlt_te_id']);
        				}
        				elseif($this->config->item('sms_gateway') == '4'){
        					$this->sms_model->sendSMS_Asterixt($mobile,$message,'',$service['dlt_te_id']);
        				}
        				elseif($this->config->item('sms_gateway') == '5'){
        					$this->sms_model->sendSMS_Qikberry($mobile,$message,'',$service['dlt_te_id']);
        				}
    			}
    			if($service['serv_whatsapp'] == 1)
    			{
                	$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
                    $this->sms_model->send_whatsApp_message($mobile,$smsData);
                }
    		   //print_r($payData[0]['email']);exit;			
    			if($service['email'] == 1 && isset($payData[0]['email']) && $payData[0]['email'] != '')
    			{
    				$to = $payData[0]['email'];
    				//print_r($to);exit;
    				$subject = "Reg - ".$this->comp['company_name']." payment for the purchase plan";
    				$data['payData'] = $payData[0];
    			    $data['type'] = 3;
    				$data['company_details'] = $this->comp;
    				$message = $this->load->view('include/emailPayment',$data,true);
    				$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");	
    			}
    		}
		}
		if($gateway != ""){
		    redirect('paymt/hdfcRedirect/failed');
		}
	}
	//for mobile payment cancel
	function cancelMURL($payData = "" , $gateway = "")
	{ 
		if($gateway == ""){
        	$payData = $_POST;
        }
		$updateData = array(
							"bank_name"			 =>	(isset($payData['issuing_bank']) ? $payData['issuing_bank'] : NULL),
							"payment_mode"       => (isset($payData['mode']) ? $payData['mode'] : NULL),
							"card_no"			 => (isset($payData['mode']) && ($payData['mode'] == 'CC' || $payData['mode'] == 'DC') ? $payData['cardnum'] :NULL ),
							"payu_id"             => (isset($payData['mihpayid']) ? $payData['mihpayid'] : NULL),
							"remark"             => (isset($payData['field9']) ? $payData['field9'] : NULL),
							"payment_ref_number" => (isset($payData['bank_ref_num']) ? $payData['bank_ref_num'] : NULL ),
							"payment_status"     => $this->payment_status['cancel'] 
						); 
		if(!empty($payData['txnid']) && $payData['txnid'] != NULL){
			$payment = $this->payment_modal->updateGatewayResponse($updateData,$payData['txnid']);			
			$serviceID = 7;
			$service = $this->services_modal->checkService($serviceID);  
			if($payment['status'] == true)
			{ 
				if($service['sms'] == 1) {
						$data =$this->services_modal->get_SMS_data($serviceID,$id);
						$mobile =$data['mobile'];
						$message = $data['message'];
						if($this->config->item('sms_gateway') == '1'){
			    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
			    		}
			    		elseif($this->config->item('sms_gateway') == '2'){
			    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
			    		}
			    		elseif($this->config->item('sms_gateway') == '3'){
        					 $this->sms_model->sendSMS_SpearUC($mobile,$message,'',$service['dlt_te_id']);
        				}
        				elseif($this->config->item('sms_gateway') == '4'){
        					$this->sms_model->sendSMS_Asterixt($mobile,$message,'',$service['dlt_te_id']);
        				}
        				elseif($this->config->item('sms_gateway') == '5'){
        					$this->sms_model->sendSMS_Qikberry($mobile,$message,'',$service['dlt_te_id']);
        				}
				} 	
				if($service['serv_whatsapp'] == 1)
    			{
                	$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
                    $this->sms_model->send_whatsApp_message($mobile,$smsData);
                }
				if($service['email'] == 1 && isset($payData[0]['email']) && $payData[0]['email'] != '')
				{
					$to = $payData[0]['email'];
					$subject = "Reg - ".$this->comp['company_name']." payment for the purchase plan";
					$data['payData'] = $payData[0];
					$data['type'] = 3;
					$message = $this->load->view('include/emailPayment',$data,true);
					$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");	
				} 			
			}	 
		} 
		if($gateway != ""){
            redirect('paymt/hdfcRedirect/cancel');
        }
	}
	function pdc_report()
	{
		$payment['pdc'] = $this->payment_modal->get_pdc_report();
		$payment['total'] = $this->payment_modal->get_pdcs();
		$data['content'] = $payment;
	  	$pageType = array('page' => 'payment','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER.'pdc_report';
		$this->load->view('layout/template', $data);
	}
	//Promotion sms and otp setting
	function send_sms($mobile,$message)
	{	
	$url = $this->sms_data['sms_url'];
	$senderid  = $this->sms_data['sms_sender_id'];
	 if(($this->sms_chk['debit_sms']!=0)){
		$arr = array("@customer_mobile@" => $mobile,"@message@" => str_replace(array("\n","\r"), '', $message),"@senderid@" => $senderid);
		$user_sms_url = strtr($url,$arr);
    	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $user_sms_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
		$result = curl_exec($ch);
		curl_close($ch);
		unset($ch);
		$status=$this->update_otp();		
		if($status==1){		
		   return TRUE;
		}
		 return FALSE;
	}else{
		return FALSE;}
  }
  function update_otp()
  {
		$query_validate=$this->db->query('UPDATE sms_api_settings SET debit_sms = debit_sms - 1 
				WHERE id_sms_api =1 and debit_sms > 0');  			
	         if($query_validate>0)
			{
				return true;
			}else{
				return false;
			}
  }
 //Promotion sms and otp setting
	//function split_payment($payment,$dues="",$type="")
	function split_payment($id_payment)
	{
		    $serv_model= self::SERV_MODEL;
		$payment = $this->payment_modal->getPaymentByID($id_payment);
		if(!empty($payment))
		{
			$date_paid   		 = $payment['date_payment'];
			$txnid        		 = $payment['id_transaction'];
		    $dues                = $payment['no_of_dues'] - 1;   
			for($i=1;$i<=$dues;$i++)
			{
			   $paid_date = date('Y-m-d H:i:s', strtotime($date_paid.' +'.$i.' months'));
			   $insertData = array(
									"id_scheme_account"	 => $payment['id_scheme_account'],
									"id_transaction" 	 => $txnid."-".$i, 
									"payment_amount" 	 => $payment['payment_amount'], 
									"payment_type" 	     => "Payu Checkout", 
									"date_payment" 		 => $paid_date,
									"bank_name"			 =>	(isset($payment['issuing_bank']) ? $payment['issuing_bank'] : NULL),
									"payment_mode"       => (isset($payment['payment_mode']) ? $payment['payment_mode'] : NULL),
									"card_no"			 => (isset($payment['card_no']) ? $payment['card_no'] : NULL),
									"card_holder"			 => (isset($payment['card_holder']) ? $payment['card_holder'] : NULL),
									"payment_ref_number" => (isset($payment['payment_ref_number']) ? $payment['payment_ref_number'] : NULL ),
									"remark"             =>  ' Splitted from transactionid '.$txnid.' paid on '.$date_paid,   
									"payment_status"     => ( $this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success']:$this->payment_status['awaiting'])
								);
				$split_data = $this->payment_modal->addPayment($insertData);   
				$serviceID =7;
				$service = $this->services_modal->checkService($serviceID);
				if($split_data['status'] == true && isset($split_data['insertID']))
				{
					$id=$split_data['insertID'];
					if($service['sms'] == 1)
					{
						$data =$this->$serv_model->get_SMS_data($serviceID,$id);
						$mobile =$data['mobile'];
						$message = $data['message'];
						if($this->config->item('sms_gateway') == '1'){
			    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
			    		}
			    		elseif($this->config->item('sms_gateway') == '2'){
			    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
			    		}
			    		elseif($this->config->item('sms_gateway') == '3'){
        					 $this->sms_model->sendSMS_SpearUC($mobile,$message,'',$service['dlt_te_id']);
        				}
        				elseif($this->config->item('sms_gateway') == '4'){
        					$this->sms_model->sendSMS_Asterixt($mobile,$message,'',$service['dlt_te_id']);
        				}
        				elseif($this->config->item('sms_gateway') == '5'){
        					$this->sms_model->sendSMS_Qikberry($mobile,$message,'',$service['dlt_te_id']);
        				}
					}
					if($service['serv_whatsapp'] == 1)
        			{
                    	$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
                        $this->sms_model->send_whatsApp_message($mobile,$smsData);
                    }
					$invoiceData = $this->payment_modal->get_paymenMailData($id);
					if($service['email'] == 1 && isset($invoiceData[0]['email']) && $invoiceData[0]['email'] != '')
					{ 
						$to = $invoiceData[0]['email'];
						$subject = "Reg - ".$this->comp['company_name'].($payment['due_type'] =='A'?' advance ':' pending ')." payment for the purchase plan";
						$data['payData'] = $invoiceData[0];
						$data['type'] = 3;
						$data['company_details'] = $this->comp;
						$message = $this->load->view('include/emailPayment',$data,true);
						$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");	
					}		
				}				
			}
		}
	}
	public function ispanReq($accId)
	{
			$data = $this->payment_modal->checkPanNo($accId);
			if($data == 1){
				$ispanReq = array("ispan_req" => 1);
				$cus_data = $this->registration_model->updateCustomer($ispanReq);
			}
			echo $data;
	}
	function wallet_transactionDB($payamt,$totamtuse_wallet){
	  $wallet_acc = $this->payment_modal->get_wallet_accounts();		
	   if(!empty($wallet_acc)){
			foreach($wallet_acc as $wallet){
				if($wallet['active']=='Active'){
					 $insertData=array( 
			                            'id_wallet_account'  => (isset($wallet['id_wallet_account']) && $wallet['id_wallet_account']!=''? $wallet['id_wallet_account']:NULL),
			                            'id_employee' 	      =>  2,
			                            'transaction_type'    => 1,
			                            'value' 		      => (isset($payamt) && $payamt!=''? $payamt:0),
			                            'date_transaction'    => date('Y-m-d H:i:s'),
			                            'description'         => 'Scheme Payment'
			                         );
						$status = $this->payment_modal->wallet_settingDB($wallet['id_wallet_account'],$insertData);						
						 /*if($status && $totamtuse_wallet){							 
							 $scheme_success = array("successMsg" => 'Payment successful. Thanks for your payment with '.$this->comp['company_name'].'');
							 $this->session->set_flashdata($scheme_success);
							  redirect("/paymt/payment_history");
						 }else if($status && $totamtuse_wallet==FALSE){							 
							return TRUE;
						 }*/
				}
			}
	   }
	   return true;
	}
	/*  KVP -- Wallet Module Starts  */
    function insertWalletTrans($tran){ 
	  if($tran)
	  {
		 $redeemed_amount=$tran['redeemed_amount'];
		 if($tran['wallet_balance_type']==1)
		 {
			$redeemed_amount=(($redeemed_amount/$tran['wallet_amt_per_points'])*$tran['wallet_points']);
		 }
	  	$transDetailData = array(	
		            				"amount" 		 => $tran['actual_trans_amt'],
		            				"date_add"       => date('Y-m-d H:i:s'),
		            				"remark" 		 => 'Debited for purchase plan payment '.$tran['txnid'],
		            				'trans_points' 	 => $redeemed_amount
		            			); 
	  	if($tran['walletIntegration'] == 0){ // 0 - No integration, 1 - Req integration as like SSS
		  	$transDetailData['trans_type'] = 2;
			$updwallet = $this->payment_modal->updwallet($transDetailData,$tran['mobile']);
			$r = array("status" 	=> $updwallet);
			return $r;
		}else{
		   	   $cat_code = 'SS';
		   	   $wal_cat_settings = $this->payment_modal->getWcategorySettings($cat_code);
		   	   // Begin the transaction
		   	   $this->db->trans_begin();
			   if($wal_cat_settings){
			   	   $transData = array(	
		            				"record_to" 	 =>  1, //1 - offline , 2 - online
		            				"entry_date" 	 =>  date('Y-m-d H:i:s'), 
		            				"bill_no" 	 	 =>  $tran['txnid'],
		            				"id_branch" 	 =>  $tran['branch'],
		            				"trans_type"     =>  2,
		            				"mobile" 	 	 =>  $tran['mobile'],
		            				"is_transferred" =>  'N',
		            				"is_modified"    =>  0,
		            				"date_add"       =>  date('Y-m-d H:i:s'), 
		            				'actual_redeemed' => $redeemed_amount,
		            				"use_points" 	 => 1,
				            		"redeem_req_pts" => $redeemed_amount,
		            				"record_type"	 =>  2// 1 - offline , 2 - online
		            			);
		           $transDetailData['id_wcat_settings'] = $wal_cat_settings['id_wcat_settings'];
	           	   $transDetailData['category_code'] = $cat_code;
			   	   $wallAccount = $this->payment_modal->getInterWalletCustomer($transData['mobile']); 
			   	   $allow = FALSE;
			   	   if($wallAccount['status']){
				   		$upd_data = array(	
                    				"available_points"  => ($tran['available_points']-$redeemed_amount),
                    				"last_update"       => date('Y-m-d H:i:s'),
                    				"mobile" 	 	 	=> $tran['mobile'],
                    			);
				   		$w_status  = $this->payment_modal->updInterWalletAcc($upd_data);
				   		if($w_status){
							$allow = TRUE;
						}
				   }else{
				   		$transDetailData['trans_type'] = 2;
				   		$transDetailData['bill_no'] = $tran['txnid'];
				   		$transDetailData['id_branch'] = $tran['branch'];
				   		$updwallet = $this->payment_modal->updwallet($transDetailData,$tran['mobile']);
				   		$msg = 'Thank you for using '.$this->comp['company_name'].' wallet. Redeemed '.number_format($redeemed_amount,'2','.','').' points for scheme payment.'; 	
						if($updwallet){
							$smsData[] = array('mobile' => $tran['mobile'],
											   'message'=> $msg
							); 
							$r = array('message' 	=> 'Sucessfully updated',
										"status" 	=> TRUE  
										);
					    }else{
							$r = array('message' 	=> $this->db->_error_message(),
										"query" 	=> $this->db->last_query(),
										"status" 	=> false
										);
						}
				   }
				   if($allow){
			           $t_status  = $this->payment_modal->insertData($transData,'inter_wallet_trans');
				   		if($t_status['status']){
				   			$transDetailData['id_inter_wallet_trans'] = $t_status['insertID'];
				   			$td_status = $this->payment_modal->insertData($transDetailData,'inter_wallet_trans_detail');
				   			if($td_status['status']){
					   	   /*if(!$wallAccount['status']){
					   	        $ac_data = array(	
	                    				"available_points"  => 0,
	                    				"date_add"          => date('Y-m-d H:i:s'),
	                    				"mobile" 	 	 	=> $tran['mobile'],
	                    			);
						   		$w_status  = $this->payment_modal->insertData($ac_data,'inter_wallet_account');  
						   		if($w_status['status']){
									$allow = TRUE;
								}
						   }else{
						   		$upd_data = array(	
		                    				"available_points"  => (float)($tran['available_points']-$tran['redeemed_amount']),
		                    				"last_update"       => date('Y-m-d H:i:s'),
		                    				"mobile" 	 	 	=> $tran['mobile'],
		                    			);
						   		$w_status  = $this->payment_modal->updInterWalletAcc($upd_data);
						   		if($w_status){
									$allow = TRUE;
								}
						   } */  			   
						   		$transDetailData['trans_type'] = 2;
						   		$transDetailData['bill_no'] = $tran['txnid'];
						   		$transDetailData['id_branch'] = $tran['branch'];
						   		$updwallet = $this->payment_modal->updwallet($transDetailData,$tran['mobile']);
							   	$syncWalData = array(
													"points"  	=> (float) ($tran['available_points']-$redeemed_amount),
								    				"mobile" 	=> $tran['mobile'],
													);
								if($tran['branch'] != ''){
									foreach($this->branch as $bran){
										$syncWalData['branch_'.$bran] = 0;								
									}
								}else{
									$syncWalData['branch_1'] = 0;
								}
							    $isExist = $this->payment_modal->getSyncWalletByMobile($tran['mobile']);
								if($isExist){
									$syncWalData['last_update'] = date('Y-m-d H:i:s');
									$this->payment_modal->updateSyncWal($syncWalData);
								}else{
									$syncWalData['date_add'] = date('Y-m-d H:i:s');
									$this->payment_modal->insertData($syncWalData,'inter_sync_wallet');
								}
							//	$msg = 'Thank you for purchasing at Saravana Selvarathnam Retail Pvt Ltd . Your Wallet Balance '.number_format($tran['available_points']+$tran['redeemed_amount'],'2','.','').' points. Redeemed '.number_format($tran['redeemed_amount'],'2','.','').' points. New Wallet Balance '.number_format($tran['available_points'],'2','.','').' Points.'; 
	    						$msg = 'Thank you for purchasing at '.$this->comp['company_name'].' . Your Wallet Balance '.number_format($tran['available_points'],'2','.','').' points. Redeemed '.number_format($redeemed_amount,'2','.','').' points. New Wallet Balance '.number_format(($tran['available_points']-$redeemed_amount),'2','.','').' Points.'; 
								if($updwallet){
									$smsData[] = array('mobile' => $tran['mobile'],
													   'message'=> $msg
									);
								}
			   			}else{
							$r = array('message' 	=> $this->db->_error_message(),
							"query" 	=> $this->db->last_query(),
										"status" 	=> false
										);
						}
					  }else{
								$r = array('message' 	=> $this->db->_error_message(),
								"query" 	=> $this->db->last_query(),
											"status" 	=> false
											);
					  }
					}else{
						$r = array('message' 	=> $this->db->_error_message(),
						"query" 	=> $this->db->last_query(),
									"status" 	=> false
									);								
					}
			   }else{
			   		$r = array('message' 	=> $this->db->_error_message(),
								"query" 	=> $this->db->last_query(),
								"status" 	=> false
								);							
			   } 
		  	  if( $this->db->trans_status() === TRUE ){
			  	$this->db->trans_commit();
			  	$this->send_sms_wallet($smsData);
			  	$r = array('message' 	=> 'Sucessfully updated',
							"status" 	=> TRUE  ,
							"query" 	=> $this->db->last_query(),
							);
			  }else{
			  	$this->db->trans_rollback();
			  	 $r = array('message' 	=> 'Something went worng',
							"status" 	=> false  ,
							"query" 	=> $this->db->last_query(),
							);
			  }	
			   return $r;
		   }
	   }
	   $r = array('message' 	=> 'No records found',
					"status" 	=> false  
					);
	   return $r;
	} 
	function send_sms_wallet($data){
	    $serviceID = 17;
		$service = $this->services_modal->checkService($serviceID);
		if($service['sms'] == 1)
		{
				//$data =$this->$sms_model->get_SMS_data($serviceID,$id);
				$mobile =$data['mobile'];
				$message = $data['message'];
				if($this->config->item('sms_gateway') == '1'){
	    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
	    		}
	    		elseif($this->config->item('sms_gateway') == '2'){
	    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
	    		}
	    		elseif($this->config->item('sms_gateway') == '3'){
					 $this->sms_model->sendSMS_SpearUC($mobile,$message,'',$service['dlt_te_id']);
				}
				elseif($this->config->item('sms_gateway') == '4'){
					$this->sms_model->sendSMS_Asterixt($mobile,$message,'',$service['dlt_te_id']);
				}
				elseif($this->config->item('sms_gateway') == '5'){
					$this->sms_model->sendSMS_Qikberry($mobile,$message,'',$service['dlt_te_id']);
				}
		}
		return TRUE;		
	}
		// offline datas transaction_details
	//To insert payment and registration details in intermediate table
	function insert_common_data_jil($id_payment)
	{
		$model = self::CHITAPI_MODEL;
		$this->load->model($model);
		//getting payment detail
		$pay_data = $this->$model->getPaymentByID($id_payment);	
		//storing temp values
		$trans_date = $pay_data[0]['trans_date'];
		$approval_no = $pay_data[0]['approval_no'];
		$ref_no = $pay_data[0]['ref_no'];
		$id_scheme_account = $pay_data[0]['id_scheme_account'];
		//getting customer detail to post registration again
		 $reg = $this->$model->getCustomerByID($id_scheme_account,$id_payment);		 
		//echo $this->db->last_query();
		 $isExists = $this->$model->checkTransExists($trans_date,$approval_no,$ref_no);
		if(!$isExists)
		{
			//insert payment detail
			$status =	$this->$model->insert_transaction($pay_data[0]);
			//echo $this->db->last_query();exit;
			  if($status)
			  {
				  //insert registration detail
				  if($reg)
				 {
					$reg[0]['transfer_jil']	= 'N';
					$reg[0]['transfer_date']= NULL ;
					$reg[0]['ref_no']		= $ref_no;
					$status = $this->$model->insert_CustomerReg($reg[0]);
						//echo $this->db->last_query();exit;
				 }	 
			  }	
		}	
			return true;
	}
	//To insert payment and registration details in intermediate table
	function insert_common_data_old($id_payment)
	{
		$model = self::API_MODEL;
		$this->load->model($model);
		//getting payment detail
		$pay_data = $this->$model->getPaymentByID($id_payment);	
		//storing temp values
		$ref_no = $pay_data[0]['ref_no'];
		$id_scheme_account = $pay_data[0]['id_scheme_account']; 
		
		// Validate gent_clientid setting — skip client_id if disabled
		$chit_settings = $this->db->query("SELECT gent_clientid FROM chit_settings LIMIT 1")->row_array();
		if(empty($chit_settings['gent_clientid']) || $chit_settings['gent_clientid'] == 0){
			$pay_data[0]['client_id'] = '';
		}
		
		$isCusRegExists = $this->$model->checkCusRegExists($id_scheme_account,$ref_no);
		if(!$isCusRegExists['status']){
		     $reg = $this->$model->getCustomerByID($id_scheme_account);	
             //insert customer registration detail
             if($reg)
             {
            	$reg[0]['record_to']= 1 ;
            	$reg[0]['is_registered_online']= 2 ;  // 2 - online record
            	$reg[0]['ref_no']		= $ref_no;
            	// Skip clientid if gent_clientid is disabled
            	if(empty($chit_settings['gent_clientid']) || $chit_settings['gent_clientid'] == 0){
            		$reg[0]['clientid'] = NULL;
            	}
            	$status = $this->$model->insert_CustomerReg($reg[0]);
            	//echo $this->db->last_query();exit;
             }	
             //echo $this->db->last_query();exit;
		}
		$isTranExists = $this->$model->checkTransExists($ref_no);
		if(!$isTranExists)
		{
            //insert payment detail
            $pay_data[0]['record_to'] = 1;	
            $pay_data[0]['payment_type'] = 1;	// 1 - online 
            $status =	$this->$model->insert_transaction($pay_data[0]); 
		}
		//echo $this->db->last_query();exit;
		return true;
	}
	
	function insert_referral_data($id_scheme_account,$referral_data)
	{ 
		$status=FALSE;			
		$serviceID=16;
		if($referral_data['referal_code']!='' && $referral_data['is_refferal_by']==1){			
		  $data = $this->payment_modal->get_empreferrals_datas($id_scheme_account);
		}else if($referral_data['referal_code']!='' && $referral_data['is_refferal_by']==0){			
			$data = $this->payment_modal->get_cusreferrals_datas($id_scheme_account);
		}	
		if(!empty($data))
		{			
			if($data['referal_code']!='' && $data['referal_value']!=''  &&  $data['id_wallet_account']!=''){
			// insert wallet transaction data //
							$wallet_data = array(
							'id_wallet_account' => $data['id_wallet_account'],
							'id_sch_ac'         => $id_scheme_account,
							'date_transaction' =>  date("Y-m-d H:i:s"),
							'transaction_type' =>  0,
							'value'            => $data['referal_value'],
							'description'      => 'Referral Benefits - '.$data['cusname'].' ref no. '.$data['id_scheme_account']
							);
				$status =$this->payment_modal->wallet_transactionDB($wallet_data);
				  if($status)
				  {
				  		// Update credit flag in customer table
					  	/* is_refbenefit_crt = 0 -> already  benefit credited  & 1-> yet to credit benefits' */					 		
					  	if($referral_data['is_refferal_by']==0 && $data['cusbenefitscrt_type']==0 && ($data['schrefbenifit_secadd']==0 || $data['schrefbenifit_secadd']==1) ){
							// customer referal - single  
							$this->payment_modal->update_customer_only(array('is_refbenefit_crt_cus'=>0),$chkreferral['id_customer']);
						}else if($referral_data['is_refferal_by']==0 && $data['cusbenefitscrt_type']==1 && $data['schrefbenifit_secadd']==1){
							// customer referal - multiple  
							$this->payment_modal->update_customer_only(array('is_refbenefit_crt_cus'=>1),$chkreferral['id_customer']);
						}else if($referral_data['is_refferal_by']==1 && $data['empbenefitscrt_type']==0 && ($data['schrefbenifit_secadd']==0 || $data['schrefbenifit_secadd']==1)){	
							 // emp referal - single  					
							$this->payment_modal->update_customer_only(array('is_refbenefit_crt_emp'=>0),$chkreferral['id_customer']);
						}else if($referral_data['is_refferal_by']==1 && $data['empbenefitscrt_type']==1 && $data['schrefbenifit_secadd']==1){	
							// emp referal - single  			
							$this->payment_modal->update_customer_only(array('is_refbenefit_crt_emp'=>1),$chkreferral['id_customer']);
						}
				  } 
				  $service = $this->services_modal->checkService($serviceID);
				  if($service['sms'] == 1 && $data['mobile']){
				       $sms_data =$this->services_modal->get_SMS_data($serviceID,$data['id_scheme_account']); 
				       if($this->config->item('sms_gateway') == '1'){
			    		  $this->sms_model->sendSMS_MSG91($sms_data['mobile'],$sms_data['message'],'',$service['dlt_te_id']);		
			    	   }
			    	   elseif($this->config->item('sms_gateway') == '2'){
			    	      $this->sms_model->sendSMS_Nettyfish($sms_data['mobile'],$sms_data['message'],'trans');	
			    	   }
			    	   elseif($this->config->item('sms_gateway') == '3'){
        					 $this->sms_model->sendSMS_SpearUC($sms_data['mobile'],$sms_data['message'],'',$service['dlt_te_id']);	
        				}
        				elseif($this->config->item('sms_gateway') == '4'){
        					$this->sms_model->sendSMS_Asterixt($sms_data['mobile'],$sms_data['message'],'',$service['dlt_te_id']);	
        				}
        				elseif($this->config->item('sms_gateway') == '5'){
        					$this->sms_model->sendSMS_Qikberry($sms_data['mobile'],$sms_data['message'],'',$service['dlt_te_id']);	
        				}
				  }
                  if($service['serv_whatsapp'] == 1)
        			{
                    	$smsData = ["message" => $sms_data['message'], "template_name" => $service['template_name'], "params" => $sms_data['params']];
                        $this->sms_model->send_whatsApp_message($sms_data['mobile'],$smsData);
                    }
				 }
		}
	}
	public function responseURL()
   {
	$paymentgateway = $this->payment_modal->getBranchGatewayData($this->session->userdata('id_branch'),$this->session->userdata('id_pg')); 
	$workingKey  = $paymentgateway['param_1'];	//Working Key should be provided here.
	$encResponse = $_POST["encResp"];			//This is the response sent by the CCAvenue Server
	$rcvdString  = decrypt($encResponse,$workingKey);		//Crypto Decryption used as per the specified working key.
	$decryptValues = explode('&', $rcvdString);
    $txnid = "";
    $issuing_bank = "";
    $tracking_id = "";
    $order_status = "";
	$payment_mode = "";
	$bank_ref_no = "";
	$amount = "";
	$mer_amount = "";
	$merchant_param4 = "";
	$dataSize = sizeof($decryptValues);
			for($i = 0; $i < $dataSize; $i++) 
			{
				$information=explode('=',$decryptValues[$i]);
				if($i==0)	$txnid        = $information[1];
				if($i==1)	$tracking_id  = $information[1];
				if($i==2)	$bank_ref_no   = $information[1];
				if($i==3)	$order_status = $information[1] ;
				if($i==5)	$payment_mode = ($information[1] == "Net Banking" ? "NB" : ($information[1] == "Credit Card" ? "CC":($information[1] == "Debit Card" ? "DC":"")) );
				if($i==10)	$amount       = $information[1];
				if($i==29)	$merchant_param4  = $information[1];
				if($i==35)	$mer_amount   = $information[1]; 
			}
			/*$payment_amount = $this->payment_modal->getpayment_amount($merchant_param4);
			if($payment_amount['id_payment'] !='') { 
			if($order_status==="Success" && $mer_amount == $payment_amount['actual_trans_amt'])*/
			$updateData = array("issuing_bank"=> $issuing_bank,
            					"mode"        => $payment_mode,
            					"cardnum"	  => NULL ,
            					"mihpayid"    => $tracking_id,
            					"remark"      => $order_status,
            					"field9"      => $order_status, // remark
            					"bank_ref_num"=> $bank_ref_no ,
            					"txnid"       => $txnid,
            					"amount"      => $mer_amount
            				  );  
            if($mer_amount == $this->session->userdata('amount') && $txnid == $this->session->userdata('txn_id'))
            {
            			$this->session->unset_userdata('amount');
            			$this->session->unset_userdata('txn_id');
                    	if($order_status==="Success")
            			{ 
            				$this->payment_success($updateData,2);
            			}
            			else if($order_status==="Aborted")
            			{
            			    $this->payment_cancel($updateData,2);
            			}
            			else if($order_status==="Failure")
            			{ 
            			    $this->payment_failure($updateData,2);			
            			}
            			else
            			{
            				$scheme_failure = array("errMsg" => 'Security Error. Illegal access detected');
            				$this->session->set_flashdata($scheme_failure);
            				redirect("/paymt");
            			}
            }   
			else
			{
				$scheme_failure = array("errMsg" => 'Security Error. Illegal access detected');
				$this->session->set_flashdata($scheme_failure);
				redirect("/paymt");
			}
	  /* }*/
   }
	public function mobileResponseURL()
    {
       	echo "<h5 style='margin-top:150px;font-size :50px;text-align : center;'>Please wait ..... </h5>";
       	$paymentgateway = $this->payment_modal->getBranchGatewayData('',13);  
    	$working_key  = $paymentgateway['param_1'];	//Working Key should be provided here.
    	$encResponse = $_POST["encResp"];			//This is the response sent by the CCAvenue Server
    	$rcvdString  = decrypt($encResponse,$working_key);		//Crypto Decryption used as per the specified working key.
    	$decryptValues = explode('&', $rcvdString);
        $txnid = "";
        $issuing_bank = "";
        $tracking_id = "";
        $order_status = "";
    	$payment_mode = "";
    	$bank_ref_no = "";
    	$amount = "";
    	$mer_amount = "";
    	$merchant_param4 = "";
    	//echo "<pre>";print_r($decryptValues);
    	$dataSize = sizeof($decryptValues);
			for($i = 0; $i < $dataSize; $i++) 
			{
				$information=explode('=',$decryptValues[$i]);
				if($i==0)	$txnid        = $information[1];
				if($i==1)	$tracking_id  = $information[1];
				if($i==2)	$bank_ref_no   = $information[1];
				if($i==3)	$order_status = $information[1];
				if($i==5)	$payment_mode = ($information[1] == "Net Banking" ? "NB" : ($information[1] == "Credit Card" ? "CC":($information[1] == "Debit Card" ? "DC": ($information[1] == "Unified Payments" ? "UPI" : $information[1]) )) );
				if($i==10)	$amount       = $information[1];
				if($i==29)	$merchant_param4  = $information[1];
				if($i==35)	$mer_amount   = $information[1]; 
			}
			$updateData = array("issuing_bank"=> $issuing_bank,
            					"mode"        => $payment_mode,
            					"cardnum"	  => NULL ,
            					"mihpayid"    => $tracking_id,
            					"remark"      => $order_status,
            					"field9"      => $order_status,
            					"bank_ref_num"=> $bank_ref_no ,
            					"txnid"       => $txnid,
            					"amount"      => $mer_amount
            				  );  
			if($order_status==="Success" || $order_status==="Initiated")
			{  
			 //   redirect('/paymt/test_redirect');
            	$this->successMURL($updateData,2); // postData and gateway
			}
			else if($order_status === "Aborted")
			{ 
		        $this->cancelMURL($updateData,2); // postData and gateway
			}
			else if($order_status === "Failure")
			{ 
				$this->failureMURL($updateData,2);  // postData and gateway
			} 
	  /* }*/
    }
	function hdfcTransStatus($type,$txnid,$amount){  
		$url = base_url()."index.php/paymt/hdfcRedirect/success";
		echo "<h5 style='margin-top:150px;font-size :50px;text-align : center;color:green'>Payment Successfull </h5>";
		echo "<h5 style='font-size :30px;text-align : center;'>Your payment amount has been processed successfully</h5><br/>";
		echo "<h5 style='font-size :30px;text-align : center;'>Amount : INR.".$amount."<br/>Transaction ID : ".$txnid." </h5>";  
		echo "<div align='center'><a style='text-decoration: none;background: #7B1B1D;text-align: center;border: 1px solid #7b1b1d;padding: 12px;color: #fff;' href='".$url."'>Back To App</a></div>";
	exit;
	}
    function hdfcRedirect(){
   		echo "<h5 style='margin-top:150px;font-size :50px;text-align : center;'>Please wait  </h5>";
    }
	function payment_rejected(){
	    $this->load->view('mobile/request_status');
	}
	function test(){
	  $id_transaction  = '15519370845c80ae3c6a802';
	  $pay = $this->payment_modal->getWalletPaymentContent($id_transaction);
      if($pay['redeemed_amount'] > 0){ 
		$transData = array('mobile' 			=> $pay['mobile'],
							 'actual_trans_amt' => $pay['actual_trans_amt'],
							 'available_points'	=> ($pay['isAvail'] == 0 ?0:$pay['available_points']),
							 'isAvail'			=> ($pay['isAvail'] == 0 ?0:1),
							 'redeemed_amount'	=> $pay['redeemed_amount'],
							 //'is_point_credited'=> $pay['is_point_credited'],
							 'id_payment'       => $pay['id_payment'],
							 'txnid'            => $id_transaction.'- D',
							 'branch'           => $pay['branch'],
	    					 'walletIntegration'=> $pay['walletIntegration'],
	    					 'wallet_balance_type' => $pay['wallet_balance_type'],
	    					 'wallet_points' => $pay['wallet_points'],
	    					 'wallet_amt_per_points' => $pay['wallet_amt_per_points'],
							); 
		if(!empty($transData)){
		    $this->insertWalletTrans($transData); 
		}
      }
	}
	/* GIFT CARD PURCHASE MODULE */
	function mGiftCardPayment()
	{
	   if(isset($_GET))
	   {  	 
			//get the values posted from mobile in array 
		   $payData = $_GET;
		   $pay_flag = TRUE;
		   $allow_flag = TRUE;
		   $submit_pay = FALSE;
		   $actAmount = (float) $payData['amount'];  // sum of all chit amount
		   $cusData = $this->payment_modal->get_customer($payData['phone']);
		   $gateway = (isset($payData['gateway']) ? $payData['gateway'] :1 );
		   $redeemed_amount = $payData['redeemed_amount'];		   
		   // For admin app
		   $id_employee = (isset($payData['id_employee']) ? $payData['id_employee']:NULL);
		   $added_through = (isset($payData['id_employee']) ? 3:2); // 2- custmer mobile app, 3 - Admin mobile app
		   //check pay_flag
		   if($pay_flag )
		   {
				//generate txnid
				$txnid = uniqid(time());
				$i=1;
				$g_payments = json_decode($payData['pay_arr']);
				$udf1= "";
				$udf2= "";
				$udf3= "";
				$productinfo= "";
				$gpayIds= "";
				$this->db->trans_begin();	
				foreach ($g_payments as $pay){
				   if($cusData['branch_settings']==1)
					{	
						if($cusData['is_branchwise_cus_reg']==1)
						{
							$id_branch  = $cusData['id_branch'];
						}
						else
						{
							$id_branch=$this->config->item('pay_branchId');
						}						
					}
					else{
						$id_branch =NULL;
					}		
					$insertData = array(
							"amount" 	         => (isset($pay->amount)? $pay->amount+($pay->discount==""?0.00:$pay->discount) : NULL ), 
							/*"gst" 	    		 => (isset( $chit['gst'])? $chit['gst'] : 0 ),
							"gst_type" 	    	 => (isset($chit['gst_type'])? $chit['gst_type'] : 0 ),*/
							"payment_type" 	     => ($gateway == 0 ?'CSH':(isset($payData['pg_code']) ? ($payData['pg_code'] == 1 ? ($redeemed_amount > 0 ?"Wallet + Payu Checkout":"Payu Checkout"):($payData['pg_code'] == 2 ? "HDFC":($payData['pg_code'] == 5 ? "Atom":"Tech Process"))): 1)),
							"actual_trans_amt"   => (isset($actAmount) ? $actAmount : 0.00),
							"id_transaction"     => ($gateway == 0 ?NULL:(isset($txnid) ? $txnid.'-'.$i : NULL)),
							"ref_trans_id"       => ($gateway == 0 ?NULL:(isset($txnid) ? $txnid : NULL)),// to update pay status after trans complete.
							"remark"             =>  'Payment for '.$pay->udf5.' Initiated.',
							"added_by"			 =>  $added_through,
							//"id_employee"		 =>  $id_employee,
							"add_charges" 	     => (isset($pay->charge) ?$pay->charge : NULL), 
							"discountAmt"        => ($pay->discount!="" ? $pay->discount : 0.00),
							"payment_status"     => ($gateway == 0 ?$this->payment_status['awaiting']:$this->payment_status['pending']),
							"id_payGateway"      => (isset($payData['gateway']) ? $payData['gateway']: 1),
					 		"redeemed_amount"     => (isset($redeemed_amount) ?$redeemed_amount :0.00),
					 		"id_branch"    		 => (isset($payData['id_branch']) ? $payData['id_branch']: $id_branch),
					// 	    "is_point_credited"   => 1
							//status - 0 (pending), will change to 1 after approved at backend
						);  
					$udf1 = $udf1." ".$pay->udf1;
					$udf2 = $udf2." ".$pay->udf2;
					$udf3 = $udf3." ".$pay->udf3;
					$productinfo = $productinfo." ".$pay->udf1;
					//inserting pay_data before gateway process
					//echo "<pre>"; print_r($insertData);exit;
					 $payment = $this->payment_modal->insertData($insertData,'payment_gift_card');
					 $gpayIds = $gpayIds.",".$payment['insertID'];
					$i++;
				}
				 if($this->db->trans_status()=== TRUE)
	             {
				 	$this->db->trans_commit();
				 	$submit_pay = TRUE;
					 /*if($gateway == 0){ // For admin app
					 	$paymtData['schAc_ids'] = $productinfo;
					 	$paymtData['gpay_ids'] = $gpayIds;
					 	$this->adminAppSuccess($paymtData);
					 }else{
					 	$submit_pay = TRUE;
					 }*/
				 }
				 else{
				 	$this->db->trans_rollback();
				 	if($gateway == 0){ // For admin app
					 	redirect('paymt/adminapp/failed');
					 }else{
					 	$submit_pay = FALSE;
					 }
				 }
				if($submit_pay)
				{
		             $paycred = $this->payment_gateway($cusData['id_branch'],$payData['gateway']); 
					//set data for hash generation
					  $data['pay'] =	array (
						'key' 			=> $paycred['param_1'], 
						'txnid' 		=> $txnid, 
						'amount' 		=> (isset($payData['amount'])   ? $payData['amount']:''),
						'productinfo'	=> (isset($productinfo)    ? $productinfo :''),
						'firstname' 	=> (isset($cusData['firstname'])? $cusData['firstname'] :''),
						'lastname' 		=> (isset($cusData['lastname']) ? $cusData['lastname']:''),
						'email' 		=> !empty($cusData['email']) ? $cusData['email'] : $this->comp['email'],
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
						'udf4' 			=> (isset($payData['udf4'])    ? $payData['udf4'] : ''),
						'udf5' 			=> (isset($payData['udf5'])? $payData['udf5']:'') 
					  );
				 /* echo "<pre>";
				  print_r( $data['pay']);
				  echo "<pre>";exit; */
				  //generate hash
				  $hash_sequence =   Misc::get_hash( $data['pay'],$paycred['param_2']);		
					if($payData['pg_code'] == 3)
                    { // Techprocess
                        $data['pay']['bankcode'] 	 = $payData['bankcode']; 
						$data['pay']['ccnum'] 		 = $payData['ccnum']; 
						$data['pay']['ccname'] 		 = $payData['ccname']; 
						$data['pay']['ccvv'] 		 = $payData['ccvv']; 
						$data['pay']['ccexpmon'] 	 = $payData['ccexpmon']; 
						$data['pay']['ccexpyr']      = $payData['ccexpyr']; 
						$data['pay']['pg']    		 = $payData['pg'];
						$data['pay']['m_code']    		 =$paycred['param_3'];
						$data['pay']['iv']    		 = $paycred['param_4'];
						$data['pay']['store_card']   = (isset($payData['store_card'])? $payData['store_card'] : 0 );
                        $data['pay']['id_pg'] = $payData['gateway'];
                        $data['pay']['id_branch'] = $cusData['id_branch'];
                        $this->submitToTechProcess($data['pay'],'mobile','G');
                    }else if($payData['pg_code'] == 2){ 
	                    $merchant_id   = $paycred['param_4'];
	                    $working_key   = $paycred['param_1'];//Shared by CCAVENUES
	                    $access_code   = $paycred['param_3'] ;//Shared by CCAVENUES 
                    	$data['hdfcpay'] =	array (
							'tid' 		    =>  (rand(10,100).''.time()), // should contain numbers only
					        'merchant_id'   =>  $merchant_id,
					        'order_id'      =>  $txnid,
							'amount' 		=> (isset($payData['amount'])   ? $payData['amount']:''),
							'productinfo'	=> (isset($productinfo)? $productinfo: ''),
							'currency'	    => 'INR',
						    'redirect_url'  =>  $this->config->item('base_url')."index.php/paymt/gPayMobileResponseURL",
						    'language'      => 'EN',
							'id_payment'    => $payment['insertID'],
							'billing_name' 	=> (isset($payData['firstname'])? (isset($payData['lastname']) ? $payData['firstname'].' '.$payData['lastname']:$payData['firstname']) :''),
							'billing_email' => (isset($payData['email'])    ? $payData['email']:''), 
							'billing_tel' 	=> (isset($payData['phone'])    ? $payData['phone'] :''),
							'billing_address' 	=> (isset($payData['address1'])? (isset($payData['address2']) ? $payData['address1'].' '.$payData['address2']:$payData['address1']) :''),
							'billing_city'	=> (isset($payData['city']) ? $payData['city'] :''), 
							'billing_state'	=> (isset($payData['state']) ? $payData['state'] : ''), 
							'billing_country'=> (isset($payData['country']) ? $payData['country'] : ''), 
							'billing_zip'	=> (isset($payData['zipcode']) ? $payData['zipcode'] :''),
							'merchant_param1' => (isset($udf1) ? $udf1 :''),
							'merchant_param2' => (isset($udf2) ? $udf2 :''),
							'merchant_param3' => (isset($udf3) ? $udf3 :''),
							'merchant_param4' => (isset($payment['insertID']) ? $payment['insertID'] : ''),
							'merchant_param5' => (isset($udf5) ? $udf5:'') 
						  );
	                    foreach ($data['hdfcpay'] as $key => $value){ 
	                        $merchant_data.=$key.'='.urlencode($value).'&';
	                    }   
	                    $encrypted_data=encrypt($merchant_data,$working_key);
	                    //Generate Encrypted Datas
	                    if($encrypted_data!='' && $txnid !='')
	                    {
	                        $data['hdfcpay']['encRequest']   = $encrypted_data;
	                        $data['hdfcpay']['access_code']  = $access_code; 
	                        //echo "<pre>";print_r($data['hdfcpay']);exit;
	                        $this->load->view('hdfc/payment',$data);
	                    }
					}
                    else
                    {
                    	$hash_sequence =   Misc::get_hash( $data['pay'],$paycred['param_2']);	
					if($hash_sequence!='' && $txnid !='')
					{
						$data['pay']['hash'] 		 = $hash_sequence; 
						$data['pay']['pg']    		 = $payData['pg']; 
						$data['pay']['bankcode'] 	 = $payData['bankcode']; 
						$data['pay']['ccnum'] 		 = $payData['ccnum']; 
						$data['pay']['ccname'] 		 = $payData['ccname']; 
						$data['pay']['ccvv'] 		 = $payData['ccvv']; 
						$data['pay']['ccexpmon'] 	 = $payData['ccexpmon']; 
						$data['pay']['ccexpyr']      = $payData['ccexpyr']; 
						$data['pay']['curl']         =  $this->config->item('base_url')."index.php/paymt/gPayResponseMURL/c"; 
						$data['pay']['furl']         =  $this->config->item('base_url')."index.php/paymt/gPayResponseMURL/f"; 
						$data['pay']['surl']         =  $this->config->item('base_url')."index.php/paymt/gPayResponseMURL/s";		
						$data['pay']['user_credentials']   =  $paycred['param_1'].':'.$payData['phone'];		
						$data['pay']['store_card']   =  (isset($payData['store_card'])? $payData['store_card'] : NULL );		
						$data['pay']['store_card_token']   =  (isset($payData['card_token'])? $payData['card_token'] : NULL );	
						$this->load->view('mobile/payment',$data);
					}	
				}
			}
		   }
	   }
	}
	function gPayResponseMURL($type,$payData = "", $gateway = "")
	{
		if($gateway == ""){
            $payData = $_POST;
        }  
	    $user = array(
		                'firstname' => $payData['firstname'], 
		                'lastname'  => $payData['lastname'], 
		                'mobile'    => $payData['phone'],
	                    'email'    => $payData['email']					
					 );
		$updateData = array(
							"bank_name"			 =>	(isset($payData['issuing_bank']) ? $payData['issuing_bank'] : NULL),
							"payment_mode"       => (isset($payData['mode']) ? $payData['mode'] : NULL),
							"card_no"			 => (isset($payData['mode']) && ($payData['mode'] == 'CC' || $payData['mode'] == 'DC') ? $payData['cardnum'] :NULL ),
							"payu_id"             => (isset($payData['mihpayid']) ? $payData['mihpayid'] : NULL),
							"remark"             => (isset($payData['field9']) ? $payData['field9'] : NULL),
							"payment_ref_number" => (isset($payData['bank_ref_num']) ? $payData['bank_ref_num'] : NULL ),
							"payment_status"     => ($type == 's' ? ($this->config->item('auto_pay_approval') == 1 || $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success']: $this->payment_status['awaiting']):($type == 'f' ? $this->payment_status['failure']:$this->payment_status['cancel']))
						);
		if(!empty($payData['txnid']) && $payData['txnid'] != NULL){
	    	$payment = $this->payment_modal->updateGatewayResponse($updateData,$payData['txnid']);
		}
		if($gateway != ""){
		    if($type == 'f')
		    redirect('paymt/hdfcRedirect/failed'); 
		    elseif($type == 's')
            redirect('paymt/hdfcRedirect/success'); 
            elseif($type == 'c')
            redirect('paymt/hdfcRedirect/cancel'); 
		}
	}
	public function gPayMobileResponseURL()
    {
       	echo "<h5 style='margin-top:150px;font-size :50px;text-align : center;'>Please wait ..... </h5>";
    	$working_key   = $this->payment_gateway[1]['key'];//Shared by CCAVENUES
    	$encResponse = $_POST["encResp"];			//This is the response sent by the CCAvenue Server
    	$rcvdString  = decrypt($encResponse,$working_key);		//Crypto Decryption used as per the specified working key.
    	$decryptValues = explode('&', $rcvdString);
        $txnid = "";
        $issuing_bank = "";
        $tracking_id = "";
        $order_status = "";
    	$payment_mode = "";
    	$bank_ref_no = "";
    	$amount = "";
    	$mer_amount = "";
    	$merchant_param4 = ""; 
    	$dataSize = sizeof($decryptValues);
		for($i = 0; $i < $dataSize; $i++) 
		{
			$information=explode('=',$decryptValues[$i]);
			if($i==0)	$txnid        = $information[1];
			if($i==1)	$tracking_id  = $information[1];
			if($i==2)	$bank_ref_no   = $information[1];
			if($i==3)	$order_status = $information[1];
			if($i==5)	$payment_mode = ($information[1] == "Net Banking" ? "NB" : ($information[1] == "Credit Card" ? "CC":($information[1] == "Debit Card" ? "DC":"")) );
			if($i==10)	$amount       = $information[1];
			if($i==29)	$merchant_param4  = $information[1];
			if($i==35)	$mer_amount   = $information[1]; 
		} 
		$updateData = array("issuing_bank"=> $issuing_bank,
        					"mode"        => $payment_mode,
        					"cardnum"	  => NULL ,
        					"mihpayid"    => $tracking_id,
        					"remark"      => $order_status,
        					"field9"      => $order_status,
        					"bank_ref_num"=> $bank_ref_no ,
        					"txnid"       => $txnid
        				  );  
		if($order_status==="Success")
		{   
        	$this->gPayResponseMURL('s',$updateData,2); // postData and gateway
		}
		else if($order_status === "Aborted")
		{ 
	        $this->gPayResponseMURL('c',$updateData,2); // postData and gateway
		}
		else if($order_status === "Failure")
		{ 
			$this->gPayResponseMURL('f',$updateData,2);  // postData and gateway
		}  
    }
    function gPaytechProMblResponseURL($payData = ""){
        echo "<h1 style='text-align:center;font-size:75px'>Please wait</h1>";
        $serv_model= self::SERV_MODEL; 
        $data=explode('&',$_SERVER['QUERY_STRING']);
        $id_branch=$data[0];
        $id_pg=$data[1];
        $paymentgateway = $this->payment_modal->getBranchGatewayData($id_branch,$id_pg);
        $iv=$paymentgateway['param_4'];	
        $key=$paymentgateway['param_1'];
        $mrctCode=$paymentgateway['param_3'];   
        $response = $_POST;
        if(is_array($response)){
            $str = $response['msg'];
        }else if(is_string($response) && strstr($response, 'msg=')){
            $outputStr = str_replace('msg=', '', $response);
            $outputArr = explode('&', $outputStr);
            $str = $outputArr[0];
        }else {
            $str = $response;
        }
        $transactionResponseBean = new TransactionResponseBean();
        $transactionResponseBean->setResponsePayload($str);
        $transactionResponseBean->setKey($key);
        $transactionResponseBean->setIv($iv);
        $response = $transactionResponseBean->getResponsePayload(); 
        /* SAMPLE :- 
            TRC :-  txn_status=0300|txn_msg=success|txn_err_msg=NA|clnt_txn_ref=TPSL399698281|tpsl_bank_cd=10140|tpsl_txn_id=117203564|txn_amt=2.00
    |clnt_rqst_meta={itc:NIC~TXN0001~122333~rt14154~8 mar 2014~Payment~forpayment}{email:sunil.sahu@techproces.co.in}{mob:9856987456}|
    tpsl_txn_time=12-11-2014 16:40:54|tpsl_rfnd_id=NA|bal_amt=NA|rqst_token=6308eff9-87db-40f1-8a17-0097c4264818|card_id=4077|
    BankTransactionID=114111201389206|alias_name=VISA_DBT_****9847|hash=d9cf87c453fbc49f32536db6097d9e80cc7d3397 
            T :-    txn_status=0399|txn_msg=failure|txn_err_msg=Transaction Cancelled : ERROR CODE TPPGE152|clnt_txn_ref=15423712435beeb7abaa834|tpsl_bank_cd=NA|tpsl_txn_id=E9472341|txn_amt=1.00|clnt_rqst_meta={custname:Pavithra}|tpsl_txn_time=16-11-2018 17:57:37|tpsl_rfnd_id=NA|bal_amt=NA|rqst_token=1817cb57-7dee-45da-b71d-477ca2ca7f87|hash=20574cff7f4ad0b1597d4088c95e04c6527b33ad
        */
        $transData = array(); 
		$payData = explode("|", $response); 
        $status_code = "";
        $status_msg = "";
        $err_msg = "";
        $txn_id = "";
        $payu_id = "";
        $card_id = NULL;
        foreach($payData as $pay){ 
        	$r = explode("=", $pay); 
        	if($r[0] === "txn_status") $status_code = $r[1];
        	if($r[0] === "txn_msg") $status_msg = $r[1];
        	if($r[0] === "txn_err_msg") $err_msg = $r[1];
        	if($r[0] === "clnt_txn_ref") $txn_id = $r[1]; 
        	if($r[0] === "tpsl_txn_id") $payu_id = $r[1];
        	if($r[0] === "card_id") $card_id = $r[1];
        }
        $updateData = array(
        		"payu_id"           => (isset($payu_id) ? $payu_id : NULL),
        		"remark"            => $status_msg.' - '.$status_code.' - '.($err_msg != 'NA' ? $err_msg : ''),
        	    "payment_status"    => ($status_code == '0300' ? ( $this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success']:$this->payment_status['awaiting']):($status_code == '0392'?$this->payment_status['cancel']:$this->payment_status['failure']))
        	 //status - 0 (pending), will change to 1 after approved at backend
        	);
		$payment = $this->payment_modal->updateGatewayResponse($updateData,$txn_id);
		$redirectURL = ($status_code == '0300' ? "gPayResponseMURL/s":($status_code == '0392'?"gPayResponseMURL/c":'gPayResponseMURL/f')); 
    	redirect("/paymt/".$redirectURL);
    }
	//GG  gift card payments
	function GiftCardPayment()
	{
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{
		    $customerid = $this->session->userdata('cus_id');
			//echo"<pre>";	print_r($customerid);exit;
			$branchWiseLogin = $this->payment_modal->get_branchWiseLogin();
			$giftcard = $this->payment_modal->get_ajax_giftdetails($customerid);
			$data = array('giftcard' => $giftcard);
			$pageType = array('page' => 'giftcard','currency_symbol'=>$this->comp['currency_symbol'],'scheme_status' =>  $this->scheme_status,'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['content'] = $data;
			$data['fileName'] = self::VIEW_FOLDER.'GiftCardPayment';
			$this->load->view('layout/template', $data);
		}	
	}
	function ajax_giftdetails() {
		 $customerid = $this->session->userdata('cus_id');
		 $data= $this->payment_modal->get_ajax_giftdetails($customerid);
		/*  echo"<pre>";	print_r(json_encode($data)); */
		 echo json_encode($data);
	}
	//GG  gift card payments
   //cashfree payment success response For Web //hh	
   public function cashfreeresponseURL()
   {
    	 /*$paymentgateway = $this->payment_modal->getBranchGatewayData($this->session->userdata('id_branch'),$this->session->userdata('id_pg')); 
    	 $secretKey  = $paymentgateway['param_1'];	//secret Key should be provided here.
		 //print_r($secretKey);exit;*/
		 $orderId       = $_POST["orderId"];
		 $orderAmount   = $_POST["orderAmount"];
		 $referenceId   = $_POST["referenceId"];
		 $txStatus      = $_POST["txStatus"];
		 $paymentMode   = $_POST["paymentMode"];
		 $txMsg         = $_POST["txMsg"];
		 $txTime        = $_POST["txTime"];
		 //$signature     = $_POST["Signature"];
		 //print_r($signature);exit;
		 $data = $orderId.$orderAmount.$referenceId.$txStatus.$paymentMode.$txMsg.$txTime; 
		 /*$hash_hmac = hash_hmac('sha256', $data, $secretkey, true) ; 
		 $computedSignature = base64_encode($hash_hmac); */
	 	 $updateData = array("mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":$paymentMode))), 
        					"cardnum"	  => NULL ,
        					"field9"      => $txMsg."[".$txTime."]", // remark
        					"bank_ref_num"=> $referenceId ,
        					"mihpayid"     => $referenceId ,
        					"txnid"       => $orderId,
        					"amount"      => $orderAmount,
        					//"Signature"   => $signature
            				);  
		//echo "<pre>";print_r($this->session->all_userdata());exit;
        if($orderAmount == $this->session->userdata('amount') && $orderId == $this->session->userdata('txn_id'))
        {
    			$this->session->unset_userdata('amount');
    			$this->session->unset_userdata('txn_id'); 
            	if($txStatus === "SUCCESS")
    			{ 
    				$this->payment_success($updateData,4);
    			}
    			else if($txStatus === "FAILED")
    			{ 
    			    $this->payment_failure($updateData,4);			
    			}
    				else if($txStatus === "CANCELLED")
    			{ 
    			    $this->payment_cancel($updateData,4);			
    			}
    			else
    			{
    				$scheme_failure = array("errMsg" => 'Signature Verification failed');
    				$this->session->set_flashdata($scheme_failure);
    				redirect("/paymt");
    			}
        } 
    	else
    	{
           $scheme_failure = array("errMsg" => 'Signature Verification failed.');
    		$this->session->set_flashdata($scheme_failure);
    		redirect("/paymt");
    	}
   } 
	public function cashfreemobile()
   {
   	echo "<h5 style='margin-top:150px;font-size :50px;text-align : center;'>Please wait ..... </h5>";
	 $secretKey  = $paymentgateway['param_1'];	//secret Key should be provided here.
         $signature     = $_POST["Signature"];
		 $orderId       = $_POST["orderId"];
		 $orderAmount   = $_POST["orderAmount"];
		 $referenceId   = $_POST["referenceId"];
		 $txStatus      = $_POST["txStatus"];
		 $paymentMode   = $_POST["paymentMode"];
		 $txMsg         = $_POST["txMsg"];
		 $txTime        = $_POST["txTime"];
		 $data = $orderId.$orderAmount.$referenceId.$txStatus.$paymentMode.$txMsg.$txTime; 
		// 	print_r($data);exit;
		 $hash_hmac = hash_hmac('sha256', $data, $secretkey, true) ; 
		 $computedSignature = base64_encode($hash_hmac); 
		$updateData = array("mode"        => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":$paymentMode))), 
        					"cardnum"	  => NULL ,
        					"field9"      => $txMsg."[".$txTime."]", // remark
        					"bank_ref_num"=> $referenceId ,
        					"mihpayid"     => $referenceId ,
        					"txnid"       => $orderId,
        					"amount"      => $orderAmount,
        					"Signature"   => $signature
            				);  
			  //print_r($updateData);exit;
			if($txStatus==="SUCCESS")
			{  
			 //   redirect('/paymt/test_redirect');
            	$this->successMURL($updateData,4); // postData and gateway
			}
			else if($txStatus === "CANCELLED")
			{ 
		        $this->cancelMURL($updateData,4); // postData and gateway
			}
			else if($txStatus === "FAILED")
			{ 
				$this->failureMURL($updateData,4);  // postData and gateway
			} 
   }
   //cashfree payment success response For App //hh	
   // this method for Random mailid cf pg//
    function random_strings($length)
    {
      // String of all alphanumeric character
         $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      // Shufle the $str_result and returns substring
      // of specified length
         return substr(str_shuffle($str_result), 0, $length);
    }
    /**	* 
	*	Khimji integration API call functions 
	* 	
	*	1. Generate  tranUniqueId
	* 	2. Generate AcountNo Or ReceiptNo
	* 
    */
    function generateTranUniqueId($chit,$amount){
        $tranUniqueId = '';
    	$postData = array(
                    	"isKycValidationCheck" => false,
                    	"customerCode"  => $chit['reference_no'],
                    	"transactionType"=> 1,
                    	"schemeCode"    => $chit['sync_scheme_code'],
                    	"amount"        => $amount,
                    	"date"          => date("Y-m-d"),
                    	"narration"     => "Requested from mobile app"
                    );
        $is_new_ac = ($chit['scheme_acc_number'] != "" && $chit['scheme_acc_number'] != NULL ? FALSE : TRUE);            
        if($is_new_ac){ // 1st Installment 
        	$postData["action"] = 1;
            $postData["narration"]   =  $chit['nominee_address1'].",".$chit['nominee_address2'];
            /*$postData["narration2"]  =  $chit['nominee_relationship'];//"Nominee Relation"
            $postData["narration3"]  =  $chit['nominee_name']; //"Nominee Name"
            $postData["narration4"]  =  "";//Nominee DOB"
            $postData["narration5"]  =  $chit['nominee_mobile']; //"Nominee MobNo"*/
            $postData["salesmanName"]=  $chit['emp_name'];
            $postData["employeeId"]  =  $chit['referal_code'];
            $postData["nominee"] = array(
                                		"nomineeName" => $chit['nominee_name'],
                                		"relation" => $chit['nominee_relationship'],
                                		"address1" => $chit['nominee_address1'],
                                		"address2" => $chit['nominee_address2'],
                                		"dateOfBirth" => NULL,
                                        "mobileNo" => $chit['nominee_mobile']
                                	   );
	    }else{
	    	$postData["action"] = 2;
	        $postData["orderNo"] = $chit['scheme_acc_number'];
	    }
       $response = $this->integration_model->khimji_curl('app/v1/saveSchemeOrInstallmentDetails',$postData);
       /*echo "<pre>";print_r($postData);
       echo "<pre>";print_r($response);exit;*/
       // Write log in case of API call failure
       if (!is_dir($this->log_dir.'/khimji')) {
            mkdir($this->log_dir.'/khimji', 0777, true);
        }
        $log_path = $this->log_dir.'/khimji/'.date("Y-m-d").'.txt';
        $logData = "\n".date('d-m-Y H:i:s')."\n API : app/v1/saveSchemeOrInstallmentDetails \n Post : ".json_encode($postData,true)."\n Response : ".json_encode($response,true);
	    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
        if($response['status'] == TRUE){
           	$resData = $response['data']->data; 
           	if($resData->status == TRUE && $resData->errorCode == 0){
               return array("status" => $resData->status, "tranUniqueId" => $resData->result->tranUniqueId);
            }
            else if($resData->status == FALSE || $resData == null){
                if($resData->errorCode == 1001){
    				$payData = array(
    							 'offline_error_msg'	=> date("Y-m-d H:i:s")." ID Gen Error : ".$resData->errorMsg,
    							 );
    				$this->integration_model->updateData($payData,'id_payment',$pay['id_payment'],'payment');
                }
               return array("status" => $resData->status, "message" => $resData->errorMsg, 'resData' => $resData);
            }
        }
	    return array("status" => FALSE, "message" => 'Unable to proceed your payment,please try after sometime or contact customer care.', 'tranUniqueId' => $tranUniqueId);
	    /*$this->db->trans_rollback();
		$this->session->set_flashdata('errMsg','Unable to proceed your payment,please try after sometime or contact customer care.');
		redirect("/paymt");*/
    }
    function generateAcNoOrReceiptNo($pay){		
           $postData = array(
					    "transactionType"=> 2,
					    "tranUniqueId"	=> $pay['offline_tran_uniqueid'],
					    "branchCode"	=> $pay['warehouse'],
					    "paymentDetail"	=> array(
											"paymentType" 		=> 9,
											"paymentTypeName" 	=> "Online",
											"amount" 			=> $pay['payment_amount'],
											"authorizationNo" 	=> "ADM_REF_".$pay['id_payment'],
											"narration" 		=> $pay['payment_type']."-".$pay['payment_mode'],
											"originalAmt" 		=> $pay['payment_amount'],
											"transationAmt" 	=> $pay['payment_amount'],
											"marchantCharges" 	=> 0.00,
											"cardChargesAmount" => 0.00,
											"cardChargesPercentage" => 0
											)
					);
            $is_new_ac = ($pay['scheme_acc_number'] != "" && $pay['scheme_acc_number'] != NULL ? FALSE : TRUE);
            if(!$is_new_ac){ // Account number already generated
                $postData["orderNo"] = $pay['scheme_acc_number'];
		    }
           $response = $this->integration_model->khimji_curl('app/v1/saveSchemeOrInstallmentDetails',$postData);
            /*echo "<pre>";print_r($postData);
            echo "<pre>";print_r($response);exit;*/
           if($response['status'] == TRUE){
               	$resData = $response['data']->data; 
               	if($resData->errorCode == 0){ // $resData->status == TRUE && 
               		if(isset($resData->result->orderNo)){
               		    $this->db->trans_begin();
						$acData = array(
									 'scheme_acc_number'=> $resData->result->orderNo,
									 'date_upd'			=> date("Y-m-d H:i:s")
									 );
						$this->payment_modal->updData($acData,'id_scheme_account',$pay['id_scheme_account'],'scheme_account');
						$payData = array(
									 'receipt_no'	=> $resData->result->orderNo,
									 'date_upd'		=> date("Y-m-d H:i:s")
									 );
						$this->payment_modal->updData($payData,'id_payment',$pay['id_payment'],'payment');
						//echo $this->db->last_query();exit;
		    			return true;						
					}
					/*if(isset($resData->result->installmentNo)){
						$payData = array(
									 'receipt_no'	=> $resData->result->installmentNo,
									 'date_upd'		=> date("Y-m-d H:i:s")
									 );
						$this->payment_modal->updData($payData,'id_payment',$pay['id_payment'],'payment');
						return true;
					}*/
                }
           }
           // Write log in case of API call failure
	       if (!is_dir($this->log_dir.'/khimji')) {
	            mkdir($this->log_dir.'/khimji', 0777, true);
	        }
	        $log_path = $this->log_dir.'/khimji/'.date("Y-m-d").'.txt';
	        $logData = "\n".date('d-m-Y H:i:s')."\n API : app/v1/saveSchemeOrInstallmentDetails \n Response : ".json_encode($response,true);
		    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
		    return true;
	}
	// .Khimji integration API call functions
	/*Agent and Employee Incentive functions 
	  Coded by Haritha */
	  function insertAgentIncentive($data,$id_sch_acc,$id_payment,$id_agent)
    {
        $checkRefExist = $this->payment_modal->checkReferalExist($id_payment,$id_sch_acc);
        if($checkRefExist == 0)
        {
                                    $insert_array = array("ly_trans_type" => 3,
                            			 	                    "cus_loyal_cus_id" => $data['id_customer'],
                            			 	                    "id_agent"   => $id_agent,
                            			 	                    "id_scheme_account" => $id_sch_acc,
                            			 	                    "id_payment"  => $id_payment,
                            			 	                    "cash_point" => $data['referal_amount'],
                            			 	                    "status"    => 1,
                            			 	                    "tr_cus_type" => 4,
                            			 	                    "cr_based_on" => 3,
                            			 	                    "unsettled_cash_pts" => $data['referal_amount'],
                            			 	                    "date_add"       => date('Y-m-d H:i:s'),
                            			 	                    "credit_for"   => $data['credit_remark'].'-Collection App'
                            			 	                            );
                            			 	     $status = $this->payment_modal->insert_agent_transaction($insert_array);
                            			 	     $this->payment_modal->updateAgentCash($id_agent,$data['referal_amount']);
                            			 	     $ag_data = array("id_agent"   => $id_agent);
                            			 	     $this->payment_modal->updData($ag_data,'id_payment',$id_payment,'payment');
                return $status;
        }
        else{
            return 0;
        }
    }
    function insertEmployeeIncentive($refdata,$id_scheme_account,$id_payment)
	{
		$status=FALSE;			
		$chkreferral=$this->payment_modal->get_referral_code($id_scheme_account);
		//print_r($chkreferral);exit;
         $data = array();
        		 $checkCreditExist = $this->payment_modal->checkCreditTransExist($id_scheme_account,$id_payment);
        		 if($checkCreditExist == 0)
        		 {
        			if($chkreferral['referal_code']!='' && $chkreferral['is_refferal_by']==1){			
        			  $data = $this->payment_modal->get_empreferrals_datas($id_scheme_account);
        			}
        		}
        		if(!empty($data) && $chkreferral['is_refferal_by']==1 && $checkCreditExist == 0)
        		{			
        			if($data['referal_code']!='' && $refdata['referal_amount']!=''  &&  $data['id_wallet_account']!='' && $data['id_wallet_account'] > 0){
        			// insert wallet transaction data //
        							$wallet_data = array(
        							'id_wallet_account' => $data['id_wallet_account'],
        							'id_sch_ac'         => $id_scheme_account,
        							'date_transaction' =>  date("Y-m-d H:i:s"),
        							'id_employee'      =>  $data['idemployee'],
        							'transaction_type' =>  0,
        							'value'            => $refdata['referal_amount'],
        							'id_payment'      => $id_payment,
        							'credit_for'     => $refdata['credit_remark'].'-Collection App',
        							'description'      => 'Referral Benefits - '.$data['cusname'].''
        							);
        						//	echo"<pre>"; print_r($wallet_data);exit;
        				$status =$this->payment_modal->wallet_transactionDB($wallet_data);
        				//echo $this->db->last_query();exit;
        				 }
        		}
		return true;
	}
	function customerIncentive($refdata,$id_scheme_account,$id_payment)
	{
	    $chkreferral=$this->payment_modal->get_referral_code($id_scheme_account);
	    //credit customer introduce staff incentive
		if($chkreferral['is_refferal_by']==0)
		{
			// customer referal - multiple  
			$this->payment_modal->update_customer_only(array('is_refbenefit_crt_cus'=>1),$chkreferral['id_customer']);
			//check customer intro staff incentive
			$isEmpRef = $this->payment_modal->get_empRefExist_datas($id_scheme_account);
			if($isEmpRef > 0)
			{
			    $data = $this->payment_modal->get_empreferrals_datas($isEmpRef['id_scheme_account']);
			    if(!empty($data))
        		{			
        			if($data['referal_code']!='' && $refdata['referal_amount']!=''  &&  $data['id_wallet_account']!='' && $data['id_wallet_account'] > 0){
        			// insert wallet transaction data //
        							$wallet_data = array(
        							'id_wallet_account' => $data['id_wallet_account'],
        							'id_sch_ac'         => $isEmpRef['id_scheme_account'],
        							'date_transaction' =>  date("Y-m-d H:i:s"),
        							'id_employee'      =>  $this->session->userdata('uid'),
        							'transaction_type' =>  0,
        							'value'            => $refdata['referal_amount'],
        							'id_payment'      => $id_payment,
        							'credit_for'     => 'Customer Intro Scheme Incentive',
        							'description'      => 'Customer Intro Referral Benefits - '.$data['cusname'].''
        							);
        						//	echo"<pre>"; print_r($wallet_data);exit;
        				$status =$this->payment_modal->wallet_transactionDB('insert','',$wallet_data);
        				 }
        		}
			}
		}
	}
    /*ends*/
    public function ipporesponseURL()
   {
    	 //echo "<h5 style='margin-top:150px;font-size :50px;text-align : center;'>Please wait ..... </h5>";
    	 $gatewayData = json_decode(file_get_contents("https://".$_POST['publicKey'].":".$_POST['secretKey']."@api.ippopay.com/v1/order/".$_POST['response']['order_id']));
		 $orderId       = $gatewayData->data->order->order_id;
		 $orderAmount   = $gatewayData->data->order->amount;
		 $referenceId   = $gatewayData->data->order->trans_id;
		 $txStatus      = $_POST['response']['status'];
		 $paymentMode   = $gatewayData->data->order->mode;
		 $txMsg         = $gatewayData->message;
		 $txTime        = $gatewayData->data->order->createdAt;
		 $data = $orderId.$orderAmount.$referenceId.$txStatus.$paymentMode.$txMsg.$txTime; 
	 	 $updateData = array("mode"       => ($paymentMode== "credit_card" ? "CC":($paymentMode == "debit_card" ? "DC":($paymentMode == "net_banking" ? "NB":$paymentMode))), 
        					"cardnum"	  => NULL ,
        					"field9"      => $txMsg."[".$txTime."]", // remark
        					"bank_ref_num"=> $referenceId ,
        					"mihpayid"     => $orderId ,
        					"txnid"       => $_POST['txnid'],
        					"amount"      => $orderAmount,
        					"issuing_bank"   => NULL
            				);  
		//echo "<pre>";print_r($this->session->all_userdata());exit;
            	if($txStatus === "success")
    			{ 
    				//$this->payment_success($updateData,6);
    				$this->successMURL($updateData,6);
    			}
    			else if($txStatus === "failure")
    			{ 
    			    $this->payment_failure($updateData,6);			
    			}
    				else if($txStatus === "CANCELLED")
    			{ 
    			    $this->payment_cancel($updateData,6);			
    			}
    			else
    			{
    				$scheme_failure = array("errMsg" => 'Verification failed');
    				$this->session->set_flashdata($scheme_failure);
    				redirect("/paymt");
    			}
   } 
   function razorresponseURL()
   {
       print_r($_POST);exit;
   }
   	//DCNM-DGS   
	   public function chit_detail_report($id_scheme_account)
	   {
		   $this->comp = $this->login_model->company_details();
		   $this->load->helper(array('dompdf', 'file'));
		   $data['sch'] = $this->scheme_modal->get_chit_data($id_scheme_account);
		   $intData = $this->scheme_modal->get_chit_int($data['sch']);
		   $intData['id_scheme_account'] = $data['sch']['id_scheme_account'];
		   $data['interest'] = $intData;
		   $data['payData'] = $this->scheme_modal->chit_detail_report($intData);	
		   if($this->branch_settings==1){
			   $data['comp_details']=$this->login_model->get_branchcompany($data['sch']['id_branch']);
		   }else{
			   $data['comp_details'] = $this->scheme_modal->get_company();
		   }
		   $dompdf = new DOMPDF();
		   $html = $this->load->view('include/chit_detail_report', $data,true);
		   $dompdf->load_html($html); 
		   $dompdf->set_paper("a4", "portriat" );
		   $dompdf->render();
		   $dompdf->stream("chit_detail_report.pdf",array('Attachment'=>0));
	   }
}	
?>