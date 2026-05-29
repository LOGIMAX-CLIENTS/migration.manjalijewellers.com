      <!-- Content Wrapper. Contains page content -->
    <style>
    	.remove-btn{
			margin-top: -168px;
		    margin-left: -38px;
		    background-color: #e51712 !important;
		    border: none;
		    color: white !important;
		}
		.custom-bx{
			box-shadow: none;
			border: 0.5px solid #e1e1e1;
		}
		.bt_search_list {
			max-height: 400px;
		}
		

    .bt_search_list thead th {
      position: sticky;
      top: 0;
	  background: #FFFFFF;
    } 
    </style>
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Inventory
            <small>Branch Transfer Request</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Inventory</a></li>
            <li class="active">Branch Transfer Request</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add Items To Branch Transfer Request </h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
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
	             <!-- form -->
				<?php  echo form_open_multipart(""); ?>	
				<div class="row">
					<div class="col-md-5">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
								<input type="hidden" id="form_secret" name="form_secret"  value="<?php echo get_form_secret_key(); ?>"> <!-- From Helper Class.. -->
				                  <label for="">Type <span class="error"> *</span></label>
				                  <div class="form-group"> 
				                     <?php 
				                     $profile=$this->admin_settings_model->profileDB('get',$this->session->userdata('profile'));
				                     ?>
				                      <?php if($profile['tag_transfer']==1){?>
				                      <input type="radio" name="transfer_item_type" id="type1" value="1" checked> <label for="type1">Tagged</label>  &nbsp;&nbsp;
				                      <?php }?>
				                      
				                      <?php if($profile['non_tag_transfer']==1){?>
				                      <input type="radio" name="transfer_item_type" id="type2" value="2"> <label for="type2">Non Tagged</label>
				                      <?php }?>
				                      
				                      <?php if($profile['purchase_item_transfer']==1){?>
				                      <input type="radio" name="transfer_item_type" id="type3" value="3"> <label for="type3">Purchase Items</label>
				                      <?php }?>
				                      
				                       <?php if($profile['packaging_item_transfer']==1){?>
				                      <input type="radio" name="transfer_item_type" id="type4" value="4"> <label for="type4">Packaging Items</label>
				                      <?php }?>
				                      
				                      <input type="radio" name="transfer_item_type" id="type5" value="5"> <label for="type5">Repair Orders</label>&nbsp;&nbsp;
					              </div>
				                </div>
							</div> 
						</div>
			
						<?php if($profile['allow_other_issue']== 1){ ?>
						<?php if($this->session->userdata('id_branch')==0){?>
						<div class="row"> 
							<div class="col-md-12"> 
								<div class="form-group">
				                  <label class="checkbox-inline"><input type="checkbox" id="isOtherIssue" name="isOtherIssue"/>Other Issue</label> 
				                  <input type="hidden" id="other_issue_branch" value="<?php echo $other_issue_branch;?>">
				                  <input type="hidden" id="is_otp_required_for_approval" value="<?php echo $is_otp_required_for_approval;?>">
				                </div>
							</div>
						</div>
						<?php }?>
						<?php }?>
						
						
						<div class="row remark">
						    <div class="col-sm-5" > 
    					 		 <label >Remark</label>	
    					 		<textarea class="form-control" id="remark" rows="5" cols="100" > </textarea>
    					 	</div>	
						</div>
						
					</div>
					<div class="col-md-7"> 
						<div class="row">
					        <div class="col-md-12">
					          <div class="box box-default custom-bx"> 
					            <div class="box-body">
									<div class="row">
										<div class="col-md-5">
											<div class="form-group">
												<div class="row">
													<div class="col-md-5 ">
													    <input type="hidden" id="head_office_branch" value="<?php echo $head_office_branch;?>">
													    <input type="hidden" id="logged_gst" name="logged_gst" value=""/>                                                                           
														<label for="" class="control-label pull-right">From Branch <span class="error"> *</span></label>
													</div>
													<div class="col-md-7">
														<?php if($this->session->userdata('id_branch')==0){?>
														<select class="form-control from_branch" id="from_brn" required></select>
														<?php }else{?> 
														<span><?php echo $this->session->userdata('branch_name') ?></span>
		                    							<input type="hidden" id="from_brn"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
		                                                <?php }?>
													</div>
												</div>  										      
										    </div>
										</div>
										<div class="col-md-1 tagged" align="right">  </div>
										<div class="col-md-5 to_branch_blk">
											<div class="form-group">
												<div class="row">
													<div class="col-md-5 ">
														<label for="" class="control-label pull-right toBranch">To Branch <span class="error"> *</span></label>
														<input type ="hidden" id="allow_transfer_type" value="<?php echo $profile['allow_bill_type']?>" />
									 					 <input type="hidden" id="is_eda" value=1>
													</div>
													<div class="col-md-7">
														
														<select class="form-control to_branch" id="to_brn" required></select>														
													</div>
												</div>  										      
										    </div>
										</div>

										<div class="col-md-5 tagged"> 
												<div class="row">
													<div class="col-md-5 ">
														<label for="" class="control-label pull-right">Tagged Branch</label> 
													</div>
													<div class="col-md-7">
														<select class="form-control" id="tagged_branch" style="width:180px;"></select>
													</div>
												</div> 
										</div> 
										
										
										<div class="col-md-5 old_metal" style="display: none;">
											<div class="form-group">
												<div class="row">
													<div class="col-md-5 ">
														<label for="" class="control-label pull-right">Date Range <span class="error"> *</span></label>
													</div>
													<div class="col-md-7">
														<button type="button" class="btn btn-default btn_date_range" id="bill_date">
														<span  style="display:none;" id="rpt_payments1"></span>
														<span  style="display:none;" id="rpt_payments2"></span>
														<i class="fa fa-calendar"></i> Date range picker
														<i class="fa fa-caret-down"></i>
													</button>														
													</div>
												</div>  										      
										    </div>
										</div>
										
										<div class="col-md-5 packaging" style="display: none;">
											<div class="form-group">
												<div class="row">
													<div class="col-md-5 ">
														<label for="" class="control-label pull-right">Select Item <span class="error"> *</span></label>
													</div>
													<div class="col-md-7">
													    <select class="form-control" id="select_item"></select>														
													</div>
												</div>  										      
										    </div>
										</div>
										
										<div class="col-md-5 packaging" style="display: none;">
											<div class="form-group">
												<div class="row">
													<div class="col-md-5 ">
														<label for="" class="control-label pull-right">No of Pcs <span class="error"> *</span></label>
													</div>
													<div class="col-md-7">
													    <input type="number" class="form-control" id="packaging_no_of_pcs">	
														<span><b>Available pcs : </b></span>
                                                         <span class="available_tot_pcs" style="font-weight: bold;">0</span>												
													</div>
												</div>  										      
										    </div>
										</div>
										
									</div>
									<p class="help-block"></p>
					               <div class="row">
					               	   <div class="col-md-5">
											<div class="form-group brn_lot" style="display: block;"> <!--Client asked to remove LOT NO SEARCH--> 
												<div class="row">
													<div class="col-md-5 ">
														<label for="" class="control-label pull-right">Lot No</label> 
													</div>
													<div class="col-md-7">
														<select class="form-control" id="lotno"></select>
													</div>
												</div> 
											</div> 

											<div class="form-group brn_nt_rec" style="display: none;">
												<div class="row">
													<div class="col-md-5 ">
														<label for="" class="control-label pull-right">NonTag Receipt</label> 
													</div>
													<div class="col-md-7">
														<select class="form-control" id="nt_receipt" style="width:180px;"></select>
													</div>
												</div> 
											</div> 
											
											<div class="form-group brn_section "> 
												<div class="row">
													<div class="col-md-5 ">
														<label for="" class="control-label pull-right">Section</label> 
													</div>
													<div class="col-md-7">
													<select class="form-control" id="section_select"></select>	
													</div>
												</div> 
											</div>
											
											<div class="form-group non_tagged" style="display: none;">   
												<div class="row">
													<div class="col-md-5 ">
														<label for="" class="control-label pull-right">Product</label> 
													</div>
													<div class="col-md-7">
														<div class="form-group" > 
															<input type="text" class="form-control product" id="nt_product" placeholder="Product Name/Code" autocomplete="off">
										                    <input type="hidden" class="form-control" id="id_product_nt">
										                    <span class="prodAlert"></span>
														</div> 
													</div>
												</div>  
												<p class="help-block"></p>
												<div class="row"> 
													<div class="col-md-offset-5 col-md-7">
														<div class="form-group"> 
															<button type="button" class="btn btn-info btn-flat btrn_search pull-right">Search</button>   
														</div>
													</div> 
												</div> 
											</div>
											<div class="form-group tagged"> 
												<div class="row">
													<div class="col-md-5 ">
														<label for="" class="control-label pull-right">Product</label> 
													</div>
													<div class="col-md-7">
														<input type="text" class="form-control product" id="product" placeholder="Product Name/Code" autocomplete="off">
														<input type="hidden" class="form-control" id="id_product">
														<span class="prodAlert"></span>
													</div>
												</div> 
											</div> 
											<div class="form-group tagged"> 
												<div class="row">
													<div class="col-md-5 ">
														<label for="" class="control-label pull-right">Design</label> 
													</div>
													<div class="col-md-7">
														<input type="text" class="form-control" id="design" placeholder="Design"  autocomplete="off">
														<input type="hidden" class="form-control" id="id_design">
													</div>
												</div> 
											</div>
											<div class="form-group tagged"> 
												<div class="row">
													<div class="col-md-5 ">
														<label for="" class="control-label pull-right">Tag Code</label> 
													</div>
													<div class="col-md-7">
														<input type="text" class="form-control" id="tag_no" placeholder="Tag Code"  autocomplete="off">
													</div>
												</div> 
											</div>
											
											<div class="form-group tagged"> 
												<div class="row">
													<div class="col-md-5 ">
														<label for="" class="control-label pull-right">Old Tag Code</label> 
													</div>
													<div class="col-md-7">
														<input type="text" class="form-control" id="old_tag_no" placeholder="Old Tag Code"  autocomplete="off">
													</div>
												</div> 
											</div>
											
											<div class="form-group orders" style="display: none;"> 
												<div class="row">
													<div class="col-md-5 ">
														<label for="" class="control-label pull-right">Order No</label> 
													</div>
													<div class="col-md-7">
														<input type="text" class="form-control" id="order_no" placeholder="Order No"  autocomplete="off">
													</div>
												</div> 
											</div>
											
											<div class="form-group tagged"> 
												<div class="row"> 
													<div class="col-md-offset-5 col-md-7">
														<button type="button" class="btn btn-info btn-flat btrn_search pull-right">Search</button>   
													</div>
												</div> 
											</div> 
											
											<div class="form-group old_metal" style="display:none;"> 
												<div class="row"> 
													<div class="col-md-offset-5 col-md-7">
														<button type="button" class="btn btn-info btn-flat btrn_search pull-right">Search</button>   
													</div>
												</div> 
											</div>
											
											<div class="form-group order_Search" style="display: none;"> 
												<div class="row"> 
													<div class="col-md-offset-5 col-md-7">
														<button type="button" class="btn btn-info btn-flat btrn_search pull-right">Search</button>   
													</div>
												</div> 
											</div>
											
											<div class="form-group packaging" style="display:none;"> 
												<div class="row"> 
													<div class="col-md-offset-5 col-md-7">
														<button type="button" class="btn btn-info btn-flat btn_add_pack_item pull-right" id="btn_add_pack_item">Add</button>   
													</div>
												</div> 
											</div>
											
										</div> 
										<div class="col-md-1 tagged" align="right"> OR </div>
										<div class="col-md-5 tagged"> 
											<div class="row">				    	
									    		<div class="col-sm-5">
									    			<label class="control-label pull-right">Search By Esti No. </label>
										 		</div>
										 		<div class="col-sm-7">
										 			<div class="form-group" > 
											 			<div class="input-group" > 
															<input type="text" class="form-control" id="esti_no" placeholder="Estimation No"  autocomplete="off">
															<span class="input-group-btn">
										                      <button type="button" id="search_est_no" class="btn btn-info btn-flat">Search</button>
										                    </span>
														</div>
														<p id="searchEstiAlert" class="error" align="left"></p>
													</div>
										 		</div>
										 	</div>
										 </div>

										 <div class="col-md-1" align="right"> </div>
										<div class="col-md-6 karigar"  style="display: none;">
											<div class="form-group">
												<div class="row">
													<div class="col-md-6 ">
														<label for="" class="control-label pull-right ">Karigar<span class="error"> *</span></label>
													</div>
													<div class="col-md-6">
														<select class="form-control " id="karigar" style="width: 200px;" required></select>														
													</div>
												</div>  										      
										    </div>
										</div>
										<div class="col-md-6">
											<div class="form-group tagged"> <!--Client asked to remove TAG DATE RANGE -->  
												<div class="row"  style="display: none;">
													<div class="col-md-5 ">
														<label for="" class="control-label pull-right">Tag Date Range</label> 
													</div>
													<div class="col-md-7">
														<input type="text" class="form-control pull-right dateRangePicker" id="tag_dt_rng" placeholder="Tag From Date - Tag To Date">
													</div>
												</div> 
											</div>
											<div class="form-group" style="display: none;"> <!--Client asked to remove LOT DATE RANGE -->  
												<div class="row">
													<div class="col-md-5 ">
														<label for="" class="control-label pull-right">Lot Date Range</label> 
													</div>
													<div class="col-md-7">
														<input type="text" class="form-control pull-right dateRangePicker" id="lot_dt_rng" placeholder="Lot From Date - Lot To Date">
													</div>
												</div> 
											</div> 
								        </div>
										<!-- ./col -->
					               </div>
					            </div>
					            <!-- /.box-body -->
					          </div>
					          <!-- /.box -->  
							</div>
						</div>
					</div>
				</div>
				<div class="row non_tagged " style="display: none">
					<div class="col-md-12"> 
						<p class="page-header">
							Non Tagged Search Result :
							 
					   </p>
					   <div class="table-responsive">
		                 <table id="bt_nt_search_list" class="table table-bordered table-striped text-center">
		                    <thead>
		                      <tr>
		                        <th width="10%"><label class="checkbox-inline"><input type="checkbox" id="nt_select_all" name="nt_select_all" value="all"/>All</label></th>   
	                          	<th width="20%">Section</th>   
	                          	<th width="20%">Product</th>   
	                          	<th width="20%">Design</th>    
	                          	<th width="10%">Pcs</th>  
	                          	<th width="20%">G.wt</th>  
	                          	<th width="20%">N.wt</th>  
		                      </tr>
		                    </thead>
		                    <tfoot>
		                    	<tr>
		                    		<th colspan="4">Total</th>
		                    		<td><input type="text" class="nt_pieces" disabled="true" placeholder="Pieces"/></td>
		                    		<td><input type="text" class="nt_grs_wt" disabled="true" placeholder="Gross Weight"/></td>
		                    		<td><input type="text" class="nt_net_wt" disabled="true" placeholder="Net Weight"/></td>
		                    	</tr>
		                    </tfoot> 
		                 </table>
	                  </div>
					</div> 
				</div> 
				<p class="help-block"></p>  
				<div class="row tagged">
					<div class="col-md-12"> 
						<p class="page-header">
							Tagged Item Search Result :
							<button id="add_to_list" type="button" class="btn btn-warning btn-flat pull-right"><i class="fa fa-plus"></i> Add To Preview</button> 
					   </p>
					   <div>
							<p>
								<span style="margin-right:15%;margin-left: 25%;">
									<span style="margin-right: 25px;"><b>TOTAL : </b></span>
									<span><b>No.of Tags : </b></span>
									<span class="tot_tag_count" style="font-bold;">0</span>
									<span style="margin-left: 40px;"><b>PCS : </b></span>
									<span class="tot_bt_pcs" style="font-bold;">0</span>
									<span style="margin-left: 40px;"><b>GRS WEIGHT : </b></span>
									<span class="tot_bt_gross_wt" style="font-bold;">0.000</span>
									<span style="margin-left: 40px;"><b>NET WEIGHT : </b></span>
									<span class="tot_bt_net_wt" style="font-bold;">0.000</span>
									<span style="margin-left: 40px;"><b>DIA WEIGHT : </b></span>
									<span class="tot_bt_dia_wt" style="font-bold;">0.000</span>
								</span>
							</p> 
                        </div></br>
					   <div class="table-responsive">
		                 <table id="bt_search_list" class="table table-bordered table-striped text-center bt_search_list">
		                    <thead>
		                      <tr>
		                        <th width="5%"><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>   
	                          	<th width="5%">Tag No</th>  
								<th width="5%">Old Tag No</th>
	                          	<th width="5%">Lot No</th> 
								<th width="5%">Section</th>  
	                          	<th width="5%">Product</th>  
	                          	<th width="5%">Design</th>  
	                          	<th width="5%">Tag Date</th>  
	                          	<th width="5%">Pcs</th>  
	                          	<th width="5%">G.wt</th>  
	                          	<th width="5%">N.wt</th> 
								<th width="5%">Dia.wt</th>   
	                          	<th width="5%">Action</th>  
		                      </tr>
		                    </thead>
		                    <tbody></tbody>
		                    <!--<tfoot><tr><th></th><th></th><th></th><th> </th><th>Total:</th><th></th><th></th><th></th><th></th></tr></tfoot> -->
		                 </table>
	                  </div>
					</div> 
				</div>	 
				<p class="help-block"></p>
				<!--End of row-->
				
				<div class="row tagged">
					<div class="col-md-12"> 
						<p class="page-header">
							Preview :
					   </p>
					   <div class="table-responsive">
		                 <table id="bt_list" class="table table-bordered table-striped text-center">
		                    <thead>
		                      <tr>
		                        <th>#</th>  
	                          	<th>Tag No</th>  
								<th>Old Tag No</th>  
	                          	<th>Lot No</th> 
								<th>Section</th>  
	                          	<th>Product</th>  
	                          	<th>Design</th>  
	                          	<th>Tag Date</th>  
	                          	<th>Pcs</th>  
	                          	<th>G.wt</th>  
	                          	<th>N.wt</th>  
								<th>Dia.wt</th>  
	                          	<th>Remarks</th>  
		                      </tr>
		                    </thead>
		                    <tbody>
		                    </tbody> 
		                    <tfoot>
		                    	<tr>
		                    		<th colspan="8">Total</th>
		                    		<td><input type="text" class="prev_pieces" disabled="true" placeholder="Pieces"/></td>
		                    		<td><input type="text" class="prev_grs_wt" disabled="true" placeholder="Gross Weight"/></td>
		                    		<td><input type="text" class="prev_net_wt" disabled="true" placeholder="Net Weight"/></td>
									<td><input type="text" class="prev_dia_wt" disabled="true" placeholder="Dia Weight"/></td>
								</tr>
		                    </tfoot>
		                 </table>
	                  </div>
					</div> 
				</div>	
				<!--End of row-->  
				
						<!-- old Metal-->
				<div class="row old_metal" style="display: none;">
					<div class="col-md-12"> 
						<p class="page-header">
							Preview :
					   </p>
					   <div class="table-responsive">
		                 <table id="old_metal_list" class="table table-bordered table-striped text-center">
		                    <thead>
		                      <tr>
		                        <th><label class="checkbox-inline"><input type="checkbox" id="old_metal_select_all" name="select_all" value="all"/>All</label></th>  
	                          	<th>Category</th>   
	                          	<th>G.Wt</th>  
	                          	<th>N.wt</th> 
								<th>Dia.wt</th>   
	                          	<th>Amount</th>  
								<th>Detail</th>  
		                      </tr>
		                    </thead>
		                    <tbody>
		                    </tbody> 
		                    <tfoot>
		                        <tr>
		                    		<th colspan="2">Total</th>
		                    		<td><input type="text" class="old_prev_grs_wt" disabled="true" placeholder="Gross Weight"/></td>
		                    		<td><input type="text" class="old_prev_net_wt" disabled="true" placeholder="Net Weight"/></td>
									<td><input type="text" class="old_prev_dia_wt" disabled="true" placeholder="Dia Weight"/></td>
		                    		<td><input type="text" class="old_prev_amt" disabled="true" placeholder="Amount"/></td>
		                    	</tr>
		                    </tfoot>
		                 </table>
	                  </div>
					</div> 
				</div>
				<!-- old Metal-->
				
				<!-- Packaging Itmes-->
			   <div class="row packaging" style="display: none;">
					<div class="col-md-12"> 
						<p class="page-header">
							Preview :
					   </p>
					   <div class="table-responsive">
		                 <table id="packaging_list" class="table table-bordered table-striped text-center">
		                    <thead>
		                      <tr>
	                          	<th>Item Name</th>  
	                          	<th>No of Pcs</th>  
	                          	<th>Action</th>  
		                      </tr>
		                    </thead>
		                    <tbody>
		                    </tbody> 
		                    <tfoot>
		                    </tfoot>
		                 </table>
	                  </div>
					</div> 
				</div>	
				<!--End of row-->  
				<!-- Packaging Itmes-->
				
				
				<div class="row orders" style="display: none;">
					<div class="col-md-12"> 
						<p class="page-header">
							Order Item Details
							
					   </p>
					   <div class="table-responsive">
		                 <table id="order_list" class="table table-bordered table-striped text-center">
		                    <thead>
		                      <tr>
		                        <th width="5%"><label class="checkbox-inline"><input type="checkbox" id="order_select_all" name="select_all" value="all"/>All</label></th>   
	                          	<th width="5%">Order No</th> 
								<th width="5%">Tag No</th>  
								<th width="5%">Section</th>   
	                          	<th width="5%">Product</th>  
	                          	<th width="5%">Design</th>  
	                          	<th width="5%">Gross Wt</th>  
								<th width="5%">Less Wt</th>  
								<th width="5%">Net Wt</th>  
								<th width="5%">Dia Wt (CT)</th>  
	                          	<th width="5%">Pcs</th>  
	                          	<th width="5%">Action</th>  
		                      </tr>
		                    </thead>
		                   		<tbody></tbody>
		                   		 <tfoot>
		                    	<tr style="font-weight: bold;">
		                    		<th colspan="6">Total</th>
		                    		<td class="cus_gwt">0.00</td>
									<td class="cus_lwt">0.00</td>
									<td class="cus_nwt">0.00</td>
									<td class="cus_dwt">0.00</td>
		                    		<td class="total_items">0</td>
		                    		
		                    	</tr>
		                    </tfoot>
		                 </table>
	                  </div>
					</div> 
				</div>	 
				<p class="help-block"></p>
				<!--End of row-->
				
				
			</div>	     
			<div class="box-footer clearfix"> 
				<div class="row">
					<div class="col-xs-offset-5">
						<button id="add_to_transfer" type="button" class="btn btn-primary btn-flat"><i class="fa fa-plus"></i> Submit</button>
						<button type="button" class="btn btn-default btn-flat btn-cancel">Back</button>
					</div> <br/>
				</div>
			</div> 
            <div class="overlay" style="display:none">
			  <i class="fa fa-refresh fa-spin"></i>
			</div>
      </div>   

	             <!-- /form -->
     </section>
	</div> 
	
	
<div class="modal fade" id="otp_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
		<h4 class="modal-title" id="myModalLabel">Verify OTP and Update Status</h4>
	  </div>
      <div class="modal-body"> 
         	<div class="row" > 
         		<div class="col-md-12">
         			<h5>We have sent OTP to autorized mobile number. Kindly verify OTP to proceed further.</h5> 
		    	</div>
		    </div>
		    <p></p>
         	<div class="row otp_block"> 
		    	<div class="col-md-2">
		    		<div class='form-group'>
		                <label for="">OTP</label>
		            </div>
		    	</div> 
		    	<div class="col-md-5">
		    		<div class='form-group'>
			    		<div class='input-group'>
			                <input type="text" id="other_issue_otp" name="other_issue_otp" placeholder="Enter 6 Digit OTP" maxlength="6" class="form-control" required /> 
			                <span class="input-group-btn">
				            	<button type="button" id="verify_other_issue_otp" class="btn btn-primary btn-flat" disabled >Verify</button>
				            </span>
			            </div> 
		            </div>
		    	</div> 
		    	<div class="col-md-2">
		    		<div class='form-group'>
		               <input type="button" id="resend_other_issue_otp" class="btn btn-warning btn-flat" value="Resend OTP"/>  
		            </div>
		    	</div>     
			 </div> 
			 <div class="row">
			 	<div class="col-md-12">
			 		<span class="otp_alert"></span>
			 	</div>
			 </div>  
	</div>  
	<div class="modal-footer">
		<button type="button" id="approve_other_issue" class="btn btn-success btn-flat" disabled>Approve</button>	 
		<button type="button"  class="btn btn-danger btn-flat" data-dismiss="modal" id="close">Close</button>
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

						 <textarea  cols="70"  id="description" name="description" ></textarea>

				  </div>

			  </div>

		  </div> 

	  </div>			

	  <div class="modal-footer">

		  <a href="#" class="btn btn-success add_order_desc">Add</a>

		  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

	  </div>

  </div>

</div>

</div>
  