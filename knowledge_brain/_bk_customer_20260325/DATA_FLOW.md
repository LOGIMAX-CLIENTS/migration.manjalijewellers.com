# Customer Module — Data Flow

> **Brain Updated:** 2026-03-17 | **Round:** 1

---

## Flow 1: CREATE Customer (Add)

**Trigger:** User clicks Save on `/customer/cus_form/Add` form  
**JS:** Form serialized → POST to `/customer/cus_post/Add`

### Steps

```
1. JS pre-validation (customer.js):
   ├─ Mobile availability: AJAX → /customer/check_mobile
   ├─ Email availability: AJAX → /customer/check_email
   ├─ Username availability: AJAX → /customer/check_username/{username}
   └─ Client-side required field checks

2. Controller: cus_post('Add') [L419–1059]
   ├─ Read branch entry date → customer_model::get_entrydate($id_branch)
   │    Reads: ret_day_closing, chit_settings
   │    Returns: custom_entry_date if edit_custom_entry_date==1
   │
   ├─ Image processing:
   │    $p_ImgData = json_decode(rawurldecode($cus['cus_img']))
   │    If webcam image → base64ToFile($src) → inject into $_FILES['cus_img']
   │
   ├─ trans_begin()
   │
   ├─ customer_model::insert_customer($cus_data) [model L250–267]
   │    Writes: INSERT customer (info fields)
   │    Returns: $cus_id (new id_customer)
   │    Then: INSERT address; UPDATE customer SET id_address = new_address_id
   │
   ├─ IF integrationType==3 OR autoSyncExisting==1:
   │    sync_existing_data($mobile, $cus_id, $id_branch) [L393–417]
   │    → insExisAcByMobile() → customer_reg table lookup
   │    → INSERT scheme_account if offline data exists
   │    → syncPayData() → INSERT payment from transaction staging
   │    → updateInterTableStatus() → UPDATE customer_reg + transaction
   │
   ├─ IF wallet_account_type==1:
   │    wallet_account_create($cus_id, $mobile) [L1769–1834]
   │    → wallet_model::get_wallet_acc_number()
   │    → wallet_model::wallet_accountDB('insert') → INSERT wallet_account
   │    → log_model::log_detail('insert')
   │    → SMS + Email if service enabled
   │
   ├─ Image upload (set_image):
   │    upload_img(cus_img) → CUS_IMG_PATH/{cus_id}/customer.jpg
   │    update_images($cus_id, {cus_img: path})
   │
   ├─ KYC Document Storage (store_KycImages) [L1989–2066]:
   │    For each: pan_img, aadhar_img, dl_img, pp_img, pb_img
   │    → json_decode(rawurldecode(base64_payload))
   │    → mkdir(img_path, 0777, TRUE)
   │    → file_put_contents(img_path, base64_decode)
   │    → updData({ImgName}, 'id_customer', $cus_id, 'customer')
   │
   ├─ KYC Master Records (get_kyc_images + insert_kyc):
   │    → get_kyc_images() — saves KYC files, returns URL array
   │    → insert_kyc(pan_data)     kyc_type=2
   │    → insert_kyc(aadhar_data)  kyc_type=3
   │    → For each bank in bank_details[]:
   │         insert_kyc(bank_data) kyc_type=1
   │
   └─ trans_commit() / trans_rollback()
      Redirect: /customer with flash message
```

### Tables Written (CREATE)

```
customer ← INSERT (main record)
address  ← INSERT (then UPDATE customer.id_address)
wallet_account ← INSERT (conditional)
scheme_account ← INSERT (conditional, sync only)
payment        ← INSERT (conditional, sync only)
customer_reg   ← UPDATE is_registered_online (sync only)
transaction    ← UPDATE is_transferred (sync only)
kyc            ← INSERT (pan, aadhar, bank — 1 per type per doc)
customer       ← UPDATE (cus_img, pan_ImgName, aadhar_ImgName etc.)
filesystem     ← WRITE (assets/img/customer/{id}/, assets/kyc/{type}/{id}/)
```

---

## Flow 2: EDIT Customer

**Trigger:** User clicks Save on `/customer/cus_form/Edit/{id}`  
**JS:** Same form, POST to `/customer/cus_post/Edit/{id}`

### Form Load

```
cus_form('Edit', $id) [L190–392]:
├─ customer_model::get_cust($id)       → customer + address + kyc (village join)
├─ customer_model::get_kyc_byid($id, 3) → Aadhar images
├─ customer_model::get_kyc_byid($id, 1) → Bank kyc list
├─ customer_model::GetFinancialYear()
└─ birthday() → compute age from DOB
```

### Edit Save

```
cus_post('Edit', $id) [L1060–1553]:
├─ Same mobile/email uniqueness checks (exclude self)
│
├─ Image re-upload (conditional, if new webcam image)
│
├─ trans_begin()
│
├─ customer_model::update_customer($cus_data, $id)
│    UPDATE customer WHERE id_customer=$id
│    IF address exists: UPDATE address WHERE id_customer=$id
│    ELSE: INSERT address (new address for customer)
│
├─ KYC Update Logic:
│    get_kyc_images() → save new images
│    kyc_exists($id, $kyc_type, $acc_number) → dedup check
│    IF exists: update_kyc / updkycData
│    ELSE: insert_kyc
│
└─ trans_commit()
   Redirect: /customer
```

---

## Flow 3: DELETE Customer

**Trigger:** User initiates delete in list  
**Two-Step flow:**

```
Step 1 — Pre-check:
GET /customer/ajax_check_delete/{id}
→ check_customer_dependencies($id):
    SELECT scheme_account WHERE active=1 AND is_closed=0
    SELECT ret_billing WHERE bill_cus_id=$id
    SELECT customerorder WHERE order_to=$id
    SELECT ret_estimation WHERE cus_id=$id
    SELECT gift_card WHERE purchased_by=$id
→ Return: {status: true/false, message: "..."}

Step 2 — Execute delete (only if Step 1 returns status=true):
GET /customer/delete/{id}  ← ⚠️ Direct GET — CSRF vulnerable
→ customer_model::delete_customer($id):
    DELETE FROM address WHERE id_customer=$id
    DELETE FROM customer WHERE id_customer=$id
    DELETE FROM wallet_account WHERE id_customer=$id
    ⚠️ kyc records NOT cleaned up (orphan risk)
    ⚠️ log_detail NOT written for delete operation
```

---

## Flow 4: KYC Document Upload

**Trigger:** User uses webcam/file upload in KYC tab of customer form

```
Client Side:
├─ Webcam capture → HTML5 MediaDevices API → base64 image
├─ File upload → FileReader.readAsDataURL → base64
└─ All stored in JSON array: [{src: "data:image/png;base64,..."}]
   → URL-encoded → stored in hidden form fields

Server Side (cus_post):
├─ json_decode(rawurldecode($cus['pan_img']))
├─ explode(";base64,", $img[0]->src)
├─ base64_decode($parts[1])
├─ mkdir("assets/kyc/pan/{id}/", 0777, TRUE)
├─ file_put_contents("assets/kyc/pan/{id}/pan_front.png", $decoded)
└─ URL = base_url() . "assets/kyc/pan/{id}/pan_front.png"
   → Stored in kyc.img_url
```

**KYC Type Codes:**

| kyc_type | Document |
|---|---|
| 1 | Bank Account / Passbook / Cheque |
| 2 | PAN Card |
| 3 | Aadhar Card |

---

## Flow 5: Agent/Employee Bulk Allocation

**Trigger:** Admin selects customers from list + agent → Click Allocate

```
JS (customer.js L1726):
├─ Collect: id_customer[] (array of selected customers)
├─ Collect: id_agent (selected agent)
└─ POST to /admin_customer/allocate_agent_toCuctomers

Controller (allocate_agent_toCuctomers) [L1930–1958]:
├─ $cus = $this->input->post('id_customer')  → array
├─ $total = count($cus)   = N
├─ $total = array()       ⚠️ IMMEDIATELY OVERWRITTEN — count lost
├─ IF ($total > 0)        ⚠️ ALWAYS FALSE (empty array is falsy but...)
│  Actually: in PHP, empty array is truthy... wait:
│  count([]) = 0, so `if (array() > 0)` → array > 0 → PHP coerces
│  array to 1 in numeric comparison → TRUE... but then $i never initialized
│  This is a subtle PHP bug — $i is undefined in count() call at L1951
└─ Returns status 0/1/2 but allocation logic may partially run
   ⚠️ Each $i++ relies on trans_status() which has no trans_begin()
```

---

## Flow 6: Profile Quick Update

**Trigger:** Customer self-service or admin from profile page

```
GET /customer/cus_profile/list → profile.php view

POST /customer/cus_profile/edit (JS L1390):
├─ searchTxt from search field
└─ Searchcustomer($SearchTxt) → customer + address (LIKE search)
   ⚠️ SQL injection via searchTxt

POST /customer/cus_profile/update/{id} (JS L1566):
├─ update_customer($cus_data, $id) → UPDATE customer + address
├─ trans_begin / commit / rollback
└─ log_detail('insert', 'Edit')
```

---

## Flow 7: ERP/Offline Sync on Customer Creation (integrationType=3)

**Trigger:** New customer registered whose mobile already exists in offline `customer_reg` staging table

```
sync_existing_data($mobile, $id_customer, $id_branch) [L393–417]:
│
├─ insExisAcByMobile($data) [model L612–671]:
│    SELECT * FROM customer_reg WHERE record_to=2 AND is_registered_online=0
│                                AND mobile={mobile} [AND branch filter]
│    For each offline registration:
│        getschId() → find matching scheme by sync_scheme_code
│        IF scheme_account already exists (by ref_no/clientid):
│            Use existing id_scheme_account
│        ELSE:
│            INSERT scheme_account (from offline customer_reg data)
│
├─ syncPayData($ac_data) [model L694–760]:
│    SELECT * FROM transaction WHERE record_to=2 AND is_transferred='N'
│                                AND client_id={clientid}
│    For each payment (is_modified==0):
│        INSERT payment (from transaction staging data)
│    For each cancelled (is_modified==1):
│        INSERT payment with payment_status=4 (cancelled)
│
└─ updateInterTableStatus($data, $payData) [model L761–778]:
    UPDATE customer_reg: is_registered_online=1, is_transferred='Y'
    UPDATE transaction: is_transferred='Y' for all synced IDs
```
