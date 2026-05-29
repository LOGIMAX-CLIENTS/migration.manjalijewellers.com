# Sales Transfer Module — Business Rules

> **Module**: Sales Transfer
> **Last Updated**: 2026-03-20 — Round 2 (Refresh)

---

## RULE-ST-001: Taxable Amount Calculation (Calc Type Based)
**Formula**:
- Per Gram (`calc_type=1`): `taxable_amt = gross_wt × rate_per_grm`
- Per Piece (`calc_type=2`): `taxable_amt = piece × rate_per_grm`

**Implementation**:
- PHP: `create_sales_transfer()` L145-149
- JS: `calculateSaleBillRowTotal()` L2133-2145

**Validation**: Both (JS for display, PHP for persistence)
**Edge cases**: If calc_type is neither 1 nor 2, no calculation happens (no default/error handling)

---

## RULE-ST-002: Tax Rate (Hardcoded 3%)
**Formula**: `tax_amount = (taxable_amt × 3) / 100`

**Implementation**:
- PHP: `create_sales_transfer()` L151
- JS: `calculateSaleBillRowTotal()` L2149

**Validation**: Both
**Edge cases**: Tax rate is **hardcoded** — not fetched from `ret_settings`. Any GST rate change requires code modification.

---

## RULE-ST-003: GST Split (IGST vs SGST+CGST)
**Formula**:
- Same country AND same state → `SGST = tax/2, CGST = tax/2, IGST = 0`
- Same country, different state → `IGST = tax, SGST = 0, CGST = 0`
- Different country → `IGST = tax, SGST = 0, CGST = 0`

**Implementation**:
- PHP: `create_sales_transfer()` L154-163
- JS: `get_sales_transfer_tag_list()` L1943-1991 (for display only, NOT used in request payload)

**Validation**: Server-side only (JS calculates for display, PHP re-calculates for storage)
**Edge cases**: `number_format($tax_amount/2, 2, ".", "")` — rounding to 2 decimals may cause 0.01 discrepancy vs non-rounded IGST

---

## RULE-ST-004: Item Total Cost
**Formula**: `item_cost = taxable_amt + tax_amount`

**Implementation**:
- PHP: `create_sales_transfer()` L152
- JS: `calculateSaleBillRowTotal()` L2153

**Validation**: Both
**Edge cases**: None

---

## RULE-ST-005: Day-Closing Date Validation (Download Only)
**Rule**: When downloading/approving a transfer, `to_branch` entry date must be >= `from_branch` entry date. Otherwise, reject with error.

**Implementation**:
- PHP: `update_sales_transfer_request()` L242-247
- PHP: `update_TagScan()` L478-483
- PHP: `update_ret_TagScan()` L551-556

**Validation**: Server-side only
**Edge cases**: `⚠️` `create_sales_transfer()` does NOT perform this check — day-closing validation is missing for REQUEST creation.

---

## RULE-ST-006: Bill Date Determination
**Formula**: `bill_date = (day_closing_date == today) ? current_datetime : day_closing_date`

**Implementation**:
- PHP: `create_sales_transfer()` L113
- PHP: `create_sales_ret_transfer()` L310

**Validation**: Server-side only
**Edge cases**: If day-closing hasn't been done, this will use the last day-closing date (potentially old), not today's date.

---

## RULE-ST-007: Tag Status Lifecycle
**Rule**: Tags follow specific status transitions during transfer operations:

| Operation | Before | After | Log Status |
|---|---|---|---|
| Sales Transfer Request | `tag_status=0` (available) | `tag_status=4` (in-transit) | status=11 |
| Sales Transfer Download | `tag_status=4` (in-transit) | `tag_status=0` (available) | status=0 |
| Sales Return Transfer Request | `tag_status=0` (available) | `tag_status=4` (in-transit) | status=12 |
| Sales Return Transfer Download | `tag_status=4` (in-transit) | `tag_status=0` (available) | status=0 |
| Return Download (tag_status=6) | `tag_status=6` (sold/returned) | `tag_status=6` (unchanged, only branch updated) | — |

**Implementation**: Multiple controller methods
**Edge cases**: Tags with `tag_status=6` in return download only get `current_branch` updated, NOT `tag_status` — this is intentional for sold items being returned.

---

## RULE-ST-008: Return Transfer Total (Negative Amount)
**Formula**: `tot_bill_amount = -$tot_bill_amount` (stored as negative in `ret_billing`)

**Implementation**: PHP: `create_sales_ret_transfer()` L367
```php
'-' . number_format($tot_bill_amount, 2, '.', '')
```

**Validation**: Server-side only
**Edge cases**: The negative sign is prepended as a string concatenation rather than mathematical negation. Result is same but unusual pattern.

---

## RULE-ST-009: Bill Number Generation (Metal-Based)
**Formula**:
- If `is_metal_for_billing=1`: `bill_no = metal_code + '-' + auto_generated_number`
- If `is_metal_for_billing=0`: `bill_no = auto_generated_number`

**Implementation**:
- PHP: `create_sales_transfer()` L117
- PHP: `create_sales_ret_transfer()` L316

**Validation**: Server-side only
**Edge cases**: Metal code comes from different sources in each method (`metal` table directly vs `ret_category` join)

---

## RULE-ST-010: Branch GST Filtering (Client-Side)
**Rule**: When selecting branches, the "To Branch" dropdown filters options based on GST number:
- **Sales Transfer Request** (type=1): Only show branches with SAME GST number (inter-branch within same entity)
- **Sales Transfer Download** (type=2): Show branches with DIFFERENT GST number (logged-in branch is "To", others are "From")

**Implementation**: JS: `getBTBranches()` L287-509, branch filter radio change handler L583-861
**Validation**: Client-side only
**Edge cases**: No server-side validation of GST match — if JS filtering is bypassed, any branch combination is accepted. **⚠️ R2 Finding**: `$('.from_branch').on('change')` handler at L537 always filters by DIFFERENT GST regardless of transfer type — contradicts initial load logic.

---

## RULE-ST-011: Return Transfer — Separate Bill per Category
**Rule**: When creating a Sales Return Transfer "Against Bill" (`aganist_bill=1`), each category in `req_data` creates a **separate** `ret_billing` record with its own `bill_no`, `ref_no`, and `bill_id`.

**Implementation**:
- PHP: `create_sales_ret_transfer()` L304-368 (foreach `$req_data`)

**Validation**: Server-side only
**Edge cases**: **⚠️ BUG (R2)**: `$tot_bill_amount` is initialized once at L295 but accumulates across all category iterations. The `updateData` at L367 uses the accumulated total, NOT per-category total. This means the 2nd category's bill includes the 1st category's amount. Fix: move `$tot_bill_amount = 0` inside the foreach loop.

---

## RULE-ST-012: From-Branch Change → To-Branch GST Filter
**Rule**: When user changes the "From Branch" dropdown, the "To Branch" dropdown is re-populated with branches that have a DIFFERENT GST number than the selected from-branch.

**Implementation**:
- JS: `$('.from_branch').on('change')` L515-569
- Compares `data-gst_number` attribute of selected from-branch against all other branches
- Only adds branches where `gst_number != from_branch_gst_number`

**Validation**: Client-side only
**Edge cases**: This logic filters by DIFFERENT GST (inter-entity), but the initial `getBTBranches()` load for type=1 (request) filters by SAME GST (intra-entity). The change handler overrides the initial filtering.

---

## RULE-ST-013: Scan Download Completion Detection
**Rule**: During scan-based download, the system checks if all tags have been scanned by comparing `actual_pcs` (total pieces in the bill) against `get_TagBilledPcs()` (count of tag_status=0 tags in that bill).

**Implementation**:
- PHP: `update_TagScan()` L505 — `if ($actual_pcs == $tagDet)`
- PHP: `update_ret_TagScan()` L578 — same logic
- JS: `$('#actual_pcs_dnload').val()` holds expected count

**Validation**: Server-side comparison, client provides expected count
**Edge cases**: **⚠️ Type coercion risk**: `$actual_pcs` is a string from POST, `$tagDet` is from DB query — `==` comparison works in PHP but `===` would fail. Also, `actual_pcs` counts pieces (may be >1 per tag), while `get_TagBilledPcs()` returns SUM of pieces — these should match but computation comes from different sources.
