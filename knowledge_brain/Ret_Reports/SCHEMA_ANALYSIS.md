# Ret_Reports — Schema Analysis

> **Module**: Ret_Reports
> **Last Updated**: 2026-03-26 — Round 8 (header sync)
> **Total Tables**: ~182 read, 1 written

---

## Part A: Owned Tables (Written by this Module)

> This module owns effectively **zero** tables — it is a pure reporting/read module.
> The only write operation is `UPDATE ret_taging.tag_mark` via `update_green_tag()`, but `ret_taging` is owned by the Tagging module.

| Table | Write Op | Fields Modified | Method |
|---|---|---|---|
| `ret_taging` (Tagging-owned) | UPDATE | `tag_mark`, `green_tag_date`, `green_tag_marked_by`, `unmark_by`, `unmark_date` | `update_green_tag()` L183 |

---

## Part B: Referenced Tables (Read by this Module)

### B1: Core Transaction Tables

| Table | Read Frequency | Key Columns Used | Module Owner |
|---|---|---|---|
| `ret_billing` | Very High | `bill_id`, `bill_no`, `bill_date`, `net_amount`, `id_branch`, `id_customer`, `bill_type`, `gst_type` | Billing |
| `ret_bill_details` | Very High | `bill_id`, `tag_id`, `item_total`, `gross_wt`, `net_wt`, `dia_wt`, `making_charges` | Billing |
| `ret_billing_payment` | High | `bill_id`, `pay_mode`, `pay_amount`, `card_amount`, `cheque_amount`, `net_banking`, `dep_id` | Billing |
| `ret_billing_advance` | Medium | `bill_id`, `advance_amount`, `id_customer` | Billing |
| `ret_billing_chit_utilization` | Low | `bill_id`, `scheme_id`, `chit_amount` | Billing |
| `ret_billing_gift_voucher_details` | Low | `bill_id`, `voucher_amount` | Billing |
| `ret_billing_item_stones` | Medium | `bill_id`, `stone_wt`, `stone_amount` | Billing |
| `ret_bill_old_metal_sale_details` | Medium | `bill_id`, `metal_type`, `metal_wt`, `metal_amount` | Billing |
| `ret_bill_return_details` | Medium | `bill_id`, `return_amount` | Billing |
| `ret_bill_pay_device` | Medium | `bill_id`, `device_name`, `device_amount` | Billing |
| `ret_bill_delivery` | Low | `bill_id`, `delivery_date`, `delivery_status` | Billing |
| `ret_bill_duplicate_copy` | Low | `bill_id`, `copy_date` | Billing |

### B2: Tagging/Inventory Tables

| Table | Read Frequency | Key Columns Used | Module Owner |
|---|---|---|---|
| `ret_taging` | Very High | `tag_id`, `tag_code`, `gross_wt`, `net_wt`, `dia_wt`, `tag_status`, `tag_mark`, `entry_date`, `id_branch`, `id_section`, `id_product`, `id_design`, `id_sub_design`, `stone_type` | Tagging |
| `ret_taging_stone` | High | `tag_id`, `stone_wt`, `stone_amt` | Tagging |
| `ret_taging_status_log` | Medium | `tag_id`, `status`, `log_date` | Tagging |
| `ret_taging_huid` | Low | `tag_id`, `huid_no` | Tagging |
| `ret_taging_images` | Low | `tag_id`, `image_path` | Tagging |
| `ret_tag_other_metals` | Low | `tag_id`, `metal_type`, `metal_wt` | Tagging |
| `ret_tag_scan` | Low | `tag_id`, `scan_date` | Tagging |
| `ret_tag_scanned` | Low | `tag_id`, `scanned_date` | Tagging |

### B3: Purchase/PO Tables

| Table | Read Frequency | Key Columns Used | Module Owner |
|---|---|---|---|
| `ret_purchase_order` | High | `po_id`, `po_no`, `po_date`, `id_supplier`, `id_branch` | Purchase |
| `ret_purchase_order_items` | High | `po_id`, `item_gross_wt`, `item_net_wt` | Purchase |
| `ret_po_payment` / `ret_po_payment_detail` | Medium | `po_id`, `pay_amount` | Purchase |
| `ret_po_rate_fix` | Medium | `po_id`, `rate`, `fix_date`, `unfix_date` | Purchase |
| `ret_po_qc_issue_process` / `ret_po_qc_issue_details` | Low | `po_id`, `qc_status` | Purchase |
| `ret_po_halmark_process` | Low | `po_id`, `hm_status` | Purchase |
| `ret_po_stone_items` | Low | `po_id`, `stone_wt` | Purchase |
| `ret_po_other_item` | Low | `po_id` | Purchase |
| `ret_po_bill_payment_details` | Low | `po_id` | Purchase |

### B4: GRN Tables

| Table | Read Frequency | Key Columns Used | Module Owner |
|---|---|---|---|
| `ret_grn_entry` | Medium | `grn_id`, `grn_no`, `grn_date`, `po_id` | GRN |
| `ret_grn_items` | Medium | `grn_id`, `gross_wt`, `net_wt` | GRN |
| `ret_grn_item_stone` | Low | `grn_id`, `stone_wt` | GRN |
| `ret_grn_other_charges` | Low | `grn_id` | GRN |
| `ret_grn_other_metals` | Low | `grn_id` | GRN |

### B5: LOT Tables

| Table | Read Frequency | Key Columns Used | Module Owner |
|---|---|---|---|
| `ret_lot_inwards` | High | `id_inward`, `lot_no`, `inward_date`, `gross_wt`, `net_wt` | LOT |
| `ret_lot_inwards_detail` | High | `id_inward`, `tag_id` | LOT |
| `ret_lot_inwards_stone_detail` | Low | `id_inward` | LOT |
| `ret_lot_merge` | Low | `merge_id`, `from_lot`, `to_lot` | LOT |
| `ret_lot_split_details` | Low | `split_id` | LOT |

### B6: Branch Transfer Tables

| Table | Read Frequency | Key Columns Used | Module Owner |
|---|---|---|---|
| `ret_branch_transfer` | High | `bt_id`, `bt_date`, `from_branch`, `to_branch`, `bt_status` | Branch Transfer |
| `ret_brch_transfer_tag_items` | High | `bt_id`, `tag_id`, `gross_wt`, `net_wt` | Branch Transfer |
| `ret_brch_transfer_non_tag_items` | Medium | `bt_id` | Branch Transfer |
| `ret_brch_transfer_old_metal` | Low | `bt_id` | Branch Transfer |
| `ret_branch_transfer_other_inventory` | Low | `bt_id` | Branch Transfer |

### B7: Issue Receipt Tables

| Table | Read Frequency | Key Columns Used | Module Owner |
|---|---|---|---|
| `ret_issue_receipt` | Medium | `ir_id`, `ir_date`, `ir_type`, `id_customer` | Issue Receipt |
| `ret_issue_rcpt_payment` | Medium | `ir_id`, `pay_mode`, `pay_amount` | Issue Receipt |
| `ret_issue_credit_collection_details` | Low | `ir_id` | Issue Receipt |
| `ret_issue_receipt_advance_adj` | Low | `ir_id` | Issue Receipt |
| `ret_metai_issue_receipt_details` | Low | `ir_id` | Issue Receipt |

### B8: Karigar/Smith Tables

| Table | Read Frequency | Key Columns Used | Module Owner |
|---|---|---|---|
| `ret_karigar` | Medium | `id_karigar`, `karigar_name` | Karigar |
| `ret_karigar_metal_issue` / `_details` | Medium | `id_issue`, `metal_wt`, `issue_date` | Karigar |
| `ret_karigar_stones` | Low | `tag_id` | Karigar |
| `ret_karikar_items_wastage` | Low | `tag_id` | Karigar |

### B9: Master/Reference Tables

| Table | Read Frequency | Key Columns Used | Module Owner |
|---|---|---|---|
| `customer` | High | `id_customer`, `customer_name`, `mobile`, `email` | Customer |
| `branch` | High | `id_branch`, `branch_name` | Settings |
| `employee` | Medium | `id_employee`, `emp_name` | HR |
| `ret_category` | High | `id_category`, `category_name` | Catalog |
| `ret_product_master` | High | `id_product`, `product_name`, `stone_type` | Catalog |
| `ret_design_master` | High | `id_design`, `design_name` | Catalog |
| `ret_sub_design_master` | Medium | `id_sub_design`, `sub_design_name` | Catalog |
| `ret_section` | High | `id_section`, `section_name` | Catalog |
| `ret_settings` | Medium | Various settings keys | Settings |
| `ret_old_metal_type` | Low | `id_type`, `type_name` | Old Metal |
| `bank` | Low | `id_bank`, `bank_name` | Settings |
| `metal` | Low | `id_metal`, `metal_name` | Settings |
| `metal_rates` | Low | `rate_date`, `rate` | Settings |

### B10: View Tables (Database Views)

| View | Purpose | Module Owner |
|---|---|---|
| `ret_view_customer_ledger` | Customer ledger view | Ledger |
| `ret_view_smith_combined_ledger` | Smith combined ledger | Ledger |
| `ret_view_smith_ledger` | Smith basic ledger | Ledger |
| `ret_view_smith_metal_amt_ledger` | Smith metal amount | Ledger |
| `ret_view_smith_metal_ledger` | Smith metal quantity | Ledger |
| `ret_view_supplier_amount_ledger` | Supplier amount | Ledger |
| `ret_view_supplier_approval_ledger` | Supplier approval | Ledger |
| `ret_view_supplier_ledger` | Supplier basic ledger | Ledger |
| `ret_view_supplier_metal_ledger` | Supplier metal | Ledger |
| `view_reorder_details` | Reorder view | Catalog |

### B11: Tables Found in R4 Audit (Previously Missing)

| Table | Read Frequency | Key Columns Used | Module Owner |
|---|---|---|---|
| `profile` | Medium | `id_profile`, `allow_bill_type` | Settings |
| `ret_advance_utilized` | Medium | `bill_id`, `utilized_amt`, `adv_utilized_type` | Billing |
| `ret_branch_floor_counter` | High | `counter_id`, `counter_name`, `floor_id` | Settings |
| `ret_purity` | Low | `id_purity`, `purity_name` | Catalog |
| `ret_stone` | Medium | `stone_id`, `stone_type`, `uom_id` | Catalog |
| `ret_uom` | Medium | `uom_id`, `divided_by_value` | Catalog |
| `ret_stone_type` | Low | `id_stone_type`, `stone_type` | Catalog |
| `ret_old_metal_category` | Medium | `id_old_metal_cat`, `old_metal_cat` | Old Metal |
| `ret_reorder_settings` | Low | `reorder_min`, `id_product`, `id_branch` | Catalog |
| `ret_partlysold` | Low | `tag_id`, `sold_bill_det_id` | Billing |
| `ret_nontag_receipt` | Low | `lot_id`, `id_lot_inward_detail`, `id_product`, `pcs`, `grs_wt`, `net_wt` | LOT |
| `ret_estimation_old_metal_sale_details` | Medium | `old_metal_sale_id`, `est_id`, `id_old_metal_category` | Estimation |

---

## Schema Risks

| Risk | Description | Impact |
|---|---|---|
| **Column rename in ret_taging** | ~50 model methods reference `ret_taging` columns by name | Breaks most reports |
| **View definition change** | 10 DB views used — view column changes not tracked by migration | Silent data loss in reports |
| **Missing indexes** | Reports with date range filters need indexes on `bill_date`, `entry_date`, `bt_date`, `ir_date` | Performance degradation |
| **Table ownership ambiguity** | `ret_taging` is written by Reports but owned by Tagging | Conflicting updates possible |
