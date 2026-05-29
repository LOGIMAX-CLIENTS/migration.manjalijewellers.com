# FIX GUIDE — Stock Issue Module
> Created: Round 12 | 2026-03-19  
> Use alongside QUICK_REFERENCE.md — Covers all 26 bugs with concrete code patches

---

> [!IMPORTANT]
> Fix order matters. Always fix **R-01** (echo/exit kills rollback) before testing any other transaction bugs. Fix **R-09** (OTP in JSON) immediately — it is a live data leak in production.

---

## 🔴 Critical Bugs (Fix First)

---

### R-01 — Debug `echo last_query(); exit` Kills Transaction Rollback
**File**: `admin_ret_stock_issue.php` (Controller)  
**Where**: `stock_issue('save')` — inside `trans_status() === FALSE` else branch

```php
// ❌ WHAT EXISTS (kills rollback — transaction stays open)
} else {
    echo $this->db->last_query(); exit;
}

// ✅ FIX — Remove echo/exit, add rollback
} else {
    $this->db->trans_rollback();
    log_message('error', 'Stock Issue save failed: ' . $this->db->_error_message());
    echo json_encode(['status' => false, 'message' => 'Save failed. Please try again.']);
}
```

> **Also check**: Search controller for ALL `echo last_query();exit` — there are 4 instances total. Remove all of them the same way.

---

### R-02 — SQL Injection: `tag_code` in `get_tag_scan_details`
**File**: `ret_stock_issue_model.php`

```php
// ❌ WHAT EXISTS
$sql = "SELECT * FROM ret_taging WHERE tag_code='".$tag_code."' AND tag_status=0";
$this->db->query($sql);

// ✅ FIX — Use prepared query
$this->db->where('tag_code', $tag_code);
$this->db->where('tag_status', 0);
$result = $this->db->get('ret_taging');
```

---

### R-03 — SQL Injection: `tag_code` in `get_receipt_tag_scan_details`
**File**: `ret_stock_issue_model.php`

```php
// ❌ WHAT EXISTS (same pattern as R-02)
$sql = "SELECT ... WHERE tag_code='".$tag_code."'";

// ✅ FIX — Same approach as R-02
$this->db->where('tag_code', $tag_code);
$result = $this->db->get('ret_taging');
```

---

### R-09 — OTP Returned in JSON Response (Live Data Leak)
**File**: `admin_ret_stock_issue.php` — `stock_issue_sendotp()` method

```php
// ❌ WHAT EXISTS (OTP visible in browser DevTools)
echo json_encode([
    'status' => true,
    'msg'    => 'OTP sent successfully',
    'OTP'    => $otp   // ← REMOVE THIS KEY ENTIRELY
]);

// ✅ FIX — Never return OTP in response
echo json_encode([
    'status' => true,
    'msg'    => 'OTP sent to registered mobile'
    // OTP is stored in session only — JS checks data.status
]);
```

> **Checklist**: Also verify session stores OTP before sending SMS:
> ```php
> $this->session->set_userdata('stock_issue_otp', $otp);
> $this->session->set_userdata('stock_issue_otp_exp', time() + 300); // 5 min expiry
> ```

---

### R-10 — Missing Rollback in OTP Verify Save Path
**File**: `admin_ret_stock_issue.php` — `stock_issue_verify_otp()` (or save path after OTP)

```php
// ❌ WHAT EXISTS — trans_begin() with no rollback on OTP verify failure
$this->db->trans_begin();
// ... save stock issue ...
if (otp_check_fails) {
    echo json_encode(['status' => false]); // No rollback!
    return;
}

// ✅ FIX — Add rollback
if (otp_check_fails) {
    $this->db->trans_rollback();
    echo json_encode(['status' => false, 'msg' => 'OTP verification failed']);
    return;
}
```

---

### R-11 — Hardcoded 3% GST (Wrong Challan Amount)
**File**: `ret_stock_issue_model.php` — `generate_challan_data()` / print functions

```php
// ❌ WHAT EXISTS
$gst_amount = $item_value * 0.03;  // Hardcoded 3%

// ✅ FIX — Fetch from tax group
$tax_rate = $this->get_tax_rate($item['tgrp_id']); // Use existing helper
$gst_amount = $item_value * ($tax_rate / 100);
```

> **Note**: Run DB_VERIFY query §5.1 first to confirm which categories have tax != 3%. Fix the query first, then verify challan PDFs match.

---

### R-15 — SQL Injection: `id_branch` in `get_StockIssuedItems`
**File**: `ret_stock_issue_model.php`

```php
// ❌ WHAT EXISTS
$sql = "SELECT ... FROM ret_stock_issue WHERE id_branch=".$id_branch;

// ✅ FIX
$this->db->where('id_branch', (int)$id_branch);
$result = $this->db->get('ret_stock_issue');
```

---

### R-16 — SQL Injection: Loop-sourced `tag_id` in batch save
**File**: `ret_stock_issue_model.php` — inside foreach loop

```php
// ❌ WHAT EXISTS
foreach ($tag_ids as $tag_id) {
    $sql = "UPDATE ret_taging SET tag_status=7 WHERE tag_id=".$tag_id;
    $this->db->query($sql);
}

// ✅ FIX
foreach ($tag_ids as $tag_id) {
    $this->db->where('tag_id', (int)$tag_id);
    $this->db->update('ret_taging', ['tag_status' => 7]);
}
// OR better — batch update with WHERE IN:
$clean_ids = array_map('intval', $tag_ids);
$this->db->where_in('tag_id', $clean_ids);
$this->db->update('ret_taging', ['tag_status' => 7]);
```

---

### R-22 — XSS via Server Message in OTP Modal
**File**: `admin/assets/js/ret_stock_issue.js` (or related JS)  
**Location**: JS `success` callback of `stock_issue_sendotp` AJAX

```javascript
// ❌ WHAT EXISTS — R-22 (raw server message injected into DOM)
$(".otp_alert").append('<p style="color:green">' + data.msg + '</p>');

// ✅ FIX — Use .text() to prevent XSS
$('<p>').css('color', 'green')
        .text(data.msg)          // .text() escapes HTML
        .appendTo('.otp_alert');
```

> **Quick scan**: Search JS for all `.append(` usages that contain server-sourced data and replace with `.text()`.

---

## 🟠 Medium Bugs (Fix in Next Sprint)

---

### R-04 — OTP Profile Check Reads Wrong Session Key
**File**: `admin_ret_stock_issue.php`

```php
// ❌ WHAT EXISTS — reads generic 'profile' session 
$profile = $this->session->userdata('profile');
$otp_req = $profile['stock_issue_otp_req'] ?? 0;

// ✅ FIX — Fetch from DB using session uid and confirm column name
$profile_data = $this->some_model->get_profile_by_employee($this->session->userdata('uid'));
$otp_req = $profile_data['stock_issue_otp_req'] ?? 0;
```

---

### R-05 — Race Condition in `generateIssueNo()`
**File**: `ret_stock_issue_model.php`

```php
// ❌ WHAT EXISTS — MAX() without lock (two requests get same MAX)
$sql = "SELECT MAX(id_stock_issue) as lastId FROM ret_stock_issue WHERE id_branch=".$id_branch;
$last = $this->db->query($sql)->row()->lastId;
$new_no = $this->format_issue_no($last + 1);

// ✅ FIX — Add SELECT FOR UPDATE (within transaction)
// Must be called inside trans_begin() block
$sql = "SELECT MAX(id_stock_issue) as lastId FROM ret_stock_issue WHERE id_branch=? FOR UPDATE";
$last = $this->db->query($sql, [$id_branch])->row()->lastId;
$new_no = $this->format_issue_no(($last ?? 0) + 1);

// ALSO: Add UNIQUE constraint on issue_no column:
// ALTER TABLE ret_stock_issue ADD UNIQUE KEY uq_issue_no (issue_no);
```

---

### R-06 — N+1 Query: `get_StockIssuedItems` Loop
**File**: `ret_stock_issue_model.php`

```php
// ❌ WHAT EXISTS — 1 query per issue row to get tags
foreach ($issues as $issue) {
    $issue['tags'] = $this->get_issue_tags($issue['id_stock_issue']); // 1 query each
}

// ✅ FIX — Collect all IDs, single IN query, merge in PHP
$issue_ids = array_column($issues, 'id_stock_issue');
$all_tags = $this->get_tags_by_issue_ids($issue_ids); // 1 query with WHERE IN

// In get_tags_by_issue_ids():
$this->db->where_in('id_stock_issue', $issue_ids);
$tags = $this->db->get('ret_stock_issue_detail')->result_array();
// Group by id_stock_issue:
$tag_map = [];
foreach ($tags as $tag) {
    $tag_map[$tag['id_stock_issue']][] = $tag;
}
// Merge back:
foreach ($issues as &$issue) {
    $issue['tags'] = $tag_map[$issue['id_stock_issue']] ?? [];
}
```

---

### R-07 — `$issue_date` Undefined in Receipt Branch
**File**: `admin_ret_stock_issue.php` — `stock_issue('save')` method

```php
// ❌ WHAT EXISTS
if ($issue_receipt_type == 1) {
    $issue_date = date("Y-m-d H:i:s"); // Only set in type=1 branch
}
// ... later used in both branches:
$logData['issue_date'] = $issue_date; // PHP Notice in type=2/3

// ✅ FIX — Initialize before the if/else
$issue_date = date("Y-m-d H:i:s"); // Default for receipt branches
if ($issue_receipt_type == 1) {
    $issue_date = date("Y-m-d H:i:s"); // Can be customized per issue type
}
```

---

### R-12 — No Input Validation on `nt_data` JSON 
**File**: `admin_ret_stock_issue.php` — NonTag save path

```php
// ❌ WHAT EXISTS — json_decode with no error check
$nt_items = json_decode($_POST['nt_data'], true);
foreach ($nt_items as $item) { /* ... */ }

// ✅ FIX — Validate JSON decode result
$nt_items = json_decode($_POST['nt_data'] ?? '[]', true);
if (json_last_error() !== JSON_ERROR_NONE || !is_array($nt_items)) {
    echo json_encode(['status' => false, 'message' => 'Invalid form data']);
    return;
}
```

---

### R-13 / R-14 — SQL Injection: `id_stock_issue` / `type_issue` in Receipt Path
**File**: `ret_stock_issue_model.php` — Receipt save methods

```php
// ❌ WHAT EXISTS  
$sql = "SELECT * FROM ret_stock_issue WHERE id_stock_issue=".$id_stock_issue;

// ✅ FIX  
$this->db->where('id_stock_issue', (int)$id_stock_issue);
$result = $this->db->get('ret_stock_issue');
```

---

### R-17 / R-18 — Undefined Variables on Edge-Case Paths
**File**: `admin_ret_stock_issue.php`

```php
// ❌ WHAT EXISTS — $issued_to / $issued_type undefined for stock_type=1 path
// Used further down in logging

// ✅ FIX — Initialize all variables before the branching switch
$issued_to   = '';
$issued_type = '';
$karigar     = '';
$cus_id      = '';
// Then assign inside the appropriate if/switch branches
```

---

### R-19 — SQL Injection: `id_branch` in Print Methods
**File**: `ret_stock_issue_model.php` — `get_challan_data()` / print query

```php
// ❌ WHAT EXISTS
$sql = "SELECT ... WHERE id_branch=".$id_branch." AND id_stock_issue=".$id_stock_issue;

// ✅ FIX
$this->db->where('id_branch', (int)$id_branch);
$this->db->where('id_stock_issue', (int)$id_stock_issue);
$result = $this->db->get('ret_stock_issue');
```

---

### R-20 — N+1 in Print Detail Loop
**File**: `ret_stock_issue_model.php` — `get_issue_print_detail()`

```php
// ❌ WHAT EXISTS — stone details fetched per tag inside print loop
foreach ($tags as $tag) {
    $tag['stone_details'] = $this->get_stone_details($tag['tag_id']); // Per-row query
}

// ✅ FIX — same pattern as R-06: collect IDs, single IN query, merge
$tag_ids = array_column($tags, 'tag_id');
$stones = $this->get_stones_by_tag_ids($tag_ids);
$stone_map = [];
foreach ($stones as $s) { $stone_map[$s['tag_id']][] = $s; }
foreach ($tags as &$tag) { $tag['stone_details'] = $stone_map[$tag['tag_id']] ?? []; }
```

---

### R-23 — Synchronous AJAX Blocks UI Thread
**File**: `admin/assets/js/ret_stock_issue.js`  
**Location**: `stock_issue_sendotp` and `stock_issue_verify_otp` AJAX calls (~L1527, L1719)

```javascript
// ❌ WHAT EXISTS (deprecated — freezes the browser tab)
$.ajax({
    url: ...,
    async: false,   // ← REMOVE THIS
    success: function(data) { ... }
});

// ✅ FIX — Standard async (default is already async:true)
$.ajax({
    url: base_url + 'admin_ret_stock_issue/stock_issue_sendotp',
    type: 'POST',
    data: formData,
    success: function(data) {
        var res = JSON.parse(data);
        if (res.status) {
            showOtpModal();
        } else {
            showError(res.msg);
        }
    },
    error: function() {
        showError('Network error. Please try again.');
    }
});
```

---

## 🟡 Low / Cleanup Bugs

---

### R-08 — Remove Unused Model Reference
**File**: `admin_ret_stock_issue.php` — `__construct()`

```php
// ❌ WHAT EXISTS — loads a model that's never called
$this->load->model('ret_some_unused_model');

// ✅ FIX — Simply delete the load line
// Confirm it's unused: grep for 'ret_some_unused_model' across controller first
```

---

### R-21 — Stale `console.log` Debug Artifacts
**File**: `admin/assets/js/ret_stock_issue.js`

```bash
# Find all instances:
grep -n "console.log" admin/assets/js/ret_stock_issue.js

# Remove or replace with controlled logging:
# If you need debug logging, use a flag:
# if (window.DEBUG_MODE) { console.log(...); }
```

Found locations (R7 audit): **3 instances** — remove all before release.

---

### R-24 — `console.log` in Other JS Files
Same pattern as R-21 — apply grep and remove across all Stock Issue JS.

---

### R-25 (View) — Flash Message Unescaped in `form.php`
**File**: `admin/application/views/ret_stock_issue/form.php` L91

```php
// ❌ WHAT EXISTS (low-risk since flash data is server-controlled, but should be consistent)
<?php echo $message['message']; ?>

// ✅ FIX — Use htmlspecialchars for consistency
<?php echo htmlspecialchars($message['message'], ENT_QUOTES, 'UTF-8'); ?>
```

---

### R-26 (View) — Duplicate DOM ID `searchEstiAlert` in `form.php`
**File**: `admin/application/views/ret_stock_issue/form.php` L437 + L707

```php
// ❌ WHAT EXISTS — same id on two elements (Issue section L437 + Receipt section L707)
<p id="searchEstiAlert" class="error" ...></p>   // L437 (Issue)
// ... 270 lines later ...
<p id="searchEstiAlert" class="error" ...></p>   // L707 (Receipt)

// ✅ FIX — Unique IDs per section
<p id="issue_searchEstiAlert" class="error" ...></p>    // L437
<p id="receipt_searchEstiAlert" class="error" ...></p>  // L707
```

**Also update all JS selectors that reference `#searchEstiAlert`:**
```javascript
// Before: $('#searchEstiAlert').text(msg);
// After — inside Issue scan callback:
$('#issue_searchEstiAlert').text(msg);
// After — inside Receipt scan callback:
$('#receipt_searchEstiAlert').text(msg);
```

> **Impact**: Without this fix, estimation-scan error messages in the Receipt section are silently
> swallowed — the error appears in the Issue section (already scrolled past) or not at all.

---

## Pre-Fix Checklist

Before starting fixes, run these DB_VERIFY queries to confirm impact:

| Query | Bug | Run in |
|---|---|---|
| §1.3 — SHOW INDEX issue_no | R-05 | phpMyAdmin |
| §2.5 — NULL dates in taging_status_log | R-07 | phpMyAdmin |
| §3.2 — Recent OTPs visible in plaintext | R-09 | phpMyAdmin |
| §5.1 — Tax rates per category | R-11 | phpMyAdmin |
| §6.1 — Count issues (N+1 severity) | R-06/20 | phpMyAdmin |

---

## Fix Sequence (Recommended)

```
Sprint 1 (Critical — do now):
  R-01, R-09, R-10  → Transaction + OTP data leak
  R-22               → XSS in OTP modal

Sprint 2 (SQLi — systematic sweep):
  R-02, R-03, R-15, R-16, R-13, R-14, R-19

Sprint 3 (Medium — stability):
  R-05, R-06, R-07, R-11, R-12, R-17, R-18, R-20, R-23

Sprint 4 (Cleanup):
  R-04, R-08, R-21, R-24, R-25, R-26
```

---

> Brain build complete — **26 bugs**, 14 rounds, 16 docs.  
> Start fixing with `/fix-single-bug R-01` 🚀
