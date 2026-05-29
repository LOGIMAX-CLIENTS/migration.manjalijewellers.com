 <!-- Content Wrapper. Contains page content -->



      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Payment

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

                  <h3 id="rpt_name" class="box-title">Scheme Payment List</h3>      
				   
				  
					<input hidden id="rpt_payments1" value="<?php  echo $print['from_date']; ?>" >
					 
					<input hidden id="rpt_payments2" value="<?php  echo $print['to_date']; ?>" >
					
                      
            
                       

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

                        <th style="text-align:left;">S.no</th>

                        <th style="text-align:left;">Name</th>

                        <th style="text-align:left;">Mobile</th>

                        <th style="text-align:left;">Ref.No</th> 

                        <th style="text-align:left;">Code</th>    

                        <th style="text-align:left;">Scheme A/c No</th>    

                        <th style="text-align:left;">Scheme Type</th>

                        <th style="text-align:left;">Paid Date</th>

                        <th style="text-align:left;">transaction ID</th>	

                        <th style="text-align:left;">Mode</th>  							                      

                        <th style="text-align:left;">Status</th>

                        <th style="text-align:right;">Amount</th>

                      

                      </tr>

                    </thead>

                    <tbody>

                     <?php 

                     	if(isset($accounts)) {  
							function moneyFormatIndia($num) {
                          return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
                        }	
							$sno=1;
							$tot=0;

                     	 foreach($accounts as $account)

						{
						 $tot= $account['payment_amount']+$tot;
                      ?>

                       <tr>

                         <td style="text-align:left;"><?php echo $sno++; ?></td>

                       	 <td style="text-align:left;"><?php echo $account['name'];?></td>

                       	 <td style="text-align:left;"><?php echo $account['mobile'];?></td>

                       	 <td style="text-align:left;"><?php echo $account['ref_no'];?></td>

                       	 <td style="text-align:left;"><?php echo $account['code'];?></td>

                       	 <!-- <td style="text-align:left;"><?php echo ($account['has_lucky_draw']==1? $account['group_code'] :$account['code']).' '.($account['scheme_acc_number']);?></td> -->

					     <td style="text-align:left;"><?php echo ($account['has_lucky_draw']==1? $account['group_code'] :$account['scheme_acc_number']);?></td>

                       	 <td style="text-align:left;"><?php echo $account['scheme_type'];?></td>

                       	 <td style="text-align:left;"><?php echo date("d-m-Y",strtotime($account['date_payment']));?></td>

                       	 <td style="text-align:left;"><?php echo $account['id_transaction'];?></td>

                  		 <td style="text-align:left;"><?php echo $account['payment_mode'];?></td>

                  		 <td style="text-align:left;"><span class="badge bg-<?php echo $account['color']; ?>"><?php echo $account['payment_status']; ?></span></td>
						 
						 <td style="text-align:right;"><?php echo moneyFormatIndia($account['payment_amount']);?></td>
                  		

                       </tr>

                       <?php } } ?>

                       </form>

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
					<th style="text-align:left;">Total</th>
					
					<th style="text-align:right;"> <?php echo moneyFormatIndia($tot);?></th>


                    </tfoot> 

                  </table>

				  </div>

                  

                </div><!-- /.box-body -->

              </div><!-- /.box -->

            </div><!-- /.col -->

          </div><!-- /.row -->

        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->

      





<!-- modal -->      

<div class="modal fade" id="pay-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Scheme Payment</h4>

      </div>

      <div class="modal-body">

         <div class="fluid-container">

         	<div class="row">

         		<div class="col-sm-6">

              		<div class="form-group">

      					<label for="">Account Name</label>

      					<input type="hidden" id="acc_id" name="acc_id"/>

		                    <input type='text' id="acc_name" name="acc_name" readonly="true" class="form-control" />

		                 

          			</div>		

	             </div>	   

	             <div class="col-sm-6">

              		<div class="form-group">

      					<label for="">Payment Date</label>

      					<div class='input-group date'>

		                    <input type='text'  id='pay_date' name="pay_date"  class="form-control myDatePicker" />

		                    <span class="input-group-addon">

		                        <span class="glyphicon glyphicon-calendar"></span>

		                    </span>

		                </div>

          			</div>		

	             </div>	   

	         </div>

         </div>    

         <div class="row">

         	<div class="col-sm-6">

          		<div class="form-group">

  					<label for="">Amount</label>

  						<div class="input-group">

  					<span class="input-group-addon">

		                        <span class="fa fa-inr"></span>

		                    </span>

	                <input type='text' id="sch_amount" name="sch_amount" readonly="true" class="form-control" />

	                </div>

      			</div>		

	         </div>	 

	         <div class="col-sm-6">

          		<div class="form-group">

  					<label for="">Payment mode</label>

  						<select id="pay_mode" name="pay_mode" class="form-control">

  							<option value="1">Cash</option>

  							<option value="2">Cheque</option>

  							<option value="3">Credit Card</option>

  							<option value="4">Debit Card</option>  							

  						</select>	              

      			</div>		

	         </div>	 

         </div>

         <div class="row">

         	 <div class="col-sm-12">

          		<div class="form-group">

  					<label for="">Remark</label>

  						<textarea id="pay_remark" name="pay_remark" class="form-control"></textarea>

      			</div>		

	         </div>	 

         </div>

      </div>

      <div class="modal-footer">

      	<a href="#" id="pay_amount" class="btn btn-danger" >Pay</a>

        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->      



