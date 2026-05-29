-- Migration: Add enable_pur_return_sales_return setting to ret_settings
-- Date: 2026-04-10
-- Description: Insert retail setting to control visibility of Sales Return radio button in Purchase Return form

-- UP

INSERT INTO `ret_settings` (`name`, `value`, `description`)
VALUES ('enable_pur_return_sales_return', '0', 'Enable Sales Return option in Purchase Return form');