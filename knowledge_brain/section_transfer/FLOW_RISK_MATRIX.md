# FLOW RISK MATRIX — Section Transfer
> Built: 2026-03-24 | Round: 15 | Module: Section Transfer

---

## 1. State Machine — `ret_taging.tag_status`

> ST = Section Transfer module. Billing = admin_ret_billing module.

| Status | Label | Set By | Meaning |
|---|---|---|---|
| 0 | Available | Billing cancel/delete, Initial | Tag is available for transfer or sale |
| 6 | Estimate Cancelled | Billing (estimate cancel) | Tag was on an estimate bill, estimate cancelled |
| 11 | Approval Stock | Billing | Approval/consignment stock |
| 14 | Home Counter | ST `updatestatus()` | Tag transferred to a home-bill-counter section |
| 16 | Home Counter (log only) | ST `insertData(ret_taging_status_log)` | Status logged to audit trail — **MISMATCH with 14** (BUG-ST-005) |

### State Transition Diagram

```
         [0 — Available]
              |
   +----------+----------+
   |                     |
   ▼                     ▼
[Billing SALE]      [ST Tagged Transfer]
   |                     |
   ▼                     ▼
[sold status]    [14 — Home Counter]  ← only if dest is_home_bill_counter=1
   |              Physical tag_status
   |
   ▼
[Billing CANCEL/DELETE]
   |
   ▼
[0 — Available again]   ← ret_home_section_item NOT reversed (BUG-ST-024)

[Any non-home-counter ST transfer]
   → tag_status stays 0 (no updatestatus call)
   → only id_section updated in ret_taging
```

### ⚠️ Status Guard in Transfer

Only tags with `tag_status == 0` can be transferred:
```php
// Controller L139:
if($tag_details['tag_status'] == 0) { ... }
```
Tags with status 6, 11, 14 are silently skipped — **no error returned to JS** (confirmed via code trace R8).

### ⚠️ Gap: BUG-ST-027 — Guard Bypassed by Tag Code / Old Tag ID Search

```
User searches by tag_code or old_tag_id
  → onlyBranchSelected = FALSE (model L89-91)
  → order reservation filter NOT applied
  → order-reserved tags appear in results
  → user can select and transfer them
  → customer order broken without any warning
```

---

## 2. Inbound Contract — What ST Accepts

### Tagged Transfer (section_item_type=1)

| POST Field | Source | Validation | Risk |
|---|---|---|---|
| `trans_to_section` | Dropdown | None — raw `$_POST` | BUG-ST-020 (no CSRF) |
| `id_branch` | Session / dropdown | None server-side | BUG-ST-009 (SQLi via `getBranchDayClosingData`) |
| `trans_data[].tag_id` | JS tag row | `tag_status==0` check only | Skipped silently if not 0 |
| `trans_data[].trans_from_section` | JS tag row | None | NULL logged if empty (BUG-ST-011) |
| `trans_data[].id_branch` | JS tag row | None | Trusted from client |
| `trans_data[].pcs` | JS tag row | None server-side | Trusted from client |
| `trans_data[].grs_wt` | JS tag row | None server-side | Trusted from client |
| `trans_data[].net_wt` | JS tag row | None server-side | Trusted from client |

### Non-Tagged Transfer (section_item_type=2)

| POST Field | Source | Validation | Risk |
|---|---|---|---|
| `trans_data[].product` | JS NT row | None server-side | BUG-ST-002 (SQLi in checkNonTagItemExist) |
| `trans_data[].design` | JS NT row | None server-side | BUG-ST-002 |
| `trans_data[].id_sub_design` | JS NT row | None server-side | BUG-ST-002 |
| `trans_data[].id_section` | JS NT row | None server-side | BUG-ST-002 |
| `trans_data[].branch` | JS NT row | None server-side | BUG-ST-003 (SQLi updateNTData) |
| `trans_data[].no_of_piece` | JS NT row | **None server-side** | BUG-ST-008 (no qty cap — 9999 possible) |
| `trans_data[].gross_wt` | JS NT row | None server-side | BUG-ST-003 |
| `trans_data[].net_wt` | JS NT row | None server-side | BUG-ST-003 |
| `trans_data[].id_nontag_item` | JS NT row | `!= ''` guard (L349/L431) | BUG-ST-017 (wrong guard scope) |

---

## 3. Outbound Contract — What ST Writes

### Tagged Transfer — Tables Written per Tag

| Step | Table | Operation | Columns | Notes |
|---|---|---|---|---|
| 1 | `ret_taging` | UPDATE `id_section` | `id_section = transfer_to_section` | Always (status=0 guard passes) |
| 2 | `ret_section_tag_status_log` | INSERT | `tag_id, from_branch=NULL, to_branch, from_section, to_section, created_by, created_on, date` | `from_branch` always NULL (BUG) |
| 3a | `ret_home_section_item` | UPDATE `updatesecNTData('-')` | `no_of_piece, gross_wt, net_wt` | ONLY if dest is_home_bill_counter=1 AND row exists. Direction WRONG — should be '+' (BUG-ST-004) |
| 3b | `ret_home_section_item` | INSERT | (whole row) | If row doesn't exist — **path not in current code** (gap: no INSERT for new home-counter product) |
| 4 | `ret_home_section_item_log` | INSERT | `id_product, no_of_piece, gross_wt, net_wt, tag_id, status=0, from_branch=NULL, to_branch, from_section=NULL, to_section, created_by, created_on, date` | `from_section` always NULL (BUG-ST-011) |
| 5 | `ret_taging` | UPDATE `tag_status=14` via `updatestatus()` | `tag_status = 14` | Only when is_home_bill_counter=1 (nested inside block L284-309) |
| 6 | `ret_taging_status_log` | INSERT | `tag_id, status=16, from_branch, to_branch=NULL, created_by, created_on, date` | Status 16 here vs 14 in ret_taging = BUG-ST-005 |

> [!IMPORTANT]
> Steps 3a–6 ONLY execute when destination section `is_home_bill_counter = 1`. For non-home-counter transfers, ONLY steps 1–2 execute.

### Non-Tagged Transfer — Tables Written per NT Item

| Step | Table | Operation | Columns | Notes |
|---|---|---|---|---|
| 1 | `ret_nontag_item` | UPDATE `updateNTData('-')` | `no_of_piece, gross_wt, net_wt` (SOURCE) | Only if `id_nontag_item != ''` |
| 2 | `ret_section_nontag_item_log` | INSERT (deduct) | `product, design, id_sub_design, no_of_piece, gross_wt, net_wt, status=4, from_branch, to_branch=NULL, from_section, to_section=NULL, ...` | `to_section=NULL` (BUG-ST-010) |
| 3a | `ret_nontag_item` | UPDATE `updateNTData('+')` | (DESTINATION) | If destination row EXISTS AND `val['id_nontag_item'] != ''` — wrong guard (BUG-ST-017) |
| 3b | `ret_nontag_item` | INSERT | (new destination row) | If destination row does NOT exist |
| 4 | `ret_section_nontag_item_log` | INSERT (add) | `product, design, id_sub_design, no_of_piece, gross_wt, net_wt, status=0, from_branch=NULL, to_branch, from_section, to_section, ...` | Logged correctly for destination side |

---

## 4. Reversal Contract — What ST Cannot Do

> ST has **no reverse, undo, or cancel transfer function** (BUG-ST-025).

### Missing Reversal Paths

| Data Written | Can It Be Reversed? | Method | Risk |
|---|---|---|---|
| `ret_taging.id_section` | ❌ No | Would need a second forward transfer | Once transferred, only another ST can move it back |
| `ret_section_tag_status_log` | ❌ No | Append-only audit log | Permanent history — but bad intermediate states persist |
| `ret_taging.tag_status = 14` | ❌ No | `updatestatus()` only ever sets 14; no reset to 0 | Tag stays "home counter" status even if moved back via ST |
| `ret_home_section_item` | ❌ No | No decrement path in ST | BUG-ST-004 + BUG-ST-024 compound |
| `ret_home_section_item_log` | ❌ No | Append-only | Permanent |
| `ret_nontag_item` (source deduct) | ❌ No | No rollback without manual DB edit | Stock disappears permanently if transfer was wrong |
| `otp.is_verified` | N/A | DB record stays verified | Low risk |

### Billing Reversal Interactions — All 4 Paths

| Billing Path | tag_status after | NT Stock | ret_home_section_item | Bug |
|---|---|---|---|---|
| Estimate cancel (`cancell`) | 6 | `updateNTData('+')` ✅ | **NOT reversed** ❌ | BUG-ST-024 |
| Bill delete | 0 | `updateNTData('+')` ✅ | **NOT reversed** ❌ | BUG-ST-024 |
| Receipt cancel | 0 | — (no NT) | **NOT reversed** ❌ | BUG-ST-024 |
| `update_branch` (approval) | 0 | — (no NT) | **NOT reversed** ❌ | BUG-ST-024 |

> [!CAUTION]
> `ret_home_section_item` is a **write-only counter in practice**: ST writes wrong direction, billing never reverses. This table cannot be trusted for stock decisions without a full rebuild + fix of both ST and billing reversal paths.

---

## 5. OTP Flow State Machine

```
Transfer button clicked
  → JS: counterchange_otp() called if allow_order_item_cancel_otp == 1
  → POST: send_counterchange_otp
      ├─ getBrnachOtpRegMobile($id_branch) → otp_verif_mobileno
      ├─ foreach mobile:
      │    ├─ unset_userdata("counterchange_otp")         [odd: cleared each iteration]
      │    ├─ $OTP = mt_rand(1001, 9999)
      │    ├─ $sent_otp .= $OTP                           [BUG-ST-007: no comma delimiter]
      │    ├─ set_userdata("counterchange_otp", $sent_otp)
      │    ├─ set_userdata("counterchange_otp_exp", time()+300)
      │    ├─ trans_begin()                               [BUG-ST-012: inside foreach]
      │    └─ insertData($insData, 'otp')
      │         └─ SMS BLOCK COMMENTED OUT                [BUG-ST-021: OTP never sent]
      └─ if($insId) → trans_commit()                     [only last $insId checked]
         → JSON: {status:true, OTP: $sent_otp}           [BUG-ST-006: OTP in response]

User receives OTP (only via DevTools in current state)
  → POST: verify_counter_change_otp
      ├─ $session_otp = session('counterchange_otp')
      ├─ explode(',', $session_otp)                       [BUG-ST-007: no comma → single element]
      ├─ foreach: if($OTP == $post_otp) →
      │    ├─ check expiry (time() >= otp_exp → EXPIRED)
      │    └─ else: updateData(is_verified=1, 'otp') → trans_commit
      │              Session OTP NOT cleared              [BUG-ST-015]
      └─ JSON: {status:true/false}

JS on verify success: add_to_trans(SectionTagData)        [BUG-ST-014: global not reset]
  → POST: save → full tagged/NT transfer flow
```

---

## 6. Risk Heatmap by Flow

| Flow | Risk Level | Highest Risk Bug | Reason |
|---|---|---|---|
| Tag search (getSectionTags) | 🔴 CRITICAL | BUG-ST-001 | 6 SQLi inputs; also BUG-ST-027 order bypass |
| Tagged save (home counter) | 🔴 CRITICAL | BUG-ST-004 | Stock direction wrong; compounds with billing |
| Tagged save (any section) | 🟠 HIGH | BUG-ST-020 | CSRF — replay any transfer without user action |
| NT search (fetchNonTaggedItems) | 🔴 CRITICAL | BUG-ST-018 | SQLi in shared BT model endpoint |
| NT save | 🟠 HIGH | BUG-ST-008 + BUG-ST-017 | No qty cap + silent skip of dest increment |
| OTP send | 🟠 HIGH | BUG-ST-006 + BUG-ST-021 | OTP in response + never delivered |
| OTP verify | 🟡 MEDIUM | BUG-ST-015 | Session not cleared → replay window |
| Billing reversal (cross-module) | 🔴 CRITICAL | BUG-ST-024 | ret_home_section_item never reversed |
| Audit logs | 🟡 MEDIUM | BUG-ST-010 + BUG-ST-011 | NULL columns break audit trail |

---

## 7. Fix Dependency Map

```
Fix Order (critical path):

Priority 0 (STOP BLEEDING — can deploy independently):
  ST-T001 → getSectionTags SQLi fix (both SQL branches)
  ST-T002 → checkNonTagItemExist SQLi fix
  ST-T003 → updateNTData/updatesecNTData SQLi fix
  ST-T020 → CSRF form_secret guard
  ST-T021 → OTP SMS restore

Priority 1 (BEFORE data integrity fixes):
  Team decision: ret_home_section_item = stock or throughput? [Blocks ST-T004]
  Team decision: tag_status 14 or 16? [Blocks ST-T013]

Priority 2 (DATA INTEGRITY — after team decisions):
  ST-T004 → Home counter direction fix [requires team sign-off]
  Billing fix → ret_home_section_item reversal on cancel/delete [BUG-ST-024, separate PR]

Priority 3 (QUALITY — low risk, fast wins):
  ST-T005, ST-T006, ST-T007, ST-T008 → OTP hardening
  ST-T010, ST-T011 → Audit log NULL fix
  ST-T014, ST-T015, ST-T016, ST-T023 → JS fixes

Priority 4 (ENHANCEMENT — new feature, not a bug):
  BUG-ST-025 → Undo/reverse transfer feature
  BUG-ST-027 → Extend onlyBranchSelected guard to barcode search
```

---

## 8. Invariant Violations Documented

| Invariant | Expected | Actual | Bug |
|---|---|---|---|
| Tags with customer orders cannot be transferred | Blocked in all search modes | Only blocked in branch/section-filter mode | BUG-ST-027 |
| Home counter stock increments on arrival | `updatesecNTData('+')` | `updatesecNTData('-')` — decrements | BUG-ST-004 |
| Billing reversal restores home counter | `updatesecNTData('-')` | Never called | BUG-ST-024 |
| OTP is delivered to approver's phone | SMS/WhatsApp sent | SMS block commented out | BUG-ST-021 |
| Session OTP cleared after use | `unset_userdata` called | Never cleared on success | BUG-ST-015 |
| OTP is not exposed in API response | Not in JSON | `'OTP' => $sent_otp` in JSON | BUG-ST-006 |
| NT destination stock updated on transfer | `updateNTData('+')` always runs | Guard `val['id_nontag_item'] != ''` can block it | BUG-ST-017 |
| Transfer log records source AND destination | Both from/to populated | `from_branch=NULL`, `from_section=NULL` in logs | BUG-ST-011 |
| CSRF protection on all state-changing endpoints | Token verified | No CSRF check in `save` | BUG-ST-020 |
