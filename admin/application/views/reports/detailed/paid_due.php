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

            <li><a href="#">Reports</a></li>

            <li class="active">Payment List</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box">

                <div class="box-header">

                  <h3 class="box-title">Scheme Payment List</h3>      

                <!--           <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/account/add'); ?>"><i class="fa fa-user-plus"></i> Add</a>--> 

                </div><!-- /.box-header -->

                <div class="box-body">

              

				<div class="table-responsive">

                  <table class="table table-bordered table-striped text-center grid">

                    <thead>

                      <tr>

                        <th>ID</th>

						<th>Scheme A/c.No</th> 

                        <th>Name</th>

                        <th>Mobile</th>                        

                        <th>Code</th>    

                        <th>Scheme Type</th>

                        <th>Last Paid Date</th>

                        <th>Last Paid Month</th>

                        <th>Status</th>

                      

                      </tr>

                    </thead>

                    <tbody>

                     <?php 

                     	if(isset($accounts)) {                     		

                     	 foreach($accounts as $account)

						{

                      ?>

                       <tr>

                         <td><?php echo $account['id_scheme_account'];?></td>

                       	 <td><?php echo $account['account_no'];?></td>

                       	 <td><?php echo $account['name'];?></td>

                       	 <td><?php echo $account['mobile'];?></td>

                       	 <td><?php echo $account['code'];?></td>

                       	 <td><?php echo $account['scheme_type'];?></td>

                       	 <td><?php echo $account['last_paid_date'];?></td>

                       	 <td><?php echo $account['last_paid_month'];?></td>                       	

                  		 <td><?php echo $account['paid_status']; ?></td>

                  		

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

      

