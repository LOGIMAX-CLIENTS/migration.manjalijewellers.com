# PUR-CR01 — Supplier Ledger Balance Nature During Approval-to-Invoice Conversion

## Feature Request (New)

| Field | Value |
|---|---|
| **Type** | 🆕 Feature Request / Change Request |
| **Severity** | P1 — Major |
| **Track** | B (Business Logic) |
| **Category** | Logic |
| **Sprint** | Sprint 2 |
| **Pattern Match** | None — Novel requirement |
| **Module Brain** | ✅ Ready |
| **Reporter** | Coswan Silvers (Client) |
| **Source** | Client (CLT) |
| **Reference ID** | bug-124744000005483093 |

---

## Problem Statement

During approval-to-invoice conversion (`supplier_rate_cut()` save flow), the system **does not validate or consider the existing balance nature (DR/CR)** of the supplier's approval ledger or main ledger. All ledger entries are defaulted to DR nature regardless of the actual existing balance.

### Current Behavior
- `updateWalletData()` (Model L1629-1635) uses raw SQL: `amount=(amount +/- value)` — purely arithmetic, no DR/CR nature awareness
- `supplier_rate_cut()` save flow (Controller L11475-11682) inserts conversion record and payment without checking ledger balance nature
- No `balance_nature` or `blc_nature` field exists in the codebase at all
- The Supplier Approval Ledger and Supplier Main Ledger always default to DR nature

### Required Behavior (Acceptance Criteria)
1. **Same Nature Addition**: DR+DR or CR+CR → Add balances
2. **Different Nature Subtraction**: DR-CR or CR-DR → Subtract balances  
3. **All Transitions Supported**: DR→DR, CR→CR, DR→CR, CR→DR
4. **Negative Balance Auto-Switch**: If subtraction results in negative balance → auto-switch nature, store absolute value
5. **No DR Default**: No ledger entry should default to DR unless explicitly configured
6. **Dynamic Recalculation**: Amount-to-weight conversion must recalculate dynamically based on updated balance

---

## Technical Analysis

### Affected Components

| Component | File | Lines | Impact |
|---|---|---|---|
| Wallet Update Model | `ret_purchase_order_model.php` | L1629-1635 | Core — needs DR/CR nature-aware logic |
| Supplier Rate Cut Controller | `admin_ret_purchase.php` | L11438-11796 | Must pass balance nature context |
| Supplier Ledger Report | `supplier_approval_transaction` view | — | Must display correct DR/CR nature |
| Supplier Main Ledger Report | `suppliertransaction` view | — | Must display correct DR/CR nature |

### Database Impact
- `ret_karigar_wallet` table likely needs a `balance_nature` column (VARCHAR(2) DEFAULT 'DR')
- `ret_supplier_rate_cut` table may need `entry_nature` column to track the nature of each conversion entry

### Cross-Module Impact
- `ret_wallet_account` / `ret_karigar_wallet` table is shared (read/written by Purchase, Billing, and Accounts modules)
- Any change to wallet update logic must be validated against ALL callers of `updateWalletData()`

---

## Evidence (Screenshots)
1. Supplier Approval Ledger showing 0.12 Cr outstanding with DR balance entries
2. Supplier Main Ledger showing 0 Cr, 0 outstanding — conversion lost nature context
3. Supplier Approval Ledger showing 895.800 Dr outstanding — forced DR default
4. Purchase receipt (job_receipt) showing the conversion entry details
5. Supplier Main Ledger showing 40439.00 Dr opening — correct DR but no nature validation on subsequent entries

---

## Recommendation
This is a **new feature request**, not a bug fix. The system was architecturally never designed to handle DR/CR balance nature transitions. Implementation requires:
1. Schema changes (new columns)
2. Model layer changes (nature-aware arithmetic)
3. Controller changes (pass nature context during conversion)
4. View/report changes (display correct nature)
5. JS changes (dynamic recalculation based on nature)
