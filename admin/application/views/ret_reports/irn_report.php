  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
          <h1>
              Reports
              <small>IRN Report</small>
          </h1>
          <ol class="breadcrumb">
              <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
              <li><a href="#">Reports</a></li>
              <li class="active">IRN Report</li>
          </ol>
      </section>
      <!-- Main content -->
      <section class="content">
          <div class="row">
              <div class="col-xs-12">
                  <div class="box box-primary">
                      <div class="box-header with-border">
                          <h3 class="box-title">IRN Report</h3> <span id="total_count" class="badge bg-green"></span>
                      </div>
                      <div class="box-body">
                          <div class="row">
                              <div class="col-md-offset-2 col-md-8">
                                  <div class="box box-default">
                                      <div class="box-body">
                                          <div class="row">
                                              <?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>
                                                  <div class="col-md-2">
                                                      <div class="form-group tagged">
                                                          <label>Select Branch</label>
                                                          <select id="branch_select" class="form-control branch_filter"></select>
                                                      </div>
                                                  </div>
                                              <?php } else { ?>
                                                  <input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">
                                              <?php } ?>
                                              <div class="col-md-3">
                                                  <div class="form-group">
                                                      <div class="input-group">
                                                          <br>
                                                          <button class="btn btn-default btn_date_range" id="rpt_payment_date">
                                                              <span style="display:none;" id="rpt_payments1"></span>
                                                              <span style="display:none;" id="rpt_payments2"></span>
                                                              <i class="fa fa-calendar"></i> Date range picker
                                                              <i class="fa fa-caret-down"></i>
                                                          </button>
                                                      </div>
                                                  </div><!-- /.form group -->
                                              </div>
                                              <div class="col-md-2">
                                                  <label>Report Type</label>
                                                  <div class="form-group">
                                                      <select class="form-control" id="irn_rprt_type">
                                                          <option class="form-control" value="1">B2B</option>
                                                          <option class="form-control" value="2">HO B2B</option>
                                                      </select>
                                                  </div>
                                              </div>
                                              <div class="col-md-2">
                                                  <label></label>
                                                  <div class="form-group">
                                                      <button type="button" id="irn_details_search" class="btn btn-info">Search</button>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-xs-12">
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
                              </div>
                          </div>
                          <div class="box box-info stock_details">
                              <div class="box-header with-border">
                                  <h3 class="box-title">IRN Details</h3>
                                  <div class="box-tools pull-right">
                                      <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                                  </div>
                              </div>
                              <div class="box-body">
                                  <div class="row" id="irn_sales_list">
                                      <div class="box-body">
                                          <div class="table-responsive">
                                              <table id="irn_details" class="table table-bordered table-striped text-center">
                                                  <thead>
                                                      <tr style="text-transform:uppercase;">

                                                          <th width="10%">#</th>

                                                          <th width="10%">BILL NO</th>

                                                          <th width="10%">Generate</th>

                                                          <th width="10%">BILL DATE </th>

                                                          <th width="10%">CUSTOMER </th>

                                                          <th width="10%">GST NO</th>

                                                          <th width="10%">PAN</th>

                                                          <th width="10%">STATE</th>

                                                          <th width="10%">PCS</th>

                                                          <th width="10%">GWT</th>

                                                          <th width="10%">NWT</th>

                                                          <th width="10%">DIA WT</th>

                                                          <th width="10%">TAXABLE AMOUNT</th>

                                                          <th width="10%">TAX(%)</th>

                                                          <th width="10%">CGST</th>

                                                          <th width="10%">SGST</th>

                                                          <th width="10%">IGST</th>

                                                          <th width="10%">GST</th>

                                                          <th width="10%">ROUND OFF</th>

                                                          <th width="10%">AMOUNT</th>

                                                          <th width="10%">Image</th>

                                                          <th style="width:50px !important">IRN</th>

                                                          <th width="10%">ACKNOWLEDGEMENT NO</th>

                                                          <th width="15%">IRN REMARKS</th>

                                                      </tr>

                                                  </thead>
                                                  <tbody></tbody>
                                                  <tfoot></tfoot>
                                              </table>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="row" id="irn_pur_ret_list" style="display: none;">
                                      <div class="box-body">
                                          <div class="table-responsive">
                                              <table id="irn_pur_details" class="table table-bordered table-striped text-center">
                                                  <thead>
                                                      <tr style="text-transform:uppercase;">

                                                          <th width="10%">#</th>

                                                          <th width="10%">BILL NO</th>

                                                          <th width="10%">Generate</th>

                                                          <th width="10%">BILL DATE </th>

                                                          <th width="10%">Supplier </th>

                                                          <th width="10%">GST NO</th>

                                                          <th width="10%">PAN</th>

                                                          <th width="10%">STATE</th>

                                                          <th width="10%">PCS</th>

                                                          <th width="10%">GWT</th>

                                                          <th width="10%">NWT</th>

                                                          <th width="10%">DIA WT</th>

                                                          <th width="10%">TAXABLE AMOUNT</th>

                                                          <th width="10%">TAX(%)</th>

                                                          <th width="10%">CGST</th>

                                                          <th width="10%">SGST</th>

                                                          <th width="10%">IGST</th>

                                                          <th width="10%">GST</th>

                                                          <th width="10%">ROUND OFF</th>

                                                          <th width="10%">AMOUNT</th>

                                                          <th width="10%">Image</th>

                                                          <th style="width:50px !important">IRN</th>

                                                          <th width="10%">ACKNOWLEDGEMENT NO</th>

                                                          <th width="15%">IRN REMARKS</th>

                                                      </tr>

                                                  </thead>
                                                  <tbody></tbody>
                                                  <tfoot></tfoot>
                                              </table>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div><!-- /.box-body -->
                      <div class="overlay" style="display:none">
                          <i class="fa fa-refresh fa-spin"></i>
                      </div>
                  </div>
              </div><!-- /.col -->
          </div><!-- /.row -->
      </section><!-- /.content -->
  </div><!-- /.content-wrapper -->
  <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Image Preview</h4>
              </div>
              <div class="modal-body">
                  <img src="" id="imagepreview" style="width: 300px; height: 264px;">
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default danger" data-dismiss="modal">Close</button>
              </div>
          </div>
      </div>
  </div>