# Playbook: CodeIgniter 2/3 Framework Debugging

> Deep debugging procedures for CI2/CI3 framework-specific issues.
> Reference: `debugger.md` § CI Framework Debug Toolkit for quick commands.

---

## Routing Issues

**Symptom**: "Page not found", wrong controller/method called, 404 on existing page.

### Binary Search
```
Step 1: Is the URL correct?
        → Check: does the controller file + method exist?
        → File: admin/application/controllers/{controller}.php
        → Method: public function {method}()

Step 2: CHECK routes.php
        → admin/application/config/routes.php
        → Is there a route override redirecting this URL?
        → Common trap: $route['(:any)'] at the bottom catches everything

Step 3: CHECK _remap()
        → Does the controller have _remap()? It overrides ALL method routing.
        → grep -rn "_remap" admin/application/controllers/ --include="*.php"

Step 4: CHECK underscores vs dashes
        → CI2/CI3: URL "admin-billing" maps to controller "admin_billing"
        → config.php: $config['permitted_uri_chars'] — is the char allowed?
```

### Common Fixes
| Problem | Cause | Fix |
|---------|-------|-----|
| 404 on valid URL | `routes.php` wildcard catches it | Move specific route ABOVE `(:any)` |
| Wrong method called | `_remap()` exists | Check `_remap()` switch/if logic |
| Works locally, 404 on server | Filename case mismatch | Linux is case-sensitive: `Admin_ret_billing.php` ≠ `admin_ret_billing.php` |

---

## Database Debugging

**The single most useful CI debugging technique.**

### See the Actual SQL
```php
// After any Active Record query — see what CI actually generated:
$result = $this->db->get('ret_billing');
echo $this->db->last_query(); die;
// OR log it:
log_message('debug', 'SQL: ' . $this->db->last_query());
```

### Get the DB Error
```php
// After a failed query:
$error = $this->db->error();
log_message('error', 'DB Error: ' . json_encode($error));
// Returns: ['code' => 1054, 'message' => "Unknown column 'xxx'"]
```

### Enable Full Query Profiling
```php
// In the controller method (shows ALL queries, memory, benchmarks):
$this->output->enable_profiler(TRUE);
// → Page footer shows every query with execution time
// ⚠️ NEVER leave this on in production
```

### Active Record Chain Debugging
```php
// When chaining produces wrong SQL, build step by step:
$this->db->select('id_billing, tot_bill_amount');
echo "After select: " . $this->db->get_compiled_select('ret_billing') . "\n";
// → Shows the SQL at this point WITHOUT executing

$this->db->where('bill_status', 1);
echo "After where: " . $this->db->get_compiled_select('ret_billing') . "\n";
// → Shows SQL with WHERE added

// Find which chain step produces wrong SQL
```

### Transaction Debugging
```php
// Suspect transaction not committing?
$this->db->trans_begin();
// ... operations ...
$status = $this->db->trans_status();
log_message('debug', 'Trans status: ' . ($status ? 'OK' : 'FAILED'));
if ($status) {
    $this->db->trans_commit();
} else {
    $this->db->trans_rollback();
    // Log WHICH operation failed:
    log_message('error', 'Trans failed. Last query: ' . $this->db->last_query());
    log_message('error', 'DB error: ' . json_encode($this->db->error()));
}
```

---

## Session/Auth Debugging

**Symptom**: "Logged out randomly", "Permission denied", "Can't access page"

```php
// Dump current session:
log_message('debug', 'Session: ' . json_encode($this->session->userdata()));

// Key session fields to check:
// - id_user: who is logged in
// - id_profile: which permission profile
// - id_branch: which branch context
// - is_logged_in: should be TRUE
```

### Common Session Issues
| Problem | Cause | Check |
|---------|-------|-------|
| Random logout | Session table full or GC too aggressive | `SELECT COUNT(*) FROM ci_sessions` |
| Wrong branch data | Session branch ≠ selected branch | Compare `$this->session->id_branch` vs POST |
| "Access denied" | Profile doesn't have menu permission | `SELECT * FROM ret_menu_access WHERE id_profile = {X}` |
| Session lost on AJAX | Cookie domain mismatch | Check `$config['cookie_domain']` in config.php |

---

## Form Validation Debugging

**Symptom**: "Form submits but nothing happens", "Validation error but no message"

```php
// After $this->form_validation->run():
if ($this->form_validation->run() == FALSE) {
    log_message('debug', 'Validation errors: ' . validation_errors());
    log_message('debug', 'POST data: ' . json_encode($this->input->post()));
}

// Common trap: field name in HTML ≠ field name in set_rules()
// HTML: <input name="bill_amount">
// PHP:  $this->form_validation->set_rules('tot_amount', ...)  ← WRONG NAME
```

---

## AJAX / JSON Response Debugging

**Symptom**: "JS gets wrong data", "SyntaxError: Unexpected token < in JSON"

### The `<` in JSON Error
This means PHP returned HTML (error page) instead of JSON:
```php
// BAD — PHP error output mixed into JSON:
echo json_encode($data);  // If any PHP error/notice before this → invalid JSON

// GOOD — proper JSON response:
$this->output
    ->set_content_type('application/json')
    ->set_output(json_encode($data));
return;  // Don't forget return!
```

### AJAX Response Debugging Checklist
```
1. F12 → Network → click the AJAX request
2. Check Status Code:
   - 200 but wrong data → PHP logic error
   - 302 → redirect (session expired, login redirect)
   - 403 → CSRF token expired or permission denied
   - 404 → wrong URL / controller method missing
   - 500 → PHP fatal error (check error log)
3. Check Response body:
   - Starts with "{" or "[" → valid JSON, check contents
   - Starts with "<" → PHP error/HTML dumped, check PHP log
   - Empty → controller didn't echo/output anything
```

---

## Autoload Issues

**Symptom**: "Call to undefined method", "Class not found"

```
Check: admin/application/config/autoload.php

$autoload['model'] = array(...)    ← Is the model listed?
$autoload['libraries'] = array(...) ← Is the library listed?
$autoload['helper'] = array(...)    ← Is the helper listed?

If not autoloaded, check if manually loaded in controller:
$this->load->model('billing_model');
$this->load->library('session');

Common trap: model file exists but class name inside doesn't match filename
→ Filename: Billing_model.php
→ Class: class Billing_model extends CI_Model {}
→ Case must match EXACTLY on Linux servers
```

---

## Hook Interference

**Symptom**: "Works in one controller but not another", "Something runs before my code"

```
Check: admin/application/config/hooks.php

Common hook types that cause issues:
- pre_controller → runs before ANY controller
- post_controller_constructor → runs after __construct()
- post_controller → runs after method completes

Diagnosis:
1. Is there a hook that redirects? (auth check, maintenance mode)
2. Is there a hook that modifies $this->input or session?
3. Temporarily disable hooks:
   config.php → $config['enable_hooks'] = FALSE;
   → Does the bug disappear? → Hook is the cause
```
