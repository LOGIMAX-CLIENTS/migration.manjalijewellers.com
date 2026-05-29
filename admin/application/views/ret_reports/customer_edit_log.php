 <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Profile Update Log
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">MIS Reports</a></li>
            <li class="active">Profile Update Log</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Profile Update Log</h3>    <span id="Customer_Edit_Log" class="badge bg-green"></span>
                           
                </div><!-- /.box-header -->
                <div class="box-body">
               
               
				<div class="row">
            <div class="col-sm-12">
            <div class="pull-left">
              <div class="form-group">
                 <button class="btn btn-default" id="customer_edit_log_date" style="margin-top: 20px;">
				 <span  style="display:none;" id="customer_edit_log_date1"></span>
            	<span  style="display:none;" id="customer_edit_log_date2"></span>
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
                  <table id="customer_edit_log_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
						<th>Previous Details</th>
						<th>Updated Details</th>
						<th>Updated By</th>
                        <th>Updated On</th>
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