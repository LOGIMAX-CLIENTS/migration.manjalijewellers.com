# Reports Module — Fix Report

> Bug fixes applied to the Reports module.
> Last Updated: 2026-03-14

---

### Fix: RPT-CLT01 — Weight Range Not Showing in Re-Order Setting Report
- **Date:** 2026-03-03
- **Track:** A (System)
- **Category:** Logic
- **Severity:** P1
- **Files Changed:** `admin/application/models/ret_reports_model.php` (line 3023)
- **Root Cause:** `CONCAT(IFNULL(wt.weight_description, ''), ' ', IFNULL(m.uom_name, ''))` produced blank when `weight_description` was NULL/empty.
- **Fix Applied:** Added IF/fallback logic — uses `weight_description + UOM` when available, else `from_weight - to_weight` range format.
- **Tests:** PASS — 6 tests, 7 assertions (`admin/tests/ReportsLogicTest.php`)
- **Pattern:** NEW — PAT-QRY-005 (NULL/Empty Fallback in SQL Display Column)
- **Rollback:** Revert line 3023 to original CONCAT expression.
- **GitHub Issue:** [#1072](https://github.com/Logimax-Technologies/etail_development_src/issues/1072) (Closed)
---

### Fix: RPT-CLT02 — Rejected Weight Only in Return Rows (Purchase Payments Report)
- **Date:** 2026-03-06
- **Track:** B (Business)
- **Category:** Logic
- **Severity:** P1
- **Files Changed:** `admin/application/models/ret_reports_model.php` (line 20494)
- **Root Cause:** The first SELECT (payment/ratecut rows) in the UNION query had a LEFT JOIN on `ret_purchase_return_items` that populated `rejected_gwt` and `rejected_purewt` — these values should only appear on return rows.
- **Fix Applied:** Replaced `IFNULL(ret.rejected_gwt, 0)` / `IFNULL(ret.rejected_purewt, 0)` with `0 as rejected_gwt, 0 as rejected_purewt` in the first SELECT; removed the LEFT JOIN subquery. Return rows (2nd SELECT) unchanged.
- **Tests:** PHP lint PASS. Manual verification required.
- **Pattern:** NEW — PAT-QRY-006 (UNION Scope Leak — Data Appearing in Wrong Row Type)
- **Rollback:** Re-add the LEFT JOIN subquery on `ret_purchase_return_items` and restore `IFNULL(ret.rejected_gwt, 0)` in first SELECT.
- **GitHub Issue:** [#1098](https://github.com/Logimax-Technologies/etail_development_src/issues/1098)
---

### Fix: RPT-CLT03 — Credit Issued Report: Received Amount Missing Discount & Issued Balance Incorrect
- **Date:** 2026-03-14
- **Track:** A (System)
- **Category:** Logic
- **Severity:** P0
- **Files Changed:** `admin/application/models/ret_reports_model.php` (lines 2056, 2093–2095, 2110)
- **Root Cause:** (A) `tot_amt_received` for Received report didn't include `credit_disc_amt`; (B) `due_amt` formula for Issued report was `(tot_amt_received - credit_ret_amt)` instead of `(tot_bill_amount - tot_amt_received - credit_ret_amt)`.
- **Fix Applied:** (A) Added `credit_disc_amt` to SQL SELECT and PHP sum; (B) Corrected `due_amt` formula.
- **Tests:** PHP lint PASS. Manual verification by user.
- **Pattern:** Matches PAT-LOGIC-002 (Return-Omission in Debt Calculation) — similar formula omission pattern.
- **Rollback:** Revert 3 changes in `getcreditBill()` function.
- **GitHub Issue:** [#1244](https://github.com/Logimax-Technologies/etail_development_src/issues/1244) (Closed)
---

### Fix: RPT-CLT04 — Chit Closing Details: Column Misalignment, Missing Columns & JS Concatenation Bugs
- **Date:** 2026-03-28
- **Track:** A (System)
- **Category:** UI/Display + JavaScript
- **Severity:** P2
- **Files Changed:** `admin/application/views/ret_reports/chit_closing.php`, `admin/assets/js/ret_reports.js` (function `set_chit_closing_table`)
- **Root Cause:** (A) View missing 4 column headers; (B) JS unary-plus bug `+ +'<td>'` → NaN → lost table cell → DataTables column mismatch error; (C) Missing `+` for `</tr>` closing tags; (D) Incorrect columnDefs alignment targets.
- **Fix Applied:** (A) Restructured view with 15 columns; (B) Fixed `+ +'<td>'` → `+ '<td>'`; (C) Fixed `'</tr>';` → `+ '</tr>';`; (D) Updated columnDefs [0-7] left, [8-14] right; (E) Added Grand Total for all 7 financial columns.
- **Tests:** Manual verification — DataTables error resolved, columns correctly positioned and aligned.
- **Pattern:** NEW — PAT-JS-006 (Unary Plus in String Concatenation)
- **Rollback:** Revert `chit_closing.php` and `set_chit_closing_table()` function in `ret_reports.js`.
- **GitHub Issue:** [#1642](https://github.com/Logimax-Technologies/etail_development_src/issues/1642) (Closed)
---
