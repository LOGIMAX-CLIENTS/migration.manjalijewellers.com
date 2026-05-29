      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Post-Dated Payment
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Post Dated Payment</a></li>
            <li class="active">Edit</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
     
          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Post-Dated Payment - Edit</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="">
              	 <?php echo  form_open('postdated/payment_entry/save/'.$payment['id_post_payment']); ?>
		
				 <div class="row">
				 
				 <div class="col-sm-2 col-md-offset-1">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Date</label>
                      
                       	 <input type="text" class="form-control" id="scheme_acc_number"  value="<?php echo set_value('payment[date_payment]',$payment['date_payment']); ?>" readonly="true" > 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
                    
			        <div class="col-sm-2">
				 	  <div class="form-group">
                       <label for="chargeseme_name" >Mode</label>
                     
                         <input type="text" class="form-control"  id="pay_mode" value="<?php echo set_value('payment[pay_mode]',$payment['pay_mode']); ?>" readonly="true" />
               
                        <p class="help-block"></p>
                       	
                       </div>
                    </div>
                    
                  <div class="col-sm-2 col-md-offset-1">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Cheque No</label>
                         
                       	 <input type="text" class="form-control" id="cheque_no"  value="<?php echo set_value('payment[cheque_no]',$payment['cheque_no']); ?>" readonly="true" > 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>            
                    
                 <div class="col-sm-2">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Amount</label>
                          <div class="input-group ">
              				<span class="input-group-addon input-sm" ><?php echo $this->session->userdata('currency_symbol')?></span>
                       	 <input type="text" class="form-control"   value="<?php echo set_value('payment[amount]',$payment['amount']); ?>" readonly="true" > 
                       	 <input type="hidden" class="form-control"  value="<?php echo set_value('payment[weight]',$payment['weight']); ?>" /> 
                       	 <input type="hidden" class="form-control"  value="<?php echo set_value('payment[metal_rate]',$payment['metal_rate']); ?>" /> 
                  <p class="help-block"></p>
                       	
                       </div>
                       </div>
                    </div>
                  
                    <div class="col-sm-4 col-md-offset-1">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Scheme A/c No</label>
                      
                       	 <input type="text" class="form-control" id="scheme_acc_number"  value="<?php echo set_value('payment[scheme_acc_number]',$payment['scheme_acc_number']); ?>" readonly="true" > 
                       	 <input type="hidden" class="form-control" name="payment[id_scheme_account]" value="<?php echo $payment['id_scheme_account']; ?>"> 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
                    
			        <div class="col-sm-4 col-md-offset-1">
				 	  <div class="form-group">
                       <label for="chargeseme_name" >A/c Name</label>
                     
                         <input type="text" class="form-control"  id="short_code" value="<?php echo set_value('payment[account_name]',$payment['account_name']); ?>" readonly="true" />
               
                        <p class="help-block"></p>
                       	
                       </div>
                    </div>         
                    
                 <div class="col-sm-4 col-md-offset-1">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Drawee A/c No</label>
                      
                       	 <input type="text" class="form-control" id="drawee"  value="<?php echo set_value('payment[drawee_acc_no]',$payment['drawee_acc_no']); ?>" readonly="true" > 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
                    
			        <div class="col-sm-4 col-md-offset-1">
				 	  <div class="form-group">
                       <label for="chargeseme_name" >Drawee A/c Name</label>
                     
                         <input type="text" class="form-control"  id="drawee_account_name" value="<?php echo set_value('payment[drawee_account_name]',$payment['drawee_account_name']); ?>" readonly="true" />
               
                        <p class="help-block"></p>
                       	
                       </div>
                    </div>
                    
			   
				 
				 <div class="col-sm-4 col-md-offset-1">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Drawee Bank</label>
                      
                       	 <input type="text" class="form-control" id="drawee_bank"  value="<?php echo set_value('payment[drawee_bank]',$payment['drawee_bank']); ?>" readonly="true" > 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
                    
			        <div class="col-sm-4 col-md-offset-1">
				 	  <div class="form-group">
                       <label for="chargeseme_name" >Drawee IFSC</label>
                     
                         <input type="text" class="form-control"  id="drawee_ifsc" value="<?php echo set_value('payment[drawee_ifsc]',$payment['drawee_ifsc']); ?>" readonly="true" />
               
                        <p class="help-block"></p>
                       	
                       </div>
                    </div>	
                 <div class="col-md-2 col-md-offset-1">			
					 	<div class="form-group">
	                       <label for="">Date Submitted</label>
             	    		<div class='input-group date'>
				                    <input type='text' class="form-control  datemask myDatePicker" name="payment[date_presented]"  id='pay_date' data-inputmask="'alias': 'dd/mm/yyyy'" data-mask  data-date-format="dd/mm/yyyy" value="<?php echo set_value('payment[date_presented',$payment['date_presented']); ?>" />
				                    <span class="input-group-addon">
				                        <span class="glyphicon glyphicon-calendar"></span>
				                    </span>
				                    
				                </div>
	                    </div>	
                    </div>
                    
                   <div class="col-sm-2">
				 	  <div class="form-group">
                       <label for="chargeseme_name" >Charges</label>
                      <div class="input-group ">
              				<span class="input-group-addon input-sm" ><?php echo $this->session->userdata('currency_symbol')?></span>
                         <input type="text" class="form-control input_currency" name="payment[charges]"  id="charges" value="<?php echo set_value('payment[charges]',$payment['charges']); ?>" />
               
                        <p class="help-block"></p>
                       	
                       </div>
                       </div>
                    </div>
                     
				 <div class="col-sm-4 col-md-offset-1">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Payment Status</label>
                               <input type="hidden" class="form-control"  id="payment_status"  name="payment_status"  value="<?php echo set_value('payment_status',$payment['id_payment_status']); ?>" readonly="true" />
                       	 <select id="ppayment_status"  name="payment[payment_status]" class="form-control"></select> 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
                    
			     
                    
				 </div>	
 
	
				
				<br/>      
				 <div class="row col-xs-12">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						<button type="submit" class="btn btn-primary">Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
						
					  </div> <br/>
					</div>
				  </div>      
				        	
               </form>              	              	
              </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
              
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
         

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->