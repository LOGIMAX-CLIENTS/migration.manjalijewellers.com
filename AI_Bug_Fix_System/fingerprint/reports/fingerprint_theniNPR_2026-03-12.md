# Customization Fingerprint — theniNPR

> **Scan Date**: 2026-03-12  
> **Source**: `d:\XAMPP\htdocs\retail_v5`  
> **Client**: `d:\XAMPP\htdocs\theniNPR_newversion`  
> **Modules Scanned**: 14  
> **Average Drift**: 6.4%  

---

## Summary

| Module | Ctrl | Model | JS | Views | CSS | Overall | Level | Lane |
|--------|------|-------|----|-------|-----|---------|-------|------|
| Purchase | 5% | 7% | 4% | 29% | 0% | 8.5% | 🟢 L1 | Express |
| LOT | 0% | 0% | 0% | 20% | 2% | 3.1% | 🟢 L1 | Express |
| Tagging | 5% | 2% | 1% | 52% | — | 10.6% | 🟡 L2 | Adapted |
| Customer Order | 2% | 5% | 0% | 71% | 0% | 12.8% | 🟡 L2 | Adapted |
| Branch Transfer | 0% | 0% | 0% | 43% | 0% | 6.5% | 🟢 L1 | Express |
| Estimation | 3% | 4% | 11% | 33% | 0% | 9.6% | 🟢 L1 | Express |
| Billing | 4% | 11% | 2% | 54% | 0% | 13.0% | 🟡 L2 | Adapted |
| Reports | 5% | 7% | 13% | 17% | — | 9.8% | 🟢 L1 | Express |
| Dashboard | 6% | 7% | 8% | 0% | 0% | 5.4% | 🟢 L1 | Express |
| Other Inventory | 0% | 0% | 0% | 0% | — | 0.0% | 🟢 L1 | Express |
| Old Metal Process | 0% | 0% | 0% | 0% | 0% | 0.1% | 🟢 L1 | Express |
| Section Transfer | 0% | 0% | 0% | 0% | — | 0.1% | 🟢 L1 | Express |
| Stock Issue | 0% | 0% | 1% | 57% | 0% | 8.7% | 🟢 L1 | Express |
| Catalog Inventory | 2% | 2% | 0% | 0% | — | 1.3% | 🟢 L1 | Express |

## Lane Distribution

- 🟢 **Express** (Level 1): 11 modules — estimated 2-4 hours each
- 🟡 **Adapted** (Level 2): 3 modules — estimated 8-15 hours each
- 🔴 **Independent** (Level 3): 0 modules — estimated 25-40 hours each

> **Total estimated effort**: ~69 hours (~8.6 working days)

---

## Per-Module Detail

### Purchase (🟢 Level 1 — Express Lane)

- **Controller** (5.0% drift): 96/101 functions shared, 5 source-only, 0 client-only | Lines: 13625 → 13390
  - Source-only: `check_cheque_number_exist`, `get_po_ratecut_balance_details`, `getPending_payment_po_bills`, `getPending_payment_po_bills_for_payment`, `get_pending_po_bills_after_tagging_without_pcs_with_weight`
- **Model** (6.6% drift): 230/243 functions shared, 13 source-only, 0 client-only, 3 significantly modified | Lines: 9043 → 8464
  - Source-only: `getTaggedRefNo`, `get_po_payment_pending_bills`, `getPending_payment_po_bills_for_payment`, `getPending_payment_po_bills`, `get_pending_po_bills_after_tagging_without_pcs_with_weight` ... and 8 more
- **Js** (3.5% drift): 320/327 functions shared, 7 source-only, 0 client-only | Lines: 83121 → 88709
  - Source-only: `getTaggedRefNo`, `load_approval_po_bill_details`, `load_approval_po_bills`, `load_supplier_approval_po_bills`, `load_supplier_po_bills`, `get_all_payment_pending_po_bills`, `calculateSelectedTotal`
- **Views** (29.4% drift): 12/12 files shared, 5 client-only
- **CSS** (0.0% drift) | Lines: 287 → 287

### LOT (🟢 Level 1 — Express Lane)

- **Controller** (0.0% drift): 21/21 functions shared, 0 source-only, 0 client-only | Lines: 2565 → 2543
- **Model** (0.0% drift): 45/45 functions shared, 0 source-only, 0 client-only | Lines: 1902 → 1902
- **Js** (0.0% drift): 109/109 functions shared, 0 source-only, 0 client-only | Lines: 23629 → 23629
- **Views** (20.0% drift): 4/4 files shared, 1 client-only
- **CSS** (1.7% drift) | Lines: 265 → 255

### Tagging (🟡 Level 2 — Adapted Lane)

- **Controller** (5.4% drift): 108/111 functions shared, 3 source-only, 0 client-only, 3 significantly modified | Lines: 9383 → 9439
  - Source-only: `convert_numeric_to_alpha`, `convert_alpha_to_numeric`, `format_tag_code`
- **Model** (2.3% drift): 129/130 functions shared, 1 source-only, 0 client-only, 2 significantly modified | Lines: 10254 → 9883
  - Source-only: `get_other_metals`
- **Js** (0.8% drift): 254/256 functions shared, 2 source-only, 0 client-only | Lines: 55760 → 55322
  - Source-only: `calc_pur_wastage`, `footerCallback`
- **Views** (51.9% drift): 14/14 files shared, 13 client-only, 1 significantly changed
- **CSS**: No CSS file for this module

### Customer Order (🟡 Level 2 — Adapted Lane)

- **Controller** (1.8% drift): 57/57 functions shared, 0 source-only, 0 client-only, 1 significantly modified | Lines: 2378 → 2324
- **Model** (5.4% drift): 106/111 functions shared, 5 source-only, 0 client-only, 1 significantly modified | Lines: 2320 → 2207
  - Source-only: `save_email_log`, `get_email_log_by_token`, `update_email_log`, `update_order_acceptance`, `update_order_rejection`
- **Js** (0.0% drift): 159/159 functions shared, 0 source-only, 0 client-only | Lines: 35185 → 35141
- **Views** (71.4% drift): 5/7 files shared, 2 source-only, 7 client-only, 1 significantly changed
- **CSS** (0.0% drift) | Lines: 254 → 254

### Branch Transfer (🟢 Level 1 — Express Lane)

- **Controller** (0.0% drift): 12/12 functions shared, 0 source-only, 0 client-only | Lines: 1377 → 1377
- **Model** (0.0% drift): 52/52 functions shared, 0 source-only, 0 client-only | Lines: 2235 → 2204
- **Js** (0.3% drift): 55/55 functions shared, 0 source-only, 0 client-only | Lines: 8878 → 8777
- **Views** (42.9% drift): 8/8 files shared, 6 client-only
- **CSS** (0.0% drift) | Lines: 276 → 276

### Estimation (🟢 Level 1 — Express Lane)

- **Controller** (3.2% drift): 60/62 functions shared, 2 source-only, 0 client-only | Lines: 3573 → 3368
  - Source-only: `get_va_range`, `getFinancialYr`
- **Model** (3.7% drift): 107/109 functions shared, 2 source-only, 0 client-only, 2 significantly modified | Lines: 3029 → 2891
  - Source-only: `get_stone_disc`, `get_va_range`
- **Js** (10.9% drift): 174/191 functions shared, 17 source-only, 0 client-only | Lines: 31402 → 26535
  - Source-only: `get_metal_master`, `calc_dia_tot_amt`, `validateVASlabDetailRow`, `create_new_empty_est_va_slab_row`, `apply_va_slab_row` ... and 12 more
- **Views** (33.3% drift): 2/2 files shared, 1 client-only
- **CSS** (0.0% drift) | Lines: 244 → 244

### Billing (🟡 Level 2 — Adapted Lane)

- **Controller** (4.3% drift): 112/115 functions shared, 3 source-only, 0 client-only, 2 significantly modified | Lines: 12279 → 12058
  - Source-only: `billing_insurance`, `bank_ledger_transfer`, `update_order_rate_type`
- **Model** (10.7% drift): 221/238 functions shared, 17 source-only, 4 client-only, 5 significantly modified | Lines: 11740 → 11465
  - Source-only: `get_total_returned_amount_by_bill`, `get_petty_cash_issue_amt`, `get_petty_cash_emp`, `getInsuranceDetails`, `code_insurance_number_generator` ... and 12 more
  - Client-only: `getColumnDefaults`, `check_advance_availability`, `check_idempotency`, `get_tag_status_for_update`
- **Js** (2.5% drift): 372/382 functions shared, 10 source-only, 0 client-only | Lines: 44875 → 43855
  - Source-only: `calculateDiscountAllocations`, `_distributeDiscountToTier`, `get_purchase_employee_options`, `append_est_sales_return_details`, `get_bank_ledger_transfer_list`, `ledger_transfer_list`, `ledger_transfer`, `applyBalanceLogic`, `onTransferModeChange`, `bLog`
- **Views** (53.8% drift): 6/8 files shared, 2 source-only, 5 client-only
- **CSS** (0.0% drift) | Lines: 1225 → 1225

### Reports (🟢 Level 1 — Express Lane)

- **Controller** (5.1% drift): 224/235 functions shared, 11 source-only, 1 client-only | Lines: 9994 → 9390
  - Source-only: `cash_book_detail`, `get_account_head_list`, `petty_cash_report`, `day_inout_cashbook`, `gst_r1` ... and 6 more
  - Client-only: `mc_avg`
- **Model** (7.2% drift): 366/389 functions shared, 23 source-only, 1 client-only, 4 significantly modified | Lines: 37324 → 33367
  - Source-only: `get_order_value`, `get_total_order_advance`, `get_gst_r1_b2b`, `get_gst_r1_b2c`, `get_gst_r1_b2cl` ... and 18 more
  - Client-only: `getMcaverageBilling`
- **Js** (13.1% drift): 316/340 functions shared, 24 source-only, 1 client-only | Lines: 69963 → 51382
  - Source-only: `get_gst_r1_b2b_items`, `get_gst_r1_b2cs_items`, `get_gst_r1_b2cl_items`, `get_gst_r1_hsn_summary`, `get_gst_r1_b2b_return` ... and 19 more
  - Client-only: `avg_mc_billing`
- **Views** (17.2% drift): 136/139 files shared, 3 source-only, 24 client-only, 1 significantly changed
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

- **Controller** (0.0% drift): 45/45 functions shared, 0 source-only, 0 client-only | Lines: 1514 → 1513
- **Model** (0.0% drift): 60/60 functions shared, 0 source-only, 0 client-only | Lines: 869 → 869
- **Js** (0.0% drift): 63/63 functions shared, 0 source-only, 0 client-only | Lines: 3392 → 3388
- **Views** (0.0% drift): 5/5 files shared
- **CSS**: No CSS file for this module

### Old Metal Process (🟢 Level 1 — Express Lane)

- **Controller** (0.0% drift): 26/26 functions shared, 0 source-only, 0 client-only | Lines: 1791 → 1772
- **Model** (0.0% drift): 84/84 functions shared, 0 source-only, 0 client-only | Lines: 1980 → 1979
- **Js** (0.3% drift): 72/72 functions shared, 0 source-only, 0 client-only | Lines: 5087 → 5041
- **Views** (0.0% drift): 1/1 files shared
- **CSS** (0.0% drift) | Lines: 149 → 149

### Section Transfer (🟢 Level 1 — Express Lane)

- **Controller** (0.0% drift): 5/5 functions shared, 0 source-only, 0 client-only | Lines: 672 → 672
- **Model** (0.0% drift): 14/14 functions shared, 0 source-only, 0 client-only | Lines: 412 → 412
- **Js** (0.4% drift): 10/10 functions shared, 0 source-only, 0 client-only | Lines: 1636 → 1614
- **Views** (0.0% drift): 1/1 files shared
- **CSS**: No CSS file for this module

### Stock Issue (🟢 Level 1 — Express Lane)

- **Controller** (0.0% drift): 13/13 functions shared, 0 source-only, 0 client-only | Lines: 1412 → 1404
- **Model** (0.0% drift): 26/26 functions shared, 0 source-only, 0 client-only | Lines: 1372 → 1377
- **Js** (0.6% drift): 39/39 functions shared, 0 source-only, 0 client-only | Lines: 12210 → 12472
- **Views** (57.1% drift): 4/4 files shared, 3 client-only, 1 significantly changed
- **CSS** (0.0% drift) | Lines: 255 → 255

### Catalog Inventory (🟢 Level 1 — Express Lane)

- **Controller** (1.8% drift): 160/163 functions shared, 3 source-only, 0 client-only | Lines: 23578 → 22920
  - Source-only: `ret_crdr_ledger`, `stone_discount`, `wastage_discount`
- **Model** (2.2% drift): 350/358 functions shared, 8 source-only, 0 client-only | Lines: 8683 → 8584
  - Source-only: `get_tgrp_items`, `ajax_get_crdr_ledger`, `get_crdr_ledger`, `ajax_get_stn_disc`, `get_stn_disc`, `check_wastage_range`, `ajax_get_wastage`, `get_wastage`
- **Js** (0.5% drift): 6/6 functions shared, 0 source-only, 0 client-only | Lines: 449 → 442
- **Views** (0.0% drift): 0/0 files shared
- **CSS**: No CSS file for this module

