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
            <li class="active">Edit</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
     
          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Payment - Edit</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="">
              	
		<form id="payment_form">
				 <div class="row">
				 
				 <div class="col-md-4 col-md-offset-1">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Date</label>
                      
                       	 <input type="text" class="form-control input-sm" id="scheme_acc_number" name="pay[date_payment]"  value="<?php echo set_value('pay[date_payment]',$pay['date_payment']); ?>" readonly="true" > 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
					
					
					</div>
					
					<div class="row">
                    
			        <div class="col-md-4 col-md-offset-1">
				 	  <div class="form-group">
                       <label for="chargeseme_name" >Type</label>
                         <input type="text" class="form-control input-sm" name="pay[payment_type]" value="<?php echo set_value('pay[payment_type]',$pay['payment_type']); ?>" readonly="true" />
                        <p class="help-block"></p>
                       </div>
                    </div>
                    
                      <div class="col-md-4 col-md-offset-1">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Transaction ID</label>
                      
                       	 <input type="text" class="form-control input-sm" id="id_transaction"  value="<?php echo set_value('pay[id_transaction]',$pay['id_transaction']); ?>" readonly="true" > 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
					</div>
                <!--    <div class="col-sm-4 col-md-offset-1">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Scheme A/c No</label>
                      	<select class="form-control" id="scheme_account"></select>
	              						<input type="hidden" class="form-control" id="id_scheme_account" name="pay[id_scheme_account]"  value="<?php echo set_value('pay[id_scheme_account]',$pay['id_scheme_account']); ?>" />
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>-->
					 <div <?php if($pay['id_payment_status']==1 && $pay['receipt_no_set']==1 ){?> class="col-sm-2 col-md-offset-1" <?php }else{?><?php }?> class="col-sm-4 col-md-offset-1">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Scheme A/c No</label>
                      	<select class="form-control" id="scheme_account"></select>
	              						<input type="hidden" class="form-control" id="id_scheme_account" name="pay[id_scheme_account]"  value="<?php echo set_value('pay[id_scheme_account]',$pay['id_scheme_account']); ?>" />
										<input type="hidden" class="form-control" name="pay[scheme_acc_number]"  value="<?php echo set_value('pay[scheme_acc_number]',$pay['scheme_acc_number']); ?>" />
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
					<?php if($pay['receipt_no_set']==1 && $pay['id_payment_status']==1){?>
					 <div class="col-sm-2">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Receipt No</label>
                      	  <div class="input-group ">              			
                       	 <input type="text" class="form-control input-sm" name="pay[receipt_no]" value="<?php echo set_value('pay[receipt_no]',$pay['receipt_no']); ?>"> 
                  <p class="help-block"></p>
                         </div>
                       </div>
                    </div>
					<?php }?>
					
					
                    
			        <div class="col-sm-4 col-md-offset-1">
				 	  <div class="form-group">
                       <label for="chargeseme_name" >A/c Name</label>
                     
                         <input type="text" class="form-control input-sm"  id="short_code" value="<?php echo set_value('pay[account_name]',$pay['account_name']); ?>" readonly="true" />
               
                        <p class="help-block"></p>
                       	
                       </div>
                    </div>    
                    
                  <div class="col-sm-2 col-md-offset-1">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Metal Rate</label>
                      	  <div class="input-group ">
              				<span class="input-group-addon input-sm" ><?php echo $this->session->userdata('currency_symbol')?></span>
                       	 <input type="text" class="form-control input-sm" name="pay[metal_rate]" value="<?php echo set_value('pay[metal_rate]',$pay['metal_rate']); ?>" readonly="true" > 
                  <p class="help-block"></p>
                         </div>
                       </div>
                    </div>       
                    
                 <div class="col-sm-2">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Weight</label>
                      
                       	 <input type="text" class="form-control input-sm" name="pay[metal_weight]" value="<?php echo set_value('pay[metal_weight]',$pay['metal_weight']); ?>" readonly="true" > 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>             
                    
                 <div class="col-sm-2 col-md-offset-1">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Charges</label>
                      <div class="input-group">
              				<span class="input-group-addon input-sm"><?php echo $this->session->userdata('currency_symbol')?></span>
                       	
                       	 <input type="text" class="form-control input-sm" name="pay[charges]" value="<?php echo set_value('pay[charges]',$pay['charges']); ?>" readonly="true" > 
                  <p class="help-block"></p>
                       	
                       </div>
                       </div>
                    </div>       
                    
                 <div class="col-sm-2">
				 	<div class="form-group">
				 	  <?php if($pay['showPaid']=='Y'){?>
                        <label for="chargeseme_name" >Paid Amount</label>
                      	 <div class="input-group ">
              				<span class="input-group-addon input-sm"><?php echo $this->session->userdata('currency_symbol')?></span>
                       	 <input type="text" class="form-control input-sm"   value="<?php echo $pay['act_amount']?>"  > 
                       	 	 <input type="hidden" class="form-control input-sm" name="pay[payment_amount]"  value="<?php echo set_value('pay[payment_amount]',$pay['payment_amount']); ?>"  > 
                  <p class="help-block"></p>
                       	
                       </div>
                       
                     <?php } else {?>
 
                        <label for="chargeseme_name" >Amount</label>
                      	 <div class="input-group ">
              				<span class="input-group-addon input-sm"><?php echo $this->session->userdata('currency_symbol')?></span>
                       	 <input type="text" class="form-control input-sm" name="pay[payment_amount]"  value="<?php echo set_value('pay[payment_amount]',$pay['payment_amount']); ?>" readonly="true"> 
                  <p class="help-block"></p>
                       	
                       </div>
                         <?php }?>
                       </div>
                    </div>
         
			     <div class="col-sm-2 col-md-offset-1">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Mode</label>
                      
                    	<select class="form-control"   id="pay_mode" ></select>
              			<input type="hidden" class="form-control" id="payment_mode" name="pay[payment_mode]" value="<?php echo set_value('pay[payment_mode]',$pay['payment_mode']); ?>" />
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
                    
			        <div class="col-sm-2 ">
				 	  <div class="form-group">
                       <label for="chargeseme_name" >Payment Ref.No</label>
	              	  	<input type="text" class="form-control"  name="pay[payment_ref_number]" value="<?php echo set_value('pay[payment_ref_number]',$pay['payment_ref_number']); ?>" />
               
                        <p class="help-block"></p>
                       	
                       </div>
                    </div>   	        
                    
                    <div class="col-sm-4 col-md-offset-1">
				 	  <div class="form-group">
                       <label for="chargeseme_name" >Payment Status</label>
                       
                     <?php if($pay['payment_type']=='Payu Checkout' && ($pay['id_payment_status']=='2' || $pay['id_payment_status']=='3')){?>
                      <input type="text" class="form-control input-sm" id="scheme_acc_number" value="<?php echo $pay['payment_status'] ?>" readonly="true" >
                     <?php } else {?>
                       <select class="form-control"  id="payment_status"></select>
                	 <?php }?>
                		<input type="hidden" class="form-control" id="pay_status" name="pay[payment_status]" value="<?php echo set_value('pay[payment_status]',$pay['id_payment_status']); ?>" />	  
                        <p class="help-block"></p>
                       	
                       </div>
                    </div>         
                    
                   <div class="col-sm-9 col-md-offset-1">
				 	  <div class="form-group">
                       <label for="chargeseme_name" >Remark</label>
                     
                         <textarea type="text" class="form-control input-sm"  name="pay[remark]" ><?php echo set_value('pay[remark]',$pay['remark']); ?></textarea>
               
                        <p class="help-block"></p>
                       	
                       </div>
                    </div>   
				 
	
                    
			     
                    
				 </div>	
 
	
	<input type="hidden" id="payment_id" value="<?php echo set_value('pay[id_payment]',$pay['id_payment']); ?>" />
				
				<br/>      
				<!-- <div class="row col-xs-12">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						<button type="submit" class="btn btn-primary">Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
						
					  </div> <br/>
					</div>
				  </div>   -->
	 <div class="row col-xs-12">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						<div class="btn-group" id="btn-submits" data-toggle="buttons">
						
				        <label class="btn btn-primary">
				            <input type="radio" name="type1" value="1">Save and Print
				        </label>

				        <label class="btn btn-primary">
				            <input type="radio" name="type1" value="2"> Save
				        </label>
				        </div>
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
						
					  </div> <br/>
					</div>
				  </div>     		  
				        	
               </form>              	              	
              </div>
            </div><!-- /.box-body -->			<div class="overlay" style="display:none">				  <i class="fa fa-refresh fa-spin"></i>			</div>
            <div class="box-footer">
              
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
         

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->