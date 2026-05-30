# Playbook #12: Client-Specific Bug

> **Symptom**: "Works in source but broken in this client", "Only this client has the issue"

## ⚡ Binary Search Integration
- **Start Layer**: Fingerprint diff — run fingerprint scan to find code divergence between source and client
- **Elimination**: Fingerprint shows divergent function → diff the function → the diff IS the bug. No code divergence → check DB schema divergence (missing columns/tables). Same code + same schema → different config in `ret_settings` or `database.php`.
- **Smell test**: "Only this client" → 40% client is outdated (missing patches). "After source update" → 25% customization conflicts with new source code.

## Collect
1. Which client?
2. Which feature is broken?
3. Does it work in source (`retail_v5`)?
4. When was client last updated from source?

## Trace

### Step 1: Fingerprint scan
```bash
php fingerprint.php \
  --source="d:/XAMPP/htdocs/retail_v5" \
  --client="d:/XAMPP/htdocs/{client}" \
  --client-name="{client}" \
  --modules="{affected_module}" \
  --detail
```

### Step 2: Analyze drift
From the fingerprint report, check if the affected function is:
- **Source-only** → client is outdated (missing the fix/feature)
- **Client-only** → client has custom code that may conflict
- **Modified** → client has a divergent version of the function

### Step 3: Function-level diff
If modified, compare the EXACT function body:
```
Fingerprint report shows line-by-line diff.
Look for:
- Missing lines (client didn't get a patch)
- Added lines (client customization)
- Changed logic (intentional client-specific behavior? or accidental drift?)
```

### Step 4: Classify the divergence
| Classification | Meaning | Action |
|---|---|---|
| **Update Lag** | Client missing source updates | Copy function from source to client |
| **Customization** | Client has intentional changes | Merge: keep custom parts + add fix |
| **Accidental Drift** | Unintended change over time | Replace with source version |
| **Schema Divergence** | Client DB missing columns/tables | Run migration scripts |

### Step 5: Check DB schema
```sql
-- Compare columns
SHOW COLUMNS FROM {table};
-- Compare with source schema
-- Look for missing columns, different types, missing indexes
```

## Common Root Causes
1. **40%**: Client outdated — missing recent source patches
2. **25%**: Client customization conflicts with new source code
3. **20%**: DB schema divergence — missing migration
4. **10%**: Different PHP version on client server
5. **5%**: Different server config (file permissions, upload limits)
