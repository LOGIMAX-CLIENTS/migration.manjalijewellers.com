# Tagging Module — Forensic Investigation Template

> **Module**: Tagging
> **Last Updated**: 2026-03-24 (Round 10)
> **Purpose**: Layer-by-layer cheat sheet for diagnosing Tagging module bugs

---

## Layer 1 — Symptom Collection

### Common Symptoms Checklist

| #   | Symptom                                         | Likely Area                                 | First Check                                                     |
| --- | ----------------------------------------------- | ------------------------------------------- | --------------------------------------------------------------- |
| S1  | Tag sale value shows wrong amount               | JS calc / MC type / wastage                 | `calculateTagSaleValue()` L15757                                |
| S2  | Tag not appearing in list                       | Status filter / branch filter / soft delete | `ajax_getTaggingList()` WHERE clause                            |
| S3  | Lot balance incorrect after tagging             | Balance deduction logic                     | `tagging('save')` L2748+ deduction loop                         |
| S4  | Stone weight/amount mismatch                    | Stone calc formula                          | `calculate_stone_amount()` L17818                               |
| S5  | Making charge incorrect                         | MC type mismatch (1/2/3)                    | `tag_mc_type` field + `calculate_total_mc()` L29306             |
| S6  | Tag delete doesn't restore balance              | Delete/OTP flow decoupled — **R10 CORRECTED**: `tagging('delete')` soft-deletes but lot balance is restored through a separate OTP confirm flow | `tagging('delete')` case L2079; lot restore is separate via `verify_otp()` L5580 |
| S7  | Branch transfer not created on cross-branch tag | BT creation condition                       | `tagging('save')` branch comparison block                       |
| S8  | QR code not generating                          | File path / permission                      | `generate_tagqrcode()` L6248                                    |
| S9  | Duplicate tag code generated                    | Code gen race condition                     | `code_number_generator()` L1825                                 |
| S10 | Order link/unlink OTP not received              | SMS config / mobile number                  | `send_order_unlink_otp()` L8774                                 |
| S11 | Tag images not saving                           | Base64 conversion / path                    | `save_base64_image()` L6398                                     |
| S12 | Wastage calculation wrong                       | Settings lookup / formula                   | `get_wastage_settings_details()` + `calc_wastage()` L33918      |
| S13 | HUID validation fails incorrectly               | Duplicate check query                       | `validate_huid()` L6222                                         |
| S14 | Retag creates wrong lot type                    | Process type logic                          | `create_retag()` L6755                                          |
| S15 | Purchase cost showing zero                      | PO lookup / formula                         | `update_purchase_cost()` L8991                                  |
| S16 | Bulk edit not saving changes                    | Form data collection                        | `validate_bulk_edit()` L10442                                   |
| S17 | Tax amount incorrect                            | Tax calc duplicate funcs                    | `calculate_base_value_tax()` — which instance? L16677 or L28549 |
| S18 | Sold tag (status=1) appears as deleted in UI    | RISK-014 — delete case doesn't check status | `tagging('delete')` L2079 — no tag_status guard                 |
| S19 | Retag failed midway — old tags stuck at status 3 | RISK-015 — nested tx rollback gap          | `create_retag()` L6939 trans_begin; `generateRetaglot()` sub-method calls |

---

## Layer 2 — Reproduce & Isolate

### Reproduction Checklist

1. **Which branch?** → Tags are branch-specific. Same tag may behave differently per branch.
2. **Which product category?** → `cat_type` (1=Ornament, 2=Bullion, 3=Stone, 4=Alloy) drives different calc paths.
3. **Which MC type?** → `tag_mc_type` (1=Per Piece, 2=Per Gram, 3=% on Price) changes the formula.
4. **Which calculation base?** → `calculation_based_on` (0=gross, 1=net, 2=gross) affects weight used.
5. **Is this a new tag or edited tag?** → Save vs Update have different code paths.
6. **Is this a cross-branch tag?** → Triggers additional BT creation logic.
7. **Which lot is the source?** → Lot type/rate calc affects tag defaults.

### Isolation Questions

| Question                                       | If Yes →                         | If No →                     |
| ---------------------------------------------- | -------------------------------- | --------------------------- |
| Does bug occur on ALL branches?                | System-wide code issue           | Branch-specific config      |
| Does bug occur for ALL product types?          | Generic logic bug                | Category-specific code path |
| Does bug occur on save AND edit?               | Shared calc function             | Save-only or edit-only code |
| Did it work before a recent change?            | Regression — diff recent commits | Longstanding bug            |
| Does the same tag show correct values in scan? | Display bug only                 | Calculation/storage bug     |

---

## Layer 3 — Client-Side Trace

### Console Log Points

```javascript
// Tag Sale Value — put breakpoint at:
// L15757: calculateTagSaleValue() — new tag
// L19409: calculateTagEditSaleValue() — edit tag
// L27243: calculateTagFormSaleValue() — form aggregate
// L32071: calculateTagPreviewSaleValue() — preview panel

// Stone Calculation:
// L17818: calculate_stone_amount() — individual stone row
// L17890: calculate_total_stone_amount() — stone total

// MC/Wastage:
// L29306: calculate_total_mc() — making charge
// L33918: calc_wastage() — wastage calculation
// L29122: get_mc_va_limit() — MC limits from server

// Tax (CAUTION — duplicate definitions!):
// L16677 AND L28549: calculate_base_value_tax() — second OVERWRITES first
// L16741 AND L28617: calculate_arrived_value_tax() — second OVERWRITES first
```

### Key Variables to Inspect

| Variable                             | Where      | What                          |
| ------------------------------------ | ---------- | ----------------------------- |
| `$('#gross_wt_X').val()`             | Form row X | Gross weight input            |
| `$('#less_wt_X').val()`              | Form row X | Less weight input             |
| `$('#net_wt_X').val()`               | Form row X | Net weight (computed)         |
| `$('#calculation_based_on_X').val()` | Form row X | 0/1/2 — which weight for calc |
| `$('#tag_mc_type_X').val()`          | Form row X | MC type: 1/2/3                |
| `$('#tag_mc_value_X').val()`         | Form row X | MC amount                     |
| `$('#sell_rate_X').val()`            | Form row X | Metal rate                    |
| `$('#sales_value_X').val()`          | Form row X | Computed sale value           |

### Network Tab Checks

| AJAX Endpoint                  | What to Check                                    |
| ------------------------------ | ------------------------------------------------ |
| `tagging/save`                 | POST body: verify `lt_item` array has all fields |
| `update_tagging_data`          | POST body: verify tag_id + all updated fields    |
| `get_tag_scan_details`         | Response: verify stone/material data in response |
| `get_wastage_settings_details` | Response: verify wastage % matches expected      |
| `get_mc_va_limit`              | Response: verify MC limits for product/design    |

---

## Layer 4 — Server-Side Trace

### Controller Trace Points

| Symptom                  | File       | Method                   | Line     | What to Check                        |
| ------------------------ | ---------- | ------------------------ | -------- | ------------------------------------ |
| Wrong sale value on save | Controller | `tagging('save')`        | 255-3003 | `$arrayTag['sales_value']` being set |
| Lot balance not deducted | Controller | `tagging('save')`        | ~2900    | Balance update queries               |
| Tag not found            | Controller | `get_tag_details()`      | 3635     | Model return value                   |
| OTP not sending          | Controller | `send_tag_otp()`         | 5418     | `$mobile` check + SMS model call     |
| Update fails silently    | Controller | `update_tagging_data()`  | 3687     | `$this->db->trans_begin()` at L3730, `trans_status()` check |
| Wrong stone total        | Controller | `tagging('save')`        | ~2600    | Stone loop aggregation               |
| BT not created           | Controller | `tagging('save')`        | ~2500    | Branch comparison `if` block         |
| Retag wrong status       | Controller | `create_retag()`         | 6755     | `tag_status = 3` assignment          |
| Purchase cost = 0        | Controller | `update_purchase_cost()` | 8991     | PO details lookup                    |
| Sold tag was deleted     | Controller | `tagging('delete')`      | 2079     | **RISK-014**: No `tag_status` guard before soft-delete |
| Retag old tags stuck=3   | Controller | `create_retag()`         | 6939     | **RISK-015**: Sub-method tx scope; check `generateRetaglot()` return |

### Model Trace Points

| Symptom               | Method                           | Line | What to Check                    |
| --------------------- | -------------------------------- | ---- | -------------------------------- |
| Tag listing empty     | `ajax_getTaggingList()`          | 360  | WHERE clause filters, raw SQL    |
| Wrong tag details     | `get_tag_details()`              | 2089 | JOIN conditions, column aliases  |
| Lot balance wrong     | `get_lot_inward_details()`       | 3466 | Balance subquery, stone balance  |
| Rate lookup returns 0 | `get_branchwise_rate()`          | 2741 | Branch filter, purity match      |
| MC limit wrong        | `get_mc_va_limit()`              | 9715 | Weight range matching            |
| Wastage wrong         | `get_wastage_settings_details()` | 7433 | Product→Design→SubDesign cascade |
| Duplicate tag code    | `code_number_generator()`        | 1825 | MAX() query, race condition      |
| Stone details missing | `get_stone_details()`            | 793  | tag_id filter                    |

---

## Layer 5 — Database Verification

### Diagnostic Queries

#### Q1: Complete Tag Record (header + children)

```sql
-- Pull complete tag with all child records
SELECT t.*, p.product_name, pur.purity_name, d.design_name
FROM ret_taging t
LEFT JOIN ret_product_master p ON t.product_id = p.pro_id
LEFT JOIN ret_purity pur ON t.purity = pur.id_purity
LEFT JOIN ret_design_master d ON t.design_id = d.design_no
WHERE t.tag_id = {TAG_ID};

-- Stones
SELECT * FROM ret_taging_stone WHERE tag_id = {TAG_ID};

-- Materials
SELECT * FROM ret_taging_material WHERE tag_id = {TAG_ID};

-- Images
SELECT * FROM ret_taging_images WHERE tag_id = {TAG_ID};

-- Status log
SELECT * FROM ret_section_tag_status_log WHERE tag_id = {TAG_ID} ORDER BY created_on;
```

#### Q2: Verify Sale Value Calculation

```sql
-- Compare stored sale_value vs expected
SELECT t.tag_id, t.tag_code, t.gross_wt, t.less_wt, t.net_wt,
       t.calculation_based_on,
       CASE t.calculation_based_on
         WHEN 0 THEN t.gross_wt WHEN 1 THEN t.net_wt WHEN 2 THEN t.gross_wt
       END as calc_weight,
       t.sell_rate, t.item_rate,
       t.tag_mc_type, t.tag_mc_value,
       t.retail_max_wastage_percent,
       t.sales_value,
       -- Stone total
       (SELECT COALESCE(SUM(amount), 0) FROM ret_taging_stone WHERE tag_id = t.tag_id) as stone_total
FROM ret_taging t
WHERE t.tag_id = {TAG_ID};
```

#### Q3: Orphan Records Check

```sql
-- Stones without parent tag
SELECT s.* FROM ret_taging_stone s
LEFT JOIN ret_taging t ON s.tag_id = t.tag_id
WHERE t.tag_id IS NULL;

-- Materials without parent tag
SELECT m.* FROM ret_taging_material m
LEFT JOIN ret_taging t ON m.tag_id = t.tag_id
WHERE t.tag_id IS NULL;

-- Images without parent tag
SELECT i.* FROM ret_taging_images i
LEFT JOIN ret_taging t ON i.tag_id = t.tag_id
WHERE t.tag_id IS NULL;
```

#### Q4: Lot Balance Integrity

```sql
-- For a given lot, compare: total tagged weight vs lot_inward_detail balance
SELECT lid.id_lot_inward_detail, lid.product_id,
       lid.gross_wt as original_gwt, lid.net_wt as original_nwt,
       lid.piece as original_pcs,
       -- What's been tagged from this lot detail
       COALESCE(SUM(t.gross_wt), 0) as tagged_gwt,
       COALESCE(SUM(t.net_wt), 0) as tagged_nwt,
       COALESCE(SUM(t.piece), 0) as tagged_pcs,
       -- Expected remaining = original - tagged (for active tags only)
       lid.gross_wt - COALESCE(SUM(CASE WHEN t.tag_status NOT IN (2,3) THEN t.gross_wt ELSE 0 END), 0) as expected_balance_gwt
FROM ret_lot_inward_detail lid
LEFT JOIN ret_taging t ON t.id_lot_inward_detail = lid.id_lot_inward_detail
WHERE lid.id_lot_inward = {LOT_ID}
GROUP BY lid.id_lot_inward_detail;
```

#### Q5: Duplicate Tag Code Check

```sql
SELECT tag_code, COUNT(*) as cnt
FROM ret_taging
WHERE tag_status != 2  -- exclude deleted
GROUP BY tag_code
HAVING COUNT(*) > 1;
```

#### Q6: Tags with Mismatched Branch

```sql
-- Tags where id_branch != current_branch (should only happen after BT)
SELECT tag_id, tag_code, id_branch, current_branch, tag_status
FROM ret_taging
WHERE id_branch != current_branch
AND tag_status NOT IN (2, 4);  -- exclude deleted and in-transit
```

---

## Layer 6 — Root Cause Classification

| Category              | Sub-Category        | Risk   | Example                                                                      |
| --------------------- | ------------------- | ------ | ---------------------------------------------------------------------------- |
| **Calculation**       | Sale value          | HIGH   | Wrong MC type applied → wrong total                                          |
| **Calculation**       | Weight              | HIGH   | `calculation_based_on` ignored → wrong weight base                           |
| **Calculation**       | Stone amount        | MEDIUM | Missing stone rows in total                                                  |
| **Calculation**       | Tax                 | MEDIUM | Duplicate JS function → wrong calc used                                      |
| **Data Integrity**    | Lot balance         | HIGH   | Tag created without deducting lot                                            |
| **Data Integrity**    | Orphan records      | MEDIUM | Stones/materials without parent tag (confirmed R9 — no child cleanup on del) |
| **Data Integrity**    | Tag status          | HIGH   | Status set by wrong module; no guard before delete (RISK-014, R9)            |
| **Data Integrity**    | Status log mismatch | MEDIUM | `ret_taging_status_log` vs `ret_section_tag_status_log` both written — can diverge |
| **Transaction**       | Nested tx scope     | HIGH   | `create_retag()` outer tx + sub-method tx may not rollback correctly (RISK-015, R9) |
| **Transaction**       | Decoupled ops       | HIGH   | Tag delete and lot balance restore are separate flows — one can succeed without the other |
| **Security**          | SQL injection       | HIGH   | Raw `$_POST` in model queries                                                |
| **Security**          | CSRF                | MEDIUM | No token on delete flows                                                     |
| **Performance**       | N+1 query           | MEDIUM | Stone lookup per tag in listing                                              |
| **UI/Display**        | Wrong data shown    | LOW    | Stale DataTable, JS cache                                                    |
| **Flow**              | OTP failure         | MEDIUM | Wrong mobile, SMS service down                                               |
| **Flow**              | BT not created      | HIGH   | Branch comparison bug                                                        |
| **Flow**              | Order link          | HIGH   | Sold tag linked to order — no status pre-check (confirmed R9, L6047)         |

---

## Layer 7 — Stock Integrity (Specialized)

> Tagging manages inventory — every tag create/delete/retag affects stock balance.

### Stock Balance Verification Steps

1. **Before operation**: Record `ret_lot_inward_detail` balance for the target lot
2. **After operation**: Re-query balance and verify delta matches:
    - Tag create → balance should DECREASE by tag's piece/gwt/nwt
    - Tag delete → balance should INCREASE by tag's piece/gwt/nwt
    - Tag edit (lot change) → old lot INCREASES, new lot DECREASES
3. **Cross-check with non-tag stock**: For retag operations, verify `ret_non_tag_stock` was updated
4. **Transaction wrapper check**:
    - `tagging('save')`: ✅ Uses `trans_begin` — verify with `trans_status()` check
    - `update_tagging_data()`: ✅ Uses `trans_begin` at **L3730** — confirmed R10
    - `tagging('delete')`: ✅ Uses `trans_begin` L2081 — but log tables written, child records NOT cleaned
    - `create_retag()`: ⚠️ Outer `trans_begin` L6939 but sub-methods separate — **RISK-015**
5. **Decoupled operations check**: Tag delete (status=2) and lot balance restore are **separate flows** — verify both completed for every tag deletion

### Common Stock Bugs

| Bug Pattern                                          | Root Cause                           | How to Detect                            |
| ---------------------------------------------------- | ------------------------------------ | ---------------------------------------- |
| Phantom stock (balance shows items that don't exist) | Tag deleted but balance not restored | Q4 query shows negative expected balance |
| Missing stock (balance 0 but items exist)            | Double deduction                     | Q4 shows more tagged than original       |
| Lot shows complete but items remain                  | Wrong lot completion check           | `lot_status = 1` but balance > 0         |
| Non-tag stock mismatch after retag                   | `updateNTData()` arithmetic wrong    | Compare `ret_non_tag_stock` vs expected  |

---

## Layer 8 — Integration Point Trace (Specialized)

> Tagging makes 17 cross-module AJAX calls and shares data with 6+ modules.

### Cross-Module Failure Scenarios

| External Module               | Failure Point                                   | Impact on Tagging                  | Detection                               |
| ----------------------------- | ----------------------------------------------- | ---------------------------------- | --------------------------------------- |
| Reports (`admin_ret_reports`) | `get_ActiveProduct` returns empty               | Product dropdown empty in tag form | Check network response                  |
| Reports (`admin_ret_reports`) | `update_green_tag` fails                        | Green tag status not updated       | Check response status                   |
| Catalog (`admin_ret_catalog`) | `active_metals` returns wrong data              | Wrong metal options                | Check metal rate in response            |
| Catalog (`admin_ret_catalog`) | `getStoneRateSettings` returns empty            | Stone rate defaults to 0           | Verify rate in response                 |
| Billing                       | Tag marked sold but tag_status not 1            | Tag still shows as "On Sale"       | Check `tag_status` in DB                |
| Branch Transfer               | BT created but tag `current_branch` not updated | Tag shows wrong location           | Compare `id_branch` vs `current_branch` |
| SMS                           | SMS service timeout                             | OTP never received                 | Check SMS model response + log_message  |

### Timeout & Error Handling Check

```javascript
// Every AJAX call should have error handler — spot-check these critical ones:
// L29729: createTag() save — does it have error: callback?
// L44341: create_retag() — does it have error: callback?
// L26233: update_order_link() — does it have error: callback?
// L37455: add_to_transfer_tag() — does it have error: callback?
```
