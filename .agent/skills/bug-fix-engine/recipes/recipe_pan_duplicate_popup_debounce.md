---
id: recipe_pan_duplicate_popup_debounce
name: Duplicate PAN Card Warning Popup on High-Value Billing
version: 1.0
created: 2026-04-21
module: Billing (ret_billing.js)
bug_id: BIL-PAN001
severity: P1
category: UI / Duplicate Popup / Race Condition
symptom_keywords: PAN popup, toaster, duplicate warning, double modal, high value bill, pan card twice
---

## Symptom

During high-value billing transactions (amount ≥ PAN threshold), the PAN card warning toaster/popup appears **multiple times** (typically 2-3 times in rapid succession) instead of once. The user sees the same PAN entry dialog stacked or repeated.

## Root Cause

Multiple functions in the billing workflow check the high-value threshold and trigger the PAN warning independently. Each function uses its own **local boolean flag** (`panWarned = false`) which is reset per function call. When these functions are called concurrently (e.g., payment mode selection + amount field blur + form submit), each one evaluates its local flag as `false`, and all trigger the popup.

**Pattern:** Function-scoped guard flags don't survive concurrent execution. The second caller sees `false` even though the first already flipped it.

## Detection

Search for this pattern in `admin/assets/js/ret_billing.js`:

```bash
grep -n "panWarned\|pan_warned\|panShown\|panPopup" admin/assets/js/ret_billing.js
```

If multiple local `var panWarned = false` declarations exist inside separate functions → vulnerable.

Also check:
```bash
grep -n "pan\|PAN" admin/assets/js/ret_billing.js | grep -i "warn\|popup\|modal\|toast"
```

## Before (Vulnerable Code)

```javascript
// Each function had its own isolated flag
function checkPaymentAmount() {
    var panWarned = false; // local scope — resets every call
    if (totalAmount >= panThreshold) {
        if (!panWarned) {
            showPanWarning(); // fires even if called 50ms apart
            panWarned = true;
        }
    }
}

function validateAndSubmit() {
    var panWarned = false; // DIFFERENT local — also fires
    if (totalAmount >= panThreshold) {
        if (!panWarned) {
            showPanWarning(); // second popup triggered
            panWarned = true;
        }
    }
}
```

## After (Fixed Code)

```javascript
// Global debounce flag — shared across all functions
var _panWarnLock = false;

function showPanWarningOnce() {
    if (_panWarnLock) { return; }        // Already shown — skip
    _panWarnLock = true;
    showPanWarning();                     // Show popup exactly once
    setTimeout(function () {
        _panWarnLock = false;            // Reset after safe window (e.g. 1500ms)
    }, 1500);
}

// All callers now use showPanWarningOnce() instead of showPanWarning() directly
function checkPaymentAmount() {
    if (totalAmount >= panThreshold) {
        showPanWarningOnce();
    }
}

function validateAndSubmit() {
    if (totalAmount >= panThreshold) {
        showPanWarningOnce();
    }
}
```

**Key design principle:** The lock must be **module-level (global in file scope)**, not function-local. The `setTimeout` reset allows re-triggering on a new transaction without sticky lockout.

## Verification Steps

1. Open a billing transaction
2. Enter an amount above the PAN threshold (typically ₹2,00,000+)
3. Tab through payment fields (triggers multiple validation functions concurrently)
4. Confirm PAN warning appears **exactly once** — not twice or more
5. Complete the transaction, start a new one above threshold → confirm warning fires again (lock resets)
6. Verify below-threshold transactions receive no PAN popup

## Files Changed

- `admin/assets/js/ret_billing.js` — Replace per-function `var panWarned` with global `_panWarnLock` + `showPanWarningOnce()` wrapper

## Clients Found In

- navratnajewellery (fixed 2026-04-21)

## Notes

- `setTimeout` duration should match or exceed the AJAX response time so the lock doesn't drop while a modal is open
- If `showPanWarning()` itself shows a modal with user interaction, consider resetting on modal close (user `OK`/dismiss) instead of setTimeout
- This pattern applies to ANY billing validation that can be triggered from multiple concurrent event handlers (blur, change, click, form submit)
- Related pattern: `recipe_concurrent_payment_race_condition.md` — same root problem class, different trigger
