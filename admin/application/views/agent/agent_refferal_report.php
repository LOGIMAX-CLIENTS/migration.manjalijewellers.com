




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
            Agent Report
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Agent Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Agent Reffered Customer Report</h3>      
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
                  <table  id="agent_reff_report" class="table table-bordered table-striped text-center  reff_reports" >
                    <thead>
                      <tr>
                        <th>Customer Id</th>
                        <th>Date of Payment</th>
                        <th>Customer Name</th>
                         <th>Mobile</th>
                        <th>Scheme Payment</th>
                        <th>Credit</th>
                        <th>Debit</th>
                        <th>Credit/Debit for</th>
                        <th>Scheme Account No</th>
                        <th>Receipt No</th>
                      <!--  <th>Credit Type</th> -->
                      </tr>
                    </thead>
                    <tbody>
                     <?php 
                     	if(isset($accounts)) {                     		
                     	 foreach($accounts as $account)
						{
                      ?>
                       <tr>
							 <td><?php echo $account['id_customer'];?></td>
							 <td><?php echo $account['date_payment'];?></td>
						 <td><?php echo $account['cus_name'];?></td>
						  <td><?php echo $account['mobile'];?></td>
						 <td><?php echo $account['payment_amount'];?></td>
						 <td><b style="color:#29eb29;"><?php echo $account['benefit'];?></b></td>
						 <td><b style="color:red;"><?php echo $account['debit'];?></b></td>
						  <td><?php echo $account['credit_for'];?></td>
                    	 <td><?php echo $account['scheme_acc_number'];?></td>
                    	 <td><?php echo $account['receipt_no'];?></td>
                      <!--    <?php if($account['issue_type'] == 'Credit') {?>
						    <td><b style="color:#29eb29;"><?php echo $account['issue_type']?></b></td>
						   <?php }else{ ?>
						   <td><b style="color:red;"><?php echo $account['issue_type']?></b></td>
						   <?php } ?> -->
                       	
                       </tr>
                       <?php } } ?>
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

