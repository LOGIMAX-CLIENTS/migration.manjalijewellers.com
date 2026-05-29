## PUR-CR02 — Improve Image Upload UX in Purchase Order (Stock Order)

| Field | Value |
|---|---|
| Type | Change Request (UX Enhancement) |
| Module | Purchase |
| Sub-Module | Purchase Order → Stock Order → Image Upload |
| Priority | Low |
| Sprint | Next Sprint |
| Related Bug | PUR-CLT02 (image upload fix) |
| Reporter | Client / Internal |
| GitHub | #1024 |

### Current Behavior
- User clicks "Add Image" → modal opens → selects files → clicks Save → modal closes
- Images are stored in a hidden field and rendered in the table row after "Add Item"
- Once added to the table, users can only preview images (eye icon) via `#imageModal_bulk_edit` but cannot easily modify/replace them
- No visual feedback showing how many images are attached before "Add Item"

### Proposed Enhancement

#### 1. Preview & Modify Before Adding Item
- After selecting images in `#imageModal_new`, show thumbnail previews with individual delete buttons
- Allow adding more images without replacing existing selection
- Show image count badge near the "Add Image" button (e.g., "Add Image (3)")

#### 2. Edit Images on Existing Table Rows
- Wire up the `#imageModal` (order_form.php line 853) with `#pur_order_images` file input and `#update_pur_img` save button for updating images on already-added rows
- Allow adding new images to an existing row without losing previously added images
- Allow deleting individual images from an existing row

#### 3. Visual Feedback
- Show image count in the Image column of the item table (e.g., "3 images" with thumbnail)
- Add a "Replace" or "Edit" button alongside the existing eye icon in each row
- Show a loading spinner during image compression

### Files to Modify
| File | Changes |
|---|---|
| `admin/application/views/ret_purchase/order_form.php` | Add image count badge, wire up edit modal |
| `admin/assets/js/ret_purchase_order.js` | Add image edit handlers for table rows, image count display |
| `admin/assets/js/ret_general.js` | Enhance `validateOrderImages()` to support appending images |

### Acceptance Criteria
- [ ] User can preview all selected images before clicking "Add Item"
- [ ] User can delete individual images from the preview
- [ ] User can add more images after initial selection
- [ ] User can edit/replace images on existing table rows
- [ ] Image count is visible on the "Add Image" button
- [ ] All flows work on both Chrome and Firefox
## PUR-CR02 — Amount to Metal Conversion Options for HM, MC & Stone Charges

| Field | Value |
|---|---|
| Severity | P2 |
| Track | B (Business) |
| Category | Logic |
| Sprint | Sprint 3 (Backlog) |
| Pattern Match | None (Novel Feature) |
| Module Brain | ✅ Ready |
| Reporter | Internal |
| Source | Internal (Change Request) |
| Type | Change Request (New Feature) |

### User Story

Introduce functionality in the Purchase module's existing Amount/Weight Conversion sub-module (`amt_weight_conversation`) to convert **amount values into equivalent metal weight** for:
1. **HM (Hallmarking) charges** — Amount paid for hallmarking → equivalent pure metal weight
2. **MC (Making Charges)** — Amount paid as making charges → equivalent pure metal weight
3. **Stone charges** — Amount paid for stones → equivalent pure metal weight

### Current State

The existing `amt_weight_conversation/form.php` view supports two conversion modes:
- **Amount to Weight** (`rate_cut_type=1`): Enter an amount → system calculates equivalent pure weight at the given rate
- **Pure to Amount** (`rate_cut_type=2`): Enter pure weight → system calculates the amount at the given rate

These conversions currently operate on the **base metal value** only. There is no facility to convert HM charges, Making Charges, or Stone charges from amount to metal weight.

### Expected Behavior (New Feature)

After this CR is implemented:
1. User should be able to select which charge type to convert: **HM Charges**, **Making Charges**, or **Stone Charges**
2. For each selected charge type, user enters:
   - The amount value of the charge
   - The current metal rate (excl. GST)
3. System calculates: `equivalent_metal_weight = charge_amount / metal_rate`
4. The converted weight is recorded against the supplier's wallet/ledger
5. GST calculations (CGST/SGST/IGST) should apply based on the supplier's state (same-state vs inter-state)
6. The conversion transaction should update the supplier's pure weight balance and amount balance accordingly

### Business Rules Affected

- **RULE-PUR-017** (Metal Value Calculation): Extended to support reverse calculation (amount → weight) for charge types
- **RULE-PUR-018** (Payment Modes): Metal adjustment mode requires weight-to-value conversion — this CR adds charge-to-weight conversion
- **RULE-PUR-003** (Rate Type): Rate type logic may need extension for charge-based conversions

### Impacted Files (Preliminary)

| File | Change Type |
|---|---|
| `admin/application/views/ret_purchase/amt_weight_conversation/form.php` | MODIFY — Add charge type selector (HM/MC/Stone) |
| `admin/application/controllers/admin_ret_purchase.php` | MODIFY — Add controller methods for charge conversion |
| `admin/application/models/ret_purchase_order_model.php` | MODIFY — Add model methods for charge-to-weight calculation and DB save |
| `admin/assets/js/ret_purchase.js` (or inline JS in view) | MODIFY — Add JS calculation logic for charge-to-weight |
| Database | MODIFY — May need new columns or table for charge conversion records |

### Steps to Implement (High Level)

1. Add radio/dropdown in `form.php` for charge type: Base Metal (existing), HM Charges, Making Charges, Stone Charges
2. Based on charge type selected, load the relevant charge amount from the PO/bill
3. Apply the conversion formula: `weight = amount / rate`
4. Save the conversion record with charge type identifier
5. Update supplier wallet balances (pure weight + amount)
6. Apply appropriate GST based on supplier state

### Evidence

Existing view file: `admin/application/views/ret_purchase/amt_weight_conversation/form.php` — partially built UI for weight/amount conversion but lacks HM/MC/Stone charge options.

### Dependencies

- Supplier wallet (`ret_karigar_wallet`, `ret_karigar_wallet_transcation`) — must support new transaction types
- HM charges data from `ret_halmarking_issue_details`
- Making charges from `ret_purchase_order_item.making_charge`
- Stone charges from `ret_purchase_order_item.stone_charge`
