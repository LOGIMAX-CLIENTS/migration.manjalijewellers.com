---
description: Post-fix knowledge capture — update Module Brain, pattern library, and track metrics for continuous improvement
version: 2.0
last_updated: 2026-03-16
---

# Learn & Improve Workflow

## Purpose

After every fix (or batch of fixes), update the knowledge base so the system gets **faster over time**. This is the feedback loop that makes pattern reuse possible and prevents re-inventing the wheel.

## When to Run

- After every approved fix via `/fix-single-bug` Step 8
- After every completed sprint (batch update)
- Monthly (metrics review + SOP update)

## Steps

### Step 0: Quick Impact Assessment [Antigravity]

Before updating, quickly assess what this fix changed:

| Change Type                         | If Yes → Update                    | If No → Skip              |
| ----------------------------------- | ---------------------------------- | ------------------------- |
| Method signature/behavior changed?  | METHOD_INDEX.md (Step 3)           | Skip Step 3               |
| Data flow / AJAX endpoint changed?  | DATA_FLOW.md                       | Skip data flow updates    |
| Business rule corrected or added?   | BUSINESS_RULES.md (Step 1 § notes) | Skip business rule update |
| Schema changed (ALTER TABLE)?       | SCHEMA_ANALYSIS.md                 | Skip schema update        |
| Variant-specific behavior changed?  | INVARIANT_MATRIX.md                | Skip matrix update        |
| Cross-module tables/models touched? | \_SYSTEM/ documents (Step 1d)      | Skip \_SYSTEM/ update     |

> Always update: Anti-patterns register (Step 1), Pattern library (Step 2), Execution plan (Step 4), Fix report (Step 4b), GitHub Issue (Step 4d), Active Bug Tracker (Step 4e)

### Step 0b: Capture Fix Velocity [Antigravity]

Record timing data for sprint planning and velocity metrics:

1. Read the bug's "Started" timestamp from `bug_report_AI/ACTIVE_BUGS.md`
2. Calculate duration: `{NOW} - {Started}`
3. Append to `bug_report_AI/FIX_VELOCITY.md` (create if not exists):
   ```
   | {BUG_ID} | {MODULE} | {SEVERITY} | {TRACK} | {CATEGORY} | {duration_minutes} | {DATE} |
   ```
4. After 10+ entries, the velocity data enables sprint time estimates:
   - "Transaction bugs average X minutes"
   - "Business bugs average Y minutes"

### Step 1: Update Module Brain [Antigravity]

For each bug fixed, update the Module Brain. Target these sections specifically:

- **§10 Bug Root Cause Register** — anti-pattern entry
- **§11 Code Anti-Patterns Map** — before/after code patterns (if coding anti-pattern)
- **§12 Function-Level Code Map** — if function reads/writes changed
- **§13 Field-Level Trace** — if data flow changed
- **§15 Unit Test Derivation Map** — new test cases from this bug
- **§21 Gaps, Risks & Technical Debt** — mark fixed debt, add new debt

#### 1a. Anti-Pattern Entry

Add to `MODULE_BRAIN.md` → Anti-Patterns Register section:

```markdown
### {BUG_ID}: {Bug Title} ✅ FIXED

| Bug ID   | Anti-Pattern              | Fix Applied               | Date   |
| -------- | ------------------------- | ------------------------- | ------ |
| {BUG_ID} | {What was wrong — 1 line} | {What was fixed — 1 line} | {date} |

**Prevention**: {What coding practice would have prevented this?}
```

#### 1b. "Why It Was Done This Way" Note

If the fix reveals **developer intent** that wasn't obvious, add a note:

```markdown
> **Note ({BUG_ID})**: The `DELETE-then-INSERT` pattern in estimation edit
> was intentional to avoid UPDATE complexity with variable-length child arrays.
> However, it creates a data-loss window if the INSERT fails after DELETE.
```

#### 1c. Field-Level Data Flow (if applicable)

If the bug involved a field where data flows across layers, document:

```markdown
| Field        | DB Column          | PHP Variable     | JS Variable | HTML Element    |
| ------------ | ------------------ | ---------------- | ----------- | --------------- |
| {field_name} | `{table}.{column}` | `$data['{key}']` | `{js_var}`  | `#{element_id}` |
```

#### 1d. Update System Brain (if cross-module impact) [Antigravity]

If `knowledge_brain/_SYSTEM/` exists AND the fix touched a shared table or model:

1. Update `_SYSTEM/CROSS_MODULE_BUGS.md` — add resolved bug entry with date and fix summary
2. If a cleanup gap was fixed → update `_SYSTEM/CLEANUP_GAPS.md` (mark as resolved)
3. If a validation gap was fixed → update `_SYSTEM/VALIDATION_GAPS.md` (mark as resolved)
4. If a hardcoded value was replaced with config → update `_SYSTEM/HARDCODED_VALUES.md` (mark as resolved)
5. If a new cross-module risk was discovered during the fix → add to the appropriate `_SYSTEM/` document
6. If `_SYSTEM/` does NOT exist → skip this step

### Step 2: Update Pattern Library [Antigravity]

// turbo

1. Read `bug_report_AI/COMMON_BUG_PATTERNS.md`
2. For the fixed bug:

**If it matched an existing pattern:**

- Find the pattern entry (e.g., `PAT-SEC-001`)
- Add `{MODULE_NAME}` to "Modules Found In" if not already listed
- If the fix deviated from the template, update the fix template with the improved version

**If it's a genuinely new pattern:**

- Generate next Pattern ID: `PAT-{CATEGORY}-{SEQ}`
- Add full entry:
  - Pattern ID
  - Category + Default Severity
  - Description: what the bug is and why it's dangerous
  - Detection Rule: grep/SQL command to find it in any module
  - Fix Template: before/after code
  - Modules Found In: `{MODULE_NAME}`

### Step 2b: POSTMORTEM CAPTURE [Antigravity — if AI diagnosis was wrong]

If the final root cause differs from the AI's initial diagnosis:

1. Open `bug_report_AI/POSTMORTEM_LOG.md`
2. Add a new entry with:
   - **Symptom**: What was reported
   - **What AI Suggested (WRONG)**: The incorrect diagnosis/fix
   - **What Was Actually Wrong**: The real root cause
   - **Why AI Was Wrong**: What thinking pattern failed
   - **Correct Approach**: What should have been done
   - **Lesson**: One-sentence rule (bolded)
3. Generate next POST-{NNN} ID

> [!IMPORTANT]
> **This step is how the system learns WITHOUT interviewing senior developers.**
> Every wrong diagnosis becomes a rule that prevents the same mistake.

### Step 2c: UPDATE DIAGNOSTIC PLAYBOOK [Antigravity]

If this fix reveals a new symptom → suspect mapping:

1. Open `knowledge_brain/_SYSTEM/DIAGNOSTIC_PLAYBOOK.md`
2. Does the symptom match an existing RULE-DX-{NNN}?
   - **Yes**: Add this module/scenario to the existing rule's examples
   - **No**: Add a new rule with:
     - Symptom category
     - **SUSPECT FIRST** order (what to investigate first → second → third)
     - **NEVER DO** list (wrong approaches)
     - Ripple check items (if applicable)
3. If a new NEVER rule was discovered → add to `knowledge_brain/_SYSTEM/DANGER_ZONES.md`
4. Generate next RULE-DX-{NNN} ID

### Step 3: Update METHOD_INDEX (if applicable) [Antigravity]

If the fix changed:

- A method's table access pattern → update METHOD_INDEX.md tables columns
- A method's caller → update the caller column
- Added a new method → add alphabetical entry
- Removed a method → remove entry

### Step 4: Update Execution Plan / Bug Report [Antigravity]

// turbo

1. In `*_BUG_FIX_EXECUTION_PLAN.md`: mark the bug as ✅ Fixed with date
2. In `CONSOLIDATED_BUG_REPORT.md`: update the master index status
3. Track completion rate: `{fixed}/{total}` bugs

### Step 4b: Generate Fix Report Entry [Antigravity]

Append to `bug_report_AI/{module}/{MODULE}_FIX_REPORT.md` (create if doesn't exist):

```markdown
---
### Fix: {BUG_ID} — {BUG_TITLE}
- **Date:** {TODAY}
- **Track:** A (System) / B (Business)
- **Category:** {category}
- **Severity:** {P0/P1/P2/P3}
- **Files Changed:** {list}
- **Root Cause:** {brief}
- **Fix Applied:** {brief}
- **Tests:** {PASS/FAIL — N tests, M assertions}
- **Pattern:** {PAT-XXX matched / NEW pattern added / N/A}
- **Rollback:** {reference to rollback plan}
---
```

### Step 4d: Close GitHub Issue [Antigravity]

> See `/github-bug-tracking` Part 4 for full details.

1. Find the GitHub Issue number from the local execution plan (`GitHub: #{N}`)
2. Add close comment via platform dispatch → `Add comment` (see `/github-bug-tracking`):
   - Fix summary, files changed, root cause, test results, pattern match
3. Close issue via platform dispatch → `Close issue` (see `/github-bug-tracking`):
   - **State**: `closed`
   - **State Reason**: `completed`
4. Remove `status:testing` label (severity/track/module labels stay for historical tracking)

### Step 4e: Update Active Bug Tracker [Antigravity]

1. Open `bug_report_AI/ACTIVE_BUGS.md`
2. Move the bug from **In-Progress** to **Recently Completed**:
   ```
   | {BUG_ID} | {MODULE} | {SEVERITY} | {1-line fix summary} | {NOW} | {duration} | #{ISSUE_NUMBER} |
   ```
3. Keep only the last 10 entries in Recently Completed (archive older ones)

### Step 4f: Post-Fix Closure Checklist [Antigravity]

Verify all items before closing the bug:

- [ ] Module Brain updated with anti-pattern (Step 1)
- [ ] COMMON_BUG_PATTERNS.md updated (Step 2)
- [ ] METHOD_INDEX.md updated if applicable (Step 3)
- [ ] Execution plan + consolidated report updated (Step 4)
- [ ] Fix report entry generated (Step 4b)
- [ ] GitHub Issue closed with fix summary (Step 4d)
- [ ] Active Bug Tracker updated (Step 4e)
- [ ] Fix velocity captured (Step 0b)
- [ ] If new business rule discovered → added to BUSINESS_RULES.md
- [ ] If INVARIANT_MATRIX exists and variant behavior changed → updated
- [ ] If cross-module tables touched → `_SYSTEM/` documents updated (Step 1d)
- [ ] Bug is officially **CLOSED**

### Step 5: Monthly Metrics Review [Human + Antigravity]

Run this monthly (or after completing a sprint):

#### 5a. Metrics to Track

| Metric                         | Target                 | Current |
| ------------------------------ | ---------------------- | ------- |
| Time to Acknowledge (client)   | ≤ 2h                   |         |
| Time to Acknowledge (internal) | ≤ 4h                   |         |
| Time to Fix (P0)               | Same day               |         |
| Time to Fix (P1)               | ≤ 3 business days      |         |
| First-Fix Success Rate         | ≥ 90% (no regressions) |         |
| Bugs per Module per Month      | Trending ↓             |         |
| Pattern Library Reuse Rate     | Trending ↑             |         |

#### 5b. Monthly Review Checklist

- [ ] Review metrics with the team
- [ ] Identify top 3 buggiest modules → prioritize Brain building for unbuilt modules
- [ ] Identify recurring patterns → add to `COMMON_BUG_PATTERNS.md`
- [ ] Check if any Module Brain is stale (code changed but brain not updated)
- [ ] Update the SOP (`FINAL_SOP_BUG_REMEDIATION.md`) if process gaps found
- [ ] Archive completed sprints

### Step 6: Brain Staleness Check [Antigravity]

// turbo
For each module with a Brain:

1. Check if the controller/model/JS files have been modified since the Brain was last updated
2. If stale → flag: "Module Brain for {MODULE_NAME} may be outdated — re-run `/build-module-brain` (incremental)"
3. Priority: modules with active bugs get updated first

## Completion Report

```
✅ Learn & Improve cycle complete
   Brain updates: {N} modules updated
   Anti-patterns added: {N} entries
   Pattern library: {N} existing patterns updated, {M} new patterns added
   METHOD_INDEX: {N} entries updated
   Bug tracking: {fixed}/{total} bugs resolved

   Staleness check:
   ├── Up-to-date: {list}
   └── Stale (needs update): {list}
```

## Output

- Updated `MODULE_BRAIN.md` with anti-patterns
- Updated `COMMON_BUG_PATTERNS.md` with new/updated patterns
- Updated `METHOD_INDEX.md` (if applicable)
- Updated execution plan and consolidated report
- Monthly metrics snapshot (if monthly review)
