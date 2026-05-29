<link href="<?php echo base_url() ?>assets/css/pages/payment.css" rel="stylesheet">
<div class="main-container">
	<div class="main-container">
		<!-- main -->		  
		<div class="main"  id="schemPayList">
				<!-- main-inner --> 
			<div class="main-inner">
					<!-- container --> 
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<div align="center"><legend class="head">PAYMENT</legend></div>
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
							if(isset($content['chits'])){
							?>
							<div class="schemeTable">
								<div class="table-responsive">
									<table  id="paydues" class="table table-bordered table-striped table-responsive display">
										<thead>
											<tr>
												<th>ID</th>
												<th>Group Code</th>
												<th>Membership No.</th>
												<th>A/c Name</th>
												<th>Scheme Name</th>
												<th>Payable</th>							
												<th>PDC/ECS</th>							
												<th>Status</th>							
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach($content['chits'] as $chit){
											// if($chit['allow_pay'] == 'Y'){
											$status =	($chit['previous_paid']==1?array('state'=>'Success','class'=>'success','msg'=>'Your payment for current month credited successfully'):($chit['current_paid_installments']==0?array('state'=>'Not Paid','class'=>'danger','msg'=>'You have not made any payment this month'):($chit['last_transaction']['payment_status']==1?array('state'=>'Success','class'=>'success','msg'=>'Your payment credited successfully'):($chit['last_transaction']['payment_status']==2?array('state'=>'Awaiting','class'=>'info','msg'=>'Your status will be updated after amount received from bank'):($chit['last_transaction']['payment_status']==3 ? array('state'=>'Failed','class'=>'danger','msg'=>'Your payment failed'):array('state'=>'Pending','class'=>'warning','msg'=>'Your payment failed'))))));
											?>
											<tr>
											<td><?php  echo $chit['id_scheme_account']; ?></td>
											<td><?php  echo $chit['code']; ?></td>
											<td><?php echo ($chit['chit_number']!=''?$chit['chit_number']:"Not Allocated");?></td>
											<td><?php echo $chit['account_name'];?></td>
											<td><?php echo $chit['scheme_name'];?></td>
											<td><?php echo ($chit['scheme_type']==0 || $chit['scheme_type']==2 ? $chit['currency_symbol']." ".number_format($chit['payable'],'2','.',''):$chit['payable'].' g (Max)');?></td>
											<td><?php echo $chit['cur_month_pdc']; ?></td>
											<td><?php echo '<span class="label label-'.$status['class'].'">'.$status['state'].' <i rel="tooltip"  title="'.$status['msg'].'" class="icon-question-sign help-icon"></i></span>' ?></td>
											<td>
											<?php if($chit['allow_pay'] == 'N'){
											$pay_lock =	"Your payment chances over for current month";
											?>
											<button class="btn btn-primary btn-single btn-sm pay_submit"  disabled="true" >Pay</button>
											<?php } else{ 
											if($chit['scheme_type']==0 ? $chit['previous_amount_eligible'] : true) {
											?>
											<a href="#;" data-id="<?php echo $chit['id_scheme_account']?>" class="btn btn-primary btn-single btn-sm pay_submit" >Pay</a>
											<?php }  else { ?>
											<button class="btn btn-primary btn-single btn-sm pay_submit"  disabled="true" >Pay</button> <i rel="tooltip"  title="Your payment chances over for current month" class="icon-question-sign help-icon"></i>
											<?php } ?>
											</td>
											<?php }?>	
											</tr>
											<?php } // }  ?> 
										</tbody> 
									</table>
								</div>			 
							</div>
							<?php     } else { ?> 	
							<div class="alert alert-danger" align="center">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<strong>Warning!</strong> You have not joined in any scheme scheme. Join first and then make payment.
							</div>
							<?php }  ?> 			
						</div>	
					</div>
				<!-- /container --> 
				</div>
				<!-- /main-inner --> 
			</div>
			<!-- /main -->	
		</div>	
	</div>
</div>
<!-- modal-->
<div class="modal fade" id="payModal" tabindex="-1" role="dialog"  aria-labelledby="gridSystemModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4  id="gridSystemModalLabel">Payment</h4>
			</div>
			<?php 
			$attributes = array('id' => 'pay_popup');
			echo form_open('paymt/paySubmit',$attributes) ?>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn join-button btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn join-button button" id="confirm_pay">Confirm Pay</button>
			</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- modal-->
<div class="modal fade" id="panModal" tabindex="-1" role="dialog"  aria-labelledby="gridSystemModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header" >
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4  id="gridSystemModalLabel">UPDATE PROFILE</h4>
			</div>
			<?php 
			$attributes = array('id' => 'pan_popup');
			echo form_open('user/insertpan',$attributes) ?>
			<div class="modal-body">
				<p> The saving scheme you have joined requires your pan card number.</p>
				<b>Enter Pan card no.</b> <input type="text" name=pan value="" id="pan_no"/>
			</div>
			<div align="center">
				<button type="button" class="btn join-button btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn join-button btn-primary" id=pan_submit>Submit</button>
			</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<br />
<br />
<br />

