# Old Metal Purchase Report - Employee Wise Subtotal - Unit Test Cases

**Task ID**: 124744000005249200  
**Module**: Old Metal Purchase Report  
**Feature**: Employee Wise Subtotal  
**Date**: 2026-02-15  
**Release**: v0.0.1  
**Developer**: Development Team  

---

## Overview
This document contains unit test cases for the employee-wise subtotal feature implemented in the Old Metal Purchase Report. The feature adds subtotal rows when "Employee Wise" grouping is selected, showing aggregated totals for each employee's transactions.

---

## Test Case 1: Employee Wise Subtotal Display

### Test ID: `OMP-EMP-001`
### Priority: **HIGH**
### Description
Verify that subtotal rows are displayed correctly when "Employee Wise" report type is selected.

### Pre-conditions
- User is logged in
- User has access to Old Metal Purchase Report
- Old metal purchase transactions exist with employee assignments
- Multiple employees have transactions in the selected date range

### Test Steps
1. Navigate to: `Reports > Old Metal Purchase > List` (`/admin_ret_reports/old_metal_purchase/list`)
2. Select **"Employee Wise"** from the "Report Type" dropdown
3. Select date range (e.g., last 30 days)
4. Select metal type (optional)
5. Click "Search" button
6. Observe the report table structure

### Expected Result
- ✅ Report displays employee name as group header
- ✅ All transactions for each employee are listed under their name
- ✅ **Green-highlighted subtotal row** appears after each employee's transactions
- ✅ Subtotal row shows "Sub Total (Employee Name)" in the first column
- ✅ Subtotal row displays totals for:
  - Gross Weight
  - Stone Weight
  - Diamond Weight
  - Dust Weight
  - Pure Weight
  - Wastage Weight
  - Net Weight
  - Value/Amount
- ✅ Grand Total row appears at the bottom with overall totals
- ✅ No DataTables errors in console

### Test Data
| Employee | Transaction Count | Expected Subtotal Gross Wt |
|----------|-------------------|----------------------------|
| John Doe | 3 | 150.500 |
| Jane Smith | 2 | 95.250 |
| Bob Wilson | 1 | 45.000 |

**Expected Grand Total Gross Wt**: 290.750

### Code Reference
- File: [`ret_reports.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_reports.js#L6081-L6113)
- Lines: 6081-6113
- Function: Old metal report rendering (Employee Wise section)

---

## Test Case 2: Subtotal Row Column Count

### Test ID: `OMP-EMP-002`
### Priority: **CRITICAL**
### Description
Verify that the subtotal row has exactly 27 columns to match the table structure and prevent DataTables errors.

### Pre-conditions
- User is logged in
- Old metal purchase transactions exist
- Browser developer console is open

### Test Steps
1. Navigate to: `/admin_ret_reports/old_metal_purchase/list`
2. Select "Employee Wise" report type
3. Select date range and click "Search"
4. Open browser Developer Tools (F12)
5. Go to Console tab
6. Check for any DataTables warnings or errors
7. Inspect the subtotal row HTML in Elements tab
8. Count the number of `<td>` elements in the subtotal row

### Expected Result
- ✅ **No DataTables errors** in console
- ✅ No warnings about "Requested unknown parameter"
- ✅ Subtotal row has exactly **27 `<td>` elements**
- ✅ Column structure matches:
  1. Branch (shows "Sub Total (Employee Name)")
  2. Bill Date (empty)
  3. Bill No (empty)
  4. Customer (empty)
  5. Address (empty)
  6. State (empty)
  7. GST No (empty)
  8. Mobile (empty)
  9. Ornament Category (empty)
  10. Product (empty)
  11. Gross Wgt (shows total)
  12. Stone Wgt (shows total)
  13. Dia Wgt (shows total)
  14. Dust Wgt (shows total)
  15. Pure Wgt (shows total)
  16. Wastage (shows total)
  17. Net Wgt (shows total)
  18. Touch (empty)
  19. Purity % (empty)
  20. Rate (empty)
  21. Value (shows total)
  22. Refund Amount (empty)
  23. Status (empty)
  24. Customer (empty)
  25. Esti No (empty)
  26. Sales Man (empty)
  27. Remark (empty)

### Code Reference
- File: [`ret_reports.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_reports.js#L6081-L6113)
- Lines: 6081-6113

---

## Test Case 3: Subtotal Calculation Accuracy

### Test ID: `OMP-EMP-003`
### Priority: **HIGH**
### Description
Verify that subtotal calculations are mathematically correct for each employee.

### Pre-conditions
- User is logged in
- Test data exists with known values

### Test Steps
1. Create test transactions for employee "Test User":
   - Transaction 1: Gross Wt = 100.500, Net Wt = 95.000, Value = 5000.00
   - Transaction 2: Gross Wt = 50.250, Net Wt = 48.000, Value = 2500.00
   - Transaction 3: Gross Wt = 25.125, Net Wt = 24.000, Value = 1250.00
2. Navigate to Old Metal Purchase Report
3. Select "Employee Wise" report type
4. Filter by "Test User" employee
5. Click "Search"
6. Manually calculate expected totals
7. Compare with displayed subtotal

### Expected Result
- ✅ Gross Weight Subtotal = 100.500 + 50.250 + 25.125 = **175.875**
- ✅ Net Weight Subtotal = 95.000 + 48.000 + 24.000 = **167.000**
- ✅ Value Subtotal = 5000.00 + 2500.00 + 1250.00 = **8,750.00**
- ✅ All weight values displayed with 3 decimal places
- ✅ Value displayed with 2 decimal places
- ✅ Indian number formatting applied (e.g., 1,75.875)

### Test Data
| Transaction | Gross Wt | Stone Wt | Dia Wt | Dust Wt | Pure Wt | Wastage | Net Wt | Value |
|-------------|----------|----------|--------|---------|---------|---------|--------|--------|
| TXN-001 | 100.500 | 5.000 | 2.000 | 1.500 | 92.000 | 3.000 | 95.000 | 5,000.00 |
| TXN-002 | 50.250 | 2.500 | 1.000 | 0.750 | 46.000 | 2.000 | 48.000 | 2,500.00 |
| TXN-003 | 25.125 | 1.250 | 0.500 | 0.375 | 23.000 | 1.000 | 24.000 | 1,250.00 |
| **Subtotal** | **175.875** | **8.750** | **3.500** | **2.625** | **161.000** | **6.000** | **167.000** | **8,750.00** |

---

## Test Case 4: Visual Styling Verification

### Test ID: `OMP-EMP-004`
### Priority: **MEDIUM**
### Description
Verify that subtotal rows have correct visual styling to distinguish them from regular data rows.

### Pre-conditions
- User is logged in
- Old metal purchase transactions exist

### Test Steps
1. Navigate to Old Metal Purchase Report
2. Select "Employee Wise" report type
3. Click "Search"
4. Inspect subtotal row styling using browser DevTools
5. Compare with regular data rows and grand total row

### Expected Result
- ✅ Subtotal row has `font-weight: bold`
- ✅ Subtotal row has `color: green`
- ✅ Subtotal row has `background-color: #f0f0f0` (light gray)
- ✅ Subtotal text is clearly visible and readable
- ✅ Styling is different from:
  - Regular data rows (no background, black text)
  - Grand Total row (blue text)
- ✅ First column text is right-aligned

### Code Reference
- File: [`ret_reports.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_reports.js#L6082)
- Line: 6082
- Style: `style="font-weight:bold;color:green;background-color:#f0f0f0;"`

---

## Test Case 5: Multiple Employees Grouping

### Test ID: `OMP-EMP-005`
### Priority: **HIGH**
### Description
Verify that when multiple employees have transactions, each gets their own subtotal row.

### Pre-conditions
- User is logged in
- Transactions exist for at least 3 different employees

### Test Steps
1. Navigate to Old Metal Purchase Report
2. Select "Employee Wise" report type
3. Select date range covering multiple employees
4. Do NOT filter by specific employee (show all)
5. Click "Search"
6. Count the number of subtotal rows
7. Verify each employee has their own subtotal

### Expected Result
- ✅ Each employee has exactly **one subtotal row**
- ✅ Subtotal rows appear in correct sequence:
  ```
  Employee A Header
    Transaction 1
    Transaction 2
  Sub Total (Employee A)
  
  Employee B Header
    Transaction 1
  Sub Total (Employee B)
  
  Employee C Header
    Transaction 1
    Transaction 2
    Transaction 3
  Sub Total (Employee C)
  
  Grand Total
  ```
- ✅ Number of subtotal rows = Number of employees with transactions
- ✅ Grand Total = Sum of all employee subtotals

---

## Test Case 6: Single Employee Filter

### Test ID: `OMP-EMP-006`
### Priority: **MEDIUM**
### Description
Verify that when filtering by a single employee, only one subtotal row is displayed.

### Pre-conditions
- User is logged in
- Multiple employees have transactions

### Test Steps
1. Navigate to Old Metal Purchase Report
2. Select "Employee Wise" report type
3. Select a specific employee from "Select Employee" dropdown
4. Click "Search"
5. Count subtotal rows

### Expected Result
- ✅ Only **one subtotal row** is displayed
- ✅ Subtotal row shows the selected employee's name
- ✅ Subtotal matches the sum of displayed transactions
- ✅ No other employees' data is shown
- ✅ Grand Total = Employee Subtotal (since only one employee)

---

## Test Case 7: Empty Result Set

### Test ID: `OMP-EMP-007`
### Priority: **MEDIUM**
### Description
Verify that no subtotal rows are displayed when no transactions match the filter criteria.

### Pre-conditions
- User is logged in

### Test Steps
1. Navigate to Old Metal Purchase Report
2. Select "Employee Wise" report type
3. Select a date range with no transactions
4. Click "Search"

### Expected Result
- ✅ No data rows displayed
- ✅ No subtotal rows displayed
- ✅ No grand total row displayed
- ✅ Empty table message shown (if applicable)
- ✅ No JavaScript errors in console

---

## Test Case 8: Print/Export Functionality

### Test ID: `OMP-EMP-008`
### Priority: **MEDIUM**
### Description
Verify that subtotal rows are included when printing or exporting the report.

### Pre-conditions
- User is logged in
- Employee wise report is displayed with subtotals

### Test Steps
1. Generate Employee Wise report with multiple employees
2. Click "Print" button
3. Verify print preview
4. Close print preview
5. Click "Excel Export" button (if available)
6. Open exported file

### Expected Result
- ✅ Print preview includes all subtotal rows
- ✅ Subtotal rows maintain green color in print (if color printing)
- ✅ Excel export includes subtotal rows
- ✅ Subtotal rows are properly formatted in Excel
- ✅ All calculations remain accurate in export

---

## Test Case 9: Detailed Report Type (No Subtotals)

### Test ID: `OMP-EMP-009`
### Priority: **MEDIUM**
### Description
Verify that subtotals also appear in "Detailed" report type (report_type = 2) which uses the same code path.

### Pre-conditions
- User is logged in

### Test Steps
1. Navigate to Old Metal Purchase Report
2. Select **"Detailed"** report type (not Employee Wise)
3. Click "Search"
4. Observe if subtotal rows appear

### Expected Result
- ✅ Subtotal rows appear grouped by **category** (not employee)
- ✅ Each category has its own subtotal row
- ✅ Subtotal calculations are correct
- ✅ Visual styling is consistent with employee-wise subtotals

### Code Reference
- File: [`ret_reports.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_reports.js#L5940)
- Line: 5940
- Condition: `if ($('#oldmetal_report_type').val() == 2 || $('#oldmetal_report_type').val() == 4)`

---

## Test Case 10: Grand Total Accuracy

### Test ID: `OMP-EMP-010`
### Priority: **CRITICAL**
### Description
Verify that the Grand Total row correctly sums all employee subtotals.

### Pre-conditions
- User is logged in
- Multiple employees have transactions

### Test Steps
1. Generate Employee Wise report with 3+ employees
2. Note each employee's subtotal values
3. Manually calculate sum of all subtotals
4. Compare with Grand Total row

### Expected Result
- ✅ Grand Total Gross Weight = Sum of all employee subtotal gross weights
- ✅ Grand Total Net Weight = Sum of all employee subtotal net weights
- ✅ Grand Total Value = Sum of all employee subtotal values
- ✅ All weight fields match manual calculations
- ✅ Grand Total row is visually distinct (blue color, bold)

### Test Data Example
| Employee | Gross Wt Subtotal | Net Wt Subtotal | Value Subtotal |
|----------|-------------------|-----------------|----------------|
| Employee A | 150.500 | 145.000 | 7,500.00 |
| Employee B | 95.250 | 90.000 | 4,750.00 |
| Employee C | 45.000 | 42.000 | 2,100.00 |
| **Grand Total** | **290.750** | **277.000** | **14,350.00** |

---

## Test Case 11: Number Formatting

### Test ID: `OMP-EMP-011`
### Priority: **MEDIUM**
### Description
Verify that all numeric values in subtotal rows use correct Indian number formatting.

### Pre-conditions
- User is logged in
- Transactions exist with various weight values

### Test Steps
1. Generate Employee Wise report
2. Inspect subtotal row values
3. Verify number formatting

### Expected Result
- ✅ Weight values use 3 decimal places (e.g., 1,234.567)
- ✅ Value/Amount uses 2 decimal places (e.g., 12,345.67)
- ✅ Indian comma separator used (e.g., 1,23,456.78)
- ✅ `money_format_india()` function applied correctly
- ✅ No values show as NaN or undefined

### Code Reference
- File: [`ret_reports.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_reports.js#L6091-L6103)
- Lines: 6091-6103
- Function: `money_format_india()`

---

## Test Case 12: Browser Compatibility

### Test ID: `OMP-EMP-012`
### Priority: **MEDIUM**
### Description
Verify that the subtotal feature works correctly across different browsers.

### Pre-conditions
- User is logged in
- Same test data available

### Test Steps
1. Test in Chrome browser
2. Test in Firefox browser
3. Test in Edge browser
4. Generate same Employee Wise report in each browser
5. Compare results

### Expected Result
- ✅ Subtotal rows display correctly in Chrome
- ✅ Subtotal rows display correctly in Firefox
- ✅ Subtotal rows display correctly in Edge
- ✅ Styling is consistent across browsers
- ✅ No browser-specific JavaScript errors
- ✅ Calculations are identical across browsers

---

## Regression Test Cases

### Test ID: `OMP-EMP-REG-001`
### Description: Verify existing report types still work
- ✅ Summary report (report_type = 1) works without subtotals
- ✅ Bill Wise report (report_type = 3) works without subtotals
- ✅ All filters (branch, metal, date) work correctly
- ✅ No existing functionality is broken

### Test ID: `OMP-EMP-REG-002`
### Description: Verify DataTables functionality
- ✅ Sorting works correctly
- ✅ Pagination works correctly
- ✅ Search/filter works correctly
- ✅ Column visibility toggle works
- ✅ Export buttons work (Print, Excel, Column Visibility)

---

## Test Execution Summary Template

| Test ID | Test Case | Priority | Status | Tested By | Date | Notes |
|---------|-----------|----------|--------|-----------|------|-------|
| OMP-EMP-001 | Employee Wise Subtotal Display | HIGH | ⬜ Pending | | | |
| OMP-EMP-002 | Subtotal Row Column Count | CRITICAL | ⬜ Pending | | | |
| OMP-EMP-003 | Subtotal Calculation Accuracy | HIGH | ⬜ Pending | | | |
| OMP-EMP-004 | Visual Styling Verification | MEDIUM | ⬜ Pending | | | |
| OMP-EMP-005 | Multiple Employees Grouping | HIGH | ⬜ Pending | | | |
| OMP-EMP-006 | Single Employee Filter | MEDIUM | ⬜ Pending | | | |
| OMP-EMP-007 | Empty Result Set | MEDIUM | ⬜ Pending | | | |
| OMP-EMP-008 | Print/Export Functionality | MEDIUM | ⬜ Pending | | | |
| OMP-EMP-009 | Detailed Report Type | MEDIUM | ⬜ Pending | | | |
| OMP-EMP-010 | Grand Total Accuracy | CRITICAL | ⬜ Pending | | | |
| OMP-EMP-011 | Number Formatting | MEDIUM | ⬜ Pending | | | |
| OMP-EMP-012 | Browser Compatibility | MEDIUM | ⬜ Pending | | | |

**Legend**: ⬜ Pending | ✅ Pass | ❌ Fail | ⚠️ Blocked

---

## Bug Report Template

If any test case fails, use this template to report the bug:

```
Bug ID: BUG-OMP-EMP-XXX
Test Case: [Test ID]
Severity: [Critical/High/Medium/Low]
Status: [Open/In Progress/Fixed/Closed]

Description:
[Describe what went wrong]

Steps to Reproduce:
1. [Step 1]
2. [Step 2]
3. [Step 3]

Expected Result:
[What should happen]

Actual Result:
[What actually happened]

Screenshots/Logs:
[Attach if available]

Environment:
- Browser: [Chrome/Firefox/Edge]
- Version: [Browser version]
- OS: [Windows/Mac/Linux]
- Screen Resolution: [e.g., 1920x1080]
```

---

## Files Modified

| File | Lines Modified | Description |
|------|----------------|-------------|
| [`ret_reports.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_reports.js#L6081-L6113) | 6081-6113 | Added employee-wise subtotal row with 27 columns |
| [`old_metal_purchase_employee_subtotal.md`](file:///c:/xampp/htdocs/etail_development_src/dev_notes/old_metal_purchase_employee_subtotal.md) | New File | Feature documentation |

---

## Notes for QA Team

1. **Critical Tests**: Test cases OMP-EMP-002 and OMP-EMP-010 are critical as they ensure data integrity and prevent DataTables errors
2. **Browser Testing**: Test on Chrome, Firefox, and Edge browsers (minimum)
3. **Data Verification**: Always verify calculations manually for at least one employee
4. **Performance**: Test with large datasets (100+ transactions) to ensure no performance degradation
5. **Print Testing**: Verify print output matches screen display
6. **Excel Export**: Verify exported data maintains formatting and accuracy

---

## Known Limitations

1. Subtotal feature only works for report types 2 (Detailed) and 4 (Employee Wise)
2. Subtotal rows are not interactive (cannot be clicked or expanded)
3. Subtotal rows are included in DataTables row count

---

## Future Enhancements

1. Add option to show/hide subtotal rows
2. Add subtotal rows for other report types (Summary, Bill Wise)
3. Add drill-down functionality to subtotal rows
4. Add export option for subtotals only

---

**Document Version**: 1.0  
**Last Updated**: 2026-02-15  
**Created By**: Development Team  
**Feature Release**: v0.0.1
