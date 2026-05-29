 

  

  <!-- Content Wrapper. Contains page content -->

    <style>

      @media print {



          html,

          body {

              height: auto;

              width: 100vh;

              margin: 0 !important;

              padding: 0 !important;

              overflow: hidden;

          }

      }

      </style>

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Reports

			 <small>Stock Issue Report</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Reports</a></li>

            <li class="active">Stock Issue Report</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               

               <div class="box box-primary">

			    <div class="box-header with-border">

                 

                 

                </div>

				

                 <div class="box-body">  

                  <div class="row">

				  	<div class="col-md-12">  

	                  <div class="box box-default">  

	                   <div class="box-body">  

						   <div class="row">

								<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

								<div class="col-md-2"> 

									<div class="form-group tagged">

										<label>Select Branch</label>

										<select id="branch_select" class="form-control branch_filter" multiple></select>

									</div> 

								</div> 

								<?php }else{?>

									<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

									<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 

								<?php }?> 


								<div class="col-md-2"> 
									<div class="form-group tagged">
										<label>Search By</label>
										<select id="search_by" class="form-control ">
											<option value="1" selected>Approved Date</option>
											<option value="2">Received Date</option>
										</select>
									</div> 
								</div>  
								
								<div class="col-md-2 det"> 
                                	<div class="form-group">
                                		<label>Status type</label>
                                		<select id="status_type" class="form-control ">
                                			<option value="0">All</option>
                                			<option value="1">Issued</option>
                                			<option value="3">Received</option>
                                		</select>
                                	</div> 
                                </div> 

								

								<div class="col-md-2"> 

									<div class="form-group">    

										<label>Date</label> 

										<?php   

											$fromdt = date("d/m/Y");

											$todt = date("d/m/Y");

									    ?>

			                   		    <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="">  

									</div> 

								</div>

								<!-- <div class="col-md-2"> 

									

									<div class='form-group tagged'>

                                    <label for="status">Select Type:</label><br>

                                        <select name="issue_type" style="width:100%;" id="issue_type" style="width: 35%;">

                                            <option value="">-Type-</option>

                                            <option value="1">Repair</option>

                                            <option value="2">Marketing</option>

											<option value="3">Photoshoot</option>

                                        </select>

                                    <span class="modal-overlay" style="display:none">

                                    <i class="fa fa-refresh fa-spin"></i>

                                    </span>

                                </div>

								</div> -->
								<div class="col-md-2 type_issue">

									<div class="form-group">

										<label>Issue Type<span class="error">*</span></label>

										<div class="form-group">

											<select class="form-control" id="issue_type" name="order[issue_type]">

												<input class="form-control" id="issue_to_cus" name="order[issue_to_cus]" type="hidden" value="">

											</select>

										</div>

									</div>

								</div>

								<div class="col-md-2 det"> 

									<div class="form-group">

										<label>Karigar</label>

										<select id="karigar" class="form-control"></select>

									</div>  

								</div>
							</div>
                            <div class="row">

								<div class="col-md-2"> 
									<div class="form-group tagged">
										<label>Report Type</label>
										<select id="report_type" class="form-control ">
											<option value="1" selected>Detailed</option>		
											<option value="2">Summary</option>
										</select>
									</div> 
								</div>  

								<div class="col-md-2 GroupBy" style="display:none" > 
									<div class="form-group">
										<label>Group By</label>
										<select id="GroupBy" class="form-control ">
											<option value="1">Category</option>
											<option value="2">Issue No</option>
										</select>
									</div> 
								</div>  

								

								

								<div class="col-md-2"> 

									<label></label>

									<div class="form-group">

										<button type="button" id="stock_issue_search" class="btn btn-info" style="margin-left:50px;">Search</button>   

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

						  <h3 class="box-title">Stock Issue List</h3>

						  <!--<div class="box-tools pull-right" style ="margin-top:-1px;">

                                     <button id="print_stock_issue" class="btn btn-success " style ="padding-bottom:4px;"><i

                                    	class="fa fa-file-print-o"></i>&nbsp;Detailed Print</button>                                     

                                 </div>-->

								

						</div>

						

						<div class="box-body StockIssue">

							<div class="row">

								<div class="box-body">

								   	<div class="table-responsive">

										<table id="stock_issue_list" class="table table-bordered table-striped text-center"  style="width:100%;">

											<thead>

												<tr>

													<th width="10%">Issue No</th>
													<th width="10%">Issue Type</th>
													<th width="10%">Issue Date</th>
													<th width="10%">Received Date</th>
													<th width="10%">Tag Code</th>
												
													<th width="10%">Pcs</th>
													<th width="10%">Weight(Gm)</th>
													<th width="10%">Issued To</th>  
													<th width="10%">Branch</th>
													<th width="10%">Status</th>
														

												</tr>


											</thead> 

											<tbody></tbody>

											<tfoot>
												<tr>
													<th colspan="3" style="text-align:center">Total:</th>
													<th style="text-align:right"></th>
													<th style="text-align:right"></th>
													<th style="text-align:right"></th>
													<th style="text-align:right"></th>
													<th style="text-align:right"></th>
													<th style="text-align:center"></th>
													<th style="text-align:center"></th>																			

												</tr>

											</tfoot>

										</table>

								  </div>

								</div> 

							</div> 

						</div>

						<div class="box-body Cat_StockIssue" style="display:none">

							<div class="row">

								<div class="box-body">

								   	<div class="table-responsive">

									   <table id="cat_stock_issue_list" class="table table-bordered table-striped text-center" style="width:100%;">

											<thead>

												<tr>

													<th width="10%">Category</th>
													<th width="10%">Issue Type</th>
													<th width="10%">Date</th>
													<th width="10%">Pcs</th>
													<th width="10%">Weight(Gm)</th>
													<th width="10%">Branch</th>
													<th width="10%">Status</th>
													

												</tr>


											</thead> 

											<tbody></tbody>

											<tfoot>
												<tr>
													<th colspan="3" style="text-align:center">Total:</th>
													<th style="text-align:right"></th>
													<th style="text-align:right"></th>
													<th style="text-align:right"></th>
													<th style="text-align:right"></th>
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

      

<div class="modal fade" id="image_pre" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"

          aria-hidden="true" data-backdrop="static" data-keyboard="false">

          <div class="modal-dialog" style="width:60%;">

              <div class="modal-content">

                  <div class="modal-header">

                      <h4 class="modal-title" id="myModalLabel">Image Preview</h4>

                  </div>

                  <div class="modal-body">

					  <div class="row">

                      	<div id="images_preview" style="margin-top: 2%;"></div>

					  </div>

                  </div>

                  <div class="modal-footer">

                      </br>

                      <button type="button" id="close_stone_details" class="btn btn-warning"

                          data-dismiss="modal">Close</button>

                  </div>

              </div>

          </div>

      </div>

