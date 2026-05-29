# Architecture: Retail Purchase

## Data Flow

1.  **Input (Masters)**:
    - `ret_karigar` (Vendors)
    - `ret_supp_catalogue` (Items)
2.  **Transaction (Entries)**:
    - **PO Entry**: Creates `ret_purchase_order` (Status: Pending/Suspense).
    - **Inward/GRN**: Populates `ret_taging` (Status: 0/Suspense).
    - **Approval**: Promotes `ret_taging` to Status 1 (Active Stock).
3.  **Movement (Issue/Receipt)**:
    - **Issue**: Moves Stock -> Karigar (Status 7).
    - **Receipt**: Moves Karigar -> Stock (Status 1).
4.  **Reporting**:
    - Aggregates data from `ret_taging` (Current Stock) and `ret_billing`/`ret_stock_issue` (History).

## Key Components

- **Controller Layer**: `admin_ret_purchase` (Entry), `admin_ret_stock_issue` (Movement), `admin_ret_reports` (Analysis).
- **Service/Model Layer**: `ret_purchase_order_model` (Business Logic), `ret_reports_model` (Data Aggregation).
- **Presentation**: CodeIgniter Views + jQuery/AJAX for dynamic forms.

## Database Schema Highlights

- **`ret_taging`**: The central "Atom" of inventory. Every physical piece of jewelry has a row here.
- **`ret_nontag_item`**: The 'Dark Matter' of inventory. Bulk undefined weight.
- **`ret_lot_inwards`**: Tracks the source of material (Lot) before it becomes a Tag.
