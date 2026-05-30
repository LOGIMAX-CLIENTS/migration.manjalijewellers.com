---
description: Set up the Bug Remediation System for an existing client running an older version of the source codebase — copy brain, refine, and scan
version: 2.0
last_updated: 2026-03-16
---

# Setup Existing Client (Older Version)

> **When to use**: Client project is a **copy of the source version** (retail_v5) but running an **older version**. Brain documents can be copied and refined instead of built from scratch.
> **Time**: ~15 minutes setup + ~15 minutes refinement per module (vs ~1 hour from scratch)
> **Key advantage**: Pattern scan will find bugs already fixed in the source version

---

## Prerequisites
- [ ] Client project codebase is accessible locally
- [ ] Source version brain exists: `knowledge_brain/{MODULE}/` in retail_v5
- [ ] You know approximately which version the client is running (or can check)

---

## Steps

### Step 1: Copy Workflow Files + Brain Templates [Developer]

Same as `/setup-new-client` Step 1:

// turbo
```powershell
# Copy workflows + templates + SOP
New-Item -ItemType Directory -Force -Path "{CLIENT_PROJECT_PATH}\.agent\workflows"
New-Item -ItemType Directory -Force -Path "{CLIENT_PROJECT_PATH}\knowledge_brain\_TEMPLATE"
New-Item -ItemType Directory -Force -Path "{CLIENT_PROJECT_PATH}\knowledge_brain\_SYSTEM"
New-Item -ItemType Directory -Force -Path "{CLIENT_PROJECT_PATH}\bug_report_AI\SOP"

Copy-Item -Recurse -Force "{SOURCE_PROJECT_ROOT}\.agent\workflows\*" "{CLIENT_PROJECT_PATH}\.agent\workflows\"
Copy-Item -Recurse -Force "{SOURCE_PROJECT_ROOT}\knowledge_brain\_TEMPLATE\*" "{CLIENT_PROJECT_PATH}\knowledge_brain\_TEMPLATE\"
Copy-Item -Force "{SOURCE_PROJECT_ROOT}\bug_report_AI\SOP\FINAL_SOP_BUG_REMEDIATION.md" "{CLIENT_PROJECT_PATH}\bug_report_AI\SOP\"

# Copy Senior Developer Knowledge Files (CRITICAL — prevents wrong AI diagnoses)
Copy-Item -Force "{SOURCE_PROJECT_ROOT}\knowledge_brain\_SYSTEM\DIAGNOSTIC_PLAYBOOK.md" "{CLIENT_PROJECT_PATH}\knowledge_brain\_SYSTEM\"
Copy-Item -Force "{SOURCE_PROJECT_ROOT}\knowledge_brain\_SYSTEM\DANGER_ZONES.md" "{CLIENT_PROJECT_PATH}\knowledge_brain\_SYSTEM\"
Copy-Item -Force "{SOURCE_PROJECT_ROOT}\bug_report_AI\POSTMORTEM_LOG.md" "{CLIENT_PROJECT_PATH}\bug_report_AI\"
```

### Step 2: Copy Source Brain [Developer]

Copy the module brain(s) from the source version:

// turbo
```powershell
# Copy the module brain (e.g., Estimation)
Copy-Item -Recurse -Force "{SOURCE_PROJECT_ROOT}\knowledge_brain\{MODULE_NAME}" "{CLIENT_PROJECT_PATH}\knowledge_brain\{MODULE_NAME}\"
```

### Step 3: Copy Pattern Library [Developer]

// turbo
```powershell
# Copy COMMON_BUG_PATTERNS.md (has all known patterns from source audits)
Copy-Item -Force "{SOURCE_PROJECT_ROOT}\bug_report_AI\COMMON_BUG_PATTERNS.md" "{CLIENT_PROJECT_PATH}\bug_report_AI\"
```

This gives the client project the FULL pattern library — including bugs already fixed in the source version. The pattern scan (Step 6) will find these in the older codebase.

### Step 4: Update Central Config [Developer]

Edit `.agent/config.md` with client's project-specific values — all env paths, repo info, and module paths.
Also edit `github-bug-tracking.md` with client's repo info (same as new client setup).

### Step 5: Refine the Copied Brain [Antigravity]

> **This is the critical step.** The brain was built for the source version — the client's older version may have differences.

#### 5a. Schema Verification (MOST IMPORTANT)

Compare the brain's schema analysis against the client's actual database:

```sql
-- Run against client's DB:
-- Check if all documented tables exist
SELECT TABLE_NAME FROM information_schema.TABLES
WHERE TABLE_SCHEMA = '{CLIENT_DB}'
AND TABLE_NAME LIKE '%{module_prefix}%';
```

Compare output with `SCHEMA_ANALYSIS.md`:
- **Missing tables** → Remove from brain, flag as "not in this version"
- **Missing columns** → Update brain, flag as "added in later version"
- **Different column types** → Update brain with client's actual types

#### 5b. Method Verification

// turbo
```powershell
# Count methods in client's model vs source brain
Select-String -Pattern "function " -Path "{CLIENT_PROJECT_PATH}\admin\models\{Module}_model.php" | Measure-Object | Select-Object Count
```

Compare with `METHOD_INDEX.md`:
- **Methods in brain but NOT in client code** → Mark as "Added in later version — N/A for this client"
- **Methods in client code but NOT in brain** → These are client-specific customizations → add to brain

#### 5c. Controller/Route Verification

// turbo
```powershell
# List controller methods
Select-String -Pattern "public function " -Path "{CLIENT_PROJECT_PATH}\admin\controllers\{Module}.php"
```

Compare with `MODULE_BRAIN.md` routes section. Update as needed.

#### 5d. JS File Verification

// turbo
```powershell
# Check if JS file exists and compare function count
Select-String -Pattern "function " -Path "{CLIENT_PROJECT_PATH}\admin\js\{module}.js" | Measure-Object | Select-Object Count
```

Compare with `DATA_FLOW.md` JS function map. The older version may have fewer functions.

#### 5e. Business Rules Check

Read `BUSINESS_RULES.md` — business rules are usually stable across versions. But check:
- [ ] Has the client customized any formulas?
- [ ] Have any rules been added in the source version that don't exist here?
- [ ] Are there client-specific pricing/discount/tax rules?

#### 5f. Mark Brain as Derived

Add a header to `MODULE_BRAIN.md`:

```markdown
> **⚠️ DERIVED BRAIN**
> Copied from: retail_v5 (source version)
> Client version: {VERSION}
> Refined on: {TODAY}
> Verified: Schema ✅ | Methods ✅ | Routes ✅ | JS ✅ | Rules ✅
>
> Differences from source:
> - {list any removed/added methods}
> - {list any missing tables/columns}
> - {list any client-specific customizations}
```

### Step 6: Run Pattern Scan [Antigravity]

This is where the copied brain pays off massively:

```
/manage-bug-patterns
Action: Scan
Module: {MODULE_NAME}
```

**Expected result**: The older version will match MORE patterns than the source version, because bugs fixed in the source still exist in the client's code.

```
Example:
Source version (retail_v5): 14/17 patterns matched — 3 already fixed
Client version (older):     16/17 patterns matched — those 3 fixes don't exist
→ 3 bugs instantly found with fix templates already available
```

### Step 7: Run Targeted Audit [Antigravity]

With the refined brain in place, run the full audit:

```
/module-bug-audit
Module: {MODULE_NAME}
```

The audit will be faster because:
- Brain already exists (skips Phase 0)
- Pattern library already has known patterns (Round 0 pre-scan is instant)
- Antigravity has full context from the copied brain

### Step 8: Create Delta Report [Antigravity]

After the audit, create a special report comparing client bugs vs source bugs:

```markdown
## Client Delta Report: {CLIENT_NAME} — {MODULE_NAME}

### Bugs Fixed in Source but Present in Client
| Bug ID (Source) | Client Bug ID | Pattern | Status |
|---|---|---|---|
| EST-R301 | {CLIENT}-R301 | PAT-SEC-001 | Fix template available |
| EST-R601 | {CLIENT}-R601 | PAT-TXN-001 | Fix template available |
| ... | ... | ... | ... |

### Bugs Unique to Client (Not in Source)
| Bug ID | Description | Category |
|---|---|---|
| {CLIENT}-XXX | {description} | {category} |

### Summary
- Bugs shared with source (fix available): {N}
- Bugs unique to client: {N}
- Total bugs: {N}
- Estimated fix time (shared bugs): {N} hours (fix templates ready)
- Estimated fix time (unique bugs): {N} hours (manual diagnosis needed)
```

---

## Time Comparison

| Approach | Setup | Per Module | Total (5 modules) |
|---|---|---|---|
| **From scratch** (`/setup-new-client`) | 30 min | ~2 hours | ~10.5 hours |
| **Copy + refine** (`/setup-existing-client`) | 15 min | ~30 min | ~2.75 hours |
| **Savings** | 15 min | ~1.5 hours | **~7.75 hours** |

---

## Completion Report
```
✅ Existing client set up (derived from source)
   Client: {CLIENT_NAME}
   Base version: {VERSION}
   Source brain: retail_v5
   
   Brain refinement:
     Schema differences: {N} (tables/columns missing or different)
     Method differences: {N} (methods added/removed since this version)
     Client customizations: {N}
   
   Pattern scan results:
     Patterns matched: {N}/{TOTAL}
     Bugs with fix templates ready: {N}
     Novel bugs (unique to client): {N}
   
   Ready for: /module-bug-audit → /fix-single-bug
```
