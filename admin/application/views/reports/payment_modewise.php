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
            Payment Report
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Payment Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Mode-wise Payment Report</h3>      
                <!--           <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/account/add'); ?>"><i class="fa fa-user-plus"></i> Add</a>--> 
                </div><!-- /.box-header -->
                <div class="box-body">
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
                   <div class="table-responsive">
                  <table class="table table-bordered table-striped text-center det_pay_report"  >
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Mode Name</th>
                        <th>Short Code</th>
                        <th>Successful Payments</th> 
                                  
                      </tr>
                    </thead>
                    <tbody>
                     <?php 
                     	if(isset($modewise)) { 
                     	               		
                     	 foreach($modewise as $pay)
						{
                      ?>
                       <tr>
                         <td><?php echo $pay['id_mode'];?></td>
                       	 <td><?php echo $pay['mode_name'];?></td>
                       	 <td><?php echo $pay['mode'];?></td>
                       	 <td><?php echo '<span class="label bg-green">'. $pay['success'].'</span>'?></td>
                     
                       </tr>
                       <?php } } ?>
                    </tbody>
                     
                  </table>
                  
                </div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      


 

