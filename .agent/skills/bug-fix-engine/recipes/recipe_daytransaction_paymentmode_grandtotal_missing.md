# Recipe: Missing Payment Mode Grand Totals in Detailed Day Transaction Report

## Metadata
- **Pattern ID**: PAT-RPT-DDT01
- **Severity**: MEDIUM
- **Modules Affected**: Ret_Reports (Detailed Day Transaction Report)
- **Auto-fixable**: No (requires manual addition of accumulator variables per section)

## Client Scope
- **Applies to**: ALL
- **Reason**: Systemic omission pattern applicable wherever `get_Detailed_Daytransaction_report()` exists

## Created By
- **Developer**: Antigravity AI
- **Client**: navratnajewellery
- **Date**: 2026-04-21
- **Source Bug ID**: RPT-DDT01

## Symptom
In the Detailed Day Transaction Report, the Grand Total rows for each section (Sales, Order Advance, Credit Collection, Advance Receipt, Payment Issue, Repair Order, Purchase Plan) show **empty cells** for all Mode of Payment columns (Cash, Card, Cheque, UPI Scanner, UPI Transfer, RTGS, IMPS, NEFT), while Pieces and Weight grand totals display correctly.

## Root Cause
In `get_Detailed_Daytransaction_report()` inside `ret_reports.js`, each section's `$.each` loop declares **local** payment variables (`cash_payment`, `card_payment`, etc.) that are correctly populated and used in individual row cells. However, **no section-level accumulator variables** (e.g., `tot_sales_cash`, `tot_ord_cash`) were ever declared or accumulated. As a result, the Grand Total row for each section was rendered with empty `<td><strong></strong></td>` cells instead of summed values.

This is a systematic omission — the pattern repeats identically across all 7 sections that collect payment mode data.

**Special case — Sales section**: Payment accumulation only happens when `item.SerialNumber == 1` (first row per bill). The section-level accumulators must mirror this identical guard condition.

## Detection
```
# Search for the function in the JS file
grep -n "get_Detailed_Daytransaction_report" admin/assets/js/ret_reports.js

# Confirm grand total rows have empty payment cells
grep -n "Sales Grand Total" admin/assets/js/ret_reports.js
# Then inspect the rows following that line for empty <td></td> or <td><strong></strong></td>
```

## Files
- `admin/assets/js/ret_reports.js` — function `get_Detailed_Daytransaction_report()`

## Fix

### Step 1: Add section-level accumulator variables (after existing section total declarations)

```js
// After: var tot_pp_amt = 0;
// Add the following payment mode accumulator blocks:

// Sales Payment Mode Totals
var tot_sales_cash = 0;
var tot_sales_card = 0;
var tot_sales_cheque = 0;
var tot_sales_adv_adj = 0;
var tot_sales_upi_scanner = 0;
var tot_sales_upi_transfer = 0;
var tot_sales_rtgs = 0;
var tot_sales_imps = 0;
var tot_sales_neft = 0;
// Order Advance Payment Mode Totals
var tot_ord_cash = 0;
var tot_ord_card = 0;
var tot_ord_cheque = 0;
var tot_ord_upi_scanner = 0;
var tot_ord_upi_transfer = 0;
var tot_ord_rtgs = 0;
var tot_ord_imps = 0;
var tot_ord_neft = 0;
// [... repeat pattern for: tot_cred_, tot_adv_, tot_issue_, tot_rep_, tot_pp_]
```

### Step 2: Accumulate in Sales $.each (inside the `if (SerialNumber == 1)` block)

```js
// BEFORE (close of SerialNumber == 1 block):
            })  // end $.each(item.sales_payment_details...)
        }       // end if (SerialNumber == 1)

// AFTER:
            })
            // Accumulate into section-level payment mode totals (only SerialNumber==1)
            tot_sales_cash += cash_payment;
            tot_sales_card += card_payment;
            tot_sales_cheque += cheque_payment;
            tot_sales_upi_scanner += upi_scanner_payment;
            tot_sales_upi_transfer += upi_transfer_payment;
            tot_sales_rtgs += rtgs_payment;
            tot_sales_imps += imps_payment;
            tot_sales_neft += neft_payment;
        }
        if (item.SerialNumber == 1) {
            tot_sales_adv_adj += parseFloat(item.adv_adj_amt.adj_amt);
        }
```

### Step 3: Accumulate in each other section's $.each (after inner payment loop closes)

```js
// Pattern for Order Advance, Credit, Advance Receipt, Payment Issue, Repair Order, Purchase Plan:
// BEFORE:
            }  // end if (item.payment_details.length > 0)
            tot_ord_taxable_amt += ...

// AFTER:
            }
            tot_ord_cash += cash_payment;
            tot_ord_card += card_payment;
            tot_ord_cheque += cheque_payment;
            tot_ord_upi_scanner += upi_scanner_payment;
            tot_ord_upi_transfer += upi_transfer_payment;
            tot_ord_rtgs += rtgs_payment;
            tot_ord_imps += imps_payment;
            tot_ord_neft += neft_payment;
            tot_ord_taxable_amt += ...
```

### Step 4: Replace empty Grand Total cells with accumulated values

```js
// BEFORE (Sales Grand Total row — payment mode cells):
'<td></td>' +
'<td></td>' +
'<td><strong></strong></td>' +
// ... (empty cells)

// AFTER:
'<td><strong>' + money_format_india(parseFloat(tot_sales_cash).toFixed(2)) + '</strong></td>' +
'<td><strong>' + money_format_india(parseFloat(tot_sales_card).toFixed(2)) + '</strong></td>' +
'<td><strong>' + money_format_india(parseFloat(tot_sales_cheque).toFixed(2)) + '</strong></td>' +
'<td><strong>' + money_format_india(parseFloat(tot_sales_adv_adj).toFixed(2)) + '</strong></td>' +
'<td><strong>' + money_format_india(parseFloat(tot_sales_upi_scanner).toFixed(2)) + '</strong></td>' +
'<td><strong>' + money_format_india(parseFloat(tot_sales_upi_transfer).toFixed(2)) + '</strong></td>' +
'<td><strong>' + money_format_india(parseFloat(tot_sales_rtgs).toFixed(2)) + '</strong></td>' +
'<td><strong>' + money_format_india(parseFloat(tot_sales_imps).toFixed(2)) + '</strong></td>' +
'<td><strong>' + money_format_india(parseFloat(tot_sales_neft).toFixed(2)) + '</strong></td>' +
```

## Verification
1. Open the Detailed Day Transaction Report (`/admin_ret_reports/detailed_day_transaction_report/list`)
2. Set a date range with known transactions
3. Click Search
4. Scroll to each Grand Total row — Cash, Card, Cheque, UPI, RTGS, IMPS, NEFT columns must show summed values in bold red
5. Cross-check: Sum of all individual rows in a payment column = that section's Grand Total value
6. Open browser console — confirm zero JS errors

## Notes
- **Purchase Plan** uses `item.payment_amount` directly (not nested `payment_details`) and `'CSH'` as Cash mode code.
- **Advance Adjustment** (Sales only) is tracked separately with `SerialNumber == 1` guard from `item.adv_adj_amt.adj_amt`.
- **Sales Return** and **Purchase** sections intentionally have no payment mode columns — leave grand total empty (correct behavior).
- Column order: Cash (col 20) → Card (21) → Cheque (22) → Advance Adj (23, Sales only) → UPI Scanner (24) → UPI Transfer (25) → RTGS (26) → IMPS (27) → NEFT (28)
