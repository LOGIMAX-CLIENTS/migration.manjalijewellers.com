# ROUND 3 SUPPLEMENT — Stock Issue Module
> Round 3 | 2026-03-19 | Deep Bug Verification + New Findings

---

## Summary

| Round | Bugs Found | Critical | Medium | Low |
|---|---|---|---|---|
| R1 | 10 | 3 | 4 | 3 |
| R2 | 2 | 1 | 0 | 1 |
| R3 | 5 | 3 | 1 | 1 |
| R4 | 1 | 0 | 1 | 0 |
| R5 | 2 | 0 | 0 | 2 |
| R6 | 1 | 0 | 0 | 1 |
| R7 | 3 | 1 | 1 | 1 |
| R12 | 1 | 0 | 0 | 1 |
| R14 | 1 | 0 | 0 | 1 |
| **Total** | **26** | **9** | **8** | **9** |

---

## Part A: Verified Bugs (R-01 through R-12 with exact line numbers)

### R-01 🔴 CRITICAL — Debug echo + exit (4 locations)
**Exact locations — CORRECTED in Round 4**:
| Line | Context | Rollback Status |
|---|---|---|
| Controller L415 | Tagged Issue — rollback branch | `echo...exit` runs BEFORE `trans_rollback()` → **ROLLBACK NEVER RUNS** |
| Controller L547 | Tagged Receipt — rollback branch | `echo...exit` runs BEFORE `trans_rollback()` → **ROLLBACK NEVER RUNS** |
| Controller L840 | NonTag Issue — rollback branch | `trans_rollback()` at L840, then `echo...exit` at L842 → **Rollback DOES run** |
| Controller L1044 | NonTag Receipt — rollback branch | `trans_rollback()` at L1044, then `echo...exit` at L1046 → **Rollback DOES run** |

**Corrected Impact**: Only **Tagged Issue (L415)** and **Tagged Receipt (L547)** branches have the fatal ordering bug. NonTag branches have the echo after rollback — response is broken but rollback still executes.

**Fix**: All 4 — Remove `echo $this->db->last_query();exit;`. Log via `log_message()` instead.

---

### R-02 🔴 CRITICAL — SQL Injection in `get_tag_scan_details()`
**Model L733-739**:
```php
AND ".($old_tag_code!='' 
    ? " tag.old_tag_id='".$old_tag_code."'" 
    : ($tag_code!='' ? "tag_code='".$tag_code."'" :'') )."
".($data['id_branch'] !='' ? " and tag.current_branch = ".$data['id_branch']."" :'')."
".($data['id_metal'] !='' ? " and c.id_metal = ".$data['id_metal']."" :'')."
".($data['id_section'] !='' ? " and tag.id_section = ".$data['id_section']."" :'')."
```
**Injected from**: `$tag_code` from `$this->input->post('tag_code')` — raw, no sanitization.
**All 4 params injectable**: `tag_code`, `id_branch`, `id_metal`, `id_section`
**Input type**: POST parameters from barcode scanner field

**Fix**: Use CI query builder: `->where('tag.tag_code', $tag_code)` OR cast numerics: `(int)$data['id_branch']`

---

### R-03 🔴 CRITICAL — SQL Injection in `get_nontag_scan_details()`
**Model L1312-1314**:
```php
WHERE branch=".$data['id_branch']."
".($data['prodId'] != '' ? ' and nt.product='.$data['prodId']: '')."
".($data['id_section'] != '' ? ' and nt.id_section='.$data['id_section']: '')."
```
**All 3 params injectable**: `id_branch`, `prodId`, `id_section`
**Fix**: Cast to `(int)` or use query builder `->where()`

---

### R-04 🟠 MEDIUM — SQL Injection in `get_profile_settings()`
**Model L74**:
```php
$data=$this->db->query("SELECT pr.stock_issue_otp_req FROM profile pr WHERE pr.id_profile ='".$id_profile."'");
```
**Caller**: Controller `stock_issue('add')` with `$id_profile = $this->session->userdata('id_profile')`
**Risk**: Lower — comes from session, not raw POST. But session data can be manipulated.
**Fix**: `$this->db->escape($id_profile)` or cast to int

---

### R-05 🟠 MEDIUM — Race Condition in `generateIssueNo()`
**Model L112**:
```php
$sql = "SELECT MAX(SUBSTRING_INDEX(issue_no, '-', -1)) AS last_issue_no 
        FROM ret_stock_issue 
        WHERE fin_year = '".$fin_year['fin_year_code']."' 
        ORDER BY last_issue_no DESC LIMIT 1";
```
**Problem**: `MAX()` read → increment → insert is NOT atomic. Two concurrent saves get the same MAX and generate duplicate issue numbers.
**Also note**: `fin_year_code` is from `get_FinancialYear()` (L84 — safe, comes from DB, no user input)
**Fix**: Use `UNIQUE` constraint on `issue_no` in `ret_stock_issue` table + retry loop, or use a `ret_stock_issue_seq` sequence table with `SELECT FOR UPDATE`

---

### R-06 🟡 LOW — N+1 Query in `ajax_getStockIssueList()`
**Model L232**:
```php
'summary' => $this->get_stock_issue_det($items['id_stock_issue']),
```
**Called inside**: `foreach($data as $items)` → one DB query per row
**Impact**: If 100 issues on list → 101 queries per page load
**Fix**: Join or subquery `get_stock_issue_det` logic into the main query

---

### R-07 🟠 MEDIUM — Undefined `$issue_date` in Receipt Branch
**Controller**: `stock_issue()` receipt path, L463:
```php
'date' => $issue_date,  // <-- uses $issue_date
```
**Problem**: `$issue_date` is defined at L211 (inside issue branch):
```php
$issue_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);
```
In receipt branch (`issue_receipt_type==2`), this code path is NOT reached. `$issue_date` is undefined → PHP NOTICE → value is `NULL` → `ret_taging_status_log.date = NULL`
**Fix**: Move `$issue_date` definition to before the branch split, or re-derive in the receipt block

---

### R-09 🔴 CRITICAL — OTP Returned in JSON Response
**Controller L1312** (CONFIRMED ACTIVE — NOT COMMENTED):
```php
$status=array('OTP' => $OTP, 'status'=>true, 'msg'=>'OTP sent Successfully');
// 'OTP' => $OTP,   <-- comment on L1313, but L1312 is LIVE
```
**Note**: L1313 shows a comment that says `// 'OTP' => $OTP,` suggesting developer intended to remove it, but L1312 still has it active!
**Impact**: Any user can capture the AJAX response in Network tab and skip OTP verification entirely.
**Fix**: Remove `'OTP' => $OTP` from the response array

---

### R-10 🟠 MEDIUM — No Rollback on OTP Verify Failure Paths
**Controller L1343**: `$this->db->trans_begin();`
**L1373**: `$this->db->trans_commit();` ← only on OTP valid + not expired
**Missing**: `trans_rollback()` in the expired OTP path (L1365) and wrong OTP path (L1391)
**Impact**: Open DB transactions not properly closed on failure paths
**Fix**: Add `$this->db->trans_rollback();` in both failure branches

---

### R-11 🔴 CRITICAL — Hardcoded 3% Tax in PDF
**issue_ack.php L569**:
```php
$tax_amount=(($total_taxable_amount * 3)/100);  // 3 hardcoded!
$total_cgst=($tax_amount/2);
$total_sgst=($tax_amount/2);
```
**Impact**: All delivery challans show wrong tax amounts if actual GST rate is not 3%. The model query fetches `tax_percentage` but the template never uses it.
**Fix**: Replace `3` with `$val['tax_percentage']`

---

### R-12 🟡 LOW — Duplicate DOM ID `sto_i_increment`
**form.php**: Two hidden inputs both have `id="sto_i_increment"` — one for issue table row counter, one for receipt table row counter.
**Impact**: `document.getElementById('sto_i_increment')` returns only the first one. Receipt row counter may not function in all browsers.
**Fix**: Rename to `sto_issue_increment` and `sto_receipt_increment` respectively

---

## Part B: New Bugs Found in Round 3

### R-13 🟠 MEDIUM — SQL Injection in `ajax_getStockIssueList()` Status Filter
**Model L195**:
```php
WHERE i.id_stock_issue IS NOT NULL ".
($data['status'] != '' && $data['status'] > 0 
    ? 'AND issue_det.status=' . $data['status'] 
    : '')
```
**Injected from**: POST `$data['status']` — from list view status dropdown
**Risk**: Moderate — comes from a dropdown (0/1/2/3), but still injectable since no type-cast
**Fix**: `(int)$data['status']`

---

### R-14 🟡 LOW — SQL Injection in `get_stock_issue_StoneDetails()`
**Model L854**:
```php
WHERE s.tag_id = '".$tag_id."'
```
**Injected from**: `$tag_id` from foreach loop over `$tagging` result array (internal DB data, not direct POST)
**Risk**: Low — tag_id is from a previous DB result, but still concatenated raw
**Fix**: Use `(int)$tag_id`

---

### R-15 🟡 LOW — SQL Injection in `get_receipt_tag_scan_details()`
**Model L925**:
```php
AND ".($old_tag_code!='' 
    ? " tag.old_tag_id='".$old_tag_code."'" 
    : ($tag_code!='' ? "tag.tag_code='".$tag_code."'" :'') )."
```
**Injected from**: `$tag_code = $this->input->post('tag_code')` (L866) — raw POST
**Risk**: HIGH — user-supplied barcode value directly injected into SQL
**Fix**: `$this->db->escape($tag_code)` or query builder

---

### R-16 🔴 CRITICAL — All Fields Raw in `updateNTData()` Arithmetic UPDATE
**Model L1346**:
```php
$sql = "UPDATE ret_nontag_item SET 
        no_of_piece=(no_of_piece".$arith." ".$data['no_of_piece']."),
        gross_wt=(gross_wt".$arith." ".$data['gross_wt']."),
        net_wt=(net_wt".$arith." ".$data['net_wt']."),
        updated_by=".$data['updated_by'].",
        updated_on='".$data['updated_on']."' 
        WHERE id_nontag_item=".$data['id_nontag_item'];
```
**ALL 6 values concatenated raw** — no escaping or casting anywhere.
**Also**: `$arith` is operator (`+` or `-`) — passed from controller. If controller input is ever user-controlled, this allows operator injection.
**Callers**: Controller builds `$data` from POST (L690-720 area)
**Impact**: All non-tag stock write operations are injectable
**Fix**: Use `(float)` cast on all numeric fields, `(int)` on IDs, `$this->db->escape()` on strings

---

### R-17 🟠 MEDIUM — Undefined Variable `$result` in `get_nontag_scan_details()`
**Model L1340**: `return $result;`
**Problem**: `$result` is only initialized inside `if($r['gross_wt'] > 0)` (L1319). If ALL returned rows have `gross_wt <= 0`, `$result` is never initialized.
**Result**: PHP NOTICE: `Undefined variable: result` → `return null` → JS receives null → non-tag item list fails silently
**Fix**: Initialize `$result = array();` before the foreach loop (L1318)

---

### R-18 🟠 MEDIUM — `$insId` Undefined in NonTag Receipt Success Response
**Controller L1036**:
```php
$return_data=array('status'=>TRUE,'message'=>'Stock Receipt successfully..','id_stock_issue'=>$insId);
```
**Problem**: In the NonTag Receipt branch (`stock_type=2, type_issue=2`), `$insId` is **never assigned**. `$insId` is only set in the NonTag Issue branch at L632: `$insId = $this->$model->insertData($insData,'ret_stock_issue');`
**Receipt never inserts a new `ret_stock_issue` row** — it updates existing issue detail rows. So `$insId` is either `0`, `NULL`, or the tail value from a prior code path.
**Impact**: JS receives `id_stock_issue: null/0` in success response → any JS redirect or reload using this ID fails silently
**Also**: Same response at L1036 says `'message'=>'Stock Receipt successfully..'` — missing trailing period for consistency
**Fix**: Set `$id_stock_issue = $addData['issue_id']` or `$nt_data[0]['id_stock_issue']` and use that in the response

---

### R-19 🟡 LOW — Raw SQL in `stock_issue_type_detail()` and `getTagDetails()`
**Model L1031**:
```php
$sql=$this->db->query("SELECT * FROM `ret_stock_issue_types` WHERE id_stock_issue_type=".$id."");
```
**Model L1229**:
```php
$sql = $this->db->query("select IFNULL(t.id_section,'') as id_section, t.gross_wt, t.net_wt, t.piece FROM ret_taging t where t.tag_id=".$tag_id."");
```
**Risk**: Low — both `$id` and `$tag_id` come from internal model results / session (not raw POST in main flow)
**Fix**: Cast to `(int)$id` and `(int)$tag_id`

---

### R-20 🟡 LOW — Second N+1 in `get_StockIssuedItems()`
**Model L1055** (tagged path):
```php
$tag_details=$this->stock_issue_tags($items['id_stock_issue']);
```
**Model L1077** (nontag path):
```php
$ntag_details=$this->stock_issue_nontags($items['id_stock_issue']);
```
**Called inside**: `foreach($result as $items)` over ALL issued stock records
**Impact**: Receipt form dropdown loads N+1 queries (one full multi-join query per issued stock record)
**Fix**: Restructure to fetch all tags/nontags in one query with GROUP BY

> Note: `get_issue_item_details()` also triggers cascading N+2 via `get_StoneDetails()` + `get_other_metal_details()` calls per category row (L406, L408, L480, L482, L572, L574, L646, L648)

---

## Part C: Cumulative Bug Register (All 17 Bugs)

| ID | Sev | Layer | Method | Line(s) | Description |
|---|---|---|---|---|---|
| R-01 | 🔴 CRIT | Controller | `stock_issue(save)` | L415, L547, L842, L1046 | `echo last_query();exit` kills rollback |
| R-02 | 🔴 CRIT | Model | `get_tag_scan_details()` | L733-739 | 4 raw params in SQL WHERE |
| R-03 | 🔴 CRIT | Model | `get_nontag_scan_details()` | L1312-1314 | 3 raw params in SQL WHERE |
| R-04 | 🟠 MED | Model | `get_profile_settings()` | L74 | Raw `$id_profile` from session |
| R-05 | 🟠 MED | Model | `generateIssueNo()` | L112 | Race condition — no DB lock |
| R-06 | 🟡 LOW | Model | `ajax_getStockIssueList()` | L232 | N+1: `get_stock_issue_det()` in loop |
| R-07 | 🟠 MED | Controller | `stock_issue(save)` | L463 | `$issue_date` undefined in receipt branch |
| R-08 | 🟡 LOW | Controller | `__construct()` | L29 | `ret_billing_model` loaded, never used |
| R-09 | 🔴 CRIT | Controller | `stock_issue_sendotp()` | L1312 | OTP value in JSON response |
| R-10 | 🟠 MED | Controller | `stock_issue_verify_otp()` | L1343+ | Trans_begin without rollback |
| R-11 | 🔴 CRIT | View | `issue_ack.php` | L569 | Tax hardcoded at 3% |
| R-12 | 🟡 LOW | View | `form.php` | — | Duplicate DOM ID `sto_i_increment` |
| R-13 | 🟠 MED | Model | `ajax_getStockIssueList()` | L195 | Raw `$data['status']` in status filter |
| R-14 | 🟡 LOW | Model | `get_stock_issue_StoneDetails()` | L854 | Raw `$tag_id` (from DB, lower risk) |
| R-15 | 🔴 CRIT | Model | `get_receipt_tag_scan_details()` | L925 | Raw `$tag_code` from POST |
| R-16 | 🔴 CRIT | Model | `updateNTData()` | L1346 | All 6 fields raw in arithmetic UPDATE |
| R-17 | 🟠 MED | Model | `get_nontag_scan_details()` | L1340 | `$result` undefined if all gross_wt ≤ 0 |
| R-18 | 🟠 MED | Controller | `stock_issue(save)` | L1036 | `$insId` undefined in NonTag Receipt response |
| R-19 | 🟡 LOW | Model | `stock_issue_type_detail()`, `getTagDetails()` | L1031, L1229 | Raw `$id`/`$tag_id` in query (internal source) |
| R-20 | 🟡 LOW | Model | `get_StockIssuedItems()` | L1055, L1077 | N+1: `stock_issue_tags()`/`stock_issue_nontags()` per issue |
| R-21 | 🟡 LOW | Controller | `stock_issue(issue_print, issue_print_detail)` | L1092, L1117 | Typo `'portriat'` in dompdf — PDF rendered in wrong orientation |
| R-22 | 🔴 CRIT | JS | `stock_order_otp()` | L1751, L1801 | XSS: raw `data.msg` from server injected into DOM via `.append()` |
| R-23 | 🟠 MED | JS | `stock_at_send_otp()`, `stock_order_otp()` | L1527, L1719 | `async: false` — synchronous AJAX blocks browser UI thread |
| R-24 | 🟡 LOW | JS | Multiple functions | L225, L1225, L1331 | `console.log` left in production (leaks response data to browser console) |
| R-25 | 🟡 LOW | View | `form.php` | L91 | `$message['message']` echoed unescaped — server-controlled flash but inconsistent escape policy |
| R-26 | 🟡 LOW | View | `form.php` | L437, L707 | Duplicate DOM ID `searchEstiAlert` (Issue + Receipt sections) — receipt scan error messages never display |

---

## Part D: Fix Priority Order

### Immediate (Production Risk — Fix First)

1. **R-01** — Remove `echo last_query();exit` from all 4 rollback paths
2. **R-09** — Remove `'OTP' => $OTP` from sendotp JSON response
3. **R-22** — Sanitize `data.msg` before `.append()` in OTP modal (escape HTML or use `.text()`)
4. **R-11** — Replace hardcoded `3` with `$val['tax_percentage']` in PDF template
5. **R-16** — Type-cast all fields in `updateNTData()`
6. **R-02/R-03/R-15** — Sanitize tag scan + nontag scan + receipt scan SQL inputs

### Short Term

7. **R-17** — Initialize `$result = []` before foreach in `get_nontag_scan_details()`
8. **R-23** — Replace `async: false` with proper async callback in sendotp + verify_otp AJAX
9. **R-07** — Move `$issue_date` definition before the issue/receipt branch split
10. **R-10** — Add `trans_rollback()` on OTP failure paths
11. **R-13** — Cast `(int)$data['status']` in list query
12. **R-24** — Remove all 3 `console.log` statements (JS L225, L1225, L1331)

### Backlog

10. **R-05** — Add unique constraint on `issue_no` + retry logic
11. **R-06** — Restructure list query to eliminate N+1
12. **R-04** — Escape session-sourced profile ID
13. **R-14** — Cast `(int)$tag_id` in loop
14. **R-12** — Rename duplicate DOM IDs
16. **R-19** — Cast `(int)$id` in `stock_issue_type_detail()` and `getTagDetails()`
17. **R-20** — Refactor `get_StockIssuedItems()` to eliminate N+1
18. **R-21** — Fix `'portriat'` → `'portrait'` in both dompdf calls (L1092, L1117)
19. **R-08** — Remove unused `ret_billing_model` load
20. **R-25** — Wrap flash echo with `htmlspecialchars()` in `form.php` L91
21. **R-26** — Rename duplicate `#searchEstiAlert` IDs to `#issue_searchEstiAlert` + `#receipt_searchEstiAlert`; update JS selectors

---

## Part E: SQL Injection Attack Surface Summary

This module has **11 SQL injection attack vectors** mapped + 3 N+1 patterns + 2 XSS/view vectors:

| # | Vector | Method | Param | Source | Risk |
|---|---|---|---|---|---|
| 1 | `tag_code` | `get_tag_scan_details` | POST `tag_code` | User barcode | 🔴 High |
| 2 | `id_branch` | `get_tag_scan_details` | POST | Dropdown | 🟠 Med |
| 3 | `id_metal` | `get_tag_scan_details` | POST | Dropdown | 🟠 Med |
| 4 | `id_section` | `get_tag_scan_details` | POST | Dropdown | 🟠 Med |
| 5 | `id_branch` | `get_nontag_scan_details` | POST | Dropdown | 🟠 Med |
| 6 | `tag_code` | `get_receipt_tag_scan_details` | POST | Barcode | 🔴 High |
| 7 | `$data` fields | `updateNTData` | POST (via controller) | Multiple | 🔴 High |
| 8 | `status` | `ajax_getStockIssueList` | POST | Dropdown | 🟡 Low |
| 9 | `id_profile` | `get_profile_settings` | Session | Session | 🟡 Low |

> **Pattern finding**: All SQL injection in this module follows the same pattern — using string concatenation instead of CI Query Builder or `$this->db->escape()`. The whole codebase was written before the team adopted parameterized queries.
