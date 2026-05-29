  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Charges
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Charges</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Charges</h3>    <span id="Charges" class="badge bg-green"></span>
                  <?php if($access['add']==1){?>
                  <a class="btn btn-success pull-right" id="charges" href="#" data-toggle="modal" ><i class="fa fa-user-plus"></i> Add</a> 
                  <?php }?>
                          </div><!-- /.box-header -->
                <div class="box-body">
                <!-- Alert -->
                <?php 
                	if($this->session->flashdata('chit_alert'))
                	 {
                		$message = $this->session->flashdata('chit_alert');
                ?>
                       <div  class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	                    <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
	                    <?php echo $message['message']; ?>
	                  </div>
	                  
	            <?php } ?> 
				<div class="row">
            <div class="col-sm-12">
            <div class="pull-left">
              <div class="form-group">
                 <button class="btn btn-default" id="charges_date" style="margin-top: 20px;">
                <i class="fa fa-calendar"></i> Date range picker
                <i class="fa fa-caret-down"></i>
                </button>
              </div>
             </div>
             </div>
          </div>	
				  <div class="row">
					<div class="col-sm-10 col-sm-offset-1">
					<div id="chit_alert"></div>
					 
					</div>
				  </div>
						
                <div class="table-responsive">
                  <table id="charges_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
						            <th>Charge</th>
                        <th>Value</th>
                        <th>Code</th>
                        <th>Action</th>
                      </tr>
                 	</thead>
                 
                  </table>
                  </div>
				  <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
	  
	  
	  
	  
	  <div class="modal fade" id="charges_add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Charge </h4>
      </div>
      <div class="modal-body">
	  <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
				</div> 
          <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Charge Name
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="charge_name" name="charge_name" placeholder="Enter Charge Name" required="true"> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div> 
				 <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Charge Code
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="charge_code" name="charge_code" placeholder="Enter Charge Code"> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				 
				 
         
                <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Charge Value
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="value_charge" name="value_charge" placeholder="Enter Charge Value"> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				 
				 <div class="row">   
                    <div class="form-group">
                       <label for="charge_tax" class="col-md-3 col-md-offset-1 ">Charge Tax(%)
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="charge_tax" name="charge_tax" placeholder="Enter Charge Tax(%)"> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>

          <div class="row">
				    <div class="form-group">
            <label for="tag_display_switch" class="col-md-3 col-md-offset-1 ">Tag Display
					   <span class="error">*</span></label>
                <div class="col-md-4">
                    <input type="checkbox" checked="true" class="switch" id="tag_display_switch" name="tag_display_switch" data-on-text="YES" data-off-text="NO" />
                    <input type="hidden" id="tag_display" value="0">
					          <p class="help-block"></p>
						    </div>    
						</div>
					</div>

					<br>
					<div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Description
					   <span class="error"></span></label>
                       <div class="col-md-4">
                       	<textarea style="width: 120%;height:60px;" id="charge_description" class="charge_desc" name="charge_description" placeholder="Description" value=""></textarea> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
			
			 </div>
			 
					 
      <div class="modal-footer">
		<a href="#" id="charge_save_and_new" class="btn btn-success">Save & New</a>
      	<a href="#" id="charge_save_and_close" class="btn btn-warning" data-dismiss="modal">Save & Close</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="edit_charges" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Charge </h4>
      </div>
      <div class="modal-body">
	   <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error'></div>
				</div>
     		 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Charge Name
					   <span class="error">*</span></label>
                       <div class="col-md-4">
					   <input type="hidden" id="edit-id-charges" value="" />
                       	 <input type="text" class="form-control" id="charge_name_edit" name="charge_name_edit" placeholder="Enter Charge Name" required="true"> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
         <div class="row">
				 	<div class="form-group">
                       <label for="charge_value_edit" class="col-md-3 col-md-offset-1 ">Charge Value
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="charge_value_edit" name="charge_value_edit" placeholder="Enter Charge Value" required="true"> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div> 

          <div class="row">
				    <div class="form-group">
            <label for="ed_tag_display_switch" class="col-md-3 col-md-offset-1 ">Tag Display
					   <span class="error">*</span></label>
                <div class="col-md-4">
                    <input type="checkbox" checked="true" class="switch" id="ed_tag_display_switch" name="ed_tag_display_switch" data-on-text="YES" data-off-text="NO" />
                    <input type="hidden" id="ed_tag_display" value="0">
					          <p class="help-block"></p>
						    </div>    
						</div>
					</div>
				 <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Charge Code
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="charge_code_edit" name="charge_code_edit" placeholder="Enter Charge Code"> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				 <div class="row">   
                    <div class="form-group">
                       <label for="charge_tax_edit" class="col-md-3 col-md-offset-1 ">Charge Tax(%)
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="charge_tax_edit" name="charge_tax_edit" placeholder="Enter Charge Tax(%)"> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				 
				 <br>
				 <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Description
					   <span class="error"></span></label>
                       <div class="col-md-4">
                       	<textarea style="width: 120%;height:60px;" id="charge_description_edit" class="charge_des_edit" name="charge_description_edit" placeholder="Description" value=""></textarea> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				 
				</div>
				 
				
      
      <div class="modal-footer">
      	<a href="#" id="update_charge" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="delete_charges" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete Charge</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this charge record?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" onclick="delete_charge()">Delete</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
