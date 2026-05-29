-- =====================================================
-- Source DB Export Queries for Port Max Migration
-- Run on SOURCE server (ret_ schema matching retail_dev)
-- Share results as CSV or dump the result DB here
-- =====================================================

-- =====================================================
-- SUMMARY COUNTS (run this first and share)
-- =====================================================
SELECT 'ret_karigar' AS tbl, COUNT(*) AS cnt FROM ret_karigar WHERE status_karigar = 1
UNION ALL SELECT 'ret_category', COUNT(*) FROM ret_category WHERE status = 1
UNION ALL SELECT 'ret_purity', COUNT(*) FROM ret_purity WHERE status = 1
UNION ALL SELECT 'ret_product_master', COUNT(*) FROM ret_product_master WHERE product_status = 1
UNION ALL SELECT 'ret_design_master', COUNT(*) FROM ret_design_master WHERE design_status = 1
UNION ALL SELECT 'ret_sub_design_master', COUNT(*) FROM ret_sub_design_master WHERE status = 1
UNION ALL SELECT 'ret_section', COUNT(*) FROM ret_section WHERE status = 1
UNION ALL SELECT 'ret_size', COUNT(*) FROM ret_size
UNION ALL SELECT 'ret_stone', COUNT(*) FROM ret_stone WHERE stone_status = 1
UNION ALL SELECT 'ret_charges', COUNT(*) FROM ret_charges
UNION ALL SELECT 'ret_taging', COUNT(*) FROM ret_taging WHERE tag_status IN (0, 1)
UNION ALL SELECT 'ret_taging_stone', COUNT(*) FROM ret_taging_stone
UNION ALL SELECT 'ret_taging_charges', COUNT(*) FROM ret_taging_charges
UNION ALL SELECT 'ret_product_mapping', COUNT(*) FROM ret_product_mapping
UNION ALL SELECT 'ret_sub_design_mapping', COUNT(*) FROM ret_sub_design_mapping
UNION ALL SELECT 'ret_product_section', COUNT(*) FROM ret_product_section
UNION ALL SELECT 'ret_metal_cat_purity', COUNT(*) FROM ret_metal_cat_purity;


-- =====================================================
-- 1. KARIGAR
-- =====================================================
SELECT 
    k.id_karigar       AS old_id,
    k.karigar_type,
    k.firstname        AS first_name,
    k.lastname         AS last_name,
    k.company          AS company_name,
    k.code_karigar     AS short_code,
    k.contactno1       AS mobile,
    k.address1,
    k.address2,
    k.address3,
    cn.name            AS country_name,
    st.name            AS state_name,
    ct.name            AS city_name,
    k.gst_number,
    k.is_tcs,
    k.tcs_tax,
    k.is_tds,
    k.tds_tax,
    k.fin_year_code,
    k.opening_balance_amount AS opening_amount
FROM ret_karigar k
LEFT JOIN country cn ON k.id_country = cn.id_country
LEFT JOIN state st ON k.id_state = st.id_state
LEFT JOIN city ct ON k.id_city = ct.id_city
WHERE k.status_karigar = 1;


-- =====================================================
-- 2. CATEGORY
-- =====================================================
SELECT 
    c.id_ret_category  AS old_id,
    c.name             AS category_name,
    c.hsn_code,
    c.cat_code         AS short_code,
    c.cat_type         AS category_type,
    c.is_multi_metal_cateory AS is_multi_metal,
    m.metal            AS metal_name,
    tg.tgrp_name       AS tax_group,
    c.description
FROM ret_category c
LEFT JOIN metal m ON c.id_metal = m.id_metal
LEFT JOIN ret_taxgroupmaster tg ON c.tgrp_id = tg.tgrp_id
WHERE c.status = 1;


-- =====================================================
-- 3. PURITY
-- =====================================================
SELECT 
    id_purity          AS old_id,
    purity             AS purity_name,
    description
FROM ret_purity
WHERE status = 1;


-- =====================================================
-- 4. CATEGORY → PURITY MAP
-- =====================================================
SELECT 
    c.name             AS category_name,
    p.purity           AS purity_name
FROM ret_metal_cat_purity cpm
LEFT JOIN ret_category c ON cpm.id_category = c.id_ret_category
LEFT JOIN ret_purity p ON cpm.id_purity = p.id_purity;


-- =====================================================
-- 5. PRODUCT MASTER
-- =====================================================
SELECT 
    p.pro_id           AS old_id,
    p.product_name,
    p.product_short_code AS short_code,
    p.hsn_code,
    p.description,
    p.product_status     AS status,
    m.metal              AS metal_name,
    c.name               AS category_name,
    tg.tgrp_name         AS tax_group,
    p.tax_type,
    tg.tgrp_name         AS pur_tax_group,
    p.no_of_pieces,
    p.stock_type,
    u.uom_name           AS uom,
    p.purchase_mode      AS purchase_based_on,
    p.sales_mode         AS sales_based_on,
    p.reorder_based_on,
    p.calculation_based_on
FROM ret_product_master p
LEFT JOIN metal m ON p.metal_type = m.id_metal
LEFT JOIN ret_category c ON p.cat_id = c.id_ret_category
LEFT JOIN ret_taxgroupmaster tg ON p.tgrp_id = tg.tgrp_id
LEFT JOIN ret_uom u ON p.uom_id = u.uom_id
WHERE p.product_status = 1;


-- =====================================================
-- 6. DESIGN MASTER
-- =====================================================
SELECT 
    design_no          AS old_id,
    design_name,
    design_code        AS short_code
FROM ret_design_master
WHERE design_status = 1;


-- =====================================================
-- 7. SUB DESIGN MASTER
-- =====================================================
SELECT 
    id_sub_design      AS old_id,
    sub_design_name,
    sub_design_code    AS short_code
FROM ret_sub_design_master
WHERE status = 1;


-- =====================================================
-- 8. PRODUCT → DESIGN MAPPING
-- =====================================================
SELECT 
    p.product_name,
    d.design_name
FROM ret_product_mapping pm
LEFT JOIN ret_product_master p ON pm.pro_id = p.pro_id
LEFT JOIN ret_design_master d ON pm.id_design = d.design_no;


-- =====================================================
-- 9. PRODUCT → SUB DESIGN MAPPING
-- =====================================================
SELECT 
    p.product_name,
    d.design_name,
    sd.sub_design_name
FROM ret_sub_design_mapping sdm
LEFT JOIN ret_product_master p ON sdm.id_product = p.pro_id
LEFT JOIN ret_design_master d ON sdm.id_design = d.design_no
LEFT JOIN ret_sub_design_master sd ON sdm.id_sub_design = sd.id_sub_design;


-- =====================================================
-- 10. SECTION / COUNTER
-- =====================================================
SELECT 
    id_section         AS old_id,
    section_name,
    section_short_code AS short_code,
    is_home_bill_counter
FROM ret_section
WHERE status = 1;


-- =====================================================
-- 11. PRODUCT → SECTION MAPPING
-- =====================================================
SELECT 
    p.product_name,
    s.section_name
FROM ret_product_section ps
LEFT JOIN ret_product_master p ON ps.pro_id = p.pro_id
LEFT JOIN ret_section s ON ps.id_section = s.id_section;


-- =====================================================
-- 12. SIZE
-- =====================================================
SELECT 
    sz.id_size         AS old_id,
    p.product_name,
    sz.value           AS size_value,
    sz.name            AS size_uom,
    sz.active          AS status
FROM ret_size sz
LEFT JOIN ret_product_master p ON sz.id_product = p.pro_id;


-- =====================================================
-- 13. STONE MASTER
-- =====================================================
SELECT 
    s.stone_id         AS old_id,
    s.stone_name,
    s.stone_code       AS short_code,
    st.stone_type,
    u.uom_name         AS uom,
    s.is_certificate_req AS certificate_req,
    s.is_4c_req        AS four_c_req
FROM ret_stone s
LEFT JOIN ret_stone_type st ON s.stone_type = st.id_stone_type
LEFT JOIN ret_uom u ON s.uom_id = u.uom_id
WHERE s.stone_status = 1;


-- =====================================================
-- 14. CHARGES MASTER
-- =====================================================
SELECT 
    id_charge          AS old_id,
    name_charge        AS charges_name,
    code_charge        AS short_code,
    value_charge       AS amount,
    charge_tax         AS charge_tax_percent,
    tag_display
FROM ret_charges;


-- =====================================================
-- 15. TAG DETAILS → ret_import_tag_details temp table
-- Columns match temp table for direct import
-- =====================================================
SELECT 
    -- Old IDs (source system references)
    t.cat_type             AS old_metal_id,
    p.cat_id               AS old_category_id,
    t.product_id           AS old_product_id,
    t.design_id            AS old_design_id,
    t.id_sub_design        AS old_sub_design_id,
    t.purity               AS old_purity_id,
    t.current_branch       AS old_branch_id,
    l.gold_smith           AS old_karigar_id,
    t.id_section           AS old_counter_id,
    t.size                 AS old_size_id,

    -- Name columns (for lookup matching in target)
    m.metal                AS metal_name,
    c.name                 AS category_name,
    p.product_name,
    d.design_name,
    sd.sub_design_name,
    pu.purity              AS purity_name,
    b.name                 AS branch_name,
    kar.firstname          AS karigar_name,
    sec.section_name       AS counter_name,
    sz.value               AS size_value,
    sz.name                AS size_name,

    -- Numeric fields
    t.piece                AS pieces,
    t.gross_wt,
    t.net_wt,
    t.less_wt,
    p.sales_mode           AS sales_type,
    p.calculation_based_on AS prod_calc_based_on,
    t.calculation_based_on AS tag_calc_based_on,
    t.retail_max_wastage_percent AS wastage_per,
    t.tag_mc_type          AS mc_type,
    t.tag_mc_value         AS mc_value,

    -- String fields
    t.tag_code             AS tag_number,
    t.hu_id                AS huid1,
    t.hu_id2               AS huid2
FROM ret_taging t
LEFT JOIN ret_product_master p ON t.product_id = p.pro_id
LEFT JOIN ret_category c ON p.cat_id = c.id_ret_category
LEFT JOIN metal m ON t.cat_type = m.id_metal
LEFT JOIN ret_design_master d ON t.design_id = d.design_no
LEFT JOIN ret_sub_design_master sd ON t.id_sub_design = sd.id_sub_design
LEFT JOIN ret_section sec ON t.id_section = sec.id_section
LEFT JOIN ret_purity pu ON t.purity = pu.id_purity
LEFT JOIN branch b ON t.current_branch = b.id_branch
LEFT JOIN ret_lot_inwards l ON l.lot_no = t.tag_lot_id
LEFT JOIN ret_karigar kar ON kar.id_karigar = l.gold_smith
LEFT JOIN ret_size sz ON sz.id_size = t.size
WHERE t.tag_status IN (0, 1);


-- =====================================================
-- 16. STONE DETAILS → ret_import_stone_details temp table
-- =====================================================
SELECT 
    -- Old IDs
    ts.stone_id            AS old_stone_id,
    sm.stone_type          AS old_stone_type_id,
    ts.uom_id              AS old_uom_id,

    -- Lookup columns
    t.tag_code             AS tag_number,
    rst.stone_type         AS stone_type_name,
    sm.stone_name,
    u.uom_name,
    ts.is_apply_in_lwt     AS is_less_id,

    -- Numeric fields
    ts.pieces,
    ts.wt                  AS stone_wt,
    ts.stone_cal_type      AS stone_calc_type,
    ts.rate_per_gram,
    ts.amount
FROM ret_taging_stone ts
LEFT JOIN ret_taging t ON ts.tag_id = t.tag_id
LEFT JOIN ret_stone sm ON ts.stone_id = sm.stone_id
LEFT JOIN ret_stone_type rst ON rst.id_stone_type = sm.stone_type
LEFT JOIN ret_uom u ON ts.uom_id = u.uom_id
WHERE t.tag_status IN (0, 1);


-- =====================================================
-- 17. TAG CHARGES → ret_import_charges temp table
-- =====================================================
SELECT 
    t.tag_code             AS tag_number,
    ch.name_charge         AS charge_name,
    tc.charge_value
FROM ret_taging_charges tc
LEFT JOIN ret_taging t ON tc.tag_id = t.tag_id
LEFT JOIN ret_charges ch ON tc.charge_id = ch.id_charge
WHERE t.tag_status IN (0, 1);
