# Branch Transfer — Round 4: JS Save Handler & View Layer

> **Date**: 2026-03-11
> **Files**: `admin/assets/js/ret_branch_transfer.js` (8,879 lines) + `admin/application/views/branch_transfer/` (8 files)

---

## Bugs Found: 3

| Bug ID | Severity | Title | Lines | Track |
|---|---|---|---|---|
| BRN-R401 | **P1** | Duplicate DOM ID `id_product` in form.php | form.php L267+L288 | A |
| BRN-R402 | **P2** | Unescaped PHP Output in Views (XSS) | Views various | A |
| BRN-R403 | **P2** | `async:false` in AJAX Calls (UI Freeze) | JS L4951, L5011, L5021, L5071 | A |

### BRN-R401 — Duplicate DOM ID `id_product` [P1]
```html
<!-- form.php L267: -->
<input type="hidden" class="form-control" id="id_product">
<!-- form.php L288: -->
<input type="hidden" class="form-control" id="id_product">
```
**Root Cause**: Two hidden inputs with same `id="id_product"`. jQuery `$('#id_product')` always returns the first. The second element's value is inaccessible via ID selector.
**Impact**: Product filtering in the second form section (non-tagged) may silently use the wrong product ID.

### BRN-R402 — Unescaped PHP Output in Views [P2]
View files use `echo $variable` and `<?= $variable ?>` without `htmlspecialchars()`. If any user-controlled data reaches these variables, XSS is possible.
**Pattern**: PAT-SEC-004
**Note**: Most variables here are from session/config, so risk is medium. But `flashdata` messages include user-facing strings that could be manipulated.

### BRN-R403 — `async:false` in AJAX Calls [P2]
4 AJAX calls use `async:false`:
- `send_otp()` L4951+L5011 — synchronous OTP send freezes the browser
- `verify_otp()` L5071 — synchronous OTP verify freezes the browser

**Impact**: Browser becomes unresponsive during SMS OTP operations. If SMS service is slow, user sees a frozen page with no feedback.
**Note**: `async:false` is deprecated in jQuery 3.x. Legacy pattern.
