-- =====================================================
-- Improved Temp Tables for Port Max Import
-- Proper data types (validated by import tool before insert)
-- =====================================================

-- 1. TAG DETAILS
-- =====================================================
DROP TABLE IF EXISTS ret_import_tag_details;

CREATE TABLE ret_import_tag_details (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Old IDs (from source system)
    old_metal_id INT NULL,
    old_category_id INT NULL,
    old_product_id INT NULL,
    old_design_id INT NULL,
    old_sub_design_id INT NULL,
    old_purity_id INT NULL,
    old_branch_id INT NULL,
    old_karigar_id INT NULL,
    old_counter_id INT NULL,
    old_size_id INT NULL,

    -- Resolved IDs (filled by API)
    metal_id INT NULL,
    category_id INT NULL,
    product_id INT NULL,
    design_id INT NULL,
    sub_design_id INT NULL,
    purity_id INT NULL,
    branch_id INT NULL,
    karigar_id INT NULL,
    counter_id INT NULL,
    size_id INT NULL,

    -- Name columns (for lookup matching)
    metal_name VARCHAR(255) NULL,
    category_name VARCHAR(255) NULL,
    product_name VARCHAR(255) NULL,
    design_name VARCHAR(255) NULL,
    sub_design_name VARCHAR(255) NULL,
    purity_name VARCHAR(255) NULL,
    branch_name VARCHAR(255) NULL,
    karigar_name VARCHAR(255) NULL,
    counter_name VARCHAR(255) NULL,
    size_value VARCHAR(255) NULL,
    size_name VARCHAR(255) NULL,

    -- Numeric fields (properly typed)
    pieces INT NOT NULL DEFAULT 1,
    gross_wt DECIMAL(12,3) NOT NULL DEFAULT 0.000,
    net_wt DECIMAL(12,3) NULL DEFAULT 0.000,
    less_wt DECIMAL(12,3) NULL DEFAULT 0.000,

    sales_type TINYINT NULL,
    prod_calc_based_on TINYINT NULL,
    tag_calc_based_on TINYINT NULL,

    wastage_per DECIMAL(8,3) NULL DEFAULT 0.000,
    wastage_wt DECIMAL(12,3) NULL DEFAULT 0.000,

    mc_type TINYINT NULL DEFAULT 0,
    mc_value DECIMAL(12,2) NULL DEFAULT 0.00,

    sales_value DECIMAL(12,2) NULL DEFAULT 0.00,
    rate_per_gram DECIMAL(12,2) NULL DEFAULT 0.00,
    sell_rate DECIMAL(12,2) NULL DEFAULT 0.00,

    -- String fields
    mfr_code VARCHAR(255) NULL,
    style_code VARCHAR(255) NULL,
    tag_number VARCHAR(255) NULL,
    certification_no VARCHAR(255) NULL,
    purchase_cost DECIMAL(12,2) NULL DEFAULT 0.00,

    tag_date VARCHAR(50) NULL,

    huid1 VARCHAR(255) NULL,
    huid2 VARCHAR(255) NULL,

    lot_wastage_percentage DECIMAL(8,3) NULL,
    lot_making_charge DECIMAL(12,2) NULL,

    -- Status tracking
    updatestatus TINYINT DEFAULT 0 COMMENT '0=Pending,1=Imported,5=Failed',
    error_code TINYINT NULL,
    error_message TEXT NULL,
    created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_on DATETIME NULL,

    INDEX idx_product (product_id),
    INDEX idx_design (design_id),
    INDEX idx_sub_design (sub_design_id),
    INDEX idx_category (category_id),
    INDEX idx_purity (purity_id),
    INDEX idx_branch (branch_id),
    INDEX idx_karigar (karigar_id),
    INDEX idx_counter (counter_id),
    INDEX idx_size (size_id),
    INDEX idx_status (updatestatus),
    INDEX idx_cat_name (category_name),
    INDEX idx_pro_name (product_name),
    INDEX idx_purity_name (purity_name)
);


-- 2. STONE DETAILS
-- =====================================================
DROP TABLE IF EXISTS ret_import_stone_details;

CREATE TABLE ret_import_stone_details (
    id INT AUTO_INCREMENT PRIMARY KEY,

    old_tag_id INT NULL,
    tag_id INT NULL,
    tag_number VARCHAR(255) NULL,
    is_less_id TINYINT NULL DEFAULT 0,

    old_stone_type_id INT NULL,
    stone_type_id INT NULL,
    stone_type_name VARCHAR(255) NULL,

    old_stone_id INT NULL,
    stone_id INT NULL,
    stone_name VARCHAR(255) NULL,

    old_uom_id INT NULL,
    uom_id INT NULL,
    uom_name VARCHAR(255) NULL,

    -- Numeric fields (properly typed)
    pieces INT NULL DEFAULT 0,
    stone_wt DECIMAL(12,3) NULL DEFAULT 0.000,
    stone_calc_type TINYINT NULL,
    rate_per_gram DECIMAL(12,3) NULL DEFAULT 0.000,
    amount DECIMAL(12,3) NULL DEFAULT 0.000,

    -- Status tracking
    updatestatus TINYINT DEFAULT 0,
    created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_on DATETIME NULL,

    INDEX idx_tag (tag_id),
    INDEX idx_stone (stone_id),
    INDEX idx_stonetype (stone_type_id),
    INDEX idx_uom (uom_id),
    INDEX idx_status (updatestatus)
);


-- 3. CHARGE DETAILS
-- =====================================================
DROP TABLE IF EXISTS ret_import_charges;

CREATE TABLE ret_import_charges (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

    tag_number VARCHAR(50) NULL,
    charge_name VARCHAR(100) NULL,
    charge_value DECIMAL(12,3) NOT NULL DEFAULT 0.000,

    -- Resolved IDs (filled by API)
    tag_id BIGINT UNSIGNED NULL,
    charge_id INT NULL,

    -- Status tracking
    updatestatus TINYINT NOT NULL DEFAULT 0,
    error_code INT NULL,
    error_message VARCHAR(255) NULL,

    created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_on DATETIME NULL,

    INDEX idx_status (updatestatus),
    INDEX idx_tag_id (tag_id),
    INDEX idx_charge_id (charge_id),
    INDEX idx_tag_number (tag_number)
);
