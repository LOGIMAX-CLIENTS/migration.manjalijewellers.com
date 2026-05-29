# Billing Module — Forensic Template

> **Round 1** | Layer-by-layer investigation cheat sheet

---

## Layer 1 — Symptom Collection

### Module-Specific Symptom Checklist

- [ ] Bill amount (front-end) ≠ DB amount
- [ ] Tax calculation wrong (CGST/SGST/IGST mismatch)
- [ ] Bill number generation failed or duplicated
- [ ] Tag not releasing after bill cancel
- [ ] Estimation not linking to bill (or wrong linkage)
- [ ] Payment total ≠ bill amount received
- [ ] Day closing date wrong on bill
- [ ] EDA/Normal toggle not working
- [ ] Credit bill OTP not sending
- [ ] Discount OTP flow breaking
- [ ] Bill split creating incorrect records
- [ ] Old metal exchange calculation wrong
- [ ] Advance adjustment not applying
- [ ] Chit utilization not deducting
- [ ] Gift voucher not validating
- [ ] E-invoice generation failing
- [ ] Print invoice showing wrong values
- [ ] Service bill save failing
- [ ] Payment mode edit not updating journal
- [ ] Bill cancel not reversing all records

---

## Layer 2 — Reproduce & Isolate

### Reproduction Checklist

1. What `bill_type` is active? (1/2/3/4/5/7/9/15)
2. Is it EDA or Normal? (`is_eda` = 1 or 2?)
3. Is it Individual or Company? (`billing_for` = 1 or 2?)
4. Is it Credit or Cash? (`is_credit` = 0 or 1?)
5. Which branch?
6. Is day closing done for today?
7. What metal type?
8. Is it a tagged item or non-tag?
9. Was estimation used or direct tag entry?
10. Were there any payment modes beyond cash?

### Isolation Questions

- Does it happen on ALL bill types or just one?
- Does it happen on ALL branches or just one?
- Does it happen on Normal AND EDA or just one?
- Is it a NEW bill or EDIT of existing?
- Does it happen with ALL tags or specific ones?

---

## Layer 3 — Client-Side Trace

### Key JS Variables to Inspect

```javascript
// In browser console:
console.log("is_eda:", $("#is_eda").val());
console.log("bill_type:", $(".bill_type_sales:checked").val());
console.log(
    "billing_for:",
    $("input[name='billing[billing_for]']:checked").val(),
);
console.log("is_credit:", $("input[name='billing[is_credit]']:checked").val());
console.log("branch:", $("#id_branch").val());
console.log("metal_type:", $("#metal_type").val());
console.log("tot_bill_amount:", $("#tot_bill_amount").val());
console.log("tot_amt_received:", tot_amt_received);
console.log("form_secret:", $("input[name='billing[form_secret]']").val());
```

### Network Tab Checks

- POST to `billing/save` — check request payload size and response
- AJAX to `getEstimationDetails` — check response data
- AJAX to `getBranchDayClosingData` — check entry_date
- AJAX to `sendotp` / `verify_otp` — check OTP response

### Key JS Functions to Trace

| Symptom          | Function to Trace                 | Location       |
| ---------------- | --------------------------------- | -------------- |
| Wrong item total | `calculateSaleBillRowTotal()`     | ret_billing.js |
| Wrong bill total | `calculateFinalCost()`            | ret_billing.js |
| Wrong discount   | `calculate_discount_amt()`        | ret_billing.js |
| Wrong tax        | Tax group selection + calculation | ret_billing.js |
| Payment mismatch | Payment mode add/remove handlers  | ret_billing.js |
| EDA issues       | Ctrl+Enter handler (L420-502)     | ret_billing.js |

---

## Layer 4 — Server-Side Trace

### Controller Trace Points

| Symptom            | File       | Method                    | Line   | What to Check                           |
| ------------------ | ---------- | ------------------------- | ------ | --------------------------------------- |
| Bill save fails    | controller | `billing()` → case 'save' | L218+  | form_secret match, trans_begin/complete |
| Bill number wrong  | controller | `billing()` → save        | L368   | `code_number_generator()` call          |
| Tag not updating   | controller | `billing()` → save        | L732   | `updateData('tag_status', 1)`           |
| Cancel not working | controller | `cancel_bill()`           | L7581  | Full reversal logic                     |
| Payment edit fails | controller | `paymentmode_edit()`      | L8306  | Journal reversal + re-creation          |
| OTP not sending    | controller | `sendotp()`               | L5534  | SMS API call                            |
| E-invoice fails    | controller | `generateEinvoice()`      | L10269 | Auth token + API call                   |
| Service bill fails | controller | `service_bill()`          | L9133  | Service bill save flow                  |
| Split save fails   | controller | `billing('split_save')`   | L303   | trans_begin, loop logic                 |
| Credit OTP fails   | controller | `send_credit_bill_otp()`  | L8982  | Profile OTP settings                    |

### Model Trace Points

| Symptom                 | Method                    | Line  | What to Check                |
| ----------------------- | ------------------------- | ----- | ---------------------------- |
| Wrong estimation data   | `getEstimationDetails()`  | L2745 | 1,694-line conditional logic |
| Wrong bill data on edit | `getBillingDetails()`     | L824  | JOIN conditions              |
| Payment total off       | `getPaymentDetails()`     | L1178 | SUM and filter               |
| Tag search issues       | `getTaggingBySearch()`    | L4975 | Complex query conditions     |
| Credit pending wrong    | `getCreditPending()`      | L9581 | 192-line DataTable query     |
| MC/VA limit wrong       | `get_mc_va_limit()`       | L8418 | 146-line limit calc          |
| Bill no duplicate       | `code_number_generator()` | L5221 | MAX query + increment        |

---

## Layer 5 — Database Verification

### Module-Specific Diagnostic SQL

```sql
-- D1: Bill integrity check (header vs details vs payments)
SELECT b.bill_id, b.bill_no, b.tot_bill_amount, b.tot_amt_received,
       COALESCE(SUM(bd.item_cost), 0) as items_total,
       COALESCE(SUM(bd.item_total_tax), 0) as tax_total,
       COALESCE((SELECT SUM(bp.payment_amount) FROM ret_billing_payment bp WHERE bp.bill_id = b.bill_id), 0) as payment_total
FROM ret_billing b
LEFT JOIN ret_bill_details bd ON b.bill_id = bd.bill_id
WHERE b.bill_id = {BILL_ID}
GROUP BY b.bill_id;

-- D2: Tag status consistency
SELECT t.tag_id, t.tag_status,
       bd.bill_id, b.is_cancelled
FROM ret_taging t
LEFT JOIN ret_bill_details bd ON t.tag_id = bd.tag_id
LEFT JOIN ret_billing b ON bd.bill_id = b.bill_id
WHERE t.tag_id = {TAG_ID};

-- D3: Estimation linkage check
SELECT ei.est_item_id, ei.purchase_status, ei.bil_detail_id,
       bd.bill_id, b.is_cancelled
FROM ret_estimation_items ei
LEFT JOIN ret_bill_details bd ON ei.bil_detail_id = bd.bill_det_id
LEFT JOIN ret_billing b ON bd.bill_id = b.bill_id
WHERE ei.est_item_id = {EST_ITEM_ID};

-- D4: Bill date vs day closing
SELECT b.bill_id, b.bill_date, dc.entry_date
FROM ret_billing b
JOIN day_closing dc ON b.id_branch = dc.id_branch
WHERE b.bill_id = {BILL_ID};

-- D5: Duplicate bill numbers
SELECT bill_no, id_branch, COUNT(*) as cnt
FROM ret_billing
WHERE is_cancelled = 0
GROUP BY bill_no, id_branch
HAVING cnt > 1;
```

---

## Layer 6 — Root Cause Classification

| Category                | Examples                                         | Risk                              |
| ----------------------- | ------------------------------------------------ | --------------------------------- |
| **Calculation Error**   | Wrong tax, wrong item total, wrong payment total | High — financial impact           |
| **State Management**    | Tag status not updated, estimation not linked    | High — data integrity             |
| **Race Condition**      | Duplicate bill numbers, same tag billed twice    | High — data corruption            |
| **Config Mismatch**     | Wrong bill_type behavior, wrong OTP flow         | Medium — wrong business flow      |
| **UI Sync Issue**       | JS calculation ≠ DB value                        | Medium — trust issue              |
| **Transaction Failure** | Partial save (some tables updated, others not)   | Critical — data corruption        |
| **API Failure**         | E-invoice API down, SMS not sending              | Low — graceful degradation needed |

---

## Layer 7 — Transaction Integrity (Financial Module)

### Transaction Wrapping Check

| Flow                       | `trans_start/trans_complete`? | Risk if Missing                       |
| -------------------------- | ----------------------------- | ------------------------------------- |
| `billing('save')` — normal | Need to verify                | Partial bill + orphan items           |
| `billing('split_save')`    | ✅ Yes (L333)                 | —                                     |
| `cancel_bill()`            | Need to verify                | Partial cancellation                  |
| `paymentmode_edit()`       | Need to verify                | Payment update without journal update |
| `issue('save')`            | Need to verify                | Payment without journal               |
| `receipt('save')`          | Need to verify                | Part receipt without journal          |

### Amount Calculation Verification

For any billing discrepancy:

1. **JS side**: Inspect `calculateSaleBillRowTotal()` output for each row
2. **POST payload**: Network tab — check `tot_bill_amount`, `tot_amt_received`
3. **Controller**: Check if POST values are used directly or recalculated
4. **DB**: Run D1 diagnostic query
5. **Compare**: JS total vs POST total vs DB total — find where divergence occurs

---

## Layer 7b — Variant Isolation (Config-Driven Bugs)

For any reported bug:

1. **First** identify which `bill_type` + `is_eda` + `billing_for` combination is active
2. **Test** the same operation with a different combination:
    - If bug in bill_type=3 (Combined), does it also happen in bill_type=1 (Normal)?
    - If bug in is_eda=2 (EDA), does it also happen in is_eda=1 (Normal)?
3. **Check** INVARIANT_MATRIX.md for known behavior differences
4. The variant combination determines which code paths execute
