# Implementation Plan: Phase 2 Documentation

## Goal

Create 3 additional knowledge base documents to complete the Estimation Module documentation, enabling safe bug fixes and feature implementation.

---

## Proposed Documents

### Document 1: Workflow 7 - Edit Estimation

**Purpose**: Document UPDATE flow differences from INSERT

**Research Required**:

- View `admin_ret_estimation.php` case "edit" (~lines 1336+)
- View `admin_ret_estimation.php` case "edit_save" (update logic)
- Analyze how existing items are modified vs deleted

**Structure**:

- Fetch existing estimation data
- Populate form with existing values
- Handle item modifications (update/delete/add)
- Recalculation triggers
- Database UPDATE vs DELETE+INSERT patterns

---

### Document 2: Edge Cases & Errors

**Purpose**: Document failure scenarios and error handling

**Research Required**:

- Search for error messages in JS (`$.toaster`, `alert`)
- Search for validation checks in controller
- Find `trans_rollback` scenarios

**Structure**:

- Pre-validation failures (missing branch, employee)
- Tag validation errors (sold, reserved, not found)
- Rate validation errors (out of range)
- Day closing validation
- Transaction failures
- Duplicate submission prevention (`form_secret`)

---

### Document 3: Business Rules Reference

**Purpose**: Consolidate calculation rules and constraints

**Research Required**:

- Extract from existing workflows
- Check settings tables for configurable rules
- Review profile permissions

**Structure**:

- Calculation types table (caltype 0,1,2,3)
- MC types table (per piece, per gram, etc.)
- Rate limits (min/max by metal)
- Mandatory fields matrix
- Profile permission flags
- Wastage slab rules

---

## Verification Plan

Since these are documentation files (not code changes), verification will be:

1. **Completeness Check**: Each document covers all sections outlined above
2. **Accuracy Check**: Code references in docs match actual code
3. **Accessibility Check**: All file links in Master Index work
4. **User Review**: Ask user to review final documents

> [!NOTE]
> No automated tests needed - this is documentation only.

---

## Execution Order

1. Research and create `workflow_07_edit_estimation.md`
2. Research and create `edge_cases_and_errors.md`
3. Research and create `business_rules.md`
4. Update `MASTER_INDEX.md` with new document links
5. Notify user for review
