# Round 4: JS Save Handler & View Layer
**Target**: `ret_branch_transfer.js` & `views/branch_transfer/*`

## Bugs Found

### BRT-R401: Unescaped Flashdata Output (XSS Risk)
**Track**: A
**Severity**: P1
**Location**: `branch_transfer/list.php`, `form.php`, `approval_list.php`
**Description**: The views echo `$message['message']` retrieved directly from session flash data without using `htmlspecialchars()` or an equivalent encoding function. While flash data is often internally generated, echoing it raw leaves the application vulnerable if user input (like a branch name or tag ID) is concatenated into a failure alert.
**Fix**: Wrap the output: `<?= htmlspecialchars($message['message'], ENT_QUOTES, 'UTF-8') ?>` or ensure all flash strings are strictly sanitized before being passed to `$this->session->set_flashdata()`.

## Observations
- Client-side validation relies predominantly on basic required field constraints and synchronous function checks during the submission event. No single-flag overwrite antipattern (`PAT-VAL-002`) was found.
- No duplicate hardcoded HTML IDs detected across the dynamically generated blocks.
- GET-based delete links were not found (relies on POST/AJAX for status modulations).

## Conclusion
The views contain standard CodeIgniter 3 unescaped echo calls for system alerts (`PAT-SEC-004`). The JS save handlers are straightforward but heavily coupled to DOM states.
