# Chit Settings Module — Data Flow Traces
> **Round**: 1 | **Date**: 2026-03-06

---

## Flow 1: General Settings Update (chit_settings table)

```
User → GET settings/general/edit/1
  → general_settings('View', 1)
    1. Get settings → settingsDB('get', 1) — chit_settings row
    2. Get config → configDB('get', 1)
    3. Get limits → limitDB('get', 1)
    4. Get discounts → discount_db('get', 1)
    5. Get gateway settings (demo/pro/hdfc/tech)
    6. Get promotion/OTP credit settings
    7. Get SMS/mail/company settings
    8. Get counts → customer_count(), scheme_count(), sch_acc_count()
    9. Get account number & receipt format details
    10. Render massive form (60+ toggle switches)

User → POST settings/general/update/1
  → general_settings('Update', 1)
    1. Read POST 'general[]' — ~60 fields
    2. Parse JSON schemeaccNo_format → build acc_format string
    3. Parse JSON receiptNo_format → build receipt_format string
    4. Build $gen_info array (~55 fields with isset checks)
    5. Update → settingsDB('update', 1, $gen_info)
    6. Read POST 'config[]' — app version, pack URLs
    7. Build $config_dat (commented out: NOT saved)
    8. Read POST 'default[]' — country/state/city
    9. Update default country → update_default_country()
    10. Log operation
    11. Flash message → redirect
```

## Flow 2: Metal Rate Entry with Branch & Notifications

```
User → POST settings/rate/save
  → metal_rates('Save')
    1. Read POST rates[], branch_data[]
    2. Get discount settings → settingsDB('get')
    3. Get last metal rate → metal_ratesDB('last')
    4. Build insertData with discount logic:
       - goldrate_22ct = mjdmagoldrate_22ct - goldDiscAmt (if enabled)
       - goldrate_18ct = market_gold_18ct - goldDiscAmt_18k (if enabled)
       - silverrate_1gm = mjdmasilverrate_1gm - silverDiscAmt (if enabled)
    5. Insert → metal_ratesDB('insert')
    6. Write rate.txt file
    7. If branch_settings=1 AND is_branchwise_rate=1:
       - Loop branches → insert_metalrate() into branch_rate
    8. Check notification settings → canSendNoti(1)
    9. If enabled:
       - send_RatesToAllUsers($branch_id) → OneSignal push
    10. Flash + redirect
```

## Flow 3: Menu + Permission Management

```
Admin → POST permission/Save
  → permission('Save')
    1. JSON decode POST 'access_data'
    2. For each permission item:
       a. Check exists → PermissionDB('exist', id_profile, id_menu)
       b. If not exists → PermissionDB('insert', ..., {view,add,edit,delete})
       c. If exists → PermissionDB('update', ..., {view,add,edit,delete})
    3. Echo success (no redirect!)

Menu creation → menu('Save')
    1. Insert menu item → menuDB('insert')
    2. Auto-create permissions:
       a. Profile 1 (admin) → full access (view=1, add=1, edit=1, delete=1)
       b. All other profiles → no access (all 0)
```

## Flow 4: Clear Database (CRITICAL)

```
Admin → POST clear_database
  ⚠️ NO CSRF PROTECTION
    1. Read raw $_POST (NOT $this->input->post)
    2. SET FOREIGN_KEY_CHECKS=0
    3. If mode=1 (All): TRUNCATE ALL tables in 10 categories
    4. If mode=0 (Selected): TRUNCATE only selected categories
    5. Delete customer images, offer images, arrival images
    6. SET FOREIGN_KEY_CHECKS=1
    7. Log the operation
    8. Echo result
    ⚠️ Tables truncated:
       masters: bank, department, designation, drawee_account, import_log, metal_rates, payment_mode
       customer: customer, address, registered_devices
       scheme: scheme, gst_splitup_detail, scheme_group, scheme_branch, scheme_benefit_deduct_settings
       account: scheme_account, payment, postdate_payment, payment_status, pending_payment, settlement, settlement_detail, scheme_reg_request
       wallet: wallet_account, wallet_transaction, wallet_settings
       log: log, log_detail
       promotions: offers, new_arrivals
       daily_collection: daily_collection
       metal_rates: metal_rates
       access: access
```

## Flow 5: DB Backup

```
Admin → db_backup()
    1. Set timezone
    2. Configure backup: ZIP format, add DROP, add INSERT
    3. $this->dbutil->backup($prefs) — full DB backup
    4. Write to ../data/backup/backup_DD_MM_YYYY_HH_ii_ss.zip
    5. Log to db_backup table
    6. force_download() — send to browser
```

## Flow 6: Branch CRUD with Metal Rate Status

```
Admin → POST settings/branch/update/:id
  → branch_form('Update', $id)
    1. Read POST branch[]
    2. Handle branch image upload
    3. Update branch → branchDB('update')
    4. Update access time restrictions

Branch Rate List → matal_ratelist($id)
    1. Get metal rate → get specific branch_rate
    2. Update status → update_metalrate_status()
```

## Flow 7: Rate Notification Push (OneSignal)

```
send_RatesToAllUsers($branchArr)
    1. Get notification settings
    2. Build rate message with metal rates
    3. Get customer tokens by branch
    4. For each customer token batch (100 at a time):
       - onesignalNotificationToAll() → POST to OneSignal API
    5. Return results
```
