---
description: "P0 Hotfix — Fast-track for critical bugs. Minimal brain, maximum speed."
---

# /hotfix — P0 Critical Bug Fast-Track

> **Time Target:** < 4 hours  
> **Applies to:** Data loss, security breach, financial miscalculation, system down

## Step 1: INTAKE (5 min) [👤 HUMAN]
Document: Reporter, Module, Symptom, Impact, Reproduction, Evidence.

## Step 2: BUILD FAST BRAIN (30 min) [🤖 AUTO]
- Quick File Map: `view_file_outline` on controller + model
- Affected Flow Trace: JS → Controller → Model → DB for ONE flow
- DB Diagnostic: query affected entity

## Step 3: DIAGNOSE (15 min) [🤖 AUTO]
Identify fault location, classify level (System/Architecture/Business).

## Step 4: APPLY FIX (15 min) [🤖 AUTO]
Smallest possible fix. ONE file if possible. Guard/cap/fallback only.

## Step 5: SYNTAX CHECK [🤖 AUTO]
// turbo
```
& "C:\php8\php.exe" -l "{FIXED_FILE_PATH}"
```

## Step 6: QUICK TEST (15 min) [🤖 AUTO]
// turbo
```
cd "c:\xampp 7.1\htdocs\etail_development_src\admin\tests" && & "C:\php8\php.exe" vendor/bin/phpunit --no-configuration {BUG_ID}Test.php --testdox
```

## Step 7: VERBAL APPROVAL [👤 HUMAN]
Show diff, explain change, get verbal GO/NO-GO.

## Step 8: DEPLOY [👤 HUMAN]
DB backup → Git tag `hotfix-{BUG_ID}-{date}` → Deploy → Monitor 2 hours.

## Step 9: BACKFILL (within 24h) [🤖 AUTO]
Create bug card, run `/update-knowledge-base`, write rollback plan.

## Step 10: MONITOR (24h) [👤 HUMAN]
Check error logs, MySQL logs, core functions, user reports.
