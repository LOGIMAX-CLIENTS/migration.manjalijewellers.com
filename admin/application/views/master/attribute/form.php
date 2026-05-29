<style type="text/css">
.add_attributes {
	cursor: pointer;
	color: blue;
}
.add_attribute, .remove_attribute {
	text-align: center;
}
.remove_attribute {
	margin-left: 5px;
}
.title-add-attribute {
	padding-bottom: 15px;
    padding-top: 15px;
	font-size: medium;
}
.label_wastage_product, .label_wastage_design, .label_sub_design {
	width: 100% !important;
}
.select2 {
	width: 100% !important;
}
.label_attr_status {
	width: 100%;
}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Attribute
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Master</a></li>
			<li class="active">Add Attribute</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- form -->
		<?php echo form_open(( $attr['attr_id']!=NULL && $attr['attr_id']>0 ?'admin_ret_catalog/attribute/update/'.$attr['attr_id']:'admin_ret_catalog/attribute/save'),array('id'=>'attribute_form')); ?>
		<!-- Default box -->
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title">Add Attribute</h3>
			</div>
			<div class="box-body">
				<div class="col-md-12">
					<div class="tab-content col-md-12">
						<div class="row">
							<div class="row" >
								<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
							</div>
							<div class='row'>							       
								<div class='col-sm-4'>
									<div class='form-group'>
										<label for="attr_name">Attribute Name<span class="error">*</span></label>
										<input class="form-control input_text" id="attr_name" name="attr[attr_name]" type="text" value="<?php echo set_value('attr[attr_name]',$attr['attr_name']); ?>" placeholder="Attribute Name"/>
									</div>
								</div>
								<div class="col-md-4">
									<div class='form-group'>
										<label for="attr_status" class="label_attr_status">Status<span class="error">*</span></label>
										<input type="checkbox"  class="attr" id="chk_attr_status" data-on-text="ACTIVE" data-off-text="INACTIVE"  value="1" <?php echo $attr['attr_status'] == 1 ? 'checked="true"':''; ?>>
										<input type="hidden" id="attr_status" name="attr[attr_status]" value="1">
										<p class="help-block"></p>
									</div>
								</div>
							</div>
						</div>

						<div class="row attribute-values">
							<div class='title-add-attribute'>
								Add Attribute Values  ( <span class="add_attributes"><i class="fa fa-plus"></i></span> )
							</div>
							<?php 
							if($attr['attr_id'] > 0) { ?>
							<?php foreach($attr_val as $key => $values) { ?>
								<div class="row attributes">					       
									<div class="col-md-3">
										<div class="form-group">
											<input type="hidden" name="attr[attr_val_id][]" value="<?php echo $values['attr_val_id'] ?>" />
											<input class="form-control attr_val" name="attr[attr_val][]"  type="text" placeholder="Attribute Value" value="<?php echo $values['attr_val'] ?>" />
										</div>
									</div>
									<div class="col-md-2 attribute_buttons">
										<div class="form-group">
										</div>
									</div>
								</div>
							<?php  
							} } ?>
						</div>
					</div>  <!-- /Tab content --> 
				</div><!-- /.box-body -->
				<div class="overlay" style="display:none">
				  	<i class="fa fa-refresh fa-spin"></i>
				</div>
			</div><!-- /.box -->
			<div class="row">
				<div class="box box-default"><br/>
					<div class="col-xs-offset-5">
						<button type="button" id="add_newattribute"  class="btn btn-primary">Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
					</div> <br/>
				</div>
			</div> 
		<?php echo form_close();?> 
	</section><!-- /.content -->
</div><!-- /.content-wrapper -->
<script type="text/javascript">

var cust_id ="<?php echo $customer['id_customer']; ?>";   

var mob_no_len ="<?php echo $this->session->userdata('mob_no_len')?>";

</script>