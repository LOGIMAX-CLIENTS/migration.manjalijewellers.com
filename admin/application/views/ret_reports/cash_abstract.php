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

            Reports

			 <small>Cash Abstract</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Reports</a></li>

            <li class="active">Cash Abstract reportss</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               

               <div class="box box-primary">

			    <div class="box-header with-border">

                  <h3 class="box-title">Cash Abstract Listyyt</h3>  <span id="total_count" class="badge bg-green"></span>  

                 

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

        									<select id="branch_select" class="form-control ret_branch" style="width:100%;"></select>

        		                     </div> 

        		                  </div> 

        						    <?php }else{?>

        		                    	<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">

        		                    	<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 

        		                  <?php }?>


								  <div class="col-md-3">
										<div class="form-group">
											<label>Report Type</label>
											<select id="report_type" class="form-control">
												<option value="1">Summary</option>
												<option value="2" selected="">Detailed</option>
											</select>
										</div>
								    </div>

									<div class="col-md-3 groupBy" style="display:none;">
										<div class="form-group">
											<label>Group </label>
											<select id="group_type" class="form-control">
												<option value="1" selected="">Category </option>
												<option value="2" >Metal</option>
											</select>
										</div>
									</div>

        		                  

        		                

        		                <div class="col-md-3"> 

									<div class="form-group tagged">

									<label>Select Floor</label>

										<select id="floor_sel" class="form-control" style="width:100%;" multiple></select>

									</div> 

								</div> 

								

								<div class="col-md-3"> 

        		                     <div class="form-group tagged">

        		                       <label>Select Counter</label>

        									<select id="counter_sel" class="form-control" style="width:100%;" multiple></select>

        		                     </div> 

        		                </div> 

							</div>

							<div class="row">

        		                <div class="col-md-3"> 

        		                     <div class="form-group tagged">

        		                       <label>Select Employee</label>

        									<select id="emp_sel" class="form-control" style="width:100%;" multiple></select>

        		                     </div> 

        		                </div> 	

                                

								<div class="col-md-3"> 

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

										<button type="button" id="cash_abstract_search" class="btn btn-info">Search</button>   

									</div>

								</div>

							<!--	<div class="col-md-2"> 

									<label></label>

									<div class="form-group">

										<button type="button" id="cash_abstract_print" class="btn btn-info">Print</button>   

									</div>

								</div>

								<div class="col-md-2"> 

									<label></label>

									<div class="form-group">

										<button type="button" id="export_csv" class="btn btn-info">Export</button>   

									</div>

								</div>-->

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

				  <div id="cash_abstract">

				   	<div class="box box-info sales_details" >

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

									  <table id="sales_list" class="table table-bordered table-striped text-left sales_list" style="width:100%;">

							 <thead style="text-transform:uppercase;">

		                      <tr>

							  	<th colspan="1"></th>

							  	<th colspan="13">Particulers</th>

								<!-- <th colspan="1">Discount</th>

								<th colspan="1">Sales Rate</th>

								<th colspan="1">Avg Rate</th> -->

		                      </tr>

							  <tr>

                                    <th width="20%">PRODUCT</th>

                                    <th width="5%">PCS</th>   

                                    <th width="10%">GWT</th>   

                                    <th width="10%">NWT</th>   

                                    <th width="10%">DIA WT</th>   

                                    <th width="10%">TAXABLEAMOUNT</th>

                                    <th width="10%">TAX</th>

                                    <th width="10%">TOTAL AMT</th>

                                    <th width="5%">DISCOUNT</th>  

                                    <th width="10%">TOTAL COLLECTED MC</th>

                                    <th width="10%">SALES RATE</th>  

                                    <th width="10%">AVG RATE</th>

                                    <th width="10%">RATE DIFF</th>

							  </tr>

		                    </thead>

		                    <tbody ></tbody>

		                </table>

								  </div>

								</div> 

							</div> 

						</div>

					</div>

					</div>





				   	<div class="box box-info purchase_details" style="display: none;">

						<div class="box-header with-border">

						  <h3 class="box-title">Purchase Details</h3>

						  <div class="box-tools pull-right">

							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

						  </div>

						</div>

						<div class="box-body">

							<div class="row">

								<div class="box-body">

								   <div class="table-responsive">

									  <table id="purchase_list" class="table table-bordered table-striped text-center">

						                    <thead>

											  <tr>

											    <th width="15%">Metal</th>                          

											    <th width="15%">Metal</th>                                

											    <th width="15%">Gross Wt</th>                               

						                        <th width="10%">Net Wt</th>

						                        <th width="10%">Amount</th>

						                        <th width="10%">Avg Rate</th>

											  </tr>

						                    </thead>

						                    <tfoot><tr><th></th><th></th><th></th><th></th><th></th><th></th></tr></tfoot>

						               </table>

								  </div>

								</div> 

							</div> 

						</div>

						</div>

			<!-- Source Wise Report Summary Block -->
			<div class="box box-info" id="source_wise_summary_box" style="display:none;">
				<div class="box-header with-border">
				  <h3 class="box-title">
					Source Wise Report Summary &nbsp;
					<b id="ca_summary_total_amt" class="badge bg-green" style="font-size:16px;height:25px;margin-left:400px;"></b>
				  </h3>
				</div>
				<div class="box-body">
				  <div class="row" style="background:#ecf0f5;">
					<div class="col-md-4" style="text-align:center;font-weight:bold;">Showroom Collection Mode-wise</div>
					<div class="col-md-4" style="text-align:center;font-weight:bold;">Online Collection Mode-wise</div>
					<div class="col-md-4" style="text-align:center;font-weight:bold;">AdminApp Collection Mode-wise</div>
				  </div>
				  <div class="row">
					<div class="box-body col-md-4" id="ca_offline_modewise"></div>
					<div class="box-body col-md-4" id="ca_online_modewise"></div>
					<div class="box-body col-md-4" id="ca_adminApp_modewise"></div>
				  </div>
				</div>
			</div>
			<!-- /Source Wise Report Summary Block -->

                </div><!-- /.box-body -->

                <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

              </div>

            </div><!-- /.col -->

          </div><!-- /.row -->

        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->

      



