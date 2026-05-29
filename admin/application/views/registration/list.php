  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Registration
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Registration</a></li>
            <li class="active">Payment List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Customer Registration List</h3>      
                           
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
                
                  <table class="table table-bordered table-striped text-center grid">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>City</th>
                        <th>Mobile</th>                     
                        <th>Scheme Code</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Reg Date</th>
                        <th>Status</th>                       
                      </tr>
                    </thead>
                    <tbody>
                     <?php 
                     	if(isset($registrations)) {                     		
                     	 foreach($registrations as $reg)
						{
                      ?>
                       <tr>
                         <td><?php echo $reg['id_register'];?></td>
                       	 <td><?php echo $reg['name'];?></td>
                       	 <td><?php echo $reg['city'];?></td>
                         <td><?php echo $reg['mobile'];?></td>
                       	 <td><?php echo $reg['code'];?></td>
                       	 <td><?php echo $reg['scheme_type'];?></td>
                       	 <td><?php echo $reg['amount'];?></td>                       	
                       	 <td><?php echo date("d-m-Y",strtotime($reg['date_register']));?></td>
                  		<td><?php echo ( $reg['profile_complete']==1 ? anchor('account/reg/'.$reg['id_customer'].'/'.$reg['id_scheme'].'/'.$reg['id_register'], "<i class='fa fa-thumbs-up'></i> Approve", array('title'=>'Registration','class'=>'btn btn-success')):'Profile not completed' );?></td>
                  			

                       	
                       </tr>
                       <?php } } ?>
                    </tbody>
                 <!--   <tfoot>
                      <tr>
                        
                      </tr>
                    </tfoot> -->
                  </table>
                  
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      


      

