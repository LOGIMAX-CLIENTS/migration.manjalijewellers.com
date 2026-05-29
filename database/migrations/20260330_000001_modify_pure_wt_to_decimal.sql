-- Migration: Modify pure_wt column to DECIMAL(10,3) in ret_estimation_items and ret_bill_details for precision rounding
-- Author: NAMBI MUTHU RAJA
-- Safe: Modifies column type (INT to DECIMAL)

-- UP

/* For Estimation Items */
ALTER TABLE ret_estimation_items MODIFY pure_wt DECIMAL(10,3) NULL DEFAULT NULL;

/* For Bill Details */
ALTER TABLE ret_bill_details MODIFY pure_wt DECIMAL(10,3) NULL DEFAULT NULL;
