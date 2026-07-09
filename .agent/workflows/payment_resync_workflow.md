# Payment Resync Workflow — Receipt & Account Number Generation

> **Purpose:** When `receipt_no_set = 3` (Integration Auto Sync), payments that succeed but fail to get a receipt number or account number from the Direct API will show a **Resync** button on the Payment List page. This workflow documents the complete end-to-end implementation for porting to other clients.

---

## Overview

The resync feature handles a specific failure scenario in the **Direct API integration** flow:
1. A payment is created and marked as **Success** (`payment_status = 1`)
2. The system attempts to push customer registration + payment data to the external Direct API
3. The Direct API returns a receipt number and/or account number
4. **If step 2 or 3 fails**, the payment row has `receipt_no = NULL` and/or `scheme_acc_number = NULL`
5. The **Resync** button appears in the payment list, allowing admins to re-trigger the Direct API push

---

## Pre-Flight Checklist (per client)

Before implementing, verify these prerequisites:

- [ ] `chit_settings.receipt_no_set` is set to `3` (Integration Auto Sync)
- [ ] `config/config.php` has `directAPI` set to `'1'`
- [ ] `config/config.php` has `directAPIurl` configured with the external API base URL
- [ ] DB table `customer_reg` exists with columns: `id_customer_reg`, `id_scheme_account`, `clientid`, `is_transferred`, `date_update`
- [ ] DB table `transaction` exists with columns: `id_transaction`, `id_scheme_account`, `ref_no`, `client_id`, `is_transferred`, `record_to`, `receipt_no`, `date_upd`, `transfer_date`
- [ ] DB table `scheme_account` has columns: `ref_no`, `group_code`, `scheme_acc_number`, `date_upd`
- [ ] DB table `payment` has columns: `receipt_no`, `date_upd`

---

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         PAYMENT LIST PAGE                              │
│  Route: payment/list → admin_payment/payment/List                      │
│  Ajax:  payment/ajax_list → admin_payment/payment/Ajax                 │
│                                                                        │
│  For each row where:                                                   │
│    receipt_no_set == '3'                                                │
│    AND (receipt_no == null || receipt_no == '' || receipt_no == '-')     │
│    AND payment_status == 'Success'                                     │
│  → Render [Resync] button                                              │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │ onclick="resync_receipt(id_payment, id_scheme_account)"
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                    JAVASCRIPT (payment.js)                              │
│  function resync_receipt(id_payment, id_scheme_account)                 │
│    POST → /index.php/chit_transaction/resync_receipt                    │
│    data: { id_payment, id_scheme_account }                             │
│    on success → redirect to payment/list                               │
└──────────────────────────────┬──────────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│              CONTROLLER: chit_transaction.php                          │
│              function resync_receipt()                                  │
│                                                                        │
│  1. check_receipt() → get id_customer_reg, scheme_acc_number,          │
│                        id_transaction, receipt_no                       │
│  2. If scheme_acc_number is empty:                                     │
│     → Reset customer_reg.is_transferred = 'N'                          │
│  3. Reset transaction.is_transferred = 'N'                             │
│  4. Call insert_common_data(id_payment) — the core Direct API push     │
│  5. Wrap in DB transaction (commit/rollback)                           │
└──────────────────────────────┬──────────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│              CONTROLLER: chit_transaction.php                          │
│              function insert_common_data($id_payment)                  │
│                                                                        │
│  Phase A — Customer Registration Push (bulkcustomerinsert)             │
│  ┌─────────────────────────────────────────────────────────────┐       │
│  │ 1. getPaymentByID($id_payment) → payment details           │       │
│  │ 2. checkCusRegExists($id_scheme_account) → check if        │       │
│  │    customer_reg record exists                               │       │
│  │ 3. getCustomerByID($id_scheme_account) → customer +        │       │
│  │    scheme_account + address details                         │       │
│  │ 4. getCustomerDet($id_scheme_account) → scheme details     │       │
│  │    (total_installment, maturity_days, etc.)                 │       │
│  │                                                             │       │
│  │ Decision tree:                                              │       │
│  │  • If NOT exists → insert_CustomerReg + set runDirectAPI    │       │
│  │  • If exists but no clientid → update clientid + run        │       │
│  │  • If exists and not transferred → run                     │       │
│  │  • Otherwise → skip Direct API                              │       │
│  │                                                             │       │
│  │ If runDirectAPI && directAPI == '1':                         │       │
│  │   → POST to directAPIurl/common/bulkcustomerinsert          │       │
│  │   → On 200: update scheme_account with:                     │       │
│  │     • group_code (from response.data[0].groupname)          │       │
│  │     • scheme_acc_number (from response.data[0].groupnumber) │       │
│  │     • ref_no (from response.data[0].clientid)               │       │
│  └─────────────────────────────────────────────────────────────┘       │
│                                                                        │
│  Phase B — Payment Transaction Push (bulkpaymentinsert)                │
│  ┌─────────────────────────────────────────────────────────────┐       │
│  │ 1. checkTransExists($ref_no) → check if transaction record │       │
│  │ 2. getPayIDdet($id_payment) → scheme + customer details    │       │
│  │                                                             │       │
│  │ Decision tree:                                              │       │
│  │  • If NOT exists → insert_transaction + set runPayDirect    │       │
│  │  • If exists but no clientid → update clientid + run        │       │
│  │  • If exists, not transferred, has acc_no → run            │       │
│  │  • Otherwise → skip                                        │       │
│  │                                                             │       │
│  │ If runPayDirect && directAPI == '1':                        │       │
│  │   → POST to directAPIurl/common/bulkpaymentinsert           │       │
│  │   → On 200 + paymentreceiptnumber present:                  │       │
│  │     • Update payment.receipt_no                              │       │
│  │     • Update transaction.receipt_no + is_transferred = 'Y'  │       │
│  └─────────────────────────────────────────────────────────────┘       │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## Implementation — File by File

### Files to Modify/Create

| # | File | Purpose |
|---|------|---------|
| 1 | `admin/application/controllers/chit_transaction.php` | Add `resync_receipt()` function + `insert_common_data()` + `sendtoDirectApi()` |
| 2 | `admin/application/models/syncapi_model.php` | Add `check_receipt()` model method |
| 3 | `admin/assets/js/payment.js` | Add `resync_receipt()` JS function + resync button rendering in DataTable columns |

---

### Step 1: Controller — `chit_transaction.php`

#### 1a. Constants & Model Loading

Ensure the controller has these constants and loads the `syncapi_model`:

```php
const SYN_MODEL = "syncapi_model";
```

In the constructor, ensure:
```php
$this->load->model(self::SYN_MODEL);
$this->log_dir = 'log/' . date("Y-m-d");
if (!is_dir($this->log_dir)) {
    mkdir($this->log_dir, 0777, TRUE);
}
```

#### 1b. `resync_receipt()` Function

Add this function to the controller. This is the entry point called by the JS:

```php
function resync_receipt()
{
    $model = self::SYN_MODEL;
    $id_payment = $this->input->post('id_payment');
    $id_scheme_account = $this->input->post('id_scheme_account');

    // Step 1: Get current state of customer_reg + transaction for this payment
    $cus_reg = $this->$model->check_receipt($id_payment, $id_scheme_account);

    // Step 2: If scheme_acc_number is still empty, reset customer_reg transfer flag
    // so the Direct API will re-push the customer registration
    if(empty($cus_reg['scheme_acc_number'])){
        $cus_reg_upd = array(
            'is_transferred' => 'N',
            'date_update' => date("Y-m-d H:i:s")
        );
        $this->$model->update_CustomerReg($cus_reg_upd, $cus_reg['id_customer_reg']);
    }

    // Step 3: Reset transaction transfer flag so the Direct API will re-push the payment
    $pay_data = array(
        'is_transferred' => 'N',
        'date_upd' => date("Y-m-d H:i:s")
    );
    $this->$model->update_transaction($pay_data, $cus_reg['id_transaction']);

    // Step 4: Re-trigger the full Direct API push (customer + payment)
    $this->db->trans_begin();
    $this->insert_common_data($id_payment);

    if ($this->db->trans_status() === TRUE) {
        $this->db->trans_commit();
        $this->session->set_flashdata('chit_alert', array(
            'message' => 'Receipt number generated successfully for the payment id ' . $id_payment,
            'class' => 'success',
            'title' => 'Scheme Payment'
        ));
        echo json_encode(['status' => true]);
    } else {
        $this->db->trans_rollback();
        $this->session->set_flashdata('chit_alert', array(
            'message' => 'Receipt number not generated',
            'class' => 'error',
            'title' => 'Scheme Payment'
        ));
        echo json_encode(['status' => false]);
    }
}
```

#### 1c. `insert_common_data()` Function

This is the core function that pushes data to the Direct API. It handles both customer registration and payment transaction:

```php
function insert_common_data($id_payment)
{
    $model = self::SYN_MODEL;
    $this->load->model($model);

    // Get payment detail
    $pay_data = $this->$model->getPaymentByID($id_payment);

    // Store temp values
    $ref_no = $pay_data[0]['ref_no'];
    $id_scheme_account = $pay_data[0]['id_scheme_account'];

    $isCusRegExists = $this->$model->checkCusRegExists($id_scheme_account, $ref_no);
    $reg = $this->$model->getCustomerByID($id_scheme_account);
    $reg_1 = $this->$model->getCustomerDet($id_scheme_account);

    if ($isCusRegExists['status']) {
        $reg[0]['clientid'] = $isCusRegExists['clientid'];
        $pay_data[0]['client_id'] = $isCusRegExists['clientid'];
    }

    $reg[0]['record_to'] = 1;
    $reg[0]['is_registered_online'] = 2;
    $reg[0]['ref_no'] = $ref_no;
    $grp_name = '';

    // --- Customer Registration Decision Tree ---
    if (!$isCusRegExists['status']) {
        if ($this->config->item('directAPI') == '1') {
            $reg[0]['clientid'] = "ON-" . $id_scheme_account;
            $pay_data[0]['client_id'] = "ON-" . $id_scheme_account;
        }
        $status = $this->$model->insert_CustomerReg($reg[0]);
        $runDirectAPI = true;

    } elseif ($isCusRegExists['status'] && $isCusRegExists['clientid'] == null
              && $isCusRegExists['is_transferred'] == 'N') {
        $reg_data = array('clientid' => "ON-" . $id_scheme_account);
        $this->$model->update_CustomerReg($reg_data, $isCusRegExists['id_customer_reg']);
        $runDirectAPI = true;

    } elseif ($isCusRegExists['status'] && $isCusRegExists['is_transferred'] == 'N') {
        $runDirectAPI = true;
    } else {
        $runDirectAPI = false;
    }

    // --- Phase A: Push Customer to Direct API ---
    if ($runDirectAPI && $this->config->item('directAPI') == '1') {
        // Calculate maturity date
        if (!empty($reg[0]['maturity_date'])) {
            $maturitydate = $reg[0]['maturity_date'];
        } else {
            $total_installment = $reg_1[0]['total_installment'];
            $maturitydate = date('Y-m-d', strtotime("+" . $reg_1[0]['maturity_days'] . " days",
                           strtotime($reg[0]['reg_date'])));
            $maturitydate = date('Y-m-d', strtotime("+" . $total_installment . " months",
                           strtotime($reg[0]['reg_date'])));
            if ($reg_1[0]['maturity_type'] == 2) {
                $maturitydate = date('Y-m-d', strtotime("+" . $reg_1[0]['maturity_days']
                    + $reg_1[0]['closing_maturity_days'] . " days",
                    strtotime($reg_1[0]['start_date'])));
            } else {
                $maturitydate = date('Y-m-d', strtotime("+" . $reg_1[0]['closing_maturity_days']
                    . " days", strtotime($reg[0]['reg_date'])));
            }
        }

        $account = array(
            "id"             => (int) $reg_1[0]['id_customer'],
            "customerid"     => (int) $reg_1[0]['id_customer'],
            "customerName"   => $reg[0]['firstname'],
            "mobileNo"       => (int) $reg[0]['mobile'],
            "branch"         => (int) $reg[0]['id_branch'],
            "insert_update"  => 1,
            "schemeid"       => (int) $reg_1[0]['id_scheme'],
            "schemerefid"    => (int) $id_scheme_account,
            "cardnumber"     => (int) $reg[0]['mobile'],
            "startdate"      => $reg[0]['reg_date'],
            "enddate"        => $maturitydate,
            "schemeamount"   => (int) $pay_data[0]['amount'],
            "clientid"       => $reg[0]['clientid'],
            "groupname"      => $reg[0]['sync_scheme_code'],
            "CreatedBy"      => 1,
            "nomineeMobile"  => (int) $reg_1[0]['nomineeMobile'],
            "doorNo"         => "NA",
            "street"         => "NA",
            "area"           => $reg_1[0]['area'],
            "taluk"          => "NA",
            "city"           => $reg_1[0]['city'],
            "pinCode"        => (int) $reg_1[0]['pincode'],
            "state"          => $reg_1[0]['state'],
        );

        $response = $this->sendtoDirectApi('common/bulkcustomerinsert', $account);
        $grp_name = $response->data[0]->groupname;

        if ($response->status == 200) {
            $cus_reg_data = $this->$model->getCustomerRegbyID($response->data[0]->clientid);
            $acc_data = array(
                'group_code'        => $response->data[0]->groupname,
                'scheme_acc_number' => $response->data[0]->groupnumber,
                'ref_no'            => $response->data[0]->clientid,
                'date_upd'          => date("Y-m-d H:i:s")
            );
            if (!empty($response->data[0]->groupnumber)) {
                $acc_status = $this->$model->update_account(
                    $acc_data,
                    $cus_reg_data[0]['id_scheme_account'],
                    $cus_reg_data[0]['id_customer_reg']
                );
            }
        }

        // Log response
        if (!is_dir($this->log_dir . '/directAPI')) {
            mkdir($this->log_dir . '/directAPI', 0777, true);
        }
        $log_path = $this->log_dir . '/directAPI/response' . date("Y-m-d") . '.txt';
        $ldata = "\n" . date('d-m-Y H:i:s')
            . " \n Acc Postdata : " . json_encode($account, true)
            . " \n Acc Response :" . json_encode($response, true);
        file_put_contents($log_path, $ldata, FILE_APPEND | LOCK_EX);
    }

    // --- Phase B: Push Payment Transaction to Direct API ---
    $isTranExists = $this->$model->checkTransExists($ref_no);
    $payID_data = $this->$model->getPayIDdet($id_payment);

    if (!$isTranExists['status']) {
        $pay_data[0]['record_to'] = 1;
        $pay_data[0]['payment_type'] = 1;
        $status = $this->$model->insert_transaction($pay_data[0]);
        $runPayDirect = true;

    } elseif ($isTranExists['status']
              && ($isTranExists['clientid'] == null || $isTranExists['clientid'] == '')) {
        $trans_data = array('client_id' => $pay_data[0]['client_id']);
        $this->$model->update_transaction($trans_data, $isTranExists['id_transaction']);
        $runPayDirect = true;

    } elseif ($isTranExists['status'] && $isTranExists['is_transferred'] == 'N'
              && ($payID_data[0]['scheme_acc_number'] != null
              || $payID_data[0]['scheme_acc_number'] != '')) {
        $runPayDirect = true;
    } else {
        $runPayDirect = false;
    }

    // For online payments: Send to Direct API and update receipt_no, ref_no
    if ($runPayDirect && $this->config->item('directAPI') == '1') {
        $payment = array(
            "paymentbranch"       => (int) $pay_data[0]['id_branch'],
            "schemeid"            => (int) $payID_data[0]['id_scheme'],
            "schemename"          => $payID_data[0]['scheme_name'],
            "schemeamount"        => (float) $pay_data[0]['amount'],
            "groupno"             => (int) $payID_data[0]['scheme_acc_number'],
            "groupname"           => ($grp_name != "" ? $grp_name : $payID_data[0]['group_code']),
            "customermobile"      => (int) $pay_data[0]['mobile'],
            "cardnumber"          => (int) $pay_data[0]['mobile'],
            "customerid"          => (int) $payID_data[0]['id_customer'],
            "monthyear"           => $pay_data[0]['payment_date'],
            "goldrate"            => (int) $pay_data[0]['rate'],
            "weight"              => (float) $pay_data[0]['weight'],
            "customername"        => $payID_data[0]['customername'],
            "onlinepaymentrefid"  => $pay_data[0]['pay_trans_id'],
            "onlinepayment"       => 1,
            "onlineamount"        => (float) $pay_data[0]['amount'],
            "createdby"           => 1,
            "clientid"            => $payID_data[0]['clientid'],
            "schemerefid"         => $id_payment
        );

        $response = $this->sendtoDirectApi('common/bulkpaymentinsert', $payment);

        if ($response->status == 200) {
            $isClientID = $this->$model->checkClientID(
                $pay_data[0]['id_scheme_account'],
                $response->data[0]->clientid
            );
            if (!empty($response->data[0]->paymentreceiptnumber)) {
                if ($isClientID['status']) {
                    $pay_array = array(
                        'receipt_no' => $response->data[0]->paymentreceiptnumber,
                        'date_upd'   => date("Y-m-d H:i:s")
                    );
                    $pay_status = $this->$model->updatedirPayment($pay_array, $pay_data[0]['ref_no']);
                }
            }
        }

        if (!is_dir($this->log_dir . '/directAPI')) {
            mkdir($this->log_dir . '/directAPI', 0777, true);
        }
        $log_path = $this->log_dir . '/directAPI/response' . date("Y-m-d") . '.txt';
        $ldata = "\n" . date('d-m-Y H:i:s')
            . " \n Postdata : " . json_encode($payment, true)
            . " \n Response :" . json_encode($response, true);
        file_put_contents($log_path, $ldata, FILE_APPEND | LOCK_EX);
    }

    return true;
}
```

#### 1d. `sendtoDirectApi()` Helper Function

```php
function sendtoDirectApi($api, $postData)
{
    $url = $this->config->item('directAPIurl') . $api;
    $payLoad[] = $postData;

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => "",
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => "POST",
        CURLOPT_POSTFIELDS     => json_encode($payLoad),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER     => array(
            "cache-control: no-cache",
            "content-type: application/json"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        return false;
    } else {
        return json_decode($response);
    }
}
```

---

### Step 2: Model — `syncapi_model.php`

#### 2a. `check_receipt()` Method

This query joins `scheme_account`, `customer_reg`, and `transaction` to get the current state of all three records for the given payment:

```php
function check_receipt($id_payment, $id_scheme_account){
    $sql = "SELECT cus.id_customer_reg, sch.scheme_acc_number,
                   t.id_transaction, t.receipt_no
            FROM scheme_account sch
            JOIN customer_reg cus ON cus.id_scheme_account = sch.id_scheme_account
            JOIN transaction t ON t.id_scheme_account = sch.id_scheme_account
            WHERE sch.id_scheme_account = '" . $id_scheme_account . "'
              AND t.ref_no = '" . $id_payment . "'";
    return $this->db->query($sql)->row_array();
}
```

> **IMPORTANT:** This assumes a `customer_reg` record already exists for this `id_scheme_account`. If your client's flow doesn't always create `customer_reg` records, this JOIN will return no rows and the resync will silently fail. Add error handling if needed.

#### 2b. Required Model Methods (verify they exist)

These methods are called by `insert_common_data()` and must exist in `syncapi_model.php`:

| Method | Purpose |
|--------|---------|
| `getPaymentByID($id_payment)` | Gets payment + scheme + customer data for the payment |
| `checkCusRegExists($id_scheme_account, $ref_no)` | Checks if `customer_reg` record exists |
| `getCustomerByID($id_scheme_account)` | Gets customer + scheme_account + address data |
| `getCustomerDet($id_scheme_account)` | Gets scheme details (installments, maturity, etc.) |
| `insert_CustomerReg($data)` | Inserts into `customer_reg` table |
| `update_CustomerReg($data, $id)` | Updates `customer_reg` by `id_customer_reg` |
| `update_transaction($data, $id)` | Updates `transaction` by `id_transaction` |
| `checkTransExists($ref_no)` | Checks if `transaction` record exists for the payment |
| `getPayIDdet($id_payment)` | Gets scheme + customer details for the payment |
| `insert_transaction($data)` | Inserts into `transaction` table |
| `checkClientID($id_scheme_account, $client_id)` | Validates the clientid from Direct API response |
| `updatedirPayment($data, $pay_id)` | Updates `payment.receipt_no` AND `transaction.receipt_no + is_transferred` |
| `getCustomerRegbyID($clientid)` | Gets `customer_reg` by clientid |
| `update_account($data, $id_scheme_account, $id_customer_reg)` | Updates `scheme_account` with group_code, acc_number, ref_no |

---

### Step 3: JavaScript — `payment.js`

#### 3a. Resync Button Rendering in DataTable

In the `set_payment_list()` function, find the column that renders the `receipt_no`. There are **two DataTable configurations** (one for `receipt_no_set == 1 || 2`, another for the else branch). Add the resync button check in **both**.

**Receipt number column — replace the simple `row.receipt_no` render with:**

```javascript
{
    "mDataProp": function(row, type, val, meta){
        if(row.receipt_no_set == '3'
            && (row.receipt_no == null || row.receipt_no == '' || row.receipt_no == '-')
            && row.payment_status === 'Success'){
            return '<button class="btn btn-primary" onclick="resync_receipt('
                + row.id_payment + ',' + row.id_scheme_account + ')">Resync</button>';
        } else {
            return row.receipt_no;
        }
    }
}
```

> **WARNING:** The condition checks `row.payment_status === 'Success'` (string comparison, case-sensitive). Ensure your payment list query returns the text `'Success'` not the numeric status `1`. The query typically JOINs with `payment_status_message` table to get the display text.

#### 3b. `resync_receipt()` Function

Add this at the bottom of `payment.js` (or wherever your utility functions are):

```javascript
function resync_receipt(id_payment, id_scheme_account) {
    $.ajax({
        url: base_url + 'index.php/chit_transaction/resync_receipt',
        dataType: "json",
        method: "POST",
        data: { 'id_payment': id_payment, 'id_scheme_account': id_scheme_account },
        success: function (data) {
            console.log("Resync : ", data);
            window.location.href = base_url + 'index.php/payment/list';
        }
    });
}
```

---

## `receipt_no_set` Values Reference

| Value | Mode | How Receipt # is Generated |
|-------|------|---------------------------|
| `0` | Manual | Admin enters receipt number manually via text input in the list |
| `1` | Automatic | System auto-generates receipt number on payment creation (using `generate_receipt_no()`) |
| `2` | Integration (Sync Tool) | Receipt number comes from external sync tool (Jilaba, etc.) |
| `3` | Integration (Auto Sync) | Receipt number comes from Direct API auto-push; **Resync button** appears if missing |

---

## Database Tables Involved

### `customer_reg`
The sync staging table for customer registration data. Acts as a queue for the Direct API.

| Column | Role in Resync |
|--------|---------------|
| `id_customer_reg` | Primary key, used to identify the record to reset |
| `id_scheme_account` | Links to `scheme_account` |
| `clientid` | Client ID from Direct API response (e.g., `ON-{id_scheme_account}`) |
| `is_transferred` | Transfer flag: `'Y'` = synced, `'N'` = pending. **Resync resets this to `'N'`** |
| `date_update` | Last update timestamp |

### `transaction`
The sync staging table for payment transaction data.

| Column | Role in Resync |
|--------|---------------|
| `id_transaction` | Primary key |
| `ref_no` | Links to `payment.id_payment` |
| `id_scheme_account` | Links to `scheme_account` |
| `client_id` | Client ID for Direct API |
| `receipt_no` | Receipt number from Direct API response |
| `is_transferred` | Transfer flag. **Resync resets this to `'N'`** |
| `record_to` | `1` = online record, `2` = offline record |

### `payment`
The main payment table.

| Column | Role in Resync |
|--------|---------------|
| `id_payment` | Primary key, passed to resync |
| `receipt_no` | **This is what gets populated on successful resync** |
| `id_scheme_account` | Links to scheme account |
| `payment_status` | Must be `1` (Success) for resync button to appear |

### `scheme_account`
The customer's scheme enrollment record.

| Column | Role in Resync |
|--------|---------------|
| `id_scheme_account` | Primary key |
| `scheme_acc_number` | **Gets populated from Direct API response (groupnumber)** |
| `group_code` | Gets populated from Direct API response (groupname) |
| `ref_no` | Gets populated from Direct API response (clientid) |

---

## Sequence of Operations (Step-by-Step)

```
1. User clicks [Resync] button on payment list row
                    │
2. JS calls POST /chit_transaction/resync_receipt
   with { id_payment, id_scheme_account }
                    │
3. Controller: check_receipt() → gets current state
   ├── id_customer_reg (from customer_reg)
   ├── scheme_acc_number (from scheme_account)
   ├── id_transaction (from transaction)
   └── receipt_no (from transaction)
                    │
4. If scheme_acc_number is empty:
   └── Reset customer_reg.is_transferred = 'N'
       (so customer registration will be re-pushed)
                    │
5. Reset transaction.is_transferred = 'N'
   (so payment will be re-pushed)
                    │
6. BEGIN DB TRANSACTION
                    │
7. insert_common_data($id_payment)
   │
   ├── Phase A: Customer Registration
   │   ├── Build customer payload
   │   ├── POST → directAPIurl/common/bulkcustomerinsert
   │   └── On 200: Update scheme_account with
   │       group_code, scheme_acc_number, ref_no
   │
   └── Phase B: Payment Transaction
       ├── Build payment payload
       ├── POST → directAPIurl/common/bulkpaymentinsert
       └── On 200 + receipt number present:
           ├── Update payment.receipt_no
           └── Update transaction (receipt_no, is_transferred='Y')
                    │
8. If DB transaction OK → COMMIT + flash success
   If DB transaction FAIL → ROLLBACK + flash error
                    │
9. Return JSON { status: true/false }
                    │
10. JS redirects to payment/list
    (flash message shown on page load)
```

---

## Config Requirements

In `application/config/config.php` (or equivalent), ensure these items are set:

```php
$config['directAPI']    = '1';               // Enable Direct API integration
$config['directAPIurl'] = 'https://your-external-api.com/api/';  // API base URL
```

---

## Logging

All Direct API requests and responses are logged to:
```
log/{YYYY-MM-DD}/directAPI/response{YYYY-MM-DD}.txt
```

Each log entry contains:
- Timestamp
- POST data (JSON)
- API response (JSON)

---

## Troubleshooting

| Symptom | Cause | Fix |
|---------|-------|-----|
| Resync button not appearing | `receipt_no_set != 3` in `chit_settings` | Update `chit_settings.receipt_no_set = 3` |
| Resync button not appearing | Payment status is not `'Success'` | Check `payment.payment_status` and `payment_status_message` table |
| Resync clicks but nothing happens | `chit_transaction` controller not accessible | Check routes or add route for `chit_transaction/resync_receipt` |
| Resync returns `status: false` | Direct API returned error | Check `log/{date}/directAPI/` for response details |
| Receipt number updated but account number still empty | `bulkcustomerinsert` returned empty `groupnumber` | Check Direct API logs and ensure customer data is valid |
| `check_receipt()` returns empty | No `customer_reg` record exists | Ensure the payment flow creates `customer_reg` records |
| `insert_common_data()` fails silently | `directAPI` config is not `'1'` | Set `$config['directAPI'] = '1'` |

---

## Testing Checklist

- [ ] Set `chit_settings.receipt_no_set = 3`
- [ ] Create a payment with `payment_status = 1` and `receipt_no = NULL`
- [ ] Ensure `transaction` record exists with matching `ref_no = id_payment`
- [ ] Ensure `customer_reg` record exists with matching `id_scheme_account`
- [ ] Verify Resync button appears in payment list
- [ ] Click Resync and verify:
  - [ ] Direct API logs are created in `log/{date}/directAPI/`
  - [ ] `scheme_account.scheme_acc_number` is populated
  - [ ] `scheme_account.group_code` is populated
  - [ ] `payment.receipt_no` is populated
  - [ ] `transaction.receipt_no` is populated
  - [ ] `transaction.is_transferred = 'Y'`
  - [ ] `customer_reg.is_transferred = 'Y'` (after the sync tool picks it up)
  - [ ] Flash message shows "Receipt number generated successfully"
