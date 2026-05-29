# Component 1 — Payment CRM: Project Skeleton

> **Module:** Payment CRM  
> **Generated:** 2026-02-18  
> **Updated:** 2026-02-18 (Verified table names)
> **Total Code:** ~22,000 lines across 4 primary files

---

## 1. File Inventory

### 1.1 Controller
| File | Path | Lines | Size | Role |
|------|------|-------|------|------|
| `admin_payment.php` | `controllers/admin_payment.php` | 6,275 | 417 KB | Master controller — routes all payment actions via `payment($type)` switch |

**Key Methods (96 total):**
- `__construct()` — Loads 7 models, sets log_dir, company data
- `payment($type, $id)` — **MEGA-METHOD** (2,036 lines!). Switch cases: View, SaveAll, Save, Update, Delete, List, Ajax, Status, Edit_payment, Update_payment, general_advance
- `postdate_payment_form($type, $id)` — PDC payment form
- `postdate_payment($type, $id)` — PDC payment CRUD
- `ajax_form_data($id)` — Loads payment modes, banks, statuses for form dropdowns
- `ajax_account_detail($id, $date)` — Loads full scheme account detail
- `ajax_customer_schemes($id_customer)` — Returns customer's scheme accounts
- `generate_receipt_no($id_scheme, $branch)` — Sequential receipt number generator
- `generateInvoice($payment_no, $id_scheme_account)` — PDF invoice generation (DomPDF)
- `sendSMSMail($serviceID, $data, $subject, $type, $id)` — Notification dispatcher
- `amount_to_weight($to_pay)` — Amount-to-weight conversion
- `online_payment_list()`, `verify_payment_view()` — Online payment flows
- `update_pay_status()` — Payment approval status update
- `split_payment($id_payment)` — Payment splitting
- `weight_settlement()`, `update_settlement()` — Weight settlement flows
- `getMetalRateBydate()` — Metal rate lookup by date

### 1.2 Model
| File | Path | Lines | Size | Role |
|------|------|-------|------|------|
| `payment_model.php` | `models/payment_model.php` | 8,812 | 584 KB | All payment DB operations |

**Key Methods (277 total):**
- `paymentDB($type, $id, $pay_array)` — Central CRUD dispatcher (insert/update/delete/get) — Lines 732-840
- `get_paymentContent($id_scheme_account, $payment_edit_date)` — **THE BIG QUERY** (1,043 lines! 1400-2443). Returns complete scheme account data for the form
- `get_receipt_no($id_scheme, $branch)` — Gets latest receipt number
- `payment_list($id, $limit, $type)` — Payment listing query
- `payment_list_range($from_date, $to_date, $type, $limit, $date_type)` — Filtered listing
- `get_payment_status()` — Payment status master data
- `get_customer_schemes($id_customer)` — Customer scheme accounts
- `wallet_balance($id_customer)` — Wallet balance lookup
- `get_schgst($id_scheme_account)` — GST/scheme config for payment
- `postdated_paymentDB($type, $id, $pay_array)` — PDC payment CRUD
- `get_postpayment($id_scheme_account)` — Pending PDC payments
- `isAcnoAvailable($id_scheme_account)` — Account number status check
- `updateGroupCode($id_scheme_account)` — Lucky draw group code update
- `get_rptnosettings()` — Receipt number settings
- `insertData($data, $table)` — Generic insert helper
- `insertBatchData($data, $table)` — Batch insert helper
- `get_curPaid_insNo($id_scheme_account)` — Current installment number
- `isRateFixed($id_scheme_account)` — OTP rate fix check
- `updFixedRate($data, $id)` — Update fixed rate data

### 1.3 JavaScript
| File | Path | Lines | Size | Role |
|------|------|-------|------|------|
| `payment.js` | `assets/js/payment.js` | 6,627 | 355 KB | All client-side payment logic |

**Key Functions (304 total):**
- `load_account_detail(sch_id)` — AJAX call to `ajax_account_detail` → populates entire form
- `loadschemeaccountbyidcus(id_cus)` — AJAX call to `ajax_customer_schemes` → populates scheme account dropdown
- `insert_payment(post_data)` — Entry point: OTP check → `payment_success()`
- `payment_success(post_data, post_otp)` — Routes to `payment/save_all` OR `admin_payment/payment/general_advance`
- `update_payment(post_data)` — AJAX call to `payment/update/{id}`
- `load_paystatus_select()` — Loads payment modes, banks, statuses into dropdowns
- `get_payment_list(from_date, to_date, id_branch, ...)` — DataTable listing
- `calculatePaymentCost()` — Multi-mode payment cost calculation
- `calculate_payAmt(payable_amt)` — Payment amount + GST calculation
- `calc_walletbalance()` — Wallet redemption calculation
- PRE-SAVE VALIDATION (~lines 3111-3266) — Comprehensive client-side validation via checkbox change handler

### 1.4 Views
| File | Path | Lines | Size | Role |
|------|------|-------|------|------|
| `form.php` | `views/payment/form.php` | 1,338 | 66 KB | Payment entry form — customer search, scheme account, payment modes, amounts |
| `form_edit.php` | `views/payment/form_edit.php` | ~1,000 | ~50 KB | Payment edit form |
| `list.php` | `views/payment/list.php` | ~200 | ~10 KB | Payment listing page |
| `status_form.php` | `views/payment/status_form.php` | ~150 | ~7 KB | Payment status/log view |

---

## 2. Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────────┐
│                              BROWSER                                    │
│                                                                         │
│  form.php ──── payment.js ──── autocomplete / select2 / datepicker     │
│      │              │                                                   │
│      │         ┌────┴──────────────────────────────┐                   │
│      │         │ Client-Side Validation             │                   │
│      │         │ (weight, date, amount, branch,     │                   │
│      │         │  employee, GST tally, PAN check)   │                   │
│      │         └────┬──────────────────────────────┘                   │
│      │              │                                                   │
│      │         insert_payment() → OTP check                            │
│      │              │                                                   │
│      │         payment_success() ──→ AJAX POST                         │
└──────┼──────────────┼──────────────────────────────────────────────────┘
       │              │
       ▼              ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                        CONTROLLER (admin_payment.php)                   │
│                                                                         │
│  ┌─────────────────┐    ┌──────────────────┐    ┌────────────────────┐ │
│  │ ajax_form_data() │    │ ajax_account_    │    │ ajax_customer_    │ │
│  │ → modes, banks,  │    │ detail($id)      │    │ schemes($id)     │ │
│  │   statuses       │    │ → payment_model  │    │ → accounts list  │ │
│  │                  │    │   .get_payment   │    │   + wallet       │ │
│  │                  │    │    Content()     │    │                   │ │
│  └─────────────────┘    └──────────────────┘    └────────────────────┘ │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │  payment('SaveAll') — THE MAIN SAVE FLOW                        │  │
│  │                                                                  │  │
│  │  1. Parse POST (generic, cus_pay_mode, pdc, adv)                │  │
│  │  2. Server-side installment validation                          │  │
│  │  3. Calculate payment_amount per installment                    │  │
│  │  4. GST adjustment (exclusive/inclusive)                        │  │
│  │  5. Determine payment_mode (CSH/CC/DC/CHQ/NB/VCH/MULTI/ADV)   │  │
│  │  6. Wallet redemption calculation                               │  │
│  │  7. BEGIN TRANSACTION                                           │  │
│  │  8. FOR each installment:                                       │  │
│  │     a. GST + Metal Weight calculation                           │  │
│  │     b. Due type resolution (ND/PD/AD)                           │  │
│  │     c. Branch resolution (5-level cascade)                      │  │
│  │     d. Receipt number generation                                │  │
│  │     e. DigiGold benefit calculation                             │  │
│  │     f. Build pay_array (50+ fields)                             │  │
│  │     g. paymentDB("insert") → payment                           │  │
│  │     h. Insert payment_mode_details per mode                     │  │
│  │     i. Account number generation (if applicable)               │  │
│  │     j. Integration data sync                                    │  │
│  │     k. Payment status log                                       │  │
│  │     l. SMS/Email/WhatsApp notification                          │  │
│  │  9. COMMIT/ROLLBACK TRANSACTION                                 │  │
│  │  10. Return JSON (payid, type, payment_status)                  │  │
│  └──────────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────────┘
       │
       ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                          MODEL (payment_model.php)                       │
│                                                                         │
│  paymentDB("insert")                                                    │
│    → INSERT into payment                                                │
│    → Returns {status, insertID}                                         │
│                                                                         │
│  get_paymentContent($id_scheme_account)  ← THE BIG QUERY               │
│    → JOIN: scheme_account + scheme + customer + branch + payments       │
│    → Computes: paid_installments, allowed_dues, due_type,              │
│      eligible_weight, gst_data, wallet_balance, discount               │
│    → Returns: 80+ fields for form population                           │
│                                                                         │
│  insertData() / insertBatchData()                                       │
│    → Generic helpers for payment_mode_details, advance_utilized, etc.    │
└─────────────────────────────────────────────────────────────────────────┘
       │
       ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                              DATABASE                                    │
│                                                                         │
│  PRIMARY: payment (id_payment PK)                                     |
|  DETAIL:  payment_mode_details (id_payment FK)                     |
|  STATUS:  payment_status_message (id_payment FK)                      |
|  ACCOUNT: scheme_account (id_scheme_account FK)                   |
|  SCHEME:  scheme (id_scheme FK)                                    |
|  ADVANCE: general_advance_payment, ret_advance_utilized                |
|  VOUCHER: voucher_utilized                                        |
|  WALLET:  wallet_account, wallet_transaction                                                   |
|  INTEGRATION: customer_reg                                    |
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Constructor Dependencies

```php
// admin_payment.php __construct()
$this->load->model('payment_model');        // PAY_MODEL
$this->load->model('settings_model');       // SET_MODEL  
$this->load->model('account_model');        // ACC_MODEL
$this->load->model('customer_model');       // CUS_MODEL
$this->load->model('sms_model');            // SMS_MODEL
$this->load->model('log_model');            // LOG_MODEL
$this->load->model('mail_model');           // MAIL_MODEL
$this->load->model('digigold_modal');       // Direct (DigiGold)

// Libraries
require_once('TransactionRequestBean.php');  // Payment gateway
require_once('TransactionResponseBean.php'); // Payment gateway
require_once('hdfc.php');                    // HDFC integration
use Dompdf\Dompdf;                           // PDF generation
```

---

## 4. File-to-File Connections

| From | To | Via | Purpose |
|------|----|------|---------|
| `form.php` → `payment.js` | JS load | `<script>` | Client logic |
| `payment.js` → `admin_payment.php` | AJAX POST | `payment/save_all` | Save payment |
| `payment.js` → `admin_payment.php` | AJAX GET | `payment/get/ajax/account/{id}` | Load account detail |
| `payment.js` → `admin_payment.php` | AJAX GET | `payment/get/ajax/customer/account/{id}` | Load customer schemes |
| `payment.js` → `admin_payment.php` | AJAX POST | `payment/get/ajax_data` | Load dropdowns |
| `admin_payment.php` → `payment_model.php` | PHP call | `paymentDB()` | CRUD operations |
| `admin_payment.php` → `payment_model.php` | PHP call | `get_paymentContent()` | Account detail query |
| `admin_payment.php` → `settings_model.php` | PHP call | `paymodeDB()`, `bankDB()` | Settings data |
| `admin_payment.php` → `account_model.php` | PHP call | `get_customer_acc()`, `select_otp()` | Account/OTP |
| `admin_payment.php` → `customer_model.php` | PHP call | `get_entrydate()` | Day close date |
| `admin_payment.php` → `sms_model.php` | PHP call | `sendSMS_*()`, `send_whatsApp_message()` | Notifications |
| `admin_payment.php` → `digigold_modal.php` | PHP call | `digiGold_account()`, `get_digi_benefit()` | DigiGold benefits |
