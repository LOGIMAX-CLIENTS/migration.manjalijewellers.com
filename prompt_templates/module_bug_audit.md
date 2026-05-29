# Module Bug Audit — Prompt Templates (6 Rounds)

> **Purpose**: Comprehensive multi-round static analysis audit of any module in this PHP CodeIgniter 2 ERP codebase.  
> **How to use**: Use the **Master Prompt** to kick off a full audit in one shot, OR run each individual round prompt sequentially for more control. Replace placeholders in `{CURLY_BRACES}` with actual values.

---

## Master Prompt — Full Audit (All 6 Rounds)

Use this single prompt to kick off the entire audit. Copy everything inside the code fence, fill the placeholders, and paste.

````
GOAL
-----
You are an engineering assistant with full access to the repository and the complete SQL schema. Your objective is to discover **existing system-level bugs** in the {MODULE_NAME} module (legacy PHP CodeIgniter 2 + JS ERP admin UI), produce a consolidated bug report with severity and classification, and for each bug produce a detailed task (one bug = one task) with an implementation plan and a permanent fix. Assume the knowledge base covers only ~40% of the module; do not assume business rules beyond what you can infer from code, schema, tests, and KB entries.

SCOPE
-----
- Target: the entire {MODULE_NAME} module and any code paths it touches (controllers, models, libraries, helpers, views, JS modules, API endpoints, background jobs, cron tasks, and DB interactions).
- Use static analysis, dynamic tests, schema inspection, existing unit/integration tests (if present), and run sample UI flows where possible.
- Use the SQL schema to validate data integrity, constraints, indexes, and transaction boundaries.
- Cross-check findings against the provided knowledge base; mark which KB entries apply and where KB is missing.

OUTPUT FORMAT
--------------
Produce two outputs in the same response:

1) **Consolidated summary (human readable)** — a Markdown document with:
   - Module snapshot (files touched, database tables involved, KB coverage %).
   - Prioritized list of found bugs (sorted by severity then confidence).
   - Quick summary table (ID, title, severity, classification, confidence).

2) **Detailed machine-readable list** — a JSON array of bug objects. Each bug object must include the following fields exactly:

{
  "id": "string (unique, e.g. {MODULE_PREFIX}-001)",
  "title": "short descriptive title",
  "severity": "P0 | P1 | P2 | P3 (explain mapping: P0=critical production loss/data corruption; P1=major functionality broken; P2=minor/edge case; P3=cosmetic/low)",
  "classification": "Functional | Calculation | Validation | Data Integrity | Concurrency | Performance | Security | UI/UX | Integration | Schema | Logging | TestCoverage",
  "confidence": "High | Medium | Low",
  "affected_files": ["repo/path/file.php:line-range", "..."],
  "affected_db_tables": ["schema.table(column,...)"],
  "reproduction_steps": ["exact UI path or API call or SQL query or unit-test to reproduce (include payloads if applicable)"],
  "repro_commands": ["shell or curl or sql statements (executable)"],
  "observed_behavior": "what the system currently does",
  "expected_behavior": "what should happen according to code/inference/KB",
  "root_cause_analysis": "concise technical root cause with pointer to code lines or DB schema mismatch",
  "proposed_permanent_fix": {
      "patch_outline": "high-level description of code changes, functions to modify, helper additions, algorithm fixes, or DB migration; include file paths and function names",
      "exact_sql_migration": "if schema change needed, provide ALTER/CREATE/UPDATE/rollback SQL statements",
      "code_snippet_or_diff_example": "small code sample or diff illustrating the fix (where feasible)"
  },
  "frontend_validations": ["field-level rules, constraints, messages; JS file paths to change"],
  "backend_validations": ["controller/model/DB-layer validation rules; exact validation code to add/modify"],
  "tests_to_add": {
      "unit_tests": ["file path and test case description; input->expected output"],
      "integration_tests": ["endpoint/flow description and assertions"],
      "e2e_tests": ["UI steps and assertions"]
  },
  "migration_plan": "data migration strategy if any (transform scripts, backfill queries, batch size, idempotency, rollback steps)",
  "rollback_plan": "how to revert code + schema + data changes safely",
  "deployment_notes": "DB locks/maintenance windows, feature-flagging guidance if needed",
  "monitoring_and_alerting": ["logs to add, metrics to add, queries that indicate recurrence"],
  "kb_correlation": {
      "kb_entries_used": ["kb_id or path or 'none'"],
      "kb_missing": "concrete KB items to add for future prevention"
  },
  "ready_to_fix_in_repo": true|false,
  "fix_one_task_instructions": ["step-by-step commands to apply the fix locally, run tests, create a PR (one task per PR)"]
}

DETAILED INSTRUCTIONS / CHECKLIST FOR YOUR ANALYSIS
-------------------------------------------------
1. Static code analysis
   - Run language-appropriate linters and static analyzers for PHP/JS.
   - Search for suspicious patterns: silent catches, disabled error reporting, direct SQL concatenation, missing transactions, duplicated business logic.
   - Locate all {MODULE_NAME}-related controllers/models/views and JS modules (search keywords: {MODULE_NAME}, estimate, quote, calc, mc, markup, margin, rate, labour, labour_rate).

2. Calculation correctness
   - Identify all calculation formulas (e.g., material cost, labour, markups, taxes). For each:
     - Verify numeric types, rounding/precision (use decimal vs float), order of operations.
     - Verify currency/units conversions.
     - Cross-check any DB-stored rate/parameters usage — ensure latest effective rate is used.
     - Simulate boundary cases (zero, negatives, very large values).

3. Data integrity & concurrency
   - Inspect DB schema for proper constraints (NOT NULL, FK, data types, precision, indexes).
   - Search for missing transactions around multi-step writes (create estimate -> add items -> commit).
   - Identify race conditions (concurrent edits, autosaves).
   - Discover orphan records and cases of inconsistent denormalized data (e.g., cached totals vs derived totals).

4. Validation (frontend & backend)
   - For each input field used in calculations, ensure both client-side and server-side validations exist and align.
   - Validate type, range, requiredness, and format.
   - Check for duplicate validations or validations only on client-side.

5. DB usage & performance
   - Identify N+1 queries, missing indexes on WHERE/JOIN columns, heavy queries without pagination.
   - Identify long-running data migrations or queries that run on web requests.

6. Security & access control
   - Check for SQL injection vectors, unsanitized user input, inadequate RBAC checks around estimate edit/approve/delete flows.
   - Check for XSS in admin UI inputs rendered back.

7. Logging, error handling, observability
   - Check if errors are logged with stack traces and sufficient context.
   - Identify noisy logs or suppressed errors.
   - Recommend actionable alerts for calculation mismatches or repeated failures.

8. Test coverage
   - Report on missing/insufficient tests for critical calc paths and DB migrations.
   - Provide exact test stubs to add.

PRIORITIZATION RULES
--------------------
- Severity mapping: P0 = production data loss or wrong financial numbers posted to customers/accounts; P1 = major functionality broken in common flows; P2 = noticeable but not broadly harming; P3 = UI/cosmetic or extremely rare edge case.
- Within same severity, sort by inferred impact (monetary/data integrity/legal) and confidence.

TASK RULES (how to treat each bug)
----------------------------------
- Treat every bug as an independent task: one bug → one PR, with its own tests and rollback plan.
- For each task provide an **ordered implementation plan** (step-by-step), including:
  1. Branch name recommendation
  2. Files to change (paths + small patch outline)
  3. Tests to add (with exact test names and assertions)
  4. SQL migrations (if any) and idempotent transformation scripts
  5. Local verification commands (unit tests, integration tests, sample API calls)
  6. PR checklist (code style, tests passed, QA steps, KB entry updated)
- Do **not** apply changes automatically now. Provide the precise instructions and code diffs so a developer can apply them safely.

KB COVERAGE & SUGGESTIONS
-------------------------
- For each bug mark if it is already covered by an existing KB rule. If not covered, produce KB additions: exact KB text, conditions, and example test cases.

CONFIDENCE & EVIDENCE
---------------------
- For each finding include the evidence: stack traces, failing test outputs, SQL explain plans, screenshots (if UI), exact log lines, and file/line references.
- Provide a confidence label (High/Medium/Low) and explain why (e.g., reproducible with unit test = High).

RESPONSES
---------
- Produce full consolidated Markdown + the complete JSON array in one response.
- If analysis finds zero bugs, still produce a coverage report, list of risky areas with recommendations, and KB gaps.

IMPORTANT CONSTRAINTS
---------------------
- Do not assume business rules not encoded in code/DB/KB.
- One bug per task; prepare tasks to be fixed independently (no combined multi-bug PRs).
- All outputs must be actionable, reproducible, and include code-level pointers or diffs.

Store bug reports in the `bug_report_AI/` directory sub-module-wise.

**Reference files for this module**:
- Controller: `{CONTROLLER_FILE}`
- Model: `{MODEL_FILE}`
- JavaScript: `{JS_FILE}`
- Views: `{VIEW_DIR}`
- Knowledge base: `{KB_DIR}`

Begin analysis now. Start by scanning the repo for files containing the module-related tokens. Correlate findings with DB tables from the schema. When ready, output the consolidated markdown summary and the JSON array described above. End.
````

---

## Variables — Fill these before starting

| Variable | Description | Example |
|---|---|---|
| `{MODULE_NAME}` | Human-readable module name | Estimation, Billing, Inventory |
| `{MODULE_PREFIX}` | Short prefix for bug IDs (3 chars) | EST, BIL, INV |
| `{CONTROLLER_FILE}` | Controller path | `admin/application/controllers/admin_ret_estimation.php` |
| `{MODEL_FILE}` | Model path | `admin/application/models/ret_estimation_model.php` |
| `{JS_FILE}` | Main JS path | `admin/assets/js/ret_estimation.js` |
| `{VIEW_DIR}` | View directory/file path | `admin/application/views/estimation/` |
| `{DB_SCHEMA_FILE}` | SQL structure file | `dev_structure.sql` |
| `{KB_DIR}` | Knowledge base directory for the module | `estimation/` |

---

## Round 1 — Controller Code-Level Analysis

```
Conduct Round 1 of a bug audit on the {MODULE_NAME} module.

**Scope**: Read the controller file `{CONTROLLER_FILE}` end-to-end.

**What to check**:
1. Trace ALL save/update/delete paths — follow every branch, every if/else.
2. For each save/update path, list every field being written to the DB. Flag:
   - Undefined or misspelled variables (e.g., `$arrayMaterials` vs `$arrayMaterial`)
   - Fields present in INSERT but missing from UPDATE (or vice versa)
   - Wrong values stored (e.g., storing `market_rate_cost` in a column named `market_rate_tax`)
   - POST field names that don't match what the JS actually sends
3. For delete paths, check if ALL child/related tables are cleaned up. Flag orphan records.
4. Check for missing CSRF / `form_secret` / double-submit protection.
5. Check for debug statements left in production (`print_r`, `var_dump`, `echo`, `exit`).
6. Check transaction handling — is `trans_begin()` / `trans_commit()` / `trans_rollback()` used correctly? Does the failure branch rollback or accidentally commit?
7. Check date formatting — does `date()` vs `date('Y-m-d')` match the DB column type?

**Output**:
- Create `bug_report_AI/` directory if it doesn't exist.
- Write a detailed report per sub-module (e.g., `bug_report_AI/add_{module}/BUG_REPORT_SUMMARY.md`).
- Write a machine-readable `bugs_detailed.json`.
- Write an initial `bug_report_AI/CONSOLIDATED_BUG_REPORT.md` with all bugs indexed.

**Bug format**: ID = `{MODULE_PREFIX}-NNN`, Severity = P0/P1/P2/P3, include file, line number, code snippet with ❌ annotation, impact, and concrete fix.

**Severity rules**:
- P0: Data loss, data corruption, security vuln, feature completely broken
- P1: Wrong results saved/displayed, validation bypass, missing data, orphan records
- P2: Edge cases, UX issues, inconsistent behavior
- P3: Code quality, dead code, debug statements
```

---

## Round 2 — DB Schema Cross-Reference

```
Conduct Round 2 of the {MODULE_NAME} module bug audit — DB Schema Cross-Reference.

**Context**: Round 1 found [N] bugs. The controller save/update paths have been analyzed. Now cross-reference against the actual DB schema.

**Scope**: 
- Extract all {MODULE_NAME}-related table schemas from `{DB_SCHEMA_FILE}`.
- Cross-reference against the PHP code's INSERT/UPDATE arrays from Round 1.

**What to check**:
1. Column type mismatches — PHP sends DECIMAL but column is INT (truncation), PHP sends datetime but column is DATE.
2. Missing AUTO_INCREMENT or PRIMARY KEY on ID columns — will cause insert failures.
3. NOT NULL columns that the PHP code never populates — will cause silent failures or constraint errors.
4. Table engine: MyISAM on tables that use transactions (`trans_begin`/`trans_commit`) — MyISAM ignores transactions silently.
5. VARCHAR length too short for the data being stored.
6. Missing UNIQUE constraints where business logic requires uniqueness.
7. Missing foreign key constraints — allows orphan records.
8. Missing indexes on columns used in JOIN ON or WHERE clauses — performance issue.
9. ENUM columns that don't cover all values the PHP code sends.

**Output**:
- Write `bug_report_AI/ROUND_2_SCHEMA_ANALYSIS.md`.
- Include the extracted CREATE TABLE statements for reference.
- For each bug, provide a ready-to-run `ALTER TABLE` fix script.
- Update `bug_report_AI/CONSOLIDATED_BUG_REPORT.md` with Round 2 findings.

**Bug IDs**: Continue from `{MODULE_PREFIX}-S01` for schema bugs.
```

---

## Round 3 — Model & Data-Flow Deep Dive

```
Conduct Round 3 of the {MODULE_NAME} module bug audit — Model & Data-Flow Deep Dive.

**Context**: Rounds 1-2 found [N] bugs across controller and schema. Now audit the model layer and trace full data flow.

**Scope**: Read the model file `{MODEL_FILE}` end-to-end — every method.

**What to check**:

**SQL Injection**:
1. Any method where a user-controlled value (especially from POST) is used to construct column names, table names, or ORDER BY clauses — these CANNOT be parameterized with query bindings.
2. String concatenation in WHERE clauses instead of using CI query bindings (`$this->db->where()`).
3. `$searchField` or similar parameters that control which column to search — if the value comes from POST, it's injectable.

**Query Logic**:
4. Cartesian JOINs: `JOIN` without proper ON condition, or self-referencing conditions like `a.col = a.col`.
5. Subqueries missing GROUP BY when used with aggregate functions — returns wrong results.
6. Missing WHERE clauses that should filter by branch, status, or other scoping fields.
7. SELECT * when only specific columns are needed (data exposure + performance).

**Variable & Data Flow**:
8. Variable name typos: `$returndata` vs `$return_data`, `$data` being overwritten by a later assignment.
9. Null dereference: accessing `$result[0]->field` when the query might return empty.
10. Parameter shadowing: function parameter name same as a local variable, causing overwrites.

**Edit Lifecycle**:
11. Trace: controller loads data (model SELECT) → passes to view → JS populates form → user edits → JS submits POST → controller processes → model saves.
12. Flag any fields that are SELECTed for edit but NOT sent back in the POST (lost data on re-save).
13. Flag any fields where the SELECT column name differs from the POST field name.

**Output**:
- Write `bug_report_AI/ROUND_3_DEEP_DIVE.md`.
- For each SQL injection finding, show the vulnerable query AND the safe replacement.
- Update `bug_report_AI/CONSOLIDATED_BUG_REPORT.md`.

**Bug IDs**: `{MODULE_PREFIX}-R3NN` (e.g., {MODULE_PREFIX}-R301).
```

---

## Round 4 — JS Save Handler & View Layer

```
Conduct Round 4 of the {MODULE_NAME} module bug audit — JS Save Handler & View Layer Deep Dive.

**Context**: Rounds 1-3 found [N] bugs across controller, schema, and model. Now audit the client-side save flow and view template.

**Scope**:
- JS file: `{JS_FILE}` — focus on save/submit click handlers.
- View file(s): `{VIEW_DIR}` — the main form template.

**JS Save Handler — What to check**:
1. Find the main save button click handler (e.g., `$('#btn-submit').click`, `$('#est_print').click`).
2. Trace the validation flow:
   - Which validation functions are called? Are the RIGHT ones called for each tab/section?
   - Are there always-true conditions? (e.g., `$('#table tbody tr').length >= 0` — .length is never negative)
   - Single-flag pattern: does a single `form_validate` variable get overwritten by each section check? (Last section's result overwrites all prior failures)
3. Trace the POST data assembly — what fields are collected and sent to the server?
4. Check if there are multiple save handlers (e.g., one for regular save, one for EDA/audit save). Do they diverge? Were changes applied to one but not the other?
5. Delete handler: is it GET or POST? GET-based deletes are CSRF-vulnerable.
6. AJAX cache-busters: `getUTCSeconds()` only returns 0-59, not unique. Should use `Date.now()`.

**View Layer — What to check**:
7. Duplicate HTML `id` attributes — JS `$('#id')` will only find the first one.
8. XSS: unescaped output (e.g., `<?= $flashdata ?>` without `htmlspecialchars()`).
9. Double form close tags (`</form>` + `<?= form_close() ?>`).
10. Hidden inputs exposing server config (max values, permission flags). Are these validated server-side or only client-side?
11. Duplicate `name` attributes on hidden inputs — only the last value gets submitted.
12. Form action URL — is it correct for both add and edit modes?

**Output**:
- Write `bug_report_AI/ROUND_4_JS_VIEW_DEEP_DIVE.md`.
- For each validation bug, show the buggy flow vs the correct flow.
- Update `bug_report_AI/CONSOLIDATED_BUG_REPORT.md`.

**Bug IDs**: `{MODULE_PREFIX}-R4NN` (e.g., {MODULE_PREFIX}-R401).
```

---

## Round 5 — JS Calculations & Data Binding

```
Conduct Round 5 of the {MODULE_NAME} module bug audit — JS Calculations & Data Binding Deep Dive.

**Context**: Rounds 1-4 found [N] bugs. Save handlers and views are analyzed. Now audit the calculation engine and data binding logic.

**Scope**: `{JS_FILE}` — focus on calculation functions, tag/item scan handlers, row creation functions.

**Calculation Functions — What to check**:
1. Find all functions that calculate: sale value, taxable amount, tax (GST/IGST/CGST/SGST), market rate, wastage, making charges (MC), net weight.
2. Variable reference errors (copy-paste bugs):
   - Function calculates `market_base_value_tax` but then adds `base_value_tax` (the regular rate's tax) instead. This is the most common copy-paste error.
   - Look for variable names that are SIMILAR but not IDENTICAL to nearby variables.
3. Division by zero: any `a / b` where `b` could be 0 (pieces, pcs, quantity). Result is `Infinity` → NaN propagation.
4. Class selector vs row-scoped selector: `$('.class_name').val(x)` sets ALL rows. Should be `curRow.find('.class_name').val(x)`.
5. `var` re-declaration inside `if`/`else` blocks — hoisted to function scope, overwrites outer variable.
6. `parseFloat` / `parseInt` without NaN guard — `parseFloat('')` returns `NaN`, which poisons every downstream calculation.

**Tag/Item Scan Handlers — What to check**:
7. Find all tag scan/search AJAX handlers (e.g., `get_tag_data`, barcode scan handler).
8. Duplicate item check: does it use the correct variable? Common bug: `items.tag_id` where `items` is from an outer `$.each` but the check is in an inner scope where `items` is undefined. Should be `data[0].tag_id`.
9. Were validation checks removed during refactoring? Compare the current version with any older/commented-out/backup versions in the file. Common: metal type mismatch check removed.
10. Undefined variables used in field assignments: function assigns `rate_per_grm` but never defines it → `undefined` stored in the field.

**Row Creation Functions — What to check**:
11. Employee dropdown logic: does it add a duplicate `<option>` for the pre-selected employee?
12. Collection confirm modal: does it fire per non-matching row (O(n²) modals)?
13. Are all hidden field values properly set when creating a new row?

**Output**:
- Write `bug_report_AI/ROUND_5_JS_CALCULATIONS_DEEP_DIVE.md`.
- For each calculation error, show the buggy formula vs the correct formula.
- Update `bug_report_AI/CONSOLIDATED_BUG_REPORT.md`.

**Bug IDs**: `{MODULE_PREFIX}-R5NN` (e.g., {MODULE_PREFIX}-R501).
```

---

## Round 6 — Validation Functions & Controller AJAX Endpoints

```
Conduct Round 6 of the {MODULE_NAME} module bug audit — Validation Functions & Controller AJAX Endpoints.

**Context**: Rounds 1-5 found [N] bugs. This is the final sweep covering validation logic and all remaining AJAX endpoints.

**Scope**:
- JS: All `validate*DetailRow()` functions in `{JS_FILE}`.
- Controller: All AJAX endpoint methods in `{CONTROLLER_FILE}` (everything after the main save method).

**Validation Functions — What to check**:
1. Missing `return false` after setting `row_validate = false` — the `.each()` loop continues to process remaining rows, potentially showing confusing error messages for later rows.
2. Logically impossible conditions:
   - `val <= 0 && val == ''` → AND requires BOTH true. `0 == ''` is true in JS, but `0.00 == ''` is false. Should likely be `||` (OR).
   - `val >= 0` used as a check — always true for non-negative numbers.
3. Side effects in validation: does the validation function MODIFY form data? (e.g., copying `.cat_mc` → `.nn_cat_mc`). Validation should only READ, never WRITE.
4. Inconsistent validation between similar functions (tag vs catalog vs custom vs old metal). Were changes applied to one but not all?
5. `else if` chain where early validation failure prevents later mandatory checks from running.

**Controller AJAX Endpoints — What to check**:
6. Transaction handling: `trans_commit()` on the failure branch instead of `trans_rollback()` — persists corrupt partial state.
7. Mixed input access: raw `$_POST['field']` vs `$this->input->post('field')`. Raw `$_POST` bypasses CI's XSS filtering. Count how many endpoints use each.
8. Missing access modifier: `function methodName()` instead of `public function methodName()`. In CI2 this defaults to public, but it breaks conventions and may cause issues in stricter environments.
9. File upload security:
   - `mkdir($path, 0777)` — world-writable directories. Should be `0755`.
   - `base64ToFile()` or similar — does it validate MIME type BEFORE writing to disk?
   - Are temp files cleaned up on failure?
10. Raw DB queries in controller (should be in model — MVC violation).
11. Commented-out debug statements (`print_r`, `var_dump`, `echo "<pre>"`) left in production.
12. Methods that pass entire `$_POST` to model (`$this->$model->method($_POST)`) — no input filtering.

**Output**:
- Write `bug_report_AI/ROUND_6_VALIDATION_AJAX_DEEP_DIVE.md`.
- Update `bug_report_AI/CONSOLIDATED_BUG_REPORT.md` with final totals and updated sprint priorities.
- In the consolidated report:
  - Update the executive summary with total bug count.
  - Add Round 6 to the distribution table.
  - Add Round 6 bugs to the master index.
  - Update sprint priorities (Sprint 1 = critical, Sprint 2 = important, Sprint 3 = backlog).
  - Update the file structure reference.

**Bug IDs**: `{MODULE_PREFIX}-R6NN` (e.g., {MODULE_PREFIX}-R601).
```

---

## Post-Audit Checklist

After all 6 rounds, verify:
- [ ] `CONSOLIDATED_BUG_REPORT.md` has correct total counts
- [ ] All round reports reference consistent bug IDs
- [ ] Sprint priorities cover all P0 and P1 bugs
- [ ] Each bug has a concrete fix (code snippet or ALTER TABLE)
- [ ] File structure section in consolidated report lists all generated files

---

## Codebase-Specific Notes

- JS files are **double-spaced** (~31K lines = ~15K actual code). `grep` may fail on patterns; prefer sequential `view_file` reads.
- Backup JS files (e.g., `ret_estimation_14_07_2025.js`) contain older function versions — useful for diffing removed validation checks.
- CodeIgniter 2 (not 3/4): `$this->input->post()` provides XSS filtering; raw `$_POST` does not.
- jQuery used for all DOM/AJAX. No modern framework.
- **All financial calculations happen client-side in JS** — server-side re-validation is minimal. This is a known architectural weakness.
