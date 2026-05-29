-- ================================================================
-- Payment Module — DB Truth Protocol
-- Updated: 2026-03-24 | Round: 3
-- ================================================================
-- PURPOSE: Diagnostic SQL queries for verifying Payment module 
-- data integrity. Run these BEFORE and AFTER any fix to confirm 
-- the fix didn't introduce regressions.
-- ================================================================

-- ============================================================
-- SECTION A: ENTITY-LEVEL CHECKS (Single Record)
-- ============================================================

-- A1: Full payment record with all related data
-- Usage: Replace {ID} with target payment ID
SELECT 
    p.id_payment, p.id_scheme_account, p.id_transaction,
    p.date_payment, p.custom_entry_date,
    p.payment_type, p.payment_mode, p.payment_amount,
    p.act_amount, p.metal_rate, p.metal_weight,
    p.payment_ref_number, p.receipt_no, p.receipt_year,
    p.payment_status AS status_code,
    psm.payment_status AS status_label, psm.color,
    p.gst, p.gst_type, p.redeemed_amount,
    p.due_type, p.no_of_dues, p.installment,
    p.id_employee, p.id_branch, p.id_payGateway,
    p.added_by, p.is_offline, p.is_settled,
    p.payu_id, p.ref_trans_id,
    p.cheque_no, p.cheque_date, p.bank_name,
    p.id_drawee, p.id_post_payment,
    p.saved_benefits, p.saved_benefit_amt,
    p.dg_other_benefit_wgt, p.add_charges,
    p.mer_net_amount, p.mer_service_fee, p.igst,
    p.form_secret, p.is_point_credited,
    p.date_add, p.date_upd, p.last_update, p.approval_date,
    sa.scheme_acc_number, sa.account_name,
    sa.total_paid_ins AS sa_paid_ins,
    c.firstname, c.lastname, c.mobile, c.email,
    s.scheme_name, s.scheme_type, s.gst AS scheme_gst,
    s.gst_type AS scheme_gst_type,
    e.firstname AS emp_name, e.emp_code,
    b.name AS branch_name
FROM payment p
LEFT JOIN scheme_account sa ON p.id_scheme_account = sa.id_scheme_account
LEFT JOIN customer c ON sa.id_customer = c.id_customer
LEFT JOIN scheme s ON sa.id_scheme = s.id_scheme
LEFT JOIN payment_status_message psm ON p.payment_status = psm.id_status_msg
LEFT JOIN employee e ON p.id_employee = e.id_employee
LEFT JOIN branch b ON p.id_branch = b.id_branch
WHERE p.id_payment = {ID};

-- A2: Payment mode details for a specific payment
SELECT 
    pmd.id_pay_mode_details, pmd.id_payment,
    pmd.payment_mode, pmd.payment_amount,
    pmd.card_type, pmd.card_no, pmd.payment_ref_number,
    pmd.id_pay_device, pmd.cheque_no, pmd.cheque_date,
    pmd.bank_name, pmd.bank_branch, pmd.bank_IFSC,
    pmd.NB_type, pmd.net_banking_date, pmd.id_bank,
    pmd.payment_status, pmd.payment_date,
    pmd.is_active, pmd.remark, pmd.adv_receipt_no,
    pmd.created_time, pmd.created_by
FROM payment_mode_details pmd
WHERE pmd.id_payment = {ID}
ORDER BY pmd.is_active DESC, pmd.id_pay_mode_details;

-- A3: Payment status audit trail
SELECT 
    ps.id_payment_status, ps.id_payment, ps.id_post_payment,
    ps.id_status_msg, psm.payment_status AS status_label,
    ps.charges, ps.id_employee,
    e.firstname AS emp_name,
    ps.date_upd
FROM payment_status ps
LEFT JOIN payment_status_message psm ON ps.id_status_msg = psm.id_status_msg
LEFT JOIN employee e ON ps.id_employee = e.id_employee
WHERE ps.id_payment = {ID}
ORDER BY ps.date_upd ASC;

-- A4: Advance utilization for a payment
SELECT 
    rau.id_issue_receipt, rau.id_payment, rau.id_adv_payment,
    rau.adjusted_for, rau.utilized_amt, rau.cash_utilized_amt
FROM ret_advance_utilized rau
WHERE rau.id_payment = {ID};


-- ============================================================
-- SECTION B: INTEGRITY CHECKS (Cross-Record Validation)
-- ============================================================

-- B1: Payment amount vs sum of mode details (MISMATCH = BUG)
-- Active mode details should sum to payment amount
SELECT 
    p.id_payment, p.payment_amount, p.payment_mode,
    SUM(pmd.payment_amount) AS mode_total,
    p.payment_amount - IFNULL(SUM(pmd.payment_amount), 0) AS diff
FROM payment p
LEFT JOIN payment_mode_details pmd 
    ON p.id_payment = pmd.id_payment AND pmd.is_active = 1
WHERE p.payment_status = 1
  AND p.payment_type = 'Manual'
GROUP BY p.id_payment
HAVING ABS(diff) > 0.01
ORDER BY ABS(diff) DESC
LIMIT 100;

-- B2: Installment count mismatch
-- scheme_account.total_paid_ins vs actual successful payment count
SELECT 
    sa.id_scheme_account, sa.scheme_acc_number,
    sa.total_paid_ins AS stored_count,
    COUNT(p.id_payment) AS actual_payment_count,
    SUM(p.no_of_dues) AS actual_dues_sum,
    sa.total_paid_ins - COUNT(p.id_payment) AS count_diff
FROM scheme_account sa
LEFT JOIN payment p 
    ON sa.id_scheme_account = p.id_scheme_account 
    AND p.payment_status = 1
GROUP BY sa.id_scheme_account
HAVING count_diff != 0
ORDER BY ABS(count_diff) DESC
LIMIT 100;

-- B3: Duplicate receipt numbers (CRITICAL)
SELECT 
    receipt_no, COUNT(*) AS cnt,
    GROUP_CONCAT(id_payment ORDER BY id_payment) AS payment_ids
FROM payment
WHERE receipt_no IS NOT NULL AND receipt_no != ''
GROUP BY receipt_no
HAVING cnt > 1
ORDER BY cnt DESC
LIMIT 50;

-- B4: Orphaned payment_mode_details (no parent payment)
SELECT 
    pmd.id_pay_mode_details, pmd.id_payment, 
    pmd.payment_mode, pmd.payment_amount, pmd.is_active
FROM payment_mode_details pmd
LEFT JOIN payment p ON pmd.id_payment = p.id_payment
WHERE p.id_payment IS NULL
LIMIT 50;

-- B5: Orphaned payment_status records
SELECT 
    ps.id_payment_status, ps.id_payment, ps.id_post_payment,
    ps.id_status_msg, ps.date_upd
FROM payment_status ps
LEFT JOIN payment p ON ps.id_payment = p.id_payment
WHERE p.id_payment IS NULL 
  AND ps.id_payment IS NOT NULL
  AND ps.id_payment > 0
LIMIT 50;

-- B6: Payments without any mode details (manual payments should have at least one)
SELECT 
    p.id_payment, p.payment_mode, p.payment_amount,
    p.payment_type, p.payment_status, p.date_payment
FROM payment p
LEFT JOIN payment_mode_details pmd 
    ON p.id_payment = pmd.id_payment AND pmd.is_active = 1
WHERE pmd.id_pay_mode_details IS NULL
  AND p.payment_type = 'Manual'
  AND p.payment_status = 1
ORDER BY p.date_payment DESC
LIMIT 50;

-- B7: Successful payments without receipt number
-- (If receipt_no_set=0, all successful payments should have receipts)
SELECT 
    p.id_payment, p.payment_amount, p.payment_status,
    p.receipt_no, p.date_payment, p.payment_type,
    sa.scheme_acc_number
FROM payment p
LEFT JOIN scheme_account sa ON p.id_scheme_account = sa.id_scheme_account
CROSS JOIN chit_settings cs
WHERE p.payment_status = 1
  AND (p.receipt_no IS NULL OR p.receipt_no = '')
  AND cs.receipt_no_set = 0
ORDER BY p.date_payment DESC
LIMIT 50;

-- B8: Payments with scheme account but no account number
-- (After first payment, account should be generated if schemeacc_no_set=0)
SELECT 
    sa.id_scheme_account,
    (SELECT COUNT(*) FROM payment WHERE id_scheme_account = sa.id_scheme_account AND payment_status = 1) AS paid_count,
    sa.scheme_acc_number, sa.account_name,
    c.firstname, c.mobile
FROM scheme_account sa
LEFT JOIN customer c ON sa.id_customer = c.id_customer
CROSS JOIN chit_settings cs
WHERE (sa.scheme_acc_number IS NULL OR sa.scheme_acc_number = '')
  AND cs.schemeacc_no_set = 0
  AND (SELECT COUNT(*) FROM payment WHERE id_scheme_account = sa.id_scheme_account AND payment_status = 1) > 0
LIMIT 50;


-- ============================================================
-- SECTION C: GST & WEIGHT VALIDATION
-- ============================================================

-- C1: GST amount validation for exclusive type
-- Expected: gst_amt = payment_amount * (gst/100)
SELECT 
    p.id_payment, p.payment_amount, p.gst, p.gst_type,
    p.act_amount,
    CASE 
        WHEN p.gst_type = 1 THEN ROUND(p.act_amount * (p.gst / 100), 2)
        WHEN p.gst_type = 0 THEN ROUND(p.act_amount - (p.act_amount * (100 / (100 + p.gst))), 2)
    END AS expected_gst_amt,
    p.payment_amount - p.act_amount AS stored_gst_amt,
    s.gst AS scheme_gst, s.gst_type AS scheme_gst_type
FROM payment p
LEFT JOIN scheme_account sa ON p.id_scheme_account = sa.id_scheme_account
LEFT JOIN scheme s ON sa.id_scheme = s.id_scheme
WHERE p.payment_status = 1
  AND p.gst > 0
  AND p.payment_type = 'Manual'
HAVING ABS(expected_gst_amt - stored_gst_amt) > 0.02
LIMIT 50;

-- C2: Metal weight validation for amount-to-weight schemes
-- Expected: metal_weight ≈ (payment_amount - gst_amt) / metal_rate
SELECT 
    p.id_payment, p.payment_amount, p.metal_rate, p.metal_weight,
    s.scheme_type, s.fix_weight,
    CASE 
        WHEN p.metal_rate > 0 THEN ROUND(p.payment_amount / p.metal_rate, 3)
        ELSE 0 
    END AS expected_weight,
    CASE 
        WHEN p.metal_rate > 0 THEN ABS(p.metal_weight - ROUND(p.payment_amount / p.metal_rate, 3))
        ELSE 0
    END AS weight_diff
FROM payment p
LEFT JOIN scheme_account sa ON p.id_scheme_account = sa.id_scheme_account
LEFT JOIN scheme s ON sa.id_scheme = s.id_scheme
WHERE p.payment_status = 1
  AND s.fix_weight = 2
  AND p.metal_rate > 0
  AND p.metal_weight > 0
HAVING weight_diff > 0.01
LIMIT 50;


-- ============================================================
-- SECTION D: GATEWAY / ONLINE PAYMENT CHECKS
-- ============================================================

-- D1: Online payments stuck in pending/awaiting for > 24 hours
SELECT 
    p.id_payment, p.ref_trans_id, p.payu_id,
    p.payment_amount, p.payment_status,
    psm.payment_status AS status_label,
    p.id_payGateway, g.pg_code, g.pg_name,
    p.date_payment, p.date_add,
    TIMESTAMPDIFF(HOUR, p.date_add, NOW()) AS hours_old
FROM payment p
LEFT JOIN payment_status_message psm ON p.payment_status = psm.id_status_msg
LEFT JOIN gateway g ON p.id_payGateway = g.id_pg
WHERE p.payment_status IN (2, 7)  -- Awaiting, Pending
  AND p.payment_type != 'Manual'
  AND TIMESTAMPDIFF(HOUR, p.date_add, NOW()) > 24
ORDER BY p.date_add DESC
LIMIT 50;

-- D2: Online payments with mismatched gateway info
SELECT 
    p.id_payment, p.id_payGateway, p.payment_type,
    p.ref_trans_id, p.payu_id,
    g.pg_code, g.pg_name,
    p.payment_status, p.payment_amount
FROM payment p
LEFT JOIN gateway g ON p.id_payGateway = g.id_pg
WHERE p.payment_type != 'Manual'
  AND p.id_payGateway IS NOT NULL
  AND g.id_pg IS NULL
ORDER BY p.date_payment DESC
LIMIT 50;

-- D3: Settled vs unsettled online payments
SELECT 
    g.pg_name,
    COUNT(*) AS total,
    SUM(CASE WHEN p.is_settled = 1 THEN 1 ELSE 0 END) AS settled,
    SUM(CASE WHEN p.is_settled = 0 OR p.is_settled IS NULL THEN 1 ELSE 0 END) AS unsettled,
    SUM(CASE WHEN p.payment_status = 1 THEN p.payment_amount ELSE 0 END) AS success_amount,
    SUM(CASE WHEN p.is_settled = 1 THEN p.payment_amount ELSE 0 END) AS settled_amount
FROM payment p
LEFT JOIN gateway g ON p.id_payGateway = g.id_pg
WHERE p.payment_type != 'Manual'
  AND p.id_payGateway IS NOT NULL
GROUP BY g.pg_name;

-- D4: Gateway configuration check
SELECT 
    g.id_pg, g.pg_code, g.pg_name,
    g.param_1 AS secret_key_set,
    g.param_3 AS app_id_set,
    g.api_url,
    g.status
FROM gateway g
ORDER BY g.pg_code;


-- ============================================================
-- SECTION E: POST-DATED CHEQUE (PDC) CHECKS
-- ============================================================

-- E1: PDC records with status mismatch
SELECT 
    pp.id_post_payment, pp.id_scheme_account,
    pp.pay_mode, pp.amount, pp.payment_status,
    pp.cheque_no, pp.date_payment,
    p.id_payment AS converted_payment_id,
    p.payment_status AS payment_status
FROM postdate_payment pp
LEFT JOIN payment p ON pp.id_post_payment = p.id_post_payment
WHERE pp.payment_status = 1  -- PDC marked success
  AND (p.id_payment IS NULL)  -- But no corresponding payment created
LIMIT 50;

-- E2: PDC records approaching maturity (next 7 days)
SELECT 
    pp.id_post_payment, pp.id_scheme_account,
    pp.cheque_no, pp.amount, pp.date_payment,
    pp.payment_status,
    sa.scheme_acc_number,
    c.firstname, c.mobile,
    DATEDIFF(pp.date_payment, CURDATE()) AS days_until_maturity
FROM postdate_payment pp
LEFT JOIN scheme_account sa ON pp.id_scheme_account = sa.id_scheme_account
LEFT JOIN customer c ON sa.id_customer = c.id_customer
WHERE pp.payment_status IN (7, 2)
  AND pp.date_payment BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
ORDER BY pp.date_payment ASC;


-- ============================================================
-- SECTION F: WALLET INTEGRITY
-- ============================================================

-- F1: Payments with wallet redemption but no wallet transaction
SELECT 
    p.id_payment, p.payment_amount, p.redeemed_amount,
    p.payment_mode, p.date_payment,
    sa.scheme_acc_number, c.firstname
FROM payment p
LEFT JOIN scheme_account sa ON p.id_scheme_account = sa.id_scheme_account
LEFT JOIN customer c ON sa.id_customer = c.id_customer
WHERE p.redeemed_amount > 0
  AND p.payment_status = 1
  AND NOT EXISTS (
    SELECT 1 FROM wallet_transaction wt 
    WHERE wt.id_payment = p.id_payment
  )
ORDER BY p.date_payment DESC
LIMIT 50;

-- F2: Wallet balance vs transaction history mismatch
-- Check if current balance = sum of credits - sum of debits
SELECT 
    iwa.mobile, iwa.available_points AS current_balance,
    (SELECT IFNULL(SUM(amount), 0) FROM wallet_transaction 
     WHERE mobile = iwa.mobile AND type = 'credit') AS total_credits,
    (SELECT IFNULL(SUM(amount), 0) FROM wallet_transaction 
     WHERE mobile = iwa.mobile AND type = 'debit') AS total_debits,
    iwa.available_points - (
        (SELECT IFNULL(SUM(amount), 0) FROM wallet_transaction 
         WHERE mobile = iwa.mobile AND type = 'credit') -
        (SELECT IFNULL(SUM(amount), 0) FROM wallet_transaction 
         WHERE mobile = iwa.mobile AND type = 'debit')
    ) AS balance_diff
FROM inter_wallet_account iwa
HAVING ABS(balance_diff) > 0.01
LIMIT 50;


-- ============================================================
-- SECTION G: GENERAL ADVANCE PAYMENT CHECKS
-- ============================================================

-- G1: General advance payments integrity
SELECT 
    gap.id_adv_payment, gap.id_scheme_account,
    gap.payment_amount, gap.payment_mode,
    gap.receipt_no, gap.payment_status,
    gap.date_payment,
    sa.scheme_acc_number
FROM general_advance_payment gap
LEFT JOIN scheme_account sa ON gap.id_scheme_account = sa.id_scheme_account
WHERE gap.payment_status = 1
ORDER BY gap.date_payment DESC
LIMIT 50;

-- G2: GA payment amount vs mode details mismatch
SELECT 
    gap.id_adv_payment, gap.payment_amount, gap.payment_mode,
    SUM(gamd.payment_amount) AS mode_total,
    gap.payment_amount - IFNULL(SUM(gamd.payment_amount), 0) AS diff
FROM general_advance_payment gap
LEFT JOIN general_advance_mode_detail gamd 
    ON gap.id_adv_payment = gamd.id_adv_payment AND gamd.is_active = 1
WHERE gap.payment_status = 1
GROUP BY gap.id_adv_payment
HAVING ABS(diff) > 0.01
LIMIT 50;


-- ============================================================
-- SECTION H: SETTLEMENT CHECKS
-- ============================================================

-- H1: Settlement records
SELECT 
    s.id_settlement, s.settlement_date,
    s.total_amount, s.total_weight,
    s.settlement_status,
    COUNT(sd.id_settlement_detail) AS detail_count,
    SUM(sd.amount) AS detail_total_amount,
    SUM(sd.weight) AS detail_total_weight
FROM settlement s
LEFT JOIN settlement_detail sd ON s.id_settlement = sd.id_settlement
GROUP BY s.id_settlement
HAVING ABS(s.total_amount - IFNULL(detail_total_amount, 0)) > 0.01
   OR ABS(s.total_weight - IFNULL(detail_total_weight, 0)) > 0.001
LIMIT 50;


-- ============================================================
-- SECTION I: REFERRAL & INCENTIVE CHECKS  
-- ============================================================

-- I1: Referral records for a payment
SELECT 
    cr.*, p.payment_amount, p.payment_status
FROM cus_refferal cr
LEFT JOIN payment p ON cr.id_payment = p.id_payment
WHERE p.id_payment = {ID};

-- I2: Agent/Employee incentive records for a payment
SELECT 'Agent' AS type, ai.* 
FROM agent_incentive ai WHERE ai.id_payment = {ID}
UNION ALL
SELECT 'Employee' AS type, ei.* 
FROM employee_incentive ei WHERE ei.id_payment = {ID};


-- ============================================================
-- SECTION J: CONFIGURATION SNAPSHOT
-- ============================================================

-- J1: Current chit_settings (affects almost all payment behavior)
SELECT 
    scheme_wise_receipt, receipt_no_set, branch_settings,
    has_lucky_draw, schemeacc_no_set, allow_wallet,
    edit_custom_entry_date, custom_entry_date, cost_center,
    edit_addpay_page, useWalletForChit
FROM chit_settings 
WHERE id_chit_settings = 1;

-- J2: Active scheme configurations
SELECT 
    s.id_scheme, s.scheme_name, s.code,
    s.scheme_type, s.gst, s.gst_type,
    s.fix_weight, s.flexible_sch_type,
    s.total_installments, s.scheme_amount,
    s.allow_advance, s.allow_unpaid,
    s.is_digi, s.interest, s.interest_value,
    s.payment_chances, s.max_chance,
    s.is_topup_scheme, s.is_lumpSum,
    s.avg_calc_ins
FROM scheme s
WHERE s.status = 1
ORDER BY s.id_scheme;

-- J3: Active payment gateways with branch mapping
SELECT 
    bg.id_branch, b.name AS branch_name,
    g.pg_code, g.pg_name,
    bg.param_1, bg.param_3, bg.api_url,
    bg.status
FROM branch_gateway bg
LEFT JOIN branch b ON bg.id_branch = b.id_branch
LEFT JOIN gateway g ON bg.pg_code = g.pg_code
WHERE bg.status = 1
ORDER BY bg.id_branch, bg.pg_code;


-- ============================================================
-- SECTION K: DAILY SUMMARY QUERIES  
-- ============================================================

-- K1: Today's payment summary by mode
SELECT 
    p.payment_mode,
    COUNT(*) AS count,
    SUM(p.payment_amount) AS total_amount,
    SUM(CASE WHEN p.payment_status = 1 THEN p.payment_amount ELSE 0 END) AS success_amount,
    SUM(CASE WHEN p.payment_status != 1 THEN p.payment_amount ELSE 0 END) AS non_success_amount
FROM payment p
WHERE DATE(p.date_payment) = CURDATE()
GROUP BY p.payment_mode
ORDER BY total_amount DESC;

-- K2: Today's payment summary by branch
SELECT 
    b.name AS branch_name,
    COUNT(*) AS count,
    SUM(p.payment_amount) AS total_amount,
    SUM(CASE WHEN p.payment_status = 1 THEN 1 ELSE 0 END) AS success_count,
    SUM(CASE WHEN p.payment_status = 2 THEN 1 ELSE 0 END) AS awaiting_count,
    SUM(CASE WHEN p.payment_status = 7 THEN 1 ELSE 0 END) AS pending_count
FROM payment p
LEFT JOIN branch b ON p.id_branch = b.id_branch
WHERE DATE(p.date_payment) = CURDATE()
GROUP BY p.id_branch
ORDER BY total_amount DESC;

-- K3: Today's payment summary by scheme
SELECT 
    s.scheme_name,
    COUNT(*) AS count,
    SUM(p.payment_amount) AS total_amount,
    SUM(p.metal_weight) AS total_weight
FROM payment p
LEFT JOIN scheme_account sa ON p.id_scheme_account = sa.id_scheme_account
LEFT JOIN scheme s ON sa.id_scheme = s.id_scheme
WHERE DATE(p.date_payment) = CURDATE()
  AND p.payment_status = 1
GROUP BY s.id_scheme
ORDER BY total_amount DESC;
