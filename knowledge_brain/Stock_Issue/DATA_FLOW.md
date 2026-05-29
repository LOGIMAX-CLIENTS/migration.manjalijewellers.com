# DATA FLOW — Stock Issue
> Verified Round 9 | 2026-03-19 | All 8 flows confirmed, bugs inline (R-07, R-11, R-22)

---

## Flow 1: CREATE ISSUE — Tagged Stock

### Overview
User scans tags on form → submits → creates issue header + detail rows → updates tag statuses

### Step-by-Step

**1. JS Trigger** (`ret_stock_issue.js` ~L825)
- `#stock_issue_submit` click fires
- JS validates: branch, issue_type, stock_type, issued_to, item count
- If `otp_required=1` AND `is_otp_verfied!=1` → open OTP modal first

**2. JS Collects Data** (~L1241)
- `$('#stock_issue_form').serialize()` for tagged stock (stock_type=1)
- Key POST fields: `form_secret`, `order[]`, `tag_id[]`, `rate_per_gram`, `stock_type`

**3. AJAX POST** (L1301)
```
POST: admin_ret_stock_issue/stock_issue/save
Data: {form, tag_id[], rate_per_gram, stock_type=1}
```

**4. Controller: `stock_issue('save')`** (L165)
- Reads `$form_secret`, `$addData`, `$tag_details`, `$rate_per_gram`, `$stock_type`
- Checks `$_SESSION['FORM_SECRET']` match → `$allow_submit = true/false`
- Gets day-close date: `admin_settings_model->getBranchDayClosingData()`
- `$issue_date` = day-close date or today current time

**5. Branch: `if($stock_type==1 && issue_receipt_type==1)`** (L217)
- `generateIssueNo()` → `{fin_year}-{00001}`
- Build `$insData` for `ret_stock_issue`
- `db->trans_begin()`
- `insertData($insData, 'ret_stock_issue')` → `$insId`

**6. For each tag_id** (L279)
- `getTagDetails($tag_id)` → get `gross_wt`, `net_wt`, `piece`, `id_section`
- `stock_issue_type_detail($issue_type)` → get `is_remove_from_stock`
- Build `$issueDetail`, `insertData($issueDetail, 'ret_stock_issue_detail')`
- `updateData(['tag_status'=>7], 'tag_id', $tag_id, 'ret_taging')`
- IF `is_remove_from_stock==1`:
  - INSERT `ret_taging_status_log` (status=7)
  - IF tag has `id_section`: INSERT `ret_section_tag_status_log`

**7. Commit / Rollback** (L381)
- `trans_status()===TRUE` → `trans_commit()` + `log_model->log_detail()`
- ELSE → ⚠️ DEBUG: `echo last_query(); exit;` → THEN rollback (dead code after exit!)

**8. Response** (L405)
```json
{"status": true, "message": "Stock Issued successfully..", "id_stock_issue": 123}
```

**9. JS Success** (L1337)
- If `issue_receipt_type==1`: open PDF in new tab: `stock_issue/issue_print/{id}`
- Redirect to `stock_issue/list`

### Tables Written (in order)
1. `ret_stock_issue` (INSERT — header)
2. `ret_stock_issue_detail` (INSERT — per tag)
3. `ret_taging` (UPDATE tag_status=7)
4. `ret_taging_status_log` (INSERT — conditional)
5. `ret_section_tag_status_log` (INSERT — conditional)

---

## Flow 2: CREATE RECEIPT — Tagged Stock

### Overview
User selects issued issue number → scans tags received back → system reverses tag status

### Step-by-Step

**1. Form Setup**
- Select issue radio → Receipt
- `get_StockIssueItems()` AJAX loads issued issue numbers with unreceived tags
- `get_receipt_tag_scan_details()` AJAX scans incoming tag (status=7 tags only)

**2. AJAX POST** (same endpoint)
```
POST: admin_ret_stock_issue/stock_issue/save
Data: {form, tag_id[], stock_type=1, issue_receipt_type=2, issue_id}
```

**3. Controller Branch: `issue_receipt_type==2`** (L425)
- `get_IssueItems($issue_id)` → get issue header
- `$received_time = time()` (used as batch key)
- For each `tag_id`:
  - `trans_begin()`
  - `updateData(['tag_status'=>0], ...)` on `ret_taging` (back to stock)
  - `updateData(['status'=>3, 'received_time'=>$received_time, 'received_date'=>...], 'tag_id', $tag_id, 'ret_stock_issue_detail')`
  - IF `is_remove_from_stock==1`:
    - INSERT `ret_taging_status_log` (status=0)
    - IF section: INSERT `ret_section_tag_status_log`

**4. Commit** (L513)
- Commit + log

⚠️ **Bug**: `$issue_date` used in receipt log (L463) but `$issue_date` is defined only in the ISSUE branch earlier (L213). In RECEIPT branch this is undefined!

---

## Flow 3: CREATE ISSUE — Non-Tagged Stock

### Overview
Admin selects items from non-tag inventory → issued out → stock deducted via arithmetic UPDATE

### Step-by-Step

**1. JS**
- `#search_non_tag` button calls `get_NonTagScanDetails()` AJAX
- User checks items in `nontagissue_item_detail` table
- On submit: JS serializes `non_tagged[]` array manually (not form serialize)

**2. AJAX POST** (L1301)
```json
POST: stock_issue/save
Data: {nt_data: [...], branch_select, issue_type, stock_type=2, issued_to, ...}
```

**3. Controller Branch: `$stock_type==2 && $type_issue==1`** (L587)
- `generateIssueNo()` → issue number
- INSERT `ret_stock_issue` with `stock_type=2`
- For each `$nt`:
  - INSERT `ret_stock_issue_detail` (with `id_non_tag_item`, piece, wt)
  - IF `is_remove_from_stock==1`:
    - INSERT `ret_nontag_item_log` (status=4)
    - IF `id_section`: INSERT `ret_section_nontag_item_log`
    - IF `id_nontag_item != ''`: `updateNTData($data, '-')` (deduct)

`updateNTData(data, '-')`:
```sql
UPDATE ret_nontag_item SET no_of_piece=(no_of_piece - {qty}), gross_wt=(gross_wt - {grs}), net_wt=(net_wt - {net}) WHERE id_nontag_item={id}
```

---

## Flow 4: CREATE RECEIPT — Non-Tagged Stock

### Overview
Issued non-tag items returned → stock re-added via arithmetic UPDATE

**Controller Branch: `$stock_type==2 && $type_issue==2`** (L850)
- For each `$nt`:
  - IF `is_remove_from_stock==1`:
    - INSERT `ret_nontag_item_log` (status=0)
    - IF section: INSERT `ret_section_nontag_item_log`
    - `updateNTData($data, '+')` (add back)
  - UPDATE `ret_stock_issue_detail.status=3` by `id_stock_issue_detail`

---

## Flow 5: PRINT — Issue Acknowledgement

**Trigger**: JS opens new tab: `stock_issue/issue_print/{id}` or `issue_print_detail/{id}`

```
Controller → get_IssueItems($id) → get_issue_item_details($id,...) → admin_settings_model->getCompanyDetails()
→ load view: ret_stock_issue/issue_ack.php (or issue_ack_det.php)
→ dompdf->load_html() → render() → stream("Receipt.pdf")
```

**Summary vs Detail Print**:
- `issue_ack.php` — groups items by category (uses `get_issue_item_details` → GROUP BY cat_id)
- `issue_ack_det.php` — individual tag rows (uses `get_issue_item_tag` → no GROUP BY)

---

## Flow 6: OTP — Stock Issue Authorization

```
1. Form validates → OTP required → $('#stock_otp_modal').show()
2. JS: stock_at_send_otp() → POST stock_issue_sendotp
   Controller: generates OTP, stores in session + otp table, sends SMS
   ⚠️ BUG: OTP returned in JSON response: {'OTP': $OTP} — exposed to frontend!
3. User enters OTP → $('#verify_stock_otp') click → stock_order_otp()
   POST stock_issue_verify_otp
   Controller: compare post_otp vs session.stock_issue_otp
   If match & not expired: update otp table is_verified=1
   Return {status: true, verified_otp: $post_otp}
4. JS sets is_otp_verfied=1, triggers #stock_issue_submit
```

SMS dispatch: `stock_trans_send_sms()` → either `sms_model->sendSMS_MSG91()` or `sendSMS_Nettyfish()` based on config

---

## JS Functions Map

| JS Function | Trigger | Purpose | AJAX Target |
|---|---|---|---|
| `set_stock_issue_list()` | page load (list) | Load DataTable | `stock_issue` (POST default) |
| `get_stock_issue_type()` | page load (add) | Fill issue type dropdown | `get_stock_issue_type` |
| `get_all_employee()` | page load (add) | Fill employee dropdowns | `admin_ret_estimation/get_employee` |
| `get_all_karigar()` | page load (add) | Fill karigar dropdown | `admin_ret_catalog/karigar/active_list` |
| `get_ActiveMetals()` | page load (add) | Fill metals | External |
| `get_metal_rates_by_branch()` | page load (add) | Get today's rates | External |
| `get_ActiveSections()` | page load (add) | Fill section filter | External |
| `get_StockIssueItems()` | page load (add) | Load issues for receipt | `get_StockIssuedItems` |
| `get_tag_scan_details()` | barcode scan (issue) | Fetch tag data | `get_tag_scan_details` |
| `get_receipt_tag_scan_details()` | barcode scan (receipt) | Fetch tag for receipt | `get_receipt_tag_scan_details` |
| `get_NonTagScanDetails()` | Search Non Tag click | Load non-tag inventory | `get_nontag_scan_details` |
| `#stock_issue_submit click` | Submit button | Validate + POST save | `stock_issue/save` |
| `.submit_stock_issue click` | OTP-aware submit | Check OTP → trigger submit | — |
| `stock_at_send_otp()` | OTP modal trigger | Send OTP SMS | `stock_issue_sendotp` |
| `stock_order_otp()` | verify button click | Verify OTP | `stock_issue_verify_otp` |

---

## Flow 7: LIST VIEW — DataTable Load (Round 2 addition)

```
page load → set_stock_issue_list()
→ Reads status from #issue_status dropdown (0=All, 1=Issued, 2=Rejected, 3=Received)
→ POST admin_ret_stock_issue/stock_issue (default case)
  data: {status: val}
→ Controller: ajax_getStockIssueList($_POST)
  → Model: queries ret_stock_issue + detail join + branch + employee + customerorder
  → Per row: calls get_stock_issue_det($id) [N+1!] for receipt summary
→ Response JSON: {list: [...], access: {...}}
→ JS populates #issue_list DataTable
```

**List View Columns** (list.php L81-90):
`#`, `Issue No`, `Branch`, `Tag Code`, `Category Name`, `Issue Date`, `Issue Type`, `Issued By`, `(blank)`, `Action`

**Filters on list**: Status dropdown only (no date range, no branch filter)

---

## Flow 8: PRINT — PDF Template Analysis (Round 2 addition)

### issue_ack.php (Summary Challan — groups by category)
**Title**: DELIVERY CHALLAN
**PHP helpers defined locally**:
- `moneyFormatIndia($num)` — Indian number format (e.g., 1,00,000.00)
- `formatnumber($num)` — `floatval(number_format($num, 2, '.', ''))`

**Template sections**: Company header → Issued To (Bill-to) → Shipped To → Items table (by category) → Totals → CGST/SGST rows (only if tax > 0)

**Items loop** (per `$item_details` grouped by cat_id):
- Shows: HSN Code, Category, Net Weight (Grams), Rate/Gram, Amount
- Sub-loop for stone details: stone_name, weight, rate, amount
- Sub-loop for other metal details: catname, weight, amount
- Totals: taxable_amount, CGST (tax%/2), SGST (tax%/2)

**Tax formula in print**:
```php
$item_cost = $val['issue_weight'] * $val['rate_per_gram'];
$total_taxable_amount += ($item_cost + $total_stone_amount + $other_metal_amount);
$tax_amount = ($total_taxable_amount * 3) / 100;  // HARDCODED 3%!
$total_cgst = $tax_amount / 2;
$total_sgst = $tax_amount / 2;
```
⚠️ **Bug R-11**: Tax rate is **HARDCODED at 3%** in the PDF template, ignoring `ret_taxmaster.tax_percentage` from the model query!

**Billed-to logic**: switches on `$issue['issued_to']`:
- 1=Customer: shows customer_name, cus_mobile, address, GST
- 2=Employee: shows emp_name, emp_mobile
- 3=Karigar: shows karigar_name, kar_mobile, supplier address, GST

### issue_ack_det.php (Detail Challan — per-tag rows)
- Same structure as issue_ack.php
- Uses `$item_details` from `get_issue_item_tag()` — individual tag rows (no GROUP BY)
- Shows `tag_code` column per row
- Same hardcoded 3% tax bug applies
