 <!-- Content Wrapper. Contains page content -->



      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Collection App Payment List

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Dashboard</a></li>

            <li class="active">Collection App Payment List</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box">

               

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

                  <table class="table table-bordered table-striped text-center grid" id="collection_app_list">

                    <thead>

                      <tr>

                        <th>S.no</th>

                        <th>Name</th>

                        <th>Mobile</th>

                        <th>Ref.No</th> 

                        <th>Code</th>    

                        <th>Scheme A/c No</th>    

                        <th>Scheme Type</th>

                        <th>Paid Date</th>

                        <th>Transaction ID</th>	

                        <th>Mode</th>  							                      

                        <th>Status</th>

                        <th>Amount</th>

                      

                      </tr>

                    </thead>

                    <tbody>

                     
                    </tbody>

                 <tfoot>
                    
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th>Total</th>
					
					<th></th>


                    </tfoot> 

                  </table>

				  </div>

                  

                </div><!-- /.box-body -->

              </div><!-- /.box -->

            </div><!-- /.col -->

          </div><!-- /.row -->

        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->

      





