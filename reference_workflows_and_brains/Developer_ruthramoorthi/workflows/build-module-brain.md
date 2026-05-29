---
description: Build a complete Module Brain for any module (first-time scan)
---

# Phase 2 — Build Module Brain (First Time Only)

**SOP Reference**: [FINAL_SOP_BUG_REMEDIATION.md](file:///c:/xampp/htdocs/etail_development_src/SOP/FINAL_SOP_BUG_REMEDIATION.md) — Section 3 (Phase 0)
**Reference Brain**: [LOT_MODULE_DIGITAL_BRAIN.md](file:///c:/Users/ruthr/.gemini/antigravity/brain/197a75fb-8a12-43f3-ac7b-2094e2abd221/LOT_MODULE_DIGITAL_BRAIN.md.resolved.5) — 22-section structure

## When to Use
- First time auditing a module
- Module has no existing brain in `knowledge/{MODULE_NAME}/`
- After a major architectural change to a module

## Required Input
Ask the user for these variables (from SOP Appendix B):

| Variable | Example |
|---|---|
| `{MODULE_NAME}` | LOT |
| `{PREFIX}` | LOT |
| `{CONTROLLER_FILE}` | `admin/application/controllers/admin_ret_lot.php` |
| `{MODEL_FILE}` | `admin/application/models/ret_lot_model.php` |
| `{JS_FILE}` | `admin/assets/js/ret_lot.js` |
| `{VIEW_DIR}` | `admin/application/views/lot/` |

## Steps

### LEVEL 1 — SYSTEM LEVEL SCAN

#### 1. File Manifest (Brain §2)
// turbo
Read the controller, model, JS, and view files to build the file manifest:
```
For each file, document:
- File name and absolute path
- File size (line count)
- Role (Controller / Model / View / JS)
- Key functions (name + line range)
```

Output: Table matching Brain §2 format.

#### 2. Entry Points & Routes (Brain §3)
// turbo
Scan `{CONTROLLER_FILE}` for all public methods:
```
For each public method:
- URL route
- HTTP method (GET / POST / AJAX)
- Controller method name + line range
- What view it loads or JSON it returns
- Input parameters ($_POST / $_GET)
- Model methods called
```

Output: Table matching Brain §3 format.

#### 3. Database Interaction Map (Brain §6)
// turbo
Scan `{MODEL_FILE}` for all SQL queries:
```
For each table used:
- Table name
- Operation: READ / WRITE / BOTH
- Key columns referenced
- Business meaning
- JOIN relationships
- Indexes present? (flag missing indexes)
```

Output: Table matching Brain §6 format. Flag:
- 🔴 SQL injection risk (string concatenation in queries)
- 🟡 Performance risk (SELECT *, missing WHERE, missing index)
- 🔴 Data integrity risk (missing transaction, no rollback)

---

### LEVEL 2 — ARCHITECTURE LEVEL SCAN

#### 4. Execution Flow Diagrams (Brain §4)
// turbo
Trace the PRIMARY data flows:
```
For each major operation (Add, Edit, Save, Update, Delete, Cancel):
  Browser → JS function → AJAX/POST → Controller method → Model method → DB tables → Response
  Note error paths and redirects.
```

Output: ASCII flow diagrams matching Brain §4 format.

#### 5. Controller Method Catalog (Brain §5)
// turbo
For each controller method, document:
```
- Method signature + line range
- Switch/case values (if mega-method like lot_inward)
- For each case: lines, input, output, notes
```

Output: Table matching Brain §5 format.

#### 6. State Management Map (Brain §7)
// turbo
Scan `{JS_FILE}` for:
```
Global JS variables:
- Variable name, where declared, where reset, risk level

DOM-based state (hidden fields):
- Element ID/class, purpose, set by whom

Item row DOM state:
- Class selectors used per-row, purpose, set by which function
```

Output: Tables matching Brain §7 format.

#### 7. Data Flow Map (Brain §8)
// turbo
Trace data flow for the primary entity:
```
DB → PHP (model) → PHP (controller) → View (HTML) → JS → User
User → JS → AJAX/POST → Controller → Model → DB
```

Include the Field Name Mapping table:
| DB Column | PHP POST Key | HTML Element | JS Selector |

Output: Matching Brain §8 format.

#### 8. Function-Level Code Map (Brain §12)
// turbo
For each key function in controller, model, and JS:
```
| Function | File:Line | Reads | Writes | Known Gaps |
```

Output: Table matching Brain §12 format.

#### 9. Field-Level Data Flow Trace (Brain §13)
// turbo
For the 5 most critical fields in the module, trace end-to-end:
```
DB column → PHP variable → HTML element → JS selector → Form submit → DB write
Mark ⚠️ BREAK POINTS at each step where data could be lost/wrong.
```

Output: ASCII traces matching Brain §13 format.

---

### LEVEL 3 — BUSINESS LEVEL SCAN

#### 10. Validation Rules Catalog (Brain §9)
// turbo
Scan both `{JS_FILE}` and `{CONTROLLER_FILE}` for validation:
```
For each field:
| Field | Rule | Frontend Validation | Backend Validation | Mismatch? |

Flag:
- ❌ MISSING — no validation exists
- ⚠️ — frontend only (bypass risk)
```

Output: Table matching Brain §9 format.

#### 11. Business Rules Engine (Brain §17)
// turbo
Extract every IF-THEN rule from code:
```
| Rule ID | Condition | Action | Enforcement Location | Type |

Types: HARD (both client+server), SOFT (client only), CONFIG (DB setting), CLIENT_VARIANT
```

Output: Table matching Brain §17 format.

#### 12. Calculation Engine (Brain §18)
// turbo
Extract every formula/calculation:
```
For each formula:
- Name (e.g., "Net Weight Calculation")
- Formula: net_wt = gross_wt - less_wt
- PHP implementation: file + line
- JS implementation: file + line (or ❌ NOT IN JS)
- Edge cases: what happens with 0 / null / negative?
- Rounding rule
```

Output: Matching Brain §18 format.

#### 13. Status Workflow (Brain §19)
// turbo
Document all status values and transitions:
```
| Status | Value | Allowed Actions |

Status Dependency Matrix:
| Current State | Can [Action1]? | Can [Action2]? | ... |
```

Output: Tables matching Brain §19 format.

#### 14. Client-Variant Settings (Brain §20)
// turbo
Scan for settings read from database or config:
```
| Setting Key | Table | Values | Effect on Module |
```

Output: Table matching Brain §20 format.

#### 15. Change Impact Map (Brain §16)
// turbo
For each major code area, document downstream impact:
```
| Change | Affected Files | Downstream Impact | Risk |
```

Output: Table matching Brain §16 format.

---

### LEVEL 4 — DEBUG & SUPPORT (Auto-Generated)

#### 16. Debug Runbooks (Brain §14)
Based on the analysis above, create 3-5 debug runbooks for the most common issues:
```
Each runbook:
- Title: "Symptom description"
- Step-by-step diagnosis guide
- Console commands to check state
- Most likely fix reference
```

#### 17. Unit Test Derivation Map (Brain §15)
Based on business rules and calculations, create initial test cases:
```
| Test ID | Rule/Bug | Scenario | Input | Expected Output |
```

#### 18. Gaps, Risks & Technical Debt (Brain §21)
Compile all flags from the scan:
```
### Missing Validations (🔴 HIGH RISK)
### Security Concerns
### Hardcoded Values
### Performance Risks
```

#### 19. Operational Support Guide (Brain §22)
Create quick-reference tables:
```
### First-Level Support Checklist
| Check Point | How to Verify | Common Fix |

### Common Issues → Quick Fix
| Issue | Go To Section |
```

---

### OUTPUT

#### 20. Save the Brain
Save the complete brain document to:
```
knowledge/{MODULE_NAME}/MODULE_BRAIN.md
```

Structure: 22 sections matching the LOT Brain format exactly.

Also create supporting files:
```
knowledge/{MODULE_NAME}/
├── MODULE_BRAIN.md           ← Main brain (22 sections)
├── DATA_FLOW.md              ← Detailed data flow traces (§4 + §8 + §13 expanded)
├── BUSINESS_RULES.md         ← Full rules + calculations (§17 + §18 expanded)
└── CROSS_MODULE_MAP.md       ← Cross-module dependencies
```

#### 21. Report to User
Tell the user:
- Brain created at `knowledge/{MODULE_NAME}/MODULE_BRAIN.md`
- Total sections filled: X/22
- Key risks found (list top 3-5)
- Missing validations count
- Recommended next step: `/module-bug-audit {MODULE_NAME}`
