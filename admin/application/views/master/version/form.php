<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Version Details
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url('index.php/settings/version/list');?>">Master</a></li>
            <li class="active">Version Details</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
     
          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Version - <?php echo ( $version_data['id_version']!=NULL?'Edit' :'Add'); ?></h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="">
				<?php echo form_open((  $version_data['id_version']!=NULL &&  $version_data['id_version']>0 ?'settings/version/update/'.$version_data['id_version']:'settings/version/save')) ?>
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Version Number <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="version_no" name="version_data[version_no]" value="<?php echo set_value('version_data[version_no]',$version_data['version_no']); ?>" placeholder="eg:03.01.00 " required="true"> 
                         <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				
				<div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name"  class="col-md-2 col-md-offset-1 ">Description<span class="error">*</span></label>
                       <div class="col-md-4">
                         
                         <textarea class="form-control"  rows="4" cols="50" name="version_data[description]" id="description" placeholder="Enter the Version Details " required="true" ><?php echo set_value('version_data[description]',$version_data['description']); ?></textarea>
                        
                        <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>	
                    <input type="hidden" name="version_data[client]" value="<?php echo set_value('version_data[client]',$version_data['client']); ?>"/>
                 <!-- <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Client Name <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="client" name="version_data[client]" value="<?php echo set_value('version_data[client]',$version_data['client']); ?>" placeholder="eg: " required="true"> 
                         <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
  -->
	
				
				<br/>      
				 <div class="row col-xs-12">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						<button type="submit" id="version_save" class="btn btn-primary">Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
						
					  </div> <br/>
					</div>
				  </div>      
				        	
               </form>              	              	
              </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
              
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
         

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->