# Unit Test — EST-RATE01: Tag Loaded Rate Not Populating

## Test ID: UT-EST-RATE01
## Date: 2026-04-18
## Module: Estimation (Tag Load)
## File: `admin/assets/js/ret_estimation.js`
## Functions: `append_tag_details()`, `calculatetag_SaleValue()`

---

## Test Cases

### TC-01: Tag Load Rate Population — Ornament Product (stone_type=0)

| Step | Action | Expected Result | Status |
|------|--------|----------------|--------|
| 1 | Open Add Estimation screen | Page loads with branch select | ✅ PASS |
| 2 | Select a branch | Metal rates populate in DOM (`.goldrate_22ct`, etc.) | ✅ PASS |
| 3 | Scan/load an ornament tag with valid Product/Design/SubDesign/Purity | Tag row appears in table | ✅ PASS |
| 4 | Observe Rate field in the tag row | Rate field shows non-zero value (matches configured metal rate) | ✅ PASS |
| 5 | Add same item via Home Bill section with same Purity | Rate field populates correctly | ✅ PASS |
| 6 | Compare Tag Rate vs Home Bill Rate | Both rates are identical | ✅ PASS |

### TC-02: Null rate_field Guard

| Step | Action | Expected Result | Status |
|------|--------|----------------|--------|
| 1 | Simulate tag with `rate_field = null` (no entry in `ret_metal_purity_rate`) | Code should NOT enter `$('.null').html()` branch | ✅ PASS |
| 2 | Verify `rate_field != null` check | Prevents null DOM selector | ✅ PASS |
| 3 | Verify `rate_field != 'null'` check | Prevents string "null" DOM selector | ✅ PASS |

### TC-03: purity_rate Fallback

| Step | Action | Expected Result | Status |
|------|--------|----------------|--------|
| 1 | Load tag where primary `rate_field` DOM returns empty | Fallback to `purity_rate` array lookup | ✅ PASS |
| 2 | Verify `purity_rate` array is available on page | `typeof purity_rate !== 'undefined'` check passes | ✅ PASS |
| 3 | Verify fallback matches `val.purity` and `val.id_metal` | Correct rate_field retrieved from purity_rate | ✅ PASS |
| 4 | Verify fallback rate populates the Rate input | Non-zero rate is set in `market_rate_value` input | ✅ PASS |

### TC-04: calculatetag_SaleValue rate_field_value Guard

| Step | Action | Expected Result | Status |
|------|--------|----------------|--------|
| 1 | After tag load, trigger sale value recalculation | `calculatetag_SaleValue()` runs correctly | ✅ PASS |
| 2 | Verify `rate_field` null check in `rate_field_value` | No JS error for null rate_field | ✅ PASS |
| 3 | Verify `board_rate` uses populated rate | Wastage and MC calculations use correct rate | ✅ PASS |

### TC-05: Edge Cases

| Step | Action | Expected Result | Status |
|------|--------|----------------|--------|
| 1 | Load tag with `stone_type != 0` (loose/diamond) | Goes to `else` branch, uses `loose_product_rate` | ✅ PASS |
| 2 | Load tag with `ord_rate_type = 1` and valid `order_rate_per_grm` | Uses order rate, skips DOM lookup | ✅ PASS |
| 3 | Load tag with Manual Rate enabled and stored `manualRates` | Uses manual rate, skips DOM lookup | ✅ PASS |
| 4 | Load tag on Edit Estimation page | Rate loads correctly from saved data | ✅ PASS |

### TC-06: Regression — Home Bill Not Affected

| Step | Action | Expected Result | Status |
|------|--------|----------------|--------|
| 1 | Add Home Bill item (select Product, Design, Purity) | Rate populates via `get_search_custom_metal_rates()` | ✅ PASS |
| 2 | Change purity in Home Bill row | Rate updates via AJAX `get_metal_purity_rate` | ✅ PASS |
| 3 | Verify Home Bill calculations unchanged | Cost/tax/total match expected values | ✅ PASS |

---

## Code Changes Verified

### `append_tag_details()` — Line ~28408
```diff
- else if (rate_field != '' && val.stone_type==0)
+ else if (rate_field != null && rate_field != '' && rate_field != 'null' && val.stone_type==0)
```
- Added null guard ✅
- Added `isNaN()` check for DOM value ✅
- Added `purity_rate` array fallback ✅

### `calculatetag_SaleValue()` — Line ~11667
```diff
- var rate_field_value = (isNaN($('.'+rate_field).html()) ||$('.'+rate_field).html() == '')  ? 0 : ...
+ var rate_field_value = (rate_field != null && rate_field != '' && rate_field != 'null' && !isNaN(...)) ? ... : 0;
```
- Added null guard ✅

---

## Result: ALL TESTS PASSED ✅
## Tester: Antigravity
## Date: 2026-04-18
