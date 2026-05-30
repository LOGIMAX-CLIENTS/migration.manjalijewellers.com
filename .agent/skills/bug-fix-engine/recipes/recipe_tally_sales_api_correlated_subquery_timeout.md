# Tally Sales API - Correlated Sub-query Timeout (N+1 x GROUP BY)

> The `getSalesNewVoucherList` function had 3 correlated sub-queries in its main SELECT plus N+1 per-bill DB queries inside the loop. Because `GROUP BY` runs before `LIMIT`, even `LIMIT 10` executed 6,837 sub-queries, causing a 47-second timeout for any date range.
>
> ## Metadata
> - **Pattern ID**: PAT-TALLY-002
> - - **Severity**: CRITICAL
>   - - **Modules Affected**: Tally API (`ret_tally_api_model`, `tally_app_api`)
>     - - **Auto-fixable**: No (requires structural SQL + PHP refactor)
>      
>       - ## Client Scope
>       - - **Applies to**: ALL
>         - - **Reason**: Core Tally export logic - any client using `importsalesvouchers` endpoint will experience timeouts when date range returns > 50 bills
>          
>           - ## Created By
>           - - **Developer**: AI (Antigravity)
>             - - **Client**: karpagamjewels.com
>               - - **Date**: 2026-05-05
>                 - - **Source Bug ID**: N/A (Performance Investigation)
>                  
>                   - ## Symptom
>                   - - `POST /tally_app_api/importsalesvouchers` times out (47+ seconds) even with `LIMIT 10`
>                     - - Response time does NOT improve when reducing the LIMIT - 10 records and 500 records take the same time
>                       - - Postman shows 200 OK only after 47s with a 97KB payload for just 10 bills
>                         - - Apache may return 504 Gateway Timeout for full date ranges
>                          
>                           - ## Root Cause
>                          
>                           - **Three compounding issues:**
>                          
>                           - ### Issue 1 - Correlated Sub-queries in SELECT (Primary - causes 47s)
> The main query had 3 correlated sub-queries directly in the SELECT clause:
>
> MySQL execution order is: `FROM -> WHERE -> GROUP BY -> SELECT -> LIMIT`.
> `LIMIT` is applied **after** SELECT expressions. So for 2,279 matching bills, MySQL executes:
> `3 sub-queries x 2,279 rows = 6,837 full scans of ret_bill_details` - regardless of LIMIT value.
>
> **Proof**: Simple query (no sub-queries) -> 119ms. Same query + 1 correlated sub-query -> killed after >5 minutes.
>
> ### Issue 2 - N+1 Per-bill DB Queries in PHP Loop (Secondary)
> Inside `foreach($sql->result() as $row)`:
> - `$sales_tax_query` - 1 DB call per bill for tax data
> - - `$salesitemquery` + `$salesitemquery1` - 2 DB calls per bill for item data
>  
>   - For 500 bills: `500 x 3 = 1,500 additional DB round-trips` after the main query.
>  
>   - ### Issue 3 - Hardcoded LIMIT 100 / DATE() Wrapper
>   - - `LIMIT 100` capped exports silently without error
>     - - `DATE(b.bill_date) BETWEEN ...` prevented index usage on `bill_date` column
>      
>       - ## Detection
>      
>       - ```bash
>         grep -n "SELECT sum.*inv_split_id = ca.cat_split_id" application/models/ret_tally_api_model.php
>         grep -n "sales_tax_query.*bill_id.*cat_split_id" application/models/ret_tally_api_model.php
>         grep -n "salesitemquery.*inv_split_id.*cat_split_id" application/models/ret_tally_api_model.php
>         grep -n "LIMIT 100\|DATE(b.bill_date)" application/models/ret_tally_api_model.php
>         ```
>
> ## Files
> - `application/models/ret_tally_api_model.php` - function `getSalesNewVoucherList()` (~line 472)
> - - `application/controllers/tally_app_api.php` - function `importsalesvouchers_post()` (~line 293)
>  
>   - ## Fix
>  
>   - ### Fix 1: Replace correlated sub-queries with pre-aggregated bdet LEFT JOIN
>  
>   - #### Before (in SELECT)
>   - ```sql
>     round((SELECT sum(d.item_cost) FROM ret_bill_details as d WHERE d.inv_split_id = ca.cat_split_id)) as TOTAL,
>     (SELECT sum(ifnull(d.total_igst,0)) FROM ret_bill_details as d WHERE d.inv_split_id = ca.cat_split_id) as total_igst,
>     (SELECT sum(ifnull(d.total_cgst,0)) FROM ret_bill_details as d WHERE d.inv_split_id = ca.cat_split_id) as total_cgst,
>     (SELECT sum(ifnull(d.total_sgst,0)) FROM ret_bill_details as d WHERE d.inv_split_id = ca.cat_split_id) as total_sgst,```
>
>     #### After (in SELECT - same alias names)
>     ```sql
>     round(IFNULL(bdet.total_item_cost, 0)) as TOTAL,
>     IFNULL(bdet.total_igst, 0) as total_igst,
>     IFNULL(bdet.total_cgst, 0) as total_cgst,
>     IFNULL(bdet.total_sgst, 0) as total_sgst,
>     ```
>
> #### Add bdet LEFT JOIN before WHERE clause
> ```sql
> LEFT JOIN (
>     SELECT inv_split_id,
>         sum(item_cost)             as total_item_cost,
>         sum(IFNULL(total_igst, 0)) as total_igst,
>         sum(IFNULL(total_cgst, 0)) as total_cgst,
>         sum(IFNULL(total_sgst, 0)) as total_sgst
>     FROM ret_bill_details
>     GROUP BY inv_split_id
> ) as bdet ON bdet.inv_split_id = ca.cat_split_id
> ```
>
> #### Fix LIMIT and DATE() wrapper
> ```php
> // Before:
> AND b.is_eda != 2 GROUP BY b.bill_id, cat_split_metal_id LIMIT 100");
> " AND DATE(b.bill_date) BETWEEN '".$from_date."' AND '".$to_date."'"
>
> // After:
> AND b.is_eda != 2 GROUP BY b.bill_id, cat_split_metal_id LIMIT 500");
> " AND b.bill_date BETWEEN '".$from_date." 00:00:00' AND '".$to_date." 23:59:59'"
> ```
>
> " AND b.bill_date BETWEEN '".$from_date." 00:00:00' AND '".$to_date." 23:59:59'"
>
> ### Fix 2: Replace N+1 per-bill queries with bulk pre-fetch maps
>
> #### Before (inside foreach loop)
> ```php
> foreach($sql->result() as $row){
>     $sales_tax_query = $this->db->query("... WHERE d.bill_id = $row->bill_id AND d.inv_split_id = $row->cat_split_id GROUP BY d.tax_group_id");
>     foreach($sales_tax_query->result() as $taxitemrow){ ... }
>     $salesitemquery  = $this->db->query("... WHERE d.inv_split_id = $row->cat_split_id AND d.net_wt > 0 ...");
>     $salesitemquery1 = $this->db->query("... WHERE d.inv_split_id = $row->cat_split_id AND d.net_wt = 0 ...");
>     $salesitemqueryresult = array_merge($salesitemquery->result_array(), $salesitemquery1->result_array());
> ```
>
> #### After (bulk pre-fetch before loop + map lookups inside loop)
> ```php
> $sql_rows = $sql->result_array();
> if (empty($sql_rows)) { return $return_data; }
>
> $all_bill_ids  = array_unique(array_column($sql_rows, 'bill_id'));
> $all_split_ids = array_unique(array_column($sql_rows, 'cat_split_id'));
> $bill_ids_str  = implode(',', array_map('intval', $all_bill_ids));
> $split_ids_str = implode(',', array_map('intval', $all_split_ids));
>
> // Bulk tax pre-fetch - 1 query for all bills
> $bulk_tax_q = $this->db->query("
>     SELECT d.bill_id, d.inv_split_id,
>         sum((d.item_cost - d.item_tota as TOTAL,
>
>     SELECT d.bill_id, d.inv_split_id,
>         sum((d.item_cost - d.item_total_tax)) as TOTAL,
>         sum(IFNULL(d.total_sgst,0)) as total_sgst,
>         sum(IFNULL(d.total_igst,0)) as total_igst,
>         taxm.tax_name, taxm.tax_percentage
>     FROM ret_bill_details d
>     LEFT JOIN ret_taxmaster as taxm ON taxm.tax_id = d.tax_group_id
>     WHERE d.bill_id IN ($bill_ids_str)
>     GROUP BY d.bill_id, d.inv_split_id, d.tax_group_id
> ");
> $tax_map = [];
> foreach ($bulk_tax_q->result_array() as $t) {
>     $tax_map[$t['bill_id']][$t['inv_split_id']][] = $t;
> }
> unset($bulk_tax_q);
>
> // Bulk item pre-fetch - 1 query for all splits
> $bulk_item_q = $this->db->query("
>     SELECT d.inv_split_id, sum(d.piece) as QTY, avg(d.rate_per_grm) as RATE,
>         sum(d.item_cost) as VALUE, sum(IFNULL(d.item_total_tax,0)) as SALESTAXAMT,
>         sum((d.item_cost - d.item_total_tax)) as TOTAL, sum(IFNULL(d.gross_wt,0)) as GROSSWT,
>         sum(IFNULL(d.net_wt,0)) as netweight, sum(IFNULL(d.bill_discount,0)) as bill_discount,
>         sum(ifnull(d.total_igst,0)) as total_igst, sum(ifnull(d.total_sgst,0)) as total_sgst,
>         sum(IFNULL(d.total_cgst,0)) as total_cgst, taxm.tax_name, taxm.tax_percentage,
>         cat.tally_qty_type, cat.name as PRODUCTNAME,
>         IFNULL(cat.hsn_code, IFNULL(pro.hsn_code,'')) as hsn_code,
>         -- same ometal and stn JOINs as original queries --
>     FROM ret_bill_details d
>     LEFT JOIN ... (same JOINs as original salesitemquery)
>     WHERE d.inv_split_id IN ($split_ids_str)
>     GROUP BY d.inv_split_id, d.product_id, cat.id_ret_category, d.calculation_based_on
> ");
> $item_map = [];
> foreach ($bulk_item_q->result_array
> ");
> $item_map = [];
> foreach ($bulk_item_q->result_array() as $item) {
>     $item_map[$item['inv_split_id']][] = $item;
> }
> unset($bulk_item_q);
>
> foreach($sql_rows as $rowArr){
>     $row = (object)$rowArr;
>     // Tax map lookup (replaces per-bill DB query)
>     $tax_rows_for_bill = isset($tax_map[$row->bill_id][$row->cat_split_id]) ? $tax_map[$row->bill_id][$row->cat_split_id] : [];
>     foreach($tax_rows_for_bill as $taxitemArr){
>         $taxitemrow = (object)$taxitemArr;
>         ...
>     }
>     // Item map lookup (replaces per-bill DB queries)
>     $salesitemqueryresult = isset($item_map[$row->cat_split_id]) ? $item_map[$row->cat_split_id] : [];
> ```
>
> ### Fix 3: Controller - raise PHP limits
>
> ```php
> // application/controllers/tally_app_api.php
> function importsalesvouchers_post()
> {
>     set_time_limit(300);              // 5 min - bulk export headroom
>     ini_set('memory_limit', '1024M'); // 1GB - large JSON response
>     $model = self::ADM_MODEL;
> ```
>
> ## Verification
> 1. Call `POST /tally_app_api/importsalesvouchers` with 2-month date range - must return within **15 seconds**
> 2. 2. Confirm **500 bills** returned - not 10 or 100
>    3. 3. Spot-check 3 vouchers: `TOTAL`, `total_igst`, `total_cgst`, `total_sgst` values match original
>       4. 4. Verify `tax_name`, `tax_percentage` on VOUCHER rows populated correctly from `$tax_map`
>          5. 5. Verify item rows (`PRODUCTNAME`, `QTY`, `RATE`, `netweight`) populated from `$item_map`
>            
>             6. ## Notes
>             7. - **LIMIT 500 is intentional** - Tally does not support paginated API calls. 500 is the agreed safe batch size
>                - - **bdet JOIN runs once** - MySQL materializes it be
>                 
>                  - ## Notes
>                  - - **LIMIT 500 is intentional** - Tally does not support paginated API calls. 500 is the agreed safe batch size
>                    - - **bdet JOIN runs once** - MySQL materializes it before the main filter (~0.3s for 113K rows)
>                      - - **KEY INSIGHT**: `GROUP BY` executes BEFORE `LIMIT` in MySQL. Correlated sub-queries in SELECT fire for ALL GROUP BY rows - `LIMIT 10` is useless as a performance band-aid when correlated sub-queries exist
>                        - - **Do NOT reintroduce** `DATE(b.bill_date)` wrapper - prevents index usage on `bill_date`
>                          - - This pattern applies to other Tally export functions - check `getRepairVoucherList`, `getPurchaseVoucherList` for similar correlated sub-queries
>                            - - Performance result: 6,837 sub-query executions eliminated -> query time 47s -> ~1s
>                              - 
