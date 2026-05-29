  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">reports</a></li>
            <li class="active">Joining request</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">Scheme Joining Request</h3>      
                      <!--<a class="btn btn-success pull-right" href="<?php // echo base_url('index.php/account/add'); ?>"><i class="fa fa-user-plus"></i> Add</a> -->
                </div><!-- /.box-header -->
                <div class="box-body">
              
                <div class="table_responsive">
                  <table id="sch_acc_list" class="table table-bordered table-striped text-center grid">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Chit No</th>
                        <th>Customer</th> 
                        <th>Mobile</th>                      
                        <th>Comments</th>                        
                        <th>Status</th>  
                      </tr>
                    </thead>
                    <tbody>
                     <?php 
                     	if(isset($requests)) {                     		
                     	 foreach($requests as $request)
						{
                      ?>
                       <tr>
                         <td><?php echo $request['id_enquiry'];?></td>
                         <td><?php echo date("d-m-Y",strtotime($request['date_enquiry']));?></td>
                       	 <td><?php echo $request['chit_acc_number'];?></td>
                       	 
                       	 <td><?php echo $request['name'];?></td>
                       	 <td><?php echo $request['mobile'];?></td>
                       	 <td><?php echo $request['comments'];?></td> 
                       	 <td><?php echo ($request['status']==1?'Approved':($request['status']==2?'Hold':($request['status']==3?'Rejected':'Pending')));?></td>                       	 
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
      
