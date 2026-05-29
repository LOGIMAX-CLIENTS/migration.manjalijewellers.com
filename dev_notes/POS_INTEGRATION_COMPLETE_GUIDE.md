# POS Integration — Complete Knowledge Document

> **Created**: 2026-03-10  
> **Project**: ARIA ERP (Retail Jewellery Billing)  
> **Framework**: CodeIgniter (PHP)  
> **Purpose**: Full reference for implementing multi-provider POS payment integration  

---

# TABLE OF CONTENTS

1. [Current Pine Labs Implementation (Existing Code)](#1-current-pine-labs-implementation)
2. [Files Inventory — What Exists & What Needs Change](#2-files-inventory)
3. [Pine Labs End-to-End Flow Explained](#3-pine-labs-end-to-end-flow)
4. [PhonePe API Documentation (DQR + IEDC)](#4-phonepe-api-documentation)
5. [Multi-Provider Comparison](#5-multi-provider-comparison)
6. [Architecture Plan — Unified POS Layer](#6-architecture-plan)
7. [UAT Testing Guide](#7-uat-testing-guide)
8. [Database Schema](#8-database-schema)
9. [Implementation Roadmap](#9-implementation-roadmap)
10. [Integration Providers in ARIA ERP](#10-all-integrations-in-aria-erp)

---

# 1. Current Pine Labs Implementation

## Business Context
- **Old clients** → Pine Labs (Plutus) — Card EDC terminal (2 years old code)
- **New clients** → PhonePe DQR (QR-based UPI payment) — planned
- **Future clients** → Could be Razorpay, Paytm, or any other provider

## Architecture (Current)
```
Browser JS (AJAX) → CI Controller (admin_ret_billing.php) → cURL → Plutus Cloud API
                                    ↓
                              MySQL Database
                          (ret_pos_requests table)
```

## Key Config (config.php)
```php
// POS integration URLs (Line 74-78)
$config['pos_api_request_url']        = "https://plutuscloudserviceuat.in:8201/API/CloudBasedIntegration/V1/UploadBilledTransaction";
$config['pos_api_trans_status_url']   = "https://plutuscloudserviceuat.in:8201/API/CloudBasedIntegration/V1/GetCloudBasedTxnStatus";
$config['pos_api_request_cancel_url'] = "https://plutuscloudserviceuat.in:8201/API/CloudBasedIntegration/V1/CancelTransaction";
```

## Controller Functions (admin_ret_billing.php, Lines 8719-8877)

### 1.1 [UploadBilledTransaction()](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/controllers/admin_ret_billing.php#8719-8775) — Initiate Payment

**What it does**: Sends a payment request to the POS terminal so customer can tap/swipe card.

**Flow**:
1. Receives `$_POST['payTransData']` from JS with: `deviceId`, `cusid`, [amount](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/models/ret_billing_model.php#4207-4216), `seqno`, `paytype`
2. Fetches POS device details from DB: `poscode`, `merchantid`, `securitytoken`, `imei`
3. Fetches customer's last 5 digits of mobile from [customer](file:///c:/xampp/htdocs/ariaemi_staging/admin/assets/js/ret_billing.js#22762-22839) table
4. Generates unique transaction number: `{cusid}_{random6digit}_{last5mobile}`
5. Builds request payload and sends to Plutus API via cURL
6. On success (ResponseCode == 0): saves to `ret_pos_requests` table
7. Returns JSON: `{responsecode, resmessage, refid}`

**Full Controller Code**:
```php
function UploadBilledTransaction(){
    $model      = "ret_billing_model";
    $addData    = $_POST['payTransData'];
    $posDetails = $this->$model->getPOSDeviceDetails($addData['deviceId']);
    $cusmob     = $this->$model->getcusLastMobile($addData['cusid']);
    
    $transno    = $addData['cusid']."_".random_int(100000, 999999)."_".$cusmob;
    
    $paydevicedata = array(
        "pos_trans_no"          => $transno,  
        "pos_store_pos_code"    => $posDetails['poscode'],
        "pos_req_amount"        => $addData['amount'] * 100,  
        "pos_usr_id"            => $this->session->userdata('uid'),  
        "pos_mer_id"            => $posDetails['merchantid'], 
        "pos_imie"              => $posDetails['imei'],
        "pos_req_createdby"     => $this->session->userdata('uid'), 
        "pos_req_bill_cusid"    => $addData['cusid'],
    );
    
    $requestData = array(
        "TransactionNumber"    => $transno,   
        "SequenceNumber"       => $addData['seqno'],                            
        "AllowedPaymentMode"   => $addData['paytype'],                              
        "MerchantStorePosCode" => $posDetails['poscode'],
        "Amount"               => $addData['amount'] * 100,                                     
        "UserID"               => $this->session->userdata('uid'),                 
        "MerchantID"           => $posDetails['merchantid'],                                
        "SecurityToken"        => $posDetails['securitytoken'],
        "IMEI"                 => $posDetails['imei'],
        "AutoCancelDurationInMinutes" => 2  // HARDCODED — not dynamic
    );
    
    $posrequest = $this->postcurlPOSRequests($requestData, $this->config->item('pos_api_request_url'));
    
    if($posrequest['ResponseCode'] == 0){
        $paydevicedata['pos_res_ref_id'] = $posrequest['PlutusTransactionReferenceID'];
        $insId = $this->$model->insertData($paydevicedata, 'ret_pos_requests');
        echo json_encode(array("responsecode" => $posrequest['ResponseCode'], "resmessage" => $posrequest['ResponseMessage'], "refid" => $posrequest['PlutusTransactionReferenceID']));
    }else{
        echo json_encode(array("responsecode" => $posrequest['ResponseCode'], "resmessage" => $posrequest['ResponseMessage'], "refid" => $posrequest['PlutusTransactionReferenceID']));
    }
}
```

**API Request Payload**:
```json
{
  "TransactionNumber": "224_481923_35323",
  "SequenceNumber": 1,
  "AllowedPaymentMode": 1,
  "MerchantStorePosCode": "1221258018",
  "Amount": 10000,
  "UserID": 1,
  "MerchantID": 29610,
  "SecurityToken": "a4c9741b-2889-47b8-be2f-ba42081a246e",
  "IMEI": "ARIA1001018",
  "AutoCancelDurationInMinutes": 2
}
```

### 1.2 [getTransactionStatus()](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/controllers/admin_ret_billing.php#8776-8799) — Poll Payment Status

**What it does**: Checks if customer has completed the card tap/swipe at the terminal.

```php
function getTransactionStatus(){
    $model      = "ret_billing_model";
    $addData    = $_POST['payTransData'];
    $posDetails = $this->$model->getPOSDeviceDetails($addData['deviceId']);
    
    $requestData = array(
        "MerchantID"                    => $posDetails['merchantid'],                                
        "SecurityToken"                 => $posDetails['securitytoken'],
        "IMEI"                          => $posDetails['imei'],
        "MerchantStorePosCode"          => $posDetails['poscode'],
        "PlutusTransactionReferenceID"  => $addData['refcode'],
    );
                    
    $posrequest = $this->postcurlPOSRequests($requestData, $this->config->item('pos_api_trans_status_url'));
    
    if($posrequest['ResponseCode'] == 0){
        $this->$model->updateData(array('pos_res_trans_data'=> $posrequest['TransactionData']), 'pos_res_ref_id', $addData['refcode'], 'ret_pos_requests');
    }
    
    echo json_encode(array("responsecode" => $posrequest['ResponseCode'], "resmessage" => $posrequest['ResponseMessage'], "transdata" => $posrequest['TransactionData']));
}
```

### 1.3 [cancelTransactionRequest()](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/controllers/admin_ret_billing.php#8800-8825) — Cancel Pending Payment

```php
function cancelTransactionRequest(){
    $model      = "ret_billing_model";
    $addData    = $_POST['payTransData'];
    $posDetails = $this->$model->getPOSDeviceDetails($addData['deviceId']);
    
    $requestData = array(
        "MerchantID"                    => $posDetails['merchantid'],                                
        "SecurityToken"                 => $posDetails['securitytoken'],
        "IMEI"                          => $posDetails['imei'],
        "MerchantStorePosCode"          => $posDetails['poscode'],
        "PlutusTransactionReferenceID"  => $addData['refcode'],
        "Amount"                        => $addData['amount'],
    );
                    
    $posrequest = $this->postcurlPOSRequests($requestData, $this->config->item('pos_api_request_cancel_url'));
    
    if($posrequest['ResponseCode'] == 0){
        $this->$model->updateData(array('pos_req_status'=> 2), 'pos_res_ref_id', $addData['refcode'], 'ret_pos_requests');
    }
    
    echo json_encode(array("responsecode" => $posrequest['ResponseCode'], "resmessage" => $posrequest['ResponseMessage'], "transdata" => $posrequest['TransactionData']));
}
```

### 1.4 [postcurlPOSRequests()](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/controllers/admin_ret_billing.php#8826-8878) — Shared cURL Utility

```php
function postcurlPOSRequests($postData, $requrl){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $requrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    return json_decode($response, true); 
}
```

---

# 2. Files Inventory

## Existing Files (Pine Labs — Currently Working)

| # | File | Location | Purpose | Lines |
|---|---|---|---|---|
| 1 | **config.php** | [admin/application/config/config.php](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/config/config.php) | POS API URLs (lines 74-78) | L74-78 |
| 2 | **admin_ret_billing.php** | [admin/application/controllers/admin_ret_billing.php](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/controllers/admin_ret_billing.php) | 4 POS controller functions | L8719-8877 |
| 3 | **ret_billing_model.php** | [admin/application/models/ret_billing_model.php](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/models/ret_billing_model.php) | POS model functions | L6665-6688 |
| 4 | **form.php** | [admin/application/views/billing/form.php](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/views/billing/form.php) | Billing form view (POS hidden field) | L228, L4508 |

## Model Functions (ret_billing_model.php)

### [getPOSDeviceList()](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/models/ret_billing_model.php#6665-6672) — Line 6665
```php
function getPOSDeviceList(){
    $device_type = $this->get_ret_settings('pos_pay_type');
    $device_query = $this->db->query("SELECT id_device, dispname, poscode, merchantid, securitytoken, imei, is_default FROM ret_pos_device_list WHERE devicetype = $device_type");
    return $device_query->result_array();
}
```

### [getPOSDeviceDetails()](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/models/ret_billing_model.php#6673-6678) — Line 6673
```php
function getPOSDeviceDetails($deviceId){
    $device_query = $this->db->query("SELECT id_device, dispname, poscode, merchantid, securitytoken, imei, is_default FROM ret_pos_device_list WHERE id_device = $deviceId");
    return $device_query->row_array();
}
```

### [getcusLastMobile()](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/models/ret_billing_model.php#6679-6684) — Line 6679
```php
function getcusLastMobile($cusId){
    $mob_query = $this->db->query("SELECT RIGHT(mobile,5) as mobile FROM customer where id_customer = $cusId");
    return $mob_query->row()->mobile;
}
```

### [getPOSMachineRequired()](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/models/ret_billing_model.php#6685-6689) — Line 6685
```php
function getPOSMachineRequired(){
    return $this->get_ret_settings('pay_by_pos');
}
```

## View File (billing/form.php)

### Line 228 — Hidden field for POS enable/disable:
```html
<input type="hidden" id="enable_pos_payment" value="<?php echo $pos_required;?>">
```

### Line 4508 — POS column in payment table header:
```php
<?php echo $pos_required == 1 ? "<th>POS</th>" : ""; ?>
```

## JS File — [admin/assets/js/ret_billing.js](file:///c:/xampp/htdocs/ariaemi_staging/admin/assets/js/ret_billing.js) (25,279 lines)

The POS JavaScript code is in [admin/assets/js/ret_billing.js](file:///c:/xampp/htdocs/ariaemi_staging/admin/assets/js/ret_billing.js). All 6 POS functions start at line 24992.

### Settings Chain: Where `pos_required` Comes From
```
ret_settings table (name='pay_by_pos', value=1 or 0)
        ↓
Model: getPOSMachineRequired() → get_ret_settings('pay_by_pos')  [Line 6685]
        ↓
Controller: $data['pos_required'] = $this->$model->getPOSMachineRequired()  [Line 267]
        ↓
View: <input type="hidden" id="enable_pos_payment" value="<?php echo $pos_required;?>">  [Line 228]
        ↓
JS reads: $('#enable_pos_payment').val()
```

Also: `pos_pay_type` setting in [ret_settings](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/models/ret_billing_model.php#381-390) controls which device type to fetch from `ret_pos_device_list`.

### All 6 JS POS Functions:

| # | JS Function | Line | Calls Controller | Purpose |
|---|---|---|---|---|
| 1 | [getposmachinelists()](file:///c:/xampp/htdocs/ariaemi_staging/admin/assets/js/ret_billing.js#24992-25016) | 24992 | [getposdevicelists](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/controllers/admin_ret_billing.php#8713-8718) | Page load → fetch POS devices → store in `bill_pos_list` |
| 2 | [add_billed_transaction(curRow)](file:///c:/xampp/htdocs/ariaemi_staging/admin/assets/js/ret_billing.js#25017-25054) | 25017 | [UploadBilledTransaction](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/controllers/admin_ret_billing.php#8719-8775) | Card "Pay" button → send to terminal (paytype=1) |
| 3 | [add_upi_billed_transaction(curRow)](file:///c:/xampp/htdocs/ariaemi_staging/admin/assets/js/ret_billing.js#25055-25094) | 25055 | [UploadBilledTransaction](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/controllers/admin_ret_billing.php#8719-8775) | UPI "Pay" button → send to terminal (paytype=10) |
| 4 | [get_upi_billed_transaction(curRow)](file:///c:/xampp/htdocs/ariaemi_staging/admin/assets/js/ret_billing.js#25095-25166) | 25095 | [getTransactionStatus](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/controllers/admin_ret_billing.php#8776-8799) | UPI "Status" button → poll status |
| 5 | [get_billed_transaction(curRow)](file:///c:/xampp/htdocs/ariaemi_staging/admin/assets/js/ret_billing.js#25166-25233) | 25166 | [getTransactionStatus](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/controllers/admin_ret_billing.php#8776-8799) | Card "Status" button → poll status |
| 6 | [cancel_billed_transaction(curRow)](file:///c:/xampp/htdocs/ariaemi_staging/admin/assets/js/ret_billing.js#25234-25280) | 25234 | [cancelTransactionRequest](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/controllers/admin_ret_billing.php#8800-8825) | "Cancel" button → cancel POS |

### Function 1: [getposmachinelists()](file:///c:/xampp/htdocs/ariaemi_staging/admin/assets/js/ret_billing.js#24992-25016) (Line 24992)
```javascript
function getposmachinelists(){
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/admin_ret_billing/getposdevicelists?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),
        dataType: "JSON",
        success: function(data){
            bill_pos_list = data;  // Global variable — stores POS device list
        },
        error: function(error) {}
    });
}
```

### Function 2: [add_billed_transaction(curRow)](file:///c:/xampp/htdocs/ariaemi_staging/admin/assets/js/ret_billing.js#25017-25054) (Line 25017) — Card Payment
```javascript
function add_billed_transaction(curRow){
    var pospostdata = [];
    $.each(bill_pos_list, function (pkey, item){
        if(item.id_device == curRow.find(".id_pos_device_lst").val()){
            pospostdata = {
                "deviceId": item.id_device,
                "amount": curRow.find(".card_amt").val(),
                "cusid": $("#bill_cus_id").val(),
                "paytype": 1,    // Card
                "seqno": $('#card_details tbody').length
            };
        }
    });
    $("div.overlay").css("display", "block");
    $.ajax({
        url: base_url + "index.php/admin_ret_billing/UploadBilledTransaction?nocache=...",
        data: {'payTransData': pospostdata},
        type: "POST",
        dataType: "JSON",
        async: false,
        success: function(data){
            curRow.find(".ref_no").val(data.refid);       // Store reference ID
            curRow.find(".ref_no").attr('readonly', 'true');
            $("div.overlay").css("display", "none");
        },
        error: function(error){ $("div.overlay").css("display", "none"); }
    });
}
```

### Function 3: [add_upi_billed_transaction(curRow)](file:///c:/xampp/htdocs/ariaemi_staging/admin/assets/js/ret_billing.js#25055-25094) (Line 25055) — UPI Payment
```javascript
function add_upi_billed_transaction(curRow){
    var pospostdata = [];
    $.each(bill_pos_list, function (pkey, item){
        if(item.id_device == curRow.find(".id_pos_device_lst").val()){
            pospostdata = {
                "deviceId": item.id_device,
                "amount": curRow.find(".amount").val(),
                "cusid": $("#bill_cus_id").val(),
                "paytype": 10,    // UPI
                "seqno": $('#net_bank_details tbody').length
            };
        }
    });
    $("div.overlay").css("display", "block");
    $.ajax({
        url: base_url + "index.php/admin_ret_billing/UploadBilledTransaction?nocache=...",
        data: {'payTransData': pospostdata},
        type: "POST",
        dataType: "JSON",
        async: false,
        success: function(data){
            curRow.find(".ref_no").val(data.refid);
            curRow.find(".ref_no").attr('readonly', 'true');
            $("div.overlay").css("display", "none");
        },
        error: function(error){ $("div.overlay").css("display", "none"); }
    });
}
```

### Function 4: [get_upi_billed_transaction(curRow)](file:///c:/xampp/htdocs/ariaemi_staging/admin/assets/js/ret_billing.js#25095-25166) (Line 25095) — Check UPI Status
```javascript
function get_upi_billed_transaction(curRow){
    var pospostdata = [];
    var refno = curRow.find(".ref_no").val();
    if(refno != ""){
        $.each(bill_pos_list, function (pkey, item){
            if(item.id_device == curRow.find(".id_pos_device_lst").val()){
                pospostdata = {
                    "deviceId": item.id_device,
                    "amount": curRow.find(".amount").val(),
                    "cusid": $("#bill_cus_id").val(),
                    "seqno": $('#card_details tbody').length,
                    "refcode": curRow.find(".ref_no").val()  // From Step 1
                };
            }
        });
        $("div.overlay").css("display", "block");
        $.ajax({
            url: base_url + "index.php/admin_ret_billing/getTransactionStatus?nocache=...",
            data: {'payTransData': pospostdata},
            type: "POST", dataType: "JSON", async: false,
            success: function(data){
                if(data.responsecode == 0){
                    curRow.find(".ref_no").attr('readonly', 'true');
                    curRow.find(".amount").attr('readonly', 'true');
                    // Parse transdata for Card Type, Acquirer Name, Card Number
                    $.each(data.transdata, function (pkey, item){
                        if(item.Tag == 'Card Type'){ /* set card_name dropdown */ }
                        if(item.Tag == 'Acquirer Name'){ /* set id_device dropdown */ }
                        if(item.Tag == 'Card Number'){ curRow.find(".card_no").val(item.Value.substr(item.Value.length - 4)); }
                    });
                } else {
                    $.toaster({ priority:'danger', message:'Please submit payment..' });
                }
                $("div.overlay").css("display", "none");
            }
        });
    }
}
```

### Function 5: [get_billed_transaction(curRow)](file:///c:/xampp/htdocs/ariaemi_staging/admin/assets/js/ret_billing.js#25166-25233) (Line 25166) — Check Card Status
Same as Function 4 but for card payment rows (uses `.card_amt` instead of `.amount`).

### Function 6: [cancel_billed_transaction(curRow)](file:///c:/xampp/htdocs/ariaemi_staging/admin/assets/js/ret_billing.js#25234-25280) (Line 25234) — Cancel Payment
```javascript
function cancel_billed_transaction(curRow){
    var pospostdata = [];
    var refno = curRow.find(".ref_no").val();
    if(refno != "" && curRow.find(".card_no").val() == ""){  // Only cancel if not already paid
        $.each(bill_pos_list, function (pkey, item){
            if(item.id_device == curRow.find(".id_pos_device_lst").val()){
                pospostdata = {
                    "deviceId": item.id_device,
                    "amount": curRow.find(".card_amt").val(),
                    "cusid": $("#bill_cus_id").val(),
                    "seqno": $('#card_details tbody').length,
                    "refcode": curRow.find(".ref_no").val()
                };
            }
        });
        $("div.overlay").css("display", "block");
        $.ajax({
            url: base_url + "index.php/admin_ret_billing/cancelTransactionRequest?nocache=...",
            data: {'payTransData': pospostdata},
            type: "POST", dataType: "JSON", async: false,
            success: function(data){
                if(data.responsecode == 0){
                    curRow.find(".ref_no").val("");  // Clear reference
                }
                $("div.overlay").css("display", "none");
            }
        });
    }
}
```

### How POS Buttons Are Rendered (Line 14404-14420)
```javascript
// When creating new payment row, POS dropdown + buttons added dynamically:
var dispPOSList = "";
var dispPOSTD = "";
var dispPOSRequest = "";

$.each(bill_pos_list, function (pkey, item) {
    posselected = item.is_default == 1 ? "selected='selected'" : "";
    dispPOSList += "<option value='"+item.id_device+"' "+ posselected +">"+item.dispname+"</option>";
});

if(dispPOSList != ""){
    // POS device dropdown column
    dispPOSTD = '<td><select class="id_pos_device_lst" name="card_details[id_posdevice][]" style="width: 100px !important;">'+dispPOSList+'</select></td>';
    // Pay + Cancel buttons inline with amount field
    dispPOSRequest = '<button class="btn btn-success btn-xs" onclick="add_upi_billed_transaction($(this).closest(\'tr\'));">Pay</button>&nbsp;&nbsp;<button class="btn btn-danger btn-xs" onclick="cancel_billed_transaction($(this).closest(\'tr\'));">Cancel</button>';
}
```

### What JS Needs to Change for PhonePe

| Current (Pine Labs) | Needed for PhonePe DQR |
|---|---|
| Calls [UploadBilledTransaction](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/controllers/admin_ret_billing.php#8719-8775) | Will call `initPOSPayment` (generic) |
| Gets back `refid` → puts in `.ref_no` | Gets back `refid` + `qrdata` → needs to display QR image |
| "Status" button calls [getTransactionStatus](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/controllers/admin_ret_billing.php#8776-8799) | Will call `checkPOSStatus` (generic) |
| "Cancel" button calls [cancelTransactionRequest](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/controllers/admin_ret_billing.php#8800-8825) | Will call `cancelPOSPayment` (generic) |
| No QR display | Need `<div id="pos_qr_display">` with QR image |
| `bill_pos_list` has no `provider` field | Needs to check `item.provider` to show QR for DQR |

## Files to MODIFY for PhonePe Integration

| # | File | What to Change |
|---|---|---|
| 1 | **config.php** | Add PhonePe UAT/Prod URLs, salt keys |
| 2 | **admin_ret_billing.php** | Add generic POS entry points + PhonePe handler functions |
| 3 | **ret_billing_model.php** | Add `provider` column queries, PhonePe credential queries |
| 4 | **form.php** | Add QR display area for DQR, provider selection dropdown |
| 5 | **ret_billing.js** | Change AJAX URLs to generic endpoints, add QR display logic |

## Files to CREATE for PhonePe Integration

| # | File | Purpose |
|---|---|---|
| 1 | **pos_phonepe_handler.php** (or inline in controller) | PhonePe-specific auth (SHA256), payload (base64), API calls |
| 2 | **pos_callback.php** (controller or route) | S2S callback receiver for PhonePe webhook |
| 3 | **QR rendering library/view** | Convert `qrString` to displayable QR image |

---

# 3. Pine Labs End-to-End Flow

## Architecture Diagram
```
┌──────────────┐     AJAX POST      ┌─────────────────────────┐     cURL POST      ┌──────────────────────────┐
│  Browser JS  │ ──────────────────► │  CI Controller          │ ──────────────────► │  Plutus Cloud API (UAT)  │
│  (Frontend)  │ ◄────────────────── │  admin_ret_billing.php  │ ◄────────────────── │  Pine Labs POS Terminal  │
│              │     JSON Response   │                         │     JSON Response   │                          │
└──────────────┘                     └──────────┬──────────────┘                     └──────────────────────────┘
                                                │
                                                │ Insert / Update
                                                ▼
                                     ┌──────────────────────┐
                                     │  MySQL Database       │
                                     │  ret_pos_requests     │
                                     │  ret_pos_device_list  │
                                     │  customer             │
                                     └──────────────────────┘
```

## 3-Step Payment Lifecycle
```
1. User clicks "Pay by POS" button
   └─► AJAX POST to UploadBilledTransaction
       └─► Gets refid (PlutusTransactionReferenceID) back

2. JS starts polling (setInterval / setTimeout)
   └─► AJAX POST to getTransactionStatus (with refid)
       └─► If responsecode == 0 → Payment complete, stop polling
       └─► If still pending → continue polling

3. If user clicks "Cancel" or timeout
   └─► AJAX POST to cancelTransactionRequest (with refid)
       └─► Marks payment as cancelled (pos_req_status = 2)
```

## Database Column Lifecycle (ret_pos_requests)
```
INSERT (Step 1)            UPDATE (Step 2)                UPDATE (Step 3 - cancel)
──────────────────────    ─────────────────────────────   ────────────────────────
pos_trans_no ✓            pos_res_trans_data ✓            pos_req_status = 2
pos_store_pos_code ✓
pos_req_amount ✓
pos_usr_id ✓
pos_mer_id ✓
pos_imie ✓
pos_req_createdby ✓
pos_req_bill_cusid ✓
pos_res_ref_id ✓
```

## JS → Controller Data Mapping

### What JS Sends (`payTransData`):
| JS Field | Used In | Purpose |
|---|---|---|
| `deviceId` | All 3 functions | Fetches POS device details from DB |
| `cusid` | UploadBilled | Customer ID for transaction number |
| [amount](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/models/ret_billing_model.php#4207-4216) | UploadBilled, Cancel | Payment amount (₹) |
| `seqno` | UploadBilled | Sequence number (split tender) |
| `paytype` | UploadBilled | 1=Card, 2=UPI, 3=Both |
| `refcode` | Status, Cancel | PlutusTransactionReferenceID from Step 1 |

### What Controller Returns to JS:
| Response Field | Source | Purpose |
|---|---|---|
| `responsecode` | API `ResponseCode` | 0 = success |
| `resmessage` | API `ResponseMessage` | Human-readable message |
| `refid` | API `PlutusTransactionReferenceID` | Used for status/cancel |
| `transdata` | API `TransactionData` | Card details, approval code (Step 2 only) |

---

# 4. PhonePe API Documentation

## 4.1 PhonePe DQR (Dynamic QR) — UPI Payment

### Init API — Generate QR
```
POST https://mercury-uat.phonepe.com/enterprise-sandbox/v3/qr/init  (UAT)
POST https://mercury-t2.phonepe.com/v3/qr/init                      (PROD)
```

**Request Headers**:
| Header | Value |
|---|---|
| `Content-Type` | `application/json` |
| `X-VERIFY` | `SHA256(base64Body + apiEndpoint + saltKey) + "###" + saltIndex` |
| `X-PROVIDER-ID` | Provided by PhonePe at onboarding |
| `X-CALLBACK-URL` | Your server URL for S2S callback (optional) |
| `X-CALL-MODE` | `POST` (for callback HTTP method) |

**Request Body** (sent as base64 wrapper):
```json
{
  "request": "<base64 encoded JSON below>"
}
```

**Actual request JSON (before base64)**:
```json
{
  "merchantId": "MERCHANTUAT",
  "transactionId": "TX32321849644234",
  "merchantOrderId": "TX32321849644234",
  "amount": 1000,
  "storeId": "234555",
  "terminalId": "894237",
  "expiresIn": 1800,
  "gstBreakup": {
    "gst": 100,
    "cgst": 25,
    "sgst": 25,
    "igst": 25,
    "cess": 25,
    "gstIncentive": 100,
    "gstPercentage": 10
  },
  "invoiceDetails": {
    "invoiceNumber": "INV001",
    "invoiceDate": "2026-03-10T10:13:54.022Z",
    "invoiceName": "Aria Jewellers"
  }
}
```

**Required Fields**: `merchantId`, `transactionId`, [amount](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/models/ret_billing_model.php#4207-4216), `storeId`, `expiresIn`
**Optional Fields**: `merchantOrderId`, `terminalId`, `subMerchantId`, `gstBreakup`, `invoiceDetails`, `message`

**Transaction ID Rules**:
- Must be < 38 characters
- Alphanumeric, only `_` and `-` allowed (no `/`, `?`, spaces)
- Must be unique (duplicate → 417 error)
- Recommended length: 20-30 characters

**Amount**: In PAISE (₹100 = 10000). Type: LONG.

**expiresIn**: In SECONDS. Max = 1 month (2592000 sec). After expiry, QR is invalid.

**Success Response**:
```json
{
  "success": true,
  "code": "SUCCESS",
  "message": "Your request has been successfully completed.",
  "data": {
    "merchantId": "MERCHANTUAT",
    "transactionId": "TX32321849644234",
    "amount": 1000,
    "qrString": "upi://pay?pa=MERCHANTUAT@ybl&pn=Test%20Merchant&am=10.00&tr=TX32321849644234&..."
  }
}
```

> **IMPORTANT**: The `qrString` is a UPI intent URL. Convert this to a QR image and display on the billing screen. Customer scans with ANY UPI app.

**Error Codes**: `INVALID_TRANSACTION_ID`, `BAD_REQUEST`, `AUTHORIZATION_FAILED`, `INTERNAL_SERVER_ERROR`

---

### Check Payment Status API
```
GET https://mercury-uat.phonepe.com/enterprise-sandbox/v3/transaction/{merchantId}/{transactionId}/status  (UAT)
GET https://mercury-t2.phonepe.com/v3/transaction/{merchantId}/{transactionId}/status                      (PROD)
```

**Headers**: `Content-Type`, `X-VERIFY`, `X-PROVIDER-ID`

**X-VERIFY for Status**: `SHA256("/v3/transaction/{merchantId}/{transactionId}/status" + saltKey) + "###" + saltIndex`

**Success Response**:
```json
{
  "success": true,
  "code": "PAYMENT_SUCCESS",
  "message": "Your payment is successful.",
  "data": {
    "transactionId": "TX32321849644234",
    "merchantId": "MERCHANTUAT",
    "providerReferenceId": "P1806151323093900554957",
    "amount": 100,
    "paymentState": "COMPLETED",
    "payResponseCode": "SUCCESS",
    "paymentModes": [
      { "mode": "ACCOUNT", "amount": 100, "utr": "816626521616" }
    ],
    "transactionContext": { "storeId": "store1", "terminalId": "terminal1" }
  }
}
```

**Status Codes**:
| Code | Meaning | Final? |
|---|---|---|
| `PAYMENT_SUCCESS` | Payment completed | ✅ Final |
| `PAYMENT_ERROR` | Payment failed | ✅ Final |
| `PAYMENT_PENDING` | In progress, wait | ❌ Non-final |
| `PAYMENT_CANCELLED` | Cancelled by merchant | ✅ Final |
| `PAYMENT_DECLINED` | Declined | ✅ Final |
| `INTERNAL_SERVER_ERROR` | Server issue, retry | ❌ Non-final |

---

### Cancel Payment Request API
```
POST https://mercury-uat.phonepe.com/enterprise-sandbox/v3/charge/{merchantId}/{transactionId}/cancel  (UAT)
POST https://mercury-t2.phonepe.com/v3/charge/{merchantId}/{transactionId}/cancel                      (PROD)
```

**Headers**: `Content-Type`, `X-VERIFY`, `X-PROVIDER-ID`, `X-CALLBACK-URL`

**Response**:
```json
{ "success": true, "code": "SUCCESS", "message": "Your request has been successfully completed." }
```

**Error**: `PAYMENT_ALREADY_COMPLETED` (can't cancel completed payment)

---

### S2S Callback (Server-to-Server)
- PhonePe POSTs to your callback URL when payment reaches terminal state (SUCCESS or FAIL)
- Payload is **base64 encoded** in request body
- Headers include `X-VERIFY` for verification
- Must respond `200 OK` within 5 seconds
- 3 retry attempts on failure
- **Callback response fields**: [success](file:///c:/xampp/htdocs/ariaemi_staging/admin/assets/js/ret_billing.js#13340-13387), [code](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/models/ret_billing_model.php#3744-3771), `message`, `transactionId`, `merchantId`, [amount](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/models/ret_billing_model.php#4207-4216), `providerReferenceId`, `paymentState`, `payResponseCode`, `paymentModes`, `transactionContext`

**Callback Setup**: Either register static URL with PhonePe OR pass `X-CALLBACK-URL` header per request

---

## 4.2 PhonePe IEDC (Integrated EDC) — Card Terminal

### EDC Sale Init API
```
POST https://mercury-uat.phonepe.com/enterprise-sandbox/v1/edc/transaction/init  (UAT)
POST https://mercury-t2.phonepe.com/v1/edc/transaction/init                      (PROD)
```

**Request JSON (before base64)**:
```json
{
  "merchantId": "MERCHANTUAT",
  "storeId": "MS2403212004046998204201",
  "orderId": "testorder1",
  "terminalId": "MST2405301213090857163565",
  "transactionId": "test_transaction1",
  "amount": 200,
  "paymentModes": ["CARD", "DQR"],
  "timeAllowedForHandoverToTerminalSeconds": 60,
  "integrationMappingType": "ONE_TO_ONE"
}
```

**Required Fields**: `merchantId`, `storeId`, `terminalId`, `orderId`, `transactionId`, [amount](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/models/ret_billing_model.php#4207-4216), `paymentModes`, `integrationMappingType`

**paymentModes Options**: `CARD`, `DQR`, `CREDIT_CARD`, `DEBIT_CARD`, `ACCOUNT_WALLET`, `ACCOUNT`, `WALLET`

### EDC Status Check API
```
POST https://mercury-uat.phonepe.com/enterprise-sandbox/v1/edc/transaction/{merchantId}/{transactionId}/status  (UAT)
POST https://mercury-t2.phonepe.com/v1/edc/transaction/{merchantId}/{transactionId}/status                      (PROD)
```

**Response Data Fields**: `merchantId`, `storeId`, `terminalId`, `orderId`, `transactionId`, `referenceNumber`, `paymentMode`, [amount](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/models/ret_billing_model.php#4207-4216), [status](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/models/ret_billing_model.php#79-88), `responseCode`, `paymentInstruments`

---

## 4.3 PhonePe Refund API

```
Refund API:       https://developer.phonepe.com/offline-integration/refund-flow/refund-api
Refund Status:    https://developer.phonepe.com/offline-integration/refund-flow/check-payment-status-api
```

---

# 5. Multi-Provider Comparison

## All Providers Follow Same 3-Step Flow
```
Step 1: INIT        →  Send payment request
Step 2: STATUS      →  Poll/check if paid
Step 3: CANCEL      →  Cancel if not paid
Step 4: CALLBACK*   →  Server push (PhonePe/Razorpay/Paytm bonus)
```

## Detailed Comparison Table

| Feature | Pine Labs | PhonePe DQR | PhonePe IEDC | Razorpay | Paytm |
|---|---|---|---|---|---|
| **Auth Method** | Token in body | SHA256 header (X-VERIFY) | SHA256 header | Basic Auth (Key:Secret) | Checksum (HMAC-SHA256) |
| **Request Format** | Plain JSON | Base64 wrapped | Base64 wrapped | Plain JSON | JSON + checksum |
| **Init Endpoint** | POST /UploadBilledTransaction | POST /v3/qr/init | POST /v1/edc/transaction/init | POST /v1/orders | Payment Request API |
| **Status Endpoint** | POST /GetCloudBasedTxnStatus | GET /v3/transaction/{id}/status | POST /v1/edc/transaction/{id}/status | GET /v1/payments/{id} | Transaction Status API |
| **Cancel Endpoint** | POST /CancelTransaction | POST /v3/charge/{id}/cancel | Same as DQR | Order cancellation | Void Transaction API |
| **HTTP Method (Status)** | POST | GET | POST | GET | POST |
| **Callback/Webhook** | ❌ No | ✅ Yes (S2S) | ✅ Yes (S2S) | ✅ Yes (webhook) | ✅ Yes (webhook) |
| **Amount Format** | Paise | Paise | Paise | Paise | Paise |
| **Payment Types** | Card + UPI | UPI only (any app) | Card + UPI both | Card + UPI + QR | Card + UPI + QR + Wallet |
| **Device Identifier** | IMEI | storeId + terminalId | storeId + terminalId | Terminal ID (POS Bridge) | Terminal ID (TID) |
| **GST Support** | ❌ | ✅ (breakup fields) | ❌ | ❌ | ❌ |
| **Refund API** | ❌ | ✅ | ✅ | ✅ | ✅ |
| **Auto-Cancel** | AutoCancelDurationInMinutes | expiresIn (seconds) | handoverTime (seconds) | Order expiry | TTL |
| **UAT Testing** | Need physical terminal | Simulator APK (Android) | Mock Pay API | Sandbox | Sandbox |

## What's Same (Generic Layer)
- ✅ 3-step flow: Init → Status → Cancel
- ✅ Amount always in paise
- ✅ Need merchant ID + device/terminal ID
- ✅ Returns a reference ID after init
- ✅ Status returns Success / Failed / Pending
- ✅ All use HTTPS + JSON

## What Differs (Provider-Specific Handler)
- 🔀 Auth construction
- 🔀 Payload encoding (base64 vs plain)
- 🔀 Endpoint URLs and HTTP methods
- 🔀 Response field names
- 🔀 Extra features (GST, refund, callback)

---

# 6. Architecture Plan

## Do You Need Separate Architecture? → **NO**

One generic layer + provider-specific handlers.

```
┌────────────────────────────────────────────────────────┐
│                   BILLING FORM (JS)                    │
│   Same UI for all providers                            │
│   "Pay by POS" → calls initPOSPayment()               │
│   + QR display area (for DQR providers)                │
└───────────────────────┬────────────────────────────────┘
                        │ AJAX POST
                        ▼
┌────────────────────────────────────────────────────────┐
│            GENERIC CONTROLLER LAYER                    │
│                                                        │
│   initPOSPayment()   → reads provider → routes        │
│   checkPOSStatus()   → reads provider → routes        │
│   cancelPOSPayment() → reads provider → routes        │
│   posCallback()      → verify & process (new!)        │
│                                                        │
│   All return SAME JSON format to JS:                   │
│   {responsecode, resmessage, refid, qrdata}            │
└────────┬──────────┬───────────┬────────────────────────┘
         │          │           │
         ▼          ▼           ▼
   ┌──────────┐ ┌──────────┐ ┌──────────┐
   │ pinelabs │ │ phonepe  │ │ future   │
   │ _handler │ │ _handler │ │ _handler │
   └────┬─────┘ └────┬─────┘ └────┬─────┘
        ▼             ▼            ▼
   Plutus API    PhonePe API   Other API
```

## Controller Pattern
```php
// GENERIC ENTRY POINT — same for all providers
function initPOSPayment() {
    $model   = "ret_billing_model";
    $addData = $_POST['payTransData'];
    $posDetails = $this->$model->getPOSDeviceDetails($addData['deviceId']);
    $provider   = $posDetails['provider']; // NEW column
    
    switch($provider) {
        case 'pinelabs':
            return $this->pinelabs_init($addData, $posDetails);
        case 'phonepe_dqr':
            return $this->phonepe_dqr_init($addData, $posDetails);
        case 'phonepe_edc':
            return $this->phonepe_edc_init($addData, $posDetails);
    }
}

function checkPOSStatus() {
    // Same pattern — read provider, route to handler
}

function cancelPOSPayment() {
    // Same pattern — read provider, route to handler
}
```

## PhonePe Auth Helper
```php
function phonepe_buildXVerify($base64Payload, $apiEndpoint, $saltKey, $saltIndex) {
    $hashInput = $base64Payload . $apiEndpoint . $saltKey;
    $hash = hash('sha256', $hashInput);
    return $hash . "###" . $saltIndex;
}

function phonepe_buildRequest($requestData) {
    return json_encode(array("request" => base64_encode(json_encode($requestData))));
}
```

---

# 7. UAT Testing Guide

## PhonePe Simulator App
- **What**: Android APK that simulates customer making PhonePe payment
- **Download**: https://docs.phonepe.com/public/zAWLKIsBbFcVzOujiGfP
- **Setup**: Install → Open → Scan & Pay → Enter VPA (mobile@ybl) → Ready
- **Usage**: Your code generates QR → Customer scans with Simulator → Simulated payment SUCCESS

## Can You Test in Postman? → YES

### PhonePe DQR in Postman:
```
POST https://mercury-uat.phonepe.com/enterprise-sandbox/v3/qr/init

Headers:
  Content-Type: application/json
  X-VERIFY: {SHA256(base64Body + "/v3/qr/init" + saltKey)}###1
  X-PROVIDER-ID: {from PhonePe}

Body:
{
  "request": "{base64 encoded JSON}"
}
```

### Pine Labs in Postman:
```
POST https://www.plutuscloudserviceuat.in:8201/API/CloudBasedIntegration/V1/UploadBilledTransaction

Headers:
  Content-Type: application/json

Body:
{
  "TransactionNumber": "TEST_001",
  "SequenceNumber": 1,
  "AllowedPaymentMode": 1,
  "MerchantStorePosCode": "1221258018",
  "Amount": 10000,
  "UserID": 1,
  "MerchantID": 29610,
  "SecurityToken": "a4c9741b-2889-47b8-be2f-ba42081a246e",
  "IMEI": "ARIA1001018",
  "AutoCancelDurationInMinutes": 2
}
```

## Where Do Requests Go?

### PhonePe DQR Flow:
```
Your Server → PhonePe UAT Cloud → Generates QR → Simulator scans → Cloud marks SUCCESS → Your Status API returns SUCCESS
```

### Pine Labs Flow:
```
Your Server → Plutus UAT Cloud → Pushes to physical terminal → Cashier enters RefID → Customer taps card → Your Status API returns SUCCESS
```

## Demo Readiness

### PhonePe DQR (✅ Can demo tomorrow):
1. Need UAT credentials from PhonePe (reply to Nayan's email)
2. Download Simulator APK on Android phone
3. Test in Postman → get qrString → Scan with Simulator

### Pine Labs (⚠️ Partial demo):
1. Postman API call works (UAT URL already in config)
2. Cannot complete payment without physical terminal
3. Transaction auto-cancels after 2 minutes

## What to Ask PhonePe (Email Template):
```
Hi Nayan,

Thanks for the API documentation. We're ready for UAT testing.
Please provide:
1. Merchant ID (UAT)
2. Salt Key
3. Salt Index
4. X-PROVIDER-ID
5. Callback URL configuration

We'll start with DQR integration.

Regards,
Logimax Solutions
```

---

# 8. Database Schema

## Existing Tables

### `ret_pos_device_list`
| Column | Type | Purpose |
|---|---|---|
| id_device | INT (PK) | Device ID |
| dispname | VARCHAR | Display name |
| poscode | VARCHAR | Store POS code |
| merchantid | VARCHAR | Merchant ID |
| securitytoken | VARCHAR | Security token |
| imei | VARCHAR | Device IMEI |
| is_default | TINYINT | Default device flag |
| devicetype | INT | Device type (from settings) |

### `ret_pos_requests`
| Column | Type | Purpose |
|---|---|---|
| pos_trans_no | VARCHAR | Transaction number |
| pos_store_pos_code | VARCHAR | POS code |
| pos_req_amount | DECIMAL | Amount in paise |
| pos_usr_id | INT | User ID |
| pos_mer_id | INT | Merchant ID |
| pos_imie | VARCHAR | IMEI |
| pos_req_createdby | INT | Created by user ID |
| pos_req_bill_cusid | INT | Customer ID |
| pos_res_ref_id | VARCHAR | Plutus Reference ID |
| pos_res_trans_data | TEXT | Transaction data JSON |
| pos_req_status | TINYINT | 1=Active, 2=Cancelled |

### [customer](file:///c:/xampp/htdocs/ariaemi_staging/admin/assets/js/ret_billing.js#22762-22839) (relevant columns)
| Column | Type | Purpose |
|---|---|---|
| id_customer | INT (PK) | Customer ID |
| mobile | VARCHAR | Mobile number (last 5 used for txn no) |

## Proposed Schema Changes (for multi-provider)

### ALTER `ret_pos_device_list`:
```sql
ALTER TABLE ret_pos_device_list 
  ADD COLUMN provider VARCHAR(20) DEFAULT 'pinelabs',
  ADD COLUMN store_id VARCHAR(50) NULL,
  ADD COLUMN terminal_id VARCHAR(50) NULL,
  ADD COLUMN salt_key VARCHAR(100) NULL,
  ADD COLUMN salt_index VARCHAR(10) NULL,
  ADD COLUMN provider_id VARCHAR(50) NULL,
  ADD COLUMN callback_url VARCHAR(255) NULL;
-- provider values: 'pinelabs', 'phonepe_dqr', 'phonepe_edc', 'razorpay', 'paytm'
```

### ALTER `ret_pos_requests`:
```sql
ALTER TABLE ret_pos_requests 
  ADD COLUMN pos_provider VARCHAR(20) DEFAULT 'pinelabs',
  ADD COLUMN pos_qr_string TEXT NULL,
  ADD COLUMN pos_callback_data TEXT NULL,
  ADD COLUMN pos_utr VARCHAR(50) NULL;
```

---

# 9. Implementation Roadmap

## Phase 1: Refactor Existing Code (Generic Layer)
1. Add `provider` column to `ret_pos_device_list`
2. Create generic entry points: `initPOSPayment()`, `checkPOSStatus()`, `cancelPOSPayment()`
3. Move existing Pine Labs code into handler functions
4. Ensure all existing functionality works as before

## Phase 2: PhonePe DQR Integration
1. Add PhonePe config (UAT URLs, salt keys)
2. Implement SHA256/base64 auth helper functions
3. Create `phonepe_dqr_init()` — calls DQR Init API, returns qrString
4. Create `phonepe_dqr_status()` — calls Status API
5. Create `phonepe_dqr_cancel()` — calls Cancel API
6. Add QR rendering in billing form view
7. Add S2S callback handler (optional but recommended)

## Phase 3: PhonePe IEDC Integration (if needed)
1. Create `phonepe_edc_init()` — calls EDC Sale Init API
2. Create `phonepe_edc_status()` — calls EDC Status Check API
3. Shares auth helpers with DQR

## Phase 4: Future Providers
1. Add Razorpay handler (if client requests)
2. Add Paytm handler (if client requests)
3. Each provider = one new handler set, same generic layer

---

# 10. All Integrations in ARIA ERP

| # | Provider | Type | Status |
|---|---|---|---|
| 1 | **Pine Labs (Plutus)** | POS Terminal | ✅ Active |
| 2 | **Msg91 / Netty Fish** | SMS Gateway | ✅ Configurable |
| 3 | **Creative Point** | WhatsApp API | ✅ Active |
| 4 | **EJ ERP / Jilaba / SKTM** | ERP Sync | ✅ Configurable |
| 5 | **Khimji** | Custom Integration | ⚙️ Configured |
| 6 | **Online Payment Gateway** | Web Payments | ✅ Active |
| 7 | **Push Notifications** | Mobile Notifications | ⚙️ Configured |
| 8 | **PhonePe (DQR + IEDC)** | POS/QR | 🆕 Planned |

### Config settings (config.php):
```php
$config['sms_gateway']     = 0;     // 1 = Msg91, 2 = Netty Fish
$config['integrationType'] = 0;     // 1 = Jilaba, 2 = Sync Tool, 3 = EJ ERP, 4 = SKTM
$config['whatsappurl']     = "..."; // WhatsApp API
```

---

# 11. Key Decisions & Notes

## AutoCancelDurationInMinutes = 2 → HARDCODED
The value `2` in Pine Labs [UploadBilledTransaction](file:///c:/xampp/htdocs/ariaemi_staging/admin/application/controllers/admin_ret_billing.php#8719-8775) is hardcoded, not from config or DB. Should be made configurable.

## PhonePe Amount = Paise
Same as Pine Labs. ₹100 = 10000 paise. Both use `amount * 100`.

## PhonePe Transaction ID Rules
- < 38 characters
- Only alphanumeric, `_`, `-`
- Must be unique globally
- Recommended: 20-30 characters

## PhonePe QR Can Be Scanned by ANY UPI App
Not just PhonePe — GPay, Paytm, BHIM, etc. can all scan the DQR.

## PhonePe QR Cannot Be Scanned Twice
If payment is initiated (even if failed), QR is invalid. Must generate new one.

## S2S Callback vs Polling
- Pine Labs: Only polling (no callback)
- PhonePe: Supports both (callback preferred, polling as fallback)
- PhonePe recommends: Use callback + always implement Status API as fallback

## UAT = No Real Money
Both Pine Labs and PhonePe UAT environments simulate payments. No actual money is charged.
