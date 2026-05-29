# Flow Risk Matrix — Old Metal Process Module

**Module**: `old_metal_process`
**Built**: Round 16 (2026-03-24)
**Source**: DATA_FLOW.md + CROSS_MODULE_MAP.md + controller/model code scan
**Version**: 1.0

---

## 3b-1. State Machines

### State Machine A: `ret_old_metal_melting_recd_details.melting_status`

> This single field drives the entire process pipeline. Every downstream module (Testing, Refining) reads it as a gate.

| State | Value | Set By (Method @ Line) | Can Transition To | Guard / Precondition |
|---|---|---|---|---|
| Melting Issued | 0 | `metal_process_issue/save` (Melting Issue) @ controller L451 | 1 (Melting Rcpt), 2 (Testing Issue) | Row inserted when melting header saved |
| Melting Receipt Done | 1 | `metal_process_issue/save` (Melting Receipt) @ controller L555 | 2 (Testing Issue) | `is_melting_select[$key]==1` from POST |
| Testing Issued | 2 | `metal_process_issue/save` (Testing Issue) @ controller L674 | 3 (Testing Rcpt) | `melting_status=1` filter in `get_testing_issue_details()` |
| Testing Receipt Done | 3 | `metal_process_issue/save` (Testing Receipt non-against-melting) @ controller L763 | 4 (Refining Issue) or 6 (via process_type=2) | `process_type` flag from POST; ⚠️ **NO GUARD** on re-receipt |
| Refining Issued | 4 | `metal_process_issue/save` (Refining Issue) @ controller L959 | 5 (Refining Rcpt) | `melting_status=3` filter in `get_RefiningIssueDetails()` |
| Refining Receipt Done | 5 | (via refining receipt flow — `refining_status=1`) | — (terminal) | ⚠️ **NO GUARD** checking if already 5 |
| Testing Direct Receipt | 6 | `metal_process_issue/save` (Testing Receipt, process_type=2) @ controller L763 | — (terminal) | `process_type==2` from POST; ⚠️ **UNDOCUMENTED STATE** |

> ⚠️ **Gap**: State 6 (`process_type=2`) is not in the original state machine documentation. Introduced in controller L763, it skips standard testing receipt and creates lot_from=5 directly.
> ⚠️ **Gap**: No guards prevent status from going backwards (e.g., re-setting melting_status=1 on a record already at 3).

---

### State Machine B: `ret_old_metal_pocket.status` (Pocket Closure)

| State | Value | Set By | Can Transition To | Guard |
|---|---|---|---|---|
| Open / Available | 0 | `metal_pocket/save` @ controller L234 (implicit default) | — | — |
| ~~Closed~~ | ~~1~~ | **⚠️ NEVER SET** | — | **POCKET NEVER CLOSED after full issue (OMP-007)** |

> ❌ **Critical Gap**: The pocket `status` field is never updated to "closed" (1) after all items are issued. Pockets remain at `status=0` forever. The balance check uses a HAVING clause (`issue_nwt < net_wt`) instead of marking status.

---

### State Machine C: `ret_taging.tag_status` / `ret_taging.is_pocketed` (Tagged Item)

| State | Value | Set By (Controller Line) | Can Transition To | Guard |
|---|---|---|---|---|
| Available (tag_status=6) | Pre-condition | Tagging module | Pocketed(13) | `get_melting_tag_details()` filters `tag_status=6` in model |
| Pocketed | is_pocketed=1, tag_status=13 | `metal_pocket/save` trans_type=2, L286 | — (no reversal) | ⚠️ **NO GUARD** — no check if already pocketed before setting |

---

### State Machine D: `ret_old_metal_testing.testing_status`

| State | Value | Set By | Can Transition To | Guard |
|---|---|---|---|---|
| Testing Issued | 0 | `metal_process_issue/save` (Testing Issue), L667 | 1 (Receipt) | Inserted row; no status column at insert |
| Testing Receipt Done | 1 | `metal_process_issue/save` (Testing Receipt against_melting=2), L739 | — (terminal) | `against_melting==2` check at controller L723; ⚠️ NO guard if already 1 |

---

### State Machine E: `ret_old_metal_refining.refining_status`

| State | Value | Set By | Can Transition To | Guard |
|---|---|---|---|---|
| Refining Issued | 0 | `metal_process_issue/save` (Refining Issue) @ controller L955 | 1 (Receipt) | `melting_status=3` pre-check in model |
| Refining Receipt Done | 1 | `metal_process_issue/save` (Refining Receipt) @ controller L995 | — (terminal) | ⚠️ **NO GUARD** against double-receipt |

---

## 3b-2. Inbound Contracts (What This Module Expects from Upstream)

| Upstream Module | Data / State Expected | Precondition Check in Code? | Where Checked | Risk if Violated |
|---|---|---|---|---|
| Billing | `ret_bill_old_metal_sale_details.is_pocketed = 0` (not yet pocketed) | `YES` (SQL WHERE in `get_metal_stock_list()` model) | model L223 | Double-pocketing of old metal from same bill line |
| Tagging/Inventory | `ret_taging.tag_status = 6` (sold/returned tag available) | `YES` (SQL WHERE in `get_melting_tag_details()`) | model L322, L559 | Pocketing a tag already in another pocket |
| Tagging/Inventory | `ret_taging.is_pocketed = 0` | `YES` (SQL WHERE `pocket.tag_id IS NULL`) | model L559 | — |
| Tagging/Inventory | `ret_taging.tag_process = 0 OR 2` | `YES` (SQL WHERE in pocket query) | model L322 | Tag in wrong process state accepted into pocket |
| Melting | `ret_old_metal_melting_recd_details.melting_status = 1` | `YES` (SQL WHERE in `get_testing_issue_details()`) | model L1059 | Testing issue on un-completed melting |
| Melting | `ret_old_metal_melting_recd_details.melting_status = 3` | `YES` (SQL WHERE in `get_RefiningIssueDetails()`) | model L1156 | Refining issue on un-tested materials |
| Melting | `ret_old_metal_melting.melting_status = 0` (pending receipt) | `YES` (SQL WHERE in `get_KarigarMeltingIssueDetilas()`) | model L1045 | Receipt against already-completed melting |
| Testing | `ret_old_metal_testing.testing_status = 0` | `YES` (SQL WHERE in `get_testing_receipt_details()`) | model L1109 | Double-receipt of testing |
| Refining | `ret_old_metal_refining.refining_status = 0` | `YES` (SQL WHERE in `get_RefiningReceiptDetails()`) | model L1171 | Double-receipt of refining |
| Settings | `smith_company_op_balance` exists | `NO` | — | Opening balance pocket creation fails silently |
| Customer Order | `order_status <= 3` AND `order_type = 4` | `YES` (SQL WHERE in `get_repair_pending_order()`) | model L1871 | Repair orders in wrong state accepted |

---

## 3b-3. Outbound Contracts (What This Module Guarantees to Downstream)

| Downstream Module | What OMP Guarantees | Enforced How? | Risk if Broken |
|---|---|---|---|
| Lot/Inventory | `ret_lot_inwards` record created with correct `lot_from` (2/4/5) | Code always sets `lot_from` explicitly | Wrong lot_from → inventory mis-categorization |
| Lot/Inventory | `ret_lot_inwards.created_branch` set correctly | Set from `$branchDetails['id_branch']` | `$id_branch` undefined in polishing receipt (OMP-028) → NULL created_branch |
| Lot/Inventory | `ret_lot_inwards_detail` populated per product | Code loops category_details JSON | JSON parse error → lot header orphaned with no detail rows |
| Stock/Reports | `ret_nontag_item` updated atomically (+/-) | Arithmetic SQL update — NOT a SELECT+UPDATE | Non-atomic under concurrent access; stock can drift |
| Stock/Reports | `ret_nontag_item_log` entry for every NT item change | Manual: inserted in every save path | ⚠️ **CONTRACT GAP**: polishing pocket save (trans_type=3, L362) inserts nontag_item_log but NOT section_nontag_item_log |
| Billing | `ret_bill_old_metal_sale_details.is_pocketed = 1` after pocketing | Set explicitly at pocket save, L262 | ⚠️ No rollback: if pocket save fails mid-loop, some lines flagged, others not |
| Tagging | `ret_taging.tag_status = 13`, `is_pocketed = 1` after pocketing | Set explicitly at pocket save, L286 | No rollback if DB fails after tag update but before pocket commit |
| Tagging | `ret_taging_status_log` entry for tag status change | Inserted at pocket save, L297 | ⚠️ **CONTRACT GAP**: log inserted before `trans_commit()`; if commit fails, orphan log exists |
| Acknowledgements | Process data queryable via `get_metal_process($id)` | DB FK on `id_old_metal_process` | — |
| Reports | `ret_purchase_items_log` written for every process receipt | Written in melting receipt & testing issue flows | ⚠️ **CONTRACT GAP**: NOT written for Refining Receipt or Polishing Receipt flows |

---

## 3b-4. Reversal Contracts (Cancel / Delete / Reverse)

> **Critical Finding**: The Old Metal Process module has **NO cancel or delete operations** implemented for any process type. `trans_rollback()` is used only for save errors (L101, L148, L372), not as a user-facing cancellation feature.

| Operation | Tables That Must Be Restored | Actually Restored in Code? | Method & Line | Gap? |
|---|---|---|---|---|
| Cancel Pocket | `ret_bill_old_metal_sale_details.is_pocketed → 0` | ❌ NO | — | ⚠️ **NO CANCEL IMPLEMENTED** |
| Cancel Pocket | `ret_taging.is_pocketed → 0, tag_status → 6` | ❌ NO | — | ⚠️ **NO CANCEL IMPLEMENTED** |
| Cancel Pocket | `ret_old_metal_pocket` header deleted | ❌ NO | — | ⚠️ **NO CANCEL IMPLEMENTED** |
| Cancel Melting Issue | `ret_old_metal_melting` header deleted | ❌ NO | — | ⚠️ **NO CANCEL IMPLEMENTED** |
| Cancel Melting Issue | `ret_old_metal_melting_details` rows deleted | ❌ NO | — | ⚠️ **NO CANCEL IMPLEMENTED** |
| Cancel Melting Issue | `ret_old_metal_pocket.issue_pcs/gwt/nwt → 0` | ❌ NO | — | ⚠️ **NO CANCEL IMPLEMENTED** |
| Cancel Melting Receipt | `ret_old_metal_melting.melting_status → 0` | ❌ NO | — | ⚠️ **NO CANCEL IMPLEMENTED** |
| Cancel Melting Receipt | `ret_lot_inwards` + `ret_lot_inwards_detail` → deleted | ❌ NO | — | ⚠️ Lot stock created cannot be undone |
| Cancel Melting Receipt | `ret_nontag_item` reversed | ❌ NO | — | ⚠️ NT stock increased, no reversal |
| Cancel Testing Issue | `ret_old_metal_testing` deleted, `melting_status → 1` | ❌ NO | — | ⚠️ **NO CANCEL IMPLEMENTED** |
| Cancel Testing Receipt | `ret_old_metal_testing.testing_status → 0`, `melting_status → 2` | ❌ NO | — | ⚠️ **NO CANCEL IMPLEMENTED** |
| Cancel Testing Receipt | `ret_lot_inwards` reversed | ❌ NO | — | ⚠️ Lot stock cannot be undone |
| Cancel Refining Issue | `ret_old_metal_refining` deleted, `melting_status → 3` | ❌ NO | — | ⚠️ **NO CANCEL IMPLEMENTED** |
| Cancel Refining Receipt | `ret_old_metal_refining.refining_status → 0`, NT stock reversed | ❌ NO | — | ⚠️ **NO CANCEL IMPLEMENTED** |
| Cancel Polishing Issue | `ret_old_metal_polishing` (+details) deleted, pocket quantities reversed | ❌ NO | — | ⚠️ **NO CANCEL IMPLEMENTED** |
| Cancel Polishing Receipt | `ret_old_metal_polishing_recd_details` reversed, lot reversed | ❌ NO | — | ⚠️ **NO CANCEL IMPLEMENTED** |

> **Summary**: **0 out of 10+ cancel scenarios are implemented.** All process types are write-once with no reversal path. This means any data entry error requires direct DB correction.

---

## 3b-5. Flow Risk Checklist (QA-Ready Test Scenarios)

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-OMP-001 | Pocket a bill item that is already pocketed (`is_pocketed=1`) | REJECT — item should not appear in stock list | 🔴 HIGH | ❌ |
| FR-OMP-002 | Pocket a tag that has `tag_status ≠ 6` (e.g., sold or in estimation) | REJECT — tag not in pocket selection list | 🔴 HIGH | ❌ |
| FR-OMP-003 | Pocket a tag that already has `is_pocketed=1` | REJECT — tag invisible in metal stock list | 🔴 HIGH | ❌ |
| FR-OMP-004 | Create melting issue with pocket that has 0 net_wt balance | REJECT or show zero-weight row | 🟡 MED | ❌ |
| FR-OMP-005 | Create melting receipt when `melting_status` is already 1 (double receipt) | Should REJECT; currently ⚠️ NO GUARD | 🔴 HIGH | ❌ |
| FR-OMP-006 | Create testing issue for record with `melting_status ≠ 1` | REJECT — record should not appear | 🔴 HIGH | ❌ |
| FR-OMP-007 | Create testing receipt when `testing_status` is already 1 | Should REJECT; currently ⚠️ NO GUARD | 🔴 HIGH | ❌ |
| FR-OMP-008 | Create refining issue for record with `melting_status ≠ 3` | REJECT — record should not appear | 🔴 HIGH | ❌ |
| FR-OMP-009 | Create refining receipt when `refining_status` is already 1 | Should REJECT; currently ⚠️ NO GUARD | 🔴 HIGH | ❌ |
| FR-OMP-010 | Save pocket with `total_pcs = 0` or `total_gross_wt = 0` | REJECT with error message | 🟡 MED | ❌ (guard added at L210) |
| FR-OMP-011 | Melting receipt — verify `ret_lot_inwards.created_branch` is NOT NULL | Should equal branch from session | 🔴 HIGH | ❌ (polishing receipt OMP-028 gap) |
| FR-OMP-012 | Polishing receipt — verify `ret_lot_inwards.created_branch` is set | NOT NULL | 🔴 HIGH | ❌ OMP-028 known bug |
| FR-OMP-013 | Cancel any process type as end-user | Should succeed; currently ❌ NO UI/API | 🟡 MED | ❌ NOT IMPLEMENTED |
| FR-OMP-014 | Pocket save fails midway (mock DB error after first detail row insert) | No orphan pocket_details rows | 🔴 HIGH | ❌ |
| FR-OMP-015 | Process save crashes after `ret_old_metal_process` insert but before detail insert | Orphan process header exists; no child records | 🔴 HIGH | ❌ (known risk OMP-009) |
| FR-OMP-016 | Two concurrent users save melting issue for same pocket | Only one succeeds with correct piece counts | 🟡 MED | ❌ (OMP-005 race condition) |
| FR-OMP-017 | Melting receipt: verify stock in `ret_nontag_item` after save matches sum of category_details | NT stock = pocket source stock minus issues + received wt | 🔴 HIGH | ❌ |
| FR-OMP-018 | Testing receipt category_details JSON — verify lot_inwards_detail count matches category rows | 1 lot detail per category | 🟡 MED | ❌ |
| FR-OMP-019 | Refining receipt — verify `ret_purchase_items_log` is written | Log entry: `item_type=4` | 🟡 MED | ❌ ⚠️ CONTRACT GAP: currently NOT written |
| FR-OMP-020 | GST type check — karigar in different state should give IGST | `get_chg_tax_type()` returns correct type | 🔴 HIGH | ❌ OMP-002 known bug (uses id_company not id_state) |
| FR-OMP-021 | Net Banking payment for receipt process — verify `net_banking_amount` is recorded | Column = NB amount, not cash amount | 🔴 HIGH | ❌ OMP-003 known bug |
| FR-OMP-022 | `#category_row` in 3 different receipt modals — verify row operations don't cross-contaminate | Each modal manages its own rows | 🟡 MED | ❌ OMP-022 DOM collision |
| FR-OMP-023 | `calculate_tag_list()` — check purity average with some rows unchecked | Only checked rows should contribute to average | 🟠 HIGH | ❌ OMP-060 known bug |
| FR-OMP-024 | `get_tag_search_list()` — enter tag code with special HTML chars | Should be safely escaped; currently 9 unquoted attrs | 🟠 HIGH | ❌ OMP-061 known bug |
| FR-OMP-025 | `check_purity_stock_details()` — verify it returns correct stock status | Returns TRUE when stock exists | 🔴 HIGH | ❌ OMP-055 double-query bug always returns FALSE |

---

## Summary of Key Gaps

| Gap Category | Count | Severity |
|---|---|---|
| No cancel/reversal operations | 10+ | 🔴 CRITICAL — any data error needs DB surgery |
| Missing status transition guards | 4 | 🔴 HIGH — double receipt/double issue possible |
| Cross-module write gaps (log missing) | 2 | 🟡 MED — audit trail incomplete |
| Pocket status never closed | 1 | 🟠 HIGH — orphan pockets accumulate |
| Undocumented `process_type=2` state=6 path | 1 | 🟡 MED — unlisted state transition |

> **QA Priority**: FR-OMP-005, 007, 009 (double-receipt guards) and FR-OMP-013 (cancel implementation) are the highest-risk missing features for production use.
