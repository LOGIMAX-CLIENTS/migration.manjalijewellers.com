# LOT MODULE — FLOW RISK MATRIX
> Module: Lot | Round 16 | 2026-03-24
> Input: DATA_FLOW.md + CROSS_MODULE_MAP.md + live model scan

---

## 3b-1. State Machine

### State Machine: `ret_lot_inwards.lot_status`

| State | Value | Set By (Method) | Can Transition To | Guard / Precondition |
|---|---|---|---|---|
| Active | 0 (default) | `lot_inward('save')` — `insertData()` L1023 | Cancelled(2) | — |
| Cancelled | 2 | `lot_inward('cancel_lot_entry')` — `updateData()` L1735 | — (terminal) | reason string required |
| *(unknown)* | 1 | ⚠️ Never set by Lot module — value 1 not assigned anywhere | Unknown | ⚠️ NO GUARD — status=1 unreachable via UI |

> **Note**: `lot_status` is only visibly used for filtering in the list (`lot_status=0` shows active). Cancel sets it to 2. Status 1 is referenced nowhere in active code paths — may be dead/legacy.

### State Machine: `ret_lot_inwards.is_closed`

| State | Value | Set By (Method) | Can Transition To | Guard / Precondition |
|---|---|---|---|---|
| Open | 0 (default) | `lot_inward('save')` — INSERT | Closed(1) | — |
| Closed | 1 | `lot_completed()` — `updateData()` L2505 | — (terminal) | ⚠️ WHERE uses `'lot_no '` with trailing space — may fail silently |

### State Machine: `ret_lot_inwards.is_lot_split`

| State | Value | Set By (Method) | Can Transition To | Guard / Precondition |
|---|---|---|---|---|
| Not Split | 0 (default) | `lot_inward('save')` — INSERT | Split(1) | — |
| Split | 1 | `lot_split('save')` — `updateData()` L2387 | — (terminal) | No check if lot already tagged |

> ⚠️ **R-LOT-023**: `lot_completed()` WHERE clause uses `'lot_no '` (trailing space) — UPDATE may silently affect 0 rows.

---

## 3b-2. Inbound Contracts

### What This Module Expects from Upstream

| Upstream Module | Data/State Expected | Precondition Check in Code? | Line | Risk if Violated |
|---|---|---|---|---|
| **Settings** | `lot_recv_branch` setting exists | `get_ret_settings('lot_recv_branch')` | Model L578 | Wrong default branch — HO logic breaks |
| **Settings** | `getBranchDayClosingData()` returns valid date | NO null check | Controller L340 | Lot saved with wrong/null date |
| **Tagging** | `ret_taging.tag_status != 2` AND `!= 5` for merge/split eligibility | YES — subquery filter | Model L1170–1175 | — |
| **Tagging** | `ret_taging.tag_lot_id IS NULL` (no tagged items) for merge candidate | YES — `lt_tag.tag_lot_id IS NULL` | Model L1192 | Merge on tagged lot → loss of tag links |
| **Orders** | `customerorder.order_no` exists when `lot_type=2` | NO explicit check — FK join only | Model L527 | Lot references deleted order — phantom data |
| **Purchase/GRN** | `po_id` or `grn_id` valid when `lot_from=2/3` | NO validation | Model L211–213 | Lot origin display broken; po_ref_no = NULL |
| **Old Metal** | `id_metal_process` valid when `lot_from=5` | NO validation | Model L215 | Lot origin display broken; process_no = NULL |
| **Karigar** | `gold_smith` (id_karigar) exists | NO explicit check — LEFT JOIN only | Model L209 | Goldsmith name = NULL on print/display |
| **NonTag Inventory** | `ret_nontag_item` record exists when `arith='+'` update | YES — `checkNonTagItemExist()` | Model L1054 | N/A (handled) |
| **Catalog** | UOM, stone, charge, product records exist | NO — all LEFT JOINs | Various | NULL values in display; no save error |

---

## 3b-3. Outbound Contracts

### What This Module Guarantees to Downstream

| Downstream Module | What This Module Guarantees | Enforced How? | Risk if Broken |
|---|---|---|---|
| **Tagging** | `ret_lot_inwards` header row exists before tagging | FK join (tag_lot_id) | No FK constraint → phantom tag reads if lot deleted (R-LOT-012) |
| **Tagging** | `ret_lot_inwards_detail` rows exist (tagged via id_lot_inward_detail) | NO FK constraint | Tags with orphan detail_id → NaN in stock calculations |
| **Tagging** | `lot_status=0` (active) when tagging occurs | ⚠️ NO check in Lot module | Tagging on cancelled lot possible |
| **Tagging** | `is_closed=0` when tagging occurs | ⚠️ NO check — Tagging doesn't read is_closed | Tagged items from closed lot = accounting error |
| **Estimation** | `ret_lot_inwards_detail.id_lot_inward_detail` valid when fetched by Estimation model | NO FK constraint | Estimation reads deleted lot detail → wrong cost data |
| **Stock Issue** | `ret_lot_inwards` / `_detail` rows remain while stock issues reference them | NO FK constraint | Stock issue references orphan lot header (R-LOT-012) |
| **Sales Transfer** | `ret_lot_inwards` header row valid for joins | NO FK constraint | Sales transfer joins return NULL row |
| **NonTag Inventory** | `ret_nontag_item` quantities + on insert, – on delete | ⚠️ PARTIAL — increment on save; **NO decrement on lot delete** | Stock quantity inflated after lot delete (R-LOT-012 extension) |
| **NonTag Inventory** | `ret_nontag_item` not updated on lot cancel | ❌ NO — cancel only sets lot_status=2 | NT stock count remains inflated after lot cancel |
| **Audit Log** | `log_model->log_detail()` called on Add/Edit/Delete/Cancel/Split | YES | If trans_commit fails before log, audit is inconsistent |

> ⚠️ **CONTRACT GAP**: No FK constraints on any Lot table. All downstream modules rely on application-layer data integrity only.

---

## 3b-4. Reversal Contracts

### Cancel Lot (`lot_inward/cancel_lot_entry`)

**Tables written during CREATE** vs **what is restored during CANCEL**:

| Table Written on CREATE | Restored on Cancel? | Method | Gap? |
|---|---|---|---|
| `ret_lot_inwards` (lot_status=0) | ✅ PARTIAL — `lot_status` → 2, `cancel_reason` set | `cancel_lot_entry()` L1735 | Partially restored ✅ |
| `ret_lot_inwards_detail` | ❌ NO — rows remain | — | ⚠️ Orphan detail rows |
| `ret_lot_inwards_stone_detail` | ❌ NO — rows remain | — | ⚠️ Orphan stone rows |
| `ret_lot_other_items` | ❌ NO — rows remain | — | ⚠️ Orphan other metal rows |
| `ret_lot_other_charges` | ❌ NO — rows remain | — | ⚠️ Orphan charge rows |
| `ret_nontag_item` (quantity +) | ❌ NO — quantity not reversed | — | ⚠️ NT stock count stays inflated |
| `ret_nontag_item_log` | ❌ NO — log row not reversed | — | Audit trail incomplete |
| `ret_section_nontag_item_log` | ❌ NO | — | Audit trail incomplete |

> 💥 **Cancel is NOT a true reversal.** It only stamps `lot_status=2` — all child rows remain and downstream modules that ignore `lot_status` will still read cancelled lot data.

### Delete Lot (`lot_inward/delete/{id}`)

| Table Written on CREATE | Restored on Delete? | Method | Gap? |
|---|---|---|---|
| `ret_lot_inwards` | ✅ YES — DELETE | `deleteData()` L1660 | ✅ |
| `ret_lot_inwards_detail` | ❌ NO — rows NOT deleted | — | ⚠️ **R-LOT-012** |
| `ret_lot_inwards_stone_detail` | ❌ NO | — | ⚠️ **R-LOT-012** |
| `ret_lot_other_items` | ❌ NO | — | ⚠️ Orphan rows |
| `ret_lot_other_charges` | ❌ NO | — | ⚠️ Orphan rows |
| `ret_nontag_item` (quantity +) | ❌ NO — quantity not reversed | — | ⚠️ Stock count inflated |
| `ret_nontag_item_log` | ❌ NO | — | — |
| `ret_section_nontag_item_log` | ❌ NO | — | — |
| Image folder `assets/img/lot/{id}` | ✅ YES — `rrmdir()` | L1669 | ✅ |

> 💥 **R-LOT-012**: `ret_lot_inwards_detail` rows survive deletion. Tagging module's 324+ queries join to `ret_lot_inwards_detail.lot_no` — after Lot header deleted, detail rows become orphans. Tagging stock calculations produce phantom results.

### Lot Edit / Update

| Table Written on CREATE | Handled During UPDATE? |
|---|---|
| `ret_lot_inwards` | ✅ Updated |
| `ret_lot_inwards_detail` | ✅ Updated (existing rows) / Inserted (new rows) |
| `ret_lot_inwards_stone_detail` | ✅ DELETE all + re-insert |
| `ret_lot_other_items` | ❌ **NOT cleaned up** — stale charges remain (R-LOT-006) |
| `ret_lot_other_charges` | ❌ **NOT cleaned up** — stale charges remain (R-LOT-006) |

---

## 3b-5. Flow Risk Checklist (QA-Ready Test Scenarios)

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-LOT-001 | DELETE lot with tagged items (tag_status != 2) | REJECT or warn — lot has active tags | 🔴 HIGH | ❌ |
| FR-LOT-002 | DELETE lot → verify Tagging module stock calculations | No change to tag balance (orphan rows must not affect counts) | 🔴 HIGH | ❌ |
| FR-LOT-003 | DELETE lot → re-create lot with same products → verify NT stock is not double-counted | NT quantity should equal actual; current: inflated by uncleaned rows | 🔴 HIGH | ❌ |
| FR-LOT-004 | CANCEL lot (lot_status=2) → verify downstream modules (Tagging, Estimation, StockIssue) still respect cancellation | Downstream modules should filter out lot_status=2; currently no check | 🔴 HIGH | ❌ |
| FR-LOT-005 | CLOSE lot (`lot_completed`) → verify `is_closed=1` actually written | Check DB after `lot_completed` AJAX — trailing space in WHERE may cause silent failure | 🔴 HIGH | ❌ |
| FR-LOT-006 | EDIT lot → modify other charges → verify old charges removed | Old `ret_lot_other_items` / `ret_lot_other_charges` rows should be gone; currently they accumulate | 🟠 MED | ❌ |
| FR-LOT-007 | EDIT lot → re-submit stone details → verify old stones deleted before re-insert | Old stone rows deleted; ✅ confirmed in code — verify no duplication in DB | 🟡 LOW | ❌ |
| FR-LOT-008 | MERGE lots → verify source lots are marked / inaccessible post-merge | Source lots not flagged as merged in `ret_lot_inwards.lot_status`; can still be accessed and edited | 🟠 MED | ❌ |
| FR-LOT-009 | Split lot → try to split same lot again | `is_lot_split=1` should block re-split; verify `getLotidsforSplit()` WHERE filter works | 🟡 LOW | ❌ |
| FR-LOT-010 | `lot_completed` with non-existent lot_no | Should return error; trailing space in WHERE means UPDATE affects 0 rows silently | 🟠 MED | ❌ |
| FR-LOT-011 | Save fails mid-transaction (simulate by checking trans_begin/complete wrapping) | No orphan rows; rollback must be clean | 🔴 HIGH | ❌ |
| FR-LOT-012 | Save with `lot_from=2` (Supplier Entry) → verify po_ref_no displayed correctly | If `po_id` null, `pur_ref_no` should show empty string; verify IFNULL handling | 🟡 LOW | ❌ |
| FR-LOT-013 | Two users save different lots concurrently | Both succeed (no shared sequence issue) | 🟡 LOW | ❌ |
| FR-LOT-014 | Image upload failure during save → verify transaction integrity | Main lot record rolled back; no orphan images in folder | 🟠 MED | ❌ |
| FR-LOT-015 | Delete lot that has been merged (exists in `ret_lot_merge`) | Should REJECT; currently no guard — orphaned merge records result | 🔴 HIGH | ❌ |

---

## Quick Reference — Top Gaps

| Bug ID | Gap | Severity |
|---|---|---|
| R-LOT-012 | Delete does not cascade to `ret_lot_inwards_detail` / `_stone` — breaks Tagging (324+ queries) | 🔴 Critical |
| R-LOT-023 | `lot_completed()` WHERE has trailing space `'lot_no '` — silent no-op | 🔴 Critical |
| R-LOT-006 | Edit does not clean `ret_lot_other_items` / `ret_lot_other_charges` — stale charges accumulate | 🟠 High |
| R-LOT-003 | Trailing space in stone INSERT column names | 🟠 High |
| *(new)* | Cancel does not reverse NT stock quantity | 🟠 High |
| *(new)* | No guard preventing tagging/estimation on cancelled lots | 🟠 High |
| *(new)* | No guard preventing delete of merged lots | 🔴 Critical |
