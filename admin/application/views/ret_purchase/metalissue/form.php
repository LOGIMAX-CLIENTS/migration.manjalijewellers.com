<!-- Content Wrapper. Contains page content -->
<style>
	.remove-btn{
	margin-top: -168px;
		margin-left: -38px;
		background-color: #e51712 !important;
		border: none;
		color: white !important;
	}
	.summary_lbl{
	font-weight:bold;
	}
	/* .repair_col{
	display:none;
	} */
	.stickyBlk {
		margin: 0 auto;
		top: 0;
		width: 100%;
		z-index: 999;
		background: #fff;
	}
	.custom-label{
	font-weight: 600 !important;
		letter-spacing: 0.5px !important;
		text-transform: uppercase !important;
	}

	.form-group {
		margin-bottom: 1px;
	}



	*[tabindex]:focus {
	outline: 1px black solid;
	}

	.billType{
		padding : 3px !important;
		margin : 0px !important;
		height: auto;
	}
	.custom-width{
		width: 11.6%;
	}
	.box{
		margin-bottom: 0 !important;
	}
	.content{
		padding: 0 15px;
	}

</style>
<div class="content-wrapper">
	<section class="content product">
		<!-- Default box -->
		<h4 class="box-title">SMITH METAL ISSUE FORM</h4>
		<div class="box box-primary">
			<div class="box-body">
				<?php
				if($this->session->flashdata('chit_alert'))
				{
					$message = $this->session->flashdata('chit_alert');
				?>
					<div  class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
						<?php echo $message['message']; ?>
					</div>
				<?php 
				}
				?>
				<!-- form container -->
				<div class="row">
			 	<!-- form -->
					<form id="metal_issue_form">
						
						<?php if($this->session->userdata('branch_settings')==1)
						{
						?>

							<?php if($this->session->userdata('id_branch')=='')
							{
							?>
								<div class="col-md-2 custom-width">
									<div class="form-group">
										<label>Select Branch <span class="error">*</span></label>
										<select class="form-control branch" id="select_branch"
										style="width:100%;">
										</select>
										<input type="hidden" id="branch" value="">
									</div>
								</div>
							<?php 
							}
							else
							{
							?>
								<input type="hidden" id="select_branch" value="<?php echo $this->session->userdata('id_branch');?>">
								<input type="hidden" id="branch" value="<?php echo $this->session->userdata('id_branch');?>">
								<input type="hidden" id="head_office" value=" ">
								<?php 
							}?>
						<?php 
						}
						?>
						<div class="col-md-2 custom-width">
							<div class="form-group" >
								<label>Karigar<span class="error">*</span></label>
								<select class="form-control" id="select_karigar" name="issue[id_karigar]" style="width:100%;"></select>
							</div>
						</div>
						<div class="col-md-2 custom-width">
							<div class="form-group" >
								<label>Metal<span class="error">*</span></label>
								<select class="form-control" id="select_metal" name="issue[id_karigar]" style="width:100%;"></select>
							</div>
						</div>
						<?php
						if($is_supplierbill_entry_req==1)
						{
						?>
							<div class="col-md-2">
								<div class="form-group" >
									<label>Metal Issue Type<span class="error">*</span></label><br>
									<input type="radio" id="metal_issue_type_normal" name="issue[metal_issue_type]" value="1" ><label for="metal_issue_type_normal">&nbsp;Normal issue</label>
									<input type="radio" id="metal_issue_type_po" name="issue[metal_issue_type]" value="2"  checked><label for="metal_issue_type_po">&nbsp;Appr Issue</label>
								</div>
							</div>
						<?php
						}
						?>
						<input type="hidden" id="head_office" value="">
						<div class="col-md-2 custom-width"> 
							<div class="form-group" >
								<label>Against Opening<span class="error">*</span></label><br>
								<input type="radio" id="is_against_opening_yes" name="issue[is_against_opening]" value="1" ><label for="is_against_opening_yes">&nbsp;Yes</label>
								<input type="radio" id="is_against_opening_no" name="issue[is_against_opening]" value="0" checked><label for="is_against_opening_no">&nbsp;No</label>
							</div>
						</div>

						<div class="col-md-2 custom-width">
							<div class="form-group" >
								<label>Aganist Order<span class="error">*</span></label><br>
								<input type="radio" id="aganist_order_yes" name="issue[issue_aganist]" value="1" ><label for="aganist_order_yes">&nbsp;Yes</label>
								<input type="radio" id="aganist_order_no" name="issue[issue_aganist]" value="0" checked><label for="aganist_order_no">&nbsp;No</label>
							</div>
						</div>
						<input type="hidden" id="issue_against_po_bills" name="issue[issue_against_po_bills]" value="<?php echo $is_against_po_bills;?>">
						<?php
						if($is_supplierbill_entry_req==1)
						{
						?>
							<div class="col-md-2 against_pur_order">
								<div class="form-group" >
									<label>Aganist Supplier Bill Entry<span class="error">*</span></label><br>
									<input type="radio" id="aganist_supplier_yes" name="issue[issue_against_po]" value="1" <?php echo ($is_supplierbill_entry_req==1 ? 'checked' :'')?> ><label for="aganist_supplier_yes">&nbsp;Yes</label>
									<input type="radio" id="aganist_supllier_no" name="issue[issue_against_po]" value="0" <?php echo ($is_supplierbill_entry_req==0 ? 'checked' :'')?> ><label for="aganist_supllier_no">&nbsp;No</label>
								</div>
							</div>
						<?php 
						}
						?>
						<div class="col-md-12">
							<table>
								<thead>
									<tr>
										<th colspan="2">Outstanding</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Amount(Rs) </td>
										<td> : </td>
										<td><span class="availableamtbalance"></span></td>
									</tr>
									<tr>
										<td>Pure Wt(Grms) </td>
										<td> : </td>
										<td><span class="availablepurebalance"></span></td>
									</tr>
								</tbody>
							</table>
							<div class="tab-content">
								<div class="tab-pane active" id="pay_items">
										<div class="box box-default">
											<div class="row" style="width: 100%;display: flex;">
												<div style="width: 30%;">
													<br>
													<div class="col-md-6" >
														<div class="form-group" >
															<label>Select PO No<span class="error">*</span></label>
															<select class="form-control" id="select_po_no" name="issue[id_order]" style="width:100%;" disabled></select>
															<input type="hidden" id="is_supplierbill_entry_req" value="<?php echo $is_supplierbill_entry_req;?>">
															<input type="hidden" id="is_stock_repair_order" value="0">
														</div>
													</div>
													<?php
													if($is_supplierbill_entry_req==1)
													{?>

														<div class="col-md-6 issue_from" style="display:none;">
															<div class="form-group">
																<label>Issue From<span class="error">*</span></label></br>
																<input type="radio" class="issue_against_tag" id="issue_against" name="issue[issue_from]" value="1" checked><label for="issue_against">&nbsp;Tag</label>
																<input type="radio" class="issue_against_nontag" id="issue_against" name="issue[issue_from]" value="2"><label for="issue_against">&nbsp;Non Tag</label>
															</div>
														</div>

														<div class="col-md-6 mt_po_no">
															<div class="form-group">
																<label>Select Supplier's Po No<span class="error">*</span></label><br>
																<select class="form-control" id="pur_fin_year_select" style="width:100px;">
																	<?php
																	foreach ($financial_year as $fin_year) { ?>
																		<option value=<?php echo $fin_year['fin_year_code']; ?> <?php echo ($fin_year['fin_status'] == 1 ? 'selected' : '')  ?>><?php echo $fin_year['fin_year_name']; ?></option>
																	<?php }
																	?>
																</select>
																<select class="form-control" id="select_supplier_po_no" name="issue[po_id]" style="width:175px;"  multiple></select>
															</div>
														</div>
														<div class="col-md-6 tag_issue_from " style="display:none;">
															<div class="form-group">
																<label>Tag Issue From<span class="error">*</span></label></br>
																<input type="radio" id="tag_issue_from" name="issue[tag_issue_from]" value="1" checked><label for="issue_against">&nbsp;Available Stock</label>
																<input type="radio" id="tag_issue_from" name="issue[tag_issue_from]" value="2"><label for="issue_against">&nbsp;Sales Return</label>
																<input type="radio" id="tag_issue_from" name="issue[tag_issue_from]" value="3"><label for="issue_against">&nbsp;Partly Sales</label>
																<input type="radio" id="tag_issue_from" name="issue[tag_issue_from]" value="4"><label for="issue_against">&nbsp;H.O OtherIssue</label>
															</div>
														</div>
														<div class="col-md-6 nontag_issue_from " style="display:none;">
															<div class="form-group">
																<label>NonTag Issue From<span class="error">*</span></label><br>
																<input type="radio" id="nontag_issue_from" name="issue[nontag_issue_from]" value="1" checked><label for="issue_against">&nbsp;Available Stock</label>
																<input type="radio" id="nontag_issue_from" name="issue[nontag_issue_from]" value="2"><label for="issue_against">&nbsp;NonTag Sales Return</label>
																<input type="radio" id="nontag_issue_from" name="issue[nontag_issue_from]" value="3"><label for="issue_against">&nbsp;NonTag Other Issue</label>
															</div>
														</div>
														<div class="col-md-6 mt_bt_issue " style="display:none;">
															<label>BT Code</label>
															<div class="form-group">
																<input type="text" class="form-control" id="bt_number" placeholder="Enter BT Code">
															</div>
														</div>
														<div class="col-md-6 mt_tag_issue " style="display:none;">
															<div>
																<label>Tag Code</label>
																<input type="text" class="form-control" id="tag_code" placeholder="Enter Tag Code">
															</div>
														</div>
														<div class="col-md-6 mt_tag_issue " style="display:none;">
															<div>
																<label>Old Tag Code</label>
																<input type="text" class="form-control" id="old_tag_code" placeholder="Enter Tag Code">
															</div>
														</div>
														<div class="col-md-2 tag_search " style="display:none;">
															<label></label>
															<div class="form-group">
																<button type="button" id="tag_history_search" class="btn btn-info" >Search</button>
															</div>
														</div>

														<div class="col-md-6 against_opening" style="display:none;"> 
															<div class="form-group" >
																<label>Search Karigar<span class="error">*</span></label> 
																<select class="form-control" id="karigar_select" style="width:100%;" ></select>
															</div>
														</div>

														<div class="col-md-6 against_opening" style="display:none;"> 
															<label></label>
															<div class="form-group">
																<button type="button" id="search_kar_opening_det" class="btn btn-info">Search</button>   
															</div>
														</div>
													<?php 
													}
													?>
													<div class="col-md-12">
														<label>Remark</label>
														<textarea class="form-control" id="remark" name="issue[remark]" rows="4" cols="500" style="width: 80%;"> </textarea>
													</div>
													<div class="col-sm-12" style="margin-top:10px;" align="center">
														<?php if($this->uri->segment(3) != 'edit'){?>
														<button type="button" id="submit_metal_issue" class="btn btn-primary" >Save</button>
														<?php }?>
														<button type="button" class="btn btn-default btn-cancel">Cancel</button>
													</div>
												</div>
												<div style="width: 70%; padding: 10px 10px 0px 10px;">
												<!-- Search Block	 -->
													<div class="row sale_details">
														<div>
															<?php if($is_against_po_bills==1) { ?>
															<div style='margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;'>
																<div style='display:flex;gap:10px; align-items: center; margin-bottom: 10px;'>
																	<div class="col-md-2">
																		<select name="search_po" id="search_po" class="form-control" placeholder="PO number (e.g. PO-001)" style="border-radius: 5px !important;width: 170px;"></select>
																	</div>
																	<div class="col-md-12" id="po_search_result" style="max-height: 250px; overflow-y: auto;">
																		
																	</div>
																</div>
															<?php } ?>
															</div>
															<center><p class="text-light-blue">Metal Issue Details</p></center>
															<div class="table-responsive" style="height:245px;">
																<table id="metal_details" class="table table-bordered table-striped text-center" style="text-transform:uppercase;">
																	<thead>
																		<tr>
																			<th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>
																			<th>Date</th>
																			<th>Karigar</th>
																			<th>Category</th>
																			<th>Section</th>
																			<th>Product</th>
																			<th>Purity</th>
																			<th>Touch</th>
																			<th>Pcs</th>
																			<th>Weight</th>
																			<th class="repair_col" style="display:none;">Net Wt</th>
																			<th class="repair_col" style="display:none;">Less Wt</th>
																			<th class="repair_col" style="display:none;">MC</th>
																			<th class="repair_col" style="display:none;" >MC TYPE</th>
																			<th class="repair_col" style="display:none;" >V.A(%)</th>
																			<th class="repair_col" style="display:none;" >V.A WGT</th>
																			<th class="repair_col" style="display:none;" >TOUCH</th>
																			<th class="repair_col" style="display:none;">CALC TYPE</th>
																			<th>Pure Wt</th>
																			<th>Action</th>
																		</tr>
																	</thead>
																	<tbody>

																	</tbody>
																	<tfoot>
																		<tr style="font-weight:bold;">
																			<td>TOTAL</td>
																			<td></td>
																			<td></td>
																			<td></td>
																			<td></td>
																			<td></td>
																			<td></td>
																			<td></td>
																			<td class="issue_tot_pcs" style="text-align:right;"></td>
																			<td class="issue_tot_wt" style="text-align:right;"></td>
																			<td></td>
																			<td></td>
																			<td class="repair_col" style="display:none;" ></td>
																			<td class="repair_col" style="display:none;" ></td>
																			<td class="repair_col" style="display:none;" ></td>
																			<td class="repair_col" style="display:none;" ></td>
																			<td class="repair_col" style="display:none;" ></td>
																			<td class="repair_col" style="display:none;" ></td>
																			<td class="repair_col" style="display:none;" ></td>
																			<td class="repair_col" style="display:none;" ></td>
																		</tr>
																	</tfoot>
																</table>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="box-body">
								<!-- Search Block	 -->
									<div class="row sale_details" style="display:none;">
										<div class="col-md-12">
											<p class="text-light-blue">Purchase Details</p>
											<div class="table-responsive">
												<table id="item_details" class="table table-bordered table-striped text-center" style="text-transform:uppercase;">
													<thead>
														<tr>
															<th>Category</th>
															<th>Product</th>
															<th>Design</th>
															<th>Sub Design</th>
															<th>Pcs</th>
															<th>G.Wt</th>
															<th>L.Wt</th>
															<th>N.Wt</th>
															<th>Wast(%)</th>
															<th>MC</th>
															<th>PURE WT</th>
														</tr>
													</thead>
													<tbody>

													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>					
						</div>
					</form>
				</div>
			</div>	<!--/ Col -->
		</div>	 <!--/ row -->
	</section>
</div>
<p class="help-block"> </p>
<?php echo form_close();?>
<div class="overlay" style="display:none">
	<i class="fa fa-refresh fa-spin"></i>
</div>	<!-- /form -->
<div class="modal fade" id="netbanking-confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Net Banking Details</h4>
			</div>
			<div class="modal-body">
				<div class="box-body chit_details">
					<div class="row">
						<div class="col-sm-12 pull-right">
							<button type="button" id="create_net_banking_row" class="btn bg-olive btn-sm pull-right"><i class="fa fa-plus"></i> Add</button>
						</div>
					</div>
					<div class="row">
						<div class="box-body">
							<div class="table-responsive">
								<table id="net_banking_details" class="table table-bordered text-center">
									<thead>
										<tr>
											<th>Amount</th>
											<th>Type</th>
											<th>Ref NO</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><input type="number" class="form-control pay_amount" type="number"/></td>
											<td>
												<select class="form-control nb_type">
													<option value="RTGS">RTGS</option>
													<option value="NEFT">NEFT</option>
												</select>
											</td>
											<td><input type="number" class="form-control ref_no" type="text"/></td>
											<td><a href="#" onclick="remove_net_banking_row($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<th colspan=2>Total</th>
											<th colspan=2><span class="total_amount"></span></th>
										</tr>
									</tfoot>
	 							</table>
	  						</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" >
				<a id="save_net_banking" class="btn btn-success">Save</a>
				<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
			</div>
		</div>	
	</div>
</div>

<div class="modal fade" id="sales_adjustment_add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Sales Details</h4>
			</div>
			<div class="modal-body">
				<div class="box-body chit_details">
					<div class="row">
						<div class="col-sm-12 pull-right">
							<button type="button" id="create_sales_details_row" class="btn bg-olive btn-sm pull-right"><i class="fa fa-plus"></i> Add</button>
						</div>
					</div>
					<div class="row">
						<div class="box-body">
							<div class="table-responsive">
								<table id="bill_details" class="table table-bordered text-center">
									<thead>
									<tr>
										<th>Bill No</th>
										<th>Amount</th>
										<th>Action</th>
									</tr>
									</thead>
									<tbody>
										<tr>
											<td><input type="text" class="form-control bill_no"/><input type="hidden" class="form-control bill_id"/></td>
											<td><input type="number" class="form-control payment_amount" type="text" readonly/></td>
											<td><a href="#" onclick="remove_net_banking_row($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<th colspan=2>Total</th>
											<th colspan=2><span class="total_amount"></span></th>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" >
				<a id="save_sales_details" class="btn btn-success">Save</a>
				<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="cus_stoneModal" tabindex="-1"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:90%;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Stone Details</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col pull-right">
						<button type="button" id="create_stone_item_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
					</div>
				</div>
				<div class="row">
					<input type="hidden" id="custom_active_id" value="0">
						<table id="estimation_stone_cus_item_details" class="table table-bordered table-striped text-center">
						<thead>
							<tr>
								<th width="5%">LWT</th>
								<th width="10%">Type</th>
								<th width="15%">Name</th>
								<th width="13%">Code</th>
								<th width="10%">Pcs</th>   
								<th width="20%">Wt</th>
								<th width="10%">Cal.Type</th>
								<th width="10%">Cut</th>
								<th width="10%">Color</th>
								<th width="10%">Clarity</th>
								<th width="10%">Shape</th>
								<th width="15%">Rate</th>
								<th width="15%">Amount</th>
								<th width="5%">Action</th>
							</tr>
						</thead> 
						<tbody></tbody>	
						<tfoot>
							<tr style="font-weight:bold;font-size:15px">
								<td>Total:</td>
								<td></td>
								<td style="display:none" class="metalissse_product"></td>
								<td></td>
								<td></td>
								<td class="stn_tot_pcs"></td>
								<td class="stn_tot_weight"></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td class="stn_tot_amount"></td>
								<td></td>
							</tr>
						</tfoot>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" id="update_return_stn_details" class="btn btn-success">Save</button>
					<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</div>