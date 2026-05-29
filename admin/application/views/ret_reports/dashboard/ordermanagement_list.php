  <!-- Content Wrapper. Contains page content -->

  <style>

.overlay{
      z-index: 50;
      background: rgba(255,255,255,0.7);
      border-radius: 3px;
}

  </style>

  <div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

      <h1>

        Reports

        <small>Order Management Report</small>

      </h1>

      <ol class="breadcrumb">

        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

        <li><a href="#">Reports</a></li>

        <li class="active">Sales Report</li>

      </ol>

    </section>



    <!-- Main content -->

    <section class="content">

      <div class="row">

        <div class="col-xs-12">



          <div class="box box-primary">

            <div class="box-header with-border">

              <h3 class="box-title">Order Management Report</h3> <span id="total_count" class="badge bg-green"></span>



            </div>

            <div class="box-body">

              <div class="row">

                <div class="col-md-offset-2 col-md-8">

                  <div class="box box-default">

                    <div class="box-body">

                      <div class="row">

                        <!-- <?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>

                        <div class="col-md-2"> 

                            <div class="form-group tagged">

                                <label>Select Branch</label>

                                <select id="branch_select" class="form-control branch_filter"></select>

                            </div> 

                        </div> 

                        <?php } else { ?>

                            <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

                        <?php } ?>  -->



                        <!-- <div class="col-md-2"> 

                            <div class="form-group tagged">

                                <label>Order Status</label>

                                <select id="order_status" class="form-control"></select>

                            </div> 

                        </div>  -->



                        <!-- <div class="col-md-2"> 

                            <div class="form-group tagged">

                                <label>Filter By</label>

                                <select id="filter_by" class="form-control">

                                    <option value="1">Order Date</option>

                                    <option value="2">Customer Delivered Date</option>

                                    <option value="3">Customer Due Date</option>

                                    <option value="4">Karigar Due Date</option>

                                    <option value="5">Customer Over Due Date</option>

                                    <option value="6">Karigar Over Due Date</option>

                                    <option value="7">Karigar Delivered Date</option>

                                </select>

                            </div> 

                        </div>  -->



                        <!-- <div class="col-md-3"> 

                             <div class="form-group">

                                <div class="input-group">

                                    <br>

                                   <button class="btn btn-default btn_date_range" id="rpt_payment_date">

                                    <span  style="display:none;" id="rpt_payments1"></span>

                                    <span  style="display:none;" id="rpt_payments2"></span>

                                    <i class="fa fa-calendar"></i> Date range picker

                                    <i class="fa fa-caret-down"></i>

                                  </button>

                                </div>

                             </div>

                        </div> -->

                        <div class="col-md-3">

                          <div class="form-group tagged">

                            <label>Select Product</label>

                            <select id="prod_select" class="form-control"></select>

                          </div>

                        </div>
                        <!-- </div> -->
                        <div class="row">

                          <div class="col-md-3">

                            <div class="form-group tagged">

                              <label>Select Design</label>

                              <select id="des_select" class="form-control"></select>

                            </div>

                          </div>

                          <div class="col-md-3">

                            <div class="form-group tagged">

                              <label>Select Sub Design</label>

                              <select id="sub_des_select" class="form-control"></select>

                            </div>

                          </div>

                          <!--<div class="col-md-2"> 

                            <label>Select Village</label>

                            <select id="village_select" class="form-control" style="width:100%;"></select>

                        </div>

                        <div class="col-md-2"> 

                            <label>Select Customer</label>

                            <select id="cus_select" class="form-control" style="width:100%;"></select>

                        </div>-->

                          <div class="col-md-2">

                            <label></label>

                            <div class="form-group">

                              <button type="button" id="dash_order_status_search" class="btn btn-info">Search</button>

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

                    <h3 class="box-title">Order Details</h3>

                    <div class="box-tools pull-right">

                      <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

                    </div>

                  </div>

                  <div class="box-body">

                    <div class="row" id="issued">

                      <div class="box-body">

                        <div class="table-responsive">

                          <table id="order_management_report" class="table table-bordered table-striped text-center">

                            <thead>

                              <!-- <tr style="text-transform:uppercase;">

                                <th width="10%">Order No</th>

                                <th width="10%">PO No</th>

                                <th width="10%">Customer</th>

                                <th width="10%">Customer Mobile</th>

                                <th width="10%">Karigar</th>

                                <th width="10%">Karigar Mobile</th>

                                <th width="10%">Order Date</th>

                                <th width="10%">Image</th>

                                <th width="10%">Product</th>

                                <th width="10%">Design</th>

                                <th width="10%">Sub Design</th>

                                <th width="10%">Due Date</th>

                                <th width="10%">Supplier Due Date</th>

                                <th width="10%">Order Status</th>

                                <th width="10%">Tag No</th>

                                <th width="10%">Delivered Date</th>

                                <th width="10%">Bill No</th>

                                <th width="10%">Bill Date</th>

                              </tr> -->

                              <tr style="text-transform:uppercase;">

<th width="10%">Branch</th>

<th width="10%">Order Type</th>

<th width="10%">Order No</th>

<th width="10%">Pur No</th>

<th width="10%">Tag No</th>



<th width="10%">Status</th>

 <th width="10%">Bill No</th>

<th width="10%">Advance Amount</th>

<th width="10%">Adjusted Amount</th>

<th width="10%">Balance Amount</th>



<th width="10%">Karigar</th>

<th width="10%">Order Date</th>

<th width="10%">Delivered Date</th>

<th width="10%">Customer Due Date</th>

<th width="10%">Karigar Due Date</th>



<th width="10%">Customer</th>

<th width="10%">Mobile</th>

<th width="10%">Image</th>

<th width="10%">Product</th>

<th width="10%">Design</th>



<th width="10%">Sub Design</th>

<th width="10%">Pcs</th>

<th width="10%">GWT(g)</th>

<th width="10%">LWT</th>

<th width="10%">NWT</th>



<th width="10%">stone Value</th>

<th width="10%">Wastage %</th>

<th width="10%">Mc</th>

<th width="10%">Assigned By</th>

<th width="10%">Assigned On</th>


<th width="10%">Remarks</th>



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