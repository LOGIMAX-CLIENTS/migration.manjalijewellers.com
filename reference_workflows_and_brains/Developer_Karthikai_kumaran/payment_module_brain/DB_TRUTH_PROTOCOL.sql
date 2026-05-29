-- ============================================================================
-- Component 6 — Payment CRM: DB Truth Protocol
-- Module: Payment CRM
-- Generated: 2026-02-18
-- Updated: 2026-02-18 (Fixing table names)
-- Purpose: Diagnostic SQL queries for verifying payment data integrity
-- ============================================================================

-- ============================================================================
-- SECTION 1: PAYMENT RECORD INTEGRITY
-- ============================================================================

-- Q1.1: Payments with impossible installment numbers
-- Expected: 0 rows. Any result = installment counter bug
SELECT 
    p.id_payment,
    p.id_scheme_account,
    sa.scheme_acc_number,
    s.scheme_name,
    s.total_installments,
    COUNT(*) AS actual_paid_count,
    GROUP_CONCAT(p.id_payment ORDER BY p.date_payment) AS payment_ids
FROM payment p
JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
JOIN scheme s ON s.id_scheme = sa.id_scheme
WHERE p.payment_status = 1
  AND p.is_deleted = 0
  AND p.due_type IN ('ND', 'PD', 'AD')
GROUP BY p.id_scheme_account
HAVING actual_paid_count > s.total_installments;

-- Q1.2: Payments with zero or negative amounts
-- Expected: 0 rows
SELECT 
    id_payment, id_scheme_account, payment_amount, act_amount,
    metal_rate, metal_weight, date_payment, payment_mode
FROM payment
WHERE (payment_amount <= 0 OR act_amount <= 0)
  AND payment_status = 1
  AND is_deleted = 0
  AND due_type NOT IN ('GA', 'GEN_ADV');

-- Q1.3: Payments where payment_amount != sum of mode details
-- Expected: 0 rows. Any result = payment mode split imbalance
SELECT 
    p.id_payment,
    p.payment_amount AS header_amount,
    COALESCE(SUM(d.payment_amount), 0) AS detail_total,
    p.payment_amount - COALESCE(SUM(d.payment_amount), 0) AS difference,
    p.payment_mode,
    p.redeemed_amount
FROM payment p
LEFT JOIN payment_mode_details d ON d.id_payment = p.id_payment
WHERE p.payment_status = 1
  AND p.is_deleted = 0
  AND p.date_payment >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
GROUP BY p.id_payment
HAVING ABS(difference) > 1
   AND p.payment_mode != 'REF_WALLET';

-- Q1.4: Duplicate receipt numbers within same scheme + branch
-- Expected: 0 rows
SELECT 
    p.receipt_no, sa.id_scheme, p.id_branch, 
    COUNT(*) AS dup_count,
    GROUP_CONCAT(p.id_payment ORDER BY p.date_payment) AS payment_ids
FROM payment p
JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
WHERE p.receipt_no IS NOT NULL
  AND p.receipt_no != ''
  AND p.is_deleted = 0
GROUP BY p.receipt_no, sa.id_scheme, p.id_branch
HAVING dup_count > 1;

-- Q1.5: Payments with metal_weight > 0 but metal_rate = 0
-- Expected: 0 rows. Result = division-by-zero bypass
SELECT 
    id_payment, id_scheme_account, payment_amount,
    metal_rate, metal_weight, date_payment
FROM payment
WHERE metal_weight > 0
  AND (metal_rate = 0 OR metal_rate IS NULL)
  AND payment_status = 1
  AND is_deleted = 0;

-- Q1.6: Payments with future dates (data entry anomaly)
-- Expected: 0 rows or only legitimate future-dated entries
SELECT 
    id_payment, id_scheme_account, date_payment,
    custom_entry_date, payment_amount, id_employee
FROM payment
WHERE date_payment > NOW()
  AND is_deleted = 0
  AND payment_status = 1;

-- ============================================================================
-- SECTION 2: SCHEME ACCOUNT CONSISTENCY
-- ============================================================================

-- Q2.1: Scheme accounts where total_paid != sum of confirmed payments
-- Expected: 0 rows
SELECT 
    sa.id_scheme_account,
    sa.scheme_acc_number,
    sa.total_paid_amount AS account_total_paid,
    COALESCE(SUM(p.payment_amount), 0) AS actual_sum_payments,
    sa.total_paid_amount - COALESCE(SUM(p.payment_amount), 0) AS discrepancy
FROM scheme_account sa
LEFT JOIN payment p ON p.id_scheme_account = sa.id_scheme_account
    AND p.payment_status = 1
    AND p.is_deleted = 0
    AND p.due_type IN ('ND', 'PD', 'AD')
GROUP BY sa.id_scheme_account
HAVING ABS(discrepancy) > 1
LIMIT 100;

-- Q2.2: Scheme accounts where paid_installments != count of confirmed payments
-- Expected: 0 rows
SELECT 
    sa.id_scheme_account,
    sa.scheme_acc_number,
    sa.paid_installments AS recorded_paid,
    COUNT(p.id_payment) AS actual_paid_count,
    sa.paid_installments - COUNT(p.id_payment) AS discrepancy
FROM scheme_account sa
LEFT JOIN payment p ON p.id_scheme_account = sa.id_scheme_account
    AND p.payment_status = 1
    AND p.is_deleted = 0
    AND p.due_type IN ('ND', 'PD', 'AD')
GROUP BY sa.id_scheme_account
HAVING discrepancy != 0
LIMIT 100;

-- Q2.3: Active scheme accounts with payments exceeding scheme period
-- Expected: 0 rows
SELECT 
    sa.id_scheme_account,
    sa.scheme_acc_number,
    s.scheme_name,
    s.total_installments AS max_installments,
    sa.paid_installments,
    sa.active
FROM scheme_account sa
JOIN scheme s ON s.id_scheme = sa.id_scheme
WHERE sa.paid_installments > s.total_installments
  AND sa.active = 1;

-- Q2.4: Accounts with fixed rate but no payments
-- Expected: 0 rows (rate should only fix on first payment)
SELECT 
    sa.id_scheme_account,
    sa.scheme_acc_number,
    sa.fixed_metal_rate,
    sa.fixed_rate_on,
    COUNT(p.id_payment) AS payment_count
FROM scheme_account sa
LEFT JOIN payment p ON p.id_scheme_account = sa.id_scheme_account
    AND p.payment_status = 1
    AND p.is_deleted = 0
WHERE sa.fixed_metal_rate > 0
GROUP BY sa.id_scheme_account
HAVING payment_count = 0;

-- ============================================================================
-- SECTION 3: FINANCIAL INTEGRITY
-- ============================================================================

-- Q3.1: Wallet deductions without matching payment
-- Expected: 0 rows
SELECT 
    wt.id_wallet_transaction, wa.id_customer, wt.value, wt.transaction_type, wt.date_transaction,
    wt.id_payment
FROM wallet_transaction wt
JOIN wallet_account wa ON wa.id_wallet_account = wt.id_wallet_account
WHERE wt.transaction_type = 1 -- REDEEM
  AND wt.id_payment IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM payment p 
      WHERE p.id_payment = wt.id_payment 
        AND p.payment_status = 1
        AND p.is_deleted = 0
  );

-- Q3.2: Advance adjustments without matching payment
-- Expected: 0 rows
SELECT 
    au.id, au.id_payment, au.utilized_amt, au.created_at
FROM ret_advance_utilized au
WHERE NOT EXISTS (
    SELECT 1 FROM payment p 
    WHERE p.id_payment = au.id_payment 
      AND p.payment_status = 1
      AND p.is_deleted = 0
);

-- Q3.3: Vouchers utilized without matching payment
-- Expected: 0 rows
SELECT 
    vu.id_voucher_utilized, vu.id_payment, vu.payment_amount, vu.created_time
FROM voucher_utilized vu
WHERE NOT EXISTS (
    SELECT 1 FROM payment p 
    WHERE p.id_payment = vu.id_payment 
      AND p.payment_status = 1
      AND p.is_deleted = 0
);

-- Q3.4: GST amount inconsistency check
-- Expected: 0 rows. Checks that gst_amount aligns with gst_type formula
SELECT 
    p.id_payment, p.payment_amount, p.gst, p.gst_type, p.gst_amount,
    CASE 
        WHEN p.gst_type = 1 THEN ROUND(p.payment_amount * (p.gst / 100), 2)
        WHEN p.gst_type = 0 THEN ROUND(p.payment_amount - (p.payment_amount * (100 / (100 + p.gst))), 2)
        ELSE 0
    END AS expected_gst,
    ABS(p.gst_amount - CASE 
        WHEN p.gst_type = 1 THEN ROUND(p.payment_amount * (p.gst / 100), 2)
        WHEN p.gst_type = 0 THEN ROUND(p.payment_amount - (p.payment_amount * (100 / (100 + p.gst))), 2)
        ELSE 0
    END) AS gst_diff
FROM payment p
WHERE p.gst > 0
  AND p.payment_status = 1
  AND p.is_deleted = 0
  AND p.date_payment >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
HAVING gst_diff > 5;

-- ============================================================================
-- SECTION 4: PAYMENT MODE DETAIL INTEGRITY
-- ============================================================================

-- Q4.1: Payments flagged as MULTI but only one mode detail exists
-- Expected: 0 rows
SELECT 
    p.id_payment, p.payment_mode, p.payment_amount,
    COUNT(DISTINCT d.payment_mode) AS distinct_modes,
    COUNT(d.id_pay_mode_details) AS detail_count
FROM payment p
JOIN payment_mode_details d ON d.id_payment = p.id_payment
WHERE p.payment_mode = 'MULTI'
  AND p.is_deleted = 0
  AND p.payment_status = 1
GROUP BY p.id_payment
HAVING distinct_modes <= 1;

-- Q4.2: Payments with mode details but no header record
-- Expected: 0 rows (orphaned mode details)
SELECT 
    d.id_pay_mode_details, d.id_payment, d.payment_mode, d.payment_amount
FROM payment_mode_details d
WHERE NOT EXISTS (
    SELECT 1 FROM payment p WHERE p.id_payment = d.id_payment
)
LIMIT 50;

-- Q4.3: Card payments without reference numbers
-- Expected: Review. Missing ref = reconciliation gap
SELECT 
    d.id_pay_mode_details, d.id_payment, d.payment_mode, d.payment_amount,
    d.card_no, d.payment_ref_number,
    p.date_payment
FROM payment_mode_details d
JOIN payment p ON p.id_payment = d.id_payment
WHERE d.payment_mode IN ('CC', 'DC')
  AND (d.payment_ref_number IS NULL OR d.payment_ref_number = '')
  AND p.is_deleted = 0
  AND p.payment_status = 1
  AND p.date_payment >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH);

-- ============================================================================
-- SECTION 5: DIGIGOLD INTEGRITY
-- ============================================================================

-- Q5.1: DigiGold payments with benefits but zero weight
-- Expected: 0 rows
SELECT 
    id_payment, id_scheme_account, payment_amount,
    metal_weight, saved_benefits, saved_benefit_amt, benefit_value
FROM payment
WHERE saved_benefits > 0
  AND metal_weight = 0
  AND payment_status = 1
  AND is_deleted = 0;

-- Q5.2: DigiGold benefit weight exceeds payment weight
-- Expected: 0 rows (benefit cannot exceed what was paid)
SELECT 
    id_payment, id_scheme_account,
    metal_weight, saved_benefits AS benefit_weight,
    saved_benefit_amt AS benefit_amount,
    benefit_value, benefit_type
FROM payment
WHERE saved_benefits > metal_weight
  AND saved_benefits > 0
  AND payment_status = 1
  AND is_deleted = 0;

-- ============================================================================
-- SECTION 6: GENERAL ADVANCE INTEGRITY
-- ============================================================================

-- Q6.1: General advance payments without mode details
-- Expected: 0 rows
SELECT 
    ga.id, ga.id_scheme_account, ga.amount, ga.date_payment
FROM general_advance_payment ga
WHERE ga.status = 1
  AND ga.id NOT IN (SELECT id_general_advance FROM general_advance_mode_detail);

-- Q6.2: Utilized advances exceeding original advance amount
-- Expected: 0 rows
SELECT 
    au.id_issue_receipt,
    ga.amount AS original_amount,
    SUM(au.utilized_amt) AS total_utilized,
    SUM(au.utilized_amt) - ga.amount AS over_utilized
FROM ret_advance_utilized au
JOIN general_advance_payment ga ON ga.id = au.id_issue_receipt
GROUP BY au.id_issue_receipt
HAVING over_utilized > 0;

-- ============================================================================
-- SECTION 7: DAILY HEALTH CHECK (Run daily)
-- ============================================================================

-- Q7.1: Today's payments summary with mode distribution
SELECT 
    payment_mode,
    COUNT(*) AS count,
    SUM(payment_amount) AS total_amount,
    MIN(payment_amount) AS min_amount,
    MAX(payment_amount) AS max_amount
FROM payment
WHERE DATE(date_payment) = CURDATE()
  AND is_deleted = 0
  AND payment_status = 1
GROUP BY payment_mode
ORDER BY total_amount DESC;

-- Q7.2: Today's potential anomalies (payments in quick succession for same account)
SELECT 
    p1.id_payment AS pay1_id,
    p2.id_payment AS pay2_id,
    p1.id_scheme_account,
    p1.date_payment AS date1,
    p2.date_payment AS date2,
    TIMESTAMPDIFF(SECOND, p1.date_payment, p2.date_payment) AS seconds_apart,
    p1.payment_amount AS amt1,
    p2.payment_amount AS amt2
FROM payment p1
JOIN payment p2 ON p1.id_scheme_account = p2.id_scheme_account
    AND p2.id_payment > p1.id_payment
    AND TIMESTAMPDIFF(SECOND, p1.date_payment, p2.date_payment) < 60
WHERE DATE(p1.date_payment) = CURDATE()
  AND p1.is_deleted = 0
  AND p2.is_deleted = 0
  AND p1.payment_status = 1
  AND p2.payment_status = 1;

-- Q7.3: Uncommitted / stuck transactions (payments with no status log)
SELECT 
    p.id_payment, p.id_scheme_account, p.payment_amount,
    p.date_payment, p.payment_status
FROM payment p
LEFT JOIN payment_status_message psm ON psm.id_payment = p.id_payment
WHERE psm.id_pay_status_msg IS NULL
  AND p.date_payment >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
  AND p.is_deleted = 0;
