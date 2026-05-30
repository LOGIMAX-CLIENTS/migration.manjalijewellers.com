# Recipe: Stock Rotation Detail — Performance + Weight Accuracy + Print Header

## Metadata
- **Pattern ID**: PAT-RPT-010
- **Severity**: HIGH
- **Modules Affected**: Ret_Reports (Stock Rotation Detail Report)
- **Auto-fixable**: No (multiple files, non-trivial query restructuring)

## Client Scope
- **Applies to**: ALL
- **Reason**: All clients share `get_stock_rotation_itemwise()` in `ret_reports_model.php` and the DataTable config in `ret_reports.js`

## Created By
- **Developer**: Antigravity
- **Client**: AMS-RetailAdmin (Dindigul branch verification)
- **Date**: 2026-05-06
- **Source Bug ID**: N/A (production discrepancy discovered during stock reconciliation)

---

## Symptom
Three problems with the Stock Rotation Detail report:

1. **Performance**: Selecting a 31-day range fires 31× per-day queries per movement type, hammering the production DB and causing timeouts.
2. **Weight mismatch**: Closing stock does not reconcile with the verified Stock In/Out report because:
   - Outward statuses included `11` (approval) instead of `7,9`
   - All bill types were aggregated into a single "sold" bucket — sales transfers (13), sales return transfers (14), and ecom outward were not separated
3. **Missing company header**: Print/Excel exports showed a bare text title instead of the full company header used in every other report (district collection, month-wise stock, etc.)
4. **Print includes all drill-down rows**: Export dumped every sub-product row; user only wants top-level (collection/product/category) summary rows

---

## Root Cause

### Performance
The original simulation loop executed `N` separate DB queries (one per day in the range) inside a `while` loop. For a 31-day range that's 31 query round-trips per movement type.

### Weight Accuracy
| Component | Old (wrong) | New (correct) | Direction |
|-----------|-------------|---------------|-----------|
| Branch outward statuses | `2,3,4,5,11` | `2,3,4,5,7,9` | REDUCE |
| Regular sold | All `bill_status=1` | `NOT IN (13,14,15) AND is_ecom='0'` | REDUCE |
| Sales transfer | _(lumped with sold)_ | `bill_type='13'` | REDUCE (new query) |
| Sales return transfer | _(not tracked)_ | `bill_type='14', is_cus_sale_return_transfer='0'` | ADDITION (new query) |
| Ecom outward | _(not tracked)_ | packing list based | REDUCE (new query) |

### Print Header
`messageTop` used a plain text string instead of the company HTML block. Hidden input fields (`#company_code`, `#company_address1`, etc.) exist in `header.php` but were not utilized.

### Print Row Filter
`exportOptions.rows` returned `true` for all rows, including hidden level-2 and level-3 drill-down rows.

---

## Detection

### Check if model has the old iterative pattern:
```powershell
findstr /n "status IN (2, 3, 4, 5, 11)" "application\models\ret_reports_model.php"
```
If it returns a match inside `get_stock_rotation_itemwise()`, the old bug exists.

### Check if sales query is missing bill_type filter:
```powershell
findstr /n "5. Sales per day (all bill types)" "application\models\ret_reports_model.php"
```
If found, the sales query is not split into separate categories.

### Check if print has company header:
```powershell
findstr /n "company_code" "assets\js\ret_reports.js" | findstr "render_sr_detail_tree"
```
If no match near the `render_sr_detail_tree` function, the print header is missing.

---

## Files
- `application/models/ret_reports_model.php` — function `get_stock_rotation_itemwise()` (queries 4–6)
- `assets/js/ret_reports.js` — function `render_sr_detail_tree()` (report title + export options)

---

## Fix

### Fix 1: Branch Outward Statuses (ret_reports_model.php)

#### Before
```php
// -- 4. Outward per day (status 2,3,4,5=issue/transfer, 11=approval outward) --
$sql_out = "SELECT p.pro_id as sub_pro_id, DATE(m1.date) as log_date, SUM(tag.gross_wt) as gwt
    FROM ret_taging_status_log m1
    LEFT JOIN ret_taging tag ON tag.tag_id = m1.tag_id
    $common_joins
    WHERE m1.status IN (2, 3, 4, 5, 11)
        AND m1.date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59'
        AND m1.from_branch IN ($id_branch)
        AND c.id_collection NOT IN ($hide_collections)
        $coll_filter $prod_filter
    GROUP BY p.pro_id, DATE(m1.date)";
```

#### After
```php
// -- 4. Branch outward per day (status 2,3,4,5,7,9 from_branch) --
$sql_out = "SELECT p.pro_id as sub_pro_id, DATE(m1.date) as log_date, SUM(tag.gross_wt) as gwt
    FROM ret_taging_status_log m1
    LEFT JOIN ret_taging tag ON tag.tag_id = m1.tag_id
    $common_joins
    WHERE m1.status IN (2, 3, 4, 5, 7, 9)
        AND m1.date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59'
        AND m1.from_branch IN ($id_branch)
        AND c.id_collection NOT IN ($hide_collections)
        $coll_filter $prod_filter
    GROUP BY p.pro_id, DATE(m1.date)";
```

---

### Fix 2: Split Sales Into 4 Separate Queries (ret_reports_model.php)

#### Before
```php
// -- 5. Sales per day (all bill types) --
$sql_sold = "SELECT p.pro_id as sub_pro_id, DATE(bill.bill_date) as log_date, SUM(tag.gross_wt) as gwt
    FROM ret_billing bill
    INNER JOIN ret_bill_details b ON b.bill_id = bill.bill_id
    LEFT JOIN ret_taging tag ON tag.tag_id = b.tag_id
    $common_joins
    WHERE bill.bill_status = 1
        AND bill.bill_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59'
        AND bill.id_branch IN ($id_branch)
        AND c.id_collection NOT IN ($hide_collections)
        $coll_filter $prod_filter
    GROUP BY p.pro_id, DATE(bill.bill_date)";
$sold_rows = $this->db->query($sql_sold)->result_array();
$sold_data = [];
foreach ($sold_rows as $r) {
    $sold_data[$r['sub_pro_id']][$r['log_date']] = floatval($r['gwt']);
}
```

#### After
```php
// -- 5a. Regular sold per day (bill_type NOT IN 13,14,15 AND is_ecom=0) → REDUCE --
$sql_sold = "SELECT p.pro_id as sub_pro_id, DATE(bill.bill_date) as log_date, SUM(tag.gross_wt) as gwt
    FROM ret_billing bill
    INNER JOIN ret_bill_details b ON b.bill_id = bill.bill_id
    LEFT JOIN ret_taging tag ON tag.tag_id = b.tag_id
    $common_joins
    WHERE bill.bill_status = 1
        AND bill.bill_type NOT IN ('13','14','15')
        AND bill.is_ecom = '0'
        AND bill.bill_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59'
        AND bill.id_branch IN ($id_branch)
        AND c.id_collection NOT IN ($hide_collections)
        $coll_filter $prod_filter
    GROUP BY p.pro_id, DATE(bill.bill_date)";
$sold_rows = $this->db->query($sql_sold)->result_array();
$sold_data = [];
foreach ($sold_rows as $r) {
    $sold_data[$r['sub_pro_id']][$r['log_date']] = floatval($r['gwt']);
}

// -- 5b. Sales transfer per day (bill_type=13) → REDUCE --
$sql_sales_trans = "SELECT p.pro_id as sub_pro_id, DATE(bill.bill_date) as log_date, SUM(tag.gross_wt) as gwt
    FROM ret_billing bill
    INNER JOIN ret_bill_details b ON b.bill_id = bill.bill_id
    LEFT JOIN ret_taging tag ON tag.tag_id = b.tag_id
    $common_joins
    WHERE bill.bill_status = 1
        AND bill.bill_type = '13'
        AND bill.bill_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59'
        AND bill.id_branch IN ($id_branch)
        AND c.id_collection NOT IN ($hide_collections)
        $coll_filter $prod_filter
    GROUP BY p.pro_id, DATE(bill.bill_date)";
$st_rows = $this->db->query($sql_sales_trans)->result_array();
$sales_trans_data = [];
foreach ($st_rows as $r) {
    $sales_trans_data[$r['sub_pro_id']][$r['log_date']] = floatval($r['gwt']);
}

// -- 5c. Sales return transfer per day (bill_type=14) → ADDITION --
$sql_sales_ret = "SELECT p.pro_id as sub_pro_id, DATE(bill.bill_date) as log_date, SUM(tag.gross_wt) as gwt
    FROM ret_billing bill
    LEFT JOIN ret_bill_return_details r ON r.bill_id = bill.bill_id
    LEFT JOIN ret_bill_details d ON d.bill_det_id = r.ret_bill_det_id
    LEFT JOIN ret_taging tag ON tag.tag_id = d.tag_id
    $common_joins
    WHERE bill.bill_status = 1
        AND bill.bill_type = '14'
        AND bill.is_cus_sale_return_transfer = '0'
        AND bill.bill_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59'
        AND bill.id_branch IN ($id_branch)
        AND c.id_collection NOT IN ($hide_collections)
        $coll_filter $prod_filter
    GROUP BY p.pro_id, DATE(bill.bill_date)";
$srt_rows = $this->db->query($sql_sales_ret)->result_array();
$sales_ret_data = [];
foreach ($srt_rows as $r) {
    $sales_ret_data[$r['sub_pro_id']][$r['log_date']] = floatval($r['gwt']);
}

// -- 5d. Ecom outward per day (packing list based) → REDUCE --
$sql_ecom = "SELECT prd.pro_id as sub_pro_id, DATE(pk.packing_confirm_date) as log_date, SUM(t.gross_wt) as gwt
    FROM ret_packing_list pk
    LEFT JOIN ret_billing b ON b.bill_id =
        CASE WHEN pk.gen_bill_id IS NULL THEN pk.pack_bill_id ELSE pk.gen_bill_id END
    LEFT JOIN ret_packing_list_details pd ON pd.id_pack_list = pk.pack_list_id
    LEFT JOIN ret_bill_details bd ON bd.bill_id = b.bill_id AND bd.tag_id = pd.pack_tag_id
    LEFT JOIN ret_taging t ON t.tag_id = pd.pack_tag_id
    LEFT JOIN ret_product_master prd ON prd.pro_id = t.product_id
    LEFT JOIN ret_product_master pro ON pro.pro_id = prd.parent_id
    LEFT JOIN ret_collection c ON c.id_collection = pro.collection_id
    LEFT JOIN ret_category cat ON cat.id_ret_category = pro.cat_id
    WHERE b.bill_status = 1
        AND b.bill_type = '1'
        AND pk.packing_list_branch IN ($id_branch)
        AND pk.packing_confirm_date BETWEEN '$from_date' AND '$to_date'
        AND c.id_collection NOT IN ($hide_collections)
        $coll_filter $prod_filter
    GROUP BY prd.pro_id, DATE(pk.packing_confirm_date)";
$ecom_rows = $this->db->query($sql_ecom)->result_array();
$ecom_data = [];
foreach ($ecom_rows as $r) {
    $ecom_data[$r['sub_pro_id']][$r['log_date']] = floatval($r['gwt']);
}
```

---

### Fix 3: Update Day-by-Day Simulation Formula (ret_reports_model.php)

#### Before
```php
// -- 6. Day-by-day simulation in PHP (no more DB queries) --
$sub_results = [];
foreach ($skeleton as $row) {
    $key     = $row['sub_pro_id'];
    $opening = isset($op_data[$key]) ? $op_data[$key] : 0;

    $total_sold = 0;
    $total_closing_accum = 0;
    $curr = $opening;

    $dt_obj = new DateTime($from_date);
    $dt_end = new DateTime($to_date);
    while ($dt_obj <= $dt_end) {
        $ymd    = $dt_obj->format('Y-m-d');
        $in     = isset($inw_data[$key][$ymd])   ? $inw_data[$key][$ymd]   : 0;
        $out    = isset($out_data[$key][$ymd])    ? $out_data[$key][$ymd]   : 0;
        $sale   = isset($sold_data[$key][$ymd])   ? $sold_data[$key][$ymd]  : 0;
        $closing = $curr + $in - $sale - $out;

        $total_sold += $sale;
        $total_closing_accum += $closing;
        $curr = $closing;
        $dt_obj->modify('+1 day');
    }
```

#### After
```php
// -- 6. Day-by-day simulation in PHP (no more DB queries) --
// closing = curr + inward + sales_ret_trans - regular_sold - sales_trans - ecom_out - branch_out
$sub_results = [];
foreach ($skeleton as $row) {
    $key     = $row['sub_pro_id'];
    $opening = isset($op_data[$key]) ? $op_data[$key] : 0;

    $total_sold = 0;
    $total_closing_accum = 0;
    $curr = $opening;

    $dt_obj = new DateTime($from_date);
    $dt_end = new DateTime($to_date);
    while ($dt_obj <= $dt_end) {
        $ymd       = $dt_obj->format('Y-m-d');
        $in        = isset($inw_data[$key][$ymd])         ? $inw_data[$key][$ymd]         : 0;
        $br_out    = isset($out_data[$key][$ymd])         ? $out_data[$key][$ymd]         : 0;
        $sale      = isset($sold_data[$key][$ymd])        ? $sold_data[$key][$ymd]        : 0;
        $s_trans   = isset($sales_trans_data[$key][$ymd]) ? $sales_trans_data[$key][$ymd] : 0;
        $s_ret     = isset($sales_ret_data[$key][$ymd])   ? $sales_ret_data[$key][$ymd]   : 0;
        $ecom_out  = isset($ecom_data[$key][$ymd])        ? $ecom_data[$key][$ymd]        : 0;

        $closing = $curr + $in + $s_ret - $sale - $s_trans - $ecom_out - $br_out;

        $total_sold += ($sale + $s_trans);
        $total_closing_accum += $closing;
        $curr = $closing;
        $dt_obj->modify('+1 day');
    }
```

---

### Fix 4: Company Header in Print/Excel (ret_reports.js)

#### Before (inside `render_sr_detail_tree()`)
```javascript
var rptTitle = 'Stock Rotation Detail (' + grpLabel + ') \u2014 ' + brText + ' | ' + fromDt + ' to ' + toDt + ' | ' + days + ' days';
```

#### After
```javascript
var company_code = $('#company_code').val() || '';
var company_address1 = $('#company_address1').val() || '';
var company_address2 = $('#company_address2').val() || '';
var company_city = $('#company_city').val() || '';
var pincode = $('#company_pincode').val() || $('#pincode').val() || '';
var company_email = $('#company_email').val() || '';
var company_gst_number = $('#company_gst_number').val() || '';
var phone = $('#phone').val() || '';

var rptTitle = `<div style='text-align: center;'><b><span style='font-size:12pt;'>${company_code}</span></b></br>` +
    `<span style='font-size:11pt;'>  ${company_address1}  </span></br>` +
    `<span style='font-size:11pt;'>  ${company_address2}  ${company_city} -  ${pincode}  </span></br>` +
    `<span style='font-size:11pt;'>GSTIN: ${company_gst_number} EMAIL: ${company_email} </span></br>` +
    `<span style='font-size:11pt;'>Contact : ${phone}</span></br>` +
    `<span style='font-size:12pt;'>&nbsp;&nbsp;STOCK ROTATION DETAIL - ${brText} &nbsp;From&nbsp;:&nbsp; ${fromDt} &nbsp;&nbsp;- ${toDt}</span></div>`;
```

---

### Fix 5: Export Only Top-Level Rows (ret_reports.js)

#### Before (both excel and print buttons)
```javascript
rows: function (idx, data) { return true; } // export ALL rows including hidden
```

#### After
```javascript
rows: function (idx, data) { return data._level === 1; } // export only top-level rows
```

This ensures:
- **Collection** group-by → only collection summary rows export
- **Product** group-by → only product summary rows export
- **Category** group-by → only category summary rows export

---

## Verification

1. Navigate to **Stock Rotation Detail Report** (`/admin_ret_reports/stock_rotation_detail/list`)
2. Select **Dindigul** branch, date range **01-03-2025 to 31-03-2025**
3. Click Search — report loads in < 3 seconds (vs 10+ seconds before)
4. **Weight check**: Cross-reference closing weight for any collection against the Stock In/Out report for the same branch/date range — values must match
5. **Print check**: Click Print button — company name, address, GST, email, contact should appear at the top, followed by the report title
6. **Export check**: Click Excel — only level-1 summary rows appear, not drill-down sub-products
7. `php -l application/models/ret_reports_model.php` → No syntax errors

## Notes

### Query Architecture
The report now uses exactly **7 fixed queries** regardless of date range:
1. Skeleton (all sub-products)
2. Opening stock
3. Inward (status 0)
4. Branch outward (status 2,3,4,5,7,9)
5. Regular sold (NOT IN 13,14,15, is_ecom=0)
6. Sales transfer (bill_type=13)
7. Sales return transfer (bill_type=14, via `ret_bill_return_details`)
8. *(plus)* Ecom outward (packing list based)

All daily simulation happens in PHP using the pre-fetched aggregated data — zero additional DB round-trips.

### Simulation Formula
```
closing = curr + inward + sales_ret_transfer
               - regular_sold - sales_transfer - ecom_outward - branch_outward
```

### Related Patterns
- The sales query breakdown mirrors the verified `stock_inout_details_2026_02_12()` function in the same model file (~L5077-5372)
- The company header pattern is identical to `get_district_collection_report()` in `ret_reports.js` (~L45649)
- The `_level` filter in export options is a DataTable standard pattern used with tree-table implementations

### Future Maintenance
- If new movement status codes are added to the system, the outward statuses in query 4 must be updated
- If new bill types are introduced, the `NOT IN` filter in query 5a must be reviewed
- Any new ecom fulfillment flow must be reflected in query 5d
