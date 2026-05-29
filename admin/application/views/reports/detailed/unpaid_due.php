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

            <li class="active">Due List</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box">

                <div class="box-header">

                  <h3 id="rpt_name" class="box-title">Due List</h3>     
					
					<input hidden id="rpt_payments1" value="<?php  echo $print['from_date']; ?>" >
					 
					<input hidden id="rpt_payments2" value="<?php  echo $print['to_date']; ?>" >
									  

				<!-- /print pdf  end-->

                <!--           <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/account/add'); ?>"><i class="fa fa-user-plus"></i> Add</a>--> 

                </div><!-- /.box-header -->

                <div class="box-body">

              

				<div class="table-responsive">

                  <table class="table table-bordered table-striped text-center grid">

                    <thead>

                      <tr>

                        <th>S.No</th>

                        <th>Name</th>

                        <th>Mobile</th>                        

						<th>Scheme A/c.No</th> 

                        <th>Code</th>    

                        <th>Scheme Type</th>

                        <th>Last Paid Date</th>

                        <!-- <th>Last Paid Month</th> -->

                        <th>Due Date</th>
						
                        <th>Due Amount</th>

                      

                      </tr>

                    </thead>

                    <tbody>

                     <?php 
							function moneyFormatIndia($num) {
							return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
                        }

                     	if(isset($accounts)) {                     		

							$sno=1;
							$tot=0;
							
                     	 foreach($accounts as $account)

						{
							$tot=$account['amount']+$tot;
                      ?>

                       <tr>

                         <td style="text-align:left;"><?php echo $sno++;?></td>

                       	 <td style="text-align:left;"><?php echo $account['name'];?></td>

                       	 <td style="text-align:left;"><?php echo $account['mobile'];?></td>

                       	   <td style="text-align:left;"><?php echo ($account['has_lucky_draw']==1? $account['group_code'] :$account['code']).' '.($account['scheme_acc_number']);?></td>

                       	 <td style="text-align:left;"><?php echo $account['code'];?></td>

                       	 <td style="text-align:left;"><?php echo $account['scheme_type'];?></td>

                       	 <td style="text-align:left;"><?php echo $account['last_paid_date'];?></td>

                      <!-- 	 <td><?php echo $account['last_paid_month'];?></td>   -->                    	

                  		 <td style="text-align:left;"><?php echo $account['due_date']; ?></td>
						 
                  		 <td style="text-align:right;"><?php echo moneyFormatIndia($account['amount']); ?></td>

                  		

                       </tr>

                       <?php } } ?>

                       </form>

                    </tbody>

               <tfoot>

                      <tr>

                        <th></th>  <th></th> <th></th> <th></th> <th></th> <th></th> <th></th> <th>Total</th> <th style="Text-align:right;"><?php echo moneyFormatIndia($tot); ?></th>

                      </tr>

                    </tfoot> 

                  </table>

				  </div>

                  

                </div><!-- /.box-body -->

              </div><!-- /.box -->

            </div><!-- /.col -->

          </div><!-- /.row -->

        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->

      

