# Old Metal Purchase Report - Employee Wise Subtotal - Unit Test Results

**Task ID**: 124744000005249200  
**Test Date**: 2026-02-15  
**Tester**: [To be filled]  
**Environment**: Development  
**Browser**: [To be filled]  
**Application URL**: http://localhost/etail_development_src/admin/  

---

## Test Execution Summary

| Test ID | Test Case | Priority | Status | Tested By | Date | Pass/Fail | Notes |
|---------|-----------|----------|--------|-----------|------|-----------|-------|
| OMP-EMP-001 | Employee Wise Subtotal Display | HIGH | ⬜ Pending | | | | |
| OMP-EMP-002 | Subtotal Row Column Count | CRITICAL | ⬜ Pending | | | | |
| OMP-EMP-003 | Subtotal Calculation Accuracy | HIGH | ⬜ Pending | | | | |
| OMP-EMP-004 | Visual Styling Verification | MEDIUM | ⬜ Pending | | | | |
| OMP-EMP-005 | Multiple Employees Grouping | HIGH | ⬜ Pending | | | | |
| OMP-EMP-006 | Single Employee Filter | MEDIUM | ⬜ Pending | | | | |
| OMP-EMP-007 | Empty Result Set | MEDIUM | ⬜ Pending | | | | |
| OMP-EMP-008 | Print/Export Functionality | MEDIUM | ⬜ Pending | | | | |
| OMP-EMP-009 | Detailed Report Type | MEDIUM | ⬜ Pending | | | | |
| OMP-EMP-010 | Grand Total Accuracy | CRITICAL | ⬜ Pending | | | | |
| OMP-EMP-011 | Number Formatting | MEDIUM | ⬜ Pending | | | | |
| OMP-EMP-012 | Browser Compatibility | MEDIUM | ⬜ Pending | | | | |
| OMP-EMP-REG-001 | Existing Report Types | MEDIUM | ⬜ Pending | | | | |
| OMP-EMP-REG-002 | DataTables Functionality | MEDIUM | ⬜ Pending | | | | |

**Legend**: ⬜ Pending | ✅ Pass | ❌ Fail | ⚠️ Blocked | 🔄 In Progress

---

## Overall Test Statistics

- **Total Test Cases**: 14
- **Passed**: 0
- **Failed**: 0
- **Blocked**: 0
- **Pending**: 14
- **Pass Rate**: 0%

---

## Test Case 1: Employee Wise Subtotal Display (OMP-EMP-001)

### Test Information
- **Priority**: HIGH
- **Status**: ⬜ Pending
- **Tester**: [To be filled]
- **Test Date**: [To be filled]

### Test Steps
1. Navigate to: `Reports > Old Metal Purchase > List`
2. Select "Employee Wise" from Report Type dropdown
3. Select date range (last 30 days)
4. Click "Search" button
5. Observe report table structure

### Expected Results
- ✅ Report displays employee name as group header
- ✅ All transactions for each employee listed
- ✅ Green-highlighted subtotal row after each employee
- ✅ Subtotal shows "Sub Total (Employee Name)"
- ✅ Subtotal displays totals for all weight fields
- ✅ Grand Total row at bottom
- ✅ No DataTables errors

### Actual Results
[To be filled during testing]

### Test Data Used
[To be filled during testing]

### Screenshots
[Attach screenshots if available]

### Pass/Fail
⬜ Not Tested | ✅ Pass | ❌ Fail

### Comments
[To be filled during testing]

---

## Test Case 2: Subtotal Row Column Count (OMP-EMP-002)

### Test Information
- **Priority**: CRITICAL
- **Status**: ⬜ Pending
- **Tester**: [To be filled]
- **Test Date**: [To be filled]

### Test Steps
1. Navigate to Old Metal Purchase Report
2. Select "Employee Wise" report type
3. Click "Search"
4. Open browser Developer Tools (F12)
5. Check Console for errors
6. Inspect subtotal row HTML
7. Count `<td>` elements

### Expected Results
- ✅ No DataTables errors in console
- ✅ No "Requested unknown parameter" warnings
- ✅ Subtotal row has exactly 27 `<td>` elements
- ✅ All columns properly aligned

### Actual Results
[To be filled during testing]

### Console Errors/Warnings
[To be filled during testing]

### Column Count Verification
- Expected: 27 columns
- Actual: [To be filled]

### Pass/Fail
⬜ Not Tested | ✅ Pass | ❌ Fail

### Comments
[To be filled during testing]

---

## Test Case 3: Subtotal Calculation Accuracy (OMP-EMP-003)

### Test Information
- **Priority**: HIGH
- **Status**: ⬜ Pending
- **Tester**: [To be filled]
- **Test Date**: [To be filled]

### Test Steps
1. Generate report for specific employee
2. Note individual transaction values
3. Manually calculate expected totals
4. Compare with displayed subtotal

### Expected Results
- ✅ Gross Weight subtotal matches sum
- ✅ Net Weight subtotal matches sum
- ✅ Value subtotal matches sum
- ✅ All weights show 3 decimal places
- ✅ Value shows 2 decimal places
- ✅ Indian number formatting applied

### Actual Results
[To be filled during testing]

### Manual Calculation
| Field | Transaction 1 | Transaction 2 | Transaction 3 | Expected Total | Actual Total | Match? |
|-------|---------------|---------------|---------------|----------------|--------------|--------|
| Gross Wt | | | | | | |
| Stone Wt | | | | | | |
| Net Wt | | | | | | |
| Value | | | | | | |

### Pass/Fail
⬜ Not Tested | ✅ Pass | ❌ Fail

### Comments
[To be filled during testing]

---

## Test Case 4: Visual Styling Verification (OMP-EMP-004)

### Test Information
- **Priority**: MEDIUM
- **Status**: ⬜ Pending
- **Tester**: [To be filled]
- **Test Date**: [To be filled]

### Test Steps
1. Generate Employee Wise report
2. Inspect subtotal row styling
3. Compare with regular rows and grand total

### Expected Results
- ✅ Subtotal has `font-weight: bold`
- ✅ Subtotal has `color: green`
- ✅ Subtotal has `background-color: #f0f0f0`
- ✅ Text is clearly visible
- ✅ Different from regular rows
- ✅ Different from Grand Total (blue)

### Actual Results
[To be filled during testing]

### Style Verification
| Property | Expected | Actual | Match? |
|----------|----------|--------|--------|
| font-weight | bold | | |
| color | green | | |
| background-color | #f0f0f0 | | |

### Pass/Fail
⬜ Not Tested | ✅ Pass | ❌ Fail

### Comments
[To be filled during testing]

---

## Test Case 5: Multiple Employees Grouping (OMP-EMP-005)

### Test Information
- **Priority**: HIGH
- **Status**: ⬜ Pending
- **Tester**: [To be filled]
- **Test Date**: [To be filled]

### Test Steps
1. Select "Employee Wise" report type
2. Select date range with multiple employees
3. Do NOT filter by specific employee
4. Click "Search"
5. Count subtotal rows

### Expected Results
- ✅ Each employee has one subtotal row
- ✅ Subtotals appear in correct sequence
- ✅ Number of subtotals = Number of employees
- ✅ Grand Total = Sum of all subtotals

### Actual Results
[To be filled during testing]

### Employee Count Verification
- Number of employees: [To be filled]
- Number of subtotal rows: [To be filled]
- Match: ⬜ Yes | ⬜ No

### Pass/Fail
⬜ Not Tested | ✅ Pass | ❌ Fail

### Comments
[To be filled during testing]

---

## Test Case 6: Single Employee Filter (OMP-EMP-006)

### Test Information
- **Priority**: MEDIUM
- **Status**: ⬜ Pending
- **Tester**: [To be filled]
- **Test Date**: [To be filled]

### Test Steps
1. Select "Employee Wise" report type
2. Select specific employee from dropdown
3. Click "Search"
4. Count subtotal rows

### Expected Results
- ✅ Only one subtotal row displayed
- ✅ Subtotal shows selected employee name
- ✅ Subtotal matches sum of transactions
- ✅ No other employees shown
- ✅ Grand Total = Employee Subtotal

### Actual Results
[To be filled during testing]

### Pass/Fail
⬜ Not Tested | ✅ Pass | ❌ Fail

### Comments
[To be filled during testing]

---

## Test Case 7: Empty Result Set (OMP-EMP-007)

### Test Information
- **Priority**: MEDIUM
- **Status**: ⬜ Pending
- **Tester**: [To be filled]
- **Test Date**: [To be filled]

### Test Steps
1. Select "Employee Wise" report type
2. Select date range with no transactions
3. Click "Search"

### Expected Results
- ✅ No data rows displayed
- ✅ No subtotal rows displayed
- ✅ No grand total row
- ✅ Empty table message shown
- ✅ No JavaScript errors

### Actual Results
[To be filled during testing]

### Pass/Fail
⬜ Not Tested | ✅ Pass | ❌ Fail

### Comments
[To be filled during testing]

---

## Test Case 8: Print/Export Functionality (OMP-EMP-008)

### Test Information
- **Priority**: MEDIUM
- **Status**: ⬜ Pending
- **Tester**: [To be filled]
- **Test Date**: [To be filled]

### Test Steps
1. Generate Employee Wise report
2. Click "Print" button
3. Verify print preview
4. Click "Excel Export" button
5. Open exported file

### Expected Results
- ✅ Print preview includes subtotal rows
- ✅ Subtotal maintains green color
- ✅ Excel export includes subtotals
- ✅ Subtotals properly formatted in Excel
- ✅ Calculations accurate in export

### Actual Results
[To be filled during testing]

### Pass/Fail
⬜ Not Tested | ✅ Pass | ❌ Fail

### Comments
[To be filled during testing]

---

## Test Case 9: Detailed Report Type (OMP-EMP-009)

### Test Information
- **Priority**: MEDIUM
- **Status**: ⬜ Pending
- **Tester**: [To be filled]
- **Test Date**: [To be filled]

### Test Steps
1. Select "Detailed" report type (not Employee Wise)
2. Click "Search"
3. Observe subtotal rows

### Expected Results
- ✅ Subtotal rows appear by category
- ✅ Each category has subtotal
- ✅ Calculations are correct
- ✅ Styling consistent with employee-wise

### Actual Results
[To be filled during testing]

### Pass/Fail
⬜ Not Tested | ✅ Pass | ❌ Fail

### Comments
[To be filled during testing]

---

## Test Case 10: Grand Total Accuracy (OMP-EMP-010)

### Test Information
- **Priority**: CRITICAL
- **Status**: ⬜ Pending
- **Tester**: [To be filled]
- **Test Date**: [To be filled]

### Test Steps
1. Generate report with 3+ employees
2. Note each employee subtotal
3. Manually calculate sum
4. Compare with Grand Total

### Expected Results
- ✅ Grand Total Gross Wt = Sum of subtotals
- ✅ Grand Total Net Wt = Sum of subtotals
- ✅ Grand Total Value = Sum of subtotals
- ✅ All fields match calculations
- ✅ Grand Total visually distinct (blue)

### Actual Results
[To be filled during testing]

### Grand Total Verification
| Employee | Gross Wt | Net Wt | Value |
|----------|----------|--------|-------|
| Employee 1 | | | |
| Employee 2 | | | |
| Employee 3 | | | |
| **Sum** | | | |
| **Grand Total** | | | |
| **Match?** | | | |

### Pass/Fail
⬜ Not Tested | ✅ Pass | ❌ Fail

### Comments
[To be filled during testing]

---

## Test Case 11: Number Formatting (OMP-EMP-011)

### Test Information
- **Priority**: MEDIUM
- **Status**: ⬜ Pending
- **Tester**: [To be filled]
- **Test Date**: [To be filled]

### Test Steps
1. Generate Employee Wise report
2. Inspect subtotal values
3. Verify number formatting

### Expected Results
- ✅ Weights use 3 decimal places
- ✅ Value uses 2 decimal places
- ✅ Indian comma separator used
- ✅ money_format_india() applied
- ✅ No NaN or undefined values

### Actual Results
[To be filled during testing]

### Pass/Fail
⬜ Not Tested | ✅ Pass | ❌ Fail

### Comments
[To be filled during testing]

---

## Test Case 12: Browser Compatibility (OMP-EMP-012)

### Test Information
- **Priority**: MEDIUM
- **Status**: ⬜ Pending
- **Tester**: [To be filled]
- **Test Date**: [To be filled]

### Test Steps
1. Test in Chrome browser
2. Test in Firefox browser
3. Test in Edge browser
4. Compare results

### Expected Results
- ✅ Works correctly in Chrome
- ✅ Works correctly in Firefox
- ✅ Works correctly in Edge
- ✅ Styling consistent across browsers
- ✅ No browser-specific errors
- ✅ Calculations identical

### Actual Results

| Browser | Version | Subtotals Display | Styling | Calculations | Errors | Pass/Fail |
|---------|---------|-------------------|---------|--------------|--------|-----------|
| Chrome | | | | | | |
| Firefox | | | | | | |
| Edge | | | | | | |

### Pass/Fail
⬜ Not Tested | ✅ Pass | ❌ Fail

### Comments
[To be filled during testing]

---

## Regression Test 1: Existing Report Types (OMP-EMP-REG-001)

### Test Information
- **Priority**: MEDIUM
- **Status**: ⬜ Pending
- **Tester**: [To be filled]
- **Test Date**: [To be filled]

### Test Steps
1. Test Summary report (report_type = 1)
2. Test Bill Wise report (report_type = 3)
3. Test all filters (branch, metal, date)

### Expected Results
- ✅ Summary report works without subtotals
- ✅ Bill Wise report works without subtotals
- ✅ All filters work correctly
- ✅ No existing functionality broken

### Actual Results
[To be filled during testing]

### Pass/Fail
⬜ Not Tested | ✅ Pass | ❌ Fail

### Comments
[To be filled during testing]

---

## Regression Test 2: DataTables Functionality (OMP-EMP-REG-002)

### Test Information
- **Priority**: MEDIUM
- **Status**: ⬜ Pending
- **Tester**: [To be filled]
- **Test Date**: [To be filled]

### Test Steps
1. Test sorting functionality
2. Test pagination
3. Test search/filter
4. Test column visibility toggle
5. Test export buttons

### Expected Results
- ✅ Sorting works correctly
- ✅ Pagination works correctly
- ✅ Search/filter works correctly
- ✅ Column visibility toggle works
- ✅ Export buttons work

### Actual Results
[To be filled during testing]

### Pass/Fail
⬜ Not Tested | ✅ Pass | ❌ Fail

### Comments
[To be filled during testing]

---

## Issues Found

### Issue 1
- **Test Case**: [Test ID]
- **Severity**: [Critical/High/Medium/Low]
- **Description**: [Issue description]
- **Steps to Reproduce**: [Steps]
- **Expected**: [Expected behavior]
- **Actual**: [Actual behavior]
- **Status**: [Open/Fixed/Closed]

---

## Test Environment Details

- **Application URL**: http://localhost/etail_development_src/admin/
- **Database**: [To be filled]
- **PHP Version**: [To be filled]
- **Browser(s)**: [To be filled]
- **OS**: [To be filled]
- **Screen Resolution**: [To be filled]

---

## Sign-off

### Tester Sign-off
- **Name**: [To be filled]
- **Date**: [To be filled]
- **Signature**: [To be filled]

### Developer Sign-off
- **Name**: [To be filled]
- **Date**: [To be filled]
- **Signature**: [To be filled]

### QA Lead Sign-off
- **Name**: [To be filled]
- **Date**: [To be filled]
- **Signature**: [To be filled]

---

**Document Version**: 1.0  
**Last Updated**: 2026-02-15  
**Status**: Ready for Testing
