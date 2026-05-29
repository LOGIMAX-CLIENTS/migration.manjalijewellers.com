# Customization Fingerprint — srjewellery

> **Scan Date**: 2026-03-12  
> **Source**: `d:\XAMPP\htdocs\retail_v5`  
> **Client**: `d:\XAMPP\htdocs\srjewellery`  
> **Modules Scanned**: 14  
> **Average Drift**: 8.6%  

---

## Summary

| Module | Ctrl | Model | JS | Views | CSS | Overall | Level | Lane |
|--------|------|-------|----|-------|-----|---------|-------|------|
| Purchase | 5% | 7% | 4% | 29% | 0% | 8.8% | 🟢 L1 | Express |
| LOT | 0% | 0% | 1% | 33% | 2% | 5.3% | 🟢 L1 | Express |
| Tagging | 8% | 2% | 1% | 26% | — | 7.0% | 🟢 L1 | Express |
| Customer Order | 3% | 5% | 1% | 64% | 0% | 12.2% | 🟡 L2 | Adapted |
| Branch Transfer | 0% | 2% | 18% | 46% | 0% | 11.9% | 🟡 L2 | Adapted |
| Estimation | 10% | 7% | 25% | 71% | 0% | 21.5% | 🟡 L2 | Adapted |
| Billing | 4% | 8% | 10% | 68% | 0% | 16.0% | 🟡 L2 | Adapted |
| Reports | 6% | 11% | 11% | 15% | — | 10.5% | 🟡 L2 | Adapted |
| Dashboard | 6% | 7% | 8% | 0% | 0% | 5.4% | 🟢 L1 | Express |
| Other Inventory | 2% | 2% | 0% | 0% | — | 1.2% | 🟢 L1 | Express |
| Old Metal Process | 0% | 0% | 0% | 0% | 0% | 0.1% | 🟢 L1 | Express |
| Section Transfer | 0% | 0% | 14% | 0% | — | 3.8% | 🟢 L1 | Express |
| Stock Issue | 8% | 0% | 14% | 67% | 0% | 15.4% | 🟡 L2 | Adapted |
| Catalog Inventory | 2% | 2% | 0% | 0% | — | 1.4% | 🟢 L1 | Express |

## Lane Distribution

- 🟢 **Express** (Level 1): 8 modules — estimated 2-4 hours each
- 🟡 **Adapted** (Level 2): 6 modules — estimated 8-15 hours each
- 🔴 **Independent** (Level 3): 0 modules — estimated 25-40 hours each

> **Total estimated effort**: ~96 hours (~12 working days)

---

## Per-Module Detail

### Purchase (🟢 Level 1 — Express Lane)

- **Controller** (5.0% drift): 96/101 functions shared, 5 source-only, 0 client-only | Lines: 13625 → 13553
  - Source-only: `check_cheque_number_exist`, `get_po_ratecut_balance_details`, `getPending_payment_po_bills`, `getPending_payment_po_bills_for_payment`, `get_pending_po_bills_after_tagging_without_pcs_with_weight`
- **Model** (7.4% drift): 230/243 functions shared, 13 source-only, 0 client-only, 5 significantly modified | Lines: 9043 → 8448
  - Source-only: `getTaggedRefNo`, `get_po_payment_pending_bills`, `getPending_payment_po_bills_for_payment`, `getPending_payment_po_bills`, `get_pending_po_bills_after_tagging_without_pcs_with_weight` ... and 8 more
- **Js** (3.5% drift): 320/327 functions shared, 7 source-only, 0 client-only | Lines: 83121 → 88714
  - Source-only: `getTaggedRefNo`, `load_approval_po_bill_details`, `load_approval_po_bills`, `load_supplier_approval_po_bills`, `load_supplier_po_bills`, `get_all_payment_pending_po_bills`, `calculateSelectedTotal`
- **Views** (29.4% drift): 12/12 files shared, 5 client-only
- **CSS** (0.0% drift) | Lines: 287 → 287

### LOT (🟢 Level 1 — Express Lane)

- **Controller** (0.0% drift): 21/21 functions shared, 0 source-only, 0 client-only | Lines: 2565 → 2552
- **Model** (0.0% drift): 45/45 functions shared, 0 source-only, 0 client-only | Lines: 1902 → 1902
- **Js** (0.7% drift): 108/109 functions shared, 1 source-only, 0 client-only | Lines: 23629 → 23587
  - Source-only: `customize`
- **Views** (33.3% drift): 4/4 files shared, 2 client-only
- **CSS** (1.7% drift) | Lines: 265 → 255

### Tagging (🟢 Level 1 — Express Lane)

- **Controller** (8.0% drift): 107/111 functions shared, 4 source-only, 1 client-only, 4 significantly modified | Lines: 9383 → 9467
  - Source-only: `convert_numeric_to_alpha`, `convert_alpha_to_numeric`, `format_tag_code`, `ret_duplicate_print_log_save`
  - Client-only: `check_isvaliddate`
- **Model** (1.5% drift): 129/130 functions shared, 1 source-only, 0 client-only, 1 significantly modified | Lines: 10254 → 9940
  - Source-only: `get_other_metals`
- **Js** (1.0% drift): 253/256 functions shared, 3 source-only, 0 client-only | Lines: 55760 → 55446
  - Source-only: `calc_pur_wastage`, `get_daterange`, `ret_duplicate_print_log_save`
- **Views** (26.3% drift): 14/14 files shared, 5 client-only
- **CSS**: No CSS file for this module

### Customer Order (🟡 Level 2 — Adapted Lane)

- **Controller** (3.4% drift): 57/57 functions shared, 0 source-only, 1 client-only, 1 significantly modified | Lines: 2378 → 2342
  - Client-only: `send_sms`
- **Model** (5.4% drift): 106/111 functions shared, 5 source-only, 0 client-only, 1 significantly modified | Lines: 2320 → 2199
  - Source-only: `save_email_log`, `get_email_log_by_token`, `update_email_log`, `update_order_acceptance`, `update_order_rejection`
- **Js** (0.7% drift): 158/159 functions shared, 1 source-only, 0 client-only | Lines: 35185 → 35440
  - Source-only: `get_branches`
- **Views** (63.6% drift): 5/7 files shared, 2 source-only, 4 client-only, 1 significantly changed
- **CSS** (0.0% drift) | Lines: 254 → 254

### Branch Transfer (🟡 Level 2 — Adapted Lane)

- **Controller** (0.0% drift): 12/12 functions shared, 0 source-only, 0 client-only | Lines: 1377 → 1357
- **Model** (1.9% drift): 52/52 functions shared, 0 source-only, 0 client-only, 1 significantly modified | Lines: 2235 → 2167
- **Js** (17.7% drift): 54/55 functions shared, 1 source-only, 1 client-only | Lines: 8878 → 4384
  - Source-only: `getNonTagReceiptedLots`
  - Client-only: `TagReceiptedLots`
- **Views** (46.2% drift): 7/8 files shared, 1 source-only, 5 client-only
- **CSS** (0.0% drift) | Lines: 276 → 276

### Estimation (🟡 Level 2 — Adapted Lane)

- **Controller** (9.5% drift): 57/62 functions shared, 5 source-only, 1 client-only | Lines: 3573 → 3350
  - Source-only: `passport_available`, `dl_available`, `get_old_metal_Product`, `get_va_range`, `getFinancialYr`
  - Client-only: `get_old_metal_category_with_rate`
- **Model** (7.3% drift): 105/109 functions shared, 4 source-only, 1 client-only, 3 significantly modified | Lines: 3029 → 2851
  - Source-only: `passport_available`, `dl_available`, `get_stone_disc`, `get_va_range`
  - Client-only: `get_old_metal_category_with_rate`
- **Js** (24.9% drift): 173/191 functions shared, 18 source-only, 4 client-only | Lines: 31402 → 13585
  - Source-only: `get_metal_master`, `calc_dia_tot_amt`, `get_old_metal_Product`, `validateVASlabDetailRow`, `create_new_empty_est_va_slab_row` ... and 13 more
  - Client-only: `get_old_metal_categories_with_rate`, `get_country`, `get_state`, `get_city`
- **Views** (71.4% drift): 2/2 files shared, 5 client-only
- **CSS** (0.0% drift) | Lines: 244 → 244

### Billing (🟡 Level 2 — Adapted Lane)

- **Controller** (3.5% drift): 112/115 functions shared, 3 source-only, 0 client-only, 1 significantly modified | Lines: 12279 → 11932
  - Source-only: `billing_insurance`, `bank_ledger_transfer`, `update_order_rate_type`
- **Model** (8.4% drift): 221/238 functions shared, 17 source-only, 0 client-only, 3 significantly modified | Lines: 11740 → 22729
  - Source-only: `get_total_returned_amount_by_bill`, `get_petty_cash_issue_amt`, `get_petty_cash_emp`, `getInsuranceDetails`, `code_insurance_number_generator` ... and 12 more
- **Js** (9.5% drift): 371/382 functions shared, 11 source-only, 6 client-only | Lines: 44875 → 35292
  - Source-only: `calculateDiscountAllocations`, `_distributeDiscountToTier`, `get_purchase_employee_options`, `get_daterange`, `append_est_sales_return_details` ... and 6 more
  - Client-only: `get_country`, `get_state`, `get_city`, `distributeRemainingDiscount`, `check_rate_is_valid`, `check_rate_disc_per_is_valid`
- **Views** (68.4% drift): 6/8 files shared, 2 source-only, 11 client-only
- **CSS** (0.0% drift) | Lines: 1225 → 1225

### Reports (🟡 Level 2 — Adapted Lane)

- **Controller** (6.4% drift): 221/235 functions shared, 14 source-only, 1 client-only | Lines: 9994 → 9122
  - Source-only: `stock_details_v1`, `active_sch_code`, `duplicate_tag_print_log`, `cash_book_detail`, `stock_details_v2` ... and 9 more
  - Client-only: `stock_details`
- **Model** (11.1% drift): 360/389 functions shared, 29 source-only, 6 client-only, 9 significantly modified | Lines: 37324 → 33673
  - Source-only: `get_order_value`, `get_total_order_advance`, `getminMaxBillsgstAbstract`, `get_gst_r1_b2b`, `get_gst_r1_b2c` ... and 24 more
  - Client-only: `getBillDetails_30_08_2025`, `get_cc_handling_charges`, `get_stock_details`, `get_nontag_stock_details`, `get_bill_no_format_detail_dt`, `get_payment_detail`
- **Js** (11.0% drift): 314/340 functions shared, 26 source-only, 0 client-only | Lines: 69963 → 56810
  - Source-only: `get_stock_details_v2`, `get_stock_details_v1`, `get_gst_r1_b2b_items`, `get_gst_r1_b2cs_items`, `get_gst_r1_b2cl_items` ... and 21 more
- **Views** (15.2% drift): 134/139 files shared, 5 source-only, 19 client-only
- **CSS**: No CSS file for this module

### Dashboard (🟢 Level 1 — Express Lane)

- **Controller** (5.6% drift): 52/54 functions shared, 2 source-only, 0 client-only, 1 significantly modified | Lines: 2150 → 2060
  - Source-only: `get_cash_deposit_details_branchwise`, `get_LedgerBalanceAlert`
- **Model** (7.1% drift): 91/97 functions shared, 6 source-only, 1 client-only | Lines: 5241 → 5089
  - Source-only: `TagStockMetalWise`, `NonTagStockMetalWise`, `get_all_branch_store`, `get_deposit_details_branchwise`, `get_previous_date_closing`, `getLedgerBalanceAlertData`
  - Client-only: `StockMetalWise`
- **Js** (7.5% drift): 92/101 functions shared, 9 source-only, 0 client-only | Lines: 19382 → 18584
  - Source-only: `get_deposit_data`, `get_delayed_po_payments`, `get_today_delivery_po_payments`, `get_delayed_purchase_orders`, `recalculate_delayed_report_footers`, `formatNumber`, `get_rate_cut_profit_loss`, `check_LedgerBalanceAlert`, `render`
- **Views** (0.0% drift): 0/0 files shared
- **CSS** (0.0% drift) | Lines: 226 → 226

### Other Inventory (🟢 Level 1 — Express Lane)

- **Controller** (2.2% drift): 44/45 functions shared, 1 source-only, 0 client-only | Lines: 1514 → 1502
  - Source-only: `CheckIsNameDuplicate`
- **Model** (1.7% drift): 59/60 functions shared, 1 source-only, 0 client-only | Lines: 869 → 846
  - Source-only: `CheckIsNameDuplicate`
- **Js** (0.2% drift): 63/63 functions shared, 0 source-only, 0 client-only | Lines: 3392 → 3370
- **Views** (0.0% drift): 5/5 files shared
- **CSS**: No CSS file for this module

### Old Metal Process (🟢 Level 1 — Express Lane)

- **Controller** (0.0% drift): 26/26 functions shared, 0 source-only, 0 client-only | Lines: 1791 → 1785
- **Model** (0.0% drift): 84/84 functions shared, 0 source-only, 0 client-only | Lines: 1980 → 1981
- **Js** (0.2% drift): 72/72 functions shared, 0 source-only, 0 client-only | Lines: 5087 → 5051
- **Views** (0.0% drift): 1/1 files shared
- **CSS** (0.0% drift) | Lines: 149 → 149

### Section Transfer (🟢 Level 1 — Express Lane)

- **Controller** (0.0% drift): 5/5 functions shared, 0 source-only, 0 client-only | Lines: 672 → 672
- **Model** (0.0% drift): 14/14 functions shared, 0 source-only, 0 client-only | Lines: 412 → 402
- **Js** (14.5% drift): 10/10 functions shared, 0 source-only, 0 client-only | Lines: 1636 → 846
- **Views** (0.0% drift): 1/1 files shared
- **CSS**: No CSS file for this module

### Stock Issue (🟡 Level 2 — Adapted Lane)

- **Controller** (7.7% drift): 13/13 functions shared, 0 source-only, 0 client-only, 1 significantly modified | Lines: 1412 → 1433
- **Model** (0.0% drift): 26/26 functions shared, 0 source-only, 0 client-only | Lines: 1372 → 1371
- **Js** (14.0% drift): 39/39 functions shared, 0 source-only, 0 client-only | Lines: 12210 → 6516
- **Views** (66.7% drift): 4/4 files shared, 5 client-only, 1 significantly changed
- **CSS** (0.0% drift) | Lines: 255 → 255

### Catalog Inventory (🟢 Level 1 — Express Lane)

- **Controller** (1.8% drift): 160/163 functions shared, 3 source-only, 0 client-only | Lines: 23578 → 22916
  - Source-only: `ret_crdr_ledger`, `stone_discount`, `wastage_discount`
- **Model** (2.5% drift): 349/358 functions shared, 9 source-only, 0 client-only | Lines: 8683 → 8573
  - Source-only: `get_tgrp_items`, `ajax_get_crdr_ledger`, `get_crdr_ledger`, `check_token_duplicates`, `ajax_get_stn_disc`, `get_stn_disc`, `check_wastage_range`, `ajax_get_wastage`, `get_wastage`
- **Js** (0.5% drift): 6/6 functions shared, 0 source-only, 0 client-only | Lines: 449 → 442
- **Views** (0.0% drift): 0/0 files shared
- **CSS**: No CSS file for this module

