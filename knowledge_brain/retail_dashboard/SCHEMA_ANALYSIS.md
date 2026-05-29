# SCHEMA ANALYSIS — Retail Dashboard
> Generated: 2026-03-16 | Module: Retail Dashboard

---

## Part A: Tables Owned/Primarily Used by Dashboard

> The Dashboard module does NOT own any tables — it is a pure-read aggregation layer.

---

## Part B: Tables Referenced (Read) by Dashboard

### Core Billing Tables

#### `ret_billing` (Primary Source)
| Key Column | Purpose | Notes |
|---|---|---|
| `bill_id` | PK | |
| `bill_no` | Bill number (display) | |
| `bill_date` | Transaction date | Used in all BETWEEN filters |
| `bill_status` | 1=valid, 0=draft/cancelled | Most queries filter `bill_status=1` |
| `bill_type` | 1-14 (see RULE-RETDASH-002) | Controls bill classification |
| `id_branch` | Branch FK | Branch-filter column |
| `bill_cus_id` | Customer FK | |
| `tot_bill_amount` | Total bill value | |
| `tot_amt_received` | Amount paid | |
| `is_credit` | 1=credit sale | |
| `ref_bill_id` | Parent bill (credit bill) | |
| `round_off_amt` | Rounding amount | |
| `handling_charges` | Handling fee | |

#### `ret_bill_details`
| Key Column | Purpose |
|---|---|
| `bill_det_id` | PK |
| `bill_id` | FK to ret_billing |
| `tag_id` | FK to ret_taging |
| `product_id` | FK to ret_product_master |
| `design_id` | FK to ret_design_master |
| `esti_item_id` | FK to ret_estimation_items |
| `gross_wt` | Gross weight |
| `net_wt` | Net weight |
| `piece` | Piece count |
| `item_cost` | Item amount |
| `item_total_tax` | Tax amount |
| `bill_discount` | Discount |
| `status` | 2=returned |
| `is_partial_sale` | 1=partial/split sale |
| `item_type` | 0=normal, NULL=normal |

#### `ret_billing_payment`
| Key Column | Purpose |
|---|---|
| `bill_id` | FK |
| `payment_mode` | CASH/CC/DC/NB/CHQ |
| `payment_amount` | Amount |
| `payment_status` | 1=valid |
| `type` | 1=received, 2=issued (for CHQ) |

#### `ret_bill_old_metal_sale_details`
| Key Column | Purpose |
|---|---|
| `old_metal_sale_id` | PK |
| `bill_id` | FK |
| `metal_type` | 1=Gold, 2=Silver |
| `gross_wt` / `net_wt` | Weights |
| `rate` | Amount paid |

---

### Estimation Tables

#### `ret_estimation`
| Key Column | Purpose |
|---|---|
| `estimation_id` | PK |
| `esti_no` | Display number |
| `estimation_datetime` | Date |
| `created_time` | Created time |
| `cus_id` | Customer FK |
| `id_branch` | Branch FK |
| `esti_for` | 1=retail estimation |
| `total_cost` | Total |
| `discount` | Discount |

#### `ret_estimation_items`
| Key Column | Purpose |
|---|---|
| `est_item_id` | PK |
| `esti_id` | FK to ret_estimation |
| `purchase_status` | 0=unbilled, 1=sold, 2=returned |
| `item_type` | 0=tag, 1=catalog, 2=custom/home |
| `item_cost` | Amount |
| `gross_wt` | Weight |

---

### Tagging Tables

#### `ret_taging`
| Key Column | Purpose |
|---|---|
| `tag_id` | PK |
| `tag_code` | Display code |
| `product_id` | FK |
| `design_id` | FK |
| `current_branch` | Current holding branch |
| `id_branch` | Origin branch |
| `tag_status` | 0=available, 1=sold, 2=removed, 4=in-transit |
| `tag_mark` | 1=green tag |
| `tag_datetime` | Created date |
| `gross_wt` / `net_wt` / `piece` | Weight/quantity |

#### `ret_taging_status_log`
| Key Column | Purpose |
|---|---|
| `id_tag_status_log` | PK (used for latest-status trick) |
| `tag_id` | FK to ret_taging |
| `status` | See RULE-RETDASH-013 |
| `from_branch` / `to_branch` | Transfer branches |
| `date` | Status change date |

---

### Lot Tables

#### `ret_lot_inwards`
| Key Column | Purpose |
|---|---|
| `lot_no` | PK/identifier |
| `lot_date` | Date |
| `created_branch` | Branch that received lot |
| `stock_type` | 2=non-tagged stock |

#### `ret_lot_inwards_detail`
| Key Column | Purpose |
|---|---|
| `lot_no` | FK |
| `no_of_piece` | Piece count |
| `gross_wt` | Weight |

---

### Order Tables

#### `customerorder`
| Key Column | Purpose |
|---|---|
| `id_customerorder` | PK |
| `order_for` | 1=karigar, 2=customer |
| `order_from` | Branch FK |
| `order_date` | Date |

#### `customerorderdetails`
| Key Column | Purpose |
|---|---|
| `id_customerorder` | FK |
| `orderstatus` | 0-5 (see RULE-RETDASH-008) |
| `smith_due_date` | Karigar delivery date |
| `cus_due_date` | Customer delivery date |
| `delivered_date` | Actual delivery date |
| `totalitems` | Piece count |
| `branch_id` | FK |

#### `order_cart`
| Key Column | Purpose |
|---|---|
| `id_branch` | Branch FK |
| `orderstatus` | 0=in cart, 1=placed |
| `id_product` / `design_no` / `id_wt_range` | Product keys |
| `created_on` | Date |

---

### Finance Tables

#### `gift_card`
| Key Column | Purpose |
|---|---|
| `id_branch` | Branch FK |
| `amount` | Card value |
| `status` | 0=issued, 1=used, 2=fully utilized |
| `free_card` | 2=sold (not free) |
| `date_add` | Date |

#### `ret_issue_receipt`
| Key Column | Purpose |
|---|---|
| `id_issue_receipt` | PK |
| `type` | 1=payment out, 2=advance in |
| `issue_type` | 1=other expense |
| `receipt_type` | Various types |
| `bill_status` | 1=valid |
| `id_branch` | Branch FK |
| `bill_date` | Date |
| `amount` | Amount |
| `weight` / `rate_per_gram` | Metal advance fields |

#### `ret_issue_rcpt_payment`
| Key Column | Purpose |
|---|---|
| `id_issue_rcpt` | FK to ret_issue_receipt |
| `payment_mode` | CASH/CC/DC/NB/CHQ |
| `payment_amount` | Amount |
| `payment_status` | 1=valid |
| `type` | 1=received, 2=paid |

---

### Reorder Settings

#### `ret_reorder_settings`
| Key Column | Purpose |
|---|---|
| `id_product` | Product FK |
| `id_design` | Design FK |
| `id_wt_range` | Weight range FK |
| `id_branch` | Branch FK |
| `min_pcs` | Reorder alert threshold |
| `max_pcs` | Max stock target |

---

### Lookup / Master Tables

| Table | Used For |
|---|---|
| `branch` | Branch name lookups |
| `metal` | metal_code, metal name (Gold/Silver) |
| `metal_rates` | Daily rate (goldrate_24ct, updatetime) |
| `ret_product_master` | product_name, cat_id, metal_type, sales_mode |
| `ret_category` | name, id_metal, id_ret_category |
| `ret_design_master` | design_name, id_size |
| `ret_size` | size value + name |
| `ret_weight` | from_weight, to_weight, id_uom |
| `ret_uom` | uom_name |
| `ret_stone` | stone_name, stone_type (1=diamond, 2=other) |
| `ret_settings` | Key-value config (incentive rates) |
| `customer` | firstname, mobile, id_branch, date_add |
| `ledger_master` | ledger_name, min_balance, opening_balance, opening_date |

---

## Part C: API Model Tables (discovered Round 5)

> These tables are referenced exclusively or primarily by `ret_dashboard_api_model.php` and `admin_ret_dashboard_api.php`.

### Purchase / Karigar Order Tables

| Table | Key Columns | Notes |
|---|---|---|
| `ret_purchase_order` | `po_id`, `po_karigar_id`, `po_date`, `po_delivery_date`, `tot_purchase_amt`, `tot_purchase_wt`, `bill_status`, `is_approved`, `pur_approval_type`, `po_ref_no`, `po_grn_id`, `isratefixed`, `is_suspense_stock` | Central karigar PO table — bill_status=1 means approved/complete |
| `ret_purchase_order_items` | `po_item_id`, `po_item_po_id`, `po_item_pro_id`, `po_item_cat_id`, `gross_wt`, `net_wt`, `no_of_pcs`, `item_pure_wt`, `purchase_touch`, `less_wt` | Line items per PO |
| `ret_purchase_return` | `pur_return_id`, `po_id`, `bill_date`, `bill_status`, `purchase_type` | Purchase return header (purchase_type: 1=B2B Sales, 2=Return) |
| `ret_purchase_return_items` | `pur_ret_itm_id`, `pur_ret_id`, `pur_ret_po_item_id`, `pur_ret_gwt`, `pur_ret_nwt`, `pur_ret_pur_wt` | Return line items |
| `ret_purchase_return_stone_items` | `pur_ret_return_id`, `ret_stone_id`, `ret_stone_wt`, `ret_stone_amount` | Stone items in returns |
| `ret_karigar_metal_issue` | `met_issue_id`, `po_id`, `met_issue_date`, `bill_status` | Metal issued to karigar header |
| `ret_karigar_metal_issue_details` | `issue_met_parent_id`, `issu_met_pro_id`, `issue_metal_wt`, `issue_metal_pur_wt`, `po_item_id`, `stone_type` | Metal issue line items |
| `customerorder` | `id_customerorder`, `id_karigar`, `order_date`, `order_status`, `order_type`, `order_from`, `pur_no`, `order_for` | Customer/karigar order header |
| `customerorderdetails` | `id_orderdetails`, `id_customerorder`, `id_product`, `smith_due_date`, `totalitems`, `orderstatus`, `design_no`, `weight` | Order line items + due-date |

### Rate Fix / Rate Cut / GRN Tables

| Table | Key Columns | Notes |
|---|---|---|
| `ret_po_rate_fix` | `rate_fix_po_item_id`, `rate_fix_wt`, `rate_fix_rate`, `total_amount`, `bill_status` | Rate-fixed allocation entries |
| `ret_supplier_rate_cut` | `id_supplier_rate_cut`, `po_id`, `id_karigar`, `weight`, `amount`, `rate_per_gram`, `id_metal`, `id_branch`, `date_add`, `ref_no`, `rate_cut_type`, `conversion_type`, `status` | Supplier rate-cut contracts. `rate_cut_type=2, conversion_type=1` = fixed rate; `conversion_type=2` = unfixed |
| `ret_crdr_note` | `crdrid`, `supid`, `transtype` (1=credit, 2=debit), `transamount`, `transdate`, `crdr_status`, `po_id`, `id_supplier_rate_cut`, `naration` | Credit/debit note adjustments on POs |
| `ret_grn_entry` | `grn_id`, `grn_ref_no`, `grn_date`, `grn_purchase_amt` | GRN (goods received note) header |

### Payment Tables

| Table | Key Columns | Notes |
|---|---|---|
| `ret_po_payment` | `pay_id`, `pay_sup_id`, `pay_refno`, `pay_status` (1=active), `pay_create_on` | PO payment header |
| `ret_po_payment_detail` | `pay_id`, `pay_mode` (CSH/RTGS/NEFT), `payment_amount`, `id_bank`, `ref_no` | PO payment mode breakdown |
| `ret_po_bill_payment_details` | `po_id`, `pay_id`, `total_amount` | Allocation of payments to PO bills |
| `bank` | `id_bank`, `bank_name` | Bank master |

### QC Tables

| Table | Key Columns | Notes |
|---|---|---|
| `ret_po_qc_issue_details` | `qc_process_id`, `po_item_id`, `failed_pcs`, `failed_gwt`, `failed_lwt`, `failed_nwt` | QC failure details per PO item |
| `ret_po_qc_issue_process` | `qc_process_id`, `created_at` | QC process header (date stamp) |
| `ret_po_stone_items` | `po_item_id`, `po_stone_id`, `po_stone_wt`, `po_stone_amount` | Stones in PO items |

### Finance / Hedging Tables

| Table | Key Columns | Notes |
|---|---|---|
| `ret_breakeven_logs` | `brevn_log_id`, `brevn_log_branchid`, `brevn_log_gold_val`, `brevn_log_silver_val`, `brevn_log_dia_val` | Daily breakeven target per branch |
| `ret_cover_up` | `id_coverup`, `weight`, `id_metal`, `created_on` | Cover-up/hedging position entries |
| `ret_view_supplier_approval_ledger` | VIEW: `customer_id`, `trans_date`, `trans_type` (1=credit, 2=debit), `trans_rec_type`, `trans_amount`, `purewt`, `id_metal` | Denormalized supplier approval ledger |
| `metal_rates` | `id_metal_rate`, `goldrate_24ct`, `updatetime` | Market gold/silver rate per timestamp |
| `payment_mode_details` | `id_payment`, `payment_mode` (CSH/FP), `payment_amount`, `payment_status`, `is_active` | Chit payment mode breakdown |
| `payment` | `id_payment`, `id_branch`, `metal_rate`, `payment_status`, `custom_entry_date`, `payment_mode` | Chit payment header |
| `chit_settings` | (JOIN only, to identify chit payments vs regular) | Global chit scheme settings |
| `scheme_account` | `id_customer` | Chit scheme account (links customer to scheme) |

### Branch Transfer Tables

| Table | Key Columns | Notes |
|---|---|---|
| `ret_branch_transfer` | `branch_transfer_id`, `transfer_from_branch`, `transfer_item_type` (1=tag, 2=non-tag, 3=old-metal), `status` (4=complete), `is_other_issue`, `dwnload_datetime`, `branch_trans_code` | BT header |
| `ret_brch_transfer_old_metal` | `transfer_id`, `tag_id`, `sold_bill_det_id`, `gross_wt`, `net_wt`, `item_type` (2=return, 3=partly), `is_non_tag` | Old-metal items within a BT |
| `ret_brch_transfer_tag_items` | `transfer_id`, `tag_id` | Tag items within a BT |
| `ret_bt_order_log` | `branch_transfer_id`, `id_orderdetails` | Order log linked to BT |
| `ret_billing_item_stones` | `bill_det_id`, `stone_id`, `wt`, `price`, `uom_id` | Stone details on a bill line item |
| `ret_purity` | `id_purity`, `purity` (numeric %, e.g. 91.6) | Purity master |
