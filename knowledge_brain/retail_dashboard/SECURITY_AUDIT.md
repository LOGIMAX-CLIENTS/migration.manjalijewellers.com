# SECURITY AUDIT — Retail Dashboard
> Generated: 2026-03-16 | Module: Retail Dashboard | Round: 10
> Based on full deep read of all 4 files: `admin_ret_dashboard.php` (2147L), `admin_ret_dashboard_api.php` (1998L), `ret_dashboard_model.php` (5271L), `ret_dashboard_api_model.php` (2642L)

---

## Summary

| Risk Category | Count | Severity |
|---|---|---|
| SQL Injection | ~136 methods | 🔴 CRITICAL |
| Wildcard CORS | 1 controller | 🔴 CRITICAL |
| Hardcoded date override (ignores user filter) | 4 API methods | 🔴 CRITICAL |
| Arithmetic formula error | 1 method | 🟡 MEDIUM |
| Debug code left in comments (`exit;`) | 40+ locations | 🟡 MEDIUM |
| No authentication on REST endpoints | 1 controller | 🟡 MEDIUM |
| CSRF exposure on AJAX endpoints | ~38 methods | 🟡 MEDIUM |
| Unauthenticated `php://input` body | ~20 API methods | 🟡 MEDIUM |

---

## SEC-01: SQL Injection (Mass Vulnerability)

**Severity:** 🔴 CRITICAL  
**Scope:** `ret_dashboard_model.php` — 97 methods; `ret_dashboard_api_model.php` — 39 methods

All queries use raw string interpolation directly in `$this->db->query($sql)`. Every filter parameter (`$from_date`, `$to_date`, `$id_branch`, `$id_metal`) is injectable without any sanitization.

**Example (ret_dashboard_api_model.php L45-161):**
```php
// ALL 136 methods follow this exact pattern:
$sql = "SELECT ... WHERE id_branch IN('{$id_branch}') AND DATE(bill_date) BETWEEN '{$from_date}' AND '{$to_date}'";
$query = $this->db->query($sql);
```

**Attack vector:**
```
POST /admin_ret_dashboard_api/get_Sales_glance_post
from_date=2024-01-01' OR '1'='1
to_date=2024-12-31
id_branch=1
```
→ Full database dump possible via UNION injection.

**Affected endpoints:** ALL `admin_ret_dashboard_api/*` and `admin_ret_dashboard/*` endpoints that accept filter parameters.

**Fix:** Use CI query builder or PDO prepared statements. See AP-01 in `ANTI_PATTERNS.md`.

---

## SEC-02: Wildcard CORS Header (Cross-Origin API Access)

**Severity:** 🔴 CRITICAL  
**File:** `admin_ret_dashboard_api.php` L3-5

```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
```

**Problem:** Any external website, mobile app, or malicious script can call ALL API endpoints from any origin. Combined with SQL injection (SEC-01), this means the full database can be exfiltrated from any browser visiting a malicious page.

**Fix:**
```php
$allowed_origins = ['https://yourdomain.com', 'https://app.yourdomain.com'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins, true)) {
    header("Access-Control-Allow-Origin: {$origin}");
}
```

---

## SEC-03: Hardcoded `date('Y-m-d')` Ignores User's Date Filter

**Severity:** 🔴 CRITICAL (data integrity)  
**File:** `admin_ret_dashboard_api.php`  
**Affected methods:**

| Controller Method | Line | What's ignored |
|---|---|---|
| `get_VitrualTag_post()` | L1030 | `$from_date`, `$to_date` → hardcoded to `date('Y-m-d'), date('Y-m-d')` |
| `get_SalesReturn_post()` | L1069 | Same — date range filter silently dropped |
| `get_LotDetails_post()` | L1107 | Same — today-only regardless of selection |
| `get_CoverUpReport_post()` | L1193 | Same — today-only regardless of selection |

**Problem:** Controller reads `$from_date` and `$to_date` from POST but then passes `date('Y-m-d'), date('Y-m-d')` hardcoded to the model call. User's selected date range is silently ignored.

**Fix:**
```php
// Change from:
$data = $this->$model->get_dashboard_virturaltag_details(date('Y-m-d'), date('Y-m-d'), $id_branch);
// To:
$data = $this->$model->get_dashboard_virturaltag_details($from_date, $to_date, $id_branch);
```

---

## SEC-04: Diamond Weight Formula Error (Always Returns Negative)

**Severity:** 🟡 MEDIUM  
**File:** `admin_ret_dashboard_api.php` L1502

```php
// Weight gain/loss for diamond — all 4 terms use the same variable:
$summary['blc_diawt'] = number_format(
    $summary['blc_diawt'] + (
        $val['lotdiawt'] - $val['lotdiawt'] - $val['lotdiawt'] - $val['lotdiawt']
    ), 2, '.', ''
);
// Evaluates to: lotdiawt × (1 - 1 - 1 - 1) = -2 × lotdiawt
// Diamond weight balance is always NEGATIVE and DOUBLE the lot weight
```

**Fix:** Replace with the correct column names for tagged, received, and LM diamond weights (pattern from `blc_gwt` line above at L1498).
```php
$summary['blc_diawt'] = number_format(
    $summary['blc_diawt'] + (
        $val['lotdiawt'] - $val['tagdiawt'] - $val['recdiawt'] - $val['lmdiawt']
    ), 2, '.', ''
);
```

---

## SEC-05: No Authentication Check on REST API Endpoints

**Severity:** 🟡 MEDIUM  
**File:** `admin_ret_dashboard_api.php`

The `REST_Controller` is used but there is NO `$this->methods['method_name']['level']` configuration and no `$this->rest->is_authenticated()` call in the constructor. Any unauthenticated user who can reach the PHP server can call all API endpoints.

**Check:** Verify if `REST_Controller.php` has built-in API key check that's configured globally.

**Fix (if not already protected):**
```php
function __construct() {
    parent::__construct();
    // Add auth check:
    if (!$this->input->cookie('ci_session')) {
        $this->response(['status' => false, 'message' => 'Unauthorized'], 401);
        exit;
    }
    $this->load->model(self::RET_DAS_MODEL);
}
```

---

## SEC-06: Debug `exit` Statements Left in Commented Code (40+ Locations)

**Severity:** 🟡 MEDIUM  

```php
// ❌ Found in dozens of locations across both controllers:
//print_r($this->db->last_query()); exit;
//print_r($_POST); exit;
//$this->response(array('status'=> true,'response_data' => rand(10,100)), 200);
```

**Problem:** Any developer uncommenting these for debugging will immediately expose raw SQL queries or internal data structures to API responses. These are also signs that the codebase has been developed without proper logging.

**Fix:** Remove all commented debug/exit lines. Use proper logging via CodeIgniter's `log_message()`.

---

## SEC-07: CSRF Risk on AJAX POST Endpoints

**Severity:** 🟡 MEDIUM  
**File:** `admin_ret_dashboard.php` — all 38 AJAX methods

CodeIgniter's CSRF protection (if enabled in `config.php`) needs to be verified for all AJAX endpoints. If CSRF is disabled for API convenience, all POST endpoints that trigger any data change are vulnerable.

**Check:**
```php
// In application/config/config.php:
$config['csrf_protection'] = TRUE; // Must be true
$config['csrf_token_name'] = 'csrf_test_name';
```

**Note:** The Dashboard module is read-only (no writes) so CSRF impact here is limited to data exfiltration, not mutation. Still, AJAX headers should validate CSRF tokens.

---

## SEC-08: Unauthenticated `php://input` JSON Body Reading

**Severity:** 🟡 MEDIUM  
**File:** `admin_ret_dashboard_api.php` L71-75

```php
function get_values() {
    return (array)json_decode(file_get_contents('php://input'));
}
```

**Problem:** When the request is NOT an AJAX request, input is read from raw `php://input`. This `else` branch is the mobile API path — it accepts raw JSON body WITHOUT going through CodeIgniter's Input class or CSRF validation. Malformed JSON returns empty array and `$from_date` etc. will be NULL.

**Fix:** Add null coalescing defaults:
```php
function get_values() {
    $raw = json_decode(file_get_contents('php://input'), true);
    return is_array($raw) ? $raw : [];
}
```
And in each method:
```php
$from_date = $post['from_date'] ?? date('Y-m-d');
```

---

## New Bugs Found — Round 10 Controller Deep Scan

The following new bugs were discovered during the full API controller read and must be added to the bug register:

### Bug #25 — Wildcard CORS on All API Endpoints

| | |
|---|---|
| **File** | `admin_ret_dashboard_api.php` L3 |
| **Severity** | 🔴 CRITICAL |
| **Description** | `Access-Control-Allow-Origin: *` exposes all API data to any origin |
| **Fix** | Restrict to specific allowed domains |

### Bug #26 — 4 API Methods Ignore Date Range (Hardcode Today)

| | |
|---|---|
| **File** | `admin_ret_dashboard_api.php` L1030, L1069, L1107, L1193 |
| **Severity** | 🔴 CRITICAL |
| **Methods** | `get_VitrualTag_post`, `get_SalesReturn_post`, `get_LotDetails_post`, `get_CoverUpReport_post` |
| **Description** | Reads `$from_date`/`$to_date` from POST but passes `date('Y-m-d')` hardcoded to model. User filter is silently ignored. |
| **Fix** | Pass `$from_date, $to_date` to model calls. |

### Bug #27 — Diamond Weight Balance Always Negative

| | |
|---|---|
| **File** | `admin_ret_dashboard_api.php` L1502 |
| **Severity** | 🟡 MEDIUM |
| **Description** | `blc_diawt` formula uses same `$val['lotdiawt']` 4 times → always `-2 × lotdiawt` |
| **Fix** | Replace with correct column names: `$val['tagdiawt']`, `$val['recdiawt']`, `$val['lmdiawt']` |

---

## Corrections to Previous Documentation

The following corrections were found during Round 10 API controller deep scan:

### Orphan Method Corrections (FORENSIC_TEMPLATE.md, ANTI_PATTERNS.md)

Methods previously classified as "orphan (web)" actually ARE called from API controller:

| Method | Previous Classification | Correct Status |
|---|---|---|
| `get_custome_wise_sale()` | Orphan | Called by `get_custome_wise_sale_post()` at L261 |
| `get_cover_up_report()` | Orphan | Called by `get_CoverUpReport_post()` at L1161 |
| `get_purchase_inwards()` | Orphan | Called by `get_purchase_inwards_post()` at L1201 |
| `get_dashboard_breakeven_details()` | Orphan | Called by `get_FinancialStatus_post()` at L1113 |
| `get_outward_details()` | Orphan | Called by `get_outward_details_post()` at L1279 |
| `get_dashboard_virturaltag_details()` | Orphan | Called by `get_VitrualTag_post()` at L998 |
| `get_dashboard_salesreturn_det()` | Orphan | Called by `get_SalesReturn_post()` at L1037 |
| `get_dashboard_lot_tag_details()` | Orphan | Called by `get_LotDetails_post()` at L1075 |
| `get_dashboard_estimation()` | Orphan | Called by `get_EstimationStatus_post()` at L942 |

**True orphan methods (no web or API caller found):**
- `get_branch_sales()` (L647) — superseded by `get_store_sales()`
- `get_rate_cut_details()` (L2522) — no controller caller found

### get_supplier_crde_post Correction

`get_supplier_crde_post()` at L1634 does NOT call `getMetalwiseApprovalTransactionList()`. Instead it calls `ret_reports_model::getSupplierTransactionList($post)` — a completely different model (ret_reports_model), with a different method name.

### Undocumented Model Methods Found

| Method | Controller Caller | Notes |
|---|---|---|
| `get_design_stock()` | `get_design_stock_post()` L1790 | Not in API model method list |
| `get_sub_design_stock()` | `get_design_stock_post()` + `get_sub_design_stock_post()` L1805, L1858 | Not documented |
| `getLotwiseTaggedVault()` | `get_weight_gain_loss_post()` L1486 | In `ret_reports_model` |
| `getSupplierTransactionList()` | `get_supplier_crde_post()` + `get_supplier_transcation_post()` L1669, L1732 | In `ret_reports_model` |
| `getTaggeditems()` | `tag_details_post()` L1909 | In `ret_reports_model` |
| `getActiveMetals()` | `getActiveMetals_get()` L1435 | In `ret_catalog_model` |

### Actual API Controller Method Count

Previous count: 29 methods. Actual full count after complete deep scan: **38 methods**.

| Additional methods found | Line |
|---|---|
| `get_top_selling_post()` | L122 |
| `get_monthly_sales_app_post()` | L327 |
| `get_branch_comparison_post()` | L374 |
| `get_branch_compare_post()` | L399 |
| `get_product_sales_post()` | L493 |
| `get_branch_avg_va_post()` | L531 |
| `get_employee_sales_post()` | L572 |
| `get_karigar_sales_post()` | L694 |
| `get_EstimationStatus_post()` | L942 |
| `get_VitrualTag_post()` | L998 |
| `get_SalesReturn_post()` | L1037 |
| `get_LotDetails_post()` | L1075 |
| `get_FinancialStatus_post()` | L1113 |
| `get_CoverUpReport_post()` | L1161 |
| `get_purchase_inwards_post()` | L1201 |
| `get_vendor_payment_post()` | L1240 |
| `get_outward_details_post()` | L1279 |
| `getMetalwiseApprovalTransaction_post()` | L1316 |
| `get_crdr_details_post()` | L1354 |
| `get_qc_details_post()` | L1391 |
| `getActiveMetals_get()` | L1429 |
| `get_weight_gain_loss_post()` | L1442 |
| `get_rate_fixed_post()` | L1519 |
| `get_rate_unfixed_post()` | L1558 |
| `get_accountstock_inwards_post()` | L1596 |
| `get_supplier_crde_post()` | L1634 |
| `get_supplier_transcation_post()` | L1697 |
| `get_design_stock_post()` | L1750 |
| `get_sub_design_stock_post()` | L1816 |
| `tag_details_post()` | L1879 |
| `get_delayed_purchase_orders_post()` | L1917 |
| `get_delayed_po_payments_post()` | L1935 |
| `get_today_delivery_po_payments_post()` | L1954 |
| `get_rate_cut_profit_loss_post()` | L1975 |
