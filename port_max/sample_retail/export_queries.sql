-- ============================================================
-- Retail Data Export Queries (Verified against retail_dev)
-- Each query matches a sheet in tag_import_template.xlsx
-- ============================================================


-- 1. karigar  (618 rows)
SELECT 
    k.karigar_type,
    k.firstname        AS first_name,
    k.lastname         AS last_name,
    k.company          AS company_name,
    k.code_karigar     AS short_code,
    k.contactno1       AS mobile,
    k.address1,
    k.address2,
    k.address3,
    k.id_country       AS country_id,
    cn.name            AS country_name,
    k.id_state         AS state_id,
    st.name            AS state_name,
    k.id_city          AS city_id,
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


-- 2. category  (171 rows)
SELECT 
    c.id_ret_category  AS category_id,
    c.name             AS category_name,
    c.hsn_code,
    c.cat_code         AS short_code,
    c.cat_type         AS category_type,
    c.is_multi_metal_cateory AS is_multi_metal,
    c.id_metal         AS metal_id,
    m.metal            AS metal_name,
    c.tgrp_id          AS tax_group_id,
    tg.tgrp_name       AS tax_group,
    c.description
FROM ret_category c
LEFT JOIN metal m ON c.id_metal = m.id_metal
LEFT JOIN ret_taxgroupmaster tg ON c.tgrp_id = tg.tgrp_id
WHERE c.status = 1;


-- 3. purity  (26 rows)
SELECT 
    id_purity          AS purity_id,
    purity             AS purity_name,
    description
FROM ret_purity
WHERE status = 1;


-- 4. category_purity_map  (379 rows)
SELECT 
    cpm.id_category    AS category_id,
    c.name             AS category_name,
    cpm.id_purity      AS purity_id,
    p.purity           AS purity_name
FROM ret_metal_cat_purity cpm
LEFT JOIN ret_category c ON cpm.id_category = c.id_ret_category
LEFT JOIN ret_purity p ON cpm.id_purity = p.id_purity;


-- 5. product  (435 rows)
SELECT 
    p.pro_id             AS product_id,
    p.product_name,
    p.product_short_code AS short_code,
    p.hsn_code,
    p.metal_type         AS metal_id,
    m.metal              AS metal_name,
    p.cat_id             AS category_id,
    c.name               AS category_name,
    p.tax_type,
    p.tgrp_id            AS pur_tax_group_id,
    tg.tgrp_name         AS pur_tax_group,
    p.no_of_pieces,
    p.stock_type,
    p.uom_id,
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


-- 6. design  (347 rows)
SELECT 
    design_no   AS design_id,
    design_name,
    design_code AS short_code
FROM ret_design_master
WHERE design_status = 1;


-- 7. sub_design  (1119 rows)
SELECT 
    id_sub_design   AS sub_design_id,
    sub_design_name,
    sub_design_code AS short_code
FROM ret_sub_design_master
WHERE status = 1;


-- 8. design_map  (740 rows)
SELECT 
    pm.pro_id      AS product_id,
    p.product_name,
    pm.id_design   AS design_id,
    d.design_name
FROM ret_product_mapping pm
LEFT JOIN ret_product_master p ON pm.pro_id = p.pro_id
LEFT JOIN ret_design_master d ON pm.id_design = d.design_no;


-- 9. sub_design_map  (1411 rows)
SELECT 
    sdm.id_product     AS product_id,
    p.product_name,
    sdm.id_design      AS design_id,
    d.design_name,
    sdm.id_sub_design  AS sub_design_id,
    sd.sub_design_name
FROM ret_sub_design_mapping sdm
LEFT JOIN ret_product_master p ON sdm.id_product = p.pro_id
LEFT JOIN ret_design_master d ON sdm.id_design = d.design_no
LEFT JOIN ret_sub_design_master sd ON sdm.id_sub_design = sd.id_sub_design;


-- 10. section  (91 rows)
SELECT 
    id_section,
    section_name,
    section_short_code AS short_code,
    status,
    is_home_bill_counter
FROM ret_section
WHERE status = 1;


-- 11. product_section_map  (21205 rows)
SELECT 
    ps.pro_id      AS product_id,
    p.product_name,
    ps.id_section  AS section_id,
    s.section_name
FROM ret_product_section ps
LEFT JOIN ret_product_master p ON ps.pro_id = p.pro_id
LEFT JOIN ret_section s ON ps.id_section = s.id_section;


-- 12. size  (277 rows)
SELECT 
    sz.id_product  AS product_id,
    p.product_name,
    sz.value       AS size_value,
    sz.name        AS size_uom,
    sz.active      AS status
FROM ret_size sz
LEFT JOIN ret_product_master p ON sz.id_product = p.pro_id;


-- 13. stone_details  (76 rows)
SELECT 
    s.stone_name,
    s.stone_code       AS short_code,
    st.stone_type,
    s.stone_type       AS stone_type_id,
    u.uom_name         AS uom,
    s.uom_id,
    s.is_certificate_req AS certificate_req,
    s.is_4c_req        AS `4c_req`
FROM ret_stone s
LEFT JOIN ret_stone_type st ON s.stone_type = st.id_stone_type
LEFT JOIN ret_uom u ON s.uom_id = u.uom_id
WHERE s.stone_status = 1;


-- 14. charges
SELECT 
    name_charge        AS charges_name,
    code_charge        AS short_code,
    value_charge       AS amount,
    charge_tax         AS charge_tax_percent,
    tag_display
FROM ret_charges;


-- 15. tag_details  (LIMIT 200 for testing)
SELECT 
    t.cat_type         AS metal_id,
    m.metal            AS metal_name,
    c.id_ret_category  AS category_id,
    c.name             AS category_name,
    t.product_id,
    p.product_name,
    t.design_id,
    d.design_name,
    t.id_sub_design    AS sub_design_id,
    sd.sub_design_name,
    t.id_section       AS section_id,
    s.section_name,
    t.piece            AS pieces,
    t.gross_wt,
    t.less_wt,
    t.net_wt,
    t.retail_max_wastage_percent AS wastage_percent,
    t.tag_mc_type      AS mc_type,
    t.tag_mc_value     AS mc_value,
    t.hu_id            AS huid1,
    t.hu_id2           AS huid2,
    t.tag_id           AS tag_number
FROM ret_taging t
LEFT JOIN ret_product_master p ON t.product_id = p.pro_id
LEFT JOIN ret_category c ON p.cat_id = c.id_ret_category
LEFT JOIN metal m ON t.cat_type = m.id_metal
LEFT JOIN ret_design_master d ON t.design_id = d.design_no
LEFT JOIN ret_sub_design_master sd ON t.id_sub_design = sd.id_sub_design
LEFT JOIN ret_section s ON t.id_section = s.id_section
WHERE t.tag_status IN (0, 1)
LIMIT 200;


-- 16. tag_stone_details
SELECT 
    ts.tag_id          AS tag_number,
    ts.is_apply_in_lwt AS is_less_wt,
    sm.stone_name,
    ts.stone_id,
    sm.stone_code      AS short_code,
    u.uom_name         AS uom,
    ts.uom_id,
    ts.pieces,
    ts.wt              AS weight,
    ts.rate_per_gram,
    ts.amount,
    ts.stone_cal_type  AS stone_calc_type,
    qc.code            AS quality_code,
    ts.stone_quality_id AS quality_code_id,
    ts.certification_cost,
    ts.pur_rate         AS purchase_rate,
    ts.pur_cost         AS purchase_cost
FROM ret_taging_stone ts
LEFT JOIN ret_stone sm ON ts.stone_id = sm.stone_id
LEFT JOIN ret_uom u ON ts.uom_id = u.uom_id
LEFT JOIN ret_quality_code qc ON ts.stone_quality_id = qc.quality_id
WHERE ts.tag_id IN (
    SELECT tag_id FROM (
        SELECT tag_id FROM ret_taging WHERE tag_status IN (0, 1) LIMIT 200
    ) tmp
);


-- 17. tag_charges
SELECT 
    tc.tag_id          AS tag_number,
    ch.name_charge     AS charges_name,
    tc.charge_id       AS charges_id,
    tc.charge_value    AS amount
FROM ret_taging_charges tc
LEFT JOIN ret_charges ch ON tc.charge_id = ch.id_charge
WHERE tc.tag_id IN (
    SELECT tag_id FROM (
        SELECT tag_id FROM ret_taging WHERE tag_status IN (0, 1) LIMIT 200
    ) tmp
);
