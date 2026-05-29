-- Migration: Add show_chit_gift_in_bill setting
-- Author : Gokul K
-- Purpose: Controls whether gift issued details (from chit accounts) are shown in bill print copy

-- UP
INSERT INTO `ret_settings` (`name`, `value`, `description`) VALUES ('show_chit_gift_in_bill', '0', '0->Disable, 1->Enable - Show gift issued details in bill print when chit account is utilized');
