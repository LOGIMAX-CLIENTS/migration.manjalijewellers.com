-- ============================================
-- Truncate retail tables for re-import
-- Order: child/mapping tables first, then masters
-- Run on: konika_prod (destination DB)
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

-- Tag detail tables (children of tags)
TRUNCATE TABLE ret_import_tag_details;
TRUNCATE TABLE ret_import_stone_details;
TRUNCATE TABLE ret_import_charges;

-- Mapping tables (depend on masters)
TRUNCATE TABLE ret_product_mapping;
TRUNCATE TABLE ret_sub_design_mapping;
TRUNCATE TABLE ret_product_section;
TRUNCATE TABLE ret_metal_cat_purity;

-- Size (depends on product)
TRUNCATE TABLE ret_size;

-- Masters
TRUNCATE TABLE ret_product_master;
TRUNCATE TABLE ret_design_master;
TRUNCATE TABLE ret_sub_design_master;
TRUNCATE TABLE ret_section;
TRUNCATE TABLE ret_stone;
TRUNCATE TABLE ret_charges;
TRUNCATE TABLE ret_category;
TRUNCATE TABLE ret_purity;
TRUNCATE TABLE ret_karigar;

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'All retail tables truncated. Ready for re-import.' AS status;
