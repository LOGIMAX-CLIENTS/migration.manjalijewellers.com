<style type="text/css">

  .view_log_details {

    cursor: pointer;

    color: blue;

    text-decoration: underline;

  }
  
  .modal-body{
   
    word-break: break-all;
    
    margin: 0;
    
    padding: 0;
  }

</style>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Developer Log
        <small>Developer Log</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Reports</a></li>
        <li class="active">Developer Log</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
           
           <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Developer Log</h3>  <span id="total_data" class="badge bg-green"></span>  
            </div>
             <div class="box-body">  
               
               <div class="row">
                    <div class="col-xs-12">
                    <!-- Alert -->
                    <?php 
                    if($this->session->flashdata('chit_alert'))
                    {
                        $message = $this->session->flashdata('chit_alert');
                    ?>
                        <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
                            <?php echo $message['message']; ?>
                        </div>
                    <?php } ?>  
                    </div>
               </div>

               <div class="row">
						<div class="col-md-2"> 
							<div class="form-group">    
								<label>Employee</label> 
								<select type="text" class="form-control" id="emp_select" name="emp_select">
								</select>
							</div> 
						</div>
						<div class="col-md-2"> 
							<div class="form-group"> 
							<label>DateRange</label>
							<button class="btn btn-default btn_date_range" id="account-dt-btn">
								<i class="fa fa-calendar"></i> Date range picker
								<i class="fa fa-caret-down"></i>
								</button>
                &nbsp;&nbsp;
                <i class="fa fa-trash style-trash clear_date"></i>
                <br>
                &nbsp;&nbsp;&nbsp;
                <span id="form_log_date1"></span> - 
								<span id="form_log_date2"></span>
							</div>
						</div>
						<div class="col-md-2"> 
							<div class="form-group">    
								<label></label> 
								<div class="form-group">
									<button type="button" id="search" class="btn btn-info">Search</button>   
								</div>
							</div> 
						</div>
				    </div>

              
                 <div class="table-responsive">
                     <table id="form_logger_list" class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th>Log Id</th>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Form</th>
                            <th>Operation</th>
                            <th>Url</th>
                            <th>IP address</th>
                            <th>View Details</th>
                        </tr>
                        </thead> 
                        <tbody>

                        </tbody>
                     </table>
                 </div>
            </div><!-- /.box-body -->
            
            <div class="overlay" style="display:none">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
        
        </div><!-- /.col -->
      </div><!-- /.row -->
    </section><!-- /.content -->
  </div><!-- /.content-wrapper --> 

<!-- modal -->
<div class="modal fade" id="log-info-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Log Information</h4>
    </div>
    <div class="modal-body">
        <div id="log_info" class="col-md-12">
            
        </div>
    </div>
    <div class="modal-footer">
        
    </div>
    </div>
</div>
</div>
<!-- / modal --> 