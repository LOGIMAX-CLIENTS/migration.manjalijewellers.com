<?php setlocale(LC_MONETARY, 'en_IN');  ?> 


<!doctype html>
<html><head>
		<meta charset="utf-8">
		<title>Receipt</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
		
		<style>
         @media print
         {
         .firstrow {page-break-inside:auto}
         }
        </style>

		</head><body>
	<div class="PDFReceipt" >
		<div class="chit_detailslogo"><img alt="" src="<?php echo base_url();?>assets/img/receipt_logo.jpg?<?php time();?>" class=""></div>
		<div class="address" align="right"> 
				 <?php  echo ($comp_details['address1'] != '') ? $comp_details['address1'].' ,' : ''; ?>				
				<?php echo ( $comp_details['address2'] != '')?$comp_details['address2'].' ,'  : ''; ?>
				<?php echo ( $comp_details['city'] != '')? $comp_details['city'].'  '.$comp_details['pincode'].' ,'  : ''; ?>
				<?php echo ( $comp_details['state'] != '')? $comp_details['state'].' ,'  : ''; ?>
				<?php echo ( $comp_details['country'] != '')? $comp_details['country'].' .'  : ''; ?>
				<p> <?php echo($comp_details['phone'] != '')?  'Phone : '. $comp_details['phone'] : '';?></p><p> <?php ($comp_details['mobile'] != '') ? 'Mobile : '.$comp_details['mobile'] :'';?></p><p></p>
			</div>
			
			<div class="heading"><?php echo $sch['scheme_name']?></div>
			<!--<div style="width: 100%;border:1px soild #ccc">
				<div class="" style="width:50%;display:inline-block"> 
					<table class="chit_customer_details" >					
						<tr>
							<td style="">Address</td>
							<td style=""> Logimax Enclave, 24&25, L&T Bypass Karpagam University Junction, <br>Near L&T Toll Gate 3, Eachanari,</td>	
							<td></td>			
												
												
						</tr>	
						<tr>						
							<td style="">Branch</td>						
							<td style=""><?php echo $sch['branch_name']?></td>		
							<td></td>			
												
						</tr>			
					</table>
				</div>
				<div class="" style="width:50%;display:inline-block"> 
					<table class="chit_customer_details">					
						<tr>				
							<td style="%">Customer Name</td>						
							<td style=""><?php echo $sch['customer_name']?></td>
												
												
						</tr>				
						<tr>					
							<td>Customer Name</td>						
							<td><?php echo $sch['customer_name']?></td>
												
						</tr>	
						<tr>					
							<td>Mobile No </td>						
							<td><?php echo $sch['mobile']?></td>	
												
						</tr>	
						<tr>					
							<td>Schme account number</td>						
							<td><?php echo $sch['scheme_acc_number']?></td>	
												
						</tr>	
						<tr>
							<td>Account Name</td>						
							<td><?php echo $sch['account_name']?></td>	
												
						</tr>			
					</table>
				</div>
			</div>-->
			<div class="" >
				
				<div class="" style="width:60%;display:inline-block;padding-top:30px;">
					<table class="chit_customer_details" >					
						<tr>
							<th><span style="width:40%;">Address</span></th>
							<td style=""><span> <?php echo $sch['add1'].", ".$sch['add2'].", ".$sch['add3'].",<br>".$sch['city']." - ".$sch['pincode']." <br>".$sch['state']; ?></span></td>	
							<td></td>			
												
												
						</tr>	
						<tr>						
							<th style=""><span>Branch</span></th>						
							<td style=""><span><?php echo $sch['branch_name']?></span></td>		
							<td></td>			
												
						</tr>			
					</table>
				</div>
				<div style="width:50%;display:inline-block;margin-top:0px;">
					<table class="chit_customer_details" style="" align="right">
						<tr>
							<th><span>Customer Name  </span></th>
							<td><span> <?php echo $sch['customer_name']?></span></td>
						</tr>
						<tr>
							<th><span >Mobile  </span></th>
							<td><span><?php echo $sch['mobile']?></span></td>
						</tr>
						<tr>
							<th><span >A/c Name</span></th>
							<td><span><?php echo $sch['account_name']?></span></td>
						</tr>
						<tr>
							<th><span >A/c No</span></th>
							<td><span><?php echo $sch['scheme_acc_number']?></span></td>
					style="page-break-inside: auto;table-layout:fixed;"	</tr>
						<tr>
							<th><span >Total Payments done</span></th>
							<td><span><?php echo $sch['pay_count']?></span></td>
						</tr>
						</tr>
					</table>
				</div>
			</div>	 
				 
				 <br>
			<table class="chit_details" style="page-break-inside: auto;width: 100%;table-layout:fixed;">
				<thead>
					<tr>
						<th colspan="4" style="text-align:center">Chit starting date : <?php echo date("d-m-Y", strtotime($sch['join_date']))?></th>
						<th colspan="4" style="text-align:center">Payment can be made till: <?php echo date("d-m-Y", strtotime($sch['allow_pay_till']))?> </th>
					</tr>
					<tr>
					    <th colspan="4" style="text-align:center">Report date : <?php echo date('d-m-y h:i:s A'); ?></th> 
						<th colspan="4" style="text-align:center">Interest : <?php echo $interest['int']['interest_value'].' '.$interest['int']['int_symbol']?></th>
					</tr>
					<tr>
						<th style="text-align:center">Due Paid</th>
						<th style="text-align:center">Paid date</th>
						<th style="text-align:center">Amount Paid (INR)</th>
						<th style="text-align:center">Board Rate (INR)</th>
						<th style="text-align:center">Saved weight (in grms)</th>
						<th style="text-align:center">Difference days for interest calculation</th>
						<th style="text-align:center">Interest as on date (in grms)</th>
						<th style="text-align:center">Receipt No</th>
					</tr>
				</thead>
				<tbody>
				
				<?php foreach($payData as $pay){ ?>

				
					<tr>
						<td style="text-align:centre"><?php echo $pay['sno']?></td>
						<td style="text-align:left"><?php echo date("d-m-Y", strtotime($pay['paid_date']))?></td>
						<td style="text-align:right"><?php echo money_format('%!i', $pay['paid_amt']) ?></td>
						<td style="text-align:right"><?php echo money_format('%!i', $pay['metal_rate']) ?></td>
						<td style="text-align:right"><?php echo $pay['metal_weight']?></td>
						<td style="text-align:right"><?php echo $pay['days_diff']?></td>
						<td style="text-align:right"><?php echo $pay['pay_int']?></td>
						<td style="text-align:right"><?php echo $pay['receipt_no']?></td>
					</tr>
				<?php }  ?>
				
					<tr>
						<td style="text-align:center;font-weight:bold;"></td>
						<td style="text-align:left;font-weight:bold;">Total</td>
						<td style="text-align:right;font-weight:bold;"><?php echo money_format('%!i', $interest['tot']['total_paid']) ?></td>
						<td style="text-align:center;font-weight:bold;"></td>
						<td style="text-align:right;font-weight:bold;"><?php echo $interest['tot']['saved_wgt'] ?></td>
						<td style="text-align:center;font-weight:bold;"></td>
						<td style="text-align:right;font-weight:bold;"><?php echo $interest['tot']['total_benefit'] ?></td>
						<td style="text-align:center;font-weight:bold;"></td>
					</tr>
					
				</tbody>
			</table>
			<p></p>
			<p></p>
		</div>
	</body>
	</html>