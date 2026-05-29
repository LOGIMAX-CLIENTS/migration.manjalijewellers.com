# Scheme Module — Invariant Matrix
> **Round**: 1 | **Date**: 2026-03-06

---

## 1. Variant Dimensions

### D1: Scheme Type (`scheme_type`)
| Value | Description | Amount Field | Weight Field | Rate Required |
|---|---|---|---|---|
| 0 | Amount-based | ✅ min/max amount | — | — |
| 1 | Weight-based | — | ✅ min/max weight | ✅ |
| 2 | Amount-to-Weight | ✅ amount | — | ✅ (settle) |
| 3 | Flexible | Per `flexible_sch_type` | Per type | Per type |

### D2: Flexible Scheme Type (`flexible_sch_type`)
| Value | Description |
|---|---|
| 1 | Amount-only flexible |
| 2 | Weight flexible (per wgt_convert) |
| 3-5 | Weight direct |
| 6 | Amount-only |
| 7-8 | Weight direct |

### D3: Benefit/Deduction Mode
| Setting | Benefit Source | Deduction Source |
|---|---|---|
| `apply_benefit_by_chart=1, apply_debit_on_preclose=0` | Interest chart (scheme_benefit_deduct_settings) | None (chart) |
| `apply_benefit_by_chart=0, apply_debit_on_preclose=1` | Standard interest fields | Pre-close deduction chart |
| `apply_benefit_by_chart=0, apply_debit_on_preclose=0` | Standard interest fields | None |
| `apply_benefit_by_chart=1, apply_debit_on_preclose=1` | ⚠️ Undefined behavior | ⚠️ Undefined |

### D4: Maturity Type (`maturity_type`)
| Value | How maturity is calculated |
|---|---|
| 1 | Flexible (no fixed maturity) |
| 2 | Fixed (start_date + maturity_days) |
| 3 | Fixed months (start_date + total_installments months) |

### D5: Referral Configuration
| Field | Values | Controls |
|---|---|---|
| `emp_refferal` | 0/1 | Employee referral enabled |
| `cus_refferal` | 0/1 | Customer referral enabled |
| `agent_refferal` | 0/1 | Agent referral enabled |
| `{type}_refferal_by` | 0=percent, 1=fixed | Value calculation mode |
| `{type}_deduct_ins` | INT | Min installments before ref deduction |

### D6: DigiGold Flag (`is_digi`)
| Value | Behavior |
|---|---|
| 0 | Standard scheme |
| 1 | DigiGold: daily_pay_limit, restrict_payment, chit_detail_days, total_days_to_pay apply |

### D7: Payment Chances (`payment_chances`)
| Value | Behavior |
|---|---|
| 0 | Single payment per installment (max_chance=1) |
| 1 | Multiple payments per installment (min_chance, max_chance) |

### D8: GST Type (`gst_type`)
| Value | Tax Calculation |
|---|---|
| 0 | GST Inclusive (amount includes tax) |
| 1 | GST Exclusive (tax added on top) |

---

## 2. Behavior Grids

### Grid 1: Scheme Type × Benefit Source

| Scheme Type / Benefit Mode | Chart-based | Standard | No benefit |
|---|---|---|---|
| 0 (Amount) | ✅ Amount calc | ✅ Amount calc | Amount only |
| 1 (Weight) | ✅ Weight calc | ✅ Weight calc | Weight only |
| 2 (Amt-to-Wgt) | ✅ Conversion | ✅ Conversion | Conversion only |
| 3 (Flexible) | ✅ Per sub-type | ✅ Per sub-type | Per sub-type |

### Grid 2: Referral Type × Credit Calculation

| Ref Type / Basis | Percent (by=0) | Fixed (by=1) |
|---|---|---|
| Employee | `amount × emp_ref_values ÷ 100` | `emp_ref_values` |
| Customer | `amount × cus_ref_values ÷ 100` | `cus_ref_values` |
| Agent | Per agent_benefit chart | Per chart |

### Grid 3: Child Table × Operation Pattern

| Table | Add | Edit | Delete |
|---|---|---|---|
| scheme_branch | Insert loop | Delete-all + Insert loop | NOT cleaned |
| gst_splitup_detail | Insert loop | Soft-delete + Insert | Hard delete |
| scheme_benefit_deduct_settings | Insert loop | Delete-all + Insert | NOT cleaned |
| scheme_debit_settings | Insert loop | Delete-all + Insert | NOT cleaned |
| scheme_agent_benefit | Insert loop | Delete-all + Insert | NOT cleaned |
| scheme_incentive_settings | Insert loop | Delete-all + Insert | NOT cleaned |
| scheme_flexi_settings | Insert loop | Delete-all + Insert | NOT cleaned |
| emp_closing_incentive | Insert loop (⚠️) | Delete-all + Insert | NOT cleaned |
| scheme_general_advance_benefit_settings | Insert loop (⚠️) | Delete-all + Insert | NOT cleaned |
| scheme_custom_payable_settings | Batch insert | Soft-delete + Batch insert | NOT cleaned |

---

## 3. Edge Cases

| # | Scenario | Behavior | Risk |
|---|---|---|---|
| EC-1 | Delete scheme with child settings | Only gst_splitup_detail cleaned | 🟡 Orphaned data |
| EC-2 | Add with emp_closing_incentive using `$id` | `$id` is empty (undefined) → inserts with NULL id_scheme | 🔴 Data corruption |
| EC-3 | Add with GA benefit calls `deleteData($id)` | Delete with empty $id → may delete wrong records | 🔴 Data corruption |
| EC-4 | Both benefit_by_chart=1 AND debit_on_preclose=1 | Undefined — code treats them as mutually exclusive | 🟡 Logic gap |
| EC-5 | TopUp chart edit commits separately from outer transaction | Double commit → outer may commit empty transaction | 🔴 Transaction integrity |
| EC-6 | Scheme with 0 total_installments | Division by zero in Account module | 🔴 Downstream |
| EC-7 | DigiGold duplicate per metal type | Blocked by enableDigiGold() check | ✅ Handled |
| EC-8 | Concurrent scheme creation | No locking on scheme_count() | 🟡 Race condition |
| EC-9 | Image upload fails silently | unlink() on non-existent file may warn | 🟡 Warning |
| EC-10 | `SHOW COLUMNS` called on every insert/update | Performance hit on high-frequency saves | 🟡 Performance |
