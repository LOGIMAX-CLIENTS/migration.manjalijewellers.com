# BUSINESS RULES — Retail Dashboard
> Generated: 2026-03-16 | Module: Retail Dashboard

---

## RULE-RETDASH-001: Estimation Status Classification
**Formula:** `if purchase_status=1 → Sold | if purchase_status=0 → Unbilled | if purchase_status=2 → Returned | else → In Process`
**Implementation:** Model L96-97 (get_dashboard_estimation_details), JS rendering
**Validation:** Server-side SQL CASE in model
**Source:** `ret_estimation_items.purchase_status`

---

## RULE-RETDASH-002: Bill Type Labels
**Formula:**
- 1=Sales, 2=Sales & Purchase, 3=Sales & Return, 4=Purchase
- 5=Order Advance, 6=Advance, 7=Sales Return
- 8=Credit Bill Payment, 9=Order Delivery
- 10=Chit Pre Close, 11=Repair Order Delivery
- 12=Supplier Sales Bill, 13=Sales Transfer, 14=Sales Ret Transfer
- else=Approval stock bill delivery
**Implementation:** Model L540-570 (`get_dashboard_bills_details()`)
**Validation:** Server-side CASE statement in SQL
**Source:** `ret_billing.bill_type`

---

## RULE-RETDASH-003: Green Tag Definition
**Formula:** A tag is "green" when `tag.tag_mark=1 AND tag.tag_status=1`
**Implementation:** Model L301, L329 (`get_dashboard_greentag_det()`)
**Source:** `ret_taging.tag_mark`, `ret_taging.tag_status`

---

## RULE-RETDASH-004: Employee Incentive Calculation (Green Tag)
**Formula:**
`incentive = (gold_net_wt × gold_incentive_rate_per_gram) + (silver_net_wt × silver_incentive_rate_per_gram)`
**Implementation:** Model L273, L287-325 — rates fetched from `ret_settings` WHERE name IN ('emp_sales_incentive_gold_perg', 'emp_sales_incentive_silver_perg')
**Validation:** Server-side SQL subquery
**Edge cases:** Rates must be configured in ret_settings or result will be 0

---

## RULE-RETDASH-005: Available Stock Formula
**Formula:** `available = opening_balance + inward_pcs - sold_pcs - branch_out_pcs`
**Implementation:** Model L2247-2251 (`AvailableStockDetails()`), L2498-2500 (`Available_SilverStockDetails()`)
**Also:** Weights: `available_gwt = op_blc_gwt + inw_gwt - sold_gwt - br_out_gwt`
**Validation:** Server-side computation using tag status log

---

## RULE-RETDASH-006: Virtual Tag Types
- **Home Sale:** `d.esti_item_id IS NOT null AND d.tag_id IS null AND e.item_type=2` — item from estimation, no physical tag
- **Tag Split (Partial Sale):** `d.tag_id IS NOT null AND d.is_partial_sale=1` — part of a tag sold
**Implementation:** Model L614-642 (`get_dashboard_virturaltag_details()`)
**Source:** `ret_bill_details.esti_item_id`, `ret_bill_details.is_partial_sale`, `ret_estimation_items.item_type`

---

## RULE-RETDASH-007: Karigar Order Status Filters
- `'T'` = Today Delivered: `delivered_date = CURDATE() AND orderstatus=5`
- `'TM'` = Tomorrow Delivery: `smith_due_date = CURDATE() + INTERVAL 1 DAY`
- `'TODDY_PENDING'` = Today Yet-To-Deliver: `smith_due_date = CURDATE() AND orderstatus<=3`
- `'OVER_DUE'` = Overdue: `smith_due_date < CURDATE() AND orderstatus=3`
- `'WIP'` = Work In Progress: `orderstatus=3`
**Implementation:** Model L1705-1739 (`karigar_orders()`)
**Also applies to:** Customer orders (model L1835+ `customer_orders()`) with `cus_due_date` instead of `smith_due_date`

---

## RULE-RETDASH-008: Customer Order Status Values
- 0 = Received/New
- 1 = Placed (for online orders in `order_cart`)
- 2 = Allocated
- 3 = WIP (Work in Progress)
- 4 = Ready for Delivery
- 5 = Delivered
**Implementation:** Model L1871, L1883, L1895, L1907, L1921
**Source:** `customerorderdetails.orderstatus`

---

## RULE-RETDASH-009: Bill Classification (New vs Old Customer)
**Intent:** Count bills from NEW customers (first-time buyers) vs OLD customers
**Formula (intended):**
- New customer bill = customer who has NO bills before `from_date`
- Old customer bill = customer who HAS bills before `from_date`
**Implementation:** Model L411-451 — ⚠️ **BUG: both queries use identical SQL** (`AND NOT EXISTS...`) — old customer query does NOT use `EXISTS` opposite, so both return same count.
**Validation:** None — server-side only, no client validation

---

## RULE-RETDASH-010: Reorder Alert Rule
**Formula:** Item triggers reorder when `available_pcs < min_pcs AND item not already in order_cart`
**Implementation:** Model L2965 (`getReorderItems()`)
**Source:** `ret_reorder_settings.min_pcs`, real-time tagging query via `getTagging()`

---

## RULE-RETDASH-011: Credit Sales vs Credit Received
- **Credit outstanding** = `SUM(tot_bill_amount - tot_amt_received)` WHERE `is_credit=1 AND bill_type!=8`
- **Credit received via direct bill** = `SUM(tot_amt_received)` WHERE `bill_type=8`
- **Credit received via receipt** = `SUM(d.received_amount)` FROM `ret_issue_credit_collection_details` WHERE `type=2 AND receipt_type=1`
**Implementation:** Model L361-393 (`get_dashboard_credit_sales()`)

---

## RULE-RETDASH-012: Estimation Detail Type Filter (get_estimation_details)
**Formula:**
- type=0 (all): show discount, chit_amt, item_cost, sales_amt; total_cost = full total
- type=1 (purchase only): skip chit; total_cost = item_cost - discount
- type=2 (sales only): skip discount; total_cost = sales_amt + chit_amt
**Implementation:** Controller L973-994 (`get_estimation_details()`)
**Validation:** Client-side filter applied — server returns all fields, controller masks based on type

---

## RULE-RETDASH-013: Tag Status Codes (for stock computation)
- Status 0 = Available/Received at branch
- Status 1 = Sold
- Status 2 = Sent to another branch (in transit out)
- Status 3 = Received at destination (transit complete)
- Status 4 = Items in transit (tag_status=4 in tagging)
- Status 5,7,9,10,12 = Various transfer/approval states
- Status 6 = Green tag sold (still shows as available for non-green-tag stock)
**Implementation:** Model L1961, L2161-2168, `AvailableStockDetails()` subqueries
**Source:** `ret_taging_status_log.status`

---

## RULE-RETDASH-014: EDA Profile Bill-Type Filter (API Model)

**Formula:**
- `allow_bill_type = 3` → include BOTH EDA and non-EDA bills: `WHERE (b.is_eda=1 OR b.is_eda=2)`
- `allow_bill_type = 1` → EDA bills only: `WHERE b.is_eda=1`
- `allow_bill_type = 2` (default) → non-EDA bills only: `WHERE b.is_eda=2`

**Purpose:** Separates jewellery showroom billing modes — EDA (estimated daily account / gold scheme billing) vs regular counter billing. Each profile is configured to see only their relevant bills.
**Implementation:** `ret_dashboard_api_model.php` — used in 15+ methods via `get_profile_settings($id_profile)` → `$profile_settings['allow_bill_type']`
**Source:** `profile.allow_bill_type` (FK: `profile.id_profile` = `session.profile`)

---

## RULE-RETDASH-015: Delayed Purchase Order Definition

**Formula:** A PO is "delayed" when:
- `c.order_type = 1` (karigar job order) AND
- `c.pur_no IS NOT NULL` (has a PO number assigned) AND
- `c.order_status NOT IN (6, 7)` AND `d.orderstatus NOT IN (6, 7)` (not cancelled/rejected) AND
- `d.smith_due_date < CURRENT_DATE()` (due date is in the past)

**Display:** Sorted by `DATEDIFF(CURRENT_DATE(), smith_due_date) DESC` — most overdue first.
**Implementation:** `get_delayed_purchase_orders()` — `ret_dashboard_api_model.php` L1449
**Source:** `customerorder`, `customerorderdetails`

---

## RULE-RETDASH-016: Breakeven Target Calculation

**Formula:**
- **Daily mode (`rep_type=1`):** `target = brevn_log_gold_val × days_elapsed_in_FY`  
  where `days_elapsed = DATEDIFF(today, fin_year_from)` from `ret_financial_year`
- **Period mode (`rep_type=0`):** `target` is fetched directly from `ret_breakeven_logs` for the date range (no multiplication)
- **Achievement** = actual `SUM(d.gross_wt)` from `ret_billing` filtered by from_date/to_date
- **Display%** = `(achievement / target) × 100`

**Metal routing:** `id_metal=1` → use `goldwt`; `id_metal=2` → use `silverwt`; else → use `diawt`
**Implementation:** `get_dashboard_breakeven_details()` — `ret_dashboard_api_model.php` L1929
**Source:** `ret_breakeven_logs`, `ret_financial_year`, `ret_billing`

---

## RULE-RETDASH-017: Cover-Up Position (Hedging) Calculation

**Formula:**
```
coverup_required_wt = SUM(sales.pure_wt)
                     + (if gold: chit_collection.pure_wt)
                     - sales_return.pure_wt
                     - old_metal.pure_wt

covered_wt = SUM(ret_cover_up.weight) + GRN_pure_wt

pending = covered_wt - coverup_required_wt
  → if pending < 0: label = "Pending Positions" (short)
  → if pending > 0: label = "Excess Positions" (over-hedged)
```

**Key rule:** Chit credit collection (scheme customer cash payments) is included ONLY when filtering for gold (`id_metal=1`).
**Implementation:** `get_cover_up_report()` — `ret_dashboard_api_model.php` L1268
**Source:** `ret_billing`, `ret_bill_details`, `ret_bill_old_metal_sale_details`, `payment_mode_details`, `payment`, `ret_cover_up`, `ret_purchase_order`, `ret_purchase_order_items`

---

## RULE-RETDASH-018: Rate Fix vs Rate Unfix Classification

**Rate-Fixed (`ret_supplier_rate_cut.conversion_type=1`):**
- A specific price per gram has been locked in for the metal
- Recorded in `ret_po_rate_fix` OR `ret_supplier_rate_cut WHERE rate_cut_type=2 AND conversion_type=1`
- `balance_weight` = already priced; reduces supplier's open position

**Rate-Unfixed (`ret_supplier_rate_cut.conversion_type=2` OR `ret_purchase_order.isratefixed=0`):**
- Metal received but price not yet fixed (supplier still at market risk)
- `balance_weight = item_pure_wt - pur_ret_pur_wt - ratefixwt`
- PO unfixed: `WHERE isratefixed=0 AND rfp.rate_fix_po_item_id IS NULL`
- Rate-cut unfixed: `WHERE conversion_type=2 AND status=1`

**Implementation:** `get_rate_fixed_details()` + `get_rate_unfixing_details()` — `ret_dashboard_api_model.php` L2062, L2130
**Source:** `ret_po_rate_fix`, `ret_supplier_rate_cut`, `ret_purchase_order`, `ret_purchase_order_items`
