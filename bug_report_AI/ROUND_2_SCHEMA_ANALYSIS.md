# Round 2: DB Schema Cross-Reference Analysis

**Analysis date:** 2026-02-17  
**Source file:** `dev_structure.sql` (15,142 lines, MySQL 8.4.7)  
**Methodology:** Column-by-column comparison of all 13 estimation tables against controller save/update/delete code

---

## Tables Analyzed (13)

| # | Table | Engine | PK Auto-Inc | FK Index Count |
|---|-------|--------|-------------|----------------|
| 1 | `ret_estimation` | InnoDB | ✅ | 10 |
| 2 | `ret_estimation_items` | InnoDB | ✅ | 14 |
| 3 | `ret_estimation_item_stones` | InnoDB | ✅ | 5 |
| 4 | `ret_estimation_item_other_materials` | InnoDB | ✅ | 0 |
| 5 | `ret_estimation_old_metal_sale_details` | InnoDB | ✅ | 9 |
| 6 | `ret_estimation_other_charges` | InnoDB | ✅ | 2 |
| 7 | `ret_estimation_other_inventory_issue` | InnoDB | ✅ | 0 |
| 8 | `ret_est_chit_utilization` | InnoDB | ✅ (UNIQUE, not PK) | 2 |
| 9 | `ret_est_gift_voucher_details` | InnoDB | ❌ **NO AUTO_INCREMENT** | 0 |
| 10 | `ret_est_other_metals` | **MyISAM** | ✅ | 5 |
| 11 | `ret_est_tag_merge` | InnoDB | ✅ | 0 |
| 12 | `ret_esti_old_metal_stone_details` | InnoDB | ✅ | 0 |
| 13 | `ret_est_sales_return_utilization` | InnoDB | ✅ | 0 |

---

## New Schema-Level Bugs Found

### EST-S01 — `ret_est_gift_voucher_details.gift_voucher_id` has NO AUTO_INCREMENT
**Severity:** P0 (Critical — Silent insert failures)  
**Classification:** Schema defect → Data loss

**Schema:**
```sql
CREATE TABLE `ret_est_gift_voucher_details` (
  `gift_voucher_id` int NOT NULL,  -- NOT NULL, no AUTO_INCREMENT, no DEFAULT
  `est_id` int DEFAULT NULL,
  `voucher_no` varchar(45) DEFAULT NULL,
  `gift_voucher_details` varchar(365) DEFAULT NULL,
  `gift_voucher_amt` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
```

**Problem:** The `gift_voucher_id` column is `int NOT NULL` without `AUTO_INCREMENT` or any default. The controller code at lines 1210 and 2336 builds the insert array as:
```php
$arrayGiftVoucher[] = array(
    'est_id' => $insId,
    'voucher_no' => $estGiftVoucher['voucher_no'][$key],
    'gift_voucher_details' => NULL,
    'gift_voucher_amt' => $estGiftVoucher['gift_voucher_amt'][$key]
);
```
Notice `gift_voucher_id` is never provided. With MySQL strict mode, this will fail with `Field 'gift_voucher_id' doesn't have a default value`. Even without strict mode, all rows get `gift_voucher_id = 0`, making the column useless as an identifier.

> [!CAUTION]
> This bug compounds with EST-001 (wrong variable `$arrayMaterials` used instead of `$arrayGiftVoucher`). Even if EST-001 is fixed, the insert will still fail due to this schema issue unless the column is altered to AUTO_INCREMENT.

**Fix:** `ALTER TABLE ret_est_gift_voucher_details MODIFY gift_voucher_id int NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (gift_voucher_id);`

---

### EST-S02 — `ret_est_other_metals` uses MyISAM engine (not transactional)
**Severity:** P1 (Major — Transaction safety violation)  
**Classification:** Schema defect → Data integrity

**Schema:**
```sql
) ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=latin1;
```

**Problem:** All other 12 estimation tables use InnoDB. The controller wraps all saves/updates/deletes inside `$this->db->trans_begin()` / `trans_commit()` / `trans_rollback()`. MyISAM ignores transactions entirely, meaning:
- If a transaction is rolled back, rows already inserted into `ret_est_other_metals` **will persist as orphans**
- If a partial failure occurs, this table will have data inconsistent with the InnoDB tables

**Fix:** `ALTER TABLE ret_est_other_metals ENGINE=InnoDB;`

---

### EST-S03 — Integer truncation: `wastage_per`, `act_wast_per`, `disc_per`, `bulk_was_disc_per`
**Severity:** P2 (Minor — Silent data precision loss)  
**Classification:** Schema / code type mismatch

| Column | Table | DB Type | Code sends | Line |
|--------|-------|---------|------------|------|
| `wastage_per` | `ret_est_chit_utilization` | `int` | Decimal from POST | 1237, 2363 |
| `act_wast_per` | `ret_estimation_items` | `int` | Decimal from POST | 972 |
| `disc_per` | `ret_estimation` | `int` | Decimal via `$addData['discount']` | 328, 1524 |
| `bulk_was_disc_per` | `ret_estimation` | `int` | Decimal via `$addData['blk_discount']` | 330, 1526 |

**Problem:** PHP sends decimal values (e.g. `12.5`), MySQL truncates to `12` silently. Percentages by nature should support decimals.

**Fix:** `ALTER TABLE` to change each to `decimal(10,2)`.

---

### EST-S04 — `form_secret` UNIQUE constraint exists but is never populated
**Severity:** P1 (Major — Missed CSRF / double-submit protection)  
**Classification:** Schema / code gap

**Schema:**
```sql
`form_secret` varchar(100) DEFAULT NULL,
-- ...
UNIQUE KEY `form_secret` (`form_secret`),
```

**Problem:** The `ret_estimation` table has a `form_secret` column with a `UNIQUE` constraint — designed for preventing double-submit attacks. However, grepping the controller for `form_secret` yields **zero results**. The save path (line 312-357) never includes `form_secret` in the insert array, so it defaults to `NULL`. Since MySQL UNIQUE allows multiple NULLs, this doesn't cause errors — but the anti-double-submit protection is completely inoperative.

**Fix:** Generate a UUID/token in the form, send it in POST, and include it in the insert array:
```php
'form_secret' => $addData['form_secret'], // in save data array
```

---

### EST-S05 — `esti_date` type mismatch: `date` column receives `datetime` string
**Severity:** P2 (Minor — Data truncation, usually harmless)  
**Classification:** Schema / code type mismatch

**Schema:** `esti_date date DEFAULT NULL` in `ret_est_sales_return_utilization`

**Code (line 1281):**
```php
'esti_date' => $estimation_datetime,  // "2024-01-15 14:30:00"
```

**Problem:** `$estimation_datetime` is a full datetime string like `"2024-01-15 14:30:00"`. MySQL will truncate to `"2024-01-15"` and may throw a warning in strict mode. Not a data-loss issue (the time portion is redundant here) but it's sloppy and will produce warnings in MySQL 8+ strict mode.

**Fix:** Use `date('Y-m-d', strtotime($estimation_datetime))` or change column to `datetime`.

---

### EST-S06 — Delete path misses 6 child tables
**Severity:** P1 (Major — Orphan data accumulation)  
**Classification:** Logic defect → Data integrity

**Delete path (lines 1453-1503) cleans up:**
| Table | ✅ Deleted |
|-------|-----------|
| `ret_est_gift_voucher_details` | ✅ |
| `ret_est_chit_utilization` | ✅ |
| `ret_estimation_items` | ✅ |
| `ret_estimation_item_stones` | ✅ |
| `ret_estimation_item_other_materials` | ✅ |
| `ret_estimation_old_metal_sale_details` | ✅ |
| `ret_estimation` | ✅ |

**Missing from delete path:**
| Table | FK Column | ❌ NOT deleted |
|-------|-----------|---------------|
| `ret_est_other_metals` | `est_item_id` | ❌ Orphaned |
| `ret_estimation_other_charges` | `est_item_id` | ❌ Orphaned |
| `ret_est_tag_merge` | `est_item_id` | ❌ Orphaned |
| `ret_esti_old_metal_stone_details` | `est_id` | ❌ Orphaned |
| `ret_est_sales_return_utilization` | `est_id` | ❌ Orphaned |
| `ret_estimation_other_inventory_issue` | `esti_id` | ❌ Orphaned |

> [!IMPORTANT]
> Note: `ret_esti_old_metal_stone_details` IS cleaned up in the update path (line 1574) but NOT in the delete path. This is an asymmetric bug.

**Update path (lines 1548-1577) similarly misses:**
- `ret_est_other_metals`
- `ret_estimation_other_charges`
- `ret_est_tag_merge`
- `ret_est_sales_return_utilization`
- `ret_estimation_other_inventory_issue`

---

### EST-S07 — `ret_est_gift_voucher_details` has NO PRIMARY KEY
**Severity:** P2 (Minor — Performance / data integrity)  
**Classification:** Schema defect

The table has no `PRIMARY KEY` defined (even though `gift_voucher_id` exists as a column). Without a PK:
- No unique row identification
- Poor query performance on large datasets
- No FK referential integrity possible
- No clustered index (InnoDB uses a hidden one, but it's not optimal)

**Fix:** After adding `AUTO_INCREMENT` (EST-S01), add `PRIMARY KEY (gift_voucher_id)`.

---

## Round 1 Bugs Confirmed / Upgraded by Schema Evidence

| Round 1 Bug | Schema Finding | Impact |
|-------------|---------------|--------|
| **EST-001** (Gift voucher wrong variable) | EST-S01 confirms the insert would also fail even if variable is fixed (schema lacks AUTO_INCREMENT) | Upgraded: Two independent bugs, both must be fixed |
| **EST-003** (Orphan records on delete) | EST-S06 provides exact table list — 6 tables missing, not 3 as initially reported | Expanded scope: 6 tables not 3 |
| **EST-006** (Duplicate rows on update) | EST-S06 confirms 5 tables missing from update delete-before-reinsert | Confirmed with full table list |
| **EST-007** (Missing `rate_per_gram` on chit update) | Column `rate_per_gram` is `decimal(10,2) NOT NULL DEFAULT '0.00'` — missing field will get default `0.00`, silently losing data | Confirmed: data loss on edit |
| **EST-004** (Missing form_secret validation) | EST-S04 confirms the column and UNIQUE constraint exist but are completely unused | Confirmed: entire mechanism inoperative |

---

## Summary Statistics

| Category | Round 1 | Round 2 (New) | Total |
|----------|---------|---------------|-------|
| P0 (Critical) | 2 | 1 (EST-S01) | 3 |
| P1 (Major) | 5 | 3 (EST-S02, EST-S04, EST-S06) | 8 |
| P2 (Minor) | 7 | 3 (EST-S03, EST-S05, EST-S07) | 10 |
| P3 (Cosmetic) | 2 | 0 | 2 |
| **Total** | **16** | **7** | **23** |

---

## Recommended Migration Script

```sql
-- EST-S01 + EST-S07: Fix gift voucher table
ALTER TABLE `ret_est_gift_voucher_details`
  MODIFY `gift_voucher_id` int NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`gift_voucher_id`);

-- EST-S02: Convert MyISAM to InnoDB
ALTER TABLE `ret_est_other_metals` ENGINE=InnoDB;

-- EST-S03: Fix integer truncation
ALTER TABLE `ret_est_chit_utilization` MODIFY `wastage_per` decimal(10,2) DEFAULT NULL;
ALTER TABLE `ret_estimation_items` MODIFY `act_wast_per` decimal(10,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `ret_estimation` MODIFY `disc_per` decimal(10,2) DEFAULT NULL;
ALTER TABLE `ret_estimation` MODIFY `bulk_was_disc_per` decimal(10,2) DEFAULT NULL;
```

> [!WARNING]
> Run these in a maintenance window. Back up tables first. The MyISAM→InnoDB conversion may take time on large tables. Test on staging before production.
