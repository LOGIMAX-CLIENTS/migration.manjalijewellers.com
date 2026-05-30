---
description: Fix a Track B (Business-Level) bug — Antigravity proposes, human validates business logic before any change
version: 1.3
last_updated: 2026-03-04
---

# Fix Business Bug Workflow

> **SOP Reference**: FINAL_SOP_BUG_REMEDIATION.md § 6 — Track B
> **Automation Level**: 50% — Antigravity investigates + proposes. Human validates business correctness.
> **Time Target**: < 4 hours per bug (includes business validation)
> **Applies to**: Calculation Errors, Missing Business Rules, Incorrect Workflows, Data Integrity, Configuration Bugs, Cross-Module Logic

## When to Use

- Called from `/fix-single-bug` Step 3 when ASSESS determines **Track B (Business)**
- The code works but produces **wrong business results**
- Antigravity NEVER auto-fixes business logic — human MUST validate

## Core Principles

> **The code works but produces WRONG business results.**
> **Antigravity NEVER auto-fixes business logic. Human MUST validate.**
> **Configuration bugs require BUSINESS DECISIONS — not code fixes.**

## Prerequisites

- Bug ID assigned
- Bug triaged via `/bug-intake-triage` (severity, track=B, category assigned)
- Execution plan: `bug_report_AI/{module}/`

### Step 0: MODULE BRAIN CHECK [Antigravity]

Check if `knowledge_brain/{MODULE_NAME}/MODULE_BRAIN.md` exists:

- **Exists** → Load brain context (especially `BUSINESS_RULES.md`, `INVARIANT_MATRIX.md`, `SCHEMA_ANALYSIS.md`), proceed to Step 1
- **Missing** → Display warning:

  ```
  ⚠️ MODULE BRAIN NOT FOUND for {MODULE_NAME}
  Business bug diagnosis WITHOUT brain is HIGH RISK:
    - No canonical business rules to validate against
    - No invariant matrix for variant-aware testing
    - No schema analysis for DB truth protocol

  STRONGLY RECOMMENDED: Run /build-module-brain first.
  Options:
    1. Run /build-module-brain now (adds ~1 hour but prevents wrong fixes)
    2. Continue without brain (risky — human must validate ALL business logic manually)
  ```

### Step 0b: LOAD CENTRAL CONFIG [Antigravity]

Read `.agent/config.md` → load module paths and shared variables.
Read `.agent/.env` → load `{PHP_PATH}`, `{PROJECT_ROOT}`, `{ISSUE_PLATFORM}`, `{REPO_OWNER}`, `{REPO_NAME}`.
For issue operations → use platform dispatch (see `/github-bug-tracking`).

> [!NOTE]
> After applying a fix in Step 6, append the rollback plan to `bug_report_AI/ROLLBACK_REGISTRY.md`.
> This ensures all rollback plans are centrally accessible during emergencies.

## Steps

### Step 1: IDENTIFY VIOLATED BUSINESS RULE [Antigravity]

1. Read bug description from execution plan
2. Read `knowledge_brain/{MODULE_NAME}/BUSINESS_RULES.md`
3. Identify which specific rule is being violated:
   - Match against `RULE-{MODULE}-NNN` entries
   - If no canonical rule exists for this scenario → flag as **"rule gap"**
4. Check `INVARIANT_MATRIX.md` (if exists):
   - Does this bug only affect specific variants (scheme types, GST modes, etc.)?
   - Which behavior grid cell is wrong?

### Step 2: RUN DB TRUTH PROTOCOL [Antigravity]

1. Read `knowledge_brain/{MODULE_NAME}/SCHEMA_ANALYSIS.md` or DB verification queries from `MODULE_BRAIN.md`
2. Execute diagnostic SQL queries for the affected entity:
   - Pull complete transaction (header + child records)
   - Calculate expected total using raw DB values
   - Check for integrity issues
3. Document:
   - **What the database actually stores** (actual value)
   - **What the business rule says it SHOULD store** (expected value)
   - **The delta** (difference) — this is the bug's fingerprint

### Step 3: LAYER-BY-LAYER TRACE [Antigravity]

Trace the calculation/logic through ALL layers, capturing **exact values** at each boundary:

```
┌──────────────────────────────────────────────────────┐
│ JS Layer:                                             │
│   What does the client-side calculation produce?      │
│   What values are sent to server?                     │
│   → VALUE AT EXIT: {exact value}                      │
│                                                       │
│ Controller Layer:                                     │
│   What does the server receive?                       │
│   How does it process the data?                       │
│   → VALUE AT EXIT: {exact value}                      │
│                                                       │
│ Model Layer:                                          │
│   What SQL is executed?                               │
│   What values are stored?                             │
│   → VALUE AT EXIT: {exact value}                      │
│                                                       │
│ DB Layer:                                             │
│   What is actually stored?                            │
│   Does it match the canonical rule?                   │
│   → STORED VALUE: {exact value}                       │
└──────────────────────────────────────────────────────┘
```

Identify the **EXACT layer** where the wrong value is introduced.

### Step 4: PRESENT FINDINGS [Antigravity → Human]

Present a structured analysis:

```markdown
### Business Bug Analysis: {BUG_ID}

#### 1. Violated Business Rule

- **Rule:** {RULE-MODULE-NNN from canonical rules, or "NEW — rule gap"}
- **Expected Behavior:** {what should happen}
- **Actual Behavior:** {what actually happens}

#### 2. DB Truth Protocol Results

- **Stored Value:** {value in database}
- **Expected Value:** {value per canonical rule}
- **Delta:** {difference — e.g., "GST calculated as inclusive but should be exclusive"}

#### 3. Fault Location

- **Layer:** {JS / Controller / Model / View}
- **File:** {file path}
- **Line:** {line number}
- **Current Code:**
```

{current code snippet}

```

#### 4. Proposed Fix
- **Change:** {what to change}
- **New Logic:**
```

{proposed code}

```

#### 5. Risk Assessment
- **Could this break other configurations?** {YES/NO + explanation}
- **Variant-specific?** {Check Invariant Matrix if applicable}
- **Cross-module impact?** {Check Cross-Module Map}

#### 6. Options (if configuration bug)
- Option A: {description}
- Option B: {description}
- ⚠️ This requires a BUSINESS DECISION — not a code fix
```

### ⏸️ Step 5: HUMAN BUSINESS VALIDATION [Human]

> [!CAUTION]
> **CRITICAL GATE: No code changes until the human confirms the business logic.**

Human must answer:

1. **Is the identified business rule correct?** (Confirm / Correct / Clarify)
2. **Is the proposed fix formula/logic correct?** (Approve / Modify)
3. **For calculation bugs:** What IS the correct formula?
4. **For missing rules:** What IS the correct constraint?
5. **For workflow bugs:** What IS the correct sequence?
6. **For configuration bugs:** Which option? (This is a business decision)

Human actions:

- **CONFIRM + APPROVE** → Proceed to Step 6
- **CORRECT** → Provide the correct formula/rule, Antigravity re-proposes at Step 4
- **DEFER** → Bug requires further investigation / stakeholder input
- **REJECT** → Not a bug / working as intended

### Step 6: APPLY FIX [Antigravity — only after human confirms]

**Sub-Procedures by Category:**

#### A. Calculation Error Fixes

1. Fix in ALL layers where the calculation exists (JS AND PHP)
2. Ensure JS and PHP implementations produce **identical results**
3. Apply fix using the **human-confirmed formula**
4. Add comment: `// Business Rule: {RULE_NAME} — {BUG_ID}`
5. If INVARIANT_MATRIX exists, verify fix works for ALL affected variants

#### B. Missing Business Rule Fixes

1. Determine enforcement location:
   - Client-side only (UX convenience)?
   - Server-side only (security)?
   - Both (defense in depth)?
2. Apply guard clause or validation at the identified location(s)
3. Add user-facing error message (if client-side)
4. Add to `BUSINESS_RULES.md` as a new canonical rule

#### C. Configuration Bug Fixes

> [!WARNING]
> Only apply after receiving **written confirmation** of correct configuration from business stakeholder.

1. Document the configuration decision
2. Apply the agreed configuration change
3. Note: This is NOT a code fix — it's a data correction

#### D. Incorrect Workflow Fixes

1. Map the current status transition sequence
2. Compare against the human-confirmed correct sequence
3. Apply fix to status update logic
4. Test all transition paths (happy path + edge cases)

#### E. Cross-Module Logic Fixes

1. Apply fix in the **SOURCE module** (where wrong data originates)
2. DO NOT change the receiving module unless confirmed
3. Test end-to-end across both modules

### Step 7: SYNTAX CHECK [Antigravity]

// turbo

```powershell
& "{PHP_PATH}" -l {affected_php_file}
```

### Step 8: WRITE SCENARIO-BASED TESTS [Antigravity]

Run via `/test-and-verify`. Business-level tests MUST include:

- **Correct result for normal input** (the happy path)
- **Edge cases:** 0, negative, very large numbers
- **Boundary values:** at the limits of the rule
- **Real-world data:** use actual transaction values from the DB
- **Variant coverage:** if Invariant Matrix exists, test at least one case per affected variant

// turbo

```powershell
cd "{PROJECT_ROOT}\{TEST_DIR}" && & "{PHP_PATH}" vendor/bin/phpunit --no-configuration {TestFile}.php --testdox
```

### Step 9: ROLLBACK PLAN [Antigravity]

Document before proceeding to human review:

```markdown
### Rollback Plan for {BUG_ID}

1. Revert file(s): {FILE_PATHS}
2. Restore original formula/logic at line(s) {LINES}
3. Note: Rollback will RE-INTRODUCE the business logic error
4. If rollback is needed, the previous (incorrect) behavior returns
```

### ⏸️ Step 10: HUMAN BUSINESS SMOKE TEST [Human]

Human must verify with a **REAL transaction**:

- [ ] Create a new entity/transaction using the fixed logic
- [ ] Verify the output matches the expected business result
- [ ] Compare with a known-good historical transaction
- [ ] Check all variants (from Invariant Matrix) that could be affected
- [ ] If financial: verify amount/weight/GST exactly match expectations

**APPROVE** → Proceed to `/learn-and-improve`
**REJECT** → Revert, re-investigate

## Post-Fix: Update Business Rules

1. If the fix reveals a new business rule → add to `BUSINESS_RULES.md`
2. If a rule was WRONG → correct it in the Brain
3. Update `INVARIANT_MATRIX.md` if variant-specific behavior changed

## Completion Report

```
✅ Bug {BUG_ID} — {TITLE} (Track B: Business)
   Category: {category}
   Business Rule: {RULE-MODULE-NNN}
   Fix: {1-line summary — human-confirmed formula/logic}
   File: {path}:{lines}
   Tests: {N} tests, {M} assertions — all passed
   Human Validation: ✅ Business logic confirmed by {name}
   Risk: {Low/Medium/High}
   Rollback: {1-line rollback instruction}
   Next: /learn-and-improve → /fix-single-bug {NEXT_BUG_ID}
```
