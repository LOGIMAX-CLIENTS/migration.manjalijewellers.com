# Handoff Audit — Cross-Module Contract Verification

> Aggregated from FLOW_RISK_MATRIX.md across 21 modules with flow documentation.
> Last updated: 2026-03-26 (Round 2 refresh)
> Purpose: Verify that every module-to-module data handoff has matching contracts
> Total handoff gaps: 11 | Verified: 7

---

## Critical Handoff Gaps (No Contract or Broken Contract)

> [!CAUTION]
> These handoffs have NO matching inbound/outbound contracts. Data crosses module boundaries without validation.

### HO-001: Tagging → Billing (Tag Availability)

| Direction | Contract | Status |
|---|---|---|
| **Outbound (Tagging)** | Guarantees `tag_status=0` means available | ⚠️ No exclusive lock — concurrent assignment possible |
| **Inbound (Billing)** | Expects `tag_status = 0 or 1` before billing | ✅ YES: `get_tag_status()` check exists |
| **Gap** | **No atomic lock between check and status write** — two billing sessions can read `tag_status=0` for the same tag | |
| **Severity** | 🔴 CRITICAL — double-sale of same tag possible | |

### HO-002: Estimation → Billing (Estimation Data Integrity)

| Direction | Contract | Status |
|---|---|---|
| **Outbound (Estimation)** | `ret_estimation.total_cost` = correct total | ⚠️ NO server-side recalculation — JS only |
| **Inbound (Billing)** | Reads estimation total for bill calculation | ✅ Reads from DB |
| **Gap** | **If JS calculation diverges from DB-stored value, billing inherits wrong total** | |
| **Severity** | 🔴 HIGH — incorrect invoice amount | |

### HO-003: Estimation → Billing (Transaction Integrity)

| Direction | Contract | Status |
|---|---|---|
| **Outbound (Estimation)** | Transaction rolls back on failure | ❌ `trans_commit()` called in error branch (EST-R601) |
| **Inbound (Billing)** | Expects valid estimation record exists | ✅ |
| **Gap** | **Estimation saves partial data even on error — Billing references corrupt estimation** | |
| **Severity** | 🔴 CRITICAL — data integrity breach | |

### HO-004: Branch Transfer Cancel → ALL (NO Reversal)

| Direction | Contract | Status |
|---|---|---|
| **Outbound (BT Cancel)** | Should restore all stock effects from transit | ❌ ZERO reversal logic — only sets master status=3 |
| **Inbound (Tagging)** | Expects `tag_status` restored | ❌ Tags stuck at status=4 |
| **Inbound (Non-Tag Stock)** | Expects weight deduction reversed | ❌ Not reversed |
| **Inbound (Billing)** | Expects `transferred_to_acc_stock` cleared | ❌ Not cleared |
| **Inbound (Packaging)** | Expects `ret_other_inventory..status` restored | ❌ Not restored |
| **Gap** | **Cancel after transit = permanent data corruption across 7+ tables** | |
| **Severity** | 🔴 CRITICAL — worst reversal gap in system | |

### HO-005: Billing → POS (POS Transaction Reversal)

| Direction | Contract | Status |
|---|---|---|
| **Outbound (Billing)** | POS transaction created on bill save via `UploadBilledTransaction()` | ✅ Created |
| **Outbound (Billing Cancel)** | POS transaction voided on cancel | ❌ NOT auto-reversed |
| **Gap** | **POS system retains active transaction reference after bill cancellation** | |
| **Severity** | 🔴 HIGH — POS/billing out of sync | |

### HO-006: Tagging Delete → Branch Transfer (Orphaned Transfer)

| Direction | Contract | Status |
|---|---|---|
| **Outbound (Tagging Delete)** | Should clean up BT records for deleted tag | ❌ NO cleanup in delete case |
| **Inbound (BT)** | Expects tag exists | ✅ Reads tag data |
| **Gap** | **Branch transfer shows pending item for a tag that no longer exists** | |
| **Severity** | 🟡 MEDIUM — orphan data, no financial impact | |

### HO-007: Old Metal → LOT (Lot Duplication)

| Direction | Contract | Status |
|---|---|---|
| **Outbound (Old Metal)** | Creates lot (lot_from=2/4/5) on receipt | ✅ Created |
| **Inbound (LOT)** | Expects unique lot per process | ❌ NO guard against duplicate lot creation |
| **Gap** | **Multiple receipts for same melting create multiple lots without deduplication** | |
| **Severity** | 🟡 MEDIUM — phantom inventory |

### HO-008: Billing → Customer (PAN/Aadhaar Write)

| Direction | Contract | Status |
|---|---|---|
| **Outbound (Billing)** | Updates `customer.pan`, `customer.aadharid` during bill save | ❗ No validation on PAN/Aadhaar format |
| **Inbound (Customer)** | Expects `customer.pan`/`aadharid` to be correct | ✅ Reads from DB |
| **Gap** | **Billing bypasses Customer module's validation — can write invalid PAN/Aadhaar directly to customer table** | |
| **Severity** | 🟡 MEDIUM — data quality issue |

### HO-009: Payment Delete → Account (Installment Count Drift)

| Direction | Contract | Status |
|---|---|---|
| **Outbound (Payment Delete)** | Removes payment record | ❌ Installment count NOT decremented |
| **Inbound (Account)** | `scheme_account.paid_installments` should match actual payment count | ❌ Stale count |
| **Gap** | **Delete payment without updating `paid_installments` = permanent installment count drift** | |
| **Severity** | 🔴 HIGH — financial integrity (VAL-038) |

### HO-010: Customer Sync → Scheme Account (No Transaction)

| Direction | Contract | Status |
|---|---|---|
| **Outbound (Customer Sync)** | `insExisAcByMobile()` creates scheme_account from `customer_reg` staging | ❌ No transaction wrapping |
| **Inbound (Scheme Account)** | Expects complete account with payment history | ⚠️ Partial sync possible |
| **Gap** | **Sync creates account but may fail to sync payments — account exists with wrong balance** | |
| **Severity** | 🟡 MEDIUM — data inconsistency on sync |

### HO-011: Payment Delete → Mode Details (Orphan Records)

| Direction | Contract | Status |
|---|---|---|
| **Outbound (Payment Delete)** | Deletes `payment` row | ❌ Hard delete, no child cleanup |
| **Inbound (payment_mode_details)** | FK to `payment.id_payment` | ❌ Orphaned rows remain |
| **Gap** | **Orphan `payment_mode_details` records make financial reconciliation impossible** | |
| **Severity** | 🔴 HIGH — financial data integrity | |

---

## Verified Handoffs (Contract Matches Both Sides)

| Handoff | Outbound (Provider) | Inbound (Consumer) | Status |
|---|---|---|---|
| Billing save → Estimation link | `estbillid` set | `is_estno_already_billed()` check | ✅ Both sides verified |
| Billing cancel → Tag restore | `tag_status` = 0 | Tag available again | ✅ Cleaned |
| Billing cancel → Estimation unlink | `estbillid` = NULL | Estimation re-billable | ✅ Cleaned |
| BT Download → Tag restore | `tag_status` = 0 + branch updated | Tagging sees correct branch | ✅ |
| BT Download → NT stock add | Weight added to destination | NT stock reflects transfer | ✅ Transactional |
| Customer Order → Tag Link/Unlink | `id_orderdetails` set/null | Bidirectional OTP-gated | ✅ Clean |
| Payment Save → Receipt Gen | `generate_receipt_no()` | Unique receipt number | ✅ |

---

## Module Coverage for Handoff Audit

| Module | FLOW_RISK_MATRIX? | Handoffs Audited? |
|---|---|---|
| Billing | ✅ | ✅ |
| Tagging | ✅ | ✅ |
| Estimation | ✅ | ✅ |
| Branch Transfer | ✅ | ✅ |
| Sales Transfer | ✅ | Inferred from CROSS_MODULE_MAP |
| Stock Issue | ❌ | Inferred only |
| Customer Order | ✅ | ✅ |
| Old Metal Process | ✅ | ✅ |
| Payment | ✅ | ✅ |
| Account | ✅ | ✅ |
| Chit Reports | ✅ | ✅ |
| LOT | ✅ | Inferred |
| Section Transfer | ✅ | Inferred |
| Customer | ✅ | ✅ |
| Retail Dashboard | ❌ | Inferred only |
| Purchase | ❌ | Inferred only |
| Settings | ❌ | N/A (config provider) |

---

## Priority Fix Order

| Priority | Handoff | Fix |
|---|---|---|
| **P0** | HO-004: BT Cancel reversal | Add tag_status, NT weight, bill flag, packaging reversal to cancel flow |
| **P0** | HO-003: Estimation trans_commit in error | Change to `trans_rollback()` in error branch |
| **P1** | HO-001: Tag status race condition | Add `SELECT ... FOR UPDATE` lock before tag_status write |
| **P1** | HO-005: POS cancel not reversed | Call `cancelTransactionRequest()` in `cancel_bill()` |
| **P2** | HO-002: Estimation JS-only calculation | Add server-side total recalculation |
| **P2** | HO-006: Tag delete orphan BT | Clean up BT records in delete case |
| **P2** | HO-008: Billing PAN/Aadhaar bypass | Add PAN/Aadhaar format validation in Billing |
| **P2** | HO-010: Customer sync no txn | Wrap sync in transaction |
| **P3** | HO-007: Old Metal lot dedup | Add guard against duplicate lot creation per process |
