# Recipe: Billing Print — MC Amount Rounded (No Decimals)

## Metadata
- **Pattern ID**: PAT-BIL-MC01
- **Severity**: MEDIUM
- **Modules Affected**: Billing (Print Templates)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: All clients use the same billing print templates with MC column

## Created By
- **Developer**: Antigravity
- **Client**: erp.sparqlediamonds.com
- **Date**: 2026-04-23
- **Source Bug ID**: N/A

## Symptom
The MC (Making Charge) column in billing print copies shows either:
1. A rate string like `150/G` or `150/P` instead of the computed total amount
2. A decimal amount like `1,500.00` instead of a whole rounded number `1,500`

Business requirement: MC should display as a **rounded whole number** (no decimals).

## Root Cause
In `bill_format_2.php`, the **sales items section** sets `$mc` as a display string:
```php
$mc = ($items['mc_type'] == 2 ? ($items['mc_value'].'/G') : ($items['mc_value'] .'/P'));
```
This stores the per-gram/per-piece rate as a string, not the computed total.
The return items section correctly computes `$mc` as a numeric total but then
formats it with 2 decimal places (`number_format($mc, 2)`).

In `receipt_billing.php`, the Designer Charge row echoes `$total_mc` raw
(no rounding, no formatting).

## Detection
```bash
grep -n "mc_value.*'/G'\|mc_value.*'/P'\|number_format.*mc.*2" \
  admin/application/views/billing/print/bill_format_2.php \
  admin/application/views/billing/print/receipt_billing.php
```
Any line setting `$mc` to a string with `/G` or `/P` suffix is the root cause.

## Files
- `admin/application/views/billing/print/bill_format_2.php`
- `admin/application/views/billing/print/receipt_billing.php`

## Fix

### bill_format_2.php — Sales items: Fix $mc calculation (lines ~744-752)

#### Before
```php
if ($items['calculation_based_on'] == 0) {
    $wastge_wt = ($items['gross_wt'] * ($items['wastage_percent'] / 100));
    $mc = ($items['mc_type'] == 2 ? ($items['mc_value'].'/G') : ($items['mc_value'] .'/P'));
} else if ($items['calculation_based_on'] == 1) {
    $wastge_wt = ($items['net_wt'] * ($items['wastage_percent'] / 100));
    $mc = ($items['mc_type'] == 2 ? ($items['mc_value'].'/G') : ($items['mc_value'] .'/P'));
} else if ($items['calculation_based_on'] == 2) {
    $wastge_wt = ($items['net_wt'] * ($items['wastage_percent'] / 100));
    $mc = ($items['mc_type'] == 2 ? ($items['mc_value'].'/G') : ($items['mc_value'] .'/P'));
}
```

#### After
```php
if ($items['calculation_based_on'] == 0) {
    $wastge_wt = ($items['gross_wt'] * ($items['wastage_percent'] / 100));
    $mc = ($items['mc_type'] == 2 ? ($items['mc_value'] * $items['gross_wt']) : ($items['mc_value'] * $items['piece']));
} else if ($items['calculation_based_on'] == 1) {
    $wastge_wt = ($items['net_wt'] * ($items['wastage_percent'] / 100));
    $mc = ($items['mc_type'] == 2 ? ($items['mc_value'] * $items['net_wt']) : ($items['mc_value'] * $items['piece']));
} else if ($items['calculation_based_on'] == 2) {
    $wastge_wt = ($items['net_wt'] * ($items['wastage_percent'] / 100));
    $mc = ($items['mc_type'] == 2 ? ($items['mc_value'] * $items['gross_wt']) : ($items['mc_value'] * $items['piece']));
}
```

### bill_format_2.php — Sales items MC display cell (line ~784)

#### Before
```php
<td style="...width:7%;"><?php echo $mc > 0 ? moneyFormatIndia($mc) : ''; ?></td>
```

#### After
```php
<td style="...width:7%;"><?php echo $mc > 0 ? moneyFormatIndia(round($mc, 0)) : ''; ?></td>
```

### bill_format_2.php — Return items MC display cell (line ~1169)

#### Before
```php
<td style="width: 15%" class="alignRight"><?php echo $mc > 0 ? moneyFormatIndia(number_format($mc, 2, '.', '')) : ''; ?></td>
```

#### After
```php
<td style="width: 15%" class="alignRight"><?php echo $mc > 0 ? moneyFormatIndia(round($mc, 0)) : ''; ?></td>
```

### receipt_billing.php — Designer Charge row (line ~687)

#### Before
```php
<td class="alignLeft"><?php echo $total_mc ?></td>
```

#### After
```php
<td class="alignLeft"><?php echo moneyFormatIndia(round($total_mc, 0)) ?></td>
```

## Verification
1. Open billing invoice `bill_format_2` format (bill_format != 0) with items that have MC
2. MC column should show whole numbers: e.g. `1,500` not `1,500.00` and not `150/G`
3. Verify MC total = `mc_value × weight` (per-gram) or `mc_value × piece` (per-piece)
4. Open `receipt_billing` format (bill_format == 0) — check Designer Charge row shows rounded total
5. Test both mc_type == 1 (per piece) and mc_type == 2 (per gram) items
6. Test calculation_based_on == 0 (gross_wt), 1 (net_wt), 2 (gross_wt) variants

## Notes
- The **root bug** was in the sales items section of `bill_format_2.php` — `$mc` was
  set as a string `"150/G"` which cannot be used in arithmetic or proper formatting
- The return items section already computed `$mc` correctly — only needed rounding
- `receipt_billing.php` has no per-item MC column — MC shows as a "Designer Charge"
  sub-row after each item, displaying the accumulated `$total_mc`
- mc_type 2 = per gram; mc_type 1 = per piece (some files use reversed convention — verify)
- Apply `mc_discount` reduction AFTER computing `$mc` total (already handled in existing code)
