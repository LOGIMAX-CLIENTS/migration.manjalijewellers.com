-- Migration: Add column `allow_previous_rcpt_date` to `profile`
-- Purpose: Profile-based setting to control whether receipt date can be changed (0 = locked to dayclose date, 1 = allow date change)
-- Author: Antigravity

-- UP

ALTER TABLE `profile`
ADD `allow_previous_rcpt_date` TINYINT(1) NOT NULL DEFAULT 0
COMMENT '0 -> Locked to dayclose date, 1 -> Allow date change';