<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class ret_app_api_model extends CI_Model

{

    const EMP_IMG_PATH = 'assets/img/employee';

    const CUS_IMG_PATH = 'assets/img/customer';

    const RETCAT_IMG_PATH = 'assets/img/ret_category';

    const RETPRO_IMG_PATH = 'assets/img/ret_product';

    const RETDES_IMG_PATH = 'assets/img/design';

    const RETSUBDES_IMG_PATH = 'assets/img/sub_design';

	const TAGIMG_PATH  = 'assets/img/tag/';

	const SUPPIMG_PATH = 'assets/img/supplier/';

	function __construct()

    {

        parent::__construct();



	}



	// General Functions

    public function insertData($data,$table)

    {

    	$insert_flag = 0;

		$insert_flag = $this->db->insert($table,$data);

		return ($insert_flag == 1 ? $this->db->insert_id(): 0);

	}

	public function updateData($data,$id_field,$id_value,$table)

    {

	    $edit_flag = 0;

	    $this->db->where($id_field,$id_value);

		$edit_flag = $this->db->update($table,$data);

		return ($edit_flag==1?$id_value:0);

	}

	public function deleteData($id_field,$id_value,$table)

    {

        $this->db->where($id_field, $id_value);

        $status= $this->db->delete($table);

		return $status;

	}



	function select_otp($otp)

	{

		$this->db->select('*');

		$this->db->where('otp_code',$otp);

		$status = $this->db->get("otp");

		return $status->row_array();

	}



	function otp_update($data,$id)

	{

		$this->db->where('id_otp',$id);

		$status = $this->db->update("otp",$data);

		return $status;

	}



	public function get_emp_by_username($name)

    {

		$this->db->select('id_employee,emp_code,date_of_birth,emp_code,id_profile,username,id_branch,login_branches');

		$this->db->where('username', $name);

		$list_data=$this->db->get("employee");

		return $list_data->row_array();

	}



	function loginOTP_exp()

	{

		 $sql="Select loginOTP_exp from chit_settings where id_chit_settings = 1";

		 return $this->db->query($sql)->row()->loginOTP_exp;

	}



	//validate user login

	function isValidLogin($username,$passwd)

	{

		$record = array('is_valid' => FALSE);

		$sql="

			Select

				e.emp_code,e.username,login_branches,b.name as branch_name,p.req_otplogin,e.id_profile,e.id_employee,e.firstname,e.lastname,e.mobile,e.email,e.id_branch,e.image

			From employee e

				Left join branch b on b.id_branch=e.id_branch

				Left join profile p on p.id_profile=e.id_profile

			where e.active=1 and e.username='".$username."' and e.passwd='".$passwd."'";

		$result = $this->db->query($sql);

		if($result->num_rows() > 0)

		{

			$row = $result->row_array();

			$file = self::EMP_IMG_PATH.'/'.$row['id_employee'].'/employee.jpg';

			$img_path = ($row['image'] != null ? (file_exists($file)? $file : null ):null);

			$record = array('is_valid' => TRUE,'id_employee' => $row['id_employee'],'emp_code' => $row['emp_code'],'username' => $row['username'],'mobile' => $row['mobile'],'login_branches' => $row['login_branches'],'branch_name' => $row['branch_name'], 'email' => $row['email'],'id_profile' => $row['id_profile'],'req_otplogin' => $row['req_otplogin'],'id_branch' => $row['id_branch'], 'lastname' =>ucfirst( $row['lastname']),'firstname' => ucfirst($row['firstname']), 'image' => $img_path);

		    return $record;

		}



	}



	function send_sms($mobile,$message)

	{

		$url = $this->sms_data['sms_url'];

		$senderid  = $this->sms_data['sms_sender_id'];



	if($this->sms_chk['debit_sms']!=0){



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

		return FALSE;



	}

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



	/*function get_currency($id_branch)

	{

		$sql = " Select c.company_name,cs.currency_symbol,cs.currency_name,cs.is_branchwise_cus_reg,cs.branch_settings,'&#8377;' as curr_symb_html

		from company c

		join chit_settings cs ";



		$data=$this->get_chit_settings();



		if($data['is_branchwise_rate']==1 &&$id_branch!='' && $id_branch!=0)

		{

			$sql1="SELECT  m.market_gold_18ct,m.goldrate_18ct,m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,

				Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime

				FROM metal_rates m

				LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates

				  ".($id_branch!='' ?" WHERE br.id_branch=".$id_branch."" :'')." ORDER by br.id_metalrate desc LIMIT 1";

		}

		else{

    		$sql1="SELECT  m.market_gold_18ct,m.goldrate_18ct,m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,

    				Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime   FROM metal_rates m

    				WHERE m.id_metalrates=( SELECT max(m.id_metalrates) FROM metal_rates m )";

		}



		$data = $this->db->query($sql);

		$result['currency']=$data->row_array();

		$rate = $this->db->query($sql1);



		$result['metal_rates']=$rate->row_array();

		return $result;

	}



	function get_chit_settings()

	{

		$sql="select * from chit_settings";

		$result = $this->db->query($sql);

		return $result->row_array();

	}*/



	/** Customer functions  **/

    function insert_customer($data)

    {

		  $status = $this->db->insert("customer",$data['info']);

		  $insertID = $this->db->insert_id();

			if($insertID){

					$data['address']['id_customer']=$insertID;

					$res=$this->db->insert("address",$data['address']);

					if($res){

						$id_address=$this->db->insert_id();

						$address = array('id_address' => $id_address);

						$this->db->where('id_customer',$insertID);

						$this->db->update("customer",$address);

						$status = array("status" => true, "insertID" => $insertID);

					}else{

				 	   $status = array("status" => false, "insertID" => '');

				    }

				}

				else{

					$status = array("status" => false, "insertID" => '');

				  }

		return $status;

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

		$redeem_updated = [];

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



		$tmp_redeem_updated_1 = [];

		// To insert data from temp table

		$tmp_table_1 = $this->db->query("select date_format(iwt.entry_date,'%d-%m-%Y') as bill_date,iwd.trans_points,iwt.actual_redeemed,category_code,iwt.bill_no,trans_type

		from inter_wallet_trans_tmp_2	 iwt

		LEFT JOIN  inter_walTransDetail_tmp_1 iwd on iwd.id_inter_wallet_trans = iwt.id_inter_waltrans_tmp

		where mobile=".$mobile);

    	if($tmp_table_1->num_rows() > 0){

		    foreach($tmp_table_1->result_array() as $record){

		    	$b_date = date_create($record['bill_date']);

                $bill_date = date_format($b_date,"Y-m-d H:i:s");

    		        if($record['actual_redeemed'] > 0){

    		            $debitdata = array('id_wallet_account'  => $id_wal_ac,

                						  'date_add' 			=> date('Y-m-d H:i:s'),

                						  'date_transaction' 	=> $bill_date,

                						  'transaction_type'	=> 1, // debit

                						  'value'				=> $record['actual_redeemed'],

                						  'ref_no'              => $record['bill_no'].'-'.$record['category_code'],

                						  'description'			=> 'Debited for bill no '.$record['bill_no'].' on '.$record['bill_date'],

                						  );

    				    if(sizeof($tmp_redeem_updated_1) > 0){

    		        		$talreadyUpdated = 0;

    		        		foreach($tmp_redeem_updated_1 as $k=>$v){

								if($k == $record['bill_no']){

									$talreadyUpdated = 1;

								}

							}

							if($talreadyUpdated == 0){

								$this->db->insert('wallet_transaction',$debitdata);

    				    		$tmp_redeem_updated_1[$record['bill_no']]=1;

							}

						}else{

    				    	$this->db->insert('wallet_transaction',$debitdata);

    				    	$tmp_redeem_updated_1[$record['bill_no']]=1;

						}

    		        }

    		        if($record['trans_type'] == 1 && $record['trans_points'] >0){

    		        	$data = array('id_wallet_account'   => $id_wal_ac,

            						  'date_add' 			=> date('Y-m-d H:i:s'),

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

		$tmp_table_1->free_result();



		$tmp_redeem_updated = [];

		// To insert data from temp table

		$tmp_table = $this->db->query("select date_format(iwt.entry_date,'%d-%m-%Y') as bill_date,iwd.trans_points,iwt.actual_redeemed,category_code,iwt.bill_no,trans_type from inter_wallet_trans_tmp	 iwt

		LEFT JOIN  inter_walTransDetail_tmp iwd on iwd.id_inter_wallet_trans = iwt.id_inter_waltrans_tmp

		where mobile=".$mobile);

    	if($tmp_table->num_rows() > 0){

		    foreach($tmp_table->result_array() as $record){

		    	$b_date = date_create($record['bill_date']);

                $bill_date = date_format($b_date,"Y-m-d H:i:s");

    		        if($record['actual_redeemed'] > 0){

    		            $debitdata = array('id_wallet_account'  => $id_wal_ac,

                						  'date_add' 			=> date('Y-m-d H:i:s'),

                						  'date_transaction' 	=> $bill_date,

                						  'transaction_type'	=> 1, // debit

                						  'value'				=> $record['actual_redeemed'],

                						  'ref_no'              => $record['bill_no'].'-'.$record['category_code'],

                						  'description'			=> 'Debited for bill no '.$record['bill_no'].' on '.$record['bill_date'],

                						  );

    				    if(sizeof($tmp_redeem_updated) > 0){

    		        		$talreadyUpdated = 0;

    		        		foreach($tmp_redeem_updated as $k=>$v){

								if($k == $record['bill_no']){

									$talreadyUpdated = 1;

								}

							}

							if($talreadyUpdated == 0){

								$this->db->insert('wallet_transaction',$debitdata);

    				    		$tmp_redeem_updated[$record['bill_no']]=1;

							}

						}else{

    				    	$this->db->insert('wallet_transaction',$debitdata);

    				    	$tmp_redeem_updated[$record['bill_no']]=1;

						}

    		        }

    		        if($record['trans_type'] == 1 && $record['trans_points'] >0){

    		        	$data = array('id_wallet_account'   => $id_wal_ac,

            						  'date_add' 			=> date('Y-m-d H:i:s'),

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

		return TRUE;



	}



	function get_payment_details($id_customer,$id_branch)

	{



		$filename = base_url().'api/rate.txt';

	    $data = file_get_contents($filename);

		$result['metal_rates'] = (array) json_decode($data);



	    $schemeAcc = array();



		$sql="Select

			    sg.group_code as scheme_group_code, UNIX_TIMESTAMP(Date_Format(sg.start_date,'%Y-%m-%d')) as group_start_date,  UNIX_TIMESTAMP(Date_Format(sg.end_date,'%Y-%m-%d')) as  group_end_date,  cs.has_lucky_draw,

                s.allowSecondPay,s.free_payment,cs.firstPayamt_payable,sa.firstPayment_amt,sa.is_registered,

			    s.gst_type,s.gst,sa.id_scheme_account,

			    IF(s.discount=1,s.firstPayDisc_value,0.00) as discount_val,s.firstPayDisc_by,s.firstPayDisc,sa.is_new,

			    s.id_scheme,br.id_branch, br.short_name, br.name as branch_name,

			    c.id_customer,s.min_amount,s.max_amount,s.pay_duration,s.discount_type,s.discount_installment,s.discount,

			   sa.scheme_acc_number as chit_number,

			    IFNULL(sa.account_name,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname))) as account_name,

			    c.mobile,

			    s.scheme_type,

			    s.fix_weight,

			    s.code,

			    IFNULL(s.min_chance,0) as min_chance,

			    IFNULL(s.max_chance,0) as max_chance,

			    Format(IFNULL(s.max_weight,0),3) as max_weight, IF(s.max_weight=s.min_weight,'1','0') as wgt_type,

			    Format(IFNULL(s.min_weight,0),3) as min_weight,s.wgt_convert,

			    Date_Format(sa.start_date,'%d-%m-%Y') as start_date,

			    IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,s.max_weight,if(s.scheme_type=3,s.min_amount,0))) as payable,

				round(IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,s.max_weight,

				if(s.scheme_type=3 && s.max_amount!='',s.max_amount,0)))) as max_amount,

				(SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1)as metal_rate,

			    s.total_installments,

				s.total_installments,IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),1,0) as  previous_paid,

				IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or s.scheme_type=3 , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)

 as paid_installments,cs.branch_settings,



IFNULL(IF(sa.is_opening=1,IFNULL(balance_amount,0)+IFNULL(SUM(p.payment_amount * p.no_of_dues),0),IFNULL(SUM(p.payment_amount * p.no_of_dues),0)) ,0)

  as total_paid_amount,

FORMAT(sum(if(p.gst > 0,if((p.gst_type = 1),0,p.payment_amount-(p.payment_amount*(100/(100+p.gst)))),0)),0) as paid_gst,



IFNULL(IF(sa.is_opening=1,IFNULL(balance_weight,0)+IFNULL(SUM(p.metal_weight),0),IFNULL(SUM(p.metal_weight),0)),0.000)

 as total_paid_weight,

  if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,   (s.total_installments - if(sa.is_opening = 1,(count(DISTINCT((Date_Format(p.date_payment,'%Y%m'))))+sa.paid_installments),count(DISTINCT((Date_Format(p.date_payment,'%Y%m')))))),ifnull(((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))+1) - IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))),if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,s.total_installments,(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))))) as totalunpaid,

   IFNULL(if(Date_Format(max(p.date_add),'%Y%m') = Date_Format(curdate(),'%Y%m'), (select SUM(ip.no_of_dues) from payment ip where Date_Format(ip.date_add,'%Y%m') = Date_Format(curdate(),'%Y%m') and sa.id_scheme_account = ip.id_scheme_account),IF(sa.is_opening=1, if(Date_Format(sa.last_paid_date,'%Y%m') = Date_Format(curdate(),'%Y%m'), 1,0),0)),0) as currentmonthpaycount,

  (select SUM(pay.no_of_dues) from payment pay where pay.id_scheme_account= sa.id_scheme_account and pay.due_type='AD' and (pay.payment_status=1 or pay.payment_status=2)) as currentmonth_adv_paycount,

  (select SUM(pay.no_of_dues) from payment pay where pay.id_scheme_account= sa.id_scheme_account and pay.due_type='PD' and (pay.payment_status=1 or pay.payment_status=2)) as currentmonth_pend_paycount,

IF(s.scheme_type =1 and s.max_weight!=s.min_weight,true,false) as is_flexible_wgt,

			    round(IFNULL(cp.total_amount,0)) as  current_total_amount,

			    Format(IFNULL(cp.total_weight,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_weight),0) ,3) as  current_total_weight,

			    IFNULL(cp.paid_installment,0)       as  current_paid_installments,

			   			    IFNULL(cp.chances,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_chances),0) as  current_chances_used,

							if(s.scheme_type=3 && s.pay_duration=0 ,IFNULL(sp.chance,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_chances),0),IFNULL(cp.chances,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_chances),0)) as  current_chances_pay,

			    s.is_pan_required,

			    IFNULL(Date_Format(max(p.date_payment),'%d-%m-%Y'),IFNULL(IF(sa.is_opening=1,Date_Format(sa.last_paid_date,'%d-%m-%Y'),'')  ,0))                 as last_paid_date,

					IFNULL(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'),Date_Format(max(p.date_add),'%Y%m')),IF(sa.is_opening=1,PERIOD_DIFF(Date_Format(curdate(),'%Y%m'),Date_Format(sa.last_paid_date,'%Y%m')),0)) as last_paid_duration,

			    month(max(p.date_payment)) as last_paid_month,

				IF(sa.is_opening = 1 and s.scheme_type = 0 || s.scheme_type = 2,

				IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),false,true),

				true) AS previous_amount_eligible,

				count(pp.id_scheme_account) as cur_month_pdc,

				s.allow_unpaid,

				if(s.allow_unpaid=1,s.unpaid_months,0) as allow_unpaid_months,

				s.allow_advance,

				if(s.allow_advance=1,s.advance_months,0) as advance_months,

				if(s.allow_preclose=1,preclose_months,0) as allow_preclose_months,

				sa.disable_payment,s.charge,s.charge_type,s.charge_head,p.payment_status,

				cs.currency_name,

				cs.currency_symbol

			From scheme_account sa

			Left Join scheme s On (sa.id_scheme=s.id_scheme)



			Left Join branch br  On (br.id_branch=sa.id_branch)

			Left Join scheme_group sg On (sa.group_code = sg.group_code )

			Left Join payment p On (sa.id_scheme_account=p.id_scheme_account and (p.payment_status=1 or p.payment_status=2))

			Left Join customer c On (sa.id_customer=c.id_customer and c.active=1)

			Left Join

			(	Select

				  sa.id_scheme_account,

				  COUNT(Distinct Date_Format(p.date_add,'%Y%m')) as paid_installment,

				  COUNT(Date_Format(p.date_add,'%Y%m')) as chances,

				  SUM(p.payment_amount) as total_amount,

				  SUM(p.metal_weight) as total_weight

				From payment p

				Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0)

				Where (p.payment_status=1 or p.payment_status=2) and  Date_Format(Current_Date(),'%Y%m')=Date_Format(p.date_add,'%Y%m')

				Group By sa.id_scheme_account

			) cp On (sa.id_scheme_account=cp.id_scheme_account)





			left join(Select sa.id_scheme_account, COUNT(Distinct Date_Format(p.date_add,'%d%m')) as paid_installment,

					COUNT(Date_Format(p.date_add,'%d%m')) as chance,

					SUM(p.payment_amount) as total_amount,

					SUM(p.metal_weight) as total_weight

					From payment p

					Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0)

					Where  (p.payment_status=2 or p.payment_status=1) and  Date_Format(Current_Date(),'%d%m')=Date_Format(p.date_add,'%d%m')

					Group By sa.id_scheme_account)sp on(sa.id_scheme_account=sp.id_scheme_account)

			 Left Join postdate_payment pp On (sa.id_scheme_account=pp.id_scheme_account and (pp.payment_status=2 or pp.payment_status=7) and (Date_Format(pp.date_payment,'%Y%m')=Date_Format(curdate(),'%Y%m')))

				JOIN chit_settings cs

		Where sa.active=1 and sa.is_closed = 0 and c.id_customer=".$id_customer."   ".($id_branch > 0 ? 'and sa.id_branch='.$id_branch :'')."

			Group By sa.id_scheme_account";





		$records = $this->db->query($sql);



		if($records->num_rows()>0)

		{

			foreach($records->result() as $record)

			{



				$allowed_due = 0;

				$due_type = '';

				$checkDues = TRUE;

				$allowSecondPay = FALSE;



				if($record->has_lucky_draw == 1 )

				{

					if( $record->group_start_date == NULL && $record->paid_installments > 1)

					{ // block 2nd payment if scheme_group_code is not updated

						$checkDues = FALSE;

					}



				    else if($record->group_start_date != NULL)

					{ // block before start date and payment after end date

						 if($record->group_end_date >= time() && $record->group_start_date <= time() ){

        				 		$checkDues = TRUE;

        				 }else{

        					$checkDues = FALSE;

        				 }

					}

				}



				if($checkDues)

				{

				if($record->paid_installments > 0 || $record->totalunpaid >0){

					if($record->currentmonthpaycount == 0){  // current month not paid (allowed pending due + current due)

						if($record->allow_unpaid == 1){

							if($record->allow_unpaid_months > 0 && ($record->total_installments - $record->paid_installments) >=  $record->allow_unpaid_months && $record->totalunpaid >0){

								if(($record->total_installments - $record->paid_installments) ==  $record->allow_unpaid_months){

									$allowed_due =  $record->allow_unpaid_months ;

								    $due_type = 'PD'; //  pending

								}

								else{

									$allowed_due =  $record->allow_unpaid_months+1 ;

								    $due_type = 'PN'; // normal and pending

								}



							}

							else{

							     $allowed_due =  1;

							     $due_type = 'ND'; // normal due

							}

						}

						else{

							 $allowed_due =  1;

							 $due_type = 'ND'; // normal due

						}

					}

					else{ 	//current month paid

					    if($record->free_payment == 1 && $record->allowSecondPay == 1 && $record->paid_installments == 1){

							$allowed_due =  1 ;

							$due_type = 'AD'; // adv due

							$allowSecondPay = TRUE;

						}else{

    						if($record->allow_unpaid == 1 && $record->allow_unpaid_months >0 && $record->totalunpaid >0 && ($record->currentmonthpaycount-1) < $record->allow_unpaid_months){



    							// can pay previous pending dues if attempts available



    							if($record->totalunpaid > $record->allow_unpaid_months){



    								 $allowed_due =  $record->allow_unpaid_months ;



    								 $due_type = 'PD'; // pending due



    							}



    							else{



    								 $allowed_due =  $record->totalunpaid;



    								 $due_type = 'PD'; // pending due



    							}



    						}



    						else{  // check allow advance



    							if($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonth_adv_paycount) < $record->advance_months){



    								if(($record->advance_months + $record->paid_installments) <= $record->total_installments){



    									 $allowed_due =  ($record->advance_months - ($record->currentmonth_adv_paycount));



    									 $due_type = 'AD'; // advance due



    								}



    								else{



    									 $allowed_due =  ($record->total_installments - $record->paid_installments);



    									 $due_type = 'AD'; // advance due



    								}



    							}



    							else{ // have to check



    								 $allowed_due =  0;



    								 $due_type = ''; // normal due



    							}







    						}



					    }

					}

				}

				else{  // check allow advance and add due with currect month (allowed advance due + current due)

					if($record->allow_advance ==1){ // check allow advance

						if($record->advance_months > 0 && $record->advance_months <= ($record->total_installments - $record->paid_installments)){

							if(($record->total_installments - $record->paid_installments) ==  $record->advance_months){

									 $allowed_due =  $record->advance_months;

									 $due_type = 'AN'; // advance and normal

								}

								else{

									$allowed_due =  $record->advance_months+1 ;

								     $due_type = 'AN'; // advance and normal

								}



						}

						else{

							 $allowed_due =  1;

							 $due_type = 'ND'; // normal due

						}

					}

					else{

						 $allowed_due =  1;

						 $due_type = 'ND'; // normal due

					}



				}

			}

				$schemeAcc[] = array(

										'gst_type'					=> $record->gst_type,

										'pay_duration' 		    	=> $record->pay_duration,

										'branch_settings' 		    => $record->branch_settings,

										'min_chance' 		        => $record->min_chance,

										'max_chance' 		        => $record->max_chance,

										'min_amount' 		        => $record->min_amount,

										'firstPayment_amt' 		    => $record->firstPayment_amt,

										'firstPayamt_payable' 		=> $record->firstPayamt_payable,

										'multiply_value' 		    => 500,

										'max_amount'                => round(($record->scheme_type==3 && $record->max_amount!=0 && $record->max_amount!=''? ((($record->firstPayamt_payable==1) && ($record->paid_installments>0))|| ($record->is_registered==1)?$record->firstPayment_amt :($record->max_amount - str_replace(',', '',$record->current_total_amount))):

										($record->scheme_type==3 && $record->max_weight!=0 && $record->max_weight!=''? (($record->max_weight - $record->current_total_weight)*$record->metal_rate) : $record->payable))),

										'metal_rate'                => $record->metal_rate,

										'gst' 						=> $record->gst,

										'paid_gst' 					=> $record->paid_gst,

										'id_branch' 				=> $record->id_branch,

										'short_name' 				=> $record->short_name,

										'branch_name' 				=> $record->branch_name,

										'currentmonthpaycount' 		=> $record->currentmonthpaycount,

										'totalunpaid' 				=> $record->totalunpaid,

										'id_scheme_account' 		=> $record->id_scheme_account,

										'max_wgt_rate' 				=> ($record->is_flexible_wgt == 1?($record->max_weight - $record->current_total_weight):$record->max_weight) * $result['metal_rates']['goldrate_22ct'],

										'charge_head' 				=> $record->charge_head,

										'charge_type' 				=> $record->charge_type,

										'charge' 					=> $record->charge,

										'chit_number' 				=> $record->chit_number,

										'account_name' 				=> $record->account_name,

										'start_date' 				=> $record->start_date,

										'mobile' 					=> $record->mobile,

										'is_flexible_wgt' 	    	=> $record->is_flexible_wgt,

										'currency_symbol' 			=> $record->currency_symbol,

										'payable' => (($record->scheme_type==3 && $record->max_amount!=0  && $record->max_amount!=''?((($record->firstPayamt_payable==1)&&($record->paid_installments>0)||($record->is_registered==1))?round($record->firstPayment_amt) :round($record->max_amount-str_replace(',', '',$record->current_total_amount))):($record->scheme_type==3 && ($record->max_weight!=0 || $record->max_weight!='')? round(($record->max_weight - $record->current_total_weight)*$record->metal_rate) : $record->payable))),

										'code' 						=> ($record->has_lucky_draw == 1 ?  $record->scheme_group_code : $record->code),

										'scheme_type' 				=> $record->scheme_type,

										'total_installments'		=> $record->total_installments,

										'paid_installments' 		=> $record->paid_installments,

										'total_paid_amount' 		=> $record->total_paid_amount,

										'total_paid_weight' 		=> $record->total_paid_weight,

										'current_total_amount' 		=> $record->current_total_amount,

										'current_paid_installments'	=> $record->current_paid_installments,

										'current_chances_used' 		=> $record->current_chances_used,

										'current_chances_pay'       => $record->current_chances_pay,

										'eligible_weight' 		    => ($record->max_weight - $record->current_total_weight),

										'allow_unpaid_months' 		=> $record->allow_unpaid_months,

										'last_paid_duration' 		=> $record->last_paid_duration,

										'last_paid_date' 			=> $record->last_paid_date,

										'last_paid_month' 			=> ($record->last_paid_month!='' || $record->last_paid_month!=NULL ? $record->last_paid_month : 0),

										'is_pan_required' 			=> $record->is_pan_required,

										'wgt_convert' 			    => $record->wgt_convert,

										'last_transaction'  	    => $this->getLastTransaction($record->id_scheme_account),

										'isPaymentExist' 			=> $this->isPaymentExist($record->id_scheme_account),

										'isPendingStatExist' 		=> $this->isPendingStatExist($record->id_scheme_account),

										'max_weight' 				=> $record->max_weight,

										'current_total_weight' 		=> $record->current_total_weight,

										'previous_amount_eligible' 	=> $record->previous_amount_eligible,

										'cur_month_pdc' 			=> $record->cur_month_pdc,

										'allow_pay'  => ($checkDues? ($allowSecondPay == FALSE ? ($record->scheme_type==3 && $record->paid_installments <= $record->total_installments && (($record-> current_total_amount < $record-> max_amount || $record-> current_total_weight < $record-> max_weight)) && $record->current_chances_pay < $record->max_chance ?'Y':($record->disable_payment != 1 && ($record->payment_status !=2) ? ($record->cur_month_pdc < 1 ? ($record->paid_installments <= $record->total_installments ?($record->is_flexible_wgt?($record->current_total_weight >= $record->max_weight || $record->current_chances_used >= $record->max_chance ?'N':'Y'):($record->paid_installments <  $record->total_installments ?($record->allow_unpaid == 1  && $record->totalunpaid >0 && ($record->currentmonthpaycount-1) < $record->allow_unpaid_months ?'Y':($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount -1) < $record->advance_months ?'Y':($record->currentmonthpaycount == 0 ? 'Y': 'N'))):'N')):'N'):'N'):'N')):'Y' ):'N'),

										'allowed_dues'  			=>($record->is_flexible_wgt == 1 && $record->scheme_type==3? 1:$allowed_due),

										'allowPayDisc'      		=> ($record->discount == 1 ? ($record->discount_type == 0? 'All': $record->discount_installment ) : 0),

										'firstPayDisc' 				=> $record->firstPayDisc,

										'firstPayDisc_by' 			=> $record->firstPayDisc_by,

										'discount_val' 				=> $record->discount_val,

									 	'due_type' 					=> ($record->is_flexible_wgt == 1 ? 'ND':$due_type),

										'max_allowed_limit'  		=>($record->is_flexible_wgt == 1 ? 1:$allowed_due),

										'sel_due'  					=>1,   //default selected due

										'pdc_payments'  			=>($record->cur_month_pdc > 0 ? $this->get_postdated_payment($record->id_scheme_account) : NULL)

									);

			}



				  return array('chits' => $schemeAcc);

		}





	}



	//get last paid entry

	function getLastTransaction($id_scheme_account)

	{

		$sql="Select no_of_dues,payment_amount,due_type,act_amount,payment_status from payment

			  Where (payment_status=1 Or payment_status=2 Or payment_status=7)

			         And id_scheme_account='$id_scheme_account'";

		return $this->db->query($sql)->row_array();

	}



	//to check whether customer has payment entry

	function isPaymentExist($id_scheme_account)

	{

		$sql = "Select

					  sa.id_scheme_account,c.mobile

				From payment p

				Left Join scheme_account sa On (p.id_scheme_account = sa.id_scheme_account)

				Left Join customer c on (sa.id_customer = c.id_customer)

				Where (p.payment_status = 2 or p.payment_status = 1) And sa.id_scheme_account= '".$id_scheme_account."' ";



			$records = $this->db->query($sql);



		if($records->num_rows()>0)

		{

			return TRUE;

		}else{

			return FALSE;

		}

	}

	//to check whether customer has pending status payment entry

	function isPendingStatExist($id_scheme_account)

	{

		$sql = "Select

					  sa.id_scheme_account,c.mobile

				From payment p

				Left Join scheme_account sa On (p.id_scheme_account = sa.id_scheme_account)

				Left Join customer c on (sa.id_customer = c.id_customer)

				Where (p.payment_status = 7) And sa.id_scheme_account= '".$id_scheme_account."' ";



			$records = $this->db->query($sql);





		if($records->num_rows()>0)

		{

			return TRUE;

		}else{

			return FALSE;

		}

	}



		//Checking the customer mobile already registered

	function isMobileExists($mobile)

	{

		$this->db->select('mobile');

		$this->db->where('mobile', $mobile);



		$customer= $this->db->get("customer");

		if($customer->num_rows()>0)

		{

			return TRUE;

		}

	}

	function clientEmail($id)

	{

	$resultset = $this->db->query("select email from customer where email='".$id."'");

		return ($resultset->num_rows() > 0 ? TRUE : FALSE);



	}

	function get_customerByMobile($mobile,$emp_branch,$br_settings)

	{

		$record = array();

		$sql="Select c.id_customer,c.firstname,c.lastname,c.notification,c.mobile,c.email,c.id_branch,c.cus_img From customer c where c.mobile=".$mobile;

		/*if($br_settings == 1){

			if($emp_branch != NULL){

				$sql = $sql." and c.id_branch=".$emp_branch;

			}

		}*/

		$result = $this->db->query($sql);

		if($result->num_rows() > 0)

		{

				$row = $result->row_array();

				$file = self::CUS_IMG_PATH.'/'.$row['id_customer'].'/customer.jpg';

				$img_path = ($row['cus_img'] != null ? (file_exists($file)? $file : null ):null);

				$record = array('mobile' => $row['mobile'],'id_customer' => $row['id_customer'], 'email' => $row['email'],'id_branch' => $row['id_branch'], 'lastname' =>ucfirst( $row['lastname']),'firstname' => ucfirst($row['firstname']), 'cus_img' => $img_path);

		}

		return $record;

	}



	function cusByMobileBranchWise($mobile,$emp_branch,$br_settings)

	{

		$record = array();

		$sql="Select c.id_customer,c.firstname,c.lastname,c.notification,c.mobile,c.email,c.id_branch,c.cus_img From customer c where c.mobile=".$mobile;

		if($br_settings == 1){

			if($emp_branch != NULL){

				$sql = $sql." and c.id_branch=".$emp_branch;

			}

		}

		$result = $this->db->query($sql);

		if($result->num_rows() > 0)

		{

				$row = $result->row_array();

				$file = self::CUS_IMG_PATH.'/'.$row['id_customer'].'/customer.jpg';

				$img_path = ($row['cus_img'] != null ? (file_exists($file)? $file : null ):null);

				$record = array('mobile' => $row['mobile'],'id_customer' => $row['id_customer'], 'email' => $row['email'],'id_branch' => $row['id_branch'], 'lastname' =>ucfirst( $row['lastname']),'firstname' => ucfirst($row['firstname']), 'cus_img' => $img_path);

		}

		return $record;

	}



	function getWalletPaymentContent($id_payment){

	    $sql="Select

				  p.id_payment,iwa.available_points,sa.id_branch as branch,sa.id_scheme_account,cs.schemeacc_no_set,sa.id_scheme,cs.receipt_no_set,cs.scheme_wise_receipt,sa.scheme_acc_number,

				  ifnull(iwa.mobile,0) as isAvail,c.email,c.mobile,redeemed_amount,actual_trans_amt,cs.allow_referral,cs.walletIntegration,c.id_customer,cs.wallet_points,cs.wallet_amt_per_points,cs.wallet_balance_type

				From payment p

				Join chit_settings cs

				Left Join scheme_account sa on (p.id_scheme_account=sa.id_scheme_account)

				Left Join customer c on (c.id_customer=sa.id_customer)

				LEFT JOIN inter_wallet_account iwa on iwa.mobile=c.mobile

				Where p.id_payment='".$id_payment."'";

	      return $this->db->query($sql)->row_array();

	}



	function getPayGenData($id_payment)

	{

		$sql = "Select p.id_payment,sa.id_scheme_account,sa.scheme_acc_number,sa.id_scheme,cs.schemeacc_no_set,cs.receipt_no_set,cs.scheme_wise_receipt,p.ref_trans_id,cs.edit_custom_entry_date,cs.custom_entry_date

				 From payment p

				 left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account

				 join chit_settings cs

				 Where p.id_payment=".$id_payment;



		return $this->db->query($sql)->row_array();

	}



	function get_paymenthistory($mobile,$id_employee)

	{

		$records = array();

		$query_scheme = $this->db->query("select id_payment, DATE_FORMAT(date_payment,'%d-%m-%Y') AS date_payment, metal_rate, payment_amount, metal_weight,pay.receipt_no,pay.add_charges,if(pay.payment_type = 'Payu checkout',(payment_amount+ifnull(add_charges,0.00)), payment_amount) as total_amt,sch.charge_head,pay.gst,pay.gst_type,br.id_branch, br.short_name, br.name as branch_name,cs.branch_settings,IFNULL(sa.account_name,'-') as account_name,sa.id_scheme_account,pay.id_employee,

		(select name from branch b where b.id_branch =cus.id_branch) as cus_branch_name,

									 if(payment_mode='CC','Credit Card',if(payment_mode='NB','Net Banking',

									  if(payment_mode='CD','Cheque or DD',if(payment_mode='CO','Cash Pick Up',

									  if(payment_mode='OP','Other',if(payment_mode='DC','Debit Card', if(payment_mode='FP','Enrollment Offer',if(payment_mode='CSH','Cash',payment_mode)) )))))) as payment_mode,IFNULL(id_transaction,'-') as id_transaction, if(payment_status = 1, 'Success',if(payment_status = 2, 'Yet to Approve',if(payment_status = 5, 'Returned',if(payment_status = 6, 'Refund',if(payment_status = 7, 'Pending',if(payment_status = 3, 'Failed',if(payment_status = 4, 'Cancelled','')))))))

									  as payment_status ,sch.code , IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number ,ref_no AS client_id, scheme_name,'&#8377;' as curr_symb_html,cs.currency_symbol,pay.payment_type,

										if(scheme_type = 0,'Amount Scheme',IF(scheme_type=1,'Weight Scheme','Amt to Wgt scheme')) as scheme_type,date(pay.entry_date) as entry_date,



										sa.group_code as scheme_group_code,cs.has_lucky_draw

										FROM payment as pay

										left join scheme_account AS sa on sa.id_scheme_account = pay.id_scheme_account

										Left Join branch br  On (pay.id_branch=br.id_branch)

										left join scheme as sch on sch.id_scheme = sa.id_scheme

										left join customer as cus on  cus.id_customer = sa.id_customer

										left join payment_mode pm on pay.payment_mode=pm.short_code

										join chit_settings cs

										WHERE cus.mobile=".$mobile." ORDER By id_payment DESC");

			if($query_scheme->num_rows() > 0)

			{

				foreach($query_scheme->result() as $row)

				{



			/*Add GST GST Amount = ( Original Cost * GST% ) / 100 Net Price = Original Cost + GST Amount

			Remove GST GST Amount = Original Cost - ( Original Cost * ( 100 / ( 100 + GST% ) ) ) Net Price = Original Cost - GST Amount */



			$paid_gst = 0.00;

			$add_gst = 0.00;

			$ins_no = "";

			$allow_print = 0;



			if($row->payment_status == 'Yet to Approve' || $row->payment_status == 'Success'){

				$ins_no = $this->getInsNo($row->id_payment,$row->id_scheme_account);

				if($row->id_employee == $id_employee && $row->entry_date == date('Y-m-d')){

					$allow_print = 1;

				}

			}



			if($row->gst > 0){

				if($row->gst_type == 1){

					$paid_gst = $row->payment_amount*($row->gst/100);

					$add_gst = $paid_gst;

				}

				else{

					$paid_gst = $row->payment_amount-($row->payment_amount*(100/(100+$row->gst)));

				}

			}

					$records[] = array('allow_print' => $allow_print,'paid_due' => $ins_no,'cus_branch_name' => $row->cus_branch_name,'curr_symb_html' => $row->curr_symb_html,'ac_name' => $row->account_name,'id_payment' => $row->id_payment,'date_payment' => $row->date_payment,'receipt_no' => $row->receipt_no,'metal_rate' => $row->metal_rate, 'payment_amount' => number_format($row->payment_amount),'metal_weight' => $row->metal_weight,'payment_mode' => $row->payment_mode,'id_branch' => $row->id_branch,'short_name' => $row->short_name,'branch_name' => $row->branch_name,	'branch_settings' => $row->branch_settings,'id_transaction' => $row->id_transaction,'payment_status' => $row->payment_status,'scheme_acc_number' => ($row->has_lucky_draw == 1 ? $row->scheme_group_code.' '.$row->scheme_acc_number : $row->code.' '.$row->scheme_acc_number),'client_id' => $row->client_id,'scheme_name' => $row->scheme_name, 'scheme_type' => $row->scheme_type, 'currency_symbol' => $row->currency_symbol, 'add_charges' => $row->add_charges, 'payment_type' => ($row->payment_type == 'CSH'?'Cash':$row->payment_type), 'total_amt' => number_format(($row->total_amt+$add_gst),'0','.',''), 'charge_head' => $row->charge_head, 'gst' => $row->gst, 'gst_type' => $row->gst_type,'paid_gst'=>number_format($paid_gst,'2','.',''));

				}

			}

		return $records;

	}



	function getInsNo($id_payment,$schId){

		$sql = $this->db->query("select IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')),COUNT(Distinct Date_Format(pay.date_payment,'%Y%m'))) as installment

			from payment pay

			left join scheme_account sa on sa.id_scheme_account=pay.id_scheme_account

			where pay.id_scheme_account=".$schId." and id_payment<=".$id_payment." group by pay.id_scheme_account");//echo $this->db->last_query();exit;

		return $sql->row('installment');

	}



	function get_branch($emp_branch,$id_profile)

	{

		if($id_profile == 1 || $id_profile == 2 ){

			$sql = "SELECT id_branch,b.name,0 as is_default FROM branch b  where  active=1";

		}else{

			if($emp_branch == NULL){

				$sql = "SELECT id_branch,b.name,0 as is_default FROM branch b  where (show_to_all = 1 or show_to_all = 2) and active=1";

			}else{

				$sql = "SELECT id_branch,b.name,0 as is_default FROM branch b  where (show_to_all = 1 or show_to_all = 2) and active=1 and id_branch=".$emp_branch;

			}

		}



		$branch = $this->db->query($sql)->result_array();

		return $branch;

	}



	function getChitSettings(){

		$sql = $this->db->query("

		SELECT

			id_chit_settings,allow_join_multiple,allow_join_unpaid,currency_name,currency_symbol,mob_code,mob_no_len,maintenance_mode,maintenance_text,branch_settings,branchWiseLogin,is_branchwise_cus_reg,is_branchwise_rate,login_branch,'&#8377;' as curr_symb_html

		FROM chit_settings");

		return $sql->row_array();

	}



	function currencyAndSettings($id_branch, $id_metalrate = 0)

	{
		$where = "";

		if($id_metalrate > 0) {

			$where = " AND m.id_metalrates = ".$id_metalrate;

		}

		$settings = $this->getChitSettings();

		$result['settings'] = $settings;

		if($settings['is_branchwise_rate'] == 1) // Branchwise Rate

		{

		    if($id_branch!='' && $id_branch!=0)

		    {

                $rateSQL = "SELECT  b.name as name,m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,
                m.goldrate_18ct,
                m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,

                Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime

                FROM metal_rates m

	                LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates

	                LEFT JOIN branch b on b.id_branch=br.id_branch

                ".($id_branch!='' ?" WHERE br.id_branch=".$id_branch."" :'')." ".$where." ORDER by br.id_metalrate desc LIMIT 1";

		    }

		    else

		    {

		        $rateSQL = "SELECT

		        	b.name as name,m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,
		        	m.goldrate_18ct,
		        	m.mjdmasilverrate_1gm,platinum_1g, Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime

                FROM metal_rates m

	                LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates

	                LEFT JOIN branch b on b.id_branch=br.id_branch

                WHERE br.id_branch=1 ".$where." ORDER by br.id_metalrate desc LIMIT 1";

		    }

		}

		else{ // Common Rate

			$rateSQL = "SELECT

					m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,

					m.goldrate_18ct,
					m.mjdmasilverrate_1gm,platinum_1g,

					Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime

				FROM metal_rates m  WHERE 1	".$where."

				ORDER BY id_metalrates DESC LIMIT 1";

		}

		$result['metal_rates'] = $this->db->query($rateSQL)->row_array();



		/*$data = $this->db->query($sql);

		$result['currency']=$data->row_array();*/



		if($result['metal_rates']['silverrate_1gm'] == 0)

		{

		    $silver="SELECT m.id_metalrates,m.silverrate_1gm FROM metal_rates m WHERE m.silverrate_1gm!='0.00' ORDER by m.id_metalrates DESC LIMIT 1";

		    $silver_rate = $this->db->query($silver)->row_array();

		    $result['metal_rates']['silverrate_1gm']=$silver_rate['silverrate_1gm'];

		}

		if($result['metal_rates']['platinum_1g']==0)

		{

		    $silver="SELECT m.id_metalrates,m.platinum_1g FROM metal_rates m WHERE m.silverrate_1gm!='0.00' ORDER by m.id_metalrates DESC LIMIT 1";

		    $silver_rate = $this->db->query($silver)->row_array();

		    $result['metal_rates']['platinum_1g']=$silver_rate['platinum_1g'];

		}



		$sql_ret_sett = "SELECT name,value,description from ret_settings";

		$ret_settings = $this->db->query($sql_ret_sett)->result_array();

		foreach($ret_settings as $r){

		    $result['ret_settings'][$r['name']] = $r['value'];

		}



		return $result;

	}

	function getBranches($type){

		if($type == "list"){

			$sql = $this->db->query("SELECT b.is_ho,b.name,b.id_branch,0 as is_default FROM branch b Where active = 1");

		}else{



		}

		return $sql->result_array();

	}



	function getBranchEmployees($id_branch){

		$result = [];

		$data = $this->db->query("SELECT

									e.id_employee,

									CONCAT(CONCAT(IFNULL(e.emp_code,''),'-',e.firstname),' ',e.lastname)as emp_name,

									s.disc_limit_type,s.disc_limit,e.login_branches,

									s.allow_branch_transfer,cs.login_branch,

									IF(s.allowed_old_met_pur = 1,'1-Gold,2-Silver,3-Platinum',IF(s.allowed_old_met_pur = 2,'1-Gold',IF(s.allowed_old_met_pur = 3,'2-Silver','')) ) as allowed_old_met_pur

								FROM employee e

									LEFT JOIN employee_settings s on s.id_employee = e.id_employee

									JOIN chit_settings cs");

		$employees = $data->result_array();

		if(sizeof($employees) > 0){

			if($id_branch == 0 || $id_branch == '' || $employees[0]['login_branch'] == 0){

				return $employees;

			}else{

				foreach($employees as $emp){

					if($emp['login_branches'] == 0 || $emp['login_branches'] == NULL){

						$result[] = array(

									"id_employee" 			=> $emp['id_employee'],

									"emp_name" 				=> $emp['emp_name'],

									"disc_limit_type"		=> $emp['disc_limit_type'],

									"disc_limit" 			=> $emp['disc_limit'],

									"allowed_old_met_pur"	=> $emp['allowed_old_met_pur'],

									"allow_branch_transfer" => $emp['allow_branch_transfer']

								);

					}else{

						$login_branches = explode(',',$emp['login_branches']);

						foreach($login_branches as $b){

							if($b == $id_branch){

								$result[] = array(

										"id_employee" 			=> $emp['id_employee'],

										"emp_name" 				=> $emp['emp_name'],

										"disc_limit_type"		=> $emp['disc_limit_type'],

										"disc_limit" 			=> $emp['disc_limit'],

										"allowed_old_met_pur"	=> $emp['allowed_old_met_pur'],

										"allow_branch_transfer" => $emp['allow_branch_transfer']

									);

							}

						}

					}



				}

				return $result;

			}

		}else{

			return $result;

		}

	}



function getAvailableCustomers($SearchTxt){

		$return_data = array();

		$data = $this->db->query("SELECT c.id_customer, concat(c.firstname,'-',c.mobile) as label,c.id_village,v.village_name,is_vip,firstname,mobile,cus_type,addr.id_state,

			(select count(sa.id_scheme_account) from scheme_account sa where sa.id_customer=c.id_customer) as accounts

			FROM customer c

			left join village v on v.id_village=c.id_village

			LEFT JOIN address addr ON addr.id_customer = c.id_customer

			WHERE username like '%".$SearchTxt."%' OR mobile like '%".$SearchTxt."%' OR firstname like '%".$SearchTxt."%'");

		$result = $data->result_array();

		foreach($result as $res) {

			$res['cus_reviews'] = $this->get_customer_review($res['id_customer']);

			$res['last_review'] = isset($res['cus_reviews'][0]) ? $res['cus_reviews'][0] : NULL;

			$res['cus_review_avg'] = $this->get_cus_review_avg($res['id_customer']);

			$return_data[] = $res;

		}

		return $return_data;

	}

	function get_cus_review_avg($cus_id) {

		$sql = "SELECT

					IFNULL(AVG(IFNULL(cr.rating,0)),0) as avg_rating

				FROM  ret_customer_review AS cr

				WHERE cr.cus_id = ".$cus_id;

		$sql = $this->db->query($sql);

		$data = $sql->row_array();

		$sql->free_result();

		return $data['avg_rating']+0;

	}


	function get_customer_review($cus_id) {

		$sql = "SELECT

					cr.id_customer_review,

					cr.branch_id,

					cr.cus_id,

					cr.esti_id,

					cr.rating,

					cr.review,

					cr.suggestion,

					DATE_FORMAT(cr.created_on, '%d-%m-%Y %H:%i:%s') AS created_on,

					cr.created_by,

					CONCAT(IFNULL(c.firstname, ''),' ',IFNULL(c.lastname, '')) AS cus_name,

					b.name AS branch_name

				FROM  ret_customer_review AS cr

				LEFT JOIN customer AS c ON c.id_customer = cr.cus_id

				LEFT JOIN branch AS b ON b.id_branch = cr.branch_id

				WHERE cr.cus_id = ".$cus_id."

				ORDER BY cr.id_customer_review DESC";

				//echo $sql;exit;

		$sql = $this->db->query($sql);

		$data = $sql->result_array();

		$sql->free_result();

		return $data;

	}



	function getUserMenus($id_profile){

        $menus = array (

        			array("id" => 1, "parent_id" => 0, "title" => "Home", "icon" => "home", "component" => "HomePage", "show"  => "1", "show_in_dash"  => "1"),

        			//array("id" => 2, "parent_id" => 0, "title" => "Estimation", "icon" => "calculator", "component" => "", "show"  => "1"),

        			array("id" => 3, "parent_id" => 0, "title" => "Create Estimation", "icon" => "calculator", "component" => "EstiPage", "page" => "create", "show"  => "1", "show_in_dash"  => "1"),

        			array("id" => 4, "parent_id" => 0, "title" => "Print Estimation", "icon" => "print", "component" => "EstiPage", "page" => "print", "show"  => "1", "show_in_dash"  => "1"),

        			array("id" => 5, "parent_id" => 0, "title" => "Create Customer", "icon" => "person-add", "component" => "CusRegisterPage", "show"  => "1", "show_in_dash"  => "1"),

        			array("id" => 6, "parent_id" => 0, "title" => "Category", "icon" => "basket", "component" => "CategoryPage", "show"  => "0", "show_in_dash"  => "0"), //Disabled for ams

        			array("id" => 7, "parent_id" => 0, "title" => "E-Catalog", "icon" => "flower", "component" => "Ecatalog", "show"  => "0", "show_in_dash"  => "0"), //Disabled for ams

        			array("id" => 8, "parent_id" => 0, "title" => "Wishlist", "icon" => "heart", "component" => "WishlistPage", "show"  => "0", "show_in_dash"  => "0"), //Disabled for ams

        			array("id" => 9, "parent_id" => 0, "title" => "Cart", "icon" => "cart", "component" => "CartPage", "show"  => 0, "show_in_dash"  => "0"), //Disabled for ams,

	                array("id" => 12, "parent_id" => 0, "title" => "Estimation List", "icon" => "list", "component" => "EstimationlistPage", "show"  => 1, "show_in_dash"  => "0"),

	                array("id" => 10, "parent_id" => 0, "title" => "Stock Entry", "icon" => "paper", "component" => "StockCodeEntryPage", "show"  => 0, "show_in_dash"  => "0"),

					array("id" => 13, "parent_id" => 0, "title" => "Design", "icon" => "paper", "component" => "DesignlistPage", "show"  => 1, "show_in_dash"  => "0"),

					array("id" => 14, "parent_id" => 0, "title" => "SubDesign", "icon" => "list-box", "component" => "SubdesignPage", "show"  => 1, "show_in_dash"  => "0"),

					array("id" => 15, "parent_id" => 0, "title" => "New Arrivals", "icon" => "ios-paper", "component" => "CollectionPage", "show"  => 1, "show_in_dash"  => "0"),

					array("id" => 16, "parent_id" => 0, "title" => "Enquiry", "icon" => "ios-create", "component" => "FaqPage", "show"  => 1, "show_in_dash"  => "1"),

					array("id" => 17, "parent_id" => 0, "title" => "Order Creation", "icon" => "basket", "component" => "OrdercreationPage", "show"  => 1, "show_in_dash"  => "0"),

					array("id" => 18, "parent_id" => 0, "title" => "Order Status", "icon" => "basket", "component" => "OrderstatusPage", "show"  => 1, "show_in_dash"  => "0"),

					array("id" => 11, "parent_id" => 0, "title" => "Edit Tag", "icon" => "create", "component" => "EdittagPage", "show"  => 1, "show_in_dash"  => "0"),

					array("id" => 12, "parent_id" => 0, "title" => "Home", "icon" => "home", "component" => "HomePage", "show"  => 0, "show_in_dash"  => "1"),

					array("id" => 13, "parent_id" => 0, "title" => "Approval", "icon" => "create", "component" => "Approval", "show"  => 0, "show_in_dash"  => "1"),

					array("id" => 14, "parent_id" => 0, "title" => "Reports", "icon" => "document-text", "component" => "Reports", "show"  => 0, "show_in_dash"  => "1"),

					array("id" => 15, "parent_id" => 0, "title" => "Sign Out", "icon" => "log-out", "component" => "Signout", "show"  => 0, "show_in_dash"  => "1"),





        		);



	    /*if($id_profile <= 2){

        	$menus[] = array("id" => 11, "parent_id" => 0, "title" => "Edit Tag", "icon" => "create", "component" => "EdittagPage", "show"  => 1, "show_in_dash"  => "0");



	    }*/



        $menus[] = array("id" => 12, "parent_id" => 0, "title" => "Sign Out", "icon" => "log-out", "component" => "logout", "show"  => "1", "show_in_dash"  => "0");

        	//	echo "<pre>";print_r($menus);exit;

		return $menus;

	}





	function getVillageData($id_village)

	{

		$sql="select * from village ".($id_village!='' ?"where id_village=".$id_village."" :'')."";

		if($id_village!='')

		{

			return $this->db->query($sql)->row_array();

		}

		else

		{

			return $this->db->query($sql)->result_array();

		}

	}



	function get_country()

	{

		$this->db->select('*');

	    return $this->db->get('country')->result_array();

	}



	function get_state($id_country)

	{

		$this->db->select('*');

		$this->db->where('id_country',$id_country);

		return $this->db->get('state')->result_array();

	}



	function get_city($id_state)

	{

		$this->db->select('*');

		$this->db->where('id_state',$id_state);

		return $this->db->get('city')->result_array();

	}



	function getAllTaxGroupItems(){

		$taxGroupData = $this->db->query("SELECT tax_id, tax_code,

						tax_percentage,tgi_calculation, tgi_type,tgi_tgrpcode

						FROM ret_taxgroupitems as grpitems

						LEFT JOIN ret_taxmaster as tax ON tax.tax_id = grpitems.tgi_taxcode");

		return $taxGroupData->result_array();

	}



	function getCategories($type,$last_id, $imgOnly = false)

	{

		$sql  = $this->db->query("SELECT c.id_ret_category ,c.name,c.cat_code,m.metal,c.image

		FROM ret_category as c

		left join metal m on m.id_metal=c.id_metal".($type == 'active' ? ' where c.status = 1'.($last_id > 0? ' and c.id_ret_category >'.$last_id : '' ) : ($last_id > 0? ' where c.id_ret_category >'.$last_id : '' ) )." ".($last_id > 0 ? 'ORDER BY c.image DESC, c.id_ret_category ASC LIMIT 10' : '' ));

		$result = $sql->result_array();

		foreach($result as $key => $val){

			$file = self::RETCAT_IMG_PATH.'/'.$val['image'];

			$image = ($val['image'] != "" ? (file_exists($file)? base_url().$file : "" ):"");

			$result[$key]['image'] = $image;

			if($imgOnly ? $image == "" : false) {

				unset($result[$key]);

		}

		}

		$result = array_values($result);

	    return $result;

	}



	function getProducts($type,$id_category,$last_id, $imgOnly = false)

	{

		$sql  = "SELECT p.pro_id,p.cat_id,c.name as category,p.product_name, p.product_name as label,p.product_short_code,p.product_status,p.image,

		        p.tgrp_id as tax_group_id,tax.tax_percentage,tax.tgi_calculation,p.calculation_based_on, p.tax_type, c.scheme_closure_benefit

		        FROM ret_product_master p

			left join ret_category c on c.id_ret_category=p.cat_id

			LEFT JOIN metal as mt ON mt.id_metal = p.metal_type

            LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,

			m.tax_percentage as tax_percentage,

			i.tgi_calculation as tgi_calculation

			FROM ret_taxgroupitems i

			LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=p.tgrp_id

			";

		if($type == 'active'){

			$sql = $sql. " where product_status = 1";

			if($id_category != '' ){

				 $sql = $sql. ' and cat_id = '.$id_category;

			}

			if($last_id > 0){

				 $sql = $sql. ' and pro_id > '.$last_id;

			}

		}else{

				 $sql = $sql. " ".($id_category != '' ? ' Where cat_id = '.$id_category.' '.($last_id > 0? ' and pro_id >'.$last_id : '' ) : ($last_id > 0? ' where pro_id >'.$last_id : '' ) );

		}

		$sql = $sql." ".($last_id >= 0 ? ' ORDER BY pro_id ASC LIMIT 10' : '' );

		$result = $this->db->query($sql)->result_array();

		//echo $this->db->last_query();exit;

		foreach($result as $key => $val){

			//$file = self::RETPRO_IMG_PATH.'/'.$val['image'];

			$result[$key]['image'] = $val['image'];

			if($imgOnly ? $val['image'] == "" : false) {

				unset($result[$key]);

			}

		}

	    return $result;

	}



	function getSubDesigns($type,$id_product,$design_no, $last_id)

	{

		$sql  = "SELECT sdes.id_sub_design,sub_design_code,sub_design_name, sub_design_name as label,p.id_sub_design_mapping,pro.product_name,

		IFNULL((SELECT image_name from ret_sub_design_mapping_images as img where is_default=1 and img.id_sub_design_mapping=p.id_sub_design_mapping),'') as default_image

        FROM ret_sub_design_mapping p

        LEFT JOIN ret_product_master pro ON pro.pro_id=p.id_product

        LEFT JOIN ret_design_master des ON des.design_no=p.id_design

		LEFT JOIN ret_sub_design_master as sdes ON sdes.id_sub_design = p.id_sub_design WHERE 1";

		if($type == 'active'){

			$sql = $sql. " AND sdes.status = 1";

		}

		if($id_product != '' ){

				$sql = $sql. ' AND p.id_product = '.$id_product;

		}

		if($design_no != '' ){

				$sql = $sql. ' AND p.id_design = '.$design_no;

		}

		if($last_id > 0){

				$sql = $sql. ' AND sdes.id_sub_design > '.$last_id;

		}

		$sql = $sql." ".($last_id >= 0 ? ' ORDER BY sdes.id_sub_design ASC LIMIT 10' : '' );

		//echo $sql;exit;
		$result = $this->db->query($sql)->result_array();

		foreach($result as $key => $val){

			$result[$key]['default_image'] = ($val['default_image']!='' ? base_url().'assets/img/sub_design/'.$val['id_sub_design_mapping'].'/'.$val['default_image']  :base_url().'assets/img/no_image.png');

			$file = self::RETSUBDES_IMG_PATH.'/'.$val['image'];

			$result[$key]['image'] = ($val['image'] != null ? (file_exists($file)? $file : null ):null);

			$result[$key]['img_details']  = $this->ret_sub_design_mapping_images($val['id_sub_design_mapping']);

		}

	    return $result;

	}

    function getDesigns($type,$id_product,$last_id, $imgOnly = false)

	{

		$sql  = "SELECT design_no AS id_design ,design_code,design_name, design_name as label,i.image,pro.product_name, mapping_id AS id_design_mapping,

				IFNULL((SELECT image_name from ret_design_mapping_images as img where is_default=1 and img.id_design_mapping=p.mapping_id),'') as default_image

            FROM ret_product_mapping p

            LEFT JOIN ret_product_master pro ON pro.pro_id=p.pro_id

            LEFT JOIN ret_design_master des ON des.design_no=p.id_design

            /*left join ret_nontag_item nt on nt.design=des.design_no */

    		left join ret_design_images i on i.id_design = des.design_no and is_default = 1";

		if($type == 'active'){

			$sql = $sql. " where des.design_status = 1";

			if($id_product != '' ){

				 $sql = $sql. ' and p.pro_id = '.$id_product;

			}

			if($last_id > 0){

				 $sql = $sql. ' and des.design_no > '.$last_id;

			}

		}else{

				 $sql = $sql. " ".($id_product != '' ? ' Where p.pro_id = '.$id_product.' '.($last_id > 0? ' and des.design_no >'.$last_id : '' ) : ($last_id > 0? ' where des.design_no >'.$last_id : '' ) );

		}

		$sql = $sql." ".($last_id >= 0 ? ' ORDER BY des.design_no ASC LIMIT 10' : '' );

		$result = $this->db->query($sql)->result_array();

		foreach($result as $key => $val){

			$file = 'assets/img/design/'.$val['id_design_mapping'].'/'.$val['default_image'];

			$image = file_exists($file)? base_url().$file : "";

			$result[$key]['image'] = $image;

			$result[$key]['default_image'] = ($val['default_image']!='' ? $image  : base_url().'assets/img/no_image.png');

			$result[$key]['image'] = ($val['default_image']!='' ? $image  : base_url().'assets/img/no_image.png');

			$result[$key]['img_details']  = $this->ret_design_mapping_images($val['id_design_mapping']);


			if($imgOnly ? $image == "" : false) {

				unset($result[$key]);

			}

		}

	    return $result;

	}

	function getTaggingBySearch($SearchTxt,$searchField,$branch)
    {
		$setting_array = ["fast_moving_gold", "slow_moving_gold", "non_moving_gold", "fast_moving_silver", "slow_moving_silver", "non_moving_silver"];

		$settings = $this->get_ret_settings($setting_array);

		$FNSdata = $this->getFNSdata($settings);

		$data = $this->db->query("SELECT tag.tag_id,

		tag_code, old_tag_id, tag_datetime, tag.tag_type, tag_lot_id,

		design_id, cost_center, tag.purity, IFNULL(CONCAT(rsz.value, ' ', rsz.name),'') as size, uom, piece, IFNULL(tag.less_wt,0) as less_wt, tag.net_wt, tag.gross_wt,

		tag.calculation_based_on, retail_max_wastage_percent,tag_mc_value as mc_value,tag_mc_type as mc_type,

		halmarking, tag.sell_rate, tag.sales_value, tag.tag_status, product_name, product_short_code, tag.product_id,

		pur.purity as purname,lot_inw.lot_received_at,

		ifnull(tag_stn_detail.stn_amount,if(tag.id_orderdetails!='',ord.stn_amt,0)) as stone_price,IFNULL(tag_stn_detail.certification_cost,0) as certification_cost,

		tag.tag_id,pro.sales_mode,tag.item_rate,tax.tax_percentage,tax.tgi_calculation,tag.current_branch,

		pro.tgrp_id as tax_group_id,IFNULL(tag.id_orderdetails,'') as id_orderdetails,

		IFNULL(lot_inw.order_no,'') as order_no,des.design_name,tag.tag_mark,r.rate_field,r.market_rate_field, rtc.charge_value,sub.sub_design_name,tag.id_sub_design, c.id_ret_category, c.name as category_name, mt.metal as metal_name, r.rate_field as metal_type, r.market_rate_field as market_metal_type, '0' as is_partial, tm.image, des.design_no, tag.id_sub_design as sub_design_no, pro.pro_id, pro.metal_type as id_metal, pro.tax_type, lot_inw.lot_no, c.scheme_closure_benefit, IFNULL(tag.tag_purchase_cost,0) as tag_purchase_cost,IFNULL(sec.section_name,'') as section_name,IFNULL(tag.hu_id,'') as huid,tag.id_section

		FROM ret_taging as tag

		Left join ret_lot_inwards_detail lot_det ON tag.id_lot_inward_detail = lot_det.id_lot_inward_detail

		LEFT JOIN ret_lot_inwards as lot_inw ON lot_inw.lot_no = lot_det.lot_no

		LEFT JOIN ret_product_master as pro ON pro.pro_id = tag.product_id

		LEFT JOIN ret_design_master des on des.design_no=tag.design_id

		left join ret_sub_design_master sub  on sub.id_sub_design=tag.id_sub_design

		LEFT JOIN ret_section sec ON sec.id_section = tag.id_section

		LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity

		left join ret_category c on c.id_ret_category=pro.cat_id

		left join metal mt on mt.id_metal=c.id_metal

		LEFT JOIN ret_size as rsz ON rsz.id_size = tag.size

		left join customerorderdetails ord on ord.id_orderdetails=tag.id_orderdetails

		LEFT JOIN ret_metal_purity_rate r on r.id_metal=c.id_metal and r.id_purity=tag.purity

		LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,

		GROUP_CONCAT(m.tax_percentage) as tax_percentage,

		GROUP_CONCAT(i.tgi_calculation) as tgi_calculation

		FROM ret_taxgroupitems i

		LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=pro.tgrp_id

		LEFT JOIN (SELECT tag_id, sum(amount) as stn_amount, sum(certification_cost) as certification_cost

		FROM `ret_taging_stone`

		GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = tag.tag_id

		LEFT JOIN (SELECT tag_id, SUM(IFNULL(charge_value,0)) AS charge_value FROM ret_taging_charges GROUP BY tag_id) AS rtc ON rtc.tag_id = tag.tag_id

		LEFT JOIN ret_taging_images tm ON tm.tag_id = tag.tag_id AND tm.is_default=1

		WHERE tag.tag_status=0 and tag.id_orderdetails is NULL AND tag.current_branch=".$branch." AND tag.".$searchField." = '".$SearchTxt."'");


        //print_r($this->db->last_query());exit;

        $returndata = $data->row_array();

		$tax_details 	= $this->get_tax_details();

		/*echo "<pre>";
		print_r($returndata);
		exit;*/

		if(isset($returndata['tag_id'])) {

			if(($returndata['calculation_based_on'] == 3 || $returndata['calculation_based_on'] == 4)) {

				if($returndata['calculation_based_on'] == 4) {

					$returndata['sell_rate'] = $returndata['sell_rate'] * $returndata['gross_wt'];

				}

				$returndata['sales_value'] = $returndata['sell_rate'];

			}

        	$returndata['stone_details'] = $this->getTagStoneDetails($returndata['tag_id']);

			$returndata['charges'] = $this->get_charges($returndata['tag_id']);

			$other_metal_record = $this->get_tag_other_metal_data($returndata['tag_id']);

			$other_metals = $this->get_other_metal_details($other_metal_record);

			$returndata['other_metal_details'] = $other_metals['otherMetals'];

			$returndata['other_metal_total_wt'] = $other_metals['other_metal_total_wt'];

			$returndata['other_metal_total_price'] = $other_metals['other_metal_total_price'];

			$returndata['incentive'] = 0;

			$returndata['colorcode'] = "";

			if($returndata['tag_status'] == 0) {

			    $incent = $this->get_colorcode_and_incentives($FNSdata, $returndata['tag_datetime'], $returndata['id_metal']);

				$returndata['incentive'] = $incent['incentive'];

				$returndata['colorcode'] = $incent['colorcode'];

			}

			$imgName = $returndata['image'];

			$file = self::TAGIMG_PATH.$imgName;

			$returndata['file'] = $file;

			$returndata['image'] = base_url().''.$file;//(file_exists($file)? base_url().''.$file : null );

			$returndata['default_image'] = ($imgName !='' ? $returndata['image']  :base_url().'assets/img/no_image.png');

			$returndata['img_details'] = $this->getTagImages($returndata['tag_id']);

		}
        //return $data->result_array();

        return $returndata;

    }

	function getTagImages($tag_id = 0) {

		$returnData=array();

		$where = "";

		if($tag_id > 0) {

			$where = " AND tag_id =".$tag_id;

		}

	    $sql=$this->db->query("SELECT * FROM `ret_taging_images` WHERE 1 ".$where);

	    $result=$sql->result_array();

	    foreach($result as $items) {

	        $returnData[]=array(

	                          'id_desmap_img'  =>$items['id_tag_img'],

							  'id_tag_img'     =>$items['id_tag_img'],

	                          'tag_id'   	   =>$items['tag_id'],

	                          'image_name'     =>($items['image']!='' ? base_url().'assets/img/tag/'.$items['image']  :base_url().'assets/img/no_image.png'),

	                          'is_default'     =>$items['is_default'],

	                         );

	    }

	    return $returnData;

	}

    function getTagStoneDetails($tagid)
    {
         $data = $this->db->query("SELECT ts.tag_id, ts.stone_id, ts.pieces, ts.wt, ts.uom_id, rate_per_gram, amount, is_apply_in_lwt , is_apply_in_lwt as lwt, st.stone_type , stone_cal_type

        FROM `ret_taging_stone` as ts

        LEFT JOIN ret_stone as st ON st.stone_id = ts.stone_id

        where tag_id = $tagid");

		 //echo $this->db->last_query();exit;

        return $data->result_array();
    }

	function get_charges($tag_id)
	{
		$sql = $this->db->query("SELECT rtc.tag_charge_id, rtc.tag_id, rtc.charge_id, rtc.charge_value, c.code_charge, c.tag_display FROM ret_taging_charges AS rtc LEFT JOIN ret_charges AS c ON rtc.charge_id = c.id_charge  WHERE tag_id=".$tag_id);

	    return $sql->result_array();
	}

	function get_estimation_charges($est_id)
	{
		$sql = $this->db->query("SELECT rtc.id_est_charge, rtc.est_item_id, rtc.id_charge, rtc.amount, c.code_charge, c.tag_display FROM ret_estimation_other_charges AS rtc LEFT JOIN ret_charges AS c ON rtc.id_charge = c.id_charge  WHERE est_item_id=".$est_id);

	    return $sql->result_array();
	}

    function getNonTagItems($id_branch) // Non tag stock products

    {

        $result = array( "products" => [], "designs" => [], "sub_designs" => []);

        $products = $this->db->query("SELECT

	        id_nontag_item, nt.branch, nt.product, nt.design, nt.no_of_piece AS AVpiece, nt.gross_wt AS AVgross_wt, nt.net_wt, nt.less_wt,

	        pro_id, concat(product_name,'-',product_short_code) as product_name,

	        concat(design_name,'-',design_code) as design_name,

	        d.wastage_type, other_materials, has_stone,sales_mode,

	        has_hook, has_screw, has_fixed_price,

	        has_size, less_stone_wt,

	        if(pro.metal_type = 1,'Gold','Silver') as metal_name,

	        calculation_based_on,

	        mt.id_metal,pro.tgrp_id as tax_group_id, c.scheme_closure_benefit, nt.id_section, IFNULL(sec.section_name,'') as section_name, nt.id_sub_design, sd.sub_design_name

	        FROM ret_nontag_item as nt

			LEFT JOIN ret_section sec ON sec.id_section = nt.id_section

			LEFT JOIN ret_product_master pro ON nt.product=pro.pro_id

			LEFT JOIN ret_design_master d ON d.design_no=nt.design

			LEFT JOIN ret_sub_design_master sd  ON sd.id_sub_design=nt.id_sub_design

			LEFT JOIN ret_category c ON c.id_ret_category=pro.cat_id

			LEFT JOIN metal mt ON mt.id_metal=c.id_metal

			WHERE pro_id IS NOT NULL

			".($id_branch != '' && $id_branch > 0 ? " AND nt.branch = '".$id_branch."'" : ''));

        $data = $products->result_array();

        foreach($data as $d){

			array_push($result['products'],array( "pro_id" => $d['pro_id'], "product_name" => $d['product_name']));

			array_push($result['designs'],array( "design_no" => $d['design'], "design_name" => $d['design_name'], "pro_id" => $d['pro_id']));

			if($d['id_sub_design'] > 0 && $d['id_sub_design'] != NULL) {

				array_push($result['sub_designs'],array( "id_sub_design" => $d['id_sub_design'], "sub_design_name" => $d['sub_design_name'], "design_no" => $d['design'], "pro_id" => $d['pro_id']));

			}

		}

		$result['products'] = array_values(array_unique($result['products'], SORT_REGULAR));

		$result['designs'] = array_values(array_unique($result['designs'], SORT_REGULAR));

		$result['sub_designs'] = array_values(array_unique($result['sub_designs'], SORT_REGULAR));

		$result['sections'] = $this->get_sections($id_branch);

		$result['nt_items'] = $data;

        return $result;

    }



    function getHomeStock($type,$SearchTxt,$searchField,$branch)

    {

		$data=$this->db->query("SELECT tag.tag_id,

        tag.tag_code, tag_datetime,prod.tgrp_id as tax_group_id,

        (tag.gross_wt-IFNULL(py.sold_gross_wt,0)) as gross_wt, '' as less_wt, (tag.net_wt-IFNULL(py.sold_net_wt,0)) as net_wt,

        prod.product_name,prod.product_short_code,pur.purity as purname,prod.metal_type as metaltype,

        prod.calculation_based_on,tag.product_id,tag.design_id,tag.purity,

        tag.size,tag.piece,tag.tag_mc_type,tag.tag_mc_value,tag.retail_max_wastage_percent,

        tag.item_rate,tag.current_branch,des.design_name,tag.design_id,



        r.rate_field,r.market_rate_field,sub.sub_design_name, tag.id_sub_design, prod.pro_id,

		if(prod.metal_type = 1,'Gold','Silver') as metal_name,

        r.rate_field as metal_type,

        r.market_rate_field as market_metal_type, c.scheme_closure_benefit

		from ret_taging tag



        LEFT JOIN (SELECT IFNULL(SUM(p.sold_gross_wt),0) as sold_gross_wt,IFNULL(SUM(p.sold_net_wt),0) as sold_net_wt,d.tag_id

        FROM ret_partlysold p

        LEFT JOIN ret_bill_details d ON d.bill_det_id = p.sold_bill_det_id

        LEFT JOIN ret_billing b ON b.bill_id = d.bill_id

        WHERE b.bill_status = 1

        GROUP BY p.tag_id) as py ON py.tag_id = tag.tag_id

		LEFT JOIN ret_product_master prod on prod.pro_id = tag.product_id

		LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity

	    left join ret_category c on c.id_ret_category=prod.cat_id

	    LEFT JOIN ret_design_master as des ON des.design_no = tag.design_id



	    left join ret_sub_design_master sub  on sub.id_sub_design=tag.id_sub_design

	    left join metal mt on mt.id_metal=c.id_metal



	    LEFT JOIN ret_metal_purity_rate r on r.id_metal=c.id_metal and r.id_purity=tag.purity

		WHERE tag.current_branch=".$branch." ".($type == 'all' ? "" : " AND tag.".$searchField." LIKE '%".$SearchTxt."%'")."

		and tag.is_partial = 1 and (tag.tag_status = 6 OR tag.tag_status = 1) Having gross_wt > 0");


    	/*$data = $this->db->query("SELECT p.tag_id,

        tag.tag_code, tag_datetime,prod.tgrp_id as tax_group_id,

        p.blc_gross_wt as gross_wt,p.blc_less_wt as less_wt,p.blc_net_wt as net_wt,

        concat(product_name,'-',product_short_code) as product_name,pur.purity as purname,prod.pro_id,

        prod.calculation_based_on,tag.design_id,tag.purity,

        tag.size,tag.piece,tag.tag_mc_type as mc_type,tag.tag_mc_value as mc_value,tag.retail_max_wastage_percent,

        tag.item_rate,concat(des.design_name,'-',des.design_code) as design_name,

        if(prod.metal_type = 1,'Gold','Silver') as metal_name,

        if(prod.metal_type = 1,'goldrate_22ct','silverrate_1gm') as metal_type,

        if(prod.metal_type = 1,'mjdmagoldrate_22ct','mjdmasilverrate_1gm') as market_metal_type, c.scheme_closure_benefit

		from ret_partlysold p

		LEFT JOIN ret_taging tag on p.tag_id=tag.tag_id

		LEFT JOIN ret_product_master prod on prod.pro_id = p.product

		LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity

	    left join ret_category c on c.id_ret_category=prod.cat_id

	    LEFT JOIN ret_design_master as des ON des.design_no = tag.design_id

	    left join metal mt on mt.id_metal=c.id_metal

		WHERE tag.current_branch=".$branch." ".($type == 'all' ? "" : " AND tag.".$searchField." LIKE '%".$SearchTxt."%'")."

		and p.status=1"); */

		if($type == 'all'){

			return $data->result_array();

		}else{

			return $data->row_array();

		}



    }



    function getEstiID($esti_no,$esti_date,$id_branch)

   	{

        $sql = $this->db->query("SELECT estimation_id from ret_estimation where id_branch=".$id_branch." and esti_no=".$esti_no." and date(estimation_datetime)='".$esti_date."'");

        if( $sql->num_rows() > 0 ){

			return $sql->row()->estimation_id;

		}else{

			return 0;

		}

    }

    function getBranchDayClosingData($id_branch)

   	{

        $sql = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing where id_branch=".$id_branch);

        return $sql->row_array();

    }

    function getOldMetalType()

	{

		$sql  = "SELECT id_metal,id_metal_type,metal_type FROM ret_old_metal_type";

		return  $this->db->query($sql)->result_array();

	}



    function generateEstiNo($date,$id_branch)

	{

	  $sql = "SELECT max(esti_no) as lasEstiNo FROM ret_estimation WHERE date(estimation_datetime)='".$date."' ".($id_branch!='' ? " and id_branch=".$id_branch."" :'')." ORDER BY estimation_id DESC";

	  $lastno =  $this->db->query($sql)->row()->lasEstiNo;

	  if($lastno != NULL && $lastno != '')

		{

		  	$number = (int) $lastno;

		  	$number++;

			$code_number=str_pad($number, 4, '0', STR_PAD_LEFT);

    		return $code_number;

		}

		else

		{

			$code_number=str_pad('1', 4, '0', STR_PAD_LEFT);;

			return $code_number;

		}

	}



    function getStones($type, $stone_type = 0){

		$data = $this->db->query("SELECT stone_id, stone_name,

		stone_code, stone_type, st.uom_id, is_certificate_req, is_4c_req,

		uom_name, uom_short_code, IF(stone_type = 1 , 'Precious', (IF(stone_type = 2 , 'Semi-Precious', (IF(stone_type = 3 , 'Normal', ''))))) AS stone_type_name

		FROM ret_stone as st

		LEFT JOIN ret_uom as uom ON uom.uom_id = st.uom_id

		WHERE 1

		".($type == 1 ? ' AND stone_status = 1' : '')

		.($stone_type > 0 ? ' AND stone_type = '.$stone_type : '')

		);

		return $data->result_array();

	}



	function prof_wise_loginotp_exp()

	{

		 $sql="Select prof_wise_loginotp_exp from chit_settings where id_chit_settings = 1";

		 return $this->db->query($sql)->row()->prof_wise_loginotp_exp;

	}



	function get_dash_esti_sale($from_date,$to_date, $id_emp, $id_branch)

	{

		$result = array('created' => 0 , 'created_amt' => 0 , 'created_wgt' => 0, 'sold' => 0 , 'sold_amt' => 0 , 'sold_wgt' => 0 );

		$sql_created = $this->db->query("SELECT count(estimation_id) AS created, IFNULL(SUM(estitm.amt),0) AS created_amt, IFNULL(SUM(estitm.gwt),0) AS created_gross_wgt, IFNULL(sum(estitm.nwt),0) AS created_wgt

										FROM ret_estimation AS est

										LEFT JOIN ( SELECT esti_id, IFNULL(SUM(item_cost),0) AS amt, IFNULL(SUM(gross_wt),0) AS gwt, IFNULL(SUM(net_wt),0) AS nwt FROM ret_estimation_items GROUP BY esti_id) as estitm ON estitm.esti_id = est.estimation_id

										LEFT JOIN (select id_profile,id_employee from employee) as e ON e.id_employee = est.created_by

										WHERE estitm.esti_id IS NOT NULL AND ( date(est.estimation_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($id_emp > 0 ? ' AND est.created_by = '.$id_emp : '').' '.($id_branch > 0 ? ' AND id_branch = '.$id_branch : ''));

		if($sql_created->num_rows() > 0){

			$result['created'] = $sql_created->row()->created;

			$result['created_amt'] = $sql_created->row()->created_amt;

			$result['created_gross_wgt'] = $sql_created->row()->created_gross_wgt;

			$result['created_wgt'] = $sql_created->row()->created_wgt;

		}



		$sql_sold = $this->db->query("SELECT

											COUNT(DISTINCT est.estimation_id)  AS sold,

											SUM(billdet.item_cost) AS sold_amt,

											SUM(billdet.gross_wt) AS sold_gross_wt,

											SUM(billdet.net_wt) AS sold_wgt

										FROM `ret_bill_details` billdet

										LEFT JOIN ret_billing AS bill ON bill.bill_id = billdet.bill_id

										LEFT JOIN ret_estimation_items ei ON ei.est_item_id = billdet.esti_item_id

										LEFT JOIN ret_estimation est ON est.estimation_id = ei.esti_id

										LEFT JOIN (select id_profile,id_employee FROM employee) AS e ON e.id_employee = est.created_by

										WHERE bill.bill_status = 1 AND (DATE(est.estimation_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($id_emp > 0 ? ' AND est.created_by = '.$id_emp : '').' '.($id_branch > 0 ? ' AND est.id_branch = '.$id_branch : ''));

										//echo $this->db->last_query();exit;


		if($sql_sold->num_rows() > 0){

			$result['sold'] = $sql_sold->row()->sold;

			$result['sold_amt'] = $sql_sold->row()->sold_amt;

			$result['sold_gross_wt'] = $sql_sold->row()->sold_gross_wt;

			$result['sold_wgt'] = $sql_sold->row()->sold_wgt;

		}

		return $result;

	}



	function get_dash_esti_old_metal($from_date, $to_date, $id_emp, $id_branch)

	{

		$result = array('created' => 0 , 'created_amt' => 0 , 'created_wgt' => 0, 'purchased' => 0 , 'purch_amt' => 0 , 'purch_wgt' => 0 );

		$sql_created = $this->db->query("SELECT

											count(est.estimation_id) AS created,

											IFNULL(SUM(esti_om.amt),0) AS created_amt,

											IFNULL(SUM(esti_om.grossWt),0) AS created_gross_wgt,

											IFNULL(SUM(esti_om.netWt),0) AS created_wgt

										FROM ret_estimation AS est

										LEFT JOIN (SELECT est_id, IFNULL(sum(amount),0) AS amt, IFNULL(SUM(gross_wt),0) AS grossWt, IFNULL(SUM(net_wt),0) AS netWt FROM ret_estimation_old_metal_sale_details GROUP BY est_id) as esti_om  ON esti_om.est_id = est.estimation_id

										LEFT JOIN (select id_profile,id_employee from employee) as e ON e.id_employee = est.created_by

										WHERE esti_om.est_id IS NOT NULL AND ( date(est.estimation_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($id_emp > 0 ? ' AND est.created_by = '.$id_emp : '').' '.($id_branch > 0 ? ' AND id_branch = '.$id_branch : ''));

	//	echo $this->db->last_query();exit;

		if($sql_created->num_rows() > 0){

			$result['created'] = $sql_created->row()->created;

			$result['created_amt'] = $sql_created->row()->created_amt;

			$result['created_gross_wgt'] = $sql_created->row()->created_gross_wgt;

			$result['created_wgt'] = $sql_created->row()->created_wgt;

		}

		$sql_purch = $this->db->query("SELECT COUNT(DISTINCT est.estimation_id) AS purchased, SUM(billold.rate) AS purch_amt, SUM(billold.gross_wt) AS purch_gross_wgt, SUM(billold.net_wt) AS purch_wgt

										FROM `ret_bill_old_metal_sale_details` billold

										LEFT JOIN ret_billing AS bill ON bill.bill_id = billold.bill_id

										LEFT JOIN ret_estimation_old_metal_sale_details os ON os.old_metal_sale_id = billold.esti_old_metal_sale_id

										LEFT JOIN ret_estimation est ON est.estimation_id = os.est_id

										LEFT JOIN (select id_profile,id_employee FROM employee) AS e ON e.id_employee = est.created_by

										WHERE bill.bill_status = 1 AND (DATE(est.estimation_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($id_emp > 0 ? ' AND est.created_by = '.$id_emp : '').' '.($id_branch > 0 ? ' AND est.id_branch = '.$id_branch : ''));

//echo $this->db->last_query();exit;

		if($sql_purch->num_rows() > 0){

			$result['purchased'] = $sql_purch->row()->purchased;

			$result['purch_amt'] = $sql_purch->row()->purch_amt;

			$result['purch_gross_wgt'] = $sql_purch->row()->purch_gross_wgt;

			$result['purch_wgt'] = $sql_purch->row()->purch_wgt;

		}

		return $result;

	}

	function isCusExist($cusmobile)

	{

	    $sql = $this->db->query("SELECT id_customer FROM customer WHERE mobile='".$cusmobile."'");

	    //echo "SELECT id_customer FROM customer WHERE mobile='".$cusmobile."'"

		if($sql->num_rows() == 0){

			return array("status" => FALSE, "message" => "Customer already exist");

		}else{

			return array("status" => TRUE, "message" => "Given mobile number already exist");

		}

	}

	function isAddressExist($id_customer)
	{
	    $sql = $this->db->query("SELECT id_address FROM address WHERE id_customer='".$id_customer."'");
	    if($sql->num_rows() == 0){
	        return false;
	    }else{
	        return true;
	    }
	}

	function getAllPurities($id_product){

		if($id_product != ''){

			 $sql = $this->db->query("SELECT p.id_purity, purity, purity as label, mpur.rate_field, mpur.market_rate_field

						FROM ret_product_master prod

						LEFT JOIN ret_metal_cat_purity cat_pur ON cat_pur.id_category = prod.cat_id

						LEFT JOIN ret_purity p ON p.id_purity = cat_pur.id_purity

						LEFT JOIN ret_category as cat ON cat.id_ret_category = cat_pur.id_category

                        LEFT JOIN ret_metal_purity_rate as mpur ON mpur.id_metal = cat.id_metal AND mpur.id_purity = cat_pur.id_purity

						where prod.pro_id=".$id_product." group by p.id_purity");

		}else{

			 $sql = $this->db->query("SELECT pur.id_purity, pur.purity, purity as label , mpur.rate_field, mpur.market_rate_field

						FROM ret_purity as pur

						LEFT JOIN ret_metal_purity_rate as mpur ON mpur.id_purity = pur.id_purity

						WHERE status=1");

		}




		return $sql->result_array();

	}

    function getCurrentDayEstimationsByBranch($id_branch="", $id_emp, $type){

		$dcData=$this->getBranchDayClosingData($id_branch);

		//$dcData['entry_date'] = "2023-08-21";

		if($type == "") {

			$query = $this->db->query("SELECT estimation_id, esti_no, esti_for, date_format(estimation_datetime, '%d-%m-%Y %H:%i:%s') as estimatedon, cus_id, has_converted_order, total_cost, est.id_branch, cus.firstname, cus.mobile,  '' AS wt, IF((bill.bill_id IS NOT NULL AND bill.bill_id > 0) OR (old_bill.bill_id IS NOT NULL AND old_bill.bill_id > 0), 1, 0) as is_billed

										FROM ret_estimation AS est

										LEFT JOIN customer as cus ON cus.id_customer = est.cus_id

										LEFT JOIN (SELECT e.esti_id,b.bill_id,b.bill_no

											FROM ret_estimation_items e

											LEFT JOIN ret_bill_details d ON d.esti_item_id=e.est_item_id

											LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

											WHERE b.bill_status=1

											GROUP by b.bill_id) as bill ON bill.esti_id=est.estimation_id

										LEFT JOIN (SELECT s.est_id,b.bill_id,b.bill_no

											FROM ret_estimation_old_metal_sale_details s

											LEFT JOIN ret_bill_old_metal_sale_details old ON old.esti_old_metal_sale_id=s.old_metal_sale_id

											LEFT JOIN ret_billing b ON b.bill_id=old.bill_id

											WHERE b.bill_status=1

											GROUP by b.bill_id) as old_bill ON old_bill.est_id=est.estimation_id

										WHERE date(estimation_datetime) = '".$dcData['entry_date']."'

										".($id_emp > 0 ? " AND est.created_by = ".$id_emp :'')."

										".($id_branch > 0 ? " AND est.id_branch = ".$id_branch : '')." ORDER BY estimation_id DESC");

		}

		else if($type == "Created") {

			$query = $this->db->query("SELECT estimation_id, esti_no, esti_for, date_format(estimation_datetime, '%d-%m-%Y %H:%i:%s') as estimatedon, cus_id, has_converted_order, total_cost, est.id_branch, cus.firstname, cus.mobile,  estitm.wt, IF((bill.bill_id IS NOT NULL AND bill.bill_id > 0), 1, 0) as is_billed

										FROM ret_estimation AS est

										LEFT JOIN customer as cus ON cus.id_customer = est.cus_id

										LEFT JOIN (SELECT esti_id, IFNULL(sum(item_cost),0) AS amt, IFNULL(sum(gross_wt),0) as wt, IFNULL(sum(net_wt),0) AS nwt FROM ret_estimation_items GROUP BY esti_id) as estitm ON estitm.esti_id = est.estimation_id

										LEFT JOIN (SELECT e.esti_id,b.bill_id,b.bill_no

											FROM ret_estimation_items e

											LEFT JOIN ret_bill_details d ON d.esti_item_id=e.est_item_id

											LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

											WHERE b.bill_status=1

											GROUP by b.bill_id) as bill ON bill.esti_id=est.estimation_id

										WHERE estitm.esti_id IS NOT NULL AND date(estimation_datetime) = '".$dcData['entry_date']."'

										".($id_emp > 0 ? " AND est.created_by = ".$id_emp :'')."

										".($id_branch > 0 ? " AND est.id_branch = ".$id_branch : '')." ORDER BY estimation_id DESC");

		} else if($type == "Converted") {

			$query = $this->db->query("SELECT estimation_id, esti_no, esti_for, date_format(estimation_datetime, '%d-%m-%Y %H:%i:%s') as estimatedon, cus_id, has_converted_order, total_cost, est.id_branch, cus.firstname, cus.mobile, IF(IFNULL(bill.bill_status,0) = 1, 1, 0) AS is_billed, IFNULL(SUM(billdet.gross_wt),0) as wt

										FROM `ret_bill_details` billdet

										LEFT JOIN ret_billing AS bill ON bill.bill_id = billdet.bill_id

										LEFT JOIN ret_estimation_items ei ON ei.est_item_id = billdet.esti_item_id

										LEFT JOIN ret_estimation est ON est.estimation_id = ei.esti_id

										LEFT JOIN customer as cus ON cus.id_customer = est.cus_id

										LEFT JOIN (select id_profile,id_employee FROM employee) AS e ON e.id_employee = est.created_by

										WHERE bill.bill_status = 1 AND date(est.estimation_datetime) = '".$dcData['entry_date']."'

										".($id_emp > 0 ? " AND est.created_by = ".$id_emp :'')."

										".($id_branch > 0 ? " AND est.id_branch = ".$id_branch : '')." GROUP BY est.estimation_id ORDER BY est.estimation_id DESC");


		} else if($type == "OldCreated") {

			$query = $this->db->query("SELECT est.estimation_id, est.esti_no, est.esti_for, date_format(est.estimation_datetime, '%d-%m-%Y %H:%i:%s') as estimatedon, est.cus_id, est.has_converted_order, est.total_cost, est.id_branch, cus.firstname, cus.mobile, esti_om.wt, IF((old_bill.bill_id IS NOT NULL AND old_bill.bill_id > 0), 1, 0) as is_billed

										FROM ret_estimation AS est

										LEFT JOIN customer as cus ON cus.id_customer = est.cus_id

										LEFT JOIN (SELECT est_id, IFNULL(sum(amount),0) AS amt, IFNULL(SUM(gross_wt),0) AS wt, IFNULL(SUM(net_wt),0) AS nwt FROM ret_estimation_old_metal_sale_details GROUP BY est_id) as esti_om  ON esti_om.est_id = est.estimation_id

										LEFT JOIN (select id_profile,id_employee from employee) as e ON e.id_employee = est.created_by

										LEFT JOIN (SELECT s.est_id,b.bill_id,b.bill_no

											FROM ret_estimation_old_metal_sale_details s

											LEFT JOIN ret_bill_old_metal_sale_details old ON old.esti_old_metal_sale_id=s.old_metal_sale_id

											LEFT JOIN ret_billing b ON b.bill_id=old.bill_id

											WHERE b.bill_status=1

											GROUP by b.bill_id) as old_bill ON old_bill.est_id=est.estimation_id

										WHERE esti_om.est_id IS NOT NULL AND date(est.estimation_datetime) = '".$dcData['entry_date']."'

										".($id_emp > 0 ? " AND est.created_by = ".$id_emp :'')."

										".($id_branch > 0 ? " AND est.id_branch = ".$id_branch : '')." ORDER BY estimation_id DESC");

		} else if($type == "OldConverted")	{

			$query = $this->db->query("SELECT est.estimation_id, est.esti_no, est.esti_for, date_format(est.estimation_datetime, '%d-%m-%Y %H:%i:%s') as estimatedon, est.cus_id, est.has_converted_order, est.total_cost, est.id_branch, cus.firstname, cus.mobile, IF(IFNULL(bill.bill_status,0) = 1, 1, 0) AS is_billed,  IFNULL(SUM(billold.gross_wt),0) AS wt

										FROM `ret_bill_old_metal_sale_details` billold

										LEFT JOIN ret_billing AS bill ON bill.bill_id = billold.bill_id

										LEFT JOIN ret_estimation_old_metal_sale_details os ON os.old_metal_sale_id = billold.esti_old_metal_sale_id

										LEFT JOIN ret_estimation est ON est.estimation_id = os.est_id

										LEFT JOIN customer as cus ON cus.id_customer = est.cus_id

										LEFT JOIN (select id_profile,id_employee FROM employee) AS e ON e.id_employee = est.created_by

										WHERE bill.bill_status = 1 AND date(est.estimation_datetime) = '".$dcData['entry_date']."'

										".($id_emp > 0 ? " AND est.created_by = ".$id_emp :'')."

										".($id_branch > 0 ? " AND est.id_branch = ".$id_branch : '')." GROUP BY est.estimation_id ORDER BY est.estimation_id DESC");

		}

        return $query->result_array();
    }

    function get_retCharges()
    {
        $cquery = $this->db->query("SELECT id_charge, code_charge, name_charge, value_charge FROM ret_charges");
        return $cquery->result_array();
    }

    function testimportTagData(){
        $ab = 0;
        $lot_grs_wt = 0;
        $totgrswt = 0;
        $tottgrswt = 0;
	    $lotquery = $this->db->query("SELECT `K ID` as karid, catid, ct.Purity, pur.id_purity, sum(ifnull(`Gross Wt`, 0)) as grs_wt
	                               FROM skn_gold_ornaments_22_ct ct
	                               LEFT JOIN ret_purity as pur ON pur.purity = ct.Purity
	                               GROUP BY catid, Purity, `K ID`");
	    foreach($lotquery->result() as $prelot){
	        $insert_lot_data = array("lot_received_at"  => 1,
	                                "gold_smith"        => $prelot->karid,
	                                "lot_date"          => date('Y-m-d H:i:s'),
	                                "id_category"       => $prelot->catid,
	                                "id_purity"         => $prelot->id_purity,
	                                "created_branch"    => 1,
	                                "created_on"        => date('Y-m-d H:i:s'),
	                                "lot_from"          => 3
	                                );

	       $lot_grs_wt += $prelot->grs_wt;
	       $lot_id = 201;
	       if(!empty($lot_id)){
	           $lot_pro_query = $this->db->query("SELECT `K ID` as karid, `Product Name` as productname, sum(Pieces) as pcs, sum(ifnull(`Gross Wt`, 0)) as gross_wt,
	                            sum(`Net Wt`) as net_wt, pr.pro_id , ct.catid, ct.Purity
	                            FROM skn_gold_ornaments_22_ct as ct
	                            LEFT JOIN ret_product_master as pr ON pr.product_name = ct.`Product Name`
	                            WHERE `K ID` = '".$insert_lot_data['gold_smith']."' AND ct.Purity = '".$prelot->Purity."' AND ct.catid = '".$prelot->catid."'
	                            GROUP BY `Product Name`");
	           //echo $this->db->last_query();
	           //echo "<br />";
	           foreach($lot_pro_query->result() as $prelotdet){
	               $insert_lot_det_data = array(
	                                "lot_no"            => $lot_id,
	                                "lot_product"       => $prelotdet->pro_id,
	                                "design_for"        => 2,
	                                "no_of_piece"       => $prelotdet->pcs,
	                                "gross_wt"          => $prelotdet->gross_wt,
	                                "gross_wt_uom"      => 1,
	                                "net_wt"            => $prelotdet->net_wt,
	                                "net_wt_uom"        => 1,
	                                "less_wt_uom"       => 1,
	                                "current_branch"    => 1,
	                                "tag_status"        => 1
	                                );
	                                $totgrswt += $prelotdet->gross_wt;
	                $lot_det_id = 202;
	                if(!empty($lot_det_id)){
	                    $tag_query = $this->db->query("SELECT `K ID`, `Product Name` , Pieces as pcs, ifnull(`Gross Wt`,0) as gross_wt,
	                                `Net Wt` as net_wt, pr.pro_id, cat.cat_code as cat_code,
	                                pr.product_short_code as product_short_code, des.design_no as design_no,
	                                sdes.id_sub_design as id_sub_design, `Less Wt` as lesswt, `Wastage Per` as wastage,
	                                if(`MC Type` = 'Per Gram', 2, 1) as mctype, `MC Value` as mcval,
	                                br.id_branch as branchid, `MFR Code` as manufacture_code,
	                                `Style Code` as style_code, `Tag Number` as oldtagno,
	                                `Certification No` as cert_no, `Purchase Cost` as tag_purchase_cost,
	                                `Tag Date` as tagdate, `HUID 1` as hu_id, `HUID 2` as hu_id2,
	                                pr.calculation_based_on as calculation_based_on, `Sales Value` as sell_rate
	                                FROM skn_gold_ornaments_22_ct as ct
	                                LEFT JOIN ret_category as cat ON cat.id_ret_category = '".$prelotdet->catid."'
	                                LEFT JOIN ret_product_master as pr ON pr.product_name = ct.`Product Name` AND pr.cat_id = '".$prelotdet->catid."'

	                                LEFT JOIN ret_design_master as des ON des.design_name = ct.`Design Name`
	                                LEFT JOIN ret_sub_design_master as sdes ON sdes.sub_design_name = ct.`Sub Design Name`
	                                LEFT JOIN branch as br ON br.name = ct.`Branch`
	                                WHERE ct.`K ID` = '".$prelotdet->karid."' AND ct.catid = '".$prelotdet->catid."'
	                                AND ct.Purity = '".$prelotdet->Purity."'
	                                AND `Product Name` = '". $prelotdet->productname."'");
	                   foreach($tag_query->result() as $tagdet){
	                       ++$ab;
	                       $tottgrswt += $tagdet->gross_wt;

	                       if(empty($tagdet->pro_id)){
	                           echo $this->db->last_query();
	                           echo "<br />";
	                       }

	                   }
	                }
	           }

	       }
	    }
	    echo json_encode(array("status" => TRUE, "message" => "Uploaded successfully", "lot_grs_wt" => $lot_grs_wt,  "lotdetgrswt" => $totgrswt, "ab" => $ab, "taggrswt" => $tottgrswt));
	    exit;
	    //return array("status" => TRUE, "message" => "Uploaded successfully");

	}


	function importTagData(){

	    $lotquery = $this->db->query("SELECT `K ID` as karid, catid, ct.Purity, pur.id_purity, sum(ifnull(`Gross Wt`, 0)) as grs_wt, ct.Branch
	                               FROM ret_import_tag_details ct
	                               LEFT JOIN ret_purity as pur ON pur.purity = ct.Purity
	                               WHERE updatestatus = 0  
	                               GROUP BY catid, Purity, `K ID`, `Branch`");
	    foreach($lotquery->result() as $prelot){
	        $insert_lot_data = array("lot_received_at"  => 1,
	                                "gold_smith"        => $prelot->karid,
	                                "lot_date"          => date('Y-m-d H:i:s'),
	                                "id_category"       => $prelot->catid,
	                                "id_purity"         => $prelot->id_purity,
	                                "created_branch"    => 1,
	                                "created_on"        => date('Y-m-d H:i:s'),
	                                "lot_from"          => 3
	                                );
	       $lot_id = $this->insertData($insert_lot_data, "ret_lot_inwards");
	       if(!empty($lot_id)){
	           $lot_pro_query = $this->db->query("SELECT `K ID` as karid, `Product Name` as productname, sum(Pieces) as pcs, sum(ifnull(`Gross Wt`, 0)) as gross_wt,
	                            sum(`Net Wt`) as net_wt, pr.pro_id , ct.catid, ct.Purity, ct.Branch
	                            FROM ret_import_tag_details as ct
	                            LEFT JOIN ret_product_master as pr ON pr.product_name = ct.`Product Name`
	                            WHERE updatestatus = 0 AND ct.`Branch` = '".$prelot->Branch."' AND `K ID` = '".$insert_lot_data['gold_smith']."' AND ct.Purity = '".$prelot->Purity."' AND ct.catid = '".$prelot->catid."'
	                            GROUP BY `Product Name`");
	           foreach($lot_pro_query->result() as $prelotdet){
	               $insert_lot_det_data = array(
	                                "lot_no"            => $lot_id,
	                                "lot_product"       => $prelotdet->pro_id,
	                                "design_for"        => 2,
	                                "no_of_piece"       => $prelotdet->pcs,
	                                "gross_wt"          => $prelotdet->gross_wt,
	                                "gross_wt_uom"      => 1,
	                                "net_wt"            => $prelotdet->net_wt,
	                                "net_wt_uom"        => 1,
	                                "less_wt_uom"       => 1,
	                                "current_branch"    => 1,
	                                "tag_status"        => 1,
									"lot_id_category"   => $prelotdet->catid,
	                                "lot_id_purity"     => $prelot->id_purity,
	                                );
	                $lot_det_id = $this->insertData($insert_lot_det_data, "ret_lot_inwards_detail");
	                if(!empty($lot_det_id)){
	                    $tag_query = $this->db->query("SELECT ct.id as imptagid, `K ID`, `Product Name` , Pieces as pcs, ifnull(`Gross Wt`,0) as gross_wt,
	                                `Net Wt` as net_wt, pr.pro_id, cat.cat_code as cat_code,
	                                pr.product_short_code as product_short_code, des.design_no as design_no,
	                                sdes.id_sub_design as id_sub_design, `Less Wt` as lesswt, `Wastage Per` as wastage,
	                                `Wastage Wt` as wastagewt,
	                                if(`MC Type` = 'PCS', 1, 2) as mctype, `MC Value` as mcval,
	                                br.id_branch as branchid, `MFR Code` as manufacture_code,
	                                `Style Code` as style_code, `Tag Number` as oldtagno,
	                                `Certification No` as cert_no, `Purchase Cost` as tag_purchase_cost,
	                                `Tag Date` as tagdate, `HUID 1` as hu_id, `HUID 2` as hu_id2,
	                                if(`Sales Value` > 0, 3, 1) as calculation_based_on, `Sales Value` as sell_rate,
									calltype, ct.size as size, sec.id_section as id_section, ifnull(rsz.id_size, NULL) as idsize, `IMAGE FILE` as tagimg, RATEPERGRAM,
									`TRANSIT NO` as transitno
	                                FROM ret_import_tag_details as ct
	                                LEFT JOIN ret_category as cat ON cat.id_ret_category = '".$prelotdet->catid."'
	                                LEFT JOIN ret_product_master as pr ON pr.product_name = ct.`Product Name` AND pr.cat_id = '".$prelotdet->catid."'
	                                LEFT JOIN ret_size as rsz ON rsz.id_product = pr.pro_id AND  rsz.value = ct.size
	                                LEFT JOIN ret_design_master as des ON des.design_name = ct.`Design Name`
	                                LEFT JOIN ret_sub_design_master as sdes ON sdes.sub_design_name = ct.`Sub Design Name`
	                                LEFT JOIN branch as br ON br.short_name = ct.`Branch`
	                                LEFT JOIN ret_section as sec ON sec.section_name = ct.Counter
	                                WHERE updatestatus = 0 AND ct.`Branch` = '".$prelotdet->Branch."' AND ct.`K ID` = '".$prelotdet->karid."' AND ct.catid = '".$prelotdet->catid."'
	                                AND ct.Purity = '".$prelotdet->Purity."'
	                                AND `Product Name` = '". $prelotdet->productname."'");
	                   foreach($tag_query->result() as $tagdet){

	                        $catcode    = $tagdet->cat_code;

							$curr_year  = date("y");

							if(empty($tagdet->pro_id)){
							    print_r($tagdet);exit;
							}

	 					    $tagCode    = $this->getlastTagCode($tagdet->pro_id);

	 					    $tag_no     = $this->generateTagCode($tagCode);

							$tag_code   = $tagdet->product_short_code.'-'.$tag_no; //$catcode.$curr_year.

							/*if($tag_code == 'SS22EAR-00001'){
							    echo $this->db->last_query();
							    echo "<br />";
							    echo "SELECT ct.id as imptagid, `K ID`, `Product Name` , Pieces as pcs, ifnull(`Gross Wt`,0) as gross_wt,
	                                `Net Wt` as net_wt, pr.pro_id, cat.cat_code as cat_code,
	                                pr.product_short_code as product_short_code, des.design_no as design_no,
	                                sdes.id_sub_design as id_sub_design, `Less Wt` as lesswt, `Wastage Per` as wastage,
	                                if(`MC Type` = 'Per Gram', 2, 1) as mctype, `MC Value` as mcval,
	                                br.id_branch as branchid, `MFR Code` as manufacture_code,
	                                `Style Code` as style_code, `Tag Number` as oldtagno,
	                                `Certification No` as cert_no, `Purchase Cost` as tag_purchase_cost,
	                                `Tag Date` as tagdate, `HUID 1` as hu_id, `HUID 2` as hu_id2,
	                                pr.calculation_based_on as calculation_based_on, `Sales Value` as sell_rate
	                                FROM ret_import_tag_details as ct
	                                LEFT JOIN ret_category as cat ON cat.id_ret_category = '".$prelotdet->catid."'
	                                LEFT JOIN ret_product_master as pr ON pr.product_name = ct.`Product Name` AND pr.cat_id = '".$prelotdet->catid."'
	                                LEFT JOIN ret_design_master as des ON des.design_name = ct.`Design Name`
	                                LEFT JOIN ret_sub_design_master as sdes ON sdes.sub_design_name = ct.`Sub Design Name`
	                                LEFT JOIN branch as br ON br.name = ct.`Branch`
	                                WHERE updatestatus = 0  AND ct.`K ID` = '".$prelotdet->karid."' AND ct.catid = '".$prelotdet->catid."'
	                                AND ct.Purity = '".$prelotdet->Purity."'
	                                AND `Product Name` = '". $prelotdet->productname."'";

							}*/

							$tag_datetime = date("Y-m-d H:i:s");

							$arrayTag = array(
								'tag_code' 			=> $tag_code,
								'current_branch' 	=> $tagdet->branchid,
								'id_branch' 		=> 1,
								'cost_center' 		=> $tagdet->branchid,
								'tag_lot_id' 		=> $lot_id,
								'product_id' 		=> $tagdet->pro_id,
								'design_id' 		=> $tagdet->design_no,
								'id_sub_design' 	=> $tagdet->id_sub_design,
								'design_for' 		=> 0,//$addData['design_for'][$key],
								'purity' 			=> $prelot->id_purity,
								'id_orderdetails'   => NULL,
								'id_lot_inward_detail' => $lot_det_id,
								'size' 				=> $tagdet->idsize,
								'piece' 			=> $tagdet->pcs,
								'gross_wt' 			=> $tagdet->gross_wt,

								'less_wt' 			=> !empty($tagdet->lesswt) ? $tagdet->lesswt : NULL,
								'net_wt' 			=> $tagdet->net_wt,
								'calculation_based_on' => $tagdet->calculation_based_on,
								'retail_max_wastage_percent' => !empty($tagdet->wastage) ? $tagdet->wastage : 0,
								'tag_mc_type' 		=> $tagdet->mctype,
								'tag_mc_value' 	    => !empty($tagdet->mcval) ? $tagdet->mcval : 0,
								'sell_rate' 		=> $tagdet->calculation_based_on == 4 ? $tagdet->RATEPERGRAM : $tagdet->sell_rate,
								'item_rate' 		=> $tagdet->calculation_based_on == 4 ? $tagdet->RATEPERGRAM : $tagdet->sell_rate,
								'sales_value' 		=> $tagdet->calculation_based_on == 4 ? $tagdet->RATEPERGRAM : $tagdet->sell_rate,
								'hu_id'             => $tagdet->hu_id,
								'hu_id2'            => $tagdet->hu_id2,
								'tag_datetime'	  	=> $tag_datetime,
								'old_tag_id'        => $tagdet->oldtagno,
								'old_tag_date'      => date('Y-m-d', strtotime($tagdet->tagdate)),
								'created_time'	  	=> date("Y-m-d H:i:s"),
								'created_by'      	=> 1,
								'ref_no'      		=> NULL,
								"cert_no"			=> $tagdet->cert_no,
								"cert_img"			=> NULL,
								'manufacture_code' 	=> $tagdet->manufacture_code,
								'style_code' 		=> $tagdet->style_code,
								'remarks' 			=> $tagdet->oldtagno,
								'tag_purchase_cost'	=> $tagdet->tag_purchase_cost,
								'tag_type' 			=> 0,
								'is_tag_imported'   => 1,
								'uom'               => 1,
								'id_section'        => $tagdet->id_section,
								'current_counter'   => $tagdet->id_section,
								'stone_calculation_based_on' => $tagdet->calltype == 'WEIGHT' ? 1 : 2,
							);
							$tag_id = $this->insertData($arrayTag, 'ret_taging');

							if(!empty($tagdet->tagimg)){
							    $this->insertData(array('tag_id' => $tag_id, 'image' => $tagdet->tagimg, 'is_default' => 1, 'date_add' => date("Y-m-d H:i:s")), 'ret_taging_images');
							}

							$log_data = array(
	 					    	'tag_id'	  =>$tag_id,
	 					    	'date'		  =>$tag_datetime,
	 					    	'status'	  =>0,
	 					    	'from_branch' =>NULL,
	 					    	'to_branch'	  => $tagdet->branchid,
	 					    	'created_on'  =>date("Y-m-d H:i:s"),
								'created_by'  =>1,
								'issuspensestock' => 0
	 					    );
	 					    $this->insertData($log_data, 'ret_taging_status_log');

	 					    if(!empty($tagdet->id_section)){
    	 					     $seclog_data = array(
    	 					    	'tag_id'	  =>$tag_id,
    	 					    	'date'		  =>$tag_datetime,
    	 					    	'status'	  =>0,
    	 					    	'from_branch' =>NULL,
    	 					    	'to_branch'	  => $tagdet->branchid,
    	 					    	'from_section'  => NULL,
    	 					    	'to_section'    => $tagdet->id_section,
    	 					    	'created_on'  =>date("Y-m-d H:i:s"),
    								'created_by'  =>1,
    								'issuspensestock' => 0
    	 					    );
    	 					    $this->insertData($seclog_data, 'ret_section_tag_status_log');
	 					    }


	 					    $this->updateData(array("updatestatus" => 1), 'id', $tagdet->imptagid, 'ret_import_tag_details');

	                   }
	                }
	           }

	       }
	    }

	    return array("status" => TRUE, "message" => "Uploaded successfully");


	}

	function importCustomerLedgerBalance(){
	    $customer_query = $this->db->query("SELECT `Customer`, `cusId`, `Mobile`, `ClosingAmt`, `OldRef`, `Remarks` GROUP BY `Mobile`");
	    foreach($customer_query->result() as $customer){

	    }
	}
    function importTagCharges()
	{
	    $charge_query = $this->db->query("SELECT chid, tag_id, tag_code, old_tag_id, ch.id_charge as charge_id, chr.`Charge Value` as charge_value
	                                    FROM `ret_import_charges` as chr
	                                    LEFT JOIN ret_taging as tag ON tag.old_tag_id = chr.`Tag Number`
	                                    LEFT JOIN ret_charges as ch ON ch.name_charge = chr.`Charge Name`
	                                    WHERE chstatus = 0");
	   foreach($charge_query->result() as $charge){
	       $charge_data=array(
							'tag_id'                =>$charge->tag_id,
							'charge_id'             =>$charge->charge_id,
							'charge_value'          =>$charge->charge_value,
							);
			$this->insertData($charge_data,'ret_taging_charges');
			$this->updateData(array("chstatus" => 1), 'chid', $charge->chid, 'ret_import_charges');
	   }
	   return array("status" => TRUE, "message" => "Uploaded successfully");
	}

	function importTagStones()
	{
	    $stone_query = $this->db->query("SELECT stiid, tag_id, tag_code, old_tag_id, st.stone_id, ist.`Pieces` as pieces,
	                                    `Weight` as wt, uom.uom_id, ist.`Amount` as amount, `Rate/Gram` as ratepergrm,
	                                    if(LOWER(ist.`Less Wt`) = 'yes', 1 , 0) as is_apply_in_lwt,
	                                    if(LOWER(ist.`CAL TYPE`) = 'weight', 1, 2)  as stone_cal_type
	                                    FROM `ret_import_stone_details` as ist
	                                    LEFT JOIN ret_taging as tag ON tag.old_tag_id = ist.`Tag Number`
	                                    LEFT JOIN ret_stone as st ON st.stone_id = ist.`Stone ID`
	                                    LEFT JOIN ret_uom as uom ON LOWER(uom.uom_name) = LOWER(ist.`Unit`)
	                                    WHERE ststatus = 0");
	    foreach($stone_query->result() as $stone){
	        $stone_data = array(
							'tag_id'                =>$stone->tag_id,
							'pieces'                =>$stone->pieces,
							'wt'                    =>$stone->wt,
							'stone_id'              =>$stone->stone_id,
							'uom_id'                =>$stone->uom_id,
							'amount'                =>$stone->amount,
							'is_apply_in_lwt'       =>$stone->is_apply_in_lwt,
							'rate_per_gram'         => $stone->ratepergrm,
							'stone_cal_type'        => $stone->stone_cal_type
							);
			$stoneInsert = $this->insertData($stone_data,'ret_taging_stone');
			$this->updateData(array("ststatus" => 1), 'stiid', $stone->stiid, 'ret_import_stone_details');
	   }
	   return array("status" => TRUE, "message" => "Uploaded successfully");
	}
	function getlastTagCode($product_id)

    {

        $sql=$this->db->query("SELECT t.tag_id,t.tag_code FROM ret_taging t where t.tag_code is not null AND product_id = ".$product_id." ORDER by tag_id DESC LIMIT 1");

		return $sql->row()->tag_code;

	}



	//Import customer balance

	function get_retWallet_details($id_customer)
	{
		$data=$this->db->query("SELECT id_ret_wallet,id_customer FROM ret_wallet w where w.id_customer=".$id_customer."");
		if($data->num_rows()>0)
		{
			return array('status'=>TRUE,'id_ret_wallet'=>$data->row('id_ret_wallet'));
		}else{
			return array('status'=>FALSE,'id_ret_wallet'=>'');
		}
	}

		//Update Wallet Account
	function updateWalletData($data,$arith)
	{
		$sql = "UPDATE ret_wallet SET amount=(amount".$arith." ".$data['amount']."),weight=(weight".$arith." ".$data['weight']."),updated_time='".date("Y-m-d H:i:s")."' WHERE id_customer=".$data['id_customer'];
		$status = $this->db->query($sql);
		return $status;
	}

	function bill_no_generate($id_branch)
	{
		$lastno = $this->get_max_bill_no($id_branch);
		if($lastno!=NULL)
		{
			$number = (int) $lastno;
			$number++;
			$code_number = str_pad($number, 5, '0', STR_PAD_LEFT);
			return $code_number;
		}
		else
		{
			$code_number = str_pad('1', 5, '0', STR_PAD_LEFT);
			return $code_number;
		}
	}
	function get_max_bill_no($id_branch)
    {
        $fin_year = $this->get_FinancialYear();
		$sql = "SELECT max(bill_no) as lastBill_no FROM ret_issue_receipt where fin_year_code=".$fin_year['fin_year_code']." ".($id_branch!='' && $id_branch>0 ? " and id_branch=".$id_branch."" :'')." ORDER BY id_issue_receipt DESC";
		return $this->db->query($sql)->row()->lastBill_no;
	}

	function get_FinancialYear()
	{
		$sql=$this->db->query("SELECT fin_year_code From ret_financial_year where fin_status=1");
		return $sql->row_array();
	}

	//Update Wallet Account

	public function __encrypt($str)
	{
		return base64_encode($str);
	}

	function getCustomerDetails($mobile,$firstname)
	{
	    $customer_id='';
	    $sql=$this->db->query("SELECT * FROM customer WHERE mobile='".$mobile."'");
	    if($sql->num_rows() == 1)
	    {
	        $customer_id=$sql->row()->id_customer;
	    }else
	    {
	        $insertData=array(
	                         'mobile'=>$mobile,
	                         'username'=>$mobile,
	                         'firstname'=>$firstname,
	                         'passwd'=>$this->__encrypt($data['mobile']),
	                         );
	        $customer_id=$this->insertData($insertData,'customer');
	    }

	    return $customer_id;
	}

	function importCustomerBalance(){
	   $responseData=array();
	   $total_import_Data=0;
	   $custrans = $this->db->query("SELECT rowid,Customer,Mobile, ClosingAmt, OldRef, Remarks, branchid, cusId FROM customeroutstanding t1
	                                WHERE LENGTH(t1.Mobile) = 10  AND cusupdatestatus = 0");
	   //print_r($this->db->last_query());exit;
	   if($custrans->num_rows() > 0){
	       $this->load->model("ret_estimation_model");
	       $this->load->model('admin_settings_model');
		   $model = "ret_estimation_model";
	       foreach($custrans->result() as $row)
	       {

	       $cusId=$this->getCustomerDetails($row->Mobile,$row->Customer);

	   if($cusId!='')
    	{

	       //if (str_contains($row->Remarks,'ADVANCE')) { //Update this into Payment Receipt

	        $bill_no    = $this->bill_no_generate($row->branchid);
    	    $dCData     = $this->admin_settings_model->getBranchDayClosingData($row->branchid);
    		$bill_date  = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);
    		$fin_year   = $this->get_FinancialYear();

	        if (strpos($row->Remarks, 'ADVANCE') !== false) {//Update this into Payment Receipt



	            $insData=array(
					'type'			=>2,
					'amount'		=>$row->ClosingAmt,
					'weight'		=>0,
					'bill_no'		=>$bill_no,
					'receipt_type'	=>4,
					'id_branch'		=> $row->branchid,
					'id_customer'	=>$cusId,
					//'refno'	        =>$row->OldRef,
					'receipt_as'	=>1,
					'store_receipt_as'=>1,
					'narration'	 	=>'Existing Balance Amount',
					'created_on' 	=> date("Y-m-d H:i:s"),
					'bill_date' 	=> $bill_date,
					'fin_year_code' => $fin_year['fin_year_code'],
				);
				//echo "<pre>"; print_r($insData);exit;
				$this->db->trans_begin();
				$insId = $this->insertData($insData,'ret_issue_receipt');
				//print_r($this->db->last_query());exit;
				if($insId)
	 			{
	 			    $total_import_Data=($total_import_Data+1);

	 			    $updStatus=$this->updateData(array('cusupdatestatus'=>1),"rowid","$row->rowid","customeroutstanding");

                    $wallet=$this->get_retWallet_details($cusId);
                    if($wallet['status'])
                    {
                        $this->updateWalletData(array('amount'=>$row->ClosingAmt,'weight'=>0,'id_customer'=>$cusId),'+');
                        $insWallet=array(
                        'id_ret_wallet'		=>$wallet['id_ret_wallet'],
                        'id_issue_receipt'	=>$insId,
                        'amount'			=>$row->ClosingAmt,
                        'weight'			=>0,
                        'transaction_type'	=>0,
                        'created_on' 		=> date("Y-m-d H:i:s"),
                        'remarks'	 		=>'Existing Balance Advace Amount'
                        );
                        $this->insertData($insWallet,'ret_wallet_transcation');
                    }
                    else
                    {
                        $wallet_acc=array(
                            'id_customer'   =>$cusId,
                            'amount'        =>$row->ClosingAmt,
                            'weight'        =>0,
                            'created_time'  =>date("Y-m-d H:i:s")
                        );
                        $insWalletAcc= $this->insertData($wallet_acc,'ret_wallet');
                        if($insWalletAcc)
                        {
                            $insWallet=array(
                            'id_ret_wallet'	    =>$insWalletAcc,
                            'id_issue_receipt'	=>$insId,
                            'amount'			=>$row->ClosingAmt,
                            'weight'			=>0,
                            'transaction_type'	=>0,
                            'created_on' 		=> date("Y-m-d H:i:s"),
                            'remarks'	 		=>'Existing Balance Advace Amount'
                            );
                            $this->insertData($insWallet,'ret_wallet_transcation');
                        }
                    }
	 			}
	        }
	        else if (strpos($row->Remarks, 'CREDIT SALES') !== false) { //Update this into Payment Issue
	            $insData=array(
				    'bill_date' 	=> $bill_date,
					'fin_year_code' => $fin_year['fin_year_code'],
				    'bill_no'	    => $bill_no,
					'type'          => 1,
					'id_branch'     => $row->branchid,
					'amount'        => $row->ClosingAmt,
					'issue_to'      => 2,
					'issue_type'    => 4,
					'id_customer'   => $cusId,
					//'refno'	        =>$row->OldRef,
					'created_on'    => date("Y-m-d H:i:s"),
					);

				$this->db->trans_begin();
				$insId = $this->insertData($insData,'ret_issue_receipt');

				if($insId)
	 			{
	 			    $total_import_Data=($total_import_Data+1);

	 			    $updStatus=$this->updateData(array('cusupdatestatus'=>1),"rowid","$row->rowid","customeroutstanding");

                    $wallet=$this->get_retWallet_details($cusId);
                    if($wallet['status'])
                    {
                        $this->updateWalletData(array('amount'=>$row->ClosingAmt,'weight'=>0,'id_customer'=>$cusId),'-');
                        $insWallet=array(
                        'id_ret_wallet'		=>$wallet['id_ret_wallet'],
                        'id_issue_receipt'	=>$insId,
                        'amount'			=>$row->ClosingAmt,
                        'weight'			=>0,
                        'transaction_type'	=>0,
                        'created_on' 		=> date("Y-m-d H:i:s"),
                        'remarks'	 		=>'Existing Balance Credit Sales Amount'
                        );
                        $this->insertData($insWallet,'ret_wallet_transcation');
                    }
                    else
                    {
                        $wallet_acc=array(
                            'id_customer'   =>$cusId,
                            'amount'        =>$row->ClosingAmt,
                            'weight'        =>0,
                            'created_time'  =>date("Y-m-d H:i:s")
                        );
                        $insWalletAcc= $this->insertData($wallet_acc,'ret_wallet');
                        if($insWalletAcc)
                        {
                            $insWallet=array(
                            'id_ret_wallet'	    =>$insWalletAcc,
                            'id_issue_receipt'	=>$insId,
                            'amount'			=>$row->ClosingAmt,
                            'weight'			=>0,
                            'transaction_type'	=>0,
                            'created_on' 		=> date("Y-m-d H:i:s"),
                            'remarks'	 		=>'Existing Balance Credit Sales Amount'
                            );
                            $this->insertData($insWallet,'ret_wallet_transcation');
                        }
                    }
	 			}

	        }
	       }
	   }
	       if($this->db->trans_status()==TRUE)
	       {
	           $this->db->trans_commit();
	           $responseData=array('status'=>'TRUE','message'=>$total_import_Data.' '.' Records Imported Successfully..');
	       }
	       else
	       {
	           $this->db->trans_rollback();
	           $responseData=array('status'=>'FLASE','message'=>'Unable to Import','last_query'=>$this->db->last_query());
	       }
	   }
	   else
	   {
	       $responseData=array('status'=>'FLASE','message'=>'No Records Found..');
	   }

	   return $responseData;
	}

	//Generate Tag Code
	function generateTagCode($lastTagCode){
	    $tagCode        = $lastTagCode; // Saple : 1-A12345 or 1-12345
        $code_det       = explode('-',$tagCode);
        $alpha_char		='';
        // Split Alphabet and Serial number
        if(preg_match('/[A-Z]+\K/',$code_det[1]))
        {
        	$str_split = preg_split('/[A-Z]+\K/',$code_det[1]);
        	$tag_number=$str_split[1];
        	$alpha_char=$str_split[0];
        }else{
        	$tag_number=$code_det[1];
        }
        //  Increment Number
        if($tag_number!=NULL && $tag_number!='' && $tag_number!=99999)
        {
        	$number = (int) $tag_number;
        	$number++;
        	$code_number=str_pad($number, 5, '0', STR_PAD_LEFT);
        }
        else
        {
        	$code_number=str_pad('1', 5, '0', STR_PAD_LEFT);
        }
        //  Increment Alphabet if reached 99999
        if($tag_number==99999)
        {
        	if($alpha_char == '')
        	{
        		$alpha_char='A';
        	}else{
        		$alpha_char=++$alpha_char;
        	}

        }
        return $alpha_char.''.$code_number;
	
	}

	//Generate Tag Code


	//customer import
    function importCustomer()
	{
	   $responseData=array();
	   $total_import_Data=0;
	   $sql=$this->db->query("SELECT * FROM `customer_import` WHERE import_status=0 AND LENGTH(mobile)=10");

	   $importData=$sql->result_array();

	   if(sizeof($importData)>0)
	   {
	       foreach($importData as $cus)
	       {
	           if($cus['mobile']!='')
	           {
	               $cusExists=$this->db->query("SELECT * FROM `customer` where mobile='".$cus['mobile']."'");
	               //print_r($cusExists->num_rows());exit;
	               if($cusExists->num_rows()==0)
	               {
	                    $insData=array(
                        'firstname'    =>($cus['firstname']!='' ? $cus['firstname']:NULL),
                        'mobile'       =>$cus['mobile'],
                        'pan'          =>($cus['pan']!='' ? $cus['pan']:NULL),
                        'gst_number'   =>($cus['pan']!='' ? $cus['pan']:NULL),
                        'username'      =>$cus['mobile'],
	                    'passwd'       =>$this->__encrypt($cus['mobile']),
                        'date_add'     =>date("Y-m-d H:i:s"),
                        );
                        $this->db->trans_begin();
                        $cusId=$this->insertData($insData,'customer');
                        if($cusId)
                        {

                            $this->updateData(array("import_status" => 1), 'id_cus_import',$cus['id_cus_import'], 'customer_import');

                            if($cus['address1']!='' || $cus['address2']!='' || $cus['city']!='' || $cus['pincode']!='' )
                            {
                                $id_city='';
                                if($cus['city']!='')
                                {
                                    $city_details=$this->get_city_details($cus['city']);
                                    if($city_details['id_city']!='')
                                    {
                                        $id_city    =$city_details['id_city'];
                                    }

                                }

                                $addressData=array(
                                              'address1'    =>($cus['address1']!='' ? $cus['address1']:NULL),
                                              'address2'    =>($cus['address2']!='' ? $cus['address2']:NULL),
                                              'address3'    =>($cus['address3']!='' ? $cus['address3']:NULL),
                                              'pincode'     =>($cus['pincode']!='' ? $cus['pincode']:NULL),
                                              'id_customer' =>$cusId,
                                              'id_state'    =>35,
                                              'id_country'  =>101,
                                              'id_city'     =>($id_city!='' ? $id_city :NULL),
                                              );
                                $this->insertData($addressData,'address');
                            }
                            $total_import_Data+=$total_import_Data+1;
                        }
	               }
	      /*         else   //    CUSTOMER ALREADY EXISTS
	               {
	                   $cusADDExists=$this->db->query("SELECT * FROM `address` where id_customer='".$cusExists->row()->id_customer."'"); // CHECK ADDRESS EXISTS


                        if($cus['address1']!='' || $cus['address2']!='' || $cus['city']!='' || $cus['pincode']!='' )
                        {
                            $id_city='';
                            if($cus['city']!='')
                            {
                                $city_details=$this->get_city_details($cus['city']);
                                if($city_details['id_city']!='')
                                {
                                    $id_city    =$city_details['id_city'];
                                }

                            }

                            $addressData=array(
                                          'address1'    =>($cus['address1']!='' ? $cus['address1']:NULL),
                                          'address2'    =>($cus['address2']!='' ? $cus['address2']:NULL),
                                          'address3'    =>($cus['address3']!='' ? $cus['address3']:NULL),
                                          'pincode'     =>($cus['pincode']!='' ? $cus['pincode']:NULL),
                                          'id_customer' =>$cusExists->row()->id_customer,
                                          'id_state'    =>35,
                                          'id_country'  =>101,
                                          'id_city'     =>($id_city!='' ? $id_city :NULL),
                                          );

                            if($cusADDExists->num_rows()==0)
                            {
                                $this->insertData($addressData,'address');
                                //print_r($this->db->last_query());exit;
                            }
                            else
                            {
                                $this->updateData($addressData, 'id_address',$cusADDExists->row()->id_address, 'address');
                            }
                        }

	               }*/
	           }
	       }

	       if($this->db->trans_status()==TRUE)
	       {
	           $this->db->trans_commit();
	           $responseData=array('status'=>'TRUE','message'=>$total_import_Data.' '.' Records Imported Successfully..');
	       }
	       else
	       {
	           $this->db->trans_rollback();
	           $responseData=array('status'=>'FLASE','message'=>'Unable to Import','last_query'=>$this->db->last_query());
	       }

	   }
	   else
	   {
	       $responseData=array('status'=>'FLASE','message'=>'No Records Found..');
	   }

	   return $responseData;
	}


	function get_city_details($city_name)
	{
	    $sql=$this->db->query("SELECT * FROM `city` WHERE name like '%".$city_name."%'");
	    //print_r($this->db->last_query());exit;
	    return $sql->row_array();
	}
	//customer import

	function getSubDesignDetail($data)
	{
		//$subproduct_required = $this->get_ret_settings('subproduct_required');
            $sql  = "SELECT m.id_sub_design_mapping,'' as id_wishlist,mt.metal,c.name as category,des.design_no,des.design_name,p.pro_id,p.product_name,subDes.id_sub_design,subDes.sub_design_name,
            p.has_stone,p.has_hook,p.has_screw,p.has_size,
            IF(design_for = 1, 'Male',IF(design_for = 2, 'Female',IF(design_for = 3, 'Unisex','Kids'))) as design_for,
            IF(usage_type = 1, 'Regular',IF(design_for = 2, 'Function','')) as usage_type,
            min_length, max_length, min_width, max_width, min_dia, max_dia, min_weight, max_weight,'' as pcs,'' as due_date,'' as id_purity,'' as weight,
            (SELECT image_name from ret_sub_design_mapping_images as img where is_default=1 and img.id_sub_design_mapping=m.id_sub_design_mapping) as default_image
            FROM ret_sub_design_mapping m
            LEFT JOIN ret_product_master p ON p.pro_id = m.id_product
            LEFT JOIN ret_design_master des ON des.design_no = m.id_design
            LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design = m.id_sub_design
            LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
            LEFT JOIN metal mt ON mt.id_metal = c.id_metal
            where p.pro_id IS NOT NULL
            ".($data['id_product']!='' ? " and m.id_product=".$data['id_product']."" :'')."
            ".($data['design_no']!='' ? " and m.id_design=".$data['design_no']."" :'')."
            ".($data['id_sub_design']!='' ? " and m.id_sub_design=".$data['id_sub_design']."" :'')."
            ";

		$resultArr = $this->db->query($sql)->row_array();
		$default_image = self::RETSUBDES_IMG_PATH.'/'.$resultArr['id_sub_design_mapping'].'/'.$resultArr['default_image'];
		$resultArr['defaultImage'] = ($resultArr['default_image']!=null ? file_exists($default_image) ? base_url().$default_image : null :null);
		$resultArr['images'] = $this->get_all_sub_design_mapping_images($resultArr['id_sub_design_mapping']);


		// IMAGES
		/*$img_sql  = $this->db->query("SELECT image,is_default FROM ret_design_images WHERE id_design= ".$design_no." ORDER BY is_default DESC");
		$img_res = $img_sql->result_array();
		foreach($img_res as $img){
			$file = self::RETDES_IMG_PATH.'/'.$resultArr['design_no'].'/'.$img['image'];
			$img_url = ($img['image'] != null ? (file_exists($file)? base_url().''.$file : null ):null);
			if($img != NULL){
				$resultArr['images'][] = $img_url;
				if($img['is_default'] == 1){
					$resultArr['defaultImage'] = $img_url;
				}
			}
		}
		$img_sql->free_result();*/

		// PURITIES
		$pur_sql  = $this->db->query("SELECT p.id_purity,p.purity
        FROM ret_metal_cat_purity c
        LEFT JOIN ret_purity p ON p.id_purity = c.id_purity
        WHERE p.status = 1 AND c.id_category = ".$data['id_category']."");
		$resultArr['purities'] = $pur_sql->result_array();
		$pur_sql->free_result();

		// SIZES
		$resultArr['sizes'] = array();

		$size_sql  = $this->db->query("SELECT * FROM ret_size WHERE id_product = ".$data['id_product']."");
		$resultArr['sizes'] = $size_sql->result_array();
		$size_sql->free_result();

        $weight_sql = $this->db->query("SELECT w.id_weight,w.weight_description,w.from_weight,w.to_weight
        FROM ret_weight w
        where w.id_product=".$data['id_product']." and w.id_design=".$data['design_no']."");
        $resultArr['weights'] = $weight_sql->result_array();
		$weight_sql->free_result();

	/*	// STONES
		$resultArr['stones'] = array();
		if($resultArr['has_stone'] == 1){
			$stone_sql  = $this->db->query("SELECT
											st.stone_id,stone_name,stone_pcs
										 FROM ret_design_stone dst
										 LEFT JOIN ret_stone st on st.stone_id = dst.stone_id
										 WHERE design_id= ".$design_no);
			$resultArr['stones'] = $stone_sql->result_array();
			$stone_sql->free_result();
		}*/

		// HOOKS
		$resultArr['hooks'] = array();
		if($resultArr['has_hook'] == 1){
			$stone_sql  = $this->db->query("SELECT
												hook_name,hook_id
										 	FROM ret_hook_type hk
										 	WHERE hook_status = 1");
			$resultArr['hooks'] = $stone_sql->result_array();
			$stone_sql->free_result();
		}

		// SCREWS
		$resultArr['screws'] = array();
		if($resultArr['has_screw'] == 1){
			$stone_sql  = $this->db->query("SELECT
												screw_name,screw_id
										 	FROM ret_screw_type sc
										 	WHERE screw_status = 1");
			$resultArr['screws'] = $stone_sql->result_array();
			$stone_sql->free_result();
		}


		return $resultArr;
	}

	function get_all_sub_design_mapping_images($id_sub_design_mapping)
	{
	    $returnData=array();
	    $sql=$this->db->query("SELECT * FROM `ret_sub_design_mapping_images` WHERE id_sub_design_mapping=".$id_sub_design_mapping."");
	    $result=$sql->result_array();
	    foreach($result as $items)
	    {
	        $returnData[]=($items['image_name']!='' ? base_url().'assets/img/sub_design/'.$items['id_sub_design_mapping'].'/'.$items['image_name']  :base_url().'assets/img/no_image.png');
	    }
	    return $returnData;
	}

	function getFilters()
    {
        $resultArr = array();

        $metal = $this->db->query('SELECT metal AS name, "-1" AS count, m.id_metal AS metal_code FROM metal m');
        $resultArr['Metal'] = $metal->result_array();
        $metal->free_result();

        $resultArr['Product'] = $this->get_product();

		$design = $this->db->query('SELECT m.mapping_id, m.pro_id as id_product, m.id_design, p.product_name, des.design_name,
										CONCAT(des.design_name, "-", LEFT(p.product_name , 3)) as name, "-1" as count,
										des.design_code, p.product_short_code AS productcode, p.metal_type AS metalcode
										FROM ret_product_mapping m
										LEFT JOIN ret_product_master p ON p.pro_id=m.pro_id
										LEFT JOIN ret_design_master des ON des.design_no=m.id_design LIMIT 200');

        $resultArr['Design'] = $design->result_array();

		//$resultArr['DesignCount'] = COUNT($design->result_array());

        $design->free_result();

		$subdesign = $this->db->query('SELECT m.id_sub_design_mapping, m.id_product, m.id_design, m.id_sub_design, p.product_name, des.design_name, sdes.sub_design_name,
										CONCAT(sdes.sub_design_name, "-", LEFT(des.design_name , 3)) as name, "-1" as count,
										des.design_code, p.product_short_code AS productcode, sdes.sub_design_code, p.metal_type AS metalcode
										FROM ret_sub_design_mapping m
										LEFT JOIN ret_product_master p ON p.pro_id=m.id_product
										LEFT JOIN ret_design_master des ON des.design_no=m.id_design
										LEFT JOIN ret_sub_design_master sdes ON sdes.id_sub_design=m.id_sub_design LIMIT 200');

        $resultArr['Subdesign'] = $subdesign->result_array();

		//$resultArr['SubdesignCount'] = COUNT($design->result_array());

        $subdesign->free_result();

        /*$stones = $this->db->query('SELECT stone_name as name, "-1" as count, stone_id FROM `ret_stone` WHERE stone_status = 1');
        $resultArr['Stone Type'] = $stones->result_array();
        $stones->free_result();*/

        $resultArr['Stock Status'] = array(
                                        array(
                                            "name" => "Open",
                                            "count"=> "-1"
                                        ),
                                        array(
                                            "name" => "Close",
                                            "count"=> "-1"
                                        )
                                    );

        $branches = $this->db->query('SELECT name, short_name as branchcode, "-1" as count, id_branch FROM `branch` d WHERE active = 1');
        $resultArr['Branch'] = $branches->result_array();
        $branches->free_result();

        $size = $this->db->query('SELECT CONCAT(value, " ", LOWER(name)) as name, "-1" as count, id_size FROM `ret_size` ORDER BY value');
        $resultArr['Size'] = $size->result_array();
        $size->free_result();

        $resultArr['From Weight(g)'] = 0;
        $resultArr['To Weight(g)'] = 0;
        /*$resultArr['From Price'] = 0;
        $resultArr['To Price'] = 0;*/

        return $resultArr;
    }

	function get_product($pro_id = 0) {

		$where = "";

		if($pro_id > 0) {

			$where = $where.' AND p.pro_id = '.$pro_id;

		}

		$product = $this->db->query('SELECT product_name AS name, "-1" AS count, p.product_short_code AS productcode, p.pro_id as id_product, p.metal_type AS metalcode FROM ret_product_master p WHERE 1 '.$where);

		return $product->result_array();

	}

    function applyFilter($data,$type)
    {
		$date_time = date('Y-m-d H:i:s');
		$searchType = 0;
		$tagStr = "";
		if(isset($data['TagNo'])) {
			$tagStr = trim($data['TagNo']);
			if($tagStr != "") {
				$isTagNo = substr_count($tagStr, '-');
				$searchType = $isTagNo > 0 ? 1 : 2;
			}
		} else if(isset($data['is_newArrival']) && $data['is_newArrival'] == 1) {
			$searchType = 3;
		}

		$page_no = isset($data['page_no']) && $data['page_no'] > 0 ? $data['page_no'] - 1 : 0;
        $resultArr = [];

		$setting_array = ["fast_moving_gold", "slow_moving_gold", "non_moving_gold", "fast_moving_silver", "slow_moving_silver", "non_moving_silver"];

		$settings = $this->get_ret_settings($setting_array);

		$FNSdata = $this->getFNSdata($settings);

		$rates 	= $this->get_rates();

		$ratesjson = json_encode($rates);

		$tax_details 	= $this->get_tax_details();

		$tax_details_json = json_encode($tax_details);

		//print_r($setting);exit;

		$this->db->_protect_identifiers = false;
        $this->db->select("
			t.tag_id as id_tag_detail,
			t.tag_datetime,
			br.id_branch as BranchCode,
			br.short_name as BranchName,
			p.metal_type as MetalCode,
			p.metal_type as market_metal_type,
			m.metal as MetalName,
			rc.name as CatName,
			p.cat_id as CatCode,
			t.product_id,
			p.product_short_code AS ProductCode,
			p.product_name as ProductName,
			t.design_id,
			des.design_name,
			t.id_sub_design,
			sdes.sub_design_name,
			t.tag_code as TagNo,
			t.old_tag_id,
			t.piece as Pieces,
			t.gross_wt as GrossWt,
			t.gross_wt as weight,
			t.less_wt,
			t.piece as pcs,
			t.net_wt as NetWt,
			t.purity as purity_id,
			rp.purity as Purity,
			'' as JewelGroupName,
			'' as ItemCollection,
			IF(tag_status = 0 , 'OPEN' , (IF(tag_status = 1 , 'CLOSE' , ''))) as Status,
			t.tag_status,
			'' as Nature,
			tm.image as TagImage,
			CONCAT(rz.value, ' ', LOWER(rz.name)) as SizeName,
			rz.id_size,
			t.calculation_based_on,
			t.net_wt,
			t.retail_max_wastage_percent,
			t.tag_mc_value as mc_value,
			t.tag_mc_type as mc_type,
			IF(t.tag_mc_type=1, t.tag_mc_value, t.tag_mc_value * t.gross_wt) as total_mc,
			t.sales_value,
			IF(IFNULL(t.id_orderdetails, '') != '', 1 ,0) as is_reserved,
			p.tgrp_id,
			'' as due_date,
			min_weight,
			max_weight,
			UNIX_TIMESTAMP(tag_datetime) as timestamp,
			r.rate_field,
			r.market_rate_field,
			t.hu_id,
			t.hu_id2,
			t.id_section,
			IFNULL(sec.section_name,'') as section_name,
			1 as display_va,
			1 as display_mc"
		);

        $this->db->from('ret_taging t');
		$this->db->join('branch br', 'br.id_branch = t.current_branch', 'left');
		$this->db->join('ret_purity rp', 'rp.id_purity = t.purity', 'left');
		$this->db->join('ret_size rz', 'rz.id_size = t.size', 'left');
        $this->db->join('ret_product_master p', 'p.pro_id = t.product_id', 'left');
		$this->db->join('ret_design_master des', 'des.design_no = t.design_id', 'left');
		$this->db->join('ret_sub_design_master sdes', 'sdes.id_sub_design = t.id_sub_design', 'left');
		$this->db->join('ret_section sec', 'sec.id_section = t.id_section', 'left');
		$this->db->join('ret_category rc', 'rc.id_ret_category = p.cat_id', 'left');
		$this->db->join('metal m', 'm.id_metal = p.metal_type', 'left');
		$this->db->join('ret_taging_images tm', 'tm.tag_id = t.tag_id AND tm.is_default=1', 'left');
		$this->db->join('ret_metal_purity_rate r', 'r.id_metal = rc.id_metal AND r.id_purity=t.purity', 'left');
		$this->db->join('(SELECT tag_id, SUM(amount) AS stone_amt FROM ret_taging_stone GROUP BY tag_id) AS st_tot', 'st_tot.tag_id = t.tag_id','left');

		$this->db->where('TRIM(tm.image) !=', '');
		$this->db->where('tm.image IS NOT NULL');

		if($searchType == 1) {

			$this->db->where("(t.tag_code='".$tagStr."' OR t.old_tag_id='".$tagStr."')", NULL, FALSE);

		} else if($searchType == 2) {

			$this->db->like('p.product_name', $tagStr);

		} else if($searchType == 3) {

			$this->db->where('t.is_new_arrival', 1);

		} else {

			if(sizeof($data['Branch']) > 0 ){
				$branch = [];
				foreach($data['Branch'] as $br){
					array_push($branch,$br->id_branch);
				}
				$this->db->where_in('t.current_branch', $branch);
			}
			if(sizeof($data['Product']) > 0 ){
				$product = [];
				foreach($data['Product'] as $pr){
					array_push($product,$pr->id_product);
				}
				$this->db->where_in('t.product_id', $product);
			}
			if(sizeof($data['Metal']) > 0 ){
				$metal = [];
				foreach($data['Metal'] as $m){
					array_push($metal,$m->metal_code);
				}
				$this->db->where_in('p.metal_type', $metal);
			}
			if(sizeof($data['Design']) > 0 ){
				$Design = [];
				foreach($data['Design'] as $des){
					array_push($Design,$des->id_design);
				}
				$this->db->where_in('t.design_id', $Design);
			}
			if(sizeof($data['Subdesign']) > 0 ){
				$Subdesign = [];
				foreach($data['Subdesign'] as $sdes){
					array_push($Subdesign,$sdes->id_sub_design);
				}
				$this->db->where_in('t.id_sub_design', $Subdesign);
			}
			if(sizeof($data['Size']) > 0){
				$size = [];
				foreach($data['Size'] as $sz){
					array_push($size,$sz->id_size);
				}
				$this->db->where_in('t.size', $size);
			}
			if(!empty($data['From Weight(g)'])){
				$this->db->where('t.gross_wt >=', $data['From Weight(g)']);
			}
			if(!empty($data['To Weight(g)'])){
				$this->db->where('t.gross_wt <=', $data['To Weight(g)']);
			}
		}

		if(sizeof($data['Stock Status']) > 0 ){
			$stock = [];
			foreach($data['Stock Status'] as $s){
				$status = $s->name == 'Open' ? 0 : ($s->name == 'Close' ? 1 : '');
				array_push($stock,$status);
			}
			$this->db->where_in('t.tag_status', $stock);
		} else {
			$status_arr = [0,1];
			$this->db->where_in('t.tag_status', $status_arr);
		}


        /*if($data['lastid'] > 0){
            $this->db->where('t.tag_id <', $data['lastid']);
        }*/

        //$this->db->group_by('t.product_id, t.design_id, t.id_sub_design, t.gross_wt');

		$this->db->order_by("tm.date_add", "desc");
		$this->db->order_by("tm.image", "desc");
        $this->db->order_by("t.tag_id", "desc");
		$this->db->limit(10, ($page_no*10));

        $query = $this->db->get();

		//echo $this->db->last_query();exit;

		$rate_fields 	= $this->get_metal_rate_fields();
		$stn_condition  = $data['Stone Type'];
		$tagStones 		= $this->get_tag_stone_details($stn_condition);
		$purities       = $this->get_purityData();
		$sizes			= $this->get_sizeData();
		$weights		= $this->get_weightRangeData();

        if($query)
        {
            $success = true;
            if ( $query->num_rows() > 0 )
            {

				$result = $query->result_array();
				$resultArr = [];
				//$i = 1;
				//$records = 1;
				/*echo "<pre>";
				print_r($result);
				exit;*/
				foreach($result as $row) {

					/*if($i > 10) {
						break;
					}*/

				    $row['incentive'] = 0;

					$row['colorcode'] = "";

					if($row['tag_status'] == 0) {

						$incent = $this->get_colorcode_and_incentives($FNSdata, $row['tag_datetime'], $row['MetalCode']);

						$row['incentive'] = $incent['incentive'];

						$row['colorcode'] = $incent['colorcode'];

					}

					$cat_id = $row['CatCode'];

					$product_id = $row['product_id'];

					$design_id = $row['design_id'];


					$file = self::TAGIMG_PATH.'/'.$row['TagImage'];
					$row['TagImage'] = (file_exists($file)? base_url().''.$file : null );

					$row['img_details']		= $this->getTagImages($row['id_tag_detail']);

					$tagStone = array();

					foreach($tagStones as $stns) {

						if($stns['tag_id'] == $row['id_tag_detail']) {

							$tagStone[] = $stns;

						}
					}

					$stone_price = 0;

					$certification_cost = 0;

					$TagStoneDetails = array();

					foreach($tagStone as $stn) {

						$stone_price = $stone_price + $stn['amount'];

						$certification_cost = $certification_cost + $stn['certification_cost'];

						$TagStoneDetails[] = array(
													"tag_id"		=>  $stn['tag_id'],
													"stone_id"   	=>  $stn['stone_id'],
													"stone_name"   	=>  $stn['stone_name'],
													"uom_short_code"=>	$stn['uom_short_code'],
													"stone_pcs"     =>  $stn['pieces'],
													"stone_wt"      =>  $stn['wt'],
													"uom_id"		=>  $stn['uom_id'],
													"StoneUom"		=>  $stn['uom_name'],
													"rate"          =>  $stn['rate_per_gram'],
													"is_apply_in_lwt" => $stn['is_apply_in_lwt'],
													"lwt"			=> $stn['lwt'],
													"stone_type"	=> $stn['stone_type'],
													"stone_type_name"	=> $stn['stone_type_name'],
													"stone_cal_type"=> $stn['stone_cal_type'],
													"stone_price"	=> $stn['amount']
													);

					}

					$row['stone_price'] = $stone_price;

					$row['certification_cost'] = $certification_cost;

					$charge_value = 0;

					$tagCharges     = $this->get_charges($row['id_tag_detail']);

					foreach($tagCharges as $chrg) {

						$charge_value = $charge_value + $chrg['charge_value'];

					}

					$row['charge_value'] = $charge_value;

					$other_metal_record = $this->get_tag_other_metal_data($row['id_tag_detail']);

					$other_metals = $this->get_other_metal_details($other_metal_record);

					$row['other_metal_details'] = $other_metals['otherMetals'];

					$row['other_metal_total_wt'] = $other_metals['other_metal_total_wt'];

					$row['other_metal_total_price'] = $other_metals['other_metal_total_price'];

					$total = $this->get_totalprice($row, $rates, $rate_fields, $tax_details, $row['other_metal_total_price']);

					$total_price = $total['total_price'];

					/*$from_price_validation = true;

					if(!empty($data['From Price'])) {

						if($total_price >= $data['From Price']) {

							$from_price_validation = true;

						} else {

							$from_price_validation = false;

						}

					}

					$to_price_validation = true;

					if(!empty($data['To Price'])) {

						if($total_price <= $data['To Price']) {

							$to_price_validation = true;

						} else {

							$to_price_validation = false;

						}

					}

					$stone_validation = true;

					if(sizeof($stn_condition) > 0) {

						if(count($TagStoneDetails) > 0) {

							$stone_validation = true;

						} else {

							$stone_validation = false;
						}
					}*/

					/*echo " from_price_validation ".$from_price_validation;
					echo " to_price_validation ".$to_price_validation;
					echo " stone_validation ".$stone_validation;*/

					//if($from_price_validation && $to_price_validation && $stone_validation) {

						//$records_start =  ($page_no * 10) + 1;

						//if($records >= $records_start) {

							$row['Rate'] = round($total_price,2);

							$row['purities'] = $this->get_purities($purities, $cat_id, $row['MetalCode'], $rate_fields);

							$row['weights'] = $this->get_weightRange($weights, $product_id, $design_id);

							$row['sizes'] = $this->get_sizes($sizes, $product_id);

							/*$exist = array_search($row['id_tag_detail'], array_column($resultArr, 'id_tag_detail'));
							if($exist > -1){
								if($TagStoneDetails == null) {
									$resultArr[$exist]['TagStoneDetails'] = array();
								} else {
									$resultArr[$exist]['TagStoneDetails'][] = $TagStoneDetails;
								}
								$i++;
							}else{
								if($TagStoneDetails == null) {
									$row['TagStoneDetails'] = array();
								} else {
									$row['TagStoneDetails'][] = $TagStoneDetails;
								}
								$resultArr[] = $row;
								$i++;
							}*/

							if($TagStoneDetails == null) {
								$row['TagStoneDetails'] = array();
							} else {
								$row['TagStoneDetails'] = $TagStoneDetails;
							}
							$resultArr[] = $row;
							//$i++;
						//}

						//$records++;

					//}

				}
				//exit;
				$message = "Records retrived successfully";
            }
            else
            {
                $message = "No records found";
            }

            return $resultArr;
        }
        else
        {
            $err_message = $this->db->_error_message();
            throw new Exception("Database Error occured.".$err_message);
        }
    }

	function get_purities($purities, $cat_id, $metal_type = "", $rate_fields = array()) {

		$purity_arr = array();

		foreach($purities as $purity) {

			if($purity['id_category'] == $cat_id) {

				$rate_field = $this->get_ratefield($purity['id_purity'], $metal_type, $rate_fields);

				$purity['rate_field'] = $rate_field;

				$purity['market_rate_field'] = $rate_field;

				$purity_arr[] = $purity;

			}

		}

		return $purity_arr;
	}

	function get_ratefield($purity_id, $metal_id, $rate_fields) {

		$rate_field = "";

		foreach($rate_fields as $fields) {

			$rate_metal_id = $fields['id_metal'];

			$rate_id_purity = $fields['id_purity'];

			$field = $fields['rate_field'];

			if($rate_metal_id == $metal_id &&  $rate_id_purity == $purity_id) {

				$rate_field = $field;

				break;

			}

		}

		return $rate_field;


	}

	function get_weightRange($weights, $product_id, $design_id) {

		$weight_range = array();

		foreach($weights as $weight) {

			if($weight['id_product'] == $product_id && $weight['id_design'] == $design_id) {

				$weight_range[] = $weight;

			}

		}

		return $weight_range;
	}

	function get_sizes($sizes, $product_id) {

		$size_arr = array();

		foreach($sizes as $size) {

			if($size['id_product'] == $product_id) {

				$size_arr[] = $size;

			}

		}

		return $size_arr;
	}

	function get_totalprice($tag, $rates, $rate_fields, $tax_details, $other_metal_amt = 0) {

		$total_tax_rate = 0;

		$metal_id = $tag['MetalCode'];

		$purity_id = $tag['purity_id'];

		$stone_price = $tag['stone_price'];

		$certification_cost = $tag['certification_cost'];

		$charge_value = $tag['charge_value'];

		$calculation_based_on = $tag['calculation_based_on'];

		$gross_wt = $tag['GrossWt'];

		$net_wt = $tag['net_wt'];

		$retail_max_wastage_percent = $tag['retail_max_wastage_percent'];

		$tag_mc_value = $tag['mc_value'];

		$tag_mc_type = $tag['mc_type'];

		$sales_value = $tag['sales_value'];

		$Pieces = $tag['Pieces'];

		$tax_group = $tag['tgrp_id'];

		//$rate = $this->get_rate($metal_id, $purity_id);

		foreach($rate_fields as $fields) {

			$rate_field = "";

			$rate_metal_id = $fields['id_metal'];

			$rate_id_purity = $fields['id_purity'];

			$field = $fields['rate_field'];

			if($rate_metal_id == $metal_id &&  $rate_id_purity == $purity_id) {

				$rate_field = $field;

				break;

			}

		}

		$rate = $rates[$rate_field];

		$rate_with_mc = 0;

		if($calculation_based_on == 0) {

			$wast_wgt 	  = round(($gross_wt * ($retail_max_wastage_percent/100)), 3);

			$mc_type 	  = $tag_mc_type == 2 ? ($tag_mc_value * $gross_wt) : ($tag_mc_value * $Pieces);

			// Metal Rate + Stone + OM + Wastage + MC

			$rate_with_mc = ($rate * ($wast_wgt + $net_wt)) + $mc_type;

		}

		else if($calculation_based_on == 1) {

			$wast_wgt 	  = round(($net_wt * ($retail_max_wastage_percent/100)), 3);

			$mc_type 	  = $tag_mc_type == 2 ? ($tag_mc_value * $net_wt) : ($tag_mc_value * $Pieces);

			// Metal Rate + Stone + OM + Wastage + MC

			$rate_with_mc = ($rate * ($wast_wgt + $net_wt)) + $mc_type;

		}

		else if($calculation_based_on == 2) {

			$wast_wgt 	  = round(($net_wt * ($retail_max_wastage_percent/100)), 3);

			$mc_type 	  = $tag_mc_type == 2 ? ($tag_mc_value * $gross_wt) : ($tag_mc_value * $Pieces);

			// Metal Rate + Stone + OM + Wastage + MC

			$rate_with_mc = ($rate * ($wast_wgt + $net_wt)) + $mc_type;

		}

		else if($calculation_based_on == 3 || $calculation_based_on == 4) {

			$rate_with_mc = $sales_value;

		}

		$rate_with_mc = $rate_with_mc + $stone_price + $certification_cost + $charge_value + $other_metal_amt;

		if(count($tax_details) > 0){

			// Tax Calculation

			$base_value_tax = round($this->calculate_base_value_tax($rate_with_mc, $tax_group, $tax_details),2);

			$base_value_amt = round(($rate_with_mc + $base_value_tax),2);

			$arrived_value_tax = round(($this->calculate_arrived_value_tax($base_value_amt, $tax_group, $tax_details)),2);

			$arrived_value_amt = round($base_value_amt + $arrived_value_tax, 2);

			$total_tax_rate = round($base_value_tax + $arrived_value_tax, 2);

		}

	  	$total_price = round($rate_with_mc + $total_tax_rate, 2);

		$total = array('rate' => $rate, 'total_price' => $total_price, 'total_tax_rate' => $total_tax_rate);

		return $total;

	}

	function get_tax_details() {

		$CI = & get_instance();

		$CI->load->model('ret_billing_model');

		$tax_details = $CI->ret_billing_model->getAllTaxgroupItems();

		return $tax_details;

	}

	function calculate_base_value_tax($taxcallrate, $taxgroup, $tax_details){

		$totaltax = 0;

		foreach($tax_details as $taxitem) {

			if($taxitem['tgi_tgrpcode'] == $taxgroup) {

				if($taxitem['tgi_calculation'] == 1) {

					if($taxitem['tgi_type'] == 1){

						$totaltax += $taxcallrate * ($taxitem['tax_percentage']/100);

					}else{

						$totaltax -= $taxcallrate * ($taxitem['tax_percentage']/100);

					}

				}

			}

		}

		return $totaltax;

	}

	function calculate_arrived_value_tax($taxcallrate, $taxgroup, $tax_details){

		$totaltax = 0;

		foreach($tax_details as $taxitem) {

			if($taxitem['tgi_tgrpcode'] == $taxgroup) {

				if($taxitem['tgi_calculation'] == 2) {

					if($taxitem['tgi_type'] == 1){

						$totaltax += $taxcallrate * ($taxitem['tax_percentage']/100);

					}else{

						$totaltax -= $taxcallrate * ($taxitem['tax_percentage']/100);

					}

				}

			}

		}

		return $totaltax;

	}

	function get_metal_rate_fields() {

		$this->db->select('id_metal_purity_rate, id_metal, id_purity, rate_field, market_rate_field');

		$this->db->from('ret_metal_purity_rate');

		//$this->db->limit(10, 0);

		$query = $this->db->get();

		$result = $query->result_array();

		return $result;

	}

	function get_rates() {

		$this->db->select('*');

		$this->db->from('metal_rates');

		$this->db->order_by("updatetime", "DESC");

		$this->db->limit(1, 0);

		$query = $this->db->get();

		$result = $query->row_array();

		return $result;

	}

	function get_tag_stone_details($stn_condition) {

		$this->db->select('
						tag_id,
						tag_stone_id,
						pieces,
						wt,
						rate_per_gram,
						amount,
						is_apply_in_lwt,
						stone_type,
						IF(stone_type=1,"Precious", IF(stone_type=2,"Semi-Precious", IF(stone_type=3,"Normal", ""))) as stone_type_name,
						stone_cal_type,
						tagst.stone_id,
						stone_code,
						stone_name,
						uom_name,
						uom_short_code,
						uom.uom_id');

		$this->db->from('ret_taging_stone tagst');

		if(sizeof($stn_condition) > 0 ){

			$stones = [];

			foreach($stn_condition as $st){

				array_push($stones,$st->stone_id);

			}

			$this->db->where_in('tagst.stone_id', $stones);
		}

		$this->db->join('ret_stone st', 'st.stone_id = tagst.stone_id', 'left');

		$this->db->join('ret_uom uom', 'uom.uom_id = tagst.uom_id', 'left');

		//$this->db->where('tagst.tag_id',$tag_id);

		$query = $this->db->get();

		$stone_details = $query->result_array();

		return $stone_details;

	}

	function ret_sub_design_mapping_images($id_sub_design_mapping)

	{

	    $returnData=array();

	    $sql=$this->db->query("SELECT * FROM `ret_sub_design_mapping_images` WHERE id_sub_design_mapping=".$id_sub_design_mapping."");

	    $result=$sql->result_array();

	    foreach($result as $items)

	    {

	        $returnData[]=array(

	                          'id_design_img'           =>$items['id_design_img'],

	                          'id_sub_design_mapping'   =>$items['id_sub_design_mapping'],

	                          'image_name'              =>($items['image_name']!='' ? base_url().'assets/img/sub_design/'.$items['id_sub_design_mapping'].'/'.$items['image_name']  :base_url().'assets/img/no_image.png'),

	                          'is_default'              =>$items['is_default'],

	                         );

	    }

	    return $returnData;

	}

	function checksub_design_mapping_images($id_sub_design_mapping)

	{

	    $sql=$this->db->query("SELECT * FROM `ret_sub_design_mapping_images` WHERE id_sub_design_mapping=".$id_sub_design_mapping." and is_default=1");

	    if($sql->num_rows()>0)

	    {

	        return FALSE;

	    }

	    else

	    {

	        return TRUE;

	    }

	}

	function get_sub_design_iamges_by_id($id_design_img)

	{

	    $sql=$this->db->query("SELECT * FROM `ret_sub_design_mapping_images` WHERE id_design_img=".$id_design_img."");

	    return $sql->row_array();

	}


	//Design image upload

	function ret_design_mapping_images($id_design_mapping)

	{

	    $returnData=array();

	    $sql=$this->db->query("SELECT * FROM `ret_design_mapping_images` WHERE id_design_mapping=".$id_design_mapping."");

	    $result=$sql->result_array();

	    foreach($result as $items)

	    {

	        $returnData[]=array(

	                          'id_desmap_img'           =>$items['id_desmap_img'],

	                          'id_design_mapping'   	=>$items['id_design_mapping'],

	                          'image_name'              =>($items['image_name']!='' ? base_url().'assets/img/design/'.$items['id_design_mapping'].'/'.$items['image_name']  :base_url().'assets/img/no_image.png'),

	                          'is_default'              =>$items['is_default'],

	                         );

	    }

	    return $returnData;

	}

	function check_design_mapping_images($id_design_mapping)

	{

	    $sql=$this->db->query("SELECT * FROM `ret_design_mapping_images` WHERE id_design_mapping=".$id_design_mapping." and is_default=1");

	    if($sql->num_rows()>0)

	    {

	        return FALSE;

	    }

	    else

	    {

	        return TRUE;

	    }

	}

	function get_design_iamges_by_id($id_desmap_img)

	{

	    $sql=$this->db->query("SELECT * FROM `ret_design_mapping_images` WHERE id_desmap_img=".$id_desmap_img."");

	    return $sql->row_array();

	}

	function get_purityData() {

		$pur_sql  = $this->db->query("SELECT

										p.id_purity,

										p.purity,

										c.id_category

									FROM ret_metal_cat_purity c

									LEFT JOIN ret_purity p ON p.id_purity = c.id_purity

									WHERE p.status = 1");

		$purities = $pur_sql->result_array();

		$pur_sql->free_result();

		return $purities;
	}

	function get_sizeData() {

		$size_sql  = $this->db->query("SELECT * FROM ret_size");

		$sizes = $size_sql->result_array();

		$size_sql->free_result();

		return $sizes;

	}

	function get_weightRangeData() {

		$weight_sql = $this->db->query("SELECT

											w.id_weight,

											w.value,

											w.weight_description,

											w.from_weight,

											w.to_weight,

											w.id_product,

											w.id_design

										FROM ret_weight w");

		$weights = $weight_sql->result_array();

		$weight_sql->free_result();

		return $weights;

	}

	function getSuppFilters()
    {
        $resultArr = array();

        $metal = $this->db->query('SELECT metal AS name, "-1" AS count, m.id_metal AS metal_code FROM metal m');
        $resultArr['Metal'] = $metal->result_array();
        $metal->free_result();

        $product = $this->db->query('SELECT product_name AS name, "-1" AS count, p.product_short_code AS productcode, p.pro_id as id_product, p.metal_type AS metalcode FROM ret_product_master p');
        $resultArr['Product'] = $product->result_array();
        $product->free_result();

		$design = $this->db->query('SELECT m.mapping_id, m.pro_id as id_product, m.id_design, p.product_name, des.design_name,
										CONCAT(des.design_name, "-", LEFT(p.product_name , 3)) as name, "-1" as count,
										des.design_code, p.product_short_code AS productcode, p.metal_type AS metalcode
										FROM ret_product_mapping m
										LEFT JOIN ret_product_master p ON p.pro_id=m.pro_id
										LEFT JOIN ret_design_master des ON des.design_no=m.id_design LIMIT 200');

        $resultArr['Design'] = $design->result_array();

        $design->free_result();

		$subdesign = $this->db->query('SELECT m.id_sub_design_mapping, m.id_product, m.id_design, m.id_sub_design, p.product_name, des.design_name, sdes.sub_design_name,
										CONCAT(sdes.sub_design_name, "-", LEFT(des.design_name , 3)) as name, "-1" as count,
										des.design_code, p.product_short_code AS productcode, sdes.sub_design_code, p.metal_type AS metalcode
										FROM ret_sub_design_mapping m
										LEFT JOIN ret_product_master p ON p.pro_id=m.id_product
										LEFT JOIN ret_design_master des ON des.design_no=m.id_design
										LEFT JOIN ret_sub_design_master sdes ON sdes.id_sub_design=m.id_sub_design LIMIT 200');

        $resultArr['Subdesign'] = $subdesign->result_array();

        $subdesign->free_result();

        $size = $this->db->query('SELECT CONCAT(value, " ", LOWER(name)) as name, "-1" as count, id_size FROM `ret_size` ORDER BY value');
        $resultArr['Size'] = $size->result_array();
        $size->free_result();

        $resultArr['From Weight(g)'] = 0;
        $resultArr['To Weight(g)'] = 0;
        /*$resultArr['From Price'] = 0;
        $resultArr['To Price'] = 0;*/

        return $resultArr;
    }

	function applySuppFilter($data,$type, $id_supp_catalogue = 0)
    {
		$searchType = 0;
		$tagStr = "";
		if(isset($data['TagNo'])) {
			$tagStr = trim($data['TagNo']);
			if($tagStr != "") {
				$isTagNo = substr_count($tagStr, '-');
				$searchType = $isTagNo > 0 ? 1 : 2;
			}
		} else if(isset($data['is_newArrival']) && $data['is_newArrival'] == 1) {
			$searchType = 3;
		}

		$page_no = isset($data['page_no']) && $data['page_no'] > 0 ? $data['page_no'] - 1 : 0;
		$id_customer = isset($data['id_customer']) && $data['id_customer'] > 0 ? $data['id_customer'] : 0;
        $resultArr = [];
		$this->db->_protect_identifiers = false;
        $this->db->select('
			t.id_supp_catalogue,
			t.ctl_datetime as tag_datetime,
			t.design_code,
			"" as BranchCode,
			"" as BranchName,
			p.metal_type as MetalCode,
			m.metal as MetalName,
			rc.name as CatName,
			p.cat_id as CatCode,
			t.product_id,
			p.product_short_code AS ProductCode,
			p.product_name as ProductName,
			t.design_id,
			des.design_name,
			t.id_sub_design,
			sdes.sub_design_name,
			"" as Pieces,
			suppwt.weight as GrossWt,
			0 as less_wt,
			suppwt.weight as weight,
			"" as pcs,
			suppwt.weight as NetWt,
			suppwt.wastage as retail_max_wastage_percent,
			suppwt.mc_value,
			suppwt.mc_type,
			IF(suppwt.mc_type=1, suppwt.mc_value, suppwt.mc_value * suppwt.weight) as total_mc,
			"" as purity_id,
			r.purity as Purity,
			"" as JewelGroupName,
			"" as ItemCollection,
			"" as Nature,
			catimg.image as TagImage,
			catimg.id_supp_cat_img as id_supp_cat_img,
			"" as SizeName,
			"" as id_size,
			"" as net_wt,
			rc.tgrp_id,
			"" as due_date,
			min_order_weight,
			0 as sales_value,
			UNIX_TIMESTAMP(t.ctl_datetime) as timestamp,
			IF(IFNULL(wl.id_wishlist,0) > 0, 1, 0) AS wishlist,
			"" as TagNo,
			"" as section_name,
			"" as hu_id,
			"" as hu_id2'
		);
        $this->db->from('ret_supp_catalogue t');
        $this->db->join('ret_product_master p', 'p.pro_id = t.product_id', 'left');
		$this->db->join('ret_design_master des', 'des.design_no = t.design_id', 'left');
		$this->db->join('ret_sub_design_master sdes', 'sdes.id_sub_design = t.id_sub_design', 'left');
		$this->db->join('ret_supp_catalogue_weight suppwt', 'suppwt.id_supp_catalogue = t.id_supp_catalogue', 'left');
		$this->db->join('supp_catalogue_images catimg', 'catimg.id_supp_catalogue = t.id_supp_catalogue  AND catimg.is_default=1', 'left');
		$this->db->join('ret_category rc', 'rc.id_ret_category = p.cat_id', 'left');
		$this->db->join('metal m', 'm.id_metal = p.metal_type', 'left');
		$this->db->join('(SELECT * FROM ret_wishlist_enquiry WHERE id_wishlist IN (SELECT MAX(id_wishlist) FROM ret_wishlist_enquiry WHERE customer_id = '.$id_customer.' GROUP BY id_supp_catalogue)) AS wl', 'wl.id_supp_catalogue = t.id_supp_catalogue', 'left');
		$this->db->join('ret_purity r', 'r.id_purity=suppwt.purity', 'left');

		if($id_supp_catalogue > 0) {

			$this->db->where_in('t.id_supp_catalogue', $id_supp_catalogue);

		}

		if(isset($data['searchTxt']) && $data['searchTxt'] != "") {

			$this->db->like('p.product_name', $data['searchTxt'], 'after');

		}

		if(sizeof($data['Product']) > 0 ){
			$product = [];
			foreach($data['Product'] as $pr){
				array_push($product,$pr->id_product);
			}
			$this->db->where_in('t.product_id', $product);
		}
		if(sizeof($data['Metal']) > 0 ){
			$metal = [];
			foreach($data['Metal'] as $m){
				array_push($metal,$m->metal_code);
			}
			$this->db->where_in('p.metal_type', $metal);
		}
		if(sizeof($data['Design']) > 0 ){
			$Design = [];
			foreach($data['Design'] as $des){
				array_push($Design,$des->id_design);
			}
			$this->db->where_in('t.design_id', $Design);
		}
		if(sizeof($data['Subdesign']) > 0 ){
			$Subdesign = [];
			foreach($data['Subdesign'] as $sdes){
				array_push($Subdesign,$sdes->id_sub_design);
			}
			$this->db->where_in('t.id_sub_design', $Subdesign);
		}
		if(sizeof($data['Size']) > 0){
			$size = [];
			foreach($data['Size'] as $sz){
				array_push($size,$sz->id_size);
			}
			$this->db->where_in('t.size', $size);
		}
		if(!empty($data['From Weight(g)'])){
			$this->db->where('suppwt.from_weight >=', $data['From Weight(g)']);
		}
		if(!empty($data['To Weight(g)'])){
			$this->db->where('suppwt.to_weight <=', $data['To Weight(g)']);
		}

		$this->db->where('t.status', 1);

		$this->db->order_by("t.created_on", "desc");
		$this->db->order_by("t.image", "desc");
        $this->db->order_by("t.id_supp_catalogue", "desc");

		if($type != "getCount") {
			$this->db->limit(10, ($page_no*10));
		}

        $query = $this->db->get();
        //echo $this->db->last_query();exit;

		//$rates 		= $this->get_rates();
		$rate_fields 	= $this->get_metal_rate_fields();
		//$tax_details 	= $this->get_tax_details();
		$suppPurities   = $this->get_suppPurityData();
		$suppSizes		= $this->get_suppsizeData();
		$suppWeights	= $this->get_suppWeightRangeData();

		$row['weights'] = array();

		/*echo "<pre>";
		print_r($suppWeights);
		print_r($rate_fields);
		print_r($suppPurities);
		print_r($suppSizes);
		exit;*/

        if($query)
        {
            $success = true;

			if ( $query->num_rows() > 0 ) {

				$result = $query->result_array();

				if($type == "getCount") {

					return $result;

				} else {

					$resultArr = [];

					$row['img_details']	= [];

					foreach($result as $row) {

						$id_supp_catalogue = $row['id_supp_catalogue'];

						$file = self::SUPPIMG_PATH.$row['id_supp_cat_img']."-".$row['TagImage'];

						$supplier_cat_img = (file_exists($file)? base_url().$file : null );

						$row['TagImage'] = $supplier_cat_img;

						$TagStoneDetails = array();

						$row['stone_price'] = "";

						$row['certification_cost'] = "";

						$row['Rate'] = "";

						$rowWeight = $this->get_suppWeightRange($suppWeights, $id_supp_catalogue);

						$defaultImages = array();

						if(count($rowWeight) > 0) {

							$weightInc = 1;

							foreach($rowWeight as $suppWeight) {

								$purityList = array();

								if(trim($suppWeight['id_purity']) != "") {

									$purArr = explode(",", trim($suppWeight['id_purity']));

									foreach($purArr as $purity_id) {

										if($purity_id != "") {

											$pur = $this->get_suppPurities($suppPurities, $purity_id, $row['MetalCode'], $rate_fields);

											if(count($pur) > 0) {

												$purityList[] = $pur;

											}

										}

									}

								}


								$suppWeight['purities'] = $purityList;

								/*$sizeList = array();

								if(trim($suppWeight['id_size']) != "") {

									$sizeList = explode(",", trim($suppWeight['id_size']));

								}*/

								$sizeList = array();

								if(trim($suppWeight['id_size']) != "") {

									$sizeArr = explode(",", trim($suppWeight['id_size']));

									foreach($sizeArr as $size_id) {

										if($size_id != "") {

											$sz = $this->get_suppSizes($suppSizes, $size_id);

											if(count($sz) > 0) {

												$sizeList[] = $sz;

											}

										}

								}

								}

								$suppWeight['sizes'] = $sizeList;

								$suppWeight['img_details']		= $this->getSuppCatImages($id_supp_catalogue, $suppWeight['id_weight']);

								$row['weights'][] = $suppWeight;

								if($weightInc == 1)  {

									$defaultImages  = $suppWeight['img_details'];

								}

								$weightInc++;

							}

						}

						$row['other_metal_details'] = array();

						$row['other_metal_total_wt'] = 0;

						$row['other_metal_total_price'] = 0;

						if($defaultImages) {

							$row['img_details'] = $defaultImages;

						}

						if($TagStoneDetails == null) {

							$row['TagStoneDetails'] = array();

						} else {

							$row['TagStoneDetails'][] = $TagStoneDetails;

						}

						$resultArr[] = $row;

					}

					$message = "Records retrived successfully";

				}
            }
            else
            {
                $message = "No records found";
            }

            return $resultArr;
        }
        else
        {
            $err_message = $this->db->_error_message();
            throw new Exception("Database Error occured.".$err_message);
        }
    }


	function get_suppPurityData() {

		$pur_sql  = $this->db->query("SELECT

										p.id_purity,

										p.purity,

										c.id_category

									FROM ret_metal_cat_purity c

									LEFT JOIN ret_purity p ON p.id_purity = c.id_purity

									WHERE p.status = 1");

		$purities = $pur_sql->result_array();

		$pur_sql->free_result();

		return $purities;
	}

	function get_suppSizeData() {

		$size_sql  = $this->db->query("SELECT * FROM ret_size");

		$sizes = $size_sql->result_array();

		$size_sql->free_result();

		return $sizes;

	}

	function get_suppWeightRangeData() {

		$weight_sql = $this->db->query("SELECT

											cw.id_catalogue_weight AS id_weight,

											cw.id_supp_catalogue,

											cw.weight,

											cw.weight AS weight_value,

											cw.purity AS id_purity,

											cw.size AS id_size,

											cw.wastage,

											cw.display_va,

											cw.mc_value,

											cw.mc_type,

											cw.display_mc,

											cw.delivery_duration AS smith_due_date,

											cw.display_duration,

											cw.calculation_based_on,

											CONCAT(cw.weight,'GM') AS weight_description,

											cw.from_weight,

											cw.to_weight

										FROM ret_supp_catalogue_weight AS cw");

		$weights = $weight_sql->result_array();

		$weight_sql->free_result();

		return $weights;

	}


	function get_suppWeightRange($weights, $supplier_id) {

		$weight_range = array();

		foreach($weights as $weight) {

			if($weight['id_supp_catalogue'] == $supplier_id) {

				$weight_range[] = $weight;

			}

		}

		return $weight_range;
	}

	function get_suppPurities($purities, $purity_id, $metal_type = "", $rate_fields = array()) {

		$purity_arr = array();

		foreach($purities as $purity) {

			if($purity['id_purity'] == $purity_id) {

				$rate_field = $this->get_ratefield($purity['id_purity'], $metal_type, $rate_fields);

				$purity['rate_field'] = $rate_field;

				$purity['market_rate_field'] = $rate_field;

				$purity_arr = $purity;

				break;

			}

		}

		return $purity_arr;

	}

	function get_suppSizes($sizes, $size_id) {

		$size_arr = array();

		foreach($sizes as $size) {

			if($size['id_size'] == $size_id) {

				$size_arr = $size;

				break;

			}

		}

		return $size_arr;

	}

	function getSuppCatImages($id_supp_catalogue = 0, $id_catalogue_weight = 0) {

		$returnData=array();

		$where = "";

		if($id_supp_catalogue > 0) {

			$where = " AND id_supp_catalogue =".$id_supp_catalogue;

		}

		/*if($id_catalogue_weight > 0) {

			$where = " AND id_catalogue_weight =".$id_catalogue_weight;

		}*/

	    $sql=$this->db->query("SELECT id_supp_cat_img, id_supp_catalogue, id_catalogue_weight, image, date_add, is_default FROM `supp_catalogue_images` WHERE 1 ".$where);

	    $result=$sql->result_array();

	    foreach($result as $items) {

			$file = self::SUPPIMG_PATH.$items['id_supp_cat_img']."-".$items['image'];

			$supplier_cat_img = (file_exists($file)? base_url().$file : null );

	        $returnData[]=array(

	                          'id_desmap_img'  		=>$items['id_supp_cat_img'],

							  'id_tag_img'     		=>$items['id_supp_cat_img'],

	                          'tag_id'   	   		=>$items['id_supp_catalogue'],

							  'id_catalogue_weight' =>$items['id_catalogue_weight'],

	                          'image_name'     		=>($items['image']!='' ? $supplier_cat_img  : base_url().'assets/img/no_image.png'),

	                          'is_default'     		=>$items['is_default'],

	                         );

	    }

	    return $returnData;

	}

	function CheckStockAvail($data)
	{
	    if($data['id_weight']!='')
	    {
	        $weight = $this->get_weight_details($data['id_weight']);
	    }

        $sql = $this->db->query("SELECT COUNT(t.piece) as tot_pcs
        FROM ret_taging t
        WHERE t.current_branch = ".$data['id_branch']." AND t.product_id = ".$data['id_product']." AND t.design_id = ".$data['design_no']." AND t.id_sub_design = ".$data['id_sub_design']."
        ".($data['id_size']!='' ? " and t.size=".$data['id_size']."" :'')."
        ".($data['id_weight']!='' && $weight['from_weight']!='' ? " and t.gross_wt BETWEEN '".$weight['from_weight']."'  AND '".$weight['to_weight']."' " :'')."");
        return $sql->row()->tot_pcs;
	}

	function get_weight_details($id_weight)
	{
	    $sql = $this->db->query("SELECT * FROM `ret_weight` where id_weight=".$id_weight."");
	    return $sql->row_array();
	}

	function get_ret_settings($settings = array()) {

		$where = "";

		if(count($settings) > 0) {

			$implode_arr = "'" . implode ( "','", $settings ) . "'";

			$where = $where." AND name IN (".$implode_arr.")";

		}

		$sql = "SELECT name, value FROM ret_settings WHERE 1 ".$where;

		$data=$this->db->query($sql);

		return $data->result_array();

	}

	function getEnqFields() {

		$price_range = array();

		$weight_range = array();

		$reasons_for_leaving = array();

		$setting_array = ['pro_price_range', 'reasons_for_leaving'];
		$settings = $this->get_ret_settings($setting_array);

		foreach($settings as $setting) {
			if($setting['name'] == 'pro_price_range') {
				$price_range = $setting['value'];
			}
			if($setting['name'] == 'reasons_for_leaving') {
				$reasons_for_leaving = $setting['value'];
			}
		}

		if($price_range != "") {
			$price_range = explode(',', $price_range);
		}

		if($reasons_for_leaving != "") {
			$reasons_for_leaving = explode(',', $reasons_for_leaving);
		}

		$weight_range = $this->get_weightRangeData();

		$return_array = array(
			"price_range"	=>	$price_range,
			"reasons_for_leaving"	=>	$reasons_for_leaving,
			"weight_range"	=>	$weight_range,
		);

		return $return_array;

	}

	function checkEnqImgExists($imgName) {

		$imageExists = false;

		$sql = "SELECT images FROM ret_wishlist_enquiry WHERE INSTR(images, '".$imgName."') > 0";

		//echo $sql;exit;

		$result = $this->db->query($sql);

		if($result->num_rows() > 0) {

			$imageExists = true;

		}

		return $imageExists;

	}

	function getEstimationByNo($esti_no, $id_branch) {


		$dcData = $this->getBranchDayClosingData($id_branch);

		$item_details=array();

		$items_query = $this->db->query("SELECT

								est.estimation_id,

								est.esti_no,

								date_format(est.estimation_datetime, '%d-%m-%Y %H:%i:%s') as estimatedon,

								est.id_branch,

								cus_id AS id_customer,

								cus.firstname,

								cus.lastname,

								cus.mobile AS phone,

								CONCAT(IFNULL(cus.firstname, ''),' ',IFNULL(cus.lastname, '')) AS name,

								cty.name AS area

								FROM ret_estimation AS est

								LEFT JOIN customer AS cus ON cus.id_customer = est.cus_id

								LEFT JOIN address AS addr ON addr.id_customer = cus.id_customer

								LEFT JOIN city AS cty ON cty.id_city = addr.id_city

								WHERE est.id_branch = '".$id_branch."' AND  est.esti_no = '".$esti_no."' AND  date(est.estimation_datetime) = '".$dcData['entry_date']."'");

								//echo $this->db->_error_message(); exit;

								//print_r($this->db->last_query());exit;

								if($items_query->num_rows() > 0) {

									$records = $items_query->result_array();

									foreach($records as $record){

										$item_details['status'] = true;

										$item_details['msg'] = "Record retrieved successfully";

										$item_details['tagData'] = $record;

										$item_details['tagData']['ref_details'] = $this->getEstimationDetails($record['estimation_id']);

									}

								} else {

									$item_details['status'] = false;

									$item_details['msg'] = "Record not found for this estimation no ".$esti_no;

									$item_details['tagData'] = array();

								}

								return $item_details;

	}

	function getEstimationDetails($est_id) {

		$return_data=array();

		$items_query = $this->db->query("SELECT

											ifnull(est_itms.product_id, '') as pro_id,

											ifnull(est_itms.design_id, '') as design_no,

											ifnull(est_itms.id_sub_design, '') as sub_design_no,

											ifnull(pro.product_name, '-') as product_name,

											ifnull(des.design_name, '') as design_name,

											ifnull(sub_des.sub_design_name, '') as sub_design_name,

											est_itms.gross_wt,

											est_itms.less_wt,

											est_itms.net_wt,

											est_itms.piece

		FROM ret_estimation_items as est_itms

		LEFT JOIN ret_product_master as pro ON pro.pro_id = est_itms.product_id

		LEFT JOIN ret_design_master as des ON des.design_no = est_itms.design_id

		LEFT JOIN ret_sub_design_master as sub_des ON sub_des.id_sub_design = est_itms.id_sub_design

		WHERE est_itms.esti_id = '".$est_id."'");

		//echo $this->db->_error_message(); exit;

		//print_r($this->db->last_query());exit;

		$item_details = $items_query->result_array();

		foreach($item_details as $details) {

			$details['images'] = array();

			$return_data[] = $details;

		}

		return $return_data;

	}

	function get_img_by_id($tag_img_id, $tag_id) {

        $sql=$this->db->query("SELECT * FROM `ret_taging_images` WHERE id_tag_img = ".$tag_img_id." AND tag_id =".$tag_id);

        return $sql->result_array();
    }

	function check_device_logged_in($username, $device_id) {

		$is_logged_in = false;

		if($username != "" && $device_id != "") {

			$sql = "SELECT IFNULL(device_logged_in,'') AS device_logged_in, is_logged_in, is_lmx FROM employee WHERE username ='".$username."'";

			$result = $this->db->query($sql);

			if($result->num_rows() > 0) {

				$result = $result->result_array();

				foreach($result as $record) {

					if($record['device_logged_in'] != $device_id && $record['is_logged_in'] == 1 && $record['is_lmx'] == 0) {

						$is_logged_in = true;

					}

				}
			}

		}

		return $is_logged_in;

	}

	function update_device_login_status($username, $device_id, $status) {

		$updStatus=$this->updateData(array('is_logged_in' => $status, 'device_logged_in' => $device_id),"username", $username, "employee");

		if($updStatus !== 0) {

			return true;

		} else {

			return false;

		}

	}

	function getFNSdata($settings) {

		$fast_moving_gold = $slow_moving_gold = $non_moving_gold ="";

		$fast_moving_silver = $slow_moving_silver = $non_moving_silver ="";

		$fast_moving_gold_days = $fast_moving_gold_color = $fast_moving_gold_incent = $slow_moving_gold_days = $slow_moving_gold_color = $slow_moving_gold_incent = $non_moving_gold_days = $non_moving_gold_color = $non_moving_gold_incent = "";

		$fast_moving_silver_days = $fast_moving_silver_color = $fast_moving_silver_incent = $slow_moving_silver_days = $slow_moving_silver_color = $slow_moving_silver_incent = $non_moving_silver_days = $non_moving_silver_color = $non_moving_silver_incent = "";

		foreach($settings as $setting) {

			if($setting['name'] == 'fast_moving_gold') {

				$fast_moving_gold = $setting['value'];

			}

			if($setting['name'] == 'slow_moving_gold') {

				$slow_moving_gold = $setting['value'];

			}

			if($setting['name'] == 'non_moving_gold') {

				$non_moving_gold = $setting['value'];

			}

			if($setting['name'] == 'fast_moving_silver') {

				$fast_moving_silver = $setting['value'];

			}

			if($setting['name'] == 'slow_moving_silver') {

				$slow_moving_silver = $setting['value'];

			}

			if($setting['name'] == 'non_moving_silver') {

				$non_moving_silver = $setting['value'];

			}
		}

		if($fast_moving_gold != "") {

			$fast_moving_item = explode(',', $fast_moving_gold);

			$fast_moving_gold_days = isset($fast_moving_item[0]) ? $fast_moving_item[0] : '';

			$fast_moving_gold_color = isset($fast_moving_item[1]) ? $fast_moving_item[1] : '';

			$fast_moving_gold_incent = isset($fast_moving_item[2]) ? $fast_moving_item[2] : '';

		}


		if($slow_moving_gold != "") {

			$slow_moving_item = explode(',', $slow_moving_gold);

			$slow_moving_gold_days = isset($slow_moving_item[0]) ? $slow_moving_item[0] : '';

			$slow_moving_gold_color = isset($slow_moving_item[1]) ? $slow_moving_item[1] : '';

			$slow_moving_gold_incent = isset($slow_moving_item[2]) ? $slow_moving_item[2] : '';

		}

		if($non_moving_gold != "") {

			$non_moving_item = explode(',', $non_moving_gold);

			$non_moving_gold_days = isset($non_moving_item[0]) ? $non_moving_item[0] : '';

			$non_moving_gold_color = isset($non_moving_item[1]) ? $non_moving_item[1] : '';

			$non_moving_gold_incent = isset($non_moving_item[2]) ? $non_moving_item[2] : '';

		}

		if($fast_moving_silver != "") {

			$fast_moving_item = explode(',', $fast_moving_silver);

			$fast_moving_silver_days = isset($fast_moving_item[0]) ? $fast_moving_item[0] : '';

			$fast_moving_silver_color = isset($fast_moving_item[1]) ? $fast_moving_item[1] : '';

			$fast_moving_silver_incent = isset($fast_moving_item[2]) ? $fast_moving_item[2] : '';

		}


		if($slow_moving_silver != "") {

			$slow_moving_item = explode(',', $slow_moving_silver);

			$slow_moving_silver_days = isset($slow_moving_item[0]) ? $slow_moving_item[0] : '';

			$slow_moving_silver_color = isset($slow_moving_item[1]) ? $slow_moving_item[1] : '';

			$slow_moving_silver_incent = isset($slow_moving_item[2]) ? $slow_moving_item[2] : '';

		}

		if($non_moving_silver != "") {

			$non_moving_item = explode(',', $non_moving_silver);

			$non_moving_silver_days = isset($non_moving_item[0]) ? $non_moving_item[0] : '';

			$non_moving_silver_color = isset($non_moving_item[1]) ? $non_moving_item[1] : '';

			$non_moving_silver_incent = isset($non_moving_item[2]) ? $non_moving_item[2] : '';

		}

		$goldArr = array(

			"fast_moving_days"		=>	$fast_moving_gold_days,

			"fast_moving_color"		=>	$fast_moving_gold_color,

			"fast_moving_incent"	=>	$fast_moving_gold_incent,

			"slow_moving_days"		=>	$slow_moving_gold_days,

			"slow_moving_color"		=>	$slow_moving_gold_color,

			"slow_moving_incent"	=>	$slow_moving_gold_incent,

			"non_moving_days"		=>	$non_moving_gold_days,

			"non_moving_color"		=>	$non_moving_gold_color,

			"non_moving_incent"		=>	$non_moving_gold_incent,

		);

		$silverArr = array(

			"fast_moving_days"		=>	$fast_moving_silver_days,

			"fast_moving_color"		=>	$fast_moving_silver_color,

			"fast_moving_incent"	=>	$fast_moving_silver_incent,

			"slow_moving_days"		=>	$slow_moving_silver_days,

			"slow_moving_color"		=>	$slow_moving_silver_color,

			"slow_moving_incent"	=>	$slow_moving_silver_incent,

			"non_moving_days"		=>	$non_moving_silver_days,

			"non_moving_color"		=>	$non_moving_silver_color,

			"non_moving_incent"		=>	$non_moving_silver_incent,

		);

		$return_data = array("gold" => $goldArr, "silver" => $silverArr);

		return $return_data;

	}

	function get_colorcode_and_incentives($FNS, $tag_datetime, $metal_type = "") {

		$date_time = date('Y-m-d H:i:s');

		$returndata['incentive'] = "";

		$returndata['colorcode'] = "";

		$fns = $metal_type == 1 ? $FNS['gold'] : ($metal_type == 2 ? $FNS['silver'] : array());

		if(count($fns) > 0) {

			$fast_moving_date = date('Y-m-d H:i:s', strtotime('+'.$fns['fast_moving_days'].' day', strtotime($tag_datetime)));

			$non_moving_date = date('Y-m-d H:i:s', strtotime('+'.$fns['non_moving_days'].' day', strtotime($tag_datetime)));

			$slow_moving_date = date('Y-m-d H:i:s', strtotime('+'.$fns['slow_moving_days'].' day', strtotime($tag_datetime)));

			if($fns['non_moving_days'] >= 0 && $date_time > $non_moving_date) {

				$returndata['incentive'] = $fns['non_moving_incent'];

				$returndata['colorcode'] = $fns['non_moving_color'];

			} else if($fns['slow_moving_days'] >= 0 && $date_time > $slow_moving_date) {

				$returndata['incentive'] = $fns['slow_moving_incent'];

				$returndata['colorcode'] = $fns['slow_moving_color'];

			} else if($fns['fast_moving_days'] >= 0 && $date_time >= $fast_moving_date) {

				$returndata['incentive'] = $fns['fast_moving_incent'];

				$returndata['colorcode'] = $fns['fast_moving_color'];

			}

		}

		return $returndata;
	}

	function getorderBySearch($SearchTxt,$branch,$fin_year_code)
    {

        $returnData = array();
        $data = $this->db->query("SELECT tag.tag_id,
        tag_code, tag_datetime, tag.tag_type, tag_lot_id,
        design_id, cost_center, tag.purity, tag.size, uom, piece, IFNULL(tag.less_wt,0) as less_wt, tag.net_wt, tag.gross_wt,
        tag.calculation_based_on, retail_max_wastage_percent,tag_mc_value as mc_value,tag_mc_type as mc_type,
        halmarking, sales_value, tag.tag_status, product_name, product_short_code, tag.product_id,
        pur.purity as purname,lot_inw.lot_received_at,
        ifnull(tag_stn_detail.stn_amount,if(tag.id_orderdetails!='',cus.stn_amt,0)) as stone_price,IFNULL(tag_stn_detail.certification_cost,0) as certification_cost,
        pro.sales_mode,tag.item_rate,tax.tax_percentage,tax.tgi_calculation,
        if(pro.metal_type = 1,'Gold','Silver') as metal_name,
        r.rate_field as metal_type,
        r.market_rate_field as market_metal_type,
        tag.current_branch,
        pro.tgrp_id as tax_group_id,IFNULL(tag.id_orderdetails,'') as id_orderdetails,
        lot_inw.order_no, des.design_name, '0' as is_partial, IFNULL(rtc.charge_value,0) AS charge_value, cus.rate_per_gram AS order_rate, ctm.id_customer, ctm.firstname, CONCAT(ctm.firstname,'-',ctm.mobile) AS cus_label, cat.scheme_closure_benefit, tag.id_sub_design, lot_inw.lot_no
        FROM ret_taging tag
        LEFT JOIN customerorderdetails cus ON cus.id_orderdetails=tag.id_orderdetails
        LEFT JOIN customerorder c ON c.id_customerorder=cus.id_customerorder
		LEFT JOIN customer ctm ON ctm.id_customer = c.order_to
        Left join ret_lot_inwards_detail lot_det ON tag.id_lot_inward_detail = lot_det.id_lot_inward_detail
        LEFT JOIN ret_lot_inwards as lot_inw ON lot_inw.lot_no = lot_det.lot_no
        LEFT JOIN ret_product_master as pro ON pro.pro_id = tag.product_id
        LEFT JOIN ret_design_master des on des.design_no=tag.design_id
        LEFT JOIN ret_purity as pur ON pur.id_purity = tag.purity
        left join ret_category cat on cat.id_ret_category=pro.cat_id
        left join metal mt on mt.id_metal=cat.id_metal
        LEFT JOIN ret_metal_purity_rate r on r.id_metal=cat.id_metal and r.id_purity=tag.purity
		LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,
		GROUP_CONCAT(m.tax_percentage) as tax_percentage,
		GROUP_CONCAT(i.tgi_calculation) as tgi_calculation
		FROM ret_taxgroupitems i
		LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=pro.tgrp_id
		LEFT JOIN (SELECT tag_id, SUM(IFNULL(charge_value,0)) AS charge_value FROM ret_taging_charges GROUP BY tag_id) AS rtc ON rtc.tag_id = tag.tag_id
        LEFT JOIN (SELECT tag_id,sum(amount) as stn_amount,sum(wt) as stn_wt,sum(certification_cost) as certification_cost
        FROM `ret_taging_stone`
        GROUP by tag_id) as tag_stn_detail ON tag_stn_detail.tag_id = tag.tag_id
		WHERE c.order_no='".$SearchTxt."' and cus.orderstatus=4 and tag.tag_status=0
		and c.fin_year_code=".$fin_year_code."
		".($branch!='' ? " and c.order_from=".$branch."" :'')."
		".($branch!='' ? " and tag.current_branch=".$branch."" :'')."
		AND cus.id_orderdetails is NOT null");
        //print_r($this->db->last_query());exit;
        $result=$data->result_array();

        foreach($result as $items)
        {
            $items['charges'] = $this->get_charges($items['tag_id']);
            $returnData[]=$items;
        }



		return $returnData;
    }

    function advance_details_order_no($orderno,$id_branch,$fin_year_code)
	{
	    $return_data=array();
	    if($orderno!=null && $orderno!='')
	    {
	       $advance=$this->db->query("SELECT IFNULL(SUM(a.advance_amount),0) as paid_advance
            FROM customerorder c
            LEFT JOIN ret_billing_advance a ON a.id_customerorder=c.id_customerorder
            LEFT JOIN ret_billing b ON b.bill_id=a.bill_id
            LEFT JOIN ret_bill_old_metal_sale_details s ON s.old_metal_sale_id=a.old_metal_sale_id
			where c.fin_year_code=".$fin_year_code." and a.is_adavnce_adjusted=0 and b.bill_status=1 and a.order_no='".$orderno."'
			".($id_branch!='' ? " and b.id_branch=".$id_branch."" :'')."");
			//print_r($this->db->last_query());exit;
			return $advance->row()->paid_advance;
	    }
	}

	function getFinancialYear()
	{
		$sql=$this->db->query("SELECT fin_year_code,fin_status,fin_year_name From ret_financial_year WHERE fin_status = 1");
		return $sql->result_array();
	}

	 public function importOldTagData()
    {
        $dataset = [];
        $query = $this->db->query("SELECT TagNumber, BranchId, TagId, counterid FROM tag_out WHERE BranchId IS NOT NULL");
        foreach($query->result() as $outtag){
            $data = array(
			            'import_tag_code' =>  $outtag->TagNumber,
						'tran_no'   => $outtag->TagNumber,
						'import_date'  => date('Y-m-d H:i:s'),
						'import_branch'	=> $outtag->BranchId,
						'import_tag_status' => 1,
						'import_by' => 1,
						'TagId' => $outtag->TagId,
						'counterid' => $outtag->counterid
						);
			array_push($dataset,$data);
        }
        foreach($dataset as $value)
		{
				if($value['import_tag_code'] != '' || $value['import_tag_code'] != null)
				{
				    $insert_data = array(
			            'import_tag_code' =>  $value['import_tag_code'],
						'tran_no'   => $value['import_tag_code'],
						'import_date'  => date('Y-m-d H:i:s'),
						'import_branch'	=> $value['import_branch'],
						'import_tag_status' => 1,
						'import_by' => 1
						);
				    $this->db->trans_begin();
			    	$insId = $this->insertData($insert_data,'ret_old_tag_import');
			    	if($insId)
			    	{
			    	    if($insert_data['import_tag_status']==1)
    			    	{

    			    	    if($insert_data['import_tag_code']!='')
    			    	    {
    			    	        $update_data = array('sold_from' => 2,'tag_status' => 1);
								$condtion_data = array('tag_status'  => 0, 'remarks' => ''.$insert_data['import_tag_code'].'');
    				            $status = $this->old_updateData($update_data,$condtion_data,'ret_taging');
    				           // print_r($this->db->last_query());
    				            if($status)
    				            {
    				                 $insert_log_data = array('tag_id'  => $value['TagId'],
    				                     'status'      => 9,
    									 'from_branch' => $value['import_branch'],
    									 'to_branch'   => NULL,
    									 'date'        => date('Y-m-d H:i:s'),
    									 'created_on'  => date('Y-m-d H:i:s'),
                                         'created_by'  => 1);
    				                    $this->insertData($insert_log_data,'ret_taging_status_log');


    				                    //Update tag counter
    				                    //$counter = $this->$model->getTagCounter($insert_data['import_tag_code']);
    				                    if(!empty($value['counterid'])){
    				                     $seclog_data = array(
                	 					    	'tag_id'	  => $value['TagId'],
                	 					    	'date'		  => date('Y-m-d H:i:s'),
                	 					    	'status'	  => 9,
                	 					    	'from_branch' => $value['import_branch'],
                	 					    	'to_branch'	  => NULL,
                	 					    	'from_section'  => $value['counterid'],
                	 					    	'to_section'    => NULL,
                	 					    	'created_on'  => date("Y-m-d H:i:s"),
                								'created_by'  => 1,
                								'issuspensestock' => 0
                	 					    );
                	 					    $this->insertData($seclog_data,'ret_section_tag_status_log');
                	 					    unset($insert_log_data);
    				                    }


    				            }else{
    				                $return_data=array('staus'=>FALSE,'message'=>'Unable to Proceed','last_query'=>$this->db->last_query());
    				            }
    			    	    }
    			    	}
			    	}

				}

		}

	    return array("status" => TRUE, "message" => "Uploaded successfully");

    }

    function check_old_tag_code($old_tag_code){
        $sql=$this->db->query("SELECT import_tag_code from ret_old_tag_import where import_tag_code = '".$old_tag_code."'");
		//print_r($this->db->last_query());
        return $sql->num_rows() > 0 ? "" : $old_tag_code;
    }
	function check_old_tag_code_mismatched($old_tag_code){
        $sql=$this->db->query("SELECT remarks as old_tag_id from ret_taging where tag_status = '0' and old_tag_id = '".$old_tag_code."'");
		//print_r($this->db->last_query());
        return $sql->num_rows() > 0 ? "" : $old_tag_code;
    }

	function old_updateData($update_data,$condtion_data,$table)
	{
			$this->db->where($condtion_data);
			$add_flag= $this->db->update($table,$update_data);
			return $add_flag;
	}

	function get_our_tag_code($old_tag_code){
        $sql=$this->db->query("SELECT tag_id from ret_taging where remarks = '".$old_tag_code."'");
        return $sql->row()->tag_id;
    }

    function getAllActiveMetalDetails(){
        $sql=$this->db->query("SELECT id_metal,metal,metal_code From metal where metal_status = 1");
		return $sql->result_array();
    }

    function getAllActiveMetalProductDetails($metalid){
        $sql=$this->db->query("SELECT pro_id, cat_id, product_short_code, product_name , cat.name as category, cat.scheme_closure_benefit

                        FROM `ret_product_master` as pr
                        LEFT JOIN ret_category as cat ON cat.id_ret_category = pr.cat_id
                        WHERE cat.id_metal = '".$metalid."' AND pr.product_status = 1");
        return $sql->result_array();
    }

    function getAllActiveProductDesignDetails($proid){
       	$sql = $this->db->query('SELECT m.pro_id as id_product, m.id_design, p.product_name, des.design_name,
										des.design_code
										FROM ret_product_mapping m
										LEFT JOIN ret_product_master p ON p.pro_id=m.pro_id
										LEFT JOIN ret_design_master des ON des.design_no=m.id_design
										WHERE m.pro_id = "'.$proid.'"');
        return $sql->result_array();
    }

    function getAllItemMatchingListDetails($metalid, $proid, $desid){
         $sql = $this->db->query("SELECT `tag_id`, `tag_code`, `piece`, `net_wt`, `gross_wt`, ifnull(`rfidtag`, '') as rfidtag,
                                (`gross_wt` + `rfidtagwt`) as totalwt, `rfidtagwt` as extrawt FROM `ret_taging`
                                WHERE  `product_id` = '". $proid ."'
                                ".($desid!='' ? " and design_id =".$desid."" :'')."");
        return $sql->result_array();
    }

    function updateRFIDTagScan($scannedData){

        foreach($scannedData['data'] as $key => $val){
            $rfidscanned = array("rfid_scan_tag_id" => $val->tagnumber, 'rfid_scan_datetime' => $val->datetime);
            $this->db->insert("ret_tag_rfid_tag_scanned", $rfidscanned);
        }
        return array("status" => TRUE, "message" => "Data inserted successfully.");
    }


	function get_supplier_items($data,$type) {

		$resultArr = array();

		$page_no 		= isset($data['page_no']) && $data['page_no'] > 0 ? $data['page_no'] - 1 : 0;

		$MetalCode 		= isset($data['MetalCode']) ? $data['MetalCode'] : 0;

		$cat_id 		= isset($data['id_category']) ? $data['id_category'] : 0;

		$product_id 	= isset($data['id_product']) ? $data['id_product'] : 0;

		$design_id 		= isset($data['design_id']) ? $data['design_id'] : 0;

		$id_sub_design 	= isset($data['id_sub_design']) ? $data['id_sub_design'] : 0;

		$this->db->select('

						p.metal_type,

						m.metal,

						cat.cat_code,

						cat.id_ret_category,

						cat.name AS cat_name,

						cat.scheme_closure_benefit,

						t.product_id,

						p.product_name,

						p.product_short_code,

						p.product_status,

						p.calculation_based_on,

						p.tax_group_id,

						p.tax_type,

						t.design_id,

						des.design_name,

						des.design_code,

						t.id_sub_design,

						sdes.sub_design_name,

						catimg.image,

						catimg.id_supp_cat_img'

					);

					$this->db->from('ret_supp_catalogue t');

					$this->db->join('ret_supp_catalogue_weight suppwt', 'suppwt.id_supp_catalogue = t.id_supp_catalogue', 'left');

					$this->db->join('supp_catalogue_images catimg', 'catimg.id_supp_catalogue = t.id_supp_catalogue  AND catimg.is_default=1', 'left');

					$this->db->join('ret_product_master p', 'p.pro_id = t.product_id', 'left');

					$this->db->join('ret_design_master des', 'des.design_no = t.design_id', 'left');

					$this->db->join('ret_sub_design_master sdes', 'sdes.id_sub_design = t.id_sub_design', 'left');

					$this->db->join('ret_category cat', 'cat.id_ret_category = p.cat_id', 'left');

					$this->db->join('metal m', 'm.id_metal = p.metal_type', 'left');

					if($MetalCode > 0) {

						$this->db->where('p.metal_type', $MetalCode);

					}

					if($cat_id > 0) {

						$this->db->where('p.cat_id', $cat_id);

					}

					if($product_id > 0) {

						$this->db->where('t.product_id', $product_id);

					}

					if($design_id > 0) {

						$this->db->where('t.design_id', $design_id);

					}

					if($id_sub_design > 0) {

						$this->db->where('t.id_sub_design', $id_sub_design);

					}

					if($data['type'] == 'mt') {

						$this->db->group_by('p.metal_type');

					}else if($data['type'] == 'cat') {

						$this->db->group_by('p.cat_id');

					} else if($data['type'] == 'pr') {

						$this->db->group_by('t.product_id');

					} else if($data['type'] == 'ds') {

						$this->db->group_by('t.design_id');

					} else if($data['type'] == 'sd') {

						$this->db->group_by('t.id_sub_design');

					}

					if($type != "getCount") {

						$this->db->limit(10, ($page_no*10));

					}

					$query = $this->db->get();

					//echo $this->db->last_query();exit;

					if($query)  {

						if ( $query->num_rows() > 0 ) {

							$resultData = $query->result_array();

							foreach($resultData as $row) {

								if($data['type'] == 'cat') {

									$result['cat_code'] = $row['cat_code'];

									$result['id_ret_category'] = $row['id_ret_category'];

									$result['metal'] = $row['metal'];

									$result['name'] = $row['cat_name'];

								} else if($data['type'] == 'pr') {

									$result['calculation_based_on'] = $row['calculation_based_on'];

									$result['cat_id'] = $row['id_ret_category'];

									$result['category'] = $row['cat_name'];

									$result['label'] = $row['product_name'];

									$result['metal_type'] = $row['metal_type'];

									$result['pro_id'] = $row['product_id'];

									$result['product_name'] = $row['product_name'];

									$result['product_short_code'] = $row['product_short_code'];

									$result['product_status'] = $row['cat_code'];

									$result['scheme_closure_benefit'] = $row['scheme_closure_benefit'];

									$result['tax_group_id'] = $row['tax_group_id'];

									$result['tax_type'] = $row['tax_type'];

								} else if($data['type'] == 'ds') {

									$result['design_code'] = $row['design_code'];

									$result['design_name'] = $row['design_name'];

									$result['id_design'] = $row['design_id'];

									$result['label'] = $row['design_name'];

									$result['product_name'] = $row['product_name'];

								}

								$file = self::SUPPIMG_PATH.$row['id_supp_cat_img']."-".$row['image'];

								$supplier_cat_img = (file_exists($file)? base_url().$file : null );

								$result['image'] = $supplier_cat_img;

								$resultArr[] = $result;

							}

						}

					}

					return  $resultArr;
	}

	function get_sections($id_branch) {

		$section_filtered = array();

		$CI = & get_instance();

		$data['id_branch'] = $id_branch;

		$sections = $CI->ret_estimation_model->get_sectionBranchwise($data);

		foreach($sections as $sec) {

			array_push($section_filtered ,array( "id_section" => $sec['id_section'], "section_name" => $sec['section_name'], "short_code" => $sec['short_code'], "is_home_bill_counter" => $sec['is_home_bill_counter']));

		}

		$result = array_values(array_unique($section_filtered, SORT_REGULAR));

		return $result;

	}

	function check_device_exists($username, $device_id, $device_type, $user_agent) {

		$current_datetime = date('Y-m-d H:i:s');

		$message = "";

		$status = false;

		$add_device = true;

		$emp_id = 0;

		if($username != "" && $device_id != "") {

			$emp = $this->db->query("SELECT e.id_employee, e.is_lmx, IFNULL(ed.id_collection_device, 0) AS id_collection_device, ed.device_uuid, ed.app_type, ed.device_uuid, ed.device_status  FROM employee e LEFT JOIN employee_devices ed ON ed.emp_id = e.id_employee WHERE username ='".$username."'");

			if($emp->num_rows() > 0) {

				$emp_result = $emp->result_array();

				foreach($emp_result as $emp) {

					$emp_id = $emp['id_employee'];

					if($emp['is_lmx'] == 1) {

						$message = "Device exists.";

						$status = true;

						$add_device = false;

						break;

					} else {

						if($emp['app_type'] == 2) {

							if($emp['id_collection_device'] > 0 && $emp['device_uuid'] == $device_id) {

								if($emp['device_status'] == 1) {

									$message = "Device exists.";

									$status = true;

								} else {

									$message = "This device is currently disabled. Please contact the admin to enable";

									$status = false;

								}

								$add_device = false;

								break;

							}

						}

					}

					}

			} else {

				$message = "Employee does not exists.";

				$status = false;

				$add_device = false;

				}

		} else {

			$message = "Username or device ID is missing";

			$status = false;

			$add_device = false;

			}

		if($add_device && $emp_id > 0) {

			$message = "This device is currently disabled. Please contact the admin to enable";

			$status = false;

			$dev_arr = array(

				'device_uuid' => $device_id,

				'device_type' => $device_type,

				'date_add'    => $current_datetime,

				'emp_id'	  => $emp_id,

				'app_type'	  => 2,

				'device_info' => $user_agent,

				'device_status' => 0

			);

			$this->db->insert("employee_devices",$dev_arr);

		}

		return array("message" => $message, "status" => $status);

	}

	function get_tag_other_metal_data($tagid)
    {
        $sql = $this->db->query("SELECT
									om.tag_other_itm_id,
									om.tag_other_itm_tag_id,
									cat.id_metal,
									om.tag_other_itm_metal_id,
									om.tag_other_itm_pur_id,
									om.tag_other_itm_grs_weight,
									om.tag_other_itm_wastage,
									om.tag_other_itm_uom,
									om.tag_other_itm_cal_type,
									om.tag_other_itm_mc,
									om.tag_other_itm_pcs,
									mt.metal,
									pur.purity AS pur_name
								FROM ret_tag_other_metals om
								LEFT JOIN ret_category AS cat ON cat.id_ret_category = om.tag_other_itm_metal_id
								LEFT JOIN metal AS mt ON mt.id_metal = cat.id_metal
								LEFT JOIN ret_purity AS pur ON pur.id_purity = om.tag_other_itm_pur_id
								WHERE tag_other_itm_tag_id = $tagid"
							);

		return $sql->result_array();
	}

	function get_other_metal_details($other_metal_record)
    {
		$otherMetals = array();

		$other_metal_total_price = 0;

		$other_metal_total_wt = 0;

		$rates 	= $this->get_rates();

		$rate_fields = $this->get_metal_rate_fields();

		if(count($other_metal_record) > 0) {

		    foreach($other_metal_record as $record) {

				$id_metal = $record['id_metal'];

				$tag_other_itm_pur_id = $record['tag_other_itm_pur_id'];

				$rate_field = $this->get_ratefield($tag_other_itm_pur_id, $id_metal, $rate_fields);

				$rate_per_gram = $rates[$rate_field];

				$tag_other_itm_grs_weight = 	$record['tag_other_itm_grs_weight'];

			    $tag_other_itm_wastage    = 	$record['tag_other_itm_wastage'];

			    $wast_wt         		  =  	($tag_other_itm_grs_weight * $tag_other_itm_wastage)/100;

			   	$tag_other_itm_cal_type   = 	$record['tag_other_itm_cal_type'];

			   	$tag_other_itm_mc   	  = 	$record['tag_other_itm_mc'];

			    $mc_value        		  =     ($tag_other_itm_cal_type == 1 ? ($tag_other_itm_grs_weight * $tag_other_itm_mc) : ($tag_other_itm_cal_type==2 ? $tag_other_itm_mc :0));

			    $total_amount    		  = 	round($rate_per_gram*($tag_other_itm_grs_weight + $wast_wt + $mc_value),2);

				$other_metal_total_wt += $tag_other_itm_grs_weight;

			    $other_metal_total_price += $total_amount;

				$otherMetals[] = [
							'tag_other_itm_id' 			=> $record['tag_other_itm_id'],
							'tag_other_itm_tag_id' 		=> $record['tag_other_itm_tag_id'],
							'id_metal' 					=> $id_metal,
							'metal_name'				=> $record['metal'],
							'tag_other_itm_metal_id' 	=> $record['tag_other_itm_metal_id'],
							'tag_other_itm_pur_id' 		=> $tag_other_itm_pur_id,
							'tag_other_itm_pur_name' 	=> $record['pur_name'],
							'tag_other_itm_grs_weight' 	=> $tag_other_itm_grs_weight,
							'tag_other_itm_wastage' 	=> $tag_other_itm_wastage,
							'tag_other_itm_uom' 		=> $record['tag_other_itm_uom'],
							'tag_other_itm_cal_type' 	=> $tag_other_itm_cal_type,
							'tag_other_itm_mc' 			=> $tag_other_itm_mc,
							'tag_other_total_mc'		=> $mc_value,
							'tag_other_itm_rate' 		=> $rate_per_gram,
							'tag_other_itm_pcs' 		=> $record['tag_other_itm_pcs'],
							'tag_other_itm_amount' 		=> round($total_amount,2)
				];
			}

		}

		return array("otherMetals" => $otherMetals, "other_metal_total_wt" => round($other_metal_total_wt,3), "other_metal_total_price" => round($other_metal_total_price,2));

    }


	//Mobile App Approval Starts Here

	function getApprovallists($post){
		$approval_type=$post['approval_type'];
		$id_branchv=$post['id_branch'];
		$report_type=$post['report_type'];

		if($report_type== 2){
			$from_date = $post['from_date'];
			$to_date = $post['to_date'];
			$datefilter = 'and date(a.apprl_requested_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"';
		}else{
			$datefilter =  "AND apprl_status=0  and date(now()) = date(a.apprl_requested_date)";
		}

		$return_data = array();
		$sales_details = array();
		$purchase_details = array();

		$sql =  $this->db->query("SELECT IFNULL(apprl_bill_discount,0)  AS bill_discount,IFNULL(apprl_bt_id,'') as apprl_bt_id,
		IFNULL(apprl_disp_message,'') as apprl_disp_message,IFNULL(apprl_tot_bill_amount,'') as apprl_tot_bill_amount,IFNULL(apprl_cr_paid_amt,'') as apprl_cr_paid_amt,apprl_status,CONCAT(e.firstname,' ',e.lastname) as apprl_requested_by,a.apprl_id as approval_id,IFNULL(a.apprl_cus_id,'') as id_customer,IFNULL(CONCAT(c.firstname,' ',IFNULL(c.lastname,'')),'') as cus_name,IFNULL(c.mobile,'') as cus_mobile,IFNULL(a.apprl_esti_id,'') as apprl_esti_id,
		IFNULL(a.apprl_bt_code,'') as apprl_bt_code,IFNULL(e.mobile,'') as emp_mobile,IFNULL(a.is_disc_modified,0) as is_disc_modified,IFNULL(a.items,'') as items,b.name as branch_name, b.short_name as brn_short_name,IFNULL(a.is_credit,'-') as is_credit

		, IF(a.transfer_item_type=1,'Tagged',(IF(a.transfer_item_type=2,'Non-Tagged',(IF(a.transfer_item_type=3,'Purchase',(IF(a.transfer_item_type=4,'Packaging Items',(IF(a.transfer_item_type=5,'Repair Order','-'))))))))) as transfer_item_type ,

		IF(a.Bt_approval_type=1,'Intransist',(IF(a.Bt_approval_type=2,'Stock Download','-'))) as Bt_approval_type,a.bt_net_wt,a.bt_gross_wt,a.bt_dia_wt,

		IF(apprl_status=0,'Waiting For Approval',IF(apprl_status=1,'Approved',IF(apprl_status=2,' Rejected',IF(apprl_status=3,'Cancelled',IF(apprl_status=4,'Expired',''))))) as apprl_status_name,

		IFNULL(date_format(a.apprl_approved_on,'%d-%m-%Y %r'),'-') as approved_on,IFNULL(emp.firstname,'-') as approved_by,date_format(a.apprl_requested_date,'%d-%m-%Y %r') as req_time,IFNULL(a.disc_amt,IFNULL(apprl_bill_discount,0)) as Approved_discount

		 FROM ret_admin_approval_status a
		 LEFT JOIN employee e on e.id_employee = a.apprl_requested_by
		 LEFT JOIN employee emp on emp.id_employee = a.apprl_approved_by
		 LEFT JOIN customer c on c.id_customer = a.apprl_cus_id
		 LEFT JOIN branch b on b.id_branch = a.id_branch
		 WHERE apprl_type=".($approval_type)." ".($datefilter)."
		 " . ($id_branch != '' && $id_branch > 0? " and a.id_branch='" . $id_branch . "'" : '') . "
		 ORDER BY a.apprl_id DESC");
		//  print_r($this->db->last_query());exit;
		 $data = $sql->result_array();
		 foreach ($data as $key => $items) {
			$id_estimations = [];
			$bt_codes = [];
			$chit_data = [];
            if($items['items'] != ''){
				$item_data = json_decode($items['items']);
				$sales_details = $item_data[0]->Sales;
				$purchase_details = $item_data[0]->Purchase;
				$chit_details = $item_data[0]->chit_details;
				$profit_amount = $item_data[0]->profit_amount;
				$profit_percentage = $item_data[0]->profit_percentage;
				$credit_due_date = $item_data[0]->credit_due_date;
			}

			//print_r($item_data);exit;

			if($items['apprl_esti_id']!=''){
				$exploded = explode(',',$items['apprl_esti_id']);

				foreach($exploded as $est_id){
					$id_estimations[] = ['esti_id' => $est_id];
				}
			}

			if($items['apprl_bt_code']!=''){ //For Getting BT Trans Code
				$apprl_bt_code = explode(',',$items['apprl_bt_code']);

				foreach($apprl_bt_code as $code){
					$bt_codes[] = ['trans_code' => $code];
				}
			}

			if($items['apprl_esti_id']!=''){  //For Getting Chit Details
				$est_ids = explode(',',$items['apprl_esti_id']);
				$closing_weight = 0;
				$closing_amount = 0;
				foreach($est_ids as $est_id){
					// $est_det =  $this->get_estimation_det($est_id);
					// $chit_data[] =  $this->get_chit_details($est_det['esti_no'],$est_det['id_branch'],$est_det['est_date']);
					// foreach ($chit_data as $key => $closing_data) {
					// 	foreach($closing_data as $det){
					// 		$closing_weight += $det['closing_weight'];
					// 		$closing_amount += $det['closing_amount'];
					// 	}
					// }
				}

			}



				$return_data[] = [
					'bill_discount'         =>  $items['bill_discount'],
					'apprl_bt_id'           => $items['apprl_bt_id'],
					'apprl_cr_paid_amt'     => $items['apprl_cr_paid_amt'],
					'apprl_disp_message'    => $items['apprl_disp_message'],
					'apprl_tot_bill_amount' => $items['apprl_tot_bill_amount'],
					'apprl_status'          => $items['apprl_status'],
					'apprl_requested_by'    => $items['apprl_requested_by'],
					'approval_id'           => $items['approval_id'],
					'id_customer'           => $items['id_customer'],
					'cus_name'              => $items['cus_name'],
					'cus_mobile'            => $items['cus_mobile'],
					'emp_mobile'            => $items['emp_mobile'],
					'is_disc_modified'      => $items['is_disc_modified'],
					'branch_name'      		=> $items['branch_name'],
					'brn_short_name'    	=> $items['brn_short_name'],
					'is_credit'    	        => $items['is_credit'],
					'Bt_approval_type'    	=> $items['Bt_approval_type'],
					'transfer_item_type'    => $items['transfer_item_type'],
					'bt_gross_wt'   		=> $items['bt_gross_wt'],
					'bt_net_wt'    			=> $items['bt_net_wt'],
					'bt_dia_wt'    			=> $items['bt_dia_wt'],
					'apprl_status_name'    			=> $items['apprl_status_name'],
					'req_time'    			=> $items['req_time'],
					'approved_by'    			=> $items['approved_by'],
					'approved_on'    			=> $items['approved_on'],
					'approved_discount'    			=>  $items['Approved_discount'],
					'timer'    			=> $timer,


					'bt_details'            => (($items['apprl_bt_id']!='') ? $this->get_bt_details($items['apprl_bt_id']): ''),
					'trans_code'            => ($items['apprl_bt_id']!='' ? $this->get_trans_code($items['apprl_bt_id']): ''),
					'estids'                => ($id_estimations),
					'bt_codes'              => ($bt_codes),
					'chit_data'             => ($chit_data),
					'closing_weight'        => ($closing_weight),
					'closing_amount'        => ($closing_amount),
					'sales_details'         => ($sales_details),
					'purchase_details'      => ($purchase_details),
					'chit_details'          => ($chit_details),
					'profit_percentage'     => ($profit_percentage),
					'profit_amount'         => ($profit_amount),
					'credit_due_date'         => $credit_due_date,

				];

				// print_r($return_data);exit;

		 }
		return $return_data;

	}


	function getApprovaldetails($apprl_id){

		$sql = $this->db->query("SELECT apprl_bill_discount AS bill_discount,apprl_bt_id,CONCAT(e.firstname,' ',e.lastname) as apprl_requested_by,apprl_disp_message,apprl_tot_bill_amount,apprl_cr_paid_amt,apprl_status,items as item_details
		FROM ret_admin_approval_status a
		LEFT JOIN employee e on e.id_employee = a.apprl_requested_by
		WHERE apprl_id=".($apprl_id)."
		");
		return $sql->result_array();

	}


	function get_trans_code($trans_id)
	{
		$apprl_bt_code = explode(',',$trans_id);
		$code = [];
		foreach ($apprl_bt_code as $key => $id) {
			$sql = $this->db->query("SELECT transfer_item_type as s_type,branch_trans_code as trans_code,date_format(created_time,'%d/%m/%Y') as bt_date
			FROM ret_branch_transfer where branch_transfer_id=".($id)."");
			$code[]= $sql->row_array();
		}

		return $code;

	}

	function get_bt_details($bt_id)
	{
		$trans_id = explode(',',$bt_id);
		$data = array();
		$trans_codes = '';
		$net_wt = 0;
		$grs_wt = 0;


		// print_r($trans_id);exit;

		foreach ($trans_id as $key => $id) {
			$sql = $this->db->query("SELECT b.transfer_item_type as s_type,b.branch_trans_code as trans_code,date_format(created_time,'%d/%m/%Y') as bt_date ,fb.name as from_branch, tb.name as to_branch,b.net_wt,b.grs_wt
			FROM ret_branch_transfer b
			LEFT JOIN branch  fb on fb.id_branch = b.transfer_from_branch
			LEFT JOIN branch  tb on tb.id_branch = b.transfer_to_branch
			 where branch_transfer_id=".($id)."");
			$data[]= $sql->row_array();
		}
		// print_r($this->db->last_query());exit;

		foreach ($data as $key => $code) {
			$trans_codes =$trans_codes.$code['trans_code'].',';
			$net_wt += $code['net_wt'];
			$grs_wt += $code['grs_wt'];

			$data = $code;

		}

		if (substr($trans_codes, -1) === ',') {
			$trans_codes = substr($trans_codes, 0, -1);
		}
		$return_data = [
			'to_branch'   		=> $data['to_branch'],
			'from_branch'		=> $data['from_branch'],
			's_type'      		=> $data['s_type'],
			'trans_code' 		=> $trans_codes,
			'net_weight' 		=> $data['net_wt'],
			'grs_wt' 		=> $data['grs_wt'],
			'net_wt'      		=> number_format($net_wt, 3, '.', ''),

		];

		return $return_data;


	}


	function get_approval_status($approval_id)
	{
		$this->db->select('apprl_id,id_branch,disc_amt,apprl_status,apprl_bill_discount,apprl_type,apprl_approved_by,is_disc_modified');
		$this->db->where('apprl_id',$approval_id);
		$this->db->from('ret_admin_approval_status');


		return $this->db->get()->row_array();
	}


	function get_estimation_det($est_id)
	{

		$data =  $this->db->query("SELECT esti_no,id_branch,date_format(estimation_datetime,'%Y-%m-%d') as est_date FROM ret_estimation where estimation_id=".($est_id)."");
		return $data->row_array();

	}

	function get_chit_details($est_no,$id_branch,$date)
	{

		$est_chit_query = $this->db->query("SELECT chit_ut_id, est_id,scheme_account_id, utl_amount,concat(s.code,'',sa.scheme_acc_number) as scheme_acc_number,s.scheme_type,sa.closing_balance,
		IFNULL(est_chit.closing_weight,0) as closing_weight,IFNULL(est_chit.wastage_per,0) as wastage_per,IFNULL(est_chit.savings_in_wastage,0) as savings_in_wastage,
		IFNULL(est_chit.mc_value,0) as mc_value,IFNULL(est_chit.savings_in_making_charge,0) as savings_in_making_charge,s.total_installments,pay.paid_installments,sa.closing_amount as closing_amount,
		s.is_wast_and_mc_benefit_apply,sa.additional_benefits,sa.closing_add_chgs
        FROM ret_est_chit_utilization as est_chit
        LEFT JOIN scheme_account sa ON sa.id_scheme_account=est_chit.scheme_account_id
        LEFT JOIN scheme s ON s.id_scheme=sa.id_scheme
        LEFT JOIN ret_estimation e ON e.estimation_id=est_chit.est_id
        LEFT JOIN (select SUM(p.payment_amount) as paid_amount, IFNULL(cp.cash_pay,0) as cash_pay,sa.id_scheme_account,
        IFNULL(IF(sa.is_opening = 1,
        IFNULL(sa.paid_installments,0) + IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0),
        if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0) as paid_installments
        FROM payment p
        LEFT JOIN (SELECT SUM(IFNULL(pmd.payment_amount,0)) AS cash_pay, id_payment FROM `payment_mode_details` AS pmd WHERE pmd.payment_mode = 'CSH' AND pmd.payment_status = 1 GROUP BY id_payment) AS cp ON cp.id_payment = p.id_payment
        left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
        left join scheme s on s.id_scheme=sa.id_scheme
        where p.payment_status=1 GROUP BY p.id_scheme_account) as pay on pay.id_scheme_account = sa.id_scheme_account
        WHERE e.esti_no = '" . $est_no . "' AND date(e.estimation_datetime)='".($date)."'
        " . ($id_branch != '' ? " and e.id_branch='" . $id_branch . "'" : '') . "");
		return $est_chit_query->result_array();

	}

	function getCustomerDet($id_branch='',$id_customer)
	{
	    $return_data=array();

		$outstanding = $this->get_credit_pending_details($id_branch, $id_customer);
        foreach ($outstanding as $key => $value) {
            $credit_col = 0;
            foreach ($value['credit_collection'] as $key => $cc_amt) {
                $credit_col += $cc_amt['tot_amt_received'];
            }
           $return_data['bal_amt'] += $value['bal_amt'];
           $return_data['tot_bill_amount'] += $value['tot_bill_amount'];
           $return_data['credit_collection'] += $credit_col;
        }
        return $return_data['bal_amt'];
	}
	function get_credit_pending_details($id_branch, $id_customer)
	{
		$return_data = array();
		$credit_detail = array();
		$credit_detai2 = array();
		$sql = $this->db->query("SELECT b.tot_amt_received,b.bill_id,b.bill_no,DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date,DATE_FORMAT(b.credit_due_date,'%d-%m-%Y') as credit_due_date,b.tot_bill_amount,b.tot_amt_received,b.bill_cus_id,c.mobile,c.firstname as cus_name,
		if(b.credit_status=1,'Paid','Pending') as credit_status,br.name as branch_name,b.tot_bill_amount,(b.tot_bill_amount-b.tot_amt_received-IFNULL(ret.credit_due_amt,0)) as bal_amt,b.credit_disc_amt,IFNULL(ret.credit_due_amt,0) as credit_due_amt,IFNULL(ret.credit_ret_amt,0) as credit_ret_amt
			from ret_billing b
			LEFT JOIN customer c on c.id_customer=b.bill_cus_id
			LEFT JOIN branch br on br.id_branch=b.id_branch
            LEFT JOIN(SELECT IFNULL((b.credit_due_amt),0) as credit_due_amt,
            r.ret_bill_id,IFNULL(b.credit_ret_amt,0) as credit_ret_amt
            FROM ret_bill_return_details r
            LEFT JOIN ret_billing b ON b.bill_id = r.bill_id
            WHERE b.bill_status = 1
            GROUP BY r.ret_bill_id) as ret ON ret.ret_bill_id = b.bill_id
			where  b.bill_id is not null and b.is_credit=1 and b.is_to_be=0 and b.bill_status=1  and b.bill_type!=8 and b.credit_status=2 and b.bill_type!=12
			" . ($id_branch != '' && $id_branch > 0 ? ' and b.id_branch=' . $id_branch : '') . "
			" . ($id_customer != '' && $id_customer > 0 ? ' and b.bill_cus_id=' . $id_customer : '') . "
			ORDER BY b.bill_cus_id");
		// print_r($this->db->last_query());exit;
		$result = $sql->result_array();
		foreach ($result as $r) {
			$paid_amount = $this->get_credit_collection_details($r['bill_id']);
			// print_r($paid_amount);exit;
			$credit_detail[] = array(
				'type'              => 0,
				'bill_no'           => $r['bill_no'],
				'bill_date'         => $r['bill_date'],
				'cus_name'          => $r['cus_name'],
				'mobile'            => $r['mobile'],
				'branch_name'       => $r['branch_name'],
				'tot_bill_amount'   => $r['tot_bill_amount'],
				'credit_due_amt'    => $r['credit_due_amt'],
				'credit_ret_amt'    => $r['credit_ret_amt'],
				'tot_amt_received'    => $r['tot_amt_received'],
				'bal_amt'           => $r['bal_amt'] - $paid_amount,
				'due_amount'        => $r['tot_bill_amount'] - $r['tot_amt_received'],
				'paid_amount'       => $paid_amount,
				'bill_id'           => $r['bill_id'],
				'credit_collection' => $this->getCreditCollection($r['bill_id'])
			);
		}
		$issue_sql = $this->db->query("SELECT r.id_issue_receipt as bill_id,r.bill_no as bill_no,cus.mobile,r.amount as due_amount,
        DATE_FORMAT(r.bill_date,'%d-%m-%Y') as bill_date,'' as credit_due_date,IFNULL(r.amount-IFNULL(coll.paid_amt,0),0) as bal_amt,r.amount as tot_amt_received,
        IFNULL(coll.paid_amt,0) as paid_amount,cus.firstname as cus_name,br.name as branch_name,'1' as type,'0' as credit_ret_amt,r.amount as tot_bill_amount
        FROM ret_issue_receipt r
        LEFT JOIN branch br on br.id_branch=r.id_branch
        LEFT JOIN (SELECT IFNULL(SUM(c.received_amount+c.discount_amt),0) as paid_amt,c.receipt_for
        FROM ret_issue_receipt r
        LEFT JOIN ret_issue_credit_collection_details c ON c.id_issue_receipt=r.id_issue_receipt
        where r.bill_status=1
        GROUP by c.receipt_for) as coll ON coll.receipt_for=r.id_issue_receipt
        LEFT JOIN customer cus ON cus.id_customer=r.id_customer
        WHERE r.type=1  and r.bill_status=1 and (r.issue_type=2 or r.issue_type=4)
        " . ($id_customer != '' && $id_customer > 0 ? ' and r.id_customer=' . $id_customer : '') . "
        " . ($id_branch != '' && $id_branch > 0 ? ' and r.id_branch=' . $id_branch : '') . "
          ");
		//print_r($this->db->last_query());exit;
		$result1 = $issue_sql->result_array();
		foreach ($result1 as $r) {
			$issueCreditDetails = $this->get_IssueCreditCollectionDetails($r['bill_id']);
			$r['credit_collection'] = $issueCreditDetails;
			$credit_detai2[] = $r;
		}
		//echo "<pre>"; print_r($credit_detai2);exit;
		$return_data = array_merge($credit_detail, $credit_detai2);
		return $return_data;
	}

	function get_credit_collection_details($bill_id)
	{
		$return_data = array();
		$total_bill_amount = 0;
		$credit_disc_amt = 0;
		$data = $this->db->query("SELECT b.bill_id,b.bill_no,b.bill_type,b.ref_bill_id,b.tot_amt_received,b.credit_disc_amt,
    	b.tot_bill_amount,DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date
    	 From ret_billing b
    	 where b.bill_status=1 and b.ref_bill_id=" . $bill_id . "");
		$items = $data->result_array();
		foreach ($items as $item) {
			$total_bill_amount  += $item['tot_amt_received'];
			$credit_disc_amt    += $item['credit_disc_amt'];
			$old_metal_details  =  $this->getOld_sales_details($item['bill_id'], 8);
			$old__metal_amount  = 0;
			foreach ($old_metal_details as $old_items) {
				$old__metal_amount += $old_items['amount'];
			}
		}
		return $total_bill_amount + $old__metal_amount + $credit_disc_amt;
	}

	function getCreditCollection($bill_id)
	{
		$return_data = array();
		$data = $this->db->query("SELECT b.bill_id,b.bill_no,b.bill_type,b.ref_bill_id,b.tot_amt_received,b.credit_disc_amt,
    	b.tot_bill_amount,DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date
    	 From ret_billing b
    	 where b.bill_status=1 and b.ref_bill_id=" . $bill_id . "");
		$items = $data->result_array();
		foreach ($items as $item) {
			$old_metal_details = $this->getOld_sales_detail($item['bill_id'], 8);
			$old_metal_amount = 0;
			foreach ($old_metal_details as $old_items) {
				$old_metal_amount += $old_items['amount'];
			}
			$return_data[] = array(
				'0'                => 'type',
				'bill_no'          => $item['bill_no'],
				'bill_id'          => $item['bill_id'],
				'bill_type'        => $item['bill_type'],
				'ref_bill_id'      => $item['ref_bill_id'],
				'tot_amt_received' => $item['tot_amt_received'],
				'credit_disc_amt'  => $item['credit_disc_amt'],
				'tot_bill_amount'  => ($item['tot_bill_amount'] + $old_metal_amount),
				'bill_date'        => $item['bill_date'],
				'old_metal_amount' => $old_metal_amount,
			);
		}
		return $return_data;
	}

	function getOld_sales_detail($bill_id, $bill_type)
	{
		$old_metal_query = $this->db->query("SELECT s.old_metal_sale_id,s.bill_id,s.purpose,s.metal_type,s.item_type,s.gross_wt,s.stone_wt,s.dust_wt,s.stone_wt,s.wastage_percent,s.wast_wt,
		s.net_wt,s.rate_per_grm as rate_per_gram,s.rate as amount,s.bill_id,s.bill_discount,est_id,b.bill_no,b.pur_ref_no
		FROM ret_billing b
		LEFT join  ret_bill_old_metal_sale_details s on s.bill_id=b.bill_id
		where b.bill_status=1 and s.bill_id=" . $bill_id . "");
		//print_r($this->db->last_query());exit;
		$old_matel_details = $old_metal_query->result_array();
		return $old_matel_details;
	}
	function getOld_sales_details($bill_id, $bill_type)
	{
		$old_metal_query = $this->db->query("SELECT s.old_metal_sale_id,s.bill_id,s.purpose,s.metal_type,s.item_type,s.gross_wt,s.stone_wt,s.dust_wt,s.stone_wt,s.wastage_percent,s.wast_wt,
		s.net_wt,s.rate_per_grm as rate_per_gram,s.rate as amount,s.bill_id,s.bill_discount,est_id,b.bill_no,b.pur_ref_no
		FROM ret_billing b
		LEFT join  ret_bill_old_metal_sale_details s on s.bill_id=b.bill_id
		where b.bill_status=1 and s.bill_id=" . $bill_id . "");
		$old_matel_details = $old_metal_query->result_array();
		return $old_matel_details;
	}

	function get_IssueCreditCollectionDetails($bill_id)
	{
		$sql = $this->db->query("SELECT r.id_issue_receipt as bill_id,r.bill_no,date_format(r.bill_date,'%d-%m-%Y') as bill_date,r.amount as tot_amt_received,
        coll.discount_amt as credit_disc_amt,'0' as old_metal_amount,'1' as type
        FROM ret_issue_receipt r
        LEFT JOIN ret_issue_credit_collection_details coll ON coll.id_issue_receipt=r.id_issue_receipt
        WHERE r.receipt_type=1 and r.bill_status=1 AND coll.receipt_for=" . $bill_id . "");
		//print_r($this->db->last_query());exit;
		return $sql->result_array();
	}



	function get_approval_rate_fix_list($post)

	{
		$from_date =$post['from_date'];
		$to_date = $post['to_date'];
		if(empty($from_date)){
			$from_date  = date('Y-m-d');
			$to_date  = date('Y-m-d');
		}

	    $sql=$this->db->query("SELECT  '4' as approved_type,rate_fix_id, po_ref_no, date_format(rate_fix_created_on, '%d-%m-%Y %H:%i:%s') as ratefixon, rate_fix_wt,

	                            rate_fix_rate, total_amount, kr.firstname as karigar,rf.is_approved,if(rf.is_approved=0,'Yet to Approve','Approved') as approve_status,rf.bill_status,

								emp.firstname as emp_name

	                            FROM ret_po_rate_fix as rf

	                            LEFT JOIN ret_purchase_order as po ON po.po_id = rf.rate_fix_po_item_id

	                            LEFT JOIN ret_karigar as kr ON kr.id_karigar = po.po_karigar_id

								LEFT JOIN employee as emp ON emp.id_employee = rf.rate_fix_create_by

	                            WHERE 1

								".($post['report_type']==2 ? "and rf.rate_fix_type = 1 and (rf.approved_through = 2) and  (date(rf.approved_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')":"and rf.rate_fix_type = 1 and rf.bill_status = 1 and rf.is_approved=0 and (date(rf.rate_fix_created_on) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')")."

	                            order by rf.rate_fix_id DESC");



	 //  echo $this->db->last_query();exit;

        return $sql->result_array();

	}

	function get_ratefix_status($rate_fix_id){

		$sql =$this->db->query("SELECT IF(rf.is_approved =0 and bill_status = 1,0,1) as is_approved FROM ret_po_rate_fix as rf

		where  rf.rate_fix_id = '".$rate_fix_id."'");

		//print_r($sql->row_array());exit;

		return $sql->row_array()['is_approved'];

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



		ifnull(cat.name,'All Categories') as cat_name,ifnull(pro.product_name,'All Produts') as product_name,ifnull(des.design_name,'All Designs') as design_name,ifnull(subdes.sub_design_name,'All Sub Designs') as sub_design_name,

		kw.status,if(kw.status =0,'Yet to Approve',if(kw.status =1,'Approved',if(kw.status =2,'Rejected','Hold'))) as wastage_status,ifnull(kar.company,'') as karigar_company,ifnull(kw.uom_id,'') as uom_id,ifnull(kw.quality_id,'') as quality_id,ifnull(kw.from_wt,0) as from_wt,ifnull(kw.to_wt,0) as to_wt,ifnull(kw.rate_per_gram,0) as rate_per_gram,kw.stone_cal_type,
		ifnull(pro.stone_type,0) as stone_type,IF(kw.karigar_calc_type =1,'Pure Weight x Rate',IF(kw.karigar_calc_type =2,'Purchase Touch','Pure Weight x Wastage %')) as karigar_calc_type_name,
		IF(kw.calc_type=0,'Mc & Wast On Gross',IF(kw.calc_type=1,'Mc & Wast On Net',IF(kw.calc_type=2,' Mc on Gross,Wast On Net',''))) as calc_type_name,IF(kw.wastage_type=1,'Percentage','Weight') as wastage_type_name,IF(kw.stone_cal_type=1,'Weight','Pcs') as stone_cal_type_name,IFNULL(uom.uom_name,'') as uom_name,
		IFNULL(qt.code,'') as quality_name,kar.id_karigar ,IFNULL(emp.firstname,'') as updated_by,IF(kw.status=1,'Approved By', IF(kw.status=2,'Rejected By',IF(kw.status=3,'Holded By',''))) as lable,
        date_format(IF(kw.status=1,kw.approved_on, IF(kw.status=2,kw.rejected_on,IF(kw.status=3,kw.holded_on,''))),'%d-%m-%Y') as updated_on,date_format(kw.created_on, '%d-%m-%Y') as created_on,
		ifnull(concat(ec.firstname,' ',IF(ec.lastname is not null, ec.lastname,'-')),'')as created_by


		FROM ret_karikar_items_wastage kw



		LEFT JOIN ret_karigar kar on kar.id_karigar=kw.id_karikar


		LEFT JOIN employee ec   ON ec.id_employee = kw.created_by



		LEFT JOIN ret_category cat on cat.id_ret_category=kw.id_category



		LEFT JOIN ret_product_master pro on pro.pro_id=kw.id_product



		LEFT JOIN ret_design_master des on des.design_no=kw.id_design



		LEFT JOIN ret_purity pur on pur.id_purity=kw.id_purity



		LEFT JOIN ret_sub_design_master subdes on subdes.id_sub_design=kw.id_sub_design

		LEFT JOIN ret_uom uom on uom.uom_id=kw.uom_id

		LEFT JOIN ret_quality_code qt on qt.quality_id=kw.quality_id

		LEFT JOIN employee emp on emp.id_employee =  IF(kw.status=1,kw.approved_by, IF(kw.status=2,kw.rejected_by,IF(kw.status=3,kw.holded_by,NULL)))

		LEFT JOIN (SELECT ki.id_karikar_wast,ki.image_name

		FROM ret_karigar_item_wast_pro_images ki

		where ki.is_default =1 ) as wast_img on wast_img.id_karikar_wast = kw.id_karikar_wast



		WHERE



		".($data['status']!="" && $data['status']==0 ? "kw.status=0" : ($data['status']!="" &&  $data['status']==3 ? "kw.status=3" : ($data['report_type']==2 ?" ( (kw.holded_through=2 AND kw.status=3)  or (kw.approved_through=2 AND kw.status=1 ) or (kw.rejected_through=2 AND kw.status=2) ) ":"kw.status=0 or kw.status=3")))."


		".($data['id_karigar']!='' && $data['id_karigar'] > 0 ? "and kw.id_karikar=".$data['id_karigar']."":'')." ORDER BY kw.id_karikar_wast DESC");



		//print_r($this->db->last_query());exit;



	    $result =  $sql->result_array();

	    $returnData = [];

	    foreach($result as $val)

	    {

	        // $val['charges_details'] = $this->get_karigar_charges($val['id_karikar_wast']);

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



		ks.status,if(ks.status =0,'Yet to Approve',if(ks.status =1,'Approved',if(ks.status =2,'Rejected','Hold'))) as stone_status,ifnull(kar.company,'') as company,iFNULL(ks.quality_id,'') as quality_id,

		IFNULL(emp.firstname,'') as updated_by,IF(ks.status=1,'Approved By', IF(ks.status=2,'Rejected By',IF(ks.status=3,'Holded By',''))) as lable,date_format(IF(ks.status=1,ks.approved_on, IF(ks.status=2,ks.rejected_on,IF(ks.status=3,ks.holded_on,''))),'%d-%m-%Y') as updated_on

		,date_format(ks.created_on, '%d-%m-%Y') as created_on


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



		LEFT JOIN employee emp on emp.id_employee =  IF(ks.status=1,ks.approved_by, IF(ks.status=2,ks.rejected_by,IF(ks.status=3,ks.holded_by,'')))



		WHERE ks.id_karigar_stone IS NOT NULL

		".($data['id_karigar']!='' && $data['id_karigar'] > 0 ? "and ks.id_karigar=".$data['id_karigar']."":"")."

		".($data['report_type'] == 2 ? " AND ( (ks.holded_through=2 AND ks.status=3)  or (ks.approved_through=2 AND ks.status=1 ) or (ks.rejected_through=2 AND ks.status=2) ) ":" and ks.status=0 or ks.status=3")."

		ORDER BY ks.id_karigar_stone DESC");



		//print_r($this->db->last_query());exit;



		return $sql->result_array();



	}

	function get_Financial_Year()
	{
		$sql=$this->db->query("SELECT fin_id,fin_year_code,fin_status,fin_year_name From ret_financial_year ORDER BY `fin_id` DESC ");
		return $sql->result_array();
	}


	function get_apprl_data()
	{
		$return_data['discount'] = $this->db->query("Select count(a.apprl_id) as discount FROM ret_admin_approval_status a where apprl_type = 1 and apprl_status=0  and date(now()) = date(a.apprl_requested_date) ")->row()->discount;

		$return_data['cr'] = $this->db->query("Select count(a.apprl_id) as discount FROM ret_admin_approval_status a where  apprl_type = 2 and apprl_status=0  and date(now()) = date(a.apprl_requested_date) ")->row()->discount;
		$return_data['Bt'] = $this->db->query("Select count(a.apprl_id) as discount FROM ret_admin_approval_status a where  apprl_type = 3 and apprl_status=0  and date(now()) = date(a.apprl_requested_date) ")->row()->discount;
		$return_data['karigar_wast'] = $this->db->query("Select count(id_karikar_wast) as discount FROM ret_karikar_items_wastage a where  status=3 or status=0 ")->row()->discount;
		$return_data['karigar_stone'] = $this->db->query("Select count(id_karigar_stone) as discount FROM ret_karigar_stones  where  status=3 or status=0 ")->row()->discount;
		$return_data['rate_fix'] = $this->db->query("Select count(rate_fix_id) as discount FROM ret_po_rate_fix  where rate_fix_type = 1 and  bill_status=1 and is_approved = 0 ")->row()->discount;
		//print_r($this->db->last_query());exit;
		return $return_data ;
	}


	function get_WastApprvKarigars($approval_for)

	{

		$sql = $this->db->query("SELECT k.id_karigar, if(karigar_type=1,ifnull(company,CONCAT(firstname,' ',IFNULL(lastname,''))),CONCAT(firstname,' ',IFNULL(lastname,''))) as karigar

		FROM ret_karigar k

		LEFT JOIN ret_karikar_items_wastage kw on kw.id_karikar = k.id_karigar

		LEFT JOIN ret_karigar_stones ks on ks.id_karigar = k.id_karigar

		WHERE ".($approval_for ==0 ? "k.status_karigar = 1 and ( (kw.holded_through=2 AND kw.status=3)  or (kw.approved_through=2 AND kw.status=1 ) or (kw.rejected_through=2 AND kw.status=2) ) " : "k.status_karigar = 1  AND ( (ks.holded_through=2 AND ks.status=3)  or (ks.approved_through=2 AND ks.status=1 ) or (ks.rejected_through=2 AND ks.status=2) ) ")."

		Group by k.id_karigar");

		return $sql->result_array();

	}
//Mobile App Approval Ends Here

}