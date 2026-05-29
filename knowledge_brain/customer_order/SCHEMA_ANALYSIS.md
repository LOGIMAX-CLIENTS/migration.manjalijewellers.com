# Database Verification Protocol & SQL

## Truth Queries

The following queries serve as baseline DB verification for Customer Order testing.

### 1. Header and Detail Verification
```sql
SELECT
    o.id_customerorder,
    o.order_no,
    o.order_type,
    o.order_date,
    SUM(od.totalitems) AS total_items_sum,
    SUM(od.weight) AS total_gross_weight,
    b.name AS branch
FROM customerorder o
JOIN customerorderdetails od ON o.id_customerorder = od.id_customerorder
JOIN branch b ON o.order_from = b.id_branch
WHERE o.order_no = 'TEST-ORD-NO'
GROUP BY o.id_customerorder;
```

### 2. Orphaned Order Lines Check
```sql
SELECT
    od.id_orderdetails,
    od.id_customerorder,
    od.order_date
FROM customerorderdetails od
LEFT JOIN customerorder o ON od.id_customerorder = o.id_customerorder
WHERE o.id_customerorder IS NULL;
```

### 3. Check Misaligned Total Weight
```sql
SELECT
    o.id_customerorder,
    IFNULL(o.order_pcs, 0) as header_pcs,
    IFNULL(o.order_approx_wt, 0) as header_wt,
    SUM(od.totalitems) as sub_pcs,
    SUM(od.weight) as sub_wt
FROM customerorder o
JOIN customerorderdetails od ON od.id_customerorder = o.id_customerorder
GROUP BY o.id_customerorder
HAVING header_pcs != sub_pcs OR header_wt != sub_wt;
```

### 4. Status Map
| DB ID | Interpretation |
|---|---|
| 0 | In Cart |
| 1 | Order Placed |
| 2 | Order Rejected (Cart boundary) |
| 3 | Work in Progress / Assigned |
| 4 | Completed / Ready |
| 5 | Delivered |
| 6 | New Order Rejected |
| 8 | Reject after assigned |

---

## Column-Level Schema

### `customerorder` (Order Header)

| Column | Type | Nullable | Source | Notes |
|---|---|---|---|---|
| `id_customerorder` | INT PK | NO | Auto | Primary key |
| `fin_year_code` | VARCHAR | NO | `get_FinancialYear()` | Financial year prefix |
| `order_no` | VARCHAR | NO | `generateOrderNo()` | Formatted: `{branch}{year}-{type}-{seq}` |
| `order_type` | TINYINT | NO | POST | 1=Stock, 2=Customer, 3=CusRepair, 4=StkRepair, 5=Tagged, 6=HomeBill |
| `order_for` | TINYINT | NO | Derived | 1=Branch, 2=Customer (set from order_type) |
| `order_to` | INT FK | YES | POST | → `customer.id_customer` or `branch.id_branch` |
| `order_from` | INT FK | NO | Session | → `branch.id_branch` |
| `order_date` | DATETIME | NO | Day-closing check | Actual entry date |
| `order_status` | TINYINT | NO | Default=0 | Header-level status |
| `order_pcs` | INT | YES | Computed after insert | SUM of item pieces (repair only) |
| `order_approx_wt` | DECIMAL | YES | Computed after insert | SUM of item weights (repair only) |
| `work_at` | TINYINT | YES | POST | 1=In House, 2=Outsource |
| `rate_type` | TINYINT | YES | POST | 1=Order Rate, 2=Delivery Rate |
| `balance_type` | TINYINT | YES | POST | 1=Metal, 2=Cash |
| `is_ho_branch` | INT FK | YES | Session | → `branch.id_branch` (HO branch) |
| `order_taken_by` | INT FK | YES | POST | → `employee.id_employee` |
| `counter_id` | INT FK | YES | Session | → counter table |
| `est_id` | VARCHAR | YES | POST | Estimation ID (from ret_estimation) |
| `rate_calc_from` | TINYINT | YES | Form default=1 | Rate calculation base |
| `createdon` | DATETIME | YES | `date()` | Creation timestamp |
| `created_by` | INT FK | YES | Session | → `employee.id_employee` |
| `updated_on` | DATETIME | YES | `date()` on update | Update timestamp |
| `updated_by` | INT FK | YES | Session | → `employee.id_employee` |
| `reject_reason` | TEXT | YES | POST | Order-level cancellation reason |

### `customerorderdetails` (Order Line Items)

| Column | Type | Nullable | Source | Notes |
|---|---|---|---|---|
| `id_orderdetails` | INT PK | NO | Auto | Primary key |
| `id_customerorder` | INT FK | NO | Parent | → `customerorder` |
| `orderno` | VARCHAR | NO | Generated | `{order_no}-{i}` suffix |
| `ortertype` | TINYINT | NO | POST | Copy of `order_type` |
| `id_product` | INT FK | YES | POST | → `ret_product_master.pro_id` |
| `design_no` | INT FK | YES | POST | → `ret_design_master.design_no` |
| `id_sub_design` | INT FK | YES | POST | → `ret_sub_design_master` |
| `id_purity` | INT FK | YES | POST | → `ret_purity.id_purity` |
| `weight` | DECIMAL | YES | POST | Gross weight |
| `less_wt` | DECIMAL | YES | POST | Deduction weight |
| `net_wt` | DECIMAL | YES | POST | Net weight |
| `pure_wt` | DECIMAL | YES | POST (repair only) | Pure weight |
| `totalitems` | INT | YES | POST | Piece count |
| `mc` | DECIMAL | YES | POST | Making charge value |
| `id_mc_type` | TINYINT | YES | POST | 1=Per Gram, 2=Piece |
| `wast_percent` | DECIMAL | YES | POST | Wastage % |
| `stn_amt` | DECIMAL | YES | POST | Stone amount total |
| `charge_value` | DECIMAL | YES | POST | Other charges total |
| `rate` | DECIMAL | YES | POST | Item rate |
| `rate_per_gram` | DECIMAL | YES | POST | Rate per gram |
| `total_sgst` | DECIMAL | YES | POST | SGST amount |
| `total_cgst` | DECIMAL | YES | POST | CGST amount |
| `total_igst` | DECIMAL | YES | POST | IGST amount |
| `size` | INT FK | YES | POST | → `ret_size.id_size` |
| `itemname` | VARCHAR | YES | POST | Design name / label |
| `description` | TEXT | YES | POST | Item notes |
| `image` | VARCHAR | YES | Legacy | Old `#`-delimited image string (deprecated) |
| `orderstatus` | TINYINT | YES | Default=0 | Item-level status |
| `tag_id` | INT FK | YES | POST | → `ret_taging.tag_id` |
| `tag_name` | VARCHAR | YES | POST | Tag barcode label |
| `id_repair_master` | INT FK | YES | POST | → `ret_repair_master` |
| `cus_due_date` | DATE | YES | Computed | Customer delivery deadline |
| `smith_due_date` | DATE | YES | Computed | Karigar deadline |
| `smith_remainder_date` | DATE | YES | Computed | Karigar reminder date |
| `order_date` | DATE | YES | From header | Copied from order header |
| `id_employee` | INT FK | YES | Session | → `employee.id_employee` |
| `id_karigar` | INT FK | YES | Assigned later | → `ret_karigar` |
| `branch_id` | INT FK | YES | From order | Branch reference |
| `current_branch` | INT FK | YES | POST (repair) | Current branch of item |
| `reject_reason` | TEXT | YES | POST | Item-level rejection reason |
| `cancelled_by` | INT FK | YES | Session | → `employee.id_employee` |
| `order_cancelled_date` | DATETIME | YES | `date()` | Cancellation timestamp |
| `deliverydate` | DATETIME | YES | Updated on delivery | Actual delivery timestamp |

### `customer_order_image` (Images)

| Column | Type | Nullable | Notes |
|---|---|---|---|
| `id` | INT PK | NO | Auto |
| `id_orderdetails` | INT FK | NO | → `customerorderdetails` |
| `image` | VARCHAR | NO | Filename in `assets/img/orders/` |

### `ret_order_item_stones` (Stones per Item)

| Column | Type | Nullable | Notes |
|---|---|---|---|
| `id` | INT PK | NO | Auto |
| `order_id` | INT FK | NO | → `customerorder` |
| `order_item_id` | INT FK | NO | → `customerorderdetails` |
| `stone_id` | INT FK | YES | → `ret_stone` |
| `stone_type` | TINYINT | YES | Enum |
| `pieces` | INT | YES | Stone count |
| `wt` | DECIMAL | YES | Stone weight |
| `uom_id` | INT FK | YES | → `ret_uom` |
| `price` | DECIMAL | YES | Stone amount |
| `stone_cal_type` | TINYINT | YES | Calculation type |
| `rate_per_gram` | DECIMAL | YES | Rate |
| `is_apply_in_lwt` | TINYINT | YES | Whether stone included in loss weight |

### `ret_order_other_charges` (Extra Charges per Item)

| Column | Type | Nullable | Notes |
|---|---|---|---|
| `id_order_other_charge` | INT PK | NO | Auto |
| `order_id` | INT FK | NO | → `customerorder` |
| `order_item_id` | INT FK | NO | → `customerorderdetails` |
| `id_charge` | INT FK | NO | → `ret_charges` |
| `amount` | DECIMAL | NO | Charge amount |

### `ret_issue_receipt` (Referenced — Advance Refund on Cancel)

> ⭐ R17: This module **WRITES** to this table as of the post-R14 code change. Previously read-only via `get_order_total_advance()`.

| Column | Value Written by This Module | Notes |
|---|---|---|
| `type` | `2` | Receipt type for refund |
| `receipt_type` | `5` | Advance return category |
| `amount` | `$order_advance['advance_amount']` | Amount to refund |
| `weight` | `0` | Not applicable for cash advance |
| `bill_no` | Generated via `bill_no_generate()` | New receipt bill number |
| `id_branch` | `$orderdetails[0]['order_from']` | Branch of the cancelled order |
| `id_customer` | `get_order_customer_id($orderid)` | Customer who placed the order |
| `id_customerorder` | `$orderid` | Links receipt back to the order |
| `is_eda` | `$order_advance['is_eda']` | EDA flag from advance record |
| `fin_year_code` | From `get_FinancialYear()` | Current financial year |
| `narration` | `'Order Advance Transfer - ' . $orderid` | Auto-generated narration |

> ⚠️ **AP-11 RISK**: The guard `if($order_advance > 0)` uses array-to-int comparison — always truthy. A zero-amount receipt row is inserted even when the order has no advance. See FLOW_RISK_MATRIX FR-CUSORD-005.

---

## Additional Diagnostic Queries

### 5. Orphan Images (no parent order detail)
```sql
SELECT i.id, i.id_orderdetails, i.image
FROM customer_order_image i
LEFT JOIN customerorderdetails od ON od.id_orderdetails = i.id_orderdetails
WHERE od.id_orderdetails IS NULL;
```

### 6. Orphan Stones (no parent order detail)
```sql
SELECT s.id, s.order_item_id
FROM ret_order_item_stones s
LEFT JOIN customerorderdetails od ON od.id_orderdetails = s.order_item_id
WHERE od.id_orderdetails IS NULL;
```

### 7. Orders with image/stone mismatch after update
```sql
SELECT od.id_orderdetails, od.stn_amt,
  IFNULL(SUM(s.price),0) as actual_stn_total
FROM customerorderdetails od
LEFT JOIN ret_order_item_stones s ON s.order_item_id = od.id_orderdetails
GROUP BY od.id_orderdetails
HAVING od.stn_amt != actual_stn_total;
```

### 8. Debug .txt file remnants (repair update artifact)
```sql
-- Cross-reference: check orders_img directory via filesystem
-- Files at: assets/img/orders_img/{id_orderdetails}.txt
-- These contain serialized $_FILES data and should be deleted
```

### 9. Tagged orders without updated tag status
```sql
SELECT od.id_orderdetails, od.tag_id, t.tag_status, o.order_type
FROM customerorderdetails od
JOIN ret_taging t ON t.tag_id = od.tag_id
JOIN customerorder o ON o.id_customerorder = od.id_customerorder
WHERE o.order_type = 4 AND t.tag_status != 8
  AND od.orderstatus < 6;
```

### 10. AP-11 Verification: Zero-amount receipts from cancel (R17)
```sql
-- Detects spurious ret_issue_receipt rows from AP-11 bug
-- These are advance-refund receipts with amount=0, linked to orders with no actual advance
SELECT r.id_receipt, r.amount, r.id_customerorder, r.narration, r.createdon
FROM ret_issue_receipt r
WHERE r.receipt_type = 5
  AND r.type = 2
  AND r.amount = 0
  AND r.narration LIKE 'Order Advance Transfer%'
ORDER BY r.createdon DESC;
```

### 11. Cancelled orders with orphan tag references (delete gap)
```sql
-- Tags still pointing to deleted/cancelled order details
SELECT t.tag_id, t.tag_code, t.id_orderdetails, od.orderstatus
FROM ret_taging t
LEFT JOIN customerorderdetails od ON od.id_orderdetails = t.id_orderdetails
WHERE t.id_orderdetails IS NOT NULL
  AND (od.id_orderdetails IS NULL OR od.orderstatus = 6);
```

