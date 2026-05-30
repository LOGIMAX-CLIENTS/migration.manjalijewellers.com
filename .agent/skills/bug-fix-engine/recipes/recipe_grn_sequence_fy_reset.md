# Recipe: GRN Sequence Not Resetting After Financial Year Change

## Metadata
- **Pattern ID**: PAT-PUR-SEQ-001
- **Severity**: HIGH
- **Modules Affected**: Purchase (GRN Entry)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Universal — any client using GRN entry with financial year tracking

## Created By
- **Developer**: Antigravity
- **Client**: retail_v5 (Sparqle Diamonds)
- **Date**: 2026-04-02
- **Source Bug ID**: N/A

## Symptom
After changing the Financial Year, GRN reference numbers (PU-XXXXX, PM-XXXXX, PC-XXXXX) continue incrementing from the previous year's last number instead of resetting to 00001 for the new FY.

## Root Cause
`generate_grn_refno()` queries `MAX(CAST(SUBSTRING_INDEX(grn_ref_no, '-', -1) AS UNSIGNED))` from `ret_grn_entry` filtered only by `grn_type`, without filtering by the current financial year (`grn_fin_year_code`). The column `grn_fin_year_code` already exists and is populated during save.

## Detection
```command
grep -rn "generate_grn_refno" admin/application/models/
```
Then check if the MAX query inside the function includes `grn_fin_year_code` in its WHERE clause. If it only filters by `grn_type`, the bug is present.

## Files
- `admin/application/models/ret_purchase_order_model.php`
- `admin/application/models/ret_purchase_approval_model.php`

## Fix

### Before
```php
    $sql = "SELECT MAX(CAST(SUBSTRING_INDEX(grn_ref_no, '-', -1) AS UNSIGNED)) as max_num
            FROM ret_grn_entry WHERE grn_type = ?";
    $result = $this->db->query($sql, array($grn_type));
```

### After
```php
    // Get active financial year code
    $fin_year = $this->get_FinancialYear();
    $fin_year_code = $fin_year['fin_year_code'];

    $sql = "SELECT MAX(CAST(SUBSTRING_INDEX(grn_ref_no, '-', -1) AS UNSIGNED)) as max_num
            FROM ret_grn_entry WHERE grn_type = ? AND grn_fin_year_code = ?";
    $result = $this->db->query($sql, array($grn_type, $fin_year_code));
```

> **Note:** Some copies may have `FOR UPDATE` at the end of the SQL — keep it if present, just add the `AND grn_fin_year_code = ?` before it.

## Verification
1. Run PHP syntax check: `php -l` on both model files
2. Check that `ret_grn_entry` table has `grn_fin_year_code` column populated
3. After FY change, create a new GRN — ref should start at PU-00001 (or PM-/PC- for other types)
4. Verify previous FY GRN records are unchanged

## Notes
- The function exists in TWO model files — both must be patched
- `get_FinancialYear()` is already available in both models (queries `ret_financial_year WHERE fin_status=1`)
- The `ret_grn_entry.grn_fin_year_code` column is already populated on insert (controller line ~9611)
- No schema changes required
