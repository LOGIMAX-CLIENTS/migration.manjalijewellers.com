<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

           Master

            <small>Purchase return list</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Purchase Return</a></li>

            <li class="active">Entry</li>

          </ol>

        </section>


        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               

               <div class="box box-primary">

			    <div class="box-header with-border">

                  <h3 class="box-title">Purchase Return Entry List</h3><span id="total_count" class="badge bg-green"></span>  

                  <div class="pull-right">

				  <?php if($access['add']==1){?>

                  	 <a class="btn btn-success pull-right" id="add_Order" href="<?php echo base_url('index.php/admin_ret_purchase/purchasereturn/add');?>" ><i class="fa fa-plus-circle"></i> Add</a> 

					   <?php }?>
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

				    <div class="col-md-12">



                <div class="col-md-2">

                                <div class="form-group">

								<label>  </label>

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

											    <label>Stock Type</label>

										    	<select id="stock_type" class="form-control">

												
													<option value="3" selected>All</option>
												<option value="0" >Purchase</option>

												<option value="1">Sales</option>
												<option value="2">Sales Return</option>

										   	</select>

									  	  </div>

								        </div>

										<div class="col-md-2"> 

									<label>Purchase Type</label>

									<select id="purchase_type" class="form-control">

										 <option value="0">All</option>

									     <option value="1">Stock</option>

									     <option value="2">Walk-in</option>

									     <option value="3">Expo</option>

									</select>

								</div>


									<div class="col-md-2"> 
									<label>Transcation Type</label>
									<select id="transcation_type" class="form-control" style="width:100%;">
										 <option value="0" selected="">All</option>
									    <option value="1">Supplier</option>
									     <option value="2">Manufaucturers</option>
									     <option value="3">Approval Ledger</option>
									     <option value="4">Stone Supplier</option>
										 <option value="5">Abstract</option>

									</select>
								
								</div>	

                        



            				         <div class="col-md-2">

                	                 <div class="form-group">

                	                  <label>PO Ref No</label>

                					 <select class="form-control" id="select_po_ref_no" ></select>

                	                  </div> 

            				            </div> 

                                     <div class="col-md-2">

                	                     <div class="form-group">

                	                       <label>Select Karigar<span class="error">*</span></label>

                							        <select class="form-control" id="select_karigar">

            								        	</select>

                	                     </div> 

            				                 </div> 	   
            				        </div> 	   



        				        </div>

								<!-- NEW ROW FOR SEARCH BUTTON -->
								<div class="row" style="margin-top:10px;">
									<div class="col-md-12">

										<div class="col-md-2 pull-left">
											<button type="button" id="purchase_return_search" 
													class="btn btn-primary btn-sm">
												Search
											</button>
										</div>

									</div>
								</div>
							

				           </div>   

			       	</div>            

					               

					                 

              <br>

                  <div class="table-responsive">

	                 <table id="pur_return_list" class="table table-bordered table-striped text-center">

	                    <thead>

	                      <tr>

	                        <th>ID</th>

	                        <th>Karigar</th>

	                        <th>Ref No</th>

	                        <th>Date</th>  

	                        <th>Return Pcs</th>  

	                        <th>Return Weight</th> 

                            <th>Stock Type</th> 

                            <th>Purchase Type</th> 

							<th>Transaction Type</th> 

                            <th>Order No</th>  

	                        <th>Status</th>  

	                        <th>Reason</th>  

	                        <th>Action</th>  

	                      </tr>

	                    </thead> 

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

        <h4 class="modal-title" id="myModalLabel">Cancel Purchase Return</h4>

        <input type="hidden" id="pur_return_id">
		<input type="hidden" id="tag_issue_from">
		<input type="hidden" id="nontag_issue_from">

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to Cancel this Entry?</strong>

      </div>

      

      <div class="col-md-12 bill_remarks">

        <label>Remarks<span class="error">*</span></label>

        <textarea class="form-control" id="ret_cancel_remark" placeholder="Enter Remarks"  rows="5" cols="10"> </textarea>

     </div>

                

      <div class="modal-footer">

      	<button type="button" id="ret_cancel" class="btn btn-danger btn-confirm" disabled>Cancel</button>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->    