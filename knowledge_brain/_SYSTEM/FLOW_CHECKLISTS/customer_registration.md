# Flow Checklist: Customer Registration + Loyalty

> **Last Updated:** 2026-03-27
> **Source:** Customer FLOW_RISK_MATRIX
> **Covers:** Customer registration → wallet creation → loyalty/KYC

---

## Modules Involved
- **Customer** (primary) — registration, edit, delete, status toggle
- **Wallet** — wallet account creation on registration
- **KYC** — identity verification records
- **Scheme** — scheme eligibility based on customer
- **ERP Sync** — online registration sync

## Tables Touched
`customer`, `address`, `wallet_account`, `kyc`, `customer_reg`, `log`

---

## SAVE Checklist (Customer Registration)

| # | Table | Expected Action | Status | Gap? |
|---|---|---|---|---|
| 1 | `customer` | INSERT (name, mobile, DoB, etc.) | ⬜ | — |
| 2 | `address` | INSERT (address1-3, city, state, pincode) | ⬜ | — |
| 3 | `wallet_account` | INSERT if `wallet_account_type=1` | ⚠️ | **BUG**: Outside main transaction — may fail silently |
| 4 | `kyc` | INSERT KYC records (Pan, Aadhar, Bank) | ⬜ | — |
| 5 | Mobile uniqueness | `mobile_available()` check | ✅ | — |
| 6 | Customer limit | `limitDB('get')` check | ✅ | — |

## DELETE Checklist

| # | Table | Expected Cleanup | Status | Gap? |
|---|---|---|---|---|
| 1 | `customer` | DELETE | ✅ | — |
| 2 | `address` | DELETE | ✅ | — |
| 3 | `wallet_account` | DELETE | ✅ | — |
| 4 | `kyc` | DELETE | ❌ | **BUG**: Orphan KYC records (CUS-BUG-006) |
| 5 | `log` entries | DELETE | ❌ | **BUG**: No audit trail cleanup |
| 6 | Image files | DELETE from disk | ❌ | **BUG**: Orphan files |
| 7 | Pre-delete guard | Block if active scheme accounts exist | ✅ | — |

**Delete reversal score: 3/6 = 50%**

## EDIT Checklist

| # | Table | Expected | Status | Gap? |
|---|---|---|---|---|
| 1 | `customer` | UPDATE (not duplicate) | ✅ | — |
| 2 | `address` | UPDATE | ✅ | — |
| 3 | `customer.cus_img` | UPDATE with new image path | ❌ | **BUG**: Image not updated (CUS-BUG-019) |
| 4 | `kyc` | UPDATE if exists, INSERT if new | ⬜ | **VERIFY**: duplicate INSERT risk |

## STATUS Checklist

| # | Operation | Method | Gap? |
|---|---|---|---|
| 1 | Activate/Deactivate | `customer_status()` via GET | **BUG**: CSRF — GET toggle (CUS-BUG-012) |
| 2 | Profile complete/incomplete | `profile_status()` via GET | **BUG**: Same CSRF |

## REPORT Checklist

| # | Report | Source | Status |
|---|---|---|---|
| 1 | Customer list | `customer` joined with `address` | ⬜ |
| 2 | KYC status report | `customer` + `kyc` + `kyc_status` | ⬜ |
| 3 | Wallet balance report | `wallet_account` | ⬜ |

## Known Bugs Found

| Bug ID | Missing Step | Severity |
|---|---|---|
| CUS-BUG-006 | Delete: orphan KYC records | 🔴 HIGH |
| CUS-BUG-019 | Edit: image not updated in DB | 🔴 HIGH |
| CUS-BUG-012 | Status toggle via GET — CSRF vulnerability | 🟡 MED |
| CUS-BUG-003 | SQL injection in search | 🔴 CRITICAL |
| CUS-BUG-002 | Path traversal in download | 🔴 CRITICAL |
| CUS-BUG-001 | Agent allocation broken | 🔴 HIGH |
| CUS-BUG-018 | Employee allocation broken | 🔴 HIGH |
| CUS-BUG-007 | Scheme count shows customer 1's count for all | 🔴 HIGH |
| — | Wallet creation outside transaction — silent failure | 🟡 MED |
| — | Customer password stored as base64 (not hashed) | 🔴 HIGH |
