<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kyc_model extends CI_Model
{
    const KYC_BASE_PATH = 'assets/kyc/';

    function __construct()
    {
        parent::__construct();
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
            $total_paid_amount = ($res2 && $res2['scheme_t']) ? $res2['scheme_t'] : 0;
        }

        $this->db->where('status', 1);
        $rules = $this->db->get('kyc_rules')->result_array();
        $kyc_mode = isset($settings['kyc_mode']) ? $settings['kyc_mode'] : 0;

        foreach($rules as $r) {
            $triggered = false;
            if ($kyc_mode == 1) {
                if ($r['rules'] == 1) { 
                    if ($r['amount'] > 0 && ($cus_overall_amount + $pay_amount) >= $r['amount']) {
                        $triggered = true;
                    }
                }
            } else if ($kyc_mode == 0) {
                if ($r['id_scheme'] == $id_scheme || empty($r['id_scheme']) || $r['id_scheme'] == 0) {
                    if ($r['rules'] == 0 && $id_scheme_account == 0) {
                        $triggered = true;
                    } else if ($r['rules'] == 1) {
                        if ($r['amount'] > 0 && ($total_paid_amount + $pay_amount) >= $r['amount']) {
                            $triggered = true;
                        }
                    }
                }
            }
            if ($triggered && $r['id_mas_kyc']) {
                $group_key = 'rule_' . $r['id_scheme'] . '_' . $r['rules'] . '_' . $r['amount'] . '_' . $r['logic'];
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
            $rule_groups = array();
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
            if ($group['logic'] == 1) {
                $has_one = false;
                foreach ($group['docs'] as $doc_id) {
                    if (isset($customer_kyc[$doc_id])) { $has_one = true; break; }
                }
                if (!$has_one) {
                    $unmet_rule_groups[$key] = $group;
                    $has_logic_1 = true;
                    foreach ($group['docs'] as $doc_id) {
                        $final_required_docs[$doc_id] = true;
                    }
                }
            } else {
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
        
        $active_logic = 0;
        if ($has_logic_1 && !$has_logic_0) { $active_logic = 1; }
        $data['active_rule_logic'] = $active_logic;
        return $data;
    }

    public function save_scheme_account_kyc($cus_id, $id_scheme, $pay_amount, $kyc_dynamic_post, $id_scheme_account = 0) {
        $data = $this->get_unified_dynamic_kyc_data($id_scheme, $cus_id, $pay_amount, $id_scheme_account);
        $kyc_master_list = $data['kyc_master_list'];
        $unmet_rule_groups = $data['unmet_rule_groups'];
        if (!is_array($kyc_dynamic_post)) $kyc_dynamic_post = array();
        $this->_process_save_kyc($cus_id, $kyc_dynamic_post, $kyc_master_list);
        return array('status' => true, 'message' => 'KYC Profile Configured Successfully');
    }

    public function save_customer_kyc($cus_id, $kyc_dynamic_post) {
        $data = $this->get_unified_dynamic_kyc_data(0, $cus_id, 0, 0);
        $kyc_master_list = $data['kyc_master_list'];
        if (!is_array($kyc_dynamic_post)) $kyc_dynamic_post = array();
        $this->_process_save_kyc($cus_id, $kyc_dynamic_post, $kyc_master_list);
        return array('status' => true, 'message' => 'Customer KYC Saved Successfully');
    }

    private function _process_save_kyc($cus_id, $kyc_dynamic_post, $kyc_master_list) {
        $settings = $this->db->get('kyc_settings')->row_array();
        $kyc_status = 0;
        if (isset($settings['kyc_integration_type']) && $settings['kyc_integration_type'] == 0 && 
            isset($settings['kyc_verification_type']) && $settings['kyc_verification_type'] == 1) {
            $kyc_status = 2;
        }

        foreach ($kyc_master_list as $kyc) {
            $id_mas = $kyc['id_mas_kyc'];
            if (!isset($kyc_dynamic_post[$id_mas])) continue;

            $post_data = $kyc_dynamic_post[$id_mas];
            $number = NULL; $img_url = NULL; $back_img_url = NULL; $document_url = NULL;

            foreach ($kyc['attributes'] as $attr) {
                $attr_name = $attr['attribute'];
                if ($attr['attr_input'] == 'varchar' || $attr['attr_input'] == 'number') {
                    if (isset($post_data[$attr_name])) $number = $post_data[$attr_name];
                } else {
                    if (isset($post_data[$attr_name]) && strpos($post_data[$attr_name], 'base64') !== false) {
                        $pan = explode(";base64,", $post_data[$attr_name]);
                        if(isset($pan[1])) {
                            $base64 = base64_decode($pan[1]);
                            $storage_rel_path = self::KYC_BASE_PATH . $id_mas . "/" . $cus_id;
                            $abs_path = FCPATH . $storage_rel_path;
                            if (!is_dir($abs_path)) { mkdir($abs_path, 0777, TRUE); chmod($abs_path, 0777); }
                            $file_name = $attr_name . "_" . time() . ".png";
                            $full_path = $abs_path . "/" . $file_name;
                            file_put_contents($full_path, $base64);
                            chmod($full_path, 0777);
                            $subfolder = trim(str_replace($_SERVER['DOCUMENT_ROOT'], '', FCPATH), '/');
                            $web_rel_path = ($subfolder ? $subfolder . "/" : "") . $storage_rel_path;
                            $protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";
                            $full_url = $protocol . $_SERVER['HTTP_HOST'] . "/" . $web_rel_path . "/" . $file_name;
                            if (strpos($attr_name, 'front') !== false) $img_url = $full_url;
                            else if (strpos($attr_name, 'back') !== false) $back_img_url = $full_url;
                            else $document_url = $full_url;
                        }
                    }
                }
            }

            if ($number != '' || $img_url != '' || $back_img_url != '' || $document_url != '') {
                $this->db->where('id_customer', $cus_id);
                $this->db->where('kyc_type', $id_mas);
                $existing = $this->db->get('kyc')->result_array();
                if (!empty($existing)) {
                    $kyc_upd = array(
                        'last_update'  => date("Y-m-d H:i:s"),
                        'number'       => $number != null ? $number : $existing[0]['number'],
                        'img_url'      => $img_url != null ? $img_url : $existing[0]['img_url'],
                        'back_img_url' => $back_img_url != null ? $back_img_url : $existing[0]['back_img_url'],
                        'document_url' => $document_url != null ? $document_url : $existing[0]['document_url']
                    );
                    $this->db->where('id_customer', $cus_id);
                    $this->db->where('kyc_type', $id_mas);
                    $this->db->update('kyc', $kyc_upd);
                } else {
                    $kyc_ins = array(
                        'id_customer'     => $cus_id, 'kyc_type' => $id_mas, 'added_by' => 1,
                        'number'          => $number, 'status'   => $kyc_status,
                        'verification_type' => 1, 'date_add' => date("Y-m-d H:i:s"),
                        'img_url'         => $img_url, 'back_img_url' => $back_img_url,
                        'document_url'    => $document_url, 'emp_verified_by' => 0,
                    );
                    $this->db->insert('kyc', $kyc_ins);
                }
            }
        }
    }
}
