## PUR-CLT02 — Unable to Add Image in Purchase Order (Stock Order)

| Field | Value |
|---|---|
| Severity | P2 |
| Track | A (System) |
| Category | Integration |
| Sprint | Sprint 3 |
| Pattern Match | None (Novel) |
| Module Brain | ✅ Ready |
| Reporter | Client |
| Source | Client |
| **Status** | **✅ Fixed — 2026-02-25** |
| **GitHub** | **#1023** |

### Steps to Reproduce
1. Navigate to Purchase Order → Add (`admin_ret_purchase/purchase/add`)
2. Select "Stock Order" radio button
3. Fill required fields (Karigar, Due Date, Product, Design, etc.)
4. Click the "Add Image" button (top-right corner)
5. In the `#imageModal_new` modal, select one or more image files
6. Wait for image compression/preview
7. Click "Save" in the modal
8. Click "Add Item" to add the item row to the table

### Expected Behavior
- After selecting images in the modal, previews should appear in the `#order_images` div inside the modal
- After clicking "Save", the selected images should be stored in the `#order_iamges` hidden field
- After clicking "Add Item", the item row in `#item_detail` table should display the image thumbnail in the Image column
- On form save, images should be uploaded and stored on the server

### Actual Behavior
- Image preview may not render inside the modal after file selection
- After adding the item to the table, the image column either shows the "no_image.png" placeholder or nothing
- Images are not successfully persisted

### Evidence
- Screenshot provided showing the Purchase Order form with "Add Image" button highlighted and empty Image column in the item table

### Root Cause Analysis (Preliminary)

**Data Flow**: `#add_image` click → `#imageModal_new` modal → `#order_images_new` file input → `validateOrderImages()` (ret_general.js:532) → Compress.js async compression → `img_resource[]` global array → localStorage `img_details` → `#update_img_new` save → reads localStorage → sets `#order_iamges` hidden input → `addItem()` reads it

**Suspected Issues**:

1. **`validateOrderImages()` uses implicit `event` object** (ret_general.js:538: `var files = event.target.files`) — This function is called by the `$('#order_images_new').on('change', function(){ validateOrderImages(); })` handler without passing the event parameter. The function relies on the global `event` object, which is only available in some browsers (Chrome-specific behavior). In modern standards-compliant browsers, the implicit `event` variable may not be defined, causing `event.target.files` to throw an error and preventing any image processing.

2. **Async timing issue with 3-second `setTimeout`** (ret_general.js:600) — The image compression runs asynchronously via `Compress.compress()`, and the preview rendering uses a hardcoded `setTimeout(function(){...}, 3000)`. If the user clicks "Save" before the 3-second timeout fires, or if compression takes longer than 3 seconds for large images, the localStorage will not contain the image data.

3. **`img_resource` global array not reset between modal opens** — The `img_resource` array (ret_purchase_order.js:73) is a global variable that is never cleared when opening the modal. If images were previously added and the user opens the modal again, stale data may interfere.

### Files Involved
| File | Lines | Role |
|---|---|---|
| `admin/application/views/ret_purchase/order_form.php` | 145, 616-658 | View: `#add_image` button + `#imageModal_new` modal |
| `admin/assets/js/ret_purchase_order.js` | 43947-43991, 7209-7337 | JS: Button click handler + Add Item image rendering |
| `admin/assets/js/ret_general.js` | 520-665 | JS: `validateOrderImages()` + `remove_order_images()` |
| `admin/application/controllers/admin_ret_purchase.php` | 846-920 | PHP: Server-side image processing on save |
## PUR-CLT02 — Tax Calculation Mismatch Between GRN and Supplier Bill Entry (Hallmark Charges)

| Field | Value |
|---|---|
| Severity | P0 |
| Track | B (Business) |
| Category | Logic |
| Sprint | Sprint 1 |
| Pattern Match | None (Novel) |
| Module Brain | ❌ Needs build |
| Reporter | Client |
| Source | Client |

### Problem Summary

When a product has Hallmark Charges (Other Charges), the **GRN** correctly calculates taxes separately:
- 3% GST on `Weight × Rate` (the gold value)
- 18% GST on Hallmark Charges (kept separate)

However, the **Supplier Bill Entry** incorrectly:
1. Adds Other Charges **WITH their 18% tax** to the base gold value
2. Then calculates 3% GST on the **inflated** taxable amount (which now includes the other charges + their tax)

This causes **cascading tax** (tax calculated on a value that already includes tax).

### Numeric Example (from screenshots)

**GRN (Correct)**:
| Component | Value |
|---|---|
| Taxable Amount (40g × ₹15,000) | ₹600,000.00 |
| CGST (1.5%) | ₹9,000.00 |
| SGST (1.5%) | ₹9,000.00 |
| Other Charges (Hallmark) | ₹300.00 |
| Other Charges CGST (9%) | ₹27.00 |
| Other Charges SGST (9%) | ₹27.00 |
| **Final Price** | **₹618,354.00** |

**Supplier Bill Entry (Incorrect)**:
| Component | Value |
|---|---|
| Other Charges | ₹354.00 (300 + 54 tax, combined) |
| Taxable Amount | ₹600,354.00 (600,000 + 354, including tax!) |
| CGST (1.5%) | ₹9,005.31 (1.5% of 600,354) |
| SGST (1.5%) | ₹9,005.31 |
| Other Charges Tax | ₹54.00 |
| **Final Price** | **₹618,419.00** |

**Mismatch**: ₹618,419 − ₹618,354 = **₹65 discrepancy**

### Root Cause (Preliminary)

In the Supplier Bill Entry item calculation:
1. Other Charges include their own tax (300 + 54 = 354) in `other_charges_amount`
2. This combined amount (354) is added to the base taxable amount (600,000 → 600,354)
3. The gold GST (3%) is then calculated on 600,354 instead of just 600,000
4. This causes double-taxation on the hallmark charges portion

### Steps to Reproduce
1. Create a GRN with Hallmark Charges applicable (HUID Gold Ornaments, 40g, ₹15,000 rate)
2. Observe GRN calculates correctly: Taxable = 600,000, GST = 18,000, Charges = 300, Charges Tax = 54
3. Open the corresponding Supplier Bill Entry
4. Observe Taxable Amount = 600,354 (incorrectly includes charges with tax)
5. Observe GST = 18,010.62 (calculated on inflated base)
6. Observe final total mismatch with GRN

### Expected Behavior
- Supplier Bill taxable amount should be ₹600,000 (same as GRN)
- GST (3%) should be ₹18,000 (same as GRN)
- Other Charges and their tax should be calculated separately
- Final totals must match: GRN = Supplier Bill = ₹618,354

### Actual Behavior
- Supplier Bill taxable amount is ₹600,354 (includes other charges with tax)
- GST (3%) is ₹18,010.62 (cascading tax)
- Final totals mismatch: GRN = ₹618,354, Supplier Bill = ₹618,419
- Supplier Payment form also shows incorrect value (₹618,419)

### Evidence
- GRN Entry: https://www.arcvisvanathanjewellers.in/staging/admin/index.php/admin_ret_purchase/grn_invoice/59
- Supplier Bill Entry: https://www.arcvisvanathanjewellers.in/staging/admin/index.php/admin_ret_purchase/purchase/job_receipt/61
- Screenshots attached in bug report

### Key Files to Investigate
| File | Purpose |
|---|---|
| `admin/application/views/ret_purchase/pur_entry_form.php` | Supplier Bill Entry view (item row construction) |
| `admin/assets/js/ret_purchase_order.js` | Client-side calculation logic |
| `admin/application/controllers/admin_ret_purchase.php` | Purchase controller |
| `admin/application/models/ret_purchase_order_model.php` | Purchase model (server-side tax calc) |
| `admin/application/views/ret_purchase/grn_entry/form.php` | GRN Entry view (correct implementation reference) |
