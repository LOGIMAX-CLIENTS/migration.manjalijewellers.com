# Recipe: Ambiguous Column Name in Subquery + Wrong Boolean Operator

## Pattern ID
PAT-QRY-007

## Bug ID
RPT-CLT05

## Symptom
Customer search filter in reports fails to show rows where the searched customer appears in a related/secondary role (e.g., as the "sender" in an advance transfer). The filter either returns no results or only matches the primary customer record.

## Root Cause
1. **Ambiguous column**: A subquery SELECT includes two columns with the same name from different tables (e.g., `rcv.id_customer, snd.id_customer`). MySQL picks the first one when the outer query references `alias.id_customer`.
2. **Contradictory AND**: The outer WHERE uses two separate AND conditions for what should be an OR relationship. Example: `AND cus.id_customer = X AND sub.id_customer = X` — since the JOIN already equates them, both always refer to the same customer. The intent was "find rows where X is either the primary OR secondary customer."

## Detection
```bash
# Find subqueries with duplicate column names
grep -n "SELECT.*\.id_customer.*,.*\.id_customer" admin/application/models/*.php

# Find contradictory AND conditions on same column
grep -n "AND.*id_customer.*" admin/application/models/*.php | grep -c "id_customer" 
```

## Fix Steps

### Step 1: Alias the duplicate column
In the subquery SELECT, alias the secondary column distinctly:
```sql
-- BEFORE:
SELECT rcv_ir.id_customer, snd_cus.id_customer,

-- AFTER:
SELECT rcv_ir.id_customer, snd_cus.id_customer AS snd_customer_id,
```

### Step 2: Combine WHERE with OR
Replace two separate AND conditions with a single OR:
```php
// BEFORE:
" . ($data['customer_id'] != '' ? " AND cus.id_customer = " . $data['customer_id'] . " " : '') . "
" . ($data['customer_id'] != '' ? " AND adv_rcvd.id_customer = " . $data['customer_id'] . " " : '') . "

// AFTER:
" . ($data['customer_id'] != '' ? " AND (cus.id_customer = " . $data['customer_id'] . " OR adv_rcvd.snd_customer_id = " . $data['customer_id'] . ") " : '') . "
```

## Files Changed
- `admin/application/models/ret_reports_model.php` — `customerAdvanceReport()` L11010, L11026

## Verification
1. Search by a customer mobile who has transferred advance to another customer
2. Verify both the searched customer's row AND the recipient's row appear in results
3. Verify that searching with no filter still shows all rows correctly

## Rollback
- Remove the `AS snd_customer_id` alias from L11010
- Restore two separate AND conditions in the WHERE clause
