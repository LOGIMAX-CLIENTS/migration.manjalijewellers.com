# Branch Transfer — Round 2: DB Schema Cross-Reference

> **Date**: 2026-03-11
> **Source**: SCHEMA_ANALYSIS.md from Brain + METHOD_INDEX.md Table→Method reverse map

---

## Bugs Found: 3

| Bug ID | Severity | Title | Table | Track |
|---|---|---|---|---|
| BRN-S01 | **P1** | Raw SQL with String Concatenation (5 methods) | Multiple | A |
| BRN-S02 | **P2** | TOCTOU in Trans Code Generator | `ret_branch_transfer` | A |
| BRN-S03 | **P3** | DomPDF Paper Orientation Typo | N/A | A |

### BRN-S01 — Raw SQL with String Concatenation [P1]
5 model methods use `$this->db->query()` with string concatenation instead of query bindings:
- `get_purchase_items()` ~L1912
- `get_partly_sale_details()` ~L1922
- `get_sales_ret_details()` ~L1932
- `get_purchase_items_details()` ~L1970
- `old_metal_bill_details()` ~L1976

**Pattern**: PAT-RAW-001
**Risk**: SQL injection if any user-controlled values reach these methods.

### BRN-S02 — TOCTOU in Trans Code Generator [P2]
```php
// trans_code_generator() ~L786-799
// Uses MAX() to find last trans code, increments in PHP, then inserts
// No FOR UPDATE lock — concurrent saves can produce duplicate trans codes
```
**Pattern**: PAT-CON-001
**Impact**: Duplicate `branch_trans_code` values under concurrent access.

### BRN-S03 — DomPDF Paper Orientation Typo [P3]
```php
// L1123:
$dompdf->set_paper("a4", "portriat"); // Should be "portrait"
```
DomPDF may silently accept the typo or fall back to default. Low severity but could cause print layout issues on some DomPDF versions.
