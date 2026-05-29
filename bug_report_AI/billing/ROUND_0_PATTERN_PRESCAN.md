# Billing Module Bug Audit — Round 0: Pattern Pre-Scan

> **Module**: Billing | **Date**: 2026-02-24 | **Auditor**: Antigravity
> **Pattern Library**: `bug_report_AI/COMMON_BUG_PATTERNS.md` (19 patterns)

---

## Summary

| Metric                  | Value                 |
| ----------------------- | --------------------- |
| Patterns scanned        | 17 (original library) |
| Matches found           | 6                     |
| New patterns discovered | 2                     |
| Clean (no match)        | 11                    |

---

## Confirmed Pattern Matches

### BIL-PAT-SEC-001 — Column Name SQL Injection (P0)

**Pattern**: PAT-SEC-001 — User-controlled values used to construct column names in SQL
**Location**: `ret_billing_model.php` L293, L5024
**Detail**:

- L293: `$field` used directly in raw SQL: `"SELECT " . $field . " From ret_billing where " . $field . " is not null"`
- L5024: `$SearchTxt` concatenated into WHERE clause without escaping
  **Status**: ⚠️ Confirmed Bug
  **Track**: A (Security — no business judgment needed)

---

### BIL-PAT-SEC-002 — Raw $\_POST Bypass (P2)

**Pattern**: PAT-SEC-002 — `$_POST[]` used instead of `$this->input->post()`
**Location**: Controller (50+ instances) + Model (19 instances)
**Key Locations**:

- Controller: L307, L309, L317, L1119, L1129, L1131, L1133, L1135, L1137, L1684, L1688, L2644, L4423, L4693, L4767, L4898, L5196–L5236, L5253–L5322, L5352–L5514, L5770–L5772
- Model: L678, L680, L5024, L6309, L6311, L6412, L6414, L6494, L6498, L6612, L6616, L8272, L8675, L8677, L10141, L10143
  **Status**: ⚠️ Confirmed Bug (50+ controller + 19 model = ~70 total)
  **Track**: A (Security)

---

### BIL-PAT-SEC-003 — mkdir 0777 Permissions (P2)

**Pattern**: PAT-SEC-003 — `mkdir($path, 0777, TRUE)`
**Location**: Controller L101, L1638, L1643, L4560, L6879, L6884
**Detail**: 6 instances of world-writable directory creation for customer images and log paths
**Status**: ⚠️ Confirmed Bug
**Track**: A (Security)

---

### BIL-PAT-TXN-001 — trans_commit on Failure Branch (P0)

**Pattern**: PAT-TXN-001 — `trans_commit()` called without `trans_status()` safety check
**Location**: Controller — 14+ transaction blocks
**Detail**: 37 `trans_commit()` calls vs only 23 `trans_status()` checks. At least 14 transaction blocks commit without verifying whether the operations succeeded.
**Key risk areas**: Split save (L333→L1070), issue/receipt handlers, service bill saves, cash collection saves, advance transfers
**Status**: ⚠️ Confirmed Bug
**Track**: A (Transaction safety)

---

### BIL-PAT-RAW-001 — Raw SQL with String Concatenation (P1) 🆕

**Pattern**: PAT-RAW-001 — `$this->db->query()` with string concatenation (NEW pattern discovered)
**Location**: `ret_billing_model.php` — 241+ instances
**Detail**: Massive use of raw SQL throughout the model. String concatenation used for WHERE clauses, JOINs, and subqueries. While many values come from controller-sanitized parameters, the pattern is inherently unsafe and bypasses CI3's query builder protection.
**Key risk lines**: L149, L160, L171, L179, L192, L293, L380, L393, L413, L433, L444, L481, L490, L563, L659, L695, L766, L828, …
**Status**: ⚠️ Confirmed Bug (systemic)
**Track**: A (Security/Architecture)

---

### BIL-PAT-TXN-003 — trans_commit Without trans_status Check (P1) 🆕

**Pattern**: PAT-TXN-003 — `trans_commit()` called directly without `trans_status()` guard (NEW pattern)
**Location**: Controller — 14+ blocks
**Detail**: Distinct from PAT-TXN-001. Here the issue is that `trans_begin()` → operations → `trans_commit()` happens with no `trans_status()` check at all, meaning silently failed queries still get committed.
**Status**: ⚠️ Confirmed Bug
**Track**: A (Transaction safety)

---

## Clean Patterns (No Match)

| Pattern                                | Status          | Notes                                 |
| -------------------------------------- | --------------- | ------------------------------------- |
| PAT-SEC-004 — XSS via Unescaped Output | ⏳ Deferred     | Requires view file scanning (Round 4) |
| PAT-TXN-002 — MyISAM Engine            | ⏳ Deferred     | Requires live DB check (Round 2)      |
| PAT-QRY-001 — Cartesian JOIN           | ✅ Not detected |                                       |
| PAT-QRY-002 — Missing GROUP BY         | ⏳ Deferred     | Needs deeper model analysis (Round 3) |
| PAT-QRY-003 — Missing WHERE Scope      | ⏳ Deferred     | Needs deeper model analysis (Round 3) |
| PAT-VAR-001 — Copy-Paste Variable      | ✅ Not detected |                                       |
| PAT-VAR-002 — Undefined Variable       | ✅ Not detected |                                       |
| PAT-VAR-003 — Return Variable Typo     | ✅ Clean        | Consistent `$return_data` naming      |
| PAT-VAL-001 — Always-True (>= 0)       | ⏳ Deferred     | JS scan needed (Round 4)              |
| PAT-VAL-002 — Flag Overwrite           | ⏳ Deferred     | JS scan needed (Round 4)              |
| PAT-VAL-003 — Validation Mutates Data  | ⏳ Deferred     | JS scan needed (Round 5)              |
| PAT-SCH-001 — Missing AUTO_INCREMENT   | ⏳ Deferred     | Requires live DB (Round 2)            |
| PAT-SCH-002 — Integer for Decimal      | ⏳ Deferred     | Requires live DB (Round 2)            |

---

## Round 0 Result

```
Round 0: Pattern Pre-Scan Complete
├── Patterns scanned: 17
├── Matches found: 6
│   ├── PAT-SEC-001: P0 — SQL Injection via column name (2 methods)
│   ├── PAT-SEC-002: P2 — Raw $_POST bypass (~70 instances)
│   ├── PAT-SEC-003: P2 — mkdir 0777 (6 instances)
│   ├── PAT-TXN-001: P0 — trans_commit without status check (14+ blocks)
│   ├── PAT-RAW-001: P1 — Raw SQL concat (241+ queries) [NEW]
│   └── PAT-TXN-003: P1 — trans_commit w/o trans_status (14+ blocks) [NEW]
├── Deferred to later rounds: 8
├── Clean (no match): 3
└── Proceed to Rounds 1–6 for novel bugs
```
