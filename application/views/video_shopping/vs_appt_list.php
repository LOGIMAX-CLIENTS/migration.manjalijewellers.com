<link href="<?php echo base_url() ?>assets/css/pages/dashboard.css" rel="stylesheet">
<div class="main-container">
<div class="main" >
  <!-- main-inner --> 
	  <div class="main-inner">
		 <!-- container --> 
		<div class="container">
			<div class="row">
				<div class="span12">
                    <div align="center"><legend class="head">Video Shopping Appointments</legend></div>
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
						if($content)
						{	
							$i = 1;
							foreach($appts as $key => $value)
							{ 
								if($i == 1)
								{
								?>
									<table id="appt_list" class="table table-bordered table-striped table-responsive display" >
										<thead style="text-align:center">
											<tr >
												<th>S.No</th>  
												<th>Name</th>
												<th>Mobile</th>
												<th>Requested On</th>
												<th>Preferred Slot</th>
												<th>Alloted Slot</th>
												<th>Status</th>
												<th>View Details</th>
											</tr>
										</thead>
										<tbody>
								<?php } ?>
									<tr>
										<td><?php echo $i ?></td>
										<td><?php echo $value['name'] ?></td>
										<td><?php echo $value['mobile'] ?></td>
										<td><?php echo $value['created_on'] ?></td>
										<td><?php echo $value['pref_slot'] ?></td>
										<td><?php echo $value['alloted_slot'] ?></td>
										<td><?php echo $value['status_msg'] ?></td>
        								<td ><a href="<?php echo base_url('index.php/vs_appt_book/vs_appt_detail/'.$value['id_appt_request'])?>"  class="btn btn-primary btn-xs" >View</a>
							        	</td>
								    </tr>
						  <?php $i++; } 
								?>
								</tbody>
								</table>	
						 <?php } else { ?>
								<div class="alert alert-danger" align="center">
								  <button type="button" class="close" data-dismiss="alert">&times;</button>
								  <strong>No appointments available.</strong>
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
					<p>Are you sure? You want to delete this Purchase plan account?</p>
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