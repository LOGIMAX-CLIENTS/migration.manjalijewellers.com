# DATA FLOW — Retail Dashboard
> Generated: 2026-03-16 | Module: Retail Dashboard

---

## Overview

This module is **read-only**. There are no CREATE, EDIT, or DELETE user flows. All flows are:
`Browser action → JS → AJAX POST → Controller → Model (SQL SELECT) → JSON → JS renders output`

---

## Flow 1: Live Cockpit Dashboard Load

**Trigger:** User clicks `#tab_livecockpit` OR page loads on `dashboard` case.

**JS function:** `get_live_cockpit_dashboard_details()`

**Fires the following sequential/parallel AJAX calls:**

| Step | AJAX URL | Controller Method | Model Method | Key Tables |
|---|---|---|---|---|
| 1 | `get_EstimationStatus` | `get_EstimationStatus()` | `get_dashboard_estimation()` | `ret_estimation`, `ret_estimation_items` |
| 2 | `get_BillingStatus` | `get_BillingStatus()` | `get_dashboard_billings()`, `get_dashboard_billings_mrp()`, `get_dashboard_billings_dia()` | `ret_billing`, `ret_bill_details`, `ret_product_master`, `ret_category`, `metal`, `ret_billing_item_stones`, `ret_stone` |
| 3 | `get_GreentagSalesDetails` | `get_GreentagSalesDetails()` | `get_dashboard_greentag_det()` | `ret_bill_details`, `ret_billing`, `ret_taging`, `ret_settings`, `metal` |
| 4 | `get_VitrualTagStatus` | `get_VitrualTagStatus()` | `get_dashboard_virturaltag_details()` | `ret_billing`, `ret_bill_details`, `ret_estimation_items`, `ret_taging` |
| 5 | `get_SalesReturnDetails` | `get_SalesReturnDetails()` | `get_dashboard_salesreturn_det()` | `ret_bill_details`, `ret_billing` |
| 6 | `get_old_metal_purchase` | `get_old_metal_purchase()` | `get_dashboard_old_metal_purchase()` | `ret_bill_old_metal_sale_details`, `ret_billing` |
| 7 | `get_CreditSalesDetails` | `get_CreditSalesDetails()` | `get_dashboard_credit_sales()` | `ret_billing`, `ret_issue_credit_collection_details`, `ret_issue_receipt` |
| 8 | `get_GiftVoucherDetails` | `get_GiftVoucherDetails()` | `get_dashboard_gift_vouchers()` | `gift_card` |
| 9 | `get_BillClassficationDetails` | `get_BillClassficationDetails()` | `get_dashboard_bills_clasfications()` | `ret_billing`, `ret_bill_details` |
| 10 | `get_BranchTransferDetails` | `get_BranchTransferDetails()` | `get_dashboard_approval_pendings()` | `ret_branch_transfer` |
| 11 | `get_lot_tag_details` | `get_lot_tag_details()` | `get_dashboard_lot_tag_details()` | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_taging` |
| 12 | `get_RecentBillDetails` | `get_RecentBillDetails()` | `get_dashboard_bills_details()` | `ret_billing`, `branch`, `customer` |
| 13 | `get_CustomerDetails` | `get_CustomerDetails()` | `get_dashboard_customer_details()` | `customer`, `branch` |

**Response:** Each returns independent JSON object. JS renders KPI cards, counters, mini-tables.

**Parameters passed:** `from_date`, `to_date`, `id_branch`

---

## Flow 2: Sales Chart Tab

**Trigger:** User clicks `#tab_sales` tab.

**JS function:** `sales_dashboard_data(from_date, to_date)`

| Step | Method Called | Tables Hit | Output |
|---|---|---|---|
| 1 | `getEstimationDetails()` | `ret_estimation`, `ret_estimation_items` | est created/sold/unsold + tag sales/returns |
| 2 | `get_green_tag_sales()` | `ret_bill_details`, `ret_billing`, `ret_taging`, `metal` | green tag sales count + amount |
| 3 | `get_creditDetilas()` | `ret_billing`, `ret_issue_credit_collection_details`, `ret_issue_receipt` | credit due + received |
| 4 | `get_customer_visit()` | `customer` | new vs old customer count |
| 5 | `get_sales_summary()` | `ret_billing`, `ret_bill_details` | total bill amount |
| 6 | `get_branchwise_sales()` | `ret_billing`, `ret_bill_details`, `branch` | branch-wise sales for Google Bar Chart |
| 7 | `get_modewise_sales()` | `ret_billing`, `ret_billing_payment` | payment mode-wise sales for Google Pie Chart |

**⚠️ Risk:** L1391 — `$estimation['tot_tag_sales']` used as denominator without zero-guard. Crashes if no tagged sales.

---

## Flow 3: Order Management Tab

**Trigger:** User clicks `#tab_order_management`.

**JS function:** `get_order_management_details()`

| AJAX Call | Controller | Tables |
|---|---|---|
| `get_OrderDetails` | `get_OrderDetails()` | `order_cart` |
| `get_KarigarOrderDetails` | `get_KarigarOrderDetails()` | `customerorderdetails`, `customerorder` |
| `get_customerOrderDetails` | `get_customerOrderDetails()` | `customerorder`, `customerorderdetails` |
| `get_MetalStockDetails` | `get_MetalStockDetails()` | `ret_taging_status_log`, `ret_taging`, `ret_nontag_item`, `metal` |
| `get_StockDetails` | `get_StockDetails()` | `ret_taging`, `ret_taging_status_log`, `ret_billing`, `ret_bill_details`, `metal` |
| `get_silver_StockDetails` | `get_silver_StockDetails()` | Same as gold but id_metal=2 |
| `get_ReorderDetails` | `get_ReorderDetails()` | `ret_reorder_settings`, `ret_taging`, `order_cart`, `ret_product_master` |

---

## Flow 4: Cash Abstract Widget

**Trigger:** User loads/refreshes cash abstract section.

**JS function:** `get_cash_abstract_data()` → AJAX to `get_cash_abstract_details`

**Controller:** `get_cash_abstract_details()` L1702-2083
- Uses `ret_reports_model::getBillDetails($_POST)` (cross-module)
- PHP-level aggregation: loops through items, returns, purchases, advances, payments, credits
- Builds `dash_cash_abstarct_details` array with 25+ computed fields

**Key computed fields:**
- `trans_total` = sales + tax + credit received + advance - purchase - returns - credit due - other expenses + handling
- `paymodes_total` = cash + card + online + cheque + adv_adj + chit_uti + order_adj + gift_voucher - roundoff

**⚠️ Note:** The model has `get_dashboard_cash_abstarct_details()` (L672-1295) which is now dead code. The controller bypasses it in favor of `ret_reports_model`.

---

## Flow 5: Branch-Wise Sales (Sales Report Tab)

**Trigger:** Page load on `dashboard` case → `get_branch_order()` fires.

**JS:** `get_branch_order()` → AJAX to `get_SaleBill_details`

**Controller:** `get_SaleBill_details()` L698-882
```
if id_branch == 0:
  loop all branches:
    - get_saleBillRecords(branch, from, to)  → category-wise
    - get_paymentBillRecords(branch, from, to) → payment-wise
    - get_metalBillRecords(branch, from, to)  → metal-wise
  get payment_summary → get_paymentBillRecords('', from, to, allBranch=1)
else:
  get for single branch only
```

---

## Flow 6: Stock Chart Tab

**Trigger:** `#tab_stock_gchart` click → Google Charts callback `get_stockDetails()`

**JS:** `get_stockDetails()` → AJAX to `get_stockchart_details`

**Controller:** `get_stockchart_details()` L1449-1489
```
loop all branches:
  get_branch_stock_details(from, to, branch_id)
  if available_pcs > 0: add to response
get_branch_transfer_details() → ⚠️ no date/branch filter
```

---

## Flow 7: Ledger Balance Alert

**Trigger:** Page load on dashboard case → `check_LedgerBalanceAlert()` fires.

**JS:** `check_LedgerBalanceAlert()` → AJAX to `get_LedgerBalanceAlert`

**Controller:** `get_LedgerBalanceAlert()` L2088-2147
```
for each ledger with min_balance > 0:
  get LedgerReportData(id_ledger, from=opening_date, to=today, branch)
  calculate running balance from last date entries
  if current_balance < min_balance: add to alerts
```

**⚠️ Risk:** Complex nested loop — N ledgers × getLedgerReportData() per ledger. Can be slow with many monitored ledgers.

---

## JS Function Summary

| JS Function | Tab/Trigger | Calls To |
|---|---|---|
| `get_live_cockpit_dashboard_details()` | Cockpit tab / filter change | 13 AJAX endpoints |
| `sales_dashboard_data()` | Sales tab | `get_saleschart_details` |
| `get_order_management_details()` | Order tab | 7 AJAX endpoints |
| `get_stockDetails()` | Stock chart tab | `get_stockchart_details` |
| `get_contract_pricing()` | Contract tab | `get_contract_approval` |
| `get_branch_order()` | Page load | `get_SaleBill_details` |
| `get_average_bill_value()` | Page load | `get_BillingStatus` |
| `get_ActiveMetals_dashboard()` | Page load | `get_BillingStatus` |
| `check_LedgerBalanceAlert()` | Page load | `get_LedgerBalanceAlert` |
| `get_cash_abstract_data()` | Cash abstract section | `get_cash_abstract_details` |
| `set_sale_dashboard()` | Sale GChart tab | `get_saleschart_details` |
| `set_estimation_table()` | Estimation sub-page | `get_estimation_details` |
| `sales_details()` | Page load | `get_stock_details` |

---

## Flow 8: API Dashboard Tabs (admin_ret_dashboard_api.php)

> These flows route through **`admin_ret_dashboard_api`** (REST controller), **not** the primary controller. The API model `ret_dashboard_api_model` handles all DB reads.

**Common filter pattern:** All API flows accept `from_date`, `to_date`, `id_branch[]`, `id_metal[]` via POST, processed through `get_profile_settings()` to apply `allow_bill_type` EDA filter.

### Flow 8a: Sales Overview Tab (API)

| AJAX URL | API Method | Model Method | Key Tables | Output |
|---|---|---|---|---|
| `admin_ret_dashboard_api/get_sales_glance` | `get_sales_glance_post()` | `get_dashboard_sales_glance()` | `ret_billing`, `ret_bill_details`, `ret_product_master`, `ret_billing_item_stones` | KPI: bills, GWT, NWT, amount, discount, return |
| `admin_ret_dashboard_api/get_top_sellers` | `get_top_sellers_post()` | `get_top_selling()` + `get_top_sellers()` | `ret_billing`, `ret_bill_details`, `ret_taging`, `ret_karigar` | Top-5 products + top-5 karigar |
| `admin_ret_dashboard_api/get_branch_comparison_details` | `get_branch_comparison_details_post()` | `get_store_sales()` | `ret_billing`, `branch` | Branch-wise sales % share pie chart |
| `admin_ret_dashboard_api/get_store_wise_sales` | `get_store_wise_sales_post()` | `get_section_sales()`, `get_employee_sales()`, `get_product_sales()`, `get_karigar_sales()` | `ret_billing`, `ret_section`, `employee`, `ret_karigar` | Multi-dimensional sales breakdown |

### Flow 8b: Monthly Sales Chart (API)

| AJAX URL | API Method | Model Method | Key Tables | Note |
|---|---|---|---|---|
| `admin_ret_dashboard_api/get_monthly_sales_details` | `get_monthly_sales_details_post()` | `get_monthly_sales()` | `ret_billing`, `ret_bill_details`, `ret_financial_year` | ⚠️ N×12 queries anti-pattern |

### Flow 8c: Karigar Stock View (API)

| AJAX URL | API Method | Model Method | Key Tables |
|---|---|---|---|
| `admin_ret_dashboard_api/get_karigar_stock` | `get_karigar_stock_post()` | `get_karigar_stock()`, `get_section_stock()`, `get_product_stock()` | `ret_taging`, `ret_karigar`, `ret_section`, `ret_product_master`, `ret_taging_stone` |

### Flow 8d: Financial Overview (API)

| AJAX URL | API Method | Model Method | Key Tables | Note |
|---|---|---|---|---|
| `admin_ret_dashboard_api/get_Financial_year` | `get_Financial_year_get()` | `get_financial_year()` | `ret_financial_year` | GET method only |
| `admin_ret_dashboard_api/get_delayed_po_payments` | `get_delayed_po_payments_post()` | `get_delayed_po_payments()` | `ret_purchase_order`, `ret_karigar`, `ret_crdr_note`, `ret_supplier_rate_cut`, `ret_karigar_metal_issue`, `ret_po_bill_payment_details`, `ret_po_payment` | UNION of unpaid+rate-cut records |
| `admin_ret_dashboard_api/get_today_delivery_po_payments` | `get_today_delivery_po_payments_post()` | `get_today_delivery_po_payments()` | Same as above + `po_delivery_date=CURDATE()` filter | Today-due unpaid POs |
| `admin_ret_dashboard_api/get_delayed_purchase_orders` | `get_delayed_purchase_orders_post()` | `get_delayed_purchase_orders()` | `customerorder`, `customerorderdetails`, `ret_karigar`, `ret_product_master` | Karigar job orders overdue |
| `admin_ret_dashboard_api/get_creditdebit` | `get_creditdebit_post()` | `get_crdr_details()` | `ret_crdr_note`, `ret_karigar` | Cr/Dr note summary |
| `admin_ret_dashboard_api/get_rate_fixed` | `get_rate_fixed_post()` | `get_rate_fixed_details()` | `ret_po_rate_fix`, `ret_purchase_order`, `ret_grn_entry`, `ret_karigar` | Rate-fixed PO summary |
| `admin_ret_dashboard_api/get_rate_unfixed` | `get_rate_unfixed_post()` | `get_rate_unfixing_details()` | `ret_purchase_order`, `ret_purchase_order_items`, `ret_karigar`, `ret_supplier_rate_cut` | Unhedged/unfixed balance stock |
| `admin_ret_dashboard_api/get_supplier_crde` | `get_supplier_crde_post()` | `getMetalwiseApprovalTransactionList()` | `ret_view_supplier_approval_ledger` (VIEW), `ret_karigar`, `metal` | Supplier approval ledger summary |
| `admin_ret_dashboard_api/get_accountstock_inwards` | `get_accountstock_inwards_post()` | `get_accountstock_inwards_details()` | `ret_branch_transfer`, `ret_brch_transfer_old_metal`, `ret_brch_transfer_tag_items`, `ret_bt_order_log` | ⚠️ BROKEN — undefined `$id_category` + `$data` |
| `admin_ret_dashboard_api/get_supplier_transcation` | `get_supplier_transcation_post()` | `get_vendor_payment()` | `ret_po_payment_detail`, `ret_po_payment`, `ret_karigar`, `bank` | Vendor payment cash/NB breakdown |
| `admin_ret_dashboard_api/get_rate_cut_profit_loss` | `get_rate_cut_profit_loss_post()` | `get_rate_cut_profit_loss()` | `ret_supplier_rate_cut`, `metal_rates` | ⚠️ BROKEN — `to_date` ignored |
| `admin_ret_dashboard_api/get_qc_details` | `get_qc_details_post()` | `get_qc_details()` | `ret_purchase_order_items`, `ret_po_qc_issue_details`, `ret_po_qc_issue_process` | QC failure summary |

---

## Cross-Model Data Flow Note

**`ret_reports_model::getBillDetails()` — used in Flow 4 (Cash Abstract)**

```
Controller: get_cash_abstract_details() [admin_ret_dashboard.php L1702]
    ↓ loads: $this->ret_reports_model
    ↓ calls: getBillDetails($POST_data)   [ret_reports_model.php — cross-module]
    ↓ returns: bills[], returns[], purchases[], advances[], payments[], credits[]
    ↓ PHP aggregation loop (no further DB calls)
    ↓ returns JSON: dash_cash_abstarct_details (25+ computed fields)
```

**Also called by:** `get_LedgerBalanceAlert()` [L2112] attempted to call `getLedgerReportData()` — **this method does not exist in `ret_reports_model.php` → Fatal PHP error (Bug #5)**
