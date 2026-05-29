<?php

class Log_model extends CI_Model

{

	const LOG_TABLE 		= "log";

	const LOG_DET_TABLE		= "log_detail";

	const EMP_TABLE			= "employee";

	



	function get_log_range($from_date,$to_date)

	{

						$sql = "Select 

										id_log,

										lg.id_employee,

										lg.login_on,

										logout_on,

										CONCAT(e.firstname,' ',e.lastname) as emp_name

							    From  ".self::LOG_TABLE." lg

							    left join  ".self::EMP_TABLE." e on (lg.id_employee = e.id_employee)

							    		 Where ( date(lg.login_on) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')	   " ;

						   return $this->db->query($sql)->result_array();

						

	}

						   

	function log($type="",$id="",$data="")

	{

		switch($type){

			case 'get':

						$sql = "Select 

										id_log,

										lg.id_employee,

										login_on,

										logout_on,

										CONCAT(e.firstname,' ',e.lastname) as emp_name

							    From  ".self::LOG_TABLE." lg

							    left join  ".self::EMP_TABLE." e on (lg.id_employee = e.id_employee)

							    			   " ;

						   return $this->db->query($sql)->result_array();

				 break;		   

			case 'insert': //insert operation

		                $status = $this->db->insert(self::LOG_TABLE,$data);

 						return	array('status'=>$status,'insertID'=>($status == TRUE ? $this->db->insert_id():''));

			      break; 

			case 'update': //update operation

						 $this->db->where("id_log",$id);

			             $status = $this->db->update(self::LOG_TABLE,$data);

					     return	array('status' => $status, 'updateID' => $id);     			

			      break;    

			case 'delete':

				   $this->db->where("id_log",$id);

		           $status = $this->db->delete(self::LOG_TABLE);

				   return	array('status' => $status, 'DeleteID' => $id);  	

			      break; 

			

		}

	}	

	

	function get_log_detail_range($from_date,$to_date)

	{

	

						$sql = "Select 

										id_log_detail,

										id_log,

										event_date,

										module,

										operation,

										record,

										remark

							    From  ".self::LOG_DET_TABLE."

							     Where (  date(event_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')	";

						   return $this->db->query($sql)->result_array();

	}

						   

	function log_detail($type="",$id="",$data="")

	{

		switch($type){

			case 'get':

						$sql = "Select 

										id_log_detail,

										id_log,

										event_date,

										module,

										operation,

										record,

										remark

							    From  ".self::LOG_DET_TABLE;

						   return $this->db->query($sql)->result_array();

			     break;		

			case 'record':

						$sql = "Select 

										id_login_detail,

										id_log,

										event_date,

										module,

										operation,

										record,

										remark

							    From  ".self::LOG_DET_TABLE." 

							    Where id_log=".$id;

						   return $this->db->query($sql)->result_array();

			     break;

			case 'insert': //insert operation

		                $status = $this->db->insert(self::LOG_DET_TABLE,$data);

 						return	array('status'=>$status,'insertID'=>($status == TRUE ? $this->db->insert_id():''));

			      break; 

			case 'update': //update operation

						 $this->db->where("id_log_detail",$id);

			             $status = $this->db->update(self::LOG_DET_TABLE,$data);

					     return	array('status' => $status, 'updateID' => $id);     			

			      break;    

			case 'delete':

				   $this->db->where("id_log_detail",$id);

		           $status = $this->db->delete(self::LOG_DET_TABLE);

				   return	array('status' => $status, 'DeleteID' => $id);  	

			      break; 

			

		}

	}

	function get_form_logger_log_list($emp_id = 0, $from_date = "-", $to_date = "-") {

		$sql=$this->db->query("SELECT

								fl.log_id,

								fl.log_datetime,

								fl.log_emp_id,

								fl.log_form,

								fl.log_operation,

								fl.log_url,

								fl.log_ip,

								e.firstname AS emp_name

							FROM form_logger fl

							LEFT JOIN employee e ON e.id_employee = fl.log_emp_id

							WHERE 1

							".($emp_id != '' && $emp_id > 0 ? ' AND fl.log_emp_id='.$emp_id: '')."

							".($from_date != '' && $to_date != '' && $from_date != '-' && $to_date != '-' ? 'AND DATE(fl.log_datetime) BETWEEN "'.$from_date.'" AND "'.$to_date.'"' : '')."

							ORDER BY fl.log_datetime DESC");

        return $sql->result_array();

	}

	function get_form_log_data($log_id) {

		$sql=$this->db->query("SELECT

								fl.log_data,

								fl.log_useragent

							FROM form_logger fl

							WHERE fl.log_id = ".$log_id."
							
							");

        return $sql->row_array();

	}

}	

?>