# Masters Module — Data Flow

> **Brain Updated:** 2026-03-25 | **Round:** R5-Upgrade

---

## Flow 1: Metal Rate Entry (Most Complex — Downstream Impact)

**Trigger:** Admin enters metal rates at `/settings/rate/View` → submits to `metal_rates('Save')`

```
POST /settings/rate/Save:
├─ Read POST: $metal[] (all rate fields)
│             $branch_data[] (branch IDs for branch-wise rates)
│
├─ Read discount settings:
│    settingsDB('get') → chit_settings:
│      enableGoldrateDisc, goldDiscAmt
│      enableGoldrateDisc_18k, goldDiscAmt_18k
│      enableSilver_rateDisc, silverDiscAmt
│
├─ Compute adjusted rates:
│    goldrate_22ct  = mjdmagoldrate_22ct - goldDiscAmt     (if enabled)
│    goldrate_18ct  = market_gold_18ct   - goldDiscAmt_18k (if enabled)
│    silverrate_1gm = mjdmasilverrate_1gm - silverDiscAmt  (if enabled)
│
├─ INSERT metal_rates → {id_metalrates}
│
├─ IF branch_settings==1 AND is_branchwise_rate==1:
│    FOR each branch_id in branch_data:
│      INSERT branch_rate {id_metalrate, id_branch, status=1, date_add}
│
├─ file_put_contents('../api/rate.txt', $rate_array)
│    ⚠️ BUG: $rate_array is PHP array → writes "Array" to file
│    ← Mobile API is now broken until next rate entry
│
└─ IF canSendNoti(1):
     send_RatesToAllUsers($branch_ids):
       → getNotificationDetails() → all customer FCM tokens
       → FOR EACH token: onesignalNotificationToAll($alertdetails)
         → curl POST → https://onesignal.com/api/v1/notifications
```

**Tables written:** `metal_rates`, `branch_rate`  
**External:** `../api/rate.txt` (mobile API file), OneSignal API  
**Downstream consumers:** payment module (rate lookups), customer scheme calculations, mobile app

---

## Flow 2: RBAC Permission Update

**Trigger:** Admin saves menu permissions at `/settings/permission/Save`

```
POST /settings/permission/Save:
├─ Read POST: access_data (JSON) → array of {id_profile, id_menu, view, add, edit, delete}
├─ FOR EACH item:
│    PermissionDB('exist', id_profile, id_menu):
│      SELECT * FROM access WHERE id_profile=$id AND id_menu=$id_menu
│    IF not exists:
│      PermissionDB('insert') → INSERT access
│    ELSE:
│      PermissionDB('update') → UPDATE access SET view,add,edit,delete
│
└─ echo "Permission updated successfully.."

Dashboard permissions follow same path via DashboardPermissionDB()
```

**Impact:** Changes take effect on next page load (no session invalidation). A user with an active session retains old permissions until they next call `get_access()`.

---

## Flow 3: Branch Create

**Trigger:** Admin submits new branch form at `/settings/branch/Save`

```
POST /settings/branch/Save:
├─ INSERT branch (basic info + address fields)
│    Tables: branch
│
├─ IF $_FILES['branch_logo']:
│    set__branch_image($id):
│        mkdir(assets/img/branch/{id}/, 0777)  ← 0777 risk
│        upload_img(process image, crop to target)
│        UPDATE branch SET logo=$filename WHERE id_branch=$id
│
├─ INSERT metal_rate_settings for new branch
│    (default metal rate format settings)
│
├─ INSERT chit_settings clone for branch
│    (copy from main chit_settings id=1)
│
└─ INSERT employee_settings for admin user on this branch
     (default access time full-day)
```

---

## Flow 4: General Settings Update

**Trigger:** Admin submits general settings form at `/settings/general/View` → Save

```
POST /settings/general/Save OR Update:
├─ Read complex POST: settings[], limit[], discount[], config[]
│
├─ trans_begin()
│
├─ settingsDB('update', $id, $data):
│    UPDATE chit_settings SET ... WHERE id_chit_settings=$id
│   ← This single row controls ENTIRE SYSTEM behavior
│
├─ limitDB('update', $id, $limit_data):
│    UPDATE chit_limit SET ...
│
├─ discount_db('update', $id, $disc_data):
│    UPDATE chit_discount SET ...
│
├─ IF account number format changed:
│    configDB('update', $id, $config_data)
│
├─ trans_commit() OR trans_rollback()
│
└─ Log change via log_model
```

**Downstream impact:** Changing `chit_settings` propagates immediately to ALL modules since each uses `settingsDB('get')` per request (no caching).

---

## Flow 5: Offer Create (Image Upload Pattern)

**Trigger:** Admin creates promotional offer at `/settings/offers/Save`

```
POST /settings/offers/Save:
├─ INSERT offers → {id_offer}
│
├─ IF $_FILES['offer_img']:
│    set_image($id_offer):
│        mkdir(assets/img/offers/{id}/, 0777)
│        upload_img → GD resize/crop → save as offer.jpg
│        UPDATE offers SET offer_img_path='offers/{id}/offer.jpg'
│
└─ redirect('settings/offers/list')
```

This pattern is repeated for: new_arrivals, gifts, payment_gateway, classification (sch_classify), branch logo — all use same `set_image()` / `set__branch_image()` / `set__clsfy_image()` / `set__paymentgateway_image()` utility methods.

---

## Flow 6: `clear_database()` (DANGER)

**Trigger:** GET `/settings/clear_database`  
⚠️ **Catastrophic — no auth check, no confirmation, instant data loss**

```
GET /settings/clear_database:
├─ NO role check
├─ NO CSRF validation
├─ Define $truncate array:
│    ['scheme_account', 'customer_reg', 'customer', 'address', 'kyc',
│     'payment', 'receipt', 'transaction', 'wallet_account', ...]
│
└─ FOR EACH table group:
     truncateFromArray($selected):
       FOR EACH table name:
         EXECUTE "TRUNCATE " . $table  ← immediate, irreversible
```

---

## Flow 7: Push Notification Dispatch

**Trigger:** Metal rate save (or manual dispatch)

```
send_RatesToAllUsers($branchArr):
├─ getNotificationDetails() [model L2418–2458]:
│    SELECT noti_sub, allow_notification FROM notification n JOIN chit_settings cs
│    → returns all customer FCM subscription tokens
│
├─ Build notification payload (rate update message)
│
└─ FOR EACH customer token:
     onesignalNotificationToAll($alertdetails):
         fields = {app_id, include_player_ids, contents, headings, data}
         curl POST → https://onesignal.com/api/v1/notifications
         Authorization: Basic {authentication_key from config}
         CURLOPT_SSL_VERIFYPEER = FALSE  ← TLS verification disabled
```

**Note:** `CURLOPT_SSL_VERIFYPEER = FALSE` disables certificate validation on the OneSignal push call — MITM vulnerability.
