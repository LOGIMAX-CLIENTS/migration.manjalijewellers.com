  <?php $branch_user = (($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) ? 0 : 1);?>
  <style>
    .hiddenforbranchuser{
         <?php echo ($branch_user == 1?"display:none;":'');?>
    }
  </style>
  
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Master
        <small>Repair Order</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Master</a></li>
        <li class="active">Repair Order</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Repair Order List</h3> <span id="total_count" class="badge bg-green"></span>
            </div>
            <div class="box-body">
              <p></p>
              <div class="row">
                <div class="col-md-offset-1 col-md-10">
                  <div class="box box-black ">
                    <div class="box-body">
                      <div class="row">
                        <?php 
                         
                        if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label>Select Branch</label>
                              <select id="branch_select" class="form-control ret_branch" style="width:100%;"></select>
                              <input type="hidden" id="branch_filter" value="">
                            </div>
                          </div>
                        <?php } else { ?>
                          <input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">
                          <input type="hidden" id="branch_name" value="<?php echo $this->session->userdata('branch_name') ?>">
                        <?php } ?>

                        <input type="hidden" id="branch_user" value="<?php echo $branch_user ?>">
                        <div class="col-md-2">
                          <div class="form-group">
                            <div class="input-group">
                              <br>
                              <button class="btn btn-default btn_date_range" id="rpt_payment_date">
                                <i class="fa fa-calendar"></i> Date range picker
                                <i class="fa fa-caret-down"></i>
                              </button>
                              <span style="display:none;" id="rpt_payments1"></span>
                              <span style="display:none;" id="rpt_payments2"></span>
                            </div>
                          </div><!-- /.form group -->
                        </div>
                        <div class="col-md-2">
                          <div class="form-group tagged">
                            <label>Order Status</label>
                            <select id="order_status" class="form-control"></select>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <label>Employee</label>
                            <div class="form-group">
                              <select id="employee_sel" class="form-control">
                                <option value=""></option>
                              </select>
                              <input type="hidden" id="id_employee" />
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <label>Repair Type</label>
                            <div class="form-group">
                              <select id="repair_type" class="form-control">
                                <option value="1">Company</option>
                                <option value="2" selected>Customer</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <label>Work Type</label>
                            <div class="form-group">
                              <select id="work_type" class="form-control">
                                <option value="0" selected>All</option>
                                <option value="1">In House</option>
                                <option value="2">Out Source</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <label>Counter</label>
                            <div class="form-group">
                              <select id="counter_sel" class="form-control">
                                                             </select>
                            </div>
                          </div>
                        </div>
                  
                        <div class="col-md-2">
                          <div class="form-group">
                            <label></label>
                            <button type="button" class="btn btn-warning" id="repair_order_search" style="margin-top: 20px;">Search</button>
                          </div>
                        </div>
                        <div class="col-md-2 repair_order_status_btn">
                          <div class="form-group">
                            <label></label>
                            <button type="button" class="btn btn-success" id="repair_order_status" style="margin-top: 20px;">Complete</button>
                          </div>
                        </div>
                        <div class="col-md-2 repair_delivered_btn" style="display:none;">
                          <div class="form-group">
                            <label></label>
                            <button type="button" class="btn btn-success" id="repair_delivered" style="margin-top: 20px;">Delivered</button>
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
              <div class="table-responsive">
                <table id="repair_order_list" class="table table-bordered table-striped text-center">
                  <thead>
                    <tr>
                      <th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all" />All</label></th>
                      <th>Order From</th>
                      <th>Current Branch</th>
                      <th>Order No</th>
                      <?php echo ($branch_user == 0?'<th class="hiddenforbranchuser">PO No</th>':'');?>

                      <th>Tag No</th>
                      <th>Order Date</th>
                      <th>Due Date</th>
                      <th>Customer</th>
                      <th>Bill No</th>

                      <th>Pcs</th>
                      <th>Gross Wt</th>
                      <th>Less Wt</th>
                      <th>Net Wt</th>
                      <th>Product</th>

                      <th>Design</th>
                      <th>Sub Design</th>
                      <th>Image</th>
                      <th>Add Weight</th>
                      <th>Completed Weight</th>

                      <th>Amount</th>
                      <th>Employee</th>
                      <th>Repair Type</th>
                      <th>Work Type</th>
                      <th>Status</th>
                      <?php echo ($branch_user == 0?'
                      <th class="hiddenforbranchuser">Karigar</th>
                      <th class="hiddenforbranchuser">Karigar Mobile</th>
                      <th class="hiddenforbranchuser">Smith Due Date</th>':'');?>
                      <th>Delivery Date</th>
                      <th>Remark</th>

                      <th>Order Taken By</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr style="font-weight:bold">
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <?php echo ($branch_user == 0?'<th></th>':'');?>

                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>

                      <th style="text-align:right"></th>
                      <th style="text-align:right"></th>
                      <th style="text-align:right"></th>
                      <th></th>
                      <th></th>

                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>

                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>

                      <th></th>
                      <th></th>
                      <?php echo ($branch_user == 0?'
                      <th class="hiddenforbranchuser"></th>
                      <th class="hiddenforbranchuser" ></th>
                      <th class="hiddenforbranchuser"></th>':'');?>
                      <!-- <th class="hiddenforbranchuser"></th>
                      <th class="hiddenforbranchuser" ></th>
                      <th class="hiddenforbranchuser"></th> -->

                      <th></th>
                      <th></th>

                     
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div><!-- /.box-body -->
            <div class="overlay" style="display:none">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
          </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
  </div><!-- /.content-wrapper -->
  <!-- modal -->
  <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Delete Order</h4>
        </div>
        <div class="modal-body">
          <strong>Are you sure! You want to delete this Order?</strong>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-danger btn-confirm">Delete</a>
          <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- / modal -->
  <!-- modal -->
  <div class="modal fade" id="confirm-view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Order Details</h4>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
          <input type="hidden" id="id_orderdetails" name="">
          <a href="#" class="btn btn-success btn-confirm" id="reason_submit">Submit</a>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="image-view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Order Images</h4>
        </div>
        <div class="modal-body">
          <div id="imagePreview"></div>
        </div>
      </div>
    </div>
  </div>
  <!-- / modal -->
  <div class="modal fade" id="imageModal_bulk_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width:90%;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel">Image Preview</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div id="order_images" style="margin-top: 2%;"></div>
          </div>
        </div>
        <div class="modal-footer">
          </br>
          <button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="other_metal_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:80%;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel">Add Metal</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col pull-right">
              <button type="button" id="create_stone_item_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
            </div>
          </div>
          <div class="row">
            <input type="hidden" id="custom_active_id" class="custom_active_id" name="" value="" />
            <table id="other_item_details" class="table table-bordered table-striped text-center">
              <thead>
                <tr>
                  <th width="15%">Category</th>
                  <th width="15%">Purity</th>
                  <th width="15%">Product</th>
                  <th width="20%">Wt(G)</th>
                  <th width="20%">V.A(%)</th>
                  <th width="15%">MC</th>
                  <th width="15%">Rate</th>
                  <th width="17%">Amount</th>
                  <th width="10%">Action</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="update_other_metal_details" class="btn btn-success">Save</button>
          <button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
<div class="modal-dialog" style="width:90%;">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Image Preview</h4>
        </div>
        <div class="modal-body">
      <div class="row">
              <div id="order_images" style="margin-top: 2%;"></div>
      </div>
        </div>
        <div class="modal-footer">
            </br>
            <button type="button" id="close_stone_details" class="btn btn-warning"
                data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
</div>
  <script type="text/javascript">
    var Categories = new Array();
    var CategorysArr = new Array();
    CategorysArr = JSON.parse('<?php echo json_encode($categories); ?>');
  </script>