-- UP
ALTER TABLE `profile` ADD COLUMN `allow_ir_cancel` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No';
ALTER TABLE `profile` ADD COLUMN `previous_ir_cancel` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No';

-- DOWN
ALTER TABLE `profile` DROP COLUMN `allow_ir_cancel`;
ALTER TABLE `profile` DROP COLUMN `previous_ir_cancel`;
