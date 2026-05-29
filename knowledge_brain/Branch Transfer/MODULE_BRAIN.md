# Module Brain - Inventory (Branch Transfer)

> **🔄 UPGRADED BRAIN**
> Previous version: Round 9 (2026-03-24)
> Upgraded to: Round 10 (2026-03-24)
> Changes: JS 8,786 → 4,483 lines (dead duplicate blocks removed), Controller 1,386 → 1,375 lines, Model 2,248 → 2,137 lines active. 1 new referenced table (`metal`). `ret_billing_item_stones` confirmed ACTIVE. `get_purchase_items()` upgraded to metal-agnostic + `dia_wt` support.

## 1. Module Overview
* **Controller**: `admin_ret_brntransfer.php` (1,375 lines, 12 methods + 18 switch sub-routes) — Handles branch transfer CRUD and approval logic.
* **Model**: `ret_brntransfer_model.php` (2,137 lines active, 56 methods incl. constructor) — Primary database interactions. ⚠️ ~300 lines are dead-code comment-blocked deprecated method stubs (L1155–L1458).
* **JS**: `ret_branch_transfer.js` (4,483 lines, 51 named functions, 30 internal + 7 cross-module AJAX calls) — Client-side validation, dynamic table management, and AJAX submissions. ✅ Old commented duplicate function blocks removed in R10.
* **Views**: `application/views/branch_transfer/` (8 files) — form.php, list.php, approval_list.php, print.php, bt_category_print.php, + 3 legacy files.
* **DB Tables**: 5 owned + 29 referenced = 34 total tables touched (see SCHEMA_ANALYSIS.md). (+1 `metal` table added R10)

**Connection Flow:**
`Browser → JS (ret_branch_transfer.js) → AJAX → Controller (admin_ret_brntransfer) → Model (ret_brntransfer_model) → DB (ret_branch_transfer, ret_taging, etc.) → View (branch_transfer/*) → Browser`

## 2. Constructor Analysis
| Model/Library | Purpose |
|---|---|
| `ret_brntransfer_model` | Primary data access for Branch Transfer |
| `sms_model` | Handles SMS sending related to transfers/OTPs |
| `log_model` | Activity and transaction logging |
| `ret_billing_model` | Used for integrating billing items (partly sale/sales return) in transfers |
| `admin_settings_model` | Settings, configs, and access control |
| `admin_usersms_model` | SMS service model |

**Session Gate:**
Checks `is_logged`. Enforces strict time-based access via `access_time_from` and `access_time_to` constraints. Fails and redirects to `chit_admin/logout` if out of bounds.

## 3. Entry Points & Routes

| URL Path | HTTP Method | Controller Method | Purpose |
|---|---|---|---|
| `/admin_ret_brntransfer/branch_transfer/list` | GET | `branch_transfer('list')` | Load transfer listing page |
| `/admin_ret_brntransfer/branch_transfer/add` | GET | `branch_transfer('add')` | Load add transfer form |
| `/admin_ret_brntransfer/branch_transfer/approval_list` | GET | `branch_transfer('approval_list')` | Load approval dashboard |
| `/admin_ret_brntransfer/branch_transfer/save` | POST (AJAX) | `branch_transfer('save')` | Save new branch transfer |
| `/admin_ret_brntransfer/branch_transfer/updateStatus` | POST (AJAX) | `branch_transfer('updateStatus')` | Approve Transit/Stock Download |
| `/admin_ret_brntransfer/branch_transfer/getTagsByFilter` | POST (AJAX) | `branch_transfer('getTagsByFilter')` | Fetch matching tags |
| `/admin_ret_brntransfer/branch_transfer/getTagsByFilter_scan` | POST (AJAX) | `branch_transfer('getTagsByFilter_scan')`| Scan barcode to fetch tag |
| `/admin_ret_brntransfer/branch_transfer/print` | GET/POST | `branch_transfer('print')` | Generate transfer printout |
| `/admin_ret_brntransfer/update_branch_transfer_cancel` | POST | `update_branch_transfer_cancel()` | Cancel branch transfer |
| `/admin_ret_brntransfer/verify_otp` | POST (AJAX) | `verify_otp()` | Verify OTP for transfer approval |

*(Note: Controller uses a single `branch_transfer($type)` hub method acting as a router for most functionality.)*

## 4. Model Methods Summary
The `ret_brntransfer_model.php` contains **56 methods** (incl. constructor).
- **Generic CRUD:** 4 methods (`insertData`, `updateData`, `updateDatamulti`, `deleteData`)
- **Filter/Fetch Grids:** 14 methods (tags, non-tags, lots, designs, products, approval listing)
- **Transfer Validations & Logic:** 12 methods (status checks, NT exist, OTP, head office)
- **Print/Report Data:** 6 methods (`getBTransData`, `getBTransDataSummary`, `get_download_data`, etc.)
- **Cross-module Fetch:** 10 methods (purchase items, sales returns, partly sales, old metal, repairs)
- **Packaging/Order:** 7 methods (inventory category, purchase details, order log, repair tags)
- **Utility:** 3 methods (constructor, trans_code_generator, get_last_trans_code)

[See METHOD_INDEX.md for full list with tables and callers.](file:///c:/xampp_7.4/htdocs/etailv3/knowledge_brain/Branch%20Transfer/METHOD_INDEX.md)

## 5. Data Flow Summary
- **CREATE:** Master record inserted into `ret_branch_transfer` alongside child records depending on `item_tag_type`. Strict piece counts and OTP rules apply.
- **UPDATE:** Approvals transition state from 1 (Pending) -> 2 (Transit) -> 4 (Downloaded). Stock operations subtract and add from module-specific tables during these transitions.
- **CANCEL:** Sets master state to 3 and halts all subsequent stock updates.
[See DATA_FLOW.md for detailed breakdown.](file:///C:/Users/Admin/.gemini/antigravity/brain/1c4f9651-77fb-4c01-b8ab-e2bbf19b9748/DATA_FLOW.md)

## 6. Key Tables
* `ret_branch_transfer`: Master table (PK: `branch_transfer_id`, status: 1/2/3/4).
* `ret_brch_transfer_tag_items`: Tagged child (FK: `transfer_id` → master, `tag_id` → `ret_taging`).
* `ret_brch_transfer_non_tag_items`: Non-Tagged child (FK: `transfer_id`, `id_nontag_item` → `ret_nontag_item`).
* `ret_brch_transfer_old_metal`: Old Metal/SR/PS child (`item_type`: 1=OM, 2=SR, 3=PS).
* `ret_branch_transfer_other_inventory`: Packaging child (FK: `id_other_inv_item`).
* **+ 26 referenced tables** — see [SCHEMA_ANALYSIS.md](file:///c:/xampp_7.4/htdocs/etailv3/knowledge_brain/Branch%20Transfer/SCHEMA_ANALYSIS.md) for full breakdown including 6 security risks.

## 7. Form Sections & Hidden Fields
### form.php (10 hidden inputs)
| ID | Purpose | Populated By |
|---|---|---|
| `form_secret` | CSRF duplicate submission guard | `get_form_secret_key()` helper |
| `other_issue_branch` | Branch ID for "Other Issue" transfers | `getSettigsByName('other_issue_branch')` |
| `is_otp_required_for_approval` | OTP toggle | `getSettigsByName(...)` |
| `head_office_branch` | Head office branch ID | `get_headoffice_branch()` |
| `logged_gst` | GST state of logged-in user | Empty (JS populated) |
| `from_brn` | Logged-in user's branch | Session `id_branch` |
| `is_eda` | EDA mode flag | Hardcoded `1` |
| `id_product` | Product autocomplete | JS autocomplete |
| `id_product` (L288) | **⚠️ DUPLICATE** — second product autocomplete (different form section) | JS autocomplete |
| `id_design` | Design autocomplete | JS autocomplete |

> ⚠️ **BUG**: Two `id="id_product"` hidden inputs at L267 and L288 — duplicate DOM ID.

### approval_list.php (21 hidden inputs)
| ID | Purpose |
|---|---|
| `form_secret` | CSRF guard |
| `filtr_to_brn` | Filter: logged branch |
| `id_product` | Product filter |
| `required_otp_approval` | OTP toggle |
| `other_issue_branch` | Other issue branch |
| `head_office_branch` | HO branch ID |
| `branch_trans_dnload` | Download setting |
| `dnload_trans_id` | Active download ID |
| `branch_trans_from_branch` | From branch (set by JS) |
| `branch_trans_to_branch` | To branch (set by JS) |
| `actual_pcs_dnload` | Actual pieces for download |
| `actual_weights_dnload` | Actual weights for download |
| `is_eda_appr` | EDA mode flag |
| `BT_otp_approval_type` | OTP approval type (from profile) |
| `uid` | User ID |
| `appr_trans_id` | Approval transaction ID |
| `approval_id` | Approval record ID |
| `apprl_status` | Approval status |
| `approval_type` | Transit (1) / Download (2) |
| `cliIDcode` | Client ID code |
| `id_design` | Design filter |

## 8. Business Rules Summary
Contains **15 rules** (BRT-001 through BRT-015) covering: item type routing, piece count integrity, Day Close validation, status transitions, OTP requirements, CSRF guards, Other Issue differentiation, packaging FIFO/LIFO, scan auto-completion, order tag handling, cancel-no-reversal gap, Head Office exception, tag availability client-only, and PS/SR duplicate-transfer gap.
[See BUSINESS_RULES.md](file:///c:/xampp_7.4/htdocs/etailv3/knowledge_brain/Branch%20Transfer/BUSINESS_RULES.md)

## 9. Cross-Module Dependencies
Heavily integrated with `Tagging`, `Billing`, `Purchase`, `Order/Repair`, `Packaging`, `Catalog`, `App API`, and `SMS` modules. 5 models loaded in constructor + 7 cross-module JS AJAX calls. Mermaid dependency graph available.
[See CROSS_MODULE_MAP.md](file:///c:/xampp_7.4/htdocs/etailv3/knowledge_brain/Branch%20Transfer/CROSS_MODULE_MAP.md)

## 10. Known Risks
- **Cancel No-Reversal (CRITICAL):** Cancelling a transit-approved transfer does NOT reverse stock changes. Tags/NT remain in wrong state. (RULE-BRT-012, Anti-Pattern #5, FR-BRT-004)
- **Tag Availability — No Server-Side Guard (HIGH):** `branch_transfer('save')` does not verify `tag_status=0` before inserting tag_items. A crafted POST can add an in-transit or sold tag to a new transfer. (RULE-BRT-014, FR-BRT-001)
- **PS/SR Duplicate Transfer — No Dedup Guard (HIGH):** Same `bill_det_id` or `tag_id` can appear in two parallel active transfers. No server-side check. Double-counting risk at download. (RULE-BRT-015, FR-BRT-002, FR-BRT-015)
- **SQL Injection (HIGH):** 5 model methods use `$this->db->query()` with string concatenation. See SCHEMA_ANALYSIS.md Part C.
- **Information Leak (MED):** `$this->db->_error_message()` exposed to client at L272-273 and L932.
- **Concurrency in Statuses (MED):** `updateStatus` iteratively accesses tagging tables without row-level locking.
- **Temporal Desyncs (MED):** Day-Close validation assumes linear closing. Backlogged branches stall transits.
- **Packaging Shortfall (MED):** If available packaging items < requested, LIMIT silently returns fewer — no validation.
- **DomPDF Typo (LOW):** `set_paper("a4", "portriat")` — misspelled "portrait" at L1123.

## 11. DB Verification Queries
See [FORENSIC_TEMPLATE.md Layer 5](file:///c:/xampp_7.4/htdocs/etailv3/knowledge_brain/Branch%20Transfer/FORENSIC_TEMPLATE.md) for 7 comprehensive diagnostic queries:
1. Full Transaction Trace (master + all child types)
2. Tagged piece count integrity check
3. Orphan records check (all 4 child tables)
4. Tags stuck in transit > 7 days
5. Non-tag weight integrity (header vs child sum)
6. Cancelled transfers with prior transit approval (stock desync)
7. Packaging FIFO/LIFO integrity check

## 12. Codebase Notes
- Controller routing relies on a single `branch_transfer($type)` hub method with 18+ switch cases (800+ lines). Standard CI3 `class/method/param` conventions bypassed.
- Massive nested IFs handle `item_tag_type` differentiation during Save (L79-283, 5 branches) and UpdateStatus (L285-938, 5 sub-types × 2 approval types).
- JS file is 8,879 lines — the largest single JS file in the project for this module.
- Print views are very large: `print.php` (85KB), `bt_category_print.php` (96KB).

## 13. Anti-Patterns Register
| # | Pattern | Location | Severity | Status | Notes |
|---|---|---|---|---|---|
| 1 | **Hub Routing** | Controller `branch_transfer()` L37-1148 | MED | Open | 18 actions in one switch case. Should be separate controller methods. |
| 2 | **Raw SQL Injection** | Model L1912, L1922, L1932, L1970, L1976 | HIGH | ✅ **FIXED 2026-03-12** | 5 methods use `$this->db->query()` with string concatenation. |
| 3 | **Monolithic Loop** | Controller `updateStatus` L285-938 | MED | Open | 650+ lines of nested type/approval logic in a single case block. |
| 4 | **Error Leak** | Controller L272-273, L932 | MED | Open | `$this->db->_error_message()` + `$this->db->last_query()` exposed in JSON response. |
| 5 | **Cancel No-Reversal** | Controller L1261-1292 | HIGH | Open | Only updates master status=3. No stock/log reversal. Data integrity gap. |
| 6 | **DomPDF Typo** | Controller L1123 | LOW | Open | `"portriat"` should be `"portrait"`. DomPDF may silently accept. |
| 7 | **$_POST Direct Access** | Controller L81, L95-106, etc. | MED | Open | Uses `$_POST` directly instead of `$this->input->post()` throughout. |
| 8 | **SELECT * in Raw SQL** | Model L1922, L1932, L1970 | LOW | Open | `SELECT *` in raw queries — violates column-specific select rule. |
| 9 | **Duplicate DOM ID** | View form.php L267 + L288 | MED | ✅ **FIXED 2026-03-12** | Two `id="id_product"` hidden inputs — jQuery `$('#id_product')` always returns first, second is shadow. |
| 10 | **Orphaned trans_begin (read-only fn)** | Controller `verify_otp()` L1154 (removed) | P0 | ✅ **FIXED 2026-03-11** | `trans_begin()` in function with zero DB writes — session reads only. Dangling transaction on every call. See BRN-101. |
| 22 | **Dead-Code Duplicate Method Stubs** | Model L1155–L1458 | LOW | Open | 4 old 3/4-param method versions wrapped in `/*...*/` comment block. Replaced by 5-param versions at L1462+. Bloats file by ~300 lines. Candidate for cleanup. |

### BRN-101: Orphaned trans_begin in verify_otp() ✅ FIXED

| Field | Value |
|---|---|
| **Bug ID** | BRN-101 |
| **Anti-Pattern** | `trans_begin()` called in a function that has **zero DB writes** (only session reads) |
| **Fix Applied** | Removed `$this->db->trans_begin()` from `verify_otp()` at L1154 |
| **Date Fixed** | 2026-03-11 |
| **Pattern** | PAT-TXN-004 (new) |

**Root cause**: The function existed only to compare `$session_otp` vs `$post_otp` and return a JSON status. No DB operations of any kind. The `trans_begin()` was a copy-paste artifact from save/approval functions.

**Prevention**: Before adding `trans_begin()` to any function, verify at least one DB write (`insert`, `update`, `delete`) exists in the code path. If the function only reads from DB or only touches session data, no transaction is needed.

**"Why it was done this way" note**: The OTP verification function was likely templated from an OTP-sending function which does have DB writes (`insertData` for OTP log). The transaction block was copy-pasted wholesale without checking whether this specific function needed it.

### BRN-102: trans_commit Before updateData() in verify_other_issue_otp() ✅ FIXED

| Field | Value |
|---|---|
| **Bug ID** | BRN-102 |
| **Anti-Pattern** | `trans_commit()` called **before** `updateData()` — DB write ran outside the transaction. Failure paths (expired/invalid OTP) left `trans_begin()` open with no `trans_rollback()`. Also: `updateData()` return value ignored — silent failure still returned `status:true`. |
| **Fix Applied** | Moved `trans_begin()` to wrap only the DB write path; added `trans_status()` + `!$updStatus` guard; all non-write paths have no transaction to close |
| **Date Fixed** | 2026-03-11 |
| **Patterns** | PAT-TXN-001 + PAT-TXN-003 (composite) |

> **Anti-pattern entry #11 in register:**

| # | Pattern | Location | Severity | Status |
|---|---|---|---|---|
| 11 | **Commit-Before-Write (trans misorder)** | Controller `verify_other_issue_otp()` (restructured) | P0 | ✅ **FIXED 2026-03-11** |

**Root cause**: The developer wrote `trans_commit()` at the start of the success branch — before calling `updateData()`. This means the DB write always ran in auto-commit mode regardless of transaction. The transaction was a no-op. Failure paths (expired OTP at L1344, invalid OTP at L1355) called neither `trans_commit()` nor `trans_rollback()`, leaving the handle open.

**Prevention**: Always follow the pattern: `trans_begin()` → DB writes → `if (trans_status() === FALSE || !$result) { trans_rollback(); } else { trans_commit(); }`. The commit must be the LAST operation after validating all writes succeeded.

**"Why it was done this way" note**: The original code appears to have been structured as "commit the session verification, then do the write" — treating the OTP check itself as the transaction subject rather than the DB update. This conceptual error (transacting the check, not the write) is a common pattern in code written by developers who copy-paste transaction blocks without fully understanding their purpose.

### BRN-104: Raw $_POST Used Throughout ✅ FIXED

| Field | Value |
|---|---|
| **Bug ID** | BRN-104 |
| **Anti-Pattern** | Used raw `$_POST` array access 104 times instead of CodeIgniter's `$this->input->post()` method, bypassing automated XSS filtering. |
| **Fix Applied** | Regex replacement of `$_POST['key']` to `$this->input->post('key')` globally across the controller. |
| **Date Fixed** | 2026-03-12 |
| **Pattern** | PAT-SEC-002 |

> **Anti-pattern entry #12 in register:**

| # | Pattern | Location | Severity | Status |
| 12 | **Raw $_POST Bypass** | Controller `admin_ret_brntransfer.php` (global) | P1 | ✅ **FIXED 2026-03-12** |

### BRN-D01: Variable Mismatch Crash in getProductsByFilter() ✅ FIXED

| Field | Value |
|---|---|
| **Bug ID** | BRN-D01 |
| **Anti-Pattern** | Assigned DB query output to `$result` but returned `$data->result_array()`, resulting in a fatal crash on undefined variable `$data`. |
| **Fix Applied** | Replaced `$data->result_array()` with `($result) ? $result->result_array() : []` to ensure null-safety and proper variable use. |
| **Date Fixed** | 2026-03-12 |
| **Pattern** | PAT-VAR-001 |

> **Anti-pattern entry #13 in register:**

| # | Pattern | Location | Severity | Status |
|---|---|---|---|---|
| 13 | **Copy-Paste Variable Mismatch** | Model `getProductsByFilter()` | P0 | ✅ **FIXED 2026-03-12** |

**Prevention**: When copy-pasting DB query templates, carefully verify that the assignment variable (e.g., `$result`) exactly matches the return variable. Add static analysis or syntax checks to validate variable definitions.

**Root cause**: Legacy PHP practices carried over into the CodeIgniter framework. Direct access to superglobals bypasses the framework's security middleware.

### BRN-D02: updateDatamulti() Returns Undefined $id_value ✅ FIXED

| Field | Value |
|---|---|
| **Bug ID** | BRN-D02 |
| **Anti-Pattern** | Copied code from `updateData()` (which takes `$id_value` as param) but failed to update the return statement, referencing an undefined variable. |
| **Fix Applied** | Replaced `return ($edit_flag == 1 ? $id_value : 0);` with `return $edit_flag;` |
| **Date Fixed** | 2026-03-12 |
| **Pattern** | PAT-VAR-001 |

> **Anti-pattern entry #14 in register:**

| # | Pattern | Location | Severity | Status |
|---|---|---|---|---|
| 14 | **Copy-Paste Variable Mismatch** | Model `updateDatamulti()` | P1 | ✅ **FIXED 2026-03-12** |

**Prevention**: When duplicating boilerplate functions across the model, double-check all variables referenced in the function body against the parameter list.
**Prevention**: Always use `$this->input->post('field', TRUE)` for all form inputs. A linting rule or pre-commit hook scanning for `$_POST` can enforce this automatically.

### BRN-D03: SQL Injection Vulnerability in Model (50+ points) ✅ FIXED

| Field | Value |
|---|---|
| **Bug ID** | BRN-D03 |
| **Anti-Pattern** | Direct concatenation of user-controlled variables into raw SQL queries (e.g., `WHERE col = ` . $var). |
| **Fix Applied** | Systematically wrapped vulnerable variables with `$this->db->escape()` and `$this->db->escape_like_str()` across 12+ methods. |
| **Date Fixed** | 2026-03-12 |
| **Pattern** | PAT-SEC-001 |

**Prevention**: Never concatenate variables directly into SQL strings. Always use Query Builder active record (`$this->db->where()`) or explicitly sanitize inputs using `$this->db->escape()` before mapping to raw SQL parameters.

### BRN-D09: Undefined $FromDt Array Clearing ✅ FIXED

| Field | Value |
|---|---|
| **Bug ID** | BRN-D09 |
| **Anti-Pattern** | Parameter `$from_date` was not correctly mapped to variable check; instead, an undefined variable `$FromDt` was referenced causing the method logic to automatically drop branch dataset on `allow_bill_type=2` filters. |
| **Fix Applied** | Renamed `$FromDt` to match the exact function parameter passed in `$from_date`. |
| **Date Fixed** | 2026-03-12 |
| **Pattern** | PAT-VAR-001 |

> **Anti-pattern entry #15 in register:**

| # | Pattern | Location | Severity | Status |
|---|---|---|---|---|
| 15 | **Copy-Paste/Typo Variable Mismatch** | Model `get_ajaxBranchTransferlist()` | P0 | ✅ **FIXED 2026-03-12** |

**Prevention**: When working with query parameter strings in isolated PHP framework methods, ensure variable inputs correctly reflect the designated parameter signatures. Add syntax checks or linting rules for checking `Undefined variable` exceptions.

### BRN-D16: getNontagItemId() null-crash ✅ FIXED

| Field | Value |
|---|---|
| **Bug ID** | BRN-D16 |
| **Anti-Pattern** | Directly accessing property array `->row()->property` assuming result counts > 0 without fallback `num_rows()` guard to prevent FATAL crash errors resolving objects. |
| **Fix Applied** | Wrapped the model method getter in `if ($sql && $sql->num_rows() > 0)` and defaulted early to `return 0;`. |
| **Date Fixed** | 2026-03-12 |
| **Pattern** | PAT-VAR-001 |

> **Anti-pattern entry #16 in register:**

| # | Pattern | Location | Severity | Status |
|---|---|---|---|---|
| 16 | **Null Object Reference / Safety Access** | Model `getNontagItemId()` | P0 | ✅ **FIXED 2026-03-12** |

**Prevention**: Never implicitly chain `->row()` pointer methods without testing `$sql->num_rows() > 0` conditionally prior.

### BRN-103: Live Error / Query Leak in Save Failure ✅ FIXED

| Field | Value |
|---|---|
| **Bug ID** | BRN-103 |
| **Anti-Pattern** | Directly echoing `last_query()` and `_error_message()` output back in raw payload execution strings directly rendering to the client's failure responses exposing internal structures. |
| **Fix Applied** | Replaced the raw assignments mapped to the response string executing `log_message('error'...)` natively recording to server logs rather than HTTP output. |
| **Date Fixed** | 2026-03-11 |
| **Pattern** | PAT-SEC-005 |

> **Anti-pattern entry #5 in register:**

| # | Pattern | Location | Severity | Status |
|---|---|---|---|---|
| 5 | **Information Leak (Server Diagnostics)** | Controller `admin_ret_brntransfer.php:save` | P1 | ✅ **FIXED 2026-03-11** |

**Prevention**: Log DB failures and exceptions to server-only error tracking using `log_message`. Never inject SQL outputs backward into JSON strings mapped dynamically for browser rendering.

### BRN-D17: get_headoffice_branch() null-crash ✅ FIXED

| Field | Value |
|---|---|
| **Bug ID** | BRN-D17 |
| **Anti-Pattern** | Directly accessing property array `->row()->property` assuming result counts > 0 without fallback `num_rows()` guard to prevent FATAL crash errors resolving objects. |
| **Fix Applied** | Wrapped the model method getter in `if ($sql && $sql->num_rows() > 0)` and defaulted early to `return 0;`. |
| **Date Fixed** | 2026-03-12 |
| **Pattern** | PAT-VAR-001 |

> **Anti-pattern entry #17 in register:**

| # | Pattern | Location | Severity | Status |
|---|---|---|---|---|
| 17 | **Null Object Reference / Safety Access** | Model `get_headoffice_branch()` | P0 | ✅ **FIXED 2026-03-12** |

**Prevention**: Never implicitly chain `->row()` pointer methods without testing `$sql->num_rows() > 0` conditionally prior.

### BRN-D37: get_headoffice_branch() Hardcoded `transfer_to` ✅ FIXED

| Field | Value |
|---|---|
| **Bug ID** | BRN-D37 |
| **Anti-Pattern** | Hardcoding the mapped target parameter payload (`'transfer_to' : 1`) inside javascript data submissions rather than reading current DOM-selected states dynamically. |
| **Fix Applied** | Traded static `1` mapping inside OM parameter injection for variable `to_brn` evaluation capturing active dropdown input. |
| **Date Fixed** | 2026-03-12 |
| **Pattern** | PAT-VAR-002 |

> **Anti-pattern entry #18 in register:**

| # | Pattern | Location | Severity | Status |
|---|---|---|---|---|
| 18 | **Hardcoded Routing Values** | UI `ret_branch_transfer.js:add_to_trans()` | P0 | ✅ **FIXED 2026-03-12** |

**Prevention**: Read active component identifiers directly instead of hardcoding numerical strings when dynamically building POST payloads internally bridging parameters to APIs.

### BRN-R401: Duplicate DOM ID `id_product` ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| BRN-R401 | Two `id="id_product"` hidden inputs in the same form — jQuery selector always returns first, non-tagged product selection silently broken | Renamed second hidden input to `id="id_product_nt"`; JS autocomplete handler rerouted via `$(e.target).attr('id')` context check | 2026-03-12 |

**Prevention**: When copy-pasting form sections (e.g., tagged vs non-tagged item blocks), always assign unique IDs to every element. Run `grep -n 'id="id_' views/` and check for duplicate `id=` attributes across the same form. HTML validators and linting tools (e.g., `htmlhint`, browser DevTools) flag duplicate IDs as errors.

**Field-Level Data Flow**:

| Field | HTML Element | JS Variable | Notes |
|---|---|---|---|
| Tagged product | `#id_product` (L267) | `$('#id_product').val()` | First in DOM — always captured correctly |
| Non-tagged product | `#id_product_nt` (L288) | `$('#id_product_nt').val()` | Was shadowed; now uniquely addressed |

---

### BRN-D10: get_verifMobNo() Null-Crash ✅ FIXED

| Field | Value |
|---|---|
| **Bug ID** | BRN-D10 |
| **File** | `admin/application/models/ret_brntransfer_model.php` L1038–1045 |
| **Anti-Pattern** | Direct `->row()->otp_verif_mobileno` access without `num_rows()` guard — fatal PHP error if branch has no OTP mobile number configured |
| **Fix Applied** | Wrapped in `if ($sql && $sql->num_rows() > 0)` block; added `$this->db->escape($branch)` to SQL; returns `null` on empty result |
| **Date Fixed** | 2026-03-12 |
| **Pattern** | PAT-NULL-001 (Null Object Reference / Row-Access Without Guard) |
| **GitHub** | [#1178](https://github.com/Logimax-Technologies/etail_development_src/issues/1178) — Closed ✅ |
| **Tests** | `test_BRN_D10.php` — 4/4 PASSED |

**Root cause**: `get_verifMobNo()` directly chained `->row()->otp_verif_mobileno` without first checking `->num_rows() > 0`. If no branch row exists (deleted/unconfigured branch), `->row()` returns `null`, and PHP fatally crashes trying to access a property on `null`.

**The controller caller `send_otp()` (L1173)** already handled the `null` gracefully — it just needed the model to return `null` safely instead of crashing.

**Prevention**: Before any `->row()->property` access, always verify `$sql->num_rows() > 0`. Apply the same guard pattern as BRN-D16, BRN-D17, and BRN-D09.

**Detection rule** (for future audits):
```bash
grep -n "->query(" models/ret_brntransfer_model.php | grep -v "num_rows"
```
Any query whose result is used with `->row()` without a `num_rows()` check is a potential PAT-NULL-001 instance.

> **Anti-pattern entry #19 in register:**

| # | Pattern | Location | Severity | Status |
|---|---|---|---|---|
| 19 | **Null Object Reference (OTP Mobile Lookup)** | Model `get_verifMobNo()` L1038 | P1 | ✅ **FIXED 2026-03-12** |

### BRN-D11: net_wt copy-paste swap ✅ FIXED

| Field | Value |
|---|---|
| **Bug ID** | BRN-D11 |
| **File** | `admin/application/models/ret_brntransfer_model.php` L1538 |
| **Anti-Pattern** | Copy-pasting array keys/values from adjacent lines and forgetting to update the variable name. The `net_wt` key was incorrectly assigned `$gross_wt`. |
| **Fix Applied** | Changed `$gross_wt` to `$net_wt` for the `net_wt` array key in the Sales Return summary block. |
| **Date Fixed** | 2026-03-12 |
| **Pattern** | PAT-VAR-001 (Copy-Paste Variable Mismatch) |

> **Anti-pattern entry #20 in register:**

| # | Pattern | Location | Severity | Status |
|---|---|---|---|---|
| 20 | **Copy-Paste Variable Mismatch** | Model `get_purchase_items()` SR Summary | P1 | ✅ **FIXED 2026-03-12** |

**Prevention**: When copy-pasting blocks of code (especially array assignments), double-check that every variable matches its corresponding key. This is a common source of business logic bugs.

### BRN-D21: Wrong variable for non-tag SR insert ✅ FIXED

| Field | Value |
|---|---|
| **Bug ID** | BRN-D21 |
| **File** | `admin/application/controllers/admin_ret_brntransfer.php` L716, L750 |
| **Anti-Pattern** | Copy-pasting from the partly_sale block above; `$item_log` was built but `$partly_sale_log` was passed to `insertData()`, causing stale data or undefined variable errors. |
| **Fix Applied** | Changed `$partly_sale_log` to `$item_log` at both insertion points for non-tag sales return log entries. |
| **Date Fixed** | 2026-03-13 |
| **Pattern** | PAT-VAR-001 (Copy-Paste Variable Mismatch) |
| **GitHub** | [#1214](https://github.com/Logimax-Technologies/etail_development_src/issues/1214) — Closed ✅ |
| **Tests** | `test_BRN_D21.php` — 2/2 PASSED |

> **Anti-pattern entry #21 in register:**

| # | Pattern | Location | Severity | Status |
|---|---|---|---|---|
| 21 | **Copy-Paste Variable Mismatch (Controller)** | Controller `save()` non-tag SR block | P1 | ✅ **FIXED 2026-03-13** |

**Prevention**: When copy-pasting loop blocks for different item types (old metal, partly sale, non-tag SR), verify each `insertData()` call references the array variable just constructed — not a variable from a previous block.
