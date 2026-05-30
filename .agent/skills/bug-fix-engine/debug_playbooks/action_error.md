# Playbook #3: Action Gives Error

> **Symptom**: "Error: something went wrong", PHP error, white screen

## ⚡ Binary Search Integration
- **Start Layer**: 5 (Error Log) — check PHP error log FIRST, not code
- **Elimination**: Error log has the exact error → parse it (see table below). No error in CI log + white screen → check Apache log (PHP may not have initialized). Error is JS → check Layer 2 (Console).
- **Smell test**: "White screen" → 95% PHP parse error. Check Apache log. "Unexpected token < in JSON" → 95% PHP error dumped into AJAX response.

## Collect (Ask Human)
1. Exact error message text (copy-paste, not paraphrase)
2. Full URL when error appeared
3. PHP error log: `tail -30 admin/application/logs/log-{date}.php`
4. Apache error log: `tail -10 /var/log/apache2/error.log` (if white screen)

## Parse Error (AI Does)

### PHP Error Patterns

| Error Text | Meaning | Fix Direction |
|---|---|---|
| `Undefined variable: $data` | Variable not initialized in current scope | Check if assigned inside if/else that didn't execute |
| `Trying to access array offset on null` | `->row_array()` or `->result_array()` returned NULL | Query returned 0 rows — add NULL check |
| `Cannot modify header information` | `echo`/`print` before `redirect()` — output started | Find stray output (debug print left in code) |
| `Maximum execution time exceeded` | N+1 query loop or unindexed table | Check for query inside foreach loop |
| `Allowed memory size exhausted` | Unbounded result set (no LIMIT/WHERE) | Add pagination or narrower WHERE |
| `Call to undefined method` | Method renamed/removed or model not loaded | Check `$this->load->model()` and method spelling |
| `A Database Error Occurred` | SQL syntax error or constraint violation | Read the SQL in the error — usually bad column name |
| `Class not found` | Controller/library file missing or misnamed | Check filename case (Linux = case sensitive) |
| `CSRF token mismatch` | Form submitted after session expired or tab duplicated | Check `csrf_protection` in config.php |

### White Screen (500 error)
1. Check Apache error log (not CI log — CI may not have initialized)
2. Common: `parse error` (syntax), `require_once failed` (missing file)
3. Run: `php -l {suspected file}` for syntax check

## Output
→ Root cause from error message parsing
→ Exact file + line from error trace
→ Hand off to Developer role
