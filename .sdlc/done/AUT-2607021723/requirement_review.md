# Documentation Plan: Complete AutoDebit Flow Document

## Description
Produce a comprehensive flow document covering the entire Cashfree AutoDebit subscription lifecycle in the project. This document will map all end-to-end touchpoints spanning subscription creation, authorization, webhook handling, payment processing, retry mechanisms, and admin reporting, while clearly addressing different API versions and behaviors in Web versus Mobile integrations.

## Document Outline
1. **Introduction & Architecture Overview**
   - High-level system architecture for AutoDebit.
   - Dual API Versioning Context (V2 Legacy for Web vs. V2025-01-01 for Mobile).
2. **Database Schema & Status Mappings**
   - Detailed schema of `auto_debit_subscription` and extended tables.
   - Status transitions (`auth_status`, `payment_status`).
3. **Subscription Creation Flows**
   - Web App Flow (`chitscheme.php`).
   - Mobile App Flow (`mobile_api.php`).
   - **Important Note:** Discrepancies between Web and Mobile Date Calculations (e.g., `first_charge_delay` vs explicitly calculated `first_charge_date`).
4. **Authorization Flow**
   - V2 Legacy Auth (Redirect via cfre.in).
   - V3 SDK Auth (Embedded SDK via auth_link).
   - Return URL handling (`autoDebitRURL`).
5. **Webhook Handling (The Core Engine)**
   - SUBSCRIPTION_STATUS_CHANGE.
   - SUBSCRIPTION_NEW_PAYMENT (including all payment side-effects like receipts, referrals, metal rates, and sync).
   - SUBSCRIPTION_PAYMENT_DECLINED.
6. **Retry Mechanism & Payment Approvals**
   - Manual/Auto charge retries (`cf_retry()`).
   - Config-driven approval workflows.
7. **Admin Features & Reporting**
   - AutoDebit settings, scheme configurations, and reports.

### API Version Comparison Table: Web vs Mobile
| Feature / Behavior | Web App (V2 Legacy) | Mobile App (V2025-01-01) |
|---|---|---|
| **API Endpoint** | `{api_url}api/v2/subscriptions/` | `{base_domain}/pg/subscriptions` |
| **Auth Flow** | Redirect to `authLink` | Hosted page loading CF JS SDK v3 |
| **Return URL** | `cf_autodebit/autoDebitRURL/W/{id_sch_ac}` | `cf_autodebit/autoDebitRURL/M/{id_sch_ac}` |
| **`first_charge_date`** | Uses `first_charge_delay = 1` | Explicitly calculates & sends 1st of next month for PERIODIC plans |

## Source Files to Reference
- **Frontend Controllers:**
  - `application/controllers/cf_autodebit.php` (L70-758): `autoDebitRURL()`, `authorize()`, `wh_response()`, `cf_retry()`, `createPayment()`
  - `application/controllers/chitscheme.php` (L1870-1940): `cf_subscription()`
  - `application/controllers/mobile_api.php` (L7910-8241): `cf_subscription_post()`
- **Frontend Models:**
  - `application/models/scheme_modal.php` (L1839-1934): All DB operations (`get_plan_detail`, `get_subsDetail`, `isPaymentAlreadyExist`, etc.)
- **Frontend Views:**
  - `application/views/cashsfree/cf_authorize.php`
  - `application/views/cashsfree/cf_subscription.php`
  - `application/views/chitscheme/my_schemes.php`
  - `application/views/chitscheme/scheme_acc_details.php`
- **Admin Controllers & Views:**
  - `admin/application/controllers/admin_reports.php` (L1111-1134)
  - `admin/application/controllers/admin_scheme.php` (L324, L719)
  - `admin/application/controllers/admin_settings.php` (L1947-1948)
  - `admin/application/views/reports/autodebit_subscription_report.php`
  - `admin/application/views/master/scheme/form.php` (L76-86)
  - `admin/application/views/settings/general/form.php` (L577-604)

## Diagrams to Include
- **System Sequence Diagram (Web vs Mobile):** Subscription Creation and Authorization.
- **Webhook Process Flow Diagram:** Showing branching logic for the 3 event types and internal payment creation side-effects.
- **Entity Relationship Diagram (ERD):** Mapping `auto_debit_subscription`, `scheme_account`, `payment`, `chit_settings`, and `gateway`.

## Known Issues to Flag
1. **CRITICAL: Return URL Parameter Mismatch (`autoDebitRURL`)**
   - *Location:* `application/controllers/cf_autodebit.php:70`
   - *Issue:* The method signature is `public function autoDebitRURL($id_sch_ac)`. Because the URL is `/autoDebitRURL/W/{id_sch_ac}`, CodeIgniter 3 passes `'W'` (or `'M'`) as the first parameter. Therefore, `$id_sch_ac` becomes `'W'` (or `'M'`), and the actual account ID is ignored. The subsequent DB update `id_scheme_account = 'W'` fails or updates nothing. This means the return flow is currently broken and needs a fix.
2. **CRITICAL: Mobile API Plan Type Inversion**
   - *Location:* `application/controllers/mobile_api.php:7956`
   - *Issue:* The ternary operator does: `$plan_type = ($planDetail['auto_debit_plan_type'] == 1) ? 'ON_DEMAND' : 'PERIODIC';`. Since `1` means `PERIODIC` in the admin settings, this assigns `ON_DEMAND` when it should be `PERIODIC`, and vice versa. This logic inversion completely flips the subscription setup on mobile.
3. **CRITICAL: Mobile API V3 webhook linkage is broken (`sub_reference_id` typo)**
   - *Location:* `application/controllers/mobile_api.php:8047`
   - *Issue:* The V3 mobile API attempts to save the subscription ID: `'sub_reference_id' => isset($res['subscription_id']) ? $res['subscription_id'] : null`. However, line 8034 checks for `isset($res['cf_subscription_id'])`. If Cashfree returns `cf_subscription_id`, `sub_reference_id` is saved as `null`. Because mobile subscriptions are saved with a `null` sub-reference ID, the webhook will never find them and fail with "sub_reference_id not found". Mobile subscriptions will not receive automated payments.
4. **CRITICAL: `cf_retry()` passes V3 subscriptions to V2 API endpoints**
   - *Location:* `application/controllers/cf_autodebit.php:385`
   - *Issue:* `cf_retry()` is hardcoded to call the legacy V2 API endpoint `api/v2/subscriptions/{sub_reference_id}/charge-retry`. If a subscription is created via the Mobile App (using the new V3 / v2025-01-01 API), retrying the charge from the web panel will send the V3 subscription ID to the legacy V2 API. Cashfree V2 API will not recognize the V3 subscription, causing the retry to fail.
5. **CRITICAL: Division by Zero in `wh_response()` for metal calculations**
   - *Location:* `application/controllers/cf_autodebit.php:236`
   - *Issue:* The webhook does `$weight = $_POST['cf_amount']/$metal_rate;` without checking if `$metal_rate` is valid. If `$metal_rate` is returned as `0` or `null`, this calculation triggers a fatal PHP `DivisionByZeroError`, crashing the webhook script entirely.
6. **WARNING: `autoDebitRURL()` expects POST but V3 SDK may redirect via GET**
   - *Location:* `application/controllers/cf_autodebit.php:77`
   - *Issue:* `autoDebitRURL()` strictly checks `if(!empty($_POST) && $_POST['cf_subscriptionId'] != '' )`. While the V2 API redirected back with an HTTP POST, the V3 Cashfree JS SDK typically redirects the user back via an HTTP GET with query parameters. If so, `$_POST` will be empty, and the return URL handler will fail.
7. **MISSING FILE: `autodebit_model.php`**
   - *Location:* Referenced by `admin/application/controllers/admin_reports.php` L1126
   - *Issue:* `ajax_get_autodebit_subscription()` will crash because the model does not exist.
8. **MISSING FILE: `Autodebit.php` Controller**
   - *Location:* Referenced by `admin/application/config/routes.php` L1648-1654
   - *Issue:* Routes for subscription CRUD point to a non-existent controller.

## Acceptance Criteria
- [ ] **TC-001:** Document covers both web app (`cf_subscription()`) and mobile app (`cf_subscription_post()`) subscription creation flows.
- [ ] **TC-002:** Document covers all 3 webhook event types in `wh_response()`, including payment duplicate prevention.
- [ ] **TC-003:** Document covers mandate authorization via SDK and return URL handling, mapping old and new flows.
- [ ] **TC-004:** Document includes complete database schema for all autodebit tables and all status code enums/mappings.
- [ ] **TC-005:** Document covers the `createPayment()` flow with all side-effects (metal rates, generated dates, receipts, referral, sync, SMS).
- [ ] **TC-006:** Document covers charge retry via `cf_retry()` including API calls and resulting DB state.
- [ ] **TC-007:** Document covers all admin-side features, toggles, scheme master settings, and flags missing files.
- [ ] **TC-008:** Document identifies all cross-module interactions, shared tables, and dependencies between the legacy V2 and new V2025-01-01 APIs.
