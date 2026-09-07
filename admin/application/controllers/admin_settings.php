<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin_settings extends CI_Controller
{
    const VIEW_FOLDER = 'settings/';
    const MAS_VIEW = 'master/';
    const SET_MODEL = "admin_settings_model";
    const SCH_MODEL = "scheme_model";
    const CUS_MODEL = "customer_model";
    const ACC_MODEL = "account_model";
    const PAY_MODEL = "payment_model";
    const CUS_IMG_PATH = 'assets/img/customer/';
    const CUS_FOLDER = 'assets/img/customer';
    const ADM_MODEL = 'chitadmin_model';
    const LOG_MODEL = "log_model";
    const MODEL = "admin_usersms_model";
    const SETT_MOD = "admin_settings_model";
    public function __construct()
    {
        parent::__construct();
        ini_set('date.timezone', 'Asia/Calcutta');
        if (!$this->session->userdata('is_logged')) {
            redirect('admin/login');
        }
        $this->id_employee = $this->session->userdata('uid');
        $this->branch_settings = $this->session->userdata('branch_settings');
        $this->branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $this->usertype = $this->session->userdata('filerbybranch');
        $this->load->library('excel');
        $this->load->model('admin_usersms_model');
        $this->load->model('admin_settings_model');
        $this->load->model('customer_model');
        $this->load->model('scheme_model');
        $this->load->model('account_model');
        $this->load->model('chitadmin_model');
        $this->load->model('payment_model');
        $this->load->library('form_validation');
        $this->load->model(self::SETT_MOD);
        $this->load->model(self::LOG_MODEL);
        $this->id_log = $this->session->userdata('id_log');
    }
    public function index()
    {
        $data['main_content'] = self::VIEW_FOLDER . 'company';
        $this->load->view('layout/template', $data);
    }
    //for backend menu
    public function menu($type, $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case 'List':
                $data['menus'] = $this->$model->menuDB('get', ($id != NULL ? $id : ''));
                $data['main_content'] = self::VIEW_FOLDER . "menu/list";
                $this->load->view('layout/template', $data);
                break;
            case 'View':
                if ($id != NULL) {
                    $data['menu'] = $this->$model->menuDB("get", $id);
                } else {
                    $data['menu'] = $this->$model->menuDB();
                }
                $data['main_content'] = self::VIEW_FOLDER . "menu/form";
                $this->load->view('layout/template', $data);
                break;
            case 'Save': //insert 
                $menu_item = $this->input->post('menu');
                $status = $this->$model->menuDB("insert", "", $menu_item);
                if ($status) {
                    $profile = $this->$model->get_AllProfile();
                    $id_menu = array('id_profile' => 1, 'id_menu' => $status['insertID'], 'view' => 1, 'add' => 1, 'edit' => 1, 'delete' => 1);
                    $add_access = $this->$model->PermissionDB("insert", "", "", $id_menu);
                    foreach ($profile as $pro) {
                        $id_menu = array('id_profile' => $pro['id_profile'], 'id_menu' => $status['insertID'], 'view' => 0, 'add' => 0, 'edit' => 0, 'delete' => 0);
                        $add_access = $this->$model->PermissionDB("insert", "", "", $id_menu);
                    }
                    $this->session->set_flashdata('chit_alert', array('message' => 'Menu added successfully', 'class' => 'success', 'title' => 'Menu Item'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Menu Item'));
                }
                redirect('settings/menu/list');
                break;
            case 'Update':
                $menu_item = $this->input->post('menu');
                $data = $menu_item;
                $data['active'] = (isset($menu_item['active']) ? $menu_item['active'] : 0);
                $status = $this->$model->menuDB("update", $id, $data);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Menu item updated successfully', 'class' => 'success', 'title' => 'Menu Item'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Menu Item'));
                }
                redirect('settings/menu/list');
                break;
            case 'Delete':
                $status = $this->$model->menuDB("delete", $id);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Menu item deleted successfully', 'class' => 'success', 'title' => 'Menu Item'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Menu Item'));
                }
                redirect('settings/menu/list');
                break;
            default:
                $items = $this->$model->menuDB('get', ($id != NULL ? $id : ''));
                $menus = array(
                    'draw' => 0,
                    'recordsTotal' => count($items),
                    'recordsFiltered' => count($items),
                    'data' => $items
                );
                echo json_encode($menus);
        }
    }
    //for profile	
    public function profile($type, $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case 'List': //showing list
                $data['profile'] = $this->$model->profileDB('get', ($id != NULL ? $id : ''));
                $data['main_content'] = self::VIEW_FOLDER . "profile/list";
                $this->load->view('layout/template', $data);
                break;
            case 'View': //showing form
                // print_r($id);exit;
                if ($id != NULL) {
                    $data['profile'] = $this->$model->profileDB("get", $id);
                } else {
                    $data['profile'] = $this->$model->profileDB();
                }
                //  print_r($data['profile']['bill_disc_approval_type']);exit;
                $data['main_content'] = self::VIEW_FOLDER . "profile/form";
                $this->load->view('layout/template', $data);
                break;
            case 'Save': //insert 
                $profile = $this->input->post('profile');
                $updData = array(
                    'profile_name' => $profile['profile_name'],
                    'allow_acc_closing' => $profile['allow_acc_closing'],
                    'req_otplogin' => $profile['req_otplogin'],
                    'show_pending_download' => $profile['show_pending_download'],
                    'show_cart' => $profile['show_cart'],
                    'allow_bill_cancel' => $profile['allow_bill_cancel'],
                    'allow_order_cancel' => $profile['allow_order_cancel'],
                    'allow_lot_cancel' => $profile['allow_lot_cancel'],
                    'bill_cancel_otp' => $profile['bill_cancel_otp'],
                    'credit_sales_otp_req' => $profile['credit_sales_otp_req'],
                    'vendor_approval_otp_req' => $profile['vendor_approval_otp_req'],
                    'stock_issue_otp_req' => $profile['stock_issue_otp_req'],
                    'allow_branch_transfer_cancel' => $profile['allow_branch_transfer_cancel'],
                    'allow_other_issue' => $profile['allow_other_issue'],
                    'device_wise_login' => $profile['device_wise_login'],
                    'allow_bill_type' => $profile['allow_bill_type'],
                    'allow_stock_type' => $profile['allow_stock_type'],
                    'order_delivery_otp' => $profile['order_delievery_otp'],
                    'previous_bill_cancel' => $profile['previous_bill_cancel'],
                    'tag_transfer' => (isset($profile['tag_transfer']) ? $profile['tag_transfer'] : 0),
                    'non_tag_transfer' => (isset($profile['non_tag_transfer']) ? $profile['non_tag_transfer'] : 0),
                    'purchase_item_transfer' => (isset($profile['purchase_item_transfer']) ? $profile['purchase_item_transfer'] : 0),
                    'packaging_item_transfer' => (isset($profile['packaging_item_transfer']) ? $profile['packaging_item_transfer'] : 0),
                    'allow_mc_edit' => $profile['allow_mc_edit'],
                    'allow_va_edit' => $profile['allow_va_edit'],
                    'est_purity_edit' => $profile['est_purity_edit'],
                    'tag_details' => (isset($profile['tag_details']) ? $profile['tag_details'] : 0),
                    'purchase_details' => (isset($profile['purchase_details']) ? $profile['purchase_details'] : 0),
                    'stone_details' => (isset($profile['stone_details']) ? $profile['stone_details'] : 0),
                    'estimation' => (isset($profile['estimation']) ? $profile['estimation'] : 0),
                    'branch_transfer_details' => (isset($profile['branch_transfer_details']) ? $profile['branch_transfer_details'] : 0),
                    'section_transfer_details' => (isset($profile['section_transfer_details']) ? $profile['section_transfer_details'] : 0),
                    'scan_details' => (isset($profile['scan_details']) ? $profile['scan_details'] : 0),
                    'stock_issue_details' => (isset($profile['stock_issue_details']) ? $profile['stock_issue_details'] : 0),
                    'est_tag' => (isset($profile['est_tag']) ? $profile['est_tag'] : 0),
                    'est_non_tag' => (isset($profile['est_non_tag']) ? $profile['est_non_tag'] : 0),
                    'est_home_bill' => (isset($profile['est_home_bill']) ? $profile['est_home_bill'] : 0),
                    'est_old_metal' => (isset($profile['est_old_metal']) ? $profile['est_old_metal'] : 0),
                    'order_cancel_otp' => $profile['order_cancel_otp_req'],
                    'counter_change_otp' => $profile['counter_change_otp'],
                    'order_unlink_otp' => $profile['order_unlink_otp'],
                    'credit_collection_disc_otp' => $profile['credit_collection_disc_otp'],
                    'bill_disc_approval_type' => $profile['bill_disc_approval_type'],
                    'credit_sales_approval_type' => $profile['credit_sales_approval_type'],
                    'BT_otp_approval_type' => $profile['BT_otp_approval_type'],
                    'pre_date_oi' => $profile['pre_date_oi'],
                    // Metal rate back date entry esakki-20250729
                    'metalrate_edit'                 => $profile['metalrate_edit'],
                    'metal_rate_datelimit'                 => $profile['metal_rate_datelimit'],
                    'wedding_wastage_slab' => $profile['wed_wastage_slab'],
                    'allow_supplier_bill_approval' => $profile['allow_supplier_bill_approval'],
                    'allow_previous_rcpt_date' => $profile['allow_previous_rcpt_date'],
                    'allow_ir_cancel' => (isset($profile['allow_ir_cancel']) ? $profile['allow_ir_cancel'] : 0),
                    'previous_ir_cancel' => (isset($profile['previous_ir_cancel']) ? $profile['previous_ir_cancel'] : 0),
                );
                $status = $this->$model->insertData($updData, 'profile');
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Profile added successfully', 'class' => 'success', 'title' => 'Profile'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Profile'));
                }
                redirect('settings/profile/list');
                break;
            case 'Update':
                $profile = $this->input->post('profile');
                $updData = array(
                    'profile_name' => $profile['profile_name'],
                    'allow_acc_closing' => $profile['allow_acc_closing'],
                    'req_otplogin' => $profile['req_otplogin'],
                    'show_pending_download' => $profile['show_pending_download'],
                    'show_cart' => $profile['show_cart'],
                    'allow_bill_cancel' => $profile['allow_bill_cancel'],
                    'allow_order_cancel' => $profile['allow_order_cancel'],
                    'allow_lot_cancel' => $profile['allow_lot_cancel'],
                    'bill_cancel_otp' => $profile['bill_cancel_otp'],
                    'credit_sales_otp_req' => $profile['credit_sales_otp_req'],
                    'allow_branch_transfer_cancel' => $profile['allow_branch_transfer_cancel'],
                    'allow_other_issue' => $profile['allow_other_issue'],
                    'device_wise_login' => $profile['device_wise_login'],
                    'vendor_approval_otp_req' => $profile['vendor_approval_otp_req'],
                    'stock_issue_otp_req' => $profile['stock_issue_otp_req'],
                    'allow_bill_type' => $profile['allow_bill_type'],
                    'allow_stock_type' => $profile['allow_stock_type'],
                    'previous_bill_cancel' => $profile['previous_bill_cancel'],
                    'tag_transfer' => (isset($profile['tag_transfer']) ? $profile['tag_transfer'] : 0),
                    'non_tag_transfer' => (isset($profile['non_tag_transfer']) ? $profile['non_tag_transfer'] : 0),
                    'purchase_item_transfer' => (isset($profile['purchase_item_transfer']) ? $profile['purchase_item_transfer'] : 0),
                    'packaging_item_transfer' => (isset($profile['packaging_item_transfer']) ? $profile['packaging_item_transfer'] : 0),
                    'allow_mc_edit' => $profile['allow_mc_edit'],
                    'allow_va_edit' => $profile['allow_va_edit'],
                    'est_purity_edit' => $profile['est_purity_edit'],
                    'order_delivery_otp' => $profile['order_delievery_otp'],
                    'tag_details' => (isset($profile['tag_details']) ? $profile['tag_details'] : 0),
                    'purchase_details' => (isset($profile['purchase_details']) ? $profile['purchase_details'] : 0),
                    'stone_details' => (isset($profile['stone_details']) ? $profile['stone_details'] : 0),
                    'estimation' => (isset($profile['estimation']) ? $profile['estimation'] : 0),
                    'branch_transfer_details' => (isset($profile['branch_transfer_details']) ? $profile['branch_transfer_details'] : 0),
                    'section_transfer_details' => (isset($profile['section_transfer_details']) ? $profile['section_transfer_details'] : 0),
                    'scan_details' => (isset($profile['scan_details']) ? $profile['scan_details'] : 0),
                    'stock_issue_details' => (isset($profile['stock_issue_details']) ? $profile['stock_issue_details'] : 0),
                    'est_tag' => (isset($profile['est_tag']) ? $profile['est_tag'] : 0),
                    'est_non_tag' => (isset($profile['est_non_tag']) ? $profile['est_non_tag'] : 0),
                    'est_home_bill' => (isset($profile['est_home_bill']) ? $profile['est_home_bill'] : 0),
                    'est_old_metal' => (isset($profile['est_old_metal']) ? $profile['est_old_metal'] : 0),
                    'order_cancel_otp' => $profile['order_cancel_otp_req'],
                    'counter_change_otp' => $profile['counter_change_otp'],
                    'order_unlink_otp' => $profile['order_unlink_otp'],
                    'credit_collection_disc_otp' => $profile['credit_collection_disc_otp'],
                    'bill_disc_approval_type' => $profile['bill_disc_approval_type'],
                    'credit_sales_approval_type' => $profile['credit_sales_approval_type'],
                    'BT_otp_approval_type' => $profile['BT_otp_approval_type'],
                    'pre_date_oi' => $profile['pre_date_oi'],
                    // Metal rate back date entry esakki-20250729
                    'metalrate_edit'                 => $profile['metalrate_edit'],
                    'metal_rate_datelimit'                 => $profile['metal_rate_datelimit'],
                    'wedding_wastage_slab' => $profile['wed_wastage_slab'],
                    'allow_supplier_bill_approval' => $profile['allow_supplier_bill_approval'],
                    'allow_previous_rcpt_date' => $profile['allow_previous_rcpt_date'],
                    'allow_ir_cancel' => (isset($profile['allow_ir_cancel']) ? $profile['allow_ir_cancel'] : 0),
                    'previous_ir_cancel' => (isset($profile['previous_ir_cancel']) ? $profile['previous_ir_cancel'] : 0),
                );
                $status = $this->$model->updateData($updData, 'id_profile', $id, 'profile');
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Profile updated successfully', 'class' => 'success', 'title' => 'Profile'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Profile'));
                }
                redirect('settings/profile/list');
                break;
            case 'Delete':
                $status = $this->$model->profileDB("delete", $id);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Profile deleted successfully', 'class' => 'success', 'title' => 'Profile'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Profile'));
                }
                redirect('settings/profile/list');
                break;
            default:
                $items = $this->$model->profileDB('get', ($id != NULL ? $id : ''));
                $access = $this->$model->get_access('settings/rate/list');
                $profile = array(
                    'draw' => 0,
                    'recordsTotal' => count($items),
                    'recordsFiltered' => count($items),
                    'profile' => $items,
                    'access' => $access
                );
                echo json_encode($profile);
        }
    }
    //for user permission
    public function permission($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case 'List': //to pass json data to list
                $items = array();
                $menus = $this->$model->PermissionDB('get', ($id != NULL ? $id : ''), 1);
                if ($menus == 0) {
                    $menus = $this->$model->PermissionDB('empty', ($id != NULL ? $id : ''), 1);
                    foreach ($menus as $menu) {
                        if ($menu['submenus'] > 0) {
                            $items[] = $menu;
                            $submenus = $this->$model->PermissionDB('empty', $id, $menu['id_menu']);
                            if (!empty($submenus)) {
                                foreach ($submenus as $submenu) {
                                    $items[] = $submenu;
                                }
                            }
                        } else {
                            $items[] = $menu;
                        }
                    }
                } else {
                    foreach ($menus as $menu) {
                        if ($menu['submenus'] > 0) {
                            $items[] = $menu;
                            $submenus = $this->$model->PermissionDB('get', $id, $menu['id_menu']);
                            if (!empty($submenus)) {
                                foreach ($submenus as $submenu) {
                                    $items[] = $submenu;
                                }
                            }
                        } else {
                            $items[] = $menu;
                        }
                    }
                }
                $dashbaord_items = array();
                $menus = $this->$model->DashboardPermissionDB('get', ($id != NULL ? $id : ''), 1);
                if ($menus == 0) {
                    $menus = $this->$model->DashboardPermissionDB('empty', ($id != NULL ? $id : ''), 1);
                    foreach ($menus as $menu) {
                        if ($menu['submenus'] > 0) {
                            $dashbaord_items[] = $menu;
                            $submenus = $this->$model->DashboardPermissionDB('empty', $id, $menu['id_menu']);
                            if (!empty($submenus)) {
                                foreach ($submenus as $submenu) {
                                    $dashbaord_items[] = $submenu;
                                }
                            }
                        } else {
                            $dashbaord_items[] = $menu;
                        }
                    }
                } else {
                    foreach ($menus as $menu) {
                        if ($menu['submenus'] > 0) {
                            $dashbaord_items[] = $menu;
                            $submenus = $this->$model->DashboardPermissionDB('get', $id, $menu['id_menu']);
                            if (!empty($submenus)) {
                                foreach ($submenus as $submenu) {
                                    $dashbaord_items[] = $submenu;
                                }
                            }
                        } else {
                            $menu['submenus'] = ($menu['parent'] == 1 ? ($menu['submenus'] == 0 ? 1 : $menu['submenus']) : 0);
                            $dashbaord_items[] = $menu;
                        }
                    }
                }
                $data['menu'] = $items;
                $data['dashboard_menu'] = $dashbaord_items;
                echo json_encode($data);
                break;
            case 'View': //to show view 
                $data['main_content'] = self::VIEW_FOLDER . "menu/permission";
                $this->load->view('layout/template', $data);
                break;
            case 'Save':
                $acc_items = (array) json_decode($this->input->post("access_data"));
                foreach ($acc_items as $item) {
                    $exists = $this->$model->PermissionDB("exist", $item->id_profile, $item->id_menu, "");
                    if (!$exists) {
                        $add_access = $this->$model->PermissionDB("insert", "", "", $item);
                    } else {
                        $upd_access = $this->$model->PermissionDB("update", $item->id_profile, $item->id_menu, $item);
                    }
                }
                echo "Permission updated successfully..";
                break;
            case 'Dashboardsave':
                $acc_items = (array) json_decode($this->input->post("access_data"));
                foreach ($acc_items as $item) {
                    $exists = $this->$model->DashboardPermissionDB("exist", $item->id_profile, $item->id_menu, "");
                    if (!$exists) {
                        $add_access = $this->$model->DashboardPermissionDB("insert", "", "", $item);
                    } else {
                        $upd_access = $this->$model->DashboardPermissionDB("update", $item->id_profile, $item->id_menu, $item);
                    }
                }
                echo "Permission updated successfully..";
                break;
        }
    }
    public function valid_str($str)
    {
        if (trim($str) === '') {
            // $this->form_validation->set_message('valid_bank_name', 'The Bank Name field cannot contain only spaces.');
            return FALSE;
        }
        return TRUE;
    }
    //Bank Master
    public function bank($type, $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case 'List': //showing list
                $data['bank'] = $this->$model->bankDB('get', ($id != NULL ? $id : ''));
                $data['main_content'] = self::MAS_VIEW . "bank/list";
                $this->load->view('layout/template', $data);
                break;
            case 'View': //showing form
                if ($id != NULL) {
                    $data['bank'] = $this->$model->bankDB("get", $id);
                } else {
                    $data['bank'] = $this->$model->bankDB();
                }
                $data['main_content'] = self::MAS_VIEW . "bank/form";
                $this->load->view('layout/template', $data);
                break;
            case 'Save': //insert 
                $rules = [
                    [
                        'field' => 'bank[bank_name]',
                        'label' => 'Bank Name',
                        'rules' => 'required|callback_valid_str'
                    ],
                ];
                $this->form_validation->set_rules($rules);
                if ($this->form_validation->run() == FALSE) {
                    // If validation fails, reload form with error messages
                    $this->session->set_flashdata('chit_alert', array(
                        'message' => "Bank Name cannot contain only spaces",
                        'class' => 'danger',
                        'title' => 'Validation Error'
                    ));
                    redirect('settings/bank/list');
                } else {
                    $bank = $this->input->post('bank');
                    $status = $this->$model->bankDB("insert", "", $bank);
                    if ($status) {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Bank added successfully', 'class' => 'success', 'title' => 'Bank'));
                    } else {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Bank Name already exist', 'class' => 'danger', 'title' => 'Bank'));
                    }
                    redirect('settings/bank/list');
                }
                break;
            // coded by jothika on 10-7-2025 [prevent updating duplicate entries]
            case 'Update':
                $rules = [
                    [
                        'field' => 'bank[bank_name]',
                        'label' => 'Bank Name',
                        'rules' => 'required|callback_valid_str'
                    ],
                ];
                $this->form_validation->set_rules($rules);
                if ($this->form_validation->run() == FALSE) {
                    // If validation fails, reload form with error messages
                    $this->session->set_flashdata('chit_alert', array(
                        'message' => "Bank Name cannot contain only spaces",
                        'class' => 'danger',
                        'title' => 'Validation Error'
                    ));
                    redirect('settings/bank/list');
                } else {
                    $bank = $this->input->post('bank');
                    $status = $this->$model->bankDB("update", $id, $bank);
                    if ($status['status']) {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Bank updated successfully', 'class' => 'success', 'title' => 'Bank'));
                    } else if (isset($status['reason']) && $status['reason'] === 'Duplicate') {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Bank already exist', 'class' => 'danger', 'title' => 'Bank'));
                    } else {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Bank'));
                    }
                    redirect('settings/bank/list');
                }
                break;
            case 'Delete':
                $status = $this->$model->bankDB("delete", $id);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Bank deleted successfully', 'class' => 'success', 'title' => 'Bank'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Bank'));
                }
                redirect('settings/bank/list');
                break;
            default: //json data for listing
                $items = $this->$model->bankDB('get', ($id != NULL ? $id : ''));
                $bank = array(
                    'draw' => 0,
                    'recordsTotal' => count($items),
                    'recordsFiltered' => count($items),
                    'data' => $items
                );
                echo json_encode($bank);
        }
    }
    //write File
    function update_rate_file($rates)
    {
        $file = "../api/rate.txt";
        //	$current = json_decode(file_get_contents($file));
        $insertRate = array(
            'goldrate_14ct' => (isset($rates['goldrate_14ct']) && $rates['goldrate_14ct'] != '' ? $rates['goldrate_14ct'] : NULL),
            'goldrate_18ct' => (isset($rates['goldrate_18ct']) && $rates['goldrate_18ct'] != '' ? $rates['goldrate_18ct'] : NULL),
            'market_gold_18ct' => (isset($rates['market_gold_18ct']) && $rates['market_gold_18ct'] != '' ? $rates['market_gold_18ct'] : NULL),
            'mjdmagoldrate_22ct' => (isset($rates['mjdmagoldrate_22ct']) && $rates['mjdmagoldrate_22ct'] != '' ? $rates['mjdmagoldrate_22ct'] : NULL),
            'mjdmasilverrate_1gm' => (isset($rates['mjdmasilverrate_1gm']) && $rates['mjdmasilverrate_1gm'] != '' ? $rates['mjdmasilverrate_1gm'] : NULL),
            'goldrate_22ct' => (isset($rates['goldrate_22ct']) && $rates['goldrate_22ct'] != '' ? $rates['goldrate_22ct'] : NULL),
            'goldrate_24ct' => (isset($rates['goldrate_24ct']) && $rates['goldrate_24ct'] != '' ? $rates['goldrate_24ct'] : NULL),
            'silverrate_1gm' => (isset($rates['silverrate_1gm']) && $rates['silverrate_1gm'] != '' ? $rates['silverrate_1gm'] : NULL),
            'silverrate_1kg' => (isset($rates['silverrate_1kg']) && $rates['silverrate_1kg'] != '' ? $rates['silverrate_1kg'] : NULL),
            'platinum_1g' => (isset($rates['platinum_1g']) && $rates['platinum_1g'] != '' ? $rates['platinum_1g'] : NULL),
            'updatetime' => (isset($rates['updatetime']) && $rates['updatetime'] != '' ? strtotime($rates['updatetime']) : NULL)
        );
        $content = json_encode($insertRate);
        file_put_contents($file, $content);
    }
    //rate master
    function metal_rates($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        $SETT_MOD = self::SETT_MOD;
        switch ($type) {
            case 'List':
                $data['main_content'] = "metal_rates/list";
                $data['access'] = $this->$SETT_MOD->get_access('admin_settings/metal_rates/list');
                $this->load->view('layout/template', $data);
                break;
            case 'View':
                //print_r($id);exit;
                if ($id != NULL) {
                    $data['rates'] = $this->$model->metal_ratesDB("get", $id);
                    $data['id_branch'] = json_encode($this->$model->get_branch_edit($id));
                } else {
                    $data['rates'] = $this->$model->metal_ratesDB();
                }
                // print_r($data['rates']);
                $data['main_content'] = "metal_rates/form";
                $this->load->view('layout/template', $data);
                break;
            case 'Save':
                $metal = $this->input->post('rates');
                $branch_data = $this->input->post("branch");
                $discount_set = $this->$model->settingsDB('get', '', '');
                $metalrate_set = $this->$model->metal_ratesDB('last', '', '');
                $insertData = array(
                    'mjdmagoldrate_22ct' => (isset($metal['mjdmagoldrate_22ct']) && $metal['mjdmagoldrate_22ct'] != '' ? $metal['mjdmagoldrate_22ct'] : 0.00),
                    'mjdmasilverrate_1gm' => (isset($metal['mjdmasilverrate_1gm']) && $metal['mjdmasilverrate_1gm'] != '' ? $metal['mjdmasilverrate_1gm'] : 0.00),
                    'goldrate_14ct' => (isset($metal['goldrate_14ct']) && $metal['goldrate_14ct'] != '' ? $metal['goldrate_14ct'] : 0.00),
                    'goldrate_9ct' => (isset($metal['goldrate_9ct']) && $metal['goldrate_9ct'] != '' ? $metal['goldrate_9ct'] : 0.00),
                    'market_gold_18ct' => (isset($metal['market_gold_18ct']) && $metal['market_gold_18ct'] != '' ? $metal['market_gold_18ct'] : 0.00),
                    'goldrate_18ct' => (isset($metal['market_gold_18ct']) && $metal['market_gold_18ct'] != '' ? ($discount_set[0]['enableGoldrateDisc_18k'] == 1 && $discount_set[0]['goldDiscAmt_18k'] != '' ? ($metal['market_gold_18ct'] - $discount_set[0]['goldDiscAmt_18k']) : $metal['market_gold_18ct']) : 0.00),
                    'goldrate_22ct' => (isset($metal['mjdmagoldrate_22ct']) && $metal['mjdmagoldrate_22ct'] != '' ? ($discount_set[0]['enableGoldrateDisc'] == 1 && $discount_set[0]['goldDiscAmt'] != '' ? ($metal['mjdmagoldrate_22ct'] - $discount_set[0]['goldDiscAmt']) : $metal['mjdmagoldrate_22ct']) : 0.00),
                    'goldrate_24ct' => (isset($metal['goldrate_24ct']) && $metal['goldrate_24ct'] != '' ? $metal['goldrate_24ct'] : 0.00),
                    'platinum_1g' => (isset($metal['platinum_1g']) && $metal['platinum_1g'] != '' ? $metal['platinum_1g'] : 0.00),
                    'silverrate_1gm' => (isset($metal['mjdmasilverrate_1gm']) && $metal['mjdmasilverrate_1gm'] != '' ? ($discount_set[0]['enableSilver_rateDisc'] == 1 && $discount_set[0]['silverDiscAmt'] != '' ? ($metal['mjdmasilverrate_1gm'] - $discount_set[0]['silverDiscAmt']) : $metal['mjdmasilverrate_1gm']) : 0.00),
                    'silverrate_1kg' => (isset($metal['silverrate_1kg']) && $metal['silverrate_1kg'] != '' ? $metal['silverrate_1kg'] : 0.00),
                    'market_gold_995' => (isset($metal['market_gold_995']) && $metal['market_gold_995'] != '' ? $metal['market_gold_995'] : 0.00),
                    'coin_gold_22ct' => (isset($metal['coin_gold_22ct']) && $metal['coin_gold_22ct'] != '' ? $metal['coin_gold_22ct'] : 0.00),
                    'mjdmasilverrate_999' => (isset($metal['mjdmasilverrate_999']) && $metal['mjdmasilverrate_999'] != '' ? $metal['mjdmasilverrate_999'] : 0.00),
                    'market_gold_20ct' => (isset($metal['market_gold_20ct']) && $metal['market_gold_20ct'] != '' ? $metal['market_gold_20ct'] : 0.00),
                    'id_employee' => $this->session->userdata('uid'),
                    'updatetime' => date('Y-m-d H:i:s'),
                    'add_date' => date("Y-m-d H:i:s"),
                );
                $status = $this->$model->metal_ratesDB("insert", "", $insertData);
                $rate_array = array(
                    'goldrate_22ct' => (isset($metal['goldrate_22ct']) && $metal['goldrate_22ct'] != '' ? $metal['goldrate_22ct'] : 0.00),
                    'goldrate_24ct' => (isset($metal['goldrate_24ct']) && $metal['goldrate_24ct'] != '' ? $metal['goldrate_24ct'] : 0.00),
                    'silverrate_1gm' => (isset($metal['silverrate_1gm']) && $metal['silverrate_1gm'] != '' ? $metal['silverrate_1gm'] : 0.00),
                    'silverrate_1kg' => (isset($metal['silverrate_1kg']) && $metal['silverrate_1kg'] != '' ? $metal['silverrate_1kg'] : 0.00),
                    'id_employee' => 0,
                    'updatetime' => date("Y-m-d H:i:s")
                );
                file_put_contents('../api/rate.txt', $rate_array);
                $branch_id = array();
                if (($this->session->userdata('branch_settings') == 1 && $metal['is_branchwise_rate'] == 1 && $status['status'] == 1)) {
                    //  metalrate branch	
                    $branch_list = implode(",", $branch_data);
                    $branch_id = array(explode(',', $branch_list));
                    foreach ($branch_id[0] as $branch) {
                        $branch_info = array(
                            'id_metalrate' => ($status['insertID']),
                            'id_branch' => ($branch),
                            'status' => 1,
                            'date_add' => date("Y-m-d H:i:s")
                        );
                        $this->$model->insert_metalrate($branch_info, 'branch_rate');
                    }
                    //  metalrate branch	
                }
                if ($status) {
                    $sendNoti = $this->$model->canSendNoti(1);
                    if ($sendNoti) {
                        $this->send_RatesToAllUsers($branch_id[0]);
                    }
                    /*$data['rates'] = $this->$model->metal_ratesDB("get",$status['insertID']);
                    $this->update_rate_file($data['rates']);
                    //$this->send_rate_noti();
                    $serviceID = 21;
                    $service = $this->$model->get_service($serviceID);
                    if($service['serv_sms'] == 1)
                    {	
                        $data =$this->get_SMS_data($serviceID);
                    }*/
                    $this->session->set_flashdata('chit_alert', array('message' => 'Metal rates added successfully', 'class' => 'success', 'title' => 'Metal Rates'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Metal Rates'));
                }
                redirect('settings/rate/list');
                break;
            case 'Update':
                $metal = $this->input->post('rates');
                $discount_set = $this->$model->settingsDB('get', '', '');
                $updateData = array(
                    'goldrate_14ct' => (isset($metal['goldrate_14ct']) && $metal['goldrate_14ct'] != '' ? $metal['goldrate_14ct'] : 0.00),
                    'goldrate_9ct' => (isset($metal['goldrate_9ct']) && $metal['goldrate_9ct'] != '' ? $metal['goldrate_9ct'] : 0.00),
                    'goldrate_18ct' => (isset($metal['market_gold_18ct']) && $metal['market_gold_18ct'] != '' ? ($discount_set[0]['enableGoldrateDisc_18k'] == 1 && $discount_set[0]['goldDiscAmt_18k'] != '' ? ($metal['market_gold_18ct'] - $discount_set[0]['goldDiscAmt_18k']) : $metal['market_gold_18ct']) : 0.00),
                    'market_gold_18ct' => (isset($metal['market_gold_18ct']) && $metal['market_gold_18ct'] != '' ? $metal['market_gold_18ct'] : 0.00),
                    'mjdmagoldrate_22ct' => (isset($metal['mjdmagoldrate_22ct']) && $metal['mjdmagoldrate_22ct'] != '' ? $metal['mjdmagoldrate_22ct'] : 0.00),
                    'goldrate_22ct' => (isset($metal['mjdmagoldrate_22ct']) && $metal['mjdmagoldrate_22ct'] != '' ? ($discount_set[0]['enableGoldrateDisc'] == 1 && $discount_set[0]['goldDiscAmt'] != '' ? ($metal['mjdmagoldrate_22ct'] - $discount_set[0]['goldDiscAmt']) : $metal['mjdmagoldrate_22ct']) : 0.00),
                    'goldrate_24ct' => (isset($metal['goldrate_24ct']) && $metal['goldrate_24ct'] != '' ? $metal['goldrate_24ct'] : 0.00),
                    'platinum_1g' => (isset($metal['platinum_1g']) && $metal['platinum_1g'] != '' ? $metal['platinum_1g'] : 0.00),
                    'silverrate_1gm' => (isset($metal['mjdmasilverrate_1gm']) && $metal['mjdmasilverrate_1gm'] != '' ? ($discount_set[0]['enableSilver_rateDisc'] == 1 && $discount_set[0]['silverDiscAmt'] != '' ? ($metal['mjdmasilverrate_1gm'] - $discount_set[0]['silverDiscAmt']) : $metal['mjdmasilverrate_1gm']) : 0.00),
                    'mjdmasilverrate_1gm' => (isset($metal['mjdmasilverrate_1gm']) && $metal['mjdmasilverrate_1gm'] != '' ? $metal['mjdmasilverrate_1gm'] : 0.00),
                    'silverrate_1kg' => (isset($metal['silverrate_1kg']) && $metal['silverrate_1kg'] != '' ? $metal['silverrate_1kg'] : 0.00),
                    'market_gold_995' => (isset($metal['market_gold_995']) && $metal['market_gold_995'] != '' ? $metal['market_gold_995'] : 0.00),
                    'coin_gold_22ct' => (isset($metal['coin_gold_22ct']) && $metal['coin_gold_22ct'] != '' ? $metal['coin_gold_22ct'] : 0.00),
                    'mjdmasilverrate_999' => (isset($metal['mjdmasilverrate_999']) && $metal['mjdmasilverrate_999'] != '' ? $metal['mjdmasilverrate_999'] : 0.00),
                    'market_gold_20ct' => (isset($metal['market_gold_20ct']) && $metal['market_gold_20ct'] != '' ? $metal['market_gold_20ct'] : 0.00),
                    'id_employee' => $this->session->userdata('uid'),
                    'add_date' => date("Y-m-d H:i:s"),
                );
                $status = $this->$model->metal_ratesDB("update", $id, $updateData);
                if ($status) {
                    $data['rates'] = $this->$model->metal_ratesDB("get", $id);
                    //$this->update_rate_file($data['rates']);
                    $this->session->set_flashdata('chit_alert', array('message' => 'Metal rates updated successfully', 'class' => 'success', 'title' => 'Metal Rates'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Metal Rates'));
                }
                redirect('settings/rate/list');
                break;
            case 'Delete':
                $status = $this->$model->metal_ratesDB("delete", $id);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Metal rates deleted successfully', 'class' => 'success', 'title' => 'Metal Rates'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Metal Rates'));
                }
                redirect('settings/rate/list');
                break;
            default:
                $data['rates'] = $this->$model->metal_ratesDB("get");
                $data['max_id'] = $this->$model->max_metalrate();
                $data['access'] = $this->$model->get_access('settings/rate/list');
                //print_r($data['rates']);exit;
                echo json_encode($data);
                break;
                /*case 'mjdma_update':
                      $metal=$this->input->post('rates');
                   //formatting form values
                   $rate_array=array( 
                                    'goldrate_22ct' 	=> (isset($metal['goldrate_22ct']) && $metal['goldrate_22ct']!=''? $metal['goldrate_22ct']:0.00),
                                    'goldrate_24ct' 	=> (isset($metal['goldrate_24ct']) && $metal['goldrate_24ct']!=''? $metal['goldrate_24ct']:0.00),
                                    'silverrate_1gm' 	=> (isset($metal['silverrate_1gm']) && $metal['silverrate_1gm']!=''? $metal['silverrate_1gm']:0.00),
                                    'silverrate_1kg' 	=> (isset($metal['silverrate_1kg']) && $metal['silverrate_1kg']!=''? $metal['silverrate_1kg']:0.00),                
                                    'id_employee' 	    => 0,
                                    'updatetime'   		=> date("Y-m-d H:i:s")
                                     );
                                     //echo "<pre>";print_r($rate_array);echo "</pre>";exit;
                           file_put_contents('../api/rate.txt', $rate_array);
                         //inserting data                  
                           $status = $this->$model->metal_ratesDB("insert","",$rate_array);
                           $url_wc_demo="http://www.bullionsoftware.com/winchit/admin/index.php/settings/mjdma_update/";
                           $url_wc_bmn="http://www.bmnthangamaaligai.com/bmnsavingscheme/new/admin/index.php/settings/mjdma_update/";
                           $url_wc_cdj="http://www.cdjewellery.com/savingscheme/admin/index.php/settings/mjdma_update/";
                           $url_wc_npr="http://nprthangamaligai.in/admin/index.php/settings/mjdma_update/";
                           $url_wc_srj="http://srjewellery.in/savingscheme/admin/index.php/settings/mjdma_update/";
                        //url-ify the data for the POST
                        $field_string = http_build_query($rate_array);
                           foreach (array($url_wc_demo,$url_wc_bmn) as $url) {
                            //open connection
                            $ch = curl_init();
                            //set the url, number of POST vars, POST data
                            curl_setopt($ch,CURLOPT_URL, $url);
                            curl_setopt($ch,CURLOPT_POST, 1);
                            curl_setopt($ch,CURLOPT_POSTFIELDS, $field_string);
                            //execute post
                            $result = curl_exec($ch);
                            //echo $result;
                            //close connection
                            curl_close($ch);
                        }
                        $email_from = 'arun@vikashinfosolutions.com';
                        $email_message = 'Dear Admin,<br>Rates has been updated <br><br>';
                        $email_to = "vinoth@logimaxindia.com";
                            $email_subject = "Rate has been updated at:".date("d/m/Y");
                        $headers = "MIME-Version: 1.0" . "\r\n";
                        $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
                        $headers .= 'From: '.$email_from."\r\n".
                        'Reply-To: '.$email_from."\r\n" .
                        'X-Mailer: PHP/' . phpversion();
                        @mail($email_to, $email_subject, $email_message, $headers);
                        //file_put_contents('../api/rate.txt', $jsonData);
                    $_SESSION['_msg'] = "Updated Successfully";
                    redirect('settings/rate/list'); 
                break;*/
        }
    }
    /*function send_rate_noti(){
        $model = self::MODEL;
        $result =array();
        $send_notif =$this->$model->check_noti_settings();
        if($send_notif == 1 ){
            //send rate notification
                    $data = $this->$model->get_cusnotiData('1');
                    $a=0;
                    foreach ($data['data']  as $r){
                        if(sizeof($r['token'])>0){
                            $arraycontent=array('token'=>$r['token'],
                                                'notification_service'=>1,
                                                'header'=>$data['header'],
                                                'message'=>$r['message'],
                                                'mobile'=>$r['mobile'],
                                                'footer'=>$data['footer']						
                            );
                            $bdaywish = $this->send_singlealert_notification($arraycontent);
                            $result['rate_noti'][$a]=$bdaywish;
                            $a++;							
                        }
                    }
        }	
        return $result;	
    }*/
    /*function send_singlealert_notification($alertdetails = array()) 
    {
        $registrationIds =array();
        $registrationIds[0] = $alertdetails['token'];
        $content = array(
        "en" => $alertdetails['message']
        );
        if($alertdetails['notification_service']== '2'){
            $targetUrl='#/app/offers';
        }
        else if($alertdetails['notification_service']== '3'){
            $targetUrl='#/app/newarrivals';
        }
        else if($alertdetails['notification_service']== '4' || $alertdetails['notification_service']== '5' || $alertdetails['notification_service']== '6'){
                $targetUrl='#/app/paydues';
        }
        else{
            $targetUrl='#/app/notification';
        }
        $fields = array(
        'app_id' => $this->config->item('app_id'),
        'include_player_ids' => $registrationIds,
        'contents' => $content,
        'headings' => array("en" => $alertdetails['header']),
        'subtitle' => array("en" => $alertdetails['footer']),
        'data' => array('targetUrl'=>$targetUrl,'noti_service'=>$alertdetails['notification_service'],'mobile'=>$alertdetails['mobile']),
        'big_picture' => (isset($alertdetails['noti_img'])?$alertdetails['noti_img']:" ")
        );
        $auth_key = $this->config->item('authentication_key');
        $fields = json_encode($fields);
         $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
          'Authorization: Basic '.$auth_key));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);  
    return $response;
    }*/
    public function get_SMS_data($serviceID)
    {
        $model = self::MODEL;
        $models = self::ADM_MODEL;
        $mobi_no = $this->$model->get_allcustomersms_list();
        $data = $this->$model->get_cusnotiData('1');
        $mobile = array();
        $sms_data = array();
        foreach ($data['data'] as $r) {
            $arraycontent = $r['message'];
        }
        foreach ($mobi_no as $row) {
            $sms_data['mobile'][] = $row;
        }
        $sms_data['message'] = $arraycontent;
        $data = $this->send_bulk_sms($sms_data);
    }
    function send_bulk_sms($data)
    {
        $mob_length = sizeof($data['mobile']);
        $content = array_chunk($data['mobile'], 5);
        foreach ($content as $mobile) {
            $mobile_number = $mobile;
            $fields = array(
                'mobile' => $mobile_number,
                'message' => $data['message']
            );
            $fields = json_encode($fields);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://nammauzhavan.com/api/v1/smjtvm_sendsms");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Basic ' . base64_encode("lmx@uzhavan:lmx@2018")
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $response = curl_exec($ch);
            curl_close($ch);
            $data['response'] = $response;
        }
        $status = $this->update_prosms($mob_length);    //Total customer
        return TRUE;
    }
    function update_prosms($mob_length)
    {
        $query_validate = $this->db->query('UPDATE promotion_api_settings SET debit_promotion = debit_promotion - ' . $mob_length . ' WHERE id_promotion_api =1 and debit_promotion > 0');
        if ($query_validate > 0) {
            return true;
        } else {
            return false;
        }
    }
    //Drawee Master
    public function drawee($type, $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case 'List': //showing list
                $data['bank'] = $this->$model->draweeDB('get', ($id != NULL ? $id : ''));
                $data['main_content'] = self::MAS_VIEW . "drawee/list";
                $this->load->view('layout/template', $data);
                break;
            case 'View': //showing form
                if ($id != NULL) {
                    $data['bank'] = $this->$model->draweeDB("get", $id);
                } else {
                    $data['bank'] = $this->$model->draweeDB();
                }
                $data['main_content'] = self::MAS_VIEW . "drawee/form";
                $this->load->view('layout/template', $data);
                break;
            case 'Save': //insert 
                $bank = $this->input->post('bank');
                $status = $this->$model->draweeDB("insert", "", $bank);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Drawee Account added successfully', 'class' => 'success', 'title' => 'Drawee Account'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Drawee Account'));
                }
                redirect('settings/drawee/list');
                break;
            case 'Update':
                $bank = $this->input->post('bank');
                $status = $this->$model->draweeDB("update", $id, $bank);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Drawee Account updated successfully', 'class' => 'success', 'title' => 'Drawee Account'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Drawee Account'));
                }
                redirect('settings/drawee/list');
                break;
            case 'Delete':
                $status = $this->$model->draweeDB("delete", $id);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Drawee Account deleted successfully', 'class' => 'success', 'title' => 'Drawee Account'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Drawee Account'));
                }
                redirect('settings/drawee/list');
                break;
            default: //json data for listing
                $items = $this->$model->draweeDB('get', ($id != NULL ? $id : ''));
                $bank = array(
                    'draw' => 0,
                    'recordsTotal' => count($items),
                    'recordsFiltered' => count($items),
                    'data' => $items
                );
                echo json_encode($bank);
        }
    }
    //Paymode Master
    public function payment_mode($type, $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case 'List': //showing list
                $data['paymode'] = $this->$model->paymodeDB('get', ($id != NULL ? $id : ''));
                $data['main_content'] = self::MAS_VIEW . "paymode/list";
                $this->load->view('layout/template', $data);
                break;
            case 'View': //showing form
                if ($id != NULL) {
                    $data['paymode'] = $this->$model->paymodeDB("get", $id);
                } else {
                    $data['paymode'] = $this->$model->paymodeDB();
                }
                $data['main_content'] = self::MAS_VIEW . "paymode/form";
                $this->load->view('layout/template', $data);
                break;
            case 'Save': //insert 
                $rules = [
                    [
                        'field' => 'paymode[mode_name]',
                        'label' => 'Payment Mode',
                        'rules' => 'required|callback_valid_str'
                    ],
                ];
                $this->form_validation->set_rules($rules);
                if ($this->form_validation->run() == FALSE) {
                    // If validation fails, reload form with error messages
                    $this->session->set_flashdata('chit_alert', array(
                        'message' => "Payment Mode  cannot contain only spaces",
                        'class' => 'danger',
                        'title' => 'Validation Error'
                    ));
                    redirect('settings/paymode/list');
                } else {
                    $paymode = $this->input->post('paymode');
                    $status = $this->$model->paymodeDB("insert", "", $paymode);
                    if ($status) {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Payment mode added successfully', 'class' => 'success', 'title' => 'Payment Mode'));
                    } else {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Payment mode already exist', 'class' => 'danger', 'title' => 'Payment Mode'));
                    }
                    redirect('settings/paymode/list');
                }
                break;
            case 'Update':
                // coded by jothika on 11-7-2025 [prevent updating duplicate entries]
                $rules = [
                    [
                        'field' => 'paymode[mode_name]',
                        'label' => 'Payment Mode',
                        'rules' => 'required|callback_valid_str'
                    ],
                ];
                $this->form_validation->set_rules($rules);
                if ($this->form_validation->run() == FALSE) {
                    // If validation fails, reload form with error messages
                    $this->session->set_flashdata('chit_alert', array(
                        'message' => "Payment Mode  cannot contain only spaces",
                        'class' => 'danger',
                        'title' => 'Validation Error'
                    ));
                    redirect('settings/paymode/list');
                } else {
                    $paymode = $this->input->post('paymode');
                    $status = $this->$model->paymodeDB("update", $id, $paymode);
                    if ($status['status']) {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Payment mode updated successfully', 'class' => 'success', 'title' => 'Payment Mode'));
                    } else if (isset($status['reason']) && $status['reason'] === 'Duplicate') {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Payment mode already exist', 'class' => 'danger', 'title' => 'Payment Mode'));
                    } else {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Payment Mode'));
                    }
                    redirect('settings/paymode/list');
                }
                break;
            case 'Delete':
                $status = $this->$model->paymodeDB("delete", $id);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Payment mode deleted successfully', 'class' => 'success', 'title' => 'Payment Mode'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Payment Mode'));
                }
                redirect('settings/paymode/list');
                break;
            default: //json data for listing
                $items = $this->$model->paymodeDB('get', ($id != NULL ? $id : ''));
                $paymode = array(
                    'draw' => 0,
                    'recordsTotal' => count($items),
                    'recordsFiltered' => count($items),
                    'data' => $items
                );
                echo json_encode($paymode);
        }
    }
    function get_access_rights()
    {
        $model = self::SET_MODEL;
        $url = $this->input->post('url');
        $access = $this->$model->get_access($url);
        echo json_encode($access);
    }
    /*-- Coded by ARVK --*/
    function get_country()
    {
        $data = $this->admin_settings_model->get_country();
        echo $data;
    }
    function update_country()
    {
        $currency = $this->input->post('comp');
        $log_model = self::LOG_MODEL;
        $id = $currency['id_country'];
        $currency_info = array(
            'currency_name' => (isset($currency['currency_name']) ? $currency['currency_name'] : NULL),
            'currency_code' => (isset($currency['currency_code']) ? $currency['currency_code'] : NULL),
            'mob_code' => (isset($currency['mob_code']) ? $currency['mob_code'] : NULL),
            'mob_no_len' => (isset($currency['mob_no_len']) ? $currency['mob_no_len'] : 0),
            'date_upd' => date('Y-m-d H:i:s')
        );
        $log_data = array(                                                                //scheme log details
            'id_log' => $this->id_log,
            'event_date' => date("Y-m-d H:i:s"),
            'module' => 'Settings - Country Settings',
            'operation' => 'Edit',
            'record' => 'Country Settings',
            'remark' => 'General Settings edited successfully'
        );
        // print_r($log_data );exit;
        $this->$log_model->log_detail('insert', '', $log_data);
        $status = $this->admin_settings_model->update_country($currency_info, $id);
        if ($status) {
            $this->session->set_flashdata('chit_alert', array('message' => 'Currency data added successfully', 'class' => 'success', 'title' => 'Currency'));
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Currency'));
        }
        redirect('settings/general/edit/1');
    }
    public function get_countryCurrency($id)
    {
        $data = $this->admin_settings_model->get_curr_detail($id);
        echo json_encode($data);
    }
    function receipt_settings($id)
    {
        $model = self::SET_MODEL;
        $general = $this->input->post('general');
        $data = array(
            'receipt' => $general['receipt'],
            'receipt_no_set' => $general['receipt_no_set']
        );
        $update = $this->$model->settingsDB('update', $id, $data);
        if ($update) {
            $this->session->set_flashdata('chit_alert', array('message' => 'General Settings edited successfully', 'class' => 'success', 'title' => 'General Settings'));
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'General Settings'));
        }
        redirect('settings/general/edit/1');
        //	 break;
    }
    /*-- / Coded by ARVK --*/
    public function get_state()
    {
        if (isset($_POST['id_country'])) {
            //$this->load->model('admin_settings_model');
            $data = $this->admin_settings_model->get_state($_POST['id_country']);
            echo $data;
        }
    }
    public function get_city()
    {
        if (isset($_POST['id_state'])) {
            //$this->load->model('admin_settings_model');
            $data = $this->admin_settings_model->get_city($_POST['id_state']);
            echo $data;
        }
    }
    function get_village_by_pincode()
	{
		if (isset($_POST['pincode'])) {
            $data = $this->admin_settings_model->get_village_by_pincode($_POST['pincode']);
            echo $data;
        }
    }
    function company_form($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case "Add":
                $data['comp'] = $this->$model->company_empty_record();
                $data['main_content'] = self::VIEW_FOLDER . "company";
                $this->load->view('layout/template', $data);
                break;
            case "Edit":
                $data['comp'] = $this->$model->get_company_detail($id);
                $data['main_content'] = self::VIEW_FOLDER . "company";
                /*echo "<pre>";print_r($data);echo "</pre>";exit;*/
                $this->load->view('layout/template', $data);
                break;
        }
    }
    function company_post($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case "Save":
                $comp = $this->input->post('comp');
                $currency = $this->admin_settings_model->get_curr_detail($comp['country']);    //Coded by ARVK
                $data = array(
                    'company_name' => (isset($comp['company_name']) ? $comp['company_name'] : NULL),
                    'short_code' => (isset($comp['short_code']) ? $comp['short_code'] : NULL),
                    'address1' => (isset($comp['address1']) ? $comp['address1'] : NULL),
                    'address2' => (isset($comp['address2']) ? $comp['address2'] : NULL),
                    'id_country' => (isset($comp['country']) ? $comp['country'] : 0),
                    'id_state' => (isset($comp['state']) ? $comp['state'] : 0),
                    'id_city' => (isset($comp['city']) ? $comp['city'] : 0),
                    'pincode' => (isset($comp['pincode']) ? $comp['pincode'] : NULL),
                    'mobile' => (isset($comp['mobile']) ? $comp['mobile'] : NULL),
                    'whatsapp_no' => (isset($comp['whatsapp_no']) ? $comp['whatsapp_no'] : NULL),
                    'mobile1' => (isset($comp['mobile1']) ? $comp['mobile1'] : NULL),
                    'tollfree1' => (isset($comp['tollfree1']) ? $comp['tollfree1'] : NULL),
                    'phone' => (isset($comp['phone']) ? $comp['phone'] : NULL),
                    'phone1' => (isset($comp['phone1']) ? $comp['phone1'] : NULL),
                    'email' => (isset($comp['email']) ? $comp['email'] : NULL),
                    'website' => (isset($comp['website']) ? $comp['website'] : NULL),
                    'bank_acc_number' => (isset($comp['bank_acc_number']) ? $comp['bank_acc_number'] : NULL),
                    'bank_acc_name' => (isset($comp['bank_acc_name']) ? $comp['bank_acc_name'] : NULL),
                    'bank_name' => (isset($comp['bank_name']) ? $comp['bank_name'] : NULL),
                    'bank_branch' => (isset($comp['bank_branch']) ? $comp['bank_branch'] : NULL),
                    'bank_ifsc' => (isset($comp['bank_ifsc']) ? $comp['bank_ifsc'] : NULL),
                    'comp_name_in_sms' => (isset($comp['comp_name_in_sms']) ? $comp['comp_name_in_sms'] : NULL),
                    'date_add' => date('Y-m-d H:i:s'),
                    'map_url' => (isset($comp['map']) ? $comp['map'] : NULL)
                );
                /* -- Coded by ARVK -- */
                $currency_info = array(
                    'currency_name' => (isset($currency['currency_name']) ? $currency['currency_name'] : NULL),
                    'currency_symbol' => (isset($currency['currency_code']) ? $currency['currency_code'] : NULL),
                    'mob_code' => (isset($currency['mob_code']) ? $currency['mob_code'] : NULL),
                    'mob_no_len' => (isset($currency['mob_no_len']) ? $currency['mob_no_len'] : 0)
                );
                $this->db->trans_begin();
                $comp_info_status = $this->$model->create_company($data);
                $currency_info_status = $this->$model->settingsDB('update', $id, $currency_info);
                if (!empty($comp['city'])) {
                    $data = array(
                        'is_default' => 1,
                    );
                    $this->$model->updateData($data, 'id_city', $comp['city'], 'city');
                }
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Company data added successfully', 'class' => 'success', 'title' => 'Company'));
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Company'));
                }
                redirect('settings/company/list');
                break;
            /* -- / Coded by ARVK -- */
            case "Update":
                $comp = $this->input->post('comp');
                $currency = $this->admin_settings_model->get_curr_detail($comp['country']); //Coded by ARVK
                $data = array(
                    'company_name' => (isset($comp['company_name']) ? $comp['company_name'] : NULL),
                    'short_code' => (isset($comp['short_code']) ? $comp['short_code'] : NULL),
                    'address1' => (isset($comp['address1']) ? $comp['address1'] : NULL),
                    'address2' => (isset($comp['address2']) ? $comp['address2'] : NULL),
                    'id_country' => (isset($comp['country']) ? $comp['country'] : 0),
                    'id_state' => (isset($comp['state']) ? $comp['state'] : 0),
                    'id_city' => (isset($comp['city']) ? $comp['city'] : 0),
                    'pincode' => (isset($comp['pincode']) ? $comp['pincode'] : NULL),
                    'mobile' => (isset($comp['mobile']) ? $comp['mobile'] : NULL),
                    'whatsapp_no' => (isset($comp['whatsapp_no']) ? $comp['whatsapp_no'] : NULL),
                    'mobile1' => (isset($comp['mobile1']) ? $comp['mobile1'] : NULL),
                    'tollfree1' => (isset($comp['tollfree1']) ? $comp['tollfree1'] : NULL),
                    'phone' => (isset($comp['phone']) ? $comp['phone'] : NULL),
                    'phone1' => (isset($comp['phone1']) ? $comp['phone1'] : NULL),
                    'email' => (isset($comp['email']) ? $comp['email'] : NULL),
                    'website' => (isset($comp['website']) ? $comp['website'] : NULL),
                    'bank_acc_number' => (isset($comp['bank_acc_number']) ? $comp['bank_acc_number'] : NULL),
                    'bank_acc_name' => (isset($comp['bank_acc_name']) ? $comp['bank_acc_name'] : NULL),
                    'bank_name' => (isset($comp['bank_name']) ? $comp['bank_name'] : NULL),
                    'bank_branch' => (isset($comp['bank_branch']) ? $comp['bank_branch'] : NULL),
                    'bank_ifsc' => (isset($comp['bank_ifsc']) ? $comp['bank_ifsc'] : NULL),
                    'comp_name_in_sms' => (isset($comp['comp_name_in_sms']) ? $comp['comp_name_in_sms'] : NULL),
                    'date_upd' => date('Y-m-d H:i:s'),
                    'map_url' => (isset($comp['map']) ? $comp['map'] : NULL),
                    "show_social_media" => (isset($comp['show_social_media']) ? $comp['show_social_media'] : 0),
                    'fb_url' => (isset($comp['fb_url']) && $comp['show_social_media'] == 1 ? $comp['fb_url'] : NULL),
                    'insta_url' => (isset($comp['insta_url']) && $comp['show_social_media'] == 1 ? $comp['insta_url'] : NULL),
                    'youtube_url' => (isset($comp['youtube_url']) && $comp['show_social_media'] == 1 ? $comp['youtube_url'] : NULL),
                    'whatsapp_url' => (isset($comp['whatsapp_url']) && $comp['show_social_media'] == 1 ? $comp['whatsapp_url'] : NULL)
                );

                /* -- Coded by ARVK -- */
                $currency_info = array(
                    'currency_name' => (isset($currency['currency_name']) ? $currency['currency_name'] : NULL),
                    'currency_symbol' => (isset($currency['currency_code']) ? $currency['currency_code'] : NULL),
                    'mob_code' => (isset($currency['mob_code']) ? $currency['mob_code'] : NULL),
                    'mob_no_len' => (isset($currency['mob_no_len']) ? $currency['mob_no_len'] : 0)
                );
                $this->db->trans_begin();
                $comp_info_status = $this->$model->update_company($data, $id);
                $currency_info_status = $this->$model->settingsDB('update', $id, $currency_info);
                if (!empty($comp['city'])) {
                    $data = array(
                        'is_default' => 1,
                    );
                    $this->$model->updateData($data, 'id_city', $comp['city'], 'city');
                }
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Company data updated successfully', 'class' => 'success', 'title' => 'Company'));
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Company'));
                }
                redirect('settings/company/list');
                break;
                /* -- / Coded by ARVK -- */
        }
    }
    function comp_list()
    {
        $model = self::SET_MODEL;
        $data['comps'] = $this->$model->get_comp_list();
        $data['main_content'] = self::VIEW_FOLDER . 'comp_list';
        $this->load->view('layout/template', $data);
    }
    function is_mobile_exists($mobile)
    {
        if ($mobile != NULL) {
            $model = self::CUS_MODEL;
            $this->load->model($model);
            $available = $this->$model->mobile_available($mobile);
            return ($available ? TRUE : FALSE);
        } else {
            return FALSE;
        }
    }
    function contact_already_exist($mobile, $email)
    {
        $is_exists = FALSE;
        $model = self::CUS_MODEL;
        $this->load->model($model);
        if ($mobile != NULL) {
            $is_exists = $this->$model->mobile_available($mobile);
        }
        if ($email != NULL) {
            $is_exists = $this->$model->email_available($email);
        }
        return $is_exists;
    }
    //  function get scheme id
    function get_scheme_id($scheme_code)
    {
        $model = self::SCH_MODEL;
        $this->load->model($model);
        $id_scheme = $this->$model->get_scheme_id($scheme_code);
        return ($id_scheme != NULL ? $id_scheme : 0);
    }
    function get_weight_scheme_id($type, $scheme_code)
    {
        $model = self::SCH_MODEL;
        $this->load->model($model);
        $id_scheme = $this->$model->get_weight_scheme_id($type, $scheme_code);
        return ($id_scheme != NULL ? $id_scheme : 0);
    }
    function weight_form($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case "Add":
                $weight = $this->input->post("weight");
                $wt_data = array('weight' => $weight);
                $this->db->trans_begin();
                $this->$model->insert_weight($wt_data);
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'New weight added successfully', 'class' => 'success', 'title' => 'Add Weight'));
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Add Weight'));
                }
                redirect('settings/weight');
                break;
            case "Edit":
                $data['weight'] = $this->$model->get_weight($id);
                echo json_encode($data['weight']);
                //$data['wt']=$weight['weight'];
                break;
            case "Delete":
                $status = $this->$model->delete_weight($id);
                echo $status;
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Weight deleted successfully', 'class' => 'success', 'title' => 'Delete Weight'));
                }
                redirect('settings/weight');
                //$data['wt']=$weight['weight'];
                break;
            case "Update":
                $data['weight'] = $this->$model->get_weight($id);
                //$id=$this->input->post('id_weight');
                $weight = $this->input->post('weight');
                $data = array("weight" => $weight);
                $this->$model->update_weight($data, $id);
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Weight updated successfully', 'class' => 'success', 'title' => 'Edit Weight'));
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Edit Weight'));
                }
                // redirect('settings/weight');
                //$data['wt']=$weight['weight'];
                break;
            default:
                //$data['weights']=$this->$model->get_weights();
                $data['main_content'] = self::MAS_VIEW . 'weight/list';
                $this->load->view('layout/template', $data);
                break;
        }
    }
    function classification_form($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case "Add":
                $clsfy = $this->input->post("classification_name");
                $des = $this->input->post("description");
                $cls_data = array('classification_name' => $clsfy, 'description' => $des);
                $this->db->trans_begin();
                $classification_id = $this->$model->insert_classification($cls_data);
                //print_r($this->db->last_query());exit;
                if (isset($_FILES['file']['name'])) {
                    if ($classification_id > 0) {
                        $result = $this->set__clsfy_image($classification_id);
                    }
                }
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'New Classification added successfully', 'class' => 'success', 'title' => 'Add Classification'));
                    echo TRUE;
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Add Classification'));
                }
                break;
            case "Edit":
                $data = $this->$model->get_classification($id);
                echo json_encode($data);
                //$data['wt']=$weight['weight'];
                break;
            case "Delete":
                $status = $this->$model->delete_classification($id);
                echo $status;
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Classification deleted successfully', 'class' => 'success', 'title' => 'Delete Classification'));
                }
                redirect('settings/classification');
                //$data['wt']=$weight['weight'];
                break;
            case "Update":
                $classification = $this->input->post('classification_name');
                $description = $this->input->post('description');
                $data = array("classification_name" => $classification, "description" => $description);
                $update_id = $this->$model->update_classification($data, $id);
                if (isset($_FILES['file']['name'])) {
                    if ($update_id > 0) {
                        $this->set__clsfy_image($id);
                    }
				}else{
				$data['logo']= null;
				$status=$this->$model->update_classification($data,$id);
                }
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Classification updated successfully', 'class' => 'success', 'title' => 'Edit Classification'));
                    echo TRUE;
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Edit Classification'));
                }
                break;
            default:
                $data['main_content'] = self::MAS_VIEW . 'classification/list';
                $this->load->view('layout/template', $data);
                break;
        }
    }
    function dept_form($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case "Add":
                $dept = $this->input->post("dept");
                $dept_data = array('name' => $dept);
                $this->db->trans_begin();
                // coded by jothika on 10-7-2025 [prevent insert duplicate entries]
                $result = $this->$model->insert_dept($dept_data);
                if ($this->db->trans_status() === TRUE) {
                    if ($result === true) {
                        $this->db->trans_commit();
                        $this->session->set_flashdata('chit_alert', array('message' => 'New department added successfully', 'class' => 'success', 'title' => 'Add Department'));
                    } else {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('chit_alert', array('message' => 'Department already exists', 'class' => 'danger', 'title' => 'Add Department'));
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Add Department'));
                }
                //redirect('settings/dept');
                break;
            case "Edit":
                $data['dept'] = $this->$model->get_dept($id);
                echo json_encode($data['dept']);
                //$data['wt']=$weight['weight'];
                break;
            case "Delete":
                $status = $this->$model->delete_dept($id);
                echo $status;
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Department deleted successfully', 'class' => 'success', 'title' => 'Delete Department'));
                }
                redirect('settings/dept');
                //$data['wt']=$weight['weight'];
                break;
            case "Update":
                $data['dept'] = $this->$model->get_dept($id);
                //$id=$this->input->post('id_weight');
                $dept = $this->input->post('dept');
                $data = array("name" => $dept);
                // coded by jothika on 10-7-2025 [prevent update duplicate entries]
                $result = $this->$model->update_dept($data, $id);
                if ($this->db->trans_status() === TRUE) {
                    if ($result === true) {
                        $this->db->trans_commit();
                        $this->session->set_flashdata('chit_alert', array('message' => 'Department updated successfully', 'class' => 'success', 'title' => 'Edit Department'));
                    } else {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('chit_alert', array('message' => 'Department already exists', 'class' => 'danger', 'title' => 'Edit Department'));
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Edit Department'));
                }
                break;
            default:
                $data['main_content'] = self::MAS_VIEW . 'dept/list';
                $this->load->view('layout/template', $data);
                break;
        }
    }
    function design_form($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case "Add":
                $design = $this->input->post("design");
                $design_data = array('name' => $design);
                $this->db->trans_begin();
                // coded by jothika on 10-7-2025 [prevent insert duplicate entries]
                $result = $this->$model->insert_design($design_data);
                if ($this->db->trans_status() === TRUE) {
                    if ($result === true) {
                        $this->db->trans_commit();
                        $this->session->set_flashdata('chit_alert', array('message' => 'New designation added successfully', 'class' => 'success', 'title' => 'Add Designation'));
                    } else {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('chit_alert', array('message' => 'Designation already exists', 'class' => 'danger', 'title' => 'Add Designation'));
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Add Designation'));
                }
                //redirect('settings/design');
                break;
            case "Edit":
                $data['design'] = $this->$model->get_design($id);
                echo json_encode($data['design']);
                //$data['wt']=$weight['weight'];
                break;
            case "Delete":
                $status = $this->$model->delete_design($id);
                echo $status;
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Designation deleted successfully', 'class' => 'success', 'title' => 'Delete Designation'));
                }
                redirect('settings/design');
                //$data['wt']=$weight['weight'];
                break;
            case "Update":
                $data['design'] = $this->$model->get_design($id);
                //$id=$this->input->post('id_weight');
                $design = $this->input->post('design');
                $data = array("name" => $design);
                // coded by jothika on 10-7-2025 [prevent update duplicate entries]
                $result = $this->$model->update_design($data, $id);
                if ($this->db->trans_status() === TRUE) {
                    if ($result === true) {
                        $this->db->trans_commit();
                        $this->session->set_flashdata('chit_alert', array('message' => 'Designation updated successfully', 'class' => 'success', 'title' => 'Edit Designation'));
                    } else {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('chit_alert', array('message' => 'Designation already exist', 'class' => 'danger', 'title' => 'Edit Designation'));
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Edit Designation'));
                }
                break;
            default:
                $data['main_content'] = self::MAS_VIEW . 'designation/list';
                $this->load->view('layout/template', $data);
                break;
        }
    }
    function ajax_get_depts()
    {
        $model = self::SET_MODEL;
        $items = $this->$model->ajax_get_depts();
        $access = $this->$model->get_access('settings/dept');
        $dept = array(
            'access' => $access,
            'data' => $items
        );
        echo json_encode($dept);
    }
    function ajax_get_designs()
    {
        $model = self::SET_MODEL;
        $items = $this->$model->ajax_get_designs();
        $access = $this->$model->get_access('settings/design');
        $depts = array(
            'access' => $access,
            'data' => $items
        );
        echo json_encode($depts);
    }
    function ajax_get_weights()
    {
        $model = self::SET_MODEL;
        $items = $this->$model->ajax_get_weights();
        $access = $this->$model->get_access('settings/weight');
        $weights = array(
            'access' => $access,
            'data' => $items
        );
        echo json_encode($weights);
    }
    function ajax_get_classifications()
    {
        $model = self::SET_MODEL;
        $items = $this->$model->ajax_get_classifications();
        $access = $this->$model->get_access('settings/classification');
        $classifications = array(
            'access' => $access,
            'data' => $items
        );
        echo json_encode($classifications);
    }
    function ajax_get_bank()
    {
        $model = self::SET_MODEL;
        $items = $this->$model->bankDB('get');
        $access = $this->$model->get_access('settings/bank/list');
        $bank = array(
            'access' => $access,
            'data' => $items
        );
        echo json_encode($bank);
    }
    function ajax_get_paymentMode()
    {
        $model = self::SET_MODEL;
        $items = $this->$model->paymodeDB('get', '');
        $access = $this->$model->get_access('settings/paymode/list');
        $paymode = array(
            'access' => $access,
            'data' => $items
        );
        echo json_encode($paymode);
    }
    function ajax_get_drawee()
    {
        $model = self::SET_MODEL;
        $items = $this->$model->draweeDB('get', '');
        $access = $this->$model->get_access('settings/drawee/list');
        $drawee = array(
            'access' => $access,
            'data' => $items
        );
        echo json_encode($drawee);
    }
    function ajax_get_exportlist()
    {
        $model = self::PAY_MODEL;
        $this->load->model($model);
        $status = $this->input->post('status');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        if ($from != NULL and $to != NULL) {
            $payments = $this->$model->ajax_get_payments($status, date("Y-m-d", strtotime($from)), date("Y-m-d", strtotime($to)));
        } else {
            $payments = $this->$model->ajax_get_payments($status, date("Y-m-d", strtotime($from)));
        }
        echo json_encode($payments);
    }
    function payment_charges($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case "Add":
                $data['charges'] = array(
                    'id_charges' => NULL,
                    'payment_mode' => NULL,
                    'code' => NULL,
                    'service_tax' => '0.00',
                    'lower_limit' => NULL,
                    'upper_limit' => NULL,
                    'charge_type' => NULL,
                    'charges_value' => NULL,
                    'active' => 1,
                    'type' => "Add"
                );
                $data['main_content'] = self::MAS_VIEW . 'payment/form';
                $this->load->view('layout/template', $data);
                break;
            case "Save":
                $charges = $this->input->post('charges');
                $data['charges'] = array(
                    'id_charges' => (isset($charges['id_charges']) ? $charges['id_charges'] : NULL),
                    'payment_mode' => (isset($charges['payment_mode']) ? $charges['payment_mode'] : NULL),
                    'code' => (isset($charges['code']) ? $charges['code'] : NULL),
                    'service_tax' => (isset($charges['service_tax']) ? $charges['service_tax'] : '0.00'),
                    'active' => (isset($charges['active']) ? $charges['active'] : 0)
                );
                $status = $this->$model->insert_charges($data['charges'], $charges['range']);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Charges detail updated successfully..', 'class' => 'success', 'title' => 'Add Charges'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Add Charges'));
                }
                redirect('settings/payment_charges');
                break;
            case "Edit":
                $charges = $this->$model->get_charges($id);
                $data['charges'] = array(
                    'id_charges' => (isset($charges['id_charges']) ? $charges['id_charges'] : NULL),
                    'payment_mode' => (isset($charges['payment_mode']) ? $charges['payment_mode'] : NULL),
                    'code' => (isset($charges['code']) ? $charges['code'] : NULL),
                    'service_tax' => (isset($charges['service_tax']) ? $charges['service_tax'] : '0.00'),
                    'active' => (isset($charges['active']) ? $charges['active'] : 0),
                    'type' => "Edit"
                );
                $data['main_content'] = self::MAS_VIEW . 'payment/form';
                $this->load->view('layout/template', $data);
                break;
            case "Update":
                $charges = $this->input->post('charges');
                $data['charges'] = array(
                    'payment_mode' => (isset($charges['payment_mode']) ? $charges['payment_mode'] : NULL),
                    'code' => (isset($charges['code']) ? $charges['code'] : NULL),
                    'service_tax' => (isset($charges['service_tax']) ? $charges['service_tax'] : '0.00'),
                    'active' => (isset($charges['active']) ? $charges['active'] : 0)
                );
                $status = $this->$model->update_charges($data['charges'], $charges['range'], $id);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Charges detail updated successfully..', 'class' => 'success', 'title' => 'Edit Charges'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Edit Charges'));
                }
                redirect('settings/payment_charges');
                break;
            case "Delete":
                $status = $this->$model->delete_charges($id);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Charges detail deleted successfully..', 'class' => 'success', 'title' => 'Delete Charges'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Delete Charges'));
                }
                redirect('settings/payment_charges');
                break;
            case "Ajax_charges":
                $list = $this->$model->ajax_get_charges();
                echo $list;
                break;
            default:
                $data['main_content'] = self::MAS_VIEW . 'payment/list';
                $this->load->view('layout/template', $data);
                break;
        }
    }
    function getCustomerByMobile($mobile)
    {
        $model = self::CUS_MODEL;
        $this->load->model($model);
        $id_customer = $this->$model->getCustomerByMobile($mobile);
        return ($id_customer ? $id_customer : 0);
    }
    function getSchemeAccountByCustomer($id)
    {
        $model = self::ACC_MODEL;
        $this->load->model($model);
        $id_scheme_account = $this->$model->getSchemeAccountByCustomerID($id);
        return $id_scheme_account;
    }
    function is_refno_exists($ref_no)
    {
        $model = self::ACC_MODEL;
        $this->load->model($model);
        return $this->$model->is_refno_exists($ref_no);
    }
    function general_settings($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        $cus_model = self::CUS_MODEL;
        $sch_model = self::SCH_MODEL;
        $sch_acc_model = self::ACC_MODEL;
        $log_model = self::LOG_MODEL;
        switch ($type) {
            case 'List': //showing list
                $data['settings'] = $this->$model->settingsDB('get', ($id != NULL ? $id : ''));
                $data['config'] = $this->$model->configDB('get', ($id != NULL ? $id : ''));
                $data['main_content'] = self::VIEW_FOLDER . "general/list";
                $this->load->view('layout/template', $data);
                break;
            case 'View':
                if ($id != NULL) {
                    $data['general'] = $this->$model->settingsDB('get', $id);
                    $data['limit'] = $this->$model->limitDB('get', $id);
                    $data['discount'] = $this->$model->discount_db('get', $id);
                    $data['demo'] = $this->$model->gateway_settingsDB('get_id', 1);
                    $data['pro'] = $this->$model->gateway_settingsDB('get_id', 2);
                    $data['demo_hdfc'] = $this->$model->gateway_settingsDB('get_id', 3);
                    $data['pro_hdfc'] = $this->$model->gateway_settingsDB('get_id', 4);
                    $data['demo_tech'] = $this->$model->gateway_settingsDB('get_id', 5);
                    $data['pro_tech'] = $this->$model->gateway_settingsDB('get_id', 6);
                    $data['promotion_crt'] = $this->$model->promotion_crt_settings('get', 1);
                    $data['promotion'] = $this->$model->promotion_crt_settings('get', 1);
                    $data['otp_crt'] = $this->$model->otp_crt_settings('get', 1);
                    $data['sms'] = $this->$model->sms_apiDB('get', 1);
                    $data['mail'] = $this->$model->get_company();
                    $data['comp'] = $this->$model->get_curr_detail($data['mail']['id_country']);
                    $data['co_default'] = $this->$model->get_default_country();
                    $data['ci_default'] = $this->$model->get_default_city();
                    $data['st_default'] = $this->$model->get_default_state();
                    $data['cus_count'] = $this->$cus_model->customer_count();
                    $data['scheme_count'] = $this->$sch_model->scheme_count();
                    $data['sch_acc_count'] = $this->$sch_acc_model->sch_acc_count();
                    $data['config'] = $this->$model->configDB('get', $id); //line added by durga 30/12/2022
                } else {
                    $data['general'] = $this->$model->settingsDB();
                    $data['limit'] = $this->$model->limitDB();
                    $data['discount'] = $this->$model->discount_db();
                    $data['config'] = $this->$model->configDB('get', $id); //line added by durga 30/12/2022
                }
                //Added by Durga acconut number , receipt number customization starts here 26-06-2023
                $acc_num_fields = array(
                    array(
                        'value' => 'br_code',
                        'text' => 'Branch Code(BB)'
                    ),
                    array(
                        'value' => 'cmp_code',
                        'text' => 'Company Code(CP)'
                    ),
                    array(
                        'value' => 'sch_code',
                        'text' => 'Scheme Code(SSS)'
                    ),
                    array(
                        'value' => 'grp_code',
                        'text' => 'Group Code(GG)'
                    ),
                    array(
                        'value' => 'fin_yr',
                        'text' => 'Financial Year(23)'
                    ),
                    array(
                        'value' => 'acc_num',
                        'text' => 'Scheme Account Number(122)'
                    ),
                    array(
                        'value' => 'hyphen',
                        'text' => 'Hyphen(-)'
                    ),
                    array(
                        'value' => 'space',
                        'text' => 'Space'
                    ),
                );
                $receipt_num_fields = array(
                    array(
                        'value' => 'br_code',
                        'text' => 'Branch Code(BB)'
                    ),
                    array(
                        'value' => 'cmp_code',
                        'text' => 'Company Code(CP)'
                    ),
                    array(
                        'value' => 'sch_code',
                        'text' => 'Scheme Code(SSS)'
                    ),
                    array(
                        'value' => 'grp_code',
                        'text' => 'Group Code(GG)'
                    ),
                    array(
                        'value' => 'rcpt_yr',
                        'text' => 'Receipt Year(23)'
                    ),
                    array(
                        'value' => 'rcpt_num',
                        'text' => 'Receipt Number(232)'
                    ),
                    array(
                        'value' => 'hyphen',
                        'text' => 'Hyphen(-)'
                    ),
                    array(
                        'value' => 'space',
                        'text' => 'Space'
                    ),
                );
                $data['acc_num_fields'] = [];
                $data['selected_acc_num_fields'] = [];
                $acc_rcpt_settingsfromdb = $this->customer_model->getFormatFromDB();
                $receipt_details = $this->get_receipt_format_details($receipt_num_fields, $acc_rcpt_settingsfromdb);
                $data['receipt_num_fields'] = $receipt_details['receipt_num_fields'];
                $data['selected_receipt_num_fields'] = $receipt_details['selected_receipt_num_fields'];
                $account_details = $this->get_acc_format_details($acc_num_fields, $acc_rcpt_settingsfromdb);
                $data['acc_num_fields'] = $account_details['acc_num_fields'];
                $data['selected_acc_num_fields'] = $account_details['selected_acc_num_fields'];
                //echo "<pre>";print_r($data['selected_acc_num_fields']);exit;
                //Added by Durga acconut number , receipt number customization ends here 26-06-2023

                // --- Load KYC Settings for edit ---
                $kyc_data = $this->admin_settings_model->get_kyc_settings();
                $kyc_rules_arr = $this->admin_settings_model->get_kyc_rules();

                if ($kyc_data['kyc_mode'] == 0) {
                    // Scheme Wise: group rules by scheme+when+type+amount, collect kyc_docs
                    $scheme_rules_map = array();
                    foreach ($kyc_rules_arr as $r) {
                        $key = $r['id_scheme'] . '_' . $r['rules'] . '_' . $r['type'] . '_' . $r['amount'];
                        if (!isset($scheme_rules_map[$key])) {
                            $scheme_rules_map[$key] = array(
                                'scheme' => $r['id_scheme'],
                                'kyc_docs' => array(),
                                'when' => $r['rules'],
                                'type' => $r['type'],
                                'amount' => $r['amount'],
                            );
                        }
                        if ($r['id_mas_kyc'] != null) {
                            $scheme_rules_map[$key]['kyc_docs'][] = $r['id_mas_kyc'];
                        }
                    }
                    $kyc_data['kyc_scheme_rules'] = json_encode(array_values($scheme_rules_map));
                    $kyc_data['kyc_customer_rules'] = '[]';
                } else {
                    // Customer Wise
                    $cust_rules = array();
                    foreach ($kyc_rules_arr as $r) {
                        $cust_rules[] = array(
                            'attribute' => $r['id_mas_kyc'],
                            'when' => $r['rules'],
                            'amount' => $r['amount'],
                        );
                    }
                    $kyc_data['kyc_customer_rules'] = json_encode($cust_rules);
                    $kyc_data['kyc_scheme_rules'] = '[]';
                }

                $data['kyc'] = $kyc_data;
                // --- End KYC Settings ---

                $data['userType'] = $this->session->userdata('profile');
                $data['main_content'] = self::VIEW_FOLDER . 'general/form';
                $this->load->view('layout/template', $data);
                break;
            case 'Save':
                $general = $this->input->post('general');
                $acc_format = '';
                $receipt_format = '';
                $acc_num_format_values = json_decode($general['schemeaccNo_format'], true);
                foreach ($acc_num_format_values as $key => $val) {
                    $acc_format .= $val['value'];
                }
                $receipt_num_format_values = json_decode($general['receiptNo_format'], true);
                foreach ($receipt_num_format_values as $key => $val) {
                    $receipt_format .= $val['value'];
                }
                $gen_info = array(
                    /*'currency_name'    	 => (isset($general['currency_name'])?$general['currency_name']:NULL),
                    'currency_symbol'    	 => (isset($general['currency_symbol'])?$general['currency_symbol']:NULL),*/
                    'edit_addpay_page' => (isset($general['edit_addpay_page']) ? $general['edit_addpay_page'] : 0),
                    'custom_entry_date' => (isset($general['custom_entry_date']) ? $general['custom_entry_date'] : NULL),
                    'edit_custom_entry_date' => (isset($general['edit_custom_entry_date']) ? $general['edit_custom_entry_date'] : 0),
                    'scheme_wise_receipt' => (isset($general['scheme_wise_receipt']) ? $general['scheme_wise_receipt'] : 0),
                    'scheme_wise_acc_no' => (isset($general['scheme_wise_acc_no']) ? $general['scheme_wise_acc_no'] : 0),
                    'allow_join_multiple' => (isset($general['allow_join_multiple']) ? $general['allow_join_multiple'] : 0),
                    'regExistingReqOtp' => (isset($general['regExistingReqOtp']) ? $general['regExistingReqOtp'] : 0),
                    'allow_join_unpaid' => (isset($general['allow_join_unpaid']) ? $general['allow_join_unpaid'] : 0),
                    'delete_unpaid' => (isset($general['delete_unpaid']) ? $general['delete_unpaid'] : 0),
                    'rate_update' => (isset($general['rate_update']) ? $general['rate_update'] : 0),
                    'maintenance_mode' => (isset($general['maintenance_mode']) ? $general['maintenance_mode'] : 0),
                    'maintenance_text' => (isset($general['maintenance_text']) ? $general['maintenance_text'] : null),
                    'reg_existing' => (isset($general['reg_existing']) ? $general['reg_existing'] : 0),
                    'newSchjoinonline' => (isset($general['newSchjoinonline']) ? $general['newSchjoinonline'] : 0),
                    'branchwise_scheme' => (isset($general['branchwise_scheme']) ? $general['branchwise_scheme'] : 0),
                    'sch_limit' => (isset($general['sch_limit']) ? $general['sch_limit'] : 0),
                    'show_closed_list' => (isset($general['show_closed_list']) ? $general['show_closed_list'] : 0),
                    'enableGoldrateDisc' => (isset($general['enableGoldrateDisc']) ? $general['enableGoldrateDisc'] : 0),
                    'goldDiscAmt' => (isset($general['goldDiscAmt']) ? $general['goldDiscAmt'] : 0),
                    'enableSilver_rateDisc' => (isset($general['enableSilver_rateDisc']) ? $general['enableSilver_rateDisc'] : 0),
                    'silverDiscAmt' => (isset($general['silverDiscAmt']) ? $general['silverDiscAmt'] : 0),
                    'enableGoldrateDisc_18k' => (isset($general['enableGoldrateDisc_18k']) ? $general['enableGoldrateDisc_18k'] : 0),
                    'goldDiscAmt_18k' => (isset($general['goldDiscAmt_18k']) ? $general['goldDiscAmt_18k'] : 0),
                    'gst_setting' => (isset($general['gst_setting']) ? $general['gst_setting'] : 0),
                    'enable_closing_otp' => (isset($general['enable_closing_otp']) ? $general['enable_closing_otp'] : 0),
                    'has_lucky_draw' => (isset($general['has_lucky_draw']) ? $general['has_lucky_draw'] : 0),
                    'receipt_no_set' => (isset($general['receipt_no_set']) ? $general['receipt_no_set'] : 0),
                    'cost_center' => (isset($general['cost_center']) ? $general['cost_center'] : 0),
                    'receipt' => (isset($general['receipt']) ? $general['receipt'] : 0),
                    'schemeacc_no_set' => (isset($general['schemeacc_no_set']) ? $general['schemeacc_no_set'] : 0),
                    'allow_savecard' => (isset($general['allow_savecard']) ? $general['allow_savecard'] : 0),
                    'allow_catlog' => (isset($general['allow_catlog']) ? $general['allow_catlog'] : 0),
                    'isOTPReqToLogin' => (isset($general['isOTPReqToLogin']) ? $general['isOTPReqToLogin'] : 0),
                    'isOTPRegForPayment' => (isset($general['isOTPRegForPayment']) ? $general['isOTPRegForPayment'] : 0),
                    'payOTP_exp' => (isset($general['payOTP_exp']) ? $general['payOTP_exp'] : 0),
                    'loginOTP_exp' => (isset($general['loginOTP_exp']) ? $general['loginOTP_exp'] : 0),
                    'req_otp_login' => (isset($general['req_otp_login']) ? $general['req_otp_login'] : 0),
                    'enable_dth' => (isset($general['enable_dth']) ? $general['enable_dth'] : 0),
                    'enable_coin_enq' => (isset($general['enable_coin_enq']) ? $general['enable_coin_enq'] : 0),
                    'gent_clientid' => (isset($general['gent_clientid']) ? $general['gent_clientid'] : 0),
                    'cusName_edit' => (isset($general['cusName_edit']) ? $general['cusName_edit'] : 0),
                    'req_gift_issue_otp' => (isset($general['req_gift_issue_otp']) ? $general['req_gift_issue_otp'] : 0),
                    'req_prize_issue_otp' => (isset($general['req_prize_issue_otp']) ? $general['req_prize_issue_otp'] : 0),
                    'metal_wgt_decimal' => (isset($general['metal_wgt_decimal']) ? $general['metal_wgt_decimal'] : NULL),
                    'vs_enable' => (isset($general['vs_enable']) ? $general['vs_enable'] : 0),
                    'metal_wgt_roundoff' => (isset($general['metal_wgt_roundoff']) ? $general['metal_wgt_roundoff'] : 0),
                    'enable_coin_book' => (isset($general['enable_coin_book']) ? $general['enable_coin_book'] : 0),
                    'auto_debit' => (isset($general['auto_debit']) ? $general['auto_debit'] : 0),
                    'auto_debit_allow_app_pay' => (isset($general['auto_debit_allow_app_pay']) ? $general['auto_debit_allow_app_pay'] : 0),
                    'isOTPReqToGift' => (isset($general['isOTPReqToGift']) ? $general['isOTPReqToGift'] : 0),
                    'giftOTP_exp' => (isset($general['giftOTP_exp']) ? $general['giftOTP_exp'] : 0),
                    'schemeaccNo_displayFrmt' => (isset($general['schemeaccNo_displayFrmt']) ? $general['schemeaccNo_displayFrmt'] : 0),
                    'receiptNo_displayFrmt' => (isset($general['receiptNo_displayFrmt']) ? $general['receiptNo_displayFrmt'] : 0),
                    /* 'enable_digi_gold' => (isset($general['enable_digi_gold']) ? $general['enable_digi_gold'] : 0),
                    'show_video_shop' => (isset($general['show_video_shop']) ? $general['show_video_shop'] : 0),
                    'show_customer_order' => (isset($general['show_customer_order']) ? $general['show_customer_order'] : 0), */
                    'custom_AccDisplayFrmt' => (isset($general['schemeaccNo_displayFrmt']) ? ($general['schemeaccNo_displayFrmt'] == 2 && $acc_format != '' ? $acc_format : NULL) : NULL),
                    'custom_ReceiptDisplayFrmt' => (isset($general['receiptNo_displayFrmt']) ? ($general['receiptNo_displayFrmt'] == 2 && $receipt_format != '' ? $receipt_format : NULL) : NULL),
                    'chitCollectionEmpCount' => (isset($general['chitCollectionEmpCount']) ? $general['chitCollectionEmpCount'] : 0),
                    'restrict_lastPayment_days' => (isset($general['restrict_lastPayment_days']) ? $general['restrict_lastPayment_days'] : 0),
                    'employee_allocation' => (isset($general['employee_allocation']) ? $general['employee_allocation'] : 0)
                );
                $status = $this->$model->settingsDB('insert', $id, $gen_info);
                //added by durga 30/12/2022 starts here 
                $config = $this->input->post('config');
                $config_dat = array(
                    // 'auto_pay_approval'=>(isset($config['auto_pay_approval'])?$config['auto_pay_approval']:0),
                    // 'integration_type'=>(isset($config['integration_type'])?$config['integration_type']:0),
                    // 'auto_sync'=>(isset($config['auto_sync'])?$config['auto_sync']:0),
                    // 'one_signal_app_id'=>(isset($config['one_signal_app_id'])?$config['one_signal_app_id']:NULL),
                    // 'one_signal_auth_key'=>(isset($config['one_signal_auth_key'])?$config['one_signal_auth_key']:NULL),
                    // 'whats_app_url'=>(isset($config['whats_app_url'])?$config['whats_app_url']:NULL),
                    // 'instance_id'=>(isset($config['instance_id'])?$config['instance_id']:NULL),
                    'app_cus_email' => (isset($config['app_cus_email']) ? $config['app_cus_email'] : 0),
                    'app_cus_address1' => (isset($config['app_cus_address1']) ? $config['app_cus_address1'] : 0),
                    'app_cus_address2' => (isset($config['app_cus_address2']) ? $config['app_cus_address2'] : 0),
                    'app_cus_country' => (isset($config['app_cus_country']) ? $config['app_cus_country'] : 0),
                    'app_cus_state' => (isset($config['app_cus_state']) ? $config['app_cus_state'] : 0),
                    'app_cus_city' => (isset($config['app_cus_city']) ? $config['app_cus_city'] : 0),
                    'app_cus_lastname' => (isset($config['app_cus_lastname']) ? $config['app_cus_lastname'] : 0),
                    // 'zoop_enabled'=>(isset($config['zoop_enabled'])?$config['zoop_enabled']:0),
                    // 'zoop_url'=>(isset($config['zoop_url'])?$config['zoop_url']:NULL),
                    // 'zoop_agency_id'=>(isset($config['zoop_agency_id'])?$config['zoop_agency_id']:NULL),
                    // 'zoop_api_key'=>(isset($config['zoop_api_key'])?$config['zoop_api_key']:NULL),
                    // 'khimji_baseURL'=>(isset($config['khimji_baseURL'])?$config['khimji_baseURL']:NULL),
                    // 'khimji_x_key'=>(isset($config['khimji_x_key'])?$config['khimji_x_key']:NULL),
                    // 'khimji_auth'=>(isset($config['khimji_auth'])?$config['khimji_auth']:NULL),
                    // 'sms_gateway'=>(isset($config['sms_gateway'])?$config['sms_gateway']:0),
                    // 'show_gcode'=>(isset($config['show_gcode'])?$config['show_gcode']:0),
                    // 'clt_id_code'=>(isset($config['clt_id_code'])?$config['clt_id_code']:NULL),
                    'play_str_url' => (isset($config['play_str_url']) ? $config['play_str_url'] : NULL),
                    'app_a_pack' => (isset($config['app_a_pack']) ? $config['app_a_pack'] : NULL),
                    'app_i_pack' => (isset($config['app_i_pack']) ? $config['app_i_pack'] : NULL),
                    // 'erp_base_url'=>(isset($config['erp_base_url'])?$config['erp_base_url']:NULL),
                    // 'ej_usr_nm'=>(isset($config['ej_usr_nm'])?$config['ej_usr_nm']:NULL),
                    // 'ej_pwd'=>(isset($config['ej_pwd'])?$config['ej_pwd']:NULL),
                    'current_android_version' => (isset($config['current_android_version']) ? $config['current_android_version'] : NULL),
                    'new_android_version' => (isset($config['new_android_version']) ? $config['new_android_version'] : NULL),
                    'current_ios_version' => (isset($config['current_ios_version']) ? $config['current_ios_version'] : NULL),
                    'new_ios_version' => (isset($config['new_ios_version']) ? $config['new_ios_version'] : NULL),
                );
                // $status_config = $this->$model->configDB('insert', $id, $config_dat);
                //added by durga 30/12/2022 ends here 
                //	print_r($gen_info[])
                $log_data = array(                                                                //general log details
                    'id_log' => $this->id_log,
                    'event_date' => date("Y-m-d H:i:s"),
                    'module' => 'General Settings',
                    'operation' => 'Add',
                    'record' => $general[tab_name],
                    'remark' => 'Settings edited successfully'
                );
                //print_r($log_data );exit;
                $this->$log_model->log_detail('insert', '', $log_data);
                //   if($status && $status_config)
			 if($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'General Settings added successfully', 'class' => 'success', 'title' => 'General Settings'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'General Settings'));
                }
                redirect('settings/general/edit/1');
                break;
            case 'Update':   // esakki 11-11
                $general = $this->input->post('general');
                // echo "<pre>";print_r($general);exit;
                //echo "<pre>";print_r($_POST);exit;
                $acc_format = '';
                $receipt_format = '';
                $acc_num_format_values = json_decode($general['schemeaccNo_format'], true);
                foreach ($acc_num_format_values as $key => $val) {
                    $acc_format .= $val['value'];
                }
                $receipt_num_format_values = json_decode($general['receiptNo_format'], true);
                foreach ($receipt_num_format_values as $key => $val) {
                    $receipt_format .= $val['value'];
                }
                $gen_info = array(
                    /*'currency_name'    	    => (isset($general['currency_name'])?$general['currency_name']:NULL),
                      'currency_symbol'     => (isset($general['currency_symbol'])?$general['currency_symbol']:NULL),*/
                    'edit_addpay_page' => (isset($general['edit_addpay_page']) ? $general['edit_addpay_page'] : 0),
                    'edit_custom_entry_date' => (isset($general['edit_custom_entry_date']) ? $general['edit_custom_entry_date'] : 0),
                    'custom_entry_date' => (isset($general['custom_entry_date']) ? $general['custom_entry_date'] : NULL),
                    'allow_join_multiple' => (isset($general['allow_join_multiple']) ? $general['allow_join_multiple'] : 0),
                    'scheme_wise_receipt' => (isset($general['scheme_wise_receipt']) ? $general['scheme_wise_receipt'] : 0),
                    'scheme_wise_acc_no' => (isset($general['scheme_wise_acc_no']) ? $general['scheme_wise_acc_no'] : 0),
                    'regExistingReqOtp' => (isset($general['regExistingReqOtp']) ? $general['regExistingReqOtp'] : 0),
                    'allow_join_unpaid' => (isset($general['allow_join_unpaid']) ? $general['allow_join_unpaid'] : 0),
                    'delete_unpaid' => (isset($general['delete_unpaid']) ? $general['delete_unpaid'] : 0),
                    'rate_update' => (isset($general['rate_update']) ? $general['rate_update'] : 0),
                    'maintenance_mode' => (isset($general['maintenance_mode']) ? $general['maintenance_mode'] : 0),
                    'maintenance_text' => (isset($general['maintenance_text']) ? $general['maintenance_text'] : null),
                    'reg_existing' => (isset($general['reg_existing']) ? $general['reg_existing'] : 0),
                    'newSchjoinonline' => (isset($general['newSchjoinonline']) ? $general['newSchjoinonline'] : 0),
                    'otp_scheme_join' => (isset($general['otp_scheme_join']) ? $general['otp_scheme_join'] : 0),
                    'branchwise_scheme' => (isset($general['branchwise_scheme']) ? $general['branchwise_scheme'] : 0),
                    'sch_limit' => (isset($general['sch_limit']) ? $general['sch_limit'] : 0),
                    'show_closed_list' => (isset($general['show_closed_list']) ? $general['show_closed_list'] : 0),
                    'gst_setting' => (isset($general['gst_setting']) ? $general['gst_setting'] : 0),
                    'enable_closing_otp' => (isset($general['enable_closing_otp']) ? $general['enable_closing_otp'] : 0),
                    'has_lucky_draw' => (isset($general['has_lucky_draw']) ? $general['has_lucky_draw'] : 0),
                    'receipt_no_set' => (isset($general['receipt_no_set']) ? $general['receipt_no_set'] : 0),
                    'receipt' => (isset($general['receipt']) ? $general['receipt'] : 0),
                    'schemeacc_no_set' => (isset($general['schemeacc_no_set']) ? $general['schemeacc_no_set'] : 0),
                    'cost_center' => (isset($general['cost_center']) ? $general['cost_center'] : 0),
                    'enableGoldrateDisc' => (isset($general['enableGoldrateDisc']) ? $general['enableGoldrateDisc'] : 0),
                    'goldDiscAmt' => (isset($general['goldDiscAmt']) ? $general['goldDiscAmt'] : 0),
                    'enableSilver_rateDisc' => (isset($general['enableSilver_rateDisc']) ? $general['enableSilver_rateDisc'] : 0),
                    'silverDiscAmt' => (isset($general['silverDiscAmt']) ? $general['silverDiscAmt'] : 0),
                    'enableGoldrateDisc_18k' => (isset($general['enableGoldrateDisc_18k']) ? $general['enableGoldrateDisc_18k'] : 0),
                    'goldDiscAmt_18k' => (isset($general['goldDiscAmt_18k']) ? $general['goldDiscAmt_18k'] : 0),
                    'allow_savecard' => (isset($general['allow_savecard']) ? $general['allow_savecard'] : 0),
                    'allow_catlog' => (isset($general['allow_catlog']) ? $general['allow_catlog'] : 0),
                    'isOTPReqToLogin' => (isset($general['isOTPReqToLogin']) ? $general['isOTPReqToLogin'] : 0),
                    'isOTPRegForPayment' => (isset($general['isOTPRegForPayment']) ? $general['isOTPRegForPayment'] : 0),
                    'payOTP_exp' => (isset($general['payOTP_exp']) ? $general['payOTP_exp'] : 0),
                    'loginOTP_exp' => (isset($general['loginOTP_exp']) ? $general['loginOTP_exp'] : 0),
                    'req_otp_login' => (isset($general['req_otp_login']) ? $general['req_otp_login'] : 0),
                    'enable_dth' => (isset($general['enable_dth']) ? $general['enable_dth'] : 0),
                    'enable_coin_enq' => (isset($general['enable_coin_enq']) ? $general['enable_coin_enq'] : 0),
                    'gent_clientid' => (isset($general['gent_clientid']) ? $general['gent_clientid'] : 0),
                    'cusName_edit' => (isset($general['cusName_edit']) ? $general['cusName_edit'] : 0),
                    'req_gift_issue_otp' => (isset($general['req_gift_issue_otp']) ? $general['req_gift_issue_otp'] : 0),
                    'req_prize_issue_otp' => (isset($general['req_prize_issue_otp']) ? $general['req_prize_issue_otp'] : 0),
                    'metal_wgt_decimal' => (isset($general['metal_wgt_decimal']) ? $general['metal_wgt_decimal'] : NULL),
                    'metal_wgt_roundoff' => (isset($general['metal_wgt_roundoff']) ? $general['metal_wgt_roundoff'] : 0),
                    'vs_enable' => (isset($general['vs_enable']) ? $general['vs_enable'] : 0),
                    'enable_coin_book' => (isset($general['enable_coin_book']) ? $general['enable_coin_book'] : 0),
                    'auto_debit' => (isset($general['auto_debit']) ? $general['auto_debit'] : 0),
                    'auto_debit_allow_app_pay' => (isset($general['auto_debit_allow_app_pay']) ? $general['auto_debit_allow_app_pay'] : 0),
                    'isOTPReqToGift' => (isset($general['isOTPReqToGift']) ? $general['isOTPReqToGift'] : 0),
                    'giftOTP_exp' => (isset($general['giftOTP_exp']) ? $general['giftOTP_exp'] : 0),
                    'schemeaccNo_displayFrmt' => (isset($general['schemeaccNo_displayFrmt']) ? $general['schemeaccNo_displayFrmt'] : 0),
                    'receiptNo_displayFrmt' => (isset($general['receiptNo_displayFrmt']) ? $general['receiptNo_displayFrmt'] : 0),
                    /* 'enable_digi_gold' => (isset($general['enable_digi_gold']) ? $general['enable_digi_gold'] : 0),
                    'show_video_shop' => (isset($general['show_video_shop']) ? $general['show_video_shop'] : 0),
                    'show_customer_order' => (isset($general['show_customer_order']) ? $general['show_customer_order'] : 0), */
                    'custom_AccDisplayFrmt' => (isset($general['schemeaccNo_displayFrmt']) ? ($general['schemeaccNo_displayFrmt'] == 2 && $acc_format != '' ? $acc_format : NULL) : NULL),
                    'custom_ReceiptDisplayFrmt' => (isset($general['receiptNo_displayFrmt']) ? ($general['receiptNo_displayFrmt'] == 2 && $receipt_format != '' ? $receipt_format : NULL) : NULL),
                    'chitCollectionEmpCount' => (isset($general['chitCollectionEmpCount']) ? $general['chitCollectionEmpCount'] : 0),
                    'restrict_lastPayment_days' => (isset($general['restrict_lastPayment_days']) ? $general['restrict_lastPayment_days'] : 0),
                    'employee_allocation' => (isset($general['employee_allocation']) ? $general['employee_allocation'] : 0),
                    'vs_booking_time' => (isset($general['fn_from']) ? (isset($general['an_to']) ? $general['fn_from'] . '-' . $general['an_to'] : '') : 0),
                    'show_kyc_optional' => (isset($general['show_kyc_optional']) ? $general['show_kyc_optional'] : 0),
                    'pan_req_amt' => (isset($general['pan_req_amt']) ? $general['pan_req_amt'] : 0),
                    'pan_required_by' => (isset($general['pan_required_by']) ? $general['pan_required_by'] : 0),
                    'block_kyc_by' => (isset($general['block_kyc_by']) ? $general['block_kyc_by'] : 0),
                );
                $status = $this->$model->settingsDB('update', $id, $gen_info);
                //added by durga 30/12/2022 starts here 
                $config = $this->input->post('config');
                //print_r($config);exit;
                $config_dat = array(
                    // 'auto_pay_approval'=>(isset($config['auto_pay_approval'])?$config['auto_pay_approval']:0),
                    // 'integration_type'=>(isset($config['integration_type'])?$config['integration_type']:0),
                    // 'auto_sync'=>(isset($config['auto_sync'])?$config['auto_sync']:0),
                    // 'one_signal_app_id'=>(isset($config['one_signal_app_id'])?$config['one_signal_app_id']:NULL),
                    // 'one_signal_auth_key'=>(isset($config['one_signal_auth_key'])?$config['one_signal_auth_key']:NULL),
                    // 'whats_app_url'=>(isset($config['whats_app_url'])?$config['whats_app_url']:NULL),
                    // 'instance_id'=>(isset($config['instance_id'])?$config['instance_id']:NULL),
                    'app_cus_email' => (isset($config['app_cus_email']) ? $config['app_cus_email'] : 0),
                    'app_cus_address1' => (isset($config['app_cus_address1']) ? $config['app_cus_address1'] : 0),
                    'app_cus_address2' => (isset($config['app_cus_address2']) ? $config['app_cus_address2'] : 0),
                    'app_cus_country' => (isset($config['app_cus_country']) ? $config['app_cus_country'] : 0),
                    'app_cus_state' => (isset($config['app_cus_state']) ? $config['app_cus_state'] : 0),
                    'app_cus_city' => (isset($config['app_cus_city']) ? $config['app_cus_city'] : 0),
                    'app_cus_lastname' => (isset($config['app_cus_lastname']) ? $config['app_cus_lastname'] : 0),
                    // 'zoop_enabled'=>(isset($config['zoop_enabled'])?$config['zoop_enabled']:0),
                    // 'zoop_url'=>(isset($config['zoop_url'])?$config['zoop_url']:NULL),
                    // 'zoop_agency_id'=>(isset($config['zoop_agency_id'])?$config['zoop_agency_id']:NULL),
                    // 'zoop_api_key'=>(isset($config['zoop_api_key'])?$config['zoop_api_key']:NULL),
                    // 'khimji_baseURL'=>(isset($config['khimji_baseURL'])?$config['khimji_baseURL']:NULL),
                    // 'khimji_x_key'=>(isset($config['khimji_x_key'])?$config['khimji_x_key']:NULL),
                    // 'khimji_auth'=>(isset($config['khimji_auth'])?$config['khimji_auth']:NULL),
                    // 'sms_gateway'=>(isset($config['sms_gateway'])?$config['sms_gateway']:0),
                    // 'show_gcode'=>(isset($config['show_gcode'])?$config['show_gcode']:0),
                    // 'clt_id_code'=>(isset($config['clt_id_code'])?$config['clt_id_code']:NULL),
                    'play_str_url' => (!empty($config['play_str_url']) ? $config['play_str_url'] : NULL),
                    'app_a_pack' => (!empty($config['app_a_pack']) ? $config['app_a_pack'] : NULL),
                    'app_i_pack' => (!empty($config['app_i_pack']) ? $config['app_i_pack'] : NULL),
                    // 'erp_base_url'=>(isset($config['erp_base_url'])?$config['erp_base_url']:NULL),
                    // 'ej_usr_nm'=>(isset($config['ej_usr_nm'])?$config['ej_usr_nm']:NULL),
                    // 'ej_pwd'=>(isset($config['ej_pwd'])?$config['ej_pwd']:NULL),
                    'current_android_version' => (!empty($config['current_android_version']) ? $config['current_android_version'] : NULL),
                    'new_android_version' => (!empty($config['new_android_version']) ? $config['new_android_version'] : NULL),
                    'current_ios_version' => (!empty($config['current_ios_version']) ? $config['current_ios_version'] : NULL),
                    'new_ios_version' => (!empty($config['new_ios_version']) ? $config['new_ios_version'] : NULL),
                );
                // $status_config = $this->$model->configDB('update', $id, $config_dat);
                $default = $this->input->post('default');
                $country_info = array(
                    'id_country' => (isset($default['id_country']) ? $default['id_country'] : NULL),
                    'id_state' => (isset($default['id_state']) ? $default['id_state'] : NULL),
                    'id_city' => (isset($default['id_city']) ? $default['id_city'] : NULL),
                );
                $default_stautus = $this->$model->update_default_country($country_info);
                //added by durga 30/12/2022 ends here
                $log_data = array(                                                                //general log details
                    'id_log' => $this->id_log,
                    'event_date' => date("Y-m-d H:i:s"),
                    'module' => 'General Settings',
                    'operation' => 'update',
                    'record' => $general[tab_name],
                    'remark' => 'Settings update successfully'
                );
                //print_r($log_data );exit;
                $this->$log_model->log_detail('insert', '', $log_data);
                //   if($status && $status_config)
			 if($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => ' General Settings edited successfully', 'class' => 'success', 'title' => 'General Settings'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'General Settings'));
                }
                redirect('settings/general/edit/1');
                break;
        }
    }
    // function otp_settings($id)
    // 	{	
    // 		$model=self::SET_MODEL;
    //    	 	$general = $this->input->post('general');
    // 		print_r($general);
    // 		$data = array(
    // 						'enable_closing_otp'	  => $general['enable_closing_otp']
    // 					);
    // 		$update =	$this->$model->settingsDB('update',$id,$data);	
    // 		if($update)  {	    
    // 			$this->session->set_flashdata('chit_alert', array('message' => 'General Settings edited successfully','class' => 'success','title'=>'General Settings'));
    // 		}
    // 		else{
    // 			  $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'General Settings'));
    // 		}
    // 		redirect('settings/general/edit/1');
    // 	//	 break;
    //    	 	}
    function get_otpsettings()
    {
        $model = self::SET_MODEL;
        $update = $this->$model->get_datas();
        echo json_encode($update);
    }
    function clear_database()
    {
        $model = self::SET_MODEL;
        $log_model = self::LOG_MODEL;
        $clear_by = $_POST;
        $truncate = array(
            'masters' => array('bank', 'department', 'designation', 'drawee_account', 'import_log', 'metal_rates', 'payment_mode'),
            'customer' => array('customer', 'address', 'registered_devices'),
            'scheme' => array('scheme', 'gst_splitup_detail', 'scheme_group', 'scheme_branch', 'scheme_benefit_deduct_settings'),
            'account' => array('scheme_account', 'payment', 'postdate_payment', 'payment_status', 'pending_payment', 'settlement', 'settlement_detail', 'scheme_reg_request'),
            'wallet' => array('wallet_account', 'wallet_transaction', 'wallet_settings'),
            'log' => array('log', 'log_detail'),
            'promotions' => array('offers', 'new_arrivals'),
            'daily_collection' => array('daily_collection'),
            'metal_rates' => array('metal_rates'),
            'access' => array('access')
        );
        $sql = "SET FOREIGN_KEY_CHECKS=0";
        $this->$model->executeQry($sql);
        if ($clear_by['mode'] == 1) {
            foreach ($truncate as $selected) {
                $this->truncateFromArray($selected);
                if (is_dir(self::CUS_FOLDER)) {
                    $this->rrmdir(self::CUS_IMG_PATH);
                }
                if (is_dir('assets/img/offers')) {
                    $this->rrmdir('assets/img/offers/');
                }
                if (is_dir('assets/img/new_arrivals')) {
                    $this->rrmdir('assets/img/new_arrivals/');
                }
            }
        } elseif ($clear_by['mode'] == 0) {
            foreach ($clear_by['selected'] as $selected) {
                $this->truncateFromArray($truncate[$selected]);
                if ($selected == 'customer') {
                    if (is_dir(self::CUS_FOLDER)) {
                        $this->rrmdir(self::CUS_IMG_PATH);
                    }
                }
                if ($selected == 'promotions') {
                    if (is_dir('assets/img/offers')) {
                        $this->rrmdir('assets/img/offers/');
                    }
                    if (is_dir('assets/img/new_arrivals')) {
                        $this->rrmdir('assets/img/new_arrivals/');
                    }
                }
            }
        }
        $sql = "SET FOREIGN_KEY_CHECKS=1";
        $this->$model->executeQry($sql);
        $log_data = array(
            'id_log' => $this->id_log,
            'event_date' => date("Y-m-d H:i:s"),
            'module' => 'settings-clear database',
            'operation' => 'Delete',
            'record' => 'DB cleared',
            'remark' => 'General setting updated successfully',
        );
        $this->$log_model->log_detail('insert', '', $log_data);
        echo "Cleared " . ($clear_by['mode'] == 0 ? 'selected' : 'all') . " tables";
    }
    function truncateFromArray($tables)
    {
        $model = self::SET_MODEL;
        foreach ($tables as $table) {
            $sql = "Truncate " . $table;
            $this->$model->executeQry($sql);
        }
    }
    //to Delete directory
    function rrmdir($path)
    {
        // Open the source directory to read in files
        $i = new DirectoryIterator($path);
        foreach ($i as $f) {
            if ($f->isFile()) {
                unlink($f->getRealPath());
            } else if (!$f->isDot() && $f->isDir()) {
                $this->rrmdir($f->getRealPath());
            }
        }
        rmdir($path);
    }
    function db_backup()
    {
        date_default_timezone_set('Asia/Calcutta');
        $model = self::SET_MODEL;
        $path = '../data/backup/';
        $filename = 'backup_' . date('d_m_Y_H_i_s');
        // Load the DB utility class 
        $this->load->dbutil();
        $prefs = array(
            'format' => 'zip', // gzip, zip, txt 
            'filename' => $filename . '.sql',
            // File name - NEEDED ONLY WITH ZIP FILES 
            'add_drop' => TRUE,
            // Whether to add DROP TABLE statements to backup file
            'add_insert' => TRUE,
            // Whether to add INSERT data to backup file 
            'newline' => "\n"
            // Newline character used in backup file 
        );
        // Backup your entire database and assign it to a variable 
        $backup = &$this->dbutil->backup($prefs);
        // Load the file helper and write the file to your server 
        $this->load->helper('file');
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }
        $write_status = write_file($path . $filename . '.zip', $backup);
        if ($write_status) {
            $db_data = array(
                'backup_date' => date('Y-m-d H:i:s'),
                'id_employee' => $this->id_employee,
                'filename' => $filename . '.zip'
            );
            $this->$model->database_backup('insert', '', $db_data);
        }
        // Load the download helper and send the file to your desktop 
        $this->load->helper('download');
        force_download($filename . '.zip', $backup);
    }
    function ajax_backup_list()
    {
        $model = self::SET_MODEL;
        $data = $this->$model->database_backup('get');
        echo json_encode($data);
    }
    function gateway_settings($type = "", $id = "", $array = "")
    {
        $model = self::SET_MODEL;
        $log_model = self::LOG_MODEL;
        switch ($type) {
            case 'Update_demo':
                $demo = $this->input->post('demo');
                $data = array(
                    'key' => $demo['key'],
                    'salt' => $demo['salt'],
                    'api_url' => $demo['api_url'],
                    'm_code' => $demo['m_code'],
                    'param_1' => $demo['param_1'],
                    'is_default' => $demo['is_default'],
                );
                $update = $this->$model->gateway_settingsDB('update', $id, $data);
                $log_data = array(                                                                //scheme log details
                    'id_log' => $this->id_log,
                    'event_date' => date("Y-m-d H:i:s"),
                    'module' => 'Settings - Gateway Settings',
                    'operation' => 'Edit',
                    'record' => 'Update demo',
                    'remark' => 'General Settings edited successfully'
                );
                if ($update['status']) {
                    $update = $this->$model->gateway_settingsDB('update', 2, array('is_default' => ($data['is_default'] == 1 ? 0 : 1)));
                    $this->session->set_flashdata('chit_alert', array('message' => 'General Settings edited successfully', 'class' => 'success', 'title' => 'General Settings'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'General Settings'));
                }
                redirect('settings/general/edit/1');
                break;
            case 'Update_pro':
                $pro = $this->input->post('pro');
                $data = array(
                    'key' => $pro['key'],
                    'salt' => $pro['salt'],
                    'api_url' => $pro['api_url'],
                    'm_code' => $pro['m_code'],
                    'is_default' => $pro['is_default']
                );
                $update = $this->$model->gateway_settingsDB('update', $id, $data);
                $log_data = array(                                                                //scheme log details
                    'id_log' => $this->id_log,
                    'event_date' => date("Y-m-d H:i:s"),
                    'module' => 'Settings - Gateway Settings',
                    'operation' => 'Edit',
                    'record' => 'Update_pro',
                    'remark' => 'General Settings edited successfully'
                );
                //print_r($log_data );exit;
                if ($update['status']) {
                    $update = $this->$model->gateway_settingsDB('update', 1, array('is_default' => ($data['is_default'] == 1 ? 0 : 1)));
                    $this->session->set_flashdata('chit_alert', array('message' => 'General Settings edited successfully', 'class' => 'success', 'title' => 'General Settings'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'General Settings'));
                }
                redirect('settings/general/edit/1');
                break;
        }
    }
    function sms_api_settings($type = "", $id = "", $array = "")
    {
        $model = self::SET_MODEL;
        $log_model = self::LOG_MODEL;
        switch ($type) {
            case 'Update':
                $sms = $this->input->post('sms');
                $data = array(
                    'sms_sender_id' => $sms['sms_sender_id'],
                    'sms_url' => $sms['sms_url']
                );
                $update = $this->$model->sms_apiDB('update', $id, $data);
                $log_data = array(                                                                //scheme log details
                    'id_log' => $this->id_log,
                    'event_date' => date("Y-m-d H:i:s"),
                    'module' => 'Settings- SMS API Settings',
                    'operation' => 'Edit',
                    'record' => 'SMS API Setting',
                    'remark' => 'General Settings edited successfully'
                );
                // print_r($log_data );exit;
                $this->$log_model->log_detail('insert', '', $log_data);
                if ($update['status']) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'General Settings edited successfully', 'class' => 'success', 'title' => 'General Settings'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'General Settings'));
                }
                redirect('settings/general/edit/1');
                break;
        }
    }
    function mail_settings($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        $log_model = self::LOG_MODEL;
        switch ($type) {
            case 'Update':
                $mail = $this->input->post('mail');
                $data = array(
                    'mail_server' => $mail['mail_server'],
                    'mail_password' => $mail['mail_password'],
                    'send_through' => $mail['send_through'],
                    'smtp_user ' => (isset($mail['smtp_user']) ? $mail['smtp_user'] : NULL),
                    'server_type' => (isset($mail['server_type']) ? $mail['server_type'] : 1),  // 1 --> default value  esakki 11-11
                    'smtp_pass' => (isset($mail['smtp_pass']) ? $mail['smtp_pass'] : NULL),
                    'smtp_host' => (isset($mail['smtp_host']) ? $mail['smtp_host'] : NULL)
                );
                $update = $this->$model->update_company($data, $id);
                $log_data = array(                                                                //scheme log details
                    'id_log' => $this->id_log,
                    'event_date' => date("Y-m-d H:i:s"),
                    'module' => 'Settings - Mail Settings',
                    'operation' => 'Edit',
                    'record' => 'Mail Settings',
                    'remark' => 'General Settings edited successfully'
                );
                // print_r($log_data );exit;
                $this->$log_model->log_detail('insert', '', $log_data);
                if ($update) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'General Settings edited successfully', 'class' => 'success', 'title' => 'General Settings'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'General Settings'));
                }
                redirect('settings/general/edit/1');
                break;
        }
    }
    /* -- Coded by ARVK -- */
    function limit_settings($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        $log_model = self::LOG_MODEL;
        switch ($type) {
            case 'Update':
                $limit = $this->input->post('limit');
                //print_r($limit);exit;
                $data = array(
                    'limit_cust' => (isset($limit['limit_cust']) ? $limit['limit_cust'] : 0),
                    'cust_max_count' => (isset($limit['cust_max_count']) ? $limit['cust_max_count'] : 0),
                    'limit_sch' => (isset($limit['limit_sch']) ? $limit['limit_sch'] : 0),
                    'sch_max_count' => (isset($limit['sch_max_count']) ? $limit['sch_max_count'] : 0),
                    'limit_branch' => (isset($limit['limit_branch']) ? $limit['limit_branch'] : 0),
                    'branch_max_count' => (isset($limit['branch_max_count']) ? $limit['branch_max_count'] : 0),
                    'limit_sch_acc' => (isset($limit['limit_sch_acc']) ? $limit['limit_sch_acc'] : 0),
                    'sch_acc_max_count' => (isset($limit['sch_acc_max_count']) ? $limit['sch_acc_max_count'] : 0)
                );
                $update = $this->$model->limitDB('update', $id, $data);
                $log_data = array(                                                                //scheme log details
                    'id_log' => $this->id_log,
                    'event_date' => date("Y-m-d H:i:s"),
                    'module' => 'Settings - Limit Settings',
                    'operation' => 'Edit',
                    'record' => 'Limit Settings',
                    'remark' => 'General Settings edited successfully'
                );
                // print_r($log_data );exit;
                $this->$log_model->log_detail('insert', '', $log_data);
                if ($update) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Limit Settings edited successfully', 'class' => 'success', 'title' => 'Limit Settings'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Limit Settings'));
                }
                redirect('settings/general/edit/1');
                break;
        }
    }
    function discount_settings($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        $log_model = self::LOG_MODEL;
        switch ($type) {
            case 'Update':
                $discount = $this->input->post('discount');
                //print_r($discount);exit;
                $data = array(
                    'free_first_payment' => (isset($discount['free_first_payment']) ? $discount['free_first_payment'] : 0)
                );
                $sch_free_payment = array(
                    'free_payment' => 0
                );
                $this->db->trans_begin();
                $update = $this->$model->discount_db('update', $id, $data);
                if ($discount['free_first_payment'] == 0) {
                    $sch_update = $this->scheme_model->update_scheme_free_payment($sch_free_payment);
                }
                $log_data = array(                                                                //scheme log details
                    'id_log' => $this->id_log,
                    'event_date' => date("Y-m-d H:i:s"),
                    'module' => 'Settings- Discount Settings',
                    'operation' => 'Edit',
                    'record' => 'Discount Settings',
                    'remark' => 'General Settings updated successfully'
                );
                // print_r($log_data );exit;
                $this->$log_model->log_detail('insert', '', $log_data);
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Discount Settings edited successfully', 'class' => 'success', 'title' => 'Discount Settings'));
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Discount Settings'));
                }
                redirect('settings/general/edit/1');
                break;
        }
    }
    /* -- / Coded by ARVK -- */
    function offers_form($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case "Add":
                $data['offer'] = $this->$model->offer_empty_record();
                $data['main_content'] = self::MAS_VIEW . 'offer/form';
                $this->load->view('layout/template', $data);
                break;
            case 'Save':
                $offer = $this->input->post('offer');
                $id_branch = $this->input->post("id_branch"); // based on the branch settings to showed branch filter//HH
                $offer_notification = isset($offer['offer_notification']) ? $offer['offer_notification'] : 0;
                $type = isset($offer['type']) ? $offer['type'] : NULL;
                $data = array(
                    'name' => (isset($offer['name']) ? $offer['name'] : 0),
                    'offer_content' => (isset($offer['offer_content']) ? $offer['offer_content'] : 0),
                    'active' => 1,
                    'offer_img_path' => (isset($offer['offer_img_path']) ? $offer['offer_img_path'] : ''),
                    'type' => (isset($offer['type']) ? $offer['type'] : ''),
                    'id_branch' => (isset($offer['id_branch']) ? $offer['id_branch'] : NULL),
                    'offer_notification' => $offer_notification,
                    'date_add' => date("Y-m-d H:i:s")
                );
                $this->db->trans_begin();
                $offer_id = $this->$model->insert_offer($data);
                if (isset($_FILES['offer_img']['name'])) {
                    if ($offer_id > 0) {
                        $this->set_image($offer_id);
                    }
                }
                /* Send Notification if enabled -by Haritha */
                if ($offer_id > 0 && $offer_notification == 1 && $offer['type'] == 0) {
                    $send_notif = $this->admin_usersms_model->check_noti_settings();
                    $offer_img_path = $this->$model->get_imgpath($offer_id, $type);
                    //    print_r($offer_img_path);exit;    
                    if ($send_notif == 1) {
                        $arraycontent = array(
                            'notification_service' => 2,
                            'header' => $offer['name'],
                            'message' => $offer['offer_content'],
                            'mobile' => '',
                            'footer' => '',
                            'noti_img' => (isset($offer_img_path[0]['offer_img_path']) ? $offer_img_path[0]['offer_img_path'] : ''),
                            'id_branch' => '',
                            'targetUrl' => '#/app/offers'
                        );
                        $this->sendPushNotificationToAll($arraycontent);
                    }
                }
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'New promotion added successfully', 'class' => 'success', 'title' => 'Add Promotion'));
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Add Promotion'));
                }
                redirect('settings/offers');
                break;
            case "Edit":
                $data['offer'] = $this->$model->get_offers($id);
                //print_r($offer);exit;
                $data['main_content'] = self::MAS_VIEW . 'offer/form';
                $this->load->view('layout/template', $data);
                break;
            case "Delete":
                $status = $this->$model->delete_offer($id);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Promotion deleted successfully', 'class' => 'success', 'title' => 'Delete Promotion'));
                }
                redirect('settings/offers');
                break;
            case "Update":
                $offer = $this->input->post('offer');
                $data['offer'] = $this->$model->get_offers($id);
                $offer_notification = isset($offer['offer_notification']) ? $offer['offer_notification'] : 0;
                $type = isset($offer['type']) ? $offer['type'] : NULL;
                //print_r($offer);exit;
                $data = array(
                    'id_offer' => (isset($offer['id_offer']) ? $offer['id_offer'] : null),
                    'name' => (isset($offer['name']) ? $offer['name'] : 0),
                    'offer_content' => (isset($offer['offer_content']) ? $offer['offer_content'] : 0),
                    'active' => (isset($offer['active']) ? $offer['active'] : 0),
                    'offer_notification' => $offer_notification,
                    'type' => (isset($offer['type']) ? $offer['type'] : ''),
                    'id_branch' => (isset($offer['id_branch']) ? $offer['id_branch'] : NULL),
                    'date_update' => date("Y-m-d H:i:s")
                );
                $this->db->trans_begin();
                $offer_id = $this->$model->update_offer($data, $data['id_offer']);
                if (isset($_FILES['offer_img']['name']) && $_FILES['offer_img']['name']) {
                    if ($offer_id > 0) {
                        $this->set_image($offer_id);
                    }
                }
                /* Send Notification if enabled -by Haritha */
                if ($offer_id > 0 && $offer_notification == 1 && $offer['type'] == 0) {
                    $send_notif = $this->admin_usersms_model->check_noti_settings();
                    $offer_img_path = $this->$model->get_imgpath($offer['id_offer'], $type);
                    if ($send_notif == 1) {
                        $arraycontent = array(
                            'notification_service' => 2,
                            'header' => $offer['name'],
                            'message' => $offer['offer_content'],
                            'mobile' => '',
                            'footer' => '',
                            'noti_img' => (isset($offer_img_path[0]['offer_img_path']) ? $offer_img_path[0]['offer_img_path'] : ''),
                            'id_branch' => '',
                            'targetUrl' => '#/app/offers'
                        );
                        $this->sendPushNotificationToAll($arraycontent);
                    }
                }
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Promotion updated successfully', 'class' => 'success', 'title' => 'Edit Promotion'));
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Edit Promotion'));
                }
                redirect('settings/offers');
                break;
            default:
                $data['main_content'] = self::MAS_VIEW . 'offer/list';
                $this->load->view('layout/template', $data);
                break;
        }
    }
    //add branch with image//hh
    function set__branch_image($id)
    {
        // print_r($id);exit;
        $data = array();
        $model = self::SET_MODEL;
        if ($_FILES['file']['name']) {
            $path = 'assets/img/branch/';
            $file_name = $_FILES['file']['name'];
            if (!is_dir($path)) {
                mkdir($path, 0777, TRUE);
            } else {
                $file = $path . $file_name;
                chmod($path, 0777);
                // unlink($file);
            }
            $img = $_FILES['file']['tmp_name'];
            $filename = $_FILES['file']['name'];
            $imgpath = 'assets/img/branch/' . $filename;
            $upload = $this->upload_img('logo', $imgpath, $img);
            $data['logo'] = $filename;
            $status = $this->$model->update_branch($data, $id);
            //print_r($status);exit;
        }
    }
    //add branch with image//hh	
    function set_image($id)
    {
        $data = array();
        $model = self::SET_MODEL;
        if ($_FILES['offer_img']['name']) {
            $path = 'assets/img/offers/';
            if (!is_dir($path)) {
                mkdir($path, 0777, TRUE);
            } else {
                $file = $path . $id . ".jpg";
                chmod($path, 0777);
                unlink($file);
            }
            $img = $_FILES['offer_img']['tmp_name'];
            $filename = $_FILES['offer_img']['name'];
            $imgpath = 'assets/img/offers/' . $filename;
            $upload = $this->upload_img('offer_img', $imgpath, $img);
            $data['offer_img_path'] = base_url() . $imgpath;
            $this->$model->update_offer($data, $id);
        }
        if ($_FILES['new_arrivals_img']['name']) {
            $path = 'assets/img/new_arrivals/';
            if (!is_dir($path)) {
                mkdir($path, 0777, TRUE);
            } else {
                $file = $path . $id . ".jpg";
                chmod($path, 0777);
                unlink($file);
            }
            $img = $_FILES['new_arrivals_img']['tmp_name'];
            $filename = $_FILES['new_arrivals_img']['name'];
            $imgpath = 'assets/img/new_arrivals/' . $filename;
            $upload = $this->upload_img('new_arrivals_img', $imgpath, $img);
            $data['new_arrivals_img_path'] = base_url() . $imgpath;
            $this->$model->update_new_arrivals($data, $id);
        }
    }
    function upload_img($outputImage, $dst, $img)
    {
        if (($img_info = getimagesize($img)) === FALSE) {
            // die("Image not found or not an image");
            return false;
        }
        $width = $img_info[0];
        $height = $img_info[1];
        switch ($img_info[2]) {
            case IMAGETYPE_GIF:
                $src = imagecreatefromgif($img);
                $tmp = imagecreatetruecolor($width, $height);
                $kek = imagecolorallocate($tmp, 255, 255, 255);
                imagefill($tmp, 0, 0, $kek);
                break;
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($img);
                $tmp = imagecreatetruecolor($width, $height);
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($img);
                $tmp = imagecreatetruecolor($width, $height);
                $kek = imagecolorallocate($tmp, 255, 255, 255);
                imagefill($tmp, 0, 0, $kek);
                break;
            default: //die("Unknown filetype");	
                return false;
        }
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);
        imagejpeg($tmp, $dst, 60);
    }
    function ajax_get_offers()
    {
        $model = self::SET_MODEL;
        $popup = array();
        $offersBanners = array();
        $items = $this->$model->ajax_get_offers();
        foreach ($items as $item) {
            if ($item['type'] == 2) {
                $popup[] = $item;
            } else {
                $offersBanners[] = $item;
            }
        }
        $access = $this->$model->get_access('settings/Offers');
        $dept = array(
            'access' => $access,
            'data' => array('offersBanners' => $offersBanners, 'popup' => $popup)
        );
        echo json_encode($dept);
    }
    function new_arrivals_form($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case "Add":
                $data['new_arrivals'] = $this->$model->new_arrivals_empty_record();
                //  
                $data['main_content'] = self::MAS_VIEW . 'new_arrivals/form';
                $this->load->view('layout/template', $data);
                break;
            case 'Save':
                $new_arrivals = $this->input->post('new_arrivals');
                $id_branch = $this->input->post("id_branch"); // based on the branch settings to showed branch filter//HH
                $send_notification = isset($new_arrivals['send_notification']) ? $new_arrivals['send_notification'] : 0;
                $data = array(
                    'id_new_arrivals' => NULL,
                    'name' => (isset($new_arrivals['name']) ? $new_arrivals['name'] : 0),
                    'new_type' => (isset($new_arrivals['new_type']) ? $new_arrivals['new_type'] : 0),
                    'gift_type' => (isset($new_arrivals['gift_type']) ? $new_arrivals['gift_type'] : 0),
                    'new_arrivals_content' => (isset($new_arrivals['new_arrivals_content']) ? $new_arrivals['new_arrivals_content'] : 0),
                    'active' => 1,
                    'new_arrivals_img_path' => (isset($new_arrivals['new_arrivals_img_path']) ? $new_arrivals['new_arrivals_img_path'] : ''),
                    'price' => (!empty($new_arrivals['price']) ? $new_arrivals['price'] : 0.00),
                    'product_code' => (isset($new_arrivals['product_code']) ? $new_arrivals['product_code'] : NULL),
                    'product_description' => (isset($new_arrivals['product_description']) ? $new_arrivals['product_description'] : NULL),
                    'show_rate' => (isset($new_arrivals['show_rate']) ? $new_arrivals['show_rate'] : 0),
                    'date_add' => date("Y-m-d H:i:s"),
                    'id_branch' => (isset($new_arrivals['id_branch']) ? $new_arrivals['id_branch'] : NULL),
                    'send_notification' => $send_notification,
                    'expiry_date' => date('Y-m-d H:i:s', strtotime($new_arrivals['expiry_date']))
                );
                // echo "<pre>";print_r($new_arrivals);echo "</pre>";exit;
                $this->db->trans_begin();
                $new_arrivals_id = $this->$model->insert_new_arrivals($data);
                if (isset($_FILES['new_arrivals_img']['name'])) {
                    if ($new_arrivals_id > 0) {
                        $this->set_image($new_arrivals_id);
                    }
                }
                /* Send Notification if enabled -by Haritha */
                if ($new_arrivals_id > 0 && $send_notification == 1 && $new_arrivals['new_type'] == 1) {
                    $send_notif = $this->admin_usersms_model->check_noti_settings();
                    $newArrival_img_path = $this->$model->get_newArival_imgpath($new_arrivals_id);
                    if ($send_notif == 1) {
                        $arraycontent = array(
                            'notification_service' => 3,
                            'header' => $new_arrivals['name'],
                            'message' => $new_arrivals['new_arrivals_content'],
                            'mobile' => '',
                            'footer' => '',
                            'noti_img' => (isset($newArrival_img_path[0]['new_arrivals_img_path']) ? $newArrival_img_path[0]['new_arrivals_img_path'] : ''),
                            'id_branch' => '',
                            'targetUrl' => '#/app/newarrivals'
                        );
                        $this->sendPushNotificationToAll($arraycontent);
                    }
                }
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'New Arrivals added successfully', 'class' => 'success', 'title' => 'Add New Arrivals'));
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Add New Arrivals'));
                }
                redirect('settings/new_arrivals');
                break;
            case "Edit":
                $data['new_arrivals'] = $this->$model->get_new_arrivals($id);
                //	echo "<pre>";print_r($data['new_arrivals']);echo "</pre>";exit;
                $data['main_content'] = self::MAS_VIEW . 'new_arrivals/form';
                $this->load->view('layout/template', $data);
                break;
            case "Delete":
                $status = $this->$model->delete_new_arrivals($id);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'New Arrivals deleted successfully', 'class' => 'success', 'title' => 'Delete New Arrivals'));
                }
                redirect('settings/new_arrivals');
                break;
            case "Update":
                $new_arrivals = $this->input->post('new_arrivals');
                $data['new_arrivals'] = $this->$model->get_new_arrivals($id);
                $send_notification = isset($new_arrivals['send_notification']) ? $new_arrivals['send_notification'] : 0;
                $data = array(
                    'id_new_arrivals' => (isset($new_arrivals['id_new_arrivals']) ? $new_arrivals['id_new_arrivals'] : null),
                    'name' => (isset($new_arrivals['name']) ? $new_arrivals['name'] : 0),
                    'new_type' => (isset($new_arrivals['new_type']) ? $new_arrivals['new_type'] : 0),
                    'gift_type' => (isset($new_arrivals['gift_type']) ? $new_arrivals['gift_type'] : 0),
                    'new_arrivals_content' => (isset($new_arrivals['new_arrivals_content']) ? $new_arrivals['new_arrivals_content'] : 0),
                    'active' => (isset($new_arrivals['active']) ? $new_arrivals['active'] : 0),
                    'price' => (isset($new_arrivals['price']) ? $new_arrivals['price'] : null),
                    'product_code' => (isset($new_arrivals['product_code']) ? $new_arrivals['product_code'] : null),
                    'product_description' => (isset($new_arrivals['product_description']) ? $new_arrivals['product_description'] : null),
                    'date_update' => date("Y-m-d H:i:s"),
                    'id_branch' => (isset($new_arrivals['id_branch']) ? $new_arrivals['id_branch'] : NULL),
                    'expiry_date' => date('Y-m-d H:i:s', strtotime($new_arrivals['expiry_date'])),
                    'send_notification' => $send_notification,
                    'show_rate' => (isset($new_arrivals['show_rate']) ? $new_arrivals['show_rate'] : 0)
                );
                $this->db->trans_begin();
                $new_arrivals_id = $this->$model->update_new_arrivals($data, $data['id_new_arrivals']);
                if (isset($_FILES['new_arrivals_img']['name']) && $_FILES['new_arrivals_img']['name']) {
                    if ($new_arrivals_id > 0) {
                        $this->set_image($new_arrivals_id);
                    }
                }
                /* Send Notification if enabled -by Haritha */
                if ($new_arrivals_id > 0 && $send_notification == 1 && $new_arrivals['new_type'] == 1) {
                    $send_notif = $this->admin_usersms_model->check_noti_settings();
                    $newArrival_img_path = $this->$model->get_newArival_imgpath($new_arrivals_id);
                    if ($send_notif == 1) {
                        $arraycontent = array(
                            'notification_service' => 3,
                            'header' => $new_arrivals['name'],
                            'message' => $new_arrivals['new_arrivals_content'],
                            'mobile' => '',
                            'footer' => '',
                            'noti_img' => (isset($newArrival_img_path[0]['new_arrivals_img_path']) ? $newArrival_img_path[0]['new_arrivals_img_path'] : ''),
                            'id_branch' => '',
                            'targetUrl' => '#/app/newarrivals'
                        );
                        $this->sendPushNotificationToAll($arraycontent);
                    }
                }
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'New Arrivals updated successfully', 'class' => 'success', 'title' => 'Edit New Arrivals'));
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Edit New Arrivals'));
                }
                redirect('settings/new_arrivals');
                break;
            default:
                $data['main_content'] = self::MAS_VIEW . 'new_arrivals/list';
                $this->load->view('layout/template', $data);
                break;
        }
    }
    function ajax_get_new_arrivals()
    {
        $model = self::SET_MODEL;
        $items = $this->$model->ajax_get_new_arrivals();
        $access = $this->$model->get_access('settings/new_arrivals');
        $dept = array(
            'access' => $access,
            'data' => $items
        );
        echo json_encode($dept);
    }
    function cardbrand_form($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case "Add":
                $card_brand = $this->input->post("card_brand");
                $code = $this->input->post("short_code");
                $ct = $this->input->post("card_type");
                $wt_data = array('card_brand' => $card_brand, 'short_code' => $code, 'card_type' => $ct);
                //	echo "<pre>";print_r($wt_data);echo "</pre>";exit; 
                $this->db->trans_begin();
                $this->$model->insert_card_brand($wt_data);
                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'New card added successfully', 'class' => 'success', 'title' => 'Add Card'));
                    //redirect('settings/cardbrand');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Add Card'));
                    //redirect('settings/cardbrand');
                }
                break;
            case "Edit":
                $data['card_brand'] = $this->$model->get_card_brand($id);
                //echo "<pre>";print_r($data['card_brand']);echo "</pre>"; exit;
                echo json_encode($data['card_brand']);
                //$data['wt']=$weight['weight'];
                break;
            case "Delete":
                $status = $this->$model->delete_card_brand($id);
                echo $status;
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'card brand deleted successfully', 'class' => 'success', 'title' => 'Delete card brand'));
                }
                redirect('settings/cardbrand');
                //$data['wt']=$weight['weight'];
                break;
            case "Update":
                //$data['id_card_brand']=$this->$model->get_card_brand($id);  
                $cb = $this->input->post('card_brand');
                $sc = $this->input->post('short_code');
                $ct = $this->input->post('card_type');
                //echo "<pre>";print_r($id);echo "</pre>";exit; 				 
                //
                $data = array("card_brand" => $cb, "short_code" => $sc, "card_type" => $ct);
                $this->$model->update_cardbrand($data, $id);
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'card brand updated successfully', 'class' => 'success', 'title' => 'Edit card brand'));
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Edit cardbrand'));
                }
                // redirect('settings/weight');
                //$data['wt']=$weight['weight']; 
                break;
            default:
                $data['main_content'] = self::VIEW_FOLDER . 'card_list';
                $this->load->view('layout/template', $data);
                break;
        }
    }
    function ajax_get_cardbrand()
    {
        $model = self::SET_MODEL;
        $weights = $this->$model->ajax_get_cardbrand();
        echo $weights;
    }
    // branch_settings 
    function branch_settings($id)
    {
        $model = self::SET_MODEL;
        $log_model = self::LOG_MODEL;
        $general = $this->input->post('general');
        $data = array(
            'branch_settings' => $general['branch_settings'],
            'branchWiseLogin' => $general['branchWiseLogin'],
            'is_branchwise_cus_reg' => $general['is_branchwise_cus_reg'],
            'is_branchwise_rate' => $general['is_branchwise_rate']
        );
        $update = $this->$model->settingsDB('update', $id, $data);
        $log_data = array(                                                                //scheme log details
            'id_log' => $this->id_log,
            'event_date' => date("Y-m-d H:i:s"),
            'module' => 'Settings - Branch setting',
            'operation' => 'Edit',
            'record' => 'Branch setting',
            'remark' => 'General Settings edited successfully'
        );
        // print_r($log_data );exit;
        $this->$log_model->log_detail('insert', '', $log_data);
        if ($update) {
            $get_chitsettngs = $this->$model->settingsDB('get', '', '');
            $this->session->set_userdata('branch_settings', $get_chitsettngs[0]['branch_settings']);
            $this->session->set_userdata('branchWiseLogin', $get_chitsettngs[0]['branchWiseLogin']);
            $this->session->set_userdata('is_branchwise_cus_reg', $get_chitsettngs[0]['is_branchwise_cus_reg']);
            $this->session->set_userdata('is_branchwise_rate', $get_chitsettngs[0]['is_branchwise_rate']);
            $this->session->set_flashdata('chit_alert', array('message' => 'General Settings edited successfully', 'class' => 'success', 'title' => 'General Settings'));
            //redirect('admin/logout');
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'General Settings'));
        }
        redirect('settings/general/edit/1');
        //	 break;
    }
    //branch_settings
    // Google Review URL sanitizer — validates and whitelists Google domains only
    private function _sanitize_google_review_url($raw_url)
    {
        if (empty($raw_url) || trim($raw_url) === '') {
            return NULL;
        }
        $url = trim($raw_url);
        // Must be a valid URL structure
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return NULL;
        }
        // Must be HTTPS
        $parsed = parse_url($url);
        if (empty($parsed['scheme']) || strtolower($parsed['scheme']) !== 'https') {
            return NULL;
        }
        // Host must be a Google domain
        $host = strtolower(isset($parsed['host']) ? $parsed['host'] : '');
        $allowed_hosts = array(
            'google.com', 'www.google.com',
            'maps.google.com', 'www.maps.google.com',
            'search.google.com',
            'g.page',
        );
        $is_google = in_array($host, $allowed_hosts);
        // Also allow country-specific: google.co.in, google.co.uk, maps.google.co.in, etc.
        if (!$is_google) {
            $is_google = (bool) preg_match('/^(www\.)?(maps\.)?google\.(com?\.[a-z]{2,3}|[a-z]{2,3})$/i', $host);
        }
        if (!$is_google) {
            return NULL;
        }
        // Strip dangerous characters
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return $url;
    }
    // branch name  with image//hh
    function branch_form($type = "", $id = "", $status = "")
    {
        $model = self::SET_MODEL;
        $sch_model = self::SCH_MODEL;
        switch ($type) {
            case "Add":
                //$branch_data=$this->input->post("bran");
                // print_r($_FILES);exit;
                $branch_data = array(
                    'map_url' => ($this->input->post("map_url") != '' ? $this->input->post("map_url") : NULL),
                    'is_ho' => ($this->input->post("is_ho") != '' ? $this->input->post("is_ho") : NULL),
                    'name' => ($this->input->post("branch") != '' ? strtoupper($this->input->post("branch")) : NULL),
                    'short_name' => ($this->input->post("short_name") != '' ? strtoupper($this->input->post("short_name")) : NULL),
                    'address1' => ($this->input->post("address1") != '' ? $this->input->post("address1") : NULL),
                    'address2' => ($this->input->post("address2") != '' ? $this->input->post("address2") : NULL),
                    'id_country' => ($this->input->post("country") != 'null' ? $this->input->post("country") : NULL),
                    'id_state' => ($this->input->post("state") != 'null' ? $this->input->post("state") : NULL),
                    'id_city' => ($this->input->post("city") != 'null' ? $this->input->post("city") : NULL),
                    'phone' => ($this->input->post("phone") != '' ? $this->input->post("phone") : NULL),
                    'mobile' => ($this->input->post("mobile") != '' ? $this->input->post("mobile") : NULL),
                    'cusromercare' => ($this->input->post("cusromercare") != '' ? $this->input->post("cusromercare") : NULL),
                    'pincode' => ($this->input->post("pincode") != '' ? $this->input->post("pincode") : NULL),
                    'id_village' => ($this->input->post("sel_village") != '' ? $this->input->post("sel_village") : NULL),
                    'gst_number' => ($this->input->post("gst_number") != '' ? $this->input->post("gst_number") : NULL),
                    'metal_rate_type' => ($this->input->post("metal_type") != '' && $this->input->post("metal_type") != 'undefined' ? $this->input->post("metal_type") : 0),
                    'day_close' => ($this->input->post('day_close') != '' ? $this->input->post('day_close') : NULL),
                    'show_to_all' => $this->input->post("show_to_all"),
                    'partial_silverrate_diff' => $this->input->post("partial_silverrate_diff"),
                    'partial_goldrate_diff' => $this->input->post("partial_goldrate_diff"),
                    'enable_gift_voucher' => $this->input->post("ed_enable_gift_voucher"),
                    'google_review_url' => $this->_sanitize_google_review_url($this->input->post("google_review_url"))
                );
                //	print_r($this->db->last_query());exit;	
                $this->db->trans_begin();
                $id_branch = $this->$model->insert_branch($branch_data);
                //	print_r($id_branch);exit;
                //	print_r($this->db->last_query());exit;			
                //insert into day closing table
                if ($id_branch['id_branch']) {
                    $branchid = array(
                        'id_branch' => $id_branch['id_branch'],
                        'entry_date' => date("Y-m-d"),
                        'day_close_time' => date("H:i:s"),
                        'created_on' => date("Y-m-d H:i:s"),
                        'created_by' => $this->session->userdata('uid'),
                    );
                    $this->$model->insertData($branchid, 'ret_day_closing');
                }
                if (!empty($this->input->post("city"))) {
                    $data = array(
                        'is_default' => 1,
                    );
                    $this->$model->updateData($data, 'id_city', $this->input->post("city"), 'city');
                }
                if (isset($_FILES['file']['name'])) {
                    if ($id_branch > 0) {
                        $result = $this->set__branch_image($id_branch['id_branch']);
                    }
                }
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'New branch added successfully', 'class' => 'success', 'title' => 'Add branch'));
                    echo TRUE;
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Add branch'));
                }
                //	redirect('branch/list');
                break;
            case "Edit":
                $data['branch'] = $this->$model->get_branch_by_id($id);
                //echo "<pre>";print_r($data['branch']['id_country']);echo "</pre>";exit;
                echo json_encode($data['branch']);
                //$data['wt']=$weight['weight'];
                break;
            case "Update":
                //$data['branch']=$this->$model->get_branch($id);
                $data = array(
                    'map_url' => ($this->input->post("map_url") != '' ? $this->input->post("map_url") : NULL),
                    'is_ho' => ($this->input->post("is_ho") != '' && $this->input->post("is_ho") != 'undefined' ? $this->input->post("is_ho") : NULL),
                    'name' => ($this->input->post("branch") != '' ? strtoupper($this->input->post("branch")) : NULL),
                    'short_name' => ($this->input->post("short_name") != '' ? strtoupper($this->input->post("short_name")) : NULL),
                    'address1' => ($this->input->post("address1") != '' ? $this->input->post("address1") : NULL),
                    'address2' => ($this->input->post("address2") != '' ? $this->input->post("address2") : NULL),
                    'id_country' => ($this->input->post("country") != 'null' ? $this->input->post("country") : NULL),
                    'id_state' => ($this->input->post("state") != 'null' ? $this->input->post("state") : NULL),
                    'id_city' => ($this->input->post("city") != 'null' ? $this->input->post("city") : NULL),
                    'phone' => ($this->input->post("phone") != '' ? $this->input->post("phone") : NULL),
                    'mobile' => ($this->input->post("mobile") != '' ? $this->input->post("mobile") : NULL),
                    'cusromercare' => ($this->input->post("cusromercare") != '' ? $this->input->post("cusromercare") : NULL),
                    'pincode' => ($this->input->post("pincode") != '' ? $this->input->post("pincode") : NULL),
                    'id_village' => ($this->input->post("sel_village") != '' ? $this->input->post("sel_village") : NULL),
                    'metal_rate_type' => ($this->input->post("metal_type") != '' && $this->input->post("metal_type") != 'undefined' ? $this->input->post("metal_type") : 0),
                    'day_close' => ($this->input->post('day_close') != '' ? $this->input->post('day_close') : NULL),
                    'show_to_all' => $this->input->post("show_to_all"),
                    'partial_silverrate_diff' => $this->input->post("partial_silverrate_diff"),
                    'partial_goldrate_diff' => $this->input->post("partial_goldrate_diff"),
                    'enable_gift_voucher' => $this->input->post("ed_enable_gift_voucher"),
                    'gst_number' => ($this->input->post("gst_number") != '' ? $this->input->post("gst_number") : NULL),
                    'google_review_url' => $this->_sanitize_google_review_url($this->input->post("google_review_url")),
                );
                //print_r($data);exit;		
                $update_id = $this->$model->update_branch($data, $id);
                //	print_r($_FILES);exit;
                if (isset($_FILES['file']['name'])) {
                    if ($update_id > 0) {
                        $this->set__branch_image($id);
                    }
                }
                if (!empty($this->input->post("city"))) {
                    $data = array(
                        'is_default' => 1,
                    );
                    $this->$model->updateData($data, 'id_city', $this->input->post("city"), 'city');
                }
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Branch updated successfully', 'class' => 'success', 'title' => 'Edit branch'));
                    echo TRUE;
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Edit branch'));
                }
                break;
            case "Update_status":
                $data = array('active' => $status);
                $stat = $this->$model->update_branch_only($data, $id);
                if ($stat) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Branch status updated as ' . ($status == '1' ? 'active' : 'inactive') . ' successfully.', 'class' => 'success', 'title' => 'Branch Status'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Branch Status'));
                }
                redirect('branch/list');
                break;
            case "get_branches":
                $data['branches'] = $this->$model->get_branches();
                echo json_encode($data);
                break;
            case "branch_check":
                $data['branch'] = $this->$sch_model->branch_check($id);
                echo json_encode($data);
                break;
            default:
                $data['main_content'] = self::MAS_VIEW . 'branch/list';
                $data['access'] = $this->$model->get_access('branch/list');
                $this->load->view('layout/template', $data);
                break;
        }
    }
    // branch list 
    // branch name 
    function ajax_get_branches()
    {
        $model = self::SET_MODEL;
        $branches['data'] = $this->$model->ajax_get_branches();
        //$branches['access']=$this->model->get_access('admin_ret_catalog/ret_product/list');
        $branches['access'] = $this->$model->get_access('branch/list');
        echo json_encode($branches);
    }
    // branch name 
    // matal_ratelist branch 
    function matal_ratelist($id)
    {
        $model = self::SET_MODEL;
        $empid = $this->$model->metal_rate_type($id);
        $emp_id = (isset($empid['metal_rate_type']) && $empid['metal_rate_type'] == 1 ? 0 : 1);
        $data['rates'] = $this->$model->metal_rates_list($id, $emp_id);
        $data['max_id'] = $this->$model->max_metalrate_list($id, $emp_id);
        $data['access'] = $this->$model->get_access('settings/rate/list');
        echo json_encode($data);
    }
    //matal_ratelist branch 
    //   function gst_settings($id)
    // {	
    // 	$model=self::SET_MODEL;
    //   	 	$general = $this->input->post('general');
    // 	$data = array(
    // 					'gst_setting'	  => $general['gst_setting']
    // 				);
    // 	$update =	$this->$model->settingsDB('update',$id,$data);	
    // 	if($update)  {	    
    // 		$this->session->set_flashdata('chit_alert', array('message' => 'General Settings edited successfully','class' => 'success','title'=>'General Settings'));
    // 	}
    // 	else{
    // 		  $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'General Settings'));
    // 	}
    // 	redirect('settings/general/edit/1');
    // 	// break;
    //   	 	}
    //schemeacc_no_settings
    function schemeacc_no_settings($id)
    {
        $model = self::SET_MODEL;
        $general = $this->input->post('general');
        $data = array(
            'schemeacc_no_set' => $general['schemeacc_no_set']
        );
        $update = $this->$model->settingsDB('update', $id, $data);
        if ($update) {
            $this->session->set_flashdata('chit_alert', array('message' => 'General Settings edited successfully', 'class' => 'success', 'title' => 'General Settings'));
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'General Settings'));
        }
        redirect('settings/general/edit/1');
        // break;
    }
    //scheme_group_settings//	
    // function  scheme_group_settings($id)
    // 	{	
    // 		$model=self::SET_MODEL;
    //    	 	$general = $this->input->post('general');
    // 		$data = array(
    // 						'scheme_group_set'	  => $general['scheme_group_set']
    // 					);
    // 		$update =	$this->$model->settingsDB('update',$id,$data);	
    // 		if($update)  {	    
    // 			$this->session->set_flashdata('chit_alert', array('message' => 'General Settings edited successfully','class' => 'success','title'=>'General Settings'));
    // 		}
    // 		else{
    // 			  $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'General Settings'));
    // 		}
    // 		redirect('settings/general/edit/1');
    // 		// break;
    //    	}	
    //scheme_group_settings//
    //Promotion sms and otp setting	
    function promotioncredit__settings($type = "", $id = "", $array = "")
    {
        $model = self::SET_MODEL;
        $log_model = self::LOG_MODEL;
        switch ($type) {
            case 'Update':
                $promotion = $this->input->post('promotion_crt');
                if ($promotion['enable_promotion'] == 1) {
                    $promotion_crt = $promotion['credit_promotion'] + $promotion['debit_promotion'];
                    $data = array(
                        'credit_promotion' => (isset($promotion['credit_promotion']) ? $promotion['credit_promotion'] : 0),
                        'debit_promotion' => (isset($promotion_crt) ? $promotion_crt : 0)
                    );
                    $update = $this->$model->promotion_crt_settings('update', $id, $data);
                    $log_data = array(                                                                //scheme log details
                        'id_log' => $this->id_log,
                        'event_date' => date("Y-m-d H:i:s"),
                        'module' => 'Settings - Promotion credit Settings',
                        'operation' => 'Edit',
                        'record' => 'Promotion credit',
                        'remark' => 'General Settings edited successfully'
                    );
                    // print_r($log_data );exit;
                    $this->$log_model->log_detail('insert', '', $log_data);
                    if ($update['status']) {
                        $this->session->set_flashdata('chit_alert', array('message' => 'General Settings edited successfully', 'class' => 'success', 'title' => 'General Settings'));
                    } else {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'General Settings'));
                    }
                }
                redirect('settings/general/edit/1');
                break;
        }
    }
    function promotion_api_settings($type = "", $id = "", $array = "")
    {
        $model = self::SET_MODEL;
        $log_model = self::LOG_MODEL;
        switch ($type) {
            case 'Update':
                $promotion = $this->input->post('promotion');
                $data = array(
                    'promotion_sender_id' => (isset($promotion['promotion_sender_id']) ? $promotion['promotion_sender_id'] : 0),
                    'promotion_url' => (isset($promotion['promotion_url']) ? $promotion['promotion_url'] : 0)
                );
                $update = $this->$model->promotion_crt_settings('update', $id, $data);
                $log_data = array(                                                                //scheme log details
                    'id_log' => $this->id_log,
                    'event_date' => date("Y-m-d H:i:s"),
                    'module' => 'Settings - Gateway Settings',
                    'operation' => 'Edit',
                    'record' => 'Promotion API',
                    'remark' => 'General Settings edited successfully'
                );
                // print_r($log_data );exit;
                $this->$log_model->log_detail('insert', '', $log_data);
                if ($update['status']) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'General Settings edited successfully', 'class' => 'success', 'title' => 'General Settings'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'General Settings'));
                }
                redirect('settings/general/edit/1');
                break;
        }
    }
    function otpcredit_settings($type = "", $id = "", $array = "")
    {
        $model = self::SET_MODEL;
        $log_model = self::LOG_MODEL;
        switch ($type) {
            case 'Update':
                $otp = $this->input->post('otp_crt');
                if ($otp['enable_otp'] == 1) {
                    $otp_crt = $otp['credit_sms'] + $otp['debit_sms'];
                    $data = array(
                        'credit_sms' => (isset($otp['credit_sms']) ? $otp['credit_sms'] : 0),
                        'debit_sms' => (isset($otp_crt) ? $otp_crt : 0)
                    );
                    $update = $this->$model->otp_crt_settings('update', $id, $data);
                    $log_data = array(                                                                //scheme log details
                        'id_log' => $this->id_log,
                        'event_date' => date("Y-m-d H:i:s"),
                        'module' => 'Settings - OTP SMS Credit Settings',
                        'operation' => 'Edit',
                        'record' => 'OTP SMS Credit',
                        'remark' => 'General Settings edited successfully'
                    );
                    // print_r($log_data );exit;
                    $this->$log_model->log_detail('insert', '', $log_data);
                    if ($update['status']) {
                        $this->session->set_flashdata('chit_alert', array('message' => 'General Settings edited successfully', 'class' => 'success', 'title' => 'General Settings'));
                    } else {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'General Settings'));
                    }
                }
                redirect('settings/general/edit/1');
                break;
        }
    }
    //Promotion sms and otp setting		
    function wallettype_account($id)
    {
        $model = self::SET_MODEL;
        $log_model = self::LOG_MODEL;
        $general = $this->input->post('general');
        $data = array(
            'allow_wallet' => (isset($general['allow_wallet']) ? $general['allow_wallet'] : 0),
            'wallet_account_type' => (isset($general['wallet_account_type']) ? $general['wallet_account_type'] : 0),
            'useWalletForChit' => (isset($general['useWalletForChit']) ? $general['useWalletForChit'] : 0),
            'walletIntegration' => (isset($general['walletIntegration']) ? $general['walletIntegration'] : 0),
            // use wallet amount//
            'emp_wallet_account_type' => (isset($general['emp_wallet_account_type']) ? $general['emp_wallet_account_type'] : 0),
            'wallet_balance_type' => (isset($general['wallet_balance_type']) ? $general['wallet_balance_type'] : 0),  // use wallet for//
            'wallet_amt_per_points' => (isset($general['wallet_amt_per_points']) ? $general['wallet_amt_per_points'] : 0),  // amt per points//
            'wallet_points' => (isset($general['wallet_points']) ? $general['wallet_points'] : 0)
        );
        $update = $this->$model->settingsDB('update', $id, $data);
        $log_data = array(                                                                //scheme log details
            'id_log' => $this->id_log,
            'event_date' => date("Y-m-d H:i:s"),
            'module' => 'Settings - Wallet Settings',
            'operation' => 'Edit',
            'record' => 'Wallet Settings',
            'remark' => 'General Settings edited successfully'
        );
        // print_r($log_data );exit;
        $this->$log_model->log_detail('insert', '', $log_data);
        if ($update) {
            $this->session->set_flashdata('chit_alert', array('message' => 'General Settings edited successfully', 'class' => 'success', 'title' => 'General Settings'));
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'General Settings'));
        }
        redirect('settings/general/edit/1');
        //	 break;
    }
    function ref_benefits_setting($id)
    {
        $model = self::SET_MODEL;
        $log_model = self::LOG_MODEL;
        $general = $this->input->post('general');
        $data = array(
            'cusplan_type' => (isset($general['cusplan_type']) ?
                $general['cusplan_type'] : 0),
            'cusbenefitscrt_type' => (isset($general['cusbenefitscrt_type']) ?
                $general['cusbenefitscrt_type'] : 1),
            'empplan_type' => (isset($general['empplan_type']) ?
                $general['empplan_type'] : 1),
            'empbenefitscrt_type' => (isset($general['empbenefitscrt_type']) ?
                $general['empbenefitscrt_type'] : 1),
            'schrefbenifit_secadd' => (isset($general['schrefbenifit_secadd']) ?
                $general['schrefbenifit_secadd'] : 1),
            'allow_referral' => (isset($general['allow_referral']) ?
                $general['allow_referral'] : 1),
            'emp_ref_by' => (isset($general['emp_ref_by']) ?
                $general['emp_ref_by'] : 1)
        );
        $update = $this->$model->settingsDB('update', $id, $data);
        $log_data = array(                                                                //scheme log details
            'id_log' => $this->id_log,
            'event_date' => date("Y-m-d H:i:s"),
            'module' => 'Settings - Referral Settings',
            'operation' => 'Edit',
            'record' => 'Referral Settings',
            'remark' => 'General Settings edited successfully'
        );
        // print_r($log_data );exit;
        $this->$log_model->log_detail('insert', '', $log_data);
        if ($update) {
            $this->session->set_flashdata('chit_alert', array('message' => 'General Settings edited successfully', 'class' => 'success', 'title' => 'General Settings'));
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'General Settings'));
        }
        redirect('settings/general/edit/1');
        //	 break;
    }
    function set__clsfy_image($id)
    {
        $data = array();
        $model = self::SET_MODEL;
        if ($_FILES['file']['name']) {
            $path = 'assets/img/sch_classify/';
            $file_name = $_FILES['file']['name'];
            if (!is_dir($path)) {
                mkdir($path, 0777, TRUE);
            } else {
                $file = $path . $file_name;
                chmod($path, 0777);
                unlink($file);
            }
            $img = $_FILES['file']['tmp_name'];
            $filename = $_FILES['file']['name'];
            $imgpath = 'assets/img/sch_classify/' . $filename;
            $upload = $this->upload_img('logo', $imgpath, $img);
            $data['logo'] = $filename;
            $status = $this->$model->update_classification($data, $id);
        }
    }
    //payment gateway
    function gateway_form($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case "Add":
                $pg_name = $this->input->post("gateway_name");
                $code = $this->input->post("code");
                $active = $this->input->post("gateway_active");
                $savecard = $this->input->post("savecard");
                $debitcard = $this->input->post("debit_card");
                $creditcard = $this->input->post("credit_card");
                $netbanking = $this->input->post("netbanking");
                $netbanking = $this->input->post("netbanking");
                $description = $this->input->post("description");
                $id_metal    = $this->input->post("id_metal");
                $data = array('pg_name' => $pg_name, 'pg_code' => $code, 'active' => $active, 'savecard' => $savecard, 'debitcard' => $debitcard, 'creditcard' => $creditcard, 'netbanking' => $netbanking, 'description' => $description, 'id_metal' => ($id_metal != '' ? $id_metal : NULL));
                $this->db->trans_begin();
                $id_payment_gateway = $this->$model->insert_payment_gateway($data);
                if (isset($_FILES['file']['name'])) {
                    if ($id_payment_gateway > 0) {
                        $result = $this->set__paymentgateway_image($id_payment_gateway);
                    }
                }
                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'New payment gateway added successfully', 'class' => 'success', 'title' => 'Payment Gateway'));
                    echo TRUE;
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Payment Gateway'));
                }
                break;
            case "Edit":
                $id = $this->input->post("id_pg");
                //$id_branch= $this->input->post("id_branch");
                $data['card_brand'] = $this->$model->get_paymentgateway($id);
                echo json_encode($data['card_brand']);
                break;
            case "Delete":
                $status = $this->$model->delete_payment_gateway($id);
                echo $status;
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Payment gateway deleted successfully', 'class' => 'success', 'title' => ' Payment Gateway'));
                }
                //	redirect('settings/payment');
                break;
            case "Update":
                $pg_name = $this->input->post("gateway_name");
                $code = $this->input->post("code");
                $active = $this->input->post("active");
                $savecard = $this->input->post("savecard");
                $debitcard = $this->input->post("debit_card");
                $creditcard = $this->input->post("creditcard");
                $netbanking = $this->input->post("netbanking");
                $id = $this->input->post("id");
                $description = $this->input->post("description");
                $id_metal    = $this->input->post("id_metal");
                //$id_branch=$this->input->post("id_branch");
                $data = array("pg_name" => $pg_name, "pg_code" => $code, "active" => $active, "savecard" => $savecard, "debitcard" => $debitcard, "creditcard" => $creditcard, "netbanking" => $netbanking, "description" => $description, "id_metal" => ($id_metal != '' ? $id_metal : NULL));
                $update_id = $this->$model->update_paymentgateway($data, $id);
                if (isset($_FILES['file']['name'])) {
                    if ($update_id > 0) {
                        $this->set__paymentgateway_image($update_id);
                    }
                }
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Payment gateway  updated successfully', 'class' => 'success', 'title' => 'Edit Payment Gateway'));
                    echo TRUE;
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Payment Gateway'));
                }
                break;
            default:
                $data['chit_metals']  = $this->$model->get_chit_metals();
                $data['main_content'] = self::VIEW_FOLDER . 'gateway_list';
                $this->load->view('layout/template', $data);
                break;
        }
    }
    function ajax_get_paymentgateway()
    {
        $model = self::SET_MODEL;
        $id_branch = $this->input->post("id_branch");
        $data = $this->$model->ajax_get_paymentgateway($id_branch);
        echo json_encode($data);
    }
    function set__paymentgateway_image($id)
    {
        $data = array();
        $model = self::SET_MODEL;
        if ($_FILES['file']['name']) {
            $path = 'assets/img/gateway/';
            $file_name = $_FILES['file']['name'];
            if (!is_dir($path)) {
                mkdir($path, 0777, TRUE);
            } else {
                $file = $path . $file_name;
                chmod($path, 0777);
                unlink($file);
            }
            $img = $_FILES['file']['tmp_name'];
            $filename = $_FILES['file']['name'];
            $imgpath = 'assets/img/gateway/' . $filename;
            $upload = $this->upload_img('pg_icon', $imgpath, $img);
            $data['pg_icon'] = $filename;
            $status = $this->$model->update_paymentgateway($data, $id);
        }
    }
    function interWalletAcc_backup()
    {
        if ($this->session->userdata('profile') <= 2) {
            date_default_timezone_set('Asia/Calcutta');
            $model = self::SET_MODEL;
            $path = '../data/backup/wallet/';
            $filename = 'walletbackup_' . date('d_m_Y_H_i_s');
            // Load the file helper and write the file to your server 
            $this->load->helper('file');
            $backup = $this->$model->getWalletData();
            if (!is_dir($path)) {
                mkdir($path, 0777, TRUE);
            }
            $write_status = write_file($path . $filename . '.txt', $backup);
            // Load the download helper and send the file to your desktop 
            if ($write_status) {
                $db_data = array(
                    'backup_date' => date('Y-m-d H:i:s'),
                    'id_employee' => $this->id_employee,
                    'filename' => $filename . '.txt'
                );
                $this->$model->database_backup('insert', '', $db_data);
            }
            $this->load->helper('download');
            force_download($filename . '.txt', $backup);
        }
    }
    function send_RatesToAllUsers($branchArr)
    {
        $model = self::MODEL;
        $result = array();
        $send_notif = $this->$model->check_noti_settings();
        $chitsettings = $this->admin_settings_model->settingsDB("get", 1, "");
        $targetUrl = '#/app/notification';
        if ($send_notif == 1) {
            //send rate notification
            if ($chitsettings['is_branchwise_rate'] == 1) {
                if (sizeof($branchArr) > 0) {
                    if ($chitsettings['is_branchwise_cus_reg'] == 1) {
                        foreach ($branchArr as $branch) {
                            $cusData = $this->$model->get_cusBranchRate($branch, "");
                            if (count($cusData) > 0) {
                                foreach ($cusData as $cus) {
                                    $noti_msg = '';
                                    $resultset = $this->db->query("SELECT noti_name,noti_name, noti_footer,noti_msg from notification where id_notification =1");
                                    foreach ($resultset->result() as $row) {
                                        $noti_msg = $row->noti_msg;
                                        $noti_footer = $row->noti_footer;
                                        $noti_header = $row->noti_name;
                                    }
                                    $resultset->free_result();
                                    //Generating Message content
                                    $field_name = explode('@@', $noti_msg);
                                    for ($i = 1; $i < count($field_name); $i += 2) {
                                        $field = $field_name[$i];
                                        if (isset($cus[$field])) {
                                            $noti_msg = str_replace("@@" . $field . "@@", $cus[$field], $noti_msg);
                                        }
                                    }
                                    $field_name_footer = explode('@@', $noti_footer);
                                    for ($i = 1; $i < count($field_name_footer); $i += 2) {
                                        if (isset($cus->$field_name_footer[$i])) {
                                            $noti_footer = str_replace("@@" . $field_name_footer[$i] . "@@", $cus[$field_name_footer[$i]], $noti_footer);
                                        }
                                    }
                                    $arraycontent = array(
                                        'notification_service' => 1,
                                        'header' => 'Daliy Rate',
                                        'message' => $noti_msg,
                                        'token' => $cus['token'],
                                        'targetUrl' => $targetUrl
                                    );
                                    $result = $this->send_singlealert_rate_notification($arraycontent);
                                }
                            }
                        }
                    } else {
                        $account = $this->$model->get_account(implode(",", $branchArr));
                        if (count($account) > 0) {
                            foreach ($account as $acc) {
                                $rate = $this->$model->get_metal_rateby_branch($acc['id_customer']);
                                $msg = '';
                                $resultset = $this->db->query("SELECT noti_name,noti_name, noti_footer,noti_msg from notification where id_notification =1");
                                foreach ($resultset->result() as $row) {
                                    $noti_msg = $row->noti_msg;
                                    $noti_footer = $row->noti_footer;
                                    $noti_header = $row->noti_name;
                                }
                                $resultset->free_result();
                                foreach ($rate as $cusData) {
                                    //$msg.="Today ".$rates['name']." Gold Rate Rs.".$rates['goldrate_22ct']."/Gm (22 kt),Silver Rate Rs.".$rates['silverrate_1gm']." Gm at ".$rates['updatetime']." .";
                                    //Generating Message content
                                    $field_name = explode('@@', $noti_msg);
                                    for ($i = 1; $i < count($field_name); $i += 2) {
                                        $field = $field_name[$i];
                                        if (isset($cusData[$field])) {
                                            $noti_msg = str_replace("@@" . $field . "@@", $cusData[$field], $noti_msg);
                                        }
                                    }
                                    $field_name_footer = explode('@@', $noti_footer);
                                    for ($i = 1; $i < count($field_name_footer); $i += 2) {
                                        if (isset($cusData->$field_name_footer[$i])) {
                                            $noti_footer = str_replace("@@" . $field_name_footer[$i] . "@@", $cusData[$field_name_footer[$i]], $noti_footer);
                                        }
                                    }
                                    $msg .= $noti_msg;
                                }
                                $arraycontent = array(
                                    'notification_service' => 1,
                                    'header' => 'Daliy Rate',
                                    'message' => $msg,
                                    'token' => $rate[0]['token'],
                                    'targetUrl' => $targetUrl
                                );
                                $result = $this->send_singlealert_rate_notification($arraycontent);
                            }
                        }
                    }
                }
            } else {
                $data = $this->$model->get_cusnotiData('1');
                foreach ($data['data'] as $r) {
                    $arraycontent = array(
                        'notification_service' => 1,
                        'header' => $data['header'],
                        'message' => $r['message'],
                        'footer' => $data['footer'],
                        'targetUrl' => $targetUrl
                    );
                }
                $send = $this->sendPushNotificationToAll($arraycontent);
                $result['rate_noti'] = $send;
            }
        }
        return $result;
    }
    /**
     * Broadcast dispatcher. The original name became the dispatcher so no call
     * site changes; every platform is matched explicitly -- never a catch-all
     * else, or a future platform 3 would silently send via FCM.
     */
    function sendPushNotificationToAll($alertdetails = array())
    {
        $this->load->helper('push_notification');
        if (notify_platform_is('fcm')) {
            return $this->sendPushNotificationToAll_fcm($alertdetails);
        }
        return $this->sendPushNotificationToAll_onesignal($alertdetails);
    }
    function sendPushNotificationToAll_fcm($alertdetails = array())
    {
        $this->load->library('fcm_service');
        return $this->fcm_service->sendToAll(
            (isset($alertdetails['header']) ? $alertdetails['header'] : ''),
            (isset($alertdetails['message']) ? $alertdetails['message'] : ''),
            array(
                'targetUrl'    => (isset($alertdetails['targetUrl']) ? $alertdetails['targetUrl'] : '#/app/notification'),
                'noti_service' => (isset($alertdetails['notification_service']) ? $alertdetails['notification_service'] : ''),
                'mobile'       => ''
            ),
            (isset($alertdetails['noti_img']) ? $alertdetails['noti_img'] : '')
        );
    }
    function sendPushNotificationToAll_onesignal($alertdetails = array())
    {
        $this->load->helper('push_notification');
        $onesignal = notify_cred_for('onesignal');
        $content = array(
            "en" => $alertdetails['message']
        );
        //$targetUrl='#/app/notification';
        $fields = array(
            'app_id' => $onesignal['id'],
            'included_segments' => array(
                'All'
            ),
            'contents' => $content,
            'headings' => array("en" => $alertdetails['header']),
            'subtitle' => array("en" => $alertdetails['footer']),
            'data' => array('targetUrl' => $alertdetails['targetUrl'], 'noti_service' => $alertdetails['notification_service'], 'mobile' => ''),
            'big_picture' => (isset($alertdetails['noti_img']) ? $alertdetails['noti_img'] : " ")
        );
        $auth_key = $onesignal['key'];
        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . $auth_key
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    function ajax_paymentgateway()
    {
        $model = self::SET_MODEL;
        $pg_code = $this->input->post('pg_code');
        $type = $this->input->post('type');
        $id_branch = $this->input->post('id_branch');
        $data = $this->$model->ajax_gateway_settings($type, $pg_code, $id_branch);
        echo json_encode($data);
    }
    function update_gateway()
    {
        $model = self::SET_MODEL;
        $pg_code = $this->input->post('pg_code');
        $type = $this->input->post('type');
        $param_1 = (!empty($this->input->post('param_1')) ? $this->input->post('param_1') : NULL);
        $param_2 = (!empty($this->input->post('param_2')) ? $this->input->post('param_2') : NULL);
        $param_3 = (!empty($this->input->post('param_3')) ? $this->input->post('param_3') : NULL);
        $param_4 = (!empty($this->input->post('param_4')) ? $this->input->post('param_4') : NULL);
        $api_url = $this->input->post('api_url');
        $is_default = $this->input->post('is_default');
        $id_pg = $this->input->post('id_pg');
        $id_branch = $this->input->post('id_branch');
        $data = array("param_1" => $param_1, "param_2" => $param_2, "param_3" => $param_3, "param_4" => $param_4, "api_url" => $api_url, "is_default" => $is_default);
        $data = $this->$model->update_gateway($data, $id_pg, $id_branch);
        echo json_encode($data);
    }
    // metal_rates_discount	//
    public function metal_rates_discount()
    {
        $model = self::SET_MODEL;
        $data['general'] = $this->$model->settingsDB('get', 1, '');
        // echo "<pre>";	print_r($data);exit; echo "<pre>";
        $data['access'] = $this->$model->get_access('settings/rate/list');
        $data['main_content'] = "metal_rates/discount";
        $this->load->view('layout/template', $data);
    }
    public function update_discout()
    {
        $model = self::SET_MODEL;
        $general = $this->input->post('general');
        $gen_info = array(
            'enableGoldrateDisc' => (isset($general['enableGoldrateDisc']) ? $general['enableGoldrateDisc'] : 0),
            'goldDiscAmt' => (isset($general['goldDiscAmt']) ? $general['goldDiscAmt'] : 0),
            'enableGoldrateDisc_18k' => (isset($general['enableGoldrateDisc_18k']) ? $general['enableGoldrateDisc_18k'] : 0),
            'goldDiscAmt_18k' => (isset($general['goldDiscAmt_18k']) ? $general['goldDiscAmt_18k'] : 0),
            'enableSilver_rateDisc' => (isset($general['enableSilver_rateDisc']) ? $general['enableSilver_rateDisc'] : 0),
            'silverDiscAmt' => (isset($general['silverDiscAmt']) ? $general['silverDiscAmt'] : 0)
        );
        $status = $this->$model->settingsDB('update', 1, $gen_info);
        redirect('settings/rate/list');
    }
    // metal_rates_discount	//
    //village
    public function village_list()
    {
        $setmodel = "admin_settings_model";
        $data['main_content'] = self::MAS_VIEW . 'village/list';
        $data['access'] = $this->$setmodel->get_access('settings/village');
        $this->load->view('layout/template', $data);
    }
    public function ajax_village_list()
    {
        $model = self::SET_MODEL;
        $id_village = $this->input->post('id_village');
        // $data=$this->$model->ajax_village_list($id_village); 
        $data['list'] = $this->$model->ajax_village_list($id_village);
        $data['access'] = $this->$model->get_access('settings/village');
        echo json_encode($data);
    }
    function village_form($type = "", $id = "")
    {
        // 	 echo $type; exit;
        $model = self::SET_MODEL;
        switch ($type) {
            case "View":
                if ($id != NULL) {
                    $data['village'] = $this->$model->village_settingDB('get', $id);
                } else {
                    $data['village'] = $this->$model->village_settingDB();
                }
                $data['main_content'] = self::MAS_VIEW . 'village/form';
                $this->load->view('layout/template', $data);
                break;
            case "Save":
                $village = $this->input->post('village');
                //formatting form values
                $insertData = array(
                    'village_name' => (isset($village['village_name']) && $village['village_name'] != '' ? $village['village_name'] : NULL),
                    'post_office' => (isset($village['post_office']) && $village['post_office'] != '' ? $village['post_office'] : NULL),
                    'taluk' => (isset($village['taluk']) && $village['taluk'] != '' ? $village['taluk'] : NULL),
                    'pincode' => (isset($village['pincode']) && $village['pincode'] != '' ? $village['pincode'] : NULL),
                    'date_add' => date("Y-m-d H:i:s")
                );
                //inserting data  
                $this->db->trans_begin();
                $status = $this->$model->village_settingDB("insert", "", $insertData);
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Village inserted successfully', 'class' => 'success', 'title' => 'Village List'));
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Category List'));
                }
                redirect('settings/village');
                break;
            case "Update":
                //get form values
                $village = $this->input->post('village');
                //formatting form values
                $insertData = array(
                    'village_name' => (isset($village['village_name']) && $village['village_name'] != '' ? $village['village_name'] : NULL),
                    'post_office' => (isset($village['post_office']) && $village['post_office'] != '' ? $village['post_office'] : NULL),
                    'taluk' => (isset($village['taluk']) && $village['taluk'] != '' ? $village['taluk'] : NULL),
                    'pincode' => (isset($village['pincode']) && $village['pincode'] != '' ? $village['pincode'] : NULL),
                );
                //update data                  
                $this->db->trans_begin();
                $status = $this->$model->village_settingDB("update", $id, $insertData);
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Category Detalis Updated successfully', 'class' => 'success', 'title' => 'Category List'));
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Category List'));
                }
                redirect('settings/village');
                break;
            case 'delete':
                $status = $this->$model->village_settingDB($type, $id, '');
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Village Records deleted successfully', 'class' => 'success', 'title' => 'Category Records'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Category Records'));
                }
                redirect('settings/village');
                break;
            default:
                $data['category'] = $this->$model->village_settingDB('get', ($id != NULL ? $id : ''));
                echo json_encode($data);
                break;
        }
    }
    //village
    function get_reports()
    {
        $data['main_content'] = self::VIEW_FOLDER . 'unreg_cus_list';
        $this->load->view('layout/template', $data);
    }
    function download()
    {
        $file_name = 'unregistered_cus.csv';
        $mime = 'application/force-download';
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: close');
        readfile($file_name);
        exit();
    }
    function compress()
    {
        $this->load->library('zip');
        $path = 'Unregistered List/';
        $this->zip->read_dir($path);
        $this->zip->download('customer_list.zip');
        $data = array('File download successfully');
    }
    // Terms For App from admin side add//hh
    public function terms_conditions($type, $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case 'List': //showing list
                $data['main_content'] = self::VIEW_FOLDER . "terms/list";
                $this->load->view('layout/template', $data);
                break;
            case 'View': //showing form
                if ($id != NULL) {
                    $data['general'] = $this->$model->terms_and_conditions("get", $id);
                    // print_r($data);exit;
                } else {
                    $data['general'] = $this->$model->terms_and_conditions();
                }
                //  echo"<pre>"; print_r($data);exit;
                $data['main_content'] = self::VIEW_FOLDER . "terms/form";
                $this->load->view('layout/template', $data);
                break;
            case 'Save': //insert 
                $general = $this->input->post('general');
                $status = $this->$model->terms_and_conditions("insert", "", $general);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Profile added successfully', 'class' => 'success', 'title' => 'Profile'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Profile'));
                }
                redirect('settings/terms_and_conditions/list');
                break;
            case 'Update':
                $general = $this->input->post('general');
                $status = $this->$model->terms_and_conditions("update", $id, $general);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Terms and Conditions updated successfully', 'class' => 'success', 'title' => 'Terms and Conditions'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Terms and Conditions'));
                }
                redirect('settings/terms_and_conditions/list');
                break;
            case 'Delete':
                $status = $this->$model->terms_and_conditions("delete", $id);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Terms and Conditions deleted successfully', 'class' => 'success', 'title' => 'Terms and Conditions'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Terms and Conditions'));
                }
                redirect('settings/terms_and_conditions/list');
                break;
            default:
                $items = $this->$model->terms_and_conditions('get', ($id != NULL ? $id : ''));
                $general = array(
                    'terms' => $items
                );
                echo json_encode($general);
        }
    }
    function send_singlealert_rate_notification($alertdetails = array())
    {
        $this->load->helper('push_notification');
        if (notify_platform_is('fcm')) {
            return $this->send_singlealert_rate_notification_fcm($alertdetails);
        }
        return $this->send_singlealert_rate_notification_onesignal($alertdetails);
    }
    function send_singlealert_rate_notification_fcm($alertdetails = array())
    {
        $this->load->library('fcm_service');
        return $this->fcm_service->sendToTokens(
            (isset($alertdetails['token']) ? (array) $alertdetails['token'] : array()),
            (isset($alertdetails['header']) ? $alertdetails['header'] : ''),
            (isset($alertdetails['message']) ? $alertdetails['message'] : ''),
            array(
                'targetUrl'    => '#/app/notification',
                'noti_service' => (isset($alertdetails['notification_service']) ? $alertdetails['notification_service'] : '')
            ),
            (isset($alertdetails['noti_img']) ? $alertdetails['noti_img'] : '')
        );
    }
    function send_singlealert_rate_notification_onesignal($alertdetails = array())
    {
        $this->load->helper('push_notification');
        $onesignal = notify_cred_for('onesignal');
        $registrationIds = array();
        $registrationIds[0] = $alertdetails['token'];
        $content = array(
            "en" => $alertdetails['message']
        );
        $targetUrl = '#/app/notification';
        $fields = array(
            'app_id' => $onesignal['id'],
            'include_player_ids' => $registrationIds,
            'contents' => $content,
            'headings' => array("en" => $alertdetails['header']),
            'data' => array('targetUrl' => $targetUrl, 'noti_service' => $alertdetails['notification_service']),
            'big_picture' => (isset($alertdetails['noti_img']) ? $alertdetails['noti_img'] : " ")
        );
        $auth_key = $onesignal['key'];
        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . $auth_key
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        //print_r($response);exit;
        curl_close($ch);
        return $response;
    }
    //Gift Master
    public function gift($type, $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case 'List': //showing list
                //$data['gift']=$this->$model->giftDB('get',($id!=NULL?$id:''));    
                $data['main_content'] = self::MAS_VIEW . "gift/list";
                $data['access'] = $this->$model->get_access('settings/gift/list');
                $this->load->view('layout/template', $data);
                break;
            case 'View': //showing form
                if ($id != NULL) {
                    $data['gift'] = $this->$model->giftDB("get", $id);
                } else {
                    $data['gift'] = $this->$model->giftDB();
                }
                $data['main_content'] = self::MAS_VIEW . "gift/form";
                $this->load->view('layout/template', $data);
                break;
            case 'Save': //insert 
                $gift = $this->input->post('gift');
                $status = $this->$model->giftDB("insert", "", $gift);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Gift added successfully', 'class' => 'success', 'title' => 'Gift'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Gift'));
                }
                redirect('settings/gift/list');
                break;
            case 'Update':
                $gift = $this->input->post('gift');
                $status = $this->$model->giftDB("update", $id, $gift);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Gift updated successfully', 'class' => 'success', 'title' => 'Gift'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Gift'));
                }
                redirect('settings/gift/list');
                break;
            case 'Delete':
                $status = $this->$model->giftDB("delete", $id);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Gift deleted successfully', 'class' => 'success', 'title' => 'Gift'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Gift'));
                }
                redirect('settings/gift/list');
                break;
            default: //json data for listing
                $items = $this->$model->giftDB('get', ($id != NULL ? $id : ''));
                $gift = array(
                    'draw' => 0,
                    'recordsTotal' => count($items),
                    'recordsFiltered' => count($items),
                    'data' => $items
                );
                echo json_encode($gift);
        }
    }
    function ajax_get_gift()
    {
        $model = self::SET_MODEL;
        $items = $this->$model->giftDB('get');
        $access = $this->$model->get_access('settings/gift/list');
        $gift = array(
            'access' => $access,
            'data' => $items
        );
        echo json_encode($gift);
    }
    function update_gift_status($status, $id)
    {
        $model = self::SET_MODEL;
        $status = $this->$model->update_gift_status($status, $id);
        if ($status) {
            $this->session->set_flashdata('chit_alert', array('message' => 'Gift status updated as ' . ($status ? 'active' : 'inactive') . ' successfully.', 'class' => 'success', 'title' => 'Account Status'));
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Account Status'));
        }
        redirect('settings/gift/list');
    }
    function get_gift_name_byId()
    {
        $id = $_POST['id'];
        $model = self::SET_MODEL;
        $items = $this->$model->giftDB("get", $id, '');
        echo json_encode($items);
    }
    function get_all_gifts()
    {
        $model = self::SET_MODEL;
        $items = $this->$model->get_all_gifts();
        echo json_encode($items);
    }
    //16-12-2022,AB
    function update_gift()
    {
        if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == '') {
            $id_branch = $this->input->post('id_branch');
        } else {
            $id_branch = $this->session->userdata('id_branch');
        }
        $id = $this->input->post('id');
        $model = self::SET_MODEL;
        $gift = array(
            "gift_name" => $this->input->post('gift_name'),
            "gift_type" => $this->input->post('gift_type'),
            "metal" => $this->input->post('gift_metal'),
            "net_weight" => $this->input->post('gift_weight'),
            "quantity" => $this->input->post('gift_qty'),
            "id_branch" => $id_branch,
            "status" => $this->input->post('status')
        );
        $status = $this->$model->giftDB("update", $id, $gift);
        echo json_encode($status);
    }
    //16-12-2022,AB
    function add_gift()
    {
        if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == '') {
            $id_branch = $this->input->post('id_branch');
        } else {
            $id_branch = $this->session->userdata('id_branch');
        }
        $model = self::SET_MODEL;
        $gift = array(
            "gift_name" => $this->input->post('gift_name'),
            "gift_type" => $this->input->post('gift_type'),
            "metal" => $this->input->post('gift_metal'),
            "net_weight" => $this->input->post('gift_weight'),
            "quantity" => $this->input->post('gift_qty'),
            "id_branch" => $id_branch,
            "status" => $this->input->post('status'),
        );
        $status = $this->$model->giftDB("insert", "", $gift);
        echo json_encode($status);
    }
    function profession_form($type = "", $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case "Add":
                $profession = $this->input->post("profession");
                $profession_data = array('name' => $profession);
                $this->db->trans_begin();
                // coded by jothika on 10-7-2025 [prevent insert duplicate entries]
                $result = $this->$model->insert_profession($profession_data);
                if ($this->db->trans_status() === TRUE) {
                    if ($result === true) {
                        $this->db->trans_commit();
                        $this->session->set_flashdata('chit_alert', array('message' => 'New profession added successfully', 'class' => 'success', 'title' => 'Add Profession'));
                    } else {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('chit_alert', array('message' => 'Profession already exist', 'class' => 'danger', 'title' => 'Add Profession'));
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Add Profession'));
                }
                //redirect('settings/design');
                break;
            case "Edit":
                $data['profession'] = $this->$model->get_profession($id);
                echo json_encode($data['profession']);
                //$data['wt']=$weight['weight'];
                break;
            case "Delete":
                $status = $this->$model->delete_profession($id);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Profession deleted successfully', 'class' => 'success', 'title' => 'Delete Profession'));
                }
                redirect('settings/profession');
                //$data['wt']=$weight['weight'];
                break;
            case "Update":
                $data['profession'] = $this->$model->get_profession($id);
                //$id=$this->input->post('id_weight');
                $design = $this->input->post('profession');
                $data = array("name" => $design);
                $result = $this->$model->update_profession($data, $id);
                // coded by jothika on 11-7-2025 [prevent updating duplicate entries]
                if ($this->db->trans_status() === TRUE) {
                    if ($result === true) {
                        $this->db->trans_commit();
                        $this->session->set_flashdata('chit_alert', array('message' => 'Profession updated successfully', 'class' => 'success', 'title' => 'Edit Profession'));
                    } else {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('chit_alert', array('message' => 'Profession already exist', 'class' => 'danger', 'title' => 'Edit Profession'));
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Edit Profession'));
                }
                break;
            default:
                $data['main_content'] = self::MAS_VIEW . 'profession/list';
                $data['access'] = $this->$model->get_access('settings/profession');
                $this->load->view('layout/template', $data);
                break;
        }
    }
    function ajax_get_profession()
    {
        $model = self::SET_MODEL;
        $items = $this->$model->ajax_get_profession();
        $access = $this->$model->get_access('settings/profession');
        $depts = array(
            'access' => $access,
            'data' => $items
        );
        echo json_encode($depts);
    }
    function get_profession()
    {
        $data = $this->admin_settings_model->ajax_get_profession();
        echo json_encode($data);
    }
    function get_otp_giftstatus()
    {
        $model = self::SET_MODEL;
        $data['general'] = $this->$model->settingsDB('get', 1);
        //print_r($data[general]);
        echo json_encode($data);
    }
    // Added by durga 12.05.2023 starts here 
    public function version_details($type, $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case 'List': //showing list
                $data['version_data'] = $this->$model->versionDB('get', ($id != NULL ? $id : ''));
                $data['main_content'] = self::MAS_VIEW . "version/list";
                $this->load->view('layout/template', $data);
                break;
            case 'View': //showing form
                if ($id != NULL) {
                    $data['version_data'] = $this->$model->versionDB("get", $id);
                } else {
                    $data['version_data'] = $this->$model->versionDB();
                }
                $data['main_content'] = self::MAS_VIEW . "version/form";
                $this->load->view('layout/template', $data);
                break;
            case 'Save': //insert 
                $version_data = $this->input->post('version_data');
                //print_r($version_data);exit;
                $existing_version_data = $this->$model->get_version_data($version_data['version_no']);
                $count = count($existing_version_data);
                if ($count > 0) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Data Already Exist', 'class' => 'danger', 'title' => 'Version Details'));
                } else {
                    $company_data = $this->$model->get_company();
                    $version_data['client'] = $company_data['company_name'];
                    //print_r($version_data);exit;
                    $status = $this->$model->versionDB("insert", "", $version_data);
                    //print_r($status);exit;
                    if ($status['status']) {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Version added successfully', 'class' => 'success', 'title' => 'Version Details'));
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "http://pm.logimaxindia.com/index.php/version_tracker/add_version",
                            CURLOPT_HTTPHEADER => array(
                                "Content-Type: application/json"
                            ),
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => $version_data,
                        ));
                        $response = curl_exec($curl);
                        //print_r($response);exit;
                        $err = curl_error($curl);
                        curl_close($curl);
                        if ($err) {
                            //echo "cURL Error #:" . $err;exit;
                        } else {
                            $gatewayResponse = json_decode($response, true);
                        }
                    } else {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Version Details'));
                    }
                }
                redirect('settings/version/list');
                break;
            case 'Update':
                $version_data = $this->input->post('version_data');
                //print_r($version_data);exit;
                $status = $this->$model->versionDB("update", $id, $version_data);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Version updated successfully', 'class' => 'success', 'title' => 'Version Details'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Version Details'));
                }
                redirect('settings/version/list');
                break;
            case 'Delete':
                $status = $this->$model->versionDB("delete", $id);
                if ($status['status']) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Version deleted successfully', 'class' => 'success', 'title' => 'Version Details'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Version Details'));
                }
                redirect('settings/version/list');
                break;
            default: //json data for listing
                $items = $this->$model->versionDB('get', ($id != NULL ? $id : ''));
                $version_data = array(
                    'draw' => 0,
                    'recordsTotal' => count($items),
                    'recordsFiltered' => count($items),
                    'data' => $items
                );
                echo json_encode($version_data);
        }
    }
    function ajax_get_version()
    {
        $from_date = $_POST['from_date'];
        $to_date = $_POST['to_date'];
        $model = self::SET_MODEL;
        $items = $this->$model->versionDB('get', '', '', $from_date, $to_date);
        $access = $this->$model->get_access('settings/version/list');
        $version_data = array(
            'access' => $access,
            'data' => $items
        );
        //print_r($version_data);exit;
        echo json_encode($version_data);
    }
    public function get_last_version()
    {
        $model = self::SET_MODEL;
        $version_data = $this->$model->get_last_version();
        //print_r($version_data);exit;
        echo json_encode($version_data);
    }
    function get_page_title($url_name)
    {
        echo json_encode($url_name);
        //print_r($url_name);exit;
    }
    // Added by durga 12.05.2023 ends here 
    // Added by Durga for customized account and receipt number settings starts here 26-06-2023 
    function get_acc_format_details($acc_num_fields, $acc_rcpt_settingsfromdb)
    {
        $frmt_acc_code = [];
        if ($acc_rcpt_settingsfromdb['custom_AccDisplayFrmt'] != '' && $acc_rcpt_settingsfromdb['custom_AccDisplayFrmt'] != null) {
            $field_name = explode('@@', $acc_rcpt_settingsfromdb['custom_AccDisplayFrmt']);
            for ($i = 1; $i < count($field_name); $i += 2) {
                $frmt_acc_code[] = $field_name[$i];
            }
            $result['acc_num_fields'] = [];
            $result['selected_acc_num_fields'] = [];
            if (count($frmt_acc_code) != 0) {
                foreach ($acc_num_fields as $key => $acc_field) {
                    if (!(in_array($acc_field['value'], $frmt_acc_code))) {
                        $result['acc_num_fields'][] = $acc_field;
                    }
                }
                $t = 0;
                $s = 0;
                foreach ($result['acc_num_fields'] as $key => $field) {
                    if ($field['value'] == 'space') {
                        $t = 1;
                        break;
                    }
                }
                if ($t == 0) {
                    $result['acc_num_fields'][] = array(
                        'value' => 'space',
                        'text' => 'Space'
                    );
                }
                foreach ($result['acc_num_fields'] as $key => $field) {
                    if ($field['value'] == 'hyphen') {
                        $s = 1;
                        break;
                    }
                }
                if ($s == 0) {
                    $result['acc_num_fields'][] = array(
                        'value' => 'hyphen',
                        'text' => 'Hyphen(-)'
                    );
                }
                foreach ($frmt_acc_code as $format) {
                    for ($i = 0; $i <= count($acc_num_fields); $i++) {
                        if ($format == $acc_num_fields[$i]['value']) {
                            $result['selected_acc_num_fields'][] = $acc_num_fields[$i];
                            break;
                        }
                    }
                }
            }
        } else {
            $result['acc_num_fields'] = $acc_num_fields;
        }
        return $result;
    }
    function get_receipt_format_details($receipt_num_fields, $acc_rcpt_settingsfromdb)
    {
        $frmt_short_code = [];
        if ($acc_rcpt_settingsfromdb['custom_ReceiptDisplayFrmt'] != '' && $acc_rcpt_settingsfromdb['custom_ReceiptDisplayFrmt'] != null) {
            $field_name = explode('@@', $acc_rcpt_settingsfromdb['custom_ReceiptDisplayFrmt']);
            for ($i = 1; $i < count($field_name); $i += 2) {
                $frmt_short_code[] = $field_name[$i];
            }
            $data['receipt_num_fields'] = [];
            $data['selected_receipt_num_fields'] = [];
            if (count($frmt_short_code) != 0) {
                foreach ($receipt_num_fields as $key => $rcpt_field) {
                    if (!(in_array($rcpt_field['value'], $frmt_short_code))) {
                        $data['receipt_num_fields'][] = $rcpt_field;
                    }
                }
                $t = 0;
                $s = 0;
                foreach ($data['receipt_num_fields'] as $key => $field) {
                    if ($field['value'] == 'space') {
                        $t = 1;
                        break;
                    }
                }
                if ($t == 0) {
                    $data['receipt_num_fields'][] = array(
                        'value' => 'space',
                        'text' => 'Space'
                    );
                }
                foreach ($data['receipt_num_fields'] as $key => $field) {
                    if ($field['value'] == 'hyphen') {
                        $s = 1;
                        break;
                    }
                }
                if ($s == 0) {
                    $data['receipt_num_fields'][] = array(
                        'value' => 'hyphen',
                        'text' => 'Hyphen(-)'
                    );
                }
                foreach ($frmt_short_code as $format) {
                    for ($i = 0; $i <= count($receipt_num_fields); $i++) {
                        if ($format == $receipt_num_fields[$i]['value']) {
                            $data['selected_receipt_num_fields'][] = $receipt_num_fields[$i];
                            break;
                        }
                    }
                }
            }
        } else {
            //foreach($receipt_num_fields as $field)
            $data['receipt_num_fields'] = $receipt_num_fields;
        }
        return $data;
    }
    // Added by Durga for customized account and receipt number settings ends here 26-06-2023 
    // Added by gopal seperate function for app config settings starts here 20/07/2023
    function config_setting($id = 1)
    {
        $model = self::SET_MODEL;
        $data['main_content'] = "config_setting";
        $data['config'] = $this->$model->configDB('get', ($id != NULL ? $id : ''));
        // print_r($data['config']);exit;
        $data['general'] = $this->$model->settingsDB('get', $id);
        // print_r($data);exit;
        $this->load->view('layout/template', $data);
    }
    function config_setupdate($id = 1)
    {
        $model = self::SET_MODEL;
        $log_model = self::LOG_MODEL;
        $config = $this->input->post('config');
        $general = $this->input->post('general');
        //   print_r($general); exit;
        $config_dat = array(
            // 'auto_pay_approval'=>(isset($config['auto_pay_approval'])?$config['auto_pay_approval']:0),
            // 'integration_type'=>(isset($config['integration_type'])?$config['integration_type']:0),
            // 'auto_sync'=>(isset($config['auto_sync'])?$config['auto_sync']:0),
            // 'one_signal_app_id'=>(isset($config['one_signal_app_id'])?$config['one_signal_app_id']:NULL),
            // 'one_signal_auth_key'=>(isset($config['one_signal_auth_key'])?$config['one_signal_auth_key']:NULL),
            // 'whats_app_url'=>(isset($config['whats_app_url'])?$config['whats_app_url']:NULL),
            // 'instance_id'=>(isset($config['instance_id'])?$config['instance_id']:NULL),
            'app_cus_email' => (isset($config['app_cus_email']) ? $config['app_cus_email'] : 0),
            'app_cus_address1' => (isset($config['app_cus_address1']) ? $config['app_cus_address1'] : 0),
            'app_cus_address2' => (isset($config['app_cus_address2']) ? $config['app_cus_address2'] : 0),
            'app_cus_country' => (isset($config['app_cus_country']) ? $config['app_cus_country'] : 0),
            'app_cus_state' => (isset($config['app_cus_state']) ? $config['app_cus_state'] : 0),
            'app_cus_city' => (isset($config['app_cus_city']) ? $config['app_cus_city'] : 0),
            'app_cus_lastname' => (isset($config['app_cus_lastname']) ? $config['app_cus_lastname'] : 0),
            // 'zoop_enabled'=>(isset($config['zoop_enabled'])?$config['zoop_enabled']:0),
            // 'zoop_url'=>(isset($config['zoop_url'])?$config['zoop_url']:NULL),
            // 'zoop_agency_id'=>(isset($config['zoop_agency_id'])?$config['zoop_agency_id']:NULL),
            // 'zoop_api_key'=>(isset($config['zoop_api_key'])?$config['zoop_api_key']:NULL),
            // 'khimji_baseURL'=>(isset($config['khimji_baseURL'])?$config['khimji_baseURL']:NULL),
            // 'khimji_x_key'=>(isset($config['khimji_x_key'])?$config['khimji_x_key']:NULL),
            // 'khimji_auth'=>(isset($config['khimji_auth'])?$config['khimji_auth']:NULL),
            // 'sms_gateway'=>(isset($config['sms_gateway'])?$config['sms_gateway']:0),
            // 'show_gcode'=>(isset($config['show_gcode'])?$config['show_gcode']:0),
            // 'clt_id_code'=>(isset($config['clt_id_code'])?$config['clt_id_code']:NULL),
            'play_str_url' => (isset($config['play_str_url']) ? $config['play_str_url'] : NULL),
            'app_a_pack' => (isset($config['app_a_pack']) ? $config['app_a_pack'] : NULL),
            'app_i_pack' => (isset($config['app_i_pack']) ? $config['app_i_pack'] : NULL),
            // 'erp_base_url'=>(isset($config['erp_base_url'])?$config['erp_base_url']:NULL),
            // 'ej_usr_nm'=>(isset($config['ej_usr_nm'])?$config['ej_usr_nm']:NULL),
            // 'ej_pwd'=>(isset($config['ej_pwd'])?$config['ej_pwd']:NULL),
            'current_android_version' => (isset($config['current_android_version']) ? $config['current_android_version'] : NULL),
            'new_android_version' => (isset($config['new_android_version']) ? $config['new_android_version'] : NULL),
            'current_ios_version' => (isset($config['current_ios_version']) ? $config['current_ios_version'] : NULL),
            'new_ios_version' => (isset($config['new_ios_version']) ? $config['new_ios_version'] : NULL),
            'show_language' => (isset($config['show_language']) ? $config['show_language'] : NULL),
            'show_close' => (isset($config['show_close']) ? $config['show_close'] : NULL),
            'show_wgt_history' => (isset($config['show_wgt_history']) ? $config['show_wgt_history'] : NULL),
            'show_amt_history' => (isset($config['show_amt_history']) ? $config['show_amt_history'] : NULL),
        );
        $status_config = $this->$model->configDB('update', $id, $config_dat);
        //added by durga 30/12/2022 ends here 
        $gen_info = array(
            'enable_digi_gold' => (isset($general['enable_digi_gold']) ? $general['enable_digi_gold'] : 0),
            'show_video_shop' => (isset($general['show_video_shop']) ? $general['show_video_shop'] : 0),
            'show_customer_order' => (isset($general['show_customer_order']) ? $general['show_customer_order'] : 0),
            'is_pin_required' => (isset($general['is_pin_required']) ? $general['is_pin_required'] : 0),
        );
        $status = $this->$model->settingsDB('update', 1, $gen_info);
        $log_data = array(                                                                //general log details
            'id_log' => $this->id_log,
            'event_date' => date("Y-m-d H:i:s"),
            'module' => 'Config Settings',
            'operation' => 'update',
            'record' => $general[tab_name],
            'remark' => 'Settings edited successfully'
        );
        $this->$log_model->log_detail('insert', '', $log_data);
        if( $status_config && $status) {
            $this->session->set_flashdata('chit_alert', array('message' => 'App Configuration Settings updated successfully', 'class' => 'success', 'title' => 'Configuration Settings'));
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Configuration Settings'));
        }
        redirect('admin_settings/config_setting');
    }
    // Added by gopal seperate function for app config settings Ends here 20/07/2023
    public function quick_link($id = "")
    {
        $model = self::SET_MODEL;
        $link_ids = $this->input->post('link_id');
        // print_r($link_ids);exit;
        $this->db->trans_begin();
        foreach ($link_ids as $link_id) {
            $this->$model->deleteData('id_menu', $link_id, 'ret_quick_link');
            $insData = array(
                'id_menu' => $link_id,
                'created_time' => date("Y-m-d H:i:s"),
                'created_by' => $this->session->userdata('uid'),
                'status' => 1
            );
            $this->$model->insertData($insData, 'ret_quick_link');
        }
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $return_data = array('status' => TRUE, 'message' => 'Menu Link Added Successfully');
            echo json_encode($return_data);
            // redirect('settings/menu/list');
        } else {
            $this->db->trans_rollback();
            $return_data = array('status' => FALSE, 'message' => 'Unable to proceed with the requested process');
            echo json_encode($return_data);
        }
    }
    public function quick_link_revert($id = "")
    {
        $model = self::SET_MODEL;
        $link_ids = $this->input->post('link_id');
        // print_r($link_ids);exit;
        $this->db->trans_begin();
        foreach ($link_ids as $link_id) {
            $data = array(
                'status' => 0,
                'updated_time' => date("Y-m-d H:i:s"),
                'updated_by' => $this->session->userdata('uid')
            );
            $this->$model->updateData($data, 'id_menu', $link_id, 'ret_quick_link');
            // print_r($this->db->last_query());exit;
        }
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $return_data = array('status' => TRUE, 'message' => 'Menu Link Status Updated Successfully');
            echo json_encode($return_data);
        } else {
            $this->db->trans_rollback();
            $return_data = array('status' => FALSE, 'message' => 'Unable to proceed with the requested process');
            echo json_encode($return_data);
        }
    }
    // Notification master--start
    public function notification($type, $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case 'List': //showing form
                $data['main_content'] = self::MAS_VIEW . "notification/list";
                $this->load->view('layout/template', $data);
                break;
            default: //json data for listing
                $getnotification = $this->$model->getnotification();
                echo json_encode($getnotification);
        }
    }
    function update_notification_status($status, $id)
    {
        $model = self::SET_MODEL;
        $sts = $this->$model->update_notification_status($status, $id);
        if ($sts) {
            $this->session->set_flashdata('chit_alert', array('message' => 'Notification status updated as ' . ($status == 1 ? 'active' : 'inactive') . ' successfully.', 'class' => 'success', 'title' => 'Notification Status'));
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Notification Status'));
        }
        redirect('settings/notification/list');
    }
    // Notification master End 

    function get_languages_known()
    {
        $data = $this->admin_settings_model->get_languages_known();
        echo $data;
    }

    public function ledger($type = '', $id = "")
    {
        $model = self::SET_MODEL;
        switch ($type) {
            case 'List':
                $data['ledgers'] = $this->$model->ledgerDB('get');
                $data['main_content'] = self::MAS_VIEW . "ledger/list";
                $this->load->view('layout/template', $data);
                break;

            case 'View':
                $data['banks'] = $this->$model->bankDB('get');
                  // $data['devices'] = $this->$model->deviceDB('get');
                $ledger_id = ($id != NULL) ? $id : 0;
                $data['devices'] = $this->$model->getAvailablePosDevices($ledger_id);

                if ($id != NULL) {
                    $data['ledger'] = $this->$model->ledgerDB('get', $id);
                } else {
                    $data['ledger'] = array(
                        'ledger_name' => '',
                        'mappings' => array()
                    );
                }

                $data['main_content'] = self::MAS_VIEW . "ledger/form";
                $this->load->view('layout/template', $data);
                break;

            case 'Save':
                    $ledger_name = $this->input->post('ledger_name');
                $opening_balance = $this->input->post('opening_balance'); 
                    $opening_date = $this->input->post('opening_date');
                    $min_balance = $this->input->post('min_balance');
                    $bank_ids = $this->input->post('bank_ids');
                    $paymode_ids = $this->input->post('paymode_ids');

                    $mappings = array();

                    if (!empty($bank_ids)) {
                        foreach ($bank_ids as $key => $bank_id) {
                            $mappings[] = array(
                                'type' => 'BANK',
                                'reference_id' => $bank_id
                            );
                        }
                    }

                    if (!empty($paymode_ids)) {
                        foreach ($paymode_ids as $key => $mode_id) {
                            $mappings[] = array(
                                'type' => 'PAYMODE',
                                'reference_id' => $mode_id
                            );
                        }
                    }

                    $data = array(
                        'ledger_name' => $ledger_name,
                    'opening_balance' => $opening_balance, 
                        'opening_date' => $opening_date,
                        'min_balance' => $min_balance,
                        'mappings' => $mappings
                    );

                    $result = $this->$model->ledgerDB('insert', '', $data);

                    if ($this->input->is_ajax_request()) {
                        echo json_encode(array(
                            'status' => $result ? 'success' : 'error',
                            'message' => $result ? 'Bank Ledger Added Successfully' : 'Failed to Add Bank Ledger',
                            'title' => $result ? 'Success' : 'Error',
                            'redirect' => site_url('settings/ledger/list')
                        ));
                }
                break;

            case 'Update':
                $ledger_name = $this->input->post('ledger_name');
                $opening_balance = $this->input->post('opening_balance'); // Capture Opening Balance
                $opening_date = $this->input->post('opening_date');
                $min_balance = $this->input->post('min_balance');
                $bank_ids = $this->input->post('bank_ids');
                $paymode_ids = $this->input->post('paymode_ids');

                $mappings = array();

                if (!empty($bank_ids)) {
                    foreach ($bank_ids as $key => $bank_id) {
                        $mappings[] = array(
                            'type' => 'BANK',
                            'reference_id' => $bank_id
                        );
                    }
                }

                if (!empty($paymode_ids)) {
                    foreach ($paymode_ids as $key => $mode_id) {
                        $mappings[] = array(
                            'type' => 'PAYMODE',
                            'reference_id' => $mode_id
                        );
                    }
                }

                $data = array(
                    'ledger_name' => $ledger_name,
                    'opening_balance' => $opening_balance, // Pass to model
                    'opening_date' => $opening_date,
                    'min_balance' => $min_balance,
                    'mappings' => $mappings
                );

                $result = $this->$model->ledgerDB('update', $id, $data);

                if ($this->input->is_ajax_request()) {
                    echo json_encode(array(
                        'status' => $result ? 'success' : 'error',
                        'message' => $result ? 'Bank Ledger Updated Successfully' : 'Failed to Update Ledger',
                        'title' => $result ? 'Success' : 'Error',
                        'redirect' => site_url('settings/ledger/list')
                    ));
                }
                break;
            case 'Delete':
                $result = $this->$model->ledgerDB('delete', $id);
                if ($result) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Bank Ledger Deleted Successfully', 'class' => 'success', 'title' => 'Success'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Failed to Delete Ledger', 'class' => 'danger', 'title' => 'Error'));
                }
                redirect('settings/ledger/list');
                break;
        }
    }

    /**
     * APP Notification settings (push provider + credentials).
     *
     * Posted from its own <form> in settings/general/form.php as
     * app_notify[notify_platform] and app_notify[cred][<slug>][id|key].
     * Every platform's credentials are stored, so switching provider never
     * destroys the other's -- the radio itself is the rollback mechanism.
     *
     * Writes through the shared settings writer; no bespoke save function.
     */
    function app_notification_settings($type = "", $id = 1)
    {
        $model = self::SET_MODEL;
        $this->load->helper('push_notification');

        if ($type != 'Update' && $type != 'Save') {
            redirect('settings/general/edit/1');
            return;
        }

        $post      = $this->input->post('app_notify');
        $platforms = notify_platform_map();

        if (!is_array($post)) {
            $this->session->set_flashdata('chit_alert', array('message' => 'No notification settings received', 'class' => 'danger', 'title' => 'APP Notification'));
            redirect('settings/general/edit/' . $id);
            return;
        }

        // --- 1. Platform must be in the whitelist ---
        $platform = isset($post['notify_platform']) ? (int) $post['notify_platform'] : 0;
        if (!isset($platforms[$platform])) {
            $this->session->set_flashdata('chit_alert', array('message' => 'Invalid notification provider selected', 'class' => 'danger', 'title' => 'APP Notification'));
            redirect('settings/general/edit/' . $id);
            return;
        }
        $active_slug = $platforms[$platform]['slug'];

        // --- 2. Collect credentials for EVERY platform ---
        $cred = array();
        foreach ($platforms as $p) {
            $slug = $p['slug'];
            $cred[$slug] = array(
                'id'  => isset($post['cred'][$slug]['id'])  ? trim((string) $post['cred'][$slug]['id'])  : '',
                'key' => isset($post['cred'][$slug]['key']) ? trim((string) $post['cred'][$slug]['key']) : '',
            );
        }

        // --- 3. Validate the ACTIVE platform only ---
        $error = '';
        if ($cred[$active_slug]['id'] === '' || $cred[$active_slug]['key'] === '') {
            $error = $platforms[$platform]['label'] . ' requires both ' . $platforms[$platform]['id_label'] . ' and ' . $platforms[$platform]['key_label'];
        } elseif ($active_slug === 'fcm') {
            $sa = json_decode($cred['fcm']['key'], TRUE);
            if (!is_array($sa)) {
                $error = 'Service Account JSON is not valid JSON';
            } elseif (empty($sa['project_id']) || empty($sa['client_email']) || empty($sa['private_key'])) {
                $error = 'Service Account JSON must contain project_id, client_email and private_key';
            } elseif ($sa['project_id'] !== $cred['fcm']['id']) {
                $error = 'Project ID does not match project_id in the Service Account JSON ("' . $sa['project_id'] . '")';
            }
        }

        if ($error !== '') {
            $this->session->set_flashdata('chit_alert', array('message' => $error, 'class' => 'danger', 'title' => 'APP Notification'));
            redirect('settings/general/edit/' . $id);
            return;
        }

        // --- 4. Save through the shared settings writer ---
        $status = $this->$model->settingsDB('update', $id, array(
            'notify_platform' => $platform,
            'notify_cred'     => json_encode($cred)
        ));

        if (!empty($status['status'])) {
            $this->session->set_flashdata('chit_alert', array('message' => 'APP Notification settings saved. Active provider: ' . $platforms[$platform]['label'], 'class' => 'success', 'title' => 'APP Notification'));
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to save APP Notification settings', 'class' => 'danger', 'title' => 'APP Notification'));
        }
        redirect('settings/general/edit/' . $id);
    }

    public function kyc_master(){

        $data = $this->admin_settings_model->kyc_master();
        echo json_encode($data);
    }

    public function get_active_schemes(){

        $data = $this->payment_model->get_active_scheme();
        echo json_encode($data);
    }

    public function kyc_settings($type){
		// print_r($type);exit;
        if ($type == 'update') {
            $kyc = $this->input->post('kyc');

            // --- 1. Prepare settings data ---
            $settings_data = array(
                'kyc_required'          => isset($kyc['kyc_required']) ? $kyc['kyc_required'] : 0,
                'kyc_mode'              => isset($kyc['kyc_mode']) ? $kyc['kyc_mode'] : 0,
                'kyc_integration_type'  => isset($kyc['kyc_integration_type']) ? $kyc['kyc_integration_type'] : 0,
                'kyc_verification_type' => isset($kyc['kyc_verification_type']) ? $kyc['kyc_verification_type'] : 0,
                'kyc_allow_type'        => isset($kyc['kyc_allow_type']) ? $kyc['kyc_allow_type'] : 0,
            );

            // --- 2. Build rules array ---
            $rules_data = array();
            $kyc_mode = isset($kyc['kyc_mode']) ? $kyc['kyc_mode'] : 0;

            if ($kyc_mode == 0) {
                // Scheme Wise
                $scheme_rules = isset($kyc['kyc_scheme_rules']) ? json_decode($kyc['kyc_scheme_rules'], true) : array();
                if (is_array($scheme_rules) && count($scheme_rules) > 0) {
                    foreach ($scheme_rules as $rule) {
                        $kyc_docs = isset($rule['kyc_docs']) ? $rule['kyc_docs'] : array();
                        if (!is_array($kyc_docs)) $kyc_docs = array($kyc_docs);
                        
                        $target_schemes = isset($rule['scheme']) ? $rule['scheme'] : array();
                        if (!is_array($target_schemes)) $target_schemes = array($target_schemes);
                        
                        if (count($target_schemes) > 0) {
                            foreach ($target_schemes as $sch_id) {
                                if (!empty($sch_id)) {
                                    if (count($kyc_docs) > 0) {
                                        foreach ($kyc_docs as $doc_id) {
                                            if (!empty($doc_id)) {
                                                $rules_data[] = array(
                                                    'id_scheme'   => $sch_id,
                                                    'id_mas_kyc'  => $doc_id,
                                                    'rules'       => isset($rule['when']) ? $rule['when'] : null,
                                                    'type'        => isset($rule['type']) ? $rule['type'] : null,
                                                    'amount'      => isset($rule['amount']) && $rule['amount'] != '' ? $rule['amount'] : null,
                                                    'logic'       => isset($rule['logic']) ? $rule['logic'] : 0,
                                                );
                                            }
                                        }
                                    } else {
                                        $rules_data[] = array(
                                            'id_scheme'   => $sch_id,
                                            'id_mas_kyc'  => null,
                                            'rules'       => isset($rule['when']) ? $rule['when'] : null,
                                            'type'        => isset($rule['type']) ? $rule['type'] : null,
                                            'amount'      => isset($rule['amount']) && $rule['amount'] != '' ? $rule['amount'] : null,
                                            'logic'       => isset($rule['logic']) ? $rule['logic'] : 0,
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                // Customer Wise
                $cust_rules = isset($kyc['kyc_customer_rules']) ? json_decode($kyc['kyc_customer_rules'], true) : array();
                if (is_array($cust_rules) && count($cust_rules) > 0) {
                    foreach ($cust_rules as $crule) {
                        $kyc_docs = isset($crule['attribute']) ? $crule['attribute'] : array();
                        if (!is_array($kyc_docs)) $kyc_docs = array($kyc_docs);
                        
                        if (count($kyc_docs) > 0) {
                            foreach ($kyc_docs as $doc_id) {
                                if (!empty($doc_id)) {
                                    $rules_data[] = array(
                                        'id_scheme'   => null,
                                        'id_mas_kyc'  => $doc_id,
                                        'rules'       => isset($crule['when']) ? $crule['when'] : null,
                                        'type'        => null,
                                        'amount'      => isset($crule['amount']) && $crule['amount'] != '' ? $crule['amount'] : null,
                                        'logic'       => isset($crule['logic']) ? $crule['logic'] : 0,
                                    );
                                }
                            }
                        }
                    }
                }
            }

            // --- 3. Save via model ---
            $uid = $this->session->userdata('uid');
            $result = $this->admin_settings_model->save_kyc_settings($settings_data, $rules_data, $uid);

            if ($result) {
                $this->session->set_flashdata('chit_alert', array('message' => 'KYC Settings updated successfully', 'class' => 'success', 'title' => 'KYC Settings'));
            } else {
                $this->session->set_flashdata('chit_alert', array('message' => 'Unable to save KYC settings', 'class' => 'danger', 'title' => 'KYC Settings'));
            }
            redirect('settings/general/edit/1');
        }
    }
    // ─── Feature Flags AJAX ───────────────────────────────────────────────────

    /**
     * AJAX GET: returns current feature_flags JSON from chit_settings row 1
     * URL: settings/get_feature_flags
     */
    public function get_feature_flags()
    {
        $model = self::SET_MODEL;
        $flags = $this->$model->get_feature_flags(1);
        header('Content-Type: application/json');
        echo json_encode(array('status' => 'ok', 'flags' => $flags));
    }

    /**
     * AJAX POST: saves feature_flags JSON to chit_settings row 1
     * URL: settings/save_feature_flags
     * POST body: flags_json = JSON string of [{code, description, status}, ...]
     */
    public function save_feature_flags()
    {
        $model = self::SET_MODEL;
        $raw   = $this->input->post('flags_json');
        $flags = @json_decode($raw, true);
        if (!is_array($flags)) {
            echo json_encode(array('status' => 'error', 'message' => 'Invalid JSON payload'));
            return;
        }
        // Sanitize each flag
        $clean = array();
        foreach ($flags as $f) {
            $code = strtoupper(trim(isset($f['code']) ? $f['code'] : ''));
            if ($code === '') continue;
            $clean[] = array(
                'code'        => $code,
                'description' => trim(isset($f['description']) ? $f['description'] : ''),
                'status'      => (int)(isset($f['status']) ? $f['status'] : 0),
            );
        }
        $ok = $this->$model->save_feature_flags($clean, 1);
        header('Content-Type: application/json');
        if ($ok) {
            echo json_encode(array('status' => 'ok', 'message' => 'Feature flags saved successfully.'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Unable to save feature flags.'));
        }
    }

}