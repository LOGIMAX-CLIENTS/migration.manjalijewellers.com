

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
		Design
		</h1>
		<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Master</a></li>
		<li class="active">Design</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- form -->
		<?php echo form_open_multipart(($subdesign['id_sub_design']!=NULL && $subdesign['id_sub_design']>0 ?'admin_ret_catalog/ret_sub_design/update/'.$subdesign['id_sub_design']:'admin_ret_catalog/ret_sub_design/save'),array('id'=>'sub_design_form')); ?>
		<!-- Default box -->
		<div class="box">
			<div class="box-header with-border">
			<h3 class="box-title">Create Sub Design</h3>
			<div class="box-tools pull-right">
			<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
			</div>
			</div>
			<div class="box-body">
			  <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
		      </div>
				<div class="row">  
					<div class="tab-content col-md-12">
						<div class="tab-pane active" id="tab_1">
							<div class='row'>	
							    <div class="col-md-12">
							        <div class='col-sm-3'>
    							        <div class='form-group'>
    						                <label for="short_code">Sub Design Name<span class="error"> *</span></label>		                
    						                <input class="form-control" id="sub_design_name" required="true" name="subdesign[sub_design_name]" placeholder="Enter Sub Design Name" value="<?php echo set_value('"subdesign[sub_design_name]',(isset($subdesign['sub_design_name'])?$subdesign['sub_design_name']:"")); ?>" type="text"  /> 			
    						            </div> 
    					            </div>
							    </div>
						    </div>
						  <div class="row"><p class="help-block"></p></div>
					 </div>
					</div>  
				</div>  <!-- /Tab content --> 
			</div><!-- /.box-body -->
			<div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
				 <div class="row">
				  <div class="col-md-12">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						<button type="button" id="subdesign_submit" class="btn btn-primary">Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
					  </div> <br/>
					  </div> 
					</div>
				  </div>    
		</div><!-- /.box -->
		 <?php echo form_close();?>
	</section><!-- /.content -->
</div><!-- /.content-wrapper -->
