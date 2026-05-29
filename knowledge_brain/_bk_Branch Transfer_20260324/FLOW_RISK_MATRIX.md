# Flow Risk Matrix — Branch Transfer

> **Round 3 Build** — 2026-03-24
> Built from: DATA_FLOW.md (6 flows) + CROSS_MODULE_MAP.md + MODULE_BRAIN.md
> **Status**: New document — previously missing from brain

---

## 1. State Machine: `ret_branch_transfer.status`

| State | Value | Description | Set By (Method) | Can Transition To | Guard / Precondition |
|---|---|---|---|---|---|
| Pending | 1 | Transfer created, awaiting transit approval | `branch_transfer('save')` L79 | Transit(2), Cancelled(3) | CSRF check, piece count ≠ 0 |
| In Transit | 2 | Transit approved by to-branch | `branch_transfer('updateStatus')` L285 | Downloaded(4), Cancelled(3) | Day Close date order validated (RULE-BRT-003) |
| Cancelled | 3 | Transfer rejected / cancelled | `update_branch_transfer_cancel()` L1262 | — (terminal) | ⚠️ NO GUARD: Any status can be cancelled |
| Downloaded | 4 | Stock received at to-branch | `branch_transfer('updateStatus')` L285 | — (terminal) | Day Close date order validated |

### Tag-level State (`ret_taging.tag_status`)
| State | Value | Set By | Can Transition To |
|---|---|---|---|
| Available | 0 | BT Download (Type 1,3) | In-Transit(4) |
| In-Transit | 4 | BT Transit Approval (Type 1,3) | Available(0), Other-Issue(3) |
| Other Issue | 3 | BT Download when `isOtherIssue=1` | — |

> ⚠️ **NO GUARD**: Cancelled transfer at status=2 (Transit) leaves tags stuck at tag_status=4. No reversal logic exists in `update_branch_transfer_cancel()`. See Anti-Pattern #5.

---

## 2. Inbound Contracts (What This Module Expects from Upstream)

| Upstream Module | Data/State Expected | Precondition Check in Code? | Line | Risk if Violated |
|---|---|---|---|---|
| Tagging | `ret_taging.tag_status` = 0 (Available) | **NO** — no tag_status pre-check before adding to transfer | — | Transfer created with already-sold or in-transit tag → phantom transfer |
| Tagging | `ret_taging` row exists for tag_id | PARTIAL — `get_tag_details()` fetches but no explicit status guard | L1087 | Could process non-existent tag |
| Non-Tagging | `ret_nontag_item` exists in branch | YES — `checkNonTagItemExist()` L1047 | L1047 | Rejects unknown NT items during Download ✅ |
| Billing (Partly Sale) | `ret_bill_details` row has valid `bill_det_id` | NO — SQL lookup only; no status validation | L1602+ | Transfer references already-transferred bill detail |
| Billing (Sales Return) | `ret_billing` row marked as sales return | NO — SQL join only | L1673+ | Duplicate SR transfer possible |
| Settings | `is_otp_required_for_approval` configured | YES — loaded as hidden field; forced through `getSettigsByName()` | L1092 | OTP requirement bypassed silently if setting missing |
| Day Close | Both branches have valid DC date | YES — `getAllBranchDCData()` enforces chronological order | L285 (updateStatus) | Transfer blocked by stale branch Day Close |

> ⚠️ **CONTRACT GAP**: Tag availability is NOT validated before creating a transfer. A tag already sold (tag_status ≠ 0) can be added. The system relies on JS-side filtering only (fetchTagsByFilter returns status=0 tags), but a crafted POST bypass is possible.

---

## 3. Outbound Contracts (What This Module Guarantees to Downstream)

| Downstream Module | What This Module Guarantees | Enforced How? | Risk if Broken |
|---|---|---|---|
| Tagging | `tag_status`=4 during transit, 0/3 after download | Yes — updateStatus sets these atomically | If BT cancelled post-transit, tag stays at status=4 → tag invisible in billing |
| Non-Tagging | NT weight deducted from from_branch, added to to_branch | Yes — `updateNTData('-')` then `('+')` within same transaction | If one fails silently, stock balance skewed |
| Billing | `ret_bill_details.transferred_to_acc_stock`=1 after SR/PS transit | Yes — updateStatus sets this | If skipped, same SR could be transferred twice |
| Billing | `ret_bill_old_metal_sale_details.is_transferred`=1 after OM download | Yes — updateStatus sets this | Duplicate OM transfers possible |
| Purchase Log | `ret_purchase_items_log` has transit/inward entries | Yes — logged within same transaction | Missing log = incomplete audit trail |
| Order Module | `customerorderdetails.current_branch` updated | YES (updateStatus Type 5) | Order still shows old branch after transfer |
| Packaging | `ret_other_inventory_purchase_items_details.status`=4 (transit) → 0 (downloaded) | YES but FIFO/LIFO shortfall not validated | Shortfall silently returns fewer items than requested (no error) |

> ⚠️ **CONTRACT GAP**: If transfer is cancelled after transit approval, `tag_status` in `ret_taging` remains 4 (In-Transit) permanently. No downstream module is notified. This is the highest-priority integrity gap.

---

## 4. Reversal Contracts (Cancel / Delete / Reverse)

| Operation | Tables That Must Be Restored | Actually Restored in Code? | Method & Line | Gap? |
|---|---|---|---|---|
| Cancel transfer | `ret_branch_transfer.status` → 3 | ✅ YES | `update_branch_transfer_cancel()` L1262 | — |
| Cancel transfer | `ret_taging.tag_status` → 0 (if in transit) | ❌ NO | — | ⚠️ Anti-Pattern #5 — tags stuck at status=4 |
| Cancel transfer | `ret_taging.current_branch` → from_branch | ❌ NO | — | ⚠️ Current branch stale after cancel |
| Cancel transfer | `ret_nontag_item` weight deduction reversal | ❌ NO | — | ⚠️ NT stock imbalance after transit+cancel |
| Cancel transfer | `ret_bill_details.transferred_to_acc_stock` → 0 | ❌ NO | — | ⚠️ SR/PS counts permanently inflated |
| Cancel transfer | `ret_bill_old_metal_sale_details.is_transferred` → 0 | ❌ NO | — | ⚠️ OM count not restored |
| Cancel transfer | `ret_purchase_items_log` reversal entry | ❌ NO | — | ⚠️ No audit trail of cancellation impact |
| Cancel transfer | `ret_other_inventory_purchase_items_details.status` reversal | ❌ NO | — | ⚠️ Packaging items stuck at status=4 |
| Cancel transfer | Activity log | ✅ YES | `log_model` write in cancel | — |

> ⚠️ **CRITICAL REVERSAL GAP**: Cancel operation only sets master status=3. ALL stock effects from transit approval (tag_status, NT weights, bill flags, packaging status) are permanently left in their post-transit state. This is documented in Anti-Pattern #5 and RULE-BRT-012.

---

## 5. Flow Risk Checklist (QA-Ready Test Scenarios)

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-BRT-001 | Create transfer with tag that has tag_status ≠ 0 (sold/in-transit) | REJECT: system should validate tag availability | 🔴 HIGH | ❌ Not implemented — client-side filter only |
| FR-BRT-002 | Create transfer with bill_det_id already used in another active transfer (PS/SR) | REJECT or warn | 🔴 HIGH | ❌ No server-side duplicate check |
| FR-BRT-003 | Approve transit when from-branch DC date < to-branch DC date | BLOCKED by RULE-BRT-003 | 🟡 MED | ✅ Implemented |
| FR-BRT-004 | Cancel after transit approval — verify ALL stock tables restored | Full restoration | 🔴 HIGH | ❌ NONE are restored (Anti-Pattern #5) |
| FR-BRT-005 | Cancel after transit → try to re-create same transfer | Should work but tag may still be at status=4 | 🔴 HIGH | ❌ Will fail because tag stuck in transit |
| FR-BRT-006 | Concurrent save by 2 users with same trans_code sequence | One gets unique code, one fails | 🟡 MED | ❌ TOCTOU race in `trans_code_generator` (BRN-S02) |
| FR-BRT-007 | Save crashes midway (DB error after master insert, before child insert) | Master inserted, child missing → orphan | 🔴 HIGH | PARTIAL — trans_begin exists but DB error monitoring is incomplete |
| FR-BRT-008 | Download NT item that doesn't exist at to-branch yet | Auto-creates new NT item at to-branch | 🟢 LOW | ✅ `checkNonTagItemExist()` handles this |
| FR-BRT-009 | Packaging: request 10 items, only 6 available | Should error; currently returns 6 silently | 🟡 MED | ❌ No shortfall validation |
| FR-BRT-010 | Scan-based download: scan same tag twice | Second scan should be rejected | 🟡 MED | ✅ `get_scan_tag_status()` checks this |
| FR-BRT-011 | OTP verification with expired OTP | REJECT | 🟡 MED | ✅ Expiry checked in `verify_otp()` / `verify_other_issue_otp()` |
| FR-BRT-012 | Transfer where `to_branch` = head office | Different OTP flow applies | 🟡 MED | ✅ `isHeadOffice()` gates this |
| FR-BRT-013 | Crafted POST with raw $\_POST bypass (now fixed) | Should use CI3 XSS filtering | ✅ FIXED | BRN-104 — `$_POST` → `$this->input->post()` applied |
| FR-BRT-014 | Print when DomPDF paper orientation is wrong due to "portriat" typo | May silently use default orientation | 🟢 LOW | ❌ Anti-Pattern #6 — open |
| FR-BRT-015 | Approve SR/PS transit when bill already closed (is_eda filter gone) | Should reject stale bill details | 🔴 HIGH | ❌ No bill status validation before transit |

---

## 6. High-Risk Handoff Points Summary

| Handoff | From | To | Risk Level | Mitigation Exists? |
|---|---|---|---|---|
| Tag availability at save | Tagging module | Branch Transfer save | 🔴 HIGH | ❌ Client-side only |
| Transit tag_status lock | Branch Transfer transit | Tagging (tag_status=4) | 🔴 HIGH | No guard — concurrent billing possible |
| Cancel post-transit state | Cancel operation | ALL downstream | 🔴 CRITICAL | ❌ Zero reversal logic |
| NT weight balance | Transit/Download | Non-Tagging stock | 🟡 MED | ✅ Transactional |
| Bill transferred_to_acc_stock | Transit approval | Billing module | 🟡 MED | ✅ Set during transit |
| Packaging shortfall | Download | Packaging inventory | 🟡 MED | ❌ Silent shortfall |
| Scan auto-complete | Scan download | Transfer master status | 🟡 MED | ✅ BT detail comparison |
