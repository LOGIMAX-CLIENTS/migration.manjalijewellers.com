# Catalog_Inventory — BUSINESS RULES

> **Built**: 2026-03-13

## RULE-CAT-001: Category Name Uppercasing
**Formula**: All category names are forced to uppercase on save/update
**Implementation**: PHP `strtoupper()` at L3416 (add), L3613 (update) of controller
**Validation**: Server-side only
**Edge cases**: None — simple string transform

## RULE-CAT-002: Category-Purity Association
**Formula**: Each category must be associated with one or more purities from `ret_purity`
**Implementation**: Controller `category(add)` L3412 — `explode(',', $_POST['id_purity'])` → loop insert into `ret_metal_cat_purity`
**Validation**: Server-side only (no JS validation visible)
**Edge cases**: Empty purity list still proceeds — no validation check

## RULE-CAT-003: Category Delete Guard
**Formula**: A category cannot be deleted if it has tagged items in `ret_taging`
**Implementation**: Controller `category(delete)` L3557 — `getItemsinTagDetails('', $id, '', '', '', '')`
**Validation**: Server-side only
**Edge cases**: Only checks `ret_taging` — doesn't check `ret_product_master.cat_id` references

## RULE-CAT-004: Product Name Uppercasing
**Formula**: Retail product names are forced to uppercase
**Implementation**: PHP `strtoupper()` at L6194 (save), L6461 (update) of controller
**Validation**: Server-side only

## RULE-CAT-005: Product Delete Guard
**Formula**: A product cannot be deleted if it has tagged items
**Implementation**: Controller `ret_product(delete)` L6377 — `getItemsinTagDetails('', '', $id, '', '', '')`
**Validation**: Server-side only

## RULE-CAT-006: Purity Delete Guard
**Formula**: A purity entry cannot be deleted if it exists in tag details
**Implementation**: Controller `purity(Delete)` L355 — `getItemsinTagDetails('', '', '', '', '', $id)`
**Validation**: Server-side only

## RULE-CAT-007: Image Size Limits
**Formula**: Category images ≤ 1MB, Product images ≤ 5MB. Only JPG/PNG allowed.
**Implementation**: JS `validate_catImage()` L276 (1MB check), `validateImage()` L374 (5MB check)
**Validation**: Client-side only — no server-side size validation visible
**Edge cases**: MIME type not checked — only extension validation. An attacker could upload a PHP file with .jpg extension.

## RULE-CAT-008: Category Image Naming
**Formula**: `{category_id}_CAT_{random(120-1230)}.jpg`
**Implementation**: Controller L3467 — `$filename = $result . '_CAT_' . mt_rand(120, 1230) . ".jpg"`
**Edge cases**: Random range is small (120-1230) — collision possible if many images for same category

## RULE-CAT-009: Product Default Values
**Formula**: When saving a retail product, boolean fields default to 0 if not provided, numeric fields to 1.
**Implementation**: Controller `ret_product(save)` L6152-6252 — `(!empty($addData['field']) ? $addData['field'] : default)`
**Edge cases**: `!empty()` treats `'0'` as empty in PHP < 8 — could cause boolean fields to always default

## RULE-CAT-010: InsertData Default Value Lookback
**Formula**: Generic `insertData()` checks DB column defaults via `SHOW COLUMNS FROM {table}`, and replaces empty values with column defaults
**Implementation**: Model L48-82
**Edge cases**: Performance concern — runs `SHOW COLUMNS` on every insert. Also, `$value === 0 || $value === '0'` special-cased to preserve zeroes.

## RULE-CAT-011: Financial Year Status Exclusivity
**Formula**: Only one financial year can be active at a time
**Implementation**: Controller `financial_year()` — sets status via `setFinancialYearStatus()` which likely deactivates others
**Validation**: Server-side

## RULE-CAT-012: Karigar Mobile/Email Uniqueness
**Formula**: Karigar mobile number and email must be unique
**Implementation**: Model `mobile_available()` L1855, `email_available()` L1885
**Validation**: Server-side (likely called via AJAX validation)

## RULE-CAT-013: Design Short Code Generation
**Formula**: Auto-generated design short codes via `genDesignShortCode()`
**Implementation**: Model L1997
**Validation**: Server-side

## RULE-CAT-014: Product Short Code Generation
**Formula**: Auto-generated product short codes via `genProdShortCode()`
**Implementation**: Model L2616
**Validation**: Server-side

## RULE-CAT-015: Karigar Wastage Approval Workflow
**Formula**: Karigar wastage rates require approval before becoming active
**Implementation**: Controller `karigar_approval()` L17926+ — loads approval list, allows approve/reject
**Validation**: Server-side, session-based (no RBAC)

## RULE-CAT-016: Bulk Product Update
**Formula**: Multiple products can be updated simultaneously (status + tax group)
**Implementation**: Controller `bulkprodupdated(update)` L6716 — loops through `$_POST['product_ids']`
**Validation**: Server-side only
**Edge cases**: No limit on batch size — could be slow for large selections

---

> Rules 17-20 added in Round 4 (verification pass)

## RULE-CAT-017: Universal Transaction Wrapping
**Formula**: Every write operation (INSERT, UPDATE, DELETE) is wrapped in `$this->db->trans_begin()` / `trans_commit()` / `trans_rollback()`
**Implementation**: 203 `trans_begin` calls across the controller — one for every CRUD operation on every entity
**Validation**: Server-side (database level)
**Edge cases**: Transaction wrapping is consistent, BUT the DELETE-then-INSERT pattern inside a transaction still creates a window where data is inconsistent if interrupted

## RULE-CAT-018: Minimal Form Validation
**Formula**: Only **3** `set_rules` in 23,579 lines — all at L22606-22608 in `cover_up()` for metal/weight/created_by fields. All other entities accept ANY POST data without validation.
**Implementation**: Controller L22606-22608 (`cover_up` method only)
**Risk**: ⚠️ All entity CRUD operations except cover_up have **zero** server-side validation — data quality depends entirely on client-side form validation which can be bypassed
**Round 6 correction**: Previously documented as 6, verified to be exactly 3.

## RULE-CAT-019: Delete Guard Pattern (6 entities only)
**Formula**: Only 6 entities check `getItemsinTagDetails()` before deletion — the rest delete without guard
**Implementation**: Delete guard at L3557 (category), L6377 (product), plus 4 more (purity, color, cut, clarity)
**Risk**: ⚠️ Entities like stone, material, UOM, section, design can be deleted even if referenced by tagged items. Only the 6 guarded entities are protected.

## RULE-CAT-020: Entity Name Uppercasing Scope
**Formula**: `strtoupper()` is applied to category names (L3416, L3613), product names (L6194, L6461), and **design names** (L7839). Other entities are saved as-is.
**Implementation**: Controller add/update cases for category, product, and design
**Edge cases**: Inconsistency — only 3 of ~30 entity types force uppercase

## RULE-CAT-021: Financial Year Status Exclusivity (No Transaction)
**Formula**: `setFinancialYearStatus()` deactivates ALL financial years, then `updateData()` activates the selected one
**Implementation**: Controller `financial_status()` at L9628-9649
**⚠️ Risk**: NOT wrapped in `trans_begin/trans_commit` — if the activate UPDATE fails, ALL financial years are inactive. This is a **P1 data integrity risk**.

## RULE-CAT-022: Design Fixed Rate Duplicate Key
**Formula**: The design save array has `'fixed_rate'` defined twice (L7867 and L7869) — second value overwrites first
**Implementation**: Controller `ret_design('save')` at L7867-7869
**Impact**: Currently no data loss (both values identical), but a code smell that could cause issues if logic changes

## RULE-CAT-023: get_profile_settings SQL Injection
**Formula**: `get_profile_settings($id_profile)` at model L199 concatenates `$id_profile` directly into SQL
**Implementation**: Model L199-206 — `"SELECT pr.vendor_approval_otp_req FROM profile pr where pr.id_profile ='".$id_profile."'"`
**Risk**: ⚠️ SQL injection via `$id_profile` parameter. This method is called from `karigar_approval()` at L17940. The `$id_profile` comes from session data, so risk is LOW (attacker would need session manipulation), but the pattern is still vulnerable.
**Round 6 discovery**: Previously undocumented method.

