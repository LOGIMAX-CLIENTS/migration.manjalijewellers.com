-- Migration: ret_view_smith_ledger
-- Date: 2026-04-10
-- Description: seperate grn flag based smith ledger viw query
-- UP

create view `ret_view_smith_ledger_2026_04_10` as select * from `ret_view_smith_ledger`;
create or replace view `ret_view_smith_ledger` as 
SELECT
    `pd`.`design_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    SUM(
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `pitm`.`gross_wt`,
            ROUND(
                `pitm`.`gross_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `gross_wt`,
    SUM(
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `pitm`.`net_wt`,
            ROUND(
                `pitm`.`net_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `net_wt`,
    SUM(`pitm`.`no_of_pcs`) AS `no_of_pcs`,
    `pitm`.`purchase_touch` AS `purchase_touch`,
    SUM(`pitm`.`item_pure_wt`) AS `purewt`,
    `po`.`po_karigar_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    IFNULL(`pochr`.`charge`, 0) + IFNULL(`post`.`stamount`, 0) + IF(
        `pitm`.`mc_type` = 1,
        `pitm`.`mc_value` * `pitm`.`gross_wt`,
        `pitm`.`mc_value` * `pitm`.`no_of_pcs`
    ) AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pr`.`stone_type` AS `stone_type`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`,
    `uom`.`uom_short_code` AS `dispuom`
FROM
    (
        (
            (
                (
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
                LEFT JOIN `ret_design_master` `pd`
                ON
                    (
                        `pd`.`design_no` = `pitm`.`po_item_des_id`
                    )
                )
            LEFT JOIN `ret_uom` `uom`
            ON
                (`uom`.`uom_id` = `pitm`.`uom`)
            )
        LEFT JOIN(
            SELECT
                SUM(
                    `ret_purchase_other_charges`.`total_charge_value`
                ) AS `charge`,
                `ret_purchase_other_charges`.`pur_po_item_id` AS `pur_po_item_id`
            FROM
                `ret_purchase_other_charges`
            GROUP BY
                `ret_purchase_other_charges`.`pur_po_item_id`
        ) `pochr`
    ON
        (
            `pochr`.`pur_po_item_id` = `pitm`.`po_item_id`
        )
        )
    LEFT JOIN(
        SELECT
            SUM(
                `ret_po_stone_items`.`po_stone_amount`
            ) AS `stamount`,
            `ret_po_stone_items`.`po_item_id` AS `po_item_id`
        FROM
            `ret_po_stone_items`
        GROUP BY
            `ret_po_stone_items`.`po_item_id`
    ) `post`
ON
    (
        `post`.`po_item_id` = `pitm`.`po_item_id`
    )
    )
WHERE
    `grn`.`grn_type` = 4 AND `po`.`is_approved` = 1 AND `po`.`bill_status` = 1
GROUP BY
    `pitm`.`po_item_id`
UNION
SELECT
    `pd`.`design_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `iss`.`met_issue_ref_id` AS `referenceno`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    SUM(
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `posi`.`po_stone_wt`,
            ROUND(
                `posi`.`po_stone_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `gross_wt`,
    SUM(
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `posi`.`po_stone_wt`,
            ROUND(
                `posi`.`po_stone_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `net_wt`,
    SUM(`posi`.`po_stone_pcs`) AS `no_of_pcs`,
    '' AS `purchase_touch`,
    '' AS `purewt`,
    `iss`.`met_issue_karid` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pr`.`stone_type` AS `stone_type`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`,
    `uom`.`uom_short_code` AS `dispuom`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    (
                                        (
                                            `ret_po_stone_items` `posi`
                                        LEFT JOIN `ret_karigar_metal_issue_details` `iitm`
                                        ON
                                            (
                                                `iitm`.`issue_met_id` = `posi`.`issue_met_id`
                                            )
                                        )
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
                LEFT JOIN `ret_design_master` `pd`
                ON
                    (
                        `pd`.`design_no` = `iitm`.`issu_met_id_design`
                    )
                )
            LEFT JOIN `ret_uom` `uom`
            ON
                (`uom`.`uom_id` = `posi`.`po_stone_uom`)
            )
        LEFT JOIN `ret_purchase_order_items` `pitm`
        ON
            (
                `pitm`.`po_item_id` = `posi`.`po_item_id`
            )
        )
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `pitm`.`po_item_po_id`)
    )
WHERE
    `posi`.`issue_met_id` IS NOT NULL AND `po`.`bill_status` = 1
GROUP BY
    `posi`.`po_st_id`
UNION
SELECT
    `pd`.`design_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `po`.`po_date` AS `trans_date`,
    `po`.`po_ref_no` AS `referenceno`,
    1 AS `trans_type`,
    `pitm`.`po_order_no` AS `trans_id`,
    SUM(
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `pitm`.`gross_wt`,
            ROUND(
                `pitm`.`gross_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `gross_wt`,
    SUM(
        IF(
            IFNULL(`uom`.`divided_by_value`, 0) = 0,
            `pitm`.`net_wt`,
            ROUND(
                `pitm`.`net_wt` / `uom`.`divided_by_value`,
                3
            )
        )
    ) AS `net_wt`,
    SUM(`pitm`.`no_of_pcs`) AS `no_of_pcs`,
    '' AS `purchase_touch`,
    '' AS `purewt`,
    `iss`.`met_issue_karid` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    `pitm`.`po_item_cat_id` AS `catid`,
    `pr`.`stone_type` AS `stone_type`,
    `pitm`.`po_item_pro_id` AS `proid`,
    1 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    IFNULL(`pitm`.`remark`, '') AS `narration`,
    UNIX_TIMESTAMP(`po`.`po_date`) AS `unixtransdate`,
    `uom`.`uom_short_code` AS `dispuom`
FROM
    (
        (
            (
                (
                    (
                        (
                            (
                                (
                                    (
                                        `ret_purchase_order_items` `pitm`
                                    LEFT JOIN `ret_karigar_metal_issue_details` `iitm`
                                    ON
                                        (
                                            `iitm`.`issue_met_id` = `pitm`.`issue_met_id`
                                        )
                                    )
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
            LEFT JOIN `ret_design_master` `pd`
            ON
                (
                    `pd`.`design_no` = `iitm`.`issu_met_id_design`
                )
            )
        LEFT JOIN `ret_uom` `uom`
        ON
            (`uom`.`uom_id` = `pitm`.`uom`)
        )
    LEFT JOIN `ret_purchase_order` `po`
    ON
        (`po`.`po_id` = `pitm`.`po_item_po_id`)
    )
WHERE
    `pitm`.`issue_met_id` IS NOT NULL AND `po`.`bill_status` = 1
GROUP BY
    `pitm`.`po_item_id`
UNION
SELECT
    `pd`.`design_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `iss`.`met_issue_date` AS `trans_date`,
    `iss`.`met_issue_ref_id` AS `referenceno`,
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
    `pr`.`stone_type` AS `stone_type`,
    `iitm`.`issu_met_pro_id` AS `proid`,
    2 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`iss`.`met_issue_date`) AS `unixtransdate`,
    `uom`.`uom_short_code` AS `dispuom`
FROM
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
        LEFT JOIN `ret_design_master` `pd`
        ON
            (
                `pd`.`design_no` = `iitm`.`issu_met_id_design`
            )
        )
    LEFT JOIN `ret_uom` `uom`
    ON
        (`uom`.`uom_id` = `iitm`.`issue_uom_id`)
    )
WHERE
    (
        `kr`.`karigar_for` = 1 OR `kr`.`karigar_for` = 5
    ) AND `iss`.`bill_status` = 1
GROUP BY
    `iitm`.`issue_cat_id`,
    `iitm`.`issu_met_pro_id`,
    `iitm`.`issue_met_parent_id`
UNION
SELECT
    'PAYMENT' AS `category`,
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
    0 AS `stone_type`,
    '' AS `proid`,
    3 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`pay`.`pay_create_on`) AS `unixtransdate`,
    '' AS `dispuom`
FROM
    (
        `ret_po_payment` `pay`
    LEFT JOIN `ret_po_payment_detail` `pd`
    ON
        (`pd`.`pay_id` = `pay`.`pay_id`)
    )
WHERE
    `pay`.`pay_status` = 1 AND `pay`.`bill_type` = 2
GROUP BY
    `pay`.`pay_id`
UNION
SELECT
    `pd`.`design_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    SUM(`pret`.`pur_ret_pur_wt`) AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pret`.`pur_ret_debit_note_amt`) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pr`.`stone_type` AS `stone_type`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`,
    '' AS `dispuom`
FROM
    (
        (
            (
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
                                        LEFT JOIN(
                                            SELECT
                                                SUM(
                                                    `ret_purchase_return_other_charges`.`total_charge_value`
                                                ) AS `charges`,
                                                `ret_purchase_return_other_charges`.`pur_ret_itm_id` AS `pur_ret_itm_id`
                                            FROM
                                                `ret_purchase_return_other_charges`
                                            GROUP BY
                                                `ret_purchase_return_other_charges`.`pur_ret_itm_id`
                                        ) `retchr`
                                    ON
                                        (
                                            `retchr`.`pur_ret_itm_id` = `pret`.`pur_ret_itm_id`
                                        )
                                        )
                                    LEFT JOIN(
                                        SELECT
                                            SUM(
                                                `ret_purchase_return_stone_items`.`ret_stone_amount`
                                            ) AS `stamount`,
                                            `ret_purchase_return_stone_items`.`pur_ret_return_id` AS `pur_ret_return_id`
                                        FROM
                                            `ret_purchase_return_stone_items`
                                        GROUP BY
                                            `ret_purchase_return_stone_items`.`pur_ret_return_id`
                                    ) `retst`
                                ON
                                    (
                                        `retst`.`pur_ret_return_id` = `pret`.`pur_ret_itm_id`
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
            LEFT JOIN `ret_design_master` `pd`
            ON
                (`pd`.`design_no` = `pret`.`id_design`)
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
    `ret`.`pur_ret_convert_to` = 2 AND `ret`.`purchase_type` = 0 AND `grn`.`grn_type` = 4 AND `ret`.`bill_status` = 1
GROUP BY
    `pret`.`pur_ret_itm_id`
UNION
SELECT
    `pd`.`design_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ret`.`bill_date` AS `trans_date`,
    `ret`.`pur_ret_ref_no` AS `referenceno`,
    2 AS `trans_type`,
    `ret`.`pur_return_id` AS `trans_id`,
    SUM(`pret`.`pur_ret_gwt`) AS `gross_wt`,
    SUM(`pret`.`pur_ret_nwt`) AS `net_wt`,
    SUM(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,
    `pret`.`pur_ret_purchase_touch` AS `purchase_touch`,
    SUM(`pret`.`pur_ret_pur_wt`) AS `purewt`,
    `ret`.`pur_ret_supplier_id` AS `customer_id`,
    1 AS `trans_rec_type`,
    SUM(`pret`.`pur_ret_debit_note_amt`) AS `trans_amount`,
    `cat`.`id_ret_category` AS `catid`,
    `pr`.`stone_type` AS `stone_type`,
    `pret`.`id_product` AS `proid`,
    5 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ret`.`bill_date`) AS `unixtransdate`,
    '' AS `dispuom`
FROM
    (
        (
            (
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
                                    LEFT JOIN(
                                        SELECT
                                            SUM(
                                                `ret_purchase_return_other_charges`.`total_charge_value`
                                            ) AS `charges`,
                                            `ret_purchase_return_other_charges`.`pur_ret_itm_id` AS `pur_ret_itm_id`
                                        FROM
                                            `ret_purchase_return_other_charges`
                                        GROUP BY
                                            `ret_purchase_return_other_charges`.`pur_ret_itm_id`
                                    ) `retchr`
                                ON
                                    (
                                        `retchr`.`pur_ret_itm_id` = `pret`.`pur_ret_itm_id`
                                    )
                                    )
                                LEFT JOIN(
                                    SELECT
                                        SUM(
                                            `ret_purchase_return_stone_items`.`ret_stone_amount`
                                        ) AS `stamount`,
                                        `ret_purchase_return_stone_items`.`pur_ret_return_id` AS `pur_ret_return_id`
                                    FROM
                                        `ret_purchase_return_stone_items`
                                    GROUP BY
                                        `ret_purchase_return_stone_items`.`pur_ret_return_id`
                                ) `retst`
                            ON
                                (
                                    `retst`.`pur_ret_return_id` = `pret`.`pur_ret_itm_id`
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
            LEFT JOIN `ret_design_master` `pd`
            ON
                (`pd`.`design_no` = `pret`.`id_design`)
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
    `ret`.`pur_ret_convert_to` = 2 AND `grn`.`grn_type` = 4 AND `ret`.`bill_status` = 1
GROUP BY
    `ret`.`pur_return_id`
UNION
SELECT
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
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
    0 AS `stone_type`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`,
    '' AS `dispuom`
FROM
    `smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 2 AND `pay`.`stock_type` = 2 AND `pay`.`amount` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION
SELECT
    'OPENING' AS `category`,
    '' AS `product`,
    `pay`.`createdon` AS `trans_date`,
    `pay`.`id_smith_company_op_balance` AS `referenceno`,
    `pay`.`weight_type` AS `trans_type`,
    `pay`.`id_smith_company_op_balance` AS `trans_id`,
    IFNULL(`pay`.`weight`, 0) AS `gross_wt`,
    IFNULL(`pay`.`weight`, 0) AS `net_wt`,
    0 AS `no_of_pcs`,
    100 AS `purchase_touch`,
    IFNULL(`pay`.`weight`, 0) AS `purewt`,
    `pay`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    '' AS `catid`,
    0 AS `stone_type`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`pay`.`createdon`) AS `unixtransdate`,
    '' AS `dispuom`
FROM
    `smith_company_op_balance` `pay`
WHERE
    `pay`.`smith_type` = 2 AND `pay`.`stock_type` = 2 AND `pay`.`weight` > 0
GROUP BY
    `pay`.`id_smith_company_op_balance`
UNION
SELECT
    IF(
        `pay`.`transtype` = 1,
        'Credit Note',
        'Debit Note'
    ) AS `category`,
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
    0 AS `stone_type`,
    '' AS `proid`,
    1 AS `trans_screen_id`,
    '' AS `id_metal`,
    '' AS `metal`,
    '' AS `rate`,
    `pay`.`naration` AS `narration`,
    UNIX_TIMESTAMP(`pay`.`transdate`) AS `unixtransdate`,
    '' AS `dispuom`
FROM
    `ret_crdr_note` `pay`
WHERE
    `pay`.`accountto` = 2 AND `pay`.`transamount` > 0 AND `pay`.`crdr_status` = 1
GROUP BY
    `pay`.`crdrid`
UNION
SELECT
    `pd`.`design_name` AS `category`,
    `pr`.`product_name` AS `product`,
    `ki`.`met_issue_date` AS `trans_date`,
    `iss`.`ref_no` AS `referenceno`,
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
    `iss`.`id_karigar` AS `customer_id`,
    1 AS `trans_rec_type`,
    0 AS `trans_amount`,
    `iitm`.`issue_cat_id` AS `catid`,
    `pr`.`stone_type` AS `stone_type`,
    `iitm`.`issu_met_pro_id` AS `proid`,
    2 AS `trans_screen_id`,
    `met`.`id_metal` AS `id_metal`,
    `met`.`metal` AS `metal`,
    '' AS `rate`,
    '' AS `narration`,
    UNIX_TIMESTAMP(`ki`.`met_issue_date`) AS `unixtransdate`,
    `uom`.`uom_short_code` AS `dispuom`
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
                                LEFT JOIN `ret_karigar_metal_issue` `ki`
                                ON
                                    (
                                        `ki`.`met_issue_id` = `iitm`.`issue_met_parent_id`
                                    )
                                )
                            LEFT JOIN `smith_company_op_balance` `iss`
                            ON
                                (
                                    `iss`.`id_smith_company_op_balance` = `iitm`.`id_smith_company_op_balance`
                                )
                            )
                        LEFT JOIN `ret_karigar` `kr`
                        ON
                            (`kr`.`id_karigar` = `iss`.`id_karigar`)
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
        LEFT JOIN `ret_design_master` `pd`
        ON
            (
                `pd`.`design_no` = `iitm`.`issu_met_id_design`
            )
        )
    LEFT JOIN `ret_uom` `uom`
    ON
        (`uom`.`uom_id` = `iitm`.`issue_uom_id`)
    )
WHERE
    `iitm`.`id_smith_company_op_balance` IS NOT NULL AND `ki`.`bill_status` = 1
GROUP BY
    `iitm`.`issue_cat_id`,
    `iitm`.`issu_met_pro_id`,
    `iitm`.`issue_met_id`
ORDER BY
    `unixtransdate`;