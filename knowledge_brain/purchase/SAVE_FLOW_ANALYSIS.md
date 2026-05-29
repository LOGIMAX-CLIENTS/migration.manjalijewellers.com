# PURCHASE MODULE — SAVE FLOW ANALYSIS & VALIDATION GAPS
> **Round:** 5 | **Date:** 2026-02-23 | **Focus:** Controller save tracing, JS-PHP validation parity, error handling

---

## Transaction Pattern Analysis

### Corrected Stats (from Round 4)
```
trans_begin():    51 calls
trans_commit():   51 calls  ← CORRECTION: 51, not 0
trans_rollback(): 54 calls
trans_start():     0 calls
trans_complete():  0 calls
```

**Pattern Used:** Manual `trans_begin()` → `trans_commit()` / `trans_rollback()` (not CI3's automatic `trans_start()`/`trans_complete()`).

**Remaining Risk:** The 26 `echo last_query();exit;` statements still sit between `trans_begin()` and `trans_rollback()` — execution halts before rollback executes, leaving partial commits. The `trans_commit()` on the success path works correctly.

---

## 12 Save Blocks — Server-Side Validation Audit

| # | Line | Sub-Module | Server Validation? | JS Validation? | Gap |
|---|---|---|---|---|---|
| 1 | 272 | Order Description | ❌ None | ❌ None | No validation anywhere |
| 2 | 4911 | Bill Entry | ⚠️ Minimal | ✅ `validateBillEntryForm()` | PHP trusts JS entirely |
| 3 | 5493 | Payment | ⚠️ Minimal | ✅ `validateChqDetailRow()` etc. | PHP trusts JS entirely |
| 4 | 6181 | Rate Fixing | ❌ None | ⚠️ Partial | Financial data unsanitized |
| 5 | 6742 | Rate Fix Approval | ❌ None | ⚠️ Partial | Approval with no check |
| 6 | 9520 | GRN Entry | ❌ None | ✅ `validate_grn_entry_form()` | PHP trusts JS entirely |
| 7 | 11475 | Supplier Rate Cut | ❌ None | ❌ None | No validation anywhere |
| 8 | 12321 | Smith Op Bal | ❌ None | ✅ `SmithCompanyOpBalanceSave()` | PHP trusts JS entirely |
| 9 | 12528 | NonTag Lot Gen | ❌ None | ✅ `ValidateNonTagLotGenerate()` | PHP trusts JS entirely |
| 10 | 12810 | NonTag Receipt | ❌ None | ✅ `SaveNtReceiptForm()` | PHP trusts JS entirely |
| 11 | 13034 | Credit/Debit Entry | ❌ None | ⚠️ Partial | Financial amounts direct |
| 12 | 13301 | Metal Issue Receipt | ❌ None | ⚠️ Partial | Weight data unsanitized |

### Key Finding: **ZERO Server-Side Validation**
Not a single save method validates:
- Required field presence (`empty()` checks)
- Numeric field types (`is_numeric()`)
- Date format validity
- Amount range sanity (negative amounts, zero weight, etc.)
- Authorization/ownership (can user edit this PO?)

All validation is delegated to JavaScript, which can be bypassed by any HTTP tool (cURL, Postman, browser DevTools).

---

## AJAX Error Handler Coverage

| Metric | Count |
|---|---|
| Total `$.ajax()` calls | **185** |
| With `error: function` handler | **72** (39%) |
| **Missing error handlers** | **113** (61%) |

When any of those 113 AJAX calls fails (network issue, 500 error, timeout), the user sees **nothing** — the UI freezes with no feedback.

---

## Insecure File Permissions

5 `mkdir()` calls use **0777** (world-writable):

| Line | Path |
|---|---|
| 870 | Purchase order images |
| 2429 | Bill entry images |
| 2625 | `vendor_ack` folder |
| 9617 | GRN entry images |
| 10105 | Purchase entry images |

**Risk:** Any server user can read/write/execute files in these directories.

---

## JS-to-PHP Validation Parity Matrix

| Sub-Module | JS Validates | PHP Validates | Parity |
|---|---|---|---|
| Purchase Order | ✅ `validateOrderDetailRow()`, `validatePurOrderDetailRow()` | ❌ | 🔴 JS-only |
| Bill Entry | ✅ `validateBillEntryForm()`, `validate_charges_row()` | ❌ | 🔴 JS-only |
| QC Issue/Receipt | ✅ `validate_qc_receipt_details()` | ❌ | 🔴 JS-only |
| GRN Entry | ✅ `validate_grn_entry_form()`, `validateGrnItemDetailRow()` | ❌ | 🔴 JS-only |
| Lot Generate | ✅ `validate_lot_generate_row()` | ❌ | 🔴 JS-only |
| Metal Issue | ✅ `validateMetalIssueRow()`, `validateKarigarMetailIssueDetailRow()` | ❌ | 🔴 JS-only |
| NonTag Lot/Receipt | ✅ `ValidateNonTagLotGenerate()`, `SaveNtReceiptForm()` | ❌ | 🔴 JS-only |
| Smith Op Bal | ✅ `SmithCompanyOpBalanceSave()` | ❌ | 🔴 JS-only |
| Payment | ✅ `validateChqDetailRow()`, `validateNetBankingDetailRow()`, `validateSalesDetailRow()` | ❌ | 🔴 JS-only |
| Order Description | ❌ | ❌ | 🔴 None |
| Supplier Rate Cut | ❌ | ❌ | 🔴 None |
| Credit/Debit | ⚠️ Partial | ❌ | 🔴 JS-only |

**Result:** All 12 sub-modules have **zero server-side validation**. The system relies entirely on client-side JavaScript validation, which can be bypassed.

---

## `echo last_query();exit;` Inside Transaction Blocks — Impact Map

Each `echo...exit;` that occurs AFTER `trans_begin()` but BEFORE `trans_commit()` on the **success path** would prevent the commit from executing. However, the actual pattern shows these `echo...exit;` statements are on the **error/rollback path** (inside the `else` block after `trans_status()===FALSE`). This means they execute *instead of* `trans_rollback()`.

**Impact:** When a transaction fails, instead of rolling back:
1. The SQL query is echoed to the client (information leak)
2. `exit;` prevents `trans_rollback()` from executing
3. PHP's connection close triggers an implicit commit of whatever was partially written

---

## Summary — Round 5 Findings

| Finding | Count | Severity |
|---|---|---|
| Save blocks with no server-side validation | **12/12** | 🔴 CRITICAL |
| AJAX calls missing error handlers | **113/185** | 🟠 HIGH |
| `mkdir 0777` (insecure permissions) | **5** | 🟡 MEDIUM |
| JS-PHP validation parity failures | **12/12** | 🔴 CRITICAL |
| Corrected: `trans_commit()` exists | 51 | ✅ CORRECTION |

## Cumulative Bug Count (Rounds 2-5)

| Round | Bugs Found | Key Pattern |
|---|---|---|
| R2 | 4 | Retagging method bugs |
| R4 | 8 patterns | Security scan (debug, $_POST, raw SQL) |
| R5 | 4 patterns | Validation gaps, error handlers, mkdir |
| **Total** | **16 patterns** | **700+ affected lines** |