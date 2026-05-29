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
            <li><a href="#">payment</a></li>
            <li class="active">Insert Transaction Records</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content"> 

          <!-- Default box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Insert Transaction Records</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
             <!-- form container -->
              <div>
	             <!-- form -->
				<?php echo form_open_multipart('payment/insert_transaction_records/save') ?>
				
                   <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Client ID</label>
                       <div class="col-md-4">
                       	<input type="text" required class="form-control" id="client_id" name="instran[client_id]" value="<?php echo set_value('instran[client_id]',$instran['client_id']); ?>"/>
                        <p class="help-block"></p>
                      	</div>                       	
                       </div>
                    </div>
				 </div>
			 				
						<div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Payment Date</label>
                       <div class="col-md-4">
                       	<input type="date" required class="form-control" id="payment_date" name="instran[payment_date]" value="<?php echo set_value('instran[payment_date]',$instran['payment_date']); ?>"/>
                        <p class="help-block"></p>
                        
							</div>                       	
                       </div>
                    </div>
					<div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Amount</label>
                       <div class="col-md-4">
                       	<input type="text" required class="form-control input_currency" id="amount" name="instran[amount]" value="<?php echo set_value('instran[amount]',$instran['amount']); ?>"/>
                        <p class="help-block"></p>
                        
							</div>                       	
                       </div>
                    </div>
					
					<div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Weight</label>
                       <div class="col-md-4">
                       	<input type="text" class="form-control input_number" required id="ins_Weight" name="instran[weight]" value="<?php echo set_value('instran[weight]',$instran['weight']); ?>"/>
                        <p class="help-block"></p>
                      
							</div>                       	
                       </div>
                    </div>
					<div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Rate</label>
                       <div class="col-md-4">
                       	<input type="text" class="form-control input_currency" required id="rate" name="instran[rate]" value="<?php echo set_value('instran[rate]',$instran['rate']); ?>"/>
                        <p class="help-block"></p>
                        	</div>                       	
                       </div>
                    </div>
					<div class="row">
				 	<div class="form-group">
                     <!--  <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Metal</label>-->
                       <div class="col-md-4">
                       	<input type="hidden" class="form-control input_currency" required id="metal" name="instran[metal]" value="<?php echo set_value('instran[metal]',$instran['metal']);  ?>" />
                        <p class="help-block"></p>
                        </div>                       	
                       </div>
                    </div>
				 <div class="row">
				 	<div class="form-group">
              			<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Payment Mode</label>
						 <div class="col-md-4" >
         			
              			<select class="form-control" required  id="pay_mode" name="instran[payment_mode]" ></select>
						<input type="hidden"  class="form-control" id="paymode"  value="<?php echo set_value('instran[payment_mode]',$instran['payment_mode']); ?>" />
						<p class="help-block"></p>
              		</div>
              		</div>
                    </div>
					<div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Reference No.</label>
                       <div class="col-md-4">
                       	<input type="text" required class="form-control " id="ref_no" name="instran[ref_no]" value="<?php echo set_value('instran[ref_no]',$instran['ref_no']); ?>"/>
                        <p class="help-block"></p>
                   
							</div>                       	
                       </div>
                    </div>
					
					<div class="row">
					<div class="form-group">
					   <label for="Offer" class="col-md-3 col-md-offset-1  " >New Customer</label>
					   <div class="col-md-6" >
						 <div class="col-md-2">
								<input type="radio" id="select_options" required  name="instran[active]" value="Y"  <?php if($instran['new_customer']==Y) { ?> checked="true" <?php } ?>  > Yes
						</div>
						<div class="col-md-2">							
							<input type="radio" id="select_options"  name="instran[active]" value="N"  <?php if($instran['new_customer']==N) { ?> checked="true" <?php } ?>> No							
						</div>						
						<p class="help-block"></p>
						
					   </div>
					</div>
				</div>
				<p class="help-block"></p>
				<div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Discount Amount</label>
                       <div class="col-md-4">
                       	<input type="text"  required class="form-control input_currency" id="discountAmt" name="instran[discountAmt]" value="<?php echo set_value('instran[discountAmt]',$instran['discountAmt']); ?>"/>
                        <p class="help-block"></p>
                       
							</div>                       	
                       </div>
                    </div>
					
					
					<div class="row">
					<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('is_branchwise_cus_reg')==1  ){?> 								

											  <div class="form-group" >
												  <label for="chargeseme_name" class="col-md-3 col-md-offset-1 " > Select Branch	 </label>
												  <div class="col-md-4">
												  <select   id="branch_select" required class="form-control">
												  <input id="id_branch"   name="instran[id_branch]" type="hidden" value="<?php echo set_value('instran[id_branch]',$instran['id_branch']);?>"  />	</select>
												<p class="help-block"></p>												  
											  </div>													
										  </div>					
										  </div>					
										 				
								  <?php }?>
								   
										
					<div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Payment Status</label>
                         <div class="col-md-4">
                          <select class="form-control" required name="instran[payment_status]" id="payment_status"></select>



	              	  	<input type="hidden"  class="form-control" id="pay_status"  value="<?php echo set_value('instran[payment_status]',$instran['payment_status']); ?>" />
									<p class="help-block"></p>
						
						</div>                       	
                    </div>
                    </div>
					
									
				<div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Recepit No.</label>
                       <div class="col-md-4">
                       	<input type="text" class="form-control input_number" id="receipt_no" required="true" name="instran[receipt_no]" value="<?php echo set_value('instran[receipt_no]',$instran['receipt_no']); ?>"/>
                        <p class="help-block"></p>
                       
							</div>                       	
                       </div>
                    </div>
					
					<div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Installment No.</label>
                       <div class="col-md-4">
                       	<input type="text" class="form-control input_number" id="installment_no" required name="instran[installment_no]" value="<?php echo set_value('instran[installment_no]',$instran['installment_no']); ?>"/>
                        <p class="help-block"></p>
                       
							</div>                       	
                       </div>
                    </div>
					
					<div class="row">
					<div class="form-group">
					   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Remarks</label>
					   <div class="col-md-4">
						 <textarea class="form-control" id="description" name="instran[remarks]"><?php echo set_value('instran[remarks]',$instran['remarks']); ?></textarea>			   
						<p class="help-block"></p>
						
					   </div>
					</div>
				 </div>
						<div class="row">
				 	<div class="form-group">
                       <!--<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Transfer Date</label>-->
                       <div class="col-md-4">
                       	<input type="hidden" class="form-control" id="transfer_date" name="transfer_date" value="<?php echo set_value('instran[transfer_date]',$instran['transfer_date']);?>" />
                        <p class="help-block"></p>
                        
							</div>                       	
                       </div>
                    </div>	
				 
			    
	            </div>
	            
				<br /> 
			     <div class="row"> 
					  <div class="col-xs-offset-5" style="margin-bottom:10px">
						<button type="submit"  class="btn btn-primary">Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
						
					  </div> 
				  </div>  
	          <?php echo form_close();?>
	             <!-- /form -->
	          </div>
             <!-- /form container -->
            </div><!-- /.box-body -->
             <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
