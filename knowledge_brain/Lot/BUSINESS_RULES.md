# LOT MODULE — BUSINESS RULES
> Module: Lot | Round 1 | 2026-03-17

---

## RULE-LOT-001: Lot Receive Branch Control

**Formula**: If `lot_recv_branch` setting = 1 → only HO can receive; if = 2 → any branch

**Implementation**:
- PHP: `empty_record_inward()` model L357–375
- Settings key: `ret_settings.name = 'lot_recv_branch'`
- `$settings == 1 && empty($loggedinbranch)` → force to HO branch
- Otherwise: `$ho = get_branchName($loggedinbranch)`, `$settings = 2`

**Validation**: Server-side only (defaults set on form load)

**Edge Case**: If user has no `id_branch` in session and setting≠1, this may fail

---

## RULE-LOT-002: Stock Type Routing

**Formula**: `stock_type=1` → Tagged lot → goes to tagging; `stock_type=2` → Non-Tagged → updates `ret_nontag_item`

**Implementation**:
- JS: `lot_inward` form radio `inward[stock_type]` change handler (L1091–1211)
  - stock_type=1: shows `.tagged` elements, requires purity/category
  - stock_type=2: hides `.tagged`, enables section selector, purity/category not required
- PHP controller L851–991 (save), L2072–2166 (merge): NonTag logic block

**Edge Case**: On form switch, all items in table are cleared (L1105, L1129)

---

## RULE-LOT-003: Lot Origin (lot_from) Values

**Formula**: Determines the source of lot inward

| Value | Origin |
|---|---|
| 1 | Manual (normal lot entry) |
| 2 | From Supplier Entry (Purchase/GRN) |
| 3 | From Import |
| 4 | From Tag Process |
| 5 | From Old Metal Process |
| 6 | From Retagging |
| 7 | Lot Merge (set by system) |
| 8 | Non-Tag Lot |

**Implementation**: `ajax_getLotList` model L196 (display), `lot_merge/save` L2000 (hardcoded=7)

**Rule**: Only `lot_from=1` lots are editable (controller L1126–1135: `if lot_from != 1 → redirect with error`)

---

## RULE-LOT-004: Lot Edit Restriction

**Formula**: Lot is editable only if `lot_from=1` (Manual) AND `is_closed=0`

**Implementation**: Controller L1126–1146
- `lot_from != 1` → "You are not allowed to edit this record" → redirect
- `is_closed == 1` → "This lot is closed. You are not allowed to edit" → redirect

**Validation**: Server-side only

---

## RULE-LOT-005: Day Close Date Override

**Formula**: `lot_date = if(entry_date == today) then now() else entry_date`

**Implementation**: Controller L344–346 (save), L2471–2473 (lot_completed)
```php
$dCData = admin_settings_model->getBranchDayClosingData($branch_id);
$bill_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);
```

**Validation**: Server-side only

---

## RULE-LOT-006: Purchase Cost Calculation Types

**Formula**: `calc_type` in `ret_lot_inwards_detail` controls how purchase cost is computed

| calc_type | Formula |
|---|---|
| 1 | Weight × Rate |
| 2 | Purchase Touch |
| 3 | Weight × Wastage % |
| NULL | Not specified (shown as '-') |

**Implementation**: Model `get_lotInward_detail()` L511, `get_lotInward_data()` L1697 — display labels; JS UI sets the value

---

## RULE-LOT-007: Making Charge Type

**Formula**: `mc_type` in `ret_lot_inwards_detail`

| mc_type | Meaning |
|---|---|
| 1 | Per Gram |
| 2 | Per Pcs |

**Implementation**: Model L509 (`IF(id.mc_type = 2 ,'PER GRAM','PER PCS') as mc_type_name`)

⚠️ **Bug Note**: The IF condition is inverted: mc_type=2 shows 'PER GRAM' but should be 'PER PCS'. The actual label logic appears swapped.

---

## RULE-LOT-008: Rate Calculation Type

**Formula**: `rate_calc_type` controls how rate is applied

| rate_calc_type | Meaning |
|---|---|
| 1 | Per Gram |
| 2 | Per Pcs |

**Implementation**: Model L513 (`IF(id.rate_calc_type = 1,'Gram','Pcs') as rate_calc_types`)

---

## RULE-LOT-009: Merge Eligibility

**Formula**: A lot is eligible for merge if ALL of the following are true:
1. `is_lot_split = 0`
2. No tagged items exist (`lt_tag.tag_lot_id IS NULL`)
3. Not already in `ret_lot_merge` as source
4. Not already a merged lot (not in `lm_lot`)
5. `is_closed = 0`
6. `stock_type IN (1,2)`

**Implementation**: `getLotidsforMerge()` model L1542–1582

---

## RULE-LOT-010: Split Eligibility

**Formula**: A lot is eligible for split if:
1. Not tagged (`lt_tag.tag_lot_id IS NULL`)
2. Not merged (`lt_merg.lot_no IS NULL`)
3. `stock_type = 1` (Tagged only — NonTag cannot be split)
4. Balance pieces > 0 (gross_wt - split_grs_wt > 0)
5. `is_closed = 0`

**Implementation**: `getLotNoForSplit()` / `getLotidsforSplit()` model L1282–1622

---

## RULE-LOT-011: Lot Item Delete Guard

**Formula**: A lot item row can be deleted only if it has NOT been tagged

**Implementation**: `lot_inwards_detail()` controller L1711
- `check_is_tagged($id_lot_inward_detail)` → if tagged → return error message
- If not tagged → delete `ret_lot_inwards_detail`

---

## RULE-LOT-012: Lot Status Values

| lot_status | Meaning |
|---|---|
| NULL/0 | Active |
| 2 | Cancelled |

**Implementation**: `lot_inward/cancel_lot_entry` sets `lot_status=2` (L1053)

**Separate field `is_closed`**:
- 0 = Open/Active
- 1 = Closed (via `lot_completed`)

---

## RULE-LOT-013: Purchase Cost From Lot Setting

**Formula**: `is_purchase_cost_from_lot` setting controls whether purchase cost fields are displayed on lot form

**Implementation**: Controller L315 (add case), L1154 (edit case)
- `$data['is_purchase_cost_from_lot'] = get_ret_settings('is_purchase_cost_from_lot')`
- Passed to view for conditional display

---

## RULE-LOT-014: Supplier Bill Entry Required

**Formula**: `is_supplierbill_entry_req` setting controls whether supplier bill fields are mandatory

**Implementation**: `empty_record_inward()` L359, controller L1148 (edit)
- Setting read and passed to form

---

## RULE-LOT-015: Non-Tag Item Arithmetic

**Formula**: When a NonTag lot item is saved, if the same item (product+section+design+sub_design+branch) already exists in `ret_nontag_item`, quantities are **added** (not replaced)

**Implementation**: `updateNTData($nt_data, '+')` model L1092–1100
```sql
UPDATE ret_nontag_item SET 
  no_of_piece=(no_of_piece + {qty}),
  gross_wt=(gross_wt + {gwt}),
  net_wt=(net_wt + {nwt})
WHERE id_nontag_item={id}
```
