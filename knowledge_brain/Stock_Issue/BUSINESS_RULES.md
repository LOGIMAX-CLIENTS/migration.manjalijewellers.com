# BUSINESS RULES — Stock Issue
> Verified Round 9 | 2026-03-19 | No changes needed

---

## RULE-SI-001: Issue Number Generation
**Formula**: `{fin_year_code} - {zero-padded 5-digit sequence}`
- Example: `2425-00001`, `2425-00023`
- `fin_year_code` comes from `ret_financial_year WHERE fin_status=1`
- Sequence = `MAX(SUBSTRING_INDEX(issue_no,'-',-1)) + 1` filtered by `fin_year`
- First issue of year = `{fin_year}-00001`
**Implementation**: `generateIssueNo()` in model (~L102)
**Validation**: None — server-side only
**Edge case**: Race condition! Two concurrent saves could get same MAX. No DB lock. ⚠️

---

## RULE-SI-002: Duplicate Submit Prevention (Form Secret)
**Rule**: `$_POST['form_secret']` must match `$_SESSION['FORM_SECRET']` (case-insensitive) for submit to proceed
**Formula**: `strcasecmp($form_secret, session['FORM_SECRET']) === 0`
**Implementation**: Controller L191-203, form.php L109 (`get_form_secret_key()` helper)
**Validation**: Server-side only — fail returns `{"status": false, "message": "Invalid Form Submit.."}`
**Edge case**: Session timeout clears FORM_SECRET → all submits will fail silently

---

## RULE-SI-003: Tag Status on Issue / Receipt
**Issue**: `ret_taging.tag_status = 7` (Issued/Out-of-stock)
**Receipt**: `ret_taging.tag_status = 0` (Back in stock)
**Implementation**: Controller L309, L447
**Validation**: No check that tag_status is currently 0 before issuing!
**Edge case**: A tag already issued (status=7) can be re-issued causing duplicate issue_detail rows

---

## RULE-SI-004: Issue Date Logic (Day-Close Aware)
**Rule**:
```
IF day_close.entry_date == TODAY:
    issue_date = current datetime (Y-m-d H:i:s)
ELSE:
    issue_date = day_close.entry_date (string)
```
**Implementation**: Controller L211-213
```php
$issue_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);
```
**Validation**: None — day_close could return null if not configured
**Edge case**: If `getBranchDayClosingData()` returns null → `$dCData['entry_date']` generates PHP warning

---

## RULE-SI-005: Stock Log Condition (Remove-from-Stock)
**Rule**: Status log entries are written ONLY when `ret_stock_issue_types.is_remove_from_stock = 1`
**Formula**: Status log not mandatory for internal tracking issue types (e.g., display/photoshoot)
**Implementation**: Controller L313, L453, L674, L870
**Validation**: Checked per loop iteration — correct
**Edge case**: If `stock_issue_type_detail()` returns empty (invalid type ID) → PHP notice, loop skips log

---

## RULE-SI-006: OTP Requirement Control
**Rule**: OTP required before submission if `profile.stock_issue_otp_req = 1` for the user's profile
**Formula**:
```
IF otp_required==1 AND is_otp_verfied!=1 → block submit, show OTP modal
```
**Implementation**: JS L1163-1179, Controller `stock_issue_sendotp()`, `stock_issue_verify_otp()`
**OTP Validity**: 60 seconds (session expiry at `time()+60`)
**Validation**: Server-side cross-check of session OTP vs posted OTP
**Critical Bug**: OTP value returned in response `{'OTP': $OTP}` — client can extract and auto-verify! (Controller L1312)

---

## RULE-SI-007: Non-Tag Stock Deduction Method
**Rule**: Non-tag stock is updated via arithmetic SQL (not row-level tracking)
**Formula**: `UPDATE ret_nontag_item SET no_of_piece=(no_of_piece - qty), gross_wt=(gross_wt - grs), net_wt=(net_wt - net) WHERE id_nontag_item={id}`
**Receipt reverses**: `+` arithmetic
**Implementation**: `updateNTData($data, '-')` and `updateNTData($data, '+')` (Model L1344)
**Validation**: None — negative stock possible if qty > available!
**Edge case**: Head Office items (`id_nontag_item == ''`) skip deduction (L776-790)

---

## RULE-SI-008: Received Time Batch Key
**Rule**: Multiple tags received in one receipt transaction share same `received_time` (UNIX timestamp)
**Purpose**: Groups receipt batches for reporting (PDF uses received_time as filter)
**Formula**: `$received_time = time()` once per receipt operation
**Implementation**: Controller L435
**Edge case**: Two rapid sequential receipts could share same `time()` value — ambiguous in queries

---

## RULE-SI-009: Section Log Condition
**Rule**: Section-level status logs written only if tag/nontag has `id_section != ''`
**Formula**: `if($tagDetails['id_section']!='')`
**Implementation**: Controller L339, L479, L710, L908
**Purpose**: Maintain section-level stock movement history for section-based inventory reports

---

## RULE-SI-010: Access Time Gate
**Rule**: Users with `access_time_from` set in session can only access module between `from` and `to` Unix timestamps
**Formula**: `$allowedAccess = ($now > $from && $now < $to) ? TRUE : FALSE`
**Implementation**: Constructor L44-64
**On Failure**: Redirect to `chit_admin/logout` with flash message "Exceeded allowed access time!!"
