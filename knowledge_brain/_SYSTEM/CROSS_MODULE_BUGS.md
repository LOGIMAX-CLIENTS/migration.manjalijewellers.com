# Cross-Module Bug History

> Bugs that affected 2+ modules. Learn from these to prevent future cross-module issues.
> Last updated: 2026-03-26 (Round 2 refresh)
> Source: 28 module brains | Total active: 113

## Active Cross-Module Bugs

| Bug ID | Modules Affected | Description | Severity | Status |
|---|---|---|---|---|
| XMOD-001 | Chit Reports → Payment | `cancel_payment` has no transaction wrapping — step 3 (cancel) can succeed, step 4 (log) can fail | 🔴 P0 | Open |
| XMOD-002 | Account / Payment | SMS gateway if/elseif chain duplicated 8+ times — changing one doesn't update others | 🟡 P2 | Open |
| XMOD-003 | Account / Payment | OTP returned in JSON response body (5 endpoints) — client-side readable | 🔴 P0 | Open |
| XMOD-004 | Account (L4188) / Payment (L5081) | OTP comparison uses `=` (assignment) instead of `==` — always evaluates true | 🔴 P0 | Open |
| XMOD-005 | System | `admin/log/` directory has 31 PII log files web-accessible — no `.htaccess` protection | 🔴 P0 | Open |
| XMOD-006 | Billing → Estimation | Bill save updates `ret_estimation.estbillid` — if save fails mid-transaction, estimation partially linked to nonexistent bill | 🔴 P1 | Open |
| XMOD-007 | Chit Reports | SQL injection in `get_customerenquiry_by_date` — raw `$status`/`$type` concat | 🔴 P0 | Open |
| XMOD-008 | Tagging / Billing / BT / Section Transfer / Stock Issue / Sales Transfer / Old Metal / Customer Order | `tag_status` written by 8 modules with no centralized state machine — race conditions possible | 🟡 P2 | Open — by design |
| XMOD-009 | Billing → Tagging | `get_tag_status()` check is not atomic with status update — two concurrent bills can reference same tag | 🔴 P1 | Open |
| XMOD-010 | Customer Order → Tagging | Repair save (type=4) sets `id_orderdetails` but NOT `tag_status=8`; update sets both — inconsistent | 🟡 P2 | Open |
| XMOD-011 | Old Metal → ret_taging | `tag_process=1` set at pocket creation but no rollback if pocket is cancelled/deleted | 🟡 P2 | Open |
| XMOD-012 | Masters → System | `clear_database()` reachable without OTP verification | 🔴 P0 | Open |
| XMOD-013 | Masters → Settings | SQLi in core RBAC `get_access()` (MST-BUG-002) | 🔴 P0 | Open |
| XMOD-014 | Retail Dashboard | `getLedgerReportData()` called on Reports model but method may not exist → Fatal PHP error | 🔴 P1 | Open |
| XMOD-015 | Customer Order | Email sent inside transaction (L1993) before `trans_commit()` — sent even if order rolls back | 🟡 P2 | Open |
| XMOD-016 | Customer Order | Email token not single-use — replay attack possible | 🟡 P2 | Open |
| XMOD-017 | Retail Dashboard (API) | `get_accountstock_inwards_details()` uses undefined `$data` for branch filter — always unfiltered | 🔴 P1 | Open |
| XMOD-018 | Retail Dashboard (API) | `get_rate_cut_profit_loss()` date filter inverted — `to_date` ignored | 🟡 P2 | Open |
| XMOD-019 | Catalog | SQL injection via profile settings concat in `get_profile_settings()` (L199-203) | 🔴 P1 | Open |
| XMOD-020 | Customer → ALL | Password stored as `base64_encode()` — trivially reversible. All modules that read `customer.passwd` can decode passwords | 🔴 P0 | Open |
| XMOD-021 | Payment | PCI violation: `payment.cvv` column exists and is writable — CVV must NEVER be stored (PCI-DSS requirement) | 🔴 P0 | Open |
| XMOD-022 | Payment | PCI risk: `payment.card_no`, `payment.exp_date` stored — must be masked/tokenized | 🔴 P1 | Open |
| XMOD-023 | Customer → Scheme/Chit | `syncPayData()` and `updateInterTableStatus()` have NO transaction wrapping — partial sync leaves customer_reg/transaction in inconsistent state | 🟡 P2 | Open |
| XMOD-024 | Payment | `payment.payment_amount` can diverge from `SUM(payment_mode_details.payment_amount)` — no reconciliation check | 🔴 P1 | Open |
| ORD-CLT01 | Purchase + Order | PO cancel/reject did not reset `order_cart.orderstatus` — cart items stuck at `1` | Added `order_cart` reset in `update_order_cancel()` and `update_order_rejection()` | 2026-03-26 |
| XMOD-025 | Customer → KYC | `delete_customer()` removes customer + address + wallet but NOT kyc records — orphaned KYC with PII | 🟡 P2 | Open |
| XMOD-026 | Customer Order → Finance | Cancel inserts zero-amount `ret_issue_receipt` row (AP-11 bug: `if($order_advance > 0)` on array = always true) | 🟡 P2 | Open |
| XMOD-027 | Payment | `update_payment()` has double `trans_begin` at L906 and L914 — nested transactions behave differently across DB engines | 🟡 P2 | Open |
| XMOD-028 | Sales Transfer → Billing | **ALL 17 model methods** use raw SQL concat — zero parameterized queries across entire module | 🔴 P0 | Open |
| XMOD-029 | Sales Transfer → Billing | `ret_billing.tot_bill_amount` is `decimal(10,0)` — financial amounts truncated to integers on every transfer | 🔴 P1 | Open |
| XMOD-030 | Sales Transfer → Billing | Return transfer omits `is_credit`/`credit_status` → gets DB defaults (0=non-credit, 1=paid) — should be (1=credit, 2=pending) | 🔴 P0 | Open |
| XMOD-031 | LOT → Tagging | Lot delete via GET (CSRF) doesn't reverse `ret_nontag_item` stock — permanent stock inflation | 🔴 P0 | Open |
| XMOD-032 | LOT | `debug echo last_query()` in production merge/split failure paths — SQL structure leaked | 🔴 P1 | Open |
| XMOD-033 | Estimation | `trans_commit()` called in error branch (EST-R601) — partial estimation saves permanently committed | 🔴 P0 | Open |
| XMOD-034 | Estimation | `get_child_tag_stone_details()` missing return statement — always NULL, stones silently lost | 🟡 P2 | Open |
| XMOD-035 | Scheme | Double `trans_commit()` at L966+L984 in TopUp edit — partial commits if subsequent inserts fail | 🔴 P1 | Open |
| XMOD-036 | Scheme | `delete_scheme()` only cleans 1 of 10 child tables — 9 orphaned child table groups | 🔴 P1 | Open |
| XMOD-037 | Employee → ALL | `employee.passwd` stored as base64 (same as customer XMOD-020) — trivially reversible | 🔴 P0 | Open |
| XMOD-038 | Tagging → Billing | Delete case has no `tag_status` guard — sold tag (status=1) can be soft-deleted (RISK-014) | 🔴 P0 | Open |
| XMOD-039 | Tagging | Retag nested transaction scope — outer `trans_rollback()` doesn't undo sub-method `trans_begin()` (RISK-015) | 🔴 P1 | Open |
| XMOD-040 | Tagging | No server-side validation of `sales_value` — JS-computed value trusted and stored directly | 🔴 P1 | Open |
| XMOD-041 | Stock Issue | OTP returned in API JSON response — visible in browser network tab | 🔴 P0 | Open |
| XMOD-042 | Stock Issue | XSS: server `msg` injected raw into DOM via `.append('<p>' + data.msg + '</p>')` | 🔴 P1 | Open |
| XMOD-043 | Sales Transfer → Billing | `$bill_date` = NULL due to `getAllBranchDCData()` array-of-arrays misuse — all transfer bills get NULL date | 🔴 P0 | Open |
| XMOD-044 | Sales Transfer | 3 download methods missing `trans_begin()` entirely — tag status + billing writes unprotected | 🔴 P0 | Open |
| XMOD-045 | Account | Gift OTP `=` instead of `==` at L4188 — any value accepted, OTP bypass | 🔴 P0 | Open |
| XMOD-046 | Account | ALL 5 OTP endpoints return OTP in JSON response — 5x security leak | 🔴 P0 | Open |
| XMOD-047 | Account | `manual_schemeaccount()` trans_commit INSIDE foreach loop — partial commits | 🔴 P1 | Open |
| XMOD-048 | Masters → ALL | `clear_database()` no auth/role gate — any logged-in user can wipe entire DB | 🔴 P0 | Open |
| XMOD-049 | Masters → Mobile | `rate.txt` writes "Array" not JSON — mobile app gets corrupt rate data | 🔴 P1 | Open |
| XMOD-050 | Catalog → ALL | 11 model methods with raw SQL concat — SQLi on all master data lookups | 🔴 P1 | Open |
| XMOD-051 | Purchase | `echo last_query();exit;` at L322/L452/L631 kills transaction rollback | 🔴 P0 | Open |
| XMOD-052 | Branch Transfer | Cancel does NO stock/log reversal for ANY item type (tagged, NT, OldMetal, packaging, order) | 🔴 P1 | Open |
| XMOD-053 | Old Metal → Payment | NB payment uses `cash_amount` instead of `net_banking_amount` (OMP-003) — wrong payment recorded | 🔴 P0 | Open |
| XMOD-054 | Old Metal → Lot | Polishing receipt `$id_branch` undefined — ALL lot inward records get `created_branch = NULL` | 🔴 P1 | Open |
| XMOD-055 | Old Metal | Testing receipt against-melting `melting_status` update entirely COMMENTED OUT (L744-755) | 🔴 P0 | Open |
| XMOD-056 | Old Metal → GST | `get_chg_tax_type()` compares `id_company` to `id_state` directly — GST split always wrong | 🔴 P1 | Open |
| XMOD-057 | Scheme → Account | Add scheme with `$id` undefined — `emp_closing_incentive` + `GA_benefit` insert with NULL `id_scheme` | 🔴 P0 | Open |
| XMOD-058 | Retail Dashboard | Old vs New customer widget uses IDENTICAL SQL — always shows same count (RULE-RETDASH-009) | 🔴 P1 | Open |
| XMOD-059 | Chit Collection → ALL | No centralized API auth — any caller with `id_customer` can access any customer's data | 🔴 P0 | Open |
| XMOD-060 | Chit Collection → Auth | Hardcoded OTP 123456 in production code — universal login bypass | 🔴 P0 | Open |
| XMOD-061 | Chit Customer → Wallet | Wallet debit BEFORE gateway confirmation — no rollback on gateway failure | 🔴 P0 | Open |
| XMOD-062 | Chit Customer → ALL | CORS wildcard `*` — any website can make authenticated API calls | 🔴 P0 | Open |
| XMOD-063 | Chit Reports → PII | Log directory web-exposed — 31 files with mobile, amounts, acc numbers accessible via URL | 🔴 P0 | Open |
| XMOD-064 | Employee → Address | Every edit creates DUPLICATE address record (INSERT not UPDATE) | 🔴 P1 | Open |
| XMOD-065 | Employee → Customer | Delete employee doesn't clean `customer.allocated_employee` FK — orphaned references | 🔴 P1 | Open |
| XMOD-066 | Masters → RBAC | `get_access()` returns NULL for unknown URLs — callers don't null-check → auth breakdown | 🔴 P1 | Open |
| XMOD-067 | Chit Collection → Gateway | Cashfree signature verification COMMENTED OUT — forged callbacks accepted | 🔴 P0 | Open |
| XMOD-068 | Chit Collection → Gateway | Ippo callback uses attacker-supplied credentials (BUG-031) | 🔴 P0 | Open |
| XMOD-069 | Chit Dashboard → ALL | `updateData()` accepts arbitrary table/field/id — any POST can write any table | 🔴 P0 | Open |
| XMOD-070 | Lot → Tagging | Delete lot does NOT cascade to `ret_lot_inwards_detail` — breaks 324+ Tagging queries | 🔴 P0 | Open |
| XMOD-071 | Lot → NonTag | Cancel lot does NOT reverse `ret_nontag_item` stock — quantity permanently inflated | 🔴 P1 | Open |
| XMOD-072 | Tagging → Orders | `update_order_link()` has NO tag_status guard — sold/deleted tags can be linked to orders | 🔴 P1 | Open |
| XMOD-073 | Sales Transfer → Tagging | No cancel flow — abandoned transfers leave tags permanently at `tag_status=4` (stuck) | 🔴 P1 | Open |
| XMOD-074 | Account → ALL | Delete account only restores 1/8 child tables (12.5%) — orphans KYC, gifts, wallet, payments | 🔴 P1 | Open |
| XMOD-075 | Branch Transfer → Tagging | Cancel after transit leaves tags at `tag_status=4` permanently — no reversal logic | 🔴 P0 | Open |
| XMOD-076 | Catalog → ALL | `financial_status()` has NO transaction — all fin years can go inactive, breaking system-wide lookups | 🔴 P0 | Open |
| XMOD-077 | Old Metal → ALL | 0/10+ cancel operations exist — any write error requires direct DB repair | 🔴 P1 | Open |
| XMOD-078 | Other Inventory → Billing | No status pre-check before Billing issues piece — race condition (BRN-OI-047) | 🔴 P1 | Open |
| XMOD-079 | Section Transfer → Billing | `ret_home_section_item` never reversed on billing cancel — stock permanently corrupted (BUG-ST-024) | 🔴 P0 | Open |
| XMOD-080 | Section Transfer → Home Counter | Stock direction WRONG — decrements on arrival instead of increment (BUG-ST-004) | 🔴 P0 | Open |
| XMOD-081 | Payment → ALL | Delete payment cleans 1/9 tables (11% reversal) — orphans mode_details, wallet, referral, sync | 🔴 P1 | Open |
| XMOD-082 | Scheme → ALL | Delete scheme cleans 2/11 child tables (18% reversal) — 9 orphaned child tables | 🔴 P1 | Open |
| XMOD-083 | Masters → Mobile API | `rate.txt` writes "Array" string instead of JSON — mobile rate display broken (MST-BUG-003) | 🔴 P0 | Open |
| XMOD-084 | Customer Order → JobOrder | Cancel order does NOT update `joborder.orderstatus` — stale WIP state | 🔴 P1 | Open |
| XMOD-085 | Stock Issue → Tagging | Same tag can be in 2+ simultaneous open issues — no uniqueness check on `ret_stock_issue_detail.tag_id` | 🔴 P1 | Open |
| XMOD-086 | Stock Issue → NonTag | NT deduct has no negative stock guard — quantity can go below zero | 🔴 P1 | Open |
| XMOD-087 | Customer → ALL | `passwd` stored as `base64_encode()` not bcrypt — reversible (CUS-BUG-009) | 🔴 P0 | Open |
| XMOD-088 | Account → Login | `verifyotp_gift()` uses assignment `=` instead of comparison `==` — OTP always passes | 🔴 P0 | Open |
| XMOD-089 | Account → ALL | LOCK TABLES commented out for account number generation — duplicate acc numbers in concurrent access | 🔴 P1 | Open |
| XMOD-090 | Account → SMS | Password sent in plaintext SMS via `send_login_details` (RULE-ACC-020) | 🔴 P1 | Open |
| XMOD-091 | Payment → ALL | OTP verify L5081 uses `=` (assignment) — always passes, bypasses ALL payment OTP (PAY-017) | 🔴 P0 | Open |
| XMOD-092 | Estimation → Billing | Print recalculates VA/MC in PHP — diverges from JS-calculated values stored in DB (EST-026) | 🔴 P1 | Open |
| XMOD-093 | Customer → ALL | Status toggle `profile_status()` and `customer_status()` via GET — no CSRF (CUS-010) | 🔴 P1 | Open |
| XMOD-094 | Customer Order → Vendor | Email acceptance token is reusable — no expiry or single-use enforcement (CUSORD-009) | 🔴 P1 | Open |
| XMOD-095 | Employee → Login | Username validation dead (`print_r; exit`) — duplicate usernames allowed (EMP-BUG-001) | 🔴 P0 | Open |
| XMOD-096 | Purchase → Orders | Piece count cap CLIENT-SIDE only — multiple partial entries can exceed total (PUR-005) | 🔴 P1 | Open |
| XMOD-097 | Chit Settings → KYC | `save_kyc_settings()` deletes ALL rules THEN re-inserts — failure between = all rules lost | 🔴 P1 | Open |
| XMOD-098 | Billing → POS | `cancel_bill()` does NOT call `cancelTransactionRequest()` — POS settlement mismatch (BIL-034) | 🔴 P0 | Open |
| XMOD-099 | Sales Transfer → GST | Tax rate hardcoded 3% — not fetched from config. Any GST change = wrong tax (ST-002) | 🔴 P1 | Open |
| XMOD-100 | Sales Transfer → Billing | Return bill `$tot_bill_amount` accumulates across categories — 2nd bill wrong (ST-011) | 🔴 P1 | Open |
| XMOD-101 | Branch Transfer → ALL | Cancel does NOT reverse ANY stock changes across 5 item types (BRT-012) | 🔴 P0 | Open |
| XMOD-102 | Stock Issue → ALL | OTP value returned in response — client can extract and bypass verification (SI-006) | 🔴 P0 | Open |
| XMOD-103 | Chit Collection → ALL | OTP hardcoded `123456` in `generateOTP_get()` (COL-010) | 🔴 P0 | Open |
| XMOD-104 | Masters → ALL | `clear_database()` no auth, no CSRF — any GET request wipes all data (MST-BUG-001) | 🔴 P0 | Open |
| XMOD-105 | Chit Customer App → Payment | Duplicate payment webhook processed — no idempotency guard on `update_trans` callback | 🔴 P1 | Open |
| XMOD-106 | Chit Reports → ALL | `admin/log/` web-accessible — PII in JSON logs (RPT-011) | 🔴 P0 | Open |
| XMOD-107 | Section Transfer → Tagging | OTP value returned in JSON response (ST-006 L610) | 🔴 P1 | Open |
| XMOD-108 | Masters → RBAC | `get_access()` concatenates `$url` raw into SQL (MST-BUG-002) | 🔴 P0 | Open |
| XMOD-109 | Catalog → ALL | Only 3 `set_rules` in 23K lines — all entity forms accept any POST (CAT-018) | 🔴 P1 | Open |
| XMOD-110 | Catalog → Upload | Image MIME type not checked — PHP file with .jpg extension accepted (CAT-007) | 🔴 P0 | Open |
| XMOD-111 | Chit Cust App → Payment | KYC gate CLIENT-SIDE only — `mobile_payment_post` does NOT check kyc_status (APP-011) | 🔴 P1 | Open |
| XMOD-112 | Chit Cust App → ALL | No OTP brute-force protection — 900K combos, zero lockout (APP-002) | 🔴 P1 | Open |
| XMOD-113 | Retail Settings → Compliance | `validate_cash_amt=0` — ₹2L cash payment limit NOT enforced (RSET-007) | 🔴 P1 | Open |

## Resolved Cross-Module Bugs

| Bug ID | Modules Affected | Root Cause | Fix | Date |
|---|---|---|---|---|
| RPT-INT01 | Reports (categorywise vs categorywise_stone) | Missing query in variant function | Added Branch Transfer Out status log query | 2026-03-04 |

## Cross-Module Patterns

| Pattern | Frequency | Prevention |
|---|---|---|
| No transaction wrapping on multi-step cross-module writes | 4+ occurrences | Wrap all cross-table writes in `trans_begin`/`trans_complete` |
| Multiple modules writing same status field | 8 modules write `tag_status` | Centralized state machine or at minimum, status validation before write |
| OTP bypass via assignment vs comparison | 2 occurrences | Code review checklist item + static analysis |
| SQL injection via raw parameter concat | 8+ occurrences | Use parameterized queries exclusively |
| SMS gateway duplication | 8+ copies | Extract to single shared method |
| Rate fetched at page load, not at save time | 3+ modules | Server-side rate lock at transaction time |
| PCI/security sensitive data storage | 3 columns (CVV, card_no, exp_date) + base64 passwords | Remove CVV column, tokenize card data, hash passwords |
| Hard delete without child cascade | 2+ (Customer→KYC, Payment→mode_details) | Add explicit child cleanup before parent delete |
| Type coercion causing always-true conditions | 3+ (OTP `=` vs `==`, array-to-int compare) | Strict type checking, `===` comparisons |
