# Playbook #11: SMS/Notification Not Sent

> **Symptom**: "Customer didn't get OTP", "Rate notification not received"

## ⚡ Binary Search Integration
- **Start Layer**: 5 (API Response) — check if the SMS API was even called and what it returned
- **Elimination**: API not called → code path never reaches send function (check config/settings first). API called but failed → gateway issue (credits, URL, credentials). API returns success but not received → carrier/number issue.
- **Smell test**: "No OTP" → 35% SMS credits exhausted (`promotion_api_settings.debit_promotion = 0`). "Works for some users" → 10% mobile number format issue.

## Collect
1. What notification? (OTP, payment receipt, rate update, offer)
2. Customer mobile number?
3. Is it ALL customers or specific ones?
4. Check: does the customer have a valid device token? (for push notifications)

## Trace

### Step 1: Check gateway configuration
```sql
SELECT * FROM promotion_api_settings;
-- Check: debit_promotion > 0? (SMS credits exist?)
-- Check: sms_sender_id, sms_url populated?
```

### Step 2: Check notification settings
```sql
SELECT * FROM ret_settings WHERE key IN ('enable_otp', 'isOTPReqToLogin', 'isOTPRegForPayment');
-- Is the feature even enabled?
```

### Step 3: Trace the code path
For OTP specifically:
```
Controller → generates OTP → stores in session → calls SMS send function
Common failure: OTP generated but SMS send silently fails
```

Check the SMS send function:
- Is the gateway URL correct and reachable?
- Is `curl_exec()` returning false? (network error)
- Is the response being checked? (many implementations ignore curl response)

### Step 4: Known issues
- 8 DUPLICATED OTP implementations across modules (admin_manage, admin_payment, chit_admin, admin_app_api, etc.)
- Each may have different gateway config
- Some use OneSignal (push), some use SMS API — different credential sets

## Common Root Causes
1. **35%**: SMS credits exhausted (`debit_promotion = 0`)
2. **25%**: Gateway URL wrong or server unreachable
3. **20%**: Feature disabled in ret_settings
4. **10%**: Customer mobile format wrong (missing country code)
5. **10%**: OTP generated but curl_exec silently fails (no error handling)
