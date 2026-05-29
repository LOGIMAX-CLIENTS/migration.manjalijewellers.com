<?php $branch_user = (($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) ? 0 : 1);?>

  <style type="text/css">
    .ord_img {
      padding: 5px 10px;
      background: #605CA8;
      border: 1px solid #605CA8;
      position: relative;
      color: #fff;
      border-radius: 2px;
      text-align: center;
      float: left;
      cursor: pointer;
    }

    .order_images_new {
      position: absolute;
      z-index: 1000;
      opacity: 0;
      cursor: pointer;
      right: 0;
      top: 0;
      height: 100%;
      font-size: 24px;
      width: 100%;
    }
  </style>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Master
        <small>New Order</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Master</a></li>
        <li class="active">New Order</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">

          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">New Order List</h3> <span id="total_count" class="badge bg-green"></span>

            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-2">
                  <div class="form-group">
                    <label> </label> <br />
                    <button class="btn btn-default btn_date_range" id="account-dt-btn">
                      
                      <i class="fa fa-calendar"></i> Date range picker
                      <i class="fa fa-caret-down"></i>
                    </button>
                    <span style="display:none;" id="new_list1"></span>
                      <span style="display:none;" id="new_list2"></span>
                  </div>
                </div>

                <div class="col-md-2">
                  <div class="form-group">
                    <label>Filter Karigar</label>
                    <select id="karigar_filter" class="form-control"></select>
                    <input type="hidden" id="filter_karigar" name="">
                  </div>
                </div>

                <?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Filter Branch</label>
                      <select id="branch_filter" class="form-control"></select>
                      <input type="hidden" id="filter_branch" name="" value="<?php echo $branch; ?>">
                    </div>
                  </div>
                <?php } else { ?>
                  <input type="hidden" id="id_branch" value="<?php echo $this->session->userdata('id_branch') ?>">
                <?php } ?>

                <div class="col-md-2">
                  <div class="form-group">
                    <label>Employee</label>
                    <div class="form-group">
                      <select id="issue_employee" class="form-control">
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
                    <div class="form-group">
                       <button type="button" class="btn btn-primary" id="search_new_order_list">Search</button>
                    </div>
                  </div>
                </div>




              </div>
              <p></p>
              <div class="row">
                <div class="col-md-offset-2 col-md-8">
                  <div class="box box-black ">
                  <?php if($access['add']==1){ ?>
                    <div class="box-body">
                      <div class="row">
                        <div class="col-md-2">
                          <label>Assign To</label>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <input type="radio" name="order[assign_to]" value="1" checked>Karigar&nbsp;
                            <input type="radio" name="order[assign_to]" value="2">Employee
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <label>Update Order</label>
                        </div>

                        <div class="col-md-3">
                          <div class="form-group" id="karigar_assign">
                            <select id="karigar_sel" class="form-control" style="width:100%;"></select>
                            <input type="hidden" id="karigar" name="">
                          </div>
                          <div class="form-group" id="emp_assign" style="display: none;">
                            <select id="employee_sel" class="form-control" style="width:100%;"></select>
                          </div>
                        </div>

                        <?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>
                          <div class="col-md-3" style="display:none;">
                            <div class="form-group">
                              <select id="select_branch" class="form-control"></select>
                              <input type="hidden" id="id_branch" name="">
                            </div>
                          </div>
                        <?php } else { ?>
                          <input type="hidden" id="id_branch" value="<?php echo $this->session->userdata('id_branch') ?>">
                        <?php } ?>

                        <div class="col-md-4">
                          <div class="form-group">
                            <div class="btn-group" data-toggle="buttons">
                              <label class="btn btn-success" id="approve">
                                <input type="radio" name="upd_status_btn" value="1"><i class="icon fa fa-check"></i> Assign
                              </label>
                              <label class="btn btn-danger" id="reject">
                                <input type="radio" name="upd_status_btn" value="2"><i class="icon fa fa-remove"></i> Reject
                              </label>
                            </div>
                          </div>
                        </div>

                      </div>
                    </div>
                    <?php }?>
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
              <input type="hidden" id="branch_user" value="<?php echo $branch_user ?>">
              <div class="table-responsive">
                <table id="neworder_list" class="table table-bordered table-striped text-center" style="width:100%">
                  <thead>
                    <tr>
                      <th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all" />All</label></th>
                      <th>Image</th>
                      <th>Description</th>
                      <th>Order No</th>
                      <?php echo ($branch_user == 0?'<th class="hiddenforbranchuser">PO No</th>':'');?>
                      <th>karigar Name</th>
                      <th>Karigar Due Date</th>
                      <th>Order Type</th>
                      <th>Customer</th>
                      <th>Customer Mobile</th>
                      <th>Employee</th>
                      <th>Product</th>
                      <th>Design</th>
                      <th>Sub Design</th>
                      <th>Items</th>
                      <th>Weight</th>
                      <th>Tag Code</th>
                      <th>Repair Type</th>
                      <th>Work Type</th>
                      <th>Size</th>
                      <th>Status</th>
                      <th>Ref No</th>
                      <th>Order Date</th>
                      <th>Due Date</th>
                      <th>Branch</th>
                      <th>Remarks</th>
                      <th>Reason</th>
                      <!-- <th>Karigar</th> -->
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th></th>
                      <th></th>
                      <th></th>
                     
                      <?php echo ($branch_user == 0?'<th></th>':'');?>
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


  <!--  Image Upload-->
  <div class="modal fade" id="imageModal_new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:60%;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel">View Image</h4>
        </div>
        <div class="modal-body" style="height: 200px;">
          <div class="ord_img" style="display: none;">
            Add Image
            <input id="order_images_new" class="order_images_new" name="order_images_new" accept="image/*" type="file" multiple="true" style="color: transparent"> 
          </div>
          <br>
          <div id="order_images" style="margin-top: 2%;"></div>
        </div>

        <div class="modal-footer">
          <button type="button" id="update_img_new" class="btn btn-success">Save</button>
          <button type="button" id="close_stone_details_new" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  </div>


  <div class="modal fade" id="order_des_new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Add Description</h4>
        </div>

        <div class="modal-body">

          <div class="row">

            <div class="col-md-10 col-md-offset-1">
              <label for="user_lastname">Item Description</label>
              <div class='form-group'>
                <textarea cols="70" id="description_new" name="description_new"></textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <a href="#" class="btn btn-success" id="add_desc_new">Add</a>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>