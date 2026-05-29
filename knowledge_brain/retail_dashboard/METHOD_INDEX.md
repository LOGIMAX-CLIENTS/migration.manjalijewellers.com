# METHOD INDEX — Retail Dashboard
> Generated: 2026-03-16 | **Updated: Round 16 (2026-03-25)** | All methods alphabetical for grep efficiency

---

## 7a. Controller Methods (Alphabetical)

| Method | Lines | Tables Read | Tables Written | Type |
|---|---|---|---|---|
| `ajax_lot_data()` | L1005-1049 | `ret_lot_inwards`, `branch` | None | AJAX/POST |
| `ajax_tag_data()` | L1051-1093 | `ret_taging`, `branch` | None | AJAX/POST |
| `get_approval()` | L1518-1532 | `ret_branch_transfer`, `ret_taging` | None | AJAX/POST |
| `get_BillClassficationDetails()` | L257-273 | `ret_billing`, `ret_bill_details` | None | AJAX/POST |
| `get_branch_transfer_details()` | L1229-1245 | `ret_branch_transfer` | None | AJAX/POST |
| `get_BillingStatus()` | L89-109 | `ret_billing`, `ret_bill_details`, `ret_product_master`, `ret_category`, `metal`, `ret_billing_item_stones`, `ret_stone` | None | AJAX/POST |
| `get_BranchTransferDetails()` | L277-293 | `ret_branch_transfer` | None | AJAX/POST |
| `get_cash_abstract_details()` | L1702-2083 | uses `ret_reports_model::getBillDetails()` — 20+ tables | None | AJAX/POST |
| `get_cash_deposit_details_branchwise()` | L1492-1514 | `branch`, multiple via model | None | AJAX/POST |
| `get_contract_approval()` | L1538-1550 | `ret_taging` (via model) | None | AJAX/POST |
| `get_CreditDetils()` | L1161-1179 | `ret_billing`, `customer` | None | AJAX/POST |
| `get_CreditSalesDetails()` | L215-231 | `ret_billing`, `ret_issue_credit_collection_details`, `ret_issue_receipt`, `customer`, `branch` | None | AJAX/POST |
| `get_customer_order_details()` | L1283-1303 | `customerorder`, `customerorderdetails` | None | AJAX/POST |
| `get_CustomerDetails()` | L457-473 | `customer`, `branch` | None | AJAX/POST ⚠️ ignores id_branch |
| `get_customerOrderDetails()` | L423-435 | `customerorder`, `customerorderdetails`, `branch` | None | AJAX/POST ⚠️ uses $_POST directly |
| `get_dreadyorderDetails()` | L1617-1635 | `customerorder`, `customerorderdetails`, `branch` | None | AJAX/POST |
| `get_deliveredorderDetails()` | L1637-1655 | `customerorder`, `customerorderdetails`, `branch` | None | AJAX/POST |
| `get_EstimationStatus()` | L67-85 | `ret_estimation`, `ret_estimation_items` | None | AJAX/POST |
| `get_estimation()` | L935-943 | None (view load) | None | GET/PAGE |
| `get_estimation_details()` | L947-1001 | `ret_estimation`, `ret_estimation_items`, `ret_estimation_item_stones`, ++ | None | AJAX/POST |
| `get_GiftVoucherDetails()` | L237-253 | `gift_card` | None | AJAX/POST |
| `get_GreentagSalesDetails()` | L133-149 | `ret_bill_details`, `ret_billing`, `ret_taging`, `ret_settings`, `metal`, `ret_product_master`, `ret_category` | None | AJAX/POST |
| `get_karigar_order_details()` | L1307-1325 | `customerorder`, `customerorderdetails`, `branch` | None | AJAX/POST |
| `get_karigaroverdueDetails()` | L1679-1697 | `customerorder`, `customerorderdetails`, `branch` | None | AJAX/POST |
| `get_karigarreminderDetails()` | L1658-1676 | `customerorder`, `customerorderdetails`, `branch` | None | AJAX/POST |
| `get_KarigarOrderDetails()` | L401-419 | `customerorder`, `customerorderdetails` | None | AJAX/POST ⚠️ uses model POST direct |
| `get_LedgerBalanceAlert()` | L2088-2147 | `ledger` + `ret_reports_model::getLedgerReportData()` | None | AJAX/POST |
| `get_lot_tag_details()` | L297-313 | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_taging` | None | AJAX/POST |
| `get_metal_stock_details()` | L1209-1225 | `ret_taging`, `ret_product_master`, `ret_category`, `branch` | None | AJAX/POST |
| `get_MetalBill_details()` | L1139-1153 | `ret_billing`, `ret_bill_details`, `metal`, `ret_product_master`, `ret_category`, `branch` | None | AJAX/POST ⚠️ no date filter |
| `get_MetalStockDetails()` | L439-453 | `ret_taging_status_log`, `ret_taging`, `ret_product_master`, `ret_category`, `metal`, `ret_nontag_item` | None | AJAX/POST |
| `get_new_customer()` | L1257-1273 | `customer`, `branch` | None | AJAX/POST |
| `get_old_metal_purchase()` | L195-211 | `ret_bill_old_metal_sale_details`, `ret_billing` | None | AJAX/POST |
| `get_OrderDetails()` | L317-333 | `order_cart` | None | AJAX/POST |
| `get_pendingorderDetails()` | L1576-1594 | `customerorder`, `customerorderdetails`, `branch` | None | AJAX/POST |
| `get_PendingDueDetails()` | L1185-1197 | `ret_billing`, `customer` | None | AJAX/POST |
| `get_RecentBillDetails()` | L477-493 | `ret_billing`, `branch`, `customer` | None | AJAX/POST |
| `get_ReorderDetails()` | L381-397 | `ret_reorder_settings`, `ret_product_master`, `ret_design_master`, `ret_taging`, `order_cart`, `branch`, `ret_weight`, `ret_uom`, `ret_size` | None | AJAX/POST |
| `get_retail_dashboard_details()` | L539-623 | 15+ tables (legacy) | None | AJAX/POST ⚠️ LEGACY |
| `get_SaleBill_details()` | L698-882 | `ret_billing`, `ret_bill_details`, `ret_billing_payment`, `ret_issue_rcpt_payment`, `ret_issue_receipt`, `ret_product_master`, `ret_category`, `metal`, `branch` | None | AJAX/POST |
| `get_sales_details()` | L1097-1135 | `ret_billing`, `branch` | None | AJAX/POST |
| `get_saleschart_details()` | L1335-1443 | `ret_estimation`, `ret_billing`, `ret_taging`, `ret_issue_receipt`, `customer`, `branch`, + more | None | AJAX/POST |
| `get_SalesReturnDetails()` | L173-189 | `ret_bill_details`, `ret_billing` | None | AJAX/POST |
| `get_silver_StockDetails()` | L359-375 | `ret_taging`, `ret_taging_status_log`, `ret_billing`, `ret_bill_details`, `ret_product_master` | None | AJAX/POST |
| `get_stock_details()` | L113-129 | Same as AvailableStockDetails (gold only) | None | AJAX/POST |
| `get_stockchart_details()` | L1449-1489 | `branch`, `ret_taging`, `ret_taging_status_log`, `ret_branch_transfer` | None | AJAX/POST |
| `get_StockDetails()` | L339-355 | `ret_taging`, `ret_taging_status_log`, `ret_billing`, `ret_bill_details`, `ret_product_master` | None | AJAX/POST |
| `get_VitrualTagStatus()` | L153-169 | `ret_billing`, `ret_bill_details`, `ret_estimation_items`, `ret_estimation`, `ret_taging` | None | AJAX/POST |
| `get_wiporderDetails()` | L1597-1615 | `customerorder`, `customerorderdetails`, `branch` | None | AJAX/POST |
| `gross_profit_report($type)` | L1552-1571 | via model `get_gross_profit_details()` | None | GET+AJAX |
| `index()` | L57-61 | None | None | GET stub |

---

## 7b. Model Methods (Alphabetical)

| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `allBranches()` | L1511-1519 | `branch` | None | get_SaleBill_details, get_order_data, ajax_lot_data, ajax_tag_data, get_sales_details, get_MetalBill_details, get_stockchart_details |
| `approve_branch_download()` | ~L3400+ | `ret_branch_transfer` | None | get_approval |
| `approve_branch_transfer()` | ~L3400+ | `ret_branch_transfer` | None | get_approval |
| `approve_contract_price()` | ~L3400+ | `ret_taging` | None | get_approval |
| `approval_contract_price()` | ~L3400+ | `ret_taging` | None | get_contract_approval |
| `Available_SilverStockDetails()` | L2259-2506 | `ret_taging`, `ret_taging_status_log`, `ret_product_master`, `ret_category`, `metal`, `ret_bill_details`, `ret_billing` | None | get_silver_StockDetails, get_retail_dashboard_details |
| `AvailableStockDetails()` | L2008-2257 | `ret_taging`, `ret_taging_status_log`, `ret_product_master`, `ret_category`, `metal`, `ret_bill_details`, `ret_billing` | None | get_StockDetails, get_stock_details, get_retail_dashboard_details |
| `customer_orders()` | L1835-1937 | `customerorder`, `customerorderdetails`, `branch` | None | get_customerOrderDetails, get_retail_dashboard_details |
| `get_billing()` | L1497-1509 | `ret_billing` | None | get_retail_dashboard_details |
| `get_branch_stock_details()` | ~L3200+ | `ret_taging`, `ret_taging_status_log`, `branch` | None | get_stockchart_details |
| `get_branch_transfer_details()` | ~L3500+ | `ret_branch_transfer` | None | get_branch_transfer_details, get_stockchart_details |
| `get_branchwise_sales()` | ~L3700+ | `ret_billing`, `ret_bill_details`, `branch`, `metal` | None | get_saleschart_details |
| `get_cart_items()` | L3033-3041 | `order_cart` | None | getReorderItems |
| `get_creditDetilas()` | ~L3700+ | `ret_billing`, `ret_issue_credit_collection_details`, `ret_issue_receipt` | None | get_saleschart_details |
| `get_CreditDetils()` | ~L3600+ | `ret_billing`, `customer` | None | get_CreditDetils |
| `get_custome_wise_sale()` | L5051-5100 | `ret_billing`, `ret_bill_details`, `scheme_account`, `customer` | None | (orphan — profile-gated) |
| `get_customer_order_details()` | ~L3800+ | `customerorder`, `customerorderdetails` | None | get_customer_order_details |
| `get_customer_visit()` | ~L4000+ | `customer` | None | get_saleschart_details |
| `get_dashboard_approval_pendings()` | L453-469 | `ret_branch_transfer` | None | get_BranchTransferDetails, get_retail_dashboard_details |
| `get_dashboard_billings()` | L132-183 | `ret_billing`, `ret_bill_details`, `ret_product_master`, `ret_category`, `metal`, `branch` | None | get_BillingStatus, get_retail_dashboard_details |
| `get_dashboard_billings_dia()` | L245-265 | `ret_billing_item_stones`, `ret_billing`, `ret_stone` | None | get_BillingStatus |
| `get_dashboard_billings_mrp()` | L215-241 | `ret_billing`, `ret_bill_details`, `ret_product_master`, `ret_category`, `metal`, `branch` | None | get_BillingStatus |
| `get_dashboard_billings_sl_wt()` | L185-213 | `ret_billing`, `ret_bill_details`, `ret_product_master`, `ret_category`, `metal`, `branch` | None | (orphan? not called from active controller) |
| `get_dashboard_bills_clasfications()` | L411-451 | `ret_billing`, `ret_bill_details` | None | get_BillClassficationDetails ⚠️ buggy |
| `get_dashboard_bills_details()` | L530-583 | `ret_billing`, `branch`, `customer` | None | get_RecentBillDetails |
| `get_dashboard_cash_abstarct_details()` | L672-1295 | 20+ tables | None | ⚠️ DEAD CODE — NOT CALLED |
| `get_dashboard_credit_sales()` | L361-393 | `ret_billing`, `ret_issue_credit_collection_details`, `ret_issue_receipt`, `customer`, `branch` | None | get_CreditSalesDetails |
| `get_dashboard_customer_details()` | L516-528 | `customer`, `branch` | None | get_CustomerDetails ⚠️ no branch filter |
| `get_dashboard_estimation()` | L60-88 | `ret_estimation`, `ret_estimation_items` | None | get_EstimationStatus |
| `get_dashboard_estimation_details()` | L90-118 | `ret_estimation`, `ret_estimation_items`, `branch`, `customer` | None | getEstimationDetails |
| `get_dashboard_gift_vouchers()` | L395-407 | `gift_card` | None | get_GiftVoucherDetails |
| `get_dashboard_greentag_det()` | L269-339 | `ret_bill_details`, `ret_billing`, `ret_taging`, `ret_product_master`, `ret_category`, `metal`, `ret_settings` | None | get_GreentagSalesDetails |
| `get_dashboard_lot_tag_details()` | L471-496 | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_taging` | None | get_lot_tag_details |
| `get_dashboard_old_metal_purchase()` | L343-357 | `ret_bill_old_metal_sale_details`, `ret_billing` | None | get_old_metal_purchase |
| `get_dashboard_orders_details()` | L498-514 | `order_cart` | None | get_OrderDetails |
| `get_dashboard_salesreturn_det()` | L652-668 | `ret_bill_details`, `ret_billing` | None | get_SalesReturnDetails |
| `get_dashboard_virturaltag_details()` | L586-648 | `ret_billing`, `ret_bill_details`, `ret_estimation_items`, `ret_estimation`, `ret_taging` | None | get_VitrualTagStatus |
| `get_dreadyorderDetails()` | ~L4500+ | `customerorder`, `customerorderdetails`, `branch` | None | get_dreadyorderDetails |
| `get_deliveredorderDetails()` | ~L4500+ | `customerorder`, `customerorderdetails`, `branch` | None | get_deliveredorderDetails |
| `get_deposit_details_branchwise()` | ~L4600+ | multiple finance tables | None | get_cash_deposit_details_branchwise |
| `get_due_pending_details()` | ~L3600+ | `ret_billing`, `customer` | None | get_PendingDueDetails |
| `get_estimation()` | L46-58 | `ret_estimation` | None | get_retail_dashboard_details |
| `get_estimation_details()` | L1303-1339 | `ret_estimation`, `ret_estimation_items`, `ret_estimation_item_stones`, `ret_estimation_item_other_materials`, `ret_estimation_old_metal_sale_details`, `ret_est_chit_utilization`, `ret_est_gift_voucher_details`, `customer` | None | get_estimation_details |
| `get_EstimationDetails()` | ~L3700+ | `ret_estimation`, `ret_estimation_items` | None | get_saleschart_details |
| `get_all_branch_store()` | ~L4600+ | `branch` | None | get_cash_deposit_details_branchwise |
| `get_green_tag_sales()` | ~L4000+ | `ret_billing`, `ret_bill_details`, `ret_taging`, `ret_product_master`, `ret_category`, `metal` | None | get_saleschart_details |
| `get_gross_profit_details()` | ~L4600+ | multiple billing/product tables | None | gross_profit_report('ajax') |
| `get_karigaroverdueDetails()` | ~L4400+ | `customerorder`, `customerorderdetails`, `branch` | None | get_karigaroverdueDetails |
| `get_karigarreminderDetails()` | ~L4400+ | `customerorder`, `customerorderdetails`, `branch` | None | get_karigarreminderDetails |
| `get_lot_data()` | L1373-1419 | `ret_lot_inwards` | None | ajax_lot_data |
| `get_metalBillRecords()` | L3184-3200+ | `ret_billing`, `ret_bill_details`, `ret_product_master`, `ret_category`, `metal`, `branch` | None | get_SaleBill_details |
| `get_modewise_sales()` | ~L4000+ | `ret_billing`, `ret_billing_payment`, `branch` | None | get_saleschart_details |
| `get_new_customer()` | ~L3600+ | `customer`, `branch` | None | get_new_customer |
| `get_order_data()` | L1521-1595 | `customerorderdetails`, `branch` | None | get_order_data |
| `get_order_details()` | ~L3800+ | `customerorder`, `customerorderdetails`, `branch` | None | get_customer_order_details, get_karigar_order_details |
| `get_pendingorderdetails()` | ~L4200+ | `customerorder`, `customerorderdetails`, `branch` | None | get_pendingorderDetails |
| `get_paymentBillRecords()` | L3120-3183 | `ret_billing`, `ret_billing_payment`, `ret_issue_rcpt_payment`, `ret_issue_receipt`, `branch` | None | get_SaleBill_details |
| `get_previous_date_closing()` | ~L4600+ | finance/ledger tables | None | get_cash_deposit_details_branchwise |
| `get_saleBill()` | L1639-1655 | `ret_billing`, `branch` | None | get_sales_details |
| `get_saleBillRecords()` | L3045-3065 | `ret_bill_details`, `ret_billing`, `ret_taging`, `ret_product_master`, `ret_design_master`, `ret_category`, `branch` | None | get_SaleBill_details |
| `get_sales_summary()` | ~L3800+ | `ret_billing`, `ret_bill_details`, `branch` | None | get_saleschart_details |
| `get_stock_category_details()` | ~L3300+ | `ret_taging`, `ret_product_master`, `ret_category`, `branch` | None | get_metal_stock_details |
| `get_tag_data()` | L1447-1493 | `ret_taging` | None | ajax_tag_data |
| `get_wiporderDetails()` | ~L4300+ | `customerorder`, `customerorderdetails`, `branch` | None | get_wiporderDetails |
| `getLedgerBalanceAlertData()` | ~L4700+ | `ledger` | None | get_LedgerBalanceAlert |
| `getReorderItems()` | L2919-3011 | `ret_reorder_settings`, `ret_product_master`, `ret_design_master`, `ret_size`, `branch`, `ret_weight`, `ret_uom` + calls getTagging + get_cart_items | None | get_ReorderDetails |
| `getTagging()` | L3017-3029 | `ret_taging` | None | getReorderItems |
| `karigar_orders()` | L1689-1753 | `customerorderdetails`, `customerorder` | None | get_KarigarOrderDetails, get_retail_dashboard_details ⚠️ reads POST directly |
| `lot_branchwise_data()` | L1343-1371 | `ret_lot_inwards`, `branch` | None | ajax_lot_data |
| `metal_bill_details()` | L1659-1681 | `ret_billing`, `ret_bill_details`, `ret_product_master`, `ret_category`, `metal`, `branch` | None | get_MetalBill_details |
| `NonTagStockMetalWise()` | L1973-2006 | `ret_nontag_item`, `ret_product_master`, `ret_category`, `metal` | None | get_MetalStockDetails |
| `StockMetalWise()` | ~L1900+ | `ret_taging_status_log`, `ret_taging`, `ret_product_master`, `ret_category`, `metal` | None | get_retail_dashboard_details |
| `tag_branchwise_data()` | L1421-1445 | `ret_taging`, `branch` | None | ajax_tag_data |
| `TagStockMetalWise()` | L1941-1971 | `ret_taging_status_log`, `ret_taging`, `ret_product_master`, `ret_category`, `metal` | None | get_MetalStockDetails |
| `total_order()` | L1597-1635 | `customerorderdetails` | None | get_order_data |

---

## 7c. JS → Controller AJAX Map

> JS file: `admin/assets/js/ret_dashboard.js` (19,383 lines)

### Internal Endpoints (admin_ret_dashboard controller)
| JS Function | AJAX URL | Controller Method |
|---|---|---|
| `get_branch_order()` | `admin_ret_dashboard/get_SaleBill_details` | `get_SaleBill_details()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_EstimationStatus` | `get_EstimationStatus()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_BillingStatus` | `get_BillingStatus()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_GreentagSalesDetails` | `get_GreentagSalesDetails()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_SalesReturnDetails` | `get_SalesReturnDetails()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_VitrualTagStatus` | `get_VitrualTagStatus()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_CreditSalesDetails` | `get_CreditSalesDetails()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_GiftVoucherDetails` | `get_GiftVoucherDetails()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_BillClassficationDetails` | `get_BillClassficationDetails()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_BranchTransferDetails` | `get_BranchTransferDetails()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_lot_tag_details` | `get_lot_tag_details()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_RecentBillDetails` | `get_RecentBillDetails()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_CustomerDetails` | `get_CustomerDetails()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_stock_details` | `get_stock_details()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_StockDetails` | `get_StockDetails()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_silver_StockDetails` | `get_silver_StockDetails()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_ReorderDetails` | `get_ReorderDetails()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_MetalStockDetails` | `get_MetalStockDetails()` |
| `get_live_cockpit_dashboard_details()` | `admin_ret_dashboard/get_cash_abstract_details` | `get_cash_abstract_details()` |
| `get_deposit_data()` | `admin_ret_dashboard/get_cash_deposit_details_branchwise` | `get_cash_deposit_details_branchwise()` |
| `get_metal_purchase_status()` | `admin_ret_dashboard/get_old_metal_purchase` | `get_old_metal_purchase()` |
| `get_approval_type()` | `admin_ret_dashboard/get_approval` | `get_approval()` |
| `get_order_management_details()` | `admin_ret_dashboard/get_customer_order_details` | `get_customer_order_details()` |
| `get_order_management_details()` | `admin_ret_dashboard/get_pendingorderDetails` | `get_pendingorderDetails()` |
| `get_order_management_details()` | `admin_ret_dashboard/get_wiporderDetails` | `get_wiporderDetails()` |
| `get_order_management_details()` | `admin_ret_dashboard/get_dreadyorderDetails` | `get_dreadyorderDetails()` |
| `get_order_management_details()` | `admin_ret_dashboard/get_deliveredorderDetails` | `get_deliveredorderDetails()` |
| `get_order_management_details()` | `admin_ret_dashboard/get_karigarreminderDetails` | `get_karigarreminderDetails()` |
| `get_order_management_details()` | `admin_ret_dashboard/get_karigaroverdueDetails` | `get_karigaroverdueDetails()` |
| `sales_details()` | `admin_ret_dashboard/get_stock_details` | `get_stock_details()` |
| `get_order_management_details()` | `admin_ret_dashboard/get_OrderDetails` | `get_OrderDetails()` |
| `get_order_management_details()` | `admin_ret_dashboard/get_KarigarOrderDetails` | `get_KarigarOrderDetails()` |
| `get_order_management_details()` | `admin_ret_dashboard/get_customerOrderDetails` | `get_customerOrderDetails()` |
| `get_order_management_details()` | `admin_ret_dashboard/get_MetalStockDetails` | `get_MetalStockDetails()` |
| `get_order_management_details()` | `admin_ret_dashboard/get_StockDetails` | `get_StockDetails()` |
| `get_order_management_details()` | `admin_ret_dashboard/get_silver_StockDetails` | `get_silver_StockDetails()` |
| `get_order_management_details()` | `admin_ret_dashboard/get_ReorderDetails` | `get_ReorderDetails()` |
| `sales_dashboard_data()` | `admin_ret_dashboard/get_saleschart_details` | `get_saleschart_details()` |
| `get_stockDetails()` | `admin_ret_dashboard/get_stockchart_details` | `get_stockchart_details()` |
| `get_contract_pricing()` | `admin_ret_dashboard/get_contract_approval` | `get_contract_approval()` |
| `get_ActiveMetals_dashboard()` | `admin_ret_dashboard/get_BillingStatus` | `get_BillingStatus()` |
| `check_LedgerBalanceAlert()` | `admin_ret_dashboard/get_LedgerBalanceAlert` | `get_LedgerBalanceAlert()` |
| `set_estimation_table()` | `admin_ret_dashboard/get_estimation_details` | `get_estimation_details()` |
| `get_cash_abstract_data()` | `admin_ret_dashboard/get_cash_abstract_details` | `get_cash_abstract_details()` |
| `get_average_bill_value()` | `admin_ret_dashboard/get_BillingStatus` | `get_BillingStatus()` |
| Various order tabs | `admin_ret_dashboard/get_pendingorderDetails` | `get_pendingorderDetails()` |
| Various order tabs | `admin_ret_dashboard/get_wiporderDetails` | `get_wiporderDetails()` |
| Various order tabs | `admin_ret_dashboard/get_dreadyorderDetails` | `get_dreadyorderDetails()` |
| Various order tabs | `admin_ret_dashboard/get_deliveredorderDetails` | `get_deliveredorderDetails()` |
| Various order tabs | `admin_ret_dashboard/get_karigarreminderDetails` | `get_karigarreminderDetails()` |
| Various order tabs | `admin_ret_dashboard/get_karigaroverdueDetails` | `get_karigaroverdueDetails()` |

### Cross-Module Endpoints
| JS Context | AJAX URL | External Controller |
|---|---|---|
| Cash deposit section | `admin_ret_dashboard/get_cash_deposit_details_branchwise` | Uses `ret_reports_model` internally |
| GP Report | `admin_ret_dashboard/gross_profit_report/ajax` | Uses `ret_reports_model` internally |

---

## 7d. Table → Methods Reverse Map (Key Tables)

| Table | Read By | Written By |
|---|---|---|
| `ret_billing` | 25+ model methods | Not written here (Billing module) |
| `ret_bill_details` | 15+ model methods | Not written here |
| `ret_taging` | 12+ model methods | Not written here (Tagging module) |
| `ret_taging_status_log` | `AvailableStockDetails`, `Available_SilverStockDetails`, `TagStockMetalWise`, `tag_branchwise_data`, `get_tag_data` | Not written here |
| `ret_estimation` | `get_dashboard_estimation`, `get_estimation_details`, `get_estimation` | Not written here |
| `ret_estimation_items` | `get_dashboard_estimation`, `get_dashboard_estimation_details` | Not written here |
| `customerorder` | `karigar_orders`, `customer_orders`, `get_order_data`, `total_order`, `get_order_details` | Not written here |
| `gift_card` | `get_dashboard_gift_vouchers` | Not written here |
| `ret_settings` | `get_dashboard_greentag_det` | Not written here |
| `ret_branch_transfer` | `get_dashboard_approval_pendings`, `get_branch_transfer_details`, `approve_branch_transfer`, `approve_branch_download` | Not written here |
| `ret_lot_inwards` | `get_dashboard_lot_tag_details`, `lot_branchwise_data`, `get_lot_data` | Not written here |
| `ret_nontag_item` | `NonTagStockMetalWise` | Not written here |
| `order_cart` | `get_dashboard_orders_details`, `get_cart_items` | Not written here |
| `ret_reorder_settings` | `getReorderItems` | Not written here |
| `ledger_master` | `getLedgerBalanceAlertData` | Not written here |

---

## 7e. API Controller Methods (admin_ret_dashboard_api.php — 52 methods)

> REST controller extends `REST_Controller`. Global `Access-Control-Allow-Origin: *` header (SEC-02). All methods use `_post` or `_get` suffix. Primary model: `ret_dashboard_api_model`.

| Method | HTTP | Lines | Model Method Called | Purpose | Bug? |
|---|---|---|---|---|---|
| `index()` | GET | L65-69 | — | Empty stub | |
| `get_Sales_glance_post()` | POST | L81-120 | `get_dashboard_sales_glance()` | Sales KPI summary | |
| `get_top_selling_post()` | POST | L122-169 | `get_top_selling()` | Top products by bill count | |
| `get_top_sellers_post()` | POST | L171-220 | `get_top_sellers()` | Top karigar sellers | |
| `get_monthly_sales_post()` | POST | L223-259 | `get_monthly_sales()` | MoM sales by FY | |
| `get_custome_wise_sale_post()` | POST | L261-325 | `get_custome_wise_sale()` | Customer-type wise sale | |
| `get_monthly_sales_app_post()` | POST | L327-360 | `get_monthly_sales_mobile()` | Mobile MoM sales | |
| `get_financial_year_get()` | GET | L362-372 | `get_financial_year()` | FY list | |
| `get_branch_comparison_post()` | POST | L374-397 | `get_store_sales()` | Branch sales % (no dual path) | |
| `get_branch_compare_post()` | POST | L399-450 | `get_store_sales()` | Same model, structured response | |
| `get_store_sales_post()` | POST | L452-491 | `get_store_sales()` | Branch sales (3rd variant) | |
| `get_product_sales_post()` | POST | L493-529 | `get_product_sales()` | Top-10 product sales pie | |
| `get_branch_avg_va_post()` | POST | L531-570 | `get_branch_wastage()` | Branch VA wastage ⚠️ Bug#21 | #21 |
| `get_employee_sales_post()` | POST | L572-632 | `get_employee_sales()` | Employee sales top-10 | |
| `get_section_sales_post()` | POST | L634-692 | `get_section_sales()` | Section sales top-10 | |
| `get_karigar_sales_post()` | POST | L694-729 | `get_karigar_sales()` | Karigar sales top-10 | |
| `get_product_stock_post()` | POST | L733-792 | `get_product_stock()` | Product stock top-5 | |
| `get_section_stock_post()` | POST | L794-858 | `get_section_stock()` | Section stock top-5 | |
| `get_karigar_stock_post()` | POST | L860-940 | `get_karigar_stock()` | Karigar stock (group_by 1/2) ⚠️ label bug | |
| `get_EstimationStatus_post()` | POST | L942-996 | `get_dashboard_estimation()` | Estimation label/value/colour | |
| `get_VitrualTag_post()` | POST | L998-1034 | `get_dashboard_virturaltag_details()` | Virtual tag ⚠️ IGNORES date | #26 |
| `get_SalesReturn_post()` | POST | L1037-1073 | `get_dashboard_salesreturn_det()` | Sales return ⚠️ IGNORES date | #26 |
| `get_LotDetails_post()` | POST | L1075-1111 | `get_dashboard_lot_tag_details()` | Lot details ⚠️ IGNORES date | #26 |
| `get_FinancialStatus_post()` | POST | L1113-1158 | `get_dashboard_breakeven_details()` | Breakeven target comparison | |
| `get_CoverUpReport_post()` | POST | L1161-1197 | `get_cover_up_report()` | Hedging cover-up P&L ⚠️ IGNORES date | #26 |
| `get_purchase_inwards_post()` | POST | L1201-1237 | `get_purchase_inwards()` | Purchase inwards GWT/NWT | |
| `get_vendor_payment_post()` | POST | L1240-1276 | `get_vendor_payment()` | Vendor payment summary | |
| `get_outward_details_post()` | POST | L1279-1314 | `get_outward_details()` | Purchase return + outward | |
| `getMetalwiseApprovalTransaction_post()` | POST | L1316-1351 | `getMetalwiseApprovalTransactionList()` | Supplier approval ledger | |
| `get_crdr_details_post()` | POST | L1354-1389 | `get_crdr_details()` | Cr/Dr note summary | |
| `get_qc_details_post()` | POST | L1391-1426 | `get_qc_details()` | QC failed pcs/wt | |
| `getActiveMetals_get()` | GET | L1429-1440 | `ret_catalog_model::getActiveMetals()` | Active metal list | |
| `get_weight_gain_loss_post()` | POST | L1442-1515 | `ret_reports_model::getLotwiseTaggedVault()` | Weight gain/loss ⚠️ diawt formula | #27 |
| `get_rate_fixed_post()` | POST | L1519-1555 | `get_rate_fixed_details()` | Rate-fixed wt/rate/amt | |
| `get_rate_unfixed_post()` | POST | L1558-1594 | `get_rate_unfixing_details()` | Rate-unfixed balance | |
| `get_accountstock_inwards_post()` | POST | L1596-1632 | `get_accountstock_inwards_details()` | Account stock ⚠️ BROKEN | #22 |
| `get_supplier_crde_post()` | POST | L1634-1695 | `ret_reports_model::getSupplierTransactionList()` | Supplier CR/DE summary | |
| `get_supplier_transcation_post()` | POST | L1697-1748 | `ret_reports_model::getSupplierTransactionList()` | Supplier txn list (duplicate caller) | |
| `get_design_stock_post()` | POST | L1750-1813 | `get_design_stock()`, `get_sub_design_stock()` | Design stock with sub-drill | |
| `get_sub_design_stock_post()` | POST | L1816-1877 | `get_sub_design_stock()` | Sub-design stock | |
| `tag_details_post()` | POST | L1879-1915 | `ret_reports_model::getTaggeditems()` | Tag item detail list | |
| `get_delayed_purchase_orders_post()` | POST | L1915-1931 | `get_delayed_purchase_orders()` | Delayed karigar POs | |
| `get_delayed_po_payments_post()` | POST | L1933-1950 | `get_delayed_po_payments()` | Overdue PO payments | |
| `get_today_delivery_po_payments_post()` | POST | L1952-1969 | `get_today_delivery_po_payments()` | Today-due POs | |
| `save_po_dashboard_comment_post()` | POST | L1971-1985 | `save_po_dashboard_comment()` | Save manager comment on PO | |
| `get_rate_cut_profit_loss_post()` | POST | L1989-2010 | `get_rate_cut_profit_loss()` | Rate-cut P&L ⚠️ inverted date | #23 |
| **🆕 NEW — Round 16 — MD Approval Dashboard** | | | | | |
| `get_md_pending_orders_post()` | POST | L2014-2018 | `get_md_pending_orders()` | POs pending MD approval (status=10) | |
| `md_approve_order_post()` | POST | L2020-2039 | `md_approve_order()` + `_send_vendor_email()` | Approve PO → status=11, email vendor | ⚠️ email-first gate |
| `md_reject_order_post()` | POST | L2041-2055 | `md_reject_order()` | Reject PO with reason → status=8 | |
| `md_bulk_approve_orders_post()` | POST | L2057-2074 | `md_bulk_approve_orders()` | Bulk approve POs, email each vendor | ⚠️ email-first gate |
| `_send_vendor_email()` *(private)* | — | L2079-2194 | `ret_purchase_approval_model`, `email_model` | Vendor email + `ret_order_email_logs` write | ⚠️ First write in dashboard |

---

## 7f. API Model Methods (ret_dashboard_api_model.php — 45 methods)

> All SQL queries use raw `$this->db->query()` with direct string interpolation — **full SQL injection risk on all filter params**.

| Method | Lines | Key Tables | Returns | Called By |
|---|---|---|---|---|
| `get_dashboard_sales_glance()` | L45-161 | `ret_billing`, `ret_bill_details`, `ret_billing_item_stones` | row_array | `get_sales_glance_post` |
| `get_sales_return_details()` | L165-231 | `ret_bill_details`, `ret_billing` | row_array | `get_dashboard_sales_glance` (internal) |
| `get_top_selling()` | L233-277 | `ret_billing`, `ret_bill_details`, `ret_taging` | result_array | `get_top_sellers_post` |
| `get_top_sellers()` | L279-327 | `ret_billing`, `ret_bill_details`, `ret_karigar` | result_array | `get_top_sellers_post` |
| `get_monthly_sales()` | L329-475 | `ret_billing`, `ret_bill_details`, `ret_financial_year` | array | `get_monthly_sales_details_post` ⚠️ N×12 queries |
| `get_monthly_sales_mobile()` | L477-582 | same as above | array | Mobile API only |
| `get_financial_year()` | L584-589 | `ret_financial_year` | result_array | `get_Financial_year_get` |
| `get_store_sales()` | L591-645 | `ret_billing`, `branch` | result_array | `get_branch_comparison_details_post` |
| `get_branch_sales()` | L647-661 | `ret_billing`, `branch` | result_array | **Orphan — no active caller** |
| `get_branch_wastage()` | L666-693 | `ret_billing`, `ret_bill_details` | result_array | ⚠️ BUG: `$id_metal` undefined |
| `get_product_sales()` | L696-738 | `ret_billing`, `ret_bill_details`, `ret_product_master` | result_array | `get_store_wise_sales_post` |
| `get_employee_sales()` | L740-792 | `ret_billing`, `employee` | result_array | `get_store_wise_sales_post` |
| `get_section_sales()` | L795-834 | `ret_billing`, `ret_section` | result_array | `get_store_wise_sales_post` |
| `get_karigar_sales()` | L836-875 | `ret_billing`, `ret_karigar` | result_array | `get_store_wise_sales_post` |
| `get_karigar_stock()` | L877-939 | `ret_taging`, `ret_karigar`, `ret_taging_stone` | result_array | `get_karigar_stock_post` |
| `get_section_stock()` | L942-987 | `ret_taging`, `ret_section` | result_array | `get_karigar_stock_post` |
| `get_product_stock()` | L988-1043 | `ret_taging`, `ret_product_master` | result_array | `get_karigar_stock_post` |
| `get_custome_wise_sale()` | L1045-1128 | `ret_billing`, `scheme_account`, `customer` | array | `get_custome_wise_sale_post()` |
| `get_dashboard_estimation()` | L1131-1176 | `ret_estimation`, `ret_estimation_items` | row_array | `get_EstimationStatus_post()` |
| `get_dashboard_virturaltag_details()` | L1178-1212 | `ret_billing`, `ret_bill_details` | row_array | `get_VitrualTag_post()` ⚠️ date ignored |
| `get_dashboard_salesreturn_det()` | L1214-1240 | `ret_bill_details`, `ret_billing` | row_array | `get_SalesReturn_post()` ⚠️ date ignored |
| `get_dashboard_lot_tag_details()` | L1242-1265 | `ret_lot_inwards`, `ret_taging` | row_array | `get_LotDetails_post()` ⚠️ date ignored |
| `get_cover_up_report()` | L1268-1414 | `ret_billing`, `ret_cover_up`, `ret_purchase_order_items` | array | `get_CoverUpReport_post()` ⚠️ date ignored |
| `get_purchase_inwards()` | L1418-1447 | `ret_purchase_order`, `ret_purchase_order_items` | row_array | `get_purchase_inwards_post()` |
| `get_delayed_purchase_orders()` | L1449-1489 | `customerorder`, `customerorderdetails` | result_array | `get_delayed_purchase_orders_post` |
| `get_delayed_po_payments()` | L1491-1598 | `ret_purchase_order`, `ret_karigar`, `ret_po_bill_payment_details` | result_array | `get_delayed_po_payments_post` |
| `get_today_delivery_po_payments()` | L1600-1677 | Same as above | result_array | `get_today_delivery_po_payments_post` |
| `get_vendor_payment()` | L1679-1699 | `ret_po_payment_detail`, `ret_po_payment`, `ret_karigar` | result_array | `get_supplier_transcation_post` |
| `get_outward_details()` | L1702-1804 | `ret_purchase_return`, `ret_branch_transfer` | result_array | `get_outward_details_post()` |
| `getMetalwiseApprovalTransactionList()` | L1806-1875 | `ret_view_supplier_approval_ledger` | result_array | `getMetalwiseApprovalTransaction_post()` |
| `get_crdr_details()` | L1878-1901 | `ret_crdr_note`, `ret_karigar` | row_array | `get_crdr_details_post()` |
| `get_qc_details()` | L1903-1927 | `ret_po_qc_issue_details`, `ret_po_qc_issue_process` | row_array | `get_qc_details_post()` |
| `get_dashboard_breakeven_details()` | L1929-2060 | `ret_billing`, `ret_breakeven_logs`, `ret_financial_year` | array | `get_FinancialStatus_post()` |
| `get_rate_fixed_details()` | L2062-2128 | `ret_po_rate_fix`, `ret_grn_entry` | array | `get_rate_fixed_post` |
| `get_rate_unfixing_details()` | L2130-2220 | `ret_purchase_order`, `ret_supplier_rate_cut` | array | `get_rate_unfixed_post` |
| `get_accountstock_inwards_details()` | L2223-2519 | `ret_branch_transfer`, `ret_brch_transfer_old_metal` | result_array | `get_accountstock_inwards_post` ⚠️ BROKEN |
| `get_rate_cut_details()` | L2531-2560 | `ret_supplier_rate_cut` | row_array | **Orphan** |
| `get_rate_cut_profit_loss()` | L2568-2646 | `ret_supplier_rate_cut`, `metal_rates` | result_array | `get_rate_cut_profit_loss_post` ⚠️ BROKEN |
| `get_profile_settings()` | L2647-2651 | `profile` | row_array | Internal utility — called by 15+ methods |
| `get_design_stock()` | ~L2650+ | `ret_taging`, `ret_product_master` | result_array | `get_design_stock_post` |
| `get_sub_design_stock()` | ~L2700+ | `ret_taging`, `ret_product_master` | result_array | `get_sub_design_stock_post`, `get_design_stock_post` |
| **🆕 NEW — Round 16** | | | | |
| `get_md_pending_orders()` | L2655-2690 | `customerorder`, `ret_karigar`, `order_status_message`, `employee`, `customerorderdetails`, `ret_product_master`, `ret_design_weight_range_wc`, `order_cart` | result_array | `get_md_pending_orders_post` |
| `md_approve_order()` | L2692-2697 | `customerorder` (UPDATE) | bool | `md_approve_order_post` |
| `md_reject_order()` | L2699-2704 | `customerorder` (UPDATE) | bool | `md_reject_order_post` |
| `md_bulk_approve_orders()` | L2706-2711 | `customerorder` (UPDATE) | int | `md_bulk_approve_orders_post` |
| `save_po_dashboard_comment()` | L2713-2718 | `ret_purchase_order` (UPDATE) | bool | `save_po_dashboard_comment_post` |
