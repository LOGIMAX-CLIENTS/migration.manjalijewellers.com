# Print Template System Documentation

## Overview
The Print Template System is a dynamic, GrapesJS-powered designer that allows users to create and customize billing receipts and other print documents. It replaces static PHP views with dynamic HTML/CSS templates that support placeholder-based data injection.

---

## Technical Components

### 1. Backend Management
- **Controller:** `admin/application/controllers/Admin_print_template.php`
  - Handles template CRUD operations.
  - Serves custom block templates for GrapesJS.
  - Manages placeholder lists via `get_fields($category)`.
- **Model:** `admin/application/models/Print_template_model.php`
  - Manages the `print_templates` and `print_template_placeholders` tables.
  - **Core Logic:** `render()` and `render_string_template()` methods handle the transformation of stored HTML into final print-ready content.

### 2. Frontend Designer (GrapesJS)
- **Initialization:** `admin/assets/js/print-designer/designer-init.js`
  - Configures the GrapesJS editor.
  - Implements remote storage logic (saving/loading designs).
- **Custom Blocks:** `admin/assets/js/print-designer/custom-blocks.js`
  - Defines reusable components like `Sales Table`, `Customer Info`, and `Company Header`.
  - Uses `{{placeholder}}` syntax for dynamic data.

### 3. Data Mapping Layer
- **Helper:** `admin/application/helpers/receipt_helper.php`
  - Function: `map_billing_data_to_template($data)`
  - Responsibility: Flattens complex database objects (billing, company, items) into a flat associative array where keys match the designer's placeholders.
  - **Dynamic Loops:** List data (like items or payment breakdowns) are mapped as nested arrays for use in loop sections.

---

## The Design & Print Process

### A. Designing a Template
1. The user opens the **Print Designer** (via `Admin_print_template.php`).
2. GrapesJS loads available **Placeholders** and **Blocks**.
3. The user drags blocks and uses placeholders (e.g., `{{customer_name}}`).
4. Upon saving, the backend stores:
   - `template_html`: The raw HTML structure.
   - `template_css`: The associated styles.
   - `gjs_data`: The JSON structure for future editing.

### B. Printing / Generating Receipt
When a print request is made (e.g., in `admin_ret_billing.php`):
1. **Fetch Data:** Raw billing data is retrieved using `get_receipt_data`.
2. **Map Data:** `map_billing_data_to_template` transforms this into a flat `$mapped_data` array.
3. **Render:** `Print_template_model->render()` is called.
   - It finds the **Active/Default** template for the bill category.
   - It performs **Mustache-style replacement**:
     - **Loops:** Sections wrapped in `{{#items}}...{{/items}}` are repeated for each entry in the items array.
     - **Values:** Simple placeholders like `{{invoice_no}}` are replaced by their mapped values.
4. **Output:** The final HTML (merged with CSS) is echoed to the browser or sent to the PDF engine.
5. **Fallback:** If no dynamic template is active, the system falls back to the legacy static view (`billing/print/receipt_billing.php`).

---

## Database Schema

### `print_templates`
| Column | Description |
| :--- | :--- |
| `id_template` | Primary Key |
| `template_name` | User-friendly name |
| `template_category` | Bill Type ID (1-15) |
| `template_html` | Rendered HTML from GrapesJS |
| `template_css` | Rendered CSS from GrapesJS |
| `gjs_data` | JSON structure for the designer |
| `is_default` | Whether this is the active template for the category |
| `is_active` | Toggle for template availability |

### `print_template_placeholders`
| Column | Description |
| :--- | :--- |
| `placeholder_key` | The actual key used in templates (e.g., `invoice_no`) |
| `placeholder_label` | Human-readable label in the designer sidebar |
| `category` | Which bill categories this placeholder belongs to |
| `placeholder_group`| Grouping for sidebar organization (e.g., "Customer") |

---

## Placeholder Syntax
- **Simple Value:** `{{key_name}}`
- **Loop Section:** 
  ```html
  {{#items}}
    <tr>
      <td>{{sno}}</td>
      <td>{{description}}</td>
    </tr>
  {{/items}}
  ```
