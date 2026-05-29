# Ret_Reports Fix Report

---
### Fix: RPT-CLT01 — Employee Filter Not Working in Cash Abstract Report
- **Date:** 2026-03-26
- **Track:** A (System)
- **Category:** Logic / JavaScript
- **Severity:** P1
- **Files Changed:** `admin/assets/js/ret_reports.js`
- **Root Cause:** Missing `#branch_select` change handler in the `cash_abstract` init case — `get_employee("cash_abs")` was called on page load only, with no mechanism to reload employees after branch selection changed. For HO users (`id_branch == 0`), the initial call was blocked by `if (id_branch > 0)` guard.
- **Fix Applied:** Added `#branch_select` change handler (L500–508) to reload employees, floors, and counters on branch change. Follows same pattern as `old_metal_purchase` case (L320–324).
- **Tests:** Manual browser verification — employee dropdown populates on branch change, employee filter correctly filters Cash/Card/UPI amounts.
- **Pattern:** New — PAT-JS-006 (Missing Dependent Dropdown Reload on Parent Change)
- **Rollback:** Remove the `#branch_select` change handler block from the cash_abstract case in `ret_reports.js`.
- **Task Link:** https://connect.zoho.in/portal/logimax-clients/task/124744000005941787
---

---
### Fix: RPT-CLT05 — Advance Report Customer Search Not Finding Sender
- **Date:** 2026-04-04
- **Track:** A (System)
- **Category:** Query Logic
- **Severity:** P1
- **Files Changed:** `admin/application/models/ret_reports_model.php`
- **Root Cause:** `adv_rcvd` subquery had two columns named `id_customer` (from `rcv_ir` and `snd_cus`) — MySQL couldn't distinguish them. Outer WHERE used two separate AND conditions (`AND cus.id_customer = X AND adv_rcvd.id_customer = X`) which was contradictory — can't be both holder AND sender.
- **Fix Applied:** Aliased `snd_cus.id_customer AS snd_customer_id` in subquery; combined filter as `AND (cus.id_customer = X OR adv_rcvd.snd_customer_id = X)`.
- **Tests:** Manual browser verification — search by customer mobile now shows both customer's own advance row and rows where customer is listed as "Advance Received From".
- **Pattern:** New — PAT-QRY-007 (Ambiguous Column Name in Derived Table)
- **Rollback:** Revert alias and WHERE change in `customerAdvanceReport()` L11010, L11026.
---
