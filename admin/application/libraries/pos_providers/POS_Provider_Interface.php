<?php
/**
 * POS Provider Interface
 * All POS provider plugins must implement this interface.
 * 
 * To add a new provider (e.g. Razorpay):
 * 1. Create application/libraries/pos_providers/POS_Razorpay.php
 * 2. Implement this interface
 * 3. Add provider record in ret_pos_providers DB table
 * 4. Done — zero controller changes needed
 */
interface POS_Provider_Interface {

    /**
     * Initialize a POS payment
     * @param array $addData    - cusid, amount, billid, deviceId, idempotency_key
     * @param array $posDetails - merchantid, securitytoken, salt_key, api_base_url, provider_code, etc.
     * @param object $CI        - CodeIgniter instance
     * @return array with keys: responsecode, resmessage, refid, [qrdata], [provider]
     */
    public function initPayment($addData, $posDetails, $CI);

    /**
     * Check payment status
     * @param array $addData    - refcode (transaction reference)
     * @param array $posDetails - device/provider config
     * @param object $CI        - CodeIgniter instance
     * @return array with keys: responsecode, resmessage, [status], [transaction data]
     */
    public function checkStatus($addData, $posDetails, $CI);

    /**
     * Cancel a payment
     * @param array $addData    - refcode (transaction reference)
     * @param array $posDetails - device/provider config
     * @param object $CI        - CodeIgniter instance
     * @return array with keys: responsecode, resmessage
     */
    public function cancelPayment($addData, $posDetails, $CI);

    /**
     * Get provider code (must match provider_code in ret_pos_providers)
     * @return string
     */
    public function getProviderCode();

    /**
     * Get human-readable provider name
     * @return string
     */
    public function getProviderName();
}
