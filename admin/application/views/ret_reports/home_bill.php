  <!-- Content Wrapper. Contains page content -->



      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Reports

			 <small>Sales Report</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Reports</a></li>

            <li class="active">Sales Report</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               

               <div class="box box-primary">

			    <div class="box-header with-border">

                  <h3 class="box-title">Home Bill List</h3>  <span id="total_count" class="badge bg-green"></span>  

                 

                </div>

                 <div class="box-body">  

                  <div class="row">

				  	<div class="col-md-offset-2 col-md-8">  

	                  <div class="box box-default">  

	                   <div class="box-body">  

						   <div class="row">

								<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

								<div class="col-md-3"> 

									<div class="form-group tagged">

										<label>Select Branch</label>

										<select id="branch_select" class="form-control branch_filter"></select>

									</div> 

								</div> 

								<?php }else{?>

									<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

									<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 

								<?php }?> 


							<div class="col-md-3"> 

									 <div class="form-group">

            		                    <div class="input-group">

            		                        <br>

            		                       <button class="btn btn-default btn_date_range" id="rpt_date_picker">

            							    

            		                        <i class="fa fa-calendar"></i> Date range picker

            		                        <i class="fa fa-caret-down"></i>

            		                      </button>

										  <span  style="display:none;" id="rpt_from_date"></span>

            							<span  style="display:none;" id="rpt_to_date"></span>

										  <span id="reportrange"></span>

            		                    </div>

            		                 </div><!-- /.form group -->

								</div>


								<div class="col-md-3"> 

									<label>Metal</label>

									<select id="metal" class="form-control" style="width:100%;" multiple></select>

								</div>

								<div class="col-md-3"> 

								<label>Category</label>

								<select id="category_select" class="form-control" style="width:100%;" multiple></select>

								</div>

						
							</div>

							<div class="row">

							<div class="col-md-3"> 

								<label>Product</label>

								<select id="prod_select" class="form-control" style="width:100%;" ></select>

								</div>


								<div class="col-md-3"> 

								<label>Design</label>

								<select id="des_select" class="form-control" style="width:100%;" ></select>

								</div>


								<div class="col-md-3"> 

								<label>Sub Design</label>

								<select id="sub_des_select" class="form-control" style="width:100%;" ></select>

								</div>


					

								<!--<div class="col-md-2"> 

									<label>Select Village</label>

									<select id="village_select" class="form-control" style="width:100%;"></select>

								</div>

								<div class="col-md-2"> 

									<label>Select Customer</label>

									<select id="cus_select" class="form-control" style="width:100%;"></select>

								</div>-->

								<div class="col-md-3"> 

									<label></label>

									<div class="form-group">

										<button type="button" id="home_bill_search" class="btn btn-info">Search</button>   

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

				   	<div class="box box-info stock_details">

						<div class="box-header with-border">

						  <h3 class="box-title">Sales Details</h3>

						  <div class="box-tools pull-right">

							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

						  </div>

						</div>

						<div class="box-body">

							<div class="row">

								<div class="box-body">

								   <div class="table-responsive">

									  <table id="bill_list" class="table table-bordered table-striped text-center">

										 <thead>

		                     

							  <tr style="text-transform:uppercase;">

							    <th width="10%">Bill No</th>

							    <th width="10%">Bill Date</th>
								
								<th width="10%">Customer</th>
								  
								<th width="10%">Mobile</th>

								<th width="5%">Category</th>

							    <th width="5%">Metal</th>

							    <th width="5%">Product</th>

							    <th width="5%">Design</th>

								<th width="5%">Sub Design</th>

							    <th width="5%">Tag No</th>

							    <th width="10%">Piece</th>

							    <th width="10%">Gross Wt</th>

							    <th width="10%">Net Wt</th>

							    <th width="10%">Tax</th>

							    <th width="10%">Amount</th>

							    <th width="10%">Stone Amt</th>

							  </tr>

		                    </thead> 

		                    <tbody></tbody>

		                     <tfoot>

	                    	<tr>

	                    		<td></td>

	                    		<td></td>

	                    		<td></td>

								<td></td>

								<td></td> 

								<td></td> 

								<td></td> 
								
								<td></td>

								<td></td> 

								<td></td> 

								<td></td> 

								<td></td> 

								<td></td> 

								<td></td> 

								<td></td>

								<td></td> 


	                    	</tr>

	                    </tfoot>

									 </table>

								  </div>

								</div> 

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

      



