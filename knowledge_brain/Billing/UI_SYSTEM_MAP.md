# Billing Module — UI & System Map

> **AI-Optimized**: Map of views, DOM elements, and critical frontend integrations.

---

## 8a. View Files

| View File                                              | Purpose                    | Key DOM Elements                          | Included JS / CSS                                  |
| ------------------------------------------------------ | -------------------------- | ----------------------------------------- | -------------------------------------------------- |
| `admin/application/views/billing/form.php`             | Main Retail Invoice screen | `#bill_form`, `#product_id`, `#items_div` | `ret_billing.js`, Core JS                          |
| `admin/application/views/billing/list.php`             | Billing List DataTables    | `#table`, `.btn-edit`                     | `ret_billing.js`, DataTables                       |
| `admin/application/views/billing/billsplit.php`        | Split Billing UI           | Split item rows                           | `ret_billing.js`                                   |
| `admin/application/views/billing/paymentmode_edit.php` | Edit Payment Modes UI      | Payment fields                            | `ret_billing.js`                                   |
| `admin/application/views/billing/print/`               | Print Formats Directory    | Tables, Invoice headers                   | Print CSS (`bill_format_2`, `thermal_print`, etc.) |
| `admin/application/views/billing/issueReceipt/`        | Issue & Receipt Views      | `issueForm`, `receiptForm`                | `ret_billing.js`                                   |
| `admin/application/views/billing/service_bill/`        | Service/Repair Bill Views  | `form.php`, `list.php`                    | `ret_billing.js`                                   |
| `admin/application/views/billing/advance_transfer/`    | Advance Transfer Views     | Transfer forms                            | `ret_billing.js`                                   |

## 8b. DOM Elements & Hidden Fields

**Critical Note:** The Billing UI (`form.php`) relies heavily on hidden fields (over 380+) to maintain state between the frontend (JavaScript) and backend form submissions. If you add or modify a UI field that needs to be sent to the backend, you must ensure the corresponding hidden field exists and is updated via JS.

### Critical Hidden States (`#bill_form`)

- `b_type`: Bill Type (defines the flow path in `billing()` mega-method)
- `act_type`: Action Type ('save', 'edit', 'update', 'delete', 'cancell')
- `bill_cus_id`: Internal Customer ID
- `bill_emp_id`: Billed By Employee ID

### Financial Calculation State Fields

- `sub_tot_hdn`: Subtotal before tax/discount
- `total_tax_hdn`: Total tax amount
- `overall_dis_amt_hdn`: Total discount amount
- `grand_total_hdn`: Final Grand Total
- `receipt_amount`: Amount paid so far
- `balance_amt`: Remaining amount to be paid

### Component-Specific Fields

- **Advances**: `old_adv_adj_hdn`, `new_adv_adj_hdn`
- **Wallets**: `wallet_amt_hdn`
- **Gift Cards**: `gift_used_amt_hdn`
- **Chit/Schemes**: `chit_adj_amt_hdn`
- **Old Metal/Purchases**: `pur_tot_amt`

---

## 8c. UI Workflows

### The Add/Scan Item Flow

1. User enters tag ID/barcode, OR selects product/design and enters weight/pieces.
2. User tabs out or clicks Add.
3. JavaScript hooks into event (e.g., `calculateSaleBillRowTotal()`).
4. Values are retrieved from input fields.
5. Gross weight, loss weight, net weight, stones, MC, VA, taxes are calculated.
6. The row is added to the DOM list.
7. Hidden fields tracking totals (`sub_tot_hdn`, etc.) are updated.
8. The "Payment" section's total/balance UI updates.

### The Payment Collection Flow

1. "Grand Total" exists as an accumulator.
2. User provides combinations of payment methods (Cash, Card, UPI, Advance, Scheme, Old Metal).
3. For each adjustment, JS updates the relevant inputs and reduces `balance_amt`.
4. When `balance_amt` <= 0, the form submission is enabled.
5. Form POSTs to `billing()` (`act_type`='save', `b_type`=type).

### The Editing Flow

1. User clicks Edit from List view.
2. Backend calls `billing('edit', ID)`.
3. Backend retrieves bill data, items, payment data, etc.
4. The page rendered pushes all existing data into the hidden fields and UI form elements.
5. In JS, `$(window).load` runs specific initialization to recalculate balances and prep UI.
6. Changes happen via UI.
7. Save clicks update `act_type`='update' and POST to `billing()`.

---

## 8d. Critical JS Events & Listeners

- **Tag Scan Blur**: Fetches tag details via AJAX (`admin_ret_tagging` cross-module call), populates line item.
- **Product Select Change**: Triggers load of related designs/purities.
- **Customer Phone Blur**: Triggers AJAX lookup of Customer Details (`getSearchCustomer()`), populates Name, Address, PAN, Schemes.
- **Save Button Click**: Triggers massive validation function `check_mc_va_limit()` before allowing submission to ensure no limit breaches.
