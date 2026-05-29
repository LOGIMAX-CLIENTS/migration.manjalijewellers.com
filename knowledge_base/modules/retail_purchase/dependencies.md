# Module Dependencies: Retail Purchase

## Internal Dependencies (Key Models)

1.  **`ret_karigar` (Vendor Master)**

    - **Critical**: All purchase orders and stock issues are linked to a Karigar ID.
    - **Risk**: Inconsistent data in `ret_karigar` (e.g., missing `karigar_for` type) can break PO generation.

2.  **`ret_taging` (Stock Master)**

    - **Critical**: The entire "Tagged Stock" workflow relies on this table.
    - **Risk**: Manual updates or direct SQL injection into this table without updating `ret_taging_status_log` will cause history mismatches.

3.  **`ret_product_master` & Category**
    - **Critical**: Required for defining items in a PO or Stock Issue.
    - **Dependencies**: Relies on `ret_catalog` module being up-to-date.

## Shared System Components

1.  **`admin_settings_model`**:

    - **Purpose**: Used for Branch settings, Financial Year calculations, and Access Control.
    - **Impact**: Incorrect settings here can block "Day Closing" or "Year End" processing.

2.  **`log_model`**:
    - **Purpose**: Used extensively for Audit Trails (Insert/Update logs).
    - **Requirement**: Almost every transactional action (Save PO, Approve Stock) triggers a log entry.

## External Interfaces

- **Barcode/Tag Generation**: The system likely interfaces with a barcode printer (client-side or server-side logic in `ret_taging`).
- **Financial Accounting**: The "Cash Abstract" report implies a tight coupling with the internal ledger system.
