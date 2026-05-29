<link href="<?php echo base_url() ?>assets/css/pages/changeUser.css" rel="stylesheet">
<div class="main-container">
	<div class="main" >
		<!-- main-inner --> 
		<div class="main-inner">
		<!-- container --> 
			<div class="container">
				<div class="row">
					<div class="span6">
						<div align="center"><legend class="head">Coin Enquiry Status</legend></div>
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
										<th>Product Name</th>
										<th>Gram</th>  
										<th>Coin Type</th> 
										<th>Status</th>
										<th>Description</th>
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
										<td><?php echo $enqury['product_name'] ?></td>
										<td><?php echo $enqury['gram'] ?></td>
										<td><?php echo $enqury['coin_type'] ?></td>
										<td><?php echo $enqury['status'] ?></td>
										<td><?php echo $enqury['enq_description'] ?></td>
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