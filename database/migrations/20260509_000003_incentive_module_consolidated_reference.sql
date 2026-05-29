-- ============================================================
-- CONSOLIDATED REFERENCE: Employee Sales Incentive Module
-- Full schema for all 4 incentive tables as of 09-05-2026.
-- This file is NOT meant to run on its own — it is a reference
-- that shows the final table state after all incremental
-- migrations have been applied.
--
-- Incremental migration history:
--   20260411_000001  CREATE ret_emp_incentive_config
--   20260411_000002  CREATE ret_emp_incentive_ranges
--   20260411_000003  CREATE ret_emp_incentive_log
--   20260411_000004  Menu: Emp Sales Incentive
--   20260411_000005  ALTER log: is_paid, paid_date, paid_by
--   20260411_000006  Menu: Emp Incentive Report
--   20260417_000001  ALTER config: multi-select VARCHAR(500)
--   20260417_000002  Menu relocation (template)
--   20260417_000003  CREATE ret_emp_incentive_payment_log
--   20260417_000004  ALTER log: paid_amount, remarks
--   20260417_000005  ALTER config: stone_type
--   20260418_000005  ALTER config: id_metal
--   20260509_000001  ALTER config: parent_id, deleted_on/by
--   20260509_000002  ADD log indexes (unique + covering)
-- ============================================================

-- ############################################################
-- TABLE 1: ret_emp_incentive_config
-- Master configuration rules for employee sales incentive.
-- ############################################################
/*
CREATE TABLE `ret_emp_incentive_config` (
    `id`                  INT(11)        NOT NULL AUTO_INCREMENT,
    `parent_id`           INT(11)        DEFAULT NULL  COMMENT 'Previous version config ID (version-based edit)',
    `id_branch`           VARCHAR(500)   DEFAULT NULL  COMMENT 'CSV branch IDs. NULL = All',
    `id_metal`            VARCHAR(500)   DEFAULT NULL  COMMENT 'CSV metal IDs. NULL = All',
    `id_category`         VARCHAR(500)   DEFAULT NULL  COMMENT 'CSV category IDs. NULL = All',
    `id_product`          VARCHAR(500)   DEFAULT NULL  COMMENT 'CSV product IDs. NULL = All',
    `id_design`           VARCHAR(500)   DEFAULT NULL  COMMENT 'CSV design IDs. NULL = All',
    `id_sub_design`       VARCHAR(500)   DEFAULT NULL  COMMENT 'CSV sub-design IDs. NULL = All',
    `id_stock_age_master` INT(11)        DEFAULT NULL  COMMENT 'FK ret_stock_age_master.id (Age Based)',
    `calc_basis`          TINYINT(1)     NOT NULL DEFAULT 1 COMMENT '1=Per Gram, 2=Per Carat, 3=% of Sales Value, 4=Age Based',
    `rate_type`           TINYINT(1)     NOT NULL DEFAULT 1 COMMENT '1=Fixed, 2=Weight Range, 3=Carat Range, 4=Age Based',
    `stone_type`          INT(11)        DEFAULT NULL  COMMENT 'Stone type ID (rate_type=3)',
    `incentive_value`     DECIMAL(12,4)  NOT NULL DEFAULT 0.0000 COMMENT 'Rate when rate_type=1 (Fixed)',
    `status`              TINYINT(1)     NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive, 2=Deleted/Versioned',
    `created_by`          INT(11)        NOT NULL,
    `created_on`          DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_by`          INT(11)        DEFAULT NULL,
    `updated_on`          DATETIME       DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_on`          DATETIME       DEFAULT NULL,
    `deleted_by`          INT(11)        DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_parent_id`   (`parent_id`),
    KEY `idx_branch`      (`id_branch`(191)),
    KEY `idx_category`    (`id_category`(191)),
    KEY `idx_product`     (`id_product`(191)),
    KEY `idx_design`      (`id_design`(191)),
    KEY `idx_sub_design`  (`id_sub_design`(191)),
    KEY `idx_status`      (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
*/

-- ############################################################
-- TABLE 2: ret_emp_incentive_ranges
-- Range slabs for Weight Range (rate_type=2) / Carat Range (3)
-- ############################################################
/*
CREATE TABLE `ret_emp_incentive_ranges` (
    `id`              INT(11)        NOT NULL AUTO_INCREMENT,
    `config_id`       INT(11)        NOT NULL COMMENT 'FK ret_emp_incentive_config.id',
    `range_from`      DECIMAL(12,4)  NOT NULL DEFAULT 0.0000,
    `range_to`        DECIMAL(12,4)  NOT NULL DEFAULT 0.0000,
    `incentive_value` DECIMAL(12,4)  NOT NULL DEFAULT 0.0000,
    PRIMARY KEY (`id`),
    KEY `idx_config` (`config_id`),
    CONSTRAINT `fk_incentive_range_config` FOREIGN KEY (`config_id`)
        REFERENCES `ret_emp_incentive_config` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
*/

-- ############################################################
-- TABLE 3: ret_emp_incentive_log
-- Per-item incentive log (freeze-at-save + payment tracking)
-- ############################################################
/*
CREATE TABLE `ret_emp_incentive_log` (
    `id`               INT(11)        NOT NULL AUTO_INCREMENT,
    `bill_id`          INT(11)        NOT NULL COMMENT 'FK ret_billing.bill_id',
    `bill_det_id`      INT(11)        NOT NULL COMMENT 'FK ret_bill_details.bill_det_id',
    `id_employee`      INT(11)        NOT NULL COMMENT 'Selling employee',
    `config_id`        INT(11)        DEFAULT NULL COMMENT 'FK ret_emp_incentive_config.id',
    `id_branch`        INT(11)        NOT NULL,
    `id_category`      INT(11)        DEFAULT NULL,
    `id_product`       INT(11)        DEFAULT NULL,
    `id_design`        INT(11)        DEFAULT NULL,
    `id_sub_design`    INT(11)        DEFAULT NULL,
    `calc_basis`       TINYINT(1)     NOT NULL COMMENT '1=Per Gram, 2=Per Carat, 3=%',
    `base_qty`         DECIMAL(12,4)  NOT NULL DEFAULT 0.0000,
    `incentive_rate`   DECIMAL(12,4)  NOT NULL DEFAULT 0.0000,
    `incentive_amount` DECIMAL(12,4)  NOT NULL DEFAULT 0.0000,
    `paid_amount`      DECIMAL(12,4)  NOT NULL DEFAULT 0.0000,
    `bill_date`        DATE           NOT NULL,
    `created_on`       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `is_paid`          TINYINT(1)     NOT NULL DEFAULT 0 COMMENT '0=Pending, 1=Given',
    `paid_date`        DATETIME       DEFAULT NULL,
    `paid_by`          INT(11)        DEFAULT NULL,
    `remarks`          VARCHAR(255)   DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_bill_item_emp`    (`bill_id`, `bill_det_id`, `id_employee`),
    KEY `idx_bill`                   (`bill_id`),
    KEY `idx_bill_det`               (`bill_det_id`),
    KEY `idx_employee`               (`id_employee`),
    KEY `idx_config`                 (`config_id`),
    KEY `idx_branch`                 (`id_branch`),
    KEY `idx_bill_date`              (`bill_date`),
    KEY `idx_date_branch_paid`       (`bill_date`, `id_branch`, `is_paid`),
    KEY `idx_report_filter`          (`id_branch`, `id_employee`, `bill_date`, `is_paid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
*/

-- ############################################################
-- TABLE 4: ret_emp_incentive_payment_log
-- Summary-level partial/full payment records per employee
-- ############################################################
/*
CREATE TABLE `ret_emp_incentive_payment_log` (
    `id`              INT(11)        NOT NULL AUTO_INCREMENT,
    `id_employee`     INT(11)        NOT NULL,
    `id_branch`       INT(11)        DEFAULT NULL,
    `date_from`       DATE           NOT NULL COMMENT 'Report period start',
    `date_to`         DATE           NOT NULL COMMENT 'Report period end',
    `original_amount` DECIMAL(15,2)  NOT NULL DEFAULT 0.00,
    `given_amount`    DECIMAL(15,2)  NOT NULL DEFAULT 0.00,
    `remarks`         VARCHAR(500)   DEFAULT NULL,
    `created_by`      INT(11)        NOT NULL,
    `created_at`      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status`          TINYINT(1)     NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `idx_emp_date` (`id_employee`, `date_from`, `date_to`),
    KEY `idx_branch`   (`id_branch`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
*/

-- ############################################################
-- KEY REPORT QUERIES (Reference Only — used in PHP model)
-- ############################################################

-- ----- QUERY 1: Sales Incentive Report (Live Calculation) -----
-- Source: Ret_emp_incentive_model::get_incentive_report()
-- Flow:  ret_billing → ret_estimation → ret_estimation_items
--        → match against active config → calculate incentive
/*
SELECT
    b.bill_id, b.bill_date, b.sales_ref_no, b.id_branch,
    b.id_employee AS bill_employee,
    br.name AS branch_name,
    ei.est_item_id, ei.esti_id, ei.item_emp_id,
    ei.product_id, ei.design_id, ei.id_sub_design,
    ei.net_wt, ei.gross_wt, ei.item_cost,
    pm.cat_id AS id_category,
    IFNULL(cat.name, '') AS category_name,
    IFNULL(pm.product_name, '') AS product_name,
    IFNULL(dm.design_name, '') AS design_name,
    IFNULL(sd.sub_design_name, '') AS sub_design_name,
    bd.tag_id,
    IFNULL(DATEDIFF(DATE(b.bill_date), DATE(tag.tag_datetime)), 0) AS item_age
FROM ret_billing b
JOIN ret_estimation e ON e.estbillid = b.bill_id
JOIN ret_estimation_items ei ON ei.esti_id = e.estimation_id
LEFT JOIN ret_bill_details bd
    ON bd.bill_id = b.bill_id AND bd.esti_item_id = ei.est_item_id
LEFT JOIN ret_taging tag ON tag.tag_id = bd.tag_id
LEFT JOIN branch br ON br.id_branch = b.id_branch
LEFT JOIN ret_product_master pm ON pm.pro_id = ei.product_id
LEFT JOIN ret_category cat ON cat.id_ret_category = pm.cat_id
LEFT JOIN ret_design_master dm ON dm.design_no = ei.design_id
LEFT JOIN ret_sub_design_master sd ON sd.id_sub_design = ei.id_sub_design
WHERE b.bill_status = 1
  AND b.bill_type = 1
  AND DATE(b.bill_date) >= :date_from
  AND DATE(b.bill_date) <= :date_to
  AND b.id_branch = :id_branch
ORDER BY b.bill_date DESC, b.bill_id, ei.est_item_id;
*/

-- ----- QUERY 2: Rule Matching Engine -----
-- Source: Ret_emp_incentive_model::find_matching_rule()
-- Specificity: sub_design(16) + design(8) + product(4) + category(2) + branch(1)
/*
SELECT c.*,
    (
        IF(c.id_sub_design IS NOT NULL AND FIND_IN_SET(:sub_design_id, c.id_sub_design), 16, 0) +
        IF(c.id_design IS NOT NULL AND FIND_IN_SET(:design_id, c.id_design), 8, 0) +
        IF(c.id_product IS NOT NULL AND FIND_IN_SET(:product_id, c.id_product), 4, 0) +
        IF(c.id_category IS NOT NULL AND FIND_IN_SET(:category_id, c.id_category), 2, 0) +
        IF(c.id_branch IS NOT NULL AND FIND_IN_SET(:branch_id, c.id_branch), 1, 0)
    ) AS specificity
FROM ret_emp_incentive_config c
WHERE c.status = 1
  AND (c.id_branch IS NULL OR FIND_IN_SET(:branch_id, c.id_branch))
  AND (c.id_category IS NULL OR FIND_IN_SET(:category_id, c.id_category))
  AND (c.id_product IS NULL OR FIND_IN_SET(:product_id, c.id_product))
  AND (c.id_design IS NULL OR FIND_IN_SET(:design_id, c.id_design))
  AND (c.id_sub_design IS NULL OR FIND_IN_SET(:sub_design_id, c.id_sub_design))
ORDER BY specificity DESC
LIMIT 1;
*/

-- ----- QUERY 3: Log-Based Incentive Report (Frozen Data) -----
-- Source: Ret_emp_incentive_model::get_incentive_report_from_log()
/*
SELECT
    log.id AS log_id,
    log.bill_id, log.bill_det_id, log.id_employee,
    log.config_id, log.id_branch, log.id_category,
    log.id_product, log.id_design, log.id_sub_design,
    log.calc_basis, log.base_qty, log.incentive_rate,
    log.incentive_amount, log.paid_amount,
    log.bill_date, log.is_paid,
    b.sales_ref_no,
    CONCAT(emp.firstname, ' ', emp.lastname) AS emp_name,
    emp.emp_code,
    IFNULL(br.name, '') AS branch_name,
    IFNULL(cat.name, '') AS category_name,
    IFNULL(pm.product_name, '') AS product_name,
    IFNULL(dm.design_name, '') AS design_name,
    IFNULL(sd.sub_design_name, '') AS sub_design_name,
    bd.gross_wt, bd.net_wt, bd.item_cost, bd.tag_id,
    IFNULL(DATEDIFF(DATE(b.bill_date), DATE(tag.tag_datetime)), 0) AS item_age,
    IFNULL((
        SELECT SUM(bis.wt)
        FROM ret_billing_item_stones bis
        WHERE bis.bill_det_id = log.bill_det_id
    ), 0) AS stone_carat_wt
FROM ret_emp_incentive_log log
JOIN ret_billing b ON b.bill_id = log.bill_id
JOIN employee emp ON emp.id_employee = log.id_employee
LEFT JOIN ret_bill_details bd ON bd.bill_det_id = log.bill_det_id
LEFT JOIN ret_taging tag ON tag.tag_id = bd.tag_id
LEFT JOIN branch br ON br.id_branch = log.id_branch
LEFT JOIN ret_product_master pm ON pm.pro_id = log.id_product
LEFT JOIN ret_category cat
    ON cat.id_ret_category = IFNULL(log.id_category, pm.cat_id)
LEFT JOIN ret_design_master dm ON dm.design_no = log.id_design
LEFT JOIN ret_sub_design_master sd ON sd.id_sub_design = log.id_sub_design
WHERE b.bill_status = 1
  AND b.bill_type = 1
  AND log.bill_date >= :date_from
  AND log.bill_date <= :date_to
  AND log.id_branch = :id_branch
  AND log.id_employee = :id_employee
  AND log.is_paid = :pay_status
ORDER BY log.bill_date DESC, log.bill_id, log.bill_det_id;
*/

-- ----- QUERY 4: Return Incentive (Log-Based) -----
-- Source: Ret_emp_incentive_model::get_return_incentive_report_from_log()
/*
SELECT
    brt.bill_id AS return_bill_id,
    brt.bill_date AS return_date,
    brt.sales_ref_no AS return_ref_no,
    log.bill_id AS orig_bill_id,
    log.bill_det_id AS est_item_id,
    log.id_employee, log.id_branch, log.calc_basis,
    log.base_qty, log.incentive_rate,
    log.incentive_amount, log.config_id,
    CONCAT(emp.firstname, ' ', emp.lastname) AS emp_name,
    emp.emp_code,
    IFNULL(br.name, '') AS branch_name,
    IFNULL(cat.name, '') AS category_name,
    IFNULL(pm.product_name, '') AS product_name,
    IFNULL(dm.design_name, '') AS design_name,
    IFNULL(sd.sub_design_name, '') AS sub_design_name,
    bd.gross_wt, bd.net_wt, bd.item_cost,
    IFNULL(DATEDIFF(DATE(log.bill_date), DATE(tag.tag_datetime)), 0) AS item_age,
    IFNULL((
        SELECT SUM(bis.wt)
        FROM ret_billing_item_stones bis
        WHERE bis.bill_det_id = log.bill_det_id
    ), 0) AS stone_carat_wt
FROM ret_bill_return_details r
JOIN ret_billing brt ON brt.bill_id = r.bill_id
JOIN ret_emp_incentive_log log ON log.bill_det_id = r.ret_bill_det_id
JOIN employee emp ON emp.id_employee = log.id_employee
LEFT JOIN ret_bill_details bd ON bd.bill_det_id = log.bill_det_id
LEFT JOIN ret_taging tag ON tag.tag_id = bd.tag_id
LEFT JOIN branch br ON br.id_branch = log.id_branch
LEFT JOIN ret_product_master pm ON pm.pro_id = log.id_product
LEFT JOIN ret_category cat
    ON cat.id_ret_category = IFNULL(log.id_category, pm.cat_id)
LEFT JOIN ret_design_master dm ON dm.design_no = log.id_design
LEFT JOIN ret_sub_design_master sd ON sd.id_sub_design = log.id_sub_design
WHERE brt.bill_status = 1
  AND brt.bill_type = 7
  AND DATE(brt.bill_date) >= :date_from
  AND DATE(brt.bill_date) <= :date_to
  AND log.id_branch = :id_branch
  AND log.id_employee = :id_employee
ORDER BY brt.bill_date DESC, brt.bill_id;
*/

-- ----- QUERY 5: Return Incentive (Live Calculation) -----
-- Source: Ret_emp_incentive_model::get_return_incentive_report()
/*
SELECT
    brt.bill_id AS return_bill_id,
    brt.bill_date AS return_date,
    brt.sales_ref_no AS return_ref_no,
    b.bill_id AS orig_bill_id,
    b.id_branch,
    b.id_employee AS bill_employee,
    br.name AS branch_name,
    ei.est_item_id, ei.esti_id, ei.item_emp_id,
    ei.product_id, ei.design_id, ei.id_sub_design,
    d.net_wt, d.gross_wt, d.item_cost, d.tag_id,
    IFNULL(DATEDIFF(DATE(b.bill_date), DATE(tag.tag_datetime)), 0) AS item_age,
    pm.cat_id AS id_category,
    IFNULL(cat.name, '') AS category_name,
    IFNULL(pm.product_name, '') AS product_name,
    IFNULL(dm.design_name, '') AS design_name,
    IFNULL(sd.sub_design_name, '') AS sub_design_name
FROM ret_bill_return_details r
JOIN ret_bill_details d ON d.bill_det_id = r.ret_bill_det_id
JOIN ret_billing b ON b.bill_id = d.bill_id
JOIN ret_billing brt ON brt.bill_id = r.bill_id
LEFT JOIN ret_taging tag ON tag.tag_id = d.tag_id
LEFT JOIN ret_estimation_items ei ON ei.est_item_id = d.esti_item_id
LEFT JOIN branch br ON br.id_branch = b.id_branch
LEFT JOIN ret_product_master pm ON pm.pro_id = d.product_id
LEFT JOIN ret_category cat ON cat.id_ret_category = pm.cat_id
LEFT JOIN ret_design_master dm ON dm.design_no = d.design_id
LEFT JOIN ret_sub_design_master sd
    ON sd.id_sub_design = IFNULL(ei.id_sub_design, 0)
WHERE b.bill_status = 1
  AND brt.bill_status = 1
  AND DATE(brt.bill_date) >= :date_from
  AND DATE(brt.bill_date) <= :date_to
  AND b.id_branch = :id_branch
ORDER BY brt.bill_date DESC, brt.bill_id;
*/

-- ----- QUERY 6: Paid/Unpaid Log Map -----
-- Source: Ret_emp_incentive_model::get_paid_log_map()
/*
SELECT id, bill_id, bill_det_id, id_employee,
       is_paid, IFNULL(paid_amount, 0) AS paid_amount
FROM ret_emp_incentive_log
WHERE bill_date >= :date_from
  AND bill_date <= :date_to
  AND id_branch = :id_branch;
*/

-- ----- QUERY 7: Payment Log Retrieval -----
-- Source: Ret_emp_incentive_model::get_payment_logs()
/*
SELECT pl.*, CONCAT(e.firstname, ' ', e.lastname) AS created_by_name
FROM ret_emp_incentive_payment_log pl
LEFT JOIN employee e ON e.id_employee = pl.created_by
WHERE pl.id_employee = :id_employee
  AND pl.status = 1
  AND pl.date_from <= :date_to
  AND pl.date_to >= :date_from
  AND pl.id_branch = :id_branch
ORDER BY pl.created_at DESC;
*/

-- ----- QUERY 8: Total Given Amount -----
-- Source: Ret_emp_incentive_model::get_total_given()
/*
SELECT IFNULL(SUM(given_amount), 0) AS total_given
FROM ret_emp_incentive_payment_log
WHERE id_employee = :id_employee
  AND status = 1
  AND date_from <= :date_to
  AND date_to >= :date_from
  AND id_branch = :id_branch;
*/
