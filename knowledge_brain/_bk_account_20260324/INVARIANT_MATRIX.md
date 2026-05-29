# Account Module — Invariant Matrix
> **Round**: 1 | **Date**: 2026-03-06

---

## 1. Variant Dimensions

### D1: Scheme Type (`scheme_type`)
| Value | Description | Weight Conversion | Closing Calculation |
|---|---|---|---|
| 0 | Amount-based | N/A | Amount-only |
| 1 | Weight-based | Direct weight | Weight + rate |
| 2 | Amount-to-Weight | Convert via rate | Weight + rate |
| 3 | Flexible | Per `flexible_sch_type` | Per sub-type |

### D2: Flexible Scheme Type (`flexible_sch_type`)
| Value | Description | is_weight |
|---|---|---|
| 1 | Amount only | 0 |
| 2 | Weight (wgt_convert: 0/1=weight, 2=amount) | depends |
| 3 | Weight direct | 1 |
| 4 | Weight direct | 1 |
| 5 | Weight direct | 1 |
| 6 | Amount only | 0 |
| 7 | Weight direct | 1 |
| 8 | Weight direct | 1 |

### D3: Account Number Mode (`scheme_wise_acc_no`)
| Value | Scope |
|---|---|
| 0 | Common (global sequential) |
| 1 | Common + branch-wise |
| 2 | Scheme-wise |
| 3 | Scheme-wise + branch-wise |
| 4 | Financial year |
| 5 | Financial year + scheme-wise |
| 6 | Financial year + scheme-wise + branch-wise |

### D4: Referral Type (`is_refferal_by`)
| Value | Source | Wallet Credit To |
|---|---|---|
| 0 | Customer referred | Customer's employee wallet |
| 1 | Employee referred | Employee wallet |
| NULL | No referral | N/A |

### D5: Calculation Type (`calculation_type`)
| Value | Benefit Check | Pre-Close Check |
|---|---|---|
| 1 | Installment-based (paid ≥ total) | Installment threshold |
| 2 | Maturity-date-based (cur_date > maturity) | Days threshold |

### D6: Maturity Type (`maturity_type`)
| Value | Description |
|---|---|
| 1 | Flexible (no fixed end date) |
| 2 | Fixed (start_date + maturity_days) |
| 3 | Fixed months (start_date + total_installments months) |

### D7: Closing Incentive Based On (`closing_incentive_based_on`)
| Value | Calculation |
|---|---|
| 1 | Based on paid installments |
| 2 | Based on closing weight |

### D8: OTP Type
| Session Key | Expiry Key | Purpose |
|---|---|---|
| `pay_OTP` | `pay_OTP_expiry` | Gift issue OTP |
| `OTP` | `gift_OTP_expiry` | Gift (inventory) OTP |
| `OTP_scheme_join` | `sche_join_otp_expiry` | Scheme join OTP |
| `OTP` | `rate_fixing_otp_exp` | Rate fixing OTP |

### D9: Gift Status
| Value | Description |
|---|---|
| 0 | Available |
| 1 | Issued |
| 2 | Deducted/Cancelled |

### D10: Sync Record Source
| Value | Description |
|---|---|
| 1 | Online payment |
| 2 | Offline payment |

---

## 2. Behavior Grids

### Grid 1: Account Status × Action Matrix

| State / Action | Add | Edit | Close | Revert | Delete | Print |
|---|---|---|---|---|---|---|
| **active=1, is_closed=0** | — | ✅ | ✅ | — | ✅ | Passbook F/B |
| **active=0, is_closed=1** | — | — | — | ✅ | — | Passbook Close |
| **active=0, is_closed=0** | — | ✅ (status change) | — | — | ✅ | Passbook F/B |
| **active=1, is_closed=1** | — | — | — | — | — | ERROR state |

### Grid 2: Referral Edit Type × Action Matrix

| Edit Type | Condition | Wallet Action |
|---|---|---|
| Type 2 | New code added (was empty) | Credit to ref'd employee/customer |
| Type 1 | Code changed (old ≠ new) | Update wallet transaction |
| Type 3 | Code removed (new is empty) | Debit from employee/customer |
| None | Code unchanged | No wallet action |

### Grid 3: Closing × Benefit/Deduction Matrix

| Scenario | Benefit Applied? | Deduction Applied? |
|---|---|---|
| Full maturity + all installments paid | ✅ Full | ❌ No |
| Pre-close + eligible (months met) | ✅ Pre-close rate | ✅ If settings enabled |
| Pre-close + not eligible | ❌ No | ✅ Full deduction |
| DigiGold (calculate_by=1) | ✅ DG formula | ✅ DG formula |
| Maturity Days (calculate_by=2) | ✅ Maturity formula | ✅ Maturity formula |

### Grid 4: Gift Source × Behavior Matrix

| Gift Source | OTP Required? | Inventory Track? | Status Update? |
|---|---|---|---|
| Simple gift (type=0) | Per settings | ❌ | gift_issued only |
| Inventory gift (type=1) | Per settings | ✅ | gift_issued + inventory |
| Prize | Per settings | ❌ | gift_issued only |

---

## 3. Edge Cases & Invariants

| # | Scenario | Current Behavior | Risk |
|---|---|---|---|
| EC-1 | Account created with 0 installments | Division by zero in lump sum calc | 🔴 Fatal |
| EC-2 | Closing with no payments made | Benefits may still calculate | 🟡 Logic error |
| EC-3 | DigiGold customer reverts while active DG exists | Blocked by `isDigiAcc()` check | ✅ Handled |
| EC-4 | Concurrent account number generation | Race condition (LOCK TABLES commented out) | 🔴 Duplicate numbers |
| EC-5 | Offline sync with missing id_scheme_account | Fallback to clientid lookup | ✅ Handled |
| EC-6 | Referral code removed + no wallet account | Silently skips debit | 🟡 Data inconsistency |
| EC-7 | Gift OTP used with `=` instead of `==` | Always passes verification | 🔴 Security bypass |
| EC-8 | Employee wallet doesn't exist at close time | New wallet created on-the-fly | ✅ Handled |
| EC-9 | Multiple gateways fail for SMS | No fallback mechanism | 🟡 Silent failure |
| EC-10 | Closing balance negative | No validation prevents negative | 🟡 Financial error |
| EC-11 | WhatsApp $params undefined | Runtime warning, message may fail | 🟡 Notification failure |
| EC-12 | Financial year record missing | Undefined `$financial_year` | 🔴 Query error |
| EC-13 | trans_commit inside foreach loop | Partial commits possible | 🔴 Data corruption |
| EC-14 | `exit;` before error tracking code | Unreachable code after exit | 🟡 Debug oversight |
| EC-15 | Password sent in plaintext SMS | Security violation | 🔴 Security |
