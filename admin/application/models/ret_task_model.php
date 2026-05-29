<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ret_task_model extends CI_Model
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

	
	function ajax_getTask($employee_filter)
	{

		$this->db->query("UPDATE ret_tasks SET is_viewed=1 WHERE task_assign_to=".$this->session->userdata('uid'));
		$this->db->query("UPDATE ret_tasks SET view_owned_by=1 WHERE task_status=2 and task_created_by=".$this->session->userdata('uid'));
    
		$id_profile=$this->session->userdata('profile');
		$id_employee=$this->session->userdata('uid');
		$sql=$this->db->query("SELECT t.id_task,concat(e.firstname,'',IFNULL(e.emp_code,'')) as emp_name,emp.firstname as created_by,t.task_name,
		date_format(t.task_created_on,'%d-%m-%Y') as created_dt,if(t.task_status=0,'Created',if(t.task_status=1,'Assigned',if(t.task_status=2,'Completed','Cancelled'))) as status,t.task_status,date_format(t.completed_on,'%d-%m-%Y') as completed_date
		FROM ret_tasks t
		LEFT JOIN employee e ON e.id_employee=t.task_assign_to
		LEFT JOIN employee emp ON emp.id_employee=t.task_created_by
		where t.id_task is not null
		".($id_profile!=1 && $id_profile!=2 && $id_profile!=3 ? " and t.task_assign_to=".$id_employee."" :'')."
		".($employee_filter!='' && $employee_filter>0 ? " and t.task_assign_to=".$employee_filter."" :'')."
		");
		return $sql->result_array();
	}

	function getTask($id_task)
	{
		$sql=$this->db->query("SELECT * FROM ret_tasks where id_task=".$id_task."");
		return $sql->row_array();
	}

	function get_ActiveProfile()
	{
		$sql=$this->db->query("SELECT * FROM profile where id_profile!=".$this->session->userdata('profile'));
		return $sql->result_array();
	}
	
	function get_employee_details($id_profile,$id_branch)
	{
	    $return_data=array();
		$sql=$this->db->query("SELECT concat(firstname,'-',IFNULL(lastname,'')) as emp_name,id_employee,login_branches,cs.login_branch
		FROM employee 
		JOIN chit_settings cs
		where id_employee!=".$this->session->userdata('uid')."
		".($id_profile!='' && $id_profile>0  ? " and id_profile=".$id_profile."" :'')."");
		$employee= $sql->result_array();
       /* if(sizeof($employee) > 0){
            if($id_branch==0 ||  $id_branch == '' || $employee[0]['login_branch'] == 0)
            {
                $return_data=$employee;
            }
            else
            {
                foreach($employee as $emp)
                {
                    if($id_branch!=0 || $id_branch='')
                    {
                        $login_branches = explode(',',$emp['login_branches']);
                        foreach($login_branches as $b)
                        {
                            if($b == $id_branch)
                            {
                                $return_data[]=$emp;
                            }
                        }
                    }
                    else
                    {
                        $return_data[]=$emp;
                    }
                }
            }
        }*/
        return $employee;
	}

	function get_ActiveEmployee($id_profile)
	{
		$sql=$this->db->query("SELECT concat(firstname,'-',IFNULL(lastname,'')) as emp_name,id_employee FROM employee 
		where id_employee!=".$this->session->userdata('uid')."
		".($id_profile!='' && $id_profile>0 ? " and id_profile=".$id_profile."" :'')."");
		return $sql->result_array();
	}

	function getNoticeboardDetails($id_noticeboard)
	{
		$sql=$this->db->query("SELECT n.id_noticeboard,n.noticeboard_text,n.noticeboard_status,date_format(n.reminder_on,'%d-%m-%Y') as reminder_on,n.visible_to
			FROM ret_noticeboard n
			where n.id_noticeboard=".$id_noticeboard);
		return $sql->row_array();
	}

	function get_branch_edit($id_noticeboard){
		$sql = $this->db->query("SELECT * FROM ret_noticeboard_view_details where id_noticeboard=".$id_noticeboard."");
				 $visible_to = array_map(function ($value) 
				 {
					return  $value['id_employee'];
				  }, $sql->result_array()); 
		return $visible_to; 
	}

	function ajax_getNoticeBoard()
	{
		$return_data=array();
	
		$id_profile=$this->session->userdata('profile');
		$sql=$this->db->query("SELECT n.id_noticeboard,n.noticeboard_text,DATE_FORMAT(n.created_on,'%d-%m-%Y') as date_add,e.firstname as emp_name,if(n.noticeboard_status=1,'Active','Inactive') as status,n.noticeboard_status,n.visible_to,
		p.profile_name,date_format(n.reminder_on,'%d-%m-%Y') as reminder_on
		FROM ret_noticeboard n
		LEFT JOIN employee e on e.id_employee=n.created_by
		LEFT JOIN profile p on p.id_profile=n.visible_to
		LEFT JOIN ret_noticeboard_view_details v on v.id_noticeboard=n.id_noticeboard
		where n.id_noticeboard is not null ".($id_profile==1 || $id_profile==2 || $id_profile==3 ? '' :" and v.id_employee=".$this->session->userdata('uid')."")."
		group by v.id_noticeboard");
		$data=$sql->result_array();
		foreach($data as $items)
		{
		    $noticeboard_status=1;
		    $valid_date=strtotime($items['reminder_on']);
		    $current_date=strtotime(date("Y-m-d"));
		    
		   /* if($valid_date<$current_date)
		    {
		        $noticeboard_status=0;
		        $this->updateData(array('noticeboard_status'=>0),'id_noticeboard',$items['id_noticeboard'],'ret_noticeboard');
		    }*/
		   
			if($id_profile==1 || $id_profile==2 || $id_profile==3)
			{
				$return_data[]=array(
								'id_noticeboard'	=>$items['id_noticeboard'],
								'noticeboard_text'	=>$items['noticeboard_text'],
								'date_add'			=>$items['date_add'],
								'profile_name'		=>($items['visible_to']==0 ? 'All' :$items['profile_name']),
								'emp_name'			=>$items['emp_name'],
								'reminder_on'		=>$items['reminder_on'],
								'status'			=>$items['status'],
								'noticeboard_status'=>$items['noticeboard_status'],
								'visible_to'		=>$items['visible_to'],
								);
			}else{
					$profiles = explode(',',$items['visible_to']);
				
					foreach($profiles as $pro)
					{
							if(($pro == $id_profile) || $pro==0)
							{
								$return_data[]=array(
											'id_noticeboard'	=>$items['id_noticeboard'],
											'noticeboard_text'	=>$items['noticeboard_text'],
											'date_add'			=>$items['date_add'],
											'emp_name'			=>$items['emp_name'],
											'reminder_on'		=>$items['reminder_on'],
											'status'			=>$items['status'],
											'noticeboard_status'=>$noticeboard_status,
											'visible_to'		=>$items['visible_to'],
											);
							}
					}
			}
		}
		return $return_data;
	}
	
	function gete_notice_board_view_details($id_noticeboard)
	{
	    $sql=$this->db->query("select * from ret_noticeboard_view_details where id_noticeboard=".$id_noticeboard."");
	    return $sql->result_array();
	}


	
	
}
?>