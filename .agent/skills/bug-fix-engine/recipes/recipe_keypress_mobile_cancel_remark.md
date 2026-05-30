# Recipe: Mobile Cancel Remark — keypress Event Doesn't Fire on Virtual Keyboards

## Metadata
- **Pattern ID**: PAT-JS-MOBILE-001
- **Severity**: HIGH
- **Modules Affected**: Billing, Purchase Order, Purchase Approval, Order, Estimation
- **Auto-fixable**: Yes (string replacement: `keypress` → `input`)

## Client Scope
- **Applies to**: ALL
- **Reason**: All clients use the same JS event binding pattern for cancel remark textareas

## Created By
- **Developer**: Antigravity
- **Client**: karpagamjewels.com
- **Date**: 2026-04-17
- **Source Bug ID**: N/A

## Symptom
Cancel modal opens on mobile but the "Cancel" / "Submit" button stays permanently disabled after typing remarks. Works fine on desktop.

User flow on mobile:
1. Tap cancel button → modal opens ✅
2. Type remarks in textarea → nothing happens ❌
3. Submit/Cancel button stays `disabled` → user is stuck ❌

## Root Cause
The `keypress` event is **deprecated** (MDN Web Docs) and **does NOT fire on mobile virtual keyboards** in most mobile browsers (Chrome Android, Safari iOS). The code uses `$('#cancel_remark').on('keypress', ...)` to detect typing and enable the submit button. Since the event never fires on mobile, the button stays disabled forever.

The `input` event fires reliably on both desktop physical keyboards and mobile virtual keyboards, and also handles paste, autocorrect, and dictation.

## Detection
```command
grep -rn "on('keypress'" admin/assets/js/ret_billing.js admin/assets/js/ret_purchase_order.js admin/assets/js/ret_purchase_approval.js admin/assets/js/ret_order.js admin/assets/js/ret_estimation.js
```

### Known Affected Selectors (cancel remark textareas)
- `#cancel_remark` — billing, purchase order, purchase approval
- `#payment_cancel_remark` — purchase order, purchase approval
- `#ret_cancel_remark` — purchase order, purchase approval
- `#metal_issue_cancel_remark` — purchase order, purchase approval
- `#conversion_cancel_remark` — purchase order
- `#ratefix_cancel_remark` — purchase order
- `#order_cancel_remark` — order
- `#od_cancel_remark` — estimation

## Files (active — exclude backup/dated files)
- `admin/assets/js/ret_billing.js`
- `admin/assets/js/ret_purchase_order.js`
- `admin/assets/js/ret_purchase_approval.js`
- `admin/assets/js/ret_order.js`
- `admin/assets/js/ret_estimation.js`

## Fix

### Before
```javascript
$('#cancel_remark').on('keypress',function(){
```

### After
```javascript
$('#cancel_remark').on('input',function(){
```

> Apply the same `keypress` → `input` replacement for ALL cancel remark selectors listed above.

## Verification
1. Open billing list on a **mobile device** (or Chrome DevTools mobile emulation)
2. Click cancel button on a bill → modal opens
3. Type more than 6 characters in the Remarks textarea
4. Confirm the Cancel button in the modal footer becomes **enabled** (clickable, red)
5. Close the modal without actually cancelling (don't modify live data)
6. Repeat on **desktop** to confirm no regression

## Notes
- The `keypress` event is officially deprecated per W3C spec. The `input` event is the correct modern replacement.
- `input` also catches paste, drag-and-drop text, autocorrect, and voice dictation — all of which `keypress` misses.
- This is a **cross-module** pattern. The same bug exists in ~36 instances across 5+ active JS files. Only `ret_billing.js` was fixed in this pass. The remaining files should be fixed via `/auto-fix` workflow.
- Backup/dated JS files (e.g., `ret_billing_12_08_2025.js`) are NOT active and should NOT be patched.
