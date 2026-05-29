# Business Rules Reference

> **Calculation Rules, Validation Constraints, and Configuration Settings**  
> Essential reference for understanding estimation logic

---

## 📊 Calculation Types (caltype)

### Overview

| Code | Name                     | Weight Used | MC Applied To |
| :--: | :----------------------- | :---------- | :------------ |
|  0   | Gross Weight             | Gross Wt    | Gross Wt      |
|  1   | Net Weight               | Net Wt      | Net Wt        |
|  2   | Net Weight + MC on Gross | Net Wt      | Gross Wt      |
|  3   | Fixed Price              | N/A         | N/A           |

### Detailed Formulas

#### caltype = 0 (Gross Weight)

```
metal_value = gross_wt × rate_per_gram
wastage_value = gross_wt × (wastage% / 100) × rate
mc_total = mc_value (applied on gross_wt)
```

#### caltype = 1 (Net Weight)

```
net_wt = gross_wt - less_wt
metal_value = net_wt × rate_per_gram
wastage_value = net_wt × (wastage% / 100) × rate
mc_total = mc_value (applied on net_wt)
```

#### caltype = 2 (Net + MC on Gross)

```
net_wt = gross_wt - less_wt
metal_value = net_wt × rate_per_gram
wastage_value = net_wt × (wastage% / 100) × rate
mc_total = mc_value (applied on gross_wt)  ← Different!
```

#### caltype = 3 (Fixed Price)

```
item_cost = fixed_amount  // No weight calculation
```

---

## 🔧 Making Charge Types (mc_type)

| Code | Type         | Calculation                                 |
| :--: | :----------- | :------------------------------------------ |
|  1   | Per Gram     | `mc_total = mc_value × weight`              |
|  2   | Per Piece    | `mc_total = mc_value × piece_count`         |
|  3   | Percentage   | `mc_total = metal_value × (mc_value / 100)` |
|  4   | Fixed Amount | `mc_total = mc_value`                       |

---

## 💰 Rate Validation Limits

### Old Metal Purchase Rates

| Metal      | Setting Key           | Typical Range |
| ---------- | --------------------- | ------------- |
| Gold Min   | `min_old_gold_rate`   | ₹4,000+       |
| Gold Max   | `max_old_gold_rate`   | ₹7,000+       |
| Silver Min | `min_old_silver_rate` | ₹50+          |
| Silver Max | `max_old_silver_rate` | ₹100+         |

### Purity Limits

| Field     | Min | Max  |
| --------- | --- | ---- |
| Purity %  | 10% | 100% |
| Wastage % | 0%  | 100% |
| Touch     | 10% | 100% |

---

## ✅ Mandatory Fields Matrix

### Tagged Item (item_type = 0)

| Field        | Required | Validation           |
| ------------ | :------: | -------------------- |
| `tag_id`     |    ✅    | Must exist, not sold |
| `product_id` |    ✅    | From tag             |
| `gross_wt`   |    ✅    | From tag             |
| `purity`     |    ⚠️    | If applicable        |
| `tax_price`  |    ✅    | Must be calculated   |

### Catalog Item (item_type = 1)

| Field        | Required | Validation    |
| ------------ | :------: | ------------- |
| `product_id` |    ✅    | User selected |
| `gross_wt`   |    ✅    | User entered  |
| `purity`     |    ✅    | User selected |
| `mc_value`   |    ⚠️    | Optional      |

### Custom Item (item_type = 2)

| Field        | Required | Validation    |
| ------------ | :------: | ------------- |
| `product_id` |    ✅    | User selected |
| `gross_wt`   |    ✅    | User entered  |
| `item_cost`  |    ✅    | User entered  |

### Old Metal

| Field         | Required | Validation         |
| ------------- | :------: | ------------------ |
| `id_category` |    ✅    | Gold=1, Silver=2   |
| `gross_wt`    |    ✅    | User entered       |
| `net_wt`      |    ✅    | Calculated         |
| `rate`        |    ✅    | Within limits      |
| `purpose`     |    ✅    | Cash=1, Exchange=2 |

---

## 🔐 Profile Permissions

### Settings from `ret_profile_settings`

| Setting                | Values | Effect                |
| ---------------------- | ------ | --------------------- |
| `allow_estimation`     | 0/1    | Can access module     |
| `allow_edit_rate`      | 0/1    | Can modify metal rate |
| `allow_discount`       | 0/1    | Can apply discounts   |
| `max_discount_percent` | %      | Maximum allowed       |
| `allow_old_metal`      | 0/1    | Can add old metal     |

### Employee Settings

| Setting             | Values | Effect               |
| ------------------- | ------ | -------------------- |
| `id_branch`         | ID     | Default branch       |
| `can_override_rate` | 0/1    | Override rate limits |

---

## 📈 Wastage Slab Rules

### Slab-Based Wastage

Many shops use slabs for wastage based on weight:

| Weight Range | Wastage % |
| ------------ | --------- |
| 0 - 5 gm     | 16%       |
| 5 - 10 gm    | 14%       |
| 10 - 20 gm   | 12%       |
| 20+ gm       | 10%       |

### Settings

| Key               | Purpose                |
| ----------------- | ---------------------- |
| `id_wastage_slab` | References slab table  |
| `wast_slab_val`   | Calculated slab value  |
| `act_wast_per`    | Actual wastage applied |
| `max_va_per`      | Maximum wastage limit  |

---

## 🎁 Chit Scheme Benefits

### Savings Calculation

| Benefit         | Formula                                    |
| --------------- | ------------------------------------------ |
| Wastage Savings | `weight × (wastage_benefit% / 100) × rate` |
| MC Savings      | `weight × mc_benefit_per_gram`             |
| Rate Lock       | `weight × (current_rate - locked_rate)`    |

### Settings

| Key                          | Purpose                       |
| ---------------------------- | ----------------------------- |
| `weightschemecaltype`        | How chit weight is calculated |
| `chit_rate_calculation_type` | Rate basis for benefits       |
| `weight_scheme_closure_type` | Closure handling              |

---

## 🏷️ Tag Status Values

| Value | Status      | Can Be Sold? |
| :---: | ----------- | :----------: |
|   0   | Available   |      ✅      |
|   1   | Sold        |      ❌      |
|   2   | Reserved    |      ⚠️      |
|   3   | Transferred |      ❌      |

---

## 📋 Estimation Status

| Field             | Value | Meaning           |
| ----------------- | :---: | ----------------- |
| `estbillid`       | NULL  | Not yet billed    |
| `estbillid`       |  ID   | Converted to bill |
| `purchase_status` |   0   | Item pending      |
| `purchase_status` |   1   | Item billed       |

---

## 🔢 Item Type Codes

| Code | Type             | Source               |
| :--: | :--------------- | :------------------- |
|  0   | Tagged Item      | `ret_taging`         |
|  1   | Catalog/Non-Tag  | `ret_nontagginglots` |
|  2   | Custom/Home Bill | Manual entry         |
|  3   | Order Item       | `customerorder`      |

---

## ⚙️ Key Settings Reference

| Setting Key             | Purpose                | Source Table |
| ----------------------- | ---------------------- | ------------ |
| `goldrate_22ct`         | Default gold rate      | Branch rate  |
| `silverrate_1gm`        | Default silver rate    | Branch rate  |
| `max_cash_allowed`      | Cash limit (₹2,00,000) | ret_settings |
| `est_emp_select_req`    | Employee mandatory     | ret_settings |
| `bulk_wastage_discount` | Enable bulk discount   | ret_settings |
| `wastage_rate_type`     | Wastage calculation    | ret_settings |

---

## ✅ Document Complete

- [x] Calculation types (caltype 0-3)
- [x] MC types (4 types)
- [x] Rate validation limits
- [x] Mandatory fields matrix
- [x] Profile permissions
- [x] Wastage slab rules
- [x] Chit benefit formulas
- [x] Status code reference
