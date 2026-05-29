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
            Unverified Payments
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Unverified Payments List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">Unverified Payment List</h3>  
                  <button id="check_transaction" class="btn btn-success pull-right" ><i class="glyphicon glyphicon-saved"></i> Verify Payment</button>    
                <!--           <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/account/add'); ?>"><i class="fa fa-user-plus"></i> Add</a>--> 
                </div><!-- /.box-header -->
                <div class="box-body">
                <!-- Alert -->
               
                       <div class="alert alert-success alert-dismissable" style="display: none;">
	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	                    <h4><i class="icon fa fa-check"></i> Payment verification!</h4>
	                        <div id="alert_msg" ></div>
	                  </div>
	                  
	          
	            <div id="verify_form"></div>
  					 <div class="row">
  					  
						<div class="col-md-12">
						      <img class="img-responsive" id="img_loader"  src="<?php echo base_url(); ?>assets/img/spinner.gif" style="margin:0 auto;display:none;"/>
							  <div class="table-responsive">							  
								  <div id="failed_report">
								    
								  </div>
							   </div>
						  
						</div>
					 </div>
                 
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
       