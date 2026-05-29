# Invariant Matrix
# Module: Sales Transfer

> **Purpose**: Documents variant-specific behavior where different configurations trigger different code paths.

---

## 1. Variant Dimensions

| # | Dimension Name | Possible Values | Controlling Field (DB) | Controlling Field (PHP) | Controlling Field (JS) |
|---|---|---|---|---|---|
| 1 | Transfer Type | 1=Sales Transfer, 2=Sales Return Transfer | N/A (page-based) | `$type` in `sales_transfer()` route | `ctrl_page[2]` ('add' vs 'ret_add') |
| 2 | Operation Mode | 1=Request, 2=Download | N/A (radio button) | Implicit via method called | `$("input[name='sales_transfer_item_type']:checked")` |
| 3 | Download Mode | 1=Batch, 2=Scan | `ret_settings.value` WHERE `name='sales_transfer_download'` | `$sales_transfer_download` | `$('#sales_trans_dnload').val()` |
| 4 | Against Bill | 1=Yes (against bill), 0=No (all available tags) | N/A | `is_aganist_bill` in POST | `$("input[name='aganist_bill']:checked")` |
| 5 | Metal for Billing | 0=No, 1=Yes | `ret_settings.value` WHERE `name='is_metal_for_billing'` | `$ismetalReq` | `$('#is_metal_for_billing').val()` |
| 6 | Calc Type | 1=Per Gram, 2=Per Piece | `ret_taging.calculation_based_on` | `$items['calc_type']` | `curRow.find('.calc_type').val()` |
| 7 | GST Type | Intra-State, Inter-State, International | `branch.id_country`, `branch.id_state` | `$from_branch_details`, `$to_branch_details` | `from_branch_country/state` vs `to_branch_country/state` |

---

## 2. Behavior Grids

### Grid: Transfer Type × Operation Mode

| | Request (1) | Download (2) |
|---|---|---|
| **Sales Transfer (add)** | `create_sales_transfer()` → bill_type=13, tag_status→4, log status=11 | Batch: `update_sales_transfer_request()`, Scan: `update_TagScan()` → tag_status→0, current_branch updated |
| **Sales Return Transfer (ret_add)** | `create_sales_ret_transfer()` → bill_type=14, tag_status→4, log status=12, amount negative | Batch: `update_sales_ret_transfer()`, Scan: `update_ret_TagScan()` → tag_status→0, current_branch updated |

### Grid: Download Mode × Transfer Type

| | Batch Download (1) | Scan Download (2) |
|---|---|---|
| **Sales Transfer** | Select bills → checkbox → `update_sales_transfer_request()` | `#scan_tag_no` → `getscan_TagSearchList()` → `update_TagScan()` one-by-one |
| **Sales Return Transfer** | Select bills → checkbox → `update_sales_ret_transfer()` | `#ret_scan_tag_no` → `get_retscan_TagSearchList()` → `update_ret_TagScan()` one-by-one |

### Grid: Against Bill × Return Transfer

| | Against Bill (1) | Not Against Bill (0) |
|---|---|---|
| **Return Request** | Queries `tag_status=0` tags from specific bill → groups by `cat_id` → sends category-level data | Queries `tag_status=6` tags (sold items) → sends individual `bill_det_id, bill_id, tag_id` |
| **Return Download** | N/A (download happens per bill) | N/A |

### Grid: Calc Type × Tax Calculation

| | Per Gram (1) | Per Piece (2) |
|---|---|---|
| **Taxable Amt** | `gross_wt × rate_per_grm` | `piece × rate_per_grm` |
| **Tax (3%)** | `taxable_amt × 3 / 100` | `taxable_amt × 3 / 100` |
| **Total** | `taxable_amt + tax` | `taxable_amt + tax` |

### Grid: GST Type × Tax Amounts

| | Same State (Intra) | Different State (Inter) | Different Country |
|---|---|---|---|
| **SGST** | `tax / 2` | 0 | 0 |
| **CGST** | `tax / 2` | 0 | 0 |
| **IGST** | 0 | `tax` | `tax` |

### Grid: Metal for Billing × Bill Number

| | Metal Required (1) | Metal Not Required (0) |
|---|---|---|
| **Bill Number** | `metal_code + '-' + auto_number` | `auto_number` |
| **Metal Select** | Visible, mandatory | Hidden |
| **Rate filtering** | Metal-specific lots loaded | All lots loaded |

---

## 3. Edge Cases & Special Combinations

### Tag Status 6 in Return Transfer
- **When**: `aganist_bill=0` (return without specific bill reference)
- **Expected**: Tags with `tag_status=6` should be available for return
- **Actual**: In `update_sales_ret_transfer()` L432-433, tags with `tag_status=6` only get `current_branch` updated, NOT `tag_status`. This is intentional — sold items being returned between branches.
- **Status**: ✅ Correct behavior (by design)

### Branch with No Day-Closing
- **When**: Branch has not done day-closing yet (no entry in day-closing table)
- **Expected**: Should prevent transfer
- **Actual**: `$fb_entry_date` and `$tb_entry_date` remain NULL, `strtotime(NULL) < strtotime(NULL)` = FALSE → validation passes
- **Status**: ⚠️ Possible issue — NULL date comparison should be explicitly handled

### Transaction Inside Loop (Sales Return)
- **When**: Multiple categories selected for return transfer
- **Expected**: All categories succeed or all fail
- **Actual**: `trans_begin()` is inside the foreach loop (L333), creating separate transactions per category. `trans_status()` at L371 only checks the last transaction.
- **Status**: ⚠️ Bug — partial commits possible if middle iteration fails

---

## 4. Variant Test Coverage

| Dimension | Total Variants | Tested | Untested | Coverage |
|---|---|---|---|---|
| Transfer Type | 2 | 0 | 2 | 0% |
| Operation Mode | 2 | 0 | 2 | 0% |
| Download Mode | 2 | 0 | 2 | 0% |
| Against Bill | 2 | 0 | 2 | 0% |
| Metal for Billing | 2 | 0 | 2 | 0% |
| Calc Type | 2 | 0 | 2 | 0% |
| GST Type | 3 | 0 | 3 | 0% |
| **Overall** | 15 | 0 | 15 | 0% |
