<style type="text/css">
 
  .style-key {

    font-style: italic;
  
  }

  .style-trash {

    color: red;

    cursor: pointer;

  }

</style>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Bulk Edit Log
        <small>Bulk Edit Log</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Reports</a></li>
        <li class="active">Bulk Edit Log</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
           
           <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Bulk Edit Log</h3>  <span id="total_data" class="badge bg-green"></span>  
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
						<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
						<div class="col-md-2"> 
							<div class="form-group">    
								<label>Branch</label> 
								<select type="text" class="form-control" id="branch_select" name="branch_select">
								</select>
							</div> 
						</div>
						<?php }else{?>
						<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
						<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
						<?php }?>
						<div class="col-md-2"> 
							<div class="form-group">    
								<label>Employee</label> 
								<select type="text" class="form-control" id="emp_select" name="emp_select">
								</select>
							</div> 
						</div>
            <div class="col-md-2"> 
							<div class="form-group">    
								<label>Tag Code</label> 
								<input type="text" class="form-control" id="tag_code" name="tag_code">
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
                <span id="bulkedit_log_date1"></span> - 
								<span id="bulkedit_log_date2"></span>
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
                     <table id="bulk_edit_log_list" class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th>Log Id</th>
                            <th>Date</th>
                            <th>Tag Id</th>
                            <th>Tag Code</th>
                            <th>Branch</th>
                            <th>Edit Field</th>
                            <th>Employee</th>
                            <th>Previous Values</th>
                            <th>Updated Values</th>
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