---
description: Intake a new bug report, classify severity/track/category, assign sprint, and prepare for fix workflow
version: 2.0
last_updated: 2026-03-16
---

# Bug Intake & Triage Workflow

## Purpose

Standardize how bugs enter the pipeline — from raw report to triaged, ID'd entry ready for `/fix-single-bug`. This is the **entry point** for ALL bugs regardless of source.

## Prerequisites

- Bug has been reported (client ticket, internal QA, or AI audit)
- Module name is known
- `bug_report_AI/COMMON_BUG_PATTERNS.md` exists

## Input

- `{BUG_SOURCE}` — Client / Support / QA / AI Audit
- `{MODULE_NAME}` — e.g., Estimation, Billing, Sales
- Raw bug description from reporter

## Steps

### Step 0: Environment Pre-Check [Antigravity]

// turbo
Before any triage work, verify critical tools are available:

1. **PHP CLI**: Run `& "{PHP_PATH}" -v` — if fails, auto-detect (see `/test-and-verify` Step 0a)
2. **Issue Platform**: Run Pre-Flight from `/github-bug-tracking` (auto-detects platform, checks token)
   - ✅ Pass → GitHub issue creation will work in Step 11
   - ❌ Fail → WARN user: "GitHub MCP not connected. Issue creation will be skipped. Fix connection and create issue manually or in next conversation."
   - Record MCP status: `Available` / `Unavailable`
3. **Config check**: Verify `.agent/config.md` `{PROJECT_ROOT}` matches current workspace
   - If mismatch → fix config FIRST (AGENT-007)

### Step 1: Collect Bug Report Fields

// turbo
Gather or extract these **required fields** from the reporter:

| Field                  | Description                                            | Required |
| ---------------------- | ------------------------------------------------------ | -------- |
| **Reporter**           | Who found it                                           | ✅       |
| **Module**             | Which module                                           | ✅       |
| **Sub-Module**         | Specific feature (e.g., Add Estimation, Print Invoice) | ✅       |
| **Steps to Reproduce** | Exact numbered steps                                   | ✅       |
| **Expected Behavior**  | What should happen                                     | ✅       |
| **Actual Behavior**    | What actually happens                                  | ✅       |
| **Evidence**           | Screenshot, video, or error log                        | ✅       |
| **Environment**        | Production / Staging / Dev + browser                   | ✅       |
| **Frequency**          | Always / Sometimes / Once                              | ✅       |

If any required field is missing, ask the reporter before proceeding.

### Step 2: Assign Severity

Classify using these criteria — pick the **highest applicable** level:

| Level             | Criteria                                                          | Target Fix Time   |
| ----------------- | ----------------------------------------------------------------- | ----------------- |
| **P0 — Critical** | Data loss, security breach, financial miscalculation, system down | Same day (Hotfix) |
| **P1 — Major**    | Wrong data display, broken business rules, orphan records         | ≤ 3 days          |
| **P2 — Minor**    | UX issues, missing validations, code quality, edge cases          | Next sprint       |
| **P3 — Cosmetic** | Debug artifacts, styling, dead code                               | Backlog           |

**Decision rule**: If the bug causes **money to be wrong** or **data to be lost**, it's P0. If it shows **wrong info** to the user, it's P1.

### Step 3: Assign Track

Determine who leads the fix:

| Track                             | When                                                                        | Antigravity Role | Human Role              |
| --------------------------------- | --------------------------------------------------------------------------- | ---------------- | ----------------------- |
| **Track A — System/Architecture** | Security, transaction, query, schema, variable, exception, performance bugs | Full fix + test  | Approve diff            |
| **Track B — Business**            | Calculation errors, missing rules, workflow bugs, integration issues        | Propose fix      | Validate business logic |

**Decision rule**: If the code runs but produces **wrong business results** → Track B. Everything else → Track A.

### Step 4: Assign Category

Pick ONE primary category:

| #   | Category               | Signals                                                            |
| --- | ---------------------- | ------------------------------------------------------------------ |
| 1   | **Logic**              | Wrong calculations, missing business rules                         |
| 2   | **Data Validation**    | No null/range/type checks, client-only validation                  |
| 3   | **Database**           | SQL injection, missing indexes, orphan records, wrong column types |
| 4   | **Exception Handling** | Missing try-catch, `trans_commit()` on failure                     |
| 5   | **Security**           | CSRF, XSS, missing sanitization, `mkdir 0777`                      |
| 6   | **Performance**        | N+1 queries, no pagination, heavy queries                          |
| 7   | **Integration**        | AJAX error handling, cross-module data handshake                   |
| 8   | **Concurrency**        | Race conditions, no locking, duplicate submissions                 |

### Step 5: Generate Bug ID

Format: `{PREFIX}-{SOURCE}{SEQ}`

| Module      | Prefix |
| ----------- | ------ |
| Estimation  | EST    |
| Billing     | BIL    |
| Inventory   | INV    |
| Sales       | SAL    |
| Purchase    | PUR    |
| LOT         | LOT    |
| CRM/Schemes | CRM    |
| Reports     | RPT    |
| Tagging     | TAG    |
| Order       | ORD    |

### Step 5b: Duplicate Bug Check [Antigravity]

Before creating the bug entry, check for duplicates:

1. **Search local audit files** — grep existing `CONSOLIDATED_BUG_REPORT.md` for similar keywords:
   - Same file + same method → likely duplicate
   - Same error description → likely duplicate
2. **Search Issues** — use platform dispatch from `/github-bug-tracking`:
   ```
   Query: "{MODULE_NAME} {key_symptom}" label:module:{MODULE_NAME}
   ```
3. **If potential duplicate found**:
   - Show the existing bug entry/issue to the developer
   - Options: **Link** (mark as duplicate of existing) / **Create Anyway** (different root cause) / **Merge** (add detail to existing)
4. **If no duplicate found** → proceed to Step 6

Source codes: `R1`–`R6` (audit rounds), `PAT` (pattern match), `CLT` (client), `QA` (QA), `INT` (internal).

Example: `EST-CLT01`, `BIL-R301`, `SAL-PAT-SEC-001`

### Step 6: Quick Pattern Check

// turbo

1. Read `bug_report_AI/COMMON_BUG_PATTERNS.md`
2. Does the reported behavior match any known pattern?
   - **Match found**: Note the Pattern ID → this accelerates the fix (fix template already exists)
   - **No match**: Novel bug — will need full diagnosis in `/fix-single-bug`

### Step 6b: DANGER ZONE CHECK [Antigravity]

// turbo

1. Read `knowledge_brain/_SYSTEM/DANGER_ZONES.md` (if exists)
2. Read `knowledge_brain/_SYSTEM/DIAGNOSTIC_PLAYBOOK.md` (if exists)
3. Does this bug involve ANY of these danger zones?
   - [ ] Payment / financial data discrepancy
   - [ ] Webhook / API integration failure
   - [ ] Concurrency (works sometimes, fails sometimes)
   - [ ] Cross-module data flow (3+ modules involved)
   - [ ] Status transitions (`tag_status`, `purchase_status`, `orderstatus`)
   - [ ] Tax / GST calculations
   - [ ] Delete / cancel / reverse operations
4. If **YES** to any checkbox → add flag to bug entry:
   ```
   ⛔ DANGER ZONE: {area}
   → AI confidence: automatically LOW
   → Require senior developer review before applying any fix
   → Do NOT accept AI's first suggestion without verification
   ```
5. Check `bug_report_AI/POSTMORTEM_LOG.md` — has AI gotten a similar bug wrong before?
   - If **YES** → add warning: "⚠️ AI previously misdiagnosed a similar bug (POST-{NNN}). Follow the corrected approach."
6. If `DANGER_ZONES.md` does NOT exist → skip this step

### Step 7: Assign Sprint

| Sprint   | Bugs Assigned                   | Timeline           |
| -------- | ------------------------------- | ------------------ |
| Sprint 1 | All P0 + critical P1            | Week 1 (Immediate) |
| Sprint 2 | Remaining P1 + high-priority P2 | Week 2             |
| Sprint 3 | Remaining P2 + all P3           | Backlog            |

### Step 8: Check Module Brain Readiness

// turbo

1. Check if `knowledge_brain/{MODULE_NAME}/MODULE_BRAIN.md` exists
2. If **NO**: Flag → "Module Brain required before fix. Run `/build-module-brain` first."
3. If **YES**: Ready for `/fix-single-bug`

### Step 8b: Cross-Module Impact Pre-Check [Antigravity]

// turbo

1. Check if `knowledge_brain/_SYSTEM/` exists
2. If **YES**:
   - Search `_SYSTEM/SHARED_TABLES.md` for tables mentioned in the bug report
   - If shared table found → flag: "⚠️ Cross-module risk — check `TAG_STATUS_MAP.md` and `MODULE_DEPENDENCIES.md` before fixing"
   - Search `_SYSTEM/CROSS_MODULE_BUGS.md` for related active bugs
   - Search `_SYSTEM/CLEANUP_GAPS.md` if the bug involves delete/cancel/reverse operations
3. If **NO** → Skip (system brain not yet built — workflow continues normally)

### Step 9: Create Bug Entry

Write the triaged bug entry to the module's bug report directory:

**File**: `bug_report_AI/{module_name}/BUG_{BUG_ID}.md` (or append to existing consolidated report)

```markdown
## {BUG_ID} — {Short Title}

| Field         | Value                         |
| ------------- | ----------------------------- |
| Severity      | P{N}                          |
| Track         | A (System) / B (Business)     |
| Category      | {Category}                    |
| Sprint        | {Sprint N}                    |
| Pattern Match | {PAT-XXX / None}              |
| Module Brain  | ✅ Ready / ❌ Needs build     |
| Reporter      | {Name}                        |
| Source        | {Client/QA/Internal/AI Audit} |

### Steps to Reproduce

1. ...

### Expected Behavior

...

### Actual Behavior

...

### Evidence

{Screenshot/log/video link}
```

### Step 10: Send Acknowledgement

Notify the reporter per SLA:

| Source              | SLA              | Message Template                                     |
| ------------------- | ---------------- | ---------------------------------------------------- |
| Client (production) | ≤ 2 hours        | "Bug {ID} received. Severity: P{N}. Target: {date}." |
| Support (internal)  | ≤ 4 hours        | "Bug {ID} received. Severity: P{N}. Target: {date}." |
| QA/Tester           | ≤ 1 business day | "Bug {ID} received. Severity: P{N}. Sprint: {N}."    |
| AI Audit            | Same sprint      | Auto-tracked in consolidated report                  |

## Completion Report

```
✅ Bug {BUG_ID} triaged
   Severity: P{N}
   Track: {A/B}
   Category: {Category}
   Sprint: {Sprint N}
   Pattern: {PAT-XXX / Novel}
   Brain: {Ready / Needs build}
   Next: /fix-single-bug {BUG_ID}
```

### Step 11: Create GitHub Issue [Antigravity]

> See `/github-bug-tracking` Part 2 for full details.

1. Get current developer username via platform dispatch → `Get user` (see `/github-bug-tracking`)
2. Create issue via platform dispatch → `Create issue` (see `/github-bug-tracking`):
   - **Title**: `[{BUG_ID}] {Bug Title}`
   - **Labels**: `{severity}`, `{track}`, `{category}`, `module:{MODULE}`, `status:triaged`
   - **Assignee**: current developer (from `get_me`)
   - **Body**: Bug details (description, location, current/expected behavior, evidence)
3. Record the GitHub Issue number in the local execution plan: `GitHub: #{N}`

## Output

- Bug entry in `bug_report_AI/{module_name}/`
- GitHub Issue created: `#{N}` with labels and assignee
- Acknowledgement sent to reporter
- Ready for: `/fix-single-bug {BUG_ID}`
