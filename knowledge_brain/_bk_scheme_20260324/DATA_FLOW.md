# Scheme Module — Data Flow Traces
> **Round**: 1 | **Date**: 2026-03-06

---

## Flow 1: Create Scheme (Add)

```
User → GET scheme/add
  → sch_form('Add')
    1. Check scheme limit: limitDB() → scheme_count()
    2. If limit exceeded → flash error, redirect
    3. Get discount settings → discount_db()
    4. Get GST settings → get_gstsettings()
    5. Load empty_record() (default data array, 130+ fields)
    6. Render form view

User → POST scheme/save
  → sch_post('Add')
    1. Read POST sch[], gst_data[], branch_data[], topUpSchemeChart[]
    2. Build $sch_info array (130+ fields):
       - Format numbers: number_format(amount, 2)
       - Conditional fields (DigiGold, flexible, lump sum, etc.)
       - Referral value formula: emp_refferal_value = percent ? amount * values / 100 : values
    3. BEGIN TRANSACTION
    4. Insert scheme → insert_scheme($sch_info)
       - SHOW COLUMNS FROM scheme (for default values)
       - INSERT → get insert_id
    5. Upload scheme image (if provided) → set_scheme_image()
    6. If success:
       a. Branch mapping (if branch_settings=1):
          - Loop branch_data → scheme_branch() inserts
       b. GST split-up:
          - Loop gst_data → insert_gstSplitup()
          - If type==NULL → update scheme.gst field
       c. Benefit chart (if apply_benefit_by_chart=1 && !apply_debit_on_preclose):
          - Loop POST['installmentchart'] → insertData('scheme_benefit_deduct_settings')
       d. Flexible installment settings:
          - Loop scheme_flexible → insertData('scheme_flexi_settings')
       e. Incentive chart (if emp/cus/agent referral enabled):
          - Loop POST['incentive_chart'] → insertData('scheme_incentive_settings')
       f. Pre-close deduction chart (if apply_debit_on_preclose=1):
          - Loop POST['installmentpreclosechart'] → insertData('scheme_debit_settings')
       g. Agent benefit chart (if agent_refferal=1):
          - Loop POST['agent_benefit_chart'] → insertData('scheme_agent_benefit')
       h. Employee closing incentive (if emp_refferal_incentive=1):
          ⚠️ BUG: Uses $id (which is empty in Add) instead of $res['id_scheme']
          - Loop POST['installmentchart_closing'] → insertData('emp_closing_incentive')
       i. GA benefit chart (if apply_adv_benefit=1):
          ⚠️ BUG: Calls deleteData before insert (but $id is empty in Add)
          - Loop POST['adv_chart'] → insertData('scheme_general_advance_benefit_settings')
       j. TopUp chart (if is_topup_scheme=1):
          - insert_batch('scheme_custom_payable_settings')
    7. Log: log_detail('insert')
    8. Trans check → COMMIT or ROLLBACK → redirect
```

## Flow 2: Edit Scheme

```
User → GET scheme/edit/:id
  → sch_form('Edit', $id)
    1. Get scheme data → get_scheme($id) (massive 25-line SQL join)
    2. Get adv_benefit_data → get_adv_benefit_data($id)
    3. Get flex_sch_data → get_flexible_ins_data($id)
    4. Get chartData → get_benfit_rdeduct_data($id)
    5. Get preclosechartdata → get_benfit_rdeduct_preclose__data($id)
    6. Get agentbenefitchart → get_agent_benefit__data($id)
    7. Get incentive_chart → get_incentive_data($id)
    8. Get gst_data → get_gstSplitupData($id)
    9. Get branch_data → get_branch_edit($id) via JSON encode
    10. Get discount settings
    11. Get closing_data → get_closing_scheme_data($id)
    12. Get enableDigi → enableDigiGold($id, $id_metal)
    13. Get topUpSchemeChart → getTopUpSchemeChart($id)
    14. Render form view with all data

User → POST scheme/update/:id
  → sch_post('Edit', $id)
    1. Read POST sch[], gst_data[], branch_data[], topUpSchemeChart[]
    2. Build $sch_info array (same 130+ fields as Add, with date_upd)
    3. BEGIN TRANSACTION
    4. Update scheme → update_scheme($sch_info, $id)
       - SHOW COLUMNS FROM scheme (for default values)
       - UPDATE WHERE id_scheme = $id
    5. Upload scheme image (if provided)
    6. Child table updates (DELETE-THEN-INSERT pattern):
       a. Agent benefit: delete_agent_benefit($id) → loop insert
       b. Incentive: delete_incentive_benefit($id) → loop insert
       c. Branches: delete_scheme_branch($id) → loop insert
       d. GST: update_gstSplitup($id) [soft delete] → loop insert
       e. Benefit chart: deleteData('scheme_benefit_deduct_settings') → loop insert
       f. Flexi settings: deleteData('scheme_flexi_settings') → loop insert
       g. Pre-close: delete_benfit_rdeduct_preclose($id) → loop insert
       h. Emp closing incentive: deleteData('emp_closing_incentive') → loop insert
       i. GA benefit: deleteData('scheme_general_advance_benefit_settings') → loop insert
       j. TopUp chart: topUpSchemeChartEditProcess($id) (soft delete + batch insert)
          ⚠️ BUG: Commits/rollbacks inside this method (L966), then outer commits again (L984)
    7. Log: log_detail('insert')
    8. Trans check → COMMIT or ROLLBACK → redirect
```

## Flow 3: Delete Scheme

```
User → GET scheme/delete/:id  ⚠️ GET = CSRF vulnerable
  → sch_post('Delete', $id)
    1. Check account records → check_acc_records($id)
    2. Log delete event
    3. If accounts exist → flash error, redirect (block delete)
    4. If no accounts:
       a. BEGIN TRANSACTION
       b. Delete GST split-up → DELETE WHERE id_scheme = $id
       c. Delete scheme → DELETE WHERE id_scheme = $id
       d. Trans check → COMMIT or ROLLBACK
    ⚠️ Note: Does NOT delete child tables:
       - scheme_benefit_deduct_settings
       - scheme_debit_settings
       - scheme_agent_benefit
       - scheme_incentive_settings
       - scheme_flexi_settings
       - emp_closing_incentive
       - scheme_general_advance_benefit_settings
       - scheme_custom_payable_settings
       - scheme_branch
```

## Flow 4: Get Scheme Business Data (AJAX)

```
AJAX → GET scheme/get_scheme/:id
  → ajax_get_scheme($id) → scheme_business($id)
    1. Get full scheme → get_scheme($id)
    2. Build response object with:
       - Core: id_scheme, scheme_name, code, metal, approval
       - Limits: sch_limit_value, flx_denomintion, min/max amount
       - Type detection: Weight/Amount/Amount-to-Weight/Flexible
       - Payment: Single/Multiple, min_chance/max_chance
       - Interest & Tax calculations
       - Lump sum weight slabs (if enabled)
       - TopUp settings
       - Account count: get_scheme_count()
       - Metal rate: get_metalrate_by_branch()
    3. Return JSON
```

## Flow 5: Get Active Schemes (AJAX — used by Account form)

```
AJAX → GET scheme/get_schemes
  → ajax_get_schemes() → get_schemes()
    1. Resolve branch:
       - Check branchwise_scheme, branch_settings
       - If uid==1 (superadmin): Show all with branch filter
       - Else: Restrict by branch
    2. If customerId provided:
       - Exclude schemes where customer already has DigiGold account
    3. SQL: SELECT ... FROM scheme LEFT JOIN scheme_branch/branch
    4. Return JSON array
```

## Flow 6: Batch GST Insert for Existing Schemes

```
AJAX → gstsplitupinsert()
    1. SELECT all scheme IDs from scheme
    2. Loop each scheme:
       - INSERT 4 rows into gst_splitup_detail (GST, SGST, CGST, IGST)
       - Hardcoded values: GST=3%, SGST=1.5%, CGST=1.5%, IGST=3%
    3. Return count
```
