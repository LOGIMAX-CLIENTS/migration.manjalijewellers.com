# Recipe: Cashfree Fractional Payload Mismatch

## Metadata
- **Pattern ID**: PAT-MOB-001
- **Severity**: P1 - Prevents Customer Payment
- **Modules Affected**: Mobile API (`mobile_api.php`) / Cashfree PG
- **Auto-fixable**: False (Requires App Developer Intervention)

## Client Scope
- Applies to: ALL (Any client using mobile app payments with fractional installments like MGG scheme)
- Reason: The mobile application has a structural bug where it truncates decimals of installments rather than properly aggregating floating-point inputs.

## Created By
- Developer: Agentic AI
- Client: erp.lakshmanaacharison.in (klson)
- Date: 2026-04-04
- Source Bug ID: ADHOC-MGG-CASHFREE

## Symptom
Customer attempts to pay via Cashfree payment gateway in the mobile app. The app rejects the action and shows the error "Invalid payment request...". This frequently happens for schemes with fractional installments (e.g., MGG where installment is `6827.5`).

## Root Cause
The mobile app calculates the top-level `amount` payload field by erroneously applying a `floor()` or rigorous decimal truncation (e.g., treating `6827.5` + `6827.5` as `6827` + `6827` = `13654`). 
However, the JSON `pay_arr` containing individual scheme accounts still transmits the original fractional values (e.g., `6827.5`). 
When the backend `mobile_payment_post()` endpoint validates that `$amt == $sum_of_payArr_pay` (`13654 == 13655`), the transaction mathematical comparison fails to prevent accounting discrepancies.

## Detection
Search for strict amount validation blocks in mobile API controllers handling Cashfree gateways:
```powershell
grep -rn "== \$sum_of_payArr_pay" application/controllers/mobile_api.php
```
If the backend throws "Invalid payment request", log the incoming payloads and check if the `amount` fields differ from the sum of `pay_arr.pay_amt`.

## Files
- **Backend File**: `application/controllers/mobile_api.php`
- **Mobile App Check-Out Loop**: *(Internal Frontend Code handling the Cart/Payments)*

## Fix
**DO NOT disable or bypass the backend validation.** Doing so will cause an accounting mismatch between actual collected revenue and the database.

**Fix requires Mobile App logic calculation change:**
### Before (Conceptual Mobile App logic)
```javascript
let totalAmount = 0;
for(let item of pay_arr) {
    totalAmount += parseInt(item.pay_amt); // or Math.floor causes precision loss
}
// totalAmount = 13654;
```
### After (Conceptual Mobile App fix)
```javascript
let totalAmount = 0.0;
for(let item of pay_arr) {
    totalAmount += parseFloat(item.pay_amt); 
}
payload.amount = parseFloat(totalAmount.toFixed(2)); // Ensures precisely 13655.0 is sent
```

## Verification
- Run a payload test with two accounts having fractional payments (e.g. 6827.5).
- Ensure the master `amount` field in the JSON POST exactly matches the calculated summation of the `pay_arr` block.

## Notes
Never adjust the backend to blindly accept a lesser aggregate amount as it directly drops income during day-end reconciliation. Always enforce strict validation and push UI changes immediately to resolve.
