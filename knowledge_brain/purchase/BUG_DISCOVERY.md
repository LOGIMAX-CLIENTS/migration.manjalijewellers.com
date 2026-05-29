# PURCHASE MODULE — BUG DISCOVERY & SECURITY SCAN
> **Round:** 4 | **Date:** 2026-02-23 | **Scan Type:** Pre-Audit

---

## 🛡️ Bug Root Cause Register (Anti-Patterns)

### PUR-INT02: Duplicate Total Value Display in Payment Reports ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| PUR-INT02 | `get_po_payments()` used `d.payment_amount` (full total from `ret_po_payment_detail`) instead of per-PO allocation, causing N× duplication when JOINed with `ret_po_bill_payment_details` | Replaced with `IFNULL(po_bill.bill_amount, d.payment_amount)` for per-PO allocation with advance payment fallback | 2026-02-26 |

**Prevention**: When a JOIN creates a 1-to-N relationship (one payment → N POs), always use the child table's allocated amount, not the parent's total. Test with multi-PO scenarios.

---

### PUR-CLT01: Purchase dashboard Today PO Pending not showing ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| PUR-CLT01 | Overly restrictive filters in dashboard queries (joining nullable categories and filtering by specific approval types) caused valid records to be hidden. | Removed the specific `pur_approval_type` filter from the dashboard PO pending query. | 2026-03-20 |

**Prevention**: Dashboard summary queries should be as inclusive as possible for "Pending" or "Today" states. Avoid filtering by status flags (like approval type) unless semantically required. Always test queries with NULL join keys.

---

## 🔴 CRITICAL FINDINGS

### C1: Manual Transaction Pattern — `trans_begin()`/`trans_commit()` (Not CI3 Auto)
**Severity: MEDIUM | Pattern: Non-Standard but Functional**
```
trans_begin():    51 calls
trans_commit():   51 calls   ← CORRECTION: 51 commits exist
trans_rollback(): 54 calls
trans_start():     0 calls   ← CI3 auto-mode never used
trans_complete():  0 calls
```
The controller uses manual `trans_begin()/trans_commit()/trans_rollback()` — not CI3's automatic `trans_start()/trans_complete()`. This works but lacks CI3's automatic rollback on failure. The **real CRITICAL risk** is C2 below: `echo exit;` prevents rollback from executing.

**Location:** Every save method in `admin_ret_purchase.php`

---

### C2: 26 ACTIVE `echo last_query();exit;` in Controller
**Severity: CRITICAL | Risk: DATA CORRUPTION + INFO LEAK**

These are **uncommented, active debug statements** that will execute in production and halt execution *inside active transactions*, leaving partial data:

| Line | Context |
|---|---|
| 322 | Inside purchase order save (after trans_begin L284) |
| 452 | Inside purchase order save (after trans_begin L410) |
| 631 | Inside QC issue save (after trans_begin L593) |
| 1055 | Inside QC receipt save (after trans_begin L754) |
| 4016 | Inside lot gen save (after trans_begin L3976) |
| 5207 | Inside PO payment save (after trans_begin L4963) |
| 5221 | Inside PO payment save (inside same transaction) |
| 5477 | Inside payment save (after trans_begin L5439) |
| 5997 | Inside payment save (another block) |
| 6413 | Inside rate fixing save (after trans_begin L6213) |
| 6427 | Inside rate fixing save (same block) |
| 6726 | Inside rate fixing (after trans_begin L6687) |
| 6852 | Inside rate fixing approval (after trans_begin L6804) |
| 7511 | Inside PO close (after trans_begin L7041) |
| 7749 | Inside PO cancel (after trans_begin L7589) |
| 8703 | Inside GRN save (after trans_begin L8585) |
| 10921 | Inside purchase return (after trans_begin L10785) |
| 11067 | Inside purchase return (same block) |
| 11668 | Inside supplier rate cut (after trans_begin L11546) |
| 12428 | Inside smith op bal (after trans_begin L12385) |
| 12939 | Inside nontag receipt (after trans_begin L12840) |
| 13085 | Inside credit/debit save (after trans_begin L13055) |
| 13164 | Inside credit/debit update (after trans_begin L13142) |
| 13218 | Inside credit/debit update (after trans_begin L13189) |
| 13462 | Inside metal issue receipt (after trans_begin L13416) |
| 13545 | Inside karigar save (after trans_begin L13530) |

---

### C3: 3 ACTIVE Debug Statements in Model
| Line | Statement | Impact |
|---|---|---|
| 106 | `print_r($this->db->last_query());exit;` | Inside `insertData()` — ALL inserts will fail! |
| 2038 | `echo $this->email->print_debugger();` | Email debug output to user |
| 4036 | `echo "<pre>";print_r($return_data);exit;` | Inside `get_lot_and_tag_wise_report()` — report crashes |

---

## 🟠 HIGH FINDINGS

### H1: 330 Direct `$_POST` vs 26 `$this->input->post()`
**Severity: HIGH | Risk: XSS + Input Bypass**

The controller uses raw `$_POST` **330 times** and CI3's safe `$this->input->post()` only **26 times**. CI3's method applies XSS filtering and CSRF validation — raw `$_POST` bypasses both.

**Key unsafe locations:**
| Line Range | Method | Data Used |
|---|---|---|
| 9527-9531 | purchase save | `$_POST['order']`, financial data |
| 11477-11479 | supplier rate cut save | `$_POST['supplier_rate_cut']`, `$_POST['billing']` |
| 12534 | nontag lot save | `$_POST['id_karigar']` |
| 13036-13051 | credit/debit save | `$_POST['transamount']`, `$_POST['weight']` |
| 13263 | retagging report | `$_POST` passed directly to model |
| 13319 | metal issue receipt | `foreach($_POST['issue']...)` |

---

### H2: 264 Raw SQL Queries in Model
**Severity: HIGH | Risk: SQL INJECTION + MAINTENANCE**

The model uses `$this->db->query()` **264 times** instead of CI3 Active Record. Many concatenate user input directly:

```php
// Example: L5168 — id_category from POST passed unsanitized
" and c.id_old_metal_cat in (".$id_category.") "
```

---

### H3: 20 `SELECT *` in Model
**Severity: MEDIUM-HIGH | Risk: PERFORMANCE + DATA LEAK**

20 queries use `SELECT *` instead of specifying columns. This returns unnecessary data, increases memory usage, and may expose sensitive fields.

---

## 🟡 MEDIUM FINDINGS

### M1: Existing Bugs from Round 2
| Bug | Line | Severity |
|---|---|---|
| Typo: `$clsoing_grm_wt` | Model L8045 | CRITICAL |
| Pocket weight missing from closing | Model L8026 | MEDIUM |
| SQL injection in retag filters | Model L5168+ | HIGH |

### M2: Manual Transaction Pattern (Functional but Non-Standard)
Pattern used:
```php
$this->db->trans_begin();
// ... inserts/updates ...
if ($this->db->trans_status() === FALSE) {
    echo $this->db->last_query();exit; // ← THIS prevents rollback!
    $this->db->trans_rollback();
} else {
    $this->db->trans_commit(); // ← This IS called (51 times)
}
```
CI3 recommended:
```php
$this->db->trans_start();
// ... inserts/updates ...
$this->db->trans_complete(); // auto-commit or rollback
```

### M3: Commented Debug `print_r($_POST);exit;` (96 occurrences)
While currently commented, these are a maintenance hazard — any accidental uncommenting will break production.

---

## 🟢 POSITIVE FINDINGS

### P1: Good JS Financial Safety
The JS file has **1,544** uses of `parseFloat/parseInt/toFixed/isNaN` — indicating good financial calculation safety practices on the client side.

### P2: Transaction Usage is Widespread
51 `trans_begin()` calls means the developer intended transactional safety — the problem is implementation (missing `trans_complete()`), not intent.

---

## Summary Statistics

| Category | Count | Severity |
|---|---|---|
| Active `echo...exit;` in controller | **26** | 🔴 CRITICAL |
| Active debug in model | **3** | 🔴 CRITICAL |
| `trans_begin` without `trans_complete` | **51** | 🔴 CRITICAL |
| Direct `$_POST` (bypassing CI3) | **330** | 🟠 HIGH |
| Raw SQL `$this->db->query()` | **264** | 🟠 HIGH |
| `SELECT *` in model | **20** | 🟡 MEDIUM |
| Commented debug statements | **96+** | 🟢 LOW (maintenance) |

## Bug Count by Severity
| Severity | Count |
|---|---|
| 🔴 CRITICAL | 3 systemic + 26 individual = **29** |
| 🟠 HIGH | 3 systemic (330 + 264 + 20 instances) |
| 🟡 MEDIUM | 3 (from Round 2 + commented debug) |
| **Total unique bug patterns** | **8** |
| **Total affected lines** | **700+** |

---

## Bug Root Cause Register — Fixed Bugs

### PUR-INT03: Missing CR/DR Indicator on Pure Balance & Amount Balance ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| PUR-INT03 | Balance values shown as raw numbers without CR/DR context; no DB persistence of balance nature | Added CSS-styled CR/DR badges, hidden inputs for DB storage (1=CR, 2=DR), and controller insert mapping | 2026-02-27 |

**Prevention**: Every financial balance display should include a CR/DR indicator. When a form captures balance data, always persist the nature (credit/debit) alongside the amount.

> **Note (PUR-INT03)**: The `get_supplier_pay_details` AJAX response returns `balance_purewt` and `balance_amt` as signed numbers (negative=CR, positive=DR). The sign determines the type, but previously this information was lost at the UI layer — users saw only absolute numbers.
## Bug Anti-Patterns Register

### PUR-UI01: Product Name "Undefined" & Column Alignment in PO Form ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| PUR-UI01 | `product_name` selected without IFNULL on LEFT JOIN + `file_get_contents()` on missing files corrupted JSON + dynamic `tag_code` cells not hidden after radio handler | IFNULL wrapper, file_exists guard, inline `display:none`, JS `\|\|''` fallback | 2026-02-25 |

**Prevention**: Always wrap LEFT JOIN display columns in `IFNULL()`. Always `file_exists()` before `file_get_contents()`. Re-apply visibility logic after dynamic row insertion.

> **Note (PUR-UI01)**: The radio button handler for `order_for` hides `.tag_code` elements *before* rows are dynamically inserted by the `#select_order_no` change handler. New rows therefore bypass the visibility rule. The fix applies inline styles during row creation rather than relying on a second pass.

#### Field-Level Data Flow

| Field | DB Column | PHP Variable | JS Variable | HTML Element |
|---|---|---|---|---|
| Weight Balance Type | `ret_supplier_rate_cut.weight_type` | `$data['weight_type']` | `$('#cashcrdr_weight_type').val()` | `#cashcrdr_weight_type` |
| Amount Balance Type | `ret_supplier_rate_cut.amount_type` | `$data['amount_type']` | `$('#cashcrdr_amount_type').val()` | `#cashcrdr_amount_type` |
| Weight Badge | _(display only)_ | _(n/a)_ | `$('.weight_bln_type').html()` | `.weight_bln_type` |
| Amount Badge | _(display only)_ | _(n/a)_ | `$('.amt_bln_type').html()` | `.amt_bln_type` |
| Product Name | `ret_product_master.product_name` | `$result['product_name']` | `items.product_name` | `<td>` (inline text, line 6555) |
| Tag Code | `ret_taging.tag_code` | N/A (not in query) | `items.tag_code` | `<td class="tag_code">` (line 6551) |

---

### PUR-CLT03: Incorrect Pure Weight Field Labels & Position in Approval to Invoice Conversion ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| PUR-CLT03 | View labels ("Pure Balance", "Outstanding Pure Wt(Grms)") did not match the data context — should read "Bill Amt(Rs)", "Bill Pure Wt(Grms)". Right-panel fields needed relocation to left panel. | Renamed labels, moved fields to left panel below Select Metal, removed redundant right-panel section, added h4 styling | 2026-03-04 |

**Prevention**: Always cross-reference view labels with the JS function that populates the field values (`set_rate_cut_details()`). When creating forms with balance/outstanding fields, maintain a label→data mapping document to prevent mismatches.

> **Note (PUR-CLT03)**: The `set_rate_cut_details()` function populates `#wt_balance` with `data.balance_purewt` and `.availablepurebalance` with `data.balance_wt`. The labels in the view must match these data meanings. This was a pure UI labeling issue — no JS or model logic changes were needed.
### RPT-CLT02: Rejected Weight Showing on Payment/Ratecut Rows ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| RPT-CLT02 | `get_po_payments()` UNION query — 1st SELECT (payment/ratecut rows) had LEFT JOIN on `ret_purchase_return_items` that populated `rejected_gwt`/`rejected_purewt` on all row types. These values should only appear on return rows (2nd SELECT). | Replaced `IFNULL(ret.rejected_gwt, 0)` with `0 as rejected_gwt` in 1st SELECT; removed LEFT JOIN subquery. Return rows (2nd SELECT) unchanged. | 2026-03-06 |

**Prevention**: In UNION queries combining different row types, each SELECT should only populate columns relevant to its row type. Use hardcoded `0` or `''` for columns that belong exclusively to another row type. Never LEFT JOIN data into a row type that shouldn't display it.

> **Note (RPT-CLT02)**: The `get_po_payments()` function (in `ret_reports_model.php` L20487) uses a UNION to merge payment/ratecut rows with purchase return rows. The rejected weight columns (`pur_ret_gwt`, `pur_ret_pur_wt`) come from `ret_purchase_return_items` and are semantically scoped to return transactions only. The JS DataTable (`ret_reports.js` L23762-23770) and view (`popayments.php` L105-106) correctly render both columns — the fix ensures only return rows contain non-zero values.

#### Field-Level Data Flow

| Field | DB Column | PHP Variable | JS Variable | HTML Element |
|---|---|---|---|---|
| Rejected Gross Wt | `ret_purchase_return_items.pur_ret_gwt` | `$result['rejected_gwt']` | `row.rejected_gwt` | Column 5 in `#purchase_pay_list` |
| Rejected Pure Wt | `ret_purchase_return_items.pur_ret_pur_wt` | `$result['rejected_purewt']` | `row.rejected_purewt` | Column 6 in `#purchase_pay_list` |
