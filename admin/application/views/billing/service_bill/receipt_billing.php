<html><head>
		<meta charset="utf-8">
		<title>Billing Receipt</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/billing_receipt.css">
		<style >
		 .head
		 {
			 color: black;
			 font-size: 30px;
		 }
		 .alignCenter {
			 text-align: center;
		 }
		 .alignRight {
			 text-align: right;
		 }
		 .table_heading {
			 font-weight: bold;
		 }
		 .textOverflowHidden {
			white-space: nowrap; 
			overflow: hidden;
			text-overflow: ellipsis;
		 }
		.duplicate_copy * {
			font-size: 9px;
		}
		.duplicate_copy #pp th, .duplicate_copy #pp td{
			font-size: 9px !important;
		}
		.return_dashed {
			width:700px !important;
		}
		.old_metal_dashed {
			width:700px !important;
		}

		.stones, .charges {
			font-style: italic;
		}
		.stones .stoneData, .charges .chargeData {
			font-size: 10px !important;
		}
		
        .addr_labels {
            display: inline-block;
            width: 30%;
			font-size: 12px !important;
        }
		
        .addr_values {
            display: inline-block;
            padding-left: -5px;
			font-size: 12px !important;
        }
		
		.rate_labels {
            display: inline-block;
            width: 30%;
			font-size: 12px !important;
        }
		
		.addr_brch_labels {
			display: inline-block;
			width: 30%;
			font-size: 12px !important;
		}

		.addr_brch_values {
			display: inline-block;
			padding-left: 2px;
			font-size: 12px !important;
		}

		.invoice {
			font-size: 0; 
		}
		
		.invoice > div {
			font-size: 15px; 
			vertical-align: top; 
		}
		
		.customer_info {
			font-size: 0; 
		}
		
		.customer_info > div {
			font-size: 15px; 
			vertical-align: top; 
		}

        </style>
</head>
<body>
<span class="PDFReceipt" >
            <!--<div>
                <img alt="" src="<?php echo base_url();?>assets/img/receipt_logo.png">
            </div>-->
<?php 

$login_emp = $billing['emp_name'];

$esti_sales_emp = '';
$esti_purchase_emp = '';
$esti_return_emp = '';

$esti_sales_id = '';
$esti_purchase_id = '';
$esti_return_id = '';

function moneyFormatIndia($num) {
	return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
}

$gold_metal_rate = ($billing['goldrate_22ct']>0 ? $billing['goldrate_22ct']:$metal_rate['goldrate_22ct']);
$silver_metal_rate = ($billing['silverrate_1gm']>0 ?$billing['silverrate_1gm'] :$metal_rate['silverrate_1gm']);
$metal_type = 0;

$tot_sales_amt=0;
$sales_cost = 0;
foreach($est_other_item['item_details'] as $items) {
	$sales_cost += $items['item_cost'];
}
$tot_sales_amt  =  $sales_cost;

$total_return=0;
foreach($est_other_item['return_details'] as $items) {
	$total_return  += $items['item_cost'];
}

$pur_total_amt=0;
foreach($est_other_item['old_matel_details'] as $items) {
	$pur_total_amt += $items['amount'];
}
?>
    <div style="margin-top:23px !important;">
			<div class="hare_krishna"> </div>
			<div class="header_top">
			
			</div><br>
			<div class="customer_info" style="width:100%; text-transform:uppercase;">

				<div style="display: inline-block; width: 40%; padding-left:0px; vertical-align: top;">
                    <?php echo ($billing['bill_type']==11 ? 'BILL TO'.'<br><br>' :'')?> 						
					<label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;'.'Mr./Ms.'.$billing['customer_name']."</div>"; ?></label><br>
					<label><?php echo '<div class="addr_labels">Mobile</div><div class="addr_values">:&nbsp;&nbsp;'.$billing['mobile']."</div>"; ?></label><br>
					<label><?php echo ($billing['address1']!='' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($billing['address1']).','."</div><br>" :''); ?></label>
					<label><?php echo ($billing['address2']!='' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;'.strtoupper($billing['address2']).','."</div><br>" :''); ?></label>
					<label><?php echo ($billing['village_name']!='' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;'.strtoupper($billing['village_name']).','."</div><br>" :''); ?></label>
					<label><?php echo ($billing['city']!='' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($billing['city']).($billing['pincode']!='' ? ' - '.$billing['pincode'].'.' :'')."</div><br>" :''); ?></label>
					<label><?php echo ($billing['cus_state']!='' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($billing['cus_state'].'-'.$billing['state_code']).','."</div><br>" :''); ?></label>
					<label><?php echo ($billing['cus_country']!='' ? '<div class="addr_labels">Country</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($billing['cus_country'])."</div><br>" :''); ?></label>
					<label><?php echo (isset($billing['pan_no']) && $billing['pan_no']!='' ? '<div class="addr_labels">PAN</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($billing['pan_no'])."</div><br>" :''); ?></label>
					<label><?php echo (isset($billing['gst_number']) && $billing['gst_number']!='' ? '<div class="addr_labels">GST IN</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($billing['gst_number'])."</div><br>" :''); ?></label>
                </div>
				<div style="display: inline-block; width: 25%;"></div>
                <div style="display: inline-block; width: 40%; vertical-align: top;">
					<div style="text-align: left !important;width: 100%;"> 
    						<label><?php echo ($comp_details['name']!='' ? '<div class="addr_brch_labels">Branch</div><div class="addr_brch_values">:&nbsp;&nbsp;'.strtoupper($comp_details['name']).','."</div><br>" :''); ?></label>
    						<label><?php echo ($comp_details['address1']!='' ? '<div class="addr_brch_labels">Address</div><div class="addr_brch_values">:&nbsp;&nbsp;'.strtoupper($comp_details['address1']).','."</div><br>" :''); ?></label>
    						<label><?php echo ($comp_details['address2']!='' ? '<div class="addr_brch_labels"></div><div class="addr_brch_values">&nbsp;&nbsp;&nbsp;'.strtoupper($comp_details['address2']).','."</div><br>" :''); ?></label>
    						<label><?php echo ($comp_details['city']!='' ? '<div class="addr_brch_labels">city</div><div class="addr_brch_values">:&nbsp;&nbsp;'.strtoupper($comp_details['city']).($comp_details['pincode']!='' ? ' - '.$comp_details['pincode'].'.' :'')."</div>" :''); ?><br></label>
    						<label><?php echo ($comp_details['state']!='' ? '<div class="addr_brch_labels">State</div><div class="addr_brch_values">:&nbsp;&nbsp;'.strtoupper($comp_details['state'].($comp_details['state_code']!='' ? '-'.$comp_details['state_code']  :'')).'.'."</div><br>" :''); ?></label>
    						<label><?php echo '<div class="addr_brch_labels">Place of supply</div><div class="addr_brch_values">:&nbsp;&nbsp;'.strtoupper($comp_details['state'].($comp_details['state_code']!='' ? '-'.$comp_details['state_code']  :'')).'.'."</div><br>"; ?></label>
    						<label><div class="addr_brch_labels">Reverse Charges</div><div class="addr_brch_values">:&nbsp;&nbsp;NO</div><br></label>
					</div>
				</div>
			</div>
			
			<?php 
    		    if(sizeof($est_other_item['item_details'])>0) //SALES BILL 
    		    {
    		        $invoice_no = $billing['branch_code'].'-SA-'.$billing['metal_code'].'-'.$billing['sales_ref_no'];
    		    }else if(sizeof($est_other_item['old_matel_details'])>0) // OLD METAL ITEMS
    		    {
    		        $invoice_no =  $billing['branch_code'].'-PU-'.$billing['metal_code'].'-'.$billing['pur_ref_no'];
    		    }
    		    else if(sizeof($est_other_item['return_details'])>0) //SALES RETURN
    		    {
    		        $invoice_no =  $billing['branch_code'].'-SR-'.$billing['metal_code'].'-'.$billing['s_ret_refno'];
    		    }
    		    else if($billing['bill_type']==5) //ORDER ADVANCE
    		    {
    		        $invoice_no =  $billing['branch_code'].'-OD-'.$billing['order_adv_ref_no'];
    		    }
    		    else if($billing['bill_type']==8)   //CREDIT COLLECTION
    		    {
    		        $invoice_no =  $billing['branch_code'].'-CC-'.$billing['credit_coll_refno'];
    		    }
    		    else if($billing['bill_type']==10)   //CHIT PRE CLOSE
    		    {
    		        $invoice_no =  $billing['branch_code'].'-'.$billing['chit_preclose_refno'];
    		    }else
    		    {
    		        $invoice_no =  $billing['bill_no'];
    		    }
		    ?>
    					    
			<div class="invoice" style="width: 100%; text-transform:uppercase;margin-top:-50px;">
			    <div style="display: inline-block; width: 33%; padding-left:0px; vertical-align: top;">
    					<label><?php echo ($metal_rate['goldrate_18ct']!=0 ? '<div class="rate_labels" ">18 KT GOLD</div><div class="addr_values" ">:&nbsp;&nbsp;'.number_format($metal_rate['goldrate_18ct'],2,'.','').'/'.'Gm'."</div>" :''); ?></label><br>
    					<label><?php echo '<div class="rate_labels">22 KT GOLD</div><div class="addr_values">:&nbsp;&nbsp;'.number_format($metal_rate['goldrate_22ct'],2,'.','').'/'.'Gm'."</div>"; ?></label><br>
    					<label><?php echo '<div class="rate_labels">SILVER</div><div class="addr_values">:&nbsp;&nbsp;'.number_format($metal_rate['silverrate_1gm'],2,'.','').'/'.'Gm'."</div>"; ?></label><br>
    		    </div>
    		    <div style="display: inline-block; width: 33%; text-align: center; font-weight: bold; vertical-align: top;">
    		        <label>
    					<?php echo "TAX INVOICE "?>
    				</label>
    		    </div>
    		    <div style="display: inline-block; width: 33%;  vertical-align: top;">
					<div style="text-align: left !important;width: 100%;"> 
    						<label><?php echo '<div class="addr_brch_labels">Invoice Date</div><div class="addr_brch_values" font-weight: bold;">:&nbsp;&nbsp;'.$billing['bill_date']."</div><br>"; ?></label>
        						<label><?php echo '<div class="addr_brch_labels">Invoice No</div><div class="addr_brch_values">:&nbsp;&nbsp;'.$invoice_no."</div><br>"; ?>
        						</label>
    				</div>
    			</div>
			</div>
			<div  class="content-wrapper" style="margin-top:-30px;">
			<div class="box">
			<div class="box-body">
						<div  class="container-fluid">
							<div id="printable">
								<?php if(sizeof($item_details)>0){?>
							    
											
									<hr class="header_dashed">
										<div class="col-xs-12">
											<div class="table-responsive">
											<table id="pp" class="table text-center" >
													<thead style="text-transform:uppercase;font-size:10px;">
														<tr>
															<td class="table_heading" style="width: 7%">S.No</td>
															<td class="table_heading" style="width: 25%">Description of Goods</td>
															<td class="table_heading alignRight" style="width: 20%">PCS</td>
															<td class="table_heading alignRight" style="width: 20%">Gwt(g)</td>
															<td class="table_heading alignRight" style="width: 23%">Amount</td>
														</tr>
													</thead>
														<tr>
															<td colspan="5"><hr class="item_dashed"></td>
														</tr>
													
													<!--<tbody>-->
													<?php
													$i=1; 
													$pieces=0;
													$gross_wt=0;
													foreach($item_details as $items)
														{
															$pieces         +=$items['piece'];
															$gross_wt       +=$items['weight'];
															$tot_tax        +=$items['item_total_tax'];
															$sales_cost     +=$items['item_total_cost'];
															$total_cgst     +=$items['total_cgst'];
															$total_sgst     +=$items['total_sgst'];
															$total_igst     +=$items['total_igst'];
															$taxable_amt    +=$items['item_total_cost']-$items['item_total_tax'];
															$amt_in_words   = $this->ret_billing_model->no_to_words($billing['tot_bill_amount']);

															$item_taxable   =number_format((float)$items['item_total_cost']-$items['item_total_tax'],2,'.','');
															$tax_percentage =number_format(($items['item_total_tax']*100)/$item_taxable,'2','.','');
															$tot_tax_per    +=$tax_percentage;
														?>

														<tr>
														<td><?php echo $i;?></td>
														<td style="font-size:10px !important;" class='textOverflowHidden'><?php echo $items['product_name'];?></td>
														<td class="alignRight"><?php echo $items['piece'];?></td>
														<td class="alignRight"><?php echo $items['weight'];?></td>
														<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($item_taxable>0 ? ($item_taxable):0),2,'.',''));?></td>
														</tr>
													
													<?php $i++;}?>
												<!--</tbody> -->
														<tr>
															<td colspan="5"><hr class="item_dashed"></td>
														</tr>
														<tr class="total" style="font-weight: bold" >
															<td>Total</td>
															<td></td>
															<td class="alignRight"><?php echo $pieces;?></td>
															<td class="alignRight"><?php echo number_format($gross_wt,3,'.','');?></td>
															<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($taxable_amt),2,'.',''));?></td>
														</tr>
														<tr>
															<td colspan="5"><hr class="item_dashed"></td>
														</tr>
														
									
														<?php if($taxable_amt>0){?>
														<tr>
															<td colspan="2"></td>
															<td class="alignRight">SUB TOTAL</td>
															<td class="alignRight">Rs.</td>
															<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($taxable_amt),2,'.',''));?></td>
														</tr>
														<?php } ?>
														<?php if($total_sgst>0){?>
														<tr>
															<td colspan="2"></td>
															<td class="alignRight">SGST</td>
															<td class="alignRight"><?php echo ($item_details[0]['tax_percentage']/2).'%'?></td>
															<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($total_sgst),2,'.',''));?></td>
														</tr>
														<?php }?>
														
														<?php if($total_cgst>0){?>
														<tr>
															<td colspan="2"></td>
															<td class="alignRight">CGST</td>
															<td class="alignRight"><?php echo ($item_details[0]['tax_percentage']/2).'%'?></td>
															<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($total_cgst),2,'.',''));?></td>
														</tr>
                                                        <?php }?>
                                                        
														<?php if($total_igst>0){?>
															<tr>
															<td colspan="2"></td>
															<td class="alignRight">IGST</td>
															<td class="alignRight"><?php echo ($item_details[0]['tax_percentage']).'%'?></td>
															<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($total_igst),2,'.',''));?></td>
														</tr>
														<?php }?>
														
														<tr>
															<td colspan="2"></td>
															<td class="alignRight">NET AMT</td>
															<td class="alignRight">Rs.</td>
															<td class="alignRight"><?php echo moneyFormatIndia(number_format($sales_cost,2,'.',''));?></td>
														</tr>
												</table>
											</div>	
										</div>	
									</div>
									<?php }
									//echo "<pre>"; print_r($stones); echo "</pre>";exit;
									?>
									
								

								
										
												<?php 
												   $due_amount=0;
												    
												    if($billing['bill_type']==8)
													{
														
														if($billing['is_credit']==1)
														{
														    $due_amount=number_format(($billing['tot_bill_amount']-$billing['tot_amt_received']),2,'.','');
														}else
														{
														    /*if($billing['due_amount']!=$billing['tot_paid_amt'])
														    {
														        $due_amount=number_format(($billing['due_amount']-($billing['tot_amt_received']-$billing['tot_paid_amt']+$pur_total_amt)),2,'.','');
														    }*/
														    
														    $due_amount=number_format(($billing['tot_bill_amount']-$billing['tot_amt_received']),2,'.','');
														}
														
													}
													else{
														if($billing['is_credit']==1)
														{
															$due_amount=number_format(($billing['tot_bill_amount']-$billing['tot_amt_received']),2,'.','');
														}
													}
													
													if(sizeof($payment['pay_details'])>0){
													$cash_amt=0;
													$card_amt=0;
													$net_banking_amt=0;
													
													$gift_amount=0;
													$chit_amount=0;
													$total_amt=0;
													$adv_adj=0;
													$imps_amt=0;
													$rtgs_amt=0;
													$upi_amt=0;
													$chq_amt=0;
												
													
													foreach($payment['pay_details'] as $items)
														{
															$total_amt+=$items['payment_amount'];
															
															if($items['payment_mode']=='Cash')
															{
																$cash_amt+=$items['payment_amount'];
															}
															if($items['payment_mode']=='CHQ')
															{
																$chq_amt+=$items['payment_amount'];
															}
															if($items['payment_mode']=='DC' || $items['payment_mode']=='CC')
															{
																$card_amt+=$items['payment_amount'];
															}
															if($items['payment_mode']=='NB' && $items['transfer_type'] == 'RTGS')
															{
																$net_banking_amt+=$items['payment_amount'];
																$rtgs_amt+=$items['payment_amount'];
															}
															if($items['payment_mode']=='NB' && $items['transfer_type'] == 'IMPS')
															{
																$net_banking_amt+=$items['payment_amount'];
																$imps_amt+=$items['payment_amount'];
															}
															if($items['payment_mode']=='NB' && $items['transfer_type'] == 'UPI')
															{
																$net_banking_amt+=$items['payment_amount'];
																$upi_amt+=$items['payment_amount'];
															}
														}
												}?>
										
										
										
										
										               <?php 
										                if($cash_amt > 0 || $chq_amt>0 || $card_amt > 0 || $rtgs_amt > 0 || $imps_amt > 0 || $adv_adj > 0 || $gift_amount > 0 || $billing['advance_deposit']> 0)
										                {?>
										                    <div align="left">
										                        <label><b>RECEIPTS : </label>
										                    </div>
										                    
										                  <table id="pp" class="pay_mode_totals table text-center" style="font-weight: bold;">
    														<tr>
    														    
    														    <?php if($cash_amt>0){?>
    															<td>CASH</td>
    															<?php }?>
    															
    															<?php if($chq_amt>0){?>
    															<td>CHQ</td>
    															<?php }?>
    															
    															<?php if($card_amt>0) { ?>
    															<?php foreach($payment['pay_details'] as $cardItem) {
    															if($cardItem['payment_mode']=='DC' || $cardItem['payment_mode']=='CC') { ?>
    															<td>CARD(<?php echo $cardItem['card_no'] ?>)</td>
    															<?php } } }  ?>
    															
    															<?php if($rtgs_amt > 0) { ?>
    															<td>RTGS</td>
    															<?php } ?>
    															
    															<?php if($imps_amt > 0) { ?>
    															<td>IMPS</td>
    															<?php } ?>
    															
    															<?php if($upi_amt > 0) { ?>
    															<td>UPI</td>
    															<?php } ?>
    															
    															<?php if($due_amount != 0){?>
    															<td>DUE AMOUNT</td>
    															<?php }?>
    															
    															<?php if($adv_adj>0){?>
    															<td>ADVANCE ADJ</td>
    															<?php }?>
    															<?php if($chit_adj != 0 && ($billing['bill_type']!=10) ){?>
    															<td>CHIT ADJ</td>
    															<?php }?>
    															
    															<?php if($bouns_amt != 0){?>
    															<td>BONUS</td>
    															<?php }?>
    															
    															<?php if($ord_adj_amt != 0 || $billing['adv_adj_amt'] != 0){?>
    															<td>ADV ADJ</td>
    															<?php }?>
    															<?php if($gift_amount != 0){?>
    															<td>GIFT UTILIZED</td>
    															<?php }?>
    
    															<?php if($billing['credit_disc_amt'] != 0){?>
    															<td>DISCOUNT</td>
    															<?php }?>
    															
    															<?php if($billing['advance_deposit'] != 0){?>
                							        	        <td>Advance</td>
                							        	        <?php }?>
    															
    													
    														</tr>
    														<tbody>
    															<tr>
    													
                                                                    <?php 
    																    if($cash_amt>0)
    																    {?>
    																    <td><?php echo moneyFormatIndia(number_format($cash_amt,2,'.',''));?></td>
    																<?php }?>
    																
    																<?php 
    																    if($chq_amt>0)
    																    {?>
    																    <td><?php echo moneyFormatIndia(number_format($chq_amt,2,'.',''));?></td>
    																<?php }?>
    																<?php if($card_amt != 0){?>
    																	<?php foreach($payment['pay_details'] as $cardItem) {
    																	if($cardItem['payment_mode']=='DC' || $cardItem['payment_mode']=='CC') { ?>
    																	<td><?php echo moneyFormatIndia(number_format($cardItem['payment_amount'],2,'.',''));?></td>
    																<?php } } }?>
    																
    																<?php 
    																    if($rtgs_amt>0)
    																    {?>
    																    <td><?php echo moneyFormatIndia(number_format($rtgs_amt,2,'.',''));?></td>
    																<?php }?>
    																
    																<?php 
    																    if($imps_amt>0)
    																    {?>
    																    <td><?php echo moneyFormatIndia(number_format($imps_amt,2,'.',''));?></td>
    																<?php }?>
    																
    															    <?php 
    																    if($upi_amt>0)
    																    {?>
    																    <td><?php echo moneyFormatIndia(number_format($upi_amt,2,'.',''));?></td>
    																<?php }?>
                                                                    
    																<?php if($due_amount != 0){?>
    																<td><?php echo moneyFormatIndia(number_format($due_amount,2,'.',''));?></td>
    																<?php }?>

    																<?php if($adv_adj != 0){?>
    																<td><?php echo moneyFormatIndia(number_format($adv_adj,2,'.',''));?></td>
    																<?php }?>
    																
    																<?php if($chit_adj != 0  && ($billing['bill_type']!=10)){?>
    																<td><?php echo moneyFormatIndia(number_format($chit_adj,2,'.',''));?></td>
    																<?php }?>
    																
    																<?php if($bouns_amt>0){?>
    																<td><?php echo moneyFormatIndia(number_format($bouns_amt,2,'.',''));?></td>
    																<?php }?>
    																
    																<?php if($ord_adj_amt != 0 || $billing['adv_adj_amt'] != 0){?>
    																<td><?php echo moneyFormatIndia(number_format($ord_adj_amt+$billing['adv_adj_amt'],2,'.',''));?></td>
    																<?php }?>
    																
    																<?php if($gift_amount != 0){?>
    																<td><?php echo moneyFormatIndia(number_format($gift_amount,2,'.',''));?></td>
    																<?php }?>
    																
    																<?php if($billing['credit_disc_amt'] != 0){?>
    																<td><?php echo moneyFormatIndia(number_format($billing['credit_disc_amt'],2,'.',''));?></td>
    																<?php }?>
    
    																<?php if($billing['advance_deposit'] != 0){?>
                							        	                <td><?php echo number_format($billing['advance_deposit'],2,'.','') ?></td>
                							        	            <?php }?>
                							        	            
    															
    															</tr>
    														</tbody>
    													</table>
													
										                <?php }
										                ?>

													
													<?php 
										                if($cash_amt < 0 || $chq_amt<0  || $rtgs_amt < 0 || $imps_amt < 0 )
										                {?>
										                    <div align="left">
										                        <label><b>PAYMENTS : </label>
										                    </div>
										                    
										                    <table id="pp" class="pay_mode_totals table text-center" style="font-weight: bold;">
														<tr>
														    
														    <?php if($cash_amt<0){?>
															<td>CASH</td>
															<?php }?>
															
															<?php if($chq_amt<0){?>
															<td>CHQ</td>
															<?php }?>
															
															
															
															<?php if($rtgs_amt < 0) { ?>
															<td>RTGS</td>
															<?php } ?>
															
															<?php if($imps_amt < 0) { ?>
															<td>IMPS</td>
															<?php } ?>
															
															<?php if($upi_amt < 0) { ?>
															<td>UPI</td>
															<?php } ?>
															
															
														</tr>
														<tbody>
															<tr>
													
                                                                <?php 
																    if($cash_amt<0)
																    {?>
																    <td><?php echo moneyFormatIndia(number_format(($cash_amt*-1),2,'.',''));?></td>
																<?php }?>
																
																<?php 
																    if($chq_amt<0)
																    {?>
																    <td><?php echo moneyFormatIndia(number_format(($chq_amt*-1),2,'.',''));?></td>
																<?php }?>
																
																<?php 
																    if($rtgs_amt<0)
																    {?>
																    <td><?php echo moneyFormatIndia(number_format(($rtgs_amt*-1),2,'.',''));?></td>
																<?php }?>
																
																<?php 
																    if($imps_amt<0)
																    {?>
																    <td><?php echo moneyFormatIndia(number_format(($imps_amt*-1),2,'.',''));?></td>
																<?php }?>
																
															    <?php 
																    if($upi_amt<0)
																    {?>
																    <td><?php echo moneyFormatIndia(number_format(($upi_amt*-1),2,'.',''));?></td>
																<?php }?>
                                                                
															
															</tr>
														</tbody>
													</table>
													
										                <?php }
										                ?>
													
								
									
										
								
							</div><br>
							
							<?php if(sizeof($receiptDetails)>0){
                                $tot_adv=0;
                                $adj_amt=0;
                                
                                foreach($receiptDetails as $val)
                                {?>
                                   <div>
                                        <table id="pp" class="table text-center"style="width:85%" >
                                          <tr>
                                           <td><b>Receipt  No</b></td>
                                           <td><b>Receipt  Date</b></td> 
                                           <td><b>Receipt  Amount</b></td>
                                           <td><b>Adjusted Amount</b></td>
                                           <td><b>Utilized Amount</b></td>
                                           <td><b>Balance  Amount</b></td>
                                           
            
                                       </tr>
                                          <tbody>
                                              <tr>
                                              <td><?php  echo ($val['bill_no']);?></td>
                                              <td><?php  echo ($val['bill_date']);?></td>
                                              <td ><?php echo number_format($val['tot_receipt_amount'],2,'.','' );?></td>
                                              <td ><?php echo number_format($val['adjuseted_amt'],2,'.','' );?></td>
                                              <td ><?php echo number_format($val['tot_utilized_amt'],2,'.','' );?></td>
                                              <td ><?php echo number_format($val['bal_amt'],2,'.','' );?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div><br>
                            <?php }}?>
                            
						
							
						</div>
			</div><br>
			

		    
		    <p>This is computer generated or electronic invoice as per IT Act 2000 and not required to bear a signature or digital signature as per GST Notification No.74/2018- Central Tax dated 31.12.2018</p>
			</div><!-- /.box-body --> 
</div>
<div>
</span>          
</body></html>