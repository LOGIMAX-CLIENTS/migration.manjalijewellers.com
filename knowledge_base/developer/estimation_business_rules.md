# Estimation Business Rules

## Metal Value Calculation

### Rule 1: Weight Selection by Calculation Type

```
IF caltype = 0 → use gross_weight
IF caltype = 1 → use net_weight (gross - less_weight)
IF caltype = 2 → use net_weight for metal value only
IF caltype = 3 → fixed price, no calculation
```

### Rule 2: Metal Value Formula

```
metal_value = weight × rate_per_gram
```

---

## Wastage Rules

### Rule 3: Wastage Calculation

```
wastage_weight = base_weight × (wastage_percentage / 100)
wastage_value = wastage_weight × rate_per_gram
```

### Rule 4: Wastage Base

```
IF wastage_on = 'gross' → base = gross_weight
IF wastage_on = 'net' → base = net_weight
```

---

## Making Charges

### Rule 5: Making Charge Types

| Type       | Formula                                       |
| ---------- | --------------------------------------------- |
| Per Gram   | `mc_value = weight × mc_per_gram`             |
| Per Piece  | `mc_value = quantity × mc_per_piece`          |
| Percentage | `mc_value = metal_value × (mc_percent / 100)` |
| Fixed      | `mc_value = fixed_amount`                     |

---

## Old Metal Exchange

### Rule 6: Old Metal Value

```
old_metal_value = weight × touch_purity × buy_rate
```

### Rule 7: Rate Tolerance (Platinum)

```
IF rate < min_tolerance → REJECT
IF rate > max_tolerance → REJECT
ELSE → ACCEPT
```

---

## Tax Calculation

### Rule 8: GST Calculation

```
IF tax_type = 'exclusive':
    tax_amount = taxable_value × (gst_rate / 100)
    final = taxable_value + tax_amount

IF tax_type = 'inclusive':
    base_value = total / (1 + gst_rate/100)
    tax_amount = total - base_value
```

---

## Chit Scheme Rules

### Rule 9: Chit Application

```
max_applicable = MIN(chit_balance, estimation_total)
remaining_balance = chit_balance - applied_amount
```

### Rule 10: Closing Balance

```
closing_balance =
    (total_paid + bonus_amount)
    - (total_utilized + forfeit_amount)
```

---

## Validation Rules

### Rule 11: Required Fields

- Branch must be selected
- Customer required for save
- At least one item required

### Rule 12: Weight Validation

- Gross weight must be ≥ 0
- Net weight cannot be negative
- Less weight cannot exceed gross weight

### Rule 13: Rate Validation

- Rate must be positive
- Make charge cannot be negative
