  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Inter Wallet Account

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Payment</a></li>

            <li class="active">Payment List</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">


           

              <div class="box">

                <div class="box-header">

                  <h3 class="box-title">Account Details</h3>      

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

                  <table class="table table-bordered table-striped text-center grid">

                    <thead>

                      <tr>

                        <th>Id</th>

                        <th>Mobile</th>

                        <th>date_add</th>	

                       <th>Available Points</th>
                       
                           <th>Name</th>
                       <th>Member Since</th>
               <th>Accounts</th>  
                      <th>Address</th>

                      </tr>

                    </thead>

                    <tbody>

                     <?php 

                     	if(isset($accounts)) {                     		

                     	 foreach($accounts as $account)

						{

                      ?>

                       <tr>

                         <td><?php echo $account['id_inter_wal_ac'];?></td>

                      
                       	 <td><?php echo $account['mobile'];?></td>

                       

                       	 <td><?php echo date("d-m-Y",strtotime($account['date_add']));?></td>

                     
                   		 <td><?php echo $account['available_points'];?></td>
 <td><?php echo $account['firstname'];?></td>
                   		 	 
                   		 	 	  <td><?php echo date("d-m-Y",strtotime($account['date_add']));?></td>	 
                   		 	 	  <td><?php echo $account['accounts'];?></td>

                  		<td><?php echo $account['address'];?></td>

                       </tr>

                       <?php } } ?>

                       </form>

                    </tbody>

                 <!--   <tfoot>

                      <tr>

                        

                      </tr>

                    </tfoot> -->

                  </table>

				  </div>

                  

                </div><!-- /.box-body -->

              </div><!-- /.box -->

            </div><!-- /.col -->

          </div><!-- /.row -->

        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->

      



