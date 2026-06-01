# Recipe: Shopify SKU Secondary Image Missing Select

## Metadata
- **Pattern ID**: PAT-QRY-007
- **Severity**: HIGH
- **Modules Affected**: Shopify Integration, Stockcode
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: The `get_stockcode_indlist` model method is part of the common core codebase and does not select the `secondary_image` column, which is required by the Shopify SKU Entry V2 edit screen.

## Created By
- **Developer**: Antigravity AI
- **Client**: AMS Retail Admin
- **Date**: 2026-05-27
- **Source Bug ID**: N/A

## Symptom
The secondary image uploaded for a stock code in the Shopify SKU Entry V2 screen appears to save successfully, but upon page reload/edit mode entry, it is not displayed and shows a blank/placeholder upload card instead.

## Root Cause
The `get_stockcode_indlist()` method in `application/models/ret_stockcode_model.php` constructs a raw SQL SELECT query to retrieve stock code details. It explicitly selects individual columns of `ret_stock_codes` but excludes `stk.secondary_image`. Since the view file depends on `$stock_code[0]['secondary_image']` to render the secondary image preview, it remains blank even though the image filename is present in the database.

## Detection
```command
grep -rn "stk.stock_image, stk.stock_video" admin/application/models/ret_stockcode_model.php
```

## Files
- `application/models/ret_stockcode_model.php`

## Fix

### Before
```php
        $sql = "SELECT stk.id_stock_code, stk.stock_code, stk.display_name, stk.description_html, stk.publish_status, stk.shopify_tags, IFNULL(stk.min_level,'-') as min_level,IFNULL(stk.max_level,'-') as max_level,  stk.stock_image, stk.stock_video, stk.is_set_ornament, stk.shopify_quantity, stk.country_of_origin, stk.hs_code,(SELECT sum(piece) AS no_pieces FROM ".(self::TAGGING)." where tag_stock_code=stk.id_stock_code GROUP BY tag_stock_code) as no_pieces
        FROM ".(self::STOCK_CODE_TABLE)." stk 
		where stk.id_stock_code = ".$id." order by stk.id_stock_code desc";
```

### After
```php
        $sql = "SELECT stk.id_stock_code, stk.stock_code, stk.display_name, stk.description_html, stk.publish_status, stk.shopify_tags, IFNULL(stk.min_level,'-') as min_level,IFNULL(stk.max_level,'-') as max_level,  stk.stock_image, stk.secondary_image, stk.stock_video, stk.is_set_ornament, stk.shopify_quantity, stk.country_of_origin, stk.hs_code,(SELECT sum(piece) AS no_pieces FROM ".(self::TAGGING)." where tag_stock_code=stk.id_stock_code GROUP BY tag_stock_code) as no_pieces
        FROM ".(self::STOCK_CODE_TABLE)." stk 
		where stk.id_stock_code = ".$id." order by stk.id_stock_code desc";
```

## Verification
1. Open the Shopify SKU Entry V2 page for a stock code that has a secondary image saved in the database.
2. Verify that the secondary image preview thumbnail is shown in the image cards modal and in the header thumbnail section.
3. Upload a new secondary image, save, and reload the page; confirm that the new image preview loads correctly.
