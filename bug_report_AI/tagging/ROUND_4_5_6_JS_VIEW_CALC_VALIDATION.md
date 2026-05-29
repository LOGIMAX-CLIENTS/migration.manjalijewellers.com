# Rounds 4-6 — JS/View, Calculations, Validation & AJAX Analysis

> **Files**: `admin/assets/js/ret_tagging.js` (~13,200 lines), `views/tagging/*.php`
> **Date**: 2026-02-24
> **Bugs Found**: 15

---

# Round 4 — JS Save Handler & View Layer

## TAG-R401 — 33 Duplicate HTML IDs in form.php (P1)

**Category**: DOM | **Track**: A
**Description**: 33 element IDs appear more than once in `form.php`. jQuery `$('#id')` only returns the first match — all others become invisible to JS.
**Key duplicates**:
| ID | Count | Impact |
|---|---|---|
| `myModalLabel` | 9 | Modal titles won't update properly |
| `close_stone_details` | 4 | Wrong modal may close |
| `branch_select` / `id_branch` | 3 each | Branch filter breaks |
| `tag_id` | 2 | Tag save may reference wrong element |
| `metal_rate` / `purity` | 2 each | Rate calculations affected |
| `tax_percentage` / `tgi_calculation` | 2 each | Tax calculations affected |
**Fix**: Rename duplicates with unique suffixes (e.g., `myModalLabel_stone`, `myModalLabel_charge`)

---

## TAG-R402 — 44 Unescaped PHP Output in Views (XSS) (P1)

**Category**: Security | **Track**: A
**Description**: 44 instances of `<?= $var ?>` or `echo $var` without `htmlspecialchars()`. If any contain user-entered data, XSS is exploitable.
**Fix**: Wrap all output in `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`

---

## TAG-R403 — 231 .on() with Zero .off() — Event Handler Leak (P1)

**Category**: Architecture | **Track**: A
**Description**: 231 `.on()` event bindings and **zero** `.off()` calls. On pages with AJAX-loaded content, handlers accumulate — clicks fire multiple times.
**Fix**: Add `.off('event').on('event', ...)` pattern for dynamically bound handlers

---

## TAG-R404 — 5 Inline JS Event Handlers (P2)

**Category**: Code Quality | **Track**: A
**Description**: 5 `onclick=`/`onchange=`/`onkeyup=` attributes in `form.php`. Mix of inline and delegated event binding causes maintenance confusion.
**Fix**: Move to `.on()` delegation in JS file

---

# Round 5 — JS Calculations & Data Binding

## TAG-R501 — 396 Unguarded parseFloat Calls (NaN Risk) (P0)

**Category**: Financial/Calculation | **Track**: B
**Description**: 604 `parseFloat()` calls, only 208 use `|| 0` guard (34%). Any empty or non-numeric form field produces `NaN`, which propagates through all subsequent calculations.
**Impact**: Tag value, weight, making charges all become `NaN`
**Fix**: Replace all with `parseFloat(val) || 0`

---

## TAG-R502 — 371 Missing toFixed (Precision Loss) (P1)

**Category**: Financial | **Track**: B  
**Description**: 604 parseFloat calls vs 233 toFixed calls (38%). Financial values displayed without rounding create inconsistencies between JS display and DB storage.
**Fix**: Apply `.toFixed(2)` for currency and `.toFixed(3)` for weight on all final assignments

---

## TAG-R503 — No Division-by-Zero Guards (P1)

**Category**: Calculation | **Track**: B
**Description**: Multiple division operations in calculation functions (e.g., wastage %, rate/gm) with no check for zero divisor. If weight is 0, division produces `Infinity`.
**Key locations**: L9950 (wastage calc), L9978, L9986, L9994 (making charge)
**Fix**: Add `divisor === 0 ? 0 : (val / divisor)` guard

---

## TAG-R504 — 25 JS Calculate Functions vs 2 PHP (P0)

**Category**: Business Logic | **Track**: B
**Description**: 25 JS `calculate` functions vs only 2 PHP-side equivalents. Tag values computed entirely client-side with no server-side re-validation. Users with browser devtools can manipulate any calculation.
**Impact**: Wrong tag values saved to DB, financial data integrity compromised
**Fix**: Add PHP recalculation in controller save method before DB insert

---

# Round 6 — Validation Functions & AJAX Endpoints

## TAG-R601 — 70 Missing AJAX Error Handlers (P1)

**Category**: Error Handling | **Track**: A
**Description**: 118 `$.ajax()` calls but only 48 `error:` handlers (59% missing). When server returns 500 or timeout, user sees frozen UI.
**Fix**: Add `error: function(xhr) { alert('...');}` to all AJAX calls

---

## TAG-R602 — 15 alert() Without return false (P1)

**Category**: Validation | **Track**: B
**Description**: 15 `alert()` validation messages that don't stop form processing. After showing the error, execution continues — invalid data submitted.
**Key lines**: L406, L3370, L3502, L5352, L10406, L10426
**Fix**: Add `return false;` after each validation alert

---

## TAG-R603 — Weight Boundary Checks (P0)

**Category**: Business Logic | **Track**: B
**Description**: ~247 weight calculations across JS but only 1 boundary check found. Negative weights, zero gross weight, net_wt > gross_wt — none are validated.
**Impact**: Physically impossible tag data saved (e.g., -5g gold tag)
**Fix**: Add boundary checks: `if (net_wt > gross_wt || net_wt < 0) { alert(...); return false; }`

---

## TAG-R604 — No Client-Side CSRF Token in AJAX (P1)

**Category**: Security | **Track**: A
**Description**: None of the 118 AJAX requests send a CSRF token. Combined with TAG-002 (no server-side check), this is a complete CSRF bypass chain.
**Fix**: Add CSRF token to AJAX `beforeSend` header or `data` payload

---

## TAG-R605 — 1 Hardcoded URL in AJAX (P2)

**Category**: Code Quality | **Track**: A
**Description**: 117 of 118 AJAX calls correctly use `base_url` variable. 1 uses hardcoded path.
**Fix**: Replace hardcoded path with `base_url + "..."`

---

## TAG-R606 — Select2 Deprecated API Usage (P2)

**Category**: Code Quality | **Track**: A
**Description**: Pattern scan found instances matching `select2("val", ...)` deprecated API pattern. Should use `.val(...).trigger('change')`.
**Fix**: Replace all `select2("val", ...)` with `.val(x).trigger('change')`

---

## Summary

| Round                | P0    | P1    | P2    | Total                              |
| -------------------- | ----- | ----- | ----- | ---------------------------------- |
| R4 (View/DOM)        | 0     | 3     | 1     | 4                                  |
| R5 (Calculations)    | 2     | 2     | 0     | 4                                  |
| R6 (Validation/AJAX) | 1     | 3     | 2     | 6                                  |
| **Total**            | **3** | **8** | **3** | **15** (note: 1 is combined R4+R6) |

| Track            | Count |
| ---------------- | ----- |
| **A (System)**   | 8     |
| **B (Business)** | 6     |
