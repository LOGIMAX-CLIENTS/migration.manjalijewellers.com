# Recipe: Dynamic Metal Weight Decimal Precision (Chit / Payment Module)

## Metadata
- **Pattern ID**: PAT-CHIT-CONFIG-001
- **Severity**: MEDIUM
- **Modules Affected**: Payment, DigiGold, Reports, Dashboard, Scheme Account, Mobile API, Admin App API, Settings
- **Auto-fixable**: Partial (grep replacements for `number_format`/`toFixed(3)` calls; new helper files need manual copy)

## Client Scope
- **Applies to**: ALL chit/payment clients
- **Reason**: All clients have hardcoded 3-decimal weight precision. This migrates them to a configurable `metal_wgt_decimal` setting stored in `chit_settings`.

## Created By
- **Developer**: Antigravity
- **Client**: dineshjewellery.in
- **Date**: 2026-04-25
- **Source Bug ID**: N/A (configuration migration, not a bug fix)
- **Source Commits**:
  - `9e998e4` — Dynamic decimal digit phase I (26 files)
  - `cb93639` — Paid installment helper updated (companion standardization in same autoload layer)

---

## Symptom

All metal weight columns (Saved Weight, Benefit Weight, Eligible Weight, Balance Weight, Dashboard weight totals) show exactly **3 decimal places** everywhere — Payment List, Payment Form, Reports (Payment History, Account Statement), Dashboard Collection Summary, Scheme Account screens — regardless of client or regulatory requirement.

Example: `0.070 gm` — cannot be changed to `0.0700` (4 places) or `0.07` (2 places) without code changes.

---

## Root Cause

Metal weight values are formatted using **hardcoded** `number_format($value, 3, '.', '')` in PHP and `.toFixed(3)` in JavaScript across all layers. There is no configuration hook. The `chit_settings` table has `metal_wgt_decimal` and `metal_wgt_roundoff` columns but they are neither loaded into session at login nor used anywhere.

---

## Detection

```bash
# PHP: find all hardcoded 3-decimal weight formatting
grep -rn "number_format.*,3," admin/application/ application/models/ --include="*.php" | grep -iv "amount\|price\|rate\|percent"

# JS: find hardcoded .toFixed(3) for weight display
grep -rn "\.toFixed(3)" admin/assets/js/payment.js admin/assets/js/paymentw.js admin/assets/js/dashboard.js admin/assets/js/scheme_account.js admin/assets/js/admin_settings.js
```

---

## Complete Files Changed

| File | Change | Description |
|---|---|---|
| `chit_settings` DB | verify/add columns | Add `metal_wgt_decimal`, `metal_wgt_roundoff` |
| `admin/application/config/autoload.php` | modify | Add `metal_wgt_digit` to helpers array |
| `admin/application/helpers/metal_wgt_digit_helper.php` | **NEW** | PHP `formatMetalWeight()` + `trim_decimal()` |
| `admin/application/controllers/admin_payment.php` | modify | Load helper; replace `number_format(...,3)` with `formatMetalWeight()` in Save + Digi block |
| `admin/application/controllers/admin_manage.php` | modify | Line-ending / formatting cleanup (bulk) |
| `admin/application/controllers/chit_admin.php` | modify | Add `metal_wgt_decimal`/`roundoff` to session at login |
| `admin/application/models/admin_settings_model.php` | modify | Add `cs.metal_wgt_decimal`, `cs.metal_wgt_roundoff` to `get_company()` SELECT |
| `admin/application/models/payment_model.php` | modify | Add `metal_wgt_decimal`/`roundoff` to `$schemeAcc` AJAX response + `formatMetalWeight()` for avg_payable |
| `admin/application/views/layout/footer.php` | modify | Add `metal_wgt_decimal`/`roundoff` as global JS vars |
| `admin/application/views/include/history_payment.php` | modify | Replace `number_format(..., 3)` → `formatMetalWeight()` for benefits + closing balance |
| `admin/application/views/reports/payment_accountwise.php` | modify | Replace `number_format(..., 3)` → `formatMetalWeight()` throughout account statement report |
| `admin/application/views/reports/payment_history.php` | modify | Replace `number_format(..., 3)` → `formatMetalWeight()` for balance_weight, bal_wt, prev_wt, cumulative totals |
| `admin/assets/js/general.js` | modify | Replace `formatMetalWeight()` + add `trimDecimal()` |
| `admin/assets/js/payment.js` | modify | Replace `.toFixed(3)` weight displays with `formatMetalWeight()`; pass decimal config in `digi_data` (3 sites) |
| `admin/assets/js/paymentw.js` | modify | Replace `.toFixed(3)` weight displays + selected weight with `formatMetalWeight()` |
| `admin/assets/js/admin_settings.js` | modify | Replace `.toFixed(3)` for edit weight display with `formatMetalWeight()` |
| `admin/assets/js/dashboard.js` | modify | Replace `.toFixed(3)` for 5 weight total cells with `formatMetalWeight()` |
| `admin/assets/js/scheme_account.js` | modify | Replace `.toFixed(3)` for add_benefits + closing_balance with `formatMetalWeight()` |
| `application/config/autoload.php` | modify | Add `metal_wgt_digit` to Mobile/API helpers array |
| `application/helpers/metal_wgt_digit_helper.php` | **NEW** | Same helper file for Mobile/API layer |
| `application/controllers/digigold.php` | **NEW** | DigiGold controller (new feature, not decimal-specific) |
| `application/models/digigold_modal.php` | **NEW** | DigiGold model (new feature, not decimal-specific) |
| `application/models/adminappapi_model.php` | modify | Load helper; replace `number_format(...,3)` avg_payable with `formatMetalWeight()` |
| `application/models/mobileapi_model.php` | modify | Load helper; replace `number_format(...,3)` for converted_wgt + avg_payable with `formatMetalWeight()` |
| `application/models/mobileapi_model_25_08_25.php` | modify | Same as mobileapi_model.php (backup variant) |
| `application/models/payment_modal.php` | modify | Replace `number_format(...,3)` avg_payable with `formatMetalWeight()` (2 occurrences) |

### Companion: `sql_common_helper.php` (commit `cb93639`)

Done in the same codebase layer alongside this migration — the autoload.php files were also updated to include `sql_common` helper:

| File | Change |
|---|---|
| `admin/application/helpers/sql_common_helper.php` | **NEW** — `paid_installments_sql()` + `maturity_date_sql()` helpers |
| `application/helpers/sql_common_helper.php` | **NEW** — same helper for mobile/API layer |
| `admin/application/config/autoload.php` | Add `sql_common` to helpers array |
| `application/config/autoload.php` | Add `sql_common` to helpers array |

See `recipe_chit_paid_installments_helper.md` (if created) for the full paid_installments migration. When implementing the metal weight decimal on a new client, apply **both** helper files and **both** autoload updates at the same time.

---

## Fix

### Step 1 — DB: Verify columns exist

```sql
SHOW COLUMNS FROM chit_settings LIKE 'metal_wgt%';

-- If missing, add:
ALTER TABLE chit_settings
  ADD COLUMN metal_wgt_decimal  TINYINT NOT NULL DEFAULT 3
    COMMENT 'Number of decimal places for metal weight display',
  ADD COLUMN metal_wgt_roundoff TINYINT NOT NULL DEFAULT 0
    COMMENT '0 = truncate, 1 = round';

UPDATE chit_settings SET metal_wgt_decimal = 3, metal_wgt_roundoff = 0;
```

---

### Step 2 — NEW FILE: PHP Helper (copy to BOTH locations)

Create identically at:
- `admin/application/helpers/metal_wgt_digit_helper.php`
- `application/helpers/metal_wgt_digit_helper.php`

```php
<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function trim_decimal($value, $precision)
{
    $precision = (int)$precision;
    $factor  = pow(10, $precision);
    $epsilon = 1e-10;
    $value   = ((int)(($value + $epsilon) * $factor)) / $factor;

    if (stripos((string)$value, 'e') !== false) {
        $value = sprintf('%.20f', $value);
        $value = rtrim($value, '0');
        $value = rtrim($value, '.');
    }

    $value = (string)$value;
    if ($precision === 0) return explode('.', $value)[0];
    if (strpos($value, '.') === false) return $value . '.' . str_repeat('0', $precision);

    list($int, $dec) = explode('.', $value, 2);
    $dec = str_pad(substr($dec, 0, $precision), $precision, '0');
    return $int . '.' . $dec;
}

function formatMetalWeight($value)
{
    $CI =& get_instance();
    $roundoff = $CI->session->userdata('metal_wgt_roundoff');
    $decimal  = (int)$CI->session->userdata('metal_wgt_decimal');
    if ($decimal <= 0) $decimal = 3;

    return ($roundoff == 0)
        ? trim_decimal($value, $decimal)
        : number_format((float)$value, $decimal, '.', '');
}
```

---

### Step 3 — Autoload: Register helper in BOTH config files

**File 1**: `admin/application/config/autoload.php`

#### Before
```php
$autoload['helper'] = array('url', 'file', 'html', 'form');
```

#### After
```php
$autoload['helper'] = array('url', 'file', 'html', 'form', 'metal_wgt_digit', 'sql_common');
```

**File 2**: `application/config/autoload.php`

#### Before
```php
$autoload['helper'] = array('url', 'file', 'html', 'form');
```

#### After
```php
$autoload['helper'] = array('url', 'file', 'html', 'form', 'metal_wgt_digit', 'sql_common');
```

> **Note**: `sql_common` must be present alongside `metal_wgt_digit`. Both helpers are required by the full migration — `metal_wgt_digit` for weight formatting, `sql_common` for `paid_installments_sql()` used in all payment models.

---

### Step 4 — Login Controller: Set Session at Login

**File**: `admin/application/controllers/chit_admin.php`

Find `$this->session->set_userdata($data)`. Add to the data array:

#### Before
```php
$data = array(
    // ...
    'is_branchwise_cus_reg' => $branch_set['is_branchwise_cus_reg']
);
```

#### After
```php
$data = array(
    // ...
    'is_branchwise_cus_reg' => $branch_set['is_branchwise_cus_reg'],
    'metal_wgt_decimal'     => $company['metal_wgt_decimal'],
    'metal_wgt_roundoff'    => $company['metal_wgt_roundoff'],
);
```

> `$company` comes from `get_company()` — must add fields there first (Step 5).

---

### Step 5 — Settings Model: Add Fields to `get_company()` SELECT

**File**: `admin/application/models/admin_settings_model.php`

#### Before
```php
$sql = "Select c.id_company, ..., c.server_type
        from company c join chit_settings cs ...";
```

#### After
```php
$sql = "Select c.id_company, ..., c.server_type,
               cs.metal_wgt_decimal, cs.metal_wgt_roundoff
        from company c join chit_settings cs ...";
```

---

### Step 6 — Footer: Add JS Global Variables

**File**: `admin/application/views/layout/footer.php`

In the `<script>` block (near `base_url`, `currency_symbol`):

```php
var metal_wgt_decimal  = "<?php echo (int)$this->session->userdata('metal_wgt_decimal'); ?>";
var metal_wgt_roundoff = "<?php echo (int)$this->session->userdata('metal_wgt_roundoff'); ?>";
```

---

### Step 7 — JS: Replace `formatMetalWeight()` + add `trimDecimal()` in `general.js`

**File**: `admin/assets/js/general.js`

Replace the existing weight formatting functions with:

```javascript
function trimDecimal(value, decimals) {
    decimals = decimals || 3;
    const factor = Math.pow(10, decimals);
    const truncated = Math.trunc((Number(value) + Number.EPSILON) * factor) / factor;
    let str = truncated.toString();
    if (!str.includes(".")) str += ".";
    let [intPart, decPart = ""] = str.split(".");
    decPart = decPart.padEnd(decimals, "0");
    return intPart + "." + decPart;
}

function formatMetalWeight(value) {
    var decimals = parseInt(metal_wgt_decimal) || 3;
    if (!value) return "";
    value = value.toString().trim();
    var match = value.match(/^([\d.]+)\s*(.*)$/);
    if (!match) return "";
    var numericValue = parseFloat(match[1]);
    var unit = match[2] || "";
    let formatted = (metal_wgt_roundoff == 0)
        ? trimDecimal(numericValue, decimals)
        : numericValue.toFixed(decimals);
    return unit ? formatted + " " + unit : formatted;
}
```

---

### Step 8 — PHP: Replace in admin_payment.php (Save block)

**File**: `admin/application/controllers/admin_payment.php`

In the Digi Gold save block:

#### Before
```php
$metal_wgt = formatMetalWeight($metal_wgt);
// ...
$metal_wgt = formatMetalWeight($amount/$generic['metal_rate']);
// ...
$dg_saved_benefit = number_format(($dg_saved_benefit_amt / $generic['metal_rate']), 3);
```

#### After
```php
$metal_wgt = formatMetalWeight($metal_wgt);
// ...
$metal_wgt = formatMetalWeight($amount / $generic['metal_rate']);
// ...
$dg_saved_benefit = formatMetalWeight($dg_saved_benefit_amt / $generic['metal_rate']);
```

---

### Step 9 — PHP: Replace in payment_model.php (AJAX response + avg_payable)

**File**: `admin/application/models/payment_model.php`

#### avg_payable (flexible weight scheme)
```php
// Before:
$avg_payable = number_format($paid_wgt / $record->avg_calc_ins, 3);
// After:
$avg_payable = formatMetalWeight($paid_wgt / $record->avg_calc_ins);
```

#### $schemeAcc AJAX response (add at end of array)
```php
'preclose_benefits'  => ($record->allow_preclose == 1 ? $record->preclose_benefits : 0),
'metal_wgt_decimal'  => (int)$this->session->userdata('metal_wgt_decimal'),   // ← ADD
'metal_wgt_roundoff' => $this->session->userdata('metal_wgt_roundoff'),        // ← ADD
```

---

### Step 10 — PHP: Replace in Mobile/Admin App API models

**File**: `application/models/mobileapi_model.php`

Load helper in `__construct()`:
```php
$this->load->helper('metal_wgt_digit');
```

Replace `number_format(..., 3)` weight calls:
```php
// avg_payable:
$avg_payable = formatMetalWeight($paid_wgt / $record->avg_calc_ins);

// converted_wgt (3 occurrences — GST inclusive, GST exclusive, no GST):
$converted_wgt = formatMetalWeight((float)(($sch_data['amount'] - $gst_amt) / $gold_rate));
$converted_wgt = formatMetalWeight((float)($sch_data['amount'] / $gold_rate));
```

**File**: `application/models/adminappapi_model.php`

Same — load helper + replace avg_payable:
```php
$this->load->helper('metal_wgt_digit');
// ...
$avg_payable = formatMetalWeight($paid_wgt / $record->avg_calc_ins);
```

**File**: `application/models/payment_modal.php` (mobile payment model — 2 occurrences)
```php
// Both occurrences of avg_payable in flexible_sch_type == 3 block:
$avg_payable = formatMetalWeight($paid_wgt / $record->avg_calc_ins);
```

---

### Step 11 — PHP Views: Replace in Report Views

**File**: `admin/application/views/reports/payment_history.php`

Replace all `number_format($..., "3", ".", "")` for weight columns:
```php
// Before:
$prev_wt = number_format($account['customer']['balance_weight'], "3", ".", "");
$bal_wt  = number_format(($bal_wt + $pay['metal_weight']), "3", ".", "");
echo number_format($bal_wt, "3", ".", "");
echo number_format(($bal_wt + $prev_wt), "3", ".", "").\" Gm\";

// After:
$prev_wt = formatMetalWeight($account['customer']['balance_weight']);
$bal_wt  = formatMetalWeight($bal_wt + ($pay['metal_weight'] != "" ? $pay['metal_weight'] : 0));
echo formatMetalWeight($bal_wt);
echo formatMetalWeight($bal_wt + $prev_wt)." Gm";
```

**File**: `admin/application/views/include/history_payment.php`

```php
// Before:
echo number_format($account['customer']['benefits'], "3", ".", "")." ";
echo number_format($account['customer']['closing_balance'], "3", ".", "")." Gm";

// After:
echo formatMetalWeight($account['customer']['benefits'])." ";
echo formatMetalWeight($account['customer']['closing_balance'])." Gm";
```

**File**: `admin/application/views/reports/payment_accountwise.php`

Search for all `number_format(..., "3", ...)` weight columns and replace with `formatMetalWeight(...)` throughout.

---

### Step 12 — JS: Replace `.toFixed(3)` in payment.js

**File**: `admin/assets/js/payment.js`

```javascript
// Before (multiple locations — weight display and selected weight sum):
$('#sel_wt').html(parseFloat(selected_weight).toFixed(3));
$(wtid).val(parseFloat(sum).toFixed(3));
$('#grand_weight').html(parseFloat(sum_by_class('payment_weight')).toFixed(3));
$(\"#amttowgt\").html(parseFloat(weight.toFixed(3))+' <strong>gm</strong>');

// After:
$('#sel_wt').html(formatMetalWeight(selected_weight));
$(wtid).val(formatMetalWeight(sum));
$('#grand_weight').html(formatMetalWeight(sum_by_class('payment_weight')));
$("#amttowgt").html(formatMetalWeight(weight)+' <strong>gm</strong>');
```

Also add `metal_wgt_decimal`/`roundoff` to all 3 `digi_data` construction sites:
```javascript
// In account_detail_view(), Proceed success, Proceed out-of-range:
var digi_data = {
    benefit_type:       data.dg_benefit_type    || 0,
    benefit_value:      data.dg_benefit_value   || 0,
    benefit_content:    data.dg_benefit_content || 'Digi Gold Benefit',
    currency_symbol:    data.currency_symbol    || '',
    metal_wgt_decimal:  data.metal_wgt_decimal  || metal_wgt_decimal,
    metal_wgt_roundoff: data.metal_wgt_roundoff,
};
```

---

### Step 13 — JS: Replace `.toFixed(3)` in paymentw.js

**File**: `admin/assets/js/paymentw.js`

```javascript
// Before:
selected_weight = parseFloat(parseFloat(selected_weight) + parseFloat($(this).val())).toFixed(3);
$('#sel_wt').html(parseFloat(selected_weight).toFixed(3));
$(wtid).val(parseFloat(sum).toFixed(3));
$('#grand_weight').html(parseFloat(sum_by_class('payment_weight')).toFixed(3));

// After:
selected_weight = parseFloat(parseFloat(selected_weight) + parseFloat($(this).val()));
$('#sel_wt').html(formatMetalWeight(selected_weight));
$(wtid).val(formatMetalWeight(sum));
$('#grand_weight').html(formatMetalWeight(sum_by_class('payment_weight')));
```

---

### Step 14 — JS: Replace `.toFixed(3)` in dashboard.js

**File**: `admin/assets/js/dashboard.js`

5 weight total cells in `set_collection_summary()`:
```javascript
// Before:
$('#op_total_wt_scheme').text((op_total_wt_scheme).toFixed(3) + 'g');
$('#collection_tot_wt').text((collection_tot_wt).toFixed(3) + 'g');
$('#closed_total_wt_scheme').text((closed_total_wt_scheme).toFixed(3) + 'g');
$('#cancelled_total_wt_scheme').text((cancelled_total_wt_scheme).toFixed(3) + 'g');
$('#closing_balance_total_wt_scheme').text((closing_balance_total_wt_scheme).toFixed(3) + 'g');

// After:
$('#op_total_wt_scheme').text(formatMetalWeight(op_total_wt_scheme) + 'g');
$('#collection_tot_wt').text(formatMetalWeight(collection_tot_wt) + 'g');
$('#closed_total_wt_scheme').text(formatMetalWeight(closed_total_wt_scheme) + 'g');
$('#cancelled_total_wt_scheme').text(formatMetalWeight(cancelled_total_wt_scheme) + 'g');
$('#closing_balance_total_wt_scheme').text(formatMetalWeight(closing_balance_total_wt_scheme) + 'g');
```

---

### Step 15 — JS: Replace `.toFixed(3)` in scheme_account.js

**File**: `admin/assets/js/scheme_account.js`

```javascript
// Before (3 occurrences):
var add_benefits = parseFloat($('#add_benefits').val()).toFixed(3);   // appears twice
$('#closing_balance').val(parseFloat(c_bal).toFixed(3));

// After:
var add_benefits = formatMetalWeight($('#add_benefits').val());       // both occurrences
$('#closing_balance').val(formatMetalWeight(c_bal));
```

---

### Step 16 — JS: Replace `.toFixed(3)` in admin_settings.js

**File**: `admin/assets/js/admin_settings.js`

```javascript
// Before:
var weight = parseFloat($("#ed_weight").val()).toFixed(3);

// After:
var weight = formatMetalWeight($("#ed_weight").val());
```

---

## Verification

1. Go to **Settings → General** — confirm `metal_wgt_decimal` input field exists and shows current value (e.g. `3`)
2. Change `metal_wgt_decimal` to `4`, save, **log out and back in**
3. **Dashboard** → Collection Summary → weight totals show 4 decimal places
4. **Payment → Add** → select Digi Gold scheme → click Proceed → Benefit Weight shows 4 decimal places
5. Save payment → **Payment → List** → Benefit Weight column shows 4 decimal places
6. **Reports → Payment History** → balance weight column shows 4 decimal places
7. **Reports → Account Statement** → weight columns show 4 decimal places
8. **Scheme Account** → add benefits → closing balance shows 4 decimal places
9. Check DB: `payment.saved_benefits` column stores value with 4 decimal places
10. Revert to `3`, re-login → confirm all displays revert correctly

---

## Notes

- `metal_wgt_roundoff = 0` → **truncate** (never rounds up — critical for gold weight accuracy to avoid over-crediting)
- `metal_wgt_roundoff = 1` → standard mathematical rounding
- PHP `formatMetalWeight()` reads from **session** — users must re-login after changing the setting
- JS `formatMetalWeight()` reads from `metal_wgt_decimal` global var (set from session in `footer.php`) — same re-login requirement
- `trim_decimal()` uses epsilon correction (`1e-10`) to avoid float truncation bugs (e.g. `0.0699999...` truncating to `0.069` instead of `0.070`)
- The **same helper file** must exist in both `admin/application/helpers/` (for admin panel) and `application/helpers/` (for mobile/API layer)
- `admin_manage.php` in the commit had bulk line-ending changes — no decimal-related logic changes needed there
