<style type="text/css">

.ui-autocomplete { 

max-height: 200px; 

overflow-y: scroll; 

overflow-x: hidden;

}



#myCheck:checked + #area {

  display: block !important;

}



.col-sm-3 {

    width: 33.333333%;

}



</style>

  <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
         
        Advance Bookings

     </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Advance Booking</a></li>
        <li class="active">Plan Settings</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Plan Settings</h3>
          <span id="total_plans" class="badge bg-green"></span>   
       
          <a class="btn btn-success pull-right" id="add_plan" style="display:none;" href="<?php echo base_url('index.php/admin_adv_booking/plan_set');?>"><i class="fa fa-user-plus"></i> Add</a> 
        </div>
        <div id="data_print" class="box-body">
                <!-- Alert -->
                <?php 
                	if($this->session->flashdata('plan_array'))
                	 {
                		$message = $this->session->flashdata('plan_array');
                ?>
                       <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	                    <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
	                    <?php echo $message['message']; ?>
	                  </div>
	                  
	            <?php } ?>      
                  <div class="table-responsive">
                      <table id="plan_list" class="table grid table-bordered table-striped text-center">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Plan Name</th>
                            <th>Code</th>                       
                            <th>Payable By</th>
                            <th>Active</th>
                            <th>Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                    <tfoot>
                          <tr>
                            
                          </tr>
                        </tfoot> 
                      </table>
                  </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				          <i class="fa fa-refresh fa-spin"></i>
              </div>
        </div>
    </div>
</div>
