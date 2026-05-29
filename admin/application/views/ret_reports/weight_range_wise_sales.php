<style>

@media print 

{    

     table tr td.sales

     { 

       font-weight:bold;

     }

 }

 </style> 

<!-- Content Wrapper. Contains page content -->



   <div class="content-wrapper">

     <!-- Content Header (Page header) -->

     <section class="content-header">

       <h1>

         Weight Range Wise Sales 

       </h1>

     </section>



     <!-- Main content -->

     <section class="content">

          <div class="row">

            <div class="col-xs-12">

               

               <div class="box box-primary">

                 <div class="box-body">  

                  <div class="row">

				  	<div class="col-md-12">  

	                  <div class="box box-default">  

	                   <div class="box-body">  

						   <div class="row">

						   <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

								<div class="col-md-2"> 

									<div class="form-group tagged">

										<label class="trans_centre">Select Branch</label>

										<select id="branch_select" class="form-control branch_filter"></select>

									</div> 

								</div> 



								<?php }else{?>

								<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

								<?php }?>

								<div class="col-md-2"> 

									<div class="form-group">    

										<label>Select Product</label> 

										<select id="prod_select" class="form-control" style="width:100%;"></select>

									</div> 

								</div>

								<div class="col-md-2"> 

									<div class="form-group">    

										<label>Select Design</label> 

										<select id="des_select" style="width:100%;"></select>

									</div> 

								</div>

								

								<div class="col-md-2"> 

									<div class="form-group">    

										<label>Select Sub Design</label> 

										<select id="sub_des_select" style="width:100%;"></select>

									</div> 

								</div>

								

								<div class="col-md-2"> 

									<div class="form-group">    

										<label>Select Weight</label> 

										<select id="wt_select" style="width: 100%;" multiple></select>

									</div> 

								</div>

								

								<div class="col-md-2"> 

									<div class="form-group">    

										<label>Select Karigar</label> 

										<select id="karigar" style="width: 100%;"></select>

									</div> 

								</div>

                            </div>

                            <div class="row">

                                <div class="col-md-2"> 

									 <div class="form-group">

            		                    <div class="input-group">

            		                        <br>

            		                       <button class="btn btn-default btn_date_range" id="rpt_payment_date">

            							    <span  style="display:none;" id="rpt_payments1"></span>

            							    <span  style="display:none;" id="rpt_payments2"></span>

            		                        <i class="fa fa-calendar"></i> Date range picker

            		                        <i class="fa fa-caret-down"></i>

            		                      </button>

            		                    </div>

            		                 </div><!-- /.form group -->

								</div>

								<div class="col-md-2"> 

									<label></label>

									<div class="form-group">

										<button type="button" id="weight_range_sales_search" class="btn btn-info">Search</button>   

									</div>

								</div>

								

							</div>

						 </div>

	                   </div> 

	                  </div> 

                   </div> 

                

				   <div class="row">

						<div class="col-xs-12">

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

						</div>

				   </div>

			  

                   <div class="row">

	                   <div class="col-md-12">

	                   	<div class="table-responsive">

                           <table id="stock_sales_list" class="table table-bordered table-striped text-center sales_list" style="width:100%;">

                                <thead style="text-transform:uppercase;">

                                    <tr>

                                        <th>Product</th>

                                        <th>Design</th>

                                        <th>Sub Design</th>

                                        <th>Weight Range</th>

                                        <th>Sales Pcs</th>

                                        <th>Sales Gwt</th>

                                        <th>Age</th>

                                        <th>Min Pcs</th>

                                        <th>Max Pcs</th>

                                    </tr>

                                </thead>

                                <tbody ></tbody>    

                            </table>

	                  </div>

	                   </div>

                   </div>

                </div><!-- /.box-body -->

                <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

              </div>

            </div><!-- /.col -->

          </div><!-- /.row -->

        </section><!-- /.content -->

   </div><!-- /.content-wrapper -->

   



<!-- CHIT DEPOSIT -->

<div class="modal fade" id="stone_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

<div class="modal-dialog modal-lg">

 <div class="modal-content">

     <div class="modal-header">

         <h4 class="modal-title" id="myModalLabel">Stone Details</h4>

     </div>

     <div class="modal-body">

         <div>

         <table id="stone_details" class="table table-bordered table-striped text-center">

             <thead>

             <tr>

             <th>Stone Name</th>

             <th>Stone Pcs</th>

             <th>Weight</th>

             <th>Rate</th>

             <th>Amount</th>

             </tr>

             </thead> 

             <tbody>

             </tbody>										

         </table>

         </div>

     </div>

     <div class="modal-footer">

         <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

     </div>

 </div>

</div>

</div>

<!-- CHIT DEPOSIT -->