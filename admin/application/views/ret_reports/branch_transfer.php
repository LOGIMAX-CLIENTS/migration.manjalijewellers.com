<!-- Content Wrapper. Contains page content -->



      <div class="content-wrapper">



        <!-- Content Header (Page header) -->



        <section class="content-header">



          <h1>



            Reports



			 <small>Branch Transfer</small>



          </h1>



          <ol class="breadcrumb">



            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>



            <li><a href="#">Reports</a></li>



            <li class="active">Branch Transfer</li>



          </ol>



        </section>







        <!-- Main content -->



        <section class="content">



          <div class="row">



            <div class="col-xs-12">



               <div class="box box-primary">



			    <div class="box-header with-border">



                  <h3 class="box-title">Branch Transfer List</h3>  <span id="total_count" class="badge bg-green"></span>  



                </div>



                 <div class="box-body">  



                <div class="row">



				  <div class="col-md-12">  



	                  <div class="box box-default">  



	                   <div class="box-body">  



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



					   	  <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>



								<div class="col-md-2"> 



									<div class="form-group tagged">



									    <label class="trans_centre">From Branch</label>



										<select id="branch_select" class="form-control branch_filter"></select>



									</div> 



								</div> 


								<div class="col-md-2"> 



									<div class="form-group tagged">



									    <label class="trans_centre">To Branch</label>



										<select id="branch_select_to" class="form-control branch_filter"></select>



									</div> 



								</div> 


								



						    <?php }else{?>



		                     <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
		                  



		                  <?php }?>








						  <div class="col-md-2"> 



						 		 <label>Transfer Type</label>



									<select id="transtype" class="form-control" style="width:100%;">

 

									    <option value="1"> Issue </option>



									    <option value="2"> Receipt </option>



									</select>



								</div>


								<!-- Stock Type: Always default to Normal Stock -->
								<input type="hidden" name="bt_stock_type" value="0" id="normal_stock">




								<div class="col-md-2"> 



									<label>Item Transfer Type</label>


									<select id="branch_transfer_type" class="form-control" style="width:100%;">



									   <option value="0" selected> ALL </option>



									  <option value="1"> Tag </option>



									    <option value="2"> Non Tag </option>



									    <option value="3"> Sales Return</option>



									    <option value="4"> Partly Sale</option>



									    <option value="5"> Old Metal</option>



										<option value="6"> Packaging Items</option>



									</select>



								</div>


					</div>







				             <div class="row">



							 	<div class="col-md-2"> 
								 	<label>Status</label>
							 		<select id="trans_status" class="form-control" style="width:100%;">
										<option value="0"> ALL </option>
										<option value="1"> Yet to Approve </option>
										<option value="2"> In Transit </option>
										<option value="4"> Stock Updated </option>
									</select>
								</div>


								 <div class="col-md-2 report" > 



									<label>Report type</label>



										<select id="report_type" class="form-control" style="width:100%;">



										<option value="1" Selected>Detailed</option>



										<option value="2">Summary</option>



										</select>



										</div>


										<div class="col-md-2 report" style="display:none;" > 



										<label>Type</label>



											<select id="brans_type" class="form-control" style="width:100%;">



											<option value="1">BT Date</option>


											<option value="2">Approved Date</option>


											<option value="3">Download Date</option>


											</select>


											</div>



							 <div class="col-md-2 groupBy" style="display:none" > 


								<label>Group By</label>



								<select id="group_by" class="form-control" style="width:100%;">

						

								<option value="1">Branch Transfer</option>



								<option value="2">Section</option>



								<option value="3">Product</option>



								</select>



								</div>



							 <div>  







							 <div class="col-md-2 metal_filter" style="display:none"  > 



								<label></label>



								<select id="metal" class="form-control" style="width:100%;" multiple></select>



							 </div>







							 <div class="col-md-2 section" style="display:none"  > 



								<label></label>



								<select id="section_select" class="form-control" style="width:100%;"multiple></select>



							 </div>







							 <div class="col-md-2 section"  style="display:none" > 



							 <label></label>



							 <select id="category" class="form-control" style="width:100%;" multiple></select>



							 </div>







							 <div class="col-md-2 pull-left"> 



							  <label></label>



							 <div class="form-group">



							 <button type="button" id="bt_report_search" class="btn btn-info pull-right">Search</button>   



							</div>







							 </div>



							 </div>



                     



						</div>



						</div> 



						</div>







				</div> 



				</div> 



                <p></p>



                



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



			  



                  <div class="table-responsive branch_transfer">



	                 <table id="bt_report" class="table table-bordered table-striped text-center">



	                    <thead>



	                      <tr>

						    <th width="5%">S.No</th> 



	                        <th width="5%">BT Date</th> 



	                        <th width="5%">BT Id</th>       



	                        <th width="5%">BT Code</th>  



							<th width="5%">Tag Code</th>   


							<th width="5%">Old Tag Code</th>   



							<th width="5%">PO NO</th>



							<th width="5%">PO Date</th>                                                                        



	                        <th width="5%">From Branch</th>                                     



	                        <th width="5%">To Branch</th>  



							<th width="5%">Section</th>     
							
							
							<th width="5%">Purity</th>                                    


	                        <th width="5%">Product</th>      



							<th width="5%">Design</th>                                     



	                        <th width="5%">Pieces</th>  



	                        <th width="5%">Gross Wt</th>  



                            <th width="5%">Net Wt</th> 



							<th width="5%">Stn Wt</th> 



							<th width="5%">Dia Wt</th> 



                            <th width="15%">Status</th> 



                            <th width="15%">Approved Date</th> 



                            <th width="15%">Download Date</th> 



                            <th width="15%">Approved By</th> 



                            <th width="15%">Download By</th> 



	                      </tr>



	                    </thead> 



						   <tfoot>



	                    	<tr style="font-weight: bold; color:red"> 



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



	                    		<td style="text-align:right;"></td>



	                    		<td style="text-align:right;"></td>



	                    		<td style="text-align:right;"></td>



								<td style="text-align:right;"></td>



								<td style="text-align:right;"></td>





	                    		<td></td>



	                    		<td></td>



	                    		<td></td>



	                    		<td></td>



	                    		<td></td>







	                    	</tr>



	                    </tfoot>



	                 </table>



                  </div>



				  <div class="table-responsive inventory" style="display:none">



	                 <table id="inventory_report" class="table table-bordered table-striped text-center">



	                    <thead>



	                      <tr>



	                        <th width="5%">BT Date</th> 



	                        <th width="5%">BT Id</th>       



	                        <th width="5%">BT Code</th>                                     



	                        <th width="5%">From Branch</th>                                     



	                        <th width="5%">To Branch</th>                                     



	                        <th width="5%">Items name</th>



	                        <th width="5%">Pieces</th>  



                            <th width="15%">Status</th> 



                            <th width="15%">Approved Date</th> 



                            <th width="15%">Download Date</th> 



                            <th width="15%">Approved By</th> 



                            <th width="15%">Download By</th> 



	                      </tr>



	                    </thead> 



						<tbody></tbody>



						   <tfoot>



	                    	<tr style="font-weight: bold; color:red"> 



	                    		<td></td>



	                    		<td></td>



	                    		<td></td>



	                    		<td></td>



	                    		<td></td>



	                    		<td></td>



	                    		<td style="text-align:right;"></td>



	                    		<td style="text-align:right;"></td>



	                    		<td style="text-align:right;"></td>



	                    		<td></td>



	                    		<td></td>



	                    		<td></td>



	                    		<td></td>



	                    		<td></td>



								 



	                    	</tr>



	                    </tfoot>



	                 </table>



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