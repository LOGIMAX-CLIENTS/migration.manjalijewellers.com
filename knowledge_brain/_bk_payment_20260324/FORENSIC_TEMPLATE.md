# Payment Module — Forensic Template
> **Updated**: 2026-03-06 | **Round**: 2

## Layer 1 — Symptom Collection

### Payment-Specific Symptom Checklist
- [ ] Payment saved but receipt not generated
- [ ] Payment amount mismatch (displayed vs stored)
- [ ] Metal weight calculated incorrectly
- [ ] GST applied wrongly (inclusive vs exclusive confusion)
- [ ] Payment mode not saving correctly (single vs multi)
- [ ] Online payment stuck in Pending/Awaiting
- [ ] Post-dated cheque not converting to payment
- [ ] Installment count wrong after payment
- [ ] Receipt number duplicated
- [ ] Payment not visible in list (branch filtering issue)
- [ ] SMS/Email not sent after payment
- [ ] Wallet amount not deducted
- [ ] Advance adjustment not recorded
- [ ] Payment deleted but installment count not updated
- [ ] Gateway verification failing
- [ ] Account number not generated on first payment

### Key Questions to Ask
1. **Which scheme type?** (Amount=0, Weight=1, A2W=2, Flexible=3)
2. **Which payment mode?** (CSH/CC/DC/CHQ/NB/VCH/ADV_ADJ/MULTI)
3. **Manual or Online?**
4. **First payment or subsequent?**
5. **Branch settings enabled?**
6. **Which receipt generation mode?** (1-7)

---

## Layer 2 — Reproduce & Isolate

### Reproduction Steps Template
1. Login as the same employee profile
2. Navigate to `/payment/add`
3. Select the same customer
4. Select the same scheme account
5. Enter the exact amount, mode, and options
6. Submit with same settings active

### Isolation Questions
- Does it happen for ALL schemes or specific scheme type?
- Does it happen for ALL payment modes or specific mode?
- Does it happen for ALL branches or specific branch?
- Was it working before? What changed? (config, code deploy, data migration)
- Is it reproducible with a different customer/account?

---

## Layer 3 — Client-Side Trace

### JS Console Log Points
| Variable | Where to Check | What to Look For |
|---|---|---|
| `generic` object | Before AJAX submit | All form values |
| `cus_pay_mode` | Before AJAX submit | Mode amounts, JSON arrays |
| Payment mode JSON | `card_pay`, `chq_pay`, `net_bank_pay` | Parse errors, empty arrays |
| `payment_mode` | After mode detection | Should match expected |
| AJAX response | Network tab → response | `status`, error messages |

### Network Tab Checks
| URL | Method | Check |
|---|---|---|
| `payment/get/ajax/account/{id}` | GET | Does account data load correctly? |
| `payment/get/ajax_data` | GET | Are modes/banks/status loaded? |
| `payment/save_all` or `payment/save` | POST | Request payload matches form? Response success? |
| `payment/update_payment/{id}` | POST | Is the correct ID sent? |

### Key JS Functions to Debug
- Mode detection logic → search for `payment_mode = 'CSH'` in payment.js
- GST calculation → search for `gst_type` in payment.js
- Metal weight → search for `metal_weight` or `amount_to_weight` in payment.js
- Save submit → around L3346 in payment.js

---

## Layer 4 — Server-Side Trace

| Symptom | File | Method | Line | What to Check |
|---|---|---|---|---|
| Receipt not generated | Controller | `generate_receipt_no` | L84-113 | Is `get_rptnosettings()==1`? Is `get_receipt_no()` returning NULL? |
| Amount wrong | Controller | `payment` SaveAll | L1303-1308 | Installment division, discount subtraction |
| Weight wrong | Controller | `payment` SaveAll | L1461-1484 | GST deduction from amount before weight calc |
| Mode not saving | Controller | `payment` SaveAll | L1333-1385 | Mode detection if/elseif chain |
| Status not updating | Controller | `update_pay_status` | L2853-2946 | Check which status code is set |
| PDC not converting | Controller | `postdate_payment_form` | L200-373 | Check `payment_status==1` branch |
| Installments wrong | Controller | `payment` SaveAll | L1235-1239 | `getPaidInsData()` and `updData()` |
| Branch filter hiding | Model | `payment_list_range` | L444-456 | `id_branch` and `show_to_all` logic |
| Online verify fail | Controller | `verify_*` methods | L3788+ | Gateway API response, curl errors |

### Log File Locations
- Manual payments: `log/{YYYY-MM-DD}/manual/create_payment_{YYYY-MM-DD}.txt`
- General advance: `log/{YYYY-MM-DD}/general_advance/create_payment_{YYYY-MM-DD}.txt`
- Full POST data logged in JSON format

---

## Layer 5 — Database Verification

### 5a. Check payment record
```sql
SELECT * FROM payment WHERE id_payment = {ID};
```

### 5b. Check mode details match
```sql
SELECT p.id_payment, p.payment_amount, p.payment_mode,
       GROUP_CONCAT(pmd.payment_mode, ':', pmd.payment_amount) as modes,
       SUM(pmd.payment_amount) as mode_total
FROM payment p
LEFT JOIN payment_mode_details pmd ON p.id_payment = pmd.id_payment AND pmd.is_active = 1
WHERE p.id_payment = {ID}
GROUP BY p.id_payment;
```

### 5c. Check installment count accuracy
```sql
SELECT sa.id_scheme_account, sa.total_paid_ins,
       COUNT(p.id_payment) as actual_count,
       SUM(p.no_of_dues) as actual_dues_sum
FROM scheme_account sa
LEFT JOIN payment p ON sa.id_scheme_account = p.id_scheme_account AND p.payment_status = 1
WHERE sa.id_scheme_account = {ID}
GROUP BY sa.id_scheme_account;
```

### 5d. Check receipt number uniqueness
```sql
SELECT receipt_no, COUNT(*) as cnt 
FROM payment 
WHERE receipt_no IS NOT NULL 
GROUP BY receipt_no 
HAVING cnt > 1;
```

### 5e. Check for orphaned mode details
```sql
SELECT pmd.* FROM payment_mode_details pmd
LEFT JOIN payment p ON pmd.id_payment = p.id_payment
WHERE p.id_payment IS NULL;
```

---

## Layer 6 — Root Cause Classification

| Category | Frequency | Risk | Example |
|---|---|---|---|
| Config-driven behavior | High | 🔴 | Wrong GST type, receipt mode, branch setting |
| Mode detection logic | Medium | 🟡 | MULTI vs single mode detection fails |
| Calculation error | Medium | 🔴 | GST, weight, discount, wallet |
| Gateway integration | Medium | 🟡 | Timeout, API change, credential expiry |
| Race condition | Low | 🔴 | Duplicate receipt numbers |
| Missing transaction wrap | Medium | 🔴 | Partial commits |
| SQL injection | Low | 🔴 | Raw concatenation in queries |
| Orphaned data | Medium | 🟡 | Deleted payment leaves mode details |

---

## Layer 7 — Transaction Integrity (Financial Module Special Layer)

### Verify `trans_begin/trans_commit` Coverage

| Flow | Transaction Wrapped? | Risk |
|---|---|---|
| SaveAll | ✅ Yes (L1550, L1255/rollback) | Low — but wraps per installment in loop |
| General Advance | ✅ Yes (L541, L796) | Low |
| Update_payment | ✅ Yes (L906, L1255) | ⚠️ Double `trans_begin` at L906 and L914 |
| Delete | ❌ **NO** | 🔴 HIGH — hard delete, no transaction, no cleanup |
| PDC Update | ❌ **NO** | 🔴 HIGH — PDC update + payment insert not wrapped |
| Online Verify | Varies | Some verify methods lack wrapping |

### Amount Validation Checklist
1. `payment.payment_amount` = Sum of `payment_mode_details.payment_amount` (where `is_active=1`)
2. `payment.act_amount` = `payment_amount + discount`
3. If wallet used: `payment_amount + redeemed_amount = total_due`
4. If multi-installment: each payment amount = total / installments

---

## Layer 8 — Variant Isolation (Config-Driven Behavior)

For any reported bug in Payment module:

1. **Identify active variant config**:
   ```sql
   SELECT scheme_type, gst, gst_type, flexible_sch_type, wgt_convert, wgt_store_as, 
          payment_chances, max_chance, allow_advance, allow_unpaid, allow_preclose,
          is_digi, interest, is_topup_scheme, is_lumpSum
   FROM scheme WHERE id_scheme = {SCHEME_ID};
   ```

2. **Check settings**:
   ```sql
   SELECT scheme_wise_receipt, receipt_no_set, branch_settings, has_lucky_draw,
          schemeacc_no_set, allow_wallet, edit_custom_entry_date, cost_center
   FROM chit_settings WHERE id_chit_settings = 1;
   ```

3. **Test same operation with different config**:
   - Different scheme type
   - Different GST type
   - Different payment mode
   - Different branch setting

---

## Layer 9 — Gateway-Specific Debugging

### Cashfree (pg_code=4)
| Check | Where | What |
|---|---|---|
| API URL correct | `branch_gateway.api_url` | Should be `https://api.cashfree.com/pg/orders` for production |
| x-api-version | Controller L3875 | Hardcoded `2022-09-01` — check if still valid |
| Response is array vs object | Controller L3892 | Cashfree returns array for multiple payments per order |
| Timeout | Controller L3868 | 8 seconds — may be too short |
| Log file | `log/{date}/cashfree/mob_response_{date}.txt` | Check for curl errors |

### Razorpay (pg_code=7)
| Check | Where | What |
|---|---|---|
| key_id / key_secret | `branch_gateway.param_3` / `param_1` | Correct credentials |
| Order ID format | Payment table `ref_trans_id` | Must match Razorpay's order_id format |
| Payment capture | Verify method | Auto-capture vs manual capture |

### PayU (pg_code=1)
| Check | Where | What |
|---|---|---|
| Hash verification | Controller L4633+ | `hash = sha512(key\|...\|salt)` — field order matters |
| Production vs test | Gateway URL | `payumoney.com` vs `test.payumoney.com` |
| Status mapping | Response `status` field | 'success' → 1, 'failure' → 3 |

### HDFC/CCAvenue (pg_code=2)
| Check | Where | What |
|---|---|---|
| AES encryption | `libraries/hdfc.php` | Must use correct working key |
| Request/Response encryption | Controller L4710+ | Encrypt request, decrypt response |
| Redirect URL | Gateway config | Must match merchant portal settings |

### EaseBuzz (pg_code=8)
| Check | Where | What |
|---|---|---|
| Hash calculation | Controller L4346+ | `hash = sha512(key\|txnid\|amount\|...\|salt)` |
| API endpoint | Gateway config | `https://dashboard.easebuzz.in/` for production |
| Status field | Response | 'success' / 'failure' / 'userCancelled' |

### TechProcess (pg_code=3)
| Check | Where | What |
|---|---|---|
| TransactionRequestBean | `libraries/techprocess/` | Bean class must be loaded |
| TransactionResponseBean | Library | Response parsing uses Bean methods |
| WSDL/API endpoint | Gateway config | Check if TechProcess API is still active |

---

## Layer 10 — Performance Hotspots

| Hotspot | Location | Issue | Mitigation |
|---|---|---|---|
| `get_receipt_no()` | Model L31-123 | Full table scan for MAX(receipt_no) | Add index on `receipt_no` + relevant columns |
| `payment_list_range()` | Model L311-484 | 6-table JOIN with date range filter | Ensure composite index on `date_payment` + `id_branch` |
| `get_paymentContent()` | Model L1386-1599 | 10+ table JOIN for single account | Cache frequently accessed account data |
| `payment.js` (357KB) | Frontend | Entire file loaded on every payment page | Split into page-specific bundles |
| Verify loop | Controller L3858 | Iterates all txn_ids with individual cURL calls | Consider batch API if gateway supports it |
| Receipt gen under load | Controller L84-113 | No table locking (commented out) | Re-enable `LOCK TABLES` or use `SELECT FOR UPDATE` |

---

## Quick Reference — Bug Lookup Table

| Symptom | Check First | Most Likely Cause | File:Line |
|---|---|---|---|
| Receipt number missing | `chit_settings.receipt_no_set` | Set to 1 but status never reached 1 | Model:L31-123 |
| Receipt number duplicated | Concurrent payments | No DB lock on receipt gen | Model:L42-43 |
| Wrong GST amount | `scheme.gst_type` value | Mismatch between scheme config and calculation | Controller:L1454-1460 |
| Weight calculation wrong | `scheme.fix_weight` | Wrong scheme type flag | Controller:L1461-1484 |
| Payment not in list | `id_branch` filter | Branch filtering hiding payment | Model:L444-456 |
| Mode mismatch | Mode detection chain | Bug in if/elseif chain | Controller:L1333-1385 |
| Online payment stuck | Gateway API response | Timeout, credential expiry, or API change | Controller:L3788+ |
| Account number not generated | `schemeacc_no_set` | Setting is 1 (manual) | Controller:L1500-1520 |
| SMS not sent | `sms_gateway` config | Gateway code doesn't match 1-5 | Controller:L5043-5053 |
| Wallet not deducted | `allow_wallet` setting | Wallet disabled or balance=0 | Controller:L1428-1444 |
| PDC not converting | `payment_status != 1` | PDC status not set to Success | Controller:L213-230 |
| Installment count wrong | `total_paid_ins` stale | Delete without count update | Controller:L1235-1239 |
| Edit loses mode details | Soft-delete + re-insert | Mode details recreated on edit | Controller:L906-1255 |
| Payment amount = 0 saved | No validation | Missing server-side amount check | Controller:L1299 |
| OTP always passes | `=` vs `==` bug | Assignment operator in condition | Controller:L5081 |
| IFSC NULL on PDC convert | Array key typo | `$pay['payee_ifsc]']` | Controller:L308 |
| Delete via bookmark/link | GET request CSRF | No POST/CSRF protection | Routes:L372 |
| Sync data missing | `integrationType` | Config not set to 1 or 2 | Controller:L321-325 |
| Advance adjustment wrong | `ret_advance_utilized` | Utilized amount not matching | Controller:L1710-1750 |
| Referral not credited | Installment milestone | `ref_benifitadd_ins` not reached | Controller:L3417-3484 |
