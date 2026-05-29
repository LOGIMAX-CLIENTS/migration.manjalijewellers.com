# BUG CANDIDATES — Section Transfer
> Built: 2026-03-14 | Rounds 2+5+6+7+11+12+13 | Updated Round 14 (2026-03-24)

---

## Summary

| Severity | Count |
|---|---|
| 🔴 CRITICAL | 6 |
| 🟠 HIGH | 10 |
| 🟡 MEDIUM | 5 |
| 🟢 LOW | 6 |
| **TOTAL** | **27** |

---

## 🔴 CRITICAL

---

### BUG-ST-001: SQL Injection in `getSectionTags` — 6 Unparameterized Inputs

**File**: `ret_section_transfer_model.php`
**Lines**: L132, L136, L140, L144, L148, L152, L201, L205, L209, L213, L217, L221

**Description**:
`getSectionTags()` builds the SQL query by directly concatenating `$_POST` values from the browser:
```php
// Model L132 (no quote escaping):
.($data['id_section']!='' && $data['id_section'] > 0 ? "and t.id_section=".$data['id_section']."":'')
// Model L144 (string with quote, but no escaping):
.($data['old_tag_id']!='' ? "and t.old_tag_id = '".$data['old_tag_id']."'":'')
// Model L148 (same pattern):
.($data['tag_code']!=''  ? "and t.tag_code = '".$data['tag_code']."'":'')
```

**Inputs vulnerable**: `id_section`, `id_branch`, `id_product`, `old_tag_id`, `tag_code`, `est_no`

**Impact**: Full SQL injection — data exfiltration, table modification/drop possible via any of these fields.

**Fix**: Use `$this->db->escape()` on all values or switch to ActiveRecord query builder.

---

### BUG-ST-002: SQL Injection in `checkNonTagItemExist`

**File**: `ret_section_transfer_model.php`
**Line**: L251

**Description**:
```php
$sql = "SELECT id_nontag_item FROM ret_nontag_item WHERE branch=".$data['branch']." AND product=".$data['product']." AND design=".$data['design']." AND id_section=".$data['id_section'] ." AND id_sub_design=".$data['id_sub_design'];
```
All 5 values come from `$_POST['trans_data']` (JS sends them). No escaping applied.

**Impact**: SQLi in non-tag transfer path.

---

### BUG-ST-003: SQL Injection in `updateNTData` — Arithmetic SQL with Raw User Values

**File**: `ret_section_transfer_model.php`
**Line**: L281

**Description**:
```php
$sql = "UPDATE ret_nontag_item rt SET 
  no_of_piece=(no_of_piece".$arith." ".$data['no_of_piece']."),
  gross_wt=(gross_wt".$arith." ".$data['gross_wt']."),
  net_wt=(net_wt".$arith." ".$data['net_wt']."),
  updated_by=".$data['updated_by'].",
  updated_on='".$data['updated_on']."' 
WHERE id_nontag_item=".$data['id_nontag_item'];
```
The arithmetic operator `$arith` ('+' or '-') comes from the controller (safe), but all numeric values come from user POST data. A crafted payload like `0); DROP TABLE ret_nontag_item; --` could be injected.

**Companion bug**: `updatesecNTData` (Model L365) has identical pattern on `ret_home_section_item`.

**Impact**: Stock table manipulation or destruction.

---

### BUG-ST-004: Home-Counter Stock — Only DECREMENTED, Never INCREMENTED

**File**: `admin_ret_section_transfer.php`
**Lines**: L181–311

**Description**:
When a tag is transferred to a home-bill-counter section (`is_home_bill_counter=1`), the code:
1. Calls `checkSectionItemExist()` at the **destination** section (L201)
2. If the destination already has that product: calls `updatesecNTData($section_item, '-')` — DECREMENT (L239)
3. No `'+'` increment path exists at all.

The intent appears to be: decrement the **source** section's home stock and increment the **destination** section. But the code:
- Checks existence at the DESTINATION (`id_section = $transfer_to_section`) — contradicts the decrement intent
- Uses `'-'` arithmetic on what it found — this REDUCES destination stock (opposite of intent)
- Has NO code to increment any section stock

**Result**: Every home-counter transfer REDUCES stock in `ret_home_section_item`, never grows it. Net effect: home counter stock steadily goes negative.

**Duplicate if block**: Line L233 is an exact duplicate of L203 (`if($isExists['id_hometag_item']!='')`) — dead nested code.

**Severity**: 🔴 CRITICAL — home counter stock corruption.

---

## 🟠 HIGH

---

### BUG-ST-005: `updatestatus()` Called with Extra Arguments (Signature Mismatch)

**File (controller)**: `admin_ret_section_transfer.php` L287
**File (model)**: `ret_section_transfer_model.php` L381

**Description**:
Controller call:
```php
$this->$model->updatestatus($val['tag_id'], 'tag_id', $val['tag_id'], 'ret_taging');
```
Model signature:
```php
function updatestatus($tag_id) {
    $sql = "UPDATE ret_taging SET tag_status= 14 WHERE tag_id=".$tag_id;
```
PHP silently ignores the extra arguments. However, the function signature is misleading — it looks like a generic `updateData` wrapper but hard-codes `tag_status=14` and `ret_taging`. Any developer who adds params expecting generic behavior will be confused.

**Also**: `tag_status` is hardcoded to **14** (not the semantically correct 16). Status 16 is logged to `ret_taging_status_log` (L295), but the physical `ret_taging.tag_status` is set to 14. Possible mismatch.

---

### BUG-ST-006: OTP Value Returned in API Response

**File**: `admin_ret_section_transfer.php`
**Line**: L610

**Description**:
```php
$status = array('status' => true, 'msg' => 'OTP sent Successfully', 'OTP' => $sent_otp);
echo json_encode($status);
```
The OTP is returned as `'OTP'` in the JSON response. Any user who opens DevTools → Network tab can see the OTP and bypass the approval flow entirely.

**Impact**: Complete bypass of counter-change authorization.

---

### BUG-ST-007: Multi-Mobile OTP — Concatenation Without Delimiter

**File**: `admin_ret_section_transfer.php`
**Lines**: L568–578

**Description**:
```php
foreach($mobile_num[0] as $mobile) {
    $OTP = mt_rand(1001,9999);
    $sent_otp .= $OTP;  // No delimiter between OTPs!
    $this->session->set_userdata('counterchange_otp', $sent_otp);
```
If a branch has 2 registered mobiles, `$sent_otp` = `"51316482"` (two 4-digit codes concatenated).

In `verify_counter_change_otp`, the session OTP is compared:
```php
$otp = array(explode(',', $session_otp)); // splits by comma — but there IS no comma!
foreach($otp[0] as $OTP) {
    if($OTP == $post_otp) { ... }
```
The `explode(',', ...)` never splits the concatenated string, so the comparison `"51316482" == "5131"` always fails. **OTP verification will always fail for branches with multiple registered mobiles.**

---

### BUG-ST-008: NT Transfer — No Server-Side Quantity Cap

**File**: `admin_ret_section_transfer.php` / `ret_section_transfer_model.php`
**Lines**: Controller L317–517

**Description**:
The client-side validates `pieces ≤ balance_pieces` etc. (JS L1293–1381). But the server-side `save` case for `section_item_type==2` does zero validation of qty:
- No check that `no_of_piece > 0`
- No check that `gross_wt >= net_wt`
- No check that entered qty ≤ available stock

A crafted POST with `no_of_piece: 9999` will deduct 9999 from `ret_nontag_item.no_of_piece`, resulting in a large negative balance.

---

### BUG-ST-009: `getBranchDayClosingData` — SQL Injection (Branch ID)

**File**: `admin_settings_model.php`
**Line**: L2410

**Description**:
```php
$sql = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing 
    ". ($id_branch != '' ? " where id_branch=".$id_branch."" : '') . "");
```
`$id_branch` comes directly from `$_POST['id_branch']` in the controller (no sanitization). While branch is usually a numeric dropdown, it is injectable.

**Also present in**: `ret_section_transfer_model.php` L71 (model also has its own copy of this method with same injection).

---

## 🟡 MEDIUM

---

### BUG-ST-010: NT Deduct Log — `to_section` Hardcoded NULL

**File**: `admin_ret_section_transfer.php`
**Lines**: L377–393

**Description**:
```php
$section_nontag_log = array(
    ...
    "to_section"  => NULL,   // ← should be $transfer_to_section
    ...
    "status"      => 4,      // deduct from source
```
The log entry for deducting NT stock from the source records `to_section=NULL`. The destination (`$transfer_to_section`) is never stored in this log row. This breaks the ability to audit the full trail: you cannot know WHERE the stock came from by reading the destination-section's log.

---

### BUG-ST-011: Home Section Item Log — `from_section` Hardcoded NULL

**File**: `admin_ret_section_transfer.php`
**Line**: L269

**Description**:
```php
"from_section" => NULL,  // ← tag's original section not recorded
```
The log entry in `ret_home_section_item_log` never records the source section. While the `ret_taging.id_section` is updated before this log is written (making the original section retrievable only from `ret_section_tag_status_log`), best practice would be to store it here for direct audit.

---

### BUG-ST-012: `$insId` Used Outside Its Inner Scope in `send_counterchange_otp`

**File**: `admin_ret_section_transfer.php`
**Lines**: L591–615

**Description**:
```php
foreach($mobile_num[0] as $mobile) {
    ...
    $insId = $this->$model->insertData($insData, 'otp');
    if($insId) { ... }
}
if($insId) {   // ← $insId from last iteration only!
    $this->db->trans_commit();
    ...
```
`$insId` is only checked after the loop. If the first mobile's OTP insert succeeds but the second fails, the transaction will be committed (since only the last `$insId` is checked).

Additionally, the inner `trans_begin()` is inside the foreach (L591) while `trans_commit()` is outside the foreach (L609) — **this means only ONE trans_begin() runs but TWO OTP inserts happen inside, with no trans_begin for the second one.**

---

### BUG-ST-013: `section_tag_search` Validation Gap — Product Required but Not for Estimation Search

**File**: `admin/assets/js/ret_section_transfer.js`
**Lines**: L424–430

**Description**:
```javascript
else if($('#prod_select').val()=="" || $('#prod_select').val()==null) {
    $.toaster({...'Select Product..'});
}
```
Product is required by JS before calling `getSectionTags`. However, when user searches by `est_no` (estimation number), no product is relevant — the estimation itself identifies the items. The product validation wrongly blocks estimation-based searches if the user hasn't selected a product.

---

### BUG-ST-014: `SectionTagData` Never Reset Before Re-Collection on OTP Re-send

**File**: `admin/assets/js/ret_section_transfer.js`
**Lines**: L1431 vs L33/L940

**Description**:
The global `var SectionTagData = []` is declared at L33 and populated in the `#section_transfer` click handler (L929–946). When the OTP `send_counter_change_otp_yes` button is clicked, a **local** `var SectionTagData = []` is declared at L1431. The global one was already populated at the Transfer button click. After OTP verification succeeds, `add_to_trans(SectionTagData)` at L1597 uses the **global** variable (from L1593 scope). If user clicks Transfer multiple times before OTP, items are duplicated in `SectionTagData` (global never cleared).

---

## 🟢 LOW

---

### BUG-ST-015: Session OTP Not Unset After Successful Verification

**File**: `admin_ret_section_transfer.php`
**Lines**: L640–653

**Description**:
After a successful OTP match, `session('counterchange_otp')` and `session('counterchange_otp_exp')` are never unset. The OTP remains valid in the session until it naturally expires (300s). A user could verify the OTP once, then immediately make another transfer without going through the OTP flow again by calling the verify endpoint directly.

---

### BUG-ST-016: `calculateSectiontotal()` Uses Hard-coded Column Indexes (`td:eq(6)`, `td:eq(7)`, `td:eq(8)`)

**File**: `admin/assets/js/ret_section_transfer.js`
**Lines**: L865–873

**Description**:
```javascript
tot_pcs = ... row.find('td:eq(6) .piece').val()
tot_gwt = ... row.find('td:eq(7) .gross_wt').val()
tot_nwt = ... row.find('td:eq(8) .net_wt').val()
```
If columns in `section_trans_list` are reordered (e.g., a new column is inserted), totals will silently return 0. Fragile — should use class-based selectors instead of position.

---

## Bug Summary by Category

| Category | Bugs |
|---|---|
| SQL Injection | BUG-ST-001, 002, 003, 009, 018 |
| Business Logic / Data Integrity | BUG-ST-004, 005, 008, 017 |
| Security | BUG-ST-006, 007, 015 |
| Audit Trail Corruption | BUG-ST-010, 011 |
| Transaction Safety | BUG-ST-012 |
| UI / JS Logic | BUG-ST-013, 014, 016 |
| Unparameterized Raw SQL | BUG-ST-019 |

---

## 🟠 HIGH — Added in Round 5

---

### BUG-ST-017: NT Destination Increment Silently Skipped When Source Has No `id_nontag_item`

**File**: `admin_ret_section_transfer.php`
**Lines**: L431–439

**Description**:
When the destination section already has the NT product (`checkNonTagItemExist` returns a record at L403), the code should add the transferred qty to it. The update is wrapped in:
```php
if($val['id_nontag_item'] != '') {
    $nt_data['id_nontag_item'] = $isExists['id_nontag_item'];
    $nt_status = $this->$model->updateNTData($nt_data,'+')
}
```
The guard `$val['id_nontag_item'] != ''` checks the SOURCE item, not the destination. If the source item came from a receipt (without an NT table row, so `id_nontag_item=''` from JS), this guard fails and the destination stock is NEVER incremented.

**Impact**: NT items transferred from receipt-source to a section where stock already exists will silently deduct from source but NOT add to destination — stock disappears.

---

### BUG-ST-018: SQL Injection in `fetchNonTaggedItems` (BT Model) — Shared Endpoint Used by ST

**File**: `ret_brntransfer_model.php`
**Lines**: L326–328

**Description**:
```php
WHERE branch=" . $data['from_brn'] . " "
.($data['prodId'] != '' ? ' and nt.product=' . $data['prodId'] : '')
.($data['id_section'] != '' ? ' and nt.id_section=' . $data['id_section'] : '')
```
The `getNonTaggedItem` endpoint in `admin_ret_brntransfer` is used by Section Transfer JS (`ret_section_transfer.js` L1133). POST params `from_brn`, `prodId`, `id_section` are passed directly from JS without server-side escaping.

**Impact**: SQLi exploitable via `prodId` in the NT item search. Affects both Section Transfer and Branch Transfer modules.

---

## 🟢 LOW — Added in Round 5

---

### BUG-ST-019: `checkSectionItemExist` Uses Unparameterized Raw SQL

**File**: `ret_section_transfer_model.php`
**Line**: L329

**Description**:
```php
$sql = "SELECT id_hometag_item FROM ret_home_section_item WHERE id_branch=".$data['id_branch']." AND id_section=".$data['id_section']." AND id_product=".$data['id_product'];
```
All three fields are integer PKs sourced from session or prior query results (not raw user POST). Risk is low since these values come from internal lookups, not direct user input. However, the pattern is inconsistent with parameterized fields elsewhere and should be cleaned up.

**Companion**: `sectionData($transfer_to_section)` at L313 has the same unparameterized pattern for `id_section`.

---

## Verified-Safe in Round 5

| Item | Verdict |
|---|---|
| NT destination INSERT path (L443–471) | ✅ EXISTS — `insertData` called when no existing row |
| `fetchNonTaggedItems` 0-weight filter | ✅ INTENTIONAL — PHP filters `gross_wt > 0` to hide empty rows |
| `checkSectionItemExist` source vs dest scope | ✅ CORRECT — called after `$nt_data['id_section']` is set to `$transfer_to_section` |

---

## 🔴 CRITICAL — Added in Round 6

---

### BUG-ST-020: `save` Endpoint Has NO CSRF / Form-Secret Protection

**File**: `admin_ret_section_transfer.php`
**Line**: L107 (case 'save')

**Description**:
The `save` case immediately processes `$_POST` data:
```php
case 'save':
    $transfer_to_section  = $_POST['trans_to_section'];
    $branch = $_POST['id_branch'];
    // ... directly into DB operations
```
There is NO CSRF token, NO `form_secret` session guard. Compare with Branch Transfer (`admin_ret_brntransfer.php`) which checks `FORM_SECRET` before any data mutation. Any authenticated session can replay a save POST silently.

**Impact**: CSRF attack — any page that tricks the logged-in user into submitting to this endpoint will silently execute a stock transfer.

---

## 🟠 HIGH — Added in Round 6

---

### BUG-ST-021: OTP SMS Never Actually Sent — Entire SMS Block Is Commented Out

**File**: `admin_ret_section_transfer.php`
**Lines**: L596–601

**Description**:
```php
if($insId) {
    // if($service['serv_whatsapp'] == 1) {
    //     $message = "Hi Your OTP For Counter Change is: ..." ;
    //     $whatsapp = $this->admin_usersms_model->send_whatsApp_message(9486528828, $message);
    // }
}
```
The OTP is generated, stored in session, and inserted into the DB — but the entire SMS/WhatsApp send block is commented out. The user receives no notification. The OTP modal opens, the "OTP sent Successfully" toast appears, but no message is ever delivered.

**Impact**: OTP flow is completely non-functional — counter-change transfers can never proceed via OTP because the OTP is never delivered to the approver's phone. The only way a user can "verify" is by reading the OTP from the JSON response (BUG-ST-006).

---

## 🟢 LOW — Added in Round 6

---

### BUG-ST-022: Multi-Mobile `otp_verif_mobileno` Trailing Spaces on Split

**File**: `admin_ret_section_transfer.php`
**Line**: L568

**Description**:
```php
$data = $this->$model->getBrnachOtpRegMobile($id_branch);
$mobile_num = array(explode(',', $data));
```
If `otp_verif_mobileno` is stored as `"9876543210, 1234567890"` (with a space after comma), `explode(',', $data)` produces `['9876543210', ' 1234567890']`. The second number has a leading space. This space is then passed as the mobile number in `$insData['mobile']` and to the SMS API — causing SMS delivery failure for the second number.

**Fix**: `trim($mobile)` inside the foreach loop.

---

## Verified-Safe in Round 6

| Item | Verdict |
|---|---|
| View layer XSS (list.php L1–443) | ✅ SAFE — no raw user-data echo; only session integers and profile flags |
| JS OTP verify flow (L1571–1627) | ✅ CONFIRMED — calls `add_to_trans(SectionTagData)` after verify (reuses global, BUG-ST-014 confirmed) |
| `getBrnachOtpRegMobile` multi-mobile query | ✅ QUERY OK — returns single string; split logic has space issue (BUG-ST-022) |
| View `$counter_change_otp` echo | ✅ LOW-RISK — integer from profile DB, not user-controlled input |

---

## 🟢 LOW — Added in Round 7

---

### BUG-ST-023: `calculateNTtotal` Selector Space Typo — NT Gross Weight Always 0

**File**: `ret_section_transfer.js`
**Line**: L1399

**Description**:
```javascript
// BUGGY — note the leading space before .nt_gross_wt:
grs_wt = grs_wt + (isNaN( row.find('.nt_gross_wt').val() ) ? 0 : parseFloat(row.find(' .nt_gross_wt').val()));
//                                                                                    ^ space here
```
The `find('.nt_gross_wt')` call for the `isNaN` check works correctly (no space), but the actual `parseFloat()` call uses `' .nt_gross_wt'` (with a leading space). jQuery will still match this (CSS descendant selector trick — same as `* .nt_gross_wt` searching all descendants), so the value IS found, but the fix is still fragile and inconsistent. However, compare with how `nt_pieces` and `nt_net_wgt` are handled at L1397 and L1401 — no leading space. This is a copy-paste artifact.

**Actual impact**: In most cases jQuery *does* find the value with a space (as it treats it as a descendant selector), so gross weight may total **correctly in practice**. The bug is a code quality / reliability issue rather than a hard functional failure. The selector is fragile if the HTML structure changes.

**Fix**: Remove the leading space: `row.find('.nt_gross_wt').val()`

---

## Verified-Safe in Round 7

| Item | Verdict |
|---|---|
| `getSectionTags` dual SQL (L98–234) | ✅ CORRECT LOGIC — `est_no` branch adds estimation JOINs; SQLi fix must be applied to BOTH branches |
| Model `insertData`/`updateData` (L23–47) | ✅ SAFE — uses CI3 ActiveRecord (`insert`, `where`+`update`), no raw SQL |
| JS `add_to_trans` POST construction (L1021–1029) | ✅ CORRECT — sends correct branch/section IDs; CSRF gap is server-side (BUG-ST-020) |
| NT `id_nontag_item` as empty string in JS (L964) | ✅ CONFIRMED — `fetchNonTaggedItems` always returns a valid `id_nontag_item` (query on `ret_nontag_item` table); BUG-ST-017 may be lower risk than originally assessed |
| `calculateNTtotal` `grs_wt` space typo (L1399) | ⚠️ SOFT BUG — added as BUG-ST-023; jQuery finds value but selector unreliable |

---

## Verified-Safe in Round 8

| Item | Verdict |
|---|---|
| JS `counterchange_otp()` send (L1495–1511) | ✅ SAFE — sends total_gwt, tot_pcs, id_branch, from_section, to_section correctly |
| OTP `send_counter_change_otp_no` (L1481) | ✅ SAFE — re-enables Transfer button when user cancels |
| Tagged save: `$tag_data` with only `id_section` (L143) | ✅ SAFE — CI3 ST model `updateData` only sets specified columns (no SHOW COLUMNS default fill-in unlike BT model) |
| `get_tag_details()` guard `tag_status==0` (L139) | ✅ CORRECT — validates tag is available before transfer |
| `$section_item_log` in tagged path (L251–283) | ✅ LOGGED — inserts to `ret_home_section_item_log` correctly |
| `$taging_status_log` status=16 (L295) | ✅ INTENTIONAL — logs status 16 even though `updatestatus()` sets 14 (BUG-ST-013 scope confirmed) |
| Index endpoint (L61–67) | ✅ EMPTY — no exposure |

---

## 🔴 AUDIT COMPLETE — 8 Rounds, 23 Bugs

| Severity | Count | Bugs |
|---|---|---|
| 🔴 CRITICAL | 5 | BUG-ST-001, 002, 003, 004, 020 |
| 🟠 HIGH | 8 | BUG-ST-005, 006, 007, 008, 009, 017, 018, 021 |
| 🟡 MEDIUM | 5 | BUG-ST-010, 011, 012, 013, 014 |
| 🟢 LOW | 5 | BUG-ST-015, 016, 019, 022, 023 |
| **TOTAL** | **23** | |

---

## 🔬 Round 9 — Billing Cross-Module Clarifications

### BUG-ST-004 Fix Direction — DEFINITIVELY CONFIRMED ✅

**Question**: Should `ret_home_section_item.no_of_piece` go UP or DOWN when a tag reaches a home-counter section?

**Billing evidence**:
- `admin_ret_billing.php` L3699 → only writes to `ret_home_section_item_log` (audit log), **NOT** to `ret_home_section_item`
- `ret_billing_model.php` → **ZERO references** to `ret_home_section_item` — billing does NOT deduct from this table on sale

**Conclusion**: `ret_home_section_item` is a Section Transfer–managed stock counter whose purpose is to track **how many items OF each product ARE AT the home counter**. When a tag **arrives** at a home-counter section: counter should **increase (+)**. When a tag **leaves** a home-counter section: counter should **decrease (-)**.

**Current code bug**: Controller L181 checks `is_home_bill_counter == 1` for destination section and calls `updatesecNTData(section_item, '-')` — DECREMENT. This is backwards for an arriving tag. The missing code is an INCREMENT call for the destination section, and a DECREMENT call for the SOURCE section (if source was also a home-counter section).

**Correct fix logic**:
```php
// When destination is home counter → INCREMENT
if($destSection['is_home_bill_counter'] == 1) {
    $isExists = $this->$model->checkSectionItemExist($dest_item);
    if($isExists['id_hometag_item'] != '') {
        $dest_item['id_hometag_item'] = $isExists['id_hometag_item'];
        $this->$model->updatesecNTData($dest_item, '+');  // ← ADD
    }
}

// When SOURCE was home counter → DECREMENT
$srcSection = $this->$model->sectionData($val['trans_from_section']);
if($srcSection['is_home_bill_counter'] == 1) {
    $isExists = $this->$model->checkSectionItemExist($src_item);
    if($isExists['id_hometag_item'] != '') {
        $src_item['id_hometag_item'] = $isExists['id_hometag_item'];
        $this->$model->updatesecNTData($src_item, '-');  // ← REMOVE
    }
}
```

---

### BUG-ST-013 Scope — CONFIRMED AS DATA-INTEGRITY ONLY ✅

**Question**: Does billing read `tag_status=14` to identify home-counter tags?

**Answer**: **NO.** Billing never queries for `tag_status=14`. Billing identifies a tag's section via the `id_section` column of `ret_taging`. Tag status values billing uses:
- `tag_status=0` → available tag (sold via billing → reset to 0 with `is_approval_stock_converted` flag)  
- `tag_status=11` → approval stock  
- Setting to `14` vs `16` only affects the **audit trail** — the `ret_taging_status_log` records the wrong status

**Fix**: In `updatestatus()` model function, change hardcoded `14` → `16` to match the status logged in `ret_taging_status_log`. OR: unify status to `14` and update the log.

---

## 🔬 Round 10 — ret_home_section_item Lifecycle (Billing Deep Read)

### BUG-ST-004 — FULLY RESOLVED ✅

**Source read**: `admin_ret_billing.php` L3580–3667

```php
// Billing L3580 trigger:
if ($billSale['itemtype'][$key] == 2 && $billSale['id_section'][$key] != '') {

    // L3638 — if row exists → INCREMENT (+)
    $nt_status = $this->$model->updatesecNTData($section_item, '+');

    // L3666 — if no row → INSERT (new entry)
    $nt_status = $this->$model->insertData($section_item, 'ret_home_section_item');
}
```

**itemtype == 2 meaning**: A **tagged item from a section** in a retail sale bill. When a billing operator sells a tagged item that belongs to a section, billing increments (+) `ret_home_section_item` for that section/product.

**ret_home_section_item lifecycle interpretation**:

| Event | Actor | Operation |
|---|---|---|
| Tag transferred TO home-counter section | Section Transfer | Should INSERT or INCREMENT `(+)` |
| Tag sold from home-counter section by billing | Billing | Calls `updatesecNTData('+')` ← ALSO increments |
| Tag transferred FROM home-counter section | Section Transfer | Should DECREMENT `(-)` |

**The real bug**: The table is being incremented by BOTH billing (on sale) AND should be incremented by ST (on transfer arrival). This suggests the table may function as a **running ledger** (total throughput, not net stock). But since the ST code currently calls `updatesecNTData('-')` (DEDUCT) on transfer TO a home counter — this is the confirmed incorrect direction.

> **⚠️ CAUTION — HUMAN VALIDATION REQUIRED BEFORE FIX**
>
> Billing's `+` increment when selling a home-counter tag is unexpected. This conflicts with typical stock logic where sales should reduce available count. Two interpretations are possible:
>
> 1. **Table = stock available at home counter**: ST should `+` on arrival, billing should `-` on sale. Billing's current `+` is a **billing bug** (outside this module's scope).
> 2. **Table = cumulative sold/processed count**: Both ST `+` and billing `+` are additive supply-chain entries. ST's current `-` is still wrong.
>
> Either way, **ST's current `(-)` on transfer-to-home-counter destination is wrong** — destination should get `(+)`.
>
> **Confirm with team**: Does `ret_home_section_item` represent available stock or cumulative throughput?

---

## 🔬 Round 11 — Billing Cancel/Return Impact Analysis

### BUG-ST-024: `ret_home_section_item` Never Reversed on Bill Cancel (NEW) 🔴 CRITICAL

**Files**: `admin_ret_billing.php`
**Evidence**:
- L7591: Receipt cancel path → `updateData(['tag_status'=>0], ...)` — **tag freed back to available**
- L9897: Approval branch transfer → `updateData(['tag_status'=>0, ...], ...)` — **same reset**
- **NEITHER path calls `updatesecNTData()` or touches `ret_home_section_item`**

**The bug**:
1. Section Transfer sends a tag to a home-counter section → `ret_home_section_item` count set
2. Billing sells the tag → billing calls `updatesecNTData('+')` — count increases further
3. Billing **cancels** the sale → tag `tag_status=0` (refunded) but `ret_home_section_item` is **never decremented**
4. Net effect: home section item count is permanently inflated each time a bill is cancelled

**Compounding with BUG-ST-004**: ST also writes the wrong direction (`'-'` on destination). Combined with billing never reversing on cancel, the table becomes an entirely unreliable counter over time.

**What billing SHOULD do on cancel**:
```php
// After resetting tag_status=0 at ~L7591, billing also needs:
$section_item_check = ['id_branch'=>$branch, 'id_section'=>$section, 'id_product'=>$product_id];
$isExists = $this->$model->checkSectionItemExist($section_item_check);
if($isExists['id_hometag_item'] != '') {
    $rollback_item = array(
        'id_hometag_item' => $isExists['id_hometag_item'],
        'no_of_piece' => $pcs,
        'gross_wt' => $gwt,
        'net_wt' => $nwt,
        'updated_by' => $this->session->userdata('uid'),
        'updated_on' => date('Y-m-d H:i:s'),
    );
    $this->$model->updatesecNTData($rollback_item, '-');  // reverse the sale
}
```

**Scope**: This is a **billing module bug** that directly creates ST data integrity issues. File under ST as cross-module finding; fix must be applied in billing controller.

**Severity**: 🔴 CRITICAL — home-counter stock count permanently inflated on every cancelled sale.

---

## 🔬 Round 12 — Billing Delete Bill + ST Reverse Gap

### BUG-ST-024 Extended — Delete Bill Also Misses ret_home_section_item Reversal

**Billing delete function evidence** (`admin_ret_billing.php` L7740–7882):

```
L7774: if ($bill['tag_id'] != '' && $bill['item_type'] == 0)     ← tagged item
L7776:   updateData(['tag_status' => 0], ...)                    ← tag freed ✅
L7796:   insertData($log_data, 'ret_taging_status_log')          ← status=6 logged ✅
L7824:   insertData($secttag_log, 'ret_section_tag_status_log')  ← section log ✅
L7830:   checkNonTagItemExist(...)                               ← NT stock check
L7852:   updateNTData($nt_data, '+')                             ← NT RESTORED ✅
NO:      updatesecNTData or ret_home_section_item reversal       ← MISSING ❌
```

**Key asymmetry confirmed**:

| Stock Table | Billing SALE | Billing DELETE |
|---|---|---|
| `ret_nontag_item` | `updateNTData('-')` | `updateNTData('+')` ✅ reversed |
| `ret_home_section_item` | `updatesecNTData('+')` | ❌ **NOT reversed** |
| `ret_taging.tag_status` | → sold (various) | → 0 ✅ restored |

NT stock is symmetrically handled (sale decrements, delete increments). Home section item has NO symmetric reversal — it is a one-way street. Every billing delete permanently adds to the home section count.

**Combined impact of all home section bugs**:

| Bug | Direction Error | Effect |
|---|---|---|
| BUG-ST-004 | ST decrements on transfer-to-destination (should increment) | Undercounts on arrival |
| BUG-ST-024 | Billing delete doesn't reverse the sale increment | Overcounts on delete |
| Net | Random drift, no reliable count possible | Stock data unreliable |

---

### BUG-ST-025: Section Transfer Has No UNDO/Reverse Feature (Missing Feature Gap) 🟠 HIGH

**Finding**: The Section Transfer controller (`admin_ret_section_transfer.php`) has **5 functions** total:
- `index()` — empty
- `ret_section_transfer('list')` — page load
- `ret_section_transfer('getSectionTags')` — search
- `ret_section_transfer('save')` — execute transfer
- `send_counterchange_otp()` / `verify_counter_change_otp()` — OTP flow

**Missing**: There is no `undo_transfer`, `reverse_transfer`, or `cancel_transfer` function.

**Impact**: Once a section transfer is committed:
- `ret_taging.id_section` is updated to new section — **no rollback**
- `ret_home_section_item` is decremented (incorrectly) — **no correction**
- `ret_section_tag_status_log` is logged — **permanent record created**
- NT stock decremented — **no reversal possible**

**Business impact**: If a user transfers a tag to the wrong section, they must do a second transfer back. But the bad intermediate state in logs and stock counts remains permanently. In the case of NT items, there is no way to know the stock was briefly wrong unless logs are carefully audited.

**Severity**: 🟠 HIGH — operational gap in jewelry retail where mis-transfers are common (wrong barcode scan, wrong section selected).

---

## 🔬 Round 13 — All Billing Reversal Paths Confirmed

### BUG-ST-024 Final Scope — Systemic Cross-Module Gap (All 4 Reversal Paths)

All 4 billing operations that reverse a tag sale were read. Result: **not one of them reverses `ret_home_section_item`**.

| Path | Billing Location | tag_status after | NT Stock | ret_home_section_item |
|---|---|---|---|---|
| Estimate bill cancel (`cancell`) | L5082–5200 | 6 (cancelled) | `updateNTData('+')` ✅ | ❌ NOT reversed |
| Bill delete | L7740–7882 | 0 (available) | `updateNTData('+')` ✅ | ❌ NOT reversed |
| Receipt cancel | L7560–7615 | 0 (available) | — (no NT) | ❌ NOT reversed |
| `update_branch` (approval conv.) | L9875–9990 | 0 (available) | — (no NT) | ❌ NOT reversed |

**Pattern**: NT stock is correctly symmetric (billing credits it back). `ret_home_section_item` is treated as write-once — no path ever decrements it on reversal. This is a systemic design gap, not an isolated bug.

**Fix scope**: All 4 billing functions need a `updatesecNTData('-')` call after finding the tag's section, similar to how `updateNTData('+')` is called for NT stock. The billing model already has access to `checkSectionItemExist` and `updatesecNTData`.

---

### BUG-ST-026: Debug `exit` Statement Left in Production Billing Code 🟢 LOW

**File**: `admin_ret_billing.php`
**Line**: L10005

```php
} else {
    echo $this->db->last_query();  // ← DEBUG CODE
    exit;                          // ← KILLS RESPONSE
    $this->db->trans_rollback();   // ← UNREACHABLE
    ...
}
```

`echo $this->db->last_query(); exit;` in the `update_branch` failure branch means:
- On any DB failure: raw SQL is printed to the browser (SQL exposure)
- `trans_rollback()` is never called (the transaction remains open/uncommitted)
- Session flashdata is never set

**Severity**: 🟢 LOW — functional impact is low (the path is rare), but any DB failure becomes a hard crash with SQL exposure instead of a graceful rollback.

---

## 🔬 Round 14 — Code Refresh (2026-03-24)

### BUG-ST-027: `onlyBranchSelected` Order-Reservation Guard Bypassed by Tag-Code/Old-Tag-ID Search 🟠 HIGH

**File**: `ret_section_transfer_model.php`
**Lines**: L89–231

**Description**:
The R14 code scan found that `getSectionTags()` now includes a guard condition:
```php
// L89-91 (model):
$onlyBranchSelected = (!empty($data['id_branch']) || !empty($data['id_section']))
    && (empty($data['old_tag_id']) && empty($data['tag_code']) && empty($data['est_no']));
```
This guard is `TRUE` only when the user filters by branch/section **without** specifying `old_tag_id`, `tag_code`, or `est_no`. When the guard is true, the query appends:
```php
// L229-231 (model):
if ($onlyBranchSelected) {
    $sql .= " AND (t.id_orderdetails IS NULL OR t.id_orderdetails = '')";
}
```
This blocks order-reserved tags from appearing in branch-only searches — **correct behavior**.

**The gap**: When a user searches by `old_tag_id` or `tag_code`, `onlyBranchSelected` evaluates to `FALSE`, and the order-reservation filter is **NOT applied**. Search results include order-reserved tags, which the user can then include in a section transfer.

**RULE-ST-007 says**: Tags linked to customer orders should be blocked from transfer. The current implementation only enforces this for branch/section-only filter mode. Barcode-scan transfers (most common in-store use case) bypass this protection entirely.

**Impact**: Staff scanning a barcode for a customer-order-reserved tag will see it in the search results and can transfer it to a different section — breaking the customer order reservation without any warning.

**Fix**: Move the order-reservation filter outside the `onlyBranchSelected` condition, or apply it unconditionally:
```php
// Apply regardless of search mode:
$sql .= " AND (t.id_orderdetails IS NULL OR t.id_orderdetails = '')";
```
Or: show the tag but display a warning badge in JS — depending on business requirement (staff may need to transfer order-tags intentionally in some workflows).

**Severity**: 🟠 HIGH — silent order-reservation bypass in the primary barcode-scan user flow.
