---
description: Build a Module Brain for any module to enable rapid bug diagnosis and AI-assisted development
version: 1.4
last_updated: 2026-03-24
---

# Build Module Brain Workflow

## Purpose
Create a comprehensive knowledge base ("Module Brain") for any module. This reduces bug diagnosis time by ~60% and is a **prerequisite** before running a bug audit on any new module.

## AI Optimization Principles

> These brain docs are consumed by Antigravity (AI agent). Structure EVERY output for machine grep:

1. **Alphabetical sorting** — All method indexes must be alphabetical so `grep_search` finds any method in one hit
2. **Method → Tables mapping** — Every method entry must specify which tables it reads/writes. This is the #1 lookup pattern for bug diagnosis
3. **Table → Methods reverse-map** — For any table bug, instantly find all methods that touch it
4. **JS → Controller AJAX map** — Every `$.ajax` / `$.post` / `$.get` call mapped to its backend endpoint
5. **Callers column** — Every method entry should say what calls it (controller method, other model method, or JS function)
6. **Line numbers** — Every reference must include line numbers. Stale line numbers are better than no line numbers
7. **Cross-references over prose** — Tables and links over paragraphs. One lookup, not a paragraph scan

## Prerequisites
- Module name identified
- Access to the module's controller, model, JS, views, and DB tables
- `knowledge_brain/` directory exists (create if not)

## Input
- `{MODULE_NAME}` — e.g., Sales, Purchase, Inventory, Billing, LOT, CRM
- `{CONTROLLER_FILE}` — e.g., `{CONTROLLER_DIR}/admin_ret_sales.php`
- `{MODEL_FILE}` — e.g., `{MODEL_DIR}/ret_sales_model.php`
- `{JS_FILE}` — e.g., `{JS_DIR}/ret_sales.js`
- `{VIEW_DIR}` — e.g., `application/views/admin/sales/`

## Output
```
knowledge_brain/{MODULE_NAME}/
├── MODULE_BRAIN.md           ← Master brain document (architecture, routes, risks)
├── METHOD_INDEX.md           ← Alphabetical method lookup with tables & callers
├── DATA_FLOW.md              ← Detailed data flow traces + JS function map
├── FLOW_RISK_MATRIX.md       ← Handoff contracts, state machines, reversal checks (QA-ready)
├── BUSINESS_RULES.md         ← Extracted business rules and formulas
├── CROSS_MODULE_MAP.md       ← Dependencies on other modules
├── SCHEMA_ANALYSIS.md        ← DB table analysis (columns, types, indexes, risks)
├── INVARIANT_MATRIX.md       ← Variant × behavior grids (if module has config-driven behavior)
├── FORENSIC_TEMPLATE.md      ← Per-module layer-by-layer investigation cheat sheet
└── COVERAGE_TRACKER.md       ← Round-by-round coverage progress (auto-updated every round)
```

> **Note**: `INVARIANT_MATRIX.md` and `FORENSIC_TEMPLATE.md` are optional for simple modules. They become critical for modules with config-driven behavior (scheme types, payment modes, GST variants, etc.).
> `COVERAGE_TRACKER.md` is **MANDATORY** — must be created on Round 1 and updated every subsequent round.
> Templates available at `knowledge_brain/_TEMPLATE/`.

## Steps

### Step 0: Existing Brain Detection [Antigravity]

Before building, check if a brain already exists:

// turbo
```powershell
Test-Path "{PROJECT_ROOT}\knowledge_brain\{MODULE_NAME}\MODULE_BRAIN.md"
```

**If brain does NOT exist** → Skip to Step 1 (build from scratch).

**If brain EXISTS** → Present three options to the developer:

| Mode | When to Use | What Happens |
|---|---|---|
| **Refresh** | Same version, brain may be stale (code changed since brain was built) | Re-scan code, MERGE new findings into existing brain. Preserve: anti-patterns register, fix history, manual notes, derived-brain annotations |
| **Upgrade** | Version upgraded (e.g., client updated from v4 to v5) | Diff old brain against new code. Add new methods/routes/tables. Flag removed ones. Keep all historical fix data |
| **Force Rebuild** | Brain is corrupted or fundamentally wrong | Back up existing brain, then wipe and regenerate from scratch |

#### Refresh Mode Procedure:
1. Read existing `METHOD_INDEX.md` — extract all documented method names
2. Scan current model file — extract all actual method names
3. **New methods** (in code but not in brain) → Generate entries and ADD to METHOD_INDEX.md
4. **Removed methods** (in brain but not in code) → Mark as `⚠️ REMOVED — no longer in codebase`
5. **Existing methods** → Keep existing entries, update line numbers only
6. Repeat for routes (MODULE_BRAIN.md), JS functions (DATA_FLOW.md), tables (SCHEMA_ANALYSIS.md)
7. **NEVER overwrite**: Anti-patterns register, fix history, DERIVED BRAIN header, manual annotations

#### Upgrade Mode Procedure:
1. Back up entire existing brain (**to a SIBLING folder, NOT inside the module dir**):
   // turbo
   ```powershell
   $date = Get-Date -Format "yyyyMMdd"
   # CRITICAL: Back up to sibling folder, never inside the module dir (prevents recursive nesting)
   robocopy "{PROJECT_ROOT}\knowledge_brain\{MODULE_NAME}" "{PROJECT_ROOT}\knowledge_brain\_bk_{MODULE_NAME}_$date" /E /XD "_backup*" "_bk_*"
   ```
   > ⚠️ **NEVER** use `Copy-Item -Recurse` into a subfolder of the source — it creates infinite recursive nesting.
2. Run full scan as if building from scratch (Steps 1–11)
3. Before writing each file, diff against the backup:
   - **SCHEMA_ANALYSIS.md**: Identify new/removed/changed columns
   - **METHOD_INDEX.md**: Identify new/removed methods
   - **MODULE_BRAIN.md**: Identify new/removed routes
   - **DATA_FLOW.md**: Identify new/changed AJAX endpoints
4. Merge historical data from backup:
   - Copy the anti-patterns register from old MODULE_BRAIN.md to new
   - Copy fix history and manual annotations
   - Copy any `DERIVED BRAIN` header
5. Add upgrade header:
   ```markdown
   > **🔄 UPGRADED BRAIN**
   > Previous version: {old_version}
   > Upgraded to: {new_version}
   > Upgrade date: {TODAY}
   > Changes: {N} methods added, {N} removed, {N} tables changed
   ```

#### Force Rebuild Procedure:
1. Back up existing brain (same as Upgrade Mode step 1)
2. Wipe and regenerate (Steps 1–11, no merging)
3. Add header noting it was force-rebuilt:
   ```markdown
   > **🔨 FORCE REBUILT**
   > Previous brain backed up to: _backup_{DATE}/
   > Rebuilt on: {TODAY}
   > Reason: {developer provides reason}
   ```

### Step 1: Project Skeleton
// turbo
1. List ALL files the module touches:
   - Controller(s): `{CONTROLLER_FILE}` — count lines and methods
   - Model(s): `{MODEL_FILE}` — count lines and methods
   - JS file(s): `{JS_FILE}` — count lines
   - View file(s): list all in `{VIEW_DIR}`
   - Config files, helpers, libraries if any

2. For each file, write a one-line purpose description

3. Document the connection flow:
   ```
   Browser → JS ({JS_FILE})
          → AJAX → Controller ({CONTROLLER_FILE})
          → Model ({MODEL_FILE})
          → DB (list tables)
          → View ({VIEW_DIR})
          → Browser
   ```

4. Note file sizes — the largest file is the most complex and risky

### Step 1.5: Constructor Analysis
Read the controller's `__construct()` method and document:

1. **Loaded Models** — List every `$this->load->model()` call with purpose
2. **Loaded Libraries** — List any `$this->load->library()` calls
3. **Session Gate** — Document authentication checks and access-time restrictions
4. **Loaded Helpers** — Any `$this->load->helper()` calls

Format:
```
| Model/Library | Purpose |
|---|---|
| `ret_estimation_model` | Primary — all estimation methods |
| `admin_settings_model` | Settings/config lookups |
```

### Step 2: Entry Points & Routes
Create a table of ALL entry points:

| URL Path | HTTP Method | Controller Method | Lines | Purpose |
|---|---|---|---|---|
| /admin/{module}/add | GET | add() | L100-200 | Load add form |
| /admin/{module}/save | POST | save() | L200-500 | Save new record |
| ... | ... | ... | ... | ... |

To build this:
1. Read the controller file outline to get all public methods
2. Identify which methods handle page loads vs AJAX calls
3. Map each method to its corresponding view/JS handler
4. Separate utility methods (non-route, internal) into their own table

### Step 3: Engine Reverse Engineering (Data Flow)
Trace the primary user actions end-to-end. At minimum, trace these 3 flows:

**Flow 1: CREATE (Save New Record)**
1. What JS function fires on save button click?
2. What validation runs?
3. What data is collected from the form?
4. What AJAX endpoint receives it?
5. What controller method handles it?
6. What model methods are called?
7. What tables are written to, and in what order?
8. What response goes back to the browser?

**Flow 2: EDIT (Update Existing Record)**
1. How is existing data loaded? (which model methods, which tables)
2. How is it populated into the form? (JS or PHP)
3. Does the update use DELETE-then-INSERT or actual UPDATE?
4. Which child tables are updated?
5. Are there fields that exist in SELECT but NOT in UPDATE (data loss risk)?

**Flow 3: DELETE**
1. Is it GET or POST? (GET = CSRF vulnerable)
2. Which tables are cleaned up?
3. Is there a soft-delete or hard-delete?
4. Are ALL child/related tables cleaned up?

Document each flow with exact function names, line numbers, and table names.

Output: `knowledge_brain/{MODULE_NAME}/DATA_FLOW.md`

### Step 3b: Flow Risk Matrix (Handoff Contracts) [Antigravity]

> **Purpose**: Capture what QA teams test — the **contracts between modules** at each handoff point. This is the #1 gap in pure documentation vs. testable specifications.
> **Input**: DATA_FLOW.md (from Step 3) + CROSS_MODULE_MAP.md (from Step 5, or constructor analysis if available)
> **When**: Build this AFTER Step 3 and Step 5 are complete. On initial brain build, do Steps 3→5 first, then come back to 3b.

Create `knowledge_brain/{MODULE_NAME}/FLOW_RISK_MATRIX.md` with these sections:

#### 3b-1. State Machine

For the module's **primary entity** (the main table — e.g., `ret_taging`, `ret_estimation`, `ret_billing`), document ALL status fields and their valid transitions:

```markdown
## State Machine: {TABLE}.{STATUS_FIELD}

| State | Value | Set By (Module.Method) | Can Transition To | Guard / Precondition |
|---|---|---|---|---|
| Available | 0 | Tagging.tagging('save') | Reserved(1), Sold(→Billing), Deleted(2) | — |
| Sold | 1 | Billing.billing('save') | Available(→Bill Cancel) | Must have estimation |
| Deleted | 2 | Tagging.verify_otp() | — (terminal) | OTP required |
```

To build this:
1. Grep the model file for all UPDATE statements that change the status field
2. Grep ALL module brains' CROSS_MODULE_MAP.md for external writes to this table's status field
3. For each status value, identify who sets it and what preconditions are checked (or not checked)
4. Flag any transitions that lack a precondition guard: `⚠️ NO GUARD`

#### 3b-2. Inbound Contracts

For each **upstream module** that feeds data INTO this module, document what this module expects:

```markdown
## Inbound Contracts (What This Module Expects from Upstream)

| Upstream Module | Data/State Expected | Precondition Check in Code? | Line | Risk if Violated |
|---|---|---|---|---|
| Tagging | tag_status=0 (Available) | YES: get_tag_status() L{N} | L{N} | Estimation on sold tag = phantom revenue |
| Tagging | ret_taging_stone records exist | NO check | — | Missing stone data, NaN in totals |
| Customer | customer.id_customer valid | YES (FK constraint) | — | DB error on save |
```

To build this:
1. From CROSS_MODULE_MAP.md, list all external tables READ by this module
2. For each, check: does the code validate the expected state before using the data?
3. Mark `YES` (with line number), `PARTIAL` (checks some but not all), or `NO`
4. Document the risk if the upstream contract is violated

#### 3b-3. Outbound Contracts

For each **downstream module** that consumes data FROM this module, document what this module guarantees:

```markdown
## Outbound Contracts (What This Module Guarantees to Downstream)

| Downstream Module | What This Module Guarantees | Enforced How? | Risk if Broken |
|---|---|---|---|
| Billing | estimation.estbillid = NULL (not yet billed) | No explicit check | Double billing possible |
| Billing | All estimation_items have valid tag_ids | FK constraint only | Bill references deleted tag |
| Reports | ret_estimation.total = sum(item totals) | No server-side validation | Report shows wrong total |
```

To build this:
1. From CROSS_MODULE_MAP.md, list all external tables WRITTEN by this module
2. From other module brains (if built), check their inbound contracts to see what they expect
3. Verify: does this module actually guarantee what downstream expects?
4. Flag mismatches as `⚠️ CONTRACT GAP`

#### 3b-4. Reversal Contracts

For each **cancel/delete/reverse** operation in this module, document what must be restored:

```markdown
## Reversal Contracts (Cancel/Delete/Reverse)

| Operation | Tables That Must Be Restored | Actually Restored in Code? | Method & Line | Gap? |
|---|---|---|---|---|
| Cancel bill | ret_taging.tag_status → 0 | ✅ YES | cancel_bill() L{N} | — |
| Cancel bill | ret_estimation.estbillid → NULL | ✅ YES | cancel_bill() L{N} | — |
| Cancel bill | ret_estimation_items.purchase_status → 0 | ✅ YES | cancel_bill() L{N} | — |
| Cancel bill | ret_billing_advance → reversed | ❌ NO | — | ⚠️ Advance not returned |
| Cancel bill | ret_journal → reverse entries | ✅ YES | cancel_bill() L{N} | — |
```

To build this:
1. From DATA_FLOW.md Flow 1 (CREATE), list ALL tables written during save
2. From DATA_FLOW.md Flow 3 (DELETE/CANCEL), list ALL tables cleaned up
3. Diff the two lists — any table written during CREATE but not restored during CANCEL is a gap
4. Mark each as `✅ YES`, `⚠️ PARTIAL`, or `❌ NO`

#### 3b-5. Flow Risk Checklist (QA-Ready Test Scenarios)

Generate a checklist of flow-wise test scenarios that a QA team should verify:

```markdown
## Flow Risk Checklist

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-{MOD}-001 | CREATE with invalid upstream state (e.g., sold tag) | REJECT with clear error | 🔴 HIGH | ❌ |
| FR-{MOD}-002 | CREATE when upstream record is deleted | REJECT | 🔴 HIGH | ❌ |
| FR-{MOD}-003 | EDIT after downstream has consumed (e.g., edit estimation after billing) | REJECT or warn | 🔴 HIGH | ❌ |
| FR-{MOD}-004 | CANCEL → verify ALL tables from CREATE are restored | Full restoration | 🔴 HIGH | ❌ |
| FR-{MOD}-005 | CANCEL → re-create same record | Should work normally | 🟡 MED | ❌ |
| FR-{MOD}-006 | Concurrent save on same entity by 2 users | One succeeds, one rejects | 🟡 MED | ❌ |
| FR-{MOD}-007 | Save crashes midway (simulate by checking trans_begin/complete) | No orphan records | 🔴 HIGH | ❌ |
| FR-{MOD}-008 | Data passed to downstream matches what was saved | Values match exactly | 🟡 MED | ❌ |
```

To build this:
1. For each inbound contract with `NO` check → create a "test invalid state" scenario
2. For each reversal gap → create a "verify restoration" scenario
3. For each outbound contract gap → create a "verify downstream receives correct data" scenario
4. Always include: concurrent access, midway crash, cancel-then-recreate

Output: `knowledge_brain/{MODULE_NAME}/FLOW_RISK_MATRIX.md`

### Step 4: Business Rules Extraction
For every calculation or constraint found in the code:

1. Extract the formula/rule
2. Write it in plain English with the formula
3. Note where it's implemented (JS line, PHP method, or both)
4. Note if it's validated server-side, client-side, or both
5. Note any edge cases or boundary conditions

Format:
```
RULE-{MODULE}-001: {Rule Name}
Formula: {plain-English formula}
Implementation: {JS function at L{N}} + {PHP method}
Validation: Client-side only / Server-side only / Both
Edge cases: {known edge cases}
```

Output: `knowledge_brain/{MODULE_NAME}/BUSINESS_RULES.md`

### Step 5: Cross-Module Mapping
Identify every interaction with external modules:

1. From the constructor, find every model/library loaded from another module
2. From the model, find every table READ that belongs to another module
3. From the model, find every table WRITTEN that belongs to another module
4. From the JS file, find every AJAX call to a different controller

Document:
| External Module | Direction | Tables/Methods | What Data | Risk |
|---|---|---|---|---|
| Inventory | Read | ret_taging | Tag details | Tag could be deleted |
| Accounts | Write | ret_journal | Journal entry | Wrong amount propagates |
| Tagging (JS AJAX) | Read | `/admin_ret_tagging/getStoneItems` | Stone types | External controller dependency |
| ... | ... | ... | ... | ... |

Include a Mermaid dependency graph showing all modules and their read/write relationships.

Output: `knowledge_brain/{MODULE_NAME}/CROSS_MODULE_MAP.md`

### Step 6: DB Truth Protocol
Create verification queries for the module's most important data:

1. A query that pulls a complete transaction by ID (header + all child records)
2. A query that calculates the expected total using raw DB values
3. A query that checks for orphan records (child records without a parent)
4. A query that checks for integrity issues (e.g., item total ≠ sum of line items)

These queries become diagnostic tools for future bug investigation.

### Step 6b: INVARIANT_MATRIX.md (if applicable)

Build this if the module has **config-driven behavior** — different code paths based on settings, types, or modes.

See template at `knowledge_brain/_TEMPLATE/INVARIANT_MATRIX.md`.

1. **Identify variant dimensions**: What configuration fields change the module's behavior? (e.g., scheme_type, gst_type, payment_mode, discount_type)
2. **For each dimension**: List all possible values and what controls them
3. **Build behavior grids**: Create Dimension A × Dimension B matrices showing:
   - What formula/logic applies in each cell
   - Flag any cells where behavior is buggy or undefined
4. **Document the controlling fields**: For each variant, specify the DB column, PHP variable, and JS variable that controls it

Output: `knowledge_brain/{MODULE_NAME}/INVARIANT_MATRIX.md`

### Step 6c: FORENSIC_TEMPLATE.md

Create a layer-by-layer investigation cheat sheet for the module. This accelerates future bug diagnosis.

See template at `knowledge_brain/_TEMPLATE/FORENSIC_TEMPLATE.md`.

1. **Layer 1 — Symptom Collection**: Module-specific symptom checklist (what could go wrong?)
2. **Layer 2 — Reproduce & Isolate**: Step-by-step reproduction checklist + isolation questions for this module
3. **Layer 3 — Client-Side Trace**: JS console log points, key variables to inspect, Network tab checks
4. **Layer 4 — Server-Side Trace**: Controller trace points table (symptom → file → method → line → what to check)
5. **Layer 5 — Database Verification**: Module-specific diagnostic SQL queries
6. **Layer 6 — Root Cause Classification**: Module-specific category × risk table

#### 6c-extra: Inject Module-Specific Investigation Layers

After building the base 6 layers, analyze what the brain discovered and add specialized layers:

| If the module has... | Add this layer |
|---|---|
| Financial transactions (INSERT into receipt/payment/billing tables) | **Layer 7 — Transaction Integrity**: Verify `trans_start/trans_complete` wrapping, check for partial commits, validate amount calculations match DB |
| GST/Tax calculations | **Layer 7 — Tax Calculation Trace**: Expected tax = base × rate, verify rate lookup, check for rounding errors, compare PHP vs DB vs displayed values |
| Multi-step approval workflows | **Layer 7 — Approval Flow Trace**: Verify each status transition is valid, check for skipped steps, validate approver permissions |
| Print/PDF generation | **Layer 7 — Print Template Trace**: Compare displayed values vs printed values, check for missing fields in print query, verify formatting |
| Inventory/stock operations | **Layer 7 — Stock Integrity**: Verify stock_in/stock_out balance, check for double-counting, validate qty across tables |
| Cross-module AJAX calls | **Layer 7 — Integration Point Trace**: List all external AJAX endpoints, verify response handling, check for timeout scenarios |
| Config-driven behavior (INVARIANT_MATRIX exists) | **Layer 7 — Variant Isolation**: For each reported bug, first identify which variant config is active, test same operation with different config |

Add at most 2-3 specialized layers (pick the most relevant ones for this module). Don't add all of them — that defeats the purpose.

Output: `knowledge_brain/{MODULE_NAME}/FORENSIC_TEMPLATE.md`


### Step 7: Build METHOD_INDEX.md

> **This is the most critical AI optimization step.**

Create `knowledge_brain/{MODULE_NAME}/METHOD_INDEX.md` with:

**7a. Controller Methods (alphabetical)**
| Method | Lines | Tables Read | Tables Written | JS Caller |
|---|---|---|---|---|

**7b. Model Methods (alphabetical)**
| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|

To build:
1. Read every model method and grep for FROM/JOIN/INSERT/UPDATE/DELETE to identify tables
2. For generic CRUD methods (insertData, updateData), mark as `{any}` in the tables column
3. Trace each method's callers from the controller

**7c. JS → Controller AJAX Map**
Grep the JS file for all AJAX calls (`url:` patterns). Map each to its controller endpoint:
| JS Line | JS Context | AJAX URL | Controller Method |
|---|---|---|---|

Separate into:
- Internal endpoints (same controller)
- Cross-module endpoints (calls to other controllers)

**7d. Table → Methods Reverse Map**
For each key table, list which methods read it and which write it:
| Table | Read By | Written By |
|---|---|---|

### Step 8: Assemble the Module Brain
Create `knowledge_brain/{MODULE_NAME}/MODULE_BRAIN.md` with these sections:

1. **Module Overview** — Purpose, file map, connection diagram
2. **Constructor** — Loaded models, session gate, libraries (from Step 1.5)
3. **Entry Points** — Route table from Step 2
4. **Model Methods Summary** — Group counts + link to METHOD_INDEX.md (NOT individual method listings)
5. **Data Flow Summary** — High-level flow from Step 3 (detailed flows in DATA_FLOW.md)
6. **Key Tables** — List with column counts, key columns, purpose
7. **Form Sections** — Hidden fields, toggle controls, DOM IDs
8. **Business Rules Summary** — Rule count and top rules (link to BUSINESS_RULES.md)
9. **Cross-Module Dependencies** — Summary (link to CROSS_MODULE_MAP.md)
10. **Known Risks** — Pre-identified risk areas from the analysis
11. **DB Verification Queries** — From Step 6
12. **Codebase Notes** — File sizes, coding patterns, JS conventions
13. **Anti-Patterns Register** — Empty initially, updated after each bug fix

> **IMPORTANT**: Keep MODULE_BRAIN.md under 400 lines. Move all individual method listings to METHOD_INDEX.md. MODULE_BRAIN is for architecture and decision context, not method catalogs.

### Step 9: Coverage Progress Scan [MANDATORY — Every Round]

> **This step MUST run at the end of every round** (initial build, refresh, upgrade, or verification round). It measures what % of the module is documented and tracks improvement over rounds.

#### 9a. Count Actuals from Codebase
Scan the actual codebase to get the TOTAL counts:

// turbo
```powershell
# Controller method count
(Select-String -Path "{CONTROLLER_FILE}" -Pattern "^\s*(public\s+)?function\s+" | Measure-Object).Count

# Model method count
(Select-String -Path "{MODEL_FILE}" -Pattern "^\s*(public\s+)?function\s+" | Measure-Object).Count

# JS AJAX endpoint count (unique URLs)
(Select-String -Path "{JS_FILE}" -Pattern "url\s*:" | Measure-Object).Count

# View/template file count
(Get-ChildItem "{VIEW_DIR}" -File -Recurse | Measure-Object).Count

# Hidden field count in main form
(Select-String -Path "{VIEW_DIR}/form.php" -Pattern 'type="hidden"' | Measure-Object).Count
```

#### 9b. Count Documented Items from Brain
Count what's already documented in each brain file:
- **METHOD_INDEX.md**: Count rows in controller methods table + model methods table
- **DATA_FLOW.md**: Count documented flows (CREATE, EDIT, DELETE, PRINT, etc.)
- **SCHEMA_ANALYSIS.md**: Count tables in Part A (owned) + Part B (referenced)
- **BUSINESS_RULES.md**: Count `RULE-{MODULE}-xxx` entries
- **CROSS_MODULE_MAP.md**: Count dependency entries
- **MODULE_BRAIN.md**: Count hidden fields, settings keys, AJAX endpoints in appendices

#### 9c. Calculate Coverage & Update Tracker

For each metric: `Coverage = (Documented / Total) × 100%`

Status thresholds:
- `0%` → ⬜ Not started
- `1-79%` → 🟡 Partial
- `80-99%` → 🟢 Complete
- `100%` + verified against live DB/code → 🔵 Verified

**Overall Coverage** = weighted average:
- Controller methods: 15%
- Model methods: 18%
- JS AJAX endpoints: 12%
- DB tables (owned): 12%
- Business rules: 10%
- Views/templates: 8%
- Data flows: 10%
- Flow risk contracts: 10%
- Hidden fields + settings: 5%

#### 9d. Write/Update COVERAGE_TRACKER.md

1. If file doesn't exist → copy template from `knowledge_brain/_TEMPLATE/COVERAGE_TRACKER.md`
2. Update the Coverage Summary table with current counts
3. Add a new **Round History** block with:
   - What was done this round
   - Before/After delta for each metric
   - Gaps found and fixed
4. Update the **Known Gaps** table with remaining undocumented areas
5. Update the **Verification Log** if any live DB / code verification was done

> ⚠️ **NEVER skip this step.** Even if no brain files changed this round (e.g., only verification), the tracker still records what was checked and confirms coverage hasn't regressed.

## Completion Report
When done, report to user:
```
✅ Module Brain — Round {N} complete for {MODULE_NAME}

   📊 COVERAGE PROGRESS:
   ┌─────────────────────────┬──────────┬──────────┬──────────┐
   │ Metric                  │ Covered  │ Total    │ Coverage │
   ├─────────────────────────┼──────────┼──────────┼──────────┤
   │ Controller methods      │ {N}      │ {N}      │ {N}%     │
   │ Model methods           │ {N}      │ {N}      │ {N}%     │
   │ JS AJAX endpoints       │ {N}      │ {N}      │ {N}%     │
   │ DB tables (owned)       │ {N}      │ {N}      │ {N}%     │
   │ DB tables (referenced)  │ {N}      │ {N}      │ {N}%     │
   │ Business rules          │ {N}      │ —        │ —        │
   │ Views/templates         │ {N}      │ {N}      │ {N}%     │
   │ Data flows (CRUD+)      │ {N}      │ {N}      │ {N}%     │
   ├─────────────────────────┼──────────┼──────────┼──────────┤
   │ OVERALL                 │          │          │ {N}%     │
   └─────────────────────────┴──────────┴──────────┴──────────┘

   Δ This round: +{N}% overall ({N} gaps found, {N} fixed)
   Remaining gaps: {N}

   Brain location: knowledge_brain/{MODULE_NAME}/
   Coverage tracker: knowledge_brain/{MODULE_NAME}/COVERAGE_TRACKER.md
   Ready for: /module-bug-audit (when overall ≥ 80%)
```

## Time Estimate
- Simple module (< 5K lines total, < 5 tables): ~30 minutes
- Medium module (5-15K lines, 5-15 tables): ~1 hour
- Complex module (15K+ lines, 15+ tables): ~2 hours
- Coverage scan (Step 9): ~5 minutes per round
