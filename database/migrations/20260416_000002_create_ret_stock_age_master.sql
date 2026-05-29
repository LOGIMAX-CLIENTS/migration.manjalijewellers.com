-- ============================================================
-- Migration: Create ret_stock_age_master table and menu entry
-- Description: Creates the Stock Age Master table for managing
--              stock age range configurations, adds a menu entry
--              under Retail Catalog (id_menu=102), and grants
--              full access to all existing profiles.
-- Date: 16-04-2026
-- ============================================================

-- Step 1: Create the ret_stock_age_master table
-- UP
CREATE TABLE IF NOT EXISTS `ret_stock_age_master` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `age_from` INT(11) NOT NULL COMMENT 'Age range start (in days)',
    `age_to` INT(11) NOT NULL COMMENT 'Age range end (in days)',
    `value` VARCHAR(255) NOT NULL COMMENT 'Label or value for this age range',
    `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive',
    `created_by` INT(11) DEFAULT NULL,
    `created_date` DATETIME DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    `updated_date` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Step 2: Insert menu entry under Retail Catalog (parent=102)
INSERT INTO `menu` (`label`, `link`, `parent`, `sort`, `icon`, `active`)
VALUES ('Stock Age Master', 'admin_stock_age_master', 102, 13, 'fa fa-circle-o', 1);

-- Step 3: Get the newly inserted menu ID and grant access to all profiles
SET @new_menu_id = LAST_INSERT_ID();

INSERT INTO `access` (`id_profile`, `id_menu`, `view`, `add`, `edit`, `delete`)
SELECT p.id_profile, @new_menu_id, 1, 1, 1, 1
FROM `profile` p
WHERE NOT EXISTS (
    SELECT 1 FROM `access` a
    WHERE a.id_profile = p.id_profile AND a.id_menu = @new_menu_id
);
