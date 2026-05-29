# MODULE BRAIN — Retail Dashboard
> **Brain built:** 2026-03-16 | **Version:** 1.4 | **Builder:** Antigravity | **Last updated:** Round 16 (2026-03-25) | **Status:** ✅ COMPLETE

---

## 1. Module Overview

**Purpose:** Aggregated analytics and operations cockpit for jewelry retail managers. Displays real-time KPIs for sales, billing, estimation, stock, orders, credits, and cash flow across branches.

**Module type:** Read-only analytics dashboard (no CREATE/UPDATE/DELETE flows — all data is fetched and rendered as charts/tables)

### File Map
| File | Lines | Bytes | Purpose |
|---|---|---|---|
| `admin/application/controllers/admin_ret_dashboard.php` | 2,151 | 46 KB | **Primary controller** — **39 AJAX handlers** for live cockpit, orders, stock, credits. Last method at L2088. |
| `admin/application/controllers/admin_ret_dashboard_api.php` | 2,196 | 49 KB | **⚠️ Secondary REST controller** (extends REST_Controller) — **52 methods** (sales/purchase analytics + MD Approval Dashboard). CORS wildcard header. Uses `_post`/`_get` URL suffix convention. |
| `admin/application/models/ret_dashboard_model.php` | 5,271 | 182 KB | Primary model — 97 methods, complex SQL readers across 48 tables |
| `admin/application/models/ret_dashboard_api_model.php` | 2,721 | 109 KB | **⚠️ Secondary model** for API controller — 45 methods; serves sales glance, monthly, karigar stock, store sales, purchase/QC/rate-fix/breakeven, and MD Approval Dashboard widgets. |
| `admin/application/models/ret_dashboard_api_model_backup.php` | — | — | Backup copy — verify for divergence before modifying API model |
| `admin/assets/js/ret_dashboard.js` | 19,383 | 298 KB | Main JS — tab switching, AJAX calls, chart rendering (Google Charts, Chart.js, Morris.js) |
| `admin/application/views/ret_dashboard/` | 1 subdir | — | View templates — `reports/` subdir only |
| `admin/application/views/ret_dashboard/reports/live_estimation.php` | 134 | 5.3 KB | **Only view file.** Estimation report page: date-range picker (spans `#estimation1`/`#estimation2`), branch filter (`branch_settings=1` guard), type filter (0=All/1=Purchase/2=Sales/3=Sales+Purchase), DataTable `#estimation_list` shell (10 cols: S.No, Customer, Type, Sale Wt, Sale Amt, Pur Wt, Pur Amt, Chit UTI, Gift Voucher UTI, Discount, Net Amt). No PHP data embedding — all data loaded via AJAX. |

### Connection Flow
```
Browser → JS (ret_dashboard.js)
       ├─→ AJAX POST → Controller (admin_ret_dashboard.php)   [cockpit/orders/stock tab]
       │              → Model (ret_dashboard_model.php)
       │              → DB (read from 48+ tables)
       │              → JSON response
       └─→ AJAX POST → Controller (admin_ret_dashboard_api.php) [sales analytics/purchase tab]
                      → Model (ret_dashboard_api_model.php)
                      → DB
                      → JSON response
       → JS renders charts/tables via Google Charts / Chart.js / Morris.js / jQuery DataTables
```

---

## 2. Constructor

**admin_ret_dashboard.php (primary controller):**
| Model/Library | Purpose |
|---|---|
| `ret_dashboard_model` | Primary model — all dashboard data methods |
| `ret_reports_model` | Secondary model — `get_cash_abstract_details()`, `gross_profit_report()`, `get_LedgerBalanceAlert()` |

**admin_ret_dashboard_api.php (REST controller — Sales Analytics/Purchase tab + MD Approval Dashboard):**
| Model/Library | Purpose |
|---|---|
| `ret_dashboard_api_model` | API model — top-selling, monthly sales, karigar stock, store/section/employee sales, MD approval |
| `ret_catalog_model` | Sub-model (const RET_CAT_MODEL) |
| `ret_purchase_order_model` | Sub-model (const RET_PUR_MODEL) |
| `ret_reports_model` | Sub-model (const RET_REP_MODEL) |
| `ret_purchase_approval_model` | **NEW (Round 16)** — loaded in `_send_vendor_email()` — provides `get_karigar_details()`, `save_email_log()`, `update_email_log()`, `get_karigar_order_details()` |
| `email_model` | **NEW (Round 16)** — loaded in `_send_vendor_email()` — provides `send_email()` with embedded image support |

**Session Gate:**
- `is_logged` — redirect to `admin/login` if not set
- `access_time_from` / `access_time_to` — enforces time-window access; redirects to `chit_admin/logout` if outside window

**Loaded Libraries:** None explicitly beyond CI base
**Loaded Helpers:** None

---

## 3. Entry Points (Route Table)

> All methods output `echo json_encode($data)` unless noted. HTTP method: POST (all AJAX), except `get_estimation()` and `gross_profit_report('list')` which are GET page loads.

| Controller Method | Lines | Type | Purpose |
|---|---|---|---|
| `index()` | L57-61 | GET | Empty stub — no content rendered |
| `get_EstimationStatus()` | L67-85 | AJAX/POST | Estimation count: created/sold/unsold by date+branch |
| `get_BillingStatus()` | L89-109 | AJAX/POST | Billing by metal (wt/amt/pcs) + MRP + Diamond data |
| `get_stock_details()` | L113-129 | AJAX/POST | Available gold/silver stock movements |
| `get_GreentagSalesDetails()` | L133-149 | AJAX/POST | Green-tag sales: weight, amount, incentive calc |
| `get_VitrualTagStatus()` | L153-169 | AJAX/POST | Home-sale & tag-split billing summary |
| `get_SalesReturnDetails()` | L173-189 | AJAX/POST | Sales returns weight+pieces |
| `get_old_metal_purchase()` | L195-211 | AJAX/POST | Old metal purchases by metal type |
| `get_CreditSalesDetails()` | L215-231 | AJAX/POST | Credit bills outstanding + credit received |
| `get_GiftVoucherDetails()` | L237-253 | AJAX/POST | Gift card issued/utilized/sold amounts |
| `get_BillClassficationDetails()` | L257-273 | AJAX/POST | New vs old customer bill counts and amounts |
| `get_BranchTransferDetails()` | L277-293 | AJAX/POST | Branch transfer approval/download pending counts |
| `get_lot_tag_details()` | L297-313 | AJAX/POST | Lot inward pcs/wt + tagging pcs/wt |
| `get_OrderDetails()` | L317-333 | AJAX/POST | Online order counts (placed/received/in-cart) |
| `get_StockDetails()` | L339-355 | AJAX/POST | Available gold stock (opening/inward/sold/br_out) |
| `get_silver_StockDetails()` | L359-375 | AJAX/POST | Available silver stock (opening/inward/sold/br_out) |
| `get_ReorderDetails()` | L381-397 | AJAX/POST | Items that have fallen below reorder minimum |
| `get_KarigarOrderDetails()` | L401-419 | AJAX/POST | Karigar orders by status (T/TM/TODDY_PENDING/OVER_DUE/WIP) |
| `get_customerOrderDetails()` | L423-435 | AJAX/POST | Customer order status summary (received/allocated/pending/ready/delivered) |
| `get_MetalStockDetails()` | L439-453 | AJAX/POST | Tag stock + NonTag stock by metal type |
| `get_CustomerDetails()` | L457-473 | AJAX/POST | Last 10 customers (ignores id_branch param!) |
| `get_RecentBillDetails()` | L477-493 | AJAX/POST | Recent bills with bill type labels |
| `getEstimationDetails()` | L519-535 | AJAX/POST | Estimation list by branch/date |
| `get_retail_dashboard_details()` | L539-623 | AJAX/POST | **LEGACY mega-method** — batch fetches 15+ dashboard widgets in one call (no id_branch passed to most sub-calls) |
| `get_SaleBill_details()` | L698-882 | AJAX/POST | Category-wise, payment-wise, metal-wise sales by branch |
| `get_order_data()` | L885-931 | AJAX/POST | Order counts by type (catalog/custom/repair) per branch |
| `get_estimation()` | L935-943 | GET/PAGE | Loads estimation report view template |
| `get_estimation_details()` | L947-1001 | AJAX/POST | Estimation detail list (with type-based field toggling: 0=all, 1=purchase, 2=sales) |
| `ajax_lot_data()` | L1005-1049 | AJAX/POST | Lot inward gross/net wt total + branch-wise breakdown |
| `ajax_tag_data()` | L1051-1093 | AJAX/POST | Tag gross/net wt total + branch-wise breakdown |
| `get_sales_details()` | L1097-1135 | AJAX/POST | Sales bill count per branch |
| `get_MetalBill_details()` | L1139-1153 | AJAX/POST | Metal-wise sales count + amount by branch (no date filter!) |
| `get_CreditDetils()` | L1161-1179 | AJAX/POST | Credit bill details list |
| `get_PendingDueDetails()` | L1185-1197 | AJAX/POST | Pending due amounts |
| `get_metal_stock_details()` | L1209-1225 | AJAX/POST | Stock by category for branch |
| `get_branch_transfer_details()` | L1229-1245 | AJAX/POST | Branch transfer summary (ignores date/branch params!) |
| `get_new_customer()` | L1257-1273 | AJAX/POST | New customers by area/branch |
| `get_customer_order_details()` | L1283-1303 | AJAX/POST | Customer order list + details |
| `get_karigar_order_details()` | L1307-1325 | AJAX/POST | Karigar order details by date/branch |
| `get_saleschart_details()` | L1335-1443 | AJAX/POST | Sales summary KPIs (estimation, green tag, credit, customer visits) + branch/mode-wise charts |
| `get_stockchart_details()` | L1449-1489 | AJAX/POST | Stock summary per branch + branch transfer |
| `get_cash_deposit_details_branchwise()` | L1492-1514 | AJAX/POST | Cash deposit and opening balance per branch |
| `get_approval()` | L1518-1532 | AJAX/POST | Contract price + branch transfer approval counts |
| `get_contract_approval()` | L1538-1550 | AJAX/POST | Contract pricing approval status |
| `gross_profit_report($type)` | L1552-1571 | GET+AJAX | GP report page load (type=list) and data (type=ajax) |
| `get_pendingorderDetails()` | L1576-1594 | AJAX/POST | Pending customer orders |
| `get_wiporderDetails()` | L1597-1615 | AJAX/POST | Work-in-progress orders |
| `get_dreadyorderDetails()` | L1617-1635 | AJAX/POST | Delivery-ready orders |
| `get_deliveredorderDetails()` | L1637-1655 | AJAX/POST | Delivered orders on date |
| `get_karigarreminderDetails()` | L1658-1676 | AJAX/POST | Karigar reminder notifications |
| `get_karigaroverdueDetails()` | L1679-1697 | AJAX/POST | Karigar overdue order details |
| `get_cash_abstract_details()` | L1702-2083 | AJAX/POST | **Complex cash abstract** — uses `ret_reports_model->getBillDetails()`, maps payment modes |
| `get_LedgerBalanceAlert()` | L2088-2147 | AJAX/POST | Ledger balance alert — calls `ret_reports_model->getLedgerReportData()` per ledger |

### 3b. API Controller Routes (admin_ret_dashboard_api.php — REST)

> This is a **separate** REST controller extending `REST_Controller`. Methods use `_post`/`_get` URL suffix (CodeIgniter REST lib convention). Called by JS AJAX as e.g. `admin_ret_dashboard_api/get_top_selling`. The JS route omits the `_post` suffix; the REST lib dispatches by HTTP verb automatically.

| Controller Method | HTTP | JS File Line | Model Method Called | Purpose |
|---|---|---|---|---|
| `get_Sales_glance_post()` | POST | JS L12181 | `get_dashboard_sales_glance()` | Sales glance KPI |
| `get_top_selling_post()` | POST | JS L12953 | `get_top_selling()` | Top selling products by bill count |
| `get_top_sellers_post()` | POST | JS L13093 | `get_top_sellers()` | Top karigar sellers by bill count |
| `get_monthly_sales_post()` | POST | JS L13219 | `get_monthly_sales()` | MoM sales comparison line chart |
| `get_monthly_sales_app_post()` | POST | Mobile only | `get_monthly_sales_mobile()` | Mobile-formatted MoM sales |
| `get_financial_year_get()` | GET | JS L13329 | `get_financial_year()` | FY dropdown options |
| `get_branch_comparison_post()` | POST | JS L13435 | `get_store_sales()` | Branch-wise sales column chart |
| `get_branch_compare_post()` | POST | Mobile only | `get_store_sales()` | Mobile-formatted branch comparison |
| `get_store_sales_post()` | POST | JS L13669 | `get_store_sales()` | Store-wise sales % progress bar table |
| `get_product_sales_post()` | POST | JS L14159 | `get_product_sales()` | Product-wise sales chart (top 10) |
| `get_branch_avg_va_post()` | POST | JS L13867 | `get_branch_wastage()` ⚠️ #21 | Branch VA/wastage analysis |
| `get_employee_sales_post()` | POST | JS L14733 | `get_employee_sales()` | Employee-wise sales pie chart |
| `get_section_sales_post()` | POST | JS L14445 | `get_section_sales()` | Section-wise sales pie chart |
| `get_karigar_sales_post()` | POST | JS L15015 | `get_karigar_sales()` | Karigar-wise sales chart |
| `get_product_stock_post()` | POST | JS L15551 | `get_product_stock()` | Product-wise stock pie chart |
| `get_section_stock_post()` | POST | JS L15887 | `get_section_stock()` | Section-wise stock table |
| `get_karigar_stock_post()` | POST | JS L16221 | `get_karigar_stock()` | Karigar-wise stock pie + table |
| `get_FinancialStatus_post()` | POST | JS L12805 | `get_dashboard_breakeven_details()` | Breakeven financial status |
| `get_crdr_details_post()` | POST | JS L17969 | `get_crdr_details()` | Purchase credit/debit balance |
| `get_qc_details_post()` | POST | JS L18031 | `get_qc_details()` | QC failed item metrics |
| `get_rate_fixed_post()` | POST | JS L18736 | `get_rate_fixed_details()` | Rate-fixed purchase wt/amt |
| `get_rate_unfixed_post()` | POST | JS L18804 | `get_rate_unfixing_details()` | Rate-unfixed supplier balance table |
| `get_supplier_crde_post()` | POST | JS L18934 | `ret_reports_model::getSupplierTransactionList()` | Supplier credit/debit summary |
| `get_accountstock_inwards_post()` | POST | JS L18994 | `get_accountstock_inwards_details()` ⚠️ #22 | Account stock inwards (GWT/NWT/DIA) |
| `get_supplier_transcation_post()` | POST | JS L19116 | `ret_reports_model::getSupplierTransactionList()` | Supplier transaction ledger |
| `get_delayed_purchase_orders_post()` | POST | JS L18234 | `get_delayed_purchase_orders()` | Delayed karigar PO tracker |
| `get_delayed_po_payments_post()` | POST | JS L18135 | `get_delayed_po_payments()` | POs with overdue payment |
| `get_today_delivery_po_payments_post()` | POST | JS L18193 | `get_today_delivery_po_payments()` | POs due for delivery today |
| `get_rate_cut_profit_loss_post()` | POST | JS L19252 | `get_rate_cut_profit_loss()` ⚠️ #23 | Rate-cut P&L analysis table |
| `get_purchase_inwards_post()` | POST | JS L17317 | `get_purchase_inwards()` | Purchase inwards GWT/NWT |
| `get_vendor_payment_post()` | POST | JS L17381 | `get_vendor_payment()` | Vendor payment summary |
| `get_outward_details_post()` | POST | JS L17543 | `get_outward_details()` | Purchase return + outward |
| `get_weight_gain_loss_post()` | POST | JS L17719 | `ret_reports_model::getLotwiseTaggedVault()` ⚠️ #27 | Weight gain/loss |
| `getMetalwiseApprovalTransaction_post()` | POST | JS L17781 | `getMetalwiseApprovalTransactionList()` | Supplier approval ledger |
| `get_custome_wise_sale_post()` | POST | JS L14029 | `get_custome_wise_sale()` | Customer-type wise sale |
| `getActiveMetals_get()` | GET | JS (filter init) | `ret_catalog_model::getActiveMetals()` | Active metal list |
| `get_EstimationStatus_post()` | POST | — | `get_dashboard_estimation()` | Estimation count widget |
| `get_VitrualTag_post()` | POST | — | `get_dashboard_virturaltag_details()` ⚠️ #26 | Virtual tag today only |
| `get_SalesReturn_post()` | POST | — | `get_dashboard_salesreturn_det()` ⚠️ #26 | Sales return today only |
| `get_LotDetails_post()` | POST | — | `get_dashboard_lot_tag_details()` ⚠️ #26 | Lot details today only |
| `get_CoverUpReport_post()` | POST | — | `get_cover_up_report()` ⚠️ #26 | Cover-up P&L today only |
| `get_design_stock_post()` | POST | — | `get_design_stock()` | Design stock chart |
| `get_sub_design_stock_post()` | POST | — | `get_sub_design_stock()` | Sub-design stock |
| `tag_details_post()` | POST | — | `ret_reports_model::getTaggeditems()` | Tag detail list |
| `save_po_dashboard_comment_post()` | POST | — | `save_po_dashboard_comment()` | Save comment on PO in dashboard |
| **🆕 NEW — Round 16 — MD Approval Dashboard** | | | | |
| `get_md_pending_orders_post()` | POST | MD Dashboard | `get_md_pending_orders()` | Fetch POs at order_status=10 (pending MD approval) with karigar, employee, products |
| `md_approve_order_post()` | POST | MD Dashboard | `md_approve_order()` | Approve single PO → status=11, sends vendor email via `_send_vendor_email()` |
| `md_reject_order_post()` | POST | MD Dashboard | `md_reject_order()` | Reject PO with reason → status=8 |
| `md_bulk_approve_orders_post()` | POST | MD Dashboard | `md_bulk_approve_orders()` | Bulk approve multiple POs, emails all vendors sequentially |
| `_send_vendor_email()` *(private)* | — | Internal | `ret_purchase_approval_model`, `email_model` | Sends HTML email to karigar with accept_url, logs to `ret_order_email_logs`; returns `['email_status'=>bool,'email_error'=>string]` |

---

## 4. Model Methods Summary

**Total model methods documented:** 97 (from `ret_dashboard_model.php` 5,271 lines — includes 2 true orphan methods not called from either controller)

See → [METHOD_INDEX.md](METHOD_INDEX.md) for complete alphabetical listing.

**Method group counts (primary model — ret_dashboard_model.php):**
- Billing queries: 14
- Stock/Tagging queries: 16
- Estimation queries: 5
- Order management queries: 12
- Credit/Payment queries: 7
- Customer/Branch queries: 8
- Chart/Analytics methods (GP, monthly sales, etc): 12
- Orphan/future methods (no active controller caller): 18
- Utility/Profile methods: 5
- Cross-model methods (ret_reports_model): 3

### 4b. API Model Methods — ret_dashboard_api_model.php (2,721 lines, 45 methods + backup file)

> ⚠️ This model was not documented until Round 5. All SQL queries use raw string interpolation — **full SQL injection risk on `$id_branch` and `$id_metal` in every method.**

| Method | Lines | Parameters | Returns | Purpose |
|---|---|---|---|---|
| `get_dashboard_sales_glance()` | L45-161 | from_date, to_date, id_branch[], id_metal[] | row_array | Sales KPI: bill count, GWT, amount, discount, return amt |
| `get_sales_return_details()` | L165-231 | from_date, to_date, id_branch[], id_metal[] | row_array | Internal helper for sales return pcs/cost |
| `get_top_selling()` | L233-277 | from_date, to_date, id_branch[], id_metal[] | result_array | Top 5 products by bill count + current stock |
| `get_top_sellers()` | L279-327 | from_date, to_date, id_branch[], id_metal[] | result_array | Top 5 karigar sellers by bill count + karigar stock |
| `get_monthly_sales()` | L329-475 | fy_year, id_branch[], id_metal[] | array | MoM sales NWT per branch for all 12 FY months (N+1 queries) |
| `get_monthly_sales_mobile()` | L477-582 | fy_year, id_branch[], id_metal[] | array | Mobile-formatted MoM sales (branch_data format) |
| `get_financial_year()` | L584-589 | — | result_array | FY list from `ret_financial_year` |
| `get_store_sales()` | L591-645 | from_date, to_date, id_branch[], id_metal[] | result_array | Branch sales with % share; adds COLOUR_CODE |
| `get_branch_sales()` | L647-661 | from_date, to_date, id_branch | result_array | Simple branch-wise sales (orphan — no API controller caller) |
| `get_branch_wastage()` | L666-693 | from_date, to_date, id_branch[], group_by | result_array | **BUGGY** — uses `$id_metal` but it is NOT in function signature |
| `get_product_sales()` | L696-738 | from_date, to_date, id_branch[], id_metal[] | result_array | Product-wise sales sorted by total |
| `get_employee_sales()` | L740-792 | from_date, to_date, id_branch[], id_metal[] | result_array | Employee-wise sales (via estimation → employee join) |
| `get_section_sales()` | L795-834 | from_date, to_date, id_branch[], id_metal[] | result_array | Section-wise sales grouped by `id_section` |
| `get_karigar_sales()` | L836-875 | from_date, to_date, id_branch[], id_metal[] | result_array | Karigar-wise sales via lot → karigar join |
| `get_karigar_stock()` | L877-939 | id_branch[], id_metal, id_karigar, group_by, id_product | result_array | Karigar stock with optional individual karigar filter and sub-stone query |
| `get_section_stock()` | L942-987 | id_branch[], id_metal[], id_section | result_array | Section-wise stock analysis |
| `get_product_stock()` | L988-1043 | id_branch[], id_metal[], id_product | result_array | Product-wise stock analysis |
| `get_custome_wise_sale()` | L1045-1128 | from_date, to_date, id_branch[], id_metal[] | array | 3-segment: Regular/New/Chit customer counts |
| `get_dashboard_estimation()` | L1131-1176 | from_date, to_date, id_branch | row_array | Estimation created/sold/unsold/old_gold/old_silver counts |
| `get_dashboard_virturaltag_details()` | L1178-1212 | from_date, to_date, id_branch | row_array | Home-sale and partly-sold tag counts |
| `get_dashboard_salesreturn_det()` | L1214-1240 | from_date, to_date, id_branch | row_array | Sales return GWT/NWT by metal |
| `get_dashboard_lot_tag_details()` | L1242-1265 | from_date, to_date, id_branch | row_array | Lot inward pcs/wt + tagged gold/silver pcs/wt |
| `get_cover_up_report()` | L1268-1414 | from_date, to_date, id_branch, id_metal | array | Complex P&L: sales - return - old metal ± chit ± GRN vs cover-up log |
| `get_purchase_inwards()` | L1418-1447 | from_date, to_date, id_branch, id_metal[] | row_array | Approved PO inwards GWT/NWT/pure-wt/dia-wt |
| `get_delayed_purchase_orders()` | L1449-1489 | from_date, to_date, id_branch, id_metal | result_array | Delayed karigar POs sorted by delay days |
| `get_delayed_po_payments()` | L1491-1598 | from_date, to_date, id_branch, id_metal | result_array | Overdue PO payments (UNION of 2 queries) |
| `get_today_delivery_po_payments()` | L1600-1677 | from_date, to_date, id_branch, id_metal | result_array | POs due today — unpaid (UNION of 2 queries) |
| `get_vendor_payment()` | L1679-1699 | from_date, to_date, id_branch | result_array | Vendor payment summary (cash/NB/total) |
| `get_outward_details()` | L1702-1804 | from_date, to_date, id_branch, id_metal[] | result_array | Purchase return + B2B sales + metal issue outwards |
| `getMetalwiseApprovalTransactionList()` | L1806-1875 | from_date, to_date, id_branch | result_array | Supplier approval ledger (uses `ret_view_supplier_approval_ledger` VIEW) |
| `get_crdr_details()` | L1878-1901 | from_date, to_date, id_branch | row_array | Cr/Dr note summary from `ret_crdr_note` |
| `get_qc_details()` | L1903-1927 | from_date, to_date, id_branch, id_metal[] | row_array | QC failed pcs/GWT/NWT from purchase order QC process |
| `get_dashboard_breakeven_details()` | L1929-2060 | from_date, to_date, id_branch, id_metal, rep_type, fin_year | array | Breakeven: daily target × days vs actual GWT |
| `get_rate_fixed_details()` | L2062-2128 | from_date, to_date, id_branch | array | Rate-fixed wt/rate/amt (GRN + rate-cut, merged) |
| `get_rate_unfixing_details()` | L2130-2220 | from_date, to_date, id_branch, id_metal[] | array | Rate-unfixed balance (UNION of 2 queries, karigar-keyed) |
| `get_accountstock_inwards_details()` | L2223-2519 | from_date, to_date, id_branch, id_metal | result_array | **BUGGY** — uses undefined `$id_category` var in all 7 sub-queries. `$data` array also undefined. |
| `get_rate_cut_details()` | L2522-2551 | from_date, to_date, id_branch, id_metal | row_array | Rate-cut aggregate for date range |
| `get_rate_cut_profit_loss()` | L2568-2646 | from_date, to_date, id_branch | result_array | **BUGGY** — SQL uses `from_date` as upper bound, ignores `to_date` entirely |
| `get_profile_settings()` | L2647-2651 | id_profile | row_array | Utility: fetch profile row |
| `get_design_stock()` | ~L2650+ | id_branch, id_product | result_array | Design-wise stock analysis (discovered Round 10) |
| `get_sub_design_stock()` | ~L2700+ | id_branch, id_product, id_design | result_array | Sub-design stock breakdown (discovered Round 10) |
| **🆕 NEW — Round 16 — MD Approval Dashboard** | | | | |
| `get_md_pending_orders()` | L2655-2690 | — | result_array | Fetches `customerorder` WHERE `order_status=10` with karigar name, employee, products (via GROUP_CONCAT), due date, and reorder source label |
| `md_approve_order()` | L2692-2697 | id_customerorder | bool | Updates `customerorder.order_status` → 11 (approved). Returns affected_rows > 0. |
| `md_reject_order()` | L2699-2704 | id_customerorder, reason | bool | Updates `customerorder.order_status` → 8, sets `reject_reason`. |
| `md_bulk_approve_orders()` | L2706-2711 | order_ids[] | int | Bulk updates `customerorder.order_status` → 11. Returns count of affected rows. |
| `save_po_dashboard_comment()` | L2713-2718 | po_id, comment | bool | Updates `ret_purchase_order.dashboard_comment`. Returns affected_rows >= 0. |

---

## 5. Data Flow Summary

**Primary pattern:** This is a **read-only dashboard** — no flow has CREATE/UPDATE/DELETE.

**Flow 1: Dashboard Widget Load (typical)**
1. JS function (e.g., `get_live_cockpit_dashboard_details()`) fires on tab click
2. AJAX POST to controller method with `from_date`, `to_date`, `id_branch`
3. Controller calls 1-N model methods
4. Model runs raw SQL query against DB
5. JSON returned and JS renders to DOM

**Flow 2: Sales Chart Tab**
1. `sales_dashboard_data(from_date, to_date)` fires
2. Calls `get_saleschart_details` → aggregates: estimation, green tag, credit, customer visit, branch sales, pay mode sales
3. Renders Google Charts + KPI cards

**Flow 3: Cash Abstract**
1. `get_cash_abstract_details()` fires
2. Controller uses `ret_reports_model::getBillDetails()` (cross-module)
3. Loops through arrays of items, returns, advances, payments, credits
4. PHP-level aggregation (no PHP-native rounding standardization)

See → [DATA_FLOW.md](DATA_FLOW.md) for detailed JS → controller → model traces.

---

## 6. Key DB Tables

| Table | Purpose | Owner Module |
|---|---|---|
| `ret_estimation` | Estimation header | Estimation |
| `ret_estimation_items` | Estimation line items | Estimation |
| `ret_billing` | Bill header | Billing |
| `ret_bill_details` | Bill line items | Billing |
| `ret_billing_payment` | Bill payment modes | Billing |
| `ret_billing_advance` | Order/General advance on bills | Billing |
| `ret_billing_chit_utilization` | Chit adjustments on bills | Billing |
| `ret_billing_gift_voucher_details` | Gift voucher on bills | Billing |
| `ret_billing_item_stones` | Stone details on bill items | Billing |
| `ret_bill_old_metal_sale_details` | Old metal purchased during billing | Old Metal |
| `ret_bill_return_details` | Items returned | Billing |
| `ret_taging` | Jewelry tag master | Tagging |
| `ret_taging_status_log` | Tag lifecycle state transitions | Tagging |
| `ret_nontag_item` | Non-tagged inventory | Inventory |
| `ret_lot_inwards` | Lot inward header | Lot |
| `ret_lot_inwards_detail` | Lot inward detail lines | Lot |
| `ret_branch_transfer` | Branch transfer header | Branch Transfer |
| `ret_product_master` | Product catalog | Product |
| `ret_category` | Product categories | Product |
| `ret_design_master` | Design definitions | Product |
| `ret_reorder_settings` | Reorder level config per product/branch | Inventory |
| `ret_settings` | Settings key-value store | Admin |
| `metal` | Metal master (Gold=1, Silver=2) | Global |
| `metal_rates` | Daily metal rate updates | Finance |
| `branch` | Branch master | Global |
| `customer` | Customer master | CRM |
| `customerorder` | Customer order header | Orders |
| `customerorderdetails` | Order line items | Orders |
| `order_cart` | Shopping cart / online orders | Ecom |
| `gift_card` | Gift card master | Finance |
| `ret_issue_receipt` | General advance/expense receipts | Finance |
| `ret_issue_rcpt_payment` | Payment modes for receipts | Finance |
| `ret_issue_credit_collection_details` | Credit collection line items | Finance |
| `ret_wallet_transcation` | Wallet/advance adjustments | Finance |
| `ret_weight` | Weight range master | Product |
| `ret_uom` | Unit of measure master | Product |
| `ret_stone` | Stone master | Product |
| `ret_size` | Size master | Product |
| `ledger_master` | Ledger accounts (⚠️ actual table name — not `ledger`) | Finance |
| `payment` / `payment_mode_details` | Chit payment entries | Chit |
| `scheme_account` / `scheme` | Chit scheme accounts | Chit |
| `ret_bank_deposit` | Cash bank deposit records | Finance |
| `ret_karikar_items_wastage` | Karigar contract price items for approval | Karigar |
| `ret_karigar` | Karigar master | Karigar |
| `ret_section` | Section/display section master | Product |
| `employee` | Employee master (staff) | HR |
| `profile` | User profile settings (EDA/allow_bill_type) | Admin |
| `order_status_message` | Order status display labels | Orders |
| `joborder` | Karigar job order sub-table | Orders |
| `ret_nontag_receipt` | Non-tag receipt records (LOT tagging fallback) | Lot |
| `village` | Village/area master for customer zoning | CRM |
| `ret_day_closing` | Day closing entry (EDA/billing filter) | Finance |
| `ret_branch_floor_counter` | Branch floor counter (counter filter) | Billing |
| `ret_billing_chit_utilization` | Chit scheme adjustments on bills | Chit |
| `ret_taging_stone` | Stone detail per tag | Tagging |
| `ret_lot_inwards_stone_detail` | Stone detail per lot inward | Lot |
| `ret_purchase_order` | Karigar purchase orders | Karigar |
| `ret_purchase_order_items` | Purchase order line items | Karigar |
| `ret_design_weight_range_wc` | **NEW (Round 16)** Weight class/range config for order items | Product |
| `ret_order_email_logs` | **NEW (Round 16)** Email audit trail for MD Approval vendor emails | Orders |

**API Model Additional Tables (discovered Round 5):**
| Table | Purpose | Owner Module |
|---|---|---|
| `ret_billing_item_stones` | Stone details per bill line item | Billing |
| `ret_purity` | Purity master (e.g., 91.6%, 99.9%) | Product |
| `ret_view_supplier_approval_ledger` | **VIEW** — supplier approval ledger denorm | Purchase |
| `ret_breakeven_logs` | Daily breakeven target log | Finance |
| `ret_cover_up` | Hedging/cover-up position log | Finance |
| `ret_po_rate_fix` | Rate-fix entries for purchase orders | Karigar |
| `ret_grn_entry` | GRN (goods received) entry header | Karigar |
| `ret_po_stone_items` | Stones attached to PO items | Karigar |
| `ret_crdr_note` | Credit/debit note adjustments | Finance |
| `ret_purchase_return` | Purchase return header | Karigar |
| `ret_purchase_return_items` | Purchase return line items | Karigar |
| `ret_purchase_return_stone_items` | Stone items in purchase returns | Karigar |
| `ret_supplier_rate_cut` | Supplier rate-cut records | Karigar |
| `ret_karigar_metal_issue` | Metal issue to karigar header | Karigar |
| `ret_karigar_metal_issue_details` | Metal issue line items | Karigar |
| `ret_po_bill_payment_details` | PO payment allocation per bill | Finance |
| `ret_po_payment` | PO payment header | Finance |
| `ret_po_payment_detail` | PO payment mode detail | Finance |
| `ret_po_qc_issue_details` | QC issue line items | Karigar |
| `ret_po_qc_issue_process` | QC issue process header | Karigar |
| `ret_brch_transfer_old_metal` | Old metal in branch transfer | Branch Transfer |
| `ret_brch_transfer_tag_items` | Tag items in branch transfer | Branch Transfer |
| `ret_bt_order_log` | Order log entries in branch transfer | Branch Transfer |
| `metal_rates` | Daily market gold/silver rates | Finance |
| `bank` | Bank master | Finance |
| `chit_settings` | Chit scheme global settings | Chit |
| `ret_nontag_item` | Non-tagged item (used in BT nontag flows) | Inventory |
| `customerorderdetails` | Customer order details (repair/catalog) | Orders |
| `ret_bill_return_details` | Bill-level return mapping | Billing |
| `ret_estimation_old_metal_sale_details` | Old metal trade-in on estimation | Estimation |

---

## 7. Known Risks

### 🔴 CRITICAL
1. **SQL Injection throughout model** — All filter parameters (`$id_branch`, `$from_date`, `$to_date`) are concatenated directly into SQL strings without using CI's query builder or prepared statements. Every model method is vulnerable.
2. **`get_CustomerDetails()` ignores `id_branch`** — Method receives `$id_branch` but the query has no branch filter (L469/524). Always returns all-branch last 10 customers.
3. **`get_branch_transfer_details()`** — Controller receives date/branch params (L1235-1239) but passes none to model (L1241: `get_branch_transfer_details()` with no params). Model ignores them.
4. **`get_MetalBill_details()`** — Has no date filter. Always shows lifetime metal bill data regardless of date range.
5. **`getLedgerReportData()` does not exist in `ret_reports_model`** — Controller `get_LedgerBalanceAlert()` L2112 calls `$this->ret_reports_model->getLedgerReportData($post_data)` but this method is **absent** from `ret_reports_model.php` (37,325 lines searched — no match). Every load of the Ledger Balance Alert widget will produce a PHP fatal error. **This widget is currently broken in production.**

### 🟡 MEDIUM
6. **`get_retail_dashboard_details()` legacy mega-method** — Calls most model methods without `$id_branch`, producing branch-unfiltered data. This method appears to be superseded by individual AJAX methods but still active.
7. **Division by zero** — `get_saleschart_details()` L1391: `$estimation['tot_tag_sales']` used as divisor without null/zero check. Crashes if no tagged sales exist.
8. **`get_BillClassficationDetails()` broken** — Both queries (new customer and old customer) use identical SQL with `AND NOT EXISTS(...)` so `oldcusbills` count always = `newcusbills` count. The old customer logic is copy-paste bug.
9. **Cash abstract dead model method** — `get_dashboard_cash_abstarct_details()` in model (L672-1295, 624 lines) is entirely bypassed — `get_cash_abstract_details()` controller (L1702+) uses `ret_reports_model` instead. Dead code never called.
10. **`$_POST` direct access** — `get_customerOrderDetails()` L431 uses `$_POST['from_date']` and `$_POST['to_date']` directly instead of `$this->input->post()`.
11. **Karigar orders model reads `$_POST` directly** — `karigar_orders()` model method (L1695) calls `$this->input->post('id_branch')` from inside the model layer — bypasses controller variable.

### 🟡 MEDIUM (Round 2 findings)
11. **`get_new_customer()` ignores branch filter** — model L3556: method signature accepts `$id_branch` but the SQL at L3570 does NOT include a WHERE branch clause — always returns all-branch customer data.
12. **Undefined variable PHP fatal** — `get_store_sales()` model L4851: `number_format($number, ...)` where `$number` is never defined in this scope — will throw PHP `Undefined variable: $number` on any call.
13. **Duplicate array key overwrites data** — `get_stock_category_details()` L3484-3486 and `get_branch_stock_details()` L4318-4320: both methods define `'available_gwt'` key twice in the return array — the second definition (actually `available_nwt` formula) silently overwrites the first, so the API returns `available_nwt` value in the `available_gwt` field. Stock weight totals will always be wrong for any caller.
14. **Wrong table name in `getLedgerBalanceAlertData`** — model L5253: queries `ledger_master` but the CROSS_MODULE_MAP documents this as `ledger`. Verify actual table name in production DB.

### 🟡 MEDIUM (Round 3 findings)
15. **`get_MetalStockDetails()` JS passes no date params** — JS L5697: only sends `id_branch` (no `from_date`/`to_date`). Controller method `get_MetalStockDetails()` reads `$from_date` = `$this->input->post('from_date')` which will be empty — model query has no date filter applied. Shows all-time metal stock regardless of selected date range.
16. **`data.dash_cash_abstarct_details` JS typo** — JS L7461: uses `cash_abstarct` (note: `abstarct` not `abstract`) in the legacy `get_retail_dashboard_details()` response handler. The same typo exists in the controller (L1702 PHP array key). As long as both sides use the same typo, it works — but any future refactor attempting to fix the spelling on one side only will break the binding.

### 🟢 LOW
17. **No output buffering** — All responses directly `echo json_encode()` without Content-Type headers.
18. **`COLOUR_CODE` constant** — Defined in **3 places** (primary model PHP, API model PHP, JS) — 3-way sync required; any update to colour palette must be applied in all three files.
19. **`get_retail_dashboard_details()` passing `$from_date` unformatted** — L549/550: passes raw string (not date-formatted) to model methods while modern individual methods use `$this->input->post()`.
20. ~~**18 orphan model methods**~~ — **CORRECTED in Round 5:** These methods (`get_top_selling`, `get_monthly_sales`, `get_employee_sales`, etc.) were NOT orphans — they live in `ret_dashboard_api_model.php` and ARE called from `admin_ret_dashboard_api.php`. Bug #20 is **closed** (false positive).

### 🔴 CRITICAL (Round 5 findings — API Model)
21. **`get_branch_wastage()` missing `$id_metal` param** — `ret_dashboard_api_model.php` L666: method signature is `get_branch_wastage($from_date, $to_date, $id_branch, $group_by)` but the SQL at L687 uses `$id_metal` in WHERE clause — this variable is never declared in scope. PHP undefined variable. Metal filter silently skipped; all branches/metals always returned.
22. **`get_accountstock_inwards_details()` undefined variables** — `ret_dashboard_api_model.php` L2223: function uses `$id_category` (undefined — zero uses in function scope) and `$data['bt_code']`/`$data['id_branch']` (undefined `$data` array) throughout all 7 sub-queries. Will produce PHP notices and apply no bt_code/category filters — returns unfiltered data.
23. **`get_rate_cut_profit_loss()` inverted date filter** — `ret_dashboard_api_model.php` L2632: SQL WHERE clause is `DATE(src.date_add) <= '{$from_date}'` — the `to_date` parameter is accepted but **never used**. Only returns rate-cuts up to `from_date`, effectively showing data for historical dates only. The "to" window is ignored entirely.

### 🟡 MEDIUM (Round 5 findings)
24. **N+1 query anti-pattern in `get_monthly_sales()`** — `ret_dashboard_api_model.php` L377-457: For each of 12 months the code fires an individual DB query for **each branch** in a nested loop. With N branches, this generates 12×N queries per request. A multi-tenant 5-branch system fires 60 queries per chart render. Should be replaced with a single GROUP BY `id_branch, MONTH` query.

### 🔴 CRITICAL (Round 10 findings — API Controller Deep Scan)
25. **Wildcard CORS header** — `admin_ret_dashboard_api.php` L3: `Access-Control-Allow-Origin: *` — any external origin can call all API endpoints. Combined with SQL injection (Risk #1), enables full DB exfiltration from a malicious page.
26. **4 API methods hardcode today's date, ignoring filter** — L1030, L1069, L1107, L1193 in `admin_ret_dashboard_api.php`: `get_VitrualTag_post()`, `get_SalesReturn_post()`, `get_LotDetails_post()`, `get_CoverUpReport_post()` all read `$from_date`/`$to_date` from POST but then pass `date('Y-m-d'), date('Y-m-d')` hardcoded to model. User's selected date range is silently ignored.

### 🟡 MEDIUM (Round 10 findings)
27. **Diamond weight balance formula always returns negative** — `admin_ret_dashboard_api.php` L1502 in `get_weight_gain_loss_post()`: `blc_diawt` calc uses `$val['lotdiawt']` for all 4 terms: `lotdiawt - lotdiawt - lotdiawt - lotdiawt` = `-2 × lotdiawt`. Result is always negative double the lot weight. Copy-paste error from adjacent `blc_gwt` formula.

---

## 8. Business Rules Summary

**Documented rules:** 18 (see [BUSINESS_RULES.md](BUSINESS_RULES.md))

Key rules:
- Estimation status: 1=Sold, 0=Unbilled, 2=Returned, else=In Process
- Bill types: 1=Sales, 2=Sales&Purchase, 3=Sales&Return, 4=Purchase, 5=OrderAdvance, 6=Advance, 7=SalesReturn, 8=CreditBillPayment, 9=OrderDelivery, 10=ChitPreClose, 11=RepairOrderDelivery, 12=SupplierSalesBill, 13=SalesTransfer, 14=SalesRetTransfer
- Green tag = `tag_mark=1 AND tag_status=1`
- Virtual tag types: item_type=2 (custom/home sale), is_partial_sale=1 (tag split)
- Customer order statuses: 0=received, 1=placed, 2=allocated, 3=WIP, 4=ready, 5=delivered
- Karigar order statuses: order_for=1 (karigar), order_for=2 (customer)
- Available stock formula: `opening + inward - sold - branch_out`

---

## 9. Cross-Module Dependencies

See → [CROSS_MODULE_MAP.md](CROSS_MODULE_MAP.md)

Key dependencies (primary controller — admin_ret_dashboard.php):
- **Estimation module** — reads `ret_estimation`, `ret_estimation_items`
- **Billing module** — reads `ret_billing`, `ret_bill_details` (primary data source)
- **Tagging module** — reads `ret_taging`, `ret_taging_status_log`
- **LOT module** — reads `ret_lot_inwards`, `ret_lot_inwards_detail`
- **Orders module** — reads `customerorder`, `customerorderdetails`, `order_cart`
- **Finance module** — reads `ret_issue_receipt`, `ret_issue_rcpt_payment`, `gift_card`, `ledger_master`
- **Chit module** — reads `payment`, `payment_mode_details`, `scheme_account`
- **ret_reports_model** — cross-controller model dependency for cash abstract + ledger alerts

Additional dependencies (API controller — admin_ret_dashboard_api.php):
- **Purchase/Karigar module** — reads `ret_purchase_order`, `ret_purchase_order_items`, `ret_purchase_return`, `ret_purchase_return_items`, `ret_karigar_metal_issue`, `ret_karigar_metal_issue_details`, `ret_po_rate_fix`, `ret_supplier_rate_cut`, `ret_crdr_note`, `ret_grn_entry`, `ret_po_payment`, `ret_po_payment_detail`, `ret_po_bill_payment_details`
- **QC module** — reads `ret_po_qc_issue_details`, `ret_po_qc_issue_process`
- **Branch Transfer module** — reads `ret_branch_transfer`, `ret_brch_transfer_old_metal`, `ret_brch_transfer_tag_items`, `ret_bt_order_log`
- **Finance (extended)** — reads `ret_breakeven_logs`, `ret_cover_up`, `metal_rates`, `ret_view_supplier_approval_ledger` (VIEW), `bank`
- **Catalog module** — `ret_catalog_model::getActiveMetals()` called by `getActiveMetals_get()` at L1435
- **ret_reports_model (extended — Round 10)** — 3 additional cross-model calls found:
  - `getSupplierTransactionList($post)` — called by `get_supplier_crde_post()` L1669 and `get_supplier_transcation_post()` L1732 (both methods call same model method)
  - `getLotwiseTaggedVault($post)` — called by `get_weight_gain_loss_post()` L1486 (weight gain/loss widget)
  - `getTaggeditems($post)` — called by `tag_details_post()` L1909 (tag detail drill-down)
- **🆕 MD Approval Dashboard (Round 16)** — new dependencies added in `admin_ret_dashboard_api.php`:
  - `ret_purchase_approval_model` — loaded on-demand in `_send_vendor_email()`. Provides: `get_karigar_details($id_karigar)`, `save_email_log($data)`, `update_email_log($id, $data)`, `get_karigar_order_details($id_customerorder)`
  - `email_model` — loaded on-demand in `_send_vendor_email()`. Provides: `send_email($to, $subject, $msg, ..., $embeddings)` with embedded image support and `last_error` property
  - **Writes to** `customerorder` (order_status updates) and `ret_order_email_logs` (email audit trail) — **⚠️ This is the FIRST time this dashboard module writes to any table**
  - New tables read: `ret_design_weight_range_wc`, `order_status_message`, `ret_order_email_logs`

---

## 10. DB Verification Queries

```sql
-- 1. Today's billing summary
SELECT count(*) as bills, SUM(tot_bill_amount) as total
FROM ret_billing WHERE date(bill_date) = CURDATE() AND bill_status=1;

-- 2. Available gold stock for a branch (id_branch=1)
SELECT COUNT(*) as available_pcs FROM ret_taging_status_log m1
LEFT JOIN ret_taging_status_log m2 ON (m1.tag_id=m2.tag_id AND m1.id_tag_status_log<m2.id_tag_status_log)
LEFT JOIN ret_taging t ON t.tag_id=m1.tag_id
LEFT JOIN ret_product_master p ON p.pro_id=t.product_id
LEFT JOIN ret_category c ON c.id_ret_category=p.cat_id
WHERE m2.id_tag_status_log IS NULL AND (m1.status=0 OR m1.status=6) AND m1.to_branch=1 AND c.id_metal=1;

-- 3. Credit bills outstanding
SELECT COUNT(*) as credit_bills, SUM(tot_bill_amount - tot_amt_received) as outstanding
FROM ret_billing WHERE is_credit=1 AND bill_status=1 AND bill_type!=8;

-- 4. Orphan check: bill details without billing header
SELECT COUNT(*) FROM ret_bill_details d LEFT JOIN ret_billing b ON b.bill_id=d.bill_id WHERE b.bill_id IS NULL;
```

---

## 11. Codebase Notes

- **Coding pattern:** All controller methods are near-identical: get `$from_date`, `$to_date`, `$id_branch` from POST → call model → `echo json_encode($data)`
- **JS tabs:** The JS uses `switch(ctrl_page[1])` to determine which functions to initialize. Dashboard page = `'dashboard'` case; estimation sub-page = `'get_estimation'` case
- **Chart libraries:** Both Google Charts API and Chart.js are used. Google Charts used for main dashboards; Chart.js used for legacy pie charts.
- **Commented code:** Large blocks of PHP and JS are commented out (`/* ... */`) throughout — dead code accumulation is significant (especially in model file)
- **Model complexity:** The model contains an entire `get_dashboard_cash_abstarct_details()` method (L672-1295) that is now dead code — the controller uses `ret_reports_model` instead
- **JS file size:** 19,383 lines — largest file in module. Contains many commented-out functions and duplicate approaches.

---

## 12. Anti-Patterns Register

> *(Empty on initial build — updated after each bug fix)*

| Pattern | Location | Description | Impact |
|---|---|---|---|
| SQL Injection | Model — all methods | Direct variable concatenation in SQL strings | CRITICAL |
| Dead code mega-method | Controller L539-623 | `get_retail_dashboard_details()` — returns unfiltered data | HIGH |
| Copy-paste logic error | Model L411-451 | Old/new customer bill classification uses identical SQL | HIGH |
| Duplicate array key | Model L3484-3486, L4318-4320 | `available_gwt` key defined twice — overwrites with `available_nwt` value | HIGH |
| Undefined variable | Model L4851 | `$number` not defined in `get_store_sales()` → PHP fatal | HIGH |
| Missing branch filter | Model L3556-3574 | `get_new_customer()` ignores `$id_branch` param | MEDIUM |
| Division by zero | Controller L1391 | No zero-guard before percentage calculation | MEDIUM |
| Direct $_POST in model | Model L1695 | Model accesses POST directly | LOW |
| ~~Orphan methods (18)~~ | Model L4124-5100 | **CORRECTED Round 10** — only 2 true orphans remain (`get_branch_sales` L647, `get_rate_cut_details` L2522). 9 previously-classified orphans are called from API controller. | LOW |
| Wildcard CORS | API Ctrl L3 | `Access-Control-Allow-Origin: *` — enables xorigin API abuse | CRITICAL |
| Hardcoded date in 4 methods | API Ctrl L1030/1069/1107/1193 | Date filter silently replaced with today's date | CRITICAL |
| Formula error | API Ctrl L1502 | Diamond weight calc always returns -2×lotdiawt | MEDIUM |

> Full anti-pattern catalogue: see [ANTI_PATTERNS.md](ANTI_PATTERNS.md) — 13 patterns with code examples and fixes.
