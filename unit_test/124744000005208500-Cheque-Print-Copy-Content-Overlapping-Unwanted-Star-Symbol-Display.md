# Unit Test: Cheque Print Copy – Content Overlapping & Unwanted Star (*) Symbol Display

**Task ID**: 124744000005208500  
**Task Name**: Cheque Print Copy – Content Overlapping & Unwanted Star (*) Symbol Display  
**Branch**: `bugfixing/124744000005208500-Cheque-Print-Copy-–-Content-Overlapping-&-Unwanted-Star-(-)-Symbol-Display`  
**Date**: 14/02/2026  
**Developer**: RUTHRAMOOTHI

---

## 📋 Test Summary

This unit test validates the fix for cheque print functionality where account numbers were not displaying correctly. The issue was that the account number field (`acc_number`) was being fetched from the wrong table (`bank` table instead of `ret_karigar` table), causing incorrect or missing account numbers to appear on printed cheques.

**Changes Made**:
- **Model** (`ret_purchase_order_model.php`): Modified `get_po_paid_payment()` function to fetch account number from `ret_karigar` table
- **SQL Query**: Added JOIN with `ret_po_payment` and `ret_karigar` tables to retrieve correct supplier/karigar account number

---

## 🔍 Root Cause Analysis

### Issue Description
When printing cheques for supplier/karigar payments, the account number displayed was either:
1. **Incorrect** - Showing bank's account number instead of supplier's account number
2. **Empty/Missing** - Showing blank or asterisk (*) symbols
3. **Overlapping** - Content alignment issues due to unexpected data

### Root Cause
The SQL query in `get_po_paid_payment()` function was fetching `acc_number` from the `bank` table (`b.acc_number`), which contains the company's bank account details, not the supplier/karigar's account number.

### Solution
- Changed `b.acc_number` to `kar.acc_number`
- Added `LEFT JOIN ret_po_payment pay ON p.pay_id = pay.pay_id` to link payment details
- Added `LEFT JOIN ret_karigar kar ON kar.id_karigar = pay.pay_sup_id` to fetch karigar/supplier account number

---

## 🧪 Test Cases

### 1. Model Tests - `ret_purchase_order_model.php`

#### Test Case 1.1: Correct Account Number Retrieved from Karigar Table
**Function**: `get_po_paid_payment($id)`  
**Lines Modified**: 2625, 2628, 2630

**Test Steps**:
1. Create a test payment with known karigar ID
2. Ensure karigar has `acc_number` populated in `ret_karigar` table
3. Call `get_po_paid_payment($payment_id)`
4. Verify the returned `acc_number` matches karigar's account number

**Test Data**:
```sql
-- Setup test data
INSERT INTO ret_karigar (id_karigar, firstname, acc_number) 
VALUES (999, 'Test Supplier', '1234567890');

INSERT INTO ret_po_payment (pay_id, pay_sup_id) 
VALUES (888, 999);

INSERT INTO ret_po_payment_detail (id_pay_details, pay_id, payment_amount, pay_mode) 
VALUES (777, 888, 50000, 'CHQ');
```

**Expected Result**:
- `acc_number` should return `'1234567890'`
- Account number should match the value from `ret_karigar` table, not `bank` table

**SQL Verification**:
```sql
SELECT kar.acc_number
FROM ret_po_payment_detail p
LEFT JOIN ret_po_payment pay ON p.pay_id = pay.pay_id
LEFT JOIN ret_karigar kar ON kar.id_karigar = pay.pay_sup_id
WHERE p.pay_id = 888;
```

**Pass Criteria**: ✅ Account number matches karigar's account number from `ret_karigar` table

---

#### Test Case 1.2: JOIN with ret_po_payment Table Works Correctly
**Function**: `get_po_paid_payment($id)`

**Test Steps**:
1. Verify `LEFT JOIN ret_po_payment pay ON p.pay_id = pay.pay_id` is present
2. Check that `pay.pay_sup_id` is accessible after the join
3. Ensure no SQL errors occur

**Expected Result**:
- JOIN executes successfully
- `pay_sup_id` is available for subsequent karigar join
- No duplicate rows returned

**Pass Criteria**: ✅ JOIN works correctly without errors

---

#### Test Case 1.3: JOIN with ret_karigar Table Works Correctly
**Function**: `get_po_paid_payment($id)`

**Test Steps**:
1. Verify `LEFT JOIN ret_karigar kar ON kar.id_karigar = pay.pay_sup_id` is present
2. Check that `kar.acc_number` is accessible
3. Test with valid and invalid `pay_sup_id` values

**Expected Result**:
- JOIN executes successfully
- `kar.acc_number` is retrieved correctly
- LEFT JOIN handles missing karigar records gracefully (returns NULL)

**Pass Criteria**: ✅ Karigar JOIN works correctly

---

#### Test Case 1.4: Handle NULL Account Numbers Gracefully
**Function**: `get_po_paid_payment($id)`

**Test Steps**:
1. Create karigar record with NULL or empty `acc_number`
2. Create payment for this karigar
3. Call `get_po_paid_payment($payment_id)`
4. Verify result handles NULL gracefully

**Test Data**:
```sql
INSERT INTO ret_karigar (id_karigar, firstname, acc_number) 
VALUES (998, 'Supplier Without Account', NULL);
```

**Expected Result**:
- Function returns successfully
- `acc_number` is NULL or empty string
- No SQL errors or PHP warnings
- Cheque print view handles NULL gracefully

**Pass Criteria**: ✅ NULL account numbers handled without errors

---

#### Test Case 1.5: Multiple Payment Details for Same Payment
**Function**: `get_po_paid_payment($id)`

**Test Steps**:
1. Create payment with multiple payment details (e.g., partial cash, partial cheque)
2. Call `get_po_paid_payment($payment_id)`
3. Verify all payment details returned with correct account number

**Test Data**:
```sql
INSERT INTO ret_po_payment_detail (id_pay_details, pay_id, payment_amount, pay_mode) 
VALUES 
(776, 888, 30000, 'CASH'),
(777, 888, 20000, 'CHQ');
```

**Expected Result**:
- All payment details returned
- Each row has same `acc_number` (from karigar)
- No duplicate or missing records

**Pass Criteria**: ✅ Multiple payment details handled correctly

---

### 2. SQL Query Tests

#### Test Case 2.1: Query Returns Correct Columns
**Function**: `get_po_paid_payment($id)`

**Test Steps**:
1. Execute the modified SQL query
2. Verify all expected columns are present
3. Check column aliases are correct

**Expected Columns**:
- `payment_amount`
- `payment_mode` (aliased from `pay_mode`)
- `ref_no`
- `transfer_type` (aliased from `type`)
- `ref_date`
- `cheque_date`
- `cheque_no`
- `id_bank`
- `payee_name`
- `bank_branch`
- `acc_number` (from `kar.acc_number`, not `b.acc_number`)

**Pass Criteria**: ✅ All columns present and correctly aliased

---

#### Test Case 2.2: Query Performance - No Cartesian Product
**Function**: `get_po_paid_payment($id)`

**Test Steps**:
1. Run `EXPLAIN` on the modified query
2. Check for proper JOIN conditions
3. Verify no Cartesian products occur

**SQL Verification**:
```sql
EXPLAIN SELECT p.payment_amount, p.pay_mode as payment_mode, p.ref_no,
IFNULL(p.type,'') as transfer_type, date_format(p.ref_date,'%d-%m-%Y') as ref_date,
date_format(p.cheque_date,'%d-%m-%Y') as cheque_date, p.cheque_no, p.id_bank, 
p.payee_name, p.bank_branch, kar.acc_number
FROM ret_po_payment_detail p
LEFT JOIN ret_po_payment pay ON p.pay_id = pay.pay_id
LEFT JOIN bank b ON b.id_bank = p.id_bank
LEFT JOIN ret_karigar kar ON kar.id_karigar = pay.pay_sup_id
WHERE p.pay_id = 888;
```

**Expected Result**:
- Proper index usage
- No full table scans on large tables
- JOIN conditions are optimized

**Pass Criteria**: ✅ Query executes efficiently

---

#### Test Case 2.3: Backward Compatibility - Bank Table Still Joined
**Function**: `get_po_paid_payment($id)`

**Test Steps**:
1. Verify `LEFT JOIN bank b ON b.id_bank = p.id_bank` is still present
2. Check if any other columns from bank table are used
3. Ensure removing bank join doesn't break anything

**Expected Result**:
- Bank table JOIN is still present (for potential future use or other columns)
- No errors if bank record is missing
- Query works with or without bank data

**Pass Criteria**: ✅ Bank JOIN maintained for compatibility

---

### 3. Integration Tests - Cheque Print Flow

#### Test Case 3.1: End-to-End Cheque Print with Correct Account Number
**Integration Flow**

**Test Steps**:
1. Login to system
2. Navigate to Purchase → Supplier Payment
3. Create a payment with cheque mode
4. Enter supplier/karigar with known account number
5. Save payment
6. Print cheque
7. Verify account number on printed cheque

**Expected Result**:
- Cheque displays supplier's account number (from `ret_karigar`)
- Account number is correctly positioned
- No overlapping content
- No asterisk (*) symbols

**Pass Criteria**: ✅ Correct account number displayed on cheque

---

#### Test Case 3.2: Cheque Print with Multiple Cheques
**Integration Flow**

**Test Steps**:
1. Create payment with multiple cheque entries
2. Print all cheques
3. Verify each cheque shows correct account number

**Expected Result**:
- Each cheque prints on separate page
- All cheques show same supplier account number
- No content overlapping between pages

**Pass Criteria**: ✅ Multiple cheques print correctly

---

#### Test Case 3.3: Cheque Print with Missing Account Number
**Integration Flow**

**Test Steps**:
1. Create karigar without account number
2. Create payment for this karigar
3. Print cheque
4. Verify graceful handling

**Expected Result**:
- Cheque prints successfully
- Account number field is empty (no asterisks or errors)
- Other fields display correctly
- No PHP errors or warnings

**Pass Criteria**: ✅ Missing account number handled gracefully

---

### 4. View Tests - `cheque.php`

#### Test Case 4.1: Account Number Display in Cheque Template
**File**: `admin/application/views/ret_purchase/print/cheque.php`  
**Line**: 67

**Test Steps**:
1. Open cheque print page
2. Inspect the account number div
3. Verify it uses `$cheque['acc_number']`
4. Check positioning and styling

**HTML Verification**:
```php
<div class="account-number"><?php echo (!empty($cheque['acc_number']) ? $cheque['acc_number'] : ''); ?></div>
```

**Expected Result**:
- Account number displays at correct position (top: 140pt, left: 100pt)
- Font size is 14pt, bold
- Empty check prevents errors if account number is missing

**Pass Criteria**: ✅ Account number displays correctly in template

---

#### Test Case 4.2: Cheque Data Array Contains Account Number
**File**: `cheque.php`

**Test Steps**:
1. Debug `$payment` array passed to view
2. Verify `acc_number` key exists in each payment detail
3. Check value matches karigar's account number

**Debug Code**:
```php
<?php 
foreach($payment as $p) {
    if($p['payment_mode'] == 'CHQ') {
        var_dump($p['acc_number']); // Should show karigar's account number
    }
}
?>
```

**Expected Result**:
- `acc_number` key exists in array
- Value matches karigar's account number from database
- No undefined index errors

**Pass Criteria**: ✅ Account number present in data array

---

#### Test Case 4.3: Cheque Filtering Logic Works
**File**: `cheque.php`  
**Lines**: 2-7

**Test Steps**:
1. Create payment with mixed payment modes (CASH, CHQ, NEFT)
2. Print cheque
3. Verify only CHQ mode payments are printed

**Expected Result**:
- Only cheque payments are filtered
- Account number is available for all cheque entries
- Non-cheque payments are excluded

**Pass Criteria**: ✅ Only cheque payments printed

---

### 5. Data Integrity Tests

#### Test Case 5.1: Verify ret_karigar Table Has acc_number Column
**Database Schema**

**Test Steps**:
1. Check `ret_karigar` table structure
2. Verify `acc_number` column exists
3. Check data type and length

**SQL Verification**:
```sql
DESCRIBE ret_karigar;
-- OR
SHOW COLUMNS FROM ret_karigar LIKE 'acc_number';
```

**Expected Result**:
```
Field: acc_number
Type: varchar(50) or similar
Null: YES
```

**Pass Criteria**: ✅ Column exists with appropriate data type

---

#### Test Case 5.2: Existing Karigar Records Have Account Numbers
**Data Validation**

**Test Steps**:
1. Query karigar records with payments
2. Check how many have account numbers populated
3. Identify records needing data entry

**SQL Verification**:
```sql
SELECT 
    COUNT(*) as total_karigars,
    SUM(CASE WHEN acc_number IS NOT NULL AND acc_number != '' THEN 1 ELSE 0 END) as with_account,
    SUM(CASE WHEN acc_number IS NULL OR acc_number = '' THEN 1 ELSE 0 END) as without_account
FROM ret_karigar
WHERE id_karigar IN (SELECT DISTINCT pay_sup_id FROM ret_po_payment);
```

**Expected Result**:
- Identify karigars needing account number entry
- Document data cleanup requirements
- No critical errors for existing records

**Pass Criteria**: ✅ Data quality assessed and documented

---

#### Test Case 5.3: Payment Records Link Correctly to Karigar
**Data Integrity**

**Test Steps**:
1. Verify all payments have valid `pay_sup_id`
2. Check for orphaned payment records
3. Ensure referential integrity

**SQL Verification**:
```sql
SELECT COUNT(*) as orphaned_payments
FROM ret_po_payment pay
LEFT JOIN ret_karigar kar ON kar.id_karigar = pay.pay_sup_id
WHERE kar.id_karigar IS NULL;
```

**Expected Result**:
- Zero orphaned payments
- All payments link to valid karigar records
- Foreign key constraints enforced (if applicable)

**Pass Criteria**: ✅ No orphaned payment records

---

### 6. Comparison Tests - Before vs After

#### Test Case 6.1: Compare Account Numbers Before and After Fix
**Regression Test**

**Test Steps**:
1. Identify test payment ID
2. Compare old query result (from `bank` table) vs new query result (from `ret_karigar` table)
3. Document differences

**Old Query** (Before Fix):
```sql
SELECT b.acc_number
FROM ret_po_payment_detail p
LEFT JOIN bank b ON b.id_bank = p.id_bank
WHERE p.pay_id = 888;
```

**New Query** (After Fix):
```sql
SELECT kar.acc_number
FROM ret_po_payment_detail p
LEFT JOIN ret_po_payment pay ON p.pay_id = pay.pay_id
LEFT JOIN ret_karigar kar ON kar.id_karigar = pay.pay_sup_id
WHERE p.pay_id = 888;
```

**Expected Result**:
- Old query returns company's bank account number (incorrect)
- New query returns supplier's account number (correct)
- Clear difference demonstrates fix effectiveness

**Pass Criteria**: ✅ New query returns correct account number

---

#### Test Case 6.2: Verify No Regression in Other Fields
**Regression Test**

**Test Steps**:
1. Compare all other fields returned by function
2. Ensure no unintended changes
3. Verify data consistency

**Fields to Verify**:
- `payment_amount`
- `payment_mode`
- `ref_no`
- `transfer_type`
- `ref_date`
- `cheque_date`
- `cheque_no`
- `id_bank`
- `payee_name`
- `bank_branch`

**Expected Result**:
- All other fields unchanged
- No data corruption
- Same behavior except for `acc_number`

**Pass Criteria**: ✅ No regression in other fields

---

### 7. Edge Cases and Error Handling

#### Test Case 7.1: Payment Without Karigar (Orphaned Payment)
**Edge Case**

**Test Steps**:
1. Create payment detail with invalid `pay_id`
2. Call `get_po_paid_payment($payment_id)`
3. Verify graceful handling

**Expected Result**:
- LEFT JOIN returns NULL for karigar fields
- `acc_number` is NULL
- No SQL errors
- Function returns successfully

**Pass Criteria**: ✅ Orphaned payments handled gracefully

---

#### Test Case 7.2: Karigar with Special Characters in Account Number
**Edge Case**

**Test Steps**:
1. Create karigar with account number containing special characters (e.g., "1234-5678-90")
2. Print cheque
3. Verify correct display

**Test Data**:
```sql
UPDATE ret_karigar SET acc_number = '1234-5678-90' WHERE id_karigar = 999;
```

**Expected Result**:
- Special characters display correctly
- No HTML encoding issues
- No truncation or formatting errors

**Pass Criteria**: ✅ Special characters handled correctly

---

#### Test Case 7.3: Very Long Account Numbers
**Edge Case**

**Test Steps**:
1. Create karigar with maximum length account number
2. Print cheque
3. Verify no overflow or truncation

**Test Data**:
```sql
UPDATE ret_karigar SET acc_number = '12345678901234567890123456789012345678901234567890' WHERE id_karigar = 999;
```

**Expected Result**:
- Account number displays completely (or truncates gracefully)
- No layout breaking
- CSS handles long text appropriately

**Pass Criteria**: ✅ Long account numbers handled appropriately

---

#### Test Case 7.4: Payment with No Payment Details
**Edge Case**

**Test Steps**:
1. Create payment with no payment details
2. Call `get_po_paid_payment($payment_id)`
3. Verify empty result

**Expected Result**:
- Function returns empty array
- No SQL errors
- No PHP warnings

**Pass Criteria**: ✅ Empty payments handled gracefully

---

### 8. Security Tests

#### Test Case 8.1: SQL Injection Prevention
**Security**

**Test Steps**:
1. Attempt SQL injection through `$id` parameter
2. Verify parameterized queries or proper escaping
3. Test with malicious input

**Test Input**:
```php
$id = "888 OR 1=1";
$id = "888; DROP TABLE ret_karigar;";
```

**Expected Result**:
- No SQL injection possible
- CodeIgniter's query builder handles escaping
- Invalid input returns no results or error

**Pass Criteria**: ✅ No SQL injection vulnerabilities

---

#### Test Case 8.2: XSS Prevention in Account Number Display
**Security**

**Test Steps**:
1. Insert account number with script tags
2. Print cheque
3. Verify script doesn't execute

**Test Data**:
```sql
UPDATE ret_karigar SET acc_number = '<script>alert("XSS")</script>' WHERE id_karigar = 999;
```

**Expected Result**:
- Script tags are HTML-encoded
- No JavaScript execution
- Account number displays as plain text

**Pass Criteria**: ✅ No XSS vulnerabilities

---

### 9. Performance Tests

#### Test Case 9.1: Query Execution Time
**Performance**

**Test Steps**:
1. Measure query execution time before fix
2. Measure query execution time after fix
3. Compare performance

**Benchmark**:
```sql
-- Run multiple times and average
SET @start = NOW(6);
SELECT * FROM ret_po_payment_detail p
LEFT JOIN ret_po_payment pay ON p.pay_id = pay.pay_id
LEFT JOIN bank b ON b.id_bank = p.id_bank
LEFT JOIN ret_karigar kar ON kar.id_karigar = pay.pay_sup_id
WHERE p.pay_id = 888;
SET @end = NOW(6);
SELECT TIMESTAMPDIFF(MICROSECOND, @start, @end) as execution_time_us;
```

**Expected Result**:
- Additional JOINs have minimal performance impact
- Execution time < 100ms for typical queries
- No significant degradation

**Pass Criteria**: ✅ Performance acceptable

---

#### Test Case 9.2: Index Recommendations
**Performance Optimization**

**Test Steps**:
1. Run `EXPLAIN` on the query
2. Identify missing indexes
3. Recommend index creation

**Recommended Indexes**:
```sql
-- If not already present
CREATE INDEX idx_pay_id ON ret_po_payment_detail(pay_id);
CREATE INDEX idx_pay_sup_id ON ret_po_payment(pay_sup_id);
CREATE INDEX idx_id_bank ON ret_po_payment_detail(id_bank);
```

**Expected Result**:
- Indexes improve query performance
- JOIN operations use indexes
- No full table scans

**Pass Criteria**: ✅ Appropriate indexes exist or recommended

---

### 10. User Acceptance Tests

#### Test Case 10.1: Print Quality - Account Number Legibility
**UAT**

**Test Steps**:
1. Print actual cheque on physical printer
2. Verify account number is legible
3. Check alignment with cheque format

**Expected Result**:
- Account number prints clearly
- Positioned correctly on cheque
- Font size and weight appropriate
- No overlapping with other fields

**Pass Criteria**: ✅ Printed cheque is acceptable quality

---

#### Test Case 10.2: User Workflow - No Additional Steps Required
**UAT**

**Test Steps**:
1. User creates payment as usual
2. User prints cheque as usual
3. Verify no additional steps needed

**Expected Result**:
- Workflow unchanged from user perspective
- Account number automatically populated
- No manual intervention required

**Pass Criteria**: ✅ User workflow seamless

---

#### Test Case 10.3: Error Messages - User-Friendly
**UAT**

**Test Steps**:
1. Attempt to print cheque for karigar without account number
2. Observe error message or behavior
3. Verify user understands what to do

**Expected Result**:
- Clear message if account number missing (or blank field)
- User can proceed or is guided to add account number
- No technical error messages shown to user

**Pass Criteria**: ✅ Error handling is user-friendly

---

## 🎯 Critical Test Scenarios (Must Pass)

### Priority 1: Correct Account Number Displayed
- **Test Case 1.1**: Correct account number retrieved from karigar table
- **Test Case 3.1**: End-to-end cheque print with correct account number
- **Test Case 4.1**: Account number display in cheque template
- **Test Case 6.1**: Compare account numbers before and after fix

**Why Critical**: This is the primary bug fix. Account numbers must be correct.

---

### Priority 2: No Data Corruption or Regression
- **Test Case 1.4**: Handle NULL account numbers gracefully
- **Test Case 6.2**: Verify no regression in other fields
- **Test Case 3.3**: Cheque print with missing account number

**Why Critical**: Ensure fix doesn't break existing functionality.

---

### Priority 3: Performance and Security
- **Test Case 8.1**: SQL injection prevention
- **Test Case 9.1**: Query execution time
- **Test Case 2.2**: Query performance - no Cartesian product

**Why Critical**: Security and performance must not be compromised.

---

## 📊 Test Execution Summary

| Category | Total Tests | Priority 1 | Priority 2 | Priority 3 |
|----------|-------------|------------|------------|------------|
| Model Tests | 5 | 2 | 2 | 1 |
| SQL Query Tests | 3 | 1 | 1 | 1 |
| Integration Tests | 3 | 2 | 1 | 0 |
| View Tests | 3 | 1 | 1 | 1 |
| Data Integrity Tests | 3 | 0 | 2 | 1 |
| Comparison Tests | 2 | 1 | 1 | 0 |
| Edge Cases | 4 | 0 | 2 | 2 |
| Security Tests | 2 | 0 | 0 | 2 |
| Performance Tests | 2 | 0 | 0 | 2 |
| UAT Tests | 3 | 1 | 1 | 1 |
| **TOTAL** | **30** | **8** | **11** | **11** |

---

## ✅ Test Execution Checklist

- [ ] All Model Tests Pass
- [ ] All SQL Query Tests Pass
- [ ] All Integration Tests Pass
- [ ] All View Tests Pass
- [ ] Data Integrity Verified
- [ ] Comparison Tests Confirm Fix
- [ ] Edge Cases Handled
- [ ] Security Validated
- [ ] Performance Acceptable
- [ ] UAT Completed
- [ ] **All Priority 1 Tests Pass** (CRITICAL)
- [ ] **All Priority 2 Tests Pass** (IMPORTANT)
- [ ] Code Review Completed
- [ ] Documentation Updated

---

## 🐛 Known Issues / Limitations

1. **Data Migration**: Existing karigar records may not have account numbers populated - requires data entry
2. **Assumption**: The fix assumes `ret_karigar.acc_number` column exists and is the correct source
3. **Bank Table**: The `bank` table JOIN is still present but `acc_number` is no longer used from it

---

## 📝 Notes

- **Root Cause**: Account number was fetched from `bank` table (company's bank account) instead of `ret_karigar` table (supplier's account)
- **Impact**: All cheque prints were showing incorrect account numbers or blank/asterisk symbols
- **Solution**: Modified SQL query to JOIN with `ret_po_payment` and `ret_karigar` tables to fetch correct account number
- **Benefits**: 
  - Correct supplier account numbers now display on cheques
  - No more content overlapping due to unexpected data
  - No more asterisk (*) symbols from missing data
  - Proper data source for account information

---

## 🔗 Related Files

- `admin/application/models/ret_purchase_order_model.php` (Lines 2620-2632, function `get_po_paid_payment()`)
- `admin/application/views/ret_purchase/print/cheque.php` (Line 67, account number display)
- `admin/application/controllers/admin_ret_purchase.php` (Controller calling the model function)

---

## 📋 Database Schema Reference

### Tables Involved

**ret_po_payment_detail**
- `id_pay_details` (PK)
- `pay_id` (FK to ret_po_payment)
- `payment_amount`
- `pay_mode`
- `ref_no`
- `type`
- `ref_date`
- `cheque_date`
- `cheque_no`
- `id_bank` (FK to bank)
- `payee_name`
- `bank_branch`

**ret_po_payment**
- `pay_id` (PK)
- `pay_sup_id` (FK to ret_karigar)
- Other payment fields...

**ret_karigar**
- `id_karigar` (PK)
- `firstname`
- `acc_number` ⭐ **This is the correct source**
- Other supplier fields...

**bank**
- `id_bank` (PK)
- `acc_number` ❌ **This was incorrectly used before**
- Other bank fields...

---

## 🔄 Migration Notes

If deploying this fix to production:

1. **Verify Schema**: Ensure `ret_karigar.acc_number` column exists
2. **Data Audit**: Check how many karigar records have account numbers populated
3. **Data Entry**: Plan to populate missing account numbers for active suppliers
4. **User Communication**: Inform users that account numbers will now display correctly
5. **Backup**: Take database backup before deployment
6. **Testing**: Test on staging environment with production data copy

---

**Test Document Version**: 1.0  
**Last Updated**: 14/02/2026  
**Status**: Ready for Execution
