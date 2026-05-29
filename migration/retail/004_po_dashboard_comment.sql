-- Add dashboard_comment column to ret_purchase_order for inline comments on purchase dashboard
ALTER TABLE ret_purchase_order ADD COLUMN dashboard_comment TEXT DEFAULT NULL;
