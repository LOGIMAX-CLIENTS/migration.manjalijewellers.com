-- Migration: Alter table ret_po_qc_issue_process — add id_reason column
-- Author: DEVADHARSHINI
-- Safe: Non-destructive ALTER (adds nullable column)

-- UP
ALTER TABLE `ret_po_qc_issue_process` ADD `id_reason` INT(11) NULL DEFAULT NULL AFTER `qc_status`;
