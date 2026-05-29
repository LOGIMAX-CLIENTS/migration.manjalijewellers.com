# Workflow 1: Create New Estimation

> **Combined Swimlane + Hierarchical Documentation**  
> Entry Point: User clicks "Add Estimation" → `/admin_ret_estimation/estimation/add`

---

## 🎯 Master Overview

```mermaid
graph LR
    subgraph "Create Estimation Flow"
        A["👤 User Click"] --> B["🎮 Backend Load"]
        B --> C["📄 Render Form"]
        C --> D["⚡ JS Initialize"]
        D --> E["📡 AJAX Load"]
        E --> F["✅ Ready"]
    end

    B -.->|Detail 1.1| B1["Backend Load"]
    D -.->|Detail 1.2| D1["JS Initialize"]
    E -.->|Detail 1.3| E1["AJAX Loading"]

    style A fill:#e3f2fd
    style F fill:#c8e6c9
```

---

## 📊 Complete Swimlane Diagram

```mermaid
sequenceDiagram
    box rgb(227,242,253) User Layer
    participant U as 👤 User
    end
    box rgb(255,243,224) Browser Layer
    participant B as 🌐 Browser
    participant JS as ⚡ JavaScript
    end
    box rgb(232,245,233) Backend Layer
    participant C as 🎮 Controller
    participant M as 📦 Model
    end
    box rgb(252,228,236) Data Layer
    participant DB as 🗄️ Database
    end

    U->>B: Click "Add Estimation"
    B->>C: GET /estimation/add

    rect rgb(232,245,233)
    Note over C,DB: 📋 PHASE 1: Backend Data Loading
    C->>M: get_profile_settings(profile_id)
    M->>DB: SELECT * FROM ret_profile_settings
    DB-->>M: profile row
    M-->>C: profile{}

    loop 12 Settings
        C->>M: get_ret_settings(key)
        M->>DB: SELECT value FROM ret_settings
        DB-->>M: value
    end
    M-->>C: settings{}

    C->>M: get_employee_settings(uid)
    M->>DB: SELECT * FROM employee_settings
    DB-->>M: emp permissions
    M-->>C: emp_setting{}
    end

    C-->>B: Render form.php + $data

    rect rgb(255,243,224)
    Note over JS: 📋 PHASE 2: Frontend Initialization
    JS->>JS: Webcam.attach()
    JS->>JS: Select2, Datepickers init
    JS->>JS: Event bindings setup
    JS->>JS: Scroll handler setup
    end

    rect rgb(227,242,253)
    Note over JS,C: 📋 PHASE 3: Parallel AJAX Loading
    par Master Data
        JS->>C: get_tag_purities()
        C-->>JS: purities[]
    and
        JS->>C: get_stones()
        C-->>JS: stones[]
    and
        JS->>C: get_materials()
        C-->>JS: materials[]
    end

    par Branch Data
        JS->>C: get_metal_rates(branch)
        C->>M: getBranchMetalRates()
        M->>DB: SELECT FROM branch_rates
        C-->>JS: metal_rates{}
    and
        JS->>C: get_employee(branch)
        C-->>JS: emp_details[]
    and
        JS->>C: get_wastage_settings()
        C-->>JS: wast_settings[]
    end

    par Config Data
        JS->>C: get_profile()
        C-->>JS: profile{}
    and
        JS->>C: get_taxgroup_items()
        C-->>JS: tax_details[]
    end
    end

    JS->>JS: Store all in global arrays
    JS-->>B: Form Ready ✅
    B-->>U: Display Form
```

---

## 📋 Detail 1.1: Backend Load

### Sequence

```mermaid
sequenceDiagram
    participant C as Controller
    participant M as Model
    participant DB as Database

    Note over C: estimation('add') - Line 199

    rect rgb(255,248,225)
    Note right of C: Step 1: Profile Settings
    C->>M: get_profile_settings(session.profile)
    M->>DB: SELECT * FROM ret_profile_settings<br/>WHERE id_profile = ?
    DB-->>M: Profile row
    M-->>C: Returns profile object
    end

    rect rgb(225,245,254)
    Note right of C: Step 2: Load 12 Retail Settings
    C->>M: get_ret_settings('min_old_gold_rate')
    C->>M: get_ret_settings('max_old_gold_rate')
    C->>M: get_ret_settings('min_old_silver_rate')
    C->>M: get_ret_settings('max_old_silver_rate')
    C->>M: get_ret_settings('max_cash_allowed')
    C->>M: get_ret_settings('weightschemecaltype')
    C->>M: get_ret_settings('bulk_wastage_discount')
    C->>M: get_ret_settings('wastage_rate_type')
    C->>M: get_ret_settings('est_emp_select_req')
    C->>M: get_ret_settings('est_old_metal_remarks_req')
    C->>M: get_ret_settings('enable_sales_return_estimations')
    C->>M: get_ret_settings('chit_rate_calculation_type')
    end

    rect rgb(232,245,233)
    Note right of C: Step 3: Employee Settings
    C->>M: get_employee_settings(uid)
    M->>DB: SELECT * FROM employee_settings<br/>JOIN profiles ON ...
    DB-->>M: Employee permissions
    M-->>C: emp_setting{}

    C->>M: profileDB('get', profile_id)
    M-->>C: profile_setting{}
    end

    rect rgb(243,229,245)
    Note right of C: Step 4: Supporting Data
    C->>M: get_empty_record()
    M-->>C: Empty estimation template

    C->>M: getUOMDetails()
    M->>DB: SELECT * FROM uom WHERE status=1
    M-->>C: uom[]

    C->>M: get_stone_disc()
    M-->>C: stone discount %
    end

    C->>C: Render form.php with $data
```

### Data Structures Returned

```javascript
// $data array passed to view
{
  profile: {
    allow_mc_edit: 1,
    allow_va_edit: 1,
    allow_manual_rate: 0,
    allow_discount: 1
  },
  estimation: {
    estimation_id: '',
    cus_id: '',
    total_cost: 0,
    // ... empty template
  },
  min_old_gold_rate: 4500,
  max_old_gold_rate: 6000,
  max_cash_allowed: 200000,
  emp_setting: {
    id_profile: 2,
    default_branch: 1
  },
  uom: [
    {id: 1, name: 'Gram'},
    {id: 2, name: 'Carat'}
  ]
}
```

---

## 📋 Detail 1.2: JS Initialize

### Sequence

```mermaid
sequenceDiagram
    participant DOC as Document
    participant JS as JavaScript
    participant UI as UI Components
    participant EVT as Event System

    DOC->>JS: $(document).ready()

    rect rgb(255,243,224)
    Note over JS,UI: Phase A: UI Components

    alt Page is 'add'
        JS->>UI: Webcam.set({width:290, height:190})
        JS->>UI: Webcam.attach('#my_camera')
        UI-->>JS: Camera ready
    end

    JS->>UI: $('#status').bootstrapSwitch()
    JS->>UI: $('#profession').select2({placeholder})
    JS->>UI: $('#date_of_birth').datepicker({format:'yyyy-mm-dd'})
    JS->>UI: $('#date_of_wed').datepicker()
    end

    rect rgb(232,245,233)
    Note over JS,EVT: Phase B: Event Bindings
    JS->>EVT: Bind estimation_type radio change
    JS->>EVT: Bind add_customer click
    JS->>EVT: Bind edit_customer click
    JS->>EVT: Bind branch_select change
    JS->>EVT: Bind window scroll (sticky header)
    end

    rect rgb(227,242,253)
    Note over JS: Phase C: Initial Data Fetch
    JS->>JS: get_all_old_metal_rates()
    JS->>JS: get_old_metal_categories()
    end

    JS->>EVT: Trigger branch_select.change()
    Note over EVT: This cascades to AJAX loading →
```

### Pseudo-code

```
FUNCTION document.ready():

    // Phase A: UI Setup
    IF page == 'add':
        Webcam.set({width: 290, height: 190, format: 'jpg'})
        Webcam.attach('#my_camera')

    bootstrapSwitch('#status')
    select2('#profession', {placeholder: "Select Profession"})
    datepicker('#date_of_birth', {format: 'yyyy-mm-dd'})

    // Phase B: Event Bindings
    ON radio[estimation.esti_for].change:
        SWITCH value:
            CASE 1 (Customer):
                Show customer required marker
                Enable catalog/custom/old metal sections
            CASE 2 (Branch Transfer):
                Hide customer required
                Disable catalog/custom/old metal
            CASE 3 (Company):
                Show GST fields

    ON window.scroll:
        IF scrollTop > 300:
            stickyBlk.position = 'fixed'
        ELSE:
            stickyBlk.position = 'static'

    // Phase C: Initial Load
    AJAX get_all_old_metal_rates()
    AJAX get_old_metal_categories()

    // Trigger cascade
    trigger('#branch_select', 'change')

END FUNCTION
```

---

## 📋 Detail 1.3: AJAX Loading

### Sequence

```mermaid
sequenceDiagram
    participant JS as JavaScript
    participant API as API Endpoints
    participant G as Global Arrays

    Note over JS: branch_select.change triggered

    rect rgb(255,248,225)
    Note over JS,G: Group 1: Master Data (Parallel)
    par
        JS->>API: get_tag_purities()
        API-->>JS: [{id,name,factor}]
        JS->>G: purities[] ✓
    and
        JS->>API: get_stones()
        API-->>JS: [{stone_id,name,rate}]
        JS->>G: stones[] ✓
    and
        JS->>API: get_materials()
        API-->>JS: [{material_id,name}]
        JS->>G: materials[] ✓
    and
        JS->>API: get_ActiveUOM()
        API-->>JS: [{id,name}]
        JS->>G: uom_details[] ✓
    end
    end

    rect rgb(225,245,254)
    Note over JS,G: Group 2: Branch-Specific (Parallel)
    par
        JS->>API: get_metal_rates_by_branch(id)
        API-->>JS: {gold_22ct, gold_18ct, silver}
        JS->>G: metal_rates[] ✓
    and
        JS->>API: get_employee(branch_id)
        API-->>JS: [{emp_id, name, commission}]
        JS->>G: emp_details[] ✓
    and
        JS->>API: get_wastage_settings()
        API-->>JS: [{min_wt, max_wt, wastage%}]
        JS->>G: wast_settings_details[] ✓
    and
        JS->>API: get_taxgroup_items()
        API-->>JS: [{tax_id, name, rate, type}]
        JS->>G: tax_details[] ✓
    end
    end

    rect rgb(232,245,233)
    Note over JS,G: Group 3: Config Data (Parallel)
    par
        JS->>API: get_profile()
        API-->>JS: {allow_mc_edit, allow_va_edit}
        JS->>G: profile[] ✓
    and
        JS->>API: getStoneRateSettings()
        API-->>JS: [{stone_id, rate_type, rate}]
        JS->>G: stone_rate_settings[] ✓
    and
        JS->>API: getActive_quality_code()
        API-->>JS: [{id, code, name}]
        JS->>G: quality_code[] ✓
    and
        JS->>API: getFinancialYr()
        API-->>JS: {fin_year_code, from, to}
        JS->>G: fin_year[] ✓
    end
    end

    Note over G: ✅ All 15 global arrays ready
```

### Data Structures

```javascript
// Global arrays after AJAX completion

purities = [
  { id: 1, purity_name: "22K", purity_factor: 0.916, category: 1 },
  { id: 2, purity_name: "18K", purity_factor: 0.75, category: 1 },
  { id: 3, purity_name: "Silver 925", purity_factor: 0.925, category: 2 },
];

metal_rates = {
  gold_22ct: 5500,
  gold_18ct: 4500,
  silver_1gm: 75,
  platinum: 3200,
};

wast_settings_details = [
  { min_weight: 0, max_weight: 10, wastage_percent: 12 },
  { min_weight: 10, max_weight: 50, wastage_percent: 10 },
  { min_weight: 50, max_weight: 999, wastage_percent: 8 },
];

profile = {
  allow_mc_edit: 1, // Can edit making charges
  allow_va_edit: 1, // Can edit value addition
  allow_manual_rate: 0, // Cannot override metal rate
  allow_discount: 1, // Can apply discounts
};

tax_details = [
  { tax_group_id: 1, name: "GST 3%", rate: 3, type: "inclusive" },
  { tax_group_id: 2, name: "GST 5%", rate: 5, type: "inclusive" },
];
```

---

## 🔗 Collision Points

| This Workflow     | Shares With     | Shared Component                           |
| ----------------- | --------------- | ------------------------------------------ |
| Create Estimation | Edit Estimation | Same AJAX calls, profile loading           |
| Create Estimation | Add Tagged Item | `metal_rates[]`, `purities[]`, `profile[]` |
| Create Estimation | Calculate Cost  | `wast_settings_details[]`, `tax_details[]` |
| Create Estimation | Save Estimation | `fin_year[]`, `profile[]` for validation   |

---

## ✅ Workflow Complete Checklist

- [x] Master overview diagram
- [x] Full swimlane sequence
- [x] Backend load detail (1.1)
- [x] JS initialize detail (1.2)
- [x] AJAX loading detail (1.3)
- [x] Data structures documented
- [x] Collision points identified

---

**Ready for Workflow 2: Add Tagged Item?**
