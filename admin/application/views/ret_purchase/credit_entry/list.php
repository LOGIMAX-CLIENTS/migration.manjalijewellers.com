<div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

       Credit and Debit Entry   

         <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Masters</a></li>

            <li class="active"> Credit and Debit Entry List</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

              <div class="box box-primary">

                <div class="box-header with-border">

                  <h3 class="box-title"> Credit and Debit Entry List</h3><span id="total_quality" class="badge bg-green"></span>      
                  <?php if($access['add']==1){?>

                  <a class="btn btn-success pull-right" id="add_quality" href="<?php echo base_url('index.php/admin_ret_purchase/credit_debit_entry/add');?>"><i class="fa fa-user-plus"></i> Add </a> 
                  <?php }?>
                </div><!-- /.box-header -->
                <?php 

                	if($this->session->flashdata('chit_alert'))

                	 {

                		$message = $this->session->flashdata('chit_alert');

                   ?>

                       <div  class="alert alert-<?php echo $message['class']; ?> alert-dismissable">

	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

	                    <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>

	                    <?php echo $message['message']; ?>

	                  </div>


                <?php } ?> 
                <div class="box-body">

                <div class="col-md-2">

                <div class="form-group">

                <label></label>
                  <div class="input-group">
                          <button class="btn btn-default btn_date_range" id="rpt_payment_date">
                                  <i class="fa fa-calendar"></i> Date range picker<i class="fa fa-caret-down"></i>
                          </button>
                              <span style="display:none;" id="rpt_payments1"></span>
                              <span style="display:none;" id="rpt_payments2"></span>
                        </div>
                    </div>
                    </div>

                 <div class="col-md-2">
                    <div class="form-group">
                          <label> Type</label>
                             <select id="transcation_type" class="form-control">
                                  <option value="" selected="">All</option>
                                     <option value="1" >Credit</option>
                                       <option value="2">Debit</option>
                               </select>
                            </div>
                          </div>


                          <div class="col-md-2">
                    <div class="form-group">
                          <label>Transcation Type</label>
                             <select id="trans_type" class="form-control">
                                  <option value="" selected="">All</option>
                                  <option value="1" >Supplier</option>
                                  <option value="2">Smith</option>
                                   <option value="3">Approvals</option>
                               </select>
                            </div>
                          </div>

                          <div class="col-md-2">
                     <div class="form-group">
                          <label>Status</label>
                             <select id="status_type" class="form-control">
                                  <option value="" selected="">All</option>
                                     <option value="1" >Success</option>
                                       <option value="2">Cancelled</option>
                               </select>
                            </div>
                          </div>

                          <div class="col-md-2"> 
            							     <label></label>
            						      	<div class="form-group">
            							    <button type="button" id="credit_debit_search" class="btn btn-info">Search</button>   
            						       </div>
            						     </div>

            <div class="row">

            <div class="col-sm-10 col-sm-offset-1">

            <div id="chit_alert"></div>

            

            </div>

            </div>

                  <div class="table-responsive">

                  <table id="trans_list" class="table table-bordered table-striped text-center">

                    <thead>

                      <tr>

                      <th>Trans BillNo</th>

					            <th>Trans Date</th>

                      <th>Supplier</th>

                      <th>Transcation Type</th>

                      <th>Type</th>

                      <th>Ledger Name</th>

                      <th>Amount</th>

                      <th>Weight</th>

					            <th>Narration</th>

                      <th>Status</th>

                      <th>Action</th>

                      </tr>

                 	</thead>

                  <tbody></tbody>
                  <tfoot>
                    <tr style="font-weight:bold;">
                      <td style="text-align:right;"></td>
                      <td style="text-align:right;"></td>  
                      <td style="text-align:right;"></td>
                      <td style="text-align:right;"></td>
                      <td style="text-align:right;"></td>

                      <td style="text-align:right;"></td> 

                      <td style="text-align:right;"></td> 

                      <td style="text-align:right;"></td>
                      <td style="text-align:right;"></td>
                      <td style="text-align:right;"></td>
                      <td style="text-align:right;"></td>

                    </tr>
                  </tfoot>

                 

                  </table>

                  </div> <div class="overlay" style="display:none">

				    <i class="fa fa-refresh fa-spin"></i>

			 	</div>

                </div><!-- /.box-body -->

              </div><!-- /.box -->

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

        <h4 class="modal-title" id="myModalLabel">Delete Credit/</h4>

      </div>

      <div class="modal-body">

      <strong>Are you sure! You want to delete this Diamond Quality?</strong>

      </div>

      <div class="modal-footer">

      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal --> 

<div class="modal fade" id="confirm-creditcancell" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="cancelModalLabel">Cancel Credit/Debit Entry</h4>
      </div>
      <div class="modal-body">
        <strong>Are you sure you want to cancel this Credit/Debit Entry?</strong>
        <p></p>
        <div class="row">
          <div class="col-md-12">
            <label>Remarks<span class="error">*</span></label>
            <input type="hidden" id="crdrid" name="">
            <textarea class="form-control" id="credit_cancel_remark" placeholder="Enter Remarks" rows="5" cols="10"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-danger" type="button" id="crdr_cancel" disabled>Cancel Entry</button>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div> 
<!-- / modal -->      