# Component 5 — Payment CRM: Invariant Matrix

> **Module:** Payment CRM  
> **Generated:** 2026-02-18
> **Updated:** 2026-02-18 (Verified table names)

---

## 1. Variant Types

| Dimension | Variants | Controlled By |
|-----------|----------|---------------|
| **Scheme Type** | Weight (1), Amount/Fix-Weight (2), Flexible (3) | `fix_weight` field |
| **Flexible Sub-Type** | 2-Amount, 3-AmtToWgt, 4-WgtBased, 5-Special, 7-AmtToWgtV2, 8-FixedAmt | `flexible_sch_type` |
| **GST Type** | Inclusive (0), Exclusive (1), None | `gst_type` + `gst > 0` |
| **Payment Mode** | CSH, CC, DC, CHQ, NB, VCH, ADV_ADJ, REF_WALLET, MULTI | Calculated from input |
| **Due Type** | ND, PD, AD, PN, AN, GA, GEN_ADV | `due_type` field |
| **Installment Count** | Single (1), Multi (2+) | `installments` field |
| **DigiGold** | Enabled (is_digi=1), Disabled (0) | `is_digi` field |
| **Wallet** | Used (is_use_wallet=1), Not Used (0) | Checkbox |
| **OTP Rate Fix** | Enabled (one_time_premium=1), Disabled | Scheme config |
| **Branch Mode** | No Branch, Single Branch, Multi-Branch, Cross-Branch | `branch_settings` + `payOtherBranch` |
| **Discount** | Every Installment (type=0), Specific Installment (type=1), None | `discount_type` |

---

## 2. Behavior Grid — Scheme Type × GST Type

| | GST Inclusive (0) | GST Exclusive (1) | No GST |
|---|---|---|---|
| **Weight (1)** | `wgt = user_selected`, `pay = wgt × rate`, `gst_amt = pay - (pay × (100/(100+gst%)))` | `wgt = user_selected`, `pay = wgt × rate`, `gst_amt = pay × (gst%/100)`, `total = pay + gst` | `wgt = user_selected`, `pay = wgt × rate`, `gst=0` |
| **Fix-Weight (2)** | `amt = sch_amt - gst_inclusive`, `wgt = amt / rate` | `amt = sch_amt - gst_excl`, `wgt = amt / rate` ⚠ BUG: same formula as inclusive | `wgt = sch_amt / rate` |
| **Flexible-Amount (3, sub=2)** | If wgt_convert≠2: `wgt = (pay - gst) / rate` | Same ⚠ | No conversion |
| **Flexible-AmtToWgt (3, sub=3)** | `wgt = (pay - gst) / rate` | Same ⚠ | `wgt = pay / rate` |
| **Flexible-WgtBased (3, sub=4)** | `wgt = (pay - gst) / rate`, `pay = payable × rate` | Same ⚠ | `pay = payable × rate` |
| **Flexible-Fixed (3, sub=8)** | `payment_amount -= gst_amount`, `wgt = (pay - gst) / rate` | No GST adjust | Direct pay |

**⚠ BUG FLAG:** Lines 1486 and 1494 — both inclusive and exclusive branches perform `amt - gst_amt`. For exclusive GST, the amount should NOT be reduced; the GST is charged ON TOP.

---

## 3. Behavior Grid — Due Type × Installment Count

| | Single (1) | Multi (2) | Multi (3+) |
|---|---|---|---|
| **ND (Normal)** | Insert 1 ND | Split amounts. Insert 2 NDs | Split amounts. Insert N NDs |
| **PD (Pending)** | Insert 1 PD | Insert 1 PD, 1 PD | Insert N PDs |
| **AD (Advance)** | Insert 1 AD | Insert 1 AD, 1 AD | Insert N ADs |
| **PN (Pending+Normal)** | ⚠ Becomes just ND | i=1→ND, i=2→PD | i=1→ND, i>1→PD |
| **AN (Advance+Normal)** | ⚠ Becomes just ND | i=1→ND, i=2→AD | i=1→ND, i>1→AD |
| **GA (General Advance)** | Separate flow (general_advance) | N/A (always single) | N/A |

---

## 4. Behavior Grid — Payment Mode × Multi-Installment

| Mode | Single Installment | Multi-Installment |
|------|-------------------|-------------------|
| **CSH** | Full cash_payment | cash_payment / installments per iteration |
| **CC/DC** | All card details inserted | ⚠ Only FIRST card's amount split. Other cards ignored for split |
| **CHQ** | All cheque details inserted | ⚠ Only FIRST cheque's amount split |
| **NB** | All NB details inserted | ⚠ Only FIRST NB's amount split |
| **VCH** | All voucher details inserted | ⚠ Only FIRST voucher split |
| **ADV_ADJ** | Full advance amount | advance_amount / installments |
| **REF_WALLET** | Full wallet deduction | Single deduction (not split per installment) |
| **MULTI** | All mode details inserted per type | ⚠ Complex: each mode is individually split |

---

## 5. Behavior Grid — DigiGold × GST

| | Same Commodity | Other Commodity |
|---|---|---|
| **Percentage Benefit** | `benefit_amt = payment × (interest/100)`, `benefit_wgt = amt / rate` | `benefit_wgt = metal_wgt × (interest/100)`, `benefit_amt = wgt × other_rate` |
| **Fixed Amount Benefit** | `benefit_amt = interest_value`, `benefit_wgt = amt / rate` | `benefit_amt = interest_value`, `benefit_wgt = amt × other_rate` ⚠ BUG: should be `/` |

---

## 6. Behavior Grid — Wallet × Payment Amount

| Scenario | Wallet Behavior | payment_mode | payment_type |
|----------|----------------|--------------|--------------|
| No wallet | No deduction | As determined | As posted |
| Wallet partial | Deduct redeem_request | Original mode | Original type |
| Wallet covers 100% | Deduct totalamount | 'REF_WALLET' | 'Wallet Payment' |
| Wallet + Cash | Deduct wallet + cash | 'MULTI' | Original type |
| Wallet > totalamount | Capped at totalamount | 'REF_WALLET' | 'Wallet Payment' |

---

## 7. Behavior Grid — Branch Resolution

| branchwise_cus_reg | branchWiseLogin | payOtherBranch | empLog_branch | Result |
|----|----|----|----|----|
| 1 | * | 0 | * | `cus_reg_branch` |
| 0 | 1 | 0 | * | `cusData['branch']` |
| 0 | 0 | 0 | * | `cusData['branch']` |
| 0 | 1 | 1 | Set | `empLog_branch` |
| 0 | 1 | 1 | 'N' | `generic[id_branch]` |
| 0 | 0 | 1 | * | `generic[id_branch]` |
| * | * | * | * [branch_settings=0] | `NULL` |

---

## 8. Behavior Grid — Discount × Installment

| discount_type | discount_installment | Installment 1 | Installment 2 | Installment N |
|---|---|---|---|---|
| 0 (Every) | * | Apply discount | Apply discount | Apply discount |
| 1 (Specific) | 1 | Apply discount | No discount | No discount |
| 1 (Specific) | N | No discount | No discount | Apply discount (if i==N) |
| None | * | No discount | No discount | No discount |
