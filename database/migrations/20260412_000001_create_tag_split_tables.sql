-- Migration: Create Partly Sale Tables (master + detail)
-- Feature: Partly Sale — physical tag splitting with weight distribution
-- Used by: ret_tag_parts_model.php, admin_ret_tag_parts.php
-- Safe: Uses IF NOT EXISTS
-- Updated: Phase 5 — Full rename: tables, PKs, columns

-- UP

-- ret_tag_parts_master — One row per partly sale event, freezes base-tag weight snapshot
CREATE TABLE IF NOT EXISTS `ret_tag_parts_master` (
    `parts_id`        INT(11)       NOT NULL AUTO_INCREMENT,
    `base_tag_id`     INT(11)       NOT NULL COMMENT 'FK ret_taging.tag_id',
    `parts_count`     TINYINT(3)    NOT NULL DEFAULT 2 COMMENT 'Number of child tags created',
    `parts_method`    TINYINT(1)    NOT NULL DEFAULT 1 COMMENT '1=Auto by Count (v1)',
    `base_gwt`        DECIMAL(10,4) NOT NULL DEFAULT 0.0000 COMMENT 'Frozen gross_wt at creation time',
    `base_nwt`        DECIMAL(10,4) NOT NULL DEFAULT 0.0000 COMMENT 'Frozen net_wt at creation time',
    `base_stone_wt`   DECIMAL(10,4) NOT NULL DEFAULT 0.0000 COMMENT 'Frozen stone wt at creation time',
    `base_dia_wt`     DECIMAL(10,4) NOT NULL DEFAULT 0.0000 COMMENT 'Frozen diamond wt at creation time',
    `base_less_wt`    DECIMAL(10,4) NOT NULL DEFAULT 0.0000 COMMENT 'Frozen less wt at creation time',
    `status`          TINYINT(1)    NOT NULL DEFAULT 1 COMMENT '1=Active, 2=Reverted, 3=Partial Billed, 4=Fully Billed',
    `revert_reason`   TINYINT(1)    DEFAULT NULL COMMENT '1=Manual, 2=EOD',
    `eod_ref_id`      INT(11)       DEFAULT NULL COMMENT 'FK to day-close log if EOD revert',
    `id_branch`       INT(11)       NOT NULL,
    `created_by`      INT(11)       NOT NULL,
    `created_on`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified_by`     INT(11)       DEFAULT NULL,
    `modified_on`     DATETIME      DEFAULT NULL,
    `reverted_by`     INT(11)       DEFAULT NULL,
    `reverted_on`     DATETIME      DEFAULT NULL,
    PRIMARY KEY (`parts_id`),
    KEY `idx_base_tag`   (`base_tag_id`),
    KEY `idx_branch`     (`id_branch`),
    KEY `idx_status`     (`status`),
    KEY `idx_created_on` (`created_on`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ret_tag_parts_detail — One row per child tag within a partly sale
CREATE TABLE IF NOT EXISTS `ret_tag_parts_detail` (
    `parts_detail_id` INT(11)       NOT NULL AUTO_INCREMENT,
    `parts_id`        INT(11)       NOT NULL COMMENT 'FK ret_tag_parts_master.parts_id',
    `child_tag_id`    INT(11)       NOT NULL COMMENT 'FK ret_taging.tag_id (newly created child)',
    `child_order`     TINYINT(3)    NOT NULL DEFAULT 1 COMMENT 'Sequence within this partly sale (1, 2, 3...)',
    `child_gwt`       DECIMAL(10,4) NOT NULL DEFAULT 0.0000,
    `child_nwt`       DECIMAL(10,4) NOT NULL DEFAULT 0.0000,
    `child_stone_wt`  DECIMAL(10,4) NOT NULL DEFAULT 0.0000,
    `child_dia_wt`    DECIMAL(10,4) NOT NULL DEFAULT 0.0000,
    `child_less_wt`   DECIMAL(10,4) NOT NULL DEFAULT 0.0000,
    `is_billed`       TINYINT(1)    NOT NULL DEFAULT 0 COMMENT '0=Unbilled, 1=Billed (locked)',
    `is_modified`     TINYINT(1)    NOT NULL DEFAULT 0 COMMENT '1=Admin modified weights',
    `modified_by`     INT(11)       DEFAULT NULL,
    `modified_on`     DATETIME      DEFAULT NULL,
    PRIMARY KEY (`parts_detail_id`),
    KEY `idx_parts_id`    (`parts_id`),
    KEY `idx_child_tag`   (`child_tag_id`),
    KEY `idx_is_billed`   (`is_billed`),
    CONSTRAINT `fk_parts_detail_master`
        FOREIGN KEY (`parts_id`) REFERENCES `ret_tag_parts_master` (`parts_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- DOWN
-- DROP TABLE IF EXISTS `ret_tag_parts_detail`;
-- DROP TABLE IF EXISTS `ret_tag_parts_master`;
