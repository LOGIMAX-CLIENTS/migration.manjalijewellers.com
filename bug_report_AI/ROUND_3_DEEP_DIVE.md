# 🔬 Round 3 — End-to-End Deep Dive Analysis

> **Audit Date**: 2026-02-17  
> **Method**: Full-stack data-flow analysis (Model ↔ Controller ↔ View ↔ JS)  
> **Scope**: All 112 model methods, controller edit/delete/save paths, view bindings, JS initialization  
> **Files Analysed**: `ret_estimation_model.php` (3,006 lines), `admin_ret_estimation.php` (3,574 lines), `ret_estimation.js` (31,391 lines), `form.php` (~2,517 lines)

---

## Executive Summary

**12 new bugs found** in Round 3, bringing the total to **35 bugs (was 23)**.

Round 3 focused on the model layer, data retrieval queries, and cross-stack data flow. The most critical findings are:

1. **Systemic SQL Injection** — 10+ model methods concatenate raw user input into SQL queries
2. **Cartesian JOIN** in `get_bill_no_format_detail` — produces wrong bill numbers
3. **Tax subquery** returns single-row for all products — wrong tax applied globally
4. **Variable name typo** in `getOrderBySearch` — order PO details silently lost

---

## New Bugs Found

---

### EST-R301 | **P0 — SQL Injection in 10+ Model Methods** (Security)

**Severity**: P0 (Critical)  
**Classification**: Security Vulnerability — SQL Injection  
**Affected File**: `ret_estimation_model.php`

**Description**: At least 10 model methods build SQL queries by directly concatenating user-controlled input without any sanitization, parameterization, or escaping. This exposes the application to classic SQL injection attacks.

**Affected Methods & Lines**:

| Method | Line(s) | Injected Parameter |
|---|---|---|
| `getEstTags` | 145 | `$tag_id` |
| `get_customer` | 154 | `$id_customer` |
| `getNonTagLots` | 164 | `$SearchTxt`, `$id_branch` |
| `ajax_getEstimationList` | 210-212 | `$id_branch`, `$from_date`, `$to_date` |
| `getTaggingBySearch` | 982 | `$SearchTxt`, `$searchField` |
| `getTaggingSearchByCollection` | 1046 | `$data['id_branch']`, `$data['id_tag_mapping']` |
| `getTaggingScanBySearch` | 1123-1125 | `$SearchTxt`, `$searchField`, `$branch`, `$order_no` |
| `getAvailableCustomers` | 903, 911 | `$SearchTxt` |
| `getProductSubDesignBySearch` | 1875 | `$SearchTxt` |
| `get_mc_va_limit` | 1887 | `$id_branch`, `$id_product`, `$id_design`, `$id_sub_design` |
| `get_non_tag_stock_details` | 1969-1973 | `$data['id_section']`, `$data['id_product']`, etc. |

**Risk**: The `$searchField` parameter in `getTaggingBySearch` (line 982) is particularly dangerous because it controls the **column name** in the WHERE clause (`tag.$searchField = '$SearchTxt'`). An attacker who can control this POST parameter can inject arbitrary SQL in the column position, bypassing any value-level escaping.

**Root Cause**: CodeIgniter 2's `$this->db->query()` is used with string concatenation instead of `$this->db->where()` or query bindings (`?` placeholders).

**Proposed Fix**: Replace all raw `$this->db->query("... $var ...")` with either:
- CI query bindings: `$this->db->query("SELECT ... WHERE id = ?", array($id))`
- Active Record: `$this->db->where('id', $id)->get('table')`

---

### EST-R302 | **P1 — Cartesian JOIN in `get_bill_no_format_detail`**

**Severity**: P1 (Major)  
**Affected File**: `ret_estimation_model.php`, line 320  
**Affected Method**: `get_bill_no_format_detail()`

**The Bug**:
```sql
LEFT JOIN ret_billing b ON b.bill_type = b.bill_type
```

This is a **self-referencing join condition** — `b.bill_type = b.bill_type` is always true (except NULL), producing a **cartesian product** between `bill_no_format` and `ret_billing`. Every row in `bill_no_format` joins to **every row** in `ret_billing`.

**Should Be**:
```sql
LEFT JOIN ret_billing b ON bf.bill_type = b.bill_type
```

**Impact**: The `get_bill_no_format_detail()` method is called for every estimation in the list. The cartesian product means:
1. **Performance**: Query returns `N × M` rows instead of `N` (where M = total billing records)
2. **Wrong Data**: `row_array()` returns the first matching row arbitrarily — bill numbers may display incorrectly
3. **The method is called in a loop** (`ajax_getEstimationList`, line 230), multiplying the performance impact

---

### EST-R303 | **P1 — Tax Subquery Missing GROUP BY**

**Severity**: P1 (Major)  
**Affected File**: `ret_estimation_model.php`, lines 967-971 (also 1031-1035, 1108-1112)  
**Affected Methods**: `getTaggingBySearch()`, `getTaggingSearchByCollection()`, `getTaggingScanBySearch()`

**The Bug**:
```sql
LEFT JOIN (select i.tgi_taxcode, i.tgi_tgrpcode,
    GROUP_CONCAT(m.tax_percentage) as tax_percentage,
    GROUP_CONCAT(i.tgi_calculation) as tgi_calculation
    FROM ret_taxgroupitems i
    LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax
    on tax.tgi_tgrpcode=pro.tgrp_id
```

This subquery uses `GROUP_CONCAT` but has **no GROUP BY clause**. In MySQL, this collapses the entire `ret_taxgroupitems` table into a **single row**, so:
- `tax_percentage` = concatenation of ALL tax percentages across ALL tax groups
- `tgi_calculation` = concatenation of ALL calculation types
- The outer `ON tax.tgi_tgrpcode=pro.tgrp_id` matches against only ONE `tgi_tgrpcode` value (non-deterministic)

**Should Be**:
```sql
LEFT JOIN (select i.tgi_tgrpcode,
    GROUP_CONCAT(m.tax_percentage) as tax_percentage,
    GROUP_CONCAT(i.tgi_calculation) as tgi_calculation
    FROM ret_taxgroupitems i
    LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode
    GROUP BY i.tgi_tgrpcode) as tax
    on tax.tgi_tgrpcode=pro.tgrp_id
```

**Impact**: Tax percentages displayed for tags when searching are potentially wrong. However, the actual save uses separate tax fetching, so this primarily affects the UI display during estimation creation. The bug is **repeated in 3 methods**.

---

### EST-R304 | **P1 — Variable Typo in `getOrderBySearch` — PO Details Lost**

**Severity**: P1 (Major)  
**Affected File**: `ret_estimation_model.php`, line 1511  

**The Bug**:
```php
$return_data[$rkey]['mc_va_limit']   = $this->get_mc_va_limit(...);  // correct array
$returndata[$rkey]['po_details'] = $this->get_purchase_details(...);  // WRONG array name!
$return_data[$rkey]['charges_details'] = $this->get_charges(...);     // correct array
```

Line 1511 writes to `$returndata` (no underscore) instead of `$return_data` (with underscore). This creates a new, unused variable. The purchase order details are silently lost and never returned to the caller.

**Impact**: When linking an order to an estimation, the PO cost/wastage details used for purchase price comparison are missing. This could lead to incorrect margin calculations.

---

### EST-R305 | **P1 — `getTaggingSearchByCollection` Overwrites Input Parameter**

**Severity**: P1 (Major)  
**Affected File**: `ret_estimation_model.php`, lines 1002, 1048  

**The Bug**:
```php
function getTaggingSearchByCollection($data)   // $data = input array
{
    $data = $this->db->query("SELECT ...");    // $data overwritten with query result!
    ...
    $returndata = $data->result_array();       // Now using query result
}
```

The method parameter `$data` (containing `id_branch` and `id_tag_mapping`) is overwritten by the DB query result object on line 1004/1048. The `$data->result_array()` at line 1048 works because `$data` is now the query result, but **the original input parameters are destroyed**.

This works "by accident" because:
1. The SQL query uses `$data['id_branch']` and `$data['id_tag_mapping']` (from the original input) **before** the reassignment
2. After the query, `$data` becomes the CI_DB_result object, and `->result_array()` happens to work

**Risk**: The code is fragile. Any future change that accesses `$data['id_branch']` after line 1004 will fail silently or with an error.

---

### EST-R306 | **P2 — Uninitialized `$dateofbirth`/`$dateofwed` in `updateCustomer`**

**Severity**: P2 (Minor)  
**Affected File**: `ret_estimation_model.php`, lines 832-855

**The Bug**:
```php
if($date_of_birth!=''){
    $d1 = date_create($date_of_birth);
    $dateofbirth = date_format($d1,"Y-m-d");
}
// If $date_of_birth IS empty, $dateofbirth is NEVER initialized
...
'date_of_birth' => $this->isEmptySetDefault($dateofbirth, NULL),  // Undefined variable!
```

If `$date_of_birth` is empty, `$dateofbirth` is never defined, causing a PHP Notice for undefined variable. The `isEmptySetDefault()` call will receive `NULL` due to PHP's default for undefined variables, which happens to be the desired behavior — but relying on undefined variable behavior generates E_NOTICE warnings.

The same issue exists for `$dateofwed` (lines 836-838, 855).

**Note**: The `createNewCustomer` method (L779-806) has the **exact same bug** — `$dateofbirth` and `$dateofwed` are only set inside conditionals.

---

### EST-R307 | **P2 — Base64 "Encryption" Used as Password Hash**

**Severity**: P2 (Minor — Security Concern)  
**Affected File**: `ret_estimation_model.php`, lines 771-774, 793, 845

**The Bug**:
```php
public function encrypt($str) {
    return base64_encode($str);    // Not encryption, trivially reversible
}
// Used as:
"passwd" => $this->encrypt($cusmobile),  // Password = base64(mobile_number)
```

Customer passwords are stored as `base64_encode(mobile_number)`, which is:
1. **Not encryption** — base64 is a reversible encoding, not a hash
2. **Predictable** — the password is the mobile number itself, just encoded
3. **No salt** — identical mobile numbers produce identical "passwords"

Anyone with database read access can decode any customer's password by running `base64_decode()`.

---

### EST-R308 | **P1 — Cartesian JOIN in `getCompanyDetails`**

**Severity**: P1 (Major)  
**Affected File**: `ret_estimation_model.php`, lines 1934, 1944

**The Bug**:
```sql
-- Branch path (line 1941-1948):
from branch b
join company c                        -- No ON condition!
left join country cy on (b.id_country=cy.id_country)
...

-- Company path (line 1928-1937):
from company c
join chit_settings cs                 -- No ON condition!
left join country cy on (c.id_country=cy.id_country)
...
```

Both query branches use `JOIN` without an `ON` condition, producing a cartesian product. If there are 3 branches and 1 company, the branch query returns 3 rows instead of 1. Since `row_array()` is used (line 1950), only the first arbitrarily-matched row is returned.

**Impact**: Currently masked because most deployments have exactly 1 company row. Would break badly in a multi-company setup.

---

### EST-R309 | **P2 — Incomplete WHERE in `get_chit_details`**

**Severity**: P2 (Minor)  
**Affected File**: `ret_estimation_model.php`, line 2016

**The Bug**:
```sql
WHERE sa.id_scheme_account and sa.is_closed = 1 and sa.is_utilized = 0
```

`sa.id_scheme_account` has **no comparison operator**. It evaluates as a truthy check: `WHERE (sa.id_scheme_account != 0) AND ...`. This works when all IDs are positive integers, but:
1. It's clearly a mistake — the intent was likely `WHERE sa.id_scheme_account = rn.scheme_account_id` or `WHERE sa.id_scheme_account IS NOT NULL`
2. The JOIN already links `sa.id_scheme_account = rn.scheme_account_id`, making the WHERE clause redundant (but misleading)

---

### EST-R310 | **P2 — Null Dereference in `getOldMetalRate`**

**Severity**: P2 (Minor)  
**Affected File**: `ret_estimation_model.php`, line 690

**The Bug**:
```php
return $sql->row()->goldrate_24ct;
```

If the query returns no rows, `$sql->row()` returns `NULL`, and accessing `->goldrate_24ct` on NULL throws a fatal error: "Trying to get property of non-object."

**Trigger**: When an old metal type has no corresponding rate in `ret_old_metal_rate`, or when `id_old_metal_type` is invalid.

---

### EST-R311 | **P2 — Edit Case Missing Access Control**

**Severity**: P2 (Minor — Security Gap)  
**Affected File**: `admin_ret_estimation.php`, lines 1351–1421

**The Bug**: The `edit` case loads the form with pre-populated data but does **not** set `$data['access']`, unlike the `add` case which sets:
```php
$data['access'] = $this->$SETT_MOD->get_access('admin_ret_estimation/estimation/add');
```

The `edit` case completely omits this. If the view uses `$data['access']` to control UI elements (e.g., disabling save buttons, hiding fields), the edit form may show/hide controls incorrectly because `$data['access']` is undefined.

---

### EST-R312 | **P3 — Duplicate Function Call in JS Init**

**Severity**: P3 (Cosmetic)  
**Affected File**: `ret_estimation.js`, lines 236-237

**The Bug**:
```javascript
getStoneRateSettings();
getStoneRateSettings();    // Called twice!
```

The `edit` case calls `getStoneRateSettings()` twice consecutively, making a redundant AJAX request.

---

## Cross-Cutting Concerns

### Data-Flow Gaps: Edit Path

The `edit` controller case (L1351) and `est_edit` AJAX case (L1425) load different sets of data:

| Data | `edit` (HTML form) | `est_edit` (AJAX/JSON) |
|---|---|---|
| `tag_details` | ❌ Not loaded | ✅ Loaded |
| `non_tag_details` | ❌ Not loaded | ✅ Loaded |
| `est_home_bill` | ❌ Not loaded | ✅ Loaded |
| `chit_details` | ❌ Not loaded | ✅ Loaded |
| `old_metal` | ❌ Not loaded | ✅ Loaded |
| `access` | ❌ Not loaded | N/A |

The `edit` case relies on the JS to make a secondary AJAX call to `est_edit` to fetch the remaining data. This is by design (SPA-like pattern), but the **edit HTML case doesn't validate** that the estimation ID exists before rendering the page — a non-existent ID would render an empty form without error.

### `getOtherEstimateItemsDetails` — N+1 Query Pattern

The `getOtherEstimateItemsDetails` method (L430-682) has severe N+1 query problems:
- For each item row, it calls: `get_stone_details()`, `get_est_stone_wt()`, `get_tag_stone_wt()`, `get_est_other_metal_details()`, `get_other_material_details()`, `get_other_estcharges()` — that's **6 sub-queries per item**
- For each old metal row: `getOldMetalRate()`, `get_old_metal_stone_details()`, `get_old_metal_type()`, `get_old_metal_category()` — **4 sub-queries per old metal**
- For each material row: `get_stone_details()`, `get_other_material_details()` — **2 sub-queries per material**

An estimation with 10 items + 3 old metals + 2 materials = **10×6 + 3×4 + 2×2 = 76 queries** from a single method call.

---

## Summary Table

| ID | Severity | Title | Fix Readiness |
|---|---|---|---|
| EST-R301 | **P0** | SQL Injection in 10+ model methods | ⚠️ Needs systematic refactor |
| EST-R302 | **P1** | Cartesian JOIN in `get_bill_no_format_detail` | ✅ Ready (1 char fix) |
| EST-R303 | **P1** | Tax subquery missing GROUP BY (×3 methods) | ✅ Ready |
| EST-R304 | **P1** | `$returndata` typo — PO details lost | ✅ Ready (1 char fix) |
| EST-R305 | **P1** | `$data` parameter overwritten in collection search | ✅ Ready |
| EST-R306 | P2 | Uninitialized `$dateofbirth`/`$dateofwed` | ✅ Ready |
| EST-R307 | P2 | Base64 "encryption" used as password hash | ⚠️ Needs migration plan |
| EST-R308 | **P1** | Cartesian JOIN in `getCompanyDetails` | ✅ Ready |
| EST-R309 | P2 | Incomplete WHERE in `get_chit_details` | ✅ Ready |
| EST-R310 | P2 | Null dereference in `getOldMetalRate` | ✅ Ready |
| EST-R311 | P2 | Edit case missing access control | ✅ Ready |
| EST-R312 | P3 | Duplicate `getStoneRateSettings()` call | ✅ Ready |

---

## Total Bug Count (All Rounds)

| Round | Bugs | P0 | P1 | P2 | P3 |
|---|---|---|---|---|---|
| Round 1 (Code) | 16 | 2 | 5 | 7 | 2 |
| Round 2 (Schema) | 7 | 1 | 3 | 3 | 0 |
| Round 3 (Deep Dive) | 12 | 1 | 5 | 5 | 1 |
| **Total** | **35** | **4** | **13** | **15** | **3** |
