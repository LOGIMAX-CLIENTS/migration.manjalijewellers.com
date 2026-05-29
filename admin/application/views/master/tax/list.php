  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Tax
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Tax List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Tax List</h3><span id="total_tax" class="badge bg-green"></span>      
                           <a class="btn btn-success pull-right" id="save_tax" href="#" data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add </a> 
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
				<div class="col-md-2" style="margin-top: 20px;">
	                 		<!-- Date and time range -->
				<div class="form-group">
				<div class="input-group">
						<button class="btn btn-default btn_date_range" id="tax-dt-btn">
						<span  style="display:none;" id="tax1"></span>
						<span  style="display:none;" id="tax2"></span>
						<i class="fa fa-calendar"></i> Date range picker
						<i class="fa fa-caret-down"></i>
						</button>
				</div>		
				</div>						
				</div>					
				  <div class="row">
					<div class="col-sm-10 col-sm-offset-1">
					<div id="chit_alert"></div>
					</div>
				  </div>				
                  <div class="table-responsive">
                  <table id="tax_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
						<th>Name</th>
						<th>Short Code</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                 	</thead>
                  </table>
                  </div> <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      

<!-- modal -->      
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete Tax</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this Tax record?</strong>
      </div>
      <div class="modal-footer">
       	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->  
<!-- modal -->      
<div class="modal fade" id="confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Tax</h4>
      </div>
      <div class="modal-body">
	  <div class="row" >
			<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
	  </div>
				 <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Tax Name
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="tax_name" name="taxname" placeholder="Enter Tax name"required/> 
						 <p class="help-block"></p>
                       </div>
                    </div>
				 </div> 
				 <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Tax Code
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="tax_code" name="taxcode" placeholder="Enter Tax code"required/> 
						 <p class="help-block"></p>
                       </div>
                    </div>
				 </div> 
				  <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Tax Percentage
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="tax_percentage" name="taxpercentage" placeholder="Enter Tax percentage" required/> 
						 <p class="help-block"></p>
                       </div>
                    </div>
				 </div> 
				<div class="row">
					<div class="form-group">
					<label for="scheme_code" class="col-md-3 col-md-offset-1 ">Status</label>
					<div class="col-md-4">
						<input type="checkbox" class="status" id="ad_taxstatus" name="ad_taxstatus" data-on-text="YES" data-off-text="NO" checked="true"/>
						<input type="hidden" id="adtax_status" value="1"/>
					</div>    	
					</div>
				</div>
			</div>
      <div class="modal-footer">
		<a href="#" id="add_newtax" class="btn btn-success">Save & New</a>
      	<a href="#" id="add_tax" class="btn btn-warning" data-dismiss="modal">Save & Close</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
	  </div>
    </div>
  </div>
</div>
<!-- / modal -->
<!-- modal -->      
<div class="modal fade" id="confirm-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Tax</h4>
      </div>
      <div class="modal-body">
	    <div class="row" >
			<div class="col-md-offset-1 col-md-10" id='error-msg1'></div>
	    </div>
				 <div class="row">   
					<div class="form-group">
					<label for="scheme_code" class="col-md-3 col-md-offset-1 ">Tax Name
					<span class="error">*</span></label>
					<div class="col-md-4">
						<input type="text" class="form-control" id="ed_taxname" name="taxname" value="<?php echo set_value('"tax[tax_name]',(isset($tag['tax_name'])?$tax['tax_name']:"")); ?>" placeholder="Enter Tax name" required/> 
						<p class="help-block"></p>
					</div>
					</div>
				 </div>
				 <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Tax Code
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="ed_tax_code" name="taxcode" value="<?php echo set_value('"tax[tax_code]',(isset($tag['tax_code'])?$tax['tax_code']:"")); ?>" placeholder="Enter Tax code"required/> 
						 <p class="help-block"></p>
                       </div>
                    </div>
				 </div> 
				 <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Tax Percentage
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="ed_tax_percentage" name="taxpercentage" value="<?php echo set_value('"tax[tax_percentage]',(isset($tag['tax_percentage'])?$tax['tax_percentage']:"")); ?>" placeholder="Enter Tax percentage"required/> 
						 <p class="help-block"></p>
                       </div>
                    </div>
				 </div> 
				<div class="row">
					<div class="form-group">
				 	<label for="scheme_code" class="col-md-3 col-md-offset-1 ">Status</label>
                    <div class="col-md-4">
                    <input type="checkbox" class="status" id="ed_taxstatus" name="ed_taxstatus" data-on-text="YES" data-off-text="NO"/>
					<input type="hidden" id="ed_tax_status" value="1">
					</div>    	
				</div>
		  </div> 
      </div>
      <div class="modal-footer">
      	<a href="#" id="update_tax" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

