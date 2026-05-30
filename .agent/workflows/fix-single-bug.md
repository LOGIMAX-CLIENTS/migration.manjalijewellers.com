---
description: Fix a single bug end-to-end using the 8-step Antigravity workflow with layer-specific procedures
version: 2.1
last_updated: 2026-04-02
---

# Fix Single Bug Workflow

## Purpose

Fix ONE bug at a time with surgical precision, full traceability, and documented rollback. This is the **Fix Execution** phase — `/bug-intake-triage` prepares the bug, this workflow fixes it.

## Core Principles

> **ONE bug at a time. No batch fixes. Stability > Elegance. Documentation first.**

1. **Isolation** — Each bug fixed in its own change, linked to a specific Bug ID
2. **Traceability** — Every change references the Bug ID in commit messages and docs
3. **Reversibility** — Every fix has a documented rollback plan BEFORE the fix is applied
4. **Surgical precision** — Minimal fix footprint, no opportunistic refactoring
5. **Pattern reuse** — Check `COMMON_BUG_PATTERNS.md` BEFORE writing a fix from scratch

## ⛔ Non-Negotiable Mandatory Rules

> [!CAUTION]
> **These two rules are HARD GATES. They CANNOT be skipped, deferred, or "done later" under ANY circumstance. Violating either rule is a workflow failure.**

### Rule 1: System Brain Cross-Reference

**WHEN**: During Step 1 (DIAGNOSE), if `knowledge_brain/_SYSTEM/` exists.
**WHAT**: Cross-reference the fix against all System Brain files (`SHARED_TABLES.md`, `TAG_STATUS_MAP.md`, `CLEANUP_GAPS.md`, `HARDCODED_VALUES.md`, `VALIDATION_GAPS.md`).
**WHY**: Prevents cross-module breakage from fixes that touch shared tables or status fields.
**ENFORCEMENT**: If System Brain exists and this step is skipped → the fix is considered **incomplete and unverified**, regardless of test results.

### Rule 2: Recipe Creation & Push

**WHEN**: Immediately after Step 7 (APPROVAL GATE) passes — NOT after "Verified working", NOT after being asked.
**WHAT**: Create recipe following `bug-recipes/recipes/_TEMPLATE.md` and push directly to central `LOGIMAX-CLIENTS/bug-recipes` repo via MCP or git CLI.
**WHY**: Every fix must be captured as a reusable recipe for cross-client auto-fix. Delaying loses context.
**ENFORCEMENT**: The Completion Report CANNOT be generated until the recipe exists on the central repo. If git push fails, use GitHub MCP as fallback.

## Prerequisites

- Bug ID is known (e.g., `EST-R301`, `BIL-S02`)
- Bug has been triaged via `/bug-intake-triage` (severity, track, category assigned)
- Execution plan exists: `bug_report_AI/{module}/` directory with `*_EXECUTION_PLAN.md`
- `bug_report_AI/COMMON_BUG_PATTERNS.md` exists and is loaded
- Module Brain preferred: `knowledge_brain/{MODULE_NAME}/MODULE_BRAIN.md`

## Input

- `{BUG_ID}` — The bug to fix (e.g., EST-R601)
- The execution plan file path

## Steps

### Step 0: DUPLICATE FIX GUARD [Antigravity]

1. Read the bug entry from the execution plan (`*_BUG_FIX_EXECUTION_PLAN.md`)
2. Check the bug's status:
   - **`✅ Fixed`** → WARN: "Bug {BUG_ID} was already fixed on {date}. Options: **Re-open** (if fix was reverted/incomplete) / **Skip** (move to next bug)"
   - **`🔧 In Progress`** → WARN: "Bug {BUG_ID} is currently being worked on. Continue? Y/N"
   - **`⏳ Queue` / blank** → Proceed normally
3. If GitHub Issue exists for this bug, check its state:
   - Closed → same warning as `✅ Fixed`

### Step 0b: REGISTER IN ACTIVE BUG TRACKER [Antigravity]

1. Read `.agent/config.md` for project variables
2. Open `bug_report_AI/ACTIVE_BUGS.md`
3. Add row to **In-Progress Bugs** table:
   ```
   | {BUG_ID} | {MODULE} | {SEVERITY} | {TRACK} | Step 1: DIAGNOSE | /fix-single-bug | {developer} | {NOW} | #{ISSUE_NUMBER} |
   ```
4. Update as you progress through steps (change "Current Step" column)

### Step 0c: DEPENDENCY CHECK [Antigravity]

1. Read `bug_report_AI/ACTIVE_BUGS.md` → list all other in-progress bugs
2. For each in-progress bug, check the execution plan for file overlap:
   - Does another bug touch the **same file(s)** as this one?
   - Does another bug touch the **same method(s)**?
3. If overlap found → WARN:
   ```
   ⚠️ DEPENDENCY CONFLICT
   Bug {BUG_ID} touches {FILE}:{METHOD}
   Bug {OTHER_BUG_ID} (in-progress) also touches {FILE}:{METHOD}
   Risk: Conflicting fixes may overwrite each other.
   Options:
     1. Fix this bug first, then re-verify the other
     2. Wait for the other bug to complete first
     3. Coordinate with developer working on {OTHER_BUG_ID}
   ```
4. If no overlap → proceed normally

### Step 1: DIAGNOSE [Antigravity]

1. Read the bug entry from the execution plan (`*_BUG_FIX_EXECUTION_PLAN.md`). Extract:
   - Problem summary
   - Root cause
   - Impact assessment
   - Affected file(s) and line number(s)
   - Proposed fix (code)
   - Risk level
   - Rollback plan

2. **Postmortem check** — search `bug_report_AI/POSTMORTEM_LOG.md`:
   - Has AI gotten a similar bug **wrong before**? If yes → follow the "Correct Approach" from the postmortem, NOT the AI's instinct
   - This prevents repeating past mistakes

3. **Diagnostic Playbook check** — search `knowledge_brain/_SYSTEM/DIAGNOSTIC_PLAYBOOK.md`:
   - Find the symptom category that matches (e.g., "Data is Missing", "Wrong Calculation", "Intermittent Failure")
   - Follow the **"SUSPECT FIRST"** order — investigate #1 before #2 before #3
   - Check **"NEVER DO"** — avoid wrong approaches listed for this symptom
   - Check `knowledge_brain/_SYSTEM/DANGER_ZONES.md` — does the fix violate any NEVER rule?

4. **Pattern check** — search `bug_report_AI/COMMON_BUG_PATTERNS.md`:
   - **Match found**: Note Pattern ID → use the fix template as starting point
   - **No match**: Novel bug → use execution plan's proposed fix

5. **Recipe check** — search the central `LOGIMAX-CLIENTS/bug-recipes` repo for a matching fix:
   - **MCP (preferred)**: `mcp_github-mcp-server_search_code(query: "{keyword} repo:LOGIMAX-CLIENTS/bug-recipes path:recipes/")` for keyword matches (module name, table name, symptom)
   - **Git CLI fallback**: `cd {CENTRAL_REPO} && git pull origin main && grep -rn "{keyword}" recipes/`
   - **Match found**: Read the recipe → use its Before/After code and Verification steps as the fix starting point
   - **No match**: Continue with execution plan's proposed fix
   - ⚠️ **This step is MANDATORY** — recipes capture cross-client fixes that patterns don't. Skipping this risks re-inventing a fix that already exists.
   - ⛔ **Do NOT read from local `.agent/skills/bug-fix-engine/recipes/`** — that directory is gitignored and deprecated.

5. If Module Brain exists, cross-reference:
   - Which business rule is violated? (check `BUSINESS_RULES.md`)
   - Which data flow path is affected? (check `DATA_FLOW.md`)
   - Any cross-module dependencies? (check `CROSS_MODULE_MAP.md`)
   - Which tables are touched? (check `METHOD_INDEX.md` reverse map)

6. **System Brain cross-reference** — if `knowledge_brain/_SYSTEM/` exists:
   - Does the fix touch a shared table? → check `_SYSTEM/SHARED_TABLES.md`
   - Does it change `tag_status`? → check `_SYSTEM/TAG_STATUS_MAP.md` for all modules that read/write it
   - Is there a known cleanup gap for this operation? → check `_SYSTEM/CLEANUP_GAPS.md`
   - Any hardcoded values involved? → check `_SYSTEM/HARDCODED_VALUES.md`
   - Any known validation gap? → check `_SYSTEM/VALIDATION_GAPS.md`
   - If `_SYSTEM/` does NOT exist → skip this step (system brain not yet built)

7. **Brain staleness check**:
   - If the affected file has been modified since the brain was last updated → WARN:
     ```
     ⚠️ Module Brain may be stale. {FILE} modified after brain update.
     Brain last updated: {brain_date}. File last modified: {file_date}.
     Proceed with caution — brain data may not reflect current code.
     ```
   - If brain is stale, read the affected function directly instead of relying on brain docs

### Step 1b: CONFIDENCE GATE [Mandatory]

> [!CAUTION]
> **This step is MANDATORY. Never skip it. It prevents wrong diagnoses.**

After completing Step 1 diagnosis, rate your confidence:

🟢 **HIGH (80%+)**: Pattern match found, brain has full coverage, straightforward fix.
   → Proceed to Step 2

🟡 **MEDIUM (50-80%)**: Brain covers the area but root cause is ambiguous.
   → Present TOP 3 possible causes to the developer WITH evidence for each
   → Developer chooses which to investigate
   → Do NOT guess — ask

🔴 **LOW (<50%)**: Novel bug, no pattern match, unfamiliar subsystem.
   → **STOP. Do NOT propose a fix.**
   → Present to developer:
     1. What symptom you see
     2. What areas you checked
     3. What you DON'T know
     4. 2-3 possible causes ranked by likelihood
     5. Request: "Please guide which direction to investigate"

**⛔ Automatic LOW confidence** — if the bug involves ANY of these, confidence is automatically 🔴 regardless of pattern match (see `_SYSTEM/DANGER_ZONES.md`):
- Payment / financial data discrepancy
- Webhook / API integration failure
- Concurrency (intermittent failures)
- Cross-module data flow (3+ modules)
- Status transitions (`tag_status`, `purchase_status`)
- Tax / GST calculations
- Delete / cancel / reverse operations

**When confidence is 🔴 LOW**: Present options. Let the developer choose. Do NOT prescribe a solution.

### Step 2: LOCATE [Antigravity]

1. Open the affected file(s) at the specified line number(s)
2. Read the current code verbatim
3. Confirm the code matches what the execution plan describes
4. If the code has changed since the audit:
   - Does the bug still exist?
   - Has it moved to a different line?
   - Has it been partially fixed by another change?
5. Layer-by-layer trace to confirm scope: `JS → Controller → Model → DB`
6. **Update Issue Status**: If issue exists, update label `status:triaged` → `status:in-progress` via platform dispatch (see `/github-bug-tracking`). Add comment: `🔧 Fix in progress`

### Step 3: ASSESS [Antigravity]

1. **Track classification**:
   - **Track A (System)**: Security, transaction, query, schema, variable, exception, performance → Antigravity fixes autonomously, human approves diff
   - **Track B (Business)**: Calculation, business rule, workflow, integration → Antigravity proposes, human validates business logic BEFORE applying
2. **Risk level**: Low (isolated, no cross-module) / Medium (touches shared code) / High (financial, cross-module, or data integrity)
3. **Cross-module impact**: Check `CROSS_MODULE_MAP.md` — if other modules read/write the affected table or call the affected method, flag the impact
4. If cross-module impact detected → **alert user before proceeding**
5. **System-level impact** — if `knowledge_brain/_SYSTEM/` exists:
   - `_SYSTEM/DATA_FLOW_CHAINS.md` — which end-to-end flow is affected?
   - `_SYSTEM/MODULE_DEPENDENCIES.md` — which other modules depend on the affected module?
   - `_SYSTEM/PERFORMANCE_RISKS.md` — is this a known performance-sensitive area?
   - If `_SYSTEM/` does NOT exist → skip this step

> **Track Routing** (for complex bugs, use the dedicated workflow):
>
> - Track A → `/fix-architecture-bug` — dedicated sub-procedures for transaction, schema, query, concurrency, performance, AJAX, security fixes with alternative approaches analysis and dual approval gates
> - Track B → `/fix-business-bug` — DB Truth Protocol step, layer-by-layer value trace, mandatory business validation BEFORE any code change, variant-aware testing via INVARIANT_MATRIX
> - For simple/straightforward fixes, continue with Steps 4–8 below. For complex fixes, switch to the dedicated workflow.

### Step 4: PLAN & APPLY FIX [Antigravity]

**Before applying any fix, document the rollback plan.**

**For delete/cancel/reverse operations** — if `knowledge_brain/_SYSTEM/CLEANUP_GAPS.md` exists:

- Check the pre-fix checklist at the bottom of CLEANUP_GAPS.md
- Verify the operation restores tag_status (check `_SYSTEM/TAG_STATUS_MAP.md`)
- Verify child records are cleaned up (check known orphan risks)
- If `_SYSTEM/` does NOT exist → use module-level CROSS_MODULE_MAP.md only

#### 4a. Append to Rollback Registry

After applying the fix, add an entry to `bug_report_AI/ROLLBACK_REGISTRY.md`:

```
| {BUG_ID} | {MODULE} | {file(s)} | {rollback steps} | {risk description} | {TODAY} |
```

#### 4b. RIPPLE CHECK [Mandatory — Before Marking Fix Complete]

> [!IMPORTANT]
> **A fix is NOT complete until all downstream consumers are verified.**
> "I fixed column X" is NOT enough. "I fixed X AND verified subtotal, grand total, footer, print" IS enough.

After applying a fix, trace ALL downstream consumers of the changed value:

**For REPORT / DataTable fixes:**
- [ ] Column value correct?
- [ ] Row subtotal uses this column? → Verified?
- [ ] Grand total sums this column? → Verified?
- [ ] Footer callback uses correct column index? (PAT-DT-001)
- [ ] Print/PDF view uses same data source? → Verified?
- [ ] Export (Excel/CSV) uses same data source? → Verified?

**For CALCULATION fixes:**
- [ ] JS calculation correct?
- [ ] PHP calculation matches JS? (both layers!)
- [ ] Display uses the corrected variable?
- [ ] Save sends the corrected value to server?
- [ ] DB column stores correct value?

**For STATUS UPDATE fixes:**
- [ ] Status changed in source table?
- [ ] All modules that READ this status still work? (check `_SYSTEM/SHARED_TABLES.md`)
- [ ] Status log entry created?
- [ ] UI reflects new status?

**For DELETE / CANCEL fixes:**
- [ ] Parent record handled?
- [ ] ALL child records cleaned up? (check `_SYSTEM/CLEANUP_GAPS.md`)
- [ ] Status flags reverted? (check `_SYSTEM/TAG_STATUS_MAP.md`)
- [ ] Quantities restored? (stock, wallet, loyalty)
- [ ] Cross-module references intact?

#### For PHP Controller / Model Fixes:

1. Locate exact file and line from execution plan
2. Read current code — confirm it matches the plan
3. If pattern match exists → adapt fix template from `COMMON_BUG_PATTERNS.md`
4. If novel bug → use execution plan's proposed fix
5. Apply the **minimal** fix — no additional refactoring:
   - Single contiguous change → `replace_file_content`
   - Multiple non-adjacent changes → `multi_replace_file_content`

#### For DB Schema Fixes:

> [!CAUTION]
> **MANDATORY: Database backup before any schema change.**

1. Generate the `ALTER TABLE` statement
2. Write a matching rollback SQL statement
3. Flag: "Test on staging/dev database FIRST"
4. Provide the exact SQL for the user to execute
5. After execution → verify existing data preserved

#### For JavaScript Fixes:

> [!WARNING]
> JS files are 30K+ lines. Surgical precision is critical.

1. Locate exact function name and line
2. Verify fix doesn't affect counterpart functions (e.g., `get_tag_data` vs `get_tag_barcode_data`)
3. Apply minimal fix — prefer patterns already proven in the same file
4. Flag for manual testing: "Clear browser cache and test BOTH Add and Edit paths"

**JS-Specific Risks:**

- Variable name mismatches between similar functions = #1 copy-paste bug
- All financial calculations happen client-side — server-side re-validation is minimal
- Changes must work for both Add and Edit modes (shared JS)

#### For View / Template Fixes:

1. Locate the template file and line
2. Apply fix (e.g., `htmlspecialchars()` for XSS, remove duplicate tags)
3. Flag for testing both Add and Edit modes
4. If affects print templates → follow the **Print/View Layout Fixes** procedure below

#### For Print/View Layout Fixes (CSS/Column/Image changes):

> [!IMPORTANT]
> **Print layout bugs are the #1 cause of fix loops.** Follow this procedure exactly to avoid CSS trial-and-error.

1. **Find a working reference FIRST**:
   - Search for a WORKING print of the same type (e.g., packing list print when fixing stock issue print)
   - Open both view files side-by-side
   - Note: CSS class names, column widths, dashed-line widths, image rendering method

2. **Copy, don't invent**:
   - Copy exact CSS values from the working reference
   - For dashed-line widths: `column_count × base_width + buffer` (e.g., 9 columns → `width: 1800%`)
   - For image cells: always use `max-width`, `max-height`, `overflow:hidden` together
   - For base64 images: copy the exact `file_get_contents()` + `base64_encode()` pattern from the reference

3. **One CSS change at a time**: Don't fix column width + image overflow + dashed line in one edit. Fix one, verify, then next.

4. **Browser verification (MANDATORY)**:
   - Use browser subagent to navigate to the print URL
   - Capture screenshot AFTER each CSS edit
   - Compare with expected layout
   - If layout differs → adjust ONE CSS property, re-verify
   - Only move to next fix after current one is visually confirmed

### Step 5: SYNTAX CHECK [Antigravity]

// turbo

1. For PHP files:

```powershell
& "{PHP_PATH}" -l {affected_php_file}
```

2. If syntax check fails → fix the syntax error → re-run
3. For JS files → flag for manual browser console check
4. For SQL → validate syntax but do NOT execute

### Step 6: TEST [Antigravity]

Run tests per the testing workflow (`/test-and-verify`):

1. Check if a relevant test file exists in `{TEST_DIR}`
2. If exists → run it:
   // turbo

```powershell
cd "{PROJECT_ROOT}\{TEST_DIR}" && & "{PHP_PATH}" vendor/bin/phpunit --no-configuration {TestFile}.php --testdox
```

3. If no test exists and P0/P1 bug → create one:
   - File: `{TEST_DIR}{ModuleName}{BugCategory}Test.php`
   - Test cases based on bug category (see `/test-and-verify` for category-specific requirements)
4. Run and confirm all assertions pass
5. Present smoke test checklist:
   - [ ] Primary **create/save** action works
   - [ ] Primary **edit/update** preserves all data
   - [ ] Primary **delete** cleans up child records
   - [ ] **Search** returns correct results
   - [ ] **Print/export** matches DB values
   - [ ] **Negative test** — malicious input rejected

### Step 7: APPROVAL GATE [Human]

Present to user for approval:

```
Bug: {BUG_ID} — {Title}
Track: {A (System) / B (Business)}
Risk: {Low / Medium / High}
Pattern: {PAT-XXX / Novel}

Changed:
  {file}:{lines} — {1-line description}

Test Results:
  {N} tests, {M} assertions — all passed

Rollback:
  {1-line rollback instruction}

Approve? (yes / modify / reject)
```

**Approval rules:**

- **Track A (System)**: Human reviews the diff → Approve/Reject
- **Track B (Business)**: Human validates the business logic → Approve/Reject
- **P0 Hotfix**: Verbal approval is sufficient for immediate deploy

**Responses:**

- **Approved** → Continue to Step 8
- **Modify** → Apply requested changes → re-run Steps 5–6 → return to Step 7
- **Reject** → Document reason → mark bug "Deferred" → stop

### Step 8: CLOSE & UPDATE [Antigravity]

1. **Bug tracking**: Mark bug as ✅ Fixed with date in consolidated report and execution plan
2. **Pattern library**: Update `COMMON_BUG_PATTERNS.md`:
   - Existing pattern match → add module to "Modules Found In" if not already there
   - New pattern → add full entry (ID, description, detection rule, fix template)
3. **Recipe creation & push** ⛔ **NON-NEGOTIABLE — triggers IMMEDIATELY after Step 7 approval passes**:
   - Create recipe file following `bug-recipes/recipes/_TEMPLATE.md` format
   - Include: Metadata, Symptom, Root Cause, Detection command, Before/After code, Verification steps
   - Push directly to central `LOGIMAX-CLIENTS/bug-recipes` repo via `/push-recipe` workflow
   - If git push hangs/fails → immediately fallback to GitHub MCP `create_or_update_file`
   - ⛔ **Do NOT save to local `.agent/skills/bug-fix-engine/recipes/`** — central repo is the sole destination
   - **Trigger**: Right after approval — do NOT wait for "Verified working" or user prompt
   - **Gate**: Completion Report CANNOT be generated until recipe is confirmed on central repo
4. **Module Brain**: Add anti-pattern note to `MODULE_BRAIN.md`:
   - What was broken
   - How it was fixed
   - "Why it was done this way" note
5. **Walkthrough**: Generate fix documentation:
   - Bug ID + title
   - File(s) changed with line numbers
   - Before/after code diff
   - Test results
   - Date fixed
6. **Communication**: Notify reporter — "Bug {ID} fixed. Pending deployment."

## Completion Report

```
✅ Bug {BUG_ID} — {TITLE}
   Track: {A/B}
   Fix: {1-line summary of change}
   File: {path}:{lines}
   Tests: {N} tests, {M} assertions — all passed
   Risk: {Low/Medium/High}
   Pattern: {PAT-XXX matched / NEW pattern added / N/A}
   Rollback: {1-line rollback instruction}
   Next bug: {NEXT_BUG_ID} — {NEXT_TITLE}
```