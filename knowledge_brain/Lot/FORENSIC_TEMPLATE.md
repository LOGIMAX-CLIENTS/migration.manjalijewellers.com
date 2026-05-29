# LOT MODULE — FORENSIC TEMPLATE
> Module: Lot | Rounds 1–11 | 2026-03-17
> **Use this during bug investigation. Follow layers in order.**

---

## Layer 1 — Symptom Collection

**What could go wrong in Lot?**

- [ ] Lot saved but items/stones not appearing in list/detail
- [ ] Wrong lot_date (off by 1 day — day close issue)
- [ ] NonTag stock count wrong after lot save
- [ ] Merge created but source lots still editable
- [ ] Split doesn't reduce original lot balance
- [ ] Image upload failing (lot images / certificates)
- [ ] Delete lot leaves orphan detail/stone records in DB
- [ ] Lot close (lot_completed) not closing lot (trailing space bug)
- [ ] Lot cancel succeeds but status not 2 in DB
- [ ] Stone details missing after edit (re-insert logic misfire)
- [ ] Other charges not updating on edit
- [ ] Edit blocked even though lot is manual (lot_from check)
- [ ] Print PDF blank or missing data
- [ ] Customer ack shows wrong data (different query vs office ack)

---

## Layer 2 — Reproduce & Isolate

**Questions to ask on every Lot bug:**

1. What is `lot_no`? (check DB directly)
2. What is `stock_type`? (1=Tagged, 2=NonTag — different code paths)
3. What is `lot_from`? (1=manual = only editable one)
4. What is `is_closed`? (1 = blocked from edit)
5. Is this Merge or Split related? (different controller methods)
6. Did the bug occur at: add / edit / delete / merge / split / print?
7. Was there a pending day close at the time?
8. Was there a certificate image involved?

**Isolation steps:**
- Check `ret_lot_inwards` WHERE `lot_no = X` — verify all header fields
- Check `ret_lot_inwards_detail` WHERE `lot_no = X` — verify item count
- Check `ret_lot_inwards_stone_detail` WHERE `id_lot_inward_detail IN (...)` — verify stones
- Check `log_model` table WHERE `module = 'Lot' AND record = X` — verify logged operations

---

## Layer 3 — Client-Side Trace

**Console log points in ret_lot.js:**
- L267: `console.log(lot_preview_item)` — triggered on edit page load
- Use browser DevTools → Network tab → look for POST to `lot_inward` (list load)
- Look for POST to `lot_inwards_detail` (line item delete)

**Key variables to inspect:**
- `ctrl_page` — determines which page-specific code runs (`ctrl_page[1]` = route segment)
- `lot_preview_item` — data loaded from PHP for edit mode
- `uom_details`, `lot_cat_details`, `section_details` — referenced throughout

**Form data to check before submit:**
- `inward[stock_type]` — 1 or 2?
- `inward_item[]` — array of line items (JSON strings for stones/metals/charges)
- `inward_item[n][stones_details]` — JSON encoded stone array
- `inward_item[n][other_metal_details]` — JSON encoded metal array
- `inward_item[n][other_charges_details]` — JSON encoded charges array

**Network tab checks:**
- `POST /admin_ret_lot/lot_inward` — list refresh
- `POST /admin_ret_lot/lot_inwards_detail` — item delete
- `POST /admin_ret_lot/lot_completed` — lot close
- `GET /admin_ret_lot/lot_inward/delete/{id}` — lot delete (note: GET!)

---

## Layer 4 — Server-Side Trace

| Symptom | File | Method | Line | What to Check |
|---|---|---|---|---|
| Lot not saving | admin_ret_lot.php | `lot_inward('save')` | L386–1047 | `trans_status()` at L999; check `_error_message()` |
| Stone not saving | admin_ret_lot.php | `lot_inward('save')` | L749 | `insertData($stones, 'ret_lot_inwards_stone_detail')` — check trailing space columns |
| NonTag stock wrong | admin_ret_lot.php | `lot_inward('save')` | L851–991 | `checkNonTagItemExist` result; `updateNTData '+' or insertData` |
| Lot not closing | admin_ret_lot.php | `lot_completed()` | L2491 | **BUG: column name `'lot_no '` has trailing space** |
| Lot delete orphans | admin_ret_lot.php | `lot_inward('delete')` | L1199 | Only deletes header — check child tables |
| Edit save not updating charges | admin_ret_lot.php | `lot_inward('update')` | L1360–1595 | Other charges/metals NOT cleaned before update |
| Cancel not working | admin_ret_lot.php | `lot_inward('cancel_lot_entry')` | L1063 | Check trans_status, check AJAX JSON response |
| Merge save crashing | admin_ret_lot.php | `lot_merge('save')` | L2270 | `echo last_query()` before `trans_rollback()` |
| Wrong lot_date | admin_ret_lot.php | `lot_inward('save')` | L344–346 | `getBranchDayClosingData` return value |
| Image upload 404 | admin_ret_lot.php | `upload_lotimg` | — | **Method MISSING from controller** |
| Print blank | admin_ret_lot.php | `lot_acknowladgement()` | L1807 | `lotInward_detail()`, `get_lot_details()` return values |
| Customer ack wrong | admin_ret_lot.php | `customer_acknowladgement()` | L2528 | Uses different model methods vs office ack |

---

## Layer 5 — Database Verification

```sql
-- Verify lot header + all children
SELECT i.lot_no, i.lot_date, i.lot_from, i.stock_type, i.lot_status, i.is_closed,
       COUNT(d.id_lot_inward_detail) as item_count,
       SUM(d.no_of_piece) as tot_pcs, SUM(d.gross_wt) as tot_gwt
FROM ret_lot_inwards i
LEFT JOIN ret_lot_inwards_detail d ON d.lot_no = i.lot_no
WHERE i.lot_no = {LOT_NO}
GROUP BY i.lot_no;

-- Check stone details for a lot
SELECT d.id_lot_inward_detail, stn.stone_id, stn.stone_pcs, stn.stone_wt, stn.price
FROM ret_lot_inwards_detail d
LEFT JOIN ret_lot_inwards_stone_detail stn ON stn.id_lot_inward_detail = d.id_lot_inward_detail
WHERE d.lot_no = {LOT_NO};

-- Check orphan detail rows (header deleted)
SELECT d.lot_no, COUNT(*) as orphan_detail_count
FROM ret_lot_inwards_detail d
LEFT JOIN ret_lot_inwards i ON i.lot_no = d.lot_no
WHERE i.lot_no IS NULL
GROUP BY d.lot_no;

-- Check non-tag stock for a product/branch
SELECT ni.id_nontag_item, ni.product, ni.no_of_piece, ni.gross_wt, ni.net_wt, ni.branch
FROM ret_nontag_item ni
WHERE ni.product = {PRODUCT_ID} AND ni.branch = {BRANCH_ID};

-- Check lot_completed execution (is_closed should be 1)
SELECT lot_no, is_closed, closed_by, closed_on FROM ret_lot_inwards
WHERE lot_no IN ({LOT_NO_LIST});

-- Check lot audit log
SELECT * FROM {log_table} WHERE module = 'Lot' AND record = {LOT_NO} ORDER BY event_date DESC;

-- Check merge records
SELECT lm.lot_no as merged_into, lm.id_lot_inward_detail, ld.lot_no as source_lot
FROM ret_lot_merge lm
LEFT JOIN ret_lot_inwards_detail ld ON ld.id_lot_inward_detail = lm.id_lot_inward_detail
WHERE lm.lot_no = {NEW_LOT_NO};
```

---

## Layer 6 — Root Cause Classification

| Category | Description | Risk Level | Action |
|---|---|---|---|
| Data Integrity | Orphan child records after delete | 🔴 HIGH | Fix delete to cascade child tables |
| Security | GET delete = CSRF vulnerability | 🔴 HIGH | Convert to POST + CSRF token |
| Security | Raw `$_POST` in SQL | 🔴 HIGH | Use parameterized queries |
| Data Correctness | Trailing space in column names | 🔴 HIGH | Fix insert arrays |
| Logic Bug | `lot_completed` trailing space WHERE | 🔴 HIGH | Fix `'lot_no '` → `'lot_no'` |
| Data Loss | Other charges not updated on lot edit | 🟠 MEDIUM | Add DELETE + re-INSERT for charges |
| Data Loss | NonTag stock inflated if lot deleted | 🟠 MEDIUM | Add decrement on lot delete |
| Debug Leak | `echo last_query()` in production | 🟠 MEDIUM | Remove debug echoes |
| Logic Bug | Double redirect after save | 🟡 LOW | Remove dead redirect |
| Performance | N+1 in `ajax_getLotList` (lot_det+tag+branch per row) | 🟠 MEDIUM | Consider batch fetch |

---

## Layer 7 — Stock Integrity Check

*(Added because module writes to ret_nontag_item — stock integrity is critical)*

**Verify NonTag stock arithmetic:**
```sql
-- Compare ret_nontag_item quantity vs sum of lot line items by product+branch
SELECT 
    ni.id_nontag_item,
    ni.product,
    ni.branch,
    ni.no_of_piece as nt_stock,
    IFNULL(lot_sum.tot_pcs, 0) as lot_total_pcs,
    (ni.no_of_piece - IFNULL(lot_sum.tot_pcs, 0)) as discrepancy
FROM ret_nontag_item ni
LEFT JOIN (
    SELECT ld.lot_product, ld.current_branch, SUM(ld.no_of_piece) as tot_pcs
    FROM ret_lot_inwards l
    JOIN ret_lot_inwards_detail ld ON ld.lot_no = l.lot_no
    WHERE l.stock_type = 2 AND l.lot_status != 2
    GROUP BY ld.lot_product, ld.current_branch
) lot_sum ON lot_sum.lot_product = ni.product AND lot_sum.current_branch = ni.branch
WHERE ni.product = {PRODUCT_ID};
```

**Check for lot items that created NonTag entries but lot was later deleted:**
```sql
SELECT log.*
FROM ret_nontag_item_log log
LEFT JOIN ret_lot_inwards lot ON lot.lot_received_at = log.to_branch
WHERE log.status = 0
ORDER BY log.created_on DESC;
```

---

## Layer 8 — Tagging Reverse Dependency Check

*(Added Round 10 — because ret_tag_model.php has 324+ queries into Lot tables)*

**When to use this layer**: When tag balances are wrong, tag list shows phantom lots, or tags can be created against lots that should be ineligible.

### 8a. Check for Orphaned Lot Detail Rows (Root Cause of Phantom Tags)
```sql
-- Lot header deleted but detail rows remain → phantom stock in tag module
SELECT d.lot_no, COUNT(*) as orphan_rows, SUM(d.no_of_piece) as phantom_pcs
FROM ret_lot_inwards_detail d
LEFT JOIN ret_lot_inwards i ON i.lot_no = d.lot_no
WHERE i.lot_no IS NULL
GROUP BY d.lot_no;
```

### 8b. Verify Lot Closure Propagated to Tag Eligibility
```sql
-- Check lots that should be closed (is_closed=1) but got skipped (R-LOT-023 trailing space bug)
SELECT lot_no, is_closed, closed_by, closed_on, lot_status
FROM ret_lot_inwards
WHERE is_closed = 0 AND lot_status = 0
  AND lot_no IN (
    SELECT DISTINCT tag_lot_id FROM ret_taging WHERE tag_status NOT IN (2,5)
  );
-- Row count > 0 means open lots still have active tags — verify intentional
```

### 8c. Check Merge Eligibility Duplicates (R-LOT-039)
```sql
-- getLotidsforSplit missing GROUP BY → duplicate lot_no entries
-- Verify directly:
SELECT lot_no, COUNT(*) as appearances
FROM ret_lot_inwards l
LEFT JOIN ret_lot_inwards_detail ltd ON ltd.lot_no = l.lot_no
WHERE l.stock_type = 1 AND l.is_closed = 0
GROUP BY l.lot_no
HAVING COUNT(*) > 1;
```

### 8d. Check Tag Model Legacy Column Impact (R-LOT-044)
```sql
-- Verify which legacy columns from old ret_lot_inwards schema still exist
-- ret_tag_model L977 reads: lot_sub_product, no_of_tags, metal, normal_stn_certificate...
SHOW COLUMNS FROM ret_lot_inwards LIKE 'lot_sub_product';
SHOW COLUMNS FROM ret_lot_inwards LIKE 'no_of_tags';
SHOW COLUMNS FROM ret_lot_inwards LIKE 'normal_stn_certificate';
-- If these return empty, ret_tag_model L977 is getting NULL for those fields silently
```

### 8e. Key Context: Tagging Module Reads These Lot Fields
| Lot Field | Used By Tag Module | Impact If Wrong |
|---|---|---|
| `is_closed` | Lot open/close check | Tags against closed lots if lot_completed fails (R-LOT-023) |
| `is_lot_split` | Split eligibility | Tags blocked incorrectly if flag set wrong |
| `lot_no` | Tag lot_id join | All tag queries break if lot header deleted (R-LOT-012) |
| `stock_type` | Tag type context | Wrong type = wrong tag display |
| `lot_from` | Lot origin display | Display only — low risk |
