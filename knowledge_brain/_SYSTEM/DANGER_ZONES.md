# Danger Zones — High-Risk Code That Requires Extreme Caution

> These are the most dangerous areas in the codebase. ANY change here has system-wide impact.
> Last updated: 2026-03-26 (Round 13 refresh)

---

## 🔴 TIER 1 — ONE WRONG CHANGE BREAKS THE ENTIRE SYSTEM

### DZ-001: `admin_settings_model.php`
- **Why**: Loaded by EVERY controller. Methods like `get_access()`, `settingsDB()`, `getBranchDayClosingData()` are called 1000+ times per page load across all modules.
- **Specific Dangers**:
  - `get_access($url)` — SQLi risk (XMOD-013). Fixing it changes behavior for ALL permission checks
  - `settingsDB()` — Adding/removing a column from `chit_settings` query breaks every module that reads that column
  - `getBranchDayClosingData()` — Determines operational date. Wrong return = all modules use wrong date
- **Before Touching**: Grep ALL 28 controllers for every method call

### DZ-002: `chit_settings` Table (Single Row)
- **Why**: 60+ columns read by every module via `settingsDB()`. Removing or renaming any column causes PHP notice/error across entire system.
- **Before Touching**: Cross-reference with `SHARED_TABLES.md` — every module reads this.

### DZ-003: `ret_settings` Table (Name-Value Pairs)
- **Why**: 90+ settings consumed by 28+ modules. UPDATE uses `WHERE name=?` — renaming a key breaks all consumers silently (they get NULL, no error).
- **Before Touching**: Cross-reference with `retail_settings/CROSS_MODULE_MAP.md` §1 for full consumer list.

### DZ-004: `clear_database()` in Masters
- **Why**: Drops all data. Currently reachable WITHOUT OTP verification (XMOD-012).
- **Before Touching**: Add OTP gate BEFORE doing anything else.

### DZ-004b: `customer.passwd` (base64 only)
- **Why**: All customer passwords stored as `base64_encode()`. Trivially reversible. Every module that reads this field can decode passwords instantly.
- **Before Touching**: Requires full migration to `password_hash()`/`password_verify()` across customer module, login, mobile apps, and ERP sync.

### DZ-004c: `payment.cvv` / `payment.card_no` / `payment.exp_date` (PCI Violation)
- **Why**: CVV MUST NEVER be stored (PCI-DSS). Card number and expiry must be masked/tokenized. These columns have active write paths in `SaveAll()`.
- **Before Touching**: Drop `cvv` column entirely. Mask card_no to last-4 digits. Tokenize with payment gateway.

---

## 🟠 TIER 2 — CHANGES CAUSE CROSS-MODULE DATA CORRUPTION

### DZ-005: `ret_taging.tag_status` Write Logic (8 Modules)
- **Why**: 8 modules write this field with no centralized state machine. Changing status values or transition logic in one module can corrupt inventory for all others.
- **Before Touching**: Check TAG_STATUS_MAP.md for all writers, test ALL transition paths.

### DZ-006: `ret_billing_model::insertData()` / `updateData()`
- **Why**: Universal CRUD methods used by ALL modules that load billing model. Changing signature or behavior affects every caller.
- **Before Touching**: Grep for ALL callers across billing, tagging, BT, ST, customer order, purchase controllers.

### DZ-007: `payment_model::generate_receipt_no()` / `payment_model::insertData()`
- **Why**: Receipt number generation has 7 modes. Wrong sequence = duplicate receipts.
- **Before Touching**: Understand all 7 receipt modes in chit_settings.

### DZ-008: Branch Transfer Cancel Operation
- **Why**: Currently has ZERO reversal logic (CLN-001). Any "fix" that adds reversal must handle ALL 7 downstream tables atomically.
- **Before Touching**: Read HANDOFF_AUDIT.md HO-004 and CLEANUP_GAPS.md CLN-001.

### DZ-009: Estimation `trans_commit()` in Error Branch
- **Why**: Error path calls `trans_commit()` instead of `trans_rollback()` (EST-R601). CRITICAL — partial estimation saves are permanently committed.
- **Before Touching**: HANDOFF_AUDIT.md HO-003. Fix is simple but MUST be verified with full regression.

---

## 🟡 TIER 3 — CHANGES CAUSE SINGLE-MODULE BREAKAGE BUT HIGH IMPACT

### DZ-010: Billing Save Flow (~L2000-3000)
- **Why**: 15+ table writes in a single method including cross-module updates to `ret_estimation`, `ret_taging`, `ret_journal`. Power out mid-save = corrupt data across 3 modules.

### DZ-011: SMS Gateway Dispatch (8+ Copies)
- **Why**: 5-gateway if/elseif chain duplicated 8+ times. Fixing one copy doesn't fix others. Must find and update ALL copies.

### DZ-012: Tagging `create_retag()` L6755
- **Why**: Creates new lot, marks old tags as status=3, creates stock process records. No cancel/rollback path. Mid-failure = permanently corrupted tags.

### DZ-013: `ret_reports_model::getBillDetails()`
- **Why**: Called from Retail Dashboard (cross-controller). If output structure changes, dashboard cash abstract silently shows wrong numbers.

### DZ-014: Day Closing Logic
- **Why**: `getBranchDayClosingData()` determines `entry_date` for ALL modules. If day closing is not set for a branch, entry_date = NULL → INSERT failures across multiple modules.

### DZ-015: Payment Delete Operation
- **Why**: Hard delete with NO transaction wrapping and NO child cleanup. Leaves `payment_mode_details` and `payment_status` orphaned. XMOD-024 makes reconciliation impossible.
- **Before Touching**: Wrap in transaction, add child cleanup, consider soft delete instead.

### DZ-016: Customer Download (KYC)
- **Why**: `download($id, $file)` has NO path sanitization — path traversal enables reading `../../config/database.php`. Direct LFI vulnerability (CUS-BUG-002).
- **Before Touching**: Must add `basename()` and whitelist-only path resolution.

### DZ-017: Customer Sync (`syncPayData` / `updateInterTableStatus`)
- **Why**: ERP sync functions run WITHOUT transaction wrapping. Partial sync leaves `customer_reg` and `transaction` tables in inconsistent state. Affects all synced clients.
- **Before Touching**: Wrap both operations in single transaction, add rollback on any failure.

### DZ-018: Sales Transfer Model (ALL 17 Methods)
- **Why**: Every single query method in `ret_sales_transfer_model.php` uses raw string concatenation. Zero parameterized queries. Any user input in tag search, branch select, or bill lookup reaches SQL directly.
- **Before Touching**: Full model rewrite to use parameterized queries. Test ALL 17 methods.

### DZ-019: Estimation `trans_commit()` in Error Branch (EST-R601)
- **Why**: When estimation save encounters an error, the code calls `trans_commit()` instead of `trans_rollback()`. Partial estimation data is permanently committed even on failure.
- **Before Touching**: Simple swap `trans_commit()` → `trans_rollback()` in error branch, but must verify ALL code paths with full regression.

### DZ-020: LOT Delete via HTTP GET
- **Why**: `lot_inward/delete/{id}` uses GET method. ANY authenticated user visiting a crafted link deletes a lot + orphans all child data. No CSRF protection.
- **Before Touching**: Change to POST, add CSRF token, add lot status/usage check before delete.

### DZ-021: Scheme Delete (1/10 Child Tables Cleaned)
- **Why**: `delete_scheme()` only removes `scheme` + `gst_splitup_detail`. 9 other child tables (benefit chart, branch mapping, topup chart, pre-close deductions, incentive, GA benefit, referral, notification, flexible settings) are orphaned.
- **Before Touching**: Add CASCADE-like cleanup for all 10+ child table groups before parent delete.

### DZ-022: `employee.passwd` (base64 — same as DZ-004b)
- **Why**: Employee passwords also stored as `base64_encode()`. Employee login has higher privilege than customer login.
- **Before Touching**: Same migration as DZ-004b but with `employee` table and all login paths.

### DZ-023: Tagging Delete Without Status Guard (RISK-014)
- **Why**: `tagging('delete')` L2079 soft-deletes any tag regardless of current `tag_status`. A sold tag (status=1) can be deleted, making it disappear from all billing/reporting systems.
- **Before Touching**: Add `IF tag_status NOT IN (0, 6, 8)` guard before proceeding with delete. Must also handle lot balance restoration correctly for each status.

### DZ-024: Stock Issue OTP in API Response
- **Why**: `stock_issue_sendotp()` returns the OTP value in the JSON response payload (`'OTP' => $OTP`). Any user can read the OTP from Network tab without receiving the SMS.
- **Before Touching**: Remove `OTP` key from response. Only return success/failure status.

### DZ-025: Sales Transfer Download Methods Without Transactions
- **Why**: `update_sales_ret_transfer()`, `update_TagScan()`, `update_ret_TagScan()` perform tag status updates + billing writes WITHOUT any `trans_begin()`. A failure midway leaves tags in inconsistent state.
- **Before Touching**: Add proper transaction wrapping to all 3 methods. Ensure `trans_rollback()` on any failure.

### DZ-026: Account Gift OTP Assignment Bug (`=` vs `==`)
- **Why**: `verifyotp_gift()` at L4188 uses `=` (assignment) instead of `==` (comparison). Any OTP value passes verification. ALL gift issuance is unprotected.
- **Before Touching**: Simple fix: `=` → `==`. But audit all 5 OTP verify methods for same pattern.

### DZ-027: Masters `clear_database()` — No Auth Gate
- **Why**: `clear_database()` at L2190 truncates ALL tables. There is NO role check, NO OTP, NO confirmation beyond the standard login session. Any logged-in user can wipe the entire database.
- **Before Touching**: Add superadmin-only guard + OTP. Consider removing this method entirely from production.

### DZ-028: Purchase Debug `echo exit` Kills Transaction Rollback
- **Why**: At L322, L452, L631 in the Purchase controller, `echo $this->db->last_query(); exit;` runs BEFORE `trans_rollback()`. The exit kills PHP execution, so the rollback never executes, leaving partial data committed.
- **Before Touching**: Remove all debug echo/exit blocks. Add proper logging instead.

### DZ-029: Branch Transfer Cancel — No Stock Reversal
- **Why**: When a BT is cancelled (status → 3), NO stock/log reversals occur for ANY item type (tagged, non-tagged, old metal, packaging, orders). Tags stay at status=4, NT stock remains deducted from sender, packaging remains in-transit.
- **Before Touching**: Add reversal logic per item type before setting status=3.

### DZ-030: Old Metal NB Payment Uses `cash_amount` (OMP-003)
- **Why**: Controller L1494 uses `$receipt_payment['cash_amount']` when recording a Net Banking payment. The correct field is `['net_banking_amount']`. Every NB payment is recorded with the cash amount value.
- **Before Touching**: Simple field name fix. But audit ALL payment recording across the module for similar field mapping errors.

### DZ-031: Old Metal Testing Receipt — `melting_status` Update Commented Out
- **Why**: Lines 744-755 that update `melting_status` and insert stock logs for against-melting testing receipts are ENTIRELY COMMENTED OUT. Upstream melting records never transition to the tested state.
- **Before Touching**: Uncomment and verify the logic matches current schema. Test with a real against-melting flow.

### DZ-032: Scheme Add — `$id` Undefined for Child Table Inserts
- **Why**: During scheme creation, `$id` (scheme ID) is used for `emp_closing_incentive` and `GA_benefit` inserts BEFORE it's assigned. `deleteData($id)` with empty `$id` could delete wrong records. Inserts get NULL `id_scheme`.
- **Before Touching**: Move child table inserts AFTER the main scheme INSERT and use the returned insert_id.

### DZ-033: Chit APIs — No Centralized Authentication
- **Why**: Both `adminapp_api.php` and `mobile_api.php` have NO token validation middleware. Any caller knowing an `id_customer` value can access/modify that customer's data, payments, and schemes. CORS is set to `*`.
- **Before Touching**: Implement JWT/token-based auth middleware. Remove CORS wildcard. Add rate limiting.

### DZ-034: Chit Collection — Hardcoded OTP 123456
- **Why**: `generateOTP_get()` at L1243 has a hardcoded `123456` OTP. This allows universal authentication bypass on all collection app instances.
- **Before Touching**: Remove the hardcode. Audit all OTP generation functions for similar test patterns.

### DZ-035: Chit Reports — Log Directory Web-Exposed
- **Why**: `admin/log/` directory has NO `.htaccess` protection. 31+ log files contain PII (mobile numbers, payment amounts, account numbers, nominee names). Directly accessible via `http://{host}/admin/log/{date}/...`.
- **Before Touching**: Create `admin/log/.htaccess` with `Deny from all`. Move sensitive logs outside web root.

### DZ-036: Employee — Address Duplicated on Every Edit
- **Why**: `employee_model::add_employee_address()` at L498 has a string comparison bug that ALWAYS triggers INSERT instead of UPDATE. Each edit creates a new address record. After 10 edits, the employee has 10 address rows.
- **Before Touching**: Fix the condition to properly check for existing address and UPDATE. Clean up existing duplicates with script.

### DZ-037: Tag Status — No Centralized State Machine
- **Why**: `ret_taging.tag_status` has 16+ values written by 10+ modules (Tagging, Billing, Branch Transfer, Stock Issue, Purchase Return, Section Transfer, Metal Process, Customer Order, etc.) with NO central guard. Each module directly UPDATEs the column. No module checks whether the transition is valid.
- **Before Touching**: Never modify tag_status without verifying current status first. A sold tag (1) can currently be deleted (2) with no guard.

### DZ-038: Lot Delete — Orphans 324+ Tagging Queries
- **Why**: `lot_inward('delete')` deletes the `ret_lot_inwards` header row but leaves ALL `ret_lot_inwards_detail` rows. 324+ Tagging queries JOIN to these detail rows. After header deletion, detail rows become orphans and cause phantom inventory in stock calculations.
- **Before Touching**: Add cascade deletion of `_detail`, `_stone_detail`, `_other_items`, `_other_charges`. Verify no active tags reference the lot.

### DZ-039: Chit Dashboard updateData — Arbitrary Table Write
- **Why**: `dashboard_model::updateData($data, $id_field, $id_value, $table)` accepts ANY table name, field, and value from the caller with NO whitelist. If called with untrusted POST input, it can UPDATE any row in any table.
- **Before Touching**: Add table whitelist. Validate that `$table` and `$id_field` are from allowed set.

### DZ-040: Sales Transfer — No Cancel Flow
- **Why**: Once a Sales Transfer is created, tags are set to `tag_status=4` (In-Transit). There is NO cancel mechanism. If the transfer is abandoned, tags remain permanently stuck at status 4 and become invisible to billing and reports. The only recovery is manual DB UPDATE.
- **Before Touching**: Implement a cancel flow that restores `tag_status=0` and cleans up the transfer bill.

### DZ-041: Old Metal Process — Write-Once, No Reversal
- **Why**: ALL 10+ process types (pocket, melting, testing, refining, polishing) are write-once. Zero cancel operations exist. Any data entry error (wrong weight, wrong karigar, wrong purity) requires direct DB surgery. State machine has no backward transitions.
- **Before Touching**: Before adding any process save, verify the `melting_status` state machine allows the transition. Never assume cancel exists — it doesn't.

### DZ-042: Section Transfer — Home Counter Stock Direction Bug
- **Why**: `updatesecNTData('-')` is called when tags arrive at a home-bill-counter section (BUG-ST-004). This decrements instead of increments. Combined with billing cancel never reversing `ret_home_section_item` (BUG-ST-024), the table is permanently corrupted.
- **Before Touching**: Team decision needed: is `ret_home_section_item` a running stock balance or throughput counter? Fix depends on this answer.

### DZ-043: Financial Year Status Toggle — No Transaction
- **Why**: `financial_status()` in Catalog_Inventory deactivates ALL financial years first, then activates the selected one. If the activate step fails after deactivate succeeds, ALL financial years become inactive. No `trans_begin/trans_commit` wrapping.
- **Before Touching**: Wrap the deactivate-then-activate in a transaction. Verify test coverage for the failure case.

### DZ-044: Reversal Gaps — System-Wide Pattern
- **Why**: 8+ modules have DELETE/CANCEL operations that restore less than 30% of child tables. Account (12%), Payment (11%), Scheme (18%), Lot (cancel=header only), Employee (30%), Branch Transfer (0/8), Old Metal (0/10+), Customer Order (3/6). This is a systemic design pattern — not an isolated bug.
- **Before Touching**: Before any cancel/delete, enumerate ALL child tables written during CREATE and verify each is restored or blocked.

### DZ-045: Customer Password — Base64 Encoding
- **Why**: `customer.passwd` is stored as `base64_encode()` — a reversible encoding, not encryption. Any DB read access instantly exposes all customer passwords in plaintext. Combined with ACC-020 sending passwords via plaintext SMS, the credential chain is fully unprotected.
- **Before Touching**: Migrate to `password_hash()` (bcrypt). Fix all login flows to use `password_verify()`. SMS flow must be removed or use one-time links.

### DZ-046: Account Number Race — LOCK TABLES Commented Out
- **Why**: `scheme_account.scheme_acc_number` uses `MAX()+1` to generate sequential numbers. The `LOCK TABLES` statement is commented out. Under concurrent account creation (e.g., bulk import), duplicate account numbers are generated and silently inserted.
- **Before Touching**: Use DB auto-increment or re-enable table locking. Verify existing data for duplicates before fix.

### DZ-047: Payment OTP — Assignment Instead of Comparison
- **Why**: Payment module `update_otp()` at L5081 uses `$otp = $this->session->userdata('pay_OTP')` — assignment, not comparison. This means OTP verification **always passes** regardless of what the user enters. Combined with Account module's identical bug (XMOD-088), two critical OTP gates are broken.
- **Before Touching**: Fix `=` to `==` in both payment and account OTP verification flows. Add unit test coverage for OTP mismatch.

### DZ-048: POS Bill Cancellation — No Provider Notification
- **Why**: `cancel_bill()` does NOT call `cancelTransactionRequest()`. If a POS-paid bill is cancelled, the POS machine/provider keeps the charge, but the system marks the bill void. Settlement reconciliation will show a permanent mismatch.
- **Before Touching**: Add `cancelTransactionRequest()` call to `cancel_bill()` when `ret_pos_requests` has a SUCCESS (1) record for that bill.

### DZ-049: Employee Username Validation — Dead Code
- **Why**: `isUserAvailable()` at L133 has `print_r(); exit;` — the real validation logic after it never executes. Duplicate usernames can be created, which will cause login ambiguity (wrong employee gets authenticated).
- **Before Touching**: Remove `print_r(); exit;` and test the actual uniqueness check. Query existing DB for duplicate usernames.

### DZ-050: Masters `clear_database()` — No Auth, No CSRF
- **Why**: A single GET request to `/settings/clear_database` truncates ALL transactional tables (scheme_account, payment, customer, etc.) with zero authentication or confirmation. Any logged-in user can wipe the entire database. No CSRF token, no admin-only gate, no confirmation dialog.
- **Before Touching**: Add admin-only auth check + CSRF token + double-confirmation modal. Consider removing this endpoint entirely from production.

### DZ-051: Chit Collection OTP Hardcoded `123456`
- **Why**: `generateOTP_get()` at L1243 sets OTP to `123456` ("For Demo or testing purpose"). This means ALL OTP-gated operations in the collection app (payment, login, transfer) can be bypassed with the hardcoded value. This is in PRODUCTION code.
- **Before Touching**: Remove hardcoded OTP. Use `mt_rand(100000, 999999)` for real OTP generation. Audit all clients for this code.

### DZ-052: Admin Log Files Web-Accessible
- **Why**: `admin/log/` directory contains JSON-encoded POST data (customer mobile, payment amounts, branch info) written by `updatePaymentDetails` and `updateAccountDetails`. Directory has no `.htaccess` protection — any web visitor can read these PII-rich log files.
- **Before Touching**: Add `admin/log/.htaccess` with `Deny from all`. Move to a non-web-accessible path long-term.

### DZ-053: OTP Returned in API Response — Stock Issue & Section Transfer
- **Why**: Stock Issue `stock_issue_sendotp()` at L1312 and Section Transfer `send_counterchange_otp()` at L610 both return the generated OTP value in the JSON response. Client-side code can extract the OTP and auto-verify without user input.
- **Before Touching**: Remove OTP from response body. OTP should only be sent via SMS/email, never in the API response.

### DZ-054: Catalog Image Upload — No MIME Validation
- **Why**: Category and product image uploads check file extension only (`.jpg`, `.png`). MIME type is NOT verified server-side. An attacker can upload a PHP webshell renamed to `exploit.jpg` — if the web server processes `.jpg` files as PHP (misconfigured), this enables remote code execution.
- **Before Touching**: Add `getimagesize()` AND `finfo_file()` MIME checks server-side. Block upload if MIME doesn't match image types.

### DZ-055: Cash Payment Limit NOT Enforced — Compliance Risk
- **Why**: `validate_cash_amt` in `ret_settings` is currently set to `0` (no validation). The ₹2,00,000 cash payment threshold from `max_cash_amt` is completely ignored. Under Indian tax law, transactions above ₹2L in cash require PAN and generate compliance obligations. This is a regulatory violation.
- **Before Touching**: Set `validate_cash_amt=1` to activate enforcement. Verify all billing paths respect this gate. Add audit trail for overrides.

---

## Safety Checklist Before Modifying Danger Zone Code

- [ ] Read the relevant module brain (`MODULE_BRAIN.md`)
- [ ] Read `CROSS_MODULE_MAP.md` for the module
- [ ] Read `FLOW_RISK_MATRIX.md` if available
- [ ] Check `SHARED_TABLES.md` for table impact
- [ ] Check `MODULE_DEPENDENCIES.md` for AJAX callers
- [ ] Grep ALL controllers for method/table usage
- [ ] Test in staging with realistic data volume
- [ ] Verify ALL reversal paths still work after change
- [ ] Check `VALIDATION_GAPS.md` for related gaps
