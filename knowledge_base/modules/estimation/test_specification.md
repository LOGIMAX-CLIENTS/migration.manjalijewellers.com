# Test Specification: Estimation Module

> **Test Cases Derived from Knowledge Base**  
> Coverage: Calculations, Validations, Edge Cases, Integration

---

## 📊 Test Coverage Summary

| Category                | Test Cases | Priority |
| ----------------------- | :--------: | :------: |
| Metal Value Calculation |     8      |   High   |
| Wastage Calculation     |     6      |   High   |
| Making Charge (MC)      |     8      |   High   |
| Old Metal               |     6      |   High   |
| Chit Scheme             |     9      |  Medium  |
| Validations             |     8      |   High   |
| **Total**               |   **45**   |          |

---

## 🧮 Category 1: Metal Value Calculation

### TC-101: caltype=0 (Gross Weight)

| Field        | Value                                        |
| ------------ | -------------------------------------------- |
| **Input**    | gross_wt=10, less_wt=2, rate=5500, caltype=0 |
| **Expected** | metal_value = 55000                          |
| **Formula**  | `gross_wt × rate`                            |

### TC-102: caltype=1 (Net Weight)

| Field        | Value                                        |
| ------------ | -------------------------------------------- |
| **Input**    | gross_wt=10, less_wt=2, rate=5500, caltype=1 |
| **Expected** | net_wt=8, metal_value = 44000                |
| **Formula**  | `(gross_wt - less_wt) × rate`                |

### TC-103: caltype=2 (Net Weight + MC on Gross)

| Field        | Value                                        |
| ------------ | -------------------------------------------- |
| **Input**    | gross_wt=10, less_wt=2, rate=5500, caltype=2 |
| **Expected** | metal_value = 44000 (uses net_wt)            |
| **Note**     | MC calculated on gross_wt separately         |

### TC-104: caltype=3 (Fixed Price)

| Field        | Value                        |
| ------------ | ---------------------------- |
| **Input**    | fixed_price=25000, caltype=3 |
| **Expected** | item_cost = 25000            |
| **Note**     | No weight calculation        |

### TC-105: Zero Weight

| Field        | Value                            |
| ------------ | -------------------------------- |
| **Input**    | gross_wt=0, rate=5500, caltype=0 |
| **Expected** | metal_value = 0                  |

### TC-106: Decimal Weight

| Field        | Value                                |
| ------------ | ------------------------------------ |
| **Input**    | gross_wt=5.678, rate=5500, caltype=0 |
| **Expected** | metal_value = 31229                  |

### TC-107: Less Weight Equals Gross Weight

| Field        | Value                                         |
| ------------ | --------------------------------------------- |
| **Input**    | gross_wt=10, less_wt=10, rate=5500, caltype=1 |
| **Expected** | net_wt=0, metal_value = 0                     |

### TC-108: Different Metal Rates (Silver)

| Field        | Value                                          |
| ------------ | ---------------------------------------------- |
| **Input**    | gross_wt=100, rate=85, metal_type=2, caltype=0 |
| **Expected** | metal_value = 8500                             |

---

## 📈 Category 2: Wastage Calculation

### TC-201: Wastage on Gross (caltype=0)

| Field        | Value                                          |
| ------------ | ---------------------------------------------- |
| **Input**    | gross_wt=10, wastage%=12, rate=5500, caltype=0 |
| **Expected** | wastage_wt=1.2, wastage_amt=6600               |
| **Formula**  | `gross_wt × (wastage% / 100) × rate`           |

### TC-202: Wastage on Net (caltype=1)

| Field        | Value                                                     |
| ------------ | --------------------------------------------------------- |
| **Input**    | gross_wt=10, less_wt=2, wastage%=12, rate=5500, caltype=1 |
| **Expected** | wastage_wt=0.96, wastage_amt=5280                         |
| **Formula**  | `net_wt × (wastage% / 100) × rate`                        |

### TC-203: Zero Wastage

| Field        | Value                              |
| ------------ | ---------------------------------- |
| **Input**    | gross_wt=10, wastage%=0, rate=5500 |
| **Expected** | wastage_amt = 0                    |

### TC-204: Maximum Wastage (100%)

| Field        | Value                                |
| ------------ | ------------------------------------ |
| **Input**    | gross_wt=10, wastage%=100, rate=5500 |
| **Expected** | wastage_amt = 55000                  |

### TC-205: Fractional Wastage

| Field        | Value                                 |
| ------------ | ------------------------------------- |
| **Input**    | gross_wt=10, wastage%=12.5, rate=5500 |
| **Expected** | wastage_amt = 6875                    |

### TC-206: Wastage with Board Rate vs Item Rate

| Field        | Value                                                  |
| ------------ | ------------------------------------------------------ |
| **Input**    | net_wt=8, wastage%=12, item_rate=5800, board_rate=5500 |
| **Expected** | wastage_amt = 5280 (uses board_rate)                   |

---

## 🔧 Category 3: Making Charge (MC)

### TC-301: MC Per Gram (mc_type=1)

| Field        | Value                              |
| ------------ | ---------------------------------- |
| **Input**    | weight=10, mc_value=400, mc_type=1 |
| **Expected** | mc_total = 4000                    |
| **Formula**  | `mc_value × weight`                |

### TC-302: MC Per Piece (mc_type=2)

| Field        | Value                              |
| ------------ | ---------------------------------- |
| **Input**    | pieces=2, mc_value=1500, mc_type=2 |
| **Expected** | mc_total = 3000                    |
| **Formula**  | `mc_value × pieces`                |

### TC-303: MC Percentage (mc_type=3)

| Field        | Value                                    |
| ------------ | ---------------------------------------- |
| **Input**    | metal_value=55000, mc_value=8, mc_type=3 |
| **Expected** | mc_total = 4400                          |
| **Formula**  | `metal_value × (mc_value / 100)`         |

### TC-304: MC Fixed Amount (mc_type=4)

| Field        | Value                    |
| ------------ | ------------------------ |
| **Input**    | mc_value=5000, mc_type=4 |
| **Expected** | mc_total = 5000          |

### TC-305: MC on Gross (caltype=2)

| Field        | Value                                                     |
| ------------ | --------------------------------------------------------- |
| **Input**    | gross_wt=10, net_wt=8, mc_value=400, mc_type=1, caltype=2 |
| **Expected** | mc_total = 4000 (uses gross_wt)                           |

### TC-306: MC on Net (caltype=1)

| Field        | Value                                                     |
| ------------ | --------------------------------------------------------- |
| **Input**    | gross_wt=10, net_wt=8, mc_value=400, mc_type=1, caltype=1 |
| **Expected** | mc_total = 3200 (uses net_wt)                             |

### TC-307: Zero MC

| Field        | Value                            |
| ------------ | -------------------------------- |
| **Input**    | weight=10, mc_value=0, mc_type=1 |
| **Expected** | mc_total = 0                     |

### TC-308: MC with Decimal Value

| Field        | Value                                   |
| ------------ | --------------------------------------- |
| **Input**    | weight=10.5, mc_value=450.75, mc_type=1 |
| **Expected** | mc_total = 4732.88                      |

---

## 🥇 Category 4: Old Metal Calculation

### TC-401: Old Gold - Basic

| Field        | Value                                   |
| ------------ | --------------------------------------- |
| **Input**    | gross_wt=10, melting_loss=2%, rate=5200 |
| **Expected** | net_wt=9.8, amount=50960                |
| **Formula**  | `net_wt × rate`                         |

### TC-402: Old Gold with Touch

| Field        | Value                               |
| ------------ | ----------------------------------- |
| **Input**    | gross_wt=10, touch=91.6%, rate=5500 |
| **Expected** | pure_wt=9.16, amount=50380          |

### TC-403: Old Silver

| Field        | Value                                  |
| ------------ | -------------------------------------- |
| **Input**    | gross_wt=100, category=silver, rate=75 |
| **Expected** | amount = 7500                          |

### TC-404: Old Metal with Stones

| Field        | Value                              |
| ------------ | ---------------------------------- |
| **Input**    | gross_wt=15, stone_wt=3, rate=5200 |
| **Expected** | net_wt=12, amount=62400            |

### TC-405: Rate Below Minimum

| Field        | Value                             |
| ------------ | --------------------------------- |
| **Input**    | rate=3000, min_old_gold_rate=4000 |
| **Expected** | Error: "Rate below minimum"       |

### TC-406: Rate Above Maximum

| Field        | Value                             |
| ------------ | --------------------------------- |
| **Input**    | rate=8000, max_old_gold_rate=7000 |
| **Expected** | Error: "Rate above maximum"       |

---

## 🎁 Category 5: Chit Scheme Benefits

### TC-501: Simple Closure (Type 1)

| Field        | Value                                   |
| ------------ | --------------------------------------- |
| **Input**    | closing_amount=50000, closure_type=1    |
| **Expected** | chit_deduction=50000, no weight benefit |

### TC-502: MC & VA Benefit (Type 2, Highest)

| Field        | Value                                                                                  |
| ------------ | -------------------------------------------------------------------------------------- |
| **Input**    | saving_wt=10, wastage%=15 (highest), mc=400/gm, rate=5500, closure_type=2, calc_type=2 |
| **Expected** | wastage_benefit=8250, mc_benefit=4000                                                  |

### TC-503: MC & VA Benefit (Type 2, Lowest)

| Field        | Value                                                                                 |
| ------------ | ------------------------------------------------------------------------------------- |
| **Input**    | saving_wt=10, wastage%=10 (lowest), mc=350/gm, rate=5500, closure_type=2, calc_type=3 |
| **Expected** | wastage_benefit=5500, mc_benefit=3500                                                 |

### TC-504: Per-Item Split (Type 3)

| Field        | Value                                          |
| ------------ | ---------------------------------------------- |
| **Input**    | saving_wt=10, items=[5gm, 5gm], closure_type=3 |
| **Expected** | Split 5gm benefit per item                     |

### TC-505: Proportional Weight Calculation

| Field        | Value                                                       |
| ------------ | ----------------------------------------------------------- |
| **Input**    | closing_balance=50000, closing_weight=10, utilization=25000 |
| **Expected** | utilized_weight = 5gm                                       |
| **Formula**  | `(utilization / balance) × weight`                          |

### TC-506: Amount Exceeds Balance

| Field        | Value                                 |
| ------------ | ------------------------------------- |
| **Input**    | chit_amt=60000, closing_balance=50000 |
| **Expected** | Error: "Amount exceeds balance"       |

### TC-507: Immature Scheme (paid != total installments)

| Field        | Value                                       |
| ------------ | ------------------------------------------- |
| **Input**    | paid_inst=10, total_inst=12, closure_type=2 |
| **Expected** | No MC/VA benefits (only cash value)         |

### TC-508: Top-up Scheme

| Field        | Value                      |
| ------------ | -------------------------- |
| **Input**    | is_topup=1, closure_type=2 |
| **Expected** | No MC/VA benefits          |

### TC-509: Rate Lock Benefit

| Field        | Value                                          |
| ------------ | ---------------------------------------------- |
| **Input**    | locked_rate=5200, current_rate=5800, weight=10 |
| **Expected** | rate_benefit = 6000                            |
| **Formula**  | `weight × (current - locked)`                  |

---

## ✅ Category 6: Validations

### TC-601: Tag Already Sold

| Field        | Value                     |
| ------------ | ------------------------- |
| **Input**    | tag_status=1 (sold)       |
| **Expected** | Error: "Tag already sold" |

### TC-602: Tag Reserved for Another Customer

| Field        | Value                                                    |
| ------------ | -------------------------------------------------------- |
| **Input**    | tag_reserved_for=customer_B, current_customer=customer_A |
| **Expected** | Error: "Tag reserved for another customer"               |

### TC-603: Day Closing Not Updated

| Field        | Value                              |
| ------------ | ---------------------------------- |
| **Input**    | branch_closing_date=yesterday      |
| **Expected** | Error: "Kindly update Day closing" |

### TC-604: Empty Tax Price

| Field        | Value                   |
| ------------ | ----------------------- |
| **Input**    | tax_price=null or empty |
| **Expected** | allow_submit = FALSE    |

### TC-605: Home Bill Weight Exceeds Balance

| Field        | Value                          |
| ------------ | ------------------------------ |
| **Input**    | entered_wt=8, tag_balance_wt=5 |
| **Expected** | Reset to 5, show error         |

### TC-606: Duplicate Estimation Submission

| Field        | Value                         |
| ------------ | ----------------------------- |
| **Input**    | form_secret already used      |
| **Expected** | Error: "Duplicate submission" |

### TC-607: Purity Out of Range

| Field        | Value                  |
| ------------ | ---------------------- |
| **Input**    | purity=5% (min is 10%) |
| **Expected** | Validation error       |

### TC-608: Negative Weight Input

| Field        | Value                 |
| ------------ | --------------------- |
| **Input**    | gross_wt=-5           |
| **Expected** | Treated as 0 or error |

---

## 🔗 Integration Tests

### IT-01: Full Estimation Save Flow

1. Create estimation header
2. Add 2 tagged items
3. Add 1 old metal
4. Apply chit scheme
5. Verify all tables populated

### IT-02: Edit Estimation (DELETE + INSERT)

1. Load existing estimation
2. Remove 1 item
3. Add 1 new item
4. Save
5. Verify old item tag status reset

### IT-03: Convert to Billing

1. Create estimation
2. Convert to bill
3. Verify `estbillid` linked
4. Verify tag status = sold

---

## 📁 Test Data Fixtures Needed

| Fixture               | Fields                                          |
| --------------------- | ----------------------------------------------- |
| `tag_gold.json`       | id, gross_wt, less_wt, rate, product_id, status |
| `customer.json`       | id, name, mobile, scheme_accounts               |
| `scheme_account.json` | id, closing_balance, closing_weight, benefits   |
| `estimation.json`     | Complete estimation with items                  |
