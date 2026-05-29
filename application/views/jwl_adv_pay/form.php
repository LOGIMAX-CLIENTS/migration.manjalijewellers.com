<?php $comp_data=$this->login_model->company_details(); ?>
<link href="<?php echo base_url() ?>assets/css/pages/jwl_adv_pay.css" rel="stylesheet" type="text/css">
<style>
    .plan-title{
        font-size :20px;
        color:#9d0b0f;
    }
</style>
<div class="main-container">
<!--<div  class="container-fluid " align="center">-->
<div class="JAPForm" >
<div class="container-fluid header">
<div class="container">	
    <?php if(!$this->session->userdata('is_logged_in')){?>
    <br/>
	<div class="row">
		<div class="col-md-12">
			<a class="btn btn-sm btn-warning pull-right" href="<?php echo base_url() ?>index.php/jwl_adv_pay/logout"><span>Logout</span> </a>
		</div>
	</div>
	<?php } ?>
	<div class="row"> 
		<div class="col-md-12"> 
			<div align="center"><legend class="head">ONLINE ADVANCE BOOKING</legend></div>
			<?php if($this->session->flashdata('successMsg')) { ?>
					<div class="alert alert-success" align="center">
					  <button type="button" class="close" data-dismiss="alert">&times;</button>
					  <strong><?php echo $this->session->flashdata('successMsg'); ?> </strong>
					</div>      
			<?php } else if($this->session->flashdata('errMsg')) {  ?>							 
					<div class="alert alert-danger" align="center">
					  <button type="button" class="close" data-dismiss="alert">&times;</button>
					  <strong><?php echo $this->session->flashdata('errMsg'); ?></strong>
					</div>
			<?php } ?>
			<!--<p>Book Jewellery Online and get protected from rise in Gold Rate and get upto 80% discount on MC.</p>-->
			<div class="">
				<?php
					$attributes 		=	array('id' => 'JAP_Form', 'name' => 'JAP_Form','autocomplete'=>'off');
					echo form_open_multipart('jwl_adv_pay/submitPay',$attributes);  
				?>
				<div class="row"> 
	 				<div class="col-md-3 box" >
 			        	<h5 class="sub-head">Customer Detail</h5>	
						<div class="cus_form reg_form" align="left">
							<input type="hidden" name="type" id="type">
							<?php if($this->session->userdata("jap_cus_name") == '' || $this->session->userdata("jap_cus_name") == ' '){?> 
							<div class="form-group" style="margin-bottom: 0px;"> 
								<div class="input-group">
			                        <span class="input-group-addon">
			                           <select name="title"> 
			                                <option value="Mr" selected>Mr</option>
			                                <option value="Ms">Ms</option>
			                                <option value="Mrs">Mrs</option>
			                                <option value="Dr">Dr</option>
			                                <option value="Prof">Prof</option>
			                            </select>
			                        </span> 
									<input type="text" id="firstname" name="firstname" class="form-control" placeholder="Name"  value="" required autofocus="true" style="text-transform:capitalize;"/>
									<span class="pull-right" id="nameErr" style="color: red"></span>
			                	</div>
			                	</div>
							<?php } else{?>
								<input type="hidden" id="firstname" class="form-control" placeholder="Name"  value="<?php echo $this->session->userdata("jap_cus_name") ?>" required autofocus="true"/>
								<b style="color: #841002;">Name : </b></br><?php  echo $this->session->userdata("jap_cus_name");?>
							<?php } ?>
						</div>
						<div class="cus_form reg_form" align="left">
							    <?php if($this->session->userdata("branch_name")==''){?>  
							<div class="form-group" style="margin-bottom: 0px;">
							    <label style="color: #841002;">Delivery Location*</label>
							    <select id="branch_select" class="form-control" step="any"></select>
							    <input type="hidden" name="id_branch" id="id_branch">
							</div> 
							<?php }else{?>
							    <b style="color: #841002;">Delivery Location : </b></br><?php echo $this->session->userdata("branch_name");?>
							    <input type="hidden" name="id_branch" id="id_branch" value="<?php echo $this->session->userdata("jap_id_branch");?>">
							<?php }?>
						</div>
						<div class="cus_form reg_form" align="left">
							    <?php if($this->session->userdata("email")==''){?>  
							<div class="form-group" style="margin-bottom: 0px;">
							    <label style="color: #841002;">Email ID </label>
							    <input type="text" id="email" name="email" class="form-control" placeholder="Email ID"  value=""/>
							</div> 
							<?php }else{?>
							    <input type="hidden" id="email" value="<?php echo $this->session->userdata("email") ?>" />
							    <b style="color: #841002;">Email ID : </b></br><?php  echo $this->session->userdata("email");?></span>
							<?php }?>
						</div>
						<div class="cus_form reg_form" align="left">
							    <?php if($this->session->userdata("alter_mobile")==''){?>  
							<div class="form-group" style="margin-bottom: 0px;">
							    <label style="color: #841002;">Alternative Mobile</label>
							    <input type="number" id="alter_mobile" name="alter_mobile" class="form-control" placeholder="Alternative Mobile Number"  value=""/>
							    <span class="pull-right" id="altermobErr" style="color: red"></span>
							</div> 
							<?php }else{?>
							    <b style="color: #841002;">Alternative Mobile : </b></br><?php echo $this->session->userdata("alter_mobile");?>
							<?php }?>
						</div>
	 				</div> 
					<div class="col-md-5 box" align="center">
					    <p>Book Jewellery Online and get protected from rise in Gold Rate and get upto 80% discount on MC.</p>
						<div id="offers" data-offerarr='<?php echo json_encode($offers); ?>'></div>
						<input type="hidden" name="delivery_preference" value="1" id="delivery_preference">
						<input type="hidden" step="any" name="rate" id="rate" value="<?php echo $gold_rate?>" />
	    				<input type="hidden" step="any" name="silver_1g" id="silver_1g" value="<?php echo isset($silver_rate) ? $silver_rate : 0 ?>" />
	    				<input type="hidden" id="mc_disc_percent" name="mc_disc_percent" />
	    				<input type="hidden" id="offer_name" name="offer_name" />
						<!--<h5>Pay Advance now and Avail offer on Making Charge !!</h5>-->
	    				<div class="row">
		    				<div class="col-md-2"></div>
		    				<div class="col-md-8">	    				
								<div class="cus_form reg_form" style="width: fit-content;">  
									<h5>Gold Rate per G : &#8377;<strong class="reg_formrate"> <span id="branch_rate"><?php echo ($gold_rate == 0 ? '-':$gold_rate)?></span></strong></h5> 
									<div class="form-group">
									 	<label class="pull-left">Weight (G) *</label>
				                        <input type="number" step="0.001" id="jap_weight" name="weight" class="form-control" placeholder="Gram" value="40" />  
				                        <span class="pull-right" id="grmErr" style="color: red"></span>
				                    </div>
									<div class="form-group">
										<label class="pull-left">No. Of Months *</label>
										<select id="no_of_month" name="no_of_month" class="form-control"> 
				                            <?php 
				                            foreach($offers['data'] as $k=>$vl) {
				                                echo "<option>".$k."</option>";
				                            } ?>
				                        </select> 
										<span class="pull-right" id="monthsErr" style="color: red"></span>
									</div>
				                    <div class="form-group">
									 	<label class="pull-left">Advance Payment *</label>
				                        <select id="adv_percent" name="adv_percent" class="form-control">
				                            <?php foreach($offers['data'][1] as $item) {
				                                if(1 >= $item['min_wgt'] &&  39.999 <= $item['max_wgt']){
				                                    echo "<option value=".$item['adv'].">".$item['adv']."%</option>";
				                                }
				                            } ?>
				                        </select> 
				                        <span class="pull-right" id="advErr" style="color: red"></span>
				                    </div>
				                    <div class="form-group">
									 	<label class="pull-left">Advance Amount</label>
				                        <input type="number" step="any" id="amount" name="amount" class="form-control" placeholder="Advance Amount" value="" readonly=""/>
				                    </div> 
				                    <div class="form-group">
									 	<label class="pull-left">Total Gold Value</label>
				                        <input type="number" step="any" id="purchase_amount" name="purchase_amount" class="form-control" placeholder="Purchase Amount" value="" readonly=""/>
				                    </div> 
									<div class="form-group" style="color: darkorange;font-size: 15px;">
										<b id="disp_mc_disc_percent"></b>
									</div> 
									<div class="login-actions" align="center"> 
										<label class="remember_me" for="a_terms"><input id="a_terms" name="a_terms" type="checkbox" class="checkbox-inline" value="Terms" required />&nbsp;&nbsp;I have Read & Agree with Terms & Conditions</a></label> 
									</div>
									<div class="form-group">
										<button type="button" id="pay_now" class="reg_button buy_button">Pay Now</button> 
									</div> 
								</div> 
							</div> 
						</div> 
					</div> 
					<div class="col-md-3" align="center">
						<h5 class="sub-head"><i class="icon-phone-sign"></i> Contact</h5>
					    <h4> Palakkad: <a href="tel:7025273916">7025273916</a></h4> 
	                    <h4> Attingal: <a href="tel:9447162261">9447162261</a></h4> 
	                    <br/><br/>
						<h5 class="sub-head"><i class="icon-envelope"></i> E-Mail</h5> 
						<p> manjalyplkd@gmail.com  <br/>  manjalyatl@gmail.com</p> 
				    </div>
				</div> <!-- /row -->
			</div><!-- /member_login -->
		</div><!--col-->	
	</div><!--row-->
 	<?php if(sizeof($history) >0 ){	?>
	<div class="row"> 
		<div class="col-md-12"> 
			<p class="sub-head">BOOKING HISTORY</p>
			<div class="table-responsive"> 
				<table id="bookings" class="table table-bordered table-striped table-responsive display">
					<thead> 
						<tr>
						    <th width="15%">Booking No</th>
							<th width="10%">Metal</th>
							<th width="10%">Rate</th>
							<th width="15%">Date</th>
							<th width="15%">Advance Paid</th>									
							<th width="10%">Weight</th>	
							<th width="10%">Disc on MC</th>			 
							<th width="10%">TxnID</th>	
							<th width="10%">Status</th>
							<th width="10%">Acknowledgement</th> 
						</tr>
					</thead>
					<tbody>
						<?php foreach ($history as $data){
							$status = ($data['status'] == '1' || $data['status'] == '2' ? '<span class="label label-success">Success</span>':($data['status'] == '7' ? '<span class="label label-warning">Pending</span> <i rel="tooltip"  title="Your payment not yet realised, status will be updated after credited from bank." class="icon-question-sign help-icon"></i>':($data['status'] == '3' ? '<span class="label label-danger">Failed</span>':($data['status'] == '5' ? '<span class="label label-danger">Rejected</span> <i rel="tooltip"  title="May be your payment realisation got failed. Please contact administrator for details" class="icon-question-sign help-icon"></i>':'')))); 
							?>
							<tr>
							    <td><?php echo $data['id_purch_payment'] ?></td>
							    <td><?php echo "Gold"?></td>
                                <td><?php echo 'Rs '.$data['metal_rate'] ?></td>
    							<td><?php echo $data['date_add'] ?></td>
                                <td><?php echo 'Rs '.$data['payment_amount']." (".$data['adv_paid_percent']."%)" ?></td>
                                <td><?php echo ($data['metal_weight'] > 0)?$data['metal_weight'].' g' : ''?></td>
                                <td><?php echo ($data['disc_mc_percent'] > 0 ? $data['disc_mc_percent']."%" : '-') ;?></td>
                                <td><?php echo $data['ref_trans_id'] ?></td>
                                <td><?php echo $status ?></td>
                                <?php if($data['status'] == '1' || $data['status'] == '2'){?>
                                <td><a href="<?php echo base_url()?>index.php/jwl_adv_pay/get_acknowladge/<?php echo $data['id_purch_payment']?>" target="_blank">Download</a></td>
							    <?php }else{?>
							    <td>-</td>
							    <?php }?>
							</tr> 
						<?php } ?> 
					</tbody>
				</table>
			</div> 
		</div><!--col-->	
	</div><!--row-->
	<?php } ?>
    <div class="col-md-12" id="terms" style="display:none;">
        <p style="text-align: justify;"><label>Terms and Conditions</label> </p>
            <div id="plana" style="display:none;">
                <p style="text-align: justify;">
                1. If the price increases at the time of purchase, get the jewellery at booked rate  or prevailing rate whichever is lower.</br>
                2. Advance is not transferable.</br>
                3. Refund is not admissible.</br>
                4. If the purchase is not made within the stipulated period,  benefit of lower gold rate will be applicable only for gold weight equivalent to advanced amount.</br>
                5. Quantity of gold for which advance is made should be purchased.<br/>
                6. This advance is for purchase of ornaments, not applicable on gold coins.<br/>
                7. Making charge will be calculated based on the billing day's gold rate and it should be paid at the time of purchase.<br/>
                8. Discount is applicable on Kerala and Bengali Jewellery only<br/>
                </p>
            </div>
            <div id="planb" style="display:none;">
                <p style="text-align: justify;">
                </br>
                </p>
            </div>
    </div>
	<br/>
	<br/>
	<br/>
	<br/>
	<div id="spinner" style="display:none;font-size: 20px; position: absolute;top: 0%; z-index: 60;width: 100%;height: 100%;left: 0%;background: rgba(255,255,255,0.7);">
					<i class="fa fa-refresh fa-spin" style="margin-top: 40%;"></i>
				</div>
</div><!--container-->	
</div><!--container-fluid header--> 
 
</div>
</div> 