# Recipe: autodebit-aut-2607021723

**Module**: autodebit
**Type**: feature
**Symptom**: Document complete autodebit flow - subscription creation, authorization, webhook handling, payment processing, retry, and admin reporting
**Created**: 2026-07-03
**Source Task**: AUT-2607021723

## Root Cause

This is a documentation-only task. The autodebit (Cashfree Subscription) feature is **already fully implemented** across frontend controllers, models, and views. The documentation will be produced by analyzing the existing codebase, mapping all touchpoints end-to-end, and consolidating them into a single comprehensive flow document.

## Fix

### Files Changed

- `admin/application/controllers/admin_reports.php`
- `admin/application/controllers/admin_scheme.php`
- `admin/application/controllers/admin_settings.php`
- `admin/application/views/reports/autodebit_subscription_report.php`
- `admin/application/views/master/scheme/form.php`
- `admin/application/views/settings/general/form.php`

### Diff Summary
```
.sdlc/active/AUT-2607021723/context.md            | 199 ++++++++++++++
 .sdlc/active/AUT-2607021723/discovery.md          | 305 ++++++++++++++++++++++
 .sdlc/active/AUT-2607021723/requirement.md        | 101 +++++++
 .sdlc/active/AUT-2607021723/requirement_review.md |  98 +++++++
 .sdlc/active/AUT-2607021723/review.md             |  47 ++++
 .sdlc/active/AUT-2607021723/test_cases.md         |  79 ++++++
 CLAUDE.md                                         |  56 ++++
 admin/application/models/scheme_model.php         |   1 +
 admin/application/views/master/scheme/form.php    |   2 +-
 admin/assets/js/scheme.js                         |  74 ++----
 knowledge_brain/autodebit/AUTODEBIT_FLOW.md       | 253 ++++++++++++++++++
 knowledge_brain/autodebit/AUTODEBIT_FLOW.pdf      |  45 ++++
 tests/tests/test_autodebit_documentation.py       |   3 +
 13 files changed, 1206 insertions(+), 57 deletions(-)
```

### Diff Detail
```diff
diff --git a/.sdlc/active/AUT-2607021723/context.md b/.sdlc/active/AUT-2607021723/context.md
new file mode 100644
index 0000000..bb5d35d
--- /dev/null
+++ b/.sdlc/active/AUT-2607021723/context.md
@@ -0,0 +1,199 @@
+## Hypothesis
+The complete autodebit flow spans 3 frontend controllers (`cf_autodebit.php`, `chitscheme.php`, `mobile_api.php`), 2 frontend models (`scheme_modal`, `payment_modal`), 3 admin controllers, 7 database tables, and 2 coexisting Cashfree API versions (v2 legacy + v2025-01-01 new). The documentation will consolidate all touchpoints into a single comprehensive flow document covering the full subscription lifecycle.
+
+## Approach
+This is a documentation-only task. The autodebit (Cashfree Subscription) feature is **already fully implemented** across frontend controllers, models, and views. The documentation will be produced by analyzing the existing codebase, mapping all touchpoints end-to-end, and consolidating them into a single comprehensive flow document.
+
+## Reference Implementation
+- **Primary controller**: `application/controllers/cf_autodebit.php` (frontend, 813 lines)
+- **Subscription creation (web)**: `application/controllers/chitscheme.php` L1870-1940
+- **Subscription creation (mobile)**: `application/controllers/mobile_api.php` L7910-8241
+- **Primary model**: `application/models/scheme_modal.php` L1839-1934
+- **Admin report**: `admin/application/controllers/admin_reports.php` L1111-1134
+- **Admin settings**: `admin/application/controllers/admin_settings.php` + `admin/application/views/settings/general/form.php` L577-604
+- **Scheme config**: `admin/application/controllers/admin_scheme.php` + `admin/application/views/master/scheme/form.php` L76-86
+
+## Existing Files (Read-Only â€” For Documentation)
+
+### Frontend Controllers
+| File | Function(s) | Line | Purpose |
+|---|---|---|---|
+| `application/controllers/cf_autodebit.php` | `autoDebitRURL()` | L70 | Return URL handler after mandate authorization |
+| | `authorize()` 
```

## Verification

- Web app flow via `chitscheme.php::cf_subscription()` is documented with correct API call sequence
- Mobile app flow via `mobile_api.php::cf_subscription_post()` is documented with new v2025-01-01 API
- Both flows show: DB insert → API call → auth_link storage → status update → user redirect
- SUBSCRIPTION_STATUS_CHANGE event → status update flow documented
- SUBSCRIPTION_NEW_PAYMENT event → full payment creation flow documented (metal rate, weight, due date, receipt, referral, SMS, sync)
- SUBSCRIPTION_PAYMENT_DECLINED event → failed payment recording documented
- Duplicate payment prevention (`isPaymentAlreadyExist`) documented
- Old flow (cfre.in redirect via authLink URL) documented
- New flow (Cashfree JS SDK v3 via `cf_autodebit/authorize/` page) documented
- Return URL handler (`autoDebitRURL`) behavior for mobile vs web documented

## Review Notes

### CRITICAL: Mobile API V3 webhook linkage is broken (`sub_reference_id` typo)
**Why this is a problem:** Because mobile subscriptions are saved with a `null` sub-reference ID, the webhook will never find them and fail with "sub_reference_id not found". Mobile subscriptions will not receive automated payments.
### CRITICAL: `cf_retry()` passes V3 subscriptions to V2 API endpoints
**Why this is a problem:** If a subscription is created via the Mobile App (using the new V3 / v2025-01-01 API), retrying the charge from the web panel will send the V3 subscription ID to the legacy V2 API. Cashfree V2 API will not recognize the V3 subscription, causing the retry to fail.
### CRITICAL: Division by Zero in `wh_response()` for metal calculations

## Fingerprint

- Module: `autodebit`
- Type: `feature`
- Keywords: `consolidating, implemented, mapping, comprehensive, fully, frontend, document, into, reporting, models, across, existing`
- Files: `admin/application/controllers/admin_reports.php, admin/application/controllers/admin_scheme.php, admin/application/controllers/admin_settings.php, admin/application/views/reports/autodebit_subscription_report.php, admin/application/views/master/scheme/form.php`

## Cross-References

- Task: AUT-2607021723
- Branch: feature/AUT-2607021723-document-complete-autodebit-flow-subscri
