# Test Cases — AUT-2607021723: AutoDebit Flow Documentation

## TC-001: Subscription Creation Flow Coverage
**Scenario**: Document covers both web app and mobile app subscription creation flows
**Verify**:
- Web app flow via `chitscheme.php::cf_subscription()` is documented with correct API call sequence
- Mobile app flow via `mobile_api.php::cf_subscription_post()` is documented with new v2025-01-01 API
- Both flows show: DB insert → API call → auth_link storage → status update → user redirect
**Expected**: Complete step-by-step flow for both entry points with file/line references

## TC-002: Webhook Event Handling Coverage
**Scenario**: Document covers all 3 webhook event types in `cf_autodebit.php::wh_response()`
**Verify**:
- SUBSCRIPTION_STATUS_CHANGE event → status update flow documented
- SUBSCRIPTION_NEW_PAYMENT event → full payment creation flow documented (metal rate, weight, due date, receipt, referral, SMS, sync)
- SUBSCRIPTION_PAYMENT_DECLINED event → failed payment recording documented
- Duplicate payment prevention (`isPaymentAlreadyExist`) documented
**Expected**: Each webhook event documented with complete data flow and DB operations

## TC-003: Authorization Flow Coverage
**Scenario**: Document covers mandate authorization via SDK and return URL handling
**Verify**:
- Old flow (cfre.in redirect via authLink URL) documented
- New flow (Cashfree JS SDK v3 via `cf_autodebit/authorize/` page) documented
- Return URL handler (`autoDebitRURL`) behavior for mobile vs web documented
- Status transitions (INITIALIZED → BANK_APPROVAL_PENDING → ACTIVE) documented
**Expected**: Both authorization flows clearly documented with device-specific handling

## TC-004: Database Schema & Status Codes
**Scenario**: Document includes complete database schema and all status code mappings
**Verify**:
- `auto_debit_subscription` table structure with all columns documented
- `auth_status` enum (1-6) with meanings documented
- `payment_status` values used by autodebit documented
- `auto_pay_approval` config values and their effects documented
- `chit_settings` auto_debit columns documented
**Expected**: All tables, columns, and status enums fully documented with comments

## TC-005: Payment Processing Logic
**Scenario**: Document covers the createPayment() flow with all side-effects
**Verify**:
- Metal rate calculation for weight-based schemes documented
- Due month/year generation logic documented
- Receipt number generation documented
- Account number generation (including lucky draw) documented
- Referral benefit processing documented
- Intermediate table sync (JIL/Standard) documented
- SMS notification trigger documented
**Expected**: Complete createPayment() flow with all conditional branches

## TC-006: Retry Mechanism Coverage
**Scenario**: Document covers charge retry via `cf_retry()`
**Verify**:
- Retry API call to Cashfree `{sub_reference_id}/charge-retry` documented
- Payment creation from retry response documented
- Web app redirect behavior (type=1) documented
- Error handling documented
**Expected**: Full retry flow from trigger to completion

## TC-007: Admin Panel Features
**Scenario**: Document covers all admin-side autodebit features
**Verify**:
- Scheme master `auto_debit_plan_type` setting documented
- General settings `auto_debit` toggle and `auto_debit_allow_app_pay` policy documented
- Subscription report (DataTable with filters) documented
- Missing files identified (autodebit_model.php, Autodebit.php controller)
**Expected**: Complete admin panel feature inventory with current status

## TC-008: Cross-Module Dependencies
**Scenario**: Document identifies all module interactions
**Verify**:
- Shared tables listed with all consumers
- Shared models listed with all callers
- Config dependencies identified
- Two API version coexistence documented
**Expected**: Dependency map showing all module touchpoints

## Generated Tests
- tests/tests/test_autodebit_documentation.py
