-- Migration: Add rate_fix_id to ret_po_bill_payment_details + Update supplier ledger view
-- Date: 22-05-2026
-- Issue: Rate Fixed PO Numbers not showing in Supplier Payment form.
--        The Supplier Payment save logic needs to store which rate_fix_id a payment is against,
--        so the payment can be linked back to the rate fixing entry for balance calculation.
-- Impact: Enables rate fix entries (from ret_po_rate_fix) to appear in Supplier Payment dropdown
--         and correctly track paid amounts against them.

-- UP: Step 1 — Add rate_fix_id column (safe to re-run — uses IF NOT EXISTS via procedure)
-- If column already exists, this will silently skip.

DROP PROCEDURE IF EXISTS add_rate_fix_id_column;
DELIMITER $$
CREATE PROCEDURE add_rate_fix_id_column()
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'ret_po_bill_payment_details'
          AND COLUMN_NAME = 'rate_fix_id'
    ) THEN
        ALTER TABLE `ret_po_bill_payment_details`
        ADD COLUMN `rate_fix_id` INT(11) DEFAULT NULL COMMENT 'FK to ret_po_rate_fix.rate_fix_id for Rate Fix payment entries' AFTER `crdrid`;
    END IF;
END$$
DELIMITER ;
CALL add_rate_fix_id_column();
DROP PROCEDURE IF EXISTS add_rate_fix_id_column;


--   UP: Step 2 — Update supplier ledger view
--   Changes:
--   Section 7 (Supplier Rate Cut): Added conversion_type = 1 filter to show only direct rate cuts
--   Section 7b (NEW): Added conversion_type = 2 section for unfix rate cuts (separated from direct)
--   This matches the model query changes in ret_purchase_order_model.php

-- Backup current view first
DROP VIEW IF EXISTS ret_view_supplier_ledger_2026_05_22;
CREATE VIEW ret_view_supplier_ledger_2026_05_22 AS SELECT * FROM ret_view_supplier_ledger;

CREATE OR REPLACE VIEW ret_view_supplier_ledger AS
-- 1. Purchase Order Items (grn_type NOT IN (2,4) — excludes smith receipt & job work)
select ifnull(pr.product_name,'Product Unavailable') AS category,po.po_id AS po_id,met.id_metal AS id_metal,ifnull(pr.product_name,'') AS product,po.po_date AS trans_date,po.po_ref_no AS referenceno,1 AS trans_type,pitm.po_order_no AS trans_id,pitm.gross_wt AS gross_wt,pitm.net_wt AS net_wt,pitm.no_of_pcs AS no_of_pcs,pitm.purchase_touch AS purchase_touch,pitm.item_pure_wt AS purewt,po.po_karigar_id AS customer_id,2 AS trans_rec_type,sum(pitm.item_cost) AS trans_amount,pitm.po_item_cat_id AS catid,pitm.po_item_pro_id AS proid,1 AS trans_screen_id,met.metal AS metal,concat(pitm.fix_rate_per_grm,if(pitm.is_rate_fixed = 1,'','(Un Fixed)')) AS rate,ifnull(pitm.remark,'') AS narration,unix_timestamp(po.po_date) AS unixtransdate
from ((((((ret_purchase_order_items pitm left join ret_purchase_order po on(po.po_id = pitm.po_item_po_id)) left join ret_grn_entry grn on(grn.grn_id = po.po_grn_id)) left join ret_karigar kr on(kr.id_karigar = po.po_karigar_id)) left join ret_category cat on(cat.id_ret_category = pitm.po_item_cat_id)) left join metal met on(met.id_metal = cat.id_metal)) left join ret_product_master pr on(pr.pro_id = pitm.po_item_pro_id))
where grn.grn_type NOT IN (2,4) and po.is_approved = 1 and po.bill_status = 1 and po.is_suspense_stock = 0
group by pitm.po_item_id

UNION ALL

-- 2. Payments
select 'PAYMENT' AS category,ifnull(bill.po_id,'') AS po_id,cat.id_metal AS id_metal,'' AS product,pay.pay_create_on AS trans_date,pay.pay_refno AS referenceno,2 AS trans_type,pay.pay_id AS trans_id,0 AS gross_wt,0 AS net_wt,0 AS no_of_pcs,'' AS purchase_touch,0 AS purewt,pay.pay_sup_id AS customer_id,2 AS trans_rec_type,sum(pd.payment_amount) AS trans_amount,'' AS catid,'' AS proid,3 AS trans_screen_id,'' AS metal,'' AS rate,'' AS narration,unix_timestamp(pay.pay_create_on) AS unixtransdate
from ((((ret_po_payment pay left join ret_po_payment_detail pd on(pd.pay_id = pay.pay_id)) left join ret_po_bill_payment_details bill on(bill.pay_id = pay.pay_id)) left join (select ret_purchase_order_items.po_item_po_id AS po_item_po_id,ret_purchase_order_items.po_item_cat_id AS po_item_cat_id from ret_purchase_order_items group by ret_purchase_order_items.po_item_po_id) po on(bill.po_id = po.po_item_po_id)) left join ret_category cat on(cat.id_ret_category = po.po_item_cat_id))
where pay.pay_status = 1 and pay.bill_type = 1
group by pay.pay_id

UNION ALL

-- 3. Rate Fixing (PO based)
select concat('RATE FIXING',if(pitm.rate > rf.rate_fix_rate,'(Dr)','(Cr)')) AS category,ifnull(po.po_id,'') AS po_id,pitm.id_metal AS id_metal,'' AS product,rf.rate_fix_created_on AS trans_date,rf.rate_fix_id AS referenceno,if(pitm.rate > rf.rate_fix_rate,2,1) AS trans_type,rf.rate_fix_id AS trans_id,'' AS gross_wt,'' AS net_wt,'' AS no_of_pcs,'' AS purchase_touch,'' AS purewt,po.po_karigar_id AS customer_id,2 AS trans_rec_type,round(abs((pitm.rate - rf.rate_fix_rate) * rf.rate_fix_wt * 1.03),2) AS trans_amount,'' AS catid,'' AS proid,7 AS trans_screen_id,'' AS metal,rf.rate_fix_rate AS rate,'' AS narration,unix_timestamp(rf.rate_fix_created_on) AS unixtransdate
from (((ret_po_rate_fix rf left join ret_purchase_order po on(po.po_id = rf.rate_fix_po_item_id)) left join (select pitm.po_item_po_id AS poid,pitm.fix_rate_per_grm AS rate,cate.id_metal AS id_metal from (ret_purchase_order_items pitm left join ret_category cate on(cate.id_ret_category = pitm.po_item_cat_id)) group by pitm.po_item_po_id) pitm on(pitm.poid = po.po_id)) left join ret_karigar kr on(kr.id_karigar = po.po_karigar_id))
where po.isratefixed = 0 and po.is_suspense_stock = 0 and rf.bill_status = 1 and rf.is_approved = 1
group by rf.rate_fix_id

UNION ALL

-- 4. Rate Fixing (Approval/Rate Cut based)
select concat('RATE FIXING',if(rc.rate_per_gram > rf.rate_fix_rate,'(Dr)','(Cr)')) AS category,ifnull(rc.po_id,'') AS po_id,rc.id_metal AS id_metal,'' AS product,rf.rate_fix_created_on AS trans_date,rf.rate_fix_id AS referenceno,if(rc.rate_per_gram > rf.rate_fix_rate,2,1) AS trans_type,rf.rate_fix_id AS trans_id,rc.weight AS gross_wt,rc.weight AS net_wt,'' AS no_of_pcs,'' AS purchase_touch,rc.weight AS purewt,rc.id_karigar AS customer_id,2 AS trans_rec_type,round(abs((rc.rate_per_gram - rf.rate_fix_rate) * rf.rate_fix_wt * 1.03),2) AS trans_amount,'' AS catid,'' AS proid,7 AS trans_screen_id,'' AS metal,rf.rate_fix_rate AS rate,'' AS narration,unix_timestamp(rf.rate_fix_created_on) AS unixtransdate
from (ret_po_rate_fix rf left join ret_supplier_rate_cut rc on(rc.id_supplier_rate_cut = rf.id_approval_ratecut))
where rf.rate_fix_type = 2 and rf.bill_status = 1 and rf.is_approved = 1
group by rf.rate_fix_id

UNION ALL

-- 5. Purchase Return (type 0)
select ifnull(pr.product_name,'Product Unavailable') AS category,ifnull(po.po_id,'') AS po_id,met.id_metal AS id_metal,ifnull(pr.product_name,'') AS product,ret.bill_date AS trans_date,ret.pur_ret_ref_no AS referenceno,2 AS trans_type,ret.pur_return_id AS trans_id,sum(pret.pur_ret_gwt) AS gross_wt,sum(pret.pur_ret_nwt) AS net_wt,sum(pret.pur_ret_pcs) AS no_of_pcs,pret.pur_ret_purchase_touch AS purchase_touch,pret.pur_ret_pur_wt AS purewt,ret.pur_ret_supplier_id AS customer_id,2 AS trans_rec_type,sum(ifnull(pret.pur_ret_debit_note_amt,0)) AS trans_amount,cat.id_ret_category AS catid,pret.id_product AS proid,5 AS trans_screen_id,met.metal AS metal,'' AS rate,'' AS narration,unix_timestamp(ret.bill_date) AS unixtransdate
from ((((((((ret_purchase_return_items pret left join ret_purchase_return ret on(ret.pur_return_id = pret.pur_ret_id)) left join ret_purchase_order_items pitm on(pitm.po_item_id = pret.pur_ret_po_item_id)) left join ret_purchase_order po on(po.po_id = pitm.po_item_po_id)) left join ret_grn_entry grn on(grn.grn_id = po.po_grn_id)) left join ret_karigar kr on(kr.id_karigar = ret.pur_ret_supplier_id)) left join ret_product_master pr on(pr.pro_id = pret.id_product)) left join ret_category cat on(cat.id_ret_category = pr.cat_id)) left join metal met on(met.id_metal = cat.id_metal))
where ret.pur_ret_convert_to = 1 and ret.purchase_type = 0 and ret.bill_status = 1
group by pret.pur_ret_itm_id

UNION ALL

-- 6. Purchase Return (type 1)
select ifnull(pr.product_name,'Product Unavailable') AS category,ifnull(po.po_id,'') AS po_id,met.id_metal AS id_metal,ifnull(pr.product_name,'') AS product,ret.bill_date AS trans_date,ret.pur_ret_ref_no AS referenceno,2 AS trans_type,ret.pur_return_id AS trans_id,sum(pret.pur_ret_gwt) AS gross_wt,sum(pret.pur_ret_nwt) AS net_wt,sum(pret.pur_ret_pcs) AS no_of_pcs,pret.pur_ret_purchase_touch AS purchase_touch,pret.pur_ret_pur_wt AS purewt,ret.pur_ret_supplier_id AS customer_id,2 AS trans_rec_type,sum(ifnull(pret.pur_ret_debit_note_amt,0)) AS trans_amount,cat.id_ret_category AS catid,pret.id_product AS proid,5 AS trans_screen_id,met.metal AS metal,'' AS rate,'' AS narration,unix_timestamp(ret.bill_date) AS unixtransdate
from ((((((((ret_purchase_return_items pret left join ret_purchase_return ret on(ret.pur_return_id = pret.pur_ret_id)) left join ret_purchase_order_items pitm on(pitm.po_item_id = pret.pur_ret_po_item_id)) left join ret_purchase_order po on(po.po_id = pitm.po_item_po_id)) left join ret_grn_entry grn on(grn.grn_id = po.po_grn_id)) left join ret_karigar kr on(kr.id_karigar = ret.pur_ret_supplier_id)) left join ret_product_master pr on(pr.pro_id = pret.id_product)) left join ret_category cat on(cat.id_ret_category = pr.cat_id)) left join metal met on(met.id_metal = cat.id_metal))
where ret.pur_ret_convert_to = 1 and ret.purchase_type = 1 and ret.bill_status = 1
group by pret.pur_ret_itm_id

UNION ALL

-- 7. Rate Fixing (Supplier Rate Cut — conversion_type = 1 only, direct rate cuts)
-- CHANGED: Added conversion_type = 1 filter to exclude unfix entries (handled in 7b)
select ifnull(pr.product_name,'RATE FIXING') AS category,ifnull(rf.po_id,'') AS po_id,rf.id_metal AS id_metal,'' AS product,rf.date_add AS trans_date,rf.id_supplier_rate_cut AS referenceno,1 AS trans_type,rf.id_supplier_rate_cut AS trans_id,rf.weight AS gross_wt,rf.weight AS net_wt,'' AS no_of_pcs,'100' AS purchase_touch,rf.weight AS purewt,rf.id_karigar AS customer_id,1 AS trans_rec_type,rf.amount AS trans_amount,'' AS catid,'' AS proid,1 AS trans_screen_id,rf.id_metal AS metal,rf.rate_per_gram AS rate,ifnull(rf.narration,'') AS narration,unix_timestamp(rf.date_add) AS unixtransdate
from (ret_supplier_rate_cut rf left join ret_product_master pr on(pr.pro_id = rf.id_product))
where rf.rate_cut_type = 2 and rf.status = 1 and rf.conversion_type = 1
group by rf.id_supplier_rate_cut

UNION ALL

-- 7b. Rate Fixing (Supplier Rate Cut — conversion_type = 2, unfix entries)
-- NEW: Separated unfix entries into their own section for proper display
select ifnull(pr.product_name,'RATE FIXING') AS category,ifnull(rf.po_id,'') AS po_id,rf.id_metal AS id_metal,'' AS product,rf.date_add AS trans_date,rf.id_supplier_rate_cut AS referenceno,1 AS trans_type,rf.id_supplier_rate_cut AS trans_id,rf.weight AS gross_wt,rf.weight AS net_wt,'' AS no_of_pcs,'100' AS purchase_touch,rf.weight AS purewt,rf.id_karigar AS customer_id,1 AS trans_rec_type,rf.amount AS trans_amount,'' AS catid,'' AS proid,1 AS trans_screen_id,rf.id_metal AS metal,concat('(Unfix)',rf.rate_per_gram) AS rate,ifnull(rf.narration,'') AS narration,unix_timestamp(rf.date_add) AS unixtransdate
from (ret_supplier_rate_cut rf left join ret_product_master pr on(pr.pro_id = rf.id_product))
where rf.rate_cut_type = 2 and rf.status = 1 and rf.conversion_type = 2
group by rf.id_supplier_rate_cut

UNION ALL

-- 8. Opening Balance (Amount)
select 'OPENING' AS category,'' AS po_id,pay.id_metal AS id_metal,'' AS product,pay.createdon AS trans_date,pay.id_smith_company_op_balance AS referenceno,pay.amount_type AS trans_type,pay.id_smith_company_op_balance AS trans_id,pay.weight AS gross_wt,pay.net_wt AS net_wt,pay.pieces AS no_of_pcs,'' AS purchase_touch,pay.pure_wt AS purewt,pay.id_karigar AS customer_id,2 AS trans_rec_type,sum(pay.amount) AS trans_amount,'' AS catid,'' AS proid,3 AS trans_screen_id,'' AS metal,'' AS rate,ifnull(pay.remarks,'') AS narration,unix_timestamp(pay.createdon) AS unixtransdate
from smith_company_op_balance pay
where (pay.smith_type = 1 or pay.smith_type = 4) and pay.stock_type = 2 and pay.amount > 0
group by pay.id_smith_company_op_balance

UNION ALL

-- 9. Opening Balance (Weight)
select 'OPENING' AS category,'' AS po_id,pay.id_metal AS id_metal,'' AS product,pay.createdon AS trans_date,pay.id_smith_company_op_balance AS referenceno,pay.amount_type AS trans_type,pay.id_smith_company_op_balance AS trans_id,ifnull(pay.weight,0) AS gross_wt,ifnull(pay.net_wt,0) AS net_wt,pay.pieces AS no_of_pcs,'' AS purchase_touch,pay.pure_wt AS purewt,pay.id_karigar AS customer_id,1 AS trans_rec_type,0 AS trans_amount,'' AS catid,'' AS proid,3 AS trans_screen_id,'' AS metal,'' AS rate,ifnull(pay.remarks,'') AS narration,unix_timestamp(pay.createdon) AS unixtransdate
from smith_company_op_balance pay
where (pay.smith_type = 1 or pay.smith_type = 4) and pay.stock_type = 2 and pay.weight > 0
group by pay.id_smith_company_op_balance

UNION ALL

-- 10. Credit/Debit Notes
select if(pay.transtype = 1,'Credit Note','Debit Note') AS category,pay.po_id AS po_id,cat.id_metal AS id_metal,'' AS product,pay.transdate AS trans_date,pay.transbillno AS referenceno,pay.transtype AS trans_type,pay.transbillno AS trans_id,0 AS gross_wt,0 AS net_wt,0 AS no_of_pcs,'' AS purchase_touch,0 AS purewt,pay.supid AS customer_id,1 AS trans_rec_type,sum(pay.transamount) AS trans_amount,'' AS catid,'' AS proid,1 AS trans_screen_id,'' AS metal,'' AS rate,pay.naration AS narration,unix_timestamp(pay.transdate) AS unixtransdate
from ((ret_crdr_note pay left join (select ret_purchase_order_items.po_item_po_id AS po_item_po_id,ret_purchase_order_items.po_item_cat_id AS po_item_cat_id from ret_purchase_order_items group by ret_purchase_order_items.po_item_po_id) po on(pay.po_id = po.po_item_po_id)) left join ret_category cat on(cat.id_ret_category = po.po_item_cat_id))
where pay.accountto = 1 and pay.transamount > 0 and pay.crdr_status = 1
group by pay.crdrid

UNION ALL

-- 11. TDS (grn_type NOT IN (2,4) — excludes smith receipt & job work)
select 'TDS' AS category,po.po_id AS po_id,cat.id_metal AS id_metal,'' AS product,po.po_date AS trans_date,po.po_ref_no AS referenceno,2 AS trans_type,po.po_id AS trans_id,0 AS gross_wt,0 AS net_wt,0 AS no_of_pcs,'' AS purchase_touch,0 AS purewt,po.po_karigar_id AS customer_id,2 AS trans_rec_type,sum(po.tds_tax_value) AS trans_amount,'' AS catid,'' AS proid,1 AS trans_screen_id,'' AS metal,'' AS rate,'' AS narration,unix_timestamp(po.po_date) AS unixtransdate
from ((((ret_purchase_order po left join ret_grn_entry grn on(grn.grn_id = po.po_grn_id)) left join ret_karigar kr on(kr.id_karigar = po.po_karigar_id)) left join (select ret_purchase_order_items.po_item_po_id AS po_item_po_id,ret_purchase_order_items.po_item_cat_id AS po_item_cat_id from ret_purchase_order_items group by ret_purchase_order_items.po_item_po_id) item on(po.po_id = item.po_item_po_id)) left join ret_category cat on(cat.id_ret_category = item.po_item_cat_id))
where grn.grn_type NOT IN (2,4) and po.is_approved = 1 and po.bill_status = 1 and po.is_suspense_stock = 0 and po.tds_tax_value > 0
group by po.po_id

UNION ALL

-- 12. TCS (grn_type NOT IN (2,4) — excludes smith receipt & job work)
select 'TCS' AS category,po.po_id AS po_id,'' AS id_metal,'' AS product,po.po_date AS trans_date,po.po_ref_no AS referenceno,2 AS trans_type,po.po_id AS trans_id,0 AS gross_wt,0 AS net_wt,0 AS no_of_pcs,'' AS purchase_touch,0 AS purewt,po.po_karigar_id AS customer_id,2 AS trans_rec_type,sum(po.tcs_tax_value) AS trans_amount,'' AS catid,'' AS proid,1 AS trans_screen_id,'' AS metal,'' AS rate,'' AS narration,unix_timestamp(po.po_date) AS unixtransdate
from ((ret_purchase_order po left join ret_grn_entry grn on(grn.grn_id = po.po_grn_id)) left join ret_karigar kr on(kr.id_karigar = po.po_karigar_id))
where grn.grn_type NOT IN (2,4) and po.is_approved = 1 and po.bill_status = 1 and po.is_suspense_stock = 0 and po.tcs_tax_value > 0
group by po.po_id

ORDER BY unixtransdate DESC;
