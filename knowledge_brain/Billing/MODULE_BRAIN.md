# Billing Module Brain

> **Built**: 2026-03-24 | **Round**: 8 (Maintenance Refresh) | **Status**: 🟢 Complete (100%)

---

## 1. Module Overview

**Purpose**: Core sales billing module handling all billing workflows — sales bills, purchase returns, old metal exchange, credit bills, EDA (No-2) bills, bill splits, service bills, issue/receipt vouchers, cash collection, insurance billing, e-invoicing, and customer order advance adjustments.

### File Map

| File             | Path                                                       | Lines  | Purpose                                                             |
| ---------------- | ---------------------------------------------------------- | ------ | ------------------------------------------------------------------- |
| Controller       | `admin/application/controllers/admin_ret_billing.php`      | ~13,800 | All billing routes, AJAX handlers, e-invoice, POS integration       |
| Model            | `admin/application/models/ret_billing_model.php`           | ~12,200 | All DB queries, bill generation, estimation lookups, POS, report data |
| JS               | `admin/assets/js/ret_billing.js`                           | 44,682 | Client-side logic — calculations, form handling, AJAX calls         |
| Main View        | `admin/application/views/billing/form.php`                 | 189KB  | Primary billing form (sales, return, purchase, etc.)                |
| Split View       | `admin/application/views/billing/billsplit.php`            | 149KB  | Bill splitting interface                                            |
| List View        | `admin/application/views/billing/list.php`                 | 8KB    | Billing list with DataTable                                         |
| Approval List    | `admin/application/views/billing/approvallist.php`         | 8KB    | Approval workflow list                                              |
| Payment Edit     | `admin/application/views/billing/paymentmode_edit.php`     | 22KB   | Payment mode editing interface                                      |
| Delivery View    | `admin/application/views/billing/item_delivery.php`        | 5KB    | Item delivery tracking                                              |
| Ledger Xfer Form | `admin/application/views/billing/ledger_transfer_form.php` | 9KB    | Bank ledger transfer form                                           |
| Ledger Xfer List | `admin/application/views/billing/ledger_transfer_list.php` | 4KB    | Ledger transfer list view                                           |
| POS Settings     | `admin/application/views/billing/pos_settings.php`         | 29KB   | POS device/provider management UI (new Round 8)                    |
| POS Transactions | `admin/application/views/billing/pos_transactions.php`     | 26KB   | POS transaction list view (new Round 8)                            |
| POS EOD          | `admin/application/views/billing/pos_eod_settlement.php`   | 16KB   | POS end-of-day settlement report (new Round 8)                     |

### View Subdirectories

| Directory                           | Files | Purpose                                            |
| ----------------------------------- | ----- | -------------------------------------------------- |
| `views/billing/print/`              | 10    | Print templates (invoices, thermal receipts, etc.) |
| `views/billing/issueReceipt/`       | 6     | Issue & receipt forms/lists                        |
| `views/billing/cash_collection/`    | 3     | Cash collection denomination forms                 |
| `views/billing/service_bill/`       | 3     | Service bill views                                 |
| `views/billing/bill_number_format/` | 2     | Bill number formatting config                      |
| `views/billing/advance_transfer/`   | 1     | Advance transfer views                             |

### Connection Flow

```
Browser → JS (admin/assets/js/ret_billing.js)
       → AJAX → Controller (admin/application/controllers/admin_ret_billing.php)
       → Model (admin/application/models/ret_billing_model.php)
       → DB (ret_billing, ret_bill_details, ret_billing_payment, ret_billing_item_stones,
              ret_bill_other_metals, ret_taging, ret_taging_status_log, ret_estimation,
              ret_estimation_items, customer, ret_billing_advance, ret_journal,
              ret_billing_service, ret_billing_service_items, ret_cash_collection, etc.)
       → View (admin/application/views/billing/)
       → Browser
```

---

## 2. Constructor Analysis

### Loaded Models

| Model                      | Variable                          | Purpose                                           |
| -------------------------- | --------------------------------- | ------------------------------------------------- |
| `ret_billing_model`        | `$this->ret_billing_model`        | Primary — all billing DB operations               |
| `ret_purchase_order_model` | `$this->ret_purchase_order_model` | Purchase order data for returns                   |
| `admin_settings_model`     | `$this->admin_settings_model`     | Profile settings, access rights, day closing data |
| `sms_model`                | `$this->sms_model`                | SMS gateway integration                           |
| `admin_usersms_model`      | `$this->admin_usersms_model`      | User SMS preferences/templates                    |
| `log_model`                | `$this->log_model`                | Activity logging                                  |
| `payment_model`            | `$this->payment_model`            | Payment processing                                |
| `account_model`            | `$this->account_model`            | Accounting/journal entries                        |
| `ret_order_model`          | `$this->ret_order_model`          | Customer order management                         |

### Libraries

- `dompdf` — PDF generation for invoices (loaded via `require_once`)

### Session Gate (L43-62)

1. Checks `is_logged` session — redirects to login if false
2. Checks `access_time_from` / `access_time_to` — enforces allowed access time window
3. If outside allowed time → redirects to logout with flash message

---

## 3. Entry Points & Routes

### Page Load Routes

| URL Path                                         | Controller Method             | Lines              | Purpose                   |
| ------------------------------------------------ | ----------------------------- | ------------------ | ------------------------- |
| `/admin_ret_billing/billing/add`                 | `billing('add')`              | L235-282           | Load add billing form     |
| `/admin_ret_billing/billing/list`                | `billing('list')`             | L284-290           | Load billing list         |
| `/admin_ret_billing/billing/edit/{id}`           | `billing('edit', id)`         | (within billing()) | Load edit form            |
| `/admin_ret_billing/billing/approvallist`        | `billing('approvallist')`     | L292-300           | Load approval list        |
| `/admin_ret_billing/billing/save`                | `billing('save')`             | (within billing()) | Save new bill             |
| `/admin_ret_billing/billing/update`              | `billing('update')`           | (within billing()) | Update existing bill      |
| `/admin_ret_billing/billing/split_save`          | `billing('split_save')`       | L303-?             | Split bill save           |
| `/admin_ret_billing/issue/{type}/{id}`           | `issue($type, $id)`           | L5738-6521         | Issue voucher CRUD        |
| `/admin_ret_billing/receipt/{type}/{id}`         | `receipt($type, $id)`         | L6523-7508         | Receipt voucher CRUD      |
| `/admin_ret_billing/service_bill/{type}`         | `service_bill($type)`         | L9133-9500         | Service bill CRUD         |
| `/admin_ret_billing/bill_split/{type}/{id}`      | `bill_split($type, $id)`      | L9907-9939         | Bill split view           |
| `/admin_ret_billing/billing_invoice/{id}`        | `billing_invoice($id)`        | L5648-5683         | Print invoice             |
| `/admin_ret_billing/paymentmode_edit/{type}`     | `paymentmode_edit($type)`     | L8306-8677         | Payment mode edit         |
| `/admin_ret_billing/advance_transfer/{type}`     | `advance_transfer($type)`     | L9941-10056        | Advance transfer          |
| `/admin_ret_billing/item_delivery/{type}`        | `item_delivery($type)`        | L10192-10231       | Delivery management       |
| `/admin_ret_billing/cash_collection/{type}/{id}` | `cash_collection($type, $id)` | L11856-12031       | Cash collection           |
| `/admin_ret_billing/bill_number_format/{type}`   | `bill_number_format($type)`   | L10747-11323       | Bill number format config |
| `/admin_ret_billing/billing_insurance/{id}`      | `billing_insurance($id)`      | L12044-12064       | Insurance billing         |
| `/admin_ret_billing/bank_ledger_transfer/{type}` | `bank_ledger_transfer($type)` | L12066-12145       | Bank ledger transfer      |

### AJAX-Only Endpoints

| Controller Method                      | Lines        | Purpose                                                  |
| -------------------------------------- | ------------ | -------------------------------------------------------- |
| `createNewCustomer()`                  | L5191-5217   | Create customer inline from billing form                 |
| `updateNewCustomer()`                  | L5219-5247   | Update customer inline                                   |
| `getEstimationDetails()`               | L5249-5312   | Fetch estimation for billing                             |
| `getEstimationDetailsTags()`           | L5315-5335   | Fetch tagged estimation items                            |
| `getAllTaxgroupItems()`                | L5337-5345   | Get tax group items                                      |
| `getCustomersBySearch()`               | L5347-5355   | Customer typeahead search                                |
| `getTaggingBySearch()`                 | L5357-5365   | Tag search                                               |
| `getProductBySearch()`                 | L5367-5375   | Product search                                           |
| `getProductDesignBySearch()`           | L5377-5385   | Design search                                            |
| `getMetalTypes()`                      | L5387-5395   | Metal type dropdown                                      |
| `get_scheme_accounts()`                | L5399-5416   | Scheme account lookup                                    |
| `get_advance_details()`                | L5422-5433   | Customer advance details                                 |
| `getBillDetails()`                     | L5437-5458   | Get bill details by bill no                              |
| `get_return_Bill_details()`            | L5460-5481   | Return bill lookup                                       |
| `getBillingDetails()`                  | L5483-5504   | Full billing details for edit                            |
| `getCreditBillDetails()`               | L5506-5532   | Credit bill details                                      |
| `sendotp()`                            | L5534-5598   | Send OTP for discount approval                           |
| `update_otp()`                         | L5600-5633   | Update OTP record                                        |
| `cancel_bill()`                        | L7581-8001   | Cancel bill (complex — reverses tags, payments, journal) |
| `get_branch_details()`                 | L8003-8014   | Branch details for form                                  |
| `getVoucherDetails()`                  | L8016-8031   | Gift voucher validation                                  |
| `getGiftProducts()`                    | L8033-8042   | Gift product lookup                                      |
| `GiftRedeemProduct()`                  | L8044-8053   | Gift redeem check                                        |
| `GeneralGiftRedeemProduct()`           | L8055-8064   | General gift redeem                                      |
| `getSearchCompanyUsers()`              | L8068-8076   | Company user search                                      |
| `addNewCompanyUsers()`                 | L8078-8087   | Add company user                                         |
| `getCompanyPurchaseAmount()`           | L8089-8098   | Company purchase total                                   |
| `get_one_time_pre_weight_scheme()`     | L8102-8111   | Weight scheme lookup                                     |
| `get_customer_weight_scheme_details()` | L8113-8122   | Customer weight scheme                                   |
| `get_bank_acc_details()`               | L8180-8189   | Bank account dropdown                                    |
| `get_payment_device_details()`         | L8191-8200   | POS device list                                          |
| `get_customer_address()`               | L8204-8213   | Customer address                                         |
| `get_mydelivery_address()`             | L8215-8224   | Delivery address                                         |
| `update_mydelivery_address()`          | L8226-8277   | Save delivery address                                    |
| `getCustomersindRecords()`             | L8279-8302   | Industrial customer records                              |
| `get_customer_credit_details()`        | L8683-8692   | Credit details per customer                              |
| `send_bill_cancel_otp()`               | L8698-8774   | OTP for bill cancellation                                |
| `verify_otp_for_billcancel()`          | L8776-8834   | Verify cancel OTP                                        |
| `admin_approval()`                     | L8840-8918   | Admin discount approval                                  |
| `verify_otp()`                         | L8920-8978   | General OTP verification                                 |
| `send_credit_bill_otp()`               | L8982-9060   | Credit bill OTP                                          |
| `verify_credit_otp()`                  | L9062-9120   | Credit OTP verify                                        |
| `getBranchDayClosingData()`            | L9122-9131   | Day closing status                                       |
| `cancel_service_bill()`                | L9547-9592   | Cancel service bill                                      |
| `order_place()`                        | L9594-9719   | Place order from billing                                 |
| `update_branch()`                      | L9721-9862   | Update bill branch details                               |
| `getCustomerDet()`                     | L9864-9872   | Customer detail for billing                              |
| `getCreditPending()`                   | L9874-9883   | Pending credit bills                                     |
| `getCustomerSalesDetails()`            | L9885-9894   | Customer sales history                                   |
| `get_payModes()`                       | L9896-9905   | Payment modes list                                       |
| `update_delivery_status()`             | L10233-10265 | Delivery status update                                   |
| `generateEinvoice()`                   | L10269-10282 | Generate e-invoice                                       |
| `get_customer_tcs_percent()`           | L11329-11350 | TCS % for customer                                       |
| `bill_payment_details()`               | L11354-11363 | Payment details                                          |
| `get_tax_group_from_billing()`         | L11365-11371 | Tax group from billing                                   |
| `validate_huid()`                      | L11842-11853 | HUID validation                                          |
| `get_home_bill_sectionBranchwise()`    | L12034-12042 | Home section branchwise                                  |
| `update_order_rate_type()`             | L12147-12170 | Update order rate type                                   |
| `mobile_approval_request()`            | L11724-11751 | Mobile approval push                                     |
| `get_prev_ref_no()`                    | L11791-11800 | Previous ref number                                      |
| `close_issue_receipt()`                | L11802-11840 | Close issue/receipt                                      |
| `getactivesize()`                      | L11716-11721 | Active sizes                                             |
| `credit_coll_disc_admin_approval()`    | L11627-11673 | Credit collection discount approval                      |
| `verify_credit_coll_disc_otp()`        | L11677-11710 | Verify credit collection discount OTP                    |
| `adtrnssendotp()`                      | L10059-10123 | Advance transfer OTP                                     |
| `verify_advance_transfer_otp()`        | L10125-10175 | Verify advance transfer OTP                              |
| `order_adtrnssendotp()`                | L11373-11437 | Order advance transfer OTP                               |
| `order_verify_otp()`                   | L11440-11490 | Order OTP verify                                         |
| `order_delievery_sendotp()`            | L11505-11571 | Delivery OTP                                             |
| `order_delievery_verify_otp()`         | L11574-11624 | Delivery OTP verify                                      |
| `create_pushnotification()`            | L11753-11788 | Push notification                                        |
| `posSettings()`                        | L13571       | POS device & provider settings CRUD (new Round 8)       |
| `posTransactions()`                    | L13719       | POS transaction list (new Round 8)                      |
| `posSettlementSummary()`               | L13748       | POS EOD settlement summary (new Round 8)                |
| `posAuditTrail()`                      | L13787       | POS audit trail (new Round 8)                           |
| `phonepeCallback()`                    | L13481       | PhonePe POS payment callback handler (new Round 8)      |
| `getposdevicelists()`                  | L12393       | POS device AJAX list (new Round 8)                      |
| `UploadBilledTransaction()`            | L12438       | Upload billed transaction to POS (new Round 8)          |
| `cancelTransactionRequest()`           | L12567       | Cancel POS transaction request (new Round 8)            |
| `getTransactionStatus()`               | L12522       | Poll POS transaction status (new Round 8)               |
| `keepAlive()`                          | L13740       | Session keep-alive heartbeat (new Round 8)              |

### Utility / Internal Methods

| Method                           | Lines        | Purpose                                 |
| -------------------------------- | ------------ | --------------------------------------- |
| `base64ToFile()`                 | L67-93       | Convert base64 to file for customer img |
| `set_image()`                    | L95-112      | Set customer image                      |
| `upload_img()`                   | L114-170     | Image upload handler                    |
| `rrmdir()`                       | L172-191     | Recursive dir removal                   |
| `remove_img()`                   | L193-210     | Remove image                            |
| `send_sms()`                     | L5635-5646   | SMS sending helper                      |
| `generate_receipt_no()`          | L8124-8176   | Receipt number generator                |
| `repair_order_thermal_print()`   | L5685-5734   | Thermal print for repair                |
| `get_account_head()`             | L7510-7519   | Account head lookup                     |
| `get_borrower()`                 | L7542-7563   | Borrower search                         |
| `get_customer_advance_details()` | L7564-7577   | Customer advance                        |
| `viewdb()`                       | L9537-9545   | View raw DB data (debug)                |
| `createGSPEInvoice()`            | L10284-10333 | Create GSP e-invoice                    |
| `getTranDtls()`                  | L10335-10340 | Transaction details for e-invoice       |
| `getDocDtls()`                   | L10342-10354 | Document details for e-invoice          |
| `getSellerDtls()`                | L10356-10391 | Seller details for e-invoice            |
| `getBuyerDtls()`                 | L10393-10423 | Buyer details for e-invoice             |
| `getValDtls()`                   | L10425-10458 | Value details for e-invoice             |
| `getItemList()`                  | L10460-10527 | Item list for e-invoice                 |
| `formatnumber()`                 | L10529-10533 | Number formatting helper                |
| `createAuthToken()`              | L10535-10593 | e-Invoice auth token                    |
| `generateGSPEInvoice()`          | L10595-10675 | Generate GSP e-invoice                  |
| `decodeeInvoice()`               | L10677-10691 | Decode e-invoice                        |
| `updateIRNDetails()`             | L10693-10702 | Update IRN details                      |
| `base64_to_jpeg()`               | L10704-10728 | Convert e-invoice QR to JPEG            |
| `einvoiceimagecreate()`          | L10730-10741 | Create e-invoice image                  |
| `ad_trans_send_sms()`            | L10177-10188 | Advance transfer SMS                    |
| `order_ad_trans_send_sms()`      | L11492-11503 | Order advance transfer SMS              |

---

## 4. Model Methods Summary

**Total model methods**: 269 — See [METHOD_INDEX.md](METHOD_INDEX.md) for complete alphabetical listing.

| Category            | Count | Examples                                                                                        |
| ------------------- | ----- | ----------------------------------------------------------------------------------------------- |
| Generic CRUD        | 4     | `insertData`, `updateData`, `insertBatchData`, `deleteData`                                     |
| Bill Generation     | 8     | `code_number_generator`, `get_bill_no`, `bill_no_generate`, `generateRefNo`                     |
| Estimation Lookup   | 5     | `getEstimationDetails` (1,694 lines!), `getEstimationDetailsTags`, `get_EstimationPackingItems` |
| Bill Data Retrieval | 15+   | `getBillingDetails`, `getBillData`, `getreturnBillData`, `getCreditBillDetails`                 |
| Payment Processing  | 10+   | `getPaymentDetails`, `get_payModes`, `getCustomerpaymentDetails`                                |
| Stone/Metal Details | 12+   | `get_stone_details`, `get_tag_stone_details`, `get_other_metal_details`                         |
| Customer Operations | 8+    | `createNewCustomer`, `updateNewCustomer`, `getAvailableCustomers`                               |
| Advance/Scheme      | 10+   | `get_advance_details`, `get_billing_advance_details`, `get_closed_accounts`                     |
| Issue/Receipt       | 8+    | `ajax_getIssuetist`, `ajax_getReceiptlist`, `get_issue_details`, `get_receipt_details`          |
| Service Bill        | 5     | `ajax_getServiceBillList`, `getServiceBillingDetails`, `getServiceBillPaymentDetails`           |
| E-Invoice           | 8     | `getbillingdetailsitems`, `getbillingInfobybillId`, `updatebilleinvoicedetails`                 |
| Cash Collection     | 4     | `ajax_getCashCollection`, `ajax_getCashCollectionList`, `get_cashCollectionDetails`             |
| Voucher/Gift        | 5     | `getVoucherDetails`, `gift_voucher_master`, `CheckRedeemProduct`                                |
| Settings/Config     | 6     | `get_retSettings`, `get_ret_settings`, `get_employee_settings`, `get_profile_settings`          |
| Utility             | 5     | `no_to_words`, `encrypt`, `isEmptySetDefault`                                                   |

---

## 5. Key Tables

| Table                       | Purpose                    | Key Columns                                                                                                                                                            |
| --------------------------- | -------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `ret_billing`               | Main billing header        | `bill_id`, `bill_no`, `bill_type`, `bill_cus_id`, `tot_bill_amount`, `tot_amt_received`, `is_credit`, `is_eda`, `bill_date`, `id_branch`, `metal_type`, `is_cancelled` |
| `ret_bill_details`          | Billing line items         | `bill_det_id`, `bill_id`, `tag_id`, `esti_item_id`, `product_id`, `design_id`, `gross_wt`, `net_wt`, `item_cost`, `rate_per_grm`, `mc_value`, `wastage_percent`        |
| `ret_billing_payment`       | Payment records            | `bill_id`, `payment_amount`, `payment_mode`, `payment_status`, `card_type`, `card_no`, `cheque_no`                                                                     |
| `ret_billing_item_stones`   | Stone details per item     | `bill_id`, `bill_det_id`, `stone_id`, `pieces`, `wt`, `price`                                                                                                          |
| `ret_bill_other_metals`     | Other metals in bill items | `bill_det_id`, `tag_other_itm_metal_id`, `tag_other_itm_grs_weight`                                                                                                    |
| `ret_taging`                | Product tags               | `tag_id`, `tag_status` (0=available, 1=sold), `id_section`, `is_partial`                                                                                               |
| `ret_taging_status_log`     | Tag movement log           | `tag_id`, `date`, `status`, `from_branch`, `to_branch`, `form_secret`                                                                                                  |
| `ret_estimation`            | Estimation header          | `estimation_id`, `estbillid`                                                                                                                                           |
| `ret_estimation_items`      | Estimation line items      | `est_item_id`, `purchase_status`, `bil_detail_id`, `tag_id`                                                                                                            |
| `customer`                  | Customer master            | `id_customer`, `pan`, `aadharid`, `driving_license_no`, `passport_no`                                                                                                  |
| `ret_billing_advance`       | Bill advance adjustments   | `bill_id`, advance details                                                                                                                                             |
| `ret_billing_service`       | Service bill header        | service bill fields                                                                                                                                                    |
| `ret_billing_service_items` | Service bill items         | service item fields                                                                                                                                                    |
| `ret_journal`               | Accounting journal         | journal entry fields                                                                                                                                                   |
| `ret_cash_collection`       | Cash collection records    | cash collection fields                                                                                                                                                 |
| `branch`                    | Branch master              | `id_branch`, branch details                                                                                                                                            |
| `ret_billing_format`        | Bill number format config  | format configuration                                                                                                                                                   |
| `gift_issued`               | Gift issued against chit   | `id_gift_issued`, `gift_desc`, `gift_amount`, `quantity`, `id_scheme_account`, `date_issued`, `status` (1=active), `type` (1=gift)                                      |
| `ret_billing_chit_utilization` | Chit utilized in bills  | `bill_id`, `id_scheme_account`, chit utilization details                                                                                                                |

---

## 6. Bill Type Reference

| `bill_type` Value | Meaning                             | UI Section Visible                                  |
| ----------------- | ----------------------------------- | --------------------------------------------------- |
| 1                 | Normal Sale                         | sale_details, search_esti, search_tag, search_order |
| 2                 | Old Metal Exchange Sale             | sale_details, purchase_details                      |
| 3                 | Sale + Return + Purchase (Combined) | All sections                                        |
| 4                 | Purchase Only                       | purchase_details                                    |
| 5                 | Order Advance                       | order_adv_details                                   |
| 6                 | Other (hidden sections)             | None visible                                        |
| 7                 | Sales Return                        | return_details, total_summary_details               |
| 9                 | (EDA variant)                       | sale_details + eda_tax_calc                         |
| 10                | (variant)                           | —                                                   |
| 15                | Suspense Stock                      | sale_details + eda_tax_calc                         |

---

## 7. Known Risks

1. **`billing()` method is 4,971 lines** (L218-5189) — monolithic switch-case handling add/edit/save/update/split_save/approval. Any bug here is extremely hard to trace.
2. **`getEstimationDetails()` model method is 1,694 lines** (L2745-4439) — massive conditional logic for different bill types and estimation sources.
3. **No transaction wrapping in some save paths** — `split_save` uses `trans_begin()` but need to verify all save paths.
4. **Direct `$_POST` access** in `split_save` (L307) — `$addData = $_POST['billing']` bypasses CI3 input sanitization.
5. **Form Secret for duplicate prevention** — Uses a `form_secret` session mechanism to prevent double submission, but implementation varies across save paths.
6. **44K-line JS file** — Extremely large, monolithic JS with global variables and no module pattern.
7. **Hard-coded bill_type numbers** — Magic numbers (1, 2, 3, 4, 5, 6, 7, 9, 10, 15) used throughout without constants.
8. **E-Invoice integration** — External API calls for GSP e-invoice with auth token management — potential failure points.
9. **OTP flow for multiple operations** — Discount approval, bill cancellation, credit sales, advance transfer, delivery — each has its own OTP flow with potential for race conditions.
10. **`cancel_bill()` (L7581-8001, ~420 lines)** — Complex reversal logic touching tags, payments, journal entries, estimations.

---

## 8. DB Verification Queries

```sql
-- Q1: Complete bill with all child records
SELECT b.*, bd.*, bp.payment_amount, bp.payment_mode
FROM ret_billing b
LEFT JOIN ret_bill_details bd ON b.bill_id = bd.bill_id
LEFT JOIN ret_billing_payment bp ON b.bill_id = bp.bill_id
WHERE b.bill_id = {BILL_ID};

-- Q2: Verify bill total matches sum of line items
SELECT b.bill_id, b.tot_bill_amount,
       SUM(bd.item_cost + bd.item_total_tax) as calc_total,
       b.tot_bill_amount - SUM(bd.item_cost + bd.item_total_tax) as diff
FROM ret_billing b
JOIN ret_bill_details bd ON b.bill_id = bd.bill_id
WHERE b.is_cancelled = 0
GROUP BY b.bill_id
HAVING ABS(diff) > 1;

-- Q3: Orphan bill details (no parent bill)
SELECT bd.*
FROM ret_bill_details bd
LEFT JOIN ret_billing b ON bd.bill_id = b.bill_id
WHERE b.bill_id IS NULL;

-- Q4: Tags marked as sold but no bill exists
SELECT t.tag_id, t.tag_status
FROM ret_taging t
LEFT JOIN ret_bill_details bd ON t.tag_id = bd.tag_id
LEFT JOIN ret_billing b ON bd.bill_id = b.bill_id AND b.is_cancelled = 0
WHERE t.tag_status = 1 AND b.bill_id IS NULL;

-- Q5: Payment total vs bill received amount
SELECT b.bill_id, b.bill_no, b.tot_amt_received,
       COALESCE(SUM(bp.payment_amount), 0) as payment_total,
       b.tot_amt_received - COALESCE(SUM(bp.payment_amount), 0) as diff
FROM ret_billing b
LEFT JOIN ret_billing_payment bp ON b.bill_id = bp.bill_id
WHERE b.is_cancelled = 0
GROUP BY b.bill_id
HAVING ABS(diff) > 1;
```

---

## 9. Anti-Patterns Register

### Pre-existing (Found during brain build — Round 2)

| #     | Anti-Pattern                         | Location                                                    | Risk      | Details                                                                            |
| ----- | ------------------------------------ | ----------------------------------------------------------- | --------- | ---------------------------------------------------------------------------------- |
| AP-01 | **Deprecated Select2 API**           | `ret_billing.js` L15529, L15572, L15599                     | 🟡 Medium | Uses `.select2('val', ...)` — deprecated. Should use `.val(...).trigger('change')` |
| AP-02 | **`async: false` in AJAX**           | `ret_billing.js` L15641, L36318, L36389                     | 🔴 High   | Blocks UI thread, causes browser freezing during data fetch                        |
| AP-03 | **`parseInt()` on currency amounts** | `ret_billing.js` L19748 (`calculate_advance_adjust_amount`) | 🔴 High   | Truncates decimal amounts — should be `parseFloat()`                               |
| AP-04 | **Debug `alert("1")` leftover**      | `ret_billing.js` L36395 (`verify_advance_transfer_otp`)     | 🟡 Medium | Debug alert shown to production users                                              |
| AP-05 | **Empty error handler**              | `ret_billing.js` L29453                                     | 🟡 Medium | `error: function (error) {}` — silent AJAX failure, user sees frozen screen        |
| AP-06 | **Direct `$_POST` access**           | `admin_ret_billing.php` L307 (split_save)                   | 🔴 High   | `$addData = $_POST['billing']` bypasses CI3 input sanitization                     |
| AP-07 | **Dead/duplicate POS method stub**   | `admin_ret_billing.php` (POS section) `updatPOSDevice`      | 🟡 Medium | Misspelled duplicate of `updatePOSDevice` — unreachable code, never called in routes; verified dead stub (new Round 9) |

### Post-Fix Tracking

| Date       | Bug ID    | Anti-Pattern                                                                                                                                                                                               | Fix Applied                                                                                                                                                                                                       | Files Changed    |
| ---------- | --------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------------- |
| 2026-02-25 | BIL-CLT01 | Blind ratio discount distribution to ALL items including those with zero MC/VA — items that shouldn't receive discount get unearned discount, overflow goes to item_blc_discount instead of redistributing | Two-tier multi-pass algorithm: `calculateDiscountAllocations()` pre-allocates. Tier 1 from margin above min MC/VA, Tier 2 from min MC/VA, `item_blc_discount` only as last resort. Items with MC=0, VA=0 skipped. | `ret_billing.js` |
| 2026-03-04 | BIL-INT01 | Credit due amount calculation ignored previously returned item costs, leading to incorrect balance display on 2nd+ return. | Subtracted `SUM(return_item_cost)` from `ret_bill_details` for the original bill in `getBillData()`. | `ret_billing_model.php` |
| 2026-03-05 | BIL-INT02 | Subquery in `getCreditBillDetails()` used `ret_billing.credit_ret_amt` (always 0) instead of actual return data; BIL-INT01 also lacked return bill status check. | Both functions now join `ret_bill_return_details → ret_bill_details` via `ret_bill_det_id`, sum `d.item_cost`, filter `rb.bill_status = 1`. JS patched to use `total_returned`. | `ret_billing_model.php`, `ret_billing.js` (3 versions) |
| 2026-03-06 | BIL-INT03 | `get_BillAmount()` did not fetch `round_off_amt`; balance formula omitted it. A fully-returned credit bill retained a tiny residual equal to `round_off_amt`, keeping `credit_status` = 2 (Pending). | Added `round_off_amt` to `get_BillAmount()` SELECT; subtracted `floatval($bill_details['round_off_amt'])` in both save and cancel balance calculations; subtracted `IFNULL(b.round_off_amt,0)` in report SQL. | `ret_billing_model.php`, `admin_ret_billing.php`, `ret_reports_model.php` |
| 2026-03-07 | BIL-INT04 | `total_returned` query in `getBillData()`, `getCreditBillDetails()`, `get_credit_pending_details()` and `getcreditBill_history()` subtracted ALL return amounts including cash refunds. Only advance-kept returns (`make_as_advance=1`) should reduce the due. | Added `AND rb.make_as_advance = 1` to all four subqueries in billing and reports models. | `ret_billing_model.php`, `ret_reports_model.php` |
| 2026-03-10 | BIL-CLT03 | Frontend ignored calculation update on `.bill_wastage_per` hidden input so it remained 0/null; PHP Controller skipped array values on default string fallback `empty("0")` | Added explicit `val()` update to JS hidden input and ternary checks to preserve `. !== '' && !== null` in the `wastage_percent` array keys across six sections. | `ret_billing.js`, `admin_ret_billing.php` |
| 2026-03-23 | BIL-CLT04 | `order_adj` query used `b.bill_date` (delivery date) instead of `a.advance_date` (actual payment date) for Order Advance details. | Changed SQL to select `a.advance_date` from `ret_billing_advance` instead of `b.bill_date` from `ret_billing`. | `ret_billing_model.php` |
| 2026-03-26 | BIL-CLT05 | `getEstimationDetails()` filters items by `purchase_status=0` AND `tag_status=0`, but cancelled bills don't reset these flags — orphaned items become invisible to re-billing. | Added `resetOrphanedEstimationItems()` pre-check that resets `purchase_status` and `tag_status` for items linked to cancelled bills (`bill_status=2`) before the query runs. | `ret_billing_model.php`, `admin_ret_billing.php` |

| 2026-04-04 | BIL-CLT06 | `get_credit_collection_details()` overwrites the correct `$paid_amount` from `get_previous_credit_collections()`, losing old_metal_amount. Legacy function also has loop-scoped variable reset bug. | Commented out the overwrite line in `get_credit_pending_details()`. The comprehensive calculation from `get_previous_credit_collections()` now takes effect. | `ret_billing_model.php` |

### BIL-CLT06: Credit Outstanding Missing Old Metal Deduction ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| BIL-INT01 | Calculating remaining due by only subtracting payments, ignoring returns. | Integrated `return_item_cost` sum into the `due_amount` calculation formula. | 2026-03-04 |

**Prevention**: When calculating "Balance Due" or "Remaining Credit", always sum both payments (`ret_billing_payment`) AND returns (`ret_bill_details.return_item_cost`) to derive the true outstanding amount.

### BIL-INT02: Wrong Data Source for Returns in Credit Due ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| BIL-INT02 | Using unpopulated column (`credit_ret_amt`) + missing return bill status check | Correct join through `ret_bill_return_details → ret_bill_details` + `rb.bill_status = 1` filter | 2026-03-05 |

**Prevention**: Never trust a column just because it exists—verify it's populated during the actual workflow. For return amounts, always join `ret_bill_return_details → ret_bill_details` and sum `item_cost`. Always filter by `bill_status = 1` to exclude cancelled returns.

> **Note (BIL-INT02)**: The `ret_billing.credit_ret_amt` column IS set during bill save (L1372) but only stores the net credit return amount at the time of billing. It does NOT accumulate across multiple partial returns. The actual per-item return costs are always in `ret_bill_details.item_cost` linked via `ret_bill_return_details`.

> **Note (BIL-CLT01)**: The original ratio-based discount distribution was simpler but fundamentally flawed — it treated all items equally regardless of their MC/VA capacity. The two-tier approach respects the business hierarchy: first absorb from margin (no OTP needed), then from minimum values (OTP required), and only as a last resort from `item_blc_discount`.

### BIL-INT03: Round-Off Omission in Credit Status Balance ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| BIL-INT03 | Balance formula omitted `round_off_amt`, leaving a residual that prevented fully-settled credit status from flipping to Paid. | Add `round_off_amt` to `get_BillAmount()` SELECT; subtract it in save/cancel balance calculations and in both credit-pending report SQL queries. | 2026-03-06 |

**Prevention**: The `tot_bill_amount` stored on `ret_billing` may include a `round_off_amt`. Any balance formula that subtracts payments/returns from `tot_bill_amount` **must also subtract `round_off_amt`** (using `floatval()` for safety). Verify that `get_BillAmount()` always SELECTs this column, and that report SQL wraps it in `IFNULL(b.round_off_amt, 0)`.

> **Note (BIL-INT03)**: `round_off_amt` is set at bill creation time and never changes. It is a permanent component of the bill total but is NOT part of the items or collections — it must be deducted separately when calculating the true outstanding balance.
| 2026-03-06 | BIL-CLT02 | `createSaleBillSplitRow` hardcoded `sale_item_type: idx == 0 ? 0 : 2` causing first split of a Home Bill to be saved as `item_type=0`, making it invisible in Cash Abstract report (which filters `item_type=2`). Also hardcoded `sale_pcs: : 1` for subsequent splits, double-counting piece totals. | (1) JS `ret_billing.js`: `sale_item_type` now reads from `curRow.find(".sale_item_type").val()`. `sale_pcs` set to `0` for subsequent splits. (2) Model `ret_reports_model.php`: Home Bill query WHERE expanded to `(d.item_type=2 OR (d.item_type=0 AND d.is_partial_sale=1))` to recover historical records. | `ret_billing.js`, `ret_reports_model.php` |

> **Note (BIL-CLT01)**: The original ratio-based discount distribution was simpler but fundamentally flawed — it treated all items equally regardless of their MC/VA capacity. The two-tier approach respects the business hierarchy: first absorb from margin (no OTP needed), then from minimum values (OTP required), and only as a last resort from `item_blc_discount`.

> **Note (BIL-CLT02)**: The `item_type` classification in `ret_bill_details` is the critical routing field for Cash Abstract. `item_type=2` + `tag_id IS NULL` = Home Bill. `item_type=0` + `tag_id IS NOT NULL` = Tagged Partial Sale. The bug caused the first split row of Home Bills to be incorrectly classified as tagged partial sales, so the model query was also widened to catch the historically misclassified rows without requiring a data migration.


### BIL-INT04: Cash Refund Returns Incorrectly Reducing Credit Due ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| BIL-INT04 | `total_returned` query in `getBillData()` + `getCreditBillDetails()` subtracted ALL return amounts from credit due, including cash refunds. Only advance-kept returns (`make_as_advance=1`) should reduce the due. | Added `AND rb.make_as_advance = 1` to both `total_returned` subqueries. | 2026-03-07 |

**Prevention**: When a sales return is processed, TWO outcomes are possible:
1. **Cash refund** (`make_as_advance=0`): money goes back to customer directly → original credit debt is **unchanged**.
2. **Kept as advance** (`make_as_advance=1`): money is held as advance for the customer → `ret_issue_receipt` row created with `receipt_type=3` → credit debt is **reduced**.

Any query summing `return_item_cost` to reduce credit due **must filter** `AND rb.make_as_advance = 1` (where `rb` is the return bill joined via `ret_bill_return_details.bill_id`).

### BIL-CLT03: Zero Value Loss During Fallback Checks ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| BIL-CLT03 | Hidden input neglected on UI updates + backend schema stripped literal `0` integers via `empty()` fallback. | Restored hidden input binding + explicitly wrapped array map fields with `!== '' && !== null` logic rather than using `empty()`. | 2026-03-10 |

**Prevention**: When dealing with fields that can legitimately be numeric zero (like percentage, wastage, or quantities), NEVER rely exclusively on PHP's `empty()` construct before database insertion, as `empty(0)` and `empty("0")` will aggressively match and trigger fallback logic, stripping explicit user entries of `0`.

### BIL-CLT05: Orphaned Estimation Items After Bill Cancellation ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| BIL-CLT05 | Cancelled bills leave `purchase_status=1` and `tag_status!=0` on estimation items/tags, making them invisible to subsequent billing queries. | Added `resetOrphanedEstimationItems()` pre-check before `getEstimationDetails()` that finds items linked to cancelled bills and resets their status. | 2026-03-26 |

**Prevention**: Any operation that changes status flags (`purchase_status`, `tag_status`, etc.) as part of a workflow MUST have a corresponding rollback mechanism when the parent operation is cancelled. When cancelling a bill, always reset the linked estimation items' `purchase_status` back to `0` and the tag `tag_status` back to `0`. Alternatively, add a pre-check before querying to clean up orphaned state.

> **Note (BIL-CLT05)**: The `bill_status=2` (cancelled) check is critical — only items linked to CANCELLED bills should be reset. Items linked to active bills (`bill_status=1`) must remain with `purchase_status=1` to prevent double-billing. The cancel routine in the controller SHOULD reset these flags but doesn't in all code paths, necessitating the pre-check approach.

### BIL-CLT06: Credit Outstanding Missing Old Metal Deduction ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| BIL-CLT06 | Two competing functions calculate `$paid_amount` for credit outstanding. The correct one (`get_previous_credit_collections`) runs first but is immediately overwritten by a legacy function (`get_credit_collection_details`) that has a loop-scoped variable reset bug, losing old metal amounts. | Commented out the overwrite call to `get_credit_collection_details()` in `get_credit_pending_details()` so the comprehensive `get_previous_credit_collections()` result is used. | 2026-04-04 |

**Prevention**: When replacing a calculation with an improved version, ALWAYS delete or comment out the old call immediately. Never leave both live — even temporarily — because the second assignment unconditionally overwrites the first. Also: never reset accumulator variables (`$old__metal_amount = 0`) INSIDE a foreach loop if you need the sum across ALL iterations.

> **Note (BIL-CLT06)**: The `get_credit_collection_details()` function (L7822) is now effectively dead code for the outstanding display path. It may still be called elsewhere — a full grep should be done before deleting it. The `get_previous_credit_collections()` function (L11722) is the authoritative calculation.

### BIL-CLT07: Print Template Chit Adjustment Columns & Split Benefits ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| BIL-CLT07 | Missing columns config and benefit variables mapping in template receipts | Mapped rate benefit / total columns and added approx discount benefits for split bills in template 480 | 2026-05-27 |

**Prevention**: Keep dynamic print template receipt helpers in sync with legacy layouts (`bill_format_2.php`) to support new customer scheme benefit modifications.

> **Note (BIL-CLT07)**: Cloned template 480 from 478 and adjusted `table_chit_adjustment_90003` configuration columns, mapping Ref No to `{{ref_no}}`, Amount to `{{amount}}`, Rate Benefit to `{{rate_benefit}}`, and Total to `{{total}}`. Added text node `text_apx_scheme_discount` displaying `"Apx Scheme Discount: {{apx_scheme_discount}}"` gated by `has_apx_scheme_discount` for split bills.

---

## 10. Codebase Notes

- **Naming Convention**: Controller uses `Admin_ret_billing`, model uses `Ret_billing_model`
- **JS Pattern**: Global variables at top of file, `$(document).ready()` with switch on `ctrl_page[1]` (route segment)
- **AJAX Pattern**: JS file uses standard `$.ajax({url: base_url + "index.php/...", ...})` pattern — confirmed via section sampling (grep fails due to encoding, likely UTF-16 BOM)
- **EDA Toggle**: `Ctrl+Enter` keyboard shortcut toggles between Normal (is_eda=1) and EDA (is_eda=2) mode
- **Day Closing Integration**: Bill date is determined by `getBranchDayClosingData()` — uses entry_date if not today
- **Metal Rate Lookup**: `get_branchwise_rate()` fetches branch-specific metal rates at time of billing
- **Bill Number Generation**: Uses `code_number_generator()` with branch + metal_type + is_eda as input parameters
- **Chit Gift in Print (2026-04-07)**: When `show_chit_gift_in_bill=1`, `getOtherEstimateItemsDetails()` queries `gift_issued` via `ret_billing_chit_utilization` for active gifts and passes `chit_gift_details[]` to the view. Currently only `bill_format_2.php` renders these as main item rows with S.NO and 0.00 amount.
