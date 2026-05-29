<link href="<?php echo base_url() ?>assets/css/pages/changeUser.css" rel="stylesheet">
<div class="main-container">
	<!-- main -->		  
	<div class="main"  id="resetPass">
		<!-- main-inner --> 
			<div class="main-inner">
				<!-- container --> 
				<div class="container">
				<!-- alert -->
				<div class="row">
					<div class="col-md-12">
						<div align="center"><legend class="head">Coin Enquiry</legend></div>
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
						<div class="mainDiv">
							<p class="description">PLEASE FILL THE COIN ENQUIRY</p>
							<?php 
								$attributes = array('id' => 'coin_enq_form', 'name' => 'coin_enq_form');
								echo form_open('user/coin_enquiry',$attributes)  ?>
							<div class="col-md-12">
							<?php 
							if($this->session->userdata('username')) { ?>
								<div class="col-md-4 row marginBottom15" style= "float: right; margin-left: 50%; margin-top: -0em; margin-right: -0em; padding: 6px">	
									<td><a href="<?php echo base_url('index.php/user/coin_enq_details/'.$content['mobile'])?>"  class="btn btn-warning btn-single btn-mini pay_submit" align="left" >View Enquiries</a></td>
								</div> 
								<?php } ?>	
								<div class="col-md-12 row marginBottom15">											
									<label class="col-md-4" for="name"> <b>Name<span class="error"> * </span> </b></label>
									<div class="col-md-8">
										<input type="text" class="form-control" id="name" name="coin_enq_form[name]" value="<?php echo $content['name']; ?>"   placeholder=" Name" required="true">
										<span id="nameErr" class="error"></span>		
									</div> 				
								</div>  
								<div class="col-md-12 row marginBottom15">											
									<label class="col-md-4" for="mobile"><b>Mobile <span class="error">  *</span></b></label>
									<div class="col-md-8">
										<input id="mobile" name="coin_enq_form[mobile]" placeholder="10 digit mobile no." value="<?php echo $content['mobile']; ?>" class="form-control"  required/ >
									<span id="mobileErr" class="error"></span>
									</div> <!-- /controls -->				
								</div> 
								<div class="col-md-12 row marginBottom15">
									<label class="col-md-4" for="coin"> <b>Coin <span class="error">  *</span></b></label>
									<div class="col-md-8">
										<select id="10"  class="form-control" name="coin_enq_form[gram]" required="true"/>
											<option value="0">Select the Coin </option> 
											<option value="1" <?php if($content['gram'] == 1){ ?> selected="true" <?php } ?>>1 Gram</option>
											<option value="2" <?php if($content['gram'] == 2){ ?> selected="true" <?php } ?>>2 Gram</option>
											<option value="4" <?php if($content['gram'] == 4){ ?> selected="true" <?php } ?>>4 Gram</option>
											<option value="8" <?php if($content['gram'] == 8){ ?> selected="true" <?php } ?>>8 Gram</option>
											<option value="10" <?php if($content['gram'] == 10){ ?> selected="true" <?php } ?>>10 Gram</option>
										</select>
									</div> <!-- /controls -->				
								</div> 
								<div class="col-md-12 row marginBottom15">
									<label class="col-md-4" for="coin_type"> <b>Coin Type <span class="error">  *</span></b></label>
									<div class="col-md-8">
										<select id="2"  class="form-control" name="coin_enq_form[coin_type]" required="true"/>
											<option value="0">Select the Coin Type</option> 
											<option value="1" <?php if($content['coin_type'] == 1){ ?> selected="true" <?php } ?>>With Neck</option>
											<option value="2" <?php if($content['coin_type'] == 2){ ?> selected="true" <?php } ?>>Without Neck</option>
										</select>
									</div> <!-- /controls -->				
								</div>
								<div class="col-md-12 row marginBottom15">
									<label class="col-md-4" for="quantity"><b>Quantity <span class="error">  *</span></b></label>
									<div class="col-md-8">
										<input type="number" class="form-control" id="qty" name="coin_enq_form[qty]"  value="<?php echo $content['qty']; ?>"  placeholder="Enter Quantity"/>
										<span id="qtyErr" class="error"></span>	
									</div> <!-- /controls -->				
								</div> 
								<div class="col-md-12 row marginBottom15">
									<label class="col-md-4" for="comments"><b>Message <span class="error">  *</span></b></label>          
									<div class="col-md-8">
										<textarea rows="4" id="comments" name="coin_enq_form[comments]" placeholder="Max. 250 characters..." class="form-control"  maxlength="300" ></textarea>
										<span id="msgErr"  class="error"></span>
									</div>
								</div>
							</div>
							<p id="err" class="txt-11 error"></p>
							<div class="update_submit">
								<button type="submit" id="coin_enq_submit" class="button btn">Save</button>
							</div>
							</form>
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
<script type="text/javascript">
page = "resetPass"
</script>