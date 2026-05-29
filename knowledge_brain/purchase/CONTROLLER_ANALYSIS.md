# PURCHASE MODULE — CONTROLLER ANALYSIS & ACCESS CONTROL AUDIT
> **Round:** 6 | **Date:** 2026-02-23 | **Focus:** Access control, file uploads, method complexity, dead code

---

## 🔴 Access Control Audit

### Pattern: Access Checked on LIST/VIEW Only — Never on SAVE/UPDATE/DELETE

47 `get_access()` calls found — **ALL on list/view page rendering, ZERO on mutation paths.**

| Sub-Module | List/View Access Check | Save Access Check | Risk |
|---|---|---|---|
| Order Description | ✅ L266, L472 | ❌ | Any user can add/delete |
| Purchase Entry | ✅ L545, L558 | ❌ | Any user can save bill |
| QC Issue/Receipt | ✅ L3829, L3870 | ❌ | Any user can issue/receive QC |
| HM Issue/Receipt | ✅ L4417, L4476 | ❌ | Any user can process HM |
| Lot Generate | ✅ L3766, L3778 | ❌ | Any user can generate lots |
| Supplier Payment | ✅ L5334, L6079 | ❌ | Any user can save payments |
| Rate Fixing | ✅ L6601, L6613 | ❌ | Any user can fix rates |
| Purchase Return | ✅ L7540, L7771 | ❌ | Any user can return items |
| Metal Issue | ✅ L8776, L9354 | ❌ | Any user can issue metal |
| GRN Entry | ✅ L9468, L10549 | ❌ | Any user can save GRN |
| Approval Stock | ✅ L2259, L2291 | ❌ | Any user can manage approvals |
| Supplier Rate Cut | ✅ L11453, L11706 | ❌ | Any user can save rate cuts |
| Smith Op Bal | ✅ L12308, L12446 | ❌ | Any user can set balances |
| NonTag Receipt | ✅ L12794, L12948 | ❌ | Any user can receive non-tag |
| Credit/Debit | ✅ L13014, L13228 | ❌ | Any user can create CR/DR |

**Impact:** Access rights are read and sent to the view (for showing/hiding buttons), but the save/update/delete controller actions **never verify** that the user has permission. A user with read-only access can directly POST to save endpoints.

---

## 🔴 File Upload Security

### `base64ToFile()` — L202-228
```php
public function base64ToFile($imgBase64){
    $data = base64_decode(preg_replace('#^data:image/\\w+;base64,#i', '', $imgBase64));
    $temp_file_path = tempnam(sys_get_temp_dir(), 'tempimg');
    file_put_contents($temp_file_path, $data);         // ← Writes any data to disk
    $image_info = getimagesize($temp_file_path);
    $imgFile = array(
         'name' => uniqid().'.'.preg_replace('!\\w+/!', '', $image_info['mime']),
         'tmp_name' => $temp_file_path,
         // ...
    );
    return $imgFile;
}
```

| Check | Present? | Risk |
|---|---|---|
| File extension whitelist | ❌ | Arbitrary file types |
| File size limit | ❌ | DoS via large files |
| MIME type validation | ⚠️ `getimagesize()` | Can be bypassed with polyglot files |
| Content scanning | ❌ | Malware upload possible |
| Output filename sanitization | ✅ `uniqid()` | Good — prevents path traversal |

### `upload_img()` — L140-198
Uses `getimagesize()` to verify image (good), then re-saves as JPEG via `imagejpeg()` (strips embedded code). **But** `base64ToFile()` writes raw data to temp BEFORE this check runs.

### Upload Locations (5 `mkdir 0777`)
| Line | Path | Issue |
|---|---|---|
| 870 | Purchase order images | World-writable |
| 2429 | Bill entry images | World-writable |
| 2625 | `vendor_ack` folder | World-writable |
| 9617 | GRN entry images | World-writable |
| 10105 | Purchase entry images | World-writable |

---

## Controller Method Complexity (Top 15)

| # | Method | Lines | Sub-Modules | Risk |
|---|---|---|---|---|
| 1 | `purchase()` | **1,714** | Bill entry, edit, list, AJAX, save | 🔴 Unmaintainable |
| 2 | `grnentry()` | **1,116** | GRN add, edit, save, list | 🔴 Unmaintainable |
| 3 | `purchasereturn()` | **873** | Return save, list, edit | 🟠 Complex |
| 4 | `supplier_po_payment()` | **825** | Payment save, list, cheque print | 🟠 Complex |
| 5 | `update_halmarking_receipt()` | **655** | HM receipt update | 🟠 Complex |
| 6 | `karigarmetalissue()` | **616** | Metal issue save, list | 🟠 Complex |
| 7 | `get_supplier_sale()` | **562** | Supplier sale logic | 🟡 Large |
| 8 | `generate_lot()` | **489** | Lot generation save | 🟡 Large |
| 9 | `rate_fixing()` | **376** | Rate fix save | 🟡 Medium |
| 10 | `supplier_rate_cut()` | **361** | Rate cut save | 🟡 Medium |
| 11 | `generate_lot_from_halmarking()` | **335** | HM lot save | 🟡 Medium |
| 12 | `purchase_payment()` | **335** | Payment alternate | 🟡 Medium |
| 13 | `nontag_lot_generate()` | **284** | NonTag lot | 🟡 Medium |
| 14 | `order_description()` | **249** | Order desc CRUD | 🟢 OK |
| 15 | `send_karigar_sms()` | **241** | SMS sending | 🟢 OK |

**Total method lines in top 4 alone: 4,528 lines** (33% of entire controller).

> `purchase()` at 1,714 lines is the **largest single method** — contains 11 `case` blocks (list, add, edit, save, pur_edit, billing_edit, cancel, ajax, approval, pur_entry_save, grn_entry_save). This should be split into at least 5 separate methods.

---

## Dead/Commented Code Analysis

| File | Total Lines | Commented Lines | Percentage |
|---|---|---|---|
| Controller | 13,615 | 275 | 2.0% |
| Model | 9,053 | 160 | 1.8% |
| **Combined** | **22,668** | **435** | **1.9%** |

435 lines of commented code across both files — mostly debug statements and old query versions.

---

## Summary — Round 6 Findings

| # | Finding | Severity | Count |
|---|---|---|---|
| 1 | **Access control not checked on save/update/delete** | 🔴 CRITICAL | 15 sub-modules |
| 2 | **File upload: no extension whitelist, no size limit** | 🔴 CRITICAL | 10 upload points |
| 3 | **`mkdir 0777` on upload directories** | 🟡 MEDIUM | 5 locations |
| 4 | **`purchase()` method = 1,714 lines** | 🟠 HIGH | Top 4 = 4,528 lines |
| 5 | **435 lines of commented dead code** | 🟢 LOW | Maintenance debt |

## Cumulative Bug Patterns (Rounds 2-6)
| Round | Patterns | Key Theme |
|---|---|---|
| R2 | 4 | Retagging method bugs |
| R4 | 8 | Security (debug, $_POST, raw SQL) |
| R5 | 4 | Validation gaps, error handlers |
| R6 | 5 | Access control, uploads, complexity |
| **Total** | **21 patterns** | **850+ affected lines** |