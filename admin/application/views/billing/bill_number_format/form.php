<script type="text/javascript">
	function insertValueQuery(bill_type) {

		if (bill_type == 1) { //Sales

			var content_field = document.getElementById("sales_content");

			var selected_field = document.getElementById("sales_field_list").value;

			console.log(selected_field);

		} else if (bill_type == 2) { //Sales & Purchase

			var content_field = document.getElementById("sales_and_purchase_content");

			var selected_field = document.getElementById("sales_and_purchase_field_list").value;

		} else if (bill_type == 3) { //Sales & Return

			var content_field = document.getElementById("sales_and_return_content");

			var selected_field = document.getElementById("sales_and_return_field_list").value;

		} else if (bill_type == 4) { //Purchase

			var content_field = document.getElementById("purchase_content");

			var selected_field = document.getElementById("purchase_field_list").value;

		} else if (bill_type == 5) { //Order Advance

			var content_field = document.getElementById("order_advance_content");

			var selected_field = document.getElementById("order_advance_field_list").value;

		} else if (bill_type == 7) { //Sales return

			var content_field = document.getElementById("sales_return_content");

			var selected_field = document.getElementById("sales_return_field_list").value;

		} else if (bill_type == 8) { //Credit Collection

			var content_field = document.getElementById("credit_collection_content");

			var selected_field = document.getElementById("credit_collection_field_list").value;

		} else if (bill_type == 9) { //Order Delivery

			var content_field = document.getElementById("order_delivery_content");

			var selected_field = document.getElementById("order_delivery_field_list").value;

		} else if (bill_type == 10) { //Chit Pre close

			var content_field = document.getElementById("chit_pre_close_content");

			var selected_field = document.getElementById("chit_pre_close_field_list").value;

		} else if (bill_type == 11) { //Repair Order Delivery

			var content_field = document.getElementById("repair_order_delivery_content");

			var selected_field = document.getElementById("repair_order_delivery_field_list").value;

		}

		//IE support

		// if (document.selection) {

		// 	content_field.focus();

		// 	sel = document.selection.createRange();

		// 	sel.text = selected_field;

		// 	document.sqlform.insert.focus();

		// }

		// //MOZILLA/NETSCAPE support
		// else if (content_field.selectionStart || content_field.selectionStart == "0") {

		// 	//alert(content_field.selectionStart);

		// 	var startPos = content_field.selectionStart;

		// 	var endPos = content_field.selectionEnd;

		// 	var chaineSql = content_field.value;

		// 	content_field.value = chaineSql.substring(0, startPos) + selected_field + chaineSql.substring(endPos, chaineSql.length);

		// } else {

		content_field.value += selected_field;

		// }

		// $('#field_list').val('');

	}
</script>

<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

	<!-- Content Header (Page header) -->

	<section class="content-header">

		<h1>

			Bill Number Format

			<small>Bill Number Format Settings</small>

		</h1>

		<ol class="breadcrumb">

			<li><a href="#"><i class="fa fa-dashboard"></i> Settings</a></li>

			<li>

				<a href="#"> Bill Number Format</a>

			</li>

			<li class="active"> Bill Number Format Settings</li>

		</ol>

	</section>

	<!-- Main content -->

	<section class="content">

		<!-- Default box -->

		<div class="box">

			<div class="box-header with-border">

				<h3 class="box-title">Bill Number format</h3>

			</div>

			<div class="box-body">

				<!-- put your content here -->

				<div class="col-md-12">


					<?php

					$attributes 		=	array('role' => 'form');

					echo form_open_multipart((sizeof($exists) > 0  ? 'admin_ret_billing/bill_number_format/update' : 'admin_ret_billing/bill_number_format/save')); ?>

					<div class="row">

						<?php

						if (isset($db_error_msg) && $db_error_msg != '') {

							echo '<div class="alert alert-danger alert-dismissable">

														<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

														<h4><i class="icon fa fa-warning"></i> Warning!</h4>    <strong>' . $db_error_msg . '</strong>

												</div>';
						}

						?>

					</div>

					<div class="row">

						<div class="col-sm-10 col-sm-offset-1">

							<div id="error-msg"></div>

						</div>

					</div>

					<form id="format">

						<!-- Sales -->

						<div class="row">

							<div class="form-group">

								<label class="control-label col-sm-2 ">Sales Bill </label>

								<div class="col-sm-4 ">

									<textarea class="form-control" aria-describedby="basic-addon1" name="content[sales][text]" id="sales_content" cols="35" rows="5" tabindex="4" required="required"><?php echo set_value('content[sales][text]', $exists[0]['bill_no_format']); ?></textarea>

									<input type="hidden" value="1" name=content[sales][bill_type]>

									<span class="help-block">Enter or choose the Sales Bill content.</span>

								</div>

								<div class="col-sm-1">

									<div class="btn-group" data-toggle="buttons">

										<label onclick="insertValueQuery(1)" style="margin-top: 85px;" class="btn btn-primary">

											<a style="color:#FFFFFF; text-decoration:none" href="" id="move_button" name="move_button" title="click">

												<< </a>

										</label>

									</div>

								</div>

								<div class="col-sm-3">

									<select style="height: 150px;" class="form-control" size="11" id="sales_field_list" name="field_list">

										<?php echo $sale_format; ?>

										<?php foreach ($sale_format as $bill_no) { ?>

											<option value="<?php echo $bill_no['value'] ?>"><?php echo $bill_no['text'] ?></option>

										<?php } ?>

									</select>

									<span class="help-block">Select the value.</span>

								</div>

							</div>

						</div>

						<!--Sales &  Purchase -->

						<div class="row">

							<div class="form-group">

								<label class="control-label col-sm-2 ">Sales & Purchase Bill </label>

								<div class="col-sm-4 ">

									<textarea class="form-control" name="content[sales_and_purchase][text]" id="sales_and_purchase_content" cols="35" rows="5" tabindex="4" required="required"><?php echo set_value('content[sales_and_purchase][text]', $exists[1]['bill_no_format']); ?></textarea>

									<input type="hidden" value="2" name=content[sales_and_purchase][bill_type]>

									<span class="help-block">Enter or choose the Sales & Purchase Bill content.</span>

								</div>

								<div class="col-sm-1">

									<div class="btn-group" data-toggle="buttons">

										<label onclick="insertValueQuery(2)" style="margin-top: 85px;" class="btn btn-primary">

											<a style="color:#FFFFFF; text-decoration:none" href="" id="move_button" name="move_button" title="click">

												<< </a>

										</label>

									</div>

								</div>

								<div class="col-sm-3">

									<select style="height: 150px;" class="form-control" size="11" id="sales_and_purchase_field_list" name="field_list">

										<?php foreach ($sale_and_purchase_format as $bill_no) { ?>

											<option value="<?php echo $bill_no['value'] ?>"><?php echo $bill_no['text'] ?></option>

										<?php } ?>

									</select>

									<span class="help-block">Select the value.</span>

								</div>

							</div>

						</div>

						<!-- Sales  & Return -->

						<div class="row">

							<div class="form-group">

								<label class="control-label col-sm-2 ">Sales & Return Bill </label>

								<div class="col-sm-4 ">

									<textarea class="form-control" name="content[sales_and_return][text]" id="sales_and_return_content" cols="35" rows="5" tabindex="4" required="required"><?php echo set_value('content[sales_and_return][text]', $exists[2]['bill_no_format']); ?></textarea>

									<input type="hidden" value="3" name=content[sales_and_return][bill_type]>

									<span class="help-block">Enter or choose the Sales & Return Bill content.</span>

								</div>

								<div class="col-sm-1">

									<div class="btn-group" data-toggle="buttons">

										<label onclick="insertValueQuery(3)" style="margin-top: 85px;" class="btn btn-primary">

											<a style="color:#FFFFFF; text-decoration:none" href="" id="move_button" name="move_button" title="click">

												<< </a>

										</label>

									</div>

								</div>

								<div class="col-sm-3">

									<select style="height: 150px;" class="form-control" size="11" id="sales_and_return_field_list" name="field_list">

										<?php foreach ($sale_and_return_format as $bill_no) { ?>

											<option value="<?php echo $bill_no['value'] ?>"><?php echo $bill_no['text'] ?></option>

										<?php } ?>

									</select>

									<span class="help-block">Select the value.</span>

								</div>

							</div>

						</div>

						<!-- Purchase Bill -->

						<div class="row">

							<div class="form-group">

								<label class="control-label col-sm-2 ">Purchase Bill </label>

								<div class="col-sm-4 ">

									<textarea class="form-control" name="content[purchase][text]" id="purchase_content" cols="35" rows="5" tabindex="4" required="required"><?php echo set_value('content[purchase][text]', $exists[3]['bill_no_format']); ?></textarea>

									<input type="hidden" value="4" name=content[purchase][bill_type]>

									<span class="help-block">Enter or choose the Purchase Bill content.</span>

								</div>

								<div class="col-sm-1">

									<div class="btn-group" data-toggle="buttons">

										<label onclick="insertValueQuery(4)" style="margin-top: 85px;" class="btn btn-primary">

											<a style="color:#FFFFFF; text-decoration:none" href="" id="move_button" name="move_button" title="click">

												<< </a>

										</label>

									</div>

								</div>

								<div class="col-sm-3">

									<select style="height: 150px;" class="form-control" size="11" id="purchase_field_list" name="field_list">

										<?php foreach ($purchase_format as $bill_no) { ?>

											<option value="<?php echo $bill_no['value'] ?>"><?php echo $bill_no['text'] ?></option>

										<?php } ?>

									</select>

									<span class="help-block">Select the value.</span>

								</div>

							</div>

						</div>

						<!-- Order Advance -->

						<div class="row">

							<div class="form-group">

								<label class="control-label col-sm-2 ">Order Advance Bill </label>

								<div class="col-sm-4 ">

									<textarea class="form-control" name="content[order_advance][text]" id="order_advance_content" cols="35" rows="5" tabindex="4" required="required"><?php echo set_value('content[order_advance][text]', $exists[4]['bill_no_format']); ?></textarea>

									<input type="hidden" value="5" name=content[order_advance][bill_type]>

									<span class="help-block">Enter or choose the Order Advance Bill content.</span>

								</div>

								<div class="col-sm-1">

									<div class="btn-group" data-toggle="buttons">

										<label onclick="insertValueQuery(5)" style="margin-top: 85px;" class="btn btn-primary">

											<a style="color:#FFFFFF; text-decoration:none" href="" id="move_button" name="move_button" title="click">

												<< </a>

										</label>

									</div>

								</div>

								<div class="col-sm-3">

									<select style="height: 150px;" class="form-control" size="11" id="order_advance_field_list" name="field_list">

										<?php foreach ($ord_adv_format as $bill_no) { ?>

											<option value="<?php echo $bill_no['value'] ?>"><?php echo $bill_no['text'] ?></option>

										<?php } ?>

									</select>

									<span class="help-block">Select the value.</span>

								</div>

							</div>

						</div>

						<!-- Sales Return -->

						<div class="row">

							<div class="form-group">

								<label class="control-label col-sm-2 ">Sales Return Bill </label>

								<div class="col-sm-4 ">

									<textarea class="form-control" name="content[sales_return_content][text]" id="sales_return_content" cols="35" rows="5" tabindex="4" required="required"><?php echo set_value('content[sales_return_content][text]', $exists[5]['bill_no_format']); ?></textarea>

									<input type="hidden" value="7" name=content[sales_return_content][bill_type]>

									<span class="help-block">Enter or choose the Sales Return Bill content.</span>

								</div>

								<div class="col-sm-1">

									<div class="btn-group" data-toggle="buttons">

										<label onclick="insertValueQuery(7)" style="margin-top: 85px;" class="btn btn-primary">

											<a style="color:#FFFFFF; text-decoration:none" href="" id="move_button" name="move_button" title="click">

												<< </a>

										</label>

									</div>

								</div>

								<div class="col-sm-3">

									<select style="height: 150px;" class="form-control" size="11" id="sales_return_field_list" name="field_list">

										<?php foreach ($sale_return_format as $bill_no) { ?>

											<option value="<?php echo $bill_no['value'] ?>"><?php echo $bill_no['text'] ?></option>

										<?php } ?>

									</select>

									<span class="help-block">Select the value.</span>

								</div>

							</div>

						</div>

						<!--credit collection-->

						<div class="row">

							<div class="form-group">

								<label class="control-label col-sm-2 ">Credit Collection </label>

								<div class="col-sm-4 ">

									<textarea class="form-control" name="content[credit_collection_content][text]" id="credit_collection_content" cols="35" rows="5" tabindex="4" required="required"><?php echo set_value('content[credit_collection_content][text]', $exists[6]['bill_no_format']); ?></textarea>

									<input type="hidden" value="8" name=content[credit_collection_content][bill_type]>

									<span class="help-block">Enter or choose the Sales Return Bill content.</span>

								</div>

								<div class="col-sm-1">

									<div class="btn-group" data-toggle="buttons">

										<label onclick="insertValueQuery(8)" style="margin-top: 85px;" class="btn btn-primary">

											<a style="color:#FFFFFF; text-decoration:none" href="" id="move_button" name="move_button" title="click">

												<< </a>

										</label>

									</div>

								</div>

								<div class="col-sm-3">

									<select style="height: 150px;" class="form-control" size="11" id="credit_collection_field_list" name="field_list">

										<?php foreach ($credit_collection_format as $bill_no) { ?>

											<option value="<?php echo $bill_no['value'] ?>"><?php echo $bill_no['text'] ?></option>

										<?php } ?>

									</select>

									<span class="help-block">Select the value.</span>

								</div>

							</div>

						</div>
						<!-- Order Delivery -->
						<div class="row">

							<div class="form-group">

								<label class="control-label col-sm-2 ">Order Delivery </label>

								<div class="col-sm-4 ">

									<textarea class="form-control" name="content[order_delivery_content][text]" id="order_delivery_content" cols="35" rows="5" tabindex="4" required="required"><?php echo $exists[7]['bill_type'] == 9 ? set_value('content[order_delivery_content][text]', $exists[7]['bill_no_format']) : ''; ?></textarea>

									<input type="hidden" value="9" name=content[order_delivery_content][bill_type]>

									<span class="help-block">Enter or choose the Order Delivery content.</span>

								</div>

								<div class="col-sm-1">

									<div class="btn-group" data-toggle="buttons">


										<label onclick="insertValueQuery(9)" style="margin-top: 85px;" class="btn btn-primary">

											<a style="color:#FFFFFF; text-decoration:none" href="" id="move_button" name="move_button" title="click">
												<< </a>

										</label>

									</div>

								</div>

								<div class="col-sm-3">


									<select style="height: 150px;" class="form-control" size="11" id="order_delivery_field_list" name="field_list">

										<?php foreach ($order_delivery as $bill_no) { ?>

											<option value="<?php echo $bill_no['value'] ?>"><?php echo $bill_no['text'] ?></option>

										<?php } ?>

									</select>

									<span class="help-block">Select the value.</span>

								</div>

							</div>

						</div>

						<!-- Chit Pre Close -->

						<div class="row">

							<div class="form-group">

								<label class="control-label col-sm-2 ">Chit Pre Close Bill </label>

								<div class="col-sm-4 ">

									<textarea class="form-control" name="content[chit_pre_close_content][text]" id="chit_pre_close_content" cols="35" rows="5" tabindex="4" required="required"><?php echo set_value('content[chit_pre_close_content][text]', $exists[8]['bill_no_format']); ?></textarea>

									<input type="hidden" value="10" name=content[chit_pre_close_content][bill_type]>

									<span class="help-block">Enter or choose the Chit Pre Close Bill content.</span>

								</div>

								<div class="col-sm-1">

									<div class="btn-group" data-toggle="buttons">

										<label onclick="insertValueQuery(10)" style="margin-top: 85px;" class="btn btn-primary">

											<a style="color:#FFFFFF; text-decoration:none" href="" id="move_button" name="move_button" title="click">

												<< </a>

										</label>

									</div>

								</div>

								<div class="col-sm-3">

									<select style="height: 150px;" class="form-control" size="11" id="chit_pre_close_field_list" name="field_list">

										<?php foreach ($chit_format as $bill_no) { ?>

											<option value="<?php echo $bill_no['value'] ?>"><?php echo $bill_no['text'] ?></option>

										<?php } ?>

									</select>

									<span class="help-block">Select the value.</span>

								</div>

							</div>

						</div>

						<!-- Repait Order Delivery -->

						<div class="row">

							<div class="form-group">

								<label class="control-label col-sm-2 ">Repair Order Delivery </label>

								<div class="col-sm-4 ">


									<textarea class="form-control" name="content[repair_order_delivery_content][text]" id="repair_order_delivery_content" cols="35" rows="5" tabindex="4" required="required"><?php echo $exists[9]['bill_type'] == 11 ? set_value('content[repair_order_delivery_content][text]', $exists[9]['bill_no_format']) : ''; ?></textarea>

									<input type="hidden" value="11" name=content[repair_order_delivery_content][bill_type]>

									<span class="help-block">Enter or choose the Repair Order Delivery Bill content.</span>

								</div>

								<div class="col-sm-1">

									<div class="btn-group" data-toggle="buttons">

										<label onclick="insertValueQuery(11)" style="margin-top: 85px;" class="btn btn-primary">

											<a style="color:#FFFFFF; text-decoration:none" href="" id="move_button" name="move_button" title="click">

												<< </a>

										</label>

									</div>

								</div>

								<div class="col-sm-3">

									<select style="height: 150px;" class="form-control" size="11" id="repair_order_delivery_field_list" name="field_list">

										<?php foreach ($repair_order_delivery as $bill_no) { ?>

											<option value="<?php echo $bill_no['value'] ?>"><?php echo $bill_no['text'] ?></option>

										<?php } ?>

									</select>

									<span class="help-block">Select the value.</span>

								</div>

							</div>

						</div>

						<div class="row">

							<div class="box box-default"><br />

								<div class="col-xs-offset-5">
									
                                     <?php if($access['add']==1){?>
									<button type="submit" class="btn btn-primary">Save</button>
										<?php }?>
									<button type="button" class="btn btn-default btn-cancel">Cancel</button>

								</div> <br />

							</div>

						</div>

						<?php echo form_close(); ?>

					</form>

					<!-- /form -->

				</div>

			</div><!-- .box-body End -->

		</div>

	</section><!-- /.content -->

</div><!-- /.content-wrapper -->