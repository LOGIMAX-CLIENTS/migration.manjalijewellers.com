# Billing Module — Data Flow

> **Round 1** | Key user flows traced end-to-end

---

## Flow 1: CREATE — Save New Bill (Normal Sale)

### Step-by-step trace

1. **JS Init** (`ret_billing.js` L360+): On `case "add"` → calls `get_employee()`, `get_ActiveUOM()`, `get_ActiveProduct()`, `getStoneRateSettings()`, `getOtherChargesDetails()`, `get_branches()`
2. **User selects branch** → JS fetches metal rates via AJAX to controller
3. **User searches estimation/tag** → JS calls controller `getEstimationDetails()` (L5249-5312) → model `getEstimationDetails()` (L2745-4439) — massive 1,694-line method
4. **Estimation populates sale row** → JS populates item fields (product, design, purity, weight, MC, VA, etc.)
5. **JS calculates row total** → `calculateSaleBillRowTotal()` function in JS — applies:
    - Rate per gram × net weight
    -   - Wastage (% or flat)
    -   - MC (making charges) — per gram or flat
    -   - Stone value
    - = Item cost before tax
    -   - CGST + SGST (or IGST) based on tax group
    - = Item total
6. **User adds payment** → Cash, Card, Cheque, Net Banking, Chit Utilization, Gift Voucher, Advance Adjustment
7. **JS form submit** → POST to `billing('save')` inside the `billing()` mega-method (L218-5189)
8. **Controller `billing('save')` path**:
    - Validates `form_secret` (duplicate prevention)
    - Checks tag availability via `get_tag_status()`
    - Gets day closing data via `getBranchDayClosingData()`
    - Gets metal rate via `get_branchwise_rate()`
    - Generates bill number via `code_number_generator()`
    - **DB Transaction starts**: `$this->db->trans_begin()`
    - Inserts `ret_billing` header record
    - For each sale item:
        - Inserts `ret_bill_details` record
        - Generates ref_no via `generateRefNo()`
        - Inserts `ret_billing_item_stones` for each stone
        - Inserts `ret_bill_other_metals` for other metals
        - Updates `ret_estimation_items.purchase_status = 1`
        - Updates `ret_estimation.estbillid`
        - Updates `ret_taging.tag_status = 1` (sold)
        - Inserts `ret_taging_status_log` (sale log)
    - For each payment:
        - Inserts `ret_billing_payment` record
    - For purchase items (old metal):
        - Handles purchase return logic
    - For advance adjustments:
        - Inserts `ret_billing_advance` records
    - Creates journal entries via `account_model`
    - **DB Transaction complete**: `$this->db->trans_complete()`
9. **Response** → JSON with `{success: true/false, bill_id, bill_no}`

### Tables written (in order)

1. `ret_billing` — bill header
2. `ret_bill_details` — line items
3. `ret_billing_item_stones` — stone details per item
4. `ret_bill_other_metals` — other metals per item
5. `ret_estimation_items` — update purchase_status
6. `ret_estimation` — link estbillid
7. `ret_taging` — mark tag as sold
8. `ret_taging_status_log` — movement log
9. `ret_billing_payment` — payment records
10. `ret_billing_advance` — advance adjustments
11. `ret_journal` — accounting journal entries
12. `customer` — PAN/Aadhaar update if provided

---

## Flow 2: EDIT — Update Existing Bill

### Step-by-step trace

1. **Page load** → `billing('edit', $id)` path in controller
2. **Controller loads**:
    - `getBillingDetails($bill_id)` (model L824-978) — bill header + customer + branch
    - `getOtherEstimateItemsDetails($bill_id)` (model L1255-1987, 732 lines) — all line items
    - `get_billing_advance_details($bill_id)` — advance adjustments
    - `getPaymentDetails($bill_id)` — payment records
    - `get_bill_stone_details($bill_id)` — stone details
3. **View renders** → `billing/form.php` with pre-populated data
4. **JS edit init** (`ret_billing.js` L237+) → `case "edit"` — shows/hides sections based on `bill_type`
5. **User modifies** → Changes items, payments, etc.
6. **Submit** → `billing('update')` path
7. **Controller `billing('update')`**:
    - ⚠️ **DELETE-then-INSERT pattern** for child records:
        - Deletes existing `ret_bill_details` for the bill
        - Deletes existing `ret_billing_payment` for the bill
        - Deletes existing `ret_billing_item_stones`
        - Re-inserts all child records from current form state
    - Updates `ret_billing` header
    - Refreshes tag statuses, estimation links, journal entries

### ⚠️ Data Loss Risk

- **DELETE-then-INSERT** means if the update fails mid-way, child records may be lost
- Fields that exist in SELECT but possibly not in UPDATE need verification
- Original `created_by` / `created_time` on child records are lost on update

---

## Flow 3: DELETE — Cancel Bill

### Step-by-step trace

1. **Trigger** → JS calls `cancel_bill()` controller (L7581-8001, ~420 lines)
2. **Method**: POST
3. **Controller `cancel_bill()`**:
    - Verifies OTP if required (`verify_otp_for_billcancel`)
    - Gets bill details via `get_bill_detail($bill_id)`
    - **Reversal logic**:
        - For each `ret_bill_details` item:
            - If `tag_id` exists → `ret_taging.tag_status = 0` (available again)
            - Inserts `ret_taging_status_log` with reverse entry
            - Updates `ret_estimation_items.purchase_status = 0` (un-bill)
            - Updates `ret_estimation.estbillid = NULL`
            - If non-tag item → reverses `ret_non_tag_item` stock
        - Reverses all payment journal entries
        - Updates `ret_billing.is_cancelled = 1` (soft delete)
    - ⚠️ **Does NOT delete** the bill — marks as cancelled

### Cleanup Tables

| Table                   | Action                       |
| ----------------------- | ---------------------------- |
| `ret_billing`           | Update `is_cancelled = 1`    |
| `ret_taging`            | Update `tag_status = 0`      |
| `ret_taging_status_log` | Insert cancel log            |
| `ret_estimation_items`  | Update `purchase_status = 0` |
| `ret_estimation`        | Update `estbillid = NULL`    |
| `ret_non_tag_item`      | Reverse stock changes        |
| `ret_journal`           | Reverse journal entries      |
| `ret_billing_payment`   | No delete — kept for audit   |

---

## Flow 4: PRINT — Invoice Generation

1. **URL** → `/admin_ret_billing/billing_invoice/{id}`
2. **Controller `billing_invoice()`** (L5648-5683):
    - Gets bill details, company details
    - Loads appropriate print view from `views/billing/print/`
3. **Uses DomPDF** for PDF generation

---

## Flow 5: SPLIT SAVE — Bill Split

1. **URL** → `billing('split_save')`
2. **Controller** (L303+):
    - ⚠️ Uses raw `$_POST['billing']` (L307)
    - Each split creates a separate `ret_billing` record with `is_bill_split = 1` and shared `bill_split_ref_id`
    - Each split gets its own bill number and payment records
    - Uses `$this->db->trans_begin()` for atomicity

---

## Flow 6: ISSUE Voucher

1. **URL** → `/admin_ret_billing/issue/{type}/{id}`
2. **Controller `issue()`** (L5738-6521, 783 lines):
    - Types: add, list, save, edit, update
    - Issue = cash/asset going OUT (expense, loan, purchase payment)
    - Creates payment records + journal entries
    - DataTable list via `ajax_getIssuetist()`

---

## Flow 7: RECEIPT Voucher

1. **URL** → `/admin_ret_billing/receipt/{type}/{id}`
2. **Controller `receipt()`** (L6523-7508, 985 lines):
    - Types: add, list, save, edit, update
    - Receipt = cash/asset coming IN (advance, credit collection, loan repayment)
    - Creates payment records + journal entries
    - DataTable list via `ajax_getReceiptlist()`

---

## Flow 8: SERVICE Bill

1. **URL** → `/admin_ret_billing/service_bill/{type}`
2. **Controller `service_bill()`** (L9133-9500, 367 lines):
    - Types: add, list, save, edit, update
    - For repair/service work billing
    - Uses `ret_billing_service` and `ret_billing_service_items` tables
    - Generates service bill number via `service_bill_number_generator()`

---

## Flow 9: The `billing()` MEGA-Method Map (L218-5645)

The `billing($type, $id)` method in `admin_ret_billing` controls the entire invoice lifecycle. It relies on a giant `switch` statement on `$type`.

### Switch Cases

| Case / Path        | Line numbers | Description                                                                          |
| ------------------ | ------------ | ------------------------------------------------------------------------------------ |
| `add`              | L360-1113    | Loads master data (products, schemes, employees), prepares UI for new bill.          |
| `save`             | L1115-3595   | **[CRITICAL]** The 2000+ line save routine. Handles 15+ sub-types of bills.          |
| `edit`             | L3601-4475   | Loads a bill into the UI. Fetches estimation, payment, tag, non-tag, old metal data. |
| `update`           | L4482-5526   | **[CRITICAL]** Updates an existing bill using a DELETE-then-INSERT strategy.         |
| `delete`           | L5532-...    | Cancels a bill, reverses inventory impacts, deletes tag statuses.                    |
| `cancell`          | ...-5573     | Reverses transaction (Soft Cancel).                                                  |
| `ajax`             | L223-289     | Serves the DataTables list for regular bills.                                        |
| `ajaxapprovallist` | L293-298     | Serves DataTables for bills pending OTP/Manager approval.                            |
| `split_save`       | L303-356     | Saves a single original bill as multiple split invoice records.                      |
| `default`          | L5575-5644   | Renders the standard list view (UI).                                                 |

### The `save` Action Anatomy (L1115-3595)

Inside `case 'save'`, the routing relies heavily on `$b_type = $_POST['b_type']` to determine which subsets of data need inserting.

1. **Bill Type Branching**
    - `1`: Standard Sale
    - `2`: Old Purchase
    - `3`: Supplier Bill
    - `4` & `5`: Chit/Scheme
    - `7`: Order Advance

2. **Payment Processing Blocks**
    - `cash_amount` -> `ret_billing_payment`
    - `card_amount` -> Card details + `ret_billing_payment`
    - `net_banking` -> NEFT/RTGS details
    - `wallet_amt_hdn` -> Wallet Deductions
    - `gift_used_amt_hdn` -> Gift Voucher logic
    - `chit_adj_amt_hdn` -> Scheme adjustments

3. **Journal Entries**
    - After the primary row insertions, `create_biling_journal_data()` is called (e.g., L3308) to push accounting entries based on the specific `b_type`.

### Refactoring / Debugging Warning

Due to its sheer size, modifying the `save` or `update` block is extremely high-risk. Any modification to how a `tax_amount` or `discount` is saved must be checked against:

1. The Sale Path (`b_type=1`)
2. The Purchase Path (`b_type=2`)
3. The `edit`/`update` roundtrip (to ensure the modified field isn't lost on edit).

---

## Flow 10: E-INVOICE Generation

1. **URL** → `/admin_ret_billing/generateEinvoice/{billId}`
2. **Controller `generateEinvoice()`** (L10269-10282):
    - Fetches bill data + customer data
    - Calls `createGSPEInvoice()` (L10284-10333) to compose the GSP payload
    - Payload sections: `getTranDtls()`, `getDocDtls()`, `getSellerDtls()`, `getBuyerDtls()`, `getValDtls()`, `getItemList()`
    - Calls `createAuthToken()` (L10535-10593) → GSP API auth
    - Calls `generateGSPEInvoice()` (L10595-10675) → submits to GSP, gets IRN
    - Calls `updateIRNDetails()` (L10693-10702) → stores IRN + QR code in DB
3. **Tables touched**: `ret_billing` (update IRN, QR, e-invoice data), reads `branch`, `company_details`

---

## Flow 11: PAYMENT MODE EDIT

1. **URL** → `/admin_ret_billing/paymentmode_edit/{type}`
2. **Controller `paymentmode_edit()`** (L8306-8677, 371 lines):
    - Types: list, edit, save
    - Loads existing payment records for a bill
    - User can change payment split (Cash↔Card↔NB↔CHQ)
    - **Save path**: DELETE existing payments → re-INSERT new payment mix
    - Also re-creates journal entries
3. **⚠️ Risk**: Same DELETE-then-INSERT pattern as the update flow — partial failure loses payment records

---

## Flow 12: BANK LEDGER TRANSFER

1. **URL** → `/admin_ret_billing/bank_ledger_transfer/{type}`
2. **Controller `bank_ledger_transfer()`** (L12066-12145):
    - Types: list, save
    - Transfers cash balance to a bank ledger account
    - Creates journal entry for the transfer
3. **Tables**: `ret_journal` (debit Cash, credit Bank)

---

## Flow 13: CASH COLLECTION

1. **URL** → `/admin_ret_billing/cash_collection/{type}`
2. **Controller `cash_collection()`** (L11856-12031):
    - Types: add, list, ajax_list, print, save, ajax
    - Records end-of-day cash denomination counts
    - Header → `ret_cash_collection`
    - Detail rows → `ret_cash_collection_details` (one row per denomination)
3. **Business Logic**: `total_amount = sales_amount + opening_balance`; compared against `cash_on_hand` (declared sum)
