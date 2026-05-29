# MODULE BRAIN — Stock Issue
> Built: 2026-03-19 | Round: 14 | Status: 🔵 Complete

---

## 1. Module Overview

**Purpose**: Manages the issuance and receipt of jewellery stock (tagged and non-tagged items) from/between branches — to customers, employees, or karigars. Also handles OTP verification for secure issue operations and PDF print of issue acknowledgements.

**Module Boundary**: This module covers stock-out (issue) and stock-in (receipt) movements. It is NOT a sale — it tracks internal and external item handoffs with full audit trail.

### File Map

| File | Lines | Purpose |
|---|---|---|
| `admin/application/controllers/admin_ret_stock_issue.php` | 1,413 | Main controller — routes, save logic, OTP |
| `admin/application/models/ret_stock_issue_model.php` | 1,373 | All DB queries for stock issue module |
| `admin/assets/js/ret_stock_issue.js` | 12,213 | Full client-side logic — form, AJAX, OTP |
| `admin/application/views/ret_stock_issue/list.php` | ~180 | List view — DataTable of all issues |
| `admin/application/views/ret_stock_issue/form.php` | 1,246 | Add/Receipt form — dual-mode (Issue + Receipt) |
| `admin/application/views/ret_stock_issue/issue_ack.php` | ~800 | PDF template — summary acknowledgement |
| `admin/application/views/ret_stock_issue/issue_ack_det.php` | ~800 | PDF template — detailed acknowledgement (per tag) |

### Connection Flow
```
Browser → JS (ret_stock_issue.js)
       → AJAX → Controller (admin_ret_stock_issue.php) → case switch routing
       → Model (ret_stock_issue_model.php)
       → DB: ret_stock_issue, ret_stock_issue_detail, ret_taging, ret_nontag_item, ret_taging_status_log, ret_section_tag_status_log, ret_nontag_item_log, ret_section_nontag_item_log, otp
       → Views (ret_stock_issue/list|form|issue_ack|issue_ack_det)
       → Browser
```

---

## 2. Constructor Analysis

| Dependency | Type | Purpose |
|---|---|---|
| `ret_stock_issue_model` | Model | Primary — all stock issue queries |
| `admin_settings_model` | Model | Branch day-close data, company details, access control |
| `admin_usersms_model` | Model | SMS sending capability |
| `ret_billing_model` | Model | Loaded but not directly called in controller methods |
| `log_model` | Model | Audit log write after each transaction |
| `sms_model` | Model | SMS gateway dispatch (MSG91 / Nettyfish) |
| `dompdf` (library) | Library | PDF generation for issue acknowledgements |

**Session Gate**:
1. Redirect to `admin/login` if not logged in  
2. If `access_time_from` is set → check if `NOW` is within `[from, to]` → redirect to logout if outside window

---

## 3. Entry Points (Routes)

URL pattern: `/admin_ret_stock_issue/{method}` or `/admin_ret_stock_issue/stock_issue/{type}`

| URL / Type | Method | HTTP | Lines | Purpose |
|---|---|---|---|---|
| `stock_issue/list` | `stock_issue('list')` | GET | L140-146 | Load list view |
| `stock_issue/add` | `stock_issue('add')` | GET | L150-162 | Load add/receipt form |
| `stock_issue/save` | `stock_issue('save')` | POST | L165-1070 | Save new issue or receipt |
| `stock_issue/issue_print/{id}` | `stock_issue('issue_print')` | GET | L1074-1098 | Generate summary PDF |
| `stock_issue/issue_print_detail/{id}` | `stock_issue('issue_print_detail')` | GET | L1099-1123 | Generate detailed PDF |
| `stock_issue` (default) | `stock_issue(default)` | POST | L1127-1144 | AJAX list data |
| `get_tag_scan_details` | `get_tag_scan_details()` | POST | L1153-1163 | AJAX — scan tag for issue |
| `get_receipt_tag_scan_details` | `get_receipt_tag_scan_details()` | POST | L1169-1177 | AJAX — scan tag for receipt |
| `get_stock_issue_type` | `get_stock_issue_type()` | GET | L1183-1193 | AJAX — load issue type dropdown |
| `get_StockIssuedItems` | `get_StockIssuedItems()` | POST | L1199-1209 | AJAX — load issued items for receipt |
| `get_nontag_scan_details` | `get_nontag_scan_details()` | POST | L1219-1229 | AJAX — scan non-tag items |
| `stock_issue_sendotp` | `stock_issue_sendotp()` | POST | L1252-1329 | Send OTP via SMS |
| `stock_issue_verify_otp` | `stock_issue_verify_otp()` | POST | L1331-1407 | Verify submitted OTP |

**Utility Methods (Non-routes)**:
| Method | Lines | Purpose |
|---|---|---|
| `shortenurl($url)` | L92-112 | URL shortening via TinyURL API |
| `isValueset($field)` | L116-124 | Returns field or '-' if empty |
| `stock_trans_send_sms($mobile,$message,$dlt)` | L1234-1250 | SMS dispatch wrapper |
| `index()` | L70-76 | Empty — no route |

---

## 4. Stock Issue Type System

`ret_stock_issue_types` table drives behavior:
- `is_remove_from_stock = 1` → tag status log and section log written
- `is_remove_from_stock = 0` → no log entries

**stock_type dimension** (form param):
- `stock_type = 1` → Tagged stock (uses `ret_taging` + `tag_id`)
- `stock_type = 2` → Non-tagged stock (uses `ret_nontag_item`)

**issue_receipt_type dimension** (form radio):
- `issue_receipt_type = 1` → ISSUE (stock goes out)
- `issue_receipt_type = 2` → RECEIPT (stock comes back)

---

## 5. Data Flow Summary

See `DATA_FLOW.md` for full traces. High-level:

**CREATE Issue (Tag)**:  
JS `#stock_issue_submit` → POST `stock_issue/save` → `generateIssueNo()` → INSERT `ret_stock_issue` → INSERT `ret_stock_issue_detail` (per tag) → UPDATE `ret_taging.tag_status=7` → INSERT `ret_taging_status_log` → COMMIT → `log_model`

**CREATE Issue (Non-Tag)**:  
Same flow but inserts into `ret_stock_issue_detail` with `id_non_tag_item` → `updateNTData(.., '-')` (deducts stock) → INSERT `ret_nontag_item_log`

**CREATE Receipt (Tag)**:  
POST `stock_issue/save` (issue_receipt_type=2) → UPDATE `ret_taging.tag_status=0` → UPDATE `ret_stock_issue_detail.status=3` → INSERT `ret_taging_status_log`

**CREATE Receipt (Non-Tag)**:  
UPDATE `ret_nontag_item` (+) → INSERT `ret_nontag_item_log` → UPDATE `ret_stock_issue_detail.status=3`

---

## 6. Key Tables

| Table | Owned | Purpose | Key Columns |
|---|---|---|---|
| `ret_stock_issue` | ✅ | Issue/receipt header | `id_stock_issue`, `issue_no`, `issue_type`, `stock_type`, `issued_to`, `status`, `fin_year` |
| `ret_stock_issue_detail` | ✅ | Per-tag or per-nontag line items | `id_stock_issue`, `tag_id`, `id_non_tag_item`, `status`, `received_time`, `received_date` |
| `ret_taging` | ❌ Referenced | Tag master (status changed) | `tag_id`, `tag_code`, `tag_status`, `current_branch` |
| `ret_taging_status_log` | ❌ Referenced | Tag status history | `tag_id`, `status`, `from_branch`, `to_branch` |
| `ret_section_tag_status_log` | ❌ Referenced | Section-level tag history | `tag_id`, `from_section`, `to_section` |
| `ret_nontag_item` | ❌ Referenced | Non-tag stock levels | `id_nontag_item`, `no_of_piece`, `gross_wt`, `net_wt` |
| `ret_nontag_item_log` | ❌ Referenced | Non-tag movement log | `product`, `design`, `status`, `gross_wt` |
| `ret_section_nontag_item_log` | ❌ Referenced | Section non-tag history | `from_section`, `to_section` |
| `ret_stock_issue_types` | ❌ Referenced | Issue type master | `id_stock_issue_type`, `name`, `is_remove_from_stock` |
| `otp` | ❌ Referenced | OTP store | `mobile`, `otp_code`, `module`, `is_verified` |
| `ret_financial_year` | ❌ Referenced | Current fin year code | `fin_year_code`, `fin_status` |
| `ret_settings` | ❌ Referenced | Module settings | `name`, `value` |
| `profile` | ❌ Referenced | OTP requirement config | `stock_issue_otp_req` |

---

## 7. Form Sections (form.php)

**Complete Hidden Field Inventory** (22 total, verified Round 2):
| ID | Name | Purpose |
|---|---|---|
| `form_secret` | `form_secret` | CSRF-style duplicate submit prevention |
| `id_branch` | `order[order_from]` | Branch for access-restricted users |
| `branch_id_country` | — | Branch country for address |
| `branch_id_state` | — | Branch state |
| `branch_id_city` | — | Branch city |
| `branch_pincode` | — | Branch pincode |
| `branch_id_village` | — | Branch village |
| `issue_to_cus` | `order[issue_to_cus]` | Whether issue type targets customer |
| `cus_id` | `order[cus_id]` | Selected customer ID |
| `cus_mobile` | `order[cus_mobile]` | Customer mobile for OTP |
| `is_otp_verfied` | `order[is_otp_verfied]` | OTP verified flag (0/1) |
| `send_resend` | `order[send_resend]` | OTP send/resend counter |
| `otp_required` | `order[otp_required]` | Profile-driven OTP requirement |
| `goldrate_22ct` | `metal_rates[goldrate_22ct]` | 22ct gold rate (from rate fetch) |
| `silverrate_1gm` | `metal_rates[silverrate_1gm]` | Silver rate |
| `sto_i_increment` | — | Row increment counter for issue items |
| `sto_i_increment` (receipt) | — | Row increment counter for receipt items |
| `issued_branch` | `issued_branch` | In receipt: issuing branch reference |
| `issued_type` | `issued_type` | In receipt: issue type reference |
| (Various per-row tag fields) | `tag_id[]` etc. | Injected dynamically by JS per scanned tag |
| `cus[id_customer]` (modal) | — | In commented-out add-customer modal (dead) |

> ⚠️ Note: `sto_i_increment` appears twice (same ID) for issue table and receipt table — **duplicate DOM ID bug**.

**Toggle Controls**:
- `.tagelement` — shown/hidden based on `#stock_type = 1`
- `.nontagelement` — shown/hidden based on `#stock_type = 2`
- `.type_issue` — shown for ISSUE mode
- `.type_receipt` — shown for RECEIPT mode
- `.customer / .employee / .karigar` — toggled by `#issued_to` value (1/2/3)

---

## 8. Business Rules Summary

See `BUSINESS_RULES.md` for full list. Key rules:
- **RULE-SI-001**: Issue number format = `{fin_year_code}-{5-digit seq}` (e.g., `2425-00001`)
- **RULE-SI-002**: `form_secret` must match session or submit is blocked
- **RULE-SI-003**: Tag status set to `7` (Issued) on issue; reset to `0` on receipt
- **RULE-SI-004**: `is_remove_from_stock = 1` → mandatory status log write
- **RULE-SI-005**: OTP required if `profile.stock_issue_otp_req = 1`
- **RULE-SI-006**: Issue date = day_close date if not today, else current datetime
- **RULE-SI-007**: Non-tag issue deducts stock via arithmetic SQL UPDATE (no separate table)

5 rules extracted. See `BUSINESS_RULES.md` for details.

---

## 9. Cross-Module Dependencies

See `CROSS_MODULE_MAP.md` for full map. Key dependencies:
- **Estimation** module: AJAX call to `admin_ret_estimation/get_employee`
- **Catalog**: AJAX call to `admin_ret_catalog/karigar/active_list`
- **Settings**: `admin_settings_model.getBranchDayClosingData()`, `getCompanyDetails()`
- **Log**: `log_model.log_detail()` writes audit after every transaction

---

## 10. Known Risks (Pre-Identified)

| # | Risk | Severity | Location |
|---|---|---|---|
| R-01 | Debug `echo $this->db->last_query(); exit;` in rollback path (Tagged branches kill rollback) | 🔴 CRIT | Controller L415, L547 |
| R-02 | SQLi in `get_tag_scan_details` — raw `$tag_code`, `$id_branch`, `$id_metal`, `$id_section` | 🔴 CRIT | Model L733-739 |
| R-03 | SQLi in `get_nontag_scan_details` — raw `$id_branch`, `$prodId`, `$id_section` | 🔴 CRIT | Model L1312-1314 |
| R-04 | SQLi in `get_profile_settings` — raw `$id_profile` (session-sourced) | 🟠 MED | Model L74 |
| R-05 | Race condition in `generateIssueNo()` — no DB lock; concurrent saves may duplicate | 🟠 MED | Model L112 |
| R-06 | N+1 query: `get_stock_issue_det()` called per row in `ajax_getStockIssueList` loop | 🟡 LOW | Model L232 |
| R-07 | `$issue_date` undefined in Tagged Receipt branch — inserted as NULL to status logs | 🟠 MED | Controller L463+ |
| R-08 | `ret_billing_model` loaded in constructor but never used | 🟡 LOW | Controller L29 |
| R-09 | OTP value returned in JSON response (`'OTP' => $OTP`) — bypasses OTP purpose | 🔴 CRIT | Controller L1312 |
| R-10 | `stock_issue_verify_otp` opens `trans_begin()` with no `trans_rollback()` on failure | 🟠 MED | Controller L1343 |
| R-11 | Hardcoded 3% GST in PDF template — ignores `ret_taxmaster.tax_percentage` | 🔴 CRIT | issue_ack.php L569 |
| R-12 | Duplicate DOM ID `sto_i_increment` (issue + receipt tables both use same ID) | 🟡 LOW | form.php |
| R-13 | SQLi in `ajax_getStockIssueList` status filter — raw `$data['status']` | 🟠 MED | Model L195 |
| R-14 | SQLi in `get_stock_issue_StoneDetails` — raw `$tag_id` (from DB result loop) | 🟡 LOW | Model L854 |
| R-15 | SQLi in `get_receipt_tag_scan_details` — raw `$tag_code` from POST | 🔴 CRIT | Model L925 |
| R-16 | All 6 fields raw in `updateNTData` arithmetic UPDATE — includes POST data | 🔴 CRIT | Model L1346 |
| R-17 | `$result` undefined in `get_nontag_scan_details` if all rows have `gross_wt ≤ 0` | 🟠 MED | Model L1340 |
| R-18 | `$insId` undefined in NonTag Receipt success response (only set in Issue branch) | 🟠 MED | Controller L1036 |
| R-19 | Raw `$id`/`$tag_id` in `stock_issue_type_detail()` and `getTagDetails()` (internal data) | 🟡 LOW | Model L1031, L1229 |
| R-20 | N+1 in `get_StockIssuedItems()` — `stock_issue_tags()`/`stock_issue_nontags()` per issue | 🟡 LOW | Model L1055, L1077 |
| R-21 | Typo `'portriat'` (should be `'portrait'`) in dompdf orientation — PDF layout broken | 🟡 LOW | Controller L1092, L1117 |
| R-22 | XSS: raw `data.msg` from server injected into OTP modal DOM via `.append()` | 🔴 CRIT | JS L1751, L1801 |
| R-23 | `async: false` on OTP send + verify AJAX — blocks browser UI thread | 🟠 MED | JS L1527, L1719 |
| R-24 | 3 `console.log()` calls in production JS — leaks response data to browser devtools | 🟡 LOW | JS L225, L1225, L1331 |

---

## 11. DB Verification Queries

```sql
-- Full transaction view by ID
SELECT si.*, GROUP_CONCAT(sid.tag_id) as tags
FROM ret_stock_issue si
LEFT JOIN ret_stock_issue_detail sid ON sid.id_stock_issue = si.id_stock_issue
WHERE si.id_stock_issue = {ID}
GROUP BY si.id_stock_issue;

-- Orphan check: detail rows without parent
SELECT sid.id_stock_issue_detail FROM ret_stock_issue_detail sid
LEFT JOIN ret_stock_issue si ON si.id_stock_issue = sid.id_stock_issue
WHERE si.id_stock_issue IS NULL;

-- Tags stuck in status 7 (issued) but no active issue record
SELECT t.tag_id, t.tag_code FROM ret_taging t
LEFT JOIN ret_stock_issue_detail sid ON sid.tag_id = t.tag_id AND sid.status = 1
WHERE t.tag_status = 7 AND sid.tag_id IS NULL;

-- Issue number gaps check
SELECT issue_no, fin_year FROM ret_stock_issue ORDER BY fin_year, CAST(SUBSTRING_INDEX(issue_no,'-',-1) AS UNSIGNED);
```

---

## 12. Codebase Notes

- Controller uses a large `switch($type)` pattern — all routes go through `stock_issue()` method
- Model uses generic `insertData/updateData/deleteData` helpers + named query methods
- JS is 12K+ lines — **majority is blank lines + large commented-out duplicate block (L1927-L2400+)** — effective active code is much smaller
- Multiple debug lines left in production code (L415, L547, L842, L1046)
- `ret_billing_model` loaded but unused — technical debt (reason unknown, likely dead code from old feature)
- **Duplicate DOM ID**: `sto_i_increment` appears twice in form.php — once for issue table, once for receipt table
- `issue_ack.php` uses local PHP helper: `moneyFormatIndia($num)` — formats number in Indian comma style
- PDF title says "DELIVERY CHALLAN" — standard trade document for jewellery handoffs
- `list.php` status filter values: 0=All, 1=Issued, 2=Rejected, 3=Received — passed as `status` in AJAX POST to default case
- `issue_ack_det.php` is structurally identical to `issue_ack.php` but uses `get_issue_item_tag()` (per-tag rows, no GROUP BY) instead of `get_issue_item_details()`

---

## 13. Anti-Patterns Register

> Identified across Rounds 1-4. Use when reviewing future bugs in this module.

| ID | Pattern | Bugs | Example File | Fix Template |
|---|---|---|---|---|
| AP-01 | **Raw string concat in SQL** | R-02, R-03, R-04, R-13, R-14, R-15, R-16 | `ret_stock_issue_model.php` | `(int)$val` or `$this->db->escape($val)` or query builder |
| AP-02 | **Debug echo+exit in production paths** | R-01 | `admin_ret_stock_issue.php` | Remove; use `log_message('error',...)` |
| AP-03 | **Undefined variable across branch** | R-07, R-17, R-18 | Controller save path | Initialize before branch split; default to `null`/`[]` |
| AP-04 | **Trans_begin without matching rollback** | R-10 | `stock_issue_verify_otp` | Add rollback to every failure path |
| AP-05 | **Sensitive data in JSON response** | R-09 | `stock_issue_sendotp` | Never return OTP/secret in API response |
| AP-06 | **Hardcoded business constants in view** | R-11 | `issue_ack.php` | Pass rate from model/DB; never hardcode in template |
| AP-07 | **N+1 DB call inside foreach** | R-06, R-20 | `ajax_getStockIssueList` | Consolidate into JOIN or GROUP BY subquery |
| AP-08 | **MAX() without lock for sequence generation** | R-05 | `generateIssueNo()` | Use `SELECT FOR UPDATE` or sequence table |
| AP-09 | **Dead model dependencies in constructor** | R-08 | `__construct()` | Remove unused model loads; audit constructor on each module |
| AP-10 | **Duplicate DOM IDs (sto_i_increment)** | R-12 | `form.php` L487+L741 | Suffix with `_issue` / `_receipt` to make unique |
| AP-11 | **Duplicate DOM IDs (searchEstiAlert)** | R-26 | `form.php` L437+L707 | JS `$('#searchEstiAlert')` picks first match — receipt messages show in issue section |
| AP-12 | **Raw server data in DOM via JS concat** | R-22 | `ret_stock_issue.js` L1751/L1801 | Always use `.text()` for server strings in DOM |
| AP-13 | **Synchronous AJAX blocks UI thread** | R-23 | `ret_stock_issue.js` L1527/L1719 | Remove `async: false`; use success/error callbacks |
| AP-14 | **Debug console.log in production JS** | R-24 | `ret_stock_issue.js` L225/L1225/L1331 | Remove all console.log before release |
| AP-15 | **Unescaped server flash in PHP view** | R-25 | `form.php` L91 | Wrap with `htmlspecialchars()` |

---

## 14. JS Layer Risks (Rounds 7 + 14)

> `ret_stock_issue.js` — 12,213 lines total | ~1,927 active lines (dead block L1927-L2400+ is commented code)

### R-22 — XSS via `.append()` in OTP Modal (🔴 Critical)

**Location**: JS ~L1751 and L1801 (OTP send + resend success callbacks)  
**Attack**: Server-controlled `data.msg` string injected into DOM via string concatenation in `.append()`:
```javascript
// ❌ Bug:
$(".otp_alert").append('<p style="color:green">' + data.msg + '</p>');
```
**OTP modal target div**: `form.php L1223` — `<span class="otp_alert"></span>`  
**Fix**: `$('<p>').css('color','green').text(data.msg).appendTo('.otp_alert');`

---

### R-23 — Synchronous AJAX Freezes Browser (🟠 Medium)

**Location**: JS L1527 (OTP send) and L1719 (OTP verify)  
**Impact**: `async: false` deprecated in modern jQuery/browsers; blocks the UI thread until SMS gateway responds (can be 2-10 seconds)  
**Fix**: Remove `async: false` — jQuery's default is already async. Convert result handling to `success:` callback.

---

### R-24 — Debug `console.log` in Production (🟡 Low)

**Locations**: JS L225 (issue type list), L1225 (NT item save response), L1331 (save result)  
**Impact**: Leaks internal response structure to anyone with DevTools open  
**Fix**: Remove all 3 before release; only `console.error` for actual errors

---

## 15. View Layer Risks (Round 14)

> `form.php` — 1,246 lines | 100% read (Rounds 12 + 14)

### R-25 — Unescaped Flash Message Echo (🟡 Low)

**Location**: `form.php` L91  
**Code**: `<?php echo $message['message']; ?>`  
**Risk**: Low — flash data is set server-side, not from user input directly. But consistent policy requires `htmlspecialchars()` for all echo'd content.  
**Fix**: `<?php echo htmlspecialchars($message['message'], ENT_QUOTES, 'UTF-8'); ?>`

---

### R-26 — Duplicate DOM ID `searchEstiAlert` (🟡 Low)

**Locations**: `form.php` L437 (Issue section), L707 (Receipt section)  
**Code**: `<p id="searchEstiAlert" class="error" ...></p>` — appears **twice**  
**Impact**: JS selector `$('#searchEstiAlert')` always picks the first match (L437). Scan error messages in the Receipt section never display because the wrong element is targeted.  
**Fix**: Rename to `id="issue_searchEstiAlert"` and `id="receipt_searchEstiAlert"` and update JS selectors accordingly.

---

> **Brain build complete. 13 rounds of source scanning. 26 bugs. 16 docs.**  
> All layers covered: Controller ✅ | Model ✅ | JS ✅ | Views 100% ✅  
> Start fixing: `/fix-single-bug R-01`

