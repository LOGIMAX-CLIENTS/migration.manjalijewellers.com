# Fix Guide: Customer Order — Critical Bug Patches

> Ready-to-apply code fixes for the highest-priority bugs.
> Each fix includes: the bug ID, current code, and the patched replacement.

---

## FIX-001: Remove Debug SQL Leak in `order('update')` Rollback

**Bug**: BUG-CUSORD-001 · **File**: `admin_ret_order.php` · **Line**: L649

```diff
- echo $this->db->last_query();exit;
- $this->db->trans_rollback();
+ // Removed debug leak
+ $this->db->trans_rollback();
+ log_message('error', 'order update failed: ' . $this->db->last_query());
```

---

## FIX-002: Fix Double-Image String in `order('update')`

**Bug**: BUG-CUSORD-002 · **File**: `admin_ret_order.php` · **Line**: L470

```diff
- 'image' => (!empty($d['image']) ? $d['image'].$d['image'] : ((!empty($d['image']) ? $d['image']:NULL ))),
+ 'image' => (!empty($d['image']) ? $d['image'] : NULL),
```

---

## FIX-003: Sanitize SQL Injection in `ajax_order_cancel`

**Bug**: BUG-CUSORD-003 · **File**: `admin_ret_order.php` · **Lines**: L660-661

```diff
- $order_id  = $_POST['order_id'];
- $remarks   = $_POST['remarks'];
+ $order_id  = (int) $this->input->post('order_id');
+ $remarks   = $this->input->post('remarks', TRUE); // XSS clean
```

Apply same pattern at L725-726 for `cancel_order_item`:
```diff
- $id_orderdetails = $_POST['id_orderdetails'];
+ $id_orderdetails = (int) $this->input->post('id_orderdetails');
```

---

## FIX-004: Remove Repair Update Debug File Dump

**Bug**: BUG-CUSORD-004 · **File**: `admin_ret_order.php` · **Lines**: L1761-1768

```diff
- $file = self::IMG_PATH."orders_img/".$orderItems['id_orderdetails'][$key].".txt";
- $filesData = serialize($_FILES);
- $unserializedData = unserialize($filesData);
- $jsonData = json_encode($unserializedData, JSON_PRETTY_PRINT);
- // print_r($file);exit;
- file_put_contents($file, $jsonData);
  if($result){
      $insImageId = $this->$model->insertData(...)
  }
```

---

## FIX-005: Remove OTP Value from JSON Response

**Bug**: BUG-CUSORD (R9) · **File**: `admin_ret_order.php` · **Line**: L2286

```diff
- $status = array('status' => true, 'msg' => 'OTP sent Successfully', 'OTP' => $sent_otp);
+ $status = array('status' => true, 'msg' => 'OTP sent Successfully');
```

---

## FIX-006: Fix Double `trans_begin()` in `cart('order_place')`

**Bug**: BUG-CUSORD-006 · **File**: `admin_ret_order.php` · **Lines**: L1897, L1926

```diff
  // L1893 case 'order_place':
- $this->db->trans_begin();   // L1897 — REMOVE this outer begin
  $i=1;
  ...
  if($_POST['status']==1) {
      ...
-     $this->db->trans_begin();  // L1926 — KEEP only this one
+     $this->db->trans_begin();
      $insOrder = $this->$model->insertData($order,'customerorder');
```

Also move email send OUTSIDE the transaction:
```diff
  if($this->db->trans_status()===TRUE) {
      $this->db->trans_commit();
+     // Send email AFTER commit — not inside TX
+     if(!empty($karigar) && !empty($karigar['email'])) {
+         // ... email sending code here ...
+     }
  }
```

---

## FIX-007: Fix Per-Item Transaction Anti-Pattern (Shared Fix for 5 Functions)

**Bug**: BUG-CUSORD-010, 015 · **Pattern** used in 5 functions

**Before** (broken pattern):
```php
foreach($req_data as $order) {
    $this->db->trans_begin(); // ← WRONG: inside loop
    $this->$model->updateData(...);
}
if($this->db->trans_status() === TRUE) { // ← only checks last iteration
    $this->db->trans_commit();
}
```

**After** (correct pattern):
```php
$this->db->trans_begin(); // ← CORRECT: before loop
foreach($req_data as $order) {
    $this->$model->updateData(...);
}
if($this->db->trans_status() === TRUE) {
    $this->db->trans_commit();
} else {
    $this->db->trans_rollback();
}
```

**Apply to these 5 functions**:
| Function | `trans_begin()` line to move |
|---|---|
| `order('update')` | L478 → before L462 foreach |
| `assign_customer_order` (assign) | L950 → before L945 foreach |
| `assign_customer_order` (reject) | L983 → before L978 foreach |
| `repair_order_status` | L1854 → before L1847 foreach |
| `repair_deliver_order_status` | L2350 → before L2344 foreach |

---

## FIX-008: Fix Always-Truthy Reject Check in `assign_customer_order`

**Bug**: BUG-CUSORD-014 · **File**: `admin_ret_order.php` · **Line**: L989

```diff
- if($upd_data) {
-     $this->db->trans_commit();
- } else {
-     $this->db->trans_rollback();
- }
+ if($this->db->trans_status() === TRUE) {
+     $this->db->trans_commit();
+ } else {
+     $this->db->trans_rollback();
+ }
```

---

## FIX-009: Fix Image Collision Risk (Replace `mt_rand`)

**Bug**: BUG-CUSORD-011 · **Lines**: L217, L507, L1269, L1310

```diff
- $img_name = $insOrderDet."_".mt_rand(120,1230).".jpg";
+ $img_name = $insOrderDet."_".uniqid('', true).".jpg";
```

Apply same replacement at all 4 lines.

---

## FIX-010: Fix `update_order_des` Wrong Failure Message

**File**: `admin_ret_order.php` · **Line**: L1370

```diff
  } else {
-     $responseData = array('status' => FALSE, 'message' => 'Description Added Successfully..');
+     $responseData = array('status' => FALSE, 'message' => 'Unable to Update Description');
  }
```

---

## FIX-011: Fix `unlink` Before Transaction Commit in `delete_order_img`

**File**: `admin_ret_order.php` · **Line**: L1348

```diff
  $this->db->trans_begin();
  $status = $this->$model->updateData($updData,'id_orderdetails',$id_orderdetails,'customerorderdetails');
+ if($this->db->trans_status() === TRUE) {
+     $this->db->trans_commit();
+     unlink(SELF::IMG_PATH.'order/customer_order/'.$id_orderdetails.'/'.$delete_image); // ← AFTER commit
+     $response_data = array('status' => TRUE, 'message' => 'Image Deleted Successfully');
+ } else {
+     $this->db->trans_rollback();
+     $response_data = array('status' => FALSE, 'message' => 'Unable To Proceed Your Request');
+ }
- unlink(SELF::IMG_PATH.'order/customer_order/'.$id_orderdetails.'/'.$delete_image);
- if($this->db->trans_status() === TRUE) { ...
```

---

## Patch Priority

| Fix | Severity | Effort | Sprint |
|---|---|---|---|
| FIX-005 (OTP exposed) | CRITICAL | 1 line | S1 |
| FIX-001 (SQL leak) | CRITICAL | 2 lines | S1 |
| FIX-003 (SQL injection) | CRITICAL | 2 lines | S1 |
| FIX-012 (AP-11 advance guard) | HIGH | 1 line | S1 |
| FIX-002 (double image) | CRITICAL | 1 line | S1 |
| FIX-006 (double TX) | CRITICAL | 5 lines | S1 |
| FIX-007 (per-item TX) | HIGH | 5 × function | S2 |
| FIX-008 (truthy check) | HIGH | 3 lines | S2 |
| FIX-009 (name collision) | HIGH | 4 × line | S2 |
| FIX-004 (file dump) | HIGH | delete block | S2 |
| FIX-010 (wrong message) | LOW | 1 line | S3 |
| FIX-011 (unlink order) | MEDIUM | refactor | S3 |

---

## FIX-012: Fix AP-11 Advance-Reversal Guard (Always-Truthy Array Compare)

**Bug**: BUG-CUSORD-024 / AP-11 · **File**: `admin_ret_order.php` · **Line**: L670

**Root cause**: `get_order_total_advance()` returns an associative array. Comparing an array to `0` using `>` in PHP coerces the array to `true`, so the condition is always truthy — a zero-amount `ret_issue_receipt` row is inserted even for orders with no advance.

```diff
- if($order_advance > 0) {
+ if (!empty($order_advance) && $order_advance['advance_amount'] > 0) {
```

**Effort**: 1 line, 5 minutes. **Verify** with query #10 in SCHEMA_ANALYSIS.md.
