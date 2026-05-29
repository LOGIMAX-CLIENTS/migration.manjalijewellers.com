<link href="<?php echo base_url() ?>assets/css/pages/dashboard.css" rel="stylesheet">
<div class="main-container">
<div class="main" >
  <!-- main-inner --> 
	  <div class="main-inner">
		 <!-- container --> 
		<div class="container">
			<div class="row">
				<div class="span12">
                    <div align="center"><legend class="head">MY SCHEMES</legend></div>
                    <div><a href="<?php echo base_url('index.php/chitscheme/exisRegReq'); ?>" class="btn btn-mini btn-warning" data-toggle="modal">Existing Reg Requests</a></div><br/>
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
					<?php } ?> 
					<?php
						if($schemes)
						{	
							$i = 1;
							foreach($schemes as $key => $value)
							{ 
								if($i == 1)
								{
								?>
									<table id="myschemes_list" class="table table-bordered table-striped table-responsive display" >
										<thead style="text-align:center">
											<tr >
												<th width="5%">S.No</th>
										<?php 	if($this->session->userdata('branch_settings')==1){?>
												<th width="10%">Branch</th>
										<?php } ?>
												<th width="11%">A/c No.</th> 
												<th width="11%">A/c Name</th>
												<th width="10%">Joined On</th>
												<th width="13%"  style="text-align:center">Payable</th>
												<th width="12%">Paid Installments</th>
												<th width="13%">Total Paid Amount </th>
												<th width="13%">Total Weight in gms </th>
												<th width="7%">Status</th>
												<th width="12%">View Details</th>
											</tr>
										</thead>
										<tbody>
								<?php } ?>
									<tr>
										<td><?php echo $i ?></td>
										<?php if($this->session->userdata('branch_settings')==1){?>
												<td><?php echo $value['branch_name'] ?></td>
										<?php } ?>
        							 	<td><?php 
        				      			echo ($value['scheme_acc_number']!=''?$value['scheme_acc_number']:"Not Allocated");
        							 	?>
        							 	</td>
        								<!--<td><?php echo $value['scheme_acc_number'] == '' ?""  : $value['scheme_acc_number'] ?></td>-->
        								<td><?php echo $value['account_name'] ?></td>
        								<td><?php echo $value['start_date'] ?></td>
        								<td><?php echo ($value['scheme_type'] == 'Amount' || $value['scheme_type'] == 'Amount to Weight'?$value['currency_symbol']." ". number_format($value['payable'],'2','.','') :' Max '.$value['payable'].' g/month' ); ?></td>
        								<td style="text-align:center"><?php echo '<span class="badge bg-green">'.$value['paid_installments'].'/'.$value['total_installments'].'</span>' ?></td>
        								<td><?php echo $value['currency_symbol']." ".($value['total_paid_amount']-$value['paid_gst']) ?></td>
        								<td><?php echo ($value['total_paid_weight']==0?'-':$value['total_paid_weight'].' g')?></td>
        								<td><?php echo $value['is_closed'] == 1 ? '<span class="label">Closed</span>':'<span class="label label-success">Active</span>'; ?></td>
        								<td ><a href="<?php echo base_url('index.php/chitscheme/scheme_account_report/'.$value['id_scheme_account'])?>"  class="btn btn-primary btn-xs" >View</a>
        								  <?php if($value['one_time_premium']==0 && $value['paid_installments']==0 && $value['has_payment']==0 && ($value['pdc']==''||$value['pdc']<=0) && ($value['pdc_status']!=2 && $value['pdc_status']!=7) && $delete_unpaid==TRUE) {?> 
        									 <a href="#confirm-delete" data-href="<?php echo base_url('index.php/chitscheme/delete_account/'.$value['id_scheme_account']); ?>" class="btn btn-xs btn-del btn-danger" data-toggle="modal"><i class="fa fa-trash"></i></a>					 
        								  <?php } ?>
							        	</td>
								    </tr>
						  <?php $i++; } 
								?>
								</tbody>
								</table>	
						 <?php } else { ?>
								<div class="alert alert-danger" align="center">
								  <button type="button" class="close" data-dismiss="alert">&times;</button>
								  <strong>Currently no scheme accounts available! Please join scheme.</strong>
								</div>      
						 <?php } ?>
						  <br/>
			</div>	
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
					<p>Are you sure? You want to delete this scheme account?</p>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn join-button btn-default"  data-dismiss="modal">Cancel</a>
					<a href="#" class="btn join-button btn-danger btn-confirm">Delete</a>
				</div>
			</div>
		</div>
	</div>
</div>
<br />
<br />
<br />