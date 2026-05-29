-- ================================================================================
-- Performance Optimization Script for Purchase Return Balance Calculations
-- Task ID: 124744000005249180
-- Created: 2026-02-12
-- Purpose: Add indexes to optimize purchase return subquery performance
-- ================================================================================

-- Database: srj
USE srj;

-- ================================================================================
-- SECTION 1: INDEX ANALYSIS
-- ================================================================================

-- Check current indexes on ret_purchase_return_items
SHOW INDEX FROM ret_purchase_return_items;

-- Check current indexes on ret_purchase_return
SHOW INDEX FROM ret_purchase_return;

-- Check current indexes on ret_purchase_order_items
SHOW INDEX FROM ret_purchase_order_items;

-- ================================================================================
-- SECTION 2: PERFORMANCE ANALYSIS (Before Optimization)
-- ================================================================================

-- Analyze the purchase return subquery performance
EXPLAIN SELECT 
    IFNULL(sum(rtn_itm.pur_ret_pur_wt), 0) as pur_ret_pur_wt,
    rtn_itm.pur_ret_po_item_id 
FROM ret_purchase_return_items rtn_itm
LEFT JOIN ret_purchase_return rtn ON rtn.pur_return_id = rtn_itm.pur_ret_id
WHERE rtn.bill_status = 1 
GROUP BY rtn.pur_return_id;

-- ================================================================================
-- SECTION 3: CREATE PERFORMANCE INDEXES
-- ================================================================================

-- Index 1: Optimize JOIN between ret_purchase_return_items and ret_purchase_return
-- This index helps the LEFT JOIN operation
CREATE INDEX idx_pur_ret_id ON ret_purchase_return_items(pur_ret_id);

-- Index 2: Optimize JOIN with ret_purchase_order_items
-- This is the critical index for the main query JOIN
CREATE INDEX idx_pur_ret_po_item_id ON ret_purchase_return_items(pur_ret_po_item_id);

-- Index 3: Optimize WHERE clause filtering on bill_status
-- This helps filter active returns quickly
CREATE INDEX idx_bill_status ON ret_purchase_return(bill_status);

-- Index 4: Composite index for optimal subquery performance
-- Combines the most frequently used columns in the subquery
CREATE INDEX idx_pur_ret_composite ON ret_purchase_return_items(pur_ret_po_item_id, pur_ret_id, pur_ret_pur_wt);

-- Index 5: Composite index on ret_purchase_return for JOIN + WHERE
CREATE INDEX idx_return_status_composite ON ret_purchase_return(pur_return_id, bill_status);

-- ================================================================================
-- SECTION 4: PERFORMANCE ANALYSIS (After Optimization)
-- ================================================================================

-- Re-analyze the same query to see improvement
EXPLAIN SELECT 
    IFNULL(sum(rtn_itm.pur_ret_pur_wt), 0) as pur_ret_pur_wt,
    rtn_itm.pur_ret_po_item_id 
FROM ret_purchase_return_items rtn_itm
LEFT JOIN ret_purchase_return rtn ON rtn.pur_return_id = rtn_itm.pur_ret_id
WHERE rtn.bill_status = 1 
GROUP BY rtn.pur_return_id;

-- ================================================================================
-- SECTION 5: VERIFY INDEX CREATION
-- ================================================================================

-- Verify all indexes were created successfully
SHOW INDEX FROM ret_purchase_return_items;
SHOW INDEX FROM ret_purchase_return;

-- ================================================================================
-- SECTION 6: ANALYZE FULL QUERY PERFORMANCE
-- ================================================================================

-- Test the complete get_approval_po_bills query performance
-- (Replace with actual karigar ID for testing)
EXPLAIN SELECT 
    p.po_id,
    p.po_ref_no as referenceno,
    IFNULL(p.tot_purchase_wt, 0) as tot_purchase_wt,
    IFNULL(p.tot_purchase_amt, 0) as tot_purchase_amt,
    m.id_metal,
    m.metal,
    (IFNULL(p.tot_purchase_wt, 0) - IFNULL(issued.tot_issued_wt, 0) - IFNULL(rc.tot_rc_wt, 0) - IFNULL(crdr.dr_wt, 0) + IFNULL(crdr.cr_wt, 0) - IFNULL(rtn_itms.pur_ret_pur_wt, 0)) as balance_wt,
    (IFNULL(p.tot_purchase_amt, 0) - IFNULL(crdr.dr_amt, 0) + IFNULL(crdr.cr_amt, 0)) as balance_amt
FROM ret_purchase_order p
JOIN ret_purchase_order_items pi ON pi.po_item_po_id = p.po_id
LEFT JOIN (
    SELECT IFNULL(sum(rtn_itm.pur_ret_pur_wt), 0) as pur_ret_pur_wt, rtn_itm.pur_ret_po_item_id 
    FROM ret_purchase_return_items rtn_itm
    LEFT JOIN ret_purchase_return rtn ON rtn.pur_return_id = rtn_itm.pur_ret_id
    WHERE rtn.bill_status = 1 
    GROUP BY rtn.pur_return_id
) rtn_itms ON rtn_itms.pur_ret_po_item_id = pi.po_item_id
JOIN ret_category cat ON cat.id_ret_category = pi.po_item_cat_id
JOIN metal m ON m.id_metal = cat.id_metal
LEFT JOIN (
    SELECT km.po_id, SUM(kmd.issue_metal_pur_wt) as tot_issued_wt
    FROM ret_karigar_metal_issue km
    JOIN ret_karigar_metal_issue_details kmd ON kmd.issue_met_parent_id = km.met_issue_id
    WHERE km.bill_status = 1 AND km.met_issue_karid IS NOT NULL
    GROUP BY km.po_id
) issued ON issued.po_id = p.po_id
LEFT JOIN (
    SELECT po_id, SUM(weight) as tot_rc_wt
    FROM ret_supplier_rate_cut
    WHERE status = 1 AND id_karigar IS NOT NULL
    GROUP BY po_id
) rc ON rc.po_id = p.po_id
LEFT JOIN (
    SELECT po_id, 
        SUM(CASE WHEN transtype = 2 THEN weight ELSE 0 END) as dr_wt,
        SUM(CASE WHEN transtype = 1 THEN weight ELSE 0 END) as cr_wt,
        SUM(CASE WHEN transtype = 2 THEN transamount ELSE 0 END) as dr_amt,
        SUM(CASE WHEN transtype = 1 THEN transamount ELSE 0 END) as cr_amt
    FROM ret_crdr_note
    WHERE crdr_status = 1
    GROUP BY po_id
) crdr ON crdr.po_id = p.po_id
WHERE p.pur_approval_type = 0 
  AND p.isratefixed = 0 
  AND p.bill_status = 1 
  AND p.is_approved = 1 
  AND p.po_karigar_id IS NOT NULL
GROUP BY p.po_id 
HAVING balance_wt != 0
LIMIT 10;

-- ================================================================================
-- SECTION 7: TABLE STATISTICS UPDATE
-- ================================================================================

-- Update table statistics for query optimizer
ANALYZE TABLE ret_purchase_return_items;
ANALYZE TABLE ret_purchase_return;
ANALYZE TABLE ret_purchase_order;
ANALYZE TABLE ret_purchase_order_items;

-- ================================================================================
-- SECTION 8: CLEANUP (Optional - Only if you need to remove indexes)
-- ================================================================================

-- UNCOMMENT ONLY IF YOU NEED TO REMOVE THE INDEXES
-- DROP INDEX idx_pur_ret_id ON ret_purchase_return_items;
-- DROP INDEX idx_pur_ret_po_item_id ON ret_purchase_return_items;
-- DROP INDEX idx_bill_status ON ret_purchase_return;
-- DROP INDEX idx_pur_ret_composite ON ret_purchase_return_items;
-- DROP INDEX idx_return_status_composite ON ret_purchase_return;

-- ================================================================================
-- SECTION 9: MONITORING QUERIES
-- ================================================================================

-- Query to monitor slow queries related to purchase returns
-- Run this periodically to check performance
SELECT 
    COUNT(*) as total_queries,
    AVG(query_time) as avg_time,
    MAX(query_time) as max_time
FROM mysql.slow_log
WHERE sql_text LIKE '%ret_purchase_return%'
  AND start_time > DATE_SUB(NOW(), INTERVAL 1 DAY);

-- Check index usage statistics
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    CARDINALITY,
    SEQ_IN_INDEX
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'srj'
  AND TABLE_NAME IN ('ret_purchase_return_items', 'ret_purchase_return')
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;

-- ================================================================================
-- END OF SCRIPT
-- ================================================================================

-- EXECUTION NOTES:
-- 1. Run SECTION 1-2 first to see current state
-- 2. Run SECTION 3 to create indexes (this is the main optimization)
-- 3. Run SECTION 4-6 to verify improvements
-- 4. Run SECTION 7 to update statistics
-- 5. Use SECTION 9 for ongoing monitoring

-- EXPECTED IMPROVEMENTS:
-- - Subquery execution should use indexes instead of full table scan
-- - JOIN operations should be faster
-- - Overall query time should reduce by 50-70%
-- - EXPLAIN should show "Using index" instead of "Using filesort"

-- ROLLBACK PLAN:
-- If any issues occur, use SECTION 8 to remove the indexes
