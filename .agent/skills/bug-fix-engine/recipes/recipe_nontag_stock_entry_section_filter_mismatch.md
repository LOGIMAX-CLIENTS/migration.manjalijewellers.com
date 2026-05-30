# Recipe: Non-Tagged Employee Stock Entry Missing Products (Section Filter Mismatch)

## Metadata
- **Recipe ID**: CAT-S02
- **Pattern**: PAT-QRY-008
- **Module**: Catalog / Stock Audit
- **Severity**: P1
- **Track**: A (System — Query Logic)
- **Created**: 2026-05-18
- **Applies To**: Any client using `getProductBySection_NonTag()` with multi-section non-tagged stock

---

## Symptom

The **Employee Stock Entry** page (`admin_ret_catalog/employee_stock_product/add`) shows **fewer
products / sections** than the **Section Stock In-Out report** (`admin_ret_reports/section_stock_inout/list`)
for the same branch and metal.

Classic example: Bangle section shows only 1 of 2 non-tagged categories in Calicut.

---

## Root Cause

Two restrictive filters in `getProductBySection_NonTag()` in `ret_catalog_model.php`:

1. **`INNER JOIN ret_section ... AND sec.status = 1`** — silently drops products whose physical
   storage section has been marked `status = 0` (inactive), even if active stock still exists there.

2. **`HAVING stock_gwt > 0`** — drops any product+section combination where the total gross weight
   sums to zero, even if `no_of_piece > 0` (pieces-only stock, e.g. low-weight items logged as pcs).

The reports model `get_nontag_section_details()` applies neither restriction, so it shows more rows.

---

## Detection

Search for this pattern in `ret_catalog_model.php`:

```bash
grep -n "INNER JOIN ret_section sec.*status = 1" admin/application/models/ret_catalog_model.php
grep -n "HAVING stock_gwt > 0" admin/application/models/ret_catalog_model.php
```

Both lines should be within the `getProductBySection_NonTag()` method.

---

## Fix

**File**: `admin/application/models/ret_catalog_model.php`
**Method**: `getProductBySection_NonTag()`

### Change 1 — Section join (removes status=1 restriction)

```diff
- INNER JOIN ret_section sec ON sec.id_section = ni.id_section AND sec.status = 1
+ LEFT JOIN ret_section sec ON sec.id_section = ni.id_section
```

### Change 2 — HAVING clause (covers pcs-only stock)

```diff
- HAVING stock_gwt > 0
+ HAVING (stock_gwt > 0 OR stock_pcs > 0)
```

---

## Before (Buggy)

```php
FROM ret_nontag_item ni
INNER JOIN ret_product_master p ON p.pro_id = ni.product
INNER JOIN ret_category cat ON cat.id_ret_category = p.cat_id
INNER JOIN ret_section sec ON sec.id_section = ni.id_section AND sec.status = 1
WHERE p.stock_type = ...
GROUP BY p.pro_id, ni.id_section
HAVING stock_gwt > 0
ORDER BY sec.section_name, p.product_name
```

## After (Fixed)

```php
FROM ret_nontag_item ni
INNER JOIN ret_product_master p ON p.pro_id = ni.product
INNER JOIN ret_category cat ON cat.id_ret_category = p.cat_id
LEFT JOIN ret_section sec ON sec.id_section = ni.id_section
WHERE p.stock_type = ...
GROUP BY p.pro_id, ni.id_section
HAVING (stock_gwt > 0 OR stock_pcs > 0)
ORDER BY sec.section_name, p.product_name
```

---

## Verification Steps

1. Open `admin_ret_reports/section_stock_inout/list` → select branch + metal → note all
   non-tagged section rows.
2. Open `admin_ret_catalog/employee_stock_product/add` → select same branch + metal + date.
3. Verify the **same sections and products** appear in step 2 as in step 1.
4. Confirm products with `no_of_piece > 0` and `gross_wt = 0` are now visible.
5. Confirm products in sections marked `status = 0` in `ret_section` are visible if stock exists.

---

## Related

- **CAT-S01**: Same file — root section join was from product master instead of `ret_nontag_item`
- **PAT-QRY-007**: Dynamic Stock Section Mismatch (parent pattern)
- **Reference**: `get_nontag_section_details()` in `ret_reports_model.php` is the canonical
  query — always cross-check against it when the stock entry page diverges from the report.
