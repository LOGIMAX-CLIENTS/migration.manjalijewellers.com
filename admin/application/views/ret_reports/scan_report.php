  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Reports

			 <small>Stock</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Reports</a></li>

            <li class="active">Stock Check report</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               

               <div class="box box-primary">

			    <div class="box-header with-border">

                  <h3 class="box-title">Stock List</h3>  <span id="total_count" class="badge bg-green"></span>  

                </div>

                 <div class="box-body">  

                  <div class="row">

				  	<div class="col-md-offset-2 col-md-8">  

	                  <div class="box box-default">  

	                   <div class="box-body"> 

	                        <div class="row">

    	                        <div align="left" >

                            		<ul class="nav nav-tabs">

                            	      	<li class="active"><a id="tag_scan" href="#crm" data-toggle="tab">Tag Scan</a></li>

                            		  	<li id="scanned_details"><a href="#live_cockpit" data-toggle="tab">Tag Scanned Details</a></li>

                            	    </ul>

                            	</div>

	                        </div>

						 </div>

	                   </div> 

	                  </div> 

                   </div> 

                   <div class="row tag_scan_filter">

				  	<div class="col-md-offset-2 col-md-8">  

	                  <div class="box box-default">  

	                   <div class="box-body">  

						   <div class="row">

						        <div class="col-md-2"> 

						            <label>Select Branch</label>

									<select id="branch_select" class="form-control" style="width:100%;"></select>

								</div>

								<div class="col-md-2">

									<label>Select Product</label> 

									<select id="prod_select" class="form-control" style="width:100%;"></select>

									</div>


									<div class="col-md-2">

											<label>Select Design</label> 

											<select id="des_select" class="form-control" style="width:100%;"></select>

											</div>
											<div class="col-md-3">

											<label>Select Sub Design</label> 

											<select id="sub_des_select" class="form-control" style="width:100%;"></select>

											</div>

											<div class="col-md-3">

											<label>Select Size</label> 

											<select id="select_size" class="form-control" style="width:100%;"></select>

											</div>

											</div>

									<div class="row">
										
								<div class="col-md-2">

									<label>Select Section</label> 

									<select id="section_select" class="form-control" style="width:100%;"></select>

									</div>

						
						

								<div class="col-md-2"> 

								    <label>TAG NO</label>

									<input type="text" class="form-control" id="tag_id"autofocus="true" placeholder="Enter Tag Number">

								</div>

								

								<div class="col-md-2">

								    <label>OLD TAG NO</label> 

                                    <input type="text" class="form-control" id="old_tag_id"autofocus="true" placeholder="Enter Old_Tag_Number">

                                </div>

								

								<div class="col-md-2"> 

								        <br>

										<button type="button" id="tag_scan_search" class="btn btn-info">Search</button>   

								</div>



								<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

								<div class="col-md-2"> 

								    <br>

									<button type="button" id="scan_close" class="btn btn-info">Close</button>   

								</div>

								 <?php }else{?>


									<?php }?> 

								

							</div>

						 </div>

	                   </div> 

	                  </div> 

                   </div>

                    <div class="row report_filter" style="display: none;">

				  	<div class="col-md-offset-2 col-md-8">  

	                  <div class="box box-default">  

	                   <div class="box-body">  

						   <div class="row">

								<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

								<div class="col-md-2"> 

									<div class="form-group tagged">

										<label>Select Branch</label>

										<select id="branch_scan_filter" class="form-control"></select>

									</div> 

								</div> 

								<?php }else{?>

								<div class="col-md-2"> 

									<div class="form-group tagged">

										<label>Select Branch</label>

										<select id="branch_scan_filter" class="form-control branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>" ></select>

									</div> 

								</div> 

									<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

								<?php }?> 

								
								<div class="col-md-2"> 

								<label>Select Section</label>

								<select id="section_filter" class="form-control" style="width:100%;"></select>

								<input type="hidden" id="id_section" value="">

								</div>


								<div class="col-md-2">

								<label>Select Category</label> 

								<select id="category_select" class="form-control" style="width:100%;"></select>

								</div>

								<div class="col-md-2">

									<label>Select Product</label> 

									<select id="prod_filter" class="form-control" style="width:100%;"></select>

									</div>

					
									<div class="col-md-2">

										<label>Select Design</label> 

										<select id="des_select" class="form-control" style="width:100%;"></select>

									</div>


										<div class="col-md-2">

										<label>Select Sub Design</label> 

										<select id="sub_des_select" class="form-control" style="width:100%;"></select>

										</div>



								<div class="col-md-2"> 

									<label></label>

									<div class="form-group">

										<button type="button" id="tag_scanned_search" class="btn btn-info">Search</button>   

									</div>

								</div>

								

							</div>

						 </div>

	                   </div> 

	                  </div> 

                   </div> 

                   

                   

                   <div class="box box-info tag_scan">

						<div class="box-header with-border">

						  <h3 class="box-title">Stock Checking Tag Details</h3><br>

						  <label>Total Scanned Pcs :&nbsp;<span id="tot_scanned_pcs"  class="badge bg-green"></span></label><br>

						  <div class="box-tools pull-right">

							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

						  </div>

						</div>

						<div class="box-body box-items no-paddingwidth" style="max-height: 300px;overflow: auto;">

							<div class="row">

								<div class="box-body">

								   <div class="table-responsive">

								    <input type="hidden" id="id_tag_scanned" value="">

									  <table id="tagging_scan_list" class="table table-bordered table-striped text-center">

										 <thead>

                							  <tr>
											    <th width="5%">Image</th>

                							    <th width="5%">Tag No</th>

												<th width="5%">Old Tag No</th>

												<th width="10%">Section</th>   

                								<th width="10%">Product</th>   

                	                            <th width="10%">Design</th>  

												<th width="10%">Sub Design</th>  

												<th width="10%">Size</th>  

                		                        <th width="10%">Pcs</th>  

                		                        <th width="10%">Gwt(G)</th>  

												<th width="10%">Nwt(G)</th>  

												<th width="10%">Dia wt(G)</th>  

                	                            <th width="10%">Mc</th> 

                	                            <th width="10%">Cost</th>  

												<th width="10%">Remarks</th>  

                							  </tr>

                		                  </thead> 

                		                  <tbody></tbody>

                		                  <tfoot></tfoot>

                						</table>

								  </div>

								</div> 

							</div> 

						</div>

					</div>

					

				   	<div class="box box-info report_filter" style="display: none;">

						<div class="box-header with-border">

						  <h3 class="box-title">Stock Checking Tag Scan</h3>

						  <div class="box-tools pull-right">

							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

						  </div>

						</div>

						<div class="box-body">

							<div class="row">

								<div class="box-body">

								   <div class="table-responsive">

									  <table id="tag_list" class="table table-bordered table-striped text-center">

										 <thead>


                							  <tr>

											    <th width="5%">Image</th>

                							    <th width="5%">Branch</th>

												<th width="10%">Section</th>



												<th width="10%">Category</th>   

                							    <th width="10%">Product</th>

												<!-- <th width="10%">Design</th>

                							    <th width="10%">Sub Design</th> -->

                							    

                							    <th width="10%">Scanned Pcs</th>  

                		                        <th width="10%">Scanned Gwt(G)</th>  

                	                            <th width="10%">Scanned Nwt(G)</th>

												<!-- <th width="10%">Scanned Dia wt(G)</th> -->


                	                            

                	                            <th width="10%">UnScanned Pcs</th>  

                		                        <th width="10%">UnScanned Gwt(G)</th>  

                	                            <th width="10%">UnScanned Nwt(G)</th>

												<!-- <th width="10%">UnScanned Dia wt(G)</th> -->


                	                            

                	                            <th width="10%">Sold/Out Pcs</th>  

                		                        <th width="10%">Sold/Out Gwt(G)</th>  

                	                            <th width="10%">Sold/Out Nwt(G)</th> 

												<!-- <th width="10%">Sold/Out  Dia wt(G)</th>  -->


                	                            <th width="10%">Tot Pcs</th>   

                								<th width="10%">Tot Gwt(G)</th>   

                	                            <th width="10%">Tot Nwt(G)</th>

												<!-- <th width="10%">Tot Dia wt(G)</th> -->


                	                            

                							  </tr>

                		                  </thead> 

                		                  <tbody></tbody>

										  <tfoot style="font-weight:bold;">
									 <tr>
										<td></td>
										<td></td>
										<td></td>

										<td></td>
										<td></td>
										<!-- <td></td>
										<td></td> -->

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

										<!-- <td></td>
										<td></td>
										<td></td>
										<td></td> -->
									    </tr></tfoot>

                						</table>

								  </div>

								</div> 

							</div> 

						</div>

					</div>

					

                </div><!-- /.box-body -->

                <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div

              </div>

            </div><!-- /.col -->

          </div><!-- /.row -->

        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->

      
	  <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header">

  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

  <h4 class="modal-title" id="myModalLabel">Image Preview</h4>

</div>

<div class="modal-body">

  <img src="" id="imagepreview" style="width: 300px; height: 264px;" >

</div>

<div class="modal-footer">

  <button type="button" class="btn btn-default danger" data-dismiss="modal">Close</button>

</div>

</div>

</div>

</div>


<div class="modal fade" id="order_des" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	
	<div class="modal-dialog">

    	<div class="modal-content">

      		<div class="modal-header">

        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        		<h4 class="modal-title" id="myModalLabel">Add Description</h4>

      		</div>

      		<div class="modal-body">			

				<div class="row">			

        			<div class="col-md-10 col-md-offset-1">

        				<label for="user_lastname">Item Description</label>

						<div class='form-group'>

			               	<textarea cols="60"  id="description" name="description" ></textarea>
							<input type ='hidden' id= scan_id value="">
							<input type ='hidden' id= des value="">


			        	</div>

			    	</div>

			    </div> 

			</div>			

			<div class="modal-footer">

				<button type="button" class="btn btn-success" id ="add_des" >Add</button>

				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

			</div>
		</div>
	</div>

</div>

