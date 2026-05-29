# Scheme Module — Forensic Template
> **Round**: 2 | **Date**: 2026-03-24

---

## Layer 1: Symptom Collection

### Common Symptoms for Scheme Module
- [ ] Scheme not appearing in dropdown (account form)
- [ ] Scheme settings not saving correctly
- [ ] Benefit chart not loading in edit form
- [ ] GST split-up missing or incorrect
- [ ] Branch mapping lost after edit
- [ ] DigiGold toggle not available
- [ ] Referral values not calculating correctly
- [ ] TopUp chart not saving/loading
- [ ] Scheme cannot be deleted (accounts exist)
- [ ] Pre-close deduction chart not applying
- [ ] Employee closing incentive not saving on Add
- [ ] GA benefit chart not saving on Add
- [ ] Duplicate scheme settings entries
- [ ] `SHOW COLUMNS` performance issues

---

## Layer 2: Reproduce & Isolate

### Quick Checks
1. **Active/Visible?** `SELECT active, visible FROM scheme WHERE id_scheme = {ID}`
2. **Branch mapped?** `SELECT * FROM scheme_branch WHERE id_scheme = {ID}`
3. **Has accounts?** `SELECT COUNT(*) FROM scheme_account WHERE id_scheme = {ID}`
4. **Child tables populated?** Run DB_TRUTH_PROTOCOL.sql Section 1

### Isolation Questions
- Is this scheme type-specific? (scheme_type=0/1/2/3)
- Is this a DigiGold scheme? (is_digi=1)
- Is branch_settings enabled? (session variable)
- Is this an Add or Edit operation that failed?

---

## Layer 3: Client-Side Trace

### Key JS Variables (scheme.js)
| Variable | Where | Purpose |
|---|---|---|
| `schemeType` | Form change handler | Controls form field visibility |
| `flexibleSchType` | Flexible sub-type | Controls flexible-specific fields |
| `applyBenefitByChart` | Chart toggle | Shows/hides benefit chart |
| `applyDebitOnPreclose` | Preclose toggle | Shows/hides deduction chart |
| `isDigi` | DigiGold toggle | Shows/hides DigiGold fields |
| `isTopupScheme` | TopUp toggle | Shows/hides TopUp chart |
| `branchData` | Branch select | Multi-select branch IDs |
| `gstData` | GST section | GST split-up array |

### Network Tab Checks
| AJAX Call | Expected Response | Common Failures |
|---|---|---|
| `scheme/get_metals` | `[{id, name}]` | Empty array (no metals) |
| `scheme/get_classifications` | `[{id, name}]` | Empty array (no classifications) |
| `scheme/get_branches` | `[{id_branch, name}]` | Empty array (no branches) |
| `admin_scheme/checkDigiAvailability` | `{is_digi_gold: 0/1}` | SQL error |
| `admin_scheme/getActivePuritiesByMetal` | `[{id_purity, purity}]` | Empty (no purity set) |

---

## Layer 4: Server-Side Trace

### Controller Trace Points

| Symptom | File | Method | Line | What To Check |
|---|---|---|---|---|
| Scheme not saving | admin_scheme.php | sch_post('Add') | L389-601 | trans_begin/commit/rollback result |
| Scheme edit fails | admin_scheme.php | sch_post('Edit') | L785-990 | trans_status, double commit at L966+L984 |
| Benefit chart lost | admin_scheme.php | sch_post('Edit') | L866-888 | deleteData then insertData |
| Branch mapping lost | admin_scheme.php | sch_post('Edit') | L831-846 | delete_scheme_branch + insert loop |
| GST not updating | admin_scheme.php | sch_post('Edit') | L847-863 | update_gst flag check at L847 |
| Image upload error | admin_scheme.php | set_scheme_image | L1155-1177 | unlink without exists check |
| Delete blocked | admin_scheme.php | sch_post('Delete') | L993-1004 | check_acc_records |
| Emp incentive bug | admin_scheme.php | sch_post('Add') | L524 | $id is empty in Add case |
| GA benefit bug | admin_scheme.php | sch_post('Add') | L536 | deleteData with empty $id |

### Model Trace Points

| Symptom | Method | Line | What To Check |
|---|---|---|---|
| Insert fails | insert_scheme() | L466-492 | SHOW COLUMNS + data mapping |
| Update fails | update_scheme() | L493-519 | SHOW COLUMNS + WHERE clause |
| Orphan records | delete_scheme() | L526-536 | Only deletes gst_splitup_detail + scheme |
| SQL injection | get_scheme() | L184 | `WHERE s.id_scheme =$id` |
| Branch filter | get_schemes() | L74-142 | Complex conditional SQL assembly |

---

## Layer 5: Database Verification

Use `DB_TRUTH_PROTOCOL.sql`:
1. **Section 1**: Full scheme record pull (all 11 child tables)
2. **Section 2**: Orphan detection (10 queries for ALL child tables)
3. **Section 3**: Data quality (DigiGold duplicates, missing GST, zero installments)
4. **Section 5**: Configuration consistency (enabled features with missing data)

---

## Layer 6: Root Cause Classification

| Category | Typical Cause | Example |
|---|---|---|
| SQL Injection | Raw variable in query | `WHERE id_scheme = $id` |
| Data Corruption | Wrong variable in Insert | `$id` in Add case → NULL scheme_id |
| Transaction Bug | Double commit/rollback | TopUp edit commits separately |
| Orphan Data | Incomplete cascade delete | delete_scheme only cleans 1 of 10 tables |
| Configuration Gap | Enabled flag with no data | apply_benefit_by_chart=1 but no chart rows |
| Performance | SHOW COLUMNS overhead | Called on every insert/update |
| Delete via GET | CSRF vulnerability | scheme/delete/:id is GET |

---

## Layer 7: Configuration-Driven Variant Isolation

> Since this module is heavily configuration-driven (8 variant dimensions), always first determine:

1. **Current variant config**: `SELECT scheme_type, is_digi, apply_benefit_by_chart, apply_debit_on_preclose, payment_chances, maturity_type FROM scheme WHERE id_scheme = {ID}`
2. **Test same operation with different config**: If bug only affects scheme_type=1, test with 0,2,3
3. **Check grid intersection**: See INVARIANT_MATRIX.md Grid 1 (Scheme Type × Benefit Source)

---

## Layer 8: Transaction Integrity

### Transaction Boundaries
```
sch_post('Add'):
  L389: trans_begin()
  ... all inserts ...
  L590: trans_status() check
  L591: trans_commit() ← single commit ✅

sch_post('Edit'):
  L785: trans_begin()
  ... child table updates ...
  L966: trans_commit() ← TopUp SUCCESS commits EARLY ⚠️
  L968: trans_rollback() ← TopUp FAILURE rollbacks
  ... log insert ...
  L983: trans_status() check
  L984: trans_commit() ← SECOND commit (no-op or error?) ⚠️

sch_post('Delete'):
  L1008: trans_begin()
  L1010: trans_status() check
  L1011: trans_commit() ✅
```

### Partial Commit Risk
The Edit path has a critical bug: if `topUpSchemeChartEditProcess()` succeeds and commits at L966, but a subsequent insert fails, the outer rollback at L988 will NOT undo the TopUp changes (they're already committed).

---

## Specific Bug Investigations

### BUG-SCH-001: Employee Closing Incentive Not Saving on Add
**Symptom**: Closing incentive chart created with wrong/NULL id_scheme  
**Root Cause**: Controller L524 uses `$id` (the function parameter, empty in Add case) instead of `$res['id_scheme']`  
**Fix**: Change `'id_scheme' => $id` to `'id_scheme' => $res['id_scheme']` at L524  
**Impact**: All DB rows in emp_closing_incentive with NULL id_scheme

### BUG-SCH-002: GA Benefit Delete on Add
**Symptom**: On Add, deleteData is called with empty `$id` → may delete unrelated records  
**Root Cause**: Controller L536 calls `deleteData('id_scheme', $id, ...)` where `$id` is empty  
**Fix**: Skip delete in Add case, or use `$res['id_scheme']` and move inside success block  
**Impact**: Potential orphan deletes in scheme_general_advance_benefit_settings

### BUG-SCH-003: Double Transaction Commit
**Symptom**: Data inconsistency on TopUp scheme edit  
**Root Cause**: L966 commits for TopUp success, L984 commits again  
**Fix**: Remove inner commit/rollback in TopUp chart edit path, let outer handle it  
**Impact**: Partial commits possible on TopUp + other settings edit failure
