<?php $data=$this->login_model->company_details(); ?>
<link href="<?php echo base_url() ?>assets/css/pages/purchase.css" rel="stylesheet" type="text/css">
<div class="main-container">
<div  class="container-fluid " align="center">
<div class="purchaseForm" >
<div class="container-fluid header">
<div class="container">	
	<div class="row"> 
		<div class="col-md-12"> 
			<div align="center"><legend class="head">AKSHAYA TRITIYA  OFFER</legend></div>
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
			<p>You can now block your gold purchase at today’s price and take delivery of your jewellery when stores open after lockdown !
</p>
			<div class="">
				<div class="row"> 
	 				<div class="col-md-4"></div>
	 				<div class="col-md-4 box">  
						<div class="" align="center"> 
							<?php 
								$attributes 		=	array('id' => 'purchaseForm', 'name' => 'signupForm','autocomplete'=>'off');
								echo form_open_multipart('purchase/submitHDFCPay',$attributes);  
							?>
							<input type="hidden" name="type" id="type">
							<?php if($this->session->userdata("purch_cus_name") == ''){?> 
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
									<input type="text" id="firstname" name="firstname" class="form-control" placeholder="Name"  value="" required autofocus="true"/>
			                	</div>
							<?php } else{?>
								<input type="hidden" id="firstname" name="firstname" class="form-control" placeholder="Name"  value="<?php echo $this->session->userdata("purch_cus_name") ?>" required autofocus="true"/>
								<b style="font-size:15px;"><?php  echo 'Name : '. $this->session->userdata("purch_cus_name");?></b>
							<?php } ?> 
							<div class="" align="center">
						      <label> Delivery Preference</label>
						   	   &nbsp;<input type="radio" id="delivery_preference1" name="delivery_preference" value="0" checked> <label for="delivery_preference1">Ornament</label>
						   	   &nbsp;<input type="radio" id="delivery_preference2" name="delivery_preference" value="1"> <label for="delivery_preference2">Coin</label>  
						    </div>
						    <hr style="margin: 1%;"/>
							<div class="cus_form reg_form">
								<h5 class="sub-head">Plan A</h5>	
							<!--	<p>Book now by paying Rs.5000/- and get a discount of Rs.150/- per gram</p> --> 
								<div class="form-group">
							   	  <label class="pull-left"> Select Amount</label>
							   	  <?php 
							   	  $value='';
							   	    foreach($denominations as  $val){
							   	       $value.= '<option value="'.$val.'">Rs. '.$val.'</option>';
							   	    }
							   	  ?>
							      <select id="amount_select" class="form-control" placeholder="Select Amount"><?php echo $value;?></select> 
							    </div>   
								<div class="login-actions" align="center"> 
									<!--<label class="remember_me" for="a_terms"><input id="a_terms" name="a_terms" type="checkbox" class="checkbox-inline" value="Terms" required />&nbsp;&nbsp;Agree with <a class="terms theme-txt" href="<?php echo base_url('index.php/purchase/terms');?>" target="_blank">Terms & Conditions</a></label>-->
									<label class="remember_me" for="a_terms"><input id="a_terms" name="a_terms" type="checkbox" class="checkbox-inline" value="Terms" required />&nbsp;&nbsp;Agree with Terms & Conditions</a></label> 
								</div>
								<button type="button" id="pay_by_amt" class="reg_button buy_button"  value="1">Pay Rs.<span id="amt"></span></button> 
								<input type="hidden" name="planA_amt" id="planA_amt" value="">
							</div>   
							</div> <!-- /col -->
							<hr style="margin: 2%;"/>
							<p class="sub-head">Plan B</p>	
							<div class="cus_form reg_form">  
								<div class="form-group"> 
									<span class="pull-left"> <h5>Rate per Gram : &#8377;<strong class="reg_formrate" style="font-size: large;"> <?php echo $gold_rate?></strong></h5></span> 
									<input type="hidden" step="any" name="rate" id="rate" value="<?php echo $gold_rate?>"  placeholder="Rate Per Gram" required />
								</div> 
								<br/>
								<div class="form-group">
									<label class="pull-left">Amount</label>
									<input type="number" step="any" id="amount" name="amount" value="" class="form-control" placeholder="Amount" value="<?php echo $gold_rate?>"/>
									<span class="pull-right" id="amtErr" style="color: red"></span>
								</div>
								<div class="form-group">
								 	<label class="pull-left">Gram</label>
			                        <input type="text" id="weight" name="weight" class="form-control" placeholder="Gram" value="" />  
			                        <span class="pull-right" id="grmErr" style="color: red"></span>
			                    </div>  
								<div class="login-actions" align="center"> 
									<label class="remember_me" for="w_terms"><input id="w_terms" name="w_terms" type="checkbox" class="checkbox-inline" value="Terms" required />&nbsp;&nbsp;Agree with Terms & Conditions</a></label> 
								</div>
								<div class="form-group">
									<button type="button" id="buy_in_grams" class="reg_button buy_button"  value="2">Buy in Grams</button> 
								</div> 
							</div>   
						<!--<div class="login-actions" align="center"> 
									<label class="remember_me" for="terms"><input id="terms" name="terms" type="checkbox" class="checkbox-inline" value="Terms" required />&nbsp;&nbsp;Agree with <a class="terms theme-txt" href="https://www.jeweloneretail.in/jwlone_plan/index.php/user/terms" >Terms & Conditions</a></label> 
								</div>-->
						
					</div> <!-- /col -->
				</div> <!-- /row --> 
			</div><!-- /member_login -->
		</div><!--col-->	
	</div><!--row-->

	<?php if(sizeof($history) >0 ){	?>
	<div class="row"> 
		<div class="col-md-12"> 
			<p class="sub-head">PURCHASE HISTORY</p>
			<div class="table-responsive"> 
				<table id="scheme-amount" class="table table-bordered table-striped table-responsive display">
					<thead> 
						<tr>
							<th width="15%">Date</th>		 
							<th width="10%">TxnID</th>
							<th width="10%">Amount</th>									
							<th width="10%">Weight</th>	
							<th width="10%">Rate</th>	
							<th width="10%">Status</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($history as $data){
							$status = ($data['status'] == '1' || $data['status'] == '2' ? '<span class="label label-success">Success</span>':($data['status'] == '7' ? '<span class="label label-warning">Pending</span> <i rel="tooltip"  title="Your payment not yet realised, status will be updated after credited from bank." class="icon-question-sign help-icon"></i>':($data['status'] == '3' ? '<span class="label label-danger">Failed</span>':($data['status'] == '5' ? '<span class="label label-danger">Rejected</span> <i rel="tooltip"  title="May be your payment realisation got failed. Please contact administrator for details" class="icon-question-sign help-icon"></i>':'')))); 
							?>
							<tr>
    							<td><?php echo $data['date_add'] ?></td>
                                <td><?php echo $data['ref_trans_id'] ?></td>
                                <td><?php echo 'Rs '.$data['payment_amount'] ?></td>
                                <td><?php echo ($data['metal_weight'] > 0)?$data['metal_weight'].' g' : ''?></td>
                                <td><?php echo 'Rs '.$data['metal_rate'] ?></td>
                                <td><?php echo $status ?></td>
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
                <label>PlanA</label></br>
                1.You can pay Amount as given in Plan A column.</br>
                2.Paid Amount would be credited to your account as amount itself and it would be considered as Purchase Advance.</br>
                3.You can purchase Jewellery / Coin for the paid amount after shop opened at prevailing market rate on that day.</br>
                4.Gold rate, Wastage, Making charges, GST and any other VA applicable for the purchased items would be collected at the time of Purchase.
                </p>
            </div>
            <div id="planb" style="display:none;">
                <p style="text-align: justify;">
                <label>PlanB</label></br>
                1.Customers can book Gold for the paid Amount. </br>
                2.For the paid amount equivalant weight of Gold would be credited to customer's account at prevailing Market Gold Rate.</br>
                3.Customer can purchase Jewellery / Coin after shop opened.</br>
                4.Wastage, Making charges, GST and any other VA applicable for the purchased items would be collected  at the time of Purchase.</br>
                5.For Purchase more than Rs.2 Lakh, customers should provide PAN card at the time of purchase.</br>
                </p>
            </div>
    </div>
	<br/>
	<br/>
	<br/>
	<br/>
</div><!--container-->	
</div><!--container-fluid header--> 
 
</div>
</div>
<script type="text/javascript">
    var mob_no_len ="<?php echo $header_data['mob_no_len'];?>";   
  </script>