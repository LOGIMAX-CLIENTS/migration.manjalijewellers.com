  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Inter Wallet Transcation 

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

                  <h3 class="box-title">Credit List</h3>      

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

                        <th>Id_inter_wallet_trans</th>

                        <th>Mobile</th>

                        <th>Transcation Date</th>	

                       <th>Transcation Points</th>
					   
                        <th>Equivalent Amount in INR</th>

                      

                      </tr>

                    </thead>

                    <tbody>

                     <?php 

                     	if(isset($accounts)) {                     		

                     	 foreach($accounts as $account)

						{

                      ?>

                       <tr>

                         <td><?php echo $account['id_inter_wallet_trans'];?></td>

                      
                       	 <td><?php echo $account['mobile'];?></td>

                       

                       	 <td><?php echo date("d-m-Y",strtotime($account['date_add']));?></td>

                     
                   		 <td><?php echo $account['amount'];?></td>
						 
                   		 <td><?php echo $account['equivalent_amt'];?></td>

                  		

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

      



