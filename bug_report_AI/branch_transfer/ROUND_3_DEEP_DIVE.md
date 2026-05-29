# Branch Transfer — Round 3: Model & Data-Flow Deep Dive

> **Date**: 2026-03-11
> **File**: `admin/application/models/ret_brntransfer_model.php` (2,236 lines, 56 methods)

---

## Bugs Found: 3

| Bug ID | Severity | Title | Lines | Track |
|---|---|---|---|---|
| BRN-R301 | **P2** | `empty()` Used on Numeric Fields | Multiple | B |
| BRN-R302 | **P2** | Inconsistent Return Variable Naming | Multiple | A |
| BRN-R303 | **P2** | Cancel Does Not Reverse Stock Changes | Controller L1261-1292 | B |

### BRN-R301 — `empty()` on Numeric Fields [P2]
The model uses `empty()` checks extensively. PHP's `empty("0")` returns `true`, so legitimate zero values (0 pieces, 0 weight) may be treated as empty.
**Pattern**: PAT-LOGIC-004
**Impact**: If a transfer has 0 gross weight (not unusual for certain non-tagged items), the fallback overwrites it.

### BRN-R302 — Inconsistent Return Variable Naming [P2]
18 instances of varying return variable naming (`$returnData`, `$return_data`, etc.). While not causing bugs currently, inconsistency increases the chance of PAT-VAR-003 typo bugs.
**Pattern**: PAT-VAR-003 (potential)

### BRN-R303 — Cancel Does Not Reverse Stock Changes [P2]
```php
// Controller L1261-1292: update_branch_transfer_cancel()
// Only updates master: status = 3
// Does NOT:
//   - Reverse tag_status in ret_taging (tags stuck in status 4)
//   - Reverse non-tag qty additions/deductions in ret_nontag_item
//   - Reverse packaging status changes
//   - Reverse old metal current_branch updates
```
**Impact**: After transit approval + cancel, inventory is desynchronized. Tags may be permanently stuck in "In Transit" status.
**Business Rule**: RULE-BRT-012 (documented in brain as known gap)
