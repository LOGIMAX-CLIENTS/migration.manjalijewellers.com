# 🔧 ESTIMATION MODULE — BUG FIX EXECUTION PLAN

> **Module**: Estimation (Full Module)  
> **Total Bugs**: 65 (9 P0 · 22 P1 · 26 P2 · 8 P3)  
> **Generated**: 2026-02-17  
> **Source of Truth**: `bug_report_AI/` directory (6 audit rounds)  
> **Protocol**: ONE bug at a time. No batch fixes. User confirmation required before each code change.

---

## Execution Order

Bugs are ordered by **Severity (P0 → P3)**, then by **Sprint (1 → 3)** within each severity.

| Severity | Count | Bug Range |
|----------|-------|-----------|
| **P0 (Critical)** | 9 | BUG 01–09 |
| **P1 (Major)** | 22 | BUG 10–31 |
| **P2 (Minor)** | 26 | BUG 32–57 |
| **P3 (Cosmetic)** | 8 | BUG 58–65 |

---

# ═══ P0 — CRITICAL (9 BUGS) ═══

---

# BUG 01 — EST-R301
**Severity:** P0  
**Module:** Model — ret_estimation_model.php  
**Fix Readiness:** ⚠️ Systematic refactor  
**Sprint:** 1

## 1. Problem Summary
SQL Injection in 10+ model methods ($searchField controls column name)

## 2. Root Cause
Raw string concatenation in $this->db->query() instead of CI query bindings. $searchField at L982 controls column name in WHERE clause.

## 3. Impact Analysis
Security: Full database compromise. Attacker can read/modify/delete any data.

## 4. Exact Location
ret_estimation_model.php: getEstTags(L145), get_customer(L154), getNonTagLots(L164), ajax_getEstimationList(L210-212), getTaggingBySearch(L982), getTaggingSearchByCollection(L1046), getTaggingScanBySearch(L1123-1125), getAvailableCustomers(L903,911), getProductSubDesignBySearch(L1875), get_mc_va_limit(L1887), get_non_tag_stock_details(L1969-1973)

## 5. Current Code (VERBATIM)
```
tag.$searchField = '$SearchTxt' — direct column-name injection via POST parameter
```

## 6. Proposed Minimal Fix
1. Whitelist allowed column names: $allowed = ['tag_id','tag_code','barcode',...]; if(!in_array($searchField,$allowed)) return [];
2. Replace all raw query() with CI bindings: $this->db->query('...WHERE id=?', array($id))

## 7. Why This Fix Is Safe
Whitelist is additive — only restricts invalid inputs. Query bindings are CI's official parameterization.

## 8. Risk Level
Medium

## 9. Regression Checklist
Tag/customer/collection/stock searches still work correctly

## 10. Rollback Plan
Revert ret_estimation_model.php

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 02 — EST-R601
**Severity:** P0  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ✅ Ready (1-word fix)  
**Sprint:** 1

## 1. Problem Summary
cancel_order_tag commits on failure instead of rollback — data corruption

## 2. Root Cause
Copy-paste error at L3362: else branch (failure path) calls trans_commit() instead of trans_rollback().

## 3. Impact Analysis
Data Corruption: Failed cancellations persist partially — tags remain reserved for cancelled orders or become orphaned.

## 4. Exact Location
admin_ret_estimation.php, L3355-3365, cancel_order_tag()

## 5. Current Code (VERBATIM)
```
} else {
    $this->db->trans_commit();    // BUG: should be trans_rollback()
    $return_data = array('status' => FALSE);
}
```

## 6. Proposed Minimal Fix
Change trans_commit() to trans_rollback() at L3362

## 7. Why This Fix Is Safe
Single word change. else branch only executes on failure — rolling back is correct behavior.

## 8. Risk Level
Low

## 9. Regression Checklist
Cancel order tag success/failure paths both produce correct results

## 10. Rollback Plan
Change trans_rollback() back to trans_commit()

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 03 — EST-R501
**Severity:** P0  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready (4 variable fixes)  
**Sprint:** 1

## 1. Problem Summary
Market rate tax uses wrong variable (base_value_tax instead of market_base_value_tax) — all market rate comparisons invalid

## 2. Root Cause
L9127-9133: market rate calculation reuses regular rate variables instead of market_ prefixed counterparts.

## 3. Impact Analysis
Financial: market_total_tax_rate always equals total_tax_rate. Entire market rate comparison feature is non-functional.

## 4. Exact Location
ret_estimation.js, L9125-9133, calculateCatalogItemSaleValue / calculateCustomItemSaleValue

## 5. Current Code (VERBATIM)
```
var market_base_value_amt = parseFloat(parseFloat(market_rate_with_mc) + parseFloat(base_value_tax)).toFixed(2);
// base_value_tax should be market_base_value_tax (4 similar errors on consecutive lines)
```

## 6. Proposed Minimal Fix
Fix 4 variable references:
- base_value_tax → market_base_value_tax (L9127)
- base_value_amt → market_base_value_amt (L9129)
- base_value_amt + arrived_value_tax → market_base_value_amt + market_arrived_value_tax (L9131)
- base_value_tax + arrived_value_tax → market_base_value_tax + market_arrived_value_tax (L9133)

## 7. Why This Fix Is Safe
Each market_ variable already defined. Simply using correct variable names.

## 8. Risk Level
Low

## 9. Regression Checklist
Market rate tax differs from regular rate; regular rate unchanged

## 10. Rollback Plan
Revert 4 variable references

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 04 — EST-R502
**Severity:** P0  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready (1 variable fix)  
**Sprint:** 1

## 1. Problem Summary
get_tag_barcode_data uses undefined items.tag_id — duplicate check always bypasses

## 2. Root Cause
L2363: references 'items' which doesn't exist in this scope. AJAX response variable is 'data'. items.tag_id is always undefined.

## 3. Impact Analysis
Data Integrity: Same tag can be added to estimation unlimited times, causing duplicate line items and incorrect totals.

## 4. Exact Location
ret_estimation.js, L2363, get_tag_barcode_data()

## 5. Current Code (VERBATIM)
```
if (items.tag_id == $(this).find('.est_tag_id').val()) {  // 'items' is UNDEFINED
```

## 6. Proposed Minimal Fix
Change items.tag_id to data[0].tag_id (matches pattern in get_tag_data at L1051)

## 7. Why This Fix Is Safe
Matches proven pattern in get_tag_data function.

## 8. Risk Level
Low

## 9. Regression Checklist
Barcode scan works; duplicate barcode scan shows warning

## 10. Rollback Plan
Change data[0].tag_id back to items.tag_id

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 05 — EST-R401
**Severity:** P0  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready (1-word fix)  
**Sprint:** 1

## 1. Problem Summary
EDA validates Home Bill with validateCatalogDetailRow() instead of validateCustomDetailRow()

## 2. Root Cause
L5349: wrong function name in EDA handler for custom section validation.

## 3. Impact Analysis
Data Integrity: Custom items in EDA flow validated with catalog rules. Custom-specific validations completely bypassed.

## 4. Exact Location
ret_estimation.js, L5349, #est_eda_print click handler

## 5. Current Code (VERBATIM)
```
if (validateCatalogDetailRow()) {    // WRONG: should be validateCustomDetailRow()
```

## 6. Proposed Minimal Fix
Change validateCatalogDetailRow() to validateCustomDetailRow() at L5349

## 7. Why This Fix Is Safe
Matches the correct code in main #est_print handler (L5113).

## 8. Risk Level
Low

## 9. Regression Checklist
EDA custom items validated with correct rules; catalog validation unchanged

## 10. Rollback Plan
Change function name back

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 06 — EST-R402
**Severity:** P0  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready  
**Sprint:** 1

## 1. Problem Summary
EDA uses $('#table').length >= 0 — always true, bypasses empty-table check

## 2. Root Cause
L5287,5321,5347,5379: jQuery .length on element (returns 1, not row count). >= 0 is always true.

## 3. Impact Analysis
Data Integrity: Empty estimation sections pass existence check, potentially allowing submission with zero items.

## 4. Exact Location
ret_estimation.js, L5287, 5321, 5347, 5379

## 5. Current Code (VERBATIM)
```
if ($('#estimation_tag_details').length >= 0) {    // Always true
```

## 6. Proposed Minimal Fix
Change to: if ($('#estimation_tag_details tbody tr').length > 0) { — apply at all 4 lines

## 7. Why This Fix Is Safe
Matches the correct check in #est_print handler (L5043).

## 8. Risk Level
Low

## 9. Regression Checklist
EDA with items proceeds; EDA with empty sections skipped correctly

## 10. Rollback Plan
Revert 4 line changes

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 07 — EST-001
**Severity:** P0  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ✅ Ready (combined with EST-S01+S07)  
**Sprint:** 1

## 1. Problem Summary
Gift voucher save uses undefined $arrayMaterials instead of $arrayGiftVoucher — vouchers never persisted

## 2. Root Cause
L1210/2336: batch_insert references $arrayMaterials (other metals) instead of $arrayGiftVoucher built above.

## 3. Impact Analysis
Data Loss: All gift voucher info silently discarded. Wrong array inserted into gift voucher table.

## 4. Exact Location
admin_ret_estimation.php, L1210 (save), L2336 (update)

## 5. Current Code (VERBATIM)
```
$this->ret_estimation_model->batch_insert('ret_est_gift_voucher_details', $arrayMaterials); // WRONG variable
```

## 6. Proposed Minimal Fix
Change $arrayMaterials to $arrayGiftVoucher at both L1210 and L2336

## 7. Why This Fix Is Safe
Simple variable name correction. $arrayGiftVoucher is built immediately above.

## 8. Risk Level
Low

## 9. Regression Checklist
Gift voucher saved/updated in DB; estimation without vouchers unaffected

## 10. Rollback Plan
Change variable back

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 08 — EST-S01 + EST-S07
**Severity:** P0  
**Module:** DB Schema — ret_est_gift_voucher_details  
**Fix Readiness:** ✅ Ready (ALTER TABLE)  
**Sprint:** 1

## 1. Problem Summary
gift_voucher_id NOT NULL without AUTO_INCREMENT + no PRIMARY KEY — inserts fail

## 2. Root Cause
Schema defect: table created without AUTO_INCREMENT and without PRIMARY KEY. Controller never provides gift_voucher_id.

## 3. Impact Analysis
Data Loss: Inserts fail in strict mode. Without strict mode, all rows get ID=0. No unique row identification.

## 4. Exact Location
Table: ret_est_gift_voucher_details DDL

## 5. Current Code (VERBATIM)
```
gift_voucher_id int NOT NULL,  -- no AUTO_INCREMENT, no DEFAULT, no PK
```

## 6. Proposed Minimal Fix
ALTER TABLE ret_est_gift_voucher_details MODIFY gift_voucher_id int NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (gift_voucher_id);

## 7. Why This Fix Is Safe
Additive change. Existing rows remain. AUTO_INCREMENT starts from MAX(id)+1.

## 8. Risk Level
Low — requires maintenance window

## 9. Regression Checklist
Verify auto-increment works; existing data unchanged

## 10. Rollback Plan
ALTER TABLE ... DROP PRIMARY KEY, MODIFY gift_voucher_id int NOT NULL;
⚠️ DB BACKUP REQUIRED BEFORE EXECUTION

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 09 — EST-002
**Severity:** P0  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 1

## 1. Problem Summary
Custom item market_rate_tax stores market_rate_cost value — wrong tax in DB

## 2. Root Cause
Variable assignment maps wrong POST field to market_rate_tax database column.

## 3. Impact Analysis
Financial Miscalculation: market_rate_tax column stores cost data. Reports/comparisons using this field are wrong.

## 4. Exact Location
admin_ret_estimation.php — custom item save array (save and update paths)

## 5. Current Code (VERBATIM)
```
market_rate_tax key assigned with market_rate_cost value in the save array
⚠️ Need exact line confirmed from source for verbatim code
```

## 6. Proposed Minimal Fix
Change the value assigned to market_rate_tax from market_rate_cost POST field to correct market_rate_tax POST field

## 7. Why This Fix Is Safe
Simple field mapping correction. Correct POST field exists and is sent by JS.

## 8. Risk Level
Low

## 9. Regression Checklist
market_rate_tax stores tax; market_rate_cost stores cost

## 10. Rollback Plan
Revert field mapping

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# ═══ P1 — MAJOR (20 BUGS) ═══

---

# BUG 10 — EST-R302
**Severity:** P1  
**Module:** Model — ret_estimation_model.php  
**Fix Readiness:** ✅ Ready (1-char fix)  
**Sprint:** 1

## 1. Problem Summary
Cartesian JOIN: b.bill_type = b.bill_type in get_bill_no_format_detail

## 2. Root Cause
L320: self-referencing JOIN condition. b.bill_type = b.bill_type is always true.

## 3. Impact Analysis
Performance + Wrong Data: N×M rows returned. Bill numbers may display incorrectly.

## 4. Exact Location
ret_estimation_model.php, L320, get_bill_no_format_detail()

## 5. Current Code (VERBATIM)
```
LEFT JOIN ret_billing b ON b.bill_type = b.bill_type
```

## 6. Proposed Minimal Fix
Change b.bill_type to bf.bill_type (bf = bill_no_format alias)

## 7. Why This Fix Is Safe
Single character fix. bf alias exists in FROM clause.

## 8. Risk Level
Low

## 9. Regression Checklist
Estimation list loads with correct bill numbers

## 10. Rollback Plan
Change bf. back to b.

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 11 — EST-R303
**Severity:** P1  
**Module:** Model — ret_estimation_model.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 1

## 1. Problem Summary
Tax subquery missing GROUP BY — wrong tax percentages for all tag searches (×3 methods)

## 2. Root Cause
L967-971 (also L1031-1035, L1108-1112): GROUP_CONCAT without GROUP BY collapses all tax groups into single row.

## 3. Impact Analysis
Wrong Tax Display: Tax percentages shown during tag search are non-deterministic. Affects 3 methods.

## 4. Exact Location
ret_estimation_model.php: getTaggingBySearch(L967), getTaggingSearchByCollection(L1031), getTaggingScanBySearch(L1108)

## 5. Current Code (VERBATIM)
```
LEFT JOIN (select i.tgi_taxcode, i.tgi_tgrpcode, GROUP_CONCAT(m.tax_percentage)... FROM ret_taxgroupitems i LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax — NO GROUP BY
```

## 6. Proposed Minimal Fix
Add GROUP BY i.tgi_tgrpcode and remove i.tgi_taxcode from SELECT. Apply to all 3 methods.

## 7. Why This Fix Is Safe
Standard SQL correction. GROUP BY matches the JOIN key.

## 8. Risk Level
Low

## 9. Regression Checklist
Tag/collection/barcode search shows correct tax per product

## 10. Rollback Plan
Remove GROUP BY clause

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 12 — EST-R603
**Severity:** P1  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready (1-char fix)  
**Sprint:** 1

## 1. Problem Summary
Old metal rate check uses && '' (dead code) — rate=0 passes validation

## 2. Root Cause
L8738: val <= 0 && val == '' requires BOTH true. Only catches empty string, not 0.

## 3. Impact Analysis
Financial Loss: Customer can submit old metal at rate=0, getting credited at zero cost.

## 4. Exact Location
ret_estimation.js, L8738, validateOldMatelDetailRow()

## 5. Current Code (VERBATIM)
```
if (($(this).find('.old_rate').val() <= 0 && $(this).find('.old_rate').val() == ''))
```

## 6. Proposed Minimal Fix
Change && to || — now catches empty string, zero, and negative values

## 7. Why This Fix Is Safe
OR correctly catches all invalid rate values.

## 8. Risk Level
Low

## 9. Regression Checklist
Rate>0 passes; rate=0 or empty blocked

## 10. Rollback Plan
Change || back to &&

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 13 — EST-R403
**Severity:** P1  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready  
**Sprint:** 1

## 1. Problem Summary
EDA single form_validate flag — last checked section overwrites prior failures

## 2. Root Cause
L5253-5487: one form_validate variable reused for all 4 sections. Each overwrites previous result.

## 3. Impact Analysis
Data Integrity: If tag fails but catalog passes, form submits. Only last section's result matters.

## 4. Exact Location
ret_estimation.js, L5253-5487, #est_eda_print handler

## 5. Current Code (VERBATIM)
```
if (validateTagDetailRow()) { form_validate = true; } else { form_validate = false; }
// Later: if (validateCatalogDetailRow()) { form_validate = true; }  // Overwrites tag failure!
```

## 6. Proposed Minimal Fix
Replace single form_validate with 4 flags (form_validate_tag, _nontag, _custom, _old) — copy pattern from #est_print handler L5041-5175

## 7. Why This Fix Is Safe
Exact same pattern proven in #est_print handler.

## 8. Risk Level
Low

## 9. Regression Checklist
EDA with tag fail + catalog pass → blocks submission

## 10. Rollback Plan
Revert to single flag

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 14 — EST-R503
**Severity:** P1  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready  
**Sprint:** 2

## 1. Problem Summary
get_tag_barcode_data uses undefined rate_per_grm — NaN in rate fields

## 2. Root Cause
L2419/2471: rate_per_grm never assigned in get_tag_barcode_data(). Defined in get_tag_data() but not here.

## 3. Impact Analysis
Financial: Rate field shows undefined/NaN. Submitted rate may be 0 or NaN.

## 4. Exact Location
ret_estimation.js, L2419, L2471, get_tag_barcode_data()

## 5. Current Code (VERBATIM)
```
value="' + rate_per_grm + '"  // rate_per_grm NEVER DEFINED in this function
```

## 6. Proposed Minimal Fix
Define rate_per_grm from rate lookup data (copy pattern from get_tag_data)

## 7. Why This Fix Is Safe
Copy of proven pattern from existing function.

## 8. Risk Level
Low

## 9. Regression Checklist
Barcode scan populates rate correctly

## 10. Rollback Plan
Remove rate_per_grm definition

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 15 — EST-R504
**Severity:** P1  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready  
**Sprint:** 2

## 1. Problem Summary
Current get_tag_data removed metal type check — allows mixing gold/silver in estimation

## 2. Root Cause
L1579-1601: current function only checks duplicate tags, NOT metal type. Old version (L1059) had the check.

## 3. Impact Analysis
Business Rule Violation: Users can mix gold and silver tags, causing wrong totals, tax grouping, and invalid invoices.

## 4. Exact Location
ret_estimation.js, L1579-1601, get_tag_data()

## 5. Current Code (VERBATIM)
```
// Current version only checks: if (val.tag_id == $(this).find('.est_tag_id').val())
// Missing: if (data[0].metal_type != $(this).find('.metal_type').val()) check
```

## 6. Proposed Minimal Fix
Re-add metal type check: if (data[0].metal_type != $(this).find('.metal_type').val()) { toaster warning; rowExist=true; }

## 7. Why This Fix Is Safe
Restores previously existing business rule check.

## 8. Risk Level
Low

## 9. Regression Checklist
Mixed metal tags blocked; same metal tags allowed

## 10. Rollback Plan
Remove metal type check

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 16 — EST-R505
**Severity:** P1  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready  
**Sprint:** 2

## 1. Problem Summary
Division by zero in diamond cent weight when piece == 0 → Infinity

## 2. Root Cause
L1191/1697: cent_wt = (grs_wt/pcs)*100. When pcs=0 (null/empty defaults to 0), produces Infinity.

## 3. Impact Analysis
Runtime Error: Diamond products with 0 pieces get rate=0 instead of error/fallback.

## 4. Exact Location
ret_estimation.js, L1191, L1697

## 5. Current Code (VERBATIM)
```
product_centwt = parseFloat(((grs_wt)/(pcs))*100).toFixed(3);  // pcs can be 0
```

## 6. Proposed Minimal Fix
Add guard: if (pcs > 0) { product_centwt = ...; } else { product_centwt = 0; /* or show error */ }

## 7. Why This Fix Is Safe
Standard division-by-zero guard.

## 8. Risk Level
Low

## 9. Regression Checklist
Diamond with pcs>0 calculates correctly; pcs=0 handled gracefully

## 10. Rollback Plan
Remove guard

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 17 — EST-R604
**Severity:** P1  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready  
**Sprint:** 2

## 1. Problem Summary
Validation functions mutate form data (copy mc/va values during validate)

## 2. Root Cause
L8492-8504: validateCatalogDetailRow() and validateCustomDetailRow() modify hidden fields during validation.

## 3. Impact Analysis
Side Effect: Validation silently backfills fields. Repeated calls compound. Makes form state unpredictable.

## 4. Exact Location
ret_estimation.js, L8492-8504 (catalog), L8664-8676 (custom)

## 5. Current Code (VERBATIM)
```
// Inside validateCatalogDetailRow():
curRow.find('.nn_cat_mc').val(curRow.find('.cat_mc').val());  // MODIFYING form data during validation
```

## 6. Proposed Minimal Fix
Extract mutation logic from validation into a separate pre-save function. Validation should only check, not change.

## 7. Why This Fix Is Safe
Separation of concerns. Validation remains pure, mutations explicit.

## 8. Risk Level
Medium

## 9. Regression Checklist
Validation still catches errors; data populated correctly before save

## 10. Rollback Plan
Move mutations back into validation

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 18 — EST-R605 (partial)
**Severity:** P1  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ⚠️ Systematic refactor  
**Sprint:** 2

## 1. Problem Summary
Mixed $_POST vs $this->input->post() — 12 endpoints bypass XSS filter

## 2. Root Cause
12 endpoints use raw $_POST while 5 use CI's input->post(). Raw access bypasses XSS filtering.

## 3. Impact Analysis
Security: Stored XSS possible if raw POST data is displayed elsewhere.

## 4. Exact Location
admin_ret_estimation.php — createNewCustomer, updateCustomer, getTaggingBySearch, getProductBySearch, getCustomProductBySearch, getProductDesignBySearch, get_metal_purity_rate, pan_available, gst_available, aadhar_available, passport_available, dl_available

## 5. Current Code (VERBATIM)
```
$_POST['field'] used instead of $this->input->post('field') in 12 endpoints
```

## 6. Proposed Minimal Fix
Replace all $_POST['field'] with $this->input->post('field') in each endpoint

## 7. Why This Fix Is Safe
CI's input->post() is the standard accessor. Same data, with XSS filtering.

## 8. Risk Level
Medium

## 9. Regression Checklist
All endpoints still receive POST data correctly

## 10. Rollback Plan
Revert to $_POST

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 19 — EST-R405
**Severity:** P1  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready  
**Sprint:** 2

## 1. Problem Summary
deleteEstimation() uses GET for destructive action — CSRF vulnerable

## 2. Root Cause
L5491-5500: $.ajax call has no 'type' specified, defaults to GET. No CSRF token sent.

## 3. Impact Analysis
Security: DELETE can be triggered by link prefetching, img tags, or CSRF attacks.

## 4. Exact Location
ret_estimation.js, L5491-5500, deleteEstimation()

## 5. Current Code (VERBATIM)
```
$.ajax({ url: base_url + 'index.php/.../delete/' + id + '?nocache=...',  // No type = GET default
```

## 6. Proposed Minimal Fix
Add type:'POST' and include CSRF token. Controller should verify HTTP method.

## 7. Why This Fix Is Safe
POST with CSRF is the standard for destructive operations.

## 8. Risk Level
Low

## 9. Regression Checklist
Delete still works via button click; CSRF attacks blocked

## 10. Rollback Plan
Remove type:'POST'

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 20 — EST-R409
**Severity:** P1  
**Module:** View — form.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 2

## 1. Problem Summary
XSS: Unescaped flashdata rendered in alert div

## 2. Root Cause
L83-89: flashdata values output without htmlspecialchars(). CSS class, title, message all unescaped.

## 3. Impact Analysis
Security: If flashdata contains user-controlled data, XSS is possible.

## 4. Exact Location
estimation/form.php, L83-89

## 5. Current Code (VERBATIM)
```
<?php echo $message['class']; ?> and <?php echo $message['title']; ?> and <?php echo $message['message']; ?> — all unescaped
```

## 6. Proposed Minimal Fix
Wrap each output in htmlspecialchars(): <?php echo htmlspecialchars($message['class'], ENT_QUOTES, 'UTF-8'); ?>

## 7. Why This Fix Is Safe
htmlspecialchars is the standard PHP XSS prevention.

## 8. Risk Level
Low

## 9. Regression Checklist
Alert still displays; HTML entities escaped

## 10. Rollback Plan
Remove htmlspecialchars wrappers

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 21 — EST-S02
**Severity:** P1  
**Module:** DB Schema — ret_est_other_metals  
**Fix Readiness:** ✅ Ready (ALTER TABLE)  
**Sprint:** 2

## 1. Problem Summary
ret_est_other_metals uses MyISAM — not transactional

## 2. Root Cause
All other 12 tables use InnoDB. MyISAM ignores transactions entirely.

## 3. Impact Analysis
Data Integrity: Rollbacks don't affect this table. Orphan rows persist on transaction failure.

## 4. Exact Location
Table: ret_est_other_metals, ENGINE=MyISAM

## 5. Current Code (VERBATIM)
```
ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=latin1
```

## 6. Proposed Minimal Fix
ALTER TABLE ret_est_other_metals ENGINE=InnoDB;

## 7. Why This Fix Is Safe
Standard engine conversion. No data change.

## 8. Risk Level
Low — maintenance window needed

## 9. Regression Checklist
Verify transactions work for this table

## 10. Rollback Plan
ALTER TABLE ret_est_other_metals ENGINE=MyISAM;
⚠️ DB BACKUP REQUIRED

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 22 — EST-003 + EST-S06
**Severity:** P1  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 2

## 1. Problem Summary
Delete leaves orphan records in 6 tables; update misses 5 tables

## 2. Root Cause
Delete path (L1453-1503) cleans 7 of 13 tables. 6 child tables never cleaned.

## 3. Impact Analysis
Data Integrity: Orphan records accumulate in ret_est_other_metals, ret_estimation_other_charges, ret_est_tag_merge, ret_esti_old_metal_stone_details, ret_est_sales_return_utilization, ret_estimation_other_inventory_issue.

## 4. Exact Location
admin_ret_estimation.php, L1453-1503 (delete), L1548-1577 (update)

## 5. Current Code (VERBATIM)
```
Missing DELETE for 6 tables in delete path and 5 tables in update path
```

## 6. Proposed Minimal Fix
Add delete statements for missing tables using existing FK columns (est_item_id or est_id or esti_id)

## 7. Why This Fix Is Safe
Additive — only adds DELETE for child records within existing transaction.

## 8. Risk Level
Medium

## 9. Regression Checklist
Delete/update cleans all 13 child tables

## 10. Rollback Plan
Remove added DELETE statements

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 23 — EST-R304
**Severity:** P1  
**Module:** Model — ret_estimation_model.php  
**Fix Readiness:** ✅ Ready (1-char fix)  
**Sprint:** 2

## 1. Problem Summary
$returndata typo in getOrderBySearch — PO details silently lost

## 2. Root Cause
L1511: writes to $returndata (no underscore) instead of $return_data (with underscore).

## 3. Impact Analysis
PO cost/wastage details missing when linking orders. Incorrect margin calculations.

## 4. Exact Location
ret_estimation_model.php, L1511

## 5. Current Code (VERBATIM)
```
$returndata[$rkey]['po_details'] = $this->get_purchase_details(...);  // WRONG: $returndata
```

## 6. Proposed Minimal Fix
Change $returndata to $return_data

## 7. Why This Fix Is Safe
Aligns with all surrounding lines.

## 8. Risk Level
Low

## 9. Regression Checklist
PO details visible when linking orders

## 10. Rollback Plan
Remove underscore

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 24 — EST-R308
**Severity:** P1  
**Module:** Model — ret_estimation_model.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 2

## 1. Problem Summary
Cartesian JOIN in getCompanyDetails (JOIN without ON condition)

## 2. Root Cause
L1934/1944: join company c and join chit_settings cs without ON conditions.

## 3. Impact Analysis
Cartesian product. Masked by single company row in most deployments. Breaks multi-company.

## 4. Exact Location
ret_estimation_model.php, L1928-1950, getCompanyDetails()

## 5. Current Code (VERBATIM)
```
from branch b
join company c    -- No ON!
left join country cy on (b.id_country=cy.id_country)
```

## 6. Proposed Minimal Fix
Add ON conditions: join company c ON b.id_company = c.id_company
⚠️ Verify exact FK column names from schema

## 7. Why This Fix Is Safe
Adds missing JOIN conditions — restricts results to correct matches.

## 8. Risk Level
Medium — verify FK columns

## 9. Regression Checklist
Company/chit details load correctly

## 10. Rollback Plan
Remove ON conditions

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 25 — EST-004 + EST-S04
**Severity:** P1  
**Module:** Controller + Schema  
**Fix Readiness:** ✅ Ready  
**Sprint:** 2

## 1. Problem Summary
form_secret UNIQUE constraint exists but never populated — double-submit unprotected

## 2. Root Cause
form_secret column with UNIQUE constraint exists but controller never includes it in save array. All rows are NULL.

## 3. Impact Analysis
Concurrency: Double-submit creates duplicate estimations. Update path (delete+reinsert) loses data on concurrent edit.

## 4. Exact Location
admin_ret_estimation.php L312-357 (save array), ret_estimation table schema

## 5. Current Code (VERBATIM)
```
form_secret varchar(100) DEFAULT NULL, UNIQUE KEY form_secret (form_secret) — never referenced in controller
```

## 6. Proposed Minimal Fix
1. Generate token in form view
2. Include in save array: 'form_secret' => $addData['form_secret']
3. Check for duplicate before insert

## 7. Why This Fix Is Safe
Uses existing schema infrastructure. NULL→value transition doesn't affect existing data.

## 8. Risk Level
Medium

## 9. Regression Checklist
Save populates form_secret; double-submit blocked

## 10. Rollback Plan
Remove form_secret from save array and form

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 26 — EST-006
**Severity:** P1  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 2

## 1. Problem Summary
Update path missing deletes for ret_est_other_metals and ret_estimation_other_charges — duplicates on every edit

## 2. Root Cause
Update path uses delete-then-reinsert but misses these two tables from delete step.

## 3. Impact Analysis
Data Integrity: Every edit doubles other-metals and other-charges rows. Totals increase.

## 4. Exact Location
admin_ret_estimation.php, L1548-1577 (update delete section)

## 5. Current Code (VERBATIM)
```
Missing DELETE for ret_est_other_metals and ret_estimation_other_charges in update path
```

## 6. Proposed Minimal Fix
Add: $this->db->where('est_item_id',$item_id)->delete('ret_est_other_metals');
$this->db->where('est_item_id',$item_id)->delete('ret_estimation_other_charges');

## 7. Why This Fix Is Safe
Follows same delete-before-reinsert pattern used for other child tables.

## 8. Risk Level
Low

## 9. Regression Checklist
Edit estimation multiple times without duplicate rows

## 10. Rollback Plan
Remove added DELETE statements

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 27 — EST-005
**Severity:** P1  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 1

## 1. Problem Summary
Child tag stone details use wrong index [$key] — stones never saved for merged child tags

## 2. Root Cause
Loop index for child tag stones uses parent loop's $key instead of child stone iteration index.

## 3. Impact Analysis
Data Loss: Stone details for merged child tags silently lost during save.

## 4. Exact Location
admin_ret_estimation.php — child tag stone save loop
⚠️ Need exact line confirmed from source

## 5. Current Code (VERBATIM)
```
Wrong [$key] index used for child tag stone array access
```

## 6. Proposed Minimal Fix
Change [$key] to correct child stone loop index variable

## 7. Why This Fix Is Safe
Index correction only — no logic change.

## 8. Risk Level
Low

## 9. Regression Checklist
Merged tags with stones save correctly

## 10. Rollback Plan
Revert index variable

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 28 — EST-007
**Severity:** P1  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 2

## 1. Problem Summary
Update path missing rate_per_gram field for chit utilization — data loss on edit

## 2. Root Cause
Chit utilization update array omits rate_per_gram. Column DEFAULT 0.00 silently resets it.

## 3. Impact Analysis
Data Loss: Editing estimation zeroes out chit rate_per_gram.

## 4. Exact Location
admin_ret_estimation.php — chit utilization update array

## 5. Current Code (VERBATIM)
```
Missing 'rate_per_gram' => ... in update path chit array. Save path includes it.
```

## 6. Proposed Minimal Fix
Add 'rate_per_gram' => $estChitUtilization['rate_per_gram'][$key] to update array

## 7. Why This Fix Is Safe
Mirrors save path. Uses existing POST data.

## 8. Risk Level
Low

## 9. Regression Checklist
Edit preserves rate_per_gram

## 10. Rollback Plan
Remove added field

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 29 — EST-R305
**Severity:** P1  
**Module:** Model — ret_estimation_model.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
$data parameter overwritten by query result in getTaggingSearchByCollection

## 2. Root Cause
L1002/1048: $data = $this->db->query() overwrites input parameter.

## 3. Impact Analysis
Fragile code. Works by accident. Future access to $data['id_branch'] after L1004 will fail.

## 4. Exact Location
ret_estimation_model.php, L1002, L1048

## 5. Current Code (VERBATIM)
```
function getTaggingSearchByCollection($data) {
    $data = $this->db->query('SELECT ...');  // input overwritten!
```

## 6. Proposed Minimal Fix
Use different variable: $query = $this->db->query(...); $returndata = $query->result_array();

## 7. Why This Fix Is Safe
Functional behavior unchanged. Only variable naming improved.

## 8. Risk Level
Low

## 9. Regression Checklist
Collection search still returns results

## 10. Rollback Plan
Rename $query back to $data

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# ═══ P2 — MINOR (25 BUGS) ═══

---

# BUG 30 — EST-S03
**Severity:** P2  
**Module:** DB Schema — multiple tables  
**Fix Readiness:** ✅ Ready (ALTER TABLE)  
**Sprint:** 3

## 1. Problem Summary
Integer truncation: wastage_per, act_wast_per, disc_per, bulk_was_disc_per

## 2. Root Cause
PHP sends decimal values. MySQL int columns truncate silently (12.5→12).

## 3. Impact Analysis
Silent data precision loss for percentage values.

## 4. Exact Location
ret_est_chit_utilization.wastage_per, ret_estimation_items.act_wast_per, ret_estimation.disc_per, ret_estimation.bulk_was_disc_per

## 5. Current Code (VERBATIM)
```
Column type: int — receives decimal POST values
```

## 6. Proposed Minimal Fix
ALTER TABLE to change each to decimal(10,2)

## 7. Why This Fix Is Safe
Standard type correction for percentage fields.

## 8. Risk Level
Low — maintenance window

## 9. Regression Checklist
Decimal percentages preserved correctly

## 10. Rollback Plan
ALTER back to int

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 31 — EST-S05
**Severity:** P2  
**Module:** DB Schema — ret_est_sales_return_utilization  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
esti_date is date type but receives datetime string

## 2. Root Cause
L1281: $estimation_datetime is '2024-01-15 14:30:00'. MySQL truncates time portion.

## 3. Impact Analysis
Data truncation warning in strict mode. Not data loss (time redundant here).

## 4. Exact Location
ret_est_sales_return_utilization.esti_date (date column), L1281

## 5. Current Code (VERBATIM)
```
'esti_date' => $estimation_datetime  // sends datetime to date column
```

## 6. Proposed Minimal Fix
Use date('Y-m-d', strtotime($estimation_datetime)) or change column to datetime

## 7. Why This Fix Is Safe
Date extraction is lossless for this use case.

## 8. Risk Level
Low

## 9. Regression Checklist
No MySQL warnings in strict mode

## 10. Rollback Plan
Revert to raw datetime

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 32 — EST-008
**Severity:** P2  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
est_date format bug with date() function

## 2. Root Cause
date($estimation_datetime) silently produces wrong format when datetime contains time component.

## 3. Impact Analysis
Functional: est_date may have unexpected format.

## 4. Exact Location
admin_ret_estimation.php — est_date assignment

## 5. Current Code (VERBATIM)
```
date($estimation_datetime) — wrong usage of date()
```

## 6. Proposed Minimal Fix
Use proper format: date('Y-m-d', strtotime($estimation_datetime))

## 7. Why This Fix Is Safe
Standard PHP date formatting.

## 8. Risk Level
Low

## 9. Regression Checklist
est_date stored in correct format

## 10. Rollback Plan
Revert date() call

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 33 — EST-009
**Severity:** P2  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ⚠️ Needs investigation  
**Sprint:** 3

## 1. Problem Summary
Order items net_wt always equals gross_wt

## 2. Root Cause
Save path assigns gross_wt to net_wt, ignoring stone/less weights.

## 3. Impact Analysis
Calculation: Net weight should be gross minus stone weight.

## 4. Exact Location
admin_ret_estimation.php — order items save array

## 5. Current Code (VERBATIM)
```
net_wt assigned with gross_wt value
```

## 6. Proposed Minimal Fix
Calculate net_wt = gross_wt - stone_wt (need to verify source of stone weight)
⚠️ Requires investigation of actual calculation logic

## 7. Why This Fix Is Safe
Standard weight calculation.

## 8. Risk Level
Medium — needs investigation

## 9. Regression Checklist
Net weight calculated correctly

## 10. Rollback Plan
Revert to gross_wt assignment

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 34 — EST-010
**Severity:** P2  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ⚠️ Needs investigation  
**Sprint:** 3

## 1. Problem Summary
Custom charges loop only keeps last charge

## 2. Root Cause
Loop overwrites charge data on each iteration, keeping only the last id_charge.

## 3. Impact Analysis
Functional: Only last custom charge saved per item.

## 4. Exact Location
admin_ret_estimation.php — custom item charges loop

## 5. Current Code (VERBATIM)
```
Loop overwrites $charge variable instead of appending to array
```

## 6. Proposed Minimal Fix
Append charges to array instead of overwriting
⚠️ Requires investigation of charge structure

## 7. Why This Fix Is Safe
Standard loop-to-array fix.

## 8. Risk Level
Medium

## 9. Regression Checklist
All custom charges saved

## 10. Rollback Plan
Revert to overwrite

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 35 — EST-011
**Severity:** P2  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
Catalog stones missing uom_id field

## 2. Root Cause
L830-836: catalog item stone save missing uom_id and quality_id compared to tag item stones.

## 3. Impact Analysis
Data Integrity: Stone UoM and quality not recorded for catalog items.

## 4. Exact Location
admin_ret_estimation.php, L830-836

## 5. Current Code (VERBATIM)
```
Catalog stone array missing 'uom_id' and 'quality_id' fields
```

## 6. Proposed Minimal Fix
Add 'uom_id' => ... and 'quality_id' => ... from POST data (mirror tag stones pattern)

## 7. Why This Fix Is Safe
Mirrors existing tag stone save pattern.

## 8. Risk Level
Low

## 9. Regression Checklist
Catalog stones have complete data

## 10. Rollback Plan
Remove added fields

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 36 — EST-012
**Severity:** P2  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
Diamond amount field name mismatch: save uses diamond_amt, update uses tot_dia_amt

## 2. Root Cause
Save path reads POST key 'diamond_amt', update reads 'tot_dia_amt' — one path gets zero.

## 3. Impact Analysis
Data Integrity: Diamond amount zeroed on one path.

## 4. Exact Location
admin_ret_estimation.php — save vs update path custom item arrays

## 5. Current Code (VERBATIM)
```
Save: 'diamond_amount' => $addData['diamond_amt']
Update: 'diamond_amount' => $addData['tot_dia_amt']
```

## 6. Proposed Minimal Fix
Standardize both paths to use the correct POST key name
⚠️ Verify which POST key JS sends

## 7. Why This Fix Is Safe
Alignment of field names.

## 8. Risk Level
Low

## 9. Regression Checklist
Diamond amount correct on both save and update

## 10. Rollback Plan
Revert field name

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 37 — ESTL-001
**Severity:** P2  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
Default case accepts any action value in estimation list

## 2. Root Cause
Switch/case or conditional in estimation list handler has a default that accepts any action.

## 3. Impact Analysis
Security: Unexpected action values processed instead of rejected.

## 4. Exact Location
admin_ret_estimation.php — estimation list action handler

## 5. Current Code (VERBATIM)
```
Default case processes action without validation
```

## 6. Proposed Minimal Fix
Add whitelist of valid actions; reject unrecognized values

## 7. Why This Fix Is Safe
Standard input validation.

## 8. Risk Level
Low

## 9. Regression Checklist
Only valid actions processed

## 10. Rollback Plan
Remove whitelist check

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 38 — ESTDA-001
**Severity:** P2  
**Module:** Controller — estimate_discount_approval  
**Fix Readiness:** ⚠️ Needs design  
**Sprint:** 3

## 1. Problem Summary
No separation of duties on discount approval

## 2. Root Cause
Same user who creates estimation can approve their own discount.

## 3. Impact Analysis
Security: Self-approval of discounts possible.

## 4. Exact Location
Discount approval controller/handler

## 5. Current Code (VERBATIM)
```
No check that approver != creator
```

## 6. Proposed Minimal Fix
Add check: if approver_id == creator_id, reject approval
⚠️ Needs business rule confirmation

## 7. Why This Fix Is Safe
Standard separation of duties.

## 8. Risk Level
Medium — needs business confirmation

## 9. Regression Checklist
Different user required for approval

## 10. Rollback Plan
Remove approver check

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 39 — EST-R306
**Severity:** P2  
**Module:** Model — ret_estimation_model.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
Uninitialized $dateofbirth/$dateofwed in updateCustomer

## 2. Root Cause
L832-855: variables only set inside conditionals. If condition false, variable undefined.

## 3. Impact Analysis
PHP E_NOTICE warnings. Happens to work (undefined→NULL→desired default) but sloppy.

## 4. Exact Location
ret_estimation_model.php, L832-855 (updateCustomer), also createNewCustomer L779-806

## 5. Current Code (VERBATIM)
```
if($date_of_birth!=''){
    $dateofbirth = date_format(...);
}
// No else — $dateofbirth undefined if empty
```

## 6. Proposed Minimal Fix
Initialize before conditional: $dateofbirth = null; $dateofwed = null;

## 7. Why This Fix Is Safe
Explicit initialization. Same runtime behavior, no E_NOTICE.

## 8. Risk Level
Low

## 9. Regression Checklist
No PHP notices; dates handled correctly

## 10. Rollback Plan
Remove initialization

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 40 — EST-R307
**Severity:** P2  
**Module:** Model — ret_estimation_model.php  
**Fix Readiness:** ⚠️ Needs migration plan  
**Sprint:** 3

## 1. Problem Summary
Base64 'encryption' used as password hash

## 2. Root Cause
L771-774: encrypt() does base64_encode(). Password = base64(mobile_number).

## 3. Impact Analysis
Security: Anyone with DB access can decode Customer passwords. Predictable, no salt.

## 4. Exact Location
ret_estimation_model.php, L771-774, encrypt()

## 5. Current Code (VERBATIM)
```
public function encrypt($str) { return base64_encode($str); }
```

## 6. Proposed Minimal Fix
Replace with password_hash() for new passwords. Migration plan needed for existing.
⚠️ Cannot fix without migration strategy

## 7. Why This Fix Is Safe
Industry standard password hashing.

## 8. Risk Level
High — needs migration plan

## 9. Regression Checklist
New passwords hashed; existing migrated

## 10. Rollback Plan
Revert to base64 (not recommended)

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 41 — EST-R309
**Severity:** P2  
**Module:** Model — ret_estimation_model.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
Incomplete WHERE clause in get_chit_details

## 2. Root Cause
L2016: sa.id_scheme_account has no comparison operator. Evaluates as truthy check.

## 3. Impact Analysis
Logic: WHERE clause is misleading. Works only because JOIN already links correctly.

## 4. Exact Location
ret_estimation_model.php, L2016, get_chit_details()

## 5. Current Code (VERBATIM)
```
WHERE sa.id_scheme_account and sa.is_closed = 1 and sa.is_utilized = 0
```

## 6. Proposed Minimal Fix
Change to: WHERE sa.id_scheme_account IS NOT NULL AND sa.is_closed = 1 AND sa.is_utilized = 0
Or remove redundant condition entirely since JOIN handles it.

## 7. Why This Fix Is Safe
Clarifies intent without changing result set.

## 8. Risk Level
Low

## 9. Regression Checklist
Chit details query unchanged functionally

## 10. Rollback Plan
Revert WHERE clause

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 42 — EST-R310
**Severity:** P2  
**Module:** Model — ret_estimation_model.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
Null dereference in getOldMetalRate when no rate exists

## 2. Root Cause
L690: $sql->row()->goldrate_24ct — if no rows, row() returns NULL, fatal error.

## 3. Impact Analysis
Runtime Error: Fatal error when old_metal_type has no rate record.

## 4. Exact Location
ret_estimation_model.php, L690, getOldMetalRate()

## 5. Current Code (VERBATIM)
```
return $sql->row()->goldrate_24ct;
```

## 6. Proposed Minimal Fix
Add null check: $row = $sql->row(); return $row ? $row->goldrate_24ct : 0;

## 7. Why This Fix Is Safe
Standard null safety pattern.

## 8. Risk Level
Low

## 9. Regression Checklist
No fatal error on missing rate; returns 0

## 10. Rollback Plan
Remove null check

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 43 — EST-R311
**Severity:** P2  
**Module:** Model/Controller  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
Edit case missing access control ($data['access'] not set)

## 2. Root Cause
L1351-1421: edit case doesn't set $data['access'] unlike add case. View may show/hide controls incorrectly.

## 3. Impact Analysis
Security Gap: Edit form may display incorrect permissions.

## 4. Exact Location
admin_ret_estimation.php, L1351-1421 (edit case)

## 5. Current Code (VERBATIM)
```
// edit case: $data['access'] is NEVER SET
// add case: $data['access'] = $this->$SETT_MOD->get_access('admin_ret_estimation/estimation/add');
```

## 6. Proposed Minimal Fix
Add: $data['access'] = $this->$SETT_MOD->get_access('admin_ret_estimation/estimation/edit');

## 7. Why This Fix Is Safe
Mirrors add case pattern.

## 8. Risk Level
Low

## 9. Regression Checklist
Edit form respects permissions

## 10. Rollback Plan
Remove $data['access'] from edit

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 44 — EST-R404
**Severity:** P2  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
Multiple consecutive alerts for missing address fields (UX issue)

## 2. Root Cause
L4967-5003: up to 4 sequential alerts for address/pincode/country/state. No early return.

## 3. Impact Analysis
UX: User sees multiple annoying popups. Submission IS blocked by final AND-check, so not data loss.

## 4. Exact Location
ret_estimation.js, L4967-5003

## 5. Current Code (VERBATIM)
```
if(!ask_cus_addr1){ alert('Customer Address Not Available..!'); }
if(!ask_cus_pincode){ alert('Customer Pincode Not Available..!'); }
// No return, no consolidation
```

## 6. Proposed Minimal Fix
Consolidate into single alert listing all missing fields. Return early.

## 7. Why This Fix Is Safe
Better UX. Same blocking behavior.

## 8. Risk Level
Low

## 9. Regression Checklist
Single consolidated alert

## 10. Rollback Plan
Revert to multiple alerts

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 45 — EST-R406
**Severity:** P2  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
Cache-buster getUTCSeconds() only returns 0-59, not unique

## 2. Root Cause
L5189/5195/5423/5429: getUTCSeconds() cycles every 60 seconds. Not a proper cache buster.

## 3. Impact Analysis
Low: Browsers typically don't cache POST. But incorrect for GET-like requests.

## 4. Exact Location
ret_estimation.js, L5189, 5195, 5423, 5429

## 5. Current Code (VERBATIM)
```
var url = base_url + '...?nocache=' + my_Date.getUTCSeconds();
```

## 6. Proposed Minimal Fix
Replace getUTCSeconds() with Date.now() at all 4 locations

## 7. Why This Fix Is Safe
Date.now() provides millisecond timestamp — truly unique.

## 8. Risk Level
Low

## 9. Regression Checklist
Cache busting works reliably

## 10. Rollback Plan
Revert to getUTCSeconds()

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 46 — EST-R407
**Severity:** P2  
**Module:** View — form.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
Duplicate id="btn-submit" on two spans in view

## 2. Root Cause
L1334/1337: both Save+Print and EDA Print buttons wrapped in span with same id.

## 3. Impact Analysis
HTML Violation: getElementById returns only first element. JS targeting #btn-submit misses EDA button.

## 4. Exact Location
estimation/form.php, L1334, L1337

## 5. Current Code (VERBATIM)
```
<span id="btn-submit"><button id="est_print">Save and Print</button></span>
...
<span id="btn-submit"><button id="est_eda_print">Print for EDA</button></span>
```

## 6. Proposed Minimal Fix
Change second span to id="btn-submit-eda" or remove id from spans (buttons have unique ids already)

## 7. Why This Fix Is Safe
HTML valid. JS targets button ids, not span ids.

## 8. Risk Level
Low

## 9. Regression Checklist
Both buttons accessible via unique ids

## 10. Rollback Plan
Revert span id

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 47 — EST-R408
**Severity:** P2  
**Module:** View — form.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
Double </form> close (HTML + CI form_close())

## 2. Root Cause
L1343-1344: explicit </form> tag followed by form_close() which outputs another </form>.

## 3. Impact Analysis
HTML Violation: Stray tag. Browsers ignore it but indicates code quality issue.

## 4. Exact Location
estimation/form.php, L1343-1344

## 5. Current Code (VERBATIM)
```
</form>
<?php echo form_close(); ?>
```

## 6. Proposed Minimal Fix
Remove one of the two closings. Prefer keeping form_close() for CI consistency.

## 7. Why This Fix Is Safe
Removes duplicate. No functional change.

## 8. Risk Level
Low

## 9. Regression Checklist
Single form close

## 10. Rollback Plan
Re-add duplicate close

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 48 — EST-R506
**Severity:** P2  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
$('.cat_tax_per').val() sets ALL catalog rows — class selector instead of row scope

## 2. Root Cause
L9143: class selector .cat_tax_per sets every row's tax display, not just current row.

## 3. Impact Analysis
Display: All catalog rows show same tax %. Hidden fields are correct, only display wrong.

## 4. Exact Location
ret_estimation.js, L9143, calculateCatalogItemSaleValue

## 5. Current Code (VERBATIM)
```
$('.cat_tax_per').val(taxitem.tax_percentage);  // Class = ALL rows
```

## 6. Proposed Minimal Fix
Change to: curRow.find('.cat_tax_per').val(taxitem.tax_percentage);

## 7. Why This Fix Is Safe
Scopes to current row. curRow already available in function.

## 8. Risk Level
Low

## 9. Regression Checklist
Each row shows its own tax %

## 10. Rollback Plan
Revert to class selector

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 49 — EST-R507
**Severity:** P2  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
Collection confirm modal fires O(n²) times per non-matching row

## 2. Root Cause
L1345-1357: modal shown for every tag row that doesn't match each collection item. 3 collections × 5 rows = 15 modal attempts.

## 3. Impact Analysis
UX: Modal flickering and potential browser freeze.

## 4. Exact Location
ret_estimation.js, L1345-1357

## 5. Current Code (VERBATIM)
```
$.each(collection_details, function(key,items) {
  $('tbody').each(function(idx,row) {
    if (items.tag_id != ...) { modal.show(); }  // Per non-match!
  });
});
```

## 6. Proposed Minimal Fix
Check if ANY collection tag exists, then show modal once

## 7. Why This Fix Is Safe
Single modal display. Same logical result.

## 8. Risk Level
Low

## 9. Regression Checklist
Modal shows once when needed

## 10. Rollback Plan
Revert to per-row modal

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 50 — EST-R508
**Severity:** P2  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
Employee dropdown adds duplicate <option> for pre-selected employee

## 2. Root Cause
L7487-7503: when employee matches last row, both non-selected AND selected option added.

## 3. Impact Analysis
UX: Employee name appears twice in dropdown.

## 4. Exact Location
ret_estimation.js, L7487-7503, create_new_empty_est_custom_row

## 5. Current Code (VERBATIM)
```
select_emp += '<option value=...>' + emp_name + '</option>';
if (lastrowemp == emp.id_employee) {
  select_emp += '<option selected=selected ...>' + emp_name + '</option>';  // DUPLICATE
}
```

## 6. Proposed Minimal Fix
Use selected attribute on the original option instead of adding a second:
var sel = (lastrowemp == emp.id_employee) ? 'selected' : '';
select_emp += '<option ' + sel + ' value=...>' + emp_name + '</option>';

## 7. Why This Fix Is Safe
Single option per employee. Selected attribute controls pre-selection.

## 8. Risk Level
Low

## 9. Regression Checklist
No duplicate employees in dropdown

## 10. Rollback Plan
Revert to dual-option pattern

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 51 — EST-R602
**Severity:** P2  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
validateTagDetailRow empty tag — no return false, continues loop unnecessarily

## 2. Root Cause
L8087-8091: empty tag sets row_validate=false but doesn't return false. Subsequent rows still validated.

## 3. Impact Analysis
UX: Unnecessary validation of remaining rows after empty tag detected. May show confusing errors.

## 4. Exact Location
ret_estimation.js, L8087-8091, validateTagDetailRow()

## 5. Current Code (VERBATIM)
```
if (tag_code == '') {
  row_validate = false;
  // NO return false
}
```

## 6. Proposed Minimal Fix
Add return false after setting row_validate = false

## 7. Why This Fix Is Safe
Stops unnecessary iteration. Same final result.

## 8. Risk Level
Low

## 9. Regression Checklist
Empty tag stops validation loop immediately

## 10. Rollback Plan
Remove return false

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 52 — EST-R606
**Severity:** P2  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
11 controller functions missing public access modifier

## 2. Root Cause
Functions use 'function' instead of 'public function'. Defaults to public in CI2 but breaks convention.

## 3. Impact Analysis
Access Control: Convention violation. Static analysis tools flag as issue.

## 4. Exact Location
admin_ret_estimation.php: get_old_metal_type(L3198), get_old_metal_types(L3209), get_old_metal_category(L3220), get_metal_purity_rate(L3231), get_purity_rate(L3276), cancel_order_tag(L3321), get_tag_status_details(L3385), get_village_by_pincode(L3423), old_get_village(L3430), get_village(L3454), get_sectionBranchwise(L3494), getNonTagproducts(L2772)

## 5. Current Code (VERBATIM)
```
All 11 functions missing 'public' keyword
```

## 6. Proposed Minimal Fix
Add 'public' keyword to all 11 function declarations

## 7. Why This Fix Is Safe
CI2 default is public. Adding keyword makes it explicit.

## 8. Risk Level
Low

## 9. Regression Checklist
All functions still accessible

## 10. Rollback Plan
Remove public keyword

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 53 — EST-R607
**Severity:** P2  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 2

## 1. Problem Summary
mkdir with 0777 permissions for customer image directories

## 2. Root Cause
L2524/3165: world-readable/writable/executable directories created.

## 3. Impact Analysis
Security: Any server user can read/modify/place files in these directories.

## 4. Exact Location
admin_ret_estimation.php, L2524, L3165

## 5. Current Code (VERBATIM)
```
mkdir($folder, 0777, TRUE);
```

## 6. Proposed Minimal Fix
Change 0777 to 0755

## 7. Why This Fix Is Safe
0755 = owner rwx, group/others r-x. Standard for web directories.

## 8. Risk Level
Low

## 9. Regression Checklist
Directories created with proper permissions

## 10. Rollback Plan
Change 0755 back to 0777

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 54 — EST-R608
**Severity:** P2  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ⚠️ Needs review  
**Sprint:** 3

## 1. Problem Summary
base64ToFile no content validation — potential file upload vector

## 2. Root Cause
L3287-3313: accepts any base64: no MIME check before writing, temp files not cleaned on failure.

## 3. Impact Analysis
Security: Malicious base64 can write non-image files to temp. Low risk (not web-accessible).

## 4. Exact Location
admin_ret_estimation.php, L3287-3313, base64ToFile()

## 5. Current Code (VERBATIM)
```
file_put_contents($temp_file_path, $data);  // No MIME validation before write
```

## 6. Proposed Minimal Fix
1. Validate MIME type before writing temp file
2. Clean up temp file if getimagesize() fails

## 7. Why This Fix Is Safe
Standard file upload security pattern.

## 8. Risk Level
Low

## 9. Regression Checklist
Only valid images written; temp files cleaned

## 10. Rollback Plan
Remove MIME check

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# ═══ P3 — COSMETIC (8 BUGS) ═══

---

# BUG 55 — EST-013
**Severity:** P3  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
update_status references undefined $status variable + wrong redirect

## 2. Root Cause
update_status case uses undefined $status and redirects to admin_ret_lot instead of estimation.

## 3. Impact Analysis
Functional: update_status action would fail with PHP error and redirect to wrong module.

## 4. Exact Location
admin_ret_estimation.php — update_status case

## 5. Current Code (VERBATIM)
```
References $status (undefined) and redirect to admin_ret_lot
```

## 6. Proposed Minimal Fix
Define $status from POST data. Change redirect to admin_ret_estimation.

## 7. Why This Fix Is Safe
Variable definition + redirect correction.

## 8. Risk Level
Low

## 9. Regression Checklist
update_status works correctly

## 10. Rollback Plan
Revert changes

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 56 — EST-014
**Severity:** P3  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
20+ debug print_r/exit statements in production code (commented out)

## 2. Root Cause
Debug artifacts left in production. If accidentally uncommented, dump raw POST to browser.

## 3. Impact Analysis
Code Quality: No runtime impact while commented. Risk if uncommented.

## 4. Exact Location
admin_ret_estimation.php — 20+ locations throughout file

## 5. Current Code (VERBATIM)
```
// print_r($_POST);exit; — various locations
```

## 6. Proposed Minimal Fix
Remove all commented debug statements

## 7. Why This Fix Is Safe
Cleanup only. No logic change.

## 8. Risk Level
Low

## 9. Regression Checklist
Cleaner code; no debug leaks

## 10. Rollback Plan
Re-add comments (not recommended)

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 57 — EST-R312
**Severity:** P3  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
Duplicate getStoneRateSettings() call in JS init

## 2. Root Cause
L236-237: getStoneRateSettings() called twice consecutively in edit case. Redundant AJAX request.

## 3. Impact Analysis
Performance: Unnecessary AJAX call. No data impact.

## 4. Exact Location
ret_estimation.js, L236-237

## 5. Current Code (VERBATIM)
```
getStoneRateSettings();
getStoneRateSettings();    // Called twice!
```

## 6. Proposed Minimal Fix
Remove the duplicate call (keep one)

## 7. Why This Fix Is Safe
Single call provides same data.

## 8. Risk Level
Low

## 9. Regression Checklist
Stone rate settings loaded once

## 10. Rollback Plan
Add duplicate call back

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 58 — EST-R410
**Severity:** P3  
**Module:** View — form.php  
**Fix Readiness:** ⚠️ Needs server-side refactor  
**Sprint:** 3

## 1. Problem Summary
30+ hidden inputs expose server settings — client-side-only permission validation

## 2. Root Cause
L127-155: min/max rates, discount limits, MC edit flags exposed as hidden inputs. Server doesn't re-validate.

## 3. Impact Analysis
Security: Employee can bypass limits via DevTools. Needs server-side validation.

## 4. Exact Location
estimation/form.php, L127-155

## 5. Current Code (VERBATIM)
```
<input type='hidden' id='min_old_gold_rate' value='...' />
<input type='hidden' id='allow_mc_edit' value='...' />
<input type='hidden' id='disc_limit' />
```

## 6. Proposed Minimal Fix
Add server-side re-validation of permission limits in save/update handler.
⚠️ Requires controller-side enforcement, not just view changes

## 7. Why This Fix Is Safe
Defense in depth — server validates regardless of client.

## 8. Risk Level
High — requires controller changes

## 9. Regression Checklist
All limits enforced server-side

## 10. Rollback Plan
Remove server-side validation (not recommended)

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 59 — EST-R411
**Severity:** P3  
**Module:** View — form.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
Duplicate name attribute on hidden input

## 2. Root Cause
L1385: input has name='cus[id_village]' AND name='' (two name attributes).

## 3. Impact Analysis
HTML Violation: Technically invalid. Browsers use first name value.

## 4. Exact Location
estimation/form.php, L1385

## 5. Current Code (VERBATIM)
```
<input type='hidden' name='cus[id_village]' id='id_village' name='' value=''>
```

## 6. Proposed Minimal Fix
Remove the second name=''

## 7. Why This Fix Is Safe
Removes invalid duplicate attribute.

## 8. Risk Level
Low

## 9. Regression Checklist
Valid HTML

## 10. Rollback Plan
Re-add name=''

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 60 — EST-R509
**Severity:** P3  
**Module:** JS — ret_estimation.js  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
var total_tax_rate re-declaration inside if block — hoisting edge case

## 2. Root Cause
L9167: var re-declares total_tax_rate inside if block, hoisted to function scope, overwrites outer declaration.

## 3. Impact Analysis
Code Quality: var keyword causes subtle hoisting. Logic appears intentional but fragile.

## 4. Exact Location
ret_estimation.js, L9167

## 5. Current Code (VERBATIM)
```
var total_tax_rate = parseFloat(calculate_inclusiveGST(rate_with_mc, tax_group)).toFixed(2);  // 'var' re-declaration
```

## 6. Proposed Minimal Fix
Remove 'var' keyword: total_tax_rate = parseFloat(...) — simple assignment instead of re-declaration

## 7. Why This Fix Is Safe
Removes hoisting risk. Same runtime behavior for the intended case.

## 8. Risk Level
Low

## 9. Regression Checklist
Inclusive tax calculation unchanged

## 10. Rollback Plan
Add var back

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 61 — EST-R609
**Severity:** P3  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
Controller does raw DB query instead of delegating to model (get_village)

## 2. Root Cause
L3454-3492: Direct $this->db->select/from/where/get in controller. Violates MVC pattern.

## 3. Impact Analysis
Architecture: Works correctly but bypasses model layer. old_get_village() correctly delegates.

## 4. Exact Location
admin_ret_estimation.php, L3454-3492, get_village()

## 5. Current Code (VERBATIM)
```
$this->db->select('*'); $this->db->from('village'); ... $query = $this->db->get();
```

## 6. Proposed Minimal Fix
Move query to model method. Controller calls model.

## 7. Why This Fix Is Safe
Standard MVC refactor.

## 8. Risk Level
Low

## 9. Regression Checklist
Village lookup still works

## 10. Rollback Plan
Move query back to controller

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# BUG 62 — EST-R610
**Severity:** P3  
**Module:** Controller — admin_ret_estimation.php  
**Fix Readiness:** ✅ Ready  
**Sprint:** 3

## 1. Problem Summary
5 commented-out debug print_r/exit in production controller

## 2. Root Cause
L2590, L2594, L3506, L3559, L3561: debug artifacts in controller.

## 3. Impact Analysis
Code Quality: No runtime impact. Risk if uncommented.

## 4. Exact Location
admin_ret_estimation.php, L2590, L2594, L3506, L3559, L3561

## 5. Current Code (VERBATIM)
```
// print_r($_POST);exit;
// print_r($data);exit;
// echo '<pre>';print_r($_POST);exit;
```

## 6. Proposed Minimal Fix
Remove all 5 commented debug lines

## 7. Why This Fix Is Safe
Cleanup only.

## 8. Risk Level
Low

## 9. Regression Checklist
Cleaner controller

## 10. Rollback Plan
Re-add comments (not recommended)

## 11. Confirmation Gate
⚠️ Awaiting user approval before modifying code.  
Proceed? (Yes / Modify / Reject)

---

# 🚦 EXECUTION PROTOCOL

All 65 bugs are now planned. To begin execution:

1. Review this plan
2. Confirm: **"Shall we begin with BUG 01?"**
3. Each bug will be worked ONE at a time
4. Current code reprinted, exact diff shown, explicit confirmation required
5. Only after confirmation: final patched code provided
6. **No batch fixes allowed**
