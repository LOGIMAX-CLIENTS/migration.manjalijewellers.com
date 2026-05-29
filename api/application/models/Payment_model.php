<?php
class Payment_model extends CI_Model {
    function get_payment_details($id_customer)
	{  
		//$showGCodeInAcNo = $this->config->item('showGCodeInAcNo'); 
		$showGCodeInAcNo = 0;
		/*$filename = base_url().'api/rate.txt'; 
	    $data = file_get_contents($filename);
		$result['metal_rates'] = (array) json_decode($data);*/
		$result['metal_rates'] = [];
	    $schemeAcc = array();
		$sql="Select s.allow_general_advance,s.adv_min_amt,s.adv_max_amt,s.adv_denomination,s.maturity_type,	
		s.unpaid_months,s.apply_template,s.disc_days,
		date_add(date(sa.start_date),interval + s.total_installments month) as daily_sch_allow_pay_till,
		
		IFNULL((SELECT count(p.id_payment) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1 AND date(p.date_payment) = curdate()),0) as curday_total_paid_count,
		IFNULL((SELECT count(p.id_payment) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1 AND p.due_type = 'AD'),0) as total_adv_paid,
		IFNULL((SELECT count(p.id_payment) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1 AND p.due_type = 'PD'),0) as total_pend_paid,

        s.pay_duration,s.installment_cycle,s.grace_days,
		
		cs.restrict_lastPayment_days,IFNULL(sa.start_year,'') as start_year,(select b.short_name from branch b where b.id_branch = sa.id_branch) as acc_branch,s.code,
		                cs.schemeaccNo_displayFrmt,s.is_lucky_draw,ifnull(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,cs.scheme_wise_acc_no,
		s.get_amt_in_schjoin,s.firstPayamt_maxpayable,s.is_enquiry,s.is_digi,s.total_days_to_pay,DATEDIFF(CURDATE(),date(sa.start_date)) joined_date_diff, 
		IFNULL((SELECT SUM(p.payment_amount) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1 AND date(p.date_payment) = curdate()),0) as curday_total_paid,
		s.daily_pay_limit,IF(s.daily_pay_limit != 'NULL' , 1,0) as daily_payLimit_applicable,
		IF(s.daily_pay_limit != 'null',IFNULL(s.daily_pay_limit - IFNULL((SELECT SUM(p.payment_amount) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND date(p.date_payment) = CURDATE() AND p.payment_status = 1),0),0),'') as eligible_amt, 
		s.show_ins_type,s.set_as_min_from,s.set_as_max_from,s.no_of_dues as dues_count,s.rate_fix_by,s.rate_select,Date_Format(sa.start_date,'%Y-%m-%d') as join_date,
                date_format(CURRENT_DATE(),'%m') as cur_month,if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments, 
                (s.total_installments - COUNT(payment_amount)), 
                ifnull((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) - SUM(p.no_of_dues),if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,s.total_installments,(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))))) 
                as missed_ins,sa.avg_payable,s.avg_calc_ins,p.payment_status,
                PERIOD_DIFF(Date_Format(CURRENT_DATE(),'%Y%m'),Date_Format(sa.start_date,'%Y%m')) as current_pay_ins, 
		    	PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')) as paid_ins,PERIOD_DIFF(Date_Format(sa.maturity_date,'%Y%m'),Date_Format(curdate(),'%Y%m')) as tot_ins,
			    sg.group_code as scheme_group_code, UNIX_TIMESTAMP(Date_Format(sg.start_date,'%Y-%m-%d')) as group_start_date,  UNIX_TIMESTAMP(Date_Format(sg.end_date,'%Y-%m-%d')) as  group_end_date,  cs.has_lucky_draw,otp_price_fixing,fixed_rate_on,
                s.allowSecondPay,s.free_payment,cs.firstPayamt_payable,sa.firstPayment_amt,sa.is_registered,
                CONCAT(if(".$showGCodeInAcNo."=1,if(has_lucky_draw = 1,sg.group_code,s.code),'') ,' ',ifnull(sa.scheme_acc_number,'Not Allocated')) as chit_number,
			    s.gst_type,s.gst,sa.id_scheme_account,
			    IF(s.discount=1,s.firstPayDisc_value,0.00) as discount_val,s.firstPayDisc_by,s.firstPayDisc,sa.is_new,
			    s.id_scheme,br.id_branch, br.short_name, br.name as branch_name, 
			    c.id_customer,s.min_amount,s.max_amount,s.pay_duration,s.discount_type,s.discount_installment,s.discount,s.disc_ins_from,s.disc_ins_to,sa.id_branch as sch_join_branch,cs.is_branchwise_rate,
			    IFNULL(sa.account_name,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname))) as account_name,
			    c.mobile,
			    s.scheme_type,s.maturity_days,s.firstPayamt_as_payamt,s.flexible_sch_type,s.one_time_premium,s.is_enquiry,
			    s.fix_weight,sa.fixed_metal_rate,sa.fixed_wgt,
			    s.code,
			    IFNULL(s.min_chance,0) as min_chance,
			    IFNULL(s.max_chance,0) as max_chance,
			    Format(IFNULL(s.max_weight,0),3) as max_weight, IF(s.max_weight=s.min_weight,'1','0') as wgt_type,
			    Format(IFNULL(s.min_weight,0),3) as min_weight,s.wgt_convert,
			    Date_Format(sa.start_date,'%d-%m-%Y') as start_date,s.flx_denomintion,
			    IF(s.scheme_type=0 OR s.scheme_type=2,TRIM(s.amount),IF(s.scheme_type=1 ,s.max_weight,if(s.scheme_type=3,if(flexible_sch_type = 3 ,  s.max_weight,if(s.firstPayamt_as_payamt=1,sa.firstPayment_amt ,TRIM(s.min_amount))),0))) as payable,
				round(IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,s.max_weight,
				if(s.scheme_type=3 && s.max_amount!='',s.max_amount,0)))) as max_amount,
				 (SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1)as metal_rate,
				s.total_installments,IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),1,0) as  previous_paid,
 				IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 and s.payment_chances=1) , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)as paid_installments,
 cs.branch_settings,
IFNULL(IF(sa.is_opening=1,IFNULL(balance_amount,0)+IFNULL(SUM(p.payment_amount * p.no_of_dues),0),IFNULL(SUM(p.payment_amount * p.no_of_dues),0)) ,0)
  as total_paid_amount,
FORMAT(sum(if(p.gst > 0,if((p.gst_type = 1),0,p.payment_amount-(p.payment_amount*(100/(100+p.gst)))),0)),0) as paid_gst,
IFNULL(IF(sa.is_opening=1,IFNULL(balance_weight,0)+IFNULL(SUM(p.metal_weight),0),IFNULL(SUM(p.metal_weight),0)),0.000)
 as total_paid_weight,
  if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,   (s.total_installments - if(sa.is_opening = 1,(count(DISTINCT((Date_Format(p.date_payment,'%Y%m'))))+sa.paid_installments),count(DISTINCT((Date_Format(p.date_payment,'%Y%m')))))),ifnull(((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))+1) - IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))),if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,s.total_installments,(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))))) as totalunpaid,   
   IFNULL(if(Date_Format(max(p.date_payment),'%Y%m') = Date_Format(curdate(),'%Y%m'), (select SUM(ip.no_of_dues) from payment ip where Date_Format(ip.date_payment,'%Y%m') = Date_Format(curdate(),'%Y%m') and sa.id_scheme_account = ip.id_scheme_account),IF(sa.is_opening=1, if(Date_Format(sa.last_paid_date,'%Y%m') = Date_Format(curdate(),'%Y%m'), 1,0),0)),0) as currentmonthpaycount,  
  (select SUM(pay.no_of_dues) from payment pay where pay.id_scheme_account= sa.id_scheme_account and pay.due_type='AD' and (pay.payment_status=1 or pay.payment_status=2)) as currentmonth_adv_paycount,
  (select SUM(pay.no_of_dues) from payment pay where pay.id_scheme_account= sa.id_scheme_account and pay.due_type='PD' and (pay.payment_status=1 or pay.payment_status=2)) as currentmonth_pend_paycount,
IF(s.scheme_type =1 and s.max_weight!=s.min_weight,true,false) as is_flexible_wgt,  
			    round(IFNULL(cp.total_amount,0)) as  current_total_amount,
			    Format(IFNULL(cp.total_weight,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_weight),0) ,3) as  current_total_weight,
			    IFNULL(cp.paid_installment,0)       as  current_paid_installments,
			   			    IFNULL(cp.chances,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_chances),0) as  current_chances_used_old,
							if(s.scheme_type=3 && s.pay_duration=0 ,IFNULL(sp.chance,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_chances),0),IFNULL(cp.chances,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_chances),0)) as  current_chances_used,
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
				sa.disable_payment,s.charge,s.charge_type,s.charge_head,
				cs.currency_name,
				cs.currency_symbol,s.firstPayamt_maxpayable,s.get_amt_in_schjoin,s.id_metal,s.id_purity,s.max_chance
			From scheme_account sa
			Left Join scheme s On (sa.id_scheme=s.id_scheme)
			Left Join branch br  On (br.id_branch=sa.id_branch)
			Left Join scheme_group sg On (sa.group_code = sg.group_code )
			Left Join payment p On (sa.id_scheme_account=p.id_scheme_account and (p.payment_status=1 or p.payment_status=2 or p.payment_status=8))
			Left Join customer c On (sa.id_customer=c.id_customer and c.active=1)
			Left Join
			(	Select
				  sa.id_scheme_account,
				  COUNT(Date_Format(p.date_payment,'%Y%m')) as paid_installment,
				  COUNT(Date_Format(p.date_payment,'%Y%m')) as chances,
				  SUM(p.payment_amount) as total_amount,
				  SUM(p.metal_weight) as total_weight
				From payment p
				Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0)
				Where (p.payment_status=1 or p.payment_status=2 ) and  Date_Format(Current_Date(),'%Y%m')=Date_Format(p.date_payment,'%Y%m')
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
		Where   sa.active=1 and sa.is_closed = 0  and s.is_digi = 0 and c.id_customer='$id_customer' and s.is_enquiry=0
			Group By sa.id_scheme_account";
		//	echo $sql;exit;
		$records = $this->db->query($sql);
		if($records->num_rows()>0)
		{
		    //customer scheme account overall amount
	        $overall_amt = 0;
			foreach($records->result() as $record)
			{
				$allow_pay 				= "N";
				$allowed_due 			= 0;
				$due_type 				= '';
				$checkDues 				= TRUE;
				$allowSecondPay 		= FALSE;
				$metal_rate 			= 0;
				$max_wgt_rate 			= 0;
				$show_payable			= 'N';
				$flx_denomintion		= 100;
				$eligible_wgt			= 0.000;
				$payable				= 0;
				$metal_rates['goldrate_22ct'] = 0;
				$discountData			= array(
											"apply_disc"	=> 0,
											"disc_by"		=> 0,
											"discount_val"	=> 0
											);
				$overall_amt			+= $record->current_total_amount;
				$current_installments	= ($record->current_chances_used == 0 ? $record->paid_installments+1 : $record->paid_installments);
				if($record->maturity_type == 3){ // 3 - Fixed Flexible[Increase maturity if has Default]
					$this->chkMaturityDate($record);
				}
				if($record->apply_template == 1){ // Pavithra :: Template
					$metal_rate = $this->get_metalrate($record->id_branch,$record->id_metal,$record->id_purity,'',$record->is_branchwise_rate);//For branchwise rate
					if($record->scheme_type == 3){
						$tempData = $this->flexiblePlan($record, $current_installments);
						$record->min_amount = $tempData["min_amount"];
						$record->max_amount = $tempData["max_amount"];
						$record->min_weight = $tempData["min_weight"];
						$record->max_weight = $tempData["max_weight"];
						$min_amount 		= $tempData["min_amount"];
						$max_amount 		= $tempData["max_amount"];
						$min_weight 		= $tempData["min_weight"];
						$max_weight 		= $tempData["max_weight"];
						$eligible_wgt 		= $tempData["max_weight"];
						$max_wgt_rate 		= $tempData["max_weight"] * $metal_rate;
						$flx_denomintion 	= $tempData["denomination"];
						$allow_pay 			= $tempData["allow_pay"];
						$allowed_due 		= $tempData["allowed_dues"];
						$due_type 			= $tempData["due_type"];
						$payable 			= $tempData["payable"];
						$discountData		= $tempData["discount"];
					}
				}
				else{
					// Check Discount
					if($record->discount == 1 ){
						$apply_disc = 0;
						if($record->discount_type == 0 ){ // Give discount for all installments
							$apply_disc = 1;
						} else{
							if($current_installments >= $record->disc_ins_from && $current_installments <=$record->disc_ins_from){ // Specific ins
								$apply_disc = 1;
							}
						}		
						if($apply_disc == 1){
							$discData = array(
										"disc_days"				=> $record->disc_days,
										"start_date"			=> $record->start_date,
										"total_installment"		=> $record->total_installments,
										"paid_ins"				=> $record->paid_installments,
										"current_installment"	=> $current_installments
									);
							$allowDisc = $this->checkAllowDisc($discData);
							if($allowDisc){
								$apply_disc = 1;
							}else{
								$apply_disc = 0;
							}
						}			
						$discountData = array(
										"apply_disc"	=> $apply_disc,
										"disc_by"		=> $record->firstPayDisc_by,
										"discount_val"	=> $record->firstPayDisc_value
										);
					}
					
					
					// Change Min Max amount by Installment
					if($record->set_as_min_from > 0 && $record->set_as_max_from > 0){ 
						$MinMaxLimitByIns = $this->setMinMaxAmountByIns($record,$current_installments);
						$record->min_amount = $MinMaxLimitByIns['min_amount'];
						$record->max_amount = $MinMaxLimitByIns['max_amount'];
					}				
					// Calculate max payable [Applicable only for No advance, No pending enabled schemes]
					// [Flexible || Weight] && avg_calc_ins
					else if((($record->scheme_type == 1 && $record->is_flexible_wgt == 1) || $record->scheme_type == 3) &&  $record->avg_calc_ins > 0 && $record->avg_calc_ins != ''){
						$maxData = $this->setAvgAsMaxLimit($record,$current_installments);
						$record->max_weight = $maxData['max_weight'];
						$record->max_amount = $maxData['max_amount'];
					}
		    
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
					if($record->maturity_days!=null)
					{
						$current_date =date("Y-m-d");
						$maturity_date=$record->maturity_date;
						if(strtotime($current_date) <= strtotime($maturity_date)) 
						{ 
							$checkDues=TRUE;
								if(($record->missed_ins+$record->paid_installments)<=$record->total_installments)
							{
								$checkDues=TRUE;
							}else{
								$checkDues = FALSE;
							}
						}
						else
						{
							$checkDues=FALSE;
						}
					}
					if($checkDues) // Pavithra :: Template
					{
						$dueData = $this->checkDuesData($record);
						$allowed_due= $dueData['allowed_due'];
			            $due_type	= $dueData['due_type'];
					}
			 
				    if(!empty($record->maturity_days) && $record->allow_unpaid == 0) // ** Advance Only. No Pending allowed. ** //
		            {
		                if($record->advance_months > 0){
			                if($record->current_paid_installments == 0 )  // Current month not Paid (Current+Advance)
			                {
				                $allowed_due = $record->total_installments-$record->current_pay_installemnt;
				                $due_type='AN';
			                }
			                else // Current month Paid (Advance)
			                {
				                $allowed_due = $record->total_installments - ($record->current_pay_installemnt+$record->current_paid_installments);
				                $due_type='AD';
			                }
		                }
		            }
            
			        //golden promise scheme    
			        if($record->set_as_min_from > 0 && $record->set_as_max_from > 0 && $record->paid_installments > 0){
			        	if($record->dues_count > 0 && $record->paid_installments >= $record->set_as_min_from && $record->paid_installments <= $record->set_as_max_from)
				        {
				            $res = $this->db->query("select p.payment_amount,sa.id_scheme_account from payment p 
	    			                 left join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
	    			                 where p.payment_status=1 and p.id_scheme_account = '".$record->id_scheme_account."' order by id_payment asc limit 1");
			                 $payamount = $res->row_array();  
			                 if($payamount['payment_amount'] > 0)
			                 {
			                    $record->max_amount = $record->dues_count * $payamount['payment_amount']; 
			                    $record->min_amount =  $payamount['payment_amount']; 
			                    if($record->currentmonthpaycount != 0)
			                    {
	    			                    if($record->paid_installments > 0 && $record->currentmonthpaycount == 1)
	    			                    {
	    			                        $record->current_total_amount = 0;
	    			                    }else{
	    			                        $month_first_day = date('Y-m-01');
	    			                        $res1 = $this->db->query("select SUM(p.payment_amount) as payment_amount,sa.id_scheme_account from payment p 
	    			                        left join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
	    			                        where p.payment_status=1 and p.due_type != 'ND' and date(p.date_payment) between '".$month_first_day."' and '".$record->pay_date."' and p.id_scheme_account = '".$record->id_scheme_account."' ");
	    			                        //echo $this->db->last_query();exit;
	    			                        $amt = $res1->row_array();
	    			                        $record->current_total_amount = $amt['payment_amount'];
	    			                    }
			                    }else{
			                        $record->min_amount = $record->min_amount;
			                        $record->max_amount = $record->dues_count * $payamount['payment_amount'];
			                        $record->current_total_amount = 0;
			                    }
			                 }else{ // No USE
			                      $record->min_amount = $record->min_amount;
			                      $record->max_amount = $record->max_amount;
			                 }
				        }
				    }
					//partly flexible scheme - NAC
					if($record->scheme_type == 3 && ($record->flexible_sch_type == 6 || $record->flexible_sch_type == 6)){
				        $result = "SELECT sa.id_scheme,sf.ins_from,sf.ins_to,sf.min_value,sf.max_value FROM scheme_flexi_settings sf
				        left join scheme_account sa on sf.id_scheme = sa.id_scheme
					    where sf.id_scheme = sa.id_scheme and sf.id_scheme = ".$record->id_scheme." and sf.ins_from <= ".$current_installments."  and  sf.ins_to >= ".$current_installments." and sa.id_scheme_account = ".$id_scheme_account;
					    //echo $result;exit;
					    $flexi = $this->db->query($result)->row_array();	
					    if($record->flexible_sch_type == 6)
					    {
	    			        if($current_installments >= $flexi['ins_from'])
	    			        {
	    			            $record->min_amount = $flexi['min_value'];
	    			            $record->max_amount = $flexi['max_value'];
	    			        }
	    			        else if($current_installments >= $flexi['ins_to'])
	    			        {
	    			            $record->min_amount = $flexi['min_value'];
	    			            $record->max_amount = $flexi['max_value'];
	    			        }
					    }
					    else if($record->flexible_sch_type == 7)
					    {
					        if($current_installments >= $flexi['ins_from'])
	    			        {
	    			            $record->min_weight = $flexi['min_value'];
	    			            $record->max_weight = $flexi['max_value'];
	    			        }
	    			        else if($current_installments >= $flexi['ins_to'])
	    			        {
	    			            $record->min_weight = $flexi['min_value'];
	    			            $record->max_weight = $flexi['max_value'];
	    			        }
					    }
				    }   
		  	// Allow Pay
			if($record->scheme_type == 3){
			    if($record->one_time_premium == 0){
    			         if($record->flexible_sch_type == 1 || $record->flexible_sch_type == 2)
    			        {
    			            $allow_pay  = ($record->disable_payment != 1 && $record->payment_status !=2  && (($record->is_flexible_wgt == 1 && $record->paid_installments <= $record->total_installments) || ($record->is_flexible_wgt == 0 && $record->paid_installments < $record->total_installments)) ? ($record->flexible_sch_type == 1 || $record->flexible_sch_type == 2 ? (($record->current_total_amount >= $record->max_amount || $record->current_chances_used >= $record->max_chance) && $record->paid_installments != 0 ? ($record->allow_unpaid == 1  && $record->totalunpaid >0 && ($record->currentmonthpaycount-1) < $record->allow_unpaid_months ?'Y':($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount -1) < $record->advance_months ?'Y':($record->currentmonthpaycount == 0 ? 'Y': 'N'))):'Y') : 'N'):'N');
    			        }
    			        elseif($record->flexible_sch_type == 3 || $record->flexible_sch_type == 4)
    			        {
    			            
    				        $allow_pay  = ($record->disable_payment != 1 && $record->payment_status !=2  && $record->paid_installments <= $record->total_installments ? ($record->flexible_sch_type == 2 || $record->flexible_sch_type == 3 ? ($record->current_total_weight >= $record->max_weight && $record->current_chances_used >= $record->max_chance ? ($record->allow_unpaid == 1  && $record->totalunpaid >0 && ($record->currentmonthpaycount-1) < $record->allow_unpaid_months ?'Y':($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount -1) < $record->advance_months ?'Y':($record->currentmonthpaycount == 0 ? 'Y': 'N'))):'Y') : 'N'):'N');
    			    
    			        }else{
    			             $allow_pay  = ($record->disable_payment != 1 && $record->payment_status !=2  && $record->paid_installments < $record->total_installments ? ($record->flexible_sch_type == 2 || $record->flexible_sch_type == 3 ? ($record->current_total_weight >= $record->max_weight && $record->current_chances_used >= $record->max_chance ? ($record->allow_unpaid == 1  && $record->totalunpaid >0 && ($record->currentmonthpaycount-1) < $record->allow_unpaid_months ?'Y':($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount -1) < $record->advance_months ?'Y':($record->currentmonthpaycount == 0 ? 'Y': 'N'))):'Y') : 'N'):'N');

    			        }
			        }else{
			        $allow_pay  = ($record->disable_payment != 1 && $record->payment_status !=2  && $record->paid_installments == 0 && $record->is_enquiry == 0 ? ($record->flexible_sch_type == 1 || $record->flexible_sch_type == 4 || $record->flexible_sch_type == 5 ? ($record->current_total_amount >= $record->max_amount || $record->current_chances_used >= $record->max_chance ?'N':'Y') : 'N'):'N');
			    }
			}else{
				$allow_pay  = ($record->disable_payment != 1 && ($record->payment_status !=2) ? ($record->cur_month_pdc < 1 ? ($record->paid_installments < $record->total_installments ?($record->is_flexible_wgt?($record->current_total_weight >= $record->max_weight || $record->current_chances_used >= $record->max_chance ?'N':'Y'):($record->paid_installments <  $record->total_installments ?($record->allow_unpaid == 1  && $record->totalunpaid >0 && ($record->currentmonthpaycount-1) < $record->allow_unpaid_months ?'Y':($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount -1) < $record->advance_months ?'Y':($record->currentmonthpaycount == 0 ? 'Y': 'N'))):'N')):'N'):'N'):'N');
			}
	//DGS-DCNM restrict payment by days allow pay settings....	
	    if($record->is_digi == 1){
	        if($record->daily_pay_limit != null){
        	    if($record->curday_total_paid != 0 && $record->curday_total_paid >= $record->daily_pay_limit)
        		{
        			$allow_pay = 'N';
        		}else{
        			$allow_pay = 'Y';
        		}  
			}
		    if($record->restrict_payment == 1){
        	    if($record->total_days_to_pay != null && ($record->joined_date_diff >= $record->total_days_to_pay))
        		{
        			$allow_pay = 'N';
        		}else{
        			$allow_pay = 'Y';
        		}  
			}
		}
	//DGS-DCNM end...
	$max_wgt_rate = ($record->is_flexible_wgt == 1?($record->max_weight - $record->current_total_weight):$record->max_weight) * $metal_rates['goldrate_22ct'];
	//allow pay for daily scheme....
	if($record->pay_duration == 0){
   
	    if($record->maturity_type == 1 && $record->paid_installments < $record->total_installments){
	        $allow_pay = 'Y';
	    }else{
	        if($record->curday_total_paid_count < $record->max_chance && date('Y-m-d') < $record->daily_sch_allow_pay_till  ){
	            $allow_pay = 'Y';
	        }else{
	            $allow_pay = 'N';
	        }
	    }
	}
	//allow pay daily scheme ends...
	//For RHR Metal Purity wise rate	
  	if($record->rate_select == 1)
	{
	    $metal_rate = $this->get_metalrate_by_branch($record->id_branch,$record->id_metal,$record->id_purity,'');//For branchwise rate
	}
	else if($record->rate_select == 2)
	{
	    $metal_rate = $this->get_metalrate_by_branch($record->id_branch,$record->id_metal,$record->id_purity,$record->join_date);//For branchwise rate
	}
	
	
	$elig_wgt =	($record->flexible_sch_type == 3 || $record->scheme_type == 1 ? ($record->min_weight == $record->max_weight ? $record->min_weight : ($record->max_weight - $record->current_total_weight)) : ($record->flexible_sch_type==8 && $record->firstPayment_as_wgt==1 && $record->paid_installments==0 ? ($record->max_weight - $record->current_total_weight) : (($record->max_amount / $metal_rate ) - $record->current_total_weight)));


	if($record->get_amt_in_schjoin==1 && ($record->firstPayamt_maxpayable==1||$record->firstPayamt_as_payamt==1) && $record->firstPayment_amt > 0){
	    
	    $eligible_wgt = ($record->firstPayment_amt / $record->metal_rate );
	    
	}else{
	    
	    if($record->scheme_type == 1 || $record->scheme_type == 3 && ($record->flexible_sch_type == 3 || $record->flexible_sch_type == 4  || $record->flexible_sch_type == 8)){   //normal weight scheme,
	
	     $eligible_wgt = ($record->max_weight - $record->current_total_weight);
	     
    	}elseif($record->scheme_type == 2){   //normal amt to wgt
    	
    	    $eligible_wgt = ($record->amount / $metal_rate);
    	    
    	}elseif($record->flexible_sch_type == 5 || $record->flexible_sch_type == 2){
	    
	        $eligible_wgt = (($record->max_amount / $record->metal_rate )- $record->current_total_weight);
	    
	    }else{
	        $eligible_wgt = $elig_wgt;
	    }
    	    
	}
	
	if($record->installment_cycle == 1 || $record->installment_cycle == 2){
		//RHR schemes :starts 20-09-2023 #ABI...
		

		/*
		1. ALLOW PAY : (Y/N)
		2. ALLOWED DUES : no of dues count can pay
		3. DUE TYPE	:
			allow pay for days duration wise instalment cycle starts...
			ND- only normal due , allowed_due : 1
			AD - only advance due, allowed_due : 1
			PD - only pending due, allowed_due : 1
			AN - advance + normal, allowed_due : no.of.adv + 1
			PN - pending + normal, allowed_due : no.of.pend + 1
			APN - Advance + pending + normal, allowed_due : no.of.adv + no.of.pend + 1
			
			RHR schemes : Installment cycle type 2 (by days duration) by 30 days payment cycle with advance , pending flow.. 20-09-2023 #ABI...
			
			RHR schemes : Installment cycle type 1 (daily)  daily payment scheme with advance and pending...updatedOn:12-10-2023, By:AB

	        worked for only single payment chance type till 12-10-2023
	        
	        need to work for multiple payment chances type further
		*/	
		
		
		//due_type , allowed_dues
			$date = date('Y-m-d');
			$paid_normal_due = 0;
			$paid_advance_due = 0;
			$paid_pending_due = 0;
			$paid_due = 0;
			$remaining_normal_due = 0;
			$remaining_advance_due = 0;
			$remaining_pending_due = 0;
			$remaining_due = 0;

			//take the no of paid dues with due_type customer paid already...
			$paid_dueData  = $this->db->query("SELECT due_type as due_name, COUNT(due_type) as dues_count FROM payment where payment_status = 1 and id_scheme_account = ".$record->id_scheme_account." group by due_type;")->result_array();

			foreach($paid_dueData  as $due){

				if($due['due_name'] == 'ND'){
					$paid_normal_due = $due['dues_count'];
				}else if($due['due_name'] == 'AD'){
					$paid_advance_due = $due['dues_count'];
				}else if($due['due_name'] == 'PD'){
					$paid_pending_due = $due['dues_count'];
				}else{
					$paid_due = $due['dues_count'];
				}
				
			}


			//take the no of remaining dues with due_type customer want to pay..
			$remaining_dueData = $this->get_due_date('allow_pay',$date,$record->id_scheme_account);

			foreach($remaining_dueData  as $due){

				if($due['due_name'] == 'ND'){
					$remaining_normal_due = $due['dues_count'];
				}else if($due['due_name'] == 'AD'){
					$remaining_advance_due = $due['dues_count'];
				}else if($due['due_name'] == 'PD'){
					$remaining_pending_due = $due['dues_count'];
				}else{
					$remaining_due = $due['dues_count'];
				}
				
			}

		    //calculate can pay advance due and pending dues...

			if(($record->allow_advance == 1 && $record->advance_months > 0) || ($record->allow_unpaid == 1 && $record->unpaid_months > 0)){
				//advance..
				$sch_advance = $record->advance_months;
				$cur_advance = abs($sch_advance - $paid_advance_due);
				$canPay_advance = ($cur_advance < $remaining_advance_due ? $remaining_advance_due : $cur_advance);
				
				//pending
				$sch_unpaid = $record->unpaid_months;
				$cur_unpaid = abs($sch_unpaid - $paid_pending_due);  //7 - 1 = 6 ,but 5 only should allow becuse remaining
				$canPay_pending = ($cur_unpaid  < $remaining_pending_due ? $remaining_pending_due : $cur_unpaid );
				

				if($remaining_normal_due == 0 && $canPay_pending > 0){			//only pending
					$due_type = 'PD';						
					$allowed_due = $canPay_pending ;
				}
				else if($remaining_normal_due == 0 && $canPay_advance > 0){		//only advance
					$due_type = 'AD';						
					$allowed_due = $canPay_advance ;
				}
				else if($remaining_normal_due > 0 && $canPay_pending > 0){		//normal + pending
					$due_type = 'PN';           			
					$allowed_due = $remaining_normal_due + $canPay_pending;
				}
				else if($remaining_normal_due > 0 && $canPay_advance > 0){		//normal + advance
					$due_type = 'AN';						
					$allowed_due = $remaining_normal_due + $canPay_advance;
				}

			}else{
				$due_type = 'ND';
				$allowed_due = $remaining_normal_due;
			}	

			//allow pay , discuss for APN and multiple payment chances

			
			
			//allow pay for daily scheme....



	    if($record->installment_cycle == 1 && $record->pay_duration == 0 ){
	        if($record->curday_total_paid_count >= $record->max_chance && ($record->allow_advance == 1 && $record->total_adv_paid >= $record->advance_months) && ($record->allow_unpaid == 1 && $record->total_pend_paid >= $record->allow_unpaid_months)){
	            $allow_pay = 'N';
	            $allowed_dues = 0;
	            $due_type = '';
	            
	        }else{
	            //due type && allowed dues count...
	            
	            if($record->curday_total_paid_count == 0){
	                
	                //normal + pending
	                if($record->allow_unpaid == 1 && $record->total_pend_paid < $record->allow_unpaid_months){
	                    $due_type = 'PN';
	                    $allowed_dues = ($record->allow_unpaid_months - $record->total_pend_paid) + 1 ;
	                }
	                //normal + advance
	                elseif($record->allow_advance == 1 && $record->total_adv_paid < $record->advance_months){
	                    $due_type = 'AN';
	                    $allowed_dues = ($record->advance_months - $record->total_adv_paid) + 1 ;

	                }
	                //only normal
	                else{
	                    $due_type = 'ND';
	                    $allowed_dues = 1;
	                }
	                
	            }else{
	                
	                //only pending
	                if($record->allow_unpaid == 1 && $record->total_pend_paid < $record->allow_unpaid_months){
	                    $due_type = 'PD';
	                    $allowed_dues = ($record->allow_unpaid_months - $record->total_pend_paid) ;
	                }
	                //only advance
	                elseif($record->allow_advance == 1 && $record->total_adv_paid < $record->advance_months){
	                    $due_type = 'AD';
	                    $allowed_dues = ($record->advance_months - $record->total_adv_paid) ;

	                }
	            }
	           
	        }
	        
	         if($allowed_due > 0 || $record->total_installments < $record->paid_installments){
	    		$allow_pay = 'Y';
	    	}else{
	    		$allow_pay = 'N';
	    	}
	    }  
	//RHR schemes ends 
	}
	
	if($record->scheme_type == 3 && $record->set_as_min_from > 0 && $record->set_as_max_from > 0 && $record->firstPayamt_as_payamt == 0)
	{
	    if($current_installments <= $record->set_as_max_from)
	    { 
	        $show_payable = 'N'; 
	    }
	}
	else{
	    $show_payable = 'Y';
	}
	
	//allow pay for general advance settings if enabled //TKV 
	
	$payable = (($record->scheme_type==3 && $record->max_amount!=0 &&($record->flexible_sch_type==1 || $record->flexible_sch_type==2 || $record->flexible_sch_type==5) && $record->max_amount!=''?((($record->firstPayamt_maxpayable==1||$record->firstPayamt_as_payamt==1)&&($record->paid_installments>0 || $record->get_amt_in_schjoin==1)||($record->is_registered==1))?round($record->firstPayment_amt) :round($record->min_amount)):($record->scheme_type==3 && ($record->max_weight!=0 || $record->max_weight!='')? ($record->flexible_sch_type==8 && $record->firstPayment_as_wgt==1 && $record->paid_installments>0 && $record->firstpayment_wgt != null ? round($record->firstpayment_wgt * $record->metal_rate,3) :($record->flexible_sch_type==8 && $record->paid_installments == 0 ? round($record->min_weight*$record->metal_rate,3) : round(($record->max_weight - $record->current_total_weight)*$record->metal_rate))) : $record->payable)));

	$eligible_wgt = ($record->flexible_sch_type == 3 || $record->scheme_type == 1 ? ($record->min_weight == $record->max_weight ? $record->min_weight : ($record->max_weight - $record->current_total_weight)) : ($record->flexible_sch_type==8 && $record->firstPayment_as_wgt==1 && $record->paid_installments==0 ? ($record->max_weight - $record->current_total_weight) : $payable/$metal_rates['goldrate_22ct']));

	$min_amount = round(($record->scheme_type==3 && $record->min_amount!=0 && $record->min_amount!='' ? ((($record->firstPayamt_maxpayable==1 ||$record->firstPayamt_as_payamt==1) && $record->firstPayment_amt != NULL)&&($record->paid_installments>0 || $record->get_amt_in_schjoin==1) ? $record->firstPayment_amt:$record->min_amount):
	($record->scheme_type==3 && $record->min_weight!=0 && $record->min_weight!=''? (($record->min_weight)*$metal_rates['goldrate_22ct']) : $record->min_amount)));
	
	$max_amount = round(($record->scheme_type==3 && $record->max_amount!=0 && $record->max_amount!='' ? (($record->firstPayamt_maxpayable==1 ||$record->firstPayamt_as_payamt==1 )&&($record->paid_installments>0 ||$record->get_amt_in_schjoin==1) ?  $record->firstPayment_amt:($record->max_amount - str_replace(',', '',$record->current_total_amount))):
	($record->scheme_type==3 && $record->max_weight!=0 && $record->max_weight!=''? (($record->max_weight - $record->current_total_weight)*$metal_rates['goldrate_22ct']) : $record->max_amount)));

	$flx_denomintion = $record->flx_denomintion;
	
	$due_type = ($record->is_flexible_wgt == 1 ? 'ND':$due_type);
	
	$allowed_due = ($record->is_flexible_wgt == 1 ? 1:$allowed_due);
} // Pavithra :: Template
	
				 
			 
   
	$dates= date('d-m-Y');
	//chit number and receipt number based on display format settings starts...
	$accNumData = array('is_lucky_draw' => $record->is_lucky_draw,
                    	'scheme_acc_number' => $record->scheme_acc_number,
                    	'scheme_group_code' => $record->scheme_group_code,
                    	'schemeaccNo_displayFrmt' => $record->schemeaccNo_displayFrmt,
                    	'scheme_wise_acc_no' => $record->scheme_wise_acc_no,
                    	'acc_branch' => $record->acc_branch,
                    	'code' => $record->code,
                    	'start_year' => $record->start_year,
						'id_scheme_account'=>$record->id_scheme_account
                    	);
	//ends
	

	if($allow_pay == 'N' && $record->allow_general_advance == 1 &&  date('Y-m-d') < $record->daily_sch_allow_pay_till){
		$genAdv_allow_pay = 'Y';
		$allow_pay = 'Y';
		$due_type = 'GA';
		$allowed_due = 1;
		$min_amount = round($record->adv_min_amt);
		$max_amount = round($record->adv_max_amt);
		$flx_denomintion = $record->adv_denomination;
		$payable = round($record->adv_min_amt);
	}else{
	    $genAdv_allow_pay = 'N';  
	}
	//TKV gen advance ends...
	
	/* Make allow pay No for payment restriction by hours if online payment is not directly success need to verify in gateway case starts ..... Dt: 7/8/2023,By:Abi */
    if($record->restrict_lastPayment_days > 0 && $allow_pay == 'Y' && $record->paid_installments == ($record->total_installments - 1)){
        
        $cur_month = $this->db->query("SELECT  MONTH(p.date_payment),YEAR(p.date_payment), p.id_payment,date(p.date_payment) as date_payment,p.payment_status 
                                        FROM payment p 
                                        JOIN chit_settings cs
                                        WHERE YEAR(CURDATE()) = YEAR(p.date_payment) AND MONTH(CURDATE()) = MONTH(p.date_payment)  
                                        AND (p.payment_status=3 OR p.payment_status=4 OR p.payment_status=7)
                                        AND TIMESTAMPDIFF(HOUR,p.date_payment,'".date('Y-m-d H:i:s')."')  < cs.restrict_lastPayment_days
                                        AND p.added_by = 2
                                        AND p.id_scheme_account =".$record->id_scheme_account );
    
        if($cur_month->num_rows() > 0){
             
            $allow_pay = 'N';
            
        }else{
            
            $allow_pay = 'Y';
        }
    
    }
	//gateway allow pay restriction ends
	
	
		$schemeAcc[] = array(				    
                        'discountData'   		  => $discountData,
                        'allow_general_advance'   => $record->allow_general_advance,
                        'genAdv_allow_pay'        => $genAdv_allow_pay,
                        'min_amount' 			  => $min_amount,
                        'max_amount'              => $max_amount,
                        'payable'                 => $payable,
                        'flx_denomintion' 		  => $flx_denomintion,
                        'due_type' 		          => $due_type,
                        'allowed_dues'  		  =>($record->is_flexible_wgt == 1 ? 1:$allowed_due),
                        'allow_pay'               => $allow_pay,
						'gst_type'				=> $record->gst_type,
						'pay_duration' 		        => $record->pay_duration,
						'branch_settings' 		        => $record->branch_settings,
						'min_chance' 		        => $record->min_chance,
						'max_chance' 		        => $record->max_chance,
						'firstPayment_amt' 		      => $record->firstPayment_amt,
						'firstPayamt_payable' 		   => $record->firstPayamt_payable,
						'firstPayamt_as_payamt' 		=> $record->firstPayamt_as_payamt,
						'flexible_sch_type' 		    => $record->flexible_sch_type,
						'get_amt_in_schjoin' 		    => $record->get_amt_in_schjoin,
						'one_time_premium' 		         => $record->one_time_premium,
						'is_enquiry'                => $record->is_enquiry,
						'otp_price_fixing' 		         => $record->otp_price_fixing,
						'multiply_value' 		        => 500,
						'fixed_wgt' 		        => $record->fixed_wgt,
						'fixed_rate' 		        => $record->fixed_metal_rate,
						'maturity_date' 		    => date_format(date_create($record->maturity_date),"d-m-Y"),
						'fixed_metal_rate' 		  =>($record->fixed_rate_on==NULL ?'NO' :'YES') ,
						'metal_rate'                => $metal_rate,
						'gst' 						=> $record->gst,
						'paid_gst' 					=> $record->paid_gst,
						'id_branch' 				=> $record->id_branch,
						'short_name' 				=> $record->short_name,
						'branch_name' 				=> $record->branch_name,
						'currentmonthpaycount' 		=> $record->currentmonthpaycount,
						'totalunpaid' 				=> $record->totalunpaid,
						'id_scheme_account' 		=> $record->id_scheme_account,
						'max_wgt_rate' 				=> $max_wgt_rate,
						'charge_head' 				=> $record->charge_head,
						'charge_type' 				=> $record->charge_type,
						'charge' 					=> $record->charge,
						'chit_number' 				=> $this->getAccNoFormat($accNumData),
						'account_name' 				=> $record->account_name,
						'start_date' 				=> $record->start_date,
						'mobile' 					=> $record->mobile,
						'is_flexible_wgt' 	    	=> $record->is_flexible_wgt,
						'currency_symbol' 			=> $record->currency_symbol,
						'code' 						=> ($record->has_lucky_draw == 1 ?  $record->scheme_group_code : $record->code),
						'scheme_type' 				=> $record->scheme_type,
						'total_installments'		=> $record->total_installments,
						'paid_installments' 		=> $record->paid_installments,
						'total_paid_amount' 		=> $record->total_paid_amount,
						'total_paid_weight' 		=> $record->total_paid_weight,
						'current_total_amount' 		=> $record->current_total_amount,
						//'current_paid_installments'	=> $record->current_paid_installments,
						'current_chances_used' 		=> $record->current_chances_used,
						'current_chances_pay'       => $record->current_chances_used,
						'eligible_weight' 		    => abs(number_format($eligible_wgt,2)),
						'allow_unpaid_months' 			=> $record->allow_unpaid_months,
						'last_paid_duration' 			=> $record->last_paid_duration,
						'last_paid_date' 			=> $record->last_paid_date,
						'current_date' 		    => $dates,
						'last_paid_month' 			=> ($record->last_paid_month!='' || $record->last_paid_month!=NULL ? $record->last_paid_month : 0),
						'is_pan_required' 			=> $record->is_pan_required,
						'wgt_convert' 			   => $record->wgt_convert, 
						'last_transaction'  	    => $this->getLastTransaction($record->id_scheme_account),
						'isPaymentExist' 			=> $this->isPaymentExist($record->id_scheme_account),
						'isPendingStatExist' 		=> $this->isPendingStatExist($record->id_scheme_account),
						'max_weight' 				=> $record->max_weight,
						'current_total_weight' 		=> $record->current_total_weight,
						'previous_amount_eligible' 	=> $record->previous_amount_eligible,
						'cur_month_pdc' 			=> $record->cur_month_pdc,
						'allowPayDisc'      => ($record->discount == 1 ? ($record->discount_type == 0? 'All': $record->discount_installment ) : 0),
						'firstPayDisc' 		=> $record->firstPayDisc,
						'firstPayDisc_by' 	=> $record->firstPayDisc_by,
						'discount_val' 			=> $record->discount_val,
					 	'due_type' 				=> $due_type,
						'max_allowed_limit'  	=>$allowed_due,
						'sel_due'  =>1,   //default selected due
						'pdc_payments'  =>($record->cur_month_pdc > 0 ? $this->get_postdated_payment($record->id_scheme_account) : NULL),
						'rate_fix_by'   => $record->rate_fix_by,
						'rate_select'  => $record->rate_select,
						'set_as_min_from' => $record->set_as_min_from,
						'set_as_max_from' => $record->set_as_max_from,
						'show_payable'  => $show_payable,
						'id_scheme' => $record->id_scheme,
						'id_customer' => $record->id_customer,
						'eligible_amt' => $record->eligible_amt,
						'daily_pay_limit' => $record->daily_pay_limit,
						'daily_payLimit_applicable' => $record->daily_payLimit_applicable
					);				
			}
			return array('chits' => $schemeAcc,'over_all_amount' => $overall_amt);
		}
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
       // print_r($rate_field);exit;
        
        if($rate_field !=''){
            $data=$this->get_settings();
            //print_r($data);exit;
    		if($data['is_branchwise_rate']==1 &&$id_branch!='' && $id_branch!=NULL)
    		{
    			$sql="select ".$rate_field." from metal_rates m
    	   		left join branch_rate br on m.id_metalrates=br.id_metalrate 
    	   		where br.id_branch=".$id_branch." 
    	   		'.($start_date!=NULL?' date(m.add_date) BETWEEN '".$start_date."' AND '".$today."' ' :'').'
    	   		order by  br.id_metalrate desc limit 1";
    		    //echo $sql;exit;
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
    	//	echo $sql;exit;
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
	
	function chkMaturityDate($ac){		
		$unpaid_dues		= 0;
		$months_till_date	= $this->getCurrMonthNo($ac->start_date);
		if($ac->paid_installments < $months_till_date){
			$unpaid_dues		= ($ac->total_installments - $ac->paid_installments);
		}
		$maturity			= date('Y-m-d', strtotime("+".($ac->total_installments+$unpaid_dues)." months", strtotime($ac->start_date)));
		if($ac->maturity_date != $maturity){
		    $updData = array( "maturity_date" => $maturity, "date_upd" => date("Y-m-d") );
			$this->db->where('id_scheme_account',$ac->id_scheme_account); 
			$this->db->update("scheme_account",$updData);
        }
		
		return TRUE;
	}
	    
	function get_metalrate($id_branch,$id_metal,$id_purity,$start_date='',$brnwise_rate)
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
       // print_r($rate_field);exit;
        
        if($rate_field !=''){
    		if($brnwise_rate==1 && $id_branch > 0)
    		{
    			$sql="select ".$rate_field." from metal_rates m
    	   		left join branch_rate br on m.id_metalrates=br.id_metalrate 
    	   		where br.id_branch=".$id_branch." 
    	   		'.($start_date!=NULL?' date(m.add_date) BETWEEN '".$start_date."' AND '".$today."' ' :'').'
    	   		order by  br.id_metalrate desc limit 1";
    		}
    		else
    		{
    			$sql="select ".$rate_field." from metal_rates
    			".($start_date!=NULL?" where date(add_date) BETWEEN '".$start_date."' AND '".$today."'" :"")."
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
	
	function get_due_date($due_type,$date_payment,$id_scheme_account){

		$result = [];

		$where = '';
	    
	    $sch = $this->get_scheme_details($id_scheme_account);

		$first_payment_date = (!empty($sch['first_payment_date']) ? $sch['first_payment_date'] : $date_payment);

         //by days duration cycle
	    if($sch['installment_cycle'] == 2){  //by days duration cycle

			$c_wh = "and  dt.due_date_from NOT IN (SELECT p.due_date from payment p where p.payment_status = 1 and p.due_date is not null and p.id_scheme_account = sa.id_scheme_account) limit 1";	
			
			if($due_type == 'ND' || $due_type == ''){
				$where = "and  date('".$date_payment."') BETWEEN dt.due_date_from and dt.due_date_to ".$c_wh." ";
			}else if($due_type == 'AD'){
				$where = "and dt.due_date_from >= date('".$date_payment."')  ".$c_wh." ";
			}else if($due_type == 'PD'){
				$where = "and dt.due_date_from <= date('".$date_payment."') ".$c_wh." ";
			}else if($due_type == 'allow_pay'){
				$where = "and  dt.due_date_from NOT IN (SELECT p.due_date from payment p where p.payment_status = 1 and p.due_date is not null and p.id_scheme_account = sa.id_scheme_account)";	

			}
	    
	        $sql ="SELECT 		if(
				dt.due_date_to <= CURDATE() ,
				'PD',
				if( CURDATE() BETWEEN dt.due_date_from AND dt.due_date_to  , 
					'ND',
					if(
                       dt.due_date_to >= CURDATE()
                        ,'AD','-'
                    )
				) 
			) as due_type,
			dt.installment,dt.due_date_from,dt.due_date_to,dt.grace_date,
			if((('".$due_type."' = 'ND' OR '".$due_type."' = '') AND date('".$date_payment."') BETWEEN dt.due_date_from and dt.grace_date) OR ('".$due_type."' = 'AD')  , '0','1') as is_limit_exceed	

			FROM scheme_account sa
			JOIN (SELECT @sno := @sno + 1 as installment,
				@due_Date_from := if(@sno = 1, '".$first_payment_date."',date_add(@pay_date ,INTERVAL ".$sch['ins_days_duration']." day )) as due_date_from,
				@due_Date_to := if(@sno = 1, date_add('".$first_payment_date."',INTERVAL ".$sch['ins_days_duration']." day ),date_add(@due_Date_from,INTERVAL ".$sch['ins_days_duration']." day )) as due_date_to,  
				@grace_date := if(@sno = 1, date_add('".$first_payment_date."',INTERVAL ".$sch['grace_days']." day ),date_add(@due_Date_from,INTERVAL ".$sch['grace_days']." day )) as grace_date,
				@pay_date := if(@sno = 1,'".$first_payment_date."',@due_Date_from) as due_pay_date
				FROM access
				join (SELECT @pay_date := if(@sno = 1,'".$first_payment_date."',@due_Date_from), @sno := 0 ) as t
				limit ".$sch['total_installments']."
			) as dt
		
			WHERE  sa.id_scheme_account = ".$id_scheme_account."  ".$where." ";

//print_r($sql);exit;
			$pay = $this->db->query($sql)->result_array();

			if($due_type == 'allow_pay'){
				foreach($pay as $p)
				{
					$grouped_dues[$p['due_type']][]=$p;
				}

				foreach($grouped_dues as $key => $gd){
					$result[] = array('due_name' => $key,'dues_count' => sizeof($gd));
				}

			}else{
				$result = $pay;
			}

	    }
	    
	    //daily payment cycle
	    elseif($sch['installment_cycle'] == 1){ 

			$c_wh = "and  dt.due_date_from NOT IN (SELECT p.due_date from payment p where p.payment_status = 1 and p.due_date is not null and p.id_scheme_account = sa.id_scheme_account) limit 1";	
			
			if($due_type == 'ND' || $due_type == ''){
				$where = "and date('".$date_payment."') = dt.due_date_from ".$c_wh." ";
			}else if($due_type == 'AD'){
				$where = "and dt.due_date_from > date('".$date_payment."')  ".$c_wh." ";
			}else if($due_type == 'PD'){
				$where = "and dt.due_date_from < date('".$date_payment."') ".$c_wh." ";
			}else if($due_type == 'allow_pay'){
				$where = "and  dt.due_date_from NOT IN (SELECT p.due_date from payment p where p.payment_status = 1 and p.due_date is not null and p.id_scheme_account = sa.id_scheme_account)";	

			}
	    
	        $sql ="SELECT 		if(
				dt.due_date_to < CURDATE() ,
				'PD',
				if( CURDATE() = dt.due_date_from  , 
					'ND',
					if(
                       dt.due_date_to > CURDATE()
                        ,'AD','-'
                    )
				) 
			) as due_type,
			dt.installment,dt.due_date_from,dt.due_date_to

			FROM scheme_account sa
			JOIN (SELECT @sno := @sno + 1 as installment,
				@due_Date_from := if(@sno = 1, '".$first_payment_date."',date_add(@pay_date ,INTERVAL 1 day )) as due_date_from,
				@due_Date_to := @due_Date_from  as due_date_to,  
				@pay_date := if(@sno = 1,'".$first_payment_date."',@due_Date_from) as due_pay_date
				FROM access
				join (SELECT @pay_date := if(@sno = 1,'".$first_payment_date."',@due_Date_from), @sno := 0 ) as t
				limit ".$sch['total_installments']."
			) as dt
		
			WHERE  sa.id_scheme_account = ".$id_scheme_account."  ".$where." ";

			$pay = $this->db->query($sql)->result_array();

			if($due_type == 'allow_pay'){
				foreach($pay as $p)
				{
					$grouped_dues[$p['due_type']][]=$p;
				}

				foreach($grouped_dues as $key => $gd){
					$result[] = array('due_name' => $key,'dues_count' => sizeof($gd));
				}

			}else{
				$result = $pay;
			}

	    }
	    
	    return $result;
	    
	    
	}
	
	function setMinMaxAmountByIns($account,$current_installments){	
		if($account->paid_installments > 1 && $account->min_amount != 0){ // UNUSED
			if($account->current_total_amount < $account->min_amount){
				$current_amt_min = 'Y';
			}else{
				$current_amt_min = 'N';
			}
		}    
        if($current_installments >= $account->set_as_min_from && $account->get_amt_in_schjoin == 0)
        {
            if($account->firstPayment_flexible == 0)
            {
				if($account->paid_installments == 0){ // No USE
					$account->min_amount = $account->min_amount;
					$account->max_amount = $account->min_amount;
				}
				else if($account->paid_installments > 1 && $account->paid_installments <= $account->set_as_max_from){ // No USE
					$account->min_amount = $account->min_amount;
}
            } 
            else if($account->firstPayment_flexible == 1)
            {
				if($account->paid_installments == 0){ // No USE
					$account->min_amount = $account->min_amount;
					$account->max_amount = $account->max_amount;
				}
				else if($account->currentmonthpaycount ==0 && $current_installments >= $account->paid_installments && $account->paid_installments <= $account->set_as_max_from)
				{
					$res = $this->db->query("select p.payment_amount,sa.id_scheme_account from payment p 
					left join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
					where p.id_scheme_account = '".$account->id_scheme_account."' order by id_payment asc limit 1");
					$payamount = $res->row_array();  
					$account->min_amount = $payamount['payment_amount'];
				}
            }
        }
        if($current_installments >= $account->set_as_max_from) // No USE
        {
            $account->min_amount = $account->min_amount;
             $account->max_amount = $account->min_amount;
        }
        
        //CJ scheme, Multiples of till paid as max,min
        if($account->dues_count > 0 && $current_installments + 1 >= $account->set_as_min_from && $current_installments +1 <= $account->set_as_max_from)
        {
            $res = $this->db->query("select p.payment_amount,sa.id_scheme_account from payment p 
                 left join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
                 where p.payment_status=1 and p.id_scheme_account = '".$account->id_scheme_account."' order by id_payment asc limit 1");
                 $payamount = $res->row_array();  
                 if($payamount['payment_amount'] > 0)
                 {
                    $account->min_amount = $account->dues_count * $payamount['payment_amount'];
                    $account->max_amount = $account->dues_count * $payamount['payment_amount']; 
                 }else{ // No USE
                      $account->min_amount = $account->min_amount;
                      $account->max_amount = $account->max_amount;
                 }
        }
	    return array(
						"min_amount"	=>	$account->min_amount,
						"max_amount"	=>	$account->max_amount
					);
	}
	
	function setAvgAsMaxLimit($account,$current_installments){
		// Previous Ins == Average calc installment
		if($current_installments-1 == $account->avg_calc_ins || $account->avg_payable > 0){
			if($account->avg_payable > 0){ // Already Average calculated, just set the value
				if($account->scheme_type == 1 && $account->is_flexible_wgt == 1 ){ // Weight - Flexible weight scheme
					// Set max payable
				}
				else if($account->scheme_type == 3 ){
					if($account->flexible_sch_type == 2){ // Flexible - Amount to weight [amount based]
						// Set max payable
					}
					elseif($account->flexible_sch_type == 3){ // Flexible - Amount to weight [weight based]
						$account->max_weight = $account->avg_payable;
					}						
				}
			}else{ // Calculate Average , set the value and updte it in schemme_account table
				$paid_sql = $this->db->query("SELECT sum(metal_weight) as paid_wgt,sum(payment_amount) as paid_amt FROM `payment` WHERE ( payment_status=1 or payment_status=2 ) AND id_scheme_account=".$account->id_scheme_account." GROUP BY YEAR(date_payment), MONTH(date_payment)");
				$paid_wgt = 0;
				$paid_amt = 0;
				$paid = $paid_sql->result_array();
				foreach($paid as $p){
					$paid_wgt = $paid_wgt + $p['paid_wgt'];
					$paid_amt = $paid_amt + $p['paid_amt'];
				}
				if($account->scheme_type == 1 && $account->is_flexible_wgt == 1 ){ // Weight - Flexible weight scheme
					// Set max payable
				}
				else if($account->scheme_type == 3 ){
					if($account->flexible_sch_type == 2){ // Flexible - Amount to weight [amount based]
						// Set max payable
					}
					elseif($account->flexible_sch_type == 3){ // Flexible - Amount to weight [weight based]
						$avg_payable = number_format($paid_wgt/$account->avg_calc_ins,3);
						$account->max_weight = $avg_payable;
					}						
				}
				$updData = array( "avg_payable" => $avg_payable, "date_upd" => date("Y-m-d") );
				$this->db->where('id_scheme_account',$account->id_scheme_account); 
				$this->db->update("scheme_account",$updData);
			} 
		}
		return array(
						"max_amount"	=>	$account->max_amount,
						"max_weight"	=>	$account->max_weight
					);	
	}
	
	function checkDuesData($account){
		$due_type		= "";
		$allowed_due	= 0;
		if($account->paid_installments > 0 || $account->totalunpaid >0){
			if($account->currentmonthpaycount == 0){  // current month not paid (allowed pending due + current due)
				if($account->allow_unpaid == 1){
					if($account->allow_unpaid_months > 0 && ($account->total_installments - $account->paid_installments) >=  $account->allow_unpaid_months && $account->totalunpaid >0){
						if(($account->total_installments - $account->paid_installments) ==  $account->allow_unpaid_months){
							$allowed_due =  $account->allow_unpaid_months ;  
						    $due_type = 'PD'; //  pending
						}
						else{
							$allowed_due =  $account->allow_unpaid_months+1 ;  
						    $due_type = 'PN'; // normal and pending
						}
					}
					else{
					     $allowed_due =  1;
					     $due_type = 'ND'; // normal due
					}
				}
				else{
					// current month not paid (allowed advance due + current due)
					if($account->allow_advance ==1){ // check allow advance
						if($account->advance_months > 0 && $account->advance_months <= ($account->total_installments - $account->paid_installments)){
							if(($account->total_installments - $account->paid_installments) ==  $account->advance_months){
									 $allowed_due =  $account->advance_months;
									 $due_type = 'AN'; // advance and normal
								}
								else{
									$allowed_due =  $account->advance_months+1 ;  
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
			else{ 	//current month paid
			    if($account->free_payment == 1 && $account->allowSecondPay == 1 && $account->paid_installments == 1){
					$allowed_due =  1 ;
					$due_type = 'AD'; // adv due
					$allowSecondPay = TRUE;
				}else{
				if($account->allow_unpaid == 1 && $account->allow_unpaid_months >0 && $account->totalunpaid >0 && ($account->currentmonthpaycount-1) < $account->allow_unpaid_months){  
					// can pay previous pending dues if attempts available 
					if($account->totalunpaid > $account->allow_unpaid_months){
						 $allowed_due =  $account->allow_unpaid_months ;
						 $due_type = 'PD'; // pending due
					}
					else{ 
						 $allowed_due =  $account->totalunpaid;
						 $due_type = 'PD'; // pending due
					}
				}
				else{  // check allow advance
				if($account->allow_advance == 1 && $account->advance_months > 0 && ($account->currentmonth_adv_paycount) < $account->advance_months){
						if(($account->advance_months + $account->paid_installments) <= $account->total_installments){
							 $allowed_due =  ($account->advance_months - ($account->currentmonth_adv_paycount));
							 $due_type = 'AD'; // advance due
						}
						else{
							 $allowed_due =  ($account->total_installments - $account->paid_installments);
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
			if($account->allow_advance ==1){ // check allow advance
				if($account->advance_months > 0 && $account->advance_months <= ($account->total_installments - $account->paid_installments)){
					if(($account->total_installments - $account->paid_installments) ==  $account->advance_months){
							 $allowed_due =  $account->advance_months;
							 $due_type = 'AN'; // advance and normal
						}
						else{
							$allowed_due =  $account->advance_months+1 ;  
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
		
		return array("allowed_due" => $allowed_due, "due_type" =>$due_type);
	}
	
	function  getAccNoFormat($record){
	   //scheme acc number format... 
	    if(	$record['is_lucky_draw'] == 1){
    	   	$record['chit_number']= 	$record['scheme_group_code'].'-'.$record['scheme_acc_number'];
    	}else{ 
        	if(	$record['schemeaccNo_displayFrmt'] == 0){   //only acc num
               $record['chit_number']= 	$record['scheme_acc_number'];
            }else if($record['schemeaccNo_displayFrmt'] == 1){ //based on acc number generation setting
                if($record['scheme_wise_acc_no']==0){
    				$record['chit_number'] =  	$record['scheme_acc_number'];
    			}else if($record['scheme_wise_acc_no']==1){
    				$record['chit_number']=  	$record['acc_branch'].'-'.$record['scheme_acc_number'];
    			}else if($record['scheme_wise_acc_no']==2){
    			    $record['chit_number'] =  $record['code'].'-'.$record['scheme_acc_number'];
    			}else if($record['scheme_wise_acc_no']==3){
    				$record['chit_number']=  $record['code'].$record['acc_branch'].'-'.$record['scheme_acc_number'];
    			}else if($record['scheme_wise_acc_no']==4){
    				$record['chit_number']=  $record['start_year'].'-'.$record['scheme_acc_number'];
    			}else if($record['scheme_wise_acc_no']==5){
    				$record['chit_number'] =  $record['start_year'].$record['code'].$record['scheme_acc_number'];
    			}else if($record['scheme_wise_acc_no']==6){
    				$record['chit_number'] =  $record['start_year'].$record['code'].$record['acc_branch'].'-'.$record['scheme_acc_number'];
    			}
            }else if($record['schemeaccNo_displayFrmt'] == 2){  
                
                //customised
              // $record['chit_number'] =  $record['scheme_acc_number'];
			    $id=$record['id_scheme_account'];
				  $acc_frmt_fromdb=$this->getFormatFromDB();
				 
				   $acc = $this->get_acc_Data($id);
				   $frmt_short_code=[];
				   if($acc_frmt_fromdb['custom_AccDisplayFrmt']!='' && $acc_frmt_fromdb['custom_AccDisplayFrmt']!=null)
				   {
					   $field_name = explode('@@', $acc_frmt_fromdb['custom_AccDisplayFrmt']);
					   for($i=1; $i < count($field_name); $i+=2) 
					   {
						   $frmt_short_code[]=$field_name[$i];
					   }
					   
					   $record['chit_number']=$this->getFormatedNumber($frmt_short_code,$acc);
					   
				   }
				   else
				   {
					   $record['chit_number'] = $acc['sch_AccNo'];
				   }
            }
    	}
    	return $record['chit_number'];
	}
	
	function getFormatFromDB()
	{
		$sql="SELECT custom_AccDisplayFrmt,custom_ReceiptDisplayFrmt,receiptNo_displayFrmt,schemeaccNo_displayFrmt from chit_settings where id_chit_settings=1";
		return $this->db->query($sql)->row_array();
	}
	
	function get_acc_Data($id){
	    /* get necessary data of scheme account by id... */
	    
	    $sql = "SELECT IFNULL(sa.scheme_acc_number,'Not Allocated') as sch_AccNo,IFNULL(sa.start_year,'') as start_year,b.short_name as branch_code,s.code,IFNULL(sa.group_code,'') as group_code,
	                if(s.is_lucky_draw = 1, CONCAT(s.code,'(',ifnull(sa.group_code,''),')') ,s.code) as scheme_code
                FROM scheme_account sa
                LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
                LEFT JOIN branch b ON (b.id_branch = sa.id_branch)
                WHERE sa.id_scheme_account = ".$id;
                
        return $this->db->query($sql)->row_array();  
	}
	
	function getFormatedNumber($frmt_short_code,$acc)
	{
		if($frmt_short_code!='' && $frmt_short_code!=null)
		{
			$finalFormat='';
			foreach($frmt_short_code as $code)
			{
				switch($code)
				{
					case 'br_code':
						$finalFormat.=$acc['branch_code'];
						break;
					case 'acc_num':
						$finalFormat.=$acc['sch_AccNo'];
						break;
					case 'sch_code':
						$finalFormat.=$acc['scheme_code'];
						break;
					case 'grp_code':
						$finalFormat.=$acc['group_code'];
						break;
					case 'fin_yr':
						$finalFormat.=$acc['start_year'];
						break;
					case 'rcpt_yr':
						$finalFormat.=$acc['receipt_year'];
						break;
					case 'rcpt_num':
						$finalFormat.=$acc['receipt_no'];
						break;
					case 'hyphen':
						$finalFormat.='-';
						break;
					case 'space':
						$finalFormat.=' ';
						break;
				}
				
			}

		}
		//$finalFormat=substr($finalFormat, 1);
		
		return $finalFormat;
	}
	
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
	
	function getLastTransaction($id_scheme_account)
	{
		$sql="Select no_of_dues,payment_amount,due_type,act_amount,payment_status from payment			
			  Where (payment_status=1 Or payment_status=2 Or payment_status=7)	
			         And id_scheme_account='$id_scheme_account'";
		return $this->db->query($sql)->row_array();	         
	}
	
	function amountPlan($acc){ // scheme_type = 0
		$allow_pay		= "N";
		$due_type		= "";
		$allowed_dues	= 0;
		$payable		= 0;
		
		return array(
				"allow_pay"		=> $allow_pay,
				"due_type"		=> $due_type,
				"allowed_dues"	=> $allowed_dues,
				"payable"		=> $payable,
				"min_amount"	=> 0,
				"max_amount"	=> 0,
				"min_weight"	=> 0,
				"max_weight"	=> 0			
				);
	}
	
	function weightPlan($acc){ // scheme_type = 1
		$allow_pay		= "N";
		$due_type		= "";
		$allowed_dues	= 0;
		$min_weight		= 0;
		$max_weight		= 0;
		
		return array(
				"allow_pay"		=> $allow_pay,
				"due_type"		=> $due_type,
				"allowed_dues"	=> $allowed_dues,
				"payable"		=> 0,
				"min_amount"	=> 0,
				"max_amount"	=> 0,
				"min_weight"	=> $min_weight,
				"max_weight"	=> $max_weight			
				);
	}
	
	function planTemplate($id_plan){
		$sql = $this->db->query("SELECT * from pp_plan_template where id_plan = ".$id_plan);
		return array(
					"data"		=> $sql->result_array(),
					"no_of_rows"=> $sql->num_rows()
					);
	}
	function insPlanTemplate($id_plan,$ins){
		$sql = $this->db->query("SELECT * from pp_plan_template where id_plan = ".$id_plan." and (".$ins." between ins_from and ins_to) "); 
		return array(
					"data"		=> $sql->row_array(),
					"no_of_rows"=> $sql->num_rows()
					);
	}
	
	function getFirstInsAmount($id_sch_ac){
		$sql = $this->db->query("SELECT 
									sum(payment_amount) as paid_amt 
								FROM payment
								WHERE 
								payment_status = 1 AND
								due_type = 'ND' AND
								id_scheme_account = ".$id_sch_ac." 
								GROUP BY YEAR(date_payment), MONTH(date_payment)
								ORDER BY date_payment ASC
								LIMIT 1");
		if($sql->num_rows() == 0){
			return 0;
		}else{
			return $sql->row()->paid_amt;
		}
	}
	
	
	function findInsTemplate($tmpData, $ins){
		$keyF = array_search($ins, array_column($tmpData, 'ins_from'));
		if($keyF > -1){
			$tData = $tmpData[$keyF];
		}else{
			$keyT = array_search($ins, array_column($tmpData, 'ins_to'));
			if($keyT > -1){
				$tData = $tmpData[$keyT];
			}
		}
		return $tData;
	}
	
	/**  
	*	CHECK ALLOW PAYMENT 
	* 
	* Advance and Pending not worked
	* Check paid <= total installments
	* Check allowed payment chances
	* IF multi payment change, Check paid <= total installments && allow pay till end of this month
	* Check pending allowed
	* Check advance allowed
	* 
	* Return's Y or N
	*/ 
	
	function checkPayChance($data){ // Check total installment && valid payment chances
		$havingChance	= FALSE;
		$messages		= "";
		if($data['paid_installments'] == $data['total_installments']){ // Reached Total Installments
			if($data['cur_chances_used'] < $data['pay_chances']){ // Not reached max limit
				$havingChance = TRUE;
			}else{
				$messages = "You have already paid all installments..";
			}
		}
		else if($data['paid_installments'] < $data['total_installments']){ // Yet to reach Total Installments
			$havingChance = TRUE;
		}else{
			$messages = "You have already paid all installments...";
		}
		return array(
						"havingChance"	=> $havingChance,
						"msg"			=> $messages
					);		
	}
	
	function checkAllowPay( $payData ){
		$messages	= "";
		$allowPay	= "N";						
		/* Check whether Installment payable reached */
		$payableReached	= FALSE;
		if($limit_by == 1){ // Amount
			
		}else if($limit_by == 2){  // Weight
			
		}
		return array(
						"allowPay"	=> $allowPay,
						"msg"		=> $messages
					);
	}
	
	function getCurrMonthPayData($id_sch_ac,$ins_no){
		$sql = $this->db->query("SELECT
								  installment, due_type,
								  COUNT(Date_Format(p.date_payment,'%Y%m')) as chances_used,
								  SUM(p.payment_amount) as total_amount,
								  SUM(p.metal_weight) as total_weight
								FROM payment p
								WHERE p.id_scheme_account = ".$id_sch_ac." AND p.payment_status <= 2 AND  '".date('Ym')."' = Date_Format(p.date_payment,'%Y%m')"
								.($ins_no > 0? 'AND p.installment = '.$ins_no:'')."
								GROUP BY year(date_payment),month(date_payment)
								ORDER BY year(date_payment),month(date_payment) ASC
								");
		return $sql->row_array();
	}
	function getCurrDayPayData($id_sch_ac){
		$sql = $this->db->query("SELECT
								  COUNT(Date_Format(p.date_payment,'%Y%m')) as chances_used,
								  SUM(p.payment_amount) as total_amount,
								  SUM(p.metal_weight) as total_weight
								FROM payment p
								WHERE p.id_scheme_account = ".$id_sch_ac." AND (p.payment_status=1 or p.payment_status=2 ) and  '".date('Y-m-d')."' = Date_Format(p.date_payment,'%Y-%m-%d')");
		return $sql->row_array();
	}
	
	function minMaxFormulaAmt($data){
		switch ($data['formula']){
			//1- Any, 2- X times of I1 inst, 3- Avg of I1 to I2, 4- I1 inst payment 5- X times of 1st payment
			case "1": // Any			
				$amount	= $data['amount'];
				break;
			case "4": // I1 inst pay
				$params	= explode(",",$data['param']);
				$I1		= $params[0];
				if($I1 == 1){ // 1st Installment amount
					$I1_amt	= $this->getFirstInsAmount($data['id_scheme_account']);
					$a	= max($I1_amt,$data['cur_amt_paid']);
					$b	= min($I1_amt,$data['cur_amt_paid']);
					$amount	= $a - $b;
				}				
				break;
		}		
		return $amount;
	}
	
	function getCurrMonthNo($start_date){
		$today = date("Y-m-d");

		$ts1 = strtotime($start_date);
		$ts2 = strtotime($today);

		$year1 = date('Y', $ts1);
		$year2 = date('Y', $ts2);

		$month1 = date('m', $ts1);
		$month2 = date('m', $ts2);

		$diff = (($year2 - $year1) * 12) + ($month2 - $month1);
		return $diff+1;
	}
	
	function checkAllowDisc($data){
		/**
		* 1. If customer pays on Start date + 1 month have to give discount.
		* 2. Should not give discount for pending or advance payment.
		* We are considering installment as month wise.
			Ex:  Start Date : 20th nov, we should give disc on every 20th
			1st ins 20th nov 
			2nd ins can pay from Dec 1, if customer pays between Dec 15th to 20th means we should give discount
		*/
		$start_date		= $data['start_date'];
		$cur_month_num  = $this->getCurrMonthNo($data['start_date']);
		if($data['current_installment'] == $cur_month_num){
			$addMonths 		= ($cur_month_num-1);
			$due_date 		= date('Y-m-d', strtotime("+".$addMonths." months", strtotime($start_date)));
			if($data['disc_days'] != NULL){
				$disc_begin_days= date('Y-m-d', strtotime("+".$data['disc_days']." days", strtotime($start_date)));
			}else{
				$disc_begin_days = $start_date;
			}
			$today = strtotime(date('Y-m-d'));
			if (($today >= strtotime($disc_begin_days)) && ($today <= strtotime($due_date))){
			    return TRUE;
			}else{
			    return FALSE;
			}
		}else{
			return FALSE;
		}
	}
	 
	
	function flexiblePlan($acc, $current_installments){ // scheme_type = 3
		$allow_pay		= "N";
		$due_type		= "";
		$allowed_dues	= 0;
		$min_amount		= 0;
		$max_amount		= 0;
		$min_weight		= 0;
		$max_weight		= 0;
		$denomination	= 0;
		$msg			= [];
		$discount = array(
						"apply_disc"	=> 0,
						"disc_by"		=> 0,
						"discount_val"	=> 0
						);
		if($acc->disable_payment == 0){
			//$templates = $this->planTemplate($acc->id_scheme);
			$template  = $this->insPlanTemplate($acc->id_scheme, $current_installments);
			if($template['no_of_rows'] > 0){
				$tData = $template['data'];
				$due_type		= "ND";
				//$tData = $this->findInsTemplate($templates['data'], $current_installments);
				if($tData['pay_chance_type'] == 1){ // Monthly
					$curMonthPaymt	= $this->getCurrMonthPayData($acc->id_scheme_account,0);
				}
				if($tData['pay_chance_type'] == 2){ // Daily
					$curMonthPaymt	= $this->getCurrDayPayData($acc->id_scheme_account);
				}			
				//echo "<pre>";echo$this->db->last_query();exit;
				$data = array(
								"current_ins"		=>	$current_installments,
								"id_scheme_account"	=>	$acc->id_scheme_account,
								"limit_by"			=>	$tData['limit_by'],
								"pay_chance_type"	=>	$tData['pay_chance_type'],
								"pay_chances"		=>	$tData['pay_chances'],
								"paid_installments"	=>	$acc->paid_installments,
								"total_installments"=>	$acc->total_installments,
								"cur_chances_used"	=>	$curMonthPaymt['chances_used'],
								"cur_amt_paid"		=>	$curMonthPaymt['total_amount']
								);
				$havingBlcPaymt = FALSE;
				if($tData['limit_by'] == 1){ // Limit by Amount				
					$data['amount']			= ($tData['max_formula'] == 1 ? $tData['max_param'] : 0 );
					$data["formula"]		= $tData['max_formula'];
					$data["param"]			= $tData['max_param'];
					$data["condition"]		= $tData['max_condition'];
					$data["condition_param"]= $tData['max_condition_param'];				
					$max_amount				= $this->minMaxFormulaAmt($data);
					
					$data['amount']			= ($tData['min_formula'] == 1 ? $tData['min_param'] : 0 );
					$data["formula"]		= $tData['min_formula'];
					$data["param"]			= $tData['min_param'];
					$data["condition"]		= $tData['min_condition'];
					$data["condition_param"]= $tData['min_condition_param'];
					$min_amount_val			= $this->minMaxFormulaAmt($data);				
					$min_amount				= $min_amount_val > $max_amount ? ($min_amount_val-$max_amount) : $min_amount_val ;
					
					$data['min_amount']		= $min_amount;
					$data['max_amount']		= $max_amount;	
					$payable				= $max_amount;				
					$havingBlcPaymt 		= ( $max_amount == 0 ? FALSE : TRUE ); // Check payable reached
				}
				else if($tData['limit_by'] == 2){ // Limit by Weight
					$data['weight']		= ($tData['max_formula'] == 1 ? $tData['min_param'] : 0 );
					$min_weight			= $this->minMaxFormulaWgt($data);	
					$data['weight']		= ($tData['min_formula'] == 1 ? $tData['max_param'] : 0 );
					$max_weight			= $this->minMaxFormulaWgt($data);
					$data['min_weight']	= $min_weight;
					$data['max_weight']	= $max_weight;
					$payable			= $max_weight;	
					$havingBlcPaymt 	= ( $max_weight == 0 ? FALSE : TRUE ); // Check payable reached
				}	
					
				if( $havingBlcPaymt ){
					$chanceData			= $this->checkPayChance($data);
					$havingPayChance	= $chanceData['havingChance']; 	
					$msg['chanceData']	= $chanceData['msg']; 	
					
					if($havingPayChance){						
						if(!empty($acc->maturity_date)){
							if(strtotime(date("Y-m-d")) <= strtotime($acc->maturity_date)){
								$allow_pay 		= "Y";
							}
							$allowed_dues	= 1;
						}
					}
				}
				
				if($allow_pay == "Y"){
					// Denomination
					if($tData['denom_type'] == 1){ // N/A
						$denomination = NULL;
					}else if($tData['denom_type'] == 2){ // Mutiples
						$denomination = $tData['denom_value'];
					}
					
					// Discount
					$apply_disc = $tData['apply_disc'];
					if($apply_disc == 1){
						$discData = array(
										"disc_days"				=> $acc->disc_days,
										"start_date"			=> $acc->start_date,
										"total_installment"		=> $acc->total_installments,
										"paid_ins"				=> $acc->paid_installments,
										"current_installment"	=> $current_installments
									);
						$this->checkAllowDisc($discData);
					}
					$discount = array(
									"apply_disc"	=> $apply_disc,
									"disc_by"		=> $tData['disc_by'],
									"discount_val"	=> $tData['disc_value']
								);
				}
				
						
			}else{
				$errors[] = "Invalid plan template";
			}
		}else{
			$msg['info'] = "Payment Disabled for this Purchase plan";
		}	
		$returnData = array(
						"errors"			=> $msg,
						"allow_pay"			=> $allow_pay,
						"due_type"			=> $due_type,
						"allowed_dues"		=> $allowed_dues,
						"payable"			=> $payable,
						"min_amount"		=> $min_amount,
						"max_amount"		=> $max_amount,
						"min_weight"		=> $min_weight,
						"max_weight"		=> $max_weight,		
						"denomination"		=> $denomination,		
						"discount"			=> $discount,	
						//"havingBlcPaymt"=> $havingBlcPaymt,		
						//"chanceData"	=> $chanceData,		
						//"account"		=> $acc,		
						//"curMonthData"	=> $curMonthPaymt,		
					);
		//echo "<pre>";print_r($returnData);exit;
		return $returnData;
	}
	
	function getCusBasicData($id_customer){
		$sql = $this->db->query("SELECT if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as cus_name, mobile FROM customer c WHERE c.id_customer = ".$id_customer);
		return $sql->row_array();
	}
	
	function getCusAccounts($id_customer){ 
		// Get active =1, closed = 0, no digi gold, not enquiry
		$resultArr = array("no_of_rows" => 0,"records" => []);
		$sql = $this->db->query("SELECT 
						sa.id_scheme_account, sa.ref_no as client_id, sa.account_name, sa.start_date, sa.maturity_date, b.name as ac_branch, sa.date_add, sa.added_by, sa.group_code,
						ifnull(sa.scheme_acc_number ,'".Globals::$default_acno_label."') as scheme_acc_number, sa.start_year,	sa.disable_payment,					
						sa.is_opening,
						IFNULL(sa.paid_installments,0) as op_bal_paid_ins,
						IFNULL(sa.balance_amount,0) as op_bal_balance_amt,
						IFNULL(sa.balance_weight,0) as op_bal_balance_wgt,
						IFNULL(sa.last_paid_weight,0) as op_bal_last_paid_wgt,
						IFNULL(sa.last_paid_chances,0) as op_bal_last_paid_chances,
						sa.last_paid_date as op_bal_last_paid_date,
						sa.avg_wgtlimit, sa.firstPayment_amt, sa.firstpayment_wgt, sa.converted_amt, sa.received_wgt, sa.fixed_metal_rate, sa.fixed_wgt, sa.fixed_rate_on, sa.rate_fixed_in, sa.avg_payable,
						s.scheme_type, s.installment_cycle, s.payment_chances, s.maturity_type, s.apply_template, s.total_installments, s.disc_days, s.id_scheme, s.trans_limit_for,
						s.allow_unpaid_in, s.allow_unpaid, s.unpaid_months,
						s.allow_advance_in, s.allow_advance, s.advance_months
					FROM scheme_account sa
						LEFT JOIN branch b on b.id_branch = sa.id_branch
						LEFT JOIN scheme s on sa.id_scheme = s.id_scheme
					WHERE  
						sa.id_customer = ".$id_customer."
						AND sa.active = 1 AND sa.is_closed = 0 AND s.is_digi = 0 AND s.is_enquiry = 0");
		if($sql->num_rows() > 0){
			$records	= $sql->result_array();
			$customer	= $this->getCusBasicData($id_customer);
			foreach($records as $k => $v){
				$records[$k]['cus_name']= $customer['cus_name'];
				$records[$k]['mobile']	= $customer['mobile'];
				if($records[$k]['installment_cycle'] == 0){ // Monthly
					if($records[$k]['maturity_type'] == 1 || $records[$k]['maturity_type'] == 3){ // 1-Flexible, 3-Fixed Flexible 
						$records[$k]['payData'] = $this->monthly_flexible($records[$k]);
					}
					else if($records[$k]['maturity_type'] == 2){ // 2-Fixed 
						//$records[$k]['payData'] = $this->monthly_fixed($records[$k]);
					}
				}
			}
			$resultArr = array("no_of_rows" => $sql->num_rows(),"records" => $records);
		}
		
		echo "<pre>";print_r($resultArr);exit;
		//echo $this->db->last_query();exit;
		
		return $resultArr;
	}
	
	/* Can pay only 1 due at a time.
	*	Multi chance will be allowed only for the current payment. [Now worked for all] 
	*   Advance, Pending
	* 	Whether i should allow current month payment if advance already paid, wt should be the due type ?
	**/
	function monthly_flexible($ac){
		$allow_pay				= "N";
		$due_type				= "";
		$id_sch_ac				= $ac['id_scheme_account'];
		$unpaid_dues			= 0;
		$ac["installment_no"]	=	0;
		$ac["paid_installments"]=	0;
		if($ac['disable_payment'] == 0){
			$ac["paid_installments"]	= $this->getPaidIns($id_sch_ac);
			if($ac["paid_installments"] == 0){
				$ac["installment_no"]	= 1;
				$due_type		= 'ND';
			}else{
				// Check Unpaid/Pending dues				
				$paidData = [];
				if($ac['trans_limit_for'] == 1){ // Month
					$paidData	= $this->getAcPaidData($id_sch_ac,'All'); // Returns paid list by installment [last as first record]
				}	  
				$months_till_date	= $this->getCurrMonthNo($ac['start_date']);
				if($ac["paid_installments"] < $months_till_date){
					$unpaid_dues	= ($months_till_date - $ac["paid_installments"]);
				}
				$PD_allowed = (($unpaid_dues > 0 && ($ac['allow_unpaid_in'] == 4 || $ac['allow_unpaid_in'] == 2) && $ac['allow_unpaid'] == 1 && $ac['unpaid_months'] > 0) ? TRUE : FALSE) ;
				/*echo "\n paid_ins :".$ac["paid_installments"];
				echo "\n months_till_date :".$months_till_date;
				echo "\n unpaid_dues :".$unpaid_dues;
				echo "\n is_PD_allowed :".$PD_allowed;*/
				//echo "\n Payment :<pre>";print_r($paidData);
				if(sizeof($paidData) > 0){
					$thisMonthData = filterArrByKeyVal($paidData,'paid_year_month',date("Y-m"));
					if(sizeof($thisMonthData) > 0){ // Having payment entry for this month
						return $this->checkInsDueData($ac,$thisMonthData,$PD_allowed);	
					}
					else{ // No payments yet for this month
						if($PD_allowed){
							$due_type = "PD";
						}else{
							$due_type = 'ND';
						}
						$ac["installment_no"]	+= 1;
					}					
				}else{ // No installments paid
					$ac["installment_no"]	= 1;
					$due_type		= 'ND';
				}				
				//echo "<pre>";echo$this->db->last_query();exit; 
			}
			$dueData = $this->checkDuePayment($ac);
			/*echo "\n due_type :".$due_type;
			echo "\n paid_ins :".$ac["paid_installments"]; 
			echo "\n installment_no :".$ac["installment_no"];*/
		}else{
			$msg['info'] = "Payment Disabled for this Purchase plan";
		}
		//echo "<pre>";print_r($dueData);exit;
		return array(
						"message"			=> $msg, 
						"due_type"			=> $due_type, 
						"installment_no"	=> $ac["installment_no"],
						"paid_installments" => $ac["paid_installments"],
						"dueData" 			=> $dueData
						);
	}
	
	function checkInsDueData($ac,$tmData,$is_PD_allowed){
		//echo "\n This Month Payment :<pre>";print_r($tmData);
		// Check last payment chance , check allow pay, set min and max, discount
		$lastPayment = $tmData[0];
		$ac["installment_no"]	= $lastPayment['installment'];
		$ac["cur_chances_used"]	= $lastPayment['chances_used'];
		$ac["cur_amt_paid"]		= $lastPayment['total_amount'];
		$ac["cur_wgt_paid"]		= $lastPayment['total_weight'];
		$dueData = $this->checkDuePayment($ac);
		if($dueData['allow_pay'] == 'Y'){
			return array(
						"message"			=> [],
						"due_type"			=> $lastPayment['due_type'], 
						"installment_no"	=> $lastPayment['installment'],
						"paid_installments" => $ac["paid_installments"],
						"dueData" 			=> $dueData
						);
		}
		else{
			$tmPDpaid = sizeof(filterArrByKeyVal($tmData,'due_type','PD'));
			$tmNDpaid = sizeof(filterArrByKeyVal($tmData,'due_type','ND'));
			$tmADpaid = sizeof(filterArrByKeyVal($tmData,'due_type','AD'));
			
			/*echo "\n tmPDpaid :".$tmPDpaid;
			echo "\n tmNDpaid :".$tmNDpaid;
			echo "\n tmADpaid :".$tmADpaid;*/
			
			$AD_allowed = ((($ac['allow_unpaid_in'] == 4 || $ac['allow_unpaid_in'] == 2) && $ac['allow_advance'] == 1 && $ac['advance_months'] > 0) ? TRUE : FALSE) ;
			
			if($is_PD_allowed && $tmPDpaid < $ac['unpaid_months']){ // Pending allowed
				$due_type = "PD";
				$ins_no   = $ac["paid_installments"]+1;
			}
			else if($tmNDpaid == 0){ // No ND payment for this month
				$due_type = "PD";
				$ins_no   = $ac["paid_installments"]+1;
			}
			else if($AD_allowed && $tmADpaid  < $ac['advance_months']){ // Advance allowed
				$due_type = "AD";
				$ins_no   = $ac["paid_installments"]+1;
			}else{
				return array(
						"message"			=> [],
						"due_type"			=> "", 
						"ins_no" 			=> 0,
						"paid_installments" => $ac["paid_installments"],
						"dueData" 			=> array("allow_pay" => "N")
						);
			}
			$ac["installment_no"]	= $ins_no;
			$ac["cur_chances_used"]	= 0;
			$ac["cur_amt_paid"]		= 0;
			$ac["cur_wgt_paid"]		= 0;
			$dueData = $this->checkDuePayment($ac);
			//echo "<pre>";print_r($dueData);exit;
			return array(
						"message"			=> "", 
						"due_type"			=> $due_type, 
						"installment_no"	=> $ins_no,
						"paid_installments" => $ac["paid_installments"],
						"dueData" 			=> $dueData
						);
		}
	}
	
	function checkDuePayment($ac){
		$allow_pay 		= "";
		$allowed_dues	= 0;
		$payable		= 0;
		$min_amount		= 0;
		$max_amount		= 0;
		$min_weight		= 0;
		$max_weight		= 0;
		$denomination	= 0;
		$msg			= [];
		$discount 		= array(
							"apply_disc"	=> 0,
							"disc_by"		=> 0,
							"discount_val"	=> 0
							);
		$template	= $this->insPlanTemplate($ac['id_scheme'], $ac['installment_no']);
		//echo "<pre>";print_r($ac);
		if($template['no_of_rows'] > 0){
			$tData = $template['data'];
			$data = array(
							"current_ins"		=>	$ac['installment_no'],
							"id_scheme_account"	=>	$ac['id_scheme_account'],
							"limit_by"			=>	$tData['limit_by'],
							"pay_chance_type"	=>	$tData['pay_chance_type'],
							"pay_chances"		=>	$tData['pay_chances'],
							"paid_installments"	=>	$ac['paid_installments'],
							"total_installments"=>	$ac['total_installments'],
							"cur_chances_used"	=>	isset($ac['cur_chances_used'])?$ac['cur_chances_used']:0
							);
			$chanceData			= $this->checkPayChance($data);
			$havingPayChance	= $chanceData['havingChance']; 	
			$msg['chanceData']	= $chanceData['msg']; 	
			
			if($havingPayChance){						
				if(!empty($acc->maturity_date)){
					if(strtotime(date("Y-m-d")) <= strtotime($ac['maturity_date'])){
						$allow_pay 	= "Y";
					}else{
						$allow_pay 	= "N";
					}
				}else{
					$allow_pay 	= "Y";
				}
			}else{
				$allow_pay 	= "N";
			}
			
			if($allow_pay != 'N' && $havingPayChance){
				$havingBlcPaymt = FALSE;
				$data["limit_by"]		= $tData['limit_by'];
				$data['value']			= ($tData['max_formula'] == 1 ? $tData['max_param'] : 0 );
				$data["formula"]		= $tData['max_formula'];
				$data["param"]			= $tData['max_param'];
				$data["condition"]		= $tData['max_condition'];
				$data["condition_param"]= $tData['max_condition_param'];
				$data["cur_paid"]		= isset($ac['cur_amt_paid'])?$ac['cur_amt_paid']:0;
				$data['max_val']		= ($tData['max_formula'] == 1 ? $tData['max_param'] : 0 );
				$max_limit				= $this->minMaxLimitCalc($data);
				$payable				= $max_limit;
				$havingBlcPaymt 		= ( $max_limit == 0 ? FALSE : TRUE ); // Check payable reached
				
				if($havingBlcPaymt){
					$data['value']			= ($tData['min_formula'] == 1 ? $tData['min_param'] : 0 );
					$data["formula"]		= $tData['min_formula'];
					$data["param"]			= $tData['min_param'];
					$data["condition"]		= $tData['min_condition'];
					$data["condition_param"]= $tData['min_condition_param'];
					$data["cur_paid"]		= isset($ac['cur_wgt_paid'])?$ac['cur_wgt_paid']:0;
					$min_val				= $this->minMaxLimitCalc($data);
					$min_limit				= $min_val > $max_limit ? ($min_val-$max_limit) : $min_val ;
				}else{
					$min_limit				= 0;
				}
				if($tData['limit_by'] == 1){ 
					$data['min_amount']		= $min_limit;
					$data['max_amount']		= $max_limit;	
				}else if($tData['limit_by'] == 2){ 
					$data['min_weight']		= $min_limit;
					$data['max_weight']		= $max_limit;
				}
				if( $havingBlcPaymt ){
					$allow_pay 	= "Y";
				}else{
					$allow_pay 	= "N";
					$msg['havingBlcPayment'] = "You have already paid your installment max amount";
				}
			}
							
			if($allow_pay == "Y"){
				// Denomination
				if($tData['denom_type'] == 1){ // N/A
					$denomination = NULL;
				}else if($tData['denom_type'] == 2){ // Mutiples
					$denomination = $tData['denom_value'];
				}
				
				// Discount
				$apply_disc = $tData['apply_disc'];
				if($apply_disc == 1){
					$discData = array(
									"disc_days"				=> $ac['disc_days'],
									"start_date"			=> $ac['start_date'],
									"total_installment"		=> $ac['total_installments'],
									"paid_ins"				=> $ac['paid_installments'],
									"current_installment"	=> $ac['installment_no']
								);
					$this->checkAllowDisc($discData);
				}
				$discount = array(
								"apply_disc"	=> $apply_disc,
								"disc_by"		=> $tData['disc_by'],
								"discount_val"	=> $tData['disc_value']
							);
			}	
		}else{
			$errors[] = "Invalid plan template";
		}
		$returnData = array(
						"errors"			=> $msg,
						"allow_pay"			=> $allow_pay,
						"payable"			=> $payable,
						"min_amount"		=> $min_amount,
						"max_amount"		=> $max_amount,
						"min_weight"		=> $min_weight,
						"max_weight"		=> $max_weight,		
						"denomination"		=> $denomination,		
						"discount"			=> $discount,	
						//"havingPayChance"	=> isset($havingPayChance)?$havingPayChance:NULL,		
						//"havingBlcPaymt"	=> isset($havingBlcPaymt)?$havingBlcPaymt:NULL,		
						//"chanceData"		=> isset($chanceData)?$chanceData:NULL,		
						//"account"			=> $ac,				
					);
		//echo "<pre>";print_r($returnData);exit;
		return $returnData;
	}
	
	function minMaxLimitCalc($data){
		switch ($data['formula']){
			//1- Any, 2- X times of I1 inst, 3- Avg of I1 to I2, 4- I1 inst payment 5- X times of 1st payment
			case "1": // Any			
				$limit	= $data['value'];
				break;
			case "4": // I1 inst pay
				$params	= explode(",",$data['param']);
				$I1		= $params[0];
				$paidDet= $this->getInsPaidData($data['id_scheme_account'],1);
				if($data['limit_by'] == 1){ // Amount
					$paid = $paidDet['paid_amt'];
				}
				else if($data['limit_by'] == 2){ // Weight
					$paid = $paidDet['paid_wgt'];
				}
				$a	= max($paid,$data['cur_paid']);
				$b	= min($paid,$data['cur_paid']);
				$limit	= $a - $b;				
				break;
		}		
		return $limit;
	}
	
	function getAcPaidData($id_sch_ac,$ins_no){ // Flexible, fixed flexible maturity && monthly cycle
		$sql = $this->db->query("SELECT
								  installment, due_type, CONCAT(year(date_payment),'-',month(date_payment)) as paid_year_month,
								  COUNT(Date_Format(p.date_payment,'%Y%m')) as chances_used,
								  SUM(p.payment_amount) as total_amount,
								  SUM(p.metal_weight) as total_weight
								FROM payment p
								WHERE p.id_scheme_account = ".$id_sch_ac." AND p.payment_status <= 2 "
								.($ins_no > 0? 'AND p.installment = '.$ins_no:'')."
								GROUP BY year(date_payment),month(date_payment),installment
								ORDER BY installment DESC
								");
		return $sql->result_array(); 
	} 
	
	function getInsPaidData($id_sch_ac,$ins_no){
		$sql = $this->db->query("SELECT 
									sum(payment_amount) as paid_amt,
									sum(metal_weight) as paid_wgt
								FROM payment
								WHERE 
								payment_status <= 2 AND
								id_scheme_account = ".$id_sch_ac." AND
								installment = ".$ins_no." 
								GROUP BY installment");
		if($sql->num_rows() == 0){
			return 0;
		}else{
			return $sql->row_array();
		}
	}
	
	function getPaidIns($id_sch_ac){
		$sql = $this->db->query("SELECT max(installment) as paid_ins from payment where payment_status <=2 AND id_scheme_account =". $id_sch_ac);
		if($sql->num_rows() > 0){
			return $sql->row('paid_ins');
		}else{
			return 0;
		}
	}
}
?>