<!doctype html>
<html>
	<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		
		<title>Receipt</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
		
	</head>
	<body>
	<span class="PDFReceipt">
			<div><img alt="" src="<?php echo base_url();?>assets/img/receipt_logo.png"></div>
		    <div class="address" align="right">
				 <?php echo $comp_details['address1'].' ,'; ?>				
				<?php echo $comp_details['address2'].' ,'; ?>
				<?php echo $comp_details['city'].'  '.$comp_details['pincode'].' ,'; ?>
				<?php echo $comp_details['state'].' ,'; ?>
				<?php echo $comp_details['country'].' .'; ?>
				<p> <?php echo 'Phone : '. $comp_details['phone'];?></p><p> <?php echo 'Mobile : '.$comp_details['mobile'];?></p><p></p>
			</div>
			
			<div class="heading">Purchase plan Closing Details</div>
			<table class="meta" style="width:90%" align="center">
				<tr>
					<th><span>Customer Name</span></th>
					<td><span> <?php echo $account['name']; ?></span></td>
				</tr>
				
				<tr>
					<th><span >Customer Mobile</span></th>
					<td><span><?php echo $account['mobile']; ?></span></td>
				</tr>				
				<tr>
					<th><span >A/c No</span></th>
					<td><span><?php echo ($account['scheme_acc_number']); ?></span></td>
				</tr>
				<tr>
					<th><span >Purchase plan Name</span></th>
					<td><span><?php echo ($account['scheme_name']); ?></span></td>
				</tr>
				<tr>
					<th><span >A/c Name</span></th>
					<td><span><?php echo $account['account_name']; ?></span></td>
				</tr>
				<tr>
					<th><span >Total Paid</span></th>
					<td><span><?php echo $account['total_paid']; ?></span></td>
				</tr>
				<tr>
					<th><span >Benefits</span></th>
					<td><span><?php echo $account['additional_benefits']; ?></span></td>
				</tr>
				<tr>
					<th><span >Detections/Tax</span></th>
					<td><span><?php echo $account['tax']; ?></span></td>
				</tr>
				<tr>
					<th><span >Closing Balance</span></th>
					<td><span><?php echo $account['closing_balance']; ?></span></td>
				</tr>
				<tr>
					<th><span >Closed Request By</span></th>
					<td><span><?php echo ($account['closed_by'].'('.$account['closedBy'].')'); ?></span></td>
				</tr>
			</table>
			<p></p>
			
		<aside>
			
			<div >
				<p class="txtAckowlege">This is system generated receipt, keep this as an acknowledgement.</p>
			</div>
		</aside>
		</span>
	</body>
</html>