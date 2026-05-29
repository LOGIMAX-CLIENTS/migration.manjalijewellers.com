# Knowledge Gaps Register: Customer Order

> Findings that require runtime testing or business confirmation to verify.
> These cannot be determined by code analysis alone.

---

## Runtime Verification Required

### KG-01: Does `generateOrderNo()` Actually Produce Duplicates Under Load?
- **Nature**: Can only be proven under concurrent load testing
- **Suspected**: Yes, due to SELECT-then-INSERT without row locking (BUG-CUSORD-017)
- **Test**: Run 2+ simultaneous order creation requests from same branch and order_type
- **What to look for**: Duplicate values in `customerorder.order_no`

### KG-02: Does CodeIgniter `trans_begin()` Inside foreach Actually Reset State?
- **Nature**: CodeIgniter-version-specific behaviour
- **Suspected**: In CI 3.x, calling `trans_begin()` when already in a transaction may reset or be a no-op
- **Test**: Enable DB query logging, create 3-item order update, verify `START TRANSACTION` count in logs
- **Impact**: Affects 5 functions with per-item TX pattern (BUG-CUSORD-010, 015)

### KG-03: Does the Double `trans_begin()` in `cart('order_place')` Actually Leave Orphan State?
- **Nature**: Depends on MySQL driver and auto-commit settings
- **Suspected**: Second `trans_begin()` at L1926 may cause a COMMIT of the first partial TX
- **Test**: Add `echo $this->db->last_query()` before L1926, intercept DB queries, check START TRANSACTION count
- **Impact**: BUG-CUSORD-006 may be more or less severe depending on driver

### KG-04: Is WEBP Image Upload Silently Failing or Being Caught?
- **Nature**: Requires actual browser upload test
- **Suspected**: `upload_img()` returns false for WEBP but caller ignores return value
- **Test**: Upload a `.webp` file through order image upload → check DB record and filesystem
- **Code**: `admin_ret_order.php` L1173-1179

### KG-05: Are There Existing `.txt` Debug Files in `orders_img/` Directory?
- **Nature**: Requires server filesystem access
- **Suspected**: Yes, for all repair orders ever updated
- **Test**: `ls assets/img/orders_img/*.txt | wc -l`
- **Impact**: Sensitive upload data on disk (BUG-CUSORD-004)

### KG-06: Is the WhatsApp OTP Service Actually Configured?
- **Nature**: Configuration-level — cannot determine from code alone
- **Suspected**: Service is commented out → OTP delivered silently with no notification (EC-18)
- **Test**: Check `ret_settings` for `order_cancel_otp` service config; check `admin_usersms_model` call
- **Impact**: Users may not receive OTP → cancellation blocked

---

## Business Logic Confirmation Required

### KG-07: Is Tag Reserve (Type=5) Intended to Skip Assignment?
- **Nature**: Business rule ambiguity
- **Observation**: `order('save')` type=5 sets `orderstatus=4` directly (skips 1→3→4)
- **Question**: Is this by design (no karigar needed) or a bug (should go through assignment)?
- **Impact**: BUG-CUSORD-021 classification depends on answer

### KG-08: Is `ortertype` Column Intentional Duplicate of `order_type`?
- **Nature**: Schema design intent unclear
- **Observation**: `customerorderdetails.ortertype` and `customerorder.order_type` appear redundant
- **Question**: Does `ortertype` serve a different purpose (e.g. sub-type) or is it truly duplicated?
- **Impact**: BUG-CUSORD-029 classification

### KG-09: What is the Intended OTP Length/Security Level?
- **Nature**: Business security policy
- **Observation**: `mt_rand(1001,9999)` — 4-digit OTP, only 8999 values
- **Question**: Is 4-digit sufficient, or is a stronger OTP (6-digit + expiry enforcement) required?

### KG-10: Are Multiple OTP Recipients (Comma-Separated Mobiles) Active in Production?
- **Nature**: Configuration-level
- **Observation**: `getBranchOtpRegMobile()` may return comma-separated mobiles → loop fires
- **Question**: Does any branch actually have multiple OTP recipients configured?
- **Impact**: If yes, TX-14 per-item TX bug (BUG-CUSORD related) is actively exploitable

---

## Performance Unknowns

### KG-11: Is `get_new_orderlist()` Used on Large Datasets?
- **Nature**: Requires production data volume context
- **Observation**: L470-500 is a 15-table LEFT JOIN query without LIMIT or pagination
- **Concern**: With thousands of orders, this query may be very slow
- **Test**: `EXPLAIN` the query with prod data; check if index on `od.orderstatus`, `o.order_type` exists

### KG-12: Does `getCustomerOrderDetails()` Cause N+1 Queries?
- **Nature**: Requires slow-query-log analysis
- **Observation**: L400 calls `get_order_images($items['id_orderdetails'])` inside a foreach loop → one DB query per order item
- **Impact**: Listing page with 50 orders × 5 items = 250+ image queries on one page load

### KG-13: Is `SHOW COLUMNS FROM $table` Called on Every Insert/Update?
- **Nature**: Performance profiling required
- **Observation**: `insertData()` and `updateData()` both execute `SHOW COLUMNS FROM $table` before every operation (L13, L49)
- **Impact**: Every order save adds 2× `SHOW COLUMNS` calls; not cached
- **Test**: Enable slow query log, check `SHOW COLUMNS` frequency

---

## Data Integrity Unknowns

### KG-14: Are There Orphan Rows in Child Tables from Pre-Cascade-Fix Orders?
- **Nature**: Requires DB query on production
- **Diagnostic**: Run SCHEMA_ANALYSIS.md orphan check queries #5 and #6
- **Expected**: Yes, if orders were ever deleted without cascade

### KG-15: Do Any Orders Have the Double-Image-String Bug (L470)?
- **Nature**: Requires DB query
- **Diagnostic**: `SELECT id_orderdetails, image FROM customerorderdetails WHERE image LIKE '%jpg%jpg%' OR image LIKE '%png%png%'`
- **Expected**: Any orders updated after double-image bug was introduced will have doubled paths

---

## FLOW_RISK_MATRIX Gaps — Runtime Confirmation Required (R17)

### KG-16: Are There Existing Spurious `ret_issue_receipt` Rows from AP-11?
- **Nature**: Requires DB query (now possible with SCHEMA_ANALYSIS #10)
- **Concern**: Every order cancel since post-R14 code change may have inserted a zero-amount receipt
- **Diagnostic**: Run query #10 in SCHEMA_ANALYSIS.md
- **Impact**: Financial reports may show phantom advance-return receipts

### KG-17: Are There Orphan Tag References After Order Deletes?
- **Nature**: Requires DB query
- **Concern**: `order('delete')` does NOT clear `ret_taging.id_orderdetails` — tags still point to deleted rows
- **Diagnostic**: Run query #11 in SCHEMA_ANALYSIS.md
- **Impact**: Tags may appear unavailable and block new orders/billings for those tag codes

### KG-18: Does Repair Deliver (`orderstatus=5`) Skip Status=4 in Production?
- **Nature**: Requires production data query
- **Concern**: `repair_deliver_order_status()` sets status=5 with no pre-check that status=4
- **Diagnostic**: `SELECT id_orderdetails, orderstatus FROM customerorderdetails WHERE orderstatus=5 AND id_orderdetails NOT IN (SELECT id_orderdetails FROM customerorderdetails WHERE orderstatus=4)`
- **Expected**: Any rows found confirm the state machine gap is actively exploited

### KG-19: Are Any Tags Still Claimed by Cancelled Orders (Cancel-Then-Tag Gap)?
- **Nature**: Requires DB query
- **Concern**: `cancel_order_item` clears `ret_taging.id_orderdetails` only if `get_tagorder_details()` returns a match — but `get_tagorder_details()` may not match if the order detail ID changed
- **Diagnostic**: `SELECT t.tag_id, t.tag_code, od.orderstatus FROM ret_taging t JOIN customerorderdetails od ON od.id_orderdetails = t.id_orderdetails WHERE od.orderstatus = 6`
- **Impact**: Tags stuck in "reserved" state even after cancel — cannot be billed

---

## Summary

| Category | Count |
|---|---|
| Runtime Verification | 6 |
| Business Logic | 4 |
| Performance | 3 |
| Data Integrity | 2 |
| FLOW_RISK Gaps (R17) | 4 |
| **Total** | **19** |
