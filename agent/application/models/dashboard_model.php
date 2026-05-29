<?php

class Dashboard_model extends CI_Model {
    
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
	
    function getCusLoyaltyTrans($data)
	{
	    $id_agent = $this->session->userdata('cus_id');
	    $data['last_id'] = 0;
	    $result = array();
		$sql  = "SELECT  
		            sa.account_name,if(cs.has_lucky_draw=1 && s.is_lucky_draw = 1,concat(concat(ifnull(sa.group_code,''),' ',ifnull(sa.scheme_acc_number,'Not Allocated')),' - ',s.code ),concat(s.code,' ',ifnull(sa.scheme_acc_number,'Not Allcoated')))as scheme_acc_number,
				 	concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')) as customer_name,c.mobile,
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
				 	if(t.status = 1, 'Active',if(t.status = 2, 'Settled Commission','Expired')) as status
				FROM `ly_customer_loyalty_transaction` t
					LEFT JOIN scheme_account sa on sa.id_scheme_account = t.id_scheme_account
					LEFT JOIN scheme s On (s.id_scheme=sa.id_scheme)
					LEFT JOIN customer c on c.id_customer = sa.id_customer
					LEFT JOIN payment p on p.id_payment = t.id_payment
					JOIN chit_settings cs 
				WHERE t.status != 2 AND t.id_agent = ".$id_agent."
				".($data['last_id'] > 0? ' and id_cus_loyal_tran <'.$data['last_id'] : '' ) ; 
		if($data['from_date'] != ''){
			$sql = $sql." and (date(t.date_add) BETWEEN '".$data['from_date']."' AND '".$data['to_date']."')";
		}
		$sql = $sql." ORDER BY id_cus_loyal_tran DESC LIMIT 5"; 
		$rows =  $this->db->query($sql)->result_array();  
		//echo $sql;
		$i = 0;
		foreach( $rows as $row ){
			$result[$i] = $row; 
			/*$result[$i]['bill_no'] = $this->getBillNo($row['bill_ref_no'],$row['bill_no']);
			$file = self::CUS_IMG_PATH.'/'.$row['id_agent'].'/customer.jpg'; 
			$result[$i]['referred_cus_img'] = (file_exists($file)? base_url().''.$file : null );*/
			$result[$i]['date'] = strtotime($row['date']);
			$i++;
		}
		
	/*	if($data['type'] == 'transactions'){
    		$settlement_sql  = "SELECT 
    		                        '' as id_cus_loyal_tran,
                					'Settled Commission' as ly_trans_type_name,
                					'Settled Commission' as trans_type,'' as referred_cus_mobile, '' as bill_no,
                				 	IFNULL(settlement_pts,0) as cash_point,
                				 	'' as trans_type_sign,
                				 	Date_format(settlement_date,'%d %b %Y') as cr_date,settlement_date as date
                				FROM `ly_influencer_settlement` s
                				WHERE s.id_agent = ".$id_agent; 
    		if($data['from_date'] != ''){
    			$settlement_sql = $settlement_sql." and (date(settlement_date) BETWEEN '".$data['from_date']."' AND '".$data['to_date']."')";
    		}
    		$settlement_sql = $settlement_sql." order by id_influencer_settlement DESC";
    		$settlmtData =  $this->db->query($settlement_sql)->result_array(); 
    		foreach( $settlmtData as $s ){
    			$result[$i] = $s;
    			$result[$i]['date'] = strtotime($s['date']);
    			$i++;
    		}
		}  */
		if(sizeof($result) > 0){
		    return $this->array_sort($result,'date','SORT_DESC');
		}else{
		    return array();
		}
	}
	
	function getAgentReferralsList($data)
	{
	    $id_agent = $this->session->userdata('cus_id');
	    $data['last_id'] = 0;
	    $result = array();
		$sql  = "SELECT  
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
				 	if(t.status = 1, 'Active',if(t.status = 2, 'Settled Commission','Expired')) as status
				FROM `scheme_account` sa
					LEFT JOIN ly_customer_loyalty_transaction t on t.id_scheme_account = sa.id_scheme_account
					LEFT JOIN scheme s On (s.id_scheme=sa.id_scheme)
					LEFT JOIN customer c on c.id_customer = sa.id_customer
					LEFT JOIN payment p on p.id_payment = t.id_payment
					JOIN chit_settings cs 
				WHERE sa.id_agent = ".$id_agent."
				".($data['last_id'] > 0? ' and id_cus_loyal_tran <'.$data['last_id'] : '' ) ; 
		if($data['from_date'] != ''){
			$sql = $sql." and (date(sa.date_add) BETWEEN '".$data['from_date']."' AND '".$data['to_date']."')";
		}
		$sql = $sql." GROUP BY sa.id_scheme_account ORDER BY sa.id_scheme_account DESC LIMIT 25"; 
		$rows =  $this->db->query($sql)->result_array();  
// 		echo $sql;
		$i = 0;
		foreach( $rows as $row ){
			$result[$i] = $row; 
			/*$result[$i]['bill_no'] = $this->getBillNo($row['bill_ref_no'],$row['bill_no']);
			$file = self::CUS_IMG_PATH.'/'.$row['id_agent'].'/customer.jpg'; 
			$result[$i]['referred_cus_img'] = (file_exists($file)? base_url().''.$file : null );*/
			$result[$i]['date'] = strtotime($row['date']);
			$i++;
		}
		
		if(sizeof($result) > 0){
		    return $this->array_sort($result,'date','SORT_DESC');
		}else{
		    return array();
		}
	}
	
	function getInfSettledData($data)
	{        
	    $result = array();
		$sql  = "SELECT
				 	id_influencer_settlement, settlement_pts, settlement_branch, if(b.name!='',b.name,'-') as branch_name,
				 	Date_format(s.settlement_date,'%d %b %Y') as settlement_date, 'Settled' as status
				FROM `ly_influencer_settlement` s
					LEFT JOIN branch  b on b.id_branch = settlement_branch
				WHERE s.id_agent = ".$data['id_agent']."
				".($data['last_id'] > 0? ' and id_influencer_settlement <'.$data['last_id'] : '' )." 
				".($data['from_date'] != '' ? ' and (date(settlement_date) BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'")' :'');
				
				//echo $sql;exit;
		return $this->db->query($sql)->result_array();
	}
	
	function getInfSetlSummmary($id_agent)
	{        
	    $result = array("settled" => 0, "earned" => 0, "outstanding" => 0);
		
		$settled  = "SELECT
				 	IFNULL(sum(settlement_pts),0) as settled
				FROM `ly_influencer_settlement` s
				WHERE s.id_agent = ".$id_agent;
		$result['settled'] = $this->db->query($settled)->row('settled');
		
		$earned  = "SELECT  
				 	IFNULL(SUM(t.cash_point),0) as cash_point				 	
				FROM ly_customer_loyalty_transaction t
				WHERE id_agent = ".$id_agent;
		$result['earned'] = $this->db->query($earned)->row('cash_point');
		
		$result['outstanding'] = number_format(($result['earned'] - $result['settled']),2,'.','');
		
		return $result;
	}
	
	function getEarnedPts($data){
	    $sql  = $this->db->query("SELECT  
				 	SUM(IFNULL(t.reward_pts,0)) as tot_reward_pts, IFNULL(SUM(t.cash_point),0) as tot_cash_point, ifnull((SUM(t.cash_point) - ifnull((SELECT sum(settlement_pts) FROM `ly_influencer_settlement` s WHERE s.id_agent = t.id_agent ),0)),0) as outstanding
				FROM `ly_customer_loyalty_transaction` t
				
				WHERE t.id_agent = ".$data['id_agent']."
				".($data['from_date'] != '' ? ' and (date(t.date_add) BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'")' :'')." 
				group by id_agent");


		 //echo $this->db->last_query();exit;
	
		
		if($sql->num_rows() == 0) {
			return array("tot_reward_pts" => 0.00, "tot_cash_point" =>0.00, "outstanding" =>0.00);
		}else{
			return $sql->row_array();  
		}
	}
	
	function getAgentReferrals($data){
	    $sql  = $this->db->query( "SELECT count(id_scheme_account) as referrals from scheme_account 
	                            WHERE id_agent = ".$data['id_agent']."
				                ".($data['from_date'] != '' ? ' and (date(date_add) BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'")' :'')
				                );
                        	   // echo $this->db->last_query();exit;
		if($sql->num_rows() == 0) {
			return 0;
		}else{
			return $sql->row()->referrals;  
		}
	}
	
	function getConversions($data){
	    $sql  = $this->db->query( "SELECT count(id_scheme_account) as conversions from ly_customer_loyalty_transaction 
	                            WHERE id_agent = ".$data['id_agent']."
				                ".($data['from_date'] != '' ? ' and (date(date_add) BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'")' :'')."
				                "
				                );
                        	   // echo $this->db->last_query();exit;
		if($sql->num_rows() == 0) {
			return 0;
		}else{
			return $sql->row()->conversions;  
		}
	}
	
	function getUnpaids($data){
	    $sql  = $this->db->query( "SELECT count(id_scheme_account) as unpaid from scheme_account sa
	                            WHERE sa.id_agent = ".$data['id_agent']." AND id_scheme_account NOT IN (SELECT p.id_scheme_account FROM payment p WHERE sa.id_agent = ".$data['id_agent'].")
				                ".($data['from_date'] != '' ? ' and (date(date_add) BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'")' :'')."
				                "
				                );
                        	 //   echo $this->db->last_query();exit;
		if($sql->num_rows() == 0) {
			return 0;
		}else{
			return $sql->row()->unpaid;  
		}
	}
	function getAllConversion($id_agent,$from_date,$to_date)
	{     //0-> Expired, 1-> Active, 2-> Settled, 3 -> Partially Settled
	   $sql = "SELECT c.id_customer,CONCAT(c.firstname,' ',c.lastname) as cus_name,c.mobile,sa.id_scheme,sa.id_scheme_account,sa.scheme_acc_number,sa.account_name,sa.date_add,
	            p.payment_amount,p.date_payment,clt.cash_point,clt.unsettled_cash_pts,ifnull((if(clt.status = 0,'Expired',if(clt.status = 1,'Active',if(clt.status = 2, 'Settled',if(clt.status = 3,'Partially Settled','-'))))),'-') as status
                FROM `ly_customer_loyalty_transaction` clt
                LEFT JOIN scheme_account sa ON (sa.id_scheme_account = clt.id_scheme_account)
                LEFT JOIN customer c ON (c.id_customer = clt.cus_loyal_cus_id AND c.id_customer = sa.id_customer)
                LEFT JOIN payment p ON (p.id_agent = clt.id_agent AND p.id_scheme_account = clt.id_scheme_account)
                WHERE sa.id_agent = ".$id_agent." AND clt.id_agent = ".$id_agent." ".($from_date != '' ? ' and (date(clt.date_add) BETWEEN "'.$from_date.'" AND "'.$to_date.'")' :'')."
                GROUP BY p.id_payment"; 
                
	   $conversionData =$this->db->query($sql); 
	 // echo $this->db->last_query();exit;
	   return $conversionData->result_array();
	   
	}
	
	function getAllReferrals($id_agent,$from_date,$to_date)
	{     
	   $sql = "SELECT c.id_customer,CONCAT(c.firstname,' ',c.lastname) as cus_name,c.mobile,sa.id_scheme,sa.id_scheme_account,sa.scheme_acc_number,sa.account_name,sa.date_add
	            FROM customer c
                LEFT JOIN scheme_account sa ON (sa.id_customer = c.id_customer )
                WHERE sa.id_agent = ".$id_agent."  ".($from_date != '' ? ' and (date(sa.date_add) BETWEEN "'.$from_date.'" AND "'.$to_date.'")' :'')." "; 
                
	   $referralData =$this->db->query($sql); 
	 // echo $this->db->last_query();exit;
	   return $referralData->result_array();
	   
	}
	

	function getAllUnpaidData($id_agent,$from_date,$to_date)
	{     
	   $sql = "SELECT c.id_customer,CONCAT(c.firstname,' ',c.lastname) as cus_name,c.mobile,sa.id_scheme,sa.id_scheme_account,sa.scheme_acc_number,sa.account_name,sa.date_add
                FROM customer c
                LEFT JOIN scheme_account sa ON (sa.id_customer = c.id_customer )
                WHERE sa.id_agent = ".$id_agent." AND sa.id_scheme_account NOT IN (SELECT p.id_scheme_account FROM payment p WHERE p.id_agent = ".$id_agent.") ".($from_date != '' ? ' and (date(sa.date_add) BETWEEN "'.$from_date.'" AND "'.$to_date.'")' :'')."
                "; 
                
	   $referralData =$this->db->query($sql); 
	  //echo $this->db->last_query();exit;
	   return $referralData->result_array();
	   
	}
	
	function getAllSettlementData($id_agent,$from_date,$to_date)
	{        
	    $result = array();
		$sql  = "SELECT
				 	id_influencer_settlement, settlement_pts, settlement_branch, if(b.name!='',b.name,'-') as branch_name,
				 	Date_format(s.settlement_date,'%d %b %Y') as settlement_date, 'Settled' as status
				FROM `ly_influencer_settlement` s
					LEFT JOIN branch  b on b.id_branch = settlement_branch
				WHERE s.id_agent = ".$id_agent."
				
				".($from_date != '' ? ' and (date(settlement_date) BETWEEN "'.$from_date.'" AND "'.$to_date.'")' :'');
				
				//echo $sql;exit;
		return $this->db->query($sql)->result_array();
	}
}

?>