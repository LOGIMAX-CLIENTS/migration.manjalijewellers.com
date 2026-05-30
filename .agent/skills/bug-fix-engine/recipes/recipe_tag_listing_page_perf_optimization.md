# Tag Listing Page — Performance Optimization & Filter Enhancement

## Metadata
- **Pattern ID**: PAT-PERF-012
- **Severity**: HIGH
- **Modules Affected**: Tagging (Tag Listing page)
- **Auto-fixable**: No (multi-file, multi-layer changes)

## Client Scope
- **Applies to**: ALL
- **Reason**: Universal pattern — any client with 10K+ tags will hit this performance wall

## Created By
- **Developer**: AI Agent (Antigravity)
- **Client**: Manepally (erp.manepally.com)
- **Date**: 2026-05-18
- **Source Bug ID**: N/A

## Symptom
1. Tag Listing page takes 30-60 seconds to open — browser shows loading spinner indefinitely
2. Lot No and PO Ref No dropdowns pre-load 51,000+ records on every page init
3. When user selects a wide date range (e.g., 1 year), the page freezes/becomes unresponsive after data loads
4. Custom date range selection is ignored — search always uses "This Month" dates
5. PO Ref No dropdown shows duplicate entries for the same reference number

## Root Cause
**5 independent performance issues compounding:**

1. **Dropdown pre-load**: `getTaggedLot()` and `getTaggedRefNo()` loaded ALL records on page init without filtering or pagination. With 30K lots + 21K PO refs = 51K JSON rows transmitted on every page open.

2. **Inefficient PO query**: `getTaggedRefNo()` joined through 3 tables (`ret_taging → ret_lot_inwards → ret_purchase_order`) instead of querying `ret_purchase_order` directly. `GROUP BY t.tag_lot_id` caused duplicate PO ref entries.

3. **Index-killing date function**: `date(t.tag_datetime) BETWEEN ...` wraps every row in a `date()` function call, preventing MySQL from using any index on `tag_datetime` — forces full table scan on 50K+ rows.

4. **O(n²) footer callback**: `footerCallback` had a `for(i=0; i<=data.length; i++)` loop wrapping 6 `column().data().reduce()` calls. Each reduce already processes all rows, so the loop caused n × 6 × n = ~255M operations for 6,516 rows.

5. **No deferred rendering**: DataTables created DOM nodes for ALL rows upfront instead of only the visible page (10 rows).

**Bonus bug — daterangepicker callback**: Hardcoded `moment().startOf('month')` / `moment().endOf('month')` instead of using the `start`/`end` parameters, causing the search to always send "This Month" dates.

## Detection
```command
REM Check for pre-loading dropdown functions without search parameter
grep -n "function getTaggedLot\b" admin/application/models/ret_tag_model.php
grep -n "function getTaggedRefNo\b" admin/application/models/ret_tag_model.php

REM Check for date() function wrapping datetime columns (index killer)
grep -n "date(t.tag_datetime)" admin/application/models/ret_tag_model.php

REM Check for O(n²) footer loop pattern
grep -n "for (var i = 0; i <= data.length" admin/assets/js/ret_tagging.js

REM Check for missing deferRender
grep -n "deferRender" admin/assets/js/ret_tagging.js
```

## Files
- `admin/assets/js/ret_tagging.js`
- `admin/application/controllers/admin_ret_tagging.php`
- `admin/application/models/ret_tag_model.php`

## Fix

### Fix 1: Convert dropdowns to AJAX Select2 (ret_tagging.js)

#### Before
```javascript
// Lot No — pre-loads ALL lots
$('#tag_lot_no').select2();
getTaggedLot(); // loads 30K+ rows

// PO Ref No — pre-loads ALL POs
$('#tag_po_ref_no').select2();
getTaggedRefNo(); // loads 21K+ rows
```

#### After
```javascript
// Lot No — AJAX lazy load
$('#tag_lot_no').select2({
    minimumInputLength: 1,
    ajax: {
        url: base_url + 'index.php/admin_ret_tagging/getTaggedLot',
        dataType: 'json',
        delay: 250,
        data: function(params) { return { search: params.term }; },
        processResults: function(data) { return { results: data }; },
        cache: true
    },
    placeholder: 'Select Lot No',
    allowClear: true
});

// PO Ref No — AJAX lazy load
$('#tag_po_ref_no').select2({
    minimumInputLength: 1,
    ajax: {
        url: base_url + 'index.php/admin_ret_tagging/getTaggedRefNo',
        dataType: 'json',
        delay: 250,
        data: function(params) { return { search: params.term }; },
        processResults: function(data) { return { results: data }; },
        cache: true
    },
    placeholder: 'Select Po RefNo',
    allowClear: true
});
```

### Fix 2: Daterangepicker callback + clear filters (ret_tagging.js)

#### Before
```javascript
function (start, end) {
$('#tag_date1').text(start.format('YYYY-MM-DD'));
$('#tag_date2').text(end.format('YYYY-MM-DD'));
get_tagging_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
}
```

#### After
```javascript
function (start, end) {
$('#tag_date1').text(start.format('YYYY-MM-DD'));
$('#tag_date2').text(end.format('YYYY-MM-DD'));
$('#tag_lot_no').val(null).trigger('change');
$('#tag_po_ref_no').val(null).trigger('change');
get_tagging_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
}
```

### Fix 3: Add deferRender + remove O(n²) loop (ret_tagging.js)

#### Before
```javascript
// Missing deferRender
"aaData": data,

// O(n²) footer loop
var api = this.api(), data;
for (var i = 0; i <= data.length - 1; i++)
{
    var intVal = function (i) { ... };
    // 6 column reduce() calls here — each processes ALL rows
    // This runs data.length times = n × 6 × n operations
}
```

#### After
```javascript
// Add deferRender
"deferRender": true,
"aaData": data,

// Remove loop — reduce() already processes all rows in one call
var api = this.api(), data;
var intVal = function (i) { ... };
// 6 column reduce() calls — runs ONCE
```

### Fix 4: Model — AJAX search with LIMIT (ret_tag_model.php)

#### Before (getTaggedLot)
```php
function getTaggedLot()
{
    $sql = $this->db->query("SELECT l.lot_no as id, l.lot_no as text
    FROM ret_taging t
    LEFT JOIN ret_lot_inwards l on l.lot_no = t.tag_lot_id
    WHERE t.tag_lot_id is not null
    GROUP BY t.tag_lot_id
    ORDER BY l.lot_no DESC");
    return $sql->result_array();
}
```

#### After (getTaggedLot)
```php
function getTaggedLot($search='')
{
    if($search == '') {
        return array();
    }
    $search_filter = " AND l.lot_no LIKE '%".$this->db->escape_like_str($search)."%'";
    $sql = $this->db->query("SELECT l.lot_no as id, l.lot_no as text
    FROM ret_taging t
    LEFT JOIN ret_lot_inwards l on l.lot_no = t.tag_lot_id
    WHERE t.tag_lot_id is not null".$search_filter."
    GROUP BY t.tag_lot_id
    ORDER BY l.lot_no DESC
    LIMIT 30");
    return $sql->result_array();
}
```

#### Before (getTaggedRefNo)
```php
function getTaggedRefNo()
{
    $sql = $this->db->query("SELECT po.po_id, po.po_ref_no
    FROM ret_taging t
    LEFT JOIN ret_lot_inwards l on l.lot_no = t.tag_lot_id
    LEFT JOIN ret_purchase_order po on po.po_id = l.po_id
    WHERE t.tag_lot_id is not null and l.po_id is not null
    GROUP BY t.tag_lot_id
    ORDER BY l.lot_no DESC");
    return $sql->result_array();
}
```

#### After (getTaggedRefNo)
```php
function getTaggedRefNo($search='')
{
    if($search == '') {
        return array();
    }
    $sql = $this->db->query("SELECT po.po_ref_no as id, po.po_ref_no as text
    FROM ret_purchase_order po
    WHERE po.po_ref_no LIKE '%".$this->db->escape_like_str($search)."%'
    GROUP BY po.po_ref_no
    ORDER BY po.po_ref_no ASC
    LIMIT 30");
    return $sql->result_array();
}
```

### Fix 5: Index-friendly date filter + OR logic + independent filters (ret_tag_model.php)

#### Before
```php
where (t.tag_status!=2)".($from_date!='' && $to_date!='' ? " and (date(t.tag_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')":'')."

".($lot_no!="" && $lot_no > 0 ? "and lot_in.lot_no=".$lot_no."":"")."

".($po_refno!="" && $po_refno > 0 ? "and po.po_id=".$po_refno."":"")."
```

#### After
```php
where (t.tag_status!=2)".($from_date!='' && $to_date!='' && $lot_no=='' && $po_refno=='' ? " and (t.tag_datetime BETWEEN '".date('Y-m-d',strtotime($from_date))." 00:00:00' AND '".date('Y-m-d',strtotime($to_date))." 23:59:59')":'')."

".( ($lot_no!="" && $lot_no > 0) && $po_refno!="" ? "and (lot_in.lot_no=".$lot_no." OR po.po_ref_no='".$this->db->escape_str($po_refno)."')" : ( ($lot_no!="" && $lot_no > 0 ? "and lot_in.lot_no=".$lot_no."":"") . ($po_refno!="" ? "and po.po_ref_no='".$this->db->escape_str($po_refno)."'":"") ) )."
```

### Fix 6: Database index
```sql
ALTER TABLE ret_taging ADD INDEX idx_tag_datetime (tag_datetime);
```

## Verification
1. Open Tag Listing page — should load instantly (no pre-load delay)
2. Type "386" in Lot No dropdown — matching lots appear within 300ms
3. Type "P-00" in PO Ref No dropdown — unique PO refs appear (no duplicates)
4. Select Lot No, click Search — shows all data for that lot regardless of date range
5. Select PO Ref No, click Search — shows all data for that PO regardless of date range
6. Select both Lot + PO, click Search — shows results matching either filter
7. Change date range — Lot and PO dropdowns automatically clear
8. Select 1-year date range — page loads without freezing, table renders instantly
9. Footer totals are correct across all scenarios
10. Pagination, Excel export, Print all work correctly

## Notes
- The O(n²) footer callback is the most impactful fix — it's a common pattern across many DataTable pages. Search for `for (var i = 0; i <= data.length` in other JS files to find similar issues.
- The `date()` function wrapper on datetime columns is another common anti-pattern. Any query using `date(column) BETWEEN` should be refactored to `column BETWEEN 'date 00:00:00' AND 'date 23:59:59'` for index usage.
- PO ref numbers reset yearly, so filtering by `po_ref_no` text (not `po_id`) ensures all years' tags appear for the same reference.
