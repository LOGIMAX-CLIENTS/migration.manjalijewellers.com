<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

	<!-- Content Header (Page header) -->

	<section class="content-header">

		<h1>

			Other Item Details

		</h1>

		<ol class="breadcrumb">

			<li><a href="#"><i class="fa fa-dashboard"></i>Master</a></li>

			<li class="active">Add other details</li>

		</ol>

	</section>

	<!-- Default box -->

	<section class="content">

		<div class="box">

			<div class="box-header with-border">

				<h3 class="box-title">Add Other items</h3>

				<div class="box-tools pull-right">

					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

					<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>

				</div>

			</div>

			<div class="box-body">



				<div class="row">

					<!-- form -->

					<?php echo form_open_multipart(($item['id_other_item_type'] != NULL && $item['id_other_item_type'] > 0 ? 'admin_ret_other_inventory/inventory_category/update/' . $item['id_other_item_type'] : 'admin_ret_other_inventory/inventory_category/save')); ?>

					<div class="row">


						<label for="scheme_code" class="col-md-2 col-md-offset-1 ">Name

							<span class="error">*</span></label>

						<div class="col-md-3">

							<div class="form-group">



								<!-- <input id="name"  class="form-control" name="item[name]" type="text" required="true" placeholder="" value="<?php echo set_value('item[name]', isset($item['name']) ? $item['name'] : NULL); ?>" autocomplete="off"/> -->
								<input id="name" class="form-control form-input" name="item[name]" type="text" required="true" placeholder="" value="<?php echo set_value('item[name]', isset($item['name']) ? $item['name'] : ''); ?>" autocomplete="off" onkeydown="return validateKeyPress(event)">



							</div>

						</div>

					</div>

					<div class="row">

						<label for="" class="col-md-2 col-md-offset-1">Outward Type</label>

						<p class="help-block"></p>

						<div class="col-sm-4">

							<input type="radio" id="outward_type2" name="item[outward_type]" value="2" <?php echo $item['outward_type'] == 2 ? 'checked="true"' : ''; ?>> &nbsp;&nbsp;&nbsp;<label for="outward_type2">Estimation</label>

							<input type="radio" id="outward_type1" name="item[outward_type]" value="1" checked <?php echo $item['outward_type'] == 1 ? 'checked="true"' : ''; ?>> &nbsp;&nbsp;&nbsp;<label for="outward_type1">Billing</label>

							&nbsp;&nbsp;&nbsp;



						</div>

					</div>




					<div class="row">

						<label for="" class="col-md-2 col-md-offset-1">As billable</label>

						<p class="help-block"></p>

						<div class="col-sm-4">

							<input type="radio" id="as_bill1" name="item[as_bill]" value="0" <?php echo $item['asbillable'] == 0 ? 'checked="true"' : ''; ?>> &nbsp;&nbsp;&nbsp;<label for="as_bill1">Free</label>

							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

							<input type="radio" id="as_bill2" name="item[as_bill]" value="1" <?php echo $item['asbillable'] == 1 ? 'checked="true"' : ''; ?>> &nbsp;&nbsp;&nbsp;<label for="as_bill2">Cost</label>

						</div>

					</div>



					<div class="row">

						<label for="" class="col-md-2 col-md-offset-1">Expiry Date Validate</label>

						<p class="help-block"></p>

						<div class="col-sm-4">

							<input type="radio" id="exp_date1" name="item[exp_date]" value="0" <?php echo $item['expirydatevalidate'] == 0 ? 'checked="true"' : ''; ?>> &nbsp;&nbsp;&nbsp;<label for="exp_date">No validity</label>

							&nbsp;&nbsp;&nbsp;&nbsp;

							<input type="radio" id="exp_date2" name="item[exp_date]" value="1" <?php echo $item['expirydatevalidate'] == 1 ? 'checked="true"' : ''; ?>> &nbsp;&nbsp;&nbsp;<label for="exp_date1">Having</label>

						</div>

					</div>



					<div class="row">

						<label for="" class="col-md-2 col-md-offset-1">Reorder Level</label>

						<p class="help-block"></p>

						<div class="col-sm-4">

							<input type="radio" id="reorder_level1" name="item[reorder_level]" value="1" checked <?php echo $item['reorderlevel'] == 1 ? 'checked="true"' : ''; ?>> &nbsp;&nbsp;&nbsp;<label for="reorder_level1">Yes</label>

							&nbsp;&nbsp;&nbsp;

							<input type="radio" id="reorder_level2" name="item[reorder_level]" value="2" <?php echo $item['reorderlevel'] == 2 ? 'checked="true"' : ''; ?>> &nbsp;&nbsp;&nbsp;<label for="reorder_level2">No</label>

						</div>

					</div>







					<div class="row">

						<div class="box box-default"><br />

							<div class="col-xs-offset-5">

								<button type="submit" class="btn btn-primary">save</button>

								<button type="button" class="btn btn-default btn-cancel">Cancel</button>

							</div> <br />

						</div>

					</div>

				</div>

	</section>

</div>

<script>
	function validateKeyPress(event) {
		// Get the pressed key code
		var keyCode = event.which || event.keyCode;

		// Allow letters, spaces, and backspace (exclude numbers)
		if ((keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122) || keyCode === 32 || keyCode === 8 ||  keyCode === 9 ) {
			return true;
		} else {
			event.preventDefault(); // Prevent the keypress
			return false;
		}
	}
</script>