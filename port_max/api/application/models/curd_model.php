<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Curd_model extends CI_Model
{
	function __construct()
    {
        parent::__construct();
    }
    // General Functions
    public function insertData($data,$table)
    {
    	$insert_flag = 0;
		$insert_flag = $this->db->insert($table, $data);
		return ($insert_flag == 1 ? $this->db->insert_id(): 0);
	}
	public function insertBatchData($data,$table)
    {
    	$insert_flag = 0;
		$insert_flag = $this->db->insert_batch($table, $data);
		if ($this->db->affected_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	public function updateData($data, $id_field, $id_value, $table)
    {    
	    $edit_flag = 0;
	    if($id_field!='' && $id_value!='')
	    {
	        $this->db->where($id_field, $id_value);
	    }
		$edit_flag = $this->db->update($table,$data);
		return ($edit_flag==1?$id_value:0);
	}	 
	public function deleteData($id_field,$id_value,$table)
    {
        $this->db->where($id_field, $id_value);
        $status= $this->db->delete($table); 
		return $status;
	}
	
}
?>