      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Village
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Village</a></li>
         
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Village</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
             <!-- form container -->
              <div>
	             <!-- form -->
				
				<?php echo form_open_multipart(( $village['id_village']!=NULL && $village['id_village']>0 ?'settings/village_form/update/'.$village['id_village']:'settings/village_form/save')); ?>
                   <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">village Name</label>
                       <div class="col-md-4">
                       	<input type="text" class="form-control" id="village_name" name="village[village_name]" value="<?php echo set_value('$village[village_name]',$village['village_name']); ?>">
                        <p class="help-block"></p>
							</div>                       	
                       </div>
                    </div>
                       <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Post office </label>
                       <div class="col-md-4">
                       	<input type="text" class="form-control" id="post_office" name="village[post_office]" value="<?php echo set_value('$village[post_office]',$village['post_office']); ?>">
                        <p class="help-block"></p>
							</div>                       	
                       </div>
                    </div>
                       <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Taluk</label>
                       <div class="col-md-4">
                       	<input type="text" class="form-control" id="category_name" name="village[taluk]" value="<?php echo set_value('$village[taluk]',$village['taluk']); ?>">
                        <p class="help-block"></p>
							</div>                       	
                       </div>
                    </div>
                      <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Pincode</label>
                       <div class="col-md-4">
                       	<input type="text" class="form-control" id="pincode" name="village[pincode]" value="<?php echo set_value('$village[pincode]',$village['pincode']); ?>">
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
      
      
      
       <!-- modal -->      
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete village</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this village record?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->
