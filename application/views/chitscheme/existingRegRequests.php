<link href="<?php echo base_url() ?>assets/css/pages/dashboard.css" rel="stylesheet">
<div class="main-container">
	<!-- main -->		  
	<div class="main">
	  <!-- main-inner --> 
		<div class="main-inner">
		 <!-- container --> 
			<div class="container dashboard">
			  <!-- alert -->
				<div class="row">
					<div class="span12">
						<div align="center"><legend>Existing Purchase plan Registration Requests</legend></div>
						<?php
						if($this->session->flashdata('successMsg')) { ?>
							<div class="alert alert-success" align="center">
							  <button type="button" class="close" data-dismiss="alert">&times;</button>
							  <strong><?php echo $this->session->flashdata('successMsg'); ?> </strong>
							</div>      
						<?php } if($this->session->flashdata('errMsg')) { ?>							 
							<div class="alert alert-danger" align="center">
							  <button type="button" class="close" data-dismiss="alert">&times;</button>
							  <strong><?php echo $this->session->flashdata('errMsg'); ?></strong>
							</div>
						<?php } ?>
						<div class="dbdata">
							<div class="">
								<?php
									if(isset($exisRegReq))
								{	
									$i = 0;
									foreach($exisRegReq as $key => $value)
								{ 
									if($i == 0)
									{
									?>
								<table class='table table-bordered table-responsive'>
									<thead>
										<tr>
										<!--	<th width="10%">Branch</th>-->
											<th width="10%">Chit A/c No.</th>
											<th width="10%">Group code</th>
											<th width="15%">Account Name</th>
											<th width="10%">Requested On</th>
											<th width="10%">Status</th>
											<th width="45%">Remark</th>
										</tr>
									</thead>
									<tbody>
									<?php } ?>
										<tr>
											<!--<td><?php echo ($value['branch_name'] != "" ? $value['branch_name'] : '-'); ?></td>-->
											<td><?php echo $value['scheme_acc_number'] == '' ? '<span class="label label-danger">Not Allocated</span> <i rel="tooltip" title="Your chit account number not yet allocated by admin." class="icon-question-sign help-icon"></i>' : $value['scheme_acc_number'] ?></td>
												<td><?php echo $value['group_code'] ?></td>
											<td><?php echo $value['ac_name'] ?></td>
											<td><?php echo $value['date_add'] ?></td>
											<td><?php echo $value['status'] == 0 ? '<span class="label label-warning">Processing</span>':($value['status'] == 1?'<span class="label label-success">Approved</span>':'<span class="label label-danger">Rejected</span>'); ?></td>
											<td><?php echo $value['remark'] ?></td>

										</tr>
										<?php $i++; } 
										?>
									</tbody>
								</table>	
								<?php }else { ?>
								<div class="alert alert-danger" align="center">
									<button type="button" class="close" data-dismiss="alert">&times;</button>
									<strong>You have not made any requests!</strong>
								</div>      
							<?php } ?>
						   </div>
						</div>
						<p style="height:10px"></p>
					</div>
				</div>		
				<!-- /alert -->  
			</div>
		<!-- /container --> 
		</div>
	  <!-- /main-inner --> 
	</div>
	<!-- /main -->		  
</div>
<div class="modal fade" id="confirm-delete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>Confirm Delete</h3>
			</div>
			<div class="modal-body">
				<p>Are you sure? You want to delete this Purchase plan account?</p>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn join-button btn-default"  data-dismiss="modal">Cancel</a>
				<a href="#" class="btn join-button btn-danger btn-confirm">Delete</a>
			</div>
		</div>
	</div>
</div>