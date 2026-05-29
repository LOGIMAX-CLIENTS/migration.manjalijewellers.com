  <!-- Content Wrapper. Contains page content -->



  <div class="content-wrapper">



<!-- Content Header (Page header) -->



<section class="content-header">



  <h1>



   Lot



    <small>Manage your Lot(s)</small>



  </h1>



  <ol class="breadcrumb">



    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>



    <li><a href="#">Physical Stock </a></li>



    



  </ol>



</section>



<!-- Main content -->



<section class="content">



  <div class="row">



    <div class="col-xs-12">



       <div class="box box-primary">



        <div class="box-header with-border">



          <h3 class="box-title">Physical Stock Entry</h3>  <span id="total_product" class="badge bg-green"></span>



          <div class="pull-right">



               <a class="btn btn-success pull-right" id="add_pcs" href="<?php echo base_url('index.php/admin_ret_catalog/employee_stock_product/add');?>" ><i class="fa fa-plus-circle"></i> Add</a>



          </div>



        </div>



         <div class="box-body">



           <div class="row">



                <div class="col-xs-12">



                <!-- Alert -->



                <?php



                    if($this->session->flashdata('chit_alert'))



                     {



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



           <div class="row">



               <div class="form-group">



                  <div class="col-md-2">



                    <div class="pull-left">



                        <div class="form-group">



                        <button class="btn btn-default btn_date_range" id="ltInward-dt-btn">



                        <span  style="display:none;" id="lt_date1"></span>



                        <span  style="display:none;" id="lt_date2"></span>



                        <i class="fa fa-calendar"></i> Date range picker



                        <i class="fa fa-caret-down"></i>



                        </button>



                        </div>



                    </div>



                  </div>







                  <div class="col-md-2">



                    <div class="form-group">



                    <select id="metal" style="width:100%;"></select>



                    </div>



                </div>







                <div class="col-md-2">



                    <select id="select_emp"  style="width:100%"></select>



                </div>







                <div class="col-md-2">



                    <div class="form-group">



                    <button type="button" id="lot_inward_search" class="btn btn-info"><i class="fa fa-search"></i></button>



                    </div>



                </div>



                <!-- <div class="col-md-2">

                    <div class="form-group">

                    <button type="button" id="lot_closed" class="btn btn-success">Completed</i></button>

                    </div>

                </div> -->







                </div>



            </div>



          <div class="table-responsive">



             <table id="employee_product_stock_list" class="table table-bordered table-striped text-center">



                <thead>



                 <tr>



                    <th >Employee</th>


                    <th >Branch</th>

                    <th >Date</th>



                    <th style="cursor:pointer;color:#3c8dbc;" title="Click on pcs count to view employee-wise entries">Pcs <i class="fa fa-info-circle"></i></th>



                 

                    <!-- <th width="5%">Category</th>



                    <th width="3%">karigar</th>



                    <th width="3%">Employee</th>



                    <th width="5%">Recd Pcs</th>



                    <th width="5%">Recd Wt</th>



                    <th width="5%">Tagged Pcs</th>



                    <th width="5%">Tagged Wt</th>



                    <th width="1%"></th>



                    <th width="1%">Pur Details</th>



                    <th width="5%">Blc Pcs</th>



                    <th width="5%">Blc Wt</th>



                    <th width="15%">Action</th>

 -->

                  </tr>



                </thead>



                <tfoot>



                    <tr style="font-weight: bold; color:red">



                    <td></td>   <td></td><td></td><td></td><td></td><td></td><td></td><td style="text-align:right;"></td><td style="text-align:right;"></td><td style="text-align:right;"></td><td style="text-align:right;"></td><td style="text-align:right;"></td><td style="text-align:right;"></td><td style="text-align:right;"></td><td style="text-align:right;"></td><td></td>



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

<!-- Entry Log Modal -->
<div class="modal fade" id="entry_log_modal" tabindex="-1" role="dialog" aria-labelledby="entryLogLabel">
  <div class="modal-dialog" style="width:65%;" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background:#3c8dbc;color:#fff;">
        <button type="button" class="close" data-dismiss="modal" style="color:#fff;"><span>&times;</span></button>
        <h4 class="modal-title" id="entryLogLabel"><i class="fa fa-list"></i> Employee Entry Log</h4>
      </div>
      <div class="modal-body">
        <p id="entry_log_product_info" class="text-muted" style="margin-bottom:10px;"></p>
        <div class="table-responsive">
          <table id="entry_log_table" class="table table-bordered table-striped text-center">
            <thead style="background:#3c8dbc;color:#fff;">
              <tr>
                <th>#</th>
                <th>Employee</th>
                <th>Branch</th>
                <th>Pcs / Weight</th>
                <th>Type</th>
                <th>Date &amp; Time</th>
              </tr>
            </thead>
            <tbody id="entry_log_tbody">
              <tr><td colspan="6">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div><!-- /entry_log_modal -->

<!-- Hidden inputs for entry log AJAX -->
<input type="hidden" id="log_id_branch" value="">



<!-- modal -->



<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">



<div class="modal-dialog">



<div class="modal-content">



<div class="modal-header">



<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>



<h4 class="modal-title" id="myModalLabel">Delete Product</h4>



</div>



<div class="modal-body">



       <strong>Are you sure! You want to delete this Product?</strong>



</div>



<div class="modal-footer">



  <a href="#" class="btn btn-danger btn-confirm" >Delete</a>



<button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>



</div>



</div>



</div>



</div>



<!-- / modal -->







<div class="modal fade" id="purchase_details"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">



<div class="modal-dialog" style="width:95%;">



<div class="modal-content">



    <div class="modal-header">



        <h4 class="modal-title" id="myModalLabel">LOT PURCHASE DETAILS</h4>



    </div>



    <div class="modal-body">







        <div class="row">



            <input type="hidden" id="" value="0">



            <table id="lot_pur_details" class="table table-bordered table-striped text-center">



            <thead>



            <tr>



            <th>Id</th>



            <th>Product</th>



            <th>Design</th>



            <th>Sub Design</th>



            <th>Purchase Wastage</th>



            <th>Purchase MC Type</th>



            <th>Purchase MC</th>



            <th>Purchase Type</th>



            <th>Purchase Touch</th>



            <th>Purchase Rate</th>





            </tr>



            </thead>



            <tbody>



            </tbody>



            <tfoot>



            <!-- <tr style="font-weight:bold;font-size:15px">

                <td>Total:</td>

                <td></td>

                <td></td>

                <td></td>

                <td class="stn_tot_pcs"></td>

                <td class="stn_tot_weight"></td>

                <td></td>

                <td></td>

                <td></td>

                <td></td>

                <td></td>

                <td></td>

                <td class="stn_tot_amount"></td>

                <td></td>

            </tr> -->



            </tfoot>



            </table>



    </div>



  </div>



  <div class="modal-footer">



    <button type="button" id="update_stone_details" class="btn btn-success">Save</button>



    <button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>



  </div>



</div>



</div>



</div>