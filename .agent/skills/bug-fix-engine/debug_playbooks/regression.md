# Playbook #4: Feature Stopped Working (Regression)

> **Symptom**: "It used to work last week", "After the update it broke"

## ⚡ Binary Search Integration
- **Start Layer**: Git log — `git log -5 --oneline -- {file}`. The bug is in the last thing that changed.
- **Elimination**: Recent commit found → `git diff` to see exact change → the diff IS the bug. No recent commit → check settings table, PHP version, or server config change.
- **Smell test**: "After the update" → 95% the update introduced it. "Nothing changed" → check `ret_settings` for hidden config changes, or compare-with-working (was a different module updated that shares a model?).

## Collect (Ask Human)
1. What feature?
2. When did it last work? (date or "before the update")
3. Was there a recent deployment? What was deployed?
4. `git log -10 --oneline -- {affected file}` (if accessible)

## Trace (AI Does)

### Step 1: Identify what changed
```bash
# Recent changes to the affected file
git log -10 --oneline -- admin/application/controllers/{file}.php
git log -10 --oneline -- admin/application/models/{file}.php

# Diff the last change
git diff HEAD~1 -- {file}
```

### Step 2: Check for common regression causes
- **Method renamed** without updating all callers
- **New parameter added** to function without default value
- **Settings key changed** (check `ret_settings` for new/renamed keys)
- **PHP version upgrade** (`each()` removed in 7.2, `${}` deprecated in 8.2)
- **Library updated** (CI2 method behavior change)
- **DB migration** without code update (new column expected but not added)

### Step 3: If no git access
- Run fingerprint incremental scan — compare current vs previous scan
- Check `COVERAGE_TRACKER.md` for recent refresh findings
- Ask: "Did anyone manually edit files on the server?"

## Common Root Causes
1. **40%**: Recent code change introduced the bug (git diff reveals)
2. **25%**: PHP/server update changed behavior (version-dependent)
3. **15%**: Settings changed (new row in ret_settings, missing default)
4. **10%**: Database schema change without code update
5. **10%**: Another module's fix created a downstream break

## Output
→ Which commit/change caused the regression
→ Exact before/after comparison
