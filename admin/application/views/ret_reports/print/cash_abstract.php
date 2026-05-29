<html><head>
		<meta charset="utf-8">
		<title>Payment Report</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/cash_abstract.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
		<style >
		 .head
		 {
			 color: black;
			 font-size: 50px;
		 }
		 span { display: inline-block; }
         </style>
</head><body>
<span class="PDFReceipt">
            <div>
                <img alt="" src="<?php echo base_url();?>assets/img/receipt_logo.png">
            </div>
            <div class="address" align="right">
				 <?php echo (!empty($comp_details['address1']) ? $comp_details['address1'].' ,':''); ?>				
				 <?php echo (!empty($comp_details['address2']) ? $comp_details['address2'].' ,':''); ?>				
				<?php echo $comp_details['city'].'  '.$comp_details['pincode'].' ,'; ?>
				<?php echo $comp_details['state'].' ,'; ?>
				<?php echo $comp_details['country'].' .'; ?>
			</div>
			<div class="heading"><h2>Cash Abstract</h2></div>
<div  class="content-wrapper">
 <div class="box">
  <div class="box-body">
 			<div  class="container-fluid">
				<div id="printable">
					<?php $partial_gwt=0;$partial_nwt=0;?>
					<?php if(sizeof($billing['item_details'])>0){?>
						<div  class="row">
							<div class="table_content">
									<span>Sale Item Details</span>
								</div>
							<div class="col-xs-12">
								<div class="table-responsive">
									<table id="sales_list" class="table table-bordered table-striped text-center">
									<thead>
									<tr>
									<th width="15%">Category Name</th>                          
									<th width="15%">Product</th>                                
									<th width="10%">Pcs</th>                                
									<th width="10%">Net Wt</th>
									<th width="15%">Amount</th>
									<th width="10%">Discount</th>
									<th width="10%">Sales Rate</th>
									<th width="10%">Avg Rate</th>
									<th width="10%">Rate Dif</th>
									</tr>
									</thead>
									<!--<tbody>-->
										<?php 
										$total_item_taxable=0;
										$id_ret_category='';
										$i=0;
										$item_piece=0;
										$no_of_piece=0;
										$bill_discount=0;
										$item_amount=0;
										$item_net_wt=0;
										$total_net_wt=0;
										$total_taxable_amt=0;
										$total_discount=0;
										$total_sale_tax=0;
										$total_due_amt=0;
										$total_credit_amt=0;
										if(sizeof($billing['due_details'])>0)
										{
											foreach($billing['due_details'] as $due)
											{
												$total_due_amt+=$due['due_amt'];
											}
										}
										if(sizeof($billing['credit_details'])>0)
										{
											foreach($billing['credit_details'] as $credit)
											{
												$total_credit_amt+=$credit['tot_amt_received'];
											}
										}
											foreach($billing['item_details'] as $items)
											{
												$id_caegory=(isset($billing['item_details'][$i+1]['id_ret_category']) ?$billing['item_details'][$i+1]['id_ret_category'] :'');
												$item_piece+=$items['piece'];
												$item_net_wt+=$items['net_wt'];
												$total_net_wt+=$items['net_wt'];
												$no_of_piece+=$items['piece'];
												$item_amount+=($items['item_cost']-$items['item_total_tax']);
												$total_taxable_amt+=$items['item_cost']-$items['item_total_tax'];
												$bill_discount+=$items['bill_discount'];
												$total_discount+=$items['bill_discount'];
												$total_sale_tax+=$items['item_total_tax'];
												if($items['has_fixed_price']==0)
												{
													$avg_rate=number_format((float)(($items['item_cost']-$items['item_total_tax'])/$items['net_wt']),2,'.','');
												}else
												{
													$avg_rate=number_format((float)(($items['item_cost']-$items['item_total_tax'])/$items['piece']),2,'.','');
												}

												if($items['is_partial_sale']==1)
												{
													$partial_gwt=$items['gross_wt']/2;
													$partial_nwt=$items['net_wt']/2;
												}

												?>
													<tr>
													<td><?php echo $items['category_name'];?></td>
													<td><?php echo $items['product_name'];?></td>
													<td><?php echo $items['piece'];?></td>
													<td><?php echo $items['net_wt'];?></td>
													<td><?php echo ($items['item_cost']-$items['item_total_tax']);?></td>
													<td><?php echo $items['bill_discount'];?></td>
													<td><?php echo $items['rate_per_grm'];?></td>
													<td><?php echo $avg_rate;?></td>
													<td><?php echo number_format((float)($avg_rate-$items['rate_per_grm']),2,'.','');?></td>
													</tr>
												<?php 
											$i++;
											if($id_caegory!=$items['id_ret_category'])
											{?>
												<tr class="group" style="background-color: #ccc;font-weight: bold;">
													<td></td>
													<td>Sub Total</td>
													<td><?php echo $item_piece;?></td>
													<td><?php echo $item_net_wt;?></td>
													<td><?php echo $item_amount;?></td>
													<td><?php echo $bill_discount;?></td>
													<td></td>
													<td></td>
													<td></td>
												</tr>
											<?php 
											$item_piece=0;$item_net_wt=0;$item_amount=0;$bill_discount=0;
											}
										}
										?>
									<!--</tbody>-->
									<tfoot>
										<tr style="background-color: #ccc;font-weight: bold;">
											<td></td>
											<td>Total</td>
											<td><?php echo $no_of_piece;?></td>
											<td><?php echo number_format((float)$total_net_wt,3,'.','');?></td>
											<td><?php echo number_format((float)$total_taxable_amt,2,'.','');?></td>
											<td><?php echo number_format((float)$total_discount,2,'.','');?></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
									</tfoot>
									</table>
									 <br>	
									
									
								</div>	
							 </div>	
						</div><br>
						<?php }?>

						<?php if(sizeof($billing['old_matel_details'])>0){?>
						<div  class="row">
								<div class="table_content">
									<span>Purchase Item Details</span>
								</div>
							<div class="col-xs-12">
								<div class="table-responsive">
								<table id="purchase_list" class="table table-bordered table-striped text-center">
						                    <thead>
											  <tr>
											    <th width="15%">Metal</th>
											    <th width="15%">Gross Wt</th>
											    <th width="10%">Net Wt</th>
						                        <th width="10%">Amount</th>
						                        <th width="10%">Avg Rate</th>
											  </tr>
						                    </thead>
						                    <!--<tbody>-->
						                    	<?php 
										$total_item_taxable=0;
										$id_ret_category='';
										$i=0;
										$item_amount=0;
										$item_gross_wt=0;
										$item_net_wt=0;
										$total_pur_gross_wt=0;
										$total_pur_net_wt=0;
										$total_purchase_amount=0;
											foreach($billing['old_matel_details'] as $items)
											{
												$id_caegory=(isset($billing['old_matel_details'][$i+1]['id_metal']) ?$billing['old_matel_details'][$i+1]['id_metal'] :'');
												$item_gross_wt+=$items['gross_wt'];;
												$total_pur_gross_wt+=$items['gross_wt'];
												$item_amount+=$items['amount'];
												$total_purchase_amount+=$items['amount'];
												$item_net_wt+=$items['net_wt'];
												$total_pur_net_wt+=$items['net_wt'];
												$avg_rate=number_format((float)(($item_amount)/$items['net_wt']),2,'.','');
												?>
													<tr>
													<td><?php echo $items['metal'];?></td>
													<td><?php echo $items['gross_wt'];?></td>
													<td><?php echo $items['net_wt'];?></td>
													<td><?php echo $items['amount'];?></td>
													<td><?php echo $avg_rate;?></td>
													</tr>
												<?php 
											$i++;
											if($id_caegory!=$items['id_metal'])
											{?>
												<tr style="background-color: #ccc;font-weight: bold;">
													<td>Sub Total</td>
													<td><?php echo number_format($item_gross_wt,3,'.','');?></td>
													<td><?php echo number_format($item_net_wt,3,'.','');?></td>
													<td><?php echo number_format($item_amount,2,'.','');?></td>
													<td></td>
												</tr>
											<?php 
											$item_gross_wt=0;$item_net_wt=0;$item_amount=0;
											}
										}
										?>
						                    <!--</tbody>-->
						                    <tfoot>
						                    	<tr style="background-color: #ccc;font-weight: bold;">
						                    		<td>Total</td>
													<td><?php echo number_format($total_pur_gross_wt,3,'.','');?></td>
													<td><?php echo number_format($total_pur_net_wt,3,'.','');?></td>
													<td><?php echo number_format($total_purchase_amount,2,'.','');?></td>
													<td></td>
						                    		
						                    	</tr>
						                    </tfoot>
						               </table>
						               <br>	
								</div>	
							 </div>	
						</div><br>
						<?php }?>
					
						<?php if(sizeof($billing['return_details'])>0 && $billing['return_details'][0]['item_cost']>0){?>
							<div  class="row">
							<div class="col-xs-12">
									<div class="table_content">
										<span>Return Item Details</span>
									</div>
								<div class="table-responsive">
								<table id="return_item" class="table table-bordered table-striped text-center">
						                    <thead>
											  <tr>
											    <th width="15%">Category</th>
											    <th width="15%">Product</th>
											    <th width="15%">Gross Wt</th> 
											    <th width="10%">Net Wt</th>
						                        <th width="10%">Amount</th>
						                        <th width="10%">Avg Rate</th>
						                        <th width="10%">Discount</th>
											  </tr>
						                    </thead>
						                    <!--<tbody>-->
										<?php 
										$id_ret_category='';
										$i=0;
										$item_piece=0;
										$ret_no_of_piece=0;
										$bill_discount=0;
										$item_amount=0;
										$item_net_wt=0;
										$total_ret_net_wt=0;
										$total_ret_amt=0;
										$total_discount=0;
										$ret_total_discount=0;
										$ret_total_tax=0;
											foreach($billing['return_details'] as $items)
											{
												$id_caegory=(isset($billing['return_details'][$i+1]['id_ret_category']) ?$billing['return_details'][$i+1]['id_ret_category'] :'');
												$item_piece+=$items['piece'];
												$item_net_wt+=$items['net_wt'];
												$total_ret_net_wt+=$items['net_wt'];
												$ret_no_of_piece+=$items['piece'];
												$ret_total_tax+=$items['item_total_tax'];
												$item_amount+=($items['item_cost']-$items['item_total_tax']);
												$total_ret_amt+=$items['item_cost']-$items['item_total_tax'];
												$bill_discount+=$items['bill_discount'];
												$ret_total_discount+=$items['bill_discount'];
												$avg_rate=number_format((float)(($items['item_cost']-$items['item_total_tax'])/$items['net_wt']),2,'.','');
												?>
													<tr>
													<td><?php echo $items['category_name'];?></td>
													<td><?php echo $items['product_name'];?></td>
													<td><?php echo $items['piece'];?></td>
													<td><?php echo $items['net_wt'];?></td>
													<td><?php echo ($items['item_cost']-$items['item_total_tax']);?></td>
													
													<td><?php echo $avg_rate;?></td>
													<td><?php echo $items['bill_discount'];?></td>
													</tr>
												<?php 
											$i++;
											if($id_caegory!=$items['id_ret_category'])
											{?>
												<tr class="group" style="background-color: #ccc;font-weight: bold;">
													<td></td>
													<td>Sub Total</td>
													<td><?php echo $item_piece;?></td>
													<td><?php echo $item_net_wt;?></td>
													<td><?php echo $item_amount;?></td>
													<td></td>
													<td><?php echo $bill_discount;?></td>
												</tr>
											<?php 
											$item_piece=0;$item_net_wt=0;$item_amount=0;$bill_discount=0;
											}
										}
										?>
									<!--</tbody>-->
									<!--<tfoot>-->
										<tr class="group" style="background-color: #ccc;font-weight: bold;">
													<td>Total</td>
													<td></td>
													<td><?php echo $ret_no_of_piece;?></td>
													<td><?php echo $total_ret_net_wt;?></td>
													<td><?php echo $total_ret_amt;?></td>
													<td></td>
													<td><?php echo $ret_total_discount;?></td>
												</tr>
									<!--</tfoot>-->
						                 </table>
									
									<br>
								</div>	
							 </div>	
						</div><br>
						<?php }?>

			  	<div class="row">
			  		<?php
			  		 if($branch_transfer_details['gross_wt']>0){?>
				  	<div class="col-sm-6">
	                  <div class="box box-info branch_transfer_details">
								<div class="row">
									<div class="table_content">
										<span>Branch Transfer Details</span>
									</div>
									   <div class="table-responsive">
										  <table id="branch_transfer" class="table table-bordered table-striped text-center">
							                    <thead>
												  <tr>
												    <th width="15%">TotalPieces</th>                    
												    <th width="15%"> Total Gross Wt</th>                    
							                        <th width="10%">Total Net Wt</th>
												  </tr>
							                    </thead>
							                    <tbody>
							                    	<tr>
							                    		<td class="no_of_piece"></td>
							                    		<td class="gwt"><?php echo 
							                    		$branch_transfer_details['gross_wt'];?></td>
							                    		<td class="net_wt"><?php echo 
							                    		$branch_transfer_details['net_wt'];?></td>
							                    	</tr>
							                    </tbody>
							               </table>
									  </div>
								</div> 
						</div>
					</div>
					<?php }?>
					<?php if($partial_gwt>0){?>
					<div class="col-sm-6">
	                  <div class="box box-info partial_sale_details">
							
								<div class="row">
									<div class="table_content">
										<span>Partial sale Details</span>
									</div>
									   <div class="table-responsive">
										  <table id="partial_sale" class="table table-bordered table-striped text-center">
							                    <thead>
												  <tr>
												    <th width="15%"> Total Gross Wt</th>
												    <th width="10%">Total Net Wt</th>
												  </tr>
							                    </thead>
							                  <!--  <tbody>-->
							                    	<tr>
							                    		<td class="partial_gwt"><?php echo number_format($partial_gwt,3,'.','');?></td>
							                    		<td class="partial_nwt"><?php echo number_format($partial_nwt,3,'.','');?></td>
							                    	</tr>
							                    <!--</tbody>-->
							               </table>
									  </div>
								</div> 
						</div>
					</div>
					<?php }?>
				</div><p></p></br>

			  	<div class="row">
				  	<div class="col-sm-6">
	                  <div class="box box-info branch_transfer_details">
								<div class="row">
									<div class="table_content">
										<span>Total Summary Details</span>
									</div>
									   <div class="table-responsive">
										  	<table id="total_summary_details" class="table table-bordered table-striped">
												<thead>
												</thead> 
												<tbody>
													<tr>
														<td class="text-right">Total Sale Amount</td>
														<td><?php echo number_format((float)$total_taxable_amt,2,'.','');?></td>
													</tr>
													<tr>
														<td class="text-right">Total Sale Tax</td>
														<td><?php echo number_format((float)$total_sale_tax,2,'.','');?></td>
													</tr>
													<tr>
														<td class="text-right">Total Due Amount</td>
														<td><?php echo number_format((float)$total_due_amt,2,'.','');?></td>
													</tr>
													<tr>
														<td class="text-right">Total Purchase Amount</td>
														<td><?php echo number_format($total_purchase_amount,2,'.','');?></td>
													</tr>
													
													<tr>
														<td class="text-right">Total Sales Return Amount</td>
														<td><?php echo number_format($total_ret_amt,2,'.','');?></td>
													</tr>

													<tr>
														<td class="text-right">Total Sales Return Tax</td>
														<td><?php echo number_format($ret_total_tax,2,'.','');?></td>
													</tr>
													<tr>
														<td class="text-right">Credit Receipt</td>
														<td><?php echo number_format($total_credit_amt,2,'.','');?></td>
													</tr>

													<tr style="background-color: #ccc;font-weight: bold;">
														<td>Final Price</td>
														<td class="final_price">
															<?php echo number_format((($total_taxable_amt+$total_sale_tax+$total_credit_amt)-($ret_total_tax+$total_ret_amt)-$total_purchase_amount-$total_due_amt),2,'.','');?>
														</td>
													</tr>
												</tbody>
											 </table>
									  </div>
								</div> 
						</div>
					</div><p><p>
					<div class="col-sm-6">
	                  <div class="box box-info partial_sale_details">
							
								<div class="row">
									<div class="table_content">
										<span>Payment Details</span>
									</div>
									   <div class="table-responsive">
										   <table id="payment_modes" class="table table-bordered table-striped">
													<thead>
													</thead> 
													<!--<tbody>-->
											<?php
											$cash_payment=0;$card_payment=0;$cheque_payment=0;
											$online_payment=0;$cash_return=0;$chit_amount=0;$gift_voucher_amt=0;
												foreach($billing['payment_details'] as $pay)
												{
													
													if($pay['payment_mode']=='Cash')
													{
													$cash_payment+=$pay['payment_amount'];
													}
													if($pay['payment_mode']=='CC' || $pay['payment_mode']=='DC')
													{
													$card_payment+=$pay['payment_amount'];
													}
													if($pay['payment_mode']=='CHQ')
													{
													$cheque_payment+=$pay['payment_amount'];
													}
													if($pay['payment_mode']=='NB')
													{
													$online_payment+=$pay['payment_amount'];
													}
												}

												foreach($billing['chit_details'] as $chit)
												{
													$chit_amount+=$chit['closing_amount'];
												}

												foreach($billing['voucher_details'] as $gift)
												{
													$gift_voucher_amt+=$gift['gift_voucher_amt'];
												}

												foreach($billing['cash_return'] as $pay)
												{
													$cash_return+=$pay['amount'];
												}

												foreach($billing['advance_detals'] as $pay)
												{
													
													if($pay['payment_mode']=='Cash')
													{
													$cash_payment+=$pay['payment_amount'];
													}
													if($pay['payment_mode']=='CC' || $pay['payment_mode']=='DC')
													{
													$card_payment+=$pay['payment_amount'];
													}
													if($pay['payment_mode']=='CHQ')
													{
													$cheque_payment+=$pay['payment_amount'];
													}
													if($pay['payment_mode']=='NB')
													{
													$online_payment+=$pay['payment_amount'];
													}
												}

											?>


														<tr>
															<td class="text-right">Total Cash</td>
															<td class="total_cash">
																<?php echo number_format(($cash_payment-$cash_return),2,'.','');?></td>
														</tr>
														<tr>
															<td class="text-right">Total Card</td>
															<td class="total_card"><?php echo number_format($card_payment,2,'.','');?></td>
														</tr>
														<tr>
															<td class="text-right">Total Cheque</td>
															<td class="total_cheque"><?php echo number_format($cheque_payment,2,'.','');?></td>
														</tr>
														<tr>
															<td class="text-right">Net Banking</td>
															<td class="online_payment"><?php echo number_format($online_payment,2,'.','');?></td>
														</tr>
														<tr>
															<td class="text-right">Chit Utilization</td>
															<td class="chit_payment"><?php echo number_format($chit_amount,2,'.','')?></td>
														</tr>
														<tr>
															<td class="text-right">Gift Voucher</td>
															<td><?php echo number_format($gift_voucher_amt,2,'.','')?></td>
														</tr>
														<tr style="background-color: #ccc;font-weight: bold;">
															<td class="text-right">Total</td>
															<td class="total_amount">
																<?php echo number_format((($cash_payment-$cash_return)+$card_payment+$cheque_payment+$online_payment+$chit_amount+$gift_voucher_amt),2,'.','');?>
															</td>
														</tr>
														
													<!--</tbody>-->
												 </table>
									  </div>
								</div> 
						</div>
					</div>
				</div><p></p>

						<div align="right">
							<div class="row">
								<label>Date : <?php echo date('d-m-Y');?></label>
							</div><br>
							<div class="row">
							<label>Time : <?php echo date('h:i A', strtotime(date('d-m-Y H:i:s')));?></label>
							</div>
						</div>
			</div>
 </div>
 </div><!-- /.box-body --> 
</div>
 </span>          
</body></html>