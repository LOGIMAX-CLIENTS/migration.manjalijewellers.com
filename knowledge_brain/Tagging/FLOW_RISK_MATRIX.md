# Tagging Module — Flow Risk Matrix

> **Module**: Tagging
> **Built**: 2026-03-24 (Round 8)
> **Verified**: 2026-03-24 (Round 9 — all gaps confirmed in source code)
> **Brain Version**: 1.7
> **Source**: DATA_FLOW.md (8 flows) + CROSS_MODULE_MAP.md

---

## 3b-1. State Machine: `ret_taging.tag_status`

The primary entity is `ret_taging`. Its `tag_status` field is the most critical shared state — written by **multiple modules** with no centralized guard.

| State | Value | Set By (Module.Method) | Can Transition To | Guard / Precondition |
|---|---|---|---|---|
| On Sale (Active) | 0 | Tagging.`tagging('save')` | 1,2,3,4,5,6,7,8,9,10,11,13,14,17 | — (initial state on creation) |
| Sold Out | 1 | Billing.`billing('save')` | 0 (via bill cancel) | Must have billed record; Billing controls this |
| Deleted | 2 | Tagging.`tagging('delete')` case L2079–2191 | — (soft delete, record stays) | Requires prior OTP via `send_tag_otp()` L5418 |
| Other Issue / Retagged | 3 | Tagging.`create_retag()` L6755 | — (source tag terminal after retag) | Source tags selected for retag |
| In Transit | 4 | Branch Transfer module | 0 (received at dest) | Branch transfer record must exist |
| Deleted for Stock | 5 | Stock Issue module | — | Stock issue approval |
| Sales Return | 6 | Billing cancel / Returns module | 0 | Bill cancellation confirmed |
| Stock Issue (Mktg/Photo) | 7 | Stock Issue module | 0 (returned) | Stock issue record |
| Repair Item | 8 | Repair module | 0 (after repair) | Repair job record |
| Purchase Return | 9 | Purchase Return module | — | Purchase return approved |
| EDA Sale | 10 | Billing/EDA module | — | EDA billing |
| Booked for Advance | 11 | Customer Order / Advance module | 0 (if advance cancelled), 1 (sold) | Advance payment record |
| Added to Pocket | 13 | Pocket module | 0 (released) | — |
| Transferred to Home Section | 14 | Section Transfer module | 0 | Section transfer record |
| Metal Issue | 17 | Metal Process module (`ret_metal_process_model`) | 0 (returned) | Metal issue record |

> ⚠️ **RISK-004 (Open)**: No centralized state machine. Each module writes `tag_status` directly. Status 12, 15, 16 are unused/reserved — undocumented in code. Status 5, 6, 7, 8, 9, 10, 13, 14 have **NO GUARD** checked by Tagging module itself.

**Key Status Setters Found in Tagging Controller/Model (R9 Verified):**

| Method | Status Written | Tables | Line |
|---|---|---|---|
| `tagging('save')` | 0 (creation, tag_status default) | `ret_taging` | L255 |
| `tagging('delete')` case | 2 (soft deleted) + log entries | `ret_taging`, `ret_taging_status_log`, `ret_section_tag_status_log` | L2079–2191 |
| `create_retag()` | 3 (old tags retagged) | `ret_taging` | L6755 |
| `update_tag_mark()` | various mark values | `ret_taging` | L5802 |
| `update_order_link()` | Updates `id_orderdetails` field (not tag_status) | `ret_taging`, `customerorderdetails`, `joborder` | L6047 |

---

## 3b-2. Inbound Contracts (What Tagging Expects from Upstream)

For each upstream module that feeds data INTO Tagging, what must be true before Tagging acts:

| Upstream Module | Data/State Expected | Precondition Check in Code? | Line | Risk if Violated |
|---|---|---|---|---|
| Lot Inward | `ret_lot_inwards` record exists with matching `id_inward` | YES — model reads lot data before insert | L~400 (save loop) | Tag created with invalid lot ref → phantom inventory |
| Lot Inward | `ret_lot_inward_detail.balance_piece > 0` (sufficient qty in lot) | ⚠️ PARTIAL — deducts balance but no explicit pre-check for zero | L~500 (deduction loop) | Tag created exceeding lot capacity → balance goes negative |
| Lot Inward | `ret_lot_inwards.lot_status = 0` (open lot, not closed) | ❌ NO explicit check on lot_status before tagging; L1161 only returns `is_lot_completed` in success response | — | Tags created from a closed/invoiced lot |
| Lot Inward (Retag) | Source lot exists and is open | ❌ NO check | — | Retag creates from already-closed lot |
| Billing | `tag_status ≠ 1` (tag not already sold) before tag edit/delete | ⚠️ PARTIAL — billing_model loaded but `tagging('delete')` case L2079 does NOT check tag_status before soft-deleting | L2079 | Deleting a sold tag = data corruption in billing |
| Branch Transfer | Cross-branch flag set correctly in POST | NO explicit guard — trusts JS POST data | L~400 | Wrong branch transfer created |
| Customer Order | `ret_customer_order_details` record valid before link | YES — FK constraint on DB | — | DB error if order ID invalid |
| Customer Order | `tag_status = 0` (available) before order-link | ❌ NO guard in `update_order_link()` L6047 — goes directly to UPDATE without checking tag status | L6047 | Linking a sold/deleted tag to an order |
| Metal Rates | Branch-wise rate exists in `ret_metal_rate` | ⚠️ NO guard — JS reads rate, server uses posted value | — | Tag saved with 0 or stale metal rate |

---

## 3b-3. Outbound Contracts (What Tagging Guarantees to Downstream)

What downstream modules depend on Tagging to maintain correctly:

| Downstream Module | What Tagging Guarantees | Enforced How? | Risk if Broken |
|---|---|---|---|
| Billing | `tag_status = 0` (available) at time of billing | Billing reads live status — no lock mechanism | ⚠️ CONTRACT GAP: No exclusive lock; concurrent sale attempt possible |
| Billing | `ret_taging_stone` records complete and accurate | FK constraint + application logic | Bill shows wrong stone details if tag stones deleted separately |
| Billing | `net_wt`, `gross_wt`, `purity` stable after tagging | No write-lock after billing | Updating tag after billing changes the billed specs |
| Branch Transfer | `ret_taging.current_branch` is accurate | Updated during tag create (cross-branch) and BT completion | ⚠️ CONTRACT GAP: No atomic update — tag create + BT create in same trans, but BT model failure not fully guarded |
| Reports | `ret_taging.tag_status` accurately reflects physical state | Application logic only | Reports show stale/wrong status if any module fails mid-operation |
| Reports | `tag_purchase_cost` calculated correctly | `update_purchase_cost()` — manual trigger, not auto | Reports show `0` purchase cost until PO-linked update is run |
| Customer Order | `id_orderdetails` set accurately on link/unlink | `update_order_link()` / `unlink_order_tags()` | Order fulfillment shows wrong linked tag |
| Section Transfer | `ret_section_tag_status_log` entries present for each status change | Created during tagging save if section is set | Missing log = section transfer has no movement history |
| Metal Process | Tag is retrievable by tag_code after creation | Tag code generated and stored atomically | Metal process can't find tag if tag_code generation fails |

---

## 3b-4. Reversal Contracts (Cancel/Delete/Reverse)

For each destructive operation, what must be restored vs. what is actually restored:

### Tag Delete (`tagging('delete')` case L2079–2191 + OTP gate via `send_tag_otp()`)

> **R9 Note**: The OTP flow (`send_tag_otp()` → `verify_otp()`) only validates the OTP and marks it verified. The **actual deletion** happens when the user submits the tagged form with the `delete` switch-case (L2079). `verify_otp()` does NOT perform the tag deletion itself.

| Tables Written During CREATE | Restored on DELETE? | Method & Line | Gap? |
|---|---|---|---|
| `ret_taging` (INSERT new tag) | ✅ YES — `tag_status = 2` (soft delete) | `tagging('delete')` L2083–2093 | Soft delete only; record stays in DB |
| `ret_taging_stone` (child stones) | ❌ NO — confirmed R9: no DELETE/UPDATE in delete case L2079–2191 | — | ⚠️ Orphaned stone records for deleted tags |
| `ret_taging_material` (child materials) | ❌ NO — confirmed R9: no DELETE/UPDATE in delete case | — | ⚠️ Orphaned material records |
| `ret_taging_images` (tag images) | ❌ NO — confirmed R9: no DELETE/UPDATE in delete case | — | ⚠️ File storage accumulates for deleted tags |
| `ret_lot_inward_detail` (balance deducted) | ❌ NOT in delete case — lot restore uses separate OTP confirmation flow | see `verify_otp()` L5538–5612 | ⚠️ Delete case and lot restore are decoupled |
| `ret_lot_inwards` (lot status may have closed) | ⚠️ PARTIAL — balance restored but lot_status not re-opened | `verify_otp()` L5580 | If lot was auto-closed after last tag, it won’t reopen |
| `ret_section_tag_status_log` (if section set on create) | ✅ YES — new log entry created with status=2 | L2121–2145 | New entry added; original entry not cancelled |
| `ret_taging_status_log` | ✅ YES — new log entry with status=2 | L2099–2119 | — |
| `ret_branch_transfer` / `ret_branch_transfer_items` (if cross-branch) | ❌ NO — confirmed R9: no cleanup in delete case | — | ⚠️ Branch transfer shows pending tag that no longer exists |

### Tag Update (via `update_tagging_data()` L3667)

| Tables Written During UPDATE | Reversal on Error? | Gap? |
|---|---|---|
| `ret_taging` (UPDATE fields) | ⚠️ PARTIAL — no explicit `trans_begin` found in this method by R9 grep (grepping returned no results — needs secondary scan) | Mid-save crash = partial update |
| `ret_taging_stone` (DELETE old + INSERT new) | ⚠️ PARTIAL — DELETE runs then INSERT; if INSERT fails, old stones lost | Data loss risk |
| `ret_taging_material` (DELETE old + INSERT new) | ⚠️ PARTIAL — same pattern as stones | Data loss risk |
| `ret_lot_inward_detail` (restore old balance + deduct new) | ✅ YES — both old restoration and new deduction in same method | — |

### Re-tagging (via `create_retag()` L6755)

| Operation | Restoration on Cancel? | Gap? |
|---|---|---|
| Old tags set to `tag_status = 3` (within `generateRetaglot/generateRetagNontaglot`) | ❌ NO cancellation flow found | ⚠️ If retag fails midway, old tags stuck at status 3 (Other Issue) |
| New lot created (`ret_lot_inwards`) | ❌ NO cleanup on partial failure | ⚠️ Ghost lot in system |
| `ret_acc_stock_process` record created L6941 | ❌ NO rollback if sub-method fails | ⚠️ Orphaned process record |
| Transaction boundary at L6939 wraps `ret_acc_stock_process` INSERT but sub-methods (`generateRetaglot`) have their own separate DB calls | ⚠️ PARTIAL — outer `trans_begin` exists but sub-method failures may not propagate rollback correctly | ⚠️ Nested transaction risk |
| Non-tag stock updated in `generateRetagNontaglot()` | YES — within nested operation | — |

### Order Link/Unlink

| Operation | Reversal | Gap? |
|---|---|---|
| `update_order_link()` — sets `id_orderdetails` on tag | `unlink_order_tags()` — nullifies it | ✅ Clean bi-directional |
| `update_order_link()` — sets `tag_linked = 1` on order detail | `unlink_order_tags()` — sets `tag_linked = 0` | ✅ Clean |
| Unlink is OTP-gated | OTP via `send_order_unlink_otp()` | ✅ Secure |

---

## 3b-5. Flow Risk Checklist (QA-Ready Test Scenarios)

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-TAG-001 | CREATE tag from a lot with exact-zero remaining balance | REJECT with "insufficient lot balance" error | 🔴 HIGH | ❌ |
| FR-TAG-002 | CREATE tag from a closed/invoiced lot (lot_status ≠ 0) | REJECT with "lot is closed" error | 🔴 HIGH | ❌ |
| FR-TAG-003 | DELETE tag (OTP) — verify lot balance restored to exact original | Balance matches pre-tag state | 🔴 HIGH | ❌ |
| FR-TAG-004 | DELETE tag — verify BT records (if cross-branch) are cleaned up | No orphaned BT record | 🔴 HIGH | ❌ |
| FR-TAG-005 | DELETE tag — verify child stone/material/image records are soft-deleted or cleaned | No orphaned child records | 🟡 MED | ❌ |
| FR-TAG-006 | EDIT a tag that has already been sold (tag_status = 1) | REJECT with "tag is sold" error | 🔴 HIGH | ❌ |
| FR-TAG-007 | EDIT a tag — crash/timeout midway through stone DELETE + INSERT | No orphan stones; old data preserved | 🔴 HIGH | ❌ |
| FR-TAG-008 | CREATE cross-branch tag — BT model write fails after tag INSERT | No orphaned tag at source; both rollback | 🔴 HIGH | ❌ |
| FR-TAG-009 | LINK tag with status ≠ 0 (sold, deleted, in-transit) to a customer order | REJECT with clear error | 🔴 HIGH | ❌ |
| FR-TAG-010 | LINK same tag to two orders simultaneously (2 users) | One succeeds, second is rejected | 🔴 HIGH | ❌ |
| FR-TAG-011 | RETAG — source tag has billing record (status = 1, sold) | REJECT — cannot retag a sold tag | 🔴 HIGH | ❌ |
| FR-TAG-012 | RETAG — complete process, then verify old tag status = 3, new lot accessible | Old tags at status 3; new lot in system | 🟡 MED | ❌ |
| FR-TAG-013 | RETAG — fails midway (e.g., new lot insert fails) — verify old tags restored to status 0 | Old tag status rolled back | 🔴 HIGH | ❌ |
| FR-TAG-014 | BULK EDIT tags — mix of available (status=0) and sold (status=1) tags in selection | Sold tags rejected; available tags updated | 🟡 MED | ❌ |
| FR-TAG-015 | UPDATE purchase cost for a tag NOT linked to a PO | Graceful error or skip | 🟡 MED | ❌ |
| FR-TAG-016 | Dead AJAX calls (RISK-013): `get/active_color`, `get/active_masters` etc. | 404 response — verify JS handles gracefully | 🔴 HIGH | ❌ |
| FR-TAG-017 | SCAN a tag by QR code — verify all stone/material/charge data returned matches DB | Values match exactly | 🟡 MED | ❌ |
| FR-TAG-018 | CANCEL an advance-booked tag (status=11) — verify order booking also cleared | Both tag and order records updated | 🔴 HIGH | ❌ |
| FR-TAG-019 | CREATE tag — verify `tag_code` is unique across branches | No duplicate codes | 🔴 HIGH | ❌ |
| FR-TAG-020 | CREATE tag with HUID — validate HUID uniqueness check fires (via `validate_huid()`) | Duplicate HUID rejected before save | 🟡 MED | ❌ |

---

## Summary: Top Gaps Requiring Immediate Attention

| Gap | Risk | Relevant Risk ID |
|---|---|---|
| No guard on `lot_status` before tagging — tags created from closed lots | 🔴 HIGH | RISK-004 (partial) |
| `ret_taging_stone` / `_material` / `_images` not cleaned on tag delete | 🟡 MED | — |
| Branch transfer records not cleaned when tag is deleted via OTP | 🔴 HIGH | — |
| Re-tagging has no cancellation/rollback — old tags stuck at status 3 on failure | 🔴 HIGH | — |
| No exclusive lock when billing concurrently reads a tag being sold | 🔴 HIGH | RISK-004 |
| `update_tagging_data()` stone DELETE→INSERT not atomic enough (partial data loss risk) | 🔴 HIGH | RISK-006 |
| 8 dead AJAX calls silently 404 on page load (RISK-013) | 🔴 HIGH | RISK-013 |
| `tag_purchase_cost` not auto-updated — manual trigger only | 🟡 MED | — |
