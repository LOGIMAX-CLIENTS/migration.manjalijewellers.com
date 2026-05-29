      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Terms and Conditions 
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url('index.php/settings/profile/list');?>">Master</a></li>
            <li class="active">Terms and Conditions</li>  
          </ol>
        </section>
    
        <!-- Main content -->
        <section class="content">
     
          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Terms and Conditions - <?php echo ( $profile['id_profile']!=NULL?'Edit' :'Add'); ?></h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="">
				<?php echo form_open((  $general['id_general']!=NULL &&  $general['id_general']>0 ?'settings/terms_and_conditions/update/'.$general['id_general']:'settings/terms_and_conditions/save')) ?> 
				  <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Name</label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="name" name="general[name]" value="<?php echo set_value('$general[name]',$general['name']); ?>" placeholder="eg:Terms and Conditions" required="true"> 	
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div><br>	
				 <div class="row">                          
					<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Select Category</label>                           
					<div class="col-md-6">  
						<div class='form-group'>
                            <select id="select_type" required>
                                    <option>--Select Category--</option>
                                    <option value="1" <?php echo ($general['type'] == '1') ? 'selected="selected"' : '';?> >Terms and Conditions</option>
                                    <option value="2" <?php echo ($general['type'] == '2') ? 'selected="selected"' : '';?> >FAQ</option>
                                    <option value="3" <?php echo ($general['type'] == '3') ? 'selected="selected"' : '';?> >About Us</option>
                                    <option value="4" <?php echo ($general['type'] == '4') ? 'selected="selected"' : '';?> >Privacy policy</option>
                            </select>
                            <input type="hidden" id="type" name="general[type]" value="<?php echo set_value('general[type]',$general['type']); ?>">
			               
			        	</div>
						<p class="help-block"></p>                        
					</div>                       
				</div><br>
				 <div class="row">                          
					<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Terms and Conditions</label>                           
					<div class="col-md-6">  
						<div class='form-group'>

			                <label for="user_lastname"></label>

			               <textarea  id="content" name="general[content]"><?php echo set_value('general[content]',$general['content']); ?></textarea>

			        	</div>
						<p class="help-block"></p>                        
					</div>                       
				</div>
				
		
 
	
				
				<br/>      
				 <div class="row col-xs-12">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						<button type="submit" class="btn btn-primary">Save</button> 
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