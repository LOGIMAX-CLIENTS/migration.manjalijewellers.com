# Hotspot Map: Customer Order

> Top 10 highest-risk code areas ranked by bug density, severity, and fix urgency.
> Use this map to prioritize which areas to fix first in `/fix-single-bug` sessions.

---

## Hotspot Tier 1 — Fix Immediately (CRITICAL)

### HS-01: `order('update')` Image-Stone-Charge Delete-Reinsert Block
- **File**: `admin_ret_order.php` · **Lines**: L478-555
- **Bug density**: 5 bugs (BUG-001 debug leak, BUG-007/008/009 orphans, BUG-010 per-item TX)
- **What breaks**: Partially saved orders, lost stones/images/charges on failed update
- **Fix approach**: Move to single outer `trans_begin()`, use temp arrays before commit
- **Linked bugs**: BUG-CUSORD-001, 007, 008, 009, 010

### HS-02: `ajax_order_cancel` and `cancel_order_item` Unsanitized Input
- **File**: `admin_ret_order.php` · **Lines**: L656-761
- **Bug density**: 2 SQL injection paths
- **What breaks**: DB corruption, privilege escalation via crafted order_id
- **Fix approach**: Use `$this->input->post()` with XSS clean, or `intval()` for IDs
- **Linked bugs**: BUG-CUSORD-003, 013

### HS-03: `cart('order_place')` Double Transaction
- **File**: `admin_ret_order.php` · **Lines**: L1897, L1926
- **Bug density**: 2 bugs (double TX, email inside TX)
- **What breaks**: Orders silently saved without atomicity; email sent but order rolls back
- **Fix approach**: Remove L1897 `trans_begin()`; move email send outside TX
- **Linked bugs**: BUG-CUSORD-006 (CRITICAL)

### HS-04: `send_order_cancel_otp()` OTP Value Exposed in Response
- **File**: `admin_ret_order.php` · **Line**: L2286
- **Bug density**: 1 CRITICAL + 2 HIGH
- **What breaks**: Client receives actual OTP → bypasses security gate entirely
- **Fix approach**: Remove `'OTP' => $sent_otp` from JSON response
- **Linked bugs**: BUG-CUSORD (NEW from R9)

---

## Hotspot Tier 2 — Fix This Sprint (HIGH)

### HS-05: `order('update')` L470 Double-Image String
- **File**: `admin_ret_order.php` · **Line**: L470
- **What breaks**: `customerorderdetails.image` stores doubled path — images display incorrectly
- **Fix approach**: Remove one `$d['image']` from concatenation
- **Linked bugs**: BUG-CUSORD-002

### HS-06: `order('update')` L649 Debug SQL Leak
- **File**: `admin_ret_order.php` · **Line**: L649
- **What breaks**: On failed order update, raw SQL exposed to browser
- **Fix approach**: Remove `echo $this->db->last_query();exit;` — replace with error log
- **Linked bugs**: BUG-CUSORD-001

### HS-07: Image Filename Collision (`mt_rand(120,1230)`)
- **File**: `admin_ret_order.php` · **Lines**: L217, L507
- **What breaks**: After ~1110 uploads, filenames repeat → silent image overwrite
- **Fix approach**: Use `uniqid('', true)` or `md5(microtime().rand())` for filename
- **Linked bugs**: BUG-CUSORD-011

### HS-08: `assign_customer_order()` Per-Item TX + Unreachable Rollback
- **File**: `admin_ret_order.php` · **Lines**: L950, L983, L989
- **What breaks**: Partial assignment on multi-item order; rejection always commits
- **Fix approach**: Move `trans_begin()` outside foreach; replace `if($upd_data)` with `trans_status()` check
- **Linked bugs**: BUG-CUSORD-014, 015

---

## Hotspot Tier 3 — Fix Next Sprint (MEDIUM)

### HS-09: `order('delete')` Missing Cascade
- **File**: `admin_ret_order.php` · **Lines**: L386-390
- **What breaks**: Orphan rows in `customer_order_image`, `ret_order_item_stones`, `ret_order_other_charges`
- **Fix approach**: Add cascade deletes before `deleteData()` on header, or add FK constraints
- **Linked bugs**: BUG-CUSORD-012

### HS-10: Repair Debug `.txt` File Dump
- **File**: `admin_ret_order.php` · **Lines**: L1761-1768
- **What breaks**: Serialized `$_FILES` written to disk on every repair update
- **Fix approach**: Remove `file_put_contents()` block entirely
- **Linked bugs**: BUG-CUSORD-004

---

## Per-Item Transaction Anti-Pattern (Shared Risk)

> **5 functions share the same broken TX pattern** — `trans_begin()` inside foreach, `trans_status()` outside:

| Function | Line | TX Opens | TX Checked |
|---|---|---|---|
| `order('update')` | L478 | Inside `o_item` foreach | L630 outside |
| `assign_customer_order` assign | L950 | Inside `req_data` foreach | L963 outside |
| `assign_customer_order` reject | L983 | Inside `req_data` foreach | L989+L997 (unreachable) |
| `repair_order_status` | L1854 | Inside `req_data` foreach | L1857 outside |
| `repair_deliver_order_status` | L2350 | Inside `req_data` foreach | L2353 outside |

**Resolution**: Apply a single fix pattern — move `trans_begin()` above the foreach in all 5 locations.

---

## Fix Sequence Recommendation

```
Sprint 1: HS-01, HS-02, HS-03, HS-04   (CRITICAL)
Sprint 2: HS-05, HS-06, HS-07, HS-08   (HIGH)
Sprint 3: HS-09, HS-10 + per-item TX   (MEDIUM)
```
