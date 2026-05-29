    <style>
      .stickyBlk{
        margin: 0 auto;
        top: 0;
        width: 100%;
        z-index: 999;
        background: #fff;
        position: static;
        padding: 1%;
      }
      .custom-box{
	  	
	  }
    </style>

    <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Material Rate
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">  Material Rate List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title"> Material Rate List</h3> <span id="total_mtrrate" class="badge bg-green"></span>      
                            
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
<div class="stickyBlk">
				<div class="form-group">
					<div class="col-md-2">
						<label></label>
						<div class="form-group">
							<button class="btn btn-default btn_date_range" id="mtrrate_date">
								<span  style="display:none;" id="mtr1"></span>
								<span  style="display:none;" id="mtr2"></span>
								<i class="fa fa-calendar"></i> Date range picker
								<i class="fa fa-caret-down"></i>
							</button>
						</div>			
					</div>	
					<div class="col-md-2">
						<div class="form-group" >
							<label>Filter Material </label>
							<select id="material_filter" class="form-control material_filter"></select>
						</div>
					</div>
					<span class="custom-box">
					<div class="col-md-offset-2 col-md-2">
						<div class="form-group" >
							<label>Add Material Rate</label>
							<select id="addmatrt_lst" class="form-control addmatrt_lst"></select>
						</div>
					</div>
					<div class="col-md-2" id="add_mrate_block">
						<label> </label>
						<div class="form-group" > 
            <?php if($access['add']==1){?>
							<button class="btn btn-primary" id="add_material_rate" ><i class="fa fa-user-plus"></i> Add Rate</button> 
              <?php }?>
							<input  type="hidden" value="0" id="i_increment" />
						</div>
					</div>
					<div class="col-md-2 pull-right">
						<label> </label>						
						<div class="form-group" > 
            <?php if($access['add']==1){?>
							<button type="submit" id="rate_submit"  class="btn btn-success">Save</button>
              <?php }?>
						</div>
					</div>
					</span> 
				</div>
      </div>
				  <div class="row">
					<div class="col-sm-10 col-sm-offset-1">
					<div id="chit_alert"></div>
					</div>
				  </div>				
                  <div class="table-responsive">
                  <input  type="hidden" value="0" id="tot_m_rate"/> 
                  <table id="mtrrate_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
						<th>Name</th>
						<th>Material Rate</th>
						<th>Effective Date</th>
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
        <h4 class="modal-title" id="myModalLabel">Delete Material Rate</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this material rate record?</strong>
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
        <h4 class="modal-title" id="myModalLabel">Add Material Rate </h4>
      </div>
      <div class="modal-body">
	  <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
				</div>
				<div class="row">
				<div class="form-group">
					<label for="scheme_code" class="col-md-3 col-md-offset-1 ">Material Name
					<span class="error">*</span></label>
					<div class="col-md-4">
						<select class="form-control" id="ad_mtrrate_name"> </select>
						<input type="hidden" id="mtrid_rate" name="mtr_rate"/>
						<p class="help-block"></p>
					</div>
					</div>
				</div> 				
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Material Rate
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="type" step="any" class="form-control" id="mtr_rate" name="material_rate" placeholder=" Material Rate" required="true"> 
                	     <p class="help-block"></p>
                       </div>
                    </div>
				 </div>
				 <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Effective Date
					   </label>
                       <div class="col-md-4">
					   <input class="form-control datemask" data-date-format="dd-mm-yyyy" id="mtr_eff_date" name="mtr_eff_date"  value="<?php echo set_value('effective_date','effective_date');?>" type="text"/>
                	  <p class="help-block"></p>
                       </div>
                    </div>
				 </div> 
      </div>
      <div class="modal-footer">
		<a href="#" id="add_newmtrrate" class="btn btn-success">Save & New</a>
      	<a href="#" id="add_mtrrate" class="btn btn-warning" data-dismiss="modal">Save & Close</a>
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
        <h4 class="modal-title" id="myModalLabel">Edit Material Rate</h4>
      </div>
      <div class="modal-body">
	   <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error-msg1'></div>
	   </div>
	   <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Material Name
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <select class="form-control" id="ed_mtrrate_name"> </select>
						 <input type="hidden" id="edmtrrate_id" name="mtr_rate"/>
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div> 
			 <div class="row">
			 	<div class="form-group">
                   <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Material Rate
				   <span class="error">*</span></label>
                   <div class="col-md-4">
                   <input type="hidden" id="edit-id" value="" />
                   	 <input type="number" class="form-control" id="ed_material_rate" step="any" name="ed_material_rate"> 
					<p class="help-block"></p>
                   </div>
                </div>
			 </div> 
			  <div class="row">   
                <div class="form-group">
                   <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Effective Date
				   <span class="error">*</span></label>
                   <div class="col-md-4">
				   <input class="form-control datemask"  data-date-format="dd-mm-yyyy" id="ed_eff_date" name="ed_eff_date" value=""/>
            	     <p class="help-block"></p>	
                   </div>
                </div>
			 </div>
      </div>
      <div class="modal-footer">
      	<a href="#" id="update_mtrrate" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

<script type="text/javascript">
     var material  = new Array();
     var materialArr = new Array();
     materialArr = JSON.parse('<?php echo json_encode($material); ?>');
</script>