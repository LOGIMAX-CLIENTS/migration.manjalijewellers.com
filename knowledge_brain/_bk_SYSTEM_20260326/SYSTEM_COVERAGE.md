# System Brain Coverage
> Last updated: 2026-03-24
> Latest: Account brain upgraded to Round 2

---

## Module Coverage

| Module | Controller | Brain? | Audit? | Bug Fixes? | Coverage % | Last Brain Update |
|---|---|---|---|---|---|---|
| **chit_settings** | `admin_settings.php` (4,709 lines) | ✅ | ❌ | ❌ | ~80% | 2026-03-06 |
| **scheme** | `admin_scheme.php` (1,240 lines) | ✅ R2 | ❌ | ❌ | 100% | 2026-03-24 |
| **account** | `admin_manage.php` (4,476 lines) | ✅ R2 | ❌ | ❌ | 100% | 2026-03-24 |
| **payment** | `admin_payment.php` (6,237 lines) | ✅ R3 | ❌ | ✅ (3 bugs fixed) | 100% | 2026-03-24 |
| **chit_reports** | `admin_reports.php` (2,137 lines) | ✅ | ❌ | ✅ (1 bug fixed) | 98% | 2026-03-16 |
| **Billing** | `admin_ret_billing.php` | ✅ | ❌ | ❌ | ~60% | 2026-02-18 |
| **Estimation** | `admin_ret_estimation.php` | ✅ | ❌ | ❌ | ~70% | 2026-02-18 |
| **Tagging** | `admin_ret_tagging.php` | ✅ | ❌ | ❌ | ~70% | 2026-02-24 |
| **purchase** | `ret_purchase_order_model.php` | ❌ | ❌ | ❌ | 0% | — |
| **Customer** | `admin_customer.php` | ❌ | ❌ | ❌ | 0% | — |
| **Employee** | `admin_employee.php` | ❌ | ❌ | ❌ | 0% | — |
| **Mobile API** | `mobile_api.php` | ❌ | ❌ | ❌ | 0% | — |
| **Dashboard** | `admin_dashboard.php` | ❌ | ❌ | ❌ | 0% | — |
| **Wallet** | `Wallet_model.php` | ❌ | ❌ | ❌ | 0% | — |
| **Lot Inward** | Various | ❌ | ❌ | ❌ | 0% | — |
| **Branch Transfer** | `ret_brntransfer_model.php` | ❌ | ❌ | ❌ | 0% | — |

---

## Coverage Summary

| Metric | Count | Notes |
|---|---|---|
| Total known modules | ~16 | Estimated from cross-module map references |
| Modules with brain | **9** | account, Billing, chit_reports, chit_settings, Estimation, payment, scheme, Tagging |
| Modules with bug audit | 0 | Not yet started |
| Modules with bug fixes | 2 | payment (3 fixes), chit_reports (1 fix) |
| Brain coverage | **56%** | 9/16 modules (3 at 100%: payment R3, scheme R2, account R2) |
| Shared tables documented | 25 | See SHARED_TABLES.md |
| Cross-module dependency links | 32 | See MODULE_DEPENDENCIES.md |
| Active cross-module bugs | **12** | See CROSS_MODULE_BUGS.md |
| Resolved cross-module bugs | 3 | See CROSS_MODULE_BUGS.md |

---

## Brain Quality Ratings

| Module | Brain Quality | Missing |
|---|---|---|
| payment | ⭐⭐⭐⭐⭐ (100%) | Nothing — R3 upgrade complete, FLOW_RISK_MATRIX.md added |
| chit_reports | ⭐⭐⭐⭐⭐ (98%) | `admin/log/` .htaccess (action required) |
| account | ⭐⭐⭐⭐⭐ (100%) | Nothing — R2 upgrade complete, FLOW_RISK_MATRIX.md added |
| chit_settings | ⭐⭐⭐⭐ (80%) | Bug audit not done |
| Tagging | ⭐⭐⭐ (70%) | Partial JS coverage, some R5 findings incomplete |
| Estimation | ⭐⭐⭐ (70%) | Deep bug audit not done |
| scheme | ⭐⭐⭐⭐⭐ (100%) | Nothing — R2 upgrade complete, FLOW_RISK_MATRIX.md added |
| Billing | ⭐⭐⭐ (60%) | Cross-module JS AJAX incomplete, no forensic template |
| **purchase** | ❌ (0%) | **No brain** |

---

## Refresh History

| Date | Mode | Modules Scanned | Changes |
|---|---|---|---|
| 2026-03-16 | **Initial Build** | 9 modules | Created all `_SYSTEM/` documents (SHARED_TABLES, MODULE_DEPENDENCIES, SHARED_MODELS, CROSS_MODULE_BUGS, SYSTEM_COVERAGE) |
| 2026-03-24 | **Upgrade** | payment | Payment brain upgraded R2→R3: diffed 3 commits, added FLOW_RISK_MATRIX.md, +2 business rules, +1 invariant dimension, +3 forensic entries |
| 2026-03-24 | **Upgrade** | scheme | Scheme brain upgraded R1→R2: diffed 1 commit (view DOM ID fix), added FLOW_RISK_MATRIX.md (2 state machines, 16 contracts, 16 QA scenarios, 18% delete reversal score) |
| 2026-03-24 | **Upgrade** | account | Account brain upgraded R1→R2: diffed 2 commits (`formatMetalWeight()` centralization + model `is_weight_scheme`), added FLOW_RISK_MATRIX.md (4 state machines, 18 contracts, 12.5% delete reversal, 50% revert reversal, 18 QA scenarios) |

---

## Next Recommended Actions

| Priority | Action | Reason |
|---|---|---|
| **P0 IMMEDIATE** | Add `.htaccess` to `admin/log/` | 31 PII log files web-accessible (XMOD-005) |
| **P0 IMMEDIATE** | Fix SQL injection in `admin_report_model::get_customerenquiry_by_date` | Raw `$status`/`$type` concat (XMOD-007) |
| **P0 IMMEDIATE** | Fix OTP bypass `=` vs `==` in Account L4188 + Payment L5081 | Always evaluates true (XMOD-004) |
| **P1** | Fix OTP returned in JSON response (5 endpoints) | Security: XMOD-003 |
| **P1** | Fix `cancel_payment` — add transaction wrapping | Data integrity: XMOD-001 |
| **P2** | Build brain for `purchase` module | 0% coverage, referenced by billing/payment/reports |
| **P2** | Build brain for `Customer` module | Universal dependency, no documentation |
| **P2** | Run `/module-bug-audit` on `chit_reports` | Brain at 98% — ready for audit |
| **P3** | Build brain for `Mobile API`, `Dashboard`, `Wallet` | Referenced by payment and account |
