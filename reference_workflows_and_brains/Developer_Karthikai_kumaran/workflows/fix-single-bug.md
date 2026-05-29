---
description: "Fix any single bug — routes to the correct level-specific workflow (System/Architecture/Business)."
---

# /fix-single-bug — Universal Bug Fix Entry Point

> **Source:** MASTER_BUG_REMEDIATION_PROCEDURE.md, FINAL_SOP_BUG_REMEDIATION.md
> **Purpose:** Single entry point for fixing any bug. Classifies and routes to the correct workflow.

## Quick Start

Tell Antigravity: **"Fix bug {BUG_ID}"** or use `/fix-single-bug {BUG_ID}`

---

## Step 1: Identify the Bug

1. Read the bug from the execution plan:
   - Look in `bug_report_AI/{module_name}/{MODULE}_BUG_FIX_EXECUTION_PLAN.md`
   - Or from the consolidated report: `bug_report_AI/{module_name}/CONSOLIDATED_BUG_REPORT.md`
   - Or from a manually filed bug card

2. Extract:
   - Bug ID
   - Module name
   - Category (1-8)
   - Severity (P0-P3)
   - Level (System / Architecture / Business)

## Step 2: Read Module Variables

Read `SOP/modules/{MODULE_NAME}_variables.md` for:
- Controller, Model, JS, View paths
- Brain directory
- Related modules

## Step 3: Classify & Route

Use the classification flowchart:

```
┌─────────────────────────────────────────────────────────┐
│                   BUG {BUG_ID}                           │
│                                                         │
│   Q1: Does it crash, error, or have a security flaw?    │
│   ├── YES → LEVEL 1 (System)         🟢               │
│   │         Use: /fix-system-bug                        │
│   └── NO ↓                                             │
│                                                         │
│   Q2: Is it about data flow, query design, schema,     │
│       performance, or transactions?                     │
│   ├── YES → LEVEL 2 (Architecture)    🟡              │
│   │         Use: /fix-architecture-bug                  │
│   └── NO ↓                                             │
│                                                         │
│   Q3: Is the output/calculation/workflow wrong          │
│       per business rules?                               │
│   ├── YES → LEVEL 3 (Business)        🔴              │
│   │         Use: /fix-business-bug                      │
│   └── NO → Re-evaluate or mark P3/Backlog              │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

## Step 4: Execute the Level-Specific Workflow

Follow the routed workflow file:
- **🟢 System:** `.agent/workflows/fix-system-bug.md`
- **🟡 Architecture:** `.agent/workflows/fix-architecture-bug.md`
- **🔴 Business:** `.agent/workflows/fix-business-bug.md`

## Step 5: Post-Fix (All Levels)

After the fix is approved:
1. Run `/update-knowledge-base` to update the Module Brain and pattern library
2. Update the bug status in the execution plan
3. Log the fix in the module's fix report

---

## Quick Reference: Antigravity Prompts

### 🟢 System-Level Bug (Auto-fix):
```
Fix system-level bug {BUG_ID} in the {MODULE_NAME} module.
Controller: {CONTROLLER_FILE}, Model: {MODEL_FILE}, JS: {JS_FILE}.
Read the bug from the execution plan, locate exact code, apply minimal fix,
run syntax check, write unit test, run it, document rollback, present diff.
```

### 🟡 Architecture-Level Bug (Propose first):
```
Analyze architecture-level bug {BUG_ID} in the {MODULE_NAME} module.
Brain: {BRAIN_DIR}. Trace data flow through all layers, check cross-module
impact, propose fix with risk assessment and rollback plan.
WAIT for my approval before applying.
```

### 🔴 Business-Level Bug (Investigate):
```
Investigate business-level bug {BUG_ID} in the {MODULE_NAME} module.
Brain: {BRAIN_DIR}. Identify which canonical rule is violated, run DB Truth
Protocol, trace calculation through all layers, identify where wrong value
is introduced. Present findings. WAIT for business validation before fixing.
```

### ⚡ P0 Hotfix (Fast-Track):
```
URGENT P0 bug in {MODULE_NAME}: {DESCRIPTION}.
Build minimum context (file map + affected flow trace + DB diagnostic),
diagnose root cause, apply minimal fix, syntax check, present diff.
Skip full brain building.
```
