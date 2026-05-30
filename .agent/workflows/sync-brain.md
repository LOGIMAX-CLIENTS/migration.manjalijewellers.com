---
description: Sync copied module brains from source to match client code — auto-diff, classify, patch changed sections
version: 1.0
last_updated: 2026-03-07
---

# Sync Brain Workflow

## Purpose

After copying `.agent/` and `knowledge_brain/` from source to a new client project, this workflow **diffs the source code vs client code** and **patches the brain docs** so they accurately describe the client's codebase — not the source's.

> **This is NOT a replacement for `/build-module-brain`.** Use `/build-module-brain` to create brains from scratch. Use `/sync-brain` to update copied brains to match a different client's code.

## When to Use

- Developer copied `knowledge_brain/` from source project to a new client
- Developer already ran `/validate-workflows` (local paths are set in `.env`)
- Brains exist but describe **source code**, not this client's code

## Prerequisites

- `.agent/.env` is configured (run `/validate-workflows` first)
- `knowledge_brain/{MODULE_NAME}/` exists with brain docs copied from source
- Developer knows the **source project path** (where brains were copied FROM)

## Input

- `{SOURCE_PATH}` — Absolute path to the source project root (e.g., `c:\xampp\htdocs\retail_v5`)
- `{MODULE_NAME}` — Optional. Sync one module, or omit to sync ALL modules with existing brains.

## Steps

### Step 0: Validate Environment [Antigravity]

// turbo

1. Read `.agent/.env` → get `{PROJECT_ROOT}`
2. Read `.agent/config.md` → get module registry
3. Confirm `{SOURCE_PATH}` exists and has `knowledge_brain/` directory
4. List all modules that have brain folders in current project's `knowledge_brain/`

If `{MODULE_NAME}` provided → sync only that module.
If omitted → sync all modules that have brain folders.

### Step 1: Code Diff per Module [Antigravity]

// turbo

For each module to sync, diff these 3 core files:

```powershell
# Controller diff
& "{PHP_PATH}" -r "echo count(token_get_all(file_get_contents('{SOURCE_PATH}/{CONTROLLER_DIR}/{CONTROLLER_FILE}')));"
& "{PHP_PATH}" -r "echo count(token_get_all(file_get_contents('{PROJECT_ROOT}/{CONTROLLER_DIR}/{CONTROLLER_FILE}')));"
```

**Quick diff approach** — compare function signatures, not full code:

1. **Extract function list from SOURCE** file (controller + model):
   ```powershell
   Select-String -Path "{SOURCE_PATH}\{MODEL_DIR}\{MODEL_FILE}" -Pattern "function\s+\w+" | ForEach-Object { $_.Matches.Value }
   ```

2. **Extract function list from CLIENT** file:
   ```powershell
   Select-String -Path "{PROJECT_ROOT}\{MODEL_DIR}\{MODEL_FILE}" -Pattern "function\s+\w+" | ForEach-Object { $_.Matches.Value }
   ```

3. **Compare the lists**:
   - Functions in BOTH → **shared** (brain likely valid)
   - Functions in SOURCE only → **removed in client** (remove from brain)
   - Functions in CLIENT only → **custom additions** (add to brain)

4. For shared functions, do a content diff:
   ```powershell
   fc.exe "{SOURCE_PATH}\{MODEL_DIR}\{MODEL_FILE}" "{PROJECT_ROOT}\{MODEL_DIR}\{MODEL_FILE}"
   ```

5. **Calculate change percentage**:
   ```
   change% = (added_functions + removed_functions + modified_functions) / total_functions × 100
   ```

### Step 2: Classify Each Module [Antigravity]

Based on Step 1 diff results:

| Change % | Classification | Action | Badge |
|----------|---------------|--------|-------|
| 0-5% | 🟢 **IDENTICAL** | Mark brain as verified, add sync metadata only | `[VERIFIED]` |
| 5-20% | 🟡 **MINOR DRIFT** | Patch changed sections in brain docs | `[PATCHED]` |
| 20-50% | 🟠 **MODERATE DRIFT** | Patch + rebuild BUSINESS_RULES and METHOD_INDEX | `[PARTIAL_REBUILD]` |
| >50% | 🔴 **MAJOR DRIFT** | Flag for full rebuild via `/build-module-brain` | `[NEEDS_REBUILD]` |

Present classification table to developer:

```
Module Sync Classification — {PROJECT_NAME}
Source: {SOURCE_PATH}

| Module     | Controller | Model  | JS     | Overall | Action           |
|------------|-----------|--------|--------|---------|------------------|
| Estimation | 3% 🟢    | 8% 🟡 | 5% 🟡 | 🟡 MINOR | Patch 2 sections |
| Billing    | 0% 🟢    | 2% 🟢 | 1% 🟢 | 🟢 IDENTICAL | Verify only  |
| LOT        | 45% 🔴   | 38% 🔴| 42% 🔴| 🔴 MAJOR | Full rebuild    |
```

> [!IMPORTANT]
> If a module is 🔴 MAJOR DRIFT, do NOT patch — recommend `/build-module-brain` instead. Patching a brain when >50% of code is different will produce an unreliable brain.

### Step 3: Patch Brain Docs [Antigravity]

For each 🟢 IDENTICAL module:
1. Add sync metadata header to `MODULE_BRAIN.md` (see Step 5)
2. No content changes needed

For each 🟡 MINOR or 🟠 MODERATE module:

#### 3a. Update METHOD_INDEX.md

1. **Remove** entries for functions that exist in source brain but NOT in client code
2. **Add** entries for functions that exist in client code but NOT in source brain:
   - Read the new function from client code
   - Generate: function name, parameters, return type, tables used, description
3. **Flag** entries for functions that exist in BOTH but have different logic:
   - Add note: `⚠️ Modified from source — logic differs`

#### 3b. Update BUSINESS_RULES.md (🟠 MODERATE only)

1. Identify which business rules are affected by modified functions
2. Read the CLIENT's version of those functions
3. Rewrite the affected rule descriptions based on CLIENT code
4. Mark unchanged rules as `[from source — verified]`

#### 3c. Update CROSS_MODULE_MAP.md (if new dependencies found)

1. Check if client code calls models/controllers not in source brain
2. Add new cross-module references if found

#### 3d. Update COVERAGE_TRACKER.md

1. Recalculate coverage based on client's actual file:
   - Count total functions in client's controller/model/JS
   - Count functions documented in brain
   - Update coverage percentage

### Step 4: Verify Sync [Antigravity]

// turbo

For each synced module, run a quick verification:

1. **Method count check**: Number of functions in METHOD_INDEX.md should match actual function count in client code (±5% tolerance for private/helper functions)
2. **Table reference check**: Tables mentioned in brain should exist in client's database config
3. **File reference check**: All file paths in brain should resolve to actual files in client project

Present verification results:

```
Sync Verification — {MODULE_NAME}

Methods:  Brain: 45  |  Code: 47  |  Coverage: 96% ✅
Tables:   Brain: 12  |  DB: 12    |  Match: 100% ✅  
Files:    Brain: 4   |  Exist: 4  |  Match: 100% ✅

Verdict: SYNC VALID ✅
```

### Step 5: Add Sync Metadata [Antigravity]

Add this metadata block to the TOP of `MODULE_BRAIN.md` for each synced module:

```markdown
<!-- SYNC METADATA -->
<!-- sync_source: {SOURCE_PATH} -->
<!-- sync_date: {TODAY} -->
<!-- sync_classification: {IDENTICAL|MINOR|MODERATE} -->
<!-- sync_change_percent: {N}% -->
<!-- sections_patched: {list of patched brain docs} -->
<!-- controller_hash: {MD5 of client controller file} -->
<!-- model_hash: {MD5 of client model file} -->
<!-- js_hash: {MD5 of client JS file} -->
<!-- confidence: {HIGH|MEDIUM|LOW} -->
<!-- next_action: {NONE|REVIEW_BUSINESS_RULES|FULL_REBUILD} -->
```

> [!NOTE]
> **Hash verification**: When any developer reads this brain later, they can compare the stored hash against the current file hash. If hashes don't match → code has changed since sync → brain may be stale.

### Step 6: Generate Sync Report [Antigravity]

Create `knowledge_brain/SYNC_REPORT_{DATE}.md`:

```markdown
# Brain Sync Report — {PROJECT_NAME}

| Field | Value |
|-------|-------|
| Source | {SOURCE_PATH} |
| Client | {PROJECT_NAME} |
| Date | {TODAY} |
| Modules Synced | {count} |

## Module Results

| Module | Classification | Patched Sections | Confidence | Action Needed |
|--------|---------------|-----------------|------------|---------------|
| {name} | 🟢 IDENTICAL | — | HIGH | None |
| {name} | 🟡 MINOR | METHOD_INDEX | HIGH | Review patched sections |
| {name} | 🔴 MAJOR | — | — | Run /build-module-brain |

## Functions Added (Client Customizations)
- {MODULE}: `{function_name}()` — {brief description}

## Functions Removed (Source-only, not in client)
- {MODULE}: `{function_name}()` — removed from brain

## Next Steps
1. Review 🟡 MINOR patched modules — verify business rules are correct
2. Run `/build-module-brain` for any 🔴 MAJOR modules
3. Commit updated `knowledge_brain/` to repo
```

## Completion Report

```
✅ Brain Sync Complete — {PROJECT_NAME}
   Source: {SOURCE_PATH}
   Modules: {N} synced
   🟢 Identical: {N} (verified, no changes needed)
   🟡 Minor: {N} (patched — review recommended)
   🟠 Moderate: {N} (patched — review business rules)
   🔴 Major: {N} (flagged for /build-module-brain)
   Report: knowledge_brain/SYNC_REPORT_{DATE}.md
```
