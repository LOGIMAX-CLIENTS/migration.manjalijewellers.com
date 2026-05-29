# Workflow 6: Convert to Billing

> **Combined Swimlane + Hierarchical Documentation**  
> Entry Point: User opens billing → enters estimation number → converts to bill

---

## 🎯 Master Overview

```mermaid
graph LR
    subgraph "Convert to Billing Flow"
        A["📋 Fetch Estimation"] --> B{"✓ Validate"}
        B -->|Fail| X["❌ Error"]
        B -->|Pass| C["📝 Load Bill Form"]
        C --> D["💰 Add Payments"]
        D --> E["🔒 Begin Trans"]
        E --> F["📄 Create Bill Header"]
        F --> G["📦 Transfer Items"]
        G --> H["🏷️ Update Tag Status"]
        H --> I["💵 Record Payments"]
        I --> J["✅ Commit"]
    end
    A -.->|Detail 6.1| A1["Estimation Fetch"]
    G -.->|Detail 6.2| G1["Item Transfer"]
    H -.->|Detail 6.3| H1["Status Updates"]

    style A fill:#e3f2fd
    style J fill:#c8e6c9
    style X fill:#ffcdd2
```

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
    participant M as 📦 Model
    end
    box rgb(232,245,233) Database
    participant DB as 🗄️ Database
    end

    U->>F: Enter estimation number
    F->>C: AJAX getEstimationDetails(est_id)

    rect rgb(255,248,225)
    Note over C,DB: 📋 PHASE 1: Fetch Estimation
    C->>M: getEstimationDetails(est_id)
    M->>DB: SELECT * FROM ret_estimation<br/>WHERE estimation_id = ?
    DB-->>M: Estimation header
    M->>DB: SELECT * FROM ret_estimation_items<br/>WHERE esti_id = ?
    DB-->>M: All items
    M->>DB: SELECT * FROM ret_estimation_old_metal<br/>WHERE est_id = ?
    DB-->>M: Old metal items
    M-->>C: Complete estimation data
    C-->>F: JSON response
    end

    F->>F: Populate bill form with estimation data
    U->>F: Review/modify items
    U->>F: Add payment details
    U->>F: Click "Create Bill"

    F->>C: POST /billing/save

    rect rgb(232,245,233)
    Note over C,DB: 📋 PHASE 2: Begin Transaction
    C->>C: Validate form_secret (prevent duplicate)
    C->>C: Get day closing data
    C->>DB: trans_begin()
    end

    rect rgb(225,245,254)
    Note over C,DB: 📋 PHASE 3: Create Bill Header
    C->>M: generateBillNo(branch, metal_type)
    M->>DB: SELECT MAX(bill_no) + 1
    DB-->>M: New bill number
    C->>DB: INSERT INTO ret_billing
    DB-->>C: bill_id
    end

    rect rgb(243,229,245)
    Note over C,DB: 📋 PHASE 4: Transfer Items
    loop Each sale item
        C->>DB: INSERT INTO ret_bill_details
        DB-->>C: bill_det_id
        C->>DB: INSERT ret_billing_item_stones
        C->>DB: INSERT ret_bill_other_metals
    end
    end

    rect rgb(255,243,224)
    Note over C,DB: 📋 PHASE 5: Update Status
    loop Each tagged item
        C->>DB: UPDATE ret_taging SET tag_status = 1
        C->>DB: INSERT ret_taging_status_log
        C->>DB: UPDATE ret_estimation_items<br/>SET purchase_status = 1
    end
    C->>DB: UPDATE ret_estimation<br/>SET estbillid = bill_id
    end

    rect rgb(227,242,253)
    Note over C,DB: 📋 PHASE 6: Record Payments
    loop Each payment method
        C->>DB: INSERT ret_billing_payment
    end
    end

    rect rgb(232,245,233)
    Note over C,DB: 📋 PHASE 7: Commit
    C->>DB: trans_status() check
    alt OK
        C->>DB: trans_commit()
        C->>DB: INSERT log_detail
        C-->>F: {status: true, bill_id}
    else Fail
        C->>DB: trans_rollback()
        C-->>F: {status: false}
    end
    end

    F-->>U: Redirect to print/list
```

---

## 📋 Detail 6.1: Estimation Fetch

### Data Flow

```mermaid
sequenceDiagram
    participant JS as JavaScript
    participant C as Controller
    participant M as Model
    participant DB as Database

    JS->>C: getEstimationDetails(est_id)

    rect rgb(255,248,225)
    Note over M,DB: Fetch Header
    C->>M: getEstimationHeader(est_id)
    M->>DB: SELECT e.*, c.name, c.mobile<br/>FROM ret_estimation e<br/>LEFT JOIN customer c<br/>WHERE estimation_id = ?
    DB-->>M: estimation header + customer
    end

    rect rgb(225,245,254)
    Note over M,DB: Fetch Items
    M->>DB: SELECT ei.*, p.product_name, d.design_name<br/>FROM ret_estimation_items ei<br/>LEFT JOIN ret_product_master p<br/>LEFT JOIN ret_design d<br/>WHERE esti_id = ?
    DB-->>M: items[]
    end

    rect rgb(232,245,233)
    Note over M,DB: Fetch Child Data
    M->>DB: SELECT * FROM ret_estimation_item_stones<br/>WHERE est_id = ?
    M->>DB: SELECT * FROM ret_estimation_old_metal_sale_details<br/>WHERE est_id = ?
    M->>DB: SELECT * FROM ret_est_chit_utilization<br/>WHERE est_id = ?
    DB-->>M: stones[], old_metal[], chit[]
    end

    M-->>C: Complete estimation object
    C-->>JS: JSON response
```

### Response Structure

```javascript
{
  estimation: {
    estimation_id: 1234,
    esti_no: "EST-2025-001",
    cus_id: 55,
    customer_name: "John Doe",
    mobile: "9876543210",
    total_cost: 150000,
    goldrate_22ct: 5500,
    silverrate_1gm: 75
  },
  items: [
    {
      est_item_id: 101,
      tag_id: 5001,
      item_type: 0,  // Tagged
      product_name: "Gold Necklace",
      gross_wt: 15.800,
      net_wt: 15.800,
      item_cost: 95000,
      stone_details: [...],
      other_metal_details: [...]
    }
  ],
  old_metal: [...],
  chit_utilization: [...],
  payments: []  // Empty for new bill
}
```

---

## 📋 Detail 6.2: Item Transfer

### Estimation → Billing Mapping

| Estimation Table                        | Billing Table                   |
| --------------------------------------- | ------------------------------- |
| `ret_estimation`                        | `ret_billing`                   |
| `ret_estimation_items`                  | `ret_bill_details`              |
| `ret_estimation_item_stones`            | `ret_billing_item_stones`       |
| `ret_est_other_metals`                  | `ret_bill_other_metals`         |
| `ret_estimation_old_metal_sale_details` | `ret_billing_old_metal_details` |
| `ret_est_chit_utilization`              | `ret_billing_chit_utilization`  |

### Bill Header Insert

```php
$data = array(
    'bill_no'           => $model->generateBillNo($branch, $metal_type),
    'fin_year_code'     => $fin_year['fin_year_code'],
    'bill_type'         => $addData['bill_type'],
    'bill_cus_id'       => $addData['cus_id'],
    'customer_name'     => $addData['customer_name'],
    'goldrate_22ct'     => $addData['goldrate_22ct'],
    'silverrate_1gm'    => $addData['silverrate_1gm'],
    'tot_bill_amount'   => $addData['tot_bill_amount'],
    'tot_amt_received'  => $addData['tot_amt_received'],
    'tot_discount'      => $addData['tot_discount'],
    'round_off_amt'     => $addData['round_off_amt'],
    'is_credit'         => $addData['is_credit'],
    'bill_date'         => $bill_date,
    'created_time'      => date("Y-m-d H:i:s"),
    'created_by'        => $session->userdata('uid'),
    'id_branch'         => $addData['id_branch'],
);

// INSERT → ret_billing
// Returns: bill_id
```

### Bill Detail Insert (per item)

```php
$arrayBillSales = array(
    'bill_id'           => $bill_id,
    'esti_item_id'      => $billSale['est_itm_id'],  // Link to estimation
    'item_type'         => $billSale['itemtype'],
    'tag_id'            => $billSale['tag'],
    'product_id'        => $billSale['product'],
    'design_id'         => $billSale['design'],
    'gross_wt'          => $billSale['gross'],
    'net_wt'            => $billSale['net'],
    'purity'            => $billSale['purity'],
    'wastage_percent'   => $billSale['wastage'],
    'mc_value'          => $billSale['mc'],
    'mc_type'           => $billSale['bill_mctype'],
    'item_cost'         => $billSale['total_sales_amount'],
    'item_total_tax'    => $billSale['item_total_tax'],
    'total_cgst'        => $billSale['total_cgst'],
    'total_sgst'        => $billSale['total_sgst'],
    'rate_per_grm'      => $billSale['per_grm'],
);

// INSERT → ret_bill_details
// Returns: bill_det_id
```

---

## 📋 Detail 6.3: Status Updates

### Tag Status Update

```mermaid
sequenceDiagram
    participant C as Controller
    participant DB as Database

    rect rgb(255,248,225)
    Note over C,DB: Update Tag Table
    C->>DB: UPDATE ret_taging<br/>SET tag_status = 1<br/>WHERE tag_id = ?
    Note right of C: tag_status: 0=Available, 1=Sold
    end

    rect rgb(225,245,254)
    Note over C,DB: Create Status Log
    C->>DB: INSERT ret_taging_status_log<br/>{tag_id, date, status=1,<br/>from_branch, form_secret}
    end

    rect rgb(232,245,233)
    Note over C,DB: Update Estimation Item
    C->>DB: UPDATE ret_estimation_items<br/>SET purchase_status = 1,<br/>bil_detail_id = ?<br/>WHERE est_item_id = ?
    end

    rect rgb(243,229,245)
    Note over C,DB: Link Estimation to Bill
    C->>DB: UPDATE ret_estimation<br/>SET estbillid = ?<br/>WHERE estimation_id = ?
    end
```

### Status Values

| Table                  | Field             | Before        | After      |
| ---------------------- | ----------------- | ------------- | ---------- |
| `ret_taging`           | `tag_status`      | 0 (Available) | 1 (Sold)   |
| `ret_estimation_items` | `purchase_status` | 0 (Pending)   | 1 (Billed) |
| `ret_estimation`       | `estbillid`       | NULL          | bill_id    |

---

## 📋 Payment Recording

### Payment Types

| Mode          | Code  | Additional Fields               |
| ------------- | ----- | ------------------------------- |
| Cash          | `CSH` | -                               |
| Card (Credit) | `CC`  | card_type, card_no, approval_no |
| Card (Debit)  | `DC`  | card_type, card_no, approval_no |
| Cheque        | `CHQ` | cheque_no, cheque_date, bank_id |
| Net Banking   | `NB`  | NB_type, ref_no                 |
| UPI           | `UPI` | ref_no                          |

### Payment Insert

```php
$arrayCashPay = array(
    'bill_id'           => $bill_id,
    'payment_amount'    => $pay['amount'],
    'payment_mode'      => $pay['payment_mode'],
    'id_pay_device'     => $pay['device_type'],
    'card_type'         => $pay['card_name'],
    'card_no'           => $pay['card_no'],
    'payment_ref_number'=> $pay['ref_no'],
    'id_bank'           => $pay['bank_id'],
    'cheque_no'         => $pay['cheque_no'],
    'cheque_date'       => $pay['cheque_date'],
    'type'              => 1,  // Receipt
    'payment_for'       => 1,  // Sale
    'payment_status'    => 1,  // Complete
    'payment_date'      => date("Y-m-d H:i:s"),
);

// INSERT → ret_billing_payment
```

---

## 🔗 Tables Affected

| Table                     | Action | Purpose                |
| ------------------------- | ------ | ---------------------- |
| `ret_billing`             | INSERT | Bill header            |
| `ret_bill_details`        | INSERT | Bill line items        |
| `ret_billing_item_stones` | INSERT | Stone details          |
| `ret_bill_other_metals`   | INSERT | Other metal components |
| `ret_billing_payment`     | INSERT | Payment records        |
| `ret_taging`              | UPDATE | Mark tag as sold       |
| `ret_taging_status_log`   | INSERT | Audit trail            |
| `ret_estimation`          | UPDATE | Link to bill           |
| `ret_estimation_items`    | UPDATE | Mark as billed         |

---

## ✅ Workflow Complete Checklist

- [x] Master overview diagram
- [x] Full swimlane sequence
- [x] Estimation fetch detail (6.1)
- [x] Item transfer detail (6.2)
- [x] Status updates detail (6.3)
- [x] Payment recording
- [x] Tables affected list

---

**All 6 Workflows Complete!** 🎉

| #   | Workflow           | Status |
| --- | ------------------ | ------ |
| 1   | Create Estimation  | ✅     |
| 2   | Add Tagged Item    | ✅     |
| 3   | Save Estimation    | ✅     |
| 4   | Add Old Metal      | ✅     |
| 5   | Apply Chit Scheme  | ✅     |
| 6   | Convert to Billing | ✅     |
