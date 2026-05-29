  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Scheme Details

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Masters</a></li>

            <li class="active">Scheme Account</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box box-primary">

                <div class="box-header">

                  <h3 id="rpt_name" class="box-title">Scheme Account List</h3>      
					
					
					<input hidden id="rpt_payments1" value="<?php  echo $print['from_date']; ?>" >
					 
					<input hidden id="rpt_payments2" value="<?php  echo $print['to_date']; ?>" >
                      

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

                 <div class="responsive">

                  <table id="sch_acc_list" class="table table-bordered table-striped text-center grid">

                    <thead>

                      <tr>

                        <th style="text-align:left;">Sno</th>

                        <th style="text-align:left;">Account.No</th>                        

                        <th style="text-align:left;">Customer</th> 

                         <th style="text-align:left;">Mobile</th>                       

                        <th style="text-align:left;">Scheme Code</th>

						<th style="text-align:left;">Type</th>

                        <th style="text-align:left;">Start Date</th>

                        <th style="text-align:left;">Joined Through</th>

                        <th style="text-align:right;">Total Installment</th> 
                        
                        <th style="text-align:right;">Initial Amt</th> 
                        
                        <th style="text-align:right;">Initial Wgt</th> 

                        <th style="text-align:right;">Installment Payable</th>

                       

                        

                      </tr>

                    </thead>

                    <tbody>

                     <?php 
					 
					 function moneyFormatIndia($num) {
                          return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
                        }

                     	if(isset($accounts)) { 

                               $tot=0; 	
								$sno=1;							   

                     	 foreach($accounts as $account)

						{
							$tot= $account['initial_amount']+$tot;
							$gtot= formatMetalWeight($account['initial_weight'])+$gtot;
							
                      ?>

                       <tr>

                         <!-- <td style="text-align:left;"><?php echo $account['id_scheme_account'];?></td> -->
                         <td style="text-align:left;"><?php echo $sno++;?></td>

                         <!-- <td style="text-align:left;"><?php echo ($account['has_lucky_draw']==1? $account['group_code'] :$account['code']).' '.($account['scheme_acc_number']);?></td> -->
                          <td style="text-align:left;"><?php echo ($account['has_lucky_draw']==1? $account['group_code'] :$account['scheme_acc_number']);?></td>

                       	 <!-- <td><?php echo $account['scheme_acc_number'];?></td> -->

                       	 <td style="text-align:left;"><?php echo $account['name'];?></td>

                       	 <td style="text-align:left;"><?php echo $account['mobile'];?></td>

                       	 <td style="text-align:left;"><?php echo $account['code'];?></td>                       	 

                         <td style="text-align:left;"><?php echo $account['scheme_type'];?></td>

						 <td style="text-align:left;"><?php echo date("d-m-Y",strtotime($account['start_date']));?></td>

						 <td style="text-align:left;"><?php echo $account['added_by'];?></td>

						 <td style="text-align:right;"><?php echo $account['total_installments'];?></td>
						   
						 <td style="text-align:right;"><?php echo $account['initial_amount'];?></td>
						   
						 <td style="text-align:right;"><?php echo formatMetalWeight($account['initial_weight']);?></td>
						  
                       	 <td style="text-align:right;"><?php echo ($account['sch_type'] ==1?"max".$account['max_weight']."g/month":  moneyFormatIndia($account['amount']));?></td>
							
                       </tr>

                       <?php } } ?>

                    </tbody>

               <tfoot>

                      <tr>

                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th style="text-align:right;">Total</th>
                        <th style="text-align:right;"><?php echo moneyFormatIndia($tot); ?></th> <br>
                        <th style="text-align:right;"><?php echo $gtot."g"; ?></th>
						<th></th>
                        

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

      



