# Recipe: Print Template Chit Adjustment vs Pre-Close Dual Display

## Metadata
- **Pattern ID**: PAT-UI-003
- **Severity**: HIGH
- **Modules Affected**: Billing
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Standard CodeIgniter 3 print template helper behavior.

## Created By
- **Developer**: Antigravity
- **Client**: Kallarackals
- **Date**: 2026-05-26
- **Source Bug ID**: N/A

## Symptom
In template-based print layouts (both Mustache-based HTML and Konva Canvas V2 templates), both "Chit Adjustment Details" and "Chit Pre-Close Details" sections are rendered simultaneously. In standard sales bills with chit utilization, only "Chit Adjustment Details" should show, while "Chit Pre-Close Details" should only appear on chit pre-close bills (`bill_type == 10`). Setting one data array to empty leaves empty table borders/labels and large blank spaces on the canvas.

## Root Cause
1. The print helper `template_receipt_helper.php` mapped and populated both `chit_pre_close_items` and `chit_general_items` arrays directly from `$items_data['chit_details']` if it was not empty, without checking the bill type.
2. In Konva stage JSON layouts, both the Chit Adjustment table/labels and Chit Pre-Close table/labels default to `"conditionVar": "has_chit_items"`. This means if either section has data, `has_chit_items` evaluates to true, rendering the borders/labels of both tables even when one data array is empty.

## Detection
Check if both sections are populated without checking the `bill_type` in `admin/application/helpers/template_receipt_helper.php`:
```command
grep -rn "chit_pre_close_items.*=" admin/application/helpers/template_receipt_helper.php
```

## Files
- `admin/application/helpers/template_receipt_helper.php`
- `admin/application/helpers/konva_receipt_helper.php`

## Fix

### template_receipt_helper.php

#### Before
```php
    // -- Chit Pre Close Specifics --
    $chit_pre_close_items = [];
    $chit_pre_close_total = 0;
    
    // Check if chit_details exists in items_data (passed from controller/model)
    // If not found, check if it's in a different key or needs to be fetched.
    // Assuming items_data['chit_details'] is populated as per Ret_billing_model snippet.
    if (!empty($items_data['chit_details'])) {
        $sno = 1;
        foreach ($items_data['chit_details'] as $item) {
            $amt = isset($item['amount']) ? $item['amount'] : (isset($item['utilized_amt']) ? $item['utilized_amt'] : 0);
            $chit_pre_close_total += $amt;
            
            $ref = isset($item['chit_ref_no']) ? $item['chit_ref_no'] : (isset($item['ref_no']) ? $item['ref_no'] : '-');
            
            $chit_pre_close_items[] = [
                'sno' => $sno++,
                'ref_no' => $ref,
                'amount' => moneyFormatIndia(number_format($amt, 2, '.', ''))
            ];
        }
    }
    
    $mapped['chit_pre_close_items'] = $chit_pre_close_items;
    $mapped['chit_pre_close_total'] = moneyFormatIndia(number_format($chit_pre_close_total, 2, '.', ''));
```
and
```php
    // -- Chit Details (Generic) --
    $chit_general_items = [];
    $chit_general_total = 0;
    if (!empty($items_data['chit_details'])) {
        $sno = 1;
        foreach ($items_data['chit_details'] as $chit) {
            $amt = $chit['utilized_amt'];
            $chit_general_items[] = [
                'sno' => $sno++,
                'ref_no' => $chit['scheme_acc_number'],
                'amount' => moneyFormatIndia($amt)
            ];
             $chit_general_total += $amt;
        }
    }
    $mapped['chit_general_items'] = $chit_general_items;
    $mapped['chit_general_total'] = moneyFormatIndia($chit_general_total);
```

#### After
```php
    // -- Chit Pre Close Specifics --
    $chit_pre_close_items = [];
    $chit_pre_close_total = 0;
    
    // Check if chit_details exists in items_data (passed from controller/model)
    // Only populate pre-close details if this is a chit pre-close bill (bill_type == 10)
    if ($billing['bill_type'] == 10 && !empty($items_data['chit_details'])) {
        $sno = 1;
        foreach ($items_data['chit_details'] as $item) {
            $amt = isset($item['amount']) ? $item['amount'] : (isset($item['utilized_amt']) ? $item['utilized_amt'] : 0);
            $chit_pre_close_total += $amt;
            
            $ref = isset($item['chit_ref_no']) ? $item['chit_ref_no'] : (isset($item['ref_no']) ? $item['ref_no'] : '-');
            
            $chit_pre_close_items[] = [
                'sno' => $sno++,
                'ref_no' => $ref,
                'amount' => moneyFormatIndia(number_format($amt, 2, '.', ''))
            ];
        }
    }
    
    $mapped['chit_pre_close_items'] = $chit_pre_close_items;
    $mapped['chit_pre_close_total'] = moneyFormatIndia(number_format($chit_pre_close_total, 2, '.', ''));
```
and
```php
    // -- Chit Details (Generic) --
    $chit_general_items = [];
    $chit_general_total = 0;
    // Only populate chit adjustment details for non-pre-close bills (bill_type != 10)
    if ($billing['bill_type'] != 10 && !empty($items_data['chit_details'])) {
        $sno = 1;
        foreach ($items_data['chit_details'] as $chit) {
            $amt = $chit['utilized_amt'];
            $chit_general_items[] = [
                'sno' => $sno++,
                'ref_no' => $chit['scheme_acc_number'],
                'amount' => moneyFormatIndia($amt)
            ];
             $chit_general_total += $amt;
        }
    }
    $mapped['chit_general_items'] = $chit_general_items;
    $mapped['chit_general_total'] = moneyFormatIndia($chit_general_total);
```

### konva_receipt_helper.php

#### Before
```php
    // Single variable (original behavior)
    $val = isset($bill_data[$condExpr]) ? $bill_data[$condExpr] : null;
    return _konva_is_truthy($val);
}
```
And throughout the rendering passes, `conditionVar` is evaluated directly.

#### After
Insert the condition resolution helper function:
```php
    // Single variable (original behavior)
    $val = isset($bill_data[$condExpr]) ? $bill_data[$condExpr] : null;
    return _konva_is_truthy($val);
}

/**
 * Resolve/override conditionVar for chit adjustment vs pre-close elements on the canvas
 */
function _konva_get_node_condition($attrs)
{
    $condVar = isset($attrs['conditionVar']) ? $attrs['conditionVar'] : '';
    if ($condVar === 'has_chit_items') {
        $id = isset($attrs['id']) ? (string)$attrs['id'] : '';
        $text = isset($attrs['text']) ? (string)$attrs['text'] : '';
        $tableConfig = isset($attrs['tableConfig']) ? $attrs['tableConfig'] : '';
        $tableConfigStr = is_array($tableConfig) ? json_encode($tableConfig) : (string)$tableConfig;
        
        // If it's a pre-close element
        if (strpos($id, 'pre_close') !== false || strpos($id, 'preclose') !== false ||
            strpos(strtolower($text), 'pre-close') !== false || strpos(strtolower($text), 'preclose') !== false ||
            strpos($tableConfigStr, 'chit_pre_close') !== false) {
            return 'is_chit_preclose';
        }
        
        // If it's a chit adjustment element
        if (strpos($id, 'chit_adjustment') !== false || 
            strpos(strtolower($text), 'chit adjustment') !== false || 
            strpos($tableConfigStr, 'chit_adjustment') !== false) {
            return 'has_chit_adj';
        }
    }
    return $condVar;
}
```
And replace the direct references of `conditionVar` in `konva_receipt_helper.php`:
- `$condVar = isset($attrs['conditionVar']) ? $attrs['conditionVar'] : '';` -> `$condVar = _konva_get_node_condition($attrs);` (during Pass 1 and Pass 2)
- In `_konva_render_node` conditional visibility:
  ```php
    $condVar = _konva_get_node_condition($attrs);
    if (!empty($condVar)) {
        if (!_konva_eval_condition($condVar, $bill_data)) {
            return '';
        }
    }
  ```

## Verification
1. Open a sales invoice with chit adjustment (`bill_type != 10`). Verify that the "Chit Adjustment Details" table renders, and the "Chit Pre-Close Details" block is completely hidden (no empty borders/labels or blank spaces).
2. Open a chit pre-close invoice (`bill_type == 10`). Verify that the "Chit Pre-Close Details" table renders, and the "Chit Adjustment Details" block is completely hidden (no empty borders/labels or blank spaces).

## Notes
Mustache conditional blocks around the tables in template HTML are keyed on `has_chit_items` or the item arrays themselves. In Konva, we dynamically override the `conditionVar` to target `has_chit_adj` or `is_chit_preclose` respectively to guarantee perfect collapsing of hidden canvas zones.
