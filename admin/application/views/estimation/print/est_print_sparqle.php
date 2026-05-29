<html><head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Estimation</title>
		<style type="text/css">
			body, html {
			margin-bottom:0;
			}
			.alignLeft {
				text-align: left;
			}
			.alignRight {
				text-align: right;
			}
			.item_name {
				font-weight: bold;
			}
			.finalTotal {
				width: 109.6%;
				font-size: 10px !important;
			}
			.received{
				text-align:left; 
				font-size: 4px !important;
			}
			.tax{
				text-align:right; 
				font-size: 4px !important;
			}
			@page { 
				size: 78mm 250mm;
				margin-bottom: 100px !important;	
			} 
			.Row:after {
				content: "";
				display: table;
				clear: both;
			}
			.Column {
				float: left;
 				width: 50%;
				/* background-color: red; Optional */
			}
			.C {
				display: table-cell;
				width: 30%;
				/* background-color: red; Optional */
			}
			/* output size */
			 span { display: inline-block; 
			} 
			.PDFReceipt *{



			font-family:sans-serif;



			}



			.PDF_CusReceipt *{



			font-family:sans-serif;





			}



			.PDF_receipt_thermal *{



			font-family:sans-serif;





			}



			.plugin

			{

				margin-left:-25px !important;

				margin-top: -45px !important;

				height:100px !important;

				margin-bottom:-40px !important;

				font-size:12px !important;	

				text-transform: uppercase;

			}



			.header

			{

				font-size:15px !important;

				text-align: center;

			}



			.tap_head

			{

				margin-top:-10px !important;

			}





			.cus_name

			{

				width:100% !important;

				font-size:12px !important;

				text-align:left;

			}

			.estimation_datetime

			{

				text-align:right;

				width:110%;

			}



			.metal_rate

			{

				width:250px !important;

				font-size:11px !important;	

				margin-left:-4px !important;	

				text-transform: uppercase;

			}

			.metal_rate td

			{

				padding-top:-2px !important;

			}

			.dashed

			{

				border-top:1px dashed black;

				border-bottom:0px;

				margin-left:-5px !important;

				width:120% !important;

			}



			.tot_dashed

			{

				border-top:1px dashed black;

				border-bottom:0px;

				margin-left:0px !important;

				width:250px !important;

			}



			.item_dashed

			{

				border-top:1px dashed black;

				border-bottom:0px;

				margin-left:0px !important;

				width:250px !important;

			}







			.estimation

			{

					margin-left:-2px !important;

					font-size:11px !important;

			}



			.purchase

			{

				width:250px !important;

				margin-left:-2px !important;	

			}



			.purchase td

			{

				font-size:12px !important;

			}

			.amount
			{
			font-size:14px !important;
			}



			.estimation td

			{

				padding-top:0px !important;

			}

			.breif_copy

			{

				font-size:15px !important;

				text-align: center;

			}

		</style>
	</head>
	<?php
		function moneyFormatIndia($num) {
		return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
	}?>
<body class="plugin">
	<span class="PDFReceipt">
		<div class="printable">
		   <!-- <h4 style="text-align:center;"> OM MURUGA </h4>	 -->
		   <h3 style="text-align:center;">Estimation - <?php echo $estimation['esti_no'];?> </h3> 
			<div>
				
						
						<table class="metal_rate" style="width:120%">
						<tbody>
						<tr>
                                <th style="text-align: left;">Rate</th>
                                <th colspan="2"></th>
                                <th class="alignRight"><?php echo date('d-m-Y',strtotime($estimation['estimation_datetime']));?></th>
                            </tr>   
                            <tr style="text-transform: capitalize;">
                                    <td colspan="2">Gold 916</td>
                                    <td class="alignRight" colspan="2"> 
                    <?php if($est_other_item['item_details'][0]['metal_type']==1 && $est_other_item['item_details'][0]['category_id']==1){
                        echo number_format($est_other_item['item_details'][0]['est_rate_per_grm'],2,'.','').'/GM';
                        }else{
                        echo number_format($metal_rates['goldrate_22ct'],2,'.','').'/GM';
                        }   
                                        ?></td>
                                    <!-- <td class="alignRight" rowspan="3"><img src="<?php echo $filename;?>" alt="" style="height:60px;" ></td> -->
                                </tr>
                                <tr style="text-transform: capitalize;">                                
                                    <td colspan="2">18KT</td>
                                    <td class="alignRight" colspan="2"><?php 
                                    
                                if($est_other_item['item_details'][0]['metal_type']==1 && $est_other_item['item_details'][0]['category_id']==4){
                                    echo number_format($est_other_item['item_details'][0]['est_rate_per_grm'],2,'.','').'/GM';
                                }else{
                                    echo number_format($metal_rates['goldrate_18ct'],2,'.','').'/GM';
                                }?></td>
                                </tr>
								<tr style="text-transform: capitalize;">                                
                                    <td colspan="2">14KT</td>
                                    <td class="alignRight" colspan="2"><?php 
                                    
                                if($est_other_item['item_details'][0]['metal_type']==1 && $est_other_item['item_details'][0]['category_id']==4){
                                    echo number_format($est_other_item['item_details'][0]['est_rate_per_grm'],2,'.','').'/GM';
                                }else{
                                    echo number_format($metal_rates['goldrate_14ct'],2,'.','').'/GM';
                                }?></td>
                                </tr>
                                <tr style="text-transform: capitalize;">                        
                                    <td colspan="2">Silver</td>
                                    <td class="alignRight" colspan="2">
                                <?php if($est_other_item['item_details'][0]['metal_type']==2){
                                        echo number_format($est_other_item['item_details'][0]['est_rate_per_grm'],2,'.','').'/GM';          
                                }else{
                                        echo number_format($metal_rates['silverrate_1gm'],2,'.','').'/GM';  
                                    }?></td>
                                </tr>
							</tbody>
						</table>
						
						
	
				<div class="item_details">
        		    <?php if(sizeof($est_other_item['item_details'])){?>
        			<table class="estimation purchase" style="width:110%;">
        				
					<tr>
        						<td><hr class="tot_dashed"></td>
        					</tr>
        				<!--</thead>-->
        				<tbody>
        					<?php 
        					    $tot_payable=0;
								$tot_purchase=0;
        					    $market_rate_cost=0;
        					    $total_wt=0;
        					    $total_gwt=0;
        					    $net_wt=0;
        					    $total_piece=0;
        					    $total_net_wt=0;
        					    $tag_net_wt=0;
        					    $making_charge=0;
								$total_tax=0;
								$sub_total=0;
								$total_dia_wt = 0;
        					    
        					    
        					    $paid_advance   =0;
				                $paid_weight    =0;
				                $wt_amt         =0;
				                $tot_adv_paid   =0;
				                $chit_amount    =0;
				                $total_chit_amount    =0;
				                if(sizeof($est_other_item['advance_details'])>0)
				                {
				                    foreach($est_other_item['advance_details'] as $advance)
            					    {
            					            $paid_advance+=$advance['paid_advance'];
    					                    $paid_weight+=$advance['paid_weight'];
    					                    $wt_amt+=($advance['paid_weight']);
            					    }
            					    $tot_adv_paid=number_format(($paid_advance+$wt_amt),2,'.','');
				                }
				                
				                if(sizeof($est_other_item['chit_details'])>0)
				                {
				                    foreach($est_other_item['chit_details'] as $advance)
            					    {
            					            $chit_amount+=$advance['utl_amount'];
            					    }
				                }
				                
				                
				                $item_no = 1;
        						foreach($est_other_item['item_details'] as $items) {
        						    
        						$making_charge=0;
        						
        						$stone_price=0;
        						
								$stone_weight=0;
								
								$stone_piece=0;
								
								$charge_price=0;
								
								$tag_other_itm_amount=0;
        						
        						$certification_cost=0;
        						
        						$tag_other_itm_grs_weight=0;
        						
        						$total_piece+=$items['piece'];
        						
        						$total_gwt+=$items['gross_wt'];
        						
        						$total_wt+=$items['net_wt'];
        						
								$tot_payable+=$items['item_cost'];
        						
        						$market_rate_cost+=$items['market_rate_cost'];
								
								$payable_without_tax = $items['item_cost']-$items['item_total_tax'];
								$sub_total += $payable_without_tax;
								
								$total_tax += $items['item_total_tax'];
        						
        						if($items['is_partial']==1)
        						{
        							$net_wt+=$items['net_wt'];
        							$tag_net_wt+=$items['tag_net_wt'];
        						}
        						if($items['calculation_based_on']==0)
        						{
        						  $wast_wgt=number_format((($items['gross_wt']) * ($items['wastage_percent']/100)),2,'.','');
        						  
        						  $making_charge=($items['mc_type']==2 ? $items['gross_wt']*$items['mc_value'] : $items['mc_value']*$items['piece']);
        						  
        						}else if($items['calculation_based_on']==1)
        						{
        							$wast_wgt=number_format((($items['net_wt']) * ($items['wastage_percent']/100)),2,'.','');
        							
        							$making_charge=($items['mc_type']==2 ? $items['net_wt']*$items['mc_value'] : $items['mc_value']*$items['piece']);
        							
        						}else if($items['calculation_based_on']==2)
        						{
        							$wast_wgt=number_format((($items['net_wt']) * ($items['wastage_percent']/100)),3,'.','');
        							
        							$making_charge=($items['mc_type']==2 ? $items['gross_wt']*$items['mc_value'] : $items['mc_value']*1);
        							
        						}
        						foreach($items['stone_details'] as $stone)
        						{
									//$stone+=$stone['stone_name'];
        							$stone_price+=$stone['price'];
									$stone_weight+=$stone['wt'];
									$stone_piece+=$stone['pieces'];
        							$certification_cost+=$stone['certification_cost'];
									if($stone['stone_type'] == 1 && $stone['uom_id'] == 6) {
										$total_dia_wt += $stone['wt'];
									}
        						}
        						
        						foreach($items['charges'] as $charge)
        						{
        						    $charge_price+=$charge['amount'];
        						}
        						
        						foreach($items['other_metal_details'] as $other_metal_details)
        						{
        						    $tag_other_itm_grs_weight+=$other_metal_details['tag_other_itm_grs_weight'];
        						    $tag_other_itm_amount+=$other_metal_details['tag_other_itm_amount'];
        						}
        						
        					?>
							
        					<tr style="margin-top:2px !important;">
        						<td colspan="3"><?php echo $item_no; ?>) <?php echo substr($items['product_name'],0,15).(sizeof($items['child_tag_details'])>0 ? '+': '');?></td>
								<td colspan="4" > <?php echo $items['is_916']==1 ? $items['cat_name'] :''; ?></td>
							</tr>
							<!-- <tr>
								<td colspan="3"></td>
								<td colspan="4" style="text-transform: capitalize;"class="alignRight"> <?php echo $items['less_wt']>0 ? 'L.Wt '.$items['less_wt']:''; ?></td>
							</tr> -->
							<!-- <tr>
								<td colspan="4"><?php echo $items['metal_name'].' - (Rs. '.(moneyFormatIndia(number_format($items['est_rate_per_grm'],2,'.',''))).'/GM)' ?></td>
							</tr> -->
							<!--<tr>
								<td colspan="3"><?php echo $items['sub_design_name'];?></td>
								
							</tr>
							-->
							<tr>
                                <td colspan="2"><?php echo $items['sub_design_name']; ?></td>
                                <td colspan="5" style="text-align:right;font-size:9px!important"><?php echo $items['est_rate_per_grm'] != '' ? '@Rate ' . $items['est_rate_per_grm'] . '/GM' : '' ?></td>
                            </tr>
                                    
							<tr>
								<td colspan="4"><?php echo (!$items['tag_code']? $items['product_short_code']:$items['tag_code']).'/'.$items['piece'];?></td>
								
							</tr>
							<?php if(sizeof($items['child_tag_details']) > 0 ){
								foreach($items['child_tag_details'] as $citems){ ?>

							<tr>
								<td colspan="7" ><?php echo ($citems['label']!=''? $citems['label']:'').($citems['piece']!=''?'/'. $citems['piece']:'')?></td>
							</tr>
							<tr>
							<td colspan="7"><?php echo ($citems['product_name']!=''? $citems['product_name']:'').($citems['sub_design_name']!=''?' - '. $citems['sub_design_name']:'')?></td>
							</tr>

							<?php 	}
								}?>
							<?php $con = ($items['metal_type'] == 2 && $items['calculation_based_on'] == 3) ? FALSE :  TRUE;?>
							<?php if($con){?>

								<tr>
								<td style="text-transform: capitalize;"> Weight...</td>
								<td></td>
								<td></td>
								<th colspan="4" class="alignRight" style="font-size:17px !important;">  <?php echo $items['gross_wt'].'<label style="font-size:12px !important;" class="alignRight"> G</label>';?></th>
							</tr>
								
								<?php }?>
							<tr>
								<td></td>
								<td></td>
								<td class="alignRight" style="text-transform: capitalize;">VA</td>
								<td colspan="4" class="alignRight"><?php 
									$type = isset($items['type']) ? $items['type'] : 1;
									$method = (isset($items['type']) && $items['type'] == 2 && isset($items['wc_method']) && $items['wc_method'] != '') ? $items['wc_method'] : (isset($items['wastag_method']) ? $items['wastag_method'] : 1);
									echo ($method == 2) ? number_format($wast_wgt, 3).' g' : number_format($items['wastage_percent'], 2).' %'; 
								?></td>
							</tr>
							<tr>
									
								
								<td colspan="6" class="alignRight" style="text-transform: capitalize;">Making Charges...<?php echo $items['mc_type']==2 ? '/gm':'/pcs' ?> </td>
									<td class="alignRight"><?php echo moneyFormatIndia(number_format($items['mc_value']));?> </td>
							</tr>
							
							<!-- stone details-->
							<?php if(sizeof($items['stone_details']) > 0) { ?>
								<?php foreach($items['stone_details'] as $stone) { 
									$stone_rate=$stone['price'];
									?>
									
									<tr>
									    <td colspan="2"><?php echo $stone['stone_name'];?></td>
										<td colspan="4" class="alignRight"><?php echo
										 ($stone['stone_cal_type']==2  ?$stone['pieces'] : number_format($stone['wt'],3,'.',''))
										.($stone['stone_cal_type']==2 ?'PCS': $stone['uom_short_code'])
										.'*'.(
											$stone['rate_per_gram'] !== null && $stone['rate_per_gram'] !== ''
											? moneyFormatIndia(number_format($stone['rate_per_gram']))
											: ($stone['wt'] > 0
												? number_format(($stone['price'] / $stone['wt']), 0, '.', '')
												: 0
											))
										
										?></td>
										
										
									</tr>
									<tr>
										<td><?php echo moneyFormatIndia(number_format($stone['price'],0,'.','')) ?></td>
								</tr>
									
							<?php } } ?>
							
        	                       
							<!--  <?php if(sizeof($items['other_metal_details']) > 0) { ?>
								<?php foreach($items['other_metal_details'] as $other_metal_details) { 
									?>
									
									<tr>
										<td colspan="4"><?php echo $other_metal_details['metal'].' - (Rs. '.(moneyFormatIndia($other_metal_details['tag_other_itm_rate'])).')' ?></td>
									</tr>
									<tr>
										<td ><?php echo $other_metal_details['tag_other_itm_grs_weight'] ?></td> 
										<td class="alignRight"><?php echo $other_metal_details['tag_other_itm_grs_weight'] ?></td>
										<td class="alignRight"><?php echo $other_metal_details['tag_other_itm_wastage'] ?></td>
										<td class="alignRight"><?php echo $other_metal_details['tag_other_itm_mc'] ?></td>
										<td class="alignRight"><?php echo moneyFormatIndia(number_format($other_metal_details['tag_other_itm_amount'])) ?></td>
										<td class="alignRight"><br><br></td>
									</tr>
							<?php } } ?> -->
							
							<?php if(sizeof($items['charges']) > 0) { ?>
								<?php foreach($items['charges'] as $charge) { 
									?>
									<tr>
										<td colspan="2"><?php echo $charge['code_charge'].' Charges' ?></td>
										<td></td>
										<td></td>
										<td></td>
										<td class="alignRight"><?php echo moneyFormatIndia(number_format($charge['amount'],2,'.',''));?></td>
									</tr>
							<?php } } ?>
        					<?php if($certification_cost>0){?>
        					<tr>
        						<td style="text-transform: capitalize;">CERTIFICATION CHARGE</td>
        						<td></td>
        						<td></td>
        						<td></td>
        						<td class="alignRight"><?php echo moneyFormatIndia(number_format($certification_cost,2,'.',''));?></td>
        					</tr>
        					<?php }?>
        					<tr style="display:none">
        						<td><?php echo $items['tgrp_name'];?></td>
        						<td></td>
        						<td></td>
        						<td></td>
        						<td class="alignRight"><?php echo moneyFormatIndia(number_format($items['item_total_tax'],2,'.',''));?></td>
        					</tr>
								<tr>
									<td></td>
									<td></td>
									<td></td>

									<td colspan="4" class="alignRight" style="font-size:18px bold !important;">  <?php echo moneyFormatIndia(number_format($items['item_cost'])) ;?></td>
								</tr>
							<tr>
        						<td><hr class="tot_dashed"></td>
        					</tr>
        					<?php $item_no ++; } ?>
        					
							
        					<?php if($tot_adv_paid>0){?>
        					
        					   <tr style="font-weight:bold;">
        					    <td style="text-transform: capitalize;">SUB TOTAL</td>
        						<td class=""></td>
        						<td class=""></td>
        						<td></td>
                                <td class="alignRight">
								<?php echo moneyFormatIndia(number_format($tot_payable)); ?></td>
        					</tr>
        					<tr>
        						<td><hr class="tot_dashed"></td>
        					</tr>
            					<tr>
            						<td style="text-transform: capitalize;">Adv Paid</td>
            						<td></td>
            						<td></td>
            						<td></td>
            						<td class="alignRight"><?php echo moneyFormatIndia(number_format($tot_adv_paid));?></td>
            					</tr>
            					<tr>
            						<td><hr class="tot_dashed"></td>
            					</tr>
        					<?php }?>
        					
        					<!-- bottom -->
        					        					<tr>
								<td colspan="2" style="text-transform: capitalize;">Tot wt </td>
        					    <td class="alignRight"><?php echo number_format($total_gwt,3,'.','');?></td>
							</tr>
							<tr>
        						<td colspan="2" style="text-transform: capitalize;">Tot pcs </td>
								<td class="alignRight"><?php echo $total_piece;?></td>
							</tr>

							<?php if($total_dia_wt > 0) { ?>
									<tr>
										<td colspan="2" style="text-transform: capitalize;">Tot Dia Ct </td>
										<td class="alignRight"><?php echo number_format($total_dia_wt, 3, '.', '');?></td>
									</tr>
							<?php } ?>

							<?php if ($estimation['is_eda'] != 1) { ?>
							<?php if(($comp_details['id_country'] == $estimation['id_country']) || $estimation['id_country'] == ''){ 
						
						if(($comp_details['id_state'] == $estimation['id_state']) || $estimation['id_country'] == '') { ?>

							<tr>

								<td colspan="2">CGST</td>

								<td class="alignRight"><?php echo $items['tax_percentage'] / 2 ?>%</td> 

								<td class="alignRight" colspan="4"><?php echo moneyFormatIndia(number_format($total_tax/2,2,'.',''));?></td>

							</tr>

							<tr>

								<td colspan="2">SGST</td>

								<td class="alignRight"><?php echo $items['tax_percentage'] / 2 ?>%</td>

								<td class="alignRight" colspan="4"><?php echo moneyFormatIndia(number_format($total_tax/2,2,'.',''));?></td>

								<!-- <th colspan="3" class="alignRight" style="font-size:15px !important"> <?php echo moneyFormatIndia(round($tot_payable-$tot_adv_paid)); ?></th> -->

							</tr>

							<?php } else { ?>

								<tr>

								<td colspan="2">IGST</td>

								<td class="alignRight"><?php echo $items['tax_percentage']?>%</td>

								<td class="alignRight" colspan="4"><?php echo moneyFormatIndia(number_format($total_tax,2,'.',''));?></td>
								<!-- <th colspan="3" class="alignRight" style="font-size:15px !important"> <?php echo moneyFormatIndia(round($tot_payable-$tot_adv_paid)); ?></th> -->
							</tr>

							<?php }
							}?>
							<?php } ?>

							<tr>
        						<td colspan="2" style="text-transform: capitalize; align: left">Total </td>
								<td colspan="5" class="alignRight" style="font-size:15px !important"> <?php echo moneyFormatIndia(round($tot_payable-$tot_adv_paid)); ?></td>
							</tr>
							
        					
        					
        					<?php if($chit_amount>0){?>
        					
        				    <tr>
								<td style="white-space:nowrap;">
									<p style="font-size:bold;">Chit Details<p>
								</td>
							</tr>
        					<tr>
        						<td><hr class="tot_dashed"></td>
        					</tr>
        					    <?php 
        					    $i =1;
        					    $total_weight_amount        =0;
        					    $total_wastage_amount       =0;
        					    $total_making_charge_amount =0;
        					    $total_chit_amount_scheme   =0;
        					    foreach($est_other_item['chit_details'] as $chit)
        					    {
        					        if($chit['scheme_type']==0)
        					        {
        					            $total_chit_amount_scheme+=$chit['utl_amount'];
        					        }
        					        
        					        $weight_amount = 0;
        					        $wastage_amount = 0;
        					        $mc_amount = 0;
        					        if($chit['scheme_type']==2 || $chit['scheme_type']==3)
        					        {?>
            					        <tr>
                    						<td colspan="7"><b><?php echo $i.'. ACC NO - '.$chit['scheme_acc_number'];?></b></td>
                    					</tr>
        					        <?php 
        					            if($chit['closing_weight']>0)
        					            {
        					                $total_weight_amount+= number_format($chit['closing_weight']*$estimation['goldrate_22ct'],2,'.','');
        					                $weight_amount = number_format($chit['closing_weight']*$estimation['goldrate_22ct'],2,'.','');
        					            ?>
        					                <tr>
                        						<td colspan="2">Saved Weight</td>
                        						<td class="alignRight"><?php echo $chit['closing_weight'];?></td>
                        						<td></td>
                        						<td class="alignRight"><?php echo moneyFormatIndia($weight_amount);?></td>
                        					</tr>
        					            <?php }else{?>
        					                    <tr>

            										<td colspan="3"><?php echo $i.'. ACC NO - '.$chit['scheme_acc_number'];?></b></td>
            
            										<td class="alignCenter"></td>
            
            										<td></td>
            
            										<td class="alignRight"><?php echo moneyFormatIndia($chit['utl_amount']);?></td>
            
            									</tr>
        					            <?php }?>
        					            
        					            <?php 
        					            if($chit['wastage_per']>0)
        					            {
        					                $total_wastage_amount+= number_format($chit['savings_in_wastage']*$estimation['goldrate_22ct'],2,'.','');
        					                $wastage_amount = number_format($chit['savings_in_wastage']*$estimation['goldrate_22ct'],2,'.','');
        					            ?>
        					                <tr>
                        						<td colspan="2" style="text-transform: capitalize;">Saved V.A(<?php echo $chit['wastage_per'].'%' ?>)</td>
                        						<td class="alignRight"><?php echo $chit['savings_in_wastage'];?></td>
                        						<td></td>
                        						<td class="alignRight"><?php echo moneyFormatIndia($wastage_amount);?></td>
                        					</tr>
        					                
        					            <?php }
        					            ?>
        					            
        					            <?php 
                    					 if($chit['savings_in_making_charge']>0)
                    					 {
                    					    $mc_amount = number_format($chit['savings_in_making_charge'],2,'.','');
                    					    $total_making_charge_amount+= number_format($chit['savings_in_making_charge'],2,'.','');
                    					 ?>
                    					    <tr>
                        						<td colspan="3" style="text-transform: capitalize;">Total Saved MC</td>
                        						<td></td>
                        						<td class="alignRight"><?php echo moneyFormatIndia($mc_amount);?></td>
                        					</tr>
                    					 <?php }
                    					 ?>
            					        
        					        <?php $i++; } else if($chit['scheme_type']==0){ ?>
        					           
            					        <tr>
                    						<td colspan="3"><?php echo $i.'. ACC NO - '.$chit['scheme_acc_number'];?></td>
                    						<td></td>
                    						<td></td>
                    						<td></td>
                    						<td class="alignRight"><?php echo moneyFormatIndia(number_format($chit['utl_amount'],2,'.',''));?></td>
                    					</tr>
            					 <?php $i++; }?>
            					    <tr>
                						<td><hr class="tot_dashed"></td>
                					</tr>
            					<?php } ?>
            					
            					<?php 
            					$total_chit_amount = number_format($total_weight_amount+$total_making_charge_amount+$total_wastage_amount+$total_chit_amount_scheme,2,'.','')
            					?>
            					<tr style="font-weight:bold;">
            					    <td style="text-transform: capitalize;">SUB TOTAL</td>
            						<td class="alignRight"></td>
            						<td class="alignRight" style="padding-right:14px"></td>
            						<td></td>
            						<td></td>
            						<td></td>
                                    <td class="alignRight"><?php echo moneyFormatIndia($total_chit_amount); ?></td>
            					</tr>
        					<?php }?>
							
							<tr>
        						<td><hr class="tot_dashed"></td>
        					</tr>
        				</tbody>
        				</table>
						<?php if($tag_net_wt!=0){?>
            			<div>
            				<label style="font-weight:bold;">PARTLY (<?php echo number_format($tag_net_wt,3,'.','').'-'.number_format($net_wt,3,'.','')?>): <?php echo number_format(($tag_net_wt-$net_wt),3,'.','');?></label>
            			</div>

        			<?php }}?>
        		
        				<?php 
        
        				if(sizeof($est_other_item['old_matel_details'])>0){?>
        					
        						<p style="text-transform:uppercase;text-align:left; font-weight: bold; line-height: 1px">Old Jewels</p>
        					
        				<table class="purchase" width="120%">
        					<!-- <tr>
							<th width="20%">METAL</th>
        					<th width="20%" class="alignRight">GR WT</th>
        					<th width="25%" class="alignRight">VA.WT</th>
							<td></td>
        					<th width="25%" class="alignRight">amount</th>
        				</tr> -->
						<tr>
							<td><hr class="item_dashed"></td>
						</tr>
						
        				<tbody>
        					<?php 
        					$gross_wt=0;
							$total_va=0;
        					$amount=0;
							$net_wt=0;
							$va='';
        					foreach($est_other_item['old_matel_details'] as $data){
        					$gross_wt+=$data['gross_wt'];
							$total_va+=$data['wastage_wt'];
							$dust_wt+=$data['dust_wt'];
							$stn_wt+=$data['stone_wt'];
        					$amount+=$data['amount'];
							$net_wt=$gross_wt-$total_va-$dust_wt-$stn_wt;
							$va='';
							if($data['metal']!='SILVER'){
								$va=$data['dust_wt']+$data['wastage_wt']+$data['stone_wt'] ;
							}
        					?>
							<tr>
								<td></td>
								<td colspan="3" style="text-transform: capitalize;">rate @ <?php echo $data['rate_per_gram'];?></td>
								<td></td>
							</tr>
        					<tr>
								<td class='textOverflowHidden' style="text-align:left;width:20%"><?php echo ($data['old_metal_prod_name'] != '' ? strtoupper($data['old_metal_prod_name']) . '/' . ($data['purid']) : $data['old_metal_type'] . '/' . ($data['purid'])); ?></td>
        						<td class="alignRight"><?php echo $data['gross_wt'];?></td>
        						<td class="alignRight"><?php echo $va;?></td>
        						<td></td>
        						<td class="alignRight"><?php echo moneyFormatIndia($data['amount']);?></td>
        					</tr>
							<tr>
        						<td><hr class="item_dashed"></td>
        					</tr>
        					<?php }?>
						
							
        				</tbody>
        				<tr style="font-weight:bold">
        					<td style="text-transform: capitalize;">tot purc</td>
        					<td class="alignRight"><?php echo number_format($net_wt,3,'.','');?></td>
        					<!-- <td class="alignRight"><?php echo number_format($total_va,3,'.','');?></td> -->
        					<td colspan="2" style="text-transform: capitalize;" class="alignRight"></td>
        					
        					<td class="alignRight"><?php 
							$tot_purchase = $amount; 
							echo moneyFormatIndia($tot_purchase);
							?>
							</td>
        				</tr>
						<tr>
							<td><hr class="item_dashed"></td>
						</tr>
						
						
        			</table>
                   
        		   <?php }?>
        		   
        		   <table class="amount" width="115%" style="font-weight:bold;">
        		       <tbody>
        		           <?php 
						   
						   if($tot_payable!=0 && $tot_purchase!=0	){
						   		if($tot_payable!=0){?>
        		           <tr>
    							
						  		<td colspan="4" class="alignRight">Sales :</td>
    							<td class="alignRight"><?php echo moneyFormatIndia($tot_payable);?></td>
								
    						</tr>
    						<?php }?>
    						<?php if($tot_purchase!=0){?>
    						<tr>
    							
    							<td colspan="4" class="alignRight">Purchase:</td>
    							<td class="alignRight"> <?php echo moneyFormatIndia($tot_purchase);?></td>
								<td></td>
    						</tr>
    						<?php }
							}?>
    						<?php if($total_chit_amount!=0){?>
    						<tr>
    							<td colspan="4" class="alignRight">CHIT :</td>
    							<td class="alignRight"> <?php echo moneyFormatIndia($total_chit_amount);?></td>
								<td></td>
								
    						</tr>
    						<?php }?>
    						
    						<?php if($tot_adv_paid!=0){?>
    						<tr style="display:none">
    							
    							<td colspan="4" class="alignRight">ADVANCE :</td>
    							<td class="alignRight"> <?php echo moneyFormatIndia($tot_adv_paid);?></td>
								<td></td>
    						</tr>
    						<?php }?>
    					
    						<!-- <tr style="font-weight:bold;">
    							
    							<td colspan="4" class="alignRight">Total :</td>
    							<td class="alignRight"> <?php echo moneyFormatIndia(number_format($tot_payable-$tot_purchase-$total_chit_amount));?></td>
    						</tr> -->
    					
        		       </tbody>
        		   </table>
        		   
        		   <table class="amount" style="margin-left:0 !important;width: 115%;;table-layout:fixed;">
                            <?php $final = $tot_payable - $tot_purchase - $chit_amount - $tot_adv_paid ?>
                            <?php if ($final != '') { ?>
                                <tr>
                                    <td class="alignLeft" style="width:60%"><b><?php echo $final > 0 ? 'To be received :' : 'To be Paid :' ?></b></td>
                                    <td style="width:50%"class="alignRight"><b style="font-size:16px !important;"> Rs. <?php echo moneyFormatIndia(round($final)); ?> </b></td>
                                </tr>
                            <?php } ?>
                    </table>
        		   
        		   <br><br><br>
				   
				   <!--<?php $final=$tot_payable-$tot_purchase-$chit_amount-$tot_adv_paid ?>-->
				   
					<!--<p class="alignRight" style="text-transform: capitalize;"> <?php echo $final>0 ? 'To be received':'To be Paid' ?><b style="font-size:16px !important;"> Rs.  <?php echo moneyFormatIndia(round($final));?> </b> </p>-->
					
					<div style="width=100%;padding-top:40px;">
						<p style="text-align:center !important; font-size:10px !important; text-transform: capitalize;">(Inclusive of tax)</p>
						<div style="text-align:center;width:100%;"> Thank you </div> <br>
						<div class="cus_name" >
							
							<table style="text-transform: capitalize;">
								<tr>
									<td>name</td>
									<td>:</td>
									<td><?php echo $estimation['customer_name']; ?> </td>
								</tr>
								<tr>
									<td>mobile No</td>
									<td>:</td>
									<td><?php echo $estimation['mobile']; ?></td>
								</tr>
								<tr>
									<td>Address</td>
									<td>:</td>
									<td><?php echo $estimation['address1'].(!$estimation['address2'] ?'' :','.$estimation['address2']).(!$estimation['address3'] ?'' :','.$estimation['address3']) ?></td>
								</tr>
								<tr>
									<td>City</td>
									<td>:</td>
									<td><?php echo $estimation['city_name']; ?></td>
								</tr>
							</table>
							<br>
						</div>
        		   </div>
				   
        		   <div>
				   <h3 style="font-weight: bold;">WT VERIFIED.................................</h3>
					   <label>EMP-ID : <?php echo $estimation['emp_code'].' / '.$estimation['emp_name'];?></label>
					</div>
				</div>
            </div>
        </div>
	 </span>          
</body></html>