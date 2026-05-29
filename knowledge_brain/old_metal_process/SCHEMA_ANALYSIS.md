# Schema Analysis — Old Metal Process Module

**Database**: `nsk`  
**Date**: 2026-03-14  

---

## Key Tables — Field-Level Analysis

### `ret_old_metal_pocket`
| Key Finding | Detail |
|---|---|
| `pocket_no` | VARCHAR — **no UNIQUE constraint** → OMP-005 variant applies to pockets too |
| `status` | int(11), MUL index, DEFAULT 0 → status column is indexed but never set to 1 (OMP-007 confirmed) |
| `issue_gwt/nwt/diawt/pcs/purity` | All numeric, DEFAULT 0 → pocket issue totals tracked at row level |
| `is_against_opening` | tinyint — confirmed field name matches controller code at line 221 |
| `trans_type` | tinyint(1) → 3 values (1=Old Metal, 2=Tagged, 3=Non-Tagged) — NO CHECK constraint |

### `ret_old_metal_process`
| Key Finding | Detail |
|---|---|
| `process_no` | VARCHAR(20) **NULLABLE, NO UNIQUE constraint** → OMP-005 FULLY UNPROTECTED at DB level |
| `id_metal_process` | int(11), NULLABLE — process type (1=Melting, 2=Testing, 3=Refining, 4=Polishing) |
| `process_for` | int(11), MUL index → 1=Issue, 2=Receipt |
| `melting_status` / `status` | tinyint(1) — NO CHECK constraint, transitions fully code-controlled |
| **`outistransfered`** | int(11) — **TALLY SYNC FLAG** (undocumented integration) |
| **`outtally_guid`** | varchar(100) — Tally export GUID |
| **`outtally_updated_on`** | datetime — Tally sync timestamp |
| **`inistransfered`** | int(11) — Tally inward sync flag |
| **`intally_guid`** | varchar(100) — Tally inward GUID |
| **`intally_updated_on`** | datetime — Tally inward sync timestamp |
| `from_process / to_process` | int(11) — chain navigation fields, never set by controller |
| `next_process_for` | int(11) NOT NULL, DEFAULT NULL — contradictory schema (NOT NULL with DEFAULT NULL) |

### `ret_old_metal_melting`
| Key Finding | Detail |
|---|---|
| `melting_status` | tinyint(1), NO CHECK → values 0=Pending, 1=Receipt Done, 2=Testing Issued, 3=Testing Complete, 4=Refining Issued, 5/6=Stock |
| `received_purity` | int(11) — purity stored as INT (not decimal!) — data granularity issue |
| `receipt_charges` | decimal(10,2), DEFAULT 0.00 — charges at melting level |
| `received_wt / received_less_wt` | decimal(10,3), DEFAULT 0.000 — both at melting row level |
| `id_old_metal_process_receipt` | int(11) — FK to ret_old_metal_process for the receipt transaction |

### `ret_old_metal_process_payment`
| Key Finding | Detail |
|---|---|
| `type` | Always 1 in controller (hardcoded) — field purpose unclear |
| `payment_mode` | varchar — 'Cash' or 'NB' stored as string, not enum |
| `payment_ref_number` | varchar — only populated for NB, cash ref field (@line 608 in view) has no `name` attribute! |
| `payment_amount` | decimal — OMP-003: NB records `cash_amount` value |

---

## OMP-019 Precise Location (View Confirmed)

```html
<!-- Line 347 — TAB LINK for payment is commented out -->
<!--<li id="tab_payment_details"><a href="#payment_details" data-toggle="tab">Payment Details</a></li>-->

<!-- Lines 590–630 — TAB PANEL containing CASH+NB input form is commented out -->
<!-- <div class="tab-pane" id="payment_details"> ... -->
<!-- <table id="receipt_payment_details"> ... -->
<!--   <input name="receipt_payment[cash_amount]"> -->
<!--   <input name="receipt_payment[net_banking_amount]"> -->
<!-- </div> -->
```
**Fix**: Uncomment lines 347 and 590–630 in `form.php`.  
Because `receipt_payment` fields won't be in POST when tab is hidden, the controller's `if(!empty($receipt_payment))` block (line 1474) will be skipped → no payments are ever saved even though the controller has working save code.

---

## OMP-022 Precise Location (View Confirmed)

All three modal tables use `id="category_row"` at:
- Line **682**: `#category_modal` (Melting receipt weight modal)
- Line **724**: `#refining_category_modal` (Refining weight modal)  
- Line **767**: `#polishing_category_modal` (Polishing weight modal)

---

## Tally Integration — Newly Discovered (GAP Resolution)

`ret_old_metal_process` has 6 Tally sync columns. This means:
- Metal process records can be exported to Tally accounting software
- The sync status is tracked bidirectionally (in/out)
- **No sync controller method found in this module** — sync is likely handled by a shared Tally sync service
- Bug risk: if `outistransfered=0` and a transaction has data errors (e.g., OMP-028 NULL `created_branch`), the Tally sync may fail silently or export wrong entries

---

## Schema Concerns

| Concern | Severity | Detail |
|---|---|---|
| `process_no` no UNIQUE | 🔴 Critical | Duplicate process numbers possible under load (OMP-005) |
| `next_process_for` NOT NULL + DEFAULT NULL | 🟡 Medium | Contradictory schema — DB may reject inserts in strict mode |
| `received_purity` as INT | 🟡 Medium | Purity stored as integer — sub-1% precision lost (e.g., 91.6% stored as 91) |
| `melting_status` no CHECK | 🟡 Medium | Invalid status values (e.g., 99) could be saved without error |
| Tally sync columns on all process records | 🟢 Note | Undocumented integration — ensure data fixes don't break Tally sync |
