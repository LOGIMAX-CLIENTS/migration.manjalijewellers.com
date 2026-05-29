# Customization Fingerprint — srjewellery

> **Scan Date**: 2026-03-27  
> **Source**: `d:\XAMPP\htdocs\retail_v5`  
> **Client**: `d:\XAMPP\htdocs\srjewellery`  
> **Modules Scanned**: 2  
> **Average Drift**: 20.7%  

---

## Summary

| Module | Ctrl | Model | JS | Views | CSS | Overall | Level | Lane |
|--------|------|-------|----|-------|-----|---------|-------|------|
| Billing | 29% | 22% | 14% | 73% | 0% | 28.1% | 🟡 L2 | Adapted |
| Reports | 7% | 20% | 11% | 15% | — | 13.3% | 🟡 L2 | Adapted |

## Lane Distribution

- 🟢 **Express** (Level 1): 0 modules — estimated 2-4 hours each
- 🟡 **Adapted** (Level 2): 2 modules — estimated 8-15 hours each
- 🔴 **Independent** (Level 3): 0 modules — estimated 25-40 hours each

> **Total estimated effort**: ~24 hours (~3 working days)

---

## Changes Since Last Scan (2026-03-27)

---

## Per-Module Detail

### Billing (🟡 Level 2 — Adapted Lane)

> **Drift Breakdown**: 80% Update Lag (111 source-only) | 20% Customization (6 client-only + 21 modified)

- **Controller** (28.6% drift): 112/147 functions shared, 35 source-only, 0 client-only, 7 significantly modified | Lines: 13812 → 11932
  - Source-only: `billing_insurance`, `bank_ledger_transfer`, `update_order_rate_type`, `getposdevicelists`, `_isPOSModuleEnabled` ... and 30 more
- **Model** (21.9% drift): 221/265 functions shared, 44 source-only, 0 client-only, 14 significantly modified | Lines: 12188 → 22729
  - Source-only: `get_total_returned_amount_by_bill`, `get_petty_cash_issue_amt`, `get_petty_cash_emp`, `getInsuranceDetails`, `code_insurance_number_generator` ... and 39 more
- **Js** (13.7% drift): 371/403 functions shared, 32 source-only, 6 client-only | Lines: 46509 → 35292
  - Source-only: `calculateDiscountAllocations`, `_distributeDiscountToTier`, `get_purchase_employee_options`, `get_daterange`, `append_est_sales_return_details` ... and 27 more
  - Client-only: `get_country`, `get_state`, `get_city`, `distributeRemainingDiscount`, `check_rate_is_valid`, `check_rate_disc_per_is_valid`
- **Views** (72.7% drift): 6/11 files shared, 5 source-only, 11 client-only
- **CSS** (0.0% drift) | Lines: 1225 → 1225

### Reports (🟡 Level 2 — Adapted Lane)

> **Drift Breakdown**: 58% Update Lag (70 source-only) | 42% Customization (7 client-only + 43 modified)

- **Controller** (6.8% drift): 221/235 functions shared, 14 source-only, 1 client-only, 1 significantly modified | Lines: 10030 → 9122
  - Source-only: `stock_details_v1`, `active_sch_code`, `duplicate_tag_print_log`, `cash_book_detail`, `stock_details_v2` ... and 9 more
  - Client-only: `stock_details`
- **Model** (19.7% drift): 360/390 functions shared, 30 source-only, 6 client-only, 42 significantly modified | Lines: 37490 → 33673
  - Source-only: `get_order_value`, `get_total_order_advance`, `getminMaxBillsgstAbstract`, `get_gst_r1_b2b`, `get_gst_r1_b2c` ... and 25 more
  - Client-only: `getBillDetails_30_08_2025`, `get_cc_handling_charges`, `get_stock_details`, `get_nontag_stock_details`, `get_bill_no_format_detail_dt`, `get_payment_detail`
- **Js** (11.1% drift): 314/340 functions shared, 26 source-only, 0 client-only | Lines: 70153 → 56810
  - Source-only: `get_stock_details_v2`, `get_stock_details_v1`, `get_gst_r1_b2b_items`, `get_gst_r1_b2cs_items`, `get_gst_r1_b2cl_items` ... and 21 more
- **Views** (15.2% drift): 134/139 files shared, 5 source-only, 19 client-only
- **CSS**: No CSS file for this module

