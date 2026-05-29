# Ret_Reports — Method Index

> **Module**: Ret_Reports
> **Last Updated**: 2026-03-26 — Round 8 (Refresh — full line resync)
> **Controller**: `admin_ret_reports.php` — 236 methods (10,030 lines)
> **Model**: `ret_reports_model.php` — 392 methods (~37,491 lines)
> **Full model reference**: See [MODEL_METHOD_FULL.md](MODEL_METHOD_FULL.md) for all 392 methods in 21 groups

---

## 7a. Controller Methods (Alphabetical)

> Pattern: Most methods use `$type` parameter with `switch(list/ajax)`.
> "Tables Read" column shows which model methods are called (see 7b for table details).

| Method | Lines | Type/Sub-routes | Model Method(s) Called | View |
|---|---|---|---|---|
| `__construct()` | L15-55 | — | Loads 5 models | — |
| `acc_stock_details()` | L5785-5822 | list, ajax | `get_acc_stock_details()` | `acc_stock_details` |
| `active_category()` | L7903-7913 | — | `getActiveCategorymtr()` | — |
| `active_sch_code()` | L7914-7925 | — | `active_sch_code()` | — |
| `active_tagcategory()` | L5770-5784 | — | `getActiveCategory()` | — |
| `advance_history()` | L651-685 | list, ajax | `get_advance_details()` | `advance_details_report` |
| `advance_receipt_report()` | L6332-6371 | list, ajax | `get_advance_receipt_report()` | `order_advance` |
| `advance_total_details()` | L5606-5651 | list, ajax | `get_advance_total_details()` | `advance_details_report` |
| `advance_transfer_details()` | L7748-7786 | list, ajax | `get_advance_transfer()` | `advance_transfer_report` |
| `ajax_zone_list()` | L2820-2842 | — | `get_zone_list()` | — |
| `approvaltag_items_designwise()` | L4566-4611 | list, ajax | `get_approvaltag_items_designwise()` | `approvaltag_items_designwise` |
| `approvalstock_details()` | L1530-1567 | list, ajax | `get_approvalstock_details()` | `approvalstock_details` |
| `bill_discount()` | L915-950 | list, ajax | `getDiscountBill()` | `discount_bills` |
| `bookstocks()` | L6068-6090 | list, ajax | `get_categorywise_bookstocks_report()` | `stock_and_sales_report` |
| `branch_trans()` | L520-570 | list, approval_pending, intransit, ajax | `getBranchTransReport()`, `getIntransitDetails()` | `branch_transfer`, `approval_pending` |
| `branch_vault_report()` | L8549-8568 | list, ajax | `branch_vault_report()` | `branch_vault_report` |
| `branchreorder_items()` | L987-1022 | list, ajax | `getBranchReorderItems()` | `branchreorder_items` |
| `cancelled_bills()` | L841-878 | list, ajax | `getCancelledBills()` | `cancelled_bills` |
| `card_payment()` | L727-761 | list, ajax | `card_payment_details()` | `card_payment` |
| `cash_abstract()` | L362-402 | list, ajax | `getBillDetails()` | `cash_abstract` |
| `cash_book()` | L8621-8643 | list, ajax | `get_cash_book_details()` | `cash_book` |
| `cash_book_detail()` | L9139-9160 | list, ajax | `get_cash_book_detailed()` | `cash_book_detail` |
| `categorywise_bt_report()` | L6372-6407 | list, ajax | `get_categorywise_bt_report()` | `branch_transfer` |
| `categorywise_day_transaction()` | L8644-8664 | list, ajax | `get_categorywise_day_transaction()` | `categorywise_day_transaction` |
| `categorywise_stock_report()` | L6408-6449 | list, ajax | `get_categorywise_stock_report()` | `section_stock_details` |
| `card_collection_report()` | L6198-6235 | list, ajax | `get_card_collection_report()` | — |
| `cheque_collection_report()` | L6236-6275 | list, ajax | `get_cheque_collection_report()` | — |
| `check_old_tag_code()` | L5505-5523 | — | `check_old_tag_code()` | — |
| `check_old_tag_code_mismatched()` | L5524-5540 | — | `check_old_tag_code_mismatched()` | — |
| `chit_closing_details()` | L689-723 | list, ajax | `chit_utilize_details()` | `chit_closing` |
| `copy_bill_details()` | L765-799 | list, ajax | `getDulpicate_bill_details()` | `copy_bill` |
| `credit_history()` | L613-647 | list, ajax | `getcreditBill_history()` | `credit_history` |
| `credit_issued()` | L574-609 | list, ajax | `getcreditBill()` | `credit_issued` |
| `credit_pending()` | L3113-3132 | list, ajax | `getCreditPendingDetails()` | `credit_pending` |
| `credit_pending_14_02_2024()` | L3076-3112 | list, ajax | `getCreditPendingDetails_14_02_2024()` | `credit_pending` |
| `customer_analysis()` | L7393-7465 | list, ajax, tag_list, customer | `customer_sales_analysis()` | `customer_analysis` |
| `customer_detail()` | L6705-6720 | list | — | `customer_detail` |
| `customer_edit_log()` | L3475-3518 | list, ajax | `getCustomerEditLog()` | `customer_edit_log` |
| `customer_history()` | L2843-2874 | list, ajax | `get_customer_history()` | `customer_history` |
| `customer_ledger_statement()` | L4612-4653 | list, ajax | `getreceiptDetails()` | `customer_ledger_statement` |
| `customer_sales_analysis()` | L8140-8183 | list, ajax | `customer_sales_analysis()` | `customer_sales_analysis` |
| `dashboard_branchtransfer()` | L5719-5731 | — | model dashboard methods | `dashboard/` |
| `dashboard_btList()` | L5694-5718 | — | model dashboard methods | — |
| `dashboard_contractprice()` | L7642-7681 | list, ajax | `get_contract_pricing()` | — |
| `dashboard_creditsales()` | L3768-3778 | — | `getCreditSalesList()` | `dashboard/` |
| `dashboard_creditsalesList()` | L3747-3767 | — | `getCreditSalesList()` | — |
| `dashboard_customerorder()` | L3934-3944 | — | `getCustomerOrderList()` | `dashboard/` |
| `dashboard_customerorderList()` | L3911-3933 | — | `getCustomerOrderList()` | — |
| `dashboard_estimation()` | L3582-3592 | — | `getEstimationList()` | `dashboard/` |
| `dashboard_estimationList()` | L3559-3581 | — | `getEstimationList()` | — |
| `dashboard_giftcard()` | L3802-3812 | — | `getGiftCardList()` | `dashboard/` |
| `dashboard_giftcardList()` | L3779-3801 | — | `getGiftCardList()` | — |
| `dashboard_greentag()` | L3692-3710 | — | `getGreenTagList()` | `dashboard/` |
| `dashboard_greentagList()` | L3671-3691 | — | `getGreenTagList()` | — |
| `dashboard_lot()` | L3968-3978 | — | `getLotList()` | `dashboard/` |
| `dashboard_lotList()` | L3945-3967 | — | `getLotList()` | — |
| `dashboard_lottag()` | L3900-3910 | — | `getLotTagList()` | `dashboard/` |
| `dashboard_lottagList()` | L3877-3899 | — | `getLotTagList()` | — |
| `dashboard_oldmetal()` | L3734-3746 | — | `getOldMetalList()` | `dashboard/` |
| `dashboard_oldmetalList()` | L3711-3733 | — | `getOldMetalList()` | — |
| `dashboard_ordermanagement()` | L4011-4018 | — | — | `dashboard/` |
| `dashboard_ordermangemnetlist()` | L4019-4047 | — | `dashboard_ordermanagementlist()` | — |
| `dashboard_salereturn()` | L3866-3876 | — | `getSaleReturnList()` | `dashboard/` |
| `dashboard_salereturnList()` | L3845-3865 | — | `getSaleReturnList()` | — |
| `dashboard_sales()` | L3616-3626 | — | `getSalesList()` | `dashboard/` |
| `dashboard_salesList()` | L3593-3615 | — | `getSalesList()` | — |
| `dashboard_tag()` | L4002-4010 | — | `getTagList()` | `dashboard/` |
| `dashboard_tagList()` | L3979-4001 | — | `getTagList()` | — |
| `dashboard_virtualsales()` | L3834-3844 | — | `getVirtualSalesList()` | `dashboard/` |
| `dashboard_virtualsalesList()` | L3813-3833 | — | `getVirtualSalesList()` | — |
| `day_closing_report()` | L6740-6774 | list, ajax | `day_closing_report()` | `day_closing_report` |
| `daytransactions()` | L6776-6943 | list, ajax | `day_transactions_report()` | `accounts_reports/day_transaction_report` |
| `debit_payment()` | L3247-3304 | — | — | — |
| `deposit_report()` | L6947-7242 | list, ajax | `getall_cashamt_by_deposit_date()` + `ret_catalog_model->get_deposit()` | `cash_deposit_report` |
| `detailed_day_transaction_report()` | L8569-8592 | list, ajax | `detailed_day_transaction_report()` | `detailed_day_transaction_report` |
| `duplicate_tag_print_log()` | L9089-9138 | list, ajax | `duplicate_tag_print_log_list()` | `duplicate_tag_print_log` |
| `employee_sales_referal_and_sales_return()` | L8524-8548 | list, ajax | `get_est_details_and_sales_returns()` | `est_referal_and_sales_return` |
| `export_csv()` | L453-514 | — | `getBillDetails()`, `getallCategory()`, `getCompanyDetails()` | `ret_reports/print/export` |
| `feedback_report()` | L3392-3419 | list, ajax | model FB methods | `feedback_report` |
| `file_upload_new_tags()` | L5330-5504 | — | File import + DB insertion | — |
| `file_upload_old_tags()` | L5071-5329 | — | File import + DB insertion | — |
| `file_upload_stones()` | L5062-5070 | — | — | — |
| `file_upload_tags()` | L5028-5061 | — | File upload + import | — |
| `generate_cash_abstract()` | L406-449 | — | `getBillDetails()`, `getallCategory()`, `getCompanyDetails()` | `ret_reports/print/cash_abstract` (PDF) |
| `get_account_head_list()` | L9417-9428 | — | `get_account_head_list()` | — |
| `get_Activedesign()` | L1061-1075 | — | `get_Activedesign()` | — |
| `get_ActiveDevicename()` | L6488-6499 | — | `get_ActiveDevicename()` | — |
| `get_ActiveBankname()` | L6500-6510 | — | `get_ActiveBankname()` | — |
| `get_ActiveLedger()` | L9922-9928 | — | `get_ActiveLedger()` | — |
| `get_ActiveNontagProduct()` | L1100-1112 | — | `get_ActiveNontagProduct()` | — |
| `get_ActiveProduct()` | L1113-1125 | — | `get_ActiveProduct()` | — |
| `get_ActiveSubDesign()` | L1076-1087 | — | `get_ActiveSubDesign()` | — |
| `get_Active_St_SubDesign()` | L1088-1099 | — | `get_Active_St_SubDesign()` | — |
| `get_customer_details_report()` | L6721-6739 | — | `get_cus_details()` | — |
| `get_customer_reserveOrders()` | L7617-7627 | — | `get_customer_reserveOrders()` | — |
| `get_day_close_date()` | L5585-5605 | — | `getDayCloseLog_Details()` | — |
| `get_feedbackReport()` | L3420-3432 | — | model FB methods | — |
| `get_img_by_tag_id()` | L8098-8104 | — | `get_history_images()` | — |
| `get_influencer()` | L3133-3146 | — | `get_influencer()` | — |
| `get_new_tag_current_branch()` | L5552-5562 | — | `get_new_tag_current_branch()` | — |
| `get_current_branch()` | L5541-5551 | — | `get_current_branch()` | — |
| `get_old_metal_type()` | L2669-2691 | — | `getOldMetalType()` | — |
| `get_our_new_tag_code()` | L5574-5584 | — | `get_our_new_tag_code()` | — |
| `get_our_tag_code()` | L5563-5573 | — | `get_our_tag_code()` | — |
| `get_po_ref_nos()` | L8018-8032 | — | `get_po_ref_nos()` | — |
| `get_product_size()` | L8494-8503 | — | `get_product_size()` | — |
| `get_stock_detail_list()` | L3627-3670 | — | `get_stock_detail_list()` | — |
| `get_telecalling_report()` | L3379-3391 | — | model telecalling methods | — |
| `get_weight_range()` | L1126-1137 | — | `get_weight_range()` | — |
| `getCustomersBySearch()` | L7628-7641 | — | `getAvailableCustomers()` | — |
| `green_tag()` | L3000-3039 | list, ajax | `getGreenTagDetails()` | `green_tag` |
| `grnbills()` | L4180-4227 | list, ajax | `get_grn_bills()` | `grnbills_report` |
| `gst_abstract()` | L6091-6159 | list, ajax | `get_gst_abstract_details_v1()` | `gst_abstract_with_return` |
| `gst_abstract_bills()` | L9804-9921 | list, ajax | `get_gst_abstract_details_bills()` | — |
| `gst_abstract_v1()` | L9676-9803 | list, ajax | `get_gst_abstract_details_v1()` | — |
| `gst_abstract_with_return()` | L8105-8139 | list, ajax | `get_gst_abstract_with_return_details()` | `gst_abstract_with_return` |
| `gst_bills()` | L879-914 | list, ajax | `getgstBills()` | `gst_bills` |
| `gst_r1()` | L9504-9639 | list, ajax | `getGroupWiseBilling()` | — |
| `gstr1_sales()` | L7825-7864 | list, ajax | `get_gstr1_sales_details()` | — |
| `gstr2_purchase()` | L7865-7902 | list, ajax | `get_gstr2_purchase_details()` | — |
| `gt_return_report()` | L3519-3558 | list, ajax | `getGTReturnReport()` | `gt_return_report` |
| `ho_daily_stock_book()` | L8686-9087 | list, ajax | Multiple methods (complex aggregation) | `ho_daily_stock_book` |
| `incentive_report()` | L3191-3246 | list, ajax, staff | `get_staff_chit_incentive_details()` | `incentive_report`, `staff_incentive_report` |
| `index()` | L59-61 | — | — | — |
| `inventory_turnover()` | L8448-8468 | list, ajax | `get_inventory_turnover_details()` | `inventory_turnover` |
| `irn_report()` | L8263-8284 | list, ajax | `irn_details()` | `irn_report` |
| `item_delivery_report()` | L7787-7824 | list, ajax | `get_DeliveryListDetails()` | `item_delivery_report` |
| `item_sales()` | L1606-1643 | list, ajax | `getItemWiseSales()` | `item-wise_sales` |
| `item_sales_detail()` | L6450-6487 | list, ajax | `getItemWiseSalesDetail()` | `detail_item-wise_sales` |
| `karigar_metal_issue()` | L6663-6704 | list, ajax | `get_karigar_metal_issue()` | `karigar_metal_issue` |
| `karigar_wise_sales()` | L2907-2942 | list, ajax | `getKarigarWiseSales()` | `karigar_wise_sales` |
| `karigar_wise_sales_detail()` | L8226-8241 | list | — | `karigarwise_sale_detail` |
| `ledger_report()` | L9929-9946 | list, ajax | `getLedgerReportData()` | — |
| `lot_details()` | L2981-2999 | — | `get_lotDetails()` | — |
| `lot_history()` | L2943-2980 | list, ajax | `getLotHistory()` | `lot_history` |
| `lot_merge_report()` | L7682-7717 | list, ajax | `get_lot_merge_details()` | `lot_merge_report` |
| `lot_split_report()` | L7718-7747 | list, ajax | `get_lot_split_details()` | `lot_split_report` |
| `lot_wise()` | L244-278 | list, ajax | `getLotwiseSoldPending()` | `lot_wise` |
| `lottagvault()` | L282-316 | list, ajax | `getLotwiseTaggedVault()` | `lottagvault` |
| `metal_available_stock_details()` | L4088-4135 | list, ajax | `getMetalAvailableStockDetails()` | `metal_available_stock_details` |
| `metal_stock_details()` | L4048-4087 | list, ajax | `getMetalStockDetails()` | `metal_stock_report` |
| `moneyFormatIndia()` | L7246-7257 | — | — (utility) | — |
| `monthly_sales()` | L2581-2630 | list, ajax | `getMonthlySales()` | `monthly_sales` |
| `monthly_slaes_comparision()` | L3305-3360 | list, ajax | `monthly_sales_comparision()` | `monthly_comparision_report` |
| `netbanking_collection_report()` | L6309-6331 | list, ajax | `getNetbankingCollectionReport()` | — |
| `netbanking_collection_report_15_02_2024()` | L6276-6308 | list, ajax | `getNetbankingCollectionReport_15_02_2024()` | — |
| `old_metal_analyse()` | L2631-2668 | list, ajax | `getOldMetalAnalyseDetails()` | `old_metal_analyse` |
| `old_metal_pl_report()` | L7926-7967 | list, ajax | `get_OldMetal_ProfitLoss_details()` | `old_metal_p&l` |
| `old_metal_purchase()` | L65-99 | list, ajax | `getOldMetalPurchases()` | `old_metal_purchase` |
| `old_sale_report()` | L4888-4930 | list, ajax | `getOldSaleReport()` | `old_sale_report` |
| `old_tag_import_report()` | L4986-5027 | list, ajax | `getOldTagImportReport()` | `old_tag_import_report` |
| `other_issue()` | L1644-1682 | list, ajax | `getOtherIssueDetails()` | `other_issue` |
| `pan_bill_details()` | L803-840 | list, ajax | `getPanBillDetails()` | `pan_bill` |
| `partly_sold()` | L322-358 | list, ajax | `getPartlySold()` | `partly_sold` |
| `pay_device()` | L6511-6556 | list, ajax | `get_pay_device_bills()` | `pay_device` |
| `payment_mode_import()` | L6033-6067 | list, ajax | model import methods | — |
| `petty_cash_report()` | L9429-9480 | list, ajax | `get_petty_cash_report()` | — |
| `day_inout_cashbook()` | L9481-9503 | list, ajax | `get_day_inout_cashbook_details()` | — |
| `pohmstatus()` | L4326-4369 | list, ajax | `getHMProcessStatus()` | — |
| `popayments()` | L4422-4477 | list, ajax | `get_po_payments()` | `popayments` |
| `purchase()` | L4136-4179 | list, ajax | `getPurchaseBillsReport()` | `purchasebills_report` |
| `purchase_import()` | L5993-6032 | list, ajax | model import methods | — |
| `purchase_itemwise()` | L5863-5910 | list, ajax | `getPurchaseItemwise()` | `purchaseitem_wise` |
| `purchase_return()` | L4228-4281 | list, ajax | `getPurchaseReturnReport()` | `purchase_return` |
| `qcstatus()` | L4282-4325 | list, ajax | `getQCProcessStatus()` | — |
| `rate_fixed()` | L4478-4521 | list, ajax | `get_rate_fixed_details()` | `rate_fixed` |
| `receipts_ledger_statement()` | L8593-8620 | list, ajax | `getReceiptsAllLedger()` | `receipts_ledger_statement` |
| `reorder_items()` | L951-986 | list, ajax | `getReorderItems()` | `reorder_items` |
| `reorder_print()` | L8333-8389 | — | `get_reorder_stock_details()` | `reorder_print` |
| `reorder_product()` | L8285-8332 | — | `get_reorder_product()` | — |
| `reorder_report()` | L8390-8447 | list, ajax | `get_reorder_details()` | `reorder_report` |
| `repair_order()` | L8665-8685 | list, ajax | `get_repair_order_details()` | `repair_order_report` |
| `repair_gst_abstract()` | L8504-8523 | list, ajax | `get_repair_gst_abstract_details()` | — |
| `retag_report()` | L5823-5862 | list, ajax | `getRetag_details()` | `retag_report` |
| `retagging_report()` | L1023-1060 | list, ajax | `getRetaggingDetails()` | `retagging` |
| `sales_analysis_report()` | L2692-2819 | list, ajax + filters | `getSalesAnalysisReport()` | `sales_analysis` |
| `sales_comparision()` | L3040-3075 | list, ajax | `getSalesComparision()` | `sales_comparision` |
| `sales_import()` | L5953-5992 | list, ajax | model import methods | — |
| `sales_purchase()` | L9640-9675 | list, ajax | model methods | `sales_purchase` |
| `sales_return()` | L5652-5693 | list, ajax | `getSalesReturnDetails()` | `sales_return` |
| `sales_return_abstract()` | L6160-6197 | list, ajax | `getSalesReturnAbstract()` | — |
| `sales_transfer()` | L8184-8204 | list, ajax | `getSalesTransReport()` | — |
| `sales_transfer_report()` | L8205-8225 | list, ajax | `get_sales_transfer_details()` | `sales_transfer_report` |
| `section_stock_inout()` | L7466-7517 | list, ajax | `get_section_wise_stock_inout_details()` | `section_stock_details` |
| `section_transfer()` | L7968-8017 | list, ajax | `get_section_transfer_Details()` | `section_transfer_report` |
| `smithalltransactions()` | L4840-4887 | list, ajax | `getSmithAllTransactions()` | `smithalltransactions` |
| `smithtransaction()` | L4698-4739 | list, ajax | `getSmithTransactions()` | `smithtransactions` |
| `staff_incentive_report()` | L6603-6662 | list, ajax + detail | `get_staff_chit_incentive_details()`, `getSchemeClosedDetails()` | `staff_incentive_report`, `staff_incentive_detailed_report` |
| `stock_age()` | L103-178 | list, tag_list, tagging, dynamic, get_dynamic_age_list, ajax | Multiple age methods | `stock_age_analysis`, `stock_age_tag_list`, `stock_age_dynamic` |
| `stock_age_analysis()` | L8242-8262 | list, ajax | `get_stock_age_analysis()` | `stockage_analysis_dynamic` |
| `stock_and_sales_report()` | L3147-3190 | list, ajax | `getStockAndSalesReport()` | `stock_and_sales_report` |
| `stock_checking()` | L1568-1605 | list, ajax | `getStockCheckingDetails()` | `stock_checking` |
| `stock_details_v1()` | L1268-1529 | list, ajax + multiple sub-types | `get_stock_details_v1()`, `get_nontag_stock_details_v1()` | `stock_details_v1` |
| `stock_details_v2()` | L9161-9416 | list, ajax + multiple sub-types | `get_stock_details_v2()`, `get_nontag_stock_details_v2()` | `stock_details_v2` |
| `stock_issue_report()` | L8033-8071 | list, ajax | `get_stock_issue_report()` | `stock_issue_report` |
| `stock_ratio_avail()` | L8469-8493 | list, ajax | `get_stock_ratio_availability()` | `stock_ratio_availability` |
| `stock_report()` | L1222-1267 | list, ajax | `getStockReport()` | `stock_report` |
| `stock_reserve_order()` | L7578-7616 | list, ajax | `get_reserve_order()` | `reserve_order` |
| `stock_rotation()` | L5911-5952 | list, ajax | `get_stock_rotation_sales_details()` | `stock_rotation` |
| `supplier_contract_wise()` | L7258-7310 | list, ajax | `get_supplier_contract_details()` | `supplier_contract_wise` |
| `supplier_unified_transaction()` | L9947-9995 | list, ajax | `getSmithCombinedLedger()` | `supplier_unified_transaction` |
| `supplierledger()` | L4654-4697 | list, ajax | model supplier ledger | — |
| `suppliertransaction()` | L4740-4789 | list, ajax | `getSupplierTransactions()` | `suppliertransaction` |
| `supplier_approval_transaction()` | L4790-4839 | list, ajax | `getSupplierApprovalTransactions()` | `supplier_approval_transaction` |
| `tag_items_designwise()` | L1138-1179 | list, ajax | `getTagItemDesignwise()` | `tag_items_designwise` |
| `tag_stone()` | L5732-5769 | list, ajax | `getTagStone()` | `tag_stone` |
| `tagged_item_report()` | L1180-1221 | list, ajax | `getTaggedItemReport()` | `tagged_item` |
| `tagging_history()` | L8072-8097 | list, ajax | `get_tag_history()` | `tag_history_form` |
| `tagwiseprofit()` | L4370-4421 | list, ajax | `getTagWiseProfit()` | `tag_wise_wastage_profit` |
| `telecalling()` | L3361-3378 | list, ajax | model telecalling methods | `telecalling` |
| `tobe_history()` | L7518-7555 | list, ajax | `getTobe_history()` | `tobe_history` |
| `tobe_pending()` | L7556-7577 | list, ajax | `get_tobe_pending_details()` | `tobe_pending` |
| `unbilled_estimation()` | L2875-2906 | list, ajax | `getUnbilledEstimation()` | `unbilled_estimation` |
| `unfixing_report()` | L4522-4565 | list, ajax | `get_rate_unfixed_details()` | `unfixing_report` |
| `update_green_tag()` | L183-240 | — (POST only) | `updateData()` → `ret_taging` | — |
| `update_vip_customer()` | L3433-3474 | — (POST only) | model update | — |
| `upload_data()` | L4931-4985 | — | File upload handler | — |
| `weight_rage_report()` | L7311-7350 | list, ajax | `get_weight_rage_report()` | `weight_rage_report` |
| `weight_range_sales()` | L6557-6602 | list, ajax | `weight_range_sale_det()` | `weight_range_wise_sales` |
| `employee_wise_tag()` | L7351-7392 | list, ajax | `get_employee_wise_tag()` | `emp_wise_tag` |

---

## 7b. Model Methods — Summary

> **Full listing**: [MODEL_METHOD_FULL.md](MODEL_METHOD_FULL.md) — all 392 methods in 21 functional groups
> Key methods table retained below for quick reference.

### Model Method Group Summary
| Group | Count | Key Methods |
|---|---|---|
| CRUD/Generic | 5 | `insertData`, `updateData`, `get_data` |
| Tag Scanning | 14 | `tag_scan_details`, `get_tag_scan_missing`, `get_TagScannedDetails` |
| Bill/Sales | 36 | `getBillDetails`, `itemwise_sales`, `getCancelledBills`, `getGSTBills` |
| Stock/Inventory | 32 | `get_stock_details_v1/v2`, `getStockAgeDetails`, `getDynamicStockAgeDetails` |
| Branch Transfer | 8 | `getBranchTransReport`, `get_categorywise_bt_report` (4 date-suffixed copies ⚠️) |
| Credit/Advance/Payment | 20 | `getcreditBill`, `customerAdvanceReport`, `chit_utilize_details` (DUP ⚠️) |
| Day Reports/Cash Book | 10 | `day_transactions_report` (DUP ⚠️), `get_cash_book_details`, `detailed_day_transaction_report` |
| GST/Tax | 20 | `get_gst_r1_*`, `get_gst_abstract_*`, `get_gstr1/2_*` |
| Supplier/Karigar/Smith | 18 | `getSmithTransactionList`, `getSupplierTransactionList`, `get_karigar_metal_issue` |
| Purchase/GRN | 15 | `get_purchasebills_details`, `get_grnbills_details`, `get_po_payments` |
| Customer/CRM | 12 | `customer_sales_analysis`, `getCustomerLedger`, `get_cus_details` |
| Dashboard | 15 | `dashboard_*List()` methods |
| Collection Reports | 5 | `get_card/cheque/netbanking_collection_report` |
| Section/Reorder | 40+ | `get_section_wise_stock_inout_details`, `get_reorder_details` |
| Tag History | 16 | `get_tag_history`, `get_tagItemStones`, `getTagBillDetails` |
| Ledger/Misc | 20 | `getLedgerReportData`, `get_petty_cash_report`, `getall_cashamt_by_deposit_date` |
| Utility/Lookup | 26 | `check_old_tag_code`, `getDateRangeArray`, `getActiveCategorymtr` |
| HO Stock Book Support | 8 | `get_SectionTag_InwardOutward_Details`, `get_PurchaseReturnItems` |
| Remaining | 20+ | Various report-specific methods |
| New/Ungrouped (R8) | 29 | `irn_details`, `getGroupWiseBilling`, `get_inventory_turnover_details`, `getReceiptsAllLedger`, `getAllPaymentLedger`, +24 more |

### ⚠️ Model Anomalies
| Issue | Details |
|---|---|
| **Duplicate definitions** | `day_transactions_report()` at L17520 + L18358; `chit_utilize_details()` at L2321 + L2367 |
| **12 date-suffixed copies** | `*_28_04_2023`, `*_16_10_2023`, `*_15_07_24`, `*_on01082022`, `*_on_300721`, `*_23072022`, `*_14_02_2024`, `*_15_02_2024`, `*240323`, `*220623`, `*_29_07_22`, `*_01_03_204` |
| **Typo** | `discount_bill_01_03_204` — missing digit in date suffix |

---

## 7c. JS → Controller AJAX Map (Complete — 287 endpoints)

> **Source**: `admin/assets/js/ret_reports.js` (~2.73 MB, ~70K lines)
> Pattern: `base_url + 'index.php/admin_ret_reports/{method}/{sub_route}'`

### Internal AJAX Endpoints (admin_ret_reports)

| Report Area | JS AJAX URL(s) | Controller Method |
|---|---|---|
| **Sales Reports** | | |
| Cash Abstract | `cash_abstract/ajax` | `cash_abstract()` |
| Bill Discount | `bill_discount/ajax` | `bill_discount()` |
| Cancelled Bills | `cancelled_bills/ajax` | `cancelled_bills()` |
| GST Bills | `gst_bills/ajax` | `gst_bills()` |
| PAN Bills | `pan_bill_details/ajax` | `pan_bill_details()` |
| Credit Issued | `credit_issued/ajax` | `credit_issued()` |
| Credit History | `credit_history/ajax` | `credit_history()` |
| Credit Pending | `credit_pending/ajax` | `credit_pending()` |
| Copy Bill | `copy_bill_details/ajax` | `copy_bill_details()` |
| Card Payment | `card_payment/ajax` | `card_payment()` |
| Item Sales | `item_sales/ajax` | `item_sales()` |
| Item Sales Detail | `item_sales_detail/ajax` | `item_sales_detail()` |
| Monthly Sales | `monthly_sales/ajax` | `monthly_sales()` |
| Monthly Comparison | `monthly_slaes_comparision/ajax` | `monthly_slaes_comparision()` |
| Sales Comparison | `sales_comparision/ajax` | `sales_comparision()` |
| Sales Analysis | `sales_analysis_report/ajax` | `sales_analysis_report()` |
| Sales Return | `sales_return/ajax` | `sales_return()` |
| Sales Transfer | `sales_transfer_report/ajax` | `sales_transfer_report()` |
| Weight Range Sales | `weight_range_sales/ajax` | `weight_range_sales()` |
| Customer Sales Analysis | `customer_sales_analysis/ajax` | `customer_sales_analysis()` |
| **Stock Reports** | | |
| Old Metal Purchase | `old_metal_purchase/ajax` | `old_metal_purchase()` |
| Old Metal Analyse | `old_metal_analyse/ajax` | `old_metal_analyse()` |
| Old Metal P&L | `old_metal_pl_report/ajax` | `old_metal_pl_report()` |
| Stock Age | `stock_age/ajax`, `stock_age/get_dynamic_age_list` | `stock_age()` |
| Stock Age Analysis | `stock_age_analysis/ajax` | `stock_age_analysis()` |
| Lot Wise | `lot_wise/ajax` | `lot_wise()` |
| Lot Tag Vault | `lottagvault/ajax` | `lottagvault()` |
| Partly Sold | `partly_sold/ajax` | `partly_sold()` |
| Stock Report | `stock_report/ajax` | `stock_report()` |
| Stock Details V1 | `stock_details_v1/ajax` | `stock_details_v1()` |
| Stock Details V2 | `stock_details_v2/ajax` | `stock_details_v2()` |
| Stock Checking | `stock_checking/ajax` | `stock_checking()` |
| Stock Rotation | `stock_rotation/ajax` | `stock_rotation()` |
| Stock & Sales | `stock_and_sales_report/ajax` | `stock_and_sales_report()` |
| Section Stock In/Out | `section_stock_inout/ajax` | `section_stock_inout()` |
| Section Transfer | `section_transfer/ajax` | `section_transfer()` |
| Category Stock | `categorywise_stock_report/ajax` | `categorywise_stock_report()` |
| Bookstocks | `bookstocks/ajax` | `bookstocks()` |
| Inventory Turnover | `inventory_turnover/ajax` | `inventory_turnover()` |
| Stock Ratio | `stock_ratio_avail/ajax` | `stock_ratio_avail()` |
| Stock Issue | `stock_issue_report/ajax` | `stock_issue_report()` |
| Approval Stock | `approvalstock_details/ajax` | `approvalstock_details()` |
| Metal Stock | `metal_stock_details/ajax` | `metal_stock_details()` |
| Metal Available | `metal_available_stock_details/ajax` | `metal_available_stock_details()` |
| Reorder Items | `reorder_items/ajax` | `reorder_items()` |
| Reorder Report | `reorder_report/ajax` | `reorder_report()` |
| Reorder Print | `reorder_print/` | `reorder_print()` |
| Reorder Product | `reorder_product` | `reorder_product()` |
| **Branch / Transfer** | | |
| Branch Transfer | `branch_trans/ajax` | `branch_trans()` |
| Category BT | `categorywise_bt_report/ajax` | `categorywise_bt_report()` |
| Branch Vault | `branch_vault_report/ajax` | `branch_vault_report()` |
| **Tag Reports** | | |
| Tag Design-wise | `tag_items_designwise/ajax` | `tag_items_designwise()` |
| Tagged Item | `tagged_item_report/ajax` | `tagged_item_report()` |
| Tag Stone | `tag_stone/ajax` | `tag_stone()` |
| Tag History | `tagging_history/ajax` | `tagging_history()` |
| Retagging | `retagging_report/ajax` | `retagging_report()` |
| Retag Report | `retag_report/ajax` | `retag_report()` |
| Employee Tag | `employee_wise_tag/ajax` | `employee_wise_tag()` |
| Duplicate Print Log | `duplicate_tag_print_log/ajax` | `duplicate_tag_print_log()` |
| Green Tag | `green_tag/ajax` | `green_tag()` |
| GT Return | `gt_return_report/ajax` | `gt_return_report()` |
| ACC Stock | `acc_stock_details/ajax` | `acc_stock_details()` |
| Weight Range Report | `weight_rage_report/ajax` | `weight_rage_report()` |
| **Financial / Accounts** | | |
| Day Transactions | `daytransactions/ajax` | `daytransactions()` |
| Detailed Day Txn | `detailed_day_transaction_report/ajax` (4 calls in JS) | `detailed_day_transaction_report()` |
| Day Closing | `day_closing_report/ajax` | `day_closing_report()` |
| Category Day Txn | `categorywise_day_transaction/ajax` | `categorywise_day_transaction()` |
| Cash Book | `cash_book/ajax` | `cash_book()` |
| Cash Book Detail | `cash_book_detail/ajax` | `cash_book_detail()` |
| Deposit Report | `deposit_report/ajax` | `deposit_report()` |
| Petty Cash | `petty_cash_report/ajax` | `petty_cash_report()` |
| Day In/Out Cashbook | `day_inout_cashbook/ajax` | `day_inout_cashbook()` |
| Receipts Ledger | `receipts_ledger_statement/ajax` | `receipts_ledger_statement()` |
| Ledger Report | `ledger_report/ajax` | `ledger_report()` |
| Sales Purchase | `sales_purchase/ajax` | `sales_purchase()` |
| **GST Reports** | | |
| GST Abstract | `gst_abstract/ajax` | `gst_abstract()` |
| GST Abstract V1 | `gst_abstract_v1/ajax` | `gst_abstract_v1()` |
| GST Abstract Bills | `gst_abstract_bills/ajax` | `gst_abstract_bills()` |
| GST with Return | `gst_abstract_with_return/ajax` | `gst_abstract_with_return()` |
| GSTR1 B2B | `gst_r1/b2b` | `gst_r1()` |
| GSTR1 B2C | `gst_r1/b2cs_others` | `gst_r1()` |
| GSTR1 B2CL | `gst_r1/b2cl` | `gst_r1()` |
| GSTR1 HSN | `gst_r1/hsn_summary` | `gst_r1()` |
| GSTR1 Return B2B | `gst_r1/b2b_return` | `gst_r1()` |
| GSTR1 Return B2C | `gst_r1/b2c_return` | `gst_r1()` |
| GSTR1 Export | `gst_r1/export` | `gst_r1()` |
| GSTR1 Doc Summary | `gst_r1/doc_issued_smry` | `gst_r1()` |
| Repair GST | `repair_gst_abstract/ajax` | `repair_gst_abstract()` |
| IRN Report | `irn_report/ajax` | `irn_report()` |
| **Purchase / Supplier** | | |
| Purchase Bills | `purchase/ajax` | `purchase()` |
| GRN Bills | `grnbills/ajax` | `grnbills()` |
| Purchase Return | `purchase_return/ajax` | `purchase_return()` |
| Purchase Itemwise | `purchase_itemwise/ajax` | `purchase_itemwise()` |
| PO Payments | `popayments/ajax` | `popayments()` |
| Rate Fixed | `rate_fixed/ajax` | `rate_fixed()` |
| Unfixing | `unfixing_report/ajax` | `unfixing_report()` |
| Supplier Contract | `supplier_contract_wise/ajax` | `supplier_contract_wise()` |
| Tag-wise Profit | `tagwiseprofit/ajax` | `tagwiseprofit()` |
| Supplier Unified | `supplier_unified_transaction/ajax` | `supplier_unified_transaction()` |
| Smith All Txn | `smithalltransactions/ajax` | `smithalltransactions()` |
| **Customer** | | |
| Customer History | `customer_history/list/` | `customer_history()` |
| Customer Analysis | `customer_analysis/ajax` | `customer_analysis()` |
| Customer Ledger | `customer_ledger_statement/ajax` | `customer_ledger_statement()` |
| **Other** | | |
| Advance Receipt | `advance_receipt_report/ajax` | `advance_receipt_report()` |
| Advance Transfer | `advance_transfer_details/ajax` | `advance_transfer_details()` |
| Incentive | `incentive_report/ajax` | `incentive_report()` |
| Staff Incentive | `staff_incentive_report/ajax` | `staff_incentive_report()` |
| Karigar Sales | `karigar_wise_sales_detail/ajax` | `karigar_wise_sales_detail()` |
| HO Stock Book | `ho_daily_stock_book/ajax` | `ho_daily_stock_book()` |
| Repair Order | `repair_order/ajax` | `repair_order()` |
| Delivery | `item_delivery_report/ajax` | `item_delivery_report()` |
| Pay Device | `pay_device/ajax` | `pay_device()` |
| Lot History | `lot_history/ajax` | `lot_history()` |
| Lot Merge | `lot_merge_report/ajax` | `lot_merge_report()` |
| Lot Split | `lot_split_report/ajax` | `lot_split_report()` |
| TOBE History | `tobe_history/ajax` | `tobe_history()` |
| TOBE Pending | `tobe_pending/ajax` | `tobe_pending()` |
| Reserve Order | `stock_reserve_order/ajax` | `stock_reserve_order()` |
| Employee Referral | `employee_sales_referal_and_sales_return/ajax` | `employee_sales_referal_and_sales_return()` |
| Dashboard Order Mgmt | `dashboard_ordermangemnetlist` | `dashboard_ordermangemnetlist()` |
| Card Collection | `card_collection_report/ajax` | `card_collection_report()` |

### AJAX Dropdown Loaders (Internal)
| Endpoint | Purpose |
|---|---|
| `get_ActiveProduct` | Product dropdown |
| `get_Activedesign` | Design dropdown |
| `get_ActivesubDesign` | Sub-design dropdown |
| `get_ActiveBankname` | Bank dropdown |
| `get_ActiveLedger` | Ledger dropdown |
| `get_product_size/` | Size filter |
| `get_influencer` | Influencer filter |
| `get_account_head_list` | Account heads |
| `active_category` | Category filter |
| `active_tagcategory` | Tag category |
| `ajax_get_village` | Village filter |

### Cross-Module AJAX Endpoints (External Controllers)
| JS Line | AJAX URL | External Controller | Purpose |
|---|---|---|---|
| L42455, L42598 | `admin_ret_estimation/get_employee` | Estimation | Employee dropdown |
| L42552 | `admin_ret_catalog/category/active_category` | Catalog | Category dropdown |
| L43027+ | `admin_ret_billing/billing_invoice/` | Billing | Invoice print (20+ refs) |
| L43403 | `admin_ret_catalog/category/cat_purity` | Catalog | Purity filter |
| L43468, L43559 | `admin_ret_billing/generateEinvoice/` | Billing | E-invoice |
| L45155 | `admin_ret_catalog/get_deviceNames` | Catalog | Device names |
| L49227+ | `admin_ret_billing/receipt/receipt_print/` | Billing | Receipt print |
| L49703+ | `admin_ret_billing/issue/issue_print/` | Billing | Issue print |
| L50172 | `payment/invoice/` | Payment | Payment invoice |
| L61057 | `admin_ret_tagging/getTaggedLot` | Tagging | Tagged lot data |
| L61737 | `admin_ret_catalog/product_grouping/active_product_group` | Catalog | Product groups |
| L61767 | `metal/metalname_list` | Metal | Metal names |
| L64062 | `admin_ret_stock_issue/get_stock_issue_type` | Stock Issue | Issue types |

---

## 7d. Table → Methods Reverse Map (Key Tables)

| Table | Read By (Model Methods) | Written By |
|---|---|---|
| `ret_taging` | `get_stock_details_v1`, `get_stock_details_v2`, `getStockAgeDetails`, `getDynamicStockAgeDetails`, `get_section_wise_stock_inout_details`, `get_stock_rotation_sales_details`, `get_employee_wise_tag`, `get_tag_history`, `get_stock_issue_report`, `get_reorder_details`, `get_stock_age_analysis`, `get_stock_ratio_availability`, `get_weight_rage_report`, `customer_sales_analysis`, `getLotwiseSoldPending`, `getLotwiseTaggedVault`, `getTaggedItemReport`, `getGreenTagDetails`, `irn_details`, `get_rate_fixed_details`, +40 more | `update_green_tag()` (controller) |
| `ret_billing` | `getBillDetails`, `getGroupWiseBilling`, `day_transactions_report`, `get_cash_book_details`, `getReceiptsAllLedger`, `get_gst_abstract_with_return_details`, `get_gstr1_sales_details`, `customer_sales_analysis`, `get_DeliveryListDetails`, `irn_details`, `getSalesTransReport`, +30 more | — |
| `ret_bill_details` | `getBillDetails`, `getGroupWiseBilling`, `get_DeliveryListDetails`, `get_est_details_and_sales_returns`, `get_gst_abstract_with_return_details`, `getItemWiseSales`, `customer_sales_analysis`, `get_inventory_turnover_details`, +20 more | — |
| `ret_billing_payment` | `card_payment_details`, `get_card_collection_report`, `get_cheque_collection_report`, `getNetbankingCollectionReport`, `day_transactions_report`, `get_cash_book_details`, `get_petty_cash_report`, `getall_cashamt_by_deposit_date`, +15 more | — |
| `ret_branch_transfer` | `getBranchTransReport`, `get_categorywise_bt_report`, `branch_vault_report`, +5 more | — |
| `ret_lot_inwards` | `getLotwiseSoldPending`, `getLotwiseTaggedVault`, `get_lot_merge_details`, `get_lotDetails`, `branch_vault_report`, +5 more | — |
| `ret_purchase_order` | `get_po_payments`, `get_rate_fixed_details`, `get_gstr2_purchase_details`, `get_supplier_contract_details`, +5 more | — |
| `ret_grn_entry` / `ret_grn_items` | `get_grn_bills`, `get_gstr2_purchase_details`, +3 more | — |
| `ret_issue_receipt` | `day_transactions_report`, `get_cash_book_details`, `getReceiptsAllLedger`, +5 more | — |
| `customer` | `get_cus_details`, `get_advance_details`, `customer_sales_analysis`, `getReceiptsAllLedger`, `get_DeliveryListDetails`, `dashboard_ordermanagementlist`, +10 more | — |
| `ret_karigar_metal_issue` | `get_karigar_metal_issue`, `get_MetalIssueItems`, +2 more | — |
| `ret_estimation` | `get_est_details_and_sales_returns`, dashboard methods, +3 more | — |
| `ret_section` | `get_section_wise_stock_inout_details`, `get_section_transfer_Details`, `get_stock_details_v1`, +5 more | — |
| `ret_nontag_item` | `get_nontag_stock_details_v1`, `get_nontag_stock_details_v2`, `get_nontag_section_details`, +3 more | — |

---

## 7e. Settings Keys Used

| Key | Method | Purpose |
|---|---|---|
| `min_pan_amt` | `pan_bill_details()` L2526 | Minimum amount requiring PAN |
| `is_metal_for_billing` | `get_sales_return()` L11187 | Metal billing flag |
| `appr_stock_incl_in_reports` | `get_stock_details_v1()` L16634 | Include approval stock in reports |

---

## 7f. Hidden Fields by View (185 total across 90+ views)

### Common Hidden Fields
| Field Name | Views Using It | Purpose |
|---|---|---|
| `branch_filter` | 70+ views | Branch filter value |
| `branch_name` | 40+ views | Branch display name |
| `id_branch` | 5 views | Branch ID |
| `id_product` | 3 views | Product filter |
| `i_increment` | 3 views | Increment counter |

### Specialized Hidden Fields
| Field Name | View | Purpose |
|---|---|---|
| `Cus_id` | `advance_details_report` | Customer ID for advance |
| `emp_sales_incentive_gold_perg` | `green_tag`, `gt_return_report` | Gold incentive % |
| `emp_sales_incentive_silver_perg` | `green_tag`, `gt_return_report` | Silver incentive % |
| `stock_branch`, `stock_type` | `day_closing_report` | Stock params |
| `cur_year` | `monthly_comparision_report` | Current year |
| `id_old_metal_type` | `old_metal_analyse`, `old_metal_p&l` | Metal type filter |
| `ro_cus_id` | `reserve_order` | Reserve order customer |
| `id_section` | `scan_report` | Section filter |
| `id_tag_scanned` | `scan_report` | Scanned tag ID |
| `tag_id` | `tag_history`, `tag_history_form` | Tag identifier |
| `min_va`, `tag_charge_amt`, `tag_stone_details` | `tag_history_form` | Tag valuation data |
| `estimation[created_by]` | `est_referal_and_sales_return` | Estimation creator |
| `print_date1`, `print_date2` | `duplicate_tag_print_log` | Print date range |
| `id_from_sec`, `id_to_sec` | `section_transfer_report` | Section transfer params |
