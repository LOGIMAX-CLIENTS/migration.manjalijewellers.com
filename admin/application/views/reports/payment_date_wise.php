<style type="text/css">
.DTTT_container{
margin-bottom:0 !important;
}
</style>
  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Payment Report
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Payment Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Date-wise Payment Report</h3>      
                <!--           <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/account/add'); ?>"><i class="fa fa-user-plus"></i> Add</a>--> 
                	
                	<div class="col-md-12">
	                	<div class="form-group col-md-offset-3 col-md-2">
			                <label for="report_date">Report Date</label>
			            </div>
			            <div class="col-md-3">
			                	<input class="form-control datemask"  data-date-format="dd-mm-yyyy" id="report_date" name="customer[report_date]" value="<?php echo date('d-m-Y'); ?>" placeholder="Report date" type="text" />
			            </div>
	                </div>
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
                   <div class="table-responsive">
                  <table id="datewise_paid_report" class="table table-bordered table-striped text-center date_pay_report"  >
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Scheme Type</th>                        
                        <th>Total Amount</th>                        
                        					                      
                      </tr>
                    </thead>
                    <tbody>
                     <?php
                     	if(isset($payments)) 
                     	{
                     		$today_total=0;
                     		foreach($payments as $pay)
							{
								$today_total = number_format((float)($today_total + $pay['classification_total']), 2, '.', '');
								
							} 
							$closing_bal = number_format((float)($today_total + $opening_balance), 2, '.', '');
							
                     	} 
                     	if(isset($payments)) { 
                     	               		
                     	 foreach($payments as $pay)
						{
                      ?>
                       <tr>
                         <td><?php echo $pay['id_classification'];?></td>
						 <td><?php echo $pay['classification_name']; ?></td>
                       	 <td><?php echo $pay['classification_total'];?></td>
                       </tr>
                       <?php } } ?>
                    </tbody>
                    
                    <tfoot>
                      <tr>
                        <th>Opening Balance</th>
                        <th>Today's Total Collection</th>                        
                        <th>Closing Balance</th>                        
                      </tr>
                      <tr>
                         <td id="open_bal"><?php echo number_format((float)$opening_balance, 2, '.', '');?></td>
						 <td id="tot_coll"><?php echo number_format((float)$today_total, 2, '.', '');?></td>
                       	 <td id="close_bal"><?php echo $closing_bal;?></td>
                       </tr>
                    </tfoot> 
                  </table>
                  
                </div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      


<!-- modal -->      
<div class="modal fade" id="pay-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Scheme Payment</h4>
      </div>
      <div class="modal-body">
         <div class="fluid-container">
         	<div class="row">
         		<div class="col-sm-6">
              		<div class="form-group">
      					<label for="">Account Name</label>
      					<input type="hidden" id="acc_id" name="acc_id"/>
		                    <input type='text' id="acc_name" name="acc_name" readonly="true" class="form-control" />
		                 
          			</div>		
	             </div>	   
	             <div class="col-sm-6">
              		<div class="form-group">
      					<label for="">Payment Date</label>
      					<div class='input-group date'>
		                    <input type='text'  id='pay_date' name="pay_date"  class="form-control myDatePicker" />
		                    <span class="input-group-addon">
		                        <span class="glyphicon glyphicon-calendar"></span>
		                    </span>
		                </div>
          			</div>		
	             </div>	   
	         </div>
         </div>    
         <div class="row">
         	<div class="col-sm-6">
          		<div class="form-group">
  					<label for="">Amount</label>
  						<div class="input-group">
  					<span class="input-group-addon">
		                        <span class="fa fa-inr"></span>
		                    </span>
	                <input type='text' id="sch_amount" name="sch_amount" readonly="true" class="form-control" />
	                </div>
      			</div>		
	         </div>	 
	         <div class="col-sm-6">
          		<div class="form-group">
  					<label for="">Payment mode</label>
  						<select id="pay_mode" name="pay_mode" class="form-control">
  							<option value="1">Cash</option>
  							<option value="2">Cheque</option>
  							<option value="3">Credit Card</option>
  							<option value="4">Debit Card</option>  							
  						</select>	              
      			</div>		
	         </div>	 
         </div>
         <div class="row">
         	 <div class="col-sm-12">
          		<div class="form-group">
  					<label for="">Remark</label>
  						<textarea id="pay_remark" name="pay_remark" class="form-control"></textarea>
      			</div>		
	         </div>	 
         </div>
      </div>
      <div class="modal-footer">
      	<a href="#" id="pay_amount" class="btn btn-danger" >Pay</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

