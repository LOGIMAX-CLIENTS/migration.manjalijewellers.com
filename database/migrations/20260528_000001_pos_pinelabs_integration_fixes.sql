-- Migration: POS Pine Labs integration fixes — cleanup stale transactions
-- Date: 28-05-2026
-- Ticket: POS Pine Labs Integration (auto-expiry + approval code fix)
-- Changes:
--   1. Clean up stale PENDING transactions older than 5 minutes (Pine Labs auto-cancels after 2 min)
--   2. Fix false SUCCESS transactions that were marked without TransactionData
-- Note: No schema changes — all columns already exist from migrations 20260318/20260326

-- UP

-- 1. Fix pos_req_status default: was DEFAULT 1 (SUCCESS), should be DEFAULT 0 (PENDING)
--    Old default caused 'ghost success' — new records marked SUCCESS before payment confirmed
ALTER TABLE `ret_pos_requests` MODIFY `pos_req_status` TINYINT DEFAULT 0 COMMENT '0=PENDING, 1=SUCCESS, 2=CANCELLED, 3=FAILED/EXPIRED';

-- 1. Clean up stale PENDING transactions (status=0) older than 5 minutes
--    These are transactions where Pine Labs already auto-cancelled after AutoCancelDurationInMinutes
UPDATE `ret_pos_requests`
SET `pos_req_status` = 3
WHERE `pos_req_status` = 0
  AND `created_at` < DATE_SUB(NOW(), INTERVAL 5 MINUTE);

-- 2. Fix false SUCCESS transactions — marked as SUCCESS (1) but have no TransactionData
--    These were incorrectly marked during the INIT call instead of the STATUS callback
UPDATE `ret_pos_requests`
SET `pos_req_status` = 3
WHERE `pos_req_status` = 1
  AND (`pos_res_trans_data` IS NULL OR `pos_res_trans_data` = '' OR `pos_res_trans_data` = 'Array')
  AND `approval_code` IS NULL;

-- DOWN
-- Note: Cleanup UPDATEs cannot be reversed — stale transactions stay as FAILED (status=3)
