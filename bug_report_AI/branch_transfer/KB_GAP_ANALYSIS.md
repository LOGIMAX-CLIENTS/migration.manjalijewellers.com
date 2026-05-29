# Branch Transfer — KB Gap Analysis

> **Date**: 2026-03-11
> **Module**: Branch Transfer
> **Audit Results**: 76 bugs found

## Gaps in Module Brain (`knowledge_brain/Branch Transfer/`)

### 1. Missing Flow: "Other Issue" Branch Transfer
- **Observation**: Multiple bugs (BRN-D50, BRN-D29) were found in the "Other Issue" logic.
- **Gap**: `DATA_FLOW.md` and `BUSINESS_RULES.md` do not explicitly define the routing constraints or OTP requirements for "Other Issue" transfers vs normal branch transfers.

### 2. Missing Flow: Scan-based Approval (Download System)
- **Observation**: `ret_branch_transfer.js` contains complex scan-based download logic (L7815-8025).
- **Gap**: This mechanism is not documented in the Module Brain's `DATA_FLOW.md`. The interaction between the scan table and the main transfer tables is undocumented.

### 3. Model Method Documentation Gaps
- **Observation**: `BRN-D03`, `BRN-D04`, `BRN-D05` relate to missing joins or incorrect where clauses in `getLotsByBranch`.
- **Gap**: `METHOD_INDEX.md` should be updated to note that `getLotsByBranch` handles multiple transfer types (Tagged vs Non-Tagged) with different filtering logic.

### 4. Permission Matrix Granularity
- **Observation**: `BRN-D29` and `BRN-D35` show that "Repair Orders" (type 5) lack permission checks present in other types.
- **Gap**: `INVARIANT_MATRIX.md` suggests a uniform check, but the implementation is inconsistent. The brain should document that each `transfer_item_type` requires an explicit permission gate.

---

## Action Plan for KB Update
1. Update `DATA_FLOW.md` to include "Other Issue" and "Scan Download" sequences.
2. Update `BUSINESS_RULES.md` with hard constraints for "Repair Order" transfers.
3. Update `METHOD_INDEX.md` with refined table associations for new AJAX methods discovered.
