# ANTI-PATTERNS REGISTER — Retail Dashboard
> Generated: 2026-03-16 | Module: Retail Dashboard | Builder: Antigravity

This document catalogs all recurring anti-patterns found during the 8-round brain build. Use it to guide refactoring and prevent re-introduction of the same patterns during bug fixes.

---

## AP-01: Raw SQL String Interpolation (SQL Injection Vector)

**Severity:** 🔴 CRITICAL  
**Occurrences:** All filter parameters in BOTH models (~136 methods affected)  
**Pattern:**
```php
// ❌ Anti-pattern — direct interpolation
$sql = "SELECT * FROM ret_billing WHERE id_branch='{$id_branch}' AND date(bill_date) BETWEEN '{$from_date}' AND '{$to_date}'";
$query = $this->db->query($sql);
```
**Problem:** Any `$id_branch`, `$from_date`, `$to_date` value from POST input can inject arbitrary SQL.  
**Fix:**
```php
// ✅ Use CI query builder or prepared statements
$this->db->where('id_branch', $id_branch);
$this->db->where('bill_date >=', $from_date);
$this->db->where('bill_date <=', $to_date);
$query = $this->db->get('ret_billing');
// OR: use $this->db->query($sql, [$id_branch, $from_date, $to_date]);
```
**Files:** `ret_dashboard_model.php`, `ret_dashboard_api_model.php` — entire file scope  
**Bugs:** #1 (primary model), all API model methods

---

## AP-02: Using `$_POST` Directly in Controller/Model

**Severity:** 🟡 MEDIUM  
**Occurrences:** 2 confirmed places  
**Pattern:**
```php
// ❌ Anti-pattern (in controller)
$from_date = $_POST['from_date'];

// ❌ Anti-pattern (in model — should never happen)
$id_branch = $this->input->post('id_branch'); // called from inside model layer
```
**Problem:** Bypasses CodeIgniter's Input class sanitization and XSS protection. Models should NEVER read input — data must be injected via controller parameters.  
**Fix:**
```php
// ✅ In controller:
$from_date = $this->input->post('from_date');
// ✅ Pass to model as parameter:
$this->model->method($from_date, $to_date, $id_branch);
```
**Files:**  
- `admin_ret_dashboard.php` L431 — `get_customerOrderDetails()` uses `$_POST['from_date']`  
- `ret_dashboard_model.php` L1695 — `karigar_orders()` calls `$this->input->post()` from inside model  
**Bugs:** #10, #11

---

## AP-03: Controller Method Receives Parameters But Passes None to Model

**Severity:** 🔴 CRITICAL  
**Occurrences:** 2 confirmed places  
**Pattern:**
```php
public function get_branch_transfer_details() {
    $from_date = $this->input->post('from_date');
    $id_branch = $this->input->post('id_branch');
    $result = $this->model->get_branch_transfer_details(); // ❌ no args passed!
    echo json_encode($result);
}
```
**Problem:** Filter parameters collected from POST but silently dropped before calling the model. Caller always gets all-data response regardless of selected filters.  
**Fix:** Pass all collected parameters through to the model call.  
**Files:**  
- `admin_ret_dashboard.php` L1241 — `get_branch_transfer_details()`  
- `admin_ret_dashboard.php` L469 — `get_CustomerDetails()` model ignores `$id_branch`  
**Bugs:** #2, #3

---

## AP-04: Undefined Variable Used in SQL WHERE Clause

**Severity:** 🔴 CRITICAL  
**Occurrences:** 2 confirmed places  
**Pattern:**
```php
// Function signature: get_branch_wastage($from_date, $to_date, $id_branch, $group_by)
// ❌ $id_metal is never declared, but used in WHERE:
WHERE id_metal='{$id_metal}'  // PHP notice: Undefined variable → empty string → broken filter
```
**Problem:** Undefined variable evaluates to empty/null. SQL filter clause becomes effectively `WHERE id_metal=''` — either matches nothing or matches everything depending on column type.  
**Fix:** Add missing parameter to function signature.  
**Files:**  
- `ret_dashboard_api_model.php` L666 — `get_branch_wastage()` missing `$id_metal`  
- `ret_dashboard_api_model.php` L2223 — `get_accountstock_inwards_details()` uses `$id_category` + `$data` (both undefined)  
**Bugs:** #21, #22

---

## AP-05: Wrong Date Bound in Range Filter

**Severity:** 🔴 CRITICAL  
**Occurrences:** 1 confirmed  
**Pattern:**
```php
// Accepts TWO date params but uses from_date as upper bound:
// function signature: get_rate_cut_profit_loss($from_date, $to_date, ...)
WHERE DATE(src.date_add) <= '{$from_date}'  // ❌ should be $to_date
// $to_date is accepted but never used anywhere in the query
```
**Problem:** Date range query selects records UP TO `from_date` instead of between `from_date` and `to_date`. Shows data for dates BEFORE the start of the range.  
**Fix:**
```php
WHERE DATE(src.date_add) BETWEEN '{$from_date}' AND '{$to_date}'
```
**File:** `ret_dashboard_api_model.php` L2632  
**Bug:** #23

---

## AP-06: Identical SQL Branch for "New" vs "Old" Classification Logic

**Severity:** 🟡 MEDIUM  
**Occurrences:** 1 confirmed  
**Pattern:**
```php
// ❌ Both "new customer" AND "old customer" queries use identical WHERE clause:
// Query 1 (new): ... AND NOT EXISTS(SELECT 1 FROM ret_billing WHERE bill_cus_id=b.bill_cus_id AND bill_date < '{$from_date}')
// Query 2 (old): ... AND NOT EXISTS(SELECT 1 FROM ret_billing WHERE bill_cus_id=b.bill_cus_id AND bill_date < '{$from_date}')
// Should be EXISTS (not NOT EXISTS) for the old customer query
```
**Problem:** All customers are classified as "new" because the "old" query uses the same exclusion logic. Old customer count always equals new customer count.  
**Fix:** Old customer query should use `EXISTS(...)` (without NOT).  
**File:** `ret_dashboard_model.php` L411-451 — `get_dashboard_bills_clasfications()`  
**Bug:** #8

---

## AP-07: N+1 Query Pattern in Loop

**Severity:** 🟡 MEDIUM  
**Occurrences:** 1 confirmed  
**Pattern:**
```php
// ❌ Anti-pattern: query inside nested loop
foreach ($months as $month) {
    foreach ($branches as $branch) {
        $query = $this->db->query("SELECT ... WHERE MONTH(bill_date)={$month} AND id_branch={$branch}");
        // → 12 × N queries for N branches
    }
}
```
**Problem:** For 5 branches: 60 individual SQL queries per single HTTP request. Grows linearly with branch count.  
**Fix:**
```php
// ✅ Single query with GROUP BY:
SELECT id_branch, MONTH(bill_date) as month, SUM(net_wt) as total_wt
FROM ret_billing
WHERE bill_date BETWEEN '{$from}' AND '{$to}' AND bill_status=1
  AND id_branch IN ({$branch_ids})
GROUP BY id_branch, MONTH(bill_date)
```
**File:** `ret_dashboard_api_model.php` L377-457 — `get_monthly_sales()`  
**Bug:** #24

---

## AP-08: Duplicate Array Key Silently Overwrites Data

**Severity:** 🟡 MEDIUM  
**Occurrences:** 2 confirmed places  
**Pattern:**
```php
$result[] = [
    'available_gwt' => $gwt_formula,
    'available_nwt' => $nwt_formula,
    'available_gwt' => $nwt_formula,  // ❌ second definition of same key — overwrites first!
];
```
**Problem:** PHP silently uses the last value for a duplicate key. Callers receive `nwt` value in a field named `gwt` — stock weight totals are permanently wrong. No error is thrown.  
**Fix:** Rename the duplicated key to its correct name (e.g., `available_nwt`).  
**Files:**  
- `ret_dashboard_model.php` L3484-3486 — `get_stock_category_details()`  
- `ret_dashboard_model.php` L4318-4320 — `get_branch_stock_details()`  
**Bug:** #13

---

## AP-09: Dead Model Method Never Called (Zombie Code)

**Severity:** 🟢 LOW  
**Occurrences:** 1 confirmed (primary model), 11 in API model  
**Pattern:**
```php
// ❌ 624-line method exists in model but controller bypasses it entirely:
public function get_dashboard_cash_abstarct_details() {
    // ... 624 lines of SQL ...
}
// Controller at L1702 instead calls: $this->ret_reports_model->getBillDetails($_POST)
```
**Problem:** Dead code creates maintenance confusion, inflates model size, and wastes brain space during reviews. Fixes applied to the dead method are silently ignored.  
**Fix:** Remove dead method or convert to a comment stub referencing the replacement.  
**Zombie methods:**  
- Primary model: `get_dashboard_cash_abstarct_details()` (L672-1295)  
- API model: `get_branch_sales()`, `get_custome_wise_sale()`, `get_dashboard_estimation()`, `get_dashboard_virturaltag_details()`, `get_dashboard_salesreturn_det()`, `get_dashboard_lot_tag_details()`, `get_cover_up_report()`, `get_purchase_inwards()`, `get_dashboard_breakeven_details()`, `get_rate_cut_details()`, `get_outward_details()`  
**Bug:** #9

---

## AP-10: Cross-Model Dependency (Controller Loads External Module's Model)

**Severity:** 🟡 MEDIUM  
**Occurrences:** 1 confirmed  
**Pattern:**
```php
// ❌ Dashboard controller directly loads Reports module model:
$this->load->model('ret_reports_model');
$data = $this->ret_reports_model->getBillDetails($_POST);
```
**Problem:** Creates tight coupling between Dashboard and Reports modules. If `ret_reports_model::getBillDetails()` changes its return structure, the Dashboard cash abstract silently produces wrong totals. The called method `getLedgerReportData()` does not even exist → Fatal PHP error.  
**Fix:** Dashboard should have its own data aggregation method, OR the dependency should be documented in a service layer contract.  
**File:** `admin_ret_dashboard.php` L1706, L2112  
**Bug:** #5 (fatal), #9 (structural)

---

## AP-11: Missing Content-Type Header on JSON Responses

**Severity:** 🟢 LOW  
**Occurrences:** All controller AJAX methods (~38 methods)  
**Pattern:**
```php
// ❌ Direct echo without headers:
echo json_encode($response);
```
**Problem:** Browsers may misinterpret response type. Some older IE versions and security proxies reject JSON without explicit `Content-Type: application/json`. Makes API testing harder.  
**Fix:**
```php
// ✅ Set header before output:
$this->output->set_content_type('application/json');
echo json_encode($response);
// OR use CI's response helper
```
**File:** `admin_ret_dashboard.php` — all AJAX controller methods  
**Bug:** #17

---

## AP-12: COLOUR_CODE Constant Defined in 3 Separate Files (3-Way Sync Risk)

**Severity:** 🟢 LOW  
**Occurrences:** 3 places  
**Pattern:**
```php
// In admin_ret_dashboard.php:
define('COLOUR_CODE', ['Gold' => '#D4AF37', 'Silver' => '#C0C0C0', ...]);

// In admin_ret_dashboard_api.php:
define('COLOUR_CODE', ['Gold' => '#D4AF37', 'Silver' => '#C0C0C0', ...]);

// In ret_dashboard.js:
var COLOUR_CODE = {Gold: '#D4AF37', Silver: '#C0C0C0', ...};
```
**Problem:** Any change to the color palette must be made in **all 3 locations**. One missed update produces inconsistent colors across dashboard tabs.  
**Fix:** Centralize in one PHP location and inject into JS via a settings endpoint, OR load via a shared CSS variable.  
**Bug:** #18

---

## AP-13: Legacy Mega-Method That Partly Bypasses Route Architecture

**Severity:** 🟡 MEDIUM  
**Occurrences:** 1 method (`get_retail_dashboard_details()` L539-623)  
**Pattern:**
```php
// ❌ One method calls 15+ model methods, returns bulk JSON:
public function get_retail_dashboard_details() {
    $data['estimation'] = $this->model->get_estimation(...);
    $data['billings'] = $this->model->get_billing(...);
    // ... 15+ more model calls
    echo json_encode($data);
}
```
**Problem:** Superseded by 38 individual AJAX endpoints but still active and callable. Branch filter is partially applied (only some sub-calls pass it). Format is inconsistent with modern endpoints. Passing `$from_date` unformatted to model.  
**Fix:** Deprecate by removing the route or returning 410 Gone. Do not fix bugs in this method — use individual endpoints instead.  
**File:** `admin_ret_dashboard.php` L539  
**Bugs:** #6, #19

---

## Summary Table

| AP ID | Pattern | Severity | Bug(s) | Effort to Fix |
|---|---|---|---|---|
| AP-01 | Raw SQL interpolation | 🔴 CRITICAL | #1 | HIGH — all model methods |
| AP-02 | `$_POST` in controller/model | 🟡 MEDIUM | #10, #11 | LOW |
| AP-03 | Params collected but not passed | 🔴 CRITICAL | #2, #3 | LOW — add args |
| AP-04 | Undefined variable in SQL | 🔴 CRITICAL | #21, #22 | LOW — add params |
| AP-05 | Wrong date bound | 🔴 CRITICAL | #23 | LOW — fix WHERE |
| AP-06 | Identical SQL for distinct logic | 🟡 MEDIUM | #8 | LOW — change EXISTS |
| AP-07 | N+1 query in loop | 🟡 MEDIUM | #24 | MEDIUM — rewrite |
| AP-08 | Duplicate array key | 🟡 MEDIUM | #13 | LOW — rename key |
| AP-09 | Zombie/dead code | 🟢 LOW | #9 | LOW — remove |
| AP-10 | Cross-model tight coupling | 🟡 MEDIUM | #5, #9 | HIGH — structural |
| AP-11 | No Content-Type header | 🟢 LOW | #17 | LOW |
| AP-12 | 3-way constant sync | 🟢 LOW | #18 | MEDIUM — centralize |
| AP-13 | Legacy mega-method still active | 🟡 MEDIUM | #6, #19 | LOW — deprecate |
