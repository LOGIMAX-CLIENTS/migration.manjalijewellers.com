# Unit Test Cases - Dashboard Purchase Branch Filtering

**Feature**: Dashboard Purchase Functions Branch Filtering Implementation  
**Developer**: ruthramoorthi  
**Date**: 2026-02-14  
**Test Type**: Functional & Integration Testing

---

## Test Environment Setup

### Prerequisites
- Active database connection with test data
- Multiple branches configured in the system
- Purchase orders created across different branches
- Rate cut transactions with various dates
- Delayed PO payments and deliveries scheduled

### Test Data Requirements
1. **Branches**: At least 3 branches (e.g., Branch A, Branch B, Branch C)
2. **Purchase Orders**: Minimum 10 POs distributed across branches
3. **Delayed Orders**: At least 3 delayed purchase orders
4. **Delayed Payments**: At least 3 delayed PO payments
5. **Today's Deliveries**: At least 2 POs with delivery date = today
6. **Rate Cut Transactions**: At least 5 rate cut entries across different dates

---

## Test Case 1: Branch Filter - All Branches Selection

### Test ID: TC_DBF_001
### Priority: High
### Module: Dashboard - Purchase Functions

**Objective**: Verify that selecting "All" branches displays data from all branches

**Preconditions**:
- User is logged into the dashboard
- Purchase data exists across multiple branches

**Test Steps**:
1. Navigate to Dashboard → Purchase tab
2. Locate the branch filter dropdown (#id_branch)
3. Select "All" or leave empty (default)
4. Observe all four report sections:
   - Delayed Purchase Orders
   - Delayed PO Payments
   - Today's Delivery PO Payments
   - Rate Cut Profit/Loss

**Expected Results**:
- ✅ All four functions execute successfully
- ✅ Data from all branches is displayed
- ✅ Total counts match database records (no branch filter applied)
- ✅ No JavaScript errors in console
- ✅ AJAX requests include `id_branch: ''` or `id_branch: '0'`

**Test Data**:
```javascript
// Expected AJAX payload
{
  from_date: "2026-02-14",
  to_date: "2026-02-14",
  id_branch: "",  // Empty for all branches
  id_metal: ""
}
```

**SQL Verification**:
```sql
-- Verify total delayed purchase orders (all branches)
SELECT COUNT(*) FROM customerorder c
JOIN customerorderdetails d ON c.id_customerorder = d.id_customerorder
WHERE c.order_type = 1 
AND c.pur_no IS NOT NULL 
AND (c.order_status != 6 AND c.order_status != 7);
```

**Pass Criteria**: All data from all branches displayed correctly

---

## Test Case 2: Branch Filter - Specific Branch Selection

### Test ID: TC_DBF_002
### Priority: High
### Module: Dashboard - Purchase Functions

**Objective**: Verify that selecting a specific branch filters data correctly

**Preconditions**:
- User is logged into the dashboard
- Purchase data exists for the selected branch

**Test Steps**:
1. Navigate to Dashboard → Purchase tab
2. Select a specific branch (e.g., Branch A with id_branch = 1)
3. Wait for all four reports to reload
4. Verify data in each section

**Expected Results**:
- ✅ Only data from selected branch is displayed
- ✅ AJAX requests include correct `id_branch: '1'`
- ✅ No data from other branches appears
- ✅ Counts match database records for that branch only
- ✅ All four functions filter consistently

**Test Data**:
```javascript
// Expected AJAX payload for Branch A (id_branch = 1)
{
  from_date: "2026-02-14",
  to_date: "2026-02-14",
  id_branch: "1",
  id_metal: ""
}
```

**SQL Verification**:
```sql
-- Verify delayed purchase orders for specific branch
SELECT COUNT(*) FROM customerorder c
JOIN customerorderdetails d ON c.id_customerorder = d.id_customerorder
WHERE c.order_type = 1 
AND c.pur_no IS NOT NULL 
AND c.id_branch = 1
AND (c.order_status != 6 AND c.order_status != 7);

-- Verify rate cut data for specific branch
SELECT COUNT(DISTINCT DATE(src.date_add)) 
FROM ret_supplier_rate_cut src
LEFT JOIN ret_purchase_order po ON po.po_id = src.po_id
WHERE src.status = 1 
AND src.ref_no IS NOT NULL
AND po.id_branch = 1;
```

**Pass Criteria**: Only selected branch data displayed

---

## Test Case 3: get_delayed_purchase_orders() - Branch Filtering

### Test ID: TC_DBF_003
### Priority: High
### Module: Dashboard - Delayed Purchase Orders

**Objective**: Verify branch filtering in delayed purchase orders function

**Preconditions**:
- Delayed purchase orders exist in multiple branches
- Some orders are delayed (smith_due_date < current_date)

**Test Steps**:
1. Open browser developer tools → Network tab
2. Select Branch A (id_branch = 1)
3. Monitor AJAX request to `admin_ret_dashboard_api/get_delayed_purchase_orders`
4. Verify request payload
5. Check response data

**Expected Results**:
- ✅ Request includes `id_branch: "1"`
- ✅ Response contains only Branch A orders
- ✅ SQL query includes `AND c.id_branch in (1)`
- ✅ Table displays correct delayed days calculation
- ✅ Karigar analysis shows only Branch A suppliers

**Test Data**:
```javascript
// Sample response structure
{
  status: true,
  response_data: [
    {
      po_number: "PO-001",
      po_date: "10-01-2026",
      expected_delivery_date: "05-02-2026",
      delayed_days: 9,
      karigar_name: "Supplier A",
      order_item: "Gold Ring",
      metal: "GOLD",
      quantity_ordered: 10,
      received_pcs: 5
    }
  ]
}
```

**Pass Criteria**: Only delayed orders from selected branch displayed

---

## Test Case 4: get_delayed_po_payments() - Branch Filtering

### Test ID: TC_DBF_004
### Priority: High
### Module: Dashboard - Delayed PO Payments

**Objective**: Verify branch filtering in delayed PO payments function

**Preconditions**:
- PO payments exist with delivery dates in the past
- Payments are unpaid or partially paid

**Test Steps**:
1. Select Branch B (id_branch = 2)
2. Monitor AJAX request to `admin_ret_dashboard_api/get_delayed_po_payments`
3. Verify delayed payment records displayed
4. Check payment status indicators (signal dots)

**Expected Results**:
- ✅ Request includes `id_branch: "2"`
- ✅ Only Branch B PO payments displayed
- ✅ SQL query includes `AND po.id_branch in (2)` in both UNION queries
- ✅ Signal dots show correct colors based on delay days
- ✅ Total bill amount calculated correctly

**Test Data**:
```javascript
// Sample response
{
  status: true,
  response_data: [
    {
      status: "Unpaid",
      po_number: "PO-B-123",
      po_date: "15-01-2026",
      supplier_name: "Supplier B",
      due_date: "10-02-2026",
      bill_amount: 50000.00,
      payment_status: "Unpaid",
      delay_days: 4,
      po_id: 123
    }
  ]
}
```

**Signal Color Verification**:
- delay_days >= 15: `dot-dark-red` with `blink-fast`
- delay_days >= 8: `dot-red` with `blink`
- delay_days >= 3: `dot-orange`
- delay_days < 3: `dot-yellow`

**Pass Criteria**: Correct delayed payments with proper status indicators

---

## Test Case 5: get_today_delivery_po_payments() - Branch Filtering

### Test ID: TC_DBF_005
### Priority: High
### Module: Dashboard - Today's Delivery PO Payments

**Objective**: Verify branch filtering for today's delivery PO payments

**Preconditions**:
- PO payments with delivery date = current date exist
- Some payments are unpaid

**Test Steps**:
1. Select Branch C (id_branch = 3)
2. Monitor AJAX request to `admin_ret_dashboard_api/get_today_delivery_po_payments`
3. Verify only today's deliveries are shown
4. Check that only Branch C data appears

**Expected Results**:
- ✅ Request includes `id_branch: "3"`
- ✅ Only POs with `po_delivery_date = CURRENT_DATE()` displayed
- ✅ Only Branch C data shown
- ✅ SQL includes `AND po.id_branch in (3)` in both UNION queries
- ✅ Green signal dots displayed (on-time deliveries)
- ✅ Total bill amount calculated correctly

**Test Data**:
```javascript
// Sample response
{
  status: true,
  response_data: [
    {
      status: "Unpaid",
      po_number: "PO-C-456",
      po_date: "01-02-2026",
      supplier_name: "Supplier C",
      due_date: "14-02-2026",
      bill_amount: 75000.00,
      status: "Pending",
      po_id: 456
    }
  ]
}
```

**Pass Criteria**: Only today's deliveries from selected branch displayed

---

## Test Case 6: get_rate_cut_profit_loss() - Branch Filtering

### Test ID: TC_DBF_006
### Priority: High
### Module: Dashboard - Rate Cut Profit/Loss

**Objective**: Verify branch filtering in rate cut profit/loss analysis

**Preconditions**:
- Rate cut transactions exist across multiple branches
- Metal rates are configured in the system

**Test Steps**:
1. Select Branch A (id_branch = 1)
2. Monitor AJAX request to `admin_ret_dashboard_api/get_rate_cut_profit_loss`
3. Verify DataTable displays correct rate cut analysis
4. Check profit/loss calculations

**Expected Results**:
- ✅ Request includes `id_branch: "1"`
- ✅ SQL includes `LEFT JOIN ret_purchase_order po` and `AND po.id_branch in (1)`
- ✅ Only rate cuts linked to Branch A POs displayed
- ✅ Rate deviation calculated correctly
- ✅ Profit percentage displayed with correct formatting
- ✅ Color coding: green for profit, red for loss

**Test Data**:
```javascript
// Sample response
{
  status: true,
  response_data: [
    {
      rate_cut_date: "14-02-2026",
      rate_cut_rate: 6500.00,
      current_bullion_rate: 6800.00,
      rate_deviation: 300.00,
      profit_loss_status: "profit",
      profit_percentage: 4.41
    }
  ]
}
```

**Calculation Verification**:
```
rate_deviation = current_bullion_rate - rate_cut_rate
profit_percentage = (rate_deviation / current_bullion_rate) * 100
```

**Pass Criteria**: Accurate rate cut analysis for selected branch

---

## Test Case 7: Branch + Metal Combined Filtering

### Test ID: TC_DBF_007
### Priority: Medium
### Module: Dashboard - Combined Filters

**Objective**: Verify branch and metal filters work together correctly

**Preconditions**:
- Purchase data exists for multiple metals and branches
- Metal filter (#metal_select_dash) is functional

**Test Steps**:
1. Select Branch A (id_branch = 1)
2. Select Metal = Gold (id_metal = 1)
3. Verify all four functions apply both filters
4. Monitor AJAX requests

**Expected Results**:
- ✅ All requests include both `id_branch: "1"` and `id_metal: "1"`
- ✅ Only Gold items from Branch A displayed
- ✅ SQL queries include both branch and metal conditions
- ✅ Data counts match filtered criteria
- ✅ No Silver or other metal data appears

**Test Data**:
```javascript
// Expected AJAX payload
{
  from_date: "2026-02-14",
  to_date: "2026-02-14",
  id_branch: "1",
  id_metal: "1"  // Gold
}
```

**Pass Criteria**: Both filters applied correctly across all functions

---

## Test Case 8: Branch Switching - Dynamic Update

### Test ID: TC_DBF_008
### Priority: Medium
### Module: Dashboard - Dynamic Filtering

**Objective**: Verify reports update correctly when branch is changed

**Preconditions**:
- Dashboard is loaded with initial branch selection

**Test Steps**:
1. Load dashboard with Branch A selected
2. Wait for all reports to load
3. Switch to Branch B
4. Observe all four report sections update
5. Switch back to "All"
6. Verify all data reappears

**Expected Results**:
- ✅ All four functions re-execute on branch change
- ✅ Data updates without page reload
- ✅ Loading indicators shown during AJAX calls
- ✅ No stale data from previous branch displayed
- ✅ Smooth transition between branch selections

**Pass Criteria**: Reports update dynamically on branch change

---

## Test Case 9: Fallback Logic - Missing Branch Parameter

### Test ID: TC_DBF_009
### Priority: Low
### Module: Dashboard - Error Handling

**Objective**: Verify fallback logic when id_branch parameter is not provided

**Preconditions**:
- Functions can be called programmatically

**Test Steps**:
1. Open browser console
2. Call function without id_branch parameter:
   ```javascript
   get_delayed_purchase_orders('2026-02-14', '2026-02-14');
   ```
3. Verify function reads from #id_branch
4. Check AJAX request includes branch value

**Expected Results**:
- ✅ Function executes without error
- ✅ Fallback logic: `if (!id_branch) id_branch = $('#id_branch').val() || '';`
- ✅ Correct branch value retrieved from DOM
- ✅ AJAX request includes proper id_branch

**Pass Criteria**: Fallback logic works correctly

---

## Test Case 10: Empty/Zero Branch Handling

### Test ID: TC_DBF_010
### Priority: Medium
### Module: Dashboard - Edge Cases

**Objective**: Verify system handles empty or zero branch values correctly

**Preconditions**:
- Dashboard is accessible

**Test Steps**:
1. Set #id_branch value to empty string
2. Trigger report refresh
3. Set #id_branch value to '0'
4. Trigger report refresh again

**Expected Results**:
- ✅ Empty string: Shows all branches
- ✅ '0' value: Shows all branches
- ✅ SQL condition: `($branch!='' && $branch !='0')` evaluates to false
- ✅ No branch filter applied in SQL
- ✅ All data displayed correctly

**Pass Criteria**: Empty/zero values treated as "all branches"

---

## Test Case 11: Date Range + Branch Filtering

### Test ID: TC_DBF_011
### Priority: Medium
### Module: Dashboard - Combined Filters

**Objective**: Verify date range and branch filters work together

**Preconditions**:
- Purchase data exists across multiple dates and branches

**Test Steps**:
1. Select Branch A
2. Set date range: 01-02-2026 to 14-02-2026
3. Verify all functions apply both filters
4. Check data matches criteria

**Expected Results**:
- ✅ Data filtered by both branch and date range
- ✅ SQL includes both conditions
- ✅ Only Branch A data within date range displayed
- ✅ Counts accurate for combined filters

**Pass Criteria**: Date range and branch filters work together

---

## Test Case 12: Performance - Large Dataset

### Test ID: TC_DBF_012
### Priority: Low
### Module: Dashboard - Performance

**Objective**: Verify performance with large datasets

**Preconditions**:
- Database contains 1000+ purchase orders across branches

**Test Steps**:
1. Select "All" branches
2. Measure AJAX response time
3. Select specific branch
4. Measure AJAX response time again
5. Compare performance

**Expected Results**:
- ✅ "All branches" query completes in < 3 seconds
- ✅ Specific branch query completes in < 2 seconds
- ✅ No timeout errors
- ✅ UI remains responsive
- ✅ DataTables render smoothly

**Performance Benchmarks**:
- AJAX response: < 2000ms
- DataTable rendering: < 500ms
- Total page update: < 3000ms

**Pass Criteria**: Acceptable performance with large datasets

---

## Test Case 13: SQL Injection Prevention

### Test ID: TC_DBF_013
### Priority: High
### Module: Dashboard - Security

**Objective**: Verify SQL injection prevention in branch filtering

**Preconditions**:
- Security testing environment

**Test Steps**:
1. Attempt to inject SQL via branch parameter:
   ```javascript
   // Malicious payload
   id_branch = "1' OR '1'='1"
   ```
2. Monitor database queries
3. Verify injection is prevented

**Expected Results**:
- ✅ Parameterized queries used
- ✅ Input sanitization applied
- ✅ No SQL injection successful
- ✅ Error handling prevents data exposure
- ✅ Logs show attempted injection

**Pass Criteria**: SQL injection attempts blocked

---

## Test Case 14: Cross-Browser Compatibility

### Test ID: TC_DBF_014
### Priority: Medium
### Module: Dashboard - Compatibility

**Objective**: Verify branch filtering works across different browsers

**Preconditions**:
- Access to multiple browsers

**Test Steps**:
1. Test in Chrome (latest version)
2. Test in Firefox (latest version)
3. Test in Edge (latest version)
4. Test in Safari (if available)

**Expected Results**:
- ✅ All functions work in all browsers
- ✅ AJAX requests execute correctly
- ✅ DataTables render properly
- ✅ No JavaScript errors
- ✅ Consistent UI/UX across browsers

**Pass Criteria**: Consistent functionality across browsers

---

## Test Case 15: Concurrent User Testing

### Test ID: TC_DBF_015
### Priority: Low
### Module: Dashboard - Multi-User

**Objective**: Verify multiple users can filter by different branches simultaneously

**Preconditions**:
- Multiple user sessions active

**Test Steps**:
1. User A selects Branch A
2. User B selects Branch B
3. User C selects "All"
4. Verify each user sees correct data

**Expected Results**:
- ✅ Each user's session is independent
- ✅ No data cross-contamination
- ✅ Correct branch data for each user
- ✅ No session conflicts

**Pass Criteria**: Independent filtering per user session

---

## Regression Test Cases

### RT_001: Existing Metal Filter Still Works
**Objective**: Verify metal filter functionality not broken  
**Steps**: Select metal filter without branch filter  
**Expected**: Metal filtering works as before

### RT_002: Date Range Filter Still Works
**Objective**: Verify date range functionality not broken  
**Steps**: Change date range without branch filter  
**Expected**: Date filtering works as before

### RT_003: Other Dashboard Tabs Unaffected
**Objective**: Verify other dashboard sections work normally  
**Steps**: Navigate to Sales, Stock, Order Management tabs  
**Expected**: All tabs function correctly

---

## Test Execution Summary Template

| Test ID | Test Name | Status | Executed By | Date | Notes |
|---------|-----------|--------|-------------|------|-------|
| TC_DBF_001 | All Branches Selection | ⬜ Pass / ❌ Fail | | | |
| TC_DBF_002 | Specific Branch Selection | ⬜ Pass / ❌ Fail | | | |
| TC_DBF_003 | Delayed Purchase Orders | ⬜ Pass / ❌ Fail | | | |
| TC_DBF_004 | Delayed PO Payments | ⬜ Pass / ❌ Fail | | | |
| TC_DBF_005 | Today's Delivery Payments | ⬜ Pass / ❌ Fail | | | |
| TC_DBF_006 | Rate Cut Profit/Loss | ⬜ Pass / ❌ Fail | | | |
| TC_DBF_007 | Branch + Metal Filtering | ⬜ Pass / ❌ Fail | | | |
| TC_DBF_008 | Dynamic Branch Switching | ⬜ Pass / ❌ Fail | | | |
| TC_DBF_009 | Fallback Logic | ⬜ Pass / ❌ Fail | | | |
| TC_DBF_010 | Empty/Zero Handling | ⬜ Pass / ❌ Fail | | | |
| TC_DBF_011 | Date Range + Branch | ⬜ Pass / ❌ Fail | | | |
| TC_DBF_012 | Performance Testing | ⬜ Pass / ❌ Fail | | | |
| TC_DBF_013 | SQL Injection Prevention | ⬜ Pass / ❌ Fail | | | |
| TC_DBF_014 | Cross-Browser Testing | ⬜ Pass / ❌ Fail | | | |
| TC_DBF_015 | Concurrent Users | ⬜ Pass / ❌ Fail | | | |

---

## Bug Report Template

**Bug ID**: BUG_DBF_XXX  
**Severity**: Critical / High / Medium / Low  
**Test Case**: TC_DBF_XXX  
**Description**: [Detailed description]  
**Steps to Reproduce**:  
1. Step 1
2. Step 2
3. Step 3

**Expected Result**: [What should happen]  
**Actual Result**: [What actually happened]  
**Screenshots**: [Attach if applicable]  
**Browser/Environment**: [Browser version, OS]  
**Assigned To**: [Developer name]  
**Status**: Open / In Progress / Fixed / Closed

---

## Test Sign-Off

**Tested By**: ___________________  
**Date**: ___________________  
**Overall Result**: ⬜ Pass / ❌ Fail  
**Comments**: ___________________

---

**End of Unit Test Cases**
