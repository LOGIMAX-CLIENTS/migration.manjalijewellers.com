## BIL-CLT05 — Mismatch in Coin Quantity & Payment Mode Not Showing in Final Bill

| Field         | Value                              |
| ------------- | ---------------------------------- |
| Severity      | P1                                 |
| Track         | B (Business)                       |
| Category      | Logic                              |
| Sprint        | Sprint 1                           |
| Status        | ✅ FIXED (2026-03-26)              |
| Pattern Match | PAT-STATE-001 (NEW — Orphaned Status from Cancelled Operations) |
| Module Brain  | ✅ Ready                           |
| Reporter      | Client                             |
| Source        | Client                             |
| Root Cause    | Model `getEstimationDetails()` filters `purchase_status=0` — cancelled bills leave orphaned flags |
| Fix           | `resetOrphanedEstimationItems()` pre-check before loading estimation items |
| Files Changed | `ret_billing_model.php`, `admin_ret_billing.php` |
| GitHub        | [#1563](https://github.com/Logimax-Technologies/etail_development_src/issues/1563) ✅ Closed |

⛔ DANGER ZONE: Payment / financial data discrepancy
→ AI confidence: automatically LOW
→ Require senior developer review before applying any fix
→ Do NOT accept AI's first suggestion without verification

### Steps to Reproduce

1. Create an estimation for 15 coins.
2. During initial bill generation, verify the draft bill correctly shows 15 coins.
3. Enter all required billing details (customer, payment mode, etc.).
4. Generate the final bill (Save).
5. Open the saved/printed final bill.

### Expected Behavior

- Final bill should display **15 coins** (matching the estimation).
- Total amount should be calculated based on 15 coins.
- Payment mode (Cash / Card / UPI / etc.) should be visible in the final bill.

### Actual Behavior

- Final bill shows only **13 coins** (2 coins lost during finalization).
- Total amount is **reduced** accordingly — lower than what the draft showed.
- **Payment mode is not displayed** in the final bill output.

### Evidence

Client-reported. No screenshot provided yet — request from reporter pending.

### Cross-Module Impact

- **Shared tables at risk**: `ret_billing`, `ret_bill_details`, `ret_estimation`, `ret_estimation_items`, `ret_billing_payment`
- **Related XMOD**: XMOD-009 (Estimation→Billing partial commit risk)
- **Diagnostic Playbook**: RULE-DX-002 (Record saved but child records missing — suspect loop counter off-by-one or array index mismatch), RULE-DX-006 (Total/amount wrong — check JS vs PHP calculation layers)

### Investigation Hints

1. **Coin quantity drop (15 → 13)**: Check `getEstimationDetails()` model method (1,694 lines!) — does it correctly pass all estimation items to billing? Check the JS loop in `ret_billing.js` that processes estimation items into bill detail rows — off-by-one, skipped items, or filter condition excluding 2 items?
2. **Payment mode missing**: Check if `ret_billing_payment` is populated during save. Check the final bill view/print template — is it reading `payment_mode` from the correct source?
3. **Draft vs Final discrepancy**: The draft likely uses JS-rendered data; the final bill uses DB-stored data. The loss happens between JS form submission and PHP `billing('save')` processing.
