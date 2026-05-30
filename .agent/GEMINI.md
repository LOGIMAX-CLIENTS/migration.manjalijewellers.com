## ⛔ STEP ZERO — BEFORE ANY TASK (NON-NEGOTIABLE)

> **This block overrides everything below. No exceptions. No shortcuts.**

Before writing ANY code, running ANY grep/search, or reading ANY source file, you MUST:

### 1. Read the System Brain (`knowledge_brain/_SYSTEM/`)

Load the relevant documents based on the task category:

| Task Type | Required System Brain Documents |
|---|---|
| **Any bug diagnosis** | `DIAGNOSTIC_PLAYBOOK.md` + `DANGER_ZONES.md` |
| **Validation / input bug** | `VALIDATION_GAPS.md` + `DANGER_ZONES.md` |
| **Cross-module bug or change** | `SHARED_TABLES.md` + `MODULE_DEPENDENCIES.md` + `CROSS_MODULE_BUGS.md` |
| **Data flow / report bug** | `DATA_FLOW_CHAINS.md` |
| **Business flow / process bug** | `FLOW_CHECKLISTS/{relevant_flow}.md` |
| **Status code / tag value issue** | `TAG_STATUS_MAP.md` + `HARDCODED_VALUES.md` |
| **Performance issue** | `PERFORMANCE_RISKS.md` |
| **Refactoring / cleanup** | `CLEANUP_GAPS.md` |
| **System audit or review** | `REVIEW_DOCUMENT.md` + `SYSTEM_COVERAGE.md` |

### 2. Read the Module Brain (`knowledge_brain/{module}/`)

If the task is scoped to a specific module, read its `MODULE_BRAIN.md` before touching any source code.

### 3. For Bug Fixes — Activate the Bug Fix Engine Skill

If the task involves fixing a bug (any bug, regardless of how it was reported):

1. **Read** `.agent/skills/bug-fix-engine/SKILL.md` — activates the 4 roles (Debugger → Architect → Developer → Tester) and routes to the correct debug playbook
2. **Search recipes** — search the central `LOGIMAX-CLIENTS/bug-recipes` repo via GitHub MCP (or git CLI fallback to sibling `bug-recipes/` directory) for existing fixes matching the symptom/module/table
3. **Search patterns** — check `bug_report_AI/COMMON_BUG_PATTERNS.md` for known fix templates
4. **If recipe/pattern found** → use it as the fix starting point, do NOT re-investigate from scratch
5. **If no match** → proceed to manual investigation using the debug playbook from SKILL.md

> ⚠️ **Skipping recipe search is a rule violation** — even if you believe the bug is novel, recipes must be checked first. This takes 30 seconds and prevents hours of redundant work.

### 4. THEN proceed with code investigation

Only after Steps 1-3 are complete may you run grep, read source files, or write code.

**Violation of this order is a rule failure** — even if the fix turns out correct, the process was wrong.

---

# Project Rules — retail_v5 Bug Remediation System

> These rules apply to every conversation in this workspace. They are non-negotiable.

---

## 1. Framework & Coding Standards (CodeIgniter 3)

- This project uses **CodeIgniter 3**. Follow CI3 conventions strictly — `$this->db->` query builder (active record), `$this->load->model()`, `$this->input->post()`, etc.
- **Never use raw SQL** (`$this->db->query("SELECT ...")`) unless the query is genuinely impossible with active record. If raw SQL is necessary, explain why.
- **Never use deprecated PHP functions** — no `mysql_*`, no `ereg()`, no `split()`.
- Use **single quotes** in PHP. Use **double quotes** in JavaScript.
- All AJAX responses must follow the standard format: `echo json_encode(['status' => true/false, 'msg' => '...', 'data' => ...]);`
- PHP files live in `admin/application/`. JS files live in `admin/assets/js/`. Never create files outside the established directory structure without approval.
- **No magic numbers in queries.** Never write `where('status', 1)` without a comment or constant explaining what `1` means. Use named constants or config values where possible. Bare numbers in WHERE clauses become untraceable bugs when someone adds a new status value.
- **Business logic belongs in models, not controllers.** Controllers orchestrate (receive input, call model, return response). Models compute (calculations, validations, data transformations). Never put financial formulas or business rules directly in a controller method.

---

## 2. Financial Calculation Safety

- **Always wrap numeric form values with `parseFloat()`** before any arithmetic in JavaScript. Never assume a form value is already a number.
- **Always apply `toFixed(2)`** on final currency amounts before display or assignment.
- **NaN guards are mandatory** — every calculation that touches money must have `isNaN()` checks with a fallback to `0`.
- When fixing financial bugs, **trace the full data flow**: form field → JS calculation → AJAX payload → PHP model → DB column. Don't fix one layer in isolation.
- **Never change a model method's return type or column set** without checking every controller/JS function that calls it.

---

## 3. Bug Fix Discipline

- **Always consult the Module Brain first** (`knowledge_brain/{module}/`) before investigating any bug. If the brain has the answer, don't waste time re-reading source files.
- When I say **"fix this bug"** without specifying a workflow, default to `/fix-single-bug`.
- **Always search recipes before investigating** — search the central `LOGIMAX-CLIENTS/bug-recipes` repo for existing fixes (via GitHub MCP or git CLI fallback). If a recipe exists, apply it — don't re-investigate.
- **After fix is verified ("Verified working")**, immediately create a recipe following `bug-recipes/recipes/_TEMPLATE.md` and push to central `LOGIMAX-CLIENTS/bug-recipes` repo. Do NOT cache locally — the central repo is the sole destination. This is MANDATORY — do NOT wait to be asked.
- **Architecture bugs** (Track A — SQL injection, wrong joins, data corruption): Use `/fix-architecture-bug`. You lead, I approve the diff.
- **Business logic bugs** (Track B — wrong calculations, missing validations, incorrect flows): Use `/fix-business-bug`. You propose, I validate the business rule before any code change.
- **Never fix a bug without identifying the root cause first.** Patching symptoms is unacceptable.
- **Never duplicate an existing function to handle a bug fix or CR.** Instead, add a condition or parameter to the existing function. If a function already does 90% of what's needed, extend it — don't clone it. Two near-identical functions = two places to maintain, two places for bugs to diverge. The only exception is if the existing function is already too complex (e.g., 100+ lines with deeply nested logic), in which case propose a refactor plan first.
- **No copy-paste code blocks.** If the same logic (5+ lines) exists in 2 or more places, extract it into a private method or helper. Fix a bug in one copy and the others silently stay broken — this is how "fixed" bugs reappear.
- After any fix, update: `ACTIVE_BUGS.md` status, GitHub issue status, and the Module Brain if the fix changes documented behavior.

---

## 4. Safety & Guardrails

- **Never run `DROP TABLE`, `TRUNCATE`, `DELETE FROM` (without WHERE), or any destructive SQL** without explicit approval.
- **Never auto-run database migration or schema-change scripts.** Always show me the SQL first.
- **Never modify files in `admin/application/config/`** without explicit approval — these affect the entire application.
- **Never overwrite `.agent/config.md` or `.agent/workflows/`** without approval. These are the system's backbone.
- When modifying a shared utility or helper, **check all modules that import it** before changing anything.

---

## 5. Documentation & Process

- **Reference bug IDs** (e.g., EST-057, BIL-003) in all commit messages, code comments, and status updates.
- When asked to audit a module, follow `/module-bug-audit` — don't improvise a different process.
- When building a Module Brain, follow `/build-module-brain` strictly — the 8-document structure is mandatory.
- **Auto-create `knowledge_brain/{Module}/` directory** if it doesn't exist when starting a brain build. Never ask the user to create it manually — just create it and proceed.
- Keep `ACTIVE_BUGS.md` and GitHub issues in sync. If one is updated, the other must match.
- All workflow references must use `{VARIABLE}` placeholders from `.agent/config.md` — no hardcoded paths in workflows.
- **Follow workflows completely — no shortcuts.** Every workflow specifies files to create, trackers to update, reports to generate, and GitHub issues to manage. All of these must be done. Don't skip `ACTIVE_BUGS.md`, `ROLLBACK_REGISTRY.md`, `FIX_VELOCITY.md`, GitHub issue status updates, or execution plan updates just because the code fix is done. The fix isn't done until every artifact the workflow specifies has been created or updated.

---

## 6. Environment Awareness

- **Local variables** (`{PHP_PATH}`, `{PROJECT_ROOT}`, `{LOCALHOST_URL}`, `{ISSUE_PLATFORM}`, `{REPO_OWNER}`, `{REPO_NAME}`) are auto-detected by `/validate-workflows` Step 0 and stored in `.agent/.env` (gitignored). **Never hardcode these values in workflows or config.md.**
- **Shared variables** (`{CONTROLLER_DIR}`, `{MODEL_DIR}`, `{VIEW_DIR}`, etc.) live in `.agent/config.md` (committed).
- **Issue tracking**: Uses platform dispatch — Gitea REST API when `{ISSUE_PLATFORM}=gitea`, GitHub MCP when `{ISSUE_PLATFORM}=github`. See `/github-bug-tracking` for the dispatch table.
- **PHPUnit**: `{TEST_DIR}vendor/bin/phpunit` (version 9.6, PHP 7.x compatible).
- **Localhost URL**: Always read `{LOCALHOST_URL}` from `.agent/.env`. Never use production URLs.
- When setting up for a new client/project, run `/validate-workflows` to auto-detect all local values — never manually copy paths from another client's setup.

---

## 7. Communication Preferences

- When presenting a fix or plan, **show the specific lines changing** — don't just describe what you'll do.
- For business logic bugs, **state the violated business rule in plain English** before showing any code.
- When multiple bugs exist, **prioritize by severity**: Critical → High → Medium → Low. Don't ask me which to fix first unless severities are equal.
- If a fix touches more than 3 files, **present an implementation plan** before starting.

---

## 8. Security — Input & Output Hygiene

- **Never trust user input.** Always use `$this->input->post()` / `$this->input->get()` instead of raw `$_POST` / `$_GET`.
- **Always use `$this->db->escape_str()` or query bindings** when any user input touches a query — even in active record `where()` clauses with raw strings.
- **XSS protection**: Use `$this->security->xss_clean()` on any user input that will be rendered back in HTML. Use `htmlspecialchars()` on output, not input.
- **CSRF tokens**: Never remove or bypass CI3's CSRF protection. If an AJAX form fails due to CSRF, fix the token handling — don't disable the feature.
- **File uploads**: Always validate file type, size, and extension server-side. Never rely on client-side validation alone.
- When fixing a bug that involves user input, **always check if the same input path has injection or XSS exposure** — fix both while you're there.

---

## 9. JavaScript — jQuery & Plugin Patterns

- This project uses **jQuery** (not vanilla JS, not React). Write jQuery-idiomatic code: `$('#id')`, `.on()`, `$.ajax()`.
- **Select2**: Use `.val(...).trigger('change')` to set values programmatically. Never use the deprecated `.select2('val', ...)` API.
- **DataTables**: When refreshing data, use `.ajax.reload()` on the existing instance — never re-initialize a DataTable on the same element.
- **Event delegation**: For dynamically added rows (e.g., estimation items, billing line items), always use `$(document).on('event', 'selector', handler)` — never bind directly to elements that don't exist at page load.
- **DOM readiness**: All page-init code must be inside `$(document).ready()` or equivalent. Never assume DOM elements exist at script load time.
- **Plugin conflicts**: Before adding or upgrading any jQuery plugin, check for version conflicts with existing plugins (especially Bootstrap JS, AdminLTE, SlimScroll, and Select2).
- **No hardcoded URLs in AJAX calls.** Never write `$.ajax({url: '/admin/controller/method'})`. Always use a base URL variable passed from PHP (e.g., `base_url` set via a global JS variable or `<meta>` tag). Hardcoded paths break when the app moves to a different subdirectory or domain.

---

## 10. Database & Query Safety

- **Never use `SELECT *`** in new code. Always specify the exact columns needed.
- **Check for N+1 queries** — if a loop runs a query per iteration, refactor to a single query with `where_in()`.
- **JOIN safety**: When writing JOINs, always specify `LEFT JOIN` vs `INNER JOIN` intentionally. Using the wrong type silently drops rows and causes data loss.
- **Soft deletes**: Many tables use `is_deleted` or `status` flags. When querying, **always include the soft-delete filter** unless explicitly told to include deleted records.
- **Pagination**: Any query that could return 100+ rows **must** have `LIMIT`/`OFFSET` or CI3's pagination library. Never load unbounded result sets into memory.
- **Transaction safety**: For operations that insert/update multiple related tables (e.g., estimation + estimation items), **always wrap in `$this->db->trans_start()` / `$this->db->trans_complete()`**.

---

## 11. Error Handling — No Silent Failures

- **Never use empty `catch` blocks** in PHP or `try-catch` in JS without at least logging the error.
- **AJAX error callbacks are mandatory** — every `$.ajax()` call must have an `error:` handler that at minimum shows a user-facing message. Never leave the user staring at a frozen screen.
- **PHP model methods** that return data must return a consistent type — if the method normally returns an array, return an empty array on failure, not `false` or `null`. Document the return type.
- **Log before you fail** — any `die()`, `exit()`, or early return in a controller should `log_message('error', ...)` first so we can trace production issues.
- When a calculation produces `0` or an unexpected value, **actively flag it** rather than silently assigning it. Zero is often a bug, not a valid result, in financial contexts.

---

## 12. Cross-Module Impact Analysis

- Before modifying any **shared model** (e.g., `common_model`, `auth_model`, `access_right_model`), **grep all controllers and views** that call it. Present the list of affected modules before making changes.
- Before modifying a **database column** used across modules, **check the Module Registry** in `.agent/config.md` and verify which modules reference that table.
- **Helper files** (`admin/application/helpers/`) are global — treat any change to a helper function as a change to every module. Run a full impact check.
- When a bug fix in Module A requires a change in Module B's model or controller, **flag it as a cross-module fix** and get approval before touching Module B.
- **Shared JS files** (e.g., `common.js`, `notifications.js`) follow the same rule — check all pages that include the file before modifying any function in it.

---

## 13. Workflow Compliance

- **Never skip a workflow step silently.** If a step must be skipped, state: (a) which step, (b) why it's being skipped, (c) impact of skipping. Silent skips are unacceptable.
- **Environment pre-check is always first.** Before any workflow, verify: PHP path works, MCP is connected (if needed), `.agent/.env` values match the current workspace.
- **Never test against production URLs.** Always use `{LOCALHOST_URL}` from `.env`. This is non-negotiable.
- **Follow the test type matrix** in `/test-and-verify` Step 1.5 — do NOT default to PHPUnit for report/SQL bugs. Use CI3 bootstrap scripts instead.
- **Create a workflow checklist** at the start of every workflow execution. Track each step as TODO/DONE/SKIPPED(reason).
- **If config is wrong, fix config FIRST** before running any workflow step. Wrong config = cascading failures.

---

## 14. Print/View Bug Protocol

- **Never guess CSS width values.** Always find a WORKING reference view in the same module or a similar module and copy its CSS pattern.
- **For dashed line widths**: Calculate as `column_count × base_width + buffer` — don't trial-and-error.
- **For image cells**: Always use `max-width`, `max-height`, and `overflow:hidden` together.
- **One CSS change at a time**: Don't fix 3 CSS issues in one edit — fix one, verify visually, then the next.
- **Always use browser subagent**: After any CSS/layout change in a print view, capture a screenshot to verify before presenting to user.
- **Compare before coding**: Before fixing any print/view bug, open the WORKING version of a similar print (e.g., packing list print when fixing stock issue print) side-by-side and note its exact CSS class names, widths, and layout structure.

---

## 15. Cross-Conversation Context

- **When resuming a bug fix from a previous conversation**, always read that conversation's walkthrough/artifacts first — don't restart diagnosis from scratch.
- **Reference previous attempts**: If a fix was attempted and rejected/reverted, the current fix must explain why the previous approach failed and how this one differs.
- **Never re-apply a rejected fix** without explicit human approval and an explanation of what changed.

---

## 16. System Brain — Mandatory Reference (`knowledge_brain/_SYSTEM/`)

> **Non-negotiable:** The `knowledge_brain/_SYSTEM/` directory is the single source of truth for cross-module system knowledge. You MUST consult it before undertaking any cross-module, system-level, or diagnostic task.

### When to Read the System Brain

- **Before any cross-module bug fix or impact analysis** — read `SHARED_TABLES.md`, `SHARED_MODELS.md`, and `MODULE_DEPENDENCIES.md` first.
- **Before diagnosing any bug** — read `DIAGNOSTIC_PLAYBOOK.md` for the standard diagnostic procedure.
- **Before modifying any shared data flow** — read `DATA_FLOW_CHAINS.md` to understand upstream/downstream impacts.
- **Before touching any high-risk area** — read `DANGER_ZONES.md` to know what can break silently.
- **Before any system-level audit or review** — read `REVIEW_DOCUMENT.md` and `SYSTEM_COVERAGE.md` for the current state of system coverage.
- **Before fixing validation or input-handling bugs** — read `VALIDATION_GAPS.md` for known gaps.
- **Before working on cross-module bugs** — read `CROSS_MODULE_BUGS.md` for documented cross-module issues and patterns.
- **Before addressing performance issues** — read `PERFORMANCE_RISKS.md` for known bottlenecks.
- **Before cleanup or refactoring** — read `CLEANUP_GAPS.md` for known technical debt.
- **Before interpreting status codes or tag values** — read `TAG_STATUS_MAP.md` and `HARDCODED_VALUES.md`.
- **Before any module handoff or onboarding** — read `HANDOFF_AUDIT.md` for handoff readiness status.
- **Before verifying business flow correctness** — read the relevant checklist in `FLOW_CHECKLISTS/`.

### Document Index

| Document | Purpose |
|---|---|
| `SHARED_TABLES.md` | Tables used by multiple modules — column maps, ownership, and cross-module dependencies |
| `SHARED_MODELS.md` | Model files shared across modules — method index and caller list |
| `MODULE_DEPENDENCIES.md` | Module-to-module dependency graph — who calls whom |
| `DATA_FLOW_CHAINS.md` | End-to-end data flow chains across modules (e.g., Estimation → Billing → GST) |
| `CROSS_MODULE_BUGS.md` | Documented cross-module bugs and resolution patterns |
| `DANGER_ZONES.md` | High-risk code areas that break silently — must-read before touching shared code |
| `DIAGNOSTIC_PLAYBOOK.md` | Standard diagnostic procedures for common bug categories |
| `VALIDATION_GAPS.md` | Known validation gaps across all modules |
| `PERFORMANCE_RISKS.md` | Known performance bottlenecks and query risks |
| `CLEANUP_GAPS.md` | Technical debt and cleanup opportunities |
| `TAG_STATUS_MAP.md` | Status/tag value mappings used across the system |
| `HARDCODED_VALUES.md` | Hardcoded magic values and their meanings |
| `HANDOFF_AUDIT.md` | Module handoff readiness and knowledge gaps |
| `REVIEW_DOCUMENT.md` | Comprehensive system review with business flow verification |
| `SYSTEM_COVERAGE.md` | Module Brain coverage status and completeness tracking |
| `FLOW_CHECKLISTS/` | Per-process business flow checklists for sign-off verification |

### Rules

- **Never skip the System Brain.** If a task involves more than one module, at least `MODULE_DEPENDENCIES.md` and `SHARED_TABLES.md` must be read before writing any code.
- **If the System Brain contradicts the Module Brain**, flag the discrepancy — do not silently pick one over the other.
- **Update the System Brain** when a fix changes cross-module behavior, adds/removes a shared table dependency, or modifies a data flow chain.
