<link href="<?php echo base_url() ?>assets/css/pages/video_shopping.css" rel="stylesheet">
<style>
	.col-xs-6
	{
		overflow-wrap: anywhere;
	}
	.appt-title{
		border-bottom: 1px solid #eee !important;
		font-size: 18px  !important;
	}
</style>
<div class="main-container">
<!-- main -->		  
<div class="main">
  <!-- main-inner --> 
  <div class="main-inner">
     <!-- container --> 
    <div class="container dashboard">
	  <!-- alert -->
      <div class="row">
	    <div class="col-md-12">
			<div align="center"><legend class="head">Appointment Detail</legend></div>
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
	    </div>		
		<!-- /alert -->  
     </div> 
     <div class="row">
		<div class="col-md-offset-2 col-md-8">
			<div class="widget"> 
	            <div class="widget-content">  
	              <div class="row"> 
					  <div class="col-md-4"> 
					  	<div class="row">
							<div class="col-xs-12" >
								<p class="appt-title">Personal Detail</p>
							</div> 
						</div>
						<div class="row">
							<div class="col-xs-6" >
								<b>Name</b>
							</div>
							<div class="col-xs-6" ><?php echo  $appts['req']['name'];?></div>
						</div>
						
						<div class="row">
							<div class="col-xs-6" >
								<b>Mobile</b>
							</div>
							<div class="col-xs-6" ><?php echo $appts['req']['mobile']; ?></div>  
						</div>   
						
						<div class="row">
							<div class="col-xs-6" >
								<b>WhatsApp No</b>
							</div>
							<div class="col-xs-6" >
								<?php echo $appts['req']['whats_app_no']; ?> 
							</div>
						</div>
						
						<div class="row">
							<div class="col-xs-6" >
								<b>Email</b>
							</div>
							<div class="col-xs-6" style="overflow-wrap: anywhere;">
								<?php echo $appts['req']['email']; ?> 
							</div>
						</div>
						 
						<div class="row">
							<div class="col-xs-6" >
								<b>Location</b>
							</div>
							<div class="col-xs-6" >
								<?php echo $appts['req']['location']; ?> 
							</div>
						</div>  
					</div>
					
					<div class="col-md-8">
						<div class="row">
							<div class="col-xs-12" >
								<p class="appt-title">Appointment Detail</p>
							</div> 
						</div> 
						<div class="row">
							<div class="col-xs-6 col-sm-5" >
								<b>Preferred Categoty</b>
							</div>
							<div class="col-xs-6" >
								<?php echo $appts['req']['pref_category']; ?> 
							</div>
						</div>  
						<div class="row">
							<div class="col-xs-6 col-sm-5" >
								<b>Preferred Item</b>
							</div>
							<div class="col-xs-6" >
								<?php echo $appts['req']['pref_item']; ?> 
							</div> 
						</div>  
						<div class="row">
							<div class="col-xs-6 col-sm-5" >
								<b>Preferred Slot</b>
							</div>
							<div class="col-xs-6" >
								<?php echo $appts['req']['pref_slot']; ?> 
							</div>
						</div>  
						<div class="row">
							<div class="col-xs-6 col-sm-5" >
								<b>Message</b>
							</div>
							<div class="col-xs-6" >
								<?php echo $appts['req']['description']; ?> 
							</div>
						</div>  
						<div class="row">
							<div class="col-xs-6 col-sm-5" >
								<b>Status</b>
							</div>
							<div class="col-xs-6" >
								<?php echo $appts['req']['status_msg']; ?> 
							</div>
						</div>
						<?php if($appts['req']['get_feedback']== 0){ ?>
							<div class="row">
								<div class="col-xs-6 col-sm-5" >
									<b>Feedback</b>
								</div>
								<div class="col-xs-6" >
									<?php echo $appts['req']['customer_feedback']; ?> 
								</div>
							</div> 
						<?php	} ?>
						  
						<div class="row">
							<div class="col-xs-6 col-sm-5" style="display:<?php echo ($appts['req']['status']==2?'block':'none'); ?>" >
								<b>Reject Reason</b>
							</div>
							<div class="col-xs-6" style="display:<?php echo ($appts['req']['status']==2?'block':'none'); ?>" >
								<?php echo $appts['req']['reject_reason']; ?> 
							</div> 
						</div>  
					</div> 
				  </div>
				  <p></p>
				  <?php if($appts['req']['get_feedback']== 1){ ?>
				  <div class="row" style="display:<?php echo ($appts['req']['status']>=3?'block':'none'); ?>">
				  <hr />
						<div class="col-xs-2"  >
							<b>Feedback</b>
						</div>
						<div class="col-xs-10">  
							<textarea rows="4" id="customer_feedback" name="customer_feedback" placeholder="Max. 300 characters..." class="form-control"  maxlength="300"></textarea>
							<span class="error" id="fErr"></span><br/>
							<button type="button" id="upd_feedback" class="btn btn-info btn-sm">Submit</button>
							<input type="hidden" id="id_appt_request" value="<?php echo $appts['req']['id_appt_request'] ?>" /> 
						</div> 
					</div>
					<?php } ?>					
		      	</div>
		    </div>
		</div>
	 </div>	   
   </div>
  </div>
</div>
</div>
<br/>
<br/>
<br/>