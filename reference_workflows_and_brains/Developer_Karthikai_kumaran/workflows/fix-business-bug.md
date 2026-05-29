---
description: "Fix a Business-Level (Level 3) bug — human-led. Antigravity proposes, human validates business logic."
---

# /fix-business-bug — Level 3: Business-Level Bug Fix

> **Source:** MASTER_BUG_REMEDIATION_PROCEDURE.md § 6, FINAL_SOP_BUG_REMEDIATION.md § 6, SOPforCRM.md
> **Automation Level:** 50% — Antigravity investigates + proposes. Human validates business correctness.
> **Time Target:** < 4 hours per bug (includes business validation)
> **Applies to:** Calculation Errors, Missing Business Rules, Incorrect Workflows, Data Integrity, Configuration Bugs, Cross-Module Logic

## Prerequisites
- Bug ID assigned
- Module Brain exists (especially Components 3, 5, 6)
- For config-driven modules (Scheme, Payment): metadata_dictionary.json available

## Core Principles
> **The code works but produces WRONG business results.**
> **Antigravity NEVER auto-fixes business logic. Human MUST validate.**
> **Configuration bugs require BUSINESS DECISIONS — not code fixes.**

---

## Step 1: IDENTIFY VIOLATED BUSINESS RULE [🤖 AUTO]

1. Read bug description from execution plan
2. Read Module Brain Component 3 (Canonical Business Rules)
3. Identify which specific rule is being violated
4. If no canonical rule exists for this scenario → flag as "rule gap"

## Step 2: RUN DB TRUTH PROTOCOL [🤖 AUTO]

1. Read Module Brain Component 6 (DB Truth Protocol)
2. Execute diagnostic SQL queries for the affected entity
3. Document:
   - What the database actually stores
   - What the business rule says it SHOULD store
   - The delta (difference)

// turbo
```
& "C:\xampp 7.1\mysql\bin\mysql.exe" -u root -e "{DIAGNOSTIC_QUERY}" {database_name}
```

## Step 3: LAYER-BY-LAYER TRACE [🤖 AUTO]

Trace the calculation/logic through ALL layers:

```
┌──────────────────────────────────────────────┐
│ JS Layer:                                     │
│   What does the client-side calculation       │
│   produce? What values are sent to server?    │
│                                               │
│ Controller Layer:                             │
│   What does the server receive?               │
│   How does it process the data?               │
│                                               │
│ Model Layer:                                  │
│   What SQL is executed?                       │
│   What values are stored?                     │
│                                               │
│ DB Layer:                                     │
│   What is actually stored?                    │
│   Does it match the canonical rule?           │
└──────────────────────────────────────────────┘
```

For each layer, capture the EXACT value at entry and exit.
Identify the EXACT layer where the wrong value is introduced.

## Step 4: PRESENT FINDINGS [🤖 AUTO → 👤 HUMAN]

Present a structured analysis:

```markdown
### Business Bug Analysis: {BUG_ID}

#### 1. Violated Business Rule
- **Rule:** {rule name from canonical rules}
- **Expected Behavior:** {what should happen}
- **Actual Behavior:** {what actually happens}

#### 2. DB Truth Protocol Results
- **Stored Value:** {value in database}
- **Expected Value:** {value per canonical rule}
- **Delta:** {difference}

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
- **Variant-specific?** {Check Invariant Matrix}
- **Cross-module impact?** {Check Cross-Module Map}

#### 6. Options (if configuration bug)
- Option A: {description}
- Option B: {description}
- ⚠️ This requires a BUSINESS DECISION
```

## ⏸️ Step 5: HUMAN BUSINESS VALIDATION [👤 HUMAN]

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
- **CORRECT** → Provide the correct formula/rule, Antigravity re-proposes
- **DEFER** → Bug requires further investigation / stakeholder input
- **REJECT** → Not a bug / working as intended

## Step 6: APPLY FIX [🤖 AUTO, only after human confirms]

### Business-Level Sub-Procedures:

#### A. Calculation Error Fixes
```
1. Fix in ALL layers where the calculation exists (JS AND PHP)
2. Ensure JS and PHP implementations produce identical results
3. Apply fix using the human-confirmed formula
4. Add comment: // Business Rule: {RULE_NAME} — {BUG_ID}
```

#### B. Missing Business Rule Fixes
```
1. Determine enforcement location:
   - Client-side only (UX convenience)?
   - Server-side only (security)?
   - Both (defense in depth)?
2. Apply guard clause or validation at the identified location(s)
3. Add user-facing error message (if client-side)
```

#### C. Configuration Bug Fixes
```
⚠️ Only apply after receiving WRITTEN confirmation of correct configuration

1. Document the configuration decision by the business stakeholder
2. Apply the agreed configuration change
3. Note: This is NOT a code fix — it's a data correction
```

#### D. Cross-Module Logic Fixes
```
1. Apply fix in the SOURCE module (where wrong data originates)
2. DO NOT change the receiving module unless confirmed
3. Test end-to-end across both modules
```

## Step 7: SYNTAX CHECK [🤖 AUTO]

// turbo
```
& "C:\php8\php.exe" -l "{FIXED_FILE_PATH}"
```

## Step 8: WRITE SCENARIO-BASED TESTS [🤖 AUTO]

Business-level tests MUST include:
- **Correct result for normal input** (the happy path)
- **Edge cases:** 0, negative, very large numbers
- **Boundary values:** at the limits of the rule
- **Real-world data:** use actual transaction values from the DB

// turbo
```
cd "c:\xampp 7.1\htdocs\etail_development_src\admin\tests" && & "C:\php8\php.exe" vendor/bin/phpunit --no-configuration {BUG_ID}Test.php --testdox
```

## Step 9: ROLLBACK PLAN [🤖 AUTO]

```markdown
### Rollback Plan for {BUG_ID}
1. Revert file(s): {FILE_PATHS}
2. Restore original formula/logic at line(s) {LINES}
3. Note: Rollback will RE-INTRODUCE the business logic error
4. If rollback is needed, the previous (incorrect) behavior returns
```

## Step 10: HUMAN BUSINESS SMOKE TEST [👤 HUMAN]

Human must verify with a REAL transaction:
- [ ] Create a new entity/transaction using the fixed logic
- [ ] Verify the output matches the expected business result
- [ ] Compare with a known-good historical transaction
- [ ] Check all variants (from Invariant Matrix) that could be affected

**APPROVE** → Proceed to knowledge update
**REJECT** → Revert, re-investigate

## Post-Fix: Update Business Rules

1. If the fix reveals a new business rule → add to Canonical Rules (Brain Component 3)
2. If a rule was WRONG → correct it in the Brain
3. Update Invariant Matrix if variant-specific behavior changed
