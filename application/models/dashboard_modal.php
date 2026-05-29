<?php


class Dashboard_modal extends CI_Model {


	function get_lastPaid()


	{


		$records = '';


		$schemes = '';


		$schSummary = '';


		$weight = '';
        $showGCodeInAcNo = $this->config->item('showGCodeInAcNo'); 

		$query = $this->db->query("SELECT CONCAT(if(".$showGCodeInAcNo."=1,if(has_lucky_draw = 1,sg.group_code,sch.code),'') ,' ',ifnull(schAcc.scheme_acc_number,'Not Allocated')) as scheme_acc_number,cs.has_lucky_draw,id_payment,DATE_FORMAT(date_payment,'%d-%m-%Y') AS date_payment,sch.scheme_name AS scheme_name,metal_weight,payment_amount,sch.code,psm.payment_status AS payment_status,if(payment_mode='CC','Credit Card',if(payment_mode='NB','Net Banking',if(payment_mode='CD','Cheque or DD',if(payment_mode='CO','Cash Pick Up',if(payment_mode='OP','Other',if(payment_mode='DC','Debit Card',pm.mode_name)))))) as payment_mode


									FROM payment AS pay 


									LEFT JOIN scheme_account AS schAcc	ON (pay.id_scheme_account = schAcc.id_scheme_account)
                                    Left Join scheme_group sg On (schAcc.group_code = sg.group_code )

									LEFT JOIN scheme AS sch ON sch.id_scheme = schAcc.id_scheme


									LEFT JOIN customer AS cus ON cus.id_customer = schAcc.id_customer


									LEFT Join payment_status_message psm ON (pay.payment_status=psm.id_status_msg)


									LEFT JOIN payment_mode pm ON (pay.payment_mode=pm.short_code)
									
									join chit_settings cs


									WHERE id_payment = (SELECT max(id_payment) FROM payment AS pt


									LEFT JOIN scheme_account AS sa ON sa.id_scheme_account = pt.id_scheme_account 
									
									
									
									WHERE id_customer = ".$this->session->userdata('cus_id').")


									AND schAcc.id_customer = ".$this->session->userdata('cus_id'));
 
 
			if($query->num_rows() > 0)


			{





					$records = $query->result_array();


			}


	


		$schemeQuery = $this->db->query("SELECT schAcc.id_scheme_account AS id_scheme_account, ref_no AS client_id,schAcc.scheme_acc_number as scheme_acc_number, IF(is_opening =1,paid_installments,0) AS paid_installments,


										IF(is_opening =1,balance_amount,0) AS balance_amount, IF(is_opening =1,balance_weight,0) AS balance_weight,


										is_opening,COUNT(DISTINCT DATE_FORMAT(date_payment,'%Y%m')) AS PaidInstall,SUM(IFNULL(payment_amount,0)) AS paymentAmt,


										SUM(IFNULL(metal_weight,0)) AS metalWgt,DATE_FORMAT(start_date,'%d-%m-%Y') AS start_date,DATE_FORMAT(closing_date,'%d-%m-%Y') AS closing_date,is_closed,closed_by,scheme_name,scheme_type,amount,


										total_installments,interest,tax,tax_by,tax_value,total_tax,interest_by,interest_value,total_interest


										FROM scheme_account AS schAcc


										LEFT JOIN payment AS pay ON schAcc.id_scheme_account = pay.id_scheme_account AND payment_status = 1


										LEFT JOIN scheme AS sch ON sch.id_scheme = schAcc.id_scheme


										WHERE schAcc.active = 1 AND schAcc.id_customer = '".$this->session->userdata('cus_id')."'


										GROUP BY schAcc.id_scheme_account");


			if($schemeQuery->num_rows() > 0)


			{	


				$schSummary = $schemeQuery->result_array();


			}


		$weightQuery = $this->db->query("SELECT id_weight,weight,active FROM weight WHERE active = 1");


			if($weightQuery->num_rows() > 0)


			{


				$weight = $weightQuery->result_array();


			}	


		return array('lastPaid' => $records, 'schSummary' => $schSummary, 'weight' => $weight);


	}


	


	function countSchemes()


	{


		$sql = " Select count(id_scheme) as schemes 


		         From scheme_account 


		         Where active=1 and id_customer ='".$this->session->userdata('cus_id')."'";


		$result = $this->db->query($sql);	


		return $result->row()->schemes;


	}	


	


	//count no schemes by the customer


	function countWallets()


	{


		$sql = " Select count(id_wallet_account) as wallets 


		         From wallet_account 


		         Where active=1 and id_customer ='".$this->session->userdata('cus_id')."'";


		$result = $this->db->query($sql);	


		return $result->row()->wallets;


	}


	


    function wallet_balance()


	{


		$sql="Select


								  wa.id_wallet_account,


								  c.id_customer,


								  Concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')) as name,


								  c.mobile,


								  Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_name,


								  wa.wallet_acc_number,


								  Date_Format(wa.issued_date,'%d-%m-%Y') as issued_date,


								  wa.remark,


								  wa.active,


								  SUM(CASE WHEN wt.transaction_type=0 THEN wt.`value` ELSE 0 END) as  issues,


								  SUM(CASE WHEN wt.transaction_type=1 THEN wt.`value` ELSE 0 END) as redeem,


								  (SUM(CASE WHEN wt.transaction_type=0 THEN wt.`value` ELSE 0 END) -   SUM(CASE WHEN wt.transaction_type=1 THEN wt.`value` ELSE 0 END)) as balance,
								  cs.wallet_amt_per_points,cs.wallet_balance_type,cs.wallet_points


							From wallet_account wa


								Left Join customer c on (wa.id_customer=c.id_customer)


								Left Join employee e on (wa.id_employee=e.id_employee)


								Left Join wallet_transaction wt on (wa.id_wallet_account=wt.id_wallet_account)
								
								join chit_settings cs


								where c.id_customer =".$this->session->userdata('cus_id');


		$result = $this->db->query($sql);	


		return $result->row()->balance;


	}


	


	function countPayments()


	{


		$sql = "Select count(distinct  p.id_payment) as payments


				From scheme_account sa


				Left Join payment p  on (sa.id_scheme_account = p.id_scheme_account and (p.payment_status=0 or p.payment_status=1 ))


         		Where sa.active=1 and sa.id_customer ='".$this->session->userdata('cus_id')."'";


				$result = $this->db->query($sql);	


		        return $result->row()->payments;


	}


		


	/*function sumPayments()


	{


		$sql = "Select


 					 ifnull(sum((ifnull(payment_amount,0))+balance_amount),0) as total_amount


			From scheme_account sa


			Left join payment p On (sa.id_scheme_account=p.id_scheme_account And payment_status=1)


			Where id_customer='".$this->session->userdata('cus_id')."'


			Group By sa.id_customer";


				$result = $this->db->query($sql);	


		        return $result->row()->total_amount;


	}	*/
	function sumPayments()
	{
	    $sql =  $this->db->query("Select (ifnull (balance_amount,0)+sum(ifnull(payment_amount,0))) as total_amount From scheme_account sa
	       Left join payment p On (sa.id_scheme_account=p.id_scheme_account And payment_status=1)
	   Where is_closed=0 and id_customer='".$this->session->userdata('cus_id')."'
	Group By sa.id_scheme_account");

	    $total=0;
	    foreach($sql->result_array() as $row)
		{
		  $total=$total+$row['total_amount'];

		}	

	    return $total;
	}


	


	function get_currency()


	{


		$sql = "Select


 						currency_name,


						currency_symbol 


			from chit_settings ";


			


				$result = $this->db->query($sql);	


		        return $result->row()->currency_symbol;


		


	}


	


	function total_payments()


	{


		$sql = "Select


 					 count(p.id_payment) as payments


			From scheme_account sa


			Left join payment p On (sa.id_scheme_account=p.id_scheme_account And payment_status=1)


			Where is_closed=0 and id_customer='".$this->session->userdata('cus_id')."'


			Group By sa.id_customer";


				$result = $this->db->query($sql);	


		        return $result->row()->payments;


	}	


	


	function get_pdcs()


	{


		$sql="Select count(pp.id_post_payment) as pdc


				From postdate_payment pp


				Left Join scheme_account sa On (pp.id_scheme_account=sa.id_scheme_account)


				Where (pp.payment_status=7 or pp.payment_status=2) and sa.id_customer='".$this->session->userdata('cus_id')."'";


		return $this->db->query($sql)->row()->pdc;	 


	}


	


	function total_closed_acc()


	{


		$sql="Select count(id_scheme_account) as closed_acc 


				From scheme_account 


				Where is_closed=1 and active=0 and id_customer='".$this->session->userdata('cus_id')."'";


		return $this->db->query($sql)->row()->closed_acc;	 


	}


	


	function unpaids()


	{


		$sql = "Select


				  ifnull(count(sa.id_scheme_account),0) as dues


				From scheme_account sa


				Left join payment p On (sa.id_scheme_account=p.id_scheme_account And payment_status=1 and date(date_payment)=date(now()))


				Where sa.active=1 and sa.is_closed=0 and id_customer='".$this->session->userdata('cus_id')."'


				Group By sa.id_customer";


				$result = $this->db->query($sql);	


		        return $result->row()->dues;


	}


	


	


	function getChitSummary($chitID)


	{


		$chitSummary = '';


		$query = $this->db->query('SELECT ref_no AS scheme_acc_number, scheme_name, DATE_FORMAT(start_date,"%d-%m-%Y") AS start_date, if(is_opening = 1,paid_installments,0) AS paid_installments, if(is_opening=1, balance_amount,0) AS balance_amount, if(is_opening=1, balance_weight,0) AS paid_weight, if(is_opening, balance_amount,0) AS balance_amount, total_installments, SUM(IFNULL(payment_amount,0)) AS total_amount,SUM(IFNULL(metal_weight,0)) AS metal_weight, scheme_type,


									COUNT(DISTINCT(DATE_FORMAT(date_payment,"%Y%m"))) AS totalPaidIns, is_closed


									FROM scheme_account AS schAcc


									LEFT JOIN scheme AS sch ON sch.id_scheme = schAcc.id_scheme


									LEFT JOIN payment AS pay ON pay.id_scheme_account = schAcc.id_scheme_account


									WHERE schAcc.id_scheme_account = '.$chitID);


				if($query->num_rows() > 0)


				{


					$chitSummary = $query->result_array();


				}


		  echo json_encode($chitSummary);


	}


	


	function getCompanyDetail()


	{


		$sql="Select * from company where id_company=1";


		


		return $this->db->query($sql)->row_array();


	}


	function get_exisRegReq()
	{
	    $showGCodeInAcNo = $this->config->item('showGCodeInAcNo'); 
		$query = $this->db->query("SELECT if(remark = '','-',remark) as remark,id_customer,
		CONCAT(if(".$showGCodeInAcNo."=1,if(has_lucky_draw = 1,sg.group_code,sch.code),'') ,' ',ifnull(schReg.scheme_acc_number,'Not Allocated')) as scheme_acc_number,
		sch.code as group_code,schReg.ac_name,DATE_FORMAT(schReg.date_add,'%d-%m-%Y') AS date_add,schReg.status,schReg.id_scheme,schReg.ac_name AS ac_name  
		from scheme_reg_request schReg
		LEFT JOIN scheme AS sch ON sch.id_scheme = schReg.id_scheme	
		LEFT JOIN scheme_group as sg on  sg.id_scheme_group=schReg.id_scheme_group	
		join chit_settings cs
		WHERE id_customer = ".$this->session->userdata('cus_id')." order by id_reg_request desc");	
		//LEFT JOIN branch AS br ON br.id_branch = schReg.id_branch	
		if($query->num_rows()>0)
		{
			return $query->result_array();
		}
	}





}


?>