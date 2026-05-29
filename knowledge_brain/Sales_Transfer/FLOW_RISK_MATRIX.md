# Sales Transfer Module — Flow Risk Matrix

> **Module**: Sales_Transfer
> **Built**: 2026-03-25 — Round 6
> **Input Sources**: DATA_FLOW.md, CROSS_MODULE_MAP.md, MODULE_BRAIN.md (Rounds 1–5)
> **Purpose**: Handoff contracts and QA-testable specifications for the Sales Transfer state machine

---

## 1. State Machine: `ret_taging.tag_status`

The primary entity of this module is the **tag** (`ret_taging`). The Sales Transfer module drives tags through a transit state machine.

| State | Value | Set By (Module.Method) | Can Transition To | Guard / Precondition |
|---|---|---|---|---|
| Available | 0 | Multiple sources (initial tag creation, billing cancel) | In-Transit(4), Sold(1→Billing), Deleted | — |
| In-Transit | 4 | `Sales_Transfer.create_sales_transfer()` L51 | Available(0) via Download | Tag must have `tag_status=0` at time of request ⚠️ NO GUARD — not verified in code |
| In-Transit (Return) | 4 | `Sales_Transfer.create_sales_ret_transfer()` L168 | Available(0) via Return Download | Tag must have `tag_status=0` at destination ⚠️ NO GUARD |
| Available (moved) | 0 | `Sales_Transfer.update_sales_transfer_request()` L101 | Next transfer cycle | Only set if source tag_status=4 **NO GUARD against partial scans** |
| Available (returned) | 0 | `Sales_Transfer.update_sales_ret_transfer()` L188 | Next transfer cycle | tag_status=4 check present; tag_status=6 silently accepted (different path) |

> **Status 6** — tags with `tag_status=6` are handled in the "Not Against Bill" return sub-variant. These tags skip status update entirely (only `current_branch` changes).

### Tag Status Log — `ret_taging_status_log`

| Event | Status Logged | Set By |
|---|---|---|
| Tag queued for Sales Transfer | 11 | `create_sales_transfer()` |
| Tag received at destination (Sales Transfer) | 0 | `update_sales_transfer_request()`, `update_TagScan()` |
| Tag queued for Sales Return Transfer | 12 | `create_sales_ret_transfer()` |
| Tag received at origin (Return Transfer) | 0 | `update_sales_ret_transfer()`, `update_ret_TagScan()` |

---

## 2. Inbound Contracts (What This Module Expects from Upstream)

| # | Upstream Module | Data/State Expected | Precondition Check in Code? | Line | Risk if Violated |
|---|---|---|---|---|---|
| IC-01 | Tagging | `ret_taging.tag_status = 0` (Available) before Sales Transfer Request | ❌ NO CHECK | — | ⚠️ Tag already sold/in-transit gets re-queued for transfer — inventory count mismatch |
| IC-02 | Tagging | `ret_taging.tag_status = 0` at destination before Return Transfer Request | ❌ NO CHECK | — | Return initiated against already-transferred tag |
| IC-03 | Settings | `ret_day_closing.entry_date` present and valid for BOTH from/to branches | ✅ YES — date comparison L242-247 in `update_sales_transfer_request()` | L242 | If missing: `$bill_date` ← NULL (Risk #12 confirmed in R3) |
| IC-04 | Billing | `ret_billing_model.code_number_generator()` returns valid bill number | ✅ YES — implied (used immediately) | L110 | No generated bill number → NULL bill_no in `ret_billing` |
| IC-05 | Settings | `is_metal_for_billing` setting key exists in `ret_settings` | ⚠️ PARTIAL — JS checks value but no PHP server-side validation | L103-106 | Metal validation skipped on server |
| IC-06 | Tagging / Sales Transfer | Tags in `ret_bill_details` still have `tag_status=4` at Download time | ⚠️ PARTIAL — `getSalesTrans_Tag()` filters by `tag_status=4` | L99 | If tag was manually reset to 0: tag may be downloaded twice |
| IC-07 | Financial Year | Active financial year exists in `ret_financial_year` | ✅ YES — `get_FinancialYear()` called | L112 | Wrong fin_year_code in bill; ref number out of sequence |

---

## 3. Outbound Contracts (What This Module Guarantees to Downstream)

| # | Downstream Module | What This Module Guarantees | Enforced How? | Risk if Broken |
|---|---|---|---|---|
| OC-01 | Billing / Reports | `ret_billing.bill_type = 13` for Sales Transfer, `14` for Return Transfer | Hardcoded in INSERT | Reports filtering by bill_type must handle both; confusion if 13 ≠ 14 |
| OC-02 | Billing / Reports | `ret_billing.tot_bill_amount` = sum of all tag `item_cost` | Updated after loop — but `decimal(10,0)` truncates fractions | ⚠️ **Risk #20/#23**: integer truncation causes totals mismatch in reports |
| OC-03 | Billing / Reports | `ret_billing.is_credit = 1`, `credit_status = 2` for Sales Transfer | Hardcoded in INSERT | ✅ Guaranteed for Sales Transfer (bill_type=13) |
| OC-04 | Billing / Reports | `ret_billing.is_credit = 1`, `credit_status = 2` for Return Transfer | ❌ NOT SET — schema default: `is_credit=0, credit_status=1` | ⚠️ **RISK #21 / CONTRACT GAP**: Return transfer bills appear as non-credit, fully paid |
| OC-05 | Tagging | `ret_taging.tag_status = 4` (In-Transit) immediately after Request | Set inside `trans_begin/trans_commit` per-tag | ✅ Guaranteed IF transaction completes |
| OC-06 | Tagging | `ret_taging.current_branch` updated to `$to_brn` on Download | Set in `update_sales_transfer_request()` L101 | ✅ Guaranteed per-tag inside loop |
| OC-07 | Billing | `ret_bill_return_details` created linking return bill to original bill | Set in `create_sales_ret_transfer()` L166 | ✅ Guaranteed; however, original `bill_id` resolved via `getBillId()` — gap if bill number lookup fails |
| OC-08 | Tagging | `ret_taging_status_log` INSERT for every status change | Set in all write methods | ✅ Present in all paths; status=11/12/0 per event |
| OC-09 | Billing / Reports | `ret_billing.billing_for = 3` (Transfer type) | Hardcoded in INSERT | ⚠️ **Risk #24**: `billing_for=3` undocumented in DB enum comment (only 1=Customer, 2=Company) |
| OC-10 | Billing | `ret_billing.tot_bill_amount` is NEGATIVE for return transfers | `UPDATE ret_billing SET tot_bill_amount = -$tot_bill_amount` L171 | ✅ Guaranteed; downstream must handle negative amounts |
| OC-11 | Billing | Per-category return transfer creates SEPARATE bill records | One INSERT per category in `create_sales_ret_transfer()` | ⚠️ **Risk #13 / CONTRACT GAP**: `$tot_bill_amount` accumulates across categories — 2nd bill INCLUDES 1st bill's total |

---

## 4. Reversal Contracts (Cancel / Delete / Reverse)

> ⚠️ **CRITICAL**: There is **NO cancel/delete flow** for sales transfers in this module (Risk #7). The reversal contract analysis therefore covers the RETURN TRANSFER as the only reversal mechanism.

| # | Operation | Tables That Must Be Restored | Actually Restored in Code? | Method & Line | Gap? |
|---|---|---|---|---|---|
| RC-01 | Sales Return Transfer (reverse of Sales Transfer) | `ret_taging.tag_status` → 0 (Available) | ✅ YES | `update_sales_ret_transfer()` L188, `update_ret_TagScan()` | — |
| RC-02 | Sales Return Transfer | `ret_taging.current_branch` → original branch | ✅ YES | `update_sales_ret_transfer()` L188 (`$to_brn` = origin) | — |
| RC-03 | Sales Return Transfer | `ret_taging_status_log` audit entry | ✅ YES | All return download methods INSERT status=0 | — |
| RC-04 | Sales Return Transfer | `ret_billing` download_date/download_by for return bill | ✅ YES | `update_sales_ret_transfer()` L397 | ⚠️ BUT `$tb_entry_date` undefined at L397 — download_date=NULL (Risk #4) |
| RC-05 | Sales Return Transfer | `ret_billing.tot_bill_amount` = negative (reversal) | ✅ YES | `create_sales_ret_transfer()` L171 — negative amount | — |
| RC-06 | Sales Return Transfer | `ret_bill_details` items status (purchase_status) restored | ❌ NO | N/A | ⚠️ **CONTRACT GAP**: `ret_bill_details` records from original Sales Transfer not updated on return |
| RC-07 | Sales Return Transfer | `ret_billing.credit_status` of ORIGINAL bill cleared/updated | ❌ NO | N/A | ⚠️ **CONTRACT GAP**: Original Sales Transfer bill credit_status not reconciled on return |
| RC-08 | No hard-cancel flow | All tables written during CREATE | ❌ N/A — NO CANCEL EXISTS | — | ⚠️ **MAJOR GAP**: Tags can get permanently stuck at `tag_status=4` if transfer is abandoned; only fix is manual DB update |
| RC-09 | Sales Return Transfer | Transaction wrapping of all writes | ❌ NO `trans_begin()` in `update_sales_ret_transfer()` | Risk #17 confirmed R2 | ⚠️ **CONTRACT GAP**: Partial write possible — tag status updated but billing not updated (or vice versa) |

---

## 5. Flow Risk Checklist (QA-Ready Test Scenarios)

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-ST-001 | CREATE Sales Transfer with a tag that has `tag_status ≠ 0` (e.g., already sold or in-transit) | REJECT with error message | 🔴 HIGH | ❌ |
| FR-ST-002 | CREATE Sales Transfer from branch A to branch A (same branch) | REJECT — no self-transfer | 🟡 MED | ❌ |
| FR-ST-003 | DOWNLOAD Sales Transfer when `to_branch.entry_date < from_branch.entry_date` | REJECT with day-closing error | 🔴 HIGH | ❌ |
| FR-ST-004 | DOWNLOAD Sales Transfer when `getAllBranchDCData()` returns array (Risk #12) | `$bill_date` should NOT be NULL | 🔴 HIGH | ❌ |
| FR-ST-005 | CREATE Sales Transfer → verify `ret_taging.tag_status = 4` and `ret_taging_status_log` entry with status=11 | Both records exist with correct values | 🔴 HIGH | ❌ |
| FR-ST-006 | DOWNLOAD Sales Transfer (batch) → verify `ret_taging.tag_status = 0`, `current_branch = to_brn`, log entry status=0 | All three verified | 🔴 HIGH | ❌ |
| FR-ST-007 | SCAN DOWNLOAD → scan same tag twice | Duplicate rejected; tag not double-downloaded | 🔴 HIGH | ❌ — Risk #31: duplicate NOT prevented |
| FR-ST-008 | SCAN DOWNLOAD → scan > 5 tags → verify localStorage not corrupted | All tags remain in localStorage; no integer stored | 🔴 HIGH | ❌ — Risk #29: `Array.push()` corrupts localStorage |
| FR-ST-009 | CREATE Sales Transfer → verify `ret_billing.is_credit = 1`, `credit_status = 2` | Both set correctly | 🟡 MED | ❌ |
| FR-ST-010 | CREATE Sales Return Transfer → verify `ret_billing.is_credit = 1`, `credit_status = 2` | Should be set (currently NOT set — Risk #21) | 🔴 HIGH | ❌ — known gap |
| FR-ST-011 | CREATE Sales Return Transfer Per Category → verify each bill.tot_bill_amount = only ITS category total | No cross-category accumulation | 🔴 HIGH | ❌ — Risk #13: accumulates across categories |
| FR-ST-012 | DOWNLOAD Sales Return Transfer → verify `$tb_entry_date` is correctly resolved | `download_date` in `ret_billing` is NOT NULL | 🔴 HIGH | ❌ — Risk #4: undefined variable |
| FR-ST-013 | DOWNLOAD Sales Return Transfer → verify `trans_begin()/trans_commit()` wraps all writes | No partial commit if one write fails | 🔴 HIGH | ❌ — Risk #17: no `trans_begin()` |
| FR-ST-014 | CREATE Sales Transfer → verify `tot_bill_amount` in `ret_billing` matches SUM of `ret_bill_details.item_cost` | Values match (after decimal truncation risk acknowledged) | 🟡 MED | ❌ — Risk #20/#23: integer truncation |
| FR-ST-015 | GST calculation: Create transfer between SAME state branches → verify SGST+CGST, not IGST | CGST + SGST each = (taxable × 3%) / 2 | 🟡 MED | ❌ |
| FR-ST-016 | GST calculation: Create transfer between DIFFERENT state branches → verify IGST only | IGST = taxable × 3%; CGST/SGST = 0 | 🟡 MED | ❌ |
| FR-ST-017 | ABANDON transfer (never download) → verify tag is NOT permanently stuck at `tag_status=4` | Should be recoverable (currently no cancel flow — Risk #7) | 🔴 HIGH | ❌ — no cancel mechanism exists |
| FR-ST-018 | Complete Sales Transfer → issue Return Transfer for same tags → verify original bill credit linkage | `ret_bill_return_details` correctly links via `ref_bill_id` | 🟡 MED | ❌ |
| FR-ST-019 | Two users simultaneously download same sales transfer bill | One succeeds, one gets conflict/error | 🟡 MED | ❌ |
| FR-ST-020 | CREATE Sales Transfer with `form_secret` mismatch | REJECT — CSRF protection | 🔴 HIGH | ❌ — Risk #19 (form_secret never validated server-side) |
| FR-ST-021 | GST rate: create transfer for item that should be 0% or 1.5% GST | Should apply correct rate, NOT hardcoded 3% | 🔴 HIGH | ❌ — Risk #2/#26: 3% hardcoded in both PHP and JS |

---

## Risk Cross-Reference (Risks Directly Supporting This Matrix)

| Risk # | Severity | Relates To Section | Description (brief) |
|---|---|---|---|
| Risk #1 | P0 | IC-01 to IC-07, OC-01+ | SQL Injection — all model queries use raw concatenation |
| Risk #2 | P1 | FR-ST-021 | PHP hardcoded 3% tax rate |
| Risk #3 | P0 | OC-01 | Undefined `$insId` in return transfer response |
| Risk #4 | P0 | RC-04 | Undefined `$tb_entry_date` → NULL `download_date` |
| Risk #5 | P1 | OC-05 | `trans_rollback()` without `trans_begin()` in download |
| Risk #7 | Info | RC-08 | No delete/cancel flow — tags can get stuck |
| Risk #8 | P1 | RC-09 | Transaction scope bug in `create_sales_ret_transfer()` |
| Risk #11 | P0 | OC-01 | Undefined `$insId` in Sales Transfer download response |
| Risk #12 | P0 | IC-03 | `getAllBranchDCData()` array structure misuse → NULL `$bill_date` |
| Risk #13 | P0 | OC-11 | Cross-category `$tot_bill_amount` accumulation |
| Risk #17 | P0 | RC-09 | No `trans_begin()` in `update_sales_ret_transfer()` |
| Risk #21 | P0 | OC-04 | Return transfer: `is_credit=0, credit_status=1` by default |
| Risk #26 | P1 | FR-ST-021 | JS hardcoded 3% GST in `calculateSaleBillRowTotal()` |
| Risk #29 | P0 | FR-ST-008 | `Array.push()` return corrupts localStorage — duplicates allowed |
| Risk #31 | P1 | FR-ST-007 | Duplicate scan not prevented — no early return |
