---
id: recipe_city_dropdown_block_comment_swap
title: "City Dropdown Shows Wrong State Cities (Block-Comment + Swapped Fields)"
module: Estimation
bug_id: KLAS-067
pattern_id: PAT-JS-006
severity: P1
track: A (System)
date_created: 2026-04-24
---

## Symptom

In the estimation creation screen, when editing an existing customer's details, the city dropdown always shows the same set of cities (Tamil Nadu cities) regardless of which state is actually selected. Changing the state in the edit-customer modal has no effect on the city list.

## Root Cause

Two bugs in `admin/assets/js/ret_estimation.js` combined to produce this behavior:

1. **Block-comment disables all city/state/country cascade functions**:
   A `/* ... */` block comment wraps the entire `get_country()`, `get_state()`, `get_city()` function definitions AND the `$('#state,#ed_cus_state').on('change', ...)` event handler. This means:
   - `get_city()` function does not exist at runtime
   - When user changes state, the `.on('change')` event does NOT fire `get_city()`
   - The city dropdown keeps showing whatever was loaded on page init (default Tamil Nadu cities)

2. **State/city IDs are swapped in `get_customer()` AJAX callback**:
   When loading a customer's data for editing, the assignments are inverted:
   ```js
   $('#ed_cus_state').val(data.id_city);  // ← Wrong: state gets city ID
   $('#ed_cus_city').val(data.id_state);  // ← Wrong: city gets state ID
   ```

## Detection Commands

```powershell
# Detect block-comment wrapping cascade functions
Select-String -Path "admin/assets/js/ret_estimation.js" -Pattern "/\* function get_country"

# Detect swapped state/city assignment
Select-String -Path "admin/assets/js/ret_estimation.js" -Pattern "ed_cus_state.*id_city|ed_cus_city.*id_state"
```

## Fix

### Fix 1 — Remove Block Comment (3 lines)

```diff
- /* function get_country() {
+ function get_country() {
```
```diff
- } */
+ }
```
(The closing `} */` is on line ~17051 — end of `get_city()` function)

### Fix 2 — Correct Swapped Field Assignments

In `get_customer()` success callback (~line 16721):
```diff
- $('#ed_cus_state').val(data.id_city);
- $('#ed_cus_city').val(data.id_state);
+ $('#ed_cus_state').val(data.id_state);
+ $('#ed_cus_city').val(data.id_city);
```

## Affected File

- `admin/assets/js/ret_estimation.js`
  - Lines ~16791 (remove `/*`)
  - Lines ~17051 (remove `*/`)
  - Lines ~16721–16723 (swap fix)

## Verification Steps

1. Navigate to: `http://localhost/erp.lakshmanaacharison.in/admin/index.php/admin_ret_estimation/estimation/add`
2. Select an existing customer (one with a known state other than Tamil Nadu — e.g., Karnataka)
3. Click "Edit Customer"
4. Verify the State field shows the correct state (Karnataka)
5. Verify the City dropdown populates with Karnataka cities, NOT Tamil Nadu cities
6. Change the State to a different state (e.g., Kerala)
7. Verify the City dropdown reloads with Kerala cities
8. Save the customer — verify state + city are saved correctly

## Ripple Check

- [x] `#ed_cus_state` change handler fires `get_city()` → Restored ✅
- [x] `get_city()` sends correct `id_state` in POST to `getcity` endpoint → Correct ✅
- [x] `#ed_id_city` hidden field stores the previously selected city for auto-selection after reload → Unaffected ✅
- [x] `update_cutomer()` sends `$('#ed_cus_city').val()` as `id_city` → Now correct ✅
- [x] `#add_new_customer` flow uses `get_country()` → Restored by fix 1 ✅

## Notes

- This is a **double bug** — block comment + field swap. Both must be fixed together.
- The `get_country()` function is called from both initialization (line 361) and `get_customer()` (line 16775). The only definition was inside the block comment → JS error at runtime (now fixed).
- `get_state()` function was also inside the block comment — now restored.
