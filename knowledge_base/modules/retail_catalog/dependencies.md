# Retail Catalog Dependencies

## Internal Models

- **ret_catalog_model**: Core data access.

## External Models

- **admin_settings_model**:
  - Used for `get_access` (Permission checks).
  - Used for `metal_ratesDB` (Metal rate retrieval).
  - Used for `get_company` (Global settings).
- **sms_model**: Loaded in constructor, likely for notifications (though not explicitly seen in the analyzed `admin_ret_catalog` chunks).
- **log_model**: Used for audit logging (`log_detail`) on CRUD operations.

## Database Dependencies

- **Company Settings**: Relies on company details and global configurations (Metal Rates).
- **User/Employee**: Relies on `profile` and `employee` tables for access control and `created_by`/`updated_by` tracking.

## Shared Views

- `layout/header`, `layout/footer`: Standard admin frame.
- `layout/template`: Main wrapper view.
