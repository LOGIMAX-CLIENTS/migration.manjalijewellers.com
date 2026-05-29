# CHIT_REPORTS — MODEL DEEP SCAN
*Expanded analysis of payment_model.php (8904L) and account_model.php (3651L)*

---

## payment_model.php — Key Discoveries

### Constructor (L22-30)
```php
function __construct() {
    parent::__construct();
    $this->load->model("customer_model");          // ← preloads customer_model
    $this->log_dir = 'log/' . date("Y-m-d");
    if (!is_dir($this->log_dir)) {
        mkdir($this->log_dir, 0777, TRUE);         // ← 0777 risk
    }
}
```
**Risk**: `mkdir(0777)` creates world-writable log directory. Same pattern in account_model.

---

### `payment_list_range()` — L311 (Used by payment history)
```php
$id_status   = $_POST['id_status'];      // 🔴 RAW POST — no sanitize
$id_customer = $_POST['id_customer'];   // 🔴 RAW POST — no sanitize
```
Uses CI query builder for WHERE clauses (OK for most fields), but these two bypass it.

---

### `sheme_payment_list_daterange()` — L6910 (Source-wise report)

**Primary JOIN chain**:
```
payment_mode_details pmd   ← BASE TABLE (not payment!)
JOIN payment p ON p.id_payment = pmd.id_payment
JOIN scheme_account sa
JOIN customer c
JOIN scheme s
JOIN branch b ON b.id_branch = p.id_branch      ← PAYMENT branch, not account branch
LEFT JOIN bank bk ON bk.id_bank = pmd.id_bank
LEFT JOIN village v ON v.id_village = c.id_village
LEFT JOIN ret_bill_pay_device dev ON dev.id_device = pmd.id_pay_device
JOIN chit_settings chit
LEFT JOIN branch acc_br ON acc_br.id_branch = sa.id_branch   ← ACCOUNT branch
LEFT JOIN employee pe ON pe.id_employee = p.id_employee
LEFT JOIN (subquery: cnt_months, total_dues per scheme_account) pay_agg
```

**Grouping**:
```sql
GROUP BY pmd.id_pay_mode_details   ← ONE ROW PER PAYMENT SPLIT
```
→ A payment paid 500 cash + 500 UPI = TWO rows in result.

**Report type grouping** (PHP-side, not SQL):
```php
if ($report_type == 1) $return_data[$r['scheme_name']][] = $r;
if ($report_type == 2) $return_data[$r['village_name']][] = $r;
if ($report_type == 0) $return_data['Payment'][] = $r;
```

---

### `get_Scheme_Payment_ModeWiseummaryDetails()` — L7055

**Bug RPT-B09**:
```php
ORDER BY p.date_payment DESC" . ($limit != NULL ? " LIMIT ".$limit." OFFSET ".$limit : " ");
```
`$limit` is NOT a parameter and NOT defined in the function scope → PHP notice, treated as NULL → no LIMIT applied. Harmless but sloppy.

**GST formula in this method**:
```sql
IF(s.gst_type=0,
   (pmd.payment_amount-(pmd.payment_amount*(100/(100+s.gst))))/2,
   ((pmd.payment_amount*(s.gst/100))/2)
) as sgst
```
- gst_type=0 (inclusive): extract GST from included amount
- gst_type=1 (exclusive): add GST on top

---

### `payment_summary_modewise_data()` — L7085

Splits data into THREE buckets based on `edit_custom_entry_date`:
```php
if date_payment < edit_custom_entry_date:
    → offline[] or online[] (based on added_by)
else:
    → admin_app[]
```
Returns: `{offline: {date: [rows]}, online: {date: [rows]}, admin_app: {date: [rows]}}`

Controller access pattern:
```php
foreach ($data['mode_wise_sum']['admin_app'] as $key => $value) {
    $admin_app[] = $value['admin_app_amt'];
}
$data['admin_app_total'] = round(array_sum($admin_app), 2);
```
🔴 If `mode_wise_sum['admin_app']` is empty array: `$admin_app` never initialized → PHP warning on `array_sum(null)`.

---

### `getMemberReport()` — L7883

Massive query with no branch/scheme filters from outside. Reads area, city, joined_through internally. Returns full member list with join analytics. Called by controller with NO parameters.

---

### `monthly_report_data()` — L8392

```php
function monthly_report_data() {
    $login_branch = $this->session->userdata('id_branch');
    if (!empty($login_branch)) {
        $id_branch = $login_branch;       // session wins
    } else {
        $id_branch = $this->input->post('id_branch');  // POST fallback
    }
    $id_scheme = $this->input->post('id_scheme');
    $month     = $this->input->post('month');
    $year      = $this->input->post('year');
```
⚠️ Reads ALL filters from POST inside model — not passed as parameters. Side effect: function signature is `monthly_report_data()` but secretly depends on POST state.

---

### `maturity_report_data()` — L8436

```php
$query = "select total_installments,scheme_name,id_scheme,installment_cycle from scheme";
$scheme_data = $this->db->query($query)->result_array();   // Get ALL schemes

foreach ($scheme_data as $scheme) {
    // Per-scheme: calculate mat date range
    if ($scheme['installment_cycle'] == 1) {    // daily
        $mat_from = date(..., strtotime($from_date . ' - ' . $tot_ins . ' day'));
    } else {                                     // monthly
        $mat_from = date(..., strtotime($from_date . ' - ' . $tot_ins . ' month'));
    }
    // Then N queries, one per scheme
    $sql = "SELECT ... WHERE date(sa.start_date) BETWEEN '$mat_from' AND '$mat_to' AND sa.id_scheme=$id";
}
```
N+1 problem: 1 scheme-list query + 1 query per scheme.

---

### `general_advance_list()` — L8297

Correctly uses:
```
FROM general_advance_mode_detail gapd
LEFT JOIN general_advance_payment gap
```
Parallel structure to `payment`/`payment_mode_details`.

Metal weight calculation:
```sql
round(IF(gap.metal_weight > 0,
    (gap.metal_weight / (select count(id_pay_mode_details) from general_advance_mode_detail
     where id_adv_payment = gap.id_adv_payment and payment_status = 1)),
    '0'), 3) as metal_weight
```
Divides total weight equally across active mode splits. ⚠️ Division by zero if count = 0.

---

## account_model.php — Key Discoveries

### Constructor (L19-28)
Same `mkdir(0777)` pattern. Also preloads `customer_model` and `payment_model`.

---

### `get_all_account()` — L461
```php
function get_all_account() {
    $accounts = $this->db->query("select ... from scheme_account s ... where s.is_closed=0");
    return $accounts->result_array();
}
```
⚠️ **No filters** — returns ALL non-closed accounts. Used in `employee_account` view load. For large installations (10k+ accounts) this is a performance risk.

---

### `get_all_closed_account()` — L678
Uses full CI query builder pattern. Branch filter:
```php
if ($branchWiseLogin == 1 || $is_branchwise_cus_reg == 1) {
    if ($id_branch != '') {
        $this->db->where("(s.closing_id_branch=$id_branch or b.show_to_all=1)", NULL, FALSE);
    }
}
```
Note: uses `closing_id_branch` (where the account was closed), not `id_branch` (where account was opened). This is correct business logic but differs from most other queries.

---

### `get_all_scheme_account_by_range()` — L2421
Used for outstanding report. Large SELECT with booking amounts, gift article status, subquery for paid installments. Key:
```php
// subquery per row for paid_installments:
IFNULL((select IFNULL(IF(sa.is_opening=1,...), ...) from payment pay where ...), 0) as old_paid_installments
```
This is a correlated subquery — runs once per account row. For 10k accounts: 10k subqueries. Use `total_paid_ins` denormalized column instead (already available in SA).

---

### `insertData()` / `updateData()` — L30-84
Generic methods that use `SHOW COLUMNS FROM table` to get defaults before inserting. This means every insert/update from account_model requires an extra schema query. For high-volume operations this is inefficient.
