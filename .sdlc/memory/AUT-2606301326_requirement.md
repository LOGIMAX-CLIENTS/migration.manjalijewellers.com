# Requirement: Fix Auto-Debit cf_retry() Variable Collision Bug

**Task ID**: AUT-2606301326
**Module**: Auto-Debit (Cashfree Subscription)
**Type**: Bug Fix
**Priority**: Critical
**Reported Via**: Flow verification audit

---

## 1. Category

**Bug Fix** — Critical runtime crash in auto-debit retry payment flow.

## 2. Task Description

The `cf_retry()` function in the Cashfree auto-debit controller crashes when a user or system attempts to retry a failed subscription payment. The function references `$response` (an empty array) when it should reference `$result` (the Cashfree API response object), causing a PHP fatal error: "Trying to get property of non-object."

**Business Impact**: Any customer whose auto-debit payment fails **cannot have their payment retried** — neither from the web admin panel nor programmatically. The system crashes silently, leaving the payment permanently in a failed state until manually corrected.

## 3. User Story

**As a** shop admin or system operator,
**I want** the auto-debit retry function to correctly process Cashfree's API response,
**So that** failed subscription payments can be retried and the customer's installment is recorded without manual intervention.

## 4. Observation

In `cf_autodebit.php::cf_retry()`:

- Line 351: The Cashfree API result is stored in `$result = $res['result']`
- Line 336: `$response` is initialized as an empty array `$response = []`
- Lines 367, 372: `$response->subStatus` is accessed — **should be** `$result->subStatus`
- Line 378: `$response->payment->paymentId` — **should be** `$result->payment->paymentId`
- Line 380: `$response->payment->paymentId` — **should be** `$result->payment->paymentId`
- Lines 387, 403, 410, 411: `$response->payment->amount` — **should be** `$result->payment->amount`
- Line 415: `$response->payment->paymentId` — **should be** `$result->payment->paymentId`

This bug has existed since the old codebase and was never caught because retry is rarely invoked in production.

## 5. Impacts

| Area | Impact |
|------|--------|
| `cf_autodebit.php` | Direct fix in `cf_retry()` method (lines 333-455) |
| `scheme_account` table | No schema change — status updates will work once variable is corrected |
| `payment` table | No schema change — payment records will be created once flow completes |
| `auto_debit_subscription` table | No schema change — subscription status updates will work |
| Web app (chitscheme) | Web retry uses same controller — will be fixed |
| Mobile API | Mobile API does NOT use `cf_retry()` — no impact |
| SMS service | SMS notifications (service ID 7) in `createPayment()` are downstream — unaffected |

**Cross-module impact**: None. The bug is isolated to `cf_autodebit::cf_retry()`.

## 6. Acceptance Criteria

1. **AC-1**: `cf_retry()` uses `$result->subStatus` instead of `$response->subStatus` for subscription status lookup (lines 367, 372)
2. **AC-2**: `cf_retry()` uses `$result->payment->paymentId` instead of `$response->payment->paymentId` for duplicate check and payment record (lines 378, 380, 415)
3. **AC-3**: `cf_retry()` uses `$result->payment->amount` instead of `$response->payment->amount` for payment amount in the insert array (lines 387, 403, 410, 411)
4. **AC-4**: No other variables or logic in `cf_retry()` are changed — the fix is strictly a variable name correction
5. **AC-5**: The `$response` array variable (used for the function's own return value) continues to work correctly for flash messages and JSON output
6. **AC-6**: PHP syntax validation passes on the modified file
