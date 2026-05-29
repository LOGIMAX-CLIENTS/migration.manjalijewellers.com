# Shared Tables — Cross-Module Risk Map

> Tables used by 2+ modules. Changes to these tables affect multiple modules.
> Last updated: 2026-03-26
> Modules scanned: 28

## Risk Levels

- 🔴 **HIGH**: Table written by 2+ modules (data corruption risk)
- 🟡 **MEDIUM**: Table read by 2+ modules but written by only 1 (stale read risk)
- 🟢 **LOW**: Lookup/config table — read-only by all modules

## Shared Table Matrix

| Table | Owner Module | Read By | Written By | Risk | Key Columns |
|---|---|---|---|---|---|
| `ret_taging` | Tagging | Billing, Reports, Estimation, Stock Issue, Branch Transfer, Section Transfer, Sales Transfer, LOT, Old Metal, Retail Dashboard, Customer Order, Purchase | Tagging, Billing, Branch Transfer, Section Transfer, Sales Transfer, Stock Issue, Old Metal, Customer Order | 🔴 | `id_tagging`, `tag_status`, `current_branch`, `gwt`, `nwt`, `id_section`, `tag_process` |
| `customer` | Customer | Billing, Estimation, Reports, Payment, Account, Dashboard, Chit Collection App, Chit Customer App, Chit Reports, Chit Dashboard, Customer Order, Other Inventory | Customer, Billing (PAN/Aadhaar), Chit Collection App, Chit Customer App, Chit Dashboard, Chit Reports | 🔴 | `id_customer`, `name`, `mobile`, `pan`, `aadharid` |
| `payment` | Payment | Account, Scheme, Chit Reports, Chit Dashboard, Chit Collection App, Chit Customer App | Payment, Chit Collection App, Chit Customer App, Chit Reports | 🔴 | `id_payment`, `payment_status`, `amount`, `added_by` |
| `scheme_account` | Account | Payment, Scheme, Chit Reports, Chit Dashboard, Chit Collection App, Chit Customer App, Reports | Account, Chit Collection App, Chit Customer App, Chit Reports | 🔴 | `id_scheme_account`, `account_no`, `payment_status` |
| `ret_nontag_item` | Catalog/Stock | Reports, Branch Transfer, Section Transfer, Stock Issue, Old Metal, LOT | Branch Transfer, Section Transfer, LOT, Old Metal | 🔴 | `id_nontag_item`, `gross_wt`, `net_wt`, `no_of_piece`, `id_section`, `id_branch` |
| `ret_billing` | Billing | Reports, Retail Dashboard, Customer, Old Metal, Sales Transfer, Other Inventory, Chit Reports | Billing, Sales Transfer | 🔴 | `id_billing`, `bill_no`, `grand_total`, `bill_type`, `download_date` |
| `ret_bill_details` | Billing | Reports, Retail Dashboard, Old Metal, Sales Transfer | Billing, Sales Transfer, Branch Transfer | 🔴 | `id_bill_details`, `current_branch`, `transferred_to_acc_stock` |
| `ret_lot_inwards` | LOT | Tagging, Estimation, Stock Issue, Reports, Sales Transfer, Old Metal | LOT, Old Metal | 🔴 | `id_lot_inwards`, `lot_no`, `lot_from` |
| `ret_lot_inwards_detail` | LOT | Tagging, Estimation, Stock Issue, Reports | LOT, Old Metal | 🔴 | Balance fields, piece/weight |
| `ret_taging_status_log` | Tagging | Reports, Branch Transfer | Tagging, Billing, Branch Transfer, Section Transfer, Sales Transfer, Stock Issue, Customer Order | 🔴 | Status history rows |
| `ret_estimation` | Estimation | Billing, Reports, Customer (delete guard), Section Transfer, Retail Dashboard | Estimation, Billing (`estbillid`) | 🔴 | `id_estimation`, `estbillid`, `is_eda` |
| `wallet_account` | Wallet | Payment, Chit Customer App, Chit Collection App | Customer, Employee, Chit Customer App | 🔴 | Balance, account number |
| `wallet_transaction` | Wallet | Chit Customer App, Reports, Chit Dashboard | Payment, Chit Customer App | 🔴 | Transaction records |
| `ret_branch_transfer` | Branch Transfer | Stock Issue, Reports, Retail Dashboard, Other Inventory | Branch Transfer, Tagging (auto-create) | 🟡 | Transfer status fields |
| `ret_karigar` | Catalog | Purchase, Old Metal, LOT, Reports, Other Inventory | Catalog | 🟡 | Karigar/vendor details, bank info |
| `employee` | Employee | Billing, Estimation, Reports, Payment, LOT, Stock Issue, Chit Dashboard, Chit Reports, Customer Order, Other Inventory, Branch Transfer | Employee | 🟡 | `id_employee`, `emp_name`, `login_branches` |
| `branch` | Masters/Settings | ALL modules | Masters/Settings | 🟢 | `id_branch`, `branch_name`, `is_ho` |
| `chit_settings` | Settings | ALL modules | Settings | 🟢 | 60+ config flags (singleton row) |
| `ret_settings` | Retail Settings | ALL retail modules | Retail Settings | 🟢 | 90+ config keys (name/value pairs) |
| `metal_rates` | Masters/Settings | Payment, Account, Chit Dashboard, Chit Customer App, Reports | Settings | 🟡 | Rate history |
| `scheme` | Scheme | Account, Payment, Chit Collection App, Chit Customer App, Chit Dashboard, Reports | Scheme | 🟡 | Scheme rules, GST, type |
| `ret_product_master` | Catalog | Tagging, Purchase, Billing, Estimation, LOT, Reports, Branch Transfer, Section Transfer, Old Metal, Other Inventory | Catalog | 🟢 | Product definitions |
| `ret_category` | Catalog | Tagging, Purchase, Billing, Estimation, LOT, Reports, Old Metal, Other Inventory | Catalog | 🟢 | Category definitions |
| `ret_purity` | Catalog | Tagging, Purchase, Estimation, Reports, Scheme, Old Metal | Catalog | 🟢 | Purity master |
| `ret_design_master` | Catalog | Tagging, Purchase, Estimation, LOT, Reports, Branch Transfer, Old Metal | Catalog | 🟢 | Design definitions |
| `customerorder` / `customerorderdetails` | Customer Order | Branch Transfer, LOT, Reports, Section Transfer, Stock Issue, Retail Dashboard | Customer Order, Branch Transfer (`current_branch`) | 🔴 | Order items, `orderstatus`, `current_branch` |
| `ret_issue_receipt` | Finance | Estimation, Reports, Retail Dashboard | Issue Receipt module | 🟡 | Advance receipts, credit collection |
| `gift_mapping` | Other Inventory | Scheme/Chit | Other Inventory | 🔴 | OI↔Scheme linkage |
| `ret_other_inventory_purchase_items_details` | Other Inventory | Branch Transfer, Billing, Account | Other Inventory, Branch Transfer, Account | 🔴 | Individual packaging items, FIFO/LIFO status |
| `profile` | Masters/Settings | ALL modules, Employee, Login | Settings | 🟢 | Role definitions, feature flags |
| `otp` | System | Account, Section Transfer, Stock Issue, Customer Order | Multiple modules (OTP insert/verify) | 🟡 | OTP management |

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
| `ret_lot_inwards` | **LOT**: standard lot creation / **Old Metal**: lot creation from melting/testing/polishing (lot_from=2/4/5) |
| `ret_taging_status_log` | **Tagging**, **Billing**, **Branch Transfer**, **Section Transfer**, **Sales Transfer**, **Stock Issue**, **Customer Order** — all INSERT log entries |
| `customerorder` / `customerorderdetails` | **Customer Order**: primary CRUD / **Branch Transfer**: current_branch updates on transit/download |

## Impact Lookup

When changing a table, check this list:

| If you change... | Check these modules... |
|---|---|
| `ret_taging` columns | Tagging, Billing, Reports, Estimation, Stock Issue, Branch Transfer, Section Transfer, Sales Transfer, LOT, Old Metal, Retail Dashboard, Customer Order, Purchase |
| `customer` columns | Customer, Billing, Estimation, Reports, Payment, Account, Chit Collection App, Chit Customer App, Chit Dashboard, Chit Reports, Customer Order, Other Inventory |
| `payment` columns | Payment, Account, Chit Reports, Chit Dashboard, Chit Collection App, Chit Customer App |
| `scheme_account` columns | Account, Payment, Scheme, Chit Reports, Chit Dashboard, Chit Collection App, Chit Customer App |
| `ret_billing` columns | Billing, Reports, Retail Dashboard, Customer (delete guard), Old Metal, Sales Transfer, Other Inventory |
| `ret_nontag_item` columns | Branch Transfer, Section Transfer, Stock Issue, LOT, Old Metal, Reports |
| `ret_product_master` columns | ALL retail modules (Tagging, Purchase, Billing, Estimation, LOT, Reports, Branch Transfer, Section Transfer, Old Metal, Other Inventory, Catalog) |
| `ret_estimation` columns | Estimation, Billing, Reports, Customer (guard), Section Transfer, Retail Dashboard |
| `ret_lot_inwards` columns | LOT, Tagging, Estimation, Stock Issue, Reports, Sales Transfer, Old Metal |
| `chit_settings` columns | ALL chit/scheme modules + ALL retail modules (via admin_settings_model) |
| `ret_settings` keys | ALL retail modules — 90+ config keys consumed by Billing, Estimation, Tagging, Purchase, Reports, etc. |
| `employee` columns | Employee, Login, Billing, Estimation, Reports, Payment, LOT, Stock Issue, Customer Order, Chit Dashboard, Chit Reports |
| `branch` columns | ALL modules — branch filter in virtually every query |
| `ret_karigar` columns | Purchase (10+ queries), Old Metal, LOT, Reports, Other Inventory, Catalog |
