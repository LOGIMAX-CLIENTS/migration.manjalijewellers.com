# Customer Module — Business Rules

> **Brain Updated:** 2026-03-17 | **Round:** 1

---

## RULE-CUS-001: Customer Creation Limit

**Rule:** If `chit_settings.limit_cust == 1`, then customer creation is gated by `cust_max_count`. If `customer.count >= limit`, creation is blocked.

**Formula:**
```
IF limit_cust == 1:
    IF customer_count() >= cust_max_count:
        BLOCK → redirect with error message
    ELSE:
        ALLOW → show Add form
ELSE:
    ALLOW unconditionally
```

**Implementation:** `cus_form('Add')` L157–187 (PHP controller)  
**Validation:** Server-side only  
**Edge case:** Count is total across all branches — branch-level limits not supported

---

## RULE-CUS-002: Customer Type (Individual vs Company)

**Rule:** `cus_type` controls form behavior:
- `cus_type = 1` → Individual: firstname + lastname shown
- `cus_type = 2` → Company: `company_name` = firstname value; address.company_name populated

**Formula:**
```
IF cus_type == 2:
    address.company_name = $cus['firstname']    // Company name stored as firstname
ELSE:
    address.company_name = NULL
```

**Implementation:** `cus_post('Add')` L500 (PHP), `customer.js` (form toggle JS)  
**Validation:** Client-side toggle (CSS show/hide) + server-side conditional

---

## RULE-CUS-003: Branch Entry Date Override

**Rule:** In some configurations, the branch has a "custom entry date" that overrides `date_add` (today's date) for customer creation. This supports back-dated entries.

**Formula:**
```
IF branch.edit_custom_entry_date == 1:
    customer.custom_entry_date = ret_day_closing.entry_date (for that branch)
ELSE:
    customer.custom_entry_date = NULL
    customer.date_add = NOW()
```

**Implementation:** `cus_post('Add')` L432–445 + `customer_model::get_entrydate()` L500–507  
**Reads:** `ret_day_closing JOIN chit_settings WHERE id_branch={id_branch}`  
**Validation:** Server-side only

---

## RULE-CUS-004: Multiple Chit Allowance

**Rule:** Controls which customers appear in the scheme-join customer dropdown:
- `chit_settings.allow_join_multiple == 1` → ALL active customers shown
- `allow_join_multiple == 0` → Only customers with NO active/closed scheme account shown

**Formula:**
```
IF allow_multiple_chit == 1:
    SELECT all active customers
ELSE:
    SELECT customers NOT IN (scheme_account WHERE active=1 OR is_closed=1)
```

**Implementation:** `ajax_get_customers()` L54–66 controller, `ajax_get_all_customers()` / `ajax_get_unallocated_customers()` model  
**Validation:** Server-side filter in SQL

---

## RULE-CUS-005: Password Encryption

**Rule:** Customer portal passwords are "encrypted" using base64 encoding (NOT actual cryptographic hashing).

**Formula:**
```
Store: base64_encode($password)   → __encrypt()
Read:  base64_decode($hashed_pwd) → __decrypt()
```

**Implementation:** `customer_model::__encrypt()` L13–16, `__decrypt()` L18–20  
**⚠️ Security risk:** base64 is trivially reversible — not real encryption  
**Validation:** None — raw base64 stored and compared

---

## RULE-CUS-006: Wallet Account Creation

**Rule:** When creating a new customer, if `chit_settings.wallet_account_type == 1`, a wallet (inter-wallet) account is auto-created for the customer at registration time.

**Formula:**
```
customer_settings = settingsDB('get', '', '')
IF settings[0]['wallet_account_type'] == 1:
    wallet_account_create($cus_id, $mobile)
    → INSERT wallet_account
    → Send SMS + Email via service_id = 8
```

**Implementation:** `cus_post('Add')` L519–522, `wallet_account_create()` L1769–1834  
**SMS Service ID:** 8 (Wallet Welcome)  
**Validation:** Server-side; no rollback if wallet creation fails but customer already inserted

---

## RULE-CUS-007: KYC Document Types

**Rule:** Three primary KYC document types stored in `kyc` table via `kyc_type` field:

| kyc_type | Document | Unique per customer? |
|---|---|---|
| 1 | Bank Passbook + Cheque | Multiple allowed (one per bank account) |
| 2 | PAN Card | One per customer |
| 3 | Aadhar Card | One per customer |

**Formula:**
```
IF pan_number OR pan_front/back image:
    INSERT kyc (kyc_type=2, id_customer, img_url, back_img_url, number)
IF aadhar_number OR aadhar front/back image:
    INSERT kyc (kyc_type=3, id_customer, img_url, back_img_url, document_url)
FOR EACH bank in bank_details[]:
    INSERT kyc (kyc_type=1, number=acc_no, name=holder, bank_name, bank_ifsc, img_url, cheque_img)
```

**Implementation:** `cus_post('Add')` L537–640  
**Validation:** Client-side optional; server-side no hard requirement — all KYC is optional

---

## RULE-CUS-008: Branch-wise Customer Visibility

**Rule:** Customer list filtered based on branch_settings:

```
IF uid IN (1,2):   // Super admin
    SHOW all customers [optionally filtered by id_branch if provided]
ELSE IF branch_settings == 1:
    IF id_branch filter provided:
        SHOW customers WHERE id_branch = filter
    ELSE:
        SHOW customers WHERE id_branch = session.id_branch OR show_to_all=2
ELSE:
    SHOW all customers (no branch filter)
```

**Implementation:** `customer_model::get_all_customers()` L72–75  
**Validation:** Server-side ONLY — client has no control

---

## RULE-CUS-009: ERP Auto-Sync on Registration (integrationType=3)

**Rule:** When `integrationType==3` OR `autoSyncExisting==1` is configured, the system checks if an offline customer with the same mobile already exists in `customer_reg` staging table. If found, scheme accounts and payments are transferred to online system.

**Formula:**
```
IF integrationType==3 OR autoSyncExisting==1:
    insExisAcByMobile(mobile, id_customer, id_branch)
    → For each unregistered offline plan:
        IF scheme_account already exists: link it
        ELSE: INSERT scheme_account from offline data
    syncPayData(scheme_accounts)
    → For each unsynced transaction: INSERT payment
    updateInterTableStatus()
    → Mark customer_reg + transaction as transferred
```

**Implementation:** `sync_existing_data()` L393–417, model methods L612–778  
**Validation:** Server-side; fails silently if synced records not found (returns error array but no UI alert in Add flow)

---

## RULE-CUS-010: Customer Status Controls

**Rule:** Two separate status fields:
- `customer.active` — Active/Inactive customer (toggleable via list action)
- `customer.profile_complete` — Profile completion flag (1=complete, 0=incomplete)

Both are toggled via **GET requests** — no CSRF protection.

**Formula:**
```
/customer/profile_status/{status}/{id}:
    UPDATE customer SET profile_complete = {status}

/customer/customer_status/{status}/{id}:
    UPDATE customer SET active = {status}
```

**Implementation:** `profile_status()` L1733–1744, `customer_status()` L1745–1756  
**⚠️ CSRF risk:** GET-based state changes

---

## RULE-CUS-011: Customer Delete Guard

**Rule:** Customer can only be deleted if ZERO active dependencies exist across 5 tables.

**Dependency Check:**
```
scheme_account WHERE id_customer=$id AND active=1 AND is_closed=0  → "Active Scheme Accounts"
ret_billing WHERE bill_cus_id=$id AND bill_status<>-1              → "Billing Records"
customerorder WHERE order_to=$id AND order_status<>-1              → "Customer Orders"
ret_estimation WHERE cus_id=$id                                    → "Estimation Records"
gift_card WHERE purchased_by=$id                                   → "Gift Voucher / Card Purchases"
```

If ANY check returns rows → block delete, show specific dependency message.

**Implementation:** `check_customer_dependencies()` model L366–395, `ajax_check_delete()` controller L108–125  
**Validation:** Server-side pre-check via AJAX; actual delete is a second GET request

---

## RULE-CUS-012: Account Number Formatting

**Rule:** Scheme account numbers and receipt numbers are auto-generated based on configured format templates stored in `chit_settings`. Format codes include branch short code, year, sequence number padding.

**Implementation:** `customer_model::format_accRcptNo()` L835–879, `accountFrmt()` L880–956, `receiptFrmt()` L957–1014, `getFormatedNumber()` L1062–1120  
**Reads:** `chit_settings`, `branch`, `scheme_account`, `payment`  
**Validation:** Server-side number generation
