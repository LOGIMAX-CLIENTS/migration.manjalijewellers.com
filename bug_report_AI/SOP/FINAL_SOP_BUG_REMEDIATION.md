# 📋 FINAL SOP — Antigravity-Driven Bug Remediation System

> **Document ID**: SOP-GLOBAL-2026-001
> **Version**: 1.0
> **Date**: 2026-02-18
> **Author**: Logimax Engineering Team
> **Scope**: All Modules — Module-Agnostic Template
> **Primary Tool**: Antigravity (AI Coding Assistant)
> **Status**: Active

---

## 1. Purpose & Objective

This SOP defines the **complete lifecycle** for finding, classifying, fixing, testing, and deploying bug fixes across **all modules** of the Retail ERP system, with **Antigravity as the primary executor**.

**Primary Objective**: Fix bugs ASAP with maximum accuracy, minimum regression, and full traceability.

**What Antigravity Does** (automated):
- Builds Module Brains (reverse-engineering, data flow mapping)
- Runs 6-round static analysis audits
- Cross-references bugs against known pattern library
- Applies surgical code fixes with rollback plans
- Writes and runs unit tests
- Generates documentation and walkthroughs

**What Humans Do** (manual):
- Triage incoming bugs and assign severity/track
- Approve fixes before deployment (approval gate)
- Validate business-logic correctness for business-level bugs
- Perform final smoke tests on staging/production
- Communicate status to reporters

---

## 2. Bug Classification: Two Tracks

> [!IMPORTANT]
> Every bug falls into one of two tracks. The track determines who leads the fix.

### Track A: System / Architecture Level

Bugs caused by **code defects** independent of business rules. Antigravity can fix these **almost autonomously** — human just approves the diff.

| Category | Examples | Antigravity Role |
|---|---|---|
| **Security** | SQL injection, XSS, CSRF, `mkdir 0777`, raw `$_POST` | Full fix + test |
| **Transaction Handling** | `trans_commit()` on failure, missing rollback | Full fix + test |
| **Query Logic** | Cartesian JOINs, missing GROUP BY, N+1 queries | Full fix + test |
| **Schema Defects** | Missing PK, wrong column types, MyISAM on transactional tables | Generate ALTER TABLE + test |
| **Variable/Copy-Paste** | Typos, undefined variables, wrong index | Full fix + test |
| **Exception Handling** | Missing try-catch, silent failures | Full fix + test |
| **Concurrency** | Race conditions, no locking | Propose fix, human validates |
| **Performance** | Missing indexes, no pagination, heavy queries | Full fix + test |

### Track B: Business Level

Bugs where the **code works but produces wrong business results**. Antigravity proposes the fix, but a **human must validate the business logic**.

| Category | Examples | Antigravity Role |
|---|---|---|
| **Calculation Errors** | Wrong formula, wrong variable in calculation chain | Propose fix + human validates the formula |
| **Missing Business Rules** | Validation gaps, missing constraints | Identify + propose, human confirms rule |
| **Incorrect Workflows** | Wrong status transitions, missing approval steps | Propose fix, human confirms workflow |
| **Data Integrity** | Orphan records, duplicate data, missing cascades | Full fix after human confirms table relationships |
| **Integration Bugs** | Wrong data handshake between modules | Propose fix, human validates cross-module impact |

---

## 3. Phase 0 — Build Module Brain (Prerequisite)

> [!IMPORTANT]
> Before fixing bugs in ANY new module, Antigravity must first build a **Module Brain**. This is non-negotiable — it reduces diagnosis time by ~60%.

**Invoke**: Tell Antigravity "Build a brain for {MODULE_NAME}" or use `/build-module-brain`

### What the Brain Contains

| Component | Source | Deliverable |
|---|---|---|
| **Project Skeleton** | SOP 2 — Component 1 | File map with every file's role and connections |
| **Engine Reverse Engineering** | SOP 2 — Component 2 | End-to-end data flow: Browser → JS → Controller → Model → DB → View |
| **Business Rules** | SOP 2 — Component 3 | Every calculation/constraint as plain-English rules with formulas |
| **Cross-Module Mapping** | SOP 2 — Component 4 | What data comes in/goes out to other modules |
| **Entry Points & Routes** | SOP 4 — Section 2 | Table of URLs, HTTP methods, controller methods, views, JS |
| **Field-Level Spec** | SOP 4 — Section 5 | Every field: source, type, mandatory, validation, calculation dependencies |
| **DB Truth Protocol** | SOP 2 — Component 6 | Verification queries to check DB vs screen vs print |

### Brain Storage

```
knowledge/{MODULE_NAME}/
├── MODULE_BRAIN.md           ← The brain document
├── DATA_FLOW.md              ← Detailed data flow traces
├── BUSINESS_RULES.md         ← Extracted business rules
└── CROSS_MODULE_MAP.md       ← Dependency mapping
```

### When to Skip the Brain

**Never for the first audit of a module.** For hotfix-only situations on already-brained modules, you can skip directly to Phase 3.

---

## 4. Phase 1 — Bug Discovery (6-Round AI Audit)

**Invoke**: Tell Antigravity "Run a module bug audit for {MODULE_NAME}" or use `/module-bug-audit`

### Pre-Scan: Round 0 — Pattern Cross-Reference (NEW)

Before the 6 deep-dive rounds, Antigravity runs a quick grep/search against the **Common Bug Patterns Library** (`bug_report_AI/COMMON_BUG_PATTERNS.md`). This instantly catches known recurring patterns without deep analysis.

```
Round 0 Output:
├── Pattern matches found: N
├── Confirmed bugs (from patterns): list
└── Remaining: proceed to Rounds 1-6 for novel bugs
```

### 6-Round Deep Analysis

| Round | Focus | Key Files | Bug ID Format |
|---|---|---|---|
| **Round 1** | Controller code-level | `{CONTROLLER_FILE}` | `{PREFIX}-NNN` |
| **Round 2** | DB Schema cross-reference | `{DB_SCHEMA_FILE}` + all module tables | `{PREFIX}-SNN` |
| **Round 3** | Model & data-flow deep dive | `{MODEL_FILE}` | `{PREFIX}-R3NN` |
| **Round 4** | JS save handler & view layer | `{JS_FILE}` + `{VIEW_DIR}` | `{PREFIX}-R4NN` |
| **Round 5** | JS calculations & data binding | `{JS_FILE}` (calc functions) | `{PREFIX}-R5NN` |
| **Round 6** | Validation functions & AJAX endpoints | Both JS + Controller | `{PREFIX}-R6NN` |

### Post-Scan: Pattern Library Update

After all rounds, Antigravity compares new bugs against existing patterns:
- **Existing pattern found in new module** → Add module to pattern's "Modules Found In" list
- **Genuinely new pattern** → Add to `COMMON_BUG_PATTERNS.md` with detection rule and fix template

### Output Artifacts

```
bug_report_AI/{module_name}/
├── CONSOLIDATED_BUG_REPORT.md          ← Master index of all bugs
├── {MODULE}_BUG_FIX_EXECUTION_PLAN.md  ← Per-bug detailed fix plan
├── ROUND_2_SCHEMA_ANALYSIS.md
├── ROUND_3_DEEP_DIVE.md
├── ROUND_4_JS_VIEW_DEEP_DIVE.md
├── ROUND_5_JS_CALCULATIONS_DEEP_DIVE.md
├── ROUND_6_VALIDATION_AJAX_DEEP_DIVE.md
├── KB_GAP_ANALYSIS.md
├── {sub_module}/BUG_REPORT_SUMMARY.md  ← Per sub-module
└── {sub_module}/bugs_detailed.json     ← Machine-readable
```

---

## 5. Phase 2 — Triage & Classification

### 5.1 Severity Levels

| Level | Criteria | Target Fix Time |
|---|---|---|
| **P0 — Critical** | Data loss, security breach, financial miscalculation, system down | Same day (Hotfix Track) |
| **P1 — Major** | Wrong data display, broken business rules, orphan records | ≤ 3 days |
| **P2 — Minor** | UX issues, missing validations, code quality, edge cases | Next sprint |
| **P3 — Cosmetic** | Debug artifacts, styling, dead code | Backlog |

### 5.2 Bug Categories (8 Standard Categories)

| # | Category | Examples |
|---|---|---|
| 1 | **Logic** | Wrong calculations, missing business rules |
| 2 | **Data Validation** | No null/range/type checks, client-only validation |
| 3 | **Database** | SQL injection, missing indexes, orphan records, wrong column types |
| 4 | **Exception Handling** | Missing try-catch, `trans_commit()` on failure |
| 5 | **Security** | CSRF, XSS, missing sanitization, `mkdir 0777` |
| 6 | **Performance** | N+1 queries, no pagination, heavy queries on web request |
| 7 | **Integration** | AJAX error handling, cross-module data handshake failures |
| 8 | **Concurrency** | Race conditions, no row locking, duplicate submissions |

### 5.3 Bug ID Format

`{PREFIX}-{ROUND}{SEQ}` — e.g., `EST-R301`, `BIL-S02`, `LOT-R501`

| Prefix | Module |
|---|---|
| EST | Estimation |
| BIL | Billing |
| INV | Inventory |
| SAL | Sales |
| PUR | Purchase |
| LOT | LOT |
| CRM | CRM/Schemes |
| RPT | Reports |

### 5.4 Sprint Assignment

| Sprint | Priority | Timeline |
|---|---|---|
| Sprint 1 | All P0 + critical P1 | Week 1 (Immediate) |
| Sprint 2 | Remaining P1 + high-priority P2 | Week 2 |
| Sprint 3 | Remaining P2 + all P3 | Backlog |

---

## 6. Phase 3 — Bug Fix Execution (Antigravity-Driven)

### 6.1 Core Principles

> **ONE bug at a time. No batch fixes. Stability > Elegance. Documentation first.**

1. **Isolation** — Each bug is fixed in its own change, linked to a specific Bug ID
2. **Traceability** — Every change references the Bug ID
3. **Reversibility** — Every fix has a documented rollback plan
4. **Surgical precision** — Minimal fix footprint, no opportunistic refactoring
5. **Pattern reuse** — Always check `COMMON_BUG_PATTERNS.md` before writing a fix from scratch

### 6.2 Invoke

Tell Antigravity: "Fix bug {BUG_ID}" or use `/fix-single-bug`

### 6.3 The 8-Step Antigravity Workflow

```
┌──────────────────────────────────────────────────────────────────┐
│                                                                  │
│  STEP 1: DIAGNOSE                                    [Antigravity] │
│  ├─ Read bug details from execution plan                         │
│  ├─ Cross-reference COMMON_BUG_PATTERNS.md                       │
│  ├─ Check Module Brain for context                               │
│  └─ Identify which business/canonical rule is violated           │
│                                                                  │
│  STEP 2: LOCATE                                      [Antigravity] │
│  ├─ Find exact file + line number                                │
│  ├─ Layer-by-layer trace: JS → Controller → Model → DB          │
│  └─ Read current code verbatim and confirm it matches the plan   │
│                                                                  │
│  STEP 3: ASSESS                                      [Antigravity] │
│  ├─ Impact: security / data / financial / UX?                    │
│  ├─ Risk level: Low / Medium / High                              │
│  ├─ Cross-module check (from Brain's Cross-Module Map)           │
│  └─ Track A (system) or Track B (business)?                      │
│                                                                  │
│  STEP 4: PLAN & APPLY FIX                            [Antigravity] │
│  ├─ Write the proposed minimal fix                               │
│  ├─ If pattern exists: use fix template from patterns library    │
│  ├─ Document the rollback step                                   │
│  └─ Apply the code change using edit tools                       │
│                                                                  │
│  STEP 5: SYNTAX CHECK                                [Antigravity] │
│  ├─ php -l {file} for PHP files                                  │
│  └─ Browser console check for JS files                           │
│                                                                  │
│  STEP 6: TEST                                        [Antigravity] │
│  ├─ Create/run unit tests (PHPUnit)                              │
│  ├─ Run module-specific smoke test checklist                     │
│  └─ Negative testing for security bugs                           │
│                                                                  │
│  STEP 7: APPROVAL GATE                              [Human]       │
│  ├─ For Track A (system): Review the diff → Approve/Reject       │
│  ├─ For Track B (business): Validate business logic → Approve    │
│  └─ For P0 Hotfix: Verbal approval is sufficient                 │
│                                                                  │
│  STEP 8: CLOSE & UPDATE                              [Antigravity] │
│  ├─ Update Module Brain with anti-pattern + "why" notes          │
│  ├─ Update COMMON_BUG_PATTERNS.md if new pattern                 │
│  ├─ Update progress tracking in SOP/execution plan               │
│  └─ Generate walkthrough documenting the fix                     │
│                                                                  │
└──────────────────────────────────────────────────────────────────┘
```

### 6.4 Fix Procedures by Code Layer

#### PHP Controller / Model Fixes

1. Locate exact file and line from execution plan
2. Read current code — confirm it matches the plan
3. Apply minimal fix — no additional refactoring
4. Run: `& "C:\php8\php.exe" -l {filename}`
5. Run PHPUnit: `& "C:\php8\php.exe" vendor/bin/phpunit --no-configuration {TestFile}.php --testdox`

#### DB Schema Fixes

> [!CAUTION]
> **MANDATORY: Database backup before any schema change.**

1. Generate `ALTER TABLE` statement
2. Test on staging/dev database first
3. Execute during maintenance window
4. Verify existing data preserved after migration
5. Confirm application functionality post-migration

#### JavaScript Fixes

1. Locate exact line and function name
2. Verify fix doesn't affect counterpart functions (e.g., `get_tag_data` vs `get_tag_barcode_data`)
3. Apply minimal fix — prefer patterns already proven in the same file
4. Clear browser cache and test
5. Test BOTH Add and Edit paths (JS is shared)

**JS-Specific Risks**:
- JS files are 30K+ lines — changes must be surgically precise
- Variable name mismatches between similar functions are the #1 copy-paste bug
- All financial calculations happen client-side — server-side re-validation is minimal

#### View / Template Fixes

1. Locate the template file and line
2. Apply fix (e.g., `htmlspecialchars()` for XSS, remove duplicate tags)
3. Test both Add and Edit modes
4. Verify print/PDF output if applicable

---

## 7. Phase 4 — Testing Standards

### 7.1 Required Tests by Bug Category

| Category | Required Automated Tests |
|---|---|
| **Security** (SQLi, XSS) | Whitelist enforcement, malicious payload blocking, binding verification |
| **Transaction** | Success path commits, failure path rollbacks, partial failure handling |
| **Logic / Calculation** | Correct result for normal input, edge cases (0, negative, very large), boundary values |
| **Data Validation** | Valid passes, invalid blocked, boundary conditions, null handling |
| **Schema** | Data preservation post-migration, type correctness, constraint enforcement |
| **Variable/Typo** | Correct value assigned, old behavior no longer occurs |
| **Integration** | Mock external responses, timeout handling, error response handling |

### 7.2 Module Smoke Test Checklist Template

After each fix, test these (customize per module):

- [ ] Primary **create/save** action works
- [ ] Primary **edit/update** action works and preserves all data
- [ ] Primary **delete** action cleans up all child records
- [ ] **Search** functions return correct results (tag, customer, etc.)
- [ ] **Print/export** output matches database values
- [ ] **Negative test** — insert malicious input via browser dev tools → verify rejection

### 7.3 Test Environment

```
PHP:     8.5.0 (CLI) at C:\php8\php.exe
PHPUnit: 12.5.8 via Composer
Tests:   admin/tests/
Run:     cd admin/tests && & "C:\php8\php.exe" vendor/bin/phpunit --no-configuration {TestFile}.php --testdox
```

### 7.4 Sign-Off Criteria (per bug)

| Criterion | Required |
|---|---|
| Syntax check passes | ✅ |
| Automated tests pass | ✅ |
| Smoke tests pass | ✅ |
| No unintended side effects | ✅ |
| Rollback plan documented | ✅ |
| Bug tracking updated | ✅ |

---

## 8. Phase 5 — Deployment

### 8.1 Scheduled (Sprint End)

1. All targeted fixes pass sign-off criteria
2. Full database backup
3. Git tag: `release-{MODULE}-{date}-sprint{N}`
4. Deploy to staging → smoke test → deploy to production
5. Notify stakeholders

### 8.2 Hotfix (Same Day — P0 Only)

1. Fix passes sign-off criteria
2. Database backup (if schema change)
3. Git tag: `hotfix-{BUG_ID}-{date}`
4. Deploy directly to production
5. Monitor for 2 hours
6. Backfill documentation within 24 hours

### 8.3 Post-Deployment Monitoring (24 Hours)

- [ ] No new PHP errors in error logs
- [ ] No MySQL warnings in slow query log
- [ ] Core module functions work correctly
- [ ] No user-reported regressions
- [ ] Database integrity checks pass (no new orphan records)

### 8.4 Rollback Procedure

1. Identify the Bug ID that caused the regression
2. Refer to the rollback plan in the Execution Plan (Section 10 of each bug)
3. Revert the specific Git commit or restore the backup file
4. For schema changes: execute the documented rollback SQL
5. Verify system returns to pre-fix state
6. Document the rollback reason and re-assess the fix approach

---

## 9. Bug Intake — How Bugs Enter the Pipeline

### 9.1 Bug Sources

| Source | Entry Method | SLA: Acknowledge |
|---|---|---|
| **Client** (production) | Support ticket / phone → Bug Report | ≤ 2 hours |
| **Support person** | Internal ticket → Bug Report | ≤ 4 hours |
| **Tester** (QA) | Test report → Bug Report | ≤ 1 business day |
| **AI Audit** (Phase 1) | Automated audit report → Bulk import | Same sprint |

### 9.2 Bug Report Required Fields

| Field | Description |
|---|---|
| **Reporter** | Who found it |
| **Module** | Which module |
| **Sub-Module** | Specific feature area |
| **Steps to Reproduce** | Exact numbered steps |
| **Expected Behavior** | What should happen |
| **Actual Behavior** | What actually happens |
| **Evidence** | Screenshot, video, or error log |
| **Environment** | Production / Staging / Dev + browser |
| **Frequency** | Always / Sometimes / Once |

### 9.3 Communication Protocol

| Event | Notify |
|---|---|
| Bug acknowledged | Reporter — "Bug {ID} received. Severity: {P}. Target: {date}." |
| Fix in progress | Reporter — "Bug {ID} being fixed by {dev}." |
| Fix deployed to staging | Reporter + QA — "Please verify." |
| Fix deployed to production | Reporter + stakeholders — "Fixed and deployed." |
| Rollback executed | Reporter + management — "Rolled back. Reason: {reason}." |

---

## 10. Metrics & Continuous Improvement

### 10.1 Track These

| Metric | Target |
|---|---|
| Time to Acknowledge | ≤ 2h (client), ≤ 4h (internal) |
| Time to Fix (P0) | Same day |
| Time to Fix (P1) | ≤ 3 business days |
| First-Fix Success Rate | ≥ 90% (no regressions) |
| Bugs per Module per Month | Trending down |
| Pattern Library Reuse Rate | Trending up |

### 10.2 Monthly Review

- Review metrics with the team
- Identify top 3 buggiest modules → prioritize Brain building
- Identify recurring patterns → add to `COMMON_BUG_PATTERNS.md`
- Update this SOP if process gaps are found

---

## 11. Roles & Responsibilities

| Role | Key Responsibilities |
|---|---|
| **Antigravity** | Builds brains, runs audits, applies fixes, writes tests, updates docs |
| **Developer (Human)** | Validates business logic, approves fixes (Step 7), performs smoke tests |
| **Bug Triage Lead** (weekly rotation) | Reviews incoming bugs, assigns severity/category/track/developer |
| **QA Tester** | Verifies fix on staging, runs full regression |
| **Module Brain Owner** | Maintains and updates the module's Brain document |
| **Project Manager** | Tracks metrics, runs monthly reviews, manages SLAs |

---

## 12. Knowledge & Pattern Library

### 12.1 Common Bug Patterns Library

Located at `bug_report_AI/COMMON_BUG_PATTERNS.md`. Updated by Antigravity after every audit and every fix.

**Rules:**
1. Before any audit → Antigravity reads the pattern library first
2. After any fix → Check if pattern exists. If new, add it. If exists, add module to "Found In" list.
3. When generating execution plans → Reference fix templates from the library

### 12.2 Module Brain Updates

After every bug fix, update the Brain with:
- **Anti-pattern**: What was broken and how it was fixed
- **"Why It Was Done This Way"**: Capture developer intent
- **Field-Level Data Flow**: Full path from DB → PHP → HTML → JS for affected fields

---

## 13. Quick Reference — How to Start

### For a New Module (Never Audited)
```
Step 1: /build-module-brain     → Antigravity builds the brain (1 conversation)
Step 2: /module-bug-audit       → Antigravity runs 6-round audit (1 conversation)
Step 3: Triage the results      → Assign severity, sprint, track
Step 4: /fix-single-bug         → Fix bugs one at a time
```

### For an Already-Audited Module (Bugs Exist in Execution Plan)
```
Step 1: /fix-single-bug {BUG_ID}  → Antigravity fixes the next bug
```

### For a Client-Reported Bug (Ad-Hoc)
```
Step 1: Fill Bug Report Form    → Module, steps, expected vs actual
Step 2: Triage Lead assigns     → Severity, category, track, developer
Step 3: /fix-single-bug         → Antigravity diagnoses and fixes
```

---

## Appendix A: Which SOP Contributed What

| Section | Source |
|---|---|
| Two-track classification | **NEW** (system vs business) |
| Module Brain (Phase 0) | SOP 2 (Module Brain) + SOP 4 (Digital Brain) |
| 8-Category Classification | SOP 4 (Digital Brain / LOT) |
| 6-Round Audit + Master Prompt | SOP 1 (Estimation) + Prompt Template |
| Pattern Cross-Reference | Implementation Plan (previous conversation) |
| 8-Step Fix Workflow | SOP 1's 11 steps (streamlined) + SOP 2's forensic approach |
| Core Principles | SOP 1 (isolation) + SOP 3 (stability > elegance) |
| Testing by Category | SOP 1 (test requirements matrix) |
| Deployment Procedure | SOP 1 (only SOP that covered this) |
| Hotfix Track | **NEW** |
| Bug Intake & Communication | **NEW** |
| Metrics | SOP 4 (performance measurement idea, expanded) |
| Exception Handling | SOP 3 (conflict deferral, ambiguous configs) |
| Operational Constraints | SOP 3 (legacy safety, tool agnosticism) |
| Living Documentation | SOP 4 (brain as living doc) + SOP 2 (brain updates) |

## Appendix B: Variables Template (Fill Per Module)

| Variable | Value |
|---|---|
| `{MODULE_NAME}` | |
| `{PREFIX}` | |
| `{CONTROLLER_FILE}` | |
| `{MODEL_FILE}` | |
| `{JS_FILE}` | |
| `{VIEW_DIR}` | |
| `{DB_SCHEMA_FILE}` | |
| `{KB_DIR}` | |

---

## Revision History

| Version | Date | Author | Changes |
|---|---|---|---|
| 1.0 | 2026-02-18 | Logimax Engineering Team | Initial consolidated SOP from 4 developer SOPs + implementation plan |

---

> **Remember**: This SOP is a living document. Update it after every module audit and every monthly review. The pattern library and module brains are what make this system faster over time — invest in keeping them current.
