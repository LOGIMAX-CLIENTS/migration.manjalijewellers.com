# Playbook #2: Action Fails Silently

> **Symptom**: "I clicked Save but nothing happened", "Button doesn't work"

## ⚡ Binary Search Integration
- **Start Layer**: 2 (JS Console) — check for red errors in F12 Console FIRST
- **Elimination**: Console has error → bug is in JS (validation, scope, undefined). No console error → check Network tab: was AJAX sent? If yes, check response. If no AJAX → JS event handler isn't firing.
- **Smell test**: "Button does nothing" + no console error → 35% JS validation blocks silently. Check `return false` and `e.preventDefault()` in the handler.

## Collect (Ask Human)
1. Which button/action?
2. Does the page reload or stay the same?
3. Any browser console errors? (F12 → Console)
4. What does the Network tab show for the AJAX call? (F12 → Network → click the request → Status + Response)

## Trace (AI Does)

### Step 1: Identify the endpoint
```
View file → find the button's JS handler
→ find the AJAX URL it calls
→ map to controller method
```

### Step 2: Check controller method
- Does it require POST data that might be missing?
- Is there a validation that fails silently (returns without message)?
- Is a redirect happening before the action completes?
- Is `$this->input->post()` returning empty? (form field name mismatch)

### Step 3: Check JS side
- Is the form actually submitting? (check for `e.preventDefault()` without subsequent submit)
- Is a JS validation blocking? (check `return false` conditions)
- Is a confirm dialog being swallowed? (check for `confirm()` calls)

### Step 4: Check error log
Ask human: `tail -20 admin/application/logs/log-{today's date}.php`

## Common Root Causes
1. **35%**: JS validation blocks submission (field name mismatch or required field empty)
2. **25%**: AJAX returns error but no UI feedback (missing error handler in JS)
3. **20%**: PHP error log has the real error (undefined variable, DB error)
4. **10%**: URL routing wrong (controller/method combo doesn't exist)
5. **10%**: Session expired (redirect to login swallowed by AJAX)

## Output
→ Whether the bug is frontend (JS) or backend (PHP)
→ Exact file and line where the silence occurs
