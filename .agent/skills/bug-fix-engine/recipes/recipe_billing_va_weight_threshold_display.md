# Recipe: Billing Print — VA Conditional Display (gm vs %)

## Metadata
- **Pattern ID**: PAT-BIL-VA01
- **Severity**: MEDIUM
- **Modules Affected**: Billing (Print Templates)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: All clients use the same billing print templates with VA column

## Created By
- **Developer**: Antigravity
- **Client**: erp.sparqlediamonds.com
- **Date**: 2026-04-23
- **Source Bug ID**: N/A

## Symptom
The V.A (Value Added) column in all billing print copies shows the same format
regardless of item weight. Business requirement is:
- Net weight **< 5g** → display VA as **weight in grams** (e.g. `0.456 gm`)
- Net weight **≥ 5g** → display VA as **wastage percentage** (e.g. `8.50%`)

## Root Cause
The original code used a single fixed display path:
- `receipt_billing.php` showed `$wastge_amt` (rupee amount) — wrong unit entirely
- `bill_format_2.php` set `$mc` as a string `"150/G"` and showed that for VA
- No threshold logic existed to switch between gm and % based on item weight

## Detection
```bash
grep -n "wastge_amt\|wastage_percent\|item_wastge_wt" \
  admin/application/views/billing/print/receipt_billing.php \
  admin/application/views/billing/print/bill_format_2.php
```
Look for VA column `<td>` cells that show `$wastge_amt` (Rs amount) or a fixed
string format instead of the conditional gm/% display.

## Files
- `admin/application/views/billing/print/receipt_billing.php`
- `admin/application/views/billing/print/bill_format_2.php`

## Fix

### receipt_billing.php — Sales items VA cell (line ~662)

#### Before
```php
<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($wastge_amt), 2, '.', '')); ?></td>
```

#### After
```php
<td class="alignRight"><?php
    if ($items['net_wt'] < 5) {
        echo $item_wastge_wt > 0 ? number_format($item_wastge_wt, 3, '.', '') . ' gm' : '';
    } else {
        echo $wastage_percent > 0 ? number_format($wastage_percent, 2, '.', '') . '%' : '';
    }
?></td>
```

### receipt_billing.php — Sub-row % display (line ~673) — CLEAR IT

#### Before
```php
<td class="alignRight"><?php echo ($wastage_percent != '' ? '(' . number_format($wastage_percent, 2, '.', '') . '%' : '') . ')'; ?></td>
```

#### After
```php
<td class="alignRight"></td>
```
> Reason: The % is now shown on the main row. The sub-row would double-display it.

### receipt_billing.php — OD items VA cell (line ~1641)

#### Before
```php
<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($od_wastage_amt), 2, '.', '')); ?></td>
```

#### After
```php
<td class="alignRight"><?php
    if ($items['net_wt'] < 5) {
        echo $od_wastage_wt > 0 ? number_format($od_wastage_wt, 3, '.', '') . ' gm' : '';
    } else {
        echo $items['wast_percent'] > 0 ? number_format($items['wast_percent'], 2, '.', '') . '%' : '';
    }
?></td>
```

### bill_format_2.php — Sales items VA cell (line ~777)

#### Before
```php
<td style="...width:7%;"><<?php
    if ($items['net_wt'] < 5) {
        echo $item_wastge_wt > 0 ? number_format($item_wastge_wt, 3, '.', '') . ' gm' : '';
    } else {
        echo $items['wastage_percent'] > 0 ? number_format($items['wastage_percent'], 2, '.', '') . '%' : '';
    }
?></td>
```
> Note: this was already the fixed state from a prior partial fix. Verify before applying.

### bill_format_2.php — Return items VA cell (line ~1162)

#### Before
```php
<td style="width: 15%" class="alignRight"><?php echo ($wastge_wt > 0 ? number_format($wastge_wt, 3, '.', '') : ''); ?></td>
```

#### After
```php
<td style="width: 15%" class="alignRight"><?php
    if ($items['net_wt'] < 5) {
        echo $wastge_wt > 0 ? number_format($wastge_wt, 3, '.', '') . ' gm' : '';
    } else {
        echo $items['wastage_percent'] > 0 ? number_format($items['wastage_percent'], 2, '.', '') . '%' : '';
    }
?></td>
```

## Verification
1. Open a billing invoice with an item whose `net_wt` < 5g → VA column should show e.g. `0.456 gm`
2. Open a billing invoice with an item whose `net_wt` ≥ 5g → VA column should show e.g. `8.50%`
3. Check both bill formats: `bill_format == 0` (receipt_billing) and `bill_format != 0` (bill_format_2)
4. Check both sales items AND return/exchange items sections
5. Check OD (Order Delivery) type invoices in receipt_billing.php
6. Verify the sub-row % is no longer duplicated in receipt_billing

## Notes
- Variables differ between sections: sales items use `$item_wastge_wt` / `$wastage_percent`;
  OD items use `$od_wastage_wt` / `$items['wast_percent']`; return items use `$wastge_wt` / `$items['wastage_percent']`
- `billing_soft_copy.php` is NOT referenced by any controller — changes there are for
  consistency only, not production impact
- The threshold is 5g (not 1g as used in the old estimation VA formula)
