# Scheme Module — Cross-Module Map
> **Round**: 2 | **Date**: 2026-03-24

---

## 1. Module Dependencies (Outbound)

| Module | Model Used | Purpose | Methods Called |
|---|---|---|---|
| **Account** | `account_model` | Check lump sum slabs, check account counts | `get_joinTime_weight_slabs()` |
| **Settings** | `admin_settings_model` | Limits, discounts, GST, access control | `limitDB()`, `discount_db()`, `get_gstsettings()`, `get_access()` |
| **Payment** | `payment_model` | Metal rate lookup for scheme business | `get_metalrate_by_branch()` |
| **Log** | `log_model` | Audit trail | `log_detail()` |

---

## 2. Module Dependencies (Inbound — Other modules reading scheme data)

| Calling Module | What It Reads | Model Method Used |
|---|---|---|
| **Account (admin_manage.php)** | Scheme rules for account operations | `get_scheme()`, `get_schemes()`, `get_scheme_active()` |
| **Payment (admin_payments.php)** | Scheme type, installments for payment calc | `get_scheme()`, `get_scheme_active()` |
| **Mobile API** | Scheme list for customer app | `get_schemes()` via API |
| **Settlement** | Fix-weight schemes for settlement | `get_fixweight_schemes()` |
| **Settings** | Scheme count for limits | `scheme_count()` |
| **Reports** | Scheme data for reports | Various scheme queries |

---

## 3. External AJAX Calls

| Endpoint | Called From | Response |
|---|---|---|
| `scheme/ajax_scheme_list` | Scheme list page JS | JSON (schemes + access) |
| `scheme/get_metals` | Scheme form JS | JSON (metals dropdown) |
| `scheme/get_classifications` | Scheme form JS | JSON (classifications) |
| `scheme/get_branches` | Scheme form JS | JSON (branches) |
| `scheme/get_units` | Scheme form JS | JSON (installment amounts) |
| `scheme/get_schemes` | Account form JS | JSON (active schemes) |
| `scheme/get_scheme/:id` | Account form JS | JSON (scheme business data) |
| `scheme/get/fix_schemes` | Settlement page JS | JSON (fix-weight schemes) |
| `admin_scheme/getActivePuritiesByMetal` | Scheme form JS | JSON (purities) |
| `admin_scheme/joinTime_weight_slabs` | Account form JS | JSON (weight slabs) |
| `admin_scheme/get_weight_list` | Scheme form JS | JSON (weight records) |
| `admin_scheme/checkDigiAvailability` | Scheme form JS | JSON (bool) |
| `admin_scheme/getFreeInsBySchId/:id` | Account form JS | JSON (free installments) |
| `admin_scheme/get_branch_edit/:id` | Scheme form JS | JSON (branch IDs) |
| `admin_scheme/gstsplitupinsert` | Admin utility | JSON (count) |

---

## 4. Referenced Tables (Full List)

| Table | Owner Module | Read | Write | Delete |
|---|---|---|---|---|
| `scheme` | **Scheme** | ✅ | ✅ | ✅ |
| `scheme_branch` | **Scheme** | ✅ | ✅ | ✅ |
| `scheme_benefit_deduct_settings` | **Scheme** | ✅ | ✅ | ✅ |
| `scheme_debit_settings` | **Scheme** | ✅ | ✅ | ✅ |
| `scheme_agent_benefit` | **Scheme** | ✅ | ✅ | ✅ |
| `scheme_incentive_settings` | **Scheme** | ✅ | ✅ | ✅ |
| `scheme_flexi_settings` | **Scheme** | ✅ | ✅ | ✅ |
| `emp_closing_incentive` | **Scheme** | ✅ | ✅ | ✅ |
| `scheme_general_advance_benefit_settings` | **Scheme** | ✅ | ✅ | ✅ |
| `scheme_custom_payable_settings` | **Scheme** | ✅ | ✅ | — |
| `gst_splitup_detail` | **Scheme** | ✅ | ✅ | ✅ |
| `sch_classify` | **Scheme** | ✅ | — | — |
| `metal` | Shared | ✅ | — | — |
| `branch` | Shared | ✅ | — | — |
| `chit_settings` | Settings | ✅ | — | — |
| `ret_metal_purity_rate` | Shared | ✅ | — | — |
| `ret_purity` | Shared | ✅ | — | — |
| `weight` | Shared | ✅ | — | — |
| `scheme_account` | Account | ✅ | — | — |
| `payment` | Payment | ✅ | — | — |
| `metal_rates` | Shared | ✅ | — | — |
| `log_detail` | Log | — | ✅ | — |

---

## 5. Dependency Diagram

```
                    ┌──────────────────────┐
                    │   Settings Module    │
                    │ (admin_settings_model)│
                    └──────────┬───────────┘
                               │ reads limits, GST, access
                               ▼
┌──────────────┐    ┌──────────────────────┐    ┌──────────────┐
│ Account      │◀───│   SCHEME MODULE      │───▶│ Payment      │
│ Module       │    │ (admin_scheme.php)    │    │ Module       │
│ reads rules  │    │ (scheme_model.php)    │    │ reads rates  │
└──────────────┘    └──────────┬───────────┘    └──────────────┘
                               │
                    ┌──────────┴───────────┐
                    │    11 Owned Tables     │
                    │  + 10 Referenced       │
                    └──────────────────────┘
```
