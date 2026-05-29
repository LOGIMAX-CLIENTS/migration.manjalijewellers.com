<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Closed Scheme Account List
      <span id="total_closed_accounts" class="badge bg-aqua"></span>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Masters</a></li>
      <li class="active">Closed Scheme Account</li>
    </ol>
  </section>
  <!-- Main content -->
  <section class="content">
    <input type="hidden" name="" id="old_sch_acc_num"
      value="<?php echo $this->session->userdata('enable_old_sch_acc_no'); ?>">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <!-- <div class="box-header">
                  <h3 class="box-title">Closed Scheme Account List</h3>  <span id="total_closed_accounts" class="badge bg-aqua"></span>       
                </div>--><!-- /.box-header -->
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
            <?php } ?>
            <div class="row">
              <div class="col-md-12">
                <div class="col-md-2">
                  <div class="pull-left">
                    <label for="Show date"> <span id="closed_list_date"></span></label>
                    <div class="form-group">
                      <button class="btn btn-default btn_date_range pull-right" id="closed-acc-dt-btn">
                        <span style="display:none;" id="closed_list1"></span>
                        <span style="display:none;" id="closed_list2"></span>
                        <i class="fa fa-calendar"></i> Closed Date
                        <i class="fa fa-caret-down"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for=""><a data-toggle="tooltip" title="Select employee"> Select Employee </a></label>
                    <select id="emp_select" class="form-control"></select>
                    <input id="id_employee" name="scheme[id_employee]" type="hidden" value="" />
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <div class="form-group">
                      <label>Filter By Scheme</label>
                      <select id="scheme_select" class="form-control" style="width:150px;"></select>
                      <input id="id_scheme" name="scheme[id_scheme]" type="hidden" value="" />
                    </div>
                  </div>
                </div>
                <!-- <div class="col-md-2" id="scheme_group">
                                    <div class="form-group">
                                     <div class="form-group">
                                      <label>Filter By Group</label>
                                      <select id="group_select" class="form-control" style="width:150px;"></select>
                                      <input id="group_id" name="scheme[group_id]" type="hidden" value=""/>
                                    </div> 
                                       </div> 
                                  </div>-->
                <?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for=""><a data-toggle="tooltip"> Select A/c close Branch </a>
                        <span class="error">*</span></label>
                      <select required id="close_branch_select" class="form-control"></select>
                      <input id="close_id_branch" name="scheme[close_id_branch]" type="hidden" value="" />
                    </div>
                  </div>
                <?php } else { ?>
                  <input id="close_id_branch" name="scheme[close_id_branch]" type="hidden"
                    value="<?php echo $this->session->userdata('id_branch') ?>" />
                <?php } ?>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for=""><a data-toggle="tooltip"> Is Utilized </a>
                      <span class="error">*</span></label>
                    <select required id="is_utilized" class="form-control">
                      <option value="">--Select--</option>
                      <option value="1">Yes</option>
                      <option value="0">No</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="table-responsive">
              <table id="closed_list" class="table table-bordered table-striped text-center grid" role="grid">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <?php if ($this->session->userdata('enable_old_sch_acc_no') == 1) { ?>
                      <th>Old A/c No</th>
                    <?php } ?>
                    <th>Mobile</th>
                    <th>A/c Name</th>
                    <th>Scheme A/c No</th>
                    <th>Code</th>
                    <th>Group Code</th>
                    <th>Start Date</th>
                    <th>Type</th>
                    <th>Payable (<?php echo $this->session->userdata('currency_symbol') ?>)</th>
                    <th>Closed Employee</th>
                    <th>Home Branch</th>
                    <th>Closed Branch</th>
                    <th>Closed Date</th>
                    <!-- <th>Total Payment Amount</th> -->
                    <th>Paid Installments</th>
                    <th>Amount Paid by Customers</th>
                    <th>Closing Amount (<?php echo $this->session->userdata('currency_symbol') ?>)</th>
                    <th>Closing Weight (In GMS)</th>
                    <th>Closing balance</th>
                    <th>Total Saved Benefits</th>
                    <th>Other Benefit Weight (In GMS)</th>
                    <th>General Advance Paid</th>
                    <th>General Advance Benefits</th>
                    <th>Gift Status</th>
                    <th>Deductions</th>
                    <th>Discount</th>
                    <th>Is Utilized</th>
                    <th>Booking Amt (<?= $this->session->userdata('currency_symbol'); ?>)</th>
                    <th>Booking WGT (g)</th>
                    <th>Booking Rate (<?= $this->session->userdata('currency_symbol'); ?>)</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <!--   <tfoot>
                      <tr>
                      </tr>
                    </tfoot> -->
              </table>
            </div>
          </div><!-- /.box-body -->
          <div class="overlay" style="display:none">
            <i class="fa fa-refresh fa-spin"></i>
          </div>
        </div><!-- /.box -->
      </div><!-- /.col -->
    </div><!-- /.row -->
  </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<!-- modal -->
<div class="modal fade" id="confirm-revert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
            class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Revert Closed Scheme</h4>
      </div>
      <div class="modal-body">
        <strong>Are you sure! You want to revert this scheme account?</strong>
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-danger btn-confirm">Revert</a>
        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->
<div class="modal fade" id="utilized" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
            class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Revert Closed Scheme</h4>
      </div>
      <div class="modal-body  text-center">
        <strong>This account is already Utilized...</strong>
      </div>
      <div class="modal-footer" style="text-align: center;">
        <!-- <a href="#" class="btn btn-danger btn-confirm">Revert</a> -->
        <a href="#" data-dismiss="modal">OK</a>
      </div>
    </div>
  </div>
</div>
<!-- modal -->
<div class="modal fade" id="clsd_acc_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-yellow">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
            class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel" align="center">Closed A/c Details</h4>
      </div>
      <div class="modal-body">
        <div class="closed_acc_detail"></div>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->
<style type="text/css">
  .popover1 {
    width: 230px;
    height: 330px;
  }

  .trans tr {
    width: 50%;
    height: 50%;
    font-size: 15px;
  }
</style>