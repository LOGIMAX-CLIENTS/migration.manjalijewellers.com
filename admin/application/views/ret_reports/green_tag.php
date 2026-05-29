  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Reports

			 <small>Green Tag Sales Details Report</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Green Tag Sales Details Report</a></li>

            <li class="active">Green Tag Sales Details Report</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               

               <div class="box box-primary">

			    <div class="box-header with-border">

                  <h3 class="box-title">Green Tag Sales Details Report</h3>  <span id="total_count" class="badge bg-green"></span>  

                 

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

        									<select id="branch_select" class="form-control ret_branch" style="width:100%;"></select>

        		                     </div> 

        		                  </div> 

        						    <?php }else{?>

        		                    	<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">

        		                    	<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 

        		                  <?php }?>

								  <div class="col-md-2">

										<div class="form-group tagged">

										<label>Select Metal</label>

											<select id="metal" class="form-control " style="width:100%;"></select>

										</div>

										</div>

										<div class="col-md-2">

										<div class="form-group tagged">

										<label>Select Category</label>

											<select id="category" class="form-control " style="width:100%;"></select>

										</div>

										</div>

    								<div class="col-md-2"> 

    									<div class="form-group">    

    										<label>Select Date</label> </br>

        										<button class="btn btn-default btn_date_range" id="rpt_payment_date">

                							    <span  style="display:none;" id="rpt_payments1"></span>

                							    <span  style="display:none;" id="rpt_payments2"></span>

                		                        <i class="fa fa-calendar"></i> Date range picker

                		                        <i class="fa fa-caret-down"></i>

                		                      </button>

    									</div> 

    								</div>


									

    								<div class="col-md-2"> 

    									<label></label>

    									<div class="form-group">

    										<button type="button" id="green_tag_search" class="btn btn-info">Search</button>   

    									</div>

								    </div>

    							</div>

    						 </div>

    	                   </div> 

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

								       <input type="hidden" id="emp_sales_incentive_gold_perg" value="<?php echo $emp_sales_incentive_gold_perg;?>">

								       <input type="hidden" id="emp_sales_incentive_silver_perg" value="<?php echo $emp_sales_incentive_silver_perg;?>">

									  <table id="green_tag_list" class="table table-bordered table-striped text-center">

										 <thead>

            							  <tr>

            							    <th>Bill No</th>

            							    <th>Est No</th>

            							    <th>Bill Date</th>

            							    <th>Tag Date</th>
            							    <th>Old Tag Date</th>

            							    <th>Tag Code</th>
            							    <th>Old Tag Code</th>

            							    <th>G.wt</th>

            							    <th>N.wt</th>

            							    <th>Incentive Amt</th>

            							    <th>Product Name</th>

            							    <th>Item Cost</th>

            							    <th>Employee</th>

            							    <th>Emp Code</th>

            							  </tr>

		                            </thead> 

		                             <tbody></tbody>

		                             <tfoot><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>

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



