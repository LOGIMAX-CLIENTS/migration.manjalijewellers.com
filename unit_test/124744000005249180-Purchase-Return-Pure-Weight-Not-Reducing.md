# Unit Tests for Purchase Return Pure Weight Bugfix

**Task ID**: 124744000005249180  
**Task Name**: Purchase Return Pure Weight Not Reducing in CR & DR, Pure Rate Conversion & Smith Metal Issue Modules  
**Task Type**: Bugfixing  
**Developer**: ruthramoorthi  
**Date**: 2026-02-12

---

## Overview

This document contains comprehensive unit tests for the bugfix that ensures Purchase Return pure weight is properly deducted from Purchase Order (PO) balance calculations across multiple modules.

### Files Modified
1. `admin/application/models/ret_purchase_order_model.php`
2. `admin/assets/js/ret_order.js`
3. `admin/application/models/ret_reports_model.php`
4. `admin/assets/js/ret_billing.js`
5. `admin/application/views/billing/ledger_transfer_form.php`

---

## Test Suite 1: Purchase Order Balance Calculations (ret_purchase_order_model.php)

### Test 1.1: get_approval_po_bills() - Purchase Return Weight Deduction

**Test Case**: Verify that purchase return pure weight is correctly deducted from PO balance

**Test Data**:
```php
$post = [
    'karigar' => 123  // Sample karigar ID
];

// Mock Database State:
// PO: po_id=1, tot_purchase_wt=1000g
// Metal Issue: issue_metal_pur_wt=200g
// Rate Cut: weight=50g
// CR/DR: dr_wt=30g, cr_wt=10g
// Purchase Return: pur_ret_pur_wt=100g
```

**Expected Result**:
```php
balance_wt = 1000 - 200 - 50 - 30 + 10 - 100 = 630g
```

**Test Steps**:
1. Create a test PO with tot_purchase_wt = 1000g
2. Create metal issue record with 200g
3. Create rate cut record with 50g
4. Create CR/DR records (30g debit, 10g credit)
5. Create purchase return with 100g pure weight
6. Call `get_approval_po_bills($post)`
7. Assert balance_wt equals 630g

**SQL Validation**:
```sql
-- Verify the subquery correctly sums purchase returns
SELECT IFNULL(sum(rtn_itm.pur_ret_pur_wt), 0) as pur_ret_pur_wt,
       rtn_itm.pur_ret_po_item_id 
FROM ret_purchase_return_items rtn_itm
LEFT JOIN ret_purchase_return rtn ON rtn.pur_return_id = rtn_itm.pur_ret_id
WHERE rtn.bill_status = 1 
GROUP BY rtn.pur_return_id;
```

---

### Test 1.2: get_approval_po_bills() - Only Active Returns

**Test Case**: Verify that only active purchase returns (bill_status=1) are included in balance calculation

**Test Data**:
```php
$post = ['karigar' => 123];

// Mock Database:
// PO: po_id=1, tot_purchase_wt=1000g
// Purchase Return 1: pur_ret_pur_wt=100g, bill_status=1 (Active)
// Purchase Return 2: pur_ret_pur_wt=50g, bill_status=0 (Cancelled)
```

**Expected Result**:
```php
// Only active return (100g) should be deducted
balance_wt = 1000 - 100 = 900g
// Cancelled return (50g) should NOT be deducted
```

**Test Steps**:
1. Create PO with 1000g
2. Create active purchase return with 100g
3. Create cancelled purchase return with 50g
4. Call `get_approval_po_bills($post)`
5. Assert balance_wt equals 900g (not 850g)

---

### Test 1.3: get_approval_po_bills() - CR/DR Calculation Fix

**Test Case**: Verify CR/DR weight calculation uses proper addition/subtraction

**Test Data**:
```php
$post = ['karigar' => 123];

// Mock Database:
// PO: tot_purchase_wt=1000g
// CR/DR: dr_wt=100g (Debit - should subtract), cr_wt=50g (Credit - should add)
```

**Expected Result**:
```php
// OLD (INCORRECT): balance_wt = 1000 - (100 - 50) = 950g
// NEW (CORRECT): balance_wt = 1000 - 100 + 50 = 950g
// Both give same result, but logic is correct now
```

**Test Steps**:
1. Create PO with 1000g
2. Create CR note with 50g weight (should ADD to balance)
3. Create DR note with 100g weight (should SUBTRACT from balance)
4. Call `get_approval_po_bills($post)`
5. Assert balance_wt = 950g
6. Verify formula uses `- dr_wt + cr_wt` not `- (dr_wt - cr_wt)`

---

### Test 1.4: get_po_details_by_ref_no() - Balance Calculation

**Test Case**: Verify purchase return weight is deducted when fetching PO by reference number

**Test Data**:
```php
$post = [
    'po_num' => 1,
    'karigar' => 123
];

// Mock Database:
// PO: po_id=1, tot_purchase_wt=2000g
// Rate Cut: weight=150g
// Metal Issue: issued_wt=300g
// CR/DR: dr=50g, cr=20g
// Purchase Return: pur_ret_pur_wt=200g
```

**Expected Result**:
```php
balance = 2000 - 50 + 20 - 150 - 300 - 200 = 1320g
```

**Test Steps**:
1. Create PO with reference and 2000g weight
2. Create all deduction records
3. Create purchase return with 200g
4. Call `get_po_details_by_ref_no($post)`
5. Assert balance equals 1320g

---

### Test 1.5: get_po_ratecut_balance_details() - Balance Amount Calculation

**Test Case**: Verify balance amount calculation with CR/DR adjustments

**Test Data**:
```php
$po_id = 1;

// Mock Database:
// PO: tot_purchase_amt=50000
// CR/DR: dr_amt=2000, cr_amt=500
```

**Expected Result**:
```php
// OLD (INCORRECT): balance_amt = 50000 - (2000 - 500) = 48500
// NEW (CORRECT): balance_amt = 50000 - (2000 + 500) = 47500
```

**Test Steps**:
1. Create PO with amount 50000
2. Create DR note with amount 2000
3. Create CR note with amount 500
4. Call `get_po_ratecut_balance_details(1)`
5. Assert balance_amt equals 47500

---

### Test 1.6: Edge Case - No Purchase Returns

**Test Case**: Verify balance calculation works correctly when no purchase returns exist

**Test Data**:
```php
$post = ['karigar' => 123];

// Mock Database:
// PO: tot_purchase_wt=1000g
// No purchase return records
```

**Expected Result**:
```php
balance_wt = 1000g
// IFNULL should return 0 for pur_ret_pur_wt
```

**Test Steps**:
1. Create PO with 1000g
2. Ensure no purchase return records exist
3. Call `get_approval_po_bills($post)`
4. Assert balance_wt equals 1000g
5. Verify no SQL errors occur

---

### Test 1.7: Edge Case - Multiple Purchase Returns for Same PO

**Test Case**: Verify multiple purchase returns are correctly summed

**Test Data**:
```php
$post = ['karigar' => 123];

// Mock Database:
// PO: po_id=1, tot_purchase_wt=1000g
// Purchase Return 1: pur_ret_pur_wt=100g, bill_status=1
// Purchase Return 2: pur_ret_pur_wt=150g, bill_status=1
// Purchase Return 3: pur_ret_pur_wt=50g, bill_status=1
```

**Expected Result**:
```php
total_returns = 100 + 150 + 50 = 300g
balance_wt = 1000 - 300 = 700g
```

**Test Steps**:
1. Create PO with 1000g
2. Create 3 separate purchase return records
3. Call `get_approval_po_bills($post)`
4. Assert balance_wt equals 700g

---

### Test 1.8: Boundary Case - Purchase Return Equals Total Weight

**Test Case**: Verify balance calculation when entire PO is returned

**Test Data**:
```php
$post = ['karigar' => 123];

// Mock Database:
// PO: tot_purchase_wt=1000g
// Purchase Return: pur_ret_pur_wt=1000g
```

**Expected Result**:
```php
balance_wt = 1000 - 1000 = 0g
```

**Test Steps**:
1. Create PO with 1000g
2. Create purchase return with 1000g (full return)
3. Call `get_approval_po_bills($post)`
4. Assert balance_wt equals 0g
5. Verify PO appears in results with 0 balance (or filtered by HAVING clause)

---

## Test Suite 2: Order Processing JavaScript (ret_order.js)

### Test 2.1: Order Placement - Overlay Display Timing

**Test Case**: Verify overlay displays at correct time during order placement

**Test Data**:
```javascript
// Mock order data
var data = [
    { totalitems: 5, item_id: 1 },
    { totalitems: 10, item_id: 2 }
];
var req_status = 1;
```

**Expected Result**:
- Overlay should display AFTER validation passes
- Overlay should NOT display during validation phase

**Test Steps**:
1. Mock order cart data
2. Call `order_place(req_status, data)`
3. Verify overlay CSS display is set to "block" AFTER validation
4. Verify overlay is NOT displayed during validation checks

---

### Test 2.2: Order Status Change - Validation Removed

**Test Case**: Verify redundant piece validation is removed from order status change

**Test Data**:
```javascript
// Order with valid data
var order_data = [
    { totalitems: 5 },
    { totalitems: 10 }
];
```

**Expected Result**:
- Order should submit without piece validation
- No "Please Enter Valid Pcs" error should appear

**Test Steps**:
1. Select order status radio button
2. Trigger change event
3. Verify no piece validation occurs
4. Verify order submission proceeds normally

---

## Test Suite 3: Reports Model (ret_reports_model.php)

### Test 3.1: Receipt Payment Filter - Exclude Ledger Transfers

**Test Case**: Verify ledger transfers (issue_type=6) are excluded from receipt reports

**Test Data**:
```php
// Mock Database:
// Receipt 1: issue_type=1, payment_status=1, bill_status=1 (Should be included)
// Receipt 2: issue_type=6, payment_status=1, bill_status=1 (Should be excluded - Ledger Transfer)
// Receipt 3: issue_type=2, payment_status=1, bill_status=1 (Should be included)
```

**Expected Result**:
- Only receipts with issue_type != 6 should be returned
- Ledger transfers should be filtered out

**Test Steps**:
1. Create test receipts with different issue_types
2. Call the receipt report function
3. Assert ledger transfer (issue_type=6) is NOT in results
4. Assert other receipt types ARE in results

**SQL Validation**:
```sql
-- Verify filter is applied
SELECT * FROM ret_issue_receipt r
WHERE r.bill_status=1 
  AND r.issue_type != 6;
```

---

### Test 3.2: Group Wise Billing - Payment Status Filter

**Test Case**: Verify only active payments (payment_status=1) are included in group-wise billing

**Test Data**:
```php
// Mock Database:
// Payment 1: payment_status=1, amount=1000 (Active - should include)
// Payment 2: payment_status=0, amount=500 (Inactive - should exclude)
// Payment 3: payment_status=1, amount=2000 (Active - should include)
```

**Expected Result**:
```php
total_amount = 1000 + 2000 = 3000
// Payment 2 (500) should NOT be included
```

**Test Steps**:
1. Create test payment records with different statuses
2. Call `getGroupWiseBilling($post)`
3. Assert total amount equals 3000 (not 3500)
4. Verify payment_status=1 filter is applied

---

### Test 3.3: Payment Mode Details - Active Status Filter

**Test Case**: Verify only active payment mode details (is_active=1) are included

**Test Data**:
```php
// Mock Database:
// Payment Mode Detail 1: is_active=1, amount=500 (Should include)
// Payment Mode Detail 2: is_active=0, amount=300 (Should exclude)
```

**Expected Result**:
```php
total_amount = 500
// Inactive payment mode (300) should NOT be included
```

**Test Steps**:
1. Create payment mode details with different active statuses
2. Call the payment mode query
3. Assert only active records are returned
4. Verify is_active=1 filter is applied

---

## Test Suite 4: Ledger Transfer UI (ret_billing.js & ledger_transfer_form.php)

### Test 4.1: Select2 Initialization

**Test Case**: Verify Select2 is properly initialized for ledger dropdowns

**Test Data**:
```javascript
// Mock ledger data
var ledgers = [
    { id_ledger: 1, ledger_name: "Bank A" },
    { id_ledger: 2, ledger_name: "Bank B" }
];
```

**Expected Result**:
- Both #from_ledger and #to_ledger should have Select2 initialized
- Placeholder text should be "Select Ledger"
- allowClear should be enabled

**Test Steps**:
1. Load ledger transfer form
2. Call `ledger_transfer()`
3. Verify Select2 is initialized on #from_ledger
4. Verify Select2 is initialized on #to_ledger
5. Check placeholder and allowClear options

---

### Test 4.2: Ledger Dropdown Population with Trigger

**Test Case**: Verify ledger dropdowns are populated and change event is triggered

**Test Data**:
```javascript
var all_ledgers = [
    { id_ledger: 1, ledger_name: "Ledger 1" },
    { id_ledger: 2, ledger_name: "Ledger 2" }
];
```

**Expected Result**:
- Dropdowns should be populated with options
- `.trigger('change')` should be called after HTML update
- Select2 should refresh properly

**Test Steps**:
1. Mock AJAX response with ledger data
2. Populate #from_ledger dropdown
3. Verify `.trigger('change')` is called
4. Verify Select2 updates correctly
5. Repeat for #to_ledger

---

### Test 4.3: From Ledger Change - To Ledger Filtering

**Test Case**: Verify selecting "From Ledger" filters "To Ledger" options

**Test Data**:
```javascript
var all_ledgers = [
    { id_ledger: 1, ledger_name: "Ledger 1" },
    { id_ledger: 2, ledger_name: "Ledger 2" },
    { id_ledger: 3, ledger_name: "Ledger 3" }
];
var selected_from_ledger = 1;
```

**Expected Result**:
- To Ledger dropdown should exclude selected From Ledger
- To Ledger should contain only Ledger 2 and Ledger 3

**Test Steps**:
1. Select Ledger 1 in From Ledger dropdown
2. Trigger change event
3. Verify To Ledger dropdown excludes Ledger 1
4. Verify `.trigger('change')` is called on To Ledger

---

### Test 4.4: Input Group Removal

**Test Case**: Verify input-group wrapper is removed from ledger selects

**Test Data**:
```html
<!-- Old structure (should NOT exist) -->
<div class="input-group">
    <span class="input-group-addon"><i class="fa fa-bank"></i></span>
    <select id="from_ledger"></select>
</div>
```

**Expected Result**:
- Select elements should NOT be wrapped in input-group div
- Icon addons should be removed
- Select should be direct child of col-sm-8

**Test Steps**:
1. Load ledger_transfer_form.php
2. Inspect #from_ledger parent elements
3. Assert no .input-group wrapper exists
4. Assert no .input-group-addon exists
5. Repeat for #to_ledger

---

## Test Suite 5: Integration Tests

### Test 5.1: End-to-End Purchase Return Flow

**Test Case**: Complete purchase return flow and verify balance updates

**Test Steps**:
1. Create Purchase Order with 1000g weight
2. Process purchase return for 200g
3. Navigate to CR/DR module
4. Verify available balance shows 800g
5. Navigate to Smith Metal Issue
6. Verify available balance shows 800g
7. Navigate to Pure Rate Conversion
8. Verify available balance shows 800g

**Expected Result**: All modules show consistent 800g balance

---

### Test 5.2: Ledger Transfer Complete Flow

**Test Case**: Complete ledger transfer from selection to submission

**Test Steps**:
1. Open ledger transfer form
2. Verify Select2 dropdowns are initialized
3. Select "From Ledger"
4. Verify "To Ledger" excludes selected ledger
5. Enter transfer amount
6. Submit form
7. Verify transfer is recorded correctly

**Expected Result**: Transfer completes successfully with proper ledger filtering

---

## Test Execution Checklist

### Pre-requisites
- [ ] Database with test data populated
- [ ] PHPUnit configured for PHP tests
- [ ] Jest/Mocha configured for JavaScript tests
- [ ] Test database isolated from production

### PHP Tests (PHPUnit)
```bash
# Run all model tests
phpunit tests/models/Ret_purchase_order_model_test.php
phpunit tests/models/Ret_reports_model_test.php
```

### JavaScript Tests (Jest)
```bash
# Run all JS tests
npm test -- ret_order.test.js
npm test -- ret_billing.test.js
```

### Manual Testing
- [ ] Test 5.1: End-to-End Purchase Return Flow
- [ ] Test 5.2: Ledger Transfer Complete Flow
- [ ] Verify UI changes in ledger transfer form
- [ ] Cross-browser testing for Select2 functionality

---

## Test Coverage Summary

| Module | Test Cases | Coverage |
|--------|-----------|----------|
| ret_purchase_order_model.php | 8 | Balance calculations, CR/DR logic, edge cases |
| ret_order.js | 2 | Overlay timing, validation removal |
| ret_reports_model.php | 3 | Filters, payment status, active records |
| ret_billing.js | 4 | Select2 init, dropdown population, filtering |
| Integration | 2 | End-to-end flows |
| **Total** | **19** | **Comprehensive** |

---

## Notes for QA Team

1. **Critical Tests**: Tests 1.1-1.5 are critical as they verify the core bugfix
2. **Regression Tests**: Tests 1.6-1.8 ensure no regressions with edge cases
3. **UI Tests**: Tests 4.1-4.4 verify UI improvements don't break functionality
4. **Data Integrity**: All tests should use isolated test database
5. **Performance**: Monitor query performance with purchase return subquery

---

## Test Data Setup Scripts

### SQL Script for Test Data
```sql
-- Create test PO
INSERT INTO ret_purchase_order (po_id, po_ref_no, tot_purchase_wt, tot_purchase_amt, bill_status, is_approved, isratefixed, po_karigar_id)
VALUES (999, 'TEST-PO-001', 1000.00, 50000.00, 1, 1, 0, 123);

-- Create test PO item
INSERT INTO ret_purchase_order_items (po_item_id, po_item_po_id, po_item_cat_id, gross_wt, net_wt)
VALUES (9999, 999, 1, 1000.00, 950.00);

-- Create test purchase return
INSERT INTO ret_purchase_return (pur_return_id, bill_status)
VALUES (888, 1);

-- Create test purchase return item
INSERT INTO ret_purchase_return_items (pur_ret_id, pur_ret_po_item_id, pur_ret_pur_wt)
VALUES (888, 9999, 200.00);

-- Create test CR/DR notes
INSERT INTO ret_crdr_note (po_id, transtype, weight, transamount, crdr_status)
VALUES (999, 1, 10.00, 500.00, 1), -- Credit
       (999, 2, 30.00, 2000.00, 1); -- Debit
```

---

**End of Unit Test Document**
