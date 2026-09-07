<?php
require_once(APPPATH . 'third_party/PHPExcel.php');
class Admin_settings_model extends CI_Model
{
    const OFF_IMG_PATH = 'assets/img/offers/';
    const VILLAGE_TABLE = "village";
    //	const BRANCH_TABLE     ="branch";
    public function __construct()
    {
        parent::__construct();
        ini_set('date.timezone', 'Asia/Calcutta');
        $this->load->library('datatables');
    }
    function update_metalrate_status($data, $id_branch)
    {
        $this->db->where("id_branch", $id_branch);
        $status = $this->db->update("branch_rate", $data);
        return array('status' => $status);
    }
    function get_branch_rate($id_branch)
    {
        $sql = "select m.silverrate_1gm from metal_rates m 
	                left join branch_rate b on b.id_metalrate=m.id_metalrates
	                 where b.id_branch=" . $id_branch . " order by m.id_metalrates DESC";
        return $this->db->query($sql)->row_array();
    }
    public function insertData($data, $table)
    {
        $insert_flag = 0;
        $insert_flag = $this->db->insert($table, $data);
        return ($insert_flag == 1 ? $this->db->insert_id() : 0);
    }
    public function updateData($data, $id_field, $id_value, $table)
    {
        $edit_flag = 0;
        $this->db->where($id_field, $id_value);
        $edit_flag = $this->db->update($table, $data);
        return ($edit_flag == 1 ? $id_value : 0);
    }
    //generating menu
    function menu_generation($id_profile)
    {
        $sql = "Select 
	  			p.id_profile,
	  			m.id_menu,
	  			m.label,
	  			m.link,
	  			m.parent,
	  			(select count(id_menu) from menu where parent=m.id_menu) as submenus,
	  			m.icon,
	  			a.`view`,
	  			a.`add`,
	  			a.`edit`,
	  			a.`delete`
			 From menu m
			 Left Join access a On (m.id_menu=a.id_menu)
			 Left Join profile p On(a.id_profile=p.id_profile)
			 Where m.active =1 And m.id_menu>1 And a.view=1 And p.id_profile=" . $id_profile .
            " Order By m.sort,m.parent,m.id_menu";
        $access = $this->db->query($sql)->result_array();
        $menu = "<ul class='sidebar-menu'> <li class='header'>MAIN NAVIGATION</li>";
        foreach ($access as $key => $item) {
            if ($item['parent'] == 1) {
                if ($item['submenus'] == 0) {
                    $menu .= "<li><a href='" . site_url("/" . $item['link']) . "'>" . ($item['icon'] != NULL ? '<i class="' . $item['icon'] . '"></i>' : '') . " <span>" . $item['label'] . "</span></a></li>";
                    unset($access[$key]);
                } else {
                    $menu .= "<li class='treeview'>
					              <a href='" . site_url("/" . $item['link']) . "'>" . ($item['icon'] != NULL ? '<i class="' . $item['icon'] . '"></i>' : '') . "
					                <span>" . $item['label'] . "</span>
					                <i class='fa fa-angle-left pull-right'></i>
					              </a>";
                    unset($access[$key]);
                    $menu .= $this->submenu($access, $item['id_menu']);
                    $menu .= "</li>";
                }
            }
        }
        $menu .= "</ul>";
        return $menu;
    }
    //generate child menu
    function submenu($items, $parent)
    {
        $submenu = "<ul class='treeview-menu'>";
        foreach ($items as $key => $item) {
            if ($item['parent'] == $parent) {
                $submenu .= "<li><a href='" . site_url("/" . $item['link']) . "'>" . ($item['icon'] != NULL ? '<i class="' . $item['icon'] . '"></i>' : '') . " <span>" . $item['label'] . "<span></a></li>";
                unset($items[$key]);
            }
        }
        $submenu .= "</ul>";
        return $submenu;
    }
    //get permission by url
    function get_access($url)
    {
        $sql = "Select
			    a.id_profile,a.id_menu,a.view,a.add,a.edit,a.delete,p.allow_acc_closing
			From access a
			Left Join menu m On(a.id_menu=m.id_menu)
			Left Join profile p On(p.id_profile=a.id_profile)
			where a.id_profile=" . $this->session->userdata('profile') . " and m.link='" . $url . "'";
        return $this->db->query($sql)->row_array();
    }
    function get_dashboard_access()
    {
        $sql = "Select
			    a.id_profile,a.id_dashboardmenu,m.label,a.view,a.add,a.edit,a.delete,a.is_default,p.allow_acc_closing
			From dashboard_access a
			Left Join dashboard_menu m On(a.id_dashboardmenu=m.id_dashboardmenu)
			Left Join profile p On(p.id_profile=a.id_profile)
			where m.active = 1 and a.id_profile=" . $this->session->userdata('profile') . " ";
        return $this->db->query($sql)->result_array();
    }
    //menu db operations
    function menuDB($type = "", $id = "", $menu_array = "")
    {
        switch ($type) {
            case 'get':
                if ($id != NULL) {
                    $sql = "SELECT id_menu,label,link,parent,(select label from menu where id_menu=m.parent) as parentname,(select count(id_menu) from menu where parent=m.id_menu) as submenus,sort,icon,active FROM menu m
				  		  WHERE m.id_menu>1 and m.id_menu=" . $id;
                    $menu = $this->db->query($sql);
                    return $menu->row_array();
                } else {
                    $sql = "SELECT id_menu,label,link,parent,(select label from menu where id_menu=m.parent) as parentname,(select count(id_menu) from menu where parent=m.id_menu) as submenus,sort,icon,active FROM menu m
				  	      WHERE m.id_menu IS NOT NULL";
                    $menu = $this->db->query($sql);
                    return $menu->result_array();
                }
                break;
            case 'insert':
                $status = $this->db->insert("menu", $menu_array);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'update':
                $this->db->where("id_menu", $id);
                $status = $this->db->update("menu", $menu_array);
                return array('status' => $status, 'updateID' => $id);
                break;
            case 'delete':
                $this->db->where("id_menu", $id);
                $status = $this->db->delete("menu");
                return array('status' => $status, 'DeleteID' => $id);
                break;
            default:
                return array(
                    'id_menu' => NULL,
                    'label' => NULL,
                    'link' => NULL,
                    'sort' => 0,
                    'parent' => 1,
                    'icon' => '',
                    'active' => 1,
                );
        }
    }
    //profile db operations
    function profileDB($type = "", $id = "", $profile_array = "")
    {
        switch ($type) {
            case 'get':
                $userType = $this->session->userdata('profile');
                if ($id != NULL) {
                    $sql = "Select * From profile Where id_profile=" . $id . ($userType != 1 ? ' And id_profile <> 1 ' : ' ');
                    $menu = $this->db->query($sql);
                    // print_r($this->db->last_query());exit;
                    return $menu->row_array();
                } else {
                    $sql = "Select * From profile" . ($userType != 1 ? ' Where id_profile <> 1 ' : ' ');
                    $menu = $this->db->query($sql);
                    //    print_r($this->db->last_query());exit;
                    return $menu->result_array();
                }
                break;
            case 'insert':
                $status = $this->db->insert("profile", $profile_array);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'update':
                $this->db->where("id_profile", $id);
                $status = $this->db->update("profile", $profile_array);
                return array('status' => $status, 'updateID' => $id);
                break;
            case 'delete':
                $this->db->where("id_profile", $id);
                $status = $this->db->delete("profile");
                return array('status' => $status, 'DeleteID' => $id);
                break;
        }
    }
    //userrights db operations 
    function PermissionDB($type = "", $id = "", $id_menu = "", $access_array = "")
    {
        switch ($type) {
            case 'empty':
                $items = array();
                $sql = "SELECT id_menu,label,link,parent,(select label from menu where id_menu=m.parent) as parentname,(select count(id_menu) from menu where parent=m.id_menu) as submenus,sort,icon,active FROM menu m
				  		  WHERE m.active=1 and  m.id_menu>1 " . ($id != 1 ? " And m.id_menu<>17 " : "") . ($id_menu != '' ? " And parent=" . $id_menu : "") . ($id == 1 || $id == 2 ? "" : "  And m.id_menu<>18 ") .
                    " Order By parent,sort,id_menu ";
                $menus = $this->db->query($sql)->result_array();
                foreach ($menus as $menu) {
                    $items[] = array(
                        'id_menu' => $menu['id_menu'],
                        'label' => $menu['label'],
                        'submenus' => $menu['submenus'],
                        'view' => 0,
                        'add' => 0,
                        'edit' => 0,
                        'delete' => 0
                    );
                }
                return $items;
                break;
            case 'get':
                $userType = $this->session->userdata('profile');
                if ($id != NULL) {
                    $sql = "Select 
				  			p.id_profile,
				  			m.id_menu,
				  			m.label,
				  			m.link,
				  			m.parent,
				  			(select count(id_menu) from menu where parent=m.id_menu) as submenus,
				  			m.icon,
				  			m.sort,
				  			a.`view`,
				  			a.`add`,
				  			a.`edit`,
				  			a.`delete`
						 From menu m
						 Left Join access a On (m.id_menu=a.id_menu)
						 Left Join profile p On(a.id_profile=p.id_profile)
						 Where  (m.active =1 And m.id_menu>1) And p.id_profile=" . $id . ($id_menu != '' ? " And m.parent=" . $id_menu : "") . ($id != 1 ? " And m.id_menu<>17 " : "") . ($id == 1 || $id == 2 ? " " : " And m.id_menu<>18 ") .
                        " Order By m.parent,m.sort,m.id_menu";
                    $access = $this->db->query($sql);
                    if ($access->num_rows() > 0) {
                        return $access->result_array();
                    } else {
                        return $access->num_rows();
                    }
                } else {
                    $sql = "Select 
				  			p.id_profile,
				  			m.id_menu,
				  			m.label,
				  			m.link,
				  			m.parent,
				  			(select count(id_menu) from menu where parent=m.id_menu) as submenus,
				  			m.icon,
				  			a.`view`,
				  			a.`add`,
				  			a.`edit`,
				  			a.`delete`
						 From menu m
						 Left Join access a On (m.id_menu=a.id_menu)
						 Left Join profile p On(a.id_profile=p.id_profile)
						 Where m.active =1 And m.id_menu>1 ";
                    $access = $this->db->query($sql);
                    if ($access->num_rows() > 0) {
                        return $access->result_array();
                    } else {
                        return $this->PermissionDB("empty");
                    }
                }
                break;
            case 'exist':
                $sql = "Select * From access Where id_profile=" . $id . " And id_menu=" . $id_menu;
                $access = $this->db->query($sql);
                if ($access->num_rows() > 0) {
                    return TRUE;
                } else {
                    return FALSE;
                }
                break;
            case 'insert':
                unset($access_array->row_access);
                $status = $this->db->insert("access", (array) $access_array);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'update':
                unset($access_array->row_access);
                $this->db->where("id_profile", $id);
                $this->db->where("id_menu", $id_menu);
                $status = $this->db->update("access", (array) $access_array);
                return array('status' => $status, 'updateID' => array('id_profile' => $id, 'id_menu' => $id_menu));
                break;
        }
    }
    function DashboardPermissionDB($type = "", $id = "", $id_menu = "", $access_array = "")
    {
        switch ($type) {
            case 'empty':
                $items = array();
                $sql = "SELECT id_dashboardmenu as id_menu,label,link,parent,(select label from dashboard_menu where id_dashboardmenu=m.parent) as parentname,(select count(id_dashboardmenu) from dashboard_menu where parent=m.id_dashboardmenu) as submenus,sort,icon,active FROM dashboard_menu m
				  		  WHERE m.active=1 and  m.id_dashboardmenu>2 " . ($id != 1 ? " And m.id_dashboardmenu<>17 " : "") . ($id_menu != '' ? " And parent=" . $id_menu : "") . ($id == 1 || $id == 2 ? "" : "  And m.id_dashboardmenu<>18 ") .
                    " Order By parent,sort,id_dashboardmenu";
                $menus = $this->db->query($sql)->result_array();
                foreach ($menus as $menu) {
                    $items[] = array(
                        'id_menu' => $menu['id_menu'],
                        'label' => $menu['label'],
                        'submenus' => $menu['parent'] == 1 ? ($menu['submenus'] == 0 ? 1 : $menu['submenus']) : 0,
                        'view' => 0,
                        'add' => 0,
                        'edit' => 0,
                        'delete' => 0,
                        'is_default' => 0
                    );
                }
                return $items;
                break;
            case 'get':
                $userType = $this->session->userdata('profile');
                if ($id != NULL) {
                    $sql = "Select 
				  			p.id_profile,
				  			m.id_dashboardmenu as id_menu,
				  			m.label,
				  			m.link,
				  			m.parent,
				  			(select count(id_dashboardmenu) from dashboard_menu where parent=m.id_dashboardmenu) as submenus,
				  			m.icon,
				  			m.sort,
				  			a.`view`,
				  			a.`add`,
				  			a.`edit`,
							a.`is_default`,
				  			a.`delete`
						 From dashboard_menu m
						 Left Join dashboard_access a On (m.id_dashboardmenu=a.id_dashboardmenu)
						 Left Join profile p On(a.id_profile=p.id_profile)
						 Where  (m.active =1 And m.id_dashboardmenu>2) And p.id_profile=" . $id . ($id_menu != '' ? " And m.parent=" . $id_menu : "") . ($id != 1 ? " And m.id_dashboardmenu<>17 " : "") . ($id == 1 || $id == 2 ? " " : " And m.id_dashboardmenu<>18 ") .
                        " Order By m.parent,m.sort,m.id_dashboardmenu";
                    $access = $this->db->query($sql);
                    // print_r($this->db->last_query($access));
                    if ($access->num_rows() > 0) {
                        return $access->result_array();
                    } else {
                        return $access->num_rows();
                    }
                } else {
                    $sql = "Select 
				  			p.id_profile,
				  			m.id_dashboardmenu as id_menu,
				  			m.label,
				  			m.link,
				  			m.parent,
				  			(select count(id_dashboardmenu) from menu where parent=m.id_dashboardmenu) as submenus,
				  			m.icon,
				  			a.`view`,
				  			a.`add`,
				  			a.`edit`,
							a.`is_default`,
				  			a.`delete`
						 From menu m
						 Left Join dashboard_access a On (m.id_dashboardmenu=a.id_dashboardmenu)
						 Left Join profile p On(a.id_profile=p.id_profile)
						 Where m.active =1 And m.id_dashboardmenu>2";
                    $access = $this->db->query($sql);
                    if ($access->num_rows() > 0) {
                        return $access->result_array();
                    } else {
                        return $this->PermissionDB("empty");
                    }
                }
                break;
            case 'exist':
                $sql = "Select * From dashboard_access Where id_profile=" . $id . " And id_dashboardmenu=" . $id_menu;
                $access = $this->db->query($sql);
                if ($access->num_rows() > 0) {
                    return TRUE;
                } else {
                    return FALSE;
                }
                break;
            case 'insert':
                $access_array->id_dashboardmenu = $access_array->id_menu;
                unset($access_array->id_menu);
                unset($access_array->row_access_dash);
                $status = $this->db->insert("dashboard_access", (array) $access_array);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'update':
                $access_array->id_dashboardmenu = $access_array->id_menu;
                // echo '<pre>';
                //print_r($access_array);
                unset($access_array->id_menu);
                unset($access_array->row_access_dash);
                $this->db->where("id_profile", $id);
                $this->db->where("id_dashboardmenu", $id_menu);
                $status = $this->db->update("dashboard_access", (array) $access_array);
                return array('status' => $status, 'updateID' => array('id_profile' => $id, 'id_menu' => $id_menu));
                break;
        }
    }
    function get_menu_link($url)
    {
        $sql = "SELECT mu.id_menu, mu.label, mu.link FROM menu mu 
		WHERE mu.link = '" . $url . "' ORDER BY mu.id_menu ASC";
        return $this->db->query($sql)->row_array();
    }
    //bank master db operations	
    function bankDB($type = "", $id = "", $bank_array = "")
    {
        switch ($type) {
            case 'get':
                if ($id != NULL) {
                    $sql = "Select id_bank, bank_name,short_code,acc_number,ifsc_code,active_bank From bank Where id_bank=" . $id;
                    return $this->db->query($sql)->row_array();
                } else {
                    $sql = "Select id_bank, bank_name,short_code,acc_number,ifsc_code,active_bank From bank";
                    return $this->db->query($sql)->result_array();
                }
                break;
            case 'insert':
				// coded by jothika on 10-7-2025 [prevent insert and update duplicate entries]
				$bank_name = $bank_array['bank_name'];
				$query = $this->db->where('bank_name', $bank_name)->get('bank');
				if ($query->num_rows() == 0) {
                $status = $this->db->insert("bank", $bank_array);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
				} else {
					return false;
				}
                break;
            case 'update':
				$bank_name = $bank_array['bank_name'];
				$current = $this->db->where('id_bank', $id)->get('bank')->row();
				if ($current && $current->bank_name == $bank_name) {
                $this->db->where("id_bank", $id);
                $status = $this->db->update("bank", $bank_array);
                return array('status' => $status, 'updateID' => $id);
				}
				$query = $this->db->where('bank_name', $bank_name)
					->where('id_bank !=', $id)
					->get('bank');
				if ($query->num_rows() == 0) {
					$this->db->where("id_bank", $id);
					$status = $this->db->update("bank", $bank_array);
					return array('status' => $status, 'updateID' => $id);
				} else {
					return array('status' => false, 'reason' => 'Duplicate');
				}
                break;
            case 'delete':
                $this->db->where("id_bank", $id);
                $status = $this->db->delete("bank");
                return array('status' => $status, 'DeleteID' => $id);
                break;
            default: //empty record
                return array(
                    'id_bank' => NULL,
                    'bank_name' => NULL,
                    'short_code' => NULL,
                    'acc_number ' => NULL,
                );
        }
    }
    //drawee master db operations	
    function draweeDB($type = "", $id = "", $bank_array = "")
    {
        switch ($type) {
            case 'get':
                if ($id != NULL) {
                    $sql = "Select
						  da.id_drawee,
						  da.account_no,
						  da.account_name,
						  b.id_bank,
						  b.bank_name,
						  b.short_code,
						  da.branch,
						  da.ifsc_code
						From drawee_account da
						Left Join bank b on (da.id_bank=b.id_bank)
						Where da.id_drawee=" . $id;
                    return $this->db->query($sql)->row_array();
                } else {
                    $sql = "Select
						  da.id_drawee,
						  da.account_no,
						  da.account_name,
						  b.id_bank,
						  b.bank_name,
						  b.short_code,
						  da.branch,
						  da.ifsc_code
						From drawee_account da
						Left Join bank b on (da.id_bank=b.id_bank)";
                    return $this->db->query($sql)->result_array();
                }
                break;
            case 'insert':
                $status = $this->db->insert("drawee_account", $bank_array);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'update':
                $this->db->where("id_drawee", $id);
                $status = $this->db->update("drawee_account", $bank_array);
                return array('status' => $status, 'updateID' => $id);
                break;
            case 'delete':
                $this->db->where("id_drawee", $id);
                $status = $this->db->delete("drawee_account");
                return array('status' => $status, 'DeleteID' => $id);
                break;
            default: //empty record
                return array(
                    'id_drawee' => NULL,
                    'account_no' => NULL,
                    'account_name' => NULL,
                    'id_bank' => NULL,
                    'bank_name' => NULL,
                    'branch' => NULL,
                    'ifsc_code' => NULL,
                );
        }
    }
    //payment_mode master db operations	
    function paymodeDB($type = "", $id = "", $mode_array = "")
    {
        switch ($type) {
            case 'get':
                if ($id != NULL) {
                    $sql = "Select id_mode, mode_name,short_code From payment_mode Where id_mode=" . $id;
                    return $this->db->query($sql)->row_array();
                } else {
                    $sql = "Select id_mode, mode_name,short_code From payment_mode";
                    return $this->db->query($sql)->result_array();
                }
                break;
            case 'insert':
				// coded by jothika on 11-7-2025 [prevent insert and update duplicate entries]
				$paymode_name = $mode_array['mode_name'];
				$query = $this->db->where('mode_name', $paymode_name)->get('payment_mode');
				if ($query->num_rows() == 0) {
                $status = $this->db->insert("payment_mode", $mode_array);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
				} else {
					return false;
				}
                break;
            case 'update':
				$paymode_name = $mode_array['mode_name'];
				$current = $this->db->where('id_mode', $id)->get('payment_mode')->row();
				if ($current && $current->mode_name == $paymode_name) {
                $this->db->where("id_mode", $id);
                $status = $this->db->update("payment_mode", $mode_array);
                return array('status' => $status, 'updateID' => $id);
				}
				$query = $this->db->where('mode_name', $paymode_name)
					->where('id_mode !=', $id)
					->get('payment_mode');
				if ($query->num_rows() == 0) {
					$this->db->where("id_mode", $id);
					$status = $this->db->update("payment_mode", $mode_array);
					return array('status' => $status, 'updateID' => $id);
				} else {
					return array('status' => false, 'reason' => 'Duplicate');
				}
                break;
            case 'delete':
                $this->db->where("id_mode", $id);
                $status = $this->db->delete("payment_mode");
                return array('status' => $status, 'DeleteID' => $id);
                break;
            default: //empty record
                return array(
                    'id_mode' => NULL,
                    'mode_name' => NULL,
                    'short_code' => NULL
                );
        }
    }
    // function deviceDB($type = "", $id = "", $device_array = "")
    // {
    //     switch ($type) {
    //         case 'get':
    //             if ($id != NULL) {
    //                 $sql = "Select id_device, device_name From ret_bill_pay_device Where id_device=" . $id;
    //                 return $this->db->query($sql)->row_array();
    //             } else {
    //                 $sql = "Select id_device, device_name From ret_bill_pay_device";
    //                 return $this->db->query($sql)->result_array();
    //             }
    //             break;
    //     }
    // }
    function get_country()
    {
        $sql = "select id_country,name,is_default,mob_no_len,min_mob_len,max_mob_len from country";
        $countries = $this->db->query($sql);
        foreach ($countries->result() as $country) {
            $return_country[] = array(
                'id' => $country->id_country,
                'name' => $country->name,
                'is_default' => $country->is_default,
                'mob_no_len' => $country->mob_no_len,
                'min_mob_len' => $country->min_mob_len,
                'max_mob_len' => $country->max_mob_len,
            );
        }
        return json_encode($return_country);
    }
    function get_state($id_country)
    {
        $sql = "select id_state,name,is_default from state where id_country=" . $id_country;
        //print_r($id_country);exit;
        $states = $this->db->query($sql);
        foreach ($states->result() as $state) {
            $data[] = array(
                'id' => $state->id_state,
                'name' => $state->name,
                'is_default' => $state->is_default
            );
        }
        return json_encode($data);
    }
    function get_city($id_state)
    {
        if ($id_state != '') {
            $sql = "select id_city,name,is_default from city where id_state=" . $id_state;
            $cities = $this->db->query($sql);
            if ($this->session->userdata('id_branch') > 0) {
                $default_city = $this->db->query("Select id_city from branch where id_branch = " . $this->session->userdata('id_branch'))->row();
            } else {
                $default_city = $this->db->query("Select id_city from company LIMIT 1")->row();
            }
            //   print_r($default_city->id_city);exit;
            foreach ($cities->result() as $city) {
                if ($default_city->id_city == $city->id_city) {
                    $data[] = array(
                        'id' => $city->id_city,
                        'name' => $city->name,
                        'is_default' => 1
                    );
                } else {
                    $data[] = array(
                        'id' => $city->id_city,
                        'name' => $city->name,
                        'is_default' => 0
                    );
                }
            }
        } else {
            $data = array();
        }
        return json_encode($data);
    }
    //Weight
    function get_weights()
    {
        $this->db->select('id_weight,weight');
        $this->db->where('active', 1);
        $weights = $this->db->get('weight');
        return $weights->result_array();
    }
    function ajax_get_weights()
    {
        $this->db->select('id_weight,weight');
        $this->db->where('active', 1);
        $weights = $this->db->get('weight');
        return $weights->result_array();
    }
    function get_weight($id)
    {
        $this->db->select('id_weight,weight');
        $this->db->where('id_weight', $id);
        $weights = $this->db->get('weight');
        return $weights->row_array();
    }
    function insert_weight($weight)
    {
        $status = $this->db->insert("weight", $weight);
        return $status;
    }
    function update_weight($data, $id)
    {
        $this->db->where("id_weight", $id);
        $status = $this->db->update("weight", $data);
        return $status;
    }
    function delete_weight($id)
    {
        $this->db->where("id_weight", $id);
        $status = $this->db->delete("weight");
        return $status;
    }
    //Classification
    function get_classifications()
    {
        $logoPath = base_url() . "assets/img/sch_classify/";
        $sql = $this->db->query("select id_classification,classification_name,description,if(logo is null,null,concat('" . $logoPath . "','',logo)) as logo from sch_classify where active=1");
        return $sql->result_array();
    }
    function ajax_get_classifications()
    {
        $logoPath = base_url() . "assets/img/sch_classify/";
        $sql = $this->db->query("select id_classification,classification_name,description,if(logo is null,null,concat('" . $logoPath . "','',logo)) as logo from sch_classify where active=1");
        //echo $this->db->last_query();
        return $sql->result_array();
    }
    function get_classification($id)
    {
        //$logoPath = base_url()."assets/img/sch_classify/"; 
        $sql = $this->db->query("select id_classification,classification_name,description,logo from sch_classify where id_classification=" . $id);
        return $sql->row_array();
    }
    function insert_classification($data)
    {
        $status = $this->db->insert("sch_classify", $data);
        $classification_id = $this->db->insert_id();
        //print_r($this->db->last_query());exit; 
        return ($status == 1 ? $classification_id : 0);
    }
    function update_classification($data, $id)
    {
        $this->db->where("id_classification", $id);
        $status = $this->db->update("sch_classify", $data);
        // print_r($this->db->last_query());exit;
        return $status;
    }
    function delete_classification($id)
    {
        $this->db->where("id_classification", $id);
        $status = $this->db->delete("sch_classify");
        return $status;
    }
    //department
    function ajax_get_depts()
    {
        $this->db->select('id_dept,name');
        $weights = $this->db->get('department');
        return $weights->result_array();
    }
    function get_dept($id)
    {
        $this->db->select('id_dept,name');
        $this->db->where('id_dept', $id);
        $weights = $this->db->get('department');
        return $weights->row_array();
    }
	// coded by jothika on 10-7-2025 [prevent insert and update duplicate entries]
    function insert_dept($dept)
    {
		$dept_name = $dept['name'];
		$query = $this->db->where("name", $dept_name)->get('department');
		if ($query->num_rows() == 0) {
        $status = $this->db->insert("department", $dept);
        return $status;
		} else {
			return false;
		}
		// $status=$this->db->insert("department",$dept);
		// return $status;
    }
    function update_dept($data, $id)
    {
		$dept_name = $data['name'];
		$current = $this->db->where('id_dept', $id)->get('department')->row();
		if ($current && $current->name == $dept_name) {
			$this->db->where('id_dept', $id);
			return $this->db->update('department', $data);
		}
		$query = $this->db->where('name', $dept_name)
			->where('id_dept !=', $id)
			->get('department');
		if ($query->num_rows() == 0) {
        $this->db->where("id_dept", $id);
        $status = $this->db->update("department", $data);
        return $status;
		} else {
			return false;
		}
    }
    function delete_dept($id)
    {
        $this->db->where("id_dept", $id);
        $status = $this->db->delete("department");
        return $status;
    }
    function ajax_get_designs()
    {
        $this->db->select('id_design,name');
        $designs = $this->db->get('designation');
        return $designs->result_array();
    }
    function get_design($id)
    {
        $this->db->select('id_design,name');
        $this->db->where('id_design', $id);
        $designs = $this->db->get('designation');
        return $designs->row_array();
    }
	// coded by jothika on 10-7-2025 [prevent insert and update duplicate entries]
    function insert_design($design)
    {
		$design_name = $design['name'];
		$query = $this->db->where('name', $design_name)->get('designation');
		if ($query->num_rows() == 0) {
        $status = $this->db->insert("designation", $design);
        return $status;
		} else {
			return false;
		}
    }
    function update_design($data, $id)
    {
		$design_name = $data['name'];
		$current = $this->db->where('id_design', $id)->get('designation')->row();
		if ($current && $current->name == $design_name) {
			$this->db->where('id_design', $id);
			return $this->db->update('designation', $data);
		}
		$query = $this->db->where('name', $design_name)
			->where('id_design !=', $id)
			->get('designation');
		if ($query->num_rows() == 0) {
        $this->db->where("id_design", $id);
        $status = $this->db->update("designation", $data);
        return $status;
		} else {
			return false;
		}
    }
    function delete_design($id)
    {
        $this->db->where("id_design", $id);
        $status = $this->db->delete("designation");
        return $status;
    }
    function company_empty_record()
    {
        $data = array(
            'id_company' => NULL,
            'company_name' => NULL,
            'short_code' => NULL,
            'address1' => NULL,
            'address2' => NULL,
            'country' => NULL,
            'state' => NULL,
            'city' => NULL,
            'pincode' => NULL,
            'mobile' => NULL,
            'whatsapp_no' => NULL,
            'mobile1' => NULL,
            'Tollfree1' => NULL,
            'phone' => NULL,
            'phone1' => NULL,
            'email' => NULL,
            'website' => NULL,
            'bank_acc_number' => NULL,
            'bank_acc_name' => NULL,
            'bank_name' => NULL,
            'bank_branch' => NULL,
            'bank_ifsc' => NULL,
            'map_url' => NULL
        );
        return $data;
    }
    function get_comp_list()
    {
        $this->db->select('id_company,company_name,short_code');
        $comps = $this->db->get('company');
        return $comps->result_array();
    }
    function get_company_detail($id)
    {
        $record = $this->db->query("Select * from company where id_company=" . $id);
        return $record->row_array();
    }
    function get_default_country()
    {
        $query = $this->db->query("SELECT * FROM country WHERE is_default = 1");
        return $query->row_array();
    }
    function get_default_state()
    {
        $query = $this->db->query("SELECT * FROM state WHERE is_default = 1");
        return $query->row_array();
    }
    function get_default_city()
    {
        $query = $this->db->query("SELECT * FROM city WHERE is_default = 1");
        return $query->row_array();
    }
    function get_curr_detail($id)
    {
        $this->db->select('*');
        $this->db->where('id_country', $id);
        $currency = $this->db->get('country');//print_r($currency->result_array());	 
        return $currency->row_array();
    }
    function get_company()  // esakki 11-11
    {
        $sql = " Select  cs.company_settings,c.id_company,c.company_name,cs.edit_custom_entry_date,c.comp_name_in_sms,IFNULL(c.gst_number,'') as gst_number,c.short_code,c.pincode,c.mobile,c.whatsapp_no,c.phone,c.email,c.website,c.address1,c.address2,c.id_country,c.id_state,c.id_city,ct.name as city,s.name as state,cy.name as country,cs.currency_symbol,cs.currency_name,cs.mob_code,cs.mob_no_len,c.mail_server,c.mail_password,c.send_through,c.mobile1,c.phone1,c.smtp_user,c.smtp_pass,c.smtp_host,c.server_type,
        cs.login_branch,IFNULL(c.cin_number,'') as cin_number,cs.currency_format,cs.currency_decimal, cs.metal_wgt_decimal, cs.metal_wgt_roundoff
        from company c
        join chit_settings cs
        left join country cy on (c.id_country=cy.id_country)
        left join state s on (c.id_state=s.id_state)
        left join city ct on (c.id_city=ct.id_city)";
        $result = $this->db->query($sql);	//print_r($result->row_array());exit;
        return $result->row_array();
    }
    function insert_import_log($data)
    {
        $status = $this->db->insert('import_log', $data);
        return $status;
    }
    function create_company($data)
    {
        $status = $this->db->insert('company', $data);
        return $status;
    }
    function update_company($data, $id)
    {
        $this->db->where('id_company', $id);
        $status = $this->db->update('company', $data);
        return $status;
    }
    function update_default_country($country_info)
    {
        $this->db->trans_start();
        $this->db->update('country', array('is_default' => 0));
        if (isset($country_info['id_country'])) {
            $this->db->where('id_country', $country_info['id_country']);
            $this->db->update('country', array('is_default' => 1));
        }
        $this->db->update('state', array('is_default' => 0));
        if (isset($country_info['id_state'])) {
            $this->db->where('id_state', $country_info['id_state']);
            $this->db->update('state', array('is_default' => 1));
        }
        $this->db->update('city', array('is_default' => 0));
        if (isset($country_info['id_city'])) {
            $this->db->where('id_city', $country_info['id_city']);
            $this->db->update('city', array('is_default' => 1));
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            return false;
        } else {
            return true;
        }
    }
    //charges master 
    function ajax_get_charges()
    {
        $this->datatables->select('id_charges,payment_mode,code,service_tax,active')->from('charges');
        echo $this->datatables->generate();
    }
    function get_service($id)
    {
        $this->db->select('id_services,serv_code,serv_name,serv_email,serv_sms,dlt_te_id,serv_whatsapp,sms_msg,template_name');
        $this->db->where('id_services', $id);
        $service = $this->db->get('services');
        return $service->row_array();
    }
    function get_charges($id)
    {
        $this->db->select('id_charges,payment_mode,code,service_tax,active');
        $this->db->where('id_charges', $id);
        $weights = $this->db->get('charges');
        return $weights->row_array();
    }
    function get_inactivity_time()
    {
        $this->db->select('value');
        $this->db->where('name', 'inactivity_time');
        $inactivity = $this->db->get('ret_settings');
        $row = $inactivity->row_array();
        return isset($row['value']) ? $row['value'] : 5;
    }
    function get_charges_range($id)
    {
        $this->db->select('id_charges, lower_limit, upper_limit, charge_type, charges_value');
        $this->db->where('id_charges', $id);
        $charges = $this->db->get('charges_range');
        return $charges->result_array();
    }
    function insert_charges($data, $range)
    {
        $status = $this->db->insert('charges', $data);
        if ($status) {
            $insertID = $this->db->insert_id();
            foreach ($range['lower_limit'] as $key => $value) {
                $insertRecord['id_charges'] = $insertID;
                $insertRecord['lower_limit'] = trim($range['lower_limit'][$key]);
                $insertRecord['upper_limit'] = strlen(trim($range['upper_limit'][$key])) > 0 ? trim($range['upper_limit'][$key]) : NULL;
                $insertRecord['charge_type'] = trim($range['charge_type'][$key]);
                $insertRecord['charges_value'] = trim($range['charges_value'][$key]);
                $this->db->insert('charges_range', $insertRecord);
                unset($insertRecord);
            }
        }
        return $status;
    }
    function update_charges($data, $range, $id)
    {
        $this->db->where('id_charges', $id);
        $status = $this->db->delete('charges_range');
        if ($status) {
            $this->db->where('id_charges', $id);
            $status = $this->db->update('charges', $data);
            foreach ($range['lower_limit'] as $key => $value) {
                $insertRecord['id_charges'] = $id;
                $insertRecord['lower_limit'] = trim($range['lower_limit'][$key]);
                $insertRecord['upper_limit'] = strlen(trim($range['upper_limit'][$key])) > 0 ? trim($range['upper_limit'][$key]) : NULL;
                $insertRecord['charge_type'] = trim($range['charge_type'][$key]);
                $insertRecord['charges_value'] = trim($range['charges_value'][$key]);
                $this->db->insert('charges_range', $insertRecord);
                unset($insertRecord);
            }
        }
        return $status;
    }
    function delete_charges($id)
    {
        $this->db->where('id_charges', $id);
        $status = $this->db->delete('charges');
        return $status;
    }
    function max_metalrate()
    {
        $is_branchwise_rate = $this->session->userdata('is_branchwise_rate');
        $id_branch = $this->session->userdata('id_branch');
        $sql = "select max(m.id_metalrates) as max_id from  metal_rates m" . ($is_branchwise_rate == 1 && $id_branch != '' ? " left join branch_rate br on br.id_metalrate=m.id_metalrates where br.id_branch=" . $id_branch . "" : '') . "";
        // print_r($sql);exit;
        return $this->db->query($sql)->row('max_id');
    }
    function metal_ratesDB($type = "", $id = "", $array = "")
    {
        switch ($type) {
            case 'get':
                $sql = "Select
						       m.id_metalrates,cs.is_branchwise_rate,
						       m.updatetime,mjdmagoldrate_22ct,mjdmasilverrate_1gm,
						       m.goldrate_22ct,
						       m.goldrate_18ct,
                               m.goldrate_14ct,
                               m.goldrate_9ct,
						       m.platinum_1g,
						       m.goldrate_24ct,
							   m.market_gold_20ct,
							   m.mjdmasilverrate_999,
							   m.market_gold_995,
                               m.coin_gold_22ct,
						       m.silverrate_1gm,
						       m.silverrate_1kg,
						       m.market_gold_18ct,
						       if(m.id_employee=0,'MJDMA',concat(e.firstname,' ',e.lastname)) as employee
						From  metal_rates m 
						join chit_settings cs
						Left Join employee e on (m.id_employee=e.id_employee) " . ($id != null ? 'Where id_metalrates=' . $id : '') .
                    " Order By m.id_metalrates Desc ";
                $r = $this->db->query($sql);
                //print_r($this->db->last_query());exit;
                if ($id != NULL) {
                    return $r->row_array(); //for single row
                } else {
                    return $r->result_array(); //for multiple rows
                }
                break;
            case 'last': //insert operation
                $last_row = $this->db->select('*')->order_by('id_metalrates', "desc")->limit(1)->get('metal_rates')->row_array();
                return $last_row;
                break;
            case 'insert': //insert operation
                $status = $this->db->insert("metal_rates", $array);
                // print_r($this->db->last_query());exit;
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'update': //update operation
                $this->db->where("id_metalrates", $id);
                $status = $this->db->update("metal_rates", $array);
                // print_r($this->db->last_query());exit;
                return array('status' => $status, 'updateID' => $id);
                break;
            case 'delete':
                $this->db->where("id_metalrates", $id);
                $status = $this->db->delete("metal_rates");
                return array('status' => $status, 'DeleteID' => $id);
                break;
            default: //empty record
                $set = $this->db->query("Select is_branchwise_rate from chit_settings");
                $metal_rates = array(
                    'id_metalrates' => NULL,
                    'mjdmagoldrate_22ct' => 0.00,
                    'goldrate_22ct' => 0.00,
                    'goldrate_24ct' => 0.00,
                    'silverrate_1gm' => 0.00,
                    'silverrate_1kg' => 0.00,
                    'goldrate_18ct' => 0.00,
                    'goldrate_14ct' => 0.00,
                    'coin_gold_22ct' => 0.00,
                    'platinum_1g' => 0.00,
                    'send_notification' => 0,
                    'market_gold_18ct' => 0.00,
                    'mjdmasilverrate_1gm' => 0.00,
                    'is_branchwise_rate' => $set->row('is_branchwise_rate')
                );
                return $metal_rates;
        }
    }
    function rates_by_branch($id_branch)
    {
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $sql = "Select
						       m.id_metalrates,cs.is_branchwise_rate,
						       m.updatetime,mjdmagoldrate_22ct,mjdmasilverrate_1gm,
						       m.goldrate_22ct,
						       m.goldrate_18ct,
                               m.goldrate_14ct,
						       m.platinum_1g,
						       m.goldrate_24ct,
						       m.silverrate_1gm,
						       m.silverrate_1kg,
						       m.market_gold_18ct,
						       if(m.id_employee=0,'MJDMA',concat(e.firstname,' ',e.lastname)) as employee
						From  metal_rates m 
						join chit_settings cs
						Left Join employee e on (m.id_employee=e.id_employee) 
						left join branch_rate br on (br.id_metalrate=m.id_metalrates)
					     " . ($uid != 1 ? ($branch != '' ? ' where br.id_branch=' . $branch . '' : '') : ($id_branch != '' ? ' where br.id_branch=' . $id_branch . '' : '')) . " group by m.id_metalrates Order By m.id_metalrates Desc ";
        $r = $this->db->query($sql);
        // print_r($sql);exit;
        return $r->result_array(); //for multiple rows
    }
    function import_excel($path = "", $filename = "", $isHeading = "")
    {
        try {
            $objPHPExcel = PHPExcel_IOFactory::load($path . $filename);
            //get only the Cell Collection
            $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
            $highestColumm = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
            $totalcolumns = PHPExcel_Cell::columnIndexFromString($highestColumm);
            $totalrows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
            // echo "columns ".($totalcolumns - 1);
            //extract to a PHP readable array format
            foreach ($cell_collection as $cell) {
                $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
                //for filtering heading
                if ($isHeading == 1) {
                    if ($row == 1) {
                        $header[$row][$column] = $data_value;
                    } else {
                        $arr_data[$row][$column] = $data_value;
                    }
                } else {
                    $arr_data[$row][$column] = $data_value;
                }
            }
            return $arr_data;
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($filename, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
    }
    function setting_data($settings)
    {
        $sql = "Select * From chit_settings Where settings='" . $settings . "'";
        return $this->db->query($sql)->row_array();
    }
    //scheme group & Use wallet amt//
    function settingsDB($type = "", $id = "", $set_array = "")
    {
        switch ($type) {
            case 'get':
                if ($id != NULL) {
                    $sql = "Select restrict_lastPayment_days,otp_scheme_join,emp_wallet_account_type,is_pin_required,
					            schemeaccNo_displayFrmt,receiptNo_displayFrmt,chitCollectionEmpCount,
                                isOTPReqToGift,giftOTP_exp,SUBSTRING_INDEX(vs_booking_time, '-', 1) AS fn_from,
								SUBSTRING_INDEX(vs_booking_time, '-', -1) AS  an_to,
								id_chit_settings, 
								currency_symbol,
								currency_name,allow_notification,
								allow_join_multiple,regExistingReqOtp,
								allow_join_unpaid,delete_unpaid,show_closed_list, 
								rate_update,maintenance_mode,maintenance_text,reg_existing,enable_closing_otp,
								gst_setting,newSchjoinonline,allow_wallet,allow_savecard,enableGoldrateDisc,goldDiscAmt,allow_catlog,receipt,edit_addpay_page,branch_settings,schemeacc_no_set,has_lucky_draw, receipt_no_set,is_ratenoti_sent,wallet_account_type,useWalletForChit,walletIntegration,schrefbenifit_secadd,cusplan_type,cusbenefitscrt_type,empplan_type,empbenefitscrt_type,allow_referral,branchWiseLogin,scheme_wise_receipt,
								scheme_wise_acc_no,wallet_balance_type,wallet_amt_per_points,wallet_points,enableSilver_rateDisc,silverDiscAmt,isOTPRegForPayment,isOTPReqToLogin,enable_dth,payOTP_exp,loginOTP_exp,req_otp_login,enable_dth,req_gift_issue_otp,req_prize_issue_otp,metal_wgt_decimal,metal_wgt_roundoff,
								enableGoldrateDisc_18k,goldDiscAmt_18k,is_branchwise_cus_reg,sch_limit,edit_custom_entry_date,getExisting_balance,is_branchwise_rate,branchwise_scheme,custom_entry_date,emp_ref_by,cost_center,enable_coin_enq,gent_clientid,cusName_edit,
								vs_enable,enable_coin_book,auto_debit,auto_debit_allow_app_pay,enable_digi_gold,show_video_shop,show_customer_order,
                                block_kyc_by, pan_required_by, pan_req_amt,show_kyc_optional,employee_allocation,notify_platform,notify_cred
						  From chit_settings
						  Where id_chit_settings=" . $id;
                    // print_r($sql);exit; 
                    return $this->db->query($sql)->row_array();
                } else {
                    $sql = "Select restrict_lastPayment_days,
					            schemeaccNo_displayFrmt,receiptNo_displayFrmt,chitCollectionEmpCount,
								id_chit_settings, 
								currency_symbol,
								currency_name,
								allow_join_multiple,allow_notification,regExistingReqOtp,
								allow_join_unpaid,	delete_unpaid,show_closed_list,enable_closing_otp,
								gst_setting,newSchjoinonline,allow_wallet,allow_savecard,
								scheme_wise_acc_no,rate_update,reg_existing, receipt,edit_addpay_page,branch_settings,schemeacc_no_set, receipt_no_set,is_ratenoti_sent,wallet_account_type,useWalletForChit,walletIntegration,schrefbenifit_secadd,cusplan_type,
								cusbenefitscrt_type,empplan_type,empbenefitscrt_type,has_lucky_draw,allow_referral,enableGoldrateDisc,goldDiscAmt,branchWiseLogin,allow_catlog,scheme_wise_receipt,
								wallet_balance_type,wallet_amt_per_points,wallet_points,enableSilver_rateDisc,silverDiscAmt,isOTPRegForPayment,isOTPReqToLogin,enable_dth,payOTP_exp,loginOTP_exp,req_otp_login,enable_dth,req_gift_issue_otp,req_prize_issue_otp,metal_wgt_decimal,metal_wgt_roundoff,
								enableGoldrateDisc_18k,goldDiscAmt_18k,is_branchwise_cus_reg,sch_limit,edit_custom_entry_date,getExisting_balance,is_branchwise_rate,branchwise_scheme,custom_entry_date,emp_ref_by,cost_center,enable_coin_enq,gent_clientid,cusName_edit,
								vs_enable,enable_coin_book,auto_debit,auto_debit_allow_app_pay,enable_digi_gold,show_video_shop,show_customer_order,notify_platform,notify_cred
						  From chit_settings";
                    return $this->db->query($sql)->result_array();
                }
                break;
            case 'insert':
                $status = $this->db->insert("chit_settings", $set_array);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'update':
                $this->db->where("id_chit_settings", $id);
                $status = $this->db->update("chit_settings", $set_array);
                	// print_r($this->db->last_query());exit;
                return array('status' => $status, 'updateID' => $id);
                break;
            case 'delete':
                $this->db->where("id_chit_settings", $id);
                $status = $this->db->delete("chit_settings");
                return array('status' => $status, 'DeleteID' => $id);
                break;
            default:
                return array(
                    'id_chit_settings' => NULL,
                    'currency_symbol' => NULL,
                    'currency_name' => NULL,
                    'allow_join_multiple' => 0,
                    'allow_join_unpaid' => 0,
                    'delete_unpaid' => 0,
                    'rate_update' => 0,
                    'reg_existing' => 0,
                    'receipt' => 0,
                    'gst_setting' => 0,
                    'enable_closing_otp' => 0,
                    'newSchjoinonline' => 0,
                    'allow_wallet' => 0,
                    'allow_savecard' => 0,
                    'allow_catlog' => 0,
                    'firstPayamt_payable' => 0,
                    'firstPayamt_as_payamt' => 0,
                    'get_amt_in_schjoin' => 0,
                    'schemeaccNo_displayFrmt' => 0,
                    'receiptNo_displayFrmt' => 0,
                    'restrict_lastPayment_days' => 0,
                );
                break;
        }
    }
    /*-- Coded by ARVK --*/
    function limitDB($type = "", $id = "", $set_array = "")
    {
        switch ($type) {
            case 'get':
                if ($id != NULL) {
                    $sql = "Select 
								id_limit, 
								limit_cust, 
								cust_max_count, limit_sch, 
								sch_max_count,
								limit_branch,
								branch_max_count,
								limit_sch_acc,
								sch_acc_max_count
						  From limit_settings
						  Where id_limit=" . $id;
                    if ($this->db->query($sql)->num_rows() > 0) {
                        return $this->db->query($sql)->row_array();
                    } else {
                        $data = array(
                            'id_limit' => NULL,
                            'limit_cust' => 0,
                            'cust_max_count' => 0,
                            'limit_sch' => 0,
                            'sch_max_count' => 0,
                            'limit_branch' => 0,
                            'branch_max_count' => 0,
                            'limit_sch_acc' => 0,
                            'sch_acc_max_count' => 0
                        );
                        $status = $this->limitDB('insert', '', $data);
                        return $data;
                    }
                } else {
                    $sql = "Select 
								id_limit, 
								limit_cust, 
								cust_max_count, limit_sch, 
								sch_max_count,
								limit_branch,
								branch_max_count,
								limit_sch_acc,
								sch_acc_max_count
						  From limit_settings";
                    return $this->db->query($sql)->result_array();
                }
                break;
            case 'insert':
                $status = $this->db->insert("limit_settings", $set_array);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'update':
                $this->db->where("id_limit", $id);
                $status = $this->db->update("limit_settings", $set_array);
                return array('status' => $status, 'updateID' => $id);
                break;
            default:
                return array(
                    'id_limit' => NULL,
                    'limit_cust' => 0,
                    'cust_max_count' => 0,
                    'limit_sch' => 0,
                    'sch_max_count' => 0,
                    'limit_branch' => 0,
                    'branch_max_count' => 0,
                    'limit_sch_acc' => 0,
                    'sch_acc_max_count' => 0
                );
                break;
        }
    }
    function discount_db($type = "", $id = "", $array_data = "")
    {
        switch ($type) {
            case 'get':
                if ($id != NULL) {
                    $sql = "Select 
								free_first_payment
						  From discount
						  Where id_discount=" . $id;
                    if ($this->db->query($sql)->num_rows() > 0) {
                        return $this->db->query($sql)->row_array();
                    } else {
                        $data = array(
                            'free_first_payment' => 0
                        );
                        $status = $this->discount_db('insert', '', $data);
                        return $data;
                    }
                } else {
                    $sql = "Select 
								id_discount, free_first_payment
						  From discount";
                    return $this->db->query($sql)->result_array();
                }
                break;
            case 'insert':
                $status = $this->db->insert("discount", $array_data);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'update':
                $this->db->where("id_discount", $id);
                $status = $this->db->update("discount", $array_data);
                return array('status' => $status, 'updateID' => $id);
                break;
            default:
                return array(
                    'free_first_payment' => 0
                );
                break;
        }
    }
    /*  function receipt_type()
     {
          $sql="SELECT receipt FROM chit_settings";
          return $this->db->query($sql)->row('receipt');
     } */
    function receipt_type()
    {
        $resultset = $this->db->query("SELECT c.receipt,c.receipt_no_set FROM chit_settings c;");
        if ($resultset->num_rows() > 0) {
            return array('status' => TRUE, 'receipt_no_set' => $resultset->row()->receipt_no_set, 'receipt' => $resultset->row()->receipt);
        } else {
            return array('status' => FALSE);
        }
    }
    /*-- / Coded by ARVK --*/
    function executeQry($sql)
    {
        return $this->db->query($sql);
    }
    function allow_autorate_update()
    {
        $sql = "SELECT rate_update,enableGoldrateDisc,goldDiscAmt,enableSilver_rateDisc,silverDiscAmt,enableGoldrateDisc_18k,goldDiscAmt_18k,is_branchwise_rate FROM chit_settings";
        return $this->db->query($sql)->row_array();
    }
    function database_backup($type = "", $id = "", $array = "")
    {
        switch ($type) {
            case 'get':
                if ($id != null) {
                    $sql = "SELECT d.id_dbbackup,d.backup_date,concat(e.firstname,' ',e.lastname) as employee,d.filename
								FROM dbbackup_log d
								LEFT JOIN employee e ON(d.id_employee=e.id_employee)
								WHERE d.id_dbbackup=" . $id;
                    return $this->db->query($sql)->row_array();
                } else {
                    $sql = "SELECT d.id_dbbackup,d.backup_date,concat(e.firstname,' ',e.lastname) as employee,d.filename
								FROM dbbackup_log d
								LEFT JOIN employee e ON(d.id_employee=e.id_employee)";
                    return $this->db->query($sql)->result_array();
                }
                break;
            case 'insert':
                $status = $this->db->insert("dbbackup_log", $array);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
        }
    }
    /*function gateway_settingsDB($type="",$id="",$array="")
    {
        switch($type)
         {
              case 'get_all':
                       $sql="SELECT
                                 id_gateway,pg_settings_id,
                                 `key`,
                                 salt,
                                api_url,m_code,param_1,
                                 if(type=0,'Demo','Real') as type,
                                 is_default
                            FROM gateway";
                           return $this->db->query($sql)->result_array();
                  break;
              case 'get_id':
                       $sql="SELECT
                                 id_gateway,pg_settings_id,
                                 `key`,
                                 salt,
                                api_url,m_code,param_1,
                                 if(type=0,'Demo','Real') as type,
                                 is_default
                            FROM gateway
                            WHERE id_gateway=".$id;
                           return $this->db->query($sql)->row_array();
                  break; 		
                  case 'get_default':
                       $sql="SELECT
                                 id_gateway,pg_settings_id,
                                 `key`,
                                 salt,
                                api_url,m_code,param_1,
                                 if(type=0,'Demo','Real') as type,
                                 is_default
                            FROM gateway
                            WHERE is_default=1";
                           return $this->db->query($sql)->result_array();
                  break;
              case 'update':
                      $this->db->where("id_gateway",$id);
                      $status = $this->db->update("gateway",$array);
                      return	array('status' => $status, 'updateID' => $id);     
                  break;
      }		    
    }*/
    function gateway_settingsDB($type = "", $id = "", $array = "")
    {
        /*param_1->key
         param_2->salt
         param_3->m_code
         param_4->iv
        */
        switch ($type) {
            case 'get_all':
                $sql = "SELECT
							      id_pg,id_branch,pg_name,pg_code,param_1,param_2,param_3,param_4,api_url,is_default,if(type=0,'Demo','Real') as type
							 FROM gateway";
                return $this->db->query($sql)->result_array();
                break;
            case 'get_id':
                $sql = "SELECT id_pg,id_branch,pg_name,pg_code,param_1,param_2,param_3,param_4,api_url,is_default,if(type=0,'Demo','Real') as type
							 FROM gateway
							 WHERE id_pg=" . $id;
                // print_r($sql);exit;
                return $this->db->query($sql)->row_array();
                break;
            case 'get_default':
                $sql = "SELECT
							      id_pg,id_branch,pg_name,pg_code,param_1,param_2,param_3,param_4,api_url,is_default,if(type=0,'Demo','Real') as type
							 FROM gateway
							 WHERE is_default=1";
                return $this->db->query($sql)->result_array();
                break;
            case 'update':
                $this->db->where("id_pg", $id);
                $status = $this->db->update("gateway", $array);
                return array('status' => $status, 'updateID' => $id);
                break;
        }
    }
    function sms_apiDB($type = "", $id = "", $data = "")
    {
        switch ($type) {
            case 'get':
                if ($id != null) {
                    $sql = "Select id_sms_api,sms_sender_id,sms_url from sms_api_settings Where id_sms_api=" . $id;
                    return $this->db->query($sql)->row_array();
                } else {
                    $sql = "Select id_sms_api,sms_sender_id,sms_url from sms_api_settings";
                    return $this->db->query($sql)->result_array();
                }
                break;
            case 'update':
                $this->db->where("id_sms_api", $id);
                $status = $this->db->update("sms_api_settings", $data);
                return array('status' => $status, 'updateID' => $id);
                break;
            default:
                return array(
                    'sms_sender_id' => NULL,
                    'sms_url' => NULL
                );
                break;
        }
    }
    //offers
    function ajax_get_offers()
    {
        $this->db->select('*');
        $weights = $this->db->get('offers');
        return $weights->result_array();
    }
    public function get_offers($id)
    {
        $this->db->select('*');
        $this->db->where('id_offer', $id);
        $weights = $this->db->get('offers');
        return $weights->row_array();
    }
    public function isPopupExist()
    {
        $this->db->select('count(*) as popups');
        $this->db->where('type', 2);
        $res = $this->db->get('offers');
        return $res->row('popups');
    }
    function offer_empty_record()
    {
        $data = array(
            'id_offer' => NULL,
            'name' => NULL,
            'offer_content' => NULL,
            'date_add' => NULL,
            'date_update' => NULL,
            'offer_img_path' => NULL,
            'type' => 0,
            'isPopupExist' => $this->isPopupExist(),
            'offer_notification' => 0
        );
        return $data;
    }
    function insert_offer($offer)
    {
        $insert_flag = 0;
        $insert_flag = $this->db->insert("offers", $offer);
        $offer_id = $this->db->insert_id();
        //print_r($this->db->last_query());exit;
        return ($insert_flag == 1 ? $offer_id : 0);
    }
    function update_offer($data, $id)
    {
        $status = 0;
        $this->db->where("id_offer", $id);
        $status = $this->db->update("offers", $data);
        //print_r($this->db->last_query());exit;
        return ($status == 1 ? $id : 0);
    }
    function delete_offer($id)
    {
        $this->db->where("id_offer", $id);
        $status = $this->db->delete("offers");
        return $status;
    }
    function getExpiredData()
    {
        $sql = "SELECT n.expiry_date,n.id_new_arrivals,n.active 
			FROM new_arrivals n 
				where n.expiry_date=DATE_FORMAT(CURDATE(), '%y-%m-%d')";
        return $this->db->query($sql)->result_array();
    }
    //new_arrivals
    function ajax_get_new_arrivals()
    {
        $this->db->select('*');
        $weights = $this->db->get('new_arrivals');
        return $weights->result_array();
    }
    public function get_new_arrivals($id)
    {
        $this->db->select('*');
        $this->db->where('id_new_arrivals', $id);
        $weights = $this->db->get('new_arrivals');
        return $weights->row_array();
    }
    function new_arrivals_empty_record()
    {
        $data = array(
            'id_new_arrivals' => NULL,
            'name' => NULL,
            'new_arrivals_content' => NULL,
            'date_add' => NULL,
            'date_update' => NULL,
            'price' => 0.00,
            'product_code' => NULL,
            'new_arrivals_img_path' => NULL,
            'product_description' => NULL,
            'new_type' => 0,
            'gift_type' => 0,
            //	'id_branch'				=>1,
            'show_rate' => 1,
            'expiry_date' => NULL,
            'send_notification' => 0
        );
        return $data;
    }
    function insert_new_arrivals($new_arrivals)
    {
        $insert_flag = 0;
        $insert_flag = $this->db->insert("new_arrivals", $new_arrivals);
        $new_arrivals_id = $this->db->insert_id();
        //print_r($this->db->last_query());exit;
        return ($insert_flag == 1 ? $new_arrivals_id : 0);
    }
    function update_new_arrivals($data, $id)
    {
        $status = 0;
        $this->db->where("id_new_arrivals", $id);
        $status = $this->db->update("new_arrivals", $data);
        return ($status == 1 ? $id : 0);
    }
    function delete_new_arrivals($id)
    {
        $this->db->where("id_new_arrivals", $id);
        $status = $this->db->delete("new_arrivals");
        return $status;
    }
    function ajax_get_cardbrand()
    {
        $wts = array();
        $this->datatables->select('id_card_brand,card_type,card_brand,short_code')->from('card_brand');
        echo $this->datatables->generate();
    }
    function get_card_brand($id)
    {
        $this->db->select('id_card_brand,card_brand,card_type,short_code');
        $this->db->where('id_card_brand', $id);
        $weights = $this->db->get('card_brand');
        return $weights->row_array();
    }
    function insert_card_brand($brand)
    {
        $status = $this->db->insert("card_brand", $brand);
        return $status;
    }
    function update_cardbrand($data, $id)
    {
        $this->db->where("id_card_brand", $id);
        $status = $this->db->update("card_brand", $data);
        return $status;
    }
    function delete_card_brand($id)
    {
        $this->db->where("id_card_brand", $id);
        $status = $this->db->delete("card_brand");
        return $status;
    }
    // branch add with image//HH
    function get_branche()
    {
        $logoPath = base_url() . "assets/img/branch/";
        $sql = $this->db->query("select id_branch,map_url,name,short_name,if(logo is null,null,concat('" . $logoPath . "','',logo)) as logo from branch where active=1");
        return $sql->result_array();
    }
    function ajax_get_branches()
    {
        $logoPath = base_url() . "assets/img/branch/";
        $wts = array();
        // $this->datatables->select('id_branch,name,logo,active,short_name')->from('branch');
        //echo $this->db->last_query();
        // echo $this->datatables->generate();
        $data = $this->db->query('SELECT id_branch,name,logo,active,short_name from branch');
        return $data->result_array();
    }
    function get_branches()
    {
        $this->db->select('id_branch,name,logo,short_name');
        $branch = $this->db->get('branch');
        return $branch->result_array();
    }
    public function update_branch_only($data, $id)
    {
        $edit_flag = 0;
        $this->db->where('id_branch', $id);
        $branch_info = $this->db->update("branch", $data);
        return $branch_info;
    }
    function update_branch($data, $id)
    {
        $this->db->where("id_branch", $id);
        $status = $this->db->update("branch", $data);
        //print_r($this->db->last_query());exit;
        return $status;
    }
    function get_branch_by_id($id)
    {
        $this->db->select('id_branch,logo,map_url,name,short_name,email,address1,address2,phone,pincode,id_country,id_city,id_state,id_village,cusromercare,mobile,otp_verif_mobileno,
	 metal_rate_type,show_to_all,partial_goldrate_diff,partial_silverrate_diff,is_ho,gst_number,google_review_url');
        $this->db->where('id_branch', $id);
        $branch = $this->db->get('branch');
        return $branch->row_array();
    }
    function insert_branch($branch_data)
    {
        $status = $this->db->insert("branch", $branch_data);
        return array('status' => $status, 'id_branch' => $this->db->insert_id());
        //print_r($this->db->last_query());exit;
        return $status;
    }
    // branch add
    //  metalrate branch wise
    public function insert_metalrate($branch_info, $table)
    {
        $status = $this->db->insert($table, $branch_info);
        return $status;
    }
    public function metal_rate_type($id)
    {
        $this->db->select('metal_rate_type');
        $this->db->where('id_branch', $id);
        $result = $this->db->get('branch');
        return $result->row_array();
    }
    public function metal_rates_list($id, $emp_id)
    {
        $sql = "SELECT m.id_metalrates, m.updatetime, m.goldrate_22ct, m.goldrate_24ct,
						m.silverrate_1gm, m.silverrate_1kg, if(m.id_employee=0,'MJDMA',concat(e.firstname,' ',e.lastname)) as employee,
						 br.status,if(m.id_employee=0,'1','0') as metal_rate_type
						FROM metal_rates m
						Left Join employee e on (m.id_employee=e.id_employee)
						Left Join branch_rate br on (m.id_metalrates= br.id_metalrate)
						left join branch b on (br.id_branch=b.id_branch)
						 where br.id_branch=" . $id . " and br.status=1 
						 " . ($emp_id == null ? ' and m.id_employee=' . $emp_id : 'and  b.metal_rate_type=0') . "
						 group by m.id_metalrates Order By m.id_metalrates Desc";
        $r = $this->db->query($sql);
        return $r->result_array();
    }
    function max_metalrate_list($id, $emp_id)
    {
        $sql = " select max(m.id_metalrates) as max_id 
						 FROM metal_rates m
						Left Join employee e on (m.id_employee=e.id_employee)
						Left Join branch_rate br on (m.id_metalrates= br.id_metalrate)
						left join branch b on (br.id_branch=b.id_branch)
						 where br.id_branch=" . $id . " and br.status=1 
						 " . ($emp_id == null ? ' and m.id_employee=' . $emp_id : 'and  b.metal_rate_type=0') . "
						 group by m.id_metalrates Order By m.id_metalrates Desc";
        return $this->db->query($sql)->row('max_id');
    }
    // metalrate branch wise
    function get_gstsettings()
    {
        $sql = $this->db->query("select * from chit_settings");
        return $sql->row_array();
        //$sql="select * from chit_settings"
    }
    function get_schdebit_settings()
    {
        $sql = $this->db->query("select * from scheme_debit_settings");
        return $sql->row_array();
        //$sql="select * from chit_settings"
    }
    // scheme account number generate
    function accno_generatorset()
    {
        $resultset = $this->db->query("SELECT c.schemeacc_no_set,gent_clientid,receipt_no_set FROM chit_settings c");
        if ($resultset->row()->schemeacc_no_set == 0) {
            return array('status' => TRUE, 'schemeacc_no_set' => $resultset->row()->schemeacc_no_set, 'gent_clientid' => $resultset->row()->gent_clientid, 'receipt_no_set' => $resultset->row()->receipt_no_set);
        } else {
            return array('status' => FALSE, 'schemeacc_no_set' => $resultset->row()->schemeacc_no_set, 'gent_clientid' => $resultset->row()->gent_clientid, 'receipt_no_set' => $resultset->row()->receipt_no_set);
        }
    }
    // scheme account number generate
    function promotion_crt_settings($type = "", $id = "", $data = "")
    {
        switch ($type) {
            case 'get':
                if ($id != null) {
                    $sql = "SELECT p.id_promotion_api, p.promotion_sender_id, p.promotion_url, p.credit_promotion, p.debit_promotion FROM promotion_api_settings p Where p.id_promotion_api=" . $id;
                    return $this->db->query($sql)->row_array();
                } else {
                    $sql = "SELECT p.id_promotion_api, p.promotion_sender_id, p.promotion_url, p.credit_promotion, p.debit_promotion FROM promotion_api_settings p";
                    return $this->db->query($sql)->result_array();
                }
                break;
            case 'update':
                $this->db->where("id_promotion_api", $id);
                $status = $this->db->update("promotion_api_settings", $data);
                return array('status' => $status, 'updateID' => $id);
                break;
            default:
                return array(
                    'promotion_sender_id' => NULL,
                    'promotion_url' => NULL,
                    'credit_promotion' => 0,
                    'debit_promotion' => 0
                );
                break;
        }
    }
    //  otp_crt_settings
    function otp_crt_settings($type = "", $id = "", $data = "")
    {
        switch ($type) {
            case 'get':
                if ($id != null) {
                    $sql = "SELECT s.id_sms_api, s.sms_sender_id, s.sms_url, s.credit_sms, s.debit_sms FROM sms_api_settings s Where s.id_sms_api=" . $id;
                    return $this->db->query($sql)->row_array();
                } else {
                    $sql = "SELECT s.id_sms_api, s.sms_sender_id, s.sms_url, s.credit_sms, s.debit_sms FROM sms_api_settings s";
                    return $this->db->query($sql)->result_array();
                }
                break;
            case 'update':
                $this->db->where("id_sms_api", $id);
                $status = $this->db->update("sms_api_settings", $data);
                return array('status' => $status, 'updateID' => $id);
                break;
            default:
                return array(
                    'credit_sms' => 0,
                    'debit_sms' => 0
                );
                break;
        }
    }
    function get_branchcompany($id)
    {
        $sql = "SELECT b.gst_number,b.id_branch, b.name, b.active, b.short_name, b.metal_rate_type,b.address1,cs.currency_symbol, b.address2, b.phone, b.mobile, b.pincode,c.name as country,s.name as state, ct.name as city FROM branch b
									 LEFT JOIN country c on c.id_country= b.id_country
									 LEFT JOIN state s on s.id_state= b.id_state
									 LEFT JOIN city ct on ct.id_city= b.id_city
									 JOIN chit_settings cs
									 where b.id_branch=" . $id . "";
        // print_r($sql);exit;
        return $result = $this->db->query($sql)->row_array('id');
    }
    //payment gateway//
    function insert_payment_gateway($gateway)
    {
        $status = $this->db->insert("gateway", $gateway);
        $id_payment_gateway = $this->db->insert_id();
        return $id_payment_gateway;
    }
    function getWalletData()
    {
        $data = '';
        $sql = "SELECT mobile,available_points from inter_wallet_account";
        $result = $this->db->query($sql)->result_array();
        foreach ($result as $row) {
            $data = $data . "" . $row['mobile'] . ',' . $row['available_points'] . "\n ";
        }
        return $data;
    }
    function ajax_get_paymentgateway($id_branch)
    {
        $id_branch = $this->session->userdata('id_branch');
        $sql = "SELECT g.active,g.id_pg,g.pg_name,g.savecard,g.debitcard,b.name as branch_name,g.netbanking,g.description,g.is_default,g.pg_icon,if(g.type=0,'Demo','pro')as type FROM gateway g 
        	left join branch b on b.id_branch=g.id_branch
         " . ($id_branch != '' ? "where  g.id_branch=" . $id_branch . "" : '') . "";
        //print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    function get_paymentgateway($id)
    {
        $this->db->select('id_pg,pg_name,description,pg_icon,pg_code,active,savecard,creditcard,debitcard,netbanking,id_metal');
        /*  if($id_branch!='')
          {
          $this->db->where('id_branch',$id_branch);
          }*/
        $this->db->where('id_pg', $id);
        $weights = $this->db->get('gateway');
        //print_r($this->db->last_query());exit;
        return $weights->row_array();
    }
    function delete_payment_gateway($id)
    {
        $this->db->where("id_pg", $id);
        $status = $this->db->delete("gateway");
        return $status;
    }
    function update_paymentgateway($data, $id)
    {
        $status = 0;
        $this->db->where("id_pg", $id);
        /* if($id_branch!='')
         {
             $this->db->where("id_branch",$id_branch); 
         } */
        $status = $this->db->update("gateway", $data);
        //print_r($this->db->last_query());exit;
        return ($status == 1 ? $id : 0);
    }
    //payment gateway//
    // Get metals marked as show_chit=1 for commodity selection in gateway
    function get_chit_metals()
    {
        $this->db->select('id_metal, metal');
        $this->db->where('show_chit', 1);
        $this->db->where('metal_status', 1);
        return $this->db->get('metal')->result_array();
    }
    function get_interWallet_trans_by_Filter($from_date, $to_date, $searchTerm, $filterBy, $id_branch = "")
    {
        if ($searchTerm != '' && $filterBy != '' && $from_date != '' && $to_date != '' && $id_branch != '') {
            $where = ($filterBy = "mobile" ? "iwt.mobile = " . $searchTerm :
                ($filterBy = "billno" ? "iwt.bill_no = '" . $searchTerm . "'" :
                    ($filterBy = "pincode" ? "a..pincode = '" . $searchTerm . "'" :
                        ($filterBy = "name" ? "firstname = '" . $searchTerm . "' or lastname = '" . $searchTerm . "'" : ''))));
            $sql = "SELECT id_inter_waltransdetail,iwt.mobile,if(iwt.trans_type = 1, if(iwt.actual_redeemed >0,'Credit + Debit','Credit' ),'Debit') as trans_type,iwt.bill_no,
                    wdet.category_code,wdet.amount as bill_amount,wdet.trans_points as credit,if(iwt.actual_redeemed >0,iwt.actual_redeemed,'-' )as debit,DATE_FORMAT(wdet.date_add,'%d-%m-%Y %H:%i:%s') as trans_date,
                    concat(firstname,' ',lastname) as name,firstname,lastname,a.pincode,cat.name as cat_name,b.name as branch
                    FROM `inter_wallet_trans` iwt 
                    left join inter_wallet_trans_detail wdet on wdet.id_inter_wallet_trans = iwt.id_inter_wallet_trans
                    left join wallet_category cat on cat.code = wdet.category_code
                    left join customer c on c.mobile = iwt.mobile
                    left join address a on a.id_customer = c.id_customer
                    left join branch b on b.id_branch = iwt.id_branch
            Where " . $where . " and iwt.id_branch=" . $id_branch . " and (date(wdet.date_add) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')";
            return $data = $this->db->query($sql)->result_array();
        } else if ($searchTerm != '' && $filterBy != '') {
            $where = ($filterBy == "mobile" ? "iwt.mobile = " . $searchTerm :
                ($filterBy == "billno" ? "iwt.bill_no = '" . $searchTerm . "'" :
                    ($filterBy == "pincode" ? "a..pincode = '" . $searchTerm . "'" :
                        ($filterBy == "name" ? "firstname = '" . $searchTerm . "' or lastname = '" . $searchTerm . "'" : ''))));
            $sql = "SELECT id_inter_waltransdetail,iwt.mobile,if(iwt.trans_type = 1, if(iwt.actual_redeemed >0,'Credit + Debit','Credit' ),'Debit') as trans_type,iwt.bill_no,
                    wdet.category_code,wdet.amount as bill_amount,wdet.trans_points as credit,if(iwt.actual_redeemed >0,iwt.actual_redeemed,'-' )as debit,DATE_FORMAT(wdet.date_add,'%d-%m-%Y %H:%i:%s') as trans_date,
                    concat(firstname,' ',lastname) as name,firstname,lastname,a.pincode,cat.name as cat_name,DATE_FORMAT(iwt.entry_date,'%d-%m-%Y') as bill_date,b.name as branch
                    FROM `inter_wallet_trans` iwt 
                    left join inter_wallet_trans_detail wdet on wdet.id_inter_wallet_trans = iwt.id_inter_wallet_trans
                    left join wallet_category cat on cat.code = wdet.category_code
                    left join customer c on c.mobile = iwt.mobile
                    left join address a on a.id_customer = c.id_customer
                    left join branch b on b.id_branch = iwt.id_branch
            Where " . $where . " " . ($id_branch != '' ? " and iwt.id_branch=" . $id_branch : '') . "";
            //  echo $sql;
            return $data = $this->db->query($sql)->result_array();
        } else if ($id_branch != '' && $from_date == '' && $to_date == '') {
            $sql = "SELECT id_inter_waltransdetail,iwt.mobile,if(iwt.trans_type = 1, if(iwt.actual_redeemed >0,'Credit + Debit','Credit' ),'Debit') as trans_type,iwt.bill_no,
                    wdet.category_code,wdet.amount as bill_amount,wdet.trans_points as credit,if(iwt.actual_redeemed >0,iwt.actual_redeemed,'-' )as debit,DATE_FORMAT(wdet.date_add,'%d-%m-%Y %H:%i:%s') as trans_date,
                    concat(firstname,' ',lastname) as name,a.pincode,cat.name as cat_name,DATE_FORMAT(iwt.entry_date,'%d-%m-%Y') as bill_date,b.name as branch
                    FROM `inter_wallet_trans` iwt 
                    left join inter_wallet_trans_detail wdet on wdet.id_inter_wallet_trans = iwt.id_inter_wallet_trans
                    left join wallet_category cat on cat.code = wdet.category_code
                    left join customer c on c.mobile = iwt.mobile
                    left join address a on a.id_customer = c.id_customer
                    left join branch b on b.id_branch = iwt.id_branch
            Where  date(wdet.date_add) = curdate() " . ($id_branch != '' ? "and iwt.id_branch=" . $id_branch : '') . " ";
            return $data = $this->db->query($sql)->result_array();
        } else {
            $sql = "SELECT id_inter_waltransdetail,iwt.mobile,if(iwt.trans_type = 1, if(iwt.actual_redeemed >0,'Credit + Debit','Credit' ),'Debit') as trans_type,iwt.bill_no,
                    wdet.category_code,wdet.amount as bill_amount,wdet.trans_points as credit,if(iwt.actual_redeemed >0,iwt.actual_redeemed,'-' )as debit,DATE_FORMAT(wdet.date_add,'%d-%m-%Y %H:%i:%s') as trans_date,
                    concat(firstname,' ',lastname) as name,a.pincode,cat.name as cat_name,DATE_FORMAT(iwt.entry_date,'%d-%m-%Y') as bill_date,b.name as branch
                    FROM `inter_wallet_trans` iwt 
                    left join inter_wallet_trans_detail wdet on wdet.id_inter_wallet_trans = iwt.id_inter_wallet_trans
                    left join wallet_category cat on cat.code = wdet.category_code
                    left join customer c on c.mobile = iwt.mobile
                    left join address a on a.id_customer = c.id_customer
                    left join branch b on b.id_branch = iwt.id_branch
            Where  (date(wdet.date_add) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "') " . ($id_branch != '' ? "and iwt.id_branch=" . $id_branch : '') . " ";
            return $data = $this->db->query($sql)->result_array();
        }
    }
    function get_interWallet_trans($id_branch = "")
    {
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $id_branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $sql = "SELECT id_inter_waltransdetail,iwt.mobile,if(iwt.trans_type = 1, if(iwt.actual_redeemed >0,'Credit + Debit','Credit' ),'Debit') as trans_type,iwt.bill_no,
                    wdet.category_code,wdet.amount as bill_amount,wdet.trans_points as credit,iwt.actual_redeemed as debit,DATE_FORMAT(wdet.date_add,'%d-%m-%Y %H:%i:%s') as trans_date,
                    concat(firstname,' ',lastname) as name,a.pincode,cat.name  as cat_name,DATE_FORMAT(iwt.entry_date,'%d-%m-%Y') as bill_date,b.name as branch
                    FROM `inter_wallet_trans` iwt 
                    left join inter_wallet_trans_detail wdet on wdet.id_inter_wallet_trans = iwt.id_inter_wallet_trans
                    left join wallet_category cat on cat.code = wdet.category_code
                    left join customer c on c.mobile = iwt.mobile
                    left join branch b on b.id_branch=iwt.id_branch
                    left join address a on a.id_customer = c.id_customer
                     Where date(wdet.date_add) = curdate() " . ($uid != 1 ? ($branchWiseLogin == 1 ? ($id_branch != '' ? " and iwt.id_branch=" . $id_branch . " or b.show_to_all=1" : '') : '') : '') . " " . ($id_branch != '' ? "and iwt.id_branch=" . $id_branch : '') . " ";
        return $data = $this->db->query($sql)->result_array();
    }
    function walSMS_settings($type = "", $id = "", $data = "")
    {
        switch ($type) {
            case 'get':
                if ($id != null) {
                    $sql = "SELECT * FROM inter_wallet_smssettings Where id_wal_smsSettings=" . $id;
                    //print_r($this->db->query($sql)->row_array());exit;
                    return $this->db->query($sql)->row_array();
                } else {
                    $sql = "SELECT * FROM inter_wallet_smsSettings where active=1";
                    return $this->db->query($sql)->result_array();
                }
                break;
            case 'update':
                $this->db->where("id_wal_smsSettings", $id);
                $status = $this->db->update("inter_wallet_smsSettings", $data);
                return array('status' => $status, 'updateID' => $id);
                break;
            default:
                return array(
                    'sent_sms' => 0,
                    'credited_sms' => 0,
                    'active' => 0,
                );
                break;
        }
    }
    // temp report
    function get_interWallet_trans_temp($id_branch = "", $date)
    {
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $uid = $this->session->userdata('uid');
        if ($id_branch == '') {
            $sql = "SELECT *
                    FROM `inter_wallet_trans_tmp` iwt  
                     Where date(date_add) = '" . $date . "'  order by id_inter_waltrans_tmp desc";
        } else {
            $sql = "SELECT *
                    FROM `inter_wallet_trans_tmp` iwt  
                     Where date(date_add) = '" . $date . "' and id_branch=" . $id_branch . " order by id_inter_waltrans_tmp desc";
        }
        return $data = $this->db->query($sql)->result_array();
    }
    function ajax_gateway_settings($type, $pg_code, $id_branch)
    {
        $sql = "SELECT
			id_pg,id_branch,pg_name,pg_code,param_1,param_2,param_3,param_4,api_url,is_default,if(type=0,'Demo','Real') as type
							 FROM gateway
							 WHERE     type=" . $type . " " . ($id_branch != '' ? " and  id_branch=" . $id_branch : '') . " and pg_code=" . $pg_code . "";
        // print_r($sql);exit;
        return $this->db->query($sql)->row_array();
    }
    function update_gateway($data, $id_pg, $id_branch)
    {
        if ($id_pg != '') {
            if ($id_branch != '') {
                $this->db->where("id_branch", $id_branch);
            }
            $this->db->where("id_pg", $id_pg);
            $status = $this->db->update("gateway", $data);
        } else {
            $status = $this->db->update("gateway", $data);
        }
        //	print_r($this->db->last_query());exit;
        return array('status' => $status);
    }
    function get_branches_for_rate()
    {
        $sql = "select * from branch b 
			where b.metal_rate_type=1 or b.metal_rate_type=2
			order by b.metal_rate_type";
        // print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    //Modified for customer listing page optimization... 28-07 #AB
    function ajax_village_list($id_village)
    {
        $from_date = $_GET['from_date'];
        $to_date = $_GET['to_date'];
        $sql = "";
        if(!empty($from_date) && !empty($to_date)){
            $sql = "select v.* from village v 
                    LEFT JOIN customer c ON c.id_village = v.id_village
                    WHERE date_format(c.date_add,'%Y-%m-%d') BETWEEN DATE_FORMAT(STR_TO_DATE('".$from_date."', '%d-%m-%Y'), '%Y-%m-%d') AND DATE_FORMAT(STR_TO_DATE('".$to_date."', '%d-%m-%Y'), '%Y-%m-%d') ";
        }else{
            $sql = "select v.* from village v Where 1 ";
        }
        if ($id_village != '') {
            $sql .= $id_village != '' ? "and  v.id_village=" . $id_village . "" : '' ;
        } 
        $sql .= "GROUP BY v.id_village";
        return $this->db->query($sql)->result_array();
    }
    function village_settingDB($type = "", $id = "", $data = "")
    {
        switch ($type) {
            case 'get':
                if ($id != NULL) {
                    $sql = "SELECT *  FROM village v " . ($id != null ? 'Where v.id_village=' . $id : '');
                    $r = $this->db->query($sql);
                    return $r->row_array(); //for single row
                }
                break;
            case 'insert': //insert operation
                $status = $this->db->insert(self::VILLAGE_TABLE, $data);
                return array('status' => $status, 'ID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'update': //update operation
                $this->db->where("id_village", $id);
                $status = $this->db->update(self::VILLAGE_TABLE, $data);
                return array('status' => $status, 'ID' => $id);
                break;
            case 'delete':
                $this->db->where("id_village", $id);
                $status = $this->db->delete(self::VILLAGE_TABLE);
                return array('status' => $status, 'deleteID' => $id);
                break;
            default: //empty record
                $vilage = array(
                    'village_name' => NULL,
                    'id_village' => NULL,
                    'post_office' => NULL,
                    'pincode' => NULL,
                    'taluk' => NULL,
                );
                return $vilage;
        }
    }
    function canSendNoti($id)
    {
        $sql = $this->db->query("select noti_sub,allow_notification from notification n join chit_settings cs where id_notification =" . $id);
        $res = $sql->row_array();
        if ($res['allow_notification'] == 1 && $res['noti_sub'] == 1) {
            return true;
        } else {
            return false;
        }
    }
    function branchname_list()
    {
        $id_branch = $this->session->userdata('id_branch');
        if ($this->session->userdata('id_branch') != '') {
            $branch = $this->db->query("SELECT b.name,b.id_branch FROM branch b Where id_branch=" . $id_branch . "");
        } else {
            $branch = $this->db->query("SELECT b.name,b.id_branch FROM branch b");
        }
        return $branch->result_array();
    }
    function get_rate_diff($id_branch)
    {
        $sql = $this->db->query("select partial_goldrate_diff as gold,partial_silverrate_diff as silver from branch where id_branch =" . $id_branch);
        $res = $sql->row_array();
        return $res;
    }
    function checkBalance($type)
    {
        $sql = $this->db->query("select msg91_authkey from chit_settings where id_chit_settings=1");
        $authkey = $sql->row('msg91_authkey');
        if ($authkey != NULL) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://control.msg91.com/api/balance.php?authkey=" . $authkey . "&type=" . $type,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                //return "cURL Error #:" . $err;
            } else {
                return $response;
            }
        }
    }
    function get_reports()
    {
        $sql = "SELECT mobile FROM inter_wallet_account";
        return $this->db->query($sql)->result_array();
    }
    function getBranchId($warehouse)
    {
        $sql = "SELECT id_branch FROM branch where expo_warehouse ='" . $warehouse . "' or  warehouse='" . $warehouse . "'";
        $res = $this->db->query($sql);
        if ($res->num_rows > 0) {
            return $res->row('id_branch');
        } else {
            return 0;
        }
    }
    function getMetalRateId($id_branch)
    {
        $sql = "SELECT id_metalrate FROM branch_rate where status=1 and id_branch=" . $id_branch;
        $res = $this->db->query($sql);
        if ($res->num_rows > 0) {
            return $res->row('id_metalrate');
        } else {
            return 0;
        }
    }
    function update_silverRate($data, $id_metalrate)
    {
        $this->db->where("id_metalrates", $id_metalrate);
        $status = $this->db->update("metal_rates", $data);
        return array('status' => $status);
    }
    function getRef_nos($data)
    {
        if (isset($data['g_ref_no']) && isset($data['s_ref_no']) && isset($data['p_ref_no'])) {
            $where = "g_ref_no=" . $data['g_ref_no'] . " or s_ref_no=" . $data['s_ref_no'] . " or p_ref_no=" . $data['p_ref_no'];
        } else {
            if (isset($data['g_ref_no'])) {
                $where = "g_ref_no=" . $data['g_ref_no'];
            }
            if (isset($data['s_ref_no'])) {
                if (sizeof($where) > 0) {
                    $where = $where . " or " . "s_ref_no=" . $data['s_ref_no'];
                } else {
                    $where = "s_ref_no=" . $data['s_ref_no'];
                }
            }
            if (isset($data['p_ref_no'])) {
                if (sizeof($where) > 0) {
                    $where = $where . " or " . "p_ref_no=" . $data['p_ref_no'];
                } else {
                    $where = "p_ref_no=" . $data['p_ref_no'];
                }
            }
        }
        $sql = $this->db->query("select id_metalrates,g_ref_no,s_ref_no,p_ref_no from metal_rates where " . $where);
        // echo $this->db->last_query();exit;
        return $sql->result_array();
    }
    //get branch edit on metal rates GG
    function get_branch_edit($id)
    {
        $sql = $this->db->query("select br.id_branch from metal_rates mr 
		left join branch_rate br on (br.id_metalrate = mr.id_metalrates) " . ($id != null ? 'Where id_metalrates=' . $id : '') . "");
        $id_branch = array_map(function ($value) {
            return $value['id_branch'];
        }, $sql->result_array());
        return $id_branch;
    }
    //get branch edit on metal rates GG
    // Terms For App from admin side add//HH
    function terms_and_conditions($type = "", $id_general = "", $general_array = "")
    {
        switch ($type) {
            case 'get':
                $userType = $this->session->userdata('profile');
                if ($id_general != NULL) {
                    $sql = "Select * From general Where id_general=" . $id_general;
                    // print_r($sql);exit;
                    $menu = $this->db->query($sql);
                    return $menu->row_array();
                } else {
                    $sql = "Select * From general";
                    $menu = $this->db->query($sql);
                    return $menu->result_array();
                }
                break;
            case 'insert':
                $status = $this->db->insert("general", $general_array);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'update':
                $this->db->where("id_general", $id_general);
                $status = $this->db->update("general", $general_array);
                return array('status' => $status, 'updateID' => $id_general);
                break;
            case 'delete':
                $this->db->where("id_general", $id_general);
                $status = $this->db->delete("general");
                return array('status' => $status, 'DeleteID' => $id);
                break;
        }
    }
    // Terms For App from admin side add//
    // branch wise raye showed in admin //HH
    function metal_rates_branch()
    {
        $id_branch = $this->session->userdata('id_branch');
        $data = $this->get_settings();
        if ($data['is_branchwise_rate'] == 1 && $id_branch != '') {
            $sql = "select * from metal_rates m
	   		left join branch_rate br on m.id_metalrates=br.id_metalrate 
	   		where br.id_branch=" . $id_branch . " order by  br.id_metalrate desc limit 1";
            // print_r($sql);exit;
        } else if ($data['is_branchwise_rate'] == 1) {
            $sql = "select * from metal_rates 
			left join branch_rate br on br.id_metalrate=metal_rates.id_metalrates 
			where br.status=1";
            //print_r($sql);exit;
        } else {
            $sql = "select * from metal_rates 
			left join branch_rate br on br.id_metalrate=metal_rates.id_metalrates order by id_metalrates desc limit 1";
            //print_r($sql);exit;
        }
        $result = $this->db->query($sql);
        // print_r($sql);exit;
        return $result->row_array();
    }
    function get_settings()
    {
        $sql = "select * from chit_settings cs";
        $result = $this->db->query($sql);
        return $result->row_array();
    }
    // branch wise raye showed in admin //
    function import_off_excel($path = "", $filename = "", $isHeading = "")
    {
        try {
            $objPHPExcel = PHPExcel_IOFactory::load($path . $filename);
            //get only the Cell Collection
            $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
            $highestColumm = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
            $totalcolumns = PHPExcel_Cell::columnIndexFromString($highestColumm);
            $totalrows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
            // echo "columns ".($totalcolumns - 1);
            //extract to a PHP readable array format
            foreach ($cell_collection as $cell) {
                $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
                //for filtering heading
                if ($isHeading == 1) {
                    if ($row == 1) {
                        $header[$row][$column] = $data_value;
                    } else {
                        $arr_data[$row][$column] = $data_value;
                    }
                } else {
                    $arr_data[$row][$column] = $data_value;
                }
            }
            return $arr_data;
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($filename, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
    }
    function get_financial_data()
    {
        $sql = "SELECT fin_id,fin_year_code,fin_status from ret_financial_year where fin_status=1";
        $res = $this->db->query($sql);
        if ($res->num_rows > 0) {
            return $res->row_array();
        } else {
            return 0;
        }
    }
    // Header data
    function getHeaderData()
    {
        $fin_year = $this->db->query("SELECT fin_id,fin_year_code,fin_year_name,fin_status from ret_financial_year where fin_status=1");
        $result['fin_year'] = $fin_year->row_array();
        $result['access'] = $this->get_access('admin/dashboard');
        $id_branch = $this->session->userdata('id_branch');
        $emp_sett = $this->db->query("SELECT allow_day_close from employee_settings where id_employee=" . $this->session->userdata('uid'));
        if ($emp_sett->num_rows > 0) {
            $result['show_dayClose'] = $emp_sett->row('allow_day_close');
        } else {
            $result['show_dayClose'] = 0;
        }
        $day_close = $this->db->query("SELECT max(date_format(entry_date,'%d-%m-%Y')) as day_close from ret_day_closing " . ($id_branch != '' ? " WHERE id_branch='" . $id_branch . "'" : '') . "  limit 1");
        if ($day_close->num_rows == 1) {
            $result['day_close_date'] = $day_close->row('day_close');
        }
        $pro_sett = $this->db->query("SELECT show_pending_download from profile where id_profile=" . $this->session->userdata('profile'));
        if ($pro_sett->num_rows > 0) {
            $result['show_pending_download'] = $pro_sett->row('show_pending_download');
        } else {
            $result['show_pending_download'] = 0;
        }
        $customer_feedback = $this->db->query("SELECT * FROM `cust_enquiry`");
        $result['customer_feedback'] = $customer_feedback->num_rows;
        return $result;
    }
    function getBTDetails()
    {
        $id_branch = $this->session->userdata('id_branch');
        $sql = $this->db->query("SELECT IFNULL(sum(b.pieces),0) as tot_pcs FROM ret_branch_transfer b WHERE b.status=2 " . ($id_branch != '' && $id_branch > 0 ? " and b.transfer_to_branch=" . $id_branch . "" : '') . " ");
        return $sql->row_array();
    }
    function get_modules($code)
    {
        $sql = $this->db->query("SELECT * FROM modules WHERE m_code='" . $code . "'");
        return $sql->row_array();
    }
    function retail_settingsDB($type = "", $name = "", $set_array = "")
    {
        switch ($type) {
            case 'get':
                $sql = "Select * From ret_settings";
                //print_r($sql);exit;
                return $this->db->query($sql)->result_array();
                break;
            case 'update':
                $this->db->where("name", $name);
                $status = $this->db->update("ret_settings", $set_array);
                return array('status' => $status, 'updateID' => $name);
                break;
            case 'delete':
                $this->db->where("name", $name);
                $status = $this->db->delete("ret_settings");
                return array('status' => $status, 'DeleteID' => $name);
                break;
            default:
                return array(
                    'id_ret_settings' => NULL,
                    'name' => NULL,
                    'value' => NULL,
                    'description' => NULL,
                    'weight_per' => 0
                );
                break;
        }
    }
    function get_ret_settings($settings)
    {
        $data = $this->db->query("SELECT value FROM ret_settings where name='" . $settings . "'");
        return $data->row()->value;
    }
    function getBranchDayClosingData($id_branch)
    {
        $sql = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing 
        	" . ($id_branch != '' ? " where id_branch=" . $id_branch . "" : '') . "");
        return $sql->row_array();
    }
    function getAllBranchDCData()
    {
        $sql = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing");
        return $sql->result_array();
    }
    function getNotificationDetails()
    {
        $return_data = '';
        $id_profile = $this->session->userdata('profile');
        $uid = $this->session->userdata('uid');
        
        $where_clause = "n.id_noticeboard is not null and noticeboard_status=1";
        
        // For restricted profiles, filter by employee ID
        if (!in_array($id_profile, array(1, 2, 3))) {
            if (!empty($uid)) {
                $where_clause .= " and v.id_employee=" . $this->db->escape($uid);
            } else {
                // If user ID is missing for restricted profile, return empty to prevent SQL error
                return '';
            }
        }
        
        $sql = "SELECT n.id_noticeboard,n.noticeboard_text,DATE_FORMAT(n.created_on,'%d-%m-%Y') as date_add,e.firstname as emp_name,if(n.noticeboard_status=1,'Active','Inactive') as status,n.noticeboard_status,n.visible_to,
		p.profile_name,date_format(n.reminder_on,'%d-%m-%Y') as reminder_on
		FROM ret_noticeboard n
		LEFT JOIN employee e on e.id_employee=n.created_by
		LEFT JOIN profile p on p.id_profile=n.visible_to
		LEFT JOIN ret_noticeboard_view_details v on v.id_noticeboard=n.id_noticeboard
		where " . $where_clause . "
		group by n.id_noticeboard";
        
        $query = $this->db->query($sql);
        
        if ($query && $query->num_rows() > 0) {
            $data = $query->result_array();
            foreach ($data as $items) {
                $validity_date = strtotime($items['reminder_on']);
                $current_time = strtotime(date("Y-m-d"));
                if ($current_time >= $validity_date) {
                    $return_data .= '<span>' . ' * ' . $items['noticeboard_text'] . '</span>';
                }
            }
        }
        return $return_data;
    }
    function get_AllProfile()
    {
        $sql = $this->db->query("SELECT * FROM profile WHERE id_profile!=1");
        return $sql->result_array();
    }
    function get_service_by_code($serv_code)
    {
        $this->db->select('id_services,serv_name,serv_email,serv_sms,serv_whatsapp,dlt_te_id');
        $this->db->where('serv_code', $serv_code);
        $service = $this->db->get('services');
        return $service->row_array();
    }
    function getCompanyDetails($id_branch)
    {
        if ($id_branch == '') {
            $sql = $this->db->query("Select  c.id_company,c.company_name,c.gst_number,c.short_code,c.pincode,c.mobile,c.whatsapp_no,c.phone,c.email,c.website,c.address1,c.address2,c.id_country,c.id_state,c.id_city,ct.name as city,s.name as state,cy.name as country,cs.currency_symbol,cs.currency_name,cs.mob_code,cs.mob_no_len,c.mail_server,c.mail_password,c.send_through,c.mobile1,c.phone1,c.smtp_user,c.smtp_pass,c.smtp_host,c.server_type,cs.login_branch,
			s.state_code
			from company c
			join chit_settings cs
			left join country cy on (c.id_country=cy.id_country)
			left join state s on (c.id_state=s.id_state)
			left join city ct on (c.id_city=ct.id_city)");
        } else {
            $sql = $this->db->query("select b.name,b.address1,b.address2,c.company_name,b.gst_number,
				cy.name as country,ct.name as city,s.name as state,b.pincode,s.id_state,s.state_code,cy.id_country,
				b.phone,c.email
				from branch b
				join company c
				left join country cy on (b.id_country=cy.id_country)
				left join state s on (b.id_state=s.id_state)
				left join city ct on (b.id_city=ct.id_city)
				where b.id_branch=" . $id_branch . "");
        }
        $result = $sql->row_array();
        return $result;
    }
    function get_company_settings()
    {
        $company_settings = $this->db->query("SELECT company_settings FROM chit_settings");
        $is_multi_company = $company_settings->row()->company_settings;
        return $is_multi_company;
    }
    //gift master db operations	
    function giftDB($type = "", $id = "", $gift_array = "")
    {
        switch ($type) {
            case 'get':
                $from_date = $this->input->post('from_date');
                $to_date = $this->input->post('to_date');
                $id_gift = $this->input->post('gift_id');
                //print_r($from_date);exit;
                $branch_settings = $this->session->userdata('branch_settings');
                $branch_wise_login = $this->session->userdata('branchWiseLogin');
                if ($this->session->userdata('id_branch') != '') {
                    $id_branch = $this->session->userdata('id_branch');
                } else {
                    $id_branch = $this->input->post('id_branch');
                }
                if ($id != NULL) {
                    $sql = "Select g.id_gift,g.gift_name,g.gift_type,g.metal,g.net_weight,IFNULL(g.quantity,0) as quantity,g.id_branch,g.status,IFNULL(g.out_stock,0) as out_stock,IFNULL(b.name,'-') as name From gifts g left join branch b on b.id_branch=g.id_branch Where g.id_gift=" . $id;
                    return $this->db->query($sql)->row_array();
                } else {
                    $sql = "Select g.id_gift,g.gift_name,g.gift_type,g.metal,g.net_weight,IFNULL(g.quantity,0) as quantity,g.status,IFNULL(g.out_stock,0) as out_stock,IFNULL(b.name,'-') as name From gifts g left join branch b on b.id_branch=g.id_branch where date(g.date_add) between '" . $from_date . "' and '" . $to_date . "'" . ($id_gift != '' ? " and g.id_gift=" . $id_gift . "" : '') . "";
                    if ($id_branch != '' && $id_branch > 0) {
                        $sql = $sql . " and g.id_branch=" . $id_branch;
                    }
                    //print_r($sql);exit;
                    return $this->db->query($sql)->result_array();
                }
                break;
            case 'insert':
                $status = $this->db->insert("gifts", $gift_array);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'update':
                $this->db->where("id_gift", $id);
                $status = $this->db->update("gifts", $gift_array);
                return array('status' => $status, 'updateID' => $id);
                break;
            case 'delete':
                $this->db->where("id_gift", $id);
                $status = $this->db->delete("gifts");
                return array('status' => $status, 'DeleteID' => $id);
                break;
            default: //empty record
                return array(
                    'id_gift' => NULL,
                    'gift_name' => NULL,
                    'status' => 1
                );
        }
    }
    function get_all_gifts()
    {
        $branch = $this->session->userdata('id_branch');
        $id_branch = $this->input->post('id_branch');
        $sql = "SELECT id_gift,gift_name FROM gifts";
        if ($branch != '' && $branch > 0) {
            $sql = $sql . " where id_branch=" . $branch;
        } else if ($id_branch != '' && $id_branch != null && $id_branch > 0) {
            $sql = $sql . " where id_branch=" . $id_branch;
        }
        //print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    function update_gift_status($status, $id)
    {
        $status = $this->db->query("UPDATE gifts SET status=" . $status . " where id_gift=" . $id . "");
        return $status;
    }
    function ajax_get_profession()
    {
        $this->db->select('id_profession,name');
        $designs = $this->db->get('profession');
        return $designs->result_array();
    }
	// coded by jothika on 10-7-2025 [prevent insert and update duplicate entries]
    function insert_profession($profession)
    {
		$prof_name = $profession['name'];
		$query = $this->db->where('name', $prof_name)->get('profession');
		if ($query->num_rows() == 0) {
        $status = $this->db->insert("profession", $profession);
        return $status;
		} else {
			return false;
		}
    }
    function get_profession($id)
    {
        $this->db->select('id_profession,name');
        $this->db->where('id_profession', $id);
        $designs = $this->db->get('profession');
        return $designs->row_array();
    }
	//    coded by jothika on 11-7-2025 [prevent updating duplicate entries]
    function update_profession($data, $id)
    {
		$prof_name = $data['name'];
		$current = $this->db->where('id_profession', $id)->get('profession')->row();
		if ($current && $current->name == $prof_name) {
			$this->db->where("id_profession", $id);
			return $this->db->update("profession", $data);
		}
		$query = $this->db->where('name', $prof_name)
			->where('id_profession !=', $id)
			->get('profession');
		if ($query->num_rows() == 0) {
        $this->db->where("id_profession", $id);
        $status = $this->db->update("profession", $data);
        return $status;
		} else {
			return false;
		}
    }
    function delete_profession($id)
    {
        $this->db->where("id_profession", $id);
        $status = $this->db->delete("profession");
        return $status;
    }
    /* created by durga 30/12/2022 -starts here 
  new function for crud in configuration table */
    function configDB($type = "", $id = "", $set_array = "")
    {
        switch ($type) {
            case 'get':
                if ($id != NULL) {
                    $sql = "select * from configuration 
								  Where id_config=" . $id;
                    return $this->db->query($sql)->row_array();
                } else {
                    $sql = "select * from configuration ";
                    return $this->db->query($sql)->result_array();
                }
            case 'update':
                $this->db->where("id_config", $id);
                $status = $this->db->update("configuration", $set_array);
                //print_r($this->db->last_query());exit;
                return array('status' => $status, 'updateID' => $id);
                break;
            case 'insert':
                $status = $this->db->insert("configuration", $set_array);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'delete':
                $this->db->where("id_config", $id);
                $status = $this->db->delete("configuration");
                return array('status' => $status, 'DeleteID' => $id);
                break;
        }
    }
    /* created by durga 30/12/2022 -ends  here 
    new function for data manipulation in configuration table */
    //Added by Durga 12.05.2023  starts here 
    //version master db operations	
    function versionDB($type = "", $id = "", $version_array = "", $from_date = "", $to_date = "")
    {
        switch ($type) {
            case 'get':
                if ($id != NULL) {
                    $sql = "Select * From version Where id_version=" . $id;
                    return $this->db->query($sql)->row_array();
                } else if ($from_date != NULL && $to_date != NULL) {
                    $sql = "Select id_version,version_no,description,date(date_add) as date,client From version where date(date_add) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "'";
                    //(date(wdet.date_add) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))
                    //print_r($sql);
                    return $this->db->query($sql)->result_array();
                } else {
                    $sql = "Select * From version";
                    return $this->db->query($sql)->result_array();
                }
                break;
            case 'insert':
                $status = $this->db->insert("version", $version_array);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'update':
                $this->db->where("id_version", $id);
                $status = $this->db->update("version", $version_array);
                return array('status' => $status, 'updateID' => $id);
                break;
            case 'delete':
                $this->db->where("id_version", $id);
                $status = $this->db->delete("version");
                return array('status' => $status, 'deleteID' => $id);
                break;
            default: //empty record
                return array(
                    'id_version' => NULL,
                    'version_no' => NULL,
                    'description' => NULL,
                    'client' => NULL,
                    'date_add' => NULL
                );
        }
    }
    function get_version_data($version_no)
    {
        $sql = "Select * From version Where version_no='" . $version_no . "'";
        //print_r($sql);exit; 
        return $this->db->query($sql)->row_array();
    }
    function get_last_version()
    {
        $sql = "select * from version order by id_version desc limit 1";
        //print_r($sql);exit; 
        return $this->db->query($sql)->row_array();
    }
    //Added by Durga 12.05.2023  ends here 
    function menuPermission($id = "", $id_menu = "")
    {
        $sql = "Select a.id_menu,a.id_profile,a.view,a.add,a.edit,a.delete 
		From access AS a Where a.id_profile=" . $id . " And a.id_menu=" . $id_menu;
        return $this->db->query($sql)->row_array();
    }
    function get_quick_link()
    {
        $sql = "SELECT rq.id_menu,mu.label,mu.link from ret_quick_link rq 
		Left join menu as mu on mu.id_menu = rq.id_menu
		Where rq.status= 1
		order by mu.id_menu asc ";
        return $this->db->query($sql)->result_array();
        // print_r($sql);exit; 
    }
    public function deleteData($id_field, $id_value, $table)
    {
        $this->db->where($id_field, $id_value);
        $status = $this->db->delete($table);
        return $status;
    }
    function getPreviousDateStatuslog($bill_id)
    {
        $sql = $this->db->query("SELECT rb.bill_id, rb.bill_date from ret_billing rb
		" . ($bill_id != '' ? " where rb.bill_id=" . $bill_id . "" : '') . "");
        return $sql->row_array();
    }
    function update_country($data, $id)
    {
        $this->db->where('id_country', $id);
        $status = $this->db->update('country', $data);
        return $status;
    }
    function update_notification_status($status, $id)
    {
        $status = $this->db->query("UPDATE notification SET active=" . $status . " where id_notification=" . $id . "");
        return $status;
    }
    function getnotification()
    {
        $sql = "select * from  notification";
        return $this->db->query($sql)->result_array();
    }
    function get_imgpath($id, $type)
    {
        $sql = "select id_offer,offer_img_path from  offers where type=" . $type . " and id_offer=" . $id . "";
        return $this->db->query($sql)->result_array();
    }
    function get_newArival_imgpath($id)
    {
        $sql = "select id_new_arrivals,new_arrivals_img_path from  new_arrivals where id_new_arrivals=" . $id . "";
        return $this->db->query($sql)->result_array();
    }
	function getBranchDetails()
	{
		$id_branch = $this->input->post('id_branch');
		$branch = $this->db->query("SELECT b.name as Branch_name,b.id_branch, b.id_country, b.id_state, b.id_city, b.address1, b.address2, b.pincode,b.id_village, b.gst_number FROM branch b Where id_branch=" . $id_branch . "");
		return $branch->result_array();
	}
    function get_languages_known()
    {
        $sql = "select * from languages";
        $languages = $this->db->query($sql)->result_array();
        return json_encode($languages);
    }
    public function ledgerDB($type = "", $id = "", $data = "")
    {
        switch ($type) {
            case 'get':
                if ($id != NULL) {
                    $ledger = $this->db->where('id_ledger', $id)->get('ledger_master')->row_array();
                    if ($ledger) {
                        $ledger['mappings'] = $this->db->where('id_ledger', $id)->get('ledger_mapping')->result_array();
                    }
                    return $ledger;
                } else {
                    $this->db->select('lm.*, GROUP_CONCAT(DISTINCT b.bank_name SEPARATOR ", ") as bank_names, GROUP_CONCAT(DISTINCT d.device_name SEPARATOR ", ") as device_names', FALSE);
                    $this->db->from('ledger_master lm');
                    $this->db->join('ledger_mapping lp', 'lm.id_ledger = lp.id_ledger AND lp.type = "BANK"', 'left');
                    $this->db->join('bank b', 'lp.reference_id = b.id_bank', 'left');
                    $this->db->join('ledger_mapping lpd', 'lm.id_ledger = lpd.id_ledger AND lpd.type = "PAYMODE"', 'left');
                    $this->db->join('ret_bill_pay_device d', 'lpd.reference_id = d.id_device', 'left');
                    $this->db->group_by('lm.id_ledger');
                    $this->db->order_by('lm.id_ledger', 'ASC');
                    return $this->db->get()->result_array();
                }
                break;
            case 'insert':
                $this->db->trans_start();
                $master_data = array(
                    'ledger_name' => $data['ledger_name'],
                    'opening_balance' => $data['opening_balance'],
                    'opening_date' => $data['opening_date'],
                    'min_balance' => $data['min_balance'],
                    'status' => 1
                );
                $this->db->insert('ledger_master', $master_data);
                $id_ledger = $this->db->insert_id();
                if (!empty($data['mappings'])) {
                    foreach ($data['mappings'] as $mapping) {
                        $mapping_data = array(
                            'id_ledger' => $id_ledger,
                            'type' => $mapping['type'],
                            'reference_id' => $mapping['reference_id']
                        );
                        $this->db->insert('ledger_mapping', $mapping_data);
                    }
                }
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    return false;
                }
                return array('status' => true, 'insertID' => $id_ledger);
                break;
            case 'update':
                $this->db->trans_start();
                $master_data = array(
                    'ledger_name' => $data['ledger_name'],
                    'opening_balance' => $data['opening_balance'],
                    'opening_date' => $data['opening_date'],
                    'min_balance' => $data['min_balance']
                );
                $this->db->where('id_ledger', $id)->update('ledger_master', $master_data);
                // Remove old mappings
                $this->db->where('id_ledger', $id)->delete('ledger_mapping');
                // Insert new mappings
                if (!empty($data['mappings'])) {
                    foreach ($data['mappings'] as $mapping) {
                        $mapping_data = array(
                            'id_ledger' => $id,
                            'type' => $mapping['type'],
                            'reference_id' => $mapping['reference_id']
                        );
                        $this->db->insert('ledger_mapping', $mapping_data);
                    }
                }
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    return false;
                }
                return array('status' => true, 'updateID' => $id);
                break;
            case 'delete':
                $this->db->trans_start();
                $this->db->where('id_ledger', $id)->delete('ledger_mapping');
                $this->db->where('id_ledger', $id)->delete('ledger_master');
                $this->db->trans_complete();
                return $this->db->trans_status();
                break;
        }
    }
     public function getAvailablePosDevices($ledger_id = 0)
    {
        // 1. Get ALL devices first
        $all_devices = $this->db->get('ret_bill_pay_device')->result_array();
        // 2. Get device IDs used by OTHER ledgers
        $this->db->select('reference_id');
        $this->db->where('type', 'PAYMODE');
        if ($ledger_id > 0) {
            $this->db->where('id_ledger !=', $ledger_id);
        }
        $query = $this->db->get('ledger_mapping');
        $used_ids = array();
        foreach ($query->result_array() as $row) {
            $used_ids[] = $row['reference_id'];
        }
        // 3. Filter devices in PHP
        // Include device if it's NOT in the used_ids list
        $available_devices = array();
        foreach ($all_devices as $device) {
            if (!in_array($device['id_device'], $used_ids)) {
                $available_devices[] = $device;
            }
        }
        return $available_devices;
    }


    function kyc_master(){
        $sql = $this->db->query("SELECT id_mas_kyc, name FROM kyc_master WHERE status = 1");
        $data = $sql->result_array();
        return $data;
    }

    function get_kyc_settings(){
        $sql = $this->db->get('kyc_settings');
        if ($sql->num_rows() > 0) {
            return $sql->row_array();
        }
        return array(
            'kyc_required' => 0,
            'kyc_mode' => 0,
            'kyc_verification_type' => 0,
            'kyc_allow_type' => 0,
        );
    }

    function get_kyc_rules(){
        $sql = $this->db->get_where('kyc_rules', array('status' => 1));
        return $sql->result_array();
    }

    function save_kyc_settings($settings_data, $rules_data, $uid){
        $this->db->trans_begin();

        // Upsert kyc_settings
        $existing = $this->db->get('kyc_settings');
        if ($existing->num_rows() > 0) {
            $row = $existing->row();
            $this->db->where('id_kyc_settings', $row->id_kyc_settings);
            $this->db->update('kyc_settings', $settings_data);
        } else {
            $this->db->insert('kyc_settings', $settings_data);
        }

        // Delete old rules
        $this->db->where('status', 1);
        $this->db->delete('kyc_rules');

        // Insert new rules
        if (is_array($rules_data) && count($rules_data) > 0) {
            foreach ($rules_data as $rule) {
                $rule['status'] = 1;
                $rule['created_by'] = $uid;
                $this->db->insert('kyc_rules', $rule);
            }
        }

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return true;
        } else {
            $this->db->trans_rollback();
            return false;
        }
    }

    // ─── Feature Flags ────────────────────────────────────────────────────────

    /**
     * Returns the feature_flags JSON array from chit_settings.
     * Always returns a PHP array (may be empty).
     */
    function get_feature_flags($id_chit_settings = 1)
    {
        $sql = "SELECT feature_flags FROM chit_settings WHERE id_chit_settings = " . (int)$id_chit_settings . " LIMIT 1";
        $row = $this->db->query($sql)->row_array();
        if (empty($row) || $row['feature_flags'] === null) {
            return array();
        }
        $flags = json_decode($row['feature_flags'], true);
        return is_array($flags) ? $flags : array();
    }

    /**
     * Replaces the entire feature_flags JSON column with the provided array.
     */
    function save_feature_flags($flags_array, $id_chit_settings = 1)
    {
        $json = json_encode(array_values($flags_array));
        $this->db->where('id_chit_settings', (int)$id_chit_settings);
        return $this->db->update('chit_settings', array('feature_flags' => $json));
    }
	// ========================================================================
	// Customer Feedback — Ported from AMS
	// ========================================================================

	function get_cusid($mobile)
	{
		$sql = $this->db->query("SELECT id_customer FROM customer WHERE mobile='" . $mobile . "'");
		if ($sql->num_rows() > 0) {
			return $sql->row('id_customer');
		} else {
			return 0;
		}
	}

	function getAvailableCustomers($SearchTxt)
	{
		$data = $this->db->query("SELECT c.id_customer as value, concat(c.mobile) as label,c.mobile, id_branch,
			a.id_state as cus_state,a.id_country as cus_country,c.firstname,c.email,c.date_of_birth,c.date_of_wed,a.pincode,a.address1,a.id_city as cus_city
			FROM customer c
			LEFT JOIN village v on v.id_village=c.id_village
			LEFT JOIN address a on a.id_customer=c.id_customer
			WHERE (username like '%" . $SearchTxt . "%' OR c.mobile like '%" . $SearchTxt . "%' OR firstname like '%" . $SearchTxt . "%') ");
		return $data->result_array();
	}

	function getFeedbackHeader($type="")
	{
		if($type=='all'){
			$data = $this->db->query("select * from feedback_header");
		}else{
			$data = $this->db->query("select * from feedback_header where status=1");
		}
		return $data->result_array();
	}

	function get_feedback_header($id)
	{
		$data = $this->db->query("select * from feedback_header where feedback_header_id=".$id );
		return $data->result_array();
	}
}
