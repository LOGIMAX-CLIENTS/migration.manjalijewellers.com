# PURCHASE MODULE — DATA FLOW
> **Module:** Purchase | **Version:** 1.0 | **Date:** 2026-02-23

---

## Flow 1: CREATE Purchase Order (Save New Order)

### 1.1 JS → Controller
1. **Button Click**: `#create_order` → `create_customer_order()` (JS L8713)
2. **Validation**: `validateOrderDetailRow()` (JS L8661) — checks category, product, weight are filled
3. **Data Collected**: `$('#order_form').serialize()` + order details array
4. **AJAX**: POST to dynamic `url` variable → `admin_ret_purchase/purchase/save`

### 1.2 Controller Processing (L710-L2239 in `purchase('save')`)
1. Get financial year: `$model->get_FinancialYear()` (L720)
2. Generate PO number: `$model->generatePurNo()` (L722)
3. Build `$order` array with: fin_year_code, pur_no, status=0, type=1, karigar, date, rate_type
4. `$this->db->trans_begin()` (L754)
5. Insert order header: `$model->insertData($order, 'customerorder')` (L756)
6. Loop through order_details: Insert each item into `customerorderdetails` (L772+)
7. Insert stone details for each item if present
8. Insert images for each item if present
9. `$this->db->trans_status()` check
10. Commit or rollback
11. Log the operation: `log_model->log_detail('insert', '', $log_data)`
12. Return JSON: `{status: true/false, message: '...'}`

### 1.3 Tables Written (in order)
| # | Table | Operation | Data |
|---|---|---|---|
| 1 | `customerorder` | INSERT | Order header |
| 2 | `customerorderdetails` | INSERT (batch) | Line items |
| 3 | `ret_customer_order_stone_details` | INSERT | Stone details per item |
| 4 | `ret_order_images` | INSERT | Reference images |

### 1.4 Response → JS
- Success: `$.toaster` success message, redirect to list
- Failure: `$.toaster` danger message

---

## Flow 2: CREATE Supplier Bill Entry (Purchase Entry)

### 2.1 JS → Controller
1. **Button Click**: Purchase entry save button
2. **Data**: Form serialization with bill details, item details, stone details, GST details
3. **AJAX**: POST to `admin_ret_purchase/purchase/purchase_entry` (JS L13496)

### 2.2 Controller Processing (inside `purchase('purchase_entry')`)
1. Check `pur_entry_secret_key` session to prevent double-submit
2. Generate bill ref number: `$model->generatePurRefOrderNo($is_suspense, $gst_bill_type)`
3. Build bill header array: po_ref_no, po_date, id_karigar, weights, amounts, GST
4. `$this->db->trans_begin()`
5. Insert bill header: `insertData($bill, 'ret_purchase_order')`
6. Loop items: `insertData($item, 'ret_purchase_order_item')`
7. For each item: Insert stone details, other metal details
8. Insert GST details: `insertData($gst, 'ret_purchase_gst_detail')`
9. Insert other charges: `insertData($charge, 'ret_purchase_other_charge_detail')`
10. Log the operation
11. Commit/rollback

### 2.3 Tables Written
| # | Table | Operation |
|---|---|---|
| 1 | `ret_purchase_order` | INSERT |
| 2 | `ret_purchase_order_item` | INSERT (batch) |
| 3 | `ret_purchase_order_stone_detail` | INSERT |
| 4 | `ret_purchase_other_metal_detail` | INSERT |
| 5 | `ret_purchase_gst_detail` | INSERT |
| 6 | `ret_purchase_other_charge_detail` | INSERT |

---

## Flow 3: QC Issue & Receipt

### 3.1 QC Issue
1. **Load form**: `qc_issue_receipt('add')` → fetches pending items via `purchase_issue()`
2. **Select PO items**: User selects items for QC
3. **Save**: `update_qc_issue()` (Controller L4055-4219)
   - Generate QC ref no: `get_qc_ref_no()`
   - Insert `ret_qc_process` header
   - Insert `ret_qc_issue_details` for each item
   - Insert stone details per QC item
   - Update PO item status

### 3.2 QC Receipt
1. **Load form**: `qc_issue_receipt('receipt')` → fetches issued items
2. **Enter results**: Pass/Fail per item, quality remarks
3. **Save**: `update_qc_status()` (Controller L2705-2877)
   - Update `ret_qc_issue_details` with QC result status
   - Update stone details with accepted/rejected quantities
   - Update PO item QC status

### 3.3 Tables
| Table | QC Issue | QC Receipt |
|---|---|---|
| `ret_qc_process` | INSERT | READ |
| `ret_qc_issue_details` | INSERT | UPDATE |
| `ret_qc_stone_details` | INSERT | UPDATE |
| `ret_purchase_order_item` | UPDATE (status) | UPDATE (status) |

---

## Flow 4: Hallmarking Issue & Receipt

### 4.1 HM Issue (`update_halmarking_issue()` L4498-4657)
1. Generate HM ref no: `generate_HalmarkingRefNo()`
2. Insert `ret_halmarking_process` header
3. Insert `ret_halmarking_issue_details` for each item
4. Insert stone details
5. Update PO item status

### 4.2 HM Receipt (`update_halmarking_receipt()` L4661-4861)
1. Update `ret_halmarking_issue_details` with receipt weights
2. Record rejection weights and reasons
3. Update hallmarking charges
4. Update PO item status to HM complete

### 4.3 Tables
| Table | HM Issue | HM Receipt |
|---|---|---|
| `ret_halmarking_process` | INSERT | READ |
| `ret_halmarking_issue_details` | INSERT | UPDATE |
| `ret_purchase_order_item` | UPDATE (status) | UPDATE (status) |

---

## Flow 5: Rate Fixing

### 5.1 Data Flow
1. **Load**: `rate_fixing('add')` → `get_rate_fixing_items()` fetches bills for rate fixing
2. **Enter rate**: User enters market metal rate
3. **Save**: Inside `rate_fixing('save')` case
   - Calculate metal value: net_wt × rate
   - Insert into `pur_rat_fix_detail`
   - Update `ret_purchase_order` with fixed rate
   - Set `rate_fix_status` = 0 (pending approval)

### 5.2 Rate Fix Approval
1. **Load**: `rate_fixing('approval_rate_fixing')` → `get_approval_rate_fix_list()`
2. **Approve/Reject**: `update_ratefix_approval()` (L6479-6551)
   - Update `pur_rat_fix_detail` approval status
   - Update `ret_purchase_order` approval status

---

## Flow 6: Supplier Payment

### 6.1 Data Flow (`supplier_po_payment('save')` L5317-6127)
1. **Load**: Get supplier details + pending bills
2. **Enter payment**: Amount, payment mode (cash/bank/metal)
3. **Save**:
   - Generate payment number: `generatePaymentNo()`
   - Insert `ret_po_payment` header
   - Insert `ret_po_payment_detail` for each bill paid
   - Update `ret_wallet_account` (supplier ledger): `updateWalletData()`
   - Update PO `grand_total_paid`: `update_po_paymentData()`
   - If cheque: print cheque via `ret_billing_model` for bank details
4. **PDF**: Generate payment acknowledgement

---

## Flow 7: Lot Generation (`generateLot()` L11093-11318 + `generate_lot()` L2883-3367)

### 7.1 Data Flow
1. Check QC/HM status of all PO items
2. For each eligible item:
   - Check if lot already exists: `checkLotItemExist()`
   - Get stone details: `get_qc_stone_accepted_details()` or `get_hm_stone_accepted_details()`
   - Insert lot header: `insertData($lot, 'ret_lot_inward')`
   - Insert lot items: `insertData($item, 'ret_lot_inward_detail')`
   - Update stock summary: `checkPurchaseItemStockExist()` → `updatePurItemData()`
   - Generate tag number if applicable
3. Insert into `ret_taging` for tag-based items

---

## Flow 8: Purchase Return (`updateporeturnitems()` L7822-8331)

### 8.1 Data Flow
1. Generate return ref no: `pur_ret_refno()`
2. Insert `ret_purchase_return` header
3. Insert `ret_purchase_return_items` for each returned item
4. Insert return stone details
5. Insert return GST details if applicable
6. Update supplier wallet: `updateWalletData()`
7. Update purchase item stock: `updatePurItemData()`
8. Generate return receipt PDF

---

## Flow 9: GRN Entry (`grnentry('save')`)

### 9.1 Data Flow
1. Generate GRN ref no: `generate_grn_refno()`
2. Insert `ret_grn_entry` header
3. Insert `ret_grn_entry_detail` for each item
4. Insert stone details per item
5. Insert GST details
6. Insert other charges
7. Insert images
8. Update PO item delivery status
9. Log the operation

---

## Flow 10: DELETE Operations

| Sub-Module | Method | Type | Cleanup |
|---|---|---|---|
| Order Description | `order_description('delete')` L338-382 | Hard DELETE | Direct delete from `ret_purchase_order_description` |
| PO Entry Cancel | `purchase('cancel_po_entry')` L579-643 | Soft DELETE | Update `bill_status=2` on `ret_purchase_order` |
| Order Close | `update_order_close()` L8399-8463 | Soft | Update `order_status=1` on `customerorder` |
| Order Cancel | `update_order_cancel()` L8465-8533 | Soft | Update `order_status=2` on `customerorder` |
| QC Issue Cancel | `qc_issue_receipt_cancel()` L13513-13556 | Soft/Hard | Cancel QC process records |
| Delete Bill Item | `delete_supplier_entry_item()` L13480-13510 | Hard DELETE | Delete from `ret_purchase_order_item` |

> **⚠️ RISK**: `delete_supplier_entry_item()` uses hard DELETE — orphan stone/metal details may remain. No cascade check visible.