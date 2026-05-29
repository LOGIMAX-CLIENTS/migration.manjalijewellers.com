<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');
class Digigold_modal extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('mobileapi_model');
        $this->load->helper('metal_wgt_digit');
	}
	function get_CusKycData($id_customer)
	{
		$sql = $this->db->query("select kyc_type,number,name,status,
								LPAD(RIGHT(number, 4), CHAR_LENGTH(number), 'X') AS masked_doc_number,
								if(kyc_type = 2,'PAN',if(kyc_type = 3, 'AADHAR',if(kyc_type = 1,'BANK','-'))) as doc_type
								from kyc where status != 3 and id_customer = " . $id_customer);
		$data = $sql->result_array();
		return $data;
	}
	function digiGold_settings($id_scheme = '')
	{
		$sql = $this->db->query("SELECT s.id_scheme,s.scheme_name,s.code,s.sync_scheme_code,s.id_metal,s.id_classification,s.scheme_type,s.total_installments,s.min_chance,
	                            s.max_chance,s.payment_chances,s.interest,s.min_amount,s.max_amount,s.pay_duration,s.wgt_convert,s.maturity_days,s.flx_denomintion,
	                            s.flexible_sch_type,s.is_digi,s.total_days_to_pay,cls.classification_name,cls.description,cs.is_branchwise_rate,s.digi_target,s.digi_target_split_unit,
	                            if(s.digi_target_split_unit = 1,'1',if(s.digi_target_split_unit = 2, '7',if(s.digi_target_split_unit = 3, '30','0'))) as digi_target_split_value,
	                            if(s.digi_target_split_unit = 1,'Every Day',if(s.digi_target_split_unit = 2, 'Every Week',if(s.digi_target_split_unit = 3, 'EVery Month',''))) as digi_target_split_term,
	                            s.flx_denomintion,s.description as know_more_description,s.key_benifits_description, 
								s.is_pan_required,s.is_aadhaar_required,s.aadhaar_required_amt, s.pan_req_amt,cs.branch_settings,cs.is_branchwise_cus_reg,cs.branchwise_scheme, s.id_purity, m.metal
	                            FROM scheme s
	                            LEFT JOIN sch_classify cls ON cls.id_classification = s.id_classification
	                            JOIN chit_settings cs
								JOIN metal m ON
									m.id_metal = s.id_metal
	                            WHERE s.is_digi = 1 and s.total_days_to_pay > 0 and s.active = 1 and (s.visible = 1 or s.visible = 0)
								" . (empty($id_scheme) ? '' : "and s.id_scheme = " . $id_scheme));
		$data = $sql->result_array();

        foreach ($data as $key => $row) {
        
            if ($row['branch_settings'] == 1) {
        
                if ($row['is_branchwise_cus_reg'] == 0) {
        
                    if ($row['branchwise_scheme'] == 1) {
        
                        $sch_branch = $this->db->query(
                            "SELECT id_branch FROM scheme_branch WHERE id_scheme = ?",
                            [$row['id_scheme']]
                        );
        
					$schBrData = $sch_branch->result_array();
        
                        if ($sch_branch->num_rows() == 1) {
                            $data[$key]['askBranch'] = 0;
                            $data[$key]['id_branch'] = $schBrData[0]['id_branch'];
                        } 
                        elseif ($sch_branch->num_rows() > 0) {
                            $data[$key]['askBranch'] = 1;
                        }
        
						//CLIENT SPECIFIC REQ FOR CJ
                        // $data[$key]['sch_branch'] = $schBrData;
						// $data[$key]['askBranch'] = 0;
        
				} else {
                        $data[$key]['askBranch'] = 1;
                    }
				}
			}
		}
		return $data;
	}
	function digiGold_account($id_customer, $id_scheme = '')
	{
		$sql = $this->db->query("SELECT s.is_digi,s.min_chance,s.max_chance,sa.id_scheme_account,sa.id_scheme,date_format(sa.start_date, '%d-%b-%Y') as start_date,sa.id_branch,
	                            IF(CURDATE() = date(sa.start_date), 1, DATEDIFF(CURDATE(),date(sa.start_date))) as date_difference,CURDATE() as cur_date, 
		                        DATE_ADD(date(sa.start_date), INTERVAL s.total_days_to_pay DAY) as allow_pay_till,IFNULL(sa.account_name,c.firstname) as account_name,
								IFNULL((SELECT count(p.id_payment) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1 AND date(p.date_payment) = curdate()),0) as curday_total_paid_count,
		                        CONCAT(sa.start_year,'-',s.code,'-',IFNULL(sa.scheme_acc_number,'Not Allocated')) as scheme_acc_number,
		                        COUNT(p.id_payment) as pay_count,sa.id_branch as joined_branch,sa.active, sa.is_closed,
		                        date_format((DATE_ADD(date(sa.start_date), INTERVAL s.total_days_to_pay DAY)),'%d-%b-%Y') as oldmaturity_date,IFNULL(date_format(sa.maturity_date,'%d-%b-%Y'),'') as maturity_date,
 		                        IFNULL(SUM(p.payment_amount),'') as total_paid_amount,
		                        IFNULL(SUM(p.metal_weight),0) as total_paid_weight,
		                        IFNULL(SUM(p.saved_benefits),0) as total_saved_benefits,
		                        IFNULL((SUM(p.metal_weight) + SUM(p.saved_benefits)),'') as total_saved, 
		                        if(sa.dg_target_value_wgt > 0,sa.dg_target_value_wgt,'Target not set') entered_target,sa.dg_target_value_wgt,sa.total_paid_ins,
								IFNULL(SUM(p.dg_other_benefit_wgt), '') AS other_benefit_wgt,
								IFNULL(SUM(p.dg_other_benefit_amt), '') AS other_benefit_amt
                                FROM scheme_account sa
                                LEFT JOIN customer c ON (c.id_customer = sa.id_customer)
                                LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
                                LEFT JOIN payment p ON (p.id_scheme_account = sa.id_scheme_account and p.payment_status = 1)
                               WHERE sa.id_customer =" . $id_customer . "  AND s.is_digi = 1 AND sa.is_closed = 0 AND sa.active = 1 and (s.visible =1 or s.visible = 0) 
							   " . (empty($id_scheme) ? '' : "and sa.id_scheme = " . $id_scheme) . "
                                GROUP BY p.id_scheme_account
	                            ");
		$data = $sql->row_array();

		 if($sql->num_rows() > 0){
		 	$data['scheme_acc_number'] = $this->format_accRcptNo('Account',$data['id_scheme_account']);
			$data['total_paid_weight'] = formatMetalWeight($data['total_paid_weight']);
			$data['total_saved_benefits'] = formatMetalWeight($data['total_saved_benefits']);
			$data['total_saved'] = formatMetalWeight($data['total_saved']);
		}
		return $data;
	}
	function digi_payDues($acc, $id_scheme = '')
	{
		$result = [];
		$date = date('Y-m-d');
		$digi = $this->digiGold_settings($id_scheme);
		$metal_rates = $this->mobileapi_model->get_metalrate($acc['joined_branch'], $digi['is_branchwise_rate']);
		//$digi_payments = $this->db->query('SELECT ')->row_array();
		foreach ($digi as $digi_item) {
			$digi = $digi_item;
			$rateName = $this->mobileapi_model->get_rate_name($digi['id_metal'], $digi['id_purity']);
			// print_r($rateName['rate']);exit;
			$rateKey = $rateName['rate_field'];
			// print_r($rateKey);exit;
		if ($date <= $acc['allow_pay_till']) {
			$result = array(
				'digi_allow_pay' => (($acc['curday_total_paid_count'] != 0 && $acc['curday_total_paid_count'] >= $acc['max_chance']) ? 'N' : 'Y'),
				'payable' => $digi['min_amount'],
				'min_amount' => $digi['min_amount'],
				'max_amount' => $digi['max_amount'],
					'min_weight' => formatMetalWeight(($digi['min_amount'] / $metal_rates[$rateKey])),
					'max_weight' => formatMetalWeight(($digi['max_amount'] / $metal_rates[$rateKey])),
				'flx_denomination' => $digi['flx_denomintion'],
				'wgt_convert'      => $digi['wgt_convert'],
				/*'total_paid_amount' => $acc['total_paid_amount'],
	                        'total_paid_weight' => $acc['total_paid_weight'],
	                        'total_benefits_earned' => $acc['total_saved_benefits'],
	                        'total_saved'   => number_format(($acc['total_paid_weight'] + $acc['total_saved_benefits']),3),*/
					'metal_rate'                => $metal_rates[$rateKey],
				// 'metal_rate_updatetime'		=> $metal_rates['updatetime'],
				'metal_rate_updatetime'		=> date("d-m-Y", strtotime($metal_rates['updatetime'])),
				'allow_benefit_calc' => $digi['interest']
			);
		} else {
			$result = array(
				'digi_allow_pay' => 'N',
				'payable' => $digi['min_amount'],
				'min_amount' => $digi['min_amount'],
				'max_amount' => $digi['max_amount'],
					'min_weight' => formatMetalWeight(($digi['min_amount'] / $metal_rates[$rateKey])),
					'max_weight' => formatMetalWeight(($digi['max_amount'] / $metal_rates[$rateKey])),
				'flx_denomination' => $digi['flx_denomintion'],
				'wgt_convert'      => $digi['wgt_convert'],
				'total_paid_amount' => $acc['total_paid_amount'],
	                        'total_paid_weight' => $acc['total_paid_weight'],
	                        'total_benefits_earned' => $acc['total_saved_benefits'],
	                        'total_saved'   => formatMetalWeight($acc['total_paid_weight'] + $acc['total_saved_benefits']),
					'metal_rate'                => $metal_rates[$rateKey],
				// 'metal_rate_updatetime'		=> $metal_rates['updatetime'],
				'metal_rate_updatetime'		=> date("d-m-Y", strtotime($metal_rates['updatetime'])),
				'allow_benefit_calc' => $digi['interest']
			);
			//$result = array('digi_allow_pay' => 'N');
			}
		}
		return $result;
	}
	function get_digi_benefit($res)
	{
		$sql_int = $this->db->query("SELECT interest_type,interest_value, IF(interest_type = 0,'%','INR') as int_symbol, commodity 
				FROM `scheme_benefit_deduct_settings` 
				WHERE ('" . $res['date_difference'] . "' BETWEEN installment_from AND installment_to) AND id_scheme=" . $res['id_scheme'] . "
    			");
		// print_r($this->db->last_query());exit;
		return $sql_int->result_array();
	}
	function getCusDigiData($data)
	{
		$result = [];
		$digi = [];
		$digi_account = [];
		$digi_account['paydues'] = [];
		$digi_account['benefit_calc'] = [];
		$date = date('Y-m-d');
		//check digi gold scheme available...
		$digi = $this->digiGold_settings($data['id_scheme'] ?? '');
		if (sizeof($digi) > 0) {
			foreach ($digi as $item) {
				$digi = $item;
				if ($digi['branch_settings'] == 1) {
					if ($digi['is_branchwise_cus_reg'] == 0) {
						if ($digi['branchwise_scheme'] == 0) {
							$digi['askBranch'] = 1;
							$branch_data = $this->mobileapi_model->get_branch();
							if (count($branch_data) == 1) {
								$digi['id_branch'] = $branch_data[0]['id_branch'];
							}
						}
					}
				}
			$digi['benefit_chart_data'] = [];
			$digi['benefit_day_start'] = '';
			$digi['benefit_day_end'] = '';
			$digi['benefit_day_current'] = '';
			$digi['benefit_slab_count'] = '';
			$digi['benefit_progress_percent'] = '';
			//check customer has active digi gold account...
				$digi_account = $this->digiGold_account($data['id_customer'], $digi['id_scheme']);
			if (sizeof($digi_account) > 0) {
				$digi_account['total_saved']   = formatMetalWeight($digi_account['total_saved']);
				$digi_account['total_saved_benefits']   = formatMetalWeight($digi_account['total_saved_benefits']);
			//  print_r($this->db->last_query());exit;
				$rateName = $this->mobileapi_model->get_rate_name($digi['id_metal'], $digi['id_purity']);
				$rateKey = $rateName['rate_field'];
			$metal_rates = $this->mobileapi_model->get_metalrate($digi_account['joined_branch'], $digi['is_branchwise_rate']);
				$digi['metal_rate'] = $metal_rates[$rateKey];
				if($digi['is_pan_required'] == 1 || $digi['is_pan_required'] == 2){
					if(empty($pan_no)){
						$get_pan = TRUE;
						$is_pan_required = 1;
					}
				}
				$digi['is_pan_req'] = $is_pan_required;
				$digi['get_pan'] = $get_pan;
				if ($digi['metal_rate'] == 0 || empty($digi['metal_rate'])) {
					$result[] = array(
						'status' => FALSE,
						'show_digi' => FALSE,
						'allow_join' => FALSE,
						'msg' => 'Metal rate not available currently...Kindly contact admin...',
						'sch_data' => $digi,
						'acc_data' => []
					);
					continue;
				}
			// Code updated by Karthikai kumaran 05/11/2025 updated to id_scheme is static to dynamic
			$reached_day = (!empty($digi_account['date_difference']) ? $digi_account['date_difference'] : 1);
			$id_scheme = (!empty($digi['id_scheme']) && $digi['id_scheme'] > 0 && isset($digi['id_scheme']) ? $digi['id_scheme'] : '');
			$digi['benefit_chart_data'] = $this->digi_benefit_chart_data($reached_day, $id_scheme);
			//echo '<pre>';print_r($currentInterest);exit;
			//   print_r($this->db->last_query());exit;
            if($digi['interest'] == 1 && sizeof($digi['benefit_chart_data']) > 0){
				$digi['benefit_day_start'] = min(array_column($digi['benefit_chart_data'], 'days_from'));
				$digi['benefit_day_end'] = max(array_column($digi['benefit_chart_data'], 'days_to'));
				$digi['benefit_day_current'] = $reached_day;
				$digi['benefit_slab_count'] = sizeof($digi['benefit_chart_data']);
				$benefit_progress_percent = (($digi['benefit_day_current'] / $digi['benefit_day_end']) * 100);
				$digi['benefit_progress_percent'] = number_format(($benefit_progress_percent > 100 ? 100 : $benefit_progress_percent), 2);
				foreach ($digi['benefit_chart_data'] as $item) {
					// For other benefit 
					if ($item['is_current'] == 1 && $item['commodity'] != 1) {
						$other_benefit_percent_current = $item['interest_value'];
					}
					if ($item['is_current'] == 1 && $item['commodity'] == 1) {
						$benefit_percent_current = $item['interest_value'];
					}
				}
				//echo '<pre>';print_r($currentInterest);exit;                         
				$digi['benefit_percent_current'] = (!empty($benefit_percent_current) ? $benefit_percent_current : 0);
				$digi['other_benefit_percent_current'] = (!empty($other_benefit_percent_current) ? $other_benefit_percent_current : 0);
			}
			// if (sizeof($digi_account) > 0) {
				//set target..
				if ($digi['digi_target'] == 1) {
						$max_weight = formatMetalWeight(($digi['max_amount'] / $metal_rates[$rateKey]));
					$entered_target = ($digi_account['dg_target_value_wgt'] > 0 ? $digi_account['dg_target_value_wgt'] : $max_weight);
					$dg_target_achieved_percent = formatMetalWeight((($digi_account['total_paid_weight'] / $entered_target) * 100));
					$digi_account['dg_target_achieved_percent'] = number_format(($digi_account['total_paid_weight'] > $entered_target ? 100 : $dg_target_achieved_percent), 1);
						$min_target_wgt = formatMetalWeight(($digi['min_amount'] / $metal_rates[$rateKey]));
					$digi['min_target_wgt'] = $digi_account['total_paid_weight'] > $min_target_wgt ? $digi_account['total_paid_weight'] : $min_target_wgt;
					$digi['max_target_wgt'] =formatMetalWeight($max_weight);
					$digi['digi_target_split_wgt'] = formatMetalWeight(($entered_target / ($digi['total_days_to_pay'] / $digi['digi_target_split_value'])));
						$digi['digi_target_split_amt'] = number_format(($digi['digi_target_split_wgt'] * $metal_rates[$rateKey]), 2);
				}
				if ($digi_account['active'] == 1 && $date <= $digi_account['allow_pay_till']) {
					$digi_account['ac_status'] =  'Active';
					$digi_account['ac_status_type'] =  '1';
				} else if ($digi_account['active'] == 1 && $date > $digi_account['allow_pay_till']) {
					$digi_account['ac_status'] =  'Matured';
					$digi_account['ac_status_type'] =  '2';
				} else if ($digi_account['active'] != 1 && $digi_account['is_closed'] != 1) {
					$digi_account['ac_status'] =  'Inactive';
					$digi_account['ac_status_type'] =  '0';
				} else if ($digi_account['active'] != 1 && $digi_account['is_closed'] == 1) {
					$digi_account['ac_status'] =  'Closed';
					$digi_account['ac_status_type'] =  '3';
				} else {
					$digi_account['ac_status'] =  '';
					$digi_account['ac_status_type'] =  '';
				}
				//get payduesdata
					$digi_account['paydues'] = $this->digi_payDues($digi_account, $digi['id_scheme']);
					//get benefit calc data
				if ($digi['interest'] == 1) {
					$digi_account['benefit_calc'] = $this->get_digi_benefit($digi_account);
				}
				//KYC Flow
				$pan_no = '';
				$adhar_no = '';
				$get_pan = FALSE;
				$is_pan_required = 0;
				$kycData = $this->get_CusKycData($data['id_customer']);
				if (sizeof($kycData) > 0) {
					foreach ($kycData as $kd) {
						if ($kd['kyc_type'] == 2) {
							$pan_no = $kd['masked_doc_number'];
						}
						if ($kd['kyc_type'] == 3) {
							$adhar_no = $kd['masked_doc_number'];
						}
					}
				}
				if ($digi['is_pan_required'] == 1 || $digi['is_pan_required'] == 2) {
					if (empty($pan_no)) {
						$get_pan = TRUE;
						$is_pan_required = 1;
					}
				}
				if ($digi['is_aadhar_required'] == 1 || $digi['is_aadhar_required'] == 2) {
					if (empty($adhar_no)) {
						$get_pan = TRUE;
						$is_aadhar_required = 1;
					}
				}
				$digi_account['is_pan_req'] = $is_pan_required;
				$digi_account['is_aadhar_req'] = $is_pan_required;
				$digi_account['pan_req_amt'] = $digi['pan_req_amt'];
				$digi_account['aadhar_req_amt'] = $digi['aadhar_req_amt'];
				$digi_account['get_pan'] = $get_pan;
				// $digi_account['pan_disclaimer'] = "As per regulatory guidelines, furnishing a PAN number is mandatory for any payment exceeding ₹ " . $digi['pan_req_amt'] . ".";
				$digi_account['pan_disclaimer'] = "As per regulatory guidelines, furnishing a PAN number is mandatory for Digi Gold.";

				//CLIENT SPECIFIC REQ FOR CJ
				/* if($digi_account['total_paid_ins'] < 1){
					$digi_account['start_date']='';
					$digi_account['maturity_date']='';
				} */
					// print_r($digi);exit;
					$result[] = array(
					'status' => TRUE,
					'show_digi' => TRUE,
					'allow_join' => FALSE,
					'msg' => "Digi $digi[metal] Account successfully retrived...",
					'sch_data' => $digi,
					'acc_data' => $digi_account,
				);
			} else {
					$result[] = array(
					'status' => TRUE,
					'show_digi' => TRUE,
					'allow_join' => TRUE,
					'msg' => "You are allowed to join Digi $digi[metal] Savings Plan... ",
					'sch_data' => $digi,
					'acc_data' => []
				);
				}
			}
		} else {
			$result = array(
				'status' => FALSE,
				'show_digi' => FALSE,
				'allow_join' => FALSE,
				'msg' => "Digi $digi[metal] Savings plan is currently unavailable...Kindly contact admin...",
				'sch_data' => [],
				'acc_data' => []
			);
		}
		return $result;
	}
	function getdigidata($data)
	{
		$sql = $this->db->query("SELECT s.is_digi,sa.id_scheme_account,sa.id_scheme,DATEDIFF(CURDATE(),date(sa.start_date)) as date_difference,CURDATE() as cur_date, 
		                        DATE_ADD(date(sa.start_date), INTERVAL s.total_days_to_pay DAY) as allow_pay_till,
		                        COUNT(p.id_payment) as pay_count,sa.id_branch as joined_branch, sa.active
                               FROM scheme_account sa
                                LEFT JOIN customer c ON (c.id_customer = sa.id_customer)
                                LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
                                 LEFT JOIN payment p ON (p.id_scheme_account = sa.id_scheme_account)
                               WHERE sa.id_customer =" . $data['id_customer'] . "  AND s.is_digi = 1 AND sa.is_closed = 0 AND sa.active = 1
                                GROUP BY p.id_scheme_account");
		$res = $sql->row_array();
		$sql1 = $this->db->query("SELECT s.id_scheme 
                                FROM scheme s
                                WHERE is_digi = 1");
		$is_digi = $sql1->row_array();
		if ($sql1->num_rows() > 0) {
			$sch = $this->get_scheme($is_digi['id_scheme'], $data['id_customer']);
		}
		if ($sql->num_rows() == 0) {
			//get scheme details
			return array('status' => FALSE, 'scheme' => $sch, 'chit' => [], 'digiwallet' => []);
		} else {
			//get scheme account details
			$sch_acc = $this->chit_scheme_detail($res['id_scheme_account']);
			if ($sch_acc['chit']['show_chit_wallet'] == 1) {
				$sql_int = $this->db->query("SELECT interest_type,interest_value, IF(interest_type = 0,'%','INR') as int_symbol 
				FROM `scheme_benefit_deduct_settings` 
				" . ($res['id_scheme'] != '' && $res['id_scheme'] != null ?
					($res['restrict_payment'] = 1 ? 'WHERE (' . $res['date_difference'] . ' BETWEEN installment_from AND installment_to) AND id_scheme=' . $res['id_scheme'] : 'WHERE id_scheme=' . $res['id_scheme'])
					: ('WHERE id_scheme=' . $res['id_scheme'])) . "
    			");
				$int = $sql_int->row_array();
				$sql_debit = $this->db->query("SELECT deduction_type ,deduction_value,installment_to FROM `scheme_debit_settings` 
	            where " . ($res['is_digi'] == 1 ? "(" . $res['date_difference'] . " BETWEEN installment_from AND installment_to)" : "(installment_from =" . $res['paid_installments'] . " or installment_to =" . $res['paid_installments'] . ")") . "
				and id_scheme=" . $res['id_scheme']);
				$debit = $sql_debit->row_array();
				//print_r($this->db->last_query());exit;
				if ($sql_int->num_rows > 0) {
					$sql_tot = $this->db->query("SELECT SUM(p.payment_amount) as total_paid,SUM(p.metal_weight) as saved_wgt,
                    SUM(ROUND((p.metal_weight)*(" . $int['interest_value'] . "/100)*(DATEDIFF(CURDATE(),date(p.date_payment))/365),3)) as total_benefit,
                    CURDATE() as cur_date, CONCAT(" . $int['interest_value'] . ",' %') as interest,COUNT(id_payment) as pay_count,date(sa.start_date) as join_date,
                    DATE_ADD(date(sa.start_date), INTERVAL s.total_days_to_pay DAY) as allow_pay_till,DATEDIFF(DATE_ADD(date(sa.start_date), INTERVAL s.total_days_to_pay DAY),date(p.date_payment)) as date_difference,
                    '' as wallet_text
        			FROM `payment` p  
        			LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
        			LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)			
        			WHERE sa.id_scheme_account = " . $res['id_scheme_account'] . " and p.payment_status = 1");
					$digiwallet = $sql_tot->row_array();
				} else {
					$sql_tot = $this->db->query("SELECT SUM(p.payment_amount) as total_paid,SUM(p.metal_weight) as saved_wgt,
                    '' as total_benefit,CURDATE() as cur_date, '' as interest,COUNT(id_payment) as pay_count,date(sa.start_date) as join_date,
                    DATE_ADD(date(sa.start_date), INTERVAL s.total_days_to_pay DAY) as allow_pay_till,DATEDIFF(CURDATE(),date(p.date_payment)) as date_difference,
                    '' as wallet_text
         			FROM `payment` p  
        			LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
        			LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)			
        			WHERE sa.id_scheme_account = " . $res['id_scheme_account'] . " and p.payment_status = 1");
					$digiwallet = $sql_tot->row_array();
				}
				if ($sql_debit->num_rows > 0) {
					$sql_tot = $this->db->query("SELECT SUM(ROUND((p.metal_weight)*(" . $debit['deduction_value'] . "/100)*(DATEDIFF(CURDATE(),date(p.date_payment))/365),3)) as preclose_benefit,
                     CONCAT(" . $debit['deduction_value'] . ",' %') as preclose_interest,
                     CONCAT(date(sa.start_date),' to ',(DATE_ADD(date(sa.start_date), INTERVAL " . $debit['installment_to'] . " DAY))) AS preclose_date
         			FROM `payment` p  
        			LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
        			LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)			
        			WHERE sa.id_scheme_account = " . $res['id_scheme_account'] . " and p.payment_status = 1");
					$digiwallet['preclose_interest'] = $sql_tot->row()->preclose_interest;
					$digiwallet['preclose_benefit'] = $sql_tot->row()->preclose_benefit;
					$digiwallet['preclose_date'] = $sql_tot->row()->preclose_date;
				} else {
					$sql_tot = $this->db->query("SELECT '' as preclose_interest, '' as preclose_benefit, '' as preclose_date
        			FROM `payment` p  
        			LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
        			LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)			
        			WHERE sa.id_scheme_account = " . $res['id_scheme_account'] . " and p.payment_status = 1");
					$digiwallet['preclose_interest'] = $sql_tot->row()->preclose_interest;
					$digiwallet['preclose_benefit'] = $sql_tot->row()->preclose_benefit;
					$digiwallet['preclose_date'] = $sql_tot->row()->preclose_date;
				}
			} else {
				$digiwallet = [];
			}
			return array('status' => TRUE, 'scheme' => $sch, 'chit' => $sch_acc, 'digiwallet' => $digiwallet);
		}
	}
	public function updData($data, $id_field, $id_value, $table)
	{
		$edit_flag = 0;
		$this->db->where($id_field, $id_value);
		$edit_flag = $this->db->update($table, $data);
		return ($edit_flag == 1 ? $id_value : 0);
	}
	public function digi_benefit_chart_data($reached_day, $id_scheme)
	{
		$response = [];
		$sql = $this->db->query("SELECT sb.installment_from as days_from ,sb.installment_to as days_to, if(sb.interest_type = 0,'%','INR') as type, sb.interest_value, 
	                                if(" . $reached_day . " BETWEEN sb.installment_from and sb.installment_to , '1','0') as is_current, 
	                                if(" . $reached_day . " > sb.installment_to,'Fully Crosed Slab',if(" . $reached_day . " > sb.installment_from and " . $reached_day . " < sb.installment_to, 'Current Slab' ,'Upcoming Slab')) as slab_status,
									sb.commodity
                                FROM `scheme_benefit_deduct_settings` as sb
                                WHERE id_scheme =" . $id_scheme);
		if ($sql->num_rows() >= 1) {
			$response = $sql->result_array();
		}
		// print_r($this->db->last_query());exit;                        
		return $response;
	}
	//RAHUL 31/07
	function format_accRcptNo($type, $id)
	{
		/* Format account and receipt no based on settings
	        Type : Account (for account num format), Receipt  (for receipt num format)
	     */
		$set = $this->get_AccRcpt_DisplaySettings();      //get the settings 
		switch ($type) {
			case 'Account':
				$accFrmt = $this->accountFrmt($set, $id);
				return $accFrmt;
				break;
			case 'Receipt':
				$rcptFrmt = $this->receiptFrmt($set, $id);
				return $rcptFrmt;
				break;
		}
	}
	function get_AccRcpt_DisplaySettings()
	{
		/* get account number and receipt number settings from chit settings table...   */
		$sql = "SELECT cs.scheme_wise_receipt,cs.scheme_wise_acc_no,cs.schemeaccNo_displayFrmt,cs.receiptNo_displayFrmt,cs.custom_AccDisplayFrmt,cs.custom_ReceiptDisplayFrmt,
	            cs.receipt_no_set,cs.schemeacc_no_set,cs.group_wise_receipt
                FROM `chit_settings` cs";
		return $this->db->query($sql)->row_array();
	}
	function accountFrmt($set, $id)
	{
		$acc = $this->get_acc_Data($id);      //get account data needed
		if ($set['schemeacc_no_set'] == 0 && $set['schemeaccNo_displayFrmt'] != 0) {
			if ($set['schemeaccNo_displayFrmt'] == 1) {
				if ($set['scheme_wise_acc_no'] == 0) {   //Common
					$accFrmt = $acc['sch_AccNo'];
				} else if ($set['scheme_wise_acc_no'] == 1) {  //Common with branch wise
					$accFrmt = $acc['branch_code'] . '-' . $acc['sch_AccNo'];
				} else if ($set['scheme_wise_acc_no'] == 2) {   //Scheme Wise
					$accFrmt = $acc['scheme_code'] . '-' . $acc['sch_AccNo'];
				} else if ($set['scheme_wise_acc_no'] == 3) {  //Scheme wise With Branch Wise
					$accFrmt = $acc['scheme_code'] . $acc['branch_code'] . '-' . $acc['sch_AccNo'];
				} else if ($set['scheme_wise_acc_no'] == 4) {   //Financial Year Wise
					$accFrmt = $acc['start_year'] . '-' . $acc['sch_AccNo'];
				} else if ($set['scheme_wise_acc_no'] == 5) {   //Financial Year with Scheme Wise
					$accFrmt = $acc['start_year'] . $acc['scheme_code'] . '-' . $acc['sch_AccNo'];
				} else if ($set['scheme_wise_acc_no'] == 6) {   //Financial Year with Scheme & Branch Wise
					$accFrmt = $acc['start_year'] . $acc['scheme_code'] . $acc['branch_code'] . '-' . $acc['sch_AccNo'];
				}
			} else if ($set['schemeaccNo_displayFrmt'] == 2) {
				$acc_frmt_fromdb = $this->getFormatFromDB();
				$acc = $this->get_acc_Data($id);
				$frmt_short_code = [];
				if ($acc_frmt_fromdb['custom_AccDisplayFrmt'] != '' && $acc_frmt_fromdb['custom_AccDisplayFrmt'] != null) {
					$field_name = explode('@@', $acc_frmt_fromdb['custom_AccDisplayFrmt']);
					for ($i = 1; $i < count($field_name); $i += 2) {
						$frmt_short_code[] = $field_name[$i];
					}
					$accFrmt = $this->getFormatedNumber($frmt_short_code, $acc);
				} else {
					$accFrmt = $acc['sch_AccNo'];
				}
			}
		} else {
			$accFrmt =  $acc['sch_AccNo'];
		}
		return $accFrmt;
	}
	function receiptFrmt($set, $id)
	{
		$rcpt = $this->get_receipt_Data($id);      //get receipt data needed
		if ($rcpt['receipt_no'] == '-') {
			$rcptFrmt =  $rcpt['receipt_no'];
		} else {
			if ($set['receipt_no_set'] == 1 && $set['receiptNo_displayFrmt'] != 0) {
				if ($set['receiptNo_displayFrmt'] == 1) {
					if ($set['scheme_wise_receipt'] == 1) {   //Common
						$rcptFrmt = $rcpt['receipt_no'];
					} else if ($set['scheme_wise_receipt'] == 2) {  //Branch-wise
						$rcptFrmt = $rcpt['branch_code'] . '-' . $rcpt['receipt_no'];
					} else if ($set['scheme_wise_receipt'] == 3) {   //scheme-wise
						$rcptFrmt = $rcpt['scheme_code'] . '-' . $rcpt['receipt_no'];
					} else if ($set['scheme_wise_receipt'] == 4) {  //scheme with Branch-wise
						$rcptFrmt = $rcpt['scheme_code'] . $rcpt['branch_code'] . '-' . $rcpt['receipt_no'];
					} else if ($set['scheme_wise_receipt'] == 5) {   // Financial Year-wise
						$rcptFrmt = $rcpt['receipt_year'] . '-' . $rcpt['receipt_no'];
					} else if ($set['scheme_wise_receipt'] == 6) {   //Financial Year with Scheme & Branch wise
						$rcptFrmt = $rcpt['receipt_year'] . $rcpt['scheme_code'] . $rcpt['branch_code'] . '-' . $rcpt['receipt_no'];
					}
				} else if ($set['receiptNo_displayFrmt'] == 2) {
					$rcpt_frmt_fromdb = $this->getFormatFromDB();
					$rcpt = $this->get_receipt_Data($id);
					$frmt_short_code = [];
					if ($rcpt_frmt_fromdb['custom_ReceiptDisplayFrmt'] != '' && $rcpt_frmt_fromdb['custom_ReceiptDisplayFrmt'] != null) {
						if ($rcpt['receipt_no'] != '' && $rcpt['receipt_no'] != null && $rcpt['receipt_no'] != 0 && $rcpt['receipt_no'] != '-') {
							$field_name = explode('@@', $rcpt_frmt_fromdb['custom_ReceiptDisplayFrmt']);
							for ($i = 1; $i < count($field_name); $i += 2) {
								$frmt_short_code[] = $field_name[$i];
							}
							$rcptFrmt = $this->getFormatedNumber($frmt_short_code, $rcpt);
						} else {
							$rcptFrmt = '-';
						}
					} else {
						if ($rcpt['receipt_no'] != '' && $rcpt['receipt_no'] != null && $rcpt['receipt_no'] != 0  && $rcpt['receipt_no'] != '-') {
							$rcptFrmt =  $rcpt['receipt_no'];
						} else {
							$rcptFrmt = '-';
						}
					}
				}
			} else {
				if ($rcpt['receipt_no'] != '' && $rcpt['receipt_no'] != null && $rcpt['receipt_no'] != 0  && $rcpt['receipt_no'] != '-') {
					$rcptFrmt =  $rcpt['receipt_no'];
				} else {
					$rcptFrmt = '-';
				}
			}
		}
		return $rcptFrmt;
	}
	function get_acc_Data($id)
	{
		/* get necessary data of scheme account by id... */
		$sql = "SELECT IFNULL(sa.scheme_acc_number,'Not Allocated') as sch_AccNo,IFNULL(sa.start_year,'') as start_year,b.short_name as branch_code,s.code,IFNULL(sa.group_code,'') as group_code,
	                if(s.is_lucky_draw = 1, CONCAT(s.code,'(',ifnull(sa.group_code,''),')') ,s.code) as scheme_code
                FROM scheme_account sa
                LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
                LEFT JOIN branch b ON (b.id_branch = sa.id_branch)
                WHERE sa.id_scheme_account = " . $id;
		// print_r($this->db->last_query());exit;
		return $this->db->query($sql)->row_array();
	}
	function getFormatFromDB()
	{
		$sql = "SELECT custom_AccDisplayFrmt,custom_ReceiptDisplayFrmt,receiptNo_displayFrmt,schemeaccNo_displayFrmt from chit_settings where id_chit_settings=1";
		return $this->db->query($sql)->row_array();
	}
	function getFormatedNumber($frmt_short_code, $acc)
	{
		if ($frmt_short_code != '' && $frmt_short_code != null) {
			$finalFormat = '';
			foreach ($frmt_short_code as $code) {
				switch ($code) {
					case 'br_code':
						$finalFormat .= $acc['branch_code'];
						break;
					case 'acc_num':
						$finalFormat .= $acc['sch_AccNo'];
						break;
					case 'sch_code':
						$finalFormat .= $acc['scheme_code'];
						break;
					/*	case 'grp_code':
						$finalFormat.=$acc['group_code'];
						break;*/
					case 'grp_code':
						if ($acc['group_code'] != null && $acc['group_code'] != '') {
							$finalFormat .= $acc['group_code'];
						} else {
							$lastCharacter = substr($finalFormat, -1);
							if ($lastCharacter == '-') {
								$finalFormat = substr($finalFormat, 0, -1);
							}
						}
						break;
					case 'fin_yr':
						$finalFormat .= $acc['start_year'];
						break;
					case 'rcpt_yr':
						$finalFormat .= $acc['receipt_year'];
						break;
					case 'rcpt_num':
						$finalFormat .= $acc['receipt_no'];
						break;
					case 'hyphen':
						$finalFormat .= '-';
						break;
					case 'space':
						$finalFormat .= ' ';
						break;
				}
			}
		}
		//$finalFormat=substr($finalFormat, 1);
		return $finalFormat;
	}
	function get_receipt_Data($id)
	{
		/* get necessary data of payment by id... */
		$sql = "SELECT IFNULL(p.receipt_no,'-') as receipt_no,IFNULL(p.receipt_year,'') as receipt_year,b.short_name as branch_code,s.code,IFNULL(sa.group_code,'') as group_code,
	                if(s.is_lucky_draw = 1 && cs.group_wise_receipt = 1 , CONCAT(s.code,'(',ifnull(sa.group_code,''),')') ,s.code) as scheme_code
                FROM payment p
                LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
                LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
                LEFT JOIN branch b ON (b.id_branch = p.id_branch)
                JOIN chit_settings cs 
                WHERE p.id_payment = " . $id;
		return $this->db->query($sql)->row_array();
	}
}
