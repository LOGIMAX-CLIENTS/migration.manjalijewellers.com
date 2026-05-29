<style type="text/css">
  .DTTT_container {
    margin-bottom: 0 !important;
  }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Customer Unpaid report
      <span class="badge bg-green" style="font-size: 14px;margin-left: 230px;color:#000;">Amount in : <b class="" id="currency_symbol"></b>
      </span>
      <small></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Reports</a></li>
      <li class="active">Customer Unpaid Report</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content report_sel">
    <div class="row">
      <div class="col-xs-12">

        <div class="box box-primary">
          <!--  <div class="box-header">
                  <h3 class="box-title">Pending Dues Payment Report</h3>      
                								
                </div> -->
          <div class="box-body">
            <!-- Alert -->
            <?php
            if ($this->session->flashdata('chit_alert')) {
              $message = $this->session->flashdata('chit_alert');
            ?>
              <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
                <?php echo $message['message']; ?>
              </div>

            <?php } ?><br>
            <div class="row">

              <div class="col-md-12">
                <?php if ($this->session->userdata('branch_settings') == 1) { ?>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Joined Branch </label>
                      <select id="branch_select" class="form-control"></select>
                      <input id="id_branch" name="scheme[id_branch]" type="hidden" value="" />
                    </div>
                  </div>
                <?php } ?>
                <input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">
                <input type="hidden" id="login_branch_name" value="<?php echo $this->session->userdata('branch_name') ?>">

                <div class="col-md-2">
                  <span style="font-weight:bold;" id="unpaid_daterange"></span>
                  <div class="form-group">
                    <div class="input-group">
                      <button class="btn btn-default btn_date_range" id="rpt_customer_unpaid">
                        <span style="display:none;" id="rpt_customer_unpaid1"></span>
                        <span style="display:none;" id="rpt_customer_unpaid2"></span>
                        <i class="fa fa-calendar"></i>Start Date
                        <i class="fa fa-caret-down"></i>
                      </button>
                    </div>
                  </div>
                </div>


                <div class="col-md-2">
                  <div class="form-group">
                    <label>Scheme</label>
                    <select id="scheme_select" class="form-control">
                      <option value=0>All</option>
                    </select>
                    <input id="id_schemes" name="id_scheme" type="hidden" value="" />
                  </div>
                </div>

                <div class="col-md-2 pull-right">
                  <label></label>
                  <div class="form-group">
                    <button type="button" id="search_unpaid_list" class="btn btn-info pull-right">Search</button>
                  </div>
                </div>

              </div>


            </div>
            <div class="box box-info stock_details collapsed-box">
              <div class="box-header with-border">
                <h3 class="box-title">Customer Unpaid Report - Summary<span class="summery_description"></span></h3>
                <div class="box-tools pull-right">
                  <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                </div>
              </div>
              <div class="box-body collapse" style="display: none;">
                <div class="row" id="unpaid_payment">
                </div>
                <div class="box-tools pull-right">
                  <button class="btn btn-success btn-sm" id="print_unpaid_summary" style="display:none;"><i class="fa fa-print"></i> Print</button>
                </div>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-bordered table-striped text-center" id="customer_pay_details">
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>ID</th>
                    <th>Cus Id</th>
                    <th>Customer</th>
                    <th>Group Code</th>
                    <th>Scheme Code</th>
                    <th>Account Number</th>
                    <th>Account Name</th>
                    <th>Mobile</th>
                    <th>Joined On</th>
                    <th>Last Paid On</th>
                    <th>Total Paid Amt</th>
                    <th>Total Installment(s)</th>
                    <th>Paid Installment(s)</th>
                    <th>Unpaid Installment(s)</th>
                    <th>Referred Employee</th>
                    <th>Employee</th>
                  </tr>
                </thead>
                <tbody>

                </tbody>

                <tfoot>

                </tfoot>
              </table>
            </div>
          </div><!-- /.box-body -->
        </div><!-- /.box -->
      </div><!-- /.col -->
    </div><!-- /.row -->
  </section><!-- /.content -->
</div><!-- /.content-wrapper -->



<!-- modal -->
<div class="modal fade" id="pay-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Scheme Payment</h4>
      </div>
      <div class="modal-body">
        <div class="fluid-container">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="">Account Name</label>
                <input type="hidden" id="acc_id" name="acc_id" />
                <input type='text' id="acc_name" name="acc_name" readonly="true" class="form-control" />

              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="">Payment Date</label>
                <div class='input-group date'>
                  <input type='text' id='pay_date' name="pay_date" class="form-control myDatePicker" />
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label for="">Amount</label>
              <div class="input-group">
                <span class="input-group-addon">
                  <span class="fa fa-inr"></span>
                </span>
                <input type='text' id="sch_amount" name="sch_amount" readonly="true" class="form-control" />
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label for="">Payment mode</label>
              <select id="pay_mode" name="pay_mode" class="form-control">
                <option value="1">Cash</option>
                <option value="2">Cheque</option>
                <option value="3">Credit Card</option>
                <option value="4">Debit Card</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <label for="">Remark</label>
              <textarea id="pay_remark" name="pay_remark" class="form-control"></textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <a href="#" id="pay_amount" class="btn btn-danger">Pay</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->