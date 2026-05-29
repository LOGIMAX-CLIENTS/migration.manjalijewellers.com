## RPT-CLT03 — Credit Issued Report: Received Amount Missing Discount & Issued Balance Wrong

| Field | Value |
|---|---|
| Severity | P0 |
| Track | A (System) |
| Category | Logic |
| Sprint | Sprint 1 |
| Pattern Match | PAT-LOGIC-002 (Return-Omission in Debt Calculation) — similar formula bug |
| Module Brain | ❌ Not built (Reports) |
| Reporter | Internal / Developer |
| Source | Internal |

### Steps to Reproduce

**Bug A — Received Amount Missing Discount:**
1. Navigate to **Reports > Credit Issued** (`admin_ret_reports/credit_issued/list`)
2. Set Report Type = **Received**
3. Set a wide date range (e.g., 13/03/2025 – 13/03/2026)
4. Click Search
5. Search for a specific bill (e.g., CSS25CC-00173)
6. Observe the **Received Amount** — it does not include the discount amount

**Bug B — Issued Balance Amount Wrong:**
1. Navigate to **Reports > Credit Issued** (`admin_ret_reports/credit_issued/list`)
2. Set Report Type = **Issued**
3. Set date range (e.g., 13/03/2026 – 13/03/2026)
4. Click Search
5. Observe the **Balance Amount** column — shows 0.00 for all rows

### Expected Behavior
- **Received**: Received Amount = `tot_amt_received + old_metal_amount + credit_disc_amt`
- **Issued**: Balance Amount = `tot_bill_amount - tot_amt_received - credit_ret_amt`

### Actual Behavior
- **Received**: Received Amount = `tot_amt_received + old_metal_amount` (discount excluded)
- **Issued**: Balance Amount = `tot_amt_received - credit_ret_amt` (wrong formula — shows near-zero)

### Root Cause
In `ret_reports_model.php` → `getcreditBill()`:
- **Bug A (line 2093–2110)**: The `sql1` query for report_type=2 didn't SELECT `credit_disc_amt`, and the PHP sum at line 2110 only added `old_metal_amount` but not `credit_disc_amt`.
- **Bug B (line 2056)**: The `due_amt` formula was `(b.tot_amt_received - ret.credit_ret_amt)` instead of `(b.tot_bill_amount - b.tot_amt_received - ret.credit_ret_amt)`.

### Evidence
Screenshots attached showing incorrect Balance Amount (0.00) for Issued and missing discount in Received Amount.
