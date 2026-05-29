<div class="main-container">
<!-- main -->		  
<div class="main"  id="schemPayList">
  <!-- main-inner --> 
  <div class="main-inner">
     <!-- container --> 
    <div class="container">
    	<div class="row">
        	<div class="span12">
					<div align="center"><legend><span class="head">WALLET</span></legend></div>
				<?php
				 
				if($this->session->flashdata('successMsg')) { ?>
					<div class="alert alert-success" align="center">
					  <button type="button" class="close" data-dismiss="alert">&times;</button>
					  <strong><?php echo $this->session->flashdata('successMsg'); ?> </strong>
					</div>      
				<?php } else if($this->session->flashdata('errMsg')) { ?>							 
					<div class="alert alert-danger" align="center">
					  <button type="button" class="close" data-dismiss="alert">&times;</button>
					  <strong><?php echo $this->session->flashdata('errMsg'); ?></strong>
					</div>
				<?php } 
		    
		    //check scheme account id		
		       if(isset($content['transactions'])){
			?>
				<div class="schemeTable">
				 <div class="table-responsive">
						<table  id="wallet_report" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>ID</th>
								<th>Date</th>
								<th>Customer</th>
								<th>Mobile</th>				
								<th>Withdrawal</th>							
								<th>Deposit</th>							
								<th>Narration</th>
							</tr>
						</thead>
						<tbody>
						<?php
							foreach($content['transactions'] as $trans){
					
						 ?>
						 
						 <tr>
						 	<td><?php echo $trans['id_wallet_transaction']; ?></td>
						 	<td><?php echo $trans['date_transaction'];?></td>
						<!-- 	<td><?php echo $trans['name'];?></td>
						 	<td><?php echo $trans['mobile'];?></td>-->
						 	<td><?php echo ($trans['transaction_type']==1? $trans['value']:'-');?></td>
						 	<td><?php echo ($trans['transaction_type']==0? $trans['value']:'-');?></td>
						 	<td><?php echo $trans['description'];?></td>
			
						 </tr>
						<?php }  ?> 
						
						</tbody> 
					    </table>
					   </div>			 
					</div>
               
				<?php }  ?> 	
				<br/>		
				<br/>		
				<br/>		
		</div>	
    </div>
    <!-- /container --> 
  </div>
  <!-- /main-inner --> 
</div>
<!-- /main -->	

<!-- modal-->
<div class="modal fade" id="payModal" tabindex="-1" role="dialog"  aria-labelledby="gridSystemModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="gridSystemModalLabel">Payment</h4>
      </div>
      
      	<?php 
      	$attributes = array('id' => 'pay_popup');
      	echo form_open('paymt/paySubmit',$attributes) ?>
      <div class="modal-body">
          
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Confirm Pay</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

