# 📚 Estimation Module — Knowledge Base Gap Analysis

> **Audit Date**: 2026-02-17  
> **KB Location**: `knowledge_base/modules/estimation/`  
> **Total KB Files Reviewed**: 18

---

## Current KB Coverage Assessment

| Area | Coverage | Quality |
|---|---|---|
| Business Rules / Calculation Types | ✅ Good | Comprehensive formulas |
| Edge Cases / Error Messages | ✅ Good | Well-documented triggers |
| DB Schema / Table Relationships | ⚠️ Partial | Missing several child tables |
| Save Workflow | ⚠️ Partial | Covers happy path, misses field mapping details |
| Update/Edit Workflow | ❌ Missing | No delete-reinsert documentation |
| Delete Workflow | ❌ Missing | No cascade cleanup docs |
| JS-to-PHP Field Mapping | ❌ Missing | Critical for debugging save bugs |
| Child Tag / Merge Handling | ❌ Missing | Complex logic undocumented |
| Gift Voucher Flow | ❌ Missing | No documentation at all |
| Chit Utilization save/update | ⚠️ Partial | Methods documented, field mapping missing |
| Discount Approval Flow | ⚠️ Partial | Basic flow documented |

---

## Recommended KB Additions

### Priority 1 — Critical (Directly Related to P0-P1 Bugs)

1. **`workflow_04_update_estimation.md`** [NEW]
   - Document the delete-and-reinsert pattern
   - List ALL tables that must be cleaned before re-insert
   - Compare save vs update field mappings for each item type
   - Highlight fields present in save but missing in update (and vice versa)

2. **`workflow_05_delete_estimation.md`** [NEW]
   - Complete list of child tables and their FK columns
   - Correct deletion order (child-first)
   - SQL to verify no orphans remain

3. **`field_mapping_reference.md`** [NEW]
   - JS form field names → PHP POST key names → DB column names
   - Organized by item type (tag, catalog, custom, order)
   - Include stone, material, charge, and other_metal sub-tables

### Priority 2 — Important

4. **`estimation_module_kb.md`** [UPDATE]
   - Add `ret_est_other_metals`, `ret_estimation_other_charges`, `ret_est_tag_merge`, `ret_est_sales_return_utilization`, `ret_estimation_other_inventory_issue` to the table reference
   - Document FK relationships for these tables

5. **`child_tag_merge_logic.md`** [NEW]
   - Document how merged tags work (istag_merged = 1 for parent, 2 for child)
   - Document stone save indexing for child tags
   - Document the ret_est_tag_merge relationship table

6. **`gift_voucher_flow.md`** [NEW]
   - Document the gift voucher save/update/delete flow
   - Include the correct variable names and table references

### Priority 3 — Nice to Have

7. **`coding_standards.md`** [NEW]
   - Debug statement removal policy
   - Variable naming conventions for batch arrays
   - Transaction management patterns

8. **`edge_cases_and_errors.md`** [UPDATE]
   - Add note that form_secret (E22) is NOT actually implemented in save/update
   - Add concurrent edit scenario documentation
