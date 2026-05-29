# Customization Fingerprint — KLSNLIVE

> **Scan Date**: 2026-03-12  
> **Source**: `d:\XAMPP\htdocs\retail_v5`  
> **Client**: `d:\XAMPP\htdocs\KLSNLIVE`  
> **Modules Scanned**: 14  
> **Average Drift**: 26.2%  

---

## Summary

| Module | Ctrl | Model | JS | Views | CSS | Overall | Level | Lane |
|--------|------|-------|----|-------|-----|---------|-------|------|
| Purchase | 25% | 21% | 9% | 41% | MISS | 25.9% | 🟡 L2 | Adapted |
| LOT | 14% | 20% | 2% | 50% | 2% | 17.6% | 🟡 L2 | Adapted |
| Tagging | 15% | 19% | 10% | 85% | — | 26.1% | 🟡 L2 | Adapted |
| Customer Order | 12% | 16% | 22% | 73% | 0% | 24.4% | 🟡 L2 | Adapted |
| Branch Transfer | 8% | 17% | 15% | 60% | 0% | 20.1% | 🟡 L2 | Adapted |
| Estimation | 21% | 34% | 20% | 80% | 63% | 35.6% | 🟡 L2 | Adapted |
| Billing | 25% | 25% | 26% | 79% | 0% | 32.1% | 🟡 L2 | Adapted |
| Reports | 18% | 42% | 21% | 52% | — | 31.8% | 🟡 L2 | Adapted |
| Dashboard | 11% | 13% | 38% | 0% | 4% | 16.2% | 🟡 L2 | Adapted |
| Other Inventory | 49% | 37% | 48% | 100% | — | 52.8% | 🔴 L3 | Independent |
| Old Metal Process | 22% | 14% | 32% | 0% | 0% | 17.7% | 🟡 L2 | Adapted |
| Section Transfer | 60% | 7% | 24% | 0% | — | 24.3% | 🟡 L2 | Adapted |
| Stock Issue | 36% | 30% | 23% | 78% | 0% | 35.3% | 🟡 L2 | Adapted |
| Catalog Inventory | 18% | 7% | 1% | 0% | — | 7.3% | 🟢 L1 | Express |

## Lane Distribution

- 🟢 **Express** (Level 1): 1 modules — estimated 2-4 hours each
- 🟡 **Adapted** (Level 2): 12 modules — estimated 8-15 hours each
- 🔴 **Independent** (Level 3): 1 modules — estimated 25-40 hours each

> **Total estimated effort**: ~182 hours (~22.8 working days)

---

## Per-Module Detail

### Purchase (🟡 Level 2 — Adapted Lane)

- **Controller** (24.8% drift): 88/101 functions shared, 13 source-only, 0 client-only, 12 significantly modified | Lines: 13625 → 23686
  - Source-only: `check_cheque_number_exist`, `get_po_ratecut_balance_details`, `get_supplier_sale`, `get_img_by_grn_id`, `retagging_report` ... and 8 more
- **Model** (20.6% drift): 212/243 functions shared, 31 source-only, 0 client-only, 19 significantly modified | Lines: 9043 → 18734
  - Source-only: `get_qc_other_metal_details`, `getqcAcceptedStoneDetails`, `get_hm_issue_stone_details`, `get_po_paid_payment`, `get_grn_images` ... and 26 more
- **Js** (9.3% drift): 291/327 functions shared, 36 source-only, 0 client-only | Lines: 83121 → 78787
  - Source-only: `calculation_rate_fixed`, `validateChqDetailRow`, `create_new_empty_chqpay_row`, `removeChq_row`, `calculate_chq_Amount` ... and 31 more
- **Views** (41.2% drift): 12/12 files shared, 5 client-only, 2 significantly changed
- **CSS**: ⚠️ Client CSS missing

### LOT (🟡 Level 2 — Adapted Lane)

- **Controller** (14.3% drift): 20/21 functions shared, 1 source-only, 0 client-only, 2 significantly modified | Lines: 2565 → 4788
  - Source-only: `customer_acknowladgement`
- **Model** (20.0% drift): 39/45 functions shared, 6 source-only, 0 client-only, 3 significantly modified | Lines: 1902 → 3392
  - Source-only: `get_profile_settings`, `get_customer_lotInward_detail`, `get_customer_lot_details`, `getlotStoneDetails`, `getlotOtherMetalDetails`, `getlotOtherChargeDetails`
- **Js** (1.6% drift): 107/109 functions shared, 2 source-only, 0 client-only | Lines: 23629 → 23875
  - Source-only: `confirm_delete`, `getActiveSubDesignsForLot`
- **Views** (50.0% drift): 4/4 files shared, 2 client-only, 1 significantly changed
- **CSS** (1.8% drift) | Lines: 265 → 510

### Tagging (🟡 Level 2 — Adapted Lane)

- **Controller** (15.2% drift): 102/111 functions shared, 9 source-only, 1 client-only, 7 significantly modified | Lines: 9383 → 7897
  - Source-only: `convert_numeric_to_alpha`, `convert_alpha_to_numeric`, `format_tag_code`, `get_section_details`, `send_order_unlink_otp`, `verify_order_unlink_otp`, `bulk_tag_edit_log`, `update_purchase_cost`, `ret_duplicate_print_log_save`
  - Client-only: `get_currentMonth_alphabet`
- **Model** (19.2% drift): 122/130 functions shared, 8 source-only, 0 client-only, 17 significantly modified | Lines: 10254 → 19067
  - Source-only: `get_other_metals`, `get_non_tag_otherissue_details`, `get_section_details`, `getBrnachOtpRegMobile`, `get_cus_order_details`, `get_bulk_tag_edit_log_list`, `getTagStoneEditByTagId`, `get_lot_stone_details`
- **Js** (10.0% drift): 231/256 functions shared, 25 source-only, 1 client-only | Lines: 55760 → 50245
  - Source-only: `BulkEditDetails`, `calculate_total_mc`, `calc_pur_wastage`, `calculate_PartlySale_RowTotal`, `calculate_OldMetal_RowTotal` ... and 20 more
  - Client-only: `EditDetails`
- **Views** (85.2% drift): 13/14 files shared, 1 source-only, 13 client-only, 9 significantly changed
- **CSS**: No CSS file for this module

### Customer Order (🟡 Level 2 — Adapted Lane)

- **Controller** (12.3% drift): 54/57 functions shared, 3 source-only, 0 client-only, 4 significantly modified | Lines: 2378 → 4094
  - Source-only: `send_order_cancel_otp`, `verify_order_cancel_otp`, `get_estimation_tags`
- **Model** (16.2% drift): 97/111 functions shared, 14 source-only, 0 client-only, 4 significantly modified | Lines: 2320 → 3187
  - Source-only: `get_profile_settings`, `get_tag_scan_details`, `get_stock_issue_StoneDetails`, `get_order`, `getBrnachOtpRegMobile` ... and 9 more
- **Js** (22.1% drift): 145/159 functions shared, 14 source-only, 1 client-only | Lines: 35185 → 16934
  - Source-only: `confirm_repair_order_cancel`, `getCurrentDate`, `add_cutomer_25_12_2023`, `get_customer_old`, `calculate_est_stone_Amount` ... and 9 more
  - Client-only: `add_cutomer`
- **Views** (72.7% drift): 5/7 files shared, 2 source-only, 4 client-only, 2 significantly changed
- **CSS** (0.0% drift) | Lines: 254 → 254

### Branch Transfer (🟡 Level 2 — Adapted Lane)

- **Controller** (8.3% drift): 12/12 functions shared, 0 source-only, 0 client-only, 1 significantly modified | Lines: 1377 → 1400
- **Model** (17.3% drift): 51/52 functions shared, 1 source-only, 0 client-only, 8 significantly modified | Lines: 2235 → 1986
  - Source-only: `getBTnontags`
- **Js** (15.3% drift): 55/55 functions shared, 0 source-only, 0 client-only | Lines: 8878 → 4348
- **Views** (60.0% drift): 7/8 files shared, 1 source-only, 7 client-only, 1 significantly changed
- **CSS** (0.0% drift) | Lines: 276 → 276

### Estimation (🟡 Level 2 — Adapted Lane)

- **Controller** (21.0% drift): 51/62 functions shared, 11 source-only, 0 client-only, 2 significantly modified | Lines: 3573 → 3995
  - Source-only: `get_purities`, `get_old_metal_types`, `old_get_village`, `get_sectionBranchwise`, `pan_available` ... and 6 more
- **Model** (33.9% drift): 86/109 functions shared, 23 source-only, 0 client-only, 14 significantly modified | Lines: 3029 → 4355
  - Source-only: `get_bill_no_format_detail`, `get_data`, `GetActiveFinancialYear`, `tag_reserve_check`, `get_Active_Purity` ... and 18 more
- **Js** (20.1% drift): 159/191 functions shared, 32 source-only, 4 client-only | Lines: 31402 → 23843
  - Source-only: `get_search_tag_metal_rates`, `get_metal_master`, `calc_dia_tot_amt`, `get_profile`, `get_customer_25_12_2023` ... and 27 more
  - Client-only: `get_tag_data_30_04_2024`, `get_country`, `get_state`, `get_city`
- **Views** (80.0% drift): 2/2 files shared, 3 client-only, 1 significantly changed
- **CSS** (63.3% drift) | Lines: 244 → 231

### Billing (🟡 Level 2 — Adapted Lane)

- **Controller** (25.0% drift): 97/115 functions shared, 18 source-only, 5 client-only, 7 significantly modified | Lines: 12279 → 12686
  - Source-only: `get_customer_tcs_percent`, `order_adtrnssendotp`, `order_verify_otp`, `order_ad_trans_send_sms`, `order_delievery_sendotp` ... and 13 more
  - Client-only: `get_customer_tds_percent`, `send_advance_adj_otp`, `verify_advance_adj_otp`, `sales_return_otp`, `verify_otp_for_salesreturn`
- **Model** (25.1% drift): 203/238 functions shared, 35 source-only, 1 client-only, 24 significantly modified | Lines: 11740 → 10282
  - Source-only: `get_advance_refund`, `get_estimation_other_metal_details`, `get_bill_detail_other_inv`, `get_total_returned_amount_by_bill`, `get_otp_profile_settings` ... and 30 more
  - Client-only: `get_customer_wise_tds_percent`
- **Js** (26.0% drift): 280/382 functions shared, 102 source-only, 12 client-only | Lines: 44875 → 36326
  - Source-only: `create_new_empty_est_cus_charges_item`, `getOtherChargesDetails`, `create_est_cus_charges_item`, `validatecusOtherChargeDetailRow`, `showTagothermetals` ... and 97 more
  - Client-only: `add_customer`, `update_customer`, `get_country`, `get_state`, `get_city` ... and 7 more
- **Views** (78.6% drift): 6/8 files shared, 2 source-only, 6 client-only, 3 significantly changed
- **CSS** (0.0% drift) | Lines: 1225 → 1225

### Reports (🟡 Level 2 — Adapted Lane)

- **Controller** (17.6% drift): 202/235 functions shared, 33 source-only, 4 client-only, 5 significantly modified | Lines: 9994 → 15452
  - Source-only: `retagging_report`, `tagged_item_report`, `stock_details_v1`, `credit_pending_14_02_2024`, `get_influencer` ... and 28 more
  - Client-only: `stock_details`, `bookstocks_170324`, `sales_summary_details`, `stock_details_print`
- **Model** (42.5% drift): 322/389 functions shared, 67 source-only, 11 client-only, 92 significantly modified | Lines: 37324 → 51887
  - Source-only: `discount_bill_01_03_204`, `get_retagging_details`, `get_Tagged_items`, `get_order_value`, `get_total_order_advance` ... and 62 more
  - Client-only: `get_Chit_saved_details`, `get_Chit_details`, `item_summary_details`, `oldgetCustomerLedger`, `get_stock_details_` ... and 6 more
- **Js** (20.8% drift): 278/340 functions shared, 62 source-only, 6 client-only | Lines: 69963 → 86504
  - Source-only: `set_discBills_27_02_2024`, `get_stock_details_v2`, `get_stock_details_v1`, `update_order_description`, `set_credit_pending_list_14_02_2024` ... and 57 more
  - Client-only: `fnFormatRowChitDetails`, `oldget_CustomerLedger`, `sales_summary_data`, `set_dashboard_ordermangement_29_01_2024`, `getSearchCustomersLedger`, `fnTagWiseSaleExcelReport`
- **Views** (52.3% drift): 120/139 files shared, 19 source-only, 14 client-only, 47 significantly changed
- **CSS**: No CSS file for this module

### Dashboard (🟡 Level 2 — Adapted Lane)

- **Controller** (10.7% drift): 52/54 functions shared, 2 source-only, 2 client-only, 2 significantly modified | Lines: 2150 → 2100
  - Source-only: `get_cash_deposit_details_branchwise`, `get_LedgerBalanceAlert`
  - Client-only: `get_cash_in_hand`, `get_SaleBill_details_new`
- **Model** (12.9% drift): 91/97 functions shared, 6 source-only, 4 client-only, 3 significantly modified | Lines: 5241 → 5151
  - Source-only: `TagStockMetalWise`, `NonTagStockMetalWise`, `get_all_branch_store`, `get_deposit_details_branchwise`, `get_previous_date_closing`, `getLedgerBalanceAlertData`
  - Client-only: `StockMetalWise`, `get_paymentBillRecords_old`, `get_metalBillRecordsNew`, `get_saleBillRecordsNew`
- **Js** (37.7% drift): 76/101 functions shared, 25 source-only, 8 client-only | Lines: 19382 → 8729
  - Source-only: `get_deposit_data`, `money_formater`, `get_FinancialStatus`, `get_purchase_inwards`, `get_vendor_payment` ... and 20 more
  - Client-only: `get_sales_dashboard_details`, `sales_dashboard_dataNew`, `sales_dashboard_data_new`, `intToString`, `fnFormatRowTagDetails`, `formatRowsubproduct`, `get_bnk_deposits`, `get_tag_details`
- **Views** (0.0% drift): 0/0 files shared
- **CSS** (4.0% drift) | Lines: 226 → 224

### Other Inventory (🔴 Level 3 — Independent Lane)

- **Controller** (48.9% drift): 25/45 functions shared, 20 source-only, 0 client-only, 2 significantly modified | Lines: 1514 → 1049
  - Source-only: `get_img_by_item_id`, `product_details`, `generaterefCode`, `get_other_inventory_product`, `get_pro_detail_list` ... and 15 more
- **Model** (36.7% drift): 42/60 functions shared, 18 source-only, 0 client-only, 4 significantly modified | Lines: 869 → 553
  - Source-only: `getlastrefno`, `ajax_getProductlist`, `get_other_inventory_product`, `get_inv_purchase_images`, `get_ActiveCategory` ... and 13 more
- **Js** (47.7% drift): 33/63 functions shared, 30 source-only, 0 client-only | Lines: 3392 → 1770
  - Source-only: `get_other_item_category_list`, `get_other_inventory_ref_no`, `get_other_inv_product_details`, `get_other_inventory_product_item`, `calculate_item_gst_amt` ... and 25 more
- **Views** (100.0% drift): 4/5 files shared, 1 source-only, 4 significantly changed
- **CSS**: No CSS file for this module

### Old Metal Process (🟡 Level 2 — Adapted Lane)

- **Controller** (22.2% drift): 23/26 functions shared, 3 source-only, 1 client-only, 2 significantly modified | Lines: 1791 → 1629
  - Source-only: `get_opening_metal_stock_list`, `get_chg_tax_type`, `get_repair_pending_order`
  - Client-only: `get_hosection`
- **Model** (14.1% drift): 77/84 functions shared, 7 source-only, 1 client-only, 4 significantly modified | Lines: 1980 → 1718
  - Source-only: `get_opening_metal_stock_list`, `get_karigar_state`, `get_company_state`, `get_tag_details`, `get_repair_pending_order`, `get_purchase_cus_order_details`, `get_tag_cus_order_details`
  - Client-only: `get_hosection`
- **Js** (31.7% drift): 64/72 functions shared, 8 source-only, 1 client-only | Lines: 5087 → 9002
  - Source-only: `get_Activesections`, `validateRefiningReceiptCategoryRow`, `get_opening_metal_stock_list`, `calculate_opening_stock_old_metal`, `calculate_OldMetal_value`, `calculate_charges_tax`, `get_chg_tax_type`, `get_karigar_pending_orders`
  - Client-only: `get_ActiveSections`
- **Views** (0.0% drift): 1/1 files shared
- **CSS** (0.0% drift) | Lines: 149 → 149

### Section Transfer (🟡 Level 2 — Adapted Lane)

- **Controller** (60.0% drift): 3/5 functions shared, 2 source-only, 0 client-only, 1 significantly modified | Lines: 672 → 556
  - Source-only: `send_counterchange_otp`, `verify_counter_change_otp`
- **Model** (7.1% drift): 13/14 functions shared, 1 source-only, 0 client-only | Lines: 412 → 197
  - Source-only: `getBrnachOtpRegMobile`
- **Js** (23.7% drift): 9/10 functions shared, 1 source-only, 0 client-only | Lines: 1636 → 723
  - Source-only: `counterchange_otp`
- **Views** (0.0% drift): 1/1 files shared
- **CSS**: No CSS file for this module

### Stock Issue (🟡 Level 2 — Adapted Lane)

- **Controller** (35.7% drift): 10/13 functions shared, 3 source-only, 1 client-only, 1 significantly modified | Lines: 1412 → 1143
  - Source-only: `stock_trans_send_sms`, `stock_issue_sendotp`, `stock_issue_verify_otp`
  - Client-only: `stockissue_print`
- **Model** (29.6% drift): 23/26 functions shared, 3 source-only, 1 client-only, 4 significantly modified | Lines: 1372 → 1239
  - Source-only: `get_profile_settings`, `get_ret_settings`, `get_issue_item_tag`
  - Client-only: `getStockIssueDetails`
- **Js** (23.2% drift): 35/39 functions shared, 4 source-only, 0 client-only | Lines: 12210 → 5699
  - Source-only: `stock_at_send_otp`, `stock_order_otp`, `get_ActiveSections`, `get_nontag_details`
- **Views** (77.8% drift): 3/4 files shared, 1 source-only, 5 client-only, 1 significantly changed
- **CSS** (0.0% drift) | Lines: 255 → 255

### Catalog Inventory (🟢 Level 1 — Express Lane)

- **Controller** (18.4% drift): 152/163 functions shared, 11 source-only, 0 client-only, 19 significantly modified | Lines: 23578 → 19288
  - Source-only: `ret_crdr_ledger`, `vendor_trans_send_sms`, `vendor_sendotp`, `vendor_verify_otp`, `get_deviceNames` ... and 6 more
- **Model** (7.3% drift): 336/358 functions shared, 22 source-only, 0 client-only, 4 significantly modified | Lines: 8683 → 7984
  - Source-only: `get_profile_settings`, `get_tgrp_items`, `get_product_section`, `ajax_get_crdr_ledger`, `get_crdr_ledger` ... and 17 more
- **Js** (0.6% drift): 6/6 functions shared, 0 source-only, 0 client-only | Lines: 449 → 440
- **Views** (0.0% drift): 0/0 files shared
- **CSS**: No CSS file for this module

