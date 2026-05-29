  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Verify Payment 

            <small>Verify online payments</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="<?php echo site_url('payment/list');?>">Payment</a></li>

            <li class="active">Verify Payment</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

     

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box box-primary">

                <div class="box-header with-border">

                  <h3 class="box-title">Payment List</h3>      

                       

                </div><!-- /.box-header -->

               <!--  <div class="box-header ">

                    <div class="row">

	                  <div class="col-md-12">

	                   <div class="form-group pull-right">

	                  	<label></label>

		                  <div class="input-group">

		                  	<button class="btn btn-primary" id="check_transaction">Verify All</button>

						  </div>

						</div>

	                  </div>

	                  

	                </div>  

                  </div> -->

                <div class="box-body">

                <!-- Alert -->

                   <div class="alert alert-success alert-dismissable" style="display: none;">

	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

	                    <h4><i class="icon fa fa-check"></i> Payment verification!</h4>

	                        <div id="alert_msg" ></div>

	                  </div>
				</br><div class="row">
	                 <div class="col-md-12">
	                 	<div class="col-md-2" id="verify_date" style="display:none;">
	                 		         	 <!-- Date and time range -->
		                  <div class="form-group">
		                    <div class="input-group">
		                         <button class="btn btn-default btn_date_range" id="payment-dt-btn">
							    <span style="display:none;" id="verifypayment_list1"></span>
							    <span style="display:none;" id="verifypayment_list2"></span>
		                        <i class="fa fa-calendar"></i> Date range picker
		                        <i class="fa fa-caret-down"></i>
		                      </button>
		                    </div>
		                 </div><!-- /.form group -->
		               </div>
					   
					   <div class="col-md-2">
					   			<label style="color: red;">Select Gateway&nbsp; * &nbsp;&nbsp;</label>
					   			<select id="gateway_select" class="form-control" style="width:150px;" ></select>
					   			<input id="id_gateway" name="scheme[id_gateway]" type="hidden" value=""/>
					   		</div>	
					   
						
							   <div class="col-md-5">
							   </div>							
							<div class="col-md-5">
						   <?php if($this->session->userdata('branch_settings')==1){?>
							
								<div class="form-group" style=" margin-left: 50px;display:none;" id="branch">
									<label>Select Branch &nbsp;&nbsp;</label>
									<select id="branch_select" class="form-control" style="width:150px;" ></select>
									<input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
								</div>
							<?php }?>
		                    </div>
							<div class="col-md-2">
							  <div class="form-group pull-right">								
								  <div class="input-group">
								    <?php if($access['add']==1){?>
									<button class="btn btn-primary" id="check_transaction">Verify All</button>
									<?php }?>
								  </div>
								</div>
		                    </div>
		                
		                </div>
	            </div></br>

                  <div class="table-responsive">

	                 <table id="payment_verification_list" class="table table-bordered table-striped text-center">

	                    <thead>

	                      <tr>

	                        <th><label class="checkbox-inline"><input type="checkbox" id="sel_failed_all" name="select_all" value="all"/>All</label> ID </th>

	                        <th>Date</th>

	                        <th>Transaction ID</th>  

	                        <th>Customer</th>

	                        <th>A/c Name</th>
	                        <th>Scheme code</th>

	                        <th>Scheme A/c No</th>

	                        <th>Mobile</th>             

	                        <th>Mode</th>                                           

	                        <th>Metal Rate (<?php echo $this->session->userdata('currency_symbol');?>)</th> 

	                        <th>Metal Weight (g)</th>                                           

	                        <th>Charges (<?php echo $this->session->userdata('currency_symbol');?>)</th>   
	                        <th>Amount (<?php echo $this->session->userdata('currency_symbol');?>)</th>   

	                        <th>Ref No</th>                                           

	                        <th>Reason</th>                                           

	                       <!--   <th>Status</th>                                           

	                         <th>Action</th>-->

	                      </tr>

	                    </thead> 



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

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Delete Payment</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this payment?</strong>

      </div>

      <div class="modal-footer">

      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->      

