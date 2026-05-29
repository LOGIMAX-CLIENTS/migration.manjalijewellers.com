# Validation Gaps — System-Wide Missing Server-Side Checks

> Aggregated from INVARIANT_MATRIX.md, FLOW_RISK_MATRIX.md, CROSS_MODULE_MAP.md, SCHEMA_ANALYSIS.md, and FORENSIC_TEMPLATE.md across all module brains.
> Last updated: 2026-03-26 (Round 13 refresh)
> Total gaps: 209

---

## Critical Validation Gaps

| ID | Module | Gap Description | Severity | Impact |
|---|---|---|---|---|
| VAL-001 | Estimation | Tag status NOT validated server-side before save — JS check only | 🔴 CRITICAL | Sold/reserved tag added to estimation via direct POST |
| VAL-002 | Estimation | `total_cost` calculated JS-only, no server recalculation | 🔴 CRITICAL | Wrong invoice total propagated to billing |
| VAL-003 | Branch Transfer | Tag availability NOT checked server-side before transfer creation | 🔴 CRITICAL | Already-sold tag added to transfer via crafted POST |
| VAL-004 | Account / Payment | OTP comparison uses `=` (assignment) instead of `==` | 🔴 CRITICAL | OTP always evaluates true — bypass |
| VAL-005 | Billing | Metal rate fetched at page load, not re-validated at save time | 🟡 MED | Stale rate used if user delays submission |
| VAL-006 | Billing | `ret_taging_stones` existence NOT checked before billing | 🟡 MED | NaN/zero stone totals silently in bill |
| VAL-007 | Billing | POS cURL failure may not propagate error to caller | 🔴 HIGH | Silent POS failure — bill saves without POS record |
| VAL-008 | Billing | Credit limit check is partial — `get_mc_va_limit()` not always enforced | 🟡 MED | Over-credit possible |
| VAL-009 | Tagging | `lot_status` NOT checked before tagging — tags from closed lots | 🔴 HIGH | Phantom inventory from closed lots |
| VAL-010 | Tagging | `tag_status` NOT checked before order-link in `update_order_link()` | 🔴 HIGH | Sold/deleted tag linked to order |
| VAL-011 | Tagging | Stone DELETE→INSERT not atomic in `update_tagging_data()` | 🔴 HIGH | Data loss if INSERT fails after DELETE |
| VAL-012 | Customer Order | Email sent inside transaction before commit | 🟡 MED | Email sent for rolled-back order |
| VAL-013 | Customer Order | Email token not single-use | 🟡 MED | Replay attack possible |
| VAL-014 | Estimation | `is_eda` flag can be cleared post-approval on re-edit | 🟡 MED | EDA bypass — discount approved without oversight |
| VAL-015 | Estimation | Gift voucher no row-level lock | 🟡 MED | Double redemption across concurrent sessions |
| VAL-016 | Estimation | SR credit > original bill amount silently accepted | 🟡 MED | Negative balance created |
| VAL-017 | Branch Transfer | Cancel does not validate current status before cancelling | 🟡 MED | Any status can be cancelled |
| VAL-018 | Retail Dashboard | `$data` undefined in `get_accountstock_inwards_details()` | 🔴 HIGH | Branch filter broken — always unfiltered |
| VAL-019 | Retail Dashboard | `get_rate_cut_profit_loss()` date range logic inverted | 🟡 MED | `to_date` ignored |
| VAL-020 | Masters | `get_access()` has SQL injection via raw parameter concat | 🔴 CRITICAL | Core RBAC vulnerable |
| VAL-021 | Masters | `clear_database()` reachable without OTP | 🔴 CRITICAL | Catastrophic data loss |
| VAL-022 | Chit Reports | SQL injection in `get_customerenquiry_by_date` raw concat | 🔴 CRITICAL | Data exfiltration |
| VAL-023 | Account Model | `scheme_group_summary_data($id)` — raw `$id` SQL injection | 🔴 HIGH | Injection in report query |
| VAL-024 | Account Model | `is_luckly_draw_scheme($id)` — raw `$id` SQL injection | 🔴 HIGH | Injection in scheme check |
| VAL-025 | Catalog | SQL injection in `get_profile_settings()` L199-203 | 🔴 HIGH | Profile settings vulnerable |
| VAL-026 | Settings | `validate_cash_amt=0` → cash limit NOT enforced | 🟡 MED | Compliance gap |
| VAL-027 | Old Metal | `ret_taging.tag_process` set to 1 — no rollback on pocket delete | 🟡 MED | Permanent pocketed state |
| VAL-028 | Customer | `Searchcustomer()` uses raw concat in SQL — SQL injection | 🔴 CRITICAL | Full DB access via search field (CUS-BUG-003) |
| VAL-029 | Customer | `download($id, $file)` has NO path sanitization — LFI/path traversal | 🔴 CRITICAL | Arbitrary file read from server (CUS-BUG-002) |
| VAL-030 | Customer | `allocate_agent_toCuctomers()` — `$total` overwritten with `array()` — always silently fails | 🔴 HIGH | Agent allocation appears to work but never persists (CUS-BUG-001) |
| VAL-031 | Customer | `allocate_employee()` — same `$total` overwrite bug as agent allocation | 🔴 HIGH | Employee allocation always fails silently (CUS-BUG-018) |
| VAL-032 | Customer | `get_customer($id)` scheme count subquery hardcoded `id_customer=1` | 🟡 MED | Shows customer 1's scheme count for all customers (CUS-BUG-007) |
| VAL-033 | Customer | `set_image()` never calls `update_images()` — webcam image path lost | 🟡 MED | Uploaded image not saved to DB (CUS-BUG-019) |
| VAL-034 | Payment | `amount_to_weight()` has no zero-check on metal rate — division by zero | 🔴 CRITICAL | Runtime crash if metal rate = 0 |
| VAL-035 | Payment | Receipt gen has no DB lock — commented out `LOCK TABLES` at L42-43 | 🔴 HIGH | Duplicate receipt numbers under concurrent payments |
| VAL-036 | Payment | `payment_amount = 0` accepted — no server-side amount validation in SaveAll | 🟡 MED | Zero-amount payment records created |
| VAL-037 | Payment | PDC convert has typo `$pay['payee_ifsc]']` — bracket in key name | 🔴 HIGH | IFSC always NULL on PDC→Payment conversion |
| VAL-038 | Payment | Delete is hard delete with no child cleanup — orphaned `payment_mode_details` | 🔴 HIGH | Orphan financial records after payment delete |
| VAL-039 | Customer Order | `if($order_advance > 0)` uses array-to-int comparison — always truthy | 🔴 HIGH | Zero-amount receipt row inserted on cancel even with no advance (AP-11) |
| VAL-040 | Sales Transfer | **ALL 17 model methods** use raw string concat SQL — zero parameterized queries | 🔴 CRITICAL | Entire module is SQLi-vulnerable — any user input in tag search/save reaches DB |
| VAL-041 | Sales Transfer | `ret_billing.tot_bill_amount` is `decimal(10,0)` — amounts truncated to integers | 🔴 HIGH | Every sales transfer bill loses decimal precision |
| VAL-042 | Sales Transfer | Return transfer omits `is_credit` and `credit_status` — gets wrong DB defaults (0/1 instead of 1/2) | 🔴 CRITICAL | Return transfers appear non-credit and fully-paid when they should be credit+pending |
| VAL-043 | Sales Transfer | `billing_for=3` (transfer) not in DB comment enum — reports may ignore it | 🟡 MED | Undocumented bill type value |
| VAL-044 | LOT | `lot_inward/delete` uses HTTP GET — CSRF vulnerability | 🔴 CRITICAL | Any auth'd user visiting a crafted link deletes a lot |
| VAL-045 | LOT | `debug echo last_query()` in production in merge/split failure paths | 🔴 HIGH | SQL query structure leaked to browser on failure |
| VAL-046 | LOT | Lot delete does NOT decrement `ret_nontag_item` stock — permanent stock count error | 🔴 HIGH | Non-tag stock inflated after lot deletion |
| VAL-047 | LOT | Trailing spaces in column names in insert arrays (`is_apply_in_lwt `, `stone_cal_type  `) | 🔴 HIGH | Column-not-found errors in strict DB mode |
| VAL-048 | Stock Issue | Same `tag_id` can appear in multiple open issues — no uniqueness check | 🔴 HIGH | Tag double-issued to different recipients |
| VAL-049 | Stock Issue | No negative stock guard on `ret_nontag_item` arithmetic deduct | 🔴 HIGH | Negative non-tag stock balance possible |
| VAL-050 | Stock Issue | `issue_no` generated via MAX() — race condition on concurrent inserts | 🟡 MED | Duplicate issue numbers under load |
| VAL-051 | Other Inventory | `stock_id_uom` and `issue_to` NOT updated in edit flow — data loss on edit | 🟡 MED | Item config resets silently |
| VAL-052 | Other Inventory | Typo in table name `ret_other_invnetory_issue` — all FKs reference misspelled table | 🟡 LOW | Consistent typo — works but confusing |
| VAL-053 | Old Metal Process | `process_no` has NO UNIQUE constraint — duplicate process numbers under load | 🔴 HIGH | Duplicate process numbers from concurrent saves |
| VAL-054 | Section Transfer | `ret_home_section_item` only DECREMENTED, never INCREMENTED — negative stock | 🔴 HIGH | Net negative home section stock over time |
| VAL-055 | Section Transfer | `from_section` hardcoded NULL in `ret_section_nontag_item_log` — incomplete audit | 🟡 MED | Cannot trace where NT item came from |
| VAL-056 | Employee | `employee.passwd` also stored as `base64_encode()` same as customer | 🔴 CRITICAL | Employee passwords trivially reversible |
| VAL-057 | Employee | `updEmpAccessTime()` raw SQL with no escaping — SQL injection | 🔴 HIGH | Access time update vulnerable |
| VAL-058 | Tagging | Delete case has NO `tag_status` guard — sold tag (status=1) can be soft-deleted (RISK-014) | 🔴 CRITICAL | Sold inventory vanishes from reports |
| VAL-059 | Tagging | Retag nested transaction scope — sub-methods create own tx; outer rollback doesn't undo (RISK-015) | 🔴 HIGH | Old tags stuck at status=3 permanently |
| VAL-060 | Tagging | No server-side validation of `sales_value` — JS-computed value stored directly | 🔴 HIGH | Manipulated POST can store any sale value |
| VAL-061 | Tagging | `set_tagging_wastage_and_mc()` defined TWICE in JS (L33456 & L33624) — second overwrites first | 🟡 MED | Wrong wastage/MC behavior depending on load order |
| VAL-062 | Stock Issue | OTP returned in API response (`'OTP' => $OTP`) — OTP value visible in network tab | 🔴 CRITICAL | OTP bypass via network inspection |
| VAL-063 | Stock Issue | XSS via raw DOM injection: `.append('<p>' + data.msg + '</p>')` — server msg injected unescaped | 🔴 HIGH | Stored XSS if server returns HTML in msg |
| VAL-064 | Stock Issue | Hardcoded GST 3% in PDF template (`issue_ack.php` L569) — ignores DB tax rate | 🟡 MED | Tax discrepancy on printed acknowledgment |
| VAL-065 | Sales Transfer | `$insId` undefined in 3 methods (L277, L397, L440) — PHP warning on every execution | 🔴 HIGH | Log insert silently fails |
| VAL-066 | Sales Transfer | `getAllBranchDCData()` returns array-of-arrays but code accesses as flat array — `$bill_date` = NULL | 🔴 CRITICAL | ALL transfer bills get NULL bill_date |
| VAL-067 | Sales Transfer | Return transfer `$tot_bill_amount` accumulates across category loop iterations | 🔴 HIGH | Bill total inflated with each category |
| VAL-068 | Sales Transfer | 3 methods (`update_sales_ret_transfer`, `update_TagScan`, `update_ret_TagScan`) missing `trans_begin()` | 🔴 CRITICAL | Database writes without transaction protection |
| VAL-069 | Sales Transfer | NULL day-closing date comparison: `strtotime(NULL) < strtotime(NULL)` = FALSE — validation bypassed | 🔴 HIGH | Transfers proceed without day-closing |
| VAL-070 | Account | Gift OTP `=` instead of `==` at L4188 — any OTP accepted | 🔴 CRITICAL | OTP bypass on gift verification |
| VAL-071 | Account | `manual_schemeaccount()` trans_commit INSIDE loop — first iteration commits, rest can't rollback | 🔴 HIGH | Partial account number updates |
| VAL-072 | Account | Account number race: `LOCK TABLES` commented out — concurrent MAX() | 🔴 HIGH | Duplicate scheme account numbers |
| VAL-073 | Account | ALL 5 OTP endpoints return OTP in JSON response | 🔴 CRITICAL | OTP visible in Network tab (5 endpoints!) |
| VAL-074 | Account | `$duration` undefined in `generate_giftotp()` L3218 — OTP expiry = 0 = always expired | 🔴 HIGH | Gift OTP immediately expires |
| VAL-075 | Account | Hardcoded date `2024-10-07` in GA bonus calc L1433 | 🟡 MED | Bonus calc wrong after that date |
| VAL-076 | Account | Image upload BEFORE `trans_status` check — failed tx leaves orphan images | 🟡 MED | Filesystem clutter |
| VAL-077 | Masters | `clear_database()` has NO auth/role gate — any logged user | 🔴 CRITICAL | Database wipe by any user |
| VAL-078 | Masters | `db_backup()` has no role check | 🔴 HIGH | DB dump downloaded by non-admin |
| VAL-079 | Masters | `rate.txt` writes "Array" instead of JSON (MST-BUG-003) | 🔴 HIGH | Mobile API gets corrupt rate data |
| VAL-080 | Masters | 61/89 AJAX calls missing `error:` handlers — failures SILENT | 🟡 MED | No user feedback on network errors |
| VAL-081 | Catalog | 11 model methods use raw `$id` string concat (get_purity, get_color, etc.) | 🔴 HIGH | SQL injection across master data lookups |
| VAL-082 | Catalog | `getActiveSearchProd()` LIKE injection — `$SearchTxt` unescaped | 🔴 HIGH | Search wildcard injection |
| VAL-083 | Catalog | Category purities DELETE-then-INSERT on edit — data loss if re-insert fails | 🔴 HIGH | Category purities orphaned mid-edit |
| VAL-084 | Catalog | Product delete doesn't clean `ret_product_section` — orphaned section records | 🟡 MED | Phantom product-section links |
| VAL-085 | Customer Order | `generateOrderNo()` race condition — MAX() without locking | 🔴 HIGH | Duplicate order numbers |
| VAL-086 | Purchase | `echo $this->db->last_query();exit;` at L322/L452/L631 — kills rollback on failure | 🔴 CRITICAL | Transaction rollback prevented by debug exit |
| VAL-087 | Old Metal | NB payment uses `$receipt_payment['cash_amount']` instead of `['net_banking_amount']` (OMP-003) | 🔴 CRITICAL | Wrong payment amount recorded for net banking |
| VAL-088 | Old Metal | Polishing receipt `$id_branch` undefined — `created_branch = NULL` on all lot inward records (OMP-028) | 🔴 HIGH | All polishing lot records have NULL branch |
| VAL-089 | Old Metal | Refining receipt hardcodes `'piece' => 1` for all category rows (OMP-029) | 🔴 HIGH | Refining stock shows 1 piece regardless of actual count |
| VAL-090 | Old Metal | Testing receipt `melting_status` update code entirely COMMENTED OUT (L744-755) | 🔴 CRITICAL | Against-melting testing never updates upstream status |
| VAL-091 | Old Metal | `get_chg_tax_type()` compares `id_company` to `id_state` directly (OMP-002) | 🔴 HIGH | GST CGST/SGST vs IGST split always wrong |
| VAL-092 | Old Metal | Pocket issue weight validated JS-only — no server-side cap (I2) | 🔴 HIGH | Crafted POST can over-issue pocket |
| VAL-093 | Retail Dashboard | `get_CustomerDetails()` ignores branch entirely — shows all-branch customer count | 🟡 MED | Wrong customer count per branch |
| VAL-094 | Retail Dashboard | Old vs New customer uses IDENTICAL SQL (RULE-RETDASH-009) | 🔴 HIGH | Copy-paste bug — always shows same count |
| VAL-095 | Retail Dashboard | Division by zero in `get_saleschart_details()` L1391 | 🔴 HIGH | Fatal error on empty sales data |
| VAL-096 | Retail Dashboard | `$id_category` + `$data` undefined in `get_accountstock_inwards_details()` L2223 | 🔴 HIGH | Account stock widget broken |
| VAL-097 | Retail Dashboard | Rate cut P&L `to_date` ignored in WHERE — shows only historical data (Bug #23) | 🟡 MED | Incomplete date filtering |
| VAL-098 | Other Inventory | `gift_mapping` writes BEFORE `trans_begin` in issue flow | 🔴 HIGH | Gift mapping committed even if issue tx fails |
| VAL-099 | Scheme | Add with `$id` undefined for `emp_closing_incentive` and `GA_benefit` inserts (EC-2, EC-3) | 🔴 CRITICAL | NULL `id_scheme` FK or wrong records deleted |
| VAL-100 | Account | Division by zero if `total_installments = 0` in lump sum calc (EC-1) | 🔴 HIGH | Fatal error on 0-installment schemes |
| VAL-101 | Account | Closing balance can go negative — no validation prevents it (EC-10) | 🟡 MED | Negative financial balance |
| VAL-102 | Chit Collection | Hardcoded OTP 123456 in `generateOTP_get()` L1243 | 🔴 CRITICAL | Any user can authenticate with 123456 |
| VAL-103 | Chit Collection | `$service` undefined in `generateOTP_get()` L1272 | 🔴 HIGH | WhatsApp notification crashes |
| VAL-104 | Chit Collection | CORS wildcard `*` — any website can call API endpoints | 🔴 CRITICAL | Cross-origin API abuse |
| VAL-105 | Chit Collection | No centralized API auth on any REST endpoint | 🔴 CRITICAL | Unauthenticated API access |
| VAL-106 | Chit Collection | `base64_encode()` used as password "encryption" via `__encrypt()` | 🔴 CRITICAL | Trivially reversible passwords |
| VAL-107 | Chit Customer | No API auth — `id_customer` in request grants full access to any customer data | 🔴 CRITICAL | IDOR — access any customer data |
| VAL-108 | Chit Customer | Wallet debit BEFORE gateway confirmation — no rollback on gateway failure | 🔴 CRITICAL | Wallet money lost on payment failure |
| VAL-109 | Chit Customer | Aadhaar upload accepts any base64 content — no file type validation | 🔴 HIGH | Arbitrary file upload |
| VAL-110 | Chit Dashboard | Paid/Unpaid % mixes SUM(amounts) with COUNT(accounts) (RULE-DAS-002) | 🔴 HIGH | ~100% paid always shown |
| VAL-111 | Chit Dashboard | "Last Week" filter generates garbled SQL (`â€“` char) in 7+ methods | 🔴 HIGH | SQL error on LW filter |
| VAL-112 | Chit Dashboard | APK upload — no file type validation in `upload()` | 🔴 HIGH | Arbitrary file upload via APK form |
| VAL-113 | Chit Reports | Log directory web-exposed — 31 PII files (mobile, amounts, acc numbers) | 🔴 CRITICAL | PII data breach via direct URL |
| VAL-114 | Chit Reports | `$today` used before assignment in `scheme_daily_collection_details()` L1163 | 🔴 HIGH | Collection report crashes |
| VAL-115 | Chit Settings | `configDB()` calls COMMENTED OUT at L1949 and L2094 | 🔴 HIGH | App version config never saves |
| VAL-116 | Employee | Address duplicated on EVERY edit — always INSERT, never UPDATE (EMP-BUG-003) | 🔴 HIGH | N duplicate address records per employee |
| VAL-117 | Employee | Employee add has `trans_status()` WITHOUT matching `trans_begin()` (EMP-BUG-002) | 🔴 HIGH | No atomicity on employee creation |
| VAL-118 | Employee | Delete only cleans address — customer.allocated_employee orphaned (EMP-BUG-016) | 🟡 MED | Broken FK references |
| VAL-119 | Masters | `get_access()` returns NULL for unknown URLs (MST-BUG-017) — callers don't null-check | 🔴 HIGH | PHP notices, potential auth bypass |
| VAL-120 | Chit Collection | Cashfree signature verification COMMENTED OUT (BUG-013) | 🔴 CRITICAL | Forged payment callbacks accepted |
| VAL-121 | Chit Collection | Ippo gateway uses attacker-supplied credentials (BUG-031) | 🔴 CRITICAL | Attacker can forge payment success |
| VAL-122 | Chit Collection | RazorPay callback is `print_r()+exit` (BUG-032) — non-functional | 🔴 HIGH | Gateway callback never processes payment |
| VAL-123 | Chit Collection | `split_payment()` has no transaction wrapping (BUG-043) | 🔴 HIGH | Partial split = payment records inconsistent |
| VAL-124 | Chit Customer | `getMatchingCountry/State/City` raw `LIKE '$char%'` (INV-052) | 🔴 HIGH | SQL injection via typeahead |
| VAL-125 | Chit Dashboard | `updateData()` accepts ANY table, id_field, id_value (INV-DAS-W01) | 🔴 CRITICAL | Arbitrary table write via POST |
| VAL-126 | Chit Dashboard | Double day-close for same branch+date — no unique guard (INV-DAS-W04) | 🔴 HIGH | Duplicate daily collection records |
| VAL-127 | Billing | POS transaction NOT reversed on bill cancel (FR-BIL-005) | 🔴 HIGH | POS out of sync on bill cancel |
| VAL-128 | Account | Delete restores only 1/8 child tables (FR-ACC-004) — 12.5% reversal | 🔴 HIGH | Orphaned KYC, gift, wallet, payment records |
| VAL-129 | Tagging | Tag delete orphans `ret_taging_stone`, `_material`, `_images` records | 🔴 HIGH | Permanent data bloat |
| VAL-130 | Tagging | `update_order_link()` L6047 has NO tag_status guard — links sold/deleted tags | 🔴 HIGH | Sold tag linked to order |
| VAL-131 | Lot | Delete does NOT cascade to `ret_lot_inwards_detail` — breaks 324+ Tagging queries (R-LOT-012) | 🔴 CRITICAL | Phantom inventory in all tag reports |
| VAL-132 | Lot | `lot_completed()` WHERE has trailing space in `'lot_no '` — silent no-op (R-LOT-023) | 🔴 CRITICAL | Lot never actually closes |
| VAL-133 | Lot | Cancel does NOT reverse `ret_nontag_item` stock quantity | 🔴 HIGH | Non-tag stock permanently inflated |
| VAL-134 | Sales Transfer | No cancel flow — tags permanently stuck at `tag_status=4` (Risk #7) | 🔴 HIGH | Inventory frozen in transit |
| VAL-135 | Estimation | Tag status check is CLIENT-SIDE ONLY — sold tag can be added to estimation via POST | 🔴 HIGH | Phantom revenue on billing |
| VAL-136 | Branch Transfer | Cancel after transit restores 0/8 child tables — tags stuck at `tag_status=4`, NT stock unreverted | 🔴 CRITICAL | Permanent inventory corruption on any BT cancel |
| VAL-137 | Catalog | `financial_status()` L9628 is NOT wrapped in transaction — all fin years can go inactive | 🔴 CRITICAL | System-wide financial year lookup failure |
| VAL-138 | Catalog | Design delete orphans ALL 5 child tables (`karigars`, `purity`, `other_materials`, `sizes`, `stone`) | 🔴 HIGH | Permanent data bloat |
| VAL-139 | Customer Order | Order delete orphans `customer_order_image`, `ret_order_item_stones`, `ret_order_other_charges` + tag FK | 🔴 HIGH | Tag points to deleted order |
| VAL-140 | Customer Order | Status 4→5 transition has NO guard — order marked Delivered without being Completed | 🔴 HIGH | Broken order lifecycle |
| VAL-141 | Old Metal Process | 0/10+ cancel operations implemented — write-once, no reversal | 🔴 CRITICAL | Any data error requires direct DB surgery |
| VAL-142 | Old Metal Process | Pocket `status` field never set to closed (`status=1`) after full issue (OMP-007) | 🔴 HIGH | Pockets accumulate forever |
| VAL-143 | Other Inventory | Cancelled purchase pieces remain in available stock (BRN-OI-023) | 🔴 HIGH | Stock inflated after cancel |
| VAL-144 | Other Inventory | `product_details/save` has NO `trans_begin()` (BRN-OI-007) | 🔴 HIGH | Partial piece creation on failure |
| VAL-145 | Payment | Delete payment cleans 1/9 tables (11% reversal) — orphans mode_details, wallet, referral | 🔴 CRITICAL | Financial data integrity broken |
| VAL-146 | Scheme | Delete scheme cleans 2/11 child tables (18% reversal) | 🔴 CRITICAL | Orphaned benefit/incentive/branch data |
| VAL-147 | Section Transfer | Home counter stock direction WRONG — decrements instead of increments (BUG-ST-004) | 🔴 CRITICAL | Stock goes negative on ST arrival |
| VAL-148 | Section Transfer | OTP returned in JSON response (BUG-ST-006) | 🔴 HIGH | OTP bypass via DevTools |
| VAL-149 | Section Transfer | OTP SMS block is commented out (BUG-ST-021) — OTP never delivered | 🔴 HIGH | OTP unusable except via DevTools |
| VAL-150 | Section Transfer | `ret_home_section_item` never reversed on billing cancel (BUG-ST-024) | 🔴 HIGH | Home counter stock permanently corrupted |
| VAL-151 | Masters | `rate.txt` writes "Array" string instead of valid JSON (MST-BUG-003) | 🔴 CRITICAL | Mobile rate display broken for all clients |
| VAL-152 | Masters | `update_rate_file()` commented out — rate edits don't update mobile file (MST-BUG-042) | 🔴 HIGH | Stale rates in mobile app |
| VAL-153 | Customer Order | `$order_advance > 0` guard truthy for zero amounts — inserts $0 receipt rows (AP-11) | 🔴 HIGH | Ghost receipt records |
| VAL-154 | Stock Issue | Same tag_id can appear in multiple open issues — no uniqueness check | 🔴 HIGH | Tag double-issued |
| VAL-155 | Stock Issue | NT deduct has no negative stock guard — arithmetic allows qty < 0 | 🔴 HIGH | Negative stock counts |
| VAL-156 | Retail Settings | `ret_settings.name` has NO UNIQUE constraint — duplicate setting names cause ambiguous lookups | 🔴 HIGH | Wrong config value returned |
| VAL-157 | Customer | `passwd` stored as `base64_encode()` not bcrypt — reversible encoding (CUS-BUG-009) | 🔴 CRITICAL | Credential exposure |
| VAL-158 | Other Inventory | `stock_id_uom` and `issue_to` NOT updated on item edit (BRN-OI-012) | 🔴 HIGH | Stale UOM/issue config |
| VAL-159 | Old Metal Process | `process_no` is NULLABLE with NO UNIQUE constraint (OMP-005) | 🔴 HIGH | Duplicate process numbers |
| VAL-160 | Lot | `mc_type` label IF condition is INVERTED (mc_type=2 shows 'PER GRAM' should be 'PER PCS') | 🔴 HIGH | Wrong label in reports/forms |
| VAL-161 | Account | LOCK TABLES commented out for acc number generation — race condition | 🔴 HIGH | Duplicate account numbers |
| VAL-162 | Account | `verifyotp_gift()` uses `=` assignment instead of `==` comparison at L4188 | 🔴 HIGH | OTP always passes |
| VAL-163 | Account | `lump_payable_weight` divides by `total_installments` — no zero guard | 🔴 HIGH | Division by zero |
| VAL-164 | Billing | GST tax per line item is CLIENT-SIDE calculation, server stores as-is (RULE-BIL-018) | 🔴 HIGH | Tax tampering via POST |
| VAL-165 | Account | Password sent plaintext via SMS on `send_login_details` (RULE-ACC-020) | 🔴 HIGH | Credential in transit |
| VAL-166 | Payment | OTP verify at L5081 uses `=` assignment instead of `==` — OTP always passes (PAY-017) | 🔴 CRITICAL | OTP bypass on all payments |
| VAL-167 | Payment | Receipt number `MAX()+1` has no DB lock — concurrent duplicates (PAY-004) | 🔴 HIGH | Duplicate receipt numbers |
| VAL-168 | Payment | Payment mode detection duplicated 3× with variations (PAY-003) | 🟠 MEDIUM | Mode classification inconsistency |
| VAL-169 | Estimation | Print templates recalculate VA/MC in PHP — can diverge from JS form values (EST-026) | 🔴 HIGH | Printed totals ≠ form totals |
| VAL-170 | Estimation | `est_print.php` ALWAYS shows CGST/SGST — never checks inter-state for IGST (EST-025) | 🔴 HIGH | Wrong tax on inter-state prints |
| VAL-171 | Estimation | Wastage% > 100 has no server-side guard (EST-002) | 🔴 HIGH | Negative net weight |
| VAL-172 | Customer | Status toggle via GET request — no CSRF protection (CUS-010) | 🔴 HIGH | CSRF state change |
| VAL-173 | Customer Order | Vendor email token is REUSABLE — no single-use enforcement (CUSORD-009) | 🔴 HIGH | Replay attack |
| VAL-174 | Customer Order | Stone delete-reinsert on edit has NO transaction — failure = data loss (CUSORD-012) | 🔴 HIGH | Permanent stone data loss |
| VAL-175 | Employee | Username validation `isUserAvailable()` has `print_r(); exit;` — dead code (EMP-BUG-001) | 🔴 HIGH | Duplicate usernames allowed |
| VAL-176 | Employee | Wallet `issued_date` uses 2-digit year `date('y-m-d')` (EMP-BUG-011) | 🟠 MEDIUM | Date parsing failures in 2100 |
| VAL-177 | Purchase | Due date validation is CLIENT-SIDE only — stale page can submit past dates (PUR-004) | 🔴 HIGH | Past-dated orders |
| VAL-178 | Purchase | Piece count cap is CLIENT-SIDE only — no server guard (PUR-005) | 🔴 HIGH | Exceeding ordered quantity |
| VAL-179 | Scheme | `SHOW COLUMNS` called on every save — performance risk on high-traffic (SCH-015) | 🟠 MEDIUM | Query overhead |
| VAL-180 | Sales Transfer | Tax rate hardcoded 3% — not from config. GST rate change requires code edit (ST-002) | 🔴 HIGH | Tax undercharge/overcharge |
| VAL-181 | Sales Transfer | Return bill `$tot_bill_amount` accumulates across categories — 2nd bill includes 1st total (ST-011) | 🔴 HIGH | Wrong bill amounts |
| VAL-182 | Sales Transfer | From-branch `on('change')` always filters DIFFERENT GST — contradicts initial load SAME GST logic (ST-010) | 🔴 HIGH | Wrong branch pairing |
| VAL-183 | Branch Transfer | Tag availability is CLIENT-SIDE only — no server guard in save (BRT-014) | 🔴 HIGH | Phantom transfer |
| VAL-184 | Branch Transfer | Cancel does NOT reverse tag/NT/OI stock changes (BRT-012) | 🔴 HIGH | Stock corruption |
| VAL-185 | Branch Transfer | PS/SR items have no duplicate-transfer guard (BRT-015) | 🔴 HIGH | Double-transfer |
| VAL-186 | Stock Issue | OTP value returned in API response — client can extract and auto-verify (SI-006) | 🔴 CRITICAL | OTP bypass |
| VAL-187 | Section Transfer | NT qty validation CLIENT-SIDE only — crafted POST can transfer more than available (ST-008) | 🔴 HIGH | Stock negative |
| VAL-188 | Section Transfer | OTP value returned in JSON response (ST-006 L610) | 🔴 HIGH | OTP bypass |
| VAL-189 | OI | Purchase cancel does NOT reverse tagged pieces or log entries (OI-006) | 🔴 HIGH | Phantom stock |
| VAL-190 | OI | `generatePurNo()` and `generaterefCode()` both have race conditions (OI-003, OI-004) | 🔴 HIGH | Dup ref numbers |
| VAL-191 | OMP | Status machine NOT enforced — processes can skip states (OMP-008) | 🔴 HIGH | Process integrity |
| VAL-192 | Masters | `clear_database()` has NO auth check, NO CSRF (MST-BUG-001) | 🔴 CRITICAL | Full DB wipe |
| VAL-193 | Masters | `get_access()` — `$url` concat raw into SQL (MST-BUG-002) | 🔴 HIGH | SQL injection |
| VAL-194 | Chit Collection | OTP hardcoded to `123456` in `generateOTP_get()` (COL-010) | 🔴 CRITICAL | OTP disabled |
| VAL-195 | Chit Collection | Cash collection by employee = instant SUCCESS without admin approval (COL-003) | 🔴 HIGH | Embezzlement risk |
| VAL-196 | Chit Dashboard | Paid/unpaid % mixes money (SUM) with count (COUNT) in denominator (DAS-002) | 🔴 HIGH | Meaningless stat |
| VAL-197 | Chit Reports | `admin/log/` is web-accessible — contains PII (RPT-011) | 🔴 CRITICAL | Data exposure |
| VAL-198 | Catalog | Only 3 `set_rules` in 23,579 lines — zero server-side form validation for all entities except cover_up (CAT-018) | 🔴 HIGH | Any POST accepted |
| VAL-199 | Catalog | Image upload checks extension only, NOT MIME type — PHP file with .jpg extension accepted (CAT-007) | 🔴 HIGH | Code execution |
| VAL-200 | Catalog | Financial year status toggle NOT transaction-wrapped — deactivate-all fails = all FY inactive (CAT-021) | 🔴 HIGH | No active FY |
| VAL-201 | Catalog | `get_profile_settings()` SQL injection via `$id_profile` concat (CAT-023) | 🔴 HIGH | SQLi (session-sourced) |
| VAL-202 | Ret Reports | Section NT closing formula OMITS `pur_ret_nwt` — net weight discrepancy (RPT-013) | 🔴 HIGH | Stock miscalculation |
| VAL-203 | Chit Cust App | No OTP brute-force protection — 900K possibilities, no lockout (APP-002) | 🔴 HIGH | OTP brute force |
| VAL-204 | Chit Cust App | `insert_common_data()` post-payment steps NOT transaction-wrapped (APP-007) | 🔴 HIGH | Partial commit |
| VAL-205 | Chit Cust App | KYC gate is CLIENT-SIDE only — `mobile_payment_post` does NOT check `kyc_status` (APP-011) | 🔴 HIGH | KYC bypass |
| VAL-206 | Chit Cust App | Rate-fix amount not server-validated — app POST can modify locked rate (APP-010) | 🔴 HIGH | Rate tampering |
| VAL-207 | Retail Settings | `validate_cash_amt=0` — ₹2L cash limit NOT enforced (RSET-007) | 🔴 HIGH | Compliance violation |
| VAL-208 | Retail Settings | Permission save not transactional per-row — partial permissions on failure (RSET-017) | 🟠 MEDIUM | Inconsistent RBAC |
| VAL-209 | Retail Settings | Company profile update has no optimistic locking — last writer wins (RSET-019) | 🟠 MEDIUM | Data overwrite |

---

## Validation Gap Patterns

| Pattern | Occurrences | Root Cause |
|---|---|---|
| Client-side-only validation (no server guard) | 5+ (Estimation, BT, Tagging, Customer Order) | Trust in JS POST data |
| SQL injection via raw variable concat | **25+** (Masters, Chit Reports, Account, Catalog, Customer, Payment, **Sales Transfer 17**, BT 6, Employee, OI) | Legacy `$this->db->query()` with string concat |
| Missing foreign key / existence check | 6+ (Estimation items, BT bill details, OMP tags, BT tag items, SI, LOT) | Implicit FK reliance |
| OTP implementation bugs | 2 (Account, Payment) | Copy-paste error (`=` vs `==`) |
| No row-level locking on shared resources | 5+ (Tag status, Gift voucher, Scheme account, Receipt no, Issue no, Process no) | Missing `FOR UPDATE` / `LOCK TABLES` |
| Silent failure (function runs but persists nothing) | 3 (Customer agent alloc, employee alloc, image upload) | Variable overwrite bug, missing DB call |
| Path traversal / LFI | 1 (Customer KYC download) | No input sanitization on file paths |
| Hard delete without child cleanup | 4 (Payment, Customer, Employee, Scheme) | Missing cascade or explicit child cleanup |
| Type coercion bugs | 2 (CusOrd AP-11, Payment OTP) | PHP loose comparison on array/int |
| CSRF via GET | 2 (LOT delete, Scheme delete) | DELETE operation accessible via GET request |
| Decimal precision mismatch | 1 (Sales Transfer — `decimal(10,0)` vs `decimal(10,2)`) | Header truncates detail precision |
| base64 password storage | 2 (Customer, Employee) | Trivially reversible — not hashed |
| `trans_commit()` in error branch | 1 (Estimation) | Partial saves permanently persisted |
| Stock decrement not reversed on delete | 2 (LOT delete, BT cancel) | No reversal logic exists |
| OTP value leaked in API response | 2 (Stock Issue, Section Transfer) | OTP returned in JSON response |
| XSS via unescaped DOM injection | 1 (Stock Issue OTP modal) | `.append()` with server msg |
| No server-side formula validation | 2 (Tagging sales_value, Estimation) | JS-computed values trusted |
| Functions defined twice in JS | 2 (Tagging wastage/MC, tax calc) | Second definition overwrites first |
| Undefined variable in production | 2 (Sales Transfer `$insId`, `$tb_entry_date`) | PHP warning, silent failure |
| Missing `trans_begin()` entirely | 3 (Sales Transfer download methods) | Writes without any transaction |
| OTP `=` (assignment) instead of `==` | 2 (Account gift OTP, Payment OTP) | OTP always passes |
| LOCK TABLES commented / no row locking | 6+ (Tag, Voucher, Scheme, Receipt, Issue, AccountNo) | Race condition duplicates |
| `clear_database()` no auth gate | 1 (Masters) | Any user can wipe DB |
| `echo last_query();exit;` kills rollback | 4+ (Purchase L322/L452/L631, LOT merge) | Debug code prevents transaction rollback |
| DELETE-INSERT pattern on edit | 2+ (Catalog purities, Customer Order stones) | Data loss if re-insert fails |
| LIKE injection via unescaped search | 2 (Catalog products, sub-products) | Wildcard injection |
| SQL injection via model raw concat | **36+** (Masters 11+, Catalog 11+, Employee 6+, ST 17, others) | All legacy query patterns |
| NB payment uses cash_amount field | 1 (Old Metal Process OMP-003) | Wrong payment source field |
| Commented-out status update code | 1 (Old Metal melting_status L744-755) | Business logic silently disabled |
| Variable undefined in production | 5+ (ST `$insId`, OMP `$id_branch`, Scheme `$id`, Dashboard `$id_category`) | NULL values or wrong behavior |
| Copy-paste identical SQL for different purposes | 1 (Dashboard old vs new customer) | Both queries return same data |
| JS-only validation, no server guard | 2+ (OMP pocket weight, Tagging sales_value) | POST manipulation bypasses |
| 0% variant test coverage | 2 (Catalog 324 combos, Sales Transfer 15 combos) | No testing of config-dependent behavior |
| Hardcoded OTP for production | 1 (Chit Collection 123456) | Authentication bypass |
| CORS wildcard (*) on API | 1 (Chit Collection REST) | Cross-origin attack surface |
| No API auth / IDOR | 2 (Chit Collection, Chit Customer) | Unauthenticated data access |
| Wallet debit before gateway confirm | 1 (Chit Customer App) | Money loss on failure |
| Log dir web-exposed with PII | 1 (Chit Reports 31 files) | Data breach via URL |
| Address INSERT instead of UPDATE | 1 (Employee) | N duplicates per employee |
| `trans_status()` without `trans_begin()` | 1 (Employee add) | False atomicity |
| `configDB()` calls commented out | 1 (Chit Settings) | Config changes never saved |
| `get_access()` returns NULL | 1 (Masters) | Auth chain breaks |
