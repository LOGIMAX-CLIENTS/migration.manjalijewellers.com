# Edge Cases — Old Metal Process Module

Documented non-obvious boundary conditions that may cause silent data corruption, errors, or unexpected UX behavior.

---

## EC-1: `get_pocket_details()` — `sales_item_details` Uninitialized for Tagged and Non-Tagged Items

**File**: `ret_metal_process_model.php` line 840  
**Code**:
```php
if($items['trans_type']==1) {
    $item_details = $this->get_melting_pocket_details($items['id_metal_pocket']);
    $sales_item_details = $this->get_melting_SalesPocket_details($items['id_metal_pocket']);
} else if($items['trans_type']==2) {
    $item_details = $this->get_melting_tag_details($items['id_metal_pocket']);
    // $sales_item_details is NEVER SET here
} else if($items['trans_type']==3) {
    $item_details = $this->get_melting_non_tag_details($items['id_metal_pocket']);
    // $sales_item_details is NEVER SET here
}
if(sizeof($item_details) > 0 || sizeof($sales_item_details) > 0) // ❌ PHP Warning
```
**Effect**: For trans_type=2 (Tagged) and trans_type=3 (Non-tagged), `$sales_item_details` is undefined. The `sizeof($sales_item_details)` call triggers a PHP warning and evaluates to 0. If `$item_details` is also empty, the pocket row is silently skipped even if it has items.  
**Bug ID**: **OMP-024** (new R3)

---

## EC-2: `get_refining_process_details()` — Missing WHERE Clause

**File**: `ret_metal_process_model.php` lines 1538–1545  
**Code**:
```sql
SELECT t.received_wt as issue_nwt, if(r.refining_status=0,'Refining Issue','Refining Completed') ...
FROM ret_old_metal_process p 
LEFT JOIN ret_old_metal_refining r ON r.id_old_metal_process=p.id_old_metal_process
LEFT JOIN ret_old_metal_testing t ON t.id_metal_testing=r.id_metal_testing
LEFT JOIN(SELECT ... FROM ret_old_metal_refining_details d GROUP by d.id_metal_refining) as recd ...
-- ❌ NO WHERE clause — returns ALL refining records, not just for $id_old_metal_process
```
**Effect**: The detail process report sub-query returns aggregated data across ALL process records, not the specific one requested. `row_array()` returns only the first row.  
**Bug ID**: **OMP-023** (new R3)

---

## EC-3: `#category_row` DOM ID Shared by Three Modals

**File**: `admin/application/views/ret_metal_process/metal_process/form.php` lines 682, 724, 767  
**Problem**: All three modals use `id="category_row"` for their inner table:
- `#category_modal` (melting receipt weight modal): `<table id="category_row">`
- `#refining_category_modal` (refining weight modal): `<table id="category_row">`  
- `#polishing_category_modal` (polishing weight modal): `<table id="category_row">`

**JS Impact**: When JS queries `$('#category_row tbody')`, it finds ALL three tables due to duplicate IDs. jQuery returns only the FIRST match — so melting receipt category always controls which tbody gets updated.  
**Symptom**: Opening the Refining or Polishing receipt category modal then saving may write categories to the wrong row, or the category data isn't stored at all.  
**Bug ID**: **OMP-022** (new R3)

---

## EC-4: Zero-Piece Pocket HAVING Clause vs Display

**Model**: `get_melting_pocket_details()` at line 806  
```sql
HAVING issue_nwt < net_wt
```
**Edge**: If `issue_nwt` is `0.000` and `net_wt` is `0.001` (rounding artifact), the pocket shows as available but has no actual material. Operators see an empty pocket they cannot meaningfully issue from.  
**Workaround**: Add `AND net_wt > 0.001` to HAVING clause.

---

## EC-5: Polishing Issue — Diamond Weight Not Deducted from Balance

**JS**: `calculate_pocketing_details()` and `get_polishing_pocket_details()` model  
**Edge**: `issue_gwt` increases per issue. `gross_wt - issue_gwt` controls remaining balance. But `dia_wt` is tracked separately and never subtracted. If a pocket has 10g gold + 2g diamond (net_wt=8g gold), the balance calculation may show more remaining than actually available since `dia_wt` was never part of the `net_wt` tracked against `issue_nwt`.

---

## EC-6: Testing Issue — `blc_weight` Validation Only Caps Input; Doesn't Prevent Save

**JS**: `ret_metal_process.js` lines 2757–2766  
```javascript
if($(this).val() > blc) {
    row.find('.aerr').text('Invalid');
    row.find('.weight').val(blc);   // resets to blc
}
```
**Edge**: Input is capped to `blc`, then the aerr span shows "Invalid" but the value is already corrected. If the user submits immediately after the correction, the `blc` value is submitted — which is correct. However, if the cap fires on the LAST iteration (e.g., weight=blc exactly), the `aerr` span is cleared using `td:eq(4).aerr` — a wrong jQuery selector — `td:eq(4)` doesn't chain `.aerr` correctly.

---

## EC-7: Refining Receipt — Purity Not Required in JS Validation

**JS**: `validateRefiningReceiptCategoryRow()` line 2864  
The refining category row validates: category, section, product, design, sub_design, purity, recd_gross_wt.  
But the polishing category validation (`validatePolishingReceiptCategoryRow()`) also validates purity AND `id_design` AND `id_sub_design` even though those SAME fields are disabled via JS (`disabled=true`) when `is_non_tag` is unchecked. This means non-tag polishing items can never pass the category row validator because disabled selects return `null`.  
**Bug ID**: **OMP-027** (new R3)

---

## EC-8: Melting Issue — Multiple Pockets Can Reference Same `id_pocket_details`

**Model**: `get_melting_tag_details()` at line 862:
```sql
LEFT JOIN ret_old_metal_melting_details md ON md.id_pocket_details = d.id_pocket_details
WHERE d.id_metal_pocket = X and md.id_pocket_details IS NULL
```
This correctly filters already-issued items. But the filter only checks `ret_old_metal_melting_details` — not `ret_old_metal_polishing_details`. A pocket item can be sent to BOTH melting AND polishing simultaneously if both tables don't have a record yet.

---

## EC-9: Against-Melting Receipt — No JS Validation Before Save

**JS**: `saveProcess()` function  
When process_code='TESTING' and process_for=2 (receipt) and `against_melting==1`, the form shows the `#against_melting_receipt` table. However, the `validateTestingReceiptRow()` validator checks `#testing_receipt` table, NOT `#against_melting_receipt`. The against-melting receipt path is ENTIRELY unvalidated before submit.

---

## EC-10: Opening Stock — `blc_weight` Can Go Negative

**Model**: `get_opening_metal_stock_list()` line 1829–1835  
```sql
HAVING blc_weight > 0
```
If more pockets have been issued against opening stock than the actual opening balance (`blc_weight < 0`), the HAVING clause filters it. However, if data was manually adjusted, the arithmetic in the JS (`actual_weight - pocket_weight`) can yield negative values that `.toFixed(3)` displays as `-0.000` — which then passes the `> 0` check as `true` due to JS floating-point behavior.

---

## EC-11: `validateTestingIssueRow()` — Wrong Table Queried

**JS**: `ret_metal_process.js` lines 2877–2884  
```javascript
function validateTestingIssueRow(){
    row_validate = false;
    $('#testing_receipt > tbody > tr').each(function(index, tr) {  // ❌ WRONG TABLE
        if($(this).find('.is_melting_select').val()==1) {
            row_validate = true;
        }
    });
}
```
This function validates TESTING ISSUE but queries `#testing_receipt` (receipt table). The correct table is `#testing_process_details`. Result: testing issue validation always fails (row_validate stays false) because `#testing_receipt` is empty when testing issue is displayed.  
**Bug ID**: **OMP-025** (new R3)

---

## EC-12: Report Date Sent as `.html()` Not `.val()`  

**JS**: `get_metal_process_report()` line 4128:
```javascript
data: {'from_date':$('#rpt_payments1').html(), 'to_date':$('#rpt_payments2').html(), ...}
```
`$('#rpt_payments1').html()` returns the inner HTML of the element. If the date-picker element stores the date as `value` attribute (common), this would return `""` for `#rpt_payments1` if it has no child text nodes. Reports may silently show all historical data (no date filter applied).  
**Bug ID**: **OMP-026** (new R3)
