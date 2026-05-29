# Estimation Module — Business Rules

> **Module**: Estimation
> **Date Built**: 2026-02-18 (Round 5 update)
> **Source**: `admin_ret_estimation.php`, `ret_estimation_model.php`, `ret_estimation.js`, `form.php` hidden fields, `est_print.php`, `est_print_2.php`

---

## Weight Calculations

### RULE-EST-001: Net Weight Calculation
- **Formula**: `Net Weight = Gross Weight − Less Weight`
- Where: `Less Weight = Stone Weight + Other Material Weight`
- **Implementation**: JS — inside `get_tag_data()` (L1393+), `calculateSaleValue()` (L8854+)
- **Validation**: Client-side only
- **Edge Cases**: If stone weight > gross weight, net weight goes negative (no guard)

### RULE-EST-002: Wastage Weight
- **Formula**: `Wastage Weight = Net Weight × (Wastage% / 100)`
- **Implementation**: JS — `set_tagging_wastage_and_mc()` (L21754)
- **Validation**: Wastage% is pulled from tagging master settings; also allows manual override
- **Edge Cases**: Wastage% > 100 — no server-side guard

### RULE-EST-003: Total Weight for Rate
- **Formula**: `Weight for Rate = Net Weight + Wastage Weight`
- **Implementation**: JS — used inside `calculateSaleValue()` for rate calculation

---

## Pricing / Sale Value Calculations

### RULE-EST-004: Rate Per Gram
- **Formula**: `Rate Per Gram = Market Rate for Metal × Purity Conversion`
- **Implementation**: JS — fetched via AJAX (`get_search_tag_metal_rates()`, L1977), stored in `manualRates` object
- **Source**: `ret_branchwise_rate` table (branch-specific metal rates)
- **Edge Cases**: Manual rate override allowed when `#manual_rate` checkbox is checked

### RULE-EST-005: Tagged Item Sale Value
- **Formula**: `Sale Value = (Net Weight + Wastage Weight) × Rate Per Gram + MC + VA`
- For stone-type items: `Sale Value = Market Rate Value` (flat rate)
- **Implementation**: JS — `calculatetag_SaleValue()` triggered on weight/rate change events
- **Validation**: Client-side only. Server stores the final value without re-calculation

### RULE-EST-006: MC (Making Charge) Calculation
- **Three Types** (based on `mc_type`):
  1. **Flat (Type 1)**: `MC = mc_value` (direct amount)
  2. **Percentage (Type 2)**: `MC = (Net Weight × Rate Per Gram) × mc_value / 100`
  3. **Per Gram (Type 3)**: `MC = Net Weight × mc_value`
- **Implementation**: JS — `calculate_total_mc_va()` (L18619-18681)
- **Edge Cases**: MC type from designs has limits (`get_mc_va_limit()`, L18527)

### RULE-EST-007: VA (Value Addition) Calculation
- **Same three types as MC** (flat, percentage, per gram)
- **Implementation**: JS — same function `calculate_total_mc_va()` but with VA parameters
- **Source**: VA range from `ret_va_range` table via `get_va_range()` AJAX call

### RULE-EST-008: Catalog Item Sale Value
- **Formula**: Same as tagged items but sourced from catalog (`ret_catalog_items` lookup)
- **Implementation**: JS — `calculateSaleValue()` (L8854-9247)

### RULE-EST-009: Custom Item Sale Value
- **Formula**: Same as tagged items but user enters product/purity/weight manually
- **Implementation**: JS — `calculateCustomItemSaleValue()` (L9249-9684)

---

## Old Metal Exchange

### RULE-EST-010: Old Metal Deduction (Full 19-Column)
- **Formula**: `Net Weight = GWT − Dust Wt − Stone Wt − (GWT × Wastage% / 100)`
- Then: `Amount = Net Weight × Rate Per Gram × (Exchange Value% / 100)`
- **Purpose field**: `1 = Cash Return`, `2 = Exchange Against New`
- **Implementation**: JS — `calculateOldMatelItemSaleValue()` (L9706-9818)
- **Validation**: Touch > 100 or ≤ 0 → reset to 100 (L9688-9700)
- **Restrictions**: `#allowed_old_met_pur` hidden field controls which metals employees can accept (1=All, 2=Gold only, 3=Silver only)
- **Rate Source**: `ret_old_metal_rate` table (NOT current market rate); bounded by `min_old_gold_rate`/`max_old_gold_rate`/`min_old_silver_rate`/`max_old_silver_rate`
- **Edge Cases**: Remarks field can be mandatory via `est_old_metal_remarks_req` setting; `max_cash_allowed` caps cash return value

### RULE-EST-011: Old Metal Stone Deduction
- **Formula**: `Net Old Metal = Old Metal Amount − Stone Value`
- **Implementation**: JS — within old metal calculation chain
- **Edge Cases**: Stone value can be 0 if no stones present

---

## Totals

### RULE-EST-012: Grand Total
- **Formula**: `Grand Total = Σ(Tag Sales Values) + Σ(Catalog Sales Values) + Σ(Custom Sales Values) − Σ(Old Metal Amounts) + Tax + Other Charges − Chit Adjustments − Gift Voucher − Sales Return Credits + Packaging`
- **Implementation**: JS — `calculate_purchase_details()` aggregates all sections
- **Validation**: Client-side only. Stored as `total_cost` in `ret_estimation`

### RULE-EST-013: Tax Calculation
- **Formula**: `Tax = Taxable Amount × Tax Rate / 100`
- Tax rates sourced from `ret_taxmaster` table
- GST components: CGST, SGST, IGST
- **Implementation**: JS — within calculation chain, rate fetched with tag data

---

## Estimation Number

### RULE-EST-014: Bill Number Generation
- **Formula**: `EST/{BRANCH_CODE}/{FIN_YEAR}/{SEQUENCE}`
- **Implementation**: PHP — `model->generateEstiNo()` (L1456-1472)
- Uses `model->get_bill_no_format_detail()` (L239-381) — 142-line method with complex formatting logic
- **Edge Cases**: Bill format is configurable per branch in `ret_bill_no_format` table

---

## Chit Scheme

### RULE-EST-015: Chit Closing Balance
- **Formula**: `Closing Balance = Previous Balance − Amount Used`
- **Implementation**: JS — `calculate_chit_closing_balance()` on chit section change
- **Validation**: Cannot use more than available balance
- **Source**: `scheme_account` table via `get_scheme_accounts()` AJAX

---

## Partial & Tag Split

### RULE-EST-016: Partial Tag
- A tag can be partially used (e.g., sell 2 pieces from a 5-piece tag)
- When partial: `gwt`, `piece` become editable; MC recalculated based on partial weight
- **Implementation**: JS — `.partial` change handler (L10969-11021), `get_partial_details()` AJAX
- **Validation**: Piece count cannot exceed actual piece count

### RULE-EST-017: Tag Split
- A single tag can be split into multiple child tags for partial estimation
- **Implementation**: JS — `set_tag_split_details()` (L26321-28297) — **1,976 lines!**
- This is the most complex single function in the entire module
- Recalculates weights, stones, and charges for each child split

---

## Validation Rules

### RULE-EST-VAL-001: Customer Required
- Customer must be selected before save
- **Implementation**: JS validation checks `$('#cus_id').val() != ''`

### RULE-EST-VAL-002: Branch Required
- Branch must be selected (if multi-branch setting enabled)
- **Implementation**: JS checks `$('#id_branch').val() != ''`

### RULE-EST-VAL-003: At Least One Item
- Estimation must have at least one item (tag, catalog, or custom)
- **Implementation**: JS checks table row counts

### RULE-EST-VAL-004: Rate Must Be Positive
- Rate per gram must be > 0 for non-stone items
- **Implementation**: JS — `check_rate_is_valid()` (L18839-18895), `check_min_max_rate()` (L21976-22068)
- Checks against min/max rate from `ret_va_range` table

### RULE-EST-VAL-005: GST Format Validation
- GSTIN must match regex: `^[0-9]{2}[a-zA-Z]{4}([1-9]|[a-zA-Z]){1}[0-9]{4}[a-zA-Z]{1}([1-9]|[a-zA-Z]){2}([0-9]|[a-zA-Z]){1}$`
- **Implementation**: JS — GST change handler (L2957+)

### RULE-EST-VAL-006: Mobile Number Length
- Mobile length validated against country-specific min/max from `country` table attributes
- **Implementation**: JS — `#cus_mobile` blur handler (L2802-2839)

### RULE-EST-VAL-007: Duplicate ID Checks
- PAN, Aadhaar, GST, Passport, DL checked for duplicates via AJAX before save
- **Implementation**: Controller — `pan_available()`, `aadhar_available()`, `gst_available()`, `passport_available()`, `dl_available()`

---

## Gift Voucher *(Round 2)*

### RULE-EST-018: Gift Voucher Redemption
- **Formula**: `Gift Voucher Deduction = Σ(voucher amounts)`
- **Implementation**: Added as rows in `estimation_gift_voucher_details` table (DOM); saved to `ret_est_gift_voucher_details`
- **Validation**: `validateVoucherDetailRow()` (L8796-8812) — checks voucher number and amount
- **Edge Cases**: Multiple vouchers can be stacked; deducted from grand total

---

## Wedding Purchase Discount / VA Slab *(Round 2)*

### RULE-EST-019: VA Slab Discount Application
- **Trigger**: `profile_setting.wedding_wastage_slab == 1` → shows VA slab section
- **Formula**: For each metal+weight range slab → applies VA% override to matching item rows
- **Implementation**: JS — `wastage_slab_value()` (L29994-30489, **496 lines**)
  - Iterates all tag/catalog/custom rows
  - If item's metal + weight falls within slab range → overrides wastage% with slab value
  - `apply_va_slab_row()` (L29921) applies per-slab-row
  - `remove_va_slab_row()` (L29972) reverts wastage to original
- **Edge Cases**: Slab ranges can overlap; last-applied takes precedence

---

## Sales Return in Estimation *(Round 2)*

### RULE-EST-020: Sales Return Credit
- **Trigger**: `enable_sales_return_estimations.value == 1` → shows SR section
- **Formula**: `SR Credit = Σ(selected bill item amounts)` — deducted from grand total
- **Implementation**: JS — `getBillDetails()` (L31062-31273, **211 lines**) fetches bill items; `#update_bill_return` applies selected items
- **Saved to**: `ret_est_sales_return_utilization` table
- **Chit Impact**: Adding/removing SR triggers `get_topup_chit_closing_balance('','sr_add')` to recalculate

---

## Stone & Bulk Discount *(Round 2)*

### RULE-EST-021: Stone Discount (Summary Level)
- **Formula**: Each stone's rate reduced by `summary_stn_dis_per%`
- **Implementation**: `#disc_apply` click handler (L29571-29725) — iterates ALL tag and custom rows, modifies each row's `stone_details` JSON array
- **Reset**: `#disc_reset` (L29439-29558) restores original stone values
- **Limits**: Bounded by `min_stn_disc_limit` / `max_stn_disc_limit` hidden fields from `stn_disc_per` setting
- **Edge Cases**: Discount rounds to `roundVal` precision; check via `check_disc_min_max_stone_rate()`

### RULE-EST-022: Bulk Wastage Discount
- **Formula**: Each tag item's wastage reduced by `summary_blk_dis_per%`
- **Implementation**: `#blk_disc_apply` (L30642-30693) — iterates tag rows, reduces wastage
- **Limits**: Bounded by `blk_wast_disc_lmt` hidden field
- **Reset**: `#blk_disc_reset` (L30694-30724)

---

## EDA (Estimate Discount Approval) *(Round 2)*

### RULE-EST-023: EDA Flag
- **Trigger**: `IS EDA` checkbox on form
- **Effect**: Sets `ret_estimation.is_eda = 1` → routes estimation to separate EDA approval queue
- **Approval Flow**: Manager views EDA list → clicks Approve/Reject via modal
- **Impact**: Unapproved EDA estimations cannot proceed to billing

---

## Packaging *(Round 2)*

### RULE-EST-024: Packaging Box / Other Invoice
- Items added to `estimation_other_inv_details` table (item name, pieces, image)
- **Saved to**: `ret_estimation_other_inventory_issue`
- **Impact on Total**: Added to grand total as flat amount

---

## Additional Validation Rules *(Round 2)*

### RULE-EST-VAL-008: Employee Selection
- Employee must be selected when `est_emp_select_req` = 1
- **Implementation**: JS — checks `#emp_id` hidden field configured via `est_emp_select_req` setting

### RULE-EST-VAL-009: Rate Tolerance
- Manual rate must be within `min_gold_tol` / `max_gold_tol` (for gold) or `min_silver_tol` / `max_silver_tol` (for silver)
- **Implementation**: JS — `check_rate_is_valid()` (L18839) checks against tolerance hidden fields
- **Source**: Employee settings (`emp_setting`)

### RULE-EST-VAL-010: Stone Discount Limits
- Stone discount % must be within `min_stn_disc_limit` and `max_stn_disc_limit`
- **Implementation**: JS — `check_disc_min_max_stone_rate()` (L30535-30603)

### RULE-EST-VAL-011: Old Metal Purity Restriction
- Employee's `allowed_old_met_pur` controls which metals can be accepted: 1=All, 2=Gold only, 3=Silver only
- **Implementation**: JS — checks against hidden field `#allowed_old_met_pur` on metal type change

### RULE-EST-VAL-012: Day Closing Gate *(Round 3)*
- Estimation creation/editing should be blocked when `ret_day_closing.is_day_closed == 1` for the branch
- **Implementation**: Model `getBranchDayClosingData(id_branch)` queries `ret_day_closing` — controller enforcement unclear

### RULE-EST-VAL-013: Access Time Gate *(Round 5)*
- Controller `__construct()` (L31-48) enforces time-of-day access restriction
- Reads `access_time_from` / `access_time_to` from session (set at login)
- If current timestamp is outside allowed window → redirect to `/chit_admin/logout` with flash message "Exceeded allowed access time!!"
- **Edge Case**: If `access_time_from` is NULL or empty, gate is bypassed (no restriction)

---

## Tax Logic *(Round 3)*

### RULE-EST-025: IGST vs CGST/SGST Split
- **Rule**: If company state == customer state → split tax into CGST (50%) + SGST (50%); if different states → single IGST (100%)
- **Implementation**: Print template `est_print_2.php` (L559-595) compares `comp_details['id_state']` with `estimation['id_state']`
- **Note**: `est_print.php` (default thermal) does NOT implement this check — always shows CGST/SGST
- **Edge Case**: If customer `id_country` is empty, defaults to same-state (CGST/SGST)

### RULE-EST-026: Print Recalculation
- Print templates recalculate VA, MC, stone totals, charge totals in PHP — NOT reading stored values
- Three `calculation_based_on` modes applied in PHP print:
  - Mode 0: VA from gross_wt, MC from gross_wt
  - Mode 1: VA from net_wt, MC from net_wt
  - Mode 2: VA from net_wt, MC from gross_wt
- **Risk**: If JS and PHP calculation diverge, printed totals differ from form totals

### RULE-EST-027: HUID Display *(Round 3)*
- `est_print_2.php` displays `hu_id` and `hu_id2` fields from tag data when present (Hallmark Unique ID — BIS compliance)
- Concatenated with comma separator when both HUID values exist
- Only displayed if values are non-empty and not "-"

### RULE-EST-028: Chit Benefit Rendering in Print
- Chit benefits rendered differently by `scheme_type`:
  - Type 0: Amount-based — shows `utl_amount` directly
  - Types 1/2/3: Weight-based — calculates `closing_weight × rate_per_gram` (or gold rate if rate_per_gram == 0)
  - VA savings: `savings_in_wastage × rate × gold_rate` (when `wastage_per > 0`)
  - MC savings: `savings_in_making_charge` (flat amount)
  - Additional benefits / Deductions: `additional_benefits`, `closing_add_chgs`
- **Total Chit** = weight_amount + wastage_amount + mc_amount + scheme_amount - deductions + benefits
