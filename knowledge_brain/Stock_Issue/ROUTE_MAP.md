# ROUTE MAP — Stock Issue
> Round 4 | 2026-03-19 | Complete URI → Controller → Method mapping

---

## Route Table

Base URL: `{base_url}index.php/`

| # | URI Pattern | HTTP | Auth | Controller Method | View / Response |
|---|---|---|---|---|---|
| 1 | `admin_ret_stock_issue/stock_issue/list` | GET | Session | `stock_issue('list')` | `ret_stock_issue/list.php` |
| 2 | `admin_ret_stock_issue/stock_issue/add` | GET | Session | `stock_issue('add')` | `ret_stock_issue/form.php` |
| 3 | `admin_ret_stock_issue/stock_issue/save` | POST | Session + FORM_SECRET | `stock_issue('save')` | JSON `{status, message, id_stock_issue}` |
| 4 | `admin_ret_stock_issue/stock_issue/issue_print/{id}` | GET | Session | `stock_issue('issue_print')` | PDF stream (DELIVERY CHALLAN summary) |
| 5 | `admin_ret_stock_issue/stock_issue/issue_print_detail/{id}` | GET | Session | `stock_issue('issue_print_detail')` | PDF stream (DELIVERY CHALLAN detail) |
| 6 | `admin_ret_stock_issue/stock_issue` (no type) | POST | Session | `stock_issue(default)` | JSON `{list:[...]}` — DataTable feed |
| 7 | `admin_ret_stock_issue/get_tag_scan_details` | POST | Session | `get_tag_scan_details()` | JSON `[{tag data...}]` |
| 8 | `admin_ret_stock_issue/get_receipt_tag_scan_details` | POST | Session | `get_receipt_tag_scan_details()` | JSON `[{tag data...}]` |
| 9 | `admin_ret_stock_issue/get_stock_issue_type` | GET | Session | `get_stock_issue_type()` | JSON `[{id, name, issue_to_cus}]` |
| 10 | `admin_ret_stock_issue/get_StockIssuedItems` | POST | Session | `get_StockIssuedItems()` | JSON `{tags:[...], nontags:[...]}` |
| 11 | `admin_ret_stock_issue/get_nontag_scan_details` | POST | Session | `get_nontag_scan_details()` | JSON `[{nontag items}]` |
| 12 | `admin_ret_stock_issue/stock_issue_sendotp` | POST | Session | `stock_issue_sendotp()` | JSON `{OTP, status, msg}` ⚠️ |
| 13 | `admin_ret_stock_issue/stock_issue_verify_otp` | POST | Session | `stock_issue_verify_otp()` | JSON `{status, msg, verified_otp}` |

---

## Auth Model

All routes protected by:
1. **Session gate** (`__construct`): redirects to `admin/login` if no session
2. **Access time gate** (`__construct`): blocks if current time outside configured access window
3. **FORM_SECRET gate** (`stock_issue/save` only): must match session value

**No role-based ACL per route** — all authenticated users with session can access all endpoints.

---

## POST Parameters Map

### Route 3 — `stock_issue/save` (Tagged Issue/Receipt, `stock_type=1`)
| POST Key | PHP Variable | Used In |
|---|---|---|
| `form_secret` | `$form_secret` | CSRF gate |
| `order[*]` | `$addData` | All issue data (branch, issue_type, etc.) |
| `tag_id[]` | `$tag_details` | Tags being issued/received |
| `rate_per_gram` | `$rate_per_gram` | Rate for issue valuation |
| `stock_type` | `$stock_type` | Determines tagged vs non-tagged path |

### Route 3 — `stock_issue/save` (NonTagged Issue/Receipt, `stock_type=2`)
| POST Key | PHP Variable | Used In |
|---|---|---|
| `form_secret` | `$form_secret` | CSRF gate |
| `stock_type` | `$stock_type` | Path switch |
| `nt_data` | `$nt_data` | JSON array of non-tag items |
| `issue_type` | `$issue_type` | DB value |
| `issued_to` | `$issued_to` | 1=Customer, 2=Employee, 3=Karigar |
| `branch_select` | `$id_branch` | Issuing branch |
| `remark` | `$remark` | Free text |
| `id_employee` | `$id_employee` | Assigned employee |
| `karigar` | `$karigar` | Karigar ID |
| `cus_id` | `$cus_id` | Customer ID |
| `rate_per_gram` | `$rate_per_gram` | Rate |
| `type_issue` | `$type_issue` | 1=Issue, 2=Receipt |
| `issued_type` | `$issued_type` | Issue type for receipt |
| `issued_branch` | `$issued_branch` | Source branch (receipt) |

### Routes 7/8/11 — Scan endpoints
| POST Key | PHP Variable |
|---|---|
| `tag_code` | `$tag_code` |
| `old_tag_code` | `$old_tag_code` |
| `id_branch` | `$data['id_branch']` |
| `id_metal` | `$data['id_metal']` |
| `id_section` | `$data['id_section']` |
| `prodId` | `$data['prodId']` |

---

## Session Reads Catalogue

| Session Key | Read In | Purpose |
|---|---|---|
| `uid` | Throughout controller | User ID for audit fields |
| `id_log` | Log writes | Log record reference |
| `FORM_SECRET` | Save gate | Anti-duplicate submit |
| `profile` | `stock_issue('add')` | Get OTP requirement |
| `access_time_from` / `_to` | Constructor | Time gate |
| `stock_issue_otp` | `verify_otp` | OTP comparison |
| `stock_issue_otp_exp` | `verify_otp` | OTP expiry check |

---

## Codeigniter Routing Note

Routes follow: `{controller}/{method}/{param1}/{param2}`  
`stock_issue($type, $id, $received_time)` maps:
- `stock_issue/list` → `$type='list'`
- `stock_issue/save` → `$type='save'`
- `stock_issue/issue_print/123` → `$type='issue_print'`, `$id='123'`
- `stock_issue` (bare, with POST) → `$type=''` → default case
