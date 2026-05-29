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

            Re-Tagging Report

          </h1>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               

               <div class="box box-primary">

			   

                 <div class="box-body">  

                  <div class="row">

				  	<div class="col-md-offset-2 col-md-8">  

	                  <div class="box box-default">  

	                   <div class="box-body">  

						   <div class="row">

						       	<div class="col-md-2"> 

						       	    <div class="form-group">

						       	        <label>Select Type</label>

						       	        <select class="form-control" id="retag_report_type">

						       	            <option value="4">Old Metal</option>

						       	            <option value="1">Sales Return</option>

						       	            <option value="3">Partly Sale</option>

						       	            <option value="5">NonTag Sales Return</option>

						       	        </select>

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

                  <div class="form-group">

                      <label>Select Metal</label>

                      <select class="form-control" id="metal">

                      </select>

                  </div>

                  </div>

                  <!-- <div class="col-md-2"> 

                      <div class="form-group">

                          <label>Select Karigar</label>

                          <select class="form-control" id="karigar">

                          </select>

                      </div>

                      </div> -->

								<div class="col-md-2"> 

									<label></label>

									<div class="form-group">

										<button type="button" id="retag_report_search" class="btn btn-info">Search</button>   

									</div>

								</div>

							</div>

						 </div>

	                   </div> 

	                  </div> 

                   </div> 

                

				  <div id="cash_abstract">

				   	<div class="box box-info sales_details" >

						<div class="box-header with-border">

						  <div class="box-tools pull-right">

							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

						  </div>

						</div>

                        <div class="box-body">

                            <div class="row">

                                <div class="box-body">

                                    <div class="table-responsive old_metal" style="">

                                        <table id="retag_list" class="table table-bordered table-striped text-center sales_list" style="width:100%;">

                                            <thead style="text-transform:uppercase;">

                                            <tr>

                                                <th>#</th>

                                                <th>Date</th>

                                                <th>Metal</th>

                                                <th>Process Type</th>

                                                <th>Process For</th>

                                                <th>Product</th>

                                                <th>Gwt</th>

                                                <th>Nwt</th>

                                                </tr>

                                            </thead>

                                            <tbody ></tbody>

                                              <tfoot>

                                                    <tr style="font-weight:bold; color:red">

                                                        <td style="text-align: right"></td>

                                                        <td style="text-align: right"></td>

                                                        <td style="text-align: right"></td>

                                                        <td style="text-align: right"></td>

                                                        <td style="text-align: right"></td>

                                                        <td style="text-align: right"></td>

                                                        <td style="text-align: right"></td>

                                                        <td style="text-align: right"></td>


                                                    </tr>

                                              </tfoot>

                                        </table>

                                    </div>

                                    

                                    <div class="table-responsive partly_sale" style="display:none">

                                        <table id="partly_sale_list" class="table table-bordered table-striped text-center sales_list" style="width:100%;">

                                            <thead style="text-transform:uppercase;">

                                            <tr>

                                                <th>#</th>

                                                <th>Date</th>

                                                <th>Metal</th>

                                                <th>Process Type</th>

                                                <th>Process For</th>

                                                <th>Supplier</th>

                                                <th>TAG NO</th>

                                                <th>Product</th>

                                                <th>Gwt</th>

                                                <th>Nwt</th>

                                                </tr>

                                            </thead>

                                            <tbody ></tbody>

                                            <tfoot><tr style="font-weight:bold; color:red">

                                            <td style="text-align: right"></td>

                                            <td style="text-align: right"></td>

                                            <td style="text-align: right"></td>

                                            <td style="text-align: right"></td>

                                            <td style="text-align: right"></td>

                                            <td style="text-align: right"></td>

                                            <td style="text-align: right"></td>

                                            <td style="text-align: right"></td>

                                            <td style="text-align: right"></td>

                                            <td style="text-align: right"></td>


                                          </tr></tfoot>

                                        </table>

                                    </div>

                                    

                                    <div class="table-responsive sales_return" style="display:none">

                                    	<table id="salesreturn_retag_list" class="table table-bordered table-striped text-center sales_list" style="width:100%;">

                                    		<thead style="text-transform:uppercase;">

                                    		<tr>

                                    			<th width="5%">#</th>

                                    			<th width="5%">Branch</th>

                                    			<th width="10%">SR Bill no</th>

                                          <th width="10%">SR Bill Date</th>

                                          <th width="10%">Metal</th>

                                          <th width="10%">Karigar</th>


                                    			<th width="15%">Tag No</th>

                                    			<th width="10%">Product</th>

                                    			<th width="10%">Retag Date</th>

                                    			<th width="15%">New Tag No</th>

                                    			<th width="5%">Gwt</th>

                                    			<th width="5%">Nwt</th>

                                    			<th width="10%">Process Type</th>

                                    			<th width="10%">Process For</th>

                                    			

                                    			</tr>

                                    		</thead>

                                    		<tbody ></tbody>

                                    		<tfoot>

                                              <tr style="font-weight:bold; color:red">

                                              <td style="text-align:right;"></td>

                                              <td style="text-align:right;"></td>

                                              <td style="text-align:right;"></td>

                                              <td style="text-align:right;"></td>

                                              <td style="text-align:right;"></td>

                                              <td style="text-align:right;"></td>

                                              <td style="text-align:right;"></td>

                                              <td style="text-align:right;"></td>

                                              <td style="text-align:right;"></td>

                                              <td style="text-align:right;"></td>

                                              <td style="text-align:right;"></td>

                                              <td style="text-align:right;"></td>

                                              <td style="text-align:right;"></td>

                                               <td style="text-align:right;"></td>

                                            </tr></tfoot>

                                    	</table>

                                    </div>

                    

                    

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

      



