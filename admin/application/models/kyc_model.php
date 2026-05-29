<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kyc_model extends CI_Model
{
    const KYC_BASE_PATH = 'assets/kyc/';

	function __construct()
    {
        parent::__construct();
    }

    public function get_required_kyc_for_customer_form() {
        $settings = $this->db->get('kyc_settings')->row_array();
        if (!$settings || $settings['kyc_required'] == 0) {
            return array();
        }
        $rule_groups = array();
        if ($settings['kyc_mode'] == 1) {
            $this->db->where('status', 1);
            $this->db->where('rules', 0);
            $rules = $this->db->get('kyc_rules')->result_array();
            foreach($rules as $r) {
                if ($r['id_mas_kyc']) {
                    $group_key = 'rule_' . $r['rules'] . '_' .  $r['logic'];
                    if (!isset($rule_groups[$group_key])) {
                        $rule_groups[$group_key] = array('logic' => $r['logic'], 'docs' => array());
                    }
                    $rule_groups[$group_key]['docs'][] = $r['id_mas_kyc'];
                }
            }
        }
        return $rule_groups;
    }

    public function get_required_kyc_for_scheme_account($id_scheme, $pay_amount = 0, $id_customer = 0, $id_scheme_account = 0) {
        $settings = $this->db->get('kyc_settings')->row_array();
        if (!$settings || $settings['kyc_required'] == 0) {
            return array();
        }

        $rule_groups = array();
        $cus_overall_amount = 0;
        $total_paid_amount = 0;
        $paid_installments = 0;
        
        if ($id_customer > 0) {
            $this->db->select('SUM(py.payment_amount) as overall_t');
            $this->db->from('payment py');
            $this->db->join('scheme_account sch_acc', 'sch_acc.id_scheme_account = py.id_scheme_account', 'left');
            $this->db->where('sch_acc.active', 1);
            $this->db->where('sch_acc.is_closed', 0);
            $this->db->where('py.payment_status', 1);
            $this->db->where('sch_acc.id_customer', $id_customer);
            $res = $this->db->get()->row_array();
            $cus_overall_amount = ($res && $res['overall_t']) ? $res['overall_t'] : 0;
        }

        if ($id_scheme_account > 0) {
            $this->db->select('SUM(py.payment_amount*py.no_of_dues) as scheme_t');
            $this->db->from('payment py');
            $this->db->where('py.id_scheme_account', $id_scheme_account);
            $this->db->where('py.payment_status', 1);
            $res2 = $this->db->get()->row_array();
            
            $this->db->where('id_scheme_account', $id_scheme_account);
            $acc = $this->db->get('scheme_account')->row_array();
            if ($acc && $acc['is_opening'] == 1) {
                $total_paid_amount = $acc['balance_amount'] + (($res2 && $res2['scheme_t']) ? $res2['scheme_t'] : 0);
            } else {
                $total_paid_amount = ($res2 && $res2['scheme_t']) ? $res2['scheme_t'] : 0;
            }
            $paidInstallmentsSQL = $this->get_paid_installments_sql('p_sub', 'sa_sub', 's_sub');
            $this->db->select("($paidInstallmentsSQL) as paid_installments", FALSE);
            $this->db->from('scheme_account sa_sub');
            $this->db->join('scheme s_sub', 's_sub.id_scheme = sa_sub.id_scheme', 'left');
            $this->db->join('payment p_sub', 'p_sub.id_scheme_account = sa_sub.id_scheme_account AND p_sub.payment_status = 1', 'left');
            $this->db->where('sa_sub.id_scheme_account', $id_scheme_account);
            $this->db->group_by('sa_sub.id_scheme_account');
            $pi_row = $this->db->get()->row_array();
            if ($pi_row && isset($pi_row['paid_installments'])) {
                $paid_installments = $pi_row['paid_installments'];
            }
        }

        $this->db->where('status', 1);
        $rules = $this->db->get('kyc_rules')->result_array();

        foreach($rules as $r) {
            $triggered = false;
            if ($settings['kyc_mode'] == 1) {
                if ($r['rules'] == 1) { 
                    if ($r['amount'] > 0 && ($cus_overall_amount + $pay_amount) >= $r['amount']) {
                        $triggered = true;
                    }
                }
            } else if ($settings['kyc_mode'] == 0) {
                if ($r['id_scheme'] == $id_scheme || empty($r['id_scheme']) || $r['id_scheme'] == 0) {
                    if ($r['rules'] == 0 && $id_scheme_account == 0) {
                        $triggered = true;
                    } else if ($r['rules'] == 1) {
                        if (isset($r['type']) && $r['type'] == 1) {
                            if ($r['amount'] > 0 && ($paid_installments + 1) >= $r['amount']) {
                                $triggered = true;
                            }
                        } else {
                            if ($r['amount'] > 0 && ($total_paid_amount + $pay_amount) >= $r['amount']) {
                                $triggered = true;
                            }
                        }
                    }
                }
            }
            if ($triggered && $r['id_mas_kyc']) {
                $group_key = 'rule_' . $r['id_scheme'] . '_' . $r['rules'] . '_' . $r['type'] . '_' . $r['amount'] . '_' . $r['logic'];
                if (!isset($rule_groups[$group_key])) {
                    $rule_groups[$group_key] = array('logic' => $r['logic'], 'docs' => array());
                }
                if (!in_array($r['id_mas_kyc'], $rule_groups[$group_key]['docs'])) {
                    $rule_groups[$group_key]['docs'][] = $r['id_mas_kyc'];
                }
            }
        }
        return $rule_groups;
    }

    public function get_paid_installments_sql($p = 'p', $sa = 'sa', $s = 's')
    {
        return "
        IFNULL(
            IF(
                {$sa}.is_opening = 1,
                IFNULL({$sa}.paid_installments, 0) +
                IFNULL(
                    IF(
                        {$s}.scheme_type = 1 AND {$s}.min_weight != {$s}.max_weight,
                        COUNT(DISTINCT DATE_FORMAT({$p}.date_payment,'%Y%m')),
                        SUM({$p}.no_of_dues)
                    ), 0
                ),
                IF(
                    ({$s}.scheme_type = 1 AND {$s}.min_weight != {$s}.max_weight) 
                    OR {$s}.scheme_type = 3,
                    COUNT(DISTINCT DATE_FORMAT({$p}.date_payment,'%Y%m')),
                    SUM({$p}.no_of_dues)
                )
            ),  
        0)";
    }

    public function get_customer_kyc($cus_id) {
        $this->db->where('id_customer', $cus_id);
        $this->db->where('status !=', 3);
        $result = $this->db->get('kyc')->result_array();
        $kyc_data = array();
        foreach ($result as $row) {
            $kyc_data[$row['kyc_type']] = $row;
        }
        return $kyc_data;
    }

    public function get_unified_dynamic_kyc_data($id_scheme = 0, $id_customer = 0, $pay_amount = 0, $id_scheme_account = 0) {
        $data = array();
        $customer_kyc = array();
        if ($id_customer > 0) {
            $customer_kyc = $this->get_customer_kyc($id_customer);
            $data['customer_kyc'] = $customer_kyc;
        } else {
            $data['customer_kyc'] = array();
        }

        if ($id_scheme == 0 && $pay_amount == 0 && $id_scheme_account == 0) {
            $rule_groups = $this->get_required_kyc_for_customer_form();
            $data['kyc_context_message'] = "Please provide the mandatory KYC documents to complete the customer profile.";
        } else {
            $rule_groups = $this->get_required_kyc_for_scheme_account($id_scheme, $pay_amount, $id_customer, $id_scheme_account);
            $data['kyc_context_message'] = "Please provide the mandatory KYC documents to proceed.";
        }

        $final_required_docs = array();
        $unmet_rule_groups = array();
        $has_logic_0 = false;
        $has_logic_1 = false;

        foreach ($rule_groups as $key => $group) {
            if ($group['logic'] == 1) { // At Least One
                $has_one = false;
                foreach ($group['docs'] as $doc_id) {
                    if (isset($customer_kyc[$doc_id])) {
                        $has_one = true; break;
                    }
                }
                if (!$has_one) {
                    $unmet_rule_groups[$key] = $group;
                    $has_logic_1 = true;
                    foreach ($group['docs'] as $doc_id) {
                        $final_required_docs[$doc_id] = true;
                    }
                }
            } else { // All Required
                $has_missing = false;
                foreach ($group['docs'] as $doc_id) {
                    if (!isset($customer_kyc[$doc_id])) {
                        $final_required_docs[$doc_id] = true;
                        $has_missing = true;
                    }
                }
                if ($has_missing) {
                     $unmet_rule_groups[$key] = $group;
                     $has_logic_0 = true;
                }
            }
        }

        $kyc_master_list = array();
        if (!empty($final_required_docs)) {
            $allowed_kyc_ids = array_keys($final_required_docs);
            $this->db->where('status', 1);
            $this->db->where_in('id_mas_kyc', $allowed_kyc_ids);
            $this->db->order_by('sort', 'ASC');
            $kyc_masters = $this->db->get('kyc_master')->result_array();
            foreach ($kyc_masters as &$master) {
                $this->db->where('id_mas_kyc', $master['id_mas_kyc']);
                $this->db->where('status', 1);
                $this->db->order_by('position', 'ASC');
                $master['attributes'] = $this->db->get('kyc_attribute')->result_array();
            }
            $kyc_master_list = $kyc_masters;
        }

        $data['kyc_master_list'] = $kyc_master_list;
        $data['unmet_rule_groups'] = $unmet_rule_groups;
        $data['id_customer'] = $id_customer;
        
        $active_logic = 0; // Default to all required
        if ($has_logic_1 && !$has_logic_0) {
            $active_logic = 1;
        }
        $data['active_rule_logic'] = $active_logic;
        return $data;
    }

    public function save_scheme_account_kyc($cus_id, $id_scheme, $pay_amount, $kyc_dynamic_post, $id_scheme_account = 0) {
        $data = $this->get_unified_dynamic_kyc_data($id_scheme, $cus_id, $pay_amount, $id_scheme_account);
        $kyc_master_list = $data['kyc_master_list'];
        $unmet_rule_groups = $data['unmet_rule_groups'];
        
        if (!is_array($kyc_dynamic_post)) $kyc_dynamic_post = array();

        $validation = $this->validate_kyc_payload($unmet_rule_groups, $kyc_dynamic_post, $kyc_master_list);
        if (!$validation['status']) {
            return $validation;
        }

        $this->_process_save_kyc($cus_id, $kyc_dynamic_post, $kyc_master_list);
        return array('status' => true, 'message' => 'KYC Profile Configured Successfully');
    }

    public function save_customer_kyc($cus_id, $kyc_dynamic_post) {
        $data = $this->get_unified_dynamic_kyc_data(0, $cus_id, 0, 0);
        $kyc_master_list = $data['kyc_master_list'];
        $unmet_rule_groups = $data['unmet_rule_groups'];
        
        if (!is_array($kyc_dynamic_post)) $kyc_dynamic_post = array();

        $validation = $this->validate_kyc_payload($unmet_rule_groups, $kyc_dynamic_post, $kyc_master_list);
        if (!$validation['status']) {
            return $validation;
        }

        $this->_process_save_kyc($cus_id, $kyc_dynamic_post, $kyc_master_list);
        return array('status' => true, 'message' => 'Customer KYC Saved Successfully');
    }

    public function validate_kyc_payload($unmet_rule_groups, $kyc_dynamic_post, $kyc_master_list) {
        if (empty($unmet_rule_groups)) {
            return array('status' => true); // Nothing required
        }

        $doc_submitted_cache = array();

        foreach ($kyc_master_list as $kyc) {
            $id_mas = $kyc['id_mas_kyc'];
            $is_submitted = false;

            if (isset($kyc_dynamic_post[$id_mas])) {
                $data = $kyc_dynamic_post[$id_mas];
                $has_data = false;
                foreach ($kyc['attributes'] as $attr) {
                    $attr_name = $attr['attribute'];
                    if (isset($data[$attr_name]) && trim($data[$attr_name]) !== '') {
                        $has_data = true;
                        break;
                    }
                    if (isset($_FILES["kyc_dynamic_file_{$id_mas}_{$attr_name}"]) && !empty($_FILES["kyc_dynamic_file_{$id_mas}_{$attr_name}"]['name'])) {
                        $has_data = true;
                        break;
                    }
                }
                if ($has_data) {
                    $is_submitted = true;
                }
            }
            $doc_submitted_cache[$id_mas] = $is_submitted;
        }

        foreach ($unmet_rule_groups as $group) {
            if ($group['logic'] == 1) {
                // At Least One
                $submitted_for_group = 0;
                foreach ($group['docs'] as $doc_id) {
                    if (isset($doc_submitted_cache[$doc_id]) && $doc_submitted_cache[$doc_id]) {
                        $submitted_for_group++;
                    }
                }
                if ($submitted_for_group == 0) {
                    return array('status' => false, 'message' => 'Backend Validation Error: Please submit at least one required document from the options provided.');
                }
            } else {
                // All Required
                foreach ($group['docs'] as $doc_id) {
                    if (!isset($doc_submitted_cache[$doc_id]) || !$doc_submitted_cache[$doc_id]) {
                        $doc_name = 'Document';
                        foreach ($kyc_master_list as $kyc) {
                            if ($kyc['id_mas_kyc'] == $doc_id) {
                                $doc_name = (isset($kyc['name']) ? $kyc['name'] : (isset($kyc['kyc_document']) ? $kyc['kyc_document'] : $doc_name));
                            }
                        }
                        return array('status' => false, 'message' => 'Backend Validation Error: Missing required mandatory document - ' . $doc_name);
                    }
                }
            }
        }

        return array('status' => true);
    }

    private function _process_save_kyc($cus_id, $kyc_dynamic_post, $kyc_master_list) {
        $settings = $this->db->get('kyc_settings')->row_array();
        $kyc_status = 0; // Default Pending
        if (isset($settings['kyc_integration_type']) && $settings['kyc_integration_type'] == 0 && 
            isset($settings['kyc_verification_type']) && $settings['kyc_verification_type'] == 1) {
            $kyc_status = 2; // Auto Verified if Self + Auto
        }

        foreach ($kyc_master_list as $kyc) {
            $id_mas = $kyc['id_mas_kyc'];
            if (!isset($kyc_dynamic_post[$id_mas])) {
                continue;
            }

            $data = $kyc_dynamic_post[$id_mas];
            $number = NULL;
            $img_url = NULL;
            $back_img_url = NULL;
            $document_url = NULL;

            foreach ($kyc['attributes'] as $attr) {
                $attr_name = $attr['attribute'];
                
                // Text fields
                if ($attr['attr_input'] == 'varchar' || $attr['attr_input'] == 'number') {
                    if (isset($data[$attr_name])) {
                        $number = $data[$attr_name];
                    }
                } 
                // Images / Files
                else {
                    $posted_file_input = "kyc_dynamic_file_" . $id_mas . "_" . $attr_name;
                    
                    // Handle Base64 from hidden webcam input first
                    if (isset($data[$attr_name]) && strpos($data[$attr_name], 'base64') !== false) {
                        $pan = explode(";base64,", $data[$attr_name]);
                        if(isset($pan[1])) {
                            $base64 = base64_decode($pan[1]);
                            
                            $normalized_fcpath = str_replace('\\', '/', FCPATH);
                            $normalized_docroot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
                            $subfolder = trim(str_replace($normalized_docroot, '', $normalized_fcpath), '/');
                            $storage_rel_path = self::KYC_BASE_PATH . $id_mas . "/" . $cus_id;
                            $abs_path = FCPATH . $storage_rel_path;
                            $web_rel_path = ($subfolder ? $subfolder . "/" : "") . $storage_rel_path;
                            
                            if (!is_dir($abs_path)) { 
                                mkdir($abs_path, 0777, TRUE); 
                                chmod($abs_path, 0777); 
                            }
                            
                            $file_name = $attr_name . "_" . time() . ".png";
                            $file_url = $web_rel_path . "/" . $file_name;
                            $full_path = $abs_path . "/" . $file_name;
                            
                            file_put_contents($full_path, $base64);
                            chmod($full_path, 0777);
                            
                            $protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";
                            $domain = $_SERVER['HTTP_HOST'];
                            $full_url = $protocol . $domain . "/" . $file_url;
                            
                            if (strpos($attr_name, 'front') !== false) {
                                $img_url = $full_url;
                            } else if (strpos($attr_name, 'back') !== false) {
                                $back_img_url = $full_url;
                            } else {
                                $document_url = $full_url;
                            }
                        }
                    }
                    // Handle regular file uploads (using $_FILES)
                    else if (isset($_FILES[$posted_file_input]['name']) && $_FILES[$posted_file_input]['name'] != '') {
                        $storage_rel_path = self::KYC_BASE_PATH . $id_mas . "/" . $cus_id;
                        $abs_path = FCPATH . $storage_rel_path;
                        
                        if (!is_dir($abs_path)) { 
                            mkdir($abs_path, 0777, TRUE); 
                            chmod($abs_path, 0777); 
                        }
                        
                        $uploaded_file = $_FILES[$posted_file_input];
                        $file_extension = pathinfo($uploaded_file['name'], PATHINFO_EXTENSION);
                        $file_name = $attr_name . "_" . time() . "." . $file_extension;
                        $target_file = $abs_path . "/" . $file_name;
                        
                        if (move_uploaded_file($uploaded_file['tmp_name'], $target_file)) {
                            chmod($target_file, 0777);
                            
                            $normalized_fcpath = str_replace('\\', '/', FCPATH);
                            $normalized_docroot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
                            $subfolder = trim(str_replace($normalized_docroot, '', $normalized_fcpath), '/');
                            $web_rel_path = ($subfolder ? $subfolder . "/" : "") . $storage_rel_path;
                            $file_url = $web_rel_path . "/" . $file_name;
                            
                            $protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";
                            $domain = $_SERVER['HTTP_HOST'];
                            $full_url = $protocol . $domain . "/" . $file_url;
                            
                            if (strpos($attr_name, 'front') !== false) {
                                $img_url = $full_url;
                            } else if (strpos($attr_name, 'back') !== false) {
                                $back_img_url = $full_url;
                            } else {
                                $document_url = $full_url;
                            }
                        } else {
                            error_log("Failed to move uploaded file: " . $posted_file_input);
                        }
                    }
                }
            }

            if ($number != '' || $img_url != '' || $back_img_url != '' || $document_url != '') {
                $this->db->where('id_customer', $cus_id);
                $this->db->where('kyc_type', $id_mas);
                $existing = $this->db->get('kyc')->result_array();

                if (!empty($existing)) {
                    $kyc_upd_data = array(
                        'last_update'  => date("Y-m-d H:i:s"),
                        'number'       => $number != null ? $number : $existing[0]['number'],
                        'img_url'      => $img_url != null ? $img_url : $existing[0]['img_url'],
                        'back_img_url' => $back_img_url != null ? $back_img_url : $existing[0]['back_img_url'],
                        'document_url' => $document_url != null ? $document_url : $existing[0]['document_url']
                    );
                    
                    $data_changed = ($number && $number != $existing[0]['number']) || 
                                    ($img_url && $img_url != $existing[0]['img_url']) || 
                                    ($back_img_url && $back_img_url != $existing[0]['back_img_url']) || 
                                    ($document_url && $document_url != $existing[0]['document_url']);
                                    
                    if ($data_changed || $existing[0]['status'] == 3) {
                        $kyc_upd_data['status'] = $kyc_status;
                    }
                    
                    $this->db->where('id_customer', $cus_id);
                    $this->db->where('kyc_type', $id_mas);
                    $this->db->update('kyc', $kyc_upd_data);
                } else {
                    $kyc_ins_data = array(
                        'id_customer'       => $cus_id,
                        'kyc_type'          => $id_mas,
                        'added_by'          => 1,
                        'number'            => $number,
                        'status'            => $kyc_status,
                        'verification_type' => 1,
                        'date_add'          => date("Y-m-d H:i:s"),
                        'img_url'           => $img_url,
                        'back_img_url'      => $back_img_url,
                        'document_url'      => $document_url,
                        'emp_verified_by'   => $this->session->userdata('uid'),
                    );
                    $this->db->insert('kyc', $kyc_ins_data);
                }
            }
        }
    }

    public function get_kyc_masters_crud() {
        $this->db->order_by('sort', 'ASC');
        return $this->db->get('kyc_master')->result_array();
    }

    public function get_master_by_id($id) {
        $this->db->where('id_mas_kyc', $id);
        return $this->db->get('kyc_master')->row_array();
    }

    public function get_attributes_by_master($id) {
        $this->db->where('id_mas_kyc', $id);
        $this->db->order_by('position', 'ASC');
        return $this->db->get('kyc_attribute')->result_array();
    }

    public function save_master_and_attributes($id, $master_data, $attributes_data) {
        $this->db->trans_start();
        
        if ($id > 0) {
            $master_data['updated_on'] = date('Y-m-d H:i:s');
            $this->db->where('id_mas_kyc', $id);
            $this->db->update('kyc_master', $master_data);
            $id_mas_kyc = $id;
        } else {
            $master_data['created_on'] = date('Y-m-d H:i:s');
            $this->db->insert('kyc_master', $master_data);
            $id_mas_kyc = $this->db->insert_id();
        }

        $this->db->where('id_mas_kyc', $id_mas_kyc);
        $this->db->delete('kyc_attribute');

        if (!empty($attributes_data)) {
            foreach ($attributes_data as $index => $attr) {
                unset($attr['id_kyc_attribute']);
                $attr['id_mas_kyc'] = $id_mas_kyc;
                $attr['position'] = $index + 1;
                $this->db->insert('kyc_attribute', $attr);
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function delete_master($id) {
        $this->db->where('id_mas_kyc', $id);
        return $this->db->delete('kyc_master');
    }
}
