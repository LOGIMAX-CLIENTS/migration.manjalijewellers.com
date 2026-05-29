# DATA FLOW — chit_dashboard
> Round 1 — 2026-03-16

---

## Flow 1: Dashboard Page Load (index → JS-driven data load)

**User Action**: Admin navigates to `/admin/admin_dashboard` or clicks "Dashboard" in nav.

**Step 1 — PHP page render** (`index()`, L130-204):
```php
$access = $this->admin_settings_model->get_access('admin/dashboard');
$dashboard_access = $this->admin_settings_model->get_dashboard_access();
$data['customer'] = $this->customer_stat();    // Customer reg stats
$data['birthday'] = $this->cus_birthday();     // Today's birthdays
$data['wedding']  = $this->cus_wedding_day();  // Today's anniversaries
$this->load->view('layout/template', $data);   // Renders dashboard.php shell
```

**Step 2 — JS bootstrap** (`dashboard.js` L670): On DOM ready, JS fires:
```javascript
$.ajax({ url: base_url + 'index.php/admin_dashboard/dashboard', type: 'POST',
         data: { id_branch: '' }, ... })
// → response populates summary cards: one_pending, two_pending, renewal, existing_request, feedback_count
```

**Step 3 — Parallel AJAX loads** (JS fires 10+ requests simultaneously):
- L740: `payment_status` → payment bar chart
- L812: `account_status` → account trend chart
- L872: `inter_wallet_status` → wallet credit/debit by branch
- L911: `ajax_collectionData` → scheme-wise collection table
- L950: `regisert_list` → inter-wallet registration counts
- L989: `getsource_wiserrecord` → source-wise records chart
- L1806: `customer_status` → customer breakdown
- L2004: `customer_count` → customer count barchart
- L2027: `account_bydate` → account trend barchart

**DB touched on page load**:
`customer`, `scheme`, `scheme_account`, `payment`, `inter_wallet`, `scheme_reg_request`, `cust_enquiry`, `branch`, `chit_settings`, `metal_rates`

---

## Flow 2: Branch Filter (dashboard refresh)

**User Action**: Admin selects branch from dropdown → triggers dashboard refresh.

**JS Side** (L670):
```javascript
// Branch selected → POST to dashboard()
$.ajax({
    url: base_url + 'index.php/admin_dashboard/dashboard',
    type: 'POST',
    data: { id_branch: selectedBranch }
})
```

**Controller** (`dashboard()`, L106-128):
```php
$this->session->unset_userdata('dashboard_branch');   // Clear old branch
$id_branch = $this->input->post('id_branch');
$this->session->set_userdata('dashboard_branch', $id_branch);  // Set new branch in session

$data['one_pending']        = $this->get_closed('1');       // About-to-close pending (1 installment)
$data['two_pending']        = $this->get_closed('2');       // About-to-close pending (2 installments)
$data['renewal']            = $this->get_renewals('renewals');
$data['existing_request']   = $this->get_existing_request();
$data['feedback_count']     = $this->get_feedback();
echo json_encode($data);
```

> ⚠️ **Risk**: `dashboard_branch` session is wiped and reset on EVERY call to `dashboard()`. If two tabs are open with different branches, they will conflict.

**Branch filter persistence**: Once set in session, ALL subsequent model queries use `$dashboard_branch` session variable for filtering. No page reload needed.

---

## Flow 3: Payment Status Chart (date range filter)

**User Action**: Admin picks date range on payment chart → chart refreshes.

**JS** (L740):
```javascript
$.ajax({
    url: base_url + 'index.php/admin_dashboard/payment_status?nocache=...',
    data: { from_date: from, to_date: to }, type: 'POST'
})
```

**Controller** (`payment_status()`, L1820-1856):
```php
$from_date = $this->input->post('from_date');
$to_date   = $this->input->post('to_date');
$data['payment_status'] = $this->$model->payment_status($from_date, $to_date);
$data['admin_paid']     = $this->$model->payment_join_through('ADMIN', $from_date, $to_date);
$data['web_paid']       = $this->$model->payment_join_through('WEB', $from_date, $to_date);
$data['mob_paid']       = $this->$model->payment_join_through('MOB', $from_date, $to_date);
$data['collection_paid'] = $this->$model->payment_join_through('COLLECTION', $from_date, $to_date);
echo json_encode($data);
```

**DB**: `payment`, `scheme_account`, `branch`

**Response JSON**:
```json
{
  "payment_status": { "paid": 50000.00, "unpaid": ... },
  "admin_paid": { "joined_thro": 45 },
  "web_paid": { "joined_thro": 12 },
  "mob_paid": { "joined_thro": 80 },
  "collection_paid": { "joined_thro": 5 }
}
```

---

## Flow 4: Due Accounts Drilldown

**User Action**: Admin clicks "Today's Due" count card → opens due account list.

**URL**: `/admin/admin_dashboard/due_list/T` (GET with URL param)

**Controller** (`due_list($filterBy)`, L1456-1492):
```php
$data['accounts'] = $this->$model->due_list($filterBy);  // 'T' = Today
$data['main_content'] = 'reports/detailed/unpaid_due';
$this->load->view('layout/template', $data);
```

**Model** (`due_list($filterBy)`, L484-534):
- Complex subquery calculates `next_due` per account:
  ```sql
  CASE
    WHEN is_opening='1' AND date_payment IS NULL THEN DATE_ADD(last_paid_date, INTERVAL 1 MONTH)
    WHEN date_payment IS NULL AND is_opening='0'  THEN date_add (account creation date)
    ELSE DATE_ADD(MAX(date_payment), INTERVAL 1 MONTH)
  END AS next_due
  ```
- Filtered by `date(next_due) = CURDATE()` for 'T'

**DB**: `scheme_account`, `payment`, `customer`, `scheme`, `chit_settings`

---

## Flow 5: Inter-Wallet Status by Branch

**User Action**: Admin opens inter-wallet tab or picks date range.

**JS** (L872):
```javascript
$.ajax({ url: 'admin_dashboard/inter_wallet_status', data: { from_date, to_date } })
```

**Controller** (`inter_wallet_status()`, L2214-2284):
1. Gets all branches via `allBranches()`
2. Gets credit totals via `inter_wallet_credit($from,$to)` → grouped by `id_branch`
3. Gets debit totals via `inter_wallet_redeem($from,$to)` → grouped by `id_branch`
4. PHP-side merge: loops branches, matches credit/debit rows by `id_branch`
5. Returns `[{ branch_name, id_branch, credit, debit, currency_symbol }]`

> ⚠️ **Bug**: Inner `foreach` credit loop — missing `break` after match. Multiple matching credit rows can overwrite `$creditAndDebit[$i]['credit']` successively (last-wins). Same for debit loop.

---

## Flow 6: Customer Edit (admin correction)

**User Action**: Admin searches for customer by mobile → clicks Edit → modifies data → saves.

**JS** (L398): `admin_customer/get_customer_by_mobile` (cross-module call)

**Route**: `/admin/admin_dashboard/customer_edit/{mobile}` (GET — no CSRF protection)

**Controller** (`customer_edit($mobile)`, L2578-2767):
1. `dashboard_model::get_cust($mobile)` → fetch customer
2. `Account_model::get_all_closed_accdetails($id_customer)` → fetch accounts
3. Loads view `dashboard/customer_edit`
4. On form POST → `dashboard_model::updateData($post_data, 'id_customer', $id, 'customer')` → UPDATE customer table

> ⚠️ **CSRF risk**: `customer_edit` URL with mobile number is GET-accessible with no token verification.
> ⚠️ **No validation**: `updateData()` is a generic method that writes whatever POST data arrives to the customer table.

---

## Flow 7: Daily Collection (ajax_collectionData)

**User Action**: Dashboard collection widget initializes or date is changed.

**JS** (L911): POST `ajax_collectionData` with `{ date: selectedDate }`

**Controller** (`ajax_collectionData()`, L2326-2346):
```php
$date = ($this->input->post('date') == "" ? date('Y-m-d') : $this->input->post('date'));
$collection = $this->paydatewise_schemecoll_list($date);
echo json_encode([ 'pay_collection' => $collection ]);
```

**Model** (`paydatewise_schemecoll($date)`, L1885-1926):
- Aggregates `SUM(payment_amount)` by scheme for the exact date
- Returns `{ code, scheme_name, collection, opening_bal }` per scheme

**DB**: `payment`, `scheme_account`, `scheme`

---

## Flow 8: Send Birthday/Wedding Wishes

**User Action**: Admin opens wishes tab → selects customers → clicks Send.

**Route**: POST to `admin_dashboard/send_customer_wishes`

**Controller** (`send_customer_wishes()`, L3053-3149):
1. Reads `from_date`, `to_date` from POST
2. `dashboard_model::cus_wishes_list_bydate($from,$to)` → fetch eligible customers
3. For each customer: loads SMS model → sends birthday/wedding SMS
4. Writes to `sms_log` table (if SMS model persists logs)

> ℹ️ This is a write path — one of few in the dashboard module.
> ⚠️ No transaction wrapping. If SMS for customer 3 fails, customers 1-2 already sent.

---

## Flow 9: Drilldown Page Navigation

**Pattern**: Admin clicks count card → navigates to drilldown page.

**URL patterns**:
- Customer detail: `/admin/admin_dashboard/reg_detail/{type}` (type = T/Y/TW/TM/ALL)
- Account detail: `/admin/admin_dashboard/acc_detail/{type}`
- Payment detail: `/admin/admin_dashboard/pay_detail/{type}`
- Closed accounts: `/admin/admin_dashboard/closed_acc_detail/{type}`
- About to close: `/admin/admin_dashboard/about_to_close/{type}`
- Renewals: `/admin/admin_dashboard/get_renewals_list/{type}`
- Due list: `/admin/admin_dashboard/due_list/{type}`

**Pattern for all**:
```php
$data['accounts'] = $this->$model->{stat_method}(strtoupper($type));
$data['main_content'] = 'reports/detailed/{view}';
$this->load->view('layout/template', $data);
```

All drill-down views render inside `layout/template` — no separate pages.

---

## JS Function → AJAX Endpoint Map (Quick Reference)

| JS Function / Context | Endpoint | Controller Method |
|---|---|---|
| cockpit.php L14 | `ajax_get_collection_list` | `ajax_get_collection_list()` |
| L306 payment joined | `ajax_get_payment_joined` | `ajax_get_payment_joined()` |
| L323 wishes tab | `ajax_customer_wishes` | `ajax_customer_wishes()` |
| L369 account joined | `ajax_get_account_joined` | `ajax_get_account_joined()` |
| L381 account date filter | `ajax_get_account` | `ajax_get_account()` |
| L398 customer search | `admin_customer/get_customer_by_mobile` | CROSS-MODULE |
| L579 rate chart | `rate/ajax/weekstat` | CROSS-MODULE |
| L597 branch dropdown | `branch/branchname_list` | CROSS-MODULE |
| L670 dashboard card refresh | `dashboard` | `dashboard()` |
| L740 payment chart | `payment_status` | `payment_status()` |
| L812 account chart | `account_status` | `account_status()` |
| L872 wallet chart | `inter_wallet_status` | `inter_wallet_status()` |
| L911 collection table | `ajax_collectionData` | `ajax_collectionData()` |
| L950 register list | `regisert_list` | `regisert_list()` |
| L989 source-wise | `getsource_wiserrecord` | `getsource_wiserrecord()` |
| L1402 retail orders | `admin_ret_dashboard/get_customer_order_details` | CROSS-MODULE |
| L1601 employee list | `reports/employee_list` | CROSS-MODULE |
| L1632 customer by date | `customer_detail_bydate` | `customer_detail_bydate()` |
| L1759 scheme accounts | `schWise_accounts_list` | `schWise_accounts_list()` |
| L1806 customer status | `customer_status` | `customer_status()` |
| L1850 collection app | `get_collection_app_details` | `get_collection_app_details()` |
| L1912 payment modewise | `admin_reports/payment_summary_modewise` | CROSS-MODULE |
| L2004 customer barchart | `customer_count` | `customer_count()` |
| L2027 account barchart | `account_bydate` | `account_bydate()` |
