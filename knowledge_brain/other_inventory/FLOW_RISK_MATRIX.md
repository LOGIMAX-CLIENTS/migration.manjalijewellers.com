# FLOW RISK MATRIX — Other Inventory
> **Module:** Other Inventory | **Built:** 2026-03-24 (Round 16)
> **Input sources:** DATA_FLOW.md, CROSS_MODULE_MAP.md, SCHEMA_ANALYSIS.md, BUG_CANDIDATES.md

---

## 3b-1. State Machine

### State Machine: `ret_other_inventory_purchase_items_details.status`

> Primary entity for tracking physical piece availability. Every piece of Other Inventory stock has exactly one row here.

| State | Value | Set By (Module.Method) | Can Transition To | Guard / Precondition |
|---|---|---|---|---|
| Available | 0 | OI.`product_details('save')` → L752 | Issued(1) | `current_branch` must match requesting branch |
| Issued | 1 | OI.`issue_item('save')` → controller L145, or Billing `admin_ret_billing` L4522 | Available(0) — ⚠️ NO reversal path | `id_inventory_issue` must be set |

> **⚠️ NO GUARD** — When Billing writes status=1 (issued via billing), there is NO pre-check that the piece is still Available (status=0). Race condition: two billing saves for the same piece will both succeed, consuming the same physical piece twice. (Related: BRN-OI-047)

---

### State Machine: `ret_other_inventory_purchase.purchase_bill_status`

| State | Value | Set By (Module.Method) | Can Transition To | Guard / Precondition |
|---|---|---|---|---|
| Active | 1 | OI.`purchase_entry('save')` L572 | Cancelled(2) | — |
| Cancelled | 2 | OI.`purchase_entry('cancel_purchase_entry')` L677 | — (terminal) | `cancel_reason` must be provided (POST only) |

> **⚠️ NO GUARD** — Cancel does NOT check if pieces have already been tagged (`ret_other_inventory_purchase_items_details` records may exist). A cancelled purchase can leave tagged pieces orphaned with valid status=0.

---

### State Machine: `ret_other_inventory_purchase_items_log.status`

> Movement ledger — every inward/outward event creates a row. No status transition; rows are immutable append-only.

| Status | Value | Written By | Meaning |
|---|---|---|---|
| Inward | 0 | OI.`product_details('save')` L765 | Piece received at HO branch |
| Issue (OI/Billing) | 1 | OI.`issue_item('save')` L146, or `admin_ret_billing` | Piece issued to customer |
| BT Other Issue | 3 | `admin_ret_brntransfer` L608 | Piece removed for Branch Transfer (is_other_issue=1) |
| BT In-Transit | 4 | `admin_ret_brntransfer` L812 | Piece in transit to dest branch (not yet received) |

---

## 3b-2. Inbound Contracts

> What Other Inventory expects from upstream modules before it can function correctly.

| Upstream Module | Data/State Expected | Precondition Check in Code? | Line | Risk if Violated |
|---|---|---|---|---|
| **Settings / Day Closing** | `ret_day_closing.entry_date` must exist for the branch | ⚠️ PARTIAL — `getBranchDayClosingData()` is called but return value is used without null-guard | Purchase: L560, Issue: L137 | `entry_date` becomes NULL → INSERT of purchase/issue header fails with DB error |
| **Settings / Day Closing** | `is_day_closed` determines whether to use today's date or DC's `entry_date` | YES — ternary at `$dCData['entry_date'] == date("Y-m-d")` | L560 | If day closing record is absent entirely, PHP warning + null entry_date |
| **Branch** | `branch.is_ho = 1` must exist to identify Head Office for purchase/tagging entry | NO check for null return | L558, L733 | `$branch['id_branch']` is null → all inserts fail silently (nulls in FK columns) |
| **Supplier (ret_karigar)** | `id_karigar` must be a valid active supplier (karigar_for=4) | NO — only POST value used | Purchase L563 | Ghost purchase with invalid supplier; print shows null name |
| **Billing (ret_billing)** | `bill_id` must be a valid active bill (bill_status=1) for issue linkage | NO — only bill_id forwarded from JS POST | Issue L138 | Issue linked to invalid bill_id; reconciliation breaks |
| **ret_product_master** | Product must exist for product mapping | NO — insertData with raw id_product | `update_product_mapping()` L1172 | Orphan `ret_other_inventory_product_link` row if product is deleted later |
| **Scheme/Chit Module** | `id_scheme` must be a valid scheme for `gift_mapping` | NO — no validation of scheme ID | `other_inventory('save')` L186 | Gift mapping references non-existent scheme → scheme module may error |
| **Catalog UOM** | `active_uom` AJAX must return valid UOM list | NO server-side validation — JS only | Form: JS `get_uom_list()` | UOM dropdown empty → user may submit with null UOM |

---

## 3b-3. Outbound Contracts

> What Other Inventory guarantees to downstream modules.

| Downstream Module | What This Module Guarantees | Enforced How? | Risk if Broken |
|---|---|---|---|
| **Billing** | `ret_other_inventory_purchase_items_details.status=0` when piece is available | No explicit check before Billing consumes pieces | Billing can issue an already-issued piece (race condition BRN-OI-047) |
| **Billing** | `ret_other_inventory_item.id_other_item` is a valid item when issue is created | FK in details table only — no soft delete prevention | If item is hard-deleted (Flow 3), existing detail rows become orphaned; billing read fails |
| **Branch Transfer** | Piece `current_branch` reflects actual physical location | Updated only via OI issue flow; BT writes its own log entries | If BT writes status=3/4 log without updating `current_branch`, stock report counts diverge from actual location |
| **Scheme/Chit** | `gift_mapping` rows have correct key structure (no trailing spaces in `id_other_item`, `id_scheme`, `item_issue_limit`) | ⚠️ NOT guaranteed — `issue_item('save')` L935-941 has trailing space keys → silent NULL inserts (BRN-OI-031) | ⚠️ **CONTRACT GAP** — Scheme module reads gift_mapping rows with NULL id_scheme and id_other_item; eligibility check always fails for these items |
| **Scheme/Chit** | `gift_mapping` rows are consistent (delete+reinsert on edit) | delete_gift_map_data() called on OI item edit | If OI item is deleted without cleaning gift_mapping → stale rows in shared table; scheme module may error |
| **Stock Reports** | `ret_other_inventory_purchase_items_log` accurately reflects all movements | Log insert called on every tagging (status=0) and issue (status=1) | If `product_details('save')` crashes mid-loop (no trans_begin! — BRN-OI-007), some pieces get log entries, others don't → stock count is wrong |
| **Reorder Reports** | `ret_other_inventory_reorder_settings` has one row per branch-item combo | Delete-then-insert on every edit | If edit crashes mid-transaction, previous settings deleted, new ones not inserted → reorder alerts stop for that item |

---

## 3b-4. Reversal Contracts

> For each cancel/delete/reverse operation, what tables must be restored to pre-operation state.

### Cancel: Purchase Entry (`purchase_entry/cancel_purchase_entry`)

| Table | Must Be Restored | Actually Restored in Code? | Method & Line | Gap? |
|---|---|---|---|---|
| `ret_other_inventory_purchase.purchase_bill_status` | → 2 (Cancelled) | ✅ YES | `cancel_purchase_entry` L677 | — |
| `ret_other_inventory_purchase_items` | No reversal needed (line items remain) | N/A — no item removal | — | Acceptable |
| `ret_other_inventory_purchase_items_details` | Pieces tagged from this purchase still exist with status=0 | ❌ NO — pieces remain valid stock | — | ⚠️ **GAP** — Pieces from a cancelled purchase remain in available stock. Stock report inflated. (BRN-OI-023 related) |
| `ret_other_inventory_purchase_images` | Disk files not removed | ❌ NO | — | ⚠️ **GAP** — Orphan image files on disk |

### Delete: Item Master (`other_inventory/delete`)

| Table | Must Be Restored/Cleaned | Actually Cleaned in Code? | Method & Line | Gap? |
|---|---|---|---|---|
| `ret_other_inventory_item` | → deleted | ✅ YES | `other_inventory('delete')` L255 | — |
| `ret_other_inventory_reorder_settings` | → deleted | ❌ NO | — | ⚠️ **GAP** — Orphan reorder settings remain |
| `gift_mapping` | → deleted | ❌ NO | — | ⚠️ **GAP** — Orphan gift scheme rows remain; scheme module may malfunction |
| `ret_other_inventory_purchase_items_details` | → should be blocked if stock exists | ❌ NO pre-check / NO cleanup | — | ⚠️ **GAP** — Detail records reference deleted item; stock queries return null names |
| `ret_other_inventory_product_link` | → deleted | ❌ NO | — | ⚠️ **GAP** — Product mapping orphaned |

### Reverse / Cancel: Issue (`issue_item` — no cancel endpoint exists)

| Table | Must Be Restored | Actually Restored in Code? | Method & Line | Gap? |
|---|---|---|---|---|
| `ret_other_invnetory_issue` | → deleted / marked cancelled | ❌ NO cancel endpoint | — | ⚠️ **GAP** — No issue reversal exists; once issued, pieces cannot be returned via OI module |
| `ret_other_inventory_purchase_items_details.status` | → 0 (Available) | ❌ NO | — | ⚠️ **GAP** — Pieces remain status=1 permanently if issue is wrong |
| `ret_other_inventory_purchase_items_log` | → reverse log entry | ❌ NO | — | ⚠️ **GAP** — Log never reversed; stock report permanently shows the outward |
| `gift_mapping` | → bonus gift rows should be removed | ❌ NO (and pre-issue inserts are outside trans_begin — BRN-OI-016) | — | ⚠️ **GAP** — Gift mapping rows cannot be reversed |

### Reverse / Delete: Category (`inventory_category/delete`)

| Table | Must Be Cleaned | Actually Cleaned? | Method & Line | Gap? |
|---|---|---|---|---|
| `ret_other_inventory_item_type` | → deleted | ✅ YES | `inventory_category('delete')` L471 | — |
| `ret_other_inventory_item.item_for` | → items with this category now have orphan FK | ❌ NO pre-check | — | ⚠️ **GAP** — Items reference deleted categories; category name shows null in lists |

---

## 3b-5. Flow Risk Checklist (QA-Ready Test Scenarios)

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-OI-001 | Issue item when `ret_day_closing` record is missing for the branch | REJECT with clear "Day not opened" error (not a PHP null warning) | 🔴 HIGH | ❌ |
| FR-OI-002 | Tag pieces from a CANCELLED purchase (`purchase_bill_status=2`) | REJECT — cancelled purchase pieces should not be available for tagging | 🔴 HIGH | ❌ |
| FR-OI-003 | Two simultaneous Billing sessions issue the same available piece | ONE succeeds, ONE rejects with "Stock not available" error | 🔴 HIGH | ❌ |
| FR-OI-004 | Cancel purchase entry AFTER pieces have been tagged | REJECT cancel OR warn and reverse tagged piece records | 🔴 HIGH | ❌ |
| FR-OI-005 | Delete item master when active stock pieces exist in `purchase_items_details` | REJECT delete with "Stock exists" error — not a silent orphan creation | 🔴 HIGH | ❌ |
| FR-OI-006 | Delete item category that is in use by existing items | REJECT with "Category in use" error | 🟠 MEDIUM | ❌ |
| FR-OI-007 | Issue items with gift scheme mapping — verify `gift_mapping` rows have correct keys (no trailing spaces) | DB row has correct `id_other_item`, `id_scheme`, `item_issue_limit` (not NULL) | 🔴 HIGH | ❌ |
| FR-OI-008 | `product_details/save` crashes mid-loop (simulate via forked request) — check for orphan detail records without log entries | No orphan records; partial insert should be rolled back | 🔴 HIGH | ❌ |
| FR-OI-009 | Purchase entry save crashes after image upload but before trans_commit | Orphan image files should NOT exist; DB records should be rolled back | 🟠 MEDIUM | ❌ |
| FR-OI-010 | Issue item when `branch.is_ho` record is missing | REJECT with "Head office not found" — not null FK insertion | 🔴 HIGH | ❌ |
| FR-OI-011 | Cancel purchase, then attempt to tag its pieces | Pieces should not appear in product tagging ref_no dropdown | 🟠 MEDIUM | ❌ |
| FR-OI-012 | Billing issues OI item (issue_form=1) — verify `purchase_items_details.status` = 1 and `id_inventory_issue` is set | Both fields updated atomically | 🔴 HIGH | ❌ |
| FR-OI-013 | Billing commits OI issue BEFORE OI insert completes (race on BRN-OI-047) | OI issue succeeds or billing rolls back — no ghost bill with un-deducted stock | 🔴 HIGH | ❌ |
| FR-OI-014 | Stock report after: purchase → tag → issue → check log-based total | `op_blc + inw - out = 0` for a fully issued item | 🟡 MEDIUM | ❌ |
| FR-OI-015 | Reorder settings edit fails mid-transaction (after DELETE, before INSERT) | Reorder settings are preserved (rollback), NOT left empty | 🟠 MEDIUM | ❌ |
| FR-OI-016 | Concurrent save of same OI item by 2 users (one save, one edit-update) | No `stock_id_uom` / `issue_to` overwrite by the update path (BRN-OI-012) | 🟡 MEDIUM | ❌ |
| FR-OI-017 | CANCEL purchase_entry — verify `getlastrefno()` still works correctly after cancel | Purchase ref_no logic must not use cancelled entries as MAX baseline | 🟡 MEDIUM | ❌ |
| FR-OI-018 | DELETE item master — verify `gift_mapping`, `reorder_settings`, `product_link` are cleaned | All child tables cleaned, no orphan rows | 🔴 HIGH | ❌ |

---

## Summary of Key Contract Gaps

| Gap | Severity | Related Bug | Quick Fix |
|---|---|---|---|
| No available-stock pre-check before Billing issues piece | 🔴 HIGH | BRN-OI-047 | Add `SELECT status FROM...WHERE status=0` before update in billing |
| `product_details/save` has no `trans_begin()` | 🔴 HIGH | BRN-OI-007 | Add `trans_begin()` at top of save case |
| Cancelled purchase pieces remain in available stock | 🔴 HIGH | BRN-OI-023 | On cancel: UPDATE `purchase_items_details` SET status=2 WHERE inv_pur_itm_id IN (...) |
| No issue reversal endpoint | 🔴 HIGH | (undocumented) | Create a `cancel_issue` endpoint mirroring billing cancel |
| Delete item master has no child cleanup | 🔴 HIGH | BRN-OI-001 (scope) | Add FK-aware cleanup or block delete if children exist |
| `gift_mapping` trailing space keys corrupt issue data | 🔴 HIGH | BRN-OI-031 | Trim trailing spaces from 3 array keys at controller L935-941 |
| `gift_mapping` inserts before `trans_begin()` | 🟠 MEDIUM | BRN-OI-016 | Move `trans_begin()` to before gift_mapping inserts |
| Day closing null not handled | 🟠 MEDIUM | — | Add null guard: `if (!$dCData) { return error; }` |
