# Recipe: Hide Rate & Net Weight for Old Metal in Estimation Print + EDA Pure Weight Decimal Fix

## Metadata
- **Pattern ID**: PAT-PRINT-EST-001
- **Severity**: MEDIUM
- **Modules Affected**: Estimation (Print Template)
- **Auto-fixable**: Yes (3 string replacements in client print template)

## Client Scope
- **Applies to**: konika (client-specific print template)
- **Reason**: Each client has its own print template under `clients/{client}/views/estimation/print/est_print_2.php`. Apply analogous changes to other client templates that show `rate_per_gram` in Old Metal row and use raw `$items['pure_wt']` in EDA mode.

## Created By
- **Developer**: Antigravity
- **Client**: konika
- **Date**: 2026-04-22
- **Source Bug ID**: N/A (reported directly, no intake triage)

## Symptom

**Bug 1 — Old Metal Rate shown:**
In the Estimation Bill Print Copy, Old Metal items show `@ {rate_per_gram}` in the item name header row. Business requirement is to hide the rate for Old Metal purchases.

**Bug 2 — Old Metal N.Wt shown:**
The Net Weight (N.Wt) column for Old Metal items displays `$data['net_wt']` in the print. Business requirement is to hide it.

**Bug 3 — EDA pure_wt no decimal:**
In EDA mode, the PURE column shows the raw value of `$items['pure_wt']` without `number_format()`. If `pure_wt` is a decimal (e.g., `2.298`), it may display as `2` or blank depending on DB column type. Also, the item name row was showing `@ pure_rate` in EDA mode, which should be hidden.

## Root Cause

**Bug 1 & 2:** The Old Metal data row in the print template directly echoes `$data['rate_per_gram']` in the item heading TD and `$data['net_wt']` in the data TD — no conditional to hide them.

**Bug 3:** `$items['pure_wt']` is echoed raw without `number_format()`. The item rate condition `if ($items['calculation_based_on'] != 3)` was showing `@ pure_rate` in EDA mode — should be suppressed.

## Detection

```bash
# Find Old Metal rate in print template
grep -n "rate_per_gram\|net_wt\|pure_wt\|is_eda" clients/konika/views/estimation/print/est_print_2.php
```

Look for:
- `$data['old_metal_type'] . " @ " . $data['rate_per_gram']` → Bug 1
- `echo $data['net_wt']` (in Old Metal data row) → Bug 2
- `($is_eda?($items['pure_wt'])` without number_format → Bug 3
- `if ($items['calculation_based_on'] != 3) { ?>` showing rate in EDA → Bug 3b

## Files
- `clients/konika/views/estimation/print/est_print_2.php`
- *(Also patched: `admin/application/views/estimation/print/est_print_konika.php` — secondary template)*

## Fix

### Fix 1 — EDA: Hide rate in item name row & suppress in EDA mode

#### Before
```php
<?php echo substr($items['product_name'], 0, 10) . " " . substr($items['sub_design_name'], 0, 10);
if ($items['calculation_based_on'] != 3) { ?> @ <?php echo $is_eda == 1 
    ? ($items['pure_rate'] ?? '') 
    : ($items['est_rate_per_grm'] ?? ''); } ?>
```

#### After
```php
<?php echo substr($items['product_name'], 0, 10) . " " . substr($items['sub_design_name'], 0, 10);
if ($items['calculation_based_on'] != 3 && !$is_eda) { ?> @ <?php echo ($items['est_rate_per_grm'] ?? ''); } ?>
```

---

### Fix 2 — EDA: pure_wt decimal formatting

#### Before
```php
<td class="alignRight"><?php echo ($is_eda?($items['pure_wt']):($items['net_wt'] != 0 ? number_format($items['net_wt'], 3, '.', '') . ($items['pro_uom'] != '' ? '-' . $items['pro_uom'] : '') : "")); ?></td>
```

#### After
```php
<td class="alignRight"><?php echo ($is_eda ? ($items['pure_wt'] != 0 ? number_format($items['pure_wt'], 3, '.', '') : '') : ($items['net_wt'] != 0 ? number_format($items['net_wt'], 3, '.', '') . ($items['pro_uom'] != '' ? '-' . $items['pro_uom'] : '') : "")); ?></td>
```

---

### Fix 3 — Old Metal: Hide Rate from item heading row

#### Before
```php
<td colspan="5"><?php echo $old_item . ") " . $data['old_metal_type'] . " @ " . $data['rate_per_gram']; ?></td>
```

#### After
```php
<td colspan="5"><?php echo $old_item . ") " . $data['old_metal_type']; ?></td>
```

---

### Fix 4 — Old Metal: Hide N.Wt cell in data row

#### Before
```php
<td class="alignRight"><?php echo $data['net_wt']; ?></td>
```

#### After
```php
<!-- N.Wt hidden for Old Metal as per business requirement -->
<td class="alignRight"></td>
```

---

### Fix 5 — Old Metal: Hide N.Wt total in summary row

#### Before
```php
<td class="alignRight"><?php echo number_format($net_wt, 3, '.', ''); ?></td>
```

#### After
```php
<!-- N.Wt total hidden for Old Metal -->
<td class="alignRight"></td>
```

## Verification

1. Open any estimation with Old Metal items and print:
   `http://localhost/{client}/admin/index.php/admin_ret_estimation/generate_invoice/{est_id}/?client=konika`
2. **Confirm**: Old Metal item heading shows ONLY the metal type name — no `@ rate` suffix
3. **Confirm**: N.Wt column is blank for all Old Metal rows
4. **Confirm**: Amount column still shows correctly
5. For EDA verification:
   - Create estimation with IS EDA checkbox ticked
   - Add item with purity (e.g., 916) and wastage %
   - Click "Print for EDA"
   - **Confirm**: PURE column header shown (not NWT)
   - **Confirm**: pure_wt displays as 3 decimal places (e.g., `2.298`)
   - **Confirm**: Item name does NOT show `@ rate` in EDA mode

## Notes

- The `clients/{client}/views/estimation/print/est_print_2.php` is the LIVE file — the `admin/views/estimation/print/est_print_konika.php` is a secondary/fallback template. **Always patch the client folder first.**
- `pure_wt` DB column type may be `int(11)` in dev but `decimal(12,3)` in production — the `number_format()` fix is required for production correctness.
- `$net_wt` variable is still accumulated in PHP for potential future use — only the display cell is hidden, not the variable.
- Old Metal totals row (`rowClass heading`) also hides N.Wt — matched to same business rule.
