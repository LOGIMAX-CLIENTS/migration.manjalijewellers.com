# Round 5: JS Calculations & Data Binding
**Target**: `ret_branch_transfer.js`

## Bugs Found

### BRT-R501: Missing parseFloat and NaN Guards
**Track**: B
**Severity**: P2
**Location**: `ret_branch_transfer.js` (multiple calculating contexts)
**Description**: The JS file completely lacks explicit `parseFloat()` conversions or `isNaN()` guards when manipulating or aggregating weights/pieces before submission. It relies entirely on implicit JS coercion or server-side parsing. If a user manages to input an empty string or non-numeric character where a weight is expected, `NaN` or string concatenation could occur silently before posting.
**Fix**: Wrap ALL numeric form value retrievals with `parseFloat(val) || 0`.

---

# Round 6: Validation Functions & AJAX Endpoints
**Target**: `ret_branch_transfer.js` & `admin_ret_brntransfer.php`

## Bugs Found

### BRT-R601: Meaningless Table Row Validation Loop
**Track**: B
**Severity**: P2
**Location**: `ret_branch_transfer.js` L731-L805 (`submit_approval` handlers)
**Description**: The validation checking if a branch transfer can be submitted iterates over the table rows via `$('... tbody tr').each()`. Inside the loop, it blindly sets `allow_submit=true_` and `return true;` without validating any internal row data (like piece counts > 0 or weights > 0). It acts as a glorified `.length > 0` check but provides no real column-level integrity validation, relying entirely on the server.
**Fix**: Implement quantitative field checks inside the `.each()` loops (e.g., `if (parseFloat($(this).find('.weight').val()) <= 0) { allow_submit = false; }`).

### BRT-R602: AJAX Success Does Not Handle JSON Parse Errors
**Track**: A
**Severity**: P3
**Location**: `ret_branch_transfer.js` L4083
**Description**: The AJAX `success` callback assumes that the server response will always be valid JSON. If the server throws a PHP Notice or Error (common due to `BRT-R302` or `BRT-102`), the JSON breaks, the success handler fails silently, and the `overlay` remains stuck forever.
**Fix**: Add an `.error(function(){ ... })` handler to all POST AJAX calls.
