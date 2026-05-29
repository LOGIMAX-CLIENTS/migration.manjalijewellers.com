# Unit Test: Customer Deletion Validation

## Overview
This document outlines the test cases for the Customer Deletion functionality, specifically focusing on the new validation logic that prevents deleting customers with active dependencies and the replacement of the error modal with a toaster notification.

## Test Scope
- **Feature**: Customer Deletion (`Admin_customer::ajax_check_delete`)
- **UI Component**: Toaster Notification vs Modal
- **Logic**: Dependency Check (`Customer_model::check_customer_dependencies`)

## Test Cases

### 1. Dependency Check Logic (Backend)
- **Objective**: Verify that the system correctly identifies all dependency types.
- **Pre-requisites**: Access to database or UI to create records.

| Case ID | Scenario | Setup Data | Expected Result | Pass/Fail |
|---------|----------|------------|-----------------|-----------|
| TC-01 | No Dependencies | Create new customer, no related transactions. | `status: true`, Message: "Are you sure..." | |
| TC-02 | Active Scheme Account | Create customer, add active scheme account. | `status: false`, Message includes "Active Scheme Accounts" | |
| TC-03 | Billing Records | Create customer, add a bill (`bill_status != -1`). | `status: false`, Message includes "Billing Records" | |
| TC-04 | Customer Orders | Create customer, add an order (`order_status != -1`). | `status: false`, Message includes "Customer Orders" | |
| TC-05 | Estimation Records | Create customer, add an estimation. | `status: false`, Message includes "Estimation Records" | |
| TC-06 | Gift Vouchers | Create customer, add gift card purchase. | `status: false`, Message includes "Gift Voucher / Card Purchases" | |
| TC-07 | Multiple Dependencies | Add Scheme Account AND Bill. | `status: false`, Message includes BOTH dependencies | |

### 2. UI/UX Interaction (Frontend)
- **Objective**: Verify the replacement of the Modal with the Toaster for errors.

| Case ID | Scenario | Action | Expected Behavior | Pass/Fail |
|---------|----------|--------|-------------------|-----------|
| UI-01 | Attempt Delete (Error) | Click Delete on customer from TC-07. | 1. **No Modal** appears.<br>2. **Red Toaster** appears top-right.<br>3. Toaster message lists dependencies.<br>4. Toaster **stays visible** (Sticky) until closed. | |
| UI-02 | Attempt Delete (Success) | Click Delete on customer from TC-01. | 1. **Confirmation Modal** appears.<br>2. Message asks "Are you sure!".<br>3. Delete button is visible. | |
| UI-03 | Confirm Delete | Click "Delete" in confirmation modal. | Customer is deleted from list. | |
| UI-04 | Cancel Delete | Click "Cancel" in confirmation modal. | Modal closes, Customer remains. | |

## Automated Unit Test (Optional)
A PHP controller `UnitTest_Customer.php` can be created to run the dependency logic checks programmatically.
**Usage**: `[BaseURL]/index.php/UnitTest_Customer`
