  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Stone
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Stone List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Stone List</h3>    <span id="total_count" class="badge bg-green"></span>      
                           <a class="btn btn-success pull-right" id="add_stone" href="#" data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a> 
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
					<div class="col-sm-10 col-sm-offset-1">
					<div id="chit_alert"></div>
					 
					</div>
				  </div>

				 <div class="row">
	                 
	                 <div class="col-md-12">
	             
	                 	<div class="col-md-2" style="margin-top: 20px;">
	                 		         	 <!-- Date and time range -->
		                  <div class="form-group">
		                    <div class="input-group">
		                       <button class="btn btn-default btn_date_range" id="stone_date">
							  <!-- <input id="rpt_payments"  name="rpt_payment" type="hidden" value="" />-->
							    <span  style="display:none;" id="stone1"></span>
							    <span  style="display:none;" id="stone2"></span>
		                        <i class="fa fa-calendar"></i> Date range picker
		                        <i class="fa fa-caret-down"></i>
		                      </button>
		                    </div>
		                 </div><!-- /.form group -->
		                </div>	
							</div>
					
	                 </div>
					
                  <div class="table-responsive">
                  <table id="stone_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Short Code</th>
						<th>UOM Name</th>
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
        <h4 class="modal-title" id="myModalLabel">Delete Stone</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this stone record?</strong>
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
        <h4 class="modal-title" id="myModalLabel">Add Stone</h4>
      </div>
      <div class="modal-body">
       <div id="error-msg"></div>
       <form id="myform">
	      <div class="row">
				 <div class="form-group">
                        <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Select UOM<span class="error"> *</span></label>
					    <div class="col-md-4">
						<select id="stone_sel" class="form-control"></select>
						<input id="uom_id" name="uom_id" type="hidden" />
                	    <p class="help-block"></p></div>
                 </div>
		 </div> 
         <div class="row">
				 	<div class="form-group">
                       <label for="stone_name" class="col-md-4 col-md-offset-1 ">Name<span class="error"> *</span></label>
                       <div class="col-md-4">
					   <input type="hidden" id="edit-id" value="" />
                       	 <input type="text" class="form-control" id="stone_name" name="stone_name" placeholder="Enter stone name" required="true"> 
                	  <p class="help-block"></p>
                       </div>
                    </div>
		</div> 
				 <div class="row">   
                    <div class="form-group">
                       <label for="stone_code" class="col-md-4 col-md-offset-1 ">Short Code<span class="error"> *</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="stone_code" name="stone_code" placeholder="Enter Stone Code"> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div> 
				 <div class="row">   
				 <div class='form-group'>
                      <label for="stone_type" class="col-md-4 col-md-offset-1 ">Stone type<span class="error"> *</span></label>
                        <div class="col-md-6">
                    <input type="radio" id="stone_type" name="stone_type" value="1" checked="true"> Diamond
                     &nbsp; &nbsp;
                    <input type="radio" id="stone_type" name="stone_type" value="2" > Gem Stones
                     &nbsp; &nbsp;
                    <input type="radio" id="stone_type" name="stone_type" value="3" > Others
                    <p class="help-block"></p>
                    </div>
					</div>
			 </div>
			 <div class="row">
				<div class="form-group">
				<label for="certificate" class="col-md-4 col-md-offset-1 ">Certificate Required</label>
                      <div class="col-md-4">
                     <input type="checkbox" checked="true" class="switch" id="ce_switch" name="ce_switch" data-on-text="YES" data-off-text="NO" />
					   <input type="hidden" id="is_certificate_req " value="1">
					   <p class="help-block"></p>
						</div>    
						</div>
					</div>
					<div class="row">
				<div class="form-group">
				<label for="4c_required" class="col-md-4 col-md-offset-1 ">4C Required</label>
                      <div class="col-md-4">
                      <input type="checkbox" checked="true" class="switch" id="4c_switch" name="4c_switch" data-on-text="YES" data-off-text="NO" />
					   <input type="hidden" id="is_4c_req" value="0">
					   <p class="help-block"></p>
						</div>    
						</div>
					</div>
					<div class="row">
				<div class="form-group">
				<label for="status" class="col-md-4 col-md-offset-1 ">Active</label>
                      <div class="col-md-4">
                      <input type="checkbox" checked="true" class="status" id="stone_switch" name="stone_switch" data-on-text="YES" data-off-text="NO" />
					   <input type="hidden" id="stone_status" value="1">
					   <p class="help-block"></p>
						</div>    
						</div>
					</div>

          </form>

      </div>


      <div class="modal-footer">
		<a href="#" id="add_stonenew" class="btn btn-success">Save & New</a>
      	<a href="#" id="add_newstone" class="btn btn-warning" data-dismiss="modal">Save & Close</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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
        <h4 class="modal-title" id="myModalLabel">Edit Stone</h4>
      </div>
     <div class="modal-body">
	  <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error'></div>
				</div>
		  <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Select UOM<span class="error"> *</span></label>
					   <div class="col-md-4">
						<select id="ed_stone_sel" class="form-control"></select>
						<input id="ed_uom_id" name="ed_uom_id" type="hidden" />
                	  <p class="help-block"></p>
					  </div>
                    </div>
				 </div> 
          <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Name<span class="error"> *</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="ed_stone_name" name="ed_stone_name" placeholder="Enter type name" required="true"> 
                	  <p class="help-block"></p>
                       </div>
                    </div>
				 </div> 
				 <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Short Code<span class="error"> *</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="ed_stone_code" name="ed_stone_code" placeholder="Enter Short Name"> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div> 
				 <div class="row">   
				 <div class='form-group'>
                      <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Stone type<span class="error"> *</span></label>
                        <div class="col-md-6">
                    <input type="radio" id="ed_stone_type" name="ed_stone_type" value="1" > Precious
                     &nbsp; &nbsp;
                    <input type="radio" id="ed_stone_type" name="ed_stone_type" value="2" > Semi-Precious
                     &nbsp; &nbsp;
                    <input type="radio" id="ed_stone_type" name="ed_stone_type" value="3" > Normal
                    <p class="help-block"></p>
                    </div>
					</div>
			 </div>
			 <div class="row">
				<div class="form-group">
				<label for="scheme_code" class="col-md-4 col-md-offset-1 ">Certificate Required</label>
                      <div class="col-md-4">
                     <input type="checkbox" checked="true" class="switch" id="ed_ce_switch" name="ed_ce_switch" data-on-text="YES" data-off-text="NO" />
					   <input type="hidden" id="ed_is_certificate_req " value="0">
					   <p class="help-block"></p>
						</div>    
						</div>
					</div>
					<div class="row">
				<div class="form-group">
				<label for="scheme_code" class="col-md-4 col-md-offset-1 ">4C Required</label>
                      <div class="col-md-4">
                      <input type="checkbox" checked="true" class="switch" id="ed_isreq_switch" name="ed_isreq_switch" data-on-text="YES" data-off-text="NO" />
					   <input type="hidden" id="ed_is_4c_req" value="1">
					   <p class="help-block"></p>
						</div>    
						</div>
					</div>
					<div class="row">
				<div class="form-group">
				<label for="scheme_code" class="col-md-4 col-md-offset-1 ">Active</label>
                      <div class="col-md-4">
                      <input type="checkbox" checked="true" class="status" id="ed_stone_switch" name="ed_stone_switch" data-on-text="YES" data-off-text="NO" />
					   <input type="hidden" id="ed_stone_status" value="1">
					   <p class="help-block"></p>
						</div>    
						</div>
					</div>
      </div>
      <div class="modal-footer">
      	<a href="#" id="update_stone" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

