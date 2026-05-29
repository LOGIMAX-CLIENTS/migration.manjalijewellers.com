# Cross-Module Map — Old Metal Process Module

## External Tables Read

| External Table | Module Owner | Used By | Purpose |
|---|---|---|---|
| `ret_billing` | Billing | `get_metal_stock_list()` | Source bill for old metal collection |
| `ret_bill_old_metal_sale_details` | Billing | `old_metal_bill_details()` | Old metal line items from bills |
| `ret_bill_details` | Billing | `get_sales_ret_details()` | Bill details for tagged item tracing |
| `ret_estimation_old_metal_sale_details` | Estimation | `old_metal_bill_details()` | Metal type, category per estimate |
| `ret_estimation` | Estimation | `old_metal_bill_details()` | Estimate header |
| `ret_taging` | Inventory/Tagging | `get_melting_tag_details()` | Tag status, weight, process state |
| `ret_taging_stone` | Inventory/Tagging | Tag-based pocket queries | Diamond weight per tag |
| `ret_product_master` | Catalog | `get_sales_ret_details()` | Product → category mapping |
| `ret_category` | Catalog | Multiple | Category details, metal mapping |
| `ret_old_metal_type` | Catalog | Reference lookups | Gold/Silver metal type |
| `ret_old_metal_category` | Catalog | `old_metal_bill_details()` | Old metal category cross-ref |
| `metal` | Catalog | Partly-sale queries | Metal type definitions |
| `ret_purity` | Catalog | `get_partly_sale_details()` | Purity percentage |
| `ret_karigar` | Catalog | All process queries | Karigar name, state |
| `ret_partlysold` | Sales | `get_partly_sale_details()` | Partial item sale records |
| `ret_acc_stock_process` | Stock Process | Gross-wt net calculations | Stock adjustment process |
| `ret_acc_stock_process_details` | Stock Process | re-tagging weight deductions | Returned/retagged weights |
| `ret_acc_stock_process_stone_details` | Stock Process | Diamond wt adjustments | Stone-level adjustments |
| `ret_purchase_items_log` | Purchase | `get_old_metal_details()` | Purchase receipt log for metal |
| `smith_company_op_balance` | Settings | `get_opening_metal_stock_list()` | Opening stock for against-opening pocket |
| `branch` | Settings | `get_headOffice()` | Head office branch lookup |
| `country`, `state`, `city` | Settings | `get_metal_process()` | Karigar geo details in acknowledgement |

---

## External Tables Written

| External Table | Written When | What's Written |
|---|---|---|
| `ret_taging` | Pocket save (trans_type=2) | `tag_process=1` (marks pocketed) |
| `ret_bill_old_metal_sale_details` | Pocket save (trans_type=1) | `is_pocketed=1` |
| `ret_nontag_item` | All receipts (non-tag items) | +gross_wt, +net_wt, +no_of_piece |
| `ret_nontag_item_log` | All receipts (non-tag items) | Movement log entry |
| `ret_section_nontag_item_log` | All receipts (non-tag items) | Section-level log entry |
| `ret_lot_inwards` | Melting/Testing/Polishing receipt | Lot header (lot_from=2/4/5) |
| `ret_lot_inwards_detail` | Melting/Testing/Polishing receipt | Lot line items |
| `ret_purchase_item_stock_summary` | Receipt (stone/pur items) | +gross_wt, +net_wt via updateStockItemData / updateStoneItemData / updatePurItemData |

---

## Upstream Dependencies (What OMP reads that other modules create)

```
ret_billing → created by: Billing module
   └─ ret_bill_old_metal_sale_details → linked to billing at bill creation
   └─ ret_bill_details → linked to billing
ret_taging → created by: Tagging / Inventory module
ret_ret_partlysold → created by: Sales module (partial sales)
ret_karigar → created by: Catalog/Masters
ret_category → created by: Catalog/Masters
```

---

## Downstream Consumers (What reads OMP's outputs)

| OMP Output Table | Consumer Module | How Used |
|---|---|---|
| `ret_lot_inwards` | Lot / Inventory | Stock inward processing |
| `ret_lot_inwards_detail` | Lot / Inventory | Lot item breakdown |
| `ret_nontag_item` | Reports, Inventory | Current non-tag stock levels |
| `ret_nontag_item_log` | Reports | Movement audit trail |
| `ret_old_metal_pocket` | Reports, Customer dashboard | Pocket status |
| `ret_old_metal_process` | Reports, Acknowledgements | Process header |

---

## Integration Risk Points

| Risk | Description |
|---|---|
| `ret_taging.tag_process` | Set to 1 at pocket creation. If pocket is deleted (not implemented), tag stays "pocketed" permanently |
| `ret_bill_old_metal_sale_details.is_pocketed` | Set to 1; no rollback mechanism if pocket is cancelled |
| `ret_lot_inwards` duplication | Multiple receipts for the same melting could create multiple lot records without guards |
| `ret_nontag_item` arithmetic | Uses `+`/`-` arithmetic updates (non-atomic in multi-user); could drift under concurrent saves |
| Lot `lot_from` values | 2=Melting, 4=Testing, 5=Polishing — no value for Refining. Lot is not created for refining receipt |

---

## Catalog/Settings Cross-References

| AJAX Call | External Controller | Data Fetched |
|---|---|---|
| `admin_ret_catalog/karigar/active_list` | Catalog controller | Karigar dropdown |
| `admin_ret_catalog/category/active_category` | Catalog controller | Category list |
| `admin_ret_catalog/ret_product/active_metal` | Catalog controller | Metal types |
| `admin_ret_catalog/get_sectionBranchwise` | Catalog controller | Sections by branch |
| `admin_ret_catalog/get_ActiveProducts` | Catalog controller | Product dropdown (JS L4570) |
| `admin_ret_catalog/get_active_design_products` | Catalog controller | Design dropdown (JS L4621) |
| `admin_ret_catalog/get_ActiveSubDesigns` | Catalog controller | Sub-design per row (JS L389, L4658) |
| `admin_ret_catalog/category/cat_purity` | Catalog controller | Category-purity for report filter (JS L3786) |

---

## JS-Level AJAX Dependencies (R17 Discovery)

> These cross-module AJAX calls exist **only at the JS layer**. They were not detectable from PHP-level code inspection. Discovered in R17 full JS AJAX scan.

| AJAX Call (JS Line) | External Controller | Purpose | Risk |
|---|---|---|---|
| `admin_ret_reports/get_old_metal_type` (L314) | Reports controller | Metal type dropdown in pocket form & report filter | ⚠️ If Reports controller is modified, OMP pocket form breaks |
| `admin_ret_reports/get_ActiveProduct` (L344, L3751) | Reports controller | Product list used in OMP forms (called twice) | ⚠️ Duplicate call; using Reports route instead of Catalog |
| `admin_ret_brntransfer/branch_transfer/getTagsByFilter` (L4439) | Branch Transfer controller | Tag search for tagged-item pocket entry | ⚠️ OMP pocket form depends on Branch Transfer module — not documented anywhere in PHP |
| `admin_ret_purchase/karigarmaterialissue/available_stock_details` (L4711) | Purchase controller | Karigar issue stock check in pocket form | ⚠️ OMP depends on Purchase module at JS level only |

---

## Shared Table Schema Notes

### `ret_lot_inwards` (key integration table)
```
id_lot_inwards       PK
lot_no               VARCHAR (auto-generated)
lot_date             DATE
lot_from             INT (2=Melt, 4=Test, 5=Polish)
lot_type             INT (always 1)
id_branch            INT FK
created_by           INT FK
narration            TEXT
```

### `ret_nontag_item` (stock summary, updated arithmetically)
```
id_nontag_item       PK
id_section           INT FK
id_product           INT FK
id_design            INT FK (stored as design alias)
id_sub_design        INT FK
id_branch            INT FK
gross_wt             DECIMAL (summed from all receipts)
net_wt               DECIMAL
no_of_piece          INT
```
