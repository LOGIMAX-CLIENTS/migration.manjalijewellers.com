# Recipe: Missing Sub Design Master Table Hardcoded Joins

> Hardcoded LEFT JOINs to a deprecated or missing `ret_sub_design_master` table cause SQL execution errors in core modules when the feature is disabled for specific clients.

## Metadata
- **Pattern ID**: PAT-SYS-012
- **Severity**: HIGH
- **Modules Affected**: QC Issue/Receipt Edit, Estimation, Catalog, Purchase Order
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: Clients without the "Sub Design" feature enabled (e.g., `ams_25`).
- **Reason**: The centralized core codebase assumes `ret_sub_design_master` exists globally, but specific client databases may lack the schema entirely.

## Created By
- **Developer**: Antigravity
- **Client**: AMS-RetailAdmin
- **Date**: 2026-05-19
- **Source Bug ID**: QC Issue Edit SQL Error

## Symptom
1. Users attempt to open the QC Issue Edit page or view specific catalog/purchase items.
2. The page completely crashes or fails to load data.
3. A fatal database error is thrown: `Table '{db_name}.ret_sub_design_master' doesn't exist`.

## Root Cause
Core models (`ret_purchase_order_model.php` and `ret_catalog_model.php`) contain over 30 hardcoded instances of `LEFT JOIN ret_sub_design_master`. When the table is dropped or not created for clients that do not utilize the sub-design feature, the SQL engine immediately aborts execution. 

## Detection
```command
REM Search for left joins to ret_sub_design_master
findstr /n /c:"LEFT JOIN ret_sub_design_master" application\models\ret_purchase_order_model.php application\models\ret_catalog_model.php
```

## Files
- `application/models/ret_purchase_order_model.php`
- `application/models/ret_catalog_model.php`

## Fix

### Fix 1: Remove the JOIN and alias the field as empty

#### Before
```php
        $sql = $this->db->query("SELECT p.product_name,des.design_name,subDes.sub_design_name,
        FROM ret_purchase_order_items p
        LEFT JOIN ret_design_master des ON des.design_no=p.po_item_des_id
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design=p.po_item_sub_des_id
```

#### After
```php
        $sql = $this->db->query("SELECT p.product_name,des.design_name,'' as sub_design_name,
        FROM ret_purchase_order_items p
        LEFT JOIN ret_design_master des ON des.design_no=p.po_item_des_id
```

**Why**: Because the table doesn't exist, we must strip the `LEFT JOIN`. To satisfy frontend grids and JavaScript expecting the variable, we inject an empty string `'' as sub_design_name` to prevent undefined index errors in PHP or `null` rendering in JS. Also ensure you resolve dangling `id_sub_design` aliases that were referencing the dropped table.

## Verification
1. Access the QC Issue Edit page and verify the data table loads successfully.
2. Ensure columns mapping to "Sub Product" or "Sub Design" cleanly render empty.
3. Validate there are no `Unknown column` syntax errors by checking database logs.
