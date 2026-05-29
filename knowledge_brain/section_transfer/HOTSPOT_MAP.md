# HOTSPOT MAP — Section Transfer
> Built: 2026-03-14 | Round 3 | Updated: R16 (2026-03-24) | 27 bugs across 5 files

---

## File Risk Heatmap

| File | Lines | Bug Count | Severity Score | Risk Level |
|---|---|---|---|---|
| `admin_ret_section_transfer.php` | 673 | 15 | 5×🔴 + 5×🟠 + 3×🟡 + 2×🟢 | 🔴 EXTREME |
| `ret_section_transfer_model.php` | 413 | 6 | 4×🔴 + 1×🟠 + 1×🟢 | 🔴 HIGH |
| `ret_section_transfer.js` | 1637 | 4 | 0×🔴 + 1×🟠 + 2×🟡 + 1×🟢 | 🟡 MEDIUM |
| `ret_brntransfer_model.php` | — | 1 | 1×🟠 | 🟠 HIGH (shared) |
| `admin_ret_billing.php` | — | 3 | 1×🔴 + 1×🟠 + 1×🟢 | 🔴 HIGH (cross-module) |
| `admin_settings_model.php` | 2866 | 1 | 1×🟠 | 🟡 LOW (shared) |

> **Note**: `admin_ret_billing.php` and `ret_brntransfer_model.php` bugs are cross-module findings documented in ST brain but must be fixed in their respective modules.

---

## Controller Hotspot Map (`admin_ret_section_transfer.php`)

```
Lines       │ Risk    │ What's There
────────────┼─────────┼──────────────────────────────────────────────────
L107–125    │ 🔴 CRIT │ save: raw POST, no CSRF/form_secret check (BUG-ST-020)
L127–135    │ 🟢 LOW  │ save: foreach trans_data — type branch (1 or 2)
L133–175    │ 🔴 HIGH │ save type=1: get_tag_details, updateData, insertData (tag silently skipped if status≠0)
L181–243    │ 🔴 CRIT │ save type=1 home-counter: DECREMENT-ONLY bug (BUG-ST-004) + dead duplicate if-block
L251–311    │ 🟡 MED  │ save type=1 home-counter cont: insertData logs, from_section=NULL (BUG-ST-011), updatestatus status=14 (BUG-ST-005)
L317–395    │ 🔴 HIGH │ save type=2 NT: updateNTData('-') SQLi (BUG-ST-003), to_section=NULL in log (BUG-ST-010)
L399–517    │ 🔴 HIGH │ save type=2 NT: checkNonTagItemExist SQLi (BUG-ST-002), wrong guard BUG-ST-017, updateNTData('+'), log insert
L521–549    │ 🟡 MED  │ save: trans_status check, commit/rollback, JSON response
L558–621    │ 🔴 CRIT │ send_counterchange_otp: no comma delimiter (BUG-ST-007), OTP in response (BUG-ST-006), SMS commented out (BUG-ST-021), trans_begin inside loop (BUG-ST-012), trailing space mobile (BUG-ST-022)
L623–664    │ 🟠 HIGH │ verify_counter_change_otp: session OTP not cleared after verify (BUG-ST-015)
```

---

## Model Hotspot Map (`ret_section_transfer_model.php`)

```
Lines       │ Risk    │ What's There
────────────┼─────────┼──────────────────────────────────────────────────
L67–75      │ 🟡 MED  │ getBranchDayClosingData: raw id_branch SQLi (BUG-ST-009)
L79–239     │ 🔴 CRIT │ getSectionTags: 6 unparameterized inputs, dual query paths, onlyBranchSelected guard bypass (BUG-ST-001 + BUG-ST-027)
L247–273    │ 🔴 CRIT │ checkNonTagItemExist: 5 unparameterized inputs (BUG-ST-002)
L277–287    │ 🔴 CRIT │ updateNTData: arithmetic SQL injection (BUG-ST-003)
L321–353    │ 🟢 LOW  │ checkSectionItemExist: unparameterized internal PKs (BUG-ST-019)
L361–377    │ 🔴 CRIT │ updatesecNTData: arithmetic SQL injection (BUG-ST-003 companion)
L381–393    │ 🟠 HIGH │ updatestatus: misleading signature, hardcoded status=14 (BUG-ST-005)
```

---

## JS Hotspot Map (`ret_section_transfer.js`)

```
Lines       │ Risk    │ What's There
────────────┼─────────┼──────────────────────────────────────────────────
L33         │ 🟡 MED  │ Global SectionTagData[] — never reset before re-collect (BUG-ST-014)
L408–447    │ 🟡 MED  │ #section_tag_search click: product required even for est_no (BUG-ST-013)
L837–893    │ 🟢 LOW  │ calculateSectiontotal: td:eq(6/7/8) hard-coded column indexes (BUG-ST-016)
L900–1009   │ 🟡 MED  │ #section_transfer click: SectionTagData accumulated without reset (BUG-ST-014)
L1395–1405  │ 🟢 LOW  │ calculateNTtotal: leading space in .nt_gross_wt selector (BUG-ST-023)
L1419–1475  │ 🟠 HIGH │ send_counter_change_otp_yes: local SectionTagData shadows global (BUG-ST-014 root)
L1495–1565  │ 🟠 HIGH │ counterchange_otp(): reads OTP from JSON response (BUG-ST-006)
L1571–1625  │ 🟠 HIGH │ verify_counter_change_otp: uses global SectionTagData (accumulation risk BUG-ST-014)
```

---

## Cross-Module Hotspot Map

```
File                      │ Lines      │ Risk    │ Bug
──────────────────────────┼────────────┼─────────┼────────────────────────────────────
ret_brntransfer_model.php │ L326–328   │ 🟠 HIGH │ fetchNonTaggedItems: from_brn, prodId, id_section unparameterized (BUG-ST-018)
admin_ret_billing.php     │ L3638      │ 🔴 CRIT │ updatesecNTData('+') on sale — never reversed on cancel/delete (BUG-ST-024)
admin_ret_billing.php     │ L7560/7740 │ 🔴 CRIT │ cancel/delete paths skip ret_home_section_item reversal (BUG-ST-024 all 4 paths)
admin_ret_billing.php     │ L10005     │ 🟢 LOW  │ echo last_query(); exit; — SQL exposed on DB failure (BUG-ST-026)
```

---

## Top 5 Highest-Risk Code Blocks (R16 Updated)

### #1 — `getSectionTags()` (Model L79–239)
**Why**: Entry point for ALL section transfer operations. 6 unparameterized inputs — any field injectable (especially string fields `old_tag_id`, `tag_code`). ALSO: `onlyBranchSelected` guard bypass allows order-reserved tags into barcode results (BUG-ST-027). Two separate critical bugs in one function.

### #2 — `save` case home-counter (Controller L107–243)
**Why**: No CSRF check (BUG-ST-020) means any authenticated page can replay a transfer. Inside: decrement-only stock bug (BUG-ST-004) corrupts silently on every home-counter transfer. Combined: unauthenticated stock manipulation with no error thrown.

### #3 — `updateNTData`/`updatesecNTData` (Model L277–287, L361–377)
**Why**: Arithmetic SQL injection directly on stock tables. A crafted `no_of_piece` value can DROP TABLE or inject rows. Both tablesaffected: `ret_nontag_item` and `ret_home_section_item`.

### #4 — OTP system (Controller L558–664)
**Why**: Five bugs interact: OTP exposed in response (BUG-ST-006), multi-mobile fails (BUG-ST-007), SMS never sent (BUG-ST-021), transaction inside loop (BUG-ST-012), session not cleared (BUG-ST-015). Net effect: counter-change transfers work **only** by reading OTP from DevTools, and only for single-mobile branches. The entire security control is non-functional.

### #5 — `ret_home_section_item` lifecycle (Cross-module)
**Why**: Three sources of corruption compound: ST decrements on arrival (BUG-ST-004), billing never reverses on cancel/delete (BUG-ST-024 all 4 paths), making the table drift unboundedly without any error signal. The table is a write-only counter that cannot be trusted for stock decisions.

---

## Cross-File Dependency Risks (R16 Updated)

| If this breaks... | These are also affected |
|---|---|
| `ret_home_section_item` stock negative | Billing module home-counter sales logic; section-wise inventory reports |
| `ret_nontag_item` stock corrupt | Branch Transfer reads this for transfers; any stock report |
| `ret_taging.id_section` wrong | All section-wise inventory reports; billing home-counter identification |
| OTP system broken | Counter-change authorization entirely non-functional (currently: all paths broken) |
| `getSectionTags` SQLi exploited | `ret_taging`, `ret_section`, `branch`, `customerorderdetails`, all estimation tables |
| `fetchNonTaggedItems` SQLi exploited (BT model) | Both ST and BT NT transfers compromised |

---

## Recommended Fix Order (R16 Updated — All 27 Bugs)

```
IMMEDIATE (P0 — deploy without waiting):
  1. BUG-ST-020 (CSRF missing)                  ← trivial, biggest attack surface
  2. BUG-ST-001 (getSectionTags SQLi)            ← 6 inputs, largest DB exposure
  3. BUG-ST-003 (updateNTData/sec SQLi)          ← stock tables at direct risk
  4. BUG-ST-002 (checkNonTagItemExist SQLi)      ← NT path

BLOCK ON TEAM DECISION (before deploying):
  5. BUG-ST-004 (home counter direction)         ← validate ret_home_section_item semantics first
  6. BUG-ST-024 (billing reversal missing)       ← billing module PR, coordinate team

NEXT SPRINT (P1 — this week):
  7. BUG-ST-021 (OTP never sent)                ← restore SMS block
  8. BUG-ST-007 (OTP delimiter)                 ← comma fix, 5 min
  9. BUG-ST-006 (OTP in response)               ← remove key, 1 min
 10. BUG-ST-017 (NT destination skip)           ← single guard change
 11. BUG-ST-008 (NT no qty cap)                 ← add server-side validation
 12. BUG-ST-009 (branch ID SQLi)                ← cast to int

FOLLOWING SPRINT (P2 — quality):
 13. BUG-ST-012 (OTP trans_begin in loop)       ← restructure send function
 14. BUG-ST-010 + BUG-ST-011 (NULL log fields)  ← trivial, audit cleanup
 15. BUG-ST-005 (updatestatus signature)        ← rename for clarity
 16. BUG-ST-015 (session not cleared)           ← add unset_userdata
 17. BUG-ST-019 (checkSectionItemExist raw SQL) ← switch to ActiveRecord

LOW PRIORITY (P3 — JS/UI):
 18. BUG-ST-013 (product validation est_no)     ← JS condition fix
 19. BUG-ST-014 (SectionTagData reset)          ← array reset at top of handler
 20. BUG-ST-016 (column index hardcodes)        ← JS selector fix
 21. BUG-ST-022 (trim mobile number)            ← 1 line fix
 22. BUG-ST-023 (NT selector space typo)        ← 1 char fix

BACKLOG / TEAM DECISION:
 23. BUG-ST-018 (BT model fetchNonTaggedItems)  ← BT team PR
 24. BUG-ST-025 (no undo/reverse feature)       ← Product design decision
 25. BUG-ST-026 (billing debug exit)            ← Billing PR, trivial
 26. BUG-ST-027 (order bypass barcode search)   ← Team: guard vs warning badge
```
