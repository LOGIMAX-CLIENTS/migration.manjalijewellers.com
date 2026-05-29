## CR_PUR-CR03 — Restrict Stock Approval Type Based on GRN Type

| Field         | Value                         |
| ------------- | ----------------------------- |
| Severity      | P2                            |
| Track         | B (Business)                  |
| Category      | Logic                         |
| Sprint        | Sprint 1                      |
| Pattern Match | None                          |
| Module Brain  | ✅ Ready                      |
| Reporter      | User                          |
| Source        | Client (Change Request)       |

### Steps to Reproduce

1. Go to Supplier Bill Entry.
2. Select a GRN from the "Select GRN" dropdown.
3. Observe the "Approval Stock" field.

### Expected Behavior

- If the selected GRN Type = Bill, "Approval Stock" should default to "No".
- If the selected GRN Type = Receipt, "Approval Stock" should default to "Yes".
- The "Approval Stock" radio buttons should be non-editable (disabled).
- The system should automatically apply the correct value and it must be submitted to the server.

### Actual Behavior

- Currently, the user can manually change the "Approval Stock" type, and it doesn't automatically default based on the GRN type.

### Evidence

User Request: "In the Supplier Bill Entry, the Stock Approval Type should be automatically determined based on the selected GRN Type. The field must be non-editable..."

### Implementation Plan

1. **JS Change**: In `admin/assets/js/ret_purchase_order.js`, update the `#select_grn` change handler to set and disable `#approval_stock_yes` / `#approval_stock_no` based on `grn_type`.
2. **JS Change**: In the same file, update the `submit_pur_entry` click handler to temporarily enable the radio buttons before form serialization so the value is captured.
