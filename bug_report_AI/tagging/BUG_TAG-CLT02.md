## TAG-CLT02 — Duplicate Tag Form Does Not Show Closed Lot Numbers

| Field         | Value                                                    |
| ------------- | -------------------------------------------------------- |
| Severity      | P2 (Minor)                                               |
| Track         | A (System/Architecture)                                  |
| Category      | Logic                                                    |
| Sprint        | Sprint 2                                                 |
| Pattern Match | None (Novel — closest: PAT-QRY-003 Missing WHERE Scope)  |
| Module Brain  | ✅ Ready                                                 |
| Reporter      | Client                                                   |
| Source        | Client (CLT)                                             |
| Danger Zone   | None                                                     |
| Date Filed    | 2026-03-24                                               |

### Description

In the Duplicate Tag form, the Lot Number filter only displays **open** lots. Once a lot is closed, its lot number disappears from the dropdown, preventing users from printing duplicate tags for items belonging to that closed lot.

The requirement is to allow selection of closed lot numbers so that duplicate tags can still be printed based on the lot number filter.

### Steps to Reproduce

1. Navigate to the Tagging module → Duplicate Tag form
2. Close a lot that has items tagged under it
3. Open the Duplicate Tag form again
4. Observe the Lot Number dropdown/filter

### Expected Behavior

- The Lot Number filter should display **both open and closed** lots
- Users should be able to select a closed lot number
- Duplicate tags should be generated/printed based on the selected lot number

### Actual Behavior

- Closed lot numbers are **not displayed** in the Lot Number filter
- Users cannot print duplicate tags for items under closed lots

### Evidence

Reported by client — no screenshot provided. Reproduced by inspecting lot filter query logic.

### Root Cause (Preliminary)

The query populating the Lot Number dropdown in the Duplicate Tag form likely filters by lot status (e.g., `lot_status = 'open'` or similar), excluding closed lots. The fix should remove or relax this status filter to include all lots (or at least both open and closed lots).

### Files to Investigate

- **Model**: `ret_taging_model.php` — Look for the method populating the lot number dropdown in the Duplicate Tag form
- **Controller**: `admin_ret_taging.php` — Check how lot data is passed to the view
- **View**: `views/ret_taging/` — Check Duplicate Tag form view for any client-side filtering
