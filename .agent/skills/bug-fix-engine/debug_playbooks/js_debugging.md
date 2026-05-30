# Playbook: JavaScript Debugging (Large Codebase)

> Debugging JS in the eTail codebase: 30K+ line single-file architecture.
> Start at **Layer 2** (JS console) when the binary search shows the bug is in frontend.

---

## Finding the Event Handler

**Problem**: You need to find which function runs when a button is clicked — in a 30K-line file.

### Method 1: Search by Element ID/Class
```
1. F12 → Elements → click the button → note its ID or class
   Example: <button id="btn_save_bill" class="btn-primary">

2. Search in the JS file for that ID:
   grep -n "btn_save_bill" admin/assets/js/ret_billing.js
   → Finds: $(document).on('click', '#btn_save_bill', function() { ... })

3. OR search by the AJAX URL the button calls:
   grep -n "save_bill" admin/assets/js/ret_billing.js
   → Finds the $.ajax({ url: ... }) call
```

### Method 2: Browser DevTools Event Listener
```
F12 → Elements → select the button → Event Listeners panel (right side)
→ Shows ALL event handlers attached to this element
→ Click the filename:line to jump to source
```

### Method 3: Break on Click
```
F12 → Sources → Event Listener Breakpoints → Mouse → click
→ Click the button → execution pauses at the handler
→ Call Stack panel shows the full chain
```

### Common Trap: Event Delegation
```javascript
// Handler is NOT on the button itself but on a parent:
$(document).on('click', '#btn_save_bill', function() { ... });
// → The handler is on `document`, delegated to `#btn_save_bill`
// → Event Listeners panel on the button won't show it
// → Search the JS file for the selector instead
```

---

## Add vs Edit Mode Debugging

**Symptom**: "Works when adding, breaks when editing" (or vice versa)

```
The same JS file handles BOTH Add and Edit. Key variables:
- mode / action / is_edit — controls which branch executes
- Hidden field: <input type="hidden" name="id_billing" value="">
  → Empty = Add mode, has value = Edit mode

Debugging:
1. Find the branching:
   grep -n "mode\|is_edit\|action.*edit" admin/assets/js/{file}.js

2. Common bugs:
   - Edit path reads a field that only exists in Add form
   - Edit path POSTs to wrong URL (add URL instead of update URL)
   - Hidden ID field is empty on Edit (form not populated)
   - Variable initialized inside Add-only block but used in shared code
```

---

## DataTable Debugging

**Symptom**: "Table shows wrong data", "Column missing", "Footer total wrong"

### Column Mismatch
```
DataTable error: "Requested unknown parameter 'X' for row Y"

→ The `columns` array in JS doesn't match the JSON keys from PHP:

JS:   columns: [ { data: 'bill_amount' }, { data: 'customer_name' } ]
PHP:  $row[] = $item['tot_bill_amount'];  // ← different key name!

Fix: Match JS column names to PHP JSON keys exactly.
```

### Footer Callback (PAT-DT-001)
```javascript
// The #1 DataTable bug — wrong column index in footerCallback:
footerCallback: function(row, data, start, end, display) {
    var api = this.api();
    // Column index is 0-based! If you added a column, ALL indexes shift.
    var total = api.column(5).data().reduce(...)  // ← Is 5 correct?
    $(api.column(5).footer()).html(total);
}

// Debug: log which column the API sees:
console.log('Column 5 header:', api.column(5).header().textContent);
console.log('Column 5 data:', api.column(5).data().toArray());
```

### Pagination Issues
```
Problem: calculations only apply to current page, not all data
→ Use api.column(X, {page: 'all'}).data() for full dataset
→ vs api.column(X).data() which is current page only
```

---

## AJAX Debugging

### Request Not Sending
```
F12 → Console: any red errors BEFORE the AJAX call?
→ JS error in validation code prevents reaching $.ajax()

F12 → Network: is the request even in the list?
→ No → JS error before the AJAX call
→ Yes → Check status + response
```

### Response Debugging
```
F12 → Network → click request → tabs:

Headers tab:
  - Request URL → correct?
  - Request Method → POST?
  - Content-Type → application/x-www-form-urlencoded?

Payload tab:
  - Form data sent → all fields present?
  - Compare field names with PHP $this->input->post() expectations

Response tab:
  - Valid JSON → check values
  - Starts with < → PHP error dumped (check PHP log)
  - Empty → controller didn't output anything
  
Preview tab:
  - Rendered HTML of error (if response is HTML)
```

### Error Handler Missing
```javascript
// BAD — no error handler:
$.ajax({
    url: '/admin/billing/save',
    success: function(data) { /* only handles success */ }
});

// GOOD — always handle errors:
$.ajax({
    url: '/admin/billing/save',
    success: function(data) { ... },
    error: function(xhr, status, error) {
        console.log('AJAX error:', status, error);
        console.log('Response:', xhr.responseText);
    }
});
```

---

## Variable Scope Bugs

### Global Pollution (Single-File Architecture)
```javascript
// In 30K-line files, variables declared without var/let/const are GLOBAL:
function save_bill() {
    total = 0;  // ← GLOBAL! Another function can overwrite this
}
function save_purchase() {
    total = 0;  // ← Overwrites the same global!
}

// Debug: search for the variable name across the entire file
// If it's used in multiple unrelated functions → scope bug
```

### Closure Leaks in Loops
```javascript
// BAD — classic loop closure bug:
for (var i = 0; i < items.length; i++) {
    setTimeout(function() {
        console.log(items[i]);  // ← Always logs the LAST item
    }, 100);
}

// The 'var' is function-scoped, not block-scoped
// By the time the timeout fires, i = items.length
```

---

## Race Conditions

**Symptom**: "Sometimes works, sometimes doesn't", "Data appears then disappears"

```
1. Multiple AJAX calls firing simultaneously:
   → Response B arrives before Response A
   → Response A's callback overwrites Response B's data

2. Debug: F12 → Network → check timing of XHR requests
   → Are two requests to the same URL overlapping?

3. Common fix:
   - Disable button after click until AJAX completes
   - Use a flag: if (isSubmitting) return; isSubmitting = true;
   - Use $.ajax({ async: false }) ONLY as last resort (blocks UI)
```

---

## Browser DevTools Workflow

### Console (Layer 2 — Check First)
```
1. Open F12 BEFORE reproducing the bug
2. Clear console (Ctrl+L)
3. Reproduce the bug
4. Red errors = JS errors. Read them:
   - "Cannot read property 'X' of undefined" → variable is null/undefined
   - "X is not a function" → wrong type, or function not loaded
   - "X is not defined" → variable/function doesn't exist in scope
```

### Network (Layer 3 — Binary Search Bisect Point)
```
1. Filter by XHR (AJAX only)
2. Reproduce the bug
3. Click the request:
   - Status 200 + correct data → bug is in JS rendering
   - Status 200 + wrong data → bug is in PHP backend
   - Status != 200 → server error (check PHP log)
```

### Sources (Deep Debugging)
```
1. Ctrl+P → type filename → open the JS file
2. Ctrl+G → jump to line number
3. Click line number → set breakpoint
4. Reproduce bug → execution pauses
5. Hover over variables to inspect values
6. Step through: F10 (over), F11 (into), Shift+F11 (out)
7. Watch panel → add expressions to monitor
```
