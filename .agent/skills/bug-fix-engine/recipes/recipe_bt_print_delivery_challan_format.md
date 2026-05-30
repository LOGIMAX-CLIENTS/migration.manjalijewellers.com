# Branch Transfer — Delivery Challan Print Format (Old Metal)

> Reformats the Branch Transfer print for OLD METAL (transfer_item_type=3, type=2) to a Delivery Challan layout matching client-provided Excel template.

## Metadata
- **Pattern ID**: PAT-PRINT-032
- **Severity**: LOW
- **Modules Affected**: branch_transfer (print)
- **Auto-fixable**: No (multi-file, layout-driven)

## Client Scope
- **Applies to**: karpagamjewels.com
- **Reason**: Client-specific Delivery Challan format requested via Excel template

## Created By
- **Developer**: Antigravity
- **Client**: karpagamjewels.com
- **Date**: 2026-04-29
- **Source Bug ID**: N/A (feature request)

## Symptom
Client requires the OLD METAL branch transfer print (transfer_item_type=3, type=2) to use a "Delivery Challan" format instead of the default receipt layout. Specific requirements:
1. Title: "BRANCH TRANSFER DELIVERY CHALLAN (Same GSTIN)"
2. FROM/TO branch details side-by-side (left/right aligned) with company name, address, pincode, GSTIN
3. DC Number displayed
4. Table columns: S.No | Item Description (category name) | Date (bill date) | Qty | Unit | Rate | Value
5. All items (OLD METAL, SALES RETURN, PARTLY SALE) combined in one bordered table
6. Footer: certification text, remarks, "For [Company]", then PREPARED BY / CHECKED BY / AUTHORIZED SIGNATORY
7. No "Date/Vehicle No/E-Way Bill" section, no "Reason for Movement" row

## Root Cause
Default print format uses a generic receipt layout for all transfer types. Client requires a GST Delivery Challan format for Old Metal transfers specifically.

## Detection
```command
grep -n "transfer_item_type==3" admin/application/views/branch_transfer/print.php
grep -n "getBTransData" admin/application/models/ret_brntransfer_model.php
grep -n "get_purchase_items_details" admin/application/models/ret_brntransfer_model.php
```

## Files
- `admin/application/views/branch_transfer/print.php`
- `admin/application/models/ret_brntransfer_model.php`

## Fix

### 1. Model: `ret_brntransfer_model.php` — `getBTransData()` for s_type==3

Add branch address/GST fields to the query SELECT:

#### Before
```php
$sql=$this->db->query("SELECT m.metal_type,SUM(est.gross_wt) as grs_wt,SUM(est.net_wt) as net_wt,SUM(est.rate) as amount,date_format(b.created_time,'%d-%m-%Y') as created_time,
b.is_other_issue,b.transfer_item_type,b.branch_trans_code,fb.name as from_branch,tb.name as to_branch,b.branch_transfer_id,bill.bill_no,
date_format(MIN(bill.bill_date),'%d-%m-%Y') as bill_from_date, date_format(MAX(bill.bill_date),'%d-%m-%Y') as bill_to_date
```

#### After
```php
$sql=$this->db->query("SELECT m.metal_type,SUM(est.gross_wt) as grs_wt,SUM(est.net_wt) as net_wt,SUM(est.rate) as amount,date_format(b.created_time,'%d-%m-%Y') as created_time,
b.is_other_issue,b.transfer_item_type,b.branch_trans_code,fb.name as from_branch,tb.name as to_branch,b.branch_transfer_id,bill.bill_no,
date_format(MIN(bill.bill_date),'%d-%m-%Y') as bill_from_date, date_format(MAX(bill.bill_date),'%d-%m-%Y') as bill_to_date,
fb.address1 as fb_address1, fb.address2 as fb_address2, IFNULL(fb.pincode,'') as fb_pincode, IFNULL(fb.gst_number,'') as fb_gst_number,
tb.address1 as tb_address1, tb.address2 as tb_address2, IFNULL(tb.pincode,'') as tb_pincode, IFNULL(tb.gst_number,'') as tb_gst_number
```

### 2. Model: `ret_brntransfer_model.php` — `get_purchase_items_details()` old_metal_details query

Add `bill_date`, `category_name`, and JOIN to `ret_old_metal_type`:

#### Before
```php
s.metal_type,met.metal as metal_name
FROM ret_branch_transfer  b
...
LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=s.esti_old_metal_sale_id
LEFT JOIN ret_billing bill ON bill.bill_id=s.bill_id
```

#### After
```php
s.metal_type,met.metal as metal_name,
date_format(bill.bill_date,'%d-%m-%Y') as bill_date,
IFNULL(omt.metal_type,'') as category_name
FROM ret_branch_transfer  b
...
LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=s.esti_old_metal_sale_id
LEFT JOIN ret_old_metal_type omt ON omt.id_metal_type=e.id_old_metal_type
LEFT JOIN ret_billing bill ON bill.bill_id=s.bill_id
```

### 3. Model: `get_purchase_items_details()` — sales_return_details query

Add `bill_date`:

#### Before
```php
met.metal as metal_name,pro.product_name
```

#### After
```php
met.metal as metal_name,pro.product_name,
date_format(bill.bill_date,'%d-%m-%Y') as bill_date
```

### 4. Model: `get_purchase_items_details()` — partly_sales_details query

Add `bill_date`:

#### Before
```php
concat('PARTLY SALE - ',met.metal) as item_type,
p.product_name
```

#### After
```php
concat('PARTLY SALE - ',met.metal) as item_type,
date_format(bill.bill_date,'%d-%m-%Y') as bill_date,
p.product_name
```

### 5. View: `print.php` — Header section

Wrap existing header in `<?php } else { ?>` block. Add new conditional header for `transfer_item_type==3 && type==2`:

```php
<?php if(isset($btrans[0]['transfer_item_type']) && $btrans[0]['transfer_item_type']==3 && $type==2){ ?>
    <!-- Title -->
    BRANCH TRANSFER DELIVERY CHALLAN (Same GSTIN)
    
    <!-- Two-column: FROM BRANCH (left) | TO BRANCH (right) -->
    <!-- Each shows: Company Name, Address1, Address2-Pincode, GSTIN -->
    <!-- Uses fb_address1, fb_address2, fb_pincode, fb_gst_number / tb_* equivalents -->
    
    <!-- DC Number only (no Date/Vehicle/E-Way Bill) -->
    DC Number: <?php echo $btrans[0]['branch_trans_code'];?>
<?php } else { ?>
    <!-- Original header unchanged -->
<?php } ?>
```

### 6. View: `print.php` — Table section for transfer_item_type==3

Replace three separate tables (OLD METAL, SALES RETURN, PARTLY SALE) with one combined bordered table:

- **Columns**: S.No | Item Description | Date | Qty | Unit | Rate | Value
- **Item Description**: `category_name` for old metal, `product_name` for sales return / partly sale
- **Date**: `bill_date` (not `created_time`)
- **Unit**: hardcoded "GRAM"
- **Rate**: calculated as `amount / qty`
- **TOTAL row** at bottom with grand qty and value

### 7. View: `print.php` — Footer section

After table, add:
```
We Certify that only for Branch Stock Transfer and Not for Sale
Remarks: [remark]

For [COMPANY NAME]
[one line space]
PREPARED BY          CHECKED BY          AUTHORIZED SIGNATORY
```

No "Reason for Movement" row. Signature labels rendered inside the table section, not in the bottom div.

## Verification
1. Print a Branch Transfer with transfer_item_type=3 (Old Metal), type=2 (Detailed)
2. Verify header shows FROM/TO branches side-by-side with address + GSTIN
3. Verify title reads "BRANCH TRANSFER DELIVERY CHALLAN (Same GSTIN)"
4. Verify table has bordered columns: S.No, Item Description (category name), Date (bill date), Qty, Unit, Rate, Value
5. Verify only actual data rows display (no empty padding rows)
6. Verify TOTAL row shows correct grand totals
7. Verify footer: certification + remarks + "For [Company]" + signatures
8. Verify other transfer types (type=1, transfer_item_type=1,2,4,5) are NOT affected

## Notes
- This is a client-specific format change, not a bug fix
- Only affects `transfer_item_type==3` (Old Metal) with `type==2` (Detailed print)
- All other print formats remain 100% unchanged via PHP conditional blocks
- The `branch` table must have `address1`, `address2`, `pincode`, `gst_number` columns populated for the header to display correctly
- DomPDF renders the bordered table well; tested with inline styles (no class-based CSS)
