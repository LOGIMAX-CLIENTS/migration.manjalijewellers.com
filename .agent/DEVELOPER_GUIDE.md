# AI Bug Remediation System — How It Works

> **Read this before using the system for the first time.** This guide explains the new AI-assisted bug remediation system — what it is, how the workflows connect, and what inputs each one needs.

---

## What Is This System?

This is a new **AI-assisted bug finding and fixing system** layered on top of our existing codebase. It doesn't change how the app works — it adds structured **knowledge documents** and **step-by-step workflows** that make bug diagnosis and fixing faster, safer, and trackable.

1. **Knowledge Brain** — Auto-generated documents describing each module's architecture, business rules, database schema, methods, and risks
2. **Workflows** — Standardized procedures (slash commands) for triaging, fixing, testing, and documenting bugs

You already know the codebase. This system just gives you **structured access** to that knowledge and a repeatable process to follow.

---

## Part 1: The Project Files

```
📁 Your Project Root
│
├── 📁 .agent/                        ← SYSTEM CONFIGURATION
│   ├── config.md                      ← ⭐ Shared project config (committed)
│   ├── .env                           ← 🔒 Local values — auto-generated, gitignored
│   ├── .env.example                   ← Template for .env (committed)
│   ├── GEMINI.md                      ← Project rules and standards
│   ├── DEVELOPER_GUIDE.md             ← This document
│   └── 📁 workflows/                  ← Step-by-step procedures
│       ├── 00-INDEX.md                ← List of all available workflows
│       ├── fix-single-bug.md          ← How to fix one bug
│       ├── bug-intake-triage.md       ← How to classify a new bug
│       ├── build-module-brain.md      ← How to build knowledge for a module
│       ├── build-system-brain.md      ← How to build cross-module knowledge
│       └── ... (17 workflows total)
│
├── 📁 knowledge_brain/               ← MODULE KNOWLEDGE
│   ├── 📁 _SYSTEM/                    ← Cross-module intelligence (11 docs)
│   │   ├── SHARED_TABLES.md           ← Which database tables are shared
│   │   ├── TAG_STATUS_MAP.md          ← ⭐ CRITICAL — tag status value guide
│   │   ├── CLEANUP_GAPS.md            ← ⭐ Check before any delete/cancel fix
│   │   ├── SYSTEM_COVERAGE.md         ← ⭐ System brain's coverage tracker
│   │   └── ... (11 documents total)
│   │
│   ├── 📁 Billing/                    ← Module Brain (14 docs when complete)
│   │   ├── MODULE_BRAIN.md            ← Module overview + architecture
│   │   ├── COVERAGE_TRACKER.md        ← ⭐ Module brain's coverage tracker
│   │   ├── BUSINESS_RULES.md          ← How calculations & logic work
│   │   └── ... (more brain docs)
│   │
│   ├── 📁 estimation/                 ← Another Module Brain
│   ├── 📁 tagging/
│   └── ... (one folder per module)
│
└── 📁 bug_report_AI/                  ← BUG TRACKING
    ├── ACTIVE_BUGS.md                 ← Currently in-progress bugs
    ├── COMMON_BUG_PATTERNS.md         ← Known bug patterns & fix templates
    └── 📁 {module}/                   ← Per-module bug reports
```

---

## Part 2: Your First Day — Read These 5 Files

| Step | File to Read                   | Why                                             |
| ---- | ------------------------------ | ----------------------------------------------- |
| 1    | `.agent/config.md` + `.agent/.env` | Know shared config + your local paths        |
| 2    | `_SYSTEM/SYSTEM_COVERAGE.md`   | See which modules have brain coverage           |
| 3    | `_SYSTEM/TAG_STATUS_MAP.md`    | Understand the most critical shared data        |
| 4    | `_SYSTEM/SHARED_TABLES.md`     | Know which tables are dangerous to modify       |
| 5    | `.agent/workflows/00-INDEX.md` | See all available workflows                     |

> **First-time setup**: Run `/validate-workflows` before anything else. This auto-detects your PHP path, project root, issue platform (Gitea/GitHub), and saves everything to `.agent/.env`.

> **Issue Tracking**: The system supports both **Gitea** and **GitHub**. Your platform is auto-detected from `git remote -v`. See `/github-bug-tracking` for the platform dispatch table.

---

## Part 3: How To Call Each Workflow (With Examples)

### 🔨 `/build-module-brain` — Build Knowledge For a Module

**When**: Before you can fix bugs in a module, you need its brain.

**What to provide**:

```
Module name:      Billing
Controller file:  application/controllers/admin_ret_billing.php
Model file:       application/models/ret_billing_model.php
JS file:          assets/js/billing.js
View folder:      application/views/admin/billing/
```

**Example call**:

> "Build a module brain for the **Sales** module. Controller: `admin_ret_sales.php`, Model: `ret_sales_model.php`, JS: `sales.js`, Views: `application/views/admin/sales/`"

**How many rounds?**: Run until `COVERAGE_TRACKER.md` says **100% on ALL metrics**. Usually 3-6 rounds.

---

### 📋 `/bug-intake-triage` — Classify a New Bug

**When**: A bug is reported by a client, QA, or found during audit.

**What to provide**:

```
Module:            Estimation
Bug description:   "When creating an estimation with partial tag,
                    the net weight shows 0 instead of calculated value"
Reporter:          Client / QA / Internal
Environment:       Production / Staging / Dev
```

**Example call**:

> "Triage this bug: In the **Estimation** module, when adding a partial tag item with calculation type 3 (Fixed Rate), the partial weight is not calculated. The tag shows full weight instead of the partial amount. Reported by **client** on production."

**Output**: Bug ID like `EST-CLT01`, severity (P0-P3), track (A/B), GitHub issue.

---

### 🔧 `/fix-single-bug` — Fix One Bug

**When**: After triage. You have a Bug ID and know which module.

**What to provide**:

```
Bug ID:     EST-CLT01 (generated by /bug-intake-triage)
```

**Example call**:

> "Fix bug **EST-CLT01** — partial tag weight calculation error in Estimation"

**Note**: The workflow reads the bug details from the execution plan that triage created.

---

### 🔬 `/fix-architecture-bug` — Fix Security/SQL/Performance Bug (Track A)

**When**: The bug is a system-level issue, not a business logic error.

**What to provide**:

```
Bug ID:     TAG-R201 (already triaged as Track A)
```

**Example call**:

> "Fix architecture bug **TAG-R201** — SQL injection in stock issue model `getStockIssueList()` method"

**Who does what**: AI fixes the code → Human reviews and approves the diff.

---

### 📊 `/fix-business-bug` — Fix Business Logic Bug (Track B)

**When**: The bug is a wrong calculation or missing business rule.

**What to provide**:

```
Bug ID:     BIL-CLT03 (already triaged as Track B)
```

**Example call**:

> "Fix business bug **BIL-CLT03** — loyalty points not deducted when using wallet payment"

**Who does what**: AI proposes fix → Human validates the business logic FIRST → Then AI applies.

---

### ✅ `/test-and-verify` — Test a Fix

**When**: After applying a fix.

**What to provide**:

```
Bug ID:     EST-CLT01 (the bug you just fixed)
```

**Example call**:

> "Test and verify the fix for **EST-CLT01**"

---

### 📚 `/learn-and-improve` — Update Knowledge After a Fix

**When**: After a fix is approved and tested.

**What to provide**:

```
Bug ID:     EST-CLT01 (the bug you just fixed)
```

**Example call**:

> "Run learn and improve for **EST-CLT01**"

---

### 🧠 `/build-system-brain` — Build Cross-Module Knowledge

**When**: You have 3+ module brains built.

**What to provide**:

```
Mode:   Initial Build (first time) / Incremental Add (new module added) / Full Refresh
```

**Example call (first time)**:

> "Build the system brain — initial build"

**Example call (after adding a new module brain)**:

> "Run system brain incremental add — just added LOT module brain"

---

### 🔍 `/module-bug-audit` — Audit a Module For All Bugs

**When**: You want to find ALL bugs in a module proactively.

**What to provide**:

```
Module name:  Estimation
```

**Example call**:

> "Run a full bug audit on the **Estimation** module"

**How many rounds?**: Always **6 rounds** (fixed by design — each round covers different bug categories). This is NOT coverage-driven.

---

### 🏗️ `/setup-existing-client` — Set Up For a New Client

**When**: A new client needs the bug remediation system.

**What to provide**:

```
Client name:      ABC Jewellers
Project root:     C:\xampp\htdocs\ABC\ABC-RetailAdmin
Source repo:      (the reference repo to copy brains from)
```

**Example call**:

> "Set up bug remediation system for client **ABC Jewellers** at `C:\xampp\htdocs\ABC\ABC-RetailAdmin`"

---

## Part 4: The Complete Flow — Three Phases

### 🔷 Phase A: Build Knowledge (Before You Can Fix Bugs)

```
Step A1: Pick a module (e.g., "Sales")
         ↓
Step A2: Run /build-module-brain (give module name + file paths)
         ↓
         Round 1 → ~50% coverage
         Round 2 → ~75% coverage
         Round 3 → ~95% coverage
         Round 4+ → Keep going until 100%
         ↓
Step A3: Check COVERAGE_TRACKER.md → Is it 100%?
         • NO → Run another round (say "continue building")
         • YES → Module brain is complete ✅
```

> **Important**: The number of rounds is NOT fixed. Some modules need 3 rounds, some need 6. You keep building until the COVERAGE_TRACKER says every metric is at 100%.

### 🔷 Phase B: Build Cross-Module Knowledge (After 3+ Module Brains)

```
Step B1: Run /build-system-brain (mode: "Initial Build")
         ↓
         Creates _SYSTEM/ folder with 11 documents
         ↓
Step B2: Check SYSTEM_COVERAGE.md → How many modules covered?
         ↓
Step B3: Build more module brains
         ↓
Step B4: Run /build-system-brain again (mode: "Incremental Add")
         ↓
         Repeat B3-B4 until all modules have brains
```

### 🔷 Phase C: Fix Bugs (The Daily Work)

```
Step C1: Bug is reported
         ↓
Step C2: Run /bug-intake-triage (give: description + module name)
         → Output: Bug ID, severity, track
         ↓
Step C3: Does the module have a brain?
         • NO → Run /build-module-brain first (Phase A)
         • YES → Continue
         ↓
Step C4: Run /fix-single-bug (give: Bug ID)
         → 8 steps: Diagnose → Locate → Assess → Fix → Test → Approve → Close
         ↓
Step C5: Run /test-and-verify (give: Bug ID)
         ↓
Step C6: Run /learn-and-improve (give: Bug ID)
         → Updates brain + pattern library + GitHub issue
```

---

## Part 5: Important Rules

### Before ANY Code Change:

- [ ] Read the module brain (`knowledge_brain/{module}/`)
- [ ] Check `_SYSTEM/SHARED_TABLES.md` if your change touches a DB table
- [ ] Check `_SYSTEM/TAG_STATUS_MAP.md` if your change involves tag_status
- [ ] Check `_SYSTEM/CLEANUP_GAPS.md` if your change involves delete/cancel/reverse

### During Code Changes:

- [ ] Use CI3 query builder — never raw SQL
- [ ] No magic numbers — use constants or comments
- [ ] Business logic in models, not controllers
- [ ] `parseFloat()` + `toFixed(2)` + `isNaN()` for all financial JS
- [ ] Always validate server-side — never trust the client

### After Code Changes:

- [ ] Run `/learn-and-improve` to update knowledge
- [ ] Update GitHub Issue status
- [ ] Update `ACTIVE_BUGS.md`

---

## Part 6: Common Mistakes

| ❌ Never Do This                               | ✅ Do This Instead                                      |
| ---------------------------------------------- | ------------------------------------------------------- |
| Start fixing without reading the brain         | Read `MODULE_BRAIN.md` + `_SYSTEM/` first               |
| Fix a bug without checking cross-module impact | Check `SHARED_TABLES.md` and `TAG_STATUS_MAP.md`        |
| Write raw SQL: `$this->db->query("SELECT...")` | Use CI3 builder: `$this->db->select()->from()->where()` |
| Use magic numbers: `where('status', 1)`        | Use constants or comments explaining what 1 means       |
| Copy-paste a function to modify it             | Extend the existing function with a parameter           |
| Skip `/learn-and-improve` after fixing         | Always run it — keeps the brain current                 |
| Hardcode dates, user IDs, tax rates            | Use `ret_settings` table or config files                |
| Trust form values for financial calculations   | Always recalculate server-side                          |
| Put business logic in controllers              | Business logic belongs in models                        |

---

## Part 7: Glossary

| Term                          | What It Means                                                         |
| ----------------------------- | --------------------------------------------------------------------- |
| **Module Brain**              | 8-14 documents describing ONE module's code, rules, and risks         |
| **System Brain** (`_SYSTEM/`) | 11 cross-module documents showing how modules interact                |
| **Coverage Tracker**          | Checklist showing what % is documented. 100% = module brain is done   |
| **Track A**                   | System bugs (SQL, security) — AI fixes, human approves diff           |
| **Track B**                   | Business bugs (wrong math) — Human validates logic first              |
| **Pattern Library**           | Reusable fix templates. Check before writing a new fix                |
| **P0 — Critical**             | Data loss / money wrong / system down → Fix same day                  |
| **P1 — Major**                | Wrong display / broken rules → Fix within 3 days                      |
| **P2 — Minor**                | Edge cases / missing validations → Fix next sprint                    |
| **P3 — Cosmetic**             | Styling / dead code → Fix when time allows                            |
| **Graceful Fallback**         | If `_SYSTEM/` doesn't exist, workflows skip those steps automatically |
