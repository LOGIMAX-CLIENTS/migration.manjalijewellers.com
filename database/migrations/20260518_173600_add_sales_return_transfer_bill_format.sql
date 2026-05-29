-- Migration: Add Sales Return Transfer (bill_type 14) to bill_no_format
-- Safe: Uses NOT EXISTS to prevent duplicates
-- UP

INSERT INTO `bill_no_format` (`bill_type`, `bill_no_format`, `created_on`)
SELECT 14, '-@@branch_code@@-@@fin_year@@-@@short_code@@-@@bill_no@@', NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `bill_no_format` WHERE `bill_type` = 14
);
