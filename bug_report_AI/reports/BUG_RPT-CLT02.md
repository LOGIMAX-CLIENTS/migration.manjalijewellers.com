## RPT-CLT02 — Incorrect Rate Pure Wt Value & Add Rejected Pure Wt Column in Supplier Purchase Payments Report

| Field | Value |
|---|---|
| Severity | P1 |
| Track | B (Business) |
| Category | Logic |
| Sprint | Sprint 2 |
| Pattern Match | None (Novel) |
| Module Brain | ✅ Ready |
| Reporter | Client |
| Source | Client |

### Description

Two issues in the **Supplier Purchase Payments Report** (`admin_ret_reports/popayments`):

1. **Incorrect Rate Pure Wt**: The `purewt` column is calculated as `payment_amount / goldrate_22ct` using the gold rate from `metal_rates` table joined via `po_date`. The user reports this value is incorrect/different from expected.

2. **Missing Rejected Pure Wt columns**: The report needs additional columns for **Rejected Gross Wt** and **Rejected Pure Wt** to help track supplier bills and verify balance pure payments.

### Steps to Reproduce
1. Navigate to Reports → Purchase Payments
2. Select a date range and/or supplier (karigar)
3. Click Search
4. Observe the **Rate** and **Pure Wt** columns — values are incorrect
5. Note the absence of **Rejected Gross Wt** and **Rejected Pure Wt** columns

### Expected Behavior
- **Rate Pure Wt** should show the correct computed value based on correct business formula
- Report should include **Rejected Gross Wt** and **Rejected Pure Wt** columns

### Actual Behavior
- **Rate** column shows `goldrate_22ct` from `metal_rates` joined on `po_date`
- **Pure Wt** column shows `payment_amount / goldrate_22ct`
- No rejected weight columns exist

### Evidence
Screenshot provided showing Rate = 12,000.00 and Pure Wt = 6.104 for a payment of 73,245.00

### Root Cause Analysis

**File**: `admin/application/models/ret_reports_model.php` (Line 20487-20509)

The SQL query in `get_po_payments()`:
```sql
IFNULL(rate.goldrate_22ct, 0) as rate, 
IFNULL(ROUND(IFNULL(po_bill.bill_amount, d.payment_amount) / NULLIF(rate.goldrate_22ct, 0), 3), 0) AS purewt
```
- Joins `metal_rates` on `po_date` — may match wrong rate or multiple rates
- Uses `goldrate_22ct` as the rate — business may need a different rate (e.g., purchase fixed rate)
- `purewt` is computed as `amount / goldrate_22ct` — this formula may need to use the PO's actual pure wt

### Affected Files

| File | Line | Purpose |
|---|---|---|
| `admin/application/models/ret_reports_model.php` | L20487-20509 | `get_po_payments()` SQL query |
| `admin/assets/js/ret_reports.js` | L23676-23822 | `get_po_payment_details()` DataTable rendering |
| `admin/application/views/ret_reports/popayments.php` | L97-140 | Report table structure (thead/tfoot) |
