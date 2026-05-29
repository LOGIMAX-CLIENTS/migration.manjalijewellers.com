# INVARIANT MATRIX — chit_customer_app
> Round 1 — 2026-03-16

---

## Dimension 1: Authentication & Security Invariants

| # | Invariant | Type | Risk if Violated |
|---|---|---|---|
| INV-001 | `customer.passwd` is ALWAYS stored as `base64_encode(plain_text)` — never bcrypt, never MD5 | Config | 🔴 P0 — Password exposure on DB leak |
| INV-002 | `authenticate_post` ALWAYS checks `active=1` before returning valid login | Code | 🔴 P0 — Inactive customers could login if check removed |
| INV-003 | OTP is ALWAYS stored in `customer.last_generated_otp` — not a separate OTP table | Design | 🟡 MED — OTP can be read by anyone with `SELECT` on customer |
| INV-004 | ALL delete/update actions accept `id_customer` from POST body — no session verification | Design | 🔴 P0 — IDOR vulnerability across all write endpoints |
| INV-005 | CORS is ALWAYS `Access-Control-Allow-Origin: *` (set at file level, L3) | Config | 🔴 P0 — Cannot restrict origins without removing this |
| INV-006 | `__encrypt` / `__decrypt` ALWAYS use `base64` — changing this breaks existing passwords | Code | 🔴 HIGH — Migration needed if changing to bcrypt |

---

## Dimension 2: Payment State Machine Invariants

| # | Invariant | Type | Risk if Violated |
|---|---|---|---|
| INV-010 | Payment ALWAYS created with `payment_status=7` (pending) BEFORE gateway call | Code | 🔴 — No DB record = payment history lost on gateway success |
| INV-011 | `payment_status` only goes: `7→2→1` (success) OR `7→3` (fail) OR `7→4` (cancel) | Business | 🔴 — Out-of-order updates (e.g., 7→1 direct) skip "awaiting" state |
| INV-012 | `insert_common_data` is ALWAYS called after gateway confirms success — never on failure | Code | 🔴 — Receipt/SMS/referral triggered on wrong status = incorrect records |
| INV-013 | `receipt_no` is ALWAYS generated via `payment_modal->get_receipt_no()` — never manually set | Code | 🟡 — Duplicate receipt numbers possible if called concurrently |
| INV-014 | Multi-chit payment validation: `redeemed_amount + amount + sum(discounts) == sum(pay_arr.pay_amt)` MUST be equal. Any mismatch = rejection | Business | 🔴 — Float precision failures at boundary values |
| INV-015 | Wallet deduction ALWAYS happens before gateway call (not after success) | Code | 🔴 — Gateway failure after wallet debit = balance lost |
| INV-016 | `added_by=2` in payment ALWAYS = Customer Mobile App (for reporting differentiation from admin) | Business | 🟡 — Admin reports filter by added_by — wrong value = wrong attribution |

---

## Dimension 3: Gateway Integration Invariants

| # | Invariant | Type | Risk if Violated |
|---|---|---|---|
| INV-020 | Gateway credentials ALWAYS fetched per-branch from `payment_gateway` table — never hardcoded (except legacy PayU consts PAYU_KEY/PAYU_SALT) | Design | 🔴 — Hardcoded keys exposed in PHP source |
| INV-021 | Gateway selection ALWAYS based on `gateway` + `pg_code` params from mobile payload — server does not validate if gateway is active/enabled for branch | Design | 🟡 — Customer could select an inactive gateway |
| INV-022 | ALL gateway callbacks log to `log/{date}/cashfree/` — even non-Cashfree gateways | Code | 🟡 LOW — Misleading log path name for non-Cashfree gateways |
| INV-023 | Signature verification ALWAYS happens in callback before updating payment status | Code | 🔴 CRITICAL — Removal = payment status can be manipulated without valid gateway call |
| INV-024 | `old_*` callback methods (old_cashfreeResponse, oldrazorResponse) are still routable — `cashfreeResponse_post` delegates to them | Design | 🟡 — Old method bugs affect current gateway flow |

---

## Dimension 4: SMS Gateway Invariants

| # | Invariant | Type | Risk if Violated |
|---|---|---|---|
| INV-030 | SMS gateway is ALWAYS selected via `$this->config->item('sms_gateway')` config | Config | 🟡 — Only ONE gateway active at a time |
| INV-031 | SMS send is ALWAYS done via `if/elseif` chain (1→5) — no gateway abstraction | Code | 🟡 — Adding new gateway = touching every SMS send point |
| INV-032 | OTP SMS uses service ID `23` — hardcoded in `generateOTP_get` | Code | 🟡 — Changing this in DB without updating code = OTP SMS broken |
| INV-033 | WhatsApp messages ALWAYS checked via `$service['serv_whatsapp']` flag before sending | Code | ✅ Correct guard — but no fallback if WhatsApp fails |

---

## Dimension 5: Scheme Join Invariants

| # | Invariant | Type | Risk if Violated |
|---|---|---|---|
| INV-040 | New scheme join (`is_new='Y'`) ALWAYS wrapped in `trans_begin()` / `trans_commit()` | Code | 🔴 — Partial join without rollback = orphan scheme_account |
| INV-041 | Existing scheme join (`is_new='N'`) is NOT in a transaction — `join_existing` goes directly to `scheme_reg_request` | Code | 🟡 — Duplicate join requests possible |
| INV-042 | ALWAYS three gates checked before join: `allowNewscheme_join` + `allowMultipleChits` + `allowUnpaid` | Business | 🔴 — Skipped gate = customers join when they shouldn't |
| INV-043 | `added_by=2` ALWAYS on scheme_account created via mobile (customer) | Business | 🟡 — Wrong value misattributes accounts in admin |
| INV-044 | `start_year` for scheme_account ALWAYS derived from `payment_modal->get_financialYear()` — never from client | Business | ✅ Correct — enforced on server |

---

## Dimension 6: Data Integrity Invariants

| # | Invariant | Type | Risk if Violated |
|---|---|---|---|
| INV-050 | `customer.mobile` is the ONLY login credential — no username-based login | Business | 🔴 — Mobile change (not currently supported in app) would break all logins |
| INV-051 | `api/rate.txt` MUST exist and contain valid JSON for scheme pages to function | File | 🔴 — If rate file missing/corrupted, ALL metal rate data in app is dark |
| INV-052 | `getMatchingCountry`, `getMatchingState`, `getMatchingCity` use `LIKE '$char%'` — raw input in SQL | Code | 🔴 P1 — SQL injection via typeahead search endpoint |
| INV-053 | `get_customerByMobile($mobile)` uses raw `$mobile` in SQL string | Code | 🔴 P1 — SQL injection direct from login payload |
| INV-054 | Payment history pagination uses `id_wallet_transaction < $id_wallet_trans` — cursor-based | Design | 🟡 — Deleting wallet transactions would break pagination |

---

## Dimension 7: Configuration-Driven Behavior

| Config Key | Source | Behavior When Set | Behavior When Unset/0 |
|---|---|---|---|
| `integrationType` | `config/` | `=2` → sync to third-party system on every login/register | No sync |
| `sms_gateway` | `config/` | Selects SMS provider (1-5) | No SMS (falls through all elseif) |
| `chit_settings.branch_settings` | DB | `=1` → branch-filtered operations | Branch NULL everywhere |
| `chit_settings.is_branchwise_cus_reg` | DB | `=1` → customer's branch auto-assigned to account | Asks or posts branch |
| `chit_settings.branchwise_scheme` | DB | `=1` → schemes filtered per branch | All schemes shown |
| `chit_settings.allow_referral` | DB | `=1` → referral code UI shown to customer | No referral |
| `chit_settings.useWalletForChit` | DB | `=1` → wallet use allowed in payment | Wallet disabled |
| `chit_settings.is_pin_required` | DB | `=1` → MPIN enforced after login | No MPIN required |
| `chit_settings.is_kyc_required` | DB | `=1` → KYC required prompt shown (client-side only!) | No KYC prompt |
| `chit_settings.kyc_approval` | DB | `=1` → KYC must be approved before use | KYC optional |
| `chit_settings.allow_new_scheme_join` | DB | `=0` → ALL new scheme joins blocked | Joins allowed |
| `chit_settings.allowMultipleChits` | DB | `=FALSE` → one active account per customer | Multiple allowed |
| `chit_settings.wallet_account_type` | DB | `=1` → auto-create wallet on registration | No auto-wallet |
| `chit_settings.is_branchwise_rate` | DB | `=1` → metal rate shown per branch | Single global rate |
| `chit_settings.reg_existing` | DB | `=1` → existing scheme join option shown | New only |
| `chit_settings.regExistingReqOtp` | DB | `=1` → OTP required for existing join | No OTP for existing join |
| `chit_settings.maintenance_mode` | DB | `=1` → maintenance mode on (checked client-side) | Normal operation |
| `configuration.show_language` | DB | Shows language selector in app | No language selector |
