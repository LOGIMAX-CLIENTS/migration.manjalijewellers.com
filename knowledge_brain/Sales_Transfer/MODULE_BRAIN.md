# Sales Transfer Module — Module Brain

> **Module**: Sales Transfer
> **Built**: 2026-03-20 — Round 1 | **Updated**: R2 (Refresh) | R3 (Verification) | R4 (Schema) | R5 (JS Deep-Dive) | R6 (FLOW_RISK_MATRIX)
> **Controller**: `admin_ret_sales_transfer.php` (591 lines)
> **Model**: `ret_sales_transfer_model.php` (709 lines)
> **JS**: `ret_sales_transfer.js` (4264 lines)
> **Views**: 4 files in `views/sales_transfer/`

---

## 1. Module Overview

**Purpose**: Manages inter-branch stock transfers for retail jewelry operations. Handles two primary workflows:
1. **Sales Transfer** — Move tagged inventory items FROM current branch TO another branch (creates billing record with `bill_type=13`)
2. **Sales Return Transfer** — Return previously transferred items back (creates billing record with `bill_type=14`)

Each workflow has two sub-modes:
- **Request** (type=1) — Initiate the transfer, tag items with `tag_status=4` (in-transit)
- **Download** (type=2) — Receive/approve the transfer at destination branch, set `tag_status=0` (available) + update `current_branch`

### File Map

| File | Lines | Bytes | Purpose |
|---|---|---|---|
| `admin_ret_sales_transfer.php` | 591 | 21,638 | Controller — routes, business logic, DB transactions |
| `ret_sales_transfer_model.php` | 709 | 15,482 | Model — CRUD, tag queries, bill lookups |
| `ret_sales_transfer.js` | 4,264 | 94,060 | JS — UI logic, AJAX calls, form validation, scanning |
| `views/list.php` | 104 | 5,070 | List view — DataTable of transfers |
| `views/sales_trasnfer.php` | 449 | 27,754 | Sales Transfer add form (request + download) |
| `views/sales_ret_transfer.php` | 377 | 23,160 | Sales Return Transfer form (request + download) |
| `views/approval.php` | 208 | 8,324 | Approval/download form |

### Connection Flow
```
Browser → JS (ret_sales_transfer.js)
       → AJAX → Controller (admin_ret_sales_transfer.php)
       → Model (ret_sales_transfer_model.php) + ret_billing_model
       → DB (ret_billing, ret_bill_details, ret_taging, ret_taging_status_log, ret_bill_return_details)
       → View (sales_transfer/*.php)
       → Browser
```

---

## 2. Constructor

### Loaded Models

| Model | Purpose |
|---|---|
| `ret_sales_transfer_model` | Primary — tag queries, branch details, bill lookups |
| `admin_settings_model` | Day-closing data, branch settings |
| `ret_billing_model` | Bill number generation, ref no generation, branchwise rates, generic CRUD |
| `log_model` | Logging (loaded but NOT actively used in this controller) |

### Session Gate
- Checks `is_logged` session → redirects to `admin/login` if not set
- Time-based access restriction: checks `access_time_from` / `access_time_to` → logs out if outside allowed window

---

## 3. Entry Points

| URL Path | HTTP | Controller Method | Lines | Purpose |
|---|---|---|---|---|
| `/admin_ret_sales_transfer/sales_transfer/list` | GET | `sales_transfer('list')` | L37-40 | Load list view |
| `/admin_ret_sales_transfer/sales_transfer/add` | GET | `sales_transfer('add')` | L41-47 | Load add form (sales transfer) |
| `/admin_ret_sales_transfer/sales_transfer/ret_add` | GET | `sales_transfer('ret_add')` | L48-53 | Load add form (sales return transfer) |
| `/admin_ret_sales_transfer/sales_transfer/sales_trans_tag` | POST | `sales_transfer('sales_trans_tag')` | L54-57 | AJAX: Get available tags for transfer |
| `/admin_ret_sales_transfer/sales_transfer/sales_trans_approval_tag` | POST | `sales_transfer('sales_trans_approval_tag')` | L58-61 | AJAX: Get tags pending approval/download |
| `/admin_ret_sales_transfer/sales_transfer/sales_return_trans_tag` | POST | `sales_transfer('sales_return_trans_tag')` | L62-65 | AJAX: Get return transfer request tags |
| `/admin_ret_sales_transfer/sales_transfer/sales_return_trans_approval_tag` | POST | `sales_transfer('sales_return_trans_approval_tag')` | L66-69 | AJAX: Get return transfer approval tags |
| `/admin_ret_sales_transfer/sales_transfer/getTagsByFilter` | POST | `sales_transfer('getTagsByFilter')` | L71-74 | AJAX: Filter tags by scan code (download) |
| `/admin_ret_sales_transfer/sales_transfer/getReturnTagsByFilter` | POST | `sales_transfer('getReturnTagsByFilter')` | L76-79 | AJAX: Filter return tags by scan code |
| `/admin_ret_sales_transfer/create_sales_transfer` | POST | `create_sales_transfer()` | L84-214 | AJAX: Create sales transfer bill |
| `/admin_ret_sales_transfer/update_sales_transfer_request` | POST | `update_sales_transfer_request()` | L217-283 | AJAX: Download/approve sales transfer |
| `/admin_ret_sales_transfer/create_sales_ret_transfer` | POST | `create_sales_ret_transfer()` | L287-380 | AJAX: Create sales return transfer bill |
| `/admin_ret_sales_transfer/update_sales_ret_transfer` | POST | `update_sales_ret_transfer()` | L383-447 | AJAX: Download/approve return transfer |
| `/admin_ret_sales_transfer/update_TagScan` | POST | `update_TagScan()` | L450-516 | AJAX: Scan-based tag download (sales transfer) |
| `/admin_ret_sales_transfer/update_ret_TagScan` | POST | `update_ret_TagScan()` | L522-589 | AJAX: Scan-based tag download (return transfer) |

---

## 4. Model Methods Summary

- **18 methods** total in `ret_sales_transfer_model.php`
- **3 generic CRUD** methods: `insertData`, `updateData`, `deleteData`
- **15 domain methods**: tag queries, branch lookups, bill ID retrieval
- Full alphabetical listing → see [METHOD_INDEX.md](file:///d:/XAMPP/htdocs/retail_v5/knowledge_brain/Sales_Transfer/METHOD_INDEX.md)

---

## 5. Data Flow Summary

4 primary flows documented in [DATA_FLOW.md](file:///d:/xampp/htdocs/etail_development_src/knowledge_brain/Sales_Transfer/DATA_FLOW.md):
1. **Sales Transfer Request** (CREATE) — `bill_type=13`, tags → `tag_status=4`
2. **Sales Transfer Download** (APPROVE) — tags → `tag_status=0`, `current_branch` updated
3. **Sales Return Transfer Request** (CREATE) — `bill_type=14`, tags → `tag_status=4`
4. **Sales Return Transfer Download** (APPROVE) — tags → `tag_status=0`, `current_branch` updated

Handoff contracts, state machine, and QA scenarios → see [FLOW_RISK_MATRIX.md](file:///d:/xampp/htdocs/etail_development_src/knowledge_brain/Sales_Transfer/FLOW_RISK_MATRIX.md)

---

## 6. Key Tables

| Table | Purpose | Key Columns |
|---|---|---|
| `ret_billing` | Header table for all billing records (owned by Billing module, written by this module) | `bill_id`, `bill_no`, `bill_type` (13=ST, 14=SRT), `from_branch`, `to_branch`, `tot_bill_amount` |
| `ret_bill_details` | Line items — per-tag billing details | `bill_det_id`, `bill_id`, `tag_id`, `item_cost`, `total_igst/sgst/cgst` |
| `ret_taging` | Master tag table — each physical jewelry item | `tag_id`, `tag_code`, `tag_status`, `current_branch`, `product_id`, `gross_wt` |
| `ret_taging_status_log` | Audit trail for tag status changes | `tag_id`, `status`, `from_branch`, `to_branch`, `date` |
| `ret_bill_return_details` | Link table for return transfers | `bill_id`, `ret_bill_id`, `ret_bill_det_id` |
| `ret_financial_year` | Financial year lookup | `fin_year_code`, `fin_status` |
| `ret_settings` | Application settings key-value store | `name`, `value` |
| `branch` | Branch master data | `id_branch`, `id_country`, `id_state`, `gst_number` |

---

## 7. Form Sections & Hidden Fields

### sales_trasnfer.php
| ID | Type | Purpose |
|---|---|---|
| `form_secret` | hidden | CSRF token (session-stored `SALES_TRANS_FORM_SECRET`) |
| `is_metal_for_billing` | hidden | Setting: whether metal selection is mandatory |
| `sales_trans_dnload` | hidden | Setting: download mode (1=batch, 2=scan) |
| `actual_pcs_dnload` | hidden | Tracks expected piece count for scan verification |
| `from_brn` | select | From branch (disabled when logged to branch) |
| `to_brn` | select | To branch |
| `select_metal` | select | Metal type (conditional on setting) |
| `select_category` | select | Category filter |
| `fin_year_code` | select | Financial year |

### sales_ret_transfer.php
Same as above PLUS:
| `sales_ret_trans_dnload` | hidden | Return download mode |
| `ret_actual_pcs_dnload` | hidden | Expected piece count for return scan |
| `aganist_bill` | radio | Yes/No — whether return is against a specific bill |

---

## 8. Business Rules Summary

**8 rules** documented in [BUSINESS_RULES.md](file:///d:/XAMPP/htdocs/retail_v5/knowledge_brain/Sales_Transfer/BUSINESS_RULES.md). Top rules:
- GST split: SGST+CGST if same state, IGST if inter-state
- Tax rate hardcoded at 3%
- Taxable amount = gross_wt × rate_per_grm (Per Gram) OR piece × rate_per_grm (Per Piece)
- Day-closing date validation: `to_branch` entry date must >= `from_branch` entry date

---

## 9. Cross-Module Dependencies

See [CROSS_MODULE_MAP.md](file:///d:/XAMPP/htdocs/retail_v5/knowledge_brain/Sales_Transfer/CROSS_MODULE_MAP.md)

| External Module | Direction | Summary |
|---|---|---|
| Billing (`ret_billing_model`) | Write | Writes to `ret_billing`, `ret_bill_details`, uses `code_number_generator`, `generateRefNo` |
| Tagging (`admin_ret_tagging`) | Read (AJAX) | Fetches metal rates via JS AJAX |
| Catalog (`admin_ret_catalog`) | Read (AJAX) | Fetches products, categories, metals via JS AJAX |
| Branch Transfer (`admin_ret_brntransfer`) | Read (AJAX) | Fetches branches, lots, designs via JS AJAX |
| Settings (`admin_settings_model`) | Read | Day-closing data, branch settings |
| Billing Invoice | Read (JS redirect) | Opens invoice after successful transfer: `admin_ret_billing/billing_invoice/{id}` |

---

## 10. Known Risks

| # | Risk | Severity | Details | Round |
|---|---|---|---|---|
| 1 | **SQL Injection** | P0 | All 17 model query methods concatenate user input directly into SQL — zero parameterized queries | R1 |
| 2 | **Hardcoded 3% tax rate** | P1 | Tax calculation in `create_sales_transfer()` L151 uses `(taxable_amt * 3) / 100` — not settings-driven | R1 |
| 3 | **Undefined `$insId` in `update_sales_ret_transfer()`** | P0 | L440 references `$insId` which is never defined — success response will contain `NULL` id | R1 |
| 4 | **Undefined `$tb_entry_date` in `update_sales_ret_transfer()`** | P0 | L397 uses `$tb_entry_date` before the foreach that defines it (L410-419) — download_date will be NULL | R1 |
| 5 | **`trans_rollback()` without `trans_begin()`** | P1 | `update_sales_transfer_request()` L243 calls `trans_rollback()` before any `trans_begin()` | R1 |
| 6 | **`async:false` AJAX calls** | P2 | Multiple JS AJAX calls use `async:false` which blocks the browser UI thread | R1 |
| 7 | **No DELETE flow** | Info | No delete/cancel functionality exists for sales transfers in this controller | R1 |
| 8 | **Transaction scope issue in `create_sales_ret_transfer()`** | P1 | `trans_begin()` inside foreach loop (L333), `trans_status()` outside (L371) — only last iteration checked | R1 |
| 9 | **`tot_bill_amount` reset to 0** | P1 | In `create_sales_transfer()` L109, `$tot_bill_amount` forced to 0, ignoring POST value from L94 | R1 |
| 10 | **Day-closing not checked for sales transfer request** | P2 | `create_sales_transfer()` doesn't validate day-closing dates, only download methods do | R1 |
| 11 | **Undefined `$insId` in `update_sales_transfer_request()`** | P0 | L277 references `$insId` in success response — never defined in this method. Same class of bug as Risk #3 | R2 |
| 12 | **`$dCData['entry_date']` used on array-of-arrays** | P0 | In `update_sales_transfer_request()` L227, `update_TagScan()` L464, `update_ret_TagScan()` L537: `getAllBranchDCData()` returns array of branch objects, but code accesses `$dCData['entry_date']` directly — this will use the array index 'entry_date' which doesn't exist at root level. `$bill_date` will be NULL/falsy | R2 |
| | | | **✅ R3 VERIFIED**: `getAllBranchDCData()` at `admin_settings_model.php:L2413` returns `$sql->result_array()` = array of arrays. `$dCData['entry_date']` is definitively NULL. Compare: `getBranchDayClosingData($id_branch)` at L2407 returns `$sql->row_array()` = single assoc array (used correctly in `create_sales_transfer()`) | |
| 13 | **Cross-category `$tot_bill_amount` accumulation bug** | P0 | In `create_sales_ret_transfer()` L295, `$tot_bill_amount` is initialized ONCE to 0, then accumulated across ALL categories. But each category creates a SEPARATE bill (each gets its own `$insId`). So the 2nd category's bill total includes the 1st category's total. Only the LAST bill gets the full accumulated amount | R2 |
| 14 | **Missing `is_credit`/`credit_status` in return transfer INSERT** | P1 | `create_sales_transfer()` sets `is_credit=1`, `credit_status=2`, but `create_sales_ret_transfer()` omits these entirely — return bills may have inconsistent credit tracking | R2 |
| 15 | **`goldrate_22ct` hardcoded as 0 for return transfers** | P2 | `create_sales_ret_transfer()` L326 sets `goldrate_22ct=0` without fetching actual rate — no `silverrate_1gm` either | R2 |
| 16 | **GST branch filtering contradiction** | P1 | `getBTBranches()` initial load (L357) filters to branches by SAME GST for type=1, but `$('.from_branch').on('change')` handler (L537) ALWAYS filters by DIFFERENT GST — contradictory logic | R2 |
| 17 | **No `trans_begin()`/`trans_commit()` in `update_sales_ret_transfer()`** | P0 | L438 calls `trans_status()` and L439 calls `trans_commit()` but there's no `trans_begin()` anywhere in the method — transaction status check is meaningless | R2 |
| 18 | **`getBranchDayClosingData()` returns arbitrary row when branch is empty** | P1 | `admin_settings_model.php:L2409` — if `$id_branch=''`, the WHERE clause is omitted and `row_array()` returns the first random row from `ret_day_closing`. In this module, `create_sales_transfer()` always passes `$from_branch` which comes from POST — if empty, wrong day-closing data is used | R3 |
| 19 | **`get_bill_no()` SQL column injection via `$field` parameter** | P2 | `ret_billing_model.php:L374` — `$field` is used directly as SQL column name (`SELECT $field as lastBill_no`). In this module's calls, `$field` is hardcoded ('sales_ref_no', 's_ret_refno') so not exploitable, but pattern is dangerous if reused | R3 |
| 20 | **`tot_bill_amount` is `decimal(10,0)` — no decimal precision** | P1 | Schema: `ret_billing.tot_bill_amount` truncates all amounts to integers. Detail `item_cost` is `decimal(10,2)`. Sum of detail costs loses fractional amounts at header level | R4 |
| 21 | **`is_credit` DEFAULT 0 + `credit_status` DEFAULT 1 on return transfer** | P0 | Schema confirms: `is_credit tinyint(1) NOT NULL DEFAULT '0'`, `credit_status tinyint(1) NOT NULL DEFAULT '1'`. Return transfers omit both → bills appear as non-credit, fully-paid instead of credit/pending. **R2 Risk #14 ESCALATED** | R4 |
| 22 | **`from_branch`/`to_branch` NULLable with no FK constraint** | P2 | Schema: `int DEFAULT NULL`, no FOREIGN KEY → orphaned branch references possible, no referential integrity | R4 |
| 23 | **Detail vs Header precision mismatch** | P1 | `ret_bill_details.item_cost` is `decimal(10,2)` but `ret_billing.tot_bill_amount` is `decimal(10,0)` → sum loses precision | R4 |
| 24 | **`billing_for=3` undocumented in schema enum** | P2 | DB comment says `1-Customer, 2-Company`, but this module sets `billing_for=3` → reports filtering by comment enum may miss transfer bills entirely | R4 |
| 25 | **`ret_day_closing.entry_date` is `date` not `datetime`** | P3 | Code uses `date()` PHP function on it — works but loses time granularity. Also, UNIQUE KEY on `id_branch` guarantees 1 row per branch (confirms R2 data structure) | R4 |
| 26 | **Hardcoded 3% GST rate in JS `calculateSaleBillRowTotal()`** | P1 | L2149: `tax_amount = (taxable_amt)*3/100` — uses hardcoded 3% regardless of actual GST slab (which could be 0%, 1.5%, 3%, etc. depending on item). No server-side rate lookup. Same rate applied to ALL items regardless of category/metal | R5 |
| 27 | **`async:false` blocks browser on ALL save calls** | P1 | `create_sales_transfer()` L2462, `create_sales_ret_transfer()` L3272, `update_sales_ret_transfer_request()` L3340 — all use synchronous AJAX. Browser freezes during save; user gets no loading feedback; can cause "page unresponsive" dialogs | R5 |
| 28 | **Dead function `get_sales_return_branch_trasnfer()` calls wrong controller** | P1 | L2650: calls `admin_ret_brntransfer/sales_transfer/sales_return_trans_tag` (Branch Transfer controller, NOT Sales Transfer). Also hardcodes `silverrate_1gm` for ALL items at L2756 regardless of metal type (gold items get silver rate) | R5 |
| 29 | **`Array.push()` return value corrupts localStorage** | P0 | L3756 and L4172: `dnloaded_tags_new = JSON.parse(dnloaded_tags).push(val.tag_code)` — `.push()` returns the new array LENGTH (an integer), not the array. Then `JSON.stringify(dnloaded_tags_new)` saves an integer to localStorage. Next scan read: `JSON.parse()` gets an integer → `Array.isArray()` returns false → all previously scanned tags lost, duplicates allowed | R5 |
| 30 | **Wrong field focused after return scan** | P2 | L4192: `$("#scan_tag_no").focus()` in `get_retscan_TagSearchList()` — should be `$("#ret_scan_tag_no")`. Cursor jumps to the sales transfer scan input instead of the return transfer scan input | R5 |
| 31 | **Duplicate scan not prevented — no early return after detection** | P1 | L3462-3510: `#scan_tag_no` keyup handler iterates `stored_tags` to detect duplicates (L3492), shows toaster and clears field, but **never returns**. Execution continues to L3510 `getscan_TagSearchList()` — the scan is processed anyway, causing duplicate downloads | R5 |
| 32 | **`my_Date` used before initialization in `create_sales_ret_transfer()`** | P2 | L3248/3256 use `my_Date.getUTCSeconds()` in URL, but `my_Date = new Date()` is declared at L3262 (after usage). Works if called from `#sales_ret_trans_submit` handler where `my_Date` is already a global, but brittle — depends on stale global state | R5 |

---

## 11. DB Verification Queries

### 11a. Pull Complete Transfer Bill
```sql
SELECT b.*, d.*, t.tag_code, t.tag_status, t.current_branch
FROM ret_billing b
LEFT JOIN ret_bill_details d ON d.bill_id = b.bill_id
LEFT JOIN ret_taging t ON t.tag_id = d.tag_id
WHERE b.bill_id = '{BILL_ID}';
```

### 11b. Recalculate Expected Total
```sql
SELECT
    b.bill_id,
    b.tot_bill_amount AS stored_total,
    SUM(d.item_cost) AS calculated_total,
    b.tot_bill_amount - SUM(d.item_cost) AS delta
FROM ret_billing b
JOIN ret_bill_details d ON d.bill_id = b.bill_id
WHERE b.bill_type IN (13, 14)
GROUP BY b.bill_id
HAVING ABS(b.tot_bill_amount - SUM(d.item_cost)) > 0.01;
```

### 11c. Orphan Bill Details
```sql
SELECT d.*
FROM ret_bill_details d
LEFT JOIN ret_billing b ON b.bill_id = d.bill_id
WHERE b.bill_id IS NULL AND d.bill_type = 2;
```

### 11d. Tags Stuck in Transit
```sql
SELECT t.tag_id, t.tag_code, t.tag_status, t.current_branch,
       b.bill_no, b.bill_date, b.from_branch, b.to_branch
FROM ret_taging t
JOIN ret_bill_details d ON d.tag_id = t.tag_id
JOIN ret_billing b ON b.bill_id = d.bill_id
WHERE t.tag_status = 4 AND b.bill_type IN (13, 14)
AND b.download_date IS NULL
AND b.bill_date < DATE_SUB(NOW(), INTERVAL 7 DAY);
```

---

## 12. Codebase Notes

- **Typo in view filename**: `sales_trasnfer.php` (should be `sales_transfer.php`)
- **Typo in success messages**: "Trasnfer" appears throughout (controller L208, L277, L373, L440)
- **No form_secret validation**: `form_secret` is posted but never validated server-side against session
- **`log_model` loaded but unused**: The `log_model` is loaded in constructor but no logging methods are called
- **JS file is bloated**: ~4264 lines with heavy code duplication between sales transfer and return transfer logic. The branch-loading logic is duplicated ~4 times

---

## 13. Anti-Patterns Register

_(Empty — populated after bug fixes)_

| # | Pattern | Found In | Bug ID | Fix Date |
|---|---|---|---|---|
| — | — | — | — | — |
