# Catalog_Inventory — METHOD INDEX

> **Built**: 2026-03-13 | **Refreshed**: 2026-03-24 (Round 6) | **Deep Scan**: 2026-03-24 (Round 7) | Sorted alphabetically within each section

## 7a. Controller Methods (Alphabetical)

> 164 methods total (including `__construct`). Organized by line number within this table.
> **Pattern**: Most methods use `switch($type)` — sub-actions listed under "Notes"

| Method | Lines | Tables Read | Tables Written | JS Caller | Notes |
|---|---|---|---|---|---|
| `__construct()` | L24-62 | — | — | — | Loads models, session gate |
| `active_clarity()` | L160-168 | `ret_clarity` | — | — | Returns active clarity JSON |
| `active_color()` | L94-108 | `ret_color` | — | — | Returns active color JSON |
| `active_cut()` | L170-178 | `ret_cut` | — | — | Returns active cut JSON |
| `active_masters()` | L126-148 | `ret_cut`, `ret_clarity` | — | — | Returns cut+clarity JSON. ⚠️ References undefined `$carat` |
| `active_metals()` | L114-122 | `metal` | — | — | Returns all metals JSON |
| `ajax_active_subCtg()` | L76-90 | `sub_category` | — | — | Returns active sub-categories |
| `ajax_getPurity()` | L498-507 | `ret_purity` | — | — | Returns all purities JSON |
| `attribute()` | L15094+ | `ret_attribute`, `ret_attribute_values` | `ret_attribute`, `ret_attribute_values`, `ret_design_attributes` | — | CRUD for design attributes |
| `bank_deposit()` | L17093+ | `ret_bank_deposit`, bank tables | `ret_bank_deposit` | — | CRUD for bank deposits |
| `base64ToFile()` | L13859+ | — | — | — | Utility: base64 to file converter |
| `branch_floor()` | L1866+ | `ret_branch_floor` | `ret_branch_floor` | — | CRUD for branch floors |
| `bulkprodupdated()` | L6709-6796 | `ret_product_master` | `ret_product_master` | — | Bulk product status/tax update |
| `category()` | L3401-3813 | `ret_category`, `ret_metal_cat_purity` | `ret_category`, `ret_metal_cat_purity` | — | Full CRUD for categories with purity junction |
| `charges()` | L13575+ | `ret_charges` | `ret_charges` | — | CRUD for charge types |
| `check_quality_code()` | L21589+ | `ret_quality_code` | — | — | Verify quality code uniqueness |
| `clarity()` | L1194-1400 | `ret_clarity` | `ret_clarity` | — | Full CRUD for clarity |
| `clarity_status()` | L1401+ | `ret_clarity` | `ret_clarity` | — | Toggle clarity active status |
| `color()` | L746-945 | `ret_color` | `ret_color` | — | Full CRUD for color |
| `color_status()` | L946+ | `ret_color` | `ret_color` | — | Toggle color active status |
| `cover_up()` | L22566+ | `ret_cover_up` | `ret_cover_up` | — | CRUD for cover-up entries |
| `create_va_mc_settings()` | L21598+ | — | — | — | VA/MC settings creation |
| `cut()` | L969-1170 | `ret_cut` | `ret_cut` | — | Full CRUD for cut |
| `cut_status()` | L1171+ | `ret_cut` | `ret_cut` | — | Toggle cut active status |
| `day_close()` | L22453+ | `ret_day_closing` | `ret_day_closing` | — | Day closing operations |
| `delete_design_attribute()` | L15034+ | — | `ret_design_attributes` | — | Delete design attribute mapping |
| `delete_design_weight_range()` | L15061+ | — | `ret_design_weight_range_wc` | — | Delete weight range |
| `delete_product_design_mapping()` | L14497+ | — | `ret_product_mapping` | — | Delete product-design mapping |
| `delete_product_mapping()` | L15812+ | — | `ret_karigar_products` | — | Delete karigar product mapping |
| `delete_sub_design_mapping()` | L14874+ | — | `ret_sub_design_mapping` | — | Delete sub-design mapping |
| `design()` | L5838+ | design tables | `ret_design_master` | — | Legacy design operations |
| `diamond()` | L18483+ | diamond tables | — | — | Diamond master operations |
| `diamond_rate()` | L20779+ | `ret_diamond_rate` | `ret_diamond_rate` | — | CRUD for diamond rates |
| `do_upload()` | L18424+ | — | — | — | Karigar file upload handler |
| `feedback()` | L13362+ | feedback tables | — | — | Customer feedback management |
| `financial_status()` | L9628+ | `ret_financial_year` | `ret_financial_year` | — | Toggle financial year status |
| `financial_year()` | L9402-9627 | `ret_financial_year` | `ret_financial_year` | — | CRUD for financial years |
| `floor_counter()` | L2114+ | `ret_branch_floor_counter` | `ret_branch_floor_counter` | — | CRUD for floor counters |
| `get_active_design_products()` | L14961+ | `ret_product_master` | — | — | Active design products |
| `get_ActiveBranchFloor()` | L16043+ | `ret_branch_floor` | — | — | Active branch floors |
| `get_ActiveCounter()` | L16030+ | `ret_branch_floor_counter` | — | — | Active counters |
| `get_ActiveDesign()` | L14212+ | `ret_design_master` | — | — | Active designs |
| `get_ActiveKYC()` | L18383+ | `ret_kyc_master` | — | — | Active KYC types |
| `get_ActiveProducts()` | L15565+ | `ret_product_master` | — | — | Active products for dropdowns |
| `get_ActiveSubDesigns()` | L14974+ | `ret_sub_design_master` | — | — | Active sub-designs |
| `get_Activesize()` | L11000+ | `ret_size` | — | — | Active sizes |
| `get_all_purity()` | L9810+ | `ret_purity` | — | — | All purities |
| `get_catByMetal()` | L3390+ | `ret_category` | — | — | Categories by metal type |
| `get_category()` | L3817-3826 | `ret_category` | — | — | Get category by ID |
| `get_design_attr_values()` | L15021+ | `ret_attribute_values` | — | — | Attribute values for design |
| `get_DesignSettingsDetails()` | L14995+ | design settings tables | — | — | Design settings details |
| `get_deviceNames()` | L22442+ | `web_registered_devices` | — | — | Device names list |
| `get_hooktype()` | L7722+ | `ret_hook_type` | — | — | Hook types |
| `get_kar_img_by_id()` | L18411+ | karigar image tables | — | — | Karigar image by ID |
| `get_karigar_wise_charges()` | L15660+ | `ret_karigar_charges` | — | — | Karigar charges |
| `get_karigar_wise_stones()` | L15643+ | `ret_karigar_stones` | — | — | Karigar stones |
| `get_karigar_wise_wastage()` | L15618+ | `ret_karikar_items_wastage` | — | — | Karigar wastage rates |
| `get_MetalCategory()` | L15580+ | `ret_old_metal_category` | — | — | Metal categories |
| `get_NonTagProducts()` | L15593+ | `ret_product_master` | — | — | Non-tagged products |
| `get_ProductDesign()` | L14914+ | product-design mapping | — | — | Product design mapping |
| `get_quality_code()` | L20762+ | `ret_quality_code` | — | — | Quality code list |
| `get_screwtype()` | L7733+ | `ret_screw_type` | — | — | Screw types |
| `get_productData()` | L7744+ | `ret_product_master` | — | — | Product data |
| `get_sectionBranchwise()` | L12388+ | `ret_section_branch` | — | — | Sections by branch |
| `get_section()` | L12412+ | `ret_section` | — | — | Section details |
| `get_wastage_details()` | L15008+ | wastage tables | — | — | Wastage details |
| `get_WastApprvKarigars()` | L18353+ | `ret_karigar` | — | — | Karigars awaiting wastage approval |
| `get_KarigarWastPurity()` | L18370+ | purity/karigar tables | — | — | Karigar wastage by purity |
| `get_weight_range_details()` | L10224+ | weight tables | — | — | Weight range details |
| `getDesignName()` | L14941+ | `ret_design_master` | — | — | Design name by ID |
| `getExistingKarigars()` | L21359+ | `ret_karigar` | — | — | Existing karigars |
| `getKycDetails()` | L18396+ | KYC tables | — | — | KYC detail records |
| `getLooseStoneProductRateSettings()` | L22302+ | stone rate tables | — | — | Loose stone product rates |
| `getLooseStonesProduct()` | L22294+ | product tables | — | — | Loose stone products |
| `getQualityDiamondRates()` | L21580+ | diamond rate tables | — | — | Quality-based diamond rates |
| `getStoneRateSettings()` | L22287+ | `ret_stone_rate_settings` | — | — | Stone rate settings |
| `getSubDesignName()` | L14929+ | `ret_sub_design_master` | — | — | Sub-design name by ID |
| `hook()` | L9160+ | `ret_hook_type` | `ret_hook_type` | — | CRUD for hook types |
| `index()` | L66-72 | — | — | — | Empty — no default action |
| `karigar()` | L3830+ | `ret_karigar` + child tables | `ret_karigar` + child tables | — | Full CRUD for karigars |
| `karigar_approval()` | L17926+ | `ret_karigar` | `ret_karigar` | — | Wastage approval workflow |
| `karigar_general()` | L19392+ | `ret_karigar` | `ret_karigar` | — | Karigar general info update |
| `karigar_kyc()` | L20310+ | `ret_karigar_kyc` | `ret_karigar_kyc` | — | Karigar KYC CRUD |
| `karigar_stone()` | L20158+ | `ret_karigar_stones` | `ret_karigar_stones` | — | Karigar stone rates CRUD |
| `karigar_wastage()` | L19734+ | `ret_karikar_items_wastage` | `ret_karikar_items_wastage` | — | Karigar wastage CRUD |
| `making_type()` | L2370+ | `ret_making_type` | `ret_making_type` | — | CRUD for making types |
| `material()` | L2866+ | `ret_material` | `ret_material` | — | CRUD for materials |
| `material_rate()` | L5372+ | `ret_material_rate` | `ret_material_rate` | — | CRUD for material rates |
| `metal()` | L7448+ | `metal` | `metal` | — | CRUD for metals |
| `metal_info_list()` | L182-220 | metal info, diamond, cut, clarity, color, purity tables | — | — | Combined metal info JSON |
| `metal_type()` | L11300+ | metal type tables | — | — | Metal type management |
| `MetalRates()` | L16834+ | `ret_metal_purity_rate` | — | — | JSON: metal purity rates |
| `MetalSelect()` | L16814+ | `metal` | — | — | JSON: metal list for select boxes |
| `moneyFormatIndia()` | L17906+ | — | — | — | Utility: Indian currency format |
| `old_metal_cat()` | L11527+ | `ret_old_metal_category` | `ret_old_metal_category` | — | Old metal category CRUD |
| `old_metal_rate()` | L11814+ | `ret_old_metal_rate` | `ret_old_metal_rate` | — | Old metal rate CRUD |
| `product()` | L253-278 | `product` (web catalog) | — | — | Web catalog product list/search |
| `product_division()` | L16568+ | `ret_product_division` | `ret_product_division` | — | CRUD - product divisions |
| `product_division_status()` | L16779+ | `ret_product_division` | `ret_product_division` | — | Toggle division status |
| `product_grouping()` | L22977+ | `ret_product_grouping` | `ret_product_grouping` | — | CRUD - product grouping |
| `purity()` | L282-494 | `ret_purity` | `ret_purity` | — | Full CRUD for purity |
| `purity_status()` | L511-530 | `ret_purity` | `ret_purity` | — | Toggle purity status |
| `PuritySelect()` | L16824+ | `ret_purity` | — | — | JSON: purity select list |
| `remove_img()` | L1601+ | — | — | — | Delete image file |
| `reorder_settings()` | L10243+ | `ret_reorder_settings` | `ret_reorder_settings` | — | Inventory reorder settings |
| `repair_master()` | L16323+ | repair tables | — | — | Repair master CRUD |
| `ret_account()` | L12740+ | `ret_account_head` | `ret_account_head` | — | Account head CRUD |
| `ret_breakeven_logs()` | L22687+ | `ret_breakeven_logs` | `ret_breakeven_logs` | — | Breakeven log entries |
| `ret_collection()` | L16066+ | collection tables | — | — | Collection master CRUD |
| `ret_crdr_ledger()` | L12989+ | CR/DR ledger tables | — | — | CR/DR ledger CRUD |
| `ret_delivery()` | L10681+ | `ret_sale_delivery` | `ret_sale_delivery` | — | Delivery master CRUD |
| `ret_design()` | L7761+ | `ret_design_master` + children | `ret_design_master` + children | — | Full design CRUD (complex, multi-table) |
| `ret_karigar_product()` | L15679+ | `ret_karigar_products` | `ret_karigar_products` | — | Karigar-product mapping |
| `ret_metalpurity()` | L16846+ | `ret_metal_purity_rate` | `ret_metal_purity_rate` | — | Metal purity rate CRUD (rates page) |
| `ret_pay_device()` | L12477+ | `ret_bill_pay_device` | `ret_bill_pay_device` | — | Payment device CRUD |
| `ret_product()` | L6125-6705 | `ret_product_master` + children | `ret_product_master`, `ret_product_section`, `ret_product_charges` | — | Full CRUD for retail products (main product entity) |
| `ret_products_mapping()` | L14329+ | product-design mapping | `ret_product_mapping` | — | Product-design mapping CRUD |
| `ret_qc_cancel_reason()` | L22865+ | `ret_qc_cancel_reason` | `ret_qc_cancel_reason` | — | QC cancel reason CRUD |
| `ret_section()` | L12056+ | `ret_section`, `ret_section_branch` | `ret_section`, `ret_section_branch` | — | Section CRUD |
| `ret_size()` | L11013+ | `ret_size` | `ret_size` | — | Size master CRUD |
| `ret_stone_rate_settings()` | L21966+ | `ret_stone_rate_settings` | `ret_stone_rate_settings` | — | Stone rate settings |
| `ret_sub_design()` | L13893+ | `ret_sub_design_master` | `ret_sub_design_master` | — | Sub-design master CRUD |
| `ret_sub_product()` | L6800+ | `ret_sub_product_master` | `ret_sub_product_master` | — | Sub-product CRUD |
| `ret_subdesign_mapping()` | L14537+ | `ret_sub_design_mapping` | `ret_sub_design_mapping` | — | Sub-design mapping |
| `rrmdir()` | L1578+ | — | — | — | Utility: recursive directory delete |
| `screw()` | L8918+ | `ret_screw_type` | `ret_screw_type` | — | CRUD for screw types |
| `selling_diamond_rate()` | L21366+ | `ret_selling_diamond_rate` | `ret_selling_diamond_rate` | — | Selling diamond rate CRUD |
| `set_image()` | L1426+ | — | — | — | Legacy image setter |
| `set_image_ret()` | L1442+ | — | `ret_product_master` | — | Retail product image setter |
| `set_ret_prod_image()` | L10930+ | — | – | — | Set retail product image |
| `shape()` | L21152+ | `ret_shape` | `ret_shape` | — | Shape master CRUD |
| `stock_issue_type()` | L13123+ | stock issue tables | — | — | Stock issue type CRUD |
| `stone()` | L3109+ | `ret_stone` | `ret_stone` | — | Stone master CRUD |
| `stone_discount()` | L23088+ | `ret_stone_discount_master` | `ret_stone_discount_master` | — | Stone discount CRUD |
| `sub_product()` | L226-249 | sub_product tables | — | — | Web sub-product search |
| `tag()` | L5597+ | `ret_tag_type_master` | `ret_tag_type_master` | — | Tag type CRUD |
| `tax()` | L5873+ | `ret_taxmaster` | `ret_taxmaster` | — | Tax master CRUD |
| `tgrp()` | L7113+ | `ret_taxgroupmaster`, `ret_taxgroupitems` | `ret_taxgroupmaster`, `ret_taxgroupitems` | — | Tax group CRUD |
| `theme()` | L2620+ | `ret_theme` | `ret_theme` | — | Theme CRUD |
| `uom()` | L1622+ | `ret_uom` | `ret_uom` | — | UOM CRUD |
| `update_design()` | L14225+ | — | `ret_design_master` | — | Update design record |
| `update_design_products()` | L14274+ | — | design-product mapping | — | Update design product links |
| `update_karigar_products()` | L14830+ | — | `ret_karigar_products` | — | Update karigar product links |
| `update_location()` | L10892+ | — | delivery table | — | Update delivery location |
| `update_product_design_mapping()` | L14425+ | — | `ret_product_mapping` | — | Update product-design mapping |
| `update_product_mapping()` | L15752+ | — | `ret_karigar_products` | — | Update karigar product mapping |
| `update_product_section()` | L12425+ | — | `ret_product_section` | — | Update product section |
| `update_rate_data()` | L9713+ | — | rate tables | — | Update rate data |
| `update_mrrate_data()` | L9829+ | — | material rate tables | — | Update material rate data |
| `update_size_status()` | L11260+ | `ret_size` | `ret_size` | — | Toggle size status |
| `update_sub_design_image()` | L14628+ | — | sub-design image tables | — | Update sub-design image |
| `update_subdesign_des()` | L14797+ | — | `ret_sub_design_master` | — | Update sub-design description |
| `update_subdesign_mapping()` | L14724+ | — | `ret_sub_design_mapping` | — | Update sub-design mapping |
| `upload_img()` | L1514+ | — | — | — | Utility: generic image upload |
| `vendor_sendotp()` | L22322+ | — | — | — | Send OTP to vendor |
| `vendor_trans_send_sms()` | L22309+ | — | — | — | Send SMS to vendor |
| `vendor_verify_otp()` | L22391+ | — | — | — | Verify vendor OTP |
| `wastage_discount()` | L23337+ | wastage tables | — | — | Wastage discount |
| `wastage_mc_settings()` | L18816+ | wastage settings tables | — | — | MC wastage settings CRUD |
| `web_devices()` | L15856+ | `web_registered_devices` | `web_registered_devices` | — | Web device management |
| `weight()` | L9945+ | `ret_weight` | `ret_weight` | — | Weight master CRUD |

## 7b. Model Methods (Alphabetical) — Top 50 Most Important

> 358 total model methods. Full list exceeds practical size. Listed here: the 50 most important methods for bug diagnosis.

| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `ajax_get_retProduct()` | L2574+ | `ret_product_master`, `ret_category` | — | `ret_product(default)` |
| `ajax_get_TaxProd()` | L1295+ | `ret_product_master`, `ret_category`, `ret_taxgroupmaster` | — | `bulkprodupdated(default)` |
| `ajax_getcategory()` | L1625+ | `ret_category`, `metal` | — | `category(default)` |
| `ajax_getProduct()` | L557+ | `product`, `sub_category`, `category` | — | `product()` |
| `ajax_getPurity()` | L176+ | `ret_purity` | — | `purity(default)`, `ajax_getPurity()` |
| `ajax_getcolor()` | L259+ | `ret_color` | — | `color(default)` |
| `ajax_getclarity()` | L403+ | `ret_clarity` | — | `clarity(default)` |
| `ajax_getcut()` | L331+ | `ret_cut` | — | `cut(default)` |
| `deleteData()` | L154+ | — | `{any}` | All delete operations |
| `empty_record_category()` | L22+ | — | — | Category form init |
| `empty_record_product()` | L475+ | — | — | Product form init (web catalog) |
| `get_category_purity()` | L1663+ | `ret_metal_cat_purity` | — | `category(edit)` |
| `get_product()` | L575+ | `product`, `sub_category`, `category`, `product_images` | — | Web catalog product edit |
| `get_ret_category()` | L1653+ | `ret_category` | — | `category(edit)` |
| `get_ret_product()` | L2638+ | `ret_product_master` | — | `ret_product(edit)` |
| `get_product_section()` | L2648+ | `ret_product_section` | — | `ret_product(edit)` |
| `get_product_charges()` | L5171+ | `ret_product_charges` | — | `ret_product(edit)` |
| `getActiveCategorymtr()` | L1687+ | `ret_category` | — | `category(active_category)` |
| `getActiveClarity()` | L789+ | `ret_clarity` | — | Multiple controllers |
| `getActivecolor()` | L821+ | `ret_color` | — | Multiple controllers |
| `getActiveCut()` | L779+ | `ret_cut` | — | Multiple controllers |
| `getActiveMetals()` | L759+ | `metal` | — | Multiple controllers |
| `getActivePurity()` | L769+ | `ret_purity` | — | Multiple controllers |
| `getActiveProducts()` | L1273+ | `ret_product_master` | — | Dropdowns |
| `getCatPurity()` | L1701+ | `ret_metal_cat_purity`, `ret_purity` | — | `category(cat_purity)` |
| `getHsnCode()` | L8234+ | `ret_category` | — | `category(hsn_code)` |
| `getItemsinTagDetails()` | L8575+ | `ret_taging` | — | Delete checks (category, product, purity) |
| `getProd_empty_record()` | L2668+ | — | — | `ret_product(add)` |
| `insertData()` | L48-82 | `INFORMATION_SCHEMA` | `{any}` | All add operations |
| `updateData()` | L84-120 | `INFORMATION_SCHEMA` | `{any}` | All update operations |
| `insert_product()` | L661+ | — | `product` | Web catalog product add |
| `update_product()` | L693+ | — | `product` | Web catalog product update |
| `delete_product()` | L709+ | — | `product`, `product_images`, `product_details` | Web product delete |
| `insert_color()` | L283+ | — | `ret_color` | `color(Add)` |
| `update_color()` | L297+ | — | `ret_color` | `color(Update)` |
| `delete_color()` | L313+ | — | `ret_color` | `color(Delete)` |
| `get_color()` | L271+ | `ret_color` | — | `color(Edit)` |
| `get_purity()` | L188+ | `ret_purity` | — | `purity(Edit)` |
| `get_cut()` | L343+ | `ret_cut` | — | `cut(Edit)` |
| `get_clarity()` | L415+ | `ret_clarity` | — | `clarity(Edit)` |
| `get_karigar()` | L1795+ | `ret_karigar` | — | `karigar(edit)` |
| `get_karigar_wastages()` | L1807+ | `ret_karikar_items_wastage` | — | Karigar edit page |
| `get_karigar_stones()` | L1821+ | `ret_karigar_stones` | — | Karigar edit page |
| `get_karigar_charges()` | L1835+ | `ret_karigar_charges` | — | Karigar edit page |
| `mobile_available()` | L1855+ | `ret_karigar` | — | Karigar add validation |
| `email_available()` | L1885+ | `ret_karigar` | — | Karigar add validation |
| `get_charges_list()` | L4293+ | `ret_charges` | — | product edit form |
| `GetFinancialYear()` | L8397+ | `ret_financial_year` | — | Financial year lookup |
| `getActiveStoneTypes()` | L6826+ | `ret_stone_type` | — | Karigar stone form |
| `web_device_settingDB()` | L5453+ | `web_registered_devices` | `web_registered_devices` | Web device settings |

## 7b-ext. Remaining Model Methods by Entity Group

> All 358 model methods are structurally documented below. Each entity group follows the same CRUD pattern: `ajax_get{Entity}()` (list), `get_{entity}($id)` (single), `getActive{Entity}()` (dropdown), `insert_`, `update_`, `delete_`. **⚠️ Every `get_{entity}($id)` method is SQL injection vulnerable** (string concatenation).

### Generic CRUD (used by all entities)
| Method | Lines | Tables | Notes |
|---|---|---|---|
| `insertData($data, $table)` | L48-82 | `{any}`, `INFORMATION_SCHEMA` | Generic insert with SHOW COLUMNS overhead |
| `updateData($data, $id_field, $id_value, $table)` | L84-120 | `{any}`, `INFORMATION_SCHEMA` | Generic update with SHOW COLUMNS overhead |
| `deleteData($id_field, $id_value, $table)` | L154-166 | `{any}` | Generic delete |

### Category Group (L22-46, L1609-1735)
| Method | Lines | Table | ⚠️ SQLi |
|---|---|---|---|
| `empty_record_category()` | L22 | — | — |
| `get_catByMetal($id)` | L1609 | `ret_category` | — |
| `ajax_getcategory($from, $to)` | L1625 | `ret_category`, `metal` | — |
| `get_ret_category($id)` | L1653 | `ret_category` | — |
| `get_category_purity($id)` | L1663 | `ret_metal_cat_purity` | — |
| `getActiveCategorymtr($post)` | L1687 | `ret_category` | — |
| `getCatPurity($id)` | L1701 | `ret_metal_cat_purity`, `ret_purity` | — |

### Product Group (L475-755, L1273-1465, L2574-2818)
| Method | Lines | Table | ⚠️ SQLi |
|---|---|---|---|
| `empty_record_product()` | L475 | — | — |
| `ajax_getProduct()` | L557 | `product`, `sub_category`, `category` | — |
| `get_product($id)` | L575 | `product`, `sub_category`, `category`, `product_images` | Yes (L587) |
| `get_prodimage($id)` | L597 | `product_images` | Yes |
| `get_defaultProdimage($id)` | L611 | `product_images` | Yes |
| `delete_prodimage($file)` | L623 | `product_images` | — |
| `insertProdImage($data)` | L637 | `product_images` | — |
| `deleteProdImage($id)` | L647 | `product_images` | — |
| `insert_product($data)` | L661 | `product` | — |
| `insert_product_detail($data)` | L677 | `product_details` | — |
| `update_product($data, $id)` | L693 | `product` | — |
| `delete_product($id)` | L709 | `product`, `product_images`, `product_details` | ⚠️ Undefined `$child` |
| `delete_prodDetail($id)` | L743 | `product_details` | — |
| `getActiveProducts()` | L1273 | `ret_product_master` | — |
| `getActiveSearchProd($txt)` | L1287 | `ret_product_master` | LIKE injection |
| `ajax_get_TaxProd(...)` | L1295 | `ret_product_master`, `ret_taxgroupmaster` | Complex elseif chain |
| `getActiveSubProducts()` | L1425 | `ret_sub_product_master` | — |
| `getActiveSearchSubProd($txt,$id)` | L1435 | `ret_sub_product_master`, `ret_product_sub_product` | LIKE injection |
| `ajax_get_retProduct($from,$to)` | L2574 | `ret_product_master`, `ret_category` | — |
| `genProdShortCode()` | L2616 | `ret_product_master` | — |
| `get_ret_product($id)` | L2638 | `ret_product_master` | — |
| `get_product_section($id)` | L2648 | `ret_product_section` | — |
| `getActiveProduct()` | L2658 | `ret_product_master` | — |
| `getProd_empty_record()` | L2668 | — | — |
| `ajax_get_retSubProduct($from,$to)` | L2790 | `ret_sub_product_master` | — |
| `get_ret_subproduct($id)` | L2818 | `ret_sub_product_master` | — |

### Purity Group (L176-251) + Cross-Module Helper
| Method | Lines | Table | ⚠️ SQLi |
|---|---|---|---|
| `ajax_getPurity()` | L176 | `ret_purity` | — |
| `get_purity($id)` | L188 | `ret_purity` | **Yes (L192)** |
| `get_profile_settings($id_profile)` | L199 | `profile` | Yes (concat) — ⚠️ Cross-module: reads `profile.vendor_approval_otp_req` for karigar OTP settings |
| `insert_purity($data)` | L209 | `ret_purity` | — |
| `update_purity($data, $id)` | L223 | `ret_purity` | — |
| `delete_purity($id)` | L239 | `ret_purity` | — |
| `getActivePurity()` | L769 | `ret_purity` | — |

### Color Group (L259-325)
| Method | Lines | Table | ⚠️ SQLi |
|---|---|---|---|
| `ajax_getcolor()` | L259 | `ret_color` | — |
| `get_color($id)` | L271 | `ret_color` | **Yes (L275)** |
| `insert_color($data)` | L283 | `ret_color` | — |
| `update_color($data, $id)` | L297 | `ret_color` | — |
| `delete_color($id)` | L313 | `ret_color` | — |
| `getActivecolor()` | L821 | `ret_color` | — |

### Cut Group (L331-397)
| Method | Lines | Table | ⚠️ SQLi |
|---|---|---|---|
| `ajax_getcut()` | L331 | `ret_cut` | — |
| `get_cut($id)` | L343 | `ret_cut` | **Yes (L347)** |
| `insert_cut($data)` | L355 | `ret_cut` | — |
| `update_cut($data, $id)` | L369 | `ret_cut` | — |
| `delete_cut($id)` | L385 | `ret_cut` | — |
| `getActiveCut()` | L779 | `ret_cut` | — |

### Clarity Group (L403-468)
| Method | Lines | Table | ⚠️ SQLi |
|---|---|---|---|
| `ajax_getclarity()` | L403 | `ret_clarity` | — |
| `get_clarity($id)` | L415 | `ret_clarity` | **Yes (L419)** |
| `insert_clarity($data)` | L427 | `ret_clarity` | — |
| `update_clarity($data, $id)` | L441 | `ret_clarity` | — |
| `delete_clarity($id)` | L457 | `ret_clarity` | — |
| `getActiveClarity()` | L789 | `ret_clarity` | — |

### Metal/Diamond Info Group (L759-843)
| Method | Lines | Table |
|---|---|---|
| `getActiveMetals()` | L759 | `metal` |
| `getmetalInfo($id)` | L799 | `product_details` |
| `getdiamondInfo($id)` | L809 | `product_details` |
| `getActiveSubctg()` | L831 | `sub_category` |

### Floor/Counter Group (L853-1012)
| Method | Lines | Table | ⚠️ SQLi |
|---|---|---|---|
| `ajax_getfloor(...)` | L853 | `ret_branch_floor`, `branch` | Yes (concat) |
| `get_floor($id)` | L895 | `ret_branch_floor` | **Yes (L899)** |
| `getActiveFloors()` | L907 | `ret_branch_floor` | — |
| `ajax_getcounter(...)` | L921 | `ret_branch_floor_counter`, `ret_branch_floor`, `branch` | Yes (concat) |
| `get_counter($id)` | L983 | `ret_branch_floor_counter` | **Yes (L987)** |
| `getActiveCounters()` | L995 | `ret_branch_floor_counter` | — |

### Making Type Group (L1023-1060)
| Method | Lines | Table | ⚠️ SQLi |
|---|---|---|---|
| `ajax_get_makingtype(...)` | L1023 | `ret_making_type` | — |
| `get_make_type($id)` | L1051 | `ret_making_type` | **Yes (L1055)** |

### Theme Group (L1075-1128)
| Method | Lines | Table | ⚠️ SQLi |
|---|---|---|---|
| `ajax_gettheme(...)` | L1075 | `ret_theme` | — |
| `get_theme($id)` | L1105 | `ret_theme` | **Yes (L1109)** |
| `getActiveTheme()` | L1115 | `ret_theme` | — |

### Material Group (L1139-1258)
| Method | Lines | Table | ⚠️ SQLi |
|---|---|---|---|
| `ajax_getmaterial(...)` | L1139 | `ret_material` | — |
| `get_material($id)` | L1169 | `ret_material` | **Yes (L1173)** |
| `get_material_lst()` | L1179 | `ret_material`, `ret_material_rate` | — |
| `ajax_getmtrrate(...)` | L1191 | `ret_material_rate`, `ret_material` | Yes (concat) |
| `get_materialrate($id)` | L1239 | `ret_material_rate` | **Yes (L1243)** |
| `getActiveMaterial()` | L1249 | `ret_material` | — |

### Stone Group (L1481-1536)
| Method | Lines | Table | ⚠️ SQLi |
|---|---|---|---|
| `getActiveStone()` | L1481 | `ret_stone` | — |
| `ajax_get_stone(...)` | L1493 | `ret_stone`, `ret_uom` | — |
| `get_stone($id)` | L1523 | `ret_stone` | **Yes (L1527)** |

### UOM Group (L1547-1598)
| Method | Lines | Table | ⚠️ SQLi |
|---|---|---|---|
| `ajax_getUOM(...)` | L1547 | `ret_uom` | — |
| `get_uom($id)` | L1575 | `ret_uom` | **Yes (L1579)** |
| `getActiveUOM()` | L1585 | `ret_uom` | — |

### Karigar Group (L1735-1885)
| Method | Lines | Table |
|---|---|---|
| `getActiveKarigar()` | L1735 | `ret_karigar` |
| `ajax_getkarigar(...)` | L1759 | `ret_karigar`, `branch` |
| `get_karigar($id)` | L1795 | `ret_karigar` |
| `get_karigar_wastages($id)` | L1807 | `ret_karikar_items_wastage` |
| `get_karigar_stones($id)` | L1821 | `ret_karigar_stones` |
| `get_karigar_charges($id)` | L1835 | `ret_karigar_charges` |
| `mobile_available($mob)` | L1855 | `ret_karigar` |
| `email_available($email)` | L1885 | `ret_karigar` |

### Tag/Design Group (L1925-2363)
| Method | Lines | Table |
|---|---|---|
| `ajax_gettag(...)` | L1925 | `ret_tag_type_master` |
| `get_tag($id)` | L1953 | `ret_tag_type_master` |
| `getActivetag()` | L1963 | `ret_tag_type_master` |
| `getSearchDesign($txt)` | L1987 | `ret_design_master` |
| `genDesignShortCode()` | L1997 | `ret_design_master` |
| `get_empty_design()` | L2017 | — |
| `ajax_get_design(...)` | L2148 | `ret_design_master`, `ret_category` |
| `ajax_get_retmaster(...)` | L2177 | Complex multi-join |
| `get_designimage($id)` | L2239 | `ret_design_images` |
| `get_ret_design($id)` | L2249 | `ret_design_master` |
| `get_design_karigar($id)` | L2259 | `ret_karigar` |
| `get_design_material($id)` | L2285 | `ret_material` |
| `get_design_purity($id)` | L2311 | `ret_purity` |
| `get_design_size($id)` | L2337 | `ret_size` |
| `get_productData($id)` | L2347 | `ret_product_master` |
| `delete_designimage($file)` | L2359 | `ret_design_images` |

### Tax Group (L2387-2570)
| Method | Lines | Table |
|---|---|---|
| `ajax_gettax(...)` | L2387 | `ret_taxmaster` |
| `get_tax($id)` | L2415 | `ret_taxmaster` |
| `get_tgrp_items($id)` | L2425 | `ret_taxgroupitems` |
| `getActivetax()` | L2446 | `ret_taxmaster` |
| `get_empty_tgrp()` | L2456 | — |
| `ajax_gettgrp(...)` | L2478 | `ret_taxgroupmaster` |
| `get_tgrp($id)` | L2506 | `ret_taxgroupmaster` |
| `getActivetgrp()` | L2516 | `ret_taxgroupmaster` |
| `get_empty_tgi()` | L2526 | — |
| `get_tgi($id)` | L2548 | `ret_taxgroupitems` |

### Metal/Screw/Hook Group (L2842-3036)
| Method | Lines | Table |
|---|---|---|
| `ajax_getmetal(...)` | L2842 | `metal` |
| `get_metals($id)` | L2872 | `metal` |
| `getActivemetal()` | L2884 | `metal` |
| `ajax_getscrew(...)` | L2912 | `ret_screw_type` |
| `get_screw($id)` | L2940 | `ret_screw_type` |
| `getActivescrew()` | L2950 | `ret_screw_type` |
| `ajax_gethook(...)` | L2974 | `ret_hook_type` |
| `get_hook($id)` | L3002 | `ret_hook_type` |
| `getActivehook()` | L3012 | `ret_hook_type` |

### Financial Year Group (L3036-3194)
| Method | Lines | Table |
|---|---|---|
| `fincnce_empty_record()` | L3036 | — |
| `ajax_get_financial_year_List(...)` | L3092 | `ret_financial_year` |
| `get_financialyear_by_status($s)` | L3108 | `ret_financial_year` |
| `get_finance_entry_records($id)` | L3124 | `ret_financial_year` |
| `update_financialData($d,$id)` | L3140 | `ret_financial_year` |
| `update_financialstatus($d,$id)` | L3156 | `ret_financial_year` |
| `setFinancialYearStatus($id)` | L3168 | `ret_financial_year` |

### Remaining Entity Groups (L3194-8684)
> The following groups follow the same CRUD pattern. Methods listed by line number.

| Group | Methods (Line Range) | Primary Table |
|---|---|---|
| Old Metal Rate | L3194-3248 | `ret_old_metal_rate` |
| Weight | L3248-3374 | `ret_weight` |
| Reorder Settings | L3374-3625 | `ret_reorder_settings` |
| Delivery | L3625-3713 | `ret_sale_delivery` |
| Size | L3730-3838 | `ret_size` |
| Old Metal Type/Category | L3838-3964 | `ret_old_metal_type`, `ret_old_metal_category` |
| Old Metal Rate | L3916-3964 | `ret_old_metal_rate` |
| Section | L3964-4063 | `ret_section` |
| Device | L4021-4106 | `ret_bill_pay_device` |
| Account | L4084-4189 | `ret_account_head` |
| Feedback | L4223-4293 | feedback tables |
| Charges | L4293-4369 | `ret_charges` |
| Sub-Design | L4369-4639 | `ret_sub_design_master`, `ret_sub_design_mapping` |
| Design Mapping | L4639-4832 | `ret_product_mapping` |
| Attribute | L4832-5130 | `ret_attribute`, `ret_attribute_values` |
| Karigar Products | L5130-5453 | `ret_karigar_products`, `ret_karigar_charges` |
| Web Devices | L5453-5541 | `web_registered_devices` |
| Collection | L5541-5575 | collection tables |
| Repair | L5575-5609 | repair tables |
| Product Division | L5609-5687 | `ret_product_division` |
| Metal Purity Rate | L5687-5816 | `ret_metal_purity_rate` |
| Bank Deposit | L5816-6428 | `ret_bank_deposit`, banking tables |
| Karigar Approval | L6428-6876 | `ret_karigar` + child tables |
| Section Branch | L6946-7092 | `ret_section_branch` |
| KYC | L7068-7324 | `ret_kyc_master`, `ret_karigar_kyc` |
| Quality Code | L7324-7544 | `ret_quality_code` |
| Diamond Rate | L7458-7572 | `ret_diamond_rate` |
| Company/Bank | L7572-7800 | `company`, `bank` |
| Wastage Settings | L7762-8050 | `ret_karikar_items_wastage`, wastage settings |
| Shape | L8092-8158 | `ret_shape` |
| Selling Diamond | L8177-8234 | `ret_selling_diamond_rate` |
| Stone Rate Settings | L8243-8415 | `ret_stone_rate_settings` |
| Day Close | L8486-8507 | `ret_day_closing` |
| Cover Up | L8497-8517 | `ret_cover_up` |
| Cancel Reason | L8526-8547 | `ret_qc_cancel_reason` |
| Product Grouping | L8547-8598 | `ret_product_grouping` |
| Tag Details Check | L8575 | `ret_taging` |
| Stone/Wastage Discount | L8611-8684 | `ret_stone_discount_master`, wastage tables |

## 7c. JS → Controller AJAX Map

### catalog.js (450 lines) — Legacy Web Catalog
| JS Line | JS Context | AJAX URL | Controller Method |
|---|---|---|---|
| L159 | `load_category_list()` | `index.php/catalog/category/ajax_list` | `category(default)` via route |
| L206 | `load_product_list()` | `index.php/catalog/product/ajax_list` | `product(default)` — ⚠️ LEGACY web catalog, NOT `ret_product()` |

### catalog_master.js (49,240 lines) — Primary Master Data JS ⚠️ PREVIOUSLY UNDOCUMENTED

> **Round 6 Discovery**: PRIMARY JS file for the entire Catalog_Inventory module. Not documented in Rounds 1–5.
> - **442 JS functions** | **382 `$.ajax()` calls**
> - Loaded on every master data page (purity, color, cut, clarity, floor, counter, material, karigar, design, product, etc.)
> - Uses `ctrl_page` routing: reads URL segments to activate correct entity table/form

#### Complete AJAX Map — catalog_master.js (382 entries) — Round 7 Deep Scan

> `(unknown)` = $.ajax inside inline handler/IIFE not inside a named function. `*(dynamic url)*` = FormData POST with runtime-built URL. **[EXT]** = cross-module call.

| JS Line | JS Function | Controller Endpoint |
|---|---|---|
| L2293 | `(unknown)` | `admin_ret_catalog/get_active_design_products` |
| L2367 | `(unknown)` | `admin_ret_catalog/get_DesignSettingsDetails` |
| L2909 | `(unknown)` | `admin_ret_catalog/ret_design/ajax_update_bulk_retdesign` |
| L4649 | `add_new_stone` | *(dynamic url)* |
| L4717 | `(unknown)` | `admin_ret_catalog/stone/add` |
| L5597 | `(unknown)` | *(dynamic url)* |
| L5681 | `checkKarigarMobileAvail` | *(dynamic url)* |
| L8455 | `(unknown)` | `admin_ret_catalog/stone/active_stones` |
| L8509 | `(unknown)` | `admin_ret_catalog/uom/active_uom` |
| L8891 | `(unknown)` | `get/active_color` |
| L8937 | `(unknown)` | `get/active_purity` |
| L9001 | `(unknown)` | `get/active_color` |
| L9045 | `(unknown)` | `get/active_masters` |
| L9157 | `prodInfo_list` | `get/metal_info_list` |
| L9271 | `(unknown)` | `admin_ret_catalog/get_productData` |
| L9361 | `(unknown)` | **[EXT]** `product/delect_prodDetail/{id}` |
| L9395 | `(unknown)` | `admin_ret_catalog/removeDesign_img` |
| L9565 | `get_metal` | `get/active_metals` |
| L9619 | `set_purity_table` | `purity/ajax` (DataTable) |
| L9747 | `add_purity` | `purity/add` |
| L9779 | `update_purity` | `purity/update/{id}` |
| L9809 | `get_purity` | `purity/edit/{id}` |
| L9845 | `update_carat` | `carat/update/{id}` |
| L9875 | `get_carat` | `carat/edit/{id}` |
| L9907 | `set_color_table` | `color/ajax` (DataTable) |
| L10065 | `add_color` | `color/add` |
| L10097 | `update_color` | `color/update/{id}` |
| L10125 | `get_color` | `color/edit/{id}` |
| L10157 | `set_cut_table` | `cut/ajax` (DataTable) |
| L10285 | `add_cut` | `cut/add` |
| L10317 | `update_cut` | `cut/update/{id}` |
| L10347 | `get_cut` | `cut/edit/{id}` |
| L10379 | `set_clarity_table` | `clarity/ajax` (DataTable) |
| L10507 | `add_clarity` | `clarity/add` |
| L10543 | `update_clarity` | `clarity/update/{id}` |
| L10573 | `get_clarity` | `clarity/edit/{id}` |
| L10605 | `set_carat_table` | `carat/ajax` (DataTable) |
| L10729 | `add_carat` | `carat/add` |
| L10765 | `update_carat` | `carat/update/{id}` |
| L10795 | `get_carat` | `carat/edit/{id}` |
| L10851 | `(unknown)` | `get/active_color` |
| L10899 | `get_purity_options` | `get/active_purity` |
| L10947 | `get_cut_options` | `get/active_cut` |
| L10995 | `get_clarity_options` | `get/active_clarity` |
| L11043 | `get_carat_options` | `get/active_carat` |
| L11105 | `getFloorBranch` | **[EXT]** `branch/branchname_list` |
| L11161 | `set_floor_table` | `admin_ret_catalog/branch_floor` |
| L11299 | `add_floor` | `admin_ret_catalog/branch_floor/add` |
| L11337 | `update_floor` | `admin_ret_catalog/branch_floor/update` |
| L11365 | `get_floor` | `admin_ret_catalog/branch_floor/edit` |
| L11423 | `set_branchfloor_table` | `admin_ret_catalog/floor_counter` |
| L11563 | `add_counter` | `admin_ret_catalog/floor_counter/add` |
| L11601 | `update_counter` | `admin_ret_catalog/floor_counter/update` |
| L11631 | `get_counter` | `admin_ret_catalog/floor_counter/edit` |
| L11683 | `get_Activefloors` | `admin_ret_catalog/branch_floor/active_floors` |
| L11777 | `set_makingtype_table` | `admin_ret_catalog/making_type` |
| L11915 | `add_making_type` | `admin_ret_catalog/making_type/add` |
| L11953 | `update_making_type` | `admin_ret_catalog/making_type/update` |
| L11983 | `get_maketype` | `admin_ret_catalog/making_type/edit` |
| L12035 | `set_theme_table` | `admin_ret_catalog/theme` |
| L12163 | `add_theme` | `admin_ret_catalog/theme/add` |
| L12201 | `update_theme` | `admin_ret_catalog/theme/update` |
| L12229 | `get_theme` | `admin_ret_catalog/theme/edit` |
| L12281 | `set_material_table` | `admin_ret_catalog/material` |
| L12427 | `add_material` | `admin_ret_catalog/material/add` |
| L12463 | `update_material` | `admin_ret_catalog/material/update` |
| L12491 | `get_material` | `admin_ret_catalog/material/edit` |
| L12543 | `(unknown)` | `admin_ret_catalog/material_rate` |
| L12705 | `add_mtrrate` | `admin_ret_catalog/material_rate/add` |
| L12741 | `update_mtrrate` | `admin_ret_catalog/material_rate/update` |
| L12765 | `get_materialSelLst` | `admin_ret_catalog/material_rate/material_lst` |
| L12817 | `get_materialrate` | `admin_ret_catalog/material_rate/edit` |
| L12853 | `get_uomlist` | `admin_ret_catalog/uom/active_uom` |
| L12895 | `set_stone_table` | `admin_ret_catalog/stone` |
| L13037 | `update_stone` | `admin_ret_catalog/stone/update` |
| L13065 | `get_stone` | `admin_ret_catalog/stone/edit` |
| L13175 | `set_uom_table` | `admin_ret_catalog/uom` |
| L13321 | `add_uom` | `admin_ret_catalog/uom/add` |
| L13359 | `update_uom` | `admin_ret_catalog/uom/update` |
| L13387 | `get_uom` | `admin_ret_catalog/uom/edit` |
| L13437 | `set_category_table` | `admin_ret_catalog/category` |
| L13633 | `(unknown)` | `admin_ret_catalog/category/add` |
| L13673 | `get_categorymtr` | `admin_ret_catalog/category/edit` |
| L13817 | `(unknown)` | `admin_ret_catalog/category/update` |
| L14053 | `set_karigar_table` | `admin_ret_catalog/karigar` |
| L14341 | `(unknown)` | `admin_ret_catalog/karigar/add` |
| L14423 | `(unknown)` | `admin_ret_catalog/karigar/update` |
| L14463 | `get_user` | `admin_ret_catalog/karigar/edit` |
| L14563 | `set_tag_table` | `admin_ret_catalog/tag` |
| L14707 | `add_tag` | `admin_ret_catalog/tag/add` |
| L14741 | `update_tag` | `admin_ret_catalog/tag/update` |
| L14769 | `get_tag` | `admin_ret_catalog/tag/edit` |
| L14817 | `set_tax_table` | `admin_ret_catalog/tax` |
| L14963 | `add_tax` | `admin_ret_catalog/tax/add` |
| L14999 | `update_tax` | `admin_ret_catalog/tax/update` |
| L15027 | `get_tax` | `admin_ret_catalog/tax/edit` |
| L15075 | `get_ActiveCatByMetal` | `admin_ret_catalog/get_category` |
| L15117 | `get_ActiveMetal` | `admin_ret_catalog/ret_product/active_metal` |
| L15187 | `get_taxgroup` | `admin_ret_catalog/tgrp/active_tgrp` |
| L15265 | `(unknown)` | `admin_ret_catalog/ret_product` |
| L15447 | `set_ret_sub_product_table` | `admin_ret_catalog/ret_sub_product` |
| L15583 | `get_activeTax` | `admin_ret_catalog/tax/active_tax` |
| L15713 | `deletetgrp` | `admin_ret_catalog/tgrp/delete` |
| L15769 | `set_tgrp_table` | `admin_ret_catalog/tgrp` |
| L15941 | `set_metal_table` | `admin_ret_catalog/metal` |
| L16087 | `add_metal` | `admin_ret_catalog/metal/add` |
| L16121 | `update_metal` | `admin_ret_catalog/metal/update` |
| L16149 | `get_metals` | `admin_ret_catalog/metal/edit` |
| L16201 | `get_purities` | `admin_ret_catalog/purity/active_purities` |
| L16253 | `get_ActiveRetMasters` | `admin_ret_catalog/ret_design/ajax_get_retmaster` |
| L16331 | `get_ActiveCategory` | `admin_ret_catalog/category/active_category` |
| L16423 | `get_Activeproduct` | `admin_ret_catalog/ret_product/active_list` |
| L16567 | `get_ActiveTheme` | `admin_ret_catalog/theme/active_theme` |
| L16617 | `get_ActiveKarigar` | `admin_ret_catalog/karigar/active_list` |
| L16683 | `get_ActivePurity` | `admin_ret_catalog/purity/active_purities` |
| L16749 | `get_ActiveMaterial` | `admin_ret_catalog/material/active_material` |
| L16819 | `get_hooktype` | `admin_ret_catalog/hook/active_hook` |
| L16861 | `get_ScrewType` | `admin_ret_catalog/screw/active_screw` |
| L16911 | `get_ActiveUOM` | `admin_ret_catalog/uom/active_uom` |
| L16997 | `deleteProdDetail` | `admin_ret_catalog/stone/delete` |
| L17031 | `remove_img` | `admin_ret_catalog/removeDesign_img` |
| L17065 | `set_design_table` | `admin_ret_catalog/ret_design` |
| L17207 | `delete_design` | `admin_ret_catalog/ret_design/delete` |
| L17325 | `set_screw_table` | `admin_ret_catalog/screw` |
| L17469 | `add_screw` | `admin_ret_catalog/screw/add` |
| L17505 | `update_screw` | `admin_ret_catalog/screw/update` |
| L17533 | `get_screw` | `admin_ret_catalog/screw/edit` |
| L17581 | `set_hook_table` | `admin_ret_catalog/hook` |
| L17725 | `add_hook` | `admin_ret_catalog/hook/add` |
| L17761 | `update_hook` | `admin_ret_catalog/hook/update` |
| L17789 | `get_hook` | `admin_ret_catalog/hook/edit` |
| L17839 | `get_financial_year_list` | `admin_ret_catalog/financial_year/ajax` |
| L17995 | `set_bulkprodupdated_table` | `admin_ret_catalog/bulkprodupdated` |
| L18093 | `update_product` | `admin_ret_catalog/bulkprodupdated/update` |
| L18243 | `get_purity_list` | `admin_ret_catalog/get_all_purity` |
| L18645 | `update_rate_data` | `admin_ret_catalog/update_rate_data` |
| L18689 | `set_materialrate_table` | `admin_ret_catalog/material_rate` |
| L18895 | `deletemtrrate` | `admin_ret_catalog/material_rate/delete` |
| L18931 | `update_mrrate_data` | `admin_ret_catalog/update_mrrate_data` |
| L18977 | `getActive_uom` | `admin_ret_catalog/uom/active_uom` |
| L19083 | `get_weight_range_sub_design` | `admin_ret_catalog/get_ActiveSubDesigns` |
| L19153 | `get_weight_range_product` | `admin_ret_catalog/ret_product/active_list` |
| L19223 | `(unknown)` | `admin_ret_catalog/get_weight_range_details` |
| L19257 | `get_weight_range_design` | `admin_ret_catalog/reorder_settings/active_design` |
| L19359 | `(unknown)` | `admin_ret_catalog/weight` |
| L19607 | `get_weight` | `admin_ret_catalog/weight/Edit` |
| L19671 | `add_weight` | *(dynamic url)* |
| L19827 | `get_weight_edit_data` | `admin_ret_catalog/weight/weight_Edit` |
| L19887 | `(unknown)` | `admin_ret_catalog/weight/Update` |
| L19963 | `(unknown)` | `admin_ret_catalog/get_weight_range_details` |
| L19999 | `(unknown)` | `admin_ret_catalog/get_weight_range_details` |
| L20043 | `get_weight_range_details` | `admin_ret_catalog/get_weight_range_details` |
| L20193 | `getFilteWeightRange` | `admin_ret_catalog/get_weight_range_details` |
| L20245 | `getFilterDesign` | `admin_ret_catalog/reorder_settings/active_design` |
| L20339 | `get_ActiveSubDesign` | **[EXT]** `admin_ret_reports/get_ActiveSubDesign` |
| L20401 | `get_ActiveSubDesingns` | `admin_ret_catalog/get_ActiveSubDesigns` |
| L20515 | `(unknown)` | `admin_ret_catalog/reorder_settings/ajax` |
| L20755 | `get_settingsProduct` | `admin_ret_catalog/ret_product/active_list` |
| L21109 | `(unknown)` | **[EXT]** `branch/branchname_list` |
| L21223 | `getReorderDetails` | `admin_ret_catalog/reorder_settings/edit_reorder` |
| L21419 | `add_retsettings` | `admin_ret_catalog/reorder_settings/add` |
| L21457 | `add_retsettings_new` | `admin_ret_catalog/reorder_settings/add` |
| L21515 | `update_retsettings` | `admin_ret_catalog/reorder_settings/update` |
| L21567 | `(unknown)` | `admin_ret_catalog/reorder_settings/weight_range` |
| L21639 | `get_wt_range_product` | `admin_ret_catalog/reorder_settings/weight_range` |
| L21717 | `get_active_sub_designs` | `admin_ret_catalog/get_ActiveSubDesigns` |
| L21767 | `get_design_product` | `admin_ret_catalog/reorder_settings/active_design` |
| L21837 | `product_design` | `admin_ret_catalog/reorder_settings/active_design` |
| L21979 | `get_delivery_details` | `admin_ret_catalog/ret_delivery/ajax` |
| L22157 | `add_location` | `admin_ret_catalog/ret_delivery/Add` |
| L22187 | `update_delivery` | `admin_ret_catalog/ret_delivery/Update` |
| L22215 | `get_delivery` | `admin_ret_catalog/ret_delivery/Edit` |
| L22245 | `get_Activesize` | `admin_ret_catalog/get_Activesize` |
| L22413 | `add_newsize` | `admin_ret_catalog/ret_size/Add` |
| L22451 | `new_add_size` | `admin_ret_catalog/ret_size/Add` |
| L22525 | `(unknown)` | `admin_ret_catalog/ret_size/ajax` |
| L22719 | `get_size` | `admin_ret_catalog/ret_size/Edit` |
| L22789 | `(unknown)` | `admin_ret_catalog/ret_size/Update` |
| L22823 | `get_metal_type_list` | `admin_ret_catalog/metal_type/ajax` |
| L22949 | `(unknown)` | `admin_ret_catalog/metal_type/add` |
| L23003 | `(unknown)` | `admin_ret_catalog/metal_type/add` |
| L23047 | `get_old_metal_type` | `admin_ret_catalog/metal_type/edit` |
| L23085 | `(unknown)` | `admin_ret_catalog/metal_type/update` |
| L23119 | `get_old_metal_cat_list` | `admin_ret_catalog/old_metal_cat/ajax` |
| L23266 | `(unknown)` | `admin_ret_catalog/old_metal_cat/add` |
| L23336 | `(unknown)` | `admin_ret_catalog/old_metal_cat/add` |
| L23384 | `get_old_metal_category` | `admin_ret_catalog/old_metal_cat/edit` |
| L23424 | `(unknown)` | `admin_ret_catalog/old_metal_cat/update` |
| L23448 | `get_ActiveOldMetal` | `admin_ret_catalog/old_metal_cat/active_oldmetal` |
| L23504 | `get_old_metal_rate` | `admin_ret_catalog/old_metal_rate` |
| L23638 | `edit_old_metal_rate` | `admin_ret_catalog/old_metal_rate/edit` |
| L23742 | `(unknown)` | `admin_ret_catalog/old_metal_rate/add` |
| L23780 | `(unknown)` | `admin_ret_catalog/old_metal_rate/update` |
| L23886 | `add_new_section` | `admin_ret_catalog/ret_section/Add` |
| L23936 | `add_section` | `admin_ret_catalog/ret_section/Add` |
| L23966 | `get_section_details` | `admin_ret_catalog/ret_section/ajax` |
| L24114 | `get_section` | `admin_ret_catalog/ret_section/Edit` |
| L24226 | `(unknown)` | `admin_ret_catalog/ret_section/Update` |
| L24324 | `add_dev` | `admin_ret_catalog/ret_pay_device/Add` |
| L24366 | `get_pay_device` | `admin_ret_catalog/ret_pay_device/ajax` |
| L24552 | `(unknown)` | `admin_ret_catalog/ret_pay_device/Update` |
| L24616 | `get_dev` | `admin_ret_catalog/ret_pay_device/Edit` |
| L24690 | `add_acc` | `admin_ret_catalog/ret_account/Add` |
| L24732 | `get_acc_head` | `admin_ret_catalog/ret_account/ajax` |
| L24910 | `(unknown)` | `admin_ret_catalog/ret_account/Update` |
| L24996 | `get_acc` | `admin_ret_catalog/ret_account/Edit` |
| L25055 | `add_crdr_ledger` | `admin_ret_catalog/ret_crdr_ledger/Add` |
| L25076 | `get_crdr_ledger` | `admin_ret_catalog/ret_crdr_ledger/ajax` |
| L25163 | `(unknown)` | `admin_ret_catalog/ret_crdr_ledger/Update` |
| L25198 | `get_crdr` | `admin_ret_catalog/ret_crdr_ledger/Edit` |
| L25240 | `breakeve_branch_details` | **[EXT]** `branch/branchname_list` |
| L25344 | `add_breakeven_name` | `admin_ret_catalog/ret_breakeven_logs/add` |
| L25414 | `get_acc_brlog` | `admin_ret_catalog/ret_breakeven_logs/ajax` |
| L25652 | `(unknown)` | `admin_ret_catalog/ret_breakeven_logs/Update` |
| L25742 | `get_brlogv` | `admin_ret_catalog/ret_breakeven_logs/Edit` |
| L25802 | `add_stak` | `admin_ret_catalog/stock_issue_type/Add` |
| L25838 | `get_stock_issue` | `admin_ret_catalog/stock_issue_type/ajax` |
| L26032 | `get_stock` | `admin_ret_catalog/stock_issue_type/Edit` |
| L26108 | `(unknown)` | `admin_ret_catalog/stock_issue_type/Update` |
| L26136 | `get_product_RetailSections` | `admin_ret_catalog/get_section` |
| L26192 | `getRetailSections` | `admin_ret_catalog/get_section` |
| L26236 | `getActiveSections` | `admin_ret_catalog/get_section` |
| L26362 | `update_product_section` | `admin_ret_catalog/update_product_section` |
| L26404 | `get_feedback_details` | `admin_ret_catalog/feedback/ajax` |
| L26520 | `add_feedback` | `admin_ret_catalog/feedback/add` |
| L26560 | `get_feedback` | `admin_ret_catalog/feedback/edit` |
| L26614 | `(unknown)` | `admin_ret_catalog/feedback/update` |
| L26712 | `add_charges` | `admin_ret_catalog/charges/Add` |
| L26772 | `add_charges_save_and_new` | `admin_ret_catalog/charges/Add` |
| L26826 | `get_active_charges` | `admin_ret_catalog/charges` |
| L26862 | `get_charges_list` | `admin_ret_catalog/charges` |
| L27000 | `edit_charges` | `admin_ret_catalog/charges/edit` |
| L27072 | `update_charges` | `admin_ret_catalog/charges/update` |
| L27134 | `delete_charge` | `admin_ret_catalog/charges/delete` |
| L27234 | `get_ActiveSubDesigns` | `admin_ret_catalog/ret_sub_design/active_list` |
| L27296 | `get_SubDesigns` | `admin_ret_catalog/ret_sub_design/active_list` |
| L27378 | `get_ActiveDesign` | `admin_ret_catalog/get_ActiveDesign` |
| L27440 | `get_sub_design_details` | `admin_ret_catalog/ret_sub_design` |
| L27609 | `get_product_mapping_details` | `admin_ret_catalog/ret_products_mapping` |
| L27772 | `update_product_mapping` | `admin_ret_catalog/update_product_design_mapping` |
| L27878 | `(unknown)` | `admin_ret_catalog/delete_product_design_mapping` |
| L27930 | `get_ProductDesign` | `admin_ret_catalog/get_ProductDesign` |
| L27992 | `get_sub_design_mapping_details` | `admin_ret_catalog/ret_subdesign_mapping` |
| L28349 | `(unknown)` | *(dynamic url)* |
| L28395 | `subdesign_description_update` | `admin_ret_catalog/ret_sub_design/sub_design_description` |
| L28453 | `(unknown)` | `admin_ret_catalog/update_subdesign_des` |
| L28539 | `update_sup_design_mapping` | `admin_ret_catalog/update_subdesign_mapping` |
| L28597 | `get_karigar_products` | `admin_ret_catalog/ret_sub_design/get_karigar_products` |
| L28647 | `(unknown)` | `admin_ret_catalog/update_karigar_products` |
| L28733 | `(unknown)` | `admin_ret_catalog/delete_sub_design_mapping` |
| L28869 | `getsub_design_images` | `admin_ret_catalog/ret_sub_design/sub_design_images` |
| L29085 | `getSubDesignName` | `admin_ret_catalog/getSubDesignName` |
| L29171 | `getDesignName` | `admin_ret_catalog/getDesignName` |
| L29251 | `get_ProductDesigns` | `admin_ret_catalog/get_ProductDesign` |
| L29363 | `wastage_view` | `admin_ret_catalog/get_wastage_details` |
| L29449 | `attribute_view` | `admin_ret_catalog/get_design_attr_values` |
| L29521 | `_ajaxCallPost` | *(dynamic url — generic POST helper)* |
| L29581 | `set_attribute_table` | `admin_ret_catalog/attribute` |
| L30311 | `get_active_design_products` | `admin_ret_catalog/get_active_design_products` |
| L30397 | `get_ActiveDesign` | `admin_ret_catalog/get_ActiveDesign` |
| L30461 | `set_karigar_product_list` | `admin_ret_catalog/ret_karigar_product` |
| L30609 | `update_product_mapping` | `admin_ret_catalog/update_product_mapping` |
| L30717 | `(unknown)` | `admin_ret_catalog/delete_product_mapping` |
| L30779 | `get_ActiveCounter` | `admin_ret_catalog/get_ActiveCounter` |
| L30833 | `get_ActiveBranchFloor` | `admin_ret_catalog/get_ActiveBranchFloor` |
| L30887 | `get_web_device_list` | `admin_ret_catalog/web_devices/ajax` |
| L31061 | `add_collection` | `admin_ret_catalog/ret_collection/save` |
| L31123 | `set_collection_list` | `admin_ret_catalog/ret_collection/ajax` |
| L31265 | `get_collection` | `admin_ret_catalog/ret_collection/edit` |
| L31321 | `update_collection` | `admin_ret_catalog/ret_collection/update` |
| L31377 | `get_repair_list` | `admin_ret_catalog/repair_master/ajax` |
| L31523 | `add_repair_damage_item` | `admin_ret_catalog/repair_master/save` |
| L31595 | `get_repair_item_list` | `admin_ret_catalog/repair_master/edit` |
| L31653 | `update_repair_master_item` | `admin_ret_catalog/repair_master/update` |
| L31707 | `set_product_division_table` | `product_division/ajax` |
| L31889 | `add_product_division` | `product_division/add` |
| L31921 | `update_product_division` | `product_division/update/{id}` |
| L31947 | `get_product_division` | `product_division/edit/{id}` |
| L31995 | `get_MetalName` | `admin_ret_catalog/MetalSelect` |
| L32051 | `SelectPurity` | `admin_ret_catalog/PuritySelect` |
| L32111 | `Metalrate` | `admin_ret_catalog/MetalRates` |
| L32289 | `SavePurityrate` | `admin_ret_catalog/ret_metalpurity/add` |
| L32333 | `get_purityratemetals` | `admin_ret_catalog/ret_metalpurity/edit` |
| L32401 | `update_purity_rate` | *(dynamic url)* |
| L32429 | `Metal_purity_table` | `admin_ret_catalog/ret_metalpurity` |
| L32525 | `set_bnk_deposit_table` | `admin_ret_catalog/bank_deposit/ajax_list` |
| L32705 | `getDepositBranch` | **[EXT]** `branch/branchname_list` |
| L32751 | `get_cash_in_hand` | `admin_ret_catalog/bank_deposit/get_cash_in_hand` |
| L32915 | `get_KarigarWastages` | `admin_ret_catalog/karigar_approval/wastages_list` |
| L33251 | `get_KarigarStones` | `admin_ret_catalog/karigar_approval/stones_list` |
| L33371 | `get_KarigarCharges` | `admin_ret_catalog/karigar_approval/charges_list` |
| L33641 | `vendor_at_send_otp` | `admin_ret_catalog/vendor_sendotp` |
| L33737 | `vendor_verify_otp` | `admin_ret_catalog/vendor_verify_otp` |
| L33863 | `karigarApprovedList` | `admin_ret_catalog/karigar_approval/save` |
| L34519 | `get_karigar_product_details` | `admin_ret_catalog/karigar/product_details` |
| L35147 | `get_KarigarProducts` | `admin_ret_catalog/ret_product/active_list` |
| L35217 | `get_KarigarWastPurity` | `admin_ret_catalog/get_KarigarWastPurity` |
| L35317 | `get_ActiveDesigns` | `admin_ret_catalog/get_active_design_products` |
| L35457 | `get_ActiveSubDesigns` | `admin_ret_catalog/get_ActiveSubDesigns` |
| L35521 | `get_WastApprvKarigars` | `admin_ret_catalog/get_WastApprvKarigars` |
| L36479 | `get_branches` | **[EXT]** `branch/branchname_list` |
| L37115 | `get_stone_details` | `admin_ret_catalog/karigar/stone_details` |
| L37285 | `get_ActiveStoneType` | **[EXT]** `admin_ret_tagging/getStoneTypes` |
| L37397 | `getActiveStones` | *(dynamic url)* |
| L37469 | `get_ActiveKYC` | `admin_ret_catalog/get_ActiveKYC` |
| L37611 | `get_kyc_details` | `admin_ret_catalog/karigar/kyc_details` |
| L37769 | `getKycDetails` | `admin_ret_catalog/getKycDetails` |
| L38742 | `view_wastage_imgs` | `admin_ret_catalog/get_kar_img_by_id` |
| L39126 | `do_upload` | `admin_ret_catalog/do_upload` |
| L39226 | `set_quality_table` | `admin_ret_catalog/diamond` |
| L39366 | `get_Activeclarity` | `admin_ret_catalog/diamond/active_diamondclarity` |
| L39438 | `get_Activecolor` | `admin_ret_catalog/diamond/active_diamondcolor` |
| L39508 | `get_Activecut` | `admin_ret_catalog/diamond/active_diamondcut` |
| L39578 | `get_Activeshape` | `admin_ret_catalog/diamond/active_diamondshape` |
| L39674 | `(unknown)` | `admin_ret_catalog/check_quality_code` |
| L39938 | `(unknown)` | `admin_ret_catalog/get_quality_code` |
| L39984 | `(unknown)` | `admin_ret_catalog/diamond_rate/active_diamondquality` |
| L40056 | `(unknown)` | `admin_ret_catalog/diamond_rate` |
| L40234 | `(unknown)` | *(dynamic url)* |
| L40806 | `update_weight_range_settings` | *(dynamic url)* |
| L40870 | `get_wastage_settings_details` | `admin_ret_catalog/wastage_mc_settings/ajax` |
| L41166 | `wastage_view_` | `admin_ret_catalog/selling_price/weight_details` |
| L41327 | `get_productWeightDetails` | `admin_ret_catalog/wastage_mc_settings/get_product_weight` |
| L41415 | `wastagesetting_view` | `admin_ret_catalog/wastage_mc_settings/get_wastage_details` |
| L41507 | `get_wastage_mc_settings` | `admin_ret_catalog/wastage_mc_settings/get_wastage_mc_settings` |
| L41609 | `get_wastage_mc_setting_WeightDetails` | `admin_ret_catalog/wastage_mc_settings/get_wastage_mc_setting_WeightDetails` |
| L41733 | `addNewKarigar` | *(dynamic url)* |
| L41971 | `add_NewContractPrice` | *(dynamic url)* |
| L42109 | `add_NewKarStones` | *(dynamic url)* |
| L42225 | `add_NewKycDetails` | *(dynamic url)* |
| L42335 | `set_shape_table` | `admin_ret_catalog/shape/ajax` |
| L42465 | `add_shape` | `admin_ret_catalog/shape/Add` |
| L42503 | `update_shape` | `admin_ret_catalog/shape/Update` |
| L42535 | `get_shape` | `admin_ret_catalog/shape/Edit` |
| L42941 | `get_reorder_branch_details` | **[EXT]** `branch/branchname_list` |
| L43001 | `get_ProductforReorder` | `admin_ret_catalog/ret_product/active_list` |
| L43087 | `get_designforReorder` | `admin_ret_catalog/get_ProductDesign` |
| L43141 | `get_Activesize_forReorder` | `admin_ret_catalog/get_Activesize` |
| L43213 | `get_reorder_sub_designs` | `admin_ret_catalog/get_ActiveSubDesigns` |
| L43301 | `get_wtrange_forReoreder` | `admin_ret_catalog/reorder_settings/weight_range` |
| L43489 | `(unknown)` | *(dynamic url)* |
| L43565 | `getExistingKarigars` | `admin_ret_catalog/getExistingKarigars` |
| L43835 | `(unknown)` | *(dynamic url)* |
| L43905 | `set_selling_diamond_rate_table` | `admin_ret_catalog/selling_diamond_rate` |
| L44538 | `add_stone_rate_settings` | *(dynamic url)* |
| L44624 | `get_stone_rates_settings_details` | `admin_ret_catalog/ret_stone_rate_settings/ajax` |
| L44878 | `getStoneRateSettings` | `admin_ret_catalog/ret_stone_rate_settings/stone_rate_details` |
| L45148 | `get_ActiveStoneTypeForRate` | **[EXT]** `admin_ret_tagging/getStoneTypes` |
| L45220 | `getActiveStonesForRate` | *(dynamic url)* |
| L45280 | `getActive_quality_codeForRate` | `admin_ret_catalog/get_quality_code` |
| L45400 | `getActiveDesignForRate` | `admin_ret_catalog/get_active_design_products` |
| L45468 | `getActive_SubdesignForRate` | `admin_ret_catalog/get_ActiveSubDesigns` |
| L45798 | `getLooseProductRateSettings` | `admin_ret_catalog/ret_stone_rate_settings/product_rate_details` |
| L46106 | `getLooseStonesProduct` | `admin_ret_catalog/getLooseStonesProduct` |
| L46194 | `getLooseStonesDesign` | `admin_ret_catalog/get_active_design_products` |
| L46264 | `getLooseStoneSubDesign` | `admin_ret_catalog/get_ActiveSubDesigns` |
| L46416 | `add_stone_product_rate_settings` | *(dynamic url)* |
| L46530 | `set_dayclose_table` | `admin_ret_catalog/day_close/Ajax` |
| L46680 | `(unknown)` | *(dynamic url)* |
| L46858 | `get_cover_up_list` | `admin_ret_catalog/cover_up` |
| L46996 | `cover_up` | **[EXT]** `admin_app_api/add_cover_up` |
| L47054 | `get_cancel_reason_details` | `admin_ret_catalog/ret_qc_cancel_reason/ajax` |
| L47170 | `get_cancel_reason` | `admin_ret_catalog/ret_qc_cancel_reason/Edit` |
| L47278 | `add_reason` | `admin_ret_catalog/ret_qc_cancel_reason/Add` |
| L47332 | `(unknown)` | `admin_ret_catalog/ret_qc_cancel_reason/Update` |
| L47532 | `add_pro_grpname` | `admin_ret_catalog/product_grouping/add` |
| L47576 | `set_product_grp_table` | `admin_ret_catalog/product_grouping` |
| L47778 | `get_product_grp` | `admin_ret_catalog/product_grouping/edit` |
| L47874 | `update_pro_grpname` | `admin_ret_catalog/product_grouping/update` |
| L47908 | `getActiveproductgrp` | `admin_ret_catalog/get_product_group` |
| L48217 | `checkGSTAvail` | **[EXT]** `admin_ret_purchase/karigar_gst_available` |
| L48234 | `checkPANAvail` | **[EXT]** `admin_ret_purchase/karigar_pan_available` |
| L48251 | `checkAADHARAvail` | **[EXT]** `admin_ret_purchase/karigar_aadhar_available` |
| L48267 | `checkAADHARAvail` | `admin_ret_catalog/web_devices/check_token_duplicates` |
| L48376 | `add_stone_disc` | `admin_ret_catalog/stone_discount/add` |
| L48408 | `set_stn_disc_table` | `admin_ret_catalog/stone_discount` |
| L48621 | `get_stn_disc` | `admin_ret_catalog/stone_discount/edit` |
| L48675 | `update_stn_disc` | `admin_ret_catalog/stone_discount/update` |
| L48788 | `add_wastage_disc` | *(dynamic url)* |
| L48905 | `(unknown)` | `admin_ret_catalog/wastage_discount` |
| L49101 | `get_va_disc` | `admin_ret_catalog/wastage_discount/Edit` |
| L49165 | `update_wastage` | `admin_ret_catalog/wastage_discount/Update` |

> **Cross-module**: `branch/branchname_list` (6×), `admin_ret_tagging/getStoneTypes` (2×), `admin_ret_purchase/karigar_*` (3× — GST/PAN/AADHAR), `admin_ret_reports/get_ActiveSubDesign` (1×), `admin_app_api/add_cover_up` (1×), legacy `product/delect_prodDetail` (1×)

## 7d. Table → Methods Reverse Map (Key Tables)

| Table | Read By (Model) | Written By (Model/Controller) |
|---|---|---|
| `ret_product_master` | `ajax_get_retProduct`, `ajax_get_TaxProd`, `get_ret_product`, `get_product_section`, `getActiveProducts`, `get_ActiveProducts`, `get_NonTagProducts`, `get_productData` | `insertData()`, `updateData()`, `deleteData()` via controller |
| `ret_category` | `ajax_getcategory`, `get_ret_category`, `getActiveCategorymtr`, `get_catByMetal`, `getHsnCode` | `insertData()`, `updateData()`, `deleteData()` |
| `ret_metal_cat_purity` | `get_category_purity`, `getCatPurity` | `insertData()`, `deleteData()` |
| `ret_purity` | `ajax_getPurity`, `get_purity`, `getActivePurity` | `insert_purity`, `update_purity`, `deleteData`, `insertData` |
| `ret_color` | `ajax_getcolor`, `get_color`, `getActivecolor` | `insert_color`, `update_color`, `delete_color` |
| `ret_cut` | `ajax_getcut`, `get_cut`, `getActiveCut` | `insert_cut`, `update_cut`, `delete_cut` |
| `ret_clarity` | `ajax_getclarity`, `get_clarity`, `getActiveClarity` | `insert_clarity`, `update_clarity`, `delete_clarity` |
| `ret_karigar` | `ajax_getkarigar`, `get_karigar`, `mobile_available`, `email_available`, `getExistingKarigars`, `getActiveKarigar` | `insertData()`, `updateData()` |
| `ret_karikar_items_wastage` | `get_karigar_wastages`, `get_karigar_wise_wastage` | `insertData()`, `updateData()` |
| `ret_design_master` | `ajax_get_design`, `get_ret_design`, `get_ActiveDesign`, `getDesignName` | `insertData()`, `updateData()`, `deleteData()` |
| `product` | `ajax_getProduct`, `get_product` | `insert_product`, `update_product`, `delete_product` |
| `product_images` | `get_prodimage`, `get_defaultProdimage` | `insertProdImage`, `deleteProdImage`, `delete_prodimage` |
| `ret_taging` | `getItemsinTagDetails` (READ) | — (owned by Tagging module) |
| `ret_financial_year` | `ajax_get_financial_year_List`, `get_financialyear_by_status`, `GetFinancialYear` | `insertData`, `update_financialData`, `setFinancialYearStatus` |
| `ret_taxgroupmaster` | `ajax_gettgrp`, `get_tgrp`, `getActivetgrp` | `insertData`, `updateData` |
| `ret_stone_rate_settings` | `ajax_get_stone_rates_settings`, `getStoneRateSettings` | `insertData`, `updateData` |
| `web_registered_devices` | `get_DeviceList`, `get_deviceNames` | `web_device_settingDB` |
