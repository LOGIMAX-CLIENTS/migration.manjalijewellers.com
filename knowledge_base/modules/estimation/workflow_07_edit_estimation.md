# Workflow 7: Edit Estimation

> **Combined Swimlane + Hierarchical Documentation**  
> Entry Point: User clicks "Edit" on estimation list → modifies → saves

---

## 🎯 Master Overview

```mermaid
graph LR
    subgraph "Edit Estimation Flow"
        A["📋 Load Edit Form"] --> B["🔍 Fetch Existing"]
        B --> C["📝 Populate Form"]
        C --> D["✏️ User Modifies"]
        D --> E["💾 Submit Update"]
        E --> F{"Billed?"}
        F -->|Yes| X["❌ Cannot Edit"]
        F -->|No| G["🗑️ DELETE All Items"]
        G --> H["➕ INSERT Fresh"]
    end
    H --> I[✅ Done]

    B -.->|Detail 7.1| B1["Fetch Data"]
    G -.->|Detail 7.2| G1["Delete Pattern"]
    H -.->|Detail 7.3| H1["Re-Insert"]

    style A fill:#e3f2fd
    style I fill:#c8e6c9
    style X fill:#ffcdd2
```

---

## ⚠️ Critical Pattern: Delete + Insert

> [!IMPORTANT]
> **Edit does NOT use SQL UPDATE for items!**  
> Instead, it **DELETES all child records** and **RE-INSERTS** them fresh.

This is **intentional** because:

1. Items can be added/removed during edit
2. Child tables (stones, materials) would need complex diff logic
3. Delete + Insert is simpler and atomic

---

## 📊 Complete Swimlane Diagram

```mermaid
sequenceDiagram
    box rgb(227,242,253) Frontend
    participant U as 👤 User
    participant F as 📝 Form
    end
    box rgb(255,243,224) Backend
    participant C as 🎮 Controller
    end
    box rgb(232,245,233) Database
    participant DB as 🗄️ Database
    end

    U->>C: GET /estimation/edit/{id}

    rect rgb(255,248,225)
    Note over C,DB: 📋 PHASE 1: Load Form + Fetch
    C->>DB: SELECT * FROM ret_estimation
    C->>DB: Call getOtherEstimateItemsDetails(id)
    DB-->>C: Estimation + all items
    C-->>F: Render form with data
    end

    F->>F: Populate all fields
    F->>C: AJAX est_edit (get item details)

    rect rgb(225,245,254)
    Note over C,DB: 📋 PHASE 2: Fetch Item Details
    C->>DB: get_est_tag_details(id)
    C->>DB: est_non_tag_items(id)
    C->>DB: est_home_bill(id)
    C->>DB: get_chit_details(id)
    C->>DB: old_Metal(id)
    DB-->>C: All items with child data
    C-->>F: JSON response
    end

    F->>F: Build item rows
    U->>F: Modify items (add/remove/update)
    U->>F: Click Save
    F->>C: POST /estimation/update/{id}

    rect rgb(255,243,224)
    Note over C: 📋 PHASE 3: Pre-Check
    C->>C: Check estbillid is empty
    alt Already Billed
        C-->>F: Error "Cannot edit billed estimation"
    end
    end

    rect rgb(243,229,245)
    Note over C,DB: 📋 PHASE 4: Begin Transaction
    C->>DB: trans_begin()
    C->>DB: UPDATE ret_estimation (header only)
    end

    rect rgb(255,204,204)
    Note over C,DB: 📋 PHASE 5: DELETE All Items
    C->>DB: DELETE FROM ret_estimation_items<br/>WHERE esti_id = ?
    C->>DB: DELETE FROM ret_estimation_item_stones<br/>WHERE est_id = ?
    C->>DB: DELETE FROM ret_estimation_item_other_materials<br/>WHERE est_id = ?
    C->>DB: DELETE FROM ret_estimation_old_metal_sale_details<br/>WHERE est_id = ?
    C->>DB: DELETE FROM ret_esti_old_metal_stone_details<br/>WHERE est_id = ?
    C->>DB: DELETE FROM ret_est_chit_utilization<br/>WHERE est_id = ?
    C->>DB: DELETE FROM ret_est_gift_voucher_details<br/>WHERE est_id = ?
    end

    rect rgb(232,245,233)
    Note over C,DB: 📋 PHASE 6: Re-Insert All
    loop Each est_tag item
        C->>DB: INSERT ret_estimation_items
        C->>DB: INSERT ret_estimation_item_stones
    end
    loop Each other item type
        C->>DB: INSERT corresponding tables
    end
    end

    rect rgb(227,242,253)
    Note over C,DB: 📋 PHASE 7: Commit
    C->>DB: trans_commit()
    C-->>F: Success redirect
    end
```

---

## 📋 Detail 7.1: Fetch Existing Data

### Controller Functions

```php
// case "edit" - Load form with header data
$data['estimation'] = $this->$model->get_entry_records($id);
$data['est_other_item'] = $this->$model->getOtherEstimateItemsDetails($id);

// case "est_edit" - AJAX fetch all items
$data['tag_details'] = $this->$model->get_est_tag_details($id);
$data['non_tag_details'] = $this->$model->est_non_tag_items($id);
$data['est_home_bill'] = $this->$model->est_home_bill($id);
$data['chit_details'] = $this->$model->get_chit_details($id);
$data['old_metal'] = $this->$model->old_Metal($id);
```

### Data Fetched

| Model Function            | Returns                      |
| ------------------------- | ---------------------------- |
| `get_entry_records(id)`   | Estimation header + customer |
| `get_est_tag_details(id)` | Tagged items with stones     |
| `est_non_tag_items(id)`   | Catalog items                |
| `est_home_bill(id)`       | Custom/Home bill items       |
| `get_chit_details(id)`    | Chit utilization             |
| `old_Metal(id)`           | Old metal purchase           |

---

## 📋 Detail 7.2: Delete Pattern

### Tables Deleted (in order)

```php
// Inside case "update" after header update succeeds
$this->$model->deleteData('est_id', $id, 'ret_est_gift_voucher_details');
$this->$model->deleteData('esti_id', $id, 'ret_estimation_items');
$this->$model->deleteData('est_id', $id, 'ret_estimation_item_stones');
$this->$model->deleteData('est_id', $id, 'ret_estimation_item_other_materials');
$this->$model->deleteData('est_id', $id, 'ret_estimation_old_metal_sale_details');
$this->$model->deleteData('est_id', $id, 'ret_esti_old_metal_stone_details');
$this->$model->deleteData('est_id', $id, 'ret_est_chit_utilization');
```

### Delete Order

```mermaid
flowchart TB
    D1[Gift Vouchers] --> D2[Estimation Items]
    D2 --> D3[Item Stones]
    D3 --> D4[Item Materials]
    D4 --> D5[Old Metal Details]
    D5 --> D6[Old Metal Stones]
    D6 --> D7[Chit Utilization]

    style D1 fill:#ffcdd2
    style D2 fill:#ffcdd2
    style D3 fill:#ffcdd2
```

---

## 📋 Detail 7.3: Re-Insert Logic

After DELETE, the code proceeds **exactly like WF3: Save Estimation**:

1. Loop each `est_tag[]` → INSERT `ret_estimation_items` + stones
2. Loop each `est_catalog[]` → INSERT items + stones + materials
3. Loop each `est_custom[]` → INSERT items + stones
4. Loop each `est_oldmatel[]` → INSERT old metal + stones
5. Loop each `chit_uti[]` → INSERT chit utilization

> [!TIP]
> See [Workflow 3: Save Estimation](./workflow_03_save_estimation.md) for detailed insert logic.

---

## 🛡️ Guards & Validations

### Cannot Edit If Billed

```php
$estimation = $this->$model->get_entry_records($id);

if ($estimation['estbillid'] == '') {
    // Proceed with update
} else {
    // Cannot edit - estimation already converted to bill
}
```

| Check          | Field       | Condition  | Result        |
| -------------- | ----------- | ---------- | ------------- |
| Already Billed | `estbillid` | Not empty  | ❌ Block edit |
| Not Billed     | `estbillid` | Empty/NULL | ✅ Allow edit |

---

## 🔄 Edit vs Save Comparison

| Aspect      | Save (WF3)              | Update (WF7)                              |
| ----------- | ----------------------- | ----------------------------------------- |
| Header      | INSERT                  | UPDATE                                    |
| Items       | INSERT                  | DELETE + INSERT                           |
| Stones      | INSERT                  | DELETE + INSERT                           |
| Transaction | Begin → Insert → Commit | Begin → Update → Delete → Insert → Commit |
| Pre-check   | Day closing             | Day closing + not billed                  |

---

## ⚠️ Risk: Tag Status Not Reverted

> [!CAUTION]
> When editing, if a tag is **removed** from the estimation:
>
> - The item row is deleted
> - **But the tag's `tag_status` in `ret_taging` may NOT be updated**
> - This is a potential bug area - check if tag reversion is needed

---

## ✅ Workflow Complete Checklist

- [x] Master overview diagram
- [x] Full swimlane sequence
- [x] Fetch existing detail (7.1)
- [x] Delete pattern detail (7.2)
- [x] Re-insert reference (7.3)
- [x] Guards & validations
- [x] Risk warnings
