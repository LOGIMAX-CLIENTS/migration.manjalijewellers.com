# Recipe: Standardize ERP Date Defaults to Today

## Metadata
- **Pattern ID**: PAT-UI-001
- **Severity**: MEDIUM
- **Modules Affected**: Customer, Payment, Scheme Account
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Standardize the default date range on listing pages to focus on current day data.

## Created By
- **Developer**: Antigravity
- **Client**: LOGIMAX Clients (Sriganesh Jewels, Lakshmana Aachari, Manepally, Gupthagem, DCNM Jewels)
- **Date**: 2026-04-18
- **Source Bug ID**: N/A

## Symptom
Listing pages (Customer, Payment, Scheme Account) default to showing the last 30 days of data (`moment().subtract(29, 'days')`). This can lead to heavy initial page loads and requires users to manually filter for current day transactions.

## Root Cause
Hardcoded `startDate` set to 29 days ago in the `daterangepicker` initialization.

## Detection
```command
grep -rn "startDate: moment().subtract(29, 'days')," admin/assets/js/
```

## Files
- `admin/assets/js/customer.js`
- `admin/assets/js/payment.js`
- `admin/assets/js/scheme_account.js`

## Fix

### Before
```javascript
              startDate: moment().subtract(29, 'days'),
```

### After
```javascript
              //startDate: moment().subtract(29, 'days'),
              startDate: moment(),
```

## Verification
1. Load the listing page for the affected module.
2. Verify that the date range picker start and end dates both point to the current date.
3. Confirm that the data displayed by default is only for today.

## Notes
- Only apply this change to primary listing buttons like `#customer-dt-btn`, `#payment-dt-btn`, `#account-dt-btn`, and `#closed-acc-dt-btn`.
- Other filters (e.g., Request List) may require different defaults depending on business logic.
