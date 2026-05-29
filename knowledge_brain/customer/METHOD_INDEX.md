# Customer Module — Method Index

> **Brain Updated:** 2026-03-25 | **Round:** R3-Upgrade | 43 ctrl + 75 model methods (unchanged). No code delta.

---

## A. Controller Methods (Alphabetical) — `admin_customer.php`

| Method | Lines | Tables Read | Tables Written | JS Caller |
|---|---|---|---|---|
| `ajax_check_delete($id)` | L108–125 | scheme_account, ret_billing, customerorder, ret_estimation, gift_card | — | `customer.js` L4679 (url: `customer/ajax_check_delete/{id}`) |
| `ajax_customers()` | L126–143 | customer, address, agent, country, state, city, village, kyc, scheme_account, branch | — | `customer.js` L1000 (url: `customer/ajax_list`) — Note: URL mismatch! JS calls `ajax_list`, controller is `ajax_customers` |
| `ajax_getAllActiveAgents()` | L1924–1929 | agent | — | `customer.js` L1751 |
| `ajax_getAllActiveEmployee()` | L2564–2569 | employee | — | `customer.js` L1863 |
| `ajax_get_customer($id)` | L68–92 | customer, address, country, state, city | — | Internal/other modules |
| `ajax_get_customers()` | L54–66 | customer, scheme_account | — | Scheme join form |
| `ajax_get_customers_list()` | L1843–1851 | customer | — | `customer.js` L1189 |
| `ajax_get_scheme_account_list()` | L1975–1983 | scheme_account, customer, scheme, chit_settings | — | `customer.js` (scheme account lookup) |
| `ajax_get_village()` | L1852–1857 | village | — | Internal |
| `allocate_agent_toCuctomers()` | L1930–1958 | — | customer | `customer.js` L1726 — ⚠️ BUG: always fails silently (L1936) |
| `allocate_employee_toCuctomers()` | L2570–2610 | — | customer | `customer.js` L1821 |
| `base64ToFile($imgBase64)` | L1960–1974 | — | filesystem | Internal (cus_post) |
| `birthday($birthday)` | L144–148 | — | — | Internal utility |
| `check_email()` | L1579–1594 | customer | — | `customer.js` L946 |
| `check_mobile()` | L1563–1578 | customer | — | `customer.js` L965 |
| `check_multiple_chits()` | L48–53 | chit_settings (via chitadmin_model) | — | Internal |
| `check_username($username)` | L1554–1562 | customer | — | `customer.js` L928 |
| `cus_form($type, $id)` | L150–392 | customer, address, kyc, country, state, city, village, chit_settings, ret_financial_year | — | — (GET page load) |
| `cus_list($msg)` | L93–106 | chit_settings (via admin_settings_model) | — | — (GET page load) |
| `cus_post('Add')` | L419–1059 | — | customer, address, kyc, wallet_account, scheme_account, customer_reg, transaction | Form submit |
| `cus_post('Edit', $id)` | L1060–1553 | — | customer, address, kyc | Form submit |
| `cus_profile('list')` | L1862–1865 | — | — | — |
| `cus_profile('edit')` | L1866–1869 | customer, address, village, country, state, city | — | `customer.js` L1390 |
| `cus_profile('update', $id)` | L1870–1921 | — | customer, address | `customer.js` L1566 |
| `customer_status($status, $id)` | L1745–1756 | — | customer | List page toggle — GET ⚠️ |
| `download($id, $file)` | L1758–1767 | filesystem | — | — ⚠️ Path traversal risk |
| `format_accRcptNo($type, $id)` | L1984–1988 | scheme_account, chit_settings, branch | — | Internal |
| `getkycdata_byid()` | L2477–2484 | kyc | — | `customer.js` L4194 |
| `get_customer_by_mobile()` | L1836–1842 | customer | — | `customer.js` JS form |
| `get_kyc_images($cus, $id)` | L2090–2476 | — | filesystem (kyc paths) | Internal (cus_post) |
| `index()` | L44–47 | — | — | — |
| `profile_status($status, $id)` | L1733–1744 | — | customer | List page toggle — GET ⚠️ |
| `retrive_KycImages($cus)` | L2067–2089 | filesystem | — | Internal (commented paths) |
| `rrmdir($path)` | L1720–1732 | filesystem | — | Internal utility |
| `set_image($cus_id)` | L1680–1719 | — | customer, filesystem | Internal (cus_post image flow) |
| `store_KycImages($cus, $id)` | L1989–2066 | — | filesystem, customer | Internal (cus_post) |
| `sync_existing_data($mobile, $id, $branch)` | L393–417 | customer_reg, scheme_account, transaction | scheme_account, payment, customer_reg, transaction | Internal (cus_post Add) |
| `upload_img($outputImage, $dst, $img)` | L1649–1679 | filesystem | filesystem | Internal |
| `upload_img__($field, $img_path, $filename)` | L1595–1648 | filesystem | filesystem | Internal |
| `wallet_account_create($cus_id, $mobile)` | L1769–1834 | — | wallet_account | Internal (cus_post Add) |
| `wallet_account_cus()` | L2611–2628 | wallet_account, customer | — | — |
| `zone($type, $id, $status)` | L2485–2563 | zone | zone | `customer.js` L4311–L4452 |

---

## B. Model Methods (Alphabetical) — `customer_model.php`

| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `__construct()` | L8–11 | — | — | — |
| `__decrypt($str)` | L18–20 | — | — | `get_login_detail()` |
| `__encrypt($str)` | L13–16 | — | — | `cus_post()` for password |
| `ajax_get_all_customers()` | L130–136 | customer | — | `ajax_get_customers()` controller |
| `ajax_get_customers($param)` | L137–141 | customer | — | External (scheme join) — ⚠️ SQL injection |
| `ajax_get_customers_list($param)` | L451–462 | customer | — | `ajax_get_customers_list()` controller |
| `ajax_get_scheme_account_list($sch, $old)` | L781–835 | scheme_account, customer, scheme, chit_settings | — | `ajax_get_scheme_account_list()` controller |
| `ajax_get_unallocated_customers()` | L120–129 | customer, scheme_account | — | `ajax_get_customers()` controller |
| `allocate_agent($data, $id)` | L571–577 | — | customer | `allocate_agent_toCuctomers()` controller |
| `allocate_employee($data, $id)` | L1217–1228 | — | customer | `allocate_employee_toCuctomers()` controller |
| `check_acc_records($id)` | L338–345 | scheme_account | — | Old delete guard (now replaced by check_customer_dependencies) |
| `check_customer_dependencies($id)` | L366–395 | scheme_account, ret_billing, customerorder, ret_estimation, gift_card | — | `ajax_check_delete()` controller |
| `check_password($id_customer, $pswd)` | L463–472 | customer | — | Login check |
| `check_pay_records($id)` | L327–337 | customer, scheme_account, payment | — | Old delete guard |
| `customer_count()` | L347–351 | customer | — | `cus_form('Add')` — limit check |
| `delete_customer($id)` | L353–364 | — | customer, address, wallet_account | Delete flow — ⚠️ incomplete |
| `email_available($email, $id)` | L434–450 | customer | — | `check_email()` controller |
| `empty_record()` | L201–249 | ret_financial_year, branch, chit_settings | — | `cus_form('Add')` |
| `format_accRcptNo($type, $id)` | L835–879 | scheme_account, chit_settings, branch, payment | — | `format_accRcptNo()` controller |
| `get_AccRcpt_DisplaySettings()` | L1015–1022 | chit_settings | — | Internal account formatting |
| `get_acc_Data($id)` | L1023–1034 | scheme_account | — | Internal |
| `get_all_customers(...)` | L24–79 | customer, address, agent, country, state, city, village, kyc, scheme_account, branch | — | `ajax_customers()` controller |
| `get_branch_details()` | L1242–1249 | branch, chit_settings | — | `empty_record()`, init dropdowns |
| `get_cus()` | L1236–1241 | customer | — | Internal |
| `get_cus_address($id)` | L194–200 | address | — | Internal |
| `get_cust($id)` | L161–188 | customer, address, country, state, city, village, kyc | — | `cus_form('Edit')` |
| `get_customer($id)` | L142–160 | customer, address, country, state, city | — | `ajax_get_customer()` — ⚠️ hardcoded id=1 in subquery |
| `get_customer_by_mobile($mobile)` | L473–484 | customer | — | `get_customer_by_mobile()` controller |
| `get_customer_range($lower, $upper)` | L508–517 | customer, address | — | Bulk SMS / range export |
| `get_customers_by_date($from, $to, $branch)` | L80–119 | customer, address, agent, country, state, city, village, kyc, scheme_account | — | `ajax_customers()` (date filter) |
| `get_entrydate($id)` | L500–507 | ret_day_closing, chit_settings | — | `cus_post('Add')` — date control |
| `get_kyc_byid($id, $type)` | L1173–1182 | kyc | — | `get_cust()`, `cus_form('Edit')` |
| `get_login_detail($id)` | L407–416 | customer | — | Login/portal |
| `get_receipt_Data($id)` | L1035–1048 | payment, chit_settings, scheme_account | — | Receipt formatting |
| `get_state_id($name)` | L594–602 | state | — | Import helper |
| `get_city_id($name)` | L603–611 | city | — | Import helper |
| `get_village()` | L485–489 | village | — | `ajax_get_village()` - ⚠️ returns SINGLE row only (row_array) |
| `getBranchCode($id_branch)` | L494–499 | branch | — | `sync_existing_data()` |
| `getAllActiveAgents()` | L566–570 | agent | — | `ajax_getAllActiveAgents()` |
| `getAllActiveEmployee()` | L1212–1216 | employee | — | `ajax_getAllActiveEmployee()` |
| `getAddressId($id_customer)` | L518–528 | address | — | Import helper |
| `getCustomerByMobile($mobile)` | L396–406 | customer | — | Check uniqueness |
| `GetFinancialYear()` | L189–193 | ret_financial_year | — | `cus_form()`, `empty_record()` |
| `getFormatFromDB()` | L1057–1061 | chit_settings | — | Format helpers |
| `getFormatedNumber($frmt, $acc)` | L1062–1120 | — | — | Account/receipt formatting |
| `getVillageZone($id_zone)` | L1189–1196 | village | — | Zone filtering |
| `getschId($data)` | L672–693 | chit_settings, scheme, scheme_branch | — | `insExisAcByMobile()` |
| `insert_address($data)` | L529–534 | — | address | Import flow |
| `insert_customer($data)` | L250–267 | — | customer, address | `cus_post('Add')` |
| `insert_customer_only($data)` | L282–287 | — | customer | Import / mobile sync |
| `insert_imported_customer($data)` | L269–281 | — | customer | CSV import |
| `insert_kyc($data)` | L1137–1142 | — | kyc | `cus_post()` KYC section |
| `insExisAcByMobile($data)` | L612–671 | customer_reg, scheme_account | scheme_account | `sync_existing_data()` |
| `insertData($data, $table)` | L1183–1188 | — | `{any}` | Generic insert |
| `isCustomerExist($mobile, $branch)` | L579–587 | customer | — | Import dedup — ⚠️ SQL injection |
| `kyc_exists($cus_id, $type, $acc)` | L1158–1165 | kyc | — | KYC dedup check |
| `mobile_available($mobile, $id)` | L417–433 | customer | — | `check_mobile()` controller |
| `mobile_available_import($mobile)` | L1121–1136 | customer | — | Import uniqueness |
| `Searchcustomer($SearchTxt)` | L535–548 | customer, address, village, country, state, city | — | `cus_profile('edit')` — ⚠️ SQL injection |
| `syncPayData($ac_data)` | L694–760 | transaction | payment | `sync_existing_data()` |
| `update_address($id, $data)` | L588–593 | — | address | Import/update |
| `update_customer($data, $id)` | L295–313 | address | customer, address | `cus_post('Edit')` |
| `update_customer_only($data, $id)` | L314–320 | — | customer | Status toggles |
| `update_images($id, $data)` | L321–326 | — | customer | Image upload |
| `update_kyc($id, $data)` | L1166–1172 | — | kyc | KYC update |
| `updData($data, $id_field, $id_value, $table)` | L1049–1056 | — | `{any}` | Generic field update |
| `updkycData($data, $id, $type)` | L1143–1148 | — | kyc | KYC update |
| `username_available($username)` | L288–294 | customer | — | `check_username()` controller |
| `updateData($data, $id_field, $id_value, $table)` | L1203–1211 | — | `{any}` | Generic update |
| `updateInterTableStatus($data, $payData)` | L761–778 | — | customer_reg, transaction | `sync_existing_data()` |

---

## C. JS → Controller AJAX Map — `customer.js`

### Internal Endpoints (admin_customer controller)

| JS Line | JS Context | AJAX URL | Controller Method |
|---|---|---|---|
| L717 | Login OTP check | `sms/login` | sms controller |
| L736 | Login email OTP | `sms/login_email` | sms controller |
| L821 | Country dropdown | `settings/company/getcountry` | admin_settings controller |
| L852 | State dropdown | `settings/company/getstate` | admin_settings controller |
| L896 | City dropdown | `settings/company/getcity` | admin_settings controller |
| L928 | Username check | `customer/check_username/{username}` | `check_username()` |
| L946 | Email check | `customer/check_email/` | `check_email()` |
| L965 | Mobile check | `customer/check_mobile` | `check_mobile()` |
| L1000 | Customer list load | `customer/ajax_list` | ⚠️ **URL mismatch** — should be `customer/ajax_customers` |
| L1189 | Customer search | `admin_customer/ajax_get_customers_list` | `ajax_get_customers_list()` |
| L1236 | Without-acc customers | `customer/without_acc_details` | ⚠️ **Unknown endpoint** — not in controller |
| L1291 | Village by date filter | `admin_settings/ajax_village_list` | admin_settings |
| L1371 | Village list | `admin_settings/ajax_village_list` | admin_settings |
| L1390 | Customer profile edit | `admin_customer/cus_profile/edit` | `cus_profile('edit')` |
| L1566 | Customer profile update | `customer/cus_profile/update/{id}` | `cus_profile('update', $id)` |
| L1598 | Refresh customer list | `customer/ajax_list` | ⚠️ Same mismatch as L1000 |
| L1726 | Allocate agent | `admin_customer/allocate_agent_toCuctomers` | `allocate_agent_toCuctomers()` |
| L1751 | Agent dropdown | `admin_customer/ajax_getAllActiveAgents` | `ajax_getAllActiveAgents()` |
| L1821 | Allocate employee | `admin_customer/allocate_employee_toCuctomers` | `allocate_employee_toCuctomers()` |
| L1863 | Employee dropdown | `admin_customer/ajax_getAllActiveEmployee` | `ajax_getAllActiveEmployee()` |
| L1923 | Profession dropdown | `settings/company/getprofession` | admin_settings |
| L4038 | Village by zone | `admin_ret_estimation/get_village` | **Cross-module — ret_estimation** |
| L4080 | Village by pincode | `admin_ret_estimation/get_village_by_pincode` | **Cross-module — ret_estimation** |
| L4194 | KYC data by ID | `admin_customer/getkycdata_byid` | `getkycdata_byid()` |
| L4311 | Zone list | `admin_customer/zone` | `zone('list')` |
| L4364 | Zone add (GET form) | `admin_customer/zone/add/` | `zone('add')` |
| L4397 | Zone add (POST save) | `admin_customer/zone/add/` | `zone('add')` |
| L4428 | Zone edit | `admin_customer/zone/edit/{id}` | `zone('edit', $id)` |
| L4452 | Zone update | `admin_customer/zone/update/` | `zone('update')` |
| L4627 | Language list | `settings/company/getlanguages` | admin_settings |
| L4679 | Delete pre-check | `customer/ajax_check_delete/{id}` | `ajax_check_delete($id)` |

---

## D. Table → Methods Reverse Map

| Table | Read By | Written By |
|---|---|---|
| `customer` | get_all_customers, get_customers_by_date, get_customer, get_cust, ajax_get_*, Searchcustomer, mobile_available, email_available, username_available, customer_count | insert_customer, update_customer, update_customer_only, update_images, delete_customer, allocate_agent, allocate_employee, updData |
| `address` | get_customer, get_cust, get_all_customers, get_cus_address | insert_customer (via insert), update_customer, insert_address, update_address, delete_customer |
| `kyc` | get_kyc_byid, kyc_exists | insert_kyc, update_kyc, updkycData |
| `wallet_account` | wallet_account_cus | wallet_account_create (via wallet_model), delete_customer |
| `scheme_account` | ajax_get_unallocated_customers, check_acc_records, check_customer_dependencies, insExisAcByMobile | insExisAcByMobile (INSERT on sync) |
| `customer_reg` | insExisAcByMobile | updateInterTableStatus |
| `transaction` | syncPayData | syncPayData (INSERT), updateInterTableStatus |
| `payment` | check_pay_records | syncPayData (INSERT) |
| `agent` | getAllActiveAgents | — |
| `employee` | getAllActiveEmployee | — |
| `kyc` filesystem | store_KycImages, get_kyc_images, retrive_KycImages | store_KycImages, get_kyc_images |
