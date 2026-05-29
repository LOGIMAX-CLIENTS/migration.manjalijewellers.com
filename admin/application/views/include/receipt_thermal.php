<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Receipt</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_thermal.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
		
	</head>
	
	<body class="plugin"  align="">
		<div class="PDF_receipt_thermal" style="font-size: 30px;">		
								
			
					<div class="col-md-12">
							<div class="col-md-12">
								<table cellspacing="0" class="meta" style="text-align:left;">
								
								<tr class="rec_no">	
								<th style="text-align:left;">    
                                    <span class="rec_no">Rc<?php echo $records[0]['receipt_no']; ?>
                                    </span>
                                </th>
										<td>
										 <span>&nbsp;<?php echo "   Ins : ".intval($records[0]['installment']);  ?></span>
										</td> 
								</tr>
								   <tr rowspacing="0" class="pay_date" >		<th style="text-align:left;">			
										<span class="pay_date">Date </span></th><td>
									<span >&nbsp;<?php echo (date('d-m-Y',strtotime(str_replace("/","-",$records[0]['date_payment'])))); ?></span></td>
								</tr>
								
								<tr class="rec_no">		
									<!-- <th style="text-align:left;">			
										<span class="received_amt">Acc no</span></th> -->
										<td>
										<span >&nbsp;<?php echo $records[0]['scheme_acc_number']; ?></span></td>
								</tr>
								
									<?php if ($records[0]['scheme_type']!=0) {?>
									<tr class="received_amt" style="font-size:12px;">
										<th class="received_amt" style="text-align:left;"><span>Rs: <?php echo number_format($records[0]['payment_amount']); ?></span></th>
											<td>
								<!--	<span style="text-align:left;"> <?php echo number_format($records[0]['payment_amount']); ?></span> -->
										 <?php if ($records[0]['scheme_type']!=0) {?>
										<span class="Wt"><?php echo "/Wt".$records[0]['metal_weight'];?></span>
									
											<?php }?>
											</td>
											
											   
										
									</tr>
									<?php } else {?>
									<tr class="received_amt" style="font-size:12px;">
										<th class="received_amt"><span></span></th>
											<td>
									<span style="text-align:left;">Rs: <?php echo number_format($records[0]['payment_amount']); ?></span> 
										 <?php if ($records[0]['scheme_type']!=0) {?>
										<span class="Wt"><?php echo "/Wt".$records[0]['metal_weight'];?></span>
									
											<?php }?>
											</td>
											
											   
										
									</tr>
									
								    <?php }?>
									<?php if ($records[0]['scheme_type']!=0) {?>
									<tr class="received_amt">
										<th class="received_amt" style="text-align:left;"><span><?php echo " Rt: ".intval($records[0]['metal_rate']);  ?></span></th>
											<td>
										
										<span class="Tw"><?php echo "/Tw";?></span>
								<span><?php echo  ($records_sch['total_paid_weight']);?></span>
											</td>
									</tr>
										<?php }?>
										
										<!--<?php if ($records[0]['scheme_type']!=0) {?>
									<tr class="received_amt">
										<th class="received_amt"><span >Wt / Rate</span></th>
											<td>
											<span><?php echo ($records[0]['metal_weight']);?></span>
											<span class="gm"><?php echo "gm";?></span>
											<span><?php echo "/ ".intval($records[0]['metal_rate']);  ?></span>
											</td>
									</tr>
									<?php }?> -->
									
									<!--	<tr class="run_amount">	
									<?php if ($records[0]['scheme_type']==0) {?>									
										<th><span class="run_amount">Tot.Amount</span></th>
										<td><span> <?php echo "Rs ".$records_sch['total_paid_amount']; ?></span><?php } 
										else {?>
										<th class="run_amount"><span >Tot.Weight</span></th>
										<td><span><?php echo $records_sch['total_paid_weight'];?>
										<span class="gm"><?php echo "gm"; }?></span>
										</td>
									</tr>-->
									
									
								
							</table>
						</div>
						
				
					</div>
				</div>
				
		<!--		<div class="PDF_receipt_thermal" style="font-size: 30px;">		
								
			
					<div class="col-md-12">
							<div class="col-md-12">
								<table class="meta" align="left">
								
									<tr class="rec_no">		<th>			
										<span class="received_amt">Rc</span></th><td>
										<span ><?php echo $records[0]['receipt_no']; ?></span> <span><?php echo "  Ins : ".intval($records[0]['installment']);  ?></span></td> 
								</tr>
								
							
								
								<tr class="pay_date">		<th>			
										<span class="pay_date">Date</span></th><td>
									<span ><?php echo (date('d-m-Y',strtotime(str_replace("/","-",$records[0]['date_payment'])))); ?></span></td>
								</tr>
								
								<tr class="rec_no">		<th>			
										<span class="received_amt"> </span></th><td>
										<span ><?php echo $records[0]['sch_acc_number']; ?></span></td>
								</tr>
								
								 
						
									
									
									<tr class="received_amt">
										<th class="received_amt"><span > </span></th>
									<td>
									<span> <?php echo " Rs: ".number_format($records[0]['payment_amount']); ?></span>
										<?php if ($records[0]['scheme_type']!=0) {?>
										<span class="Wt"><?php echo "/Wt";?></span>
										<span><?php echo ($records[0]['metal_weight']);?></span>
											<?php }?>
											</td>
									</tr>
								
									<?php if ($records[0]['scheme_type']!=0) {?>
									<tr class="received_amt">
										<th class="received_amt"><span ></span></th>
											<td>
										<span><?php echo " Rt: ".intval($records[0]['metal_rate']);  ?></span>
										<span class="Tw"><?php echo "/Tw";?></span>
								<span><?php echo  ($records_sch['total_paid_weight']);?></span>
											</td>
									</tr>
										<?php }?>
									
									
								
								
									
									
								
							</table>
						</div>
						
				
					</div>
				</div> -->
	</body>
	
</html>