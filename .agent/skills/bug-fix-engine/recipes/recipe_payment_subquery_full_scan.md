# Recipe: Unfiltered Derived Subquery Full Table Scan in Payment Queries

> Derived subqueries in `get_paymentContent()` scan ALL 143K+ payment rows instead of filtering by the specific account, causing slow page loads and 502 timeouts

## Metadata
- **Pattern ID**: PAT-QUERY-001
- **Severity**: HIGH
- **Modules Affected**: Payment (payment_model), Account Enrollment
- **Auto-fixable**: Yes
- **Related**: PAT-INFRA-001 (FPM terminate timeout prevents 502 from any slow request)

## Client Scope
- **Applies to**: ALL (any client with 50K+ payment records)
- **Reason**: The `get_paymentContent()` query structure is shared across all deployments. Becomes critical when payment table exceeds ~50K rows.

## Created By
- **Developer**: Antigravity AI
- **Client**: nskjewels.com (143,512 payment rows)
- **Date**: 2026-04-22
- **Source Bug ID**: N/A (root cause of recurring 502 on payment/add)

## Symptom
- `payment/add` page loads slowly or triggers 502 Bad Gateway
- MySQL EXPLAIN shows `rows: 143512` with `type: ALL` (full table scan) on derived subqueries
- PHP-FPM slow log shows `get_paymentContent()` taking >10 seconds
- Problem worsens as payment table grows

## Root Cause
The `get_paymentContent()` function in `payment_model.php` has two derived subqueries (`cp` and `sp`) that calculate current month/day payment totals. These subqueries scan the **entire payment table** for **all accounts** even though the outer query only needs data for **one specific account**.

The subqueries lack a `WHERE p.id_scheme_account = ?` filter, so MySQL:
1. Reads all 143,512 rows from `payment`
2. Applies `Date_Format()` to every row's `date_payment`
3. Groups results by `id_scheme_account` for ALL accounts
4. JOINs the result back — but only uses ONE row (the requested account)

This is like reading the entire phone book to find one person's number.

## Detection
```bash
# Check if subqueries in get_paymentContent lack id_scheme_account filter
grep -A5 "From payment p" admin/application/models/payment_model.php | grep -B1 "Date_Format(Current_Date"
```

Also run EXPLAIN on the query:
```sql
EXPLAIN SELECT ... -- paste the full get_paymentContent query
-- Look for rows > 50000 on derived subqueries
```

## Files
- `admin/application/models/payment_model.php` — `get_paymentContent()` function

## Fix

### Subquery cp (monthly totals) — ~line 1378-1382

#### Before
```sql
Where (p.payment_status=2 or p.payment_status=1) and Date_Format(Current_Date(),'%Y%m')=Date_Format(p.date_payment,'%Y%m')
```

#### After
```sql
Where (p.payment_status=2 or p.payment_status=1) and p.id_scheme_account='$id_scheme_account' and Date_Format(Current_Date(),'%Y%m')=Date_Format(p.date_payment,'%Y%m')
```

### Subquery sp (daily totals) — ~line 1389-1392

#### Before
```sql
Where (p.payment_status=2 or p.payment_status=1) and Date_Format(Current_Date(),'%d%m')=Date_Format(p.date_payment,'%d%m')
```

#### After
```sql
Where (p.payment_status=2 or p.payment_status=1) and p.id_scheme_account='$id_scheme_account' and Date_Format(Current_Date(),'%d%m')=Date_Format(p.date_payment,'%d%m')
```

## Verification
1. Open `payment/add`, select an account — page should load noticeably faster
2. Check PHP-FPM slow log — `get_paymentContent` should no longer appear
3. Run EXPLAIN on the modified query — derived subquery rows should show ~7 instead of 143,512
4. Verify payment amounts, installment counts, and chances display correctly (unchanged business logic)

## Notes
- The `$id_scheme_account` variable is already available in the function scope — passed as the function parameter
- Adding the filter makes MySQL use the `FK_payment_scheme_account` index, reducing scan from 143K to ~7 rows per subquery
- **Future optimization**: Replace `Date_Format()` comparison with date range (`p.date_payment >= '2026-04-01' AND p.date_payment < '2026-05-01'`) to also enable index use on the date column
- The `cshpay` subquery (payment_mode_details) also scans 67K rows without filtering — candidate for same fix pattern
- **Always check other queries in payment_model.php** for the same pattern — this 526KB model likely has more unfiltered subqueries
