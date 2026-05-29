# Workflow 3: Save Estimation

> **Combined Swimlane + Hierarchical Documentation**  
> Entry Point: User clicks "Save" button → POST to `/admin_ret_estimation/estimation/save`

---

## 🎯 Master Overview

```mermaid
graph TB
    subgraph "Save Estimation Flow"
        A["📤 Form Submit"] --> B{"✓ Pre-Validate"}
        B -->|Fail| X["❌ Error Response"]
        B -->|Pass| C["🔒 Begin Transaction"]
        C --> D["📋 Insert Header"]
        D --> E["🏷️ Insert Items Loop"]
        E --> F["💎 Insert Child Data"]
        F --> G["💰 Insert Old Metal"]
        G --> H["🎁 Insert Chit/Voucher"]
        H --> I{"✓ Trans Status"}
        I -->|OK| J["✅ Commit"]
        I -->|Fail| K["🔄 Rollback"]
        J --> L["📝 Log & Return"]
    end

    D -.->|Detail 3.1| D1["Header Insert"]
    E -.->|Detail 3.2| E1["Items Loop"]
    F -.->|Detail 3.3| F1["Child Tables"]
    G -.->|Detail 3.4| G1["Old Metal"]

    style A fill:#e3f2fd
    style J fill:#c8e6c9
    style K fill:#ffcdd2
    style X fill:#ffcdd2
```

---

## 📊 Complete Swimlane Diagram

```mermaid
sequenceDiagram
    box rgb(227,242,253) Frontend
    participant F as 📝 Form
    participant JS as ⚡ JavaScript
    end
    box rgb(255,243,224) Backend
    participant C as 🎮 Controller
    end
    box rgb(232,245,233) Database
    participant DB as 🗄️ Database
    end

    F->>JS: Submit button click
    JS->>JS: Validate form fields
    JS->>C: POST /estimation/save

    rect rgb(255,248,225)
    Note over C: 📋 PHASE 1: Pre-Validation
    C->>C: Get day closing data
    C->>C: Check tax_price not empty
    alt Day closing missing OR tax invalid
        C-->>JS: {status: false, message}
    end
    end

    rect rgb(232,245,233)
    Note over C,DB: 📋 PHASE 2: Begin Transaction
    C->>DB: trans_begin()
    end

    rect rgb(225,245,254)
    Note over C,DB: 📋 PHASE 3: Insert Header
    C->>DB: INSERT ret_estimation
    DB-->>C: estimation_id = $insId
    end

    rect rgb(243,229,245)
    Note over C,DB: 📋 PHASE 4: Insert Items Loop
    loop Each est_tag item
        C->>DB: INSERT ret_estimation_items
        DB-->>C: est_item_id
        C->>DB: INSERT ret_estimation_item_stones
        C->>DB: INSERT ret_est_other_metals
        C->>DB: INSERT ret_estimation_other_charges
        opt Has child tags (merged)
            C->>DB: INSERT ret_estimation_items (child)
            C->>DB: INSERT ret_est_tag_merge
        end
    end
    loop Each est_catalog item
        C->>DB: INSERT ret_estimation_items
        C->>DB: INSERT stones/materials/charges
    end
    loop Each est_custom item
        C->>DB: INSERT ret_estimation_items
        C->>DB: INSERT stones/materials
    end
    loop Each order item
        C->>DB: INSERT ret_estimation_items
        C->>DB: UPDATE customerorder.est_id
    end
    end

    rect rgb(255,243,224)
    Note over C,DB: 📋 PHASE 5: Old Metal & Extras
    loop Each old_metal item
        C->>DB: INSERT ret_estimation_old_metal_sale_details
        C->>DB: INSERT ret_esti_old_metal_stone_details
    end
    C->>DB: INSERT ret_estimation_other_inventory_issue
    C->>DB: INSERT ret_est_gift_voucher_details
    C->>DB: INSERT ret_est_chit_utilization
    C->>DB: INSERT ret_est_sales_return_utilization
    end

    rect rgb(232,245,233)
    Note over C,DB: 📋 PHASE 6: Commit/Rollback
    C->>DB: Check trans_status()
    alt Status OK
        C->>DB: trans_commit()
        C->>DB: INSERT log_detail
        C-->>JS: {status: true, id: $insId}
    else Status Fail
        C->>DB: trans_rollback()
        C-->>JS: {status: false, message}
    end
    end

    JS-->>F: Redirect to list/print
```

---

## 📋 Detail 3.1: Header Insert

### Sequence

```mermaid
sequenceDiagram
    participant C as Controller
    participant M as Model
    participant DB as Database

    C->>C: Extract $_POST['estimation']
    C->>M: getBranchDayClosingData(branch_id)
    M->>DB: SELECT entry_date FROM day_closing
    DB-->>M: dCData

    C->>M: get_FinancialYear()
    M->>DB: SELECT * FROM ret_financial_year<br/>WHERE status=1
    DB-->>M: fin_year

    C->>M: generateEstiNo(date, branch)
    M->>DB: SELECT MAX(esti_no) FROM ret_estimation<br/>WHERE date = ? AND branch = ?
    DB-->>M: next_esti_no

    C->>C: Build header array
    C->>M: insertData(data, 'ret_estimation')
    M->>DB: INSERT INTO ret_estimation (...)
    DB-->>M: estimation_id
    M-->>C: $insId
```

### Header Data Array

```php
$data = array(
    'estimation_datetime' => $estimation_datetime,  // from day_closing
    'fin_year_code'       => $fin_year['fin_year_code'],
    'esti_no'             => $model->generateEstiNo($date, $branch),
    'esti_for'            => $addData['esti_for'],      // 1=Customer, 2=Branch, 3=Company
    'cus_id'              => $addData['cus_id'],
    'disc_per'            => $addData['discount'],
    'bulk_was_disc_per'   => $addData['blk_discount'],
    'gift_voucher_amt'    => $addData['gift_voucher_amt'],
    'total_cost'          => $addData['total_cost'],
    'est_date'            => date($estimation_datetime),
    'created_time'        => date("Y-m-d H:i:s"),
    'created_by'          => $addData['created_by'],
    'id_branch'           => $addData['id_branch'],
    'is_eda'              => $addData['is_eda'],
    'goldrate_22ct'       => $addData['goldrate_22ct'],
    'silverrate_1gm'      => $addData['silverrate_1gm'],
    'goldrate_18ct'       => $addData['goldrate_18ct'],
    'manual_rate'         => $addData['manual_rate'],
);

// INSERT → ret_estimation
// Returns: estimation_id
```

---

## 📋 Detail 3.2: Items Loop

### Flowchart

```mermaid
flowchart TD
    A[Start Items] --> B{est_tag exists?}
    B -->|Yes| C[Loop est_tag]
    B -->|No| D{order exists?}

    C --> C1[Build item array]
    C1 --> C2[INSERT ret_estimation_items]
    C2 --> C3{stone_details?}
    C3 -->|Yes| C4[INSERT ret_estimation_item_stones]
    C3 -->|No| C5{child_tag_details?}
    C4 --> C5
    C5 -->|Yes| C6[INSERT child items + ret_est_tag_merge]
    C5 -->|No| C7{other_metal_details?}
    C6 --> C7
    C7 -->|Yes| C8[INSERT ret_est_other_metals]
    C7 -->|No| C9{charges?}
    C8 --> C9
    C9 -->|Yes| C10[INSERT ret_estimation_other_charges]
    C9 -->|No| C11[Next tag]
    C10 --> C11
    C11 --> B

    D -->|Yes| E[Loop order items]
    D -->|No| F{est_catalog?}
    E --> E1[INSERT ret_estimation_items item_type=3]
    E1 --> E2[UPDATE customerorder.est_id]
    E2 --> F

    F -->|Yes| G[Loop catalog items]
    F -->|No| H{est_custom?}
    G --> G1[INSERT ret_estimation_items item_type=1]
    G1 --> G2[INSERT stones/materials/charges]
    G2 --> H

    H -->|Yes| I[Loop custom items]
    H -->|No| J[Done Items]
    I --> I1[INSERT ret_estimation_items item_type=2]
    I1 --> I2[INSERT stones/materials]
    I2 --> J
```

### Item Types

| item_type | Source           | Array Key     | Description               |
| --------- | ---------------- | ------------- | ------------------------- |
| **0**     | Tagged Item      | `est_tag`     | Items from `ret_taging`   |
| **1**     | Catalog/Non-Tag  | `est_catalog` | From `ret_nontagginglots` |
| **2**     | Custom/Home Bill | `est_custom`  | Manual entry              |
| **3**     | Order Item       | `order`       | From `customerorder`      |

### Tag Item Insert Structure

```php
$arrayEstTags = array(
    'esti_id'              => $insId,           // FK to ret_estimation
    'tag_id'               => $estTag['tag_id'][$key],
    'item_type'            => 0,                // Tagged item
    'product_id'           => $estTag['pro_id'][$key],
    'design_id'            => $estTag['design_id'][$key],
    'id_sub_design'        => $estTag['id_sub_design'][$key],
    'purity'               => $estTag['purity'][$key],
    'size'                 => $estTag['size'][$key],
    'piece'                => $estTag['piece'][$key],
    'less_wt'              => $estTag['lwt'][$key],
    'net_wt'               => $estTag['nwt'][$key],
    'gross_wt'             => $estTag['gwt'][$key],
    'calculation_based_on' => $estTag['caltype'][$key],
    'wastage_percent'      => $estTag['wastage'][$key],
    'mc_value'             => $estTag['mc'][$key],
    'mc_type'              => $estTag['id_mc_type'][$key],
    'item_cost'            => $estTag['cost'][$key],
    'item_total_tax'       => $estTag['tax_price'][$key],
    'est_rate_per_grm'     => $estTag['est_rate_per_grm'][$key],
    'is_partial'           => $estTag['is_partial'][$key],
    'item_emp_id'          => $estTag['item_emp_id'][$key],
    'discount'             => $estTag['discount_amount'][$key],
    'stone_amount'         => $estTag['stone_amt'][$key],
    // ... more fields
);

// INSERT → ret_estimation_items
// Returns: est_item_id (used for child tables)
```

---

## 📋 Detail 3.3: Child Tables Insert

### Sequence

```mermaid
sequenceDiagram
    participant C as Controller
    participant DB as Database

    Note over C: After each item INSERT

    rect rgb(255,248,225)
    Note over C,DB: Stone Details
    C->>C: JSON decode stone_details
    loop Each stone
        C->>DB: INSERT ret_estimation_item_stones<br/>{est_id, est_item_id, stone_id, pieces, wt, price, ...}
    end
    end

    rect rgb(225,245,254)
    Note over C,DB: Merged Tags (Child)
    C->>C: JSON decode child_tag_details
    loop Each child tag
        C->>DB: INSERT ret_estimation_items<br/>(istag_merged = 2)
        DB-->>C: child_est_item_id
        C->>DB: INSERT ret_est_tag_merge<br/>{est_item_id: parent, ref_est_item_id: child}
        C->>DB: UPDATE parent item<br/>SET istag_merged = 1
    end
    end

    rect rgb(232,245,233)
    Note over C,DB: Other Metals
    C->>C: JSON decode other_metal_details
    loop Each other metal
        C->>DB: INSERT ret_est_other_metals<br/>{est_item_id, metal_id, weight, rate, amount, ...}
    end
    end

    rect rgb(243,229,245)
    Note over C,DB: Other Charges
    C->>C: Get charges from tag
    loop Each charge
        C->>DB: INSERT ret_estimation_other_charges<br/>{est_item_id, id_charge, amount}
    end
    end

    rect rgb(255,243,224)
    Note over C,DB: Materials (Catalog/Custom only)
    C->>C: JSON decode material_details
    loop Each material
        C->>DB: INSERT ret_estimation_item_other_materials<br/>{est_id, est_item_id, material_id, wt, price}
    end
    end
```

### Child Table Relationships

```
ret_estimation_items (est_item_id)
    ├── ret_estimation_item_stones (FK: est_item_id)
    ├── ret_est_other_metals (FK: est_item_id)
    ├── ret_estimation_other_charges (FK: est_item_id)
    ├── ret_estimation_item_other_materials (FK: est_item_id)
    └── ret_est_tag_merge (FK: est_item_id → ref_est_item_id)
```

---

## 📋 Detail 3.4: Old Metal & Extras

### Sequence

```mermaid
sequenceDiagram
    participant C as Controller
    participant DB as Database

    rect rgb(255,248,225)
    Note over C,DB: Old Metal Purchase
    loop Each est_oldmatel item
        C->>DB: INSERT ret_estimation_old_metal_sale_details<br/>{est_id, id_category, gross_wt, net_wt,<br/>touch, purity, rate_per_gram, amount, purpose}
        DB-->>C: old_metal_sale_id
        opt Has stones
            loop Each stone
                C->>DB: INSERT ret_esti_old_metal_stone_details
            end
        end
    end
    end

    rect rgb(225,245,254)
    Note over C,DB: Other Inventory Issue
    loop Each est_oth_inv item
        C->>DB: INSERT ret_estimation_other_inventory_issue<br/>{esti_id, id_other_item, no_of_piece}
    end
    end

    rect rgb(232,245,233)
    Note over C,DB: Gift Vouchers
    loop Each gift_voucher
        C->>DB: INSERT ret_est_gift_voucher_details<br/>{est_id, voucher_no, gift_voucher_amt}
    end
    end

    rect rgb(243,229,245)
    Note over C,DB: Chit Scheme Utilization
    loop Each chit_uti
        C->>DB: INSERT ret_est_chit_utilization<br/>{est_id, scheme_account_id, utl_amount,<br/>closing_weight, savings_in_wastage, savings_in_making_charge}
    end
    end

    rect rgb(255,243,224)
    Note over C,DB: Sales Return Utilization
    loop Each sales_ret_uti
        C->>DB: INSERT ret_est_sales_return_utilization<br/>{est_id, bill_id, bill_det_id}
    end
    end
```

---

## 📋 Transaction Handling

### Pseudo-code

```
FUNCTION estimation_save():

    // PHASE 1: Pre-validation
    dCData = model.getBranchDayClosingData(branch_id)
    fin_year = model.get_FinancialYear()

    IF dCData is empty:
        RETURN {status: false, message: "Day closing required"}

    // Check all tag prices have tax calculated
    FOR EACH tag IN est_tag:
        IF tag.tax_price is empty:
            RETURN {status: false, message: "GST info wrong"}

    // PHASE 2: Begin Transaction
    db.trans_begin()

    // PHASE 3: Insert Header
    estimation_id = model.insertData(headerData, 'ret_estimation')

    IF estimation_id:

        // PHASE 4: Insert Tag Items
        FOR EACH tag IN est_tag:
            item_id = model.insertData(tagData, 'ret_estimation_items')

            IF tag.stone_details:
                FOR EACH stone: INSERT ret_estimation_item_stones

            IF tag.child_tag_details:
                FOR EACH child:
                    child_id = INSERT ret_estimation_items (merged)
                    INSERT ret_est_tag_merge (parent → child)
                    UPDATE parent.istag_merged = 1

            IF tag.other_metal_details:
                FOR EACH metal: INSERT ret_est_other_metals

            IF tag.charges:
                FOR EACH charge: INSERT ret_estimation_other_charges

        // Insert Order Items (item_type = 3)
        FOR EACH order:
            INSERT ret_estimation_items
            UPDATE customerorder.est_id

        // Insert Catalog Items (item_type = 1)
        FOR EACH catalog:
            INSERT ret_estimation_items + stones/materials/charges

        // Insert Custom Items (item_type = 2)
        FOR EACH custom:
            INSERT ret_estimation_items + stones/materials

        // PHASE 5: Insert Old Metal
        FOR EACH old_metal:
            INSERT ret_estimation_old_metal_sale_details + stones

        // Insert Other Inventory
        FOR EACH oth_inv:
            INSERT ret_estimation_other_inventory_issue

        // Insert Gift Vouchers
        FOR EACH voucher:
            INSERT ret_est_gift_voucher_details

        // Insert Chit Utilization
        FOR EACH chit:
            INSERT ret_est_chit_utilization

        // Insert Sales Return
        FOR EACH sales_ret:
            INSERT ret_est_sales_return_utilization

    // PHASE 6: Commit/Rollback
    IF db.trans_status() === TRUE:
        db.trans_commit()
        log_model.log_detail('Add Estimation', estimation_id)
        flash_message('Estimation added successfully')
        RETURN {status: true, id: estimation_id}
    ELSE:
        db.trans_rollback()
        flash_message('Unable to proceed')
        RETURN {status: false}

END FUNCTION
```

---

## 🔗 Tables Affected

| Table                                   | Insert Count  | Purpose                 |
| --------------------------------------- | ------------- | ----------------------- |
| `ret_estimation`                        | 1             | Header record           |
| `ret_estimation_items`                  | N × items     | Line items (all types)  |
| `ret_estimation_item_stones`            | N × stones    | Stone details per item  |
| `ret_est_other_metals`                  | N × metals    | Other metal components  |
| `ret_estimation_other_charges`          | N × charges   | Additional charges      |
| `ret_estimation_item_other_materials`   | N × materials | Other materials         |
| `ret_est_tag_merge`                     | N × merged    | Tag merge relationships |
| `ret_estimation_old_metal_sale_details` | N × old_metal | Old metal purchase      |
| `ret_esti_old_metal_stone_details`      | N × stones    | Stones in old metal     |
| `ret_estimation_other_inventory_issue`  | N × inventory | Other inventory         |
| `ret_est_gift_voucher_details`          | N × vouchers  | Gift vouchers           |
| `ret_est_chit_utilization`              | N × chit      | Chit scheme usage       |
| `ret_est_sales_return_utilization`      | N × returns   | Sales returns           |

---

## 🔗 Collision Points

| This Workflow   | Shares With       | Component                     |
| --------------- | ----------------- | ----------------------------- |
| Save Estimation | Create Estimation | Settings, financial year      |
| Save Estimation | Add Tagged Item   | Item data structure           |
| Save Estimation | Convert to Bill   | Same item structure → billing |
| Save Estimation | Edit Estimation   | Same save logic with UPDATE   |

---

## ✅ Workflow Complete Checklist

- [x] Master overview diagram
- [x] Full swimlane sequence
- [x] Header insert detail (3.1)
- [x] Items loop detail (3.2)
- [x] Child tables detail (3.3)
- [x] Old metal & extras detail (3.4)
- [x] Transaction handling pseudo-code
- [x] Tables affected list

---

**Ready for Workflow 4: Add Old Metal Purchase?**
