<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Diamond Quality
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Diamond List</li>
          </ol>
        </section>

	<!-- Main content -->
	<section class="content">
		<!-- form -->
		<?php echo form_open_multipart(($quality['quality_id']!= NULL && $quality['quality_id']>0 ?'admin_ret_catalog/diamond/update/'.$quality['quality_id']:'admin_ret_catalog/diamond/save'),array('id'=>'diamond_master')); ?>
		<!-- Default box -->
		<div class="box">
			<div class="box-header with-border">
              <h3 class="box-title">Diamond List</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div> 
			<div class="box-body">
	        <div class="row" >
			<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
			</div> 
            <div class="box-body">
			 <div class="row">   
			   <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Name
			   <span class="error">*</span></label>
				   <div class="col-md-3">
				   <div class="form-group">
						<input type="hidden" id="edit-id" name="quality[quality_id]" value="<?php echo set_value('quality[quality_id]',$quality['quality_id']); ?>"/>
						<input id="code" class="form-control" name="quality[code]" type="text"  placeholder="" value="<?php echo set_value('quality[code]',$quality['code']); ?>" />
						
					</div>
                    </div>
				 </div> 
				 
				 <div class="row">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Select Cut
					   <span class="error">*</span></label>
					   <div class="col-md-3">
                       <div class="form-group">	
                        <select id="cut_sel" class="form-control"  style="width:100%;"></select>
						<input id="cut_id" name="quality[cut_id]" type="hidden" value="<?php echo set_value('quality[cut_id]',$quality['cut_id']); ?>"/>
                      </div>
                    </div>
				 </div> 
				 
                 <div class="row">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Select Clarity
					   <span class="error">*</span></label>
					   <div class="col-md-3">
                       <div class="form-group">	
                        <select id="clarity_sel" class="form-control"  style="width:100%;"></select>
						<input id="clarity_id" name="quality[clarity_id]" type="hidden" value="<?php echo set_value('quality[clarity_id]',$quality['clarity_id']); ?>"/>
                      </div>
                    </div>
				 </div>  
                 <div class="row">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Select Color
					   <span class="error">*</span></label>
					   <div class="col-md-3">
                       <div class="form-group">	
                        <select id="color_sel" class="form-control"  style="width:100%;"></select>
						<input id="color_id" name="quality[color_id]" type="hidden" value="<?php echo set_value('quality[color_id]',$quality['color_id']); ?>"/>
                      </div>
                    </div>
				 </div> 
                 
                 <div class="row">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Select Shape
					   <span class="error">*</span></label>
					   <div class="col-md-3">
                       <div class="form-group">	
                        <select id="shape_sel" class="form-control"  style="width:100%;"></select>
						<input id="shape_id" name="quality[shape_id]" type="hidden" value="<?php echo set_value('quality[shape_id]',$quality['shape_id']); ?>"/>
                      </div>
                    </div>
				 </div> 
			<div class="row">
				<label for="scheme_code" class="col-md-2 col-md-offset-1 ">Active</label>
				 <div class="col-md-3">
					 <div class="form-group">	
						<input type="checkbox" id="status" class="status" data-on-text="YES" data-off-text="NO" name="quality[status]"  <?php if($quality['status'] == 1) { ?> checked="true" <?php } ?>/>
						<input type="hidden" id="ad_quality_status" name="quality[status]" value="1"/>
					</div>
				<p></p>
				</div>
			</div>
		<div class="row">
		   <div class="box box-default"><br/>
			  <div class="col-xs-offset-5">
				<button type="button" id="qua_submit" class="btn btn-primary">Save</button> 
				<button type="button" class="btn btn-default btn-cancel">Cancel</button>
			  </div> <br/>
			</div>
		  </div> 			
     </div>	
		<?php echo form_close();?> 
	</section><!-- /.content -->
</div>
</div> <!-- /.content-wrapper -->
   <script type="text/javascript">

  </script>