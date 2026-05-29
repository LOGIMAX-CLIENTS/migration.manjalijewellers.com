      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Bank master
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Post Dated Payment</a></li>
            <li class="active">Status</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
     
          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Post-Dated Payment - Status</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="">
              	
		
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
                    
                      <div class="col-sm-4 col-md-offset-1">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Cheque No</label>
                      
                       	 <input type="text" class="form-control" id="cheque_no"  value="<?php echo set_value('payment[cheque_no]',$payment['cheque_no']); ?>" readonly="true" > 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
                    
                    <div class="col-sm-4 col-md-offset-1">
				 	<div class="form-group">
                       <label for="chargeseme_name" >Scheme A/c No</label>
                      
                       	 <input type="text" class="form-control" id="scheme_acc_number"  value="<?php echo set_value('payment[scheme_acc_number]',$payment['scheme_acc_number']); ?>" readonly="true" > 
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
                     
			
                    
				 </div>	
				 <div class="row">
				 	<div class="col-sm-10 col-md-offset-1">
				 		<table class="table  table-bordered table-striped text-center">
				 			<thead>
				 				<tr>
				 					<th>Date</th>
				 					<th>User</th>				 					
				 					<th>Charges ( <?php echo $this->session->userdata('currency_symbol')?> )</th>
				 					<th>Status</th>				 					
				 				</tr>
				 			</thead>
				 			<tbody>
				 			<?php 
				 			if(isset($status_log )){
								
				 			foreach($status_log as $pay_status){?>
				 				<tr>
				 					<td><?php echo $pay_status['date_upd'];?></td>	
				 					<td><?php echo $pay_status['username'];?></td>	
				 					<td><?php echo $pay_status['charges'];?></td>	
				 					<td>
				 					<span class='label bg-<?php echo $pay_status['status_color'];?>-active'><?php echo $pay_status['payment_status'];?></span></td>	
				 						
				 				</tr>
				 				
								<?php } 
									}
								?>
				 			</tbody>
				 		</table>
				 	</div>
				 </div>
 
	
				
				<br/>      
				 <div class="row col-xs-12">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						
						<a class="btn btn-primary btn-cancel" href="<?php echo base_url('index.php/postdated/payment/list');?>">Back</a>
						
					  </div> <br/>
					</div>
				  </div>      
				        	
                            	              	
              </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
              
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
         

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->