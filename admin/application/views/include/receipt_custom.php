<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Receipt</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/cus_receipt.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
		
	</head>
	
	<body class="margin">
	<div class="PDF_CusReceipt">
	    <div class="" style="font-weight: 400 !important; font-size:16px !important;margin-top:-12px;" align="center">
						
			</div>
			<?php 
		
			 $total_installment=$records[0]['total_installments'];
            //  $maturitydate = date('d-m-Y', strtotime("+".$total_installment." months", strtotime($records_sch['start_date'])));

			if (!empty($records[0]['maturity_date'])) {

				$maturitydate = date('d-m-Y', strtotime($records[0]['maturity_date']));
			} else {

				$total_installment = $records[0]['total_installments'];
					$maturitydate = date('d-m-Y', strtotime("+" . $records[0]['maturity_days'] . " days" , strtotime($records[0]['start_date'])));

					$maturitydate = date('d-m-Y', strtotime("+" . $total_installment . " months", strtotime($records[0]['start_date']))); 
				if($scheme['maturity_type'] == 2){
					$maturitydate = date('d-m-Y', strtotime("+" . $records[0]['maturity_days']+ $records[0]['closing_maturity_days'] . " days", strtotime($records[0]['start_date'])));

				}
				else{
					$maturitydate = date('d-m-Y', strtotime("+" .$records[0]['closing_maturity_days'] . " days", strtotime($records[0]['start_date'])));
				} 
			}
		
            ?>
			<span style="float:right;"><?php echo 'Print taken on : '.date("d-m-Y H:i:s");?></span><br/>
			<br>
		<div class="" align="center">
		    <img src="<?php echo base_url();?>assets/img/receiptLogo.png" style="max-width:120px; max-height:60px; width:auto; height:auto; display:inline-block; margin-bottom:6px;">
		
               	<?php echo !empty($comp_name['company_name']) && isset($comp_name['company_name']) ?strtoupper($comp_name['company_name']):'' ; ?><br/>				
			<?php echo empty($comp_details['company_name']) ? '' :$comp_details['company_name'] ; ?><br/>				
				<?php echo empty($comp_details['address1']) ? '' :$comp_details['address1'].','; ?>				
				<?php echo empty($comp_details['address1']) ? '' :$comp_details['address2'].','; ?><br/>
				<?php echo empty($comp_details['city'] && $comp_details['pincode']) ? '' :$comp_details['city'].' - '.$comp_details['pincode'].'.'; ?><br/><br/>
			</div>
			<span style="float:left;">
			    <?php echo 'Maturity Date : '.$maturitydate;?>

			</span>
			<br/>
				<div style="font-weight: 400 !important; font-size:15px !important; text-align: center; display: block; width: 100%; margin-top: 5px;">
				<?php echo $comp_details['company_name'];?>			
			</div>
			</br>
			<div class="" style="font-weight: 400 !important; font-size:15px !important;" align="center">
				<?php echo $records[0]['scheme_name'];?><hr>				
			</div>
			<div>
			<table class="meta" style="width: 100%" align="center">
				<tr>
					<th><span >MS no</span></th>
					<td>
					    <span>
					        <?php echo $records[0]['scheme_acc_number']; ?>
					    
					   </span>
					 </td>
				</tr>
				<tr>
					<th><span >Receipt No</span></th>
					<td>
					    <span >
					    <?php echo $records[0]['receipt_no']; ?>
					     
					    </span>
					 </td>
				</tr>
				<?php if ($this->session->userdata('enable_old_sch_acc_no') == 1) { ?>
					<tr>
						<th><span >Old A/c No</span></th>
						<td>
							<span>
								<?php echo $records[0]['old_scheme_acc_number']; ?>
							
						</span>
						</td>
					</tr>	
				<?php } ?>
				<tr>
					<!-- <th><span >Installment No</span></th> -->
					 <th><span >Installment (Month/Day)</span></th>
					<td><span><?php echo $records[0]['installment_no']; ?></span></td>
				</tr>
				<tr>
					<th><span ><?php echo $records[0]['metal_name']; ?> Rate</span></th>
					<td><span><?php echo $records[0]['metal_rate']; ?></span></td>
				</tr>
				<tr>
					<th><span >Paid Amount</span></th>
					<td><span><?php echo "Rs ".$records[0]['payment_amount']; ?></span></td>
				</tr>
			<?php if($records[0]['scheme_type'] != 0 && $records[0]['flexible_sch_type'] != 1 && $records[0]['flexible_sch_type'] != 6 ){?>
				<tr>
					<th><span > Paid Weight</span></th>
					<td><span><?php echo formatMetalWeight($records[0]['metal_weight']); ?></span></td>
				</tr> 
				<tr>
					<th><span >Running Weight</span></th>
					<td><span><?php echo formatMetalWeight($records[0]['total_weight']).' g'; ?></span></td>
				</tr>
			<?php }?>
	
				<tr>
					<th><span >Mode</span></th>
					<!--<td><span><?php echo $records[0]['payment_mode']; ?></span></td>-->
					<td>
					    <?php if($records[0]['multi_modes'] == ''){ ?>
    					    <span><?php echo $records[0]['payment_mode']; ?></span>
    					    <br/>
					        <span><?php echo number_format((float)($records[0]['payment_amount']-$records[0]['discountAmt'].' INR '),2, '.', ''); ?></span>
					    <?php }else{ ?>
					        <span><?php echo $records[0]['multi_modes']; ?></span>
					    <?php }?>
					 </td>
				</tr>
				<tr>
					<th><span >Mobile</span></th>
					<td><span><?php echo $records[0]['mobile']; ?></span></td>
				</tr>
				<tr>
					<th><span >Payment Date</span></th>
					<td><span><?php echo $records[0]['date_payment']; ?></span></td>
				</tr>
			</table><hr>
			</div>
			<div>
				<?php if($records[0]['due_type']=='D') { ?>
					<p align="left">Paid by <?php echo $comp_details['company_name']; ?> for <br/>Mr/Mrs. <?php echo $records[0]['firstname'].(isset($records[0]['firstname'])?', ':''); ?></p>
					<div align="left"><span data-prefix><?php echo $comp_details['currency_symbol']; ?> </span><span style="font-weight: 400 !important; font-size:16px !important;"><?php echo $records[0]['payment_amount']; ?></span></div>
				<?php }else{ ?>
					<p align="left" style="margin-top:-5px;">Received with thanks from <br/>Mr/Mrs. <?php echo $records[0]['firstname'].(isset($records[0]['firstname'])?', ':''); ?></p>
					<div align="left">

						<span ><?php echo $comp_details['currency_symbol']; ?> </span><span style="font-weight: 400 !important; font-size:16px !important;"><?php echo $records[0]['payment_amount']; ?></span>
</div>
				<!-- </tr></span> -->
				<?php } ?>
				
				<p style="font-size:12px !important;margin-top:-2px;"><?php echo ucwords($records[0]['amount_in_words']); ?> Only</p>
							
			<p style="font-size:12px !important;margin-top:-14px;text-align:right;">Emp Code : <?php echo $records[0]['login_employee']; ?></p>
			<!--	<div style="float:right">For <?php echo empty($comp_details['company_name']) && !isset($comp_details['company_name']) ? $comp_details['name'] :$comp_details['company_name']; ?></div><br/><br/>
				<div style="float:right">Signature</div>-->
			</div>
		</div>
		<script type="text/javascript"> 
		this.print(); 
		</script> 
	</body>
	
</html>