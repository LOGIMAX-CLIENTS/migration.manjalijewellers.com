# Recipe: Stock Rotation Detail — 4-Level Hierarchy (Design + Karigar Dimensions)

## Metadata
- **Pattern ID**: PAT-RPT-011
- **Severity**: MEDIUM
- **Modules Affected**: Ret_Reports (Stock Rotation Detail Report)
- **Auto-fixable**: No (multi-file, SQL restructuring + JS rendering + view update)

## Client Scope
- **Applies to**: ALL
- **Reason**: All clients share `get_stock_rotation_itemwise()` in `ret_reports_model.php`, the DataTable renderer in `ret_reports.js`, and the view in `stock_rotation_detail.php`

## Created By
- **Developer**: Antigravity
- **Client**: AMS-RetailAdmin
- **Date**: 2026-05-12
- **Source Bug ID**: N/A (feature expansion — requested to drill from sub-product into Design or Karigar)

---

## Symptom
The Stock Rotation Detail report only supports a 3-level tree (Group → Product → Sub-Product) for all group-by modes. Users need:

1. **Collection mode (group_by=1)**: Drill into **Design** within each sub-product → `Collection → Product → Sub-Product → Design`
2. **Karigar mode (group_by=4)**: Group by karigar first, then drill through collection hierarchy → `Karigar → Collection → Product → Sub-Product`

Without this, stock rotation analysis cannot isolate individual design performance or karigar-level inventory efficiency.

---

## Root Cause
The existing architecture groups all movement data (inward, outward, sold, etc.) by `sub_pro_id` only. When two different designs exist within the same sub-product, their movements are merged — making per-design analysis impossible. Similarly, karigar-level tracking requires a completely different top-level grouping axis.

### Technical Gaps
| Component | Problem |
|-----------|---------|
| Skeleton query | No design_id or karigar_id column |
| Movement queries (5a–5d) | No extra dimension in SELECT/GROUP BY |
| Simulation keys | Flat `sub_pro_id` — collides when same sub-product has multiple designs/karigars |
| Hierarchy builder | Hardcoded 3-level nesting |
| JS renderer | Hardcoded 3-level flatten + drill-down |
| View dropdown | Missing Karigar option |

---

## Detection

### Check if skeleton has extra-dimension support:
```powershell
findstr /n "extra_skel_select" "application\models\ret_reports_model.php"
```
If no matches inside `get_stock_rotation_itemwise()`, the old 3-level-only code exists.

### Check if Karigar option exists in dropdown:
```powershell
findstr /n "Karigar" "application\views\ret_reports\stock_rotation_detail.php"
```
If no match, the Karigar grouping mode is not available.

### Check if JS supports level 4:
```powershell
findstr /n "sr-leaf-row" "assets\js\ret_reports.js"
```
If no match, the 4-level drill-down is not implemented.

---

## Files
- `application/models/ret_reports_model.php` — function `get_stock_rotation_itemwise()` (skeleton, all queries, simulation, hierarchy builder)
- `assets/js/ret_reports.js` — function `render_sr_detail_tree()` + drill-down event handlers
- `application/views/ret_reports/stock_rotation_detail.php` — group-by dropdown

---

## Fix

### Fix 1: Add Karigar Option to View Dropdown (stock_rotation_detail.php)

#### Before
```html
<option value="1" selected>Collection</option>
<option value="2">Product</option>
<option value="3">Category</option>
```

#### After
```html
<option value="1" selected>Collection</option>
<option value="2">Product</option>
<option value="3">Category</option>
<option value="4">Karigar</option>
```

---

### Fix 2: Dynamic Extra-Dimension Variables (ret_reports_model.php)

Add these variables after `$common_joins`, before the skeleton query:

```php
// -- Extra dimension based on group_by --
$extra_skel_join = "";
$extra_skel_select = "";
$extra_data_join = "";
$extra_data_select = "";
$extra_group = "";

if ($group_by == 1) {
    // Collection mode: add Design level under sub-product
    $extra_skel_join = "LEFT JOIN ret_design_master des ON des.design_no = tag.design_id";
    $extra_skel_select = ", IFNULL(tag.design_id, 0) as design_id, IFNULL(des.design_name, 'No Design') as design_name";
    $extra_data_select = ", IFNULL(tag.design_id, 0) as extra_dim";
    $extra_group = ", tag.design_id";
} elseif ($group_by == 4) {
    // Karigar mode: group by karigar
    $extra_skel_join = "LEFT JOIN ret_lot_inwards lot_k ON lot_k.lot_no = tag.tag_lot_id
        LEFT JOIN ret_karigar kar ON kar.id_karigar = lot_k.gold_smith";
    $extra_skel_select = ", IFNULL(lot_k.gold_smith, 0) as id_karigar, IFNULL(kar.firstname, 'Unknown') as karigar_name";
    $extra_data_join = "LEFT JOIN ret_lot_inwards lot_k ON lot_k.lot_no = tag.tag_lot_id";
    $extra_data_select = ", IFNULL(lot_k.gold_smith, 0) as extra_dim";
    $extra_group = ", lot_k.gold_smith";
}
```

**Key design decisions:**
- `extra_skel_join`: Skeleton needs design name / karigar name for display labels
- `extra_data_join`: Data queries need lot join only for karigar (design_id is on tag table directly)
- `extra_data_select`: All data queries output `extra_dim` uniformly — consumed as composite key
- `extra_group`: Appended to every GROUP BY clause

---

### Fix 3: Inject Extra Dimension into Skeleton Query (ret_reports_model.php)

#### Before
```php
$sql_skeleton = "SELECT p.pro_id as sub_pro_id,
        p.product_name as sub_product_name,
        ...
    FROM ret_taging tag
    $common_joins
    WHERE ...
    GROUP BY p.pro_id";
```

#### After
```php
$sql_skeleton = "SELECT p.pro_id as sub_pro_id,
        p.product_name as sub_product_name,
        ...
        $extra_skel_select
    FROM ret_taging tag
    $common_joins
    $extra_skel_join
    WHERE ...
    GROUP BY p.pro_id $extra_group";
```

---

### Fix 4: Inject Extra Dimension into All Data Queries (ret_reports_model.php)

Apply the same pattern to **all 8 movement queries** (opening, inward, outward, sold 5a, sales_trans 5b, sales_ret 5c, ecom 5d):

For queries using `$common_joins` (opening, inward, outward, 5a, 5b, 5c):
```php
// Add to SELECT:
$extra_data_select

// Add to FROM/JOIN (after $common_joins):
$extra_data_join

// Add to GROUP BY:
$extra_group
```

For the **ecom query (5d)** which has its own join chain (tag alias = `t`):
```php
// Build inline since alias differs
$ecom_extra_select = "";
$ecom_extra_join = "";
$ecom_extra_group = "";
if ($group_by == 1) {
    $ecom_extra_select = ", IFNULL(t.design_id, 0) as extra_dim";
    $ecom_extra_group = ", t.design_id";
} elseif ($group_by == 4) {
    $ecom_extra_join = "LEFT JOIN ret_lot_inwards lot_k2 ON lot_k2.lot_no = t.tag_lot_id";
    $ecom_extra_select = ", IFNULL(lot_k2.gold_smith, 0) as extra_dim";
    $ecom_extra_group = ", lot_k2.gold_smith";
}
```

**Critical**: The ecom query uses `t` as the tag alias (not `tag`), and already has a `lot_k` alias in the karigar chain from common joins, so the ecom karigar join uses `lot_k2` to avoid collision.

---

### Fix 5: Composite Keys in Data Accumulation Loops (ret_reports_model.php)

Every `foreach` that accumulates query results must build composite keys for group_by 1 and 4:

#### Before (all queries)
```php
foreach ($rows as $r) {
    $data[$r['sub_pro_id']][$r['log_date']] = floatval($r['gwt']);
}
```

#### After
```php
foreach ($rows as $r) {
    $k = ($group_by == 1 || $group_by == 4) ? $r['sub_pro_id'] . '_' . $r['extra_dim'] : $r['sub_pro_id'];
    $data[$k][$r['log_date']] = floatval($r['gwt']);
}
```

For opening balance (no date dimension):
```php
// Before
$op_data[$r['sub_pro_id']] = floatval($r['gwt']);
// After
$k = ($group_by == 1 || $group_by == 4) ? $r['sub_pro_id'] . '_' . $r['extra_dim'] : $r['sub_pro_id'];
$op_data[$k] = floatval($r['gwt']);
```

---

### Fix 6: Composite Keys in Simulation Loop (ret_reports_model.php)

#### Before
```php
foreach ($skeleton as $row) {
    $key = $row['sub_pro_id'];
    // ...simulation...
    $sub_results[$key] = array(
        'sub_pro_id' => $key,
        // ...
    );
}
```

#### After
```php
foreach ($skeleton as $row) {
    // Build key: composite for group_by 1 (design) or 4 (karigar)
    if ($group_by == 1) {
        $key = $row['sub_pro_id'] . '_' . $row['design_id'];
    } elseif ($group_by == 4) {
        $key = $row['sub_pro_id'] . '_' . $row['id_karigar'];
    } else {
        $key = $row['sub_pro_id'];
    }
    // ...simulation unchanged...

    $result_row = array(
        'sub_pro_id'          => $row['sub_pro_id'],  // real ID, not composite
        'sub_product_name'    => $row['sub_product_name'],
        'parent_pro_id'       => $row['parent_pro_id'],
        'parent_product_name' => $row['parent_product_name'],
        'id_collection'       => $row['id_collection'],
        'collection_name'     => $row['collection_name'],
        'id_ret_category'     => $row['id_ret_category'],
        'category_name'       => $row['category_name'],
        'sold_gwt'            => round($total_sold, 3),
        'avg_stock_gwt'       => $avg_stock,
        'closing_wt'          => round($curr, 3),
        'inventory_ret'       => $inventory_ret,
        'inv_ret_per_year'    => $inv_ret_per_year,
    );
    // Carry extra dimension data through
    if ($group_by == 1) {
        $result_row['design_id']   = $row['design_id'];
        $result_row['design_name'] = $row['design_name'];
    } elseif ($group_by == 4) {
        $result_row['id_karigar']   = $row['id_karigar'];
        $result_row['karigar_name'] = $row['karigar_name'];
    }
    $sub_results[$key] = $result_row;
}
```

---

### Fix 7: 4-Level Hierarchy Builder (ret_reports_model.php)

Replace the existing 3-level hierarchy builder with a mode-aware builder:

```php
foreach ($sub_results as $item) {
    if ($group_by == 4) {
        // Karigar > Collection > Product > SubProduct (4-level)
        $top_key  = $item['id_karigar'];
        $top_name = $item['karigar_name'];
        // L1 init → L2 (collection) init → L3 (product) init → L4 leaf (sub-product)
        // Roll up: L4 → L3 → L2 → L1

    } elseif ($group_by == 1) {
        // Collection > Product > SubProduct > Design (4-level)
        $top_key  = $item['id_collection'];
        $top_name = $item['collection_name'];
        // L1 init → L2 (product) init → L3 (sub-product) init → L4 leaf (design)
        // Roll up: L4 → L3 → L2 → L1

    } elseif ($group_by == 2) {
        // Product > Collection > SubProduct (3-level — unchanged)

    } else {
        // Category > Product > SubProduct (3-level — unchanged)
    }

    // Roll up to top-level group (all modes)
    $hierarchy[$top_key]['sold_gwt']     += $item['sold_gwt'];
    $hierarchy[$top_key]['avg_stock_gwt'] += $item['avg_stock_gwt'];
    $hierarchy[$top_key]['closing_wt']   += $item['closing_wt'];
}
```

**Important**: The 4-level modes use keyed arrays (not `[]` append) for L2 and L3 `sub_products` to enable deduplication. The L4 `designs` array uses `[]` append since it's the leaf.

---

### Fix 8: Inventory Turns at All Depths (ret_reports_model.php)

```php
$has_4_levels = ($group_by == 1 || $group_by == 4);
foreach ($hierarchy as $gk => $grp) {
    foreach ($grp['products'] as $pid => $prod) {
        if ($has_4_levels && isset($prod['sub_products'])) {
            // Compute L3 turns from rolled-up L4 sums
            foreach ($prod['sub_products'] as $sid => $sub) {
                $sub['inventory_ret'] = ($sub['avg_stock_gwt'] > 0)
                    ? round($sub['sold_gwt'] / $sub['avg_stock_gwt'], 3) : 0;
                $sub['inv_ret_per_year'] = ($sub['avg_stock_gwt'] > 0)
                    ? round(($sub['inventory_ret'] / $stock_rotate_days) * 365, 4) : 0;
            }
        }
        // Compute L2 turns
        // ...
    }
    // Compute L1 turns
    // ...
}
```

---

### Fix 9: JS Flatten Loop — 4th Level Support (ret_reports.js)

#### Before
```javascript
subs.forEach(function (sub) {
    flatData.push({
        _level: 3, _sort: sortIdx++, _gid: gid, _pid: pid,
        // ...
    });
});
```

#### After
```javascript
var has4Levels = (groupBy == 1 || groupBy == 4);
// ...
var sIdx = 0;
subs.forEach(function (sub) {
    var sid = pid + '-' + sIdx++;
    flatData.push({
        _level: 3, _sort: sortIdx++, _gid: gid, _pid: pid, _sid: sid,
        // ...
    });

    // Level 4: designs (leaf) — only present in 4-level modes
    if (has4Levels && sub.designs) {
        sub.designs.forEach(function (des) {
            flatData.push({
                _level: 4, _sort: sortIdx++, _gid: gid, _pid: pid, _sid: sid,
                name: des.product_name || '-',
                // ...metrics...
            });
        });
    }
});
```

---

### Fix 10: JS Row Styling + 4-Level Drill-Down Events (ret_reports.js)

#### createdRow — add L3 expandable + L4 leaf styling:
```javascript
} else if (data._level === 3) {
    if (has4Levels) {
        $(row).css({ 'background-color': '#fdf6e3', 'font-weight': '500', 'cursor': 'pointer' });
        $(row).addClass('sr-sub-row sr-sub-expandable');
    } else {
        $(row).addClass('sr-sub-row');
    }
    $(row).hide();
} else if (data._level === 4) {
    $(row).addClass('sr-leaf-row');
    $(row).hide();
}
```

#### New L3→L4 click handler:
```javascript
$(document).on('click', '#sr_detail_table .sr-sub-expandable', function () {
    var $row = $(this);
    var sid = $row.data('sid');
    var isExpanded = $row.hasClass('sr-expanded');

    if (isExpanded) {
        $('#sr_detail_table .sr-leaf-row[data-sid="' + sid + '"]').hide();
        $row.removeClass('sr-expanded');
        $row.find('.sr-toggle-icon').css('transform', 'rotate(0deg)');
    } else {
        $('#sr_detail_table .sr-leaf-row[data-sid="' + sid + '"]').show();
        $row.addClass('sr-expanded');
        $row.find('.sr-toggle-icon').css('transform', 'rotate(90deg)');
    }
});
```

#### Update existing collapse cascades:
- L1 collapse must also hide `.sr-leaf-row[data-gid=...]`
- L2 collapse must also hide `.sr-leaf-row[data-pid=...]` and reset L3 `.sr-expanded`

---

### Fix 11: JS Group-By Dropdown — Karigar Option (ret_reports.js)

```javascript
} else if (val == '4') {
    // Karigar mode: show collection filter
    $('.sr_coll_filter').show();
    $('.sr_prod_filter').hide();
    $('#sr_detail_col_header').text('Karigar / Collection / Product');
}
```

---

## Verification

1. Navigate to **Stock Rotation Detail Report** (`/admin_ret_reports/stock_rotation_detail/list`)
2. Select a branch with known inventory

### Collection Mode (Group By = Collection)
3. Select group_by **Collection**, run report
4. Click a collection row → expands to **Products**
5. Click a product row → expands to **Sub-Products**
6. Click a sub-product row → expands to **Designs** (4th level)
7. Verify design-level metrics sum to the parent sub-product row
8. Collapse at L1 — all L2/L3/L4 rows hide + icons reset

### Karigar Mode (Group By = Karigar)
9. Switch to **Karigar** mode, run report
10. Top-level rows show karigar names
11. Click karigar → expands to **Collections**
12. Click collection → expands to **Products**
13. Click product → expands to **Sub-Products** (4th level)
14. Verify sub-product metrics sum to parent product row

### Regression Check (3-Level Modes)
15. Switch to **Product** mode — report behaves exactly as before (3-level)
16. Switch to **Category** mode — report behaves exactly as before (3-level)
17. No L3 toggle icons appear in 3-level modes

### Print/Export
18. Click Print — only L1 summary rows export
19. Click Excel — only L1 summary rows export
20. `php -l application/models/ret_reports_model.php` → No syntax errors

---

## Notes

### Hierarchy Modes Summary
| Group By | Value | Hierarchy | Levels |
|----------|-------|-----------|--------|
| Collection | 1 | Collection → Product → Sub-Product → **Design** | 4 |
| Product | 2 | Product → Collection → Sub-Product | 3 |
| Category | 3 | Category → Product → Sub-Product | 3 |
| Karigar | 4 | **Karigar** → Collection → Product → Sub-Product | 4 |

### Composite Key Pattern
```
key = sub_pro_id + '_' + extra_dim
```
Where `extra_dim` is `design_id` (group_by=1) or `gold_smith` (group_by=4). This ensures two designs within the same sub-product get separate simulation tracks.

### Join Alias Convention
| Query Context | Tag Alias | Lot Alias | Notes |
|---------------|-----------|-----------|-------|
| Common queries (1–5c) | `tag` | `lot_k` | Uses `$extra_data_join` |
| Ecom query (5d) | `t` | `lot_k2` | Built inline to avoid alias collision |

### Row Styling Convention
| Level | Background | Font | Class |
|-------|-----------|------|-------|
| L1 (Group) | `#d5f5e3` | bold | `sr-grp-row` |
| L2 (Mid) | `#f0f9ff` | 600 | `sr-prod-row` |
| L3 (Sub, 4-level) | `#fdf6e3` | 500 | `sr-sub-row sr-sub-expandable` |
| L3 (Sub, 3-level) | default | normal | `sr-sub-row` |
| L4 (Leaf) | default | normal | `sr-leaf-row` |

### Related Patterns
- Builds on top of **PAT-RPT-010** (`recipe_stock_rotation_detail_perf_and_accuracy.md`)
- The `ret_design_master` join pattern is used in tagging and catalog modules
- The `ret_lot_inwards → ret_karigar` join chain is used in lot QC and reorder reports
- The `_sid` data attribute for L3→L4 tracking follows the same pattern as `_pid` for L2→L3

### Future Maintenance
- If a new grouping dimension is added (e.g., "by Metal Type"), add a new `elseif ($group_by == N)` block to the extra-dimension variables, and a new branch in the hierarchy builder
- The ecom query will always need its own inline extra-dimension logic due to the different tag alias
- If `ret_design_master` schema changes (e.g., `design_no` renamed), update `$extra_skel_join`
