<html>
	<head>
		<meta charset="utf-8">
		
		<title>Job Receipt - <?php echo $order['pur_no'];?> </title>
		
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/job_receipt.css">
		
		<style>
		    .addr_labels {
                display: inline-block;
                width: 30%;
				padding-bottom: 5px;
            }
    		
            .addr_values {
                display: inline-block;
                padding-left: -5px;
            }
            
            .addr_brch_labels {
    			display: inline-block;
    			vertical-align:top; 
				width: 40%; 
				text-align: right;
    		}
    
    		.addr_brch_values {
    			display: inline-block;
    			vertical-align:top; 
				width: 40%; 
				text-align: left;
    		}
		
		</style>
	</head>
	<body>
	    <?php 
	    function moneyFormatIndia($num) {
        	return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
        }
	    ?>
		<div class="PDFReceipt">
			<div class="heading">
				<div class="company_name"><h1><?php echo strtoupper($comp_details['company_name']); ?></h1></div>
				<div><?php echo strtoupper($comp_details['address1']) ?> , <?php echo strtoupper($comp_details['address2']) ?></div>
				<?php echo ($comp_details['email']!='' ? '<div>Email : '.$comp_details['email'].' </div>' :'') ?>
				<?php echo ($comp_details['gst_number']!='' ? '<div>GST : '.$comp_details['gst_number'].' </div>' :'') ?>
			</div><br>
			<hr class="borderline">
			<div style="width: 100%; text-transform:uppercase;margin-top:1px;margin-bottom:5px;text-align:center;font-size:20px;font-weight:bold;">
				<label><?php echo (isset($po_item['grn_type1']) && $po_item['grn_type1'] == 4) ? 'JOB WORK RECEIPT' : 'PURCHASE INVOICE'; ?></label>
			</div>
			<hr class="borderline">
			<div style="width: 100%; text-transform:uppercase;height:130px;">
				<div style="display: inline-block; width: 45%;">
					    <label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;'.'Mr./Ms.'.$po_item['supplier_name']."</div>"; ?></label><br>
    					<label><?php echo '<div class="addr_labels">Mobile</div><div class="addr_values">:&nbsp;&nbsp;'.$po_item['mobile']."</div>"; ?></label><br>
    					<label><?php echo ($po_item['supplier_address1']!='' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($po_item['supplier_address1']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($po_item['supplier_address2']!='' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;'.strtoupper($po_item['supplier_address2']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($po_item['supplier_city_name']!='' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($po_item['supplier_city_name']).($po_item['supplier_pincode']!='' ? ' - '.$po_item['supplier_pincode'].'.' :'')."</div><br>" :''); ?></label>
    					<label><?php echo ($po_item['supplier_state_name']!='' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($po_item['supplier_state_name'].'-'.$po_item['supplier_state_code']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($po_item['supplier_country_name']!='' ? '<div class="addr_labels">Country</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($po_item['supplier_country_name'])."</div><br>" :''); ?></label>
    					<label><?php echo (isset($po_item['pan_no']) && $po_item['pan_no']!='' ? '<div class="addr_labels">PAN</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($po_item['pan_no'])."</div><br>" :''); ?></label>
    					<label><?php echo (isset($po_item['gst_number']) && $po_item['gst_number']!='' ? '<div class="addr_labels">GST IN</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($po_item['gst_number'])."</div><br>" :''); ?></label>
                </div>
				
                
                <div style="display: inline-block; text-align: center !important; width: 19%; "></div>
                
               <div style="width: 35%; display: inline-block;vertical-align: top;text-align: right">
					 
					<label><?php echo ($po_item['po_date']!='' ? '<div class="addr_brch_labels">Invoice Date &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$po_item['po_date']."</div><br>" :''); ?></label><br>
					<label><?php echo ($po_item['po_ref_no']!='' ? '<div class="addr_brch_labels">Invoice No &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$po_item['po_ref_no']."</div><br>" :''); ?></label>
    						
					
				</div>
			</div><br>
			<!-- <hr class="borderline">
			<div style="width: 100%; text-transform:uppercase;margin-top:1px;">
			    <div style="text-align: center !important;"><?php echo $po_item['grn_type']?></div>
			</div> -->
			<div class="receipt_details" style="margin-top:-40px;">
				<table class="job_receipt_table">
					<thead>
						<tr>
							<th style="width:7px" class="alignLeft">S.No</th>
							<th style="width:7px" class="alignRight">Purity</th>
							<th style="width:25px" class="alignLeft">Description</th>
							<th style="width:5px" class="alignRight">HSN</th>
							<th style="width:5px" class="alignRight">Pcs</th>
							<th style="width:10px" class="alignRight">Gwt</th>
							<th style="width:10px" class="alignRight">Nwt</th>
							<th style="width:10px" class="alignRight">Touch</th>
							<th style="width:10px" class="alignRight">Rate</th>
							<th style="width:10px" class="alignRight">V.A(%)</th>
							<th style="width:10px" class="alignRight">Pure</th>
							<th style="width:10px" class="alignRight">MC</th>
							<th style="width:10px" class="alignRight">Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$i = 1;
							$total_pcs = 0;
							$total_gwt = 0;
							$total_nwt = 0;
							$total_wastage = 0;
							$total_mc = 0;
							$item_pure_wt = 0;
							$taxable_amt = 0;
							$total_cgst = 0;
							$total_sgst = 0;
							$total_igst = 0;
							$grandtotal = 0;
							foreach($po_item_details as $po_detail) { ?>
								<?php 
								$other_charges = 0;
								$stone_charges = 0;
								foreach($po_detail['other_charge_details'] as $val)
								{
								    $other_charges+=$val['total_charge_value'];
								}
								foreach($po_detail['stn_details'] as $sval){
								    $stone_charges+=$sval['po_stone_amount'];
								}
								$total_cgst+=$po_detail['total_cgst'];
								$total_sgst+=$po_detail['total_sgst'];
								$total_igst+=$po_detail['total_igst'];
								
								$total_pcs = $total_pcs + $po_detail['no_of_pcs'];
								$current_gwt = $po_detail['gross_wt'];
								$current_nwt = $po_detail['net_wt'];
								$current_pure_wt = $po_detail['item_pure_wt'];

								if($po_detail['uom'] == 'CT') {
									$current_gwt = $current_gwt * 0.2;
									$current_nwt = $current_nwt * 0.2;
									$current_pure_wt = $current_pure_wt * 0.2;
								}

								$total_gwt = $total_gwt + $current_gwt;
								$total_nwt = $total_nwt + $current_nwt;
								$item_pure_wt = $item_pure_wt + $current_pure_wt;
								$taxable_amt+=$po_detail['item_cost']-$po_detail['total_tax'];
								
								
								$making_charge = round($po_detail['mc_type'] == 1 ? $po_detail['mc_value'] * $po_detail['gross_wt'] : $po_detail['mc_value'] * $po_detail['no_of_pcs'],3); 
								$tot_making_charge += round($po_detail['mc_type'] == 1 ? $po_detail['mc_value'] * $po_detail['gross_wt'] : $po_detail['mc_value'] * 1,3); 

								
								$total_mc = $total_mc + $making_charge;
								
								$wastage = round(($po_detail['gross_wt'] / 100) * $po_detail['item_wastage'],3);
								
								
								$total_wastage = $total_wastage + $wastage;
								?>
								<tr>
									<td class="alignLeft" ><?php echo $i ?></td>
									<td class="alignRight"><?php echo $po_detail['purity_name'] ?></td>
									<td class="alignLeft"><?php echo $po_detail['product_name'] ?></td>
									<td class="alignRight"><?php echo $po_detail['hsn_code']?></td>
									<td class="alignRight"><?php echo $po_detail['no_of_pcs'] ?></td>
									<td class="alignRight"><?php echo moneyFormatIndia($po_detail['gross_wt']) ?></td>
									<td class="alignRight"><?php echo moneyFormatIndia($po_detail['stone_type'] == 0 ? $po_detail['net_wt'] : '') ?></td>
									<td class="alignRight"><?php echo moneyFormatIndia($po_detail['stone_type'] == 0 ? $po_detail['purchase_touch'] : '') ?></td>
									<td class="alignRight"><?php echo moneyFormatIndia($po_detail['fix_rate_per_grm']) ?></td>
									<td class="alignRight"><?php echo moneyFormatIndia($po_detail['stone_type'] == 0 ? $po_detail['item_wastage'] : '') ?></td>
									<td class="alignRight"><?php echo moneyFormatIndia($po_detail['stone_type'] == 0 ? $po_detail['item_pure_wt'] : '') ?></td>
									<td class="alignRight"><?php echo moneyFormatIndia($po_detail['stone_type'] == 0 ? $making_charge : ''); ?></td>
									<td class="alignRight"><?php echo moneyFormatIndia(number_format($po_detail['item_cost']-$po_detail['total_tax']-$other_charges,2,'.','')) ?></td>
								</tr>
								<?php if($po_detail['mc_value']>0){?>
								<tr>
									<td colspan="11"></td>
									<td class="alignRight"><?php echo !empty($po_detail['mc_value']) ? moneyFormatIndia($po_detail['mc_value']+0).'/'.($po_detail['mc_type'] == 1 ? 'Grm' : 'Pcs'):'';?></td>
									<td></td>
								</tr>
								<?php }?>
								<?php if($po_detail['stone_type']!=0){?>
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td><?php echo $po_detail['quality_code'];?></td>
									<td></td>
									<td class="alignRight"><?php echo $po_detail['net_wt'].'/'.$po_detail['uom'];?></td>
									<td class="alignRight">Rs.<?php echo $po_detail['fix_rate_per_grm'].'/'.$po_detail['rate_calc_type'];?></td>
									<td ></td>
									<td></td>
									<td></td>
									<td></td>
									<td class="alignRight"></td>
								</tr>
								<?php }?>
								<?php
    								foreach($po_detail['stn_details'] as $sval)
    							    {?>
    							        <tr>
    							            <td></td>
    							            <td></td>
    							            <td></td>
											<td></td>
    							            <td><?php echo $sval['stone_name'];?></td>
    							            <td></td>
    							            <td class="alignRight"><?php echo $sval['po_stone_wt'].'/'.$sval['uom_short_code'];?></td>
    							            <td class="alignRight">Rs.<?php echo $sval['po_stone_rate'].'/'.$sval['uom_short_code'];?></td>
    							            <td class="alignRight">Rs.<?php echo moneyFormatIndia(number_format($sval['po_stone_amount'],2,'.',''))?></td>
    							            <td></td>
    							            <td></td>
    							            <td></td>
    							            <td class="alignRight"></td>
    							        </tr>
    							    <?php }?>
    							    <?php if($stone_charges>0){?>
    							     <tr style="font-weight:bold;">
    							            <td></td>
    							            <td></td>
    							            <td></td>
    							            <td></td>
											<td></td>
    							            <td></td>
    							            <td class="alignRight"></td>
    							            <td class="alignRight">TOTAL</td>
    							            <td class="alignRight">Rs.<?php echo moneyFormatIndia(number_format($stone_charges,2,'.',''))?></td>
    							            <td></td>
    							            <td></td>
    							            <td></td>
    							            <td class="alignRight"></td>
    							        </tr>
    							      <?php }?>
								<?php ?>
								
								<?php
    								foreach($po_detail['other_charge_details'] as $val)
    							    {?>
    							        <tr>
    							            <td></td>
											<td></td>
    							            <td colspan="10"><?php echo $val['name_charge'];?></td>
    							            <td class="alignRight"><?php echo moneyFormatIndia(number_format(($val['calc_type']==2 ? ($val['pur_othr_charge_value']*$po_detail['no_of_pcs']) :$val['pur_othr_charge_value']),2,'.',''))?></td>
    							        </tr>
    							    <?php }
								?>

								<?php
    								foreach($po_detail['other_metal_details'] as $val)
    							    {?>
    							        <tr>
    							            <td></td>
    							            <td></td>
    							            <td></td>
											<td></td>
    							            <td><?php echo $val['cat_name'];?></td>
    							            <td></td>
    							            <td class="alignRight"><?php echo $val['othr_metal_wt'];?></td>
    							            <td class="alignRight">Rs.<?php echo $val['othr_metal_rate'];?></td>
    							            <td class="alignRight">Rs.<?php echo moneyFormatIndia(number_format($val['othr_metal_amt'],2,'.',''))?></td>
    							            <td></td>
    							            <td></td>
    							            <td></td>
    							            <td class="alignRight"></td>
    							        </tr>
    							    <?php }
								?>

								<?php 
								if($po_detail['remark']!=''){?>
								    <tr>
    							            <td></td>
    							            <td></td>
    							            <td colspan="11">REMARKS :- <?php echo $po_detail['remark'];?></td>
    							        </tr>
								<?php }
								?>
								
						<?php $i++; }	?>

						<tr style="font-weight:bold;">
							<td class="rateborders" ></td>
							<td class="rateborders alignCenter"> TOTAL</td>
							<td class="rateborders"></td>
							<td class="rateborders"></td>
							<td class="rateborders alignRight" ><?php echo $total_pcs ?></td>
							<td class="rateborders alignRight" ><?php echo moneyFormatIndia(number_format((float)$total_gwt, 3, '.', '')); ?></td>
							<td class="rateborders alignRight" ><?php echo moneyFormatIndia(number_format((float)$total_nwt, 3, '.', '')); ?></td>
							<td class="rateborders alignRight" ></td>
							<td class="rateborders alignRight" ></td>
							<td class="rateborders alignRight" ></td>
							<td class="rateborders alignRight" ><?php echo moneyFormatIndia(number_format((float)$item_pure_wt, 3, '.', '')); ?></td>
							<td class="rateborders alignRight" ><?php echo moneyFormatIndia(number_format((float)$tot_making_charge, 2, '.', '')); ?></td>
							<td class="rateborders alignRight" ><?php echo moneyFormatIndia(number_format((float)$taxable_amt, 2, '.', '')); ?></td>
						</tr>	
					</tbody>
					<tfoot>

					    <?php 
					    if($total_cgst>0)
					    {
							
							?>
					        <tr style="font-weight:bold;">
        						<td class="rateborders" style="border-left: none"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders alignRight">CGST(<?php echo ($po_detail['tax_percentage']/2) ?> %)</td>
        						<td class="rateborders alignRight"><?php echo moneyFormatIndia(number_format($total_cgst,2,".","")) ?></td>
        					</tr>
        					
        					<tr style="font-weight:bold;">
        						<td style="border-left: none"></td>
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
        						<td class="alignRight">SGST(<?php echo ($po_detail['tax_percentage']/2) ?> %)</td>
        						<td class="alignRight"><?php echo moneyFormatIndia(number_format($total_sgst,2,".","")) ?></td>
        					</tr>
        					
					    <?php }else if($total_igst>0){ ?>
					        <tr style="font-weight:bold;">
        						<td class="rateborders" style="border-left: none"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders"></td>
        						<td class="rateborders"></td>
        						<td colspan="2" class="alignRight">IGST(<?php echo ($po_detail['tax_percentage']) ?> %)</td>
        						<td class="alignRight"><?php echo moneyFormatIndia(number_format($total_igst,2,".","")) ?></td>
        					</tr>
					    <?php }?>


						<!--<?php if($po_item['tds_percent']>0){ ?> 
							<tr style="font-weight:bold;">
        						<td colspan="9"></td>
        						<td colspan="2" class="alignRight">TDS(<?php echo ($po_item['tds_percent']) ?> %)</td>
        						<td class="alignRight"><?php echo moneyFormatIndia(number_format($po_item['tds_tax_value'],2,".","")) ?></td>
        					</tr>
						<?php } ?> -->

						<?php if($po_item['tcs_tax_value'] > 0 || $po_item['tds_tax_value'] > 0){ ?>
							<tr style="font-weight:bold;">
        						<td class="" style="border-left: none"></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td colspan="3" class="alignRight">Bill Amount</td>
        						<td class="rateborders"><?php echo moneyFormatIndia(number_format($total_cgst + $total_sgst + $total_igst + $taxable_amt,2,".","")) ?></td>
        					</tr>
						<?php }?>

						<?php if($po_item['tds_tax_value'] > 0){ ?>
					        <tr style="font-weight:bold;">
        						<td class="" style="border-left: none"></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td colspan="2" class="alignRight"> TDS (<?php echo ($po_item['tds_percent']) ?> %)</td>
        						<td class="rateborders"><?php echo moneyFormatIndia(number_format($po_item['tds_tax_value'],2,".","")) ?></td>
        					</tr>
					    <?php }?>

						<?php if($po_item['tcs_tax_value'] > 0){ ?>
					        <tr style="font-weight:bold;">
        						<td class="" style="border-left: none"></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td class=""></td>
        						<td colspan="2" class="alignRight">TCS (<?php echo ($po_item['tcs_percent']) ?> %)</td>
        						<td class="alignRight"><?php echo moneyFormatIndia(number_format($po_item['tcs_tax_value'],2,".","")) ?></td>
        					</tr>
					    <?php }?>
						
			
					<?php $grandtotal =  $total_cgst + $total_sgst + $total_igst + $taxable_amt - $po_item['tds_tax_value'] + $po_item['tcs_tax_value']?>
						<tr>			
						<td class="rateborders border-left: none"></td>
						<td class="rateborders border-left: none"></td>
						<td colspan="2" class="rateborders"><b>FINAL TOTAL</b></td>
						<td class="rateborders"></td>
						<td class="rateborders"></td>
						<td class="rateborders"></td>
						<td class="rateborders"></td>
						<td class="rateborders"></td>
						<td class="rateborders"></td>
						<td class="rateborders"></td>
						<td class="rateborders"></td>
						<td class="rateborders alignRight"><b><?php echo moneyFormatIndia(number_format(round($grandtotal),2,".",""),2,'.','') ?></b></td>
					</tr>
					</tfoot>
				</table>
				&nbsp;&nbsp;
				<?php if(isset($po_item['rate_fixed']) && $po_item['rate_fixed'] !== '' && $po_item['po_type'] != 3 && $po_item['grn_type1'] != 2): ?>
					<!-- <div style="font-weight:bold;"><?php echo $po_item['rate_fixed'] . ':- ' . $po_detail['fix_rate_per_grm']; ?></div> -->
					<div style="font-weight:bold;"><?php echo $po_item['rate_fixed']; ?></div>
				<?php elseif($po_item['po_type'] == 3): ?>
					<div style="font-weight:bold;"><?php echo "NOTE : ";?></div><br>
					<div style="font-weight:bold;"><?php echo "VENDOR BEING A NON-GST DEALER AND HAVE SUBMITTED A DECLARATION, HENCE NO GST IS CHARGED IN THE INVOICE.";?></div>
				<?php endif; ?>
			</div>
			
			<div class="footer">
				<div class="footer_left" style="width:25%;">
						<label>Audited By</label>
				</div>
				<div class="footer_left" style="width:20%;">
						<label>Party Sign</label>
				</div>
				<div class="footer_left" style="width:23%;">
						<label>Manager Sign</label>
				</div>
				<div class="footer_left"style="width:25%;">
						<label>Operator </label></br>
						<?php echo $po_item['emp_name'] .'-'. date("d-m-y h:i:sa"); ?>
				</div>
			</div>
		</div>
		
		<script type="text/javascript">
			setTimeout(function(){
			
				window.print();
			
			}, 1000);
		</script>
	</body>
</html>