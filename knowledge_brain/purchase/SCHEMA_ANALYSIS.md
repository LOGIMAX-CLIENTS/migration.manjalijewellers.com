# PURCHASE MODULE — SCHEMA ANALYSIS
> **Module:** Purchase | **Version:** 1.0 | **Date:** 2026-02-23

---

## Part A: Owned Tables (Primary Purchase Tables)

### A1. `customerorder` — Purchase Order Header
| Column | Type (Inferred) | Purpose |
|---|---|---|
| `id_customerorder` | INT PK AUTO | Primary key |
| `pur_no` | VARCHAR | Purchase order number (FY-XXXX) |
| `fin_year_code` | VARCHAR | Financial year code |
| `order_status` | TINYINT | 0=Open, 1=Completed, 2=Cancelled |
| `order_type` | TINYINT | 1=Stock, 2=Customer Order, 3=Repair |
| `order_pcs` | INT | Total ordered pieces |
| `order_approx_wt` | DECIMAL | Approx weight ordered |
| `order_for` | INT | Order purpose identifier |
| `id_karigar` | INT FK | Vendor/karigar reference |
| `cus_ord_ref` | INT NULL FK | Customer order reference (if type=2) |
| `order_date` | DATETIME | Order creation date |
| `createdon` | DATETIME | Record created timestamp |
| `order_taken_by` | INT FK | User who created order |
| `rate_type` | TINYINT | 1=Per-piece, 2=Per-gram |

### A2. `customerorderdetails` — Purchase Order Line Items
| Column | Type | Purpose |
|---|---|---|
| `id_orderdetails` | INT PK | Primary key |
| `id_customerorder` | INT FK | Parent order |
| `id_product` | INT FK | Product |
| `cus_orderdet_ref` | INT NULL FK | Customer order detail ref |
| `design_no` | INT NULL FK | Design |
| `id_sub_design` | INT NULL FK | Sub-design |
| `id_weight_range` | INT NULL FK | Weight range |
| `weight` | DECIMAL | Target weight |
| `size` | VARCHAR NULL | Size |
| `less_wt` | DECIMAL NULL | Less weight |
| `no_of_pcs` | INT | Number of pieces |

### A3. `ret_purchase_order` — Supplier Bill Entry Header
| Column | Type | Purpose |
|---|---|---|
| `po_id` | INT PK AUTO | Primary key |
| `po_ref_no` | VARCHAR | Bill reference (P-XXXX / PM-XXXX / PA-XXXX) |
| `po_date` | DATE | Bill date |
| `id_karigar` | INT FK | Vendor/karigar |
| `bill_status` | TINYINT | 0=Active, 1=Completed, 2=Cancelled |
| `pur_approval_type` | TINYINT | 0=Pending, 1=Approved |
| `is_suspense_stock` | TINYINT | 0=Normal, 1=Suspense |
| `gst_bill_type` | VARCHAR | P/PM/PA |
| `grand_total` | DECIMAL | Bill grand total |
| `grand_total_paid` | DECIMAL | Total paid so far |
| `ewaybillno` | VARCHAR | E-way bill number |
| `cancel_reason` | TEXT NULL | Cancel reason if cancelled |
| `cancelled_date` | DATETIME NULL | Cancel date |
| `cancelled_by` | INT NULL FK | Who cancelled |
| `fin_year_code` | VARCHAR | Financial year |

### A4. `ret_purchase_order_item` — Bill Line Items
| Column | Type | Purpose |
|---|---|---|
| `po_item_id` | INT PK AUTO | Primary key |
| `po_item_po_id` | INT FK | Parent bill (po_id) |
| `id_category` | INT FK | Category |
| `id_product` | INT FK | Product |
| `id_design` | INT NULL FK | Design |
| `id_sub_design` | INT NULL FK | Sub-design |
| `no_of_pcs` | INT | Pieces |
| `gross_wt` | DECIMAL | Gross weight |
| `less_wt` | DECIMAL | Less weight |
| `net_wt` | DECIMAL | Net weight |
| `pure_wt` | DECIMAL | Pure weight |
| `making_charge` | DECIMAL | Making charges |
| `wastage` | DECIMAL | Wastage amount |
| `stone_charge` | DECIMAL | Stone charges |
| `total_amount` | DECIMAL | Line total |
| `qc_status` | TINYINT | QC status flag |
| `hm_status` | TINYINT | HM status flag |
| `lot_status` | TINYINT | Lot generation status |

### A5. `ret_qc_process` — QC Process Header
| Column | Type | Purpose |
|---|---|---|
| `qc_process_id` | INT PK | Primary key |
| `po_id` | INT FK | Parent bill |
| `qc_ref_no` | VARCHAR | QC reference number |
| `qc_date` | DATE | QC date |
| `qc_by` | INT FK | QC user |

### A6. `ret_halmarking_process` — Hallmarking Header
| Column | Type | Purpose |
|---|---|---|
| `hm_process_id` | INT PK | Primary key |
| `po_id` | INT FK | Parent bill |
| `hm_ref_no` | VARCHAR | HM reference number |
| `hm_vendor` | INT FK | HM vendor |

### A7. `ret_po_payment` — Payment Header
| Column | Type | Purpose |
|---|---|---|
| `pay_id` | INT PK AUTO | Primary key |
| `pay_ref_no` | VARCHAR | Payment reference |
| `id_karigar` | INT FK | Vendor paid |
| `amount` | DECIMAL | Total payment amount |
| `payment_type` | TINYINT | Cash/Bank/Metal |
| `cheque_no` | VARCHAR NULL | Cheque number |
| `pay_date` | DATE | Payment date |
| `is_verified` | TINYINT(1) | 0=Unverified, 1=Verified (added 2026-03-18 for payment verification feature) |

> **FEATURE Note (2026-03-18)**: `is_verified` column added for the Purchase Payment Verification feature.
> Read/written by `ret_reports_model.get_po_payments()` and `ret_reports_model.verify_po_payment()` via the
> Reports controller endpoint `admin_ret_reports/popayments/verify`. GitHub Issue #1274.

### A8. `ret_purchase_return` — Return Header
| Column | Type | Purpose |
|---|---|---|
| `pur_ret_id` | INT PK AUTO | Primary key |
| `pur_ret_ref_no` | VARCHAR | Return reference |
| `id_karigar` | INT FK | Vendor |
| `return_date` | DATE | Return date |
| `return_reason` | TEXT | Reason |

### A9-A15: Supporting Tables
| Table | Purpose | Key FK |
|---|---|---|
| `ret_purchase_order_stone_detail` | Stone details per bill item | `po_item_id` |
| `ret_purchase_other_metal_detail` | Other metal in items | `po_item_id` |
| `ret_purchase_other_charge_detail` | Other charges on bill | `po_id` |
| `ret_purchase_gst_detail` | GST on bill | `po_id` |
| `ret_purchase_return_items` | Return line items | `pur_ret_id` |
| `ret_purchase_order_description` | Order instructions master | `id_order_des` PK |
| `ret_purchase_item_stock_summary` | Stock summary (running) | `id_stock_summary` PK |

---

## Part B: Referenced Tables (from other modules)

| Table | Owner Module | Used For | Access Pattern |
|---|---|---|---|
| `ret_karigar` | Catalog | Vendor/supplier master, bank details | JOIN on id_karigar |
| `ret_category` | Catalog | Product categories | JOIN on id_category |
| `ret_product` | Catalog | Products | JOIN on id_product |
| `ret_design` | Catalog | Designs | JOIN on design_no |
| `ret_sub_design` | Catalog | Sub-designs | JOIN on id_sub_design |
| `ret_purity` | Catalog | Metal purity | JOIN on id_purity |
| `ret_weight_range` | Catalog | Weight ranges | Direct read |
| `ret_taging` | Tagging | Tag records | Read/Write for lot generation |
| `ret_lot_inward` | Inventory | Lot headers | Write on lot generation |
| `ret_lot_inward_detail` | Inventory | Lot items | Write on lot generation |
| `ret_settings` | Settings | System config | Direct read |
| `ret_wallet_account` | Shared/Accounts | Supplier ledger balances | Read/Write for payments |
| `ret_stone_item` | Tagging | Stone items | AJAX read |
| `ret_stone_type` | Tagging | Stone types | AJAX read |
| `ret_approval_stock` | Approval | Approval stock entries | Read for approval flow |
| `ret_grn_entry` | Purchase (sub) | GRN headers | Read/Write for GRN flow |
| `ret_grn_entry_detail` | Purchase (sub) | GRN items | Write |
| `pur_rat_fix_detail` | Purchase (sub) | Rate fixing records | Read/Write |
| `ret_karigar_metal_issue` | Purchase (sub) | Metal issue headers | Read/Write |
| `ret_karigar_metal_issue_detail` | Purchase (sub) | Metal issue items | Read/Write |
| `ret_non_tag_lot` | Purchase (sub) | Non-tag lots | Read/Write |
| `ret_non_tag_lot_detail` | Purchase (sub) | Non-tag lot items | Read/Write |
| `ret_credit_debit_entry` | Purchase (sub) | CR/DR entries | Read/Write |
| `ret_smith_cmpy_op_bal` | Purchase (sub) | Smith opening balance | Read/Write |
| `ret_supplier_rate_cut` | Purchase (sub) | Rate cut records | Read/Write |

> **PUR-INT03 Note**: `ret_supplier_rate_cut` was updated with `amount_type TINYINT(1)` and `weight_type TINYINT(1)` columns (1=CR, 2=DR) to persist balance nature. Added after `igst_cost`.

---

## Diagnostic Queries

```sql
-- Q1: Complete purchase transaction by PO ID (header + items + stones)
SELECT po.po_id, po.po_ref_no, po.po_date, po.grand_total, po.bill_status,
       k.firstname as karigar_name,
       poi.po_item_id, poi.gross_wt, poi.net_wt, poi.making_charge, poi.total_amount,
       cat.name as category, pro.product_name
FROM ret_purchase_order po
JOIN ret_karigar k ON k.id_karigar = po.id_karigar
LEFT JOIN ret_purchase_order_item poi ON poi.po_item_po_id = po.po_id
LEFT JOIN ret_category cat ON cat.id_ret_category = poi.id_category
LEFT JOIN ret_product pro ON pro.id_ret_product = poi.id_product
WHERE po.po_id = {PO_ID}
ORDER BY poi.po_item_id;

-- Q2: Verify bill total = sum of line items
SELECT po.po_id, po.po_ref_no, po.grand_total,
  SUM(poi.total_amount) as item_total,
  po.grand_total - SUM(poi.total_amount) as diff
FROM ret_purchase_order po
JOIN ret_purchase_order_item poi ON poi.po_item_po_id = po.po_id
WHERE po.bill_status != 2  -- exclude cancelled
GROUP BY po.po_id
HAVING ABS(diff) > 0.01;

-- Q3: Orphan items (no parent PO)
SELECT poi.* FROM ret_purchase_order_item poi
LEFT JOIN ret_purchase_order po ON po.po_id = poi.po_item_po_id
WHERE po.po_id IS NULL;

-- Q4: Orphan stones (no parent item)
SELECT s.* FROM ret_purchase_order_stone_detail s
LEFT JOIN ret_purchase_order_item poi ON poi.po_item_id = s.po_item_id
WHERE poi.po_item_id IS NULL;

-- Q5: Payment vs bill balance check
SELECT po.po_id, po.po_ref_no, po.grand_total,
       po.grand_total_paid,
       COALESCE((SELECT SUM(ppd.amount) FROM ret_po_payment_detail ppd 
                 WHERE ppd.po_id = po.po_id), 0) as calc_paid,
       po.grand_total - COALESCE((SELECT SUM(ppd.amount) FROM ret_po_payment_detail ppd 
                                   WHERE ppd.po_id = po.po_id), 0) as balance
FROM ret_purchase_order po
WHERE po.bill_status = 0  -- active only
HAVING ABS(po.grand_total_paid - calc_paid) > 0.01;

-- Q6: QC items without parent PO item
SELECT qcd.* FROM ret_qc_issue_details qcd
LEFT JOIN ret_purchase_order_item poi ON poi.po_item_id = qcd.po_item_id
WHERE poi.po_item_id IS NULL;
```