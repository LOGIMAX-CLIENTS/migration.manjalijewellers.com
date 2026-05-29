<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
		Influencer
			<small>Tag</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="#">Loyalty</a></li>
			<li class="active">Influencer</li>
		</ol>
	</section>
    <!-- Main content -->
	<section class="content loyalty-settings">
		<!-- Default box -->
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">Influencer</h3>
				<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
				<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
				</div>
			</div>
			<div class="box-body">
				<!-- form container -->
				<div class="row">
					<!-- form -->
					<?php
					$id = $records['id_influencer_settings'];
					echo form_open('admin_ret_loyalty/influencer_DBO/'.$type.'/'.$id, array('id'=> "influencer_form",'method'=>'post')); ?>
					<div class="col-sm-12"> 
						<legend class="sub-title">Settings Details</legend>
						<div class="row">
							<label for="name" class="col-md-2 col-md-offset-1">Template Name </label>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group ">
									 	<input type="test" class="form-control decimalsInput" id="name" name="name" placeholder="Enter Template Name" value="<?php echo $records['name'] ?>" autocomplete='Off'> 
										<p class="help-block"></p>
									</div>
								</div>
							</div>
							<label for="type_influencer" class="col-md-2">Type</label>
							<div class="col-md-3">
								<input type="radio" id="type_influencer" name="type" value="1" <?php echo $records['type'] == 1 ? "checked" : '' ?>  /> &nbsp; <label for="type_influencer"> Influencer</label> &nbsp;
								<input type="radio" id="type_referral" name="type" value="2" <?php echo $records['type'] == 2 ? "checked" : '' ?> />  &nbsp; <label for="type_referral"> Referral</label>
								<p class="help-block"></p>
							</div>
						</div>
						<div class="row">
							<label for="point_type_point" class="col-md-2 col-md-offset-1 ">Point Type</label>
							<div class="col-md-3">
								<input type="radio" id="point_type_point" name="point_type" value="1" <?php echo $records['point_type'] == 1 ? "checked" : '' ?> /> &nbsp; <label for="point_type_point"> Point</label> &nbsp;
								<input type="radio" id="point_type_cash" name="point_type" value="2" <?php echo $records['point_type'] == 2 ? "checked" : '' ?> />  &nbsp; <label for="point_type_cash"> Cash</label>
								<p class="help-block"></p>
							</div>
							<label for="earning_rule_point" class="col-md-2 ">Accumulate Type </label>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group ">
										<input type="radio" id="accumulate_type_perc" name="accumulate_type" value="1" <?php echo $records['accumulate_type'] == 1 ? "checked" : '' ?> /> &nbsp; <label for="accumulate_type_perc"> %</label> &nbsp;
										<input type="radio" id="accumulate_type_value" name="accumulate_type" value="2" <?php echo $records['accumulate_type'] == 2 ? "checked" : '' ?> />  &nbsp; <label for="accumulate_type_value"> value</label>
										<p class="help-block"></p>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<label for="earning_rule_value" class="col-md-2 col-md-offset-1">Earning rule value </label>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group ">
									 	<input type="number" class="form-control decimalsInput" id="earning_rule_value" name="earning_rule_value" placeholder="Enter earning rule value" value="<?php echo $records['earning_rule_value'] ?>"> 
										<p class="help-block"></p>
									</div>
								</div>
							</div>
							<label for="earning_rule_point" class="col-md-2 ">Earning rule point </label>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group ">
										<input type="number" class="form-control numberOnly" id="earning_rule_point" name="earning_rule_point" placeholder="Enter earning rule point" value="<?php echo $records['earning_rule_point'] ?>" />
										<p class="help-block"></p>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<label for="expiration_yes" class="col-md-2 col-md-offset-1"> Expiration? </label>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<input type="radio" id="expiration_yes" name="expiration" <?php echo $records['expiration'] == 1 ? "checked" : '' ?> value="1" /> &nbsp; <label for="expiration_yes"> Yes</label> &nbsp;
										<input type="radio" id="expiration_no" name="expiration" <?php echo $records['expiration'] == 0 ? "checked" : '' ?> value="0" />  &nbsp; <label for="expiration_no"> No</label>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<label for="expire_type_days" class="col-md-2 col-md-offset-1">Expiration Type  </label>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group ">
										<input type="radio" id="expire_type_days" name="expire_type" <?php echo $records['expire_type'] == 1 ? "checked" : '' ?> value="1" /> &nbsp; <label for="expire_type_days"> Days</label> &nbsp;
										<input type="radio" id="expire_type_month" name="expire_type" <?php echo $records['expire_type'] == 2 ? "checked" : '' ?> value="2" />  &nbsp; <label for="expire_type_month"> Month</label> &nbsp;
										<input type="radio" id="expire_type_year" name="expire_type" <?php echo $records['expire_type'] == 3 ? "checked" : '' ?> value="3" />  &nbsp; <label for="expire_type_year"> Year</label>
									</div>
								</div>
							</div>
							<label for="expire_after" class="col-md-2"> Expire after(No. Days or Month or Year) </label>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
									<input class="form-control numberOnly" id="expire_after" name="expire_after" type="number" autocomplete="off" value="<?php echo $records['expire_after'] ?>" />
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<label for="type_influencer" class="col-md-2 col-md-offset-1 ">Point based on category ?</label>
							<div class="col-md-3">
								<input type="radio" id="point_based_category_yes" name="point_based_category" value="1"  <?php echo $records['point_based_category'] == 1 ? "checked" : '' ?>  /> &nbsp; <label for="point_based_category_yes"> Yes</label> &nbsp;
								<input type="radio" id="point_based_category_no" name="point_based_category" value="0" <?php echo $records['point_based_category'] == 0 ? "checked" : '' ?>  />  &nbsp; <label for="point_based_category_no"> No</label>
								<p class="help-block"></p>
							</div>
						</div>
						<legend class="sub-title has_category" <?php if($records['point_based_category'] == 0) { ?> style="display:none" <?php } ?> >Category Details</legend>
						<div class="row has_category" <?php if($records['point_based_category'] == 0) { ?> style="display:none" <?php } ?>>
							<div class="col-md-9 col-md-offset-1">
								<table class="category_table table" >
								<thead>
									<th>Category</th>
									<th>Earning Rule Value</th>
									<th>Earning Rule Point</th>
								</thead>
								<tbody>
								<?php foreach($category as $cat) { ?>
									<tr>
										<td><?php echo $cat['name'] ?><input type="hidden" value="<?php echo $cat['id_ret_category'] ?>" name="category_id[]" /></td>
										<td><input type="text" class="form-control" value="<?php echo $cat['category_earning_rule_value'] ?>" name="category_earning_rule_value[]" /></td>
										<td><input type="text" class="form-control" value="<?php echo $cat['category_earning_rule_point'] ?>" name="category_earning_rule_point[]" /></td>
									</tr>
								<?php } ?>
								</tbody>
								</table>
							</div>
						</div>
						<p class="help-block"></p>
					</div>	<!--/ Col --> 
				</div>	 <!--/ row -->
				<div class="row">
					<div class="box box-default"><br/>
						<div class="col-xs-offset-5">
							<button type="submit" class="btn btn-primary">Save</button> 
							<button type="button" class="btn btn-default btn-cancel">Cancel</button>
						</div> <br/>
					</div>
				</div>
			</div>  
			<?php echo form_close();?>
			<div class="overlay" style="display:none">
				<i class="fa fa-refresh fa-spin"></i>
			</div>
			<!-- /form -->
		</div>
	</section>
</div>