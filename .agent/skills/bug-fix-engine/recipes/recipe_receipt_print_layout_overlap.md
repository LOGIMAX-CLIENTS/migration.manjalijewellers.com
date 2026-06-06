# Recipe: Column-Aware Reflow Collision Resolution in print_template

> Horizontal collision detection to prevent layout overlap and unwanted vertical gaps in side-by-side print columns.

## Metadata
- **Pattern ID**: PAT-BIL-PRINT02
- **Severity**: HIGH
- **Modules Affected**: Billing (Print / Receipts)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: All templates rendered via `konva_receipt_helper.php` use the same layout reflow algorithm.

## Created By
- **Developer**: Antigravity
- **Client**: etail_development_src
- **Date**: 2026-06-05
- **Source Bug ID**: PRT-CLT02

## Symptom
When text wraps (auto-wrap) in the left column (e.g. addresses or descriptions), the entire page is pushed down, causing:
1. Unwanted vertical gaps on the right-hand column.
2. Elements on the second page overwriting or overlapping one another.
3. Print dialog failing to trigger automatically due to javascript errors.

## Root Cause
The reflow javascript shifted all elements below the wrapped text's Y coordinate regardless of their horizontal position. Additionally, it did not take horizontal overlapping into account or resolve propagation top-to-bottom iteratively.

## Detection
```command
grep -rn "ny >= threshold" admin/application/helpers/
```

## Files
- `admin/application/helpers/konva_receipt_helper.php`

## Fix

See the code changes in `konva_receipt_helper.php`:
- Retrieve `nodeX` and `nodeW` for all nodes in Pass 2 and inject them into output container HTML as `data-node-x` and `data-node-w`.
- Replaced the post-render reflow javascript with a top-to-bottom collision pass:
  - Loop elements sorted from top to bottom.
  - If a lower element overlaps horizontally with an upper element `(prev.x < node.x + node.w) && (node.x < prev.x + prev.w)` and collides vertically, push it down to start `0.5mm` below the upper element's actual bottom.

## Verification
1. Navigate to any bill print page.
2. Ensure columns are aligned properly (wrapped text on the left does not push right-side elements).
3. Confirm print dialog is triggered automatically.

## Notes
- `data-node-w` for tables must be computed as the sum of all visible (non-hidden) column widths to reflect their true horizontal boundaries.
- Collision resolution must be run separately for `data-repeat` and body elements.
