---
name: requirement-generator
description: Supporting skill for /get-requirement workflow — module keyword dictionary, task type classification, role mappings, and worked examples for calibrating output quality.
---

# Requirement Generator Skill

This skill supports the `/get-requirement` workflow by providing reference data and examples that calibrate AI output quality and consistency.

> **SAFETY REMINDER**: This skill is used by non-technical support team members. When executing the `/get-requirement` workflow: use ONLY `view_file` (read files) and `grep_search` (search across files — bundled in IDE, no installation needed). NEVER use `run_command` — the support team does not have terminal tools installed. If any step fails, skip it silently and continue.

---

## 1. Module Keyword Dictionary

Use this table to map keywords from the task description to the correct module brain. Match against the **Keywords** column (case-insensitive). If multiple modules match, pick the one with the strongest keyword match.

| Module | Brain Path | Keywords |
|---|---|---|
| **Billing** | `knowledge_brain/Billing/` | bill, billing, invoice, sale, sell, sold, payment mode, credit sale, credit bill, POS, advance, discount, EDA, no-2, bill cancel, cash collection, denomination, service bill, issue voucher, receipt voucher, ledger transfer, advance transfer, bill split, bill number, e-invoice, IRN, TCS |
| **Tagging** | `knowledge_brain/Tagging/` | tag, tagging, barcode, label, HUID, tag status, tag delete, tag print, tag stone, purity, QR code, tag certification, section, size, OTP delete |
| **Estimation** | `knowledge_brain/Estimation/` | estimate, estimation, valuation, pricing, rate card, estimation print, stone estimation, karigar estimation, making charge estimation, VA estimation |
| **Ret_Reports** | `knowledge_brain/Ret_Reports/` | report, abstract, statement, daybook, ledger, cash abstract, stock report, sales report, purchase report, tax report, GST report, missing tag, aging report, daily summary |
| **Lot** | `knowledge_brain/Lot/` | lot, lot number, lot balance, lot deduction, lot issue, lot return, GRN, goods receipt, lot creation, karigar lot |
| **Branch Transfer** | `knowledge_brain/Branch Transfer/` | branch transfer, BT, inter-branch, stock transfer, transfer approval, transfer receive, non-tag transfer, NT transfer |
| **Catalog_Inventory** | `knowledge_brain/Catalog_Inventory/` | catalog, product, design, sub-design, category, section, purity, UOM, karigar master, metal type, stone type, HSN, product master, size master |
| **customer** | `knowledge_brain/customer/` | customer, CRM, loyalty, customer group, customer search, PAN, Aadhaar, KYC, customer import, wallet |
| **purchase** | `knowledge_brain/purchase/` | purchase, PO, purchase order, GRN, vendor, supplier, karigar, purchase approval, purchase return, karigar material issue |
| **scheme** | `knowledge_brain/scheme/` | scheme, chit, savings, gold scheme, scheme account, scheme payment, chit collection, chit maturity, scheme close, scheme transfer |
| **section_transfer** | `knowledge_brain/section_transfer/` | section transfer, inter-section, section move |
| **Stock_Issue** | `knowledge_brain/Stock_Issue/` | stock issue, karigar issue, karigar return, material issue, stock return |
| **Sales_Transfer** | `knowledge_brain/Sales_Transfer/` | sales transfer, deemed sale, inter-GST, GST transfer, entity transfer, cross-branch sale |
| **old_metal_process** | `knowledge_brain/old_metal_process/` | old metal, old gold, melting, refining, metal process, exchange metal |
| **customer_order** | `knowledge_brain/customer_order/` | order, customer order, repair, job order, repair order, custom order, order delivery, order advance |
| **payment** | `knowledge_brain/payment/` | payment, receipt, collection, chit payment, payment receipt, payment voucher, receipt number |
| **account** | `knowledge_brain/account/` | account, ledger, journal, accounting, account head, lump sum, maturity, account number |
| **retail_dashboard** | `knowledge_brain/retail_dashboard/` | dashboard, home page, overview, summary widget, KPI |
| **retail_settings** | `knowledge_brain/retail_settings/` | settings, config, setup, configuration, retail settings, enable, disable, toggle, feature flag |
| **employee** | `knowledge_brain/employee/` | employee, staff, user, RBAC, role, permission, access, login, branch access, employee master |
| **masters** | `knowledge_brain/masters/` | master, branch master, metal rate, payment mode master, tax group, company, profile |
| **other_inventory** | `knowledge_brain/other_inventory/` | packaging, other inventory, non-metal, gift, cover, box, wrapper |
| **chit_dashboard** | `knowledge_brain/chit_dashboard/` | chit dashboard, chit overview, chit KPI |
| **chit_reports** | `knowledge_brain/chit_reports/` | chit report, scheme report, collection report, passbook, chit statement |
| **chit_collection_app** | `knowledge_brain/chit_collection_app/` | collection app, mobile collection, field collection |
| **chit_services** | `knowledge_brain/chit_services/` | chit service, scheme service, sync service |
| **chit_settings** | `knowledge_brain/chit_settings/` | chit settings, scheme settings, chit config |

> **Disambiguation rule**: If keywords match 2+ modules, prefer the module where the PRIMARY action happens. Example: "show card_date in payment report" → Primary is `Ret_Reports` (report change), Secondary is `Billing` (data source).

---

## 2. Task Type Classification

| Task Type | Signals in Description | Example Phrases |
|---|---|---|
| **New Feature** | "add new", "create", "implement", "introduce", "build" | "Add a new approval workflow for..." |
| **Enhancement** | "improve", "modify", "update", "change", "add field", "include", "show" | "Add card_date field to payment mode" |
| **Bug Fix** | "fix", "broken", "not working", "incorrect", "wrong", "missing", "error" | "Credit outstanding shows wrong amount" |
| **Report Change** | "report", "add column", "show in report", "export", "print", "display in" | "Show section-wise stock in daily report" |
| **Configuration** | "settings", "toggle", "enable", "disable", "config" | "Add a setting to control OTP for..." |
| **Integration** | "API", "sync", "webhook", "external", "third-party", "e-invoice", "SMS" | "Integrate POS machine payment callback" |

---

## 3. User Role Mappings

| Role | Who They Are | Typical Tasks |
|---|---|---|
| **Counter Staff** | Front-desk billing operator | Billing, estimation, customer handling, tag scanning |
| **Branch Manager** | Branch head with approval power | Approvals, day closing, branch reports, OTP verification |
| **Admin** | System administrator | Settings, user management, configuration, access control |
| **Accountant** | Financial/accounting staff | Ledger, journal entries, credit management, reconciliation |
| **IT/Support** | Technical support | Debugging, configuration, deployment, data fixes |
| **Karigar/Craftsman** | Workshop staff | Stock issue receipt, LOT tracking, material return |
| **Store Manager** | Inventory/stock controller | Stock reports, branch transfer, section transfer, catalog |
| **Management** | Business owner/director | Dashboard, KPI reports, P&L, compliance, audit |
| **Customer** | End customer (app/portal) | Order tracking, scheme payments, wallet |
| **Auditor** | Internal/external auditor | Tax reports, compliance checks, GST filing data |

---

## 4. Worked Examples

### Example 1: Enhancement — Add a Date Field to Billing Payment

**Input (raw task description)**:
> "We need to track when a credit/debit card payment was actually processed at the terminal. Currently billing only captures card number and type but not the transaction date. The card machine gives a date on the receipt slip. We want this date field added in billing and visible in the bill print copy."

**Expected Output**:

```
📋 CATEGORY
═══════════════════════════════════════════
Billing — Payment Mode
Type: Enhancement


📋 TASK
═══════════════════════════════════════════
Add card transaction date (card_date) field to billing payment capture and bill print


📋 USER STORY
═══════════════════════════════════════════
As a **Counter Staff**, I want to **enter the card machine transaction date when recording a card payment**, so that **the actual card processing date is accurately tracked for payment reconciliation with the bank**.

As a **Branch Manager**, I want to **see the card transaction date on the printed bill copy**, so that **payment records match the card terminal receipts during daily reconciliation**.


📋 OBSERVATION
═══════════════════════════════════════════
### Current System Behavior
The billing form currently captures card payments through a payment modal that collects:
- `card_type` (CC/DC) via dropdown
- `card_no` (card number) via text input
- `payment_amount` via number input
- `bank` (issuing bank) via text
- `ref_number` (transaction reference) via text

There is NO date field for the card transaction. The only date associated is `bill_date` which comes from the day closing system (RULE-BIL-002).

### Relevant Existing Features
- **RULE-BIL-027**: Multi-Payment Mode Support — each bill can have simultaneous Cash + Card + Cheque + NB payments, stored in `ret_billing_payment` table
- **RULE-BIL-002**: Bill date comes from day closing, NOT from actual transaction time
- The payment modal is in `admin/application/views/billing/form.php` (~189KB main form) and also in `paymentmode_edit.php` (22KB — for editing payments after save)

### Technical Context (for dev team)
- **Controller**: `admin_ret_billing.php` — `billing('save')` L470-533 processes payment arrays
- **Model**: `ret_billing_model.php` — `getPaymentDetails()` fetches payment rows
- **JS**: `ret_billing.js` — payment modal handling, card section toggle
- **Views**: `form.php` (card payment section), `paymentmode_edit.php`, print templates in `views/billing/print/`
- **Tables**: `ret_billing_payment` — currently has `card_type`, `card_no`, `bank`, `ref_number` but NO `card_date` column

### Current Limitations / Gaps
- Card payments processed on a different date than the billing date have no way to track the actual processing date
- Bank reconciliation requires manually cross-referencing card terminal slips
- The `ret_billing_payment` table lacks a `card_date` column


📋 IMPACTS
═══════════════════════════════════════════
### Primary Module: Billing
| Component | File | What Changes |
|---|---|---|
| Controller | `admin_ret_billing.php` | Add `card_date` to payment save array, handle dd-mm-yyyy → yyyy-mm-dd conversion |
| Model | `ret_billing_model.php` | Return `card_date` in payment detail queries, format for display |
| JS | `ret_billing.js` | Add datepicker-enabled input to card payment modal |
| View - Form | `views/billing/form.php` | Add `card_date` input field in card payment section |
| View - Edit | `views/billing/paymentmode_edit.php` | Add `card_date` field in card payment edit section |
| View - Print | `views/billing/print/bill_format_*.php` | Display card_date in payment details section |
| DB Table | `ret_billing_payment` | Add `card_date DATE DEFAULT NULL` column |

### Cross-Module Impacts
| Affected Module | Why | What Changes | Risk Level |
|---|---|---|---|
| Ret_Reports | Payment reports may need to show card_date | Report queries that join ret_billing_payment | 🟡 Medium |
| Issue/Receipt | If issue/receipt also captures card payments | Same payment modal pattern | 🟢 Low |
| Service Bill | Service bills have separate payment table | May need same field in ret_service_bill_payment | 🟢 Low |

### Settings / Configuration
No new settings needed — card_date is always available when card payment is selected.

### Print / Report Impacts
All active bill print formats need to display card_date if present.


📋 ACCEPTANCE CRITERIA
═══════════════════════════════════════════
- [ ] AC-1: Card payment modal shows a datepicker field labeled "Card Date" when CC or DC payment type is selected
- [ ] AC-2: Card Date field defaults to the current bill date but allows manual override
- [ ] AC-3: Card Date is saved to `ret_billing_payment.card_date` in yyyy-mm-dd format
- [ ] AC-4: Card Date is displayed on the bill print copy in dd-mm-yyyy format next to card number
- [ ] AC-5: Payment mode edit screen shows the saved card_date and allows modification
- [ ] AC-6: Card Date field is hidden when payment type is not CC/DC (Cash, Cheque, NB)
- [ ] AC-7: If card_date is left empty, it defaults to bill_date (backward compatible)
- [ ] AC-8: Existing bills without card_date continue to display normally (NULL handling)
```

---

### Example 2: New Feature — Cross-GST Entity Sales Transfer

**Input (raw task description)**:
> "When our client has multiple branches under different GST registrations, they need to transfer stock from one GST entity to another. This should generate a B2B bill from the sending branch and receive as purchase/GRN at the receiving branch. We need a sales transfer module that handles tagged items, non-tagged items, old metal, and packaging materials."

**Expected Output**:

```
📋 CATEGORY
═══════════════════════════════════════════
Sales_Transfer — Inter-GST Entity Stock Movement
Type: New Feature


📋 TASK
═══════════════════════════════════════════
Implement Sales Transfer module for cross-GST entity stock movement with B2B billing and auto-GRN


📋 USER STORY
═══════════════════════════════════════════
As a **Store Manager**, I want to **transfer tagged and non-tagged stock from my branch to another branch under a different GST entity**, so that **inventory is moved with proper GST-compliant B2B documentation and the receiving branch gets automatic stock intake**.

As a **Accountant**, I want the **sales transfer to generate a B2B tax invoice with correct IGST/CGST+SGST based on inter-state or intra-state rules**, so that **GST filings are accurate and no manual invoice creation is needed**.


📋 OBSERVATION
═══════════════════════════════════════════
### Current System Behavior
The system currently has a **Branch Transfer** module (`knowledge_brain/Branch Transfer/`) that handles physical stock movement between branches. However, Branch Transfer:
- Does NOT generate tax invoices (it's an internal stock movement)
- Does NOT handle cross-GST entity scenarios
- Assumes all branches are under the same GSTIN
- Uses `ret_taging_status_log` for movement tracking

The **Billing** module can generate B2B invoices but has no automated flow triggered by stock transfers.

### Relevant Existing Features
- **Branch Transfer**: Tag status change, approval workflow, non-tag transfer — provides foundation for the transfer UI pattern
- **Billing RULE-BIL-018**: GST tax per line item (CGST/SGST for intra-state, IGST for inter-state)
- **Billing RULE-BIL-008**: Metal rate capture at billing time
- **Billing RULE-BIL-023**: Sales reference number generation
- **_SYSTEM/MODULE_DEPENDENCIES.md**: Branch Transfer and Billing are both depended on by Reports

### Technical Context (for dev team)
- **Controller**: `admin_ret_sales_transfer.php` (21KB — existing skeleton)
- **Model**: New model needed or extend existing
- **JS**: New JS file needed
- **Tables**: New `ret_sales_transfer`, `ret_sales_transfer_items` tables needed
- **Billing integration**: Must call `ret_billing_model` for B2B bill generation
- **Day closing**: Must respect `getBranchDayClosingData()` like all billing flows

### Current Limitations / Gaps
- No mechanism for stock movement between different GST entities
- Manual process: create bill at source, call receiving branch, they manually do GRN
- No tracking of "deemed sales" for tax purposes
- Non-tag and packaging items have no transfer mechanism outside Branch Transfer


📋 IMPACTS
═══════════════════════════════════════════
### Primary Module: Sales_Transfer
| Component | File | What Changes |
|---|---|---|
| Controller | `admin_ret_sales_transfer.php` | Full CRUD: initiate, approve, save (triggers billing), receive |
| Model | New: `ret_sales_transfer_model.php` | Transfer creation, item management, billing integration, GRN creation |
| JS | New: `ret_sales_transfer.js` | Transfer form, item selection, approval flow, rate calculation |
| Views | New: `views/sales_transfer/` | Form, list, approval list, item selection modals |
| DB Tables | New: `ret_sales_transfer`, `ret_sales_transfer_items` | Transfer header and line items |

### Cross-Module Impacts
| Affected Module | Why | What Changes | Risk Level |
|---|---|---|---|
| Billing | B2B bill auto-generation from transfer | Billing model called to create bill with bill_type 13/14 | 🔴 High |
| Tagging | Tag status update on transfer | tag_status change + status log entry | 🔴 High |
| Branch Transfer | Shared patterns, potential confusion with BT | UI must clearly differentiate BT vs Sales Transfer | 🟡 Medium |
| Ret_Reports | New reports for sales transfer tracking | New report queries and views | 🟡 Medium |
| Catalog | Product/section lookups for items | AJAX calls to catalog controller | 🟢 Low |
| Settings | New settings for enabling/configuring feature | New ret_settings entries | 🟢 Low |

### ⛔ Danger Zone Warning
This feature touches:
- **Financial transactions** (B2B bill generation — wrong amounts propagate to GST filings)
- **Tag status transitions** (tag moves between branches/entities)
- **Cross-module data flow** (4+ modules involved: Sales Transfer → Billing → Tagging → Reports)
Requires senior developer review on the billing integration and tax calculation logic.

### Settings / Configuration
- `enable_sales_transfer` — master toggle for the feature
- `sales_transfer_approval_required` — whether approval workflow is needed
- `sales_transfer_auto_grn` — whether receiving branch auto-receives

### Print / Report Impacts
- Sales Transfer B2B invoice print (new print template)
- Sales Transfer register report (new report)
- Impact on existing GST reports (B2B summary must include sales transfer invoices)


📋 ACCEPTANCE CRITERIA
═══════════════════════════════════════════
- [ ] AC-1: Store Manager can create a sales transfer selecting source branch and destination branch (different GST entity)
- [ ] AC-2: Transfer can include tagged items (scanned or searched), non-tag items, old metal, and packaging
- [ ] AC-3: On transfer save, a B2B tax invoice is auto-generated with correct GST (IGST for inter-state, CGST+SGST for intra-state)
- [ ] AC-4: Tag status changes to reflect transfer-in-progress at source branch
- [ ] AC-5: Receiving branch can view pending transfers and accept/receive them
- [ ] AC-6: On receive, items are added to receiving branch stock (tag branch updated, lot created for non-tag)
- [ ] AC-7: Sales Transfer invoice is visible in GST B2B report with correct GSTIN
- [ ] AC-8: Transfer cannot be initiated if day closing is not done for the source branch
- [ ] AC-9: Cancelled transfers revert all tag status and stock changes
- [ ] AC-10: Sales Transfer register report shows all transfers with status, dates, and amounts
```

---

### Example 3: Report Change — Add Column to Existing Report

**Input (raw task description)**:
> "In the daily cash abstract report, the client wants to see the customer's mobile number alongside the customer name. Currently it only shows name. This applies to all sections of the cash abstract — tagged sales, home bill, partly sale, and return sections."

**Expected Output**:

```
📋 CATEGORY
═══════════════════════════════════════════
Ret_Reports — Cash Abstract Report
Type: Report Change


📋 TASK
═══════════════════════════════════════════
Add customer mobile number column to all sections of the Cash Abstract report


📋 USER STORY
═══════════════════════════════════════════
As a **Branch Manager**, I want to **see the customer's mobile number in the Cash Abstract report**, so that **I can quickly contact customers for follow-up without opening individual bill records**.


📋 OBSERVATION
═══════════════════════════════════════════
### Current System Behavior
The Cash Abstract report displays daily billing summary sectioned by:
- Tagged Sales (item_type=0 with tag)
- Home Bill / Full Sale (item_type=2, tag_id IS NULL)
- Partly Sale (item_type=3)
- Sales Return (separate section)

Each section shows: Bill No, Customer Name, Product, Weight, Amount. Customer mobile is NOT currently included in the report query or display.

### Relevant Existing Features
- RULE-BIL-032 (item_type classification) controls which section each item appears in
- Customer data is in the `customer` table with `mobile` field
- The cash abstract query already JOINs `ret_billing` → `customer` for customer name

### Technical Context (for dev team)
- **Controller**: `admin_ret_reports.php` — cash abstract method
- **Model**: `ret_reports_model.php` — `getBillDetails()` and related query methods
- **Views**: `views/reports/cash_abstract.php` — display template
- **Tables**: `customer.mobile` — the field to add to SELECT

### Current Limitations / Gaps
- Customer mobile exists in DB but is not fetched in the cash abstract query
- No way to contact customer from the report without opening the bill


📋 IMPACTS
═══════════════════════════════════════════
### Primary Module: Ret_Reports
| Component | File | What Changes |
|---|---|---|
| Model | `ret_reports_model.php` | Add `c.mobile` to SELECT in cash abstract queries (all 4 sections) |
| View | `views/reports/cash_abstract.php` | Add "Mobile" column header and data cell in all section tables |
| Controller | `admin_ret_reports.php` | No change (data passes through from model to view) |

### Cross-Module Impacts
| Affected Module | Why | What Changes | Risk Level |
|---|---|---|---|
| None | Report is read-only, no writes | — | 🟢 None |

### Settings / Configuration
No new settings needed.

### Print / Report Impacts
- Cash Abstract print layout needs wider table or adjusted column widths to accommodate mobile number column
- If report is exported to Excel, mobile column should be formatted as text (not number) to preserve leading zeros


📋 ACCEPTANCE CRITERIA
═══════════════════════════════════════════
- [ ] AC-1: Cash Abstract report shows "Mobile" column after "Customer Name" in all sections (Tagged, Home Bill, Partly Sale, Return)
- [ ] AC-2: Mobile number displays as stored in customer master (with country code if present)
- [ ] AC-3: If customer has no mobile number, the cell shows "-" (not blank or error)
- [ ] AC-4: Print layout accommodates the new column without breaking page width
- [ ] AC-5: Excel export includes mobile column formatted as text
- [ ] AC-6: Existing reports without mobile data continue to work (backward compatible)
```

---

## 5. Output Quality Checklist

Before presenting the final output, verify:

- [ ] **Category** is specific (Module + Sub-feature), not just the module name
- [ ] **Task** is one clear sentence, not a paragraph
- [ ] **User Story** uses a real role from the role mapping table, not generic "user"
- [ ] **User Story** benefit is a BUSINESS reason, not "so that the feature works"
- [ ] **Observation** references specific brain files and rules when available
- [ ] **Observation** includes Technical Context with file paths for dev team
- [ ] **Impacts** table has at least Controller, Model, JS, View, DB rows
- [ ] **Cross-Module Impacts** checked against `_SYSTEM/MODULE_DEPENDENCIES.md`
- [ ] **Acceptance Criteria** are testable by a non-developer (clear pass/fail)
- [ ] **Acceptance Criteria** include edge cases (empty data, backward compatibility)
- [ ] No jargon in Category, Task, or User Story sections (business-friendly)
- [ ] File references and code details are ONLY in Observation and Impacts sections
