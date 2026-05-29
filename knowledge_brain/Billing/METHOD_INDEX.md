# Billing Module — Method Index

> **AI-Optimized**: Alphabetical for grep. Every method → tables → callers.

---

## 7a. Controller Methods (Admin_ret_billing) — Alphabetical

| Method                                 | Lines        | Tables Read                                                            | Tables Written                                                                                                                                       | Purpose                                                        |
| -------------------------------------- | ------------ | ---------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------------------------- |
| `addNewCompanyUsers()`                 | L8078-8087   | —                                                                      | `company_users` (via model)                                                                                                                          | Add company user inline                                        |
| `admin_approval()`                     | L8840-8918   | `ret_otp_approval`                                                     | `ret_otp_approval`                                                                                                                                   | Admin discount approval via OTP                                |
| `adtrnssendotp()`                      | L10059-10123 | `employee`, `branch`                                                   | `ret_otp_approval`                                                                                                                                   | Send OTP for advance transfer                                  |
| `ad_trans_send_sms()`                  | L10177-10188 | —                                                                      | —                                                                                                                                                    | SMS helper for advance transfer                                |
| `advance_transfer()`                   | L9941-10056  | `ret_billing`, `ret_billing_advance`                                   | `ret_billing_advance`, `ret_billing`                                                                                                                 | Transfer advance between bills                                 |
| `bank_ledger_transfer()`               | L12066-12145 | `ret_billing`, bank tables                                             | bank tables                                                                                                                                          | Bank ledger transfer CRUD                                      |
| `base64ToFile()`                       | L67-93       | —                                                                      | filesystem                                                                                                                                           | Convert base64 to image file                                   |
| `base64_to_jpeg()`                     | L10704-10728 | —                                                                      | filesystem                                                                                                                                           | QR code image for e-invoice                                    |
| `bill_number_format()`                 | L10747-11323 | `ret_billing_format`, `branch`                                         | `ret_billing_format`                                                                                                                                 | Bill number format CRUD                                        |
| `bill_payment_details()`               | L11354-11363 | `ret_billing_payment`                                                  | —                                                                                                                                                    | Get payment details for a bill                                 |
| `bill_split()`                         | L9907-9939   | `ret_billing`, `ret_bill_details`                                      | —                                                                                                                                                    | Load bill split view                                           |
| `billing()`                            | L218-5189    | **Many** (see DATA_FLOW)                                               | `ret_billing`, `ret_bill_details`, `ret_billing_payment`, `ret_billing_item_stones`, `ret_bill_other_metals`, `ret_taging`, `ret_estimation`, + more | **Mega-method**: list/add/edit/save/update/split_save/approval |
| `billing_insurance()`                  | L12044-12064 | `ret_billing`, insurance tables                                        | `ret_billing`                                                                                                                                        | Insurance billing                                              |
| `billing_invoice()`                    | L5648-5683   | `ret_billing`, `ret_bill_details`, company                             | —                                                                                                                                                    | Print invoice view                                             |
| `cancel_bill()`                        | L7581-8001   | `ret_billing`, `ret_bill_details`, `ret_billing_payment`, `ret_taging` | `ret_billing`, `ret_taging`, `ret_estimation_items`, `ret_billing_payment`, `ret_journal`                                                            | Cancel bill — full reversal                                    |
| `cancel_service_bill()`                | L9547-9592   | `ret_billing_service`                                                  | `ret_billing_service`                                                                                                                                | Cancel service bill                                            |
| `cancelTransactionRequest()`           | L12567+      | POS transaction tables                                                 | POS transaction tables                                                                                                                               | Cancel a POS transaction request                               |
| `cash_collection()`                    | L11856-12031 | `ret_cash_collection`, `denomination`                                  | `ret_cash_collection`                                                                                                                                | Cash collection CRUD                                           |
| `close_issue_receipt()`                | L11802-11840 | `ret_billing`                                                          | `ret_billing`                                                                                                                                        | Close issue/receipt                                            |
| `createAuthToken()`                    | L10535-10593 | company auth table                                                     | company auth table                                                                                                                                   | e-Invoice auth token                                           |
| `createGSPEInvoice()`                  | L10284-10333 | company, branch, billing                                               | —                                                                                                                                                    | Build e-invoice JSON                                           |
| `createNewCustomer()`                  | L5191-5217   | —                                                                      | `customer`                                                                                                                                           | Create new customer inline                                     |
| `create_pushnotification()`            | L11753-11788 | employee FCM tokens                                                    | —                                                                                                                                                    | Send push notification                                         |
| `deletePOSDevice()`                    | L13636       | POS device tables                                                      | POS device tables                                                                                                                                    | Delete a POS device record                                     |
| `credit_coll_disc_admin_approval()`    | L11627-11673 | `ret_otp_approval`                                                     | `ret_otp_approval`                                                                                                                                   | Credit collection discount approval                            |
| `decodeeInvoice()`                     | L10677-10691 | —                                                                      | —                                                                                                                                                    | Decode e-invoice signed data                                   |
| `einvoiceimagecreate()`                | L10730-10741 | `ret_billing`                                                          | filesystem                                                                                                                                           | Create e-invoice QR image                                      |
| `formatnumber()`                       | L10529-10533 | —                                                                      | —                                                                                                                                                    | Format number to 2 decimals                                    |
| `generateEinvoice()`                   | L10269-10282 | `ret_billing`, customer                                                | `ret_billing`                                                                                                                                        | Generate e-invoice flow entry                                  |
| `generateGSPEInvoice()`                | L10595-10675 | —                                                                      | `ret_billing`                                                                                                                                        | Submit e-invoice to GSP API                                    |
| `generate_receipt_no()`                | L8124-8176   | scheme receipt table                                                   | scheme receipt table                                                                                                                                 | Generate receipt number                                        |
| `get_account_head()`                   | L7510-7519   | `account_head`                                                         | —                                                                                                                                                    | Account heads for vouchers                                     |
| `getactivesize()`                      | L11716-11721 | `ret_size`                                                             | —                                                                                                                                                    | Active sizes for product                                       |
| `get_advance_details()`                | L5422-5433   | `ret_billing`, advance tables                                          | —                                                                                                                                                    | Customer advance details                                       |
| `get_bank_acc_details()`               | L8180-8189   | `bank_accounts`                                                        | —                                                                                                                                                    | Bank accounts for payment                                      |
| `getBillingDetails()`                  | L5483-5504   | `ret_billing`, `ret_bill_details`, `ret_billing_payment`               | —                                                                                                                                                    | Full billing details for edit                                  |
| `getBillDetails()`                     | L5437-5458   | `ret_billing`, `ret_bill_details`                                      | —                                                                                                                                                    | Bill details by bill number                                    |
| `get_borrower()`                       | L7542-7563   | `customer`, borrower tables                                            | —                                                                                                                                                    | Borrower search for issue/receipt                              |
| `get_branch_details()`                 | L8003-8014   | `branch`                                                               | —                                                                                                                                                    | Branch details                                                 |
| `getBranchDayClosingData()`            | L9122-9131   | `day_closing`                                                          | —                                                                                                                                                    | Day closing status for branch                                  |
| `getBuyerDtls()`                       | L10393-10423 | `customer`                                                             | —                                                                                                                                                    | e-Invoice buyer details                                        |
| `getCompanyPurchaseAmount()`           | L8089-8098   | `ret_billing`                                                          | —                                                                                                                                                    | Total company purchase                                         |
| `getCreditBillDetails()`               | L5506-5532   | `ret_billing`, credit tables                                           | —                                                                                                                                                    | Credit bill lookup                                             |
| `getCreditPending()`                   | L9874-9883   | `ret_billing`                                                          | —                                                                                                                                                    | Pending credit list                                            |
| `get_customer_address()`               | L8204-8213   | `customer`, address tables                                             | —                                                                                                                                                    | Customer address                                               |
| `get_customer_advance_details()`       | L7564-7577   | advance tables                                                         | —                                                                                                                                                    | Customer advance summary                                       |
| `get_customer_credit_details()`        | L8683-8692   | `ret_billing`, credit tables                                           | —                                                                                                                                                    | Customer credit details                                        |
| `get_customer_tcs_percent()`           | L11329-11350 | `customer`, TCS tables                                                 | —                                                                                                                                                    | Customer TCS percentage                                        |
| `getCustomerDet()`                     | L9864-9872   | `customer`                                                             | —                                                                                                                                                    | Customer detail                                                |
| `getCustomersBySearch()`               | L5347-5355   | `customer`                                                             | —                                                                                                                                                    | Customer typeahead                                             |
| `getCustomerSalesDetails()`            | L9885-9894   | `ret_billing`, `ret_bill_details`                                      | —                                                                                                                                                    | Customer sales history                                         |
| `getCustomersindRecords()`             | L8279-8302   | `customer`                                                             | —                                                                                                                                                    | Industrial customer records                                    |
| `getDocDtls()`                         | L10342-10354 | `ret_billing`                                                          | —                                                                                                                                                    | e-Invoice doc details                                          |
| `getEstimationDetails()`               | L5249-5312   | `ret_estimation`, `ret_estimation_items`, `ret_taging`                 | —                                                                                                                                                    | Fetch estimation for billing                                   |
| `getEstimationDetailsTags()`           | L5315-5335   | `ret_estimation`, tags                                                 | —                                                                                                                                                    | Tagged estimation items                                        |
| `getGiftProducts()`                    | L8033-8042   | gift product tables                                                    | —                                                                                                                                                    | Gift products                                                  |
| `get_home_bill_sectionBranchwise()`    | L12034-12042 | `ret_billing`, section tables                                          | —                                                                                                                                                    | Dashboard section data                                         |
| `getItemList()`                        | L10460-10527 | `ret_bill_details`                                                     | —                                                                                                                                                    | e-Invoice item list                                            |
| `getMetalTypes()`                      | L5387-5395   | `metal_types`                                                          | —                                                                                                                                                    | Metal type list                                                |
| `get_mydelivery_address()`             | L8215-8224   | delivery address tables                                                | —                                                                                                                                                    | Delivery address                                               |
| `get_one_time_pre_weight_scheme()`     | L8102-8111   | scheme tables                                                          | —                                                                                                                                                    | Weight scheme lookup                                           |
| `get_customer_weight_scheme_details()` | L8113-8122   | scheme tables                                                          | —                                                                                                                                                    | Customer weight scheme                                         |
| `get_payModes()`                       | L9896-9905   | `ret_payment_modes`                                                    | —                                                                                                                                                    | Payment modes                                                  |
| `get_payment_device_details()`         | L8191-8200   | `payment_devices`                                                      | —                                                                                                                                                    | POS devices                                                    |
| `getProductBySearch()`                 | L5367-5375   | `product`                                                              | —                                                                                                                                                    | Product search                                                 |
| `getProductDesignBySearch()`           | L5377-5385   | `product_design`                                                       | —                                                                                                                                                    | Design search                                                  |
| `get_prev_ref_no()`                    | L11791-11800 | `ret_billing`                                                          | —                                                                                                                                                    | Previous ref number                                            |
| `get_return_Bill_details()`            | L5460-5481   | `ret_billing`, return tables                                           | —                                                                                                                                                    | Return bill lookup                                             |
| `get_scheme_accounts()`                | L5399-5416   | scheme account tables                                                  | —                                                                                                                                                    | Scheme accounts                                                |
| `getSearchCompanyUsers()`              | L8068-8076   | `company_users`                                                        | —                                                                                                                                                    | Company user search                                            |
| `getSellerDtls()`                      | L10356-10391 | company, branch                                                        | —                                                                                                                                                    | e-Invoice seller                                               |
| `getTaggingBySearch()`                 | L5357-5365   | `ret_taging`                                                           | —                                                                                                                                                    | Tag search                                                     |
| `get_tax_group_from_billing()`         | L11365-11371 | tax group tables                                                       | —                                                                                                                                                    | Tax group                                                      |
| `getTranDtls()`                        | L10335-10340 | —                                                                      | —                                                                                                                                                    | e-Invoice transaction details                                  |
| `getValDtls()`                         | L10425-10458 | `ret_billing`, `ret_bill_details`                                      | —                                                                                                                                                    | e-Invoice values                                               |
| `getVoucherDetails()`                  | L8016-8031   | `gift_voucher` tables                                                  | —                                                                                                                                                    | Voucher validation                                             |
| `GiftRedeemProduct()`                  | L8044-8053   | gift tables                                                            | —                                                                                                                                                    | Gift redeem check                                              |
| `GeneralGiftRedeemProduct()`           | L8055-8064   | gift tables                                                            | —                                                                                                                                                    | General gift redeem                                            |
| `getAllTaxgroupItems()`                | L5337-5345   | `tax_group_items`                                                      | —                                                                                                                                                    | Tax group items                                                |
| `getPOSDeviceById()`                   | L13581       | POS device tables                                                      | —                                                                                                                                                    | Fetch single POS device by ID                                  |
| `getposdevicelists()`                  | L12393       | POS device tables                                                      | —                                                                                                                                                    | List POS devices (AJAX DataTable)                              |
| `getPOSProviderById()`                 | L13660       | POS provider tables                                                    | —                                                                                                                                                    | Fetch single POS provider by ID                                |
| `getPOSTransactionDetail()`            | L13728       | POS transaction tables                                                 | —                                                                                                                                                    | POS transaction detail view                                    |
| `getTransactionStatus()`               | L12522       | POS transaction tables                                                 | —                                                                                                                                                    | Poll/check POS transaction status                              |
| `index()`                              | L65          | —                                                                      | —                                                                                                                                                    | Empty index                                                    |
| `keepAlive()`                          | L13740       | —                                                                      | —                                                                                                                                                    | Session keep-alive ping (AJAX heartbeat)                       |
| `issue()`                              | L5738-6521   | `ret_billing`, issue tables                                            | `ret_billing`, issue tables, `ret_journal`                                                                                                           | Issue voucher CRUD (add/list/save/edit/update)                 |
| `item_delivery()`                      | L10192-10231 | delivery tables                                                        | delivery tables                                                                                                                                      | Delivery management                                            |
| `mobile_approval_request()`            | L11724-11751 | employee, approval                                                     | —                                                                                                                                                    | Mobile approval push                                           |
| `order_adtrnssendotp()`                | L11373-11437 | employee, branch                                                       | `ret_otp_approval`                                                                                                                                   | Order advance OTP                                              |
| `order_ad_trans_send_sms()`            | L11492-11503 | —                                                                      | —                                                                                                                                                    | Order advance SMS                                              |
| `order_delievery_sendotp()`            | L11505-11571 | employee, branch                                                       | `ret_otp_approval`                                                                                                                                   | Delivery OTP                                                   |
| `order_delievery_verify_otp()`         | L11574-11624 | `ret_otp_approval`                                                     | `ret_otp_approval`                                                                                                                                   | Verify delivery OTP                                            |
| `order_place()`                        | L9594-9719   | order tables                                                           | order tables, `ret_billing`                                                                                                                          | Place order from billing                                       |
| `order_verify_otp()`                   | L11440-11490 | `ret_otp_approval`                                                     | `ret_otp_approval`                                                                                                                                   | Verify order OTP                                               |
| `paymentmode_edit()`                   | L8306-8677   | `ret_billing`, `ret_billing_payment`                                   | `ret_billing`, `ret_billing_payment`, `ret_journal`                                                                                                  | Edit payment mode after save                                   |
| `phonepeCallback()`                    | L13481       | POS transaction tables                                                 | POS transaction tables                                                                                                                               | PhonePe POS payment callback handler                           |
| `posAuditTrail()`                      | L13787       | POS audit tables                                                       | —                                                                                                                                                    | POS audit trail view/AJAX                                      |
| `posSettings()`                        | L13571       | POS settings tables                                                    | POS settings tables                                                                                                                                  | POS settings CRUD                                              |
| `posSettlementSummary()`               | L13748       | POS transaction tables                                                 | —                                                                                                                                                    | POS settlement summary report                                  |
| `postcurlPOSRequests()`                | L13401       | —                                                                      | —                                                                                                                                                    | cURL wrapper for POS API calls                                 |
| `posTransactions()`                    | L13719       | POS transaction tables                                                 | —                                                                                                                                                    | POS transaction list view                                      |
| `receipt()`                            | L6523-7508   | `ret_billing`, receipt tables                                          | `ret_billing`, receipt tables, `ret_journal`                                                                                                         | Receipt voucher CRUD (add/list/save/edit/update)               |
| `remove_img()`                         | L193-210     | —                                                                      | filesystem                                                                                                                                           | Remove image                                                   |
| `repair_order_thermal_print()`         | L5685-5734   | repair order tables                                                    | —                                                                                                                                                    | Thermal print for repair                                       |
| `rrmdir()`                             | L172-191     | —                                                                      | filesystem                                                                                                                                           | Recursive dir removal                                          |
| `send_bill_cancel_otp()`               | L8698-8774   | employee, branch                                                       | `ret_otp_approval`                                                                                                                                   | Bill cancel OTP                                                |
| `send_credit_bill_otp()`               | L8982-9060   | employee, branch                                                       | `ret_otp_approval`                                                                                                                                   | Credit bill OTP                                                |
| `savePOSDevice()`                      | L13588       | POS device tables                                                      | POS device tables                                                                                                                                    | Save/create POS device                                         |
| `savePOSProvider()`                    | L13667       | POS provider tables                                                    | POS provider tables                                                                                                                                  | Save/create POS provider                                       |
| `send_sms()`                           | L5635-5646   | —                                                                      | —                                                                                                                                                    | SMS helper                                                     |
| `setDefaultPOSDevice()`                | L13643       | POS device tables                                                      | POS device tables                                                                                                                                    | Set a POS device as default                                    |
| `sendotp()`                            | L5534-5598   | employee, branch                                                       | `ret_otp_approval`                                                                                                                                   | Discount OTP                                                   |
| `service_bill()`                       | L9133-9500   | `ret_billing_service`, service tables                                  | `ret_billing_service`, `ret_billing_service_items`, `ret_billing_payment`                                                                            | Service bill CRUD                                              |
| `service_bill_invoice()`               | L9502-9535   | `ret_billing_service`                                                  | —                                                                                                                                                    | Service bill print                                             |
| `set_image()`                          | L95-112      | —                                                                      | filesystem                                                                                                                                           | Set image                                                      |
| `toggleProviderEnv()`                  | L13650       | POS provider tables                                                    | POS provider tables                                                                                                                                  | Toggle POS provider env (sandbox/live)                         |
| `update_branch()`                      | L9721-9862   | `ret_billing`, branch tables                                           | `ret_billing`                                                                                                                                        | Update bill's branch                                           |
| `update_delivery_status()`             | L10233-10265 | delivery tables                                                        | delivery tables                                                                                                                                      | Update delivery status                                         |
| `update_mydelivery_address()`          | L8226-8277   | —                                                                      | delivery address tables                                                                                                                              | Save delivery address                                          |
| `update_order_rate_type()`             | L12147-12170 | order tables                                                           | order tables, `ret_billing`                                                                                                                          | Update order rate type                                         |
| `update_otp()`                         | L5600-5633   | `ret_otp_approval`                                                     | `ret_otp_approval`                                                                                                                                   | Update OTP status                                              |
| `updateIRNDetails()`                   | L10693-10702 | —                                                                      | `ret_billing`                                                                                                                                        | Update IRN e-invoice details                                   |
| `updatePOSDevice()`                    | L13612       | POS device tables                                                      | POS device tables                                                                                                                                    | Update POS device record                                       |
| `updatePOSProvider()`                  | L13689       | POS provider tables                                                    | POS provider tables                                                                                                                                  | Update POS provider record                                     |
| `UploadBilledTransaction()`            | L12438       | `ret_billing`, POS transaction tables                                  | POS transaction tables                                                                                                                               | Upload billed transaction data to POS system                   |
| `updateNewCustomer()`                  | L5219-5247   | —                                                                      | `customer`                                                                                                                                           | Update customer inline                                         |
| `upload_img()`                         | L114-170     | —                                                                      | filesystem                                                                                                                                           | Upload image                                                   |
| `validate_huid()`                      | L11842-11853 | `ret_taging`                                                           | —                                                                                                                                                    | Validate HUID                                                  |
| `verify_advance_transfer_otp()`        | L10125-10175 | `ret_otp_approval`                                                     | advance tables                                                                                                                                       | Verify advance transfer OTP                                    |
| `verify_credit_coll_disc_otp()`        | L11677-11710 | `ret_otp_approval`                                                     | —                                                                                                                                                    | Verify credit coll disc OTP                                    |
| `verify_credit_otp()`                  | L9062-9120   | `ret_otp_approval`                                                     | `ret_otp_approval`                                                                                                                                   | Verify credit OTP                                              |
| `verify_otp()`                         | L8920-8978   | `ret_otp_approval`                                                     | `ret_otp_approval`                                                                                                                                   | Verify general OTP                                             |
| `verify_otp_for_billcancel()`          | L8776-8834   | `ret_otp_approval`                                                     | `ret_otp_approval`                                                                                                                                   | Verify cancel OTP                                              |
| `viewdb()`                             | L9537-9545   | `{any}`                                                                | —                                                                                                                                                    | Debug: view raw DB data                                        |

---

## 7b. Model Methods (Ret_billing_model) — Alphabetical

| Method                                                    | Lines        | Tables Read                                                                    | Tables Written                 | Called By                                    |
| --------------------------------------------------------- | ------------ | ------------------------------------------------------------------------------ | ------------------------------ | -------------------------------------------- |
| `addNewCompanyUsers($data)`                               | L7651-7676   | —                                                                              | `company_users`                | `addNewCompanyUsers()` ctrl                  |
| `ajax_getApprovalBillingList($data)`                      | L675-758     | `ret_billing`, `customer`, `branch`                                            | —                              | `billing('approvallist')`                    |
| `ajax_getBillingList($data)`                              | L523-673     | `ret_billing`, `customer`, `branch`, `ret_bill_details`                        | —                              | `billing('list')`                            |
| `ajax_getCashCollection()`                                | L11131-11270 | `ret_cash_collection`, `ret_billing`, `employee`                               | —                              | `cash_collection('list')`                    |
| `ajax_getCashCollectionList()`                            | L11273-11296 | `ret_cash_collection`                                                          | —                              | `cash_collection()`                          |
| `ajax_getIssuetist($data)`                                | L6606-6726   | `ret_billing` (issue type), `customer`, `branch`                               | —                              | `issue('list')`                              |
| `ajax_getReceiptlist($data)`                              | L6488-6604   | `ret_billing` (receipt type), `customer`, `branch`                             | —                              | `receipt('list')`                            |
| `ajax_getServiceBillList($data)`                          | L8669-8715   | `ret_billing_service`, `customer`                                              | —                              | `service_bill('list')`                       |
| `advance_details_order_no($orderno)`                      | L7249-7279   | `ret_billing_advance`, order tables                                            | —                              | `billing()` save flow                        |
| `bill_no_generate($id_branch, $is_eda)`                   | L7330-7351   | `ret_billing`                                                                  | —                              | `billing()` save                             |
| `CheckProductAvailability($id)`                           | L409-416     | `ret_taging`                                                                   | —                              | `billing()` save validation                  |
| `checkNonTagItemExist($data)`                             | L7113-7142   | `ret_non_tag_item`                                                             | —                              | `billing()` save                             |
| `checkSectionItemExist($data)`                            | L10230-10249 | section tables                                                                 | —                              | Non-tag item handling                        |
| `code_insurance_number_generator()`                       | L11500-11522 | `ret_billing`                                                                  | —                              | `billing_insurance()`                        |
| `code_number_generator($id_branch, $metal_type, $is_eda)` | L5221-5257   | `ret_billing`                                                                  | —                              | `billing()` save, `split_save`               |
| `createNewCustomer(...)`                                  | L2474-2592   | `customer`                                                                     | `customer`, `customer_advance` | `createNewCustomer()` ctrl                   |
| `deleteData($id_field, $id_value, $table)`                | L132-141     | —                                                                              | `{any}`                        | Multiple controllers                         |
| `encrypt($str)`                                           | L2465-2470   | —                                                                              | —                              | Password encryption                          |
| `generateRefNo(...)`                                      | L329-358     | `ret_billing`                                                                  | —                              | `billing()` save                             |
| `get_account_head()`                                      | L6056-6065   | `account_head`                                                                 | —                              | `get_account_head()` ctrl                    |
| `get_active_bill($bill_id)`                               | L8367-8371   | `ret_billing`                                                                  | —                              | `paymentmode_edit()`                         |
| `get_active_bill_list(...)`                               | L8333-8363   | `ret_billing`                                                                  | —                              | `paymentmode_edit()`                         |
| `get_activesize($id_product)`                             | L10912-10916 | `ret_size`                                                                     | —                              | `getactivesize()` ctrl                       |
| `get_advance_adjusted($bill_id)`                          | L2132-2151   | advance tables                                                                 | —                              | `billing()` edit                             |
| `get_advance_details(...)`                                | L5369-5501   | `ret_billing`, advance, scheme tables                                          | —                              | `get_advance_details()` ctrl                 |
| `get_advance_refund($bill_id)`                            | L2153-2165   | advance refund tables                                                          | —                              | `billing()` edit                             |
| `get_adv_order_details(...)`                              | L11624-11637 | advance order tables                                                           | —                              | Order advance flow                           |
| `get_all_ledgers()`                                       | L11639-11644 | `ret_ledger`                                                                   | —                              | `bank_ledger_transfer()`                     |
| `get_bank_acc_details()`                                  | L8170-8181   | `bank_accounts`                                                                | —                              | `get_bank_acc_details()` ctrl                |
| `get_bill_detail($bill_id)`                               | L7081-7100   | `ret_billing`, `ret_bill_details`                                              | —                              | `cancel_bill()`, `receipt()`                 |
| `get_bill_detail_other_inv(...)`                          | L7102-7111   | `ret_bill_details`                                                             | —                              | Other inventory handling                     |
| `get_bill_ids(...)`                                       | L11592-11612 | `ret_billing`                                                                  | —                              | Credit collection                            |
| `get_bill_no(...)`                                        | L360-381     | `ret_billing`                                                                  | —                              | Bill number lookup                           |
| `get_bill_no_format_detail(...)`                          | L10484-10626 | `ret_billing_format`, `ret_billing`                                            | —                              | `bill_number_format()`                       |
| `get_bill_stone_details($bill_id)`                        | L2115-2128   | `ret_billing_item_stones`                                                      | —                              | `billing()` edit                             |
| `get_billing_adj_details($id)`                            | L10264-10283 | advance adj tables                                                             | —                              | Advance adjustment                           |
| `get_billing_advance_details($bill_id)`                   | L2013-2111   | `ret_billing_advance`, multiple                                                | —                              | `billing()` edit                             |
| `get_BillAmount($bill_id)`                                | L5962-5969   | `ret_billing`                                                                  | —                              | Various                                      |
| `getBill_details($bill_id)`                               | L1102-1113   | `ret_billing`                                                                  | —                              | Invoice print                                |
| `getBillingDetTaxPer($tgrp_id)`                           | L10316-10331 | `tax_group`, `tax_group_items`                                                 | —                              | Tax calc                                     |
| `getBillingDetails($bill_id, $type)`                      | L824-978     | `ret_billing`, `ret_bill_details`, `ret_billing_payment`, `customer`, `branch` | —                              | `getBillingDetails()` ctrl, invoice          |
| `getBilling_details(...)`                                 | L5990-6005   | `ret_billing`                                                                  | —                              | Report query                                 |
| `getBillingMetalrate(...)`                                | L787-822     | `metal_rate`                                                                   | —                              | `billing()` add/edit                         |
| `getBillData(...)`                                        | L5507-5750   | `ret_billing`, `ret_bill_details`, `ret_billing_payment`                       | —                              | `getBillDetails()` ctrl                      |
| `getBillDetailsData($billId)`                             | L8568-8576   | `ret_bill_details`                                                             | —                              | e-Invoice                                    |
| `getBill_refnumbers($bill_id)`                            | L11614-11623 | `ret_billing`                                                                  | —                              | Ref number lookup                            |
| `getBranchDayClosingData($id_branch)`                     | L2736-2743   | `day_closing`                                                                  | —                              | `getBranchDayClosingData()` ctrl             |
| `getBrnachOtpRegMobile(...)`                              | L9436-9443   | `branch`                                                                       | —                              | OTP flows                                    |
| `get_borrower_details(...)`                               | L6067-6152   | `customer`, borrower tables                                                    | —                              | `get_borrower()` ctrl                        |
| `get_branch($id_branch)`                                  | L188-195     | `branch`                                                                       | —                              | Branch lookup                                |
| `get_branch_details($id_branch)`                          | L387-405     | `branch`, company                                                              | —                              | `billing()` add                              |
| `get_bt_details($bt_id)`                                  | L11026-11071 | bank transfer tables                                                           | —                              | `bank_ledger_transfer()`                     |
| `get_cashCollectionDetails($id)`                          | L11299-11308 | `ret_cash_collection`                                                          | —                              | `cash_collection('edit')`                    |
| `get_charges($tag_id)`                                    | L7891-7898   | `ret_taging_charges`                                                           | —                              | Tag charges                                  |
| `get_closed_accounts(...)`                                | L5298-5363   | scheme account tables                                                          | —                              | Closed accounts lookup                       |
| `get_credit_collection_details(...)`                      | L7684-7721   | credit collection tables                                                       | —                              | Credit collection                            |
| `get_credit_old_metal_amount(...)`                        | L5918-5931   | old metal tables                                                               | —                              | Credit bill calc                             |
| `get_credit_pay_amount(...)`                              | L5935-5958   | `ret_billing_payment`                                                          | —                              | Credit bill payment                          |
| `get_credit_pending_details(...)`                         | L9164-9323   | `ret_billing`, credit tables                                                   | —                              | `getCreditPending()` ctrl                    |
| `getCreditBill(...)`                                      | L6269-6284   | `ret_billing`                                                                  | —                              | Credit bill search                           |
| `getCreditBillDetails(...)`                               | L5848-5914   | `ret_billing`, `ret_bill_details`                                              | —                              | `getCreditBillDetails()` ctrl                |
| `getCreditCollection($bill_id)`                           | L9327-9380   | credit collection tables                                                       | —                              | Credit collection detail                     |
| `getCreditPending($data)`                                 | L9581-9773   | `ret_billing`, credit tables                                                   | —                              | Credit pending DataTable                     |
| `get_cus_advance_details(...)`                            | L6156-6171   | advance tables                                                                 | —                              | Customer advance                             |
| `get_customer($id)`                                       | L486-521     | `customer`                                                                     | —                              | `billing()` edit                             |
| `get_customer_credit_details($data)`                      | L8375-8416   | `ret_billing`, credit                                                          | —                              | `get_customer_credit_details()` ctrl         |
| `get_customer_details($id_customer)`                      | L10289-10314 | `customer`                                                                     | —                              | Customer detail                              |
| `get_customer_order_details(...)`                         | L11542-11551 | order tables                                                                   | —                              | Order rate type                              |
| `get_customer_reg_add($id_customer)`                      | L8200-8219   | `customer`                                                                     | —                              | Registered address                           |
| `get_customer_weight_scheme_details($data)`               | L8103-8138   | scheme tables                                                                  | —                              | Weight scheme                                |
| `get_customer_wise_tcs_percent(...)`                      | L10676-10701 | TCS tables                                                                     | —                              | TCS calc                                     |
| `getCustomerDet(...)`                                     | L8893-9158   | `customer`, `ret_billing`, many tables                                         | —                              | **265 lines** — customer detail with history |
| `getCustomerpaymentDetails(...)`                          | L10745-10867 | `ret_billing`, `ret_billing_payment`                                           | —                              | Customer payment history                     |
| `getCustomerSalesDetails($data)`                          | L9781-9816   | `ret_billing`, `ret_bill_details`                                              | —                              | Sales DataTable                              |
| `getCusDelivery_address(...)`                             | L8223-8242   | `delivery_address`                                                             | —                              | Delivery address                             |
| `get_data()`                                              | L10630-10637 | varies                                                                         | —                              | Generic data getter                          |
| `get_DeliveryList($data)`                                 | L10137-10193 | delivery tables                                                                | —                              | Delivery DataTable                           |
| `get_denomination()`                                      | L11121-11129 | `denomination`                                                                 | —                              | Cash collection                              |
| `get_deposit_type_bill_no(...)`                           | L10647-10675 | `ret_billing`                                                                  | —                              | Deposit type bill number                     |
| `getDenomination($id)`                                    | L11310-11319 | `denomination`                                                                 | —                              | Cash collection detail                       |
| `get_einvoiceirndetails()`                                | L10470-10478 | `ret_billing`                                                                  | —                              | e-Invoice IRN                                |
| `get_empty_record()`                                      | L2176-2400   | —                                                                              | —                              | **224 lines** — default empty billing record |
| `get_employee_settings($id)`                              | L7938-7950   | `employee`, profile                                                            | —                              | Employee settings                            |
| `get_entry_records($est_id)`                              | L762-783     | `ret_estimation_items`                                                         | —                              | Entry records                                |
| `get_est_adv_details($id)`                                | L7033-7040   | estimation advance                                                             | —                              | Receipt advance                              |
| `get_est_adv_tag_details($id)`                            | L7044-7051   | estimation tag advance                                                         | —                              | Receipt tag advance                          |
| `get_est_other_metal_details(...)`                        | L11321-11345 | estimation other metal                                                         | —                              | Other metal details                          |
| `get_est_split_details($data)`                            | L9854-10135  | `ret_billing`, `ret_bill_details`, many                                        | —                              | **281 lines** — bill split details           |
| `get_esti_status($est_item_id)`                           | L156-163     | `ret_estimation_items`                                                         | —                              | Status check                                 |
| `get_estimation_other_metal_details(...)`                 | L4831-4854   | estimation other metals                                                        | —                              | Estimation metals                            |
| `getEstimationDetails(...)`                               | L2745-4439   | `ret_estimation`, `ret_estimation_items`, `ret_taging`, many                   | —                              | **1,694 lines** — MEGA method                |
| `getEstimationDetailsTags(...)`                           | L4480-4779   | `ret_estimation`, `ret_taging`, many                                           | —                              | **299 lines** — tagged items                 |
| `get_EstimationPackingItems(...)`                         | L4455-4476   | packing items table                                                            | —                              | Packing items                                |
| `get_existingAuthToken()`                                 | L10436-10442 | company auth                                                                   | —                              | e-Invoice auth                               |
| `getAvailableCustomers($SearchTxt)`                       | L4965-4973   | `customer`                                                                     | —                              | Customer search                              |
| `getAvailableIndCustomers(...)`                           | L8269-8305   | `customer`                                                                     | —                              | Industrial customer search                   |
| `get_FinancialYear()`                                     | L418-425     | `financial_year`                                                               | —                              | Current FY                                   |
| `GetFinancialYear()`                                      | L2404-2411   | `financial_year`                                                               | —                              | Duplicate of above                           |
| `get_gift_issue_details(...)`                             | L7582-7598   | gift issue tables                                                              | —                              | Gift issue                                   |
| `get_gift_voucher_settings()`                             | L440-447     | `gift_voucher_settings`                                                        | —                              | GV settings                                  |
| `get_headOffice()`                                        | L8859-8866   | `branch`                                                                       | —                              | Head office branch                           |
| `get_homebill_counters($data)`                            | L11347-11361 | counter tables                                                                 | —                              | Home bill counters                           |
| `get_insurance_last_code_no(...)`                         | L11524-11540 | `ret_billing`                                                                  | —                              | Insurance code                               |
| `get_InventoryCategory(...)`                              | L7969-7982   | inventory category                                                             | —                              | Inventory lookup                             |
| `get_IssueCreditCollectionDetails(...)`                   | L9382-9399   | issue credit collection                                                        | —                              | Issue credit                                 |
| `get_issue_details($id)`                                  | L6772-6844   | issue tables                                                                   | —                              | Issue detail                                 |
| `get_last_code_no(...)`                                   | L5259-5283   | `ret_billing`                                                                  | —                              | Last bill code                               |
| `get_last_service_bill_no(...)`                           | L8644-8665   | `ret_billing_service`                                                          | —                              | Last service bill                            |
| `get_maxcash_settings()`                                  | L4443-4451   | settings table                                                                 | —                              | Max cash settings                            |
| `get_max_bill_no(...)`                                    | L7355-7379   | `ret_billing`                                                                  | —                              | Max bill number                              |
| `get_mc_va_limit(...)`                                    | L8418-8564   | MC/VA limit tables                                                             | —                              | **146 lines** — MC/VA limit calc             |
| `get_metal_details($id_metal)`                            | L7925-7932   | `metal_types`                                                                  | —                              | Metal details                                |
| `get_mydelivery_address(...)`                             | L8246-8263   | delivery address                                                               | —                              | My delivery address                          |
| `getMetalTypes()`                                         | L5160-5166   | `metal_types`                                                                  | —                              | Metal type list                              |
| `get_old_estimation_details(...)`                         | L8005-8012   | old estimation                                                                 | —                              | Old estimation                               |
| `get_old_esti_status(...)`                                | L167-174     | old estimation items                                                           | —                              | Old est status                               |
| `get_old_metal_est_details(...)`                          | L9822-9833   | old metal estimation                                                           | —                              | Old metal est                                |
| `get_old_metal_stone_details(...)`                        | L4875-4894   | old metal stones                                                               | —                              | Old metal stones                             |
| `getOldMetalRate($id_metal)`                              | L7602-7609   | `metal_rate`                                                                   | —                              | Old metal rate                               |
| `getOld_sales_detail(...)`                                | L9413-9432   | old sales                                                                      | —                              | Old sales detail                             |
| `getOld_sales_details(...)`                               | L7725-7742   | old sales                                                                      | —                              | Old sales details                            |
| `get_one_time_pre_weight_scheme()`                        | L8092-8099   | scheme tables                                                                  | —                              | Weight scheme                                |
| `get_order_adj_details(...)`                              | L1063-1070   | order adj tables                                                               | —                              | Order adj                                    |
| `get_order_advance($order_no)`                            | L2167-2174   | order advance                                                                  | —                              | Order advance                                |
| `get_order_advance_details(...)`                          | L11426-11440 | order advance                                                                  | —                              | Order advance detail                         |
| `get_order_id_details(...)`                               | L8881-8887   | order tables                                                                   | —                              | Order ID                                     |
| `get_order_rate(...)`                                     | L10879-10910 | order rate tables                                                              | —                              | Order rate                                   |
| `get_order_stones($order_no)`                             | L1991-2009   | order stones                                                                   | —                              | Order stones                                 |
| `get_ord_adv_adj($bill_id)`                               | L982-1005    | order advance adj                                                              | —                              | Order advance adj                            |
| `getOtherEstimateItemsDetails(...)`                       | L1255-1987   | `ret_bill_details`, many                                                       | —                              | **732 lines** — other est items              |
| `get_other_estcharges(...)`                               | L7902-7917   | estimation charges                                                             | —                              | Est charges                                  |
| `get_other_inventory_purchase_items_details(...)`         | L7986-8001   | inventory items                                                                | —                              | Inv items                                    |
| `get_other_metal_details($tagid)`                         | L4805-4829   | other metals                                                                   | —                              | Tag other metals                             |
| `get_otp_profile_settings(...)`                           | L8718-8726   | profile OTP settings                                                           | —                              | OTP config                                   |
| `get_paid_bill($bill_id)`                                 | L1089-1100   | `ret_billing`                                                                  | —                              | Paid bill check                              |
| `get_partial_sale_det($tag_id)`                           | L7283-7297   | partial sale                                                                   | —                              | Partial sale                                 |
| `get_payModes()`                                          | L5287-5294   | `ret_payment_modes`                                                            | —                              | Payment modes                                |
| `getPaymentDetails($bill_id)`                             | L1178-1253   | `ret_billing_payment`                                                          | —                              | Payment details                              |
| `get_payment_device_details()`                            | L8185-8192   | `payment_devices`                                                              | —                              | POS devices                                  |
| `get_petty_cash_emp($data)`                               | L11479-11491 | petty cash emp                                                                 | —                              | Petty cash                                   |
| `get_petty_cash_issue_amt($data)`                         | L11442-11477 | petty cash                                                                     | —                              | Petty cash amt                               |
| `get_previous_credit_collections(...)`                    | L11576-11591 | credit collection                                                              | —                              | Credit coll history                          |
| `get_previous_order_details(...)`                         | L9507-9575   | order tables                                                                   | —                              | Previous order                               |
| `get_prev_ref_no($ref_no)`                                | L11073-11089 | `ret_billing`                                                                  | —                              | Prev ref no                                  |
| `getPreviousDateStatuslog(...)`                           | L10736-10741 | status log                                                                     | —                              | Status log                                   |
| `get_profile_settings(...)`                               | L7954-7961   | profile                                                                        | —                              | Profile settings                             |
| `getProductBySearch(...)`                                 | L5118-5136   | `product`                                                                      | —                              | Product search                               |
| `getProductDesignBySearch(...)`                           | L5138-5158   | `product_design`                                                               | —                              | Design search                                |
| `get_purchase_details(...)`                               | L8590-8613   | purchase tables                                                                | —                              | Purchase details                             |
| `get_purchase_stone(...)`                                 | L11364-11386 | purchase stones                                                                | —                              | Purchase stones                              |
| `get_purchase_stone_tag(...)`                             | L11388-11408 | purchase stone tags                                                            | —                              | Purchase stone tags                          |
| `get_purchase_voucher(...)`                               | L7499-7517   | purchase voucher                                                               | —                              | Purchase voucher                             |
| `get_purity_details($purity)`                             | L8155-8162   | `purity`                                                                       | —                              | Purity details                               |
| `get_receipt_advance_adj_details(...)`                    | L7022-7029   | receipt advance adj                                                            | —                              | Receipt adj                                  |
| `get_receipt_advance_details($id)`                        | L6847-6885   | receipt advance                                                                | —                              | Receipt advance                              |
| `get_receipt_details($id)`                                | L6940-7019   | receipt tables                                                                 | —                              | Receipt detail                               |
| `get_receipt_no_against_purchase(...)`                    | L10703-10715 | receipt tables                                                                 | —                              | Receipt vs purchase                          |
| `get_receipt_payment($id)`                                | L7055-7073   | payment tables                                                                 | —                              | Receipt payment                              |
| `get_receipt_refund(...)`                                 | L6177-6263   | refund tables                                                                  | —                              | Receipt refund                               |
| `get_redeem_details($id)`                                 | L7556-7578   | redeem tables                                                                  | —                              | Redeem details                               |
| `get_rep_ord_stone_details(...)`                          | L11410-11424 | repair order stones                                                            | —                              | Repair stones                                |
| `get_ret_settings($settings)`                             | L429-436     | `ret_settings`                                                                 | —                              | Retail settings                              |
| `get_retSettings()`                                       | L2413-2461   | `ret_settings`                                                                 | —                              | All retail settings                          |
| `getreturnBillData(...)`                                  | L5754-5840   | `ret_billing`, return tables                                                   | —                              | Return bill data                             |
| `get_retWallet_details(...)`                              | L6286-6299   | wallet tables                                                                  | —                              | Wallet details                               |
| `get_branchwise_rate($id_branch)`                         | L451-482     | `branch_metal_rate`                                                            | —                              | Branch metal rates                           |
| `get_due_bill($bill_id)`                                  | L1074-1085   | `ret_billing`                                                                  | —                              | Due bill check                               |
| `get_sales_return_bill_no(...)`                           | L1036-1057   | `ret_billing`                                                                  | —                              | Sales return bill no                         |
| `get_sale_est_details(...)`                               | L9837-9848   | estimation tables                                                              | —                              | Sale est details                             |
| `getSearchCompanyUsers(...)`                              | L7632-7647   | `company_users`                                                                | —                              | Company user search                          |
| `getServiceBillingDetails($id)`                           | L8728-8759   | `ret_billing_service`                                                          | —                              | Service bill detail                          |
| `getServiceBillItemDetails($id)`                          | L8811-8828   | `ret_billing_service_items`                                                    | —                              | Service items                                |
| `getServiceBillPaymentDetails(...)`                       | L8763-8807   | `ret_billing_payment` (service)                                                | —                              | Service payment                              |
| `get_stone_details($est_item_id)`                         | L4781-4802   | `ret_estimation_stones`                                                        | —                              | Estimation stones                            |
| `get_tag_details($tag_id)`                                | L7299-7326   | `ret_taging`                                                                   | —                              | Tag details                                  |
| `get_tag_status($tag_id)`                                 | L145-152     | `ret_taging`                                                                   | —                              | Tag status check                             |
| `get_tag_stone_details($tag_id)`                          | L4856-4873   | `ret_taging_stones`                                                            | —                              | Tag stones                                   |
| `getTagDetails($tag_id, $est_id)`                         | L7803-7826   | `ret_taging`, `ret_estimation`                                                 | —                              | Tag+est details                              |
| `getTaggingBySearch($SearchTxt)`                          | L4975-5116   | `ret_taging`, many                                                             | —                              | **141 lines** — tag search                   |
| `getTagImageDetails($tag_id)`                             | L10717-10724 | `ret_taging`                                                                   | —                              | Tag image                                    |
| `get_tax_group_from_billing()`                            | L10869-10875 | tax group                                                                      | —                              | Tax group                                    |
| `get_test_datas()`                                        | L10918-10922 | varies                                                                         | —                              | Test/debug                                   |
| `get_total_advance_for_order(...)`                        | L11559-11574 | advance tables                                                                 | —                              | Total order advance                          |
| `get_trans_code($trans_id)`                               | L11010-11024 | transaction code                                                               | —                              | Transaction code                             |
| `get_wallet_acc_number()`                                 | L7851-7867   | wallet accounts                                                                | —                              | Wallet acc no                                |
| `get_wallet_account($id_employee)`                        | L7830-7847   | wallet accounts                                                                | —                              | Wallet account                               |
| `getWalletTransDetails($bill_id)`                         | L7871-7878   | wallet trans                                                                   | —                              | Wallet transactions                          |
| `getWalletTransTagDetails($tag_id)`                       | L7882-7889   | wallet trans tags                                                              | —                              | Wallet tag trans                             |
| `getAdvanceAdjusted_Details(...)`                         | L7769-7776   | advance adj                                                                    | —                              | Advance adjusted                             |
| `getApprovallists($approval_type)`                        | L10931-10992 | approval tables                                                                | —                              | Approval lists                               |
| `getApprovaldetails($apprl_id)`                           | L10995-11004 | approval tables                                                                | —                              | Approval detail                              |
| `get_approval_tag_details($tag_id)`                       | L8870-8877   | `ret_taging`                                                                   | —                              | Approval tag                                 |
| `getChitPayDetails($bill_id)`                             | L8142-8149   | chit payment                                                                   | —                              | Chit payment                                 |
| `getChitUtilized($bill_id)`                               | L7615-7622   | chit utilized                                                                  | —                              | Chit utilized                                |
| `getCompanyDetails($id_branch)`                           | L6007-6048   | company, branch                                                                | —                              | Company details                              |
| `getCompanyPurchaseAmount(...)`                           | L7780-7795   | `ret_billing`                                                                  | —                              | Company purchase                             |
| `get_currentBranchName(...)`                              | L5177-5190   | `branch`                                                                       | —                              | Branch name                                  |
| `get_currentBranches(...)`                                | L5192-5217   | `branch`                                                                       | —                              | Current branches                             |
| `get_adjust_in_sales($bill_id)`                           | L1007-1032   | adjust tables                                                                  | —                              | Adjust in sales                              |
| `getbillingdetailsitems($billId)`                         | L10335-10384 | `ret_bill_details`                                                             | —                              | e-Invoice items                              |
| `getbillingInfobybillId($billId)`                         | L10390-10406 | `ret_billing`                                                                  | —                              | e-Invoice info                               |
| `getbilltotalvaluesdetails(...)`                          | L10410-10432 | `ret_billing`, `ret_bill_details`                                              | —                              | e-Invoice values                             |
| `GeneralGiftRedeemProduct($id)`                           | L7532-7539   | gift tables                                                                    | —                              | General gift redeem                          |
| `CheckRedeemProduct($id)`                                 | L7521-7528   | gift tables                                                                    | —                              | Redeem check                                 |
| `getAllTaxgroupItems()`                                   | L4944-4963   | `tax_group_items`                                                              | —                              | Tax items                                    |
| `getInsuranceDetails($id)`                                | L11494-11498 | insurance tables                                                               | —                              | Insurance details                            |
| `getUOMDetails()`                                         | L5168-5175   | `uom`                                                                          | —                              | UOM list                                     |
| `gift_voucher_master(...)`                                | L7477-7495   | `gift_voucher`                                                                 | —                              | Gift voucher master                          |
| `get_VoucherDetails(...)`                                 | L7383-7473   | gift voucher tables                                                            | —                              | **90 lines** — voucher details               |
| `insertBatchData($data, $table)`                          | L102-117     | —                                                                              | `{any}`                        | Batch insert                                 |
| `insertData($data, $table)`                               | L17-51       | —                                                                              | `{any}`                        | Generic insert                               |
| `isEmptySetDefault(...)`                                  | L10726-10734 | —                                                                              | —                              | Utility                                      |
| `is_estno_already_billed(...)`                            | L10197-10226 | `ret_estimation`, `ret_billing`                                                | —                              | Duplicate check                              |
| `max_metalrate()`                                         | L5973-5986   | `metal_rate`                                                                   | —                              | Max metal rate                               |
| `no_to_words($no)`                                        | L7156-7185   | —                                                                              | —                              | Number to words                              |
| `no_to_words1($nos1)`                                     | L7189-7247   | —                                                                              | —                              | Number to words (alt)                        |
| `otp_app_approval()`                                      | L10924-10928 | approval tables                                                                | —                              | OTP approval                                 |
| `ret_bill_return_details(...)`                            | L7545-7552   | return details                                                                 | —                              | Return detail                                |
| `service_bill_number_generator(...)`                      | L8621-8642   | `ret_billing_service`                                                          | —                              | Service bill no                              |
| `stone_details_by_bill_det_id(...)`                       | L4921-4940   | `ret_billing_item_stones`                                                      | —                              | Stones by det ID                             |
| `stone_details_by_bill_id(...)`                           | L4896-4917   | `ret_billing_item_stones`                                                      | —                              | Stones by bill ID                            |
| `get_repair_item_details(...)`                            | L8834-8853   | repair items                                                                   | —                              | Repair items                                 |
| `updateBatchData(...)`                                    | —            | —                                                                              | `{any}`                        | Batch update                                 |
| `updatecompanyauthtoken(...)`                             | L10446-10452 | —                                                                              | company auth                   | Auth token update                            |
| `updatebilleinvoicedetails(...)`                          | L10456-10466 | —                                                                              | `ret_billing`                  | e-Invoice update                             |
| `updateData(...)`                                         | L53-89       | —                                                                              | `{any}`                        | Generic update                               |
| `updateNewCustomer(...)`                                  | L2596-2732   | —                                                                              | `customer`                     | Customer update                              |
| `deletePOSDevice($id)`                                    | L11954       | POS tables                                                                     | POS device tables               | Delete POS device (model)                    |
| `getActivePOSTransaction($ref)`                           | L12039       | POS transaction tables                                                         | —                              | Get active/pending POS transaction           |
| `getAllPOSDevicesWithProvider()`                           | L11923       | POS device + provider tables                                                   | —                              | All POS devices with provider details        |
| `getAllPOSProviders()`                                    | L11911       | POS provider tables                                                             | —                              | All POS providers                            |
| `getAllPOSTransactions($data)`                            | L12001       | POS transaction tables                                                          | —                              | POS transaction DataTable query              |
| `getcusLastMobile($id_customer)`                         | L11896       | `customer`                                                                     | —                              | Fetch customer's last mobile number          |
| `get_credit_history_for_print($bill_id)`                 | L11783       | `ret_billing`, credit tables                                                   | —                              | Credit history for print view                |
| `get_ledger_current_balance($ledger_id)`                 | L11732       | `ret_ledger`, ledger tables                                                    | —                              | Current balance for a ledger account         |
| `get_ledger_transfer_list($data)`                        | L11774       | `ret_ledger`, bank transfer tables                                              | —                              | Ledger transfer DataTable list               |
| `get_total_returned_amount_by_bill($bill_id)`            | L7629        | `ret_billing`                                                                  | —                              | Total returned amount for a bill             |
| `getPhonePeSaltKey($provider_id)`                        | L12090       | POS provider tables                                                             | —                              | Retrieve PhonePe salt key for POS            |
| `getPOSApiUrl($provider_id)`                             | L11889       | POS provider tables                                                             | —                              | Get POS provider API URL                     |
| `getPOSAuditTrail($data)`                                | L12156       | POS audit tables                                                                | —                              | POS audit trail DataTable query              |
| `getPOSDeviceById($id)`                                  | L11934       | POS device tables                                                               | —                              | Fetch single POS device by ID                |
| `getPOSDeviceDetails($id)`                               | L11855       | POS device tables                                                               | —                              | Full POS device details                      |
| `getPOSDeviceList()`                                     | L11836       | POS device tables                                                               | —                              | All POS devices list                         |
| `getPOSMachineRequired()`                                | L11903       | POS settings tables                                                             | —                              | Check if POS machine is required             |
| `getPOSProvider($data)`                                  | L11875       | POS provider tables                                                             | —                              | POS provider lookup                          |
| `getPOSProviderById($id)`                                | L11917       | POS provider tables                                                             | —                              | Single POS provider by ID                    |
| `getPOSSettlementSummary($data)`                         | L12122       | POS transaction tables                                                          | —                              | Settlement summary totals                    |
| `getPOSTransactionById($id)`                             | L12019       | POS transaction tables                                                          | —                              | Single POS transaction by ID                 |
| `getPOSTransactionByRef($ref)`                           | L12073       | POS transaction tables                                                          | —                              | POS transaction by reference number          |
| `process_ledger_transfer($data)`                         | L11766       | ledger tables                                                                   | ledger tables                  | Process bank-to-ledger transfer              |
| `savePOSDevice($data)`                                   | L11940       | POS device tables                                                               | POS device tables              | Insert new POS device                        |
| `savePOSProvider($data)`                                 | L11978       | POS provider tables                                                             | POS provider tables            | Insert new POS provider                      |
| `setDefaultPOSDevice($id)`                               | L11961       | POS device tables                                                               | POS device tables              | Mark POS device as default                   |
| `toggleProviderEnv($id)`                                 | L11971       | POS provider tables                                                             | POS provider tables            | Toggle provider sandbox/live env             |
| `updatePOSDevice($id, $data)`                            | L11947       | POS device tables                                                               | POS device tables              | Update POS device record                     |
| `updatePOSProvider($id, $data)`                          | L11985       | POS provider tables                                                             | POS provider tables            | Update POS provider record                   |
| `updateNTData($data, $arith)`                             | L7144-7152   | —                                                                              | non-tag item                   | Non-tag update                               |
| `updatePurItemData(...)`                                  | L8076-8084   | —                                                                              | purchase items                 | Purchase item update                         |
| `updatesecNTData($data, $arith)`                          | L10251-10260 | —                                                                              | section non-tag                | Section NT update                            |
| `updateWalletData($data, $arith)`                         | L7752-7761   | —                                                                              | wallet                         | Wallet update                                |
| `update_customer_order_rate(...)`                         | L11553-11557 | —                                                                              | order                          | Order rate update                            |
| `validate_huid($tag_id, $huid)`                           | L11092-11118 | `ret_taging`                                                                   | —                              | HUID validation                              |
| `viewdb($post_data)`                                      | L176-184     | `{any}`                                                                        | —                              | Debug query                                  |

---

## 7c. JS → Controller AJAX Map (Round 2)

> Extracted from `admin/assets/js/ret_billing.js` (44,682 lines, 1.4MB). Grep fails on this file due to encoding (likely UTF-16 BOM) — extracted via section sampling.

### Billing Core AJAX

| JS Function                        | HTTP | Backend Endpoint                                | Data Sent                                                                                  | Controller Method               |
| ---------------------------------- | ---- | ----------------------------------------------- | ------------------------------------------------------------------------------------------ | ------------------------------- |
| `get_billing_list()`               | POST | `admin_ret_billing/billing/ajax`                | `dt_range`, `bill_no`, `id_branch`, `order_status`                                         | `billing('list')`               |
| `get_approval_billing_list()`      | POST | `admin_ret_billing/billing/ajaxapprovallist`    | `dt_range`, `bill_no`, `id_branch`, `order_status`                                         | `billing('approvallist')`       |
| `get_branch_details()`             | POST | `admin_ret_billing/get_branch_details`          | `id_branch`                                                                                | `get_branch_details()`          |
| `get_branch_day_closing_details()` | POST | `admin_ret_billing/getBranchDayClosingData`     | `id_branch`                                                                                | `getBranchDayClosingData()`     |
| `getSearchCustomer(searchTxt)`     | POST | `admin_ret_billing/get_borrower`                | `searchTxt`, `id_branch`, `issue_to`, `issue_type`, `is_eda`, `receipt_to`, `receipt_type` | `get_borrower()`                |
| `get_customer_credit_details(id)`  | POST | `admin_ret_billing/get_customer_credit_details` | `id_customer`, `id_employee`, `id_karigar`, `receipt_type`, `receipt_to`                   | `get_customer_credit_details()` |
| `get_advance_details()`            | POST | `admin_ret_billing/get_advance_details`         | `bill_cus_id`, `is_eda`, `id_branch`                                                       | `get_advance_details()`         |
| `oldget_advance_details()`         | POST | `admin_ret_billing/get_advance_details`         | `bill_cus_id`, `is_eda`, `id_branch`                                                       | `get_advance_details()`         |

### OTP & Approval AJAX

| JS Function                     | HTTP | Backend Endpoint                                | Data Sent               | Controller Method               |
| ------------------------------- | ---- | ----------------------------------------------- | ----------------------- | ------------------------------- |
| `at_send_otp()`                 | POST | `admin_ret_billing/adtrnssendotp`               | `mobile`, `send_resend` | `adtrnssendotp()`               |
| `verify_advance_transfer_otp()` | POST | `admin_ret_billing/verify_advance_transfer_otp` | `otp`                   | `verify_advance_transfer_otp()` |

### Cross-Module AJAX (called from billing JS but hitting other controllers)

| JS Function                        | HTTP | Backend Endpoint                                 | Data Sent   | Controller Method                                   |
| ---------------------------------- | ---- | ------------------------------------------------ | ----------- | --------------------------------------------------- |
| `get_metal()`                      | GET  | `get/active_metals`                              | —           | `Get::active_metals()`                              |
| `get_ActiveMetal()`                | GET  | `admin_ret_catalog/ret_product/active_metal`     | —           | `Admin_ret_catalog::ret_product('active_metal')`    |
| `get_received_lots()`              | GET  | `admin_ret_tagging/get_lot_ids`                  | —           | `Admin_ret_tagging::get_lot_ids()`                  |
| `get_tag_types()`                  | GET  | `admin_ret_tagging/get_tag_types`                | —           | `Admin_ret_tagging::get_tag_types()`                |
| `get_tag_purities()`               | GET  | `admin_ret_catalog/purity/active_purities`       | —           | `Admin_ret_catalog::purity('active_purities')`      |
| `get_metal_rates_by_branch()`      | POST | `admin_ret_tagging/get_metal_rates_by_branch`    | `id_branch` | `Admin_ret_tagging::get_metal_rates_by_branch()`    |
| `getDesignPurityByDesignId(id)`    | POST | `admin_ret_tagging/getDesignPurityByDesignId`    | `designId`  | `Admin_ret_tagging::getDesignPurityByDesignId()`    |
| `getDesignStoneDetails(id)`        | POST | `admin_ret_tagging/getDesignStonesByDesignId`    | `designId`  | `Admin_ret_tagging::getDesignStonesByDesignId()`    |
| `getDesignMaterialsByDesignId(id)` | POST | `admin_ret_tagging/getDesignMaterialsByDesignId` | `designId`  | `Admin_ret_tagging::getDesignMaterialsByDesignId()` |
| `load_tag_stone_list_on_edit()`    | POST | `admin_ret_tagging/getTagStoneByTagId`           | `tagId`     | `Admin_ret_tagging::getTagStoneByTagId()`           |

### Key JS Calculation Functions (No AJAX — Client-Side)

| Function                               | Lines (approx) | Purpose                                                        | Risk                 |
| -------------------------------------- | -------------- | -------------------------------------------------------------- | -------------------- |
| `calculateSaleBillRowTotal()`          | ~28000-28500   | Sale line item total (metal rate × weight + MC + stones + tax) | 🔴 Core financial    |
| `calculatePurchaseBillRowTotal()`      | ~29600+        | Purchase line total (old metal calc)                           | 🔴 Core financial    |
| `calculate_chit_closing_balance()`     | ~16800-17400   | Weight scheme closure (VA+MC benefits, board rate)             | 🔴 Complex financial |
| `calculate_est_chit_closing_balance()` | ~17400-17800   | Estimation-based scheme closure                                | 🔴 Complex financial |
| `calculate_advance_adjust_amount()`    | ~19848-19886   | Advance adjustment balance calc                                | 🟡 Financial         |
| `calculate_advance_transfer_amount()`  | ~36267-36293   | Advance transfer running total                                 | 🟡 Financial         |
| `calculateRepairOrderDetails()`        | ~25989-26120   | Repair order tax calc (reverse-calculate taxable amount)       | 🟡 Financial         |
| `calculateOrderColumnTotal()`          | ~16207         | Order column totals                                            | 🟡 Financial         |
| `check_mc_va_limit()`                  | ~29050-29390   | MC/VA discount limit enforcement                               | 🔴 Business rule     |
| `DeliveryStatusChange()`               | ~36487-36500   | Jewel delivery status toggle                                   | 🟢 UI logic          |
| `calculateDiscountAllocations()`       | ~7808-8034     | Two-tier multi-pass bill discount pre-allocator (BIL-CLT01)    | 🔴 Core financial    |
| `_distributeDiscountToTier()`          | ~8035-8076     | Multi-pass helper — distributes with overflow redistribution   | 🔴 Core financial    |
| `check_is_weight_scheme()`             | ~29392-29414   | Weight scheme detection from JSON                              | 🟢 Utility           |

### ⚠️ Anti-Patterns Found in JS (Round 2)

1. **Deprecated Select2 API** — Lines 15529, 15572, 15599: `$('#select').select2('val', ...)` — deprecated, should use `.val(...).trigger('change')`
2. **`async: false` in AJAX** — Lines 15641, 36318, 36389: Blocks UI thread, causes browser freezing
3. **Missing `parseFloat()` guards in some calcs** — `calculate_advance_adjust_amount()` uses `parseInt()` on line 19748 instead of `parseFloat()` for amounts — potential truncation bug
4. **`alert("1")` debug leftover** — Line 36395 in `verify_advance_transfer_otp()`
5. **Empty error handlers** — Line 29453: `error: function (error) {}` — silent failure

## 7d. Comprehensive JS AJAX Map (Round 4)

> Extracted via automated parsing of 44k-line `ret_billing.js`.

### AJAX Endpoints Found

| JS Function Context                       | AJAX Endpoint URL                                                                           |
| ----------------------------------------- | ------------------------------------------------------------------------------------------- |
| `Unknown`                                 | `admin_ret_tagging/getAvailableTaxGroupItems`                                               |
| `getOtherChargesDetails`                  | `admin_ret_tagging/getOtherCharges`                                                         |
| `getEstimationDetails`                    | `admin_ret_billing/getEstimationDetails`                                                    |
| `gift_voucher_redeem`                     | `admin_ret_billing/GiftRedeemProduct` / `GeneralGiftRedeemProduct`                          |
| `check_gift_vocuher_issue`                | `admin_ret_billing/getGiftProducts`                                                         |
| `deleteEstimation`                        | `admin_ret_estimation/estimation/delete/`                                                   |
| `add_customer_25_12_2023`                 | `admin_ret_billing/createNewCustomer`                                                       |
| `update_customer_25_12_2023`              | `admin_ret_billing/updateNewCustomer`                                                       |
| `getSearchCustomers`                      | `admin_ret_estimation/getCustomersBySearch`                                                 |
| `getSearchTags`                           | `admin_ret_billing/getTaggingBySearch`                                                      |
| `getSearchProducts`                       | `admin_ret_estimation/getProductBySearch`                                                   |
| `getSearchDesign`                         | `admin_ret_estimation/getProductDesignBySearch`                                             |
| `get_stones`                              | `admin_ret_tagging/getStoneItems`                                                           |
| `get_materials`                           | `admin_ret_tagging/getAvailableMaterials`                                                   |
| `get_bank_details`                        | `admin_ret_billing/get_bank_acc_details`                                                    |
| `get_payment_device_details`              | `admin_ret_billing/get_payment_device_details`                                              |
| `get_billing_list`                        | `admin_ret_billing/billing/ajax`                                                            |
| `get_approval_billing_list`               | `admin_ret_billing/billing/ajaxapprovallist`                                                |
| `get_metal`                               | `get/active_metals`                                                                         |
| `get_received_lots`                       | `admin_ret_tagging/get_lot_ids`                                                             |
| `get_tag_types`                           | `admin_ret_tagging/get_tag_types`                                                           |
| `get_tag_purities`                        | `admin_ret_catalog/purity/active_purities`                                                  |
| `get_metal_rates_by_branch`               | `admin_ret_tagging/get_metal_rates_by_branch`                                               |
| `get_branch_details`                      | `admin_ret_billing/get_branch_details`                                                      |
| `get_taxgroup_items`                      | `admin_ret_billing/getAllTaxgroupItems`                                                     |
| `get_tag_matels`                          | `admin_ret_estimation/getMetalTypes`                                                        |
| `get_tag_taxgroups`                       | `admin_ret_tagging/getAvailableTaxGroups`                                                   |
| `getDesignPurityDetails`                  | `admin_ret_tagging/getDesignPurityByDesignId`                                               |
| `getDesignStoneDetails`                   | `admin_ret_tagging/getDesignStonesByDesignId`                                               |
| `getDesignMaterialsByDesignId`            | `admin_ret_tagging/getDesignMaterialsByDesignId`                                            |
| `load_tag_stone_list_on_edit`             | `admin_ret_tagging/getTagStoneByTagId`                                                      |
| `get_SchemeAcc_number`                    | `admin_ret_billing/get_scheme_accounts`                                                     |
| `getSearchAcc`                            | `admin_ret_billing/get_scheme_accounts`                                                     |
| `calculateChit_Amount`                    | `admin_ret_billing/sendotp` / `update_otp`                                                  |
| `getVoucherDetails`                       | `admin_ret_billing/getVoucherDetails`                                                       |
| `get_advance_details`                     | `admin_ret_billing/get_advance_details`                                                     |
| `oldget_advance_details`                  | `admin_ret_billing/get_advance_details`                                                     |
| `getCreditBillDetails`                    | `admin_ret_billing/getCreditBillDetails`                                                    |
| `getBillDetails`                          | `admin_ret_billing/getBillDetails`                                                          |
| `get_ActiveUOM`                           | `admin_ret_tagging/get_ActiveUOM`                                                           |
| `get_payModes`                            | `admin_ret_billing/get_payModes`                                                            |
| `get_stone_types`                         | `admin_ret_tagging/getStoneTypes`                                                           |
| `cancel_issue_bill`                       | `admin_ret_billing/issue/cancel`                                                            |
| `getSearchCustomer`                       | `admin_ret_billing/get_borrower`                                                            |
| `get_customer_credit_details`             | `admin_ret_billing/get_customer_credit_details`                                             |
| `get_customer_advance_details`            | `admin_ret_billing/get_customer_advance_details`                                            |
| `get_receipt_advance_details`             | `admin_ret_billing/get_advance_details`                                                     |
| `billcancel_otp`                          | `admin_ret_billing/send_bill_cancel_otp` / `verify_otp_for_billcancel` / `cancel_bill/ajax` |
| `getSearchCompanyUsers`                   | `admin_ret_billing/getSearchCompanyUsers`                                                   |
| `add_company_user`                        | `admin_ret_billing/addNewCompanyUsers`                                                      |
| `getCompanyPurchaseAmount`                | `admin_ret_billing/getCompanyPurchaseAmount`                                                |
| `discount_otp`                            | `admin_ret_billing/admin_approval` / `verify_otp`                                           |
| `get_weight_scheme_details`               | `admin_ret_billing/get_customer_weight_scheme_details`                                      |
| `get_available_metal_stock_details`       | `admin_ret_purchase/karigarmetalissue/available_stock_details`                              |
| `get_customer_address_det`                | `admin_ret_billing/get_customer_address`                                                    |
| `get_delivery_country` / `state` / `city` | `settings/company/getcountry` etc.                                                          |
| `get_branches`                            | `branch/branchname_list`                                                                    |
| `get_village_list`                        | `admin_ret_estimation/ajax_get_village` / `getCustomersindRecords`                          |
| `get_check_customer_payment_det`          | `admin_ret_billing/bill_payment_details`                                                    |
| `send_credit_bill_otp`                    | `admin_ret_billing/send_credit_bill_otp` / `verify_credit_otp`                              |
| `get_service_bill_list`                   | `admin_ret_billing/service_bill/ajax`                                                       |
| `order_place`                             | `admin_ret_billing/order_place`                                                             |
| `get_tag_scan_details`                    | `admin_ret_reports/tag_history/ajax`                                                        |
| `get_tcs_percent`                         | `admin_ret_billing/get_customer_tcs_percent`                                                |
| `customer_detail_modal`                   | `admin_ret_billing/getCustomerDet`                                                          |
| `getCreditPending`                        | `admin_ret_billing/getCreditPending`                                                        |
| `getCustomerSalesDetails`                 | `admin_ret_billing/getCustomerSalesDetails`                                                 |
| `get_advance_detail`                      | `admin_ret_billing/get_advance_details`                                                     |
| `at_send_otp`                             | `admin_ret_billing/adtrnssendotp`                                                           |
| `verify_advance_transfer_otp`             | `admin_ret_billing/verify_advance_transfer_otp`                                             |
| `get_all_old_metal_rates`                 | `admin_ret_estimation/get_all_old_metal_rates`                                              |
| `cc_discount_otp`                         | `admin_ret_billing/credit_coll_disc_admin_approval`                                         |
| `getCusSearchTags`                        | `admin_ret_estimation/getPartialTagSearch`                                                  |
| `validate_huid`                           | `admin_ret_billing/validate_huid`                                                           |
| `send_mobile_approval_request`            | `admin_app_api/bill_disc_app_approval`                                                      |
| `update_aprvl_status`                     | `admin_app_api/update_aprvl_status`                                                         |

### 7e. Major JS Functions (Top 15 by size)

| Function Name                        | Approx Size | Context                                             |
| ------------------------------------ | ----------- | --------------------------------------------------- |
| `getEstimationDetails`               | ~66k bytes  | Massive multi-line estimation fetch and populate UI |
| `calculateSaleBillRowTotal`          | ~54k bytes  | 🔴 Core financial computation per sale row          |
| `get_village_list`                   | ~31k bytes  | Heavy address/area dropdown formatting              |
| `get_return_Bill_details`            | ~30k bytes  | Populates sales returns / refunds list              |
| `get_esti_details`                   | ~30k bytes  | Populating estimation details                       |
| `calculateChitSaleBillRowTotal`      | ~24k bytes  | 🔴 Core scheme calculation logic                    |
| `createSaleBillSplitRow`             | ~22k bytes  | Spawns split invoice row UI clones                  |
| `calculateSaleBillSplitRowTotal`     | ~20k bytes  | 🟡 Split bill financial computation                 |
| `group_splitted_item`                | ~20k bytes  | Arrays merging/splitting items logic                |
| `calculateOrderSaleBillRowTotal`     | ~17k bytes  | 🟡 Order conversions to sale computation            |
| `calculate_est_chit_closing_balance` | ~16k bytes  | 🔴 Estimative scheme closures                       |
| `send_mobile_approval_request`       | ~15k bytes  | Push notifications to manager app                   |
| `calculatePaymentCost`               | ~15k bytes  | 🟡 Payment mode summary computation                 |
| `create_new_bill_sale_details`       | ~13k bytes  | UI DOM element injector for line items              |
| `calculate_sales_details`            | ~12k bytes  | Overall summary accumulator for bottom UI           |

---

## 7f. Table → Methods Reverse Map

| Table                     | Read By                                                                                                                                                       | Written By                                                                            |
| ------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------- |
| `ret_billing`             | `ajax_getBillingList`, `getBillingDetails`, `getBillData`, `get_bill_no`, `cancel_bill`, `get_due_bill`, `get_paid_bill`, `getbillingInfobybillId`, many more | `insertData` (via billing save), `updateData` (via update/cancel), `paymentmode_edit` |
| `ret_bill_details`        | `getBillingDetails`, `getOtherEstimateItemsDetails`, `get_bill_detail`, `getBillDetailsData`, `getbillingdetailsitems`                                        | `insertData` (via billing save)                                                       |
| `ret_billing_payment`     | `getPaymentDetails`, `getServiceBillPaymentDetails`, `getCustomerpaymentDetails`                                                                              | `insertData` (via payment save)                                                       |
| `ret_billing_item_stones` | `get_bill_stone_details`, `stone_details_by_bill_id`, `stone_details_by_bill_det_id`                                                                          | `insertData` (via save)                                                               |
| `ret_taging`              | `get_tag_status`, `getTaggingBySearch`, `get_tag_details`, `getTagDetails`, `CheckProductAvailability`, `validate_huid`                                       | `updateData` (tag_status update on sale/cancel)                                       |
| `ret_estimation`          | `getEstimationDetails`, `is_estno_already_billed`                                                                                                             | `updateData` (estbillid link)                                                         |
| `ret_estimation_items`    | `getEstimationDetails`, `get_entry_records`, `get_esti_status`                                                                                                | `updateData` (purchase_status on sale)                                                |
| `customer`                | `getAvailableCustomers`, `get_customer`, `getCustomerDet`, `get_customer_details`                                                                             | `createNewCustomer`, `updateNewCustomer`, `updateData` (PAN/Aadhaar)                  |
| `ret_billing_service`     | `ajax_getServiceBillList`, `getServiceBillingDetails`                                                                                                         | `insertData`, `updateData`                                                            |
| `ret_cash_collection`     | `ajax_getCashCollection`, `get_cashCollectionDetails`                                                                                                         | `insertData`                                                                          |

---

## 7c. JS → Controller AJAX Route Map (Round 10 — 2026-03-24)

> **Total unique routes**: 104 — extracted from `ret_billing.js` (46,476 lines)
> All routes are prefixed with `index.php/admin_ret_billing/`
> Cross-module routes (admin_pos, admin_ret_tagging, etc.) are not listed here.

### Group 1: Core Billing CRUD

| JS Route                      | Maps To Controller Method          | Type     |
| ----------------------------- | ---------------------------------- | -------- |
| `billing/add`                 | `billing('add')`                   | Page load |
| `billing/edit`                | `billing('edit')`                  | Page load |
| `billing/list`                | `billing('list')`                  | Page load |
| `billing/ajax`                | `billing('ajax')`                  | AJAX GET  |
| `billing/ajaxapprovallist`    | `billing('ajaxapprovallist')`      | AJAX GET  |
| `billing/save`                | `billing('save')`                  | AJAX POST |
| `billing/split_save`          | `billing('split_save')`            | AJAX POST |
| `billing/cancell`             | `billing('cancell')`               | AJAX POST |
| `billing/delete`              | `billing('delete')`                | AJAX POST |
| `billing_invoice`             | `billing_invoice()`                | Page load |
| `billing_insurance`           | `billing_insurance()`              | Page load |
| `cancel_bill/ajax`            | `cancel_bill('ajax')`              | AJAX POST |
| `bill_split/esti_details`     | `bill_split('esti_details')`       | AJAX GET  |
| `bill_payment_details`        | `bill_payment_details()`           | AJAX GET  |
| `getBillingDetails`           | `getBillingDetails()`              | AJAX GET  |
| `getBillDetails`              | `getBillDetails()`                 | AJAX GET  |
| `get_return_Bill_details`     | `get_return_Bill_details()`        | AJAX GET  |

### Group 2: Customer & Estimation Lookup

| JS Route                           | Maps To Controller Method               | Type     |
| ---------------------------------- | --------------------------------------- | -------- |
| `getCustomerDet`                   | `getCustomerDet()`                      | AJAX GET  |
| `getCustomersindRecords`           | `getCustomersBySearch()`                | AJAX GET  |
| `getEstimationDetails`             | `getEstimationDetails()`                | AJAX GET  |
| `getEstimationDetailsTags`         | `getEstimationDetailsTags()`            | AJAX GET  |
| `getTaggingBySearch`               | `getTaggingBySearch()`                  | AJAX GET  |
| `getCustomerSalesDetails`          | `getCustomerSalesDetails()`             | AJAX GET  |
| `getCreditBillDetails`             | `getCreditBillDetails()`                | AJAX GET  |
| `getCreditPending`                 | `getCreditPending()`                    | AJAX GET  |
| `get_customer_credit_details`      | `get_customer_credit_details()`         | AJAX GET  |
| `get_customer_advance_details`     | `get_customer_advance_details()`        | AJAX GET  |
| `get_advance_details`              | `get_advance_details()`                 | AJAX GET  |
| `get_customer_address`             | `get_customer_address()`                | AJAX GET  |
| `get_customer_tcs_percent`         | `get_customer_tcs_percent()`            | AJAX GET  |
| `get_customer_weight_scheme_details` | `get_customer_weight_scheme_details()` | AJAX GET  |
| `check_pan_duplicate`              | `check_pan_duplicate()`                 | AJAX GET  |
| `get_mydelivery_address`           | `get_mydelivery_address()`              | AJAX GET  |
| `createNewCustomer`                | `createNewCustomer()`                   | AJAX POST |
| `updateNewCustomer`                | `updateNewCustomer()`                   | AJAX POST |

### Group 3: Payment, OTP & Approval

| JS Route                           | Maps To Controller Method               | Type     |
| ---------------------------------- | --------------------------------------- | -------- |
| `get_payment_device_details`       | `get_payment_device_details()`          | AJAX GET  |
| `get_payModes`                     | `get_payModes()`                        | AJAX GET  |
| `get_bank_acc_details`             | `get_bank_acc_details()`                | AJAX GET  |
| `get_scheme_accounts`              | `get_scheme_accounts()`                 | AJAX GET  |
| `getAllTaxgroupItems`               | `getAllTaxgroupItems()`                  | AJAX GET  |
| `sendotp`                          | `sendotp()`                             | AJAX POST |
| `send_credit_bill_otp`             | `send_credit_bill_otp()`                | AJAX POST |
| `send_bill_cancel_otp`             | `send_bill_cancel_otp()`                | AJAX POST |
| `adtrnssendotp`                    | `adtrnssendotp()`                       | AJAX POST |
| `verify_otp`                       | `verify_otp()`                          | AJAX POST |
| `verify_credit_otp`                | `verify_credit_otp()`                   | AJAX POST |
| `verify_otp_for_billcancel`        | `verify_otp_for_billcancel()`           | AJAX POST |
| `verify_advance_transfer_otp`      | `verify_advance_transfer_otp()`         | AJAX POST |
| `verify_credit_coll_disc_otp`      | `verify_credit_coll_disc_otp()`         | AJAX POST |
| `admin_approval`                   | `admin_approval()`                      | AJAX POST |
| `credit_coll_disc_admin_approval`  | `credit_coll_disc_admin_approval()`     | AJAX POST |
| `update_otp`                       | `update_otp()`                          | AJAX POST |

### Group 4: Issue / Receipt / Cash Collection

| JS Route                        | Maps To Controller Method            | Type     |
| ------------------------------- | ------------------------------------ | -------- |
| `issue/ajax`                    | `issue('ajax')`                      | AJAX GET  |
| `issue/cancel`                  | `issue('cancel')`                    | AJAX POST |
| `issue/petty_cash`              | `issue('petty_cash')`                | AJAX GET  |
| `issue/petty_cash_emp`          | `issue('petty_cash_emp')`            | AJAX GET  |
| `issue/issue_print`             | `issue('issue_print')`               | Page load |
| `issue/print_cheque`            | `issue('print_cheque')`              | Page load |
| `close_issue_receipt`           | `close_issue_receipt()`              | AJAX POST |
| `receipt/ajax`                  | `receipt('ajax')`                    | AJAX GET  |
| `receipt/cancel`                | `receipt('cancel')`                  | AJAX POST |
| `receipt/save`                  | `receipt('save')`                    | AJAX POST |
| `receipt/credit_bill`           | `receipt('credit_bill')`             | AJAX GET  |
| `receipt/receipt_print`         | `receipt('receipt_print')`           | Page load |
| `cash_collection/ajax`          | `cash_collection('ajax')`            | AJAX GET  |
| `cash_collection/ajax_list`     | `cash_collection('ajax_list')`       | AJAX GET  |
| `cash_collection/list`          | `cash_collection('list')`            | Page load |
| `cash_collection/save`          | `cash_collection('save')`            | AJAX POST |
| `cash_collection/print`         | `cash_collection('print')`           | Page load |
| `advance_transfer/save`         | `advance_transfer('save')`           | AJAX POST |

### Group 5: Service Bill / Delivery / Orders

| JS Route                         | Maps To Controller Method             | Type      |
| -------------------------------- | ------------------------------------- | --------- |
| `service_bill/add`               | `service_bill('add')`                 | Page load |
| `service_bill/ajax`              | `service_bill('ajax')`                | AJAX GET  |
| `service_bill/save`              | `service_bill('save')`                | AJAX POST |
| `service_bill_invoice`           | `service_bill_invoice()`              | Page load |
| `cancel_service_bill`            | `cancel_service_bill()`               | AJAX POST |
| `repair_order_thermal_print`     | `repair_order_thermal_print()`        | Page load |
| `item_delivery/ajax`             | `item_delivery('ajax')`               | AJAX GET  |
| `update_delivery_status`         | `update_delivery_status()`            | AJAX POST |
| `order_delievery_sendotp`        | `order_delievery_sendotp()`           | AJAX POST |
| `order_delievery_verify_otp`     | `order_delievery_verify_otp()`        | AJAX POST |
| `order_place`                    | `order_place()`                       | AJAX POST |
| `get_prev_ref_no`                | `get_prev_ref_no()`                   | AJAX GET  |
| `get_home_bill_sectionBranchwise`| `get_home_bill_sectionBranchwise()`   | AJAX GET  |

### Group 6: Vouchers, Gift, Scheme & Settings

| JS Route                              | Maps To Controller Method                  | Type     |
| ------------------------------------- | ------------------------------------------ | -------- |
| `getVoucherDetails`                   | `getVoucherDetails()`                      | AJAX GET  |
| `getGiftProducts`                     | `getGiftProducts()`                        | AJAX GET  |
| `GiftRedeemProduct`                   | `GiftRedeemProduct()`                      | AJAX POST |
| `GeneralGiftRedeemProduct`            | `GeneralGiftRedeemProduct()`               | AJAX POST |
| `get_one_time_pre_weight_scheme`      | `get_one_time_pre_weight_scheme()`         | AJAX GET  |
| `getactivesize`                       | `getactivesize()`                          | AJAX GET  |
| `validate_huid`                       | `validate_huid()`                          | AJAX GET  |
| `getCompanyPurchaseAmount`            | `getCompanyPurchaseAmount()`               | AJAX GET  |
| `get_borrower`                        | `get_borrower()`                           | AJAX GET  |
| `get_account_head`                    | `get_account_head()`                       | AJAX GET  |
| `paymentmode_edit/list`               | `paymentmode_edit('list')`                 | Page load |
| `paymentmode_edit/active_bill_list`   | `paymentmode_edit('active_bill_list')`     | AJAX GET  |
| `paymentmode_edit/save`               | `paymentmode_edit('save')`                 | AJAX POST |
| `paymentmode_edit/update`             | `paymentmode_edit('update')`               | AJAX POST |
| `bank_ledger_transfer`                | `bank_ledger_transfer()`                   | AJAX POST |
| `update_branch`                       | `update_branch()`                          | AJAX POST |
| `getBranchDayClosingData`             | `getBranchDayClosingData()`                | AJAX GET  |
| `get_branch_details`                  | `get_branch_details()`                     | AJAX GET  |
| `addNewCompanyUsers`                  | `addNewCompanyUsers()`                     | AJAX POST |
| `getSearchCompanyUsers`               | `getSearchCompanyUsers()`                  | AJAX GET  |
| `keepAlive`                           | `keepAlive()`                              | AJAX POST |

### Group 7: POS Integration

| JS Route                    | Maps To Controller Method          | Type     |
| --------------------------- | ---------------------------------- | -------- |
| *(POS routes via admin_pos)*| `UploadBilledTransaction()`        | AJAX POST |
| *(POS routes via admin_pos)*| `cancelTransactionRequest()`       | AJAX POST |
| *(POS routes via admin_pos)*| `getTransactionStatus()`           | AJAX GET  |
| *(POS routes via admin_pos)*| `getposdevicelists()`              | AJAX GET  |

> Note: POS AJAX calls go through `index.php/admin_pos/` (separate controller) not `admin_ret_billing`. They are cross-module endpoints already documented in CROSS_MODULE_MAP.md.
