# QUICK REFERENCE — Stock Issue Module
> Version: Round 14 Final | 2026-03-19 | 26 bugs | 16 brain docs

---

## Module Identity

| Item | Value |
|---|---|
| Controller | `admin_ret_stock_issue` |
| Model | `ret_stock_issue_model` |
| JS | `admin/assets/js/ret_stock_issue.js` |
| Views | `ret_stock_issue/` (4 files) |
| Owned Tables | `ret_stock_issue`, `ret_stock_issue_detail` |
| Brain Path | `knowledge_brain/Stock_Issue/` |
| Bugs Found | **26** (9 Critical 🔴 | 8 Medium 🟠 | 9 Low 🟡) |

---

## Route Cheatsheet

| Action | URI | Method |
|---|---|---|
| List page | `/admin_ret_stock_issue/stock_issue/list` | GET |
| Add form | `/admin_ret_stock_issue/stock_issue/add` | GET |
| Save | `/admin_ret_stock_issue/stock_issue/save` | POST |
| Print summary | `/admin_ret_stock_issue/stock_issue/issue_print/{id}` | GET |
| Print detail | `/admin_ret_stock_issue/stock_issue/issue_print_detail/{id}` | GET |
| List AJAX | `/admin_ret_stock_issue/stock_issue` (bare, POST) | POST |
| Tag scan | `/admin_ret_stock_issue/get_tag_scan_details` | POST |
| Receipt scan | `/admin_ret_stock_issue/get_receipt_tag_scan_details` | POST |
| NT scan | `/admin_ret_stock_issue/get_nontag_scan_details` | POST |
| Send OTP | `/admin_ret_stock_issue/stock_issue_sendotp` | POST |
| Verify OTP | `/admin_ret_stock_issue/stock_issue_verify_otp` | POST |

---

## Status Values

### `ret_stock_issue.status`
| Value | Meaning |
|---|---|
| 0 | Approval Pending |
| 1 | Issued |
| 2 | Rejected |
| 3 | Received |

### `ret_stock_issue_detail.status`
| Value | Meaning |
|---|---|
| 1 | Issued (active) |
| 2 | Rejected |
| 3 | Received back |

### `ret_taging.tag_status`
| Value | Meaning in this module |
|---|---|
| 0 | In stock (default) |
| 7 | Out for issue (in transit) |

---

## Key Controller Logic Branches

```
stock_issue(save)
  ├── stock_type=1 (Tagged)
  │   ├── issue_receipt_type=1 → TAGGED ISSUE
  │   │   → INSERT ret_stock_issue → foreach tag_id → UPDATE ret_taging (0→7)
  │   │   → INSERT ret_stock_issue_detail + ret_taging_status_log
  │   └── issue_receipt_type=2 → TAGGED RECEIPT
  │       → foreach tag_id → UPDATE ret_taging (7→0) + ret_stock_issue_detail (status→3)
  │       → if is_remove_from_stock → INSERT ret_taging_status_log ⚠️ $issue_date=null (R-07)
  └── stock_type=2 (NonTagged)
      ├── type_issue=1 → NONTAG ISSUE
      │   → INSERT ret_stock_issue → foreach nt_data → INSERT ret_stock_issue_detail
      │   → if is_remove_from_stock → updateNTData(data,'-') ⚠️ raw SQL (R-16)
      │   → INSERT ret_nontag_item_log
      └── type_issue=2 → NONTAG RECEIPT
          → foreach nt_data → updateNTData(data,'+') ⚠️ raw SQL (R-16)
          → UPDATE ret_stock_issue_detail (status→3, received_date=$issue_date)
          → ⚠️ $insId undefined in response (R-18)
```

---

## OTP Flow Summary

```
JS → stock_issue_sendotp (POST: mobile, send_resend)
  → generates mt_rand(100001,999999)
  → stores in session: stock_issue_otp + stock_issue_otp_exp (60s TTL)
  → INSERT otp table
  → sends SMS
  → ⚠️ RETURNS OTP in JSON (R-09 — CRITICAL)

JS → stock_issue_verify_otp (POST: otp)
  → compares post_otp vs session stock_issue_otp
  → if match AND not expired → trans_commit → UPDATE otp table
  → if expired or wrong → ⚠️ NO trans_rollback (R-10)
  → JS stores verified_otp in hidden field is_otp_verfied
```

---

## Top 5 Bugs to Fix First

| # | Bug | Line | Fix |
|---|---|---|---|
| 1 | R-09: OTP in JSON response | Controller L1312 | Remove `'OTP' => $OTP` |
| 2 | R-22: XSS in OTP modal | JS L1751, L1801 | Use `$('<p>').text(data.msg)` instead of `.append()` |
| 3 | R-01: echo kills Tagged rollback | Controller L415, L547 | Remove echo/exit; use `log_message()` |
| 4 | R-16: updateNTData all-raw SQL | Model L1346 | Cast all fields: `(int)`, `(float)`, `escape()` |
| 5 | R-11: Hardcoded 3% GST in PDF | issue_ack.php L569 | Use `$val['tax_percentage']` from DB |
| 6 | R-02/R-15: SQLi in barcode scan | Model L733, L925 | Use `$this->db->escape()` |

---

## SQL Injection Hotspots (11 vectors) + 1 XSS

| Method | Param | Line | Type |
|---|---|---|---|
| `get_tag_scan_details` | `tag_code` (POST barcode) | L733 | 🔴 High |
| `get_receipt_tag_scan_details` | `tag_code` (POST barcode) | L925 | 🔴 High |
| `updateNTData` | all 6 fields (POST) | L1346 | 🔴 High |
| `get_nontag_scan_details` | `id_branch`, `prodId`, `id_section` | L1312 | 🟠 Med |
| `ajax_getStockIssueList` | `status` | L195 | 🟠 Med |
| `get_profile_settings` | `id_profile` (session) | L74 | 🟡 Low |
| `get_issue_item_details` | `id`, `received_time` | L360,L362 | 🟡 Low |
| `stock_issue_type_detail` | `$id` | L1031 | 🟡 Low |
| `getTagDetails` | `tag_id` | L1229 | 🟡 Low |
| `get_stock_issue_StoneDetails` | `$tag_id` (DB loop) | L854 | 🟡 Low |
| `stock_issue_tags` | `$id_stock_issue` (internal) | L1213 | 🟡 Low |
| **XSS**: `stock_order_otp()` | `data.msg` from server | JS L1751, L1801 | 🔴 **XSS** |

---

## N+1 Query Locations (3 patterns)

| Where | Per-Call | Impact |
|---|---|---|
| `ajax_getStockIssueList` L232 | `get_stock_issue_det()` per issue row | List page slow |
| `get_StockIssuedItems` L1055/L1077 | `stock_issue_tags()` / `stock_issue_nontags()` per issue | Receipt form load slow |
| `get_issue_item_details` L406,L408 | `get_StoneDetails()` + `get_other_metal_details()` per category | Print page slow |

---

## Undefined Variable Summary

| Variable | Where used | Defined at | Risk |
|---|---|---|---|
| `$issue_date` | L463, L702, L742, L898, L940 (receipt branches) | L213 (issue branch only) | NULL date in logs |
| `$result` | Model L1340 | Inside `if(gross_wt>0)` loop | Returns null to JS |
| `$insId` | Controller L1036 (NT Receipt success) | L632 (NT Issue branch only) | Wrong ID in JS response |

---

## Form Hidden Fields (22 total — key ones)

| ID | Purpose |
|---|---|
| `form_secret` | Anti-duplicate submit (CSRF-style) |
| `is_otp_verfied` | OTP gate — `1` = verified |
| `otp_required` | Profile setting — show/hide OTP UI |
| `cus_mobile` | Mobile number for OTP SMS |
| `goldrate_22ct`, `silverrate_1gm` | Metal rates (fetched on load) |
| `sto_i_increment` ×2 | Row counter ⚠️ duplicate DOM ID (R-12) |

---

## Debugging Checklist

**"Stock not deducted after issue"**
→ Check R-01: if trans_status=FALSE on Tagged Issue → echo kills rollback → no UPDATE to tag_status

**"Wrong GST on delivery challan"**
→ Check R-11: issue_ack.php L569 hardcodes 3%

**"OTP bypassed in browser"**
→ Check R-09: OTP visible in Network tab response
→ Check R-22: OTP modal shows unexpected HTML → data.msg unescaped at JS L1751/L1801

**"OTP modal freezes browser"**
→ Check R-23: async:false at JS L1527 (send) + L1719 (verify) blocks UI thread

**"NonTag receipt shows wrong issue ID"**
→ Check R-18: $insId undefined in L1036 response

**"Non-tag items not showing in scan"**
→ Check R-17: $result undefined when all gross_wt≤0

**"Issue number duplicated"**
→ Check R-05: MAX() race condition in generateIssueNo()

**"Server response data leaking to users"**
→ Check R-24: console.log at JS L225 (issue types), L1225 (NT items), L1331 (save response)


**"PDF renders in wrong orientation"**
→ Check R-21: 'portriat' typo in dompdf at Controller L1092, L1117

**"Flash alert shows raw HTML in form"** *(low risk — server data)*
→ Check R-25: `$message['message']` echoed without `htmlspecialchars()` in `form.php` L91

**"Estimation scan error not showing in Receipt section"**
→ Check R-26: Duplicate DOM ID `#searchEstiAlert` at `form.php` L437 (Issue) + L707 (Receipt) — JS always targets first match

---

> **Fix Docs**: See `FIX_GUIDE.md` for concrete before/after patches for all 26 bugs.  
> **DB Checks**: See `DB_VERIFY_QUERIES.md` to run pre-fix diagnostics against the database.
