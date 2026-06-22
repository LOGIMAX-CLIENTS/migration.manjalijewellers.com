# Recipe: Credit History Report — DataTables mData Error + Negative Balance Values

## Metadata
- **Pattern ID**: PAT-DT-002
- **Severity**: MEDIUM
- **Modules Affected**: ret_reports (Credit History)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Any client using credit_history report with the Remark column in JS but not in thead will hit this

## Created By
- **Developer**: Antigravity
- **Client**: pondy (pondythangamaaligai.com)
- **Date**: 2026-04-22
- **Source Bug ID**: N/A

## Symptom
1. **Console error**: `Uncaught TypeError: Cannot read properties of undefined (reading 'mData')` at `jquery.dataTables.min.js` when loading Credit History report
2. **Negative values**: Balance amount columns show negative numbers when payments/discounts/old_metal exceed the original bill amount

## Root Cause
1. **mData error**: The `set_credit_history_table()` JS function generates 12 `<td>` cells per row (including a Remark column), but the `credit_history.php` view's `<thead>` only has 11 `<th>` elements (missing Remark). DataTables requires an exact match between thead columns and tbody cells.
2. **Negative balances**: `bill_bal_amt` is computed as running subtraction (`bal_amt - received - discount - old_metal`) which can go below zero. No floor/clamp was applied before display. Same for `sub_total_bal_amt` and `total_bal_amount`.

## Detection
```command
grep -n "item.remark\|value.remark" admin/assets/js/ret_reports.js | grep -i "credit_history\|set_credit_history"
```
Cross-check: count `<th>` in `credit_history.php` thead vs `<td>` count in JS data rows.

## Files
- `admin/assets/js/ret_reports.js` — `set_credit_history_table()` function
- `admin/application/views/ret_reports/credit_history.php` — table header

## Fix

### Option A: Remove Remark from JS (match 11-column thead) — PREFERRED

#### Before (data row)
```javascript
+ '<td>' + (item.bal_amt > 0 ? money_format_india(parseFloat(item.bal_amt || 0).toFixed(2)) : parseFloat(0).toFixed(2)) + '</td>'
+ '<td>' + (item.remark ? item.remark : '-') + '</td>'
+ '</tr>';
```

#### After (data row)
```javascript
+ '<td>' + (item.bal_amt > 0 ? money_format_india(parseFloat(item.bal_amt || 0).toFixed(2)) : parseFloat(0).toFixed(2)) + '</td>'
+ '</tr>';
```

#### Before (collection row)
```javascript
+ '<td>' + money_format_india(parseFloat(bill_bal_amt).toFixed(2)) + '</td>'
+ '<td>' + (value.remark ? value.remark : '-') + '</td>'
+ '</tr>';
```

#### After (collection row — also fixes negative balance)
```javascript
+ '<td>' + money_format_india(Math.max(0, parseFloat(bill_bal_amt)).toFixed(2)) + '</td>'
+ '</tr>';
```

#### Before (sub-total row)
```javascript
+ '<td>' + money_format_india(parseFloat(sub_total_bal_amt || 0).toFixed(2)) + '</td>'
+ '<td></td>'
+ '</tr>';
```

#### After (sub-total row — also fixes negative balance)
```javascript
+ '<td>' + money_format_india(Math.max(0, parseFloat(sub_total_bal_amt || 0)).toFixed(2)) + '</td>'
+ '</tr>';
```

#### Before (grand total row)
```javascript
+ '<td>' + money_format_india(total_bal_amount || 0) + '</td>'
+ '<td></td>'
+ '</tr>';
```

#### After (grand total row — also fixes negative balance)
```javascript
+ '<td>' + money_format_india(Math.max(0, total_bal_amount) || 0) + '</td>'
+ '</tr>';
```

### Option B: Add Remark to thead (match 12-column JS)
Add `<th>Remark</th>` after `<th>Balance Amount</th>` in `credit_history.php`.

## Verification
1. Open Credit History report in browser
2. Select a customer with credit bills, set date range, click Search
3. Open browser console (F12) — confirm NO `mData` error
4. Check all Balance Amount cells — NO negative values should appear
5. Verify Sub Total and Grand Total balance rows are non-negative
6. Test with a customer who has overpaid a credit bill (total payments > bill amount) — balance should show 0.00, not negative

## Notes
- The `columnDefs` targets `[5, 6, 7, 8, 9, 10]` remain valid for 11 columns (0-indexed)
- This is a variant of PAT-DT-001 (DataTables column mismatch pattern)
- The negative balance fix uses `Math.max(0, value)` clamping — the original `item.bal_amt` row already had a `> 0` guard but the collection sub-rows and totals did not
- Source repo (`etail_development_src`) has the same Remark column in JS — may need the same fix there
