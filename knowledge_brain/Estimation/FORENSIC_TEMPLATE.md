# Estimation Module — Forensic Investigation Template

> **Module**: Estimation
> **Date Built**: 2026-03-24 (Round 1)
> **Purpose**: Layer-by-layer bug investigation cheat sheet — follow layers in order for any Estimation bug

---

## Layer 1 — Symptom Collection

Before touching any code, classify the symptom:

| Symptom | Likely Area | Go To Layer |
|---|---|---|
| Wrong total shown on form | JS calculation | Layer 3 (Client-Side) |
| Wrong total on printed PDF | PHP print template | Layer 4 → 3b-4 (Reversal) |
| Estimation saves but shows error | Transaction / rollback bug | Layer 4 → EST-R601 |
| Estimation saves silently with wrong data | Server-side validation gap | Layer 4 |
| Tag not found / wrong tag added | Tag search / tagging module | Layer 3 → tagging AJAX |
| Items lost after edit | DELETE-then-INSERT failure | Layer 4 + Layer 5 |
| Customer data saved incorrectly | `createNewCustomer`/`updateCustomer` | Layer 4 |
| EDA discount bypass | State machine gap | FLOW_RISK_MATRIX section 3b-1 |
| Old metal rate wrong | Rate lookup | Layer 3 + Layer 5 |
| Chit balance wrong | Scheme calculation + variant | INVARIANT_MATRIX Dimension 3 |
| Stones missing / wrong weight | Stone sub-table orphan | Layer 5 |
| Cannot save on specific date | Day closing gate | Layer 4: `getBranchDayClosingData()` |

**Checklist — collect before investigating:**
- [ ] Which estimation ID? Can it be reproduced?
- [ ] Add new / Edit existing / Save / Print?
- [ ] Which item types involved? (Tag / Catalog / Custom / Old Metal / Chit)
- [ ] Which branch / employee?
- [ ] Settings active: `calculation_based_on=?`, `wastage_rate_type=?`, `is_eda=?`
- [ ] Was this a concurrent access scenario?

---

## Layer 2 — Reproduce & Isolate

1. **Reproduce** the bug on a test estimation (never touch production data directly)
2. **Isolate the variant**: Check which invariant config was active (see INVARIANT_MATRIX.md)
   - Run: `SELECT * FROM ret_settings WHERE setting_name IN ('calculation_based_on', 'wastage_rate_type', 'wastage_rate_type', 'chit_rate_cal_type')`
   - Run: `SELECT * FROM emp_setting WHERE id_employee = {emp_id}`
3. **Isolate the item type**: Does bug affect ALL item types or just one? (Tag=0, Catalog=1, Custom=2)
4. **Isolate the flow**: Does bug occur on CREATE only, EDIT only, or both?
5. **Check DB state** immediately after the bug occurs — run queries from Layer 5 before anything changes

**Isolation questions:**
- Does the bug happen with a fresh estimation (no edit history)?
- Does it reproduce with a single item (no multi-item complexity)?
- Does it reproduce without old metal / chit / gift voucher sections?
- Does it reproduce as a super-admin (ruling out permission/settings variance)?

---

## Layer 3 — Client-Side Trace

> **Tools**: Browser DevTools → Console, Network, Sources tabs

### Key Variables to Inspect in Console

```javascript
// After form load:
$('#calculation_based_on').val()     // Should be 0, 1, or 2
$('#wastage_rate_type').val()         // Should be 0 or 1
$('#allow_manual_rate').val()         // 0 or 1
$('#allow_mc_edit').val()             // 0 or 1
$('#allow_va_edit').val()             // 0 or 1

// During calculation:
// Add breakpoint at calculateSaleValue() → watch: nwt, gwt, rate, wastage_per, mc_per
// Add breakpoint at calculate_total_mc_va() → watch: mc_type, va_type, mc_val, va_val
```

### Key Console Log Points

| Issue | Where to Break | What to Check |
|---|---|---|
| Wrong VA/MC | `calculateSaleValue()` L8854 | `gwt`, `nwt`, `rate`, `wastage_per`, mode (`calculation_based_on`) |
| Tag not loading | `get_tag_data()` L1393 | AJAX response, `tag_status` in response |
| Tag scan fails | `get_tag_barcode_data()` L2265 | Network tab → POST to `/getTaggingScanBySearch` |
| Old metal wrong | `calculateOldMatelItemSaleValue()` L9706 | `gross_wt`, `touch`, `rate_per_gram`, `amount` |
| Chit wrong | `calculate_chit_closing_balance()` | `scheme_type`, `closing_weight`, `gold_rate` |
| Validation fails silently | `validateTagDetailRow()` L7967 | Check `is_valid` flag, which field returns false |
| VA slab wrong | `wastage_slab_value()` L29994 | `slab_config`, `metal_type`, `nwt` range |

### Key Network Tab Checks

| AJAX Call | URL | What to Verify |
|---|---|---|
| Tag search | `/admin_ret_estimation/getTaggingBySearch` | Response has `tag_status=0`, correct `gwt`/`nwt` |
| Metal rate | `/admin_ret_estimation/get_metal_purity_rate` | Returns positive rate for selected purity |
| Old metal rate | `/admin_ret_estimation/get_all_old_metal_rates` | All metal types have rates |
| Save estimation | `/admin_ret_estimation/estimation/save` | Response status, error message, returned `estimation_id` |
| Save response | Same | Check `trans_status` implied by success/error response |

---

## Layer 4 — Server-Side Trace

> **File**: `admin/application/controllers/admin_ret_estimation.php`
> **Key method**: `estimation()` L190-2474 (2,284-line monolith)

### Controller Trace Points Table

| Symptom | Controller Method | Lines | What to Check |
|---|---|---|---|
| Save fails silently | `estimation('save')` | L190+ | Check `trans_begin()` / `trans_commit()` / `trans_status()` |
| Transaction not rolling back | `estimation('save')` | ~L88 of save branch | **EST-R601**: `trans_commit()` called in BOTH success AND error branches |
| Tag items lost after edit | `estimation('save')` edit branch | Delete section | `deleteData('esti_id', $est_id, 'ret_estimation_items')` called before re-insert |
| `ret_est_other_metals` orphans | `estimation('save')` edit branch | — | Verify `deleteData('est_item_id', ..., 'ret_est_other_metals')` exists |
| Bill number wrong | `estimation('save')` | L~300 | `generateEstiNo()` → `get_bill_no_format_detail()` (142 lines) |
| Customer created with wrong data | `createNewCustomer()` | L2478-2555 | Raw `$_POST` vs `$this->input->post()` (EST-R605) |
| Day closing not blocked | `estimation('save')` | Check `getBranchDayClosingData()` call | Verify controller rejects save when `is_day_closed=1` |
| Print total wrong | `generate_invoice()` | L2939-2996 | Template selection logic, `getOtherEstimateItemsDetails()` data |

### Model Trace Points

| Symptom | Model Method | Lines | What to Check |
|---|---|---|---|
| Estimation not found | `get_entry_records()` | L403-429 | JOINs: `customer`, `village`, `address`, `employee`, `branch`, `city` |
| Stone totals wrong | `get_est_tag_details()` | L2045-2265 | 220-line method joining 10+ tables — check stone JOINs |
| Estimation list wrong total | `ajax_getEstimationList()` | L169-235 | Complex 15-table join — check SUM/GROUP BY correctness |
| Chit wrong on edit load | `get_chit_details()` | L2021-2044 | Joins `scheme_account`, `scheme` — check `utl_amount` vs `closing_weight` |
| Old metal wrong on edit | `old_metal()` | L2522-2579 | Check `purpose` field (1=Cash, 2=Exchange) |
| Credit balance wrong | `get_credit_pending_details()` | L2709-2777 | 6-method chain — add logging between each call |
| Child tag stone null | `get_child_tag_stone_details()` | L2637-2645 | **⚠️ BUG: no return statement** — always returns NULL |
| Estimate number duplicate | `generateEstiNo()` | L1456-1472 | Race condition — no locking; check concurrent saves |

---

## Layer 5 — Database Verification

Run these queries immediately after a bug is triggered (before any retry):

```sql
-- 1. Complete estimation state (header + all child records)
SELECT e.estimation_id, e.esti_no, e.total_cost, e.is_eda,
       ei.est_item_id, ei.tag_id, ei.item_type, ei.gross_wt, ei.net_wt, ei.item_cost,
       eis.est_item_stone_id, eis.stone_id, eis.wt AS stone_wt, eis.price AS stone_price,
       eom.old_metal_sale_id, eom.gross_wt AS om_gwt, eom.amount AS om_amount
FROM ret_estimation e
LEFT JOIN ret_estimation_items ei ON e.estimation_id = ei.esti_id
LEFT JOIN ret_estimation_item_stones eis ON ei.est_item_id = eis.est_item_id
LEFT JOIN ret_estimation_old_metal_sale_details eom ON e.estimation_id = eom.est_id
WHERE e.estimation_id = {ID};

-- 2. Total integrity check (header vs sum of items)
SELECT e.estimation_id, e.esti_no, e.total_cost AS header_total,
       SUM(ei.item_cost) AS sum_items,
       ABS(e.total_cost - SUM(ei.item_cost)) AS discrepancy
FROM ret_estimation e
JOIN ret_estimation_items ei ON e.estimation_id = ei.esti_id
WHERE e.estimation_id = {ID}
GROUP BY e.estimation_id
HAVING ABS(e.total_cost - SUM(ei.item_cost)) > 0.01;

-- 3. Orphan items (items without parent estimation)
SELECT ei.est_item_id, ei.esti_id
FROM ret_estimation_items ei
LEFT JOIN ret_estimation e ON ei.esti_id = e.estimation_id
WHERE e.estimation_id IS NULL;

-- 4. Orphan stones (stones without parent item)
SELECT eis.est_item_stone_id, eis.est_item_id
FROM ret_estimation_item_stones eis
LEFT JOIN ret_estimation_items ei ON eis.est_item_id = ei.est_item_id
WHERE ei.est_item_id IS NULL;

-- 5. MyISAM orphan check (ret_est_other_metals — not covered by transactions)
SELECT eom.est_other_itm_id, eom.est_item_id
FROM ret_est_other_metals eom
LEFT JOIN ret_estimation_items ei ON eom.est_item_id = ei.est_item_id
WHERE ei.est_item_id IS NULL;

-- 6. Chit utilization orphan check
SELECT cu.chit_ut_id, cu.est_id
FROM ret_est_chit_utilization cu
LEFT JOIN ret_estimation e ON cu.est_id = e.estimation_id
WHERE e.estimation_id IS NULL;

-- 7. Tag status at time of bug (check if tag was sold/reserved)
SELECT tag_id, tag_code, tag_status, reserve_status
FROM ret_taging
WHERE tag_id = {TAG_ID};

-- 8. Day closing status for branch
SELECT id_branch, is_day_closed, entry_date
FROM ret_day_closing
WHERE id_branch = {BRANCH_ID}
ORDER BY entry_date DESC LIMIT 1;
```

---

## Layer 6 — Root Cause Classification

| Category | Examples | Required Fix Location |
|---|---|---|
| **A: Transaction / Commit Bug** | EST-R601 — `trans_commit` in error branch | Controller save method — swap `trans_commit` → `trans_rollback` in error branch |
| **B: Validation Gap — Server** | Tag status not re-checked server-side; SR amount not validated | Controller or model — add server-side guard before DB insert |
| **C: Calculation Divergence** | PHP print template uses different formula than JS | Both JS calc function AND PHP print template must be updated together |
| **D: Missing Cleanup on Edit** | `ret_est_other_metals` or `ret_est_tag_merge` not deleted on re-edit | Controller save/edit branch — add `deleteData()` call |
| **E: Client-Side Validation Only** | Rate limits, discount limits, employee permission checks | Add identical server-side validation in controller before any DB write |
| **F: Race Condition** | Concurrent save with same estimation ID; voucher double-redemption | Add row-level locking or optimistic concurrency check |
| **G: Config-Driven Silent Change** | `wastage_rate_type` changed mid-deployment | Document in INVARIANT_MATRIX; alert team before any setting change |
| **H: Copy-Paste Function Divergence** | `get_tag_data()` vs `get_tag_barcode_data()` subtle differences | Fix in both functions simultaneously; document divergence |
| **I: Model Bug** | `get_child_tag_stone_details()` missing return statement | Model fix only; ensure caller handles NULL gracefully |

---

## Layer 7 — Transaction Integrity (Financial Module Specialized Layer)

> Use this layer when the bug involves DB state inconsistency after a save

### Transaction Wrapping Check
1. Locate `$this->db->trans_begin()` in `estimation('save')` 
2. Confirm ALL insert/update/delete calls are INSIDE the transaction block
3. Check `$this->db->trans_status()` is called before commit/rollback
4. **Verify** `trans_rollback()` is called in ALL error branches — EST-R601 confirmed `trans_commit()` is incorrectly called in error paths

### Partial Commit Detection
```sql
-- Check if estimation header exists WITHOUT items (partial save)
SELECT e.estimation_id, e.esti_no, e.total_cost, COUNT(ei.est_item_id) AS item_count
FROM ret_estimation e
LEFT JOIN ret_estimation_items ei ON e.estimation_id = ei.esti_id
WHERE e.estimation_id = {ID}
GROUP BY e.estimation_id
HAVING item_count = 0;
```

### MyISAM Risk Reminder
> `ret_est_other_metals` and `ret_tag_other_metals` are **MyISAM** tables (confirmed in SCHEMA_ANALYSIS.md).
> MyISAM does NOT support transactions. Even if `trans_rollback()` is called, writes to these tables are permanent.
> Any bug involving other metals on tags requires manual cleanup if the transaction fails.

---

## Layer 7b — Print Template Trace (Specialized Layer)

> Use this layer when printed PDF total differs from form total

1. Open `est_print.php` (L1-623) — find the VA/MC calculation block
2. Verify `$calculation_based_on` is read from DB (not hardcoded)
3. Compare the PHP formula against JS `calculateSaleValue()`:
   - Both should use the same `calculation_based_on` value
   - Both should use the same `wastage_rate_type` value
4. Check IGST/CGST split logic:
   - `est_print_2.php` → compare `company->id_state` vs `customer->id_state`
   - If `is_eda=1` → tax lines should be HIDDEN
5. Verify `getOtherEstimateItemsDetails()` returns correct data (run DB query 1 from Layer 5)
6. If bug is in ONE branch-specific template only — check ALL 9 templates for the same bug (print template duplication risk)
