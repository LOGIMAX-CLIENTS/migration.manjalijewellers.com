# Transaction Trace: Customer Order

> Deep trace of every transaction block showing exact INSERT/UPDATE/DELETE sequence with line numbers.

---

## TX-1: `order('save')` — New Customer Order (L~120-314)

### Pre-Transaction Setup
```
L124: $order_datetime = date('Y-m-d', strtotime(POST[order_date]))
L125: $tag_id = POST[tag_id] or NULL
L127-128: Date conversion for smith_remainder_date, smith_due_date, cus_due_date
```

### Transaction Block
```
L130: trans_begin()
L131: INSERT → customerorder (header)          → returns $insOrder
  ↓ foreach POST[o_item] as $d:
    L185: INSERT → customerorderdetails (item)  → returns $insOrderDet
    L190-227: BASE64 → file → INSERT → customer_order_image (per image)
    L228-246: INSERT → ret_order_item_stones (per stone, if any)
    L248-262: INSERT → ret_order_other_charges (per charge, if any)
    L263-271: IF type=5 (Tag Reserve):
              UPDATE → ret_taging (set id_orderdetails)
              UPDATE → customerorderdetails (orderstatus→4)
              UPDATE → joborder (orderstatus→4)
  ↓ end foreach
L290: trans_status() check
L292: trans_commit() OR L310: trans_rollback()
```

### Risk Points
| Line | Risk | Severity |
|---|---|---|
| L217 | `mt_rand(120,1230)` for image name → **1110 possible values** → collision overwrites | **HIGH** |
| L182 | Items only inserted if `$insOrder > 0`, but no explicit check before image processing | MEDIUM |
| L272-273 | Tag Reserve: status set to 4 immediately on save (skips assignment) | LOW (by design) |

---

## TX-2: `order('update')` — Update Existing Order (L421-654)

### Pre-Transaction Setup
```
L423: $updData = $_POST (entire POST body)
L424-426: Date conversions
L427: $id = order_id from POST
```

### Transaction Block (per item)
```
  ↓ foreach POST[o_item] as $d:
    IF $d['id_orderdetails'] EXISTS (updating existing item):
      L478: trans_begin()
      L479: UPDATE → customerorderdetails
      L481: DELETE → customer_order_image (ALL for this item) ⚠️
      L485-516: BASE64 → file → INSERT → customer_order_image (re-insert)
      L520: DELETE → ret_order_item_stones (ALL for this item) ⚠️
      L521-539: INSERT → ret_order_item_stones (re-insert)
      L542: DELETE → ret_order_other_charges (ALL for this item) ⚠️
      L543-554: INSERT → ret_order_other_charges (re-insert)
    ELSE (new item during update):
      L566: trans_begin()
      L567: INSERT → customerorderdetails (new row)
      L570-603: INSERT → stones + charges
      L604-611: IF type=5: UPDATE → ret_taging, customerorderdetails, joborder
  ↓ end foreach
L630: trans_status() check
L632: trans_commit() OR L650: trans_rollback()
```

### Risk Points
| Line | Risk | Severity |
|---|---|---|
| L470 | `'image' => $d['image'].$d['image']` — **DOUBLE IMAGE** string concatenation bug | **CRITICAL** |
| L481 | Images DELETED before re-insert — if TX fails after L481, images lost | **HIGH** |
| L520 | Stones DELETED before re-insert — same orphan risk | **HIGH** |
| L542 | Charges DELETED before re-insert — same orphan risk | **HIGH** |
| L478 | `trans_begin()` inside foreach → **each item is a separate transaction** | **HIGH** |
| L507 | `mt_rand(120,1230)` collision risk (same as save) | **HIGH** |
| L649 | `echo $this->db->last_query();exit;` — **DEBUG LEAK** in rollback path | **CRITICAL** |

---

## TX-3: `order('delete')` — Delete Order (L386-419)

### Transaction Block
```
L387: trans_begin()
L388: DELETE → customerorder (header)
L389: DELETE → customerorderdetails (all items)
L390: DELETE → ret_order_advance_payment (advance records)
L391: trans_status() check
L393: trans_commit() OR L416: trans_rollback()
```

### Risk Points
| Line | Risk | Severity |
|---|---|---|
| L388-390 | Hard DELETE (not soft delete) — data unrecoverable | **HIGH** |
| L388-390 | No cascade to `customer_order_image`, `ret_order_item_stones`, `ret_order_other_charges` — **orphan rows** | **HIGH** |
| — | No file system cleanup — image files remain on disk | MEDIUM |

---

## TX-4: `ajax_order_cancel` — Cancel Entire Order (L656-721)

> ⭐ **R15/R16 Update**: This TX was enhanced post-R14 to include advance-payment reversal. Full details below.

### Transaction Block
```
L657: trans_begin()
L661: UPDATE → customerorder (order_status=6, reject_reason=POST[remarks])   ☠️ AP-3: $orderid not cast
L662: UPDATE → customerorderdetails (orderstatus=6, same reason)
L663-666: foreach details: UPDATE → ret_taging (id_orderdetails=NULL)   ⟵ Tag freed ✅
L667: $order_advance = get_order_total_advance($orderid)  ← returns ARRAY
L668: $bill_no = bill_no_generate($order_from, $order_advance['is_eda'])
L669: $fin_year = get_FinancialYear()
L670: if($order_advance > 0)  ← ⚠️ AP-11: array compared to int, ALWAYS truthy
  L671: $id_customer = get_order_customer_id($orderid)
  L673-694: BUILD $receiptinsData (
    type=2, amount=$order_advance['advance_amount'],
    receipt_type=5, id_branch, id_customer,
    bill_no, fin_year_code, id_customerorder, is_eda
  )
  L695: INSERT → ret_issue_receipt (advance refund row)  ⚠️ Always inserted due to AP-11
L697: trans_status() check
L699: trans_commit() OR L719: trans_rollback()
```

### Gap: joborder NOT restored on cancel
> `joborder.orderstatus` is set to 4 by `order('save')` type=5 but is **never reset on cancel**. Job order shows stale WIP state.

### Risk Points
| Line | Risk | Severity |
|---|---|---|
| L660-661 | **SQL INJECTION** — `$_POST['order_id']` and `$_POST['remarks']` passed directly | **CRITICAL** |
| L670 | **AP-11** — `$order_advance > 0` compares array to int — always true — inserts zero-amount receipt | **HIGH** |
| — | `joborder.orderstatus` not restored on cancel | **MEDIUM** |

---

## TX-5: `cancel_order_item` — Cancel Single Item (L723-761)

### Transaction Block
```
L724: trans_begin()
L725: $id_orderdetails = $_POST['id_orderdetails']
L726: UPDATE → customerorderdetails (orderstatus=6, cancelled_by, date)
L728-733: IF tag linked: UPDATE → ret_taging (id_orderdetails=NULL)
L734: trans_status() check
L736: trans_commit() OR L758: trans_rollback()
```

### Risk Points
| Line | Risk | Severity |
|---|---|---|
| L725 | **SQL INJECTION** — `$_POST['id_orderdetails']` not sanitized | **HIGH** |
| L726 | `$_POST['remarks']` injected directly into UPDATE | **HIGH** |

---

## Summary: Transaction Safety Matrix

| Operation | Trans Boundaries | Delete-Reinsert | Cascade Clean | SQL Injection | Debug Leak |
|---|---|---|---|---|---|
| Save (new) | ✅ Single | ❌ N/A | ❌ N/A | ⚠️ Low | ❌ None |
| Update | ⚠️ Per-item | ⚠️ Images+Stones+Charges | ❌ None | ⚠️ Low | ❌ **L649** |
| Delete | ✅ Single | ❌ N/A | ❌ **Orphans** | ⚠️ Low | ❌ None |
| Cancel (order) | ✅ Single | ❌ N/A | ✅ Tags cleared | ❌ **L660-661** | ❌ None |
| Cancel (item) | ✅ Single | ❌ N/A | ✅ Tag cleared | ❌ **L725-726** | ❌ None |

---

## TX-6: `repair_order('save')` — New Repair Order (L1447-1618)

### Transaction Block
```
L1452: get_FinancialYear()
L1454: generateOrderNo($order_from, $order_type)
L1471: trans_begin()
L1472: INSERT → customerorder (header)            → returns $insOrder
  ↓ foreach POST[order_item][id_product] as $key:
    L1504: INSERT → customerorderdetails (item)    → returns $insOrderDet
    L1506-1528: INSERT → ret_order_item_stones (per stone)
    L1529-1531: IF type=4 (Stock Repair):
                UPDATE → ret_taging (set id_orderdetails)
    L1553-1583: BASE64 → file → INSERT → customer_order_image (per image)
  ↓ end foreach
L1587: UPDATE → customerorder (set order_pcs, order_approx_wt)
L1590: trans_status() check
L1592: trans_commit() OR L1614: trans_rollback()
```

### Risk Points
| Line | Risk | Severity |
|---|---|---|
| L1573 | `mt_rand(100001,999999)` — better range than customer order (899K values) | LOW |
| L1529-1531 | Type 4: Sets `id_orderdetails` on tag but does NOT set `tag_status=8` (commented out at L1533-1546) | **MEDIUM** — tag status may be inconsistent with save vs update |
| L1475 | POST items accessed by `$_POST['order_item']` — no sanitization | HIGH |

### Key Difference from Customer Order Save
- Repair save uses array-indexed POST (`order_item[id_product][]`) vs customer order's object-based POST (`o_item[]`)
- Repair includes `id_repair_master`, `pure_wt`, `tag_id` per item
- Repair updates total `order_pcs` / `order_approx_wt` on header AFTER all items inserted

---

## TX-7: `repair_order('update')` — Update Repair Order (L1621-1810)

### Transaction Block
```
L1630: generateOrderNo() — generates NEW order_no (⚠️ may change order number)
L1650: trans_begin()
L1651: UPDATE → customerorder (header)
  ↓ foreach POST[order_item][id_product] as $key:
    IF id_orderdetails EXISTS:
      L1689: UPDATE → customerorderdetails
    ELSE:
      L1691: INSERT → customerorderdetails (new item)
    L1696-1698: IF existing item: DELETE → ret_order_item_stones ⚠️
    L1699-1716: INSERT → ret_order_item_stones (re-insert)
    L1718-1730: IF type=4 (Stock Repair):
                UPDATE → ret_taging (tag_status=8)
                UPDATE → ret_taging_status_log (status=8, from_branch)
    L1733: DELETE → customer_order_image (ALL for this item) ⚠️
    L1737-1775: BASE64 → file → INSERT → customer_order_image (re-insert)
    L1761-1768: file_put_contents() → serialized $_FILES to .txt ⚠️
  ↓ end foreach
L1779: UPDATE → customerorder (order_pcs, order_approx_wt)
L1782: trans_status() check
L1784: trans_commit() OR L1806: trans_rollback()
```

### Risk Points
| Line | Risk | Severity |
|---|---|---|
| L1761-1768 | **DEBUG ARTIFACT** — `file_put_contents()` dumps serialized `$_FILES` to `orders_img/{id}.txt` | **CRITICAL** — file system leak of upload data |
| L1733 | Images DELETED before re-insert — orphan risk on TX failure | **HIGH** |
| L1697 | Stones DELETED before re-insert — orphan risk on TX failure | **HIGH** |
| L1630 | `generateOrderNo()` called but never used (order_no not in UPDATE array) | MEDIUM — dead code |
| L1718-1730 | Type 4: Sets `tag_status=8` AND logs to `ret_taging_status_log` — different behavior from save | MEDIUM — inconsistency |

---

## Updated Summary: Transaction Safety Matrix (7 TX Blocks)

| Operation | Lines | Trans Boundaries | Delete-Reinsert | SQL Injection | Debug Leak |
|---|---|---|---|---|---|
| Customer Save | L120-314 | ✅ Single | ❌ N/A | ⚠️ Low | ❌ None |
| Customer Update | L421-654 | ⚠️ Per-item | ⚠️ Img+Stone+Charge | ⚠️ Low | ❌ **L649** |
| Order Delete | L386-419 | ✅ Single | ❌ N/A (orphans) | ⚠️ Low | ❌ None |
| Cancel Order | L656-721 | ✅ Single | ❌ N/A | ❌ **L660** | ❌ None |
| Cancel Item | L723-761 | ✅ Single | ❌ N/A | ❌ **L725** | ❌ None |
| **Repair Save** | L1447-1618 | ✅ Single | ❌ N/A | ⚠️ POST | ❌ None |
| **Repair Update** | L1621-1810 | ✅ Single | ⚠️ Img+Stone | ⚠️ POST | ❌ **L1761** |

---

## TX-8: `assign_customer_order()` — Karigar Assignment / Rejection (L932-1003)

### Transaction Block (Assign path, req_status=1)
```
  ↓ foreach $req_data as $data:
    L950: trans_begin()   ← inside foreach ⚠️
    L961: UPDATE → customerorderdetails (assign_to, id_karigar, orderstatus=3, dates)
  ↓ end foreach
L963: trans_status() check  ← outside foreach ⚠️
L965: trans_commit() OR L971: trans_rollback()
```

### Transaction Block (Reject path, req_status≠1)
```
  ↓ foreach $req_data as $data:
    L983: trans_begin()
    L985: UPDATE → customerorderdetails (orderstatus=6)
  ↓ end foreach
L989: if($upd_data)  ← ⚠️ ALWAYS TRUE (array assignment)
L991: trans_commit()
L997: trans_rollback()  ← UNREACHABLE
```

### Risk Points
| Line | Risk | Severity |
|---|---|---|
| L950 | `trans_begin()` inside foreach → each item starts new TX, only last TX boundary checked | **HIGH** |
| L989 | `if($upd_data)` — `$upd_data` is array set at L984, always truthy → rollback at L997 never fires | **HIGH** |
| L956 | `'assigned_by' => $this->config->item('uid')` — uses `config->item()` not `session->userdata()` for user ID | MEDIUM |

---

## TX-9: `add_to_cart()` — Add Items to Cart (L2058-2088)

### Transaction Block
```
L2063: trans_begin()
  ↓ foreach $reqdata as $items:
    L2077: INSERT → order_cart
  ↓ end foreach
L2079: trans_status() check
L2081: trans_commit() OR L2084: trans_rollback()
```

### Notes
✅ Clean single-transaction block. No SQL injection. No delete-reinsert.

---

## TX-10: `cart('order_place')` — Place Cart as Stock Order (L1893-2043)

### Transaction Block
```
L1897: trans_begin()  ← outer ⚠️
  IF status=1 (approve):
    L1926: trans_begin()  ← SECOND trans_begin INSIDE ⚠️
    L1927: INSERT → customerorder (header)
    foreach req_data:
      L1946: INSERT → customerorderdetails (orderstatus=3)
      L1949: UPDATE → order_cart (orderstatus=1, id_orderdetails set)
    L1964: INSERT → ret_order_email_logs (token)
    L1993: send_email() to karigar
    L1996: UPDATE → ret_order_email_logs (status=1 if sent)
  ELSE (reject):
    foreach req_data:
      L2020: UPDATE → order_cart (orderstatus=2, reject_reason)
L2029: trans_status() check
L2031: trans_commit() OR L2038: trans_rollback()
```

### Risk Points
| Line | Risk | Severity |
|---|---|---|
| L1897+L1926 | **DOUBLE `trans_begin()`** — CodeIgniter's DB class resets TX on nested begin; prior operations may not be protected | **CRITICAL** |
| L1993 | Email sent INSIDE transaction — if TX rolls back, email already sent but order not saved | **HIGH** |
| L2029 | Single `trans_status()` for both approve and reject paths — if reject UPDATE fails silently, still commits | MEDIUM |

---

## Final Summary: Transaction Safety Matrix (10 TX Blocks)

| Operation | Lines | Trans Boundaries | Delete-Reinsert | SQL Injection | Debug Leak |
|---|---|---|---|---|---|
| Customer Save | L120-314 | ✅ Single | ❌ N/A | ⚠️ Low | ❌ None |
| Customer Update | L421-654 | ⚠️ Per-item | ⚠️ Img+Stone+Charge | ⚠️ Low | ❌ **L649** |
| Order Delete | L386-419 | ✅ Single | ❌ N/A (orphans) | ⚠️ Low | ❌ None |
| Cancel Order | L656-721 | ✅ Single | ❌ N/A | ❌ **L660** | ❌ None |
| Cancel Item | L723-761 | ✅ Single | ❌ N/A | ❌ **L725** | ❌ None |
| Repair Save | L1447-1618 | ✅ Single | ❌ N/A | ⚠️ POST | ❌ None |
| Repair Update | L1621-1810 | ✅ Single | ⚠️ Img+Stone | ⚠️ POST | ❌ **L1761** |
| **Assign/Reject** | L932-1003 | ⚠️ Per-item | ❌ N/A | ⚠️ Low | ❌ None |
| **Add to Cart** | L2058-2088 | ✅ Single | ❌ N/A | ⚠️ Low | ❌ None |
| **Cart Order Place** | L1893-2043 | ❌ **Double TX** | ❌ N/A | ⚠️ Low | ❌ None |

---

## TX-11: `repair_order_status()` — Mark Repair Complete (L1841-1870)

### Transaction Block
```
  ↓ foreach $req_data as $order:
    L1854: trans_begin()  ← inside foreach ⚠️
    L1855: UPDATE → customerorderdetails (orderstatus=4, completed_weight, rate)
  ↓ end foreach
L1857: trans_status() check  ← outside foreach ⚠️
L1859: trans_commit() OR L1865: trans_rollback()
```

### Risk Points
| Line | Risk | Severity |
|---|---|---|
| L1854 | `trans_begin()` inside foreach — same anti-pattern as assign | **HIGH** |
| L1851 | `rate` field set to `$order['final_amount']` — no server validation of amount | MEDIUM |

---

## TX-12: `repair_deliver_order_status()` — Mark Repair Delivered (L2338-2366)

### Transaction Block
```
  ↓ foreach $req_data as $order:
    L2350: trans_begin()  ← inside foreach ⚠️
    L2351: UPDATE → customerorderdetails (orderstatus=5, delivered_date)
  ↓ end foreach
L2353: trans_status() check  ← outside foreach ⚠️
L2355: trans_commit() OR L2361: trans_rollback()
```

### Risk Points
| Line | Risk | Severity |
|---|---|---|
| L2350 | Same per-item `trans_begin()` anti-pattern | **HIGH** |
| — | No check that item is in status=4 before setting 5 — can skip steps | MEDIUM |

---

## TX-13: `update_repair_order_other_details()` — Add Repair Sub-Items (L2158-2224)

### Transaction Block
```
L2164: trans_begin()
L2165: DELETE → customer_order_other_details (ALL for this item) ⚠️
  ↓ foreach order_item[id_ret_category] as $key:
    L2187: INSERT → customer_order_other_details (metal detail)
    IF stone_details:
      L2206: INSERT → customer_order_stone_details (per stone)
  ↓ end foreach
L2210: UPDATE → customerorderdetails (orderstatus=4, rate, completed_weight)
L2211: trans_status() check
L2213: trans_commit() OR L2219: trans_rollback()
```

### Risk Points
| Line | Risk | Severity |
|---|---|---|
| L2165 | DELETE before insert — orphan risk on TX failure | **HIGH** |
| L2169 | `$weight` used without initialization — PHP notice, value accumulates across calls if persistent | MEDIUM |
| L2162-2163 | `$_POST['id_orderdetails']` and `$_POST['service_charge']` — no sanitization | HIGH |
| L2210 | `completed_weight = weight + orderDetails['weight']` — adds new + original weight, may double-count | MEDIUM |

---

## TX-14: `send_order_cancel_otp()` — Generate and Send OTP (L2238-2294)

### Transaction Block
```
  ↓ foreach $mobile_num as $mobile:
    L2270: trans_begin()  ← inside foreach ⚠️
    L2271: INSERT → otp (mobile, otp_code, timestamp, module, id_emp)
    IF $insId: (optional) send WhatsApp (commented out)
  ↓ end foreach
L2283: if($insId)  ← uses LAST iteration's $insId only ⚠️
L2285: trans_commit() OR L2290: trans_rollback()
```

### Risk Points
| Line | Risk | Severity |
|---|---|---|
| L2286 | **OTP EXPOSED** — `'OTP' => $sent_otp` returned in JSON response | **CRITICAL** |
| L2270 | `trans_begin()` inside foreach — only last TX committed | **HIGH** |
| L2283 | `if($insId)` checks only last iteration — earlier failures ignored | HIGH |
| L2254 | OTP is `mt_rand(1001,9999)` — only 8999 possible values, brute-forceable | MEDIUM |
| L2257 | OTP stored in session — if user has multiple tabs/sessions, OTP may be wrong | MEDIUM |

---

## TX-15: `verify_order_cancel_otp()` — Verify OTP (L2295-2336)

### Transaction Block
```
L2301: foreach $otp[0] as $OTP:
  IF $OTP == $post_otp:
    IF time() >= expiry: → expired response
    ELSE:
      L2317: trans_begin()
      L2318: UPDATE → otp (is_verified=1, verified_time)
      L2322: trans_commit() OR L2325: trans_rollback()
    break
  ELSE: invalid OTP response
```

### Risk Points
| Line | Risk | Severity |
|---|---|---|
| L2318 | OTP matched by `otp_code` value — if same code exists for another user, wrong record updated | MEDIUM |
| — | No rate limiting on verify attempts — brute force of 8999-value OTP space | **HIGH** |
| L2305 | Expiry checked AFTER match — expired OTP still leaks timing info | LOW |

---

## COMPLETE Summary: Transaction Safety Matrix (15 TX Blocks)

| Operation | Lines | Trans Boundaries | Delete-Reinsert | SQL Injection | Debug/Info Leak |
|---|---|---|---|---|---|
| Customer Save | L120-314 | ✅ Single | ❌ N/A | ⚠️ Low | ❌ None |
| Customer Update | L421-654 | ⚠️ Per-item | ⚠️ Img+Stone+Charge | ⚠️ Low | ❌ **L649** SQL |
| Order Delete | L386-419 | ✅ Single | ❌ N/A (orphans) | ⚠️ Low | ❌ None |
| Cancel Order | L656-721 | ✅ Single | ❌ N/A | ❌ **L660** | ❌ None |
| Cancel Item | L723-761 | ✅ Single | ❌ N/A | ❌ **L725** | ❌ None |
| Repair Save | L1447-1618 | ✅ Single | ❌ N/A | ⚠️ POST | ❌ None |
| Repair Update | L1621-1810 | ✅ Single | ⚠️ Img+Stone | ⚠️ POST | ❌ **L1761** file |
| Assign/Reject | L932-1003 | ⚠️ Per-item | ❌ N/A | ⚠️ Low | ❌ None |
| Add to Cart | L2058-2088 | ✅ Single | ❌ N/A | ⚠️ Low | ❌ None |
| Cart Order Place | L1893-2043 | ❌ **Double TX** | ❌ N/A | ⚠️ Low | ❌ None |
| **Repair Status** | L1841-1870 | ⚠️ Per-item | ❌ N/A | ⚠️ Low | ❌ None |
| **Repair Deliver** | L2338-2366 | ⚠️ Per-item | ❌ N/A | ⚠️ Low | ❌ None |
| **Repair Sub-Details** | L2158-2224 | ✅ Single | ⚠️ Sub-items | ⚠️ **L2162** | ❌ None |
| **Send OTP** | L2238-2294 | ⚠️ Per-item | ❌ N/A | ⚠️ Low | ❌ **L2286** OTP |
| **Verify OTP** | L2295-2336 | ✅ Single | ❌ N/A | ⚠️ Low | ❌ None |
