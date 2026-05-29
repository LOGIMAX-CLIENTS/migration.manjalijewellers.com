<!doctype html>
<html><head>
		<meta charset="utf-8">
		<title>Receipt</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/cus_receipt.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
</head><body class="margin">
	<div class="PDF_CusReceipt">
	<div class="" align="left">
	    	<!--<div align="center">CHIT RECEIPT PRINT FOR CUSTOMER
COPY</div>-->
				<span style="font-weight: bold !important; font-size:20px !important;" ><?php echo $comp_details['company_name']; ?><br/></span>
				<?php if($comp_details['name']){?>
			<?php echo $comp_details['name'].' - '.$comp_details['pincode'].' '; ?><br/><br/>
			 <?php }else{?>	
			 <?php echo $comp_details['address2']?>
			  <?php }?>
			  <span> CLOSING SLIP - <?php echo $account['acc']['id_scheme_account']; ?></span><br/>
			<?php echo $comp_details['phone'].''; ?>
			
		</div>
			<div class="" style="font-weight: 100 !important; font-size:16px !important;" align="center">
				<?php echo $account['acc']['scheme_name'];?><hr>				
			</div>
			<div>
			<table class="meta" style="width: 100%" align="center">
				<tr>
					<th><span >Cust Name</span></th>
					<td><span><?php echo $account['acc']['name']; ?></span></td>
				</tr>
				<tr>
					<th><span >Mobile No.</span></th>
					<td><span><?php echo $account['acc']['mobile']; ?></span></td>
				</tr>
					<tr>
					<th><span >A/c Name</span></th>
					<td><span><?php echo $account['acc']['account_name']; ?></span></td>
				</tr>
					<tr>
					<th><span >Msno</span></th>
					<td><span><?php echo $account['acc']['code'].'-'.$account['acc']['scheme_acc_number']; ?></span></td>
				</tr>
				
				</tr>
					<tr>
					<th><span >Scheme A/c ID</span></th>
					<td><span><?php echo $account['acc']['id_scheme_account']; ?></span></td>
				</tr>
					<tr>
					<th><span >Start Date</span></th>
					<td><span><?php echo $account['acc']['start_date']; ?></span></td>
				</tr>
					<tr>
					<th><span >Close Date</span></th>
					<td><span><?php echo $account['acc']['closing_date']; ?></span></td>
				</tr>
				<!--<tr>
					<th><span >Closed EmpID</span></th>
					<td><span><?php echo $account['acc']['employee_closed']; ?></span></td>
				</tr>
					<tr>
					<th><span >Scheme Name</span></th>
					<td><span><?php echo $account['acc']['scheme_name']; ?></span></td>
				</tr>-->
					<tr>
					<th><span >Type</span></th>
					<td><span><?php echo $account['acc']['scheme_type'];  ?></span></td>
				</tr>
					<tr>
					<th><span >Amt Payable</span></th>
					<td><span><?php echo $account['acc']['sch_amt'];  ?></span></td>
				</tr>
					<tr>
					<th><span >T.Paid Ins.</span></th>
					<td><span><?php echo $account['acc']['paid_installments'].'/'.$account['acc']['total_installments'];  ?></span></td>
					</tr>
					<tr>
					<th><span >Total Paid</span></th>
					<td><span><?php echo $account['acc']['total_paid']; ?></span></td>
				</tr>
				<tr>
					<th><span >Add. Benefits</span></th>
					<td><span><?php echo $account['acc']['additional_benefits']; ?></span></td>
				</tr>
				
					<tr>
					<th><span >Discount</span></th>
					<td><span><?php echo ($account['acc']['discountAmt']*$account['acc']['paid_installments']); ?></span></td>
				</tr>
				<tr>
					<th><span >charges</span></th>
					<td><span><?php echo $account['acc']['closing_add_chgs']; ?></span></td>
				</tr>
				<!-- 05-12-2022-->
				<tr>
					<th><span >Dudection</span></th>
					<td><span><?php echo $account['acc']['deduction']; ?></span></td>
				</tr>
				
				<tr>
					<th><span >Closing Bal.</span></th>
					<td><span><?php echo $account['acc']['closing_balance']; ?></span></td>
				</tr>
				<tr>
					<th><span >Closed By</span></th>
					<td><span><?php echo $account['acc']['closed_by'].'('.$account['acc']['closedBy'].')'; ?></span></td>
				</tr>
				<!--<tr>
					<th><span >OTP Verified No.</span></th>
					<td><span><?php echo $account['acc']['otp_verified_mob']; ?></span></td>
				</tr>
				<tr>
					<th><span >Comments</span></th>
					<td><span><?php echo $account['acc']['remark_close']; ?></span></td>
				</tr>-->
			</table><hr> 
			</div>
			<div align="center">Thank You Visit Again </div>
			<!--<div>
				<?php if($records[0]['due_type']=='D') { ?>
					<p align="left">Paid by <?php echo $comp_details['company_name']; ?> for <br/>Mr/Mrs. <?php echo $records[0]['firstname'].(isset($records[0]['firstname'])?', ':''); ?></p>
					<div align="left"><span data-prefix><?php echo $comp_details['currency_symbol']; ?> </span><span style="font-weight: 400 !important; font-size:16px !important;"><?php echo $records[0]['payment_amount']; ?></span></div>
				<?php }else{ ?>
					<p align="left">Received with thanks form <br/>Mr/Mrs. <?php echo $records[0]['firstname'].(isset($records[0]['firstname'])?', ':''); ?></p>
					<div align="left"><span data-prefix><?php echo $comp_details['currency_symbol']; ?> </span><span style="font-weight: 400 !important; font-size:16px !important;"><?php echo $records[0]['payment_amount']; ?></span></div>
				<?php } ?>
				<p style="font-size:12px !important;"><?php echo ucwords($records[0]['amount_in_words']); ?> Only</p>
			<p></p>
			<div align="right">Signature</div> 
				<div align="right">For <?php echo $comp_details['company_name']; ?></div>
		</div>-->
		</div>
		<script type="text/javascript"> 
		this.print(); 
		</script> 
</body></html>