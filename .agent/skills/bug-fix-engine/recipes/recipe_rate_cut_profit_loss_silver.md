# Recipe: Dashboard Profit & Loss – Incorrect Current Rate Display for Silver Rate Cut Analysis

> In the Dashboard Profit & Loss – Rate Cut Analysis, supplier purchase rate cuts display the Gold 24KT rate and calculate profit/loss based on the Gold rate even when the rate cut transaction is for Silver.

## Metadata
- **Pattern ID**: PAT-REP-023
- **Severity**: HIGH
- **Modules Affected**: Retail Dashboard (admin_ret_dashboard_api, ret_dashboard_api_model)
- **Auto-fixable**: Yes (code replacement)

## Client Scope
- **Applies to**: ALL (any client using Dashboard Profit & Loss - Rate Cut Analysis)
- **Reason**: Common SQL logic defect in date filter, branch filter, and metal-wise rate mapping.

## Created By
- **Developer**: Antigravity AI
- **Client**: etail_development_src
- **Date**: 2026-06-23
- **Source Bug ID**: N/A

## Symptom
- In the Dashboard → Profit & Loss – Rate Cut Analysis, the system shows the 24KT Gold Rate under the "Current Rate" column for all rate cut transactions, including Silver rate cuts.
- Profit/Loss calculations for Silver rate cuts are based on the Gold rate, leading to inaccurate deviation, profit/loss status, and profit percentage.
- The date range filter is broken/inverted, ignoring the `to_date` parameter.
- The branch filter is not applied to the query.

## Root Cause
1. **Wrong Metal Rate Mapping**: The query in `get_rate_cut_profit_loss()` in `ret_dashboard_api_model.php` hardcoded the retrieval of `goldrate_24ct` from `metal_rates` regardless of `src.id_metal`.
2. **Inverted Date Filter**: The WHERE clause used `DATE(src.date_add) <= '{$from_date}'`, ignoring `to_date`.
3. **Missing Branch Filter**: The query completely lacked filtering for `id_branch` when passed.
4. **Missing Grouping by Metal**: Grouping was done only by date, which collapses Gold and Silver transactions on the same date.

## Detection
```command
grep -rn "goldrate_24ct" admin/application/models/ret_dashboard_api_model.php | grep -i "get_rate_cut_profit_loss"
```

## Files
- `admin/application/models/ret_dashboard_api_model.php`
- `admin/assets/js/ret_dashboard.js`

## Fix

### Model Changes
In `admin/application/models/ret_dashboard_api_model.php`:

#### Before
```php
    function get_rate_cut_profit_loss($from_date, $to_date, $id_branch)
    {
        $branch = is_array($id_branch) ? implode(',', $id_branch) : $id_branch;
        
        $sql = $this->db->query("SELECT 
            DATE_FORMAT(src.date_add, '%d-%m-%Y') as rate_cut_date,
            (SELECT AVG(rate_per_gram) 
             FROM ret_supplier_rate_cut 
             WHERE status = 1 
             AND DATE(date_add) = DATE(src.date_add)) as rate_cut_rate,
            IFNULL((SELECT goldrate_24ct 
             FROM metal_rates 
             WHERE DATE(updatetime) <= DATE(src.date_add) 
             AND goldrate_24ct > 0
             ORDER BY updatetime DESC 
             LIMIT 1), 0) as current_bullion_rate,
            (IFNULL((SELECT goldrate_24ct 
             FROM metal_rates 
             WHERE DATE(updatetime) <= DATE(src.date_add) 
             AND goldrate_24ct > 0
             ORDER BY updatetime DESC 
             LIMIT 1), 0) - (SELECT AVG(rate_per_gram) 
             FROM ret_supplier_rate_cut 
             WHERE status = 1 
             AND DATE(date_add) = DATE(src.date_add))) as rate_deviation,
            CASE 
                WHEN ((SELECT AVG(rate_per_gram) 
             FROM ret_supplier_rate_cut 
             WHERE status = 1 
             AND DATE(date_add) = DATE(src.date_add)) - IFNULL((SELECT goldrate_24ct 
             FROM metal_rates 
             WHERE DATE(updatetime) <= DATE(src.date_add) 
             AND goldrate_24ct > 0
             ORDER BY updatetime DESC 
             LIMIT 1), 0)) < 0 THEN 'profit'
                WHEN ((SELECT AVG(rate_per_gram) 
             FROM ret_supplier_rate_cut 
             WHERE status = 1 
             AND DATE(date_add) = DATE(src.date_add)) - IFNULL((SELECT goldrate_24ct 
             FROM metal_rates 
             WHERE DATE(updatetime) <= DATE(src.date_add) 
             AND goldrate_24ct > 0
             ORDER BY updatetime DESC 
             LIMIT 1), 0)) > 0 THEN 'loss'
                ELSE 'neutral'
            END as profit_loss_status,
            CASE 
                WHEN IFNULL((SELECT goldrate_24ct 
             FROM metal_rates 
             WHERE DATE(updatetime) <= DATE(src.date_add) 
             AND goldrate_24ct > 0
             ORDER BY updatetime DESC 
             LIMIT 1), 0) > 0 THEN 
                    ROUND(((IFNULL((SELECT goldrate_24ct 
             FROM metal_rates 
             WHERE DATE(updatetime) <= DATE(src.date_add) 
             AND goldrate_24ct > 0
             ORDER BY updatetime DESC 
             LIMIT 1), 0) - (SELECT AVG(rate_per_gram) 
             FROM ret_supplier_rate_cut 
             WHERE status = 1 
             AND DATE(date_add) = DATE(src.date_add))) / IFNULL((SELECT goldrate_24ct 
             FROM metal_rates 
             WHERE DATE(updatetime) <= DATE(src.date_add) 
             AND goldrate_24ct > 0
             ORDER BY updatetime DESC 
             LIMIT 1), 0)) * 100, 2)
                ELSE 0
            END as profit_percentage
        FROM ret_supplier_rate_cut src
        LEFT JOIN ret_purchase_order po ON po.po_id = src.po_id
        WHERE src.status = 1
        AND src.ref_no IS NOT NULL
        ".($from_date != '' ? 
            " AND DATE(src.date_add) <= '".date('Y-m-d', strtotime($from_date))."'" : '')."
        group by DATE(src.date_add) ORDER BY DATE(src.date_add) DESC");
		// print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
```

#### After
```php
    function get_rate_cut_profit_loss($from_date, $to_date, $id_branch)
    {
        $branch = is_array($id_branch) ? implode(',', $id_branch) : $id_branch;
        
        $sql = $this->db->query("SELECT 
            rate_cut_date,
            id_metal,
            metal_name,
            rate_cut_rate,
            current_bullion_rate,
            (current_bullion_rate - rate_cut_rate) as rate_deviation,
            CASE 
                WHEN (rate_cut_rate - current_bullion_rate) < 0 THEN 'profit'
                WHEN (rate_cut_rate - current_bullion_rate) > 0 THEN 'loss'
                ELSE 'neutral'
            END as profit_loss_status,
            CASE 
                WHEN current_bullion_rate > 0 THEN 
                    ROUND(((current_bullion_rate - rate_cut_rate) / current_bullion_rate) * 100, 2)
                ELSE 0
            END as profit_percentage
        FROM (
            SELECT 
                DATE(src.date_add) as raw_date,
                DATE_FORMAT(src.date_add, '%d-%m-%Y') as rate_cut_date,
                src.id_metal,
                m.metal as metal_name,
                AVG(src.rate_per_gram) as rate_cut_rate,
                IFNULL((SELECT 
                    CASE 
                        WHEN src.id_metal = 2 THEN mr.silverrate_1gm
                        ELSE mr.goldrate_24ct
                    END
                 FROM metal_rates mr
                 WHERE DATE(mr.updatetime) <= DATE(src.date_add) 
                 AND (CASE WHEN src.id_metal = 2 THEN mr.silverrate_1gm ELSE mr.goldrate_24ct END) > 0
                 ORDER BY mr.updatetime DESC 
                 LIMIT 1), 0) as current_bullion_rate
            FROM ret_supplier_rate_cut src
            LEFT JOIN ret_purchase_order po ON po.po_id = src.po_id
            LEFT JOIN metal m ON m.id_metal = src.id_metal
            WHERE src.status = 1
            AND src.ref_no IS NOT NULL
            ".($branch != '' && $branch != '0' ? " AND src.id_branch IN (" . $branch . ")" : "")."
            ".($from_date != '' && $to_date != '' ? " AND DATE(src.date_add) BETWEEN '".date('Y-m-d', strtotime($from_date))."' AND '".date('Y-m-d', strtotime($to_date))."'" : "")."
            GROUP BY DATE(src.date_add), src.id_metal
        ) t
        ORDER BY raw_date DESC");
		// print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
```

### JS Changes
In `admin/assets/js/ret_dashboard.js`:

#### Before
```javascript
                $('#rate_cut_profit_loss_table').DataTable({
                    data: response.response_data,
                    columns: [
                        { data: 'rate_cut_date' },
```

#### After
```javascript
                $('#rate_cut_profit_loss_table').DataTable({
                    data: response.response_data,
                    columns: [
                        { 
                            data: 'rate_cut_date',
                            render: function(data, type, row) {
                                return data + (row.metal_name ? ' (' + row.metal_name + ')' : '');
                            }
                        },
```

## Verification
1. Run syntax validation check on PHP file:
   `php -l admin/application/models/ret_dashboard_api_model.php`
2. Open Dashboard → Purchase Dashboard → Profit & Loss – Rate Cut Analysis.
3. Observe the first column containing the date (e.g. `15-05-2024 (SILVER)`).
4. Verify that current rate, deviation, profit/loss status, and profit percentage calculations match the Silver rate (e.g. ₹173.00 instead of ₹11540.00).

## Notes
- To prevent UI column misalignment, we avoid changing the table headers/structure and instead append the metal name to the date column.
- Branch and date filters are corrected to improve analytical scoping.
