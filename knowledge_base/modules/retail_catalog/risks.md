# Retail Catalog Risks & Architecture

## Architectural Observations

- **MVC Pattern**: Standard CodeIgniter 3 MVC structure.
- **AJAX-Heavy**: Many list views likely use DataTables with AJAX sources (seen `ajax_active_subCtg`, `active_masters` in controller).
- **Direct Querying**: The Model (`ret_catalog_model`) uses `this->db->query()` with raw SQL often, instead of Active Record for reads. This makes schema refactoring harder.

## Identified Risks

### Data Integrity

- **Schema Duality**: There appears to be a `product` table (E-com?) and `ret_product_master` (Retail?). The controller methods mix usage:
  - `getActiveProducts` uses `ret_product_master`.
  - `get_product` uses `product`.
  - **Risk**: Potential data inconsistency if both tables are meant to represent the same entity type but are managed separately.
- **Deletion Logic**: While `delete_purity` checks for usage, it's not clear if _all_ master data deletions have strict referential integrity checks (e.g., using Foreign Keys or manual checks).

### Security

- **Hardcoded IDs**: Potential presence of hardcoded User IDs for specific features (observed in View headers previously, check controller for similar patterns).
- **Raw SQL**: Usage of raw SQL in models increases SQL Injection risk if input sanitization is missed (though CI3 usually handles binding if used correctly, raw concat is dangerous).

### Maintenance

- **Mega-Controller**: `admin_ret_catalog` is very large (20k+ lines implied?), handling too many distinct entities (Purity, Color, Floor, Product, etc.). This violates Single Responsibility Principle.
- **Date Logic**: Date filtering logic is repeated in every `ajax_get*` function in the model.
