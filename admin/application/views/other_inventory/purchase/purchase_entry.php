<html>
	<head>
		<meta charset="utf-8">
		
		<title>Purchase Entry - <?php echo $order['pur_no'];?> </title>
		
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/purchase_entry.css">
		
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
			<div style="width: 100%; text-transform:uppercase;height:130px;">
				<div style="display: inline-block; width: 45%;">
					    <label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;'.'Mr./Ms.'.$pur_item['supplier_name']."</div>"; ?></label><br>
    					<label><?php echo '<div class="addr_labels">Mobile</div><div class="addr_values">:&nbsp;&nbsp;'.$pur_item['supplier_mobile']."</div>"; ?></label><br>
    					<label><?php echo ($pur_item['supplier_address1']!='' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($pur_item['supplier_address1']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($pur_item['supplier_address2']!='' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;'.strtoupper($pur_item['supplier_address2']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($pur_item['supplier_city_name']!='' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($pur_item['supplier_city_name']).($pur_item['supplier_pincode']!='' ? ' - '.$pur_item['supplier_pincode'].'.' :'')."</div><br>" :''); ?></label>
    					<label><?php echo ($pur_item['supplier_state_name']!='' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($pur_item['supplier_state_name'].'-'.$pur_item['supplier_state_code']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($pur_item['supplier_country_name']!='' ? '<div class="addr_labels">Country</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($pur_item['supplier_country_name'])."</div><br>" :''); ?></label>
    					<label><?php echo (isset($pur_item['pan_no']) && $pur_item['pan_no']!='' ? '<div class="addr_labels">PAN</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($pur_item['pan_no'])."</div><br>" :''); ?></label>
    					<label><?php echo (isset($pur_item['gst_number']) && $pur_item['gst_number']!='' ? '<div class="addr_labels">GST IN</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($pur_item['gst_number'])."</div><br>" :''); ?></label>
                </div>
				
                
                <div style="display: inline-block; text-align: center !important; width: 19%; "></div>
                
               <div style="width: 35%; display: inline-block;vertical-align: top;text-align: right">
					 
					<label><?php echo ($pur_item['entry_date']!='' ? '<div class="addr_brch_labels"> Date &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$pur_item['entry_date']."</div><br>" :''); ?></label><br>
					<label><?php echo ($pur_item['pur_order_ref_no']!='' ? '<div class="addr_brch_labels">Ref No &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$pur_item['pur_order_ref_no']."</div><br>" :''); ?></label>
    						
					
				</div>
			</div><br>
			<!-- <hr class="borderline">
			<div style="width: 100%; text-transform:uppercase;margin-top:1px;">
			    <div style="text-align: center !important;"><?php echo $po_item['grn_type']?></div>
			</div> -->
			<div class="receipt_details" style="margin-top:-40px;width: 100%;">
				<table class="job_receipt_table">
					<thead>

						<tr>
							<th  class="alignLeft">S.No</th>
							<th  class="alignLeft">Description</th>
							<th  class="alignRight">Pcs</th>
							<th  class="alignRight">Rate</th>
							<!-- <th  class="alignRight">Gst</th>
							<th  class="alignRight">Gst Amount</th> -->
							<!-- <th  class="alignRight">Taxable Amount</th> -->
							<th  class="alignRight">Amount</th>
 
						</tr>
					</thead>
					<tbody>
						<?php
							$i = 1;
							$total_pcs = 0;
							$total_rate = 0;
							$tot_gst = 0;
							$tax_amount = 0;
							$tot_amount = 0;
							$final_amt = 0;
							$gst_amt =0;
							// $total_cgst = 0;
							// $total_sgst = 0;
							// $total_igst = 0;
							$grandtotal = 0;
							$total =0;
							$total_gst =0;
							$gst =0;
							foreach($pur_item_details as $po_detail) { ?>
								<?php 
							
								// $total_cgst+=$po_detail['total_cgst'];
								// $total_sgst+=$po_detail['total_sgst'];
								// $total_gst = $total_gst + $po_detail['gst_amount'];
								// $gst = $gst + $po_detail['pur_gst'];

								$total_pcs = $total_pcs + $po_detail['tot_pcs'];
								$total_rate = $total_rate + $po_detail['tot_rate'];
								$tot_amount+=($po_detail['tot_amount']-$po_detail['gst_amount']);
								// $total+= ($total_gst - $tot_amount)/2;
								?>
								<tr>
									<td class="alignLeft" ><?php echo $i ?></td>
									<td class="alignLeft"><?php echo  $po_detail['product_name']?></td>
									<td class="alignRight"><?php echo number_format((float)$po_detail['tot_pcs'], 0, '.', ''); ?></td>
									<td class="alignRight"><?php echo moneyFormatIndia($po_detail['tot_rate']) ?></td>
									<td class="alignRight"><?php echo moneyFormatIndia(number_format($po_detail['tot_amount'] - $po_detail['gst_amount'],2,'.','')); ?></td>
								</tr>

								<!-- <php if($po_detail['mc_value']>0){?>
								<tr>
									<td colspan="10"></td>
									<td class="alignRight"><php echo !empty($po_detail['mc_value']) ? moneyFormatIndia($po_detail['mc_value']+0).'/'.($po_detail['mc_type'] == 2 ? 'Grm' : 'Pcs'):'';?></td>
									<td></td>
								</tr>
								<php }?>
								<php if($po_detail['stone_type']!=0){?>
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td><php echo $po_detail['quality_code'];?></td>
									<td></td>
									<td class="alignRight"><php echo $po_detail['net_wt'].'/'.$po_detail['uom'];?></td>
									<td class="alignRight">Rs.<php echo $po_detail['fix_rate_per_grm'].'/'.$po_detail['rate_calc_type'];?></td>
									<td ></td>
									<td></td>
									<td></td>
									<td></td>
									<td class="alignRight"></td>
								</tr>
								<php }?>
								<php
    								foreach($po_detail['stn_details'] as $sval)
    							    {?>
    							        <tr>
    							            <td></td>
    							            <td></td>
    							            <td></td>
    							            <td><php echo $sval['stone_name'];?></td>
    							            <td></td>
    							            <td class="alignRight"><php echo $sval['po_stone_wt'].'/'.$sval['uom_short_code'];?></td>
    							            <td class="alignRight">Rs.<php echo $sval['po_stone_rate'].'/'.$sval['uom_short_code'];?></td>
    							            <td class="alignRight">Rs.<php echo moneyFormatIndia(number_format($sval['po_stone_amount'],2,'.',''))?></td>
    							            <td></td>
    							            <td></td>
    							            <td></td>
    							            <td class="alignRight"></td>
    							        </tr>
    							    <php }?>
    							    <php if($stone_charges>0){?>
    							     <tr style="font-weight:bold;">
    							            <td></td>
    							            <td></td>
    							            <td></td>
    							            <td></td>
    							            <td></td>
    							            <td class="alignRight"></td>
    							            <td class="alignRight">TOTAL</td>
    							            <td class="alignRight">Rs.<php echo moneyFormatIndia(number_format($stone_charges,2,'.',''))?></td>
    							            <td></td>
    							            <td></td>
    							            <td></td>
    							            <td class="alignRight"></td>
    							        </tr>
    							      <php }?>
								<php ?>
								
								<php
    								foreach($po_detail['other_charge_details'] as $val)
    							    {?>
    							        <tr>
    							            <td></td>
    							            <td colspan="10"><php echo $val['name_charge'];?></td>
    							            <td class="alignRight"><php echo moneyFormatIndia(number_format(($val['calc_type']==2 ? ($val['pur_othr_charge_value']*$po_detail['no_of_pcs']) :$val['pur_othr_charge_value']),2,'.',''))?></td>
    							        </tr>
    							    <php }
								?>
								<php 
								if($po_detail['remark']!=''){?>
								    <tr>
    							            <td></td>
    							            <td colspan="11">REMARKS :- <php echo $po_detail['remark'];?></td>
    							        </tr>
								<php }
								?> -->
								
						<?php $i++; }	?>

						<tr style="font-weight:bold;">
							<td class="rateborders" ></td>
							<td class="rateborders alignCenter"> TOTAL</td>
							<td class="rateborders alignRight" ><?php echo moneyFormatIndia(number_format((float)$total_pcs, 0, '.', '')); ?></td>
							<td class="rateborders alignRight" ><?php echo moneyFormatIndia(number_format((float)$total_rate, 2, '.', '')); ?></td>
							<td class="rateborders alignRight" ><?php echo moneyFormatIndia(number_format((float)$tot_amount, 2, '.', '')); ?></td>
						
						</tr>	
					</tbody>
					<tfoot>

					<?php
					$total_gst = 0;
					$gst = 0;
					$total = 0;
					foreach ($pur_gst_details as $po_detail) {
						$total_gst += $po_detail['gst_amount'] / 2;
						// $gst += $po_detail['pur_gst'];
					?>

					<?php if ($comp_details['id_state'] == $pur_item['id_state']) { ?>
						<tr style="font-weight:bold;">
							<td style="border-left: none"></td>
							<td></td>
							<td colspan="2" class="alignRight">CGST(<?php echo ($po_detail['pur_gst'] / 2) ?> %)</td>
							<td class="alignRight"><?php echo moneyFormatIndia(number_format($total_gst, 2, ".", "")) ?></td>
						</tr>
						<tr style="font-weight:bold;">
							<td style="border-left: none"></td>
							<td></td>
							<td colspan="2" class="alignRight">SGST(<?php echo ($po_detail['pur_gst'] / 2) ?> %)</td>
							<td class="alignRight"><?php echo moneyFormatIndia(number_format($total_gst, 2, ".", "")) ?></td>
						</tr>
					<?php } ?>

					<?php if ($comp_details['id_state'] != $pur_item['id_state']) { ?>
						<tr style="font-weight:bold;">
							<td style="border-left: none"></td>
							<td></td>
							<td colspan="2" class="alignRight">IGST(<?php echo ($po_detail['pur_gst']) ?> %)</td>
							<td class="alignRight"><?php echo moneyFormatIndia(number_format($po_detail['gst_amount'], 2, ".", "")) ?></td>
						</tr>
					<?php } ?>

					<?php } ?>
						
			
					<?php $grandtotal = $tot_amount + $total_gst + $total_gst  ?>
		
						<tr>			
						<td class="rateborders border-left: none"></td>
						<td class="rateborders border-left: none"></td>
						<td colspan="2" class="rateborders"><b>FINAL TOTAL</b></td>
						<td class="rateborders alignRight"><b><?php echo moneyFormatIndia(number_format(round($grandtotal,2),2,".",""),2,'.','') ?></b></td>
					</tr>
					</tfoot>

				</table>
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
						<?php echo $pur_item['emp_name'] .'-'. date("d-m-y h:i:sa"); ?>
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