  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Closed Account Details
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Closed Scheme Account</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Closed Scheme Account List</h3>  
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
                  <table id="closed_list" class="table table-bordered table-striped text-center grid" role="grid">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Account No</th>
                        <th>Customer</th>   
						<th>Mobile</th>						
                        <th>Scheme Code</th>
                        <th>Start Date</th>                        
                        <th>Type</th>
                        <th>Amount</th>                        
                        <th>Closing Balance</th>
                        <th>Closed Date</th>
                      </tr>
                    </thead>
                    <tbody>
                     <?php 
                     	if(isset($closed)) { 
                                		
                     	 foreach($closed as $account)
						{
                      ?>
                       <tr>
                         <td><?php echo $account['id_scheme_account'];?></td>
                       	 <td><?php echo $account['scheme_acc_number'];?></td>
                       	 <td><?php echo $account['name'];?></td>
                       	 <td><?php echo $account['mobile'];?></td>
                       	 <td><?php echo $account['code'];?></td>                       	 
                         <td><?php echo date("d-m-Y",strtotime($account['start_date']));?></td>
                         <td><?php echo $account['scheme_type'];?></td>
                         <td><?php echo $account['amount'];?></td>
                         <td><?php echo $account['closing_balance'];?></td>
						 <td><?php echo $account['closing_date'];?></td>
				
                       	
                       </tr>
                       <?php } } ?>
                    </tbody>
                   
					<!--   <tfoot>
                      <tr>
                        
                      </tr>
                    </tfoot> -->
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
      


