# Tagging Module — Fix Report

> **Module**: Tagging | **Last Updated**: 2026-03-24

---

### Fix: TAG-CLT02 — Duplicate Tag Form Does Not Show Closed Lot Numbers
- **Date:** 2026-03-24
- **Track:** A (System)
- **Category:** Logic
- **Severity:** P2 (Minor)
- **Files Changed:** `admin/assets/js/ret_tagging.js` (L6219)
- **Root Cause:** Client-side JS filter `if(item.is_closed==0)` in `get_received_lots()` unconditionally excluded closed lots from the `#tag_lot_received_id` dropdown, even though the backend already returned them via the `include_closed` parameter.
- **Fix Applied:** Added page-context exception: `if(item.is_closed==0 || (typeof ctrl_page !== 'undefined' && ctrl_page[2] == 'duplicate_print'))`
- **Tests:** Manual verification — closed lots appear in Duplicate Tag dropdown; Add Tag still excludes them.
- **Pattern:** NEW pattern added — PAT-JS-001 (Client-side filter contradicts backend parameter)
- **Rollback:** Revert L6219 to `if(item.is_closed==0)`

---
