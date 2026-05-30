# Recipe: History vs. Master Profile Fetch (Missing KYC/PAN Data)

> A common anti-pattern where a profile detail fetch (e.g., PAN, Aadhar) is driven by a transaction table (e.g., `ret_billing`) instead of the master table (e.g., `customer`). This causes the fetch to fail for new customers who have no transaction history.

## Metadata
- **Pattern ID**: PAT-LOG-002
- **Severity**: MEDIUM/HIGH (Depends on legal/financial impact of missing KYC)
- **Modules Affected**: Billing, CRM, Loyalty, Estimation
- **Auto-fixable**: Yes (by refactoring the SQL `FROM` table and `JOIN` directions)

## Created By
- **Developer**: Antigravity AI
- **Client**: Konika
- **Date**: 2026-04-02
- **Source Bug ID**: BIL-CLT001

## Symptom
- KYC details (PAN, Aadhar, etc.) are **not** populated in the form when a customer is selected.
- The user reports: "It only works for customers who have been billed before."
- Searching for a customer with a profile in the Master record returns empty fields in the UI.

## Root Cause
The backend model query fetching the customer details is structured to use the transaction table as the primary source:

```sql
SELECT ... FROM ret_billing bill
LEFT JOIN customer c ON (c.id_customer=bill.bill_cus_id)
WHERE c.id_customer = ?
```

If the customer has no entries in `ret_billing`, the query returns **zero rows**, even if the customer exists in the `customer` table.

## Detection

### Find queries driven by transaction tables
```command
grep -r "from ret_billing bill.*join customer c" admin/application/models/
```

### Find missing fields in autocomplete responses
Check if the AJAX handler for customer selection (`getAvailableCustomers`) is selecting the required master fields.

## Fix

### 1. Refactor SQL (Model)
Change the base table to the master table (`customer`) and `LEFT JOIN` the transaction data:

```diff
-FROM ret_billing bill
-LEFT JOIN customer c ON (c.id_customer=bill.bill_cus_id)
+FROM customer c
+LEFT JOIN ret_billing bill ON (c.id_customer=bill.bill_cus_id)
```

### 2. Include Fields in Search (Model)
Ensure the `getAvailableCustomers` (or similar search method) includes the target profile fields in its `SELECT` list.

### 3. Update Population (JS)
In the autocomplete `select` callback, explicitly set the form values from the response item:

```javascript
select: function (e, i) {
    $("#pan_no").val(i.item.pan_no);
    $("#aadhar_no").val(i.item.aadhar_no);
    // ...
}
```

## Verification
1. Select a **new** customer (one with no billing history).
2. Confirm the PAN/KYC fields are populated.
3. Select an **existing** customer (one with history).
4. Confirm the PAN/KYC fields are still populated (no regressions).

## Notes
- **Cross-module impact**: Ensure that changing the `FROM` table doesn't break aggregate calculations like `last_bill_date` (use `max(bill.bill_date)` with the `LEFT JOIN`).
- **Client-specific JS**: Always check if the client has an overridden version of the JS file (e.g., `clients/{client_name}/assets/js/ret_billing.js`) and apply the fix there as well.
