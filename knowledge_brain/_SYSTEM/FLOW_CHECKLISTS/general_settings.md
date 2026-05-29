# Flow Checklist: General Settings

> **Last Updated:** 2026-03-27
> **Controller:** `admin_settings.php`
> **Tables:** `ret_settings`, `chit_settings`, `branch`
> **CRITICAL:** Wrong setting = wrong behavior across ALL modules

---

## Key Settings That Affect System-Wide Behavior

| Setting Key | Controls | Impact if Wrong |
|---|---|---|
| `bill_no_prefix` | Bill number format | Wrong bill series |
| `is_credit` | Allow credit sales | Block or allow unpaid bills |
| `otp_enable` | OTP for delete/edit | Security bypass |
| `gst_enable` | GST calculation | Tax compliance |
| `is_advance` | Allow advance billing | Block advance orders |
| `day_closing_required` | Mandatory day close before billing | Billing blocked/allowed incorrectly |
| `min_pan_amt` | PAN threshold for billing | Legal compliance |
| `is_metal_for_billing` | Metal weight billing flag | Affects billing calculation |
| `appr_stock_incl_in_reports` | Include approval stock in reports | Stock numbers wrong |
| `eda_limit` | Discount approval threshold | Revenue leakage |

## SETTINGS SAVE

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_settings` | UPDATE key-value pairs | ⬜ |
| 2 | Validation | Type checking on values | ⬜ **VERIFY** |
| 3 | `configDB()` | Persist to config? | ⬜ — MST-BUG-022: commented out |
| 4 | Audit trail | Who changed what, when? | ⬜ **VERIFY** |

## Known Bugs (from FLOW_RISK_MATRIX)

| Bug ID | Description | Severity |
|---|---|---|
| MST-BUG-001 | `clear_database()` NO AUTH — anyone can wipe DB | 🔴 CRITICAL |
| MST-BUG-022 | `configDB()` save commented out — settings may not persist | 🔴 HIGH |
| MST-BUG-006 | SQL injection in `get_state()` via raw `$_POST` | 🔴 HIGH |
| MST-BUG-033 | `db_backup()` no role check | 🔴 HIGH |
| MST-BUG-034 | `unregistered_cus.csv` download no role check | 🟡 MED |
| MST-BUG-038 | No validation on entity name (old CRUD) | 🟡 MED |
