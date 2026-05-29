# Ret_Reports — Full Model Method Reference

> **Module**: Ret_Reports
> **Model**: `ret_reports_model.php` — 392 methods (~37,491 lines)
> **Last Updated**: 2026-03-26 — Round 8 (Refresh — full line resync)

---

## Functional Groups

### Group 1: CRUD / Generic (5 methods)
| Method | Line | Purpose |
|---|---|---|
| `__construct()` | L5 | Constructor |
| `insertData()` | L10 | Generic INSERT into any table |
| `updateData()` | L29 | Generic UPDATE on any table |
| `old_updateData()` | L10632 | Legacy update variant |
| `get_data()` | L25524 | Generic data getter |

### Group 2: Tag Scanning (14 methods)
| Method | Line | Purpose |
|---|---|---|
| `update_tag_scan()` | L16 | Update scan status |
| `get_tag_scan_missing()` | L4239 | Missing scan tags |
| `get_TagScannedDetails()` | L4251 | Scanned tag details |
| `get_product_scan_details()` | L4408 | Product-level scan summary |
| `get_tag_scanned_items()` | L4421 | Scanned item list |
| `get_unscanned_details()` | L4435 | Unscanned items |
| `get_tag_sold_details()` | L4450 | Sold during scan |
| `get_tag_scan_start()` | L4461 | Scan session start |
| `tag_scan_details()` | L4467 | Full scan report |
| `get_opening_balance()` | L4534 | Opening balance for scan |
| `get_inward_details()` | L4545 | Inward during scan |
| `get_entry_records()` | L4558 | Entry records |
| `get_tagging()` | L4600 | Tagging records |
| `get_scanned_details()` | L4634 | Scanned details |

### Group 3: Bill / Sales Data (36 methods)
| Method | Line | Purpose |
|---|---|---|
| `get_bill_customer()` | L36 | Bill customer lookup |
| `getOldMetalPurchases()` | L44 | Old metal purchase list |
| `return_bill_details()` | L261 | Return bill details |
| `get_partly_sales()` | L289 | Partly sold items |
| `getPartlySold()` | L540 | Partly sold list |
| `get_partial_sale_details()` | L535 | Partial sale details by tag_id + sold_bill_det_id (reads `ret_partlysold`) |
| `get_partly_sale_details()` | L10840 | Detail view |
| `getBillDetails()` | L623 | Main bill details query |
| `getOldMetalPurchaseAmount()` | L1208 | Old metal amounts |
| `getCompanyDetails()` | L1215 | Company info for reports |
| `getallCategory()` | L1238 | All category list |
| `getCancelledBills()` | L2726 | Cancelled bills |
| `getDiscountBills()` | L2762 | Discount bills |
| `discount_bill_01_03_204()` | L2802 | Date-suffixed variant ⚠️ |
| `discount_bill()` | L2845 | Current discount bill |
| `getGSTBills()` | L3001 | GST bills |
| `pan_bill_details()` | L2546 | PAN bill details |
| `itemwise_sales_details()` | L4649 | Item-wise sales |
| `design_wise_sales()` | L4855 | Design-wise sales |
| `design_wise_bill_det()` | L4899 | Design bill details |
| `itemwise_sales()` | L4914 | Item sales aggregate |
| `getBillingDetails()` | L5032 | Billing details |
| `get_salesDetails()` | L5159 | Sales details |
| `getOld_sales_details()` | L5238 | Old sales |
| `get_payment_details()` | L5249 | Payment details |
| `get_receipt_payment_details()` | L5257 | Receipt payments |
| `bill_wise_details()` | L5265 | Bill-wise report |
| `getOtherIssueList()` | L5301 | Other issue list |
| `get_home_bill_details()` | L5517 | Home billing |
| `get_sale_details()` | L5772 | Sale details |
| `getDulpicate_bill_details()` | L2510 | Duplicate bill copies |
| `getPartlySaleDetails()` | L11883 | Partly sale details |
| `getOldMetalItemDetails()` | L11864 | Old metal items |
| `get_salesDetails_groupwise()` | L35589 | Groupwise sales |
| `getOld_sales_details_groupwise()` | L35655 | Groupwise old sales |
| `get_payment_details_groupwise()` | L35677 | Groupwise payments |

### Group 4: Stock / Inventory (32 methods)
| Method | Line | Purpose |
|---|---|---|
| `getLotwiseSoldPending()` | L309 | Lot sold/pending |
| `getLotwiseTaggedVault()` | L340 | Lot vault stock |
| `getStartTagNo()` | L517 | Start tag number |
| `getEndTagNo()` | L526 | End tag number |
| `get_partly_sale_tag_details()` | L615 | Partly sale tags |
| `stock_balance_nontag()` | L3989 | Non-tag balance |
| `stock_details()` | L4059 | Stock details |
| `stock_checking()` | L4165 | Stock checking |
| `get_stock_details_29_07_22()` | L16548 | Date-suffixed ⚠️ |
| `get_stock_details_v1()` | L16643 | Stock details V1 |
| `get_stock_details_v2()` | L34162 | Stock details V2 |
| `get_approvalstock_details()` | L17222 | Approval stock |
| `get_nontag_stock_details_v1()` | L17322 | Non-tag V1 |
| `get_nontag_stock_details_v2()` | L34708 | Non-tag V2 |
| `get_OpeningStockDetails()` | L7700 | Opening stock |
| `get_metal_stock_details()` | L7710 | Metal stock |
| `get_available_metal_stock_details()` | L10683 | Available metal |
| `getOldMetalCatDetails()` | L10800 | Old metal categories |
| `stock_details_categorywise()` | L14181 | Category stock |
| `get_categorywise_stock_report()` | L19466 | Category stock report |
| `get_stock_detail_list()` | L20634 | Stock detail list |
| `get_stock_rotation_details()` | L12043 | Stock rotation |
| `get_StockRotationSales()` | L12111 | Rotation sales |
| `get_weightranageStockRotations()` | L12129 | Weight range rotation |
| `get_stock_rotation_list()` | L12173 | Rotation list |
| `get_stock_rotation_sales_details()` | L16114 | Rotation sales detail |
| `get_stock_age_analysis()` | L26982 | Stock age analysis |
| `getDynamicStockAgeDetails()` | L28811 | Dynamic age groups |
| `getStockAgeDetails()` | L2609 | Basic stock age |
| `get_stock_age_tag()` | L2638 | Single tag age |
| `get_stock_ratio_availability()` | L29181 | Stock ratio |
| `get_stock_issue_report()` | L25291 | Stock issue report |

### Group 5: Branch Transfer (8 methods)
| Method | Line | Purpose |
|---|---|---|
| `getBranchTransReport()` | L1554 | Branch transfer report |
| `get_categorywise_bt_report()` | L14358 | Category BT report |
| `get_categorywise_bt_report_on01082022()` | L14666 | Date-suffixed ⚠️ |
| `get_categorywise_bt_report_on_300721()` | L15015 | Date-suffixed ⚠️ |
| `get_categorywise_bt_report_23072022()` | L15294 | Date-suffixed ⚠️ |
| `getIntransitDetails()` | L6833 | In-transit items |
| `branch_vault_report()` | L29637 | Branch vault |
| `dashboard_btList()` | L11287 | Dashboard BT |

### Group 6: Credit / Advance / Payment (20 methods)
| Method | Line | Purpose |
|---|---|---|
| `getcreditBill()` | L2060 | Credit bills issued |
| `getcreditBill_history()` | L2160 | Credit history |
| `getCreditCollection()` | L2260 | Credit collection |
| `get_advance_details()` | L2292 | Advance details |
| `chit_utilize_details()` | L2321, L2367 | Chit utilization (DUPLICATE ⚠️) |
| `card_payment_details()` | L2412 | Card payments |
| `get_credit_pending_details_14_02_2024()` | L6859 | Date-suffixed ⚠️ |
| `get_credit_pending_details()` | L6926 | Credit pending |
| `get_IssueCreditCollectionDetails()` | L7028 | Issue credit collection |
| `get_credit_collection_details()` | L7038 | Credit collection detail |
| `customerAdvanceReport()` | L10869 | Customer advance |
| `get_customer_advance_details()` | L11030 | Advance details |
| `get_customer_advance_utilized_details()` | L11052 | Advance utilized |
| `get_customer_advance_refund_details()` | L11110 | Advance refund |
| `get_customer_advance_transfer_details()` | L11130 | Advance transfer |
| `get_advance_total()` | L11151 | Advance totals |
| `get_receipteddetails()` | L11191 | Receipted details |
| `get_utilizeddetails()` | L11206 | Utilized details |
| `get_advance_receipt_report()` | L14111 | Advance receipt report |
| `get_advance_transfer()` | L24431 | Advance transfer |

### Group 7: Day Reports / Cash Book (10 methods)
| Method | Line | Purpose |
|---|---|---|
| `getDayCloseLog_Details()` | L17488 | Day close log |
| `day_closing_report()` | L17496 | Day closing |
| `day_transactions_report()` | L17520 | Day transactions (1st def — commented legacy `/* */`) |
| `day_transactions_report()` | L18358 | Day transactions (active) |
| `detailed_day_transaction_report()` | L31152 | Detailed day txn |
| `get_cash_book_details()` | L32345 | Cash book |
| `get_cash_book_opening()` | L32688 | Cash book opening |
| `get_cash_book_detailed()` | L33811 | Cash book detailed |
| `get_categorywise_day_transaction()` | L32759 | Category day txn |
| `get_day_inout_cashbook_details()` | L34951 | Day in/out cashbook |

### Group 8: GST / Tax (20 methods)
| Method | Line | Purpose |
|---|---|---|
| `get_gst_abstract_details()` | L12291 | GST abstract |
| `getminMaxBills()` | L12439 | Min/max bills |
| `getminMaxBillsgstAbstract()` | L12458 | GST abstract min/max |
| `get_gst_abstract_overseas_details()` | L12483 | Overseas GST |
| `getminMaxBillsOverSeas()` | L12612 | Overseas min/max |
| `getRepairCharges()` | L12630 | Repair charges |
| `get_gst_r1_b2b()` | L12717 | GSTR1 B2B |
| `get_gst_r1_b2c()` | L12831 | GSTR1 B2C |
| `get_gst_r1_b2cl()` | L12908 | GSTR1 B2CL |
| `get_gst_r1_hsn_summary()` | L12960 | GSTR1 HSN summary |
| `get_hsn_summary_sales()` | L13018 | HSN sales |
| `get_hsn_summary_sales_return()` | L13075 | HSN sales return |
| `get_gst_r1_return()` | L13139 | GSTR1 returns |
| `get_gst_r1_doc_issued_smry()` | L13259 | GSTR1 doc summary |
| `get_gst_abstract_with_return_details()` | L25530 | GST with returns |
| `get_gst_abstract_with_return_overseas_details()` | L25656 | GST overseas returns |
| `get_gstr1_sales_details()` | L25119 | GSTR1 sales |
| `get_gstr2_purchase_details()` | L25188 | GSTR2 purchase |
| `get_gst_abstract_details_v1()` | L35693 | GST abstract V1 |
| `get_gst_abstract_details_bills()` | L36304 | GST abstract bills |

### Group 9: Supplier / Karigar / Smith (18 methods)
| Method | Line | Purpose |
|---|---|---|
| `getSupplierLedger()` | L9326 | Supplier ledger |
| `getSmithTransactionList()` | L9576 | Smith transactions |
| `getSupplierTransactionList220623()` | L9832 | Date-suffixed ⚠️ |
| `getSupplierTransactionList()` | L9913 | Supplier transactions |
| `getMetalwiseSupplierTransactionList()` | L10144 | Metalwise supplier |
| `getSupplierApprovalTransactionList()` | L10178 | Approval transactions |
| `getMetalwiseApprovalTransactionList()` | L10409 | Metalwise approval |
| `getSmithAllTransactionList()` | L10442 | All smith txn |
| `getSupplierAmountClosingBalance()` | L10612 | Amount closing |
| `getSupplierMetalClosingBalance()` | L10621 | Metal closing |
| `get_karigar_metal_issue()` | L16398 | Karigar metal issue |
| `get_issue_detials()` | L16512 | Issue details |
| `karigar_wise_sales()` | L6668 | Karigar sales |
| `get_karigar_wise_sales_detail()` | L26858 | Detail karigar sales |
| `get_supplier_contract_details()` | L20588 | Supplier contracts |
| `get_supplier_contract_stone_details()` | L20606 | Contract stones |
| `getSmithCombinedLedger()` | L37109 | Combined ledger |
| `getminMaxRepairBills()` | L13433 | Repair min/max |

### Group 10: Purchase / GRN (15 methods)
| Method | Line | Purpose |
|---|---|---|
| `get_purchasebills_details()` | L8144 | Purchase bills |
| `get_grnbills_details()` | L8323 | GRN bills |
| `get_charges()` | L8464 | Charges |
| `get_grn_charges_details()` | L8469 | GRN charges |
| `get_return_charges_details()` | L8481 | Return charges |
| `get_grn_other_metal_details()` | L8494 | GRN other metals |
| `get_tag_description()` | L8502 | Tag description |
| `get_purchase_billsitem_details()` | L8509 | Purchase items |
| `get_purchase_return_details()` | L8525 | Purchase returns |
| `get_return_other_metal_details()` | L8841 | Return metals |
| `get_purchasebillsqc_details()` | L8851 | QC details |
| `get_purchasebillshm_details()` | L8879 | Hallmark details |
| `get_PurchaseItemwise()` | L11910 | Purchase itemwise |
| `get_po_stone_details()` | L11941 | PO stones |
| `get_po_payments()` | L20540 | PO payments |

### Group 11: Customer / CRM (12 methods)
| Method | Line | Purpose |
|---|---|---|
| `get_customer_details()` | L6522 | Customer details |
| `get_cus_details()` | L16526 | Customer report data |
| `cusSalesDetails()` | L6387 | Customer sales |
| `getCustomerLedger()` | L9295 | Customer ledger |
| `get_customer_ledger_statement_details()` | L9307 | Ledger statement |
| `getCustomerLedgerTransaction()` | L9316 | Ledger transactions |
| `getAvailableCustomers()` | L20966 | Customer search |
| `get_customer_reserveOrders()` | L20958 | Reserve orders |
| `get_customer_edit_log()` | L7327 | Customer edit log |
| `get_mobileNumber()` | L11221 | Mobile numbers |
| `customer_sales_analysis()` | L26148 | Sales analysis |
| `get_customer_without_acc()` | L11489 | Customers without acc |

### Group 12: Dashboard (15 methods)
| Method | Line | Purpose |
|---|---|---|
| `dashboard_EstimationList()` | L7363 | Estimation |
| `dashboard_salesList()` | L7441 | Sales |
| `dashboard_greentagList()` | L7482 | Green tag |
| `dashboard_greentagList_incent()` | L7497 | Green tag incentive |
| `dashboard_oldmetalList()` | L7512 | Old metal |
| `dashboard_creditsalesList()` | L7523 | Credit sales |
| `dashboard_virtualsalesList()` | L7545 | Virtual sales |
| `dashboard_salereturnList()` | L7583 | Sale return |
| `dashboard_taglotList()` | L7593 | Tag lot |
| `dashboard_giftcardList()` | L7616 | Gift card |
| `dashboard_customerorderList()` | L7643 | Customer order |
| `dashboard_btList()` | L11287 | Branch transfer |
| `dashboard_ordermanagementlist()` | L26394 | Order management |
| `getAllbranchIds()` | L27106 | All branch IDs |
| `get_branches()` | L29356 | Branch list |

### Group 13: Collection Reports (5 methods)
| Method | Line | Purpose |
|---|---|---|
| `get_card_collection_report()` | L13533 | Card collection |
| `get_cheque_collection_report()` | L13679 | Cheque collection |
| `getNetbankingCollectionReport_15_02_2024()` | L13846 | Date-suffixed ⚠️ |
| `getNetbankingCollectionReport()` | L13985 | Net banking collection |
| `get_sals_return_details()` | L13452 | Sales return details |

### Group 14: Section / Reorder / Misc (40+ methods)
| Method | Line | Purpose |
|---|---|---|
| `get_profile_settings()` | L1203 | Profile settings |
| `get_ret_settings()` | L2541 | Ret settings lookup |
| `get_design_age_analysis_report()` | L2680 | Design age analysis |
| `getReorderitems()` | L3048 | Reorder items |
| `getBranchReorderitems()` | L3115 | Branch reorder |
| `get_retagging_details()` | L3162 | Retagging details |
| `get_reorder_settings()` | L3320 | Reorder settings |
| `get_Activedesign()` | L3335 | Active designs |
| `get_ActiveSubDesign()` | L3361 | Active sub-designs |
| `get_Active_St_SubDesign()` | L3385 | Active stone sub-design |
| `get_ActiveProduct()` | L3393 | Active products |
| `get_ActiveNontagProduct()` | L3418 | Active non-tag products |
| `get_weight_range()` | L3425 | Weight ranges |
| `getTaggeditems()` | L3438 | Tagged items |
| `getTagMultiMetalDetails()` | L3668 | Multi-metal tags |
| `getTagStoneDetails()` | L3679 | Tag stone details |
| `get_Tagged_items()` | L3689 | Tagged items variant |
| `getTaggeditems_branchwise()` | L3870 | Branch-wise tags |
| `getApprovalTaggeditems()` | L3930 | Approval tags |
| `getapprovalTaggeditems_branchwise()` | L3961 | Branch approval tags |
| `getBranchDayClosingData()` | L3984 | Branch day closing |
| `get_tagged_stone()` | L11322 | Tagged stones |
| `get_tagcategory()` | L11351 | Tag categories |
| `get_acc_stock_details()` | L11358 | ACC stock details |
| `product_analysis_details()` | L11370 | Product analysis |
| `get_crm_analysis_details()` | L11398 | CRM analysis |
| `get_sales_analysis_details()` | L11424 | Sales analysis |
| `sales_analysis_other_city()` | L11506 | Other city analysis |
| `sales_analysis_report()` | L11610 | Sales analysis report |
| `get_retag_report_details()` | L11716 | Retag report |
| `getSalesReturnRetag()` | L11841 | Sales return retag |
| `get_section_wise_stock_inout_details()` | L22829 | Section stock in/out |
| `get_section_stock_inout_details()` | L23868 | Section stock details |
| `get_section_stock_inout_details_28_04_2023()` | L20975 | Date-suffixed ⚠️ |
| `get_section_stock_inout_details_16_10_2023()` | L21108 | Date-suffixed ⚠️ |
| `get_section_wise_stock_inout_details_15_07_24()` | L21979 | Date-suffixed ⚠️ |
| `get_section_transfer_Details()` | L25255 | Section transfer |
| `get_section_transfer_non_tag_Details()` | L29613 | Non-tag section transfer |
| `get_nontag_section_details_16_10_2023()` | L24834 | Date-suffixed ⚠️ |
| `get_nontag_section_details()` | L24944 | Non-tag section |

### Group 15: Remaining Methods (26 methods)
| Method | Line | Purpose |
|---|---|---|
| `get_cart_items()` | L3293 | Cart items |
| `get_order_items()` | L3299 | Order items |
| `get_tagged_details()` | L3309 | Tagged details |
| `get_gift_voucher_details()` | L5887 | Gift vouchers |
| `checkGiftExpire()` | L5946 | Gift card expiry |
| `order_status_message()` | L5967 | Order messages |
| `order_status()` | L6035 | Order status |
| `get_order_images()` | L6157 | Order images |
| `get_order_stones()` | L6162 | Order stones |
| `get_village()` | L6175 | Village list |
| `monthly_sales()` | L6181 | Monthly sales |
| `get_sales_details_metal()` | L6212 | Metal sales |
| `get_pur_min_bill()` | L6227 | Min purchase bill |
| `get_pur_max_bill()` | L6237 | Max purchase bill |
| `get_max_bill()` | L6247 | Max bill |
| `get_min_bill()` | L6257 | Min bill |
| `get_old_metal_type()` | L6269 | Old metal types |
| `getOldMetalAnalyse()` | L6274 | Old metal analysis |
| `get_metal_rates()` | L6353 | Metal rates |
| `getActiveZone()` | L6360 | Active zones |
| `get_design_wise_sales_details()` | L6365 | Design sales |
| `get_scheme_account()` | L6489 | Scheme accounts |
| `get_modules()` | L6611 | Module list |
| `unbilled_estimation()` | L6617 | Unbilled estimations |
| `get_estimation_details()` | L6656 | Estimation details |
| `get_product_wise_sales()` | L6696 | Product sales |

### Group 16: Ledger / Misc Report (20 methods)
| Method | Line | Purpose |
|---|---|---|
| `get_received_details()` | L6707 | Received details |
| `getLotDetails()` | L6720 | Lot details |
| `getLotWiseSales()` | L6754 | Lot sales |
| `getGreenTagDetails()` | L6771 | Green tag details |
| `getMonthlySalesDetails()` | L6795 | Monthly sales detail |
| `get_incentive_report()` | L7123 | Incentive report |
| `incentive_emp_list()` | L7144 | Incentive emp list |
| `getVillageWiseSales()` | L7160 | Village sales |
| `get_monthly_sales_details()` | L7186 | Monthly sales |
| `get_telecalling_cus_det()` | L7212 | Telecalling |
| `get_feedbackReport()` | L7312 | Feedback report |
| `get_feedbackById()` | L7319 | Feedback by ID |
| `get_gt_return_report()` | L7342 | GT return report |
| `get_wastagewisepandlreport()` | L8955 | Wastage P&L |
| `get_popaymentreport()` | L9256 | PO payment report |
| `getall_cashamt_by_deposit_date()` | L20184 | Cash deposit |
| `get_petty_cash_report()` | L34885 | Petty cash |
| `get_account_head_list()` | L34944 | Account heads |
| `getLedgerReportData()` | L36725 | Ledger report |
| `get_ActiveLedger()` | L37104 | Active ledgers |

### Group 17: Utility / Lookup (26 methods)
| Method | Line | Purpose |
|---|---|---|
| `check_old_tag_code()` | L10638 | Old tag validation |
| `check_old_tag_code_mismatched()` | L10643 | Old tag mismatch |
| `get_old_sale_report_report()` | L10648 | Old sale report |
| `get_current_branch()` | L10661 | Current branch |
| `get_our_tag_code()` | L10665 | Our tag code |
| `get_day_close_date()` | L10669 | Day close date |
| `get_new_tag_current_branch()` | L10673 | New tag branch |
| `get_our_new_tag_code()` | L10677 | Our new tag code |
| `get_sales_return()` | L11229 | Sales return |
| `get_sales_return_details()` | L10818 | Return details |
| `get_partly_sale_details()` | L10840 | Partly sale |
| `get_sales_import()` | L11952 | Sales import |
| `get_purchase_import_details()` | L11975 | Purchase import |
| `get_payment_mode_import()` | L11998 | Payment import |
| `getDateRangeArray()` | L12155 | Date range utility |
| `get_contract_pricing()` | L24316 | Contract pricing |
| `getlotMergeNos()` | L24338 | Lot merge numbers |
| `get_lot_merge_details()` | L24343 | Lot merge details |
| `get_lotDetails()` | L24391 | Lot details |
| `get_lot_split_details()` | L24414 | Lot split details |
| `branch_outward()` | L24454 | Branch outward |
| `other_ow()` | L24518 | Other outward |
| `showroom_sales()` | L24574 | Showroom sales |
| `issued_stock()` | L24633 | Issued stock |
| `getActiveCategorymtr()` | L24676 | Active categories |
| `active_sch_code()` | L24692 | Active scheme codes |

### Group 18: HO Stock Book Support (8 methods)
| Method | Line | Purpose |
|---|---|---|
| `get_SectionTag_InwardOutward_Details()` | L33560 | Section tag in/out |
| `get_SectionNonTag_InwardOutward_Details()` | L33599 | Non-tag in/out |
| `get_PurchaseReturnItems()` | L33631 | Purchase return items |
| `get_MetalIssueItems()` | L33667 | Metal issue items |
| `indexing_array()` | L28770 | Array indexer utility |
| `calculate_inclusiveGST()` | L33525 | Inclusive GST calc |
| `calculate_ExclusiveGST()` | L33543 | Exclusive GST calc |
| `getRepairOrderDet()` | L33697 | Repair order details |

### Group 19: Tag History & Details (16 methods)
| Method | Line | Purpose |
|---|---|---|
| `get_tag_history()` | L25847 | Full tag history |
| `getTaggingBySearch()` | L25839 | Tagging search |
| `get_branch_details()` | L25983 | Branch details |
| `get_section_tag_det()` | L26002 | Section tag details |
| `get_scan_details()` | L26014 | Scan details |
| `get_tag_stock_details()` | L26023 | Tag stock |
| `get_history_images()` | L26043 | History images |
| `get_tagItemStones()` | L26048 | Tag item stones |
| `get_other_metal_details()` | L26061 | Other metals |
| `get_tag_stone_details()` | L26066 | Tag stones |
| `get_tagItemOtherMetal()` | L26087 | Tag other metal |
| `get_tag_det()` | L26099 | Tag details |
| `getTagEstDetails()` | L26110 | Tag estimation |
| `getTagBillDetails()` | L26129 | Tag billing |
| `getSalesTransReport()` | L26812 | Sales transfer |
| `get_sales_transfer_details()` | L26895 | Sales transfer detail |

### Group 20: Remaining / Misc (20 methods)
| Method | Line | Purpose |
|---|---|---|
| `stock_and_sales_details()` | L7067 | Stock & sales |
| `get_staff_chit_incentive_details()` | L16186 | Staff incentive |
| `getSchemeClosedDetails()` | L16239 | Scheme closed |
| `checkSchemeCloseBeiefits()` | L16296 | Scheme benefits |
| `getMetalRates()` | L16301 | Metal rates |
| `get_employee_referred_acc_details()` | L16307 | Employee referral |
| `weight_range_sale_det()` | L16162 | Weight range sales |
| `get_ActiveDevicename()` | L16031 | Active devices |
| `get_pay_device_bills()` | L16036 | Pay device bills |
| `get_rate_fixed_details()` | L20256 | Rate fixed |
| `get_rate_unfixed_details240323()` | L20303 | Date-suffixed ⚠️ |
| `get_rate_unfixed_details()` | L20356 | Rate unfixed |
| `get_weight_rage_report()` | L20729 | Weight range report |
| `get_employee_wise_tag()` | L20744 | Employee tags |
| `getTobe_history()` | L20776 | TOBE history |
| `get_tobe_pending_details()` | L20843 | TOBE pending |
| `get_reserve_order()` | L20922 | Reserve orders |
| `get_customer_reserveOrders()` | L20958 | Customer reserves |
| `getAvailableCustomers()` | L20966 | Available customers |
| `get_DeliveryListDetails()` | L24698 | Delivery list |

### Group 21: New / Ungrouped Methods (found during R8 scan)
| Method | Line | Purpose |
|---|---|---|
| `get_order_advance()` | L5588 | Order advance amount |
| `get_order_value()` | L5646 | Order value lookup |
| `get_total_order_advance()` | L5652 | Total order advance |
| `get_est_details()` | L5657 | Estimation details for orders |
| `getreceiptDetails()` | L25764 | Receipt details |
| `irn_details()` | L27015 | IRN (e-invoice) details |
| `get_categorywise_bookstocks_report()` | L27111 | Categorywise book stock report |
| `get_weight_range_details()` | L27877 | Weight range details |
| `get_product_size_details()` | L27888 | Product size details |
| `get_reorder_details()` | L27901 | Reorder details |
| `get_reordervalue()` | L28011 | Reorder value calculation |
| `get_reorder_stock_details()` | L28022 | Reorder stock details |
| `get_reorder_product()` | L28061 | Reorder product lookup |
| `get_inventory_turnover_details()` | L28069 | Inventory turnover analysis |
| `get_product_size()` | L29352 | Product size lookup |
| `get_influencer()` | L29361 | Influencer data |
| `get_repair_gst_abstract_details()` | L29368 | Repair GST abstract |
| `get_ActiveBankname()` | L29391 | Active bank names |
| `get_est_details_and_sales_returns()` | L29396 | Est details + sales returns |
| `getAdvanceAdj_amount()` | L31649 | Advance adjustment amount |
| `getReceiptsAllLedger()` | L31666 | All receipts ledger |
| `getAllPaymentLedger()` | L31919 | All payment ledger |
| `get_repair_order_details()` | L33464 | Repair order details |
| `duplicate_tag_print_log_list()` | L33769 | Duplicate tag print log |
| `getGroupWiseBilling()` | L35393 | Groupwise billing report |
| `get_OldMetal_ProfitLoss_details()` | L25217 | Old metal profit/loss |
| `get_po_ref_nos()` | L25284 | PO reference numbers |
| `get_bill_no_format_detail()` | L25466 | Bill number format details |
| `verify_po_payment()` | L20582 | Verify PO payment |

---

## ⚠️ Known Issues Summary

| Issue | Methods | Count |
|---|---|---|
| **Commented-out legacy** | `day_transactions_report()` L17520 in `/* */`, active at L18358; `chit_utilize_details()` L2321 in `/* */`, active at L2367 | 2 |
| **Date-suffixed copies** | `*_28_04_2023`, `*_16_10_2023`, `*_15_07_24`, `*_on01082022`, `*_on_300721`, `*_23072022`, `*_14_02_2024`, `*_15_02_2024`, `*240323`, `*220623`, `*_29_07_22`, `*_01_03_204` | 12 |
| **Typo in name** | `discount_bill_01_03_204` (missing digit) | 1 |
| **Total dead/legacy methods** | — | ~15 |
