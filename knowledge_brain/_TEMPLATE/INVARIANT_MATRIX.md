# Invariant Matrix Template
# Module: {MODULE_NAME}

> **Purpose**: Documents variant-specific behavior for modules where different configurations trigger different code paths.
> **When to build**: During `/build-module-brain` Step 6b — skip if module has no config-driven behavior.
> **Example**: See `knowledge_brain/Payment/INVARIANT_MATRIX.md` for a completed example.

---

## 1. Variant Dimensions

List all axes of variation for this module:

| # | Dimension Name | Possible Values | Controlling Field (DB) | Controlling Field (PHP) | Controlling Field (JS) |
|---|---|---|---|---|---|
| 1 | {e.g., Scheme Type} | {e.g., Gold, Silver, Diamond} | `{table.column}` | `$var_name` | `$('#element_id')` |
| 2 | {e.g., GST Type} | {e.g., Inclusive, Exclusive, Exempt} | `{table.column}` | `$var_name` | `$('#element_id')` |
| 3 | ... | ... | ... | ... | ... |

---

## 2. Behavior Grids

Create one grid per pair of interacting dimensions that produce different outcomes.

### Grid: {Dimension A} × {Dimension B}

| | {B Value 1} | {B Value 2} | {B Value 3} |
|---|---|---|---|
| **{A Value 1}** | Formula: {formula} | Formula: {formula} | Formula: {formula} |
| **{A Value 2}** | Formula: {formula} | ⚠️ BUG FLAG: {description} | Formula: {formula} |
| **{A Value 3}** | N/A (not applicable) | Formula: {formula} | ❓ UNDEFINED |

> Symbols:
> - ✅ = Verified correct
> - ⚠️ = Known bug (reference bug ID)
> - ❓ = Undefined behavior (needs specification)
> - N/A = Not applicable / impossible combination

### Grid: {Dimension C} × {Dimension D}

_(Repeat for each pair that interacts)_

---

## 3. Edge Cases & Special Combinations

### {Combination Name}
- **When**: {Condition}
- **Expected**: {What should happen}
- **Actual**: {What currently happens}
- **Status**: ✅ Correct / ⚠️ Bug / ❓ Untested

---

## 4. Variant Test Coverage

| Dimension | Total Variants | Tested | Untested | Coverage |
|---|---|---|---|---|
| {Dimension 1} | {N} | {N} | {N} | {%} |
| {Dimension 2} | {N} | {N} | {N} | {%} |
| **Overall** | {N combinations} | {N} | {N} | {%} |
