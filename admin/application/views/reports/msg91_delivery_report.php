<style type="text/css">
.DTTT_container{
margin-bottom:0 !important;
}
</style>
  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Msg91 
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Msg91 Delivery Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Msg91 Delivery Report</h3> <span id="total" class="badge bg-green"></span>    
                </div><!-- /.box-header -->
                <div class="box-body"> 
                <div class="row">
                    <div class="col-md-2">
						<div class="pull-left">
							<div class="form-group">
							   <button class="btn btn-default btn_date_range" id="msg_rep_date-dt-btn">
								
								<span  style="display:none;" id="msg_rep_date1"></span>
								<span  style="display:none;" id="msg_rep_date2"></span>
								<i class="fa fa-calendar"></i> Date range picker
								<i class="fa fa-caret-down"></i>
								</button>
							</div>
						 </div>						
					</div>
                </div>
                <div class="table-responsive">
                  <table  id="msg_deliv_report" class="table table-bordered table-striped text-center cus_refferal" >
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Req ID</th>
                        <th>Delivery Date</th>
                        <th>Receiver</th>                     
                        <th>Description </th> 
                      </tr>
                    </thead> 
                     <tfoot>
                 <th></th> <th></th> <th></th> <th></th><th></th>
                    </tfoot>
                  </table>
                 </div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      


<!-- modal -->      

