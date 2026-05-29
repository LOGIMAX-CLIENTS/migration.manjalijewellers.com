<html>
					<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<title>Receipt</title>
					<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/receipt.css" >
					</head>
					<body>
					<div class="PDFReceipt">
					<table style="width:75%;margin:0 10%;">
						<tr >
							<td width="61%" style="margin-right:20px; border-right:none"><img src="<?php echo base_url();?>assets/img/logo.png?<?php time()?>"/>
							<p style='margin:0 2% 1% 3%;text-align:left;'>617/20, 12th Main, 3rd Block, Rajaji Nagar, Bengaluru-10</p>
							<p style='margin:1.5% 2% 1% 3%;text-align:left;font-size:small;font-weight:normal'>Phone: +91 80 2315 1844, +91 80 2330 5307, +91 80 4173 8979</p></td>
							<td style="border-left:none;text-align:center">Receipt</td>
						</tr>
						<tr>
							<td style="width:70%;border-right:none" class="address">
								TO,<br /> 
									<span class="useraddr"><?php echo $records[0]['firstname'].' '.$records[0]['lastname']; ?></span>
								   <pre style="margin-left:18px;margin-top:0"> <?php echo nl2br($records[0]['address1']); ?></pre>
								   <pre style="margin-left:18px;margin-top:-18px"> <?php echo nl2br($records[0]['address2']); ?></pre>
								   <pre style="margin-left:18px;margin-top:-18px"> <?php echo nl2br($records[0]['address3']); ?></pre>
								   <pre style="margin-left:18px;margin-top:-18px"> <?php echo nl2br($records[0]['city']." - ".$records[0]['pincode']); ?></pre>
							</td>
							</td>
							<td   style="width:30%;border-left:none" class="receiptDts">
							    <div style="text-align:left;margin-top:-10px;">Receipt No : #<?php echo $records[0]['id_payment']; ?></div>
								<div style="text-align:left;margin-top:-10px;">Account No : <?php echo $records[0]['scheme_acc_number']; ?></div>
								<div style="text-align:left;margin-top:-10px;">Date : <?php echo $records[0]['date_payment']; ?></div>
								<div style="text-align:left;margin-top:-10px;">Installment :<?php echo $records[0]['installment']; ?></div>
							</td>
						</tr>
						<tr class="heading">
							<td style="text-align:center">DESCRIPTION</td>
							<td width="29%" style="text-align:right">AMOUNT (RS)</td>
						</tr>
						<tr class="values">
							<td style="text-align:left">For the payment of Saving Purchase plan - <strong><?php echo $records[0]['scheme_name']; ?></strong></td>
							<td style="text-align:right"><?php echo $records[0]['payment_amount']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><?php echo $records[0]['amount_in_words']; ?> Only</td>
						</tr>
						<tr>
							<td class="txtAckowlege"  colspan="2">
							This is system generated receipt, keep this as an acknowledgement.
							</td>
						</tr>
					</table>
					</body>
					</html>
					</div>