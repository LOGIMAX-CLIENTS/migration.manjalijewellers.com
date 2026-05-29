<!-- Content Wrapper. Contains page content -->

    

  <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Supplier Contract  Report

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Reports</a></li>

            <li class="active">Supplier contract Report</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               

               <div class="box box-primary">

                 <div class="box-body">  

					

				   	<div class="box box-info stock_details">

						<div class="box-header with-border">



						  <div class="row">

                              <div class="col-md-3"> 

                                      <div class="form-group tagged">

                                          <label>Type :<span class="error"></span></label>

                                          <div class="form-group">  

                                              <input type="radio" class = "aprroval_for" id="wast_approval" name="app[approval_for]" value="0" checked>&nbsp;&nbsp;Wastage&nbsp;&nbsp;

                        					    <input type="radio" class = "aprroval_for"  id="stn_approval"  name="app[approval_for]" value="1" >&nbsp;&nbsp;Stone&nbsp;&nbsp;

                                            </div> 

            						   </div> 

    						   </div>  

                   

                                <div class="col-md-2"> 

									<label>Select Karigar</label>

									<select id="karigar" class="form-control" style="width:100%;"></select>

								</div>

								<div class="col-md-2"> 

									<label>Select Status</label>

									<select id="price_status" class="form-control" style="width:100%;">

									    <option value="">All</option>

									    <option value="0">Yet to Approved</option>

									    <option value="1">Approved</option>

									    <option value="2">Rejected</option>

										<option value="3">Hold</option>

									</select>

								</div>

								<div class="col-md-2"> 

									<label></label>

									<div class="form-group">

										<button type="button" id="supplier_contract_search" class="btn btn-info">Search</button>   

									</div>

								</div>

							</div>

							

						</div>

						<div class="box-body">

							<div class="row">

								<div class="box-body">

								   <div class="table-responsive wastages" style="display:block;">

									  <table id="supplier_contract_list" class="table table-bordered table-striped text-center">

										 <thead>

		                    

										<tr>

											<th width="10%">#</th>   

											<th width="10%">Name</th>   

											<th width="10%">Created Date</th>

											<th width="10%">Approved Date</th>

											<th width="10%">Rejected Date</th> 

											<th width="10%">Product</th>  

											<th width="10%">Design</th>  

											<th width="10%">Sub Design</th> 

											<th width="10%">MC Type</th> 

											<th width="10%">MC Value</th> 

											<th width="10%">Touch</th> 

											<th width="10%">V.A(%)</th>  

											<th width="10%">Status</th>  

											<th width="10%">Created By</th> 

											<th width="10%">Approved By</th> 

											<th width="10%">Rejected By</th> 

											

							  </tr>

		                    </thead> 

		                    <tbody>

                            </tbody>

							 </table>

								  </div>



					<div class="table-responsive stones" style="display:none;">

                    <table id="karigar_stones_list" class="table table-bordered table-striped text-center">

                      <thead>

                        <tr>

                          <th>ID</th>

                          <th>Karigar</th>

						  <th>Created Date</th>

						  <th>Approved Date</th>

						  <th>Rejected Date</th> 

                          <th>Stone Type</th>

                          <th>Stone Name</th>

                          <th>Uom</th>

                          <th>Calc Type</th>

                          <th>From Wt</th>

                          <th>To Wt</th>

                          <th>Quality Code</th>

                          <th>Clarity</th>

                          <th>Color</th>

                          <th>Cut</th>

                          <th>Shape</th>

						  <th>Rate</th>

						  <th>Created By</th> 

						  <th>Approved By</th> 

						  <th>Rejected by</th>

                         

                        

                        </tr>

                      </thead>

                      <tbody><td></td><td></td><td></td><td></td><td></td><td></td></tbody>

                    </table>

                  </div> 



								</div> 

							</div> 

						</div>

					</div>

                </div>

                <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

              </div>

            </div>

          </div>

        </section>

</div>

