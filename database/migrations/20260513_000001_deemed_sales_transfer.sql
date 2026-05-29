-- Deemed Sales Transfer — Schema Migration
-- Run this on retail_dev (and all client databases)
-- Adds salesTransType column to ret_bill_details to track transfer item type

ALTER TABLE ret_bill_details 
ADD COLUMN salesTransType TINYINT(1) NOT NULL DEFAULT 1 
COMMENT '1=Tagged, 2=Non-Tagged, 3=Old Gold, 4=Sales Return, 5=Partly Sold' 
AFTER item_type;

-- Also add lock columns for NT stock if not present
ALTER TABLE ret_nontag_item 
ADD COLUMN is_st_locked TINYINT(1) NOT NULL DEFAULT 0,
ADD COLUMN st_bill_id INT(11) DEFAULT NULL;

-- Add lock column for Old Gold if not present
ALTER TABLE ret_bill_old_metal_sale_details 
ADD COLUMN is_transferred TINYINT(1) NOT NULL DEFAULT 0,
ADD COLUMN st_bill_id INT(11) DEFAULT NULL;
