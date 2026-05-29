-- ╔══════════════════════════════════════════════════════════════════════════════╗
-- ║  ⚠️  DEPRECATED — DO NOT ADD NEW QUERIES HERE                              ║
-- ║                                                                            ║
-- ║  This file is kept for historical reference only.                          ║
-- ║  Queries here may or may not have been executed on all environments.        ║
-- ║                                                                            ║
-- ║  NEW PROCESS (effective March 2026):                                       ║
-- ║  Place your .sql migration files in:  database/migrations/                 ║
-- ║  Naming format:  YYYYMMDD_HHMMSS_description.sql                          ║
-- ║  Example:  20260318_153000_add_insurance_columns.sql                       ║
-- ║                                                                            ║
-- ║  Migrations are auto-executed during deploy via run-migrations.sh          ║
-- ║  and tracked in the migration_history table (no duplicates).               ║
-- ║  See:  database/migrations/README.md  for full instructions.               ║
-- ╚══════════════════════════════════════════════════════════════════════════════╝

-- 26/05/2025 

ALTER TABLE `general` CHANGE `type` `type` INT NULL DEFAULT NULL COMMENT '1-T&c,2-FAQ, 3- aboutus,4-privacy policy\r\n';

ALTER TABLE `ret_old_metal_process` CHANGE `next_process_for` `next_process_for` INT(11) NULL DEFAULT '0' COMMENT '1-> Process 2->Stock';

ALTER TABLE `ret_old_metal_melting` CHANGE `rate` `rate` DECIMAL(10,2) NULL DEFAULT NULL;

ALTER TABLE `ret_old_metal_melting_recd_details` CHANGE `testing_completed_wt` `testing_completed_wt` DECIMAL(10,3) NULL DEFAULT NULL;

ALTER TABLE `ret_old_metal_melting_recd_details` CHANGE `tested_purity` `tested_purity` DECIMAL(10,3) NULL DEFAULT NULL;

ALTER TABLE `ret_old_metal_refining` CHANGE `issue_weight` `issue_weight` DECIMAL(10,3) NULL DEFAULT NULL;

ALTER TABLE `ret_old_metal_refining` CHANGE `id_melting` `id_melting` INT(11) NULL DEFAULT NULL;

ALTER TABLE `ret_old_metal_refining` CHANGE `id_metal_testing` `id_metal_testing` INT(11) NULL DEFAULT NULL;

-- 18/11/2025

ALTER TABLE `profile` ADD `allow_other_issue` TINYINT(5) NOT NULL DEFAULT '0' COMMENT '0 -> No 1-> Yes ' AFTER `metal_rate_datelimit`;

ALTER TABLE `profile` ADD `wedding_wastage_slab` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0 -> No, 1 -> Yes' AFTER `metalrate_edit`;

ALTER TABLE `ret_estimation_items` ADD `wast_slab_value` DECIMAL(10,2) NULL DEFAULT NULL AFTER `wastage_slab_id`;

ALTER TABLE `ret_estimation_items` ADD `tag_blk_disc` TINYINT(5) NOT NULL DEFAULT '0' COMMENT '0 -> Bulk disc is not applied\r\n1 -> Bulk disc applied.' AFTER `wastage_slab_id`;

ALTER TABLE `ret_estimation` ADD `bulk_was_disc_per` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Bulk tag wastage discount perc.' AFTER `disc_per`;

// Added is_split_row column:

ALTER TABLE `ret_estimation_items` ADD `is_split_row` INT(11) NOT NULL DEFAULT '0' COMMENT '0=> normal 1=> is_split_row' AFTER `isTagsplitted`;

// Added bulk_wast_disc_limit and dia_disc_limit column:

ALTER TABLE `employee_settings` ADD `bulk_wast_disc_limit` DECIMAL(10,2) NOT NULL DEFAULT '0.00' ;

ALTER TABLE `employee_settings` ADD `dia_disc_limit` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `bulk_wast_disc_limit`;

// Added wedding_wastage_slab column:

ALTER TABLE `profile` ADD `wedding_wastage_slab` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0 -> No, 1 -> Yes' AFTER `allow_mc_va`;

//Added manual rate value in ret_estimation table:

ALTER TABLE `ret_estimation` ADD `manual_rate` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '0 -> unchecked 1 -> checked' AFTER `bulk_was_disc_per`;

//Added ret_est_sales_return_utilization table:

CREATE TABLE `ret_est_sales_return_utilization` (
  `sr_ut_id` int(11) NOT NULL,
  `est_id` int(11) DEFAULT NULL,
  `bill_id` int(11) DEFAULT NULL,
  `bill_det_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `ret_est_sales_return_utilization`
  ADD PRIMARY KEY (`sr_ut_id`);
ALTER TABLE `ret_est_sales_return_utilization`
  MODIFY `sr_ut_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

//Date column added in ret_est_sales_return_utilization table:

ALTER TABLE `ret_est_sales_return_utilization` ADD `esti_date` DATE NULL DEFAULT NULL AFTER `bill_det_id`;

//Added stone amt and diamond amt column:

ALTER TABLE `ret_estimation_items` ADD `stone_amount` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `act_wast_per`;

ALTER TABLE `ret_estimation_items` ADD `diamond_amount` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `stone_amount`;

//Added act_wastage_per column:

ALTER TABLE `ret_estimation_items` ADD `act_wast_per` INT(5) NOT NULL DEFAULT '0' AFTER `tag_blk_disc`;


//stn disc per column added :

ALTER TABLE `ret_estimation` ADD `disc_per` DECIMAL(10,2) NOT NULL DEFAULT '0' COMMENT 'Stone discount percentage' AFTER `handling_charges`;

//stn amount column added:

ALTER TABLE `ret_estimation_item_stones` ADD `max_stn_amt` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Stone amount before stone discount applied.' AFTER `quality_id`;

//wastage_slab_id column is added:

ALTER TABLE `ret_estimation_items` ADD `wastage_slab_id` INT(11) NULL DEFAULT NULL AFTER `round_off_amount`;

//Adding comment and changing the default value for the status column:

ALTER TABLE `ret_stone_discount_master` CHANGE `status` `status` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '0 - Inactive, 1 -Active';

//Added blk_disc_per column:

ALTER TABLE `ret_estimation` ADD `bulk_was_disc_per` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Bulk tag wastage discount perc.' AFTER `disc_per`;

-- Added stone discount master table

CREATE TABLE `ret_stone_discount_master` (
  `id` int(11) NOT NULL,
  `id_stone_type` int(5) NOT NULL,
  `min_disc_per` decimal(5,2) DEFAULT NULL,
  `max_disc_per` decimal(5,2) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 - Inactive, 1 -Active',
  `created_by` varchar(15) NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `modified_by` varchar(15) DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
COMMIT;

-- Added wastage slab master table

CREATE TABLE `ret_wastage_discount_master` (
  `id_wastage` int(11) NOT NULL,
  `id_metal` int(5) NOT NULL,
  `from_range` decimal(5,2) DEFAULT NULL,
  `to_range` decimal(5,2) DEFAULT NULL,
  `value` decimal(10,2) DEFAULT NULL,
  `created_by` varchar(15) NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `modified_by` varchar(15) NOT NULL,
  `modified_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
COMMIT;

-- NAMBI MUTHU RAJA - 11/11/2025  
ALTER TABLE `ret_billing` ADD `irn_date` DATE DEFAULT NULL;

-- 18/11/2025

--Added a rate_type coulmn in scheme table in etail

ALTER TABLE `scheme` ADD `rate_type` INT(11) NOT NULL DEFAULT '1' COMMENT '1 - Board Rate, 2 - Avg rate per gram' AFTER `store_closing_balance`;

--Adding flag in the employee table for manager.

ALTER TABLE `employee` ADD `is_store_manager` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0 -> employee 1-> manager' AFTER `enable_chit_collection`;

-- To store actual MC
ALTER TABLE `ret_estimation_items` ADD `max_mc` INT(10) NULL DEFAULT NULL AFTER `wastage_percent`;

-- To store acual VA
ALTER TABLE `ret_estimation_items` ADD `max_VA` INT(10) NULL DEFAULT NULL AFTER `wastage_percent`;

-- To store acual VA Per
ALTER TABLE `ret_estimation_items` ADD `max_va_per` INT(10) NULL DEFAULT NULL AFTER `wastage_percent`;

-- NAMBI MUTHU RAJA - 19/12/2025
ALTER TABLE `branch` ADD `id_village` INT(11) NULL DEFAULT NULL AFTER `pincode`;

DROP VIEW IF EXISTS
    ret_view_supplier_approval_ledger;
CREATE VIEW ret_view_supplier_approval_ledger AS SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `srj`.`ret_purchase_order_items` `pitm`
                        LEFT JOIN `srj`.`ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `srj`.`ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `srj`.`ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `srj`.`ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `srj`.`metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `srj`.`ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` = 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 1
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    'PAYMENT' AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    2 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    `rf`.`rate_per_gram` AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    `srj`.`ret_supplier_rate_cut` `rf`
WHERE
    `rf`.`rate_cut_type` = 1 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    IF(
        `rf`.`weight` = 0,
        'Bill Conv(Amount)',
        'Bill Conv(A to P)'
    ) AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    2 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    IF(`rf`.`weight` = 0, '', '100') AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    IF(
        `rf`.`charges_amount` > 0,
        `rf`.`charges_amount`,
        ''
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `rf`.`id_metal` AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    `rf`.`rate_per_gram` AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    `srj`.`ret_supplier_rate_cut` `rf`
WHERE
    `rf`.`rate_cut_type` = 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `srj`.`ret_purchase_return_items` `pret`
                                LEFT JOIN `srj`.`ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `srj`.`ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `srj`.`ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `srj`.`ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `srj`.`ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `srj`.`ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `srj`.`ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `srj`.`metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `srj`.`ret_purchase_return_items` `pret`
                                LEFT JOIN `srj`.`ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `srj`.`ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `srj`.`ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `srj`.`ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `srj`.`ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `srj`.`ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `srj`.`ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `srj`.`metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `iss`.`met_issue_date` AS `trans_date`,
    `iss`.`met_issue_ref_id` AS `referenceno`,
    '/karigarmetalissue_acknowladgement/' AS `link`,
    `iitm`.`issue_met_parent_id` AS `print_id`,
    2 AS `trans_type`,
    `iitm`.`issue_met_parent_id` AS `trans_id`,
    IF(
        `cat`.`cat_type` = 2,
        0,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `gross_wt`,
    IF(
        `cat`.`cat_type` = 2,
        0,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `net_wt`,
    IFNULL(`iitm`.`issue_pcs`, 1) AS `no_of_pcs`,
    '100' AS `purchase_touch`,
    IF(
        `pr`.`stone_type` = 0,
        `iitm`.`issue_metal_pur_wt`,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `purewt`,
    `iss`.`met_issue_karid` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    `iitm`.`issue_cat_id` AS `catid`,
    `iitm`.`issu_met_pro_id` AS `proid`,
    2 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    IFNULL(`iss`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`iss`.`met_issue_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `srj`.`ret_karigar_metal_issue_details` `iitm`
                        LEFT JOIN `srj`.`ret_karigar_metal_issue` `iss`
                        ON
                            (
                                `iss`.`met_issue_id` = `iitm`.`issue_met_parent_id`
                            )
                        )
                    LEFT JOIN `srj`.`ret_karigar` `kr`
                    ON
                        (
                            `kr`.`id_karigar` = `iss`.`met_issue_karid`
                        )
                    )
                LEFT JOIN `srj`.`ret_category` `cat`
                ON
                    (
                        `cat`.`id_ret_category` = `iitm`.`issue_cat_id`
                    )
                )
            LEFT JOIN `srj`.`metal` `met`
            ON
                (`met`.`id_metal` = `cat`.`id_metal`)
            )
        LEFT JOIN `srj`.`ret_product_master` `pr`
        ON
            (
                `pr`.`pro_id` = `iitm`.`issu_met_pro_id`
            )
        )
    LEFT JOIN `srj`.`ret_uom` `uom`
    ON
        (`uom`.`uom_id` = `iitm`.`issue_uom_id`)
    )
WHERE
    `iss`.`metalissue_type` = 2 AND `iss`.`bill_status` = 1
GROUP BY
    `iitm`.`issue_met_id`,
    `iitm`.`issue_cat_id`,
    `iitm`.`issu_met_pro_id`,
    `iitm`.`issue_met_parent_id`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `link`,
    '' AS `print_id`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `srj`.`smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `link`,
    '' AS `print_id`,
    `pay`.`weight_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    IFNULL(`pay`.`weight`, 0) AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `srj`.`smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    '/credit_debit_acknolodgement/' AS `link`,
    `pay`.`crdrid` AS `print_id`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    `pay`.`weight` AS `gross_wt`,
    `pay`.`weight` AS `net_wt`,
    `pay`.`weight` AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `pay`.`weight` AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`naration`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    `srj`.`ret_crdr_note` `pay`
WHERE
    `pay`.`accountto` = 3 AND `pay`.`crdr_status` = 1 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`crdrid`
ORDER BY
    `unixtransdate`

-- DEVADHARSHINI - 12/01/26
    --- Coswan Bank ledger Table -- 
CREATE TABLE `ledger_master` (
  `id_ledger` int(11) NOT NULL AUTO_INCREMENT,
  `ledger_name` varchar(255) NOT NULL,
  `status` int(11) DEFAULT '1',
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_ledger`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--- Coswan Bank ledger mapping Table --
CREATE TABLE `ledger_mapping` (
  `id_mapping` int(11) NOT NULL AUTO_INCREMENT,
  `id_ledger` int(11) NOT NULL,
  `type` enum('BANK','PAYMODE') NOT NULL,
  `reference_id` int(11) NOT NULL,
  `opening_balance` decimal(15,2) DEFAULT '0.00',
  PRIMARY KEY (`id_mapping`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--- ALTER TABLE `ledger_master` 
ALTER TABLE `ledger_master` 
ADD COLUMN `opening_balance` DECIMAL(15,2) DEFAULT 0.00 
AFTER `ledger_name`;

ALTER TABLE ledger_master 
ADD COLUMN opening_date DATE NULL AFTER opening_balance;

--- Bank ledger transfer Table --
CREATE TABLE `ret_ledger_transfer` (
  `transfer_id` int(11) NOT NULL AUTO_INCREMENT,
  `from_ledger_id` int(11) NOT NULL,
  `to_ledger_id` int(11) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT '0.00',
  `narration` text DEFAULT NULL,
  `transfer_type` int(11) DEFAULT '1' COMMENT '1 for Transfer, 2 for Manual',
  `transaction_type` int(11) DEFAULT NULL COMMENT '1 - Credit, 2 - Debit',
  `created_by` int(11) DEFAULT NULL,
  `transfer_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`transfer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- rudra 22-01-2026 supplier ledger


DROP VIEW IF EXISTS `ret_view_supplier_ledger`;

CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `ret_view_supplier_ledger`  AS 
SELECT
    IFNULL(
        `pr`.`product_name`,
        'Product Unavailable'
    ) AS `category`,
    `po`.`po_id` AS `po_id`,
    `met`.`id_metal` AS `id_metal`,
    IFNULL(`pr`.`product_name`, '') AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `retaillogimaxind_test_etail_v3`.`ret_purchase_order_items` `pitm`
                        LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `retaillogimaxind_test_etail_v3`.`metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    'PAYMENT' AS `category`,
    IFNULL(`bill`.`po_id`, '') AS `po_id`,
    `cat`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `pay`.`pay_create_on` AS `trans_date`,
    `pay`.`pay_refno` AS `referenceno`,
    2 AS `trans_type`,
    `pay`.`pay_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`pay_sup_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pd`.`payment_amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`pay`.`pay_create_on`) AS `unixtransdate`
FROM
    (
        (
            `retaillogimaxind_test_etail_v3`.`ret_po_payment` `pay`
        LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_po_payment_detail` `pd`
        ON
            (`pd`.`pay_id` = `pay`.`pay_id`)
        )
    LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_po_bill_payment_details` `bill`
    ON
        (`bill`.`pay_id` = `pay`.`pay_id`)
    LEFT JOIN `ret_purchase_order_items` `po`
    ON 
        (`bill`.`po_id` = `po`.`po_item_po_id`)
    LEFT JOIN `ret_category` `cat`
    ON
        (`cat`.`id_ret_category` = `po`.`po_item_cat_id`)
    )
WHERE
    `pay`.`pay_status` = 1 AND `pay`.`bill_type` = 1
GROUP BY
    `pay`.`pay_id`
UNION ALL
SELECT
    CONCAT(
        'RATE FIXING',
        IF(
            `pitm`.`rate` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    IFNULL(`po`.`po_id`, '') AS `po_id`,
    `pitm`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    IF(
        `pitm`.`rate` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    '' AS `gross_wt`,
    '' AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    '' AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (`pitm`.`rate` - `rf`.`rate_fix_rate`) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        (
            (
                `retaillogimaxind_test_etail_v3`.`ret_po_rate_fix` `rf`
            LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_purchase_order` `po`
            ON
                (
                    `po`.`po_id` = `rf`.`rate_fix_po_item_id`
                )
            )
        LEFT JOIN(
            SELECT
                `pitm`.`po_item_po_id` AS `poid`,
                `pitm`.`fix_rate_per_grm` AS `rate`,
                `cate`.`id_metal` AS `id_metal`
            FROM
                (
                    `retaillogimaxind_test_etail_v3`.`ret_purchase_order_items` `pitm`
                LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_category` `cate`
                ON
                    (
                        `cate`.`id_ret_category` = `pitm`.`po_item_cat_id`
                    )
                )
            GROUP BY
                `pitm`.`po_item_po_id`
        ) `pitm`
    ON
        (`pitm`.`poid` = `po`.`po_id`)
        )
    LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `po`.`isratefixed` = 0 AND `po`.`is_suspense_stock` = 0 AND `rf`.`bill_status` = 1 AND `rf`.`is_approved` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    CONCAT(
        'RATE FIXING',
        IF(
            `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    IFNULL(`rc`.`po_id`, '') AS `po_id`,
    `rc`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    IF(
        `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    `rc`.`weight` AS `gross_wt`,
    `rc`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `rc`.`weight` AS `purewt`,
    `rc`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (
                `rc`.`rate_per_gram` - `rf`.`rate_fix_rate`
            ) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        `retaillogimaxind_test_etail_v3`.`ret_po_rate_fix` `rf`
    LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_supplier_rate_cut` `rc`
    ON
        (
            `rc`.`id_supplier_rate_cut` = `rf`.`id_approval_ratecut`
        )
    )
WHERE
    `rf`.`rate_fix_type` = 2 AND `rf`.`bill_status` = 1 AND `rf`.`is_approved` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    IFNULL(
        `pr`.`product_name`,
        'Product Unavailable'
    ) AS `category`,
    IFNULL(`po`.`po_id`, '') AS `po_id`,
    `met`.`id_metal` AS `id_metal`,
    IFNULL(`pr`.`product_name`, '') AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `retaillogimaxind_test_etail_v3`.`ret_purchase_return_items` `pret`
                                LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `retaillogimaxind_test_etail_v3`.`metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    IFNULL(
        `pr`.`product_name`,
        'Product Unavailable'
    ) AS `category`,
    IFNULL(`po`.`po_id`, '') AS `po_id`,
    `met`.`id_metal` AS `id_metal`,
    IFNULL(`pr`.`product_name`, '') AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `retaillogimaxind_test_etail_v3`.`ret_purchase_return_items` `pret`
                                LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `retaillogimaxind_test_etail_v3`.`metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    IFNULL(`pr`.`product_name`, 'RATE FIXING') AS `category`,
    IFNULL(`rf`.`po_id`, '') AS `po_id`,
    `rf`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    1 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '100' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `rf`.`id_metal` AS `metal`,
    IF(
        `rf`.`conversion_type` = 2,
        CONCAT('(Unfix)', `rf`.`rate_per_gram`),
        `rf`.`rate_per_gram`
    ) AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        `retaillogimaxind_test_etail_v3`.`ret_supplier_rate_cut` `rf`
    LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_product_master` `pr`
    ON
        (`pr`.`pro_id` = `rf`.`id_product`)
    )
WHERE
    `rf`.`rate_cut_type` = 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `po_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    `pay`.`weight` AS `gross_wt`,
    `pay`.`net_wt` AS `net_wt`,
    `pay`.`pieces` AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `pay`.`pure_wt` AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `retaillogimaxind_test_etail_v3`.`smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `po_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`net_wt`, 0) AS `net_wt`,
    `pay`.`pieces` AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `pay`.`pure_wt` AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `retaillogimaxind_test_etail_v3`.`smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    `pay`.`po_id` AS `po_id`,
    `cat`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    `pay`.`naration` AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    `retaillogimaxind_test_etail_v3`.`ret_crdr_note` `pay`
     LEFT JOIN `ret_purchase_order_items` `po`
	 ON 
        (`pay`.`po_id` = `po`.`po_item_po_id`)
     LEFT JOIN `ret_category` `cat`
     ON
    	(`cat`.`id_ret_category` = `po`.`po_item_cat_id`)
WHERE
    `pay`.`accountto` = 1 AND `pay`.`transamount` > 0 AND `pay`.`crdr_status` = 1
GROUP BY
    `pay`.`crdrid`
UNION ALL
SELECT
    'TDS' AS `category`,
    `po`.`po_id` AS `po_id`,
    `cat`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tds_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    `retaillogimaxind_test_etail_v3`.`ret_purchase_order` `po`
    LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_grn_entry` `grn`
    ON
        (`grn`.`grn_id` = `po`.`po_grn_id`)
    LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_karigar` `kr`
    ON
        (`kr`.`id_karigar` = `po`.`po_karigar_id`)
    LEFT JOIN `ret_purchase_order_items` `item`
    ON 
        (`po`.`po_id` = `item`.`po_item_po_id`)
    LEFT JOIN `ret_category` `cat`
    ON
        (`cat`.`id_ret_category` = `item`.`po_item_cat_id`)    
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tds_tax_value` > 0
GROUP BY
    `po`.`po_id`
UNION ALL
SELECT
    'TCS' AS `category`,
    `po`.`po_id` AS `po_id`,
    '' AS `id_metal`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tcs_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            `retaillogimaxind_test_etail_v3`.`ret_purchase_order` `po`
        LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_grn_entry` `grn`
        ON
            (`grn`.`grn_id` = `po`.`po_grn_id`)
        )
    LEFT JOIN `retaillogimaxind_test_etail_v3`.`ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tcs_tax_value` > 0
GROUP BY
    `po`.`po_id`
ORDER BY
    `unixtransdate`

-- Nambi Muthu Raja 27-01-2026 

ALTER TABLE `metal_rates` ADD `goldrate_14ct` DECIMAL(10,2 ) NULL DEFAULT NULL AFTER `platinum_1g`;

ALTER TABLE `ret_billing` ADD `goldrate_14ct` DECIMAL(10,2) NULL DEFAULT NULL AFTER `silverrate_1gm`;

ALTER TABLE `ret_estimation` ADD `goldrate_14ct` DECIMAL(10,2) NULL DEFAULT NULL AFTER `silverrate_1gm`;

-- Nambi Gokul 29-01-2026 

ALTER TABLE `metal_rates` ADD `goldrate_9ct` DECIMAL(10,2 ) NULL DEFAULT NULL AFTER `platinum_1g`;

ALTER TABLE `ret_billing` ADD `goldrate_9ct` DECIMAL(10,2) NULL DEFAULT NULL AFTER `silverrate_1gm`;

ALTER TABLE `ret_estimation` ADD `goldrate_9ct` DECIMAL(10,2) NULL DEFAULT NULL AFTER `silverrate_1gm`;

-- rudra 27-01-2026


DROP VIEW IF EXISTS `ret_view_smith_combined_ledger`;

CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `ret_view_smith_combined_ledger`  AS 
SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `grn`.`grn_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 1' AS `qry_type`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `retaillogimaxind_etailv2`.`ret_purchase_order_items` `pitm`
                        LEFT JOIN `retaillogimaxind_etailv2`.`ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `retaillogimaxind_etailv2`.`ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `retaillogimaxind_etailv2`.`ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `retaillogimaxind_etailv2`.`ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `retaillogimaxind_etailv2`.`metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `retaillogimaxind_etailv2`.`ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` = 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 1
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    'PAYMENT' AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    'type 2' AS `qry_type`,
    2 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    `rf`.`rate_per_gram` AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    `retaillogimaxind_etailv2`.`ret_supplier_rate_cut` `rf`
WHERE
    `rf`.`rate_cut_type` = 1 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    2 AS `ledger_type`,
    IF(
        `rf`.`weight` = 0,
        'Bill Conv(Amount)',
        'Bill Conv(A to P)'
    ) AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    'type 3' AS `qry_type`,
    2 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    IF(`rf`.`weight` = 0, '', '100') AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    IF(
        `rf`.`charges_amount` > 0,
        `rf`.`charges_amount`,
        ''
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `rf`.`id_metal` AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    `rf`.`rate_per_gram` AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    `retaillogimaxind_etailv2`.`ret_supplier_rate_cut` `rf`
WHERE
    `rf`.`rate_cut_type` = 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `grn`.`grn_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 4' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `retaillogimaxind_etailv2`.`ret_purchase_return_items` `pret`
                                LEFT JOIN `retaillogimaxind_etailv2`.`ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `retaillogimaxind_etailv2`.`ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `retaillogimaxind_etailv2`.`ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `retaillogimaxind_etailv2`.`ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `retaillogimaxind_etailv2`.`ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `retaillogimaxind_etailv2`.`ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `retaillogimaxind_etailv2`.`ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `retaillogimaxind_etailv2`.`metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `grn`.`grn_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 5' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `retaillogimaxind_etailv2`.`ret_purchase_return_items` `pret`
                                LEFT JOIN `retaillogimaxind_etailv2`.`ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `retaillogimaxind_etailv2`.`ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `retaillogimaxind_etailv2`.`ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `retaillogimaxind_etailv2`.`ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `retaillogimaxind_etailv2`.`ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `retaillogimaxind_etailv2`.`ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `retaillogimaxind_etailv2`.`ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `retaillogimaxind_etailv2`.`metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `iss`.`met_issue_date` AS `trans_date`,
    `iss`.`met_issue_ref_id` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '/karigarmetalissue_acknowladgement/' AS `link`,
    `iitm`.`issue_met_parent_id` AS `print_id`,
    'type 6' AS `qry_type`,
    2 AS `trans_type`,
    `iitm`.`issue_met_parent_id` AS `trans_id`,
    IF(
        IFNULL(`uom`.`divided_by_value`, 0) = 0,
        `iitm`.`issue_metal_wt`,
        ROUND(
            `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
            3
        )
    ) AS `gross_wt`,
    IF(
        IFNULL(`uom`.`divided_by_value`, 0) = 0,
        `iitm`.`issue_metal_wt`,
        ROUND(
            `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
            3
        )
    ) AS `net_wt`,
    IFNULL(`iitm`.`issue_pcs`, 1) AS `no_of_pcs`,
    `iitm`.`touch` AS `purchase_touch`,
    IF(
        `pr`.`stone_type` = 0,
        `iitm`.`issue_metal_pur_wt`,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `purewt`,
    `iss`.`met_issue_karid` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    `iitm`.`issue_cat_id` AS `catid`,
    `iitm`.`issu_met_pro_id` AS `proid`,
    2 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    IFNULL(`iss`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`iss`.`met_issue_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `retaillogimaxind_etailv2`.`ret_karigar_metal_issue_details` `iitm`
                        LEFT JOIN `retaillogimaxind_etailv2`.`ret_karigar_metal_issue` `iss`
                        ON
                            (
                                `iss`.`met_issue_id` = `iitm`.`issue_met_parent_id`
                            )
                        )
                    LEFT JOIN `retaillogimaxind_etailv2`.`ret_karigar` `kr`
                    ON
                        (
                            `kr`.`id_karigar` = `iss`.`met_issue_karid`
                        )
                    )
                LEFT JOIN `retaillogimaxind_etailv2`.`ret_category` `cat`
                ON
                    (
                        `cat`.`id_ret_category` = `iitm`.`issue_cat_id`
                    )
                )
            LEFT JOIN `retaillogimaxind_etailv2`.`metal` `met`
            ON
                (`met`.`id_metal` = `cat`.`id_metal`)
            )
        LEFT JOIN `retaillogimaxind_etailv2`.`ret_product_master` `pr`
        ON
            (
                `pr`.`pro_id` = `iitm`.`issu_met_pro_id`
            )
        )
    LEFT JOIN `retaillogimaxind_etailv2`.`ret_uom` `uom`
    ON
        (`uom`.`uom_id` = `iitm`.`issue_uom_id`)
    )
WHERE
    `iss`.`metalissue_type` = 2 AND `iss`.`bill_status` = 1
GROUP BY
    `iitm`.`issue_met_id`,
    `iitm`.`issue_cat_id`,
    `iitm`.`issu_met_pro_id`,
    `iitm`.`issue_met_parent_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 7' AS `qry_type`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `retaillogimaxind_etailv2`.`smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    2 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 8' AS `qry_type`,
    `pay`.`weight_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    IFNULL(`pay`.`weight`, 0) AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `retaillogimaxind_etailv2`.`smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    2 AS `ledger_type`,
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '/credit_debit_acknolodgement/' AS `link`,
    `pay`.`crdrid` AS `print_id`,
    'type 9' AS `qry_type`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`naration`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    `retaillogimaxind_etailv2`.`ret_crdr_note` `pay`
WHERE
    `pay`.`accountto` = 3 AND `pay`.`transamount` > 0
GROUP BY
    `pay`.`crdrid`
UNION ALL
SELECT
    1 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `grn`.`grn_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 10' AS `qry_type`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `retaillogimaxind_etailv2`.`ret_purchase_order_items` `pitm`
                        LEFT JOIN `retaillogimaxind_etailv2`.`ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `retaillogimaxind_etailv2`.`ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `retaillogimaxind_etailv2`.`ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `retaillogimaxind_etailv2`.`ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `retaillogimaxind_etailv2`.`metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `retaillogimaxind_etailv2`.`ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'PAYMENT' AS `category`,
    '' AS `product`,
    `pay`.`pay_create_on` AS `trans_date`,
    `pay`.`pay_refno` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '/supplier_po_payment/paymentacknolodgement/' AS `link`,
    `pay`.`pay_id` AS `print_id`,
    'type 11' AS `qry_type`,
    2 AS `trans_type`,
    `pay`.`pay_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`pay_sup_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pd`.`payment_amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    `pay`.`pay_narration` AS `narration`,
    UNIX_TIMESTAMP(`pay`.`pay_create_on`) AS `unixtransdate`
FROM
    (
        `retaillogimaxind_etailv2`.`ret_po_payment` `pay`
    LEFT JOIN `retaillogimaxind_etailv2`.`ret_po_payment_detail` `pd`
    ON
        (`pd`.`pay_id` = `pay`.`pay_id`)
    )
WHERE
    `pay`.`pay_status` = 1 AND `pay`.`bill_type` = 1
GROUP BY
    `pay`.`pay_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    CONCAT(
        'RATE FIXING',
        IF(
            `pitm`.`rate` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `grn`.`grn_ref_no` AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 12' AS `qry_type`,
    IF(
        `pitm`.`rate` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    '' AS `gross_wt`,
    '' AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    '' AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (`pitm`.`rate` - `rf`.`rate_fix_rate`) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    `retaillogimaxind_etailv2`.`ret_po_rate_fix` `rf`
                LEFT JOIN `retaillogimaxind_etailv2`.`ret_purchase_order` `po`
                ON
                    (
                        `po`.`po_id` = `rf`.`rate_fix_po_item_id`
                    )
                )
            LEFT JOIN `retaillogimaxind_etailv2`.`ret_grn_entry` `grn`
            ON
                (
                    `grn`.`grn_id` = `po`.`po_grn_id`
                )
            )
        LEFT JOIN(
            SELECT
                `pitm`.`po_item_po_id` AS `poid`,
                `pitm`.`fix_rate_per_grm` AS `rate`
            FROM
                `retaillogimaxind_etailv2`.`ret_purchase_order_items` `pitm`
            GROUP BY
                `pitm`.`po_item_po_id`
        ) `pitm`
    ON
        (`pitm`.`poid` = `po`.`po_id`)
        )
    LEFT JOIN `retaillogimaxind_etailv2`.`ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `po`.`isratefixed` = 0 AND `po`.`is_suspense_stock` = 0 AND `rf`.`bill_status` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    CONCAT(
        'RATE FIXING',
        IF(
            `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 13' AS `qry_type`,
    IF(
        `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    '' AS `gross_wt`,
    '' AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    '' AS `purewt`,
    `rc`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (
                `rc`.`rate_per_gram` - `rf`.`rate_fix_rate`
            ) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        `retaillogimaxind_etailv2`.`ret_po_rate_fix` `rf`
    LEFT JOIN `retaillogimaxind_etailv2`.`ret_supplier_rate_cut` `rc`
    ON
        (
            `rc`.`id_supplier_rate_cut` = `rf`.`id_approval_ratecut`
        )
    )
WHERE
    `rf`.`rate_fix_type` = 2 AND `rf`.`bill_status` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `grn`.`grn_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 14' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `retaillogimaxind_etailv2`.`ret_purchase_return_items` `pret`
                                LEFT JOIN `retaillogimaxind_etailv2`.`ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `retaillogimaxind_etailv2`.`ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `retaillogimaxind_etailv2`.`ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `retaillogimaxind_etailv2`.`ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `retaillogimaxind_etailv2`.`ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `retaillogimaxind_etailv2`.`ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `retaillogimaxind_etailv2`.`ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `retaillogimaxind_etailv2`.`metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `grn`.`grn_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 15' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `retaillogimaxind_etailv2`.`ret_purchase_return_items` `pret`
                                LEFT JOIN `retaillogimaxind_etailv2`.`ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `retaillogimaxind_etailv2`.`ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `retaillogimaxind_etailv2`.`ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `retaillogimaxind_etailv2`.`ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `retaillogimaxind_etailv2`.`ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `retaillogimaxind_etailv2`.`ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `retaillogimaxind_etailv2`.`ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `retaillogimaxind_etailv2`.`metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    'type 16' AS `qry_type`,
    1 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '100' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    IF(
        `rf`.`conversion_type` = 2,
        CONCAT(`rf`.`rate_per_gram`, '(Unfix)'),
        `rf`.`rate_per_gram`
    ) AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        `retaillogimaxind_etailv2`.`ret_supplier_rate_cut` `rf`
    LEFT JOIN `retaillogimaxind_etailv2`.`ret_product_master` `pr`
    ON
        (`pr`.`pro_id` = `rf`.`id_product`)
    )
WHERE
    `rf`.`rate_cut_type` = 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 17' AS `qry_type`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `retaillogimaxind_etailv2`.`smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 18' AS `qry_type`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `retaillogimaxind_etailv2`.`smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    1 AS `ledger_type`,
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '/credit_debit_acknolodgement/' AS `link`,
    `pay`.`crdrid` AS `print_id`,
    'type 19' AS `qry_type`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    `pay`.`naration` AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    `retaillogimaxind_etailv2`.`ret_crdr_note` `pay`
WHERE
    `pay`.`accountto` = 1 AND `pay`.`transamount` > 0 AND `pay`.`crdr_status` = 1
GROUP BY
    `pay`.`crdrid`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'TDS' AS `category`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `grn`.`grn_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 20' AS `qry_type`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tds_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            `retaillogimaxind_etailv2`.`ret_purchase_order` `po`
        LEFT JOIN `retaillogimaxind_etailv2`.`ret_grn_entry` `grn`
        ON
            (`grn`.`grn_id` = `po`.`po_grn_id`)
        )
    LEFT JOIN `retaillogimaxind_etailv2`.`ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tds_tax_value` > 0
GROUP BY
    `po`.`po_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'TCS' AS `category`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `grn`.`grn_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 21' AS `qry_type`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tcs_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            `retaillogimaxind_etailv2`.`ret_purchase_order` `po`
        LEFT JOIN `retaillogimaxind_etailv2`.`ret_grn_entry` `grn`
        ON
            (`grn`.`grn_id` = `po`.`po_grn_id`)
        )
    LEFT JOIN `retaillogimaxind_etailv2`.`ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tcs_tax_value` > 0
GROUP BY
    `po`.`po_id`
ORDER BY
    `unixtransdate`


 -- rudra 28-01-2026

 ALTER TABLE `ret_karigar` ADD `owner_account` INT NOT NULL AFTER `opening_balance_amount`;

 -- rudra 20-01-2026
 
 ALTER TABLE `ret_bill_old_metal_sale_details` ADD `id_employee` INT NOT NULL;

 -- rudra 04-02-2026

CREATE TABLE `ret_order_email_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_customerorder` int(11) NOT NULL,
  `id_karigar` int(11) NOT NULL,
  `email_id` varchar(255) DEFAULT NULL,
  `token` varchar(100) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0: Not Sent, 1: Sent, 2: Failed, 3: Accepted',
  `due_date` date DEFAULT NULL,
  `error_msg` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `accepted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_customerorder` (`id_customerorder`),
  KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ret_order_email_logs ADD COLUMN rejected_at DATETIME NULL AFTER accepted_at; ALTER TABLE ret_order_email_logs ADD COLUMN rejection_reason TEXT NULL AFTER rejected_at;

ALTER TABLE `ret_order_email_logs` 
ADD COLUMN `rejected_at` DATETIME NULL AFTER `accepted_at`,
ADD COLUMN `rejection_reason` TEXT NULL AFTER `rejected_at`;

-- Update company table with Gmail SMTP settings
-- Replace the values with your actual credentials

UPDATE company SET 
  smtp_user = 'mruthramoorthi66@gmail.com',           -- Your Gmail address
  smtp_pass = 'lpsoqtzmrorwmwpq',                     -- Your App Password (no spaces)
  smtp_host = 'smtp.gmail.com',                        -- Gmail SMTP server
WHERE id_company = 1;  -- Update this with your actual company ID
 -- Nambi Muthu Raja 02-02-2026 

ALTER TABLE ret_insurance CHANGE insurance_id insurance_id INT(11) NOT NULL AUTO_INCREMENT;


--  Karthikai Kumaran 05/02/26

-- Dynamic decimal for metal weight

ALTER TABLE
    scheme_account CHANGE dg_target_value_wgt dg_target_value_wgt DECIMAL(10, 4) NOT NULL DEFAULT '0.000',
    CHANGE dg_target_wgt_achieved dg_target_wgt_achieved DECIMAL(10, 4) NOT NULL DEFAULT '0.000',
    CHANGE closing_balance closing_balance DECIMAL(10, 4) NULL DEFAULT '0.000',
    CHANGE tot_genadv_wgt_paid tot_genadv_wgt_paid DECIMAL(10, 4) NULL DEFAULT NULL,
    CHANGE tot_genadv_benefit_wgt tot_genadv_benefit_wgt DECIMAL(10, 4) NULL DEFAULT NULL,
    CHANGE lump_joined_weight lump_joined_weight DECIMAL(10, 4) NOT NULL DEFAULT '0.000' COMMENT 'lump sum scheme-> customer choosing total weight at the time of scheme join for all total installments',
    CHANGE lump_payable_weight lump_payable_weight DECIMAL(10, 4) NOT NULL DEFAULT '0.000' COMMENT 'lump sum scheme-> weight payable per installment calculated by (lump_joined_weight / total_installments)',
    CHANGE topup_weight topup_weight DECIMAL(10, 4) NOT NULL DEFAULT '0.000' COMMENT 'Joining time booking weight';
ALTER TABLE
    payment CHANGE metal_rate metal_rate DECIMAL(10, 2) NULL DEFAULT '0.00',
    CHANGE metal_weight metal_weight DECIMAL(12, 4) NULL DEFAULT NULL,
    CHANGE old_metal_weight old_metal_weight DECIMAL(10, 4) NOT NULL DEFAULT '0.000',
    CHANGE saved_benefits saved_benefits DECIMAL(10, 4) NULL DEFAULT NULL COMMENT 'For digi gold(weight) ',
    CHANGE benefit_value benefit_value DECIMAL(10, 4) NULL DEFAULT NULL COMMENT 'For digi gold';

UPDATE chit_settings SET metal_wgt_roundoff = '0' WHERE chit_settings.id_chit_settings = 1;
UPDATE chit_settings SET metal_wgt_decimal = '4' WHERE chit_settings.id_chit_settings = 1;

-- rudra -- 6/2/2026 -- approval ledger

SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_purchase_order_items` `pitm`
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` = 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    'PAYMENT' AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    2 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    `rf`.`id_metal` AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    `rf`.`rate_per_gram` AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        (
            `ret_supplier_rate_cut` `rf`
        LEFT JOIN `ret_purchase_order_items` `order`
        ON
            (`order`.`po_item_po_id` = `rf`.`po_id`)
        )
    LEFT JOIN `ret_category` `cate`
    ON
        (
            `cate`.`id_ret_category` = `order`.`po_item_cat_id`
        )
    )
WHERE
    `rf`.`rate_cut_type` = 1 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    IF(
        `rf`.`weight` = 0,
        'Bill Conv(Amount)',
        'Bill Conv(A to P)'
    ) AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    2 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    IF(`rf`.`weight` = 0, '', '100') AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    IF(
        `rf`.`charges_amount` > 0,
        `rf`.`charges_amount`,
        ''
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `rf`.`id_metal` AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    `rf`.`rate_per_gram` AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    `ret_supplier_rate_cut` `rf`
WHERE
    `rf`.`rate_cut_type` = 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `iss`.`met_issue_date` AS `trans_date`,
    `iss`.`met_issue_ref_id` AS `referenceno`,
    '/karigarmetalissue_acknowladgement/' AS `link`,
    `iitm`.`issue_met_parent_id` AS `print_id`,
    2 AS `trans_type`,
    `iitm`.`issue_met_parent_id` AS `trans_id`,
    IF(
        `cat`.`cat_type` = 2,
        0,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `gross_wt`,
    IF(
        `cat`.`cat_type` = 2,
        0,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `net_wt`,
    IFNULL(`iitm`.`issue_pcs`, 1) AS `no_of_pcs`,
    '100' AS `purchase_touch`,
    IF(
        `pr`.`stone_type` = 0,
        `iitm`.`issue_metal_pur_wt`,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `purewt`,
    `iss`.`met_issue_karid` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    `iitm`.`issue_cat_id` AS `catid`,
    `iitm`.`issu_met_pro_id` AS `proid`,
    2 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    IFNULL(`iss`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`iss`.`met_issue_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_karigar_metal_issue_details` `iitm`
                        LEFT JOIN `ret_karigar_metal_issue` `iss`
                        ON
                            (
                                `iss`.`met_issue_id` = `iitm`.`issue_met_parent_id`
                            )
                        )
                    LEFT JOIN `ret_karigar` `kr`
                    ON
                        (
                            `kr`.`id_karigar` = `iss`.`met_issue_karid`
                        )
                    )
                LEFT JOIN `ret_category` `cat`
                ON
                    (
                        `cat`.`id_ret_category` = `iitm`.`issue_cat_id`
                    )
                )
            LEFT JOIN `metal` `met`
            ON
                (`met`.`id_metal` = `cat`.`id_metal`)
            )
        LEFT JOIN `ret_product_master` `pr`
        ON
            (
                `pr`.`pro_id` = `iitm`.`issu_met_pro_id`
            )
        )
    LEFT JOIN `ret_uom` `uom`
    ON
        (`uom`.`uom_id` = `iitm`.`issue_uom_id`)
    )
WHERE
    `iss`.`metalissue_type` = 2 AND `iss`.`bill_status` = 1
GROUP BY
    `iitm`.`issue_met_id`,
    `iitm`.`issue_cat_id`,
    `iitm`.`issu_met_pro_id`,
    `iitm`.`issue_met_parent_id`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `link`,
    '' AS `print_id`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `link`,
    '' AS `print_id`,
    `pay`.`weight_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    IFNULL(`pay`.`weight`, 0) AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    '/credit_debit_acknolodgement/' AS `link`,
    `pay`.`crdrid` AS `print_id`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    `pay`.`weight` AS `gross_wt`,
    `pay`.`weight` AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `pay`.`weight` AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `cate`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`naration`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    (
        (
            `ret_crdr_note` `pay`
        LEFT JOIN `ret_purchase_order_items` `order`
        ON
            (
                `order`.`po_item_po_id` = `pay`.`po_id`
            )
        )
    LEFT JOIN `ret_category` `cate`
    ON
        (
            `cate`.`id_ret_category` = `order`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`accountto` = 3 AND `pay`.`crdr_status` = 1 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`crdrid`
ORDER BY
    `unixtransdate`
DESC
    
-- rudra -- 6/2/2026 -- supplier ledger

SELECT
    IFNULL(
        `pr`.`product_name`,
        'Product Unavailable'
    ) AS `category`,
    `po`.`po_id` AS `po_id`,
    `met`.`id_metal` AS `id_metal`,
    IFNULL(`pr`.`product_name`, '') AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_purchase_order_items` `pitm`
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    'PAYMENT' AS `category`,
    IFNULL(`bill`.`po_id`, '') AS `po_id`,
    `cat`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `pay`.`pay_create_on` AS `trans_date`,
    `pay`.`pay_refno` AS `referenceno`,
    2 AS `trans_type`,
    `pay`.`pay_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`pay_sup_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pd`.`payment_amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`pay`.`pay_create_on`) AS `unixtransdate`
FROM
    `ret_po_payment` `pay`
    LEFT JOIN `ret_po_payment_detail` `pd`
    ON
        (`pd`.`pay_id` = `pay`.`pay_id`)
    LEFT JOIN `ret_po_bill_payment_details` `bill`
    ON
        (`bill`.`pay_id` = `pay`.`pay_id`)
    LEFT JOIN (
            SELECT `po_item_po_id`, `po_item_cat_id`
            FROM `ret_purchase_order_items`
            GROUP BY `po_item_po_id`
        ) `po`
        ON `bill`.`po_id` = `po`.`po_item_po_id`
    LEFT JOIN `ret_category` `cat`
    ON
        (`cat`.`id_ret_category` = `po`.`po_item_cat_id`)
WHERE
    `pay`.`pay_status` = 1 AND `pay`.`bill_type` = 1
GROUP BY
    `pay`.`pay_id`
UNION ALL
SELECT
    CONCAT(
        'RATE FIXING',
        IF(
            `pitm`.`rate` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    IFNULL(`po`.`po_id`, '') AS `po_id`,
    `pitm`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    IF(
        `pitm`.`rate` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    '' AS `gross_wt`,
    '' AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    '' AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (`pitm`.`rate` - `rf`.`rate_fix_rate`) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        (
            (
                `ret_po_rate_fix` `rf`
            LEFT JOIN `ret_purchase_order` `po`
            ON
                (
                    `po`.`po_id` = `rf`.`rate_fix_po_item_id`
                )
            )
        LEFT JOIN(
            SELECT
                `pitm`.`po_item_po_id` AS `poid`,
                `pitm`.`fix_rate_per_grm` AS `rate`,
                `cate`.`id_metal` AS `id_metal`
            FROM
                (
                    `ret_purchase_order_items` `pitm`
                LEFT JOIN `ret_category` `cate`
                ON
                    (
                        `cate`.`id_ret_category` = `pitm`.`po_item_cat_id`
                    )
                )
            GROUP BY
                `pitm`.`po_item_po_id`
        ) `pitm`
    ON
        (`pitm`.`poid` = `po`.`po_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `po`.`isratefixed` = 0 AND `po`.`is_suspense_stock` = 0 AND `rf`.`bill_status` = 1 AND `rf`.`is_approved` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    CONCAT(
        'RATE FIXING',
        IF(
            `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    IFNULL(`rc`.`po_id`, '') AS `po_id`,
    `rc`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    IF(
        `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    `rc`.`weight` AS `gross_wt`,
    `rc`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `rc`.`weight` AS `purewt`,
    `rc`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (
                `rc`.`rate_per_gram` - `rf`.`rate_fix_rate`
            ) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        `ret_po_rate_fix` `rf`
    LEFT JOIN `ret_supplier_rate_cut` `rc`
    ON
        (
            `rc`.`id_supplier_rate_cut` = `rf`.`id_approval_ratecut`
        )
    )
WHERE
    `rf`.`rate_fix_type` = 2 AND `rf`.`bill_status` = 1 AND `rf`.`is_approved` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    IFNULL(
        `pr`.`product_name`,
        'Product Unavailable'
    ) AS `category`,
    IFNULL(`po`.`po_id`, '') AS `po_id`,
    `met`.`id_metal` AS `id_metal`,
    IFNULL(`pr`.`product_name`, '') AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    IFNULL(
        `pr`.`product_name`,
        'Product Unavailable'
    ) AS `category`,
    IFNULL(`po`.`po_id`, '') AS `po_id`,
    `met`.`id_metal` AS `id_metal`,
    IFNULL(`pr`.`product_name`, '') AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    IFNULL(`pr`.`product_name`, 'RATE FIXING') AS `category`,
    IFNULL(`rf`.`po_id`, '') AS `po_id`,
    `rf`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    1 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '100' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `rf`.`id_metal` AS `metal`,
    IF(
        `rf`.`conversion_type` = 2,
        CONCAT('(Unfix)', `rf`.`rate_per_gram`),
        `rf`.`rate_per_gram`
    ) AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        `ret_supplier_rate_cut` `rf`
    LEFT JOIN `ret_product_master` `pr`
    ON
        (`pr`.`pro_id` = `rf`.`id_product`)
    )
WHERE
    `rf`.`rate_cut_type` = 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `po_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    `pay`.`weight` AS `gross_wt`,
    `pay`.`net_wt` AS `net_wt`,
    `pay`.`pieces` AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `pay`.`pure_wt` AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `po_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`net_wt`, 0) AS `net_wt`,
    `pay`.`pieces` AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `pay`.`pure_wt` AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    `pay`.`po_id` AS `po_id`,
    `cat`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    `pay`.`naration` AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    (
        (
            `ret_crdr_note` `pay`
        LEFT JOIN (
            SELECT `po_item_po_id`, `po_item_cat_id`
            FROM `ret_purchase_order_items`
            GROUP BY `po_item_po_id`
        ) `po`
        ON `pay`.`po_id` = `po`.`po_item_po_id`
        )
    LEFT JOIN `ret_category` `cat`
    ON
        (
            `cat`.`id_ret_category` = `po`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`accountto` = 1 AND `pay`.`transamount` > 0 AND `pay`.`crdr_status` = 1
GROUP BY
    `pay`.`crdrid`
UNION ALL
SELECT
    'TDS' AS `category`,
    `po`.`po_id` AS `po_id`,
    `cat`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tds_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    `ret_purchase_order` `po`
                LEFT JOIN `ret_grn_entry` `grn`
                ON
                    (`grn`.`grn_id` = `po`.`po_grn_id`)
                )
            LEFT JOIN `ret_karigar` `kr`
            ON
                (
                    `kr`.`id_karigar` = `po`.`po_karigar_id`
                )
            )
            LEFT JOIN (
                SELECT `po_item_po_id`, `po_item_cat_id`
                FROM `ret_purchase_order_items`
                GROUP BY `po_item_po_id`
            ) `item`
            ON `po`.`po_id` = `item`.`po_item_po_id`
        )
    LEFT JOIN `ret_category` `cat`
    ON
        (
            `cat`.`id_ret_category` = `item`.`po_item_cat_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tds_tax_value` > 0
GROUP BY
    `po`.`po_id`
UNION ALL
SELECT
    'TCS' AS `category`,
    `po`.`po_id` AS `po_id`,
    '' AS `id_metal`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tcs_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            `ret_purchase_order` `po`
        LEFT JOIN `ret_grn_entry` `grn`
        ON
            (`grn`.`grn_id` = `po`.`po_grn_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tcs_tax_value` > 0
GROUP BY
    `po`.`po_id`
ORDER BY
    `unixtransdate`
DESC
    
-- rudra -- 6/2/2026 -- combined ledger

SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 1' AS `qry_type`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_purchase_order_items` `pitm`
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` = 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 1
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    'PAYMENT' AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    'type 2' AS `qry_type`,
    2 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    `rf`.`rate_per_gram` AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    `ret_supplier_rate_cut` `rf`
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `rf`.`po_id`)
WHERE
    `rf`.`rate_cut_type` = 1 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    2 AS `ledger_type`,
    IF(
        `rf`.`weight` = 0,
        'Bill Conv(Amount)',
        'Bill Conv(A to P)'
    ) AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    'type 3' AS `qry_type`,
    2 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    IF(`rf`.`weight` = 0, '', '100') AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    IF(
        `rf`.`charges_amount` > 0,
        `rf`.`charges_amount`,
        ''
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `rf`.`id_metal` AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    `rf`.`rate_per_gram` AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    `ret_supplier_rate_cut` `rf`
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `rf`.`po_id`)
WHERE
    `rf`.`rate_cut_type` = 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 4' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 5' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `iss`.`met_issue_date` AS `trans_date`,
    `iss`.`met_issue_ref_id` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '/karigarmetalissue_acknowladgement/' AS `link`,
    `iitm`.`issue_met_parent_id` AS `print_id`,
    'type 6' AS `qry_type`,
    2 AS `trans_type`,
    `iitm`.`issue_met_parent_id` AS `trans_id`,
    IF(
        IFNULL(`uom`.`divided_by_value`, 0) = 0,
        `iitm`.`issue_metal_wt`,
        ROUND(
            `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
            3
        )
    ) AS `gross_wt`,
    IF(
        IFNULL(`uom`.`divided_by_value`, 0) = 0,
        `iitm`.`issue_metal_wt`,
        ROUND(
            `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
            3
        )
    ) AS `net_wt`,
    IFNULL(`iitm`.`issue_pcs`, 1) AS `no_of_pcs`,
    `iitm`.`touch` AS `purchase_touch`,
    IF(
        `pr`.`stone_type` = 0,
        `iitm`.`issue_metal_pur_wt`,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `purewt`,
    `iss`.`met_issue_karid` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    `iitm`.`issue_cat_id` AS `catid`,
    `iitm`.`issu_met_pro_id` AS `proid`,
    2 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    IFNULL(`iss`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`iss`.`met_issue_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_karigar_metal_issue_details` `iitm`
                        LEFT JOIN `ret_karigar_metal_issue` `iss`
                        ON
                            (
                                `iss`.`met_issue_id` = `iitm`.`issue_met_parent_id`
                            )
                        )
                    LEFT JOIN `ret_karigar` `kr`
                    ON
                        (
                            `kr`.`id_karigar` = `iss`.`met_issue_karid`
                        )
                    )
                LEFT JOIN `ret_category` `cat`
                ON
                    (
                        `cat`.`id_ret_category` = `iitm`.`issue_cat_id`
                    )
                )
            LEFT JOIN `metal` `met`
            ON
                (`met`.`id_metal` = `cat`.`id_metal`)
            )
        LEFT JOIN `ret_product_master` `pr`
        ON
            (
                `pr`.`pro_id` = `iitm`.`issu_met_pro_id`
            )
        )
    LEFT JOIN `ret_uom` `uom`
    ON
        (`uom`.`uom_id` = `iitm`.`issue_uom_id`)
    )
WHERE
    `iss`.`metalissue_type` = 2 AND `iss`.`bill_status` = 1
GROUP BY
    `iitm`.`issue_met_id`,
    `iitm`.`issue_cat_id`,
    `iitm`.`issu_met_pro_id`,
    `iitm`.`issue_met_parent_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 7' AS `qry_type`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    2 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 8' AS `qry_type`,
    `pay`.`weight_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    IFNULL(`pay`.`weight`, 0) AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    2 AS `ledger_type`,
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/credit_debit_acknolodgement/' AS `link`,
    `pay`.`crdrid` AS `print_id`,
    'type 9' AS `qry_type`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    IFNULL(`pay`.`weight`, 0) AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`naration`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    `ret_crdr_note` `pay`
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `pay`.`po_id`)
WHERE
    `pay`.`accountto` = 3 AND `pay`.`weight` > 0 AND `pay`.`crdr_status` = 1
GROUP BY
    `pay`.`crdrid`
UNION ALL
SELECT
    1 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 10' AS `qry_type`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_purchase_order_items` `pitm`
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'PAYMENT' AS `category`,
    '' AS `product`,
    `pay`.`pay_create_on` AS `trans_date`,
    `pay`.`pay_refno` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/supplier_po_payment/paymentacknolodgement/' AS `link`,
    `pay`.`pay_id` AS `print_id`,
    'type 11' AS `qry_type`,
    2 AS `trans_type`,
    `pay`.`pay_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`pay_sup_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pd`.`payment_amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    `pay`.`pay_narration` AS `narration`,
    UNIX_TIMESTAMP(`pay`.`pay_create_on`) AS `unixtransdate`
FROM
    (
        `ret_po_payment` `pay`
    LEFT JOIN `ret_po_payment_detail` `pd`
    ON
        (`pd`.`pay_id` = `pay`.`pay_id`)
    LEFT JOIN `ret_po_bill_payment_details` `po_bill`
    ON
        (`po_bill`.`pay_id` = `pay`.`pay_id`)
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `po_bill`.`po_id`)
    )
WHERE
    `pay`.`pay_status` = 1 AND `pay`.`bill_type` = 1
GROUP BY
    `pay`.`pay_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    CONCAT(
        'RATE FIXING',
        IF(
            `pitm`.`rate` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 12' AS `qry_type`,
    IF(
        `pitm`.`rate` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    '' AS `gross_wt`,
    '' AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    '' AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (`pitm`.`rate` - `rf`.`rate_fix_rate`) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    `ret_po_rate_fix` `rf`
                LEFT JOIN `ret_purchase_order` `po`
                ON
                    (
                        `po`.`po_id` = `rf`.`rate_fix_po_item_id`
                    )
                )
            LEFT JOIN `ret_grn_entry` `grn`
            ON
                (`grn`.`grn_id` = `po`.`po_grn_id`)
            )
        LEFT JOIN(
            SELECT
                `pitm`.`po_item_po_id` AS `poid`,
                `pitm`.`fix_rate_per_grm` AS `rate`
            FROM
                `ret_purchase_order_items` `pitm`
            GROUP BY
                `pitm`.`po_item_po_id`
        ) `pitm`
    ON
        (`pitm`.`poid` = `po`.`po_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `po`.`isratefixed` = 0 AND `po`.`is_suspense_stock` = 0 AND `rf`.`bill_status` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    CONCAT(
        'RATE FIXING',
        IF(
            `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 13' AS `qry_type`,
    IF(
        `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    '' AS `gross_wt`,
    '' AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    '' AS `purewt`,
    `rc`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (
                `rc`.`rate_per_gram` - `rf`.`rate_fix_rate`
            ) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        `ret_po_rate_fix` `rf`
    LEFT JOIN `ret_supplier_rate_cut` `rc`
    ON
        (
            `rc`.`id_supplier_rate_cut` = `rf`.`id_approval_ratecut`
        )
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `rc`.`po_id`)
    )
WHERE
    `rf`.`rate_fix_type` = 2 AND `rf`.`bill_status` = 1 and `rc`.`conversion_type` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 14' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 15' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    ifnull(`pr`.`product_name`, 'PURE') AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    'type 16' AS `qry_type`,
    1 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '100' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    IF(
        `rf`.`conversion_type` = 2,
        CONCAT(`rf`.`rate_per_gram`, '(Unfix)'),
        `rf`.`rate_per_gram`
    ) AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        `ret_supplier_rate_cut` `rf`
    LEFT JOIN `ret_product_master` `pr`
    ON
        (`pr`.`pro_id` = `rf`.`id_product`)
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `rf`.`po_id`)
    )
WHERE
    `rf`.`rate_cut_type` = 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 17' AS `qry_type`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 18' AS `qry_type`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    1 AS `ledger_type`,
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/credit_debit_acknolodgement/' AS `link`,
    `pay`.`crdrid` AS `print_id`,
    'type 19' AS `qry_type`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    `pay`.`naration` AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    `ret_crdr_note` `pay`
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `pay`.`po_id`)
WHERE
    `pay`.`accountto` = 1 AND `pay`.`transamount` > 0 AND `pay`.`crdr_status` = 1
GROUP BY
    `pay`.`crdrid`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'TDS' AS `category`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 20' AS `qry_type`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tds_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            `ret_purchase_order` `po`
        LEFT JOIN `ret_grn_entry` `grn`
        ON
            (`grn`.`grn_id` = `po`.`po_grn_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tds_tax_value` > 0
GROUP BY
    `po`.`po_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'TCS' AS `category`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 21' AS `qry_type`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tcs_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            `ret_purchase_order` `po`
        LEFT JOIN `ret_grn_entry` `grn`
        ON
            (`grn`.`grn_id` = `po`.`po_grn_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tcs_tax_value` > 0
GROUP BY
    `po`.`po_id`
ORDER BY
    `unixtransdate`
desc

-- 11-02-26 Devadharshini - Ledger report update start

-- Add minimum balance column
ALTER TABLE `ledger_master` 
ADD COLUMN `min_balance` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `opening_date`;

-- Added Comment in issue receipt table
ALTER TABLE `ret_issue_receipt` CHANGE `issue_type` `issue_type` TINYINT(1) NULL DEFAULT NULL COMMENT '1 - Petty Cash, 2 - Credit,3-Advance Refund,4-Existing Credit Sales, 6 - Non jewellery Expenses';

-- Ledger report end.

-- 16-02-26 Gokul - 
ALTER TABLE `ret_old_metal_category` ADD `is_active` INT NULL COMMENT '1 -> Active, 0 -> InActive' AFTER `created_by`;
-- for adding new column in ret_old_metal_category for status

-- 20-02-26 Gurushankar --
 
ALTER TABLE `ret_taging` ADD `purchase_touch` DECIMAL(10,2) NULL DEFAULT NULL AFTER `net_wt`;
-- for adding new column in ret_taging for purchase_touch

-- rudra for keep the transaction type in cr or dr (27-02-2026)

-- PUR-INT03: Add amount_type and weight_type columns to ret_supplier_rate_cut
-- Values: 1 = CR (negative/credit balance), 2 = DR (positive/debit balance), NULL = not set
-- Run this BEFORE testing the form

ALTER TABLE `ret_supplier_rate_cut`
  ADD COLUMN `amount_type` TINYINT(1) DEFAULT NULL COMMENT '1=CR, 2=DR' AFTER `igst_cost`,
  ADD COLUMN `weight_type` TINYINT(1) DEFAULT NULL COMMENT '1=CR, 2=DR' AFTER `amount_type`;

-- Rollback SQL (if needed):
-- ALTER TABLE `ret_supplier_rate_cut` DROP COLUMN `amount_type`, DROP COLUMN `weight_type`;

ALTER TABLE `ret_supplier_rate_cut` CHANGE `amount_type` `amount_type` TINYINT(1) NULL DEFAULT '1' COMMENT '1=CR, 2=DR';
ALTER TABLE `ret_supplier_rate_cut` CHANGE `weight_type` `weight_type` TINYINT(1) NULL DEFAULT '1' COMMENT '1=CR, 2=DR';

-- suplier ledger --

DROP VIEW IF EXISTS `ret_view_supplier_ledger`;

CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `ret_view_supplier_ledger`  AS 

SELECT
    IFNULL(
        `pr`.`product_name`,
        'Product Unavailable'
    ) AS `category`,
    `po`.`po_id` AS `po_id`,
    `met`.`id_metal` AS `id_metal`,
    IFNULL(`pr`.`product_name`, '') AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_purchase_order_items` `pitm`
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    'PAYMENT' AS `category`,
    IFNULL(`bill`.`po_id`, '') AS `po_id`,
    `cat`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `pay`.`pay_create_on` AS `trans_date`,
    `pay`.`pay_refno` AS `referenceno`,
    2 AS `trans_type`,
    `pay`.`pay_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`pay_sup_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`bill`.`bill_amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`pay`.`pay_create_on`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    `ret_po_payment` `pay`
                LEFT JOIN `ret_po_payment_detail` `pd`
                ON
                    (`pd`.`pay_id` = `pay`.`pay_id`)
                )
            LEFT JOIN `ret_po_bill_payment_details` `bill`
            ON
                (`bill`.`pay_id` = `pay`.`pay_id`)
            )
        LEFT JOIN(
            SELECT
                `ret_purchase_order_items`.`po_item_po_id` AS `po_item_po_id`,
                `ret_purchase_order_items`.`po_item_cat_id` AS `po_item_cat_id`
            FROM
                `ret_purchase_order_items`
            GROUP BY
                `ret_purchase_order_items`.`po_item_po_id`
        ) `po`
    ON
        (`bill`.`po_id` = `po`.`po_item_po_id`)
        )
    LEFT JOIN `ret_category` `cat`
    ON
        (
            `cat`.`id_ret_category` = `po`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`pay_status` = 1 AND `pay`.`bill_type` = 1
GROUP BY
    `bill`.`po_id`
UNION ALL
SELECT
    CONCAT(
        'RATE FIXING',
        IF(
            `pitm`.`rate` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    IFNULL(`po`.`po_id`, '') AS `po_id`,
    `pitm`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    IF(
        `pitm`.`rate` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    '' AS `gross_wt`,
    '' AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    '' AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (`pitm`.`rate` - `rf`.`rate_fix_rate`) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        (
            (
                `ret_po_rate_fix` `rf`
            LEFT JOIN `ret_purchase_order` `po`
            ON
                (
                    `po`.`po_id` = `rf`.`rate_fix_po_item_id`
                )
            )
        LEFT JOIN(
            SELECT
                `pitm`.`po_item_po_id` AS `poid`,
                `pitm`.`fix_rate_per_grm` AS `rate`,
                `cate`.`id_metal` AS `id_metal`
            FROM
                (
                    `ret_purchase_order_items` `pitm`
                LEFT JOIN `ret_category` `cate`
                ON
                    (
                        `cate`.`id_ret_category` = `pitm`.`po_item_cat_id`
                    )
                )
            GROUP BY
                `pitm`.`po_item_po_id`
        ) `pitm`
    ON
        (`pitm`.`poid` = `po`.`po_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `po`.`isratefixed` = 0 AND `po`.`is_suspense_stock` = 0 AND `rf`.`bill_status` = 1 AND `rf`.`is_approved` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    CONCAT(
        'RATE FIXING',
        IF(
            `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    IFNULL(`rc`.`po_id`, '') AS `po_id`,
    `rc`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    IF(
        `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    `rc`.`weight` AS `gross_wt`,
    `rc`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `rc`.`weight` AS `purewt`,
    `rc`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (
                `rc`.`rate_per_gram` - `rf`.`rate_fix_rate`
            ) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        `ret_po_rate_fix` `rf`
    LEFT JOIN `ret_supplier_rate_cut` `rc`
    ON
        (
            `rc`.`id_supplier_rate_cut` = `rf`.`id_approval_ratecut`
        )
    )
WHERE
    `rf`.`rate_fix_type` = 2 AND `rf`.`bill_status` = 1 AND `rf`.`is_approved` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    IFNULL(
        `pr`.`product_name`,
        'Product Unavailable'
    ) AS `category`,
    IFNULL(`po`.`po_id`, '') AS `po_id`,
    `met`.`id_metal` AS `id_metal`,
    IFNULL(`pr`.`product_name`, '') AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    IFNULL(
        `pr`.`product_name`,
        'Product Unavailable'
    ) AS `category`,
    IFNULL(`po`.`po_id`, '') AS `po_id`,
    `met`.`id_metal` AS `id_metal`,
    IFNULL(`pr`.`product_name`, '') AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    IFNULL(`pr`.`product_name`, 'RATE FIXING') AS `category`,
    IFNULL(`rf`.`po_id`, '') AS `po_id`,
    `rf`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
	CASE
        WHEN `rf`.`weight` = 0 AND `rf`.`amount_type` = 1 THEN 1
	WHEN `rf`.`weight` = 0 AND `rf`.`amount_type` = 2 THEN 2
 	WHEN `rf`.`weight` != 0 AND `rf`.`weight_type` = 1 THEN 1
	WHEN `rf`.`weight` != 0 AND `rf`.`weight_type` = 2 THEN 2
    END AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '100' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `rf`.`id_metal` AS `metal`,
    IF(
        `rf`.`conversion_type` = 2,
        CONCAT('(Unfix)', `rf`.`rate_per_gram`),
        `rf`.`rate_per_gram`
    ) AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        `ret_supplier_rate_cut` `rf`
    LEFT JOIN `ret_product_master` `pr`
    ON
        (`pr`.`pro_id` = `rf`.`id_product`)
    )
WHERE
    `rf`.`rate_cut_type` = 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `po_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    `pay`.`weight` AS `gross_wt`,
    `pay`.`net_wt` AS `net_wt`,
    `pay`.`pieces` AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `pay`.`pure_wt` AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `po_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`net_wt`, 0) AS `net_wt`,
    `pay`.`pieces` AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `pay`.`pure_wt` AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    `pay`.`po_id` AS `po_id`,
    `cat`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    `pay`.`naration` AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    (
        (
            `ret_crdr_note` `pay`
        LEFT JOIN(
            SELECT
                `ret_purchase_order_items`.`po_item_po_id` AS `po_item_po_id`,
                `ret_purchase_order_items`.`po_item_cat_id` AS `po_item_cat_id`
            FROM
                `ret_purchase_order_items`
            GROUP BY
                `ret_purchase_order_items`.`po_item_po_id`
        ) `po`
    ON
        (`pay`.`po_id` = `po`.`po_item_po_id`)
        )
    LEFT JOIN `ret_category` `cat`
    ON
        (
            `cat`.`id_ret_category` = `po`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`accountto` = 1 AND `pay`.`transamount` > 0 AND `pay`.`crdr_status` = 1
GROUP BY
    `pay`.`crdrid`
UNION ALL
SELECT
    'TDS' AS `category`,
    `po`.`po_id` AS `po_id`,
    `cat`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tds_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    `ret_purchase_order` `po`
                LEFT JOIN `ret_grn_entry` `grn`
                ON
                    (`grn`.`grn_id` = `po`.`po_grn_id`)
                )
            LEFT JOIN `ret_karigar` `kr`
            ON
                (
                    `kr`.`id_karigar` = `po`.`po_karigar_id`
                )
            )
        LEFT JOIN(
            SELECT
                `ret_purchase_order_items`.`po_item_po_id` AS `po_item_po_id`,
                `ret_purchase_order_items`.`po_item_cat_id` AS `po_item_cat_id`
            FROM
                `ret_purchase_order_items`
            GROUP BY
                `ret_purchase_order_items`.`po_item_po_id`
        ) `item`
    ON
        (`po`.`po_id` = `item`.`po_item_po_id`)
        )
    LEFT JOIN `ret_category` `cat`
    ON
        (
            `cat`.`id_ret_category` = `item`.`po_item_cat_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tds_tax_value` > 0
GROUP BY
    `po`.`po_id`
UNION ALL
SELECT
    'TCS' AS `category`,
    `po`.`po_id` AS `po_id`,
    '' AS `id_metal`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tcs_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            `ret_purchase_order` `po`
        LEFT JOIN `ret_grn_entry` `grn`
        ON
            (`grn`.`grn_id` = `po`.`po_grn_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tcs_tax_value` > 0
GROUP BY
    `po`.`po_id`
ORDER BY
    `unixtransdate`
DESC
    

    -- approval ledger --

DROP VIEW IF EXISTS `ret_view_supplier_approval_ledger`;

CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `ret_view_supplier_approval_ledger`  AS 
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `arc_staging_24_02_26`.`ret_purchase_order_items` `pitm`
                        LEFT JOIN `arc_staging_24_02_26`.`ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `arc_staging_24_02_26`.`ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `arc_staging_24_02_26`.`ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `arc_staging_24_02_26`.`ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `arc_staging_24_02_26`.`metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `arc_staging_24_02_26`.`ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` = 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    'PAYMENT' AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    2 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    `rf`.`id_metal` AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    `rf`.`rate_per_gram` AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        (
            `arc_staging_24_02_26`.`ret_supplier_rate_cut` `rf`
        LEFT JOIN `arc_staging_24_02_26`.`ret_purchase_order_items` `order`
        ON
            (`order`.`po_item_po_id` = `rf`.`po_id`)
        )
    LEFT JOIN `arc_staging_24_02_26`.`ret_category` `cate`
    ON
        (
            `cate`.`id_ret_category` = `order`.`po_item_cat_id`
        )
    )
WHERE
    `rf`.`rate_cut_type` = 1 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    IF(
        `rf`.`weight` = 0,
        'Bill Conv(Amount)',
        'Bill Conv(A to P)'
    ) AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    CASE
        WHEN `rf`.`weight` = 0 AND `rf`.`amount_type` = 1 THEN 2
	WHEN `rf`.`weight` = 0 AND `rf`.`amount_type` = 2 THEN 1
 	WHEN `rf`.`weight` != 0 AND `rf`.`weight_type` = 1 THEN 2
	WHEN `rf`.`weight` != 0 AND `rf`.`weight_type` = 2 THEN 1
    END AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    IF(`rf`.`weight` = 0, '', '100') AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    IF(
        `rf`.`charges_amount` > 0,
        `rf`.`charges_amount`,
        ''
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `rf`.`id_metal` AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    `rf`.`rate_per_gram` AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    `arc_staging_24_02_26`.`ret_supplier_rate_cut` `rf`
WHERE
    `rf`.`rate_cut_type` = 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `arc_staging_24_02_26`.`ret_purchase_return_items` `pret`
                                LEFT JOIN `arc_staging_24_02_26`.`ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `arc_staging_24_02_26`.`ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `arc_staging_24_02_26`.`ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `arc_staging_24_02_26`.`ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `arc_staging_24_02_26`.`ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `arc_staging_24_02_26`.`ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `arc_staging_24_02_26`.`ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `arc_staging_24_02_26`.`metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `arc_staging_24_02_26`.`ret_purchase_return_items` `pret`
                                LEFT JOIN `arc_staging_24_02_26`.`ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `arc_staging_24_02_26`.`ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `arc_staging_24_02_26`.`ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `arc_staging_24_02_26`.`ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `arc_staging_24_02_26`.`ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `arc_staging_24_02_26`.`ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `arc_staging_24_02_26`.`ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `arc_staging_24_02_26`.`metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `iss`.`met_issue_date` AS `trans_date`,
    `iss`.`met_issue_ref_id` AS `referenceno`,
    '/karigarmetalissue_acknowladgement/' AS `link`,
    `iitm`.`issue_met_parent_id` AS `print_id`,
    2 AS `trans_type`,
    `iitm`.`issue_met_parent_id` AS `trans_id`,
    IF(
        `cat`.`cat_type` = 2,
        0,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `gross_wt`,
    IF(
        `cat`.`cat_type` = 2,
        0,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `net_wt`,
    IFNULL(`iitm`.`issue_pcs`, 1) AS `no_of_pcs`,
    '100' AS `purchase_touch`,
    IF(
        `pr`.`stone_type` = 0,
        `iitm`.`issue_metal_pur_wt`,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `purewt`,
    `iss`.`met_issue_karid` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    `iitm`.`issue_cat_id` AS `catid`,
    `iitm`.`issu_met_pro_id` AS `proid`,
    2 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    IFNULL(`iss`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`iss`.`met_issue_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `arc_staging_24_02_26`.`ret_karigar_metal_issue_details` `iitm`
                        LEFT JOIN `arc_staging_24_02_26`.`ret_karigar_metal_issue` `iss`
                        ON
                            (
                                `iss`.`met_issue_id` = `iitm`.`issue_met_parent_id`
                            )
                        )
                    LEFT JOIN `arc_staging_24_02_26`.`ret_karigar` `kr`
                    ON
                        (
                            `kr`.`id_karigar` = `iss`.`met_issue_karid`
                        )
                    )
                LEFT JOIN `arc_staging_24_02_26`.`ret_category` `cat`
                ON
                    (
                        `cat`.`id_ret_category` = `iitm`.`issue_cat_id`
                    )
                )
            LEFT JOIN `arc_staging_24_02_26`.`metal` `met`
            ON
                (`met`.`id_metal` = `cat`.`id_metal`)
            )
        LEFT JOIN `arc_staging_24_02_26`.`ret_product_master` `pr`
        ON
            (
                `pr`.`pro_id` = `iitm`.`issu_met_pro_id`
            )
        )
    LEFT JOIN `arc_staging_24_02_26`.`ret_uom` `uom`
    ON
        (`uom`.`uom_id` = `iitm`.`issue_uom_id`)
    )
WHERE
    `iss`.`metalissue_type` = 2 AND `iss`.`bill_status` = 1
GROUP BY
    `iitm`.`issue_met_id`,
    `iitm`.`issue_cat_id`,
    `iitm`.`issu_met_pro_id`,
    `iitm`.`issue_met_parent_id`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `link`,
    '' AS `print_id`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `arc_staging_24_02_26`.`smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `link`,
    '' AS `print_id`,
    `pay`.`weight_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    IFNULL(`pay`.`weight`, 0) AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `arc_staging_24_02_26`.`smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    '/credit_debit_acknolodgement/' AS `link`,
    `pay`.`crdrid` AS `print_id`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    `pay`.`weight` AS `gross_wt`,
    `pay`.`weight` AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `pay`.`weight` AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `cate`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`naration`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    (
        (
            `arc_staging_24_02_26`.`ret_crdr_note` `pay`
        LEFT JOIN `arc_staging_24_02_26`.`ret_purchase_order_items` `order`
        ON
            (
                `order`.`po_item_po_id` = `pay`.`po_id`
            )
        )
    LEFT JOIN `arc_staging_24_02_26`.`ret_category` `cate`
    ON
        (
            `cate`.`id_ret_category` = `order`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`accountto` = 3 AND `pay`.`crdr_status` = 1 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`crdrid`
ORDER BY
    `unixtransdate`
DESC
    

    -- combined ledger --

DROP VIEW IF EXISTS `ret_view_smith_combined_ledger`;

CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `ret_view_smith_combined_ledger`  AS 

SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 1' AS `qry_type`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_purchase_order_items` `pitm`
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` = 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 1
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    'PAYMENT' AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    'type 2' AS `qry_type`,
    2 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    `rf`.`id_metal` AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    `rf`.`rate_per_gram` AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        `ret_supplier_rate_cut` `rf`
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `rf`.`po_id`)
    )
WHERE
    `rf`.`rate_cut_type` = 1 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    2 AS `ledger_type`,
    IF(
        `rf`.`weight` = 0,
        'Bill Conv(Amount)',
        'Bill Conv(A to P)'
    ) AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    'type 3' AS `qry_type`,
    CASE
        WHEN `rf`.`weight` = 0 AND `rf`.`amount_type` = 1 THEN 2
	WHEN `rf`.`weight` = 0 AND `rf`.`amount_type` = 2 THEN 1
 	WHEN `rf`.`weight` != 0 AND `rf`.`weight_type` = 1 THEN 2
	WHEN `rf`.`weight` != 0 AND `rf`.`weight_type` = 2 THEN 1
    END AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    IF(`rf`.`weight` = 0, '', '100') AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    IF(
        `rf`.`charges_amount` > 0,
        `rf`.`charges_amount`,
        ''
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `rf`.`id_metal` AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    `rf`.`rate_per_gram` AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        `ret_supplier_rate_cut` `rf`
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `rf`.`po_id`)
    )
WHERE
    `rf`.`rate_cut_type` = 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 4' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 5' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `iss`.`met_issue_date` AS `trans_date`,
    `iss`.`met_issue_ref_id` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/karigarmetalissue_acknowladgement/' AS `link`,
    `iitm`.`issue_met_parent_id` AS `print_id`,
    'type 6' AS `qry_type`,
    2 AS `trans_type`,
    `iitm`.`issue_met_parent_id` AS `trans_id`,
    IF(
        IFNULL(`uom`.`divided_by_value`, 0) = 0,
        `iitm`.`issue_metal_wt`,
        ROUND(
            `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
            3
        )
    ) AS `gross_wt`,
    IF(
        IFNULL(`uom`.`divided_by_value`, 0) = 0,
        `iitm`.`issue_metal_wt`,
        ROUND(
            `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
            3
        )
    ) AS `net_wt`,
    IFNULL(`iitm`.`issue_pcs`, 1) AS `no_of_pcs`,
    IFNULL(`iitm`.`touch`, '') AS `purchase_touch`,
    IF(
        `pr`.`stone_type` = 0,
        `iitm`.`issue_metal_pur_wt`,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `purewt`,
    `iss`.`met_issue_karid` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    `iitm`.`issue_cat_id` AS `catid`,
    `iitm`.`issu_met_pro_id` AS `proid`,
    2 AS `trans_screen_id`,
    `iitm`.`issue_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    IFNULL(`iss`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`iss`.`met_issue_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_karigar_metal_issue_details` `iitm`
                                LEFT JOIN `ret_karigar_metal_issue` `iss`
                                ON
                                    (
                                        `iss`.`met_issue_id` = `iitm`.`issue_met_parent_id`
                                    )
                                )
                            LEFT JOIN `ret_karigar` `kr`
                            ON
                                (
                                    `kr`.`id_karigar` = `iss`.`met_issue_karid`
                                )
                            )
                        LEFT JOIN `ret_category` `cat`
                        ON
                            (
                                `cat`.`id_ret_category` = `iitm`.`issue_cat_id`
                            )
                        )
                    LEFT JOIN `metal` `met`
                    ON
                        (`met`.`id_metal` = `cat`.`id_metal`)
                    )
                LEFT JOIN `ret_product_master` `pr`
                ON
                    (
                        `pr`.`pro_id` = `iitm`.`issu_met_pro_id`
                    )
                )
            LEFT JOIN `ret_uom` `uom`
            ON
                (`uom`.`uom_id` = `iitm`.`issue_uom_id`)
            )
        LEFT JOIN `ret_purchase_order` `po`
        ON
            (`po`.`po_id` = `iss`.`po_id`)
        )
    LEFT JOIN `ret_purchase_order_items` `poitm`
    ON
        (
            `poitm`.`po_item_id` = `iitm`.`po_item_id`
        )
    )
WHERE
    `iss`.`metalissue_type` = 2 AND `iss`.`bill_status` = 1
GROUP BY
    `iitm`.`issue_met_id`,
    `iitm`.`issue_cat_id`,
    `iitm`.`issu_met_pro_id`,
    `iitm`.`issue_met_parent_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 7' AS `qry_type`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    2 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 8' AS `qry_type`,
    `pay`.`weight_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    IFNULL(`pay`.`weight`, 0) AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    2 AS `ledger_type`,
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/credit_debit_acknolodgement/' AS `link`,
    `pay`.`crdrid` AS `print_id`,
    'type 9' AS `qry_type`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    IFNULL(`pay`.`weight`, 0) AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `cate`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`naration`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    (
        (
            (
                `ret_crdr_note` `pay`
            LEFT JOIN `ret_purchase_order` `po`
            ON
                (`po`.`po_id` = `pay`.`po_id`)
            )
        LEFT JOIN `ret_purchase_order_items` `order`
        ON
            (`order`.`po_item_po_id` = `po`.`po_id`)
        )
    LEFT JOIN `ret_category` `cate`
    ON
        (
            `cate`.`id_ret_category` = `order`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`accountto` = 3 AND `pay`.`weight` > 0 AND `pay`.`crdr_status` = 1
GROUP BY
    `pay`.`crdrid`
UNION ALL
SELECT
    1 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 10' AS `qry_type`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_purchase_order_items` `pitm`
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'PAYMENT' AS `category`,
    '' AS `product`,
    `pay`.`pay_create_on` AS `trans_date`,
    `pay`.`pay_refno` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/supplier_po_payment/paymentacknolodgement/' AS `link`,
    `pay`.`pay_id` AS `print_id`,
    'type 11' AS `qry_type`,
    2 AS `trans_type`,
    `pay`.`pay_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`pay_sup_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po_bill`.`bill_amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    `cate`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    `pay`.`pay_narration` AS `narration`,
    UNIX_TIMESTAMP(`pay`.`pay_create_on`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        `ret_po_payment` `pay`
                    LEFT JOIN `ret_po_payment_detail` `pd`
                    ON
                        (`pd`.`pay_id` = `pay`.`pay_id`)
                    )
                LEFT JOIN `ret_po_bill_payment_details` `po_bill`
                ON
                    (`po_bill`.`pay_id` = `pay`.`pay_id`)
                )
            LEFT JOIN `ret_purchase_order` `po`
            ON
                (`po`.`po_id` = `po_bill`.`po_id`)
            )
        LEFT JOIN(
            SELECT DISTINCT
                `ret_purchase_order_items`.`po_item_po_id` AS `po_item_po_id`,
                `ret_purchase_order_items`.`po_item_cat_id` AS `po_item_cat_id`
            FROM
                `ret_purchase_order_items`
            LIMIT 1
        ) `order`
    ON
        (`order`.`po_item_po_id` = `po`.`po_id`)
        )
    LEFT JOIN `ret_category` `cate`
    ON
        (
            `cate`.`id_ret_category` = `order`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`pay_status` = 1 AND `pay`.`bill_type` = 1
GROUP BY
    `po_bill`.`po_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    CONCAT(
        'RATE FIXING',
        IF(
            `pitm`.`rate` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 12' AS `qry_type`,
    IF(
        `pitm`.`rate` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    '' AS `gross_wt`,
    '' AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    '' AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (`pitm`.`rate` - `rf`.`rate_fix_rate`) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    `ret_po_rate_fix` `rf`
                LEFT JOIN `ret_purchase_order` `po`
                ON
                    (
                        `po`.`po_id` = `rf`.`rate_fix_po_item_id`
                    )
                )
            LEFT JOIN `ret_grn_entry` `grn`
            ON
                (`grn`.`grn_id` = `po`.`po_grn_id`)
            )
        LEFT JOIN(
            SELECT
                `pitm`.`po_item_po_id` AS `poid`,
                `pitm`.`fix_rate_per_grm` AS `rate`
            FROM
                `ret_purchase_order_items` `pitm`
            GROUP BY
                `pitm`.`po_item_po_id`
        ) `pitm`
    ON
        (`pitm`.`poid` = `po`.`po_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `po`.`isratefixed` = 0 AND `po`.`is_suspense_stock` = 0 AND `rf`.`bill_status` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    CONCAT(
        'RATE FIXING',
        IF(
            `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 13' AS `qry_type`,
    IF(
        `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    '' AS `gross_wt`,
    '' AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    '' AS `purewt`,
    `rc`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (
                `rc`.`rate_per_gram` - `rf`.`rate_fix_rate`
            ) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    `rc`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        (
            `ret_po_rate_fix` `rf`
        LEFT JOIN `ret_supplier_rate_cut` `rc`
        ON
            (
                `rc`.`id_supplier_rate_cut` = `rf`.`id_approval_ratecut`
            )
        )
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `rc`.`po_id`)
    )
WHERE
    `rf`.`rate_fix_type` = 2 AND `rf`.`bill_status` = 1 AND `rc`.`conversion_type` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 14' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 15' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    IFNULL(`pr`.`product_name`, 'PURE') AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    'type 16' AS `qry_type`,
    1 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '100' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `rf`.`id_metal` AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    IF(
        `rf`.`conversion_type` = 2,
        CONCAT(`rf`.`rate_per_gram`, '(Unfix)'),
        `rf`.`rate_per_gram`
    ) AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        (
            `ret_supplier_rate_cut` `rf`
        LEFT JOIN `ret_product_master` `pr`
        ON
            (`pr`.`pro_id` = `rf`.`id_product`)
        )
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `rf`.`po_id`)
    )
WHERE
    `rf`.`rate_cut_type` = 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 17' AS `qry_type`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 18' AS `qry_type`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    1 AS `ledger_type`,
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/credit_debit_acknolodgement/' AS `link`,
    `pay`.`crdrid` AS `print_id`,
    'type 19' AS `qry_type`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `cate`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    `pay`.`naration` AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    (
        (
            (
                `ret_crdr_note` `pay`
            LEFT JOIN `ret_purchase_order` `po`
            ON
                (`po`.`po_id` = `pay`.`po_id`)
            )
        LEFT JOIN `ret_purchase_order_items` `order`
        ON
            (`order`.`po_item_po_id` = `po`.`po_id`)
        )
    LEFT JOIN `ret_category` `cate`
    ON
        (
            `cate`.`id_ret_category` = `order`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`accountto` = 1 AND `pay`.`transamount` > 0 AND `pay`.`crdr_status` = 1
GROUP BY
    `pay`.`crdrid`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'TDS' AS `category`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 20' AS `qry_type`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tds_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            `ret_purchase_order` `po`
        LEFT JOIN `ret_grn_entry` `grn`
        ON
            (`grn`.`grn_id` = `po`.`po_grn_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tds_tax_value` > 0
GROUP BY
    `po`.`po_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'TCS' AS `category`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 21' AS `qry_type`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tcs_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            `ret_purchase_order` `po`
        LEFT JOIN `ret_grn_entry` `grn`
        ON
            (`grn`.`grn_id` = `po`.`po_grn_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tcs_tax_value` > 0
GROUP BY
    `po`.`po_id`
ORDER BY
    `unixtransdate`
DESC;
    
-- ruthramoorthi --(3-3-26) supplier ledger
DROP VIEW IF EXISTS `ret_view_supplier_ledger`;

CREATE VIEW `ret_view_supplier_ledger`  AS 
SELECT
    IFNULL(
        `pr`.`product_name`,
        'Product Unavailable'
    ) AS `category`,
    `po`.`po_id` AS `po_id`,
    `met`.`id_metal` AS `id_metal`,
    IFNULL(`pr`.`product_name`, '') AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_purchase_order_items` `pitm`
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    'PAYMENT' AS `category`,
    IFNULL(`bill`.`po_id`, '') AS `po_id`,
    `cat`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `pay`.`pay_create_on` AS `trans_date`,
    `pay`.`pay_refno` AS `referenceno`,
    2 AS `trans_type`,
    `pay`.`pay_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`pay_sup_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`bill`.`bill_amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`pay`.`pay_create_on`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    `ret_po_payment` `pay`
                LEFT JOIN `ret_po_payment_detail` `pd`
                ON
                    (`pd`.`pay_id` = `pay`.`pay_id`)
                )
            LEFT JOIN `ret_po_bill_payment_details` `bill`
            ON
                (`bill`.`pay_id` = `pay`.`pay_id`)
            )
        LEFT JOIN(
            SELECT
                `ret_purchase_order_items`.`po_item_po_id` AS `po_item_po_id`,
                `ret_purchase_order_items`.`po_item_cat_id` AS `po_item_cat_id`
            FROM
                `ret_purchase_order_items`
            GROUP BY
                `ret_purchase_order_items`.`po_item_po_id`
        ) `po`
    ON
        (`bill`.`po_id` = `po`.`po_item_po_id`)
        )
    LEFT JOIN `ret_category` `cat`
    ON
        (
            `cat`.`id_ret_category` = `po`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`pay_status` = 1 AND `pay`.`bill_type` = 1
GROUP BY
    `bill`.`po_id`
UNION ALL
SELECT
    CONCAT(
        'RATE FIXING',
        IF(
            `pitm`.`rate` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    IFNULL(`po`.`po_id`, '') AS `po_id`,
    `pitm`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    IF(
        `pitm`.`rate` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    '' AS `gross_wt`,
    '' AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    '' AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (`pitm`.`rate` - `rf`.`rate_fix_rate`) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        (
            (
                `ret_po_rate_fix` `rf`
            LEFT JOIN `ret_purchase_order` `po`
            ON
                (
                    `po`.`po_id` = `rf`.`rate_fix_po_item_id`
                )
            )
        LEFT JOIN(
            SELECT
                `pitm`.`po_item_po_id` AS `poid`,
                `pitm`.`fix_rate_per_grm` AS `rate`,
                `cate`.`id_metal` AS `id_metal`
            FROM
                (
                    `ret_purchase_order_items` `pitm`
                LEFT JOIN `ret_category` `cate`
                ON
                    (
                        `cate`.`id_ret_category` = `pitm`.`po_item_cat_id`
                    )
                )
            GROUP BY
                `pitm`.`po_item_po_id`
        ) `pitm`
    ON
        (`pitm`.`poid` = `po`.`po_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `po`.`isratefixed` = 0 AND `po`.`is_suspense_stock` = 0 AND `rf`.`bill_status` = 1 AND `rf`.`is_approved` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    CONCAT(
        'RATE FIXING',
        IF(
            `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    IFNULL(`rc`.`po_id`, '') AS `po_id`,
    `rc`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    IF(
        `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    `rc`.`weight` AS `gross_wt`,
    `rc`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `rc`.`weight` AS `purewt`,
    `rc`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (
                `rc`.`rate_per_gram` - `rf`.`rate_fix_rate`
            ) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        `ret_po_rate_fix` `rf`
    LEFT JOIN `ret_supplier_rate_cut` `rc`
    ON
        (
            `rc`.`id_supplier_rate_cut` = `rf`.`id_approval_ratecut`
        )
    )
WHERE
    `rf`.`rate_fix_type` = 2 AND `rf`.`bill_status` = 1 AND `rf`.`is_approved` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    IFNULL(
        `pr`.`product_name`,
        'Product Unavailable'
    ) AS `category`,
    IFNULL(`po`.`po_id`, '') AS `po_id`,
    `met`.`id_metal` AS `id_metal`,
    IFNULL(`pr`.`product_name`, '') AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    IFNULL(
        `pr`.`product_name`,
        'Product Unavailable'
    ) AS `category`,
    IFNULL(`po`.`po_id`, '') AS `po_id`,
    `met`.`id_metal` AS `id_metal`,
    IFNULL(`pr`.`product_name`, '') AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    IFNULL(`pr`.`product_name`, 'RATE FIXING') AS `category`,
    IFNULL(`rf`.`po_id`, '') AS `po_id`,
    `rf`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    CASE WHEN `rf`.`weight` = 0 AND `rf`.`amount_type` = 1 THEN 1 WHEN `rf`.`weight` = 0 AND `rf`.`amount_type` = 2 THEN 2 WHEN `rf`.`weight` <> 0 AND `rf`.`weight_type` = 1 THEN 1 WHEN `rf`.`weight` <> 0 AND `rf`.`weight_type` = 2 THEN 2
END AS `trans_type`,
`rf`.`id_supplier_rate_cut` AS `trans_id`,
`rf`.`weight` AS `gross_wt`,
`rf`.`weight` AS `net_wt`,
'' AS `no_of_pcs`,
'100' AS `purchase_touch`,
`rf`.`weight` AS `purewt`,
`rf`.`id_karigar` AS `customer_id`,
1 AS `trans_rec_type`,
`rf`.`amount` AS `trans_amount`,
'' AS `catid`,
'' AS `proid`,
1 AS `trans_screen_id`,
`rf`.`id_metal` AS `metal`,
IF(
    `rf`.`conversion_type` = 2,
    CONCAT('(Unfix)', `rf`.`rate_per_gram`),
    `rf`.`rate_per_gram`
) AS `rate`,
IFNULL(`rf`.`narration`, '') AS `narration`,
UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        `ret_supplier_rate_cut` `rf`
    LEFT JOIN `ret_product_master` `pr`
    ON
        (`pr`.`pro_id` = `rf`.`id_product`)
    )
WHERE
    `rf`.`rate_cut_type` = 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `po_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    `pay`.`weight` AS `gross_wt`,
    `pay`.`net_wt` AS `net_wt`,
    `pay`.`pieces` AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `pay`.`pure_wt` AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `po_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`net_wt`, 0) AS `net_wt`,
    `pay`.`pieces` AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `pay`.`pure_wt` AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    `pay`.`po_id` AS `po_id`,
    `cat`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    `pay`.`naration` AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    (
        (
            `ret_crdr_note` `pay`
        LEFT JOIN(
            SELECT
                `ret_purchase_order_items`.`po_item_po_id` AS `po_item_po_id`,
                `ret_purchase_order_items`.`po_item_cat_id` AS `po_item_cat_id`
            FROM
                `ret_purchase_order_items`
            GROUP BY
                `ret_purchase_order_items`.`po_item_po_id`
        ) `po`
    ON
        (`pay`.`po_id` = `po`.`po_item_po_id`)
        )
    LEFT JOIN `ret_category` `cat`
    ON
        (
            `cat`.`id_ret_category` = `po`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`accountto` = 1 AND `pay`.`transamount` > 0 AND `pay`.`crdr_status` = 1
GROUP BY
    `pay`.`crdrid`
UNION ALL
SELECT
    'TDS' AS `category`,
    `po`.`po_id` AS `po_id`,
    `cat`.`id_metal` AS `id_metal`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tds_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    `ret_purchase_order` `po`
                LEFT JOIN `ret_grn_entry` `grn`
                ON
                    (`grn`.`grn_id` = `po`.`po_grn_id`)
                )
            LEFT JOIN `ret_karigar` `kr`
            ON
                (
                    `kr`.`id_karigar` = `po`.`po_karigar_id`
                )
            )
        LEFT JOIN(
            SELECT
                `ret_purchase_order_items`.`po_item_po_id` AS `po_item_po_id`,
                `ret_purchase_order_items`.`po_item_cat_id` AS `po_item_cat_id`
            FROM
                `ret_purchase_order_items`
            GROUP BY
                `ret_purchase_order_items`.`po_item_po_id`
        ) `item`
    ON
        (`po`.`po_id` = `item`.`po_item_po_id`)
        )
    LEFT JOIN `ret_category` `cat`
    ON
        (
            `cat`.`id_ret_category` = `item`.`po_item_cat_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tds_tax_value` > 0
GROUP BY
    `po`.`po_id`
UNION ALL
SELECT
    'TCS' AS `category`,
    `po`.`po_id` AS `po_id`,
    '' AS `id_metal`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tcs_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            `ret_purchase_order` `po`
        LEFT JOIN `ret_grn_entry` `grn`
        ON
            (`grn`.`grn_id` = `po`.`po_grn_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tcs_tax_value` > 0
GROUP BY
    `po`.`po_id`
ORDER BY
    `unixtransdate`
DESC;

-- approval ledger --

DROP VIEW IF EXISTS `ret_view_supplier_approval_ledger`;

CREATE VIEW `ret_view_supplier_approval_ledger`  AS 
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_purchase_order_items` `pitm`
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` = 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    'PAYMENT' AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    2 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    `rf`.`id_metal` AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    `rf`.`rate_per_gram` AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        (
            `ret_supplier_rate_cut` `rf`
        LEFT JOIN `ret_purchase_order_items` `order`
        ON
            (`order`.`po_item_po_id` = `rf`.`po_id`)
        )
    LEFT JOIN `ret_category` `cate`
    ON
        (
            `cate`.`id_ret_category` = `order`.`po_item_cat_id`
        )
    )
WHERE
    `rf`.`rate_cut_type` = 1 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    IF(
        `rf`.`weight` = 0,
        'Bill Conv(Amount)',
        'Bill Conv(A to P)'
    ) AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    CASE WHEN `rf`.`weight` <> 0 AND `rf`.`weight_type` = 1 AND `rf`.`rate_cut_type` = 2 THEN 2 WHEN `rf`.`weight` <> 0 AND `rf`.`weight_type` = 2 AND `rf`.`rate_cut_type` = 2 THEN 1 WHEN `rf`.`weight` <> 0 AND `rf`.`rate_cut_type` = 3 THEN 3
END AS `trans_type`,
`rf`.`id_supplier_rate_cut` AS `trans_id`,
`rf`.`weight` AS `gross_wt`,
`rf`.`weight` AS `net_wt`,
'' AS `no_of_pcs`,
IF(`rf`.`weight` = 0, '', '100') AS `purchase_touch`,
`rf`.`weight` AS `purewt`,
`rf`.`id_karigar` AS `customer_id`,
1 AS `trans_rec_type`,
IF(
    `rf`.`charges_amount` > 0,
    `rf`.`charges_amount`,
    ''
) AS `trans_amount`,
'' AS `catid`,
'' AS `proid`,
1 AS `trans_screen_id`,
`rf`.`id_metal` AS `id_metal`,
`rf`.`id_metal` AS `metal`,
`rf`.`rate_per_gram` AS `rate`,
IFNULL(`rf`.`narration`, '') AS `narration`,
UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    `ret_supplier_rate_cut` `rf`
WHERE
    `rf`.`rate_cut_type` >= 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `iss`.`met_issue_date` AS `trans_date`,
    `iss`.`met_issue_ref_id` AS `referenceno`,
    '/karigarmetalissue_acknowladgement/' AS `link`,
    `iitm`.`issue_met_parent_id` AS `print_id`,
    2 AS `trans_type`,
    `iitm`.`issue_met_parent_id` AS `trans_id`,
    IF(
        `cat`.`cat_type` = 2,
        0,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `gross_wt`,
    IF(
        `cat`.`cat_type` = 2,
        0,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `net_wt`,
    IFNULL(`iitm`.`issue_pcs`, 1) AS `no_of_pcs`,
    '100' AS `purchase_touch`,
    IF(
        `pr`.`stone_type` = 0,
        `iitm`.`issue_metal_pur_wt`,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `purewt`,
    `iss`.`met_issue_karid` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    `iitm`.`issue_cat_id` AS `catid`,
    `iitm`.`issu_met_pro_id` AS `proid`,
    2 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    IFNULL(`iss`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`iss`.`met_issue_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_karigar_metal_issue_details` `iitm`
                        LEFT JOIN `ret_karigar_metal_issue` `iss`
                        ON
                            (
                                `iss`.`met_issue_id` = `iitm`.`issue_met_parent_id`
                            )
                        )
                    LEFT JOIN `ret_karigar` `kr`
                    ON
                        (
                            `kr`.`id_karigar` = `iss`.`met_issue_karid`
                        )
                    )
                LEFT JOIN `ret_category` `cat`
                ON
                    (
                        `cat`.`id_ret_category` = `iitm`.`issue_cat_id`
                    )
                )
            LEFT JOIN `metal` `met`
            ON
                (`met`.`id_metal` = `cat`.`id_metal`)
            )
        LEFT JOIN `ret_product_master` `pr`
        ON
            (
                `pr`.`pro_id` = `iitm`.`issu_met_pro_id`
            )
        )
    LEFT JOIN `ret_uom` `uom`
    ON
        (`uom`.`uom_id` = `iitm`.`issue_uom_id`)
    )
WHERE
    `iss`.`metalissue_type` = 2 AND `iss`.`bill_status` = 1
GROUP BY
    `iitm`.`issue_met_id`,
    `iitm`.`issue_cat_id`,
    `iitm`.`issu_met_pro_id`,
    `iitm`.`issue_met_parent_id`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `link`,
    '' AS `print_id`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `link`,
    '' AS `print_id`,
    `pay`.`weight_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    IFNULL(`pay`.`weight`, 0) AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    '/credit_debit_acknolodgement/' AS `link`,
    `pay`.`crdrid` AS `print_id`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    `pay`.`weight` AS `gross_wt`,
    `pay`.`weight` AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `pay`.`weight` AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `cate`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`naration`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    (
        (
            `ret_crdr_note` `pay`
        LEFT JOIN `ret_purchase_order_items` `order`
        ON
            (
                `order`.`po_item_po_id` = `pay`.`po_id`
            )
        )
    LEFT JOIN `ret_category` `cate`
    ON
        (
            `cate`.`id_ret_category` = `order`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`accountto` = 3 AND `pay`.`crdr_status` = 1 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`crdrid`
ORDER BY
    `unixtransdate`
DESC;

-- combined ledger --


Folder highlights
SQL views define ledger records for supplier approvals, payments, rate fixings, and purchase returns, unifying transaction types.

DROP VIEW IF EXISTS `ret_view_smith_combined_ledger`;

CREATE VIEW `ret_view_smith_combined_ledger`  AS 
SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 1' AS `qry_type`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_purchase_order_items` `pitm`
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` = 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 1
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    'PAYMENT' AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    'type 2' AS `qry_type`,
    2 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    `rf`.`id_metal` AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    `rf`.`rate_per_gram` AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        `ret_supplier_rate_cut` `rf`
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `rf`.`po_id`)
    )
WHERE
    `rf`.`rate_cut_type` = 1 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    2 AS `ledger_type`,
    IF(
        `rf`.`weight` = 0,
        'Bill Conv(Amount)',
        'Bill Conv(A to P)'
    ) AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    'type 3' AS `qry_type`,
    CASE WHEN `rf`.`weight` <> 0 AND `rf`.`weight_type` = 1 AND `rf`.`rate_cut_type` = 2 THEN 2 WHEN `rf`.`weight` <> 0 AND `rf`.`weight_type` = 2 AND `rf`.`rate_cut_type` = 2 THEN 1 WHEN `rf`.`weight` <> 0 AND `rf`.`rate_cut_type` = 3 THEN 3
END AS `trans_type`,
`rf`.`id_supplier_rate_cut` AS `trans_id`,
`rf`.`weight` AS `gross_wt`,
`rf`.`weight` AS `net_wt`,
'' AS `no_of_pcs`,
IF(`rf`.`weight` = 0, '', '100') AS `purchase_touch`,
`rf`.`weight` AS `purewt`,
`rf`.`id_karigar` AS `customer_id`,
1 AS `trans_rec_type`,
IF(
    `rf`.`charges_amount` > 0,
    `rf`.`charges_amount`,
    ''
) AS `trans_amount`,
'' AS `catid`,
'' AS `proid`,
1 AS `trans_screen_id`,
`rf`.`id_metal` AS `id_metal`,
`rf`.`id_metal` AS `metal`,
`rf`.`rate_per_gram` AS `rate`,
IFNULL(`rf`.`narration`, '') AS `narration`,
UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        `ret_supplier_rate_cut` `rf`
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `rf`.`po_id`)
    )
WHERE
    `rf`.`rate_cut_type` >= 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 4' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 5' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `iss`.`met_issue_date` AS `trans_date`,
    `iss`.`met_issue_ref_id` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/karigarmetalissue_acknowladgement/' AS `link`,
    `iitm`.`issue_met_parent_id` AS `print_id`,
    'type 6' AS `qry_type`,
    2 AS `trans_type`,
    `iitm`.`issue_met_parent_id` AS `trans_id`,
    IF(
        IFNULL(`uom`.`divided_by_value`, 0) = 0,
        `iitm`.`issue_metal_wt`,
        ROUND(
            `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
            3
        )
    ) AS `gross_wt`,
    IF(
        IFNULL(`uom`.`divided_by_value`, 0) = 0,
        `iitm`.`issue_metal_wt`,
        ROUND(
            `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
            3
        )
    ) AS `net_wt`,
    IFNULL(`iitm`.`issue_pcs`, 1) AS `no_of_pcs`,
    IFNULL(`iitm`.`touch`, '') AS `purchase_touch`,
    IF(
        `pr`.`stone_type` = 0,
        `iitm`.`issue_metal_pur_wt`,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `purewt`,
    `iss`.`met_issue_karid` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    `iitm`.`issue_cat_id` AS `catid`,
    `iitm`.`issu_met_pro_id` AS `proid`,
    2 AS `trans_screen_id`,
    `iitm`.`issue_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    IFNULL(`iss`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`iss`.`met_issue_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_karigar_metal_issue_details` `iitm`
                                LEFT JOIN `ret_karigar_metal_issue` `iss`
                                ON
                                    (
                                        `iss`.`met_issue_id` = `iitm`.`issue_met_parent_id`
                                    )
                                )
                            LEFT JOIN `ret_karigar` `kr`
                            ON
                                (
                                    `kr`.`id_karigar` = `iss`.`met_issue_karid`
                                )
                            )
                        LEFT JOIN `ret_category` `cat`
                        ON
                            (
                                `cat`.`id_ret_category` = `iitm`.`issue_cat_id`
                            )
                        )
                    LEFT JOIN `metal` `met`
                    ON
                        (`met`.`id_metal` = `cat`.`id_metal`)
                    )
                LEFT JOIN `ret_product_master` `pr`
                ON
                    (
                        `pr`.`pro_id` = `iitm`.`issu_met_pro_id`
                    )
                )
            LEFT JOIN `ret_uom` `uom`
            ON
                (`uom`.`uom_id` = `iitm`.`issue_uom_id`)
            )
        LEFT JOIN `ret_purchase_order` `po`
        ON
            (`po`.`po_id` = `iss`.`po_id`)
        )
    LEFT JOIN `ret_purchase_order_items` `poitm`
    ON
        (
            `poitm`.`po_item_id` = `iitm`.`po_item_id`
        )
    )
WHERE
    `iss`.`metalissue_type` = 2 AND `iss`.`bill_status` = 1
GROUP BY
    `iitm`.`issue_met_id`,
    `iitm`.`issue_cat_id`,
    `iitm`.`issu_met_pro_id`,
    `iitm`.`issue_met_parent_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 7' AS `qry_type`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    2 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 8' AS `qry_type`,
    `pay`.`weight_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    IFNULL(`pay`.`weight`, 0) AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    2 AS `ledger_type`,
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/credit_debit_acknolodgement/' AS `link`,
    `pay`.`crdrid` AS `print_id`,
    'type 9' AS `qry_type`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    IFNULL(`pay`.`weight`, 0) AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `cate`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`naration`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    (
        (
            (
                `ret_crdr_note` `pay`
            LEFT JOIN `ret_purchase_order` `po`
            ON
                (`po`.`po_id` = `pay`.`po_id`)
            )
        LEFT JOIN `ret_purchase_order_items` `order`
        ON
            (`order`.`po_item_po_id` = `po`.`po_id`)
        )
    LEFT JOIN `ret_category` `cate`
    ON
        (
            `cate`.`id_ret_category` = `order`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`accountto` = 3 AND `pay`.`weight` > 0 AND `pay`.`crdr_status` = 1
GROUP BY
    `pay`.`crdrid`
UNION ALL
SELECT
    1 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 10' AS `qry_type`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_purchase_order_items` `pitm`
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'PAYMENT' AS `category`,
    '' AS `product`,
    `pay`.`pay_create_on` AS `trans_date`,
    `pay`.`pay_refno` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/supplier_po_payment/paymentacknolodgement/' AS `link`,
    `pay`.`pay_id` AS `print_id`,
    'type 11' AS `qry_type`,
    2 AS `trans_type`,
    `pay`.`pay_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`pay_sup_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po_bill`.`bill_amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    `cate`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    `pay`.`pay_narration` AS `narration`,
    UNIX_TIMESTAMP(`pay`.`pay_create_on`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        `ret_po_payment` `pay`
                    LEFT JOIN `ret_po_payment_detail` `pd`
                    ON
                        (`pd`.`pay_id` = `pay`.`pay_id`)
                    )
                LEFT JOIN `ret_po_bill_payment_details` `po_bill`
                ON
                    (`po_bill`.`pay_id` = `pay`.`pay_id`)
                )
            LEFT JOIN `ret_purchase_order` `po`
            ON
                (`po`.`po_id` = `po_bill`.`po_id`)
            )
        LEFT JOIN(
            SELECT DISTINCT
                `ret_purchase_order_items`.`po_item_po_id` AS `po_item_po_id`,
                `ret_purchase_order_items`.`po_item_cat_id` AS `po_item_cat_id`
            FROM
                `ret_purchase_order_items`
            LIMIT 1
        ) `order`
    ON
        (`order`.`po_item_po_id` = `po`.`po_id`)
        )
    LEFT JOIN `ret_category` `cate`
    ON
        (
            `cate`.`id_ret_category` = `order`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`pay_status` = 1 AND `pay`.`bill_type` = 1
GROUP BY
    `po_bill`.`po_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    CONCAT(
        'RATE FIXING',
        IF(
            `pitm`.`rate` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 12' AS `qry_type`,
    IF(
        `pitm`.`rate` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    '' AS `gross_wt`,
    '' AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    '' AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (`pitm`.`rate` - `rf`.`rate_fix_rate`) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    `ret_po_rate_fix` `rf`
                LEFT JOIN `ret_purchase_order` `po`
                ON
                    (
                        `po`.`po_id` = `rf`.`rate_fix_po_item_id`
                    )
                )
            LEFT JOIN `ret_grn_entry` `grn`
            ON
                (`grn`.`grn_id` = `po`.`po_grn_id`)
            )
        LEFT JOIN(
            SELECT
                `pitm`.`po_item_po_id` AS `poid`,
                `pitm`.`fix_rate_per_grm` AS `rate`
            FROM
                `ret_purchase_order_items` `pitm`
            GROUP BY
                `pitm`.`po_item_po_id`
        ) `pitm`
    ON
        (`pitm`.`poid` = `po`.`po_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `po`.`isratefixed` = 0 AND `po`.`is_suspense_stock` = 0 AND `rf`.`bill_status` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    CONCAT(
        'RATE FIXING',
        IF(
            `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 13' AS `qry_type`,
    IF(
        `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    '' AS `gross_wt`,
    '' AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    '' AS `purewt`,
    `rc`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (
                `rc`.`rate_per_gram` - `rf`.`rate_fix_rate`
            ) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    `rc`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        (
            `ret_po_rate_fix` `rf`
        LEFT JOIN `ret_supplier_rate_cut` `rc`
        ON
            (
                `rc`.`id_supplier_rate_cut` = `rf`.`id_approval_ratecut`
            )
        )
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `rc`.`po_id`)
    )
WHERE
    `rf`.`rate_fix_type` = 2 AND `rf`.`bill_status` = 1 AND `rc`.`conversion_type` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 14' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 15' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    IFNULL(`pr`.`product_name`, 'PURE') AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    'type 16' AS `qry_type`,
    CASE WHEN `rf`.`weight` = 0 AND `rf`.`amount_type` = 1 THEN 1 WHEN `rf`.`weight` = 0 AND `rf`.`amount_type` = 2 THEN 2 WHEN `rf`.`weight` <> 0 AND `rf`.`weight_type` = 1 THEN 1 WHEN `rf`.`weight` <> 0 AND `rf`.`weight_type` = 2 THEN 2
    END AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '100' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `rf`.`id_metal` AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    IF(
        `rf`.`conversion_type` = 2,
        CONCAT(`rf`.`rate_per_gram`, '(Unfix)'),
        `rf`.`rate_per_gram`
    ) AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        (
            `ret_supplier_rate_cut` `rf`
        LEFT JOIN `ret_product_master` `pr`
        ON
            (`pr`.`pro_id` = `rf`.`id_product`)
        )
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `rf`.`po_id`)
    )
WHERE
    `rf`.`rate_cut_type` >= 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 17' AS `qry_type`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 18' AS `qry_type`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    1 AS `ledger_type`,
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/credit_debit_acknolodgement/' AS `link`,
    `pay`.`crdrid` AS `print_id`,
    'type 19' AS `qry_type`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `cate`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    `pay`.`naration` AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    (
        (
            (
                `ret_crdr_note` `pay`
            LEFT JOIN `ret_purchase_order` `po`
            ON
                (`po`.`po_id` = `pay`.`po_id`)
            )
        LEFT JOIN `ret_purchase_order_items` `order`
        ON
            (`order`.`po_item_po_id` = `po`.`po_id`)
        )
    LEFT JOIN `ret_category` `cate`
    ON
        (
            `cate`.`id_ret_category` = `order`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`accountto` = 1 AND `pay`.`transamount` > 0 AND `pay`.`crdr_status` = 1
GROUP BY
    `pay`.`crdrid`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'TDS' AS `category`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 20' AS `qry_type`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tds_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            `ret_purchase_order` `po`
        LEFT JOIN `ret_grn_entry` `grn`
        ON
            (`grn`.`grn_id` = `po`.`po_grn_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tds_tax_value` > 0
GROUP BY
    `po`.`po_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'TCS' AS `category`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 21' AS `qry_type`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tcs_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            `ret_purchase_order` `po`
        LEFT JOIN `ret_grn_entry` `grn`
        ON
            (`grn`.`grn_id` = `po`.`po_grn_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tcs_tax_value` > 0
GROUP BY
    `po`.`po_id`
ORDER BY
    `unixtransdate`
DESC;

ALTER TABLE ret_supplier_rate_cut
  ADD COLUMN amount_type TINYINT(1) DEFAULT NULL COMMENT '1=CR, 2=DR' AFTER igst_cost,
  ADD COLUMN weight_type TINYINT(1) DEFAULT NULL COMMENT '1=CR, 2=DR' AFTER amount_type;

-- Rollback SQL (if needed):
-- ALTER TABLE ret_supplier_rate_cut DROP COLUMN amount_type, DROP COLUMN weight_type;

ALTER TABLE ret_supplier_rate_cut CHANGE amount_type amount_type TINYINT(1) NULL DEFAULT '1' COMMENT '1=CR, 2=DR';
ALTER TABLE ret_supplier_rate_cut CHANGE weight_type weight_type TINYINT(1) NULL DEFAULT '1' COMMENT '1=CR, 2=DR';

-- ruthramoorthi -- combined ledger 

SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 1' AS `qry_type`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_purchase_order_items` `pitm`
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` = 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 1
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    'PAYMENT' AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    'type 2' AS `qry_type`,
    2 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    `rf`.`id_metal` AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    `rf`.`rate_per_gram` AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        `ret_supplier_rate_cut` `rf`
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `rf`.`po_id`)
    )
WHERE
    `rf`.`rate_cut_type` = 1 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    2 AS `ledger_type`,
    IF(
        `rf`.`weight` = 0,
        'Bill Conv(Amount)',
        'Bill Conv(A to P)'
    ) AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    'type 3' AS `qry_type`,
    CASE WHEN `rf`.`weight` = 0 AND `rf`.`amount_type` = 1 THEN 2 WHEN `rf`.`weight` = 0 AND `rf`.`amount_type` = 2 THEN 1 WHEN `rf`.`weight` <> 0 AND `rf`.`weight_type` = 1 THEN 2 WHEN `rf`.`weight` <> 0 AND `rf`.`weight_type` = 2 THEN 1
END AS `trans_type`,
`rf`.`id_supplier_rate_cut` AS `trans_id`,
`rf`.`weight` AS `gross_wt`,
`rf`.`weight` AS `net_wt`,
'' AS `no_of_pcs`,
IF(`rf`.`weight` = 0, '', '100') AS `purchase_touch`,
`rf`.`weight` AS `purewt`,
`rf`.`id_karigar` AS `customer_id`,
1 AS `trans_rec_type`,
IF(
    `rf`.`charges_amount` > 0,
    `rf`.`charges_amount`,
    ''
) AS `trans_amount`,
'' AS `catid`,
'' AS `proid`,
1 AS `trans_screen_id`,
`rf`.`id_metal` AS `id_metal`,
`rf`.`id_metal` AS `metal`,
`rf`.`rate_per_gram` AS `rate`,
IFNULL(`rf`.`narration`, '') AS `narration`,
UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        `ret_supplier_rate_cut` `rf`
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `rf`.`po_id`)
    )
WHERE
    `rf`.`rate_cut_type` = 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 4' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 5' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `iss`.`met_issue_date` AS `trans_date`,
    `iss`.`met_issue_ref_id` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/karigarmetalissue_acknowladgement/' AS `link`,
    `iitm`.`issue_met_parent_id` AS `print_id`,
    'type 6' AS `qry_type`,
    2 AS `trans_type`,
    `iitm`.`issue_met_parent_id` AS `trans_id`,
    IF(
        IFNULL(`uom`.`divided_by_value`, 0) = 0,
        `iitm`.`issue_metal_wt`,
        ROUND(
            `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
            3
        )
    ) AS `gross_wt`,
    IF(
        IFNULL(`uom`.`divided_by_value`, 0) = 0,
        `iitm`.`issue_metal_wt`,
        ROUND(
            `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
            3
        )
    ) AS `net_wt`,
    IFNULL(`iitm`.`issue_pcs`, 1) AS `no_of_pcs`,
    IFNULL(`iitm`.`touch`, '') AS `purchase_touch`,
    IF(
        `pr`.`stone_type` = 0,
        `iitm`.`issue_metal_pur_wt`,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `purewt`,
    `iss`.`met_issue_karid` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    `iitm`.`issue_cat_id` AS `catid`,
    `iitm`.`issu_met_pro_id` AS `proid`,
    2 AS `trans_screen_id`,
    `iitm`.`issue_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    IFNULL(`iss`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`iss`.`met_issue_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_karigar_metal_issue_details` `iitm`
                                LEFT JOIN `ret_karigar_metal_issue` `iss`
                                ON
                                    (
                                        `iss`.`met_issue_id` = `iitm`.`issue_met_parent_id`
                                    )
                                )
                            LEFT JOIN `ret_karigar` `kr`
                            ON
                                (
                                    `kr`.`id_karigar` = `iss`.`met_issue_karid`
                                )
                            )
                        LEFT JOIN `ret_category` `cat`
                        ON
                            (
                                `cat`.`id_ret_category` = `iitm`.`issue_cat_id`
                            )
                        )
                    LEFT JOIN `metal` `met`
                    ON
                        (`met`.`id_metal` = `cat`.`id_metal`)
                    )
                LEFT JOIN `ret_product_master` `pr`
                ON
                    (
                        `pr`.`pro_id` = `iitm`.`issu_met_pro_id`
                    )
                )
            LEFT JOIN `ret_uom` `uom`
            ON
                (`uom`.`uom_id` = `iitm`.`issue_uom_id`)
            )
        LEFT JOIN `ret_purchase_order` `po`
        ON
            (`po`.`po_id` = `iss`.`po_id`)
        )
    LEFT JOIN `ret_purchase_order_items` `poitm`
    ON
        (
            `poitm`.`po_item_id` = `iitm`.`po_item_id`
        )
    )
WHERE
    `iss`.`metalissue_type` = 2 AND `iss`.`bill_status` = 1
GROUP BY
    `iitm`.`issue_met_id`,
    `iitm`.`issue_cat_id`,
    `iitm`.`issu_met_pro_id`,
    `iitm`.`issue_met_parent_id`
UNION ALL
SELECT
    2 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 7' AS `qry_type`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    2 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 8' AS `qry_type`,
    `pay`.`weight_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    IFNULL(`pay`.`weight`, 0) AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    2 AS `ledger_type`,
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/credit_debit_acknolodgement/' AS `link`,
    `pay`.`crdrid` AS `print_id`,
    'type 9' AS `qry_type`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    IFNULL(`pay`.`weight`, 0) AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `cate`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`naration`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    (
        (
            (
                `ret_crdr_note` `pay`
            LEFT JOIN `ret_purchase_order` `po`
            ON
                (`po`.`po_id` = `pay`.`po_id`)
            )
        LEFT JOIN `ret_purchase_order_items` `order`
        ON
            (`order`.`po_item_po_id` = `po`.`po_id`)
        )
    LEFT JOIN `ret_category` `cate`
    ON
        (
            `cate`.`id_ret_category` = `order`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`accountto` = 3 AND `pay`.`weight` > 0 AND `pay`.`crdr_status` = 1
GROUP BY
    `pay`.`crdrid`
UNION ALL
SELECT
    1 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 10' AS `qry_type`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_purchase_order_items` `pitm`
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'PAYMENT' AS `category`,
    '' AS `product`,
    `pay`.`pay_create_on` AS `trans_date`,
    `pay`.`pay_refno` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/supplier_po_payment/paymentacknolodgement/' AS `link`,
    `pay`.`pay_id` AS `print_id`,
    'type 11' AS `qry_type`,
    2 AS `trans_type`,
    `pay`.`pay_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`pay_sup_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po_bill`.`bill_amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    `cate`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    `pay`.`pay_narration` AS `narration`,
    UNIX_TIMESTAMP(`pay`.`pay_create_on`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        `ret_po_payment` `pay`
                    LEFT JOIN `ret_po_payment_detail` `pd`
                    ON
                        (`pd`.`pay_id` = `pay`.`pay_id`)
                    )
                LEFT JOIN `ret_po_bill_payment_details` `po_bill`
                ON
                    (`po_bill`.`pay_id` = `pay`.`pay_id`)
                )
            LEFT JOIN `ret_purchase_order` `po`
            ON
                (`po`.`po_id` = `po_bill`.`po_id`)
            )
        LEFT JOIN(
            SELECT
                `ret_purchase_order_items`.`po_item_po_id` AS `po_item_po_id`,
                MIN(`ret_purchase_order_items`.`po_item_cat_id`) AS `po_item_cat_id`
            FROM
                `ret_purchase_order_items`
            GROUP BY
                `ret_purchase_order_items`.`po_item_po_id`
        ) `order`
    ON
        (`order`.`po_item_po_id` = `po`.`po_id`)
        )
    LEFT JOIN `ret_category` `cate`
    ON
        (
            `cate`.`id_ret_category` = `order`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`pay_status` = 1 AND `pay`.`bill_type` = 1
GROUP BY
    `po_bill`.`po_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    CONCAT(
        'RATE FIXING',
        IF(
            `pitm`.`rate` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 12' AS `qry_type`,
    IF(
        `pitm`.`rate` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    '' AS `gross_wt`,
    '' AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    '' AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (`pitm`.`rate` - `rf`.`rate_fix_rate`) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    `ret_po_rate_fix` `rf`
                LEFT JOIN `ret_purchase_order` `po`
                ON
                    (
                        `po`.`po_id` = `rf`.`rate_fix_po_item_id`
                    )
                )
            LEFT JOIN `ret_grn_entry` `grn`
            ON
                (`grn`.`grn_id` = `po`.`po_grn_id`)
            )
        LEFT JOIN(
            SELECT
                `pitm`.`po_item_po_id` AS `poid`,
                `pitm`.`fix_rate_per_grm` AS `rate`
            FROM
                `ret_purchase_order_items` `pitm`
            GROUP BY
                `pitm`.`po_item_po_id`
        ) `pitm`
    ON
        (`pitm`.`poid` = `po`.`po_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `po`.`isratefixed` = 0 AND `po`.`is_suspense_stock` = 0 AND `rf`.`bill_status` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    CONCAT(
        'RATE FIXING',
        IF(
            `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
            '(Dr)',
            '(Cr)'
        )
    ) AS `category`,
    '' AS `product`,
    `rf`.`rate_fix_created_on` AS `trans_date`,
    `rf`.`rate_fix_id` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 13' AS `qry_type`,
    IF(
        `rc`.`rate_per_gram` > `rf`.`rate_fix_rate`,
        2,
        1
    ) AS `trans_type`,
    `rf`.`rate_fix_id` AS `trans_id`,
    '' AS `gross_wt`,
    '' AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    '' AS `purewt`,
    `rc`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    ROUND(
        ABS(
            (
                `rc`.`rate_per_gram` - `rf`.`rate_fix_rate`
            ) * `rf`.`rate_fix_wt` * 1.03
        ),
        2
    ) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    `rc`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    `rf`.`rate_fix_rate` AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`rf`.`rate_fix_created_on`) AS `unixtransdate`
FROM
    (
        (
            `ret_po_rate_fix` `rf`
        LEFT JOIN `ret_supplier_rate_cut` `rc`
        ON
            (
                `rc`.`id_supplier_rate_cut` = `rf`.`id_approval_ratecut`
            )
        )
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `rc`.`po_id`)
    )
WHERE
    `rf`.`rate_fix_type` = 2 AND `rf`.`bill_status` = 1 AND `rc`.`conversion_type` = 1
GROUP BY
    `rf`.`rate_fix_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 14' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    'type 15' AS `qry_type`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 1 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    IFNULL(`pr`.`product_name`, 'PURE') AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    'type 16' AS `qry_type`,
    1 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '100' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `rf`.`id_metal` AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    IF(
        `rf`.`conversion_type` = 2,
        CONCAT(`rf`.`rate_per_gram`, '(Unfix)'),
        `rf`.`rate_per_gram`
    ) AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        (
            `ret_supplier_rate_cut` `rf`
        LEFT JOIN `ret_product_master` `pr`
        ON
            (`pr`.`pro_id` = `rf`.`id_product`)
        )
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `rf`.`po_id`)
    )
WHERE
    `rf`.`rate_cut_type` = 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 17' AS `qry_type`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `po_date`,
    '' AS `po_ref_no`,
    '' AS `link`,
    '' AS `print_id`,
    'type 18' AS `qry_type`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    (
        `pay`.`smith_type` = 1 OR `pay`.`smith_type` = 4
    ) AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    1 AS `ledger_type`,
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/credit_debit_acknolodgement/' AS `link`,
    `pay`.`crdrid` AS `print_id`,
    'type 19' AS `qry_type`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `cate`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    `pay`.`naration` AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    (
        (
            (
                `ret_crdr_note` `pay`
            LEFT JOIN `ret_purchase_order` `po`
            ON
                (`po`.`po_id` = `pay`.`po_id`)
            )
        LEFT JOIN `ret_purchase_order_items` `order`
        ON
            (`order`.`po_item_po_id` = `po`.`po_id`)
        )
    LEFT JOIN `ret_category` `cate`
    ON
        (
            `cate`.`id_ret_category` = `order`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`accountto` = 1 AND `pay`.`transamount` > 0 AND `pay`.`crdr_status` = 1
GROUP BY
    `pay`.`crdrid`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'TDS' AS `category`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 20' AS `qry_type`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tds_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            `ret_purchase_order` `po`
        LEFT JOIN `ret_grn_entry` `grn`
        ON
            (`grn`.`grn_id` = `po`.`po_grn_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tds_tax_value` > 0
GROUP BY
    `po`.`po_id`
UNION ALL
SELECT
    1 AS `ledger_type`,
    'TCS' AS `category`,
    '' AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    `po`.`po_date` AS `po_date`,
    `po`.`po_ref_no` AS `po_ref_no`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    'type 21' AS `qry_type`,
    2 AS `trans_type`,
    `po`.`po_id` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    2 AS `trans_rec_type`,
    SUM(`po`.`tcs_tax_value`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            `ret_purchase_order` `po`
        LEFT JOIN `ret_grn_entry` `grn`
        ON
            (`grn`.`grn_id` = `po`.`po_grn_id`)
        )
    LEFT JOIN `ret_karigar` `kr`
    ON
        (
            `kr`.`id_karigar` = `po`.`po_karigar_id`
        )
    )
WHERE
    `grn`.`grn_type` <> 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1 AND `po`.`is_suspense_stock` = 0 AND `po`.`tcs_tax_value` > 0
GROUP BY
    `po`.`po_id`
ORDER BY
    `unixtransdate`
DESC;
-- DEVADHARSHINI - 07-03-26
CREATE TABLE IF NOT EXISTS ret_crdr_ledger (
  id_crdr_ledger int(11) NOT NULL AUTO_INCREMENT,
  ledger_name varchar(255) NOT NULL,
  status tinyint(4) NOT NULL DEFAULT 1,
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  updated_on datetime DEFAULT NULL,
  updated_by int(11) DEFAULT NULL,
  PRIMARY KEY (id_crdr_ledger)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- CR DR Entry Table
ALTER TABLE ret_crdr_note ADD COLUMN id_cr_dr_ledger int(11) DEFAULT NULL AFTER supid;

-- NAMBI MUTHU RAJA - 18-03-26
CREATE TABLE IF NOT EXISTS `ret_pan_verification_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pan_number` varchar(10) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `name_on_pan` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `response_json` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `pan_number` (`pan_number`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- rudra 11/3/26-- approval ledger
DROP VIEW IF EXISTS `ret_view_supplier_approval_ledger`;

CREATE VIEW `ret_view_supplier_approval_ledger`  AS 
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    '/purchase/job_receipt/' AS `link`,
    `po`.`po_id` AS `print_id`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    `pitm`.`gross_wt` AS `gross_wt`,
    `pitm`.`net_wt` AS `net_wt`,
    `pitm`.`no_of_pcs` AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    `pitm`.`item_pure_wt` AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pitm`.`item_cost`) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    CONCAT(
        `pitm`.`fix_rate_per_grm`,
        IF(
            `pitm`.`is_rate_fixed` = 1,
            '',
            '(Un Fixed)'
        )
    ) AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_purchase_order_items` `pitm`
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `po`.`po_karigar_id`
                    )
                )
            LEFT JOIN `ret_category` `cat`
            ON
                (
                    `cat`.`id_ret_category` = `pitm`.`po_item_cat_id`
                )
            )
        LEFT JOIN `metal` `met`
        ON
            (`met`.`id_metal` = `cat`.`id_metal`)
        )
    LEFT JOIN `ret_product_master` `pr`
    ON
        (
            `pr`.`pro_id` = `pitm`.`po_item_pro_id`
        )
    )
WHERE
    `grn`.`grn_type` = 2 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1
GROUP BY
    `pitm`.`po_item_id`
UNION ALL
SELECT
    'PAYMENT' AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    2 AS `trans_type`,
    `rf`.`id_supplier_rate_cut` AS `trans_id`,
    `rf`.`weight` AS `gross_wt`,
    `rf`.`weight` AS `net_wt`,
    '' AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `rf`.`weight` AS `purewt`,
    `rf`.`id_karigar` AS `customer_id`,
    2 AS `trans_rec_type`,
    `rf`.`amount` AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    7 AS `trans_screen_id`,
    `rf`.`id_metal` AS `id_metal`,
    `rf`.`id_metal` AS `metal`,
    `rf`.`rate_per_gram` AS `rate`,
    IFNULL(`rf`.`narration`, '') AS `narration`,
    UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    (
        (
            `ret_supplier_rate_cut` `rf`
        LEFT JOIN `ret_purchase_order_items` `order`
        ON
            (`order`.`po_item_po_id` = `rf`.`po_id`)
        )
    LEFT JOIN `ret_category` `cate`
    ON
        (
            `cate`.`id_ret_category` = `order`.`po_item_cat_id`
        )
    )
WHERE
    `rf`.`rate_cut_type` = 1 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    IF(
        `rf`.`weight` = 0,
        'Bill Conv(Amount)',
        'Bill Conv(A to P)'
    ) AS `category`,
    '' AS `product`,
    `rf`.`date_add` AS `trans_date`,
    `rf`.`id_supplier_rate_cut` AS `referenceno`,
    '/supplier_rate_cut/job_receipt/' AS `link`,
    `rf`.`id_supplier_rate_cut` AS `print_id`,
    CASE 
    WHEN `rf`.`weight_type` = 1 AND `rf`.`amount_type` = 2 AND `rf`.`rate_cut_type` = 2 THEN 1 
    WHEN `rf`.`amount_type` = 2 AND `rf`.`weight_type` = 2 AND `rf`.`rate_cut_type` = 2 THEN 2 
    WHEN `rf`.`weight_type` = 2 AND `rf`.`amount_type` = 1 AND `rf`.`rate_cut_type` = 2 THEN 2 
    WHEN `rf`.`amount_type` = 1 AND `rf`.`weight_type` = 1 AND `rf`.`rate_cut_type` = 2 THEN 2 
    WHEN `rf`.`weight` <> 0 AND `rf`.`rate_cut_type` = 3 THEN 3 
    ELSE 2
    END AS `trans_type`,
`rf`.`id_supplier_rate_cut` AS `trans_id`,
`rf`.`weight` AS `gross_wt`,
`rf`.`weight` AS `net_wt`,
'' AS `no_of_pcs`,
IF(`rf`.`weight` = 0, '', '100') AS `purchase_touch`,
`rf`.`weight` AS `purewt`,
`rf`.`id_karigar` AS `customer_id`,
1 AS `trans_rec_type`,
CASE
    WHEN `rf`.`charges_amount` > 0 THEN IFNULL(`rf`.`charges_amount`, `rf`.`amount`)
    ELSE `rf`.`amount`
END AS `trans_amount`,
'' AS `catid`,
'' AS `proid`,
1 AS `trans_screen_id`,
`rf`.`id_metal` AS `id_metal`,
`rf`.`id_metal` AS `metal`,
`rf`.`rate_per_gram` AS `rate`,
IFNULL(`rf`.`narration`, '') AS `narration`,
UNIX_TIMESTAMP(`rf`.`date_add`) AS `unixtransdate`
FROM
    `ret_supplier_rate_cut` `rf`
WHERE
    `rf`.`rate_cut_type` >= 2 AND `rf`.`status` = 1
GROUP BY
    `rf`.`id_supplier_rate_cut`
UNION ALL
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 0 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    '/return_receipt_acknowladgement/' AS `link`,
    `ret`.`pur_return_id` AS `print_id`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    `pret`.`pur_ret_pur_wt` AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(
        IFNULL(`pret`.`pur_ret_debit_note_amt`, 0)
    ) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    `pret`.`pur_ret_rate` AS `rate`,
    IFNULL(`ret`.`pur_ret_remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    `ret_purchase_return_items` `pret`
                                LEFT JOIN `ret_purchase_return` `ret`
                                ON
                                    (
                                        `ret`.`pur_return_id` = `pret`.`pur_ret_id`
                                    )
                                )
                            LEFT JOIN `ret_purchase_order_items` `pitm`
                            ON
                                (
                                    `pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`
                                )
                            )
                        LEFT JOIN `ret_purchase_order` `po`
                        ON
                            (`po`.`po_id` = `pitm`.`po_item_po_id`)
                        )
                    LEFT JOIN `ret_grn_entry` `grn`
                    ON
                        (`grn`.`grn_id` = `po`.`po_grn_id`)
                    )
                LEFT JOIN `ret_karigar` `kr`
                ON
                    (
                        `kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`
                    )
                )
            LEFT JOIN `ret_product_master` `pr`
            ON
                (`pr`.`pro_id` = `pret`.`id_product`)
            )
        LEFT JOIN `ret_category` `cat`
        ON
            (
                `cat`.`id_ret_category` = `pr`.`cat_id`
            )
        )
    LEFT JOIN `metal` `met`
    ON
        (`met`.`id_metal` = `cat`.`id_metal`)
    )
WHERE
    `ret`.`pur_ret_convert_to` = 3 AND `ret`.`purchase_type` = 1 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION ALL
SELECT
    `pr`.`product_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `iss`.`met_issue_date` AS `trans_date`,
    `iss`.`met_issue_ref_id` AS `referenceno`,
    '/karigarmetalissue_acknowladgement/' AS `link`,
    `iitm`.`issue_met_parent_id` AS `print_id`,
    2 AS `trans_type`,
    `iitm`.`issue_met_parent_id` AS `trans_id`,
    IF(
        `cat`.`cat_type` = 2,
        0,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `gross_wt`,
    IF(
        `cat`.`cat_type` = 2,
        0,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `net_wt`,
    IFNULL(`iitm`.`issue_pcs`, 1) AS `no_of_pcs`,
    '100' AS `purchase_touch`,
    IF(
        `pr`.`stone_type` = 0,
        `iitm`.`issue_metal_pur_wt`,
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `iitm`.`issue_metal_wt`,
            ROUND(
                `iitm`.`issue_metal_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `purewt`,
    `iss`.`met_issue_karid` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    `iitm`.`issue_cat_id` AS `catid`,
    `iitm`.`issu_met_pro_id` AS `proid`,
    2 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    IFNULL(`iss`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`iss`.`met_issue_date`) AS `unixtransdate`
FROM
    (
        (
            (
                (
                    (
                        (
                            `ret_karigar_metal_issue_details` `iitm`
                        LEFT JOIN `ret_karigar_metal_issue` `iss`
                        ON
                            (
                                `iss`.`met_issue_id` = `iitm`.`issue_met_parent_id`
                            )
                        )
                    LEFT JOIN `ret_karigar` `kr`
                    ON
                        (
                            `kr`.`id_karigar` = `iss`.`met_issue_karid`
                        )
                    )
                LEFT JOIN `ret_category` `cat`
                ON
                    (
                        `cat`.`id_ret_category` = `iitm`.`issue_cat_id`
                    )
                )
            LEFT JOIN `metal` `met`
            ON
                (`met`.`id_metal` = `cat`.`id_metal`)
            )
        LEFT JOIN `ret_product_master` `pr`
        ON
            (
                `pr`.`pro_id` = `iitm`.`issu_met_pro_id`
            )
        )
    LEFT JOIN `ret_uom` `uom`
    ON
        (`uom`.`uom_id` = `iitm`.`issue_uom_id`)
    )
WHERE
    `iss`.`metalissue_type` = 2 AND `iss`.`bill_status` = 1
GROUP BY
    `iitm`.`issue_met_id`,
    `iitm`.`issue_cat_id`,
    `iitm`.`issu_met_pro_id`,
    `iitm`.`issue_met_parent_id`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `link`,
    '' AS `print_id`,
    `pay`.`amount_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    0 AS `gross_wt`,
    0 AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    0 AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`amount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    '' AS `link`,
    '' AS `print_id`,
    `pay`.`weight_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    IFNULL(`pay`.`weight`, 0) AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    `pay`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`remarks`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`
FROM
    `smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 3 AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION ALL
SELECT
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
    '' AS `product`,
    `pay`.`transdate` AS `trans_date`,
    `pay`.`transbillno` AS `referenceno`,
    '/credit_debit_acknolodgement/' AS `link`,
    `pay`.`crdrid` AS `print_id`,
    `pay`.`transtype` AS `trans_type`,
    `pay`.`transbillno` AS `trans_id`,
    `pay`.`weight` AS `gross_wt`,
    `pay`.`weight` AS `net_wt`,
    0 AS `no_of_pcs`,
    '' AS `purchase_touch`,
    `pay`.`weight` AS `purewt`,
    `pay`.`supid` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pay`.`transamount`) AS `trans_amount`,
    '' AS `catid`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    `cate`.`id_metal` AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    IFNULL(`pay`.`naration`, '') AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`
FROM
    (
        (
            `ret_crdr_note` `pay`
        LEFT JOIN `ret_purchase_order_items` `order`
        ON
            (
                `order`.`po_item_po_id` = `pay`.`po_id`
            )
        )
    LEFT JOIN `ret_category` `cate`
    ON
        (
            `cate`.`id_ret_category` = `order`.`po_item_cat_id`
        )
    )
WHERE
    `pay`.`accountto` = 3 AND `pay`.`crdr_status` = 1 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`crdrid`
ORDER BY
    `unixtransdate`
DESC;
-- ==============================================================================
-- POS INTEGRATION — Full Migration Script (CORRECTED & IDEMPOTENT)
-- Original Date: 12-03-2026
-- Corrected: 14-03-2026
-- Description: Creates all POS tables, settings, and seed data for multi-provider
--              POS payment integration (Pine Labs, PhonePe DQR, PhonePe IEDC)
-- 
-- IMPORTANT: This script is IDEMPOTENT — safe to run on:
--   • Fresh databases (creates everything correctly)
--   • Databases that ran the old (wrong) migration (fixes/adds what's missing)
--   • Can be run multiple times without errors
-- ==============================================================================

SET SQL_SAFE_UPDATES=0;

-- -----------------------------------------------
-- 1. TABLE: ret_pos_providers
--    Stores POS payment provider configurations
--    (Pine Labs, PhonePe DQR, PhonePe IEDC, etc.)
--
--    NOTE: Column sizes match actual source DB:
--    provider_name=VARCHAR(50), provider_code=VARCHAR(20),
--    API URLs=VARCHAR(255), auth_type default='token'
-- -----------------------------------------------

CREATE TABLE IF NOT EXISTS `ret_pos_providers` (
  `id_provider`         INT NOT NULL AUTO_INCREMENT,
  `provider_name`       VARCHAR(50) NOT NULL COMMENT 'Display name (e.g. Pine Labs, PhonePe DQR)',
  `provider_code`       VARCHAR(20) NOT NULL COMMENT 'Code used in routing logic (e.g. pinelabs, phonepe_dqr)',
  `api_url_uat_init`    VARCHAR(255) DEFAULT NULL COMMENT 'UAT URL for payment init',
  `api_url_uat_status`  VARCHAR(255) DEFAULT NULL COMMENT 'UAT URL for status check',
  `api_url_uat_cancel`  VARCHAR(255) DEFAULT NULL COMMENT 'UAT URL for payment cancel',
  `api_url_live_init`   VARCHAR(255) DEFAULT NULL COMMENT 'Live/Production URL for payment init',
  `api_url_live_status` VARCHAR(255) DEFAULT NULL COMMENT 'Live/Production URL for status check',
  `api_url_live_cancel` VARCHAR(255) DEFAULT NULL COMMENT 'Live/Production URL for payment cancel',
  `auth_type`           VARCHAR(20) DEFAULT 'token' COMMENT 'token|sha256_header|basic_auth',
  `is_env_live`         TINYINT DEFAULT 0 COMMENT '0=UAT, 1=LIVE',
  `has_qr_display`      TINYINT DEFAULT 0 COMMENT '1=show QR on screen',
  `has_callback`        TINYINT DEFAULT 0 COMMENT '1=supports S2S callback',
  `status_method`       VARCHAR(10) DEFAULT 'POST' COMMENT 'POST or GET',
  `is_active`           TINYINT DEFAULT 1,
  `created_at`          DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_provider`),
  UNIQUE KEY `uk_provider_code` (`provider_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- FIX: If table existed from old migration with wrong column sizes, fix them
ALTER TABLE `ret_pos_providers`
  MODIFY `provider_name` VARCHAR(50) NOT NULL,
  MODIFY `provider_code` VARCHAR(20) NOT NULL,
  MODIFY `api_url_uat_init` VARCHAR(255) DEFAULT NULL,
  MODIFY `api_url_uat_status` VARCHAR(255) DEFAULT NULL,
  MODIFY `api_url_uat_cancel` VARCHAR(255) DEFAULT NULL,
  MODIFY `api_url_live_init` VARCHAR(255) DEFAULT NULL,
  MODIFY `api_url_live_status` VARCHAR(255) DEFAULT NULL,
  MODIFY `api_url_live_cancel` VARCHAR(255) DEFAULT NULL,
  MODIFY `auth_type` VARCHAR(20) DEFAULT 'token' COMMENT 'token|sha256_header|basic_auth',
  MODIFY `is_env_live` TINYINT DEFAULT 0 COMMENT '0=UAT, 1=LIVE',
  MODIFY `has_qr_display` TINYINT DEFAULT 0 COMMENT '1=show QR on screen',
  MODIFY `has_callback` TINYINT DEFAULT 0 COMMENT '1=supports S2S callback',
  MODIFY `status_method` VARCHAR(10) DEFAULT 'POST' COMMENT 'POST or GET',
  MODIFY `is_active` TINYINT DEFAULT 1;

-- -----------------------------------------------
-- 2. TABLE: ret_pos_device_list
--    Stores individual POS device/terminal configs
--    Linked to a provider via id_provider FK
--
--    NOTE: Column sizes match actual source DB:
--    merchantid=VARCHAR(50), securitytoken=VARCHAR(100),
--    salt_key=VARCHAR(100), devicetype=INT DEFAULT 1
--    Includes id_pay_device and id_bank for billing link
-- -----------------------------------------------

CREATE TABLE IF NOT EXISTS `ret_pos_device_list` (
  `id_device`       INT NOT NULL AUTO_INCREMENT,
  `dispname`        VARCHAR(100) DEFAULT NULL COMMENT 'Display name for dropdown',
  `devicetype`      INT DEFAULT 1 COMMENT '0=Card+UPI(Both), 1=Card only, 3=UPI QR only',
  `poscode`         VARCHAR(50) DEFAULT NULL COMMENT 'POS code (Pine Labs store code)',
  `merchantid`      VARCHAR(50) DEFAULT NULL COMMENT 'Merchant ID from provider',
  `securitytoken`   VARCHAR(100) DEFAULT NULL COMMENT 'API key / security token',
  `imei`            VARCHAR(50) DEFAULT NULL COMMENT 'Device IMEI (Pine Labs)',
  `is_default`      TINYINT DEFAULT 0 COMMENT '1=Default device (pre-selected)',
  `id_provider`     INT DEFAULT NULL COMMENT 'FK to ret_pos_providers',
  `store_id`        VARCHAR(50) DEFAULT NULL COMMENT 'PhonePe storeId',
  `terminal_id`     VARCHAR(50) DEFAULT NULL COMMENT 'PhonePe terminalId',
  `salt_key`        VARCHAR(100) DEFAULT NULL COMMENT 'PhonePe saltKey',
  `salt_index`      VARCHAR(10) DEFAULT NULL COMMENT 'PhonePe saltIndex',
  `provider_id`     VARCHAR(50) DEFAULT NULL COMMENT 'PhonePe X-PROVIDER-ID',
  `callback_url`    VARCHAR(255) DEFAULT NULL COMMENT 'S2S callback URL',
  `is_active`       TINYINT DEFAULT 1 COMMENT '0=inactive, 1=active',
  `id_pay_device`   INT DEFAULT NULL COMMENT 'Links to ret_bill_pay_device — which bank swipe device this POS maps to',
  `id_bank`         INT DEFAULT NULL COMMENT 'Links to bank — which client bank account receives money from this POS device',
  PRIMARY KEY (`id_device`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- FIX: If table existed from old migration with wrong column sizes/types, fix them
ALTER TABLE `ret_pos_device_list`
  MODIFY `dispname` VARCHAR(100) DEFAULT NULL,
  MODIFY `devicetype` INT DEFAULT 1 COMMENT '0=Card+UPI(Both), 1=Card only, 3=UPI QR only',
  MODIFY `merchantid` VARCHAR(50) DEFAULT NULL,
  MODIFY `securitytoken` VARCHAR(100) DEFAULT NULL,
  MODIFY `salt_key` VARCHAR(100) DEFAULT NULL COMMENT 'PhonePe saltKey',
  MODIFY `provider_id` VARCHAR(50) DEFAULT NULL COMMENT 'PhonePe X-PROVIDER-ID',
  MODIFY `store_id` VARCHAR(50) DEFAULT NULL COMMENT 'PhonePe storeId',
  MODIFY `terminal_id` VARCHAR(50) DEFAULT NULL COMMENT 'PhonePe terminalId',
  MODIFY `callback_url` VARCHAR(255) DEFAULT NULL COMMENT 'S2S callback URL',
  MODIFY `id_provider` INT DEFAULT NULL COMMENT 'FK to ret_pos_providers',
  MODIFY `is_active` TINYINT DEFAULT 1 COMMENT '0=inactive, 1=active';

-- FIX: Add missing columns if they don't exist (safe — errors ignored if already exist)
-- These columns link POS device to billing pay device and bank for reconciliation
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_device_list' AND COLUMN_NAME = 'id_pay_device');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_device_list ADD COLUMN id_pay_device INT DEFAULT NULL COMMENT ''Links to ret_bill_pay_device'' AFTER is_active',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_device_list' AND COLUMN_NAME = 'id_bank');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_device_list ADD COLUMN id_bank INT DEFAULT NULL COMMENT ''Links to bank table'' AFTER id_pay_device',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------
-- 3. TABLE: ret_pos_requests
--    Transaction log for all POS payment requests
--    Records init, status, and cancel operations
--
--    NOTE: Includes pos_imie (device IMEI sent with request),
--    pos_qr_string (QR data for DQR flow), and separate
--    created_at + pos_req_created_at columns.
--    Default for pos_req_status is 1 (not 0).
-- -----------------------------------------------

CREATE TABLE IF NOT EXISTS `ret_pos_requests` (
  `pos_req_id`          INT NOT NULL AUTO_INCREMENT,
  `pos_trans_no`        VARCHAR(100) DEFAULT NULL COMMENT 'Our generated transaction ID',
  `pos_store_pos_code`  VARCHAR(50) DEFAULT NULL COMMENT 'Store/POS code sent to provider',
  `pos_req_amount`      DECIMAL(12,2) DEFAULT NULL COMMENT 'Amount in paise (PhonePe) or rupees',
  `pos_usr_id`          INT DEFAULT NULL COMMENT 'User who initiated payment',
  `pos_mer_id`          VARCHAR(50) DEFAULT NULL COMMENT 'Merchant ID used',
  `pos_imie`            VARCHAR(50) DEFAULT NULL COMMENT 'Device IMEI sent with request',
  `pos_req_createdby`   INT DEFAULT NULL COMMENT 'Created by user ID',
  `pos_req_bill_cusid`  INT DEFAULT NULL COMMENT 'Customer ID from billing',
  `pos_req_bill_id`     INT DEFAULT NULL COMMENT 'Bill ID (FK to ret_estimation)',
  `pos_res_ref_id`      VARCHAR(100) DEFAULT NULL COMMENT 'Provider reference/transaction ID',
  `pos_res_trans_data`  TEXT DEFAULT NULL COMMENT 'Full provider response data (JSON)',
  `pos_req_status`      TINYINT DEFAULT 1 COMMENT '0=INIT/PENDING, 1=SUCCESS, 2=CANCELLED, 3=FAILED',
  `created_at`          DATETIME DEFAULT CURRENT_TIMESTAMP,
  `id_provider`         INT DEFAULT NULL COMMENT 'FK to ret_pos_providers',
  `pos_qr_string`       TEXT DEFAULT NULL COMMENT 'QR data for DQR flow',
  `pos_callback_data`   TEXT DEFAULT NULL COMMENT 'S2S callback response',
  `idempotency_key`     VARCHAR(100) DEFAULT NULL COMMENT 'Client-generated idempotency key to prevent duplicate payments',
  `pos_utr`             VARCHAR(50) DEFAULT NULL COMMENT 'UPI UTR number',
  `pos_req_payload`     TEXT DEFAULT NULL COMMENT 'Full API request payload (JSON)',
  `pos_res_payload`     TEXT DEFAULT NULL COMMENT 'Full API response payload (JSON)',
  `pos_req_created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pos_req_id`),
  UNIQUE KEY `idx_idempotency_key` (`idempotency_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- FIX: If table existed from old migration, fix column sizes and defaults
ALTER TABLE `ret_pos_requests`
  MODIFY `pos_mer_id` VARCHAR(50) DEFAULT NULL,
  MODIFY `pos_res_ref_id` VARCHAR(100) DEFAULT NULL,
  MODIFY `pos_utr` VARCHAR(50) DEFAULT NULL COMMENT 'UPI UTR number',
  MODIFY `pos_req_status` TINYINT DEFAULT 1 COMMENT '0=INIT/PENDING, 1=SUCCESS, 2=CANCELLED, 3=FAILED';

-- FIX: Add missing columns if they don't exist
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_imie');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN pos_imie VARCHAR(50) DEFAULT NULL COMMENT ''Device IMEI'' AFTER pos_mer_id',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_qr_string');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN pos_qr_string TEXT DEFAULT NULL COMMENT ''QR data for DQR'' AFTER id_provider',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'created_at');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP AFTER pos_req_status',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_req_bill_id');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN pos_req_bill_id INT DEFAULT NULL COMMENT ''FK to ret_estimation'' AFTER pos_req_bill_cusid',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_utr');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN pos_utr VARCHAR(50) DEFAULT NULL COMMENT ''UPI UTR number'' AFTER idempotency_key',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_req_payload');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN pos_req_payload TEXT DEFAULT NULL COMMENT ''Full API request payload (JSON)''',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_res_payload');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN pos_res_payload TEXT DEFAULT NULL COMMENT ''Full API response payload (JSON)''',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'idempotency_key');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN idempotency_key VARCHAR(100) DEFAULT NULL COMMENT ''Idempotency key''',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_req_created_at');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN pos_req_created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------
-- 4. RETAIL SETTING: pay_by_pos
--    Enable/disable POS integration globally
-- -----------------------------------------------
INSERT INTO `ret_settings` (`name`, `value`, `description`, `created_by`)
SELECT 'pay_by_pos', '0', 'Enable POS machine payment for card transactions (1=enable, 0=disable)', 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `ret_settings` WHERE `name` = 'pay_by_pos');

-- -----------------------------------------------
-- 4b. MODULE: Register POS in modules table
-- -----------------------------------------------
INSERT INTO modules (m_name, m_code, m_app, m_web, m_active, date_add, date_upd) 
VALUES ('POS Integration', 'POS', 0, 1, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE m_name = 'POS Integration';

-- -----------------------------------------------
-- 4c. MENUS: POS Management + sub-menus
--     Only insert if not already present
-- -----------------------------------------------

-- Clean up old/incorrect POS menu entries (from earlier versions)
DELETE a FROM access a INNER JOIN menu m ON a.id_menu = m.id_menu 
WHERE m.link IN ('admin_ret_billing/posSettings', 'admin_ret_billing/posTransactions');
DELETE FROM menu WHERE link IN ('admin_ret_billing/posSettings', 'admin_ret_billing/posTransactions');

-- Only create menus if POS Management doesn't already exist
SET @pos_menu_exists = (SELECT COUNT(*) FROM menu WHERE label = 'POS Management' AND link = '#');

-- If POS Management menu already exists, get its id
SET @pos_parent = (SELECT id_menu FROM menu WHERE label = 'POS Management' AND link = '#' LIMIT 1);

-- If it doesn't exist, create it
SET @sql = IF(@pos_menu_exists = 0,
  'INSERT INTO menu (label, link, parent, sort, icon, active) VALUES (''POS Management'', ''#'', 1, 5, ''fa fa-credit-card'', 1)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Get the parent ID (either just inserted or already existing)
SET @pos_parent = IF(@pos_menu_exists = 0, LAST_INSERT_ID(), @pos_parent);

-- Sub-menus (only if not already present)
SET @sub_exists = (SELECT COUNT(*) FROM menu WHERE link = 'admin_pos/posSettings');
SET @sql = IF(@sub_exists = 0,
  CONCAT('INSERT INTO menu (label, link, parent, sort, icon, active) VALUES (''POS Settings'', ''admin_pos/posSettings'', ', @pos_parent, ', 1, ''fa fa-cog'', 1)'),
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @settings_menu = IF(@sub_exists = 0, LAST_INSERT_ID(), (SELECT id_menu FROM menu WHERE link = 'admin_pos/posSettings' LIMIT 1));

SET @sub_exists = (SELECT COUNT(*) FROM menu WHERE link = 'admin_pos/posTransactions');
SET @sql = IF(@sub_exists = 0,
  CONCAT('INSERT INTO menu (label, link, parent, sort, icon, active) VALUES (''Transaction Log'', ''admin_pos/posTransactions'', ', @pos_parent, ', 2, ''fa fa-list-alt'', 1)'),
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @trans_menu = IF(@sub_exists = 0, LAST_INSERT_ID(), (SELECT id_menu FROM menu WHERE link = 'admin_pos/posTransactions' LIMIT 1));

SET @sub_exists = (SELECT COUNT(*) FROM menu WHERE link = 'admin_pos/posSettlementPage');
SET @sql = IF(@sub_exists = 0,
  CONCAT('INSERT INTO menu (label, link, parent, sort, icon, active) VALUES (''Settlement Summary'', ''admin_pos/posSettlementPage'', ', @pos_parent, ', 3, ''fa fa-bar-chart'', 1)'),
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @settle_menu = IF(@sub_exists = 0, LAST_INSERT_ID(), (SELECT id_menu FROM menu WHERE link = 'admin_pos/posSettlementPage' LIMIT 1));

-- -----------------------------------------------
-- 4d. ACCESS PERMISSIONS for POS menus
-- -----------------------------------------------

-- Admin (profile 1) — full access
INSERT IGNORE INTO access (id_profile, id_menu, `view`, `add`, `edit`, `delete`)
VALUES (1, @pos_parent, 1, 1, 1, 1), (1, @settings_menu, 1, 1, 1, 1), 
       (1, @trans_menu, 1, 1, 1, 1), (1, @settle_menu, 1, 1, 1, 1);

-- Developer (profile 2) — full access
INSERT IGNORE INTO access (id_profile, id_menu, `view`, `add`, `edit`, `delete`)
VALUES (2, @pos_parent, 1, 1, 1, 1), (2, @settings_menu, 1, 1, 1, 1), 
       (2, @trans_menu, 1, 1, 1, 1), (2, @settle_menu, 1, 1, 1, 1);

-- Manager (profile 3) — view only
INSERT IGNORE INTO access (id_profile, id_menu, `view`, `add`, `edit`, `delete`)
VALUES (3, @pos_parent, 1, 0, 0, 0), (3, @settings_menu, 1, 0, 0, 0), 
       (3, @trans_menu, 1, 0, 0, 0), (3, @settle_menu, 1, 0, 0, 0);

-- -----------------------------------------------
-- 5. SEED DATA: Default providers
--    Pre-populate Pine Labs and PhonePe providers
--    (safe — WHERE NOT EXISTS prevents duplicates)
-- -----------------------------------------------

INSERT INTO `ret_pos_providers` (`provider_name`, `provider_code`, `auth_type`, `status_method`,
  `api_url_uat_init`, `api_url_uat_status`, `api_url_uat_cancel`,
  `api_url_live_init`, `api_url_live_status`, `api_url_live_cancel`,
  `is_env_live`, `has_qr_display`, `has_callback`)
SELECT 'Pine Labs', 'pinelabs', 'token', 'POST',
  'https://www.plutuscloudserviceuat.in/API/CloudBasedIntegration/V1/UploadBilledTransaction',
  'https://www.plutuscloudserviceuat.in/API/CloudBasedIntegration/V1/GetCloudBasedTxnStatus',
  'https://www.plutuscloudserviceuat.in/API/CloudBasedIntegration/V1/CancelTransaction',
  'https://www.plutuscloudservice.in/API/CloudBasedIntegration/V1/UploadBilledTransaction',
  'https://www.plutuscloudservice.in/API/CloudBasedIntegration/V1/GetCloudBasedTxnStatus',
  'https://www.plutuscloudservice.in/API/CloudBasedIntegration/V1/CancelTransaction',
  0, 0, 0
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `ret_pos_providers` WHERE `provider_code` = 'pinelabs');

INSERT INTO `ret_pos_providers` (`provider_name`, `provider_code`, `auth_type`, `status_method`,
  `api_url_uat_init`, `api_url_uat_status`, `api_url_uat_cancel`,
  `api_url_live_init`, `api_url_live_status`, `api_url_live_cancel`,
  `is_env_live`, `has_qr_display`, `has_callback`)
SELECT 'PhonePe DQR', 'phonepe_dqr', 'x_verify', 'GET',
  'https://mercury-uat.phonepe.com/enterprise-sandbox/v3/qr/init',
  'https://mercury-uat.phonepe.com/enterprise-sandbox/v3/transaction/{merchantId}/{transactionId}/status',
  'https://mercury-uat.phonepe.com/enterprise-sandbox/v3/charge/{merchantId}/{transactionId}/cancel',
  'https://mercury-t2.phonepe.com/v3/qr/init',
  'https://mercury-t2.phonepe.com/v3/transaction/{merchantId}/{transactionId}/status',
  'https://mercury-t2.phonepe.com/v3/charge/{merchantId}/{transactionId}/cancel',
  0, 1, 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `ret_pos_providers` WHERE `provider_code` = 'phonepe_dqr');

INSERT INTO `ret_pos_providers` (`provider_name`, `provider_code`, `auth_type`, `status_method`,
  `api_url_uat_init`, `api_url_uat_status`, `api_url_uat_cancel`,
  `api_url_live_init`, `api_url_live_status`, `api_url_live_cancel`,
  `is_env_live`, `has_qr_display`, `has_callback`)
SELECT 'PhonePe IEDC', 'phonepe_iedc', 'x_verify', 'GET',
  'https://mercury-uat.phonepe.com/enterprise-sandbox/v1/edc/transaction/init',
  'https://mercury-uat.phonepe.com/enterprise-sandbox/v1/edc/transaction/{merchantId}/{transactionId}/status',
  'https://mercury-uat.phonepe.com/enterprise-sandbox/v3/charge/{merchantId}/{transactionId}/cancel',
  'https://mercury-t2.phonepe.com/v1/edc/transaction/init',
  'https://mercury-t2.phonepe.com/v1/edc/transaction/{merchantId}/{transactionId}/status',
  'https://mercury-t2.phonepe.com/v3/charge/{merchantId}/{transactionId}/cancel',
  0, 0, 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `ret_pos_providers` WHERE `provider_code` = 'phonepe_iedc');

-- -----------------------------------------------
-- 6. RECONCILIATION: pos_req_id on ret_billing_payment
--    Links POS payment to billing payment record
-- -----------------------------------------------

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_billing_payment' AND COLUMN_NAME = 'pos_req_id');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_billing_payment ADD COLUMN pos_req_id INT DEFAULT NULL COMMENT ''FK to ret_pos_requests'' AFTER NB_type',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------
-- 7. POS PAYMENT SESSION LOCK TABLE
--    Prevents double payments, locks bill during
--    active POS payment, enables reconciliation.
--    States: INIT → PENDING → SUCCESS/FAILED/
--            CANCELLED/EXPIRED
--
--    NOTE: Uses ENUM for status, has updated_at,
--    all columns are DEFAULT NULL (not NOT NULL)
-- -----------------------------------------------

CREATE TABLE IF NOT EXISTS `pos_payment_sessions` (
    `session_id`    INT NOT NULL AUTO_INCREMENT,
    `invoice_id`    VARCHAR(50) DEFAULT NULL COMMENT 'bill_est_id',
    `customer_id`   INT DEFAULT NULL COMMENT 'cusid',
    `device_id`     INT DEFAULT NULL,
    `provider_code` VARCHAR(50) DEFAULT NULL,
    `amount_paise`  BIGINT DEFAULT NULL,
    `status`        ENUM('INIT','PENDING','SUCCESS','FAILED','CANCELLED','EXPIRED') DEFAULT 'INIT',
    `pos_req_id`    INT DEFAULT NULL COMMENT 'links to ret_pos_requests.pos_req_id',
    `created_by`    INT DEFAULT NULL,
    `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `expires_at`    DATETIME DEFAULT NULL COMMENT 'auto-expire time',
    PRIMARY KEY (`session_id`),
    KEY `idx_invoice` (`invoice_id`),
    KEY `idx_status` (`status`),
    KEY `idx_customer` (`customer_id`),
    KEY `idx_expires` (`expires_at`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='POS payment session lock for bill safety';

-- FIX: Add updated_at if missing from old migration
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pos_payment_sessions' AND COLUMN_NAME = 'updated_at');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE pos_payment_sessions ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------
-- VERIFICATION: Check tables were created correctly
-- -----------------------------------------------
SHOW TABLES LIKE 'ret_pos%';
SHOW TABLES LIKE 'pos_payment%';
DESCRIBE ret_pos_providers;
DESCRIBE ret_pos_device_list;
DESCRIBE ret_pos_requests;
DESCRIBE pos_payment_sessions;
SELECT * FROM ret_settings WHERE name = 'pay_by_pos';

-- =======================================================
-- POS MODULE — PHASE 1: Data Model Upgrade (14-03-2026)
-- Adds device tracking, audit trail, payment mode extraction
-- =======================================================

-- -----------------------------------------------
-- 8. PHASE 1: Add missing columns to ret_pos_requests
--    Device tracking, provider filter, payment mode,
--    card receipt extraction, audit trail columns
-- -----------------------------------------------

-- 8a. id_device — FK to ret_pos_device_list (critical for multi-terminal reporting)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'id_device');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN id_device INT DEFAULT NULL COMMENT ''FK to ret_pos_device_list'' AFTER id_provider',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 8b. provider_code — quick filter without JOIN
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'provider_code');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN provider_code VARCHAR(30) DEFAULT NULL COMMENT ''pinelabs/phonepe_dqr/phonepe_iedc'' AFTER id_device',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 8c. payment_mode — extracted from JSON for searchability
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'payment_mode');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN payment_mode VARCHAR(20) DEFAULT NULL COMMENT ''CARD/UPI/DQR/NB'' AFTER pos_utr',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 8d. card_last4 — last 4 digits for receipt/dispute
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'card_last4');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN card_last4 VARCHAR(4) DEFAULT NULL COMMENT ''Last 4 digits of card'' AFTER payment_mode',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 8e. approval_code — bank approval/auth code
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'approval_code');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN approval_code VARCHAR(20) DEFAULT NULL COMMENT ''Bank approval/auth code'' AFTER card_last4',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 8f. updated_at — auto-tracks last status change
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'updated_at');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT ''Last status change'' AFTER created_at',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 8g. Cancel audit trail (previously failed silently because columns didn't exist)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_cancelled_by');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN pos_cancelled_by INT DEFAULT NULL COMMENT ''User who cancelled'' AFTER pos_req_status',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_cancelled_at');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN pos_cancelled_at DATETIME DEFAULT NULL COMMENT ''When cancelled'' AFTER pos_cancelled_by',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 8h. Status check audit trail (also previously failing silently)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_last_checked_by');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN pos_last_checked_by INT DEFAULT NULL COMMENT ''User who last checked status'' AFTER pos_cancelled_at',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_last_checked_at');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN pos_last_checked_at DATETIME DEFAULT NULL COMMENT ''When last status check'' AFTER pos_last_checked_by',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------
-- 9. PHASE 1: Performance indexes
-- -----------------------------------------------

-- Safe index creation — ignore error if already exists
-- MySQL doesn't support IF NOT EXISTS for indexes via prepared statements cleanly,
-- so we use a procedure-style approach or just run and ignore duplicate key errors.

SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND INDEX_NAME = 'idx_pos_device');
SET @sql = IF(@idx_exists = 0, 
  'CREATE INDEX idx_pos_device ON ret_pos_requests (id_device)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND INDEX_NAME = 'idx_pos_status_date');
SET @sql = IF(@idx_exists = 0, 
  'CREATE INDEX idx_pos_status_date ON ret_pos_requests (pos_req_status, created_at)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND INDEX_NAME = 'idx_pos_ref_id');
SET @sql = IF(@idx_exists = 0, 
  'CREATE INDEX idx_pos_ref_id ON ret_pos_requests (pos_res_ref_id)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND INDEX_NAME = 'idx_pos_bill_id');
SET @sql = IF(@idx_exists = 0, 
  'CREATE INDEX idx_pos_bill_id ON ret_pos_requests (pos_req_bill_id)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND INDEX_NAME = 'idx_pos_provider');
SET @sql = IF(@idx_exists = 0, 
  'CREATE INDEX idx_pos_provider ON ret_pos_requests (provider_code)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------
-- 10. PHASE 1: Backfill existing data
--     Maps existing rows to devices and providers
-- -----------------------------------------------

-- Backfill id_device from merchant_id match
UPDATE ret_pos_requests r
  JOIN ret_pos_device_list d ON d.merchantid = r.pos_mer_id
SET r.id_device = d.id_device
WHERE r.id_device IS NULL;

-- Backfill provider_code from provider FK
UPDATE ret_pos_requests r
  JOIN ret_pos_providers p ON p.id_provider = r.id_provider
SET r.provider_code = p.provider_code
WHERE r.provider_code IS NULL;


-- =======================================================
-- POS MODULE — PHASE 2: Reconciliation & Settlement
-- Date: 14-03-2026
-- =======================================================

-- -----------------------------------------------
-- 11. ORPHAN TRANSACTION DETECTION QUERIES
--     Run periodically to find data integrity issues
-- -----------------------------------------------

-- 11a. Orphan Type 1: POS payment succeeded but bill NOT saved
--      (Customer paid, but ERP crashed before saving billing record)
-- SELECT r.pos_req_id, r.pos_trans_no, r.pos_req_amount/100 as amount_rs,
--        r.pos_utr, r.provider_code, r.payment_mode,
--        r.created_at, d.dispname as device_name,
--        e.name as employee_name
-- FROM ret_pos_requests r
-- LEFT JOIN ret_billing_payment bp ON bp.pos_req_id = r.pos_req_id
-- LEFT JOIN ret_pos_device_list d ON d.id_device = r.id_device
-- LEFT JOIN employee e ON e.id_employee = r.pos_usr_id
-- WHERE r.pos_req_status = 1                    -- SUCCESS in provider
--   AND bp.payment_id IS NULL                   -- No billing payment record
--   AND r.pos_req_bill_id IS NOT NULL           -- Had a bill reference
-- ORDER BY r.created_at DESC;

-- 11b. Orphan Type 2: Bill saved with POS payment but no POS record
--      (Billing record exists but somehow POS log is missing)
-- SELECT bp.payment_id, bp.bill_est_id, bp.paid_amount,
--        bp.pos_req_id, e.bill_est_no
-- FROM ret_billing_payment bp
-- LEFT JOIN ret_pos_requests r ON r.pos_req_id = bp.pos_req_id
-- JOIN ret_estimation e ON e.bill_est_id = bp.bill_est_id
-- WHERE bp.pos_req_id IS NOT NULL               -- Claims to be POS payment
--   AND r.pos_req_id IS NULL                    -- But no POS record found
-- ORDER BY bp.payment_id DESC;

-- 11c. Stale Sessions: Sessions stuck in INIT/PENDING past expiry
-- SELECT s.session_id, s.invoice_id, s.amount_paise/100 as amount_rs,
--        s.status, s.created_at, s.expires_at,
--        d.dispname as device_name
-- FROM pos_payment_sessions s
-- LEFT JOIN ret_pos_device_list d ON d.id_device = s.device_id
-- WHERE s.status IN ('INIT', 'PENDING')
--   AND s.expires_at < NOW()
-- ORDER BY s.created_at DESC;

-- -----------------------------------------------
-- 12. SETTLEMENT TABLE
--     Tracks money received from provider into bank
-- -----------------------------------------------

CREATE TABLE IF NOT EXISTS `pos_settlements` (
    `settlement_id`      INT NOT NULL AUTO_INCREMENT,
    `provider_code`      VARCHAR(30) NOT NULL COMMENT 'pinelabs/phonepe_dqr/phonepe_iedc',
    `provider_batch_id`  VARCHAR(100) DEFAULT NULL COMMENT 'Provider settlement batch reference',
    `settlement_date`    DATE NOT NULL COMMENT 'Date funds received',
    `total_transactions` INT DEFAULT 0,
    `total_amount_paise` BIGINT DEFAULT 0 COMMENT 'Total settlement amount in paise',
    `commission_paise`   BIGINT DEFAULT 0 COMMENT 'Provider commission/MDR deducted',
    `net_amount_paise`   BIGINT DEFAULT 0 COMMENT 'Net amount received in bank',
    `bank_reference`     VARCHAR(100) DEFAULT NULL COMMENT 'Bank UTR/NEFT reference',
    `id_bank`            INT DEFAULT NULL COMMENT 'FK to bank table — which account received',
    `status`             ENUM('PENDING','RECONCILED','DISPUTED') DEFAULT 'PENDING',
    `notes`              TEXT DEFAULT NULL,
    `created_by`         INT DEFAULT NULL,
    `created_at`         DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`         DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`settlement_id`),
    INDEX `idx_settle_provider` (`provider_code`, `settlement_date`),
    INDEX `idx_settle_date` (`settlement_date`),
    INDEX `idx_settle_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='POS settlement tracking — links provider payouts to bank';

-- -----------------------------------------------
-- 13. SETTLEMENT LINK on ret_pos_requests
--     Links individual transactions to settlement batch
-- -----------------------------------------------

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'settlement_id');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN settlement_id INT DEFAULT NULL COMMENT ''FK to pos_settlements'' AFTER pos_last_checked_at',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'settled_at');
SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE ret_pos_requests ADD COLUMN settled_at DATE DEFAULT NULL COMMENT ''Date this transaction was settled'' AFTER settlement_id',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------
-- 14. USEFUL REPORTING QUERIES
-- -----------------------------------------------

-- 14a. Daily POS summary by device and provider
-- SELECT DATE(r.created_at) as txn_date,
--        IFNULL(d.dispname, 'Unknown') as device,
--        r.provider_code,
--        COUNT(*) as total,
--        SUM(CASE WHEN r.pos_req_status=1 THEN 1 ELSE 0 END) as success,
--        SUM(CASE WHEN r.pos_req_status=2 THEN 1 ELSE 0 END) as cancelled,
--        SUM(CASE WHEN r.pos_req_status=3 THEN 1 ELSE 0 END) as failed,
--        SUM(CASE WHEN r.pos_req_status=1 THEN r.pos_req_amount ELSE 0 END)/100 as total_success_amount
-- FROM ret_pos_requests r
-- LEFT JOIN ret_pos_device_list d ON d.id_device = r.id_device
-- WHERE r.created_at >= CURDATE() - INTERVAL 7 DAY
-- GROUP BY txn_date, device, r.provider_code
-- ORDER BY txn_date DESC, device;

-- 14b. Payment mode breakdown
-- SELECT r.payment_mode,
--        COUNT(*) as count,
--        SUM(r.pos_req_amount)/100 as total_amount
-- FROM ret_pos_requests r
-- WHERE r.pos_req_status = 1
--   AND r.created_at >= CURDATE() - INTERVAL 30 DAY
-- GROUP BY r.payment_mode;

-- 14c. Unsettled transactions (awaiting bank credit)
-- SELECT r.pos_req_id, r.pos_trans_no, r.pos_req_amount/100 as amount,
--        r.provider_code, r.pos_utr, r.created_at
-- FROM ret_pos_requests r
-- WHERE r.pos_req_status = 1
--   AND r.settlement_id IS NULL
--   AND r.created_at < CURDATE() - INTERVAL 2 DAY
-- ORDER BY r.created_at;

-- -----------------------------------------------
-- VERIFICATION: Updated checks (Phase 1 + 2)
-- -----------------------------------------------
SHOW TABLES LIKE 'ret_pos%';
SHOW TABLES LIKE 'pos_%';
DESCRIBE ret_pos_providers;
DESCRIBE ret_pos_device_list;
DESCRIBE ret_pos_requests;
DESCRIBE pos_payment_sessions;
DESCRIBE pos_settlements;
SELECT * FROM ret_settings WHERE name = 'pay_by_pos';

-- =======================================================
-- POS MODULE — COMPLETE TABLE & COLUMN SUMMARY
-- Updated: 14-03-2026 (Phase 1 + Phase 2)
-- =======================================================
-- 
-- TABLES:
-- ┌─────────────────────────┬───────────────────────────────────────────────┐
-- │ Table                   │ Purpose                                       │
-- ├─────────────────────────┼───────────────────────────────────────────────┤
-- │ ret_pos_providers       │ Provider definitions (Pine Labs, PhonePe...)  │
-- │ ret_pos_device_list     │ Device/terminal configurations               │
-- │ ret_pos_requests        │ Transaction log (Layer 3)                    │
-- │ pos_payment_sessions    │ Payment session lock (Layer 2)               │
-- │ pos_settlements         │ Settlement tracking (Layer 5)                │
-- │ ret_billing_payment     │ Bill payment record (Layer 4, existing)      │
-- └─────────────────────────┴───────────────────────────────────────────────┘
-- 
-- ARCHITECTURE (4-Layer Model):
--   pos_devices → pos_payment_sessions → pos_transactions → bill_payments → pos_settlements
--   (Layer 1)     (Layer 2)               (Layer 3)          (Layer 4)       (Layer 5)
-- 
-- ret_pos_requests COLUMNS (Phase 1 additions marked with ★):
--   pos_req_id, pos_trans_no, pos_store_pos_code, pos_req_amount,
--   pos_usr_id, pos_mer_id, pos_imie, pos_req_createdby,
--   pos_req_bill_cusid, pos_req_bill_id, pos_res_ref_id, pos_res_trans_data,
--   pos_req_status, pos_cancelled_by★, pos_cancelled_at★,
--   pos_last_checked_by★, pos_last_checked_at★,
--   created_at, updated_at★, id_provider, id_device★, provider_code★,
--   pos_qr_string, pos_callback_data, idempotency_key,
--   pos_utr, payment_mode★, card_last4★, approval_code★,
--   pos_req_payload, pos_res_payload, pos_req_created_at,
--   settlement_id★, settled_at★
-- 
-- INDEXES on ret_pos_requests:
--   idx_idempotency_key (UNIQUE), idx_pos_device, idx_pos_status_date,
--   idx_pos_ref_id, idx_pos_bill_id, idx_pos_provider
-- 
-- KEY CHANGES FROM PHASE 1:
-- • Added 12 new columns to ret_pos_requests (device, audit, receipt, settlement)
-- • Fixed silent audit trail failure (pos_cancelled_by/pos_last_checked_by columns)
-- • All provider plugins now store id_device, provider_code, payment_mode
-- • Model queries JOIN on id_device (not fragile merchantid match)
-- • 5 performance indexes added
-- • Settlement table created for bank reconciliation
-- 
-- =======================================================
