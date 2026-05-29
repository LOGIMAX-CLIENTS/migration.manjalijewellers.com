# Standard Operating Procedure (SOP)
## Generating a Digital Brain for a Separate Module

| **Document Version** | 1.0                                                              |
| -------------------- | ---------------------------------------------------------------- |
| **Date**             | 2026-02-18                                                       |
| **Author**           | Senior Technical Documentation Architect / Senior Developer      |
| **Purpose**          | To provide a repeatable, systematic process for creating a       |
|                      | “Digital Brain” for any software module, enabling rapid bug      |
|                      | diagnosis, impact analysis, and automated fix implementation.    |

---

## 1. Introduction

This SOP captures the methodology used during the creation of the **LOT Module Digital Brain**. It transforms an undocumented, legacy module into a fully documented, analyzable “brain” that reduces bug‑fixing time by **~60%** (from ~20 minutes to ~8 minutes per issue). The process is designed to be applied to any module, regardless of complexity, by following the documented phases.

---

## 2. Scope

This procedure covers the complete lifecycle of building a module brain:
- Initial codebase analysis (controller, model, views, JavaScript)
- Extraction of business rules, calculations, and workflows
- Detection of frontend/backend mismatches and hidden bugs
- Classification of bugs into eight standard categories
- Creation of executable planning, implementation, and unit‑test scripts
- Application of fixes and verification
- Measurement of performance improvement

---

## 3. Prerequisites

- Access to the full source code of the target module (PHP, JavaScript, SQL)
- A development environment where changes can be safely tested
- Basic knowledge of the module’s purpose (can be obtained from business docs)
- Tools: Code editor, browser with developer tools, database client
- For automation: PowerShell (or equivalent scripting language)

---

## 4. Step‑by‑Step Process

### 4.1 Initial Source Code Analysis

1. **Identify entry points**  
   - List all URLs, controller methods, and AJAX endpoints.  
   - Document HTTP methods, parameters, and which view/JS is involved.

2. **Map the data flow**  
   - Trace from controller → model → SQL → view → JavaScript.  
   - Note every table read/written, every field used, and any calculated values.

3. **Extract exact logic**  
   - Record function names, SQL queries, field names, and validation rules.  
   - Do not simplify – keep the original code’s complexity.

4. **Flag unknowns**  
   - If logic is not visible (e.g., external API calls), mark as `[UNKNOWN]` or `[INFERRED]`.

**Deliverable**: A raw document containing all extracted facts (can be used as the skeleton for the Digital Brain).

---

### 4.2 Build the Digital Brain Document

Follow the **Digital Brain structure** defined in the original prompt. The mandatory sections are:

| Section | Content |
|---------|---------|
| 1. Module Overview | Purpose, upstream/downstream flow |
| 2. Entry Points & Routes | Table of URLs, methods, views, JS |
| 3. Controller Method Map | Each method: inputs, called models, DB tables, failure conditions |
| 4. Database Interaction Map | Table‑level: fields, read/write, relationships, business meaning |
| 5. Field‑Level Specification | Every field: source, type, mandatory, validation, calculation dependencies |
| 6. Calculation Engine | Formulas in pseudo‑code, input/output, implementation location (PHP/JS) |
| 7. Business Rule Engine | Rules as IF‑THEN, with type (hard rule, client variant, manual exception) |
| 8. Status Workflow | State transitions, allowed next states, enforcement location |
| 9. Client‑Side Logic (JS) | Validations, calculations, UI rules, AJAX calls |
| 10. Frontend vs Backend Mismatches | Table of discrepancies, risk level, recommended fix |
| 11. Common Bug Root Causes | Symptom → root cause → fix location → test case |
| 12. Unit Test Derivation Map | Rule ID → scenario → input → expected output → file/method |
| 13. Change Request Impact Map | For typical changes (rate change, status change) – affected files/tables |
| 14. Manual Override Register | Rules that cannot be automated |
| 15. Gaps, Risks & Technical Debt | Missing validations, hardcoded values, duplicated logic |

**Critical Thinking Rule**: For every major logic block, answer:  
- WHAT does it do?  
- HOW does it work?  
- WHERE can it fail?  
- WHY would it fail?  
- HOW should it be fixed?

---

### 4.3 Bug Detection and Classification

1. **Run a static analysis**  
   - Compare PHP validation with JavaScript validation.  
   - Identify missing edge cases (e.g., negative weights, less_wt > gross_wt).  
   - Look for SQL injection risks, missing try‑catch, etc.

2. **Categorize each bug** according to the eight standard types:

   | Category | Examples |
   |----------|----------|
   | 1. Logic Bugs | Wrong calculations, missing business rules |
   | 2. Data Validation Bugs | No null/range/type checks |
   | 3. Database Bugs | SQL injection, N+1 queries, missing indexes |
   | 4. Exception Handling | Missing try‑catch, poor error messages |
   | 5. Security Bugs | CSRF, missing sanitization, file upload flaws |
   | 6. Performance Bugs | Inefficient queries, no pagination |
   | 7. Integration Bugs | AJAX error handling, timeouts |
   | 8. Concurrency Bugs | Race conditions, no locking |

3. **Assign severity** (Critical / High / Medium / Low) and priority (P0–P3).

4. **Document each bug** with:  
   - Bug ID (e.g., BUG‑V001)  
   - Description, impact, root cause  
   - Test case (input → expected output)  
   - Location in code (file + line if possible)  
   - Proposed fix

**Deliverable**: A set of bug reports, one per category, stored in a `bug_reports/` folder.

---

### 4.4 Creating Automated Fix Scripts

For each bug (especially P0/P1), generate three PowerShell scripts:

1. **`BUG‑XXX_PLANNING.ps1`**  
   - Verifies whether the bug still exists.  
   - Assesses impact and risk.  
   - Outputs a decision (fix / no fix) and estimated effort.

2. **`BUG‑XXX_IMPLEMENTATION.ps1`**  
   - Creates a timestamped backup of the affected file.  
   - Applies the exact code change (using string replacement or direct edit).  
   - Verifies the change by checking for the presence of the fix.

3. **`BUG‑XXX_UNIT_TESTS.ps1`**  
   - Generates an HTML test page (or a simple test runner) that exercises the fixed functionality.  
   - Includes multiple test cases with expected results.  
   - Provides a visual pass/fail summary.

These scripts allow any developer to apply the fix and verify it with minimal manual intervention.

---

### 4.5 Applying Fixes and Verifying

1. Run the planning script for the target bug.  
2. If the decision is “fix”, run the implementation script.  
3. Run the unit tests (open the generated HTML file in a browser) and confirm all tests pass.  
4. Perform manual regression testing on related features.

---

### 4.6 Update the Digital Brain

After each bug is fixed, enrich the Digital Brain with:

- **Anti‑Patterns Map**: Document exactly what was broken and how it was fixed (e.g., “line 6764 read from wrong element – changed to fallback”).  
- **Function‑Level Code Map**: For key functions, record line numbers, inputs, outputs, and known gaps.  
- **Field‑Level Data Flow Trace**: Show the full path from DB → PHP → HTML → JS for critical fields.  
- **“Why It Was Done This Way” notes**: Capture developer intent to help future maintainers.

This turns the brain from a static reference into a living, ever‑more‑accurate knowledge base.

---

### 4.7 Measure Performance Improvement

1. Before the brain exists, time how long it takes to diagnose a typical bug (e.g., 20 minutes).  
2. After the brain is complete, time the same diagnosis using the brain (e.g., 8 minutes).  
3. Document the time saved in the module’s executive summary.

In our LOT Module case, the brain reduced diagnosis time by **60%** (from ~20 min to ~8 min).

---

## 5. Limitations and Pitfalls

- **The Digital Brain is only as good as the source code analysis** – if the code contains hidden logic (e.g., in external libraries), the brain may miss it.  
- **Automated scripts rely on exact string matching** – whitespace variations can cause edit failures; always create a backup first.  
- **Unit tests cover only the fixed bug** – regression testing must still be performed manually for complex flows.  
- **The brain does not replace direct code reading** – subtle bugs (like commented‑out code) still require manual inspection.  
- **Effectiveness diminishes if the brain is not kept up‑to‑date** – it must be updated after every significant change.

---

## 6. Tools and Templates

| Item | Location / Description |
|------|------------------------|
| Digital Brain structure | Original prompt (section 1–15) |
| Bug classification | Eight categories defined in this SOP |
| PowerShell script templates | See examples in `bug_reports/1_logic_bugs_planning_and_implementation/` |
| Unit test HTML template | Generated by `BUG‑XXX_UNIT_TESTS.ps1` |
| Backup command | `Copy-Item $file "$file.backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"` |

---

## 7. Appendix: Sample Workflow (LOT Module)

1. **Initial scan** of `admin_ret_lot.php` and `ret_lot.js` → created Digital Brain v1.  
2. **Bug classification** produced 31 bugs across 8 categories.  
3. **Automated scripts** created for BUG‑L001, BUG‑L002, BUG‑L005, BUG‑L006, BUG‑V001, BUG‑V002, BUG‑D001, etc.  
4. **Fixes applied** – e.g., added client‑side calculations, fixed design/sub‑design “ALL” bug, added validation for less_wt > gross_wt, fixed SQL injection risks.  
5. **Brain updated** with anti‑patterns, function maps, and field traces.  
6. **Time measurement** confirmed a 60% reduction in bug‑fixing time.

---

## 8. Conclusion

This SOP provides a complete, repeatable method for building a Digital Brain for any module. By following these steps, teams can transform undocumented legacy code into a structured, searchable knowledge base that drastically accelerates debugging, reduces errors, and improves code quality. The key is to treat the brain as a living document, continuously enriched with each bug fix and code change.

**Prepared by:** Senior Technical Documentation Architect / Senior Developer  
**Date:** 2026-02-18