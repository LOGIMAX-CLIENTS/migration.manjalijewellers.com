-- Migration: Add pan_verification setting
-- Author: NAMBI MUTHU RAJA
-- Safe: Adds a new setting if it doesn't exist

-- UP
INSERT INTO `ret_settings` (`name`, `value`, `description`) 
SELECT 'pan_verification', '0', '0->Disable, 1->Enable'
WHERE NOT EXISTS (
    SELECT 1 FROM `ret_settings` WHERE `name` = 'pan_verification'
);
