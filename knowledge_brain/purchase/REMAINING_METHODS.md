# PURCHASE MODULE — REMAINING MODEL METHODS (Round 3 Supplement)
> **Supplement to METHOD_INDEX.md** | Methods L8053-9050

---

## Supplier Sale & Return Details (L8053-8116)

| Method | Line | Purpose | Tables |
|---|---|---|---|
| `get_supplier_sale($data)` | 8053-8077 | Get supplier sale details for purchase return flow | `ret_purchase_return_items`, `ret_purchase_return` |
| `get_supplier_bill_details($pur_return_id)` | 8078-8097 | Get bill details linked to a purchase return | `ret_purchase_return_items`, `ret_purchase_order_item`, joins to category/product/design |
| `getSupplierstoneDetails($pur_ret_itm_id)` | 8099-8108 | Get stone details for a return item | `ret_purchase_return_stone_items`, `ret_stone` |
| `getSupplierOtherMetalDetails($pur_ret_itm_id)` | 8110-8116 | Get other metal details for a return item | `ret_purchase_return_other_metal` |

## Metal Issue Receipt (L8118-8269)

| Method | Line | Purpose | Tables |
|---|---|---|---|
| `get_metal_issue_details($data)` | 8118-8133 | Get metal issue receipt details | `ret_karigar_metal_issue`, `ret_karigar_metal_issue_details` |
| `getKarigarIssueRef_No($data)` | 8135-8146 | Get karigar issue reference number for receipt | `ret_karigar_metal_issue` |
| `GetFinancialYear()` | 8148-8153 | Get current financial year (duplicate of L133) | `ret_settings` |
| `get_rate_fix_details($id)` | 8155-8206 | Get rate fix details with bill info | `pur_rat_fix_detail`, `ret_purchase_order`, `ret_purchase_order_item` |
| `get_credit_debit_detail($id)` | 8211-8250 | Get credit/debit entry details | `ret_credit_debit_entry` with joins |
| `get_issue_received_wt($issue_met_id)` | 8254-8258 | Get received weight for a metal issue | `ret_karigar_metal_issue_details` |
| `get_order_details($issue_met_id)` | 8260-8269 | Get order details for metal issue | `ret_karigar_metal_issue_details`, product/design joins |
| `get_tag_status($tag_id)` | 8271-8278 | Check tag current status | `ret_taging` |

## Opening Metal Stock (L8280-8381)

| Method | Line | Purpose | Tables |
|---|---|---|---|
| `get_opening_metal_stock_list($data)` | 8280-8381 | Get available metal opening stock list for karigar | Raw SQL — `ret_lot_inward_detail`, `ret_lot_inward`, `ret_purchase_order_item`, `ret_karigar_metal_issue_details`, complex balance query |

## Lot & HM Stone Details (L8383-8416)

| Method | Line | Purpose | Tables |
|---|---|---|---|
| `get_hm_stone_accepted_details($po_item_id)` | 8383-8416 | Get HM-accepted stone details for lot generation | `ret_halmarking_issue_details`, `ret_halmarking_stone_details`, `ret_stone` |

## Karigar Validation (L8417-8494)

| Method | Line | Purpose | Tables |
|---|---|---|---|
| `karigar_pan_available($pan_number, $id_karigar)` | 8417-8442 | Check if PAN is unique | `ret_karigar` |
| `karigar_gst_available($gst_number, $id_karigar)` | 8444-8468 | Check if GST is unique | `ret_karigar` |
| `karigar_aadhar_available($aadhar_no, $id_karigar)` | 8470-8494 | Check if Aadhar is unique | `ret_karigar` |

## Tagged Reference & PO Bills (L8496-8831)

| Method | Line | Purpose | Tables |
|---|---|---|---|
| `getTaggedRefNo()` | 8496-8536 | Get tagged reference numbers for approval | `ret_taging`, product/category joins |
| `get_po_payment_pending_bills($data)` | 8538-8546 | Get pending payment PO bills | `ret_purchase_order` |
| `getPending_payment_po_bills_for_payment($post)` | 8548-8627 | Get pending bills for payment form (complex balance calc) | `ret_purchase_order`, `ret_po_payment_detail`, `ret_credit_debit_entry`, `ret_purchase_return` |
| `getPending_payment_po_bills($post)` | 8629-8711 | Get pending PO bills list | Same as above |
| `get_pending_po_bills_after_tagging_without_pcs_with_weight($post)` | 8713-8783 | Get pending bills after tagging — credit note flow | `ret_purchase_order`, `ret_purchase_order_item`, `ret_lot_inward_detail` |
| `get_approval_po_bills($post)` | 8784-8831 | Get approval PO bills | `ret_purchase_order`, `ret_approval_stock` |

## PO Details & Utilities (L8833-9050)

| Method | Line | Purpose | Tables |
|---|---|---|---|
| `get_po_details_by_ref_no($post)` | 8833-8956 | Get full PO details by ref number — payment form | `ret_purchase_order`, `ret_purchase_order_item`, complex with `ret_po_payment`, `ret_credit_debit_entry`, `ret_purchase_return` balance calc |
| `get_gold_rate()` | 8958-8969 | Get current gold rate | `ret_settings` |
| `check_cheque_number_exist($cheque_no)` | 8971-8982 | Check cheque number uniqueness | `ret_po_payment` |
| `get_po_ratecut_balance_details($po_id)` | 8984-9033 | Get PO rate cut balance details | `ret_purchase_order`, `pur_rat_fix_detail`, joins |
| `save_email_log($data)` | 9036-9039 | Save email log entry | `ret_email_log` |
| `get_email_log_by_token($token)` | 9041-9045 | Get email log by token | `ret_email_log` |
| `update_email_log($id, $data)` | 9047-9050 | Update email log | `ret_email_log` |

---

## Summary
| Category | Methods |
|---|---|
| Supplier sale/return details | 4 |
| Metal issue receipt | 8 |
| Opening metal stock | 1 |
| HM stone details | 1 |
| Karigar validation (PAN/GST/Aadhar) | 3 |
| Tagged ref & PO bills | 6 |
| PO details & utilities | 7 |
| **Total documented in Round 3** | **30** |
| **Grand total (all rounds)** | **246/246 = 100%** |