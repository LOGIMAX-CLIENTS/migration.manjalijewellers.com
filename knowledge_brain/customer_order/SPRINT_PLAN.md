# Sprint Plan: Customer Order Bug Fixes

> Actionable sprint stories derived from BUG_CANDIDATES.md and HOTSPOT_MAP.md.
> Each story has: ID, title, acceptance criteria, affected files, and linked bugs.

---

## Sprint 1 — Critical Security & Data Integrity (Fix Now)

### STORY-01: Remove SQL Debug Leak from Order Update Rollback
**Priority**: CRITICAL · **Effort**: XS (1 line) · **Linked bug**: BUG-CUSORD-001

**Story**: As a security-conscious operator, I want the application to never expose raw SQL to the browser, even on errors.

**Acceptance Criteria**:
- [ ] `echo $this->db->last_query();exit;` at L649 is removed
- [ ] Failure is logged to CodeIgniter log via `log_message('error', ...)`
- [ ] Failing order update returns JSON `{ status: false, msg: '...' }` without raw SQL
- [ ] No browser console or network tab shows SQL on a failed update

**Fix**: `admin_ret_order.php` L649 — See FIX_GUIDE.md FIX-001

---

### STORY-02: Fix Double Image Path Concatenation Bug
**Priority**: CRITICAL · **Effort**: XS (1 line) · **Linked bug**: BUG-CUSORD-002

**Story**: As an order manager, I want uploaded images stored with correct paths so they display properly.

**Acceptance Criteria**:
- [ ] `customerorderdetails.image` for updated orders stores single path (not doubled)
- [ ] Existing doubled paths: verify if data migration needed for affected rows
- [ ] All order images display correctly after update

**Fix**: `admin_ret_order.php` L470 — See FIX_GUIDE.md FIX-002

---

### STORY-03: Sanitize SQL Injection in Order Cancel Endpoints
**Priority**: CRITICAL · **Effort**: XS · **Linked bugs**: BUG-CUSORD-003, 013

**Story**: As a security engineer, I want all cancel endpoints to use parameterized input so they are immune to SQL injection.

**Acceptance Criteria**:
- [ ] `ajax_order_cancel`: `$order_id = (int) $this->input->post('order_id')`
- [ ] `ajax_order_cancel`: `$remarks = $this->input->post('remarks', TRUE)` (XSS clean)
- [ ] `cancel_order_item`: `$id_orderdetails = (int) $this->input->post('id_orderdetails')`
- [ ] Sending `order_id=1 OR 1=1` results in no unintended DB changes
- [ ] Sending `remarks=<script>alert(1)</script>` is stored escaped

**Fix**: `admin_ret_order.php` L660-661, L725-726 — See FIX_GUIDE.md FIX-003

---

### STORY-04: Remove OTP Value from Cancel OTP JSON Response
**Priority**: CRITICAL · **Effort**: XS (1 key) · **Linked bug**: BUG-CUSORD-030

**Story**: As a security engineer, I want the OTP cancel endpoint to never expose the OTP value in the API response, so the OTP security gate is meaningful.

**Acceptance Criteria**:
- [ ] `send_order_cancel_otp` response does NOT include `'OTP'` key
- [ ] OTP is delivered only via the registered mobile number (SMS/WhatsApp)
- [ ] Cancellation flow still works correctly without OTP in response

**Fix**: `admin_ret_order.php` L2286 — See FIX_GUIDE.md FIX-005

---

### STORY-05: Fix Double `trans_begin()` in Cart Order Place
**Priority**: CRITICAL · **Effort**: S · **Linked bug**: BUG-CUSORD-006

**Story**: As a developer, I want the cart order-place transaction to have a single, correct transaction boundary so order placements are fully atomic.

**Acceptance Criteria**:
- [ ] Only ONE `trans_begin()` call exists in `cart('order_place')`
- [ ] Email to karigar is sent AFTER `trans_commit()`, not inside TX
- [ ] Verified: if email send fails, order is still committed
- [ ] Verified: if DB fails, order is rolled back and no confirmation email is sent

**Fix**: `admin_ret_order.php` L1897, L1926, L1993 — See FIX_GUIDE.md FIX-006

---

### STORY-06: Remove Debug File Dump from Repair Order Update
**Priority**: HIGH · **Effort**: XS · **Linked bug**: BUG-CUSORD-004

**Story**: As an operator, I want the repair order update to not create `.txt` debug files on the server containing sensitive file upload data.

**Acceptance Criteria**:
- [ ] `file_put_contents()` block at L1761-1768 removed
- [ ] `orders_img/` directory cleaned of existing `.txt` files
- [ ] Repair order update continues to function normally

**Fix**: `admin_ret_order.php` L1761-1768 — See FIX_GUIDE.md FIX-004

---

## Sprint 2 — High Severity Data & Logic Fixes

### STORY-07: Fix Per-Item Transaction Anti-Pattern (5 Functions)
**Priority**: HIGH · **Effort**: M · **Linked bugs**: BUG-CUSORD-010, 015

**Story**: As a developer, I want all multi-item update functions to use a single transaction boundary wrapping the entire forEach loop.

**Acceptance Criteria**:
- [ ] `trans_begin()` moved outside foreach in all 5 functions (see FIX_GUIDE.md FIX-007)
- [ ] Partial failure in any item causes full rollback of all items in that operation
- [ ] Multi-item assign/update/status-change tested with DB error simulation

**Affected functions**: `order('update')`, `assign_customer_order` (×2), `repair_order_status`, `repair_deliver_order_status`

---

### STORY-08: Fix Always-Truthy Reject Path in `assign_customer_order`
**Priority**: HIGH · **Effort**: XS · **Linked bug**: BUG-CUSORD-014

**Story**: As a karigar manager, I want order rejections to properly rollback if the DB update fails.

**Acceptance Criteria**:
- [ ] `if($upd_data)` at L989 replaced with `if($this->db->trans_status() === TRUE)`
- [ ] DB failure on rejection triggers rollback, not commit
- [ ] Flash message reflects actual outcome

**Fix**: `admin_ret_order.php` L989 — See FIX_GUIDE.md FIX-008

---

### STORY-09: Replace `mt_rand(120,1230)` Image Filenames (4 Locations)
**Priority**: HIGH · **Effort**: XS × 4 · **Linked bug**: BUG-CUSORD-011, 033

**Story**: As a system administrator, I want image filenames to be truly unique to prevent accidental overwrites.

**Acceptance Criteria**:
- [ ] All 4 image naming locations use `uniqid('', true)` or `md5(microtime() . rand())`
- [ ] Two simultaneous uploads cannot produce the same filename
- [ ] Existing images referenced in DB still accessible (no rename of existing)

**Files**: `admin_ret_order.php` L217, L507, L1269, L1310

---

### STORY-10: Fix `unlink()` Before `trans_commit()` in `delete_order_img`
**Priority**: HIGH · **Effort**: XS · **Linked bug**: BUG-CUSORD-031

**Story**: As a developer, I want image deletion to only remove the physical file AFTER the database record is successfully updated.

**Acceptance Criteria**:
- [ ] `unlink()` at L1348 moved to after `trans_commit()` block
- [ ] DB failure → file NOT deleted, DB rolled back
- [ ] DB success → file deleted, DB committed

**Fix**: `admin_ret_order.php` L1346-1357 — See FIX_GUIDE.md FIX-011

---

## Sprint 3 — Medium & View Layer Fixes

### STORY-11: Add Cascade Delete for Order Header Deletion
**Priority**: MEDIUM · **Effort**: S · **Linked bug**: BUG-CUSORD-012

**Story**: As a DBA, I want deleting a customer order to automatically remove all associated images, stones, and charges.

**Acceptance Criteria**:
- [ ] Before `deleteData('id_customerorder', ...)`, fetch all `id_orderdetails` for the order
- [ ] Delete from `customer_order_image`, `ret_order_item_stones`, `ret_order_other_charges` for those IDs
- [ ] All operations within same transaction
- [ ] Verify no orphan rows remain after delete

**File**: `admin_ret_order.php` L386-419

---

### STORY-12: Fix XSS in Order Print View (cus_order_print.php)
**Priority**: MEDIUM · **Effort**: S · **Linked bugs**: BUG-CUSORD-028

**Story**: As a security engineer, I want all user-supplied data rendered in the print view to be HTML-escaped to prevent XSS.

**Acceptance Criteria**:
- [ ] All `echo $val['product_name']`, `$val['description']`, `$val['pur_description']`, `$val['design_name']`, `$stoneItems['stone_name']`, `$cus_details['cus_name']` wrapped with `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` or CI's `html_escape()`
- [ ] Order print still renders correctly for normal data
- [ ] Injecting `<script>alert(1)</script>` in description → escaped in print

**File**: `cus_order_print.php` L166-320 (approx. 8 echo points)

---

### STORY-13: Fix Wrong Failure Message in `update_order_des`
**Priority**: LOW · **Effort**: XS (1 string) · **Linked bug**: BUG-CUSORD-034

**Acceptance Criteria**:
- [ ] Failure response shows `'Unable to Update Description'` not `'Description Added Successfully..'`

**Fix**: `admin_ret_order.php` L1370 — See FIX_GUIDE.md FIX-010

---

## Sprint Summary

| Sprint | Stories | Severity | Estimated Effort |
|---|---|---|---|
| S1 | STORY-01 to 06 | CRITICAL / HIGH | ~1 dev-day |
| S2 | STORY-07 to 10 | HIGH | ~2 dev-days |
| S3 | STORY-11 to 13 | MEDIUM / LOW | ~1 dev-day |
| **Total** | **13 stories** | | **~4 dev-days** |

> Reference: [FIX_GUIDE.md](./FIX_GUIDE.md) for code patches · [BUG_CANDIDATES.md](./BUG_CANDIDATES.md) for full bug list · [HOTSPOT_MAP.md](./HOTSPOT_MAP.md) for fix priorities
