## BIL-CLT03 — Wastage weight edited to zero in bill form not updated in print copy and sales reports

| Field | Value |
|---|---|
| **Severity** | P1 — Major |
| **Track** | B (Business Logic) |
| **Category** | Integration — Data flow discrepancy between form and downstream output |
| **Sprint** | Sprint 1 |
| **Pattern Match** | None (Novel bug related to 0-value handling) |
| **Module Brain** | ✅ Ready (`knowledge_brain/billing/MODULE_BRAIN.md`) |
| **Reporter** | Client (via Support) |
| **Source** | Client (CLT) |
| **Date Reported** | 2026-03-09 |

---

### Bug Summary

When a user calls a tag in the bill form and manually edits the **wastage weight** of that tag to **zero** (0), the bill form correctly reflects this change. However, after saving the bill:
1. The **Bill Print Copy** continues to display the original tag weight (wastage) instead of 0.
2. **Sales Related Reports** also display the original wastage weight instead of 0.

---

### Steps to Reproduce

1. Open the **Billing** module.
2. Load/call a tag into the bill.
3. Locate the **Wastage Weight** field for that tag and change the value to `0`.
4. Save the bill.
5. Generate the **Bill Print Copy**.
6. Open **Sales Reports** and check the wastage weight for this bill.

---

### Expected Behavior

The Bill Print Copy and Sales Reports should reflect the updated wastage weight (`0`) as entered and saved in the bill form.

---

### Actual Behavior

The Bill Print Copy and Sales Reports display the **old wastage weight** (original weight of the tag) instead of the updated `0`.

---

### Root Cause Analysis (Hypothesis)

1. **Zero Handling in DB Update**: The save logic might be using `isset()` or `empty()` which treats `0` as "not set", leading to the update being skipped and the old value persisting in the DB for certain columns used by print/reports.
2. **Independent Table Updates**: The bill form might be updating the main billing table, but the table used for items (which print/reports fetch from) might be re-fetching from the original tag data instead of the form's modified data.
3. **Report/Print Query Logic**: The queries for print and reports might be JOINing with the original tagging table instead of using the weight saved in the billing items table.

---

### Files to Investigate

| File | Concern |
|---|---|
| `admin/application/controllers/admin_ret_billing.php` | Save/Update case handling for bill items and wastage. |
| `admin/application/models/ret_billing_model.php` | Methods responsible for saving/updating bill items weights. |
| `admin/assets/js/ret_billing.js` | How the wastage weight is captured and sent to the server. |
| `admin/application/controllers/admin_ret_reports.php` | Queries for sales reports (checking if they JOIN with original tags). |

---

### Acknowledgement SLA

**Source**: Client → SLA ≤ 2 hours  
**Status**: ✅ Triaged — Sprint 1  
**Message**: "Bug BIL-CLT03 received. Severity: P1. Target: ≤ 3 days (Sprint 1)."
