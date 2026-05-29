# FORENSIC TEMPLATE — Stock Issue
> Updated Round 8 | 2026-03-19 | Use this for every bug in this module

---

## Layer 1 — Symptom Collection

When a bug is reported, capture:

| Question | Answer |
|---|---|
| What stock_type? | Tagged (1) or Non-Tagged (2)? |
| Issue or Receipt? | issue_receipt_type = 1 or 2? |
| Which issue_type? | Repair / Marketing / Karigar / Customer? |
| What is `is_remove_from_stock` for that type? | Check `ret_stock_issue_types` |
| Was OTP involved? | otp_required = 1? |
| Which branch? | Head Office or sub-branch? |
| Day-close done? | Is today's day-close pending? |
| Tag status before issue | Run: `SELECT tag_status FROM ret_taging WHERE tag_id=?` |

**Common Symptoms Checklist**:
- [ ] Issue number skipped / duplicate
- [ ] Tag still shows in stock after issue
- [ ] Tag not showing in list
- [ ] Receipt not working (tag not reverting to stock)
- [ ] OTP not sent / not verified
- [ ] PDF blank or missing data
- [ ] Non-tag stock level wrong after issue
- [ ] Cannot submit (form secret mismatch)
- [ ] Employee / Karigar dropdown empty

---

## Layer 2 — Reproduce & Isolate

**Step-by-step reproduction**:
1. Identify: stock_type (tagged/non-tag), operation (issue/receipt)
2. Check which branch was selected
3. Determine issue_type and its `is_remove_from_stock` value
4. Check OTP requirement (`profile.stock_issue_otp_req`)
5. Try to reproduce with exact same parameters
6. If fails: check `form_secret` session validity

**Isolation questions**:
- Does the problem occur for ALL issue types or just one?
- Does it affect ALL branches or just one?
- Does it affect tagged AND non-tagged or only one?
- Was browser session fresh (not timed out)?

---

## Layer 3 — Client-Side Trace

**Browser Console**:
```javascript
// Check form state before submit
$('#stock_type').val()       // 1=tagged, 2=nontag
$('#issue_type').val()       // issue type ID
$('#is_otp_verfied').val()   // 0 or 1
$('#form_secret').val()      // must match session
stockIssueDetails            // scanned tag objects
receipt_details              // receipt tag objects
```

**Network Tab**:
- Monitor POST to `stock_issue/save` — check request payload
- Check response JSON: `{status: true/false, message: "...", id_stock_issue: N}`
- Monitor AJAX to `get_tag_scan_details` — check that tags return properly
- Monitor `stock_issue_sendotp` and `stock_issue_verify_otp` for OTP flows

**DOM Checks**:
- `$('#tagissue_item_detail > tbody > tr').length` — should be > 0 before submit
- `$('#nontagissue_item_detail > tbody > tr').length` — for nontag
- `$('#otp_required').val()` — OTP enabled?

> ⚠️ **XSS Investigation (R-22)**: If OTP modal displays unexpected HTML/script behavior, check:
> 1. Open Network tab → `stock_issue_verify_otp` response → inspect `msg` field value
> 2. Check `$('.otp_alert').html()` after OTP attempt — raw server text is injected here
> 3. Fix: server `msg` should only return plain text; JS should use `.text()` not `.append('<p>'+msg+'</p>')`

---

## Layer 4 — Server-Side Trace

| Symptom | File | Method | Line | What to Check |
|---|---|---|---|---|
| Issue not saved | controller | `stock_issue(save)` | L191-207 | session FORM_SECRET match? |
| Issue date wrong | controller | `stock_issue(save)` | L211-213 | `getBranchDayClosingData()` return |
| Issue no duplicate | model | `generateIssueNo()` | L102-152 | concurrent MAX race |
| Tag not updating | controller | `stock_issue(save)` | L309 | trans_begin/commit path |
| Receipt date missing | controller | `stock_issue(save)` | L463 | `$issue_date` undefined in receipt branch |
| Non-tag stock wrong | model | `updateNTData()` | L1344 | Raw SQL arithmetic check |
| OTP exposed | controller | `stock_issue_sendotp()` | L1312 | `'OTP' => $OTP` in response |
| PDF blank | controller | `stock_issue(issue_print)` | L1080 | `get_issue_item_details()` return |
| Debug echo blocking | controller | Various | L415, L547, L842, L1046 | Remove debug `echo last_query();exit` |
| Employee dropdown empty | JS | `get_all_employee()` | L557 | AJAX to estimation ctrl — is it up? |

---

## Layer 5 — Database Verification

```sql
-- 1. Check issue header exists and its status
SELECT id_stock_issue, issue_no, status, stock_type, issued_to, id_branch, issue_date
FROM ret_stock_issue WHERE id_stock_issue = {ID};

-- 2. Check all detail rows for that issue
SELECT id_stock_issue_detail, tag_id, id_non_tag_item, status, received_date, received_time
FROM ret_stock_issue_detail WHERE id_stock_issue = {ID};

-- 3. Check tag status (should be 7 if issued, 0 if received)
SELECT tag_id, tag_code, tag_status FROM ret_taging WHERE tag_id IN (SELECT tag_id FROM ret_stock_issue_detail WHERE id_stock_issue = {ID});

-- 4. Check status log for this tag
SELECT * FROM ret_taging_status_log WHERE tag_id = {TAG_ID} ORDER BY date DESC LIMIT 10;

-- 5. Check non-tag stock levels (for nontag issues)
SELECT id_nontag_item, no_of_piece, gross_wt, net_wt FROM ret_nontag_item WHERE id_nontag_item = {NONTAG_ID};

-- 6. Orphan detail check
SELECT sid.id_stock_issue_detail FROM ret_stock_issue_detail sid
LEFT JOIN ret_stock_issue si ON si.id_stock_issue = sid.id_stock_issue
WHERE si.id_stock_issue IS NULL;

-- 7. Tags stuck in issued status (7) without active issue
SELECT t.tag_id, t.tag_code FROM ret_taging t
LEFT JOIN ret_stock_issue_detail sid ON sid.tag_id = t.tag_id AND sid.status = 1
WHERE t.tag_status = 7 AND sid.tag_id IS NULL;

-- 8. Issue number duplicates check
SELECT issue_no, COUNT(*) as cnt FROM ret_stock_issue GROUP BY issue_no HAVING cnt > 1;

-- 9. OTP status check
SELECT * FROM otp WHERE module='Stock Issue' ORDER BY otp_gen_time DESC LIMIT 5;
```

---

## Layer 6 — Root Cause Classification

| Category | Risk | Examples |
|---|---|---|
| SQL Injection (Server) | 🔴 Critical | `get_tag_scan_details` L733, `get_nontag_scan_details` L1312, `get_profile_settings` L74 |
| XSS (Client-Side) | 🔴 Critical | `data.msg` injected raw into OTP modal DOM (JS L1751, L1801) — R-22 |
| Debug Code in Production | 🔴 Critical | `echo last_query(); exit;` at L415, L547, L842, L1046 — kills Tagged Issue/Receipt rollback |
| Security — OTP Leak | 🔴 Critical | OTP returned in `sendotp` JSON response (Controller L1312) |
| Hardcoded Business Value | 🔴 Critical | GST 3% in PDF template (issue_ack.php L569) — ignores DB rate |
| Race Condition | 🟠 Medium | `generateIssueNo()` — concurrent MAX query (Model L112) |
| Undefined Variable | 🟠 Medium | `$issue_date` used in receipt branch but defined in issue branch |
| Synchronous AJAX | 🟠 Medium | `async: false` on OTP send + verify — blocks browser UI (JS L1527, L1719) — R-23 |
| Unchecked All-Raw SQL | 🔴 Critical | `updateNTData` arithmetic — all 6 fields raw (Model L1346) — R-16 |
| N+1 Query | 🟡 Low | `get_stock_issue_det()` in list loop, `stock_issue_tags()` in issued items loop |
| Dead Dependency | 🟡 Low | `ret_billing_model` loaded in constructor but unused |
| Transaction Isolation | 🟥 Design | Receipt flow: `trans_begin()` without matching rollback on failure paths |
| Debug Console Logs | 🟡 Low | 3 `console.log()` in production JS (L225, L1225, L1331) — R-24 |

---

## Layer 7 — Stock Integrity Trace

Since this module moves physical stock, always verify:

```sql
-- Stock integrity: tag must not appear in two active issues simultaneously
SELECT tag_id, COUNT(*) as active_issues
FROM ret_stock_issue_detail
WHERE status = 1
GROUP BY tag_id
HAVING active_issues > 1;

-- Non-tag stock must not go negative
SELECT id_nontag_item, no_of_piece, gross_wt, net_wt
FROM ret_nontag_item
WHERE no_of_piece < 0 OR gross_wt < 0 OR net_wt < 0;

-- Verify issue/receipt balance: all status=1 detail rows should have tag_status=7
SELECT sid.tag_id
FROM ret_stock_issue_detail sid
LEFT JOIN ret_taging t ON t.tag_id = sid.tag_id
WHERE sid.status = 1 AND t.tag_status != 7;
```

---

## Layer 8 — OTP Flow Trace

If OTP-related bug:
1. Check `profile.stock_issue_otp_req` = 1 for user's profile
2. Check `$('#is_otp_verfied').val()` in browser — should be `1` after verify
3. Check `otp` table: `SELECT * FROM otp WHERE module='Stock Issue' ORDER BY otp_gen_time DESC`
4. Check session: `$_SESSION['stock_issue_otp']` and `stock_issue_otp_exp`
5. ⚠️ Network tab: look for `OTP` key in `sendotp` response — this is a security leak
6. Check SMS gateway config: `config->item('sms_gateway')` → 1=MSG91, 2=Nettyfish
