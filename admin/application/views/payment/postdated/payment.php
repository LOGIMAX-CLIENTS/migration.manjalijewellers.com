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
            <li class="active">Payment List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Scheme Payment List</h3>      
                <!--           <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/account/add'); ?>"><i class="fa fa-user-plus"></i> Add</a>--> 
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
                  <table class="table table-bordered table-striped text-center grid">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Ref.No</th> 
                        <th>Code</th>    
                        <th>Scheme Type</th>
                        <th>Paid Date</th>
                        <th>transaction ID</th>	
                        <th>Mode</th>  							                      
                        <th>Amount</th>
                        <th>Status</th>
                      
                      </tr>
                    </thead>
                    <tbody>
                     <?php 
                     	if(isset($accounts)) {                     		
                     	 foreach($accounts as $account)
						{
                      ?>
                       <tr>
                         <td><?php echo $account['id_payment'];?></td>
                       	 <td><?php echo $account['name'];?></td>
                       	 <td><?php echo $account['mobile'];?></td>
                       	 <td><?php echo $account['ref_no'];?></td>
                       	 <td><?php echo $account['code'];?></td>
                       	 <td><?php echo $account['scheme_type'];?></td>
                       	 <td><?php echo date("d-m-Y",strtotime($account['date_payment']));?></td>
                       	 <td><?php echo $account['id_transaction'];?></td>
                  		 <td><?php echo $account['payment_mode'];?></td>
                   		 <td><?php echo $account['payment_amount'];?></td>
                  		 <td><?php echo $account['payment_status']; ?></td>
                  		
                       </tr>
                       <?php } } ?>
                       </form>
                    </tbody>
                 <!--   <tfoot>
                      <tr>
                        
                      </tr>
                    </tfoot> -->
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

