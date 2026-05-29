# FORENSIC TEMPLATE — Retail Settings
> **Module:** Retail Settings | **Round:** 3 | **Date:** 2026-03-16  
> Use this template when debugging any Settings module bug or investigating unexpected behavior in ANY module that reads settings.

---

## Step 1 — Identify the Symptom

Describe the bug symptom clearly:

```
[ ] Billing calculation wrong — which field? ________________
[ ] OTP not firing when expected
[ ] OTP firing when it shouldn't
[ ] Cash limit not enforced
[ ] Bill split not triggering
[ ] Discount not applying correctly
[ ] Employee/section/counter not mandatory when expected
[ ] Metal rate not saving / rate.txt not updating
[ ] Permission access wrong (user can/cannot do something unexpected)
[ ] SMS template not sending
[ ] Scheme closure calculation wrong
[ ] Stock color classification wrong
[ ] Other: ________________
```

---

## Step 2 — Check ret_settings Live Values

Run this first before any code investigation:

```sql
-- Full snapshot
SELECT id_ret_settings, name, value, description 
FROM ret_settings 
ORDER BY id_ret_settings;

-- Quick check for specific setting
SELECT name, value FROM ret_settings WHERE name IN (
  'is_tcs_required','validate_cash_amt','max_cash_amt',
  'bill_discount_type','bill_split_min_amount','bill_split_max_amount',
  'is_section_required','billing_emp_select_req','is_counter_req',
  'order_delievery_otp','stock_issue_otp','is_otp_required_for_approval'
);
```

**Compare against SETTINGS_SNAPSHOT.md baseline.** Any deviation = potential misconfiguration.

---

## Step 3 — Trace the Read Path

### For `ret_settings` reads:

```
Q: Which module is exhibiting the bug?
→ Find which ret_settings key it reads
→ Confirm: $this->db->where('name', '{key}')->get('ret_settings')->row()->value
→ Cast correctly? (string → int/float/bool as needed)
→ Is the value fresh or cached?
```

**Common method:** `admin_settings_model::get_ret_settings($settings_name)` at L2402  
**Also:** `admin_settings_model::retail_settingsDB('get')` at L2373 (returns all rows)

### For `chit_settings` reads:

```
→ admin_settings_model::settingsDB('get', $id) at L1162
→ or admin_settings_model::get_settings() at L2287 (bare SELECT *)
→ or admin_settings_model::get_gstsettings() at L1757
→ or admin_settings_model::allow_autorate_update() at L1378
```

---

## Step 4 — Trace the Write Path (for config saves)

### ret_settings Update Path:
```
URL: /usersms/ret_settings_post/Update/{id}
Controller: admin_usersms::ret_settings_post() at L3276
Model: admin_usersms_model::update_ret_settings() 
     → calls: admin_settings_model::retail_settingsDB('update', $name, $data) at L2381
     → SQL: UPDATE ret_settings SET value=?, updated_by=?, updated_on=? WHERE name=?
```

**Check:** Was `updated_on` / `updated_by` actually updated? If not, the save failed silently.

```sql
-- Verify last update
SELECT name, value, updated_on, updated_by FROM ret_settings 
WHERE name = '{setting_key}';
```

---

## Step 5 — Check Profile Permissions

If a user reports they can't access something:

```sql
-- Check their profile's access
SELECT m.label, m.link, a.view, a.add, a.edit, a.delete
FROM access a
JOIN menu m ON a.id_menu = m.id_menu
WHERE a.id_profile = {id_profile}
AND m.link LIKE '%{module_url}%';

-- Get user's profile
SELECT id_employee, firstname, id_profile FROM employee WHERE id_employee = {uid};
```

---

## Step 6 — Metal Rate Issues

```sql
-- Latest rate
SELECT * FROM metal_rates ORDER BY id_metalrates DESC LIMIT 1;

-- Branch-specific rate status
SELECT br.id_branch, br.status, m.goldrate_22ct, m.updatetime
FROM branch_rate br
JOIN metal_rates m ON m.id_metalrates = br.id_metalrate
WHERE br.id_branch = {id_branch};
```

**Check `../api/rate.txt`** — does it exist? Is it PHP array format?

---

## Step 7 — SMS / OTP Issues

```sql
-- SMS service toggle
SELECT id_services, serv_name, serv_code, serv_sms, serv_email, serv_whatsapp
FROM services WHERE serv_code = '{SERVICE_CODE}';

-- OTP settings from chit_settings
SELECT isOTPReqToLogin, payOTP_exp, loginOTP_exp, req_otp_login, isOTPRegForPayment
FROM chit_settings;

-- OTP gates from ret_settings
SELECT name, value FROM ret_settings 
WHERE name IN ('is_otp_required_for_approval','advance_transfer_otp',
               'order_delievery_otp','vendor_approval_otp','stock_issue_otp');
```

**Remember:** SMS gateway = PHP config file, NOT database.  
`config/config.php` → `sms_gateway` item. Check: 1=MSG91, 2=Nettyfish, 3=SpearUC, 4=Asterixt, 5=Qikberry

---

## Step 8 — Common Root Cause Checklist

| Check | Query/Method |
|---|---|
| `ret_settings` value correct? | `SELECT value FROM ret_settings WHERE name='...'` |
| `chit_settings` value correct? | `SELECT * FROM chit_settings LIMIT 1` |
| Profile has required access? | Check `access` table for profile × menu |
| Payment mode exists and active? | `SELECT * FROM payment_mode` |
| Metal rates fresh? | Check `id_metalrates` timestamp |
| Branch rate linked? | `branch_rate` table — `status=1` for active |
| SMS service enabled? | `services.serv_sms = 1` for the required service |

---

## Step 9 — Known Bugs / Anti-Patterns

| Bug Pattern | Location | Fix |
|---|---|---|
| `validate_cash_amt = 0` — cash limit not enforced | `ret_settings` | Set to `1` if enforcement required |
| `clear_database()` has no OTP gate | `admin_settings.php` L2147 | Add OTP confirmation before call |
| Metal rate `rate.txt` writes PHP array, not JSON | `update_rate_file()` L493 | Convert to JSON format |
| `permission()` loop — no per-row transaction | `admin_settings.php` L282 | Wrap in single transaction |
| `catlog_module_post('Update')` passes `$module_id` before assignment | `admin_usersms.php` L3176 | Should pass `$module_data` |
| `ajax_village_list()` reads `$_GET` directly without CI input | `admin_settings_model.php` L2056 | Use `$this->input->get()` |
| `get_interWallet_trans` — broken OR logic in branch filter | L1976 | parentheses missing around OR |

---

*Template built from: Round 3 full scan | 2026-03-16*
