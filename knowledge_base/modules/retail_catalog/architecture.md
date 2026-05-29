# Retail Catalog Architecture

## Design Pattern

- **Framework**: CodeIgniter 3 (HMVC structure not strictly followed, seems standard MVC).
- **Controller Strategy**: Monolithic controller `admin_ret_catalog.php` acting as a "God Class" for all Master Data.
- **View Strategy**: `layout/template` wrapper with injected `main_content`. Views are organized in `master/` subdirectory.

## Data Flow

1.  **Request**: User accesses `admin_ret_catalog/method`.
2.  **Permission**: Controller checks `is_logged` and `access_time`.
3.  **Model**: Calls `ret_catalog_model` for CRUD.
    - **Reads**: Often Raw SQL.
    - **Writes**: `insertData`, `updateData` helper methods in Model (generic wrappers).
4.  **Logging**: `log_model` records the operation.
5.  **View**: JSON response (for AJAX) or View load (for pages).

## Key Components

- **Generic Persistence**: The model uses generic `insertData($data, $table)` and `updateData` methods, which is a good DRY pattern but reduces type safety.
- **Session Handing**: Heavy reliance on `$this->session->userdata` for branching logic and access control.
