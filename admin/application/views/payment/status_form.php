      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Bank Master
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo site_url('payment/list');?>">Payment</a></li>
            <li class="active">Status Log</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Payment Status Log</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
          
              <div class="col-md-10 col-md-offset-1">              	
              		<div class="row">
	              		<div class="col-sm-6">
	              			 <div class="form-group">
	              					<label for="" >Scheme A/c No</label>
	              		                      <label class="form-control input-sm" id="scheme_acc_number"><?php echo $pay['scheme_acc_number'];?></label>
	              		            <div style="display: none;">
	              		            		<select class="form-control" name="pay[id_scheme_account]" id="scheme_account" ></select>
	              						<input type="hidden" class="form-control" id="id_scheme_account"  value="<?php echo set_value('pay[id_scheme_account]',$pay['id_scheme_account']); ?>" />
	              		            </div>          
	              				
	              			 </div>
	              		</div>
	              		<div class="col-sm-4">
	              		<div class="form-group">
	          					<label for="">Payment Date</label>
	          					<div class='input-group date'>
				                    <input type='text' class="form-control input-sm datemask myDatePicker" name="pay[date_payment]"  id='pay_date' data-inputmask="'alias': 'dd/mm/yyyy'" data-mask  data-date-format="dd/mm/yyyy" value="<?php echo set_value('pay[date_payment',$pay['date_payment']); ?>" readonly="true" />
				                    <span class="input-group-addon">
				                        <span class="glyphicon glyphicon-calendar"></span>
				                    </span>
				                    
				                </div>
	          				</div>		
	              		</div>	 
	              		
	              	</div>
              		
              		  	<div class="row">
			            <div class="col-xs-12 col-md-12 col-lg-12">
			                <div  id="scheme-detail-box" class="box box-solid box-default">
			                    <div class="box-header with-border"><h3 class="box-title">Scheme A/c Details</h3></div>
			                    <div class="box-body">
			                    
				                    <div class="col-xs-12 col-md-4 col-lg-4 pull-left">
				                       <table class="table table-condensed">
				                       	<tr>
				                       		<th>Joined on</th>
				                       		<td><span id="start_date"></span></td>
				                       	</tr> 	
				                       	<tr>
				                       		<th>A/c Name</th>
				                       		<td><span id="acc_name"></span></td>
				                       	</tr> 	
				                       	<tr>
				                       		<th>Scheme Code</th>
				                       		<td><span id="scheme_code"></span></td>
				                       	</tr> 	
				                       	<tr>
				                       		<th>Type</th>
				                       		<td><span id="scheme_type"></span></td>
				                       	</tr>
				                       </table>			                        
				                     </div>          
				                     
				                     <div class="col-xs-12 col-md-4 col-lg-4">
				                       <table class="table table-condensed">
				                       	<tr>
				                       		<th>Payable</th>
				                       		<td><span id="payable"></span></td>
				                       	</tr> 	
				                       	<tr>
				                       		<th>Paid Installments</th>
				                       		<td><span id="paid_installments"></span></td>
				                       	</tr> 	
				                       	<tr>
				                       		<th>Amount Paid</th>
				                       		<td><span id="total_amount_paid"></span></td>
				                       	</tr> 	
				                       	<tr>
				                       		<th>Weight Paid</th>
				                       		<td><span id="total_weight_paid"></span></td>
				                       	</tr>
				                       </table>			                        
				                     </div>
				                     
				                     <div class="col-xs-12 col-md-4 col-lg-4">
				                       <table class="table table-condensed">
				                       	<tr>
				                       		<th>Last Paid Date</th>
				                       		<td><span id="last_paid_date"></span></td>
				                       	</tr> 	
				                       	<tr>
				                       		<th>Unpaid Dues</th>
				                       		<td><span id="unpaid_dues"></span></td>
				                       	</tr> 	
				                       	<tr>
				                       		<th>PDC/ECS exists</th>
				                       		<td><span id="total_pdc"></span></td>
				                       	</tr> 	
				                       	<tr>
				                       		<th>Can pay?</th>
				                       		<td><span id="allow_pay"></span></td>
				                       	</tr>
				                       </table>			                        
				                     </div>
			                     
			                    </div>
			                </div>
			            </div>
			        
			
			        </div>
              
             
              
              <div class="row">
              		<div class="col-sm-12">
              				<table class="table  table-bordered table-striped text-center">
				 			<thead>
				 				<tr>
				 					<th>Date</th>
				 					<th>User</th>				 					
				 					<th>Amount ( <?php echo $this->session->userdata('currency_symbol')?> )</th>
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
        
				  
            </div><!-- /.box-body -->
           
            <div class="box-footer">
              <div align="center">
				  
						<a class="btn btn-primary" href="<?php echo base_url('index.php/payment/list');?>">Back</a>
						
				  </div> 
            </div><!-- /.box-footer-->
               <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
          </div><!-- /.box -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->