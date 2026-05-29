
    <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Employee Settings
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">  Employee Settings</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title"> Employee Settings List</h3> <span id="total_empset" class="badge bg-green"></span>      
                            
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
	            
				<?php if($this->session->userdata('id_profile') <= 3){?>
				<div class="row">
					<div class="col-md-3">
					<span class="lead">Filter</span>
						<div class="row">
							<div class="col-md-12">
								<label></label>
								<div class="form-group">
									<button class="btn btn-default btn_date_range" id="empset_date">
										<span  style="display:none;" id="empset1"></span>
										<span  style="display:none;" id="empset2"></span>
										<i class="fa fa-calendar"></i> Date range picker
										<i class="fa fa-caret-down"></i>
									</button>
								</div>			
							</div>	
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group" >
									<select id="emp_filter" class="form-control emp_filter"></select>
									<input type="hidden" id="blk_wast_disc_per" value="<?php echo $bulk_wastage_discount['value']; ?>" />
									<input type="hidden" id="dia_disc_min" value="<?php echo $dia_disc['min_disc_per']; ?>" />
									<input type="hidden" id="dia_disc_max" value="<?php echo $dia_disc['max_disc_per']; ?>" />
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4" style="border: 0.5px solid #e1e1e1;">
						<p class="lead">Update Employee's Access Time</p>
						<div class="row">
							<div class="col-sm-4">
								<label>Access Time From</label>
								<div class="form-group">
									<input type="time" name="" id="access_time_from" class="form-control"/> 
								</div> 
							</div>
							<div class="col-sm-4"> 
								<label>Access Time To</label>
								<div class="form-group">
									<input type="time" name="" id="access_time_to" class="form-control"/> 
								</div> 
							</div>
							<div class="col-sm-4 margin">  
								<div class="form-group" > 
									<button type="submit" id="update_access_time" class="btn btn-warning btn-flat">Update Access Time</button>
								</div>
							</div>
						</div> 
					</div>
					<div class="col-md-offset-1 col-md-3"  style="border: 0.5px solid #e1e1e1;">
						<p class="lead">Add/Update Employee's</p>
						<div class="row">   
							<div class="col-md-6">
								<div class="form-group" >
									<label>Add Employee</label>
									<select id="add_employeeset" class="form-control add_employeeset"></select>
								</div>
							</div> 
						</div> 
						<div class="row">   
							<div class="col-md-6" id="add_empset_block">
								<label> </label>
								<div class="form-group" > 
									<button class="btn btn-primary btn-flat" id="add_empset" ><i class="fa fa-user-plus"></i> Add Employee</button> 
									<input  type="hidden" value="0" id="i_increment" />
								</div>
							</div>
						</div> 
					</div>  
				</div>  
				<div class="row">
					<div class="col-md-11">
						<label> </label>						
						<div class="form-group" > 
							<button type="submit" id="empset_submit"  class="btn btn-success btn-flat pull-right">Save Settings</button>
						</div>
					</div>
				</div>  
				<?php }?>  
	            <div class="row alert">
					<div class="col-xs-12">
					<!-- Alert -->
					<span class="alert-msg">
					</span> 
					</div>
			   </div>
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
					<div id="chit_alert"></div>
					</div>
				</div>		  
                <div class="table-responsive">
                  <input  type="hidden" value="0" id="tot_limit"/> 
                  <table id="emp_set_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
						<th>Name</th>
						<th>Bulk Wastage Disc Limit</th>
						<th>Diamond Disc Limit</th>
						<th>Disc limit type</th>
						<th>Disc limit</th>
						<th>Max Gold</th>
						<th>Min Gold</th>
						<th>Max Silver</th>
						<th>Min Silver</th>
						<th>Old Metal Sale</th>
						<th>Allow Day Close</th>
						<th>Allow Manual Rate</th>
						<th>Bill Discount Approval</th>
						<th>Access Time</th>
                        <th>Action</th>
                      </tr>
                 	</thead>
                 
                  </table>
                  </div> 
                  <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>   
                </div><!-- /.box-body -->
              <!-- /.box -->
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
        <h4 class="modal-title" id="myModalLabel">Delete Employee Settings</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this employee settings record?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->  
				

<script type="text/javascript">

	 var employee    = new Array();
     var employeeArr = new Array();
     employeeArr = JSON.parse('<?php echo json_encode($employee); ?>');
</script>