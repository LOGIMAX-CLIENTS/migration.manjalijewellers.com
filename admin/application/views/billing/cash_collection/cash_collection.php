      <!-- Content Wrapper. Contains page content -->
      <style>
      	.remove-btn {
      		margin-top: -168px;
      		margin-left: -38px;
      		background-color: #e51712 !important;
      		border: none;
      		color: white !important;
      	}

      	.sm {
      		font-weight: normal;
      	}

      	#denomination input[type=text],
      	#denomination input[type=number],
      	#denomination button {
      		height: 25px !important;
      		padding: 1px 5px !important;
      	}
      </style>
      <div class="content-wrapper">
      	<!-- Content Header (Page header) -->
      	<section class="content-header">
      		<h1>
      			Cash Collection
      		</h1>
      		<ol class="breadcrumb">
      			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      			<li><a href="#">Billing</a></li>
      			<li class="active">Cash Collection</li>
      		</ol>
      	</section>

      	<!-- Main content -->
      	<section class="content order">

      		<!-- Default box -->
      		<div class="box box-primary">

      			<div class="box-body">


      				<?php
						if ($this->session->flashdata('chit_alert')) {
							$message = $this->session->flashdata('chit_alert');
						?>
      					<div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
      						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      						<h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
      						<?php echo $message['message']; ?>
      					</div>

      				<?php } ?>

      				<form id="cash_coll_form">

      					<div class="row">
      						<div class="col-md-offset-1 col-md-10">
      							<div class="box box-default">
      								<div class="box-body">
      									<div class="row">

      										<?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>
      											<div class="col-md-3">
      												<label>Branch</label>
      												<select class="form-control" name="cash[id_branch]" id="branch_select" style="width:100%;"></select>
      											</div>
      										<?php } else { ?>
      											<input type="hidden" name="cash[id_branch]" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">
      										<?php } ?>

      										<div class="col-md-2">

      											<div class="form-group">

      												<label>Date</label>

      												<div class="input-group">

      													<input type="text" id="cash_coll_date" class="form-control" id="receipt_date" data-date-format="dd-mm-yyyy" placeholder="Select Date" name="cash[coll_date]" readonly></input>

      												</div>

      											</div><!-- /.form group -->

      										</div>

      										<div class="col-md-2">

      											<div class="form-group tagged">

      												<label>Select Counter</label>

      												<select id="counter_sel" name="cash[id_counter]" class="form-control" style="width:100%;"></select>

      											</div>

      										</div>

      										<div class="col-md-3">

      											<label>Cash Type </label><br>

      											<input type="radio" name="cash[cash_type]" value="1" id="cash_type_1"> &nbsp;

      											<label for="cash_type_1">CRM</label> &nbsp;

      											<input type="radio" name="cash[cash_type]" value="2" id="cash_type_2" checked=""> &nbsp;

      											<label for="cash_type_2">Retail</label>

												<input type="radio" name="cash[cash_type]" value="3" id="cash_type_3" > &nbsp;

												<label for="cash_type_3">All</label>

      										</div>

      										<div class="col-md-1">
      											<label></label>
      											<div class="form-group">
      												<button type="button" id="cash_coll_search" class="btn btn-info">Search</button>
      											</div>
      										</div>
      									</div>
      								</div>
      							</div>
      						</div>
      					</div>

      					<div class="row" style="display:none;">
      						<div class="col-md-offset-3 col-sm-3" style="text-align: right;">
      							<h4><b>Cash Amount :</b>
      								<h4>
      						</div>
      						<div class="col-sm-6">
      							<h4><b><span  class="cash_amount"></span></b></h4>
								<input type="hidden" id="cash_amount" name="cash[cash_amount]">
      						</div>
      					</div>
      					<br>

      					<div class="row">
      						<div class="col-md-6">
      							<legend>Denomination</legend>

      							<div class="table-responsive">
      								<table class="table table-bordered" id="denomination">
      									<tbody>
      										<?php if (count($denomination) > 0) {
													foreach ($denomination as $d) { ?>
      												<tr>
      													<th width="45%" class="text-right"><?= $d['value'] ?></th>
      													<th width="10%" class="text-center">X</th>
      													<th width="45%">
      														<input class="form-control cash_count" name="cash[denomination][value][]" type="number">
      														<input type="hidden" value="<?= $d['value'] ?>" class="cash_value">
      														<input type="hidden" name="cash[denomination][id][]" value="<?= $d['id_denomination'] ?>" class="id_denomination">

															<input type="hidden" name="cash[denomination][cash_value][]" value="<?= $d['value'] ?>">
      													</th>
      												</tr>

      										<?php }
												} ?>

      										<tr>
      											<th width="55%" colspan="2" class="text-right">Total :</th>
      											<th width="45%">
      												<input class="form-control" readonly type="number" id="total_denomination_amount" name="cash[total_denomination_amount]">
      											</th>
      										</tr>
      									</tbody>
      								</table>
      							</div>

      						</div>
      						<div class="col-md-6">
      							<legend>Collection</legend>

      							<div class="table-responsive">
      								<table class="table table-bordered" id="denomination">
      									<tbody>

      										<tr>
      											<th width="40%" class="text-right">Cash </th>
      											<th width="60%"><input name="cash[cash_received]" class="form-control" type="number" id="cash_received" readonly></th>
      										</tr>

      										<tr>
      											<th width="40%" class="text-right">Opening Balance </th>
      											<th width="50%"><input name="cash[cash_opening_balance]" class="form-control" type="number" id="cash_opening_balance"></th>
      										</tr>

      										<tr>
      											<th width="40%" class="text-right">Total </th>
      											<th width="50%"><input name="cash[cash_total]" readonly class="form-control" type="number" id="cash_total"></th>
      										</tr>

											  <tr>
      											<th width="40%" class="text-right">Difference </th>
      											<th width="50%"><input name="cash[difference]" readonly class="form-control" type="number" id="total_diff"></th>
      										</tr>
      									</tbody>
      								</table>
      							</div>
      						</div>
      					</div>

      					<div class="row">
      						<br />
      						<div class="col-xs-offset-5">
      							<button class="btn btn-primary" id="cash_coll_save" type="button">Save</button>
      							<button type="button" class="btn btn-default btn-cancel" id="cash_coll_cancel">Cancel</button>
      						</div> <br />

      					</div>
      				</form>
      			</div>



      		</div> <!-- box-body-->
      		<div class="overlay" style="display:none">
      			<i class="fa fa-refresh fa-spin"></i>
      		</div>
      </div> <!-- Default box-->