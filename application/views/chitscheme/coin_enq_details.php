<link href="<?php echo base_url() ?>assets/css/pages/changeUser.css" rel="stylesheet">
<div class="main-container">
	<div class="main" >
		<!-- main-inner --> 
		<div class="main-inner">
			<!-- container --> 
			<div class="container">
				<div class="row">
					<div class="span6">
						<div align="center"><legend class="head">Coin Enquiry Details</legend></div>
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
							if(isset($content)){	?>
						<div class="table-responsive">
							<table class="table table-bordered table-striped ">
								<thead>
									<tr>
										<th>Date</th>
										<th>Status</th>
										<th>Gram</th> 
										<th width="12%">View Details</th>
									</tr>
								</thead>
								<tbody>
									<!--<tfoot>
									<tr >
									<td colspan="10"> <p style="text-align:left"></p></td>
									</tr>
									</tfoot> -->
								<?php
									$i=1;
									foreach($content as $enqury){
								?>
									<tr>
										<td><?php echo $enqury['date_add'] ?></td>
										<td><?php echo $enqury['status'] ?></td>
										<td><?php echo $enqury['gram'] ?></td>
										<td><a href="<?php echo base_url('index.php/user/coin_enq_status/'.$enqury['id_enquiry'])?>"  class="btn btn-primary btn-single btn-mini pay_submit" >View</a>
									</td>
									</tr>
								<?php  } } ?>	
								</tbody>
							</table>
						</div>
						<br/>
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