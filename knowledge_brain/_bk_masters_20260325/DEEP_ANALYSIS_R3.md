# Masters Module — Deep Analysis Round 3

> **Brain Updated:** 2026-03-17 | **Round:** 3 Complete — **FINAL ROUND**

---

## Scope Covered in R3

1. Entity CRUD spot-checks: `bank()`, `drawee()`, `payment_mode()`, `ledger()` — full traces
2. `design_form()` (department/designation combined entity)
3. `db_backup()` + `compress()` + `download()` — full traces
4. View files — XSS scan across all 150 master views
5. JS AJAX error handler audit — identified 61 missing handlers

---

## 1. Entity CRUD Pattern Verification

Five representative entity CRUDs were fully traced to validate (or disprove) the uniform pattern assumption made in R1.

### `bank()` — Exemplary CRUD with Form Validation ✅

```php
public function bank($type, $id = "")
{
    // Save case:
    $rules = [['field' => 'bank[bank_name]', 'rules' => 'required|callback_valid_str']];
    $this->form_validation->set_rules($rules);
    if ($this->form_validation->run() == FALSE) {
        // Validation failed → flash + redirect
    } else {
        $bank = $this->input->post('bank');  // ← CI input helper ✅
        $status = $this->$model->bankDB("insert", "", $bank);
    }

    // Update case (coded by jothika on 10-7-2025):
    // Same validation + duplicate check in model returning ['status', 'reason']
    if ($status['reason'] === 'Duplicate') { /* handles gracefully */ }
}
```

**Assessment:** Model `bankDB()` uses CI query builder (safe). Form validation is run before insert. Duplicate detection implemented. **This is the cleanest CRUD in the module — written later (2025) vs older CRUDs.**

Contrast note: Older CRUDs (drawee, classification, weight) do NOT have form validation.

---

### `drawee()` — Older CRUD Pattern, No Validation ⚠️

```php
case 'Save':
    $bank = $this->input->post('bank');
    $status = $this->$model->draweeDB("insert", "", $bank);
```

Uses CI input helper but **no form validation** — empty names can be saved.

**No new bug ID added** — this is a pattern finding, not a unique bug. Grouped with MST-BUG-032 below.

---

### `payment_mode()` — Has Validation (newer code) ✅

```php
$rules = [['field' => 'paymode[mode_name]', 'rules' => 'required|callback_valid_str']];
$this->form_validation->set_rules($rules);
// coded by jothika on 11-7-2025
```

Validated ✅. Consistent with bank(). More recently written.

---

### `ledger()` — Complex Multi-Table Save ⚠️

This is the most recently developed entity (full POST-AJAX flow):

```php
case 'Save':
    $ledger_name = $this->input->post('ledger_name');
    $opening_balance = $this->input->post('opening_balance');
    $bank_ids = $this->input->post('bank_ids');
    $paymode_ids = $this->input->post('paymode_ids');

    $mappings = array();
    if (!empty($bank_ids)) {
        foreach ($bank_ids as $key => $bank_id) {
            $mappings[] = array('type' => 'BANK', 'reference_id' => $bank_id);
        }
    }
    if (!empty($paymode_ids)) {
        foreach ($paymode_ids as $key => $mode_id) {
            $mappings[] = array('type' => 'PAYMODE', 'reference_id' => $mode_id);
        }
    }

    $result = $this->$model->ledgerDB('insert', '', $data);
    if ($this->input->is_ajax_request()) {
        echo json_encode(array('status' => $result ? 'success' : 'error', ...));
    }
```

**New finding:** `$bank_id` and `$mode_id` come from `$_POST` array values. The model's `ledgerDB('insert')` uses CI insert — safe. However **no validation on `$opening_balance`** — a non-numeric value could corrupt the ledger balance.

**MST-BUG-032 | P3:** Opening balance in `ledger('Save')` has no numeric validation.

---

### `design_form()` — Department + Designation Logic at Line 1446

This handles both **department** AND **designation** (confusingly named). No `department()` function exists separately.

```php
function design_form($type = "", $id = "")
{
    // Handles:
    // settings/design_form/dept/list   → departments
    // settings/design_form/desig/list  → designations
    // Both via $type + $id params
}
```

Uses CI input + CI query builder — clean pattern ✅.

---

## 2. `db_backup()` — Full Security Trace

```php
function db_backup()
{
    date_default_timezone_set('Asia/Calcutta');
    $model = self::SET_MODEL;
    $path = '../data/backup/';         // ← stores OUTSIDE webroot ✅ (corrected from R1 assumption)
    $filename = 'backup_' . date('d_m_Y_H_i_s');

    $this->load->dbutil();
    $prefs = array(
        'format' => 'zip',
        'add_drop' => TRUE,
        'add_insert' => TRUE,
    );
    $backup = &$this->dbutil->backup($prefs);

    $this->load->helper('file');
    if (!is_dir($path)) {
        mkdir($path, 0777, TRUE);   // ← world-writable (MST-BUG-014 family)
    }
    $write_status = write_file($path . $filename . '.zip', $backup);
    if ($write_status) {
        $this->$model->database_backup('insert', '', $db_data); // log to DB
    }

    $this->load->helper('download');
    force_download($filename . '.zip', $backup);  // ← sends to browser immediately
}
```

**R1 Correction:** The backup path `'../data/backup/'` is **OUTSIDE the webroot** (relative to the app's `admin/` root, `..` goes up one level). This actually mitigates MST-BUG-015. **MST-BUG-015 should be DOWNGRADED or CLOSED** — the backup path is not in the webroot.

However:
- `mkdir(0777)` — still world-writable (covered by MST-BUG-014 family)
- `force_download()` sends the full backup to the browser — **any logged-in employee** can trigger this (no role check). Still a significant privilege concern.

**MST-BUG-033 | P2:** `db_backup()` — no role/profile check before initiating and downloading a full database backup. Any logged-in employee (junior collection agent, etc.) can download the full database.

---

## 3. `compress()` + `download()` — Trace

```php
function compress()
{
    $this->load->library('zip');
    $path = 'Unregistered List/';    // ← relative path = webroot-relative!
    $this->zip->read_dir($path);
    $this->zip->download('customer_list.zip');
    $data = array('File download successfully');  // ← this data is never used
}

function download()
{
    $file_name = 'unregistered_cus.csv';  // ← hardcoded relative path = webroot
    header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
    readfile($file_name);  // ← reads file relative to current dir (webroot)
    exit();
}
```

**MST-BUG-034 | P2:** `download()` — `readfile('unregistered_cus.csv')` reads a file relative to the Apache `DocumentRoot`. If this file does not exist, `readfile` emits a PHP warning and delivers an empty response. More critically, no authentication role check — any employee can trigger a customer CSV download.

**MST-BUG-035 | P3:** `compress()` — `$data = array('File download successfully')` is computed and immediately abandoned (never echoed or returned). Dead assignment.

---

## 4. View Files — XSS Scan

### Scan Result Summary

All 150 master view files scanned. The dominant pattern found:

```php
// Found in list.php across ~40+ master entity views:
$message = $this->session->flashdata('chit_alert');
echo $message['title'];     // ← LINE 32–35 pattern, NO htmlspecialchars
echo $message['message'];   // ← unescaped flash message echoed directly to HTML
echo $message['class'];     // ← used in class attribute: alert-<?php echo $message['class'] ?>
```

### XSS Risk Assessment

**`$message['class']`** — used inside `class="alert-<?php echo $message['class'] ?>"`. This comes entirely from controller-set flashdata with values `'success'` / `'danger'`. Controllers never set this from user input. **Low real-world risk.**

**`$message['title']` and `$message['message']`** — These come from controller hardcoded strings ("Bank added successfully", etc.) in most cases. However:

**MST-BUG-036 | P2:** In `general_settings('Save')` and several other methods, message strings potentially incorporate the POST tab or entity name via `$general['tab_name']` and similar. If any controller ever interpolates user input into the flash message string and that string is echoed unescaped, this becomes reflected XSS. The current escape boundary is loose — it relies on every controller ensuring hardcoded strings only.

**Pattern in all 40+ list.php files:**
```php
<div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
    <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
    <?php echo $message['message']; ?>
</div>
```

**Fix (systemic):** All three echoes should use `htmlspecialchars()`:
```php
<div class="alert alert-<?php echo htmlspecialchars($message['class']); ?> alert-dismissable">
    <h4><?php echo htmlspecialchars($message['title']); ?>!</h4>
    <?php echo htmlspecialchars($message['message']); ?>
</div>
```

---

## 5. JS AJAX Error Handler Audit

Total AJAX blocks: **87** | With `error:` handler: **26** | Without: **61**

### Highest-risk unhandled AJAX calls (by function impact):

| JS Function | URL Called | Risk if Silently Failed |
|---|---|---|
| Rate save AJAX | `settings/rate/Save` | Admin sees no error, rate not saved, mobile API not updated |
| Branch save/update | `settings/branch/Add` | Branch created but image silently fails — no feedback |
| Gateway update | `settings/gateway/Update_demo` | Credentials silently not saved |
| SMS API update | `settings/sms_api/Update` | SMS gateway credentials lost silently |
| Village form | `settings/village/Save` | Village insert may silently fail |
| Notification template | `settings/notification/Update` | Push notification config unchecked |
| Metal rate per-branch | `settings/matal_ratelist` | Branch rate display not refreshed |

**MST-BUG-037 | P3:** 61 of 87 AJAX calls in `admin_settings.js` lack `error:` handlers — UI silently stalls or shows misleading state when server errors occur (500, 403, timeout).

---

## 6. New Bugs Found in R3

| Bug ID | P | Location | Description |
|---|---|---|---|
| MST-BUG-032 | P3 | Controller L4573 | `ledger('Save')`: no numeric validation on `opening_balance` |
| MST-BUG-033 | P2 | Controller L2232 | `db_backup()`: no role check — any employee can download full DB backup |
| MST-BUG-034 | P2 | Controller L3831 | `download()`: no role check + `readfile` relative path to hardcoded CSV |
| MST-BUG-035 | P3 | Controller L3846 | `compress()`: dead `$data` assignment, result never used |
| MST-BUG-036 | P2 | All master views (40+) | Flash message echoed without `htmlspecialchars()` — potential XSS vector |
| MST-BUG-037 | P3 | `admin_settings.js` | 61/87 AJAX calls without error handlers |

---

## 7. R1 Bug Correction — MST-BUG-015

**MST-BUG-015** (originally: "DB backup stored in webroot") should be **CORRECTED**:

The actual backup path is `'../data/backup/'` — this is **outside** the webroot, not inside it. The backup file is NOT publicly accessible via HTTP. The backup path is safe.

**MST-BUG-015 status:** DOWNGRADE to P3 / documentation note. The real remaining concern is just the missing role check (now captured as MST-BUG-033).

---

## 8. Pattern Analysis — Clean vs Dirty CRUDs

Based on all 3 rounds of analysis, two generations of CRUD code exist in this controller:

| Generation | Authored | Validation | Input | Pattern |
|---|---|---|---|---|
| **Old** (pre-2024) | Various devs | ❌ None | CI input post() | Bare insert/update, no duplicate check |
| **New** (2025) | "jothika" | ✅ `form_validation` + `callback_valid_str` | CI input post() | Validates + handles duplicates gracefully |

New-pattern entities: `bank`, `payment_mode`, others noted as "coded by jothika on x-7-2025"  
Old-pattern entities: `drawee`, `classification`, `weight`, `country`, `state`, `city`, `department`, `designation`

**MST-BUG-038 | P3:** ~15 older entity CRUDs lack form validation — empty/blank names can be saved for drawee, classification, weight, country/state/city, department, designation masters. Consistent `required|callback_valid_str` should be retrofitted.
