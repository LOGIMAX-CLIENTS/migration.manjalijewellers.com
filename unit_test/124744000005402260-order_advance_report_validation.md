# Unit Test: Order Advance Report Validation

## Overview
This document outlines the test cases for the **Order Advance Report** modifications.
The changes involve adding new columns (**Order Value**, **Total Advance**, **Advance Percentage**) and ensuring the **Grand Total** footer calculates correctly.

## Test Scope
- **Module**: Sales Report -> Order Advance Report
- **File**: `admin/assets/js/ret_reports.js` (DataTable Configuration)
- **View**: `admin/application/views/ret_reports/order_advance.php`

## Test Cases

### 1. UI Layout & Column Visibility
- **Objective**: Verify that the new columns appear in the report table.

| Case ID | Checkpoint | Expected Result | Pass/Fail |
|---------|------------|-----------------|-----------|
| UI-01 | **Order Value** Column | Column exists after "Rate Per Gram". Data is formatted as currency (e.g., `1,200.00`). | |
| UI-02 | **Total Advance** Column | Column exists after "Order Value". Data is formatted as currency. | |
| UI-03 | **Advance Percentage** Column | Column exists after "Total Advance". Data is formatted as `%`. | |
| UI-04 | **Status** Column | Column exists at the end of the table. | |

### 2. Data Accuracy & Logic
- **Objective**: Verify that the data in the new columns is calculated and displayed correctly.

| Case ID | Scenario | Logic to Verify | Expected Behavior |
|---------|----------|-----------------|-------------------|
| DL-01 | **Order Value** | `row.order_value` | Should match the total value of the order from the database/invoice. |
| DL-02 | **Total Advance** | `row.total_order_adv` | Should match the total advance paid for that order. |
| DL-03 | **Advance % Calculation** | `(Total Advance / Order Value) * 100` | Example: Order Value `1000`, Advance `200` -> **20.00%**. |
| DL-04 | **Advance % Formatting** | Color Coding | - **> 80%**: Displayed in **Blue**.<br>- **<= 80%**: Displayed in **Red**. |

### 3. Footer Totals (Grand Total)
- **Objective**: Verify that the footer row correctly sums up the visible data.

| Case ID | Column | Calculation Validation | Pass/Fail |
|---------|--------|------------------------|-----------|
| FT-01 | **Advance Amount** | Sum of all rows in 'Advance Amount' column. | |
| FT-02 | **Advance Amount** | Sum of all rows in 'Advance Weight' column. | |
| FT-03 | **Order Value** | Sum of all rows in 'Order Value' column. | |
| FT-04 | **Total Advance** | Sum of all rows in 'Total Advance' column. | |

## Automated Verification (Console Snippet)
You can run this snippet in the browser console while on the report page to verify the footer math matches the visible rows:

```javascript
// Verification Snippet
var api = $('#adv_list').DataTable();
var totalOrderValue = api.column(9).data().reduce((a,b) => (parseFloat(a.replace(/,/g,''))||0) + (parseFloat(b.replace(/,/g,''))||0), 0);
var footerOrderValue = parseFloat($(api.column(9).footer()).text().replace(/,/g,''));

console.log("Calculated Order Value: " + totalOrderValue.toFixed(2));
console.log("Footer Order Value:     " + footerOrderValue.toFixed(2));
console.log("Match: " + (totalOrderValue.toFixed(2) == footerOrderValue.toFixed(2)));
```
