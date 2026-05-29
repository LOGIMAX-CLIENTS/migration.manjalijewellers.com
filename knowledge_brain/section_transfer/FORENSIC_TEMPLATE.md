# FORENSIC TEMPLATE — Section Transfer
> Built: 2026-03-14 | Round 1

---

## Layer 1 — Symptom Collection

Use this checklist when a bug is reported for the Section Transfer module:

- [ ] **Tag not appearing in search** — tag_status not 0, wrong branch/section filter, order reservation
- [ ] **Transfer button disabled / no rows collected** — checkbox not checked, `SectionTagData` not populated
- [ ] **OTP modal not appearing** — `allow_order_item_cancel_otp` hidden field value wrong
- [ ] **OTP not being sent** — `otp_verif_mobileno` empty in branch record
- [ ] **OTP verify fails** — session expired (>300s), concatenation mismatch for multi-mobile
- [ ] **Tag transferred but section not updated** — `updateData` silent fail, transaction rolled back
- [ ] **Home section stock going negative** — `updatesecNTData('-')` called without source balance check
- [ ] **NT stock double-deducted** — `updateNTData('-')` called twice due to logic error
- [ ] **Log records created but tag not moved** — partial commit / `trans_begin` scope issue
- [ ] **Section dropdown empty after branch change** — `admin_ret_catalog` AJAX failure
- [ ] **NT items not loading** — `admin_ret_brntransfer` AJAX failure

---

## Layer 2 — Reproduce & Isolate

### Reproduction Checklist
1. What type? Tagged (type=1) or Non-Tagged (type=2)?
2. Is OTP feature enabled? (`profile.counter_change_otp == 1`?)
3. Is target section a home-bill-counter? (`ret_section.is_home_bill_counter = 1`?)
4. Single-branch or multi-branch user?
5. Was the transfer eventually committed? (`ret_section_tag_status_log` has an entry?)

### Isolation Questions
- Does it fail for ALL items or just specific tags?
- Does the error appear before or after OTP?
- Does NT transfer work when Tagged fails or vice versa?

---

## Layer 3 — Client-Side Trace

| Check | What To Inspect |
|---|---|
| Network POST payload | `trans_data`, `section_item_type`, `trans_to_section`, `id_branch` |
| Network response | `{status: true/false, message: "..."}` |
| Console | `SectionTagData` array before `add_to_trans()` call |
| `allow_order_item_cancel_otp` | View source or Elements panel — should be 0 or 1 |
| Checkbox state | `$('input.tag_id:checked').length` — must be > 0 |
| `#select_to_section` value | Must not be empty/null |

**Key JS console points**:
```javascript
// Before transfer, in browser console:
console.log(SectionTagData);   // Should have items
console.log($('#allow_order_item_cancel_otp').val());  // 0 or 1
console.log($('#select_to_section').val());  // destination section
```

---

## Layer 4 — Server-Side Trace

### Controller Trace Points

| Symptom | File | Method | Line | What To Check |
|---|---|---|---|---|
| Tag not transferred | controller | `save` | L139 | `$tag_details['tag_status']==0` — must be true |
| Wrong datetime on logs | controller | `save` | L123 | `$dCData['entry_date']` vs `date("Y-m-d")` |
| Home stock not updated | controller | `save` | L181 | `$section['is_home_bill_counter'] == 1` |
| Home stock decremented only | controller | `save` | L239 | Only `updatesecNTData('-')` called — no increment path! |
| Transaction rollback | controller | `save` | L521 | `$this->db->trans_status()` — check DB errors |
| OTP not verified | controller | `verify_counter_change_otp` | L631–638 | Session OTP vs POST otp, expiry check |

**Enable CI query logging** in `application/config/database.php`:
```php
$db['default']['save_queries'] = TRUE;
// Then: print_r($this->db->queries);
```

---

## Layer 5 — Database Verification

```sql
-- 1. Check tag current state
SELECT tag_id, tag_code, id_section, current_branch, tag_status, product_id
FROM ret_taging WHERE tag_id = {TAG_ID};

-- 2. Check section transfer logs for a tag
SELECT * FROM ret_section_tag_status_log 
WHERE tag_id = {TAG_ID} ORDER BY id DESC LIMIT 10;

-- 3. Check home section stock for branch/product
SELECT h.*, s.section_name, p.product_name 
FROM ret_home_section_item h
JOIN ret_section s ON s.id_section = h.id_section
JOIN ret_product_master p ON p.pro_id = h.id_product
WHERE h.id_branch = {BRANCH_ID};

-- 4. NT stock balance check
SELECT ni.*, s.section_name 
FROM ret_nontag_item ni
JOIN ret_section s ON s.id_section = ni.id_section
WHERE ni.branch = {BRANCH_ID} AND ni.no_of_piece < 0;  -- Negative = data corruption

-- 5. OTP records for session
SELECT * FROM otp WHERE module = 'Counter Change OTP' 
ORDER BY otp_gen_time DESC LIMIT 5;

-- 6. Check if day closing is open or closed
SELECT id_branch, is_day_closed, entry_date FROM ret_day_closing 
WHERE id_branch = {BRANCH_ID};

-- 7. Orphan NT log records (log with no matching item)
SELECT sl.* FROM ret_section_nontag_item_log sl
LEFT JOIN ret_nontag_item ni ON ni.product = sl.product AND ni.branch = sl.from_branch
WHERE ni.id_nontag_item IS NULL LIMIT 20;
```

---

## Layer 6 — Root Cause Classification

| Category | Risk | Examples |
|---|---|---|
| SQL Injection | 🔴 CRITICAL | `getSectionTags`, `checkNonTagItemExist`, `updateNTData` — all use raw `$_POST` in SQL |
| Stock Integrity | 🔴 HIGH | Home section item only decremented; NT stock no server-side cap |
| Data Loss | 🟡 MED | `from_section` NULL in home log; `to_section` NULL in NT deduct log |
| Transaction Safety | 🟡 MED | `trans_begin` wraps full foreach — one bad item rolls back all |
| Security | 🟡 MED | OTP returned in API response; no CSRF tokens |
| Logic Error | 🟡 MED | `updatestatus()` signature mismatch — extra params ignored |
| Cross-Module Dependency | 🟢 LOW | NT search uses BT controller endpoint — coupling risk |

---

## Layer 7 — Stock Integrity (Specialized)

Use this when investigating NT or home-counter stock discrepancies:

```sql
-- Expected NT balance vs actual
SELECT 
    log.product, log.from_section, 
    SUM(CASE WHEN log.status=4 THEN -log.no_of_piece ELSE log.no_of_piece END) as log_balance,
    ni.no_of_piece as actual_balance
FROM ret_section_nontag_item_log log
LEFT JOIN ret_nontag_item ni ON ni.product=log.product AND ni.id_section=log.from_section
WHERE log.from_branch = {BRANCH_ID}
GROUP BY log.product, log.from_section;

-- Home counter: verify log vs actual
SELECT 
    h.id_product, h.no_of_piece as actual,
    COUNT(l.tag_id) as log_entries
FROM ret_home_section_item h
LEFT JOIN ret_home_section_item_log l ON l.id_product = h.id_product AND l.to_section = h.id_section
WHERE h.id_branch = {BRANCH_ID}
GROUP BY h.id_product;
```
