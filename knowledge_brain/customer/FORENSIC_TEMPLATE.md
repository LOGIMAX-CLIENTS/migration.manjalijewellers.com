# Customer Module — Forensic Investigation Template

> **Brain Updated:** 2026-03-25 | **Round:** R3-Upgrade

---

## Layer 1 — Symptom Collection

When a customer bug is reported, collect this information first:

| # | Question | Why It Matters |
|---|---|---|
| 1 | What operation failed? (Add / Edit / Delete / Image Upload / Allocation) | Different code path for each |
| 2 | What is the `id_customer`? | Needed for DB verification queries |
| 3 | What is the `added_by` source? (Admin=1, Mobile=2, Collection=3, Import=6) | Identifies which code path created the record |
| 4 | Is the customer in a specific branch? (`id_branch`) | Branch filtering affects visibility |
| 5 | Was a KYC document attached? Which type? (PAN/Aadhar/Bank) | KYC has separate table + filesystem storage |
| 6 | Was a wallet account expected to be created? | Check chit_settings.wallet_account_type |
| 7 | Is this a new customer add or edit of existing? | Different controller method (`Add` vs `Edit`) |
| 8 | Was an agent/employee allocation attempted? | Known silently-failing bug (COL-BUG-xxx) |
| 9 | Did the user see an error message or was it silent? | Flash message vs. redirect |
| 10 | Does the issue affect one customer or all? | Single-record vs. systematic |

---

## Layer 2 — Reproduce & Isolate

**Standard reproduction checklist:**

```
□ Can you reproduce in fresh browser session? (rules out stale JS/session)
□ Is the issue branch-specific? (test with different id_branch)
□ Is the issue user-role specific? (uid=1 superadmin vs regular)
□ Does it happen for all cus_type=1 (Individual) or only cus_type=2 (Company)?
□ Is integrationType==3 active? (ERP sync affects Add flow)
□ Is wallet_account_type==1? (affects wallet creation in Add)
```

**Isolation shortcuts:**
- Temporarily add `echo json_encode($cus_data); exit;` at top of `cus_post()` to see raw POST
- Check `db->last_query()` traces — file has many `//echo $this->db->last_query();exit;` already
- Enable `$this->db->save_queries = TRUE` in CI to collect all queries

---

## Layer 3 — Client-Side Trace (JS)

**Key DOM IDs to inspect in browser DevTools → Elements:**

| Field | DOM ID / Name | Notes |
|---|---|---|
| Customer ID | `#id_customer` | Hidden field — must be present on Edit |
| Branch | `#id_branch` / `customer[id_branch]` | Session branch may override |
| Mobile | `customer[mobile]` | Availability checked via AJAX |
| Password | `customer[passwd]` | base64 sent to server |
| Webcam image | `customer[cus_img]` | JSON-encoded base64 array |
| KYC PAN | `customer[pan_img_front]`, `customer[pan_img_back]` | base64 |
| KYC Aadhar | `customer[aadhar_img_front]`, `customer[aadhar_img_back]` | base64 |
| Bank details | `customer[bank_details][]` | Array of bank objects |

**Network tab — check these AJAX calls on form submit:**
1. `/customer/check_mobile` → should return `{status: false}` if unique
2. `/customer/check_email/` → should return `{status: false}` if unique
3. `/customer/cus_post/Add` — the main POST — check response body

**Console warnings to watch for:**
- `Uncaught ReferenceError` → JS form binding failed (DOM ID mismatch)
- `$.ajax error handler` → Look for error callback in customer.js AJAX calls

---

## Layer 4 — Server-Side Trace

| Symptom | File | Method | Line | What to Check |
|---|---|---|---|---|
| Customer not saved | admin_customer.php | `cus_post('Add')` | L510–513 | Did `insert_customer()` return `$cus_id > 0`? |
| Address not saved | customer_model.php | `insert_customer()` | L254–264 | Is `$insert_flag == 1`? |
| KYC not uploaded | admin_customer.php | `store_KycImages()` | L1999–2002 | Does directory exist? file_put_contents return value |
| KYC not in kyc table | admin_customer.php | `cus_post`, L543–640 | L561 | Did `insert_kyc()` succeed? |
| Wallet not created | admin_customer.php | `wallet_account_create()` | L1787 | `wallet_accountDB('insert')` return status |
| SMS not sent | admin_customer.php | `wallet_account_create()` | L1800–1821 | Service ID 8 — is service enabled? |
| Edit not saving | admin_customer.php | `cus_post('Edit')` | L1060 | Check `trans_status()` after update |
| Image path wrong | admin_customer.php | `set_image()` / `upload_img()` | L1680 | Verify `CUS_IMG_PATH/{id}/customer.jpg` |
| Delete blocked | admin_customer.php | `ajax_check_delete()` | L115 | Run `check_customer_dependencies()` — which table has records? |
| Agent allocation fails | admin_customer.php | `allocate_agent_toCuctomers()` | L1936 | Bug: `$total = array()` — code never allocates |
| Profile update silent fail | admin_customer.php | `cus_profile('update')` | L1902 | `trans_status()` — check DB error |
| Customer not visible in list | customer_model.php | `get_all_customers()` | L72–75 | Branch filter — check session `branch_settings`, `id_branch` |

---

## Layer 5 — Database Verification

### Complete customer record dump
```sql
SELECT c.*, a.*, GROUP_CONCAT(k.kyc_type, ':', k.number SEPARATOR ' | ') as kyc_summary
FROM customer c
LEFT JOIN address a ON a.id_customer = c.id_customer
LEFT JOIN kyc k ON k.id_customer = c.id_customer
WHERE c.id_customer = {ID}
GROUP BY c.id_customer;
```

### KYC document check
```sql
SELECT id_kyc, kyc_type, number, status, verification_type, 
       LEFT(img_url, 60) as img_url_preview, 
       DATE_FORMAT(date_add, '%d-%m-%Y %H:%i') as created
FROM kyc
WHERE id_customer = {ID}
ORDER BY kyc_type, id_kyc;
```

### Wallet account check
```sql
SELECT wa.id_wallet_account, wa.wallet_acc_number, wa.available_points, wa.active,
       wa.issued_date
FROM wallet_account wa
WHERE wa.id_customer = {ID};
```

### Scheme accounts for customer
```sql
SELECT sa.id_scheme_account, s.name as scheme, sa.scheme_acc_number,
       sa.active, sa.is_closed, sa.start_date, sa.added_by,
       COUNT(p.id_payment) as payment_count
FROM scheme_account sa
JOIN scheme s ON s.id_scheme = sa.id_scheme
LEFT JOIN payment p ON p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1
WHERE sa.id_customer = {ID}
GROUP BY sa.id_scheme_account;
```

### Check if customer exists in ERP sync staging
```sql
SELECT clientid, mobile, sync_scheme_code, is_registered_online, is_transferred, 
       record_to, is_closed, reg_date
FROM customer_reg
WHERE mobile = {MOBILE};
```

### Orphan check — kyc without customer
```sql
SELECT k.id_kyc, k.id_customer, k.kyc_type
FROM kyc k
LEFT JOIN customer c ON c.id_customer = k.id_customer
WHERE c.id_customer IS NULL;
```

### Customer visibility check (branch filter debug)
```sql
-- Replace with actual session values
SELECT c.id_customer, c.firstname, c.id_branch, b.name as branch,
       b.show_to_all
FROM customer c
LEFT JOIN branch b ON b.id_branch = c.id_branch
WHERE c.id_customer = {ID};
```

---

## Layer 6 — Root Cause Classification

| Category | Examples | Risk |
|---|---|---|
| **SQL Injection** | `Searchcustomer`, `ajax_get_customers`, `isCustomerExist` | HIGH — direct user input in SQL |
| **Silent Failure** | `allocate_agent_toCuctomers` bug, wallet on error, sync on error | MEDIUM — no error surfaced |
| **File System** | KYC upload paths, 0777 permissions, no file type validation | HIGH — arbitrary file write possible |
| **Transaction Integrity** | Missing commit pair in some paths | MEDIUM — orphan partial records |
| **Password Security** | base64 "encryption" | HIGH — passwords trivially reversible |
| **CSRF** | GET-based status toggles | MEDIUM — state changed by forged link |
| **Path Traversal** | `download($id, $file)` — no path sanitization | HIGH — arbitrary file read |
| **Data Completeness** | Delete leaves kyc + logs uncleaned | LOW — orphan records in kyc |

---

## Layer 7 — File System Investigation

When KYC images are missing or wrongly uploaded:

```
1. Check server filesystem:
   ls -la admin/assets/img/customer/{id}/
   ls -la admin/assets/kyc/pan/{id}/
   ls -la admin/assets/kyc/aadhar/{id}/
   ls -la admin/assets/kyc/pb/{id}/
   ls -la admin/assets/kyc/ch/{id}/

2. Check permissions:
   stat admin/assets/kyc/pan/{id}/
   → Should be 0777 (world-readable, as coded)

3. Check kyc table for this customer:
   SELECT img_url, back_img_url, document_url FROM kyc WHERE id_customer={ID}
   → URL format: http://domain/assets/kyc/pan/{id}/pan_front.png

4. Verify URL is reachable:
   curl -I "http://yourdomain/assets/kyc/pan/{id}/pan_front.png"

5. Common failure: base64 JSON decode fails silently
   → Check customer[pan_img_front] POST field — should be JSON array with `src` key
   → Example: '[{"src":"data:image/png;base64,iVBOR..."}]'
```
