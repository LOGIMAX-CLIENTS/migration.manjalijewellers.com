# Master Implementation Guide: Edit Account & Edit Payment Module

This guide contains the complete end-to-end implementation details, database dependencies, business rules, multi-row batch processing, sync tool integration, and ready-to-copy code for deploying the **Edit Account & Edit Payment** master query page (`reports/edit_acc_pay`) into any client repository running CodeIgniter 3.

---

## Table of Contents
1. [Prerequisites & Database Requirements](#1-prerequisites--database-requirements)
2. [Step 1: Route Setup](#step-1-route-setup)
3. [Step 2: View Page Implementation](#step-2-view-page-implementation)
4. [Step 3: Controller Implementation](#step-3-controller-implementation)
5. [Step 4: Model Implementation](#step-4-model-implementation)
6. [Step 5: Frontend JavaScript Implementation](#step-5-frontend-javascript-implementation)
7. [Step 6: Business Rules & Technical Architecture](#step-6-business-rules--technical-architecture)
8. [Step 7: Verification & Deployment Checklist](#step-7-verification--deployment-checklist)

---

## 1. Prerequisites & Database Requirements

Before implementing code, verify that the target client's database has the following tables and columns:

### Required Core Tables & Columns:
- **`scheme_account`**:
  - `id_scheme_account` (PRIMARY KEY, INT)
  - `id_customer` (INT)
  - `id_scheme` (INT)
  - `account_name` (VARCHAR)
  - `scheme_acc_number` (VARCHAR)
  - `start_date` (DATE / DATETIME)
  - `maturity_date` (DATE / DATETIME)
  - `total_paid_ins` (INT)
  - `group_code` (VARCHAR)
  - `active` (TINYINT: 1=Active, 0=Inactive)
  - `is_closed` (TINYINT: 1=Closed, 0=Open)
- **`payment`**:
  - `id_payment` (PRIMARY KEY, INT)
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
  - `saved_benefits` (DECIMAL)
  - `saved_benefit_amt` (DECIMAL)
  - `benefit_value` (DECIMAL)
  - `benefit_type` (TINYINT)
  - `dg_other_benefit_wgt` (DECIMAL)
  - `dg_other_benefit_amt` (DECIMAL)
  - `gst_amount` (DECIMAL)
  - `offline_tran_uniqueid` (VARCHAR)
- **`scheme`**:
  - `id_scheme` (PRIMARY KEY, INT)
  - `code` (VARCHAR)
  - `scheme_name` (VARCHAR)
  - `scheme_type` (INT: 0=Amount, 1=Weight, 2=Flexible Weight, 3=Flexible Amount)
  - `flexible_sch_type` (INT)
  - `installment_cycle` (INT: 0=Monthly, 1=Daily, 2=Custom Days, 3=Daily Alternative, 4=Custom Payable)
  - `ins_days_duration` (INT)
  - `maturity_days` (INT)
  - `total_installments` (INT)
  - `payment_chances` (INT: 0=Single payment per cycle, 1=Multiple)
  - `allow_general_advance` (INT: 0=Disabled, 1=Enabled)
  - `is_digi` (TINYINT: 1=Digi scheme, 0=Normal)
  - `interest` (TINYINT: 1=Has interest/benefit chart)
  - `id_metal` (INT)
- **`customer`**:
  - `id_customer` (PRIMARY KEY, INT)
  - `mobile` (VARCHAR)
  - `firstname` (VARCHAR)
  - `reference_no` (VARCHAR)
- **`payment_mode_details`**:
  - `id_pay_mode_details` (PRIMARY KEY, INT)
  - `id_payment` (INT)
  - `payment_mode` (VARCHAR)
  - `payment_amount` (DECIMAL)
  - `payment_status` (INT)
  - `payment_date` (DATETIME)
  - `is_active` (TINYINT: 1=Active, 0=Deactivated history)
  - `created_by` / `updated_by` (INT)

### Optional Sync & Integration Tables (Config `integrationType == 2` or `5`):
- **`customer_reg`** (`integrationType == 2`): `id_customer_reg`, `id_scheme_account`, `clientid`, `mobile`, `reg_date`, `maturity_date`
- **`transaction`** (`integrationType == 2`): `id_transaction`, `client_id`, `ref_no`, `payment_date`, `rate`, `weight`, `payment_status`, `installment_no`, `due_type`
- **`scheme_benefit_deduct_settings`**: `id_scheme`, `installment_from`, `installment_to`, `interest_type`, `interest_value`, `commodity`

---

## Step 1: Route Setup

Add the following route to `admin/application/config/routes.php`:

```php
// CRM Queries Master / Edit Account & Payment Route
$route['reports/edit_acc_pay'] = 'admin_reports/edit_acc_pay';
```

---

## Step 2: View Page Implementation

Create or update `admin/application/views/reports/editable_settings/acc_pay_form.php`:

```html
<?php 
$username = ($this->session->userdata['profile']);
$sync_settings = $this->config->item('integrationType');
?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>CRM Queries Master <small></small></h1>
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

            <?php if($this->session->flashdata('chit_alert')) { $message = $this->session->flashdata('chit_alert'); ?>
              <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
                <?php echo $message['message']; ?>
              </div>
            <?php } ?> 

            <style>
              .crm-search-bar { padding: 12px 15px; background: #f9fafb; border-bottom: 1px solid #eee; }
              .crm-search-bar .input-group { max-width: 550px; }
              .crm-search-bar .input-group .input-group-addon { background: #fff; border-right: 0; color: #999; font-size: 14px; }
              .crm-search-bar .input-group .form-control { border-left: 0; box-shadow: none; height: 36px; font-size: 13px; }
              .crm-search-bar .input-group .form-control:focus { border-color: #d2d6de; box-shadow: none; }
              .crm-search-bar .input-group .input-group-btn .btn { height: 36px; font-weight: 600; font-size: 12px; letter-spacing: 0.3px; padding: 0 18px; }
              .crm-search-bar .search-hint { font-size: 11px; color: #aaa; margin: 6px 0 0 0; }
            </style>

            <!-- ===== EDIT ACCOUNT BOX ===== -->
            <div class="box box-info stock_details collapsed-box">
              <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-pencil-square-o"></i> Edit Account </h3>
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
                <h3 class="box-title"><i class="fa fa-pencil-square-o"></i> Edit Payment </h3>
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

            <!-- ===== ACME / KHIMJI INTEGRATION EDITS (Config integrationType == 5) ===== -->
            <?php if($sync_settings == 5){ ?>
            <div class="box box-info stock_details collapsed-box">
              <div class="box-header with-border">
                <h3 class="box-title">Acme Integration Edits</h3>
                <div class="box-tools pull-right">
                  <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
                </div>
              </div>

              <div class="box-body collapse" style="display: none;">
                <div class="row col-md-12"><span class="text-primary" id="khimji_post"></span></div>    
                <div class="row col-md-12">
                  <div class="col-md-6">
                    <h4 class="box-title">Generate Acc/Rcpt No</h4>
                    <div class="col-md-3">
                      <label>Payment Id<input type="number" id="payId" class="form-control"></label>	
                    </div>
                    <div class="col-md-2 pull-right">
                      <button class="btn btn-success pull-right" id="gen_accrcpt"> Generate </button>
                    </div>
                  </div>
                  <div class="col-md-1" style="text-align:center;border-right: 2px solid dodgerblue;height:100px;"></div>
                  <div class="col-md-5">
                    <h4 class="box-title">Generate Trans-uniq ID</h4>
                    <label>Enter Payment ID</label>&nbsp;&nbsp;<input type="text" id="trans_idpay" class="form-control input-sm" style="display:inline-block;width:150px;">
                    <button class="btn btn-success pull-right" id="gen_transid"> Generate </button>
                  </div>
                </div> 
              </div>
            </div>
            <?php } ?>

            <input type="hidden" id="hiddenuserdata" value="<?php echo $username; ?>">
            <div class="overlay" style="display: none;">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Modal: Rate Update Confirmation -->
<div class="modal fade" id="rateupdate_confirm" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Update Metal Rate</h4>
      </div>
      <div class="modal-body">
        <strong>Would you like to update the Metal Rate as well?</strong>
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-danger confirm_upd">Yes</a>
        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
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

## Step 3: Controller Implementation

Add or update these methods in `admin/application/controllers/admin_reports.php`:

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
            $id_scheme_account = intval($this->input->post('id_scheme_account'));
            $data = $this->$model->getSchAccByID($id_scheme_account);
            echo json_encode($data ? $data : array());
            break;

        case 'get_pay_byId':
            $id_payment = intval($this->input->post('id_payment'));
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
                    WHERE sa.id_scheme_account = " . intval($data['id_scheme_account']))->row();
                
                $data['acc_start_date']    = isset($acc_data->acc_start_date) ? $acc_data->acc_start_date : '';
                $data['is_digi']           = isset($acc_data->is_digi) ? intval($acc_data->is_digi) : 0;
                $data['installment_cycle'] = isset($acc_data->installment_cycle) ? intval($acc_data->installment_cycle) : 0;
                $data['ins_days_duration'] = isset($acc_data->ins_days_duration) ? intval($acc_data->ins_days_duration) : 0;
                $data['payment_chances']   = isset($acc_data->payment_chances) ? intval($acc_data->payment_chances) : 0;
                $data['scheme_type']       = isset($acc_data->scheme_type) ? intval($acc_data->scheme_type) : 0;
                $data['flexible_sch_type'] = isset($acc_data->flexible_sch_type) ? intval($acc_data->flexible_sch_type) : 0;

                // Check if this payment is the 1st payment of the scheme account
                $first_pay_row = $this->db->query("SELECT id_payment FROM payment WHERE id_scheme_account = " . intval($data['id_scheme_account']) . " ORDER BY id_payment ASC LIMIT 1")->row();
                $data['is_first_payment'] = (!empty($first_pay_row) && $first_pay_row->id_payment == $id_payment) ? 1 : 0;
            }
            echo json_encode($data ? $data : array());
            break;

        case 'get_acc_start_date':
            $id_scheme_account = intval($this->input->post('id_scheme_account'));
            $acc_data = $this->db->query("SELECT DATE_FORMAT(sa.start_date, '%Y-%m-%d') as acc_start_date FROM scheme_account sa WHERE sa.id_scheme_account = " . $id_scheme_account)->row();
            echo json_encode(array('acc_start_date' => isset($acc_data->acc_start_date) ? $acc_data->acc_start_date : ''));
            break;

        case 'check_transfer_target':
            $id_scheme_account = intval($this->input->post('id_scheme_account'));
            $acc_data = $this->db->query("SELECT DATE_FORMAT(sa.start_date, '%Y-%m-%d') as acc_start_date FROM scheme_account sa WHERE sa.id_scheme_account = " . $id_scheme_account)->row();
            $check = $this->$model->checkTransferTargetAccount($id_scheme_account);
            echo json_encode(array(
                'acc_start_date' => isset($acc_data->acc_start_date) ? $acc_data->acc_start_date : '',
                'valid'          => $check['status'] ? 1 : 0,
                'msg'            => $check['status'] ? '' : $check['msg']
            ));
            break;

        case 'get_digi_benefit':
            $id_scheme_account = intval($this->input->post('id_scheme_account'));
            $date_payment      = $this->input->post('date_payment');
            $payment_amount    = floatval($this->input->post('payment_amount'));
            $metal_rate        = floatval($this->input->post('metal_rate'));
            $metal_weight      = floatval($this->input->post('metal_weight'));
            
            $result = array('is_digi' => 0, 'saved_benefits' => 0, 'saved_benefit_amt' => 0);
            
            $sch_data = $this->db->query("SELECT s.is_digi, s.interest, s.id_metal, sa.start_date, sa.id_branch, sa.id_scheme
                FROM scheme_account sa 
                LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme 
                WHERE sa.id_scheme_account = " . $id_scheme_account)->row();
            
            if (!empty($sch_data) && $sch_data->is_digi == 1) {
                $result['is_digi'] = 1;
                if ($sch_data->interest == 1) {
                    $acc_start = date('Y-m-d', strtotime($sch_data->start_date));
                    $date_diff = max(1, (strtotime($date_payment) - strtotime($acc_start)) / 86400);
                    if ($date_payment == $acc_start) $date_diff = 1;
                    
                    $benefit_data = $this->db->query("SELECT interest_type, interest_value, commodity 
                        FROM scheme_benefit_deduct_settings 
                        WHERE ('" . $date_diff . "' BETWEEN installment_from AND installment_to) 
                        AND id_scheme = " . $sch_data->id_scheme)->result_array();
                    
                    if (!empty($benefit_data)) {
                        foreach ($benefit_data as $benefit) {
                            if ($benefit['commodity'] == $sch_data->id_metal) {
                                if ($benefit['interest_type'] == 0) {
                                    $result['saved_benefit_amt'] = $payment_amount * ($benefit['interest_value'] / 100);
                                } else {
                                    $result['saved_benefit_amt'] = $benefit['interest_value'];
                                }
                                $result['saved_benefits'] = ($metal_rate > 0 ? formatMetalWeight($result['saved_benefit_amt'] / $metal_rate) : 0);
                            }
                        }
                    }
                }
            }
            echo json_encode($result);
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
                $new_start_date = '';
                $new_maturity_date = '';
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

                // Sync tool integration (config integrationType == 2)
                if ($this->config->item('integrationType') == 2) {
                    $reg_data = array('mobile' => $_POST['mobile']);
                    if ($date_updated) {
                        $reg_data['reg_date'] = $new_start_date;
                        if (!empty($new_maturity_date)) {
                            $reg_data['maturity_date'] = $new_maturity_date;
                        }
                    }
                    $reg_synced = $this->$model->syncCustomerRegOnAccEdit($_POST['id_scheme_account'], $reg_data);
                    $ldata = "\n" . date('d-m-Y H:i:s') . " \n customer_reg sync (id_scheme_account : " . $_POST['id_scheme_account'] . ") : " . ($reg_synced ? 'updated ' . json_encode($reg_data, true) : 'no matching customer_reg row / no change');
                    file_put_contents($log_path, $ldata, FILE_APPEND | LOCK_EX);
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

public function generateTransUniqId()
{
    $this->load->model("payment_model");
    if (!empty($_POST)) {
        $updData = array('offline_tran_uniqueid' => null);
        $data = $this->payment_model->updData($updData, 'id_payment', $_POST['id_payment'], 'payment');
        if ($data > 0) {
            $gen = $this->generateTranUniqueIdManually($_POST['id_payment']);
            $result = "RESPONSE: <pre>" . json_encode($gen);
        } else {
            $result = "Unable to proceed your request";
        }
        $this->session->set_flashdata('chit_alert', array('message' => $result, 'class' => 'success', 'title' => "Result"));
        echo json_encode(true);
    }
}
```

---

## Step 4: Model Implementation

Add or update these methods in `admin/application/models/payment_model.php`:

```php
function getPaymentDataByID($id_payment)
{
    $sql = $this->db->query("SELECT * from payment where id_payment = " . intval($id_payment));
    return $sql->row_array();
}

function getPaymentModeDetailsDataByID($id_payment)
{
    $sql = $this->db->query("SELECT * from payment_mode_details where id_payment = " . intval($id_payment) . " and is_active = 1");
    return $sql->result_array();
}

function getSchAccByID($id_scheme_account)
{
    $sql = $this->db->query("SELECT sa.id_scheme_account, sa.id_customer, sa.account_name, sa.scheme_acc_number, c.mobile,
    sa.start_date, sa.maturity_date, s.total_installments, s.installment_cycle, s.ins_days_duration, s.maturity_days,
    DATE_FORMAT(
        IFNULL(sa.maturity_date,
            IF(s.maturity_days IS NOT NULL AND s.maturity_days > 0,
                DATE_ADD(sa.start_date, INTERVAL s.maturity_days DAY),
                IF(
                    s.installment_cycle = 0,
                    DATE_ADD(sa.start_date, INTERVAL s.total_installments MONTH),
                    IF(
                        s.installment_cycle = 1,
                        DATE_ADD(sa.start_date, INTERVAL s.total_installments DAY),
                        IF(
                            s.installment_cycle = 2,
                            DATE_ADD(sa.start_date, INTERVAL (s.ins_days_duration * s.total_installments) DAY),
                            NULL
                        )
                    )
                )
            )
        ),
        '%Y-%m-%d'
    ) AS display_maturity_date
    FROM scheme_account sa
    LEFT JOIN customer c ON c.id_customer = sa.id_customer
    LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
    WHERE sa.id_scheme_account = " . intval($id_scheme_account));
    return $sql->row_array();
}

function calcMaturityDate($start_date, $id_scheme_account)
{
    $sql = $this->db->query("SELECT 
    DATE_FORMAT(
        IF(s.maturity_days IS NOT NULL AND s.maturity_days > 0,
            DATE_ADD('" . $start_date . "', INTERVAL s.maturity_days DAY),
            IF(
                s.installment_cycle = 0,
                DATE_ADD('" . $start_date . "', INTERVAL s.total_installments MONTH),
                IF(
                    s.installment_cycle = 1,
                    DATE_ADD('" . $start_date . "', INTERVAL s.total_installments DAY),
                    IF(
                        s.installment_cycle = 2,
                        DATE_ADD('" . $start_date . "', INTERVAL (s.ins_days_duration * s.total_installments) DAY),
                        NULL
                    )
                )
            )
        ),
        '%Y-%m-%d'
    ) AS maturity_date
    FROM scheme_account sa
    LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
    WHERE sa.id_scheme_account = " . intval($id_scheme_account));
    return $sql->row()->maturity_date;
}

public function checkTransferTargetAccount($id_scheme_account)
{
    $id_scheme_account = intval($id_scheme_account);
    if ($id_scheme_account <= 0) {
        return array("status" => FALSE, "msg" => "Enter a valid Scheme Account ID.");
    }
    $acc = $this->db->query("SELECT IFNULL(sa.is_closed, 0) as is_closed, IFNULL(sa.active, 0) as active,
        IFNULL(s.total_installments, 0) as total_installments
        FROM scheme_account sa
        LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
        WHERE sa.id_scheme_account = " . $id_scheme_account)->row();

    if (empty($acc)) {
        return array("status" => FALSE, "msg" => "Scheme account " . $id_scheme_account . " does not exist.");
    }
    if (intval($acc->is_closed) == 1) {
        return array("status" => FALSE, "msg" => "Scheme account " . $id_scheme_account . " is closed, so this payment cannot be moved to it.");
    }
    if (intval($acc->active) != 1) {
        return array("status" => FALSE, "msg" => "Scheme account " . $id_scheme_account . " is not active, so this payment cannot be moved to it.");
    }

    $total_installments = intval($acc->total_installments);
    if ($total_installments > 0) {
        $paid = $this->getPaidInsData($id_scheme_account);
        $paid_ins = isset($paid['paid_installments']) ? intval($paid['paid_installments']) : 0;
        if ($paid_ins >= $total_installments) {
            return array("status" => FALSE, "msg" => "Scheme account " . $id_scheme_account . " has already completed all its installments (" . $paid_ins . "/" . $total_installments . "), so this payment cannot be moved to it.");
        }
    }
    return array("status" => TRUE);
}

public function syncCustomerRegOnAccEdit($id_scheme_account, $reg_data)
{
    $id_scheme_account = intval($id_scheme_account);
    if ($id_scheme_account <= 0 || empty($reg_data)) return false;
    $exist = $this->db->query("SELECT id_customer_reg FROM customer_reg WHERE id_scheme_account = " . $id_scheme_account);
    if ($exist->num_rows() == 0) return false;
    $reg_data['date_update'] = date('Y-m-d H:i:s');
    $this->db->where('id_scheme_account', $id_scheme_account)->update('customer_reg', $reg_data);
    return ($this->db->affected_rows() > 0);
}

public function updateAccountPaidInstallments($id_scheme_account)
{
    $id_scheme_account = intval($id_scheme_account);
    if ($id_scheme_account <= 0) return false;
    $paid = $this->getPaidInsData($id_scheme_account);
    $total_paid_ins = isset($paid['paid_installments']) ? intval($paid['paid_installments']) : 0;
    $this->updData(array('total_paid_ins' => $total_paid_ins), 'id_scheme_account', $id_scheme_account, 'scheme_account');
    return $total_paid_ins;
}

public function checkTransactionSyncTarget($pay_data, $target_sch_acc)
{
    $trans = $this->findMirrorTransaction(
        isset($pay_data['id_payment']) ? $pay_data['id_payment'] : 0,
        $pay_data['id_scheme_account'],
        isset($pay_data['payment_ref_number']) ? $pay_data['payment_ref_number'] : ''
    );
    if (empty($trans)) return array("status" => TRUE);

    $tgt = $this->db->query("SELECT clientid FROM customer_reg WHERE id_scheme_account = " . intval($target_sch_acc))->row();
    if (empty($tgt) || trim((string)$tgt->clientid) === '') {
        return array("status" => FALSE, "msg" => "Scheme account " . intval($target_sch_acc) . " has no client id in sync table (customer_reg), so payment cannot be transferred.");
    }
    return array("status" => TRUE);
}

public function findMirrorTransaction($id_payment, $id_scheme_account, $payment_ref_number)
{
    $id_payment = intval($id_payment);
    if ($id_payment <= 0) return null;
    $src = $this->db->query("SELECT ref_no FROM scheme_account WHERE id_scheme_account = " . intval($id_scheme_account))->row();
    if (empty($src) || empty($src->ref_no)) return null;
    $ref = trim((string)$payment_ref_number);
    if ($ref === '') $ref = (string)$id_payment;
    return $this->db->query("SELECT id_transaction FROM transaction WHERE client_id = " . $this->db->escape($src->ref_no) . " AND ref_no = " . $this->db->escape($ref))->row();
}

public function syncTransactionOnPaymentEdit($id_payment, $old_id_scheme_account, $payment_ref_number)
{
    $id_payment = intval($id_payment);
    $trans = $this->findMirrorTransaction($id_payment, $old_id_scheme_account, $payment_ref_number);
    if (empty($trans)) return false;

    $pay = $this->db->query("SELECT id_scheme_account, DATE(date_payment) as date_payment,
        metal_rate, metal_weight, payment_status, installment, due_type,
        IFNULL(saved_benefits, 0) as saved_benefits, IFNULL(saved_benefit_amt, 0) as saved_benefit_amt,
        IFNULL(benefit_value, 0) as benefit_value, benefit_type
        FROM payment WHERE id_payment = " . $id_payment)->row();
    if (empty($pay)) return false;

    $upd = array(
        'payment_date'       => $pay->date_payment,
        'rate'               => $pay->metal_rate,
        'weight'             => $pay->metal_weight,
        'payment_status'     => $pay->payment_status,
        'installment_no'     => $pay->installment,
        'due_type'           => $pay->due_type,
        'saved_benefits_wgt' => $pay->saved_benefits,
        'saved_benefit_amt'  => $pay->saved_benefit_amt,
        'benefit_value'      => $pay->benefit_value,
        'benefit_type'       => $pay->benefit_type,
        'date_upd'           => date('Y-m-d H:i:s')
    );

    if (intval($pay->id_scheme_account) != intval($old_id_scheme_account)) {
        $upd['id_scheme_account'] = $pay->id_scheme_account;
        $tgt = $this->db->query("SELECT clientid FROM customer_reg WHERE id_scheme_account = " . intval($pay->id_scheme_account))->row();
        if (!empty($tgt) && trim((string)$tgt->clientid) !== '') {
            $upd['client_id'] = trim($tgt->clientid);
        }
    }

    $this->db->where('id_transaction', $trans->id_transaction)->update('transaction', $upd);
    return ($this->db->affected_rows() > 0);
}

function updatePaymentdata($postdata)
{
    $upd_data = array();
    $pay_data = $this->getPaymentDataByID($postdata['id_payment']);
    $this->db->trans_begin();

    if ($pay_data['id_scheme_account'] != $postdata['id_scheme_account']) {
        $sql = $this->db->query("SELECT id_scheme_account from scheme_account where id_scheme_account = " . intval($postdata['id_scheme_account']));
        if ($sql->num_rows() == 1) {
            $upd_data['id_scheme_account'] = $postdata['id_scheme_account'];
        } else {
            return array("status" => FALSE, "msg" => "Not a Valid Scheme Account");
        }
    } else {
        $upd_data['id_scheme_account'] = $pay_data['id_scheme_account'];
    }

    $upd_data['date_payment']   = $postdata['date_payment'] != '' && $pay_data['added_by'] != 2 ? $postdata['date_payment'] : $pay_data['date_payment'];
    $upd_data['metal_rate']     = $postdata['metal_rate'] != '' && $postdata['metal_rate'] != $pay_data['metal_rate'] ? $postdata['metal_rate'] : $pay_data['metal_rate'];
    $upd_data['metal_weight']   = $postdata['metal_weight'] != '' && $postdata['metal_weight'] != $pay_data['metal_weight'] ? $postdata['metal_weight'] : $pay_data['metal_weight'];
    $upd_data['payment_status'] = $postdata['payment_status'] != '' && $postdata['payment_status'] != $pay_data['payment_status'] && $pay_data['added_by'] != 2 ? $postdata['payment_status'] : $pay_data['payment_status'];
    
    $target_sch_acc = $upd_data['id_scheme_account'];
    $target_date_payment = date('Y-m-d', strtotime($upd_data['date_payment']));
    $date_changed = ($target_date_payment != date('Y-m-d', strtotime($pay_data['date_payment'])));
    $acc_changed  = ($target_sch_acc != $pay_data['id_scheme_account']);

    // Target account guard
    if ($acc_changed) {
        $target_check = $this->checkTransferTargetAccount($target_sch_acc);
        if ($target_check['status'] === FALSE) {
            $this->db->trans_rollback();
            return array("status" => FALSE, "msg" => $target_check['msg']);
        }
    }

    // Sync target guard
    if ($acc_changed && $this->config->item('integrationType') == 2) {
        $sync_check = $this->checkTransactionSyncTarget($pay_data, $target_sch_acc);
        if ($sync_check['status'] === FALSE) {
            $this->db->trans_rollback();
            return array("status" => FALSE, "msg" => $sync_check['msg']);
        }
    }

    if ($date_changed || $acc_changed) {
        $sch_settings = $this->db->query("SELECT s.installment_cycle, s.ins_days_duration, 
            s.payment_chances, sa.start_date, s.is_digi, s.interest, s.id_metal, 
            sa.id_branch, sa.id_scheme, sa.id_customer, s.scheme_type, s.flexible_sch_type,
            IFNULL(s.allow_general_advance, 0) as allow_general_advance
            FROM scheme_account sa 
            LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme 
            WHERE sa.id_scheme_account = " . $target_sch_acc);
        
        if ($sch_settings->num_rows() > 0) {
            $sch = $sch_settings->row();
            
            // Date bounds check
            $acc_start_date = date('Y-m-d', strtotime($sch->start_date));
            if ($target_date_payment < $acc_start_date) {
                $this->db->trans_rollback();
                return array("status" => FALSE, "msg" => "Payment date (" . $target_date_payment . ") cannot be before the scheme account start date (" . $acc_start_date . ").");
            }
            
            // Single payment slot validation & Advance Due conversion
            $payment_chances = intval($sch->payment_chances);
            $allow_advance = ($acc_changed || intval($sch->allow_general_advance) == 1);

            if ($payment_chances == 0 && !$allow_advance) {
                if ($sch->installment_cycle == 1 || $sch->installment_cycle == 3) {
                    $existing_count = $this->db->query("SELECT COUNT(*) as cnt FROM payment WHERE id_scheme_account = " . $target_sch_acc . " AND DATE(date_payment) = '" . $target_date_payment . "' AND payment_status = 1 AND id_payment != " . $postdata['id_payment'])->row()->cnt;
                    if ($existing_count > 0) {
                        $this->db->trans_rollback();
                        return array("status" => FALSE, "msg" => "A successful payment already exists on " . $target_date_payment . " for this scheme account.");
                    }
                } else if ($sch->installment_cycle == 0) {
                    $existing_count = $this->db->query("SELECT COUNT(*) as cnt FROM payment WHERE id_scheme_account = " . $target_sch_acc . " AND DATE_FORMAT(date_payment, '%Y-%m') = DATE_FORMAT('" . $target_date_payment . "', '%Y-%m') AND payment_status = 1 AND id_payment != " . $postdata['id_payment'])->row()->cnt;
                    if ($existing_count > 0) {
                        $this->db->trans_rollback();
                        return array("status" => FALSE, "msg" => "A successful payment already exists in " . date('F Y', strtotime($target_date_payment)) . " for this scheme account.");
                    }
                }
            }

            // Calculate due dates
            if ($sch->installment_cycle == 1 || $sch->installment_cycle == 3) {
                $upd_data['due_date'] = $target_date_payment;
                $upd_data['due_date_to'] = $target_date_payment;
            } else if ($sch->installment_cycle == 0) {
                $upd_data['due_date'] = date('Y-m-01', strtotime($target_date_payment));
                $upd_data['due_date_to'] = date('Y-m-t', strtotime($target_date_payment));
            } else if ($sch->installment_cycle == 2) {
                $days_duration = intval($sch->ins_days_duration);
                if ($days_duration > 0) {
                    $days_diff = (strtotime($target_date_payment) - strtotime($acc_start_date)) / 86400;
                    $cycle_index = floor($days_diff / $days_duration);
                    $cycle_start = date('Y-m-d', strtotime($acc_start_date . ' + ' . ($cycle_index * $days_duration) . ' days'));
                    $cycle_end   = date('Y-m-d', strtotime($cycle_start . ' + ' . ($days_duration - 1) . ' days'));
                    $upd_data['due_date'] = $cycle_start;
                    $upd_data['due_date_to'] = $cycle_end;
                }
            }

            // Digi benefits recalculation
            if ($sch->is_digi == 1 && $sch->interest == 1) {
                $date_diff = max(1, (strtotime($target_date_payment) - strtotime($acc_start_date)) / 86400);
                if ($target_date_payment == $acc_start_date) $date_diff = 1;
                
                $benefit_data = $this->db->query("SELECT interest_type, interest_value, commodity FROM scheme_benefit_deduct_settings WHERE ('" . $date_diff . "' BETWEEN installment_from AND installment_to) AND id_scheme = " . $sch->id_scheme)->result_array();
                
                if (!empty($benefit_data)) {
                    $payment_amount = floatval($pay_data['payment_amount']);
                    $metal_rate = floatval($upd_data['metal_rate'] > 0 ? $upd_data['metal_rate'] : $pay_data['metal_rate']);
                    foreach ($benefit_data as $benefit) {
                        if ($benefit['commodity'] == $sch->id_metal) {
                            $upd_data['saved_benefit_amt'] = ($benefit['interest_type'] == 0) ? $payment_amount * ($benefit['interest_value'] / 100) : $benefit['interest_value'];
                            $upd_data['saved_benefits'] = ($metal_rate > 0 ? formatMetalWeight($upd_data['saved_benefit_amt'] / $metal_rate) : 0);
                            $upd_data['benefit_value'] = $benefit['interest_value'];
                            $upd_data['benefit_type'] = $benefit['interest_type'];
                        }
                    }
                } else {
                    $upd_data['saved_benefits'] = 0;
                    $upd_data['saved_benefit_amt'] = 0;
                    $upd_data['benefit_value'] = 0;
                    $upd_data['benefit_type'] = 0;
                }
            }
        }
    }

    // No-change detection
    $has_changes = false;
    $compare_fields = array('id_scheme_account', 'date_payment', 'metal_rate', 'metal_weight', 'payment_status');
    foreach ($compare_fields as $field) {
        if (isset($upd_data[$field])) {
            $old_val = isset($pay_data[$field]) ? trim($pay_data[$field]) : '';
            $new_val = trim($upd_data[$field]);
            if ($field == 'date_payment') {
                $old_val = date('Y-m-d', strtotime($old_val));
                $new_val = date('Y-m-d', strtotime($new_val));
            }
            if (in_array($field, array('metal_rate', 'metal_weight'))) {
                $old_val = floatval($old_val);
                $new_val = floatval($new_val);
            }
            if ($old_val != $new_val) {
                $has_changes = true;
                break;
            }
        }
    }
    if (!$has_changes) {
        $this->db->trans_rollback();
        return array("status" => TRUE, "msg" => "No changes detected.", "no_change" => true);
    }

    $result = $this->updData($upd_data, 'id_payment', $postdata['id_payment'], 'payment');
    if ($result > 0) {
        $this->recalculateAccountPaymentDueDates($target_sch_acc);
        if ($acc_changed && !empty($pay_data['id_scheme_account']) && $pay_data['id_scheme_account'] != $target_sch_acc) {
            $this->recalculateAccountPaymentDueDates($pay_data['id_scheme_account']);
        }

        $this->updateAccountPaidInstallments($target_sch_acc);
        if ($acc_changed && !empty($pay_data['id_scheme_account']) && $pay_data['id_scheme_account'] != $target_sch_acc) {
            $this->updateAccountPaidInstallments($pay_data['id_scheme_account']);
        }

        if ($this->config->item('integrationType') == 2) {
            $this->syncTransactionOnPaymentEdit($postdata['id_payment'], $pay_data['id_scheme_account'], isset($pay_data['payment_ref_number']) ? $pay_data['payment_ref_number'] : '');
        }

        // Deactivate old payment_mode_details and insert new active batch
        $paymodedetails = $this->getPaymentModeDetailsDataByID($postdata['id_payment']);
        $this->update_modestatus_data(array(
            'is_active' => 0,
            'updated_by' => $this->session->userdata('uid'),
            'updated_time' => date('Y-m-d H:i:s')
        ), $postdata['id_payment']);

        if (!empty($paymodedetails)) {
            $new_mode_entry = array();
            foreach ($paymodedetails as $pmode) {
                $temp = array();
                foreach ($pmode as $key => $value) {
                    if ($key != 'id_pay_mode_details') {
                        if ($key == 'payment_status') {
                            $temp[$key] = $postdata['payment_status'];
                        } else if ($key == 'payment_date') {
                            $temp[$key] = $postdata['date_payment'];
                        } else if ($key == 'created_by') {
                            $temp[$key] = $this->session->userdata('uid');
                        } else if ($key == 'created_time') {
                            $temp[$key] = date("Y-m-d H:i:s");
                        } else {
                            $temp[$key] = $value;
                        }
                    }
                }
                $new_mode_entry[] = $temp;
            }
            $this->insertBatchData($new_mode_entry, 'payment_mode_details');
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

    $payments = $this->db->query("SELECT id_payment, date_payment FROM payment WHERE id_scheme_account = " . $id_scheme_account . " AND payment_status IN (1, 2) ORDER BY date_payment ASC, id_payment ASC")->result_array();
    if (empty($payments)) return;

    if (!empty($new_start_date)) {
        $first_orig_date = date('Y-m-d', strtotime($payments[0]['date_payment']));
        $this->db->where('id_scheme_account', $id_scheme_account)
                 ->where("DATE(date_payment) = '" . $this->db->escape_str($first_orig_date) . "'")
                 ->update('payment', array('date_payment' => $acc_start_date));

        foreach ($payments as &$p) {
            if (date('Y-m-d', strtotime($p['date_payment'])) === $first_orig_date) {
                $p['date_payment'] = $acc_start_date;
            }
        }
        unset($p);
    }

    $this->db->where('id_scheme_account', $id_scheme_account)->update('payment', array('due_date' => NULL));

    foreach ($payments as $idx => $pay) {
        $pay_id = $pay['id_payment'];
        $pay_date = date('Y-m-d', strtotime($pay['date_payment']));

        $upd = array();

        if (intval($sch->payment_chances) == 1) {
            $cycle = intval($sch->installment_cycle);
            if ($cycle == 0) {
                $same_cycle = "DATE_FORMAT(p.date_payment, '%Y-%m') = '" . date('Y-m', strtotime($pay_date)) . "'";
            } else if ($cycle == 2 && intval($sch->ins_days_duration) > 0) {
                $days_duration = intval($sch->ins_days_duration);
                $cyc_start_ref = date('Y-m-d', strtotime($sch->start_date));
                $cyc_diff      = floor((strtotime($pay_date) - strtotime($cyc_start_ref)) / 86400);
                $cyc_index     = floor($cyc_diff / $days_duration);
                $cyc_from      = date('Y-m-d', strtotime($cyc_start_ref . ' + ' . ($cyc_index * $days_duration) . ' days'));
                $cyc_to        = date('Y-m-d', strtotime($cyc_from . ' + ' . ($days_duration - 1) . ' days'));
                $same_cycle    = "DATE(p.date_payment) BETWEEN '" . $cyc_from . "' AND '" . $cyc_to . "'";
            } else {
                $same_cycle = "DATE(p.date_payment) = '" . $this->db->escape_str($pay_date) . "'";
            }

            $sameday_pay = $this->db->query(
                "SELECT p.installment, p.due_date, p.due_date_to, p.due_type
                 FROM payment p
                 WHERE p.id_scheme_account = " . $id_scheme_account . "
                   AND p.payment_status IN (1, 2)
                   AND p.installment IS NOT NULL
                   AND p.installment > 0
                   AND p.due_date IS NOT NULL
                   AND " . $same_cycle . "
                   AND p.id_payment != " . (int)$pay_id . "
                 ORDER BY p.id_payment ASC
                 LIMIT 1"
            )->row_array();

            if (!empty($sameday_pay) && !empty($sameday_pay['installment'])) {
                $upd['due_date']    = $sameday_pay['due_date'];
                $upd['due_date_to'] = $sameday_pay['due_date_to'];
                $upd['installment'] = $sameday_pay['installment'];
                $upd['due_type']    = $sameday_pay['due_type'];
            }
        }

        if (empty($upd)) {
            $due_info = $this->get_due_date('ND', $pay_date, $id_scheme_account);
            if (empty($due_info)) $due_info = $this->get_due_date('AD', $pay_date, $id_scheme_account);
            if (empty($due_info)) $due_info = $this->get_due_date('PD', $pay_date, $id_scheme_account);

            if (!empty($due_info) && isset($due_info[0]['due_date_from'])) {
                $upd['due_date']    = $due_info[0]['due_date_from'];
                $upd['due_date_to'] = $due_info[0]['due_date_to'];
                $upd['installment'] = $due_info[0]['installment'];
                $upd['due_type']    = $due_info[0]['due_type'];
            }
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

Add or update these handlers in `admin/assets/js/reports.js`:

```javascript
// Numeric Input Restrictions for Comma-separated Batch IDs
let toasterShown = false;
$(document).on('input', '#sch_acc_id', function (e) {
  const val = $(this).val();
  if (!/^[\d,\s]*$/.test(val)) {
    $(this).val(val.replace(/[^\d,\s]/g, ""));
    if (!toasterShown) {
      $.toaster({ priority: 'error', title: 'Warning!', message: 'Only Numbers and commas are allowed ...' });
      toasterShown = true;
      setTimeout(() => { toasterShown = false; }, 2000);
    }
    $(this).focus();
  }
});
let paytoasterShown = false;
$(document).on('input', '#pay_id', function (e) {
  const val = $(this).val();
  if (!/^[\d,\s]*$/.test(val)) {
    $(this).val(val.replace(/[^\d,\s]/g, ""));
    if (!paytoasterShown) {
      $.toaster({ priority: 'error', title: 'Warning!', message: 'Only Numbers and commas are allowed ...' });
      paytoasterShown = true;
      setTimeout(() => { paytoasterShown = false; }, 2000);
    }
    $(this).focus();
  }
});
$(document).on('input', '.row-sch-account-id', function () {
  this.value = this.value.replace(/[^\d]/g, '');
});

// ==================== ACCOUNT SUBMIT (multi-row batch fetch) ====================
$("#acc_submit").on("click", function () {
  var raw = $("#sch_acc_id").val().trim();
  if (raw === '') {
    $.toaster({ priority: 'warning', title: 'Warning!', message: 'Please enter valid Scheme Account ID(s).' });
    return;
  }
  var ids = raw.split(',').map(function(s) { return s.trim(); }).filter(function(s) { return s !== '' && $.isNumeric(s); });
  if (ids.length === 0) {
    $.toaster({ priority: 'warning', title: 'Warning!', message: 'Please enter valid Scheme Account ID(s).' });
    return;
  }

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
      error: function() { results[index] = { id: id, found: false }; notFoundIds.push(id); },
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

// ==================== PAYMENT SUBMIT (multi-row batch fetch) ====================
$("#pay_submit").on("click", function () {
  var raw = $("#pay_id").val().trim();
  if (raw === '') {
    $.toaster({ priority: 'warning', title: 'Warning!', message: 'Please enter valid Payment ID(s).' });
    return;
  }
  var ids = raw.split(',').map(function(s) { return s.trim(); }).filter(function(s) { return s !== '' && $.isNumeric(s); });
  if (ids.length === 0) {
    $.toaster({ priority: 'warning', title: 'Warning!', message: 'Please enter valid Payment ID(s).' });
    return;
  }

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
      error: function() { results[index] = { id_payment: id, found: false }; notFoundIds.push(id); },
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

// ==================== RENDER PAYMENT TABLE (multi-row) ====================
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
      ' data-ins-days-duration="' + (data.ins_days_duration || 0) + '"' +
      ' data-payment-chances="' + (data.payment_chances || 0) + '"' +
      ' data-scheme-type="' + (data.scheme_type || 0) + '"' +
      ' data-flexible-type="' + (data.flexible_sch_type || 0) + '"' +
      ' data-acc-start="' + (data.acc_start_date || '') + '">' +
      '<td>' + rowIdx + '</td>' +
      '<td><span class="row-id-payment">' + id_payment + '</span></td>' +
      '<td><input type="text" value="' + data.id_scheme_account + '" class="form-control input-sm row-sch-account-id" style="width:90px;margin:0 auto;text-align:center;" ' + (data.added_by == 2 ? 'disabled' : '') + ' /></td>' +
      '<td><div class="input-group date"><input type="text" value="' + data.date_payment + '" class="form-control input-sm date row-pay-datetimepicker" data-date-end-date="0d" data-date-format="dd-mm-yyyy" ' + (data.added_by == 2 ? 'disabled' : '') + ' /><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div></td>' +
      '<td><span class="row-pay-amt">' + parseFloat(data.payment_amount) + '</span></td>' +
      '<td><span class="row-metal-rate">' + parseFloat(data.metal_rate) + '</span></td>' +
      '<td><span class="row-metal-weight">' + (data.metal_weight != null && data.metal_weight != '' ? formatMetalWeight(data.metal_weight) : '0') + '</span></td>' +
      '<td><span class="row-saved-benefits">' + (data.saved_benefits != null && data.saved_benefits != '' && parseFloat(data.saved_benefits) > 0 ? formatMetalWeight(data.saved_benefits) : '-') + '</span></td>' +
      '<td><span class="row-receipt-no">' + (data.receipt_no != null && data.receipt_no !== '' ? data.receipt_no : '-') + '</span></td>' +
      '<td><span class="row-payment-status-text">' + payment_status + '</span>' +
      '<input type="hidden" class="row-payment-status" value="' + data.payment_status + '">' +
      '<input type="hidden" class="row-gst-amount" value="' + (parseFloat(data.gst_amount) || 0) + '">' +
      '<input type="hidden" class="row-acc-start-date" value="' + (data.acc_start_date || '') + '">' +
      '<input type="hidden" class="row-original-date" value="' + data.date_payment + '">' +
      '<input type="hidden" class="row-added-by" value="' + (data.added_by || 0) + '">' +
      '</td>' +
      '</tr>';
  });
  $('#note').html(hasOnlinePayment ? "Do not allow edit permission for online payments on the payment date and payment status columns." : "");
  $('#table_paymnt_list > tbody').html(srHTML);
  $(".update_pay").show();
  $("#cancel_pay").show();
}

// ==================== RENDER ACCOUNT TABLE (multi-row) ====================
function set_acc_table_multi(results) {
  var srHTML = '';
  window._accRowSchData = {};
  results.forEach(function(item) {
    var data = item.data;
    var rowIdx = item.rowIndex;
    var startDateFormatted = data.start_date ? data.start_date.substring(0, 10) : '';
    var maturityDateDisplay = data.display_maturity_date || '-';
    window._accRowSchData[rowIdx] = {
      maturity_days: parseInt(data.maturity_days) || 0,
      total_installments: parseInt(data.total_installments) || 0,
      installment_cycle: parseInt(data.installment_cycle) || 0,
      ins_days_duration: parseInt(data.ins_days_duration) || 0
    };
    srHTML += '<tr data-row="' + rowIdx + '" data-accid="' + data.id_scheme_account + '"' +
      ' data-original-mobile="' + data.mobile + '"' +
      ' data-original-startdate="' + startDateFormatted + '">' +
      '<td>' + rowIdx + '</td>' +
      '<td><span class="row-id-sch-acc">' + data.id_scheme_account + '</span></td>' +
      '<td><input type="text" minlength="10" maxlength="10" onkeypress="return /^[0-9]$/i.test(event.key)" class="row-cus-mobile form-control input-sm" value="' + data.mobile + '" style="width:120px;margin:0 auto;text-align:center;"></td>' +
      '<td><span class="row-cust-id">' + data.id_customer + '</span></td>' +
      '<td><span class="row-account-name">' + data.account_name + '</span></td>' +
      '<td><span class="row-acc-number">' + (data.scheme_acc_number && data.scheme_acc_number !== 'null' ? data.scheme_acc_number : '<em style="color:#999;">Not Allocated</em>') + '</span></td>' +
      '<td><input type="text" value="' + startDateFormatted + '" class="form-control input-sm row-acc-start-date" data-date-format="yyyy-mm-dd" style="width:120px;margin:0 auto;text-align:center;" /></td>' +
      '<td><span class="row-acc-maturity-date" style="font-weight:bold;color:#3c8dbc;">' + maturityDateDisplay + '</span></td>' +
      '</tr>';
  });
  $('#table_acc_list > tbody').html(srHTML);

  $('#table_acc_list tbody tr').each(function() {
    var $row = $(this);
    var rowIdx = $row.data('row');
    var schData = window._accRowSchData ? window._accRowSchData[rowIdx] : {};
    var cycle = schData.installment_cycle;
    var origStartStr = String($row.data('original-startdate') || '').trim();

    var datepickerOptions = { format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true };
    if (origStartStr) {
      var origParts = origStartStr.substring(0, 10).split('-');
      if (origParts.length === 3) {
        var startYr = parseInt(origParts[0], 10);
        var startMo = parseInt(origParts[1], 10) - 1;
        var startDy = parseInt(origParts[2], 10);
        if (cycle === 1 || cycle === 3) {
          datepickerOptions.endDate = new Date(startYr, startMo, startDy);
        } else if (cycle === 0) {
          datepickerOptions.endDate = new Date(startYr, startMo + 1, 0);
        } else {
          datepickerOptions.endDate = new Date(startYr, startMo, startDy);
        }
      }
    }
    $row.find('.row-acc-start-date').datepicker(datepickerOptions);
  });
  $(".update_acc").show();
  $("#cancel_acc").show();
}

// ==================== ACCOUNT START DATE CHANGE (maturity recalc) ====================
$('body').on('changeDate change', '.row-acc-start-date', function () {
  var $row = $(this).closest('tr');
  var rowIdx = $row.data('row');
  var schData = window._accRowSchData ? window._accRowSchData[rowIdx] : {};
  var cycle = schData.installment_cycle;
  var newStartDate = $(this).val() ? $(this).val().trim() : '';
  if (!newStartDate) return;

  var origStartStr = String($row.data('original-startdate') || '').trim();
  if (origStartStr) {
    var origParts = origStartStr.substring(0, 10).split('-');
    if (origParts.length === 3) {
      var startYr = parseInt(origParts[0], 10);
      var startMo = parseInt(origParts[1], 10) - 1;
      var startDy = parseInt(origParts[2], 10);
      var selectedDt = new Date(newStartDate);
      selectedDt.setHours(0, 0, 0, 0);

      if (cycle === 1 || cycle === 3) {
        var maxDailyDt = new Date(startYr, startMo, startDy, 23, 59, 59, 999);
        if (selectedDt > maxDailyDt) {
          $.toaster({ priority: 'danger', title: 'Invalid Date!', message: 'Row #' + rowIdx + ': Daily scheme start date cannot be later than ' + origStartStr.substring(0, 10) + '.' });
          $(this).val(origStartStr.substring(0, 10));
          return;
        }
      } else if (cycle === 0) {
        var maxMonthlyDt = new Date(startYr, startMo + 1, 0, 23, 59, 59, 999);
        if (selectedDt > maxMonthlyDt) {
          $.toaster({ priority: 'danger', title: 'Invalid Date!', message: 'Row #' + rowIdx + ': Monthly scheme start date cannot be in next month from ' + origStartStr.substring(0, 10) + '.' });
          $(this).val(origStartStr.substring(0, 10));
          return;
        }
      }
    }
  }

  if (newStartDate && schData) {
    var startDt = new Date(newStartDate);
    var maturityDt;
    if (schData.maturity_days > 0) {
      maturityDt = new Date(startDt.getTime() + schData.maturity_days * 86400000);
    } else if (schData.total_installments > 0) {
      if (schData.installment_cycle === 0) {
        maturityDt = new Date(startDt.getFullYear(), startDt.getMonth() + schData.total_installments, startDt.getDate());
      } else if (schData.installment_cycle === 1) {
        maturityDt = new Date(startDt.getTime() + schData.total_installments * 86400000);
      } else if (schData.installment_cycle === 2) {
        maturityDt = new Date(startDt.getTime() + (schData.ins_days_duration * schData.total_installments) * 86400000);
      }
    }
    if (maturityDt) {
      var yy = maturityDt.getFullYear();
      var mm = ('0' + (maturityDt.getMonth() + 1)).slice(-2);
      var dd = ('0' + maturityDt.getDate()).slice(-2);
      $row.find('.row-acc-maturity-date').text(yy + '-' + mm + '-' + dd);
    }
  }
});

// ==================== PAYMENT TRANSFER TARGET VALIDATION ====================
function validateTransferTarget($input) {
  var $row = $input.closest('tr');
  var rowIdx = $row.data('row');
  var payId = $row.data('payid');
  var newAccId = $input.val().trim();
  var originalAccId = String($row.data('original-accid') || '').trim();

  if (newAccId === '' || !$.isNumeric(newAccId)) {
    $.toaster({ priority: 'warning', title: 'Warning!', message: 'Row #' + rowIdx + ': Enter a valid Scheme Account ID.' });
    $input.val(originalAccId);
    return;
  }
  if (newAccId === originalAccId) return;

  $.ajax({
    url: base_url + 'index.php/admin_reports/editAccOrPayments/check_transfer_target',
    type: 'POST',
    data: { id_scheme_account: newAccId },
    dataType: 'json',
    success: function (res) {
      if (!res || res.valid != 1) {
        $.toaster({ priority: 'danger', title: 'Error!', message: 'Row #' + rowIdx + ': ' + ((res && res.msg) ? res.msg : 'Scheme Account ' + newAccId + ' cannot be used.') });
        $input.val(originalAccId);
        return;
      }
      var accStartDate = res.acc_start_date || '';
      $row.find('.row-acc-start-date').val(accStartDate);
      var payDate = format_date($row.find('.row-pay-datetimepicker').val());
      if (accStartDate && payDate && payDate < accStartDate) {
        $.toaster({ priority: 'danger', title: 'Error!', message: 'Row #' + rowIdx + ' (Pay ID: ' + payId + '): Payment date ' + payDate + ' is before account ' + newAccId + ' start date (' + accStartDate + ').' });
      }
    }
  });
}
$('body').on('change', '.row-sch-account-id', function () { validateTransferTarget($(this)); });
$('body').on('paste', '.row-sch-account-id', function () {
  var $input = $(this);
  setTimeout(function () { validateTransferTarget($input); }, 0);
});

// ==================== ACCOUNT MOBILE LOOKUP ====================
$('body').on('keyup', '.row-cus-mobile', function () {
  var $row = $(this).closest('tr');
  var mob = $(this).val().trim();
  if (mob.length > 0 && mob.length < 10) {
    $row.find('.row-cust-id').text('-');
  }
  if (mob.length == 10) {
    $.ajax({
      url: base_url + 'index.php/admin_customer/ajax_get_customers_list',
      data: { 'mobile': mob },
      dataType: "JSON",
      type: "POST",
      success: function (data) {
        var rowIdx = $row.data('row');
        if (data.length > 0)
          $row.find('.row-cust-id').text(data[0].id_customer);
        else
          $.toaster({ priority: 'warning', title: 'Warning!', message: 'Row #' + rowIdx + ': No customer found for this mobile number.' });
      }
    });
  }
});

// Helper for row-level status updates
function setRowStatusCell($row, type, message) {
  var iconClass = 'fa-check', textClass = 'text-success', bgClass = '#dff0d8';
  if (type === 'no_change') { iconClass = 'fa-minus-circle'; textClass = 'text-muted'; bgClass = '#f5f5f5'; }
  else if (type === 'danger') { iconClass = 'fa-times'; textClass = 'text-danger'; bgClass = '#f2dede'; }

  $row.css('background-color', bgClass);
  var statusHtml = '<span class="' + textClass + '"><i class="fa ' + iconClass + '"></i> ' + message + '</span>';
  var $statusTd = $row.find('.row-status-td');
  if ($statusTd.length > 0) $statusTd.html(statusHtml);
  else $row.append('<td class="row-status-td" style="min-width:100px;">' + statusHtml + '</td>');
}

// ==================== PAYMENT UPDATE CONFIRM & SAVE ====================
$(".confirm_pay_upd").on("click", function (e) {
  e.preventDefault();
  $('#payupdate_confirm').modal('hide');
  var $rows = $('#table_paymnt_list tbody tr');
  if ($rows.length === 0) return;

  $rows.find('.row-status-td').remove();
  var successCount = 0, failCount = 0, noChangeCount = 0, processedCount = 0, totalRows = $rows.length;

  $rows.each(function() {
    var $row = $(this);
    var rowIdx = $row.data('row');
    var post_data = {
      date_payment: format_date($row.find('.row-pay-datetimepicker').val()),
      id_scheme_account: $row.find('.row-sch-account-id').val(),
      id_payment: $row.find('.row-id-payment').text(),
      payment_amount: $row.find('.row-pay-amt').text(),
      metal_rate: $row.find('.row-metal-rate').text(),
      metal_weight: $row.find('.row-metal-weight').text(),
      payment_status: $row.find('.row-payment-status').val()
    };
    $.ajax({
      type: 'post',
      url: base_url + 'index.php/admin_reports/updatePaymentDetails',
      dataType: 'json',
      data: post_data,
      success: function (data) {
        if (data.status && data.no_change) {
          noChangeCount++;
          setRowStatusCell($row, 'no_change', 'No changes');
        } else if (data.status) {
          successCount++;
          setRowStatusCell($row, 'success', 'Updated');
        } else {
          failCount++;
          setRowStatusCell($row, 'danger', data.msg);
          $.toaster({ priority: 'danger', title: 'Update Failed!', message: 'Row #' + rowIdx + ' (Pay ID: ' + post_data.id_payment + '): ' + data.msg });
        }
      },
      error: function() {
        failCount++;
        setRowStatusCell($row, 'danger', 'Server Error');
      },
      complete: function() {
        processedCount++;
        if (processedCount === totalRows) {
          if (failCount === 0 && noChangeCount === totalRows) {
            $.toaster({ priority: 'info', title: 'Info', message: 'No changes detected in any payment row.' });
          } else if (failCount === 0) {
            $.toaster({ priority: 'success', title: 'Success!', message: successCount + ' updated, ' + noChangeCount + ' unchanged out of ' + totalRows + ' payment(s).' });
            if (successCount > 0) setTimeout(function() { window.location.reload(); }, 2000);
          } else {
            $.toaster({ priority: 'warning', title: 'Completed', message: successCount + ' updated, ' + failCount + ' failed, ' + noChangeCount + ' unchanged out of ' + totalRows + ' payment(s).' });
          }
        }
      }
    });
  });
});

// ==================== ACCOUNT UPDATE CONFIRM & SAVE ====================
$(".update_acc").on("click", function () {
  var $rows = $('#table_acc_list tbody tr');
  if ($rows.length === 0) return;

  $rows.find('.row-status-td').remove();
  var hasErrors = false;
  $rows.each(function() {
    var $row = $(this);
    var rowIdx = $row.data('row');
    var mobile = $row.find('.row-cus-mobile').val().trim();
    if (mobile.length != 10) {
      $.toaster({ priority: 'warning', title: 'Warning!', message: 'Row #' + rowIdx + ': Invalid mobile number. Must be 10 digits.' });
      $row.find('.row-cus-mobile').focus();
      $row.css('background-color', '#f2dede');
      hasErrors = true;
    }
  });
  if (hasErrors) return;

  var successCount = 0, failCount = 0, noChangeCount = 0, processedCount = 0, totalRows = $rows.length;

  $rows.each(function() {
    var $row = $(this);
    var currentMobile = $row.find('.row-cus-mobile').val().trim();
    var currentStartDate = $row.find('.row-acc-start-date').val().trim();
    var originalMobile = String($row.data('original-mobile') || '').trim();
    var originalStartDate = String($row.data('original-startdate') || '').trim();

    if (currentMobile === originalMobile && currentStartDate === originalStartDate) {
      noChangeCount++;
      processedCount++;
      setRowStatusCell($row, 'no_change', 'No changes');
      if (processedCount === totalRows) {
        if (noChangeCount === totalRows) {
          $.toaster({ priority: 'info', title: 'Info', message: 'No changes detected in any account row.' });
        } else if (failCount === 0) {
          $.toaster({ priority: 'success', title: 'Success!', message: successCount + ' updated, ' + noChangeCount + ' unchanged out of ' + totalRows + ' account(s).' });
          if (successCount > 0) setTimeout(function() { window.location.reload(); }, 2000);
        } else {
          $.toaster({ priority: 'warning', title: 'Completed', message: successCount + ' updated, ' + failCount + ' failed, ' + noChangeCount + ' unchanged out of ' + totalRows + ' account(s).' });
        }
      }
      return;
    }

    var post_data = {
      id_scheme_account: $row.find('.row-id-sch-acc').text(),
      mobile: currentMobile,
      id_customer: $row.find('.row-cust-id').text(),
      account_name: $row.find('.row-account-name').text(),
      scheme_acc_number: $row.find('.row-acc-number').text(),
      start_date: currentStartDate
    };
    $.ajax({
      type: 'post',
      url: base_url + 'index.php/admin_reports/updateAccountDetails',
      dataType: 'json',
      data: post_data,
      success: function (data) {
        if (data.status) {
          successCount++;
          setRowStatusCell($row, 'success', 'Updated');
        } else {
          failCount++;
          setRowStatusCell($row, 'danger', data.msg);
        }
      },
      error: function() {
        failCount++;
        setRowStatusCell($row, 'danger', 'Server Error');
      },
      complete: function() {
        processedCount++;
        if (processedCount === totalRows) {
          if (failCount === 0 && noChangeCount === totalRows) {
            $.toaster({ priority: 'info', title: 'Info', message: 'No changes detected in any account row.' });
          } else if (failCount === 0) {
            $.toaster({ priority: 'success', title: 'Success!', message: successCount + ' updated, ' + noChangeCount + ' unchanged out of ' + totalRows + ' account(s).' });
            if (successCount > 0) setTimeout(function() { window.location.reload(); }, 2000);
          } else {
            $.toaster({ priority: 'warning', title: 'Completed', message: successCount + ' updated, ' + failCount + ' failed, ' + noChangeCount + ' unchanged out of ' + totalRows + ' account(s).' });
          }
        }
      }
    });
  });
});

// Cancel Reset Buttons
$("#cancel_acc").on("click", function (e) {
  e.preventDefault(); e.stopPropagation();
  $('#table_acc_list > tbody').html('');
  $('#sch_acc_id').val('');
  $('.update_acc').hide(); $('#cancel_acc').hide();
  return false;
});
$("#cancel_pay").on("click", function (e) {
  e.preventDefault(); e.stopPropagation();
  $('#table_paymnt_list > tbody').html('');
  $('#pay_id').val('');
  $('.update_pay').hide(); $('#cancel_pay').hide(); $('#note').html('');
  return false;
});
```

---

## Step 6: Business Rules & Technical Architecture

### 1. Multi-Row Batch Fetch & Update Architecture:
- Allows comma-separated inputs (e.g. `101, 102, 103`) for both Account and Payment searches.
- Fires asynchronous AJAX requests per ID, handles individual missing IDs gracefully with user-friendly warnings, and builds a consolidated table.
- Row status updates (`Updated`, `No changes`, `Server Error`, error messages) are dynamically rendered per row (`.row-status-td`) with visual feedback (`#dff0d8` green, `#f2dede` red, `#f5f5f5` gray).

### 2. Scheme Account Transfer Validation (`checkTransferTargetAccount`):
- When moving a payment to a new `Sch_Acc_Id`, client and server validate that the target account is:
  1. Existing in database.
  2. Open (`is_closed == 0`).
  3. Active (`active == 1`).
  4. Has open installment slots remaining (`paid_ins < total_installments`).
- If target validation fails, transaction rolls back and returns specific failure reasons.

### 3. Payment Slot Recalculation Engine (`recalculateAccountPaymentDueDates`):
- Installment numbers are **never** assigned by hardcoded array index.
- Chronologically orders all valid payments (`payment_status IN (1, 2)`).
- Temporarily clears `due_date = NULL` and passes payments through `get_due_date('ND'/'AD'/'PD')`.
- For multi-payment-chance schemes (`payment_chances == 1`), payments occurring within the same cycle share installment numbers.
- Updates `due_monthyear` via `update_dueMonYear()` and refreshes `total_paid_ins` on both source and target accounts via `updateAccountPaidInstallments()`.

### 4. Digi Scheme Benefit Recalculation:
- Automatically triggered on payment date change for Digi schemes (`is_digi == 1` & `interest == 1`).
- Computes `date_difference` from `start_date` and queries `scheme_benefit_deduct_settings`.
- Calculates percentage or fixed saved benefits (`saved_benefit_amt`, `saved_benefits`, `benefit_value`, `benefit_type`, `dg_other_benefit_wgt`, `dg_other_benefit_amt`).
- Clears benefit fields if scheme is non-Digi or no slab matches.

### 5. Online Payment Restrictions (`added_by == 2`):
- Online gateway payments (`added_by == 2`) have disabled account ID and date fields in UI.
- Backend strictly preserves `date_payment` and `payment_status` for online payments.

### 6. Display-Only Receipt Number Safety:
- Receipt numbers are non-editable on this screen and are explicitly omitted from `$upd_data` to avoid overwriting actual receipt numbers with formatted text (such as `"-"` or `"null"`).

### 7. Sync Tool Mirroring (`integrationType == 2`):
- Account edits call `syncCustomerRegOnAccEdit()` to update `customer_reg` (`reg_date`, `maturity_date`, `mobile`).
- Payment edits call `syncTransactionOnPaymentEdit()` to update `transaction` (`payment_date`, `rate`, `weight`, `payment_status`, `installment_no`, `due_type`, `client_id`, etc.).

---

## Step 7: Verification & Deployment Checklist

When deploying to a new client codebase:
- [ ] Run SQL schema check for required tables (`scheme_account`, `payment`, `scheme`, `customer`, `payment_mode_details`, `customer_reg`, `transaction`).
- [ ] Verify `routes.php` has `$route['reports/edit_acc_pay'] = 'admin_reports/edit_acc_pay';`.
- [ ] Deploy view `application/views/reports/editable_settings/acc_pay_form.php`.
- [ ] Deploy controller methods in `admin_reports.php` (`edit_acc_pay`, `editAccOrPayments`, `updatePaymentDetails`, `updateAccountDetails`, `generateTransUniqId`).
- [ ] Deploy model methods in `payment_model.php` (`getSchAccByID`, `updatePaymentdata`, `recalculateAccountPaymentDueDates`, `checkTransferTargetAccount`, `syncTransactionOnPaymentEdit`, `syncCustomerRegOnAccEdit`, `updateAccountPaidInstallments`).
- [ ] Deploy JS event handlers in `assets/js/reports.js`.
- [ ] Execute `php -l` linting on modified PHP files.
- [ ] Test multi-row fetching for account IDs (e.g. `101, 102`) and payment IDs (e.g. `501, 502`).
- [ ] Test account transfer validation (open, active, incomplete installments).
- [ ] Test start date edit & maturity date dynamic recalculation.
- [ ] Test payment date edit & metal rate/weight auto-calculation.
- [ ] Test Digi benefit recalculation.
- [ ] Verify execution logs created under `log/accountYYYY-MM-DD.txt` and `log/paymentYYYY-MM-DD.txt`.
