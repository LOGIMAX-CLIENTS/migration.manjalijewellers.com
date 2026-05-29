# Billing Module — Flow Risk Matrix

> **Round 8** | Built 2026-03-24
> Input: DATA_FLOW.md + CROSS_MODULE_MAP.md + live code scan

---

## State Machine: `ret_billing.bill_status` / `ret_taging.tag_status`

### Primary Entity: `ret_billing`

| State        | Value            | Set By (Module.Method)                          | Can Transition To              | Guard / Precondition                           |
| ------------ | ---------------- | ----------------------------------------------- | ------------------------------ | ---------------------------------------------- |
| Active       | `status=1`       | Billing.`billing()` save                        | Cancelled(0), Paid(varies)     | Estimation linked, tag available               |
| Cancelled    | `status=0`       | Billing.`cancel_bill()`                         | — (terminal)                   | OTP verified (if OTP cancel enabled)           |
| Credit Bill  | `cust_type='C'`  | Billing.`billing()` save (credit mode)          | Receipt issued, Active         | Customer has credit limit                      |
| Issue/Rcpt   | `type='I'/'R'`   | Billing.`issue()` / `receipt()`                 | Closed                         | Borrower valid                                 |

### Secondary State: `ret_taging.tag_status`

| State     | Value | Set By                              | Can Transition To      | Guard                              |
| --------- | ----- | ----------------------------------- | ---------------------- | ---------------------------------- |
| Available | `0`   | Tagging.save / Billing.cancel_bill  | Reserved(1), Sold(2)   | —                                  |
| Reserved  | `1`   | Estimation.save                     | Available(cancel), Sold | Estimation valid                   |
| Sold      | `2`   | Billing.`billing()` save            | Available (cancel)     | `get_tag_status() == 0 or 1`       |
| Deleted   | `D`   | Tagging.verify_otp()                | — (terminal)           | OTP required ⚠️ NO GUARD in billing |

### Secondary State: `ret_estimation.estbillid`

| State    | Value     | Set By                    | Can Transition To | Guard                      |
| -------- | --------- | ------------------------- | ----------------- | -------------------------- |
| Unbilled | `NULL`    | Estimation.save           | Billed            | —                          |
| Billed   | `bill_id` | Billing.`billing()` save  | Unbilled (cancel) | `is_estno_already_billed()` check |

---

## Inbound Contracts (What Billing Expects from Upstream)

| Upstream Module | Data / State Expected                              | Precondition Check in Code?          | Line      | Risk if Violated                                              |
| --------------- | -------------------------------------------------- | ------------------------------------ | --------- | ------------------------------------------------------------- |
| Tagging         | `tag_status = 0` (Available) or 1 (Reserved)       | ✅ YES: `get_tag_status()` model      | L145-152  | Bill references already-sold tag → phantom revenue / conflict |
| Tagging         | `ret_taging_stones` records exist for tag           | ❌ NO check                           | —         | Missing stone data, silent NaN in totals                      |
| Estimation      | `ret_estimation.estbillid = NULL` (not double billed) | ✅ YES: `is_estno_already_billed()`  | L10197    | Double billing of same estimation                             |
| Estimation      | All `ret_estimation_items` have valid `tag_id`     | ❌ NO check                           | —         | Bill line references deleted/invalid tag                      |
| Customer        | `id_customer` valid and not deleted                | ✅ YES: FK constraint                 | DB level  | DB error on save obscures root cause                          |
| Customer        | Credit limit available (for credit bills)          | ⚠️ PARTIAL: `get_mc_va_limit()` check | L8418     | Over-credit silently allowed if limit not enforced            |
| Day Closing     | Day closed for correct date                        | ⚠️ PARTIAL: `getBranchDayClosingData()` | L2736   | Bill posts on wrong date (prior day)                          |
| Metal Rates     | Rates current at time of billing                   | ❌ NO check (fetched at page load)    | —         | Stale rate used if user delays submission                     |
| Orders          | Order advance not already fully consumed           | ⚠️ PARTIAL: `get_order_advance()`     | L2167     | Over-adjustment, advance goes negative                        |

---

## Outbound Contracts (What Billing Guarantees to Downstream)

| Downstream Module | What Billing Guarantees                              | Enforced How?                          | Risk if Broken                                     |
| ----------------- | ---------------------------------------------------- | -------------------------------------- | -------------------------------------------------- |
| Accounts          | Journal entry = bill total (DR/CR balanced)          | PHP calculation, no post-write check   | Imbalanced journal → financial statement errors    |
| Accounts          | Advance reversal journal on cancel                   | `cancel_bill()` logic                  | Advance not reversed → overstated liability        |
| Estimation        | `estbillid` set on billing save, cleared on cancel   | Explicit UPDATE in save + cancel       | If save crashes after update, orphan link remains  |
| Estimation        | `ret_estimation_items.purchase_status = 1` on sale   | Explicit UPDATE in save                | Items marked sold but bill doesn't exist           |
| Tagging           | `tag_status = 2` (sold) on billing save              | Explicit UPDATE in billing save        | Tag shows available when it's actually sold        |
| Tagging           | `tag_status = 0` (available) on bill cancel          | `cancel_bill()` explicit UPDATE        | Tag permanently locked if cancel fails mid-way     |
| Reports           | `ret_billing.total = sum(ret_bill_details totals)`   | PHP sum, no server-side validation     | Report shows wrong total; discrepancy not caught   |
| POS System        | Transaction reference created on POS payment         | `UploadBilledTransaction()` called     | ⚠️ CONTRACT GAP: failure is not retried / flagged  |

---

## Reversal Contracts (Cancel Bill)

> Primary operation: `cancel_bill()` at ~L7581-8001

| What Was Created (SAVE)                          | Tables That Must Be Restored            | Actually Restored? | Method & Line               | Gap?                                     |
| ------------------------------------------------ | --------------------------------------- | ------------------ | --------------------------- | ---------------------------------------- |
| `ret_billing` header record                      | `ret_billing.status → 0`               | ✅ YES             | `cancel_bill()` L~7700      | —                                        |
| `ret_bill_details` line items                    | Soft-deleted via status                 | ✅ YES             | `cancel_bill()`             | —                                        |
| `ret_billing_payment` records                    | Reversed / marked cancelled             | ✅ YES             | `cancel_bill()`             | —                                        |
| `ret_billing_item_stones` stone records          | Status update / soft-delete             | ⚠️ PARTIAL         | `cancel_bill()`             | Stone count may not be zeroed            |
| `ret_bill_other_metals` records                  | Status update                           | ⚠️ PARTIAL         | `cancel_bill()`             | Verify old metal reversal                |
| `ret_taging.tag_status → 0` (available)          | `tag_status` reset                      | ✅ YES             | `cancel_bill()`             | —                                        |
| `ret_estimation.estbillid → NULL`               | `estbillid` cleared                     | ✅ YES             | `cancel_bill()`             | —                                        |
| `ret_estimation_items.purchase_status → 0`      | `purchase_status` reset                 | ✅ YES             | `cancel_bill()`             | —                                        |
| `ret_journal` entries                            | Reverse journal entries                 | ✅ YES             | `cancel_bill()` + accounts  | —                                        |
| `ret_billing_advance` advance adjustments        | Advance restored to customer            | ⚠️ PARTIAL         | `cancel_bill()`             | Not all advance types may be reversed    |
| POS transaction record (if POS payment used)     | POS transaction voided/reversed         | ❌ NO              | —                           | ⚠️ POS transaction not auto-reversed on cancel |
| Order advance adjustment                         | Order advance restored                  | ⚠️ PARTIAL         | `cancel_bill()`             | Cross-module — depends on order state    |
| Wallet transaction (if wallet payment)           | Wallet balance restored                 | ⚠️ PARTIAL         | `cancel_bill()` → wallet    | Verify wallet debit reversal             |
| Service bill reversal (service_bill cancel)      | `ret_billing_service` status            | ✅ YES             | `cancel_service_bill()`     | —                                        |

---

## Flow Risk Checklist (QA-Ready Test Scenarios)

| ID          | Test Scenario                                                           | Expected Result                            | Priority   | Verified? |
| ----------- | ----------------------------------------------------------------------- | ------------------------------------------ | ---------- | --------- |
| FR-BIL-001  | Bill a tag that is already sold (`tag_status = 2`)                      | REJECT with "tag already sold" error        | 🔴 HIGH    | ❌        |
| FR-BIL-002  | Bill an estimation that is already billed (`estbillid ≠ NULL`)          | REJECT with "already billed" error          | 🔴 HIGH    | ❌        |
| FR-BIL-003  | Edit a bill after POS payment has been processed                        | WARN or REJECT edit                         | 🔴 HIGH    | ❌        |
| FR-BIL-004  | Cancel a bill → verify ALL tables restored (see Reversal Contracts)     | Full restoration across all 10+ tables     | 🔴 HIGH    | ❌        |
| FR-BIL-005  | Cancel a bill paid via POS → verify POS transaction voided              | POS transaction reversed                    | 🔴 HIGH    | ❌        |
| FR-BIL-006  | Cancel bill with wallet payment → verify wallet balance restored        | Wallet balance increased by bill amount     | 🔴 HIGH    | ❌        |
| FR-BIL-007  | Cancel bill → re-bill same estimation and tag                           | Should work normally after cancel           | 🟡 MED     | ❌        |
| FR-BIL-008  | Two users bill same tag concurrently (race condition test)               | One succeeds, one gets "tag sold" error     | 🔴 HIGH    | ❌        |
| FR-BIL-009  | Bill save crashes after estimation update but before billing insert      | No orphan estimation link; no orphan bill   | 🔴 HIGH    | ❌        |
| FR-BIL-010  | Customer TCS percentage changes between session load and submit          | Server-side rate used at time of save       | 🟡 MED     | ❌        |
| FR-BIL-011  | Credit bill exceeds customer credit limit                                | REJECT with limit exceeded message          | 🟡 MED     | ❌        |
| FR-BIL-012  | Bill with advance adjustment → cancel → re-bill                         | Advance fully restored and re-adjustable   | 🔴 HIGH    | ❌        |
| FR-BIL-013  | Split bill: cancel one split → verify correct partial reversal           | Only split portion reversed                 | 🟡 MED     | ❌        |
| FR-BIL-014  | Order-converted bill: cancel → verify order advance restored            | Order advance credited back                 | 🟡 MED     | ❌        |
| FR-BIL-015  | POS payment: `postcurlPOSRequests` timeout → bill save outcome          | No orphan POS record; bill not saved        | 🔴 HIGH    | ❌        |

---

## POS Integration Flow Risk (New in Round 8)

> The POS (Point-of-Sale) subsystem was added since Round 7. Key risks:

| Risk                                      | Severity  | Description                                                                              |
| ----------------------------------------- | --------- | ---------------------------------------------------------------------------------------- |
| POS transaction not reversed on cancel    | 🔴 HIGH   | `cancel_bill()` does not auto-void POS transaction via `cancelTransactionRequest()`      |
| `postcurlPOSRequests()` failure handling  | 🔴 HIGH   | cURL failures to POS API may not propagate error to caller — silent failure risk          |
| `UploadBilledTransaction()` not retried   | 🟡 MED    | If upload fails at T+1 after bill save, POS system is out of sync with billing DB        |
| `phonepeCallback()` concurrency          | 🟡 MED    | Webhook callback can arrive before or after `billing()` save completes — race condition  |
| Salt key exposure                         | 🟡 MED    | `getPhonePeSaltKey()` returns raw key — verify it is not logged or exposed in responses |
