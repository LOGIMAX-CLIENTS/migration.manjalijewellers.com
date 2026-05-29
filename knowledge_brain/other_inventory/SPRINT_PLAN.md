# SPRINT PLAN — Other Inventory Bug Fixes
> **Module:** Other Inventory | **Created:** 2026-03-14 (Round 4) | **Updated:** 2026-03-14 (Round 13) | **Bugs:** 51 total

---

## Sprint Summary

| Sprint | Focus | Bug Count | Risk Level |
|---|---|---|---|
| S1 — Critical Fixes | Server crashes, wrong responses, broken transactions, silent corruption, missing rollback, generaterefCode null fatal, billing cross-module boundary | 11 | 🔴 High |
| S2 — Data Integrity | Missing field updates, orphan files, N+1 (getlastrefno loop), ZPL, wrong filter | 9 | 🟠 Medium |
| S3 — Security | SQL injection (gift/print/mapping paths), CSRF, XSS (print views, form.php JS, flash, QR print, dompdf label), SQL injection cross-module billing model | 16 | 🔴 High |
| S4 — UX / JS | Wrong messages, selector bugs, type coercion, wrong status boolean | 6 | 🟡 Low |
| S5 — Low / Cleanup | Race conditions, typos, hardcoded values, date type | 8 | 🟡 Low |

---

## Sprint 1 — Critical Server-Side Fixes
> **Goal:** Eliminate crashing, wrong logic responses, broken transactions, and silent DB corruption
> **Effort:** ~4–6 hours

| Bug ID | Title | File | Line | Fix |
|---|---|---|---|---|
| BRN-OI-002 | `updateBatchData()` has `print_r + exit` | Model | L32–33 | Delete 2 lines |
| BRN-OI-008 | `issue_item/save` failure returns `status=TRUE` | Controller | L987 | Change `TRUE` → `FALSE` |
| BRN-OI-031 | `issue_item/save` gift_mapping insert — 3 PHP array keys have trailing spaces → NULLs | Controller | L935–941 | Remove trailing space from `'id_other_item '`, `'id_scheme '`, `'item_issue_limit '` |
| BRN-OI-006 | `cancel_purchase_entry` debug echo + exit before rollback | Controller | L684–686 | Remove echo+exit, add proper rollback |
| BRN-OI-007 | `product_details/save` missing `trans_begin()` | Controller | L738 | Add `$this->db->trans_begin()` before foreach |
| BRN-OI-009 | `delete_product_mapping` — `trans_begin()` inside loop | Controller | L1159–1162 | Hoist `trans_begin()` before foreach |
| BRN-OI-010 | `update_product_mapping` — `trans_begin()` inside loop | Controller | L1188–1205 | Hoist `trans_begin()` before foreach |
| BRN-OI-016 | Gift mapping inserts before `trans_begin()` in issue/save | Controller | L927–948 | Move gift_mapping inserts inside trans block |
| BRN-OI-039 | `purchase_entry/save` — no rollback when header insert fails | Controller | L658–660 | Add `$this->db->trans_rollback()` in else branch |
| BRN-OI-042 | `generaterefCode(NULL)` — PHP8 fatal / PHP7 malformed ref codes | Controller | L803–832 | Null guard: `if (!$lastTagCode) return '1-00001';` |
| BRN-OI-047 | Cross-module: billing commits before OI issue inserts | `admin_ret_billing.php` | L4492–4568 | Move OI inserts inside billing transaction before `trans_commit()` |

---

## Sprint 2 — Data Integrity Fixes
> **Goal:** Fix silent data loss and broken report/stock features
> **Effort:** ~3–5 hours

| Bug ID | Title | File | Line | Fix |
|---|---|---|---|---|
| BRN-OI-011 | `set_image_other()` uses update return value as ID | Controller | L345 | Change `$item` to `$id` |
| BRN-OI-012 | Edit doesn't update `stock_id_uom` or `issue_to` | Controller | L269–277 | Add 2 fields to update array |
| BRN-OI-015 | `get_AvailableStockDetails()` wrong filter key | Model | L523 | Change `$data['id_size']` → `$data['id_inv_size']` |
| BRN-OI-017 | `other_inventory_print()` — `$tagprintCode` not reset | Controller | L1325 | Add `$tagprintCode = ""` after emit |
| BRN-OI-018 | `admin_settings_model` loaded twice in constructor | Controller | L19 | Remove duplicate load |
| BRN-OI-025 | Issue form shows "Please Select Branch" for missing item | JS | L2118 | Fix message string |
| BRN-OI-026 | Delete mapping no-selection shows success toast | JS | L2730 | Change `priority:'success'` → `'danger'` |
| BRN-OI-030 | JS POSTs `id_size` but model expects `id_inv_size` | JS + Model | L2495, L523 | Fix both sides simultaneously |
| BRN-OI-037 | `get_productMappedDetails()` N+1 query | Model | L569–581 | Rewrite as single JOIN query |
| BRN-OI-040 | Orphan image files on failed purchase save | Controller | L610–640 | `unlink()` written files on rollback |
| BRN-OI-041 | `getlastrefno()` called per-piece in tagging nested loop | Controller | L740 | Cache ref code before loop, increment local counter |

---

## Sprint 3 — Security
> **Goal:** Remediate SQL injection and CSRF vulnerabilities
> **Effort:** ~6–10 hours (larger scope)

| Bug ID | Title | File | Scope | Fix |
|---|---|---|---|---|
| BRN-OI-001 | SQL injection in all raw query methods | Model | ~20 methods | Migrate to CI Active Record / parameterized queries |
| BRN-OI-003 | `getActiveskuid()` — column selector injection | Model | L207 | Whitelist allowed columns |
| BRN-OI-004 | `CheckIsNameDuplicate()` — name concatenated | Model | L854–855 | Use `$this->db->where()` |
| BRN-OI-005 | DELETE via GET — CSRF vulnerable | Controller | L253, L469, L1079 | Convert to POST + CSRF token |
| BRN-OI-032 | `get_inv_chit_gift()` — raw `$id` in SELECT | Model | L675 | Use Active Record `where()->get()` |
| BRN-OI-033 | `delete_gift_map_data()` — raw `$id` in DELETE | Model | L682 | Use Active Record `where()->delete()` |
| BRN-OI-034 | 4 print-path methods — raw `$id` in queries | Model | L789,L814,L830,L841 | Parameterized queries |
| BRN-OI-035 | Product mapping methods — raw param concat | Model | L545,L592,L595 | Use `$this->db->where()` |
| BRN-OI-036 | `getlastrefno()` null dereference on empty table | Model | L151–152 | Null guard on `row()` result |
| BRN-OI-043 | Stored XSS via `other_id` in JS variable (`form.php`) | View | L228 | `json_encode((int)$other['id_other_item'])` |
| BRN-OI-044 | Stored XSS in purchase print via supplier fields | View | L45–61 | Wrap all fields in `htmlspecialchars()` |
| BRN-OI-045 | Stored XSS via `product_name` in print table | View | L127 | `htmlspecialchars($po_detail['product_name'])` |
| BRN-OI-046 | Flash message class/title/body unescaped | View | L65,L69,L71 | `htmlspecialchars()` + class whitelist |
| BRN-OI-049 | Stored XSS in QR print via product name / ref code | View | `qr_print.php` L55–56 | `htmlspecialchars()` on both label fields |
| BRN-OI-050 | SQL injection in billing model cross-module OI SELECT | `ret_billing_model.php` | L7138 | `$this->db->where('bill_id', (int)$bill_id)->get(...)` |
| BRN-OI-051 | Stored XSS in dompdf print label via product name | View | `item_qrcode.php` L24–25 | `htmlspecialchars()` on `$img[0]['name']` |

---

## Sprint 4 — UX / JS Polish
> **Goal:** Fix misleading UI feedback and broken widget initializations
> **Effort:** ~1–2 hours

| Bug ID | Title | File | Line | Fix |
|---|---|---|---|---|
| BRN-OI-027 | `get_other_inventory_ref_no` missing `#` selector | JS | L699 | Add `#` prefix |
| BRN-OI-028 | `keypress` instead of `keyup` on cancel remark | JS | L1457 | Change event name |
| BRN-OI-029 | Issue pcs validation — string vs number coercion | JS | L2161 | Wrap with `parseFloat()` |
| BRN-OI-021 | `HAVING` without GROUP BY in `get_invnetory_item()` | Model | L409 | Verify MySQL mode or add explicit GROUP BY |
| BRN-OI-020 | Day closing entry_date DATE vs DATETIME inconsistency | Controller | L560, L735, L914 | Standardize to `Y-m-d H:i:s` always |
| BRN-OI-048 | `issue_item/save` returns `status=>TRUE` on insert failure | Controller | L986–988 | Change `TRUE` → `FALSE` in else branch |

---

## Sprint 5 — Low Risk / Cleanup
> **Goal:** Address known but low-risk items
> **Effort:** ~1–2 hours

| Bug ID | Title | Notes |
|---|---|---|
| BRN-OI-013 | `generateItemRefNo()` race condition | Requires `SELECT FOR UPDATE` or UNIQUE constraint on `item_ref_no` |
| BRN-OI-014 | `getlastrefno()` race condition in tagging loop | Same fix as BRN-OI-013 |
| BRN-OI-019 | Hardcoded log status values | Model | Extract to named constants |
| BRN-OI-022 | Table name typo `ret_other_invnetory_issue` | DB + code migration required — schedule separately |
| BRN-OI-023 | Purchase cancel doesn't reverse tagged pieces | Business decision needed before fix |
| BRN-OI-024 | Duplicate `get_other_inventory_item` route | Remove duplicate controller entry |
| BRN-OI-020 | Day closing `entry_date` DATE vs DATETIME | Standardize all 3 locations to `Y-m-d H:i:s` always |
| BRN-OI-038 | `delete_gift_map_data()` fragile return value | Change `== 1` to `!== false` |

---

## Fix Sequence Recommendation

```
S1 (Critical) → Deploy → S3 (Security+XSS) → Deploy → S2 (Data Integrity) → S4 (UX) → S5 (Cleanup)
```

Start with S1 for crash bugs (BRN-OI-002), silent corruption (BRN-OI-031), missing rollback (BRN-OI-039), and null cascade (BRN-OI-042). S3 security follows for SQL injection cluster (BRN-OI-032–036) AND XSS in print view (BRN-OI-043–046) — XSS is exploitable by any user who can add a supplier or product. BRN-OI-036+042 are S1-priority on PHP8 installs.
