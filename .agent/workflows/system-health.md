---
description: Cross-module risk heatmap and system-wide bug analysis from all audit data
version: 1.0
last_updated: 2026-02-19
---

# System Health Workflow

> **Purpose**: Aggregate data across ALL audited modules to show system-wide risk, pattern spread, and hot modules.
> **When to use**: Before sprint planning, management reporting, or when deciding which module to audit next.

## Prerequisites
- At least 2 modules audited via `/module-bug-audit`
- `bug_report_AI/COMMON_BUG_PATTERNS.md` exists

## Steps

### Step 1: Scan All Audit Data [Antigravity]
// turbo
1. List all subdirectories in `bug_report_AI/` (each = one audited module)
2. For each module, read `CONSOLIDATED_BUG_REPORT.md` (or equivalent)
3. Extract: total bugs, bugs by severity, bugs by category, fixed vs open count

### Step 2: Scan Pattern Library [Antigravity]
// turbo
Read `bug_report_AI/COMMON_BUG_PATTERNS.md`:
- For each pattern, note which modules it appears in
- Count: patterns found in 1 module, 2 modules, 3+ modules (systemic)

### Step 3: Generate Risk Heatmap [Antigravity]

```markdown
# System Health Report — {DATE}

## Module Risk Heatmap

| Module | Total Bugs | P0 | P1 | P2 | P3 | Fixed | Open | Risk Score |
|---|---|---|---|---|---|---|---|---|
| {MODULE_1} | {N} | {N} | {N} | {N} | {N} | {N} | {N} | 🔴/🟡/🟢 |
| {MODULE_2} | {N} | {N} | {N} | {N} | {N} | {N} | {N} | 🔴/🟡/🟢 |
| ... | | | | | | | | |

**Risk Score**: 🔴 = any open P0, 🟡 = open P1s, 🟢 = P2/P3 only or all fixed

## Category Distribution (System-Wide)

| Category | Count | Modules Affected | Most Common Fix |
|---|---|---|---|
| Security | {N} | {list} | {PAT-ID or description} |
| Transaction | {N} | {list} | |
| Logic/Calc | {N} | {list} | |
| Validation | {N} | {list} | |
| Query | {N} | {list} | |
| Variable | {N} | {list} | |
| Integration | {N} | {list} | |
| Performance | {N} | {list} | |

## Systemic Patterns (Found in 3+ Modules)

| Pattern | ID | Modules | Severity | Status |
|---|---|---|---|---|
| {Pattern Name} | {PAT-ID} | {list of modules} | {P0/P1/P2} | {N fixed, M open} |

> ⚠️ Systemic patterns should be prioritized — fixing them once creates a template for all modules.

## Unaudited Modules

| Module | Brain Exists? | Estimated Risk | Recommended Action |
|---|---|---|---|
| {MODULE} | Yes/No | High/Medium/Low | Build brain → Audit / Audit next / Low priority |

**Risk estimation for unaudited modules**:
- If similar modules have high bug counts → estimate high
- If module handles money/inventory → estimate high
- If module is rarely used → estimate low

## Recommendations
1. **Next module to audit**: {MODULE} — {reason}
2. **Systemic fix priority**: {PATTERN} — affects {N} modules
3. **Quick wins**: {N} bugs with fix templates ready across all modules
```

### Step 4: Present Report [Antigravity → Human]
Display the report. Save to `bug_report_AI/SYSTEM_HEALTH_{DATE}.md`.
