# Purchase Module — Fix Report

> **Module**: Purchase | **Last Updated**: 2026-02-26

---

### Fix: PUR-INT02 — Duplicate Total Value Display in Supplier Payment Reports and Supplier Ledger (Multiple PO)
- **Date:** 2026-02-26
- **Track:** A (System)
- **Category:** Database / Query Logic
- **Severity:** P1
- **Files Changed:** `admin/application/models/ret_reports_model.php` (L20459)
- **Root Cause:** `get_po_payments()` used `d.payment_amount` (full payment mode total from `ret_po_payment_detail`) which duplicated when one payment covered N POs via `ret_po_bill_payment_details` JOIN.
- **Fix Applied:** Replaced `d.payment_amount` with `IFNULL(po_bill.bill_amount, d.payment_amount)` to use per-PO allocated amount, with fallback for advance payments without PO allocation.
- **Tests:** PASS — 4 tests, 8 assertions (`PurchaseQueryLogicTest.php`)
- **Pattern:** PAT-QRY-001 matched (Cartesian JOIN — column-level variant)
- **Rollback:** Replace `IFNULL(po_bill.bill_amount, d.payment_amount)` back to `d.payment_amount` in SELECT clause
> Tracking all applied bug fixes for the Purchase module.

---

### Fix: PUR-CLT02 — Unable to Add Image in Purchase Order (Stock Order)
- **Date:** 2026-02-25
- **Track:** A (System)
- **Category:** Integration
- **Severity:** P2
- **Files Changed:** `ret_general.js` (lines 516-651), `ret_purchase_order.js` (lines 43947-44016), `order_form.php` (lines 148-149)
- **Root Cause:** `validateOrderImages()` used implicit global `event` object (Chrome-only), fragile 3s setTimeout for async compression, `img_resource` global not cleared between modal opens, no visual feedback after save
- **Fix Applied:** Direct DOM file access (`$('#order_images_new')[0].files`), Promise.all for reliable async timing, clear stale data on modal open, thumbnail preview + count badge near Add Image button
- **Tests:** Syntax pass, browser test pass (modal preview, save thumbnail, button count)
- **Pattern:** NEW pattern added (PAT-INT-001)
- **Rollback:** Revert `validateOrderImages()` to implicit `event`, revert Promise.all→setTimeout, remove preview code
> **Last Updated**: 2026-02-25

---

### Fix: PUR-UI01 — Product Name "Undefined" & Column Alignment in PO Form
- **Date:** 2026-02-25
- **Track:** A (System)
- **Category:** UI/Data Display
- **Severity:** P2
- **Files Changed:**
  - `admin/application/models/ret_purchase_order_model.php` (lines 544, 573–580)
  - `admin/assets/js/ret_purchase_order.js` (lines 6551, 6555)
- **Root Cause:** `get_customer_order_details()` selected `p.product_name` without IFNULL wrapper on a LEFT JOIN — NULL when no product match. `get_images()` called `file_get_contents()` on missing files, generating PHP warnings that corrupt JSON. Dynamically inserted `tag_code` `<td>` cells were not hidden after the radio handler ran.
- **Fix Applied:** Added `IFNULL(p.product_name,'')`, `file_exists()` guard in `get_images()`, inline `display:none` for `tag_code` cells when `order_for==2`, and `|| ''` JS fallback for `product_name`.
- **Tests:** PHP syntax check PASS. Manual verification pending.
- **Pattern:** PAT-QRY-004 matched (Missing IFNULL on LEFT JOIN), PAT-INT-001 NEW (Unchecked file_get_contents)
- **Rollback:** Revert IFNULL wrapper and file_exists guard; revert JS tag_code style and product_name fallback.

> **Purpose**: Sequential log of all applied bug fixes in the Purchase module.

---
### Fix: PUR-INT01 — Duplicate po_ref_no / grn_ref_no Due to Race Condition
- **Date:** 2026-02-25
- **Track:** A (System)
- **Category:** 8 — Concurrency
- **Severity:** P1
- **Files Changed:** `admin/application/models/ret_purchase_order_model.php`, `admin/application/models/ret_purchase_approval_model.php`, `admin/application/controllers/admin_ret_purchase.php`
- **Root Cause:** Read-then-increment logic (TOCTOU) for reference numbers without database locking, causing concurrent requests to generate duplicates. Also, mismatched prefix generation due to raw input usage versus hardcoded database flags.
- **Fix Applied:** Moved reference number generation inside database transactions. Applied `SELECT ... FOR UPDATE` locking to prevent dirty reads. Consolidated duplicated model methods. Rectified the `is_suspense_stock` check for "Against Order". Added JS double-submit prevention. Fixed data for existing duplicate (`grn_id=31`).
- **Tests:** PASS
- **Pattern:** NEW pattern added (PAT-CON-001)
- **Rollback:** Revert controller transaction block positions and model generation methods.
---

### Fix: PUR-INT03 — Missing CR/DR Indicator on Pure Balance & Amount Balance + DB Storage
- **Date:** 2026-02-27
- **Track:** A (System)
- **Category:** 7 — Integration/UI
- **Severity:** P2
- **Files Changed:** `admin/application/views/ret_purchase/amt_weight_conversation/form.php`, `admin/assets/js/ret_purchase_order.js`, `admin/application/controllers/admin_ret_purchase.php`
- **Root Cause:** Balance values displayed as raw numbers without CR/DR context. No mechanism to persist balance nature (credit/debit) to the database for downstream reporting.
- **Fix Applied:** Added CSS-styled `(Cr)`/`(Dr)` badges next to Pure Balance and Amount Balance labels. Added hidden inputs (`supplier_rate_cut[weight_type]`, `supplier_rate_cut[amount_type]`) populated via JS (1=CR for negative, 2=DR for positive). Added `amount_type` and `weight_type` to the controller insert array. ALTER TABLE adds columns to `ret_supplier_rate_cut`.
- **Tests:** PASS — 3 tests, 7 assertions (no regression). Browser smoke 8/8 ✅
- **Pattern:** NEW pattern added (PAT-INT-001)
- **Rollback:** Remove CSS classes and spans from `form.php`, revert JS balance logic, remove columns from controller `$data` array. See `ROLLBACK_REGISTRY.md`.
### Fix: PUR-CLT02 — Tax Calculation Mismatch Between GRN and Supplier Bill Entry (Hallmark Charges)
- **Date:** 2026-02-28
- **Track:** B (Business)
- **Category:** 1 — Logic
- **Severity:** P0
- **Files Changed:** `admin/assets/js/ret_purchase_order.js`
- **Root Cause:** In `calculate_purchase_item_cost()`, `other_charges_amount` (which includes charge tax, e.g. hallmark ₹300 + 18% = ₹354) was added to the `item_cost` base before product GST calculation, causing cascading tax (GST on a value that already includes another tax).
- **Fix Applied:** Removed `other_charges_amount` from pre-tax `item_cost` at 3 locations (lines 20180, 20184, 20876). Added `other_charges_amount` back after GST computation at 2 locations (lines 20531, 20862). Applied to both Supplier Bill (grn_type==1) and Job Work Receipt (grn_type==2) paths.
- **Tests:** PASS — 7 new tests (PurchaseLogicTest.php), 26 assertions; 10 existing tests, 17 assertions — all pass, no regression
- **Pattern:** NEW pattern added (PAT-CALC-001)
- **Rollback:** Re-add `+ parseFloat(other_charges_amount)` to item_cost at L20180/20184/20876; remove post-tax lines at L20529-31/20860-62
---

### Fix: PUR-CLT03 — Incorrect Pure Weight Field Labels & Position in Approval to Invoice Conversion
- **Date:** 2026-03-04
- **Track:** A (System)
- **Category:** UI/Label Mismatch
- **Severity:** P1
- **Files Changed:** `admin/application/views/ret_purchase/amt_weight_conversation/form.php`
- **Root Cause:** View labels did not match the data being displayed — "Pure Balance" was showing where "Bill Pure WT" should be, and "Outstanding Pure Wt(Grms)" was in the wrong position. The right-panel outstanding section needed to be relocated to the left panel.
- **Fix Applied:** Renamed labels to "Bill Amt(Rs)" and "Bill Pure Wt(Grms)", moved them to left panel below Select Metal. Retained "Pure Balance"/"Amount Balance" in original position. Removed right-panel outstanding section. Added h4 styling for consistent display.
- **Tests:** Syntax check PASS. View-only change — no automated tests applicable.
- **Pattern:** NEW pattern added (PAT-UI-001 — UI Label Mismatch)
- **Rollback:** Revert `form.php` to restore original label text and positions.
### Fix: PUR-CLT03 — Bill-wise Payment Selected but Total Outstanding Balance Shown
- **Date:** 2026-03-03
- **Track:** A (System)
- **Category:** Logic
- **Severity:** P1
- **Files Changed:** `admin/assets/js/ret_purchase_order.js` (L10457-10481, L82914-82922, L82917)
- **Root Cause:** In `get_supplier_pay_details()`, the `else` branch unconditionally set `#balance_amount` to total outstanding (L10461-10462). In `get_all_payment_pending_po_bills()`, synchronous code at L82916-82917 also set `#balance_amount` to total outstanding before the AJAX callback could run `calculateSelectedTotal()`. Due to async timing, the correct selected-bill total was overwritten back to total outstanding.
- **Fix Applied:** (1) Added `ctrl_page[1] != 'supplier_po_payment'` guard at L10461 so balance is only set from total outstanding for non-payment pages. (2) Removed synchronous total outstanding set at end of `get_all_payment_pending_po_bills()`. (3) Added `calculatePaymentCost()` inside AJAX success callback.
- **Tests:** PASS — 7 existing tests, 26 assertions (no regression)
- **Pattern:** NEW pattern added (PAT-UI-001)
- **Rollback:** Remove ctrl_page guard at L10461; restore synchronous balance set after AJAX in `get_all_payment_pending_po_bills()`. See `ROLLBACK_REGISTRY.md`.
---

### Fix: PUR-CR04 — Add Print Button for Duplicate Cheque Copy in Supplier Payment Listing
- **Date:** 2026-03-11
- **Track:** A (System)
- **Category:** UI/Integration
- **Severity:** P2
- **Files Changed:** `admin/application/models/ret_purchase_order_model.php` (L1576), `admin/application/models/ret_purchase_approval_model.php` (L1139), `admin/assets/js/ret_purchase_order.js` (L34170, L34253), `admin/assets/js/ret_purchase_approval.js` (L6313, L6343)
- **Root Cause:** No UI option to reprint cheque from the listing page. Cheque print was only triggered on initial payment save. No `has_cheque` flag in listing AJAX response to differentiate cheque vs non-cheque payments.
- **Fix Applied:** Added `has_cheque` correlated subquery (`SELECT COUNT(*) FROM ret_po_payment_detail WHERE type=5`) to both listing model methods. Added conditional green Cheque Print button (visible only when `has_cheque > 0`). Payment Copy button now triggers `printPaymentCopy()` which opens both Payment Ack + Cheque PDF for cheque entries (dual-print).
- **Tests:** PASS — PHP syntax 2/2 files. Browser test deferred to manual verification.
- **Pattern:** N/A (Novel — new feature, not a bug pattern)
- **Rollback:** Remove `has_cheque` subquery from both models. Revert JS action column to use simple `<a href>` for Payment Copy. Remove `printPaymentCopy()` function from both JS files.
---
### Fix: PUR-CLT01 — Purchase dashboard Today PO Pending not showing
- **Date:** 2026-03-20
- **Track:** A (System)
- **Category:** 1 — Logic / Database
- **Severity:** P1
- **Files Changed:** `admin/application/models/ret_dashboard_api_model.php`
- **Root Cause:** The query in `get_today_delivery_po_payments` had a restrictive `pur_approval_type = 1` filter and joined with `ret_category` without NULL category handling, which excluded valid POs due today from being displayed.
- **Fix Applied:** Removed the `pur_approval_type = 1` filter from the SQL WHERE clause to ensure all approved and billed POs due for delivery today are included in the dashboard list.
- **Tests:** Manual verification — record `PO-TEST-005` in `retail_dev2` confirmed to match the updated query.
- **Pattern:** PAT-LOGIC-003 NEW (Overly Restrictive Filter)
- **Rollback:** Re-add `and po.pur_approval_type = 1` to the `WHERE` clause in `get_today_delivery_po_payments`.
---
### Fix: PUR-CLT04 — getPending_payment_po_bills_for_payment Returns Empty (No Pending Bills on Payment Page)
- **Date:** 2026-03-21
- **Track:** A (System)
- **Category:** Logic / Variable
- **Severity:** P1
- **Files Changed:** `admin/application/controllers/admin_ret_purchase.php` (L13618-13632)
- **Root Cause:** Controller function `getPending_payment_po_bills_for_payment()` was missing `$model = self::RET_PUR_ORDER_MODEL;` initialization and `$list = $this->$model->getPending_payment_po_bills_for_payment($_POST);` call. The model function existed (L8543) but was never invoked. Copy-paste oversight from neighboring function.
- **Fix Applied:** Added 2 missing lines: model initialization and model method call to populate `$list`.
- **Tests:** Manual verification — controller now returns pending bills JSON.
- **Pattern:** PAT-VAR-002 matched (Undefined Variable Assignment)
- **Rollback:** Remove the 2 added lines from the controller function.
---
### Fix: PUR-CLT05 — Karigar Metal Issue Touch/Weight Column Misalignment & UOM Auto-Select
- **Date:** 2026-03-27
- **Track:** A (System)
- **Category:** UI / JS Row Builder
- **Severity:** P1
- **Files Changed:** `admin/assets/js/ret_purchase_order.js` (L69434, L69378-69405)
- **Root Cause:** Two issues in `getAvailableTagDetails()` tag flow row builder: (1) Missing `<td>` for Touch input column — PO flow had it but tag flow didn't, causing PCS/Weight/PureWT/Action to shift left by one column. (2) UOM dropdown not auto-selected when tag data's `item.uom` was null/undefined — left at placeholder `-UOM-`, causing `validateMetalIssueRow()` to fail validation and block Save.
- **Fix Applied:** (1) Added Touch `<td>` with `karigar_touch` input between Purity and PCS columns. (2) Added UOM auto-select fallback — when no `item.uom` match found, first available UOM option is auto-selected.
- **Tests:** Manual verification — columns align correctly, Save no longer shows validation errors.
- **Pattern:** PAT-UI-002 NEW (Missing Column in JS Dynamic Row Builder)
- **Rollback:** Remove the added Touch `<td>` at L69434; revert UOM builder to original loop without auto-select at L69378-69405.
---
