# FORENSIC TEMPLATE — Retail Dashboard
> Generated: 2026-03-16 | Module: Retail Dashboard
> Use this cheat sheet when diagnosing dashboard bugs.

---

## Layer 1 — Symptom Collection

**What type of dashboard issue is reported?**

| Symptom | Likely Layer |
|---|---|
| Widget shows 0 / blank / no data | JS AJAX failure OR wrong date filter |
| Wrong numbers (too high or too low) | SQL logic bug in model method |
| Stock count doesn't match actual | Tag status log desync bug |
| Credit amount is wrong | Cross-module credit collection query issue |
| Cash abstract totals don't match | ret_reports_model::getBillDetails logic |
| Chart not rendering | Google Charts API or JS rendering error |
| Branch filter not working | Controller not passing `id_branch` to model (known bug) |
| Customer count wrong | `get_CustomerDetails()` ignores branch entirely |
| Old vs new customer incorrect | Known bug — identical SQL for both (RULE-RETDASH-009) |
| Reorder alert fires incorrectly | `ret_reorder_settings` misconfiguration |
| Karigar order count wrong | Status filter or order_for mismatch |

---

## Layer 2 — Reproduce & Isolate

1. **Which tab/widget?** → Narrows the AJAX endpoint
2. **Which branch?** → Check if branch filter is applied correctly (see Known Risks)
3. **Which date range?** → Reproduce with today's date, then extend
4. **Is it all branches or one branch?** → If one, branch FK issue; if all, SQL logic issue
5. **Does the source table have the expected data?** → Direct DB query (see Layer 5)
6. **Is it a display bug or a data bug?** → Check raw JSON response vs what's shown

---

## Layer 3 — Client-Side Trace

### Console Checks
```javascript
// 1. Check AJAX response raw data
// In browser Console, monitor XHR calls to admin_ret_dashboard/*
// Key response keys to check:
//   dash_estmation: {created, sold, unsold}
//   dash_billing: {Gold: {wt, amt, count}, Silver: {...}}
//   dash_stock_details: {g_opening_pcs, g_inward_pcs, g_tot_sales_pcs, g_br_out_pcs, g_available_pcs}
//   dash_credeit_sales: {tot_credit_bill, tot_due_amount, creditreceived}

// 2. Check date filter values
console.log($('#payment_list1').text()); // from_date
console.log($('#payment_list2').text()); // to_date

// 3. Check active branch
console.log($('#id_branch').val()); // or equivalent branch selector
```

### Network Tab Checks
- Check request payload: `from_date`, `to_date`, `id_branch` — ensure correct values
- Check response: Look for JSON parse errors or empty arrays
- Check for 500 errors → means PHP exception in controller

---

## Layer 4 — Server-Side Trace

| Symptom | File | Method | Line | What to Check |
|---|---|---|---|---|
| Estimation widget wrong | model | `get_dashboard_estimation()` | L60-88 | `ret_estimation.esti_for=1` filter? Date format? |
| Billing metal breakdown wrong | model | `get_dashboard_billings()` | L132-183 | `GROUP BY m.metal` correct? `bill_status=1` filter? |
| MRP count wrong | model | `get_dashboard_billings_mrp()` | L215-241 | `pro.sales_mode=1` — is MRP product flagged correctly? |
| Diamond count wrong | model | `get_dashboard_billings_dia()` | L245-265 | `stn.stone_type=1` — is stone_type correctly set? |
| Green tag incentive wrong | model | `get_dashboard_greentag_det()` | L273-339 | Check `ret_settings` values for incentive rates |
| Stock counter wrong | model | `AvailableStockDetails()` | L2008-2257 | Complex self-join on `ret_taging_status_log` — check status codes |
| Credit received wrong | model | `get_dashboard_credit_sales()` | L361-393 | Two sources: `ret_billing.bill_type=8` + `ret_issue_credit_collection_details` |
| Branch filter not applied | controller | any method | Check if `$id_branch` passed to model call | See Known Risks bugs list |
| Cash abstract wrong | controller | `get_cash_abstract_details()` | L1702-2083 | Uses `ret_reports_model` — debug there first |
| Order count wrong | model | `karigar_orders()` | L1689-1753 | Check `order_for=1` (karigar) vs `order_for=2` (customer) |

**Quick debug pattern (add to controller):**
```php
// After model call, add temporarily:
echo $this->db->last_query(); exit;
```

---

## Layer 5 — Database Verification

```sql
-- 1. Verify today's estimation counts
SELECT
  COUNT(*) as total_created,
  SUM(CASE WHEN estitm.purchase_status=1 THEN 1 ELSE 0 END) as sold_est
FROM ret_estimation est
LEFT JOIN ret_estimation_items estitm ON estitm.esti_id=est.estimation_id
WHERE date(est.estimation_datetime) = CURDATE()
  AND est.id_branch = {BRANCH_ID};

-- 2. Verify gold billing for today
SELECT SUM(bd.net_wt) as gold_nwt, SUM(bd.item_cost) as gold_amt, COUNT(b.bill_id) as bill_count
FROM ret_billing b
LEFT JOIN ret_bill_details bd ON bd.bill_id=b.bill_id
LEFT JOIN ret_product_master p ON p.pro_id=bd.product_id
LEFT JOIN ret_category c ON c.id_ret_category=p.cat_id
WHERE date(b.bill_date)=CURDATE() AND b.bill_status=1 AND c.id_metal=1
  AND b.id_branch={BRANCH_ID};

-- 3. Verify available gold stock (current state)
SELECT COUNT(*) as available_tags, SUM(t.gross_wt) as available_gwt
FROM ret_taging_status_log m1
LEFT JOIN ret_taging_status_log m2 ON (m1.tag_id=m2.tag_id AND m1.id_tag_status_log<m2.id_tag_status_log)
LEFT JOIN ret_taging t ON t.tag_id=m1.tag_id
LEFT JOIN ret_product_master p ON p.pro_id=t.product_id
LEFT JOIN ret_category c ON c.id_ret_category=p.cat_id
WHERE m2.id_tag_status_log IS NULL AND (m1.status=0 OR m1.status=6)
  AND m1.to_branch={BRANCH_ID} AND c.id_metal=1;

-- 4. Verify credit outstanding
SELECT COUNT(*) as credit_bills, SUM(tot_bill_amount-tot_amt_received) as outstanding
FROM ret_billing WHERE is_credit=1 AND bill_status=1 AND bill_type!=8
  AND id_branch={BRANCH_ID}
  AND date(bill_date) BETWEEN '{FROM}' AND '{TO}';

-- 5. Verify green tag incentive rate config
SELECT name, value FROM ret_settings
WHERE name IN ('emp_sales_incentive_gold_perg', 'emp_sales_incentive_silver_perg');

-- 6. Check for orphan bill details (data integrity)
SELECT COUNT(*) FROM ret_bill_details bd
LEFT JOIN ret_billing b ON b.bill_id=bd.bill_id WHERE b.bill_id IS NULL;

-- 7. Check karigar order status distribution
SELECT orderstatus, COUNT(*) as cnt FROM customerorderdetails od
LEFT JOIN customerorder o ON o.id_customerorder=od.id_customerorder
WHERE o.order_for=1 AND date(o.order_date)=CURDATE()
GROUP BY orderstatus;
```

---

## Layer 6 — Root Cause Classification

| Category | Risk | Examples |
|---|---|---|
| **SQL injection** | CRITICAL | All model methods — direct param concatenation |
| **Missing branch filter** | HIGH | `get_CustomerDetails`, `get_branch_transfer_details` |
| **Division by zero** | MEDIUM | `get_saleschart_details` L1391 |
| **Copy-paste logic bug** | HIGH | `get_BillClassficationDetails` — old/new customer |
| **Dead code confusion** | MEDIUM | `get_dashboard_cash_abstarct_details()` model method |
| **Date format issue** | MEDIUM | Date strings vs Y-m-d format in SQL |
| **Cross-model dependency** | MEDIUM | `ret_reports_model` structure changes silently break dashboard |
| **Tag status desync** | HIGH | Complex self-join on `ret_taging_status_log` — wrong stock if log is inconsistent |

---

## Layer 7 — Cross-Module Integration Trace

> Added because module has critical cross-model dependencies.

**When Cash Abstract is wrong:**
1. Controller `get_cash_abstract_details()` calls `ret_reports_model::getBillDetails($_POST)`
2. Debug `ret_reports_model.php` — the `getBillDetails()` method
3. Check that the return array keys match what controller expects: `item_details`, `return_details`, `old_matel_details`, `advance_detals`, `general_adv_details`, `credit_details`, `due_details`, `payment_details`, `chit_details`, `voucher_details`, `order_adj`, `advance_adjusted`, `bill_det`, `other_expense`, `general_pay`, `advance_deposit`, `adv_refund`, `general_credit_collection`, `chit_credit_collection`, `repair_order_delivered`
4. If any key is missing → PHP `Undefined index` → value defaults to 0 silently

**When Ledger Alert is wrong:**
1. `get_LedgerBalanceAlert()` loops each ledger with `min_balance>0`
2. For each: calls `ret_reports_model::getLedgerReportData($post_data)` with opening_date to today
3. Manually computes running balance from last date's entries
4. Compare: check if `opening_date` column exists in `ledger_master` table and has correct value
5. Check `ledger_master.min_balance` against what DB shows

---

## Layer 8 — API Controller Diagnostics (admin_ret_dashboard_api)

> Use this section when an **API dashboard tab** (Karigar Stock, PO, Rate Fix, QC, Account Stock, etc.) shows wrong data.

### Common First Steps

```javascript
// All API endpoints POST to admin_ret_dashboard_api/*
// Check request payload in Network tab:
// from_date, to_date, id_branch[], id_metal[], id_profile
// All responses are JSON via REST_Controller response()
```

```php
// Inside any API model method, add temporarily:
echo $this->db->last_query(); exit;
```

### Symptom → API Diagnosis Map

| Symptom | API Endpoint | Method to Check | Key Issue |
|---|---|---|---|
| Monthly chart shows wrong bracket | `get_monthly_sales_details` | `get_monthly_sales()` L329 | N+1 queries; check FY from `ret_financial_year` |
| Top sellers not showing correct karigar | `get_top_sellers` | `get_top_sellers()` L279 | Check `ret_karigar.karigar_name` and billing linkage |
| Karigar stock count wrong | `get_karigar_stock` | `get_karigar_stock()` L877 | Check `ret_taging` status + `ret_karigar_id` FK |
| Metal filter not applied on wastage | `(internal to store_wise_sales)` | `get_branch_wastage()` L666 | **Bug #21** — `$id_metal` missing from signature |
| Account stock shows all records (no filter) | `get_accountstock_inwards` | `get_accountstock_inwards_details()` L2223 | **Bug #22** — `$id_category` + `$data` undefined |
| Rate cut P&L shows historical data only | `get_rate_cut_profit_loss` | `get_rate_cut_profit_loss()` L2632 | **Bug #23** — WHERE clause uses `<= from_date`, `to_date` ignored |
| Delayed POs list incorrect | `get_delayed_purchase_orders` | `get_delayed_purchase_orders()` L1449 | Check `smith_due_date < CURRENT_DATE()` and `pur_no IS NOT NULL` |
| QC widget shows 0 | `get_qc_details` | `get_qc_details()` L1903 | Verify `ret_po_qc_issue_process` has entries + JOIN |
| Supplier approval ledger blank | `get_supplier_crde` | `getMetalwiseApprovalTransactionList()` L1806 | Depends on `ret_view_supplier_approval_ledger` VIEW — verify VIEW exists in DB |
| Rate fixed/unfixed weights wrong | `get_rate_fixed` / `get_rate_unfixed` | `get_rate_fixed_details()` L2062 | Check `isratefixed` flag in `ret_purchase_order` |
| EDA bills showing in regular branch dash | Any API tab | `get_profile_settings()` L2638 | Check `profile.allow_bill_type` value for this user |
| Wrong billing amounts on sales glance | `get_sales_glance` | `get_dashboard_sales_glance()` L45 | Check `is_eda` filter applied correctly (RULE-RETDASH-014) |

### Quick API DB Verification Queries

```sql
-- 1. Check profile allow_bill_type for a user
SELECT id_profile, allow_bill_type FROM profile WHERE id_profile = {PROFILE_ID};

-- 2. Verify delayed karigar POs
SELECT c.id_customerorder, d.smith_due_date, d.orderstatus,
  DATEDIFF(CURRENT_DATE(), d.smith_due_date) as days_overdue
FROM customerorder c
JOIN customerorderdetails d ON d.id_customerorder = c.id_customerorder
WHERE c.order_type=1 AND c.pur_no IS NOT NULL
  AND c.order_status NOT IN (6,7) AND d.orderstatus NOT IN (6,7)
  AND d.smith_due_date < CURRENT_DATE()
ORDER BY days_overdue DESC LIMIT 20;

-- 3. Check rate cut entries for a date range
SELECT id_supplier_rate_cut, weight, rate_per_gram, rate_cut_type, conversion_type, date_add
FROM ret_supplier_rate_cut
WHERE date_add BETWEEN '{FROM}' AND '{TO}'  -- Note: Bug #23 ignores to_date!
  AND status=1 ORDER BY date_add DESC;

-- 4. Verify supplier approval ledger VIEW exists
SHOW FULL TABLES WHERE table_type='VIEW' AND Tables_in_{DB} LIKE 'ret_view_supplier_approval_ledger';

-- 5. Check QC issue counts
SELECT count(*) as qc_count, COALESCE(SUM(d.failed_pcs),0) as failed_pcs
FROM ret_po_qc_issue_process p
LEFT JOIN ret_po_qc_issue_details d ON d.qc_process_id=p.qc_process_id
LEFT JOIN ret_purchase_order_items i ON i.po_item_id=d.po_item_id
WHERE p.created_at BETWEEN '{FROM}' AND '{TO}';
```

### API Orphan Methods (CORRECTED — Round 10/12)

> **Previous list was wrong.** The methods below were classified as orphans before the API controller deep read (Round 10). After reading all 1,998 lines of `admin_ret_dashboard_api.php`, the correct orphan status is:

**✅ TRUE Orphans (2 only — no caller found anywhere):**

| Method | Lines | Notes |
|---|---|---|
| `get_branch_sales()` | L647 | Superseded by `get_store_sales()` |
| `get_rate_cut_details()` | L2522 | Possible helper or dead code |

**❌ Previously Misclassified as Orphan (has API ctrl caller):**

| Method | Lines | Actual Caller | Context |
|---|---|---|---|
| `get_custome_wise_sale()` | L1045 | `get_custome_wise_sale_post()` L261 | Customer-type wise sale |
| `get_dashboard_estimation()` | L1131 | `get_EstimationStatus_post()` L942 | Estimation status widget |
| `get_dashboard_virturaltag_details()` | L1178 | `get_VitrualTag_post()` L998 | ⚠️ date hardcoded (Bug #26) |
| `get_dashboard_salesreturn_det()` | L1214 | `get_SalesReturn_post()` L1037 | ⚠️ date hardcoded (Bug #26) |
| `get_dashboard_lot_tag_details()` | L1242 | `get_LotDetails_post()` L1075 | ⚠️ date hardcoded (Bug #26) |
| `get_cover_up_report()` | L1268 | `get_CoverUpReport_post()` L1161 | ⚠️ date hardcoded (Bug #26) |
| `get_purchase_inwards()` | L1418 | `get_purchase_inwards_post()` L1201 | Purchase inwards GWT/NWT |
| `get_dashboard_breakeven_details()` | L1929 | `get_FinancialStatus_post()` L1113 | Breakeven targets |
| `get_outward_details()` | L1702 | `get_outward_details_post()` L1279 | Purchase return + outward |
