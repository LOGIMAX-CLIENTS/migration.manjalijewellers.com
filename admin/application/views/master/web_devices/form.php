      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Device
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Device</a></li>
         
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">device</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
             <!-- form container -->
              <div>
	             <!-- form -->
				
				<?php echo form_open_multipart(( $device['id_device']!=NULL && $device['id_device']>0 ?'admin_ret_catalog/web_devices/update/'.$device['id_device']:'admin_ret_catalog/web_devices/save')); ?>
                   <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Device Name<span class="error">*</span></label>
                       <div class="col-md-4">
                       	<input type="text" class="form-control" id="device_name" name="device[device_name]" value="<?php echo set_value('$device[device_name]',$device['device_name']); ?>" placeholder="Enter The Device Name" required>
                        <p class="help-block"></p>
							</div>                       	
                       </div>
                    </div>
                       <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Token<span class="error">*</span></label>
                       <div class="col-md-4">
                       	<input type="text" class="form-control" id="token_id" name="device[token_id]" value="<?php echo set_value('$device[token_id]',$device['token_id']); ?>" placeholder="Enter The Device Token" required>
                        <p class="help-block"></p>
							</div>                       	
                       </div>
                    </div>
                       <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Branch<span class="error">*</span></label>
                       <div class="col-md-4">
                        <select class="form-control" id="branch_select" ></select>
                        <input type="hidden" id="id_branch" name="device[id_branch]" value="<?php echo set_value('$device[id_branch]',$device['id_branch']); ?>">
                        <p class="help-block"></p>
							</div>                       	
                       </div>
                    </div>
                    
                    <div class="row">
    				 	<div class="form-group">
                           <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Floor<span class="error">*</span></label>
                           <div class="col-md-4">
                            <select class="form-control" id="counter_flr_sel" name="device[id_floor]" required></select>
                            <input type="hidden" id="id_floor" value="<?php echo set_value('$device[id_floor]',$device['id_floor']); ?>" >
                            <p class="help-block"></p>
    							</div>                       	
                           </div>
                    </div>
                    
                    <div class="row">
    				 	<div class="form-group">
                           <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Counter<span class="error">*</span></label>
                           <div class="col-md-4">
                            <select class="form-control" id="counter_select" name="device[id_counter]" required></select>
                            <input type="hidden" id="id_counter" value="<?php echo set_value('$device[id_counter]',$device['id_counter']); ?>" >
                            <p class="help-block"></p>
    							</div>                       	
                           </div>
                    </div>
                    
                    
                    
                      <div class="row">
    				 	<div class="form-group">
                           <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Browser Name<span class="error">*</span></label>
                           <div class="col-md-4">
                           	<input type="text" class="form-control" id="" name="device[browser]" value="<?php echo set_value('$device[browser]',$device['browser']); ?>" placeholder="Enter The Browser Name" required>
                            <p class="help-block"></p>
    							</div>                       	
                           </div>
                    </div>
				 </div>
		
		
				 
			    
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
