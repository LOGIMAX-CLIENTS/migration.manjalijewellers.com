# Edge Cases Register: Customer Order

> Known boundary conditions, special paths, and edge states that require careful handling.

---

## Order Creation Edge Cases

### EC-01: Zero-Item Order Submission
- **Trigger**: User submits order form with no items added
- **Behavior**: `customerorder` header is inserted, foreach is skipped, TX commits with empty order
- **Risk**: Orphan header row with no detail rows; `order_pcs=0`, `order_approx_wt=0`
- **Verification**: Submit order with empty item list, check `customerorder` table

### EC-02: Order Date Before Financial Year Start
- **Trigger**: Day-closing date falls before active financial year
- **Behavior**: `fin_year_code` prefix will be mismatched with `order_date`
- **Risk**: Financial year mismatch in reports; `order_no` prefix inconsistent
- **Code**: L1452-1456 (repair), L~124-130 (customer order)

### EC-03: Multiple Users Creating Orders Simultaneously (Same Branch)
- **Trigger**: Concurrent order saves from same branch
- **Behavior**: `generateOrderNo()` does SELECT then INSERT without locking
- **Risk**: Duplicate order numbers (see BUG-CUSORD-017)
- **Code**: `ret_order_model.php` L138-202

### EC-04: Order with All Items Cancelled Mid-Flow
- **Trigger**: All items cancelled via `cancel_order_item` after placement
- **Behavior**: Each item → status=6, but header `customerorder.order_status` remains 0/1
- **Risk**: Header appears "active" in listings but has no live items
- **Verification**: Cancel all items one by one, check header status

### EC-05: Tag Assigned to Two Orders Simultaneously
- **Trigger**: Two users scan same tag on two order forms and submit
- **Behavior**: Both updates fire; second write wins; first order has tag_id pointing to unowned tag
- **Risk**: Silent data corruption in tagging system
- **Code**: L1531 (repair save), L481 (customer update)

---

## Financial Calculation Edge Cases

### EC-06: Zero Rate Order
- **Trigger**: User sets `rate=0` or `mc=0` legitimately (e.g. complementary orders)
- **Behavior**: Accepted — no server-side minimum validation
- **Risk**: Intentional or accidental zero-value orders may cause reporting anomalies

### EC-07: Negative Weight Submission
- **Trigger**: User sets `net_wt` or `weight` to negative value
- **Behavior**: Accepted — no server-side range validation
- **Risk**: Negative weight stored in DB; total weight calculations may go negative
- **Code**: VALIDATION_GAPS.md EC row — weight field has `type=number` but no `min=0`

### EC-08: Stone Amount Mismatch with Calculated Total
- **Trigger**: User modifies `stn_amt` in stone modal, then closes without saving
- **Behavior**: `stn_amt` hidden field retains old value; JSON stone_details updated but total not recalculated
- **Risk**: DB stores inconsistent `stn_amt` vs sum of `ret_order_item_stones.price`
- **Diagnostic Query**: SCHEMA_ANALYSIS.md Query #7

### EC-09: Tax Fields on Cross-State Orders (IGST vs SGST/CGST)
- **Trigger**: Order from branch in State A to customer in State B
- **Behavior**: Tax type selection is UI-only; no server enforcement of which tax applies
- **Risk**: Wrong tax type stored — regulatory compliance risk
- **Code**: All tax fields unvalidated per VALIDATION_GAPS.md

### EC-10: Making Charge Type Switch After Entry
- **Trigger**: User enters MC value, then changes `id_mc_type` from per-gram to per-piece
- **Behavior**: MC value stays the same; calculation formula changes but stored value doesn't recalculate
- **Risk**: MC amount becomes incorrect relative to selected type

---

## Image Handling Edge Cases

### EC-11: Very Large Base64 Image Upload
- **Trigger**: User uploads image >5MB via webcam or file picker
- **Behavior**: `base64ToFile()` runs in PHP — no size limit enforced
- **Risk**: Memory exhaustion, PHP fatal error mid-transaction
- **Code**: L190, L1553, L1556

### EC-12: Non-JPEG Image Uploaded
- **Trigger**: User uploads PNG, GIF, or WEBP
- **Behavior**: `upload_img()` supports JPEG, GIF, PNG (L1150-1178); WEBP falls to `default: return false`
- **Risk**: WEBP upload silently fails (`$result=false`), no insert, no error response
- **Code**: L1173-1179

### EC-13: Image Folder Permission Error
- **Trigger**: `assets/img/orders/` directory not writable
- **Behavior**: `mkdir()` may fail silently; `imagejpeg()` returns false
- **Risk**: Images not saved, `customer_order_image` row inserted with filename but no actual file
- **Code**: L1563-1566

### EC-14: Legacy `#`-Delimited vs New `customer_order_image` Table
- **Trigger**: Orders created before migration to separate image table
- **Behavior**: `get_ordersby_id()` reads from `customerorderdetails.image` (old `#` format at L1041); new orders use separate `customer_order_image` table
- **Risk**: Mixed image storage formats; some endpoints only read one format
- **Code**: L1041 (`get_ordersby_id`), L1236 (`get_img_by_id` uses `##` separator)

---

## Repair-Specific Edge Cases

### EC-15: Repair with Zero Items
- **Trigger**: Repair order submitted with no items (same as EC-01)
- **Behavior**: `customerorder` inserted, pcs/wt totals updated to 0
- **Risk**: Empty repair orders in the system; karigar gets notification for nothing

### EC-16: Stock Repair (Type=4) Tag Status Inconsistency on Save vs Update
- **Trigger**: New stock repair saved (type=4), then immediately updated
- **Behavior**: On save: `id_orderdetails` set on tag (status NOT set to 8); on update: `tag_status=8` IS set
- **Risk**: Tag status wrong after save-only flow; only correct after first update
- **Code**: L1529-1546 (save), L1718-1730 (update), BUG-CUSORD-025

### EC-17: `completed_weight` Double-Count in `update_repair_order_other_details`
- **Trigger**: User submits sub-details twice for same item
- **Behavior**: L2165 deletes old sub-details but L2210 adds `$weight + orderDetails['weight']`; if `orderDetails['weight']` already included previous `completed_weight`, it double-counts
- **Risk**: `completed_weight` inflated on second sub-details submission
- **Code**: L2169, L2210, BUG-CUSORD-033 context

---

## OTP / Cancellation Edge Cases

### EC-18: OTP for Multi-Number Branch
- **Trigger**: Branch has multiple OTP recipient mobile numbers (comma-separated)
- **Behavior**: Loop generates unique OTP per mobile and stores CSV in session (`$sent_otp`)
- **Risk**: `trans_begin()` inside loop (see TX-14), only last `$insId` checked at L2283
- **Code**: L2249-2282

### EC-19: Session-Based OTP with Concurrent Logins
- **Trigger**: Same user logged in from two tabs — OTP requested from Tab 1, then Tab 2
- **Behavior**: Tab 2 `set_userdata('Ordercancel_otp')` overwrites Tab 1's OTP in session
- **Risk**: Tab 1 OTP verification will fail; user must re-request
- **Code**: L2253-2257

### EC-20: Empty Remarks in Cancel Flow
- **Trigger**: Cancel OTP confirmed but no remarks entered
- **Behavior**: Client validates required, but server does not — `reject_reason=''` OR `NULL` stored
- **Risk**: Cancellation without mandatory reason audit trail

---

## Summary

| Category | Count |
|---|---|
| Order Creation | 5 |
| Financial Calculation | 5 |
| Image Handling | 4 |
| Repair-Specific | 3 |
| OTP/Cancellation | 3 |
| **Total Edge Cases** | **20** |
