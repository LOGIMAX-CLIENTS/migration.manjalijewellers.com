<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class advance_booking_model extends CI_Model
{
	
	function __construct()
    {      
        parent::__construct();
	}

    public function insertData($data,$table)
    {
    	$insert_flag = 0;
		$insert_flag = $this->db->insert($table,$data);
	    return ($insert_flag == 1 ? $this->db->insert_id(): 0);
	}

    public function updData($data, $id_field, $id_value, $table)
    {    
    	$edit_flag = 0;
    	$this->db->where($id_field, $id_value);
    	$edit_flag = $this->db->update($table,$data);
    	return ($edit_flag==1?$id_value:0);
    }

    public function payment_gateway($pg_id)
  	{
   		$sql="SELECT param_1,param_2,param_3,param_4,pg_code,api_url,pg_name,type from gateway where id_pg=".$pg_id ;
		$result=  $this->db->query($sql)->row_array();
		return $result;   	
    }

    public function get_customer($mobile)
	{
	  $query_customer = $this->db->query("SELECT cus.id_customer,cus.nominee_name,cus.nominee_mobile, cus.nominee_relationship,reference_no,cus.id_employee,cus.last_sync_time,
		cus.nominee_address1,cus.nominee_address2,cus.id_customer,cus.mobile,cus.id_branch,cs.branchWiseLogin,cs.is_branchwise_cus_reg,cs.pg_email,
	  cs.branch_settings,firstname,lastname,email,address1,pincode,reference_no FROM  customer AS cus 
	  LEFT JOIN address AS addr ON cus.id_customer = addr.id_customer join chit_settings cs WHERE mobile='".$mobile."'");
	  return $query_customer->row_array();
	}

    public function activePlans(){
        $response = [];
        $sql = "SELECT bs.*,m.metal FROM `ct_advance_booking_settings` bs left join metal m on (m.id_metal = bs.id_metal) where m.id_metal = bs.id_metal and is_active = 1";
        $res = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($res);exit;
        foreach($res as $r){
            $r['metal_rate'] = $this->get_metalrate_by_branch($r['accessible_branches'],$r['id_metal'],$r['id_purity'],'');
           
            $r['description'] = 'Terms and conditions....';
            $response[$r['metal']] = $r;
            
        }
      
        return $response;
    }

    public function fetch_planFormData($id){
        if($id>0){
            $sql = "SELECT * FROM ct_advance_booking_settings WHERE id_plan =".$id;
        }else{
            //#Warn# --arrive new logic from table schema strcture of settings table and send as default form data....
            $sql = "select COLUMN_NAME as name, COLUMN_DEFAULT as default_value FROM information_schema.`COLUMNS` where TABLE_NAME = 'ct_advance_booking_settings'";
        }
        
        $res = $this->db->query($sql)->row_array();  

        $res['metal_rate'] = $this->get_metalrate_by_branch($res['accessible_branches'],$res['id_metal'],$res['id_purity'],'');
           
        return    $res     ;  
    }

    public function get_paymentData($id_cus,$source,$id_booking='',$id_plan='',$type="",$fil_status=""){
        
        
        $source = ($source == 'WEB' || $source == 'MOB' ? 'ONLINE' : $source);
        
        $sql = "SELECT  Date_format(ba.closing_date,'%d-%m%-%Y %r') as closing_date,
        IFNULL(Date_format(ba.booking_date,'%d-%m%-%Y %r'),'-') as booking_date,
        
         IFNULL(Date_format(ba.maturity_date,'%d-%m%-%Y %r'),'-') as maturity,cs.curr_symb_html,
        
                IFNULL(ba.booking_name,c.firstname) as booking_name,
              
                IFNULL(ba.booking_amount,0) as booking_amount,
                IFNULL(ba.booking_weight,0) as booking_weight,
        ba.booking_rate,CONCAT(bs.plan_code,'-',IFNULL(ba.booking_id,'Not Booked')) as booking_number,ba.status,ba.id_branch,ba.booking_id,
                if(ba.status = 1,'Open',if(ba.status=2,'Advance Paid',if(ba.status = 3, 'Paid',if(ba.status = 4,'Closed',if(ba.status = 5,'Cancelled','-'))))) as booking_status,
                bs.minimum_val as min_payable,if(bs.payable_by = 0,ba.booking_amount,ba.booking_weight) as max_payable,
                bs.*,m.metal
                FROM ct_advance_account ba
                LEFT JOIN ct_advance_booking_settings bs ON (ba.id_plan = bs.id_plan)
                left join metal m on (m.id_metal = bs.id_metal)
                left join customer c on (c.id_customer = ba.id_customer)
                JOIN chit_settings cs
                WHERE ba.id_customer = ".$id_cus."  
                ".($id_booking > 0 ? 'AND ba.booking_id='.$id_booking :'')."
                ".($id_plan > 0 ? 'AND bs.id_plan='.$id_plan :'')."
                ".($source == 'ONLINE' && $type != 'REPORT'? 'and ba.status IN (1,2)' :'')."
                ".($source == 'ONLINE' && $type == 'REPORT' && $fil_status >0 ? 'and ba.status ='.$fil_status :'')."
               and bs.is_visible = 1
               
               ORDER BY ba.booking_date DESC ";
     
//and ((ba.booking_date >= now() - interval 24 hour))
        $bookings = $this->db->query($sql);
       
        if($bookings->num_rows() > 0)
        {
            foreach($bookings->result_array() as $res)
            {
                $pay_report = [];
                
                $paid_sql = $this->db->query("SELECT bp.*, if(bp.transaction_type = 'AP',sum(bp.payment_amount),0) as advance_paid,if(bp.transaction_type = 'BP',sum(bp.payment_amount),0) as balance_paid,
                            IFNULL(sum(bp.payment_amount),0) as total_paid_amount
                            FROM ct_advance_payment bp 
                           
                            WHERE  bp.payment_status = 1 
                            ".($res['booking_id'] > 0 ? 'AND bp.id_adv_booking='.$res['booking_id'] :'')."
                            ")->row_array();
                            
                $res['total_paid_amount'] = $paid_sql['total_paid_amount'];
                $res['balance_paid'] = $paid_sql['balance_paid'];
                $res['advance_paid'] = $paid_sql['advance_paid'];
             
                // min payable, max payable, 
                
                 //amount whole
                if($res['is_adv_limit_available'] == 1 && $res['total_adv_limit_value'] > 0){
                    $total_advance_amt =  round(($res['adv_limit_type'] == 0 ? $res['total_adv_limit_value'] : ($res['booking_amount'] * ($res['total_adv_limit_value']/100))),0);
                    $total_online_adv_amt =  round(($res['adv_limit_type'] == 0 ? $res['adv_limit_value_online'] : ($res['booking_amount'] * ($res['adv_limit_value_online']/100)) ),0);

                    $res['min_amount'] = ($source == 'ONLINE' ? $total_online_adv_amt : $total_advance_amt);
                    $res['max_amount'] = ($source == 'ONLINE' ? $total_online_adv_amt : $total_advance_amt);
                
                } 
                
                $total_advance_amt = 0;
                $advance_value = '';
                $online_advance_amt = 0;
                $balance_adv_amt = 0;

               
                if($res['is_adv_limit_available'] == 1 && $res['total_adv_limit_value'] > 0){
                    
                    if($res['adv_limit_type'] == 1){
                        //percent --> eg: 20% of booked amount...
                        $total_advance_amt = round($res['booking_amount'] * ($res['total_adv_limit_value'] / 100),0);
                        $advance_value = $res['total_adv_limit_value'].' %';
                       
                        
                    }else{
                        //amount  --> specified amount in settings
                        $total_advance_amt = $res['total_adv_limit_value'];
                        $advance_value = 'INR '.$res['total_adv_limit_value'];
                    }
                    
                    $eligible_onAdv_Amt = round(abs($total_advance_amt - $res['advance_paid']),0);
                    
                    $balance_adv_amt = round(abs($total_advance_amt - $res['advance_paid']),0);
                    
                    $online_advance_amt = round($res['adv_limit_value_online'],0);
                } 
                
                $balance_amt = $res['booking_amount'] - $res['total_paid_amount'];

                $payable = ($res['is_adv_limit_available'] == 1 && $source == 'ONLINE' ? ($online_advance_amt > $eligible_onAdv_Amt ? $eligible_onAdv_Amt : $online_advance_amt) : $balance_amt) ;
        
                if($res['payable_by'] == 0){
                    //amount
                    $min_payable_amount = $res['minimum_val'] ;

                }else{
                    //weight
                    $min_payable_amount = $res['minimum_val'] *  $res['booking_rate'] ;
                }
        
                $max_payable_amount = ($source == 'ONLINE' ? $total_advance_amt : $balance_amt);


                /*if((($source != 'ONLINE' && $res['status'] == 1) || (($eligible_onAdv_Amt > 0 && $source == 'ONLINE')) && $payable > 0 && ($res['status'] != 3 || $res['status'] != 4 || $res['status'] != 5))){
                    $can_pay = 'Y';
                    $trans_type = 'AP';    //Advance Pay
                }else if($res['status'] == 2 && $source != 'ONLINE' && $payable > 0){
                    $can_pay = 'Y';
                    $trans_type = 'BP';    //Balance Pay
                }else if($res['status'] == 3 || $res['status'] == 4 || $res['status'] == 5 || ($eligible_onAdv_Amt == 0 && $source == 'ONLINE') || $payable == 0){
                    $can_pay = 'N';
                    $trans_type = '';
                }*/
                
                
                if(($res['status'] == 3 && $source == 'ONLINE') || ($res['status'] == 4 || $res['status'] == 5) || $payable <= 0){
                    $can_pay = 'N';
                    $trans_type = '';
                }
                else if((($source == 'ONLINE' && ($res['status'] == 1 || $eligible_onAdv_Amt > 0)) || ($source != 'ONLINE' && $payable > 0)) && $payable > 0){
                    $can_pay = 'Y';
                    $trans_type = 'AP';   
                } 
                
                if($can_pay == 'N' && $source == 'ONLINE'){
                    $content = "Your Booking Status is ".$res['booking_status']." .Contact store to pay balance amount: ".$res['curr_symb_html']." ".($res['booking_amount'] - $res['total_paid_amount']);
                }else if($can_pay == 'Y' && $source == 'ONLINE' && $trans_type == 'AP'){
                    $content = "Total Advance to confirm your booking : ".$res['curr_symb_html']." ".$total_advance_amt." .You can pay ".$res['curr_symb_html']." ".$online_advance_amt." only in online from that.";
                }else if($res['status'] == 3){
                    $content = "Your Booking Status is ".$res['booking_status']." .You have successfully paid booked amount...";
                }else if($res['status'] == 4){
                    $content = "Your Booking Status is ".$res['booking_status'].".";
                }else{
                    $content = "";
                }

        if($type == 'REPORT'){
             $pay_report = $this->db->query("SELECT amp.id_adv_payment as id_payment, Date_format(amp.payment_date,'%d-%m%-%Y %r') as payment_date,amp.payment_mode,amp.payment_amount,ap.id_adv_booking as booking_id,CONCAT(plan.plan_code,'-',ap.id_payment) as receipt_no,pm.mode_name
                                            FROM `ct_advbook_mode_detail` amp
                                            LEFT JOIN ct_advance_payment ap on (ap.id_payment = amp.id_adv_payment)
                                            LEFT JOIN ct_advance_account ab ON (ab.booking_id = ap.id_adv_booking)
                                            LEFT JOIN payment_mode pm ON (pm.short_code = amp.payment_mode)
                                            LEFT JOIN ct_advance_booking_settings plan ON (plan.id_plan = ab.id_plan)
                                            where ap.payment_status = 1 and amp.payment_status = 1 and ab.id_customer = ".$id_cus." and ab.booking_id = ".$res['booking_id']."
                                            and amp.is_active = 1;
            
                                        ")->result_array();   
        }
               
                
                $result[] = array(//display fields...
                                'booking_name'       => $res['booking_name'],
                                'booking_date'       => $res['booking_date'],
                                'booking_amount'     => $res['booking_amount'],
                                'booking_weight'     => $res['booking_weight'],
                                'booking_rate'       => $res['booking_rate'],
                                'booking_number'     => $res['booking_number'],
                                'booking_status'     => $res['booking_status'],
                                'status'             => $res['status'],
                                'total_advance_amt'  => $total_advance_amt,
                                'advance_value'      => $advance_value,
                                'online_advance_amt' => round($online_advance_amt,2),
                                'balance_adv_amt'    => $balance_adv_amt,
                                'balance_amt'        => $balance_amt,
        /*impo*/                'payable'            => round($payable,0),
                                'content_box'        => $content,
                                'closing_date'     => $res['closing_date'],
                                
                                //back-end hidden fields...
                                'payable_by' =>$res['payable_by'],
                                'eligible_on'        => $eligible_onAdv_Amt,
                                'advance_paid'       => $res['advance_paid'],
                                'balance_paid'       => $res['balance_paid'],
                                'total_paid_amount'  => $res['total_paid_amount'],
                                'booking_id'         => $res['booking_id'],
                                'can_pay'            => $can_pay,
                                'trans_type'         => $trans_type,     //AP -  Advance pay, BP - Balance Pay ADV_PAY, 
        /*impo*/                'min_payable_amount' => round(($res['is_adv_limit_available'] == 1 && $source == 'ONLINE' ? $payable : ($min_payable_amount > $payable ? $payable : $min_payable_amount)),2),
        /*impo*/                'max_payable_amount' => round(($res['is_adv_limit_available'] == 1 && $source == 'ONLINE' ? $payable : $max_payable_amount),2),
                                'metal'              => $res['metal'],
                                'id_metal'           => $res['id_metal'],
                                'maturity_date'     => $res['maturity'],
                                'payments'          => $pay_report,
                                );
                                
                   
                
            }
            
        }
        
        
     //  echo '<pre>';print_r($result);exit;
        
        if($id_booking > 0){
            foreach($result as $r){
                $result = $r;
            }
            return $result; 
        }else{
            return $result; 
        }
        //return $result; 
        
    }
    
    function getCusData($id)
    {
    	$sql = "Select * from customer where id_customer = ".$id;
    	return	$result = $this->db->query($sql)->row_array();		
    }
    
     function get_metalrate_by_branch($id_branch,$id_metal,$id_purity,$start_date='')
    {
        $today = date('Y-m-d H:i:s');
       
        $rate_field = '';
        if($id_purity > 0){
            $rf_sql=$this->db->query("SELECT rate_field,market_rate_field FROM `ret_metal_purity_rate` where id_metal=".$id_metal." and id_purity=".$id_purity."");
            
            if($rf_sql->num_rows() > 0 ){
                $rate_field =  $rf_sql->row("rate_field");
            }
        }
        
        if(!empty($rate_field) && $id_purity > 0 ){
            $rate_field =  $rf_sql->row("rate_field");
        }else if($id_metal > 0){
            $rate_field = ($id_metal == 2  ? "silverrate_1gm" : "goldrate_22ct");
        } 
  
        
        if($rate_field !=''){
            $data=$this->get_settings();
    		if($data['is_branchwise_rate']==1 &&$id_branch!='' && $id_branch!=NULL && $id_branch > 0)
    		{
    			$sql="select ".$rate_field." from metal_rates m
    	   		left join branch_rate br on m.id_metalrates=br.id_metalrate 
    	   		where FIND_IN_SET('".$id_branch."' , br.id_branch)
    	   		'.($start_date!=NULL?' date(m.add_date) BETWEEN '".$start_date."' AND '".$today."' ' :'').'
    	   		order by  br.id_metalrate desc limit 1";
    		}
    		else if($data['is_branchwise_rate']==1)
    		{
    			$sql="select ".$rate_field." from metal_rates 
    			left join branch_rate br on br.id_metalrate=metal_rates.id_metalrates 
    			where br.status=1
    	   		'.($start_date!=NULL?' date(metal_rates.add_date) BETWEEN '".$start_date."' AND '".$today."' ' :'').'
    			";
    		}
    		else
    		{
    			$sql="select ".$rate_field." from metal_rates 
    			left join branch_rate br on br.id_metalrate=metal_rates.id_metalrates
    			".($start_date!=NULL?" where date(metal_rates.add_date) BETWEEN '".$start_date."' AND '".$today."'" :"")."
    			order by id_metalrates desc limit 1";
    		}

    		$result = $this->db->query($sql);	
    		if($result->num_rows() > 0){
               return $result->row($rate_field);
            }else{
               return 0;
            }
        }else{
             return 0;
        }

    }
    
    function get_settings()
	{   
	    $result = array();
		$this->db->select('allow_notification,delete_unpaid,reg_existing,show_closed_list,branch_settings,is_branchwise_rate,regExistingReqOtp');
		$result= $this->db->get('chit_settings');
		return $result->row_array();
	}
	
	function updateGatewayResponse($data,$txnid)
	{
		$this->db->where('ref_trans_id',$txnid); 
		$status = $this->db->update('ct_advance_payment',$data);	
		$result=array(
		              'status' => $status,
		             'id_payment' => $this->get_lastUpdateID($txnid) 
		              );
		return $result;
	}
	
	 
    function getPayIds($txnid)
	{
    	$sql = "Select bp.id_payment,bp.payment_amount,ba.id_customer,ba.booking_id
    			 From ct_advance_payment bp
    			 left join ct_advance_account ba on ba.booking_id=bp.id_adv_booking
    			 LEFT JOIN ct_advance_booking_settings bs ON bs.id_plan = ba.id_plan

    			 Where bp.ref_trans_id='".$txnid."'";
    			 
    		//	 print_r($sql);exit;
        $pay = $this->db->query($sql)->result_array();	
    

        if(sizeof($pay) > 0){
            return $pay;
        }elseif(sizeof($g_adv) > 0){
            return $g_adv;
        } else{
            return [];
        }
	 
	
	}
	
		function get_lastUpdateID($txnid)
	{
		$this->db->select('id_payment');  
		$this->db->where('ref_trans_id',$txnid); 
		$payid = $this->db->get('ct_advance_payment');	
		return $payid->row()->id_payment;
	}
	
    //Booking List Gopal code starts
    //public function   get_prebookpayment()
   public function   old_get_prebookpayment($from_date="",$to_date="",$id_branch="",$id_customer="")
    {
       
           
        	$data = $this->db->query("SELECT ac.booking_id,ap.id_payment,c.firstname as cus_name,c.mobile,bs.plan_code,b.name as branch,ap.date_payment,ap.payment_amount,concat(m.payment_mode,'-',m.payment_amount) as  payment_mode,pm.remark as  payment_remarks,e.firstname as emp_name,IF(ap.added_by = 0, 'admin',IF(ap.added_by = 1, 'web app',IF(ap.added_by = 2, 'mobile app',IF(ap.added_by = 3, 'Admin App',IF(ap.added_by = 4, 'Cashfree Subscription',IF(ap.added_by = 5, 'Sync', '-') ) ) ) )) AS payment_through ,ap.payment_ref_number,ap.payment_type,ac.booking_amount,ac.booking_weight,ac.booking_rate,paidwt.booking_weight as paid_wt,
        	pay.totalpaid,
        	IF(
        		ac.status = 1,
        		'open',
        		IF(
        			ac.added_by = 2,
        			'Advance Done',
        			IF(
        				ac.added_by = 3,
        				'Paid',
        				IF(
        					ac.added_by = 4,
        					'Closed',
        					''
        				)
        			)
        		)
        	) AS `booking_Status`,
        	
        	IF(
        		ac.added_by = 0,
        		'WebApp',
        		IF(
        			ac.added_by = 1,
        			'Admin',
        			IF(
        				ac.added_by = 2,
        				'mobile app',
        				IF(
        					ac.added_by = 3,
        					'Collection app',
        					IF(
        						ac.added_by = 4,
        						'Retail App',
        						IF(
        							ac.added_by = 5,
        							'Sync',
        							'-'
        						)
        					)
        				)
        			)
        		)
        	) AS booking_through,ac.remark as booking_remarks,IF(ap.transaction_type='AP','Advance Pay','Balance Pay') AS transaction_type,pay.totalpaid,paidwt.booking_weight,ps.payment_status
        	FROM `ct_advance_account` ac
        	left join ct_advance_booking_settings bs on bs.id_plan=ac.id_plan
        	left join ct_advance_payment ap on ap.id_adv_booking=ac.booking_id
        	left join ct_advbook_mode_detail m on m.id_adv_payment=ap.id_payment
        	left join payment_mode_details pm  on pm.id_payment=m.id_adv_payment
        	left join payment_status_message ps on ps.id_status_msg=ap.payment_status
        	LEFT JOIN (
        			SELECT
        				booking_id,
        				SUM(booking_weight) AS booking_weight
        			FROM
        				ct_advance_account
        			GROUP BY
        				booking_id
        		) AS paidwt ON paidwt.booking_id =ap.id_adv_booking
        		
        		LEFT JOIN (
        			SELECT
        				booking_id,
        				SUM(booking_amount) AS totalpaid
        			FROM
        				ct_advance_account
        			GROUP BY
        				booking_id
        		) AS pay ON pay.booking_id =ap.id_adv_booking
        	left join customer c on c.id_customer=ac.id_customer
        	left join employee e on e.id_employee= ap.id_employee
        	left join branch b on b.id_branch=ap.id_branch 
        	where ac.status=1
        	" . ($from_date != '' && $to_date != '' ? "AND date(ap.date_payment) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "'" : '') . "
            " . ($id_branch != 0 && $id_branch !='' ? "AND b.id_branch = " . $id_branch : '') . "
        	" . ($id_customer != 0 && $id_customer !='' ? "AND c.id_customer = " . $id_customer : '') . "
        "); 
        	
        //	print_r($this->db->last_query());exit;
        	return $data->result_array();
        	
	}
    //Booking List Gopal code ends
      //Booking List Gopal code starts
    //public function   get_prebookpayment()
   public function   get_prebookpayment($from_date="",$to_date="",$id_branch="",$id_customer="",$id_booking="")
    {
      
        	$sql ="SELECT IFNULL(ac.booking_id,'-') as booking_id,
        	concat(bs.plan_code,'-',ac.booking_id) as booking_number,
            IFNULL(ap.id_payment,'-') as id_payment,
            IFNULL(concat(c.firstname,' ',if(c.lastname!='',c.lastname,'')),'-') as cus_name,
            IFNULL(c.mobile,'-') as mobile,
            IFNULL(bs.plan_code,'-') as plan_code,
            IFNULL(b.name,'-') as branch,
            IFNULL(DATE_FORMAT(ap.date_payment,'%d-%m-%Y %H:%i'),'-') as date_payment,
            IFNULL(m.payment_amount,0) as payment_amount,
            IFNULL(m.payment_mode,'-') as payment_mode,
            IFNULL(e.firstname,'-') as emp_name,
            IFNULL(IF(ap.added_by = 0, 'admin',IF(ap.added_by = 1, 'web app',IF(ap.added_by = 2, 'mobile app',IF(ap.added_by = 3, 'Admin App',IF(ap.added_by = 4, 'Cashfree Subscription',IF(ap.added_by = 5, 'Sync', '-') ) ) ) )),'-') AS payment_through ,
            IFNULL(ap.payment_ref_number,'-') as payment_ref_number,
            IFNULL(ap.payment_type,'-') as payment_type,
            IF(ap.transaction_type='AP','Advance Pay','Balance Pay') AS transaction_type,
            ps.payment_status,
            if(ap.payment_status!=1,'-',concat(bs.plan_code,' - ',ap.id_payment)) as receipt_number,
            IFNULL(ap.remark,'-') as remarks
            FROM ct_advance_payment ap
            left join `ct_advance_account` ac  on ac.booking_id =ap.id_adv_booking
            left join ct_advance_booking_settings bs on bs.id_plan=ac.id_plan 
            left join ct_advbook_mode_detail m on m.id_adv_payment=ap.id_payment 
            left join payment_status_message ps on ps.id_status_msg=ap.payment_status 
            left join customer c on c.id_customer=ac.id_customer 
            left join employee e on e.id_employee= ap.id_employee 
            left join branch b on b.id_branch=ap.id_branch";
       
            if(!empty($id_customer))
        	{
        	    $sql=$sql." where ac.id_customer =".$id_customer ;
        	}
        	else if(!empty($id_booking))
        	{
        	    $sql=$sql." where ap.payment_status=1 and ap.id_adv_booking=".$id_booking;
        	}
        	else
        	{
        	    $sql=$sql." " .($from_date != '' && $to_date != '' ? "where  date(ap.date_payment) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "'" : '') . "
                 " . ($id_branch != 0 && $id_branch !='' ? "AND ap.id_branch = " . $id_branch : '') . "";
        	}
       
        	$data= $this->db->query($sql);
        	//print_r($this->db->last_query());exit;
        	return $data->result_array();
        	
	}
    //Booking List Gopal code ends
    
    //pre booking account listing starts
     public function get_prebookacc($from_date="",$to_date="",$id_branch="",$id_customer="")
    {
        
           
        	$sql = "SELECT IFNULL(ct_acc.booking_id,'-') as booking_id,
             IFNULL(concat(c.firstname,' ',if(c.lastname!='',c.lastname,'')),'-') as cus_name,
            IFNULL(c.mobile,'-') as mobile,
            IFNULL(plan.plan_name,'-') as plan_name,
            IFNULL(ct_acc.booking_name,c.firstname) as booking_name,
            CONCAT(plan.plan_code,'-',ct_acc.booking_id) as booking_number,
            IFNULL(DATE_FORMAT(ct_acc.booking_date,'%d-%m-%Y  %H:%i'),'-') as booking_date,
            IFNULL(ct_acc.booking_amount,0) as booking_amount,
            IFNULL(ct_acc.booking_weight,0) as booking_weight,
            IFNULL(ct_acc.booking_rate,0) as booking_rate,
            IFNULL(ct_acc.added_by,'-') as added_by,
            IFNULL(ct_acc.remark,'-') as remarks,
            IFNULL(ct_acc.status,'-') as status,
            IFNULL(b.name,'-') as branch,
            
            IFNULL(concat(e.firstname,' ',if(e.lastname!='' && e.lastname!=null,e.lastname,'')),'-') as employee,
            	
            IFNULL((SELECT sum(payment_amount) from ct_advance_payment where id_adv_booking=ct_acc.booking_id and payment_status=1) ,0) as payment_amount
            
            from ct_advance_account ct_acc 
            left join customer c on c.id_customer=ct_acc.id_customer
            left join ct_advance_booking_settings plan on plan.id_plan=ct_acc.id_plan
            left join employee e on e.id_employee=ct_acc.id_employee
            left join branch b on b.id_branch=ct_acc.id_branch";
        //   where
        // 	" . ($from_date != '' && $to_date != '' ? "  date(ct_acc.booking_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "'" : '') . "
        //     " . ($id_branch != 0 && $id_branch !='' ? "AND ct_acc.id_branch = " . $id_branch : '') . "
        // 	" . ($id_customer != 0 && $id_customer !='' ? "and ct_acc.id_customer = " . $id_customer : '') . "
         
        	if(!empty($id_customer))
        	{
        	    $sql=$sql." where ct_acc.id_customer =".$id_customer ;
        	}
        	else
        	{
        	    $sql=$sql." where " .($from_date != '' && $to_date != '' ? "  date(ct_acc.booking_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "'" : '') . "
                 " . ($id_branch != 0 && $id_branch !='' ? "AND ct_acc.id_branch = " . $id_branch : '') . "";
        	}
       
        	$data=$this->db->query($sql);
        	//	print_r($this->db->last_query());exit;
        	return $data->result_array();
        	
	}
    //pre booking account listing ends
	public function insertBatchData($data,$table)
    {
    	$insert_flag = 0;
		$insert_flag = $this->db->insert_batch($table, $data);
		if ($this->db->affected_rows() > 0){
			return 1;
		}else{
			return 0;
		}
	}
	
	function company_details()
	{
		$sql = " Select  cs.maintenance_text,cs.maintenance_mode,cs.mob_code as call_prefix,c.id_company,c.whatsapp_no,c.company_name,c.short_code,c.pincode,c.mobile,c.phone,c.email,c.website,c.address1,c.address2,c.id_country,c.id_state,c.id_city,ct.name as city,s.name as state,cy.name as country,cs.currency_symbol,cs.currency_name,c.phone1,c.mobile1,cs.allow_notification
				from company c
					join chit_settings cs
					left join country cy on (c.id_country=cy.id_country)
					left join state s on (c.id_state=s.id_state)
					left join city ct on (c.id_city=ct.id_city) ";
		$result = $this->db->query($sql);	
		return $result->row_array();
	}

}
?>        