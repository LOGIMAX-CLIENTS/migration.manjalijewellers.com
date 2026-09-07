---
description: Port the OneSignal -> FCM push notification migration (settings-driven credentials, provider switch, shared send library) to another client project.
---

# FCM Notification Migration Workflow

Implementation guide for porting the **OneSignal → Firebase Cloud Messaging (FCM)** migration
into another client project, keeping **OneSignal available as a one-click rollback**.

Distilled from the eTail source implementation:

| Commit | What it delivered |
|---|---|
| `ad26b5e4` | CRUD for notify settings (DB columns + settings tab + save handler) |
| `1a01af4c` | FCM implementation (library, helper, dispatchers across 5 controllers, dedupe) |
| `0f40c01f` | Broadcast fix (token filtering, curl timeouts, parallel send) |
| `d3c2fbc5` | iOS diagnosis + per-device registration fix |

**Total surface: 15 files, ~1294 insertions.**

## File Inventory

Map these to the target client's equivalents — names may differ, the roles will not.

| File | New? | Role | Section |
|---|---|---|---|
| `database/migrations/<date>_add_notify_platform_cred_to_chit_settings.sql` | new | The two settings columns | §1.1 |
| `admin/application/helpers/push_notification_helper.php` | new | Settings reader, platform switch, dedupe | §2.1 |
| `admin/application/libraries/Fcm_service.php` | new | FCM HTTP v1 send library | §2.2 |
| `admin/application/models/admin_settings_model.php` | edit | Add 2 columns to the settings SELECT (1 line) | §2.3 |
| `admin/application/views/settings/general/form.php` | edit | APP Notification Settings tab | §2.3 |
| `admin/assets/js/admin_settings.js` | edit | Platform radio toggle | §2.3 |
| `admin/application/controllers/admin_settings.php` | edit | Save handler + 2 dispatchers | §2.3, §2.4 |
| `admin/application/controllers/admin_services.php` | edit | 3 dispatchers | §2.4 |
| `admin/application/controllers/admin_usersms.php` | edit | 3 dispatchers | §2.4 |
| `admin/application/controllers/admin_manage.php` | edit | 1 dispatcher | §2.4 |
| `admin/application/controllers/admin_payment.php` | edit | 1 dispatcher | §2.4 |
| `admin/application/models/account_model.php` | edit | Dedupe ×2 | §2.5 |
| `admin/application/models/admin_usersms_model.php` | edit | Dedupe ×2 | §2.5 |
| `application/models/mobileapi_model.php` | edit | Per-device upsert (customer API) | §3.1 |
| `application/controllers/mobile_api.php` | edit | Token guard fix (customer API) | §3.2 |

---

## 0. Before You Start — Decide These

| Decision | Default used in source | Why it matters |
|---|---|---|
| Where credentials live | `chit_settings.notify_cred` (DB, admin UI) | Client asked for no `config.php` credentials — file edits don't survive deploys |
| `notify_cred` column type | `TEXT`, JSON via PHP | Native `JSON` needs MySQL 5.7.8+/MariaDB 10.2+; a failed ALTER **aborts the whole deploy** |
| Which table | `chit_settings` | Already the client-settings table with an existing JSON column precedent (`feature_flags`) |
| Rollback mechanism | The `notify_platform` radio itself | No code change or deploy needed to revert to OneSignal |

> **Do not skip the OneSignal isolation.** The old code is the rollback path. Never delete it.

---

## 1. Database

### 1.1 Migration

Add two columns to the settings table. Idempotent, guarded by `INFORMATION_SCHEMA`:

```sql
-- UP
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chit_settings' AND COLUMN_NAME = 'notify_platform');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `chit_settings` ADD `notify_platform` TINYINT(1) NOT NULL DEFAULT \'1\' COMMENT \'1 - One Signal, 2 - FCM\'', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chit_settings' AND COLUMN_NAME = 'notify_cred');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `chit_settings` ADD `notify_cred` TEXT NULL DEFAULT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
```

- No `AFTER <col>` clause — the anchor column may not exist on every client.
- **No data back-fill in a schema file.** The read accessor falls back instead.

### 1.2 `notify_cred` shape

Store **every** platform, not just the active one, so switching provider never destroys the other's credentials:

```json
{
  "onesignal": { "id": "<app id>",     "key": "<rest api key>" },
  "fcm":       { "id": "<project id>", "key": "<service account json>" }
}
```

Generic `id`/`key` per platform keeps the UI a loop and the dispatchers branch-free.
**A new platform = one more key. No schema change.**

### 1.3 Check `registered_devices` FIRST

```sql
SHOW COLUMNS FROM registered_devices;   -- token MUST be varchar(500), not varchar(45)
SELECT device_type, COUNT(*),
       SUM(token LIKE '%:%')                                   AS fcm_shaped,
       SUM(token NOT LIKE '%:%' AND token REGEXP '^[0-9a-f]+$') AS raw_apns,
       SUM(LENGTH(token)>=100)                                 AS usable
  FROM registered_devices WHERE token<>'' GROUP BY device_type;
```

> In the source client this revealed **2 usable tokens out of ~4100**. Run it before promising
> anyone that notifications will work.

---

## 2. Backend — Admin App

### 2.1 Helper: `application/helpers/push_notification_helper.php` (new)

Single read point. Four functions:

| Function | Purpose |
|---|---|
| `get_notify_settings($with_all_cred=FALSE)` | Active platform + credentials, static-cached per request |
| `notify_cred_for($slug)` | A *specific* platform's credentials — used by the OneSignal path |
| `notify_platform_is($slug)` | The switch every dispatcher calls |
| `dedupe_device_tokens($rows, $key)` | Collapses rows sharing a token; drops empties |

Key rules:
- Guard with `$CI->db->field_exists('notify_cred','chit_settings')` so a client that hasn't
  migrated logs an error instead of fatalling.
- Normalise an unknown `notify_platform` to OneSignal **and log it** — never silently route to
  a provider nobody selected.
- Load with `$this->load->helper('push_notification')` inside each dispatcher (do **not** add to
  `autoload.php` unless the client wants it globally).

### 2.2 Library: `application/libraries/Fcm_service.php` (new)

FCM **HTTP v1** with service-account OAuth2 (JWT via `openssl_sign`, zero composer deps).
The legacy server-key API is retired by Google — a "Server Key" field has nothing valid to hold.

```php
const MIN_FCM_TOKEN_LENGTH = 100;   // below any real token, above every legacy id
const CURL_CONNECT_TIMEOUT = 5;
const CURL_TIMEOUT         = 15;
const SEND_CONCURRENCY     = 20;
```

Public API — returns OneSignal-shaped JSON (`id`, `recipients`) so existing
`json_decode($res)->recipients` call sites keep working:

| Method | Use |
|---|---|
| `sendToToken($token,$title,$body,$data,$image)` | one device |
| `sendToTokens($tokens,...)` | list — dedupes, filters, sends in parallel |
| `sendToAll($title,$body,$data,$image)` | broadcast — queries devices, delegates to `sendToTokens` |

**Four things that are not optional:**

1. **cURL timeouts on every handle.** Without them one stalled connection hangs the page forever.
2. **Filter unusable tokens before spending a request.** FCM v1 has no "send to many"; each token
   is one HTTPS round trip. Legacy OneSignal ids / truncated tokens are guaranteed failures.
3. **`curl_multi` for the send loop.** Sequential sends make a broadcast take one round trip per
   device (~15 min for 3500 devices in the source client).
4. **Cap failure logging** (first ~5). One line per dead token floods the log.

Credentials come from `get_notify_settings()`, **not** `config.php` and **not** a JSON file on disk —
a file never travels with a code deploy and would need placing on every client separately.

### 2.3 Settings tab (view + controller + model)

**Model** — add the two columns to the existing settings SELECT (one line):
```php
single_session_login,notify_platform,notify_cred
```
Reuse the generic settings writer (`settingsDB('update', $id, $array)`) — **do not** write a new
save function; the shared writer already updates this table.

**View** — one tab pane driven by a platform map so a third platform needs no view edit:
```php
$notify_platforms = array(
  1 => array('slug'=>'onesignal','label'=>'One Signal','id_label'=>'App ID','key_label'=>'REST API Key','key_type'=>'text'),
  2 => array('slug'=>'fcm','label'=>'FCM','id_label'=>'Project ID','key_label'=>'Service Account JSON','key_type'=>'textarea'),
);
```
Field names post as `app_notify[notify_platform]` and `app_notify[cred][<slug>][id|key]`, which
`json_encode`s straight into `notify_cred`.

> The tab must be its own `<form>` if it sits outside the main settings form — HTML forms cannot
> overlap. Post directly to `admin_settings/app_notification_settings/Update/1`; **no route entry
> is needed** if the app has no catch-all route.

**Controller** — validate before saving, or a bad paste fails silently later:
- platform must be in the whitelist
- id + key mandatory for the **active** platform only
- for FCM: JSON parses, has `project_id`/`client_email`/`private_key`, and `project_id` matches
  the Project ID field

**JS** — one platform-agnostic toggle showing `#notify_block_<id>` for the checked radio.

### 2.4 Dispatchers — one per OneSignal function

For **every** function that calls the OneSignal REST API, apply this shape. The original name
becomes the dispatcher so **no call site changes**:

```php
function send_singlealert_notification($alertdetails = array())
{
    $this->load->helper('push_notification');
    if (notify_platform_is('fcm')) {
        return $this->send_singlealert_notification_fcm($alertdetails);
    }
    return $this->send_singlealert_notification_onesignal($alertdetails);   // incumbent fallback
}
```

**Match each platform explicitly.** Never `if (onesignal) {...} else { fcm }` — with a catch-all
`else`, a future platform 3 silently routes to FCM.

Rename any function whose name states a provider (e.g. `onesignalNotificationToAll` →
`sendPushNotificationToAll`) — a name that says "onesignal" while sending via FCM misleads
everyone. Verify zero live callers before dropping the old name; **don't add a compatibility alias
without checking** (the source repo had none).

Source inventory — 10 dispatchers across 5 controllers:

| Controller | Dispatchers |
|---|---|
| `admin_services` | `send_singlealert_notification`, `sendPushNotificationToAll`, `send_singlealert_rate_notification` |
| `admin_usersms` | `sendPushNotificationToAll`, `send_singlealert_send_notification`, `send_singlealert_notification` |
| `admin_settings` | `sendPushNotificationToAll`, `send_singlealert_rate_notification` |
| `admin_manage`, `admin_payment` | `send_singlealert_notification` |

**Deliberately excluded** — check for equivalents in the target client:
- A function using a **different** OneSignal app id (e.g. a staff/admin app) — different audience,
  different token table. Migrating it broadcasts to the wrong people.
- Dead functions with **zero callers**.

**The isolated `_onesignal` bodies must also read credentials from settings**, not `config.php`:
```php
'app_id'  => notify_cred_for('onesignal')['id'],
$auth_key = notify_cred_for('onesignal')['key'];
```
Otherwise the OneSignal fields on the settings screen are decorative.

### 2.5 Dedupe at source

`registered_devices` usually has **no unique constraint** on token/uuid, so one physical device can
appear on several rows and receive the same push twice. Fix inside each model function that
*returns* token rows (not per call site):

```php
$this->load->helper('push_notification');
$data = dedupe_device_tokens($data, 'token');
```

Source applied this in 4 places (`account_model` ×2, `admin_usersms_model` ×2).
**Dedupe by token value, never by customer** — a customer's genuinely different devices must all
still be notified.

---

## 3. Backend — Customer API App

> ⚠️ Multi-app repos share `application/config/` folder names. Confirm you are editing the
> **customer API** app, not admin.

### 3.1 One row per DEVICE, not per customer

The original write path keyed on `id_customer` alone:

```php
if ($res->num_rows() > 0) { $this->db->where('id_customer',$id); $this->db->update(...); }
```

That **overwrites every row** for the customer — a second device (iPhone after Android) silently
kills the first. Meanwhile the other write path inserted unconditionally, creating duplicate rows.

Replace with an upsert keyed on **customer + device** (`uuid` when supplied, else `token`):

- refuse blank tokens (they can never be delivered to and pollute every broadcast query)
- delete rows holding the same token under a **different** customer (handed-down handset)
- stamp `created_on` / `updated_on`
- keep `insert_deviceData()` / `update_deviceData()` as wrappers with their **original return
  shapes** so existing call sites work unchanged

### 3.2 Fix the token guard

```php
if ($data['token'] != 'null') {          // WRONG - compares to the literal string
if (isset($data['token']) && trim($data['token']) !== '' && strtolower(trim($data['token'])) !== 'null') {
```

The original let empty tokens through — the source client had 61 blank rows because of it.

---

## 4. App Side (hand to the app developer)

The backend cannot fix these.

| # | Item | Detail |
|---|---|---|
| 1 | **iOS sends the wrong token** | `@capacitor/push-notifications` returns the **APNs** token on iOS by design. `AppDelegate` must set `Messaging.messaging().apnsToken`, request the FCM token, and re-post it via `.capacitorDidRegisterForRemoteNotifications`. Alternative: use `@capacitor-firebase/messaging`, which returns FCM tokens on both platforms |
| 2 | **Stable `uuid` per device** | The backend keys devices on `uuid`. It must survive token rotation, or every rotation creates a new row |
| 3 | **Push token on rotation** | Diff against the stored token in the `registration` listener; silently re-authenticate on change |
| 4 | **Logout must not wipe the token** | Back up `DeviceData` before `storage.clear()` and restore it |
| 5 | **Foreground display** | Android needs `LocalNotifications.schedule()`; **iOS presents natively** — calling it unconditionally shows the notification twice. Branch on `Capacitor.getPlatform()` |
| 6 | **iOS project config** | `GoogleService-Info.plist` added *through Xcode*, APNs `.p8` uploaded to Firebase, Push Notifications + Background Modes capabilities, Firebase via **SPM or CocoaPods, not both** |

**Acceptance test:** after an iOS login, the stored token contains `:` and is ~142 chars.

---

## 5. Verification

### 5.1 Mock self-test (before any real credentials)

Run a dependency-free mock FCM/OAuth2 server on `127.0.0.1:<port>`, point
`fcm_oauth2_url` / `fcm_send_url_base` at it, and exercise the **real** library:

| Case | Expected |
|---|---|
| normal token | `recipients:1` + a message id |
| `INVALID_TEST_TOKEN` | `recipients:0` + `fcm_response.error` set |
| credentials blank | `error: FCM_NOT_CONFIGURED` |

Generate the throwaway RSA key with the **OpenSSL CLI** (`openssl genrsa`), not
`openssl_pkey_new()` — the PHP function fails on Windows without `openssl.cnf`.
Start the mock via a **backgrounded shell command**, not `proc_open()`.

### 5.2 Live credential check (no message delivered)

Sign a JWT with the saved service account and POST to `https://oauth2.googleapis.com/token`.
`http_code=200` + an access token proves the credentials. Then probe `messages:send` with a
deliberately invalid token:

| Response | Meaning |
|---|---|
| `400 INVALID_ARGUMENT` | **Correct** — auth, project id and endpoint all good |
| `404` | Project id wrong, or FCM API not enabled |
| `401` / `403` | Auth or permission problem |

### 5.3 Checklist

- [ ] Migration applies, is idempotent, and its DOWN works
- [ ] Settings tab creates / shows / edits; switching platform **preserves the other's credentials**
- [ ] Invalid FCM JSON and mismatched Project ID are rejected on save
- [ ] **OneSignal still works** with `notify_platform = 1` (credentials now come from the DB!)
- [ ] Mock self-test: 3/3
- [ ] Dedupe verified with a synthetic dataset (repeat token, empty token, two real devices for one customer)
- [ ] Multi-device: two rows for one customer both survive a login
- [ ] `php -l` on every changed file; **compare `function NAME(` counts against `git show HEAD:`** —
      `php -l` does **not** catch duplicate method declarations
- [ ] Line endings unchanged (new files written LF must be converted to the repo's CRLF)
- [ ] Rollback: switch to One Signal, save, notifications go back through OneSignal

---

## 6. Known Traps

| Trap | Symptom | Fix |
|---|---|---|
| Sequential sends | Send page "just loading" for minutes | Filter unusable tokens + `curl_multi` |
| No cURL timeout | Page hangs indefinitely | `CURLOPT_CONNECTTIMEOUT` / `CURLOPT_TIMEOUT` |
| `varchar(45)` token column | Every token truncated, all sends fail | Widen to `varchar(500)` **and ship it as a migration** |
| iOS stores APNs token | Firebase Console works, backend doesn't | App-side AppDelegate bridge (§4.1) |
| Broadcast filtered on a preference column | `recipients=0` with valid tokens | Check `WHERE customer.notification = 1` is reachable and actually set |
| Credentials still read from `config.php` | Settings screen appears to do nothing | `notify_cred_for()` in the `_onesignal` bodies too |
| FCM as the `else` branch | A future platform silently sends via FCM | Match every platform explicitly |
| Write tool emits LF | GitHub Desktop CRLF warning | Convert new files to CRLF before committing |

---

## 7. Deliberately Out of Scope

State these plainly rather than leaving them implied:

- **No dead-token cleanup.** FCM returns a specific error for unregistered tokens; nothing deletes
  those rows, so every broadcast keeps retrying them.
- **Broadcasts run inside the web request.** At ~77 req/s a large audience still holds the page
  open. Consider a queued/background job, or **FCM topics** (one request, no stored tokens —
  but broadcast only; targeted sends still need tokens).
- **Payment notifications don't exist** in the source product from any entry point (admin, app or
  cron). If a client expects "payment received" pushes, that is new work: a notification template,
  plus wiring both entry points.
