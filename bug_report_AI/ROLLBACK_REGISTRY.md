# Rollback Registry

> **Central index of all fix rollback plans.** Use this during emergencies.
> Last Updated: {auto-updated by workflows}

## Quick Rollback Guide

1. Find the bug ID below
2. Follow the rollback steps exactly
3. Run the verification command to confirm rollback worked
4. Update the bug status in the execution plan back to "Open"
5. Re-open the GitHub Issue if it was closed

---

## Active Rollback Plans

| Bug ID    | Module  | File(s)                                                                                | Rollback Command                                                                                                                                                                                                                                                                                                                                           | Risk if Rolled Back                                                                  | Applied Date |
| --------- | ------- | -------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------ | ------------ |
| BIL-CLT01 | Billing | `admin/assets/js/ret_billing.js`                                                       | Remove `calculateDiscountAllocations()` and `_distributeDiscountToTier()` functions (inserted before `calculateSaleBillRowTotal`). Restore original discount block in `calculateSaleBillRowTotal` (ratio-based `disc_per = disc_amt / total_sales_amt * 100` applied to all rows with inline MC/VA waterfall). Remove `discountAllocations` pre-pass call. | Discount will revert to applying to all items by ratio, including items with MC=VA=0 | 2026-02-25   |
| BIL-CLT02 | Billing | `admin_ret_billing.php`, `ret_billing_model.php`, `billing/form.php`, `ret_billing.js` | Code: git revert 4 files — restore disc*blw_metal_rate in controller/form, remove enable*\* from model emptydata, restore old outer gate in JS. DB: `DELETE FROM ret_settings WHERE name IN ('enable_emp_disc_limit','enable_mc_va_disc_limit','enable_disc_blw_metal_rate');`                                                                             | All 3 limit checks become permanently active; no per-limit bypass option             | 2026-03-03   |
| Bug ID    | Module  | File(s)                          | Rollback Command                                                                                                                                                                                                                                                                                                                                           | Risk if Rolled Back                                                                  | Applied Date |
| --------- | ------- | -------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------ | ------------ |
| BIL-CLT01 | Billing | `admin/assets/js/ret_billing.js` | Remove `calculateDiscountAllocations()` and `_distributeDiscountToTier()` functions (inserted before `calculateSaleBillRowTotal`). Restore original discount block in `calculateSaleBillRowTotal` (ratio-based `disc_per = disc_amt / total_sales_amt * 100` applied to all rows with inline MC/VA waterfall). Remove `discountAllocations` pre-pass call. | Discount will revert to applying to all items by ratio, including items with MC=VA=0 | 2026-02-25   |
| Bug ID | Module | File(s) | Rollback Command | Risk if Rolled Back | Applied Date |
|---|---|---|---|---|---|
| PUR-INT03 | Purchase | `form.php`, `ret_purchase_order.js`, `admin_ret_purchase.php` | 1. Remove `.badge-cr`/`.badge-dr` CSS, spans, and hidden inputs from `form.php`. 2. Re-comment CR weight branch, remove `cashcrdr_*` setters in JS. 3. Remove `amount_type`/`weight_type` from controller insert array. 4. `ALTER TABLE ret_supplier_rate_cut DROP COLUMN amount_type, DROP COLUMN weight_type;` | CR/DR indicators disappear, type columns lost | 2026-02-26 |
| PUR-INT02 | Purchase | `ret_reports_model.php` L20457-20497 | Revert `get_po_payments()` to original flat JOIN: restore direct `LEFT JOIN ret_po_bill_payment_details po_bill ON po_bill.pay_id = p.pay_id` and remove the `po_agg` subquery | Report may show duplicate data again for multi-PO payments | 2026-02-26 |
| PUR-CLT02 | Purchase | `ret_general.js`, `ret_purchase_order.js` | Revert `validateOrderImages()` to use implicit `event`, revert setTimeout, remove img_resource clear on modal open | Image upload in Purchase Order Stock Order will stop working again | 2026-02-25 |
| PUR-CLT02 | Purchase | `admin/assets/js/ret_purchase_order.js` | Re-add `+ parseFloat(other_charges_amount)` to item_cost calc at L20180, L20184, L20876; remove post-tax addition lines at L20529-20531, L20860-20862 | Cascading tax returns (GST on charges+tax) | 2026-02-28 |
| BIL-INT01 | Billing  | `admin/application/models/ret_billing_model.php` | Revert `getBillData()` around L5620: Delete the `$total_returned` query and restore original `due_amount` calculation. | `due_amount` will ignore returned items in billing summary | 2026-03-04 |
| BIL-INT02 | Billing  | `ret_billing_model.php`, `ret_billing*.js` | 1. Revert `ret_billing_model.php` (L5869, L5881-5886) to use `credit_ret_amt` again. 2. Revert JS files to original calculation. | Credit Due Amount will again ignore sales returns. | 2026-03-04 |
| BIL-INT04 | Billing | `ret_billing_model.php`, `ret_reports_model.php` | Revert `AND rb.make_as_advance = 1` filters in: 1. `ret_billing_model.php`: `getBillData()` (~L5626), `getCreditBillDetails()` (~L5888). 2. `ret_reports_model.php`: `get_credit_pending_details()` (~L6894), `getcreditBill_history()` (~L2147). | Cash refunds will incorrectly reduce credit due balances again. | 2026-03-07 |
| PUR-CLT03 | Purchase | `admin/assets/js/ret_purchase_order.js` | 1. Remove `ctrl_page[1] != 'supplier_po_payment'` guard at L10461, restore original `if (data.balance_amount < 0)` block. 2. Restore synchronous balance set at end of `get_all_payment_pending_po_bills()`: add back `if (parseFloat(balance.balance_amount) >= 0) { $("#balance_amount").val(balance.balance_amount); ... }` after the AJAX call | Balance field will show total outstanding instead of selected bills total | 2026-03-03 |
| BRN-101 | Branch Transfer | `admin/application/controllers/admin_ret_brntransfer.php` | Re-add `$this->db->trans_begin();` at line 1154, inside `verify_otp()`, just before the `if ($session_otp == $post_otp)` block. Remove the 2-line BRN-101 comment. | Dangling transaction returns — no functional impact expected but trans handle left open after every OTP verification | 2026-03-11 |
| BRN-102 | Branch Transfer | `admin/application/controllers/admin_ret_brntransfer.php` | Restore original `verify_other_issue_otp()`: 1. Move `$this->db->trans_begin()` to just before the outer `foreach`. 2. Restore `$this->db->trans_commit()` at L1348 (before `updateData()`). 3. Remove `trans_status()` guard block + `$updStatus` check. 4. Remove `$this->db->trans_rollback()` calls from failure branches. | DB write will run outside transaction again; silent updateData() failures return status:true | 2026-03-11 |

| BRN-104 | Branch Transfer | `admin_ret_brntransfer.php` | `git checkout admin_ret_brntransfer.php` | Minimal logic risk. Restore file replaces `$_POST` references | 2026-03-12 |
| BRN-D01 | Branch Transfer | `ret_brntransfer_model.php` | `git checkout admin_ret_brntransfer_model.php` | Restores null array return causing crash | 2026-03-12 |
| BRN-D02 | Branch Transfer | `ret_brntransfer_model.php` | `git checkout admin_ret_brntransfer_model.php` | Restores undefined `$id_value` causing bad return type | 2026-03-12 |
| BRN-D03 | Branch Transfer | `ret_brntransfer_model.php` | `git checkout admin_ret_brntransfer_model.php` | Restores unescaped SQL conditions | 2026-03-12 |
| BRN-D09 | Branch Transfer | `ret_brntransfer_model.php:1138` | `git checkout admin_ret_brntransfer_model.php` | Restores undefined variable logic mismatch | 2026-03-12 |
| BRN-D16 | Branch Transfer | `ret_brntransfer_model.php:2238` | `git checkout admin_ret_brntransfer_model.php` | Restores ->row() crash on empty recordsets | 2026-03-12 |
| BRN-D17 | Branch Transfer | `ret_brntransfer_model.php:1906` | `git checkout admin_ret_brntransfer_model.php` | Restores ->row() crash on missing HO | 2026-03-12 |
| BRN-D10 | Branch Transfer | `ret_brntransfer_model.php:1038` | `git checkout admin/application/models/ret_brntransfer_model.php` | Restores ->row()->otp_verif_mobileno crash when branch has no OTP mobile | 2026-03-12 |
| BRN-D11 | Branch Transfer | `ret_brntransfer_model.php:1538` | `git checkout admin/application/models/ret_brntransfer_model.php` | Restores $gross_wt for 'net_wt' key in SR summary — net_wt column will again show gross weight | 2026-03-12 |

| BRN-D37 | Branch Transfer | `ret_branch_transfer.js:4039` | `git checkout admin/assets/js/ret_branch_transfer.js` | Restores hardcoded Branch 1 logic for OM | 2026-03-12 |
| BRN-D21 | Branch Transfer | `admin_ret_brntransfer.php:716,750` | `git checkout admin/application/controllers/admin_ret_brntransfer.php` | Restores wrong `$partly_sale_log` variable for non-tag SR insert | 2026-03-13 |
| TAG-CLT03 | Tagging | `admin/application/controllers/admin_ret_tagging.php` (L6711-6728, L6812-6820), `admin/application/views/tagging/retagging_form.php` (L97-105) | **Controller**: Revert `retagging()/add` — replace `get_headOffice()` block with raw `$data['ho_branch_id'] = 0`. Revert `create_retag()` — replace null-safe HO block with `$id_branch = $this->input->post('id_branch')`. **View**: Restore `value="1"` on both `id_branch` hidden inputs. | Lots created via Retagging will again be stored under Palanai branch (ID 1) — tagging progress will fail for those lots | 2026-04-18 |
| TAG-CLT02 | Tagging | `admin/assets/js/ret_tagging.js:6219` | Revert L6219 to: `if(item.is_closed==0)` — remove the `\|\| (typeof ctrl_page !== 'undefined' && ctrl_page[2] == 'duplicate_print')` condition | Closed lots will no longer appear in Duplicate Tag dropdown | 2026-03-24 |
| RPT-SD01 | Reports | `admin/application/models/ret_reports_model.php` | Remove $id_category implode block (lines added after 16708). Revert lines ~17005 and ~17228 from `p.cat_id in ($id_category)` back to `p.cat_id=$data['id_category']` | Category filter with multi-select reverts to broken 'Array' SQL | 2026-04-03 |
| RPT-CLT02 | Reports | `admin/application/models/ret_reports_model.php` L13590, L13603-13605, L13607, L13623-13624, L13641-13645, L13646-13653 | Remove `$selectedcat` extraction line; remove 3 `AND p/rp/pmd.id_pay_device = ?` conditions (one per query); remove `$result = []` initialization; restore `if(empty($data['source_type']))` | Device filter ignored again — all devices returned regardless of dropdown selection | 2026-04-29 |
| BIL-CLT07 | Billing | `admin/application/helpers/template_receipt_helper.php`, `admin/application/helpers/konva_receipt_helper.php`, `admin/application/core/MY_DB_mysqli_driver.php` | Code: `git checkout admin/application/helpers/template_receipt_helper.php admin/application/helpers/konva_receipt_helper.php admin/application/core/MY_DB_mysqli_driver.php`. DB: `DELETE FROM print_templates WHERE id_template = 480;` | Reverts chit columns layout and split benefits on invoices, and restores potential Globals class crash locally | 2026-05-27 |

---

## How to Use During Crisis

### Rollback a Single Fix

1. Find the bug ID in the table above
2. Execute the rollback steps
3. Run syntax check: `& "{PHP_PATH}" -l {file}`
4. Verify the module still works

### Rollback Multiple Fixes (Batch)

1. Identify all bugs to rollback — **rollback in REVERSE chronological order** (newest fix first)
2. For each bug (newest → oldest):
    - Execute rollback steps
    - Run syntax check
3. After all rollbacks, run full module smoke test
4. Update all GitHub Issues to re-opened

### Emergency: Rollback Everything for a Module

1. Filter table by Module
2. Rollback ALL entries in reverse order
3. This effectively returns the module to its pre-fix state

---

## How This File Gets Updated

| Event                         | Workflow                 | Action                  |
| ----------------------------- | ------------------------ | ----------------------- |
| Fix applied                   | `/fix-single-bug` Step 5 | Append rollback entry   |
| Fix verified stable (30 days) | Manual review            | Move to Archive section |
| Fix rolled back               | Emergency procedure      | Mark as "ROLLED BACK"   |
| N/A | admin | admin/application/models/payment_model.php | Revert lines 1754-1773 in get_paymentContent() to just checking allow_advance | Low | 17-04-2026 |
| N/A | admin | admin/application/models/payment_model.php | Revert lines 2110-2118 in get_paymentContent() to just due_type ND without totalunpaid or currentmonthpaycount conditions | Low | 17-04-2026 |
| N/A | admin | admin/application/models/payment_model.php | Revert lines 2034, 2078, 2092 by removing !empty() checks from maturity_date and due_cycle_end logic | Low | 17-04-2026 |
