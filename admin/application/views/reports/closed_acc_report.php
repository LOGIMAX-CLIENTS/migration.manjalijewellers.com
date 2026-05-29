<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Closed Account Report
      <!-- <span id="total_closed_accounts" class="badge bg-aqua"></span> -->
      <span class="badge bg-green" style="font-size: 14px;margin-left: 130px;color:#000;">Amount in : <b class="" id="currency_symbol"></b>
        &nbsp; | &nbsp; Weight in : <b>Gram</b>
      </span>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Masters</a></li>
      <li class="active">Closed Account Report</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content report_sel">
    <div class="row">
      <div class="col-xs-12">

        <div class="box">
          <!-- <div class="box-header">
                  <h3 class="box-title">Closed Account Report</h3>  <span id="total_closed_accounts" class="badge bg-aqua"></span>       
                         
                </div> -->
          <!-- /.box-header -->
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
                <!-- <br> -->

                <?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="close_branch_select">Closed Branch</label><br>
                      <select required id="close_branch_select" class="formcontrol"></select>
                      <input id="close_id_branch" name="scheme[close_id_branch]" type="hidden" value="" />
                    </div>
                  </div>
                <?php } else { ?>
                  <input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">
                  <input type="hidden" id="login_branch_name" value="<?php echo $this->session->userdata('branch_name') ?>">
                <?php } ?>

                <div class="col-md-2">
                  <label for="">
                    <span style="font-weight:bold;" id="date_range"></span>
                  </label>
                  <div class="form-group">
                    <div class="input-group">
                      <button class="btn btn-default btn_date_range" id="rpt_payment_date">
                        <!-- <input id="rpt_payments"  name="rpt_payment" type="hidden" value="" />-->
                        <span style="display:none;" id="rpt_payments1"></span>
                        <span style="display:none;" id="rpt_payments2"></span>
                        <i class="fa fa-calendar"></i> Closed Date
                        <i class="fa fa-caret-down"></i>
                      </button>
                    </div>
                  </div><!-- /.form group -->
                </div>


                <!--  <div class="col-md-2">
										<div class="form-group">
										   <label for="" ><a  data-toggle="tooltip" title="Select employee"> Select Employee </a></label>
											<select id="emp_select" class="form-control"></select>
											<input id="id_employee" name="scheme[id_employee]" type="hidden" value=""/>
										</div>
									</div>-->


                <div class="col-md-2">
                  <div class="form-group">
                    <label for="scheme_select">Scheme </label><br>
                    <select id="scheme_select" class="form-control"></select>
                    <input id="id_schemes" name="id_scheme" type="hidden" value="" />
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="account_type_select">Account Type</label><br>
                    <select id="account_type_select" class="form-control"></select>

                  </div>
                </div>
                <div class="col-md-2 pull-right">
                  <label></label>
                  <div class="form-group">
                    <button type="button" id="closed_acc_search" class="btn btn-info pull-right">Search</button>
                  </div>
                </div>

              </div>
            </div>

            <div class="box box-info stock_details collapsed-box">
              <div class="box-header with-border">
                <h3 class="box-title">Closed Account Summary <span class="summery_description"></span></h3>
                <div class="box-tools pull-right">
                  <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                </div>
              </div>
              <div class="box-body collapse" style="display: none;">
                <div class="row" style="align:center;">

                  <table id="closed_summary_list" class="table table-bordered text-center" style="width:800px;background: #d2d6de;margin:0 auto;">
                    <thead>
                      <tr>
                        <th>Scheme Name</th>
                        <th>Account Count</th>
                        <th>Closed Balance (In Amt)</th>
                        <th>Closed Balance (In Wgt)</th>
                      </tr>
                    </thead>
                    <tbody style="background: #ecf0f5;">
                    </tbody>


                  </table>
                </div>
              </div>

            </div>



            <div class="table-responsive">
              <table id="closed_list" class="table table-bordered table-striped text-center grid" role="grid">
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Scheme A/c No</th>
                    <th>A/c Name</th>
                    <!-- <th>Customer</th>    -->
                    <th>Mobile</th>
                    <th>Joined On</th>
                    <th>Plan Type</th>
                    <th>Paid Installment(s)</th>
                    <th>Amount Paid by Customers</th>
                    <th>Closed Balance (In Amt)</th>
                    <th>Closed Balance (In Wgt)</th>
                    <th>Joined Branch</th>
                    <th>Closed Branch</th>
                    <th>Closed On</th>
                    <th>Pre Close Charge</th>
                    <th>Bonus Amount</th>
                    <!-- <th>Bill No</th>	-->
                    <th>Closed Employee</th>
                    <th>Referred Employee</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr style="font-weight:bold;">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                </tfoot>
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
<div class="modal fade" id="confirm-revert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
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

<!-- modal -->

<div class="modal fade" id="clsd_acc_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header bg-yellow">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel" align="center">Transaction Detail</h4>

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