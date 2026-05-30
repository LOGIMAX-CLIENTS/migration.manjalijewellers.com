# Recipe: Tagging Weight Tolerance Percentage Miscalculation

> weight_per setting (a percentage) is added directly to balance weight instead of being calculated as a percentage, causing tolerance to be weight_per grams instead of weight_per% of balance.

## Metadata
- **Pattern ID**: PAT-TAG-TOLERANCE-001
- **Severity**: HIGH
- **Modules Affected**: Tagging
- **Auto-fixable**: Yes (string replacement in ret_tagging.js)

## Client Scope
- **Applies to**: ALL
- **Reason**: Any client with ret_settings `weight_per` enabled will hit this if their JS has the wrong formula

## Created By
- **Developer**: Antigravity
- **Client**: Sri Amman Jewellers (amman)
- **Date**: 2026-04-08 (updated 2026-04-08)
- **Source Bug ID**: N/A

## Symptom
**Weight tolerance broken**: Setting weight_per=10 (meaning 10%) on a 10g lot should allow up to 11g, but allows up to 20g. Setting weight_per=1 (1%) should allow 10.1g but allows 11g. The tolerance is treated as absolute grams instead of a percentage.

## Root Cause

### Version A (string concatenation — older code)
`$('#weight_per').val()` returns a **string**. The code does `lot_bal_wt + weight_per` which triggers JS string concatenation: `10 + "1" = "101"` instead of `11`.

### Version B (percentage treated as absolute — current code after partial fix)
Even after parseFloat is applied to remove string concat, the formula is **`lot_bal_wt + weight_per`** which adds the percentage value directly: `10 + 10 = 20` instead of the correct `10 + (10*10/100) = 11`.

**The correct formula is**: `(lot_bal_wt * weight_per / 100) + lot_bal_wt`

## Detection
```command
Select-String -Path "admin/assets/js/ret_tagging.js" -Pattern "lot_bal_wt.*\+.*weight_per" | ForEach-Object { "$($_.LineNumber): $($_.Line.Trim())" }
```
If any line does NOT contain the pattern `(lot_bal_wt * weight_per) / 100` → Bug is present.

## Files
- `admin/assets/js/ret_tagging.js`

## Fix Locations (2 places)

### Location 1: `set_multiple_rows()` — lot inward detail iteration

#### Before (WRONG)
```javascript
lot_bal_wt = parseFloat(((item['lot_blc'].lot_bal_wt + weight_per)));
```

#### After (CORRECT)
```javascript
lot_bal_wt = parseFloat(((parseFloat(item['lot_blc'].lot_bal_wt) * parseFloat(weight_per)) / 100) + parseFloat(item['lot_blc'].lot_bal_wt));
```

### Location 2: `checking_lot_availability()` — tag_blc_gross calculation

#### Before (WRONG)
```javascript
$('#tag_blc_gross').val(parseFloat(parseFloat(lot_bal_wt) + parseFloat(weight_per)));
```

#### After (CORRECT)
```javascript
$('#tag_blc_gross').val(parseFloat(((parseFloat(lot_bal_wt) * parseFloat(weight_per)) / 100) + parseFloat(lot_bal_wt)));
```

## Verification
1. Set `weight_per=10` in Retail Settings (meaning 10%)
2. Create a lot with 10.000g
3. In Tagging Add:
   - Enter Gross Wt = 11 → should accept (10 + 10% = 11)
   - Enter Gross Wt = 12 → should reject with "balance weight is 11.000"
4. Set `weight_per=1` (meaning 1%):
   - Enter Gross Wt = 10.1 → should accept (10 + 1% = 10.1)
   - Enter Gross Wt = 10.2 → should reject
5. Set `weight_per=0` → tolerance disabled, strict check applies

## Math Verification
| lot_bal_wt | weight_per | Expected Max | Old (Wrong) | New (Correct) |
|------------|-----------|-------------|-------------|---------------|
| 10         | 10 (10%)  | 11.000      | 20.000      | 11.000        |
| 10         | 1 (1%)    | 10.100      | 11.000      | 10.100        |
| 50         | 5 (5%)    | 52.500      | 55.000      | 52.500        |
| 100        | 10 (10%)  | 110.000     | 110.000     | 110.000       |

## Notes
- The hidden input `#weight_per` in `tagging/form.php` must exist for the JS to read.
- `$('#weight_per').val()` always returns a string — always use `parseFloat()`.
- The old backup files (ret_tagging_28.04.2023.js, ret_tagging15-03-2022.js) had the CORRECT `(lot_bal_wt*weight_per)/100+lot_bal_wt` formula. The bug was introduced when someone simplified the formula during a previous fix.
- Related: Any `.val()` used in arithmetic without `parseFloat()` is a potential string-concat bug (PAT-JS-TYPE-001).
