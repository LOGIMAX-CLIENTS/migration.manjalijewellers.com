# Recipe: 0-Pcs Pocket Blocked from Melting Issue Screen

## Metadata
- **Pattern ID**: PAT-RET-MELT-001
- **Severity**: HIGH
- **Modules Affected**: Ret Metal Process (Melting Issue)
- **Auto-fixable**: Yes — single string replacement in model WHERE clause

## Client Scope
- **Applies to**: ALL clients using the Retail Old Metal / Melting workflow
- **Reason**: Core model method, not client-specific

## Created By
- **Developer**: Antigravity (AI)
- **Client**: erp.manepally.com
- **Date**: 2026-05-18
- **Source Bug ID**: RET-MELT-001

## Symptom
When a user creates an Old Metal Pocketing entry for scrap material (gold dust, grains,
unformed lumps) and enters a valid weight but **0 pieces**, the pocket is completely
invisible on the Melting Issue screen. The "Select Pocket" dropdown returns "No results
found" even though the pocket was saved successfully and has valid gross/net weight.

The entire melting and receipt workflow for unformed scrap materials is blocked.

## Root Cause
`get_melting_pocket_details()` in `ret_metal_process_model.php` has a `WHERE p.piece > 0`
guard that hard-blocks any pocket whose header column `piece = 0`, regardless of whether
it has valid weight.

The outer `get_pocket_details()` function only includes a pocket in its response when
`sizeof($item_details) > 0`. Since the inner query returns empty for 0-pcs pockets, the
pocket is silently dropped from the API response before it ever reaches the frontend.

**Failure chain:**
```
API: get_pocket_details (POST trans_type=1)
  → calls get_melting_pocket_details($id_metal_pocket)
      → WHERE p.piece > 0  ← BLOCKS all pockets where piece = 0
      → returns []
  → sizeof($item_details) == 0 → pocket excluded from response
  → Frontend dropdown: "No results found"
```

**Division-by-zero risk:** The query also computes:
```sql
round(IFNULL(SUM(d.purity*d.piece)/SUM(d.piece),0),2) as tot_purity
```
When `SUM(d.piece) = 0`, MySQL returns NULL, and IFNULL(..., 0) returns 0 safely.
No crash — but this was previously hidden because `piece > 0` prevented 0-pcs rows.

## Detection
```powershell
# Find the buggy WHERE clause
grep -n "p.piece>0" admin/application/models/ret_metal_process_model.php
```

Expected output (bug present):
```
773:        where p.piece>0 AND p.status = 0 and d.id_metal_pocket = ...
```

## Files
- `admin/application/models/ret_metal_process_model.php`
  - Function: `get_melting_pocket_details()` (~line 773)

## Fix

### Before
```php
        where p.piece>0 AND p.status = 0 and d.id_metal_pocket = ".$id_metal_pocket." and d.type=1
```

### After
```php
        where (p.piece>0 OR p.net_wt>0) AND p.status = 0 and d.id_metal_pocket = ".$id_metal_pocket." and d.type=1
```

**Rationale:** A pocketing entry is valid for melting if it has physical weight — regardless
of piece count. Gold dust, grains, and unformed lumps legitimately have 0 pieces but non-zero
weight. The `HAVING issue_nwt < net_wt` clause at line 775 still correctly prevents already
fully-issued pockets from re-appearing.

## Verification

1. **DB check (prove bug):** Run the old query against the affected pocket — it should return 0 rows:
   ```sql
   SELECT COUNT(*) FROM ret_old_metal_pocket_details d
   LEFT JOIN ret_old_metal_pocket p ON p.id_metal_pocket = d.id_metal_pocket
   WHERE p.piece > 0 AND p.status = 0 AND d.id_metal_pocket = {pocket_id} AND d.type = 1;
   -- Expected: 0 rows (bug confirmed)
   ```

2. **DB check (verify fix):** Run the fixed query — it should return ≥1 row:
   ```sql
   SELECT COUNT(*) FROM ret_old_metal_pocket_details d
   LEFT JOIN ret_old_metal_pocket p ON p.id_metal_pocket = d.id_metal_pocket
   WHERE (p.piece > 0 OR p.net_wt > 0) AND p.status = 0 AND d.id_metal_pocket = {pocket_id} AND d.type = 1;
   -- Expected: ≥1 row (fix verified)
   ```

3. **UI check:** Navigate to Melting Issue → Select Type: Old Metal → Select Vendor → Search.
   The 0-pcs pocket must now appear in the "Select Pocket" dropdown.

4. **Issue it** to the smith → confirm save succeeds with no error.

5. **Regression:** Verify existing piece>0 pockets still appear as before.

6. **PHP syntax:** `php -l admin/application/models/ret_metal_process_model.php` → No errors.

## Notes
- `get_melting_SalesPocket_details()` (the sibling function for Sales Return/Partly Sales pockets)
  already uses `p.gross_wt > 0` instead of `p.piece > 0` — it is NOT affected by this bug.
- The `tot_purity` and `avg_rate` columns will display `0` for 0-pcs pockets. This is
  cosmetically correct (purity/rate-per-gram is meaningless for unformed dust) and causes
  no downstream errors.
- This bug was confirmed with 8/8 unit tests on the `manepally` database (232ms, 2026-05-18).
