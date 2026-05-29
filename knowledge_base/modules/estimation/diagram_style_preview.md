# Diagram Style Preview: Option C vs Option D

> Compare both styles to decide which provides better clarity for your documentation.

---

# 🏊 Option C: Swimlane Style

Shows parallel processes across clear lanes with timing.

```mermaid
sequenceDiagram
    box rgb(200,230,255) User Actions
    participant U as 👤 User
    end
    box rgb(255,230,200) Browser/JavaScript
    participant B as 🌐 Browser
    participant JS as ⚡ JavaScript
    end
    box rgb(200,255,200) Backend
    participant C as 🎮 Controller
    participant M as 📦 Model
    end
    box rgb(255,200,200) Database
    participant DB as 🗄️ Database
    end

    U->>B: Click "Add Estimation"
    B->>C: GET /estimation/add

    rect rgb(240,248,255)
    Note over C,DB: Backend Data Loading Phase
    C->>M: get_profile_settings(profile_id)
    M->>DB: SELECT * FROM ret_profile_settings WHERE id=?
    DB-->>M: {allow_mc_edit: 1, allow_va_edit: 1, ...}
    M-->>C: profile_data

    C->>M: get_ret_settings('min_old_gold_rate')
    M->>DB: SELECT value FROM ret_settings WHERE key=?
    DB-->>M: 4500

    C->>M: get_employee_settings(uid)
    M->>DB: SELECT * FROM employee_settings WHERE emp_id=?
    DB-->>M: {id_profile: 2, branch_access: [1,2,3]}
    end

    C-->>B: Render form.php with $data

    rect rgb(255,248,240)
    Note over JS: Frontend Initialization Phase
    JS->>JS: Webcam.attach('#my_camera')
    JS->>JS: Initialize Select2, Datepickers
    end

    rect rgb(240,255,240)
    Note over JS,C: Parallel AJAX Loading
    par Metal Rates
        JS->>C: get_metal_rates_by_branch(1)
        C->>M: getBranchMetalRates(1)
        M->>DB: SELECT * FROM branch_metal_rates
        DB-->>M: rates
        C-->>JS: {gold_22ct: 5500, silver: 75}
    and Employees
        JS->>C: get_employee(1)
        C->>M: getEmployeesByBranch(1)
        M->>DB: SELECT * FROM employee WHERE branch=?
        DB-->>M: employees
        C-->>JS: [{id: 1, name: "John"}, ...]
    and Tax Groups
        JS->>C: get_taxgroup_items()
        C->>M: getTaxGroups()
        M->>DB: SELECT * FROM tax_group
        DB-->>M: taxes
        C-->>JS: [{id: 1, name: "GST 3%", rate: 3}]
    end
    end

    JS->>JS: Store in global arrays
    JS-->>B: Form Ready ✅
    B-->>U: Display Estimation Form
```

---

# 📊 Option D: Hierarchical Drill-Down

**Master overview** with **clickable sub-diagrams** for each phase.

## Level 0: Master Overview

```mermaid
flowchart TB
    subgraph MASTER["🎯 CREATE ESTIMATION - Master Flow"]
        A[👤 User Clicks Add] --> B[🎮 Backend Load]
        B --> C[📄 Render Form]
        C --> D[⚡ JS Initialize]
        D --> E[📡 AJAX Load Data]
        E --> F[✅ Form Ready]
    end

    B -.-> B1[/"📋 Detail 1.1: Backend Load"/]
    D -.-> D1[/"📋 Detail 1.2: JS Initialize"/]
    E -.-> E1[/"📋 Detail 1.3: AJAX Loading"/]

    style B1 fill:#e1f5fe
    style D1 fill:#e1f5fe
    style E1 fill:#e1f5fe
```

---

## Level 1.1: Backend Load (Drill-Down)

```mermaid
sequenceDiagram
    participant C as Controller
    participant M as Model
    participant DB as Database

    Note over C: estimation('add') called

    rect rgb(255,245,230)
    Note right of C: Step 1: Profile Settings
    C->>M: get_profile_settings(session.profile)
    M->>DB: SELECT FROM ret_profile_settings<br/>WHERE id_profile = ?
    DB-->>M: Row data
    M-->>C: profile = {<br/>  allow_mc_edit: 1,<br/>  allow_va_edit: 1,<br/>  allow_manual_rate: 0<br/>}
    end

    rect rgb(230,255,230)
    Note right of C: Step 2: Retail Settings (×12)
    loop For each setting key
        C->>M: get_ret_settings(key)
        M->>DB: SELECT value FROM ret_settings<br/>WHERE setting_key = ?
        DB-->>M: setting value
    end
    M-->>C: settings = {<br/>  min_old_gold_rate: 4500,<br/>  max_old_gold_rate: 6000,<br/>  max_cash_allowed: 200000,<br/>  ...<br/>}
    end

    rect rgb(230,230,255)
    Note right of C: Step 3: Employee Settings
    C->>M: get_employee_settings(uid)
    M->>DB: SELECT FROM employee_settings<br/>JOIN employee ON ...<br/>WHERE emp_id = ?
    DB-->>M: Employee permissions
    M-->>C: emp_setting = {<br/>  id_profile: 2,<br/>  allowed_branches: [1,2],<br/>  default_branch: 1<br/>}
    end

    C->>C: Compile $data array
    Note over C: $data ready for view
```

---

## Level 1.2: JS Initialize (Drill-Down)

```mermaid
sequenceDiagram
    participant DOM as DOM Ready
    participant JS as JavaScript
    participant UI as UI Components

    DOM->>JS: $(document).ready()

    rect rgb(255,240,245)
    Note over JS,UI: Phase A: UI Component Init
    JS->>UI: Webcam.attach('#my_camera')
    UI-->>JS: Camera ready
    JS->>UI: $('#status').bootstrapSwitch()
    JS->>UI: $('#profession').select2()
    JS->>UI: $('#date_of_birth').datepicker()
    end

    rect rgb(240,255,245)
    Note over JS: Phase B: Scroll Handler
    JS->>JS: Setup sticky header handler
    Note right of JS: if(scrollTop > 300)<br/>  stickyBlk.fixed = true
    end

    rect rgb(245,240,255)
    Note over JS: Phase C: Event Bindings
    JS->>JS: Bind estimation type radio
    JS->>JS: Bind customer add/edit buttons
    JS->>JS: Bind branch change handler
    end

    JS->>JS: Trigger branch_select.change()
    Note over JS: Triggers AJAX cascade →
```

---

## Level 1.3: AJAX Loading (Drill-Down)

```mermaid
sequenceDiagram
    participant JS as JavaScript
    participant API as API Endpoints
    participant G as Global Arrays

    Note over JS: Parallel AJAX Calls Begin

    par Group 1: Master Data
        JS->>API: GET get_tag_purities()
        API-->>JS: [{id:1, name:"22K", factor:0.916}, ...]
        JS->>G: purities[] = response
    and
        JS->>API: GET get_stones()
        API-->>JS: [{id:1, name:"Diamond"}, ...]
        JS->>G: stones[] = response
    and
        JS->>API: GET get_materials()
        API-->>JS: [{id:1, name:"Enamel"}, ...]
        JS->>G: materials[] = response
    end

    par Group 2: Branch-Specific
        JS->>API: POST get_metal_rates_by_branch(1)
        API-->>JS: {gold_22ct:5500, gold_18ct:4500, silver:75}
        JS->>G: metal_rates[] = response
    and
        JS->>API: POST get_employee(1)
        API-->>JS: [{emp_id:1, name:"John"}, ...]
        JS->>G: emp_details[] = response
    and
        JS->>API: POST get_wastage_settings()
        API-->>JS: [{min_wt:0, max_wt:50, wastage:12}, ...]
        JS->>G: wast_settings_details[] = response
    end

    par Group 3: Settings
        JS->>API: GET get_profile()
        API-->>JS: {allow_mc_edit:1, allow_va_edit:1}
        JS->>G: profile[] = response
    and
        JS->>API: GET getStoneRateSettings()
        API-->>JS: [{stone_id:1, rate_type:1, rate:500}]
        JS->>G: stone_rate_settings[] = response
    end

    Note over G: All 15+ global arrays populated
    Note over JS: Form fully initialized ✅
```

---

# 🆚 Comparison Summary

| Aspect          | Option C (Swimlane)      | Option D (Hierarchical)     |
| --------------- | ------------------------ | --------------------------- |
| **Best For**    | Seeing full flow at once | Deep-diving specific phases |
| **Complexity**  | One large diagram        | Multiple focused diagrams   |
| **Parallelism** | Shows clearly with `par` | Shows in sub-diagrams       |
| **Readability** | Good for overview        | Better for detailed study   |
| **Maintenance** | Update one diagram       | Update specific sub-diagram |
| **Navigation**  | Scroll through           | Click to drill-down         |

---

**Which style do you prefer?** Or should I combine elements of both?
