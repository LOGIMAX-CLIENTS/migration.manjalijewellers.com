# Shared Tables — Cross-Module Risk Map

> Tables used by 2+ modules. Changes to these tables affect multiple modules.
> Last updated: 2026-03-26 (Round 2 refresh)
> Modules scanned: 28 | Shared tables: 36

## Risk Levels

- 🔴 **HIGH**: Table written by 2+ modules (data corruption risk)
- 🟡 **MEDIUM**: Table read by 2+ modules but written by only 1 (stale read risk)
- 🟢 **LOW**: Lookup/config table — read-only by all modules

## Shared Table Matrix

| Table | Owner Module | Read By | Written By | Risk | Key Columns |
|---|---|---|---|---|---|
| `ret_taging` | Tagging | Billing, Reports, Estimation, Stock Issue, Branch Transfer, Section Transfer, Sales Transfer, LOT, Old Metal, Retail Dashboard, Customer Order, Purchase, Catalog (delete guard) | Tagging, Billing, Branch Transfer, Section Transfer, Sales Transfer, Stock Issue, Old Metal, Customer Order | 🔴 | `id_tagging`, `tag_status`, `current_branch`, `gwt`, `nwt`, `id_section`, `tag_process` |
| `customer` | Customer | Billing, Estimation, Reports, Payment, Account, Dashboard, Chit Collection App, Chit Customer App, Chit Reports, Chit Dashboard, Customer Order, Other Inventory, Employee (FK) | Customer, Billing (PAN/Aadhaar), Estimation (inline create/update), Chit Collection App, Chit Customer App, Chit Dashboard, Chit Reports | 🔴 | `id_customer`, `name`, `mobile`, `pan`, `aadharid` |
| `payment` | Payment | Account, Scheme, Chit Reports, Chit Dashboard, Chit Collection App, Chit Customer App | Payment, Chit Collection App, Chit Customer App, Chit Reports | 🔴 | `id_payment`, `payment_status`, `amount`, `added_by` |
| `scheme_account` | Account | Payment, Scheme, Chit Reports, Chit Dashboard, Chit Collection App, Chit Customer App, Reports | Account, Chit Collection App, Chit Customer App, Chit Reports | 🔴 | `id_scheme_account`, `account_no`, `payment_status` |
| `ret_nontag_item` | Catalog/Stock | Reports, Branch Transfer, Section Transfer, Stock Issue, Old Metal, LOT, Retail Dashboard | Branch Transfer, Section Transfer, LOT, Old Metal | 🔴 | `id_nontag_item`, `gross_wt`, `net_wt`, `no_of_piece`, `id_section`, `id_branch` |
| `ret_billing` | Billing | Reports, Retail Dashboard, Customer (delete guard), Old Metal, Sales Transfer, Other Inventory, Chit Reports, Catalog (deposit queries) | Billing, Sales Transfer | 🔴 | `id_billing`, `bill_no`, `grand_total`, `bill_type`, `download_date` |
| `ret_bill_details` | Billing | Reports, Retail Dashboard, Old Metal, Sales Transfer | Billing, Sales Transfer, Branch Transfer | 🔴 | `id_bill_details`, `current_branch`, `transferred_to_acc_stock` |
| `ret_lot_inwards` | LOT | Tagging (324+ refs), Estimation (~12 refs), Stock Issue (~6 refs), Reports, Sales Transfer, Old Metal | LOT, Old Metal (lot_from=2/4/5) | 🔴 | `id_lot_inwards`, `lot_no`, `lot_from` |
| `ret_lot_inwards_detail` | LOT | Tagging, Estimation, Stock Issue, Reports | LOT, Old Metal | 🔴 | Balance fields, piece/weight |
| `ret_taging_status_log` | Tagging | Reports, Branch Transfer, Retail Dashboard | Tagging, Billing, Branch Transfer, Section Transfer, Sales Transfer, Stock Issue, Customer Order | 🔴 | Status history rows |
| `ret_estimation` | Estimation | Billing, Reports, Customer (delete guard), Section Transfer, Retail Dashboard | Estimation, Billing (`estbillid`) | 🔴 | `id_estimation`, `estbillid`, `is_eda` |
| `wallet_account` | Wallet | Payment, Chit Customer App, Chit Collection App | Customer, Employee, Chit Customer App | 🔴 | Balance, account number |
| `wallet_transaction` | Wallet | Chit Customer App, Reports, Chit Dashboard, Retail Dashboard | Payment, Chit Customer App | 🔴 | Transaction records |
| `customerorder` / `customerorderdetails` | Customer Order | Branch Transfer, LOT, Reports, Section Transfer, Stock Issue, Retail Dashboard | Customer Order, Branch Transfer (`current_branch`) | 🔴 | `orderstatus`, `current_branch`, `id_orderdetails` |
| `ret_other_inventory_purchase_items_details` | Other Inventory | Branch Transfer, Billing, Account | Other Inventory, Branch Transfer, Account | 🔴 | Individual packaging items, FIFO/LIFO status |
| `gift_mapping` | Other Inventory | Scheme/Chit | Other Inventory | 🔴 | OI↔Scheme linkage |
| `ret_branch_transfer` | Branch Transfer | Stock Issue, Reports, Retail Dashboard, Other Inventory | Branch Transfer, Tagging (auto-create) | 🟡 | Transfer status fields |
| `ret_karigar` | Catalog | Purchase (10+ queries), Old Metal, LOT, Reports, Other Inventory | Catalog | 🟡 | Karigar/vendor details, bank info |
| `employee` | Employee | Billing, Estimation, Reports, Payment, LOT, Stock Issue, Chit Dashboard, Chit Reports, Customer Order, Other Inventory, Branch Transfer, Retail Dashboard | Employee | 🟡 | `id_employee`, `emp_name`, `login_branches` |
| `ret_issue_receipt` | Finance | Estimation, Reports, Retail Dashboard | Issue Receipt module | 🟡 | Advance receipts, credit collection |
| `scheme` | Scheme | Account, Payment, Chit Collection App, Chit Customer App, Chit Dashboard, Reports | Scheme | 🟡 | Scheme rules, GST, type |
| `metal_rates` | Masters/Settings | Payment, Account, Chit Dashboard, Chit Customer App, Reports, Retail Dashboard | Settings | 🟡 | Rate history |
| `otp` | System | Account, Section Transfer, Stock Issue, Customer Order | Multiple modules (OTP insert/verify) | 🟡 | OTP management |
| `ret_product_master` | Catalog | Tagging, Purchase, Billing, Estimation, LOT, Reports, Branch Transfer, Section Transfer, Old Metal, Other Inventory | Catalog | 🟢 | Product definitions |
| `ret_category` | Catalog | Tagging, Purchase, Billing, Estimation, LOT, Reports, Old Metal, Other Inventory | Catalog | 🟢 | Category definitions |
| `ret_purity` | Catalog | Tagging, Purchase, Estimation, Reports, Scheme, Old Metal | Catalog | 🟢 | Purity master |
| `ret_design_master` | Catalog | Tagging, Purchase, Estimation, LOT, Reports, Branch Transfer, Old Metal | Catalog | 🟢 | Design definitions |
| `branch` | Masters/Settings | ALL modules | Masters/Settings | 🟢 | `id_branch`, `branch_name`, `is_ho` |
| `chit_settings` | Settings | ALL modules (every controller reads) | Settings | 🟢 | 60+ config flags (singleton row) |
| `ret_settings` | Retail Settings | ALL retail modules | Retail Settings | 🟢 | 90+ config keys (name/value pairs) |
| `payment_mode_details` | Payment | Payment, Chit Reports, Account | Payment, Chit Reports | 🔴 | `id_payment` FK, mode splits, amounts — ⚠️ orphaned on payment delete |
| `kyc` | Customer | Customer, Billing (PAN check), Reports | Customer, Chit Collection App, Chit Customer App | 🟡 | KYC documents — ⚠️ orphaned on customer delete (CUS-BUG-006) |
| `ret_issue_receipt` | Finance | Estimation, Reports, Retail Dashboard, Customer Order | Issue Receipt module, Customer Order (cancel refund) | 🔴 | Advance receipts — ⚠️ CO cancel writes zero-amount rows (AP-11) |
| `postdate_payment` | Payment | Payment, Chit Reports | Payment | 🟡 | PDC records — converts to payment |
| `general_advance_payment` | Payment | Payment, Reports | Payment | 🟡 | GA payment records |
| `ret_advance_utilized` | Payment | Payment, Billing | Payment | 🟡 | Advance receipt utilization tracking |
| `profile` | Masters/Settings | ALL modules, Employee, Login | Settings | 🟢 | Role definitions, feature flags |

## High-Risk Tables (Written by 2+ Modules)

> [!CAUTION]
> These tables receive writes from multiple modules. A bug fix in one module
> can corrupt data for another. Always check ALL writing modules before changing
> INSERT/UPDATE logic.

| Table | Written By | What Each Writes |
|---|---|---|
| `ret_taging` | **Tagging**: tag CRUD, status transitions / **Billing**: tag_status=1 (sold) / **Branch Transfer**: current_branch, tag_status=4 (transit) / **Section Transfer**: id_section, tag_status=14 / **Sales Transfer**: tag_status, current_branch / **Stock Issue**: tag_status=7 (issued) or 0 (receipt) / **Old Metal**: tag_process=1 / **Customer Order**: id_orderdetails |
| `customer` | **Customer**: full CRUD / **Billing**: PAN, Aadhaar, passport updates / **Estimation**: inline create/update / **Chit Collection App**: registration / **Chit Customer App**: profile update, KYC / **Chit Dashboard**: customer_edit() / **Chit Reports**: updateAccountDetails cascading |
| `payment` | **Payment**: primary CRUD / **Chit Collection App**: addPayment() / **Chit Customer App**: gateway callbacks / **Chit Reports**: cancel_payment(status=4), updatePaymentDetails |
| `scheme_account` | **Account**: primary CRUD / **Chit Collection App**: account creation / **Chit Customer App**: createAccount_post / **Chit Reports**: updateAccountDetails |
| `ret_nontag_item` | **Branch Transfer**: gross_wt/net_wt/pcs arithmetic / **Section Transfer**: stock re-assignment / **LOT**: lot receipt stock / **Old Metal**: receipt stock increments |
| `ret_billing` | **Billing**: full CRUD / **Sales Transfer**: INSERT (bill_type 13/14), UPDATE (download_date) |
| `ret_bill_details` | **Billing**: full CRUD / **Sales Transfer**: INSERT / **Branch Transfer**: UPDATE (current_branch, transferred_to_acc_stock) |
| `ret_lot_inwards` | **LOT**: standard lot creation / **Old Metal**: lot creation from melting/testing/polishing (lot_from=2/4/5) |
| `ret_taging_status_log` | **Tagging**, **Billing**, **Branch Transfer**, **Section Transfer**, **Sales Transfer**, **Stock Issue**, **Customer Order** — all INSERT log entries |
| `customerorder`/`customerorderdetails` | **Customer Order**: primary CRUD / **Branch Transfer**: current_branch updates on transit/download |
| `ret_other_inventory_purchase_items_details` | **Other Inventory**: primary CRUD / **Branch Transfer**: status transitions (0→4→0) / **Account**: gift inventory updates |
| `wallet_account` | **Customer**: on customer add / **Employee**: on employee add / **Chit Customer App**: wallet operations |

## Impact Lookup

When changing a table, check this list:

| If you change... | Check these modules... |
|---|---|
| `ret_taging` columns | Tagging, Billing, Reports, Estimation, Stock Issue, Branch Transfer, Section Transfer, Sales Transfer, LOT, Old Metal, Retail Dashboard, Customer Order, Purchase, Catalog |
| `customer` columns | Customer, Billing, Estimation, Reports, Payment, Account, Chit Collection App, Chit Customer App, Chit Dashboard, Chit Reports, Customer Order, Other Inventory |
| `payment` columns | Payment, Account, Chit Reports, Chit Dashboard, Chit Collection App, Chit Customer App |
| `scheme_account` columns | Account, Payment, Scheme, Chit Reports, Chit Dashboard, Chit Collection App, Chit Customer App |
| `ret_billing` columns | Billing, Reports, Retail Dashboard, Customer (delete guard), Old Metal, Sales Transfer, Other Inventory, Catalog |
| `ret_nontag_item` columns | Branch Transfer, Section Transfer, Stock Issue, LOT, Old Metal, Reports |
| `ret_product_master` columns | ALL retail modules (Tagging, Purchase, Billing, Estimation, LOT, Reports, Branch Transfer, Section Transfer, Old Metal, Other Inventory, Catalog) |
| `ret_estimation` columns | Estimation, Billing, Reports, Customer (guard), Section Transfer, Retail Dashboard |
| `ret_lot_inwards` columns | LOT, Tagging, Estimation, Stock Issue, Reports, Sales Transfer, Old Metal |
| `chit_settings` columns | ALL chit/scheme modules + ALL retail modules (via admin_settings_model) |
| `ret_settings` keys | ALL retail modules — 90+ config keys consumed by Billing, Estimation, Tagging, Purchase, Reports, etc. |
| `employee` columns | Employee, Login, Billing, Estimation, Reports, Payment, LOT, Stock Issue, Customer Order, Chit Dashboard, Chit Reports |
| `branch` columns | ALL modules — branch filter in virtually every query |
| `ret_karigar` columns | Purchase (10+ queries), Old Metal, LOT, Reports, Other Inventory, Catalog |
