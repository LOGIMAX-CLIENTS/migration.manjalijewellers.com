# Recipe: Bill Print — Separate Gold & Silver Gross Weight Display

## Metadata
- **Pattern ID**: PAT-PRINT-007
- **Severity**: MEDIUM
- **Modules Affected**: Billing (Print), bill_format_2.php
- **Auto-fixable**: No (requires PHP template logic + CSS judgment)

## Client Scope
- **Applies to**: sarangapani | Any client using `bill_format_2.php` with mixed Gold + Silver items
- **Reason**: Clients selling both Gold and Silver items in a single bill need separate G.Wt totals for audit clarity and customer transparency.

## Created By
- **Developer**: Antigravity
- **Client**: sarangapani
- **Date**: 2026-04-29
- **Source Bug ID**: N/A (enhancement request)

## Symptom
When a bill contains both Gold and Silver tagged items, the GRS.WT total row shows only one combined gross weight. Users cannot tell the individual metal-wise contribution from the bill print. The request is:
- If only Gold → show `Total Gold G.Wt : X.XXX`
- If only Silver → show `Total Silver G.Wt : X.XXX`
- If both → show `Total Gold G.Wt : X.XXX   Total Silver G.Wt : X.XXX` on the left side of the SUB TOTAL row

## Root Cause
The item loop in `bill_format_2.php` accumulated a single `$gross_wt` variable. There was no per-metal tracking. The SUB TOTAL row's left cell (`<td colspan="7">`) was always empty — wasted space that can carry the metal-wise G.Wt breakdown.

## Detection
```bash
grep -n "gold_gross_wt\|silver_gross_wt" clients/sarangapani/views/billing/print/bill_format_2.php
```
If no results → recipe not yet applied.

## Files
- `clients/sarangapani/views/billing/print/bill_format_2.php`

## Pre-Fix Checklist
Before applying this fix, verify:
- [ ] Confirm `$items['metal_type']` is available in the item loop (`1` = Gold, `2` = Silver)
- [ ] Confirm the bill has a `<td colspan="7" style="border:none;">` empty cell in the SUB TOTAL row
- [ ] Confirm the bill uses the `foreach ($est_other_item['item_details'] as $items)` loop structure
- [ ] Confirm `$taxable_amt > 0` wraps the SUB TOTAL row (the label goes inside this condition)

## Fix

### Step 1 — Initialize tracking variables (in the variable declaration block before the item loop)

#### Before
```php
$gross_wt = 0;
$net_wt   = 0;
```

#### After
```php
$gross_wt        = 0;
$gold_gross_wt   = 0;
$silver_gross_wt = 0;
$net_wt          = 0;
```

### Step 2 — Accumulate per-metal G.Wt inside the item loop

#### Before
```php
$gross_wt += $items['gross_wt'];
```

#### After
```php
$gross_wt += $items['gross_wt'];
if ($items['metal_type'] == 2) {
    $silver_gross_wt += $items['gross_wt'];
} else {
    $gold_gross_wt += $items['gross_wt'];
}
```

### Step 3 — Display in SUB TOTAL row (replace empty colspan-7 cell)

#### Before
```php
<td colspan="7" style="border:none;"></td>
```

#### After
```php
<td colspan="7" style="padding:3px; border:none; font-weight:bold; font-size:12px;">
    <?php if ($gold_gross_wt > 0) { ?>Total Gold G.Wt : <?php echo number_format($gold_gross_wt, 3, '.', ''); ?><?php } ?>
    <?php if ($gold_gross_wt > 0 && $silver_gross_wt > 0) { ?> &nbsp;&nbsp; <?php } ?>
    <?php if ($silver_gross_wt > 0) { ?>Total Silver G.Wt : <?php echo number_format($silver_gross_wt, 3, '.', ''); ?><?php } ?>
</td>
```

## In-Progress Checklist
While applying the fix:
- [ ] Add `$gold_gross_wt = 0;` and `$silver_gross_wt = 0;` in the same block as `$gross_wt = 0;`
- [ ] Add the `if ($items['metal_type'] == 2)` branch immediately after the `$gross_wt += $items['gross_wt'];` line
- [ ] Replace **only** the SUB TOTAL row's `<td colspan="7" style="border:none;"></td>` — do NOT change the Total row's GRS.WT cell (it keeps the combined `$gross_wt`)
- [ ] Keep the `<?php if ($taxable_amt > 0) { ?>` wrapper intact — the label only renders when there is a taxable amount

## Verification

### Post-Fix Checklist
After applying the fix, verify:
- [ ] Open a bill with **both Gold and Silver** items → confirm `Total Gold G.Wt : X.XXX   Total Silver G.Wt : X.XXX` appears on the left of the SUB TOTAL row
- [ ] Open a bill with **Gold only** → confirm only `Total Gold G.Wt : X.XXX` appears (no empty Silver label)
- [ ] Open a bill with **Silver only** → confirm only `Total Silver G.Wt : X.XXX` appears
- [ ] Confirm the GRS.WT column in the **Total row** still shows the **combined** weight (not split) — the split only appears in the summary area

## Notes
- `metal_type == 1` = Gold, `metal_type == 2` = Silver. All non-silver items fall into Gold bucket by default (the `else` branch).
- The combined `$gross_wt` in the Total row is intentionally kept — changing it would break column alignment.
- Font size for the weight labels is `12px` (matching the ₹ symbol cell next to SUB TOTAL).
- DomPDF renders `display:block` spans reliably — safe to use if labels need to be stacked on separate lines instead.
- Related recipe: `recipe_bill_copy_salesman_field.md` (same file, different section).
