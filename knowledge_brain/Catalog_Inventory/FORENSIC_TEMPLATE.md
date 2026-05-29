# Forensic Investigation Template
# Module: Catalog_Inventory

> **Purpose**: Layer-by-layer investigation cheat sheet for diagnosing bugs in this module.
> **Built**: 2026-03-13 — Round 2 (aligned to template)
> **How to use**: When a bug is reported, start at Layer 1 and work down until root cause is found.

---

## Layer 1: Symptom Collection

### Common Symptoms in Catalog_Inventory
| # | Symptom | Likely Layer | Severity |
|---|---|---|---|
| 1 | Master data not appearing in dropdowns (other modules) | Model / DB | P1 |
| 2 | Category/Product add/edit fails silently | Controller | P1 |
| 3 | Product not showing in Tagging/Billing/Estimation | DB (status) | P1 |
| 4 | Purity/Color/Cut/Clarity dropdown empty | Model query | P2 |
| 5 | Karigar information not saving (wastage/stone/charges) | Controller transaction | P1 |
| 6 | Image upload fails | Controller `upload_img()` / permissions | P3 |
| 7 | Status toggle not working (active/inactive) | Controller `*_status()` | P2 |
| 8 | Delete fails with "Exists in Stock" incorrectly | Model `getItemsinTagDetails()` | P2 |
| 9 | Financial year not switching | Model `setFinancialYearStatus()` | P1 |
| 10 | Tax group not applying to products | DB FK / Controller save | P1 |
| 11 | Bulk product update partial failure | Controller `bulkprodupdated()` | P2 |
| 12 | Undefined variable $carat notice | Controller `active_masters()` L143 | P3 |
| 13 | Category purities lost after edit | Controller DELETE-then-INSERT at L3669 | P1 |
| 14 | Product sections orphaned after delete | Controller `ret_product(delete)` missing cleanup | P2 |
| 15 | Design/Sub-design mapping broken | Controller design CRUD methods | P2 |

### Data to Collect Immediately
- [ ] Screenshot / exact error message
- [ ] URL and route that produced the error
- [ ] Input data that triggered the issue
- [ ] Browser console errors (F12 → Console)
- [ ] Network tab response (F12 → Network → XHR)
- [ ] User's branch/configuration
- [ ] Record ID(s) affected
- [ ] Which entity is affected? (product, category, karigar, purity, etc.)

---

## Layer 2: Reproduce & Isolate

### Reproduction Checklist
1. [ ] Can you reproduce with the SAME input data?
2. [ ] Can you reproduce with DIFFERENT input data?
3. [ ] Does it happen for ALL users or specific configuration?
4. [ ] Does it happen in ALL branches or specific branch?
5. [ ] Check INVARIANT_MATRIX: Is this variant-specific? (e.g., does it happen only for `wastage_type=2`?)
6. [ ] Is the record active (status=1) or inactive?

### Isolation Questions
- **When did it start?** → Check recent commits to `admin_ret_catalog.php` or `ret_catalog_model.php`
- **Who reported it?** → Check their branch, access level
- **Intermittent or consistent?** → If intermittent, likely transaction/concurrency
- **Data-dependent?** → Check for edge case values (empty purity list, 0 wastage, NULL metal_type)

---

## Layer 3: Client-Side Trace (JavaScript)

### JS File: `admin/assets/js/catalog.js`

### Key Console Log Points
```javascript
// 1. On category list load
console.log('CAT — Category AJAX response', data);

// 2. On product list load
console.log('CAT — Product AJAX response', data);

// 3. On jstree selection (category tree)
console.log('CAT — Tree selected:', data.selected, 'id_parent:', id_parent);

// 4. On image validation
console.log('CAT — Image file:', arguments[0].files[0].size, arguments[0].files[0].name);
```

### Key Variables to Inspect
| Variable | Where | Expected | How to Check |
|---|---|---|---|
| `ctrl_page` | Global, L1-2 | `['catalog','category','list']` | Console: `ctrl_page` |
| `base_url` | Global | App base URL with trailing `/` | Console: `base_url` |
| `access` | `load_category_list()` L162 | `{edit:'1',delete:'1'}` | Console after AJAX |
| `oTable` | DataTable instance | DataTable object | Console: `$('#catagory_list').DataTable()` |
| `id_parent` | jstree handler L70 | Parent category ID | Console: `$('#id_parent').val()` |

### Network Tab Checks
| Endpoint | Method | Expected Status | Key Params |
|---|---|---|---|
| `catalog/category/ajax_list` | GET | 200 JSON | `{category: [...], access: {...}}` |
| `catalog/product/ajax_list` | GET | 200 JSON | `{product: [...], access: {...}}` |
| `admin_ret_catalog/category/add` | POST | 200 JSON | name, cat_code, id_metal, id_purity |
| `admin_ret_catalog/ret_product/save` | POST | 302 Redirect | product[...] array |
| `admin_ret_catalog/category/edit/{id}` | GET | 200 JSON | Category data + purities |

---

## Layer 4: Server-Side Trace (PHP)

### Controller: `admin_ret_catalog.php`

### Trace Points Table
| Symptom | File | Method | Line | What to Check |
|---|---|---|---|---|
| Category not saving | `admin_ret_catalog.php` | `category('add')` | L3410 | Is `$data` array populated? Check `$this->input->post()` |
| Category purities lost | `admin_ret_catalog.php` | `category('update')` | L3669 | `deleteData` deletes all purities before re-insert |
| Product not saving | `admin_ret_catalog.php` | `ret_product('save')` | L6144 | Check `$_POST['product']` — is it nested array? |
| Product sections orphaned | `admin_ret_catalog.php` | `ret_product('delete')` | L6375-6404 | Deletes `ret_product_master` + `ret_product_charges` but NOT `ret_product_section` |
| Delete blocked incorrectly | `admin_ret_catalog.php` | delete cases | varies | Check `getItemsinTagDetails()` — what arguments passed? |
| Karigar save fails | `admin_ret_catalog.php` | `karigar('save')` | L3830+ | Check child table inserts (wastage, stones, charges, KYC, bank) |
| Status not toggling | `admin_ret_catalog.php` | `*_status()` methods | varies | Check `updateData` return from model + redirect |
| Image upload fails | `admin_ret_catalog.php` | `upload_img()` | L1514 | Check file permissions, dir existence, tmp_name |
| Undefined $carat | `admin_ret_catalog.php` | `active_masters()` | L143 | `$carat` from commented-out `getActiveCarat()` at L135 |
| Financial year stuck | `admin_ret_catalog.php` | `financial_status()` | L9628 | Check `setFinancialYearStatus()` model method |
| Bulk update partial | `admin_ret_catalog.php` | `bulkprodupdated('update')` | L6716 | Loop over `$_POST['product_ids']` — partial failure? |

### Model: `ret_catalog_model.php`

### Query Investigation Points
| Method | Query Type | Table(s) | Common Issue |
|---|---|---|---|
| `get_purity($id)` L192 | SELECT | `ret_purity` | **SQL injection** — `$id` concatenated directly |
| `get_color($id)` L275 | SELECT | `ret_color` | **SQL injection** — string concat |
| `get_cut($id)` L347 | SELECT | `ret_cut` | **SQL injection** — string concat |
| `get_clarity($id)` L419 | SELECT | `ret_clarity` | **SQL injection** — string concat |
| `get_floor($id)` L899 | SELECT | `ret_branch_floor` | **SQL injection** — string concat |
| `get_counter($id)` L987 | SELECT | `ret_branch_floor_counter` | **SQL injection** — string concat |
| `get_make_type($id)` L1055 | SELECT | `ret_making_type` | **SQL injection** — string concat |
| `get_theme($id)` L1109 | SELECT | `ret_theme` | **SQL injection** — string concat |
| `get_material($id)` L1173 | SELECT | `ret_material` | **SQL injection** — string concat |
| `get_stone($id)` L1527 | SELECT | `ret_stone` | **SQL injection** — string concat |
| `get_uom($id)` L1579 | SELECT | `ret_uom` | **SQL injection** — string concat |
| `getActiveSearchProd()` L1289 | SELECT | `ret_product_master` | **LIKE injection** — `$SearchTxt` unescaped |
| `getActiveSearchSubProd()` L1439 | SELECT | `ret_sub_product_master` | **LIKE injection** — `$SearchTxt` unescaped |
| `insertData()` L48 | INSERT | `{any}` | Runs `SHOW COLUMNS` every time — performance |
| `updateData()` L84 | UPDATE | `{any}` | Same `SHOW COLUMNS` overhead |
| `delete_product()` L709 | DELETE | product, product_images, product_details | Undefined `$child` variable if `$img` delete fails |
| `ajax_get_TaxProd()` L1295 | SELECT | `ret_product_master` | Complex elseif chain — easy to get wrong WHERE clause |

---

## Layer 5: Database Verification

### Diagnostic SQL Queries

#### 5a. Pull Complete Category Record
```sql
SELECT c.*, GROUP_CONCAT(p.purity SEPARATOR ', ') as purities, m.name as metal_name
FROM ret_category c
LEFT JOIN ret_metal_cat_purity mcp ON c.id_ret_category = mcp.id_category
LEFT JOIN ret_purity p ON mcp.id_purity = p.id_purity
LEFT JOIN metal m ON c.id_metal = m.id_metal
WHERE c.id_ret_category = '{RECORD_ID}'
GROUP BY c.id_ret_category;
```

#### 5b. Pull Complete Product with Sections and Charges
```sql
SELECT pm.*,
  rc.name as category_name,
  GROUP_CONCAT(DISTINCT s.section_name) as sections,
  GROUP_CONCAT(DISTINCT CONCAT(ch.charge_name, ':', pc.charge_value)) as charges
FROM ret_product_master pm
LEFT JOIN ret_category rc ON pm.cat_id = rc.id_ret_category
LEFT JOIN ret_product_section ps ON pm.pro_id = ps.pro_id
LEFT JOIN ret_section s ON ps.id_section = s.id
LEFT JOIN ret_product_charges pc ON pm.pro_id = pc.prod_id
LEFT JOIN ret_charges ch ON pc.charge_id = ch.id
WHERE pm.pro_id = '{RECORD_ID}'
GROUP BY pm.pro_id;
```

#### 5c. Check Orphan Records
```sql
-- Product section orphans (product deleted but sections remain)
SELECT ps.* FROM ret_product_section ps
LEFT JOIN ret_product_master pm ON ps.pro_id = pm.pro_id
WHERE pm.pro_id IS NULL;

-- Category purity orphans
SELECT mcp.* FROM ret_metal_cat_purity mcp
LEFT JOIN ret_category c ON mcp.id_category = c.id_ret_category
WHERE c.id_ret_category IS NULL;

-- Product charge orphans
SELECT pc.* FROM ret_product_charges pc
LEFT JOIN ret_product_master pm ON pc.prod_id = pm.pro_id
WHERE pm.pro_id IS NULL;
```

#### 5d. Check Entity Stock References (Delete Guard)
```sql
-- Does this category have tagged items?
SELECT COUNT(*) as tag_count FROM ret_taging WHERE id_category = '{CATEGORY_ID}';

-- Does this product have tagged items?
SELECT COUNT(*) as tag_count FROM ret_taging WHERE id_product = '{PRODUCT_ID}';

-- Does this purity have tagged items?
SELECT COUNT(*) as tag_count FROM ret_taging WHERE id_purity = '{PURITY_ID}';
```

#### 5e. Audit Trail Check
```sql
SELECT * FROM log_detail
WHERE module IN ('Category','Product','Purity','Color','Cut','Clarity','Karigar')
  AND record = '{RECORD_ID}'
ORDER BY event_date DESC
LIMIT 20;
```

---

## Layer 6: Root Cause Classification

| Category | Risk | Example |
|---|---|---|
| **SQL Injection** | P0 — Security | `get_purity($id)` — 11+ methods with string concat |
| **LIKE Injection** | P1 — Security | `getActiveSearchProd()` — unescaped search text |
| **Orphan Records** | P1 — Data Integrity | Product section not cleaned on delete |
| **Data Loss (DELETE-INSERT)** | P1 — Data Integrity | Category purities lost if re-insert fails |
| **CSRF (GET Delete)** | P1 — Security | Purity/Color/Cut delete via GET |
| **Missing Server Validation** | P2 — Data Quality | Image size not validated server-side |
| **Undefined Variable** | P2 — Stability | `$carat` in `active_masters()`, `$child` in `delete_product()` |
| **Performance** | P3 — Performance | `SHOW COLUMNS` on every insert/update |
| **Configuration Conflict** | P2 — Business | `stone_board_rate_cal=1` + `has_stone=0` (see INVARIANT_MATRIX) |

### Bug Ticket Template
```
Bug ID: CAT-{TRACK}{ROUND}{NN}
Title: {Concise description}
Severity: {P0/P1/P2/P3}
Track: {A (System) / B (Business)}
Category: {from table above}
Root Cause Layer: {JS / Controller / Model / DB / Config}
File: {path}
Method: {function_name}
Line: {line_number}
Current Behavior: {what happens now}
Expected Behavior: {what should happen}
Evidence: {DB query results, console logs, screenshots}
```

---

## Layer 7: Master Data Integrity (Module-Specific)

Since Catalog_Inventory is the **master data hub** for the entire ERP, a bug here has cascading effects:

1. **Before modifying ANY master data**, verify downstream impact:
   ```sql
   -- Tags referencing this product
   SELECT COUNT(*) FROM ret_taging WHERE id_product = {ID};
   -- Billing records referencing this product
   SELECT COUNT(*) FROM ret_billing WHERE product_id = {ID};
   ```

2. **After a status change (inactive)**, check if dependent modules handle it:
   - Does Billing block sales of inactive products?
   - Does Tagging prevent new tags for inactive products?
   - Does Estimation show warnings?

3. **After category rename/purity change**, verify FK references still valid downstream

---

## Layer 7b: Karigar Approval Flow Trace

For karigar wastage/approval bugs:

1. Check `ret_karigar.approval_status` in DB
2. Verify `ret_karikar_items_wastage` records have correct `approved` flag
3. Check `get_profile_settings()` → is `vendor_approval_otp_req` enabled?
4. OTP flow: `vendor_sendotp()` → `vendor_verify_otp()` → approval update
5. Verify SMS model is loaded and functional
