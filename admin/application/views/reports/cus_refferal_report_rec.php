




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
                  <h3 class="box-title">Customer Referrals Details</h3> 
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
                  <table  id="reff_report" class="table table-bordered table-striped text-center  reff_reports" >
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Customer Id</th>
                        <th>Customer Name</th>
                        <th>Scheme Name</th>
                        <th>Date of Payment</th>
                      </tr>
                    </thead>
                    <tbody>
                     <?php 
					 $i=1;
                     	if(isset($accounts)) {                     		
                     	 foreach($accounts as $account)
						{
                      ?>
                       <tr>
						<td><?php echo $i;?></td>
						<td><?php echo $account['id_customer'];?></td>
						 <td><?php echo $account['name'];?></td>
                    	 <td><?php echo $account['scheme_name'];?></td>
                    	 <td><?php echo $account['date_payment'];?></td>
                       </tr>
                       <?php $i++;} } ?>
                    </tbody>
                     <tfoot>
                
                    </tfoot>
                  </table>
                 </div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      



<!-- / modal -->      

