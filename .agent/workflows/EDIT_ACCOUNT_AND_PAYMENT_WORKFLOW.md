# Master Implementation Guide: Edit Account & Edit Payment Module

This guide contains the complete end-to-end implementation details, database dependencies, business rules, and ready-to-copy code for deploying the **Edit Account & Edit Payment** master query page (`reports/edit_acc_pay`) into any client repository running CodeIgniter 3.

---

## Table of Contents
1. [Prerequisites & Database Requirements](#1-prerequisites--database-requirements)
2. [Step 1: Route Setup](#step-1-route-setup)
3. [Step 2: View Page Creation](#step-2-view-page-creation)
4. [Step 3: Controller Methods](#step-3-controller-methods)
5. [Step 4: Model Methods](#step-4-model-methods)
6. [Step 5: Frontend JavaScript Implementation](#step-5-frontend-javascript-implementation)
7. [Step 6: Business Flow & Edge Case Rules](#step-6-business-flow--edge-case-rules)
8. [Step 7: Verification & Deployment Checklist](#step-7-verification--deployment-checklist)

---

## 1. Prerequisites & Database Requirements

Before implementing code, verify that the target client's database has the following tables and columns:

### Required Tables & Columns:
- **`scheme_account`**:
  - `id_scheme_account` (PRIMARY KEY)
  - `id_customer` (INT)
  - `id_scheme` (INT)
  - `account_name` (VARCHAR)
  - `scheme_acc_number` (VARCHAR)
  - `start_date` (DATE)
  - `maturity_date` (DATE)
- **`payment`**:
  - `id_payment` (PRIMARY KEY)
  - `id_scheme_account` (INT)
  - `date_payment` (DATETIME)
  - `payment_amount` (DECIMAL)
  - `metal_rate` (DECIMAL)
  - `metal_weight` (DECIMAL)
  - `receipt_no` (VARCHAR)
  - `payment_status` (INT: 1=Success, 3=Failed, 7=Pending)
  - `due_date` (DATE)
  - `due_date_to` (DATE)
  - `installment` (INT)
  - `due_type` (VARCHAR: 'ND', 'AD', 'PD')
  - `due_monthyear` (VARCHAR / INT)
  - `added_by` (INT: 2=Online payment)
- **`scheme`**:
  - `id_scheme` (PRIMARY KEY)
  - `installment_cycle` (INT: 0=Monthly, 1=Daily, 2=Custom Days, 3=Daily Alternative, 4=Custom Payable)
  - `ins_days_duration` (INT)
  - `payment_chances` (INT: 0=Single payment per cycle, 1=Multiple)
  - `allow_general_advance` (INT: 0=Disabled, 1=Enabled)
- **`customer`**:
  - `id_customer` (PRIMARY KEY)
  - `mobile` (VARCHAR)

---

## Step 1: Route Setup

Add the following line to `admin/application/config/routes.php`:

```php
// CRM Queries Master / Edit Account & Payment Route
$route['reports/edit_acc_pay'] = 'admin_reports/edit_acc_pay';
```

---

## Step 2: View Page Creation

Create or update the file `admin/application/views/reports/editable_settings/acc_pay_form.php`:

```html
<?php 
$username = ($this->session->userdata['profile']);
$sync_settings = $this->config->item('integrationType');
?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>CRM Queries Master</h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">CRM Queries Master</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body">

            <!-- ===== EDIT ACCOUNT BOX ===== -->
            <div class="box box-info stock_details collapsed-box">
              <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-pencil-square-o"></i> Edit Account</h3>
                <div class="box-tools pull-right">
                  <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
                </div>
              </div>

              <div class="box-body collapse" style="display: none;">
                <div class="crm-search-bar" id="input_data_acc">
                  <div style="display:flex;align-items:center;flex-wrap:wrap;gap:10px;">
                    <div class="input-group" style="flex:1;min-width:300px;">
                      <span class="input-group-addon"><i class="fa fa-search"></i></span>
                      <input type="text" id="sch_acc_id" class="form-control" placeholder="e.g. 101, 102, 103" autocomplete="off">
                      <span class="input-group-btn">
                        <button type="button" id="acc_submit" name="acc_submit" class="btn btn-primary"><i class="fa fa-arrow-right"></i> Fetch</button>
                      </span>
                    </div>
                    <div data-toggle="buttons">
                      <label class="btn btn-sm btn-success update_acc" id="update_acc" style="display: none;">
                        <input type="radio" name="upd_acc_btn" value="1"><i class="icon fa fa-check"></i> Update
                      </label>
                      <label class="btn btn-sm btn-warning" id="cancel_acc" style="display: none;">
                        <input type="radio" name="cancel_acc_btn" value="2"><i class="icon fa fa-close"></i> Cancel
                      </label>
                    </div>
                  </div>
                  <p class="search-hint">Enter one or more Account IDs separated by commas</p>
                </div>

                <div id="table_Account">
                  <table id="table_acc_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr> 
                        <th>S.No</th>
                        <th>Sch_Acc_Id</th>
                        <th>Customer Mobile</th>
                        <th>Id_Customer</th>
                        <th>Account_Name</th>
                        <th>Account_Number</th>
                        <th>Start Date</th>
                        <th>Maturity Date</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>  
              </div>
            </div>                
            <!-- ===== END EDIT ACCOUNT BOX ===== -->

            <!-- ===== EDIT PAYMENT BOX ===== -->
            <div class="box box-info stock_details collapsed-box">
              <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-pencil-square-o"></i> Edit Payment</h3>
                <div class="box-tools pull-right">
                  <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
                </div>
              </div>

              <div class="box-body collapse" style="display: none;">
                <div class="crm-search-bar" id="input_data_paymnt">
                  <div style="display:flex;align-items:center;flex-wrap:wrap;gap:10px;">
                    <div class="input-group" style="flex:1;min-width:300px;">
                      <span class="input-group-addon"><i class="fa fa-search"></i></span>
                      <input type="text" id="pay_id" class="form-control" placeholder="e.g. 591, 592, 593" autocomplete="off">
                      <span class="input-group-btn">
                        <button type="button" id="pay_submit" name="pay_submit" class="btn btn-primary"><i class="fa fa-arrow-right"></i> Fetch</button>
                      </span>
                    </div>
                    <div data-toggle="buttons">
                      <label class="btn btn-sm btn-success update_pay" id="update_pay" style="display: none;">
                        <input type="radio" name="upd_pay_btn" value="1"><i class="icon fa fa-check"></i> Update
                      </label>
                      <label class="btn btn-sm btn-warning" id="cancel_pay" style="display: none;">
                        <input type="radio" name="cancel_pay_btn" value="2"><i class="icon fa fa-close"></i> Cancel
                      </label>
                    </div>
                  </div>
                  <p class="text-danger" id="note"></p>
                  <p class="search-hint">Enter one or more Payment IDs separated by commas</p>
                </div>

                <div id="table_payment">
                  <table id="table_paymnt_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr> 
                        <th>S.No</th>
                        <th>Pay_Id</th>
                        <th>Sch_Acc_Id</th>
                        <th>Payment_Date</th>
                        <th>Amount</th>
                        <th>Metal_Rate</th>
                        <th>Metal_Weight</th>
                        <th>Saved_Benefits</th>
                        <th>Receipt Number</th>
                        <th>Payment Status</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>  
              </div>
            </div>                
            <!-- ===== END EDIT PAYMENT BOX ===== -->

            <div class="overlay" style="display: none;">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Modal: Payment Update Confirmation -->
<div class="modal fade" id="payupdate_confirm" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Update Payment</h4>
      </div>
      <div class="modal-body">
        <strong>Are you sure! you want to update this payment?</strong>
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-danger confirm_pay_upd">Update</a>
        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
```

---

## Step 3: Controller Methods

Add these methods to `admin/application/controllers/admin_reports.php`:

```php
public function edit_acc_pay()
{
    $data['main_content'] = self::REP_VIEW . 'editable_settings/acc_pay_form';
    $this->load->view('layout/template', $data);
}

public function editAccOrPayments($func_type)
{
    $model = self::PAY_MODEL;
    switch ($func_type) {
        case 'get_acc_byId':
            $id_scheme_account = $this->input->post('id_scheme_account');
            $data = $this->$model->getSchAccByID($id_scheme_account);
            echo json_encode($data);
            break;

        case 'get_pay_byId':
            $id_payment = $this->input->post('id_payment');
            $data = $this->$model->getPaymentDataByID($id_payment);
            if (!empty($data['id_scheme_account'])) {
                $acc_data = $this->db->query("SELECT DATE_FORMAT(sa.start_date, '%Y-%m-%d') as acc_start_date, 
                    IFNULL(s.is_digi, 0) as is_digi,
                    IFNULL(s.installment_cycle, 0) as installment_cycle,
                    IFNULL(s.ins_days_duration, 0) as ins_days_duration,
                    IFNULL(s.payment_chances, 0) as payment_chances,
                    IFNULL(s.scheme_type, 0) as scheme_type,
                    IFNULL(s.flexible_sch_type, 0) as flexible_sch_type
                    FROM scheme_account sa 
                    LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme 
                    WHERE sa.id_scheme_account = " . intval($data['id_scheme_account']))->row_array();
                
                if (!empty($acc_data)) {
                    $data['acc_start_date'] = $acc_data['acc_start_date'];
                    $data['is_digi'] = $acc_data['is_digi'];
                    $data['installment_cycle'] = $acc_data['installment_cycle'];
                    $data['ins_days_duration'] = $acc_data['ins_days_duration'];
                    $data['payment_chances'] = $acc_data['payment_chances'];
                    $data['scheme_type'] = $acc_data['scheme_type'];
                    $data['flexible_sch_type'] = $acc_data['flexible_sch_type'];
                }

                // Check if this payment is the 1st payment of the scheme account
                $first_pay_row = $this->db->query("SELECT id_payment FROM payment WHERE id_scheme_account = " . intval($data['id_scheme_account']) . " ORDER BY id_payment ASC LIMIT 1")->row();
                $data['is_first_payment'] = (!empty($first_pay_row) && $first_pay_row->id_payment == $id_payment) ? 1 : 0;
            }
            echo json_encode($data);
            break;

        case 'get_acc_start_date':
            $id_scheme_account = $this->input->post('id_scheme_account');
            $row = $this->db->query("SELECT DATE_FORMAT(start_date, '%Y-%m-%d') as acc_start_date FROM scheme_account WHERE id_scheme_account = " . intval($id_scheme_account))->row_array();
            echo json_encode($row ? $row : array('acc_start_date' => ''));
            break;
    }
}

public function updatePaymentDetails()
{
    $model = self::PAY_MODEL;
    if (!empty($_POST)) {
        $log_path = 'log/payment' . date("Y-m-d") . '.txt';
        $ldata = "\n" . date('d-m-Y H:i:s') . " \n Edit Payment : " . json_encode($_POST, true);
        file_put_contents($log_path, $ldata, FILE_APPEND | LOCK_EX);
        
        $data = $this->$model->updatePaymentdata($_POST);
        echo json_encode($data);
    }
}

public function updateAccountDetails()
{
    $model = self::PAY_MODEL;
    if (!empty($_POST)) {
        $log_path = 'log/account' . date("Y-m-d") . '.txt';
        $ldata = "\n" . date('d-m-Y H:i:s') . " \n Edit Account : " . json_encode($_POST, true);
        file_put_contents($log_path, $ldata, FILE_APPEND | LOCK_EX);
        
        $cusmobile_exist = $this->$model->cusexist($_POST['mobile'], 'mobile', 'customer');
        if ($cusmobile_exist) {
            $inp_data = array('id_customer' => $cusmobile_exist['id_customer']);
            $setStatus = $this->checkCommonSettings($_POST);
            if ($setStatus['success']) {
                $data = $this->$model->updateDatacus($inp_data, 'id_scheme_account', $_POST['id_scheme_account'], 'scheme_account');
                
                $date_updated = false;
                if (!empty($_POST['start_date'])) {
                    $new_start_date = date('Y-m-d', strtotime($_POST['start_date']));
                    $new_maturity_date = $this->$model->calcMaturityDate($new_start_date, $_POST['id_scheme_account']);
                    $date_data = array(
                        'start_date' => $new_start_date,
                        'maturity_date' => $new_maturity_date
                    );
                    $date_updated = $this->$model->updData($date_data, 'id_scheme_account', $_POST['id_scheme_account'], 'scheme_account');
                    if ($date_updated) {
                        $this->$model->recalculateAccountPaymentDueDates($_POST['id_scheme_account'], $new_start_date);
                    }
                }

                if ($data == true || $date_updated) {
                    $result = array("status" => TRUE, "msg" => "Account details updated successfully.");
                } else {
                    $result = array("status" => FALSE, "msg" => "No changes detected or update failed.");
                }
            } else {
                $result = array("status" => $setStatus['success'], "msg" => $setStatus['message']);
            }
        } else {
            $result = array("status" => FALSE, "msg" => "Customer mobile number is not available.");
        }
        echo json_encode($result);
    }
}
```

---

## Step 4: Model Methods

Add these methods to `admin/application/models/payment_model.php`:

```php
function getSchAccByID($id_scheme_account)
{
    $sql = "SELECT c.mobile, c.id_customer, sa.account_name, sa.scheme_acc_number, 
        s.installment_cycle, s.ins_days_duration, s.maturity_days, s.total_installments,
        Date_format(sa.start_date, '%d-%m-%Y') as start_date, 
        Date_format(sa.maturity_date, '%d-%m-%Y') as maturity_date,
        IFNULL((
            SELECT DATE_FORMAT(due_date_to, '%Y-%m-%d')
            FROM payment
            WHERE id_scheme_account = sa.id_scheme_account
            ORDER BY id_payment ASC LIMIT 1
        ), LAST_DAY(sa.start_date)) as first_pay_due_date_to
        FROM scheme_account sa
        JOIN customer c ON c.id_customer = sa.id_customer
        JOIN scheme s ON s.id_scheme = sa.id_scheme
        WHERE sa.id_scheme_account = '$id_scheme_account'";
    return $this->db->query($sql)->row_array();
}

function updatePaymentdata($postdata)
{
    $upd_data = array();
    $pay_data = $this->getPaymentDataByID($postdata['id_payment']);
    $this->db->trans_begin();

    if ($pay_data['id_scheme_account'] != $postdata['id_scheme_account']) {
        $sql = $this->db->query("SELECT id_scheme_account from scheme_account where id_scheme_account=" . intval($postdata['id_scheme_account']));
        if ($sql->num_rows() == 1) {
            $upd_data['id_scheme_account'] = $postdata['id_scheme_account'];
        } else {
            return array("status" => FALSE, "msg" => "Not a Valid Scheme Account");
        }
    } else {
        $upd_data['id_scheme_account'] = $pay_data['id_scheme_account'];
    }

    $upd_data['date_payment'] = $postdata['date_payment'] != '' && $pay_data['added_by'] != 2 ? $postdata['date_payment'] : $pay_data['date_payment'];
    $upd_data['metal_rate']   = $postdata['metal_rate'] != '' && $postdata['metal_rate'] != $pay_data['metal_rate'] ? $postdata['metal_rate'] : $pay_data['metal_rate'];
    $upd_data['metal_weight'] = $postdata['metal_weight'] != '' && $postdata['metal_weight'] != $pay_data['metal_weight'] ? $postdata['metal_weight'] : $pay_data['metal_weight'];
    $upd_data['receipt_no']   = $postdata['receipt_no'] != '' && $postdata['receipt_no'] != $pay_data['receipt_no'] ? $postdata['receipt_no'] : $pay_data['receipt_no'];
    $upd_data['payment_status'] = $postdata['payment_status'] != '' && $pay_data['added_by'] != 2 ? $postdata['payment_status'] : $pay_data['payment_status'];
    
    $target_sch_acc = $upd_data['id_scheme_account'];
    $target_date_payment = date('Y-m-d', strtotime($upd_data['date_payment']));
    $date_changed = ($target_date_payment != date('Y-m-d', strtotime($pay_data['date_payment'])));
    $acc_changed = ($target_sch_acc != $pay_data['id_scheme_account']);
    
    if ($date_changed || $acc_changed) {
        $sch_settings = $this->db->query("SELECT s.installment_cycle, s.ins_days_duration, 
            s.payment_chances, sa.start_date, s.is_digi, s.interest, s.id_metal, 
            sa.id_branch, sa.id_scheme, sa.id_customer, s.scheme_type, s.flexible_sch_type,
            s.allow_general_advance
            FROM scheme_account sa 
            LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme 
            WHERE sa.id_scheme_account = " . $target_sch_acc);
        
        if ($sch_settings->num_rows() > 0) {
            $sch = $sch_settings->row();
            
            // Check if 1st payment -> update start_date & maturity_date
            $first_pay_row = $this->db->query("SELECT id_payment FROM payment WHERE id_scheme_account = " . intval($target_sch_acc) . " ORDER BY id_payment ASC LIMIT 1")->row();
            $is_first_payment = (!empty($first_pay_row) && $first_pay_row->id_payment == $postdata['id_payment']);

            if ($is_first_payment) {
                $new_start_date = date('Y-m-d', strtotime($target_date_payment));
                $new_maturity_date = $this->calcMaturityDate($new_start_date, $target_sch_acc);
                $this->db->where('id_scheme_account', $target_sch_acc)->update('scheme_account', array(
                    'start_date' => $new_start_date,
                    'maturity_date' => $new_maturity_date
                ));
            }

            // Single payment slot validation & Advance Due conversion
            if (intval($sch->payment_chances) == 0) {
                $check_due_date = isset($upd_data['due_date']) ? $upd_data['due_date'] : $pay_data['due_date'];
                $dup_check = $this->db->query("SELECT id_payment FROM payment 
                    WHERE id_scheme_account = " . $target_sch_acc . " 
                    AND due_date = '" . $check_due_date . "' 
                    AND payment_status = 1 
                    AND id_payment != " . $postdata['id_payment'])->row();
                
                if (!empty($dup_check)) {
                    if ($acc_changed || (isset($sch->allow_general_advance) && $sch->allow_general_advance == 1)) {
                        $upd_data['due_type'] = 'AD';
                    } else {
                        $this->db->trans_rollback();
                        return array("status" => FALSE, "msg" => "Installment slot already has a payment for this account.");
                    }
                }
            }
        }
    }
    
    $result = $this->updData($upd_data, 'id_payment', $postdata['id_payment'], 'payment');
    if ($result > 0) {
        // Recalculate target account payments using get_due_date()
        $this->recalculateAccountPaymentDueDates($target_sch_acc);

        // Recalculate original account payments if moved
        if ($acc_changed && !empty($pay_data['id_scheme_account']) && $pay_data['id_scheme_account'] != $target_sch_acc) {
            $this->recalculateAccountPaymentDueDates($pay_data['id_scheme_account']);
        }
    }

    if ($this->db->trans_status() === TRUE) {
        $this->db->trans_commit();
        return array("status" => TRUE, "msg" => "Payment details Updated Successfully");
    } else {
        $this->db->trans_rollback();
        return array("status" => FALSE, "msg" => "Unable to proceed your request");
    }
}

function recalculateAccountPaymentDueDates($id_scheme_account, $new_start_date = NULL)
{
    $id_scheme_account = intval($id_scheme_account);
    $sch_query = $this->db->query("SELECT s.installment_cycle, s.ins_days_duration, s.payment_chances, sa.start_date
        FROM scheme_account sa 
        LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme 
        WHERE sa.id_scheme_account = " . $id_scheme_account);
    
    if ($sch_query->num_rows() == 0) return;
    $sch = $sch_query->row();

    if (!empty($new_start_date)) {
        $acc_start_date = date('Y-m-d', strtotime($new_start_date));
        $this->db->where('id_scheme_account', $id_scheme_account)->update('scheme_account', array('start_date' => $acc_start_date));
    }

    // Fetch all valid payments ordered chronologically
    $payments = $this->db->query("SELECT id_payment, date_payment FROM payment WHERE id_scheme_account = " . $id_scheme_account . " AND payment_status IN (1, 2) ORDER BY date_payment ASC, id_payment ASC")->result_array();
    if (empty($payments)) return;

    if (!empty($new_start_date)) {
        $first_pay_id = $payments[0]['id_payment'];
        $this->db->where('id_payment', $first_pay_id)->update('payment', array('date_payment' => $acc_start_date));
        $payments[0]['date_payment'] = $acc_start_date;
    }

    // Reset due_date to NULL temporarily so get_due_date assigns clean non-overlapping slots
    $this->db->where('id_scheme_account', $id_scheme_account)->update('payment', array('due_date' => NULL));

    foreach ($payments as $idx => $pay) {
        $pay_id = $pay['id_payment'];
        $pay_date = date('Y-m-d', strtotime($pay['date_payment']));

        // Use native get_due_date matching payment/add logic
        $due_info = $this->get_due_date('ND', $pay_date, $id_scheme_account);
        if (empty($due_info)) $due_info = $this->get_due_date('AD', $pay_date, $id_scheme_account);
        if (empty($due_info)) $due_info = $this->get_due_date('PD', $pay_date, $id_scheme_account);

        $upd = array();
        if (!empty($due_info) && isset($due_info[0]['due_date_from'])) {
            $upd['due_date'] = $due_info[0]['due_date_from'];
            $upd['due_date_to'] = $due_info[0]['due_date_to'];
            $upd['installment'] = $due_info[0]['installment'];
            $upd['due_type'] = $due_info[0]['due_type'];
        }

        if (!empty($upd)) {
            $this->db->where('id_payment', $pay_id)->update('payment', $upd);
        }
    }
    
    $this->update_dueMonYear($id_scheme_account);
}

function update_dueMonYear($id_scheme_account)
{
    $get_dueData = $this->db->query("SELECT p.id_payment,p.id_scheme_account,date(p.date_payment), p.due_type, 
    IF(Date_Format(date(p.date_payment),'%Y-%m') != @prev_paid_date && due_type != 'AD', @months:=0, @months:=@months+1) as m,
    if(due_type = 'AD', Date_Format((date_add(date(p.date_payment), INTERVAL @months month)),'%Y-%m'), if(due_type = 'PD',null,Date_Format(date(p.date_payment),'%Y-%m'))) as paidmonth,
    @prev_paid_date := Date_Format(date(p.date_payment),'%Y-%m')
    FROM payment p 
    join (SELECT @months:= 0, @prev_paid_date := '') months 
    where p.id_scheme_account = " . intval($id_scheme_account) . " 
    order by p.id_scheme_account,p.date_payment ASC")->result_array();

    if (sizeof($get_dueData) > 0) {
        foreach ($get_dueData as $due) {
            $updData = array("due_monthyear" => $due['paidmonth']);
            $this->db->where('id_payment', $due['id_payment'])->update("payment", $updData);
        }
    }
}
```

---

## Step 5: Frontend JavaScript Implementation

Add this complete section to `admin/assets/js/reports.js`:

```javascript
// Numeric Input Restrictions
$(document).on('input', '#sch_acc_id', function () {
  this.value = this.value.replace(/[^\d,\s]/g, '');
});
$(document).on('input', '#pay_id', function () {
  this.value = this.value.replace(/[^\d,\s]/g, '');
});
$(document).on('input', '.row-sch-account-id', function () {
  this.value = this.value.replace(/[^\d]/g, '');
});

// Fetch Accounts Click Handler
$("#acc_submit").on("click", function () {
  var raw = $("#sch_acc_id").val().trim();
  if (raw === '') {
    $.toaster({ priority: 'warning', title: 'Warning!', message: 'Please enter valid Scheme Account ID(s).' });
    return;
  }
  var ids = raw.split(',').map(function(s) { return s.trim(); }).filter(function(s) { return s !== '' && $.isNumeric(s); });
  
  var results = new Array(ids.length);
  var fetchCount = 0;
  var notFoundIds = [];
  ids.forEach(function(id, index) {
    $.ajax({
      type: 'post',
      url: base_url + 'index.php/admin_reports/editAccOrPayments/get_acc_byId',
      dataType: 'json',
      data: { 'id_scheme_account': id },
      success: function (data) {
        if (Object.keys(data).length > 0) {
          results[index] = { data: data, found: true };
        } else {
          results[index] = { id: id, found: false };
          notFoundIds.push(id);
        }
      },
      complete: function() {
        fetchCount++;
        if (fetchCount === ids.length) {
          if (notFoundIds.length > 0) {
            $.toaster({ priority: 'warning', title: 'Warning!', message: 'Account not found for ID(s): ' + notFoundIds.join(', ') });
          }
          var validResults = [];
          for (var i = 0; i < results.length; i++) {
            if (results[i] && results[i].found) {
              results[i].rowIndex = validResults.length + 1;
              validResults.push(results[i]);
            }
          }
          if (validResults.length > 0) set_acc_table_multi(validResults);
        }
      }
    });
  });
});

// Fetch Payments Click Handler
$("#pay_submit").on("click", function () {
  var raw = $("#pay_id").val().trim();
  if (raw === '') {
    $.toaster({ priority: 'warning', title: 'Warning!', message: 'Please enter valid Payment ID(s).' });
    return;
  }
  var ids = raw.split(',').map(function(s) { return s.trim(); }).filter(function(s) { return s !== '' && $.isNumeric(s); });

  var results = new Array(ids.length);
  var fetchCount = 0;
  var notFoundIds = [];
  ids.forEach(function(id, index) {
    $.ajax({
      type: 'post',
      url: base_url + 'index.php/admin_reports/editAccOrPayments/get_pay_byId',
      dataType: 'json',
      data: { 'id_payment': id },
      success: function (data) {
        if (Object.keys(data).length > 0) {
          results[index] = { data: data, id_payment: id, found: true };
        } else {
          results[index] = { id_payment: id, found: false };
          notFoundIds.push(id);
        }
      },
      complete: function() {
        fetchCount++;
        if (fetchCount === ids.length) {
          if (notFoundIds.length > 0) {
            $.toaster({ priority: 'warning', title: 'Warning!', message: 'Payment data not found for ID(s): ' + notFoundIds.join(', ') });
          }
          var validResults = [];
          for (var i = 0; i < results.length; i++) {
            if (results[i] && results[i].found) {
              results[i].rowIndex = validResults.length + 1;
              validResults.push(results[i]);
            }
          }
          if (validResults.length > 0) set_pay_table_multi(validResults);
        }
      }
    });
  });
});

// Render Payment Multi-Row Table
function set_pay_table_multi(results) {
  var srHTML = '';
  var hasOnlinePayment = false;
  results.forEach(function(item) {
    var data = item.data;
    var id_payment = item.id_payment;
    var rowIdx = item.rowIndex;
    var payment_status = (data.payment_status == 1 ? 'Success' : (data.payment_status == 7 ? 'Pending' : 'Failed'));
    if (data.added_by == 2) hasOnlinePayment = true;

    srHTML += '<tr data-row="' + rowIdx + '" data-payid="' + id_payment + '"' +
      ' data-original-date="' + data.date_payment + '"' +
      ' data-original-accid="' + data.id_scheme_account + '"' +
      ' data-installment-cycle="' + (data.installment_cycle || 0) + '"' +
      ' data-is-first-payment="' + (data.is_first_payment || 0) + '">' +
      '<td>' + rowIdx + '</td>' +
      '<td><span class="row-id-payment">' + id_payment + '</span></td>' +
      '<td><input type="text" value="' + data.id_scheme_account + '" class="form-control input-sm row-sch-account-id" style="width:90px;margin:0 auto;text-align:center;" ' + (data.added_by == 2 ? 'disabled' : '') + ' /></td>' +
      '<td><div class="input-group date"><input type="text" value="' + data.date_payment + '" class="form-control input-sm date row-pay-datetimepicker" data-date-end-date="0d" data-date-format="dd-mm-yyyy" ' + (data.added_by == 2 ? 'disabled' : '') + ' /><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div></td>' +
      '<td><span class="row-pay-amt">' + parseFloat(data.payment_amount) + '</span></td>' +
      '<td><span class="row-metal-rate">' + parseFloat(data.metal_rate) + '</span></td>' +
      '<td><span class="row-metal-weight">' + (data.metal_weight ? formatMetalWeight(data.metal_weight) : '0') + '</span></td>' +
      '<td><span class="row-saved-benefits">' + (data.saved_benefits > 0 ? formatMetalWeight(data.saved_benefits) : '-') + '</span></td>' +
      '<td><span class="row-receipt-no">' + data.receipt_no + '</span></td>' +
      '<td><span class="row-payment-status-text">' + payment_status + '</span></td>' +
      '</tr>';
  });

  $('#note').html(hasOnlinePayment ? "Do not allow edit permission for online payments." : "");
  $('#table_paymnt_list > tbody').html(srHTML);
  $(".update_pay").show();
  $("#cancel_pay").show();
}

// Payment Update Confirm
$(".confirm_pay_upd").on("click", function (e) {
  e.preventDefault();
  $('#payupdate_confirm').modal('hide');
  var $rows = $('#table_paymnt_list tbody tr');
  $rows.find('.status-result-col').remove();

  $rows.each(function() {
    var $row = $(this);
    var post_data = {
      date_payment: format_date($row.find('.row-pay-datetimepicker').val()),
      id_scheme_account: $row.find('.row-sch-account-id').val(),
      id_payment: $row.find('.row-id-payment').text(),
      payment_amount: $row.find('.row-pay-amt').text(),
      metal_rate: $row.find('.row-metal-rate').text(),
      metal_weight: $row.find('.row-metal-weight').text(),
      receipt_no: $row.find('.row-receipt-no').text(),
      payment_status: $row.find('.row-payment-status').val()
    };
    $.ajax({
      type: 'post',
      url: base_url + 'index.php/admin_reports/updatePaymentDetails',
      dataType: 'json',
      data: post_data,
      success: function (data) {
        $row.find('.status-result-col').remove();
        if (data.status) {
          $row.css('background-color', '#dff0d8');
          $row.find('td:last').after('<td class="status-result-col"><span class="text-success"><i class="fa fa-check"></i> Updated</span></td>');
        } else {
          $row.css('background-color', '#f2dede');
          $row.find('td:last').after('<td class="status-result-col"><span class="text-danger"><i class="fa fa-times"></i> ' + data.msg + '</span></td>');
        }
      }
    });
  });
});
```

---

## Step 6: Business Flow & Edge Case Rules

1. **First Payment Synchronization**:
   - If the edited payment is the 1st payment (`ORDER BY id_payment ASC LIMIT 1`) of a scheme account, changing its payment date automatically updates `scheme_account.start_date` and recalculates `scheme_account.maturity_date`.
2. **Account Transfer (`Sch_Acc_Id` Edit)**:
   - When a payment is transferred from Account A to Account B, duplicate due slot conflicts on Account B are converted to `due_type = 'AD'` (Advance Due) if `allow_general_advance == 1`.
   - `recalculateAccountPaymentDueDates()` runs for **both** Account A (source) and Account B (target) to keep both accounts' installment numbers clean.
3. **Due Date & Installment Engine (`get_due_date`)**:
   - Installment numbers are NOT assigned by arbitrary array indices (`1, 2, 3...`).
   - They are calculated by `get_due_date('ND'/'AD'/'PD')`, ensuring that if Installment #5 already exists, an advance payment on the same date becomes **Installment #6**.

---

## Step 7: Verification & Deployment Checklist

When deploying to a new client codebase:
- [ ] Run SQL column checks to verify `allow_general_advance`, `due_date`, `due_date_to`, `installment`, and `due_type` exist.
- [ ] Copy route to `application/config/routes.php`.
- [ ] Create view `application/views/reports/editable_settings/acc_pay_form.php`.
- [ ] Add controller methods to `admin_reports.php`.
- [ ] Add model methods to `payment_model.php`.
- [ ] Add JS event handlers to `assets/js/reports.js`.
- [ ] Run `php -l` on modified PHP files.
- [ ] Test fetching account/payment IDs and saving updates in browser.
