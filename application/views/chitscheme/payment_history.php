<link href="<?php echo base_url() ?>assets/css/pages/payment.css" rel="stylesheet">
<?php 
$cmp = $this->login_model->get_settings();
?>
<div class="main-container">
	<div class="main-container">
		<!-- main -->		  
		<div class="main" >
		  <!-- main-inner --> 
			<div class="main-inner">
				 <!-- container --> 
				<div class="container">
				  <!-- alert -->
					<div class="row">
						<div id="payHistory" class="col-md-12">
							<div align="center"><legend class="head">PAYMENT HISTORY</legend></div>
							<!--<div align="center"><legend><span class="head">PAYMENT HISTORY</span></legend></div>-->
							<?php
							if($this->session->flashdata('successMsg')) { ?>
							<div class="alert alert-success" align="center">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<strong><?php echo $this->session->flashdata('successMsg'); ?> </strong>
							</div>      
							<?php } if($this->session->flashdata('errMsg')) { ?>							 
							<div class="alert alert-danger" align="center">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<strong><?php echo $this->session->flashdata('errMsg'); ?></strong>
							</div>
							<?php }?>
							<?php /*?><div  class="selectGroup">
								<div class="inner-group">
									<div class="inner-group">Scheme No</div>
									<div class="inner-group">
										<select>
											<option></option>
										</select>
									</div>
								</div>
								<div class="inner-group">
									<div class="inner-group">Payment Status</div>
									<div class="inner-group">
										<select>
											<option value="3">All</option>
											<option value="1">Approved</option>
											<option value="0">Pending</option>
											<option value="-1">Failed</option>
											<option value="2">Rejected</option>
										</select>
									</div>
								</div>
							</div><?php */?>
						<!--<?php if($cmp['is_multi_commodity']=='1'){?>
							<div class="col-md-2 col-sm-2 col-xs-2"><label>Filter By Metal</label></div>	
							<div class="col-md-2 col-sm-2 col-xs-3">	
									<select id="metal_select" class="form-control" required></select>
									<input id="id_metal" name="id_metal" type="hidden" value=""/>						
									<input id="is_multi_commodity" name="is_multi_commodity" type="hidden" value="<?php echo $cmp['is_multi_commodity']; ?>"/>			
							</div>
							<?php } ?>-->
								<?php 
								if(isset($payHistory[0]['id_payment']))
								{ 
								 if($is_branchwise_cus_reg == 0 && $branch_settings == 1){		
									echo "<div class='row'><div class='col-md-12'>";				
									$idx = 0;
									foreach ($branches as $branch){
									echo "<button type='button' class='".($idx == 0 ? 'theme-btn-bg':'')." brn_btn btn btn-sm' id='brn_btn".$branch['id_branch']."' value='".$branch['id_branch']."'>".$branch['name']."</button>";
									$idx++;
									}
									echo "</div></div><p class='help-block'></p>";
									}
								?>
							<div class="schemesList">
								<?php 
								$i = 0;
								foreach($payHistory as $key => $value)
								{ 
								$status = ($value['payment_status'] == 'paid' ? '<span class="label label-success">Paid</span>':($value['payment_status'] == 'pending' ? '<span class="label label-warning">Pending</span> <i rel="tooltip"  title="Your payment not yet realised, status will be updated after credited from bank." class="icon-question-sign help-icon"></i>':($value['payment_status'] == 'Failed' ? '<span class="label label-danger">Failed</span>':($value['payment_status'] == 'Rejected' ? '<span class="label label-danger">Rejected</span> <i rel="tooltip"  title="May be your payment realisation got failed. Please contact administrator for details" class="icon-question-sign help-icon"></i>':'')))); 
								if($i == 0){
								?>
								<div class="table-responsive">
									<table id="historyTables" class="table table-bordered table-striped table-responsive display">
									<thead>SAQQ	21
										<tr>
											<th width="5%">Payment No</th>
											<?php 	if($this->session->userdata('branch_settings')==1){?>
											<th width="10%">Branch</th>
											 <?php } ?>
											<!--<th width="5%">Due</th>-->
											<!--<th>Receipt No</th>-->
											<th width="10%">Scheme Code</th>
											<th width="10%">A/c No</th>
											<!--<th>Scheme Name</th>-->
											<th width="7%">Metal Weight</th>
											<th width="10%">Payment Amount (<?php echo $value['currency_symbol']?>)</th>
											<th width="7%">Additional Charge* (<?php echo $value['currency_symbol']?>)</th>
											<th width="7%">GST (<?php echo $value['currency_symbol']?>)</th>
											<th width="10%">Paid Amount (<?php echo $value['currency_symbol']?>)</th>
											<th width="5%">Payment Mode</th>
											<th width="10%">Paid Date</th>
											<th width="7%">Status</th>
											<th width="7%">Receipt No</th></th>
											<th width="7%">Action</th>
										</tr>
									</thead>
									<tbody>
									<?php $i++; } ?>
										<tr class="pay_card pay_ac_<?php echo $value['id_branch']?>" style="display:<?php echo $is_branchwise_cus_reg == 0 && $branch_settings == 1 ? ($branches[0]['id_branch'] == $value['id_branch'] ?'revert':'none'):'revert'?> ">
											<td><?php echo $value['id_payment']; ?><span style="display:none"><?php echo $value['due_type']; ?></span></td>
											<?php 	if($this->session->userdata('branch_settings')==1){?>
											<td><?php echo $value['branch_name'];?></td>
											<?php } ?>
											<td><?php echo $value['code']; ?>
											<td><?php 

											if($this->config->item('showGCodeInAcNo')==0)
											{
												echo ($value['scheme_acc_number']== '' ? $this->config->item('default_acno_label'): $value['scheme_acc_number']);
											}
											else
											{
												echo ($value['has_lucky_draw']==1 ? $value['scheme_group_code'] : $value['code']).' '.($value['scheme_acc_number']!=''?$value['scheme_acc_number']:"Not Allocated");
											}
											?>
											</td>	
											<!--<td><?php echo $value['scheme_name']; ?></td>-->
											<td><?php echo $value['scheme_type'] == "Weight Scheme" ? $value['metal_weight'].' g' : "-"; ?></td>
											<td style="text-align:right"><?php echo $value['payment_amount']; ?><br /> <?php echo ($value['gst'] > 0 ? ($value['gst_type'] == 0?"<span style='color:#7ea0bd;'>Inclusive of GST</span>":""):"")?></td>
											<td><?php echo $value['add_charges'] ?></td>
											<td>
											<?php 
											$gst_calc = 0;
											$gst_amt = 0;
											if($value['gst'] > 0){
												if($value['gst_type'] == 1 ){
													$gst_calc = $value['payment_amount']*($value['gst']/100);
													echo number_format($gst_calc,"2",".","");
													$gst_amt = $gst_calc;
												}
												else{
													$gst_calc = $value['payment_amount']-($value['payment_amount']*(100/(100+$value['gst'])));
													echo number_format($gst_calc,"2",".","");
												}
											}
											else{
												echo '-';
											}
											 ?>
											</td>
											<td><?php echo number_format((number_format($value['add_charges'],"2",".","")+number_format($value['payment_amount'],"2",".","")+number_format($gst_amt,"2",".","")),"2",".",""); ?></td>
											<td><?php echo ($value['payment_mode']!= '' ? ($value['payment_mode']== 'FP' ? 'Free' : $value['payment_mode']) : '-'); ?></td>
											<td><?php echo $value['date_payment'] ?></td>
											<td><?php echo $value['payment_status'] ?></td>
											<td><?php echo ($value['receipt_no']!= '' ? $value['receipt_no'] : '-'); ?></td>
											<td><?php if($value['id_pay_status'] == 1){ ?> 
											<a target="_blank" href="<?php echo base_url() ?>index.php/paymt/generateInvoice/<?php echo $value['id_payment'] ?>" >Print</a> 
											<?php } else { ?>
											-
											<?php } ?>
											</td>
										</tr>
								   <?php } ?>
								   </tbody>
									<tfoot>
									  <th colspan="14">
										 <p class="text-align:left;">CC - Credit Card, DB - Debit Card, NB - Net Banking, CSH - CASH, CHQ - Cheque, FP - Free Payment.</p>
									  </th>
									</tfoot>
									</table>
								</div>
						   </div>
							<?php } else { ?>							 
							<div class="alert alert-danger" align="center">
								<strong>You have not made any payment.</strong>
							</div>
							<?php } ?>
							<div class="row" style="display: none" >
								<div class="schemesList">
									<table id="historyTable" class="table table-bordered table-striped table-responsive display">
										<thead>
											<tr>
												<th width="5%">Payment No</th>
												<?php 	if($this->session->userdata('branch_settings')==1){?>
												<th width="10%">Branch</th>
												 <?php } ?>
												<!--<th width="5%">Due</th>-->
												<!--<th>Receipt No</th>-->
												<th width="10%">Scheme Code</th>
												<th width="10%">A/c No</th>
												<!--<th>Scheme Name</th>-->
												<th width="7%">Metal Weight</th>
												<th width="10%">Payment Amount (<?php echo $cmp['currency_symbol']?>)</th>
												<th width="7%">Additional Charge* (<?php echo $cmp['currency_symbol']?>)</th>
												<th width="7%">GST (<?php echo $cmp['currency_symbol']?>)</th>
												<th width="10%">Paid Amount (<?php echo $cmp['currency_symbol']?>)</th>
												<th width="5%">Payment Mode</th>
												<th width="10%">Paid Date</th>
												<th width="7%">Status</th>
												<th width="7%">Receipt No</th></th>
												<th width="7%">Action</th>
											</tr>
										</thead>
										<tfoot>
											<th colspan="14">
												<p class="text-align:left;">CC - Credit Card, DB - Debit Card, NB - Net Banking, CSH - CASH, CHQ - Cheque, FP - Free Payment.</p>
											</th>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
					</div>		
					<!-- /alert -->  
				</div>
				<!-- /container --> 
			</div>
		  <!-- /main-inner --> 
		</div>
	</div>
</div>
<!-- /main -->		  
<br />
<br />
<br />
