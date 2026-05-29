# Flow Checklist: Day Closing

> **Last Updated:** 2026-03-27
> **Controller:** `admin_settings.php` (day close functions)
> **Tables:** `ret_day_closing_log`, `ret_settings`
> **CRITICAL:** Gates ALL billing — wrong date = all bills have wrong date

---

## DAY CLOSE

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_day_closing_log` | INSERT (close_date, branch, closed_by, close_time) | ⬜ |
| 2 | Entry date advance | `ret_settings.entry_date` → next business day | ⬜ |
| 3 | Stock snapshot | Capture stock counts at close? | ⬜ **VERIFY** |
| 4 | Cash verification | Verify cash balance before close | ⬜ **VERIFY** |
| 5 | Pending transactions check | Block close if pending items? | ⬜ **VERIFY** |

## DAY OPEN

| # | Item | Status |
|---|---|---|
| 1 | Is explicit day-open required? | ⬜ **VERIFY** |
| 2 | Opening balance carried forward | ⬜ |

## CANCEL / RE-OPEN

| # | Item | Status |
|---|---|---|
| 1 | Can day be re-opened? | ⬜ **VERIFY** |
| 2 | Impact on bills already created with next day's date | ⬜ |

## Impact on Billing

| Scenario | Effect |
|---|---|
| Day NOT closed | All bills use today's date |
| Day closed | Bills use next business day from `entry_date` |
| Roll-back | Previous day bills may get wrong date |

## Known Risks

| Risk | Description | Severity |
|---|---|---|
| DC-001 | If day_closing_required=1 and close fails, billing is blocked | 🔴 HIGH |
| DC-002 | bill_date uses day close date — timezone issues with IST | 🟡 MED |
