  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Payment

            <small></small>

          </h1>
 
          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Payment</a></li>

            <li class="active">Online Payment List</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box">

                <div class="box-header">

                  <h3 class="box-title">Online Payment List</h3> <span class="badge bg-green" id="total_payments"></span>

               

                </div><!-- /.box-header -->

                <div class="box-body">

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

	            <?php 

				  $attributes = array('id' => 'pay_status_form');

				//  echo form_open('payment/update_status',$attributes);

				 ?>

                <div class="row">

                	<div class="error-msg">
                	</div>
                	<div class="col-md-5">

                	<div class="btn-group" data-toggle="buttons">

				        <label class="btn btn-success">

				            <input type="radio" name="pay_status" value="1"><i class="icon fa fa-check"></i> Success

				        </label>

				        <label class="btn btn-danger">

				            <input type="radio" name="pay_status" value="3"><i class="icon fa fa-remove"></i> Failed

				        </label>

						 <label class="btn bg-aqua">

				            <input type="radio" name="pay_status" value="6" checked=""><i class="icon fa fa-reply"></i> Refund

				        </label>
						
					 </div>

                </div>
			</div> </br></br>
				
				<div class="row">
	                 <div class="col-md-12">
	                     	       <?php if($this->payment_model->entry_date_settings()==1){?>	
								<div class="col-md-3">
										<div class="form-group" style="margin-left: 50px;">
										   <label>Select Date</label>
											<select id="date_Select" class="form-control" style="width:150px;">
											    <option value="">Select Date by</option></option>
											    <option value=1 selected>Payment Date</option>
											     <option value=2>Entry Date</option>
											</select>
											<input id="id_type" name="scheme[id_type]" type="hidden" value=""/>
										</div>
							    </div>
							    <?php }else{?>
							    <input id="id_type" name="scheme[id_type]" type="hidden" value=""/>
							    <?php }?>
	                 	<div class="col-md-2" style="margin-top:20px">
	                 		         	 <!-- Date and time range -->
		                  <div class="form-group">
		                    <div class="input-group">
		                        <button class="btn btn-default btn_date_range" id="onlinePayment-dt-btn">
							    <span style="display:none;" id="onlinePayment_list1"></span>
							    <span style="display:none;" id="onlinePayment_list2"></span>
		                        <i class="fa fa-calendar"></i> Date range picker
		                        <i class="fa fa-caret-down"></i>
		                      </button>
		                    </div>
		                 </div><!-- /.form group -->
		               </div>
							<div class="col-md-3">
							         <?php if($this->session->userdata('branch_settings')==1){?>				
										<div class="form-group" style="    margin-left: 50px;">
										   <label>Select Branch &nbsp;</label>
											<select id="branch_select" class="form-control" style="width:150px;"></select>
											<input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
										</div>
										
										
							       <?php }?>
							    </div>	
							    
							    	<div class="col-md-3">
										<div class="form-group">
										   <label>Select Settlmts</label>
											<select id="settle_Select" class="form-control" style="width:150px;">
											   <option  selected disabled selected>Select type</option>
											     <option value=1>Settled</option>
											     <option value=2>UnSettled</option>
											</select>
											<input id="id_settled" name="scheme[id_settled]" type="hidden" value=""/>
										</div>
							    </div>
							    
		                 </div>
	                 </div></br>

				  <div class="table-responsive">

                  <table id="online_payments" class="table table-bordered table-striped table-condensed text-center">

                    <thead>

                      <tr>

	                        <th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>

	                        <th>Ref Trans ID</th>  
	                        
	                        <th>Settl Action</th>

	                        <th>Customer</th> 

                          <th>A/c Name</th>

	                        <th>Scheme code</th>

	                        <th>Scheme A/c No</th>
	                        
	                        <th>Total Paid Instal.</th>    <!-- hh -->

	                        <th>Mobile</th>

	                       <?php if($this->payment_model->entry_date_settings()==1){?>	
	                        <th>Entry Date</th>
	                        <?php }else{?>
	                        <th>Payment Date</th>
	                        <?php }?>        

	                        <th>Mode</th>                                           

	                        <th>Metal Rate (<?php echo $this->session->userdata('currency_symbol')?>)</th>  

	                        <th>Metal Weight (g)</th>                                           

	                        <th>Amount (<?php echo $this->session->userdata('currency_symbol')?>)</th> 
	                        
	                       <th>Net Amt (&#8377;)</th>
						   
						   <th>Service Fee (&#8377;)</th>
						   
						   <th>IGST (&#8377;)</th>

	                        <th>Status</th>

	                        <th>Transaction</th>

	                      </tr>

                    </thead>

                    <tbody>

                     

                    

                    </tbody>

                 <tfoot>

                      <tr >

                         <td colspan="10"> <p style="text-align:left"><strong>Note:</strong> Cannot change the status of the approved payment record.</p></td>

                      </tr>

                    </tfoot> 

                  </table>

                  </div>

                </div><!-- /.box-body -->				 <div class="overlay" style="display:none">				 <i class="fa fa-refresh fa-spin"></i>				 </div>
              </div><!-- /.box -->

            </div><!-- /.col -->

          </div><!-- /.row -->

        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->

      







<!-- modal -->      

<div class="modal fade" id="pay_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header bg-yellow">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel" align="center">Transaction Detail</h4>

      </div>

      <div class="modal-body">

    	       

           <div class="trans-det"></div>    

      </div>

      <!--<div class="modal-footer">

      	<div class="col-sm-6 col-sm-offset-3">

          <button type="button" class="btn btn-block btn-warning" data-dismiss="modal">Close</button>

        </div>

      </div>-->

    </div>

  </div>

</div>

<!-- / modal -->       



<style type="text/css">

.popover1{

    width:230px;

    height:330px;    

}

.trans tr{

	 width:50%;

    height:50%;

	font-size:15px;

	

}

</style>