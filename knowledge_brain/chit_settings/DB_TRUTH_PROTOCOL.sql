-- Chit Settings Module — DB Truth Protocol
-- Round: 1 | Date: 2026-03-06
-- ===================================================================

-- ===================================================================
-- 1. GENERAL SETTINGS STATE (chit_settings singleton)
-- ===================================================================

-- 1.1 Full general settings dump
SELECT * FROM chit_settings WHERE id_chit_settings = 1;

-- 1.2 Key feature flags
SELECT 
    branchwise_scheme, sch_limit, show_closed_list,
    enableGoldrateDisc, goldDiscAmt, enableSilver_rateDisc, silverDiscAmt,
    enableGoldrateDisc_18k, goldDiscAmt_18k,
    gst_setting, enable_closing_otp, has_lucky_draw,
    scheme_wise_receipt, scheme_wise_acc_no, allow_join_multiple,
    allow_join_unpaid, delete_unpaid, rate_update, maintenance_mode,
    isOTPReqToLogin, isOTPRegForPayment, isOTPReqToGift,
    metal_wgt_decimal, metal_wgt_roundoff, auto_debit,
    schemeaccNo_displayFrmt, receiptNo_displayFrmt,
    custom_AccDisplayFrmt, custom_ReceiptDisplayFrmt
FROM chit_settings WHERE id_chit_settings = 1;


-- ===================================================================
-- 2. PERMISSION/ACCESS MATRIX
-- ===================================================================

-- 2.1 All profiles
SELECT * FROM profile ORDER BY id_profile;

-- 2.2 Full access matrix for a profile
SELECT p.profile_name, m.label, m.link,
       a.`view`, a.`add`, a.`edit`, a.`delete`
FROM access a
JOIN menu m ON a.id_menu = m.id_menu
JOIN profile p ON a.id_profile = p.id_profile
WHERE a.id_profile = {PROFILE_ID}
ORDER BY m.parent, m.sort;

-- 2.3 Menus without access records (orphan menus)
SELECT m.* FROM menu m
LEFT JOIN access a ON m.id_menu = a.id_menu
WHERE a.id_menu IS NULL AND m.active = 1 AND m.id_menu > 1;

-- 2.4 Access records without valid menu (orphan access)
SELECT a.* FROM access a
LEFT JOIN menu m ON a.id_menu = m.id_menu
WHERE m.id_menu IS NULL;

-- 2.5 Access records without valid profile (orphan access)
SELECT a.* FROM access a
LEFT JOIN profile p ON a.id_profile = p.id_profile
WHERE p.id_profile IS NULL;


-- ===================================================================
-- 3. METAL RATES
-- ===================================================================

-- 3.1 Latest 10 metal rates
SELECT id_metalrates, mjdmagoldrate_22ct, goldrate_22ct, goldrate_18ct,
       mjdmasilverrate_1gm, silverrate_1gm, updatetime, id_employee
FROM metal_rates
ORDER BY id_metalrates DESC LIMIT 10;

-- 3.2 Rate discount impact (calculate selling vs market)
SELECT mr.id_metalrates,
       mr.mjdmagoldrate_22ct AS market_22ct,
       mr.goldrate_22ct AS selling_22ct,
       (mr.mjdmagoldrate_22ct - mr.goldrate_22ct) AS discount_22ct,
       mr.mjdmasilverrate_1gm AS market_silver,
       mr.silverrate_1gm AS selling_silver,
       (mr.mjdmasilverrate_1gm - mr.silverrate_1gm) AS discount_silver
FROM metal_rates mr
ORDER BY mr.id_metalrates DESC LIMIT 5;

-- 3.3 Branch rate mappings
SELECT br.*, b.name AS branch_name, mr.goldrate_22ct, mr.silverrate_1gm, mr.updatetime
FROM branch_rate br
JOIN branch b ON br.id_branch = b.id_branch
JOIN metal_rates mr ON br.id_metalrate = mr.id_metalrates
WHERE br.status = 1
ORDER BY b.name;

-- 3.4 Branches without rate mapping
SELECT b.id_branch, b.name
FROM branch b
WHERE b.active = 1
  AND NOT EXISTS (
    SELECT 1 FROM branch_rate br WHERE br.id_branch = b.id_branch AND br.status = 1
  );


-- ===================================================================
-- 4. MASTER DATA INTEGRITY
-- ===================================================================

-- 4.1 Scheme classifications
SELECT * FROM sch_classify WHERE active = 1;

-- 4.2 Active banks
SELECT * FROM bank ORDER BY bank_name;

-- 4.3 Active payment modes
SELECT * FROM payment_mode ORDER BY mode_name;

-- 4.4 Active departments
SELECT * FROM department ORDER BY name;

-- 4.5 Active designations
SELECT * FROM designation ORDER BY name;

-- 4.6 Active branches
SELECT id_branch, name, code, active, branch_settings FROM branch WHERE active = 1;

-- 4.7 Active weights
SELECT * FROM weight WHERE active = 1 ORDER BY weight;

-- 4.8 Drawee accounts
SELECT da.*, b.bank_name FROM drawee_account da
LEFT JOIN bank b ON da.id_bank = b.id_bank;


-- ===================================================================
-- 5. GATEWAY & SMS CONFIGURATION
-- ===================================================================

-- 5.1 Payment gateway settings (check which is active)
SELECT * FROM gateway_settings;

-- 5.2 SMS API settings
SELECT * FROM sms_api_settings;

-- 5.3 Company mail settings
SELECT mail_server, send_through, smtp_user, smtp_host, server_type
FROM company LIMIT 1;


-- ===================================================================
-- 6. SYSTEM HEALTH
-- ===================================================================

-- 6.1 Menu hierarchy (check for orphan parents)
SELECT m.id_menu, m.label, m.parent, m.active,
       (SELECT label FROM menu p WHERE p.id_menu = m.parent) AS parent_label,
       (SELECT COUNT(*) FROM menu c WHERE c.parent = m.id_menu) AS child_count
FROM menu m
WHERE m.id_menu > 1
ORDER BY m.parent, m.sort;

-- 6.2 Dashboard menu sync check
SELECT dm.id_dashboardmenu, dm.label, dm.active,
       (SELECT COUNT(*) FROM dashboard_access da WHERE da.id_dashboardmenu = dm.id_dashboardmenu) AS access_count
FROM dashboard_menu dm
WHERE dm.active = 1;

-- 6.3 Backup history (last 5)
SELECT * FROM db_backup ORDER BY backup_date DESC LIMIT 5;

-- 6.4 Config settings state
SELECT * FROM config_settings WHERE id = 1;

-- 6.5 Negative selling rate check (discount > market)
SELECT id_metalrates, mjdmagoldrate_22ct, goldrate_22ct
FROM metal_rates
WHERE goldrate_22ct < 0 OR silverrate_1gm < 0;
