# Bug Candidates Register: Customer Order

> Consolidated bug list from Rounds 1-16. Severity: CRITICAL / HIGH / MEDIUM / LOW.
> Status: CANDIDATE (unverified) — verify before filing.
> **R16 (2026-03-24)**: BUG-CUSORD-024 cross-referenced to AP-11. No new IDs added (AP-11 = same root cause as 024).

---

## CRITICAL Severity

| ID | Bug | File | Line(s) | Found In | How to Verify |
|---|---|---|---|---|---|
| BUG-CUSORD-001 | Debug SQL leak: `echo $this->db->last_query();exit;` in update rollback | `admin_ret_order.php` | L649 | R6 | Trigger a failed order update — browser shows raw SQL |
| BUG-CUSORD-002 | Double-image path: `$d['image'].$d['image']` concatenates image string with itself | `admin_ret_order.php` | L470 | R6 | Edit order with image → check `customerorderdetails.image` column |
| BUG-CUSORD-003 | SQL injection in cancel: `$_POST['order_id']` and `$_POST['remarks']` passed unsanitized | `admin_ret_order.php` | L660-661 | R6 | Send crafted `order_id` payload, observe SQL error or data change |
| BUG-CUSORD-004 | Debug file dump: `file_put_contents()` writes serialized `$_FILES` to `orders_img/{id}.txt` on every repair update | `admin_ret_order.php` | L1761-1768 | R7 | After any repair update, check `assets/img/orders_img/` for `.txt` files |
| BUG-CUSORD-005 | Tax fields (SGST/CGST/IGST) calculated client-side and passed unchecked — can be tampered | `admin_ret_order.php` | L462-464 | R7 | POST modified tax values and observe they are saved verbatim |
| BUG-CUSORD-006 | Double `trans_begin()` in cart order_place: L1897 and L1926 both call `trans_begin()` — nested TX behaviour undefined in CodeIgniter | `admin_ret_order.php` | L1897, L1926 | R8 | Review CodeIgniter transaction docs — nested `trans_begin()` resets TX state |

---

## HIGH Severity

| ID | Bug | File | Line(s) | Found In | How to Verify |
|---|---|---|---|---|---|
| BUG-CUSORD-007 | Delete-reinsert orphan risk: images deleted at L481 before re-insert — TX fail leaves no images | `admin_ret_order.php` | L481 | R6 | Simulate DB error mid-update, check `customer_order_image` |
| BUG-CUSORD-008 | Delete-reinsert orphan risk: stones deleted at L520 before re-insert | `admin_ret_order.php` | L520 | R6 | Same as above for `ret_order_item_stones` |
| BUG-CUSORD-009 | Delete-reinsert orphan risk: charges deleted at L542 before re-insert | `admin_ret_order.php` | L542 | R6 | Same for `ret_order_other_charges` |
| BUG-CUSORD-010 | Per-item `trans_begin()` (L478) inside foreach — each item is a separate TX, mixing allowed | `admin_ret_order.php` | L478 | R6 | Partially update 3-item order, observe partial DB state |
| BUG-CUSORD-011 | Image filename collision: `mt_rand(120,1230)` → only 1110 possible names in customer order save/update | `admin_ret_order.php` | L217, L507 | R6 | Upload 1110+ images — file overwrite causes image loss |
| BUG-CUSORD-012 | Hard delete without cascade: deleting order leaves orphans in images, stones, charges tables | `admin_ret_order.php` | L386-390 | R6 | Delete an order, query child tables for orphan rows |
| BUG-CUSORD-013 | SQL injection in cancel item: `$_POST['id_orderdetails']` and `$_POST['remarks']` unsanitized | `admin_ret_order.php` | L725-726 | R6 | Send crafted `id_orderdetails` payload |
| BUG-CUSORD-014 | `assign_customer_order()` reject path: `if($upd_data)` always true after array assignment — rollback path unreachable | `admin_ret_order.php` | L989 | R8 | Simulate DB failure in rejection loop — rollback at L997 never fires |
| BUG-CUSORD-015 | `assign_customer_order()` assign path: `trans_begin()` at L950 inside foreach but `trans_status()` checked at L963 outside — if first item fails, loop continues silently | `admin_ret_order.php` | L950, L963 | R8 | Assign 3 items, force DB error on item 2, verify item 1 is present, item 2+ absent |
| BUG-CUSORD-016 | `get_karigar_acknowladgement()` uses `$_GET['id_order']` directly without sanitization | `admin_ret_order.php` | L1009 | R8 | Traverse to `/admin_ret_order/get_karigar_acknowladgement?id_order=1,2` |
| BUG-CUSORD-017 | `generateOrderNo()` race condition: no lock between SELECT last order_no and INSERT — concurrent requests may get same number | `ret_order_model.php` | L138-202 | R5 | Create 2 orders simultaneously from same branch |
| BUG-CUSORD-018 | Repair update calls `generateOrderNo()` at L1630 but never uses result — dead code and wasted DB query | `admin_ret_order.php` | L1630 | R7 | Check: `$order_no` never used in update array |

---

## MEDIUM Severity

| ID | Bug | File | Line(s) | Found In | How to Verify |
|---|---|---|---|---|---|
| BUG-CUSORD-019 | `delete_order_img()` always returns 0 — `$edit_flag` never set to 1 | `ret_order_model.php` | L630-639 | R5 | Call function, check return value |
| BUG-CUSORD-020 | `update_order_des()` always returns 0 — same pattern | `ret_order_model.php` | L640-649 | R5 | Same |
| BUG-CUSORD-021 | Tag Reserve (type 5): immediately sets `orderstatus=4` on save, skipping karigar assignment flow | `admin_ret_order.php` | L272-273 | R6 | Create type-5 order, check orderstatus in DB |
| BUG-CUSORD-022 | OTP cancel: no server-side expiry enforcement — OTP valid indefinitely | `admin_ret_order.php` | L2295-2336 | R6 | Generate OTP, wait >5min, still verifies |
| BUG-CUSORD-023 | Vendor email token reuse: no single-use enforcement — token can accept/reject multiple times | `admin_ret_order.php` | L1963-2000 | R8 | Click accept URL twice — both succeed |
| BUG-CUSORD-024 | `$order_advance > 0` comparison at L670 treats array as truthy — advance receipt always attempted even when advance=0 | `admin_ret_order.php` | L670 | R6 | Cancel order with no advance — check `ret_issue_receipt` for spurious row |
> ⭐ **[AP-11 / R15-R16 NOTE]**: This bug is now formally documented as Anti-Pattern #11. The `get_order_total_advance()` return is an array, not a scalar. The `ajax_order_cancel` code was enhanced post-R14 to actually USE this advance amount (inserting into `ret_issue_receipt`), which makes the always-truthy guard ACTIVELY harmful — it now inserts a zero-amount receipt row on every cancel, even when no advance exists. Fix: `if (!empty($order_advance) && $order_advance['advance_amount'] > 0)`
| BUG-CUSORD-025 | Repair order save type=4: sets `id_orderdetails` on tag but does NOT set `tag_status=8` (code commented out L1533-1546) | `admin_ret_order.php` | L1529-1546 | R7 | Save stock repair, check `ret_taging.tag_status` — still shows old status |
| BUG-CUSORD-026 | `get_karigar_acknowladgement()` at L1023: `$file_name=$order[0]['order_no'].'.pdf'` — uses `$order` (undefined var, should be `$data`) | `admin_ret_order.php` | L1023 | R8 | Generate karigar ack PDF — file saved with null name |

---

## LOW Severity

| ID | Bug | File | Line(s) | Found In | How to Verify |
|---|---|---|---|---|---|
| BUG-CUSORD-027 | Customer/employee not required server-side — orders can be saved without customer | `admin_ret_order.php` | L〜130 | R7 | POST without `order[order_to]` |
| BUG-CUSORD-028 | Description/remarks fields have no XSS sanitization | `admin_ret_order.php` | L175, L469 | R7 | Add `<script>` in description — check if rendered |
| BUG-CUSORD-029 | `ortertype` column stored as copy of `order_type` — redundant and may drift | `ret_order_model.php` | Model schema | R7 | Check orderdetails.ortertype vs customerorder.order_type |

---

## Summary

| Severity | Count |
|---|---|
| CRITICAL | 6 |
| HIGH | 12 |
| MEDIUM | 8 |
| LOW | 3 |
| **Total** | **29** |

> **Priority fixes**: BUG-CUSORD-001 (SQL leak), BUG-CUSORD-003 (injection), BUG-CUSORD-006 (double TX).
> Use `/fix-single-bug` workflow for each.

---

## R9/R10 Additions

### CRITICAL

| ID | Bug | File | Line(s) | How to Verify |
|---|---|---|---|---|
| BUG-CUSORD-030 | **OTP exposed in JSON response**: `'OTP' => $sent_otp` sent to browser | `admin_ret_order.php` | L2286 | Inspect network response on OTP send |

### HIGH

| ID | Bug | File | Line(s) | How to Verify |
|---|---|---|---|---|
| BUG-CUSORD-031 | `unlink()` at L1348 fires before `trans_commit()` — file deleted even on rollback | `admin_ret_order.php` | L1348 | Force DB fail in `delete_order_img()` |
| BUG-CUSORD-032 | `$addData['order']['order_for']` undefined in `update_and_retrive_order_image` L1301 | `admin_ret_order.php` | L1301 | Upload image via retrive path — wrong folder |
| BUG-CUSORD-033 | `mt_rand(120,1230)` again in `update_order_image` and `update_and_retrive` | `admin_ret_order.php` | L1269, L1310 | Same collision risk as BUG-011 |

### LOW

| ID | Bug | File | Line(s) | How to Verify |
|---|---|---|---|---|
| BUG-CUSORD-034 | `update_order_des()` failure message says "Added Successfully" — wrong | `admin_ret_order.php` | L1370 | Force DB fail — misleading UI response |

---

## Updated Summary (R10)

| Severity | Count |
|---|---|
| CRITICAL | 7 |
| HIGH | 15 |
| MEDIUM | 8 |
| LOW | 4 |
| **Total** | **34** |

> **Fix guide with code patches**: [FIX_GUIDE.md](./FIX_GUIDE.md)

---

## R13 Additions — Model Layer SQL Injection

> **CRITICAL**: These are in `ret_order_model.php` — model-layer raw string concatenation into `db->query()`.

### CRITICAL — Model SQL Injections

| ID | Bug | File | Line(s) | How to Verify |
|---|---|---|---|---|
| BUG-CUSORD-035 | `getOrderNos()`: `$SearchTxt` concatenated directly into LIKE query | `ret_order_model.php` | L205 | Send `%' UNION SELECT...` as order search — observe extra rows |
| BUG-CUSORD-036 | `get_ret_settings()`: `$settings` concatenated into WHERE clause | `ret_order_model.php` | L214 | Craft `settings` name with SQL fragment |
| BUG-CUSORD-037 | `fetchEstiBySearch()`: `$SearchTxt` in LIKE query without binding | `ret_order_model.php` | L251 | Send crafted estimation search string |
| BUG-CUSORD-038 | `getEstiDetailsById()`: `$SearchTxt` in 3 raw queries (L258, L260, L267) | `ret_order_model.php` | L258-279 | Send crafted estimation ID |
| BUG-CUSORD-039 | `ajax_getOrders()`: `$data['id_branch']`, `from_date`, `to_date` concatenated | `ret_order_model.php` | L315-317 | Craft branch ID with SQL fragment |
| BUG-CUSORD-040 | `get_new_orderlist()`: 7 params concatenated (`$branch`, `$id_employee`, `$repair_type`, etc.) | `ret_order_model.php` | L493-500 | Craft any filter param with SQL |
| BUG-CUSORD-041 | `getOrderByCus()`: `$id_cus` concatenated directly | `ret_order_model.php` | L209 | Send crafted customer ID |
| BUG-CUSORD-042 | `generateOrderNo()`: `$id_branch`, `$order_type`, `$fin_year` concatenated | `ret_order_model.php` | L151-165 | Race condition + injection in sequential select |

### MEDIUM — Performance

| ID | Bug | File | Line(s) | How to Verify |
|---|---|---|---|---|
| BUG-CUSORD-043 | N+1 query: `get_order_images()` called per item inside foreach in `getCustomerOrderDetails()` | `ret_order_model.php` | L400 | Profile listing with 50-order dataset |
| BUG-CUSORD-044 | `SHOW COLUMNS FROM $table` on every `insertData()` and `updateData()` — not cached | `ret_order_model.php` | L13, L49 | Enable slow query log, observe SHOW COLUMNS frequency |

---

## Final Summary (R13 → R16 refresh)

| Severity | Count |
|---|---|
| CRITICAL | 15 |
| HIGH | 15 |
| MEDIUM | 10 |
| LOW | 4 |
| **Total** | **44** (AP-11 = BUG-024 same root cause, no new ID added) |

> **Fix for model injections**: Replace all string-concat queries with CI Query Builder or `$this->db->query($sql, [$param])` with escape binding.
> **Fix for AP-11 (BUG-024)**: `if (!empty($order_advance) && $order_advance['advance_amount'] > 0)` at `admin_ret_order.php` L670. See [FLOW_RISK_MATRIX.md](./FLOW_RISK_MATRIX.md) FR-CUSORD-005.
