# Refined Estimation Test Suite

> **Strategy:** Fix each reported bug/inconsistency in a dedicated branch.

## 🚨 1. Bugs Backlog (From Raja's Excel)

These are confirmed failures that need immediate attention.

| Bug ID          | Component   | Issue Description                                    | Original Link                                                                  | Priority     |
| --------------- | ----------- | ---------------------------------------------------- | ------------------------------------------------------------------------------ | ------------ |
| **BUG-EST-001** | Old Metal   | Old Metal Name Not Displaying                        | [Link](https://connect.zoho.in/portal/logimax-clients/task/124744000003678118) | **Blocker**  |
| **BUG-EST-002** | Stock       | Non-Tag available stock shows less than report       | [Link](https://connect.zoho.in/portal/logimax-clients/task/124744000003694297) | **Critical** |
| **BUG-EST-003** | Calculation | **Tax calculating at 6% of GST** but shows 3%        | -                                                                              | **Critical** |
| **BUG-EST-004** | Print       | Product Details Not Displaying Properly              | [Link](https://connect.zoho.in/portal/logimax-clients/task/124744000003687826) | **Critical** |
| **BUG-EST-005** | Settings    | MC Type not showing (Non-Tagged/Home Bill)           | [Link](https://connect.zoho.in/portal/logimax-clients/task/124744000003875536) | **Critical** |
| **BUG-EST-006** | Settings    | Home Bill displays wrong purity                      | [Link](https://connect.zoho.in/portal/logimax-clients/task/124744000003875432) | **Critical** |
| **BUG-EST-007** | Rate        | Home Bill/Old Metal Rate not showing                 | [Link](https://connect.zoho.in/portal/logimax-clients/task/124744000003860033) | **Critical** |
| **BUG-EST-008** | UI          | Tagging: Delete button/Sales rate alignment mismatch | -                                                                              | High         |
| **BUG-EST-009** | Tagging     | Multi-Metal Tagging Details Display Issue            | [Link](https://connect.zoho.in/portal/logimax-clients/task/124744000004304247) | High         |

---

## 🔍 2. Cross-Module Consistency Matrix

Verify that these fields behave consistently across all 4 modes.

| Field        | Tagged Item | Non-Tagged  | Home Bill   | Old Metal   | Expected Behavior                               |
| ------------ | ----------- | ----------- | ----------- | ----------- | ----------------------------------------------- |
| **Rate**     | Auto (Meta) | Auto (Meta) | Auto (Meta) | Auto (OldM) | Must fetch from respective Rate Master          |
| **Purity**   | Auto (Tag)  | Auto (Cat)  | Auto (Cat)? | Manual      | Must match Product Category unless manual       |
| **MC/VA**    | Auto (Set)  | Auto (Set)  | Auto (Set)  | N/A         | Must trigger from Design/SubDesign settings     |
| **Gross Wt** | Auto (Tag)  | Manual      | Manual      | Manual      | Decimal precision (3 places) must match         |
| **Net Wt**   | Auto (Tag)  | Calc        | Calc        | Calc        | `Gross - Stone - Wastage` (Formula consistency) |

**Test Task:** Run `TEST-CONS-001` to `TEST-CONS-005` filling this matrix.

---

## 🧮 3. Calculation Combinations (Data Driven)

Use these inputs to verify the "Money Math" is robust.

| Case ID      | Type      | Weight | Rate | Wastage | MC    | Tax | Expected Outcome                                           |
| ------------ | --------- | ------ | ---- | ------- | ----- | --- | ---------------------------------------------------------- |
| **CALC-001** | Gold      | 10.000 | 5000 | 12%     | 500/g | 3%  | `(10*5000) + (1.2*5000) + (10*500) = 61,000 + 3% = 62,830` |
| **CALC-002** | Diamond   | 10.000 | 5000 | 0%      | Fixed | 3%  | `(10*5000) + 10000(Stone) + 5000(MC) = 65,000 + 3%`        |
| **CALC-003** | Old Metal | 10.000 | 4800 | 0%      | N/A   | 0%  | `10 * 4800 = 48,000` (Credit)                              |
| **CALC-004** | Silver    | 100.0  | 80   | 5%      | 10/g  | 3%  | `(100*80) + (5*80) + (100*10) = 9,400 + 3%`                |

---

## 🧪 4. Functional Test Cases (Expanded)

### A. Customer & Header

- **EST-CUST-001**: Search Customer by Mobile (Existing)
- **EST-CUST-002**: Add New Customer (Mandatory Fields: Name, Mobile, State, City)
- **EST-CUST-003**: **[Edge]** Add Customer with Duplicate Mobile
- **EST-CUST-004**: KYC Validation (Aadhaar format, PAN format)
- **EST-CUST-005**: Branch Transfer Selection (Verify Destination Branch dropdown)

### B. Tagged Items

- **EST-TAG-001**: Search Valid Tag (Auto-fill all fields)
- **EST-TAG-002**: **[Edge]** Search Sold Tag (Expect Error)
- **EST-TAG-003**: **[Edge]** Search Tag from different Branch (Expect Error/Warning)
- **EST-TAG-004**: Multi-Metal Tag (Verify secondary metal breakdown)

### C. Non-Tagged Items

- **EST-NTAG-001**: Product Dropdown Filter (Section -> Category -> Product)
- **EST-NTAG-002**: Design Dropdown Filter (Product -> Design)
- **EST-NTAG-003**: Stock Validation (Input Qty > Avail Qty check)

### D. Home Bill (The "Wild West")

- **EST-HOME-001**: Manual Entry integrity (Allow all fields)
- **EST-HOME-002**: **[Inconsistency]** Verify MC/VA triggers even without tag
- **EST-HOME-003**: Verify Rate fetch happens on Purity selection

---

## 🛑 5. Negative Testing (Destructive)

- **NEG-001**: Enter Negative Weight (`-10.00`)
- **NEG-002**: Enter Negative Rate (`-5000`)
- **NEG-003**: Save Estimation with 0 Items
- **NEG-004**: Change Rate to `0` and Save
- **NEG-005**: Delete "Other Charges" while calculation is pending

---

## Next Steps

1. **Pick a Bug** (e.g., `BUG-EST-003`)
2. **Create Branch** `fix/tax-calculation-6percent`
3. **Reproduce** using `CALC-001` logic
4. **Fix & Verify**
