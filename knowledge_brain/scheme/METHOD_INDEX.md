# Scheme Module — Method Index
> **Round**: 2 | **Date**: 2026-03-24

---

## Controller Methods (`admin_scheme.php` — 24 methods)

| # | Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|---|
| 1 | `__construct()` | L11-33 | — | — | CI framework |
| 2 | `ajax_fixweight_schemes()` | L50-55 | scheme, metal, scheme_account, payment | — | AJAX |
| 3 | `ajax_get_scheme($id)` | L1125-1129 | (via scheme_business) | — | AJAX |
| 4 | `ajax_get_schemes()` | L1113-1118 | scheme | — | AJAX |
| 5 | `ajax_get_schemes_list()` | L38-49 | scheme, access control | — | AJAX |
| 6 | `checkDigiAvailability()` | L1232-1239 | scheme | — | AJAX |
| 7 | `get_branch_edit($id)` | L1135-1142 | scheme_branch, scheme | — | AJAX |
| 8 | `get_branches()` | L1149-1154 | branch | — | AJAX |
| 9 | `get_classifications()` | L1028-1033 | sch_classify | — | AJAX |
| 10 | `get_metals()` | L1022-1027 | metal | — | AJAX |
| 11 | `get_units()` | L1034-1039 | (via get_installment_amount) | — | AJAX |
| 12 | `get_weight_list()` | L1225-1230 | weight | — | AJAX |
| 13 | `getActivePuritiesByMetal()` | L1210-1215 | ret_metal_purity_rate, ret_purity | — | AJAX |
| 14 | `getFreeInsBySchId($id)` | L1130-1134 | scheme | — | AJAX |
| 15 | `getSchemeTypeByID($id)` | L1119-1124 | scheme | — | AJAX |
| 16 | `gstsplitupinsert()` | L1143-1148 | scheme | gst_splitup_detail | AJAX |
| 17 | `index()` | L34-37 | — | — | Route |
| 18 | `joinTime_weight_slabs()` | L1217-1223 | scheme, weight | — | AJAX |
| 19 | `sch_form($type, $id)` | L129-199 | scheme, scheme_branch, scheme_benefit_deduct_settings, scheme_debit_settings, scheme_agent_benefit, scheme_incentive_settings, scheme_flexi_settings, emp_closing_incentive, scheme_general_advance_benefit_settings, gst_splitup_detail, scheme_custom_payable_settings, chit_settings | — | Route |
| 20 | `sch_list($msg)` | L62-74 | scheme | — | Route |
| 21 | `sch_post($type, $id)` | L200-1021 | scheme (check_acc_records) | scheme, scheme_branch, gst_splitup_detail, scheme_benefit_deduct_settings, scheme_flexi_settings, scheme_incentive_settings, scheme_debit_settings, scheme_agent_benefit, emp_closing_incentive, scheme_general_advance_benefit_settings, scheme_custom_payable_settings, log_detail | Route |
| 22 | `scheme_business($id)` | L1040-1112 | scheme, metal_rate, scheme_account, weight | — | Internal |
| 23 | `set_scheme_image($id)` | L1155-1177 | — | scheme (logo update) | Internal |
| 24 | `upload_img(...)` | L1178-1209 | — | Filesystem | Internal |

---

## Model Methods (`scheme_model.php` — 53 methods)

| # | Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|---|
| 1 | `__construct()` | L11-14 | — | — | CI |
| 2 | `branchwise_scheme()` | L405-409 | chit_settings | — | empty_record() |
| 3 | `check_acc_records($id)` | L537-544 | scheme_account | — | sch_post('Delete') |
| 4 | `delete_agent_benefit($id)` | L733-738 | — | scheme_agent_benefit | sch_post('Edit') |
| 5 | `delete_benfit_rdeduct($id)` | L677-681 | — | scheme_benefit_deduct_settings | (unused?) |
| 6 | `delete_benfit_rdeduct_preclose($id)` | L722-726 | — | scheme_debit_settings | sch_post('Edit') |
| 7 | `delete_incentive_benefit($id)` | L753-758 | — | scheme_incentive_settings | sch_post('Edit') |
| 8 | `delete_scheme($id)` | L526-536 | — | gst_splitup_detail, scheme | sch_post('Delete') |
| 9 | `delete_scheme_branch($id)` | L654-658 | — | scheme_branch | sch_post('Edit') |
| 10 | `deleteData($id_field, $id_value, $table)` | L34-39 | — | {any} | sch_post (multiple) |
| 11 | `empty_record()` | L236-399 | chit_settings (via sch_limit, branchwise_scheme) | — | sch_form('Add') |
| 12 | `enableDigiGold($id, $id_metal)` | L801-815 | scheme | — | sch_form('Edit'), checkDigiAvailability() |
| 13 | `get_adv_benefit_data($id)` | L775-780 | scheme_general_advance_benefit_settings | — | sch_form('Edit') |
| 14 | `get_agent_benefit__data($id)` | L739-744 | scheme_agent_benefit | — | sch_form('Edit') |
| 15 | `get_all_schemes()` | L54-59 | scheme | — | sch_list(), ajax_get_schemes_list() |
| 16 | `get_benfit_rdeduct_data($id)` | L671-676 | scheme_benefit_deduct_settings | — | sch_form('Edit') |
| 17 | `get_benfit_rdeduct_preclose__data($id)` | L727-732 | scheme_debit_settings | — | sch_form('Edit') |
| 18 | `get_branch_data($id)` | L659-664 | scheme_branch | — | External |
| 19 | `get_branch_edit($id)` | L579-587 | scheme_branch, scheme | — | sch_form('Edit'), get_branch_edit() |
| 20 | `get_branches()` | L629-642 | branch | — | get_branches() |
| 21 | `get_chitsettings()` | L143-148 | chit_settings | — | External |
| 22 | `get_classifications()` | L40-53 | sch_classify | — | get_classifications() |
| 23 | `get_closing_scheme_data($id)` | L716-721 | emp_closing_incentive | — | sch_form('Edit') |
| 24 | `get_fixweight_schemes()` | L545-573 | scheme, metal, scheme_account, payment, metal_rates | — | ajax_fixweight_schemes() |
| 25 | `get_flexible_ins_data($id)` | L759-764 | scheme_flexi_settings | — | sch_form('Edit') |
| 26 | `get_gstSplitupData($id)` | L595-600 | gst_splitup_detail | — | sch_form('Edit') |
| 27 | `get_incentive_data($id)` | L747-752 | scheme_incentive_settings | — | sch_form('Edit') |
| 28 | `get_joinTime_weight_slabs($id_scheme)` | L782-787 | scheme, weight | — | scheme_business(), joinTime_weight_slabs() |
| 29 | `get_metals()` | L15-27 | metal | — | get_metals() |
| 30 | `get_scheme($id)` | L157-193 | scheme, metal, scheme_branch, scheme_benefit_deduct_settings, scheme_debit_settings, sch_classify, chit_settings | — | sch_form('Edit'), scheme_business() |
| 31 | `get_scheme_active($id)` | L205-220 | scheme, metal | — | External |
| 32 | `get_scheme_count($id_scheme)` | L665-669 | scheme_account | — | scheme_business() |
| 33 | `get_scheme_id($scheme_code)` | L221-227 | scheme | — | External |
| 34 | `get_scheme1($id)` | L194-204 | scheme, metal, scheme_branch, sch_classify | — | (legacy) |
| 35 | `get_schemes()` | L74-142 | scheme, scheme_branch, branch, scheme_account | — | ajax_get_schemes() |
| 36 | `get_schemes_type($id)` | L149-156 | scheme | — | External |
| 37 | `get_weight_list($min, $max)` | L790-799 | weight | — | get_weight_list() |
| 38 | `getActivePuritiesByMetal($id_metal)` | L766-773 | ret_metal_purity_rate, ret_purity | — | getActivePuritiesByMetal() |
| 39 | `getChitSettings()` | L410-414 | chit_settings | — | External |
| 40 | `getFreeInsBySchId($id)` | L574-578 | scheme | — | getFreeInsBySchId() |
| 41 | `getTopUpSchemeChart($schemeId)` | L818-826 | scheme_custom_payable_settings | — | sch_form('Edit') |
| 42 | `insert_gstSplitup($data)` | L601-605 | — | gst_splitup_detail | sch_post |
| 43 | `insert_scheme($data)` | L466-492 | scheme (SHOW COLUMNS) | scheme | sch_post('Add') |
| 44 | `insertData($data, $table)` | L689-715 | {table} (SHOW COLUMNS) | {any} | sch_post (multiple charts) |
| 45 | `insetrgstsplitup()` | L615-628 | scheme | gst_splitup_detail | gstsplitupinsert() |
| 46 | `sch_limit()` | L400-404 | chit_settings | — | empty_record() |
| 47 | `scheme_branch($data)` | L649-653 | — | scheme_branch | sch_post |
| 48 | `scheme_count()` | L416-420 | scheme | — | sch_form('Add') |
| 49 | `topUpSchemeChartEditProcess($id, $data)` | L827-852 | — | scheme_custom_payable_settings | sch_post('Edit') |
| 50 | `update_gstSplitup($id)` | L606-613 | — | gst_splitup_detail | sch_post('Edit') |
| 51 | `update_scheme($data, $id)` | L493-519 | scheme (SHOW COLUMNS) | scheme | sch_post('Edit') |
| 52 | `update_scheme_free_payment($data)` | L520-525 | — | scheme | External |
| 53 | `delete_scheme($id)` | L526-536 | — | gst_splitup_detail, scheme | sch_post('Delete') |

---

## Table → Methods Reverse Map

| Table | Read By | Written By |
|---|---|---|
| `scheme` | get_all_schemes, get_scheme, get_scheme_active, get_scheme_id, get_schemes, get_schemes_type, get_fixweight_schemes, empty_record(via sch_limit), scheme_count, getFreeInsBySchId, enableDigiGold, insetrgstsplitup, get_joinTime_weight_slabs | insert_scheme, update_scheme, update_scheme_free_payment, delete_scheme |
| `scheme_branch` | get_branch_edit, get_branch_data, get_scheme, get_scheme1 | scheme_branch, delete_scheme_branch |
| `scheme_benefit_deduct_settings` | get_benfit_rdeduct_data, get_scheme | insertData (chart), deleteData |
| `scheme_debit_settings` | get_benfit_rdeduct_preclose__data, get_scheme | insertData (chart), delete_benfit_rdeduct_preclose |
| `scheme_agent_benefit` | get_agent_benefit__data | insertData (chart), delete_agent_benefit |
| `scheme_incentive_settings` | get_incentive_data | insertData (chart), delete_incentive_benefit |
| `scheme_flexi_settings` | get_flexible_ins_data | insertData (chart), deleteData |
| `emp_closing_incentive` | get_closing_scheme_data | insertData (chart), deleteData |
| `scheme_general_advance_benefit_settings` | get_adv_benefit_data | insertData (chart), deleteData |
| `scheme_custom_payable_settings` | getTopUpSchemeChart | insert_batch, topUpSchemeChartEditProcess |
| `gst_splitup_detail` | get_gstSplitupData | insert_gstSplitup, update_gstSplitup, insetrgstsplitup, delete_scheme |
| `metal` | get_metals, get_scheme | — |
| `branch` | get_branches | — |
| `sch_classify` | get_classifications, get_scheme | — |
| `chit_settings` | sch_limit, branchwise_scheme, getChitSettings, get_chitsettings, get_scheme | — |
| `weight` | get_weight_list, get_joinTime_weight_slabs | — |
| `ret_metal_purity_rate` | getActivePuritiesByMetal | — |
| `ret_purity` | getActivePuritiesByMetal | — |
| `scheme_account` | check_acc_records, get_scheme_count, get_schemes | — |
| `metal_rates` | get_fixweight_schemes | — |
