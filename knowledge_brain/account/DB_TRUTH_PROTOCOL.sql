-- Account Module — DB Truth Protocol
-- Round: 2 | Date: 2026-03-24
-- ===================================================================
-- Run these queries against the application database to verify data integrity
-- ===================================================================

-- ===================================================================
-- 1. ORPHAN DETECTION
-- ===================================================================

-- 1.1 Scheme accounts with no customer
SELECT sa.id_scheme_account, sa.account_name, sa.id_customer
FROM scheme_account sa
LEFT JOIN customer c ON c.id_customer = sa.id_customer
WHERE c.id_customer IS NULL;

-- 1.2 Scheme accounts with no scheme
SELECT sa.id_scheme_account, sa.account_name, sa.id_scheme
FROM scheme_account sa
LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
WHERE s.id_scheme IS NULL;

-- 1.3 Payments with no scheme account
SELECT p.id_payment, p.id_scheme_account, p.payment_amount
FROM payment p
LEFT JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
WHERE sa.id_scheme_account IS NULL;

-- 1.4 Gift issued with no scheme account
SELECT gi.id_gift_issued, gi.id_scheme_account, gi.gift_desc
FROM gift_issued gi
LEFT JOIN scheme_account sa ON sa.id_scheme_account = gi.id_scheme_account
WHERE sa.id_scheme_account IS NULL;

-- 1.5 Wallet transactions with no scheme account
SELECT wt.id_wallet_transaction, wt.id_sch_ac, wt.value
FROM wallet_transaction wt
LEFT JOIN scheme_account sa ON sa.id_scheme_account = wt.id_sch_ac
WHERE wt.id_sch_ac IS NOT NULL AND sa.id_scheme_account IS NULL;

-- 1.6 Scheme groups with no scheme
SELECT sg.id_scheme_group, sg.group_code, sg.id_scheme
FROM scheme_group sg
LEFT JOIN scheme s ON s.id_scheme = sg.id_scheme
WHERE s.id_scheme IS NULL;


-- ===================================================================
-- 2. DUPLICATE DETECTION
-- ===================================================================

-- 2.1 Duplicate account numbers within same scheme
SELECT sa.id_scheme, sa.scheme_acc_number, COUNT(*) as cnt
FROM scheme_account sa
WHERE sa.scheme_acc_number IS NOT NULL
GROUP BY sa.id_scheme, sa.scheme_acc_number
HAVING cnt > 1;

-- 2.2 Duplicate account numbers within same branch (mode 1,3,6)
SELECT sa.id_branch, sa.scheme_acc_number, COUNT(*) as cnt
FROM scheme_account sa
WHERE sa.scheme_acc_number IS NOT NULL AND sa.id_branch IS NOT NULL
GROUP BY sa.id_branch, sa.scheme_acc_number
HAVING cnt > 1;

-- 2.3 Duplicate group codes
SELECT sg.group_code, COUNT(*) as cnt
FROM scheme_group sg
GROUP BY sg.group_code
HAVING cnt > 1;

-- 2.4 Duplicate client IDs (ref_no)
SELECT sa.ref_no, COUNT(*) as cnt
FROM scheme_account sa
WHERE sa.ref_no IS NOT NULL AND sa.ref_no != ''
GROUP BY sa.ref_no
HAVING cnt > 1;


-- ===================================================================
-- 3. STATE CONSISTENCY
-- ===================================================================

-- 3.1 Accounts marked closed but still active
SELECT id_scheme_account, account_name, active, is_closed
FROM scheme_account
WHERE is_closed = 1 AND active = 1;

-- 3.2 Accounts marked active but also closed
SELECT id_scheme_account, account_name, active, is_closed, closing_date
FROM scheme_account
WHERE active = 0 AND is_closed = 0;

-- 3.3 Closed accounts without closing date
SELECT id_scheme_account, account_name, is_closed, closing_date
FROM scheme_account
WHERE is_closed = 1 AND (closing_date IS NULL OR closing_date = '0000-00-00 00:00:00');

-- 3.4 Accounts with more paid installments than total
SELECT sa.id_scheme_account, sa.paid_installments, s.total_installments,
       sa.account_name
FROM scheme_account sa
JOIN scheme s ON s.id_scheme = sa.id_scheme
WHERE sa.paid_installments > s.total_installments AND s.total_installments > 0;

-- 3.5 Active accounts with start_date in the future
SELECT id_scheme_account, account_name, start_date
FROM scheme_account
WHERE active = 1 AND start_date > NOW();

-- 3.6 Payment status anomalies (paid on closed accounts)
SELECT p.id_payment, p.id_scheme_account, p.payment_status, p.date_payment,
       sa.is_closed, sa.closing_date
FROM payment p
JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
WHERE sa.is_closed = 1
  AND p.date_payment > sa.closing_date
  AND p.payment_status = 1;


-- ===================================================================
-- 4. DATA QUALITY
-- ===================================================================

-- 4.1 Accounts with NULL/empty branch where branch_settings enabled
SELECT sa.id_scheme_account, sa.account_name, sa.id_branch
FROM scheme_account sa
WHERE sa.id_branch IS NULL OR sa.id_branch = 0;

-- 4.2 Negative closing amounts
SELECT id_scheme_account, closing_amount, closing_weight, closing_balance
FROM scheme_account
WHERE is_closed = 1
  AND (closing_amount < 0 OR closing_weight < 0 OR closing_balance < 0);

-- 4.3 Accounts with zero total installments (division by zero risk)
SELECT sa.id_scheme_account, s.total_installments, sa.account_name
FROM scheme_account sa
JOIN scheme s ON s.id_scheme = sa.id_scheme
WHERE s.total_installments = 0 OR s.total_installments IS NULL;

-- 4.4 OTP records without verification
SELECT COUNT(*) as unverified_count
FROM otp
WHERE is_verified = 0 AND otp_gen_time > DATE_SUB(NOW(), INTERVAL 30 DAY);

-- 4.5 Mismatched paid installments vs actual payment count
SELECT sa.id_scheme_account, sa.paid_installments,
       COUNT(p.id_payment) as actual_payments,
       ABS(sa.paid_installments - COUNT(p.id_payment)) as difference
FROM scheme_account sa
LEFT JOIN payment p ON p.id_scheme_account = sa.id_scheme_account
                    AND p.payment_status = 1
GROUP BY sa.id_scheme_account
HAVING difference > 0
ORDER BY difference DESC
LIMIT 50;


-- ===================================================================
-- 5. REFERRAL INTEGRITY
-- ===================================================================

-- 5.1 Referral codes that don't match any employee/customer
SELECT sa.id_scheme_account, sa.referal_code, sa.is_refferal_by
FROM scheme_account sa
WHERE sa.referal_code IS NOT NULL AND sa.referal_code != ''
  AND sa.is_refferal_by = 1
  AND NOT EXISTS (
    SELECT 1 FROM employee e WHERE e.referal_code = sa.referal_code
  );

-- 5.2 Wallet credits without corresponding account action
SELECT wt.id_wallet_transaction, wt.id_sch_ac, wt.value, wt.description,
       wt.date_transaction
FROM wallet_transaction wt
WHERE wt.description LIKE '%Referral%'
  AND NOT EXISTS (
    SELECT 1 FROM scheme_account sa WHERE sa.id_scheme_account = wt.id_sch_ac
  );

-- 5.3 Accounts with referral but no wallet transaction
SELECT sa.id_scheme_account, sa.referal_code, sa.is_refferal_by
FROM scheme_account sa
WHERE sa.referal_code IS NOT NULL AND sa.referal_code != ''
  AND NOT EXISTS (
    SELECT 1 FROM wallet_transaction wt
    WHERE wt.id_sch_ac = sa.id_scheme_account
      AND wt.description LIKE '%Referral%'
  );


-- ===================================================================
-- 6. GIFT INTEGRITY
-- ===================================================================

-- 6.1 Gift issued to closed accounts
SELECT gi.id_gift_issued, gi.id_scheme_account, gi.date_issued,
       sa.is_closed, sa.closing_date
FROM gift_issued gi
JOIN scheme_account sa ON sa.id_scheme_account = gi.id_scheme_account
WHERE sa.is_closed = 1 AND gi.date_issued > sa.closing_date
  AND gi.status = 1;

-- 6.2 Inventory items issued but without gift_issued record
SELECT ip.pur_item_detail_id, ip.item_ref_no, ip.status
FROM ret_other_inventory_purchase_items_details ip
WHERE ip.status = 1
  AND NOT EXISTS (
    SELECT 1 FROM gift_issued gi WHERE gi.item_ref_no = ip.item_ref_no
  );

-- 6.3 Double-issued inventory items
SELECT item_ref_no, COUNT(*) as cnt
FROM gift_issued
WHERE status = 1 AND item_ref_no IS NOT NULL AND item_ref_no != ''
GROUP BY item_ref_no
HAVING cnt > 1;


-- ===================================================================
-- 7. SYNC INTEGRITY
-- ===================================================================

-- 7.1 Pending sync records
SELECT COUNT(*) as pending_count, record_to
FROM customer_reg
WHERE is_transferred = 'N'
GROUP BY record_to;

-- 7.2 Sync log summary
SELECT DATE(sync_date) as sync_day, SUM(total_records) as total,
       SUM(scheme_accounts) as accounts, SUM(payments) as payments
FROM sync_log
WHERE sync_date > DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(sync_date)
ORDER BY sync_day DESC;


-- ===================================================================
-- 8. CLOSING CALCULATION VERIFICATION
-- ===================================================================

-- 8.1 Closed accounts with suspicious benefit amounts
SELECT sa.id_scheme_account, sa.closing_benefits, sa.closing_deductions,
       sa.closing_balance, sa.closing_amount, sa.closing_weight,
       sa.bonus_percent, sa.additional_benefits
FROM scheme_account sa
WHERE sa.is_closed = 1
  AND (sa.closing_benefits > sa.closing_amount * 0.5
    OR sa.closing_deductions > sa.closing_amount * 0.5)
ORDER BY sa.closing_date DESC
LIMIT 20;

-- 8.2 Weight-based closings with zero weight
SELECT sa.id_scheme_account, s.scheme_type, sa.closing_weight,
       sa.closing_amount
FROM scheme_account sa
JOIN scheme s ON s.id_scheme = sa.id_scheme
WHERE sa.is_closed = 1
  AND (s.scheme_type = 1 OR s.scheme_type = 2)
  AND (sa.closing_weight = 0 OR sa.closing_weight IS NULL);


-- ===================================================================
-- 9. REGISTRATION REQUEST INTEGRITY
-- ===================================================================

-- 9.1 Approved requests without scheme account
SELECT rr.*
FROM reg_request rr
WHERE rr.status = 1
  AND NOT EXISTS (
    SELECT 1 FROM scheme_account sa
    WHERE sa.id_customer = rr.id_customer AND sa.id_scheme = rr.id_scheme
  );

-- 9.2 Pending requests older than 30 days
SELECT rr.*, c.firstname, c.mobile
FROM reg_request rr
JOIN customer c ON c.id_customer = rr.id_customer
WHERE rr.status = 0
  AND rr.date_add < DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY rr.date_add ASC;


-- ===================================================================
-- 10. VOUCHER/GIFT CARD INTEGRITY
-- ===================================================================

-- 10.1 Active vouchers for closed accounts
SELECT gc.id_gift_card, gc.id_scheme_account, gc.status,
       sa.is_closed
FROM gift_card gc
JOIN scheme_account sa ON sa.id_scheme_account = gc.id_scheme_account
WHERE gc.status IN (0, 1) AND sa.is_closed = 1;

-- 10.2 Deactivated vouchers without replacement
SELECT gc1.id_gift_card, gc1.id_scheme_account, gc1.status
FROM gift_card gc1
WHERE gc1.status = 5
  AND NOT EXISTS (
    SELECT 1 FROM gift_card gc2
    WHERE gc2.id_scheme_account = gc1.id_scheme_account
      AND gc2.status IN (0, 1)
      AND gc2.id_gift_card > gc1.id_gift_card
  );
