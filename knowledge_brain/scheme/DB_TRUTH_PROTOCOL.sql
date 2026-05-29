-- Scheme Module — DB Truth Protocol
-- Round: 2 | Date: 2026-03-24
-- ===================================================================

-- ===================================================================
-- 1. COMPLETE SCHEME RECORD BY ID
-- ===================================================================

-- 1.1 Full scheme with all settings
SELECT s.id_scheme, s.scheme_name, s.code, s.scheme_type, s.amount,
       s.total_installments, s.maturity_type, s.maturity_days,
       s.interest, s.interest_by, s.interest_value,
       s.tax, s.tax_by, s.tax_value,
       s.free_payment, s.allow_preclose, s.preclose_months,
       s.apply_benefit_by_chart, s.apply_debit_on_preclose,
       s.is_digi, s.is_lumpSum, s.is_topup_scheme,
       s.active, s.visible, s.date_add, s.date_upd
FROM scheme s
WHERE s.id_scheme = {ID};

-- 1.2 Scheme branches
SELECT sb.*, b.name as branch_name
FROM scheme_branch sb
LEFT JOIN branch b ON b.id_branch = sb.id_branch
WHERE sb.id_scheme = {ID};

-- 1.3 Benefit chart
SELECT * FROM scheme_benefit_deduct_settings WHERE id_scheme = {ID};

-- 1.4 Deduction chart
SELECT * FROM scheme_debit_settings WHERE id_scheme = {ID};

-- 1.5 Agent benefit chart
SELECT * FROM scheme_agent_benefit WHERE id_scheme = {ID};

-- 1.6 Incentive settings
SELECT * FROM scheme_incentive_settings WHERE id_scheme = {ID};

-- 1.7 Flexible installment settings
SELECT * FROM scheme_flexi_settings WHERE id_scheme = {ID};

-- 1.8 Employee closing incentive
SELECT * FROM emp_closing_incentive WHERE id_scheme = {ID};

-- 1.9 GA benefit settings
SELECT * FROM scheme_general_advance_benefit_settings WHERE id_scheme = {ID};

-- 1.10 TopUp chart
SELECT * FROM scheme_custom_payable_settings 
WHERE id_scheme = {ID} AND range_status = 1;

-- 1.11 GST split-up
SELECT * FROM gst_splitup_detail WHERE id_scheme = {ID} AND status = 1;


-- ===================================================================
-- 2. ORPHAN DETECTION
-- ===================================================================

-- 2.1 Benefit charts without parent scheme
SELECT sbds.* FROM scheme_benefit_deduct_settings sbds
LEFT JOIN scheme s ON s.id_scheme = sbds.id_scheme
WHERE s.id_scheme IS NULL;

-- 2.2 Deduction charts without parent scheme
SELECT sds.* FROM scheme_debit_settings sds
LEFT JOIN scheme s ON s.id_scheme = sds.id_scheme
WHERE s.id_scheme IS NULL;

-- 2.3 Agent benefit without parent scheme
SELECT sab.* FROM scheme_agent_benefit sab
LEFT JOIN scheme s ON s.id_scheme = sab.id_scheme
WHERE s.id_scheme IS NULL;

-- 2.4 Incentive settings without parent scheme
SELECT sis.* FROM scheme_incentive_settings sis
LEFT JOIN scheme s ON s.id_scheme = sis.id_scheme
WHERE s.id_scheme IS NULL;

-- 2.5 Branch mappings without parent scheme
SELECT sb.* FROM scheme_branch sb
LEFT JOIN scheme s ON s.id_scheme = sb.id_scheme
WHERE s.id_scheme IS NULL;

-- 2.6 GST split-up without parent scheme
SELECT gsd.* FROM gst_splitup_detail gsd
LEFT JOIN scheme s ON s.id_scheme = gsd.id_scheme
WHERE s.id_scheme IS NULL;

-- 2.7 Closing incentive without parent scheme
SELECT eci.* FROM emp_closing_incentive eci
LEFT JOIN scheme s ON s.id_scheme = eci.id_scheme
WHERE s.id_scheme IS NULL;

-- 2.8 GA benefit without parent scheme
SELECT sgabs.* FROM scheme_general_advance_benefit_settings sgabs
LEFT JOIN scheme s ON s.id_scheme = sgabs.id_scheme
WHERE s.id_scheme IS NULL;

-- 2.9 Flexi settings without parent scheme
SELECT sfs.* FROM scheme_flexi_settings sfs
LEFT JOIN scheme s ON s.id_scheme = sfs.id_scheme
WHERE s.id_scheme IS NULL;

-- 2.10 TopUp chart without parent scheme
SELECT scps.* FROM scheme_custom_payable_settings scps
LEFT JOIN scheme s ON s.id_scheme = scps.id_scheme
WHERE s.id_scheme IS NULL;


-- ===================================================================
-- 3. DATA QUALITY
-- ===================================================================

-- 3.1 Schemes with 0 total installments (causes division by zero)
SELECT id_scheme, scheme_name, total_installments
FROM scheme
WHERE total_installments = 0 OR total_installments IS NULL;

-- 3.2 Active schemes without metal association
SELECT id_scheme, scheme_name, id_metal
FROM scheme
WHERE active = 1 AND (id_metal IS NULL OR id_metal = 0);

-- 3.3 Schemes with benefit chart AND deduction chart (ambiguous)
SELECT id_scheme, scheme_name, apply_benefit_by_chart, apply_debit_on_preclose
FROM scheme
WHERE apply_benefit_by_chart = 1 AND apply_debit_on_preclose = 1;

-- 3.4 DigiGold duplicates per metal type
SELECT id_metal, COUNT(*) as digi_count, GROUP_CONCAT(id_scheme) as schemes
FROM scheme
WHERE is_digi = 1 AND active = 1
GROUP BY id_metal
HAVING digi_count > 1;

-- 3.5 Schemes with GST but no split-up detail
SELECT s.id_scheme, s.scheme_name, s.gst, s.gst_type
FROM scheme s
WHERE s.gst > 0
  AND NOT EXISTS (
    SELECT 1 FROM gst_splitup_detail gsd
    WHERE gsd.id_scheme = s.id_scheme AND gsd.status = 1
  );

-- 3.6 Schemes active but not visible (hidden from users)
SELECT id_scheme, scheme_name, active, visible
FROM scheme
WHERE active = 1 AND visible = 0;

-- 3.7 Closing incentive with NULL id_scheme (from Add bug)
SELECT * FROM emp_closing_incentive
WHERE id_scheme IS NULL OR id_scheme = 0;

-- 3.8 GA benefit with NULL id_scheme (from Add bug)
SELECT * FROM scheme_general_advance_benefit_settings
WHERE id_scheme IS NULL OR id_scheme = 0;


-- ===================================================================
-- 4. ACCOUNT IMPACT ANALYSIS
-- ===================================================================

-- 4.1 Scheme usage summary
SELECT s.id_scheme, s.scheme_name, s.active,
       COUNT(DISTINCT CASE WHEN sa.is_closed = 0 THEN sa.id_scheme_account END) as open_accounts,
       COUNT(DISTINCT CASE WHEN sa.is_closed = 1 THEN sa.id_scheme_account END) as closed_accounts
FROM scheme s
LEFT JOIN scheme_account sa ON sa.id_scheme = s.id_scheme
GROUP BY s.id_scheme
ORDER BY open_accounts DESC;

-- 4.2 Inactive schemes with open accounts
SELECT s.id_scheme, s.scheme_name, s.active,
       COUNT(sa.id_scheme_account) as open_accounts
FROM scheme s
JOIN scheme_account sa ON sa.id_scheme = s.id_scheme AND sa.is_closed = 0 AND sa.active = 1
WHERE s.active = 0
GROUP BY s.id_scheme;


-- ===================================================================
-- 5. CONFIGURATION CONSISTENCY
-- ===================================================================

-- 5.1 Referral enabled but zero value
SELECT id_scheme, scheme_name,
       emp_refferal, emp_refferal_value, Emp_ref_values,
       cus_refferal, cus_refferal_value, cus_ref_values,
       agent_refferal
FROM scheme
WHERE (emp_refferal = 1 AND (emp_refferal_value = 0 OR emp_refferal_value IS NULL))
   OR (cus_refferal = 1 AND (cus_refferal_value = 0 OR cus_refferal_value IS NULL));

-- 5.2 Pre-close enabled but months = 0
SELECT id_scheme, scheme_name, allow_preclose, preclose_months, preclose_benefits
FROM scheme
WHERE allow_preclose = 1 AND (preclose_months = 0 OR preclose_months IS NULL);

-- 5.3 Benefit chart enabled but no chart data
SELECT s.id_scheme, s.scheme_name
FROM scheme s
WHERE s.apply_benefit_by_chart = 1
  AND NOT EXISTS (
    SELECT 1 FROM scheme_benefit_deduct_settings sbds
    WHERE sbds.id_scheme = s.id_scheme
  );

-- 5.4 Deduction chart enabled but no chart data
SELECT s.id_scheme, s.scheme_name
FROM scheme s
WHERE s.apply_debit_on_preclose = 1
  AND NOT EXISTS (
    SELECT 1 FROM scheme_debit_settings sds
    WHERE sds.id_scheme = s.id_scheme
  );

-- 5.5 TopUp scheme without chart data
SELECT s.id_scheme, s.scheme_name
FROM scheme s
WHERE s.is_topup_scheme = 1
  AND NOT EXISTS (
    SELECT 1 FROM scheme_custom_payable_settings scps
    WHERE scps.id_scheme = s.id_scheme AND scps.range_status = 1
  );
