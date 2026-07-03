## Hypothesis
The complete autodebit flow spans 3 frontend controllers (`cf_autodebit.php`, `chitscheme.php`, `mobile_api.php`), 2 frontend models (`scheme_modal`, `payment_modal`), 3 admin controllers, 7 database tables, and 2 coexisting Cashfree API versions (v2 legacy + v2025-01-01 new). The documentation will consolidate all touchpoints into a single comprehensive flow document covering the full subscription lifecycle.

## Approach
This is a documentation-only task. The autodebit (Cashfree Subscription) feature is **already fully implemented** across frontend controllers, models, and views. The documentation will be produced by analyzing the existing codebase, mapping all touchpoints end-to-end, and consolidating them into a single comprehensive flow document.

## Reference Implementation
- **Primary controller**: `application/controllers/cf_autodebit.php` (frontend, 813 lines)
- **Subscription creation (web)**: `application/controllers/chitscheme.php` L1870-1940
- **Subscription creation (mobile)**: `application/controllers/mobile_api.php` L7910-8241
- **Primary model**: `application/models/scheme_modal.php` L1839-1934
- **Admin report**: `admin/application/controllers/admin_reports.php` L1111-1134
- **Admin settings**: `admin/application/controllers/admin_settings.php` + `admin/application/views/settings/general/form.php` L577-604
- **Scheme config**: `admin/application/controllers/admin_scheme.php` + `admin/application/views/master/scheme/form.php` L76-86

## Existing Files (Read-Only — For Documentation)

### Frontend Controllers
| File | Function(s) | Line | Purpose |
|---|---|---|---|
| `application/controllers/cf_autodebit.php` | `autoDebitRURL()` | L70 | Return URL handler after mandate authorization |
| | `authorize()` | L147 | SDK-based authorization page (loads CF JS SDK) |
| | `cf_authRedirect()` | L178 | Mobile redirect placeholder |
| | `wh_response()` | L192 | **Webhook handler** — processes all 3 event types |
| | `cf_retry()` | L373 | Charge retry — calls CF API and creates payment |
| | `cf_curl()` | L497 | Cashfree Subscription API v2 cURL wrapper |
| | `createPayment()` | L531 | **Core payment creation** — receipt, account no, referral, sync |
| | `insert_common_data_jil()` | L654 | JIL intermediate table sync |
| | `insert_common_data_old()` | L690 | Standard intermediate table sync |
| | `generate_receipt_no()` | L736 | Receipt number generator |
| | `insert_referral_data()` | L758 | Referral benefit processing |
| `application/controllers/chitscheme.php` | `cf_subscription()` | L1870-1940 | **Web app subscription creation** — creates DB record, calls CF API, stores auth_link |
| `application/controllers/mobile_api.php` | `cf_subscription_post()` | L7910-8176 | **Mobile app subscribe/unsubscribe** — new v2025-01-01 API |
| | `_cf_api_call()` | L8182 | New CF API cURL wrapper (v2025-01-01 headers) |
| | `_cf_random_strings()` | L8223 | Dummy email generator |
| | `_cf_logToFile()` | L8232 | Subscription action logger |

### Frontend Models
| File | Function | Line | Purpose |
|---|---|---|---|
| `application/models/scheme_modal.php` | `get_plan_detail()` | L1839 | Gets scheme account + customer + subscription data for creation |
| | `get_subscriptionData()` | L1860 | Gets subscription + gateway data for cancel/retry |
| | `get_subsDetail()` | L1869 | Gets full subscription detail for webhook processing |
| | `isPaymentAlreadyExist()` | L1888 | Duplicate payment check by `payment_ref_number` |
| | `getSubscriptionStatus()` | L1928 | Gets current auth_status from `auto_debit_subscription` |
| | `insertData()` | L1897 | Generic insert into any table |
| | `updateData()` | L1902 | Generic update on any table |

### Frontend Views
| File | Purpose |
|---|---|
| `application/views/cashsfree/cf_authorize.php` | SDK-based authorization page — loads Cashfree JS SDK v3, auto-triggers `subscriptionsCheckout()` |
| `application/views/cashsfree/cf_subscription.php` | Subscription status display (web app) |
| `application/views/chitscheme/my_schemes.php` | My Schemes list — subscribe/unsubscribe/authorize/retry buttons |
| `application/views/chitscheme/scheme_acc_details.php` | Account detail page — subscription status card |

### Admin Controllers
| File | Function | Line | Purpose |
|---|---|---|---|
| `admin/application/controllers/admin_reports.php` | `get_autodebit_subscription()` | L1112 | Renders subscription report view |
| | `ajax_get_autodebit_subscription()` | L1124 | AJAX endpoint — loads `autodebit_model`, queries subscription data |
| `admin/application/controllers/admin_scheme.php` | (save methods) | L324, L719 | Saves `auto_debit_plan_type` to `scheme` table |
| `admin/application/controllers/admin_settings.php` | (save methods) | L1947-1948 | Saves `auto_debit`, `auto_debit_allow_app_pay` to `chit_settings` |

### Admin Views & JS
| File | Purpose |
|---|---|
| `admin/application/views/reports/autodebit_subscription_report.php` | DataTable report — Branch, Customer, Mobile, A/C Name, A/C No., Status, Last Updated |
| `admin/application/views/master/scheme/form.php` L76-86 | Auto Debit Plan Type selector (0=NA, 1=Periodic, 2=OnDemand) |
| `admin/application/views/settings/general/form.php` L577-604 | Auto Debit Settings — Active/Inactive toggle + App Payment Policy |
| `admin/assets/js/reports.js` L8312-8356 | `get_autodebit_subscription()` AJAX + `set_autodebit_subscription()` DataTable renderer |

### Missing Files (Referenced but Don't Exist)
| File | Referenced By | Impact |
|---|---|---|
| `admin/application/models/autodebit_model.php` | `admin_reports.php` L1126 | **MISSING** — `ajax_get_autodebit_subscription()` will crash |
| `admin/application/controllers/Autodebit.php` | `routes.php` L1648-1654 | **MISSING** — Routes for subscription CRUD all point to non-existent controller |

## Database

### Tables Used (Existing)
| Table | Relevant Columns | Purpose |
|---|---|---|
| `auto_debit_subscription` | `id_auto_debit_subscription`, `id_scheme_account`, `subscription_id`, `sub_reference_id`, `plan_id`, `first_charge_delay`, `expires_on`, `auth_status`, `auth_link`, `message`, `status`, `created_on`, `added_by`, `last_update` | Core subscription record |
| `scheme_account` | `auto_debit_status`, `date_upd` (+ standard cols) | Extended with autodebit status mirror |
| `scheme` | `auto_debit_plan_type` (0=NA, 1=Periodic, 2=OnDemand) | Scheme-level plan type config |
| `chit_settings` | `auto_debit` (0/1), `auto_debit_allow_app_pay` (0=Block, 1=Allow, 2=Conditional) | Global toggle + app payment policy |
| `gateway` | `param_1` (secret), `param_3` (app ID), `api_url`, `id_pg`, `pg_code=4`, `is_default`, `id_branch` | Cashfree gateway credentials per branch |
| `payment` | `added_by=4` (Cashfree Sub), `payment_ref_number`, `id_payGateway`, standard payment cols | Payment records from subscription |

### Status Code Mappings
**`auth_status`** / **`auto_debit_status`**:
| Code | Status | Description |
|---|---|---|
| 0 | NOT_SUBSCRIBED | No subscription exists |
| 1 | INITIALIZED | Created, awaiting authorization |
| 2 | BANK_APPROVAL_PENDING | Customer authorized, bank processing |
| 3 | ACTIVE | Mandate active, charges deducted |
| 4 | ON_HOLD | Subscription paused |
| 5 | CANCELLED | Cancelled |
| 6 | COMPLETED | All cycles done |

**`payment_status`** (for autodebit payments):
| Code | Status |
|---|---|
| 1 | Success |
| 2 | Awaiting |
| 3 | Failure |
| 4 | Cancel |
| 7 | Pending |

**`payment.added_by`**: 0=Admin, 1=Web App, 2=Mobile App, 3=Admin App, **4=Cashfree Subscription**, 5=Sync

**`auto_pay_approval` config**: 1=Auto-approve, 2=Auto-approve+sync, Other=Manual approval (awaiting)

## Two Cashfree API Versions Coexist

### V2 (Legacy — `chitscheme.php` + `cf_autodebit.php`)
- Endpoint: `{api_url}api/v2/subscriptions/`
- Headers: `X-Client-Id`, `X-Client-Secret`, Content-Type: `application/x-www-form-urlencoded`
- Auth flow: API returns `authLink` (cfre.in URL) → customer redirects to hosted page
- Return URL: `cf_autodebit/autoDebitRURL/{W|M}/{id_sch_ac}`

### V2025-01-01 (New — `mobile_api.php`)
- Endpoint: `{base_domain}/pg/subscriptions`
- Headers: `x-client-id`, `x-client-secret`, `x-api-version: 2025-01-01`, Content-Type: `application/json`
- Auth flow: API returns `subscription_session_id` → stored in `auth_link` → server-hosted authorize page loads CF JS SDK v3 → `cashfree.subscriptionsCheckout({subsSessionId})`
- Return URL: `cf_autodebit/autoDebitRURL/M/{id_sch_ac}`

## Impacted Files
| File | Function | Line | Impact |
|---|---|---|---|
| `application/controllers/cf_autodebit.php` | `wh_response()` | L192-371 | Core webhook handler |
| `application/controllers/cf_autodebit.php` | `autoDebitRURL()` | L70-136 | Return URL after authorization |
| `application/controllers/cf_autodebit.php` | `authorize()` | L147-176 | SDK authorization page |
| `application/controllers/cf_autodebit.php` | `cf_retry()` | L373-495 | Charge retry mechanism |
| `application/controllers/cf_autodebit.php` | `createPayment()` | L531-650 | Payment creation with all side-effects |
| `application/controllers/chitscheme.php` | `cf_subscription()` | L1870-1940 | Web app subscription creation |
| `application/controllers/mobile_api.php` | `cf_subscription_post()` | L7910-8241 | Mobile subscribe/unsubscribe |
| `application/models/scheme_modal.php` | 7 functions | L1839-1934 | All subscription DB operations |
| `admin/application/controllers/admin_reports.php` | 2 functions | L1111-1134 | Admin report |
| `admin/application/controllers/admin_scheme.php` | (save) | L324,L719 | Saves auto_debit_plan_type |
| `admin/application/controllers/admin_settings.php` | (save) | L1947-1948 | Saves auto_debit settings |

## Cross-Module Risk
- [x] **Affects other modules**: Scheme, Payment, Gateway, Settings, Services (SMS), Sync/Integration, Customer
- [x] **Shared tables**: `payment`, `scheme_account`, `gateway`, `chit_settings`
- [x] **Shared models**: `scheme_modal`, `payment_modal`, `services_modal`, `admin_settings_model`

## Self-Review Checklist
- [x] I found and documented the closest reference implementation (the feature itself — already built)
- [x] I'm documenting existing patterns, not inventing new ones
- [x] I verified this feature fully exists already (documentation-only task)
- [x] I identified all DB tables this touches (7 tables)
- [x] I identified missing files that will cause runtime errors (autodebit_model.php, Autodebit controller)
- [x] I traced the complete end-to-end flow: subscription creation → authorization → webhook → payment → retry → reporting

## Step 3: INVESTIGATE (completed 17:38)


## Step 4: TEST_DESIGN (completed 17:40)


## Step 5: PLAN (completed 17:47)


## Step 6: REVIEW (completed 18:04)


## Step 7: APPROVAL (completed 10:26)


## Step 8: CODE (completed 10:55)


## Verification

### Generated Test
- File: `tests/tests/test_autodebit_documentation.py`
- Result: PASS ✅
- Output: `1 passed in 0.05s`

### Document Verification
- Markdown File: `knowledge_brain/autodebit/AUTODEBIT_FLOW.md` created ✅
- PDF File: `knowledge_brain/autodebit/AUTODEBIT_FLOW.pdf` created ✅
- Manual Test Cases (TC-001 to TC-008): PASS ✅ (All documented)

### Overall Verdict: PASS ✅

## Step 9: VERIFY (completed 17:01)

## Rollback
- **Git Revert**: `git revert HEAD` (after commit is made)
- **Affected Files**:
  - `knowledge_brain/autodebit/AUTODEBIT_FLOW.md`
  - `knowledge_brain/autodebit/AUTODEBIT_FLOW.pdf`
  - `tests/tests/test_autodebit_documentation.py`
  - `.sdlc/active/AUT-2607021723/*`
- **Verification**: Run `python -m pytest tests/tests/test_autodebit_documentation.py` (should fail as file will be removed).
