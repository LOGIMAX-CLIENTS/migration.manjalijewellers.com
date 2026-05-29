<div class="main-container">
<!-- main -->		  
<div class="main"  id="schemPayList">
<!-- main-inner --> 
<div class="main-inner">
	<!-- container --> 
	<div class="container">
		<div class="row">
			<div class="span12">
				<div align="center"><legend><span class="head">PDC / ECS</span></legend></div>
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

				?> <p style="color:red;"> Post-date payments to be submitted: <?php echo $content['total']; ?></p>
				<div class="schemeTable">
					<div class="table-responsive">
						<table  id="pdc_report"  class="table table-bordered table-striped table-responsive display">
							<thead>
								<tr>
									<!--<th>Group Code</th>-->
									<th>A/c No</th>
									<th>Date</th>
									<th>ECS / Cheque No</th>
									<th>Bank</th>							
									<th>Branch</th>							
									<th>Amount</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php
									if(isset($content['pdc'])){
									foreach($content['pdc'] as $pay){
								?>
								<tr>
									<td><?php echo $pay['code'].' '.$pay['scheme_acc_number']; ?></td>
									<td><?php echo $pay['date_payment'];?></td>
									<td><?php echo $pay['cheque_no'];?></td>
									<td><?php echo $pay['payee_bank'];?></td>
									<td><?php echo $pay['payee_branch'];?></td>
									<td><?php echo $pay['amount'];?></td>
									<td><?php echo $pay['payment_status'];?></td>
								</tr>
								<?php }  }  ?> 
							</tbody> 
						</table>
					</div>			 
				</div>
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