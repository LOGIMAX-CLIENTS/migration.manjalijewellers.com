# Estimation Fix Report

> Fix reports for Estimation module bugs.
> Last Updated: 2026-04-10

---

### Fix: EST-RC01 — Catalog Sub Design Race Condition on Edit Screen
- **Date:** 2026-04-10
- **Track:** A (System)
- **Category:** JS Race Condition
- **Severity:** P1
- **Files Changed:** `clients/konika/assets/js/ret_estimation.js`
- **Root Cause:** AJAX race condition — `cat_sub_design_details` loaded via async AJAX at 1s timeout, edit row rendering at 2s timeout. When master data response hadn't arrived yet, sub design `<select>` rendered with zero options. `getNonTagDesignDetails()` read `.cat_sub_design` (empty select) instead of `.cat_id_sub_design` (hidden input with correct value).
- **Fix Applied:** (1) Changed selector in `getNonTagDesignDetails()` from `.cat_sub_design` to `.cat_id_sub_design` (line 19905); (2) Added fallback `<option>` creation from `item.id_sub_design`/`item.sub_design_name` when `cat_sub_design_details` hasn't loaded (after line 20938).
- **Tests:** Manual — edit screen sub design dropdown shows correctly, stock AJAX receives correct id_sub_design
- **Pattern:** NEW pattern added — PAT-JS-006 (AJAX Race Condition on Edit Screen Dropdown)
- **Recipe:** `recipe_cat_sub_design_race_condition.md`
- **Rollback:** Revert `.cat_id_sub_design` → `.cat_sub_design` on L19905; remove fallback `if` block after L20938

---
