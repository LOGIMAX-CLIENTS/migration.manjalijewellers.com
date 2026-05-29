<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Receipt</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/wallet_thermal.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
		
	</head>
	
	
	
	
			<body class="plugin">
					<div class="PDF_receipt_thermal">			
					
				<span><img src="<?php echo base_url();?>assets/img/receipt_logo.png" style="width:10% !important; margin-left:20px; margin-top:2px !important;"></span>
						
							<table class="meta" style="margin-top:-7px !important;">
								<tr class="installment">	
								     <th><span class="installment">Name</span></th>
										<td><span ><?php echo $wallet['name']; ?></span></td>
								</tr>
								
								   <tr class="installment">	
								      <th>				
										<span class="installment" >Mobile No</span></th>
										<td><span><?php echo $wallet['mobile']; ?></span></td>
									</tr>
									<tr class="installment">
									<th>				
										<span class="installment" >Amount</span></th>
										<td><span ><?php echo 'Rs  '.(number_format($wallet['value'])); ?></span></td>
									</tr>
									<tr class="installment">
									<th>				
										<span class="installment" >Date</span></th>
										<td><span ><?php echo (date('d-m-Y',strtotime(str_replace("/","-",$wallet['date_transaction'])))); ?></span></td>
									</tr>
									
									<tr class="installment">
									<th>				
										<span class="installment" >Trans.Type</span></th>
										<td><span ><?php echo ($wallet['transaction_type']==0?'Issue':'Redeem'); ?></span></td>
									</tr>
									
								
							</table>
				</div>
			
		
	</body>
	
</html>