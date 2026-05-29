---
description: "Phase 0 — Build a Module Brain for any module before bug fixing."
---

# /build-module-brain — Phase 0: Module Brain Building

> **Automation Level:** 90%  
> **Source:** MASTER_BUG_REMEDIATION_PROCEDURE.md § 7

## Prerequisites
- Module name and prefix known
- Access to controller, model, JS, and view files

## Step 1: Identify Module Files
// turbo
Run to find all relevant module files:
```
Get-ChildItem -Path "c:\xampp 7.1\htdocs\etail_development_src\admin\application" -Filter "*{MODULE_SNAKE}*" -Recurse | Select Name, Length, FullName
```

## Step 2: Fill Module Variables Template
Create `SOP/modules/{MODULE_NAME}_variables.md` with all variables from MASTER_BUG_REMEDIATION_PROCEDURE.md § 2.

## Step 3: Build Component 1 — Project Skeleton
Use `view_file_outline` on controller, model, JS, views.
- List every file, its role, and connections
- Draw: Browser → JS → Controller → Model → DB → View
- Save to: `AI_docs/{MODULE_NAME}/module_brain/PROJECT_SKELETON.md`

## Step 4: Build Component 2 — Engine Reverse Engineering
For each PRIMARY ACTION (Save, Edit, Delete, Print, List):
- Trace JS → AJAX → Controller → Model → DB → Response
- Document complete chain with file names and functions
- Save to: `AI_docs/{MODULE_NAME}/module_brain/DATA_FLOW.md`

## Step 5: Build Component 3 — Canonical Business Rules
- Extract every calculation from code (JS + PHP)
- Write each as plain-English rule WITH formula
- Flag where JS and PHP differ
- Save to: `AI_docs/{MODULE_NAME}/module_brain/BUSINESS_RULES.md`

## Step 6: Build Component 4 — Cross-Module Mapping
- Identify external tables/functions called
- Group by module, document data IN/OUT
- Flag dangerous connections
- Save to: `AI_docs/{MODULE_NAME}/module_brain/CROSS_MODULE_MAP.md`

## Step 7: Build Component 5 — Invariant Matrix
- List variant types, define dimensions, fill behavior grid
- Save to: `AI_docs/{MODULE_NAME}/module_brain/INVARIANT_MATRIX.md`

## Step 8: Build Component 6 — DB Truth Protocol
- Write diagnostic SQL queries for primary entities
- Save to: `AI_docs/{MODULE_NAME}/module_brain/DB_TRUTH_PROTOCOL.sql`

## Step 9: Build Component 7 — Forensic Template
- Create layer-by-layer investigation template for this module
- Save to: `AI_docs/{MODULE_NAME}/module_brain/FORENSIC_TEMPLATE.md`

## Step 10: Create MODULE_BRAIN.md Master Index
- Link all 7 components
- Save to: `AI_docs/{MODULE_NAME}/module_brain/MODULE_BRAIN.md`

## Fast Brain (Urgent P0/P1)
Build ONLY Components 1, 2, 6 (~4 hours total).
