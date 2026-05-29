# Tagging Module — Cross-Module Map

> **Module**: Tagging
> **Last Updated**: 2026-02-24

---

## 1. Direct Model Dependencies (Loaded in Constructor)

| Model                     | Module Owner    | Why Tagging Uses It                               |
| ------------------------- | --------------- | ------------------------------------------------- |
| `ret_tag_model`           | Tagging         | Primary model — all tag CRUD                      |
| `admin_settings_model`    | Settings        | Access rights, profiles, day closing, branch data |
| `sms_model`               | SMS             | OTP message sending                               |
| `log_model`               | System          | Activity audit logging                            |
| `ret_brntransfer_model`   | Branch Transfer | Transfer code generation, cross-branch tag moves  |
| `ret_billing_model`       | Billing         | Check if tag is already billed/sold               |
| `ret_catalog_model`       | Catalog         | Product master references                         |
| `ret_metal_process_model` | Metal Process   | Metal processing linkage                          |
| `admin_usersms_model`     | User SMS        | User-level SMS features                           |

---

## 2. Outbound Dependencies (Tagging → Other Modules)

### Branch Transfer Module

- **When**: Tag created with different `to_branch` than `id_branch`
- **What**: Creates `ret_branch_transfer` + `ret_branch_transfer_items` records
- **Model Method**: `ret_brntransfer_model->trans_code_generator()`
- **Impact**: Tag appears in branch transfer pending list

### Billing Module

- **When**: Tag scan, tag details view
- **What**: Checks if tag is billed (sold), billing reference data
- **Model Method**: `ret_billing_model` (indirect reads)
- **Impact**: Tag status reflects sale status

### Settings Module

- **When**: Every page load, tag operations with OTP
- **What**: Access rights checking, branch-level settings, day closing dates
- **Model Methods**: `get_access()`, `profileDB()`, `getBranchDayClosingData()`
- **Impact**: Controls which features are visible per user role

### Lot Inwards Module

- **When**: Tag creation, tag edit, tag delete
- **What**: Deducts/restores lot inward balance when tags are created/deleted
- **Tables**: `ret_lot_inwards`, `ret_lot_inward_detail`
- **Impact**: Lot balance tracking depends on accurate tag operations

### Estimation Module (R5 NEW)

- **When**: Lot design search, employee lookup
- **What**: Calls `admin_ret_estimation/get_employee` (L12892) and `admin_ret_estimation/getProductDesignBySearch` (L14604) for design-by-product search and employee data
- **Model Method**: Indirect via AJAX
- **Impact**: Shared employee lookup between Tagging and Estimation — inconsistent data if models differ

### Billing Module (JS-level, R5 NEW)

- **When**: Tax group item loading
- **What**: Calls `admin_ret_billing/getAllTaxgroupItems` (L16589) to populate tax group dropdown
- **Model Method**: Indirect via AJAX
- **Impact**: Tax group data comes from Billing module — if Billing's tax structure changes, Tagging forms break

### Generic / Legacy Controllers (R5 NEW)

- **When**: Initial form load for diamond/stone tagging
- **What**: Calls bare `get/` controller for `active_color`, `active_purity`, `active_masters`, `metal_info_list`, `active_metals`
- **Also**: Calls legacy `product/delect_prodDetail` and `admin_catalog/remove_img`
- **Impact**: ⚠️ These may be dead code if the `get/` controller doesn't exist in current CI3 routing

---

## 3. Inbound Dependencies (Other Modules → Tagging)

### Billing Module → Tagging

- **Purpose**: Reads tag details for sale transactions
- **Tables Read**: `ret_taging`, `ret_taging_stone`, `ret_taging_material`
- **Key Fields**: `tag_code`, `tag_status`, `product_id`, `net_wt`, `gross_wt`, `purity`, `sales_value`, `tag_mc_type`, `tag_mc_value`
- **Status Change**: Billing sets `tag_status = 1` (Sold Out) after sale

### Branch Transfer Module → Tagging

- **Purpose**: Reads/updates tag branch info during transfers
- **Tables Modified**: `ret_taging.current_branch`, `ret_taging.tag_status` (4 = In Transit)
- **Impact**: Branch transfer changes tag's physical location

### Reports Module → Tagging

- **Purpose**: Stock reports, aging reports, category-wise reports
- **Tables Read**: `ret_taging` (all columns), joins with product/design/purity masters
- **Key Queries**: Stock detail, stock summary, age analysis, collection reports

### Estimation Module → Tagging

- **Purpose**: May link estimates to specific tags
- **Tables Read**: `ret_taging` for tag lookup in estimation flow
- **Key Field**: `id_orderdetails` links tag to customer order

### Metal Process Module → Tagging

- **Purpose**: Tags involved in melting/processing operations
- **Tables Modified**: `ret_taging.tag_status` (17 = Metal Issue)

### Customer Order Module → Tagging

- **Purpose**: Link customer orders to specific tags
- **Tables Used**: `ret_taging.id_orderdetails`, `ret_customer_order_details`
- **Impact**: Order-tag linking affects order fulfillment tracking

---

## 4. Shared Database Tables

| Table                        | Owner Module    | How Tagging Uses It     | Risk Level                                     |
| ---------------------------- | --------------- | ----------------------- | ---------------------------------------------- |
| `ret_lot_inwards`            | Lot Inward      | R/W — balance deduction | **HIGH** — incorrect deduction = phantom stock |
| `ret_lot_inward_detail`      | Lot Inward      | R/W — item balance      | **HIGH** — same as above                       |
| `ret_branch_transfer`        | Branch Transfer | W — creates transfers   | MEDIUM                                         |
| `ret_branch_transfer_items`  | Branch Transfer | W — transfer items      | MEDIUM                                         |
| `ret_product_master`         | Catalog         | R — product info        | LOW                                            |
| `ret_design_master`          | Catalog         | R — design info         | LOW                                            |
| `ret_purity`                 | Settings        | R — purity lookup       | LOW                                            |
| `ret_stone`                  | Settings        | R — stone master        | LOW                                            |
| `ret_karigar`                | Vendor          | R — vendor info         | LOW                                            |
| `branch`                     | System          | R — branch info         | LOW                                            |
| `employee`                   | System          | R — employee info       | LOW                                            |
| `company`                    | System          | R — company info        | LOW                                            |
| `ret_customer_order_details` | Orders          | R/W — tag linking       | MEDIUM                                         |
| `ret_non_tag_stock`          | Stock           | R/W — non-tag balance   | **HIGH**                                       |
| `ret_metal_rate`             | Settings        | R — metal rates         | LOW                                            |
| `ret_selling_settings`       | Settings        | R — wastage/MC config   | LOW                                            |
| `ret_purchase_order`         | Purchase        | R — PO details          | LOW                                            |
| `ret_purchase_order_items`   | Purchase        | R — PO items            | LOW                                            |
| `ret_tax_group`              | Billing/Tax     | R — tax group data      | LOW (via AJAX to billing)                      |
| `ret_color`                  | Settings        | R — color master        | LOW (via generic `get/` controller)            |

---

## 5. Event-Driven Cross-Module Effects

| Event                 | Trigger               | Cross-Module Effect                                       |
| --------------------- | --------------------- | --------------------------------------------------------- |
| **Tag Created**       | `tagging('save')`     | Lot balance decremented, BT record if cross-branch        |
| **Tag Deleted**       | `verify_otp()`        | Lot balance restored, non-tag stock restored              |
| **Tag Sold**          | Billing module        | `tag_status = 1`, not reversible from tagging             |
| **Tag Transferred**   | BT module             | `current_branch` changed, `tag_status = 4` during transit |
| **Tag Retagged**      | `create_retag()`      | Old tag `tag_status = 3`, new lot created                 |
| **Tag Status Change** | Various               | Section log entry created, affects stock reports          |
| **Order Linked**      | `update_order_link()` | `id_orderdetails` set, order marked as linked             |
| **Order Unlinked**    | `unlink_order_tags()` | `id_orderdetails` nulled, order marked as unlinked        |

---

## 6. Critical Integration Points

### ⚠️ High-Risk Interactions

1. **Lot Balance Deduction** (Tag Create/Delete)
    - Creates/deletes a tag → must accurately adjust lot_inward_detail piece/weight balances
    - Bug here = phantom inventory or missing stock
2. **Non-Tag Stock** (Retag Non-Tag)
    - Retagging non-tag items updates `ret_non_tag_stock`
    - Wrong arithmetic direction = stock discrepancy

3. **Tag Status Transitions**
    - Multiple modules change `tag_status` (Billing, BT, Metal Process, Tagging itself)
    - No centralized state machine — each module writes directly
    - Risk of status conflicts (e.g., tag in transit but marked sold)

4. **Branch Transfer Integration**
    - Tag creation can auto-create a branch transfer
    - If BT creation fails after tag is created = orphaned tag at wrong branch
