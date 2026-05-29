# Round 3: Model & Data-Flow Deep Dive
**Target**: `ret_brntransfer_model.php`

## Analysis Focus
- SQL Injection via column selection
- Cartesian JOINs
- Missing Aggregation Grouping
- Variable Typos
- Null Fallbacks

## Bugs Found

### BRT-R301: Zero Value Loss During Fallback Checks
**Track**: B
**Severity**: P1
**Location**: `insertData()` L32, `updateData()` L67
**Description**: The model generic CRUD methods use `empty($value)` to check if a field is null/empty before saving. However, PHP's `empty()` treats literal integer `0` and string `"0"` as true. This causes explicit 0 values (e.g., 0 gross weight diffs) to be discarded and replaced with database defaults or skipped.
**Fix**: Replace `empty($value)` with strict type checks: `if ($value === '' || $value === null)`.

### BRT-R302: Inconsistent Variable Naming ($returnData vs $return_data)
**Track**: A
**Severity**: P1
**Location**: Scattered across 15+ methods (e.g. L1129 vs L1150 vs L1668)
**Description**: The model switches haphazardly between `$return_data`, `$return_Data`, and `$returnData`. This drastically increases the risk of returning an undefined array or silently missing data if a developer copy-pastes or modifies assigning code without noticing the case difference.
**Fix**: Standardize all returns to `$return_data`.

## Observations
*   No severe SQL Injection via column naming was detected (`$searchField` is absent).
*   No Cartesian JOINs were detected; all aliases in `JOIN` clauses correctly map across tables (`fb.id_branch = b.transfer_from_branch`).

## Conclusion
The model has critical logic fallback issues (`BRT-R301`) that could alter calculation accuracy by discarding zeros, but is structurally sound regarding injection and cartesian joins.
