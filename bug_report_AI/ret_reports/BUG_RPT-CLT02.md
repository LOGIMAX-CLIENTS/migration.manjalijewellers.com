## RPT-CLT02 — Card Collection Report Device Filter Not Working

| Field         | Value                                                                 |
| ------------- | --------------------------------------------------------------------- |
| Severity      | P1                                                                    |
| Track         | A (System)                                                            |
| Category      | Query — PAT-QRY-005                                                   |
| Sprint        | Current                                                               |
| Pattern Match | PAT-QRY-005 (POST filter param received but never applied in SQL)    |
| Module Brain  | —                                                                     |
| Reporter      | User                                                                  |
| Source        | Client (etail_development_src)                                        |
| Status        | ✅ Fixed — 2026-04-29                                                 |

---

### Steps to Reproduce

1. Navigate to `admin_ret_reports/card_collection_report/list`
2. Select any device from the Device dropdown (e.g., "VVE")
3. Click Search

### Expected Behavior

Only card collection records from the selected device shown.

### Actual Behavior

All records shown regardless of device selection — filter is silently ignored.

---

### Root Cause

**PHP Model layer** — `get_card_collection_report()` in `ret_reports_model.php`.

JS AJAX sends `'selectedcat': $('#device').val()` (the `id_device` numeric value) but the model function never reads or applies it. All 3 SQL sub-queries (billing, issue_receipt, chit) lacked the `AND id_pay_device = ?` condition.

**Secondary bugs found:**
1. `$result = []` not initialized before `foreach` — if billing query returns zero rows, `array_merge($result, ...)` would emit an undefined variable warning.
2. `if(empty($data['source_type']))` is unreliable — PHP's `empty()` treats `'0'` as empty, making the "All" (value=0) case behaviorally correct by accident but fragile.

---

### Evidence

- JS L30386: `'selectedcat': $('#device').val()` — device ID sent ✅
- Model L13590 (before fix): no `$selectedcat` extraction — ❌ never read
- Model L13604 (before fix): billing query has no `id_pay_device` filter ❌
- Model L13623 (before fix): issue_receipt query has no `id_pay_device` filter ❌
- Model L13641 (before fix): chit query has no `id_pay_device` filter ❌

---

### Fix Applied

**File:** `admin/application/models/ret_reports_model.php`

| Line | Change |
|------|--------|
| L13590 | Added `$selectedcat` extraction with `'0'` guard |
| L13603–13605 | Added `AND p.id_pay_device = '$selectedcat'` to billing query |
| L13607 | Initialized `$result = []` before foreach loop |
| L13623–13624 | Added `AND rp.id_pay_device = '$selectedcat'` to issue_receipt query |
| L13641–13645 | Added `AND pmd.id_pay_device = '$selectedcat'` to chit query |
| L13646–13653 | Replaced `empty()` with explicit `== '' || == '0' || == 0` check |

---

### Rollback

Remove `$selectedcat` extraction + 3 `id_pay_device` conditions + `$result = []` init; restore `if(empty($data['source_type']))`.
See `ROLLBACK_REGISTRY.md` entry for RPT-CLT02.

---

### Smoke Test

- [ ] Device = All (0) → all records shown
- [ ] Device = specific device → only that device's records
- [ ] Source = Purchase + Device filter → combined filter works
- [ ] Source = Chit + Device filter → chit-only device filter works
- [ ] Branch + Device filters applied together
