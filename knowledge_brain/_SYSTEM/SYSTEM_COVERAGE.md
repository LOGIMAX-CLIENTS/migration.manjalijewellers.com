# System Brain Coverage

> How much of the system has brain coverage?
> Last updated: 2026-03-26 (Round 13 refresh — **ALL 5 SOURCES AT 100%**)

---

## Module Coverage

| Module | Controller | Brain? | FLOW_RISK_MATRIX? | SCHEMA_ANALYSIS? | FORENSIC? | INVARIANT? | Last Updated |
|---|---|---|---|---|---|---|---|
| **Billing** | `admin_ret_billing.php` | ✅ | ✅ | ✅ | ✅ | ✅ | 2026-02-18 |
| **Tagging** | `admin_ret_tagging.php` | ✅ | ✅ | ✅ | ✅ | ✅ | 2026-02-24 |
| **Estimation** | `admin_ret_estimation.php` | ✅ | ✅ | ✅ | ✅ | ✅ | 2026-02-18 |
| **Ret_Reports** | `admin_ret_reports.php` | ✅ | ✅ | ✅ | ✅ | ✅ | 2026-03-26 |
| **Branch Transfer** | `admin_ret_brntransfer.php` | ✅ | ✅ | ✅ | ✅ | ✅ | 2026-03-24 |
| **Stock Issue** | `admin_ret_stock_issue.php` | ✅ | ❌ | ✅ | ✅ | ✅ | 2026-03-19 |
| **Sales Transfer** | `admin_ret_sales_transfer.php` | ✅ | ✅ | ✅ | ✅ | ✅ | 2026-03-20 |
| **LOT** | `admin_ret_lot.php` | ✅ | ✅ | ✅ | ✅ | ❌ | 2026-03-17 |
| **Catalog_Inventory** | `admin_ret_catalog.php` | ✅ | ✅ | ✅ | ✅ | ✅ | 2026-03-13 |
| **Section Transfer** | `admin_ret_section_transfer.php` | ✅ | ✅ | ✅ | ✅ | ❌ | 2026-03-14 |
| **Old Metal Process** | `admin_ret_old_metal_process.php` | ✅ | ✅ | ✅ | ✅ | ❌ | 2026-03-14 |
| **Other Inventory** | `admin_ret_other_inventory.php` | ✅ | ✅ | ✅ | ❌ | ❌ | 2026-03-14 |
| **Retail Dashboard** | `admin_ret_dashboard.php` | ✅ | ❌ | ✅ | ✅ | ❌ | 2026-03-16 |
| **Retail Settings** | `admin_settings.php (retail)` | ✅ | ❌ | ✅ | ✅ | ❌ | 2026-03-16 |
| **Purchase** | `admin_ret_purchase.php` | ✅ | ❌ | ✅ | ✅ | ✅ | 2026-02-23 |
| **Customer** | `admin_customer.php` | ✅ | ✅ | ✅ | ✅ | ✅ | 2026-03-25 |
| **Customer Order** | `admin_ret_order.php` | ✅ | ✅ | ✅ | ✅ | ✅ | 2026-03-25 |
| **Employee** | `admin_employee.php` | ✅ | ✅ | ✅ | ✅ | ✅ | 2026-03-25 |
| **Masters** | `admin_settings.php (chit)` | ✅ | ✅ | ✅ | ✅ | ❌ | 2026-03-25 |
| **Payment** | `admin_payment.php` | ✅ R3 | ✅ | ✅ | ✅ | ✅ | 2026-03-24 |
| **Account** | `admin_manage.php` | ✅ R2 | ✅ | ✅ | ✅ | ✅ | 2026-03-24 |
| **Scheme** | `admin_scheme.php` | ✅ R2 | ✅ | ✅ | ✅ | ✅ | 2026-03-24 |
| **Chit Reports** | `admin_reports.php` | ✅ | ✅ | ✅ | ✅ | ✅ | 2026-03-16 |
| **Chit Dashboard** | `admin_dashboard.php` | ✅ | ✅ | ✅ | ✅ | ✅ | 2026-03-16 |
| **Chit Collection App** | `admin_collection_app.php` | ✅ | ✅ | ✅ | ✅ | ✅ | 2026-03-16 |
| **Chit Customer App** | `admin_customer_app.php` | ✅ | ❌ | ✅ | ✅ | ✅ | 2026-03-16 |
| **Chit Services** | `admin_services.php` | ✅ | ❌ | ✅ | ❌ | ❌ | 2026-03-16 |
| **Chit Settings** | `admin_settings.php` | ✅ | ❌ | ✅ | ✅ | ❌ | 2026-03-06 |

---

## Coverage Summary

| Metric | Count | Notes |
|---|---|---|
| Total module brains | **28** | All with MODULE_BRAIN.md, CROSS_MODULE_MAP.md, METHOD_INDEX.md |
| FLOW_RISK_MATRIX coverage | **21/28** (75%) | Missing: Stock Issue, Retail Dashboard, Retail Settings, Purchase, Chit Customer App, Chit Services, Chit Settings |
| SCHEMA_ANALYSIS coverage | **27/28** (96%) | All except chit_services |
| FORENSIC_TEMPLATE coverage | **26/28** (93%) | Missing: Other Inventory, Chit Services |
| INVARIANT_MATRIX coverage | **22/28** (79%) | Missing: LOT, Section Transfer, Old Metal, Other Inventory, Retail Dashboard, Retail Settings |
| Brain coverage | **100%** | 28/28 modules |
| Shared tables documented | **36** | See SHARED_TABLES.md |
| Cross-module dependency links | **50+** | See MODULE_DEPENDENCIES.md |
| Active cross-module bugs | **113** | See CROSS_MODULE_BUGS.md (was 108 in R12) |
| Resolved cross-module bugs | **1** | See CROSS_MODULE_BUGS.md |
| Validation gaps | **209** | See VALIDATION_GAPS.md (was 197 in R12) |
| Cleanup gaps | **26** | See CLEANUP_GAPS.md |
| Performance risks | **16** | See PERFORMANCE_RISKS.md |
| Handoff gaps | **11** | See HANDOFF_AUDIT.md |
| Danger zones | **57** | See DANGER_ZONES.md (was 55 in R12) |
| Diagnostic playbooks | **9** | See DIAGNOSTIC_PLAYBOOK.md |

---

## Brain Quality Ratings

| Module | Quality | Notes |
|---|---|---|
| Payment | ⭐⭐⭐⭐⭐ (R3) | Full upgrade, FLOW_RISK_MATRIX, all docs |
| Account | ⭐⭐⭐⭐⭐ (R2) | Full upgrade, FLOW_RISK_MATRIX, all docs |
| Scheme | ⭐⭐⭐⭐⭐ (R2) | Full upgrade, FLOW_RISK_MATRIX, all docs |
| Ret_Reports | ⭐⭐⭐⭐⭐ (R8) | Latest refresh, FLOW_RISK_MATRIX complete |
| Branch Transfer | ⭐⭐⭐⭐ (R10) | High round, full docs |
| Customer | ⭐⭐⭐⭐ (R3) | Recent upgrade, all docs |
| Employee | ⭐⭐⭐⭐ (R2) | Recent upgrade, all docs |
| Masters | ⭐⭐⭐⭐ (R5) | Full coverage, missing INVARIANT |
| Tagging | ⭐⭐⭐ (70%) | Partial JS coverage |
| Estimation | ⭐⭐⭐ (70%) | Deep audit not done |
| Billing | ⭐⭐⭐ (60%) | Cross-module JS AJAX incomplete |
| Stock Issue | ⭐⭐⭐ (R11) | High round but missing FLOW_RISK_MATRIX |

---

## Refresh History

| Date | Mode | Modules Scanned | Changes |
|---|---|---|---|
| 2026-03-26 | **Round 13** | 28 modules | BUSINESS_RULES (5: CatalogInv, RetReports, ChitSettings, ChitCustApp, RetailSettings). +12 VAL, +5 XMOD, +2 DZ. Image MIME bypass, cash ₹2L not enforced, OTP brute-force, KYC client-side. **🌟 ALL 5 SOURCES AT 100% 🌟** |
| 2026-03-26 | **Round 12** | 28 modules | SCHEMA (1) + BUSINESS_RULES (11). +18 VAL, +10 XMOD, +4 DZ. **SCHEMA 100%. BUSINESS_RULES 81%.** |
| 2026-03-26 | **Round 6** | 28 modules | FORENSIC (4) + INVARIANT (4). +15 VAL, +6 XMOD, +3 DZ. |
| 2026-03-26 | **Round 5** | 28 modules | FORENSIC (7) + INVARIANT (1). +17 VAL, +8 XMOD, +4 DZ, +4 CLN. |
| 2026-03-26 | **Round 4** | 28 modules | FORENSIC (4) + INVARIANT (4). +12 VAL, +7 XMOD, +3 DZ. |
| 2026-03-26 | **Round 3** | 28 modules | SCHEMA (8) + FORENSIC (4). +18 VAL, +10 XMOD, +7 CLN, +5 DZ. |
| 2026-03-26 | **Round 2** | 28 modules | +12 VAL, +8 XMOD, +4 CLN, +6 PERF, +5 DZ. |
| 2026-03-26 | **Round 1** | 28 modules | Created 14 _SYSTEM docs. |

---

## Next Recommended Actions

| Priority | Action | Reason |
|---|---|---|
| **P0** | Fix XMOD-004: OTP `=` vs `==` in Account/Payment | Always-true OTP bypass |
| **P0** | Fix XMOD-005: Add `.htaccess` to `admin/log/` | 31 PII files web-accessible |
| **P0** | Fix XMOD-007: SQL injection in chit_reports | Raw param concat |
| **P0** | Fix XMOD-012: `clear_database()` no OTP guard | Catastrophic risk |
| **P0** | Fix XMOD-013: SQLi in RBAC `get_access()` | Core security |
| **P0** | Fix XMOD-020: Password base64 → hash | Reversible passwords |
| **P0** | Fix XMOD-021: Drop `payment.cvv` column | PCI-DSS |
| **P0** | Fix XMOD-028: Sales Transfer SQLi (17 methods) | No parameterized queries |
| **P0** | Fix XMOD-030: ST return defaults | Returns appear non-credit |
| **P0** | Fix XMOD-031: LOT GET delete CSRF | Link click deletes |
| **P0** | Fix XMOD-033: Estimation trans_commit error | Partial saves |
| **P0** | Fix XMOD-037: Employee base64 passwords | Higher privilege |
| **P0** | Fix XMOD-038: Tagging delete sold tags | Inventory vanishes |
| **P0** | Fix XMOD-041: Stock Issue OTP leak | OTP bypass |
| **P0** | Fix XMOD-043: ST NULL bill_date | NULL on transfers |
| **P0** | Fix XMOD-044: ST no trans_begin | 3 methods |
| **P0** | Fix XMOD-045: Account gift OTP `=` | Any OTP accepted |
| **P0** | Fix XMOD-046: Account 5 OTP leaks | 5x security |
| **P0** | Fix XMOD-048: Masters clear_database | Any user wipes DB |
| **P0** | Fix XMOD-051: Purchase echo exit | Rollback killed |
| **P0** | Fix XMOD-053: OMP NB payment wrong field | Wrong amount |
| **P0** | Fix XMOD-055: OMP melting commented | Status broken |
| **P0** | Fix XMOD-057: Scheme $id undefined | NULL FK |
| **P0** | Fix XMOD-059: Chit API no auth | Full IDOR |
| **P0** | Fix XMOD-060: Hardcoded OTP 123456 | Universal bypass |
| **P0** | Fix XMOD-061: Wallet debit no rollback | Money loss |
| **P0** | Fix XMOD-062: CORS wildcard | Cross-origin abuse |
| **P0** | Fix XMOD-063: Log dir PII exposed | Data breach |
| **P0** | Fix XMOD-067: Cashfree sig off | Forged callbacks |
| **P0** | Fix XMOD-068: Ippo attacker creds | Payment forgery |
| **P0** | Fix XMOD-069: Dashboard arbitrary write | Any-table update |
| **P0** | Fix XMOD-070: Lot delete orphans | Phantom inventory |
| **P0** | Fix XMOD-075: BT cancel 0/8 reversal | Permanent inv corruption |
| **P0** | Fix XMOD-076: FinYear no transaction | System-wide fin failure |
| **P0** | Fix XMOD-079: ST→Billing home counter | Stock corruption |
| **P0** | Fix XMOD-080: ST stock direction wrong | Negative stock |
| **P0** | Fix XMOD-083: rate.txt writes Array | Mobile rates broken |
| **P0** | Fix XMOD-087: Customer base64 passwd | Credential exposure |
| **P0** | Fix XMOD-088: OTP `=` assignment | OTP always passes |
| **P0** | Fix XMOD-091: Payment OTP `=` assignment | OTP always passes |
| **P0** | Fix XMOD-095: Employee username dead code | Duplicate usernames |
| **P0** | Fix XMOD-098: POS cancel no provider notify | Settlement mismatch |
| **P0** | Fix XMOD-101: BT cancel restores nothing | Stock corruption |
| **P0** | Fix XMOD-102: Stock Issue OTP in response | OTP bypass |
| **P0** | Fix XMOD-103: Collection OTP hardcoded 123456 | OTP disabled |
| **P0** | Fix XMOD-104: `clear_database()` no auth | Full DB wipe |
| **P0** | Fix XMOD-106: admin/log/ web accessible | PII exposure |
| **P0** | Fix XMOD-108: `get_access()` SQLi | SQL injection |
| **P0** | Fix XMOD-110: Image MIME bypass | Code execution |
| **P0** | Fix VAL-028: Customer search SQLi | User input in SQL |
| **P0** | Fix VAL-029: Customer KYC path traversal | File read |
| **P1** | Fix XMOD-022: Mask `payment.card_no` to last-4 | PCI risk |
| **P1** | Fix XMOD-029: Sales Transfer decimal truncation | Financial precision loss |
| **P1** | Fix XMOD-035: Scheme double trans_commit | Partial commits |
| **P1** | Fix XMOD-036: Scheme delete child cleanup | 9 orphaned table groups |
| **P1** | Fix VAL-034: Metal rate zero-check in `amount_to_weight()` | Division by zero crash |
| **P1** | Fix HO-009: Payment delete → installment count update | Financial integrity |
| **P1** | Build FLOW_RISK_MATRIX for Stock Issue, Purchase, Retail Dashboard | 3 critical modules missing flow contracts |
| **P2** | Add INVARIANT_MATRIX to LOT, Section Transfer, Old Metal | Missing constraint documentation |
| **P3** | Next refresh: 2026-06-26 | Quarterly schedule |
