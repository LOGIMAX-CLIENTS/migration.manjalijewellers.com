---
description: Bug Intake & Triage — Classify and register a new bug into the pipeline
---

# Phase 1 — Bug Intake & Triage

**SOP Reference**: [FINAL_SOP_BUG_REMEDIATION.md](file:///c:/xampp/htdocs/etail_development_src/SOP/FINAL_SOP_BUG_REMEDIATION.md) — Section 9 (Bug Intake) + Section 5 (Triage)

## When to Use
- A client, support person, tester, or developer reports a new bug
- A bug is found during code review or ad-hoc testing
- You want to register a bug before fixing it

## Required Input
Ask the user for these fields (skip any already provided):

| Field | Required | Example |
|---|---|---|
| **Reporter** | Yes | "Client — Rajesh" |
| **Module** | Yes | "LOT" |
| **Sub-Module** | Optional | "Lot Inward" |
| **Steps to Reproduce** | Yes | "1. Open Lot Inward Add, 2. Select product, 3. Design dropdown is empty" |
| **Expected Behavior** | Yes | "Design dropdown should show designs for the selected product" |
| **Actual Behavior** | Yes | "Design dropdown stays empty" |
| **Evidence** | Optional | Screenshot, error log, video |
| **Environment** | Yes | "Production / Chrome" |
| **Frequency** | Yes | "Always / Sometimes / Once" |

## Steps

### 1. Determine the Bug Track

Classify as **Track A (System/Architecture)** or **Track B (Business)**:

**Track A — System/Architecture** (Antigravity can fix autonomously):
- Security bugs (SQL injection, XSS, CSRF, raw `$_POST`)
- Transaction handling (missing rollback, `trans_commit()` on failure)
- Query logic (Cartesian JOINs, missing GROUP BY, N+1)
- Schema defects (missing PK, wrong column types, MyISAM)
- Variable/copy-paste bugs (typos, undefined vars, wrong index)
- Exception handling (missing try-catch, silent failures)
- Concurrency (race conditions, no locking)
- Performance (missing indexes, no pagination)

**Track B — Business** (Antigravity proposes, human validates):
- Calculation errors (wrong formula, wrong variable in chain)
- Missing business rules (validation gaps, missing constraints)
- Incorrect workflows (wrong status transitions, missing approvals)
- Data integrity (orphan records, duplicates, missing cascades)
- Integration bugs (wrong data handshake between modules)

### 2. Assign Severity

| Level | Criteria | Target Fix Time |
|---|---|---|
| **P0 — Critical** | Data loss, security breach, financial miscalculation, system down | Same day |
| **P1 — Major** | Wrong data display, broken business rules, orphan records | ≤ 3 days |
| **P2 — Minor** | UX issues, missing validations, code quality, edge cases | Next sprint |
| **P3 — Cosmetic** | Debug artifacts, styling, dead code | Backlog |

### 3. Assign Category

One of: Logic, Data Validation, Database, Exception Handling, Security, Performance, Integration, Concurrency

### 4. Generate Bug ID

Format: `{PREFIX}-{ROUND}{SEQ}`

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

For ad-hoc bugs (not from 6-round audit), use round prefix `AH`: e.g., `LOT-AH01`

### 5. Assign Sprint

| Sprint | Priority | Timeline |
|---|---|---|
| Sprint 1 | All P0 + critical P1 | This week |
| Sprint 2 | Remaining P1 + high P2 | Next week |
| Sprint 3 | Remaining P2 + all P3 | Backlog |

### 6. Check for Existing Module Brain

// turbo
Read the module's brain file if it exists:
```
Check if knowledge/{MODULE_NAME}/MODULE_BRAIN.md exists
```

- If brain exists → proceed to `/fix-single-bug` or `/module-bug-audit`
- If brain does NOT exist → tell user to run `/build-module-brain` first

### 7. Output — Bug Entry

Create or append to `bug_report_AI/{module}/CONSOLIDATED_BUG_REPORT.md`:

```markdown
### {BUG_ID}: {Title}

| Field | Value |
|---|---|
| Bug ID | {BUG_ID} |
| Module | {MODULE} |
| Sub-Module | {SUB_MODULE} |
| Reporter | {REPORTER} |
| Date | {TODAY} |
| Track | A (System) / B (Business) |
| Severity | P{N} |
| Category | {CATEGORY} |
| Sprint | Sprint {N} |
| Status | New |

**Steps to Reproduce**:
1. {step}
2. {step}

**Expected**: {expected}
**Actual**: {actual}
**Evidence**: {link or description}
```

### 8. Notify

Tell the user:
- Bug ID assigned
- Severity and sprint
- Whether a Module Brain exists (if not, recommend `/build-module-brain`)
- Next action: `/fix-single-bug {BUG_ID}` or `/module-bug-audit {MODULE}`
