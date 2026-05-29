# PURCHASE MODULE — METHOD INDEX
> **Module:** Purchase | **Version:** 2.0 | **Date:** 2026-03-24 | **Round:** 4

---

## 7a. Controller Methods (Alphabetical)

| # | Method | Lines | Purpose | JS Caller |
|---|---|---|---|---|
| 1 | `approval_rate_fixed($type)` | L12244-12286 | Approval rate fixed list/form | - |
| 2 | `approval_unfixing_report($type)` | L12194-12236 | Unfixing report list | - |
| 3 | `approvalstock($type)` | L2243-2309 | Approval stock list/add | - |
| 4 | `approvalstock_tag($type)` | L10675-10723 | Approval tag list/add | - |
| 5 | `base64ToFile($imgBase64)` | L202-228 | Convert base64 to file | Internal |
| 6 | `check_cheque_number_exist()` | L83-116 | AJAX: Validate cheque number | check_cheque_number_exist() |
| 7 | `convert_to_normal_stock()` | L10939-11079 | Convert approval→normal | JS convert handler |
| 8 | `CrDr_noteinv($id)` | L12466-12496 | Print CR/DR note PDF | - |
| 9 | `credit_debit_entry($type,$id,$status)` | L13003-13237 | CR/DR entry CRUD | JS CR/DR handler |
| 10 | `delete_supplier_entry_item()` | L13480-13510 | AJAX: Delete bill item | delete_supplier_entry_item() |
| 11 | `generate_lot($po_id)` | L2883-3367 | Generate lot from PO | Internal |
| 12 | `generate_lot_from_halmarking($hm_process_id)` | L3373-3703 | Generate lot from HM | Internal |
| 13 | `generateLot()` | L11093-11318 | AJAX: Lot generation | generateLot() |
| 14 | `get_ActiveWeightRange()` | L2657-2667 | AJAX: Weight ranges | get_ActiveWeightRange() |
| 15 | `get_approvl_ratefix_po()` | L6569-6579 | AJAX: Approval ratefix PO | JS handler |
| 16 | `get_approval_rate_fixing_po_no()` | L11410-11420 | AJAX: Approval rate fix PO | JS handler |
| 17 | `get_Available_SupplierPo()` | L12154-12164 | AJAX: Available supplier POs | get_Available_SupplierPo() |
| 18 | `get_bill_details()` | L6944-6956 | AJAX: Bill details | get_bill_details() |
| 19 | `get_customer_order_pending_details()` | L498-508 | AJAX: Pending cust orders | get_customer_order_pending_details() |
| 20 | `get_img_by_grn_id()` | L13240-13250 | AJAX: GRN images | JS handler |
| 21 | `get_karigar_acknowladgement($id)` | L2557-2633 | Karigar ack PDF | Internal |
| 22 | `get_karigar_details()` | L9392-9400 | AJAX: Karigar details | get_karigar_details() |
| 23 | `get_karigar_pending_order_details()` | L8559-8569 | AJAX: Pending order details | get_karigar_pending_order_details() |
| 24 | `get_karigar_pending_orders()` | L8545-8555 | AJAX: Pending orders | get_karigar_pending_orders() |
| 25 | `get_karigar_pending_ordes()` | L9378-9388 | AJAX: Pending orders (alt) | JS handler |
| 26 | `get_karigar_SupplierEntrys()` | L12142-12152 | AJAX: Supplier entries | get_karigar_SupplierEntrys() |
| 27 | `get_KarigarOrders()` | L2641-2651 | AJAX: Karigar orders | get_KarigarOrders() |
| 28 | `get_lot_nontag_details()` | L12957-12962 | AJAX: Non-tag lot details | JS handler |
| 29 | `get_OrderProducts()` | L9404-9414 | AJAX: Order products | get_OrderProducts() |
| 30 | `get_OrderProductsDesign()` | L9418-9428 | AJAX: Product designs | get_OrderProductsDesign() |
| 31 | `get_OrderSubDesigns()` | L9432-9442 | AJAX: Sub-designs | get_OrderSubDesigns() |
| 32 | `get_pending_po_bills_after_tagging_without_pcs_with_weight()` | L13640-13646 | AJAX: Pending PO bills (weight) | JS handler |
| 33 | `get_po_balance()` | L3722-3727 | AJAX: PO balance | get_po_balance() |
| 34 | `get_po_balance_details()` | L3730-3735 | AJAX: PO balance details | JS handler |
| 35 | `get_po_ratecut_balance_details()` | L3737-3743 | AJAX: Rate cut balance | JS handler |
| 36 | `get_pur_order_Details()` | L2673-2683 | AJAX: PO details | get_pur_order_Details() |
| 37 | `get_purchase_issue_entry_items()` | L2689-2699 | AJAX: Issue entry items | JS QC handler |
| 38 | `get_qc_faild_items_by_poid()` | L7800-7808 | AJAX: QC failed items by PO | JS handler |
| 39 | `get_qc_faild_items_by_supid()` | L7810-7818 | AJAX: QC failed by supplier | JS handler |
| 40 | `get_qc_status_details()` | L3709-3719 | AJAX: QC status details | JS handler |
| 41 | `get_rate_fixing_po_no()` | L6555-6565 | AJAX: Rate fix PO numbers | JS handler |
| 42 | `get_stock_repair_order_details()` | L514-524 | AJAX: Repair order details | get_stock_repair_order_details() |
| 43 | `get_supplier_sale()` | L6962-6966 | AJAX: Supplier sale data | JS handler |
| 44 | `get_tds_percent()` | L12981-13000 | AJAX: TDS percentage | JS handler |
| 45 | `getKarigarIssueRefNo()` | L12166-12176 | AJAX: Issue ref number | getKarigarIssueRefNo() |
| 46 | `getKarigarMetalIssueLooseStones()` | L12178-12188 | AJAX: Loose stones | JS handler |
| 47 | `getNonTagLotItemDetails()` | L12963-12968 | AJAX: Non-tag lot items | JS handler |
| 48 | `getOrderNosBySearch()` | L6930-6938 | AJAX: Search order numbers | JS handler |
| 48b | `get_crdr_debit_entries_for_payment()` | L13633-13638 | AJAX: CR/DR debit entries (no PO) for payment screen | JS handler |
| 49 | `getPending_payment_po_bills()` | L13609-13615 | AJAX: Pending payment bills report list | JS handler |
| 50 | `getPending_payment_po_bills_for_payment()` | L13617-13631 | AJAX: Pending bills for payment + CR/DR credits merge | JS handler |
| 51 | `getpurchase_po_list()` | L12132-12140 | AJAX: Purchase PO list | JS handler |
| 52 | `getreturn_po_list()` | L7790-7798 | AJAX: Return PO list | JS handler |
| 53 | `grnentry($type,$id)` | L9450-10565 | GRN entry CRUD | JS GRN handler |
| 54 | `grn_invoice($id)` | L10567-10595 | GRN invoice PDF | - |
| 55 | `halmarking_issue_receipt($type)` | L4401-4492 | HM issue/receipt list/add | JS HM handler |
| 56 | `headoffice_valut_report($type)` | L11846-12008 | HO vault report | - |
| 57 | `imgTobase64($path)` | L230-242 | Convert image to base64 | Internal |
| 58 | `index()` | L77-81 | Default (empty) | - |
| 59 | `isEmptySetDefault($value,$default)` | L12971-12979 | Utility: empty check | Internal |
| 60 | `karigar_aadhar_available()` | L13578-13586 | AJAX: Aadhar validation | JS handler |
| 61 | `karigar_gst_available()` | L13568-13576 | AJAX: GST validation | JS handler |
| 62 | `karigar_pan_available()` | L13558-13566 | AJAX: PAN validation | JS handler |
| 63 | `karigarmetalissue($type)` | L8761-9372 | Metal issue CRUD | JS metal issue handler |
| 64 | `karigarmetalissue_acknowladgement($id)` | L8725-8757 | Metal issue ack PDF | - |
| 65 | `lot_and_tag_wise_report($type)` | L11800-11842 | Lot/tag report | - |
| 66 | `lot_generate($type)` | L3749-3845 | Lot generation list/add | JS lot handler |
| 67 | `metal_issue_receipt($type)` | L13274-13477 | Metal issue receipt CRUD | JS handler |
| 68 | `nontag_lot_generate($type,$id,$status)` | L12500-12780 | Non-tag lot CRUD | JS handler |
| 69 | `nontag_receipt($type,$id,$status)` | L12784-12956 | Non-tag receipt CRUD | JS handler |
| 70 | `opening_balance_ratefixing()` | L11424-11434 | AJAX: Opening balance ratefix | JS handler |
| 71 | `order_description($type,$id)` | L248-486 | Order desc CRUD | JS handler |
| 72 | `order_place()` | L10727-10933 | AJAX: Place order | order_place() |
| 73 | `purchase($type,$id)` | L528-2239 | **MEGA METHOD** Purchase CRUD | Multiple JS handlers |
| 74 | `purchase_payment($type)` | L6143-6469 | Purchase payment CRUD | JS payment handler |
| 75 | `purchasereturn($type)` | L7525-7788 | Purchase return CRUD | JS return handler |
| 76 | `qc_issue_receipt($type,$id)` | L3855-4051 | QC issue/receipt CRUD | JS QC handler |
| 77 | `qc_issue_receipt_cancel()` | L13513-13556 | AJAX: Cancel QC | JS handler |
| 78 | `rate_fixing($type,$id)` | L6585-6924 | Rate fixing CRUD | JS rate handler |
| 79 | `retagging_report($type)` | L13252-13272 | Retagging report | - |
| 80 | `return_receipt_acknowladgement($id,$Print_Type)` | L8335-8389 | Return receipt PDF | - |
| 81 | `returnpoitems()` | L6969-7521 | AJAX: Process return items | returnpoitems() |
| 82 | `send_karigar_sms()` | L2315-2553 | Send SMS + PDF to karigar | send_karigar_sms() |
| 83 | `set_image($id,$img_path,$file)` | L122-138 | Upload image handler | Internal |
| 84 | `smith_cmpy_op_bal($type,$id,$status)` | L12292-12462 | Smith opening balance CRUD | JS handler |
| 85 | `supplier_po_payment($type,$id)` | L5317-6127 | Supplier payment CRUD | JS payment handler |
| 86 | `supplier_rate_cut($type,$id,$status)` | L11438-11796 | Rate cut CRUD | JS handler |
| 87 | `update_halmarking_issue()` | L4498-4657 | AJAX: Save HM issue | JS HM handler |
| 88 | `update_halmarking_receipt()` | L4661-4861 | AJAX: Save HM receipt | JS HM handler |
| 89 | `update_karigar()` | L10607-10663 | AJAX: Update karigar | JS handler |
| 90 | `update_order_cancel()` | L8465-8533 | AJAX: Cancel order | update_order_cancel() |
| 91 | `update_order_close()` | L8399-8463 | AJAX: Close order | update_order_close() |
| 92 | `update_order_delivery()` | L8573-8713 | AJAX: Update delivery | update_order_delivery() |
| 93 | `update_po_approval()` | L11328-11402 | AJAX: PO approval | update_po_approval() |
| 94 | `update_qc_issue()` | L4055-4219 | AJAX: Save QC issue | JS QC handler |
| 95 | `update_qc_status()` | L2705-2877 | AJAX: Update QC status | JS QC handler |
| 96 | `update_ratefix_approval()` | L6479-6551 | AJAX: Rate fix approval | JS handler |
| 97 | `updateporeturnitems()` | L7822-8331 | AJAX: Update return items | updateporeturnitems() |
| 98 | `upload_img($outputImage,$dst,$img)` | L140-198 | Image upload utility | Internal |
| 99 | `weight_gain_loss($type)` | L12010-12128 | Weight gain/loss report | - |
| 100 | `qc_issue_update()` | L4221-4385 | AJAX: QC issue update | JS handler |
| 101 | `get_karigar_pending_orders()` | L8545-8555 | Pending orders list | get_karigar_pending_orders() |
| 102 | `getreturn_po_list()` | L7790-7798 | Return PO list | JS handler |
| 103 | `get_qc_faild_items_by_poid()` | L7800-7808 | QC failed items by PO | JS handler |
| 104 | `get_qc_faild_items_by_supid()` | L7810-7818 | QC failed items by supplier | JS handler |
| 105 | `getPending_payment_po_bills()` | L13609-13615 | **[R4-NEW]** AJAX: Pending payment PO bills report | getPending_payment_po_bills() JS handler |
| 106 | `get_crdr_debit_entries_for_payment()` | L13633-13638 | **[R4-NEW]** AJAX: CR/DR debit entries (no PO) for payment right-side panel | JS payment handler |

> **⚠️ MEGA METHOD:** `purchase($type,$id)` spans L528-L2239 (1,711 lines) and handles: list, pur_order, add, purchase_add, order_status, qc_status, order_delivery, save, purchase_entry, cancel_po_entry — via switch($type).

---

## 7b. Model Methods (Alphabetical)

| # | Method | Lines | Key Tables | Called By |
|---|---|---|---|---|
| 1 | `ajax_getGrnentrydetails($from,$to)` | L2833-2851 | `ret_grn_entry`, `ret_karigar` | `grnentry()` |
| 2 | `check_cheque_number_exist($cheque_no)` | L8971-8982 | `ret_po_payment` | `check_cheque_number_exist()` |
| 3 | `check_purchase_halmarking_details($po_id)` | L1330-1351 | `ret_halmarking_issue_details` | `generate_lot()` |
| 4 | `checkLotItemExist($data)` | L3262-3273 | `ret_lot_inward_detail` | `generate_lot()` |
| 5 | `checkNonTagItemExist($data)` | L2241-2260 | `ret_non_tag_lot_detail` | `karigarmetalissue()` |
| 6 | `checkPurchaseItemStockExist($data)` | L2281-2296 | `ret_purchase_item_stock_summary` | Multiple |
| 7 | `code_number_generator()` | L5084-5099 | `ret_taging` | `nontag_lot_generate()` |
| 8 | `deleteData($id_field,$id_value,$table)` | L127-132 | `{any}` | Multiple |
| 9 | `generate_approval_refno()` | L213-234 | `ret_purchase_order` | `approvalstock()` |
| 10 | `generate_grn_refno($grn_type)` | L2806-2832 | `ret_grn_entry` | `grnentry()` |
| 11 | `generate_HalmarkingRefNo()` | L992-1016 | `ret_halmarking_process` | `halmarking_issue_receipt()` |
| 12 | `generate_opblc_refno()` | L4644-4664 | `ret_smith_cmpy_op_bal` | `smith_cmpy_op_bal()` |
| 13 | `generatePaymentNo()` | L163-186 | `ret_po_payment` | `supplier_po_payment()` |
| 14 | `generatePurNo()` | L138-162 | `customerorder` | `purchase('save')` |
| 15 | `generatePurRefOrderNo($is_suspense,$gst_bill_type)` | L187-212 | `ret_purchase_order` | `purchase('purchase_entry')` |
| 16 | `get_ActiveWeightRange($data)` | L235-242 | `ret_weight_range` | `get_ActiveWeightRange()` |
| 17 | `get_approval_conversion_details($id)` | L3360-3387 | `ret_supplier_rate_cut` | `supplier_rate_cut()` |
| 18 | `get_approval_po_bills($post)` | L8784-8831 | Multiple PO tables | `getPending_payment_po_bills()` |
| 19 | `get_approval_purchase_entry_details()` | L669-684 | `ret_purchase_order`, `ret_karigar` | `approvalstock()` |
| 20 | `get_approval_rate_fix_list($from,$to)` | L1558-1570 | `pur_rat_fix_detail` | `rate_fixing()` |
| 21 | `get_approval_rate_fixed_details($data)` | L4545-4577 | `pur_rat_fix_detail` | `approval_rate_fixed()` |
| 22 | `get_approval_rate_fixing_po_no($data)` | L3303-3359 | `ret_purchase_order`, `pur_rat_fix_detail` | `get_approval_rate_fixing_po_no()` |
| 23 | `get_approval_rate_unfixed_details($data)` | L4430-4544 | `pur_rat_fix_detail`, `ret_purchase_order` | `approval_unfixing_report()` |
| 24 | `get_approval_stock_tags($data)` | L3175-3195 | `ret_taging`, `ret_approval_stock` | `approvalstock_tag()` |
| 25 | `get_approval_tag_details($tag_id)` | L3196-3200 | `ret_taging` | `approvalstock_tag()` |
| 26 | `get_available_stock_details($data)` | L2141-2182 | `ret_purchase_item_stock_summary` | `karigarmetalissue()` |
| 27 | `get_bill_details($SearchTxt,$bill_cus_id)` | L1621-1629 | `ret_purchase_order` | `get_bill_details()` |
| 28 | `get_credit_debit($id)` | L5073-5083 | `ret_credit_debit_entry` | `credit_debit_entry()` |
| 29 | `get_credit_debit_detail($id)` | L8211-8250 | `ret_credit_debit_entry` | `CrDr_noteinv()` |
| 30 | `get_creditdebit_entry($data)` | L5041-5072 | `ret_credit_debit_entry`, `ret_karigar` | `credit_debit_entry()` |
| 31 | `get_crdr_detail($rate_fix_id)` | L4597-4643 | `ret_credit_debit_entry` | `smith_cmpy_op_bal()` |
| 32 | `get_customer_order_details($id)` | L555-578 | `customerorder`, `customerorderdetails` | `purchase()` |
| 33 | `get_customer_order_item_details($id)` | L600-625 | `customerorderdetails` | `purchase()` |
| 34 | `get_customer_order_pending_details($data)` | L478-501 | `customerorder`, `customerorderdetails` | Controller |
| 35 | `get_customer_order_stone_details($id)` | L308-318 | `ret_customer_order_stone_details` | `purchase()` |
| 36 | `get_empty_record()` | L2780-2805 | None | `purchase('add')` |
| 37 | `get_FinancialYear()` | L133-137 | `ret_settings` | Multiple |
| 38 | `get_gold_rate()` | L8958-8969 | `ret_settings` or rate table | `purchase('add')` |
| 39 | `get_grn_charge_details($grn_id)` | L2924-2932 | `ret_grn_other_charge` | `grnentry()` |
| 40 | `get_grn_details($id)` | L2857-2884 | `ret_grn_entry`, `ret_karigar` | `grnentry()` |
| 41 | `get_grn_gst_details($grn_id)` | L2914-2923 | `ret_grn_gst_detail` | `grnentry()` |
| 42 | `get_grn_images($grn_id)` | L2852-2856 | `ret_grn_images` | `get_img_by_grn_id()` |
| 43 | `get_grn_item_details($grn_id)` | L2885-2903 | `ret_grn_entry_detail` | `grnentry()` |
| 44 | `get_grn_stone_details($grn_item_id)` | L2904-2913 | `ret_grn_stone_detail` | `grnentry()` |
| 45 | `get_halmarking_details()` | L1109-1135 | `ret_halmarking_process` | `halmarking_issue_receipt()` |
| 46 | `get_halmarking_issue_order_details($hm_id)` | L1153-1185 | `ret_halmarking_issue_details` | Controller |
| 47 | `get_halmarking_issue_orders()` | L1136-1152 | `ret_halmarking_process` | Controller |
| 48 | `get_halmarking_items($po_id)` | L1038-1087 | `ret_purchase_order_item` | Controller |
| 49 | `get_halmarking_purchase_order($hm_id)` | L1259-1268 | `ret_halmarking_process`, `ret_purchase_order` | `generate_lot_from_halmarking()` |
| 50 | `get_headOffice()` | L1399-1403 | `ret_settings` or branches | Multiple |
| 51 | `get_hm_issue_stone_details($hm_receipt_id)` | L1187-1198 | HM stone details | Controller |
| 52 | `get_hm_stone_accepted_details($po_item)` | L8383-8416 | HM stone details | `generate_lot_from_halmarking()` |
| 53 | `get_images($id)` | L579-599 | Order images | `purchase()` |
| 54 | `get_issue_received_wt($issue_met_id)` | L8254-8258 | `ret_karigar_metal_issue_detail` | `metal_issue_receipt()` |
| 55 | `get_karigar_details($id_karigar)` | L434-447 | `ret_karigar` | Multiple |
| 56 | `get_karigar_order_details($id)` | L270-307 | `customerorder`, `customerorderdetails` | `purchase()` |
| 57 | `get_karigar_pending_order_details($data)` | L2054-2076 | `customerorder`, `customerorderdetails` | Controller |
| 58 | `get_karigar_pending_orders($data)` | L2041-2053 | `customerorder` | Controller |
| 59 | `get_karigar_wise_tds_percent($id,$fy)` | L4823-4841 | TDS config | `get_tds_percent()` |
| 60 | `get_KarigarMetal_issue($met_issue_id)` | L2266-2273 | `ret_karigar_metal_issue` | Controller |
| 61 | `get_KarigarOrders($data)` | L421-433 | `customerorder` | `get_KarigarOrders()` |
| 62 | `get_last_code_no()` | L5100-5108 | `ret_taging` | `code_number_generator()` |
| 63 | `get_lot_nontag_details($data)` | L4683-4699 | `ret_non_tag_lot` | Controller |
| 64 | `get_metal_issue_details($data)` | L8118-8133 | `ret_karigar_metal_issue` | `metal_issue_receipt()` |
| 65 | `get_metal_issue_purity($purity)` | L2192-2196 | `ret_purity` | `karigarmetalissue()` |
| 66 | `get_metal_issue_ref_no()` | L2091-2117 | `ret_karigar_metal_issue` | `karigarmetalissue()` |
| 67 | `get_NontagLots()` | L4665-4682 | `ret_non_tag_lot` | `nontag_lot_generate()` |
| 68 | `get_nontag_receiptedList($data)` | L4789-4803 | `ret_non_tag_receipt` | `nontag_receipt()` |
| 69 | `get_order_description()` | L1985-1991 | `ret_purchase_order_description` | Controller |
| 70 | `get_order_details($issue_met_id)` | L8260-8269 | `ret_karigar_metal_issue` | `metal_issue_receipt()` |
| 71 | `get_order_images($id)` | L319-326 | Order images table | `purchase()` |
| 72 | `get_orderdescription($id)` | L1992-1996 | `ret_purchase_order_description` | Controller |
| 73 | `get_pending_halmarking_items()` | L1017-1037 | `ret_purchase_order_item` | Controller |
| 74 | `get_pending_po_bills_after_tagging_without_pcs_with_weight($post)` | L8713-8783 | Multiple PO/tagging tables | Controller |
| 75 | `get_po_balance()` | L4842-4929 | `ret_purchase_order`, payments | Controller |
| 76 | `get_po_balance_details($data)` | L4930-5040 | `ret_purchase_order`, payments, returns | Controller |
| 77 | `get_po_details($po_id)` | L3279-3283 | `ret_purchase_order` | `generateLot()` |
| 78 | `get_po_details_by_ref_no($post)` | L8833-8956 | `ret_purchase_order` + items | Multiple |
| 79 | `get_po_item_details($po_id)` | L3284-3288 | `ret_purchase_order_item` | `generateLot()` |
| 80 | `get_po_payment($id)` | L3137-3164 | `ret_po_payment` | `supplier_po_payment()` |
| 81 | `get_po_paymentDetails($pay_id)` | L3165-3174 | `ret_po_payment_detail` | `supplier_po_payment()` |
| 82 | `get_po_payment_pending_bills($data)` | L8538-8546 | PO bills | Controller |
| 83 | `get_po_ratecut_balance_details($po_id)` | L8984-9033 | Rate cut tables | Controller |
| 84 | `get_profile_settings($id_profile)` | L2118-2122 | `ret_settings` | `karigarmetalissue()` |
| 85 | `get_pur_order_det($id)` | L2480-2484 | `customerorder` | Controller |
| 86 | `get_pur_order_details($data)` | L448-477 | `customerorder`, `customerorderdetails` | Controller |
| 87 | `get_pur_order_item_details($po_item_id)` | L1373-1393 | `ret_purchase_order_item` | `generate_lot()` |
| 88 | `get_purchase_by_category($po_id)` | L1249-1258 | `ret_purchase_order_item` | `generate_lot()` |
| 89 | `get_purchase_cus_order_details($id)` | L2412-2444 | `customerorderdetails` | Controller |
| 90 | `get_purchase_entry_details($from,$to)` | L626-668 | `ret_purchase_order`, `ret_karigar` | `purchase('list')` |
| 91 | `get_purchase_issue_entry_items($data)` | L716-758 | `ret_purchase_order_item` | Controller |
| 92 | `get_purchase_item_details($po_id,$fy)` | L892-923 | `ret_purchase_order_item` | `qc_issue_receipt()` |
| 93 | `get_purchase_order($po_id)` | L1240-1248 | `ret_purchase_order` | `generate_lot()` |
| 94 | `get_purchase_order_Details($data)` | L243-269 | `customerorder`, `customerorderdetails` | Controller |
| 95 | `get_purchase_order_hm_details($hm_id)` | L1411-1421 | HM details | Controller |
| 96 | `get_purchase_order_item_det($po_id)` | L2716-2758 | `ret_purchase_order_item` + joins | Controller |
| 97 | `get_purchase_order_item_search($po_item_id)` | L1352-1372 | `ret_purchase_order_item` | Controller |
| 98 | `get_purchase_order_items($po_id,$id_product)` | L1312-1329 | `ret_purchase_order_item` | Controller |
| 99 | `get_purchase_order_pending_details($data)` | L2308-2362 | `ret_purchase_order` + returns | Controller |
| 100 | `get_purchase_order_qc_details($po_id)` | L1200-1239 | QC + purchase tables | Controller |
| 101 | `get_purchase_order_status($data)` | L3109-3136 | `ret_purchase_order` | Controller |
| 102 | `get_purchase_qc_details($data)` | L950-977 | QC tables | Controller |
| 103 | `get_purchase_ret_charge_details($ret_id)` | L1939-1946 | Return charge tables | Controller |
| 104 | `get_purchase_ret_charge_gst_details($ret_id)` | L1947-1956 | Return GST tables | Controller |
| 105 | `get_purchaseItemStones($poId)` | L1766-1774 | Stone detail tables | Controller |
| 106 | `get_purchaseReturn_item_details($ret_id)` | L3201-3232 | Return item tables | Controller |
| 107 | `get_purity_details($id_purity)` | L2274-2280 | `ret_purity` | Multiple |
| 108 | `get_PurchasePaymentList($from,$to)` | L1571-1580 | Payment tables | Controller |
| 109 | `get_PurchaseSupplierPaymentList($from,$to)` | L1581-1594 | Payment tables | Controller |
| 110 | `get_qc_issue_details()` | L808-817 | QC tables | Controller |
| 111 | `get_qc_other_metal_details($po_item_id)` | L941-949 | QC other metal | Controller |
| 112 | `get_qc_receipt_items($data)` | L685-703 | QC receipt tables | Controller |
| 113 | `get_qc_ref_no()` | L787-807 | `ret_qc_process` | Controller |
| 114 | `get_qc_stone_accepted_details($po_item)` | L3233-3261 | QC stone tables | `generate_lot()` |
| 115 | `get_qc_stone_details($po_item_id)` | L924-940 | QC stone tables | Controller |
| 116 | `get_qc_stone_rejected_details($po_item,$type)` | L1742-1765 | QC stone tables | Controller |
| 117 | `get_rate_fix_details($id)` | L8155-8206 | `pur_rat_fix_detail` + joins | Controller |
| 118 | `get_rate_fixing_items($data)` | L1600-1620 | Rate fixing tables | Controller |
| 119 | `get_rate_fixing_po_no($data)` | L3066-3108 | `ret_purchase_order` + rate fix | Controller |
| 120 | `get_ret_settings($settings)` | L1394-1398 | `ret_settings` | Multiple |
| 121 | `get_retagging_details($data)` | L5109-8052 | **MEGA** ~3000 lines, many tables | Controller |
| 122 | `get_return_bill_details($bill_detail_id)` | L2297-2301 | Return tables | Controller |
| 123 | `get_return_non_tag_details($ret_id)` | L1852-1856 | Non-tag return tables | Controller |
| 124 | `get_return_tag_details($ret_id)` | L1857-1861 | Tag return tables | Controller |
| 125 | `get_retWallet_details($id_karigar)` | L1630-1639 | `ret_wallet_account` | Controller |
| 126 | `get_smith_cmpy_op_bal_details($data)` | L4578-4596 | Opening balance tables | Controller |
| 127 | `get_status_details()` | L769-786 | Status tables | Controller |
| 128 | `get_stock_repair_order($data)` | L502-526 | Repair order tables | Controller |
| 129 | `get_stock_repair_order_details($id,$data)` | L527-554 | Repair order detail tables | Controller |
| 130 | `get_supplier_advance_details($data)` | L1640-1644 | Advance detail tables | Controller |
| 131 | `get_supplier_bill_details($ret_id)` | L8078-8097 | Supplier bill tables | Controller |
| 132 | `get_supplier_pay_details($data)` | L1422-1463 | Payment + PO tables | Controller |
| 133 | `get_supplier_sale($data)` | L8053-8077 | Sale tables | Controller |
| 134 | `get_tag_cus_order_details($id)` | L2363-2398 | Tagging + order tables | Controller |
| 135 | `get_tag_details($tag_id)` | L1980-1984 | `ret_taging` | Controller |
| 136 | `get_tag_status($tag_id)` | L8271-8278 | `ret_taging` | Controller |
| 137 | `get_weight_gain_loss_report($data)` | L4171-4267 | Weight gain/loss tables | Controller |
| 138 | `getActiveGRNsDetails($data)` | L2943-2974 | `ret_grn_entry` | Controller |
| 139 | `getAvailableOrders($SearchTxt,$supplierId)` | L1595-1599 | `customerorder` | Controller |
| 140 | `getBalanceQcIssueDetails($po_item,$qc_id)` | L4095-4112 | QC balance tables | Controller |
| 141 | `getCatPurity($id_cat)` | L2183-2191 | Category purity tables | Controller |
| 142 | `getCompletedNonTag($lot_id,$id_product)` | L4757-4764 | Non-tag completed tables | Controller |
| 143 | `GetFinancialYear()` | L8148-8153 | Financial year tables | Controller |
| 144 | `getGRNCategoryStonedetails($grncatId)` | L3021-3030 | GRN stone tables | Controller |
| 145 | `getGRNOtherchargeDetails($grnId)` | L3042-3051 | GRN charge tables | Controller |
| 146 | `getGRNOthermetalDetails($grncatId)` | L3031-3041 | GRN other metal tables | Controller |
| 147 | `getGRNsCatDetailsbyGRNId($postdata)` | L2975-3020 | GRN category tables | Controller |
| 148 | `getKarigarIssueRef_No($data)` | L8135-8146 | Metal issue tables | Controller |
| 149 | `getKarigarIssueRefNo($data)` | L4393-4401 | Metal issue ref table | Controller |
| 150 | `getKarigarMetalIssueList()` | L2123-2140 | `ret_karigar_metal_issue` | Controller |
| 151 | `getKarigarMetalIssueLooseStones($data)` | L4402-4429 | Loose stone tables | Controller |
| 152 | `getLotNotTag($lot_id,$id_product)` | L4745-4756 | Non-tag lot tables | Controller |
| 153 | `getLottedNotTagWt($lot,$product,$design,$sub)` | L4804-4814 | Lotted non-tag weight | Controller |
| 154 | `getMetalIssue($id)` | L2197-2216 | `ret_karigar_metal_issue` | Controller |
| 155 | `getMetalIssueDetails($id)` | L2217-2240 | Metal issue detail tables | Controller |
| 156 | `getNonTagLotItemDetails($data)` | L4700-4744 | Non-tag lot item tables | Controller |
| 157 | `getNonTagReceiptNum()` | L4765-4788 | Non-tag receipt tables | Controller |
| 158 | `get_po_purchase_items($po_id,$id_cat)` | L1290-1311 | PO items | `generate_lot()` |
| 159 | `get_purchase_orders_by_product($po_id,$cat,$pur,$stock)` | L1276-1289 | PO items | `generate_lot()` |
| 160 | `getPending_payment_po_bills($post)` | L8629-8711 | Pending PO bills | Controller |
| 161 | `getPending_payment_po_bills_for_payment($post)` | L8548-8627 | Pending bills for payment | Controller |
| 162 | `getPurchase_return_StoneDetails($ret_itm_id)` | L1918-1929 | Return stone tables | Controller |
| 163 | `getPurchaseOrderDet($po_id)` | L2640-2667 | `ret_purchase_order` + karigar | `purchase('purchase_add')` |
| 164 | `getPurchaseOrderItemDet($po_id)` | L2668-2707 | `ret_purchase_order_item` + joins | Controller |
| 165 | `getPurchaseOtherChargeDetails($po_item_id)` | L2708-2715 | Other charge tables | Controller |
| 166 | `getPurchaseOtherMetalDetails($po_item_id)` | L2770-2779 | Other metal tables | Controller |
| 167 | `getPurchasePos($data)` | L1847-1851 | PO tables | Controller |
| 168 | `getPurchaseStoneDetails($po_item_id)` | L2759-2769 | Stone detail tables | Controller |
| 169 | `get_purOrders($ref_no)` | L1269-1275 | `customerorder` | Controller |
| 170 | `getqc_item_details($qc_id)` | L978-991 | QC item tables | Controller |
| 171 | `getqcAcceptedStoneDetails($qc_id)` | L1089-1108 | QC accepted stones | Controller |
| 172 | `get_qcReceiptDetails($data)` | L4113-4158 | QC receipt tables | Controller |
| 173 | `get_qcReceiptStoneDetails($qc_id)` | L4159-4170 | QC receipt stone tables | Controller |
| 174 | `getReceiptedNontagWt($lot,$product,$design,$sub)` | L4815-4822 | Receipted non-tag | Controller |
| 175 | `getRejectedItemsByPoId($poid,$type,$fy)` | L1685-1741 | Rejected items tables | Controller |
| 176 | `getRejectedItemsBySupId($supid)` | L1784-1812 | Rejected items by supplier | Controller |
| 177 | `getRejectedPos($data)` | L1652-1684 | Rejected POs | Controller |
| 178 | `getReturnedRequestList($data)` | L1813-1846 | Return request tables | Controller |
| 179 | `getReturnReceipt($id)` | L1862-1882 | Return receipt tables | Controller |
| 180 | `getReturnReceiptDetails($id)` | L1883-1917 | Return receipt detail tables | Controller |
| 181 | `getReturnReceiptGSTDetails($id)` | L1930-1938 | Return GST tables | Controller |
| 182 | `getSupplierstoneDetails($ret_itm_id)` | L8099-8108 | Supplier stone tables | Controller |
| 183 | `getSupplierOtherMetalDetails($ret_itm_id)` | L8110-8116 | Supplier other metal | Controller |
| 184 | `getTaggedRefNo()` | L8496-8536 | `ret_taging` | Controller |
| 185 | `insertBatchData($data,$table)` | L85-100 | `{any}` | Multiple |
| 186 | `insertData($data,$table)` | L11-45 | `{any}` | Multiple |
| 187 | `item_rate_fixing_details($po_item_id)` | L1529-1541 | Rate fix detail tables | Controller |
| 188 | `karigar_aadhar_available($aadhar,$id)` | L8470-8494 | `ret_karigar` | Controller |
| 189 | `karigar_gst_available($gst,$id)` | L8444-8468 | `ret_karigar` | Controller |
| 190 | `karigar_pan_available($pan,$id)` | L8417-8442 | `ret_karigar` | Controller |
| 191 | `opening_balance_ratefixing($data)` | L3289-3302 | Opening balance tables | Controller |
| 192 | `pourchase_entry_stone_details($po_item_id)` | L759-768 | Stone detail tables | Controller |
| 193 | `pur_ret_refno($PurType)` | L1957-1974 | `ret_purchase_return` | `purchasereturn()` |
| 194 | `purchase_issue($fin_year)` | L818-867 | QC issue tables | Controller |
| 195 | `purchase_payment_details($data)` | L1464-1528 | Payment detail tables | Controller |
| 196 | `purchase_receipt_orders()` | L868-891 | QC receipt tables | Controller |
| 197 | `save_email_log($data)` | L9036-9039 | Email log table | Controller |
| 198 | `send_email(...)` | L1997-2040 | None (sends email) | Controller |
| 199 | `update_lot_data($data,$arith)` | L3274-3278 | Lot tables | `generate_lot()` |
| 200 | `update_order_delivery($data,$arith)` | L2084-2090 | `customerorder` | Controller |
| 201 | `update_partial_order_delivery($data,$arith)` | L2077-2083 | `customerorder` | Controller |
| 202 | `update_po_paymentData($data,$arith)` | L1404-1410 | PO payment tables | Controller |
| 203 | `updateBatchData($data,$table,$id_field,$id_value)` | L101-112 | `{any}` | Multiple |
| 204 | `updateData($data,$id_field,$id_value,$table)` | L47-83 | `{any}` | Multiple |
| 205 | `updateMultipleWhereData($data,$where,$table)` | L113-126 | `{any}` | Multiple |
| 206 | `updateNTData($data,$arith)` | L2261-2265 | Non-tag tables | Controller |
| 207 | `updatePurItemData($id,$data,$arith)` | L2302-2307 | `ret_purchase_item_stock_summary` | Controller |
| 208 | `updateWalletData($data,$arith)` | L1645-1651 | `ret_wallet_account` | Controller |
| 209 | `get_karigar_order_product_details(...)` | L373-392 | Order product details | Controller |
| 210 | `get_karigar_order_detail_images(...)` | L393-406 | Order images | Controller |
| 211 | `get_customer_order_detail_images(...)` | L407-420 | Customer order images | Controller |
| 212 | `get_karigar_order_products($id)` | L327-372 | Order products | Controller |
| 213 | `get_karigar_SupplierEntrys($data)` | L4268-4319 | Supplier entry tables | Controller |
| 214 | `get_Available_SupplierPo($data)` | L4320-4392 | Available supplier PO | Controller |
| 215 | `get_max_pur_ret_refno($PurType)` | L1975-1979 | Return ref tables | Internal |
| 216 | `get_approvl_ratefix_po($data)` | L3052-3065 | Rate fix approval PO | Controller |
| 217 | `get_PO_Ratefix_List($from,$to)` | L1542-1557 | Rate fix list tables | Controller |
| 218 | `get_opening_metal_stock_list($data)` | L8280-8381 | Metal stock tables | Controller |
| 219 | `get_email_log_by_token($token)` | L9036-9039 | Email log table | Controller |
| 220 | `update_email_log($id,$data)` | L9042-9045 | Email log table | Controller |
| 221 | `get_crdr_credit_entries_without_po($post)` | L9047-9069 | **[R4-NEW]** `ret_crdr_note`, `ret_karigar` | `getPending_payment_po_bills_for_payment()` |
| 222 | `get_crdr_debit_entries_without_po($post)` | L9073-9091 | **[R4-NEW]** `ret_crdr_note`, `ret_karigar` | `get_crdr_debit_entries_for_payment()` |

> **⚠️ MEGA METHOD:** `get_retagging_details($data)` spans L5109-L8052 (~2,943 lines) — single model method.

---

## 7c. JS → Controller AJAX Map

> **⚠️ R4 DRIFT NOTE:** JS file grew from ~25K to 83,372 lines. Total AJAX `url:` calls = 187 (was 84 internal + 20 cross-module = 104 in R3). The 83 additional calls are from newly added JS sub-modules (payment screen, supplier ledger, CR/DR management). Full re-scan deferred to R5 deep-dive. New R4 additions documented below.

### Internal Endpoints (same controller: `admin_ret_purchase`)

| JS Line | JS Function | AJAX URL | Controller Method |
|---|---|---|---|
| L5715 | `get_customer_order_pending_details()` | `admin_ret_purchase/get_customer_order_pending_details` | `get_customer_order_pending_details()` |
| L5883 | `get_stock_repair_order_details()` | `admin_ret_purchase/get_stock_repair_order_details` | `get_stock_repair_order_details()` |
| L8265 | `get_ActiveWeightRange()` | `admin_ret_purchase/get_ActiveWeightRange` | `get_ActiveWeightRange()` |
| L8805 | `create_customer_order()` | Dynamic URL | `purchase/save` |
| L8921 | `get_purchase_order_list()` | `admin_ret_purchase/purchase/purchase_order` | `purchase('purchase_order')` |
| L9249 | `send_karigar_sms()` | `admin_ret_purchase/send_karigar_sms` | `send_karigar_sms()` |
| L10009 | `get_karigar_pending_orders()` | `admin_ret_purchase/get_karigar_pending_order_details` | `get_karigar_pending_order_details()` |
| L10129 | `get_karigar_SupplierEntrys()` | `admin_ret_purchase/get_po_balance` | `get_po_balance()` |
| L10321 | `get_supplier_pay_details()` | `admin_ret_purchase/supplier_po_payment/get_supplier_pay_details` | `supplier_po_payment('get_supplier_pay_details')` |
| L10634 | `get_payhistory_by_supid()` | `admin_ret_purchase/supplier_po_payment/paymenthistory` | `supplier_po_payment('paymenthistory')` |
| L11519 | `get_KarigarOrders()` | `admin_ret_purchase/get_KarigarOrders` | `get_KarigarOrders()` |
| L11951 | `get_pur_order_Details()` | `admin_ret_purchase/get_pur_order_Details` | `get_pur_order_Details()` |
| L12578 | `update_order_close()` | `admin_ret_purchase/update_order_close` | `update_order_close()` |
| L12772 | `update_order_cancel()` | `admin_ret_purchase/update_order_cancel` | `update_order_cancel()` |
| L12928 | `get_karigar_pending_order_details()` | `admin_ret_purchase/get_karigar_pending_order_details` | `get_karigar_pending_order_details()` |
| L13212 | `update_order_delivery()` | `admin_ret_purchase/update_order_delivery` | `update_order_delivery()` |
| L13408 | `update_po_approval()` | `admin_ret_purchase/update_po_approval` | `update_po_approval()` |
| L13496 | JS purchase entry handler | `admin_ret_purchase/purchase/purchase_entry` | `purchase('purchase_entry')` |
| L14135 | JS cancel handler | `admin_ret_purchase/purchase/cancel_po_entry` | `purchase('cancel_po_entry')` |
| L14187 | JS approval handler | `admin_ret_purchase/approvalstock/ajax_purchase_list` | `approvalstock()` |
| L15422 | `get_OrderProducts()` | `admin_ret_purchase/get_OrderProducts` | `get_OrderProducts()` |
| L15542 | JS design handler | `admin_ret_purchase/get_OrderProductsDesign` | `get_OrderProductsDesign()` |
| L16747 | JS sub-design handler | `admin_ret_purchase/get_OrderSubDesigns` | `get_OrderSubDesigns()` |
| L18282 | delete entry item | `admin_ret_purchase/delete_supplier_entry_item` | `delete_supplier_entry_item()` |
| L21252 | JS issue items | `admin_ret_purchase/get_purchase_issue_entry_items` | `get_purchase_issue_entry_items()` |
| L22400 | JS QC handler | `admin_ret_purchase/update_qc_status` | `update_qc_status()` |
| ~L60000+ | JS payment handler | `admin_ret_purchase/getPending_payment_po_bills` | `getPending_payment_po_bills()` |
| ~L60000+ | JS payment handler | `admin_ret_purchase/getPending_payment_po_bills_for_payment` | `getPending_payment_po_bills_for_payment()` |
| ~L60000+ | JS payment handler | `admin_ret_purchase/get_crdr_debit_entries_for_payment` | `get_crdr_debit_entries_for_payment()` |

### Cross-Module Endpoints (calls to OTHER controllers)

| JS Line | AJAX URL | Target Controller | Purpose |
|---|---|---|---|
| L5073 | `admin_ret_catalog/get_karigar_wise_wastage` | Catalog | Karigar wastage config |
| L5117 | `admin_ret_catalog/get_karigar_wise_stones` | Catalog | Karigar stone rates |
| L5157 | `admin_ret_catalog/get_karigar_wise_charges` | Catalog | Karigar charges |
| L5309 | `admin_ret_catalog/category/active_category` | Catalog | Active categories dropdown |
| L5349 | `admin_ret_catalog/karigar/active_list` | Catalog | Active karigars dropdown |
| L6771 | `admin_ret_order/get_img_by_order_id` | Orders | Order images |
| L8089 | `admin_ret_catalog/get_ActiveProducts` | Catalog | Active products dropdown |
| L8409 | `admin_ret_tagging/get_ActiveSize` | Tagging | Size dropdown |
| L8489 | `admin_ret_catalog/get_active_design_products` | Catalog | Design products |
| L8605 | `admin_ret_catalog/get_ActiveSubDesigns` | Catalog | Sub-designs |
| L14412 | `admin_ret_catalog/active_metals` | Catalog | Active metals |
| L14700 | `admin_ret_catalog/get_ActiveProducts` | Catalog | Products (metal issue) |
| L14764 | `admin_ret_catalog/get_MetalCategory` | Catalog | Metal categories |
| L15192 | `admin_ret_catalog/charges/getActiveChargesList` | Catalog | Active charges |
| L16381 | `admin_ret_catalog/get_active_design_products` | Catalog | Design products |
| L16863 | `admin_ret_catalog/get_ActiveSubDesigns` | Catalog | Sub-designs |
| L16999 | `admin_ret_catalog/category/cat_purity` | Catalog | Category purity |
| L22520 | `admin_ret_catalog/ajax_getPurity` | Catalog | Purity data |
| L24798 | `admin_ret_tagging/getStoneItems` | Tagging | Stone items |
| L24846 | `admin_ret_tagging/getStoneTypes` | Tagging | Stone types |

## 7d. Dashboard API Model Methods (Purchase Related)

| # | Method | Lines | Key Tables | Called By |
|---|---|---|---|---|
| 1 | `get_delayed_purchase_orders($from,$to,$br,$met)` | L1449-1489 | `customerorder`, `customerorderdetails`, `ret_karigar` | JS L18234 |
| 2 | `get_delayed_po_payments($from,$to,$br,$met)` | L1491-1598 | `ret_purchase_order`, `ret_po_payment`, `ret_crdr_note` | JS L18135 |
| 3 | `get_today_delivery_po_payments($from,$to,$br,$met)` | L1602-1686 | `ret_purchase_order`, `ret_po_payment`, `ret_crdr_note` | JS L18193 |
| 4 | `get_vendor_payment($from,$to,$id_branch)` | L1688-1708 | `ret_po_payment_detail`, `ret_karigar` | JS L17381 |
| 5 | `get_outward_details($from,$to,$br,$met)` | L1711-1813 | `ret_purchase_return`, `ret_karigar_metal_issue` | JS L17543 |
| 6 | `get_crdr_details($from,$to,$id_branch)` | L1887-1910 | `ret_crdr_note`, `ret_karigar` | JS L17969 |
| 7 | `get_qc_details($from,$to,$br,$met)` | L1912-1936 | `ret_purchase_order_item`, `ret_po_qc_issue_details` | JS L18031 |

---

## 7e. Table → Methods Reverse Map

| Table | Read By (model methods) | Written By (model methods) |
|---|---|---|
| `customerorder` | `get_purchase_order_Details`, `get_karigar_order_details`, `get_KarigarOrders`, `get_karigar_pending_orders`, `generatePurNo` | `insertData`, `update_order_delivery`, `update_partial_order_delivery` |
| `customerorderdetails` | `get_customer_order_details`, `get_customer_order_item_details`, `get_purchase_cus_order_details` | `insertData` |
| `ret_purchase_order` | `get_purchase_entry_details`, `getPurchaseOrderDet`, `get_po_balance`, `get_po_balance_details`, `generatePurRefOrderNo`, `get_rate_fixing_po_no`, `get_bill_details` | `insertData`, `updateData` |
| `ret_purchase_order_item` | `getPurchaseOrderItemDet`, `get_purchase_item_details`, `get_purchase_issue_entry_items`, `get_halmarking_items`, `get_pending_halmarking_items` | `insertData`, `updateData` |
| `ret_qc_process` | `get_qc_ref_no`, `get_qc_issue_details`, `get_purchase_qc_details` | `insertData` |
| `ret_qc_issue_details` | `get_qc_receipt_items`, `getBalanceQcIssueDetails` | `insertData`, `updateData` |
| `ret_halmarking_process` | `get_halmarking_details`, `get_halmarking_issue_orders`, `generate_HalmarkingRefNo` | `insertData` |
| `ret_po_payment` | `get_po_payment`, `check_cheque_number_exist`, `generatePaymentNo` | `insertData` |
| ↳ *Cross-module* | `ret_reports_model.get_po_payments` (reads `is_verified`, `pay_id`) | `ret_reports_model.verify_po_payment` (sets `is_verified=1`) |
| `ret_po_payment_detail` | `get_po_paymentDetails`, `purchase_payment_details` | `insertData` |
| `ret_purchase_return` | `getReturnReceipt`, `getReturnedRequestList`, `pur_ret_refno` | `insertData` |
| `ret_purchase_return_items` | `getReturnReceiptDetails`, `get_purchaseReturn_item_details` | `insertData` |
| `ret_karigar` | `get_karigar_details`, `karigar_pan_available`, `karigar_gst_available`, `karigar_aadhar_available` | `updateData` |
| `ret_karigar_metal_issue` | `getKarigarMetalIssueList`, `getMetalIssue`, `get_KarigarMetal_issue`, `get_metal_issue_ref_no` | `insertData` |
| `ret_lot_inward` | `checkLotItemExist` | `insertData` |
| `ret_lot_inward_detail` | `checkLotItemExist` | `insertData` |
| `ret_wallet_account` | `get_retWallet_details` | `updateWalletData` |
| `ret_taging` | `get_tag_details`, `get_tag_status`, `getTaggedRefNo`, `get_approval_stock_tags` | `insertData`, `updateData` |
| `ret_settings` | `get_FinancialYear`, `get_ret_settings`, `get_gold_rate` | Read-only |
| `ret_purchase_item_stock_summary` | `checkPurchaseItemStockExist`, `get_available_stock_details` | `updatePurItemData` |
| `ret_grn_entry` | `ajax_getGrnentrydetails`, `get_grn_details`, `getActiveGRNsDetails` | `insertData` |
| `ret_credit_debit_entry` | `get_creditdebit_entry`, `get_credit_debit`, `get_credit_debit_detail` | `insertData`, `updateData` |
| `ret_crdr_note` (no-PO entries) | `get_crdr_credit_entries_without_po` (CR type=1), `get_crdr_debit_entries_without_po` (DR type=2) | `insertData` (via `credit_debit_entry('save')`) |
| `ret_non_tag_lot` | `get_NontagLots`, `get_lot_nontag_details` | `insertData` |
| `pur_rat_fix_detail` | `get_PO_Ratefix_List`, `get_approval_rate_fix_list`, `get_rate_fix_details`, `item_rate_fixing_details` | `insertData`, `updateData` |
| `ret_smith_cmpy_op_bal` | `get_smith_cmpy_op_bal_details`, `generate_opblc_refno` | `insertData` |

---

## 7e. Approval Controller Methods — `admin_ret_purchase_approval.php`
> **[R5-NEW]** Controller: `admin/application/controllers/admin_ret_purchase_approval.php` (4,538 lines)
> Uses model: `ret_purchase_approval_model` | Same session gate as main controller.

| # | Method | Lines | Purpose | Notes |
|---|---|---|---|---|
| 1 | `__construct()` | L17-71 | Auth gate, loads approval model + settings | Same pattern as main ctrl |
| 2 | `index()` | L75-79 | Default (empty) | — |
| 3 | `set_image($id,$img_path,$file)` | L85-101 | Upload image handler | Utility |
| 4 | `upload_img($outputImage,$dst,$img)` | L103-161 | Image resize + save | Utility |
| 5 | `base64ToFile($imgBase64)` | L165-191 | Convert base64 to file | Utility |
| 6 | `imgTobase64($path)` | L193-205 | Convert image to base64 | Utility |
| 7 | `get_customer_order_pending_details()` | L213-223 | AJAX: Pending customer orders | Same as main ctrl |
| 8 | `get_stock_repair_order_details()` | L229-239 | AJAX: Repair order details | Same as main ctrl |
| 9 | `purchase($type,$id)` | L243-1686 | **MEGA METHOD** Purchase CRUD for approval flow | switch($type): list, pur_order, add, purchase_add, order_status, qc_status, order_delivery, save, po_entry_save, cancel_po_entry |
| 10 | `approvalstock($type)` | L1687-2681 | Approval stock list/management | Multi-case switch |
| 11 | `send_karigar_sms()` | L2682-2923 | Send SMS + PDF to karigar | Same as main ctrl |
| 12 | `get_karigar_acknowladgement($id_customer_order)` | L2924-3009 | Karigar acknowledgement PDF | Same as main ctrl |
| 13 | `get_KarigarOrders()` | L3010-3025 | AJAX: Karigar orders | Same as main ctrl |
| 14 | `get_ActiveWeightRange()` | L3026-3041 | AJAX: Weight range dropdown | Same as main ctrl |
| 15 | `get_pur_order_Details()` | L3042-3055 | AJAX: Purchase order details | Same as main ctrl |
| 16 | `get_purchase_issue_entry_items()` | L3056-3071 | AJAX: Issue entry items | Same as main ctrl |
| 17 | `update_qc_status()` | L3072-3281 | AJAX: Update QC status and insert items | Complex, writes multiple tables |
| 18 | `generate_lot($po_id)` | L3282-3771 | Generate lot from PO items | Complex internal flow |
| 19 | `generate_lot_from_halmarking($hm_process_id)` | L3772-4109 | Generate lot from hallmarking | Complex internal flow |
| 20 | `get_qc_status_details()` | L4110-4125 | AJAX: QC status details | Same as main ctrl |
| 21 | `get_karigar_pending_ordes()` | L4126-4139 | AJAX: Karigar pending orders (typo: "ordes") | Same as main ctrl |
| 22 | `get_karigar_details()` | L4140-4151 | AJAX: Karigar details | Same as main ctrl |
| 23 | `get_OrderProducts()` | L4152-4165 | AJAX: Order products | Same as main ctrl |
| 24 | `get_OrderProductsDesign()` | L4166-4179 | AJAX: Product designs | Same as main ctrl |
| 25 | `get_OrderSubDesigns()` | L4180-4195 | AJAX: Sub-designs | Same as main ctrl |
| 26 | `grn_invoice($id)` | L4196-4235 | GRN invoice PDF | Same as main ctrl |
| 27 | `update_karigar()` | L4236-4300 | AJAX: Update karigar details | Same as main ctrl |
| 28 | `md_dashboard($type,$id)` | L4301-4538 | MD Approval Dashboard — approval/reject orders | **NEW** vs main ctrl — MD workflow |

> **⚠️ KEY INSIGHT:** The approval controller (`admin_ret_purchase_approval`) is a **near-mirror** of the main controller — it duplicates all the core purchase/lot-generation logic but uses `ret_purchase_approval_model` instead of `ret_purchase_order_model`. New unique method: `md_dashboard()` handles the MD-level bulk approval/rejection workflow.

---

## 7f. Approval Model Methods — `ret_purchase_approval_model.php`
> **[R5-NEW]** Model: `admin/application/models/ret_purchase_approval_model.php`
> This model is a **near-complete mirror** of `ret_purchase_order_model` for approval stock flows.

| # | Method | Lines | Key Tables | Notes |
|---|---|---|---|---|
| 1 | `__construct()` | L5 | — | CI model constructor |
| 2 | `insertData($data,$table)` | L10 | `{any}` | Generic insert |
| 3 | `insertBatchData($data,$table)` | L16 | `{any}` | Batch insert |
| 4 | `updateBatchData($data,$table,$id_field,$id_value)` | L26 | `{any}` | Batch update |
| 5 | `updateData($data,$id_field,$id_value,$table)` | L38 | `{any}` | Generic update |
| 6 | `updateMultipleWhereData($data,$where,$table)` | L45 | `{any}` | Multi-where update |
| 7 | `deleteData($id_field,$id_value,$table)` | L52 | `{any}` | Generic delete |
| 8 | `get_FinancialYear()` | L60 | `ret_settings` | Financial year lookup |
| 9 | `generatePurNo()` | L66 | `customerorder` | Generate purchase number |
| 10 | `generatePaymentNo()` | L94 | `ret_po_payment` | Generate payment number |
| 11 | `generatePurRefOrderNo($is_suspense_stock,$gst_bill_type)` | L121 | `ret_purchase_order` | Generate PO ref number |
| 12 | `get_ActiveWeightRange($data)` | L141 | `ret_weight_range` | Weight range dropdown |
| 13 | `get_purchase_order_Details($data)` | L151 | `customerorder`, `customerorderdetails` | PO details |
| 14 | `get_karigar_order_details($id_customerorder)` | L168 | `customerorder`, `customerorderdetails` | Karigar order details |
| 15 | `get_order_images($id_orderdetails)` | L213 | Order images table | Order images |
| 16 | `get_karigar_order_products($id_customerorder)` | L222 | `customerorderdetails` | Order products |
| 17 | `get_karigar_order_product_details($id_customerorder,$id_product,$id_design,$id_sub_design)` | L265 | `customerorderdetails` | Product details |
| 18 | `get_karigar_order_detail_images($id_customerorder,$id_product,$id_design,$id_sub_design)` | L287 | Order image tables | Order detail images |
| 19 | `get_KarigarOrders($data)` | L304 | `customerorder` | Karigar orders list |
| 20 | `get_karigar_details($id_karigar)` | L320 | `ret_karigar` | Karigar details |
| 21 | `get_pur_order_details($data)` | L335 | `customerorder`, `customerorderdetails` | PO order details |
| 22 | `get_customer_order_pending_details($data)` | L368 | `customerorder`, `customerorderdetails` | Pending customer orders |
| 23 | `get_stock_repair_order($data)` | L392 | Repair order tables | Stock repair orders |
| 24 | `get_stock_repair_order_details($id_customerorder,$data)` | L416 | Repair detail tables | Repair order details |
| 25 | `get_customer_order_details($id_customerorder)` | L431 | `customerorder` | Customer order header |
| 26 | `get_customer_order_item_details($id_customerorder)` | L446 | `customerorderdetails` | Customer order items |
| 27 | `get_purchase_entry_details($from_date,$to_date)` | L489 | `ret_purchase_order`, `ret_karigar` | Purchase entry list |
| 28 | `get_approval_purchase_entry_details($from_date,$to_date)` | L510 | `ret_purchase_order`, `ret_karigar` | Approval purchase entry list |
| 29 | `get_purchase_issue_entry_items($data)` | L532 | `ret_purchase_order_item` | Issue entry items |
| 30 | `pourchase_entry_stone_details($po_item_id)` | L576 | Stone detail tables | Stone details |
| 31 | `get_status_details()` | L588 | Status tables | Status list |
| 32 | `purchase_issue()` | L608 | QC issue tables | Purchase issue list |
| 33 | `purchase_receipt_orders()` | L627 | QC receipt tables | Receipt orders |
| 34 | `get_purchase_item_details($po_id)` | L645 | `ret_purchase_order_item` | Purchase item details |
| 35 | `get_purchase_qc_details()` | L664 | QC tables | QC details |
| 36 | `generate_HalmarkingRefNo()` | L684 | `ret_halmarking_process` | HM ref number |
| 37 | `get_pending_halmarking_items()` | L711 | `ret_purchase_order_item` | Pending HM items |
| 38 | `get_halmarking_items($po_id)` | L730 | `ret_purchase_order_item` | HM items by PO |
| 39 | `get_halmarking_details()` | L748 | `ret_halmarking_process` | HM details |
| 40 | `get_halmarking_issue_orders()` | L759 | `ret_halmarking_process` | HM issue orders |
| 41 | `get_halmarking_issue_order_details($hm_process_id)` | L777 | HM issue detail tables | HM issue detail |
| 42 | `get_purchase_order_qc_details($po_id)` | L811 | QC + purchase tables | PO QC details |
| 43 | `get_purchase_order($po_id)` | L823 | `ret_purchase_order` | PO header |
| 44 | `get_purchase_by_category($po_id)` | L831 | `ret_purchase_order_item` | Items by category |
| 45 | `get_halmarking_purchase_order($hm_process_id)` | L842 | `ret_halmarking_process`, `ret_purchase_order` | HM PO |
| 46 | `get_purOrders($po_ref_no)` | L853 | `customerorder` | PO by ref no |
| 47 | `get_purchase_orders_by_product($po_id,$id_category,$id_purity,$stock_type)` | L861 | `ret_purchase_order_item` | PO by product |
| 48 | `get_po_purchase_items($po_id,$id_category)` | L879 | `ret_purchase_order_item` | PO items |
| 49 | `get_purchase_order_items($po_id,$id_product)` | L902 | `ret_purchase_order_item` | PO items by product |
| 50 | `check_purchase_halmarking_details($po_id)` | L921 | `ret_halmarking_issue_details` | HM check |
| 51 | `get_purchase_order_item_search($po_item_id)` | L944 | `ret_purchase_order_item` | PO item search |
| 52 | `get_pur_order_item_details($po_item_id)` | L967 | `ret_purchase_order_item` | PO item details |
| 53 | `get_ret_settings($settings)` | L989 | `ret_settings` | Settings lookup |
| 54 | `get_headOffice()` | L994 | `ret_settings` / branches | Head office |
| 55 | `update_po_paymentData($data,$arith)` | L1000 | PO payment tables | Update PO payment |
| 56 | `get_purchase_order_hm_details($hm_process_id)` | L1008 | HM detail tables | HM details |
| 57 | `get_supplier_pay_details($data)` | L1023 | Payment + PO tables | Supplier pay details |
| 58 | `purchase_payment_details($data)` | L1033 | Payment detail tables | Purchase payment details |
| 59 | `item_rate_fixing_details($po_item_id)` | L1104 | Rate fix detail tables | Item rate fix |
| 60 | `get_PO_Ratefix_List($from_date,$to_date)` | L1118 | Rate fix list tables | Rate fix list |
| 61 | `get_PurchasePaymentList($from_date,$to_date)` | L1131 | Payment tables | Payment list |
| 62 | `get_PurchaseSupplierPaymentList($from_date,$to_date)` | L1142 | Payment tables | Supplier payment list |
| 63 | `getAvailableOrders($SearchTxt,$supplierId)` | L1162 | `customerorder` | Available orders search |
| 64 | `get_rate_fixing_items($data)` | L1172 | Rate fixing tables | Rate fixing items |
| 65 | `get_bill_details($SearchTxt,$bill_cus_id)` | L1193 | `ret_purchase_order` | Bill details |
| 66 | `get_retWallet_details($id_karigar)` | L1203 | `ret_wallet_account` | Wallet details |
| 67 | `get_supplier_advance_details($data)` | L1214 | Advance detail tables | Advance details |
| 68 | `updateWalletData($data,$arith)` | L1221 | `ret_wallet_account` | Update wallet |
| 69 | `getRejectedPos($data)` | L1232 | Rejected PO tables | Rejected POs |
| 70 | `getRejectedItemsByPoId($poid)` | L1246 | Rejected items tables | Rejected items by PO |
| 71 | `get_purchaseItemStones($poId)` | L1268 | Stone detail tables | Purchase item stones |
| 72 | `get_purchaseItemOtherMetal($poId)` | L1278 | Other metal tables | Purchase item other metal |
| 73 | `getRejectedItemsBySupId($supid)` | L1289 | Rejected items tables | Rejected by supplier |
| 74 | `getReturnedRequestList()` | L1318 | Return request tables | Return request list |
| 75 | `get_return_non_tag_details($pur_ret_id)` | L1354 | Non-tag return tables | Return non-tag |
| 76 | `get_return_tag_details($pur_ret_id)` | L1360 | Tag return tables | Return tag details |
| 77 | `getReturnReceipt($id)` | L1366 | Return receipt tables | Return receipt |
| 78 | `getReturnReceiptDetails($id)` | L1388 | Return receipt detail tables | Return receipt details |
| 79 | `getReturnReceiptGSTDetails($id)` | L1401 | Return GST tables | Return GST details |
| 80 | `get_purchase_ret_charge_details($pur_ret_id)` | L1414 | Return charge tables | Return charges |
| 81 | `get_purchase_ret_charge_gst_details($pur_ret_id)` | L1423 | Return charge GST tables | Return charge GST |
| 82 | `pur_ret_refno()` | L1435 | `ret_purchase_return` | Return ref number |
| 83 | `get_max_pur_ret_refno()` | L1451 | `ret_purchase_return` | Max return ref no |
| 84 | `get_tag_details($tag_id)` | L1457 | `ret_taging` | Tag details |
| 85 | `get_order_description()` | L1469 | `ret_purchase_order_description` | Order description |
| 86 | `get_orderdescription($id)` | L1475 | `ret_purchase_order_description` | Order description by ID |
| 87 | `send_email(...)` | L1485 | None (send email) | Email send |
| 88 | `get_karigar_pending_orders($data)` | L1530 | `customerorder` | Karigar pending orders |
| 89 | `get_karigar_pending_order_details($data)` | L1544 | `customerorder`, `customerorderdetails` | Pending order details |
| 90 | `update_partial_order_delivery($data,$arith)` | L1568 | `customerorder` | Partial delivery update |
| 91 | `update_order_delivery($data,$arith)` | L1576 | `customerorder` | Order delivery update |
| 92 | `get_metal_issue_ref_no()` | L1586 | `ret_karigar_metal_issue` | Metal issue ref no |
| 93 | `getKarigarMetalIssueList()` | L1613 | `ret_karigar_metal_issue` | Metal issue list |
| 94 | `get_available_stock_details($data)` | L1627 | `ret_purchase_item_stock_summary` | Available stock |
| 95 | `get_metal_issue_purity($purity)` | L1650 | `ret_purity` | Metal issue purity |
| 96 | `getMetalIssue($id)` | L1656 | `ret_karigar_metal_issue` | Metal issue header |
| 97 | `getMetalIssueDetails($id)` | L1672 | Metal issue detail tables | Metal issue details |
| 98 | `checkNonTagItemExist($data)` | L1687 | `ret_non_tag_lot_detail` | Check non-tag exists |
| 99 | `updateNTData($data,$arith)` | L1708 | Non-tag tables | Update non-tag |
| 100 | `get_KarigarMetal_issue($metal_issue_id)` | L1714 | `ret_karigar_metal_issue` | Metal issue by ID |
| 101 | `get_purity_details($id_purity)` | L1727 | `ret_purity` | Purity details |
| 102 | `checkPurchaseItemStockExist($data)` | L1733 | `ret_purchase_item_stock_summary` | Stock check |
| 103 | `get_return_bill_details($bill_detail_id)` | L1751 | Return tables | Return bill details |
| 104 | `updatePurItemData($id_stock_summary,$data,$arith)` | L1758 | `ret_purchase_item_stock_summary` | Update stock summary |
| 105 | `get_purchase_order_pending_details($data)` | L1769 | `ret_purchase_order` | Pending PO details |
| 106 | `get_purchase_cus_order_details($id_customerorder)` | L1806 | `customerorderdetails` | Customer order details |
| 107 | `get_OrderProducts($data)` | L1827 | Order product tables | Order products |
| 108 | `get_OrderProductsDesign($data)` | L1840 | Order design tables | Order designs |
| 109 | `get_OrderSubDesigns($data)` | L1852 | Sub-design tables | Sub-designs |
| 110 | `get_pur_order_det($id_customerorder)` | L1865 | `customerorder` | PO detail |
| 111 | `get_cus_order_details($id_cus_order,$id_product,$design_no,$id_sub_design)` | L1876 | `customerorderdetails` | Customer order details (spec) |
| 112 | `updatePurOrderStatus($data,$arith)` | L1885 | `customerorder` | Update PO status |
| 113 | `updatePurOrderDetailStatus($data,$updateId,$arith)` | L1892 | `customerorderdetails` | Update PO detail status |
| 114 | `get_karigarPendingPos($karigar)` | L1900 | `ret_purchase_order` | Karigar pending POs |
| 115 | `get_karigarPosPaidHistory($karigar)` | L1937 | PO payment tables | Karigar payment history |
| 116 | `get_po_paid_detail($payid)` | L1951 | PO payment detail tables | Payment detail |
| 117 | `getPurchaseOrderDet($po_id)` | L1976 | `ret_purchase_order`, `ret_karigar` | PO header details |
| 118 | `getPurchaseOrderItemDet($po_id)` | L1985 | `ret_purchase_order_item` + joins | PO item details |
| 119 | `getPurchaseStoneDetails($po_item_id)` | L2040 | Stone detail tables | Stone details |
| 120 | `getPurchaseOtherMetalDetails($po_item_id)` | L2046 | Other metal tables | Other metal |
| 121 | `get_empty_record()` | L2052 | None | Empty record template |
| 122 | `generate_grn_refno($grn_type)` | L2077 | `ret_grn_entry` | GRN ref number |
| 123 | `ajax_getGrnentrydetails($from_date,$to_date)` | L2096 | `ret_grn_entry`, `ret_karigar` | GRN list |
| 124 | `get_grn_details($id)` | L2108 | `ret_grn_entry`, `ret_karigar` | GRN header |
| 125 | `get_grn_item_details($grn_id)` | L2140 | `ret_grn_entry_detail` | GRN items |
| 126 | `get_grn_stone_details($grn_item_id)` | L2159 | `ret_grn_stone_detail` | GRN stones |
| 127 | `get_grn_gst_details($grn_id)` | L2168 | `ret_grn_gst_detail` | GRN GST |
| 128 | `get_grn_charge_details($grn_id)` | L2182 | `ret_grn_other_charge` | GRN charges |
| 129 | `get_grn_charge_gst_details($grn_id)` | L2191 | GRN charge GST tables | GRN charge GST |
| 130 | `getActiveGRNsDetails()` | L2202 | `ret_grn_entry` | Active GRNs |
| 131 | `getGRNsCatDetailsbyGRNId($postdata)` | L2218 | GRN category tables | GRN category details |
| 132 | `getGRNCategoryStonedetails($grncatId)` | L2252 | GRN stone tables | GRN stone details |
| 133 | `getGRNOthermetalDetails($grncatId)` | L2262 | GRN other metal tables | GRN other metal |
| 134 | `getGRNOtherchargeDetails($grnId)` | L2274 | GRN charge tables | GRN other charges |
| 135 | `get_rate_fixing_po_no($data)` | L2286 | `ret_purchase_order`, rate fix tables | Rate fix PO numbers |
| 136 | `get_purchase_order_status($data)` | L2303 | `ret_purchase_order` | PO status |
| 137 | `get_md_pending_orders()` | L2334 | `customerorder` | **MD-UNIQUE**: MD pending orders |
| 138 | `get_md_order_items($id_customerorder)` | L2353 | `customerorderdetails` | **MD-UNIQUE**: MD order items |
| 139 | `md_approve_order($id_customerorder)` | L2372 | `customerorder` | **MD-UNIQUE**: Approve single order |
| 140 | `md_reject_order($id_customerorder,$reason)` | L2378 | `customerorder` | **MD-UNIQUE**: Reject order with reason |
| 141 | `md_bulk_approve_orders($order_ids)` | L2387 | `customerorder` | **MD-UNIQUE**: Bulk approve orders |
| 142 | `save_email_log($data)` | L2393 | `ret_order_email_logs` | Save email log |
| 143 | `update_email_log($id,$data)` | L2398 | `ret_order_email_logs` | Update email log |

> **⚠️ KEY INSIGHT — MD Approval Workflow:** Methods 137-141 are **unique to the approval model** (not in main model). They power the MD Dashboard (`md_dashboard()` in approval controller) for management-level order approval/rejection. The `md_bulk_approve_orders()` accepts an array of order IDs for batch processing.