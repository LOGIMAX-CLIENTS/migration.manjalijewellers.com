# Recipe: Supplier Payment total_entries Overwrite Causes Missing Bill Detail Records

## Metadata
- **Pattern ID**: PAT-PAY-001
- **Severity**: HIGH
- **Modules Affected**: Purchase (supplier_po_payment)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Core JS logic in ret_purchase_order.js used by all clients

## Created By
- **Developer**: AI (Antigravity)
- **Client**: AMS-RetailAdmin
- **Date**: 2026-06-25
- **Source Bug ID**: N/A

## Symptom
Supplier payment screen shows full bill amount instead of balance (bill amount minus previously paid). The payment header record exists in ret_po_payment but the bill-level detail in ret_po_bill_payment_details is missing.

## Root Cause
In calculateSelectedTotal(), total_entries is overwritten with the count of checked checkboxes instead of keeping the last row index. PHP save loop uses total_entries as upper bound so later-indexed bills are never reached.

## Detection
grep -n total_entries assets/js/ret_purchase_order.js

## Files
- assets/js/ret_purchase_order.js - calculateSelectedTotal() function

## Fix
Remove the line that overwrites total_entries with checkbox count. Replace with a comment explaining why it must not be overwritten.

## Verification
1. Create 3+ PO bills for a supplier
2. Open supplier payment, check ONLY the last bill
3. Pay a partial amount
4. Save and reopen - verify balance deducted correctly

## Notes
- PHP save loop already handles unchecked rows via bill_checked guard
- Existing payments before fix may have missing ret_po_bill_payment_details records
