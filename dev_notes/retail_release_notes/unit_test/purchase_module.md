# Purchase Module - Unit Test Cases
**Module**: Purchase Order Payment  
**Date**: 2026-02-07  
**Release**: Payment Balance Calculation Fix  

---

## Overview
This document contains unit test cases for the payment balance calculation fixes implemented in the Purchase Order Payment module. The fixes address issues with balance amount calculations, opening balance payments, and form submission validation.

---

## Test Case 1: Balance Amount Calculation (Without Opening)

### Test ID: `PO-PAY-001`
### Priority: **HIGH**
### Description
Verify that the balance amount field displays only the selected bills total, without adding opening amount.

### Pre-conditions
- User is logged in
- User has access to Purchase Order Payment form
- Supplier/Karigar has pending bills

### Test Steps
1. Navigate to: `Purchase > PO Payment > Add`
2. Select a Karigar/Supplier from dropdown
3. Wait for pending bills to load
4. Select 2-3 bills from the pending bills table by checking their checkboxes
5. Observe the "Balance Amount" field

### Expected Result
- ✅ Balance Amount field should show the **exact total of selected bills only**
- ✅ Opening amount should **NOT** be added to the balance
- ✅ Example: If Bill 1 = ₹1000, Bill 2 = ₹500, Balance Amount should show ₹1500

### Test Data
| Bill ID | Bill Amount | Selected |
|---------|-------------|----------|
| BILL001 | 1000.00     | ✓        |
| BILL002 | 500.00      | ✓        |
| BILL003 | 750.00      | ✗        |

**Expected Balance Amount**: ₹1500.00

### Code Reference
- File: [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L82696-L82700)
- Lines: 82696-82700

---

## Test Case 2: Balance Amount Visibility with Opening Checkbox

### Test ID: `PO-PAY-002`
### Priority: **HIGH**
### Description
Verify that the balance amount field remains visible and populated when the opening checkbox is checked.

### Pre-conditions
- User is logged in
- User has access to Purchase Order Payment form
- Supplier/Karigar has pending bills

### Test Steps
1. Navigate to: `Purchase > PO Payment > Add`
2. Select a Karigar/Supplier from dropdown
3. Wait for pending bills to load
4. Select 2 bills from the pending bills table
5. Note the Balance Amount displayed
6. Check the "OPENING" checkbox
7. Observe the Balance Amount field

### Expected Result
- ✅ Balance Amount field should **remain visible**
- ✅ Balance Amount should still show the selected bills total
- ✅ The value should **NOT change to 0** when opening is checked

### Test Data
| Bill ID | Bill Amount | Selected |
|---------|-------------|----------|
| BILL001 | 2000.00     | ✓        |
| BILL002 | 1500.00     | ✓        |

**Expected Balance Amount (Before Opening)**: ₹3500.00  
**Expected Balance Amount (After Opening)**: ₹3500.00

### Code Reference
- File: [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L82696-L82700)
- Lines: 82696-82700

---

## Test Case 3: Opening Checkbox Calculation

### Test ID: `PO-PAY-003`
### Priority: **HIGH**
### Description
Verify that when opening checkbox is checked, the system correctly calculates totals without errors.

### Pre-conditions
- User is logged in
- User has access to Purchase Order Payment form
- Supplier/Karigar has pending bills

### Test Steps
1. Navigate to: `Purchase > PO Payment > Add`
2. Select a Karigar/Supplier from dropdown
3. Check the "OPENING" checkbox first
4. Select 1 bill from the pending bills table
5. Enter payment amount in "Cash" field (e.g., 1000)
6. Observe all calculation fields

### Expected Result
- ✅ No JavaScript errors in console
- ✅ Balance Amount shows selected bill total
- ✅ Receive Amount shows entered payment amount
- ✅ All calculations work correctly

### Code Reference
- File: [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L82761-L82779)
- Lines: 82761-82779

---

## Test Case 4: Form Validation - No Bills Selected

### Test ID: `PO-PAY-004`
### Priority: **CRITICAL**
### Description
Verify that the system prevents payment submission when no bills are selected.

### Pre-conditions
- User is logged in
- User has access to Purchase Order Payment form
- Supplier/Karigar has pending bills

### Test Steps
1. Navigate to: `Purchase > PO Payment > Add`
2. Select a Karigar/Supplier from dropdown
3. Wait for pending bills to load
4. **Do NOT select any bills** (uncheck all checkboxes)
5. Enter payment amount in "Cash" field (e.g., 500)
6. Click "Save" button

### Expected Result
- ✅ Form submission should be **blocked**
- ✅ Error message displayed: "Please select at least one bill to make a payment."
- ✅ **No records inserted** into database
- ✅ User remains on the form page

### Code Reference
- File: [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L33679-L33686)
- Lines: 33679-33686

---

## Test Case 5: Form Validation - Opening with Zero Amount

### Test ID: `PO-PAY-005`
### Priority: **CRITICAL**
### Description
Verify that the system prevents payment submission when opening is checked but total payment amount is 0.

### Pre-conditions
- User is logged in
- User has access to Purchase Order Payment form
- Supplier/Karigar has pending bills

### Test Steps
1. Navigate to: `Purchase > PO Payment > Add`
2. Select a Karigar/Supplier from dropdown
3. Check the "OPENING" checkbox
4. Select 1 bill from the pending bills table
5. **Do NOT enter any payment amount** (leave all payment fields empty or 0)
6. Click "Save" button

### Expected Result
- ✅ Form submission should be **blocked**
- ✅ Error message displayed: "Please enter a payment amount for opening balance payment."
- ✅ **No records inserted** into database
- ✅ User remains on the form page

### Code Reference
- File: [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L33688-L33695)
- Lines: 33688-33695

---

## Test Case 6: Form Validation - Opening with Bills and Valid Amount

### Test ID: `PO-PAY-006`
### Priority: **HIGH**
### Description
Verify that payment submission succeeds when opening is checked, bills are selected, and valid payment amount is entered.

### Pre-conditions
- User is logged in
- User has access to Purchase Order Payment form
- Supplier/Karigar has pending bills

### Test Steps
1. Navigate to: `Purchase > PO Payment > Add`
2. Select a Karigar/Supplier from dropdown
3. Check the "OPENING" checkbox
4. Select 2 bills from the pending bills table
5. Enter payment amount in "Cash" field (e.g., 2000)
6. Fill in other required fields (payment date, etc.)
7. Click "Save" button

### Expected Result
- ✅ Form submission should **succeed**
- ✅ Success message displayed
- ✅ Payment record **inserted** into database
- ✅ User redirected to payment list or confirmation page

### Test Data
| Bill ID | Bill Amount | Selected |
|---------|-------------|----------|
| BILL001 | 1000.00     | ✓        |
| BILL002 | 1000.00     | ✓        |

**Payment Amount**: ₹2000.00  
**Opening Checked**: Yes

---

## Test Case 7: Selected Total Calculation

### Test ID: `PO-PAY-007`
### Priority: **MEDIUM**
### Description
Verify that the selected total is calculated correctly when bills are checked/unchecked.

### Pre-conditions
- User is logged in
- User has access to Purchase Order Payment form
- Supplier/Karigar has pending bills

### Test Steps
1. Navigate to: `Purchase > PO Payment > Add`
2. Select a Karigar/Supplier from dropdown
3. Select Bill 1 (₹1000) - observe total
4. Select Bill 2 (₹500) - observe total
5. Unselect Bill 1 - observe total
6. Select Bill 3 (₹750) - observe total

### Expected Result
- ✅ After Step 3: Total = ₹1000
- ✅ After Step 4: Total = ₹1500
- ✅ After Step 5: Total = ₹500
- ✅ After Step 6: Total = ₹1250

### Code Reference
- File: [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L82717-L82722)
- Lines: 82717-82722

---

## Test Case 8: Normal Payment (Without Opening)

### Test ID: `PO-PAY-008`
### Priority: **HIGH**
### Description
Verify that normal payment submission works correctly when opening checkbox is NOT checked.

### Pre-conditions
- User is logged in
- User has access to Purchase Order Payment form
- Supplier/Karigar has pending bills

### Test Steps
1. Navigate to: `Purchase > PO Payment > Add`
2. Select a Karigar/Supplier from dropdown
3. **Do NOT check** the "OPENING" checkbox
4. Select 3 bills from the pending bills table
5. Enter payment amount matching the total (e.g., 3000)
6. Fill in payment date and other required fields
7. Click "Save" button

### Expected Result
- ✅ Form submission should **succeed**
- ✅ Success message displayed
- ✅ Payment record inserted into database with correct amounts
- ✅ Selected bills are marked as paid/partially paid
- ✅ Opening flag is NOT set in database

### Test Data
| Bill ID | Bill Amount | Selected |
|---------|-------------|----------|
| BILL001 | 1000.00     | ✓        |
| BILL002 | 1000.00     | ✓        |
| BILL003 | 1000.00     | ✓        |

**Payment Amount**: ₹3000.00  
**Opening Checked**: No

---

## Test Case 9: Database Validation - No Zero Amount Records

### Test ID: `PO-PAY-009`
### Priority: **CRITICAL**
### Description
Verify that no records with 0 amount are inserted into the database when validation fails.

### Pre-conditions
- User is logged in
- User has access to Purchase Order Payment form
- Database access to verify records

### Test Steps
1. Navigate to: `Purchase > PO Payment > Add`
2. Select a Karigar/Supplier from dropdown
3. Attempt to submit payment without selecting bills (should fail validation)
4. Query database: `SELECT * FROM ret_po_payment WHERE pay_amount = 0 ORDER BY pay_id DESC LIMIT 5`
5. Check if any new records were inserted in the last minute

### Expected Result
- ✅ **No new records** with `pay_amount = 0` should be found
- ✅ Database remains unchanged after failed validation
- ✅ No orphan records created

### SQL Query for Verification
```sql
SELECT 
    pay_id, 
    pay_amount, 
    pay_date, 
    created_at 
FROM ret_po_payment 
WHERE pay_amount = 0 
    AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
ORDER BY pay_id DESC;
```

**Expected Result**: 0 rows

---

## Test Case 10: Multiple Bill Selection and Deselection

### Test ID: `PO-PAY-010`
### Priority: **MEDIUM**
### Description
Verify that selecting and deselecting multiple bills updates the balance amount correctly.

### Pre-conditions
- User is logged in
- User has access to Purchase Order Payment form
- Supplier/Karigar has at least 5 pending bills

### Test Steps
1. Navigate to: `Purchase > PO Payment > Add`
2. Select a Karigar/Supplier from dropdown
3. Check all bills using "Select All" checkbox (if available)
4. Observe Balance Amount
5. Uncheck 2 bills
6. Observe Balance Amount
7. Check those 2 bills again
8. Observe Balance Amount

### Expected Result
- ✅ After Step 4: Balance Amount = Sum of all bills
- ✅ After Step 5: Balance Amount = Sum of all bills - unchecked bills
- ✅ After Step 7: Balance Amount = Sum of all bills (same as Step 4)
- ✅ All calculations are accurate

---

## Regression Test Cases

### Test ID: `PO-PAY-REG-001`
### Description: Verify existing payment functionality still works
- ✅ Normal payment submission (without opening)
- ✅ Payment with multiple payment modes (Cash + Cheque + Net Banking)
- ✅ Payment date selection
- ✅ Narration/remarks field

### Test ID: `PO-PAY-REG-002`
### Description: Verify payment list page still works
- ✅ Payment list displays correctly
- ✅ Edit payment functionality
- ✅ Delete payment functionality
- ✅ Print payment receipt

---

## Test Execution Summary Template

| Test ID | Test Case | Status | Tested By | Date | Notes |
|---------|-----------|--------|-----------|------|-------|
| PO-PAY-001 | Balance Amount Calculation | ⬜ Pending | | | |
| PO-PAY-002 | Balance with Opening Checkbox | ⬜ Pending | | | |
| PO-PAY-003 | Opening Checkbox Calculation | ⬜ Pending | | | |
| PO-PAY-004 | Validation - No Bills Selected | ⬜ Pending | | | |
| PO-PAY-005 | Validation - Opening Zero Amount | ⬜ Pending | | | |
| PO-PAY-006 | Opening with Valid Amount | ⬜ Pending | | | |
| PO-PAY-007 | Selected Total Calculation | ⬜ Pending | | | |
| PO-PAY-008 | Normal Payment | ⬜ Pending | | | |
| PO-PAY-009 | Database Validation | ⬜ Pending | | | |
| PO-PAY-010 | Multiple Bill Selection | ⬜ Pending | | | |

**Legend**: ⬜ Pending | ✅ Pass | ❌ Fail | ⚠️ Blocked

---

## Bug Report Template

If any test case fails, use this template to report the bug:

```
Bug ID: BUG-PO-PAY-XXX
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
```

---

## Files Modified

| File | Lines Modified | Description |
|------|----------------|-------------|
| [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L82696-L82700) | 82696-82700 | Fixed balance calculation to always show balance amount |
| [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L82717-L82722) | 82717-82722 | Removed conditional check in `calculateSelectedTotal()` |
| [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L37621) | 37621 | Fixed field selector in `calculatePaymentCost()` |
| [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L82761-L82779) | 82761-82779 | Updated opening checkbox handler |
| [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L33679-L33696) | 33679-33696 | Added form submission validation to prevent invalid database insertions |

---

## Notes for QA Team

1. **Critical Tests**: Test cases PO-PAY-004, PO-PAY-005, and PO-PAY-009 are critical as they prevent data integrity issues
2. **Browser Testing**: Test on Chrome, Firefox, and Edge browsers
3. **Database Verification**: Always verify database after validation tests to ensure no orphan records
4. **User Permissions**: Test with different user roles if applicable
5. **Network Conditions**: Test with slow network to ensure validation works before AJAX completes

---

**Document Version**: 1.0  
**Last Updated**: 2026-02-07  
**Created By**: Development Team

---

## Additional Test Cases - CR/DR Weight Adjustment

### Test ID: `PO-PAY-011`
### Priority: **HIGH**
### Description
Verify that remaining weight calculation correctly accounts for CR/DR note adjustments in the `get_pending_po_bills_after_tagging_without_pcs_with_weight` function.

### Pre-conditions
- User is logged in
- Purchase Order exists with tagged items
- CR/DR notes have been created for the PO
- Database access to verify calculations

### Test Steps
1. Navigate to: `Purchase > PO Payment > Add`
2. Select a Karigar/Supplier who has a PO with:
   - Tagged items (some weight tagged)
   - CR/DR notes (both credit and debit)
3. Observe the "Remaining Weight" displayed in the pending bills table
4. Manually calculate expected remaining weight:
   - Formula: `Total Lot Weight - (Tagged Weight - DR Weight + CR Weight)`
5. Compare displayed value with calculated value

### Expected Result
- ✅ Remaining weight should correctly subtract DR (Debit) weight
- ✅ Remaining weight should correctly add CR (Credit) weight
- ✅ Formula applied: `remining_wt = SUM(lot.gross_wt) - SUM(IFNULL(tag.gross_wt, 0) - IFNULL(crdr.dr_wt, 0) + IFNULL(crdr.cr_wt, 0))`
- ✅ Displayed remaining weight matches manual calculation

### Test Data Example
| Component | Weight (grams) |
|-----------|----------------|
| Total Lot Weight | 1000.00 |
| Tagged Weight | 600.00 |
| DR (Debit) Weight | 50.00 |
| CR (Credit) Weight | 30.00 |

**Calculation**:
```
Remaining Weight = 1000 - (600 - 50 + 30)
                 = 1000 - 580
                 = 420.00 grams
```

### Code Reference
- File: [`ret_purchase_order_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_purchase_order_model.php#L8722-L8724)
- Lines: 8722-8724
- Function: `get_pending_po_bills_after_tagging_without_pcs_with_weight`

### SQL Verification Query
```sql
SELECT 
    i.lot_no,
    SUM(lot.gross_wt) AS total_lot_weight,
    SUM(IFNULL(tag.gross_wt, 0)) AS tagged_weight,
    IFNULL(crdr.dr_wt, 0) AS debit_weight,
    IFNULL(crdr.cr_wt, 0) AS credit_weight,
    (SUM(lot.gross_wt) - SUM(IFNULL(tag.gross_wt, 0) - IFNULL(crdr.dr_wt, 0) + IFNULL(crdr.cr_wt, 0))) AS remaining_weight
FROM ret_lot_inwards i
LEFT JOIN ret_lot_inwards_detail lot ON lot.lot_no = i.lot_no
LEFT JOIN ret_purchase_order pur ON pur.po_id = i.po_id
LEFT JOIN (
    SELECT po_id, 
        SUM(CASE WHEN transtype = 2 THEN weight ELSE 0 END) as dr_wt,
        SUM(CASE WHEN transtype = 1 THEN weight ELSE 0 END) as cr_wt
    FROM ret_crdr_note 
    WHERE crdr_status = 1
    GROUP BY po_id
) crdr ON crdr.po_id = pur.po_id
LEFT JOIN (
    SELECT tag_lot_id, SUM(gross_wt) AS gross_wt
    FROM ret_taging
    GROUP BY tag_lot_id
) tag ON tag.tag_lot_id = lot.lot_no
WHERE pur.po_id = [PO_ID]
GROUP BY i.lot_no;
```

---

### Test ID: `PO-PAY-012`
### Priority: **MEDIUM**
### Description
Verify that CR/DR notes with `crdr_status = 0` (cancelled/inactive) are excluded from weight calculations.

### Pre-conditions
- User is logged in
- Purchase Order exists with both active and cancelled CR/DR notes
- Database access to verify data

### Test Steps
1. Create a PO with tagged items
2. Create a CR note with `crdr_status = 1` (active) - 20 grams
3. Create a DR note with `crdr_status = 0` (cancelled) - 15 grams
4. Navigate to: `Purchase > PO Payment > Add`
5. Select the Karigar/Supplier for this PO
6. Observe the remaining weight calculation

### Expected Result
- ✅ Only CR/DR notes with `crdr_status = 1` should be included
- ✅ Cancelled CR/DR notes (`crdr_status = 0`) should be ignored
- ✅ Remaining weight calculation should only consider active CR note (20g)
- ✅ Cancelled DR note (15g) should NOT affect the calculation

### Code Reference
- File: [`ret_purchase_order_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_purchase_order_model.php#L8749-L8753)
- Lines: 8749-8753

---

### Test ID: `PO-PAY-013`
### Priority: **HIGH**
### Description
Verify that multiple CR/DR notes for the same PO are correctly aggregated in weight calculations.

### Pre-conditions
- User is logged in
- Purchase Order exists with multiple CR/DR notes

### Test Steps
1. Create a PO with tagged items (Total: 1000g, Tagged: 500g)
2. Create multiple CR/DR notes:
   - CR Note 1: 10 grams (credit)
   - CR Note 2: 15 grams (credit)
   - DR Note 1: 20 grams (debit)
   - DR Note 2: 5 grams (debit)
3. All notes should have `crdr_status = 1`
4. Navigate to: `Purchase > PO Payment > Add`
5. Select the Karigar/Supplier
6. Verify remaining weight calculation

### Expected Result
- ✅ All CR notes should be summed: 10 + 15 = 25 grams
- ✅ All DR notes should be summed: 20 + 5 = 25 grams
- ✅ Remaining weight = 1000 - (500 - 25 + 25) = 500 grams
- ✅ Calculation correctly aggregates multiple CR/DR notes per PO

### Test Data
| Note Type | Weight | Status | Included |
|-----------|--------|--------|----------|
| CR Note 1 | 10.00  | 1      | ✓        |
| CR Note 2 | 15.00  | 1      | ✓        |
| DR Note 1 | 20.00  | 1      | ✓        |
| DR Note 2 | 5.00   | 1      | ✓        |

**Total CR**: 25.00g  
**Total DR**: 25.00g  
**Net Adjustment**: 0.00g

---

### Test ID: `PO-PAY-014`
### Priority: **MEDIUM**
### Description
Verify that POs without any CR/DR notes still calculate remaining weight correctly.

### Pre-conditions
- User is logged in
- Purchase Order exists WITHOUT any CR/DR notes

### Test Steps
1. Create a PO with tagged items (Total: 800g, Tagged: 300g)
2. Do NOT create any CR/DR notes for this PO
3. Navigate to: `Purchase > PO Payment > Add`
4. Select the Karigar/Supplier
5. Verify remaining weight calculation

### Expected Result
- ✅ Remaining weight = 800 - 300 = 500 grams
- ✅ No errors occur when CR/DR notes are NULL
- ✅ IFNULL functions handle NULL values correctly
- ✅ Calculation works as expected: `remining_wt = 800 - (300 - 0 + 0) = 500`

### Code Reference
- File: [`ret_purchase_order_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_purchase_order_model.php#L8722-L8724)
- Lines: 8722-8724

---

## Additional Test Cases - Purchase Return Module

### Test ID: `PO-RET-001`
### Priority: **MEDIUM**
### Description
Verify that the `purchase_type` field correctly displays "Sales Return" when `purchase_type = 2` in the returned request list.

### Pre-conditions
- User is logged in
- Purchase return records exist with different purchase types

### Test Steps
1. Navigate to: `Purchase > Purchase Return > List`
2. Create/verify purchase return records with:
   - `purchase_type = 0` (Purchase)
   - `purchase_type = 1` (Sales)
   - `purchase_type = 2` (Sales Return)
3. Observe the "Purchase Type" column in the list

### Expected Result
- ✅ `purchase_type = 0` displays "Purchase"
- ✅ `purchase_type = 1` displays "Sales"
- ✅ `purchase_type = 2` displays "Sales Return"
- ✅ Any other value displays "-"

### Code Reference
- File: [`ret_purchase_order_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_purchase_order_model.php#L1816-L1821)
- Lines: 1816-1821
- Function: `getReturnedRequestList`

---

### Test ID: `PO-RET-002`
### Priority: **MEDIUM**
### Description
Verify that the `stock_type` filter correctly filters purchase return records.

### Pre-conditions
- User is logged in
- Purchase return records exist with different stock types

### Test Steps
1. Navigate to: `Purchase > Purchase Return > List`
2. Apply filter with `stock_type = 0` (Purchase)
3. Verify filtered results
4. Apply filter with `stock_type = 1` (Sales)
5. Verify filtered results
6. Apply filter with `stock_type = 3` (All)
7. Verify all records are shown

### Expected Result
- ✅ When `stock_type = 0`: Only Purchase returns are shown
- ✅ When `stock_type = 1`: Only Sales returns are shown
- ✅ When `stock_type = 3`: All records are shown (no filter applied)
- ✅ Filter works correctly with other filters (karigar, PO, etc.)

### Code Reference
- File: [`ret_purchase_order_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_purchase_order_model.php#L1840)
- Line: 1840

---

## Updated Test Execution Summary

| Test ID | Test Case | Priority | Status | Tested By | Date | Notes |
|---------|-----------|----------|--------|-----------|------|-------|
| PO-PAY-001 | Balance Amount Calculation | HIGH | ⬜ Pending | | | |
| PO-PAY-002 | Balance with Opening Checkbox | HIGH | ⬜ Pending | | | |
| PO-PAY-003 | Opening Checkbox Calculation | HIGH | ⬜ Pending | | | |
| PO-PAY-004 | Validation - No Bills Selected | CRITICAL | ⬜ Pending | | | |
| PO-PAY-005 | Validation - Opening Zero Amount | CRITICAL | ⬜ Pending | | | |
| PO-PAY-006 | Opening with Valid Amount | HIGH | ⬜ Pending | | | |
| PO-PAY-007 | Selected Total Calculation | MEDIUM | ⬜ Pending | | | |
| PO-PAY-008 | Normal Payment | HIGH | ⬜ Pending | | | |
| PO-PAY-009 | Database Validation | CRITICAL | ⬜ Pending | | | |
| PO-PAY-010 | Multiple Bill Selection | MEDIUM | ⬜ Pending | | | |
| **PO-PAY-011** | **CR/DR Weight Adjustment** | **HIGH** | ⬜ Pending | | | **New** |
| **PO-PAY-012** | **CR/DR Status Filter** | **MEDIUM** | ⬜ Pending | | | **New** |
| **PO-PAY-013** | **Multiple CR/DR Aggregation** | **HIGH** | ⬜ Pending | | | **New** |
| **PO-PAY-014** | **No CR/DR Notes Handling** | **MEDIUM** | ⬜ Pending | | | **New** |
| **PO-RET-001** | **Purchase Type Display** | **MEDIUM** | ⬜ Pending | | | **New** |
| **PO-RET-002** | **Stock Type Filter** | **MEDIUM** | ⬜ Pending | | | **New** |

---

## Updated Files Modified

| File | Lines Modified | Description |
|------|----------------|-------------|
| [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L82696-L82700) | 82696-82700 | Fixed balance calculation to always show balance amount |
| [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L82717-L82722) | 82717-82722 | Removed conditional check in `calculateSelectedTotal()` |
| [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L37621) | 37621 | Fixed field selector in `calculatePaymentCost()` |
| [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L82761-L82779) | 82761-82779 | Updated opening checkbox handler |
| [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L33679-L33696) | 33679-33696 | Added form submission validation to prevent invalid database insertions |
| **[`ret_purchase_order_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_purchase_order_model.php#L8722-L8724)** | **8722-8724** | **Fixed remaining weight calculation to include CR/DR adjustments** |
| **[`ret_purchase_order_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_purchase_order_model.php#L8749-L8753)** | **8749-8753** | **Added CR/DR note JOIN with status filter** |
| **[`ret_purchase_order_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_purchase_order_model.php#L1816-L1821)** | **1816-1821** | **Updated purchase_type CASE statement to include Sales Return** |
| **[`ret_purchase_order_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_purchase_order_model.php#L1840)** | **1840** | **Added stock_type filter condition** |

---

**Document Version**: 1.1  
**Last Updated**: 2026-02-07 18:07  
**Created By**: Development Team

---

## Additional Test Cases - Rate Cut Profit/Loss

### Test ID: `PO-RATE-001`
### Priority: **HIGH**
### Description
Verify that the rate cut profit/loss report correctly fetches the bullion rate when an exact date match exists in the `metal_rates` table.

### Pre-conditions
- User is logged in
- Rate cut records exist in `ret_supplier_rate_cut` table
- Corresponding bullion rates exist in `metal_rates` table for the same dates

### Test Steps
1. Navigate to: `Dashboard > Rate Cut Profit/Loss Report`
2. Select date range: `14-12-2025` to `14-12-2025`
3. Select branch (if applicable)
4. Click "Generate Report" or "Search"
5. Observe the "Current Bullion Rate" column

### Expected Result
- ✅ Current Bullion Rate should display **₹13,494.00** (exact match for 14-12-2025)
- ✅ Rate should NOT be 0
- ✅ Profit/Loss status should be calculated correctly
- ✅ Profit percentage should be displayed

### Test Data
| Rate Cut Date | Rate Cut Rate | Expected Bullion Rate | Source |
|---------------|---------------|----------------------|---------|
| 14-12-2025 | 5,200.00 | 13,494.00 | Exact match from metal_rates |

### Code Reference
- File: [`ret_dashboard_api_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_dashboard_api_model.php#L2565-L2569)
- Lines: 2565-2569
- Function: `get_rate_cut_profit_loss`

---

### Test ID: `PO-RATE-002`
### Priority: **CRITICAL**
### Description
Verify that the system automatically fetches the most recent previous non-zero bullion rate when no exact date match exists.

### Pre-conditions
- User is logged in
- Rate cut exists for date 25-12-2025
- No bullion rate exists for 25-12-2025 in `metal_rates`
- Most recent bullion rate is from 20-12-2025 (₹13,506.00)

### Test Steps
1. Navigate to: `Dashboard > Rate Cut Profit/Loss Report`
2. Select date range: `25-12-2025` to `25-12-2025`
3. Select branch (if applicable)
4. Click "Generate Report"
5. Observe the "Current Bullion Rate" column

### Expected Result
- ✅ Current Bullion Rate should display **₹13,506.00** (from 20-12-2025)
- ✅ System should automatically fetch the most recent previous rate
- ✅ Rate should NOT be 0
- ✅ No errors in console or UI

### Test Data
| Rate Cut Date | Metal Rates Available | Expected Bullion Rate | Fetched From |
|---------------|----------------------|----------------------|--------------|
| 25-12-2025 | 23-12: 0.00, 22-12: 0.00, 20-12: 13,506.00 | 13,506.00 | 20-12-2025 |

### Code Reference
- File: [`ret_dashboard_api_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_dashboard_api_model.php#L2565-L2569)
- Lines: 2565-2569

### SQL Verification Query
```sql
-- Verify the correlated subquery logic
SELECT 
    '2025-12-25' as rate_cut_date,
    (SELECT goldrate_24ct 
     FROM metal_rates 
     WHERE DATE(updatetime) <= '2025-12-25' 
     AND goldrate_24ct > 0
     ORDER BY updatetime DESC 
     LIMIT 1) as fetched_rate,
    (SELECT DATE(updatetime) 
     FROM metal_rates 
     WHERE DATE(updatetime) <= '2025-12-25' 
     AND goldrate_24ct > 0
     ORDER BY updatetime DESC 
     LIMIT 1) as fetched_from_date;
```

**Expected Result**: 
- `fetched_rate`: 13506.00
- `fetched_from_date`: 2025-12-20

---

### Test ID: `PO-RATE-003`
### Priority: **CRITICAL**
### Description
Verify that the system skips zero-value bullion rates and fetches the most recent non-zero rate.

### Pre-conditions
- User is logged in
- Rate cut exists for date 23-12-2025
- Metal rates table has entries with `goldrate_24ct = 0.00` for recent dates
- Most recent non-zero rate is from 20-12-2025

### Test Steps
1. Verify database has zero rates:
   ```sql
   SELECT DATE(updatetime), goldrate_24ct 
   FROM metal_rates 
   WHERE DATE(updatetime) BETWEEN '2025-12-20' AND '2025-12-25'
   ORDER BY updatetime DESC;
   ```
2. Navigate to: `Dashboard > Rate Cut Profit/Loss Report`
3. Select date range: `23-12-2025` to `23-12-2025`
4. Click "Generate Report"
5. Observe the "Current Bullion Rate" column

### Expected Result
- ✅ System should **skip** entries where `goldrate_24ct = 0.00`
- ✅ Current Bullion Rate should display **₹13,506.00** (from 20-12-2025)
- ✅ Should NOT display 0.00 even though more recent dates have zero values
- ✅ Query uses `AND goldrate_24ct > 0` condition

### Test Data
| Date | Bullion Rate | Should Use? |
|------|--------------|-------------|
| 23-12-2025 | 0.00 | ❌ Skip |
| 22-12-2025 | 0.00 | ❌ Skip |
| 20-12-2025 | 13,506.00 | ✅ Use this |

### Code Reference
- File: [`ret_dashboard_api_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_dashboard_api_model.php#L2565-L2569)
- Lines: 2565-2569

---

### Test ID: `PO-RATE-004`
### Priority: **HIGH**
### Description
Verify that profit status is correctly calculated when bullion rate is higher than rate cut rate.

### Pre-conditions
- User is logged in
- Rate cut exists with rate_per_gram = 5,000.00
- Current bullion rate = 13,494.00

### Test Steps
1. Navigate to: `Dashboard > Rate Cut Profit/Loss Report`
2. Select date range containing the test rate cut
3. Click "Generate Report"
4. Observe the "Profit/Loss Status" and "Profit Percentage" columns

### Expected Result
- ✅ Profit/Loss Status should display **"profit"**
- ✅ Rate Deviation should be **positive** (13,494 - 5,000 = 8,494)
- ✅ Profit Percentage should be **positive** (~62.95%)
- ✅ Formula: `((bullion_rate - rate_cut_rate) / bullion_rate) * 100`

### Test Data
| Rate Cut Rate | Bullion Rate | Expected Status | Expected Deviation | Expected % |
|---------------|--------------|-----------------|-------------------|------------|
| 5,000.00 | 13,494.00 | profit | 8,494.00 | 62.95% |

### Code Reference
- File: [`ret_dashboard_api_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_dashboard_api_model.php#L2578-L2596)
- Lines: 2578-2596

---

### Test ID: `PO-RATE-005`
### Priority: **HIGH**
### Description
Verify that loss status is correctly calculated when rate cut rate is higher than bullion rate.

### Pre-conditions
- User is logged in
- Rate cut exists with rate_per_gram = 14,000.00
- Current bullion rate = 13,494.00

### Test Steps
1. Navigate to: `Dashboard > Rate Cut Profit/Loss Report`
2. Select date range containing the test rate cut
3. Click "Generate Report"
4. Observe the "Profit/Loss Status" and "Profit Percentage" columns

### Expected Result
- ✅ Profit/Loss Status should display **"loss"**
- ✅ Rate Deviation should be **negative** (13,494 - 14,000 = -506)
- ✅ Profit Percentage should be **negative** (~-3.75%)
- ✅ Status indicator should show loss (red color/icon if applicable)

### Test Data
| Rate Cut Rate | Bullion Rate | Expected Status | Expected Deviation | Expected % |
|---------------|--------------|-----------------|-------------------|------------|
| 14,000.00 | 13,494.00 | loss | -506.00 | -3.75% |

---

### Test ID: `PO-RATE-006`
### Priority: **MEDIUM**
### Description
Verify that neutral status is correctly calculated when rate cut rate equals bullion rate.

### Pre-conditions
- User is logged in
- Rate cut exists with rate_per_gram = 13,494.00
- Current bullion rate = 13,494.00

### Test Steps
1. Navigate to: `Dashboard > Rate Cut Profit/Loss Report`
2. Select date range containing the test rate cut
3. Click "Generate Report"
4. Observe the "Profit/Loss Status" and "Profit Percentage" columns

### Expected Result
- ✅ Profit/Loss Status should display **"neutral"**
- ✅ Rate Deviation should be **0.00**
- ✅ Profit Percentage should be **0.00%**
- ✅ No profit or loss indicator shown

### Test Data
| Rate Cut Rate | Bullion Rate | Expected Status | Expected Deviation | Expected % |
|---------------|--------------|-----------------|-------------------|------------|
| 13,494.00 | 13,494.00 | neutral | 0.00 | 0.00% |

---

### Test ID: `PO-RATE-007`
### Priority: **HIGH**
### Description
Verify that multiple rate cuts in a date range all display correct bullion rates (each fetching their own appropriate rate).

### Pre-conditions
- User is logged in
- Multiple rate cuts exist across different dates
- Metal rates table has varying rates for different dates

### Test Steps
1. Navigate to: `Dashboard > Rate Cut Profit/Loss Report`
2. Select date range: `01-12-2025` to `31-12-2025`
3. Click "Generate Report"
4. Observe all records in the result table

### Expected Result
- ✅ Each rate cut should fetch its own appropriate bullion rate
- ✅ Rate cut on 14-12-2025 should show ₹13,494.00
- ✅ Rate cut on 20-12-2025 should show ₹13,506.00
- ✅ Rate cut on 25-12-2025 should show ₹13,506.00 (from 20-12)
- ✅ All records should have non-zero bullion rates

### Test Data
| Rate Cut Date | Expected Bullion Rate | Fetched From |
|---------------|----------------------|--------------|
| 14-12-2025 | 13,494.00 | 14-12-2025 (exact) |
| 20-12-2025 | 13,506.00 | 20-12-2025 (exact) |
| 25-12-2025 | 13,506.00 | 20-12-2025 (previous) |

---

### Test ID: `PO-RATE-008`
### Priority: **MEDIUM**
### Description
Verify that branch filter correctly filters rate cut records.

### Pre-conditions
- User is logged in
- Rate cuts exist for multiple branches
- User has access to multiple branches

### Test Steps
1. Navigate to: `Dashboard > Rate Cut Profit/Loss Report`
2. Select specific branch from dropdown
3. Select date range
4. Click "Generate Report"
5. Verify all records belong to selected branch

### Expected Result
- ✅ Only rate cuts for selected branch should be displayed
- ✅ All records should have correct bullion rates
- ✅ Branch filter works correctly with date filter

### Code Reference
- File: [`ret_dashboard_api_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_dashboard_api_model.php#L2624)
- Line: 2624

---

### Test ID: `PO-RATE-009`
### Priority: **MEDIUM**
### Description
Verify that empty result set is handled gracefully when no rate cuts exist for the selected criteria.

### Pre-conditions
- User is logged in
- No rate cuts exist for the selected date range/branch

### Test Steps
1. Navigate to: `Dashboard > Rate Cut Profit/Loss Report`
2. Select date range with no rate cuts (e.g., `01-01-2020` to `31-01-2020`)
3. Click "Generate Report"

### Expected Result
- ✅ Report should display "No records found" or similar message
- ✅ No errors should occur
- ✅ Table should be empty or show appropriate message
- ✅ No JavaScript console errors

---

### Test ID: `PO-RATE-010`
### Priority: **LOW**
### Description
Verify that the average daily rate cut rate is calculated correctly for multiple rate cuts on the same date.

### Pre-conditions
- User is logged in
- Multiple rate cuts exist for the same date with different rates

### Test Steps
1. Create 3 rate cuts for date 14-12-2025:
   - Rate Cut 1: 5,000.00
   - Rate Cut 2: 5,200.00
   - Rate Cut 3: 5,100.00
2. Navigate to: `Dashboard > Rate Cut Profit/Loss Report`
3. Select date: `14-12-2025`
4. Click "Generate Report"
5. Observe the "Rate Cut Rate" column

### Expected Result
- ✅ Rate Cut Rate should show **average**: (5000 + 5200 + 5100) / 3 = **5,100.00**
- ✅ All three records should show the same average rate
- ✅ Profit/Loss calculation should use this average rate

### Test Data
| Rate Cut | Individual Rate | Expected Average |
|----------|----------------|------------------|
| RC001 | 5,000.00 | 5,100.00 |
| RC002 | 5,200.00 | 5,100.00 |
| RC003 | 5,100.00 | 5,100.00 |

### Code Reference
- File: [`ret_dashboard_api_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_dashboard_api_model.php#L2560-L2563)
- Lines: 2560-2563

---

## Dashboard - Delayed PO Payment Logic

### Test ID: `PO-DASH-011`
### Priority: **CRITICAL**
### Description
Verify that POs are correctly listed as delayed based on their delivery date, regardless of partial payment dates.

### Pre-conditions
- User is logged in
- PO exists with `po_delivery_date` < `CURRENT_DATE()`
- PO has outstanding weight balance (`tot_purchase_wt - total_rate_cut_wt - total_issue_wt > 0`)

### Test Steps
1. Navigate to: `Dashboard`
2. Locate the "Delayed PO Payments" section
3. Verify the PO with past delivery date is listed
4. Record a partial payment for this PO dated *after* the delivery date
5. Refresh the Dashboard
6. Verify the PO remains in the delayed list

### Expected Result
- ✅ PO appears in the list as soon as `po_delivery_date` is passed
- ✅ Partial payments do not exclude the PO from the list if weight is still pending
- ✅ Query logic uses `DATEDIFF(po.po_delivery_date, CURRENT_DATE()) < 0`

### Code Reference
- File: [`ret_dashboard_api_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_dashboard_api_model.php#L1546-L1548)
- Lines: 1546-1548
- Function: `get_delayed_po_payments`

---

## Rate Cut P&L - Edge Cases

### Test ID: `PO-RATE-011`
### Priority: **HIGH**
### Description
Verify system behavior when no bullion rates exist in the database at all.

### Pre-conditions
- User is logged in
- Rate cuts exist
- `metal_rates` table is empty OR all rates are 0

### Test Steps
1. Verify metal_rates condition:
   ```sql
   SELECT COUNT(*) FROM metal_rates WHERE goldrate_24ct > 0;
   ```
2. Navigate to: `Dashboard > Rate Cut Profit/Loss Report`
3. Generate report

### Expected Result
- ✅ Current Bullion Rate should display **0.00**
- ✅ Profit/Loss Status should be **"neutral"**
- ✅ No errors should occur
- ✅ System should handle NULL gracefully with IFNULL

---

### Test ID: `PO-RATE-012`
### Priority: **MEDIUM**
### Description
Verify that the query performs efficiently with large datasets.

### Pre-conditions
- Database has 1000+ rate cut records
- Database has 365+ metal rate records

### Test Steps
1. Navigate to: `Dashboard > Rate Cut Profit/Loss Report`
2. Select large date range (e.g., entire year)
3. Click "Generate Report"
4. Measure query execution time

### Expected Result
- ✅ Report should load within **5 seconds**
- ✅ No timeout errors
- ✅ All records should have correct bullion rates
- ✅ Pagination should work correctly if implemented

### Performance Note
The correlated subquery is executed for each row. Consider adding an index on `metal_rates(updatetime, goldrate_24ct)` for better performance.

---

## Updated Test Execution Summary

| Test ID | Test Case | Priority | Status | Tested By | Date | Notes |
|---------|-----------|----------|--------|-----------|------|-------|
| PO-PAY-001 | Balance Amount Calculation | HIGH | ⬜ Pending | | | |
| PO-PAY-002 | Balance with Opening Checkbox | HIGH | ⬜ Pending | | | |
| PO-PAY-003 | Opening Checkbox Calculation | HIGH | ⬜ Pending | | | |
| PO-PAY-004 | Validation - No Bills Selected | CRITICAL | ⬜ Pending | | | |
| PO-PAY-005 | Validation - Opening Zero Amount | CRITICAL | ⬜ Pending | | | |
| PO-PAY-006 | Opening with Valid Amount | HIGH | ⬜ Pending | | | |
| PO-PAY-007 | Selected Total Calculation | MEDIUM | ⬜ Pending | | | |
| PO-PAY-008 | Normal Payment | HIGH | ⬜ Pending | | | |
| PO-PAY-009 | Database Validation | CRITICAL | ⬜ Pending | | | |
| PO-PAY-010 | Multiple Bill Selection | MEDIUM | ⬜ Pending | | | |
| PO-PAY-011 | CR/DR Weight Adjustment | HIGH | ⬜ Pending | | | |
| PO-PAY-012 | CR/DR Status Filter | MEDIUM | ⬜ Pending | | | |
| PO-PAY-013 | Multiple CR/DR Aggregation | HIGH | ⬜ Pending | | | |
| PO-PAY-014 | No CR/DR Notes Handling | MEDIUM | ⬜ Pending | | | |
| PO-RET-001 | Purchase Type Display | MEDIUM | ⬜ Pending | | | |
| PO-RET-002 | Stock Type Filter | MEDIUM | ⬜ Pending | | | |
| **PO-RATE-001** | **Rate Cut - Exact Date Match** | **HIGH** | ⬜ Pending | | | **New** |
| **PO-RATE-002** | **Rate Cut - Previous Date Fallback** | **CRITICAL** | ⬜ Pending | | | **New** |
| **PO-RATE-003** | **Rate Cut - Skip Zero Rates** | **CRITICAL** | ⬜ Pending | | | **New** |
| **PO-RATE-004** | **Rate Cut - Profit Calculation** | **HIGH** | ⬜ Pending | | | **New** |
| **PO-RATE-005** | **Rate Cut - Loss Calculation** | **HIGH** | ⬜ Pending | | | **New** |
| **PO-RATE-006** | **Rate Cut - Neutral Calculation** | **MEDIUM** | ⬜ Pending | | | **New** |
| **PO-RATE-007** | **Rate Cut - Multiple Records** | **HIGH** | ⬜ Pending | | | **New** |
| **PO-RATE-008** | **Rate Cut - Branch Filter** | **MEDIUM** | ⬜ Pending | | | **New** |
| **PO-RATE-009** | **Rate Cut - Empty Result Set** | **MEDIUM** | ⬜ Pending | | | **New** |
| **PO-RATE-010** | **Rate Cut - Average Daily Rate** | **LOW** | ⬜ Pending | | | **New** |
| PO-RATE-011 | Rate Cut - No Bullion Rates | HIGH | ⬜ Pending | | | |
| PO-RATE-012 | Rate Cut - Performance Test | MEDIUM | ⬜ Pending | | | |
| **PO-DASH-011** | **Delayed PO - Date Logic** | **CRITICAL** | ⬜ Pending | | | **New** |

---

## Updated Files Modified

| File | Lines Modified | Description |
|------|----------------|-------------|
| [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L82696-L82700) | 82696-82700 | Fixed balance calculation to always show balance amount |
| [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L82717-L82722) | 82717-82722 | Removed conditional check in `calculateSelectedTotal()` |
| [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L37621) | 37621 | Fixed field selector in `calculatePaymentCost()` |
| [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L82761-L82779) | 82761-82779 | Updated opening checkbox handler |
| [`ret_purchase_order.js`](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_purchase_order.js#L33679-L33696) | 33679-33696 | Added form submission validation to prevent invalid database insertions |
| [`ret_purchase_order_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_purchase_order_model.php#L8722-L8724) | 8722-8724 | Fixed remaining weight calculation to include CR/DR adjustments |
| [`ret_purchase_order_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_purchase_order_model.php#L8749-L8753) | 8749-8753 | Added CR/DR note JOIN with status filter |
| [`ret_purchase_order_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_purchase_order_model.php#L1816-L1821) | 1816-1821 | Updated purchase_type CASE statement to include Sales Return |
| [`ret_purchase_order_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_purchase_order_model.php#L1840) | 1840 | Added stock_type filter condition |
| **[`ret_dashboard_api_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_dashboard_api_model.php#L2565-L2614)** | **2565-2614** | **Fixed rate cut P&L to fetch previous non-zero bullion rates automatically** |
| **[`ret_dashboard_api_model.php`](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_dashboard_api_model.php#L1546-L1548)** | **1546-1548** | **Simplified delayed PO logic to strictly use delivery date vs current date** |

---

**Document Version**: 1.3  
**Last Updated**: 2026-02-11  
**Created By**: Development Team
