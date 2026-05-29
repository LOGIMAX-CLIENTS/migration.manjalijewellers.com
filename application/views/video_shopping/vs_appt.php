<link href="<?php echo base_url() ?>assets/css/pages/video_shopping.css" rel="stylesheet"></link>
<style>
	/*.modal-header{
		background: #f99704;
	}*/
	.popover{
		min-width: min-content !important;
	}
	.popover-title{
		background: #fff;
		color: #fba017;
		font-weight: 400;
		text-transform: none;
	}
	.popover-content{
		/*display: content;*/
		padding: 9px 6px !important;
	}
	#slotArea{
/*		width: 500px;*/
		max-height: 1000px;
		border:  2px solid #eee;
		padding:  5px; 
		margin: auto;
	}
	.date-col{ 
		background: #07b4ed;
		color: #fff;
		border-bottom: 2px solid #fff;
		border-right: 2px solid #fff; 
		text-align: center;
		text-transform: uppercase;
		font-size: 11px !important;
		padding: 5px !important;
		width: 20% !important;
	}
	.date-col a{  
		color: #fff;
		text-decoration:none !important;
		text-transform: uppercase;
		font-size: 11px !important; 
	}
	.date-col p{ 
		font-size: 11px !important;
	}
	
	.time-col-btn button:hover,.time-col-btn:hover{ 
		background: #fba017 !important;  
		border: none !important;  
		cursor: pointer; 
		color: #fff !important;
	} 
	.time-col-btn{ 
		/*margin: 5px 0px 5px;
	    padding: 5%;*/
	    border: none;
	    color: #786a6a;
    	background: transparent;
    	border: 1px solid #bdc8cc;
    	margin-right: 2px;
	}
	#sel_slot{
		font-size: 14px; 
	    line-height: 3;
	    letter-spacing: 1px; 
	    color: #fba017;
	}
</style>
<div class="main-container">
<!-- main -->		  
<div class="main"  id="vs_appt"> 
  <div class="main-inner"> 
    <div class="container">
      <div class="row">
        <div class="col-md-12">
			<div align="center"><legend class="head">Book the video shopping  Appointment</legend></div> 
	  		<!-- alert -->
			<?php
			if($this->session->flashdata('successMsg')) { ?>
				<div class="alert alert-success" align="center">
				  <button type="button" class="close" data-dismiss="alert">&times;</button>
				  <strong><?php echo $this->session->flashdata('successMsg'); ?> </strong>
				</div>      
			<?php } else if($this->session->flashdata('errMsg')) { ?>							 
				<div class="alert alert-danger" align="center">
				  <button type="button" class="close" data-dismiss="alert">&times;</button>
				  <strong><?php echo $this->session->flashdata('errMsg'); ?>
				</div>
			<?php } ?>	
			<?php 
			$attributes = array('id' => 'vs_appt_form', 'name' => 'vs_appt_form', 'class' => 'vs_form');
			echo form_open('vs_appt_book/vs_appt',$attributes)  ?>
			
			<?php if($content['cusData']['mobile'] != '' || $this->session->userdata('vs_mobile') != ''){ ?> 
				<div class="row" align="right">
					<div class="col-sm-11 p-2">
						<a href="<?php echo base_url('index.php/vs_appt_book/vs_appt_list'); ?>" class="btn btn-mini btn-warning pull-right" data-toggle="modal">My Bookings</a>
					</div>
				</div> 
			<?php }?>
			<?php if(sizeof($content['slots'])>0){?> 
			<!--Personal Details-->
			<div class="row">        
				<div class="col-sm-offset-1 col-sm-4" >
					<legend>Personal Details</legend>
					<div class="row"> 
						<div class="col-sm-10 margin-b-5">
							<label for="name"> <b>Name<span class="error"> * </span> </b></label>
							<div>
								<input type="text" class="form-control" id="name" name="vs_appt_form[name]" value="<?php echo ($content['cusData']['name'] != '' ? $content['cusData']['name']:''); ?>"   placeholder=" Name" required="true" autocomplete="off">
								<span id="nameErr" class="error"></span>
							</div>
						</div> 
					</div> 
					<div class="row">
						<div class="col-sm-4 margin-b-5">
							<label for="mobile"><b>Mobile <span class="error">  *</span></b></label>     
							<div>
								<input id="mobile" name="vs_appt_form[mobile]" placeholder="10 digit mobile no." value="<?php echo ($content['cusData']['mobile'] != '' ? $content['cusData']['mobile']:$this->session->userdata('vs_mobile')); ?>" class="form-control"  required type="hidden" autocomplete="off"/>
								<p><?php echo ($content['cusData']['mobile'] != '' ? $content['cusData']['mobile']:$this->session->userdata('vs_mobile')); ?> </p>
								</div> <!-- /controls -->
						</div> 
						<div class="col-sm-6 margin-b-5">											
							<label for="name"> <b>Whatsapp No.</b></label>
							<div>
								<input type="text" class="form-control" id="whats_app_no" name="vs_appt_form[whats_app_no]" placeholder="Your Whatsapp No." autocomplete="off" value="<?php echo ($content['cusData']['mobile'] != '' ? $content['cusData']['mobile']:$this->session->userdata('vs_mobile')); ?>"/>
								<span id="waErr"  class="error"></span>
							</div>
						</div>  
					</div> 
					<div class="row">
						<div class="col-sm-10 margin-b-5">											
							<label for="name"> <b>Email ID</b></label>
							<div>
								<input type="email" class="form-control" id="email" name="vs_appt_form[email]" placeholder="Your Email ID" value="<?php echo ($content['cusData']['email'] != '' ? $content['cusData']['email']:''); ?>" autocomplete="off"/>
								<span id="emailErr"  class="error"></span>
							</div>
						</div>
					</div> 
					<div class="row">
						<div class="col-sm-10 margin-b-5">
							<label for="name"> <b>Location</b></label>
							<div>
								<input type="text" class="form-control" id="location" name="vs_appt_form[location]" placeholder="Your Location." autocomplete="off"/>
								<span id="waErr"  class="error"></span>
							</div>
						</div>
					</div>
					<div class="row"> 
		                <div class="col-sm-10 margin-b-5">											
							<label><b>Message </b></label>          
							<div>
								<textarea rows="4" id="description" name="vs_appt_form[description]" placeholder="Max. 300 characters..." class="form-control"  maxlength="300" ></textarea>
								<span id="msgErr"  class="error"></span>
							</div>
						</div> 
					</div>
				</div> 
				<div class="col-sm-6">
					<legend>Appointment Details</legend>
					<div class="row">  
						<div class="col-sm-5 margin-b-5">											
							<label for="name"> <b>Preferred Category <span class="error">  *</span></b></label>
							<div>
								<select class="form-control" id="category" name="vs_appt_form[pref_category]" required="">
									<option value="">-- Choose --</option>
									<option value="1">Gold</option>
									<option value="2">Silver</option>
									<option value="3">Platinum</option>
									<option value="4">Diamond</option>
								</select>
								<span id="catErr"  class="error"></span>
							</div>
						</div>
						<div class="col-sm-5 margin-b-5">											
							<label for="name"> <b>Preferred Item </b></label>
							<div>
								<input type="text" class="form-control" id="pref_item" name="vs_appt_form[pref_item]" placeholder="Your Preferred Item" autocomplete="off"/>
								<span id="itemErr"  class="error"></span>
							</div>
						</div>
					</div> 
					<div class="row">        
						<div class="col-sm-12 col-xs-12 col-md-10">       
							<label> Choose Preferred Slot <span class="error"> * </span></label> 	
							<i>( Choose your preferred appointment date and time )</i>  
							<span id="pslotErr"  class="error"></span>
							<input type="hidden" value="" id="pref_slot" name="vs_appt_form[pref_slot]" required=""/>				 	
							<div id="sel_slot" align="center"><span id='sel_date'></span> <span id='sel_time'></span></div>
							<div class="row" id="slotArea">
		             			<?php foreach($content['slots'] as $key => $val){   
		             				$slotData = "";
									$date=date_create($key);
									$n = date_format($date,"w");
									$day = ($n == 0 ? 'Sun':($n == 1 ? 'Mon':($n == 2 ? 'Tue':($n == 3 ? 'Wed':($n == 4 ? 'Thu':($n == 5 ? 'Fri':'Sat')))))); 
							  		// Create Slots in popup 
							  		foreach($val as $slot){   
									  $slotData = $slotData."<button type='button' class='".$key." btn btn-sm btn-default time-col-btn' value='".$slot['id_appointment_slot']."' id='t".$slot['id_appointment_slot']."'>".$slot['time_from']." <br/><span style='font-size: x-small'>To</span><br/>".$slot['time_to']."<input type='hidden' id='tval".$slot['id_appointment_slot']."' value='".$slot['time_from']." - ".$slot['time_to']."'/></button>"; 
								  } ?> 
								  <div class="col-md-2 col-sm-2 col-xs-2 date-col" align="center" id="<?php echo $slot['id_appointment_slot'] ;?>">
		             			 <!-- <a class="slotDayBox" id="<?php echo $slot['id_appointment_slot'] ;?>" href="#" data-toggle="popover" title="Time" data-html="true"  data-content="<?php echo $slotData ;?>" rel="tooltip" data-placement="auto" data-trigger="focus" >--> 
		             			 <span style="display: none;" id="slotData_<?php echo $slot['id_appointment_slot'] ;?>"><?php echo $slotData;?></span>
		             			  <?php
		             			  	echo "<p>".$day."</p>";
									echo "<span>".date_format($date,"d M Y")."<span>";
									echo "<input type='hidden' class='date_val' id='d_val".$slot['id_appointment_slot']."' value='".$day.", ".date_format($date,"d M Y")."'/>";
								  ?>
								 <!-- </a> -->
								  </div>   
			                   <?php } ?> 
							</div>							
						</div>  
					</div>  
					</div>					
				</div>  
				<div class="col-sm-1"></div>  
			</div>   
			<p></p>  
			<p></p>  
			<div class="update_submit" align="center">
				<p id="err" class="txt-11 error"></p>
			    <button type="submit" id="vs_appt_submit" class="btn btn-info">Book Now</button>
			</div> 
			<?php }else{?>
				<div class="row">
					<div class="col-md-12">
						<div class="alert alert-danger" align="center">
						  <button type="button" class="close" data-dismiss="alert">&times;</button>
						  <?php echo "No appointment slots available!!"; ?>
						</div>
					</div>
				</div>
			<?php }?>
		</div>
    </div>
    <!-- /container --> 
  </div>
  <!-- /main-inner --> 
</div>
<!-- /main -->	
</div>
<div id="time_slot_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header ">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="myModalLabel"> &nbsp;Available Time Slots</h3>
			</div>
			<div class="modal-body">
				<p>Choose your preferred time <i class='icon-time'></i></p> 
				<div id="slotTimeArea">
					
				</div>

			</div>
			<div class="modal-footer">
				<input style="margin:1%" type="button" value="Proceed" id="time_slot_sub" style="background-color:#0079C0"  class="button btn btn-primary btn-large" />
			</div>
		</div>
	</div>
</div>
 