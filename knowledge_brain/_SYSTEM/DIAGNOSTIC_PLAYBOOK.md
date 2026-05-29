# Diagnostic Playbook — Cross-Module Troubleshooting Guide

> When a bug crosses module boundaries, use this playbook.
> Last updated: 2026-03-27
> Playbooks: 10

---

## Playbook 1: Tag Shows Wrong Status

**Symptoms**: Tag appears sold but bill doesn't exist, or tag shows available but was actually transferred.

```
Step 1: Query tag current state
  SELECT id_tagging, tag_status, current_branch, id_orderdetails, tag_process
  FROM ret_taging WHERE tag_code = '{TAG_CODE}';

Step 2: Query status history
  SELECT * FROM ret_taging_status_log
  WHERE id_tagging = {ID} ORDER BY id DESC LIMIT 20;

Step 3: Check which module wrote last
  - tag_status=1 → Check ret_billing for bill with this tag
  - tag_status=4 → Check ret_branch_transfer for active transfer
  - tag_status=7 → Check ret_stock_issue for active issue
  - tag_status=14 → Check ret_section_tag_status_log
  - tag_status=2/5 → Deleted — check if lot balance restored

Step 4: Cross-reference with SHARED_TABLES.md
  → 8 modules write tag_status — check each for recent writes
```

> **Root Cause Hint**: Most tag status bugs are from BT cancel (no reversal from status=4) or concurrent billing (no row lock).

**Symptoms**: Physical count != system count for non-tagged items.

## Playbook 2: Stock Balance Mismatch (Non-Tag)

**Symptoms**: Physical count != system count for non-tagged items.

```
Step 1: Get current system balance
  SELECT * FROM ret_nontag_item
  WHERE id_branch = {BRANCH} AND id_product = {PRODUCT}
    AND id_design = {DESIGN} AND id_section = {SECTION};

Step 2: Reconstruct from log
  SELECT * FROM ret_nontag_item_log
  WHERE id_nontag_item = {ID} ORDER BY id DESC LIMIT 50;

Step 3: Identify writers — check MODULE_DEPENDENCIES.md
  4 modules write this table:
  - Branch Transfer (transit/download)
  - Section Transfer (section move)
  - LOT (lot receipt stock)
  - Old Metal Process (melting/testing receipt)

Step 4: Check for cancelled BT that didn't reverse
  SELECT * FROM ret_branch_transfer
  WHERE status = 3 AND from_branch = {BRANCH}
  ORDER BY id DESC LIMIT 20;
  → If found, NT weight was NOT reversed (CLN-001)
```

---

## Playbook 3: Payment/Scheme Account Discrepancy

**Symptoms**: Payment recorded but account balance wrong, or payment showing cancelled but was actually processed.

```
Step 1: Query payment record
  SELECT * FROM payment WHERE id_payment = {ID};

Step 2: Check payment status history
  SELECT * FROM payment_status_log WHERE id_payment = {ID};

Step 3: Check which module wrote last
  - payment_status=4 via Chit Reports → No transaction wrapping (XMOD-001)
  - payment_status=4 via Payment → Has proper audit trail

Step 4: Verify scheme account linkage
  SELECT * FROM scheme_account WHERE id_scheme_account = {ACC_ID};
  → Verify active/is_closed state matches payment history

Step 5: Check for OTP bypass
  → XMOD-004: OTP always evaluates true. Any payment modification
  may have bypassed OTP check.
```

---

## Playbook 4: Estimation Total ≠ Bill Total

**Symptoms**: Bill total differs from estimation total for the same estimation.

```
Step 1: Get estimation total
  SELECT id_estimation, total_cost, grand_total
  FROM ret_estimation WHERE id_estimation = {ID};

Step 2: Get bill total
  SELECT id_billing, grand_total, total_tax
  FROM ret_billing WHERE id_billing = (
    SELECT estbillid FROM ret_estimation WHERE id_estimation = {ID}
  );

Step 3: If they differ:
  → VAL-002: Estimation total is JS-calculated, no server recalculation
  → Check if metal rate changed between estimation save and billing
  → Check if estimation items were edited after billing

Step 4: Print verification
  → FLOW_RISK_MATRIX EST: Print template recalculates VA/MC in PHP
  → PHP calculation may diverge from JS calculation
```

---

## Playbook 5: Cross-Module Permission Issue

**Symptoms**: Feature accessible for wrong profile, or blocked for correct profile.

```
Step 1: Check profile assignment
  SELECT * FROM profile WHERE id_profile = {PROFILE_ID};

Step 2: Check access table
  SELECT * FROM access WHERE id_profile = {PROFILE_ID}
  AND url LIKE '%{CONTROLLER}%';

Step 3: Verify admin_settings_model.get_access()
  → WARNING (XMOD-013): This method has SQL injection via raw parameter
  → Check if profile name contains special characters that break access check

Step 4: Check for dual-route risk
  → Some modules have both `/reports/` and `/admin_reports/` routes active
  → Permission may be set for one route but not the other
```

---

## Playbook 6: Dashboard Shows Wrong Numbers

**Symptoms**: Dashboard counts don't match reports.

```
Step 1: Identify which dashboard widget is wrong
  → Primary controller: admin_ret_dashboard.php
  → API controller: admin_ret_dashboard_api.php (AJAX)

Step 2: Check branch filtering
  → XMOD-017: get_accountstock_inwards_details() uses undefined $data
  → XMOD-018: get_rate_cut_profit_loss() date range inverted
  → get_CustomerDetails() has NO branch filter
  → get_branch_transfer_details() receives params but doesn't pass to model

Step 3: Check cross-model dependency
  → ret_reports_model.getLedgerReportData() — MAY NOT EXIST (Bug #5)
  → Fatal PHP error hidden behind AJAX — dashboard shows blank
```

---

## Playbook 7: Settings Change Has No Effect

**Symptoms**: Admin changed a setting but behavior didn't change.

```
Step 1: Verify setting was actually saved
  SELECT name, value FROM ret_settings WHERE name = '{SETTING_KEY}';

Step 2: If setting uses old key name:
  → ret_settings UPDATE uses WHERE name=? — if key was renamed, old value orphaned

Step 3: Check for dual config
  → chit_settings.is_branchwise_rate vs ret_settings — both control rates
  → Which one takes precedence depends on which model is loaded first

Step 4: Check if module reads fresh or cached
  → All modules read settingsDB() fresh per request (no cache)
  → But JS dropdowns may cache old values until page reload
```

Step 3: Check for dual config
  → chit_settings.is_branchwise_rate vs ret_settings — both control rates
  → Which one takes precedence depends on which model is loaded first

## Quick Reference: Which Module To Check

| Symptom | Check These Modules First |
|---|---|
| Wrong tag status | Tagging, Billing, Branch Transfer (see TAG_STATUS_MAP.md) |
| Stock imbalance | Branch Transfer, LOT, Old Metal, Section Transfer |
| Wrong bill total | Billing, Estimation (see HANDOFF_AUDIT HO-002) |
| Payment issues | Payment, Chit Reports, Account |
| Permission denied | Masters (RBAC), Settings |
| Dashboard wrong | Retail Dashboard (check XMOD-017, XMOD-018) |
| Report mismatch | Ret_Reports model, check branch/date filters |
| Report filter misses related records | Check subquery column aliases + AND/OR logic (see RULE-DX-011) |
| Edit screen dropdown empty/unselected | AJAX race condition — check setTimeout chains + master data load order (see PAT-JS-006) |
| SMS not sent | Check 8+ duplicated gateway implementations |
| Customer data issue | Customer module (see Playbook 8) |
| Payment financial mismatch | Payment module (see Playbook 9) |

---

## Playbook 8: Customer Security Issues

**Symptoms**: SQL injection detected, unauthorized file access, or password exposure.

```
Step 1: Identify attack vector
  → CUS-BUG-003: Searchcustomer() raw concat — search field SQL injection
  → CUS-BUG-002: download($id, $file) — path traversal / LFI
  → CUS-BUG-009: base64 passwords — trivially reversible

Step 2: Check for KYC file exposure
  → Verify KYC upload dirs are NOT world-accessible:
     curl -I "http://domain/assets/kyc/pan/{id}/pan_front.png"
  → CUS-BUG-011: dirs created with 0777

Step 3: Check for orphaned KYC data
  SELECT k.id_kyc, k.id_customer, k.kyc_type
  FROM kyc k
  LEFT JOIN customer c ON c.id_customer = k.id_customer
  WHERE c.id_customer IS NULL;
  → Orphaned KYC = PII exposure risk (CUS-BUG-006)

Step 4: Check agent/employee allocation
  → CUS-BUG-001/018: allocate functions always silently fail
  → Verify customer.id_agent and allocated_employee are actually set
```

---

## Playbook 9: Payment Financial Discrepancy

**Symptoms**: Payment amount doesn't match mode details, orphan records found, or receipt duplicated.

```
Step 1: Check payment vs mode details reconciliation
  SELECT p.id_payment, p.payment_amount,
         SUM(pmd.payment_amount) as mode_total,
         p.payment_amount - SUM(pmd.payment_amount) as diff
  FROM payment p
  JOIN payment_mode_details pmd ON p.id_payment = pmd.id_payment AND pmd.is_active = 1
  GROUP BY p.id_payment
  HAVING ABS(diff) > 0.01;
  → XMOD-024: No reconciliation check in SaveAll

Step 2: Check for orphan mode details
  SELECT pmd.* FROM payment_mode_details pmd
  LEFT JOIN payment p ON pmd.id_payment = p.id_payment
  WHERE p.id_payment IS NULL;
  → VAL-038: Delete has no child cleanup

Step 3: Check for duplicate receipts
  SELECT receipt_no, COUNT(*) as cnt
  FROM payment
  WHERE receipt_no IS NOT NULL
  GROUP BY receipt_no HAVING cnt > 1;
  → VAL-035: Receipt gen has no DB lock

Step 4: Check metal rate = 0 edge case
  SELECT * FROM payment
  WHERE metal_rate = 0 AND metal_weight > 0;
  → VAL-034: amount_to_weight() no zero-check

Step 5: Check PDC IFSC bug
  SELECT * FROM payment p
  WHERE p.id_post_payment IS NOT NULL
  AND p.bank_IFSC IS NULL;
  → VAL-037: Typo in array key
```

---

## Playbook 10: Table Column Misalignment in Dynamic Forms

**Symptoms**: Form columns appear shifted — data typed in visible inputs ends up under wrong headers. Save fails with "Required Fields" despite all visible fields being filled.

```
Step 1: Count header columns vs body columns
  Open browser DevTools → Elements tab
  Count <th> in <thead> vs <td> in the affected <tbody> row
  If td_count < th_count → missing column(s)

Step 2: Identify which row builder is responsible
  → Check if the form has multiple entry paths (e.g., PO vs Tag, With Bill vs Without Bill)
  → Search JS file for the table ID or form name to find all row-building code blocks
  → Compare <td> count in each builder

Step 3: Match columns left-to-right
  List header columns: [checkbox, date, karigar, category, section, product, purity, touch, pcs, weight, purewt, action]
  Map each <td> in the row to a header — identify the gap

Step 4: Check for hidden validation failures
  → Missing columns may contain required fields (e.g., UOM dropdown)
  → Search for validation functions: validateMetalIssueRow(), validateRow(), etc.
  → Check what fields they require — a missing <td> means the field is absent

Step 5: Check for auto-select defaults
  → Dropdowns built via JS loop may not match any value from data → stays at placeholder
  → If validation checks the dropdown value, it fails silently
```

> **Root Cause Pattern**: PAT-UI-002. Most common when a new column is added to the header but only one of multiple JS row builders is updated.

5. Upstream linked table not reset — cancel updates `customerorder` but linked `order_cart` still has `orderstatus=1` (ORD-CLT01)

**NEVER DO:**
- ❌ NEVER fix only the direct cancel table — ALWAYS check ALL linked upstream/downstream tables
