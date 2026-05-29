# Payment Module — Invariant Matrix
> **Updated**: 2026-03-06 | **Round**: 2

## Variant Dimensions

### Dimension 1: Scheme Type (`scheme.scheme_type`)
| Value | Label | Payment Behavior |
|---|---|---|
| 0 | Amount | Fixed amount per installment |
| 1 | Weight | Min/max weight per installment |
| 2 | Amount to Weight | Fixed amount, converted to weight |
| 3 | Flexible | Multiple sub-types (see `flexible_sch_type`) |

### Dimension 2: GST Type (`scheme.gst_type`)
| Value | Label | Calculation |
|---|---|---|
| 0 | Inclusive | `gst_amt = amount - (amount × (100 / (100 + gst)))` |
| 1 | Exclusive | `gst_amt = amount × (gst / 100)` |

### Dimension 3: Payment Mode
| Code | Label | Extra Fields Required |
|---|---|---|
| CSH | Cash | — |
| CC | Credit Card | `card_type`, `card_no`, `ref_no`, `id_device` |
| DC | Debit Card | Same as CC |
| CHQ | Cheque | `cheque_no`, `bank_name`, `bank_branch`, `bank_IFSC`, `cheque_date` |
| NB | Net Banking | `NB_type`, `id_bank`, `ref_no`, `nb_date`, `id_device` |
| VCH | Voucher | `card_no` (voucher number), `payment_amount` |
| ADV_ADJ | Advance Adjustment | `id_issue_receipt`, `utilized_amt`, `cash_utilized_amt` |
| REF_WALLET | Referral Wallet | `redeem_request` |
| MULTI | Multiple | Combination of 2+ above modes |

### Dimension 4: Receipt Number Mode (`chit_settings.scheme_wise_receipt`)
| Value | Mode | Scope | Branch-dependent |
|---|---|---|---|
| 1 | Common | Global | No |
| 2 | Branch-wise | Per branch | Yes |
| 3 | Scheme-wise | Per scheme | No |
| 4 | Scheme + Branch | Per scheme per branch | Yes |
| 5 | Financial Year | Per FY | No |
| 6 | FY + Scheme + Branch | Most granular | Yes |
| 7 | FY + Branch | Per FY per branch | Yes |

### Dimension 5: Due Type
| Code | Label | When Used |
|---|---|---|
| ND | Normal Due | Regular monthly payment |
| PD | Pending Due | Paying overdue months |
| AD | Advance Due | Paying future months |
| GA | General Advance | General advance payment |
| A | Advance (online) | Online advance |
| P | Pending (online) | Online pending |
| S | Split | Split payment |
| PN | Pending + Normal | First=ND, rest=PD |
| AN | Advance + Normal | First=ND, rest=AD |

### Dimension 6: Flexible Scheme Type (`scheme.flexible_sch_type`)
| Value | Label | Weight Calculation |
|---|---|---|
| 2 | Amount-based (with wgt_convert) | If `wgt_convert != 2` → amount_to_weight() |
| 3 | Weight-based | amount_to_weight() |
| 4 | Type 4 | amount_to_weight() |
| 5 | Type 5 (with wgt_store_as) | If `wgt_store_as == 1` → amount_to_weight() |
| 7 | Type 7 | amount_to_weight() |
| 8 | Type 8 | amount_to_weight() (special: no GST deduction from pay_amt) |

## Behavior Grid: Scheme Type × GST Type → Weight Calculation

| | GST Inclusive (0) | GST Exclusive (1) |
|---|---|---|
| **Amount (0)** | No weight calc needed | No weight calc needed |
| **Weight (1)** | Weight from form directly | Weight from form directly |
| **Amount to Weight (2)** | `wgt = (amt - gst_amt) / rate` | `wgt = amt / rate` |
| **Flexible (3)** | Depends on `flexible_sch_type` | Depends on `flexible_sch_type` |

## Behavior Grid: Scheme Type × Due Type → Amount Handling

| | ND | PD | AD | GA |
|---|---|---|---|---|
| **Amount (0)** | Fixed scheme amount | Fixed scheme amount | Fixed scheme amount | User-entered amount |
| **Weight (1)** | Weight × rate = amount | Same | Same | User-entered amount → weight calc |
| **Flexible (3)** | Min/Max range | Same | Same | User-entered amount |

## Configuration Controls

| Setting | DB Location | PHP Variable | JS Variable | Effect |
|---|---|---|---|---|
| `receipt_no_set` | `chit_settings` | `$this->get_rptnosettings()` | — | 0=auto, 1=on success |
| `scheme_wise_receipt` | `chit_settings` | Model: `$data['scheme_wise_receipt']` | — | Receipt gen mode (1-7) |
| `branch_settings` | `chit_settings` / session | `$this->branch_settings` | — | Enable branch-wise ops |
| `payOtherBranch` | `config` | `$this->config->item('payOtherBranch')` | — | Allow cross-branch payment |
| `allow_wallet` | `chit_settings` | `$this->allow_wallet()` | — | Enable wallet payments |
| `has_lucky_draw` | `chit_settings` | via JOIN | — | Enable lucky draw groups |
| `edit_addpay_page` | `chit_settings` | `$this->checkSettings()` | — | Edit on add page |
| `integrationType` | `config` | `$this->config->item('integrationType')` | — | 1=JIL, 2=Standard |
| `sms_gateway` | `config` | `$this->config->item('sms_gateway')` | — | 1-5 (MSG91,Nettyfish,SpearUC,Asterixt,Qikberry) |
| `company_settings` | session | `$this->session->userdata('company_settings')` | — | Multi-company mode |

### Dimension 7: Payment Source (`payment.added_by`)
| Value | Label | Behavior |
|---|---|---|
| 0 | Admin Panel | Manual entry, full CRUD, requires session login |
| 1 | Mobile App | Online payment, gateway verification needed |
| 2 | Website | Online payment, gateway verification needed |
| 3 | Gateway Callback | Auto-triggered by gateway webhook |

### Dimension 8: Integration Type (`config.integrationType`)
| Value | Label | Sync Behavior |
|---|---|---|
| 1 | JIL | `insert_common_data_jil()` — JIL-specific sync tables |
| 2 | Standard | `insert_common_data()` — Generic sync |
| NULL/0 | None | No sync performed |

### Dimension 9: Gateway Code (`gateway.pg_code`)
| Code | Gateway | Verify Method | Settlement Method | API Auth |
|---|---|---|---|---|
| 1 | PayU | `verify_PayUpayments` | — | key + salt hash |
| 2 | HDFC/CCAvenue | `verify_hdfcpayment` | `hdfcSettlement` | AES encryption |
| 3 | TechProcess | `verifyWithTechProcess` | — | TransactionRequestBean |
| 4 | Cashfree | `verify_cashfreepayment` | `cashfreeSettlement` | x-client-id + x-client-secret |
| 7 | Razorpay | `verifyRazorPayments` | — | key_id + key_secret |
| 8 | EaseBuzz | `verify_easebuzzpayment` | — | key + salt hash |

## Behavior Grid: Payment Status × Action Allowed

| | View | Edit | Delete | Print | Verify | Approve | Revert |
|---|---|---|---|---|---|---|---|
| **Pending (7)** | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |
| **Awaiting (2)** | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ | ❌ |
| **Success (1)** | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ |
| **Failure (3)** | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |
| **Cancel (4)** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Refund (6)** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

## Behavior Grid: Payment Source × Gateway Handling

| | Manual | Cashfree | Razorpay | PayU | HDFC | EaseBuzz | TechProcess |
|---|---|---|---|---|---|---|---|
| **Admin Panel** | Direct insert status=1 | N/A | N/A | N/A | N/A | N/A | N/A |
| **Mobile App** | N/A | cURL verify | cURL verify | cURL verify | AES encrypt | hash verify | Bean class |
| **Website** | N/A | cURL verify | cURL verify | cURL verify | AES encrypt | hash verify | Bean class |

## Edge Cases & Special Behaviors

| # | Scenario | Expected Behavior | Actual Behavior | Risk |
|---|---|---|---|---|
| 1 | Payment amount = 0 | Should reject | Controller checks `payment_amount > 0` for GA; SaveAll doesn't explicitly check | 🟡 |
| 2 | Metal rate = 0 when weight calc needed | Division by zero | `amount_to_weight()` has no zero-check | 🔴 |
| 3 | Multiple installments where amount doesn't divide evenly | Rounding issue | Amount divided equally — last installment may differ by cents | 🟡 |
| 4 | Wallet balance < redeemed_amount | Over-redemption | `floor(min(redeem_request, can_redeem))` prevents this | ✅ |
| 5 | Concurrent receipt number generation | Duplicate receipts | No DB lock — commented out `LOCK TABLES` at L42-43 | 🔴 |
| 6 | Payment deleted but mode_details remain | Orphaned records | DELETE is hard delete, no child cleanup | 🔴 |
| 7 | Gateway timeout (8s) | Partial update | Status stays PENDING, manual re-verify possible | 🟡 |
| 8 | OTP expired during payment form fill | Payment fails | Session-based expiry, checked at verify time | ✅ |
| 9 | Branch=NULL for non-branch-enabled company | NULL branch in payment | Branch resolution falls through to NULL | ✅ |
| 10 | Scheme with no GST (gst=0) | No GST calculation | gst_amt = 0, no special handling needed | ✅ |
| 11 | PDC → Payment with missing payee_ifsc | NULL bank IFSC | L308 typo: `$pay['payee_ifsc]']` — bracket in key name | 🔴 |
| 12 | Online payment with unknown pg_code | No verification | `verify_payment()` silently does nothing | 🟡 |

