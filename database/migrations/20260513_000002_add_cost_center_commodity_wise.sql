-- Migration: Update cost_center column comment to include new value 4 (Multi-Cost Center Commodity Wise Accounts)
-- Date: 13-05-2026
-- Branch: feature/commodity-based-gateway
-- Safe: MODIFY COLUMN — Only changes the COMMENT, no data type or default change

-- UP
ALTER TABLE `chit_settings`
  MODIFY COLUMN `cost_center` TINYINT(1) NOT NULL DEFAULT '1'
  COMMENT '1 -> Single, 2 -> Multi-Cost Center [Branch-wise Customer], 3 -> Multi-Cost Center [Single Customer Ac for multi branch], 4 -> Multi-Cost Center [Commodity Wise Accounts]';

-- DOWN
-- ALTER TABLE `chit_settings`
--   MODIFY COLUMN `cost_center` TINYINT(1) NOT NULL DEFAULT '1'
--   COMMENT '1 -> Single, 2 -> Multi-Cost Center [Branch-wise Customer], 3 -> Multi-Cost Center [Single Customer Ac for multi branch]';
