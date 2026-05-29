# Standard Issue Reporting Protocols

To ensure efficient handling of tasks, all issues must be reported using the following standards.

## 1. Classification Definitions

| Type                | Code  | Definition                                 | Example                             |
| :------------------ | :---- | :----------------------------------------- | :---------------------------------- |
| **Bug**             | `BUG` | The feature is broken or incorrect.        | "Tax calculates 6% instead of 3%."  |
| **Change Request**  | `CR`  | Feature works, but logic needs adjustment. | "Change default tax from 3% to 5%." |
| **New Requirement** | `NR`  | Feature does not exist yet.                | "Add a button to export PDF."       |

---

## 2. Reporting Templates

### 🐛 BUG Report Template

**Subject:** `[BUG] - {Short Description}`

**1. Issue:**
_Describe what is wrong clearly._

**2. Steps to Reproduce:**

1. Go to ...
2. Click on ...
3. Enter ...
4. See error ...

**3. Expected Behavior:**
_What should have happened?_

**4. Severity:**

- [ ] Critical (Blocker)
- [ ] High (Major feature broken)
- [ ] Medium (Workaround exists)
- [ ] Low (Cosmetic)

---

### 🔄 Change Request (CR) Form

**Title:** `[CR] - {Feature/Module Name}`
**System Area:** `{e.g., Estimation, Billing, Catalog}`
**Priority:** `Low / Medium / High`

#### 1. The Change (What?)

| Context              | Description                                                  |
| :------------------- | :----------------------------------------------------------- |
| **Current Behavior** | _Describe exactly how it works today (or paste screenshot)._ |
| **Desired Behavior** | _Describe exactly how it SHOULD work after this change._     |

#### 2. The Why (Business Value)

> _Why is this change necessary? (e.g., "Compliance Requirement", "Saves 5 clicks per user", "Fixes calculation logic gap")._

#### 3. Impact Analysis (Dev Input)

- **Modules Affected:** `{List connected modules}`
- **Est. Risk:** `Low (UI only) / High (Logic change)`
- **Data Update Required?** `Yes / No`

#### 4. Acceptance Criteria

- [ ] Logic adheres to new rule: `{Rule}`.
- [ ] No regression in `{Related Feature}`.

---

### ✨ New Requirement (NR) Template

**Subject:** `[NR] - {Feature Name}`

**1. User Story:**
"As a **{Role}**, I want to **{Action}**, so that **{Benefit}**."

**2. Acceptance Criteria:**

- [ ] The system must...
- [ ] The user can...

---

## 3. Workflow for Developers

1. **Submission:**
   - **Method A (Preferred):** Create a Markdown file in `tasks/inbox/` using the template.
   - **Method B (Excel):** Fill the `Standard_Issue_Sheet.xlsx` (Columns: Type, Priority, Description, Steps).

2. **Triage:**
   - Lead Developer reviews inbox daily.
   - Assigns a **Test Case ID** (e.g., `EST-TAG-005`) to the issue.
   - Moves to `tasks/active/`.

3. **Execution:**
   - Developer creates branch `fix/{issue-id}`.
   - Writes Test Case first (Red).
   - Fixes Code (Green).
