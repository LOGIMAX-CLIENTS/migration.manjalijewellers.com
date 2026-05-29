      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Process
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Process</a></li>
         
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Process</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
             <!-- form container -->
              <div>
	             <!-- form -->
				<?php echo form_open_multipart(( $process['id_metal_process']!=NULL && $process['id_metal_process']>0 ?'admin_ret_metal_process/metal_process/update/'.$process['id_metal_process']:'admin_ret_metal_process/metal_process/save')); ?>
                   <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Process Name<span class="error">*</span></label>
                       <div class="col-md-4">
                       	<input type="text" class="form-control" id="process_name" placeholder="Enter Process Name" name="process[process_name]" value="<?php echo set_value('$process[process_name]',$process['process_name']); ?>" required>
                        <p class="help-block"></p>
							</div>                       	
                       </div>
                    </div><br/> 
                    
                    <div class="row">
    				 	<div class="form-group">
                           <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Has Charge<span class="error">*</span></label>
                           <div class="col-md-4">
                           	        <input type="radio" id="has_charge" name="process[has_charge]" value="1" class="minimal" <?php if($process['has_charge']==1){ ?> checked <?php } ?> required/>&nbsp;Yes&nbsp;
    								<input type="radio" id="has_charge" name="process[has_charge]" value="0" class="minimal" <?php if($process['has_charge']==0){ ?> checked <?php } ?>/>&nbsp;No&nbsp;
    							</div>                       	
                          </div>
                    </div><br/> 
                        
                    <div class="row">
    				 	<div class="form-group">
                           <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Charge Type<span class="error">*</span></label>
                           <div class="col-md-4">
                           	        <input type="radio" id="charge_type" name="process[charge_type]" value="1" class="minimal" <?php if($process['charge_type']==1){ ?> checked <?php } ?> required/>&nbsp;Per Gram&nbsp;
    								<input type="radio" id="charge_type" name="process[charge_type]" value="2" class="minimal" <?php if($process['charge_type']==2){ ?> checked <?php } ?>/>&nbsp;Flat
    							</div>                       	
                          </div>
                    </div><br/> 
               		
				 </div>
			        <br/> 
			     <div class="row"> 
					  <div class="col-xs-offset-5">
						<button type="submit"  class="btn btn-primary">Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
					  </div> 
				  </div><br/>  
	          <?php echo form_close();?>
	             <!-- /form -->
	          </div>
             <!-- /form container -->
            </div><!-- /.box-body -->
             <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
