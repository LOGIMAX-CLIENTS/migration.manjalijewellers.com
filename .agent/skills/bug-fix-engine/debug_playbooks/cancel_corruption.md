# Playbook #9: Data Corruption After Cancel

> **Symptom**: "Cancelled bill but tags still in transit", "Cancelled BT but stock wrong"

## ⚡ Binary Search Integration
- **Start Layer**: Skip binary search — go directly to `_cancel_master.md` checklist
- **Elimination**: List what SAVE writes vs what CANCEL reverses. The GAP between them is the bug. Every unreversed table is a bug.
- **Smell test**: "After cancel" → 95% cancel didn't reverse all table writes. This is the most predictable bug pattern — always use the SAVE-vs-CANCEL comparison technique.

## Collect
1. What was cancelled? (bill type, bill number)
2. What data is STILL wrong after cancel? (tag status, stock count, balance)
3. What SHOULD have been reversed?

## Trace (THE MOST CRITICAL PLAYBOOK)

### Step 1: What did SAVE write?
Read the save function → list ALL tables it touches:
```
For billing save:
- ret_billing (bill record)
- ret_bill_detail (line items)
- ret_bill_pay_device (payment breakdown)
- ret_taging (tag_status update)
- ret_non_taging (NT stock update)
- ret_journal (journal entries)
- ret_credit_history (if credit sale)
- customer (balance update)
```

### Step 2: What does CANCEL reverse?
Read `cancel_bill()` → list ALL tables it touches.

### Step 3: COMPARE — The Gap is the Bug
```
SAVE writes to:     CANCEL reverses:    GAP:
ret_billing      ✅  bill_status=2       —
ret_bill_detail  ✅  detail_status=2     —
ret_taging       ✅  tag_status restored  —
ret_non_taging   ✅  qty restored        ⚠️ WRONG DIRECTION for purchase
ret_journal      ❌  NOT TOUCHED         🔴 BUG: journal orphaned
ret_credit       ❌  NOT TOUCHED         🔴 BUG: credit status wrong
customer balance ❌  NOT TOUCHED         🔴 BUG: balance not reversed
```

### Step 4: Check using `_cancel_master.md`
Load `knowledge_brain/_SYSTEM/FLOW_CHECKLISTS/_cancel_master.md` — it has the full 20-step cancel trace with known gaps.

## Known Architecture Traps (eTail specific)
1. **ret_journal NEVER reversed** — ALL cancel types (PAT-CANCEL-001)
2. **Purchase cancel adds stock** — should subtract (PAT-STOCK-001)
3. **Loop variable bug** — `$other_inv` outside foreach, only deletes LAST item
4. **Sales return cancel double-adds** — return adds stock, cancel adds again
5. **Credit cancel hardcodes** — sets `credit_status=2` ignoring partial collections

## Output
→ Which reversal steps are MISSING
→ Which tables still have stale/wrong data after cancel
→ Exact line in cancel_bill() where reversal should be added
