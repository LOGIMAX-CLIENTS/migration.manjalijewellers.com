# Estimation Code Quality Audit

## Overview

| Metric              | Value               |
| ------------------- | ------------------- |
| **File**            | `ret_estimation.js` |
| **Total Lines**     | 31,384              |
| **Total Functions** | 512                 |
| **Indexed Calls**   | 1,497               |

---

## 🚨 Top 10 Largest Functions (Refactor Candidates)

| #   | Function                         | Lines     | Risk        | Action                            |
| --- | -------------------------------- | --------- | ----------- | --------------------------------- |
| 1   | `set_tag_split_details`          | **1,976** | 🔴 Critical | Split into 5-10 smaller functions |
| 2   | `estimation_tag_data`            | **1,349** | 🔴 Critical | Extract data processing logic     |
| 3   | `set_tag_split_details_old`      | **866**   | 🟠 High     | Legacy - consider deprecation     |
| 4   | `calculatetag_SaleValue`         | **754**   | 🟠 High     | Multiple responsibilities         |
| 5   | `calculateOrderTag`              | **704**   | 🟠 High     | Similar to #4, consolidate        |
| 6   | `get_tag_data`                   | **574**   | 🟡 Medium   | AJAX + UI logic mixed             |
| 7   | `append_tag_details`             | **565**   | 🟡 Medium   | Template building                 |
| 8   | `get_tag_data_29_09`             | **498**   | 🟡 Medium   | Date-specific variant             |
| 9   | `wastage_slab_value`             | **495**   | 🟡 Medium   | Complex calculation               |
| 10  | `calculate_chit_closing_balance` | **461**   | 🟡 Medium   | Financial calculation             |

---

## 🔥 High-Impact Functions (Most Called)

| Function                  | Callers | Risk                               |
| ------------------------- | ------- | ---------------------------------- |
| `calculate_sales_details` | 20      | High - Changes affect many callers |
| `calculateSaleValue`      | 20      | High - Core calculation            |
| `get_tag_data`            | 4       | Medium - Entry point               |

---

## Recommendations

### P0: Immediate (Stop the Bleeding)

1. **No new code in top 3 functions** - They're already unmaintainable
2. **Add unit tests** before any refactor

### P1: Short-term (This Sprint)

1. Extract helper functions from `set_tag_split_details`
2. Create `TagCalculator` module for tag-related calculations
3. Separate AJAX calls from UI manipulation

### P2: Medium-term (Next Sprint)

1. Consolidate similar functions (`calculateOrderTag` + `calculatetag_SaleValue`)
2. Remove `_old` variants after verification
3. Add JSDoc comments for complex functions

---

## Technical Debt Score

| Category      | Score | Notes                                  |
| ------------- | ----- | -------------------------------------- |
| Function Size | 2/10  | Top functions are 50-200x ideal size   |
| Coupling      | 5/10  | High caller count on core functions    |
| Naming        | 6/10  | Inconsistent (underscore vs camelCase) |
| Duplication   | 3/10  | `_old` variants indicate copy-paste    |

**Overall: 4/10** - Significant refactoring needed
