# 📋 SOP — Estimation Module Bug Remediation

> **Document ID**: SOP-EST-2026-001  
> **Version**: 1.0  
> **Date**: 2026-02-18  
> **Author**: Logimax Engineering Team  
> **Module**: Estimation (Full Module)  
> **Status**: Active

---

## 1. Purpose & Scope

This Standard Operating Procedure defines the structured process for identifying, prioritizing, fixing, testing, and deploying bug fixes in the **Estimation Module** of the Retail ERP system.

**In Scope:**
- 65 bugs identified via AI-assisted static analysis across 6 audit rounds
- All Estimation sub-modules: Add Estimation, Estimation List, Estimate Discount Approval
- All code layers: Controller, Model, JavaScript, Views, DB Schema
- All severity levels: P0 (Critical), P1 (Major), P2 (Minor), P3 (Cosmetic)

**Out of Scope:**
- Feature enhancements or redesign
- Bugs in other modules (Sales, Purchase, Inventory, etc.)
- Infrastructure or server-level changes

---

## 2. Reference Documents

All source documents are located in the project repository under `bug_report_AI/`:

| Document | Purpose |
|---|---|
| `CONSOLIDATED_BUG_REPORT.md` | Master index of all 65 bugs with severity, fix readiness, and sprint assignment |
| `ESTIMATION_BUG_FIX_EXECUTION_PLAN.md` | Per-bug execution plan with root cause, proposed fix, risk level, and rollback plan |
| `ROUND_2_SCHEMA_ANALYSIS.md` | DB schema cross-reference analysis (7 schema bugs) |
| `ROUND_3_DEEP_DIVE.md` | Model & data-flow analysis (12 bugs) |
| `ROUND_4_JS_VIEW_DEEP_DIVE.md` | JS save handler & view analysis (11 bugs) |
| `ROUND_5_JS_CALCULATIONS_DEEP_DIVE.md` | JS calculations & data binding (9 bugs) |
| `ROUND_6_VALIDATION_AJAX_DEEP_DIVE.md` | Validation functions & controller AJAX (10 bugs) |
| `KB_GAP_ANALYSIS.md` | Knowledge base coverage gaps identified during audit |
| `add_estimation/BUG_REPORT_SUMMARY.md` | Detailed summary for Add Estimation sub-module |
| `add_estimation/bugs_detailed.json` | Machine-readable bug data |
| `estimation_list/BUG_REPORT_SUMMARY.md` | Bug summary for Estimation List |
| `estimate_discount_approval/BUG_REPORT_SUMMARY.md` | Bug summary for Discount Approval |

---

## 3. Bug Discovery Methodology

### 3.1 Audit Approach

The bugs were discovered through a **6-round AI-assisted static analysis**, each round progressively deepening the analysis scope:

| Round | Focus Area | Scope | Bugs Found |
|---|---|---|---|
| Round 1 | Code-Level (Controller) | 3,574 lines, 64 methods | 16 |
| Round 2 | DB Schema Cross-Reference | 13 estimation tables | 7 |
| Round 3 | Model & Data-Flow | 3,006 lines, 112 methods | 12 |
| Round 4 | JS & View Layer | 31,391 lines + 2,526 lines | 11 |
| Round 5 | JS Calculations & Bindings | Calculation functions | 9 |
| Round 6 | Validation & AJAX | Validation functions, AJAX handlers | 10 |
| **Total** | | | **65** |

### 3.2 Severity Classification

| Severity | Criteria | Count |
|---|---|---|
| **P0 — Critical** | Security vulnerabilities, data corruption, data loss, financial miscalculation | 9 |
| **P1 — Major** | Wrong data display, broken business rules, orphan records, concurrency issues | 22 |
| **P2 — Minor** | Silent data truncation, missing fields, code quality, UX issues | 26 |
| **P3 — Cosmetic** | Debug artifacts, duplicate calls, code style | 8 |

---

## 4. Bug Fix Execution Protocol

### 4.1 Core Principles

> [!IMPORTANT]
> **ONE bug at a time. No batch fixes. Explicit approval required before each code change.**

1. **Isolation**: Each bug is fixed in isolation to minimize regression risk
2. **Traceability**: Every change is linked to a specific bug ID
3. **Reversibility**: Every fix has a documented rollback plan
4. **Defense in Depth**: Security fixes use multiple layers of protection (e.g., whitelist + parameterization for SQL injection)

### 4.2 Execution Order

Bugs are prioritized by **Severity (P0 → P3)**, then by **Sprint (1 → 3)**:

| Sprint | Severity Focus | Bug Count | Timeline |
|---|---|---|---|
| **Sprint 1** | All P0 + critical P1 bugs | ~15 | Immediate (Week 1) |
| **Sprint 2** | Remaining P1 + high-priority P2 | ~20 | Week 2 |
| **Sprint 3** | Remaining P2 + all P3 | ~30 | Backlog |

### 4.3 Per-Bug Workflow

Each bug follows this 11-step workflow (as documented in `ESTIMATION_BUG_FIX_EXECUTION_PLAN.md`):

```
┌─────────────────────────────────────────────────────┐
│  Step 1: Review Problem Summary                     │
│  Step 2: Verify Root Cause in source code           │
│  Step 3: Assess Impact (security, data, financial)  │
│  Step 4: Locate Exact Line(s) in codebase           │
│  Step 5: Read Current Code (verbatim)               │
│  Step 6: Apply Proposed Minimal Fix                 │
│  Step 7: Validate Safety Rationale                  │
│  Step 8: Assess Risk Level (Low/Medium/High)        │
│  Step 9: Execute Regression Checklist               │
│  Step 10: Document Rollback Plan                    │
│  Step 11: Confirmation Gate → Proceed/Modify/Reject │
└─────────────────────────────────────────────────────┘
```

---

## 5. Fix Types & Procedures

### 5.1 Code-Level Fixes (Controller / Model / JS / View)

**Applies to**: Bugs in `admin_ret_estimation.php`, `ret_estimation_model.php`, `ret_estimation.js`, `form.php`

**Procedure:**
1. Locate the exact file and line number from the execution plan
2. Read the current code verbatim and confirm it matches the plan
3. Apply the minimal fix as specified — no additional refactoring
4. Run PHP syntax check: `php -l <filename>`
5. Execute relevant unit tests (if available)
6. Perform smoke test on local environment
7. Document the change in a walkthrough

**Example (Bug 01 — EST-R301, SQL Injection):**

| Step | Action | Result |
|---|---|---|
| Fix Applied | Column whitelist + query parameter binding in 7 methods | `ret_estimation_model.php` modified |
| Syntax Check | `php -l ret_estimation_model.php` | No errors |
| Unit Tests | 42 tests, 99 assertions | All passed ✅ |
| Test Coverage | Whitelist enforcement (24 payloads), binding verification (15 methods), edge cases (3) | 100% of fix scope |

### 5.2 Schema-Level Fixes (DB)

**Applies to**: Bugs EST-S01 through EST-S07

**Procedure:**
1. **MANDATORY: Take a full database backup before any schema change**
2. Execute changes during a maintenance window only
3. Test the ALTER TABLE statement on a staging/dev database first
4. Verify existing data is preserved after the migration
5. Confirm application functionality post-migration

**Pre-approved Migration Script:**
```sql
-- EST-S01 + EST-S07: Fix gift voucher table
ALTER TABLE `ret_est_gift_voucher_details`
  MODIFY `gift_voucher_id` int NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`gift_voucher_id`);

-- EST-S02: Convert MyISAM to InnoDB
ALTER TABLE `ret_est_other_metals` ENGINE=InnoDB;

-- EST-S03: Fix integer truncation for percentage columns
ALTER TABLE `ret_est_chit_utilization` MODIFY `wastage_per` decimal(10,2) DEFAULT NULL;
ALTER TABLE `ret_estimation_items` MODIFY `act_wast_per` decimal(10,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `ret_estimation` MODIFY `disc_per` decimal(10,2) DEFAULT NULL;
ALTER TABLE `ret_estimation` MODIFY `bulk_was_disc_per` decimal(10,2) DEFAULT NULL;
```

> [!CAUTION]
> The MyISAM → InnoDB conversion may take significant time on large tables. Schedule during off-peak hours.

### 5.3 JavaScript Fixes

**Applies to**: Bugs in `ret_estimation.js` (31,391 lines)

**Procedure:**
1. Locate the exact line number and function name
2. Verify the bug pattern (e.g., wrong variable, missing guard, incorrect selector)
3. Apply the minimal fix — preferring patterns already proven elsewhere in the same file
4. Clear browser cache and test on all supported browsers
5. Test both Add and Edit estimation paths, as JS is shared

**Special Considerations:**
- JS file is 31K+ lines — changes must be surgically precise
- Many bugs involve variable name mismatches between similar functions (e.g., `get_tag_data` vs `get_tag_barcode_data`)
- Always verify the fix doesn't affect the counterpart function

---

## 6. Testing & Verification Standards

### 6.1 Automated Testing

**Environment:**
- PHP 8.5.0 (CLI) at `C:\php8\php.exe`
- PHPUnit 12.5.8 via Composer
- Test directory: `admin/tests/`

**Running Tests:**
```bash
cd admin/tests
& "C:\php8\php.exe" vendor/bin/phpunit --no-configuration <TestFile>.php --testdox
```

**Test Coverage Requirements per Bug Type:**

| Bug Type | Required Tests |
|---|---|
| SQL Injection | Whitelist enforcement, malicious payload blocking, binding verification, edge cases |
| Transaction errors | Success path commits, failure path rollbacks, partial failure handling |
| Variable/typo fixes | Correct value assigned, old behavior no longer occurs |
| Validation logic | Valid input passes, invalid input blocked, boundary conditions |
| Schema changes | Data preservation, type correctness, constraint enforcement |

### 6.2 Manual Testing Checklist

After each code fix, perform the following smoke tests:

- [ ] **Tag search** — Search by Tag ID, Tag Code, and Barcode
- [ ] **Customer search** — Verify autocomplete returns correct results
- [ ] **Estimation save** — Create a new estimation with tag, catalog, and custom items
- [ ] **Estimation edit** — Edit an existing estimation, verify all data preserved
- [ ] **Estimation delete** — Delete and verify no orphan records remain
- [ ] **EDA flow** — Test Estimate Discount Approval with items in all sections
- [ ] **Order linking** — Link an order and verify PO details display
- [ ] **Old metal** — Add old metal exchange with rate validation
- [ ] **Negative test** — Insert malicious input via browser dev tools, verify rejection

### 6.3 Verification Sign-Off

Each bug fix must be signed off with:

| Criterion | Required |
|---|---|
| Syntax check passes | ✅ |
| Unit tests pass (if applicable) | ✅ |
| Smoke tests pass | ✅ |
| No unintended side effects | ✅ |
| Rollback plan documented | ✅ |
| Walkthrough/changelog updated | ✅ |

---

## 7. Deployment Procedure

### 7.1 Pre-Deployment

1. Verify all targeted bug fixes have passed verification sign-off
2. Take a full database backup
3. Take a full codebase backup (or ensure Git commit is tagged)
4. Notify stakeholders of the maintenance window
5. For schema changes: run migration on staging first, verify, then production

### 7.2 Deployment Steps

1. Pull the latest code changes from the approved branch
2. Execute any pending schema migrations in order
3. Clear PHP opcode cache (if applicable)
4. Instruct users to hard-refresh their browsers (JS cache)
5. Run a quick smoke test on production

### 7.3 Post-Deployment Monitoring

Monitor for 24 hours after deployment:

- [ ] No PHP errors in error logs
- [ ] No MySQL warnings/errors in slow query log
- [ ] Estimation save/edit/delete operations work correctly
- [ ] No user-reported issues
- [ ] Database integrity checks pass (no new orphan records)

### 7.4 Rollback Procedure

If a fix causes issues in production:

1. **Identify** the bug ID that caused the regression
2. **Refer** to the rollback plan in `ESTIMATION_BUG_FIX_EXECUTION_PLAN.md` (Section 10 of each bug)
3. **Execute** the rollback — each bug has a specific, minimal rollback step
4. **Verify** the system returns to its previous (pre-fix) state
5. **Document** the rollback reason and re-assess the fix approach

---

## 8. Sprint Breakdown

### Sprint 1 — Critical & Immediate (15 bugs)

| # | Bug ID | Title | Type | Risk |
|---|---|---|---|---|
| 1 | EST-R301 | SQL Injection in 10+ model methods | Code | Medium |
| 2 | EST-R601 | `cancel_order_tag` commits on failure | Code | Low |
| 3 | EST-R501 | Market rate tax uses wrong variable | JS | Low |
| 4 | EST-R502 | Undefined `items.tag_id` in barcode scan | JS | Low |
| 5 | EST-R401 | EDA validates with wrong function | JS | Low |
| 6 | EST-R402 | EDA empty-table check always true | JS | Low |
| 7 | EST-R403 | EDA single validation flag overwritten | JS | Low |
| 8 | EST-001 | Gift voucher wrong variable (`$arrayMaterials`) | Code | Low |
| 9 | EST-S01+S07 | Gift voucher table missing AUTO_INCREMENT + PK | Schema | Low |
| 10 | EST-002 | `market_rate_tax` stores cost instead of tax | Code | Low |
| 11 | EST-R302 | Cartesian JOIN (`b.bill_type = b.bill_type`) | Code | Low |
| 12 | EST-R303 | Tax subquery missing GROUP BY (×3 methods) | Code | Low |
| 13 | EST-R603 | Old metal rate `&&` should be `\|\|` | JS | Low |
| 14 | EST-005 | Child tag stones wrong index | Code | Low |
| 15 | EST-R504 | Missing metal type check in `get_tag_data` | JS | Low |

### Sprint 2 — Major Fixes (20 bugs)

Includes remaining P1 bugs and high-priority P2 issues: EST-R503, EST-R505, EST-R604, EST-R605, EST-R405, EST-R409, EST-R607, EST-S02, EST-006, EST-R304, EST-R308, EST-004, EST-007, EST-R305, EST-R506, EST-R507, EST-R508, EST-R306, EST-R309, EST-R310.

### Sprint 3 — Cleanup & Backlog (30 bugs)

Remaining P2 and all P3 bugs: schema type corrections, debug statement removal, code style improvements, UX enhancements.

---

## 9. Progress Tracking

### Current Status

| Sprint | Total | Completed | In Progress | Remaining |
|---|---|---|---|---|
| Sprint 1 | 15 | 1 | 0 | 14 |
| Sprint 2 | 20 | 0 | 0 | 20 |
| Sprint 3 | 30 | 0 | 0 | 30 |
| **Total** | **65** | **1** | **0** | **64** |

### Completed Fixes

| Bug ID | Title | Date Fixed | Tests | Status |
|---|---|---|---|---|
| EST-R301 | SQL Injection in 10+ model methods | 2026-02-17 | 42 tests, 99 assertions ✅ | Deployed to staging |

---

## 10. Risks & Mitigations

| Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|
| Fix introduces new regression | Medium | High | Per-bug regression checklist, unit tests, rollback plan |
| Schema migration causes downtime | Low | High | Run on staging first, maintenance window, backup |
| JS fix breaks another function | Medium | Medium | Test both Add and Edit paths, cross-reference similar functions |
| Multiple bugs interact | Low | High | Fix one at a time, verify after each |
| Test environment differs from production | Medium | Medium | Use same DB schema version, match PHP version |

---

## 11. Knowledge Base Updates

As bugs are fixed, the following KB documents should be created or updated (per `KB_GAP_ANALYSIS.md`):

### Priority 1 — Required
- [ ] `workflow_04_update_estimation.md` — Delete-and-reinsert pattern, complete table list
- [ ] `workflow_05_delete_estimation.md` — Complete child table cascade
- [ ] `field_mapping_reference.md` — JS → PHP → DB column name mapping

### Priority 2 — Recommended
- [ ] Update `estimation_module_kb.md` with 6 missing child tables
- [ ] `child_tag_merge_logic.md` — Merged tag save indexing
- [ ] `gift_voucher_flow.md` — Gift voucher save/update/delete lifecycle

### Priority 3 — Optional
- [ ] `coding_standards.md` — Debug removal policy, variable naming, transaction patterns
- [ ] Update `edge_cases_and_errors.md` — `form_secret` status, concurrent edit scenarios

---

## 12. Approval & Distribution

| Role | Name | Signature | Date |
|---|---|---|---|
| **Prepared By** | Engineering Team | | 2026-02-18 |
| **Reviewed By** | | | |
| **Approved By** | | | |

**Distribution List:**
- Development Team
- QA Team
- Project Manager
- Database Administrator (for schema changes)

---

## Revision History

| Version | Date | Author | Changes |
|---|---|---|---|
| 1.0 | 2026-02-18 | Engineering Team | Initial SOP created from 6-round audit findings and Bug 01 fix experience |

---

> **Next Action**: Continue bug fix execution starting with **BUG 02 (EST-R601)** — `cancel_order_tag` transaction rollback fix.
