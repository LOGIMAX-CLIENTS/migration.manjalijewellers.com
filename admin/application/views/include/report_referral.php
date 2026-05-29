<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Referral Report</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
		
	</head>
	<body>
	<span class="PDFReceipt">
			<div align="center"> Employee Referral Report</div>
			<p></p>
			<table>
				<thead>
				<?php foreach($records as $referralrecord){?>			
				
					<tr>
						<th><span contenteditable><?php echo $referralrecord['name']; ?></span></th>
						<th><span contenteditable><?php echo $referralrecord['emp_code']; ?></span></th>					
						<th><span contenteditable><?php echo $referralrecord['benifits']; ?></span></th>						
					</tr>
				</thead>
				<tbody>
				<?php if(count($referralrecord['referral_deatils'])>0){
					   foreach($referralrecord['referral_deatils'] as $refferalrecord){ ?>
					<tr>					
						<td><span contenteditable><?php echo $refferalrecord['id_customer']; ?></span></td>
						<td><span contenteditable><?php echo $refferalrecord['cus_name'];    ?></span></td>
						<td><span contenteditable><?php echo $refferalrecord['scheme_acc_number']; ?></span></td>
						<td><span contenteditable><?php echo $refferalrecord['date_payment']; ?></span></td>
						<td><span contenteditable><?php echo $refferalrecord['code'];    ?></span></td>
						<td><span contenteditable><?php echo $refferalrecord['payment_amount'];    ?></span></td>
					</tr>
					
				</tbody>
				
				<?php 
				   }				
				  }
				}
				?>
			</table>
			<p></p>
		</span>
	</body>
</html>