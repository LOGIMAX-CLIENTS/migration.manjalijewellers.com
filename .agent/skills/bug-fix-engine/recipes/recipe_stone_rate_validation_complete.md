# Recipe: Stone Rate Validation — Complete Fix (Quality, Closure, Chrome, Amount)

## Metadata
- **Pattern ID**: PAT-JS-014
- **Severity**: HIGH
- **Modules Affected**: Ret_Billing (stone modal), ret_billing.js
- **Auto-fixable**: Partial

## Client Scope
- **Applies to**: ALL (any client with stone rate settings and quality codes)
- **Reason**: All clients share the same billing JS and stone modal logic

## Created By
- **Developer**: Antigravity AI
- **Client**: erp.sriammanjewellers.in (amman)
- **Date**: 2026-04-16
- **Source Bug ID**: N/A

## Symptom
1. Stone quality (VVS, VVS2 etc.) not auto-selecting in billing stone modal for existing rows
2. Rate validation fires (console confirms) but `rate_per_gram` field still shows "0" — not the clamped `max_rate`
3. Amount field shows `price ÷ wt` written back into itself (e.g. 100000 becomes 83333.33)
4. Entering an amount in stone_price triggers check_min_max which clamps rate=0 to max_rate and overwrites user's typed amount

## Root Cause

### 1. Quality not auto-selected for legacy data
Old billing stones have no `quality_id` stored — the quality dropdown stays on the default blank option because there's nothing to pre-select from `stn_data`.

### 2. Stale closure in `$.each` loop
`stone_rate` was captured **once** before the `$.each(stone_rate_settings)` loop. If iteration 1 clamped the rate to `max_rate`, iteration 2 still held the old stale value (e.g. `"0"`) and wrote it back, resetting the corrected rate.

### 3. Chrome `type=number` input does not visually repaint on programmatic set
`element.value = x` and `$(el).val(x)` both set the JS DOM property, but Chrome's `<input type="number">` does not always refresh its visual display. The internal value is correct but the field still renders the old typed value.

### 4. `calculate_stone_amount` `stonePrice_focus` logic writes derived rate back into `stone_price`
When `stone_price` was focused, the function computed `rate = price / weight` then wrote it back into `stone_price` (circular). Should write derived rate to `rate_per_gram` instead.

### 5. `stone_price` included in the `check_min_max` change trigger
`$(document).on("change", ".rate_per_gram,.stone_price,...")` caused check_min_max to fire whenever the user typed an amount. Since `rate_per_gram` was 0 at that moment, it clamped to `max_rate` and overwrote the user's amount entry.

## Detection
```powershell
# Check 1: stone_rate captured outside $.each (stale closure)
Select-String -Path "admin/assets/js/ret_billing.js" -Pattern "var stone_rate.*=.*rate_per_gram" | Where-Object { $_.LineNumber -lt 43150 }

# Check 2: stone_price included in check_min_max trigger
Select-String -Path "admin/assets/js/ret_billing.js" -Pattern "on.*change.*stone_price.*rate_per_gram|on.*change.*rate_per_gram.*stone_price"

# Check 3: stonePrice_focus writes back to stone_price
Select-String -Path "admin/assets/js/ret_billing.js" -Pattern "stone_price.*val\(st_rate\)"
```

## Files
- `admin/assets/js/ret_billing.js`

## Fix

### Fix 1: Auto-derive quality from `stone_rate_settings` for legacy stones

In `create_new_empty_stone_item()`, after resolving `qid` from `stn_data`:

#### Before
```javascript
var qid = (raw_qid !== null && ...) ? String(raw_qid) : "";
if (qid !== "") {
    $("#estimation_stone_item_details tbody tr:last .quality_id").val(qid);
}
```

#### After
```javascript
var qid = (raw_qid !== null && ...) ? String(raw_qid) : "";

// If quality not stored, auto-derive from stone_rate_settings
if (qid === "" && stone_id > 0) {
    var branch_id = $("#id_branch").val() || "";
    var row_uom_id = stn_data ? (stn_data.uom_id || stn_data.stone_uom_id || "") : "";
    $.each(stone_rate_settings, function (k, setting) {
        if (
            setting.id_branch == branch_id &&
            setting.stone_type == stone_type &&
            setting.stone_id == stone_id &&
            (setting.uom_id == "" || setting.uom_id == row_uom_id)
        ) {
            if (setting.quality_id && setting.quality_id !== "" && setting.quality_id !== "0") {
                qid = String(setting.quality_id);
                return false; // break
            }
        }
    });
}
if (qid !== "") {
    $("#estimation_stone_item_details tbody tr:last .quality_id").val(qid);
}
```

---

### Fix 2: Read `stone_rate` INSIDE `$.each` loop (stale closure fix) + break after first match

#### Before
```javascript
function check_min_max_stone_rate(curRow) {
    var stone_rate = curRow.find(".rate_per_gram ").val(); // captured ONCE — stale

    $.each(stone_rate_settings, function (key, items) {
        // ... iteration 1 sets rate to 100000
        // ... iteration 2: stone_rate is still "0" → writes "0" back → rate reset
        curRow.find(".rate_per_gram ").val(stone_rate);
    });
}
```

#### After
```javascript
function check_min_max_stone_rate(curRow) {
    $.each(stone_rate_settings, function (key, items) {
        var stone_rate = parseFloat(curRow.find(".rate_per_gram").val()) || 0; // read inside loop

        // ... matching conditions ...

        if (stone_rate >= min_rate && stone_rate <= max_rate) {
            // valid — keep as-is
        } else {
            // clamp (see Fix 3 for DOM update)
        }
        return false; // break after first match — prevents later iterations overwriting clamped value
    });
    calculate_stone_amount();
}
```

---

### Fix 3: Chrome `type=number` visual refresh (text→set→number trick)

Chrome does not always repaint `<input type="number">` when set programmatically. Force it:

#### Before
```javascript
curRow.find(".rate_per_gram").val(max_rate);        // jQuery .val() — doesn't always repaint
curRow.find("input.rate_per_gram")[0].value = max_rate; // direct DOM — same issue
```

#### After
```javascript
var $rateEl = curRow.find("input.rate_per_gram");
$rateEl[0].type = 'text';                              // 1. switch to text (removes number constraints)
$rateEl[0].value = parseFloat(max_rate).toFixed(2);   // 2. set value as string
$rateEl[0].type = 'number';                            // 3. switch back — Chrome renders the new value
```

---

### Fix 4: `stonePrice_focus` — write derived rate to `rate_per_gram`, not `stone_price`

#### Before
```javascript
if (stonePrice_focus == true) {
    st_rate = parseFloat(stonePrice) / parseFloat(stone_wt);
    curRow.find(".stone_price ").val(st_rate); // BUG: circular — overwrites amount with derived rate
}
```

#### After
```javascript
if (stonePrice_focus == true) {
    // Derive rate from typed amount, write to rate_per_gram — leave stone_price alone
    var $rateEl = curRow.find("input.rate_per_gram");
    $rateEl[0].type = 'text';
    $rateEl[0].value = parseFloat(st_rate).toFixed(2);
    $rateEl[0].type = 'number';
    // Do NOT write to stone_price — user is actively typing it
}
```

---

### Fix 5: Split change event — separate `stone_price` from `check_min_max` trigger

#### Before
```javascript
$(document).on("change", ".rate_per_gram,.stone_price,.stone_wt,.stone_uom_id,.quality_id", function () {
    var row = $(this).closest("tr");
    check_min_max_stone_rate(row); // BUG: stone_price change clamps rate=0 → overwrites user's amount
});
```

#### After
```javascript
// Rate/wt/uom/quality → validate rate bounds then recalculate amount
$(document).on("change", ".rate_per_gram,.stone_wt,.stone_uom_id,.quality_id", function () {
    var row = $(this).closest("tr");
    check_min_max_stone_rate(row); // internally calls calculate_stone_amount
});

// Amount → derive rate from amount, then validate derived rate
$(document).on("change", ".stone_price", function () {
    var row = $(this).closest("tr");
    calculate_stone_amount();        // writes rate_per_gram = amount / wt (stonePrice_focus=true)
    check_min_max_stone_rate(row);  // validates derived rate, clamps if needed
});
```

## Verification
1. Open billing → Add Stone modal for a bill with existing stones (no stored quality)
2. **Quality auto-select**: VVS/VVS2 should auto-select from `stone_rate_settings` without user action
3. **Rate out of range**: Enter `1` in rate → tab out → field shows `100000.00`, toast warning, amount updates
4. **Rate in range**: Enter `95000` → tab out → stays, amount = wt × 95000
5. **Amount entry (in range)**: Type `114000` in amount → tab out → rate = 114000÷1.2 = 95000 (in range → stays)
6. **Amount entry (out of range)**: Type `48000` in amount → tab out → rate = 40000 (below min) → clamps to 100000 → amount = 120000, toast shown
7. Multiple rows — each validates independently

## Notes
- `stone_rate` MUST be read inside `$.each`, never before. Outside capture = stale closure = second matching setting overwrites the corrected value.
- Chrome `type=number` inputs silently ignore `element.value = x` visually in some cases. The `type text → set → type number` trick is the only reliable cross-version fix.
- Bidirectional rate ↔ amount entry requires **two separate event handlers**. A single merged handler causes check_min_max to fire on amount change when rate is 0, incorrectly clamping.
- `getStoneRateSettings()` and `getActive_quality_code()` must use `async: false` so data is available before the stone modal rows are rendered.
- Related: `recipe_stone_rate_validation_event_handler.md` (initial event fix), `recipe_stone_type_data_flow_billing.md` (stone_type derivation for legacy data)
