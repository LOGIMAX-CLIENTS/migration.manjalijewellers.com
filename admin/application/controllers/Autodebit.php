<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Autodebit Controller
 * 
 * Handles Cashfree Subscription-based recurring auto-debit for scheme payments.
 * 
 * Public (no auth): webhook, callback, redirect
 * Protected (session required): create, cancel, retry
 * 
 * Cashfree pg_code = 4
 * payment.added_by = 4 (Cashfree Subscription)
 * 
 * Status mapping (auto_debit_subscription.status):
 *   INITIALIZED, BANK_APPROVAL_PENDING, ACTIVE, ON_HOLD, CANCELLED, COMPLETED
 * 
 * auto_debit_status (scheme_account column):
 *   0=None, 1=Pending, 2=Active, 3=Paused, 4=Cancelled, 5=Completed
 */
class Autodebit extends CI_Controller
{
    private $log_dir;

    function __construct()
    {
        parent::__construct();
        $this->load->model('autodebit_model');
        $this->load->model('payment_model');
        $this->load->model('chit_transaction_model');

        // Set up logging directory
        $this->log_dir = 'log/' . date('Y-m-d');
        if (!is_dir($this->log_dir)) {
            mkdir($this->log_dir, 0777, TRUE);
        }
    }

    // ═══════════════════════════════════════════════════════════
    // Phase 1: CREATE SUBSCRIPTION
    // POST /subscription/create/{id_scheme_account}
    // ═══════════════════════════════════════════════════════════

    function create($id_scheme_account)
    {
        // Validate scheme account
        $planDetail = $this->autodebit_model->getSubscriptionData($id_scheme_account);

        if (empty($planDetail)) {
            echo json_encode(array('status' => false, 'msg' => 'Scheme account not found'));
            return;
        }

        // Check if account is active and not closed
        if ($planDetail['active'] != 1 || $planDetail['is_closed'] == 1) {
            echo json_encode(array('status' => false, 'msg' => 'Scheme account is not active'));
            return;
        }

        // Check if auto-debit is enabled globally
        if ($this->config->item('auto_debit') != 1) {
            echo json_encode(array('status' => false, 'msg' => 'Auto-debit is not enabled'));
            return;
        }

        // Check if subscription already exists and is active
        $existingSub = $this->autodebit_model->getActiveSubscription($id_scheme_account);
        if (!empty($existingSub)) {
            echo json_encode(array(
                'status' => false,
                'msg' => 'Active subscription already exists',
                'data' => array(
                    'status'    => $existingSub['status'],
                    'auth_link' => $existingSub['auth_link']
                )
            ));
            return;
        }

        // Get gateway credentials
        $gateway = $this->autodebit_model->getGatewayData($planDetail['id_branch']);
        if (empty($gateway)) {
            echo json_encode(array('status' => false, 'msg' => 'Payment gateway not configured'));
            return;
        }

        $clientId  = $gateway['param_3'];
        $secretKey = $gateway['param_1'];

        // Determine plan amount based on scheme_type
        // scheme_type: 0=Amount, 1=Weight, 2=Amount to Weight, 3=Flexible Amount
        $plan_amount = 0;
        if ($planDetail['scheme_type'] == 0 || $planDetail['scheme_type'] == 2) {
            $plan_amount = floatval($planDetail['scheme_amount']);
        } elseif ($planDetail['scheme_type'] == 3) {
            $plan_amount = floatval($planDetail['min_amount']);
        } else {
            // Weight-based scheme — amount comes from POST (app calculates based on rate × weight)
            $plan_amount = floatval($this->input->post('plan_amount'));
        }

        if ($plan_amount <= 0) {
            echo json_encode(array('status' => false, 'msg' => 'Invalid plan amount'));
            return;
        }

        // Determine plan type: PERIODIC or ON_DEMAND
        // auto_debit_plan_type: 0=Periodic, 1=On Demand
        $plan_type = ($planDetail['auto_debit_plan_type'] == 1) ? 'ON_DEMAND' : 'PERIODIC';

        // Generate unique subscription_id
        $subscription_id = 'SUB_' . $id_scheme_account . '_' . time();

        // Determine return URL based on request source
        $is_mobile = $this->_isMobileBrowser();
        $base_url  = base_url();
        $return_url = $base_url . 'autodebit/callback/' . $id_scheme_account;

        // Calculate first charge date (next month 1st or scheme-specific logic)
        $first_charge_date = date('Y-m-d', strtotime('first day of next month'));

        // Build Cashfree subscription payload
        $cf_payload = array(
            'subscription_id'     => $subscription_id,
            'customer_details'    => array(
                'customer_name'  => trim($planDetail['customer_name']),
                'customer_email' => !empty($planDetail['email']) ? $planDetail['email'] : 'noemail@example.com',
                'customer_phone' => $planDetail['mobile']
            ),
            'plan_details' => array(
                'plan_name'          => $planDetail['scheme_name'] . ' - ' . $planDetail['code'],
                'plan_type'          => $plan_type,
                'plan_currency'      => 'INR',
                'plan_recurring_amount' => $plan_amount,
                'plan_max_amount'    => $plan_amount,
                'plan_max_cycles'    => intval($planDetail['total_installments'])
            ),
            'subscription_meta' => array(
                'return_url' => $return_url
            ),
            'authorization_details' => array(
                'authorization_amount' => 1 // Re 1 authorization charge (refundable)
            )
        );

        // Add periodic-specific fields
        if ($plan_type == 'PERIODIC') {
            $cf_payload['plan_details']['plan_recurring_interval'] = 1;
            $cf_payload['plan_details']['plan_recurring_interval_type'] = 1; // 1 = Month
            $cf_payload['subscription_meta']['first_charge_date'] = $first_charge_date;
        }

        // Log request
        $this->_logToFile('cf_subscription', 'create_request', array(
            'id_scheme_account' => $id_scheme_account,
            'payload'           => $cf_payload
        ));

        // Call Cashfree API — use gateway.api_url for sandbox/production switching
        $api_url  = rtrim($gateway['api_url'], '/') . '/subscriptions';
        $response = $this->_cashfreeApiCall($api_url, $cf_payload, $clientId, $secretKey, 'POST');

        // Log response
        $this->_logToFile('cf_subscription', 'create_response', $response);

        if (isset($response['cf_subscription_id'])) {
            // Success — save subscription record
            $sub_data = array(
                'id_scheme_account' => $id_scheme_account,
                'subscription_id'   => $subscription_id,
                'cf_subscription_id' => $response['cf_subscription_id'],
                'sub_reference_id'  => isset($response['sub_reference_id']) ? $response['sub_reference_id'] : null,
                'status'            => isset($response['status']) ? $response['status'] : 'INITIALIZED',
                'plan_amount'       => $plan_amount,
                'plan_type'         => $plan_type,
                'plan_max_cycles'   => intval($planDetail['total_installments']),
                'first_charge_date' => $first_charge_date,
                'auth_link'         => isset($response['authorization_link']) ? $response['authorization_link'] : null,
                'cf_response'       => json_encode($response),
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s')
            );

            $insert_id = $this->autodebit_model->insertSubscription($sub_data);

            // Update scheme_account.auto_debit_status = 1 (Pending)
            $this->autodebit_model->updateSchemeAccountAutoDebitStatus($id_scheme_account, 1);

            echo json_encode(array(
                'status' => true,
                'msg'    => 'Subscription created successfully',
                'data'   => array(
                    'subscription_id'   => $subscription_id,
                    'auth_link'         => isset($response['authorization_link']) ? $response['authorization_link'] : '',
                    'cf_subscription_id' => $response['cf_subscription_id'],
                    'sub_status'        => isset($response['status']) ? $response['status'] : 'INITIALIZED'
                )
            ));
        } else {
            // API error
            $error_msg = isset($response['message']) ? $response['message'] : 'Failed to create subscription';
            echo json_encode(array(
                'status' => false,
                'msg'    => $error_msg,
                'data'   => $response
            ));
        }
    }

    // ═══════════════════════════════════════════════════════════
    // Phase 3: CALLBACK (Return URL after authorization)
    // POST/GET /autodebit/callback/{id_scheme_account}
    // ═══════════════════════════════════════════════════════════

    function callback($id_scheme_account)
    {
        // Cashfree redirects here after customer authorizes the mandate
        $subscription_id = $this->input->get_post('subscription_id');
        $status          = $this->input->get_post('subscription_status');

        $this->_logToFile('cf_subscription', 'callback', array(
            'id_scheme_account' => $id_scheme_account,
            'subscription_id'   => $subscription_id,
            'status'            => $status,
            'GET'               => $_GET,
            'POST'              => $_POST
        ));

        if (!empty($subscription_id)) {
            // Update subscription status from callback
            $update_data = array(
                'status'     => !empty($status) ? strtoupper($status) : 'BANK_APPROVAL_PENDING',
                'updated_at' => date('Y-m-d H:i:s')
            );

            $this->autodebit_model->updateSubscriptionBySubId($subscription_id, $update_data);

            // Map status to scheme_account.auto_debit_status
            $acc_status = $this->_mapStatusToAccountStatus($update_data['status']);
            $this->autodebit_model->updateSchemeAccountAutoDebitStatus($id_scheme_account, $acc_status);
        }

        // Determine redirect based on source (mobile app vs web)
        $is_mobile = $this->_isMobileBrowser();

        if ($is_mobile) {
            // Redirect to mobile app deep link or status page
            $redirect_status = ($status == 'ACTIVE') ? 'success' : 'pending';
            redirect(base_url('autodebit/redirect/' . $redirect_status . '/auth'));
        } else {
            // Redirect to admin dashboard or show status message
            $msg = ($status == 'ACTIVE')
                ? 'Auto-debit subscription activated successfully!'
                : 'Authorization is pending bank approval. Status will be updated automatically.';

            $this->session->set_flashdata('chit_alert', array(
                'message' => $msg,
                'class'   => ($status == 'ACTIVE') ? 'success' : 'info',
                'title'   => 'Auto Debit'
            ));
            redirect(base_url());
        }
    }

    // ═══════════════════════════════════════════════════════════
    // Phase 4-6: WEBHOOK (Cashfree server-to-server notifications)
    // POST /autodebit/webhook
    // ═══════════════════════════════════════════════════════════

    function webhook()
    {
        // Read raw POST body
        $raw_post = file_get_contents('php://input');

        $this->_logToFile('cf_hook', 'raw_webhook', $raw_post);

        $postData = json_decode($raw_post, true);

        if (empty($postData) || !isset($postData['type'])) {
            $this->_logToFile('cf_hook', 'invalid_webhook', 'Empty or invalid payload');
            http_response_code(400);
            echo json_encode(array('status' => false, 'msg' => 'Invalid payload'));
            return;
        }

        // Verify signature if present
        $signature = isset($_SERVER['HTTP_X_CASHFREE_SIGNATURE']) ? $_SERVER['HTTP_X_CASHFREE_SIGNATURE'] : '';
        if (!empty($signature)) {
            // Get gateway to fetch secret key — use first available gateway
            $gateway = $this->autodebit_model->getGatewayData('');
            if (!empty($gateway)) {
                $is_valid = $this->_verifySignature($raw_post, $signature, $gateway['param_1']);
                if (!$is_valid) {
                    $this->_logToFile('cf_hook', 'signature_failed', array(
                        'signature' => $signature,
                        'raw_post'  => $raw_post
                    ));
                    http_response_code(401);
                    echo json_encode(array('status' => false, 'msg' => 'Signature verification failed'));
                    return;
                }
            }
        }

        $event_type = $postData['type'];

        $this->_logToFile('cf_hook', 'event_' . $event_type, $postData);

        // Route to appropriate handler
        switch ($event_type) {
            case 'SUBSCRIPTION_NEW_PAYMENT':
                $this->_handleNewPayment($postData);
                break;

            case 'SUBSCRIPTION_PAYMENT_DECLINED':
                $this->_handleDeclinedPayment($postData);
                break;

            case 'SUBSCRIPTION_STATUS_CHANGE':
                $this->_handleStatusChange($postData);
                break;

            default:
                $this->_logToFile('cf_hook', 'unhandled_event', $event_type);
                break;
        }

        // Always respond 200 to Cashfree
        http_response_code(200);
        echo json_encode(array('status' => true, 'msg' => 'Webhook processed'));
    }

    // ═══════════════════════════════════════════════════════════
    // Phase 7: CANCEL SUBSCRIPTION
    // POST /subscription/cancel/{id_scheme_account}
    // ═══════════════════════════════════════════════════════════

    function cancel($id_scheme_account)
    {
        // Get active subscription
        $sub = $this->autodebit_model->getActiveSubscription($id_scheme_account);

        if (empty($sub)) {
            echo json_encode(array('status' => false, 'msg' => 'No active subscription found'));
            return;
        }

        // Get gateway credentials
        $planDetail = $this->autodebit_model->getSubscriptionData($id_scheme_account);
        $gateway = $this->autodebit_model->getGatewayData(isset($planDetail['id_branch']) ? $planDetail['id_branch'] : '');

        if (empty($gateway)) {
            echo json_encode(array('status' => false, 'msg' => 'Payment gateway not configured'));
            return;
        }

        $clientId  = $gateway['param_3'];
        $secretKey = $gateway['param_1'];

        // Call Cashfree cancel API — use gateway.api_url for sandbox/production switching
        $api_url = rtrim($gateway['api_url'], '/') . '/subscriptions/' . $sub['subscription_id'] . '/cancel';

        $response = $this->_cashfreeApiCall($api_url, array(), $clientId, $secretKey, 'POST');

        $this->_logToFile('cf_subscription', 'cancel_response', array(
            'id_scheme_account' => $id_scheme_account,
            'subscription_id'   => $sub['subscription_id'],
            'response'          => $response
        ));

        // Update subscription status regardless of API response
        $this->autodebit_model->updateSubscriptionById($sub['id_subscription'], array(
            'status'     => 'CANCELLED',
            'updated_at' => date('Y-m-d H:i:s')
        ));

        // Update scheme_account.auto_debit_status = 4 (Cancelled)
        $this->autodebit_model->updateSchemeAccountAutoDebitStatus($id_scheme_account, 4);

        echo json_encode(array(
            'status' => true,
            'msg'    => 'Subscription cancelled successfully'
        ));
    }

    // ═══════════════════════════════════════════════════════════
    // Phase 8: RETRY FAILED PAYMENT
    // POST /subscription/retry/{id_scheme_account}
    // ═══════════════════════════════════════════════════════════

    function retry($id_scheme_account)
    {
        // Get active subscription
        $sub = $this->autodebit_model->getActiveSubscription($id_scheme_account);

        if (empty($sub)) {
            echo json_encode(array('status' => false, 'msg' => 'No active subscription found'));
            return;
        }

        // Get gateway credentials
        $planDetail = $this->autodebit_model->getSubscriptionData($id_scheme_account);
        $gateway = $this->autodebit_model->getGatewayData(isset($planDetail['id_branch']) ? $planDetail['id_branch'] : '');

        if (empty($gateway)) {
            echo json_encode(array('status' => false, 'msg' => 'Payment gateway not configured'));
            return;
        }

        $clientId  = $gateway['param_3'];
        $secretKey = $gateway['param_1'];

        // Call Cashfree charge API to retry — use gateway.api_url for sandbox/production switching
        $api_url = rtrim($gateway['api_url'], '/') . '/subscriptions/' . $sub['subscription_id'] . '/charge';

        $charge_payload = array(
            'subscription_id' => $sub['subscription_id'],
            'payment_amount'  => floatval($sub['plan_amount'])
        );

        $response = $this->_cashfreeApiCall($api_url, $charge_payload, $clientId, $secretKey, 'POST');

        $this->_logToFile('cf_subscription', 'retry_response', array(
            'id_scheme_account' => $id_scheme_account,
            'subscription_id'   => $sub['subscription_id'],
            'response'          => $response
        ));

        if (isset($response['status']) && $response['status'] == 'OK') {
            echo json_encode(array(
                'status' => true,
                'msg'    => 'Payment retry initiated successfully'
            ));
        } else {
            $error_msg = isset($response['message']) ? $response['message'] : 'Retry failed';
            echo json_encode(array(
                'status' => false,
                'msg'    => $error_msg,
                'data'   => $response
            ));
        }
    }

    // ═══════════════════════════════════════════════════════════
    // REDIRECT (Mobile app status page)
    // GET /autodebit/redirect/{status}/{flag?}
    // ═══════════════════════════════════════════════════════════

    function redirect($status = 'success', $flag = '')
    {
        $data = array(
            'status'  => $status,
            'flag'    => $flag,
            'message' => ''
        );

        switch ($status) {
            case 'success':
                $data['message'] = 'Auto-debit has been activated successfully! Your monthly payments will be deducted automatically.';
                break;
            case 'pending':
                $data['message'] = 'Your authorization is pending bank approval. We will notify you once it is active.';
                break;
            case 'failed':
                $data['message'] = 'Authorization failed. Please try again or contact support.';
                break;
            case 'cancelled':
                $data['message'] = 'Auto-debit has been cancelled.';
                break;
            default:
                $data['message'] = 'Status: ' . htmlspecialchars($status, ENT_QUOTES, 'UTF-8');
                break;
        }

        // Simple HTML page for mobile app to read status
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Auto Debit Status</title>'
            . '<style>body{font-family:Arial,sans-serif;display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0;background:#f5f5f5;}'
            . '.card{background:#fff;border-radius:12px;padding:32px;max-width:400px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,0.1);}'
            . '.icon{font-size:48px;margin-bottom:16px;}'
            . '.title{font-size:20px;font-weight:600;margin-bottom:8px;}'
            . '.msg{color:#666;line-height:1.5;}'
            . '.success .icon{color:#4CAF50;} .pending .icon{color:#FF9800;} .failed .icon{color:#F44336;} .cancelled .icon{color:#9E9E9E;}'
            . '</style></head><body>'
            . '<div class="card ' . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . '">'
            . '<div class="icon">' . ($status == 'success' ? '✓' : ($status == 'pending' ? '⏳' : ($status == 'failed' ? '✗' : '⊘'))) . '</div>'
            . '<div class="title">' . ucfirst(htmlspecialchars($status, ENT_QUOTES, 'UTF-8')) . '</div>'
            . '<div class="msg">' . htmlspecialchars($data['message'], ENT_QUOTES, 'UTF-8') . '</div>'
            . '</div></body></html>';
    }

    // ═══════════════════════════════════════════════════════════
    // PRIVATE HELPER METHODS
    // ═══════════════════════════════════════════════════════════

    /**
     * Handle SUBSCRIPTION_NEW_PAYMENT webhook.
     * Phase 4-5: Insert payment record if not duplicate.
     */
    private function _handleNewPayment($postData)
    {
        $data = isset($postData['data']) ? $postData['data'] : array();

        $sub_reference_id  = isset($data['subscription']['sub_reference_id']) ? $data['subscription']['sub_reference_id'] : '';
        $cf_payment_id     = isset($data['payment']['cf_payment_id']) ? $data['payment']['cf_payment_id'] : '';
        $payment_amount    = isset($data['payment']['payment_amount']) ? floatval($data['payment']['payment_amount']) : 0;
        $payment_status_cf = isset($data['payment']['payment_status']) ? $data['payment']['payment_status'] : '';

        if (empty($sub_reference_id) || empty($cf_payment_id)) {
            $this->_logToFile('cf_hook', 'missing_data', 'sub_reference_id or cf_payment_id missing');
            return;
        }

        // Get subscription details
        $sub = $this->autodebit_model->getSubscriptionBySubRefId($sub_reference_id);
        if (empty($sub)) {
            $this->_logToFile('cf_hook', 'sub_not_found', 'sub_reference_id: ' . $sub_reference_id);
            return;
        }

        // Duplicate check — prevent double payment from retried webhooks
        $ref_number = 'ADSUB_' . $cf_payment_id;
        if ($this->autodebit_model->isPaymentExists($ref_number)) {
            $this->_logToFile('cf_hook', 'duplicate_payment', 'Ref: ' . $ref_number);
            return;
        }

        // Only process SUCCESS payments
        if (strtoupper($payment_status_cf) != 'SUCCESS') {
            $this->_logToFile('cf_hook', 'non_success_payment', $payment_status_cf);
            return;
        }

        $id_scheme_account = $sub['id_scheme_account'];

        // Get current metal rate for weight calculation
        $metal_rate = $this->autodebit_model->getCurrentMetalRate($sub['id_branch']);

        // Calculate weight for amount-based schemes
        $metal_weight = 0;
        if ($metal_rate > 0 && $payment_amount > 0) {
            $metal_weight = round($payment_amount / $metal_rate, 3);
        }

        // Calculate due month/year
        $due_info = $this->_calculateDueMonthYear($id_scheme_account);

        // Get gateway info for id_payGateway (needed by onPayTranStream for payment_mode_details)
        $gateway = $this->autodebit_model->getGatewayData($sub['id_branch']);
        $id_payGateway = !empty($gateway['id_pg']) ? $gateway['id_pg'] : null;

        // Build payment record — follows existing pattern from admin_services.php
        $pay_array = array(
            'id_scheme_account' => $id_scheme_account,
            'id_scheme'         => $sub['id_scheme'],
            'id_branch'         => $sub['id_branch'],
            'date_payment'      => date('Y-m-d H:i:s'),
            'date_add'          => date('Y-m-d H:i:s'),
            'metal_rate'        => $metal_rate,
            'payment_amount'    => $payment_amount,
            'actual_trans_amt'  => $payment_amount,
            'metal_weight'      => $metal_weight,
            'payment_mode'      => 'Subscription',
            'payment_status'    => ($this->config->item('auto_pay_approval') == 1 || $this->config->item('auto_pay_approval') == 2) ? 1 : 2, // 1=Approved, 2=Awaiting
            'payment_type'      => 'Auto Debit',
            'due_type'          => 'D', // Due payment
            'installment'       => isset($due_info['installment']) ? $due_info['installment'] : 0,
            'due_month'         => isset($due_info['due_month']) ? $due_info['due_month'] : date('m'),
            'due_year'          => isset($due_info['due_year']) ? $due_info['due_year'] : date('Y'),
            'receipt_no'        => null,
            'remark'            => 'Auto-Debit Subscription Payment :: CF#' . $cf_payment_id,
            'payment_ref_number' => $ref_number,
            'payu_id'           => $cf_payment_id,
            'ref_trans_id'      => $sub['subscription_id'],
            'id_payGateway'     => $id_payGateway, // Links to gateway table for payment_mode_details
            'added_by'          => 4, // 4 = Cashfree Subscription
            'no_of_dues'        => 1,
            'date_upd'          => date('Y-m-d H:i:s')
        );

        // Use transaction for safety
        $this->db->trans_start();

        $payment_id = $this->autodebit_model->insertPayment($pay_array);

        if ($payment_id > 0) {
            // Post-payment processing: use canonical onPayTranStream()
            // This handles ALL post-payment operations in one call:
            //   - payment_mode_details insert
            //   - receipt number generation
            //   - scheme account number generation
            //   - client ID generation
            //   - start_date / maturity_date update (first payment)
            //   - due date / installment / grace date calculation
            //   - total_paid_ins update
            //   - employee/customer referral incentives
            //   - inter-tool integration
            //   - first payable fix
            // Same function used by mobile_api webhook, admin payment, and manual verification
            $this->chit_transaction_model->onPayTranStream($payment_id);

            // Update subscription's last_payment_date and charge_count
            $this->autodebit_model->updateSubscriptionBySubRefId($sub_reference_id, array(
                'last_payment_date' => date('Y-m-d H:i:s'),
                'charge_count'      => intval($sub['charge_count']) + 1,
                'updated_at'        => date('Y-m-d H:i:s')
            ));
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->_logToFile('cf_hook', 'transaction_failed', array(
                'id_scheme_account' => $id_scheme_account,
                'cf_payment_id'     => $cf_payment_id
            ));
        } else {
            $this->_logToFile('cf_hook', 'payment_inserted', array(
                'id_payment'        => $payment_id,
                'id_scheme_account' => $id_scheme_account,
                'amount'            => $payment_amount
            ));
        }
    }

    /**
     * Handle SUBSCRIPTION_PAYMENT_DECLINED webhook.
     * Phase 5: Record failed payment.
     */
    private function _handleDeclinedPayment($postData)
    {
        $data = isset($postData['data']) ? $postData['data'] : array();

        $sub_reference_id = isset($data['subscription']['sub_reference_id']) ? $data['subscription']['sub_reference_id'] : '';
        $cf_payment_id    = isset($data['payment']['cf_payment_id']) ? $data['payment']['cf_payment_id'] : '';
        $payment_amount   = isset($data['payment']['payment_amount']) ? floatval($data['payment']['payment_amount']) : 0;
        $failure_reason   = isset($data['payment']['payment_message']) ? $data['payment']['payment_message'] : 'Payment Declined';

        if (empty($sub_reference_id)) {
            return;
        }

        $sub = $this->autodebit_model->getSubscriptionBySubRefId($sub_reference_id);
        if (empty($sub)) {
            return;
        }

        // Duplicate check
        $ref_number = 'ADSUB_FAIL_' . $cf_payment_id;
        if ($this->autodebit_model->isPaymentExists($ref_number)) {
            return;
        }

        $id_scheme_account = $sub['id_scheme_account'];

        // Insert a failed payment record for tracking
        $pay_array = array(
            'id_scheme_account' => $id_scheme_account,
            'id_scheme'         => $sub['id_scheme'],
            'id_branch'         => $sub['id_branch'],
            'date_payment'      => date('Y-m-d H:i:s'),
            'date_add'          => date('Y-m-d H:i:s'),
            'payment_amount'    => $payment_amount,
            'actual_trans_amt'  => $payment_amount,
            'payment_mode'      => 'Subscription',
            'payment_status'    => 3, // 3 = Failed
            'payment_type'      => 'Auto Debit',
            'due_type'          => 'D',
            'remark'            => 'Auto-Debit DECLINED: ' . $this->db->escape_str($failure_reason),
            'payment_ref_number' => $ref_number,
            'payu_id'           => $cf_payment_id,
            'ref_trans_id'      => $sub['subscription_id'],
            'added_by'          => 4, // 4 = Cashfree Subscription
            'date_upd'          => date('Y-m-d H:i:s')
        );

        $this->autodebit_model->insertPayment($pay_array);

        // Update subscription failure reason
        $this->autodebit_model->updateSubscriptionBySubRefId($sub_reference_id, array(
            'failure_reason' => $this->db->escape_str($failure_reason),
            'updated_at'     => date('Y-m-d H:i:s')
        ));

        $this->_logToFile('cf_hook', 'payment_declined', array(
            'id_scheme_account' => $id_scheme_account,
            'cf_payment_id'     => $cf_payment_id,
            'reason'            => $failure_reason
        ));
    }

    /**
     * Handle SUBSCRIPTION_STATUS_CHANGE webhook.
     * Phase 6: Update subscription and account status.
     */
    private function _handleStatusChange($postData)
    {
        $data = isset($postData['data']) ? $postData['data'] : array();

        $sub_reference_id = isset($data['subscription']['sub_reference_id']) ? $data['subscription']['sub_reference_id'] : '';
        $new_status       = isset($data['subscription']['status']) ? strtoupper($data['subscription']['status']) : '';

        if (empty($sub_reference_id) || empty($new_status)) {
            return;
        }

        $sub = $this->autodebit_model->getSubscriptionBySubRefId($sub_reference_id);
        if (empty($sub)) {
            return;
        }

        // Update subscription status
        $this->autodebit_model->updateSubscriptionBySubRefId($sub_reference_id, array(
            'status'     => $new_status,
            'updated_at' => date('Y-m-d H:i:s')
        ));

        // Update scheme_account.auto_debit_status
        $acc_status = $this->_mapStatusToAccountStatus($new_status);
        $this->autodebit_model->updateSchemeAccountAutoDebitStatus($sub['id_scheme_account'], $acc_status);

        $this->_logToFile('cf_hook', 'status_change', array(
            'id_scheme_account' => $sub['id_scheme_account'],
            'sub_reference_id'  => $sub_reference_id,
            'old_status'        => $sub['status'],
            'new_status'        => $new_status
        ));
    }

    /**
     * Map Cashfree subscription status to scheme_account.auto_debit_status integer.
     * 0=None, 1=Pending, 2=Active, 3=Paused, 4=Cancelled, 5=Completed
     */
    private function _mapStatusToAccountStatus($cf_status)
    {
        $map = array(
            'INITIALIZED'           => 1,
            'BANK_APPROVAL_PENDING' => 1,
            'ACTIVE'                => 2,
            'ON_HOLD'               => 3,
            'PAUSED'                => 3,
            'CANCELLED'             => 4,
            'COMPLETED'             => 5
        );

        return isset($map[$cf_status]) ? $map[$cf_status] : 0;
    }

    /**
     * Calculate next due month/year for auto-debit payment.
     */
    private function _calculateDueMonthYear($id_scheme_account)
    {
        $last = $this->autodebit_model->getLastDueMonthYear($id_scheme_account);

        if (!empty($last) && isset($last['due_month']) && isset($last['due_year'])) {
            // Calculate next month from last paid
            $last_month = intval($last['due_month']);
            $last_year  = intval($last['due_year']);
            $installment = intval($last['installment']) + 1;

            if ($last_month >= 12) {
                $due_month = 1;
                $due_year  = $last_year + 1;
            } else {
                $due_month = $last_month + 1;
                $due_year  = $last_year;
            }

            return array(
                'due_month'   => $due_month,
                'due_year'    => $due_year,
                'installment' => $installment
            );
        }

        // Fallback: use current month
        return array(
            'due_month'   => intval(date('m')),
            'due_year'    => intval(date('Y')),
            'installment' => 1
        );
    }

    // NOTE: _postPaymentProcessing() was removed — replaced by chit_transaction_model->onPayTranStream()
    // which handles all 13 post-payment operations in a single canonical call.

    /**
     * cURL wrapper for Cashfree Subscription API calls.
     */
    private function _cashfreeApiCall($url, $payload, $clientId, $secretKey, $method = 'POST')
    {
        $curl = curl_init();

        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json',
                'x-client-id: ' . $clientId,
                'x-client-secret: ' . $secretKey,
                'x-api-version: 2025-01-01'
            )
        );

        if ($method == 'POST' && !empty($payload)) {
            $options[CURLOPT_POSTFIELDS] = json_encode($payload);
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $err      = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($err) {
            $this->_logToFile('cf_subscription', 'curl_error', array(
                'url'   => $url,
                'error' => $err
            ));
            return array('status' => false, 'message' => 'cURL Error: ' . $err);
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : array('raw_response' => $response, 'http_code' => $httpCode);
    }

    /**
     * Verify Cashfree webhook signature using HMAC-SHA256.
     */
    private function _verifySignature($payload, $signature, $secretKey)
    {
        $computed = base64_encode(hash_hmac('sha256', $payload, $secretKey, true));
        return hash_equals($computed, $signature);
    }

    /**
     * Log data to file — follows existing pattern from admin_services.php.
     */
    private function _logToFile($subfolder, $suffix, $data)
    {
        $dir = $this->log_dir . '/' . $subfolder;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $log_path = $dir . '/' . $suffix . '_' . date('Y-m-d') . '.txt';
        $log_data = "\n ----- \n" . date('Y-m-d H:i:s') . ': '
                    . (is_array($data) || is_object($data) ? json_encode($data) : $data);

        file_put_contents($log_path, $log_data, FILE_APPEND | LOCK_EX);
    }

    /**
     * Detect if request is from a mobile browser.
     */
    private function _isMobileBrowser()
    {
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        return (bool) preg_match('/Android|iPhone|iPad|iPod|webOS|BlackBerry|Windows Phone/i', $user_agent);
    }
}
