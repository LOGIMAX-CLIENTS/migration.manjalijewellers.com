<link href="<?php echo base_url() ?>assets/css/pages/dashboard.css" rel="stylesheet">
<div class="main-container">
	<div class="main" >
		<!-- main-inner --> 
		<div class="main-inner">
			<!-- container -->
			<div class="container">
				<div class="row">
					<div class="span6">
						<div align="center"><legend class="head">Customer Complaints</legend></div>
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
						<?php }?> 
						<?php
							if(isset($content)){
							$i = 1;
							foreach($content as $record){
							if($i == 1)
							{
						?>				
						<div class="table-responsive">
							<table id="cuscomplaint_list" class="table table-bordered table-striped table-responsive display" >
								<thead>
									<tr>
										<th>S.No</th>
										<th>Date</th> 
										<th>Complaint No </th> 
										<th>Status</th> 
										<th width="12%">View Details</th>
									</tr>
								</thead>
								<tbody>
									<?php } ?>
									<tr>
										<td><?php echo $i ?></td>
										<td><?php echo $record['date_add'] ?></td>
										<td><?php echo $record['ticket_no'] ?></td>
										<td><?php echo $record['status'] ?></td>
										<td><a href="<?php echo base_url('index.php/user/custComplaintStatus/'.$record['id_enquiry'].'/'.$record['ticket_no'])?>"  class="btn btn-primary btn-single btn-mini pay_submit" >View</a>
										</td>
									</tr>
									<?php $i++; } 
									?>
								</tbody>
							</table>	
							<?php } ?>
						</div>
					</div>	
				</div>
				<!-- /container --> 
			</div>
			<!-- /main-inner --> 
		</div>
		<!-- /main -->	
	</div>
</div>
<br />
<br />
<br />