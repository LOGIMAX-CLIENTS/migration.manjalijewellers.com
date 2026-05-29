-- Migration: Alter table ret_estimation_items and ret_bill_details — add pure_wt and pure_rate columns
-- Author: NAMBI MUTHU RAJA
-- Safe: Non-destructive ALTER (adds nullable column)

-- UP

ALTER TABLE ret_estimation_items ADD pure_wt INT(11) NULL DEFAULT NULL ;

ALTER TABLE ret_estimation_items ADD pure_rate INT(11) NULL DEFAULT NULL;

ALTER TABLE ret_bill_details ADD pure_wt INT(11) NULL DEFAULT NULL;