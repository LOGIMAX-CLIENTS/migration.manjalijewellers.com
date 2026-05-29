# Remediation Plan: Customer Order Module

> Single entry-point for all fixes. Prioritized by CVSS-equivalent impact.
> Reference: BUG_CANDIDATES.md (44 bugs + R14 additions + AP-11 R15) · SPRINT_PLAN.md (13 stories) · FIX_GUIDE.md (11 patches)
> **R15 update (2026-03-24)**: +1 new anti-pattern AP-11 (advance-reversal guard bug). Total: 45 bugs.


---

## Priority 0 — Fix Today (CRITICAL Security)

These are active security vulnerabilities that can be exploited with zero authentication difficulty.

| # | Action | File | Line(s) | Effort |
|---|---|---|---|---|
| P0-1 | **Remove `echo $this->db->last_query();exit;`** — SQL leak on update failure | `admin_ret_order.php` | L649 | 5 min |
| P0-2 | **Remove OTP from JSON response** — cancel OTP exposed in plain text | `admin_ret_order.php` | L2286 | 5 min |
| P0-3 | **Remove debug `file_put_contents()`** — saves `$_FILES` to disk on every repair update | `admin_ret_order.php` | L1761-1768 | 10 min |
| P0-4 | **Cast `order_id` + `id_orderdetails` in cancel endpoints** | `admin_ret_order.php` | L660-661, L725-726 | 15 min |
| P0-5 | **Fix advance-reversal guard** — `if($order_advance > 0)` compares array to int — always true — inserts `ret_issue_receipt` row even when advance=0 | `admin_ret_order.php` | L670 | 5 min |

---

## Priority 1 — Fix This Sprint (Model SQL Injection — Batch Fix)

**Pattern**: All model methods in `ret_order_model.php` use raw string concatenation. Fix by migrating to CI Query Builder or `$this->db->escape()`.

### Batch Fix Strategy for Model Injections

```php
// BEFORE (vulnerable):
$this->db->query("SELECT ... WHERE id=" . $id);

// AFTER — Option A (CI Query Builder):
$this->db->where('id', $id);
$this->db->get('table');

// AFTER — Option B (parameterized query):
$this->db->query("SELECT ... WHERE id = ?", [$id]);

// AFTER — for LIKE searches:
$this->db->like('column', $searchTxt);   // auto-wraps with %

// AFTER — for dynamic ORDER/column names (getIssueTaggingBySearch):
$allowed_fields = ['tag_code', 'product_name', 'design_name'];
if (!in_array($searchField, $allowed_fields)) return [];
$this->db->query("... and tag.$searchField LIKE ?", ["%$SearchTxt%"]);
```

### Methods to Fix (ordered by exposure level)

| Priority | Method | Line | Attack Surface | Pattern |
|---|---|---|---|---|
| 🔴 | `getIssueTaggingBySearch()` | L726 | `$searchField` = **column name** injection | Allowlist required |
| 🔴 | `get_new_orderlist()` | L493-500 | 7 params including branch, dates, karigar | `escape()` / QB |
| 🔴 | `get_repair_orders_list()` | L880-887 | 7 params — listing query | `escape()` / QB |
| 🔴 | `ajax_getRepairOrders()` | L774-777 | 4 params | `escape()` / QB |
| 🔴 | `ajax_getOrders()` | L315-317 | branch + date range | `escape()` / QB |
| 🔴 | `getEstiDetailsById()` | L258-279 | 3 queries with `$SearchTxt` | `escape()` |
| 🔴 | `getSearchSubDesign()` | L663-665 | `searchTxt`, `design_no`, `id_product` | `like()` + `where()` |
| 🔴 | `getSearchDesign()` | L676 | `searchTxt`, `product_id` | `like()` + `where()` |
| 🔴 | `getOrderNos()` | L205 | search text LIKE | `like()` |
| 🔴 | `fetchEstiBySearch()` | L251 | estimation search | `like()` |
| 🟠 | `get_customerorder_details()` | L507 | `$id_orderdetails` (int) | `(int)` cast |
| 🟠 | `get_joborder_details()` | L512 | `$id_orderdetails` (int) | `(int)` cast |
| 🟠 | `get_orderdetails_by_id()` | L540 | `$id` (int) | `(int)` cast |
| 🟠 | `getAvailableTaxGroupItems()` | L587 | `$taxgroupid` (int) | `(int)` cast |
| 🟠 | `get_karigar_orders()` | L604 | `$id_customerorder` (int) | `(int)` cast |
| 🟠 | `getSms_data()` | L614 | `$id_customerorder` (int) | `(int)` cast |
| 🟠 | `get_product_size()` | L620 | `$id_product` (int) | `(int)` cast |
| 🟠 | `get_img_by_id()` | L627 | `$id_orderdetails` inline | `(int)` cast |
| 🟠 | `get_dec_by_id()` | L652 | `$id_orderdetails` inline | `(int)` cast |
| 🟠 | `getRepairDetailsStatus()` | L825 | `$id_customerorder` (int) | `(int)` cast |
| 🟠 | `getRepairOrderDet()` | L810 | `$id_customerorder` (int) | `(int)` cast |
| 🟠 | `getOrderByCus()` | L209 | `$id_cus` (int) | `(int)` cast |
| 🟠 | `get_order_charges()` | L369-372 | `$id_orderdetails` (int) | `(int)` cast |
| 🟠 | `get_order_stones()` | L411 | `$id_orderdetails` (int) | `(int)` cast |
| 🟠 | `get_order_images()` | L417 | `$id_orderdetails` (int) | `(int)` cast |
| 🟡 | `get_ret_settings()` | L214 | `$settings` string | `escape()` |
| 🟡 | `generateOrderNo()` | L151-165 | branch, order_type, fin_year | `(int)` cast |
| 🟡 | `get_profile_settings()` | L111 | `$id_profile` (int) | `(int)` cast |
| 🟡 | `get_active_product_byCatId()` | L703 | `$catId` (int) | `(int)` cast |

---

## Priority 2 — Fix This Sprint (Transaction Integrity)

| # | Action | File | Lines | Effort |
|---|---|---|---|---|
| P2-1 | Fix double `trans_begin()` in `cart('order_place')` | `admin_ret_order.php` | L1897, L1926 | 30 min |
| P2-2 | Move `trans_begin()` outside foreach in all 5 per-item TX functions | `admin_ret_order.php` | Multiple | 1 hr |
| P2-3 | Move `unlink()` to after `trans_commit()` in `delete_order_img` | `admin_ret_order.php` | L1348 | 15 min |
| P2-4 | Fix always-truthy reject path in `assign_customer_order` | `admin_ret_order.php` | L989 | 5 min |
| P2-5 | Move email send to after `trans_commit()` in cart place | `admin_ret_order.php` | L1993 | 30 min |

---

## Priority 3 — Fix Next Sprint (Data Integrity)

| # | Action | File | Lines | Effort |
|---|---|---|---|---|
| P3-1 | Fix double image path concatenation bug | `admin_ret_order.php` | L470 | 5 min |
| P3-2 | Replace `mt_rand(120,1230)` filenames with `uniqid()` (4 locations) | `admin_ret_order.php` | L217, L507, L1269, L1310 | 20 min |
| P3-3 | Add cascade delete for order header | `admin_ret_order.php` | L386-419 | 45 min |
| P3-4 | Fix missing cascade on `customerorder` delete | Schema | — | 30 min |
| P3-5 | Add XSS escaping to `cus_order_print.php` (8 echo points) | `cus_order_print.php` | L166-320 | 30 min |

---

## Priority 4 — Performance (Backlog)

| # | Action | File | Lines | Effort |
|---|---|---|---|---|
| P4-1 | Cache `SHOW COLUMNS` in `insertData()`/`updateData()` | `ret_order_model.php` | L13, L49 | 1 hr |
| P4-2 | Fix N+1: batch-load images outside foreach in listings | `ret_order_model.php` | L400, L815 | 2 hrs |
| P4-3 | Add pagination/LIMIT to `get_new_orderlist()` and `get_repair_orders_list()` | `ret_order_model.php` | L501, L887 | 1 hr |
| P4-4 | Add indexes on `customerorderdetails(orderstatus, id_customerorder)` | Schema | — | 15 min |

---

## Fix Velocity Estimates

| Priority | Issues | Est. Dev Time |
|---|---|---|
| P0 (Today) | 5 (+1 R15) | ~40 min |
| P1 (Model injections — int casts only) | 20 | ~2 hrs |
| P1 (Model injections — LIKE/string) | 9 | ~3 hrs |
| P2 (TX integrity) | 5 | ~2 hrs |
| P3 (Data integrity) | 5 | ~2 hrs |
| P4 (Performance) | 4 | ~4 hrs |
| **Total** | **48 (+1 R15)** | **~14.5 hrs** |

> **See [FIX_GUIDE.md](./FIX_GUIDE.md)** for diff-format patches on P0 and P2 items.
> **See [SPRINT_PLAN.md](./SPRINT_PLAN.md)** for story-level acceptance criteria.
