  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Cover Up
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Cover Up</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">

              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Cover Up</h3>    <span id="dover_Up" class="badge bg-green"></span>
                           <a class="btn btn-success pull-right" id="Cover_Up" href="#" data-toggle="modal" ><i class="fa fa-user-plus"></i> Add</a>
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
                 <button class="btn btn-default" id="coverup_date" style="margin-top: 20px;">
                <i class="fa fa-calendar"></i> <span>Date range picker</span>
                <i class="fa fa-caret-down"></i>
                </button>
                <span id = "rpt_from_date" style ="display:none"></span>
                <span id = "rpt_to_date"  style ="display:none" ></span>

              </div>
             </div>
             </div>
          </div>
				  <div class="row">

          <input type="hidden" id="id_user"  value=<?php echo $this->session->userdata('uid');?> >
					<div class="col-sm-10 col-sm-offset-1">

					<div id="chit_alert"></div>

					</div>
				  </div>

                <div class="table-responsive">
                  <table id="coverup_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>Id</th>
                        <th>Date</th>
						            <th>Metal</th>
                        <th>Weight</th>
                        <!-- <th>Action</th> -->
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




	  <div class="modal fade" id="coverup_add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Cover Up </h4>
      </div>
      <div class="modal-body">
	  <div class="row" >
      <form id =""></form>

					<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
				</div>
          <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Metal
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       <select name="metal" id="metal_sel"> </select>
                	  <p class="help-block"></p>

                       </div>
                    </div>
				 </div>
				 <div class="row">
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Weight
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="number" class="form-control" id="weight" name="charge_code" placeholder="Enter Weight">
                	  <p class="help-block"></p>

                       </div>
                    </div>
				 </div>





			 </div>


      <div class="modal-footer">
		<!-- <a href="#" id="charge_save_and_new" class="btn btn-success">Save & New</a> -->
      	<a href="#" id="cover_save" class="btn btn-warning" >Save</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="edit_coverup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Cover Up </h4>
      </div>
      <div class="modal-body">
	   <div class="row" >

					<div class="col-md-offset-1 col-md-10" id='error'></div>
				</div>
     		 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Metal
					   <span class="error">*</span></label>
                       <div class="col-md-4">
					            <input type="hidden" id="edit-id-coverup" value="" />
                      <select name="metal" id="metal_category"> </select>
                	  <p class="help-block"></p>

                       </div>
                    </div>
				 </div>
         <div class="row">
				 	<div class="form-group">
                       <label for="charge_value_edit" class="col-md-3 col-md-offset-1 ">Weight
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="ed_weight" name="charge_value_edit" placeholder="Enter Weight" required="true">
                	  <p class="help-block"></p>

                       </div>
                    </div>
				 </div>

				</div>



      <div class="modal-footer">
      	<a href="#" id="update_coverup" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="delete_coverup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
