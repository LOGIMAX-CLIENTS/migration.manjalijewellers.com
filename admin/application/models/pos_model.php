<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * POS Model — All POS-related database operations
 * Separated from ret_billing_model as part of POS Module Architecture
 */
class Pos_model extends CI_Model {

    private $_posTablesChecked = array();

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Check if a POS table exists — cached per request
     */
    private function _posTableSafe($table)
    {
        if(!isset($this->_posTablesChecked[$table])){
            $this->_posTablesChecked[$table] = $this->db->table_exists($table);
        }
        return $this->_posTablesChecked[$table];
    }

    // ============================================
    // DEVICE LIST & DETAILS
    // ============================================

    function getPOSDeviceList()
    {
        if(!$this->_posTableSafe('ret_pos_device_list')) return array();
        $device_query = $this->db->query("
            SELECT d.id_device, d.dispname, d.poscode, d.merchantid, d.securitytoken, 
                   d.imei, d.is_default, d.id_provider, d.devicetype,
                   p.provider_code, p.provider_name
            FROM ret_pos_device_list d
            LEFT JOIN ret_pos_providers p ON d.id_provider = p.id_provider
            WHERE (d.is_active = 1 OR d.is_active IS NULL)
            ORDER BY d.is_default DESC, d.dispname ASC
        ");
        return $device_query->result_array();
    }

    function getPOSDeviceDetails($deviceId)
    {
        if(!$this->_posTableSafe('ret_pos_device_list')) return null;
        $deviceId = intval($deviceId);
        $device_query = $this->db->query("
            SELECT d.*, 
                   p.provider_code, p.provider_name, p.auth_type,
                   p.api_url_uat_init, p.api_url_uat_status, p.api_url_uat_cancel,
                   p.api_url_live_init, p.api_url_live_status, p.api_url_live_cancel,
                   p.is_env_live, p.has_qr_display, p.has_callback, p.status_method,
                   bpd.device_name as linked_pay_device_name,
                   bk.bank_name as linked_bank_name, bk.short_code as linked_bank_code
            FROM ret_pos_device_list d
            LEFT JOIN ret_pos_providers p ON d.id_provider = p.id_provider
            LEFT JOIN ret_bill_pay_device bpd ON d.id_pay_device = bpd.id_device
            LEFT JOIN bank bk ON d.id_bank = bk.id_bank
            WHERE d.id_device = $deviceId
        ");
        return $device_query->row_array();
    }

    function getPOSProvider($providerId)
    {
        if(!$this->_posTableSafe('ret_pos_providers')) return null;
        $providerId = intval($providerId);
        return $this->db->query("SELECT * FROM ret_pos_providers WHERE id_provider = $providerId")->row_array();
    }

    function getPOSApiUrl($deviceDetails, $action)
    {
        $env = $deviceDetails['is_env_live'] ? 'live' : 'uat';
        $key = "api_url_{$env}_{$action}";
        return isset($deviceDetails[$key]) ? $deviceDetails[$key] : '';
    }

    function getcusLastMobile($cusId)
    {
        $cusId = intval($cusId);
        $mob_query = $this->db->query("SELECT RIGHT(mobile,5) as mobile FROM customer where id_customer = $cusId");
        return $mob_query->row()->mobile;
    }

    function getPOSMachineRequired()
    {
        $this->load->model('ret_billing_model');
        $val = $this->ret_billing_model->get_ret_settings('pay_by_pos');
        return ($val !== null && $val !== false) ? $val : 0;
    }

    /**
     * Auto-expire stale pending POS transactions
     * Pine Labs auto-cancels after AutoCancelDurationInMinutes (typically 2 min).
     * We mark as FAILED (status=3) after $minutesThreshold to unblock new payments.
     *
     * @param int $cusId Customer ID (0 = expire for ALL customers)
     * @param int $minutesThreshold Minutes after which pending transactions are expired
     * @return int Number of rows expired
     */
    function expireStalePOSTransactions($cusId = 0, $minutesThreshold = 5)
    {
        if(!$this->_posTableSafe('ret_pos_requests')) return 0;
        
        $cusId = intval($cusId);
        $minutesThreshold = intval($minutesThreshold);
        
        $sql = "UPDATE ret_pos_requests 
                SET pos_req_status = 3 
                WHERE pos_req_status = 0 
                  AND created_at < DATE_SUB(NOW(), INTERVAL ? MINUTE)";
        $params = array($minutesThreshold);
        
        if($cusId > 0){
            $sql .= " AND pos_req_bill_cusid = ?";
            $params[] = $cusId;
        }
        
        $this->db->query($sql, $params);
        $affected = $this->db->affected_rows();
        
        if($affected > 0){
            log_message('info', 'POS: Auto-expired '.$affected.' stale pending transactions (older than '.$minutesThreshold.' min)' . ($cusId ? ' for cusid='.$cusId : ''));
        }
        
        return $affected;
    }

    // ============================================
    // POS SETTINGS CRUD
    // ============================================

    function getAllPOSProviders()
    {
        if(!$this->_posTableSafe('ret_pos_providers')) return array();
        return $this->db->query("SELECT * FROM ret_pos_providers ORDER BY id_provider")->result_array();
    }

    function getPOSProviderById($providerId)
    {
        if(!$this->_posTableSafe('ret_pos_providers')) return null;
        return $this->db->query("SELECT * FROM ret_pos_providers WHERE id_provider = ".intval($providerId))->row_array();
    }

    function getAllPOSDevicesWithProvider()
    {
        if(!$this->_posTableSafe('ret_pos_device_list')) return array();
        return $this->db->query("
            SELECT d.*, p.provider_name, p.provider_code,
                   IFNULL(bpd.device_name, '-') as linked_device_name
            FROM ret_pos_device_list d
            LEFT JOIN ret_pos_providers p ON d.id_provider = p.id_provider
            LEFT JOIN ret_bill_pay_device bpd ON d.id_pay_device = bpd.id_device
            ORDER BY d.id_device
        ")->result_array();
    }

    function getPOSDeviceById($deviceId)
    {
        if(!$this->_posTableSafe('ret_pos_device_list')) return null;
        return $this->db->query("SELECT * FROM ret_pos_device_list WHERE id_device = ".intval($deviceId))->row_array();
    }

    function savePOSDevice($data)
    {
        if(!$this->_posTableSafe('ret_pos_device_list')) return 0;
        $this->db->insert('ret_pos_device_list', $data);
        return $this->db->insert_id();
    }

    function updatePOSDevice($deviceId, $data)
    {
        if(!$this->_posTableSafe('ret_pos_device_list')) return false;
        $this->db->where('id_device', intval($deviceId));
        return $this->db->update('ret_pos_device_list', $data);
    }

    function deletePOSDevice($deviceId)
    {
        if(!$this->_posTableSafe('ret_pos_device_list')) return false;
        $this->db->where('id_device', intval($deviceId));
        return $this->db->update('ret_pos_device_list', array('is_active' => 0));
    }

    function setDefaultPOSDevice($deviceId)
    {
        if(!$this->_posTableSafe('ret_pos_device_list')) return false;
        $this->db->update('ret_pos_device_list', array('is_default' => 0));
        $this->db->where('id_device', intval($deviceId));
        return $this->db->update('ret_pos_device_list', array('is_default' => 1));
    }

    function toggleProviderEnv($providerId, $isLive)
    {
        if(!$this->_posTableSafe('ret_pos_providers')) return false;
        $this->db->where('id_provider', intval($providerId));
        return $this->db->update('ret_pos_providers', array('is_env_live' => intval($isLive)));
    }

    function savePOSProvider($data)
    {
        if(!$this->_posTableSafe('ret_pos_providers')) return 0;
        $this->db->insert('ret_pos_providers', $data);
        return $this->db->insert_id();
    }

    function updatePOSProvider($providerId, $data)
    {
        if(!$this->_posTableSafe('ret_pos_providers')) return false;
        $this->db->where('id_provider', intval($providerId));
        return $this->db->update('ret_pos_providers', $data);
    }

    // ============================================
    // TRANSACTION LOG & SECURITY
    // ============================================

    function getAllPOSTransactions()
    {
        if(!$this->_posTableSafe('ret_pos_requests')) return array();
        $sql = "SELECT r.*, 
                       r.created_at as pos_req_createdon,
                       p.provider_name, p.provider_code,
                       d.dispname as device_name
                FROM ret_pos_requests r
                LEFT JOIN ret_pos_providers p ON p.id_provider = r.id_provider
                LEFT JOIN ret_pos_device_list d ON d.id_device = r.id_device
                ORDER BY r.pos_req_id DESC
                LIMIT 500";
        return $this->db->query($sql)->result_array();
    }

    function getPOSTransactionById($id)
    {
        if(!$this->_posTableSafe('ret_pos_requests')) return null;
        $this->db->select('r.*, r.created_at as pos_req_createdon, p.provider_name, p.provider_code');
        $this->db->from('ret_pos_requests r');
        $this->db->join('ret_pos_providers p', 'p.id_provider = r.id_provider', 'left');
        $this->db->where('r.pos_req_id', intval($id));
        return $this->db->get()->row_array();
    }

    function getActivePOSTransaction($cusid)
    {
        if(!$this->_posTableSafe('ret_pos_requests')) return null;
        $cusid = intval($cusid);
        $oldDebug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->query("
            UPDATE ret_pos_requests 
            SET pos_req_status = 3 
            WHERE pos_req_bill_cusid = $cusid
            AND pos_req_status = 0
            AND pos_req_created_at < DATE_SUB(NOW(), INTERVAL 30 MINUTE)
        ");
        $this->db->db_debug = $oldDebug;
        
        $query = $this->db->query("
            SELECT pos_req_id, pos_res_ref_id, pos_req_amount, pos_req_status
            FROM ret_pos_requests
            WHERE pos_req_bill_cusid = $cusid
            AND pos_req_status = 0
            ORDER BY pos_req_id DESC
            LIMIT 1
        ");
        return $query->row_array();
    }

    function getPOSTransactionByRef($refId)
    {
        if(!$this->_posTableSafe('ret_pos_requests')) return null;
        $refId = $this->db->escape($refId);
        $query = $this->db->query("
            SELECT pos_req_id, pos_res_ref_id, pos_req_status
            FROM ret_pos_requests
            WHERE pos_res_ref_id = $refId
            LIMIT 1
        ");
        return $query->row_array();
    }

    function getPhonePeSaltKey()
    {
        $data = $this->getPhonePeSaltKeyWithIndex();
        return $data['salt_key'];
    }

    /**
     * Returns both salt_key and salt_index for X-VERIFY signature construction.
     * Used by S2S callback handler where salt_index must NOT be hardcoded.
     */
    function getPhonePeSaltKeyWithIndex()
    {
        $default = array('salt_key' => '', 'salt_index' => '1');
        if(!$this->_posTableSafe('ret_pos_device_list')) return $default;
        $query = $this->db->query("
            SELECT d.salt_key, d.salt_index
            FROM ret_pos_device_list d
            LEFT JOIN ret_pos_providers p ON d.id_provider = p.id_provider
            WHERE p.provider_code LIKE 'phonepe%'
            AND d.salt_key IS NOT NULL AND d.salt_key != ''
            LIMIT 1
        ");
        $row = $query->row_array();
        if(!$row) return $default;
        return array(
            'salt_key' => $row['salt_key'],
            'salt_index' => !empty($row['salt_index']) ? $row['salt_index'] : '1'
        );
    }

    // ============================================
    // GENERIC DB HELPERS (used by plugins)
    // ============================================

    function insertData($data, $table)
    {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    function updateData($data, $key, $value, $table)
    {
        $this->db->where($key, $value);
        return $this->db->update($table, $data);
    }

    // ============================================
    // SETTLEMENT & AUDIT
    // ============================================

    private function _getPOSDateColumn()
    {
        if(!$this->_posTableSafe('ret_pos_requests')) return '';
        // IMPORTANT: `pos_req_createdon` has DEFAULT CURRENT_TIMESTAMP and was mass-reset
        //   during a migration/ALTER — all rows got today's date. It is NOT the real date.
        //   `created_at` and `pos_req_created_at` hold the actual transaction dates.
        //   Priority: created_at > pos_req_created_at > pos_created_on > pos_req_createdon(LAST)
        $candidates = array('created_at', 'pos_req_created_at', 'pos_created_on', 'pos_req_createdon');
        $cols = $this->db->list_fields('ret_pos_requests');
        foreach($candidates as $c){
            if(in_array($c, $cols)) return $c;
        }
        return '';
    }

    function getPOSSettlementSummary($fromDate, $toDate)
    {
        $dateCol = $this->_getPOSDateColumn();
        if($dateCol){
            $sql = "SELECT DATE(r.{$dateCol}) as txn_date,
                           IFNULL(p.provider_name, 'Unknown') as provider_name,
                           r.pos_req_status,
                           COUNT(*) as txn_count,
                           SUM(r.pos_req_amount) as total_paise
                    FROM ret_pos_requests r
                    LEFT JOIN ret_pos_providers p ON p.id_provider = r.id_provider
                    WHERE DATE(r.{$dateCol}) BETWEEN ? AND ?
                    GROUP BY DATE(r.{$dateCol}), IFNULL(p.provider_name, 'Unknown'), r.pos_req_status
                    ORDER BY txn_date DESC, provider_name";
            return $this->db->query($sql, array($fromDate, $toDate))->result_array();
        } else {
            $sql = "SELECT NULL as txn_date,
                           IFNULL(p.provider_name, 'Unknown') as provider_name,
                           r.pos_req_status,
                           COUNT(*) as txn_count,
                           SUM(r.pos_req_amount) as total_paise
                    FROM ret_pos_requests r
                    LEFT JOIN ret_pos_providers p ON p.id_provider = r.id_provider
                    GROUP BY IFNULL(p.provider_name, 'Unknown'), r.pos_req_status
                    ORDER BY provider_name";
            return $this->db->query($sql)->result_array();
        }
    }

    function getPOSAuditTrail($fromDate, $toDate)
    {
        $dateCol = $this->_getPOSDateColumn();
        $dateSelect = $dateCol ? "r.{$dateCol} as pos_req_createdon" : "NULL as pos_req_createdon";
        $dateWhere  = $dateCol ? "WHERE DATE(r.{$dateCol}) BETWEEN ? AND ?" : "WHERE 1=1";
        $dateOrder  = $dateCol ? "r.{$dateCol} DESC" : "r.pos_req_id DESC";
        
        $sql = "SELECT r.pos_req_id, r.pos_trans_no, r.pos_req_amount,
                       r.pos_req_status, r.pos_res_ref_id,
                       r.pos_req_createdby, {$dateSelect},
                       r.pos_req_bill_cusid, r.pos_req_bill_id, r.pos_req_est_id,
                       r.pos_req_bill_type, r.pos_req_source_ref, r.pos_req_id_branch,
                       IFNULL(e.firstname, CONCAT('UID:', r.pos_req_createdby)) as created_by_name,
                       IFNULL(p.provider_name, 'Unknown') as provider_name,
                       IFNULL(d.dispname, '-') as device_name,
                       IFNULL(b.name, '-') as branch_name
                FROM ret_pos_requests r
                LEFT JOIN employee e ON e.id_employee = r.pos_req_createdby
                LEFT JOIN ret_pos_providers p ON p.id_provider = r.id_provider
                LEFT JOIN ret_pos_device_list d ON d.id_device = r.id_device
                LEFT JOIN branch b ON b.id_branch = r.pos_req_id_branch
                {$dateWhere}
                ORDER BY {$dateOrder}
                LIMIT 500";
        
        $params = $dateCol ? array($fromDate, $toDate) : array();
        return $this->db->query($sql, $params)->result_array();
    }

    /**
     * Client-friendly payment report with customer names and bill links
     * Returns: ['summary' => [...], 'transactions' => [...], 'breakdown' => [...]]
     */
    function getPaymentReport($dateStr = null, $dateEndStr = null)
    {
        if(!$this->_posTableSafe('ret_pos_requests')) return array('summary'=>array(),'transactions'=>array(),'breakdown'=>array());
        $dateCol = $this->_getPOSDateColumn();
        $dateWhere = '';
        $params = array();
        if($dateStr && $dateCol){
            if($dateEndStr && $dateEndStr != $dateStr){
                // Date range: from $dateStr to $dateEndStr
                $dateWhere = "AND DATE(r.{$dateCol}) BETWEEN ? AND ?";
                $params[] = $dateStr;
                $params[] = $dateEndStr;
                log_message('debug', 'POS getPaymentReport: RANGE dateCol='.$dateCol.' from='.$dateStr.' to='.$dateEndStr);
            } else {
                // Single date
                $dateWhere = "AND DATE(r.{$dateCol}) = ?";
                $params[] = $dateStr;
                log_message('debug', 'POS getPaymentReport: dateCol='.$dateCol.' dateStr='.$dateStr);
            }
        } else {
            log_message('debug', 'POS getPaymentReport: NO DATE FILTER! dateCol='.$dateCol.' dateStr='.$dateStr);
        }
        $dateOrder = $dateCol ? "r.{$dateCol} DESC" : "r.pos_req_id DESC";
        $dateSelect = $dateCol ? "r.{$dateCol} as pos_req_createdon" : "NULL as pos_req_createdon";

        // 1. Client-friendly transaction list
        $sql = "SELECT r.pos_req_id, r.pos_trans_no, r.pos_req_amount,
                       r.pos_req_status, r.pos_res_ref_id, r.pos_utr,
                       r.pos_req_createdby, {$dateSelect},
                       r.pos_success_at, r.pos_last_checked_at,
                       r.pos_req_bill_cusid, r.pos_req_bill_id, r.pos_req_est_id,
                       r.pos_req_bill_type, r.pos_req_source_ref, r.pos_req_id_branch,
                       IFNULL(r.payment_mode, IFNULL(r.provider_code, '-')) as payment_mode,
                       IFNULL(r.card_last4, '') as card_last4,
                       IFNULL(r.approval_code, '') as approval_code,
                       r.pos_res_trans_data,
                       IFNULL(c.firstname, 'Walk-in') as customer_name,
                       IFNULL(c.mobile, '') as customer_mobile,
                       IFNULL(e.firstname, CONCAT('UID:', r.pos_req_createdby)) as staff_name,
                       IFNULL(p.provider_name, 'Unknown') as provider_name,
                       p.provider_code,
                       IFNULL(d.dispname, '-') as device_name,
                       IFNULL(b.name, IFNULL(br2.name, '-')) as branch_name,
                       IFNULL(br2.short_name, IFNULL(b.short_name, '')) as branch_short,
                       CASE r.pos_req_bill_type
                           WHEN 1 THEN 'Sales'
                           WHEN 2 THEN 'Sales & Purchase'
                           WHEN 3 THEN 'Sales & Return'
                           WHEN 4 THEN 'Purchase'
                           WHEN 5 THEN 'Order Advance'
                           WHEN 7 THEN 'Sales Return'
                           WHEN 8 THEN 'Credit Collection'
                           WHEN 9 THEN 'Order Delivery'
                           ELSE ''
                       END as bill_type_label,
                       CASE
                            WHEN r.pos_req_bill_id IS NOT NULL AND bl.bill_id IS NOT NULL THEN
                                CONCAT(IFNULL(br2.short_name,''), IFNULL(bl.fin_year_code,''),
                                    CASE bl.bill_type
                                        WHEN 1 THEN CONCAT('SA/', LPAD(IFNULL(bl.sales_ref_no, bl.bill_no), 5, '0'))
                                        WHEN 2 THEN CONCAT('SA/', LPAD(IFNULL(bl.sales_ref_no, bl.bill_no), 5, '0'))
                                        WHEN 3 THEN CONCAT('SR/', LPAD(IFNULL(bl.s_ret_refno, bl.bill_no), 5, '0'))
                                        WHEN 4 THEN CONCAT('PU/', LPAD(IFNULL(bl.pur_ref_no, bl.bill_no), 5, '0'))
                                        WHEN 5 THEN CONCAT('OD/', LPAD(IFNULL(bl.order_adv_ref_no, bl.bill_no), 5, '0'))
                                        WHEN 7 THEN CONCAT('SR/', LPAD(IFNULL(bl.s_ret_refno, bl.bill_no), 5, '0'))
                                        WHEN 8 THEN CONCAT('CC/', LPAD(IFNULL(bl.credit_coll_refno, bl.bill_no), 5, '0'))
                                        ELSE IFNULL(bl.bill_no, CONCAT('B-', r.pos_req_bill_id))
                                    END)
                            WHEN r.pos_req_est_id IS NOT NULL AND est.estimation_id IS NOT NULL THEN
                                CONCAT('EST-', IFNULL(est.esti_no, r.pos_req_est_id))
                            ELSE ''
                        END as bill_number,
                        IFNULL(bl.bill_type, 0) as ret_bill_type,
                       IFNULL(bl.tot_bill_amount, IFNULL(est.total_cost, 0)) as bill_total_amount,
                       DATE_FORMAT(IFNULL(bl.bill_date, est.estimation_datetime), '%d-%m-%Y') as bill_date,
                       IFNULL(bp.card_no, r.card_last4) as bp_card_no,
                       CASE bp.card_type WHEN 1 THEN 'Rupay' WHEN 2 THEN 'Visa' WHEN 3 THEN 'Mastro' WHEN 4 THEN 'Master' ELSE '' END as bp_card_name,
                       IFNULL(bp.payment_ref_number, r.approval_code) as bp_approval_no,
                       bp.payment_mode as bp_payment_mode,
                       bp.NB_type as bp_nb_type,
                       IFNULL(bpd.device_name, '') as bp_device_name
                FROM ret_pos_requests r
                LEFT JOIN customer c ON c.id_customer = r.pos_req_bill_cusid
                LEFT JOIN employee e ON e.id_employee = r.pos_req_createdby
                LEFT JOIN ret_pos_providers p ON p.id_provider = r.id_provider
                LEFT JOIN ret_pos_device_list d ON d.id_device = r.id_device
                LEFT JOIN branch b ON b.id_branch = r.pos_req_id_branch
                LEFT JOIN ret_billing bl ON bl.bill_id = r.pos_req_bill_id
                LEFT JOIN branch br2 ON br2.id_branch = bl.id_branch
                LEFT JOIN ret_estimation est ON est.estimation_id = r.pos_req_est_id
                LEFT JOIN ret_billing_payment bp ON bp.bill_id = r.pos_req_bill_id AND bp.payment_mode IN ('CC','DC','NB')
                LEFT JOIN ret_bill_pay_device bpd ON bpd.id_device = bp.id_pay_device
                WHERE 1=1 {$dateWhere}
                ORDER BY {$dateOrder}
                LIMIT 500";
        $transactions = $this->db->query($sql, $params)->result_array();

        // 2. Summary totals
        $summary = array('total'=>0,'success'=>0,'failed'=>0,'pending'=>0,'cancelled'=>0,'success_amount'=>0,'total_amount'=>0);
        foreach($transactions as $t){
            $summary['total']++;
            $amt = floatval($t['pos_req_amount']);
            $summary['total_amount'] += $amt;
            switch(intval($t['pos_req_status'])){
                case 1: $summary['success']++; $summary['success_amount'] += $amt; break;
                case 2: $summary['failed']++; break;
                case 3: $summary['cancelled']++; break;
                default: $summary['pending']++; break;
            }
        }

        // 3. Device-wise breakdown (reuse existing)
        $breakdown = array();
        if($dateStr){
            $eod = $this->getEODBreakdown($dateStr);
            $breakdown = $eod['breakdown'];
        }

        return array('summary' => $summary, 'transactions' => $transactions, 'breakdown' => $breakdown);
    }

    // ============================================
    // MODULE CHECK
    // ============================================

    function isPOSModuleEnabled()
    {
        $oldDebug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $result = $this->db->query("SELECT m_web, m_active FROM modules WHERE m_code = 'POS' LIMIT 1");
        $this->db->db_debug = $oldDebug;
        if(!$result) return false;
        $mod = $result->row_array();
        return ($mod && isset($mod['m_web']) && $mod['m_web'] == 1 && isset($mod['m_active']) && $mod['m_active'] == 1);
    }
    function getEODBreakdown($dateStr)
    {
        if(!$this->_posTableSafe('ret_pos_requests')) return array('breakdown' => array(), 'transactions' => array());
        $dateCol = $this->_getPOSDateColumn();
        $dateWhere = $dateCol ? "AND DATE(r.{$dateCol}) = ?" : "";
        $dateOrder = $dateCol ? "r.{$dateCol} DESC" : "r.pos_req_id DESC";
        $params = $dateCol ? array($dateStr) : array();
        
        // Device-wise breakdown
        $sql = "SELECT 
                    IFNULL(d.dispname, 'Unknown Device') as device_name,
                    IFNULL(p.provider_name, 'Unknown') as provider_name,
                    COUNT(*) as total,
                    SUM(CASE WHEN r.pos_req_status = 1 THEN 1 ELSE 0 END) as success,
                    SUM(CASE WHEN r.pos_req_status = 1 THEN r.pos_req_amount ELSE 0 END) as success_amount,
                    SUM(CASE WHEN r.pos_req_status IN (2,3) THEN 1 ELSE 0 END) as failed,
                    SUM(CASE WHEN r.pos_req_status = 0 THEN 1 ELSE 0 END) as pending
                FROM ret_pos_requests r
                LEFT JOIN ret_pos_providers p ON p.id_provider = r.id_provider
                LEFT JOIN ret_pos_device_list d ON d.id_device = r.id_device
                WHERE 1=1 {$dateWhere}
                GROUP BY IFNULL(d.dispname, 'Unknown Device'), IFNULL(p.provider_name, 'Unknown')
                ORDER BY provider_name, device_name";
        $breakdown = $this->db->query($sql, $params)->result_array();
        
        // Transaction list for the date
        $dateSelect = $dateCol ? "r.{$dateCol} as pos_req_createdon" : "NULL as pos_req_createdon";
        $sql2 = "SELECT r.pos_req_id, r.pos_trans_no, r.pos_req_amount,
                        r.pos_req_status, r.pos_res_ref_id,
                        r.pos_req_createdby, {$dateSelect},
                        r.pos_req_bill_cusid, r.pos_req_bill_id, r.pos_req_est_id,
                        r.pos_req_bill_type, r.pos_req_source_ref, r.pos_req_id_branch,
                        IFNULL(e.firstname, CONCAT('UID:', r.pos_req_createdby)) as created_by_name,
                        IFNULL(p.provider_name, 'Unknown') as provider_name,
                        IFNULL(d.dispname, '-') as device_name,
                        IFNULL(b.name, '-') as branch_name
                 FROM ret_pos_requests r
                 LEFT JOIN employee e ON e.id_employee = r.pos_req_createdby
                 LEFT JOIN ret_pos_providers p ON p.id_provider = r.id_provider
                 LEFT JOIN ret_pos_device_list d ON d.id_device = r.id_device
                 LEFT JOIN branch b ON b.id_branch = r.pos_req_id_branch
                 WHERE 1=1 {$dateWhere}
                 ORDER BY {$dateOrder}
                 LIMIT 500";
        $transactions = $this->db->query($sql2, $params)->result_array();
        
        return array('breakdown' => $breakdown, 'transactions' => $transactions);
    }

    // ============================================
    // PAYMENT SESSION LOCK
    // ============================================

    /**
     * Create a new payment session and lock the bill
     * Returns session_id or FALSE if bill is already locked
     */
    function createPaymentSession($invoiceId, $customerId, $deviceId, $providerCode, $amountPaise, $createdBy)
    {
        if(!$this->_posTableSafe('pos_payment_sessions')) return FALSE;
        // First expire any stale sessions
        $this->expireStaleSessions();
        
        // Check if bill already has an active session
        $active = $this->getActiveSession($invoiceId);
        if($active) return FALSE;
        
        $data = array(
            'invoice_id'    => $invoiceId,
            'customer_id'   => intval($customerId),
            'device_id'     => intval($deviceId),
            'provider_code' => $providerCode,
            'amount_paise'  => intval($amountPaise),
            'status'        => 'PENDING',
            'created_by'    => intval($createdBy),
            'created_at'    => date('Y-m-d H:i:s'),
            'expires_at'    => date('Y-m-d H:i:s', strtotime('+5 minutes'))
        );
        $this->db->insert('pos_payment_sessions', $data);
        return $this->db->insert_id();
    }

    /**
     * Get active (non-terminal) session for an invoice
     * Returns session row or NULL
     */
    function getActiveSession($invoiceId)
    {
        if(!$this->_posTableSafe('pos_payment_sessions')) return null;
        // Expire stale ones first
        $this->expireStaleSessions();
        
        $sql = "SELECT * FROM pos_payment_sessions 
                WHERE invoice_id = ? 
                AND status IN ('INIT','PENDING') 
                ORDER BY session_id DESC LIMIT 1";
        $result = $this->db->query($sql, array($invoiceId));
        return $result->row_array();
    }

    /**
     * Get active session by customer ID (for billing page lock check)
     */
    function getActiveSessionByCustomer($customerId)
    {
        if(!$this->_posTableSafe('pos_payment_sessions')) return null;
        $this->expireStaleSessions();
        
        $sql = "SELECT * FROM pos_payment_sessions 
                WHERE customer_id = ? 
                AND status IN ('INIT','PENDING') 
                ORDER BY session_id DESC LIMIT 1";
        $result = $this->db->query($sql, array(intval($customerId)));
        return $result->row_array();
    }

    /**
     * Update session status
     */
    function updateSessionStatus($sessionId, $newStatus, $posReqId = null)
    {
        $data = array('status' => $newStatus);
        if($posReqId) $data['pos_req_id'] = $posReqId;
        
        $this->db->where('session_id', intval($sessionId));
        return $this->db->update('pos_payment_sessions', $data);
    }

    /**
     * Update session by invoice — used when we don't have session_id handy
     */
    function updateActiveSessionByInvoice($invoiceId, $newStatus, $posReqId = null)
    {
        $data = array('status' => $newStatus);
        if($posReqId) $data['pos_req_id'] = $posReqId;
        
        $this->db->where('invoice_id', $invoiceId);
        $this->db->where_in('status', array('INIT','PENDING'));
        return $this->db->update('pos_payment_sessions', $data);
    }

    /**
     * Auto-expire stale INIT/PENDING sessions past their expires_at
     */
    function expireStaleSessions()
    {
        if(!$this->_posTableSafe('pos_payment_sessions')) return;
        $oldDebug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->query("UPDATE pos_payment_sessions SET status = 'EXPIRED' 
                          WHERE status IN ('INIT','PENDING') AND expires_at < NOW()");
        $this->db->db_debug = $oldDebug;
    }

    /**
     * Get PENDING sessions for background reconciliation
     * Only sessions older than 30 seconds (give normal flow time to complete)
     */
    function getPendingSessionsForReconciliation()
    {
        $this->expireStaleSessions();
        
        $sql = "SELECT s.*, d.merchantid, d.id_provider,
                       p.provider_code, p.provider_name
                FROM pos_payment_sessions s
                LEFT JOIN ret_pos_device_list d ON d.id_device = s.device_id
                LEFT JOIN ret_pos_providers p ON p.id_provider = d.id_provider
                WHERE s.status = 'PENDING'
                AND s.created_at < DATE_SUB(NOW(), INTERVAL 30 SECOND)
                ORDER BY s.created_at ASC";
        return $this->db->query($sql)->result_array();
    }

    // ============================================
    // ORPHAN DETECTION (Phase 3)
    // ============================================

    /**
     * Find POS payments that succeeded at provider but have no billing record
     * (Customer paid, ERP crashed before bill save)
     */
    function findOrphanPayments($daysBack = 7)
    {
        if(!$this->_posTableSafe('ret_pos_requests')) return array();
        $sql = "SELECT r.pos_req_id, r.pos_trans_no, r.pos_req_amount/100 as amount_rs,
                       r.pos_utr, r.provider_code, r.payment_mode, r.card_last4,
                       r.created_at, r.pos_req_bill_cusid,
                       IFNULL(d.dispname, 'Unknown') as device_name,
                       IFNULL(e.firstname, CONCAT('UID:', r.pos_req_createdby)) as employee_name,
                       IFNULL(c.firstname, '') as customer_name
                FROM ret_pos_requests r
                LEFT JOIN ret_billing_payment bp ON bp.pos_req_id = r.pos_req_id
                LEFT JOIN ret_pos_device_list d ON d.id_device = r.id_device
                LEFT JOIN employee e ON e.id_employee = r.pos_usr_id
                LEFT JOIN customer c ON c.id_customer = r.pos_req_bill_cusid
                WHERE r.pos_req_status = 1
                  AND bp.payment_id IS NULL
                  AND r.pos_req_bill_id IS NOT NULL
                  AND r.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                ORDER BY r.created_at DESC";
        return $this->db->query($sql, array(intval($daysBack)))->result_array();
    }

    /**
     * Find billing payments marked as POS but with no matching POS record
     * (Billing record exists but POS log is missing/corrupt)
     */
    function findOrphanBillingPayments($daysBack = 7)
    {
        if(!$this->_posTableSafe('ret_pos_requests')) return array();
        $oldDebug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $sql = "SELECT bp.payment_id, bp.bill_id, bp.payment_amount,
                       bp.pos_req_id, bp.payment_date,
                       IFNULL(b.bill_est_no, '') as bill_no
                FROM ret_billing_payment bp
                LEFT JOIN ret_pos_requests r ON r.pos_req_id = bp.pos_req_id
                LEFT JOIN ret_billing b ON b.bill_id = bp.bill_id
                WHERE bp.pos_req_id IS NOT NULL
                  AND r.pos_req_id IS NULL
                  AND bp.payment_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
                ORDER BY bp.payment_id DESC";
        $result = $this->db->query($sql, array(intval($daysBack)));
        $this->db->db_debug = $oldDebug;
        return $result ? $result->result_array() : array();
    }

    /**
     * Find sessions stuck in INIT/PENDING past their expiry
     */
    function getStaleExpiredSessions($limit = 50)
    {
        if(!$this->_posTableSafe('pos_payment_sessions')) return array();
        $sql = "SELECT s.session_id, s.invoice_id, s.amount_paise/100 as amount_rs,
                       s.status, s.created_at, s.expires_at, s.provider_code,
                       IFNULL(d.dispname, 'Unknown') as device_name
                FROM pos_payment_sessions s
                LEFT JOIN ret_pos_device_list d ON d.id_device = s.device_id
                WHERE s.status IN ('INIT', 'PENDING')
                  AND s.expires_at < NOW()
                ORDER BY s.created_at DESC
                LIMIT ?";
        return $this->db->query($sql, array(intval($limit)))->result_array();
    }

    // ============================================
    // SETTLEMENT RECONCILIATION (Phase 3)
    // ============================================

    /**
     * Mark transactions as settled when bank confirms receipt
     * @param array $posReqIds - IDs of settled transactions
     * @param int $settlementId - FK to pos_settlements table
     * @param string $settledDate - Date of settlement (Y-m-d)
     */
    function markTransactionsSettled($posReqIds, $settlementId, $settledDate)
    {
        if (empty($posReqIds) || !is_array($posReqIds)) return 0;
        
        $this->db->where_in('pos_req_id', array_map('intval', $posReqIds));
        $this->db->where('pos_req_status', 1); // Only settle successful txns
        $this->db->update('ret_pos_requests', array(
            'settlement_id' => intval($settlementId),
            'settled_at'    => $settledDate
        ));
        return $this->db->affected_rows();
    }

    /**
     * Get unsettled successful transactions (for reconciliation dashboard)
     * @param int $daysOld - Only show txns older than this many days
     */
    function getUnsettledTransactions($daysOld = 2)
    {
        if(!$this->_posTableSafe('ret_pos_requests')) return array();
        $sql = "SELECT r.pos_req_id, r.pos_trans_no, r.pos_req_amount/100 as amount_rs,
                       r.provider_code, r.pos_utr, r.created_at,
                       IFNULL(d.dispname, 'Unknown') as device_name
                FROM ret_pos_requests r
                LEFT JOIN ret_pos_device_list d ON d.id_device = r.id_device
                WHERE r.pos_req_status = 1
                  AND r.settlement_id IS NULL
                  AND r.created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
                ORDER BY r.created_at";
        return $this->db->query($sql, array(intval($daysOld)))->result_array();
    }

}
