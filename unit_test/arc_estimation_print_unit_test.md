# Unit Test: ARC Estimation Print Template Validation

## Overview
This document outlines the test cases for the new ARC-specific estimation print template (`est_print_arc.php`). The goal is to ensure data accuracy, layout consistency, and correct conditional logic for Value Addition (VA) display.

## Test Cases

### 1. Template Routing
- **Objective:** Verify that the system uses the `est_print_arc` view.
- **Action:** Generate an estimation PDF/Print view.
- **Expected Result:** The printed output matches the ARC layout structure defined in `views/estimation/print/est_print_arc.php`.

### 2. Header Information
- **Objective:** Ensure customer and transaction metadata are accurate.
- **Checkpoints:**
  - Estimate Number matches the generated ID.
  - Customer Name, Village, and Mobile are displayed (e.g., "John Doe / Village Name / 9876543210").
  - Date and Time reflect the estimation creation timestamp.
  - Current Metal Rates (Gold 22K, 18K, Silver) are visible and correct.

### 3. Item List Calculation & Formatting
- **Objective:** Validate item row data.
- **Checkpoints:**
  - Product Name and Sub-Design are concatenated correctly.
  - Gross Weight and Net Weight are displayed with 3 decimal places.
  - Amount is formatted using Indian Currency format (e.g., "1,23,456.78").

### 4. VA Column Conditional Logic (Critical)
- **Objective:** Test the logic for displaying Value Addition.
- **Scenario A: Net Weight >= 1.000g**
  - **Action:** Add an item with Net Wt = 1.050g and Wastage = 12%.
  - **Expected Result:** The VA column should show "12%".
- **Scenario B: Net Weight < 1.000g**
  - **Action:** Add an item with Net Wt = 0.500g.
  - **Expected Result:** The VA column should show the absolute VA amount (Wastage Value + MC), rounded to 0 decimals.

### 5. Calculation Logic (Old Metal & Adjustments)
- **Objective:** Ensure the final payable amount is correct.
- **Scenario:** Sales Total: 50,000, Old Metal: 10,000, Advance: 5,000.
- **Expected Result:**
  - Sales section shows 50,000.
  - Purchase (Old Metal) section shows 10,000.
  - Advance section shows 5,000.
  - Grand Total shows 35,000.

### 6. Edge Cases
- **HUID Display:** Verify that HUID and HUID2 are displayed correctly if available, separated by a comma.
- **Partial Tag Split:** Verify that "PARTLY STONE" or "PARTLY" weight differences are shown when tag weights differ from estimation weights.
- **Tax Calculation:** Verify CGST/SGST (intra-state) vs IGST (inter-state) based on branch and customer location.
