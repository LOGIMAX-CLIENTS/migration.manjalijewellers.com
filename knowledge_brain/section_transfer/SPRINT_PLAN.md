# SPRINT PLAN — Section Transfer
> Built: 2026-03-14 | Round 4 | Source: BUG_CANDIDATES.md + FIX_GUIDE.md

---

## Sprint 1 — Critical Security & Data Integrity

**Duration**: 1–2 days | **Goal**: Stop silent stock corruption + plug SQLi holes

| Ticket | Bug | File | Effort | Priority |
|---|---|---|---|---|
| ST-T001 | SQL injection in `getSectionTags` — 6 inputs | `ret_section_transfer_model.php` L79–239 | 2h | P0 |
| ST-T002 | SQL injection in `checkNonTagItemExist` | `ret_section_transfer_model.php` L247–273 | 30m | P0 |
| ST-T003 | SQL injection in `updateNTData`/`updatesecNTData` | `ret_section_transfer_model.php` L277–287, L361–377 | 1h | P0 |
| ST-T004 | Home counter stock — decrement-only bug (stock goes negative) | `admin_ret_section_transfer.php` L181–311 | 3h | P0 |

### ST-T001 Acceptance Criteria
- [ ] All 6 inputs cast to `(int)` or wrapped in `$this->db->escape()`
- [ ] Both query paths (tag-only branch, full query) fixed
- [ ] Tested with `tag_code = "1' OR 1=1 --"` — should return empty results

### ST-T004 Acceptance Criteria
- [ ] Source home section stock is DECREMENTED when tag leaves home-counter section
- [ ] Destination home section stock is INCREMENTED when tag arrives at home-counter section
- [ ] Duplicate `if($isExists['id_hometag_item']!='')` block at L233 removed
- [ ] `ret_home_section_item.no_of_piece` remains ≥ 0 after any transfer

---

## Sprint 2 — OTP Security + NT Server-Side Cap

**Duration**: 1 day | **Goal**: Fix auth bypass + verify integrity

| Ticket | Bug | File | Effort | Priority |
|---|---|---|---|---|
| ST-T005 | OTP value returned in JSON response | Controller L610 | 5m | P1 |
| ST-T006 | Multi-mobile OTP concat — missing comma delimiter | Controller L578 | 5m | P1 |
| ST-T007 | Session OTP not cleared after verification | Controller L640–653 | 10m | P1 |
| ST-T008 | NT transfer no server-side qty cap | Controller L317–517 | 1h | P1 |
| ST-T009 | `getBranchDayClosingData` SQLi via branch ID | `admin_settings_model.php` L2410 | 15m | P1 |

### ST-T006 Notes
- **Reference fix**: Branch transfer's `send_other_issue_otp` (L1306) correctly uses `$sent_otp .= $OTP . ','` — this is the proven pattern used elsewhere, copy it here.
- The `verify` side already uses `explode(',', $session_otp)` correctly — only the send side needs fixing.

### ST-T007 Acceptance Criteria
- [ ] `session('counterchange_otp')` unset_userdata after successful verification
- [ ] `session('counterchange_otp_exp')` unset_userdata after successful verification
- [ ] Second transfer attempt without OTP is redirected to OTP flow

---

## Sprint 3 — Audit Trail + OTP Transaction Safety

**Duration**: ½ day | **Goal**: Fix log integrity + OTP DB commit safety

| Ticket | Bug | File | Effort | Priority |
|---|---|---|---|---|
| ST-T010 | NT deduct log — `to_section` NULL | Controller L379 | 5m | P2 |
| ST-T011 | Home item log — `from_section` NULL | Controller L269 | 5m | P2 |
| ST-T012 | OTP trans_begin inside foreach, commit outside | Controller L591–615 | 45m | P2 |
| ST-T013 | `updatestatus()` signature mismatch + status=14 confusion | Model L381 | 30m | P2 |

### ST-T013 Decision Required
> ⚠️ Should tag_status be set to **14** or **16** when transferred to home counter?
> - `ret_taging_status_log` records status=16 (L295)
> - `ret_taging.tag_status` is set to 14 via `updatestatus()` (L381)
> - Check if billing module reads tag_status=14 or 16 for home-counter identification

---

## Sprint 4 — JS/UI Fixes

**Duration**: 1–2 hours | **Goal**: Stable UI + correct totals

| Ticket | Bug | File | Effort | Priority |
|---|---|---|---|---|
| ST-T014 | Product required validation blocks estimation search | `ret_section_transfer.js` L230 | 10m | P3 |
| ST-T015 | `SectionTagData` global accumulates on repeat clicks | `ret_section_transfer.js` L474 | 5m | P3 |
| ST-T016 | `calculateSectiontotal()` hard-coded column indexes | `ret_section_transfer.js` L451–456 | 15m | P3 |

### Notes from Archived JS Analysis
The file `ret_section_transfer_14_07_2025.js` (847 lines, archived) shows the module's state before the OTP flow was added. BUG-ST-014 (`SectionTagData` accumulation) was introduced when the OTP feature was layered on top — the global was already there but wasn't reset in the new OTP branch at L744. This confirms the fix is safe: just add `SectionTagData = []` at the top of the `#section_transfer` click handler.

---

## Fix Execution Order Across Sprints

```
Sprint 1 (P0): ST-T001 → ST-T002 → ST-T003 → ST-T004
                (commit after each, test DB state)
               
Sprint 2 (P1): ST-T005 → ST-T006 → ST-T007 (all trivial: 1 commit)
               ST-T008 (server-side qty cap — separate commit)
               ST-T009 (settings model — separate commit, shared code)

Sprint 3 (P2): ST-T010 + ST-T011 (trivial, 1 commit)
               ST-T012 (OTP transaction restructure)
               ST-T013 (pending decision)

Sprint 4 (P3): ST-T014 + ST-T015 + ST-T016 (all JS, 1 commit)
```

---

## Risk Table for Sprint 1

| Fix | Regression Risk | Test Points |
|---|---|---|
| ST-T001 SQLi fix in getSectionTags | Low | Test all 6 search paths: by section, branch, product, tag_code, old_tag_id, est_no |
| ST-T002 SQLi fix in checkNonTagItemExist | Low | NT transfer for existing + new item |
| ST-T003 SQLi fix in updateNTData/sec | Low | NT quantities remain correct after transfer |
| ST-T004 Home counter decrement fix | **MEDIUM** | Requires verifying home section item tracking logic with billing team before deploying |

> [!CAUTION]
> Do NOT merge ST-T004 without validating with the billing module team — `ret_home_section_item` is read by billing for home-counter sales. The column may need to go UP or DOWN depending on whether it tracks "items at home counter" or "items NOT at home counter".

---

## Dependency Map

```
ST-T001 ─┐
ST-T002 ─┤── All independent, no cross-dependencies in Sprint 1
ST-T003 ─┤
ST-T004 ─┘

ST-T006 ──── required before ST-T007 can be validated (OTP won't work without delimiter fix)
ST-T012 ──── depends on ST-T006 context (restructuring same function)

ST-T009 ──── touches shared code (admin_settings_model) — test in BT module too after fix
```

---

## NT Data Contract Reference (Round 4 Verified)

**Endpoint**: `admin_ret_brntransfer/branch_transfer/getNonTaggedItem`
**Controller**: `admin_ret_brntransfer.php` L1076
**Model call**: `$model->fetchNonTaggedItems($_POST)`
**POST params used by ST JS**: `{lot_dt_rng, prodId, lotno, from_brn}`

**Response columns consumed by ST JS** (from `ret_section_transfer_14_07_2025.js` and current JS):
| Column | JS class | Purpose |
|---|---|---|
| `id_nontag_item` | `.id_nontag_item` | Primary key for deduct operation |
| `id_section` | `.id_section` | Source section for deduct log |
| `product` | `.product` | Product FK → model checkNonTagItemExist |
| `design` | `.design` | Design FK |
| `id_sub_design` | `.id_sub_design` | Sub-design FK |
| `no_of_piece` | `.blc_pieces` / `.nt_piece` | Balance shown + input field |
| `gross_wt` | `.blc_gross_wt` / `.nt_gross_wt` | Balance shown + input field |
| `net_wt` | `.blc_net_wgt` / `.nt_net_wgt` | Balance shown + input field |

---

## Sprint 5 — CSRF & OTP Delivery (Round 6 + 7 bugs)

**Duration**: 1 day | **Goal**: Fix CSRF exposure + make OTP functional

| Ticket | Bug | File | Effort | Priority |
|---|---|---|---|---|
| ST-T020 | CSRF gap in `save` — no form_secret check | Controller L107 | 30m | P1 |
| ST-T021 | OTP SMS block commented out — OTP never delivered | Controller L596 | 45m | P0 |
| ST-T022 | Multi-mobile split trailing space | Controller L568 | 5m | P3 |
| ST-T023 | `calculateNTtotal` selector space typo L1399 | JS L1399 | 2m | P3 |

### ST-T021 Notes — OTP Restore
The commented-out block referenced `admin_usersms_model` (which exists for billing). To restore:
1. Load the model: `$this->load->model('admin_usersms_model')`
2. Choose delivery channel — check `$service['serv_whatsapp']` flag (already fetched at L581)
3. Uncomment/rewrite the send block with `$mobile` (not hardcoded `9486528828`)

### ST-T020 Fix Pattern (copy from Branch Transfer)
```php
// Add at start of 'save' case:
if($this->session->userdata('FORM_SECRET') != $_POST['form_secret']){
    echo json_encode(['status'=>false,'message'=>'Invalid request']);
    return;
}
```
And add `form_secret` to `add_to_trans` postData in JS.

---

## Sprint 6 — Order Reservation Guard (Round 14 + 15 findings)

**Duration**: ½ day | **Goal**: Enforce RULE-ST-007 across all search modes

| Ticket | Bug | File | Effort | Priority |
|---|---|---|---|---|
| ST-T027 | `onlyBranchSelected` guard bypassed by tag_code/old_tag_id search — order-reserved tags appear in barcode results | `ret_section_transfer_model.php` L89–91 | 30m | P1 |

### ST-T027 Decision Required

> ⚠️ Two possible fix approaches — team must decide:
> 1. **Unconditional guard** (safest): Apply `AND (t.id_orderdetails IS NULL OR t.id_orderdetails = '')` to ALL `getSectionTags` queries regardless of search mode. Order-reserved tags never appear in any ST search.
> 2. **Warning badge** (UX-friendly): Allow order-reserved tags to appear in search results but mark them with a visual warning badge; let supervisor override the transfer with confirmation dialog.

### ST-T027 Acceptance Criteria (unconditional guard option)
- [ ] `onlyBranchSelected` logic removed or replaced with unconditional `id_orderdetails` filter
- [ ] Test: search by `tag_code` for an order-reserved tag → should NOT appear in results
- [ ] Test: search by `old_tag_id` for an order-reserved tag → should NOT appear in results
- [ ] Test: available (non-reserved) tags still appear normally in all search modes

### ST-T027 Acceptance Criteria (warning badge option)
- [ ] Order-reserved tag rows show a red "ORDER RESERVED" badge in `section_trans_list`
- [ ] Transfer button blocked for rows with order reservation unless supervisor override checkbox ticked
- [ ] Override action logged to `ret_section_tag_status_log` with a special note field

---

## getSectionTags SQLi Fix — Dual Branch Note (Round 7)

> ⚠️ **CRITICAL**: `getSectionTags` has TWO SQL branches. The fix (ST-T001) must be applied to BOTH:
> - **Branch 1** (L98–153): runs when `est_no` is empty — 5 injectable fields
> - **Branch 2** (L159–225): runs when `est_no` is set — adds JOIN to `ret_estimation_items` + `ret_estimation`, same 5 injectable fields PLUS `est_no` at L221

**Fields requiring cast to `(int)` in both branches**:
- `$data['id_section']` → `(int)$data['id_section']`
- `$data['id_branch']` → `(int)$data['id_branch']`
- `$data['id_product']` → `(int)$data['id_product']`
- `$data['est_no']` → `(int)$data['est_no']`

**Fields requiring `$this->db->escape()` in both branches**:
- `$data['old_tag_id']` → `$this->db->escape($data['old_tag_id'])`
- `$data['tag_code']` → `$this->db->escape($data['tag_code'])`

